<html>
<body>
<table>
<thead><tr><th>ID</th><th>商品名</th><th>条码</th><th>分类</th><th>售价</th><th>进货价</th><th>库存</th><th>最低折扣</th><th>积分兑换</th></tr></thead>
<tbody>
<?php
if($data) {
$cssArr = array(2=>'tr_pro_2');
foreach($data as $v) {
$css = isset($cssArr[$v['ctype']]) ? $cssArr[$v['ctype']] : "";
$outStr = '<tr>';
$outStr.='<td>'.$v['id'].'</td>';
$outStr.='<td  data="name" rel="'.$v['id'].'" style="text-align:left;" class="'.$css.'">'.$v['name'].'</td>';
$outStr.='<td data="code" rel="'.$v['id'].'" >'.$v['code'].'</td>';
$outStr.='<td>'.(isset($cateInfo[$v['cateId']]) ? $cateInfo[$v['cateId']] : '-').'</td>';
$outStr.='<td data="price" rel="'.$v['id'].'" >'.round($v['price']/100,2).'</td>';
$outStr.='<td data="inPrice" rel="'.$v['id'].'" >'.round($v['inPrice']/100,2).'</td>';
$outStr.='<td data="stock" rel="'.$v['id'].'" >'.$v['stock'].'</td>';
//$outStr.='<td class="editColumn" data="score" rel="'.$v['id'].'" >'.$v['score'].'</td>';
$outStr.='<td data="discut" rel="'.$v['id'].'" >'.($v['discut'] == "0.00" ? "-" : $v['discut']).'</td>';
$outStr.='<td>'.($v['canByScore']?"是":"否").'</td>';

$outStr.='</tr>';
echo $outStr;
}
} ?>
</tbody>
</table>        
</body>
</html>