<?php
/**
 *  登录相关
 */
class Andyou_Page_Login extends Andyou_Page_Abstract{

    public function __construct(){}

    public function validate(ZOL_Request $input, ZOL_Response $output){
        $output->pageType    = 'Login';
        $output->noLoginCheck = true; #不验证登录
        
        if (!parent::baseValidate($input, $output)) { return false; }
        return true;
    }

    /**
     * 默认方法
     */
    public function doDefault(ZOL_Request $input, ZOL_Response $output){
        
         $output->setTemplate('Default');
    }
    
    public function doToLogin(ZOL_Request $input, ZOL_Response $output){
         $output->msg = $input->get("msg");
         $output->setTemplate('ToLogin');
    }
	
    public function doLogin(ZOL_Request $input, ZOL_Response $output){
         $userId  = $input->post("userId");
         $passWd  = $input->post("passwd");
         $rtnFlag = Helper_Member::login(array(
                                                'userId'       => $userId,
                                                'password'     => $passWd,
                                             ));
         if($rtnFlag == 1){#登录OK
             Helper_Front::JumpToHome();             
         }else{#登录失败
             Helper_Front::JumpToLogin(array(
                'msg'     => '用户名或密码错误', #消息内容
             ));
         }
    }
    
    //退出登录
    public function doLogout(ZOL_Request $input, ZOL_Response $output){
        
        setcookie("admin_uid",'xxx',1,"/","");
        setcookie("admin_cipher", "xxx", null, "/", "", null, true);
        Helper_Front::JumpToLogin(array('backUrl'=>''));
    }

}
