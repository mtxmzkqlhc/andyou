///import core
///commands �Զ��ύ
///commandsName  autosubmit
///commandsTitle  �Զ��ύ
UE.plugins['autosubmit'] = function(){
    var me = this;
    me.commands['autosubmit'] = {
        execCommand:function () {
            var me=this,
                form = domUtils.findParentByTagName(me.iframe,"form", false);
            if (form)    {
                if(me.fireEvent("beforesubmit")===false){
                    return;
                }
                me.sync();
                form.submit();
            }
        }
    };
    //��ݼ�
    me.addshortcutkey({
        "autosubmit" : "ctrl+13" //�ֶ��ύ
    });
};
