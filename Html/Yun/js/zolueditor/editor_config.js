/* UEditor完整配置项 */
(function (){
    var URL = window.UEDITOR_HOME_URL||"/js/zolueditor/";
    window.UEDITOR_CONFIG = {
        //为编辑器实例添加一个路径，这个不能被注释
        UEDITOR_HOME_URL : URL
        //工具栏上的所有的功能按钮和下拉框，可以在new编辑器的实例时选择自己需要的从新定义
        ,toolbars:[
            ['source', '|', 'undo', 'redo', '|',
                'bold', 'italic', 'underline', 'strikethrough', 'removeformat','pasteplain', 'paragraph', 'customstyle' , 'forecolor', 
                'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|','anchor','link', 'unlink', 'spechars', 'insertorderedlist', 'insertunorderedlist', 'imagenone', 'imageleft', 'imageright','imagecenter', '|',
                'insertimage','insertvideo',
                'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', '|',
                'searchreplace']
        ]
    };
})();