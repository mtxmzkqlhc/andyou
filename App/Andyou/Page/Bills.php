<?php
/**
 * 订单管理管理
 *
 */
class  Andyou_Page_Bills  extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Bills';
        $output->permission = array(0,1);//指定权限
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}

    /**
     * 获得数据列表
     */
	public function doDefault(ZOL_Request $input, ZOL_Response $output){
		$wArr     = array();#搜索字段
		$whereSql = "";
		$page = (int)$input->get('page')<1?1:(int)$input->get('page');
		$output->serbno = $wArr['bno'] = $input->get('bno');
        $output->serstaffid = $wArr['staffid'] = $input->get('staffid');
        $output->sermemberId = $wArr['memberId'] = $input->get('memberId');
        $output->sermemberPhone  = $input->get('memberPhone');
        
        //如果传入了会员电话
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
			'dbName'        => "Db_Andyou",  #数据库名
			'tblName'       => "bills",       #表名
			'cols'          => "*",            #列名
			'pageSize'      => $pageSize,      #每页数量
			'page'          => $page,          #当前页
			'pageUrl'       => $pageUrl,       #页面URL规则
			'whereSql'      => $whereSql,       #where条件
			'orderSql'      => $orderSql,
		    'iswrite'       =>  true,
		    'pageTpl'       =>  9,     #分页模板
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
     * 添加记录
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
		        'addItem'       =>  $Arr, #数据列
		        'dbName'        =>  'Db_Andyou',    #数据库名
		        'tblName'       =>  'bills',    #表名
		));
		/*backUrl*/
        $urlStr = $pageUrl ? $pageUrl : "?c={$output->ctlName}&t={$output->rnd}";
	    echo "<script>document.location='{$urlStr}';</script>";
		exit;
	}
    
    /**
     * 更新数据
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
	            'editItem'       =>  $Arr, #数据列
	            'dbName'         =>  'Db_Andyou',    #数据库名
	            'tblName'        =>   'bills',    #表名
	            'where'          =>   ' id='.$input->request('dataid'), #更新条件
	    ));
	    /*backUrl*/
        $urlStr = $pageUrl ? $pageUrl : "?c={$output->ctlName}&t={$output->rnd}";
	    echo "<script>document.location='{$urlStr}';</script>";
	    exit;
	 }
     //从Checkout删除订单
     public function doDelBill(ZOL_Request $input, ZOL_Response $output){
         $bid = (int)$input->get("bid");
         $sn  = $input->get("sn");
         if($sn == substr(md5($bid."HOOHAHA"), 0,10)){
             
            Helper_Dao::delItem(array(
                    'dbName'  => 'Db_Andyou',#数据库名
                    'tblName' => 'bills',#表名
                    'where'   => 'id='.$bid,#更新条件
            ));
            Helper_Dao::delItem(array(
                    'dbName'  => 'Db_Andyou',#数据库名
                    'tblName' => 'billsitem',#表名
                    'where'   => 'bid='.$bid,#更新条件
            ));
            echo "<script>alert('删除成功！');document.location='?c=Checkout';</script>";
         }
         exit;
     }
    /**
     * 删除数据
     */
	 public function doDelItem(ZOL_Request $input, ZOL_Response $output) {

		Helper_Dao::delItem(array(
                'dbName'=> 'Db_Andyou',#数据库名
                'tblName' => 'bills',#表名
                'where'=> 'id='.$input->post('dataid'),#更新条件
        ));;
        Helper_Dao::delItem(array(
                'dbName'  => 'Db_Andyou',#数据库名
                'tblName' => 'billsitem',#表名
                'where'   => 'bid='.$input->post('dataid'),#更新条件
        ));
		$pageUrl = $input->request('pageUrl');
		/*backUrl*/
        $urlStr = $pageUrl ? $pageUrl : "?c={$output->ctlName}&t={$output->rnd}";
	    echo "<script>document.location='{$urlStr}';</script>";
		exit;
	 }

	
    /**
     * ajax获得指定数据
     */
	 public function doAjaxData(ZOL_Request $input, ZOL_Response $output) {
		$id = (int)$input->get('id');
		$arr = Helper_Dao::getRows(array(
		        'dbName'   => "Db_Andyou", #数据库名
		        'tblName'  => "bills", #表名
		        'cols'     => "*", #列名
		        'whereSql' =>  ' and id='.$id
		));
		$data = ZOL_String::convToU8($arr);
		if(isset($data[0])){
		  echo json_encode($data[0]);
		}
		exit();
	 }
	
}

