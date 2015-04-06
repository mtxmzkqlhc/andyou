<?php
/**
 * ������ϸ�������
 *
 */
class  Andyou_Page_BillsItem  extends Andyou_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'BillsItem';
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
		$output->serbid = $wArr['bid'] = $input->get('bid');
        $output->serbno = $wArr['bno'] = $input->get('bno');
        $output->serproId = $wArr['proId'] = $input->get('proId');
        $output->serstaffid = $wArr['staffid'] = $input->get('staffid');
        $output->sermemberId = $wArr['memberId'] = $input->get('memberId');
        
	    if(!empty ($wArr)){
		    foreach($wArr as $k=>$v){
		        if(gettype($v) == 'string'){
                     $whereSql .= !empty($v)?' AND '.$k.' like binary "%'.$v.'%" ':'';
                  }else{
                     $whereSql .= !empty($v)?' AND '.$k.'='.$v:'';
                }    
		    }
		}
		$pageUrl  = "?c={$output->ctlName}&a={$output->actName}&page={$page}&bid={$wArr['bid']}&bno={$wArr['bno']}&proId={$wArr['proId']}&staffid={$wArr['staffid']}&memberId={$wArr['memberId']}";
		$pageSize = 30;
		$orderSql = "order by id desc";
		
		$data = Helper_Dao::getList(array(
			'dbName'        => "Db_Andyou",  #���ݿ���
			'tblName'       => "billsItem",       #����
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
		
        $output->staffInfo = Helper_Staff::getStaffPairs();
		$output->setTemplate('BillsItem');
	}
	
    /**
     * ��Ӽ�¼
     */
	public function doAddItem(ZOL_Request $input, ZOL_Response $output){
	                        
		$Arr['bid'] = $input->post('bid');
        $Arr['bno'] = $input->post('bno');
        $Arr['proId'] = $input->post('proId');
        $Arr['num'] = $input->post('num');
        $Arr['discount'] = $input->post('discount');
        $Arr['price'] = $input->post('price');
        $Arr['staffid'] = $input->post('staffid');
        $Arr['memberId'] = $input->post('memberId');
        $Arr['tm'] = SYSTEM_TIME;
        
		$pageUrl = $input->request('pageUrl');
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $Arr, #������
		        'dbName'        =>  'Db_Andyou',    #���ݿ���
		        'tblName'       =>  'billsItem',    #����
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
	    
	    $input->request('bid')?$Arr['bid'] = $input->request('bid'):'';
        $input->request('bno')?$Arr['bno'] = $input->request('bno'):'';
        $input->request('proId')?$Arr['proId'] = $input->request('proId'):'';
        $input->request('num')?$Arr['num'] = $input->request('num'):'';
        $input->request('discount')?$Arr['discount'] = $input->request('discount'):'';
        $input->request('price')?$Arr['price'] = $input->request('price'):'';
        $input->request('staffid')?$Arr['staffid'] = $input->request('staffid'):'';
        $input->request('memberId')?$Arr['memberId'] = $input->request('memberId'):'';
        
	    $pageUrl = $input->request('pageUrl');
	    $data = Helper_Dao::updateItem(array(
	            'editItem'       =>  $Arr, #������
	            'dbName'         =>  'Db_Andyou',    #���ݿ���
	            'tblName'        =>   'billsItem',    #����
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
                'tblName' => 'billsItem',#����
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
		        'tblName'  => "billsItem", #����
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

