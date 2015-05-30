/* 
 *  右侧表格的操作
 */


//追加到产品列表
var proTableTr = document.getElementById('proTableTr').innerHTML;
var proJuicer  = juicer(proTableTr);
var proTrIdx   = 0; //记录插入了第几行了
var appendProTable = function(proInfo){
    var disc = 1;//memberDisc;
    //获得产品所在分类的折扣
    if(memberDiscArr){
        if(proInfo.cateId in memberDiscArr){
            disc = memberDiscArr[proInfo.cateId];
        }
    }
    if(proInfo.discut && proInfo.discut > disc){
        disc = proInfo.discut;
    }
    //检查该产品是否在表格中已经在存在了，如果已经存在了，只要完成数量+1就可以了
    var hasSamePro = false;
    if(proTrIdx > 0){
        $(".proTblItemIds").each(function(){
            var v = $(this).val();
            if(v == proInfo.id){
                var idx = $(this).attr("data-idx");
                proTblAddNum(idx);
                hasSamePro = true;
                
                proTblCalPrice(idx);//计算单行价格
                return false;
            }
        })
    }
    if(!hasSamePro){
        var data = {pro:proInfo,rowIdx:proTrIdx,memberDisc:disc};
        var html = proJuicer.render(data);
        if(proTrIdx == 0)$("#proListTbody").empty();
        $("#proListTbody").append(html);
        proTblCalPrice(proTrIdx);//计算单行价格
        proTrIdx++;
    }
    calcBillSumInfo();//计算最后的订单价格
    //显示清空商品的按钮
    $("#removeGoodsBtn").show();
};
//多个商品
var proSelectTableTr = document.getElementById('proSelectTableTr').innerHTML;
var proSelectJuicer  = juicer(proSelectTableTr);
var appendSelectProTable = function(list,num){
    var data = {list:list,num:num};
    var html = proSelectJuicer.render(data);
    $("#proAddBoxTbody").empty();
    $("#proAddBoxTbody").append(html);
};

//从选择框中选了一个商品
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

//批量设置折扣
$("#setDiscBtn").click(function(){
    art.dialog({
        title : '设置商品折扣',
        content: '<div style="padding:40px 80px;font-size:12px;">设置折扣： <input type="text" value="1" id="dlgSetDisVal" style="width:50px"></div>',
        button: [{
            name: '设置',
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



//条形码扫描
var selectProBoxDlg = null;
var selectProBoxData = []; //保存已经存储产品数组
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
                }else if(data.num > 1){//如果一个条码有多个商品
                    //$("#proAddBoxTbody").html('<tr><td colspan="10" style="text-align: center;color:#666666;padding:20px 0 20px;background:#ffffff;">-- 加载中 --</td></tr>');
                    selectProBoxData = data.data;
                    appendSelectProTable(data.data,data.num)
                    selectProBoxDlg = art.dialog({title: '请选择商品',width:"680px",content: $("#add-pro-box").html()});
                }else{
                    alert("该商品不能进行积分兑换");
                }
            }else{
                alert("该商品不能进行积分兑换");
            }
        });
        $("#proBarCode").val("");
    }
}





//删除一个商品
var proTblDel = function(i){
    if(confirm("确认去掉该商品吗？")){
        $("#item_row_"+i).remove();
        calcBillSumInfo();
    }
}
//清空所有商品
$("#removeGoodsBtn").click(function(){
     if(confirm("确认清空所有商品吗？")){
        $(".item_row_tr").remove();
        calcBillSumInfo();
        $("#removeGoodsBtn").hide();
    }
});
