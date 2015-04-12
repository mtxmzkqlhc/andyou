<!DOCTYPE html>
<html>
    <head>
        <meta charset="GBK">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
        <title>登录<?=$sysName?></title>
        <style>
            body{margin: 0px;background:#000 url("image/loginbg.jpg") no-repeat;}
            .page{height: 100%;margin:0px;}
            .homeLayover {position: fixed;background: #000;background: rgba(0,0,0,.8);top: 0;left: 0;bottom: 0;right: 0;z-index:1000}

            .homeDialog.loginDialog {
                width: 347px;
                margin: 200px 202px;
                display: block;
                opacity: 1
            }


            .homeDialog.loginDialog .loginWrapper {
                padding: 55px 20px 5px
            }

            .homeDialog.loginDialog input {
                outline: 0;
                display: block;
                width: 100%;
                line-height: 24px;
                height: 44px;
                border: 1px solid #AEAEAE;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
                -moz-background-clip: padding;
                -webkit-background-clip: padding-box;
                background-clip: padding-box;
                margin-bottom: 13px;
                padding: 9px;
                font-size: 16px;
                color: #666
            }

            .homeDialog.loginDialog .btnLoginSubmit {
                font-size: 16px;
                display: block;
                width: 100%;
                text-align: center;
                color: #FFF;
                background: #0080ff;
                line-height: 42px;
                margin-top: 24px;
                border: 1px solid #06C;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
                -moz-background-clip: padding;
                -webkit-background-clip: padding-box;
                background-clip: padding-box
            }

            .homeDialog.loginDialog .verifyCode input {
                width: 150px;
                display: inline-block;
                margin: 0 10px 0 0
            }


            .homeDialog.loginDialog .loginAction {
                margin: 13px 0;
                font-size: 12px
            }

            .homeDialog.loginDialog .loginAction .forgetPsd {
                float: right;
                color: #575757
            }


            .homeDialog.loginDialog .loginCheck input {
                display: block;
                opacity: 0;
                position: absolute;
                z-index: 2;
                left: 0;
                top: 0;
                margin: 0;
                width: 14px;
                height: 14px;
                cursor: pointer
            }

            

        </style>
    </head>
    <body>


        <div class="homeLayover1"></div>
            <form method="POST" action="?">
        <div class="homeDialog loginDialog show">
            <div class="loginContainer">
                <div class="loginForm">
                    <div class="loginWrapper">
                        <div class="inputWrapper">
                            <input class="iptUserName" id="loginName" type="text" name="userId" placeholder="用户名"></div>
                        <div class="inputWrapper"><input class="iptPsd" id="loginPwd" type="password" name="passwd" placeholder="密码"></div>
                        <button class="btnLoginSubmit" type="submit">登录</button>
                        <div style="color: red;padding: 10px 0;text-align: center;"><?=$msg?></div>
                        <input type="hidden" value="Login" name="c"/>
                        <input type="hidden" value="Login" name="a"/>
                        
                    </div>
                </div>
            </div>
        </div>
            </form>


    </body>
</html>