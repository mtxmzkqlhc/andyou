<?php
/**
 * ����¼����
 *
 */
class  Andyou_Page_LogInStorage  extends Andyou_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'LogInStorage';
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
		$output->serproId = $wArr['proId'] = $input->get('proId');
        $output->sercateId = $wArr['cateId'] = $input->get('cateId');
        $output->sername = $wArr['name'] = $input->get('name');
        $output->sercode = $wArr['code'] = $input->get('code');
        $output->startTime = $wArr['startTime'] = $input->get('startTime');
        $output->endTime = $wArr['endTime'] = $input->get('endTime');
        
        if($output->sercateId)$whereSql .= ' AND cateId ='.$output->sercateId;
        if($output->sercode)$whereSql .= ' AND cateId =\''.$output->sercode.'\'';
        if($output->sername)$whereSql .= ' AND name like \'%'.$output->sername.'%\'';
        if($output->startTime){
            $stm = strtotime($output->startTime . "00:00:00");
            $whereSql .= " AND dateTm > {$stm}";            
        }
        if($output->endTime){
            $stm = strtotime($output->endTime . "23:59:59");
            $whereSql .= " AND dateTm < {$stm}";            
        }
        
        
		$pageUrl  = "?c={$output->ctlName}&a={$output->actName}&page={$page}&proId={$wArr['proId']}&cateId={$wArr['cateId']}&name={$wArr['name']}&code={$wArr['code']}&startTime={$wArr['startTime']}&endTime={$wArr['endTime']}";
		$pageSize = 30;
		$orderSql = "order by id desc";
		
		$data = Helper_Dao::getList(array(
			'dbName'        => "Db_Andyou",  #���ݿ���
			'tblName'       => "log_productinstorage",       #����
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
		//������еĲ�Ʒ����
        $output->proCateArr = Helper_Product::getProductCatePairs();
		$output->setTemplate('LogInStorage');
	}
	
    /**
     * ��Ӽ�¼
     */
	public function doAddItem(ZOL_Request $input, ZOL_Response $output){
	    $Arr = array();                     
		$Arr['proId'] = $input->post('proId');
        $Arr['adminer'] = $input->post('adminer');
        $Arr['staffid'] = $input->post('staffid');
        $Arr['dateTm'] = $input->post('dateTm');
        $Arr['orgNum'] = SYSTEM_TIME;
        
		$pageUrl = $input->request('pageUrl');
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $Arr, #������
		        'dbName'        =>  'Db_Andyou',    #���ݿ���
		        'tblName'       =>  'log_productinstorage',    #����
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
	    
	    $input->request('proId')?$Arr['proId'] = $input->request('proId'):'';
        $input->request('adminer')?$Arr['adminer'] = $input->request('adminer'):'';
        $input->request('staffid')?$Arr['staffid'] = $input->request('staffid'):'';
        $input->request('dateTm')?$Arr['dateTm'] = $input->request('dateTm'):'';
        $input->request('orgNum')?$Arr['orgNum'] = $input->request('orgNum'):'';
        $input->request('addNum')?$Arr['addNum'] = $input->request('addNum'):'';
        $input->request('cateId')?$Arr['cateId'] = $input->request('cateId'):'';
        $input->request('name')?$Arr['name'] = $input->request('name'):'';
        
	    $pageUrl = $input->request('pageUrl');
	    $data = Helper_Dao::updateItem(array(
	            'editItem'       =>  $Arr, #������
	            'dbName'         =>  'Db_Andyou',    #���ݿ���
	            'tblName'        =>   'log_productinstorage',    #����
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
                'tblName' => 'log_productinstorage',#����
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
		        'tblName'  => "log_productinstorage", #����
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

