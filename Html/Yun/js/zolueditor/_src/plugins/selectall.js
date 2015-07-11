///import core
///commands ȫѡ
///commandsName  SelectAll
///commandsTitle  ȫѡ
/**
 * ѡ������
 * @function
 * @name baidu.editor.execCommand
 * @param   {String}   cmdName    selectallѡ�б༭�������������
 * @author zhanyi
*/
UE.plugins['selectall'] = function(){
    var me = this;
    me.commands['selectall'] = {
        execCommand : function(){
            //ȥ����ԭ����selectAll,��Ϊ����ֱ���͵�����Ϊ��ʱ�����ܳ��ֱպ�״̬�Ĺ��
            var me = this,body = me.body,
                range = me.selection.getRange();
            range.selectNodeContents(body);
            if(domUtils.isEmptyBlock(body)){
                //opera�����Զ��ϲ���Ԫ�ص���ߣ�Ҫ�ֶ�����һ��
                if(browser.opera && body.firstChild && body.firstChild.nodeType == 1){
                    range.setStartAtFirst(body.firstChild);
                }
                range.collapse(true);
            }
            range.select(true);
        },
        notNeedUndo : 1
    };
    function isBoundaryNode(node,dir){
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
    me.addListener('keydown', function(type, evt) {
        var rng = me.selection.getRange();

        if(!rng.collapsed && !(evt.ctrlKey || evt.metaKey || evt.shiftKey || evt.altKey)){
            var tmpNode = rng.startContainer;
            if(domUtils.isFillChar(tmpNode)){
                rng.setStartBefore(tmpNode)
            }
            tmpNode = rng.endContainer;
            if(domUtils.isFillChar(tmpNode)){
                rng.setEndAfter(tmpNode)
            }
            rng.txtToElmBoundary();
            if(rng.startOffset == 0){
                tmpNode = rng.startContainer;
                if(isBoundaryNode(tmpNode,'firstChild')){
                    tmpNode = rng.endContainer;
                    if(rng.endOffset == rng.endContainer.childNodes.length && isBoundaryNode(tmpNode,'lastChild') ){
                        me.fireEvent('saveScene');
                        me.body.innerHTML = '<p>'+(browser.ie ? '' : '<br/>')+'</p>';
                        rng.setStart(me.body.firstChild,0).setCursor(false,true);
                        me.fireEvent('saveScene');
                        browser.ie && me._selectionChange();
                        return;
                    }
                }
            }
        }
    });
    //��ݼ�
    me.addshortcutkey({
         "selectAll" : "ctrl+65"
    });
};
