<!DOCTYPE html>
<html>
<head>
	<meta charset="GBK" />
	<title>打印</title>
    <style>
        
    </style>
</head>
<body>
    <?php
    error_reporting(0);
    ?>
    <div id="printDiv">
    <table align="center" width="100%">
        <tr><td colspan="9">&nbsp;</td></tr>
        <tr><td colspan="9" style="font-weight: bold" align="center"><?=$sysName?></td></tr>
        <tr><td colspan="9"><?=$sysCfg['PrintSubTitle']["value"] ?></td></tr>
        <tr><td>销售单号</td><td>No.<?=$bno?></td></tr>
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
    
    <table align="center" width="100%">
        <tr><td>商品</td><td>单价</td><td>数量</td><td>合计</td><td>折后价</td></tr>
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
                    echo "<tr><td>&nbsp;</td><td>{$proPrice}</td><td>{$proInfo["num"]}</td><td>".($proPrice*$proInfo["num"])."</td><td>".($proInfo["price"]/100)."</td></tr>";
                }
            }
        ?>
    </table>
    <table align="center"  width="100%">
        <tr><td colspan="9">&nbsp;</td></tr>
        
        <tr><td>商品数量：</td><td><?=count($proInfoArr)?></td></tr>
        <tr><td>营收金额合计：</td><td>￥</td></tr>
        <tr><td>积分抵扣金额：</td><td>￥<?=$billDetail["useScoreAsMoney"]?></td></tr>
        <tr><td>卡内支付金额：</td><td>￥<?=$billDetail["useCard"]?></td></tr>
        <tr><td>打折的优惠金额：</td><td>￥</td></tr>
        
        <tr><td>本次实付金额：</td><td>￥<?=$billDetail["price"]/100?></td></tr>
        <tr><td>获得积分：</td><td></td></tr>
        <tr><td colspan="9">&nbsp;</td></tr>
        
        <tr><td>销售员：</td><td><?=$staffName?></td></tr>
        <tr><td>出单时间：</td><td><?=date("Y-m-d H:i:s",$billDetail["tm"])?></td></tr>
        <tr><td colspan="9"><?=$sysCfg['PrintEndTitle']["value"] ?></td></tr>
        <tr><td colspan="9">谢谢光临，我们将竭诚为您服务！</td></tr>
        <tr><td colspan="9">&nbsp;</td></tr>
        <tr><td colspan="9">&nbsp;</td></tr>
    </table>
    </div>
    <pre style="display:none">
        <?php
            print_r($billDetail);
            print_r($proInfoArr);
            print_r($memLeftInfo);
        ?>
    </pre>
    <input type="text" value="2" id="pnum" size="4"/>份
    <input type="button" value="打印" onclick="print()"/>
    <input type="button" value="返回" onclick="goback()"/>
<script src="js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="js/jquery.jqprint.js" type="text/javascript"></script>
    <script>
        //printDiv
    var print = function(){
        var pnum = $("#pnum").val();
        if(pnum != 1){
            $("#printDiv").append($("#printDiv").html());
        }
        $("#printDiv").jqprint();
    }
    
    var goback = function(){
        window.location.href = "?c=Checkout";
    }
    </script>
</body></html> 