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
#proBarCode{width:300px;}
#proListTbody input{margin-bottom:0px;width:25px;}
#proListTbody .btn-small{padding:0 0 0 3px;}
#searchMemBtn,#searchProBtn,#setDiscBtn{padding:3px 4px;margin-bottom:10px;}
#billContent .memextinfo{display:none}
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
                                <dd><input type="text" value="13512026125" id="memberPhone"><span class="btn btn-mini" title="��ѯ�û�" id="searchMemBtn"><i class="halflings-icon search white"></i></span>
                                </dd>
                            </dl>
                        </div>
                        <div class="box-r">
                            <table width="100%" id="memberInfoTbl">
                                <tr>
                                    <td class="mtbl_l">��Ա����</td><td class="mtbl_r"><span class="label label-success" id="memtbl_name"></span></td>
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
    
            <form method="POST" action="?" onsubmit="return doCheckIpt()">
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
                                <dt>�ܽ��</dt>
                                <dd><input type="text" value="0" id="bill_sum_price" name="bill[bill_sum_price]" readonly="true" trueprice="0" /></dd>
                            </dl>
                            <dl class="clearfix" style="display:none">
                                <dt>�ۿ�</dt>
                                <dd><input type="text" value="1" id="bill_disc" class="billIptChg" name="bill[bill_disc]"/></dd>
                            </dl>
                            <dl class="clearfix"  style="display:none">
                                <dt>�ۿۺ���</dt>
                                <dd><input type="text" value="0.00" id="bill_aftdisc_price" name="bill[bill_aftdisc_price]"  readonly="true" /></dd>
                            </dl>
                            <dl class="clearfix memextinfo">
                                <dt>���ڿۿ�</dt>
                                <dd><input type="text" value="0" id="bill_member_card" class="billIptChg" readonly="true" name="bill[bill_member_card]"/></dd>
                            </dl>
                            <dl class="clearfix memextinfo">
                                <dt>ʹ�û���</dt>
                                <dd><input type="text" value="0" id="bill_member_score" class="billIptChg" readonly="true" name="bill[bill_member_score]">
                                   <br/><span style="color:#999999;padding-bottom:5px;">����ֻ��ʹ��<?=$scoreRatio?>��������</span>
                                </dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>Ӧ�տ�</dt>
                                <dd><input type="text" value="0.00" id="bill_end_sum" name="bill[bill_end_sum]" style="font-weight:bold;color:#EB3C00"></dd>
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
                                <dl class="clearfix">
                                    <dt>��Ա</dt>
                                    <dd><input type="text" value="" id="bill_end_membernm"  disabled="true"></dd>
                                </dl>
                                
                                <dl class="clearfix memextinfo">
                                    <dt>�������</dt>
                                    <dd><input type="text" value="" id="bill_card_left"  disabled="true"></dd>
                                </dl>
                                
                                <dl class="clearfix memextinfo">
                                    <dt>���ջ���</dt>
                                    <dd><input type="text" value="" id="bill_score_left"  disabled="true"></dd>
                                </dl>
                            </dl>
                            <div style="text-align:center;">
                                <input type="submit" value="ȷ���տ�" class="btn btn-primary" id="addbtn"/>
                                <input type="hidden" value="Checkout" name="c"/>
                                <input type="hidden" value="Done" name="a"/>
                                <input type="hidden" value="0" name="memberId" id="memberId"/>
                            </div>
                        </div>
                        <div class="box-r" style="width:700px;">
                            <div id="barScanDiv"><span style="font-weight:bold;padding-right:10px;">����</span> <input type="text" value="12345678890233232" id="proBarCode"><span class="btn btn-mini" title="��ѯ��Ʒ" id="searchProBtn"><i class="halflings-icon search white"></i></span>
                            <span class="btn btn-mini btn-info" title="���������ۿ�" id="setDiscBtn"><i class="halflings-icon th-list white"></i>���������ۿ�</span>
                            </div>
                            <div>
                                <table class="table table-striped table-bordered" id="proListTable">
                                    <thead><tr role="row"><th>��Ʒ����</th><th  style="width:30px;">���</th><th>����</th><th style="width:99px;">����</th><th  style="width:30px;">�ۿ�</th><th>�۸�</th><th style="width:60px;">����Ա</th><th style="width:60px;">����</th></tr> </thead>   
						  
                                    <tbody id="proListTbody">
                                        <tr><td colspan="10" style="text-align: center;color:#666666;padding:100px 0 200px;background:#ffffff;">-- ��ɨ�������������Ʒ --</td></tr>
                                    </tbody></table>
                            </div>
                        </div>
            </div>
            </form>
