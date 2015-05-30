<?php
/**
 * 入库记录管理
 *
 */
class  Andyou_Page_LogInStorage  extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'LogInStorage';
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
			'dbName'        => "Db_Andyou",  #数据库名
			'tblName'       => "log_productinstorage",       #表名
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
		//获得所有的产品分类
        $output->proCateArr = Helper_Product::getProductCatePairs();
		$output->setTemplate('LogInStorage');
	}
	
    /**
     * 添加记录
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
		        'addItem'       =>  $Arr, #数据列
		        'dbName'        =>  'Db_Andyou',    #数据库名
		        'tblName'       =>  'log_productinstorage',    #表名
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
	            'editItem'       =>  $Arr, #数据列
	            'dbName'         =>  'Db_Andyou',    #数据库名
	            'tblName'        =>   'log_productinstorage',    #表名
	            'where'          =>   ' id='.$input->request('dataid'), #更新条件
	    ));
	    /*backUrl*/
        $urlStr = $pageUrl ? $pageUrl : "?c={$output->ctlName}&t={$output->rnd}";
	    echo "<script>document.location='{$urlStr}';</script>";
	    exit;
	 }
    /**
     * 删除数据
     */
	 public function doDelItem(ZOL_Request $input, ZOL_Response $output) {

		Helper_Dao::delItem(array(
                'dbName'=> 'Db_Andyou',#数据库名
                'tblName' => 'log_productinstorage',#表名
                'where'=> 'id='.$input->post('dataid'),#更新条件
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
		        'tblName'  => "log_productinstorage", #表名
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

