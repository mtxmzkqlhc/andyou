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
#barScanDiv{pdding:10px 0 15px 0;margin-bottom:10px;}
#proBarCode{width:300px;}
#proListTbody input{margin-bottom:0px;width:20px;}
#proListTbody .btn-small{padding:0 0 0 3px;}
</style>
<div id="content">

			<div class="row-fluid">
                
                <div class="box">
					<div class="box-header">
						<h2><i class="halflings-icon list-alt"></i><span class="break"></span>��Ա��Ϣ</h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
						</div>
					</div>
                    <div class="box-content clearfix">
                        <div class="box-l clearfix">
                            <dl>
                                <dt>��Ա�绰</dt>
                                <dd><input type="text" value="13512026125" id="memberPhone"></dd>
                            </dl>
                        </div>
                        <div class="box-r">
                            <table width="100%" id="memberInfoTbl">
                                <tr>
                                    <td class="mtbl_l">��Ա����</td><td class="mtbl_r" id="memtbl_name"></td>
                                    <td class="mtbl_l">��Ա����</td><td class="mtbl_r" id="memtbl_cate"></td>
                                    <td class="mtbl_l">�����ۿ�</td><td class="mtbl_r" id="memtbl_disc"</td>
                                </tr>
                                <tr>
                                    <td class="mtbl_l">��ǰ����</td><td class="mtbl_r" id="memtbl_score"></td>
                                    <td class="mtbl_l">�������</td><td class="mtbl_r" id="memtbl_card"></td>
                                    <td class="mtbl_l">&nbsp;</td><td class="mtbl_r" >&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="mtbl_l">��ע</td><td class="mtbl_r" id="memtbl_info" colspan="8"> </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
    
    
    
            <!--   �˵�����     -->
    
            <form method="POST" action="?">
			<div class="row-fluid">
                
                <div class="box">
					<div class="box-header">
						<h2><i class="halflings-icon list-alt"></i><span class="break"></span>�˵���Ϣ</h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
						</div>
					</div>
                    <div class="box-content clearfix" id="billContent">
                        <div class="box-l clearfix">
                            <dl class="clearfix">
                                <dt>����</dt>
                                <dd><input type="text" value="<?=Helper_Bill::getMaxBno()?>" id="memberPhone" disabled="true" ></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>Ӧ�ս��</dt>
                                <dd><input type="text" value="0" id="bill_sum_price" name="bill[bill_sum_price]"></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>�ۿ�</dt>
                                <dd><input type="text" value="1" id="bill_disc" class="billIptChg" name="bill[bill_disc]"/></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>����Ӧ��</dt>
                                <dd><input type="text" value="0.00" id="bill_aftdisc_price" name="bill[bill_aftdisc_price]"/></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>���ڿۿ�</dt>
                                <dd><input type="text" value="0" id="bill_member_card" class="billIptChg" readonly="true" name="bill[bill_member_card]"/></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>ʹ�û���</dt>
                                <dd><input type="text" value="0" id="bill_member_score" class="billIptChg" readonly="true" name="bill[bill_member_score]"></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>�������տ�</dt>
                                <dd><input type="text" value="0.00" id="bill_end_sum" name="bill[bill_end_sum]"></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>����Ա</dt>
                                <dd>
                                    <select name="staffid" id="staffid" name="bill[staffid]"><option value='0'>��ѡ��</option>
                                       <?php
                                       if ($staffArr) {
                                              foreach ($staffArr as $k=>$v) {
                                                  echo '<option value="' . $k . '">' . $v . '</option>' . "\n";
                                              } 
                                          }
                                        ?>
                                  </select>  
                                </dd>
                            </dl>
                            <div style="text-align:center;">
                                <input type="submit" value="ȷ���տ�" class="btn btn-primary" id="addbtn"/>
                                <input type="hidden" value="Checkout" name="c"/>
                                <input type="hidden" value="Done" name="a"/>
                                <input type="hidden" value="0" name="memberId" id="memberId"/>
                            </div>
                        </div>
                        <div class="box-r" style="width:700px;">
                            <div id="barScanDiv"><span style="font-weight:bold;padding-right:10px;">����</span> <input type="text" value="12345678890233232" id="proBarCode"></div>
                            <div>
                                <table class="table table-striped table-bordered" id="proListTable">
                                    <thead><tr role="row"><th>��Ʒ����</th><th  style="width:30px;">���</th><th>����</th><th style="width:95px;">����</th><th  style="width:30px;">�ۿ�</th><th>�۸�</th><th style="width:60px;">����Ա</th><th style="width:60px;">����</th></tr> </thead>   
						  
                                    <tbody id="proListTbody">
                                        <tr><td colspan="10" style="text-align: center;color:#666666;padding:100px 0 200px;background:#ffffff;">-- ��ɨ�������������Ʒ --</td></tr>
                                    </tbody></table>
                            </div>
                        </div>
            </div>
            </form>
