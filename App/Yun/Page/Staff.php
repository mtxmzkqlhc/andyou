<?php
/**
 * 员工管理管理
 *
 */
error_reporting(E_ALL);
ini_set("display_errors",1);
class  Yun_Page_Staff  extends Yun_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Staff';
        $output->permission = array(1);//指定权限
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
		$output->sername = $wArr['name'] = $input->get('name');
        $output->sercateId = $wArr['cateId'] = $input->get('cateId');
        
	    if(!empty ($wArr)){
		    foreach($wArr as $k=>$v){
		        if(gettype($v) == 'string'){
                     $whereSql .= !empty($v)?' AND '.$k.' like binary "%'.$v.'%" ':'';
                  }else{
                     $whereSql .= !empty($v)?' AND '.$k.'='.$v:'';
                }    
		    }
		}
		$pageUrl  = "?c={$output->ctlName}&a={$output->actName}&page={$page}&name={$wArr['name']}&cateId={$wArr['cateId']}";
		$pageSize = 30;
		$orderSql = "order by id desc";
		
		$data = Helper_Dao::getList(array(
			'dbName'        => "Db_AndyouYun",  #数据库名
			'tblName'       => "staff",       #表名
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
		
        $output->staffCate = Helper_Yun_Staff::getStaffCatePairs();
		$output->setTemplate('Staff');
	}
	
    /**
     * 添加记录
     */
	public function doAddItem(ZOL_Request $input, ZOL_Response $output){
	                        
		$Arr['name'] = $input->post('name');
        $Arr['inDate'] = $input->post('inDate');
        $Arr['byear'] = (int)$input->post('byear');
        $Arr['bmonth'] = (int)$input->post('bmonth');
        $Arr['bday'] = (int)$input->post('bday');
        $Arr['ryear'] = (int)$input->post('ryear');
        $Arr['rmonth'] = (int)$input->post('rmonth');
        $Arr['rday'] = (int)$input->post('rday');
        $Arr['cateId'] = $input->post('cateId');
        $Arr['salary'] = $input->post('salary');
        $Arr['percentage'] = $input->post('percentage');
        
		$pageUrl = $input->request('pageUrl');
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $Arr, #数据列
		        'dbName'        =>  'Db_AndyouYun',    #数据库名
		        'tblName'       =>  'staff',    #表名
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
	    
	    $input->request('name')?$Arr['name'] = $input->request('name'):'';
        $input->request('inDate')?$Arr['inDate'] = $input->request('inDate'):'';
        $input->request('byear')?$Arr['byear'] = (int)$input->request('byear'):'';
        $input->request('bmonth')?$Arr['bmonth'] = (int)$input->request('bmonth'):'';
        $input->request('bday')?$Arr['bday'] = (int)$input->request('bday'):'';
        $input->request('ryear')?$Arr['ryear'] = (int)$input->request('ryear'):'';
        $input->request('rmonth')?$Arr['rmonth'] = (int)$input->request('rmonth'):'';
        $input->request('rday')?$Arr['rday'] = (int)$input->request('rday'):'';
        $input->request('cateId')?$Arr['cateId'] = $input->request('cateId'):'';
        $input->request('salary')?$Arr['salary'] = $input->request('salary'):'';
        $input->request('percentage')?$Arr['percentage'] = $input->request('percentage'):'';
        
	    $pageUrl = $input->request('pageUrl');
	    $data = Helper_Dao::updateItem(array(
	            'editItem'       =>  $Arr, #数据列
	            'dbName'         =>  'Db_AndyouYun',    #数据库名
	            'tblName'        =>   'staff',    #表名
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
                'dbName'=> 'Db_AndyouYun',#数据库名
                'tblName' => 'staff',#表名
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
		        'dbName'   => "Db_AndyouYun", #数据库名
		        'tblName'  => "staff", #表名
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

