<?php
/**
 * ������������
 *
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);
class  Yun_Page_Bills  extends Yun_Page_Abstract {
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
        $output->isAddUser = (int)$input->get("isAddUser"); 
		$whereSql = "";
		$page = (int)$input->get('page')<1?1:(int)$input->get('page');
		$output->serbno = $wArr['bno'] = $input->get('bno');
        $output->serstaffid = $wArr['staffid'] = $input->get('staffid');
        $output->sermemberId = $wArr['memberId'] = $input->get('memberId');
        $output->sermemberPhone  = $input->get('memberPhone');
        $output->isBuyScore  = $input->get('isBuyScore');
        $output->hasChangePrice   = (int)$input->get("hasChangePrice");//����Ա�޸Ĺ��Ķ���
        
        //��������˻�Ա�绰
        if($output->sermemberPhone){
            $wArr['phone'] = $output->sermemberPhone;
            
        }
        if($output->isBuyScore == 1)$whereSql .= " and  isBuyScore = 1";
        if($output->isBuyScore == 2)$whereSql .= " and  isBuyScore = 0";
        
        //����Ա�޸��˼۸�
        if($output->hasChangePrice)$whereSql .= " and  priceTrue > 0 ";
        
	    if(!empty ($wArr)){
		    foreach($wArr as $k=>$v){
		        if(gettype($v) == 'string'){
                    if($k == "phone"){
                        $whereSql .= !empty($v)?' AND '.$k.'='.$v:'';
                    }else{
                         $whereSql .= !empty($v)?' AND '.$k.' like binary "%'.$v.'%" ':'';
                    }
                  }else{
                     $whereSql .= !empty($v)?' AND '.$k.'='.$v:'';
                }    
		    }
		}
        if($output->isAddUser){
            $whereSql .= " AND memberId = 0 ";
        }
		$pageUrl  = "?c={$output->ctlName}&a={$output->actName}&page={$page}&bno={$wArr['bno']}&isAddUser={$output->isAddUser}&staffid={$wArr['staffid']}&memberPhone=$output->sermemberPhone&hasChangePrice=$output->hasChangePrice";
		$pageSize = 30;
		$orderSql = "order by id desc";
		
		$data = Helper_Dao::getList(array(
			'dbName'        => "Db_AndyouYun",  #���ݿ���
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
		
        $output->staffInfo = Helper_Yun_Staff::getSiteStaffPairs();
		$output->setTemplate('Bills');
	}
	
    /**
     * ���Ӽ�¼
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
		        'dbName'        =>  'Db_AndyouYun',    #���ݿ���
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
	            'dbName'         =>  'Db_AndyouYun',    #���ݿ���
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
                    'dbName'  => 'Db_AndyouYun',#���ݿ���
                    'tblName' => 'bills',#����
                    'where'   => 'id='.$bid,#��������
            ));
            Helper_Dao::delItem(array(
                    'dbName'  => 'Db_AndyouYun',#���ݿ���
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
                'dbName'=> 'Db_AndyouYun',#���ݿ���
                'tblName' => 'bills',#����
                'where'=> 'id='.$input->post('dataid'),#��������
        ));;
        Helper_Dao::delItem(array(
                'dbName'  => 'Db_AndyouYun',#���ݿ���
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
		        'dbName'   => "Db_AndyouYun", #���ݿ���
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
