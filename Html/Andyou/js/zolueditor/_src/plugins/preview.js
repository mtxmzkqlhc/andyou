///import core
///commands Ԥ��
///commandsName  Preview
///commandsTitle  Ԥ��
/**
 * Ԥ��
 * @function
 * @name baidu.editor.execCommand
 * @param   {String}   cmdName     previewԤ���༭������
 */
UE.commands['preview'] = {
    execCommand : function(){
        var w = window.open('', '_blank', ''),
            d = w.document;
        d.open();
        d.write('<html><head><script src="'+this.options.UEDITOR_HOME_URL+'uparse.js"></script><script>' +
            "setTimeout(function(){uParse('div',{" +
            "    'highlightJsUrl':'"+this.options.UEDITOR_HOME_URL+"third-party/SyntaxHighlighter/shCore.js'," +
                "    'highlightCssUrl':'"+this.options.UEDITOR_HOME_URL+"third-party/SyntaxHighlighter/shCoreDefault.css'" +
                "})},300)" +
            '</script></head><body><div>'+this.getContent(null,null,true)+'</div></body></html>');
        d.close();
    },
    notNeedUndo : 1
};
