/**
 * @file
 * @name UE.Editor
 * @short Editor
 * @import editor.js,core/utils.js,core/EventBase.js,core/browser.js,core/dom/dtd.js,core/dom/domUtils.js,core/dom/Range.js,core/dom/Selection.js,plugins/serialize.js
 * @desc �༭�����࣬�����༭���ṩ�Ĵ󲿷ֹ��ýӿ�
 */
(function () {
    var uid = 0,_selectionChangeTimer;

    /**
     * �滻src��href
     * @private
     * @ignore
     * @param div
     */
    function replaceSrc( div ) {
        var imgs = div.getElementsByTagName( "img" ),
                orgSrc;
        for ( var i = 0, img; img = imgs[i++]; ) {
            if ( orgSrc = img.getAttribute( "orgSrc" ) ) {
                img.src = orgSrc;
                img.removeAttribute( "orgSrc" );
            }
        }
        var as = div.getElementsByTagName( "a" );
        for ( var i = 0, ai; ai = as[i++]; i++ ) {
            if ( ai.getAttribute( 'data_ue_src' ) ) {
                ai.setAttribute( 'href', ai.getAttribute( 'data_ue_src' ) )
            }
        }
    }

    /**
     * @private
     * @ignore
     * @param form  �༭�����ڵ�formԪ��
     * @param editor  �༭��ʵ������
     */
    function setValue( form, editor ) {
        var textarea;
        if ( editor.textarea ) {
            if ( utils.isString( editor.textarea ) ) {
                for ( var i = 0, ti, tis = domUtils.getElementsByTagName( form, 'textarea' ); ti = tis[i++]; ) {
                    if ( ti.id == 'ueditor_textarea_' + editor.options.textarea ) {
                        textarea = ti;
                        break;
                    }
                }
            } else {
                textarea = editor.textarea;
            }
        }
        if ( !textarea ) {
            form.appendChild( textarea = domUtils.createElement( document, 'textarea', {
                'name':editor.options.textarea,
                'id':'ueditor_textarea_' + editor.options.textarea,
                'style':"display:none"
            } ) );
            //��Ҫ�������textarea
            editor.textarea = textarea;
        }
        textarea.value = editor.hasContents() ?
            (editor.options.allHtmlEnabled ? editor.getAllHtml() : editor.getContent(null,null,true)):
            ''
    }

    /**
     * UEditor�༭����
     * @name Editor
     * @desc ����һ�����༭��ʵ��
     * - ***container*** �༭����������
     * - ***iframe*** �༭�������ڵ�iframe����
     * - ***window*** �༭�������ڵ�window
     * - ***document*** �༭�������ڵ�document����
     * - ***body*** �༭�������ڵ�body����
     * - ***selection*** �༭�����ѡ������
     */
    var Editor = UE.Editor = function ( options ) {
        var me = this;
        me.uid = uid++;
        EventBase.call( me );
        me.commands = {};
        me.options = utils.extend( utils.clone(options || {}),UEDITOR_CONFIG, true );
        me.shortcutkeys = {};
        //����Ĭ�ϵĳ�������
        me.setOpt( {
            isShow:true,
            initialContent:'',
            autoClearinitialContent:false,
            iframeCssUrl:me.options.UEDITOR_HOME_URL + 'themes/iframe.css',
            textarea:'editorValue',
            focus:false,
            initialFrameWidth:1000,
            initialFrameHeight:me.options.minFrameHeight||320,//�����ϰ汾������
            minFrameWidth:800,
            minFrameHeight:220,
            autoClearEmptyNode:true,
            fullscreen:false,
            readonly:false,
            zIndex:999,
            imagePopup:true,
            enterTag:'p',
            pageBreakTag:'_baidu_page_break_tag_',
            customDomain:false,
            lang:'zh-cn',
            langPath:me.options.UEDITOR_HOME_URL + 'lang/',
            theme:'default',
            themePath:me.options.UEDITOR_HOME_URL + 'themes/',
            allHtmlEnabled:false,
            scaleEnabled:false,
            tableNativeEditInFF:false
        } );

        utils.loadFile( document, {
            src:me.options.langPath + me.options.lang + "/" + me.options.lang + ".js",
            tag:"script",
            type:"text/javascript",
            defer:"defer"
        }, function () {
            //��ʼ�����
            for ( var pi in UE.plugins ) {
                UE.plugins[pi].call( me );
            }
            me.langIsReady = true;

            me.fireEvent( "langReady" );
        });
        UE.instants['ueditorInstant' + me.uid] = me;
    };
    Editor.prototype = {
        /**
         * ���༭��ready��ִ�д����fn,����༭���Ѿ����ready��������ִ��fn��fn���е�this�Ǳ༭��ʵ����
         * �󲿷ֵ�ʵ���ӿڶ���Ҫ���ڸ÷����ڲ�ִ�У�������IE�¿��ܻᱨ��
         * @name ready
         * @grammar editor.ready(fn) fn�ǵ��༭����Ⱦ�ú�ִ�е�function
         * @example
         * var editor = new UE.ui.Editor();
         * editor.render("myEditor");
         * editor.ready(function(){
         *     editor.setContent("��ӭʹ��UEditor��");
         * })
         */
        ready:function ( fn ) {
            var me = this;
            if ( fn ){
                me.isReady ? fn.apply( me ) : me.addListener( 'ready', fn );
            }
        },
        /**
         * Ϊ�༭������Ĭ�ϲ���ֵ�����û�����Ϊ�գ�����Ĭ������Ϊ׼
         * @grammar editor.setOpt(key,value);      //����һ������ֵ��
         * @grammar editor.setOpt({ key:value});   //����һ��json����
         */
        setOpt:function ( key, val ) {
            var obj = {};
            if ( utils.isString( key ) ) {
                obj[key] = val
            } else {
                obj = key;
            }
            utils.extend( this.options, obj, true );
        },
        /**
         * ���ٱ༭��ʵ������
         * @name destroy
         * @grammar editor.destroy();
         */
        destroy:function () {

            var me = this;
            me.fireEvent( 'destroy' );
            var container = me.container.parentNode;
            var textarea = me.textarea;
            if(!textarea){
                textarea = document.createElement('textarea');
                container.parentNode.insertBefore(textarea,container);
            }else{
                textarea.style.display = ''
            }
            textarea.style.width = container.offsetWidth + 'px';
            textarea.style.height = container.offsetHeight + 'px';
            textarea.value = me.getContent();
            textarea.id = me.key;
            container.innerHTML = '';
            domUtils.remove( container );
            var key = me.key;
            //trace:2004
            for ( var p in me ) {
                if ( me.hasOwnProperty( p ) ) {
                    delete this[p];
                }
            }
            UE.delEditor(key);

        },
        /**
         * ��Ⱦ�༭����DOM��ָ��������������ֻ�ܵ���һ��
         * @name render
         * @grammar editor.render(containerId);    //����ָ��һ������ID
         * @grammar editor.render(containerDom);   //Ҳ����ֱ��ָ����������
         */
        render:function ( container ) {
            var me = this, options = me.options;
            if ( utils.isString(container) ) {
                container = document.getElementById( container );
            }
            if ( container ) {
                var useBodyAsViewport = ie && browser.version < 9,
                        html = ( ie && browser.version < 9 ? '' : '<!DOCTYPE html>') +
                                '<html xmlns=\'http://www.w3.org/1999/xhtml\'' + (!useBodyAsViewport ? ' class=\'view\'' : '') + '><head>' +
                                ( options.iframeCssUrl ? '<link rel=\'stylesheet\' type=\'text/css\' href=\'' + utils.unhtml( options.iframeCssUrl ) + '\'/>' : '' ) +
                                '<style type=\'text/css\'>' +
                            //�������ܵ�����
                                '.view{padding:0;word-wrap:break-word;cursor:text;height:100%;}\n' +
                            //����Ĭ��������ֺ�
                            //font-family���������ģ���safari��fillchar���н�������
                                'body{margin:8px;font-family:sans-serif;font-size:16px;}' +
                            //���ö�����
                                'p{margin:5px 0;}'
                                + ( options.initialStyle || '' ) +
                                '</style></head><body' + (useBodyAsViewport ? ' class=\'view\'' : '') + '></body>';
                if ( options.customDomain && document.domain != location.hostname ) {
                    html += '<script>window.parent.UE.instants[\'ueditorInstant' + me.uid + '\']._setup(document);</script></html>';
                    container.appendChild( domUtils.createElement( document, 'iframe', {
                        id:'baidu_editor_' + me.uid,
                        width:"100%",
                        height:"100%",
                        frameborder:"0",
                        src:'javascript:void(function(){document.open();document.domain="' + document.domain + '";' +
                                'document.write("' + html + '");document.close();}())'
                    } ) );
                } else {
                    container.innerHTML = '<iframe id="' + 'baidu_editor_' + this.uid + '"' + 'width="100%" height="100%" scroll="no" frameborder="0" ></iframe>';
                    var doc = container.firstChild.contentWindow.document;
                    //ȥ����ԭ�����ж�!browser.webkit����Ϊ�ᵼ��onloadע����¼�������
                    doc.open();
                    doc.write( html + '</html>' );
                    doc.close();
                    me._setup( doc );
                }
                container.style.overflow = 'hidden';
            }
        },
        /**
         * �༭����ʼ��
         * @private
         * @ignore
         * @param {Element} doc �༭��Iframe�е��ĵ�����
         */
        _setup:function ( doc ) {
            var me = this,
                    options = me.options;
            if ( ie ) {
                doc.body.disabled = true;
                doc.body.contentEditable = true;
                doc.body.disabled = false;
            } else {
                doc.body.contentEditable = true;
                doc.body.spellcheck = false;
            }
            me.document = doc;
            me.window = doc.defaultView || doc.parentWindow;
            me.iframe = me.window.frameElement;
            me.body = doc.body;
            //���ñ༭����С�߶�
            me.setHeight( Math.max(options.minFrameHeight, options.initialFrameHeight));
            me.selection = new dom.Selection( doc );
            //gecko��ʼ�����ܵõ�range,�޷��ж�isFocus��
            var geckoSel;
            if ( browser.gecko && (geckoSel = this.selection.getNative()) ) {
                geckoSel.removeAllRanges();
            }
            this._initEvents();
            if ( options.initialContent ) {
                if ( options.autoClearinitialContent ) {
                    var oldExecCommand = me.execCommand;
                    me.execCommand = function () {
                        me.fireEvent( 'firstBeforeExecCommand' );
                        return oldExecCommand.apply( me, arguments );
                    };
                    this._setDefaultContent( options.initialContent );
                } else
                    this.setContent( options.initialContent, false,true );
            }
            //Ϊform�ύ�ṩһ�����ص�textarea
            for ( var form = this.iframe.parentNode; !domUtils.isBody( form ); form = form.parentNode ) {
                if ( form.tagName == 'FORM' ) {
                    domUtils.on( form, 'submit', function () {
                        setValue( this, me );
                    } );
                    break;
                }
            }
            //�༭������Ϊ������
            if ( domUtils.isEmptyNode( me.body ) ) {
                me.body.innerHTML = '<p>' + (browser.ie ? '' : '<br/>') + '</p>';
            }
            //���Ҫ��focus, �Ͱѹ�궨λ�����ݿ�ʼ
            if ( options.focus ) {
                setTimeout( function () {
                    me.focus();
                    //����Զ�������ţ��Ͳ���Ҫ��selectionchange;
                    !me.options.autoClearinitialContent && me._selectionChange();
                },0);
            }
            if ( !me.container ) {
                me.container = this.iframe.parentNode;
            }
            if ( options.fullscreen && me.ui ) {
                me.ui.setFullScreen( true );
            }

            try {
                me.document.execCommand( '2D-position', false, false );
            } catch ( e ) {}
            try {
                me.document.execCommand( 'enableInlineTableEditing', false, false );
            } catch ( e ) {}
            try {
                me.document.execCommand( 'enableObjectResizing', false, false );
            } catch ( e ) {
//                domUtils.on(me.body,browser.ie ? 'resizestart' : 'resize', function( evt ) {
//                    domUtils.preventDefault(evt)
//                });
            }
            me._bindshortcutKeys();
            me.isReady = 1;
            me.fireEvent( 'ready' );
            options.onready && options.onready.call(me);
            if ( !browser.ie ) {//�޸�����ƫ������
//                domUtils.on( me.window, ['blur', 'focus'], function ( e ) {
//                    //chrome�»����alt+tab�л�ʱ������ѡ��λ�ò���
//                    if ( e.type == 'blur' ) {
//                        me._bakRange = me.selection.getRange();
//                        try{
//                            me.selection.getNative().removeAllRanges();
//                        }catch(e){}
//
//                    } else {
//                        try {
//                            me._bakRange && me._bakRange.select();
//                        } catch ( e ) {
//                        }
//                    }
//                } );
            }
            //trace:1518 ff3.6body���������ᵼ�µ���հ״��޷���ý���
            if ( browser.gecko && browser.version <= 10902 ) {
                //�޸�ff3.6��ʼ�����������ܵ����ý���
                me.body.contentEditable = false;
                setTimeout( function () {
                    me.body.contentEditable = true;
                }, 100 );
                setInterval( function () {
                    me.body.style.height = me.iframe.offsetHeight - 20 + 'px'
                }, 100 )
            }
            !options.isShow && me.setHide();
            options.readonly && me.setDisabled();
        },
        /**
         * ͬ���༭�������ݣ�Ϊ�ύ������׼������Ҫ���������ֶ��ύ�����
         * @name sync
         * @grammar editor.sync(); //�ӱ༭�����������ϲ��ң�����ҵ���ͬ������
         * @grammar editor.sync(formID); //formID�ƶ�һ��Ҫͬ�����ݵ�form��id,�༭�������ݻ�ͬ������ָ��form��
         * @desc
         * ��̨ȡ�����ݵü�ֵʹ���������ϵ�''name''���ԣ����û�о�ʹ�ò��������''textarea''
         * @example
         * editor.sync();
         * form.sumbit(); //form�����Ѿ�ָ����formԪ��
         *
         */
        sync:function ( formId ) {
            var me = this,
                    form = formId ? document.getElementById( formId ) :
                            domUtils.findParent( me.iframe.parentNode, function ( node ) {
                                return node.tagName == 'FORM'
                            }, true );
            form && setValue( form, me );
        },
        /**
         * ���ñ༭���߶�
         * @name setHeight
         * @grammar editor.setHeight(number);  //����ֵ��������λ
         */
        setHeight:function ( height ) {
            if ( height !== parseInt( this.iframe.parentNode.style.height ) ) {
                this.iframe.parentNode.style.height = height + 'px';
            }
            this.document.body.style.height = height - 20 + 'px';
        },

        addshortcutkey : function(cmd,keys){
            var obj = {};
            if(keys){
                obj[cmd] = keys
            }else{
                obj = cmd;
            }
            utils.extend(this.shortcutkeys,obj)
        },
        _bindshortcutKeys : function(){
            var me = this,shortcutkeys = this.shortcutkeys;
            me.addListener('keydown',function(type,e){
                var keyCode = e.keyCode || e.which;
                for ( var i in shortcutkeys ) {
                    var tmp = shortcutkeys[i].split(',');
                    for(var t= 0,ti;ti=tmp[t++];){
                        ti = ti.split(':');
                        var key = ti[0],param = ti[1];
                        if ( /^(ctrl)(\+shift)?\+(\d+)$/.test( key.toLowerCase() ) || /^(\d+)$/.test( key ) ) {
                            if ( ( (RegExp.$1 == 'ctrl' ? (e.ctrlKey||e.metaKey) : 0)
                                && (RegExp.$2 != "" ? e[RegExp.$2.slice(1) + "Key"] : 1)
                                && keyCode == RegExp.$3
                                ) ||
                                keyCode == RegExp.$1
                                ){
                                me.execCommand(i,param);
                                domUtils.preventDefault(e);
                            }
                        }
                    }

                }
            });
        },
        /**
         * ��ȡ�༭������
         * @name getContent
         * @grammar editor.getContent()  => String //���༭����ֻ�����ַ�"&lt;p&gt;&lt;br /&gt;&lt;/p/&gt;"�᷵�ؿա�
         * @grammar editor.getContent(fn)  => String
         * @example
         * getContentĬ���ǻ��ֵ���hasContents���жϱ༭���Ƿ�Ϊ�գ�����ǣ���ֱ�ӷ��ؿ��ַ���
         * ��Ҳ���Դ���һ��fn������hasContents�Ĺ����������жϵĹ���
         * editor.getContent(function(){
         *     return false //�༭��û������ ��getContentֱ�ӷ��ؿ�
         * })
         */
        getContent:function ( cmd, fn, isPreview ) {
            var me = this;
            if ( cmd && utils.isFunction( cmd ) ) {
                fn = cmd;
                cmd = '';
            }
            if ( fn ? !fn() : !this.hasContents() ) {
                return '';
            }
            var range = me.selection.getRange(),
                address = range.createAddress();
            me.fireEvent( 'beforegetcontent', cmd );
            var reg = new RegExp( domUtils.fillChar, 'g' ),
            //ie��ȡ�õ�html���ܻ���\n���ڣ�Ҫȥ�����ڴ���replace(/[\t\r\n]*/g,'');���������\n����ȥ��
                    html = me.body.innerHTML.replace( reg, '' ).replace( />[\t\r\n]*?</g, '><' );
            me.fireEvent( 'aftergetcontent', cmd );
//IE���Զ���������������
//            try{
//                range.moveToAddress(address).select(true);
//            }catch(e){}
            if ( me.serialize ) {
                var node = me.serialize.parseHTML( html );
                node = me.serialize.transformOutput( node );
                html = me.serialize.toHTML( node );
            }

            if ( ie && isPreview ) {
                //trace:2471
                //����br�ᵼ�¿��У�����������ע�ӵ�
                html = html//.replace(/<\s*br\s*\/?\s*>/gi,'<br/><br/>')
                        .replace( /<p>\s*?<\/p>/g, '<p>&nbsp;</p>' );
            } else {
                //���&nbsp;Ҫת���ɿո��&nbsp;����ʽ��Ҫ��Ԥ��ʱ������һ��
                html = html.replace( /(&nbsp;)+/g, function ( s ) {
                    for ( var i = 0, str = [], l = s.split( ';' ).length - 1; i < l; i++ ) {
                        str.push( '&nbsp;' );
                        //str.push( i % 2 == 0 ? ' ' : '&nbsp;' );
                    }
                    return str.join( '' );
                } );
            }

            return  html;

        },
        /**
         * ȡ��������html���룬����ֱ����ʾ��������html�ĵ�
         * @name getAllHtml
         * @grammar editor.getAllHtml()  => String
         */
        getAllHtml:function () {
            var me = this,
                    headHtml = [],
                    html = '';
            me.fireEvent( 'getAllHtml', headHtml );
            if(browser.ie && browser.version > 8){
                var headHtmlForIE9= '';
                utils.each(me.document.styleSheets,function(si){
                    headHtmlForIE9 += ( si.href ? '<link rel="stylesheet" type="text/css" href="'+si.href+'" />': '<style>'+si.cssText+'</style>');
                });
                utils.each(me.document.getElementsByTagName('script'),function(si){
                    headHtmlForIE9 += si.outerHTML;
                });

            }
            return '<html><head>' + (me.options.charset ? '<meta http-equiv="Content-Type" content="text/html; charset=' + me.options.charset + '"/>' : '')
                + (headHtmlForIE9 || me.document.getElementsByTagName( 'head' )[0].innerHTML) + headHtml.join('\n') + '</head>'
                    + '<body ' + (ie && browser.version < 9 ? 'class="view"' : '') + '>' + me.getContent( null, null, true ) + '</body></html>';
        },
        /**
         * �õ��༭���Ĵ��ı����ݣ����ᱣ�������ʽ
         * @name getPlainTxt
         * @grammar editor.getPlainTxt()  => String
         */
        getPlainTxt:function () {
            var reg = new RegExp( domUtils.fillChar, 'g' ),
                    html = this.body.innerHTML.replace( /[\n\r]/g, '' );//ieҪ��ȥ��\n�ڴ���
            html = html.replace( /<(p|div)[^>]*>(<br\/?>|&nbsp;)<\/\1>/gi, '\n' )
                    .replace( /<br\/?>/gi, '\n' )
                    .replace( /<[^>/]+>/g, '' )
                    .replace( /(\n)?<\/([^>]+)>/g, function ( a, b, c ) {
                        return dtd.$block[c] ? '\n' : b ? b : '';
                    } );
            //ȡ�����Ŀո����c2a0�������룬�����������\u00a0
            return html.replace( reg, '' ).replace( /\u00a0/g, ' ' ).replace( /&nbsp;/g, ' ' );
        },

        /**
         * ��ȡ�༭���еĴ��ı�����,û�ж����ʽ
         * @name getContentTxt
         * @grammar editor.getContentTxt()  => String
         */
        getContentTxt:function () {
            var reg = new RegExp( domUtils.fillChar, 'g' );
            //ȡ�����Ŀո����c2a0�������룬�����������\u00a0
            return this.body[browser.ie ? 'innerText' : 'textContent'].replace( reg, '' ).replace( /\u00a0/g, ' ' );
        },

        /**
         * ��html���õ��༭����, ��������ڳ�ʼ��ʱ���༭������ֵ����������ready�����ڲ�ִ��
         * @name setContent
         * @grammar editor.setContent(html)
         * @example
         * var editor = new UE.ui.Editor()
         * editor.ready(function(){
         *     //��Ҫready��ִ�У�������ܱ���
         *     editor.setContent("��ӭʹ��UEditor��");
         * })
         */
        setContent:function ( html, isAppendTo,notFireSelectionchange ) {
            var me = this,
                    inline = utils.extend( {a:1, A:1}, dtd.$inline, true ),
                    lastTagName;

            html = html
                    .replace( /^[ \t\r\n]*?</, '<' )
                    .replace( />[ \t\r\n]*?$/, '>' )
                    //ie��ʱ��Դ�����>&nbsp;<�����
                    //.replace(/>(?:(\s|&nbsp;)*?)</g,'><' )//���������\n����ȥ�� * @modifier : xuzongsheng �޸�ͼƬ������֮��Ŀո�����
                    .replace( /[\s\/]?(\w+)?>[ \t\r\n]*?<\/?(\w+)/gi, function ( a, b, c ) {
                        if ( b ) {
                            lastTagName = c;
                        } else {
                            b = lastTagName;
                        }
                        return !inline[b] && !inline[c] ? a.replace( />[ \t\r\n]*?</, '><' ) : a;
                    } );
            html = {'html':html};
            me.fireEvent( 'beforesetcontent',html );
            html = html.html;
            var serialize = this.serialize;
            if ( serialize ) {
                var node = serialize.parseHTML( html );
                node = serialize.transformInput( node );
                node = serialize.filter( node );
                html = serialize.toHTML( node );
            }
            //html.replace(new RegExp('[\t\n\r' + domUtils.fillChar + ']*','g'),'');
            //ȥ����\t\n\r ����в���Ĵ��룬��Դ���л�����������ģʽʱ�����ж�������
            //\r��ie�µĲ��ɼ��ַ�����Դ���л�ʱ���ɶ��&nbsp;
            //trace:1559
            this.body.innerHTML = (isAppendTo ? this.getContent() : '') + html.replace( new RegExp( '[\r' + domUtils.fillChar + ']*', 'g' ), '' );
            //����ie6��innerHTML�Զ������·��ת���ɾ���·��������
            if ( browser.ie && browser.version < 7 ) {
                replaceSrc( this.document.body );
            }
            //���ı�����inline�ڵ���p��ǩ
            if ( me.options.enterTag == 'p' ) {

                var child = this.body.firstChild, tmpNode;
                if ( !child || child.nodeType == 1 &&
                        (dtd.$cdata[child.tagName] ||
                                domUtils.isCustomeNode( child )
                                )
                        && child === this.body.lastChild ) {
                    this.body.innerHTML = '<p>' + (browser.ie ? '&nbsp;' : '<br/>') + '</p>' + this.body.innerHTML;

                } else {
                    var p = me.document.createElement( 'p' );
                    while ( child ) {
                        while ( child && (child.nodeType == 3 || child.nodeType == 1 && dtd.p[child.tagName] && !dtd.$cdata[child.tagName]) ) {
                            tmpNode = child.nextSibling;
                            p.appendChild( child );
                            child = tmpNode;
                        }
                        if ( p.firstChild ) {
                            if ( !child ) {
                                me.body.appendChild( p );
                                break;
                            } else {
                                child.parentNode.insertBefore( p, child );
                                p = me.document.createElement( 'p' );
                            }
                        }
                        child = child.nextSibling;
                    }
                }
            }
            me.fireEvent( 'aftersetcontent' );
            me.fireEvent( 'contentchange' );
            !notFireSelectionchange && me._selectionChange();
            //��������ѡ��
            me._bakRange = me._bakIERange = null;
            //trace:1742 setContent��gecko�ܵõ���������
            var geckoSel;
            if ( browser.gecko && (geckoSel = this.selection.getNative()) ) {
                geckoSel.removeAllRanges();
            }
        },

        /**
         * �ñ༭����ý��㣬toEndȷ��focusλ��
         * @name focus
         * @grammar editor.focus([toEnd])   //Ĭ��focus���༭��ͷ����toEndΪtrueʱfocus������β��
         */
        focus:function ( toEnd ) {
            try {
                var me = this,
                        rng = me.selection.getRange();
                if ( toEnd ) {
                    rng.setStartAtLast( me.body.lastChild ).setCursor( false, true );
                } else {
                    rng.select( true );
                }
            } catch ( e ) {
            }
        },

        /**
         * ��ʼ��UE�¼��������¼�����
         * @private
         * @ignore
         */
        _initEvents:function () {
            var me = this,
                    doc = me.document,
                    win = me.window;
            me._proxyDomEvent = utils.bind( me._proxyDomEvent, me );
            domUtils.on( doc, ['click', 'contextmenu', 'mousedown', 'keydown', 'keyup', 'keypress', 'mouseup', 'mouseover', 'mouseout', 'selectstart'], me._proxyDomEvent );
            domUtils.on( win, ['focus', 'blur'], me._proxyDomEvent );
            domUtils.on( doc, ['mouseup', 'keydown'], function ( evt ) {
                //�����������selectionchange
                if ( evt.type == 'keydown' && (evt.ctrlKey || evt.metaKey || evt.shiftKey || evt.altKey) ) {
                    return;
                }
                if ( evt.button == 2 )return;
                me._selectionChange( 250, evt );
            } );
            //������ק
            //ie ff���ܴ��������
            //chromeֻ��Դ������������ݹ���
            var innerDrag = 0, source = browser.ie ? me.body : me.document, dragoverHandler;
            domUtils.on( source, 'dragstart', function () {
                innerDrag = 1;
            } );
            domUtils.on( source, browser.webkit ? 'dragover' : 'drop', function () {
                return browser.webkit ?
                        function () {
                            clearTimeout( dragoverHandler );
                            dragoverHandler = setTimeout( function () {
                                if ( !innerDrag ) {
                                    var sel = me.selection,
                                            range = sel.getRange();
                                    if ( range ) {
                                        var common = range.getCommonAncestor();
                                        if ( common && me.serialize ) {
                                            var f = me.serialize,
                                                    node =
                                                            f.filter(
                                                                    f.transformInput(
                                                                            f.parseHTML(
                                                                                    f.word( common.innerHTML )
                                                                            )
                                                                    )
                                                            );
                                            common.innerHTML = f.toHTML( node );
                                        }
                                    }
                                }
                                innerDrag = 0;
                            }, 200 );
                        } :
                        function ( e ) {
                            if ( !innerDrag ) {
                                e.preventDefault ? e.preventDefault() : (e.returnValue = false);
                            }
                            innerDrag = 0;
                        }
            }() );
        },
        /**
         * �����¼�����
         * @private
         * @ignore
         */
        _proxyDomEvent:function ( evt ) {
            return this.fireEvent( evt.type.replace( /^on/, '' ), evt );
        },
        /**
         * �仯ѡ��
         * @private
         * @ignore
         */
        _selectionChange:function ( delay, evt ) {
            var me = this;
            //�й�����selectionchange Ϊ�˽��δfocusʱ���source���ܴ������Ĺ�����״̬�����⣨source����notNeedUndo=1��
//            if ( !me.selection.isFocus() ){
//                return;
//            }
            var hackForMouseUp = false;
            var mouseX, mouseY;
            if ( browser.ie && browser.version < 9 && evt && evt.type == 'mouseup' ) {
                var range = this.selection.getRange();
                if ( !range.collapsed ) {
                    hackForMouseUp = true;
                    mouseX = evt.clientX;
                    mouseY = evt.clientY;
                }
            }
            clearTimeout( _selectionChangeTimer );
            _selectionChangeTimer = setTimeout( function () {
                if ( !me.selection.getNative() ) {
                    return;
                }
                //�޸�һ��IE�µ�bug: �����һ����ѡ����ı��м�ʱ��������mouseup���һ��ʱ����ȡ����range����selection��typeΪNone�µĴ���ֵ.
                //IE������û�����קһ����ѡ���ı����򲻻ᴥ��mouseup�¼���������������⴦���������Ӱ��
                var ieRange;
                if ( hackForMouseUp && me.selection.getNative().type == 'None' ) {
                    ieRange = me.document.body.createTextRange();
                    try {
                        ieRange.moveToPoint( mouseX, mouseY );
                    } catch ( ex ) {
                        ieRange = null;
                    }
                }
                var bakGetIERange;
                if ( ieRange ) {
                    bakGetIERange = me.selection.getIERange;
                    me.selection.getIERange = function () {
                        return ieRange;
                    };
                }
                me.selection.cache();
                if ( bakGetIERange ) {
                    me.selection.getIERange = bakGetIERange;
                }
                if ( me.selection._cachedRange && me.selection._cachedStartElement ) {
                    me.fireEvent( 'beforeselectionchange' );
                    // �ڶ�������causeByUiΪtrue�������û�������ɵ�selectionchange.
                    me.fireEvent( 'selectionchange', !!evt );
                    me.fireEvent( 'afterselectionchange' );
                    me.selection.clear();
                }
            }, delay || 50 );
        },
        _callCmdFn:function ( fnName, args ) {
            var cmdName = args[0].toLowerCase(),
                    cmd, cmdFn;
            cmd = this.commands[cmdName] || UE.commands[cmdName];
            cmdFn = cmd && cmd[fnName];
            //û��querycommandstate����û��command�Ķ�Ĭ�Ϸ���0
            if ( (!cmd || !cmdFn) && fnName == 'queryCommandState' ) {
                return 0;
            } else if ( cmdFn ) {
                return cmdFn.apply( this, args );
            }
        },

        /**
         * ִ�б༭����cmdName����ɸ��ı��༭Ч��
         * @name execCommand
         * @grammar editor.execCommand(cmdName)   => {*}
         */
        execCommand:function ( cmdName ) {
            cmdName = cmdName.toLowerCase();
            var me = this,
                    result,
                    cmd = me.commands[cmdName] || UE.commands[cmdName];
            if ( !cmd || !cmd.execCommand ) {
                return null;
            }
            if ( !cmd.notNeedUndo && !me.__hasEnterExecCommand ) {
                me.__hasEnterExecCommand = true;
                if ( me.queryCommandState( cmdName ) != -1 ) {
                    me.fireEvent( 'beforeexeccommand', cmdName );
                    result = this._callCmdFn( 'execCommand', arguments );
                    !me._ignoreContentChange && me.fireEvent('contentchange');
                    me.fireEvent( 'afterexeccommand', cmdName );
                }
                me.__hasEnterExecCommand = false;
            } else {
                result = this._callCmdFn( 'execCommand', arguments );
                !me._ignoreContentChange && me.fireEvent('contentchange')
            }
            !me._ignoreContentChange && me._selectionChange();
            return result;
        },
        /**
         * ���ݴ����command�����ѡ�༭����ǰ��ѡ�������������״̬
         * @name  queryCommandState
         * @grammar editor.queryCommandState(cmdName)  => (-1|0|1)
         * @desc
         * * ''-1'' ��ǰ�������
         * * ''0'' ��ǰ�������
         * * ''1'' ��ǰ�����Ѿ�ִ�й���
         */
        queryCommandState:function ( cmdName ) {
            return this._callCmdFn( 'queryCommandState', arguments );
        },

        /**
         * ���ݴ����command�����ѡ�༭����ǰ��ѡ���������������ص�ֵ
         * @name  queryCommandValue
         * @grammar editor.queryCommandValue(cmdName)  =>  {*}
         */
        queryCommandValue:function ( cmdName ) {
            return this._callCmdFn( 'queryCommandValue', arguments );
        },
        /**
         * ���༭�������Ƿ������ݣ�������tags�еĽڵ����ͣ�ֱ�ӷ���true
         * @name  hasContents
         * @desc
         * Ĭ�����ı����ݣ����������½ڵ㶼����Ϊ�ǿ�
         * <code>{table:1,ul:1,ol:1,dl:1,iframe:1,area:1,base:1,col:1,hr:1,img:1,embed:1,input:1,link:1,meta:1,param:1}</code>
         * @grammar editor.hasContents()  => (true|false)
         * @grammar editor.hasContents(tags)  =>  (true|false)  //���ĵ��а���tags�������Ӧ��tag��ֱ�ӷ���true
         * @example
         * editor.hasContents(['span']) //����༭��������Щ������Ϊ�ǿ�
         */
        hasContents:function ( tags ) {
            if ( tags ) {
                for ( var i = 0, ci; ci = tags[i++]; ) {
                    if ( this.document.getElementsByTagName( ci ).length > 0 ) {
                        return true;
                    }
                }
            }
            if ( !domUtils.isEmptyBlock( this.body ) ) {
                return true
            }
            //��ʱ���,����������ǩ������ڣ�������Ϊ�ǿ�
            tags = ['div'];
            for ( i = 0; ci = tags[i++]; ) {
                var nodes = domUtils.getElementsByTagName( this.document, ci );
                for ( var n = 0, cn; cn = nodes[n++]; ) {
                    if ( domUtils.isCustomeNode( cn ) ) {
                        return true;
                    }
                }
            }
            return false;
        },
        /**
         * ���ñ༭���������������tabʹ��ͬһ���༭��ʵ��
         * @name  reset
         * @desc
         * * ��ձ༭������
         * * ��ջ����б�
         * @grammar editor.reset()
         */
        reset:function () {
            this.fireEvent( 'reset' );
        },
        setEnabled:function () {
            var me = this, range;
            if ( me.body.contentEditable == 'false' ) {
                me.body.contentEditable = true;
                range = me.selection.getRange();
                //�п������ݶ�ʧ��
                try {
                    range.moveToBookmark( me.lastBk );
                    delete me.lastBk
                } catch ( e ) {
                    range.setStartAtFirst( me.body ).collapse( true )
                }
                range.select( true );
                if ( me.bkqueryCommandState ) {
                    me.queryCommandState = me.bkqueryCommandState;
                    delete me.bkqueryCommandState;
                }
                me.fireEvent( 'selectionchange' );
            }
        },
        /**
         * ���õ�ǰ�༭������Ա༭
         * @name enable
         * @grammar editor.enable()
         */
        enable:function(){
            return this.setEnabled();
        },
        setDisabled:function ( except ) {
            var me = this;
            except = except ? utils.isArray( except ) ? except : [except] : [];
            if ( me.body.contentEditable == 'true' ) {
                if ( !me.lastBk ) {
                    me.lastBk = me.selection.getRange().createBookmark( true );
                }
                me.body.contentEditable = false;
                me.bkqueryCommandState = me.queryCommandState;
                me.queryCommandState = function ( type ) {
                    if ( utils.indexOf( except, type ) != -1 ) {
                        return me.bkqueryCommandState.apply( me, arguments );
                    }
                    return -1;
                };
                me.fireEvent( 'selectionchange' );
            }
        },
        /** ���õ�ǰ�༭���򲻿ɱ༭,except�е��������
         * @name disable
         * @grammar editor.disable()
         * @grammar editor.disable(except)  //��������Ҳ����ʹ������disable���˴����õ�������Ȼ����ִ��
         * @example
         * //���ù������г��ӴֺͲ���ͼƬ֮������й���
         * editor.disable(['bold','insertimage']);//�����ǵ�һ��String,Ҳ������Array
        */
        disable:function(except){
            return this.setDisabled(except);
        },
        /**
         * ����Ĭ������
         * @ignore
         * @private
         * @param  {String} cont Ҫ���������
         */
        _setDefaultContent:function () {
            function clear() {
                var me = this;
                if ( me.document.getElementById( 'initContent' ) ) {
                    me.body.innerHTML = '<p>' + (ie ? '' : '<br/>') + '</p>';
                    me.removeListener( 'firstBeforeExecCommand focus', clear );
                    setTimeout( function () {
                        me.focus();
                        me._selectionChange();
                    },0 )
                }
            }
            return function ( cont ) {
                var me = this;
                me.body.innerHTML = '<p id="initContent">' + cont + '</p>';
                if ( browser.ie && browser.version < 7 ) {
                    replaceSrc( me.body );
                }
                me.addListener( 'firstBeforeExecCommand focus', clear );
            }
        }(),
        /**
         * show�����ļ��ݰ汾
         * @private
         * @ignore
         */
        setShow:function () {
            var me = this,range = me.selection.getRange();
            if ( me.container.style.display == 'none' ) {
                //�п������ݶ�ʧ��
                try {
                    range.moveToBookmark( me.lastBk );
                    delete me.lastBk
                } catch ( e ) {
                    range.setStartAtFirst( me.body ).collapse( true )
                }
                //ie��focusʵЧ���������˸��ӳ�
               setTimeout(function(){
                   range.select( true );
               },100);
                me.container.style.display = '';
            }

        },
        /**
         * ��ʾ�༭��
         * @name show
         * @grammar editor.show()
         */
        show:function(){
            return this.setShow();
        },
        /**
         * hide�����ļ��ݰ汾
         * @private
         * @ignore
         */
        setHide:function () {
            var me = this;
            if ( !me.lastBk ) {
                me.lastBk = me.selection.getRange().createBookmark( true );
            }
            me.container.style.display = 'none'
        },
        /**
         * ���ر༭��
         * @name hide
         * @grammar editor.hide()
         */
        hide:function(){
            return this.setHide();
        },
        /**
         * �����ƶ���·������ȡ��Ӧ��������Դ
         * @name  getLang
         * @grammar editor.getLang(path)  =>  ��JSON|String) ·�����ݵ���langĿ¼�µ������ļ���·���ṹ
         * @example
         * editor.getLang('contextMenu.delete') //�����ǰ�����ģ��Ƿ����ǵ���ɾ��
         */
        getLang:function ( path ) {
            var lang = UE.I18N[this.options.lang];
            if(!lang){
                throw Error("not import language file");
            }
            path = (path || "").split( "." );
            for ( var i = 0, ci; ci = path[i++]; ) {
                lang = lang[ci];
                if ( !lang )break;
            }
            return lang;
        },
        /**
         * ����༭����ǰ���ݵĳ���
         * @name  getContentLength
         * @grammar editor.getContentLength(ingoneHtml,tagNames)  =>
         * @example
         * editor.getLang(true)
         */
        getContentLength : function(ingoneHtml,tagNames){
            var count = this.getContent().length;
            if(ingoneHtml){
                tagNames = (tagNames||[]).concat([ 'hr','img','iframe']);
                count = this.getContentTxt().replace(/[\t\r\n]+/g,'').length;
                for(var i= 0,ci;ci=tagNames[i++];){
                    count += this.document.getElementsByTagName(ci).length;
                }
            }
            return count;
        }
        /**
         * �õ�dialogʵ������
         * @name getDialog
         * @grammar editor.getDialog(dialogName) => Object
         * @example
         * var dialog = editor.getDialog("insertimage");
         * dialog.open();   //��dialog
         * dialog.close();  //�ر�dialog
         */
    };
    utils.inherits( Editor, EventBase );
})();

