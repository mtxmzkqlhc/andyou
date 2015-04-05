<?php
/**
 * Form����Ajax����
 */
class Andyou_Page_Ajax_Product extends Andyou_Page_Abstract{

    public function __construct(){}

    
    
    /**
     * ���ݵ绰�����û�Ա��Ϣ
     */
    public function doGetProductByCode(ZOL_Request $input, ZOL_Response $output) {
        $code = $input->get('code');
        if(!$code){
            echo "{}";exit;
        }
        //��û�Ա��Ϣ
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


