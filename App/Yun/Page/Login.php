<?php
/**
 *  登录相关
 */
class Yun_Page_Login extends Yun_Page_Abstract{

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
         $rtnFlag = Helper_Yun_Member::login(array(
                                                'userId'       => $userId,
                                                'password'     => $passWd,
                                             ));
         if($rtnFlag == 1){#登录OK
             header("Location:?c=Member");
             exit;       
         }else{#登录失败
             Helper_Front::JumpToLogin(array(
                'msg'     => '用户名或密码错误', #消息内容
             ));
         }
    }
    
    //退出登录
    public function doLogout(ZOL_Request $input, ZOL_Response $output){
        
        setcookie("yun_admin_uid",'xxx',1,"/","");
        setcookie("yun_admin_cipher", "xxx", null, "/", "", null, true);
        Helper_Front::JumpToLogin(array('backUrl'=>''));
    }

}
