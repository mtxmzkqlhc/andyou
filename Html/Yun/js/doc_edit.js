/****************** ��ʼ���༭�� ******************/
var zolEditor = UE.getEditor('editContent',{
    initialFrameWidth:960,        //��ʼ���༭�����
    initialFrameHeight:500,       //��ʼ���༭���߶�
    autoHeightEnabled:false,      //�Ƿ��Զ�����
    autoFloatEnabled:false,       //�Ƿ񱣳�toolbar��λ�ò���
    wordCount:false,              //�ر�����ͳ��
    elementPathEnabled:false,     //�ر�elementPath
    catchRemoteImageEnable:false, //�Ƿ���Զ��ͼƬץȡ
    focus:true,                   //�Ƿ��Զ���ȡ����
    autoClearinitialContent:false,//�Զ��������
    initialStyle:'body{font:14px/22px SimSun;background:#FFF;}p{margin-bottom:14px;}h1{border-top:1px solid #6A9CC3;font-family:microsoft yahei; background-color:#D6E3F1;font-size:14px;height:30px;line-height:30px;text-indent:8px;}'
});
zolEditor.ready(function(){
    //�༭����ʼ����ִ��
});

/****************** ��ʼ��ͼƬ�ϴ� ******************/
var uploadConfig  = {
    btnObj      : $(".uploadBtn")  //��ťID
    ,flash_url  : "http://uppic.fd.zol-img.com.cn/swfupload.swf"
    ,module     : 'common'         //�ϴ�ģ��
    ,btnText    : '�ϴ�ͼƬ'        //��ť���ı�
    ,single     : 0                //�Ƿ��ϴ�����һ���ļ�
    ,callback   : function(data){
    if (!data) {
        alert("ͼƬ������Ҫ��");
        return false;
    }
    var imgsrc = "http://i2.uppic.fd.zol-img.com.cn/"+data.filename;
    var data = '<img src="' + imgsrc + '"/>';
    zolEditor.execCommand("insertHtml", data);
    }
};
$(".uploadArea").uploadInput(uploadConfig);