/**
 * @file
 * @name UE.dom.domUtils
 * @short DomUtils
 * @import editor.js, core/utils.js,core/browser.js,core/dom/dtd.js
 * @desc UEditor��װ�ĵײ�dom������
 */
function getDomNode(node, start, ltr, startFromChild, fn, guard) {
    var tmpNode = startFromChild && node[start],
        parent;
    !tmpNode && (tmpNode = node[ltr]);
    while (!tmpNode && (parent = (parent || node).parentNode)) {
        if (parent.tagName == 'BODY' || guard && !guard(parent)) {
            return null;
        }
        tmpNode = parent[ltr];
    }
    if (tmpNode && fn && !fn(tmpNode)) {
        return  getDomNode(tmpNode, start, ltr, false, fn);
    }
    return tmpNode;
}
var attrFix = ie && browser.version < 9 ? {
        tabindex:"tabIndex",
        readonly:"readOnly",
        "for":"htmlFor",
        "class":"className",
        maxlength:"maxLength",
        cellspacing:"cellSpacing",
        cellpadding:"cellPadding",
        rowspan:"rowSpan",
        colspan:"colSpan",
        usemap:"useMap",
        frameborder:"frameBorder"
    } : {
        tabindex:"tabIndex",
        readonly:"readOnly"
    },
    styleBlock = utils.listToMap([
        '-webkit-box', '-moz-box', 'block' ,
        'list-item' , 'table' , 'table-row-group' ,
        'table-header-group', 'table-footer-group' ,
        'table-row' , 'table-column-group' , 'table-column' ,
        'table-cell' , 'table-caption'
    ]);