</div>

<div id="add-pro-box" style="display: none;">
    <table class="table table-striped table-bordered" style="width:580px;margin:10px 0;font-size:12px;">
    <thead><tr role="row"><th>��Ʒ����</th><th  style="width:30px;">���</th><th>����</th><th>����</th></tr> </thead>
    <tbody id="proAddBoxTbody">
        <tr><td colspan="10" style="text-align: center;color:#666666;padding:20px 0 20px;background:#ffffff;">-- ������ --</td></tr>
    </tbody></table>
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
        <td>${pro.stock}</td>
        <td id="item_sprice_${rowIdx}">${pro.price}</td>
        <td align="center"><span class="btn btn-small btn-info"  onclick="proTblDelNum(${rowIdx})"><i class="halflings-icon minus white"></i></span>
        <input type='text' value='1' id='item_num_${rowIdx}' name='item_num[${rowIdx}]' class='tblProNum'>
        <span class="btn btn-small btn-info" onclick="proTblAddNum(${rowIdx})"><i class="halflings-icon plus white "></i></span></td>
        <td><input type='text' value='1' id='item_disc_${rowIdx}' name='item_disc[${rowIdx}]' onblur="proTblCalPrice(${rowIdx})" class='tblProDisc' data-idx="${rowIdx}"></td>
        <td id="item_price_${rowIdx}" >${pro.price}</td>  
        <td id="item_stafftd_${rowIdx}"><span style="color:#999999">ͬ���</span></td>
        <td><button class="btn btn-small btn-info" onclick="proTblDel(${rowIdx})"><i class="halflings-icon remove white "></i></button>
            <span class="btn btn-small" onclick="proTblSetStaff(${rowIdx})" title="���ø���Ʒ������Ա"><i class="halflings-icon user white"></i></span>
            <input type="hidden" value="${pro.id}" name="item_id[${rowIdx}]"/>
            <input type="hidden" value="0" name="item_staffid[${rowIdx}]" id="item_staff_${rowIdx}"/>  
            <input type="hidden" value="${pro.oprice}" id="item_org_sprice_${rowIdx}"/>
            <input type="hidden" value="${pro.oprice}" id="item_calc_sprice_${rowIdx}" class="tblProPrice"/>
        </td>            
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
    $("#proBarCode").focus(); 
    //����ת���۸�
    var scoreToMoney = function(score){
        var rule = <?=$scoreRatio?>; //300�� = 10Ԫ
        var price = Math.floor(score/rule);
        var leftScore = score - price * rule;
        return {
          price :   price,
          score :   leftScore,
          canUseScore :  price * rule ,
        };
    }
    //Ǯ������
    var moneyToScore = function(price){
        var rule = <?=$scoreRatio?>; //300�� = 10Ԫ
        return price * rule;
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
        var sprice = parseInt($("#item_org_sprice_"+i).val(),10);
        var num    = parseInt($("#item_num_"+i).val(),10);
        var disc   = parseFloat($("#item_disc_"+i).val());
        if(isNaN(disc) || disc > 1) disc =1;
        var price  = sprice * num * disc;
        $("#item_calc_sprice_"+i).val(price);//�����Է�Ϊ��λ�ļ۸�
        price = price / 100;
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
            sum += $(this).val() - 0;
        });
        $("#bill_sum_price").attr("trueprice",sum);
        $("#bill_sum_price").val((sum/100).toFixed(2));
    }
    //�������ն������ܽ������
    var calcBillSumInfo = function(){
        calcBillPrice();//�����ܼ�
        
        var memberId = $("#memberId").val();
        if(memberId){
            //�����û���Ҫ���Ѷ��ٿ��Ͻ��
            if(($("#bill_member_card").val()-0) > ($("#bill_sum_price").val()-0)){
                $("#bill_member_card").val($("#bill_sum_price").val());
            }
        }
        
        var billSumPrice         = parseInt($("#bill_sum_price").attr("trueprice"),10);//parseFloat($("#bill_sum_price").val());//Ӧ�ս��
        var billDisc             = parseFloat($("#bill_disc").val());//�ۿ�
        var billMemCard          = parseFloat($("#bill_member_card").val());//���Ͻ��
        var billMemScore         = parseInt($("#bill_member_score").val(),10);//ʹ�û���
        if(billMemScore){
            var scoreToMoneyObj      = scoreToMoney(billMemScore);
            billMemScore = scoreToMoneyObj.price;
         }
         //����û��������㹻�Ļ���
         
        
        var endPrice = billSumPrice * billDisc;//�ۿۺ�ļ۸�
        $("#bill_aftdisc_price").val((endPrice/100).toFixed(2));
        if(billMemCard){//����û����ﻹ�����
            if(billMemCard * 100 > endPrice){//���ڻ���ǰ
                  billMemCard = (billMemCard * 100 - endPrice) / 100;
                  endPrice = 0;  
            }else{
                endPrice = endPrice - billMemCard * 100;
                billMemCard = 0;
            }
        }
        if(billMemScore){//ʹ���û��Ļ���
            if(billMemScore * 100 > endPrice){
                billMemScore = (billMemScore * 100 - endPrice) / 100;
                endPrice = 0;  
            }else{
                endPrice = endPrice - billMemScore * 100;
                billMemScore = 0;                
            }
            
        }
        //��Ǯ�û���
        var newScore = 0;
        newScore = moneyToScore($("#bill_sum_price").val()) - $("#bill_member_score").val();
        
        $("#bill_card_left").val($("#memtbl_card").html() - $("#bill_member_card").val() + billMemCard);
        $("#bill_score_left").val($("#memtbl_score").html() - $("#bill_member_score").val() +newScore);
        $("#bill_end_sum").val((endPrice/100).toFixed(2));
    }
    //�˵��е��������������ı䣬�����¼���
    $(".billIptChg").blur(function(){
        //���ڽ��
        if($(this).attr("id") == "bill_member_card"){ //�ж����õĽ��ܹ���
            if($(this).val()-0 > $("#memtbl_card").html()-0){
                $(this).val($("#memtbl_card").html());
            }
            if(!$(this).val()||$(this).val() == "")$(this).val(0);
        }
        //����
        if($(this).attr("id") == "bill_member_score"){ //�ж����õĽ��ܹ���
            if($(this).val() -0 > $("#memtbl_score").html() -0){
                $(this).val($("#memtbl_score").html());
            }
            //������ת�����Ա���������������
            var o = scoreToMoney($(this).val());
            if(o){
                $(this).val(o.canUseScore);
            }
            
            if(!$(this).val())$(this).val(0);
        }
            
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
    
    //���������ۿ�
    $("#setDiscBtn").click(function(){
        art.dialog({
            title : '����������Ʒ�ۿ�',
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
//    var enterIn = function(evt){
//        var evt=evt?evt:(window.event?window.event:null);//����IE��FF
//        if (evt.keyCode==13){
//            doSearchMember();
//        }
//    }
    //������Ա
    iptEnter("#memberPhone",function(){
        doSearchMember();
    });
    $("#searchMemBtn").click(function(){
        doSearchMember();
    });
    //������
    var doSearchMember = function(){
        var phone = $("#memberPhone").val();
        if(phone){
            var url = "?c=Ajax_Member&a=GetMemberByPhone&phone=" + phone;
            $.getJSON(url,{},function(data){
                if(data){
                    var bls = data.balance ? data.balance : 0;
                    $("#memtbl_name").html(data.name);
                    $("#bill_end_membernm").val(data.name);
                    $("#memtbl_score").html(data.score);
                    $("#memtbl_card").html(bls);
                    $("#memtbl_cate").html(data.cateName);
                    $("#bill_member_card").val(bls);
                    
                    $("#memberId").val(data.id);
                    $("#bill_member_card").removeAttr("readonly");
                    $("#bill_member_score").removeAttr("readonly");
                    
                    $("#bill_card_left").val(bls);
                    $("#bill_score_left").val(data.score);
                    $(".memextinfo").show();
                }
            });
        }else{
            $("#memtbl_name").html("");
            $("#bill_end_membernm").val("");
            $("#memtbl_score").html("");
            $("#memtbl_card").html("");
            $("#memtbl_cate").html("");
            $("#memberId").val(0);
            $("#bill_member_card").attr("readonly","true");
            $("#bill_member_score").attr("readonly","true");
            $("#bill_card_left").val(0);
            $("#bill_score_left").val(0);
            $(".memextinfo").hide();
        }
    }
    //������
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
                        alert("����Ʒδ���");
                    }
                }else{
                    alert("����Ʒδ���");
                }
            });
        }
    }
    
</script>
</body>
</html>