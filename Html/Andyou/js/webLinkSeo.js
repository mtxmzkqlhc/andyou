(function(){
    $('#subcateId').change(function(){
            var subcateId = $(this).val();
            $("#manuId").rselect({ //Ʒ��
                'url' : '/?c=Ajax_Select&a=ManuOptions&subcateId='+subcateId
                ,topTxt:    'ѡ��Ʒ��' //Ĭ�ϵ��ı�
            });
    });
    $("#manuId").rselect({ //Ʒ��
        'url' : '/?c=Ajax_Select&a=ManuOptions&subcateId='+gSubcateId
        ,topTxt:    'ѡ��Ʒ��' //Ĭ�ϵ��ı�
        ,initVal:   gManuId
    });
    
    var objId = $('#areaSelect').find("option:selected").attr('obj');
    if(objId==1){ //1:��Ʒ�� 2:Ƶ�� 3:��̳
        $('#proDiv').show();
        $('#cmsDiv').hide();
    }else if(objId==2){
        $('#proDiv').hide();
        $('#cmsDiv').show();
    }
    
    $('#areaSelect').change(function(){
        var objid = $(this).find("option:selected").attr('obj');
        if(objid==1){ //1:��Ʒ�� 2:Ƶ�� 3:��̳
            $('#proDiv').show();
            $('#cmsDiv').hide();
        }else if(objid==2){
            $('#proDiv').hide();
            $('#cmsDiv').show();
        }
    });
    
})()

function syncContent() {
    //��ʾͨ������
    var dialogParam = {  //����content֮������ֵ��Ҫ�ı䣬Ҫ�õĻ��� ���Ƶ��ֲ������иı�
        lock : true, /* ���������ֲ㡿  */
        title:'��ʾ��Ϣ',
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

