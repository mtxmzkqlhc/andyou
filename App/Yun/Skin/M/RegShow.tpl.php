<!DOCTYPE html>
<html>
<head>
    <meta charset="gbk">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>爱之秀会员注册</title>         
<link rel="stylesheet" href="css/frozen.css"> 
<link rel="stylesheet" href="css/m/global.css"> 
<script src="js/zepto.min.js"></script>       
<script src="js/frozen.js"></script>    
<script src="js/m/global.js"></script>  
</head>
    
   
<body ontouchstart>
  
<header class="ui-header ui-header-positive ui-border-b">
    <i class="ui-icon-return" onclick="history.back()"></i><h1>注册安之秀</h1>
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
            <p class="demo-desc">请输入如下信息进行注册</p>
            <div class="demo-block">
                    <div class="ui-form-item ui-form-item-pure ui-border-b">
                        <input type="number"  name="phone" id="phone" placeholder="请输入您的手机号" />
                        <a href="#" class="ui-icon-close">
                        </a>
                    </div>
                    <div class="ui-form-item ui-form-item-pure ui-border-b">
                        <input type="password" name="passwd" id="passwd" placeholder="请输入密码">
                        <a href="#" class="ui-icon-close">
                        </a>
                    </div>
                    <div class="ui-form-item ui-form-item-pure ui-border-b">
                        <input type="password"  name="passwd2"  id="passwd2" placeholder="请再输入一次密码">
                        <a href="#" class="ui-icon-close">
                        </a>
                    </div>
                
            </div>
        </div>
        <div class="demo-item">
            <div class="demo-block">
                <div class="ui-btn-wrap">
                <button class="ui-btn-lg ui-btn-primary">
                    注册
                </button>
            </div>
            </div>
        </div>
        <input type="hidden" value="<?=$ctlName?>" name="c"/>
        <input type="hidden" value="Reg" name="a"/>
        </form> 
        <div class="demo-block"> 
            <div class="ui-btn-wrap">
                <a class="ui-btn-lg" href="?c=M_Member&a=LoginShow" style="color:#666666">
                    注册过？去登录！
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
    var passwd2 = $.trim($("#passwd2").val());
    if(phone == "" || passwd == "" || passwd2 == "" ){        
        showTips("请填写完整");
        return false;
    }
    var reg = /^0?1[0-9]\d{9}$/;
    if (!reg.test(phone)) {
        showTips("您输入手机号不符合要求");
        return false;
    };
    if(passwd != passwd2){
        showTips("您输入两次密码不一致");
        return false;
    }
    return true;
}

$("#error_msg").tap(function(){
    $(this).hide();
})


//关闭按钮
$(".ui-icon-close").click(function(){
    $(this).siblings("input").val(""); 
});


</script>
    </body>
</html>