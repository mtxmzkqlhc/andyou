/*
 *   ����������ļ���������
 */
UE.plugins['keystrokes'] = function() {
    var me = this;

    me.addListener('keydown', function(type, evt) {
        var keyCode = evt.keyCode || evt.which,
            rng = me.selection.getRange();

        //����ȫѡ�����
        if(!rng.collapsed && !(evt.ctrlKey || evt.metaKey || evt.shiftKey || evt.altKey || keyCode == 9 )){

            var tmpNode = rng.startContainer;
            if(domUtils.isFillChar(tmpNode)){
                rng.setStartBefore(tmpNode)
            }
            tmpNode = rng.endContainer;
            if(domUtils.isFillChar(tmpNode)){
                rng.setEndAfter(tmpNode)
            }
            rng.txtToElmBoundary();
            //�����߽���ܷŵ���br��ǰ�ߣ�Ҫ��br��������
            // x[xxx]<br/>
            if(rng.endContainer && rng.endContainer.nodeType == 1){
                tmpNode = rng.endContainer.childNodes[rng.endOffset];
                if(tmpNode && domUtils.isBr(tmpNode)){
                    rng.setEndAfter(tmpNode);
                }
            }
            if(rng.startOffset == 0){
                tmpNode = rng.startContainer;
                if(domUtils.isBoundaryNode(tmpNode,'firstChild') ){
                    tmpNode = rng.endContainer;
                    if(rng.endOffset == (tmpNode.nodeType == 3 ? tmpNode.nodeValue.length : tmpNode.childNodes.length) && domUtils.isBoundaryNode(tmpNode,'lastChild')){
                        me.fireEvent('saveScene');
                        me.body.innerHTML = '<p>'+(browser.ie ? '' : '<br/>')+'</p>';
                        rng.setStart(me.body.firstChild,0).setCursor(false,true);
                        browser.ie && me._selectionChange();
                        domUtils.preventDefault(evt);
                        return;
                    }
                }
            }
        }


        //����backspace/del
        if (keyCode == 8) {//|| keyCode == 46
            var start,end;
            //���ⰴ����ɾ��������Ч������
            if(rng.inFillChar()){
                start = rng.startContainer;
                rng.setStartBefore(start).shrinkBoundary(true).collapse(true);
                if(domUtils.isFillChar(start)){
                    domUtils.remove(start)
                }else{
                    start.nodeValue = start.nodeValue.replace(new RegExp('^' + domUtils.fillChar ),'');
                }
            }

            //���ѡ��controlԪ�ز���ɾ��������
            if (start = rng.getClosedNode()) {
                me.fireEvent('saveScene');
                rng.setStartBefore(start);
                domUtils.remove(start);
                rng.setCursor();
                me.fireEvent('saveScene');
                domUtils.preventDefault(evt);
                return;
            }
            //��ֹ��table�ϵ�ɾ��
            if (!browser.ie) {
                start = domUtils.findParentByTagName(rng.startContainer, 'table', true);
                end = domUtils.findParentByTagName(rng.endContainer, 'table', true);
                if (start && !end || !start && end || start !== end) {
                    evt.preventDefault();
                    return;
                }
            }
        }
        //����tab�����߼�
        if (keyCode == 9) {
            //���������±�ǩ
            var excludeTagNameForTabKey = {
                'ol' : 1,
                'ul' : 1,
                'table':1
            };
            //����������tab�����¼�
            if(me.fireEvent('tabkeydown')){
                domUtils.preventDefault(evt);
                return;
            }
            range = me.selection.getRange();
            me.fireEvent('saveScene');
            for (var i = 0,txt = '',tabSize = me.options.tabSize|| 4,tabNode =  me.options.tabNode || '&nbsp;'; i < tabSize; i++) {
                txt += tabNode;
            }
            var span = me.document.createElement('span');
            span.innerHTML = txt + domUtils.fillChar;
            if (range.collapsed) {
                range.insertNode(span.cloneNode(true).firstChild).setCursor(true);
            } else {
                //��ͨ�����
                start = domUtils.findParent(range.startContainer, filterFn);
                end = domUtils.findParent(range.endContainer, filterFn);
                if (start && end && start === end) {
                    range.deleteContents();
                    range.insertNode(span.cloneNode(true).firstChild).setCursor(true);
                } else {
                    var bookmark = range.createBookmark(),
                        filterFn = function(node) {
                            return domUtils.isBlockElm(node) && !excludeTagNameForTabKey[node.tagName.toLowerCase()]

                        };
                    range.enlarge(true);
                    var bookmark2 = range.createBookmark(),
                        current = domUtils.getNextDomNode(bookmark2.start, false, filterFn);
                    while (current && !(domUtils.getPosition(current, bookmark2.end) & domUtils.POSITION_FOLLOWING)) {
                        current.insertBefore(span.cloneNode(true).firstChild, current.firstChild);
                        current = domUtils.getNextDomNode(current, false, filterFn);
                    }
                    range.moveToBookmark(bookmark2).moveToBookmark(bookmark).select();
                }
            }
            domUtils.preventDefault(evt)
        }
        //trace:1634
        //ff��del���������յ�ʱ��Ҳ��ɾ��
        if(browser.gecko && keyCode == 46){
            range = me.selection.getRange();
            if(range.collapsed){
                start = range.startContainer;
                if(domUtils.isEmptyBlock(start)){
                    var parent = start.parentNode;
                    while(domUtils.getChildCount(parent) == 1 && !domUtils.isBody(parent)){
                        start = parent;
                        parent = parent.parentNode;
                    }
                    if(start === parent.lastChild)
                        evt.preventDefault();
                    return;
                }
            }
        }
    });
    me.addListener('keyup', function(type, evt) {
        var keyCode = evt.keyCode || evt.which,
            rng;
        if(keyCode == 8){
            rng = me.selection.getRange();
            //����ɾ����bodyʱ��Ҫ���¸�p��ǩչλ
            if(rng.collapsed && domUtils.isBody(rng.startContainer)){
                var tmpNode = domUtils.createElement(me.document,'p',{
                    'innerHTML' : browser.ie ? domUtils.fillChar : '<br/>'
                });
                rng.insertNode(tmpNode).setStart(tmpNode,0).setCursor(false,true);
            }
//            //chrome�����ɾ����inline��ǩ����������м��䣬���������ֻ��ǻ����ϸղ�ɾ���ı�ǩ������������ѡһ�ξͲ�����
            if(browser.chrome && rng.collapsed && rng.startContainer.nodeType == 1 && domUtils.isEmptyBlock(rng.startContainer)){
                rng.select(true);
            }
        }
    })
};