<?php

abstract class Andyou_Page_Abstract extends ZOL_Abstract_Page{
	
	/**
	 * 父类的Validate
	 */
	public function baseValidate(ZOL_Request $input, ZOL_Response $output){
        

		$output->execName   = $input->execName    = $input->getExecName();
		$output->actName    = $input->actName     = $input->getActionName();
		$output->ctlName    = $input->ctlName     = $input->getControllerName();
        $output->admin      = $input->cookie(Helper_Member::$strUid);
        $output->userId     = $input->cookie(Helper_Member::$strUid); #用户名
        $cipher             = $input->cookie(Helper_Member::$strCipher);
        $output->sysCfg     = Helper_Option::getAllOptions();
        $output->sysName    = $output->sysCfg['SysName']["value"] ;
        
        if(!$output->noLoginCheck){
            #验证登录
            $output->isLogin = Helper_Member::checkLogin(array(
                                                'userid'       => $output->userId,
                                                'cipher'       => $cipher,
                                           ));

            if(!$output->isLogin){#如果登录不OK
                $backUrl = isset($_SERVER['REQUEST_URI']) ? 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '';
                if(strpos($backUrl, "Login"))$backUrl = "";
                Helper_Front::JumpToLogin(array('backUrl' => $backUrl));
            }
        }
        
        #头尾html
		$output->header     = $output->fetchCol("Part/Header");
         //左侧
        $output->navi       = $output->fetchCol("Part/Navi");
		$output->footer     = $output->fetchCol("Part/Footer");
        return true;
	}
    

    protected function showHtml($msg){
        echo '<!DOCTYPE html><html lang="en"><head><title>我了个去...</title></head>';
        echo "<div style='margin:0 auto;text-align:center;padding-top:30px;line-height:40px'>{$msg}</div>";
        echo '</body></html>';
        exit;
    }
}
