<!DOCTYPE html>
<html>
<head>
	<meta charset="GBK" />
	<title>打印</title>
    <style>
        
    </style>
</head>
<body>
    <h3>整体信息</h3>
    <table align="center">
        <tr><td>单号</td><td><?=$bno?></td></tr>
        <tr><td>销售员</td><td><?=$staffName?></td></tr>
        <tr><td>收款</td><td><?=$billDetail["price"]/100?></td></tr>
        <tr><td>卡上消费</td><td><?=$billDetail["useCard"]?></td></tr>
        <tr><td>使用积分</td><td><?=$billDetail["useScore"]?></td></tr>
        <tr><td>消费时间</td><td><?=date("Y-m-d H:i:s",$billDetail["tm"])?></td></tr>
    </table>
    <h3>产品信息</h3>
    <table align="center">
        <tr><td>商品</td><td>单价</td><td>数量</td><td>折扣</td><td>售价</td></tr>
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
                    echo "<tr><td>{$proName}</td><td>{$proPrice}</td><td>{$proInfo["num"]}</td><td>{$proInfo["discount"]}</td><td>".($proInfo["price"]/100)."</td></tr>";
                }
            }
        ?>
    </table>
    
    <pre>
        <?php
            print_r($billDetail);
            print_r($proInfoArr);
            print_r($memLeftInfo);
        ?>
    </pre>
    <input type="button" value="打印" onclick="print()"/>
    <input type="button" value="返回" onclick="goback()"/>
    <script>
    var print = function(){
        
    }
    
    var goback = function(){
        window.location.href = "?c=Checkout";
    }
    </script>
</body></html> 