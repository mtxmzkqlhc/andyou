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
						<h2><i class="halflings-icon list-alt"></i><span class="break"></span>会员信息</h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
						</div>
					</div>
                    <div class="box-content clearfix">
                        <div class="box-l clearfix">
                            <dl>
                                <dt>会员电话</dt>
                                <dd><input type="text" value="13512026125" id="memberPhone"><span class="btn btn-mini" title="查询用户" id="searchMemBtn"><i class="halflings-icon search white"></i></span>
                                </dd>
                            </dl>
                        </div>
                        <div class="box-r">
                            <table width="100%" id="memberInfoTbl">
                                <tr>
                                    <td class="mtbl_l">会员姓名</td><td class="mtbl_r"><span class="label label-success" id="memtbl_name"></span></td>
                                    <td class="mtbl_l">会员类型</td><td class="mtbl_r" id="memtbl_cate"></td>
                                    <td class="mtbl_l">享受折扣</td><td class="mtbl_r" id="memtbl_disc"</td>
                                </tr>
                                <tr>
                                    <td class="mtbl_l">当前积分</td><td class="mtbl_r" id="memtbl_score"></td>
                                    <td class="mtbl_l">卡内余额</td><td class="mtbl_r" id="memtbl_card"></td>
                                    <td class="mtbl_l">&nbsp;</td><td class="mtbl_r" >&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="mtbl_l">备注</td><td class="mtbl_r" id="memtbl_info" colspan="8"> </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
    
    
    
            <!--   账单部分     -->
    
            <form method="POST" action="?" onsubmit="return doCheckIpt()">
			<div class="row-fluid">
                
                <div class="box">
					<div class="box-header">
						<h2><i class="halflings-icon list-alt"></i><span class="break"></span>账单信息</h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="halflings-icon remove"></i></a>
						</div>
					</div>
                    <div class="box-content clearfix" id="billContent">
                        <div class="box-l clearfix">
                            <dl class="clearfix">
                                <dt>单号</dt>
                                <dd><input type="text" value="<?=Helper_Bill::getMaxBno()?>" id="memberPhone" disabled="true" ></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>总金额</dt>
                                <dd><input type="text" value="0" id="bill_sum_price" name="bill[bill_sum_price]" readonly="true" trueprice="0" /></dd>
                            </dl>
                            <dl class="clearfix" style="display:none">
                                <dt>折扣</dt>
                                <dd><input type="text" value="1" id="bill_disc" class="billIptChg" name="bill[bill_disc]"/></dd>
                            </dl>
                            <dl class="clearfix"  style="display:none">
                                <dt>折扣后金额</dt>
                                <dd><input type="text" value="0.00" id="bill_aftdisc_price" name="bill[bill_aftdisc_price]"  readonly="true" /></dd>
                            </dl>
                            <dl class="clearfix memextinfo">
                                <dt>卡内扣款</dt>
                                <dd><input type="text" value="0" id="bill_member_card" class="billIptChg" readonly="true" name="bill[bill_member_card]"/></dd>
                            </dl>
                            <dl class="clearfix memextinfo">
                                <dt>使用积分</dt>
                                <dd><input type="text" value="0" id="bill_member_score" class="billIptChg" readonly="true" name="bill[bill_member_score]">
                                   <br/><span style="color:#999999;padding-bottom:5px;">积分只能使用<?=$scoreRatio?>的整数倍</span>
                                </dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>应收款</dt>
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
                                <dl class="clearfix">
                                    <dt>会员</dt>
                                    <dd><input type="text" value="" id="bill_end_membernm"  disabled="true"></dd>
                                </dl>
                                
                                <dl class="clearfix memextinfo">
                                    <dt>卡上余额</dt>
                                    <dd><input type="text" value="" id="bill_card_left"  disabled="true"></dd>
                                </dl>
                                
                                <dl class="clearfix memextinfo">
                                    <dt>最终积分</dt>
                                    <dd><input type="text" value="" id="bill_score_left"  disabled="true"></dd>
                                </dl>
                            </dl>
                            <div style="text-align:center;">
                                <input type="submit" value="确认收款" class="btn btn-primary" id="addbtn"/>
                                <input type="hidden" value="Checkout" name="c"/>
                                <input type="hidden" value="Done" name="a"/>
                                <input type="hidden" value="0" name="memberId" id="memberId"/>
                            </div>
                        </div>
                        <div class="box-r" style="width:700px;">
                            <div id="barScanDiv"><span style="font-weight:bold;padding-right:10px;">条码</span> <input type="text" value="12345678890233232" id="proBarCode"><span class="btn btn-mini" title="查询商品" id="searchProBtn"><i class="halflings-icon search white"></i></span>
                            <span class="btn btn-mini btn-info" title="批量设置折扣" id="setDiscBtn"><i class="halflings-icon th-list white"></i>批量设置折扣</span>
                            </div>
                            <div>
                                <table class="table table-striped table-bordered" id="proListTable">
                                    <thead><tr role="row"><th>商品名称</th><th  style="width:30px;">库存</th><th>单价</th><th style="width:99px;">数量</th><th  style="width:30px;">折扣</th><th>价格</th><th style="width:60px;">销售员</th><th style="width:60px;">操作</th></tr> </thead>   
						  
                                    <tbody id="proListTbody">
                                        <tr><td colspan="10" style="text-align: center;color:#666666;padding:100px 0 200px;background:#ffffff;">-- 请扫描条码以添加商品 --</td></tr>
                                    </tbody></table>
                            </div>
                        </div>
            </div>
            </form>
