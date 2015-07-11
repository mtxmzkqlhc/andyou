<?php
/**
* 国美数据访问接口
 *
*/
class API_Item_Open_Gome
{
    private static $_APPKEY       = "40131470092";
    private static $_APPSECRET    = "FF47D244F8B46432F3EF3288C60D7E5E";
    private static $_APPCALLBURL  = "http://open.gome.com.cn/interface/cooperate/gateway"; 
    
    
    /**
     * 获得所有的产品类目
     * 
     */    
    public static function getCategorys() {       
       $data =  self::fetchData(array('apiName'=>'gome.categorys.get'));
       if($data){
           return $data["categorys"];
       }
    }
    
    /**
     *  获得Sku的列表(全量)
     *  每天晚上十一点到十二点更新一次。
     * 
     */
    public static function getSkuList($paramArr) {
		$options = array(
			'category_id'         => false, #分类ID
			'page_no'             => 1, #第几页
			'page_size'           => 100, #每页多少个，最大100
			'stock_status'        => false, #库存状况 0 无库存 1有库存
			'product_id'          => false, #产品ID
			'sku_id'              => false, #SKUID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //要传递的参数的处理
        $data = self::mergeParam(array('category_id','page_no','page_size','stock_status','product_id','sku_id'),$options);
        
        return self::fetchData(array('apiName'=>'gome.items.page.get','data'=>$data));
         
         
    }
    
    
    /**
     *  获得增量Sku的列表
     *  每小时更新一次
     */
    public static function getAddSkuList($paramArr) {
		$options = array(
			'category_id'         => false, #分类ID
			'page_no'             => 1, #第几页
			'page_size'           => 100, #每页多少个，最大10000
			'stock_status'        => false, #库存状况 0 无库存 1有库存
			'product_id'          => false, #产品ID
			'sku_id'              => false, #SKUID
			'update_start_date'   => false, #更新开始时间
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //要传递的参数的处理
        $data = self::mergeParam(array('category_id','page_no','page_size','stock_status','product_id','sku_id','update_start_date'),$options);
        
        return self::fetchData(array('apiName'=>'gome.items.maintain.page.get','data'=>$data));
         
         
    }
    
    
    /**
     *  根据SkuId获得Sku的信息
     * 
     */
    public static function getSkuInfo($paramArr) {
		$options = array(
			'sku_id'         => false, #SKUID
			'product_id'     => false, #产品ID
			'getAll'         => false, #是否获得所有数据，国美奇怪，一个sku在不同的类目中也返回多条数据
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(!$sku_id && !$product_id)return false;
        
        $data = self::getSkuList(array(
			'sku_id'         => $sku_id, #SKUID
			'product_id'     => $product_id, #产品ID
        ));
        if(!$data || empty($data["items"]))return false;
        
        if($getAll){
            return $data["items"];
        }else{
             return $data["items"][0];
        }
        
         
         
    }
    /**
     *  获得商品区域库存信息
     * 
        城市对应表：
     * 
        北京市	DA11010200	黑龙江	DA51010200
        天津市	DA12010100	吉林省	DA52010200
        河北省	DA13080100	辽宁省	DA53010200
        山西省	DA14080200	宁夏省	DA61050200
        内蒙古	DA15060300	新疆省	DA62010100
        上海市	DA21010100	青海省	DA63010100
        浙江省	DA22010600	陕西省	DA64010200
        江苏省	DA23010100	甘肃省	DA65010400
        安徽省	DA24010100	四川省	DA71010100
        福建省	DA25010100	云南省	DA72010300
        山东省	DA26050400	贵州省	DA73010300
        广东省	DA31010200	重庆市	DA74010100
        广西省	DA32010300	西藏省	DA75010100
        海南省	DA33010300	台湾省	DA81010100
        湖北省	DA41010500	香港	DA82010100
        湖南省	DA42010100	澳门	DA83010100
        河南省	DA43010100	钓鱼岛	DA84010100
        江西省	DA44010200		

     */
    public static function getSkuStockInfo($paramArr) {
		$options = array(
			'sku_id'         => false, #SKUID
			'city_id'        => false, #城市ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(!$sku_id)return false;
        
        //要传递的参数的处理
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
     *  获得团购列表/团购信息
     * 
     */
    public static function getGroupList($paramArr) {
		$options = array(
			'category_id'         => false, #分类ID
			'page_no'             => 1, #第几页
			'page_size'           => 100, #每页多少个，最大100
			'sku_id'              => false, #SKUID
			'product_id'          => false, #产品ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        
        //要传递的参数的处理
        $data = self::mergeParam(array('category_id','sku_id','product_id','page_no','page_size'),$options);
        
        $apiName = 'gome.grouppurchase.page.get';
        return self::fetchData(array('apiName'=>$apiName,'data'=>$data));         
         
    }
    
    /**
     *  获得抢购列表，每天调用上限为三次。
     * 
     */
    public static function getRushList($paramArr) {
		$options = array(
			'category_id'         => false, #分类ID
			'page_no'             => 1, #第几页
			'page_size'           => 10, #每页多少个，最大1000
			'sku_id'              => false, #SKUID
			'product_id'          => false, #产品ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        
        //要传递的参数的处理
        $data = self::mergeParam(array('category_id','sku_id','product_id','page_no','page_size'),$options);
        
        $apiName = 'gome.limitbuy.page.get';
        return self::fetchData(array('apiName'=>$apiName,'data'=>$data));         
         
    }
    
    
    /**
     *  获取数据
     */
    private static function fetchData($paramArr){
		$options = array(
			'apiName'         => '', #API名称
			'data'            => false,  #请求数据
			'format'          => 'json', #返回数据类型
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //系统级参数部分
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
     * 拼装国美的请求参数
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

