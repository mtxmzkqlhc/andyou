<!DOCTYPE html>
<html>
<head>
	<meta charset="GBK" />
	<title>��ӡ</title>
    <style>
        
    </style>
</head>
<body>
    <table align="center">
        <tr><td>����</td><td><?=$bno?></td></tr>
        <tr><td>Ա��</td><td><?=$staffName?></td></tr>
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