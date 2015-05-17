<?php
/**
 * Form表单的Ajax数据
 */
class Andyou_Page_Ajax_Member extends Andyou_Page_Abstract{

    public function __construct(){}

    
    
    /**
     * 根据电话号码获得会员信息
     */
    public function doGetMemberByPhone(ZOL_Request $input, ZOL_Response $output) {
        $phone     = $input->get('phone');
        $phonecard = $input->get('phonecard');
        if( (!$phone || !is_numeric($phone)) && (!$phonecard || !is_numeric($phonecard))  ){
            echo "{}";exit;
        }
        //获得会员信息
        $memInfo = Helper_Member::getMemberInfo(array('phone' => $phone,'phoneOrCardno'=>$phonecard));
        if($memInfo){
            echo api_json_encode($memInfo);
        }else{
            echo "{}";
        }
        exit;
    }
    
}


