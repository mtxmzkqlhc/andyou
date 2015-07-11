///import core
///import plugins/inserthtml.js
///import plugins/undo.js
///import plugins/serialize.js
///commands ճ��
///commandsName  PastePlain
///commandsTitle  ���ı�ճ��ģʽ
/*
 ** @description ճ��
 * @author zhanyi
 */
(function() {
    function getClipboardData( callback ) {

        var doc = this.document;

        if ( doc.getElementById( 'baidu_pastebin' ) ) {
            return;
        }

        var range = this.selection.getRange(),
            bk = range.createBookmark(),
        //��������������div
            pastebin = doc.createElement( 'div' );

        pastebin.id = 'baidu_pastebin';


        // Safari Ҫ��div���������ݣ�����ճ�����ݽ���
        browser.webkit && pastebin.appendChild( doc.createTextNode( domUtils.fillChar + domUtils.fillChar ) );
        doc.body.appendChild( pastebin );
        //trace:717 ���ص�span���ܵõ�top
        //bk.start.innerHTML = '&nbsp;';
        bk.start.style.display = '';
        pastebin.style.cssText = "position:absolute;width:1px;height:1px;overflow:hidden;left:-1000px;white-space:nowrap;top:" +
            //Ҫ�����ڹ��ƽ�е�λ�ü��룬������������������
            domUtils.getXY( bk.start ).y + 'px';

        range.selectNodeContents( pastebin ).select( true );

        setTimeout( function() {

            if (browser.webkit) {

                for(var i=0,pastebins = doc.querySelectorAll('#baidu_pastebin'),pi;pi=pastebins[i++];){
                    if(domUtils.isEmptyNode(pi)){
                        domUtils.remove(pi);
                    }else{
                        pastebin = pi;
                        break;
                    }
                }


            }

            try{
                pastebin.parentNode.removeChild(pastebin);
            }catch(e){}

            range.moveToBookmark( bk ).select(true);
            callback( pastebin );
        }, 0 );


    }

    UE.plugins['paste'] = function() {
        var me = this;
        var word_img_flag = {flag:""};

        var pasteplain = me.options.pasteplain === true;
        var modify_num = {flag:""};
        me.commands['pasteplain'] = {
            queryCommandState: function (){
                return pasteplain;
            },
            execCommand: function (){
                pasteplain = !pasteplain|0;
            },
            notNeedUndo : 1
        };
        var txtContent,htmlContent,address;

        function filter(div){

            var html;
            if ( div.firstChild ) {
                //ȥ��cut����ӵı߽�ֵ
                var nodes = domUtils.getElementsByTagName(div,'span');
                  for(var i=0,ni;ni=nodes[i++];){
                    if(ni.id == '_baidu_cut_start' || ni.id == '_baidu_cut_end'){
                        domUtils.remove(ni);
                    }
                }

                if(browser.webkit){

                    var brs = div.querySelectorAll('div br');
                    for(var i=0,bi;bi=brs[i++];){
                        var pN = bi.parentNode;
                        if(pN.tagName == 'DIV' && pN.childNodes.length ==1){
                            pN.innerHTML = '<p><br/></p>';

                            domUtils.remove(pN);
                        }
                    }
                    var divs = div.querySelectorAll('#baidu_pastebin');
                    for(var i=0,di;di=divs[i++];){
                        var tmpP = me.document.createElement('p');
                        di.parentNode.insertBefore(tmpP,di);
                        while(di.firstChild){
                            tmpP.appendChild(di.firstChild);
                        }
                        domUtils.remove(di);
                    }

                    var metas = div.querySelectorAll('meta');
                    for(var i=0,ci;ci=metas[i++];){
                        domUtils.remove(ci);
                    }

                    var brs = div.querySelectorAll('br');
                    for(i=0;ci=brs[i++];){
                        if(/^apple-/.test(ci)){
                            domUtils.remove(ci);
                        }
                    }

                    utils.each(domUtils.getElementsByTagName(div,'span',function(node){
                        if(node.style.cssText){
                            node.style.cssText =  node.style.cssText.replace(/white-space[^;]+;/g,'');
                            if(!node.style.cssText){
                                domUtils.removeAttributes(node,'style');
                                if(domUtils.hasNoAttributes(node)){
                                    return 1
                                }
                            }
                        }
                        return 0
                    }),function(si){
                        domUtils.remove(si,true)
                    })
                }
                if(browser.gecko){
                    var dirtyNodes = div.querySelectorAll('[_moz_dirty]');
                    for(i=0;ci=dirtyNodes[i++];){
                        ci.removeAttribute( '_moz_dirty' );
                    }
                }
                if(!browser.ie ){
                    var spans = div.querySelectorAll('span.Apple-style-span');
                    for(var i=0,ci;ci=spans[i++];){
                        domUtils.remove(ci,true);
                    }
                }

                //ie��ʹ��innerHTML����������\r\n�ַ���Ҳ�����&nbsp;������˵�
                //html = div.innerHTML.replace(/>(?:(\s|&nbsp;)*?)</g,'><');
				html = div.innerHTML.replace(/>(?:(\s)*?)</g,'><');
				// Remove mso-xxx styles.
				html = html.replace( /\s*mso-[^:]+:[^;"]+;?/gi,'');
				// Remove Class attributes
				html = html.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/gi,"<$1$3");
				html = html.replace( /<SPAN\s*>([\s\S]*?)<\/SPAN>/gi,'$1');
				html = html.replace( /<FONT\s*>([\s\S]*?)<\/FONT>/gi,'$1');
                var f = me.serialize;
                if(f){
                    //������˳������⣬��������ֱ�Ӳ������ݣ�������ִ�����ճ������ʧ��
                    try{
                        html = UE.filterWord(html);

                        var node =  f.transformInput(
                            f.parseHTML(
                                //todo: ��ʱ����dtd�Ĺ���
                                html//, true
                            ),word_img_flag
                        );
                        //trace:924
                        //���ı�ģʽҲҪ��������
                        node = f.filter(node,pasteplain ? {
                            whiteList: {
                                'p': {'br':1,'BR':1,$:{}},
                                'br':{'$':{}},
                                'div':{'br':1,'BR':1,'$':{}},
                                'li':{'$':{}},
                                'tr':{'td':1,'$':{}},
                                'td':{'$':{}}

                            },
                            blackList: {
                                'style':1,
                                'script':1,
                                'object':1
                            }
                        } : null, !pasteplain ? modify_num : null);

                        if(browser.webkit){
                            var length = node.children.length,
                                child;
                            while((child = node.children[length-1]) && child.tag == 'br'){
                                node.children.splice(length-1,1);
                                length = node.children.length;
                            }
                        }

                        html = f.toHTML(node,pasteplain);

                        txtContent = f.filter(node,{
                            whiteList: {
                                'p': {'br':1,'BR':1,$:{}},
                                'br':{'$':{}},
                                'div':{'br':1,'BR':1,'$':{},'table':1,'ul':1,'ol':1},
                                'li':{'$':{}},
                                'ul':{'li':1,'$':{}},
                                'ol':{'li':1,'$':{}},
                                'tr':{'td':1,'$':{}},
                                'td':{'$':{}},
                                'table': {'tr':1,'tbody':1,'td':1,'$':{}},
                                'tbody': {'tr':1,'td':1,'$':{}},
                                h1:{'$':{}},h2:{'$':{}},h3:{'$':{}},h4:{'$':{}},h5:{'$':{}},h6:{'$':{}}
                            },
                            blackList: {
                                'style':1,
                                'script':1,
                                'object':1
                            }
                        });

                        txtContent = f.toHTML(txtContent,true)

                    }catch(e){}

                }

                //�Զ���Ĵ���
                html = {'html':html,'txtContent':txtContent};

                //me.fireEvent('beforepaste',html);  * @modifier : xuzongsheng ����ճ����񲼾��ҵ�����
                //�������߹�����
                if(html.html){
                    htmlContent = html.html;
                    address = me.selection.getRange().createAddress(true);
                    me.execCommand( 'insertHtml',htmlContent,true);
                    me.fireEvent("afterpaste");
                }

            }
        }

        me.addListener('pasteTransfer',function(cmd,plainType){
            if(address && txtContent && htmlContent && txtContent != htmlContent){
                var range = me.selection.getRange();
                range.moveToAddress(address,true).deleteContents();
                range.select(true);
                me.__hasEnterExecCommand = true;
                var html = htmlContent;
                if(plainType === 2){
                    html = html.replace(/<(\/?)([\w\-]+)([^>]*)>/gi,function(a,b,tagName,attrs){
                        tagName = tagName.toLowerCase();
                        if({img:1}[tagName]){
                            return a;
                        }
                        attrs = attrs.replace(/([\w\-]*?)\s*=\s*(("([^"]*)")|('([^']*)')|([^\s>]+))/gi,function(str,atr,val){
                            if({
                                'src':1,
                                'href':1,
                                'name':1
                            }[atr.toLowerCase()]){
                                return atr + '=' + val + ' '
                            }
                            return ''
                        });
                        if({
                            'span':1,
                            'div':1
                        }[tagName]){
                            return ''
                        }else{

                            return '<' + b + tagName + ' ' + utils.trim(attrs) + '>'
                        }

                    });
                }else if(plainType){
                    html = txtContent;
                }
                me.execCommand('inserthtml',html,true);
                me.__hasEnterExecCommand = false;
                var tmpAddress = me.selection.getRange().createAddress(true);
                address.endAddress = tmpAddress.startAddress;
            }
        });
        me.addListener('ready',function(){
            domUtils.on(me.body,'cut',function(){
                var range = me.selection.getRange();
                if(!range.collapsed && me.undoManger){
                    me.undoManger.save();
                }

            });
            //ie��beforepaste�ڵ���Ҽ�ʱҲ�ᴥ���������ü�ؼ��̲Ŵ���
            domUtils.on(me.body, browser.ie || browser.opera ? 'keydown' : 'paste',function(e){
                if((browser.ie || browser.opera) && ((!e.ctrlKey && !e.metaKey) || e.keyCode != '86')){
                    return;
                }
                getClipboardData.call( me, function( div ) {
                    filter(div);
                } );
            });

        });

    };

})();

