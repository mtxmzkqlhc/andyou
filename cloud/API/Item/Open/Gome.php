<?php
/**
* �������ݷ��ʽӿ�
 *
*/
class API_Item_Open_Gome
{
    private static $_APPKEY       = "40131470092";
    private static $_APPSECRET    = "FF47D244F8B46432F3EF3288C60D7E5E";
    private static $_APPCALLBURL  = "http://open.gome.com.cn/interface/cooperate/gateway"; 
    
    
    /**
     * ������еĲ�Ʒ��Ŀ
     * 
     */    
    public static function getCategorys() {       
       $data =  self::fetchData(array('apiName'=>'gome.categorys.get'));
       if($data){
           return $data["categorys"];
       }
    }
    
    /**
     *  ���Sku���б�(ȫ��)
     *  ÿ������ʮһ�㵽ʮ�������һ�Ρ�
     * 
     */
    public static function getSkuList($paramArr) {
		$options = array(
			'category_id'         => false, #����ID
			'page_no'             => 1, #�ڼ�ҳ
			'page_size'           => 100, #ÿҳ���ٸ������100
			'stock_status'        => false, #���״�� 0 �޿�� 1�п��
			'product_id'          => false, #��ƷID
			'sku_id'              => false, #SKUID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //Ҫ���ݵĲ����Ĵ���
        $data = self::mergeParam(array('category_id','page_no','page_size','stock_status','product_id','sku_id'),$options);
        
        return self::fetchData(array('apiName'=>'gome.items.page.get','data'=>$data));
         
         
    }
    
    
    /**
     *  �������Sku���б�
     *  ÿСʱ����һ��
     */
    public static function getAddSkuList($paramArr) {
		$options = array(
			'category_id'         => false, #����ID
			'page_no'             => 1, #�ڼ�ҳ
			'page_size'           => 100, #ÿҳ���ٸ������10000
			'stock_status'        => false, #���״�� 0 �޿�� 1�п��
			'product_id'          => false, #��ƷID
			'sku_id'              => false, #SKUID
			'update_start_date'   => false, #���¿�ʼʱ��
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //Ҫ���ݵĲ����Ĵ���
        $data = self::mergeParam(array('category_id','page_no','page_size','stock_status','product_id','sku_id','update_start_date'),$options);
        
        return self::fetchData(array('apiName'=>'gome.items.maintain.page.get','data'=>$data));
         
         
    }
    
    
    /**
     *  ����SkuId���Sku����Ϣ
     * 
     */
    public static function getSkuInfo($paramArr) {
		$options = array(
			'sku_id'         => false, #SKUID
			'product_id'     => false, #��ƷID
			'getAll'         => false, #�Ƿ����������ݣ�������֣�һ��sku�ڲ�ͬ����Ŀ��Ҳ���ض�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(!$sku_id && !$product_id)return false;
        
        $data = self::getSkuList(array(
			'sku_id'         => $sku_id, #SKUID
			'product_id'     => $product_id, #��ƷID
        ));
        if(!$data || empty($data["items"]))return false;
        
        if($getAll){
            return $data["items"];
        }else{
             return $data["items"][0];
        }
        
         
         
    }
    /**
     *  �����Ʒ��������Ϣ
     * 
        ���ж�Ӧ��
     * 
        ������	DA11010200	������	DA51010200
        �����	DA12010100	����ʡ	DA52010200
        �ӱ�ʡ	DA13080100	����ʡ	DA53010200
        ɽ��ʡ	DA14080200	����ʡ	DA61050200
        ���ɹ�	DA15060300	�½�ʡ	DA62010100
        �Ϻ���	DA21010100	�ຣʡ	DA63010100
        �㽭ʡ	DA22010600	����ʡ	DA64010200
        ����ʡ	DA23010100	����ʡ	DA65010400
        ����ʡ	DA24010100	�Ĵ�ʡ	DA71010100
        ����ʡ	DA25010100	����ʡ	DA72010300
        ɽ��ʡ	DA26050400	����ʡ	DA73010300
        �㶫ʡ	DA31010200	������	DA74010100
        ����ʡ	DA32010300	����ʡ	DA75010100
        ����ʡ	DA33010300	̨��ʡ	DA81010100
        ����ʡ	DA41010500	���	DA82010100
        ����ʡ	DA42010100	����	DA83010100
        ����ʡ	DA43010100	���㵺	DA84010100
        ����ʡ	DA44010200		

     */
    public static function getSkuStockInfo($paramArr) {
		$options = array(
			'sku_id'         => false, #SKUID
			'city_id'        => false, #����ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(!$sku_id)return false;
        
        //Ҫ���ݵĲ����Ĵ���
        $data = self::mergeParam(array('sku_id','city_id'),$options);
        
        $apiName = 'gome.items.stock.get';
        $rtnData = self::fetchData(array('apiName'=>$apiName,'data'=>$data));
        #return $rtnData;
        if($rtnData && !empty($rtnData['items'])){
            return $rtnData['items'][0];
        }else{
            return false;
        }
         
         
    }
    
    
    
    /**
     *  ����Ź��б�/�Ź���Ϣ
     * 
     */
    public static function getGroupList($paramArr) {
		$options = array(
			'category_id'         => false, #����ID
			'page_no'             => 1, #�ڼ�ҳ
			'page_size'           => 100, #ÿҳ���ٸ������100
			'sku_id'              => false, #SKUID
			'product_id'          => false, #��ƷID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        
        //Ҫ���ݵĲ����Ĵ���
        $data = self::mergeParam(array('category_id','sku_id','product_id','page_no','page_size'),$options);
        
        $apiName = 'gome.grouppurchase.page.get';
        return self::fetchData(array('apiName'=>$apiName,'data'=>$data));         
         
    }
    
    /**
     *  ��������б�ÿ���������Ϊ���Ρ�
     * 
     */
    public static function getRushList($paramArr) {
		$options = array(
			'category_id'         => false, #����ID
			'page_no'             => 1, #�ڼ�ҳ
			'page_size'           => 10, #ÿҳ���ٸ������1000
			'sku_id'              => false, #SKUID
			'product_id'          => false, #��ƷID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        
        //Ҫ���ݵĲ����Ĵ���
        $data = self::mergeParam(array('category_id','sku_id','product_id','page_no','page_size'),$options);
        
        $apiName = 'gome.limitbuy.page.get';
        return self::fetchData(array('apiName'=>$apiName,'data'=>$data));         
         
    }
    
    
    /**
     *  ��ȡ����
     */
    private static function fetchData($paramArr){
		$options = array(
			'apiName'         => '', #API����
			'data'            => false,  #��������
			'format'          => 'json', #������������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //ϵͳ����������
        $reqData = array(
            'app_key'    => self::$_APPKEY,
            'app_secret' => self::$_APPSECRET,
            'api_name'   => $apiName,
            'format'     => $format,
        );
        
        if($data){
            $reqData = array_merge($data,$reqData);
        }
        $url          = self::$_APPCALLBURL . "?" . http_build_query($reqData); 
        #
        $responseData = API_Http::curlPage(array('url'=> $url, 'timeout'=> 30));        
        if($responseData && $responseData != "null"){
            return api_json_decode($responseData);
        }else{
            return false;
        }        
        
        
    }
    /**
     * ƴװ�������������
     */
    private static function mergeParam($paramNms,$options){
        $data = array();
        foreach($paramNms as $nm){
            if(empty($options[$nm]))continue;            
            $data[$nm] = $options[$nm];
        }
        return $data;
    }

}

