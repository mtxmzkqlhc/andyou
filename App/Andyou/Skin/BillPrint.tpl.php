<!DOCTYPE html>
<html>
<head>
	<meta charset="GBK" />
	<title>��ӡ</title>
    <style>
        
    </style>
</head>
<body>
    <h3>������Ϣ</h3>
    <table align="center">
        <tr><td>����</td><td><?=$bno?></td></tr>
        <tr><td>����Ա</td><td><?=$staffName?></td></tr>
        <tr><td>�տ�</td><td><?=$billDetail["price"]/100?></td></tr>
        <tr><td>��������</td><td><?=$billDetail["useCard"]?></td></tr>
        <tr><td>ʹ�û���</td><td><?=$billDetail["useScore"]?></td></tr>
        <tr><td>����ʱ��</td><td><?=date("Y-m-d H:i:s",$billDetail["tm"])?></td></tr>
    </table>
    <h3>��Ʒ��Ϣ</h3>
    <table align="center">
        <tr><td>��Ʒ</td><td>����</td><td>����</td><td>�ۿ�</td><td>�ۼ�</td></tr>
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
    <input type="button" value="��ӡ" onclick="print()"/>
    <input type="button" value="����" onclick="goback()"/>
    <script>
    var print = function(){
        
    }
    
    var goback = function(){
        window.location.href = "?c=Checkout";
    }
    </script>
</body></html> 