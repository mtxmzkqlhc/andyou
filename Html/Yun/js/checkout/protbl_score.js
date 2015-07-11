/* 
 *  �Ҳ���Ĳ���
 */


//׷�ӵ���Ʒ�б�
var proTableTr = document.getElementById('proTableTr').innerHTML;
var proJuicer  = juicer(proTableTr);
var proTrIdx   = 0; //��¼�����˵ڼ�����
var appendProTable = function(proInfo){
    var disc = 1;//memberDisc;
    //��ò�Ʒ���ڷ�����ۿ�
    if(memberDiscArr){
        if(proInfo.cateId in memberDiscArr){
            disc = memberDiscArr[proInfo.cateId];
        }
    }
    if(proInfo.discut && proInfo.discut > disc){
        disc = proInfo.discut;
    }
    //���ò�Ʒ�Ƿ��ڱ�����Ѿ��ڴ����ˣ�����Ѿ������ˣ�ֻҪ�������+1�Ϳ�����
    var hasSamePro = false;
    if(proTrIdx > 0){
        $(".proTblItemIds").each(function(){
            var v = $(this).val();
            if(v == proInfo.id){
                var idx = $(this).attr("data-idx");
                proTblAddNum(idx);
                hasSamePro = true;
                
                proTblCalPrice(idx);//���㵥�м۸�
                return false;
            }
        })
    }
    if(!hasSamePro){
        var data = {pro:proInfo,rowIdx:proTrIdx,memberDisc:disc};
        var html = proJuicer.render(data);
        if(proTrIdx == 0)$("#proListTbody").empty();
        $("#proListTbody").append(html);
        proTblCalPrice(proTrIdx);//���㵥�м۸�
        proTrIdx++;
    }
    calcBillSumInfo();//�������Ķ����۸�
    //��ʾ�����Ʒ�İ�ť
    $("#removeGoodsBtn").show();
};
//�����Ʒ
var proSelectTableTr = document.getElementById('proSelectTableTr').innerHTML;
var proSelectJuicer  = juicer(proSelectTableTr);
var appendSelectProTable = function(list,num){
    var data = {list:list,num:num};
    var html = proSelectJuicer.render(data);
    $("#proAddBoxTbody").empty();
    $("#proAddBoxTbody").append(html);
};

//��ѡ�����ѡ��һ����Ʒ
var boxSelectPro = function(pid){
    var len = selectProBoxData.length;
    if(len > 0){
        var proInfo = {}
        for(var i=0;i<len;i++){
            if(selectProBoxData[i].id == pid){
                proInfo = selectProBoxData[i];
                break;
            }
        }
        appendProTable(proInfo);
    }
    if(selectProBoxDlg != null)selectProBoxDlg.close();
    $("#proBarCode").focus(); 
}

//���������ۿ�
$("#setDiscBtn").click(function(){
    art.dialog({
        title : '������Ʒ�ۿ�',
        content: '<div style="padding:40px 80px;font-size:12px;">�����ۿۣ� <input type="text" value="1" id="dlgSetDisVal" style="width:50px"></div>',
        button: [{
            name: '����',
            callback: function () {
                var v = $("#dlgSetDisVal").val();
                if(v > 1)v = 1;
                $(".tblProDisc").each(function(){
                    $(this).val(v);
                    var idx = $(this).attr("data-idx");
                    proTblCalPrice(idx);
                })
                return true;
            },
            focus: true
        }]
    })

});



//������ɨ��
var selectProBoxDlg = null;
var selectProBoxData = []; //�����Ѿ��洢��Ʒ����
iptEnter("#proBarCode",function(){
    doSearchPro();        
});
$("#searchProBtn").click(function(){
    doSearchPro();
})
var doSearchPro = function(){

    var barcode = $("#proBarCode").val();
    if(barcode){//doGetProductByCode
        var t = Date.parse(new Date()); 
        var url = "?c=Ajax_Product&a=GetProductByCode&fromScore=1&code=" + barcode+"&t="+t;
        $.getJSON(url,{},function(data){
            if(data){

                if(data.num == 1){
                    var proInfo = data.data[0];
                    appendProTable(proInfo);
                }else if(data.num > 1){//���һ�������ж����Ʒ
                    //$("#proAddBoxTbody").html('<tr><td colspan="10" style="text-align: center;color:#666666;padding:20px 0 20px;background:#ffffff;">-- ������ --</td></tr>');
                    selectProBoxData = data.data;
                    appendSelectProTable(data.data,data.num)
                    selectProBoxDlg = art.dialog({title: '��ѡ����Ʒ',width:"680px",content: $("#add-pro-box").html()});
                }else{
                    alert("����Ʒ���ܽ��л��ֶһ�");
                }
            }else{
                alert("����Ʒ���ܽ��л��ֶһ�");
            }
        });
        $("#proBarCode").val("");
    }
}





//ɾ��һ����Ʒ
var proTblDel = function(i){
    if(confirm("ȷ��ȥ������Ʒ��")){
        $("#item_row_"+i).remove();
        calcBillSumInfo();
    }
}
//���������Ʒ
$("#removeGoodsBtn").click(function(){
     if(confirm("ȷ�����������Ʒ��")){
        $(".item_row_tr").remove();
        calcBillSumInfo();
        $("#removeGoodsBtn").hide();
    }
});