var domUtils = dom.domUtils = {
    //�ڵ㳣��
    NODE_ELEMENT:1,
    NODE_DOCUMENT:9,
    NODE_TEXT:3,
    NODE_COMMENT:8,
    NODE_DOCUMENT_FRAGMENT:11,

    //λ�ù�ϵ
    POSITION_IDENTICAL:0,
    POSITION_DISCONNECTED:1,
    POSITION_FOLLOWING:2,
    POSITION_PRECEDING:4,
    POSITION_IS_CONTAINED:8,
    POSITION_CONTAINS:16,
    //ie6ʹ�������Ļ���һ�οհ׳���
    fillChar:ie && browser.version == '6' ? '\ufeff' : '\u200B',
    //-------------------------Node����--------------------------------
    keys:{
        /*Backspace*/ 8:1, /*Delete*/ 46:1,
        /*Shift*/ 16:1, /*Ctrl*/ 17:1, /*Alt*/ 18:1,
        37:1, 38:1, 39:1, 40:1,
        13:1 /*enter*/
    },
    /**
     * ��ȡ�ڵ�A����ڽڵ�B��λ�ù�ϵ
     * @name getPosition
     * @grammar UE.dom.domUtils.getPosition(nodeA,nodeB)  =>  Number
     * @example
     *  switch (returnValue) {
     *      case 0: //��ȣ�ͬһ�ڵ�
     *      case 1: //�޹أ��ڵ㲻����
     *      case 2: //���棬���ڵ�Aͷ��λ�ڽڵ�Bͷ���ĺ���
     *      case 4: //ǰ�ã����ڵ�Aͷ��λ�ڽڵ�Bͷ����ǰ��
     *      case 8: //�����������ڵ�A���ڵ�B����
     *      case 10://������ͣ����ڵ�A�������ڵ�B�ұ��ڵ�B������ʵ���ϣ�������������ض����棬����returnValue��ʵ�ϲ������8�������
     *      case 16://���������ڵ�A�����ڵ�B
     *      case 20://������ͣ����ڵ�A����ǰ�ýڵ�A�Ұ����ڵ�B��ͬ��������������ض�ǰ�ã�����returnValue��ʵ��Ҳ�������16�����
     *  }
     */
    getPosition:function (nodeA, nodeB) {
        // ��������ڵ���ͬһ���ڵ�
        if (nodeA === nodeB) {
            // domUtils.POSITION_IDENTICAL
            return 0;
        }
        var node,
            parentsA = [nodeA],
            parentsB = [nodeB];
        node = nodeA;
        while (node = node.parentNode) {
            // ���nodeB��nodeA�����Ƚڵ�
            if (node === nodeB) {
                // domUtils.POSITION_IS_CONTAINED + domUtils.POSITION_FOLLOWING
                return 10;
            }
            parentsA.push(node);
        }
        node = nodeB;
        while (node = node.parentNode) {
            // ���nodeA��nodeB�����Ƚڵ�
            if (node === nodeA) {
                // domUtils.POSITION_CONTAINS + domUtils.POSITION_PRECEDING
                return 20;
            }
            parentsB.push(node);
        }
        parentsA.reverse();
        parentsB.reverse();
        if (parentsA[0] !== parentsB[0]) {
            // domUtils.POSITION_DISCONNECTED
            return 1;
        }
        var i = -1;
        while (i++, parentsA[i] === parentsB[i]) {
        }
        nodeA = parentsA[i];
        nodeB = parentsB[i];
        while (nodeA = nodeA.nextSibling) {
            if (nodeA === nodeB) {
                // domUtils.POSITION_PRECEDING
                return 4
            }
        }
        // domUtils.POSITION_FOLLOWING
        return  2;
    },

    /**
     * ���ؽڵ�node�ڸ��ڵ��е�����λ��
     * @name getNodeIndex
     * @grammar UE.dom.domUtils.getNodeIndex(node)  => Number  //����ֵ��0��ʼ
     */
    getNodeIndex:function (node, ignoreTextNode) {
        var preNode = node,
            i = 0;
        while (preNode = preNode.previousSibling) {
            if (ignoreTextNode && preNode.nodeType == 3) {
                if(preNode.nodeType != preNode.nextSibling.nodeType ){
                    i++;
                }
                continue;
            }
            i++;
        }
        return i;
    },

    /**
     * ���ڵ�node�Ƿ��ڽڵ�doc�����ϣ�ʵ�����Ǽ���Ƿ�doc����
     * @name inDoc
     * @grammar UE.dom.domUtils.inDoc(node,doc)   =>  true|false
     */
    inDoc:function (node, doc) {
        return domUtils.getPosition(node, doc) == 10;
    },
    /**
     * ����node�ڵ�����Ƚڵ�
     * @name findParent
     * @grammar UE.dom.domUtils.findParent(node)  => Element  // ֱ�ӷ���node�ڵ�ĸ��ڵ�
     * @grammar UE.dom.domUtils.findParent(node,filterFn)  => Element  //filterFnΪ���˺�����node��Ϊ����������trueʱ�ŻὫnode��Ϊ����Ҫ��Ľڵ㷵��
     * @grammar UE.dom.domUtils.findParent(node,filterFn,includeSelf)  => Element  //includeSelfָ���Ƿ��������
     */
    findParent:function (node, filterFn, includeSelf) {
        if (node && !domUtils.isBody(node)) {
            node = includeSelf ? node : node.parentNode;
            while (node) {
                if (!filterFn || filterFn(node) || domUtils.isBody(node)) {
                    return filterFn && !filterFn(node) && domUtils.isBody(node) ? null : node;
                }
                node = node.parentNode;
            }
        }
        return null;
    },
    /**
     * ͨ��tagName����node�ڵ�����Ƚڵ�
     * @name findParentByTagName
     * @grammar UE.dom.domUtils.findParentByTagName(node,tagNames)   =>  Element  //tagNames֧�����飬���ִ�Сд
     * @grammar UE.dom.domUtils.findParentByTagName(node,tagNames,includeSelf)   =>  Element  //includeSelfָ���Ƿ��������
     * @grammar UE.dom.domUtils.findParentByTagName(node,tagNames,includeSelf,excludeFn)   =>  Element  //excludeFnָ�������������������trueʱ���Ըýڵ�
     */
    findParentByTagName:function (node, tagNames, includeSelf, excludeFn) {
        tagNames = utils.listToMap(utils.isArray(tagNames) ? tagNames : [tagNames]);
        return domUtils.findParent(node, function (node) {
            return tagNames[node.tagName] && !(excludeFn && excludeFn(node));
        }, includeSelf);
    },
    /**
     * ���ҽڵ�node�����Ƚڵ㼯��
     * @name findParents
     * @grammar UE.dom.domUtils.findParents(node)  => Array  //����һ�����Ƚڵ����鼯�ϣ�����������
     * @grammar UE.dom.domUtils.findParents(node,includeSelf)  => Array  //����һ�����Ƚڵ����鼯�ϣ�includeSelfָ���Ƿ��������
     * @grammar UE.dom.domUtils.findParents(node,includeSelf,filterFn)  => Array  //����һ�����Ƚڵ����鼯�ϣ�filterFnָ����������������true��node����ѡȡ
     * @grammar UE.dom.domUtils.findParents(node,includeSelf,filterFn,closerFirst)  => Array  //����һ�����Ƚڵ����鼯�ϣ�closerFirstΪtrue�Ļ���node��ֱ�Ӹ��׽ڵ�������ĵ�0��
     */
    findParents:function (node, includeSelf, filterFn, closerFirst) {
        var parents = includeSelf && ( filterFn && filterFn(node) || !filterFn ) ? [node] : [];
        while (node = domUtils.findParent(node, filterFn)) {
            parents.push(node);
        }
        return closerFirst ? parents : parents.reverse();
    },

    /**
     * �ڽڵ�node��������½ڵ�newNode
     * @name insertAfter
     * @grammar UE.dom.domUtils.insertAfter(node,newNode)  => newNode
     */
    insertAfter:function (node, newNode) {
        return node.parentNode.insertBefore(newNode, node.nextSibling);
    },

    /**
     * ɾ���ڵ�node��������keepChildrenָ���Ƿ����ӽڵ�
     * @name remove
     * @grammar UE.dom.domUtils.remove(node)  =>  node
     * @grammar UE.dom.domUtils.remove(node,keepChildren)  =>  node
     */
    remove:function (node, keepChildren) {
        var parent = node.parentNode,
            child;
        if (parent) {
            if (keepChildren && node.hasChildNodes()) {
                while (child = node.firstChild) {
                    parent.insertBefore(child, node);
                }
            }
            parent.removeChild(node);
        }
        return node;
    },

    /**
     * ȡ��node�ڵ���dom���ϵ���һ���ڵ�,�����������
     * @name  getNextDomNode
     * @grammar UE.dom.domUtils.getNextDomNode(node)  => Element
     * @example
     */
    getNextDomNode:function (node, startFromChild, filterFn, guard) {
        return getDomNode(node, 'firstChild', 'nextSibling', startFromChild, filterFn, guard);
    },
    /**
     * ���ڵ�node�Ƿ�����bookmark�ڵ�
     * @name isBookmarkNode
     * @grammar UE.dom.domUtils.isBookmarkNode(node)  => true|false
     */
    isBookmarkNode:function (node) {
        return node.nodeType == 1 && node.id && /^_baidu_bookmark_/i.test(node.id);
    },
    /**
     * ��ȡ�ڵ�node���ڵ�window����
     * @name  getWindow
     * @grammar UE.dom.domUtils.getWindow(node)  => window����
     */
    getWindow:function (node) {
        var doc = node.ownerDocument || node;
        return doc.defaultView || doc.parentWindow;
    },
    /**
     * �õ�nodeA��nodeB���������Ƚڵ�
     * @name  getCommonAncestor
     * @grammar UE.dom.domUtils.getCommonAncestor(nodeA,nodeB)  => Element
     */
    getCommonAncestor:function (nodeA, nodeB) {
        if (nodeA === nodeB)
            return nodeA;
        var parentsA = [nodeA] , parentsB = [nodeB], parent = nodeA, i = -1;
        while (parent = parent.parentNode) {
            if (parent === nodeB) {
                return parent;
            }
            parentsA.push(parent);
        }
        parent = nodeB;
        while (parent = parent.parentNode) {
            if (parent === nodeA)
                return parent;
            parentsB.push(parent);
        }
        parentsA.reverse();
        parentsB.reverse();
        while (i++, parentsA[i] === parentsB[i]) {
        }
        return i == 0 ? null : parentsA[i - 1];

    },
    /**
     * ���node�ڵ������ֵ�Ϊ�յ�inline�ڵ�
     * @name clearEmptySibling
     * @grammar UE.dom.domUtils.clearEmptySibling(node)
     * @grammar UE.dom.domUtils.clearEmptySibling(node,ignoreNext)  //ignoreNextָ���Ƿ�����ұ߿սڵ�
     * @grammar UE.dom.domUtils.clearEmptySibling(node,ignoreNext,ignorePre)  //ignorePreָ���Ƿ������߿սڵ�
     * @example
     * <b></b><i></i>xxxx<b>bb</b> --> xxxx<b>bb</b>
     */
    clearEmptySibling:function (node, ignoreNext, ignorePre) {
        function clear(next, dir) {
            var tmpNode;
            while (next && !domUtils.isBookmarkNode(next) && (domUtils.isEmptyInlineElement(next)
                //���ﲻ�ܰѿո��������ɿո�ɵ����������ּ�Ŀո񶪵���
                || !new RegExp('[^\t\n\r' + domUtils.fillChar + ']').test(next.nodeValue) )) {
                tmpNode = next[dir];
                domUtils.remove(next);
                next = tmpNode;
            }
        }
        !ignoreNext && clear(node.nextSibling, 'nextSibling');
        !ignorePre && clear(node.previousSibling, 'previousSibling');
    },
    /**
     * ��һ���ı��ڵ�node��ֳ������ı��ڵ㣬offsetָ�����λ��
     * @name split
     * @grammar UE.dom.domUtils.split(node,offset)  =>  TextNode  //���ش��з�λ�ÿ�ʼ�ĺ�һ���ı��ڵ�
     */
    split:function (node, offset) {
        var doc = node.ownerDocument;
        if (browser.ie && offset == node.nodeValue.length) {
            var next = doc.createTextNode('');
            return domUtils.insertAfter(node, next);
        }
        var retval = node.splitText(offset);
        //ie8��splitText�������childNodes,�����ֶ��������ĸ���
        if (browser.ie8) {
            var tmpNode = doc.createTextNode('');
            domUtils.insertAfter(retval, tmpNode);
            domUtils.remove(tmpNode);
        }
        return retval;
    },

    /**
     * ���ڵ�node�Ƿ�Ϊ�սڵ㣨�����ո񡢻��С�ռλ�����ַ���
     * @name  isWhitespace
     * @grammar  UE.dom.domUtils.isWhitespace(node)  => true|false
     */
    isWhitespace:function (node) {
        return !new RegExp('[^ \t\n\r' + domUtils.fillChar + ']').test(node.nodeValue);
    },
    /**
     * ��ȡԪ��element�����viewport��λ������
     * @name getXY
     * @grammar UE.dom.domUtils.getXY(element)  => Object //�����������{x:left,y:top}
     */
    getXY:function (element) {
        var x = 0, y = 0;
        while (element.offsetParent) {
            y += element.offsetTop;
            x += element.offsetLeft;
            element = element.offsetParent;
        }
        return { 'x':x, 'y':y};
    },
    /**
     * ΪԪ��element��ԭ��DOM�¼���typeΪ�¼����ͣ�handlerΪ������
     * @name on
     * @grammar UE.dom.domUtils.on(element,type,handler)   //type֧�����鴫��
     * @example
     * UE.dom.domUtils.on(document.body,"click",function(e){
     *     //eΪ�¼�����thisΪ�����Ԫ�ض�Ϸ�Ǹ�
     * })
     * @example
     * UE.dom.domUtils.on(document.body,["click","mousedown"],function(evt){
     *     //evtΪ�¼�����thisΪ�����Ԫ�ض���
     * })
     */
    on:function (element, type, handler) {
        var types = utils.isArray(type) ? type : [type],
            k = types.length;
        if (k) while (k--) {
            type = types[k];
            if (element.addEventListener) {
                element.addEventListener(type, handler, false);
            } else {
                if (!handler._d) {
                    handler._d = {
                        els : []
                    };
                }
                var key = type + handler.toString(),index = utils.indexOf(handler._d.els,element);
                if (!handler._d[key] || index == -1) {
                    if(index == -1){
                        handler._d.els.push(element);
                    }
                    if(!handler._d[key]){
                        handler._d[key] = function (evt) {
                            return handler.call(evt.srcElement, evt || window.event);
                        };
                    }


                    element.attachEvent('on' + type, handler._d[key]);
                }
            }
        }
        element = null;
    },
    /**
     * ���ԭ��DOM�¼���
     * @name un
     * @grammar  UE.dom.donUtils.un(element,type,handler)  //�μ�<code><a href="#on">on</a></code>
     */
    un:function (element, type, handler) {
        var types = utils.isArray(type) ? type : [type],
            k = types.length;
        if (k) while (k--) {
            type = types[k];
            if (element.removeEventListener) {
                element.removeEventListener(type, handler, false);
            } else {
                var key = type + handler.toString();
                try{
                    element.detachEvent('on' + type, handler._d ? handler._d[key] : handler);
                }catch(e){}
                if (handler._d && handler._d[key]) {
                    var index = utils.indexOf(handler._d.els,element);
                    if(index!=-1){
                        handler._d.els.splice(index,1);
                    }
                    handler._d.els.length == 0 && delete handler._d[key];
                }
            }
        }
    },

    /**
     * �ȽϽڵ�nodeA��ڵ�nodeB�Ƿ������ͬ�ı�ǩ�����������Լ�����ֵ
     * @name  isSameElement
     * @grammar UE.dom.domUtils.isSameElement(nodeA,nodeB) => true|false
     * @example
     * <span  style="font-size:12px">ssss</span> and <span style="font-size:12px">bbbbb</span>   => true
     * <span  style="font-size:13px">ssss</span> and <span style="font-size:12px">bbbbb</span>   => false
     */
    isSameElement:function (nodeA, nodeB) {
        if (nodeA.tagName != nodeB.tagName) {
            return false;
        }
        var thisAttrs = nodeA.attributes,
            otherAttrs = nodeB.attributes;
        if (!ie && thisAttrs.length != otherAttrs.length) {
            return false;
        }
        var attrA, attrB, al = 0, bl = 0;
        for (var i = 0; attrA = thisAttrs[i++];) {
            if (attrA.nodeName == 'style') {
                if (attrA.specified) {
                    al++;
                }
                if (domUtils.isSameStyle(nodeA, nodeB)) {
                    continue;
                } else {
                    return false;
                }
            }
            if (ie) {
                if (attrA.specified) {
                    al++;
                    attrB = otherAttrs.getNamedItem(attrA.nodeName);
                } else {
                    continue;
                }
            } else {
                attrB = nodeB.attributes[attrA.nodeName];
            }
            if (!attrB.specified || attrA.nodeValue != attrB.nodeValue) {
                return false;
            }
        }
        // �п���attrB�����԰�����attrA������֮�⻹���Լ�������
        if (ie) {
            for (i = 0; attrB = otherAttrs[i++];) {
                if (attrB.specified) {
                    bl++;
                }
            }
            if (al != bl) {
                return false;
            }
        }
        return true;
    },

    /**
     * �жϽڵ�nodeA��ڵ�nodeB��Ԫ�������Ƿ�һ��
     * @name isSameStyle
     * @grammar UE.dom.domUtils.isSameStyle(nodeA,nodeB) => true|false
     */
    isSameStyle:function (nodeA, nodeB) {
        var styleA = nodeA.style.cssText.replace(/( ?; ?)/g, ';').replace(/( ?: ?)/g, ':'),
            styleB = nodeB.style.cssText.replace(/( ?; ?)/g, ';').replace(/( ?: ?)/g, ':');
        if (browser.opera) {
            styleA = nodeA.style;
            styleB = nodeB.style;
            if (styleA.length != styleB.length)
                return false;
            for (var p in styleA) {
                if (/^(\d+|csstext)$/i.test(p)) {
                    continue;
                }
                if (styleA[p] != styleB[p]) {
                    return false;
                }
            }
            return true;
        }
        if (!styleA || !styleB) {
            return styleA == styleB;
        }
        styleA = styleA.split(';');
        styleB = styleB.split(';');
        if (styleA.length != styleB.length) {
            return false;
        }
        for (var i = 0, ci; ci = styleA[i++];) {
            if (utils.indexOf(styleB, ci) == -1) {
                return false;
            }
        }
        return true;
    },
    /**
     * ���ڵ�node�Ƿ�Ϊ��Ԫ��
     * @name isBlockElm
     * @grammar UE.dom.domUtils.isBlockElm(node)  => true|false
     */
    isBlockElm:function (node) {
        return node.nodeType == 1 && (dtd.$block[node.tagName] || styleBlock[domUtils.getComputedStyle(node, 'display')]) && !dtd.$nonChild[node.tagName];
    },
    /**
     * ���node�ڵ��Ƿ�Ϊbody�ڵ�
     * @name isBody
     * @grammar UE.dom.domUtils.isBody(node)   => true|false
     */
    isBody:function (node) {
        return  node && node.nodeType == 1 && node.tagName.toLowerCase() == 'body';
    },
    /**
     * ��node�ڵ�Ϊ���ģ����ýڵ��ָ�����Ƚڵ�parent��ֳ�2��
     * @name  breakParent
     * @grammar UE.dom.domUtils.breakParent(node,parent) => node
     * @desc
     * <code type="html"><b>ooo</b>��node�ڵ�
     * <p>xxxx<b>ooo</b>xxx</p> ==> <p>xxx</p><b>ooo</b><p>xxx</p>
     * <p>xxxxx<span>xxxx<b>ooo</b>xxxxxx</span></p>   =>   <p>xxxxx<span>xxxx</span></p><b>ooo</b><p><span>xxxxxx</span></p></code>
     */
    breakParent:function (node, parent) {
        var tmpNode,
            parentClone = node,
            clone = node,
            leftNodes,
            rightNodes;
        do {
            parentClone = parentClone.parentNode;
            if (leftNodes) {
                tmpNode = parentClone.cloneNode(false);
                tmpNode.appendChild(leftNodes);
                leftNodes = tmpNode;
                tmpNode = parentClone.cloneNode(false);
                tmpNode.appendChild(rightNodes);
                rightNodes = tmpNode;
            } else {
                leftNodes = parentClone.cloneNode(false);
                rightNodes = leftNodes.cloneNode(false);
            }
            while (tmpNode = clone.previousSibling) {
                leftNodes.insertBefore(tmpNode, leftNodes.firstChild);
            }
            while (tmpNode = clone.nextSibling) {
                rightNodes.appendChild(tmpNode);
            }
            clone = parentClone;
        } while (parent !== parentClone);
        tmpNode = parent.parentNode;
        tmpNode.insertBefore(leftNodes, parent);
        tmpNode.insertBefore(rightNodes, parent);
        tmpNode.insertBefore(node, rightNodes);
        domUtils.remove(parent);
        return node;
    },
    /**
     * ���ڵ�node�Ƿ��ǿ�inline�ڵ�
     * @name  isEmptyInlineElement
     * @grammar   UE.dom.domUtils.isEmptyInlineElement(node)  => 1|0
     * @example
     * <b><i></i></b> => 1
     * <b><i></i><u></u></b> => 1
     * <b></b> => 1
     * <b>xx<i></i></b> => 0
     */
    isEmptyInlineElement:function (node) {
        if (node.nodeType != 1 || !dtd.$removeEmpty[ node.tagName ]) {
            return 0;
        }
        node = node.firstChild;
        while (node) {
            //����Ǵ�����bookmark������
            if (domUtils.isBookmarkNode(node)) {
                return 0;
            }
            if (node.nodeType == 1 && !domUtils.isEmptyInlineElement(node) ||
                node.nodeType == 3 && !domUtils.isWhitespace(node)
                ) {
                return 0;
            }
            node = node.nextSibling;
        }
        return 1;

    },

    /**
     * ɾ��node�ڵ��µ����ҿհ��ı��ӽڵ�
     * @name trimWhiteTextNode
     * @grammar UE.dom.domUtils.trimWhiteTextNode(node)
     */
    trimWhiteTextNode:function (node) {
        function remove(dir) {
            var child;
            while ((child = node[dir]) && child.nodeType == 3 && domUtils.isWhitespace(child)) {
                node.removeChild(child);
            }
        }
        remove('firstChild');
        remove('lastChild');
    },

    /**
     * �ϲ�node�ڵ�����ͬ���ӽڵ�
     * @name mergeChild
     * @desc
     * UE.dom.domUtils.mergeChild(node,tagName) //tagNameҪ�ϲ����ӽڵ�ı�ǩ
     * @example
     * <p><span style="font-size:12px;">xx<span style="font-size:12px;">aa</span>xx</span></p>
     * ==> UE.dom.domUtils.mergeChild(node,'span')
     * <p><span style="font-size:12px;">xxaaxx</span></p>
     */
    mergeChild:function (node, tagName, attrs) {
        var list = domUtils.getElementsByTagName(node, node.tagName.toLowerCase());
        for (var i = 0, ci; ci = list[i++];) {
            if (!ci.parentNode || domUtils.isBookmarkNode(ci)) {
                continue;
            }
            //span��������
            if (ci.tagName.toLowerCase() == 'span') {
                if (node === ci.parentNode) {
                    domUtils.trimWhiteTextNode(node);
                    if (node.childNodes.length == 1) {
                        node.style.cssText = ci.style.cssText + ";" + node.style.cssText;
                        domUtils.remove(ci, true);
                        continue;
                    }
                }
                ci.style.cssText = node.style.cssText + ';' + ci.style.cssText;
                if (attrs) {
                    var style = attrs.style;
                    if (style) {
                        style = style.split(';');
                        for (var j = 0, s; s = style[j++];) {
                            ci.style[utils.cssStyleToDomStyle(s.split(':')[0])] = s.split(':')[1];
                        }
                    }
                }
                if (domUtils.isSameStyle(ci, node)) {
                    domUtils.remove(ci, true);
                }
                continue;
            }
            if (domUtils.isSameElement(node, ci)) {
                domUtils.remove(ci, true);
            }
        }
    },

    /**
     * ԭ������getElementsByTagName�ķ�װ
     * @name getElementsByTagName
     * @grammar UE.dom.domUtils.getElementsByTagName(node,tagName)  => Array  //�ڵ㼯������
     */
    getElementsByTagName:function (node, name,filter) {
        if(filter && utils.isString(filter)){
           var className = filter;
           filter =  function(node){return domUtils.hasClass(node,className)}
        }
        name = utils.trim(name).replace(/[ ]{2,}/g,' ').split(' ');
        var arr = [];
        for(var n = 0,ni;ni=name[n++];){
            var list = node.getElementsByTagName(ni);
            for (var i = 0, ci; ci = list[i++];) {
                if(!filter || filter(ci))
                    arr.push(ci);
            }
        }

        return arr;
    },
    /**
     * ���ڵ�node�ϲ������ڵ���
     * @name mergeToParent
     * @grammar UE.dom.domUtils.mergeToParent(node)
     * @example
     * <span style="color:#fff"><span style="font-size:12px">xxx</span></span> ==> <span style="color:#fff;font-size:12px">xxx</span>
     */
    mergeToParent:function (node) {
        var parent = node.parentNode;
        while (parent && dtd.$removeEmpty[parent.tagName]) {
            if (parent.tagName == node.tagName || parent.tagName == 'A') {//���a��ǩ��������
                domUtils.trimWhiteTextNode(parent);
                //span��Ҫ���⴦��  ��������������� <span stlye="color:#fff">xxx<span style="color:#ccc">xxx</span>xxx</span>
                if (parent.tagName == 'SPAN' && !domUtils.isSameStyle(parent, node)
                    || (parent.tagName == 'A' && node.tagName == 'SPAN')) {
                    if (parent.childNodes.length > 1 || parent !== node.parentNode) {
                        node.style.cssText = parent.style.cssText + ";" + node.style.cssText;
                        parent = parent.parentNode;
                        continue;
                    } else {
                        parent.style.cssText += ";" + node.style.cssText;
                        //trace:952 a��ǩҪ�����»���
                        if (parent.tagName == 'A') {
                            parent.style.textDecoration = 'underline';
                        }
                    }
                }
                if (parent.tagName != 'A') {
                    parent === node.parentNode && domUtils.remove(node, true);
                    break;
                }
            }
            parent = parent.parentNode;
        }
    },
    /**
     * �ϲ��ڵ�node�������ֵܽڵ�
     * @name mergeSibling
     * @grammar UE.dom.domUtils.mergeSibling(node)
     * @grammar UE.dom.domUtils.mergeSibling(node,ignorePre)    //ignorePreָ���Ƿ�������ֵ�
     * @grammar UE.dom.domUtils.mergeSibling(node,ignorePre,ignoreNext)  //ignoreNextָ���Ƿ�������ֵ�
     * @example
     * <b>xxxx</b><b>ooo</b><b>xxxx</b> ==> <b>xxxxoooxxxx</b>
     */
    mergeSibling:function (node, ignorePre, ignoreNext) {
        function merge(rtl, start, node) {
            var next;
            if ((next = node[rtl]) && !domUtils.isBookmarkNode(next) && next.nodeType == 1 && domUtils.isSameElement(node, next)) {
                while (next.firstChild) {
                    if (start == 'firstChild') {
                        node.insertBefore(next.lastChild, node.firstChild);
                    } else {
                        node.appendChild(next.firstChild);
                    }
                }
                domUtils.remove(next);
            }
        }
        !ignorePre && merge('previousSibling', 'firstChild', node);
        !ignoreNext && merge('nextSibling', 'lastChild', node);
    },

    /**
     * ���ýڵ�node�����ӽڵ㲻�ᱻѡ��
     * @name unSelectable
     * @grammar UE.dom.domUtils.unSelectable(node)
     */
    unSelectable:ie || browser.opera ? function (node) {
        //for ie9
        node.onselectstart = function () {
            return false;
        };
        node.onclick = node.onkeyup = node.onkeydown = function () {
            return false;
        };
        node.unselectable = 'on';
        node.setAttribute("unselectable", "on");
        for (var i = 0, ci; ci = node.all[i++];) {
            switch (ci.tagName.toLowerCase()) {
                case 'iframe' :
                case 'textarea' :
                case 'input' :
                case 'select' :
                    break;
                default :
                    ci.unselectable = 'on';
                    node.setAttribute("unselectable", "on");
            }
        }
    } : function (node) {
        node.style.MozUserSelect =
            node.style.webkitUserSelect =
                node.style.KhtmlUserSelect = 'none';
    },
    /**
     * ɾ���ڵ�node�ϵ�����attrNames��attrNamesΪ������������
     * @name  removeAttributes
     * @grammar UE.dom.domUtils.removeAttributes(node,attrNames)
     * @example
     * //Before remove
     * <span style="font-size:14px;" id="test" name="followMe">xxxxx</span>
     * //Remove
     * UE.dom.domUtils.removeAttributes(node,["id","name"]);
     * //After remove
     * <span style="font-size:14px;">xxxxx</span>
     */
    removeAttributes:function (node, attrNames) {
        attrNames = utils.isArray(attrNames) ? attrNames : utils.trim(attrNames).replace(/[ ]{2,}/g,' ').split(' ');
        for (var i = 0, ci; ci = attrNames[i++];) {
            ci = attrFix[ci] || ci;
            switch (ci) {
                case 'className':
                    node[ci] = '';
                    break;
                case 'style':
                    node.style.cssText = '';
                    !browser.ie && node.removeAttributeNode(node.getAttributeNode('style'))
            }
            node.removeAttribute(ci);
        }
    },
    /**
     * ��doc�´���һ����ǩ��Ϊtag������Ϊattrs��Ԫ��
     * @name createElement
     * @grammar UE.dom.domUtils.createElement(doc,tag,attrs)  =>  Node  //���ش����Ľڵ�
     */
    createElement:function (doc, tag, attrs) {
        return domUtils.setAttributes(doc.createElement(tag), attrs)
    },
    /**
     * Ϊ�ڵ�node�������attrs��attrsΪ���Լ�ֵ��
     * @name setAttributes
     * @grammar UE.dom.domUtils.setAttributes(node,attrs)  => node
     */
    setAttributes:function (node, attrs) {
        for (var attr in attrs) {
            if(attrs.hasOwnProperty(attr)){
                var value = attrs[attr];
                switch (attr) {
                    case 'class':
                        //ie��Ҫ������ֵ��setAttribute��������
                        node.className = value;
                        break;
                    case 'style' :
                        node.style.cssText = node.style.cssText + ";" + value;
                        break;
                    case 'innerHTML':
                        node[attr] = value;
                        break;
                    case 'value':
                        node.value = value;
                        break;
                    default:
                        node.setAttribute(attrFix[attr] || attr, value);
                }
            }
        }
        return node;
    },

    /**
     * ��ȡԪ��element�ļ�����ʽ
     * @name getComputedStyle
     * @grammar UE.dom.domUtils.getComputedStyle(element,styleName)  => String //���ض�Ӧ��ʽ���Ƶ���ʽֵ
     * @example
     * getComputedStyle(document.body,"font-size")  =>  "15px"
     * getComputedStyle(form,"color")  =>  "#ffccdd"
     */
    getComputedStyle:function (element, styleName) {
        //һ�µ����Ե�������
        var pros = 'width height top left';

        if(pros.indexOf(styleName) > -1){
            return element['offset' + styleName.replace(/^\w/,function(s){return s.toUpperCase()})] + 'px';
        }
        //�����ı��ڵ�
        if (element.nodeType == 3) {
            element = element.parentNode;
        }
        //ie��font-size��body�¶�����font-size�����currentStyle���ȡ�����font-size. ȡ����ʵ��ֵ���ʴ��޸�.
        if (browser.ie && browser.version < 9 && styleName == 'font-size' && !element.style.fontSize &&
            !dtd.$empty[element.tagName] && !dtd.$nonChild[element.tagName]) {
            var span = element.ownerDocument.createElement('span');
            span.style.cssText = 'padding:0;border:0;font-family:simsun;';
            span.innerHTML = '.';
            element.appendChild(span);
            var result = span.offsetHeight;
            element.removeChild(span);
            span = null;
            return result + 'px';
        }
        try {
            var value = domUtils.getStyle(element, styleName) ||
                (window.getComputedStyle ? domUtils.getWindow(element).getComputedStyle(element, '').getPropertyValue(styleName) :
                    ( element.currentStyle || element.style )[utils.cssStyleToDomStyle(styleName)]);

        } catch (e) {
            return "";
        }
        return utils.transUnitToPx(utils.fixColor(styleName, value));
    },
    /**
     * ��Ԫ��element��ɾ��classNames��֧��ͬʱɾ�����
     * @name removeClasses
     * @grammar UE.dom.domUtils.removeClasses(element,classNames)
     * @example
     * //ִ�з���ǰ��dom�ṹ
     * <span class="test1 test2 test3">xxx</span>
     * //ִ�з���
     * UE.dom.domUtils.removeClasses(element,["test1","test3"])
     * //ִ�з������dom�ṹ
     * <span class="test2">xxx</span>
     */
    removeClasses:function (elm, classNames) {
        classNames = utils.isArray(classNames) ? classNames :
            utils.trim(classNames).replace(/[ ]{2,}/g,' ').split(' ');
        for(var i = 0,ci,cls = elm.className;ci=classNames[i++];){
            cls = cls.replace(new RegExp('\\b' + ci + '\\b'),'')
        }
        cls = utils.trim(cls).replace(/[ ]{2,}/g,' ');
        if(cls){
            elm.className = cls;
        }else{
            domUtils.removeAttributes(elm,['class']);
        }
    },
    /**
     * ��Ԫ��element������һ����ʽ��className��֧���Կո�ֿ��Ķ������
     * �����ͬ���������������
     * @name addClass
     * @grammar UE.dom.domUtils.addClass(element,classNames)
     */
    addClass:function (elm, classNames) {
        if(!elm)return;
        classNames = utils.trim(classNames).replace(/[ ]{2,}/g,' ').split(' ');
        for(var i = 0,ci,cls = elm.className;ci=classNames[i++];){
            if(!new RegExp('\\b' + ci + '\\b').test(cls)){
                elm.className += ' ' + ci;
            }
        }
    },
    /**
     * �ж�Ԫ��element�Ƿ������ʽ����className,֧���Կո�ֿ��Ķ������,�������˳��ͬҲ���ԱȽ�
     * @name hasClass
     * @grammar UE.dom.domUtils.hasClass(element,className)  =>true|false
     */
    hasClass:function (element, className) {
        if(utils.isRegExp(className)){
            return className.test(element.className)
        }
        className = utils.trim(className).replace(/[ ]{2,}/g,' ').split(' ');
        for(var i = 0,ci,cls = element.className;ci=className[i++];){
            if(!new RegExp('\\b' + ci + '\\b','i').test(cls)){
                return false;
            }
        }
        return i - 1 == className.length;
    },

    /**
     * ��ֹ�¼�Ĭ����Ϊ
     * @param {Event} evt    ��Ҫ��֯���¼�����
     */
    preventDefault:function (evt) {
        evt.preventDefault ? evt.preventDefault() : (evt.returnValue = false);
    },
    /**
     * ɾ��Ԫ��element����ʽ
     * @grammar UE.dom.domUtils.removeStyle(element,name)        ɾ������ʽ����
     */
    removeStyle:function (element, name) {
        if(browser.ie && browser.version > 8){
            element.style.cssText = element.style.cssText.replace(new RegExp(name + '\s*:\s*[^;]+;?'),'')
        }else{
            if (element.style.removeProperty) {
                element.style.removeProperty (name);
            }else {
                element.style.removeAttribute (utils.cssStyleToDomStyle(name));
            }
        }


        if (!element.style.cssText) {
            domUtils.removeAttributes(element, ['style']);
        }
    },
    /**
     * ��ȡԪ��element��ĳ����ʽֵ
     * @name getStyle
     * @grammar UE.dom.domUtils.getStyle(element,name)  => String
     */
    getStyle:function (element, name) {
        var value = element.style[ utils.cssStyleToDomStyle(name) ];
        return utils.fixColor(name, value);
    },
    /**
     * ΪԪ��element������ʽ����ֵ
     * @name setStyle
     * @grammar UE.dom.domUtils.setStyle(element,name,value)
     */
    setStyle:function (element, name, value) {
        element.style[utils.cssStyleToDomStyle(name)] = value;
    },
    /**
     * ΪԪ��element������ʽ����ֵ
     * @name setStyles
     * @grammar UE.dom.domUtils.setStyle(element,styles)  //stylesΪ��ʽ��ֵ��
     */
    setStyles:function (element, styles) {
        for (var name in styles) {
            if (styles.hasOwnProperty(name)) {
                domUtils.setStyle(element, name, styles[name]);
            }
        }
    },
    /**
     * ɾ��_moz_dirty����
     * @function
     */
    removeDirtyAttr:function (node) {
        for (var i = 0, ci, nodes = node.getElementsByTagName('*'); ci = nodes[i++];) {
            ci.removeAttribute('_moz_dirty');
        }
        node.removeAttribute('_moz_dirty');
    },
    /**
     * �����ӽڵ������
     * @function
     * @param {Node}    node    ���ڵ�
     * @param  {Function}    fn    �����ӽڵ�Ĺ�����Ϊ�գ���õ������ӽڵ������
     * @return {Number}    ���������ӽڵ������
     */
    getChildCount:function (node, fn) {
        var count = 0, first = node.firstChild;
        fn = fn || function () {
            return 1;
        };
        while (first) {
            if (fn(first)) {
                count++;
            }
            first = first.nextSibling;
        }
        return count;
    },

    /**
     * �ж��Ƿ�Ϊ�սڵ�
     * @function
     * @param {Node}    node    �ڵ�
     * @return {Boolean}    �Ƿ�Ϊ�սڵ�
     */
    isEmptyNode:function (node) {
        return !node.firstChild || domUtils.getChildCount(node, function (node) {
            return  !domUtils.isBr(node) && !domUtils.isBookmarkNode(node) && !domUtils.isWhitespace(node)
        }) == 0
    },
    /**
     * ��սڵ����е�className
     * @function
     * @param {Array}    nodes    �ڵ�����
     */
    clearSelectedArr:function (nodes) {
        var node;
        while (node = nodes.pop()) {
            domUtils.removeAttributes(node, ['class']);
        }
    },
    /**
     * ����ʾ�����������ʾ�ڵ��λ��
     * @function
     * @param    {Node}   node    �ڵ�
     * @param    {window}   win      window����
     * @param    {Number}    offsetTop    �����Ϸ���ƫ����
     */
    scrollToView:function (node, win, offsetTop) {
        var getViewPaneSize = function () {
                var doc = win.document,
                    mode = doc.compatMode == 'CSS1Compat';
                return {
                    width:( mode ? doc.documentElement.clientWidth : doc.body.clientWidth ) || 0,
                    height:( mode ? doc.documentElement.clientHeight : doc.body.clientHeight ) || 0
                };
            },
            getScrollPosition = function (win) {
                if ('pageXOffset' in win) {
                    return {
                        x:win.pageXOffset || 0,
                        y:win.pageYOffset || 0
                    };
                }
                else {
                    var doc = win.document;
                    return {
                        x:doc.documentElement.scrollLeft || doc.body.scrollLeft || 0,
                        y:doc.documentElement.scrollTop || doc.body.scrollTop || 0
                    };
                }
            };
        var winHeight = getViewPaneSize().height, offset = winHeight * -1 + offsetTop;
        offset += (node.offsetHeight || 0);
        var elementPosition = domUtils.getXY(node);
        offset += elementPosition.y;
        var currentScroll = getScrollPosition(win).y;
        // offset += 50;
        if (offset > currentScroll || offset < currentScroll - winHeight) {
            win.scrollTo(0, offset + (offset < 0 ? -20 : 20));
        }
    },
    /**
     * �жϽڵ��Ƿ�Ϊbr
     * @function
     * @param {Node}    node   �ڵ�
     */
    isBr:function (node) {
        return node.nodeType == 1 && node.tagName == 'BR';
    },
    isFillChar:function (node,isInStart) {
        return node.nodeType == 3 && !node.nodeValue.replace(new RegExp((isInStart ? '^' : '' ) + domUtils.fillChar), '').length
    },
    isStartInblock:function (range) {
        var tmpRange = range.cloneRange(),
            flag = 0,
            start = tmpRange.startContainer,
            tmp;
        if(start.nodeType == 1 && start.childNodes[tmpRange.startOffset]){
            start = start.childNodes[tmpRange.startOffset];
            var pre = start.previousSibling;
            while(pre && domUtils.isFillChar(pre)){
                start = pre;
                pre = pre.previousSibling;
            }
        }
        if(this.isFillChar(start,true) && tmpRange.startOffset == 1){
            tmpRange.setStartBefore(start);
            start = tmpRange.startContainer;
        }

        while (start && domUtils.isFillChar(start)) {
            tmp = start;
            start = start.previousSibling
        }
        if (tmp) {
            tmpRange.setStartBefore(tmp);
            start = tmpRange.startContainer;
        }
        if (start.nodeType == 1 && domUtils.isEmptyNode(start) && tmpRange.startOffset == 1) {
            tmpRange.setStart(start, 0).collapse(true);
        }
        while (!tmpRange.startOffset) {
            start = tmpRange.startContainer;
            if (domUtils.isBlockElm(start) || domUtils.isBody(start)) {
                flag = 1;
                break;
            }
            var pre = tmpRange.startContainer.previousSibling,
                tmpNode;
            if (!pre) {
                tmpRange.setStartBefore(tmpRange.startContainer);
            } else {
                while (pre && domUtils.isFillChar(pre)) {
                    tmpNode = pre;
                    pre = pre.previousSibling;
                }
                if (tmpNode) {
                    tmpRange.setStartBefore(tmpNode);
                } else {
                    tmpRange.setStartBefore(tmpRange.startContainer);
                }
            }
        }
        return flag && !domUtils.isBody(tmpRange.startContainer) ? 1 : 0;
    },
    isEmptyBlock:function (node) {
        var reg = new RegExp('[ \t\r\n' + domUtils.fillChar + ']', 'g');
        if (node[browser.ie ? 'innerText' : 'textContent'].replace(reg, '').length > 0) {
            return 0;
        }
        for (var n in dtd.$isNotEmpty) {
            if (node.getElementsByTagName(n).length) {
                return 0;
            }
        }
        return 1;
    },

    setViewportOffset:function (element, offset) {
        var left = parseInt(element.style.left) | 0;
        var top = parseInt(element.style.top) | 0;
        var rect = element.getBoundingClientRect();
        var offsetLeft = offset.left - rect.left;
        var offsetTop = offset.top - rect.top;
        if (offsetLeft) {
            element.style.left = left + offsetLeft + 'px';
        }
        if (offsetTop) {
            element.style.top = top + offsetTop + 'px';
        }
    },
    fillNode:function (doc, node) {
        var tmpNode = browser.ie ? doc.createTextNode(domUtils.fillChar) : doc.createElement('br');
        node.innerHTML = '';
        node.appendChild(tmpNode);
    },
    moveChild:function (src, tag, dir) {
        while (src.firstChild) {
            if (dir && tag.firstChild) {
                tag.insertBefore(src.lastChild, tag.firstChild);
            } else {
                tag.appendChild(src.firstChild);
            }
        }
    },
    //�ж��Ƿ��ж�������
    hasNoAttributes:function (node) {
        return browser.ie ? /^<\w+\s*?>/.test(node.outerHTML) : node.attributes.length == 0;
    },
    //�ж��Ƿ��Ǳ༭���Զ���Ĳ���
    isCustomeNode:function (node) {
        return node.nodeType == 1 && node.getAttribute('_ue_custom_node_');
    },
    isTagNode:function (node, tagName) {
        return node.nodeType == 1 && new RegExp(node.tagName,'i').test(tagName)
    },
    /**
     * ����nodelist��filter���й���
     * @name filterNodeList
     * @since 1.2.4+
     * @grammar UE.dom.domUtils.filterNodeList(nodelist,filter,onlyFirst)  => �ڵ�
     * @example
     * UE.dom.domUtils.filterNodeList(document.getElementsByTagName('*'),'div p') //���ص�һ����div����p�Ľڵ�
     * UE.dom.domUtils.filterNodeList(document.getElementsByTagName('*'),function(n){return n.getAttribute('src')})
     * //���ص�һ����src���ԵĽڵ�
     * UE.dom.domUtils.filterNodeList(document.getElementsByTagName('*'),'i',true) //�������飬��߶���i�ڵ�
     */
    filterNodeList : function(nodelist,filter,forAll){
        var results = [];
        if(!utils .isFunction(filter)){
            var str = filter;
            filter = function(n){
                return utils.indexOf(utils.isArray(str) ? str:str.split(' '), n.tagName.toLowerCase()) != -1
            };
        }
        utils.each(nodelist,function(n){
            filter(n) && results.push(n)
        });
        return results.length  == 0 ? null : results.length == 1 || !forAll ? results[0] : results
    },

    isInNodeEndBoundary : function (rng,node){
        var start = rng.startContainer;
        if(start.nodeType == 3 && rng.startOffset != start.nodeValue.length){
            return 0;
        }
        if(start.nodeType == 1 && rng.startOffset != start.childNodes.length){
            return 0;
        }
        while(start !== node){
            if(start.nextSibling){
                return 0
            };
            start = start.parentNode;
        }
        return 1;
    },
    isBoundaryNode : function (node,dir){
        var tmp;
        while(!domUtils.isBody(node)){
            tmp = node;
            node = node.parentNode;
            if(tmp !== node[dir]){
                return false;
            }
        }
        return true;
    }
};
var fillCharReg = new RegExp(domUtils.fillChar, 'g');