<!DOCTYPE html>
<html>
<head>
    <meta charset="gbk">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>��֮���Աע��</title>         
<link rel="stylesheet" href="css/frozen.css"> 
<link rel="stylesheet" href="css/m/global.css"> 
<script src="js/zepto.min.js"></script>       
<script src="js/frozen.js"></script>    
<script src="js/m/global.js"></script>  
</head>
    
   
<body ontouchstart>
  
<header class="ui-header ui-header-positive ui-border-b">
    <i class="ui-icon-return" onclick="history.back()"></i><h1>ע�ᰲ֮��</h1>
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
            <p class="demo-desc">������������Ϣ����ע��</p>
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
                    <div class="ui-form-item ui-form-item-pure ui-border-b">
                        <input type="password"  name="passwd2"  id="passwd2" placeholder="��������һ������">
                        <a href="#" class="ui-icon-close">
                        </a>
                    </div>
                
            </div>
        </div>
        <div class="demo-item">
            <div class="demo-block">
                <div class="ui-btn-wrap">
                <button class="ui-btn-lg ui-btn-primary">
                    ע��
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
                    ע�����ȥ��¼��
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
        showTips("����д����");
        return false;
    }
    var reg = /^0?1[0-9]\d{9}$/;
    if (!reg.test(phone)) {
        showTips("�������ֻ��Ų�����Ҫ��");
        return false;
    };
    if(passwd != passwd2){
        showTips("�������������벻һ��");
        return false;
    }
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