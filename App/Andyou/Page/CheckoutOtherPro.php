<?php
/**
 * 其他类型产品的销售如次卡
 *
 */

//error_reporting(0);
class  Andyou_Page_CheckoutOtherPro  extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Checkout';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}

    /**
     * 获得数据列表
     */
	public function doDefault(ZOL_Request $input, ZOL_Response $output){
		
        $output->ctype = (int)$input->get("ctype");//其他产品的消费类型
        if(!$output->ctype)$output->ctype = 2;
        
        //获得所有会员类型
        $output->memberCate = Helper_Member::getMemberCatePairs();
		//获得所有的员工
        $output->staffArr  = Helper_Staff::getStaffPairs();
        $output->proCtypeArr = ZOL_Config::get("GLOBAL","PRO_CTYPE");
        if(!isset($output->proCtypeArr[$output->ctype])){
            echo "ERROR";exit;
        }
        $output->ctypeName = $output->proCtypeArr[$output->ctype]['name'];
		$output->setTemplate('CheckoutOtherPro');
	}
    
    private function showErrMsg(){
        echo "数据错误！";
        exit;
    }
    
	
    public function doDone(ZOL_Request $input, ZOL_Response $output){
        
        $otherProIdArr  = $input->post("otherProId");//所有商品
        $staffid        = (int)$input->post("staffid");//员工
        $remark         = $input->post("remark");//填写的备注
        $memberId       = (int)$input->post("memberId");//会员ID
        
        if(!$staffid || empty($otherProIdArr)){
            $this->showErrMsg();
        }
        
        //获得会员信息
        $memberInfo  = Helper_Member::getMemberInfo(array("id"=>$memberId));
        if(!$memberInfo){
            $this->showErrMsg();
        }
        
        //获得商品列表
        $proInfoArr = array();
        foreach($otherProIdArr as $id){
           $info = Helper_Product::getMemberOtherPro(array('id'=>$id,'memberId'=>$memberId));
           if($info && $info["num"] > 0){//筛选一下商品
               $proInfoArr[] = $info;
           }
        }
        //生成一个单号
        $bno = Helper_Bill::getCommonMaxBno();
        
        $db = Db_Andyou::instance();
        if($proInfoArr){
            foreach($proInfoArr as $info){
                
                //数量减少
                $sql = "update memeberotherpro set num = num - 1 where id = ".$info["id"];
                $db->query($sql);
                
                //记录消费日志
                $tmpLogRow = array(
                    'memberId'      => $memberId,
                    'phone'         => $memberInfo["phone"],
                    'otherproId'    => $info["id"],
                    'name'          => $info["name"],
                    'direction'     => 1,
                    'cvalue'        => 1,
                    'orgcvalue'     => $info["num"],
                    'ctype'         => $info["ctype"],
                    'dateTm'        => SYSTEM_TIME,
                    'staffid'       => $staffid,
                    'bno'           => $bno,
                    'remark'        => $remark,
                );
                
                Helper_Dao::insertItem(array(
                        'addItem'       =>  $tmpLogRow,
                        'dbName'        =>  'Db_Andyou',
                        'tblName'       =>  'log_useotherpro',
                ));
                
            }
        }
        $staffArr  = Helper_Staff::getStaffPairs();
        
        
         //准备进入打印页面
        $output->proInfoArr   = $proInfoArr;
        $output->bno          = $bno;
        $output->memberId     = $memberId;
        $output->memberInfo   = $memberInfo;//会员信息
        $output->staffid      = $staffid;
        $output->staffName    = $staffArr[$staffid];
        Helper_Bill::createOneCommonBno();//生成一个通用订单号
		$output->setTemplate('OtherProPrint');        
    }
}

