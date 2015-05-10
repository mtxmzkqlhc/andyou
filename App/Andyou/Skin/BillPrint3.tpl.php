<!DOCTYPE html>
<html>
<head>
	<meta charset="GBK" />
	<title>打印</title>
    <style>
        *{margin:0px;padding:0px;}
        #printDiv{margin:auto;font-size:12px;}
    </style>
</head>
<body>
    <?php
    error_reporting(0);
    ?>
    <div style="text-align:center;padding-top:10px;">
        
        打印<input type="text" value="2" id="pnum" size="2"/>份
        <input type="button" value="打印" onclick="print()" id="btnPrint"/>
        <input type="button" value="返回" onclick="goback()"/>
        <a href="?c=Bills&a=DelBill&bid=<?=$bid?>&sn=<?=$bsn?>">取消该订单</a>
    </div>
    <div id="printDiv">
    <table align="center" width="100%">
        <tr><td colspan="9">&nbsp;</td></tr>
        <tr><td colspan="9" style="font-weight: bold" align="center"><?=$sysName?></td></tr>
        <tr><td colspan="9" align="center"><?=$sysCfg['PrintSubTitle']["value"] ?></td></tr>
        <tr><td>销售单号</td><td style="font-size:11px;">No.<?=$bno?></td></tr>
        <tr><td colspan="9">&nbsp;</td></tr>
        <?php
        if($memberInfo){
        ?>
            <tr><td>会员类型</td><td><?=$memberInfo["cateName"]?></td></tr>
            <tr><td>会员积分</td><td><?=empty($memLeftInfo["score"])? 0 : $memLeftInfo["score"]?></td></tr>
            <tr><td>卡内余额</td><td>￥<?=empty($memLeftInfo["balance"])?0 : $memLeftInfo["balance"]?></td></tr>
            <tr><td colspan="9">&nbsp;</td></tr>
        <?php }?>
    </table>
    
    <table align="center" width="100%" style="font-size:12px;">
        <tr><td>商品</td><td>单价</td><td>数量</td><td>合计</td></tr>
        <?php
            if($proInfoArr){
                $proArr = array();
                foreach($proInfoArr as $proInfo){
                    $proId = $proInfo["proId"];
                    $proName = "";
                    $proPrice = 0;
                    if(!isset($proArr[$proId])){
                        $proArr[$proId] = Helper_Product::getProductInfo(array('id'=>$proId));
                    }
                    if( $proArr[$proId]){
                        $proName = $proArr[$proId]["name"];
                        $proPrice = $proArr[$proId]["price"];
                    }
                    echo "<tr><td colspan='9'>{$proName}</td></tr>";
                    echo "<tr><td>&nbsp;</td><td>{$proPrice}</td><td>{$proInfo["num"]}</td><td>".($proPrice*$proInfo["num"])."</td></tr>";//<td>".($proInfo["price"]/100)."</td>
                }
            }
        ?>
    </table>
    <table align="center"  width="100%">
        <tr><td colspan="9">&nbsp;</td></tr>
        
        <tr><td>商品数量：</td><td align="right"><?=count($proInfoArr)?></td></tr>
        <tr><td>应收金额合计：</td><td align="right">￥<?=$orgSumPrice/100?></td></tr>
        <?php  if($memberInfo){  ?>
      
        <tr><td>卡内支付金额：</td><td align="right">￥<?=$billDetail["useCard"]?></td></tr>
        <?php }?>
        <tr><td>本次折扣：</td><td align="right"><?=$billDetail["discount"]?></td></tr>
        
        <?php  if($isBuyScore){  ?>
      
        <tr><td>使用积分：</td><td align="right"><?=$billDetail["useScore"]?></td></tr>
        <?php }?>
        <tr><td>本次实付金额：</td><td align="right">￥<?=$billDetail["price"]/100?></td></tr>
        <?php  if($memberInfo){  ?>
        <tr><td>获得积分：</td><td align="right"><?=$newScore?></td></tr>
        <?php }?>
        <tr><td colspan="9">&nbsp;</td></tr>
        
        <tr><td>销售员：</td><td align="right"><?=$staffName?></td></tr>
        <tr><td>出单时间：</td><td align="right"><?=date("Y-m-d H:i",$billDetail["tm"])?></td></tr>
        <tr><td colspan="9" align="center" style="padding-top:8px;"><?=$sysCfg['PrintEndTitle']["value"] ?></td></tr>
        <tr><td colspan="9" align="center">谢谢光临，我们将竭诚为您服务！</td></tr>
        <tr><td colspan="9">&nbsp;</td></tr>
        <tr><td colspan="9">&nbsp;</td></tr>
    </table>
    </div>
<script src="js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="js/jquery.jqprint.js" type="text/javascript"></script>
    <script>
        //printDiv
    var print = function(){
        var pnum = $("#pnum").val();
        if(pnum != 1){
            $("#printDiv").append($("#printDiv").html());
        }
        $("#btnPrint").val("打印中...");
        $("#printDiv").jqprint();
    }
    
    var goback = function(){
        window.location.href = "?c=Checkout";
    }
    </script>
</body></html> 