///import core
///import plugins\inserthtml.js
///commands ������
///commandsName  InsertFrame
///commandsTitle  ����Iframe
///commandsDialog  dialogs\insertframe

UE.plugins['insertframe'] = function() {
   var me =this;
    function deleteIframe(){
        me._iframe && delete me._iframe;
    }

    me.addListener("selectionchange",function(){
        deleteIframe();
    });

};

