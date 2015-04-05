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
                                <dd><input type="text" value="13512026125" id="memberPhone"></dd>
                            </dl>
                        </div>
                        <div class="box-r">
                            <table width="100%" id="memberInfoTbl">
                                <tr>
                                    <td class="mtbl_l">会员姓名</td><td class="mtbl_r" id="memtbl_name"></td>
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
    
            <form method="POST" action="?">
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
                                <dt>应收金额</dt>
                                <dd><input type="text" value="0" id="bill_sum_price" name="bill[bill_sum_price]"></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>折扣</dt>
                                <dd><input type="text" value="1" id="bill_disc" class="billIptChg" name="bill[bill_disc]"/></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>本次应收</dt>
                                <dd><input type="text" value="0.00" id="bill_aftdisc_price" name="bill[bill_aftdisc_price]"/></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>卡内扣款</dt>
                                <dd><input type="text" value="0" id="bill_member_card" class="billIptChg" readonly="true" name="bill[bill_member_card]"/></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>使用积分</dt>
                                <dd><input type="text" value="0" id="bill_member_score" class="billIptChg" readonly="true" name="bill[bill_member_score]"></dd>
                            </dl>
                            <dl class="clearfix">
                                <dt>本次需收款</dt>
                                <dd><input type="text" value="0.00" id="bill_end_sum" name="bill[bill_end_sum]"></dd>
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
                            </dl>
                            <div style="text-align:center;">
                                <input type="submit" value="确认收款" class="btn btn-primary" id="addbtn"/>
                                <input type="hidden" value="Checkout" name="c"/>
                                <input type="hidden" value="Done" name="a"/>
                                <input type="hidden" value="0" name="memberId" id="memberId"/>
                            </div>
                        </div>
                        <div class="box-r" style="width:700px;">
                            <div id="barScanDiv"><span style="font-weight:bold;padding-right:10px;">条码</span> <input type="text" value="12345678890233232" id="proBarCode"></div>
                            <div>
                                <table class="table table-striped table-bordered" id="proListTable">
                                    <thead><tr role="row"><th>商品名称</th><th  style="width:30px;">库存</th><th>单价</th><th style="width:95px;">数量</th><th  style="width:30px;">折扣</th><th>价格</th><th style="width:60px;">销售员</th><th style="width:60px;">操作</th></tr> </thead>   
						  
                                    <tbody id="proListTbody">
                                        <tr><td colspan="10" style="text-align: center;color:#666666;padding:100px 0 200px;background:#ffffff;">-- 请扫描条码以添加商品 --</td></tr>
                                    </tbody></table>
                            </div>
                        </div>
            </div>
            </form>
</div>

<div id="add-pro-box" style="display: none;">
    <table class="table table-striped table-bordered" styple="width:100%">
    <thead><tr role="row"><th>商品名称</th><th  style="width:30px;">库存</th><th>单价</th><th>操作</th></tr> </thead>
    <tbody id="proAddBoxTbody"></tbody></table>
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
    <tr id="item_row_${rowIdx}">
        <td>${pro.name}</td>
        <td>${pro.stock}</td>
        <td id="item_sprice_${rowIdx}">${pro.price}</td>
        <td align="center"><span class="btn btn-small btn-info"  onclick="proTblDelNum(${rowIdx})"><i class="halflings-icon minus white"></i></span>
        <input type='text' value='1' id='item_num_${rowIdx}' name='item_num[${rowIdx}]' class='tblProNum'>
        <span class="btn btn-small btn-info" onclick="proTblAddNum(${rowIdx})"><i class="halflings-icon plus white "></i></span></td>
        <td><input type='text' value='1' id='item_disc_${rowIdx}' name='item_disc[${rowIdx}]' onblur="proTblCalPrice(${rowIdx})" class='tblProDisc'></td>
        <td id="item_price_${rowIdx}" class="tblProPrice">${pro.price}</td>  
        <td id="item_stafftd_${rowIdx}"><span style="color:#999999">同左边</span></td>
        <td><button class="btn btn-small btn-info" onclick="proTblDel(${rowIdx})"><i class="halflings-icon remove white "></i></button>
            <span class="btn btn-small" onclick="proTblSetStaff(${rowIdx})" title="设置该商品的销售员"><i class="halflings-icon user white"></i></span>
            <input type="hidden" value="${pro.id}" name="item_id[${rowIdx}]"/>
            <input type="hidden" value="0" name="item_staffid[${rowIdx}]" id="item_staff_${rowIdx}"/>        
        </td>            
    </tr>
</script>
<script>
    $("#proBarCode").focus(); 
    
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
        var sprice = parseFloat($("#item_sprice_"+i).html());
        var num    = parseInt($("#item_num_"+i).val(),10);
        var disc   = parseFloat($("#item_disc_"+i).val());
        if(isNaN(disc) || disc > 1) disc =1;
        var price  = sprice * num * disc;
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
            sum += $(this).html() - 0;
        });
        $("#bill_sum_price").val(sum);
    }
    //计算最终订单的总金额数据
    var calcBillSumInfo = function(){
        calcBillPrice();//计算总价
        var billSumPrice         = parseFloat($("#bill_sum_price").val());//应收金额
        var billDisc             = parseFloat($("#bill_disc").val());//折扣
        var billMemCard          = parseFloat($("#bill_member_card").val());//卡上金额
        var billMemScore         = parseInt($("#bill_member_score").val(),10);//使用积分
        if(billMemScore){
            var scoreToMoneyObj      = scoreToMoney(billMemScore);
            billMemScore = scoreToMoneyObj.price;
         }
        
        var endPrice = billSumPrice * billDisc;//折扣后的价格
        $("#bill_aftdisc_price").val(endPrice.toFixed(2));
        if(billMemCard){//如果用户卡里还有余额
            if(billMemCard > endPrice){//卡内还有前
                  billMemCard = billMemCard - endPrice;
                  endPrice = 0;  
            }else{
                endPrice = endPrice - billMemCard;
                billMemCard = 0;
            }
        }
        if(billMemScore){//使用用户的积分
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
    //账单有的输入框如何有所改变，就重新计算
    $(".billIptChg").blur(function(){
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
    
    
//    var enterIn = function(evt){
//        var evt=evt?evt:(window.event?window.event:null);//兼容IE和FF
//        if (evt.keyCode==13){
//            doSearchMember();
//        }
//    }
    //搜索会员
    iptEnter("#memberPhone",function(){
        doSearchMember();
        $("#bill_member_card").removeAttr("readonly");
        $("#bill_member_score").removeAttr("readonly");
    });
    //条形码
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
    //条形码
    iptEnter("#proBarCode",function(){
        var barcode = $("#proBarCode").val();
        if(barcode){//doGetProductByCode
            var url = "?c=Ajax_Product&a=GetProductByCode&code=" + barcode;
            $.getJSON(url,{},function(data){
                if(data){
                    art.dialog({title: '请选择商品',width:"600px",content: $("#add-pro-box").html()});
                    if(data.num == 1){
                        var proInfo = data.data[0];
                        appendProTable(proInfo);
                    }else if(data.num > 1){
                        
                    }else{
                        alert("该商品未入库");
                    }
                }else{
                    alert("该商品未入库");
                }
            });
            
        }
    });
    
</script>
</body>
</html>