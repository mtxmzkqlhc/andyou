<!DOCTYPE html>
<html>
<head>
	<meta charset="GBK" />
	<title>��ӡ</title>
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
        <tr><td>���۵���</td><td>No.<?=$bno?></td></tr>
        <tr><td colspan="9">&nbsp;</td></tr>
        <?php
        if($memberInfo){
        ?>
            <tr><td>��Ա����</td><td><?=$memberInfo["cateName"]?></td></tr>
            <tr><td>��Ա����</td><td><?=empty($memLeftInfo["score"])? 0 : $memLeftInfo["score"]?></td></tr>
            <tr><td>�������</td><td>��<?=empty($memLeftInfo["balance"])?0 : $memLeftInfo["balance"]?></td></tr>
            <tr><td colspan="9">&nbsp;</td></tr>
        <?php }?>
    </table>
    
    <table align="center" width="100%">
        <tr><td>��Ʒ</td><td>����</td><td>����</td><td>�ϼ�</td><td>�ۺ��</td></tr>
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
        
        <tr><td>��Ʒ������</td><td><?=count($proInfoArr)?></td></tr>
        <tr><td>Ӫ�ս��ϼƣ�</td><td>��</td></tr>
        <tr><td>���ֵֿ۽�</td><td>��<?=$billDetail["useScoreAsMoney"]?></td></tr>
        <tr><td>����֧����</td><td>��<?=$billDetail["useCard"]?></td></tr>
        <tr><td>���۵��Żݽ�</td><td>��</td></tr>
        
        <tr><td>����ʵ����</td><td>��<?=$billDetail["price"]/100?></td></tr>
        <tr><td>��û��֣�</td><td></td></tr>
        <tr><td colspan="9">&nbsp;</td></tr>
        
        <tr><td>����Ա��</td><td><?=$staffName?></td></tr>
        <tr><td>����ʱ�䣺</td><td><?=date("Y-m-d H:i:s",$billDetail["tm"])?></td></tr>
        <tr><td colspan="9"><?=$sysCfg['PrintEndTitle']["value"] ?></td></tr>
        <tr><td colspan="9">лл���٣����ǽ��߳�Ϊ������</td></tr>
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
    <input type="text" value="2" id="pnum" size="4"/>��
    <input type="button" value="��ӡ" onclick="print()"/>
    <input type="button" value="����" onclick="goback()"/>
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