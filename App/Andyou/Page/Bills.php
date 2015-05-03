<?php
/**
 * �����������
 *
 */
class  Andyou_Page_Bills  extends Andyou_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Bills';
        $output->permission = array(0,1);//ָ��Ȩ��
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
		$output->serbno = $wArr['bno'] = $input->get('bno');
        $output->serstaffid = $wArr['staffid'] = $input->get('staffid');
        $output->sermemberId = $wArr['memberId'] = $input->get('memberId');
        $output->sermemberPhone  = $input->get('memberPhone');
        
        //��������˻�Ա�绰
        if($output->sermemberPhone){
            $memInfo = Helper_Member::getMemberInfo(array('phone'=>$output->sermemberPhone));
            if($memInfo){
                $output->sermemberId = $wArr['memberId'] = (int)$memInfo["id"];
            }
        }
        
        
	    if(!empty ($wArr)){
		    foreach($wArr as $k=>$v){
		        if(gettype($v) == 'string'){
                     $whereSql .= !empty($v)?' AND '.$k.' like binary "%'.$v.'%" ':'';
                  }else{
                     $whereSql .= !empty($v)?' AND '.$k.'='.$v:'';
                }    
		    }
		}
		$pageUrl  = "?c={$output->ctlName}&a={$output->actName}&page={$page}&bno={$wArr['bno']}&staffid={$wArr['staffid']}&memberPhone=$output->sermemberPhone";
		$pageSize = 30;
		$orderSql = "order by id desc";
		
		$data = Helper_Dao::getList(array(
			'dbName'        => "Db_Andyou",  #���ݿ���
			'tblName'       => "bills",       #����
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
		$output->setTemplate('Bills');
	}
	
    /**
     * ��Ӽ�¼
     */
	public function doAddItem(ZOL_Request $input, ZOL_Response $output){
	                        
		$Arr['bno'] = $input->post('bno');
        $Arr['useScore'] = $input->post('useScore');
        $Arr['useCard'] = $input->post('useCard');
        $Arr['price'] = $input->post('price');
        $Arr['discount'] = $input->post('discount');
        $Arr['staffid'] = $input->post('staffid');
        $Arr['staffName'] = $input->post('staffName');
        $Arr['tm'] = SYSTEM_TIME;
        $Arr['memberId'] = $input->post('memberId');
        
		$pageUrl = $input->request('pageUrl');
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $Arr, #������
		        'dbName'        =>  'Db_Andyou',    #���ݿ���
		        'tblName'       =>  'bills',    #����
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
	    
	    $input->request('bno')?$Arr['bno'] = $input->request('bno'):'';
        $input->request('useScore')?$Arr['useScore'] = $input->request('useScore'):'';
        $input->request('useCard')?$Arr['useCard'] = $input->request('useCard'):'';
        $input->request('price')?$Arr['price'] = $input->request('price'):'';
        $input->request('discount')?$Arr['discount'] = $input->request('discount'):'';
        $input->request('staffid')?$Arr['staffid'] = $input->request('staffid'):'';
        $input->request('staffName')?$Arr['staffName'] = $input->request('staffName'):'';
        $input->request('memberId')?$Arr['memberId'] = $input->request('memberId'):'';
        
	    $pageUrl = $input->request('pageUrl');
	    $data = Helper_Dao::updateItem(array(
	            'editItem'       =>  $Arr, #������
	            'dbName'         =>  'Db_Andyou',    #���ݿ���
	            'tblName'        =>   'bills',    #����
	            'where'          =>   ' id='.$input->request('dataid'), #��������
	    ));
	    /*backUrl*/
        $urlStr = $pageUrl ? $pageUrl : "?c={$output->ctlName}&t={$output->rnd}";
	    echo "<script>document.location='{$urlStr}';</script>";
	    exit;
	 }
     //��Checkoutɾ������
     public function doDelBill(ZOL_Request $input, ZOL_Response $output){
         $bid = (int)$input->get("bid");
         $sn  = $input->get("sn");
         if($sn == substr(md5($bid."HOOHAHA"), 0,10)){
             
            Helper_Dao::delItem(array(
                    'dbName'  => 'Db_Andyou',#���ݿ���
                    'tblName' => 'bills',#����
                    'where'   => 'id='.$bid,#��������
            ));
            Helper_Dao::delItem(array(
                    'dbName'  => 'Db_Andyou',#���ݿ���
                    'tblName' => 'billsitem',#����
                    'where'   => 'bid='.$bid,#��������
            ));
            echo "<script>alert('ɾ���ɹ���');document.location='?c=Checkout';</script>";
         }
         exit;
     }
    /**
     * ɾ������
     */
	 public function doDelItem(ZOL_Request $input, ZOL_Response $output) {

		Helper_Dao::delItem(array(
                'dbName'=> 'Db_Andyou',#���ݿ���
                'tblName' => 'bills',#����
                'where'=> 'id='.$input->post('dataid'),#��������
        ));;
        Helper_Dao::delItem(array(
                'dbName'  => 'Db_Andyou',#���ݿ���
                'tblName' => 'billsitem',#����
                'where'   => 'bid='.$input->post('dataid'),#��������
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
		        'tblName'  => "bills", #����
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

