<?php
/**
 * ��Ա�������
 *
 */
class  Andyou_Page_Member extends Andyou_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Member';		
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
			'dbName'        => "Db_Andyou",  #���ݿ���
			'tblName'       => "member",       #����
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
        
        $output->memberCate = Helper_Member::getMemberCatePairs();
		
		//������е�Ա��
        $output->staffArr  = Helper_Staff::getStaffPairs();
		$output->setTemplate('Member');
	}
	
    /**
     *  ��֤�Ƿ���ԴӶ�����ӻ�Ա
     */
    private function checkFromBill(ZOL_Request $input, ZOL_Response $output){
        header("Content-type: text/html; charset=GBK");
        //��ö�����Ϣ
        $billInfo = Helper_Bill::getBillsInfo(array(
            'id'              => $output->bid, #ID
            'bno'             => $output->bno, #����
        ));
        if(!$billInfo){
            echo "Bill Not Found!";
            exit;
        }
        if($billInfo["memberId"]){
            echo "�����Ѿ�������Ա��";
            exit;
        }
        $output->billInfo = $billInfo;
        //�ɶһ�����
        
        $sysOptions = Helper_Option::getAllOptions();        
        $scoreRatio = !empty($sysOptions["ScoreRatio"]) ? $sysOptions["ScoreRatio"]["value"] : 0;
        
        $price = $output->billInfo["price"];
        $output->canGetScore = $scoreRatio ? round($price*$scoreRatio/100) : 0;
    }
    /**
     * �Ӷ�����ӻ�Ա
     */
	public function doToAddUserFromBill(ZOL_Request $input, ZOL_Response $output){
        $output->bno = $input->get("bno");
        $output->bid = $input->get("bid");
        $this->checkFromBill($input,$output);//��֤����
        $output->memberCate = Helper_Member::getMemberCatePairs();
		$output->setTemplate('MemberAddFromBill');
        
    }
    /**
     * �Ӷ�����ӻ�Աģ��
     */
    public function doAddUserFromBill(ZOL_Request $input, ZOL_Response $output){
        $output->bno = $input->post("bno");
        $output->bid = $input->post("bid");
        
        $this->checkFromBill($input,$output);//��֤����
        
        
        
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
        
        //�鿴�õ绰�Ƿ�ע����
        $minfo = Helper_Member::getMemberInfo(array(
            'phone'           => $Arr['phone'], #ID
        ));
        
        if(!$minfo && $Arr['introducer']){ //�����������û�����֤�������Ƿ����
            $pminfo = Helper_Member::getMemberInfo(array(
                'phone'           => $Arr['introducer'], #ID
            ));
            if(!$pminfo){ #���û�в鵽�����Ա����ս������ֶ�
                $Arr['introducer'] = false;
            }
        }
        
        //�����Ա�������ܶ� allsum
        $toAllSumPrice = round($price/100);
        $Arr['allsum'] = $toAllSumPrice;
        
        $db = Db_Andyou::instance();
        if($minfo){ //�����Ա�Ѿ������ˣ��ͽ���������ۼӵ������û���
            $memberId = $minfo["id"];
            //���»���
            $sql = "update member set score = score + {$output->canGetScore},allsum=allsum + {$toAllSumPrice}  where id = {$memberId}";
            $db->query($sql);
        
        }else{
            
            $memberId = Helper_Dao::insertItem(array(
                'addItem'       =>  $Arr, #������
                'dbName'        =>  'Db_Andyou',    #���ݿ���
                'tblName'       =>  'member',    #����
            ));
        }
        //�����˵����������û�ID
        $bid = $output->billInfo["id"];
        $bno = $output->billInfo["bno"];
        
        //��¼��Ա������ʷ        
        Helper_Member::addScoreLog(array(
            'memberId'         => $memberId, #ID
            'direction'        => 0, #1 �� 0 ��
            'score'            => $output->canGetScore, #����
            'orgScore'         => $minfo ? $minfo['score'] : 0, #ԭʼ����
            'bno'              => $bno, #������
            'remark'           => '����', #
        ));
		
        
        $sql = "update bills set memberId = {$memberId} where id = {$bid} limit 1";
        $db->query($sql);
                
        $sql = "update billsitem set memberId = {$memberId} where bid = {$bid}";
        $db->query($sql);
        
        //����������ӻ���
        if(!emtpy($Arr['introducer'])){
            
        }
        
                
        $urlStr = "?c={$output->ctlName}";
	    echo "<script>document.location='?c=Member&phone={$Arr['phone']}';</script>";
		exit;
        
    }
    /**
     *  �����û�����
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
        
        #��û�Ա��Ϣ
        $minfo = Helper_Member::getMemberInfo(array("id"=>$mid));
        if(!$minfo){
             echo "<script>alert('Member Error!');document.location='{$urlStr}';</script>";
             exit;
        }
        if($direction == 1){//���ֵ�ʱ���ж��û��Ƿ�����ô��
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
		        'addItem'       =>  $logItem, #������
		        'dbName'        =>  'Db_Andyou',    #���ݿ���
		        'tblName'       =>  'log_scorechange',    #����
		));
        echo "<script>document.location='{$urlStr}';</script>";
        exit;
        
    }
    
    /**
     *  �����û���Ա�����
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
        
        #��û�Ա��Ϣ
        $minfo = Helper_Member::getMemberInfo(array("id"=>$mid));
        if(!$minfo){
             echo "<script>alert('Member Error!');document.location='{$urlStr}';</script>";
             exit;
        }
        if($direction == 1){//���ֵ�ʱ���ж��û��Ƿ�����ô��
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
		        'addItem'       =>  $logItem, #������
		        'dbName'        =>  'Db_Andyou',    #���ݿ���
		        'tblName'       =>  'log_cardchange',    #����
		));
        echo "<script>document.location='{$urlStr}';</script>";
        exit;
        
    }
    /**
     * ��Ӽ�¼
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
         //�鿴�õ绰�Ƿ�ע����
        $minfo = Helper_Member::getMemberInfo(array(
            'phone'           => $Arr['phone'], #ID
        ));
        $urlStr =  "?c={$output->ctlName}&t={$output->rnd}";
        if($minfo){
            echo "<script>alert('���ֻ����Ѿ������ˣ�');document.location='?c=Member&phone={$Arr['phone']}';</script>";
            exit;
        }
		$pageUrl = $input->request('pageUrl');
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $Arr, #������
		        'dbName'        =>  'Db_Andyou',    #���ݿ���
		        'tblName'       =>  'member',    #����
		));
		/*backUrl*/
	    echo "<script>document.location='{$urlStr}';</script>";
		exit;
	}
    
    /**
     * ��������
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
	            'editItem'       =>  $Arr, #������
	            'dbName'         =>  'Db_Andyou',    #���ݿ���
	            'tblName'        =>   'member',    #����
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
                'tblName' => 'member',#����
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
		        'tblName'  => "member", #����
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

