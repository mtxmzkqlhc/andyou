<?php
/**
 * ������Ʒ�������
 *
 */
class  Andyou_Page_MemeberOtherPro  extends Andyou_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'MemeberOtherPro';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}

    /**
     * ��������б�
     */
	public function doDefault(ZOL_Request $input, ZOL_Response $output){
		$wArr     = array();#�����ֶ�
		$whereSql = "";
		$page = (int)$input->get('page')<1?1:(int)$input->get('page');
        
		$member = $input->get('member');
        if($member){
            $memInfo = Helper_Member::getMemberInfo(array('phoneOrCardno'=>$member));
            if($memInfo){
                $output->memberId = $memInfo["id"];
            }
        }
	  
        if($output->serctype) $whereSql .= " AND ctype = " . $output->serctype;
        if($output->memberId) $whereSql .= " AND memberId = " . $output->memberId;
        
        
		$pageUrl  = "?c={$output->ctlName}&a={$output->actName}&page={$page}&memberId={$output->memberId}&ctype={$wArr['ctype']}";
		$pageSize = 30;
		$orderSql = "order by id desc";
		
		$data = Helper_Dao::getList(array(
			'dbName'        => "Db_Andyou",  #���ݿ���
			'tblName'       => "memeberotherpro",       #����
			'cols'          => "*",            #����
			'pageSize'      => $pageSize,      #ÿҳ����
			'page'          => $page,          #��ǰҳ
			'pageUrl'       => $pageUrl,       #ҳ��URL����
			'whereSql'      => $whereSql,       #where����
			'orderSql'      => $orderSql,
		    'iswrite'       =>  true,
		    'pageTpl'       =>  9,     #��ҳģ��
		    #'debug'        =>1
		));
		
		if($data){
		    $output->pageBar = $data['pageBar'];
		    $output->allCnt  = $data['allCnt'];
		    $output->data    = $data['data'];
			$output->pageUrl= $pageUrl;
		}
		$output->member = $member;
        $output->proCtypeArr = ZOL_Config::get("GLOBAL","PRO_CTYPE");
		$output->setTemplate('MemeberOtherPro');
	}
	
    /**
     * ��Ӽ�¼
     */
	public function doAddItem(ZOL_Request $input, ZOL_Response $output){
	    $Arr = array();                     
		$Arr['memberId'] = $input->post('memberId');
        $Arr['proId'] = $input->post('proId');
        $Arr['name'] = $input->post('name');
        $Arr['proName'] = $input->post('proName');
        $Arr['num'] = SYSTEM_TIME;
        
		$pageUrl = $input->request('pageUrl');
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $Arr, #������
		        'dbName'        =>  'Db_Andyou',    #���ݿ���
		        'tblName'       =>  'memeberotherpro',    #����
		));
		/*backUrl*/
        $urlStr = $pageUrl ? $pageUrl : "?c={$output->ctlName}&t={$output->rnd}";
	    echo "<script>document.location='{$urlStr}';</script>";
		exit;
	}
    
    /**
     * ��������
     */
	 public function doUpItem(ZOL_Request $input, ZOL_Response $output){
	    $Arr = array();
	    
	    $input->request('memberId')?$Arr['memberId'] = $input->request('memberId'):'';
        $input->request('proId')?$Arr['proId'] = $input->request('proId'):'';
        $input->request('name')?$Arr['name'] = $input->request('name'):'';
        $input->request('proName')?$Arr['proName'] = $input->request('proName'):'';
        $input->request('num')?$Arr['num'] = $input->request('num'):'';
        $input->request('buytm')?$Arr['buytm'] = $input->request('buytm'):'';
        $input->request('ctype')?$Arr['ctype'] = $input->request('ctype'):'';
        
	    $pageUrl = $input->request('pageUrl');
	    $data = Helper_Dao::updateItem(array(
	            'editItem'       =>  $Arr, #������
	            'dbName'         =>  'Db_Andyou',    #���ݿ���
	            'tblName'        =>   'memeberotherpro',    #����
	            'where'          =>   ' id='.$input->request('dataid'), #��������
	    ));
	    /*backUrl*/
        $urlStr = $pageUrl ? $pageUrl : "?c={$output->ctlName}&t={$output->rnd}";
	    echo "<script>document.location='{$urlStr}';</script>";
	    exit;
	 }
    /**
     * ɾ������
     */
	 public function doDelItem(ZOL_Request $input, ZOL_Response $output) {

		Helper_Dao::delItem(array(
                'dbName'=> 'Db_Andyou',#���ݿ���
                'tblName' => 'memeberotherpro',#����
                'where'=> 'id='.$input->post('dataid'),#��������
        ));
		$pageUrl = $input->request('pageUrl');
		/*backUrl*/
        $urlStr = $pageUrl ? $pageUrl : "?c={$output->ctlName}&t={$output->rnd}";
	    echo "<script>document.location='{$urlStr}';</script>";
		exit;
	 }

	
    /**
     * ajax���ָ������
     */
	 public function doAjaxData(ZOL_Request $input, ZOL_Response $output) {
		$id = (int)$input->get('id');
		$arr = Helper_Dao::getRows(array(
		        'dbName'   => "Db_Andyou", #���ݿ���
		        'tblName'  => "memeberotherpro", #����
		        'cols'     => "*", #����
		        'whereSql' =>  ' and id='.$id
		));
		$data = ZOL_String::convToU8($arr);
		if(isset($data[0])){
		  echo json_encode($data[0]);
		}
		exit();
	 }
	
}

