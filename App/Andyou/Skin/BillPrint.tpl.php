<!DOCTYPE html>
<html>
<head>
	<meta charset="GBK" />
	<title>打印</title>
    <style>
        
    </style>
</head>
<body>
    <table align="center">
        <tr><td>单号</td><td><?=$bno?></td></tr>
        <tr><td>员工</td><td><?=$staffName?></td></tr>
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