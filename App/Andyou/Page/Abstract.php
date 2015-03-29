<?php

abstract class Andyou_Page_Abstract extends ZOL_Abstract_Page{
	
	/**
	 * �����Validate
	 */
	public function baseValidate(ZOL_Request $input, ZOL_Response $output){
        
        
//        if(!$output->noAdminLogin){
//        
//            $loginFlag = ZOL_Api::run("Security.Auth.adminIsLogin" , array('remoteCheck'=>0,'recAdminLog'=>0));
//            if(!$loginFlag){
//                $this->showHtml("<a href=\"http://admin.zol.com.cn/login.php\"><img src='http://mj.zol.com.cn/img/login.jpg'  style='border:1px solid #ccc;padding:3px;'></a>
//                                <Br/>��֪�����˰ɣ�����ȥ��̨��¼�����ǵı��鶼���������ˣ�");
//            }
//        }

		$output->execName   = $input->execName    = $input->getExecName();
		$output->actName    = $input->actName     = $input->getActionName();
		$output->ctlName    = $input->ctlName     = $input->getControllerName();
        $output->admin      = $input->cookie("S_uid");
        $output->userId     = $input->cookie("zol_userid"); #�û���
        $cipher             = $input->cookie("star_cipher");
        
        #�ύ��
        
        
        
        $checkParam = array(
            'userid' => $output->userId,  #�û�id
            'option' => $output->pageType, #����
        );
//        $permission = Helper_Permission_Function::CheckPermission($checkParam);
//        #�����û�Ȩ�޵��ж�
//        if(!$permission){
//            Helper_Func_Front::showMsg(array(
//                'message'       => '��ϲ���������ɹ�����ȴ���ˣ�',
//                'level'         =>  2,     #0:��ʾ��1:�ɹ� 2��ʧ��
//                'jumpSec'       =>  4,     #�������0������ת
//                'jumpUrl'       =>  'http://www.zol.com.cn/',    #������ת��url
//                'urlArr'        =>  array(#������ת�ĵ�ַ����
//                    '�ص���ҳ' => 'http://www.zol.com.cn/',
//                    '��������' => 'http://my.zol.com.cn/',
//                ), 
//            )); 
//            exit();
//        }
        
        #ͷβhtml
		$output->header     = $output->fetchCol("Part/Header");
         //���
        $output->navi       = $output->fetchCol("Part/Navi");
		$output->footer     = $output->fetchCol("Part/Footer");
        return true;
	}
    

    protected function showHtml($msg){
        echo '<!DOCTYPE html><html lang="en"><head><title>���˸�ȥ...</title></head>';
        echo "<div style='margin:0 auto;text-align:center;padding-top:30px;line-height:40px'>{$msg}</div>";
        echo '</body></html>';
        exit;
    }
}
