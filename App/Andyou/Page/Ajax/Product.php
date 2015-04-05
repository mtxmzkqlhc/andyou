<?php
/**
 * Form表单的Ajax数据
 */
class Andyou_Page_Ajax_Product extends Andyou_Page_Abstract{

    public function __construct(){}

    
    
    /**
     * 根据电话号码获得会员信息
     */
    public function doGetProductByCode(ZOL_Request $input, ZOL_Response $output) {
        $code = $input->get('code');
        if(!$code){
            echo "{}";exit;
        }
        //获得会员信息
        $proList = Helper_Product::getProductList(array('code' => $code,'num'=>30));
        if($proList){
            $data = array(
                'num'   => count($proList),
                'data'  => $proList,
            );
            echo api_json_encode($data);
        }else{
            echo "{}";
        }
        exit;
    }
    
}


