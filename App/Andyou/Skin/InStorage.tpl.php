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
						<h2><i class="halflings-icon list-alt"></i><span class="break"></span>��Ʒ���</h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
						</div>
					</div>
                    <div class="box-content clearfix">
                        <div style="padding:20px  40px;">
                        ���룺 <input type="text" value="" id="proBarCode"><span class="btn btn-mini btn-primary" title="��ѯ��Ʒ" id="searchProBtn"><i class="halflings-icon search white"></i>�鿴</span>
                        </div>
                    </div>
                    
                </div>
            </div>
            
    
    
    
            <form method="POST" action="?" onsubmit="return doCheckIpt()">
			<div class="row-fluid">
                
                <div class="box">
					<div class="box-header">
						<h2><i class="halflings-icon list-alt"></i><span class="break"></span>��Ʒ��Ϣ</h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
						</div>
					</div>
                    <div class="box-content clearfix" id="billContent">
                        <table class="table table-striped table-bordered" id="proListTable">
                            <thead><tr role="row"><th>��Ʒ����</th><th style="width:99px;">�������</th><th  style="width:60px;">��ǰ���</th><th>����</th></tr> </thead>   

                            <tbody id="proListTbody">
                                <tr><td colspan="10" style="text-align: center;color:#666666;padding:100px 0 200px;background:#ffffff;">-- ��ɨ�������Ի����Ʒ��Ϣ --</td></tr>
                            </tbody>
                            
                            <tbody id="doInStoreRow" style="display:none">
                                <tr><td colspan="10" style="text-align: center;background:#ffffff;padding: 20px 0">
                                        <input type="submit" value=" ȷ����� " class="btn btn-primary"/>
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
    <thead><tr role="row"><th>��Ʒ����</th><th  style="width:30px;">���</th><th>����</th><th>����</th></tr> </thead>
    <tbody id="proAddBoxTbody">
        <tr><td colspan="10" style="text-align: center;color:#666666;padding:20px 0 20px;background:#ffffff;">-- ������ --</td></tr>
    </tbody> </table>
</div>
<div id="set-staff-box" style="display: none;">
    <select onchange="chgSetStaff(this)"><option value='0'>��ѡ��</option><option value='0'>ͬ���</option>
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
<!--  ���Ʒ��ѡ  -->
<script id="proSelectTableTr" type="text/template">
     {@each list as pro,index}
            <tr>
                <td>${pro.name}</td><td>${pro.stock}</td><td>${pro.price}</td>
                <td style="width:80px;"><span class="btn btn-small btn-info" onclick="boxSelectPro(${pro.id})"><i class="halflings-icon ok white "></i>ѡ��</span>                      
                </td> 
            </tr>
     {@/each}

    
</script>
<script>
   
   
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
        var url = "?c=Ajax_Product&a=GetProductByCode&code=" + barcode;
        $.getJSON(url,{},function(data){
            if(data){

                if(data.num == 1){
                    var proInfo = data.data[0];
                    appendProTable(proInfo);
                }else if(data.num > 1){//���һ�������ж����Ʒ
                    //$("#proAddBoxTbody").html('<tr><td colspan="10" style="text-align: center;color:#666666;padding:20px 0 20px;background:#ffffff;">-- ������ --</td></tr>');
                    selectProBoxData = data.data;
                    appendSelectProTable(data.data,data.num)
                    selectProBoxDlg = art.dialog({title: '��ѡ����Ʒ',width:"600px",content: $("#add-pro-box").html()});
                }else{
                    //alert("����Ʒδ���");
                }
            }else{
                //alert("����Ʒδ���");
            }
        });
    }
}

//׷�ӵ���Ʒ�б�
var proTableTr = document.getElementById('proTableTr').innerHTML;
var proJuicer  = juicer(proTableTr);
var proTrIdx   = 0; //��¼�����˵ڼ�����
var appendProTable = function(proInfo){
    var data = {pro:proInfo,rowIdx:proTrIdx};
    var html = proJuicer.render(data);
    if(proTrIdx == 0)$("#proListTbody").empty();
    $("#proListTbody").append(html);
    proTrIdx++;
    
    $("#doInStoreRow").show();
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
}  
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   

    //�����ύ��֤
    var doCheckIpt = function(){
        
        //�Ƿ�ѡ����Ʒ����֤
        var rightRows = $(".item_row_tr").size();
        if(rightRows < 1){
            alert("���������Ʒ");
            return false;
        }
        
        //��֤Ӧ�ռ۸�
        var ysPrice = $("#bill_sum_price").val();
        if(ysPrice == "" || ysPrice == "0" || ysPrice == "0.00"){
            alert("���������Ʒ");
            return false;
        }
        
        //��֤����Ա�Ƿ��Ѿ�ѡ��
        var staffid = parseInt($("#staffid").val(),10);
        if(staffid == 0){
            alert("��ѡ������Ա");
            return false;
        }
        
        
        return true;
    }
    
    
    
    
</script>
</body>
</html>