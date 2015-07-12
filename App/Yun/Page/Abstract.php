<?php

abstract class Yun_Page_Abstract extends ZOL_Abstract_Page{

	/**
	 * �����Validate
	 */
	public function baseValidate(ZOL_Request $input, ZOL_Response $output){
         
		$output->execName   = $input->execName    = $input->getExecName();
		$output->actName    = $input->actName     = $input->getActionName();
		$output->ctlName    = $input->ctlName     = $input->getControllerName();
        $output->admin      = $input->cookie(Helper_Yun_Member::$strUid);
        $output->userId     = $input->cookie(Helper_Yun_Member::$strUid); #�û���
        $cipher             = $input->cookie(Helper_Yun_Member::$strCipher);
        $output->sysCfg     = Helper_Yun_Option::getAllOptions();
        $output->sysName    = empty($output->sysCfg['SysName']) ? "" : $output->sysCfg['SysName']["value"] ;
        
        if(!$output->noLoginCheck){
            #��֤��¼
            $output->isLogin = Helper_Yun_Member::checkLogin(array(
                                                'userid'       => $output->userId,
                                                'cipher'       => $cipher,
                                           ));

            if(!$output->isLogin){#�����¼��OK
                $backUrl = isset($_SERVER['REQUEST_URI']) ? 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '';
                if(strpos($backUrl, "Login"))$backUrl = "";
                Helper_Front::JumpToLogin(array('backUrl' => $backUrl));
            }
        }
        #��ù���Ա���
        $output->adminInfo = Helper_Yun_AdminUser::getAdminUserInfo(array('userId'=>$output->userId));
        $output->adminType = (int)$output->adminInfo["isAdmin"];//����Ա���ͣ�����Ȩ���ж�
        
        if($output->permission && !in_array($output->adminType,$output->permission)){
            echo "Permission denied";
            exit;
        }
        #ͷβhtml
		$output->header     = $output->fetchCol("Part/Header");
         //���
        $output->navi       = $output->fetchCol("Part/Navi");
		$output->footer     = $output->fetchCol("Part/Footer");
        return true;

        return true;
	}
    

    protected function showHtml($msg){
        echo '<!DOCTYPE html><html lang="en"><head><title>���˸�ȥ...</title></head>';
        echo "<div style='margin:0 auto;text-align:center;padding-top:30px;line-height:40px'>{$msg}</div>";
        echo '</body></html>';
        exit;
    }
}
