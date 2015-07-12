<?php
/**
 *  ��¼���
 */
class Yun_Page_Login extends Yun_Page_Abstract{

    public function __construct(){}

    public function validate(ZOL_Request $input, ZOL_Response $output){
        $output->pageType    = 'Login';
        $output->noLoginCheck = true; #����֤��¼
        
        if (!parent::baseValidate($input, $output)) { return false; }
        return true;
    }

    /**
     * Ĭ�Ϸ���
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
         if($rtnFlag == 1){#��¼OK
             header("Location:?c=Member");
             exit;       
         }else{#��¼ʧ��
             Helper_Front::JumpToLogin(array(
                'msg'     => '�û������������', #��Ϣ����
             ));
         }
    }
    
    //�˳���¼
    public function doLogout(ZOL_Request $input, ZOL_Response $output){
        
        setcookie("yun_admin_uid",'xxx',1,"/","");
        setcookie("yun_admin_cipher", "xxx", null, "/", "", null, true);
        Helper_Front::JumpToLogin(array('backUrl'=>''));
    }

}
