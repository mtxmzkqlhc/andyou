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
    <div style="text-align:center;padding:50px 0;margin:40px auto;width:500px;border:1px solid #cccccc;">
        小票打印中...
        <br/><br/>
        <a href="?c=Checkout">返回继续收银</a>
        <!--
        打印<input type="text" value="2" id="pnum" size="2"/>份
        <input type="button" value="打印小票" onclick="print()" id="btnPrint"/>
        <br/><br/>
        <a href="?c=Checkout">返回继续收银</a> | 
        <a href="?c=Bills&a=DelBill&bid=<?=$bid?>&sn=<?=$bsn?>">取消该订单</a>
        <?=print_r($billDetail);?>
        <?=print_r($proInfoArr);?>
        -->
    </div>
<script src="js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="js/LodopFuncs.js" type="text/javascript"></script>

<script language="javascript" type="text/javascript"> 
   var LODOP; //声明为全局变量
   var iTop = 0;
   var pageWidth = "48mm";
   var txtLineHeight = 15;
	function MyPreview() {	
		LODOP=getLodop();  
		LODOP.PRINT_INIT("打印");
		createContent();
        var pnum = $("#pnum").val();
        if(pnum != 1){
            iTop += 50;
            createContent();
        }
		LODOP.SET_PRINT_PAGESIZE(3,580,45,"");//这里3表示纵向打印且纸高“按内容的高度”；1385表示纸宽138.5mm；45表示页底空白4.5mm
		//LODOP.PREVIEW();	
		LODOP.PRINT();	
	};
    
	function createContent(){	
        
        //顶部
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,20,"<?=$sysName?>");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",9);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
        //欢迎词
        iTop += 20;
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"<?=$sysCfg['PrintSubTitle']["value"] ?>");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",8);
		LODOP.SET_PRINT_STYLEA(0,"Bold",0);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
        
        //销售单号
        iTop += txtLineHeight;
        <?php if($isBuyScore){?>
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"积分单号：No.<?=$bno?>");
        <?php }else{?>
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"销售单号：No.<?=$bno?>");
        <?php }?>
		LODOP.SET_PRINT_STYLEA(0,"FontSize",8);
        
        
        //会员信息
        <?php
        
        if($memberInfo){
            $txtArr = array(
                "会员类型：".$memberInfo["cateName"],
            );
            if(!empty($memLeftInfo["score"])){
                $txtArr[] = "会员积分：". $memLeftInfo["score"];
            }
            if(!empty($memLeftInfo["balance"])){
                $txtArr[] = "卡内余额：￥". $memLeftInfo["balance"];
            }
            $i = 4;
            foreach ($txtArr as $txt){
                echo "iTop += txtLineHeight;
                LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,'{$txt}');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);
                ";
                
            }
            
        }
        ?>
               
        iTop += 10;
        
        
        //底部
        <?php
            $proAllNum = 0; //购买的产品件数
             if($proInfoArr){
                 echo "iTop += txtLineHeight;"
                         . "LODOP.ADD_PRINT_TEXT(iTop,0,35,txtLineHeight,'品名');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);"
                         . "LODOP.ADD_PRINT_TEXT(iTop,35,45,txtLineHeight,'单价');LODOP.SET_PRINT_STYLEA(0,'FontSize',8); "
                         . "LODOP.ADD_PRINT_TEXT(iTop,80,35,txtLineHeight,'数量');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);"
                         . "LODOP.ADD_PRINT_TEXT(iTop,115,35,txtLineHeight,'折扣');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);"
                         . "LODOP.ADD_PRINT_TEXT(iTop,150,60,txtLineHeight,'合计');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);";
                $proArr = array();
                foreach($proInfoArr as $proInfo){
                    $proId = $proInfo["proId"];
                    $proAllNum += $proInfo["num"];
                    $proName = "";
                    $proPrice = 0;
                    if(!isset($proArr[$proId])){
                        $proArr[$proId] = Helper_Product::getProductInfo(array('id'=>$proId));
                    }
                    if( $proArr[$proId]){
                        $proName = $proArr[$proId]["name"];
                        $proPrice = $proArr[$proId]["price"];
                    }
                    echo "iTop += txtLineHeight;LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,'{$proName}'); LODOP.SET_PRINT_STYLEA(0,'FontSize',8);";
                    //echo "<tr><td colspan='9'>{$proName}</td></tr>";
                    echo "iTop += txtLineHeight;"
                         . "LODOP.ADD_PRINT_TEXT(iTop,35,50,txtLineHeight,'{$proPrice}');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);"
                         . "LODOP.ADD_PRINT_TEXT(iTop,80,30,txtLineHeight,'{$proInfo["num"]}');LODOP.SET_PRINT_STYLEA(0,'FontSize',8); "
                         . "LODOP.ADD_PRINT_TEXT(iTop,105,40,txtLineHeight,'{$proInfo["discount"]}');LODOP.SET_PRINT_STYLEA(0,'FontSize',8); "
                         . "LODOP.ADD_PRINT_TEXT(iTop,145,60,txtLineHeight,'".round($proInfo["price"]/100)."');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);";//$proPrice*$proInfo["num"]
                    //echo "<tr><td>&nbsp;</td><td>{$proPrice}</td><td>{$proInfo["num"]}</td><td>".($proPrice*$proInfo["num"])."</td></tr>";//<td>".($proInfo["price"]/100)."</td>
                }
            }
            echo "iTop += 10;  ";
        
       
            $txtArr = array(
                "商品数量："=>$proAllNum,
                //"应收金额："=>"￥".($orgSumPrice/100),
                "应收金额："=>"￥".round($billDetail["itemSumPrice"]/100),
            );
            //$txtArr["实收金额："] = "￥".($billDetail["price"]/100);
            //$txtArr["本次折扣："] =  $billDetail["discount"];
            $tmpflag = false;
            if($isBuyScore){#积分兑换
                $txtArr["使用积分："] =  $billDetail["useScore"];
                $tmpflag = true;
            }
            if($memberInfo && $billDetail["useCard"]){
                $txtArr["卡内扣款："] =  "￥".$billDetail["useCard"];
                $tmpflag = true;
            }
            if($tmpflag){//如果用了卡或者积分，就叫补充金额
                $txtArr["现金支付："] = "￥".round($billDetail["price"]/100);
            }else{
                $txtArr["实收金额："] = "￥".round($billDetail["price"]/100);
            }
            if($memberInfo){
                //$txtArr["获得积分："] = $newScore;
            }
            $txtArr["销售员："] = $staffName;
            foreach ($txtArr as $key => $txt){
                echo "iTop += txtLineHeight;LODOP.ADD_PRINT_TEXT(iTop,0,100,txtLineHeight,'{$key}');LODOP.SET_PRINT_STYLEA(0,'Alignment',1);LODOP.SET_PRINT_STYLEA(0,'FontSize',8);"
                . "LODOP.ADD_PRINT_TEXT(iTop,100,70,txtLineHeight,'{$txt}');LODOP.SET_PRINT_STYLEA(0,'Alignment',3);LODOP.SET_PRINT_STYLEA(0,'FontSize',8);";
                
            }
         ?>
                 
        iTop += txtLineHeight;
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"出单时间：<?=date("Y-m-d H:i",$billDetail["tm"])?>");
		LODOP.SET_PRINT_STYLEA(0,'FontSize',8);
        //底部
        iTop += txtLineHeight+10;
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"<?=$sysCfg['PrintEndTitle']["value"] ?>");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",8);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
        
        iTop += txtLineHeight;
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"谢谢光临，我们将竭诚为您服务！");
		LODOP.SET_PRINT_STYLEA(0,'FontSize',8);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
        
        
	};	
    MyPreview();
    setTimeout(function(){
         window.location.href = "?c=Checkout";
    },2000);
</script> 
    <script>
        //printDiv
    var print = function(){
//        var pnum = $("#pnum").val();
//        if(pnum != 1){
//            $("#printDiv").append($("#printDiv").html());
//        }
        $("#btnPrint").val("打印中!...");
        //$("#printDiv").jqprint();
        MyPreview();
    }
    
    var goback = function(){
        window.location.href = "?c=Checkout";
    }
    </script>
</body></html> 