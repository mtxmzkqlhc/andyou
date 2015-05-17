<?php
/**
 * Form表单的Ajax数据
 */
class Andyou_Page_Ajax_Product extends Andyou_Page_Abstract{

    public function __construct(){}

    
    
    /**
     * 根据电话号码获得产品信息
     */
    public function doGetProductByCode(ZOL_Request $input, ZOL_Response $output) {
        $code = $input->get('code');
        $fromScore = (int)$input->get('fromScore');
        if(!$code){
            echo "{}";exit;
        }
        $proList = Helper_Product::getProductList(array('code' => $code,'num' => 30,'canByScore'  => $fromScore));
        if($proList){
            //完善产品的分类信息
            $catePairs = Helper_Product::getProductCatePairs();
            foreach($proList as $k => $p){
                $proList[$k]['cateName'] = $catePairs[$p["cateId"]];
                
            }
            $data = array(
                'num'         => count($proList),
                'data'        => $proList,
            );
            echo api_json_encode($data);
        }else{
            echo "{}";
        }
        exit;
    }
    
}


