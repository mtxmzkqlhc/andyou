<?php

abstract class Andyou_Page_Abstract extends ZOL_Abstract_Page{
	
	/**
	 * 父类的Validate
	 */
	public function baseValidate(ZOL_Request $input, ZOL_Response $output){
        
        
//        if(!$output->noAdminLogin){
//        
//            $loginFlag = ZOL_Api::run("Security.Auth.adminIsLogin" , array('remoteCheck'=>0,'recAdminLog'=>0));
//            if(!$loginFlag){
//                $this->showHtml("<a href=\"http://admin.zol.com.cn/login.php\"><img src='http://mj.zol.com.cn/img/login.jpg'  style='border:1px solid #ccc;padding:3px;'></a>
//                                <Br/>不知所措了吧，还不去后台登录？我们的鼻涕都快流出来了！");
//            }
//        }

		$output->execName   = $input->execName    = $input->getExecName();
		$output->actName    = $input->actName     = $input->getActionName();
		$output->ctlName    = $input->ctlName     = $input->getControllerName();
        $output->admin      = $input->cookie("S_uid");
        $output->userId     = $input->cookie("zol_userid"); #用户名
        $cipher             = $input->cookie("star_cipher");
        
        #提交的
        
        
        
        $checkParam = array(
            'userid' => $output->userId,  #用户id
            'option' => $output->pageType, #操作
        );
//        $permission = Helper_Permission_Function::CheckPermission($checkParam);
//        #进行用户权限的判断
//        if(!$permission){
//            Helper_Func_Front::showMsg(array(
//                'message'       => '恭喜您，操作成功，请等待审核！',
//                'level'         =>  2,     #0:提示，1:成功 2：失败
//                'jumpSec'       =>  4,     #如果大于0，将跳转
//                'jumpUrl'       =>  'http://www.zol.com.cn/',    #进行跳转的url
//                'urlArr'        =>  array(#设置跳转的地址数组
//                    '回到首页' => 'http://www.zol.com.cn/',
//                    '个人中心' => 'http://my.zol.com.cn/',
//                ), 
//            )); 
//            exit();
//        }
        
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
