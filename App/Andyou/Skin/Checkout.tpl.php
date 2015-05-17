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
.mtbl_l{width:80px;font-weight: bold;}
.mtbl_r{text-align:left;padding-left:8px;min-width: 40px;}
#memberInfoTbl .mtbl_r{width:120px;}
#billContent dl{margin-bottom:3px;margin-top:2px;}
.tblProNum{width:20px;margin-bottom:0px;}
#barScanDiv{padding:10px 0 15px 0;margin-bottom:10px;}
#proBarCode{width:300px;}
#proListTbody input{margin-bottom:0px;width:25px;}
#proListTbody .btn-small{padding:0 0 0 3px;}
#searchMemBtn,#searchProBtn,#setDiscBtn,#removeGoodsBtn,#removeMemInfo{padding:3px 4px;margin-bottom:10px;}
#billContent .memextinfo{display:none}
</style>
<div id="content">

			<div class="row-fluid">
                
                <div class="box">
                    <div class="box-content clearfix"  style="padding:15px 10px 8px;">
                        <div class="box-l clearfix">
                            <dl>
                                <dt>��Ա</dt>
                                <dd><input type="text" value="" id="memberPhone"><span class="btn btn-mini" title="��ѯ�û�" id="searchMemBtn"><i class="halflings-icon search white"></i></span>
                                </dd>
                            </dl><dl>
                                <dt>&nbsp;</dt>
                                <dd style="text-align:right;width: 180px;">
                                <span class="btn btn-mini btn-info" title="���������ѡ�����Ʒ" id="removeMemInfo" style="display:none;"><i class="halflings-icon star-empty white"></i>���</span>
                                </dd>
                            </dl>
                        </div>
                        <div class="box-r">
                            <table width="100%" id="memberInfoTbl">
                                <tr>
                                    <td class="mtbl_l">��Ա����</td><td class="mtbl_r"><span class="label label-success" id="memtbl_name" style="display:none"></span></td>
                                    <td class="mtbl_l">��Ա����</td><td class="mtbl_r" id="memtbl_cate"></td>
                                    <td class="mtbl_l">�����ۿ�</td><td class="mtbl_r" id="memtbl_disc"></td>
                                </tr>
                                <tr>
                                    <td class="mtbl_l">�ۼ�����</td><td class="mtbl_r" id="memtbl_allsum"></td>
                                    <td class="mtbl_l">��ǰ����</td><td class="mtbl_r" id="memtbl_score"></td>
                                    <td class="mtbl_l">�������</td><td class="mtbl_r" id="memtbl_card"></td>
                                </tr>
                                <tr><td class="mtbl_l">��ע</td><td class="mtbl_r" id="memtbl_remark" colspan="8"> </td></tr>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
    
    
    
            <!--   �˵�����     -->
    
            <form method="POST" action="?" onsubmit="return doCheckIpt()" onkeydown="if(event.keyCode==13)return false;" >
			<div class="row-fluid">
                
                <div class="box">
					<div class="box-header">
						<h2>������Ϣ</h2>
					</div>
                    <div class="box-content clearfix" id="billContent">
                        <div class="box-l clearfix">
                            <dl class="clearfix">
                                <dt>����</dt>
                                <dd><input type="text" value="<?=Helper_Bill::getMaxBno()?>" id="memberPhone" disabled="true" ></dd>
                            </dl>
                            <dl class="clearfix memextinfo">
                                <dt>�ܽ��</dt>
                                <dd><input type="text" value="0" id="bill_sum_price" name="bill[bill_sum_price]" readonly="true" trueprice="0" /></dd>
                            </dl>
                            <dl class="clearfix" style="display:none;">
                                <dt>ʹ�û���</dt>
                                <dd><input type="text" value="0" id="bill_member_score" class="billIptChg" readonly="true" name="bill[bill_member_score]">
                                   <span style="color:#999999;padding-bottom:5px;" id="scoreToMoneyNote"></span>
                                </dd>
                            </dl>
                            <dl class="clearfix" style="display:none">
                                <dt>�ۿ�</dt>
                                <dd><input type="text" value="1" id="bill_disc" class="billIptChg" name="bill[bill_disc]" /></dd>
                            </dl>
                            <dl class="clearfix"  style="display:none">
                                <dt>�ۿۺ�Ӧ��</dt>
                                <dd><input type="text" value="0.00" id="bill_aftdisc_price" name="bill[bill_aftdisc_price]"  readonly="true" /></dd>
                            </dl>
                            <dl class="clearfix memextinfo">
                                <dt>���ڿۿ�</dt>
                                <dd><input type="text" value="0" id="bill_member_card" class="billIptChg" readonly="true" name="bill[bill_member_card]" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/></dd>
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
                                <dl class="clearfix" style="display:none;">
                                    <dt>��Ա</dt>
                                    <dd><input type="text" value="" id="bill_end_membernm"  disabled="true"></dd>
                                </dl>
                                
                                <dl class="clearfix" style="display:none;">
                                    <dt>�������</dt>
                                    <dd><input type="text" value="" id="bill_card_left"  disabled="true"></dd>
                                </dl>
                                
                                <dl class="clearfix" style="display:none;">
                                    <dt>���ջ���</dt>
                                    <dd><input type="text" value="" id="bill_score_left"  disabled="true"></dd>
                                </dl>
                                
                                <dl class="clearfix">
                                    <dt>��ע˵��</dt>
                                    <dd><textarea style="width:140px;height:50px;" name="remark"></textarea></dd>
                                </dl>
                            </dl>
                            <div style="text-align:center;">
                                <input type="submit" value="ȷ���տ�" class="btn btn-primary" id="addbtn"/>
                                <input type="hidden" value="Checkout" name="c"/>
                                <input type="hidden" value="Done" name="a"/>
                                <input type="hidden" value="0" name="memberId" id="memberId"/>
                                <input type="hidden" value="0" name="endSumModifyFlag" id="endSumModifyFlag"/>
                            </div>
                        </div>
                        <div class="box-r" style="width:700px;">
                            <div id="barScanDiv"><span style="font-weight:bold;padding-right:10px;display: inline;">����</span> <input type="text" value="" id="proBarCode"><span class="btn btn-mini" title="��ѯ��Ʒ" id="searchProBtn"><i class="halflings-icon search white"></i></span>
                                <span class="btn btn-mini btn-info" title="�����ۿ�" id="setDiscBtn" style="display:none;"><i class="halflings-icon th-list white"></i>�����ۿ�</span>
                                <span class="btn btn-mini btn-info" title="���������ѡ�����Ʒ" id="removeGoodsBtn" style="display:none;"><i class="halflings-icon th-list white"></i>�����Ʒ</span>
                            </div>
                            <div>
                                <table class="table table-striped table-bordered" id="proListTable">
                                    <thead><tr role="row"><th>��Ʒ����</th><th  style="width:30px;">���</th><th>����</th><th style="width:99px;">����</th><th  style="width:30px;">�ۿ�</th><th>�۸�</th><th style="width:60px;">����Ա</th><th style="width:60px;">����</th></tr> </thead>   
						  
                                    <tbody id="proListTbody">
                                        <tr><td colspan="10" style="text-align: center;color:#666666;padding:100px 0 100px;background:#ffffff;">-- ��ɨ�������������Ʒ --</td></tr>
                                    </tbody>
                                
                                    <tbody id="proTblNumRow">
                                        <tr><td colspan="10" style="text-align: right;color:#666666;padding:10px 20px 10px;background:#ffffff;">��Ʒ������<span id="proTblNumRow_num">0</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
            </div>
            </form>
