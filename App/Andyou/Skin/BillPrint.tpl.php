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
    <div style="text-align:center;padding:50px 0;margin:40px auto;width:500px;border:1px solid #cccccc;">
        СƱ��ӡ��...
        <br/><br/>
        <a href="?c=Checkout">���ؼ�������</a>
        <!--
        ��ӡ<input type="text" value="2" id="pnum" size="2"/>��
        <input type="button" value="��ӡСƱ" onclick="print()" id="btnPrint"/>
        <br/><br/>
        <a href="?c=Checkout">���ؼ�������</a> | 
        <a href="?c=Bills&a=DelBill&bid=<?=$bid?>&sn=<?=$bsn?>">ȡ���ö���</a>
        <?=print_r($billDetail);?>
        <?=print_r($proInfoArr);?>
        -->
    </div>
<script src="js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="js/LodopFuncs.js" type="text/javascript"></script>

<script language="javascript" type="text/javascript"> 
   var LODOP; //����Ϊȫ�ֱ���
   var iTop = 0;
   var pageWidth = "48mm";
   var txtLineHeight = 15;
	function MyPreview() {	
		LODOP=getLodop();  
		LODOP.PRINT_INIT("��ӡ");
		createContent();
        var pnum = $("#pnum").val();
        if(pnum != 1){
            iTop += 50;
            createContent();
        }
		LODOP.SET_PRINT_PAGESIZE(3,580,45,"");//����3��ʾ�����ӡ��ֽ�ߡ������ݵĸ߶ȡ���1385��ʾֽ��138.5mm��45��ʾҳ�׿հ�4.5mm
		//LODOP.PREVIEW();	
		LODOP.PRINT();	
	};
    
	function createContent(){	
        
        //����
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,20,"<?=$sysName?>");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",9);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
        //��ӭ��
        iTop += 20;
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"<?=$sysCfg['PrintSubTitle']["value"] ?>");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",8);
		LODOP.SET_PRINT_STYLEA(0,"Bold",0);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
        
        //���۵���
        iTop += txtLineHeight;
        <?php if($isBuyScore){?>
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"���ֵ��ţ�No.<?=$bno?>");
        <?php }else{?>
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"���۵��ţ�No.<?=$bno?>");
        <?php }?>
		LODOP.SET_PRINT_STYLEA(0,"FontSize",8);
        
        
        //��Ա��Ϣ
        <?php
        
        if($memberInfo){
            $txtArr = array(
                "��Ա���ͣ�".$memberInfo["cateName"],
            );
            if(!empty($memLeftInfo["score"])){
                $txtArr[] = "��Ա���֣�". $memLeftInfo["score"];
            }
            if(!empty($memLeftInfo["balance"])){
                $txtArr[] = "��������". $memLeftInfo["balance"];
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
        
        
        //�ײ�
        <?php
            $proAllNum = 0; //����Ĳ�Ʒ����
             if($proInfoArr){
                 echo "iTop += txtLineHeight;"
                         . "LODOP.ADD_PRINT_TEXT(iTop,0,35,txtLineHeight,'Ʒ��');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);"
                         . "LODOP.ADD_PRINT_TEXT(iTop,35,45,txtLineHeight,'����');LODOP.SET_PRINT_STYLEA(0,'FontSize',8); "
                         . "LODOP.ADD_PRINT_TEXT(iTop,80,35,txtLineHeight,'����');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);"
                         . "LODOP.ADD_PRINT_TEXT(iTop,115,35,txtLineHeight,'�ۿ�');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);"
                         . "LODOP.ADD_PRINT_TEXT(iTop,150,60,txtLineHeight,'�ϼ�');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);";
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
                "��Ʒ������"=>$proAllNum,
                //"Ӧ�ս�"=>"��".($orgSumPrice/100),
                "Ӧ�ս�"=>"��".round($billDetail["itemSumPrice"]/100),
            );
            //$txtArr["ʵ�ս�"] = "��".($billDetail["price"]/100);
            //$txtArr["�����ۿۣ�"] =  $billDetail["discount"];
            $tmpflag = false;
            if($isBuyScore){#���ֶһ�
                $txtArr["ʹ�û��֣�"] =  $billDetail["useScore"];
                $tmpflag = true;
            }
            if($memberInfo && $billDetail["useCard"]){
                $txtArr["���ڿۿ"] =  "��".$billDetail["useCard"];
                $tmpflag = true;
            }
            if($tmpflag){//������˿����߻��֣��ͽв�����
                $txtArr["�ֽ�֧����"] = "��".round($billDetail["price"]/100);
            }else{
                $txtArr["ʵ�ս�"] = "��".round($billDetail["price"]/100);
            }
            if($memberInfo){
                //$txtArr["��û��֣�"] = $newScore;
            }
            $txtArr["����Ա��"] = $staffName;
            foreach ($txtArr as $key => $txt){
                echo "iTop += txtLineHeight;LODOP.ADD_PRINT_TEXT(iTop,0,100,txtLineHeight,'{$key}');LODOP.SET_PRINT_STYLEA(0,'Alignment',1);LODOP.SET_PRINT_STYLEA(0,'FontSize',8);"
                . "LODOP.ADD_PRINT_TEXT(iTop,100,70,txtLineHeight,'{$txt}');LODOP.SET_PRINT_STYLEA(0,'Alignment',3);LODOP.SET_PRINT_STYLEA(0,'FontSize',8);";
                
            }
         ?>
                 
        iTop += txtLineHeight;
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"����ʱ�䣺<?=date("Y-m-d H:i",$billDetail["tm"])?>");
		LODOP.SET_PRINT_STYLEA(0,'FontSize',8);
        //�ײ�
        iTop += txtLineHeight+10;
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"<?=$sysCfg['PrintEndTitle']["value"] ?>");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",8);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
        
        iTop += txtLineHeight;
		LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,"лл���٣����ǽ��߳�Ϊ������");
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
        $("#btnPrint").val("��ӡ��!...");
        //$("#printDiv").jqprint();
        MyPreview();
    }
    
    var goback = function(){
        window.location.href = "?c=Checkout";
    }
    </script>
</body></html> 