</div>

<div id="add-pro-box" style="display: none;">
    <table class="table table-striped table-bordered" styple="width:100%">
    <thead><tr role="row"><th>��Ʒ����</th><th  style="width:30px;">���</th><th>����</th><th>����</th></tr> </thead>
    <tbody id="proAddBoxTbody"></tbody></table>
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
    <tr id="item_row_${rowIdx}">
        <td>${pro.name}</td>
        <td>${pro.stock}</td>
        <td id="item_sprice_${rowIdx}">${pro.price}</td>
        <td align="center"><span class="btn btn-small btn-info"  onclick="proTblDelNum(${rowIdx})"><i class="halflings-icon minus white"></i></span>
        <input type='text' value='1' id='item_num_${rowIdx}' name='item_num[${rowIdx}]' class='tblProNum'>
        <span class="btn btn-small btn-info" onclick="proTblAddNum(${rowIdx})"><i class="halflings-icon plus white "></i></span></td>
        <td><input type='text' value='1' id='item_disc_${rowIdx}' name='item_disc[${rowIdx}]' onblur="proTblCalPrice(${rowIdx})" class='tblProDisc'></td>
        <td id="item_price_${rowIdx}" class="tblProPrice">${pro.price}</td>  
        <td id="item_stafftd_${rowIdx}"><span style="color:#999999">ͬ���</span></td>
        <td><button class="btn btn-small btn-info" onclick="proTblDel(${rowIdx})"><i class="halflings-icon remove white "></i></button>
            <span class="btn btn-small" onclick="proTblSetStaff(${rowIdx})" title="���ø���Ʒ������Ա"><i class="halflings-icon user white"></i></span>
            <input type="hidden" value="${pro.id}" name="item_id[${rowIdx}]"/>
            <input type="hidden" value="0" name="item_staffid[${rowIdx}]" id="item_staff_${rowIdx}"/>        
        </td>            
    </tr>
