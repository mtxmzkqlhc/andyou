///import core
///commands ������,ȡ������
///commandsName  Link,Unlink
///commandsTitle  ������,ȡ������
///commandsDialog  dialogs\link
/**
 * ������
 * @function
 * @name baidu.editor.execCommand
 * @param   {String}   cmdName     link���볬����
 * @param   {Object}  options         url��ַ��title���⣬target�Ƿ����ҳ
 * @author zhanyi
 */
/**
 * ȡ������
 * @function
 * @name baidu.editor.execCommand
 * @param   {String}   cmdName     unlinkȡ������
 * @author zhanyi
 */
(function() {
    function optimize( range ) {
        var start = range.startContainer,end = range.endContainer;

        if ( start = domUtils.findParentByTagName( start, 'a', true ) ) {
            range.setStartBefore( start );
        }
        if ( end = domUtils.findParentByTagName( end, 'a', true ) ) {
            range.setEndAfter( end );
        }
    }


    UE.commands['unlink'] = {
        execCommand : function() {
            var range = this.selection.getRange(),
                bookmark;
            if(range.collapsed && !domUtils.findParentByTagName( range.startContainer, 'a', true )){
                return;
            }
            bookmark = range.createBookmark();
            optimize( range );
            range.removeInlineStyle( 'a' ).moveToBookmark( bookmark ).select();
        },
        queryCommandState : function(){
            return !this.highlight && this.queryCommandValue('link') ?  0 : -1;
        }

    };
    function doLink(range,opt,me){
        var rngClone = range.cloneRange(),
            link = me.queryCommandValue('link');
        optimize( range = range.adjustmentBoundary() );
        var start = range.startContainer;
        if(start.nodeType == 1 && link){
            start = start.childNodes[range.startOffset];
            if(start && start.nodeType == 1 && start.tagName == 'A' && /^(?:https?|ftp|file)\s*:\s*\/\//.test(start[browser.ie?'innerText':'textContent'])){
                start[browser.ie ? 'innerText' : 'textContent'] =  utils.html(opt.textValue||opt.href);

            }
        }
        if( !rngClone.collapsed || link){
            range.removeInlineStyle( 'a' );
            rngClone = range.cloneRange();
        }

        if ( rngClone.collapsed ) {
            var a = range.document.createElement( 'a'),
                text = '';
            if(opt.textValue){

                text =   utils.html(opt.textValue);
                delete opt.textValue;
            }else{
                text =   utils.html(opt.href);

            }
            domUtils.setAttributes( a, opt );
            start =  domUtils.findParentByTagName( rngClone.startContainer, 'a', true );
            if(start && domUtils.isInNodeEndBoundary(rngClone,start)){
                range.setStartAfter(start).collapse(true);

            }
            a[browser.ie ? 'innerText' : 'textContent'] = text;
            range.insertNode(a).selectNode( a );
        } else {
            range.applyInlineStyle( 'a', opt );

        }
    }
    UE.commands['link'] = {
        execCommand : function( cmdName, opt ) {
            var range;
            opt.data_ue_src && (opt.data_ue_src = utils.unhtml(opt.data_ue_src,/[<">]/g));
            opt.href && (opt.href = utils.unhtml(opt.href,/[<">]/g));
            opt.textValue && (opt.textValue = utils.unhtml(opt.textValue,/[<">]/g));
            doLink(range=this.selection.getRange(),opt,this);
            //�պ϶�����ռλ����������˻���a��߶��ռλ���ڵ㣬����a��ͼƬ������ɵ��б����ֿհ�����
            range.collapse().select(true);

        },
        queryCommandValue : function() {
            var range = this.selection.getRange(),
                node;



            if ( range.collapsed ) {
//                    node = this.selection.getStart();
                //��ie��getstart()ȡֵƫ����
                node = range.startContainer;
                node = node.nodeType == 1 ? node : node.parentNode;

                if ( node && (node = domUtils.findParentByTagName( node, 'a', true )) && ! domUtils.isInNodeEndBoundary(range,node)) {

                    return node;
                }
            } else {
                //trace:1111  �����<p><a>xx</a></p> startContainer��p�ͻ��Ҳ���a
                range.shrinkBoundary();
                var start = range.startContainer.nodeType  == 3 || !range.startContainer.childNodes[range.startOffset] ? range.startContainer : range.startContainer.childNodes[range.startOffset],
                    end =  range.endContainer.nodeType == 3 || range.endOffset == 0 ? range.endContainer : range.endContainer.childNodes[range.endOffset-1],
                    common = range.getCommonAncestor();
                node = domUtils.findParentByTagName( common, 'a', true );
                if ( !node && common.nodeType == 1){

                    var as = common.getElementsByTagName( 'a' ),
                        ps,pe;

                    for ( var i = 0,ci; ci = as[i++]; ) {
                        ps = domUtils.getPosition( ci, start ),pe = domUtils.getPosition( ci,end);
                        if ( (ps & domUtils.POSITION_FOLLOWING || ps & domUtils.POSITION_CONTAINS)
                            &&
                            (pe & domUtils.POSITION_PRECEDING || pe & domUtils.POSITION_CONTAINS)
                            ) {
                            node = ci;
                            break;
                        }
                    }
                }
                return node;
            }

        },
        queryCommandState : function() {
            //�ж��������Ƶ�Ļ����Ӳ�����
            //fix 853
            var img = this.selection.getRange().getClosedNode(),
                flag = img && (img.className == "edui-faked-video");
            return flag ? -1 : 0;
        }
    };
})();