</div>

<div id="add-pro-box" style="display: none;">
    <table class="table table-striped table-bordered" style="width:660px;margin:10px 0;font-size:12px;">
    <thead><tr role="row"><th>��Ʒ����</th><th>����</th><th  style="width:30px;">���</th><th>����</th><th>����ۿ�</th><th>����</th></tr> </thead>
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
        <td>${pro.stock}</td>
        <td id="item_sprice_${rowIdx}">${pro.price}</td>
        <td align="center"><span class="btn btn-small btn-info"  onclick="proTblDelNum(${rowIdx})"><i class="halflings-icon minus white"></i></span>
        <input type='text' value='1' id='item_num_${rowIdx}' name='item_num[${rowIdx}]' class='tblProNum' onblur="proTblCalPrice(${rowIdx})">
        <span class="btn btn-small btn-info" onclick="proTblAddNum(${rowIdx})"><i class="halflings-icon plus white "></i></span></td>
        <td><input type='text' value='${memberDisc}' data-rel='${pro.discut}' id='item_disc_${rowIdx}' name='item_disc[${rowIdx}]' onblur="proTblCalPrice(${rowIdx})" class='tblProDisc' data-idx="${rowIdx}" readonly="true"></td>
        <td id="item_price_${rowIdx}" >${pro.price}</td>  
        <td id="item_stafftd_${rowIdx}"><span style="color:#999999">ͬ���</span></td>
        <td><button class="btn btn-small btn-info" onclick="proTblDel(${rowIdx})"><i class="halflings-icon remove white "></i></button>
            <span class="btn btn-small" onclick="proTblSetStaff(${rowIdx})" title="���ø���Ʒ������Ա"><i class="halflings-icon user white"></i></span>
            <input type="hidden" value="${pro.id}" name="item_id[${rowIdx}]" class="proTblItemIds" data-idx="${rowIdx}"/>
            <input type="hidden" value="0" name="item_staffid[${rowIdx}]" id="item_staff_${rowIdx}"/>  
            <input type="hidden" value="${pro.oprice}" id="item_org_sprice_${rowIdx}" class="tblProOrgPrice" data-idx="${rowIdx}"/>
            <input type="hidden" value="${pro.oprice}" id="item_calc_sprice_${rowIdx}" class="tblProPrice"/>
        </td>            
    </tr>
