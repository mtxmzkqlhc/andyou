/****************** 初始化编辑器 ******************/
var zolEditor = UE.getEditor('editContent',{
    initialFrameWidth:960,        //初始化编辑器宽度
    initialFrameHeight:500,       //初始化编辑器高度
    autoHeightEnabled:false,      //是否自动长高
    autoFloatEnabled:false,       //是否保持toolbar的位置不动
    wordCount:false,              //关闭字数统计
    elementPathEnabled:false,     //关闭elementPath
    catchRemoteImageEnable:false, //是否开启远程图片抓取
    focus:true,                   //是否自动获取焦点
    autoClearinitialContent:false,//自动清除内容
    initialStyle:'body{font:14px/22px SimSun;background:#FFF;}p{margin-bottom:14px;}h1{border-top:1px solid #6A9CC3;font-family:microsoft yahei; background-color:#D6E3F1;font-size:14px;height:30px;line-height:30px;text-indent:8px;}'
});
zolEditor.ready(function(){
    //编辑器初始化后执行
});

/****************** 初始化图片上传 ******************/
var uploadConfig  = {
    btnObj      : $(".uploadBtn")  //按钮ID
    ,flash_url  : "http://uppic.fd.zol-img.com.cn/swfupload.swf"
    ,module     : 'common'         //上传模块
    ,btnText    : '上传图片'        //按钮的文本
    ,single     : 0                //是否上传单独一个文件
    ,callback   : function(data){
    if (!data) {
        alert("图片不符合要求");
        return false;
    }
    var imgsrc = "http://i2.uppic.fd.zol-img.com.cn/"+data.filename;
    var data = '<img src="' + imgsrc + '"/>';
    zolEditor.execCommand("insertHtml", data);
    }
};
$(".uploadArea").uploadInput(uploadConfig);