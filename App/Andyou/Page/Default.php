<?php
/**
 * ��̨��Ĭ����ҳ
 * ��ΰ��
 * 2012-12-02
 */
class Andyou_Page_Default extends Andyou_Page_Abstract{

    public function __construct(){}

    public function validate(ZOL_Request $input, ZOL_Response $output){
        $output->pageType = 'Default';
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
        
         $output->setTemplate('ToLogin');
    }
	
    public function doLogin(ZOL_Request $input, ZOL_Response $output){
         $userId   = $input->post("userId");
         $password = Helper_AdminUser::passwdEncrypt($input->post("passwd"));
         $userInfo = Helper_AdminUser::getAdminUserInfo(array('userId'=>$userId));
         if($userInfo["passwd"] != $password ){
             
         }else{
             header("Location:?c=Default");
             exit;
         }
         exit;
        // $output->setTemplate('ToLogin');
    }

}
