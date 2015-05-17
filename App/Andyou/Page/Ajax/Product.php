<?php
/**
 * Form����Ajax����
 */
class Andyou_Page_Ajax_Product extends Andyou_Page_Abstract{

    public function __construct(){}

    
    
    /**
     * ���ݵ绰�����ò�Ʒ��Ϣ
     */
    public function doGetProductByCode(ZOL_Request $input, ZOL_Response $output) {
        $code = $input->get('code');
        $fromScore = (int)$input->get('fromScore');
        if(!$code){
            echo "{}";exit;
        }
        $proList = Helper_Product::getProductList(array('code' => $code,'num' => 30,'canByScore'  => $fromScore));
        if($proList){
            //���Ʋ�Ʒ�ķ�����Ϣ
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


