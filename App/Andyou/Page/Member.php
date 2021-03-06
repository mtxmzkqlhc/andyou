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
        $output->andCard = (int)$input->get("andCard");//是否添加账号并进行充值
        if(!$output->andCard){
            $this->checkFromBill($input,$output);//验证订单
        }
        $output->memberCate = Helper_Member::getMemberCatePairs();
        
		//获得所有的员工
        $output->staffArr  = Helper_Staff::getStaffPairs();
		$output->setTemplate('MemberAddFromBill');
        
    }
    
    /**
     *  添加会员并充值
     */
    public function doAddUserAndCard(ZOL_Request $input, ZOL_Response $output){
        $output->andCard = (int)$input->get("andCard");//是否添加账号并进行充值
        
        $Arr = array();        
		$Arr['name']    = $input->post('name');
        $Arr['phone']   = $input->post('phone');
        $Arr['cardno']  = $input->post('cardno');
        $Arr['cateId']  = $input->post('cateId');
        $Arr['byear']   = $input->post('byear');
        $Arr['bmonth']  = $input->post('bmonth');
        $Arr['bday']    = $input->post('bday');
        $Arr['addTm']   = SYSTEM_TIME;
        $Arr['score']   = 0;
        $balance        =
        $Arr['balance'] = $input->post('balance');
        $Arr['remark']  = $input->post('remark');
        $remark2        = $input->post('remark2');
        $Arr['introducer']  = $input->post('introducer');
        
        $staffid        = (int)$input->post("staffid");
        
        
        if(!$balance){            
            echo "<script>alert('请填写充值卡金额');document.location='?c=Member&a=ToAddUserFromBill&andCard=1';</script>";
            exit;
        }
        
        //查看该电话是否注册了
        $minfo = Helper_Member::getMemberInfo(array(
            'phone'           => $Arr['phone'], #ID
        ));
        
        if($minfo){            
            echo "<script>alert('该会员已经存在了，不能再次添加，直接充值就可以了');document.location='?c=Member&phone={$Arr['phone']}';</script>";
            exit;
        }
        $minfo = $Arr;
        
        //确认一下介绍人
        if($Arr['introducer']){ //如果是新添加用户，验证介绍人是否存在
            $pminfo = Helper_Member::getMemberInfo(array(
                'phone'           => $Arr['introducer'], #ID
            ));
            if(!$pminfo){ #如果没有查到这个会员，清空介绍人字段
                $Arr['introducer'] = false;
            }
        }
        
        //添加会员
        $memberId = Helper_Dao::insertItem(array(
            'addItem'       =>  $Arr, #数据列
            'dbName'        =>  'Db_Andyou',    #数据库名
            'tblName'       =>  'member',    #表名
        ));
        
        $card = $balance;
        //进行充值        
//        $db = Db_Andyou::instance();
//        $sql = "update member set balance = balance + {$Arr['balance']} where id = {$memberId}";
//        $db->query($sql);
        
        
//        $output->bno         = Helper_Bill::getCardMaxBno();
        $output->bno         = Helper_Bill::getCommonMaxBno();
        $logItem = array(
            "memberId"   => $memberId,
            "direction"  => 0,
            "card"       => $Arr['balance'],
            "dateTm"     => SYSTEM_TIME,
            "adminer"    => $output->admin,
            "remark"     => $remark2,
            "orgCard"    => 0,
            "staffid"    => $staffid,
            "bno"        => $output->bno ,
        );
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $logItem, #数据列
		        'dbName'        =>  'Db_Andyou',    #数据库名
		        'tblName'       =>  'log_cardchange',    #表名
		));
        $memCate = Helper_Member::getMemberCatePairs();
        //会员类型
        $minfo["cateName"]   = $memCate[$minfo["cateId"]];
        $output->money       = $card;  #充值的钱
        $output->nowBalance  = $card;
        $output->memberInfo  = $minfo;

        $staffArr             = Helper_Staff::getStaffPairs();
        $output->staffName    = $staffArr[$staffid];
        
        Helper_Bill::createOneCommonBno();//生成一个通用订单号
        $output->setTemplate("CardPrint");
        
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
        
        //查看用户本次消费是否购买了其他商品的服务
        $sql = "select bid,bno,i.proId,i.num bnum,i.staffid,p.* from billsitem i left join product p on i.proId = p.id where bid = {$bid} and p.ctype > 1";
        $res = $db->getAll($sql);
        //购买了其他商品的服务，如次卡
        if($res){
            foreach($res as $re){
                $num  = (int)$re["bnum"];
                //商品用
                $tmpRow = array(
                      'memberId' => $memberId,
                      'proId'    => $re["proId"],
                      'name'     => $re["othername"],
                      'proName'  => $re["name"],
                      'num'      => $re["num"],
                      'ctype'    => $re["ctype"],
                      'buytm'    => SYSTEM_TIME,
                );
                //日志用
                $tmpLogRow = array(
                      'memberId'      => $memberId,
                      'otherproId'    => $re["proId"],
                      'name'          => $re["othername"],
                      'direction'     => 1,
                      'cvalue'        => $re["num"],
                      'orgcvalue'     => 0,
                      'ctype'         => $re["ctype"],
                      'dateTm'        => SYSTEM_TIME,
                      'staffid'       => $re["staffid"],
                      'bno'           => $bno,
                );
                //记录用户的所有服务
                for($i=0;$i<$num;$i++){                    
                    Helper_Dao::insertItem(array(
                           'addItem'       =>  $tmpRow,
                           'dbName'        =>  'Db_Andyou',
                           'tblName'       =>  'memeberotherpro',
                   ));
                   //数据变化日志 
                   Helper_Dao::insertItem(array(
                           'addItem'       =>  $tmpLogRow,
                           'dbName'        =>  'Db_Andyou',
                           'tblName'       =>  'log_useotherpro',
                   ));
                }
            }
        }
        
        //给介绍人添加积分
        if(!empty($Arr['introducer'])){
            
            $sysOptions = Helper_Option::getAllOptions(); 
            //给介绍人增加积分
            if($sysOptions && !empty($sysOptions["MemberParentRatio"])&& !empty($sysOptions["MemberParentRatio"]["value"])){
                $introducer = $Arr['introducer'];
                if(empty($minfo) || empty($minfo["allsum"])){//如果用户从来没有消费过，也就是第一次消费才给介绍人增加积分
                   

                    $introInfo = Helper_Member::getMemberInfo(array('phone'=>$introducer));
                    $introducerId = $introInfo["id"];
                    $iscore = $output->canGetScore * $sysOptions["MemberParentRatio"]["value"];                    
                    //更新积分
                    $sql = "update member set score = score + {$iscore}  where id = {$introducerId}";
                    $db->query($sql);
                    
                    //记录积分
                    Helper_Member::addScoreLog(array(
                        'memberId'         => $introducerId, #ID
                        'direction'        => 0, #1 减 0 加
                        'score'            => $iscore, #积分
                        'orgScore'         => $introInfo["score"], #原始积分
                        'bno'              => $bno, #订单号
                        'remark'           => $minfo ? '介绍【'.$minfo["phone"]."-".$minfo["name"].'】消费得积分' . $output->canGetScore."*".$sysOptions["MemberParentRatio"]["value"]
                                                     : '介绍【'.$Arr['phone'].'】消费得积分' . $output->canGetScore."*".$sysOptions["MemberParentRatio"]["value"], #
                    ));

                    
                }
            }
        }
        
                
        $urlStr = "?c={$output->ctlName}";
	    echo "<script>alert('添加成功');document.location='?c=Bills&isAddUser=1';</script>";
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
        
        //生成一个单号
        $output->bno         = Helper_Bill::getCommonMaxBno();
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
            "bno"        => $output->bno ,
        );
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $logItem, #数据列
		        'dbName'        =>  'Db_Andyou',    #数据库名
		        'tblName'       =>  'log_cardchange',    #表名
		));
        if($direction != 1){//充值大小票
            
            $output->money       = $card;  #充值的钱
            $output->nowBalance  = $card + $minfo["balance"];
            $output->memberInfo  = $minfo;
            
            $staffArr             = Helper_Staff::getStaffPairs();
            $output->staffName    = $staffArr[$staffid];
            
            Helper_Bill::createOneCommonBno();//生成一个通用订单号
            $output->setTemplate("CardPrint");
        }else{
            echo "<script>document.location='{$urlStr}';</script>";
            exit;        
        }
        
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
        $Arr["upTm"] = SYSTEM_TIME;
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

