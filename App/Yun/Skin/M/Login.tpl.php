<!DOCTYPE html>
<html>
<head>
    <meta charset="gbk">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>��֮���Ա��¼</title>         
<link rel="stylesheet" href="css/frozen.css"> 
<link rel="stylesheet" href="css/m/global.css"> 
<script src="js/zepto.min.js"></script>       
<script src="js/frozen.js"></script>    
<script src="js/m/global.js"></script>  
</head>
    
   
<body ontouchstart>
  
<header class="ui-header ui-header-positive ui-border-b">
    <i class="ui-icon-return" onclick="history.back()"></i><h1>��¼��֮��</h1>
</header>
    
   
    
<section class="ui-container">

    <section>
        <?php
        if($errMsg){
        ?>
        <div class="ui-tooltips ui-tooltips-warn" id="error_msg">
            <div class="ui-tooltips-cnt ui-border-b">
                <i></i> <?php echo $errMsg;?><a class="ui-icon-close"></a>
            </div>
        </div>
        <?php
        }
        ?>
        <form action="?" method="POST" onsubmit="return doCheck()">

        <div class="demo-item">
            
            <div class="demo-block">
                    <div class="ui-form-item ui-form-item-pure ui-border-b">
                        <input type="number"  name="phone" id="phone" placeholder="�����������ֻ���" />
                        <a href="#" class="ui-icon-close">
                        </a>
                    </div>
                    <div class="ui-form-item ui-form-item-pure ui-border-b">
                        <input type="password" name="passwd" id="passwd" placeholder="����������">
                        <a href="#" class="ui-icon-close">
                        </a>
                    </div>
                
            </div>
        </div>
        <div class="demo-item">
            <div class="demo-block">
                <div class="ui-btn-wrap">
                <button class="ui-btn-lg ui-btn-primary">
                    ��¼
                </button>
            </div>
            </div>
        </div>
        <input type="hidden" value="<?=$ctlName?>" name="c"/>
        <input type="hidden" value="Login" name="a"/>
        </form> 
        
        <div class="demo-block"> 
            <div class="ui-btn-wrap">
                <a class="ui-btn-lg" href="?c=M_Member&a=RegShow"  style="color:#666666"> 
                    ûע�᣿ȥע�ᣡ
                </a>
            </div>
        </div>
    </section>




</section><!-- /.ui-container-->
<script>
var doCheck = function(){
    //return true;
    var phone   = $.trim($("#phone").val());
    var passwd  = $.trim($("#passwd").val());
    if(phone == "" || passwd == ""  ){        
        showTips("����д����");
        return false;
    }
    var reg = /^0?1[0-9]\d{9}$/;
    if (!reg.test(phone)) {
        showTips("�������ֻ��Ų�����Ҫ��");
        return false;
    };
    return true;
}

$("#error_msg").tap(function(){
    $(this).hide();
})


//�رհ�ť
$(".ui-icon-close").click(function(){
    $(this).siblings("input").val(""); 
});


</script>
    </body>
</html>