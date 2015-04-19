<?= $header ?>
<?= $navi ?>
<style>
.clearfix:after {content:"."; display: block; visibility: hidden; clear: both; height:0; font-size:0}
.clearfix {*zoom:1}
.box{margin-bottom: 10px;}
.box-l{float:left;width:290px;}
.box-r{float:left;margin-left:10px;border-left:1px solid #ccc;padding-left:35px;}
.box-l dl{display: block;}
.box-l dt{float:left;width:70px;text-align: right;padding-right: 15px;line-height: 20px;}
.box-l dd{float:left}
.box-l dd input{width:140px;}
.box-l dd select{width:148px;}
.mtbl_l{width:80px;font-weight: bold;color:#999;}
.mtbl_r{text-align:left;padding-left:8px;min-width: 40px;}
#memberInfoTbl .mtbl_r{width:120px;}
#billContent dl{margin-bottom:3px;margin-top: 5px;}
.tblProNum{width:20px;margin-bottom:0px;}
#barScanDiv{padding:10px 0 15px 0;margin-bottom:10px;}
#proBarCode{width:400px;}
#proListTbody input{margin-bottom:0px;width:25px;}
#proListTbody .btn-small{padding:0 0 0 3px;}
#searchMemBtn,#searchProBtn,#setDiscBtn,#removeGoodsBtn,#removeMemInfo{padding:3px 4px;margin-bottom:10px;}
#billContent .memextinfo{display:none}
</style>
<div id="content">

			<div class="row-fluid">
                
                <div class="box">
					<div class="box-header">
						<h2><i class="halflings-icon list-alt"></i><span class="break"></span>商品入库</h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
						</div>
					</div>
                    <div class="box-content clearfix">
                        <div style="padding:20px  40px;">
                        条码： <input type="text" value="" id="proBarCode"><span class="btn btn-mini btn-primary" title="查询商品" id="searchProBtn"><i class="halflings-icon search white"></i>查看</span>
                        </div>
                    </div>
                    
                </div>
            </div>
            
    
    
    
            <form method="POST" action="?" onsubmit="return doCheckIpt()">
			<div class="row-fluid">
                
                <div class="box">
					<div class="box-header">
						<h2><i class="halflings-icon list-alt"></i><span class="break"></span>商品信息</h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
						</div>
					</div>
                    <div class="box-content clearfix" id="billContent">
                        <table class="table table-striped table-bordered" id="proListTable">
                            <thead><tr role="row"><th>商品名称</th><th style="width:99px;">入库数量</th><th  style="width:60px;">当前库存</th><th>单价</th></tr> </thead>   

                            <tbody id="proListTbody">
                                <tr><td colspan="10" style="text-align: center;color:#666666;padding:100px 0 200px;background:#ffffff;">-- 请扫描条码以获得商品信息 --</td></tr>
                            </tbody>
                            
                            <tbody id="doInStoreRow" style="display:none">
                                <tr><td colspan="10" style="text-align: center;background:#ffffff;padding: 20px 0">
                                        <input type="submit" value=" 确认入库 " class="btn btn-primary"/>
                                    </td></tr>
                            </tbody>
                            
                        </table>
                    </div>
                        
            </div>
             <input type="hidden" value="AddIn" name="a"/>
             <input type="hidden" value="InStorage" name="c"/>
             
            </form>
</div>

<div id="add-pro-box" style="display: none;">
    <table class="table table-striped table-bordered" style="width:580px;margin:10px 0;font-size:12px;">
    <thead><tr role="row"><th>商品名称</th><th  style="width:30px;">库存</th><th>单价</th><th>操作</th></tr> </thead>
    <tbody id="proAddBoxTbody">
        <tr><td colspan="10" style="text-align: center;color:#666666;padding:20px 0 20px;background:#ffffff;">-- 加载中 --</td></tr>
    </tbody> </table>
</div>
<div id="set-staff-box" style="display: none;">
    <select onchange="chgSetStaff(this)"><option value='0'>请选择</option><option value='0'>同左边</option>
            <?php
            if ($staffArr) {
                   foreach ($staffArr as $k=>$v) {
                       echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                   } 
               }
             ?>
       </select> 
</div>

<?= $footer ?>
<script type="text/javascript" src="js/global.js"></script>
<script type="text/javascript" src="js/juicer-min.js"></script>
<script id="proTableTr" type="text/template">
    <tr id="item_row_${rowIdx}" class="item_row_tr">
        <td>${pro.name}</td>
        <td align="center"><span class="btn btn-small btn-info"  onclick="proTblDelNum(${rowIdx})"><i class="halflings-icon minus white"></i></span>
        <input type='text' value='1' id='item_num_${rowIdx}' name='item_num[${rowIdx}]' class='tblProNum' onblur="proTblCalPrice(${rowIdx})">
        <span class="btn btn-small btn-info" onclick="proTblAddNum(${rowIdx})"><i class="halflings-icon plus white "></i></span>
         
        
        <input type="hidden" value="${pro.id}" name ="item_id[${rowIdx}]"/>
        </td>            
        <td>${pro.stock}</td>
        <td id="item_sprice_${rowIdx}">${pro.price}</td>
    </tr>
</script>
<!--  多产品多选  -->
<script id="proSelectTableTr" type="text/template">
     {@each list as pro,index}
            <tr>
                <td>${pro.name}</td><td>${pro.stock}</td><td>${pro.price}</td>
                <td style="width:80px;"><span class="btn btn-small btn-info" onclick="boxSelectPro(${pro.id})"><i class="halflings-icon ok white "></i>选择</span>                      
                </td> 
            </tr>
     {@/each}

    
</script>
<script>
   
   
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
        var url = "?c=Ajax_Product&a=GetProductByCode&code=" + barcode;
        $.getJSON(url,{},function(data){
            if(data){

                if(data.num == 1){
                    var proInfo = data.data[0];
                    appendProTable(proInfo);
                }else if(data.num > 1){//如果一个条码有多个商品
                    //$("#proAddBoxTbody").html('<tr><td colspan="10" style="text-align: center;color:#666666;padding:20px 0 20px;background:#ffffff;">-- 加载中 --</td></tr>');
                    selectProBoxData = data.data;
                    appendSelectProTable(data.data,data.num)
                    selectProBoxDlg = art.dialog({title: '请选择商品',width:"600px",content: $("#add-pro-box").html()});
                }else{
                    //alert("该商品未入库");
                }
            }else{
                //alert("该商品未入库");
            }
        });
    }
}

//追加到产品列表
var proTableTr = document.getElementById('proTableTr').innerHTML;
var proJuicer  = juicer(proTableTr);
var proTrIdx   = 0; //记录插入了第几行了
var appendProTable = function(proInfo){
    var data = {pro:proInfo,rowIdx:proTrIdx};
    var html = proJuicer.render(data);
    if(proTrIdx == 0)$("#proListTbody").empty();
    $("#proListTbody").append(html);
    proTrIdx++;
    
    $("#doInStoreRow").show();
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
}  
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   

    //最后的提交验证
    var doCheckIpt = function(){
        
        //是否选择商品的验证
        var rightRows = $(".item_row_tr").size();
        if(rightRows < 1){
            alert("请先添加商品");
            return false;
        }
        
        //验证应收价格
        var ysPrice = $("#bill_sum_price").val();
        if(ysPrice == "" || ysPrice == "0" || ysPrice == "0.00"){
            alert("请先添加商品");
            return false;
        }
        
        //验证销售员是否已经选择
        var staffid = parseInt($("#staffid").val(),10);
        if(staffid == 0){
            alert("请选择销售员");
            return false;
        }
        
        
        return true;
    }
    
    
    
    
</script>
</body>
</html>