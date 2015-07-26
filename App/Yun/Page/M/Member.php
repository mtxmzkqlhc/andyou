<?php
/**
 * ��Ա�������
 *
 */
class  Yun_Page_M_Member extends Yun_Page_M_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'M_Member';		
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}
    
    /**
     *  ע����ʾ��ҳ��
     */
    public function doRegShow(ZOL_Request $input, ZOL_Response $output){
        $output->errMsg = $input->get("errMsg");
		$output->setTemplate('M/RegShow');
        
    }
    /**
     * ��¼��ʾ��ҳ��
     */
    public function doLoginShow(ZOL_Request $input, ZOL_Response $output){
        $output->errMsg = $input->get("errMsg");
		$output->setTemplate('M/Login');
    }
    
    /**
     * ִ��ע��Ķ���
     */
    public function doReg(ZOL_Request $input, ZOL_Response $output){
        $phone  = $input->post("phone");
        $passwd = $input->post("passwd");
        if(empty($phone) || empty($passwd)){
            $this->showRegErrorMsg("����������");
        }
        if(!preg_match("#^1\d{10}$#", $phone,$mt)){
            $this->showRegErrorMsg("�ֻ����������");
        }
        
        //��õ�¼��Ϣ
        $loginInfo = Helper_Yun_Member::getLoginInfo(array('phone'=>$phone));
        if($loginInfo){//�Ѿ�ע�����
            $this->showRegErrorMsg("���ֻ����Ѿ�ע����ˣ���ֱ�ӵ�¼");
        }
        
        //������¼��Ϣ
        $passwd = Helper_Yun_Member::mkLoginPasswd(array('passwd'=>$passwd,'salt'=>$phone));
        $item = array(
            'phone'  => $phone,
            'passwd' => $passwd,
            'regtm'  => SYSTEM_TIME,
        );
        
		Helper_Dao::insertItem(array(
            'addItem'       =>  $item, #������
            'dbName'        =>  'Db_AndyouYun',    #���ݿ���
            'tblName'       =>  'member_login',    #����
		));
        
        $output->message = "��ϲ����ע��ɹ�";
        $this->showMessage($input,$output);
        exit;
    }

    /**
     * ִ�е�¼�Ķ���
     */
    public function doLogin(ZOL_Request $input, ZOL_Response $output){
        $phone  = $input->post("phone");
        $passwd = $input->post("passwd");
        if(empty($phone) || empty($passwd)){
            $this->showRegErrorMsg("����������");
        }
        if(!preg_match("#^1\d{10}$#", $phone,$mt)){
            $this->showRegErrorMsg("�ֻ����������");
        }
        
        //��õ�¼��Ϣ
        $loginInfo = Helper_Yun_Member::getLoginInfo(array('phone'=>$phone));
        if(!$loginInfo){//�Ѿ�ע�����
            $this->showLoginErrorMsg("<a href='?c=M_Member&a=RegShow'>���ֻ��Ż�û��ע�������ע��</a>");
        }
        
        $passwd = Helper_Yun_Member::mkLoginPasswd(array('passwd'=>$passwd,'salt'=>$phone));
        if($passwd == $loginInfo["passwd"]){
            
            $output->message = "��ϲ������¼�ɹ�";
            $this->showMessage($input,$output);
            
        }else{
            $this->showLoginErrorMsg("�û������������");
        }
        
        exit;
    }
    /**
     * ��ʾע�������Ϣ
     */
    private function showRegErrorMsg($msg){
        $url = "?c=M_Member&a=RegShow&errMsg=".urlencode($msg);
        header("Location:".$url);
        exit;
                
    }
    
    /**
     * ��ʾ��¼������Ϣ
     */
      private function showLoginErrorMsg($msg){
        $url = "?c=M_Member&a=LoginShow&errMsg=".urlencode($msg);
        header("Location:".$url);
        exit;
                
    }
	
}

