(function(){
    $('#subcateId').change(function(){
            var subcateId = $(this).val();
            $("#manuId").rselect({ //品牌
                'url' : '/?c=Ajax_Select&a=ManuOptions&subcateId='+subcateId
                ,topTxt:    '选择品牌' //默认的文本
            });
    });
    $("#manuId").rselect({ //品牌
        'url' : '/?c=Ajax_Select&a=ManuOptions&subcateId='+gSubcateId
        ,topTxt:    '选择品牌' //默认的文本
        ,initVal:   gManuId
    });
    
    var objId = $('#areaSelect').find("option:selected").attr('obj');
    if(objId==1){ //1:产品库 2:频道 3:论坛
        $('#proDiv').show();
        $('#cmsDiv').hide();
    }else if(objId==2){
        $('#proDiv').hide();
        $('#cmsDiv').show();
    }
    
    $('#areaSelect').change(function(){
        var objid = $(this).find("option:selected").attr('obj');
        if(objid==1){ //1:产品库 2:频道 3:论坛
            $('#proDiv').show();
            $('#cmsDiv').hide();
        }else if(objid==2){
            $('#proDiv').hide();
            $('#cmsDiv').show();
        }
    });
    
})()

function syncContent() {
    //提示通用配置
    var dialogParam = {  //除了content之外其他值不要改变，要用的话请 复制到局部变量中改变
        lock : true, /* 锁屏【遮罩层】  */
        title:'提示消息',
        width : '242px',
        height: '117px',
        icon:'error',  // error,message,message,succeed,warning
        ok: function () {
                        this.close();
                        return false;
                    },
        content:''
    };
    $('#objVal').val($('#areaSelect').find("option:selected").attr('obj'));
    $('#editform').submit();
}

