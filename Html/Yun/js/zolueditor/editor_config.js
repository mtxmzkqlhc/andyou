/* UEditor���������� */
(function (){
    var URL = window.UEDITOR_HOME_URL||"/js/zolueditor/";
    window.UEDITOR_CONFIG = {
        //Ϊ�༭��ʵ�����һ��·����������ܱ�ע��
        UEDITOR_HOME_URL : URL
        //�������ϵ����еĹ��ܰ�ť�������򣬿�����new�༭����ʵ��ʱѡ���Լ���Ҫ�Ĵ��¶���
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