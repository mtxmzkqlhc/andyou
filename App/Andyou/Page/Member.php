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
        $output->sercardno = $wArr['cardno'] = $input->get('cardno');
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
		$pageUrl  = "?c={$output->ctlName}&a={$output->actName}&page={$page}&name={$wArr['name']}&phone={$wArr['phone']}&cardno={$wArr['cardno']}&cateId={$wArr['cateId']}";
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
		
		//获得所有的员工
        $output->staffArr  = Helper_Staff::getStaffPairs();
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
        
        
        
        $price = $output->billInfo["price"];
        
        $Arr = array();        
		$Arr['name']    = $input->post('name');
        $Arr['phone']   = $input->post('phone');
        $Arr['cardno']  = $input->post('cardno');
        $Arr['cateId']  = $input->post('cateId');
        $Arr['byear']   = $input->post('byear');
        $Arr['bmonth']  = $input->post('bmonth');
        $Arr['bday']    = $input->post('bday');
        $Arr['addTm']   = SYSTEM_TIME;
        $Arr['score']   = $output->canGetScore;
        $Arr['balance'] = $input->post('balance');
        $Arr['remark']  = $input->post('remark');
        $Arr['introducer']  = $input->post('introducer');
        
        //查看该电话是否注册了
        $minfo = Helper_Member::getMemberInfo(array(
            'phone'           => $Arr['phone'], #ID
        ));
        
        if(!$minfo && $Arr['introducer']){ //如果是新添加用户，验证介绍人是否存在
            $pminfo = Helper_Member::getMemberInfo(array(
                'phone'           => $Arr['introducer'], #ID
            ));
            if(!$pminfo){ #如果没有查到这个会员，清空介绍人字段
                $Arr['introducer'] = false;
            }
        }
        
        //计算会员的消费总额 allsum
        $toAllSumPrice = round($price/100);
        $Arr['allsum'] = $toAllSumPrice;
        
        $db = Db_Andyou::instance();
        if($minfo){ //如果会员已经存在了，就将这个积分累加到现有用户上
            $memberId = $minfo["id"];
            //更新积分
            $sql = "update member set score = score + {$output->canGetScore},allsum=allsum + {$toAllSumPrice}  where id = {$memberId}";
            $db->query($sql);
        
        }else{
            
            $memberId = Helper_Dao::insertItem(array(
                'addItem'       =>  $Arr, #数据列
                'dbName'        =>  'Db_Andyou',    #数据库名
                'tblName'       =>  'member',    #表名
            ));
        }
        //更新账单，关联上用户ID
        $bid = $output->billInfo["id"];
        $bno = $output->billInfo["bno"];
        
        //记录会员积分历史        
        Helper_Member::addScoreLog(array(
            'memberId'         => $memberId, #ID
            'direction'        => 0, #1 减 0 加
            'score'            => $output->canGetScore, #积分
            'orgScore'         => $minfo ? $minfo['score'] : 0, #原始积分
            'bno'              => $bno, #订单号
            'remark'           => '消费', #
        ));
		
        
        $sql = "update bills set memberId = {$memberId} where id = {$bid} limit 1";
        $db->query($sql);
                
        $sql = "update billsitem set memberId = {$memberId} where bid = {$bid}";
        $db->query($sql);
        
        //给介绍人添加积分
        if(!emtpy($Arr['introducer'])){
            
        }
        
                
        $urlStr = "?c={$output->ctlName}";
	    echo "<script>document.location='?c=Member&phone={$Arr['phone']}';</script>";
		exit;
        
    }
    /**
     *  更新用户积分
     */
    public function doUpScore(ZOL_Request $input, ZOL_Response $output){
        $mid       = (int)$input->post("mid");
        $score     = (int)$input->post("score");
        $direction = (int)$input->post("direction");
        $remark    = $input->post("remark");
        $urlStr = "?c={$output->ctlName}";
        if($mid == 0){
             echo "<script>alert('Error!');document.location='{$urlStr}';</script>";
             exit;
        }
        if($score < 0)$score = -$score;
        
        #获得会员信息
        $minfo = Helper_Member::getMemberInfo(array("id"=>$mid));
        if(!$minfo){
             echo "<script>alert('Member Error!');document.location='{$urlStr}';</script>";
             exit;
        }
        if($direction == 1){//减分的时候判断用户是否有这么多
            if($minfo["score"] < $score){
             echo "<script>alert('Score Error!');document.location='{$urlStr}';</script>";
                exit;
            }
        }
        $db = Db_Andyou::instance();
        $op = $direction == 1 ? "-" : "+";
        $sql = "update member set score = score {$op} {$score} where id = {$mid}";
        $db->query($sql);
        
        $logItem = array(
            "memberId"   => $mid,
            "direction"  => $direction,
            "score"      => $score,
            "dateTm"     => SYSTEM_TIME,
            "adminer"    => $output->admin,
            "remark"     => $remark,
            "orgScore"   => $minfo["score"],
        );
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $logItem, #数据列
		        'dbName'        =>  'Db_Andyou',    #数据库名
		        'tblName'       =>  'log_scorechange',    #表名
		));
        echo "<script>document.location='{$urlStr}';</script>";
        exit;
        
    }
    
    /**
     *  更新用户会员卡余额
     */
    public function doUpCard(ZOL_Request $input, ZOL_Response $output){
        $mid       = (int)$input->post("mid");
        $card     = (int)$input->post("card");
        $direction = (int)$input->post("direction");
        $remark    = $input->post("remark");
        $staffid   = (int)$input->post('staffid');
        $urlStr = "?c={$output->ctlName}";
        if($mid == 0){
             echo "<script>alert('Error!');document.location='{$urlStr}';</script>";
             exit;
        }
        if($card < 0)$card = -$card;
        
        #获得会员信息
        $minfo = Helper_Member::getMemberInfo(array("id"=>$mid));
        if(!$minfo){
             echo "<script>alert('Member Error!');document.location='{$urlStr}';</script>";
             exit;
        }
        if($direction == 1){//减分的时候判断用户是否有这么多
            if($minfo["balance"] < $card){
             echo "<script>alert('Balance Error!');document.location='{$urlStr}';</script>";
                exit;
            }
        }
        $db = Db_Andyou::instance();
        $op = $direction == 1 ? "-" : "+";
        $sql = "update member set balance = balance {$op} {$card} where id = {$mid}";
        $db->query($sql);
        
        $logItem = array(
            "memberId"   => $mid,
            "direction"  => $direction,
            "card"       => $card,
            "dateTm"     => SYSTEM_TIME,
            "adminer"    => $output->admin,
            "remark"     => $remark,
            "orgCard"    => $minfo["balance"],
            "staffid"    => $staffid,
        );
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $logItem, #数据列
		        'dbName'        =>  'Db_Andyou',    #数据库名
		        'tblName'       =>  'log_cardchange',    #表名
		));
        echo "<script>document.location='{$urlStr}';</script>";
        exit;
        
    }
    /**
     * 添加记录
     */
	public function doAddItem(ZOL_Request $input, ZOL_Response $output){
	    $Arr = array();
        
		$Arr['name'] = $input->post('name');
        $Arr['cardno'] = $input->post('cardno');
        $Arr['phone'] = $input->post('phone');
        $Arr['cateId'] = $input->post('cateId');
        $Arr['byear'] = $input->post('byear');
        $Arr['bmonth'] = $input->post('bmonth');
        $Arr['bday'] = $input->post('bday');
        $Arr['addTm'] = SYSTEM_TIME;
        $Arr['score'] = $input->post('score');
        $Arr['balance'] = $input->post('balance');
        $Arr['remark'] = $input->post('remark');
        $Arr['introducer'] = $input->post('introducer');
         //查看该电话是否注册了
        $minfo = Helper_Member::getMemberInfo(array(
            'phone'           => $Arr['phone'], #ID
        ));
        $urlStr =  "?c={$output->ctlName}&t={$output->rnd}";
        if($minfo){
            echo "<script>alert('该手机号已经存在了！');document.location='?c=Member&phone={$Arr['phone']}';</script>";
            exit;
        }
		$pageUrl = $input->request('pageUrl');
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $Arr, #数据列
		        'dbName'        =>  'Db_Andyou',    #数据库名
		        'tblName'       =>  'member',    #表名
		));
		/*backUrl*/
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
        $input->request('cardno')?$Arr['cardno'] = $input->request('cardno'):'';
        $input->request('cateId')?$Arr['cateId'] = $input->request('cateId'):'';
        $input->request('byear')?$Arr['byear'] = $input->request('byear'):'';
        $input->request('bmonth')?$Arr['bmonth'] = $input->request('bmonth'):'';
        $input->request('bday')?$Arr['bday'] = $input->request('bday'):'';
        $input->request('score')?$Arr['score'] = $input->request('score'):'';
        $input->request('balance')?$Arr['balance'] = $input->request('balance'):'';
        $input->request('remark')?$Arr['remark'] = $input->request('remark'):'';
        $input->request('introducer')?$Arr['introducer'] = $input->request('introducer'):'';
        
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

