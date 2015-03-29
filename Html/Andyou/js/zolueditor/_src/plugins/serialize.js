///import core
///commands ���ƹ��˹���
///commandsName  Serialize
///commandsTitle  ���ƹ��˹���
UE.plugins['serialize'] = function () {
    var ie = browser.ie,
        version = browser.version;

    function ptToPx(value){
        return /pt/.test(value) ? value.replace( /([\d.]+)pt/g, function( str ) {
            return  Math.round(parseFloat(str) * 96 / 72) + "px";
        } ) : value;
    }
    var me = this, autoClearEmptyNode = me.options.autoClearEmptyNode,
        EMPTY_TAG = dtd.$empty,
        parseHTML = function () {
            //�ɵ�<a> ����ÿո񣬱���</a>  �����Ŀո�
            var RE_PART = /<(?:(?:\/([^>]+)>)|(?:!--([\S|\s]*?)-->)|(?:([^\s\/>]+)\s*((?:(?:"[^"]*")|(?:'[^']*')|[^"'<>])*)\/?>))/g,
                RE_ATTR = /([\w\-:.]+)(?:(?:\s*=\s*(?:(?:"([^"]*)")|(?:'([^']*)')|([^\s>]+)))|(?=\s|$))/g,
                EMPTY_ATTR = {checked:1,compact:1,declare:1,defer:1,disabled:1,ismap:1,multiple:1,nohref:1,noresize:1,noshade:1,nowrap:1,readonly:1,selected:1},
                CDATA_TAG = {script:1,style: 1},
                NEED_PARENT_TAG = {
                    "li": { "$": 'ul', "ul": 1, "ol": 1 },
                    "dd": { "$": "dl", "dl": 1 },
                    "dt": { "$": "dl", "dl": 1 },
                    "option": { "$": "select", "select": 1 },
                    "td": { "$": "tr", "tr": 1 },
                    "th": { "$": "tr", "tr": 1 },
                    "tr": { "$": "tbody", "tbody": 1, "thead": 1, "tfoot": 1, "table": 1 },
                    "tbody": { "$": "table", 'table':1,"colgroup": 1 },
                    "thead": { "$": "table", "table": 1 },
                    "tfoot": { "$": "table", "table": 1 },
                    "col": { "$": "colgroup","colgroup":1 }
                };
            var NEED_CHILD_TAG = {
                "table": "td", "tbody": "td", "thead": "td", "tfoot": "td", //"tr": "td",
                "colgroup": "col",
                "ul": "li", "ol": "li",
                "dl": "dd",
                "select": "option"
            };

            function parse( html, callbacks ) {

                var match,
                    nextIndex = 0,
                    tagName,
                    cdata;
                RE_PART.exec( "" );
                while ( (match = RE_PART.exec( html )) ) {

                    var tagIndex = match.index;
                    if ( tagIndex > nextIndex ) {
                        var text = html.slice( nextIndex, tagIndex );
                        if ( cdata ) {
                            cdata.push( text );
                        } else {
                            callbacks.onText( text );
                        }
                    }
                    nextIndex = RE_PART.lastIndex;
                    if ( (tagName = match[1]) ) {
                        tagName = tagName.toLowerCase();
                        if ( cdata && tagName == cdata._tag_name ) {
                            callbacks.onCDATA( cdata.join( '' ) );
                            cdata = null;
                        }
                        if ( !cdata ) {
                            callbacks.onTagClose( tagName );
                            continue;
                        }
                    }
                    if ( cdata ) {
                        cdata.push( match[0] );
                        continue;
                    }
                    if ( (tagName = match[3]) ) {
                        if ( /="/.test( tagName ) ) {
                            continue;
                        }
                        tagName = tagName.toLowerCase();
                        var attrPart = match[4],
                            attrMatch,
                            attrMap = {},
                            selfClosing = attrPart && attrPart.slice( -1 ) == '/';
                        if ( attrPart ) {
                            RE_ATTR.exec( "" );
                            while ( (attrMatch = RE_ATTR.exec( attrPart )) ) {
                                var attrName = attrMatch[1].toLowerCase(),
                                    attrValue = attrMatch[2] || attrMatch[3] || attrMatch[4] || '';
                                if ( !attrValue && EMPTY_ATTR[attrName] ) {
                                    attrValue = attrName;
                                }
                                if ( attrName == 'style' ) {
                                    if ( ie && version <= 6 ) {
                                        attrValue = attrValue.replace( /(?!;)\s*([\w-]+):/g, function ( m, p1 ) {
                                            return p1.toLowerCase() + ':';
                                        } );
                                    }
                                }
                                //û��ֵ�����Բ����
                                if ( attrValue ) {
                                    attrMap[attrName] = attrValue.replace( /:\s*/g, ':' )
                                }

                            }
                        }
                        callbacks.onTagOpen( tagName, attrMap, selfClosing );
                        if ( !cdata && CDATA_TAG[tagName] ) {
                            cdata = [];
                            cdata._tag_name = tagName;
                        }
                        continue;
                    }
                    if ( (tagName = match[2]) ) {
                        callbacks.onComment( tagName );
                    }
                }
                if ( html.length > nextIndex ) {
                    callbacks.onText( html.slice( nextIndex, html.length ) );
                }
            }

            return function ( html, forceDtd ) {

                var fragment = {
                    type: 'fragment',
                    parent: null,
                    children: []
                };
                var currentNode = fragment;

                function addChild( node ) {
                    node.parent = currentNode;
                    currentNode.children.push( node );
                }

                function addElement( element, open ) {
                    var node = element;
                    // �����ṹ����ǩ��ʱ��
                    if ( NEED_PARENT_TAG[node.tag] ) {
                        // �������������ʱ��, ����֮ǰ�ı�ǩ
                        // e.g. <table><tr><td>12312`<tr>`4566
                        while ( NEED_PARENT_TAG[currentNode.tag] && NEED_PARENT_TAG[currentNode.tag][node.tag] ) {
                            currentNode = currentNode.parent;
                        }
                        // ���ǰһ����ǩ�������ǩ��ͬһ��, ����֮ǰ�ı�ǩ
                        // e.g. <ul><li>123<li>
                        if ( currentNode.tag == node.tag ) {
                            currentNode = currentNode.parent;
                        }
                        // ���ϲ��븸��ǩ
                        while ( NEED_PARENT_TAG[node.tag] ) {
                            if ( NEED_PARENT_TAG[node.tag][currentNode.tag] ) break;
                            node = node.parent = {
                                type: 'element',
                                tag: NEED_PARENT_TAG[node.tag]['$'],
                                attributes: {},
                                children: [node]
                            };
                        }
                    }
                    if ( forceDtd ) {
                        // ������������ǩ���ܷ���ǰһ����ǩ�ڲ��������ǰһ����ǩ,span��������
                        while ( dtd[node.tag] && !(currentNode.tag == 'span' ? utils.extend( dtd['strong'], {'a':1,'A':1} ) : (dtd[currentNode.tag] || dtd['div']))[node.tag] ) {
                            if ( tagEnd( currentNode ) ) continue;
                            if ( !currentNode.parent ) break;
                            currentNode = currentNode.parent;
                        }
                    }
                    node.parent = currentNode;
                    currentNode.children.push( node );
                    if ( open ) {
                        currentNode = element;
                    }
                    if ( element.attributes.style ) {
                        element.attributes.style = element.attributes.style.toLowerCase();
                    }
                    return element;
                }

                // ����һ����ǩ��ʱ����Ҫ�ж�һ�����Ƿ�ȱ���ӱ�ǩ
                // e.g. <table></table>
                function tagEnd( node ) {
                    var needTag;
                    if ( !node.children.length && (needTag = NEED_CHILD_TAG[node.tag]) ) {
                        addElement( {
                            type: 'element',
                            tag: needTag,
                            attributes: {},
                            children: []
                        }, true );
                        return true;
                    }
                    return false;
                }

                parse( html, {
                    onText: function ( text ) {

                        while ( !(dtd[currentNode.tag] || dtd['div'])['#'] ) {
                            //�ڵ�֮��Ŀհײ��ܵ����ڵ㴦��
//                                if(/^[ \t\r\n]+$/.test( text )){
//                                    return;
//                                }
                            if ( tagEnd( currentNode ) ) continue;
                            currentNode = currentNode.parent;
                        }
                        //if(/^[ \t\n\r]*/.test(text))
                        addChild( {
                            type: 'text',
                            data: text
                        } );

                    },
                    onComment: function ( text ) {
                        addChild( {
                            type: 'comment',
                            data: text
                        } );
                    },
                    onCDATA: function ( text ) {
                        while ( !(dtd[currentNode.tag] || dtd['div'])['#'] ) {
                            if ( tagEnd( currentNode ) ) continue;
                            currentNode = currentNode.parent;
                        }
                        addChild( {
                            type: 'cdata',
                            data: text
                        } );
                    },
                    onTagOpen: function ( tag, attrs, closed ) {
                        closed = closed || EMPTY_TAG[tag] ;
                        addElement( {
                            type: 'element',
                            tag: tag,
                            attributes: attrs,
                            closed: closed,
                            children: []
                        }, !closed );
                    },
                    onTagClose: function ( tag ) {
                        var node = currentNode;
                        // ������ƥ��ı�ǩ, ���ﲻ����dtd���������ΪtagOpen��ʱ���Ѿ��������, ���ﲻ������
                        while ( node && tag != node.tag ) {
                            node = node.parent;
                        }
                        if ( node ) {
                            // �ر��м�ı�ǩ
                            for ( var tnode = currentNode; tnode !== node.parent; tnode = tnode.parent ) {
                                tagEnd( tnode );
                            }
                            //ȥ���հ׵�inline�ڵ�
                            //��ҳ��ê�㱣��
                            //|| dtd.$removeEmptyBlock[node.tag])
//                                if ( !node.children.length && dtd.$removeEmpty[node.tag] && !node.attributes.anchorname && node.attributes['class'] != 'pagebreak' && node.tag != 'a') {
//
//                                    node.parent.children.pop();
//                                }
                            currentNode = node.parent;
                        } else {
                            // ���û���ҵ���ʼ��ǩ, �򴴽��±�ǩ
                            // eg. </div> => <div></div>
                            //���������վembed������������������⴦��һ��
                            if ( !(dtd.$removeEmpty[tag] || dtd.$removeEmptyBlock[tag] || tag == 'embed') ) {
                                node = {
                                    type: 'element',
                                    tag: tag,
                                    attributes: {},
                                    children: []
                                };
                                addElement( node, true );
                                tagEnd( node );
                                currentNode = node.parent;
                            }


                        }
                    }
                } );
                // �����������, ֻ�п�ʼ��ǩû�н�����ǩ�����, ��Ҫ�رտ�ʼ��ǩ
                // eg. <table>
                while ( currentNode !== fragment ) {
                    tagEnd( currentNode );
                    currentNode = currentNode.parent;
                }
                return fragment;
            };
        }();
    var unhtml1 = function () {
        var map = { '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };

        function rep( m ) {
            return map[m];
        }

        return function ( str ) {
            str = str + '';
            return str ? str.replace( /[<>"']/g, rep ) : '';
        };
    }();
    var toHTML = function () {
        function printChildren( node, pasteplain ) {
            var children = node.children;

            var buff = [];
            for ( var i = 0,ci; ci = children[i]; i++ ) {

                buff.push( toHTML( ci, pasteplain ) );
            }
            return buff.join( '' );
        }

        function printAttrs( attrs ) {
            var buff = [];
            for ( var k in attrs ) {
                var value = attrs[k];

                if(k == 'style'){

                    //pt==>px
                    value = ptToPx(value);
                    //color rgb ==> hex
                    if(/rgba?\s*\([^)]*\)/.test(value)){
                        value = value.replace( /rgba?\s*\(([^)]*)\)/g, function( str ) {
                            return utils.fixColor('color',str);
                        } )
                    }
                    //���˵����е�white-space,�ڴ��ı��༭����ճ�����������ݣ���chrome�л����span��white-space���ԣ����³��ֲ������е����
                    //����������ȥ���������
                    attrs[k] = utils.optCss(value.replace(/windowtext/g,'#000'))
                        .replace(/white-space[^;]+;/g,'');
                    if(!attrs[k]){
                        continue;
                    }
                }

                buff.push( k + '="' + unhtml1( attrs[k] ) + '"' );
            }
            return buff.join( ' ' )
        }

        function printData( node, notTrans ) {
            //trace:1399 ����html����ʱ�ո�ת����Ϊ&nbsp;
            //node.data.replace(/&nbsp;/g,' ') ���pre�еĿո�ͳ��ֵ�&nbsp;�������ڵõ���html�����ж�ת����Ϊ�ո�Ϊ����Դ��ģʽ����ʾΪ�ո������&nbsp;
            return notTrans ? node.data.replace(/&nbsp;/g,' ') : unhtml1( node.data ).replace(/ /g,'&nbsp;');
        }

        //���ı�ģʽ�±�ǩת��
        var transHtml = {
            'li':'p',
            'h1':'p','h2':'p','h3':'p','h4':'p','h5':'p','h6':'p',
            'tr':'p',
            'br':'br',
            'div':'p',
            'p':'p'//trace:1398 ����p��ǩ�Լ�Ҫ����p,����transHtml[tag]��undefined
        };
        var clearTagName ={
            'table':1,
            'tbody':1,
            'ol':1,
            'ul':1,
            'dt':1
        }
        function printElement( node, pasteplain ) {
            if ( node.type == 'element' && !node.children.length && (dtd.$removeEmpty[node.tag]) && node.tag != 'a' && utils.isEmptyObject(node.attributes) && autoClearEmptyNode) {// ê�㱣��
                return html;
            }
            var tag = node.tag;
            if ( pasteplain && tag == 'td' ) {
                if ( !html ) html = '';
                html += printChildren( node, pasteplain ) + '&nbsp;&nbsp;&nbsp;';
            } else {
                var attrs = printAttrs( node.attributes );

                var html = pasteplain && clearTagName[tag] ? '' :
                    '<' + (pasteplain && transHtml[tag] !==undefined? transHtml[tag] : tag) + (attrs ? ' ' + attrs : '') + (EMPTY_TAG[tag] ? ' />' : '>');
                if ( !EMPTY_TAG[tag] ) {
                    //trace:1627 ,2070
                    //p��ǩΪ�գ�����ռλ����ռλ���������ã���&nbsp;����br
                    if( tag == 'p' && !node.children.length){
                        html += browser.ie ? '&nbsp;' : '<br/>';
                    }
                    html += printChildren( node, pasteplain );
                    html +=(pasteplain && clearTagName[tag] ? '' : '</' + (pasteplain && transHtml[tag]!==undefined? transHtml[tag] : tag) + '>');
                }
            }

            return html;
        }

        return function ( node, pasteplain ) {
            if ( node.type == 'fragment' ) {
                return printChildren( node, pasteplain );
            } else if ( node.type == 'element' ) {
                return printElement( node, pasteplain );
            } else if ( node.type == 'text' || node.type == 'cdata' ) {
                return printData( node, dtd.$notTransContent[node.parent.tag] );
            } else if ( node.type == 'comment' ) {
                return '<!--' + node.data + '-->';
            }
            return '';
        };
    }();

    var NODE_NAME_MAP = {
        'text': '#text',
        'comment': '#comment',
        'cdata': '#cdata-section',
        'fragment': '#document-fragment'
    };


    //д��༭��ʱ�����ã�����ת������
    function transNode( node, word_img_flag ) {

        var sizeMap = [0, 10, 12, 16, 18, 24, 32, 48],
            attr,
            indexOf = utils.indexOf;
        switch ( node.tag ) {
            case 'script':
                node.tag = 'div';
                node.attributes._ue_org_tagName = 'script';
                node.attributes._ue_div_script = 1;
                node.attributes._ue_script_data = node.children[0] ? encodeURIComponent(node.children[0].data)  : '';
                node.attributes._ue_custom_node_ = 1;
                node.children = [];
                break;
            case 'style':
                node.tag = 'div';
                node.attributes._ue_div_style = 1;
                node.attributes._ue_org_tagName = 'style';
                node.attributes._ue_style_data = node.children[0] ? encodeURIComponent(node.children[0].data)  : '';
                node.attributes._ue_custom_node_ = 1;
                node.children = [];
                break;
            case 'img':
                //todo base64��ʱȥ���������Զ��ͼƬ�ϴ��󣬸ɵ����
                if(node.attributes.src && /^data:/.test(node.attributes.src)){
                    return {
                        type : 'fragment',
                        children:[]
                    }
                }
                if ( node.attributes.src && /^(?:file)/.test( node.attributes.src ) ) {
                    if ( !/(gif|bmp|png|jpg|jpeg)$/.test( node.attributes.src ) ) {
                        return {
                            type : 'fragment',
                            children:[]
                        }
                    }
                    node.attributes.word_img = node.attributes.src;
                    node.attributes.src = me.options.UEDITOR_HOME_URL + 'themes/default/images/spacer.gif';
                    var flag = parseInt(node.attributes.width)<128||parseInt(node.attributes.height)<43;
                    node.attributes.style="background:url(" + (flag? me.options.themePath+me.options.theme +"/images/word.gif":me.options.langPath+me.options.lang + "/images/localimage.png")+") no-repeat center center;border:1px solid #ddd";
                    //node.attributes.style = 'width:395px;height:173px;';
                    word_img_flag && (word_img_flag.flag = 1);
                }
                if(browser.ie && browser.version < 7 )
                    node.attributes.orgSrc = node.attributes.src;
                node.attributes.data_ue_src = node.attributes.data_ue_src || node.attributes.src;
                break;
            case 'li':
                var child = node.children[0];

                if ( !child || child.type != 'element' || child.tag != 'p' && dtd.p[child.tag] ) {
                    var tmpPNode = {
                        type: 'element',
                        tag: 'p',
                        attributes: {},

                        parent : node
                    };
                    tmpPNode.children = child ? node.children :[
                        browser.ie ? {
                            type:'text',
                            data:domUtils.fillChar,
                            parent : tmpPNode

                        }:
                        {
                            type : 'element',
                            tag : 'br',
                            attributes:{},
                            closed: true,
                            children: [],
                            parent : tmpPNode
                        }
                    ];
                    node.children =   [tmpPNode];
                }
                break;
            case 'table':
            case 'td':
                optStyle( node );
                break;
            case 'a'://ê�㣬a==>img
                if ( node.attributes['anchorname'] ) {
                    node.tag = 'img';
                    node.attributes = {
                        'class' : 'anchorclass',
                        'anchorname':node.attributes['name']
                    };
                    node.closed = 1;
                }
                node.attributes.href && (node.attributes.data_ue_src = node.attributes.href);
                break;
            case 'b':
                node.tag = node.name = 'strong';
                break;
            case 'i':
                node.tag = node.name = 'em';
                break;
            case 'u':
                node.tag = node.name = 'span';
                node.attributes.style = (node.attributes.style || '') + ';text-decoration:underline;';
                break;
            case 's':
            case 'del':
                node.tag = node.name = 'span';
                node.attributes.style = (node.attributes.style || '') + ';text-decoration:line-through;';
                if ( node.children.length == 1 ) {
                    child = node.children[0];
                    if ( child.tag == node.tag ) {
                        node.attributes.style += ";" + child.attributes.style;
                        node.children = child.children;

                    }
                }
                break;
            case 'span':

                var style = node.attributes.style;
                if ( style ) {
                    if ( !node.attributes.style  || browser.webkit && style == "white-space:nowrap;") {
                        delete node.attributes.style;
                    }
                }

                //���ff3.6span����ʽ������ȷ�̳е��޸�

                if(browser.gecko && browser.version <= 10902 && node.parent){
                    var parent = node.parent;
                    if(parent.tag == 'span' && parent.attributes && parent.attributes.style){
                        node.attributes.style = parent.attributes.style + ';' + node.attributes.style;
                    }
                }
                if ( utils.isEmptyObject( node.attributes ) && autoClearEmptyNode) {
                    node.type = 'fragment'
                }
                break;
            case 'font':
                node.tag = node.name = 'span';
                attr = node.attributes;
                node.attributes = {
                    'style': (attr.size ? 'font-size:' + (sizeMap[attr.size] || 12) + 'px' : '')
                        + ';' + (attr.color ? 'color:'+ attr.color : '')
                        + ';' + (attr.face ? 'font-family:'+ attr.face : '')
                        + ';' + (attr.style||'')
                };
/*
                while(node.parent.tag == node.tag && node.parent.children.length == 1){
                    node.attributes.style && (node.parent.attributes.style ? (node.parent.attributes.style += ";" + node.attributes.style) : (node.parent.attributes.style = node.attributes.style));
                    node.parent.children = node.children;
                    node = node.parent;

                }
*/
                break;
            /*������������
            case 'p':
                if ( node.attributes.align ) {
                    node.attributes.style = (node.attributes.style || '') + ';text-align:' +
                        node.attributes.align + ';';
                    delete node.attributes.align;
                }
            */

        }
        return node;
    }

    function optStyle( node ) {
        if ( ie && node.attributes.style ) {
            var style = node.attributes.style;
            node.attributes.style = style.replace(/;\s*/g,';');
            node.attributes.style = node.attributes.style.replace( /^\s*|\s*$/, '' )
        }
    }
    //getContent����ת��
    function transOutNode( node ) {

        switch ( node.tag ) {
            case 'div' :
                if(node.attributes._ue_div_script){
                    node.tag = 'script';
                    node.children = [{type:'cdata',data:node.attributes._ue_script_data?decodeURIComponent(node.attributes._ue_script_data):'',parent:node}];
                    delete node.attributes._ue_div_script;
                    delete node.attributes._ue_script_data;
                    delete node.attributes._ue_custom_node_;
                    delete node.attributes._ue_org_tagName;

                }
                if(node.attributes._ue_div_style){
                    node.tag = 'style';
                    node.children = [{type:'cdata',data:node.attributes._ue_style_data?decodeURIComponent(node.attributes._ue_style_data):'',parent:node}];
                    delete node.attributes._ue_div_style;
                    delete node.attributes._ue_style_data;
                    delete node.attributes._ue_custom_node_;
                    delete node.attributes._ue_org_tagName;

                }
                break;
            case 'table':
                !node.attributes.style && delete node.attributes.style;
                if ( ie && node.attributes.style ) {

                    optStyle( node );
                }
                break;
            case 'td':
            case 'th':
                if ( /display\s*:\s*none/i.test( node.attributes.style ) ) {
                    return {
                        type: 'fragment',
                        children: []
                    };
                }
                if ( ie && !node.children.length ) {
                    var txtNode = {
                        type: 'text',
                        data:domUtils.fillChar,
                        parent : node
                    };
                    node.children[0] = txtNode;
                }
                if ( ie && node.attributes.style ) {
                    optStyle( node );
                }
                break;
            case 'img'://ê�㣬img==>a
                if ( node.attributes.anchorname ) {
                    node.tag = 'a';
                    node.attributes = {
                        name : node.attributes.anchorname,
                        anchorname : 1
                    };
                    node.closed = null;
                }else{
                    if(node.attributes.data_ue_src){
                        node.attributes.src = node.attributes.data_ue_src;
                        delete node.attributes.data_ue_src;
                    }
                }
                break;

            case 'a':
                if(node.attributes.data_ue_src){
                    node.attributes.href = node.attributes.data_ue_src;
                    delete node.attributes.data_ue_src;
                }
        }

        return node;
    }

    function childrenAccept( node, visit, ctx ) {

        if ( !node.children || !node.children.length ) {
            return node;
        }
        var children = node.children;
        for ( var i = 0; i < children.length; i++ ) {
            var newNode = visit( children[i], ctx );
            if ( newNode.type == 'fragment' ) {
                var args = [i, 1];
                args.push.apply( args, newNode.children );
                children.splice.apply( children, args );
                //�ڵ�Ϊ�յľ͸ɵ�����Ȼ��ߵĲ�ȫ��������Ӷ���Ľڵ�
                if ( !children.length ) {
                    node = {
                        type: 'fragment',
                        children: []
                    }
                }
                i --;
            } else {
                children[i] = newNode;
            }
        }
        return node;
    }

    function Serialize( rules ) {
        this.rules = rules;
    }


    Serialize.prototype = {
        // NOTE: selectorĿǰֻ֧��tagName
        rules: null,
        // NOTE: node������fragment
        filter: function ( node, rules, modify ) {
            rules = rules || this.rules;
            var whiteList = rules && rules.whiteList;
            var blackList = rules && rules.blackList;

            function visitNode( node, parent ) {
                node.name = node.type == 'element' ?
                    node.tag : NODE_NAME_MAP[node.type];
                if ( parent == null ) {
                    return childrenAccept( node, visitNode, node );
                }

                if ( blackList && (blackList[node.name]|| (node.attributes && node.attributes._ue_org_tagName && blackList[node.attributes._ue_org_tagName]))) {
                    modify && (modify.flag = 1);
                    return {
                        type: 'fragment',
                        children: []
                    };
                }
                if ( whiteList ) {
                    if ( node.type == 'element' ) {
                        if ( parent.type == 'fragment' ? whiteList[node.name] : whiteList[node.name] && whiteList[parent.name][node.name] ) {

                            var props;
                            if ( (props = whiteList[node.name].$) ) {
                                var oldAttrs = node.attributes;
                                var newAttrs = {};
                                for ( var k in props ) {
                                    if ( oldAttrs[k] ) {
                                        newAttrs[k] = oldAttrs[k];
                                    }
                                }
                                node.attributes = newAttrs;
                            }


                        } else {
                            modify && (modify.flag = 1);
                            node.type = 'fragment';
                            // NOTE: ��������һ��hack
                            node.name = parent.name;
                        }
                    } else {
                        // NOTE: �ı�Ĭ������
                    }
                }
                if ( blackList || whiteList ) {
                    childrenAccept( node, visitNode, node );
                }
                return node;
            }

            return visitNode( node, null );
        },
        transformInput: function ( node, word_img_flag ) {

            function visitNode( node ) {
                node = transNode( node, word_img_flag );

                node = childrenAccept( node, visitNode, node );

                if ( me.options.pageBreakTag && node.type == 'text' && node.data.replace( /\s/g, '' ) == me.options.pageBreakTag ) {

                    node.type = 'element';
                    node.name = node.tag = 'hr';

                    delete node.data;
                    node.attributes = {
                        'class' : 'pagebreak',
                        noshade:"noshade",
                        size:"5",
                        'unselectable' : 'on',
                        'style' : 'moz-user-select:none;-khtml-user-select: none;'
                    };

                    node.children = [];

                }
                //ȥ������Ŀո�ͻ���
                if(node.type == 'text' && !dtd.$notTransContent[node.parent.tag]){
                    node.data = node.data.replace(/[\r\t\n]*/g,'')//.replace(/[ ]*$/g,'')
                }
                return node;
            }

            return visitNode( node );
        },
        transformOutput: function ( node ) {
            function visitNode( node ) {

                if ( node.tag == 'hr' && node.attributes['class'] == 'pagebreak' ) {
                    delete node.tag;
                    node.type = 'text';
                    node.data = me.options.pageBreakTag;
                    delete node.children;

                }
                node = transOutNode( node );
                node = childrenAccept( node, visitNode, node );
                return node;
            }

            return visitNode( node );
        },
        toHTML: toHTML,
        parseHTML: parseHTML,
        word: UE.filterWord
    };
    me.serialize = new Serialize( me.options.serialize || {});
    UE.serialize = new Serialize( {} );
};
