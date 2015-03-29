<?php
/**
 * 商品管理管理
 *
 */
class  Andyou_Page_Product  extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Product';
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
        $output->sercode = $wArr['code'] = $input->get('code');
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
		$pageUrl  = "?c={$output->ctlName}&a={$output->actName}&page={$page}&name={$wArr['name']}&code={$wArr['code']}&cateId={$wArr['cateId']}";
		$pageSize = 30;
		$orderSql = "order by id desc";
		
		$data = Helper_Dao::getList(array(
			'dbName'        => "Db_Andyou",  #数据库名
			'tblName'       => "product",       #表名
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
		
        $output->cateInfo = Helper_Product::getProductCatePairs();
        
		$output->setTemplate('Product');
	}
	
    /**
     * 添加记录
     */
	public function doAddItem(ZOL_Request $input, ZOL_Response $output){
	                        
		$Arr['name'] = $input->post('name');
        $Arr['code'] = $input->post('code');
        $Arr['cateId'] = $input->post('cateId');
        $Arr['price'] = $input->post('price');
        $Arr['inPrice'] = $input->post('inPrice');
        $Arr['stock'] = $input->post('stock');
        $Arr['score'] = $input->post('score');
        $Arr['discut'] = $input->post('discut');
        $Arr['addtm'] = SYSTEM_TIME;
        
		$pageUrl = $input->request('pageUrl');
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $Arr, #数据列
		        'dbName'        =>  'Db_Andyou',    #数据库名
		        'tblName'       =>  'product',    #表名
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
        $input->request('code')?$Arr['code'] = $input->request('code'):'';
        $input->request('cateId')?$Arr['cateId'] = $input->request('cateId'):'';
        $input->request('price')?$Arr['price'] = $input->request('price'):'';
        $input->request('inPrice')?$Arr['inPrice'] = $input->request('inPrice'):'';
        $input->request('stock')?$Arr['stock'] = $input->request('stock'):'';
        $input->request('score')?$Arr['score'] = $input->request('score'):'';
        $input->request('discut')?$Arr['discut'] = $input->request('discut'):'';
        
	    $pageUrl = $input->request('pageUrl');
	    $data = Helper_Dao::updateItem(array(
	            'editItem'       =>  $Arr, #数据列
	            'dbName'         =>  'Db_Andyou',    #数据库名
	            'tblName'        =>   'product',    #表名
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
                'tblName' => 'product',#表名
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
		        'tblName'  => "product", #表名
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