</script>
<!--  ���Ʒ��ѡ  -->
<script id="proSelectTableTr" type="text/template">
     {@each list as pro,index}
            <tr>
                <td style="cursor:pointer" onclick="boxSelectPro(${pro.id})">${pro.name}</td><td>${pro.cateName}</td><td>${pro.stock}</td><td>${pro.price}</td><td>${pro.discut}</td>
                <td style="width:80px;"><span class="btn btn-small btn-info" onclick="boxSelectPro(${pro.id})"><i class="halflings-icon ok white "></i>ѡ��</span>                      
                </td> 
            </tr>
     {@/each}

    
</script>
<script>
    var scoreRatio = <?=$scoreRatio?>;
    var memberDisc = 1;//��Ա�ۿۼ�
    var memberDiscArr = {};//��Ա�ۿ۵�ȫ������
    $("#memberPhone").focus(); 
    //����ת���۸�
    var scoreToMoney = function(score){
        var rule = <?=$scoreRatio?>; //300�� = 10Ԫ
        
        var price = (score/rule).toFixed(2);//Math.floor(score/rule);
        var leftScore = score - Math.floor(price * rule);
        var allScore  = parseInt($("#memtbl_score").html());
        return {
          price :   price,
          score :   leftScore,
          allLeftScore : allScore - score + leftScore, 
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
        
         if(!confirm("�Ƿ�ȷ���շ�")){
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
        if(num < 1){
            num = 1;
        }
        $("#item_num_"+i).val(num);//����ȡ���󱣴�
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
        //$(".tblProOrgPrice").each(function(){//��������ۿ�
            sum += $(this).val() - 0;
            /* ��������ۿ�
            //��Ʒ����
            var idx = $(this).attr("data-idx");
            var num = parseInt($("#item_num_"+idx).val())
            sum += $(this).val()*num - 0;
            */
        });
        $("#bill_sum_price").attr("trueprice",sum);
        $("#bill_sum_price").val((sum/100).toFixed(2));
    }
    //������Ʒ�ϼ�
    var calcProNum = function(){
        var allNum = 0;
        $(".tblProNum").each(function(){
            allNum = allNum + parseInt($(this).val()); 
        });
        $("#proTblNumRow_num").html(allNum);
    }
    //�������ն������ܽ������
    var calcBillSumInfo = function(){
        calcBillPrice();//�����ܼ�
        calcProNum();//������Ʒ�����ϼ�
        
        var memberId = $("#memberId").val();
        if(memberId){
            //�����û���Ҫ���Ѷ��ٿ��Ͻ��
            if(($("#bill_member_card").val()-0) > ($("#bill_sum_price").val()-0)){
                $("#bill_member_card").val(Math.floor($("#bill_sum_price").val()));//���ڿۿ� ֻ��������
            }
        }
        
        var billSumPrice         = parseInt($("#bill_sum_price").attr("trueprice"),10);//parseFloat($("#bill_sum_price").val());//Ӧ�ս��
        var billDisc             = parseFloat($("#bill_disc").val());//�ۿ�
        var billMemCard          = Math.floor($("#bill_member_card").val());//���Ͻ��
        var billMemScore         = parseInt($("#bill_member_score").val(),10);//ʹ�û���
        if(billMemScore){
            var scoreToMoneyObj      = scoreToMoney(billMemScore);
            billMemScore = scoreToMoneyObj.price;
         }
         //����û��������㹻�Ļ���
         
        
        var endPrice = billSumPrice;// * billDisc;//�ۿۺ�ļ۸�  ��Ϊÿ�м۸񶼼�¼�ˣ��Ͳ���Ҫ����ڼ����ۿ���
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
        //����ʣ���ǰ
        $("#bill_card_left").val($("#memtbl_card").html() - $("#bill_member_card").val() + billMemCard);
        if(billMemScore){
            //var scoreToMoneyObj;
            var m1 = $("#bill_member_card").val()-0;
            var m2 = $("#bill_end_sum").val()-0;
            $("#bill_score_left").val(parseInt(scoreToMoneyObj.allLeftScore,10) + parseInt(moneyToScore(m1 - 0 + m2),10) -scoreToMoneyObj.canUseScore);
        }else{
            $("#bill_score_left").val($("#memtbl_score").html()-0 + newScore);
        }
        $("#bill_end_sum").val(Math.round((endPrice/100).toFixed(2)));
    }
    //�˵��е��������������ı䣬�����¼���
    $(".billIptChg").blur(function(){
        //���ڽ��
        if($(this).attr("id") == "bill_member_card"){ //�ж����õĽ��ܹ���
            if($(this).val()-0 > $("#memtbl_card").html()-0){
                alert("�������㱾��������ϵͳ���Զ�����");
                $(this).val($("#memtbl_card").html());
            }
            if(!$(this).val()||$(this).val() == "")$(this).val(0);
        }
        //����
        if($(this).attr("id") == "bill_member_score"){ //�ж����õĽ��ܹ���
            if($(this).val() -0 > $("#memtbl_score").html() -0){
                $(this).val($("#memtbl_score").html());
            }
            //������ת�����Ա��������������� scoreRatio
            var o = scoreToMoney($(this).val()); //########################
            if(o){
                $(this).val($(this).val());
                $("#scoreToMoneyNote").html("<br/>���ϻ����൱�ڣ�"+o.price+"Ԫ ");//+o.allLeftScore
            }
            
            if(!$(this).val())$(this).val(0);
        }
        //�ۿ۽������޸ģ������Ժ���ӵ���Ʒ��������ۿ�    
        if($(this).attr("id") == "bill_disc"){
            if($(this).val() > 1){
                $(this).val(1);
            }
            memberDisc = $(this).val();
        }
        
            
        calcBillSumInfo();
    });
    
    
    
    
    //------------------------------------
    //  ��������
    //------------------------------------
    //����������¼���
    var refreshRightTbl = function(){
        var v = $("#bill_disc").val() - 0;
        $(".tblProDisc").each(function(){
            var proDisc = $(this).attr("data-rel") - 0;//��Ʒ���õ�����ۿ�
           if(proDisc == "0.00" || proDisc < v){//�������������ۿۣ����ܰ������ۿ۽��м���
              $(this).val(v);
           }else if(proDisc > v){
               $(this).val(proDisc);
           }
           var i = $(this).attr("data-idx");
           //����ÿ�еļ۸�
           proTblCalPrice(i);
        });
    }
    //�޸����ۿ�
    $("#bill_disc").blur(function(){//����ۿ��޸�ҳ��
        refreshRightTbl();
    });
    //���Ӧ���۸��Ƿ��е���
    $("#bill_end_sum").keyup(function(){
        $("#endSumModifyFlag").val(1);
    });
    
    var showMemDis = function(){
        if(memberDiscArr){
            var proCateName = <?=$productCateJson?>;
            var msgStr = "��������ۿ۷ֱ��ǣ�\n\n";
            
            for(k in memberDiscArr){
                if(k in proCateName){
                    msgStr += "    " + proCateName[k] + " : " + memberDiscArr[k]+"\n";
                }
            }
            alert(msgStr);
        }
        
    }
//    var enterIn = function(evt){
//        var evt=evt?evt:(window.event?window.event:null);//����IE��FF
//        if (evt.keyCode==13){
//            doSearchMember();
//        }
//    }
</script>
<script type="text/javascript" src="js/checkout/memeber.js"></script>
<script type="text/javascript" src="js/checkout/protbl.js"></script>
</body>
</html>