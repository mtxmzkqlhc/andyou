<?php
/**
 * 会员管理管理
 *
 */
class  Andyou_Page_Member extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Member';		
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
        $output->serphone = $wArr['phone'] = $input->get('phone');
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
		$pageUrl  = "?c={$output->ctlName}&a={$output->actName}&page={$page}&name={$wArr['name']}&phone={$wArr['phone']}&cateId={$wArr['cateId']}";
		$pageSize = 30;
		$orderSql = "order by id desc";
		
		$data = Helper_Dao::getList(array(
			'dbName'        => "Db_Andyou",  #数据库名
			'tblName'       => "member",       #表名
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
        
        $output->memberCate = Helper_Member::getMemberCatePairs();
		
		$output->setTemplate('Member');
	}
	
    /**
     *  验证是否可以从订单添加会员
     */
    private function checkFromBill(ZOL_Request $input, ZOL_Response $output){
        header("Content-type: text/html; charset=GBK");
        //获得订单信息
        $billInfo = Helper_Bill::getBillsInfo(array(
            'id'              => $output->bid, #ID
            'bno'             => $output->bno, #单号
        ));
        if(!$billInfo){
            echo "Bill Not Found!";
            exit;
        }
        if($billInfo["memberId"]){
            echo "订单已经关联会员！";
            exit;
        }
        $output->billInfo = $billInfo;
        //可兑换积分
        
        $sysOptions = Helper_Option::getAllOptions();        
        $scoreRatio = !empty($sysOptions["ScoreRatio"]) ? $sysOptions["ScoreRatio"]["value"] : 0;
        
        $price = $output->billInfo["price"];
        $output->canGetScore = $scoreRatio ? round($price*$scoreRatio/100) : 0;
    }
    /**
     * 从订单添加会员
     */
	public function doToAddUserFromBill(ZOL_Request $input, ZOL_Response $output){
        $output->bno = $input->get("bno");
        $output->bid = $input->get("bid");
        $this->checkFromBill($input,$output);//验证订单
        $output->memberCate = Helper_Member::getMemberCatePairs();
		$output->setTemplate('MemberAddFromBill');
        
    }
    /**
     * 从订单添加会员模板
     */
    public function doAddUserFromBill(ZOL_Request $input, ZOL_Response $output){
        $output->bno = $input->post("bno");
        $output->bid = $input->post("bid");
        
        $this->checkFromBill($input,$output);//验证订单
        ;
        
        
        $price = $output->billInfo["price"];
        
        $Arr = array();        
		$Arr['name']    = $input->post('name');
        $Arr['phone']   = $input->post('phone');
        $Arr['cateId']  = $input->post('cateId');
        $Arr['byear']   = $input->post('byear');
        $Arr['bmonth']  = $input->post('bmonth');
        $Arr['bday']    = $input->post('bday');
        $Arr['addTm']   = SYSTEM_TIME;
        $Arr['score']   = $output->canGetScore;
        $Arr['balance'] = $input->post('balance');
        $Arr['remark']  = $input->post('remark');
        
        //查看该电话是否注册了
        $minfo = Helper_Member::getMemberInfo(array(
            'phone'           => $Arr['phone'], #ID
        ));
        if($minfo){
            echo "该手机号，已经在使用了！";
            exit;
        }
        
		$memberId = Helper_Dao::insertItem(array(
            'addItem'       =>  $Arr, #数据列
            'dbName'        =>  'Db_Andyou',    #数据库名
            'tblName'       =>  'member',    #表名
		));
		
        //更新账单，关联上用户ID
        $db = Db_Andyou::instance();
        $bid = $output->billInfo["id"];
        $bno = $output->billInfo["bno"];
        
        $sql = "update bills set memberId = {$memberId} where id = {$bid} limit 1";
        $db->query($sql);
                
        $sql = "update billsitem set memberId = {$memberId} where id = {$bid}";
        $db->query($sql);
                
        $urlStr = "?c={$output->ctlName}";
	    echo "<script>document.location='{$urlStr}';</script>";
		exit;
        
    }
        
    /**
     * 添加记录
     */
	public function doAddItem(ZOL_Request $input, ZOL_Response $output){
	    $Arr = array();
        
		$Arr['name'] = $input->post('name');
        $Arr['phone'] = $input->post('phone');
        $Arr['cateId'] = $input->post('cateId');
        $Arr['byear'] = $input->post('byear');
        $Arr['bmonth'] = $input->post('bmonth');
        $Arr['bday'] = $input->post('bday');
        $Arr['addTm'] = SYSTEM_TIME;
        $Arr['score'] = $input->post('score');
        $Arr['balance'] = $input->post('balance');
        $Arr['remark'] = $input->post('remark');
        
		$pageUrl = $input->request('pageUrl');
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $Arr, #数据列
		        'dbName'        =>  'Db_Andyou',    #数据库名
		        'tblName'       =>  'member',    #表名
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
        $input->request('phone')?$Arr['phone'] = $input->request('phone'):'';
        $input->request('cateId')?$Arr['cateId'] = $input->request('cateId'):'';
        $input->request('byear')?$Arr['byear'] = $input->request('byear'):'';
        $input->request('bmonth')?$Arr['bmonth'] = $input->request('bmonth'):'';
        $input->request('bday')?$Arr['bday'] = $input->request('bday'):'';
        $input->request('score')?$Arr['score'] = $input->request('score'):'';
        $input->request('balance')?$Arr['balance'] = $input->request('balance'):'';
        $input->request('remark')?$Arr['remark'] = $input->request('remark'):'';
        
	    $pageUrl = $input->request('pageUrl');
	    $data = Helper_Dao::updateItem(array(
	            'editItem'       =>  $Arr, #数据列
	            'dbName'         =>  'Db_Andyou',    #数据库名
	            'tblName'        =>   'member',    #表名
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
                'tblName' => 'member',#表名
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
		        'tblName'  => "member", #表名
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