</div>

<div id="add-pro-box" style="display: none;">
    <table class="table table-striped table-bordered" style="width:580px;margin:10px 0;font-size:12px;">
    <thead><tr role="row"><th>商品名称</th><th  style="width:30px;">库存</th><th>单价</th><th>操作</th></tr> </thead>
    <tbody id="proAddBoxTbody">
        <tr><td colspan="10" style="text-align: center;color:#666666;padding:20px 0 20px;background:#ffffff;">-- 加载中 --</td></tr>
    </tbody></table>
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
        <input type='text' value='1' id='item_num_${rowIdx}' name='item_num[${rowIdx}]' class='tblProNum'>
        <span class="btn btn-small btn-info" onclick="proTblAddNum(${rowIdx})"><i class="halflings-icon plus white "></i></span></td>
        <td><input type='text' value='1' id='item_disc_${rowIdx}' name='item_disc[${rowIdx}]' onblur="proTblCalPrice(${rowIdx})" class='tblProDisc' data-idx="${rowIdx}"></td>
        <td id="item_price_${rowIdx}" >${pro.price}</td>  
        <td id="item_stafftd_${rowIdx}"><span style="color:#999999">同左边</span></td>
        <td><button class="btn btn-small btn-info" onclick="proTblDel(${rowIdx})"><i class="halflings-icon remove white "></i></button>
            <span class="btn btn-small" onclick="proTblSetStaff(${rowIdx})" title="设置该商品的销售员"><i class="halflings-icon user white"></i></span>
            <input type="hidden" value="${pro.id}" name="item_id[${rowIdx}]"/>
            <input type="hidden" value="0" name="item_staffid[${rowIdx}]" id="item_staff_${rowIdx}"/>  
            <input type="hidden" value="${pro.oprice}" id="item_org_sprice_${rowIdx}"/>
            <input type="hidden" value="${pro.oprice}" id="item_calc_sprice_${rowIdx}" class="tblProPrice"/>
        </td>            
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
    $("#proBarCode").focus(); 
    //积分转换价格
    var scoreToMoney = function(score){
        var rule = <?=$scoreRatio?>; //300分 = 10元
        var price = Math.floor(score/rule);
        var leftScore = score - price * rule;
        return {
          price :   price,
          score :   leftScore,
          canUseScore :  price * rule ,
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
        
        //验证销售员是否已经选择
        var staffid = parseInt($("#staffid").val(),10);
        if(staffid == 0){
            alert("请选择销售员");
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
    //删除一个商品
    var proTblDel = function(i){
        $("#item_row_"+i).remove();
        calcBillSumInfo();
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
            sum += $(this).val() - 0;
        });
        $("#bill_sum_price").attr("trueprice",sum);
        $("#bill_sum_price").val((sum/100).toFixed(2));
    }
    //计算最终订单的总金额数据
    var calcBillSumInfo = function(){
        calcBillPrice();//计算总价
        
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
         
        
        var endPrice = billSumPrice * billDisc;//折扣后的价格
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
        
        $("#bill_card_left").val($("#memtbl_card").html() - $("#bill_member_card").val() + billMemCard);
        $("#bill_score_left").val($("#memtbl_score").html() - $("#bill_member_score").val() +newScore);
        $("#bill_end_sum").val((endPrice/100).toFixed(2));
    }
    //账单有的输入框如何有所改变，就重新计算
    $(".billIptChg").blur(function(){
        //卡内金额
        if($(this).attr("id") == "bill_member_card"){ //判断设置的金额不能过大
            if($(this).val()-0 > $("#memtbl_card").html()-0){
                $(this).val($("#memtbl_card").html());
            }
            if(!$(this).val()||$(this).val() == "")$(this).val(0);
        }
        //积分
        if($(this).attr("id") == "bill_member_score"){ //判断设置的金额不能过大
            if($(this).val() -0 > $("#memtbl_score").html() -0){
                $(this).val($("#memtbl_score").html());
            }
            //将积分转换可以被可以整除的数字
            var o = scoreToMoney($(this).val());
            if(o){
                $(this).val(o.canUseScore);
            }
            
            if(!$(this).val())$(this).val(0);
        }
            
        calcBillSumInfo();
    });
    
    //追加到产品列表
    var proTableTr = document.getElementById('proTableTr').innerHTML;
    var proJuicer  = juicer(proTableTr);
    var proTrIdx   = 0; //记录插入了第几行了
    var appendProTable = function(proInfo){
        var data = {pro:proInfo,rowIdx:proTrIdx};
        var html = proJuicer.render(data);
        if(proTrIdx == 0)$("#proListTbody").empty();
        $("#proListTbody").append(html);
        proTblCalPrice(proTrIdx);//计算单行价格
        calcBillSumInfo();//计算最后的订单价格
        proTrIdx++;
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
    
    //批量设置折扣
    $("#setDiscBtn").click(function(){
        art.dialog({
            title : '批量设置商品折扣',
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
//    var enterIn = function(evt){
//        var evt=evt?evt:(window.event?window.event:null);//兼容IE和FF
//        if (evt.keyCode==13){
//            doSearchMember();
//        }
//    }
    //搜索会员
    iptEnter("#memberPhone",function(){
        doSearchMember();
    });
    $("#searchMemBtn").click(function(){
        doSearchMember();
    });
    //条形码
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
    //条形码
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
                        alert("该商品未入库");
                    }
                }else{
                    alert("该商品未入库");
                }
            });
        }
    }
    
</script>
</body>
</html>