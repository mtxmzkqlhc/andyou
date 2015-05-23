<!DOCTYPE html>
<html>
<head>
	<meta charset="GBK" />
	<title>充值打印</title>
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
		createContent(0);
        var pnum = $("#pnum").val();
        if(pnum != 1){
            iTop += 50;
            createContent(1);
        }
		LODOP.SET_PRINT_PAGESIZE(3,580,45,"");//这里3表示纵向打印且纸高“按内容的高度”；1385表示纸宽138.5mm；45表示页底空白4.5mm
		//LODOP.PREVIEW();	
		LODOP.PRINT();	
	};
    
    
    
	function createContent(iii){	
        
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
        
        iTop += 10;
        
        
        //会员信息
        <?php
        
        if($memberInfo){
            $txtArr = array(
                "会员卡号：".$memberInfo["cardno"],
                "会员类型：".$memberInfo["cateName"],
                "充值单号："."No.".$bno,
                "充值金额：".$money,
                "当前余额：".$nowBalance,
                "销 售 员：".$staffName,
                "出单时间：".date("Y-m-d H:i",SYSTEM_TIME),
            );
            
            $i = 4;
            foreach ($txtArr as $txt){
                echo "iTop += txtLineHeight;
                LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,'{$txt}');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);
                ";
                
            }
            
        }
        ?>
               
        iTop += 10;
        
        if(iii == 1){
            iTop += txtLineHeight;
            LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,'<第一联>商户留存');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);
            iTop += txtLineHeight;
            LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,'请顾客在下方签字：');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);
            
            iTop += txtLineHeight*3;
        }else{
            iTop += txtLineHeight;
            LODOP.ADD_PRINT_TEXT(iTop,0,pageWidth,txtLineHeight,'<第二联>顾客留存');LODOP.SET_PRINT_STYLEA(0,'FontSize',8);
            
        }
        
        //底部
        iTop += txtLineHeight;
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