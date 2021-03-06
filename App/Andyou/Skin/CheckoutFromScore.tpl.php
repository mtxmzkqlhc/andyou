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
#searchMemBtn2,#searchProBtn,#setDiscBtn,#removeGoodsBtn,#removeMemInfo{padding:3px 4px;margin-bottom:10px;}
#billContent .memextinfo{display:none}
.yellow, .yellow [class="box-header"], .yellow.box, .slider.yellow .ui-slider-range, .progress.yellow .ui-progressbar-value, .sliderVertical.yellow .ui-slider-range, .progressSlim.yellow .ui-progressbar-value, .label-warning, .badge-warning {
  background: #FFC40D !important;
  border-color: #FFC40D !important;
  
}
.yellow .box-header{color: #fff;}
.box-content {
  background: #fff !important;
}
</style>
<div id="content">

			<div class="row-fluid">
                
                <div class="box" style="border-color: #FFC40D !important;">
                    <div class="box-content clearfix"  style="padding:15px 10px 8px;">
                        <div class="box-l clearfix">
                            <dl>
                                <dt>会员</dt>
                                <dd><input type="text" value="" id="memberPhone"><span class="btn btn-mini" title="查询用户" id="searchMemBtn2"><i class="halflings-icon search white"></i></span>
                                </dd>
                            </dl><dl>
                                <dt>&nbsp;</dt>
                                <dd style="text-align:right;width: 180px;">
                                <span class="btn btn-mini btn-info" title="清空所有已选择的商品" id="removeMemInfo" style="display:none;"><i class="halflings-icon star-empty white"></i>清空</span>
                                </dd>
                            </dl>
                        </div>
                        <div class="box-r">
                            <table width="100%" id="memberInfoTbl">
                                <tr>
                                    <td class="mtbl_l">会员姓名</td><td class="mtbl_r"><span class="label label-success" id="memtbl_name" style="display:none"></span></td>
                                    <td class="mtbl_l">会员类型</td><td class="mtbl_r" id="memtbl_cate"></td>
                                    <td class="mtbl_l">享受折扣</td><td class="mtbl_r" id="memtbl_disc"></td>
                                </tr>
                                <tr>
                                    <td class="mtbl_l">累计消费</td><td class="mtbl_r" id="memtbl_allsum"></td>
                                    <td class="mtbl_l">当前积分</td><td class="mtbl_r" id="memtbl_score"></td>
                                    <td class="mtbl_l">卡内余额</td><td class="mtbl_r" id="memtbl_card"></td>
                                </tr>
                                <tr> <td class="mtbl_l">备注</td><td class="mtbl_r" id="memtbl_remark" colspan="8"> </td></tr>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
    
    
    
            <!--   账单部分     -->
    
            <form method="POST" action="?" onsubmit="return doCheckIpt()" onkeydown="if(event.keyCode==13)return false;" >
			<div class="row-fluid" id="listBox" style="display:none">
                
                <div class="box yellow">
					<div class="box-header">
						<h2>积分兑换</h2>
					</div>
                    <div class="box-content clearfix" id="billContent">
                        <div class="box-l clearfix">
                            <dl class="clearfix">
                                <dt>单号</dt>
                                <dd><input type="text" value="S<?=Helper_Bill::getCommonMaxBno()?>" id="memberPhone" disabled="true" ></dd>
                            </dl>
                            <dl class="clearfix memextinfo">
                                <dt>总金额</dt>
                                <dd><input type="text" value="0" id="bill_sum_price" name="bill[bill_sum_price]" readonly="true" trueprice="0" /></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>使用积分</dt>
                                <dd><input type="text" value="0" id="bill_member_score" data-org="0" class="billIptChg" readonly="true" name="bill[bill_member_score]">
                                   <span style="color:#999999;padding-bottom:5px;" id="scoreToMoneyNote"></span>
                                </dd>
                            </dl>
                            <dl class="clearfix"  style="display:none">
                                <dt>折扣</dt>
                                <dd><input type="text" value="1" id="bill_disc" class="billIptChg" name="bill[bill_disc]" /></dd>
                            </dl>
                            <dl class="clearfix"  style="display:none">
                                <dt>折扣后应付</dt>
                                <dd><input type="text" value="0.00" id="bill_aftdisc_price" name="bill[bill_aftdisc_price]"  readonly="true" /></dd>
                            </dl>
                            <dl class="clearfix memextinfo">
                                <dt>卡内扣款</dt>
                                <dd><input type="text" value="0" id="bill_member_card" data-org="0" class="billIptChg" readonly="true" name="bill[bill_member_card]" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>现金支付</dt>
                                <dd><input type="text" value="0.00" id="bill_end_sum" name="bill[bill_end_sum]" style="font-weight:bold;color:#EB3C00"></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>销售员</dt>
                                <dd>
                                    <select name="staffid" id="staffid" name="bill[staffid]"><option value='0'>请选择</option>
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
                                    <dt>会员</dt>
                                    <dd><input type="text" value="" id="bill_end_membernm"  disabled="true"></dd>
                                </dl>
                                
                                <dl class="clearfix" style="display:none;">
                                    <dt>卡上余额</dt>
                                    <dd><input type="text" value="" id="bill_card_left"  disabled="true"></dd>
                                </dl>
                                
                                <dl class="clearfix" style="display:none;">
                                    <dt>最终积分</dt>
                                    <dd><input type="text" value="" id="bill_score_left"  disabled="true"></dd>
                                </dl>
                                
                                <dl class="clearfix">
                                    <dt>备注说明</dt>
                                    <dd><textarea style="width:140px;height:50px;" name="remark"></textarea></dd>
                                </dl>
                            </dl>
                            <div style="text-align:center;">
                                <input type="submit" value="确认兑换积分" class="btn btn-primary" id="addbtn" disabled="true"/>
                                <input type="hidden" value="Checkout" name="c"/>
                                <input type="hidden" value="Done" name="a"/>
                                <input type="hidden" value="0" name="memberId" id="memberId"/>
                                <input type="hidden" value="1" name="isBuyScore" id="isBuyScore"/>
                                <input type="hidden" value="0" name="endSumModifyFlag" id="endSumModifyFlag"/>
                            </div>
                        </div>
                        <div class="box-r" style="width:700px;">
                            <div id="barScanDiv"><span style="font-weight:bold;padding-right:10px;display: inline;">条码</span> <input type="text" value="" id="proBarCode"><span class="btn btn-mini" title="查询商品" id="searchProBtn"><i class="halflings-icon search white"></i></span>
                                <span class="btn btn-mini btn-info" title="设置折扣" id="setDiscBtn" style="display:none;"><i class="halflings-icon th-list white"></i>设置折扣</span>
                                <span class="btn btn-mini btn-info" title="清空所有已选择的商品" id="removeGoodsBtn" style="display:none;"><i class="halflings-icon th-list white"></i>清空商品</span>
                            </div>
                            <div>
                                <table class="table table-striped table-bordered" id="proListTable">
                                    <thead><tr role="row"><th>商品名称</th><th  style="width:30px;">库存</th><th>单价</th><th style="width:99px;">数量</th><th  style="width:30px;">折扣</th><th>价格</th><th style="width:60px;">销售员</th><th style="width:60px;">操作</th></tr> </thead>   
						  
                                    <tbody id="proListTbody">
                                        <tr><td colspan="10" style="text-align: center;color:#666666;padding:100px 0 100px;background:#ffffff;">-- 请扫描条码以添加商品 --</td></tr>
                                    </tbody>
                                
                                    <tbody id="proTblNumRow">
                                        <tr><td colspan="10" style="text-align: right;color:#666666;padding:10px 20px 10px;background:#ffffff;">商品总数：<span id="proTblNumRow_num">0</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
            </div>
            </form>
</div>

<div id="add-pro-box" style="display: none;">
    <table class="table table-striped table-bordered" style="width:640px;margin:5px 0;font-size:12px;">
    <thead><tr role="row"><th>商品名称</th><th>分类</th><th  style="width:30px;">库存</th><th>单价</th><th>最低折扣</th><th>操作</th></tr> </thead>
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
        <td>${pro.stock}</td>
        <td id="item_sprice_${rowIdx}">${pro.price}</td>
        <td align="center"><span class="btn btn-small btn-info"  onclick="proTblDelNum(${rowIdx})"><i class="halflings-icon minus white"></i></span>
        <input type='text' value='1' id='item_num_${rowIdx}' name='item_num[${rowIdx}]' class='tblProNum' onblur="proTblCalPrice(${rowIdx})">
        <span class="btn btn-small btn-info" onclick="proTblAddNum(${rowIdx})"><i class="halflings-icon plus white "></i></span></td>
        <td><input type='text' value='${memberDisc}' data-rel='${pro.discut}' data-cate='${pro.cateId}' id='item_disc_${rowIdx}' name='item_disc[${rowIdx}]' onblur="proTblCalPrice(${rowIdx})" class='tblProDisc' data-idx="${rowIdx}" readonly="true"></td>
        <td id="item_price_${rowIdx}" >${pro.price}</td>  
        <td id="item_stafftd_${rowIdx}"><span style="color:#999999">同左边</span></td>
        <td><button class="btn btn-small btn-info" onclick="proTblDel(${rowIdx})"><i class="halflings-icon remove white "></i></button>
            <span class="btn btn-small" onclick="proTblSetStaff(${rowIdx})" title="设置该商品的销售员"><i class="halflings-icon user white"></i></span>
            <input type="hidden" value="${pro.id}" name="item_id[${rowIdx}]" class="proTblItemIds" data-idx="${rowIdx}"/>
            <input type="hidden" value="0" name="item_staffid[${rowIdx}]" id="item_staff_${rowIdx}"/>  
            <input type="hidden" value="${pro.oprice}" id="item_org_sprice_${rowIdx}" class="tblProOrgPrice" data-idx="${rowIdx}"/>
            <input type="hidden" value="${pro.oprice}" id="item_calc_sprice_${rowIdx}" class="tblProPrice"/>
        </td>            
    </tr>
</script>
<!--  多产品多选  -->
<script id="proSelectTableTr" type="text/template">
     {@each list as pro,index}
            <tr>
                <td style="cursor:pointer" onclick="boxSelectPro(${pro.id})">${pro.name}</td><td>${pro.cateName}</td><td>${pro.stock}</td><td>${pro.price}</td><td>${pro.discut}</td>
                <td style="width:80px;"><span class="btn btn-small btn-info" onclick="boxSelectPro(${pro.id})"><i class="halflings-icon ok white "></i>选择</span>                      
                </td> 
            </tr>
     {@/each}

    
</script>
<script>
    var memberAllScore = 0;
    var scoreRatio = <?=$scoreRatio?>;
    var minCheckoutScore = <?=$minCheckoutScore?>;
    var memberDisc = 1;//会员折扣价
    var memberDiscArr = {};//会员折扣的全部数组
    var memCardChange = false;//销售员是否修改了卡内支付的金额
    var memScoreChage = false;//销售员是否修改了积分
    $("#memberPhone").focus(); 
    //积分转换价格
    var scoreToMoney = function(score){
        var rule = <?=$scoreRatio?>; //300分 = 10元
        
        var price = (score/rule).toFixed(2);//Math.floor(score/rule);
        var leftScore = score - Math.floor(price * rule);
        var allScore  = parseInt($("#memtbl_score").html());
        return {
          price :   price,
          score :   leftScore,
          allLeftScore : allScore - score + leftScore, 
          canUseScore :  price * rule
        };
    }
    //钱换积分
    var moneyToScore = function(price){
        var rule = <?=$scoreRatio?>; //300分 = 10元
        return price * rule;
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
        
        /*
        //如果会员有卡内余额，但是销售员没有数据，就提示一下!
        if($("#memtbl_card").html() != "" && $("#memtbl_card").html() != "0"){
            var memcard = parseInt($("#memtbl_card").html(),10); 
            var billcard = $("#bill_member_card").val();
            if((billcard == "" || billcard == "0") &&  memcard > 0){
                 if(!confirm("顾客卡内还有余额，确认不从卡内扣款吗？")){
                     return false;
                 }
            }
        }*/
        
        //验证销售员是否已经选择
        var staffid = parseInt($("#staffid").val(),10);
        if(staffid == 0){
            alert("请选择销售员");
            return false;
        }
        
        var cardV = $("#bill_member_card").val() - 0;
        var cashV = $("#bill_end_sum").val() - 0;
        var all = $("#bill_sum_price").val()-0;
        var scoreV = $("#bill_member_score").val() -0;
        var cfmStr =  "是否确定收费 "+all+" 元，其中使用积分"+scoreV+"，卡内扣款 "+cardV+" 元，现金支付 "+cashV+" 元？";        
               
         
        if(!confirm(cfmStr)){
            return false;
        }
        
        return true;
    }
    
    
    
    //商品表格的操作
    //设置一个商品的营业员
    var setStaffIdx = 0;
    var setStaffDlg = null;
    var proTblSetStaff = function(i){
        setStaffIdx = i;
        setStaffDlg = art.dialog({title: '请选择销售员',width:"300px",height:"100px",content: $("#set-staff-box").html()});
    }
    var chgSetStaff = function(o){
        var i = o.value;
        var name = o.options[o.options.selectedIndex].innerHTML;
        if(i==0){
            $("#item_stafftd_"+setStaffIdx).html('<span style="color:#999999">同左边</span>');            
            $("#item_staff_"+setStaffIdx).val(0);
        }else{
            $("#item_stafftd_"+setStaffIdx).html(name);
            $("#item_staff_"+setStaffIdx).val(i);
        }
        if(setStaffDlg != null)setStaffDlg.close();
    }
    
    var proTblNumChg = function(i,t){
        
        var num = parseInt($("#item_num_"+i).val(),10);
        if(t == 1){//加            
            if(num>100)return false;
            num++;
        }else{
            if(num<2)return false;
            num--;
        }
        $("#item_num_"+i).val(num);
        //重新计算价格
        proTblCalPrice(i);
    }
    //计算一行的总价
    var proTblCalPrice = function(i){
        var sprice = parseInt($("#item_org_sprice_"+i).val(),10);
        var num    = parseInt($("#item_num_"+i).val(),10);
        if(num < 1){
            num = 1;
        }
        $("#item_num_"+i).val(num);//数量取整后保存
        var disc   = parseFloat($("#item_disc_"+i).val());
        if(isNaN(disc) || disc > 1) disc =1;
        var price  = sprice * num * disc;
        $("#item_calc_sprice_"+i).val(price);//保存以分为单位的价格
        price = price / 100;
        price = price.toFixed(2);
        $("#item_price_"+i).html(price);
        calcBillSumInfo();
    }
    //增加购买数量
    var proTblAddNum = function(i){
        proTblNumChg(i,1);
        return false;
    }
    //减少购买数量
    var proTblDelNum = function(i){
        proTblNumChg(i,0);
        return false;
    };
    //计算最终订单的价格
    var calcBillPrice = function(){
        var sum = 0;
       $(".tblProPrice").each(function(){
        //$(".tblProOrgPrice").each(function(){//不管最低折扣
            sum += $(this).val() - 0;
            /* 不管最低折扣
            //商品数量
            var idx = $(this).attr("data-idx");
            var num = parseInt($("#item_num_"+idx).val())
            sum += $(this).val()*num - 0;
            */
        });
        $("#bill_sum_price").attr("trueprice",sum);
        $("#bill_sum_price").val(Math.round((sum/100).toFixed(2)));
    }
    //计算商品合计
    var calcProNum = function(){
        var allNum = 0;
        $(".tblProNum").each(function(){
            allNum = allNum + parseInt($(this).val()); 
        });
        $("#proTblNumRow_num").html(allNum);
    }
    //计算最终订单的总金额数据
    var calcBillSumInfo = function(){
        calcBillPrice();//计算总价
        calcProNum();//计算商品数量合计
        
        var memberId = $("#memberId").val();
        if(memberId){
            //修正用户需要花费多少卡上金额
            if(($("#bill_member_card").val()-0) > ($("#bill_sum_price").val()-0)){
                $("#bill_member_card").val($("#bill_sum_price").val());
            }
        }
        
        var billSumPrice         = parseInt($("#bill_sum_price").attr("trueprice"),10);//parseFloat($("#bill_sum_price").val());//应收金额
        var billDisc             = parseFloat($("#bill_disc").val());//折扣
        var billMemCard          = parseFloat($("#bill_member_card").val());//卡上金额
        var billMemScore         = parseInt($("#bill_member_score").val(),10);//使用积分
        if(billMemScore){
            var scoreToMoneyObj      = scoreToMoney(billMemScore);
            billMemScore = scoreToMoneyObj.price;
         }
         //如果用户卡内有足够的积分
         
        
        var endPrice = Math.round(billSumPrice/100);// * billDisc;//折扣后的价格  因为每行价格都记录了，就不需要最后在计算折扣了
         //---------------------------------------------------
         //~~~~~ 首先核算积分 ~~~~~~
         //---------------------------------------------------
        //计算这些钱需要多少积分
        //alert(needScore);
        if(!memScoreChage){
            var needScore = Math.round(endPrice * scoreRatio);
            var needPayMoney = 0;
            if(memberAllScore < needScore){
                //alert("会员的积分已不足");
                needScore = memberAllScore;
                needPayMoney = Math.round(endPrice - needScore/scoreRatio);
                //$("#scoreToMoneyNote").show();
                //alert(needPayMoney);
            }
            $("#bill_member_score").val(needScore);
            $("#bill_member_score").attr("data-org",needScore);
        }
        
        
        //将积分转换可以被可以整除的数字 scoreRatio
        var o = scoreToMoney($("#bill_member_score").val()); 
        if(o){
            $("#scoreToMoneyNote").html("<br/>以上积分相当于："+Math.round(o.price)+"元 ");//+o.allLeftScore

            //计算扣除积分后，还需要多少钱
            endPrice = Math.round(endPrice - o.price);
        }
         //---------------------------------------------------
         //~~~~~ 进行卡内扣款 ~~~~~~
         //---------------------------------------------------
        
        //会员的卡内支付  当卡内余额>0时，左侧卡内消费栏自动显示相应扣款额（如余额100，本次商品80则此栏显示80，本次商品120则此栏为100），
        if(endPrice > 0 && memberId && !memCardChange){//如果销售员修改过卡内支付的金额，就不调整数字了
            //客户当前剩余的钱
            var memNowCard   = $("#memtbl_card").html() - 0;
            var showEndPrice = endPrice;
            var tmpp = 0;
            if(memNowCard > showEndPrice){//如果当前会员卡内余额大于总额
                tmpp = showEndPrice;
            }else{
                tmpp = memNowCard;
            }
                        
            $("#bill_member_card").val(tmpp);
            $("#bill_member_card").attr("data-org",tmpp)
                 
        }
        //计算扣除积分后，还需要多少钱
        endPrice = Math.round(endPrice - $("#bill_member_card").val());
        if(endPrice < 0)endPrice=0;
        /*
        var billMemCard = $("#bill_member_card").val();
        $("#bill_aftdisc_price").val((endPrice/100).toFixed(2));
        if(billMemCard){//如果用户卡里还有余额
            if(billMemCard * 100 > endPrice){//卡内还有前
                  billMemCard = (billMemCard * 100 - endPrice) / 100;
                  endPrice = 0;  
            }else{
                endPrice = endPrice - billMemCard * 100;
                billMemCard = 0;
            }
        }
        if(billMemScore){//使用用户的积分
            if(billMemScore * 100 > endPrice){
                billMemScore = (billMemScore * 100 - endPrice) / 100;
                endPrice = 0;  
            }else{
                endPrice = endPrice - billMemScore * 100;
                billMemScore = 0;                
            }
            
        }
        //花钱得积分
        var newScore = 0;
        newScore = moneyToScore($("#bill_sum_price").val()) - $("#bill_member_score").val();
        //卡上剩余的前
        
        $("#bill_card_left").val($("#memtbl_card").html() - $("#bill_member_card").val() + billMemCard);
        if(billMemScore){
            //var scoreToMoneyObj;
            var m1 = $("#bill_member_card").val()-0;
            var m2 = $("#bill_end_sum").val()-0;
            $("#bill_score_left").val(parseInt(scoreToMoneyObj.allLeftScore,10) + parseInt(moneyToScore(m1 - 0 + m2),10) -scoreToMoneyObj.canUseScore);
        }else{
            $("#bill_score_left").val($("#memtbl_score").html()-0 + newScore);
        }*/
        $("#bill_end_sum").val(Math.round(endPrice));
    }
    //账单有的输入框如何有所改变，就重新计算
    $(".billIptChg").blur(function(){
        //卡内金额
        if($(this).attr("id") == "bill_member_card"){ //判断设置的金额不能过大
            if($(this).val()-0 > $("#memtbl_card").html()-0){
                alert("卡内余额不足本次输入金额系统将自动调整");
                $(this).val($("#memtbl_card").html());
            }
            if(!$(this).val()||$(this).val() == "")$(this).val(0);
            //判断销售员是否手动修改过
            if(($("#bill_member_card").attr("data-org") - 0) != ($(this).val() - 0)){
                memCardChange = true;
            }
        }
        //积分
        if($(this).attr("id") == "bill_member_score"){ //判断设置的金额不能过大
            if($(this).val() -0 > $("#memtbl_score").html() -0){
                $(this).val($("#memtbl_score").html());
            }
            
            //将积分转换可以被可以整除的数字 scoreRatio
            var o = scoreToMoney($(this).val()); //########################
            if(o){
                $(this).val($(this).val());
                $("#scoreToMoneyNote").html("<br/>以上积分相当于："+Math.round(o.price)+"元 ");//+o.allLeftScore
            }
            if(!$(this).val())$(this).val(0);
            if($(this).val() !== $(this).attr("data-org")){
                memScoreChage = true;
            }
        }
        //折扣进行了修改，设置以后添加的商品都是这个折扣    
        if($(this).attr("id") == "bill_disc"){
            if($(this).val() > 1){
                $(this).val(1);
            }
            memberDisc = $(this).val();
        }
        
            
        calcBillSumInfo();
    });
    
    
    
    
    //------------------------------------
    //  左侧操作区
    //------------------------------------
    //左侧区域重新计算
    var refreshRightTbl = function(){
        var v = $("#bill_disc").val() - 0;
        $(".tblProDisc").each(function(){
            var proDisc = $(this).attr("data-rel") - 0;//产品设置的最低折扣
            var proCateId = $(this).attr("data-cate"); //产品的分类
            v = 1;
            //获得产品所在分类的折扣
            if(memberDiscArr){
                if(proCateId in memberDiscArr){
                    v = memberDiscArr[proCateId];
                }
            }
           if(proDisc == "0.00" || proDisc == 0 || proDisc < v){//如果有设置最低折扣，就能按照总折扣进行计算
              $(this).val(v);
           }else if(proDisc > v){
               $(this).val(proDisc);
           }
           var i = $(this).attr("data-idx");
           //计算每行的价格
           proTblCalPrice(i);
        });
    }
    //修改总折扣
    $("#bill_disc").blur(function(){//左侧折扣修改页面
        refreshRightTbl();
    });
    //监控应付价格是否有调整
    $("#bill_end_sum").keyup(function(){
        $("#endSumModifyFlag").val(1);
    });
    
    var showMemDis = function(){
        if(memberDiscArr){
            var proCateName = <?=$productCateJson?>;
            var msgStr = "各分类的折扣分别是：\n\n";
            
            for(k in memberDiscArr){
                if(k in proCateName){
                    msgStr += "    " + proCateName[k] + " : " + memberDiscArr[k]+"\n";
                }
            }
            alert(msgStr);
        }
        
    }
//    var enterIn = function(evt){
//        var evt=evt?evt:(window.event?window.event:null);//兼容IE和FF
//        if (evt.keyCode==13){
//            doSearchMember();
//        }
//    }

</script>
<script type="text/javascript" src="js/checkout/memeber_score.js"></script>
<script type="text/javascript" src="js/checkout/protbl_score.js"></script>
</body>
</html>