<?php
/**
 * 会员管理管理
 *
 */
class  Yun_Page_M_Member extends Yun_Page_M_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'M_Member';		
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}
    
    /**
     *  注册显示的页面
     */
    public function doRegShow(ZOL_Request $input, ZOL_Response $output){
        $output->errMsg = $input->get("errMsg");
		$output->setTemplate('M/RegShow');
        
    }
    /**
     * 登录显示的页面
     */
    public function doLoginShow(ZOL_Request $input, ZOL_Response $output){
        $output->errMsg = $input->get("errMsg");
		$output->setTemplate('M/Login');
    }
    
    /**
     * 执行注册的动作
     */
    public function doReg(ZOL_Request $input, ZOL_Response $output){
        $phone  = $input->post("phone");
        $passwd = $input->post("passwd");
        if(empty($phone) || empty($passwd)){
            $this->showRegErrorMsg("请输入完整");
        }
        if(!preg_match("#^1\d{10}$#", $phone,$mt)){
            $this->showRegErrorMsg("手机号输入错误");
        }
        
        //获得登录信息
        $loginInfo = Helper_Yun_Member::getLoginInfo(array('phone'=>$phone));
        if($loginInfo){//已经注册过了
            $this->showRegErrorMsg("该手机号已经注册过了，请直接登录");
        }
        
        //创建登录信息
        $passwd = Helper_Yun_Member::mkLoginPasswd(array('passwd'=>$passwd,'salt'=>$phone));
        $item = array(
            'phone'  => $phone,
            'passwd' => $passwd,
            'regtm'  => SYSTEM_TIME,
        );
        
		Helper_Dao::insertItem(array(
            'addItem'       =>  $item, #数据列
            'dbName'        =>  'Db_AndyouYun',    #数据库名
            'tblName'       =>  'member_login',    #表名
		));
        
        $output->message = "恭喜您，注册成功";
        $this->showMessage($input,$output);
        exit;
    }

    /**
     * 执行登录的动作
     */
    public function doLogin(ZOL_Request $input, ZOL_Response $output){
        $phone  = $input->post("phone");
        $passwd = $input->post("passwd");
        if(empty($phone) || empty($passwd)){
            $this->showRegErrorMsg("请输入完整");
        }
        if(!preg_match("#^1\d{10}$#", $phone,$mt)){
            $this->showRegErrorMsg("手机号输入错误");
        }
        
        //获得登录信息
        $loginInfo = Helper_Yun_Member::getLoginInfo(array('phone'=>$phone));
        if(!$loginInfo){//已经注册过了
            $this->showLoginErrorMsg("<a href='?c=M_Member&a=RegShow'>该手机号还没有注册过，请注册</a>");
        }
        
        $passwd = Helper_Yun_Member::mkLoginPasswd(array('passwd'=>$passwd,'salt'=>$phone));
        if($passwd == $loginInfo["passwd"]){
            
            $output->message = "恭喜您，登录成功";
            $this->showMessage($input,$output);
            
        }else{
            $this->showLoginErrorMsg("用户名或密码错误");
        }
        
        exit;
    }
    /**
     * 显示注册错误信息
     */
    private function showRegErrorMsg($msg){
        $url = "?c=M_Member&a=RegShow&errMsg=".urlencode($msg);
        header("Location:".$url);
        exit;
                
    }
    
    /**
     * 显示登录错误信息
     */
      private function showLoginErrorMsg($msg){
        $url = "?c=M_Member&a=LoginShow&errMsg=".urlencode($msg);
        header("Location:".$url);
        exit;
                
    }
	
}