</script>
<script>
    $("#proBarCode").focus(); 
    
    //��Ʒ���Ĳ���
    //����һ����Ʒ��ӪҵԱ
    var setStaffIdx = 0;
    var setStaffDlg = null;
    var proTblSetStaff = function(i){
        setStaffIdx = i;
        setStaffDlg = art.dialog({title: '��ѡ������Ա',width:"300px",height:"100px",content: $("#set-staff-box").html()});
    }
    var chgSetStaff = function(o){
        var i = o.value;
        var name = o.options[o.options.selectedIndex].innerHTML;
        if(i==0){
            $("#item_stafftd_"+setStaffIdx).html('<span style="color:#999999">ͬ���</span>');            
            $("#item_staff_"+setStaffIdx).val(0);
        }else{
            $("#item_stafftd_"+setStaffIdx).html(name);
            $("#item_staff_"+setStaffIdx).val(i);
        }
        if(setStaffDlg != null)setStaffDlg.close();
    }
    //ɾ��һ����Ʒ
    var proTblDel = function(i){
        $("#item_row_"+i).remove();
        calcBillSumInfo();
    }
    var proTblNumChg = function(i,t){
        
        var num = parseInt($("#item_num_"+i).val(),10);
        if(t == 1){//��            
            if(num>100)return false;
            num++;
        }else{
            if(num<2)return false;
            num--;
        }
        $("#item_num_"+i).val(num);
        //���¼���۸�
        proTblCalPrice(i);
    }
    //����һ�е��ܼ�
    var proTblCalPrice = function(i){
        var sprice = parseFloat($("#item_sprice_"+i).html());
        var num    = parseInt($("#item_num_"+i).val(),10);
        var disc   = parseFloat($("#item_disc_"+i).val());
        if(isNaN(disc) || disc > 1) disc =1;
        var price  = sprice * num * disc;
        price = price.toFixed(2);
        $("#item_price_"+i).html(price);
        calcBillSumInfo();
    }
    //���ӹ�������
    var proTblAddNum = function(i){
        proTblNumChg(i,1);
        return false;
    }
    //���ٹ�������
    var proTblDelNum = function(i){
        proTblNumChg(i,0);
        return false;
    };
    //�������ն����ļ۸�
    var calcBillPrice = function(){
        var sum = 0;
        $(".tblProPrice").each(function(){
            sum += $(this).html() - 0;
        });
        $("#bill_sum_price").val(sum);
    }
    //�������ն������ܽ������
    var calcBillSumInfo = function(){
        calcBillPrice();//�����ܼ�
        var billSumPrice         = parseFloat($("#bill_sum_price").val());//Ӧ�ս��
        var billDisc             = parseFloat($("#bill_disc").val());//�ۿ�
        var billMemCard          = parseFloat($("#bill_member_card").val());//���Ͻ��
        var billMemScore         = parseInt($("#bill_member_score").val(),10);//ʹ�û���
        if(billMemScore){
            var scoreToMoneyObj      = scoreToMoney(billMemScore);
            billMemScore = scoreToMoneyObj.price;
         }
        
        var endPrice = billSumPrice * billDisc;//�ۿۺ�ļ۸�
        $("#bill_aftdisc_price").val(endPrice.toFixed(2));
        if(billMemCard){//����û����ﻹ�����
            if(billMemCard > endPrice){//���ڻ���ǰ
                  billMemCard = billMemCard - endPrice;
                  endPrice = 0;  
            }else{
                endPrice = endPrice - billMemCard;
                billMemCard = 0;
            }
        }
        if(billMemScore){//ʹ���û��Ļ���
            if(billMemScore > endPrice){
                billMemScore = billMemScore - endPrice;
                endPrice = 0;  
            }else{
                endPrice = endPrice - billMemScore;
                billMemScore = 0;                
            }
            
        }
        //$("#bill_member_card").val()
        $("#bill_end_sum").val(endPrice.toFixed(2));
    }
    //�˵��е��������������ı䣬�����¼���
    $(".billIptChg").blur(function(){
        calcBillSumInfo();
    });
    
    //׷�ӵ���Ʒ�б�
    var proTableTr = document.getElementById('proTableTr').innerHTML;
    var proJuicer  = juicer(proTableTr);
    var proTrIdx   = 0; //��¼�����˵ڼ�����
    var appendProTable = function(proInfo){
        var data = {pro:proInfo,rowIdx:proTrIdx};
        var html = proJuicer.render(data);
        if(proTrIdx == 0)$("#proListTbody").empty();
        $("#proListTbody").append(html);
        proTblCalPrice(proTrIdx);//���㵥�м۸�
        calcBillSumInfo();//�������Ķ����۸�
        proTrIdx++;
    };
    
    
//    var enterIn = function(evt){
//        var evt=evt?evt:(window.event?window.event:null);//����IE��FF
//        if (evt.keyCode==13){
//            doSearchMember();
//        }
//    }
    //������Ա
    iptEnter("#memberPhone",function(){
        doSearchMember();
        $("#bill_member_card").removeAttr("readonly");
        $("#bill_member_score").removeAttr("readonly");
    });
    //������
    var doSearchMember = function(){
        var phone = $("#memberPhone").val();
        if(phone){
            var url = "?c=Ajax_Member&a=GetMemberByPhone&phone=" + phone;
            $.getJSON(url,{},function(data){
                if(data){
                    $("#memtbl_name").html(data.name);
                    $("#memtbl_score").html(data.score);
                    $("#memtbl_card").html(data.card ? data.card : 0);
                    $("#memtbl_cate").html(data.cateName);
                    
                    $("#memberId").val(data.id);
                }
            });
        }
    }
    //������
    iptEnter("#proBarCode",function(){
        var barcode = $("#proBarCode").val();
        if(barcode){//doGetProductByCode
            var url = "?c=Ajax_Product&a=GetProductByCode&code=" + barcode;
            $.getJSON(url,{},function(data){
                if(data){
                    art.dialog({title: '��ѡ����Ʒ',width:"600px",content: $("#add-pro-box").html()});
                    if(data.num == 1){
                        var proInfo = data.data[0];
                        appendProTable(proInfo);
                    }else if(data.num > 1){
                        
                    }else{
                        alert("����Ʒδ���");
                    }
                }else{
                    alert("����Ʒδ���");
                }
            });
            
        }
    });
    
</script>
</body>
</html>