<?php
/**
* 京东数据访问接口
*
 * 一些文档
 * 产品：http://help.jd.com/jos/question-628.html
 * 类目：http://help.jd.com/jos/question-627.html
 * 价格及促销服务：http://help.jd.com/jos/question-809.html
 * 
*/

class API_Item_Open_JD
{
    private static $_APPKEY       = "71EAC7689F08DE6A8CEFC091D020BB2E";
    private static $_APPSECRET    = "a8ecaeeb45cf40ae8dce6ecc6746d634";
    private static $_APPCALLBURL  = "http://gw.api.360buy.com/routerjson"; 
        

    /**
     * 获得所有的产品类目
     *  http://help.jd.com/jos/question-627.html
     * 此接口的查询必须是根据父类查询子类
     */    
    public static function getCategorys($paramArr){
		$options = array(
			'catelogyId'   => 0,     #分类ID
			'level'        => 0,     #level所在等级
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $apiParam = array(
            'catelogyId'   => $catelogyId, #分类ID
            'level'        => $level, #level
            'isIcon'       => false, 
            'isDescription'=> false, 
            'client'       => 'jingdong', 
        );
        $out = self::fetchData(array('apiName'=>'jingdong.ware.product.catelogy.list.get','data'=>$apiParam));
        if(!$out)return false;
        //如果有错误信息，调试的时候可以打印出来查看
        if(isset($out['error_response'])){
            return false;
        }
        if(!empty($out['jingdong_ware_product_catelogy_list_get_responce']) && !empty($out['jingdong_ware_product_catelogy_list_get_responce']['productCatelogyList'])){
            $data = $out['jingdong_ware_product_catelogy_list_get_responce']['productCatelogyList']['catelogyList'];
            return $data;
        }
        return $out;
    }
    
    /**
     * 根据三级类目获得产品列表
     * http://help.jd.com/jos/question-628.html
     * 此接口的查询必须是根据父类查询子类
     */    
    public static function getProListByCate($paramArr){
		$options = array(
			'catelogyId'   => 0,     #分类ID
			'page'         => 1,     #页码
			'pageSize'     => 10,     #每页个数，每页最多100个
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $cols = 'catelogyId,level,isIcon,isDescription,client';
        $apiParam = array(
            'catelogyId'   => $catelogyId, #分类ID
            'page'         => $page, #页数
            'pageSize'     => $pageSize, #每页内产品数
            'client'       => 'jingdong', 
        );
        $out = self::fetchData(array('apiName'=>'jingdong.ware.promotion.search.catelogy.list','data'=>$apiParam));
        if(!$out)return false;
        //如果有错误信息，调试的时候可以打印出来查看
        if(isset($out['error_response'])){
            return false;
        }
        if(!empty($out['jingdong_ware_promotion_search_catelogy_list_responce'])){
            $data = $out['jingdong_ware_promotion_search_catelogy_list_responce']['searchCatelogyList'];
            unset($data["show"]);
            unset($data["selfIsTrue"]);
            unset($data["resultCode"]);
            unset($data["regionIsTrue"]);
            return $data;
        }
        return $out;
    }
    
    /**
     * 获得产品基础信息
     */
    public static function getBaseProductInfo($paramArr){
		$options = array(
			'skuIds'         => '',    #Skuids 可以传入多个
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        /**
         * 字段
         * state 上下柜状态     0、下柜，1、上柜，2、可上柜（基本信息完备，采销没有正式上柜），10、POPSKU删除
         * is_delete  是否有效  0无效，1有效 
         * 
         * 更多字段可以查看 http://help.jd.com/jos/question-628.html 
         */
        
        $cols = 'sku_id,name,brand_name,ebrand,image_path,state,wserve,category';
        $apiParam = array(
            'ids'  => $skuIds, #skuId
            'base' => $cols,
        );
        $out = self::fetchData(array('apiName'=>'jingdong.ware.baseproduct.get','data'=>$apiParam)); if(!$out)return false;
        //如果有错误信息，调试的时候可以打印出来查看
        if(isset($out['error_response'])){
            return false;
        }
        
        if(!empty($out['jingdong_ware_baseproduct_get_responce'])){
            $data = $out['jingdong_ware_baseproduct_get_responce']['product_base'];
            if(strpos($skuIds, ',') === false){#如果只获得一个产品
                $data = $data[0];
            }
            return $data;
        }
        return $out;
    }
    
    /** 
     * 获得详细的产品信息
     */
    public static function getDetailProductInfo($paramArr){
		$options = array(
			'skuId'         => '',    #skuid
			'getAllImg'     => false, #是否返回所有的产品图片
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $skuId = (int)$skuId;
        $apiParam = array(
            'skuId'            => $skuId, #skuId
            'isLoadWareScore'  => 'true',
            'client'           => 'm',
        );
        $apiName = 'jingdong.ware.product.detail.search.list.get';
        
        $out = self::fetchData(array('apiName'=>$apiName,'data'=>$apiParam));
        if(!$out)return false;
        //如果有错误信息，调试的时候可以打印出来查看
        if(isset($out['error_response'])){
            return false;
        }
        
        if(!empty($out['jingdong_ware_product_detail_search_list_get_responce'])){
            $data = $out['jingdong_ware_product_detail_search_list_get_responce']["productDetailList"];
            if(!$getAllImg){#如果不获得图片，就删除这个图片
                $data = $data['productInfo'];
            }
            return $data;
        }
        return $out;
        
        
    }
    
    
    /** 
     * 购买该商品的人最终购买了
     */
    public static function getBuyToBuy($paramArr){  
		$options = array(
			'skuId'         => '',    #skuid
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $skuId = (int)$skuId;
        $apiParam = array(
            'wareId '            => $skuId, #skuId
            'client'             => 'jingdong',
        );
        $apiName = 'jingdong.ware.buytobuy.list.get';
        
        $out = self::fetchData(array('apiName'=>$apiName,'data'=>$apiParam));
        return $out;
        if(!$out)return false;
        //如果有错误信息，调试的时候可以打印出来查看
        if(isset($out['error_response'])){
            return false;
        }
        
        if(!empty($out['jingdong_ware_product_detail_search_list_get_responce'])){
            $data = $out['jingdong_ware_product_detail_search_list_get_responce']["productDetailList"];
            if(!$getAllImg){#如果不获得图片，就删除这个图片
                $data = $data['productInfo'];
            }
            return $data;
        }
        return $out;
        
        
    }
    
    /** 
     * 获得产品的大字段内容
     */
    public static function getProductBigFild($paramArr){
		$options = array(
			'skuId'         => '',    #SKUID
            'getIntro'      => true,  #是否获得产品介绍
            'getShouHou'    => false,  #是否获得售后
            'getGuige'      => false,  #是否获得规格参数
            'getQingdan'    => false,  #是否获得包装清单 
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $skuId = (int)$skuId;
        $colArr = array();
        if($getIntro)$colArr[]   = 'wdis';
        if($getShouHou)$colArr[] = 'shou_hou';
        if($getGuige)$colArr[]   = 'prop_code';
        if($getQingdan)$colArr[] = 'ware_qd';
        
        $apiParam = array(
            'sku_id'            => $skuId, #skuId
            'field'             => implode(",",$colArr)
        );
        
        $apiName = 'jingdong.ware.productbigfield.get';
        
        $out = self::fetchData(array('apiName'=>$apiName,'data'=>$apiParam));
        if(!$out)return false;
        //如果有错误信息，调试的时候可以打印出来查看
        if(isset($out['error_response'])){
            return false;
        }
        
        if(!empty($out['jingdong_ware_productbigfield_get_responce'])){
            $data = $out['jingdong_ware_productbigfield_get_responce'];
            unset($data['code']);
            return $data;
        }
        return $out;
        
        
    }
     
    /**
     * 获得产品促销信息
     */
    public static function getPromotionInfo($paramArr){
		$options = array(
			'skuId'         => '',    #Skuids 可以传入多个
			'webSite'        => 1,    #站点,1-京东、2-千寻、3-ept海外购 
			'origin'         => 1,    #来源,1-网站交易、2-手机包括ipad 
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        /**
         * 字段
         * state 上下柜状态     0、下柜，1、上柜，2、可上柜（基本信息完备，采销没有正式上柜），10、POPSKU删除
         * is_delete  是否有效  0无效，1有效 
         * 
         * 更多字段可以查看 http://help.jd.com/jos/question-628.html 
         */
        
        $cols = 'skuId,webSite,origin';
        $apiParam = array(
            'skuId'   => $skuId, #skuId
            'webSite' => $webSite,
            'origin'  => $origin,
        );
        $out = self::fetchData(array('apiName'=>'jingdong.ware.promotionInfo.get','data'=>$apiParam)); 
        if(!$out)return false;
        
        //如果有错误信息，调试的时候可以打印出来查看
        if(isset($out['error_response'])){
            return false;
        }
        
        if(!empty($out['jingdong_ware_promotionInfo_get_responce']) && !empty($out['jingdong_ware_promotionInfo_get_responce']['promoInfoResponse']['promotionInfoList'])){
            $data = $out['jingdong_ware_promotionInfo_get_responce']['promoInfoResponse']['promotionInfoList'];
            return $data;
        }
        return $out;
    }
    
    /**
     * 获得产品价格
     */
    public static function getPrice($paramArr){
		$options = array(
			'skuId'         => '',    #Skuids 可以传入多个
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $skuIdStr = "J_".$skuId;
        $apiParam = array(
            'sku_id'   => $skuIdStr, #skuId
        );
        $out = self::fetchData(array('apiName'=>'jingdong.ware.price.get','data'=>$apiParam)); 
        if(!$out)return false;
        
        //如果有错误信息，调试的时候可以打印出来查看
        if(isset($out['error_response'])){
            return false;
        }
        
        if(!empty($out['jingdong_ware_price_get_responce']) && !empty($out['jingdong_ware_price_get_responce']["price_changes"])){
            $data = $out['jingdong_ware_price_get_responce']["price_changes"];
            if($data)$data = $data[0];
            return $data;
        }
        return $out;
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
        $sysParam = array(
            'app_key'    => self::$_APPKEY,
            'method'     => $apiName,
            'timestamp'  => date("Y-m-d H:i:s"),
            'v'          => "2.0",//版本号
        );
        
        
        //具体一个api的参数的处理
        $apiParam = array();
        ksort($data);
        $apiParam['360buy_param_json'] = json_encode($data);
        //参数的签名部分
        $sysParam["sign"] = self::generateSign(array_merge($sysParam, $apiParam));
        $url               = self::buildUrl($sysParam); //拼装URL
        
        //去请求数据
        $responseData = self::doCurl(array('url'=> $url,'apiParams'=>$apiParam, 'timeout'=> 30));
        if($responseData){
            #解码过程中遇到了一些特殊字符，对这些字符进行处理
            $responseData = str_replace('	', '', $responseData);
            return api_json_decode($responseData);
        }else{
            return false;
        }
    }
    
    /**
     *
     * 发送http请求
     */
    private static function doCurl($paramArr){
		$options = array(
			'url'         => '', #请求的地址
			'apiParams'   => false,  #数据参数
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (is_array($apiParams) && 0 < count($apiParams)) {
            $postBodyString = "";
            foreach ($apiParams as $k => $v) {
                $postBodyString .= "$k=" . urlencode($v) . "&";
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
        }
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            #throw new Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                #throw new Exception($response, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $response;
    }
    
    
    /**
     *  对参数进行签名
     */
    private static function generateSign($params){
        if ($params != null) { //所有请求参数按照字母先后顺序排序
            ksort($params);
            //定义字符串开始 结尾所包括的字符串
            $stringToBeSigned = self::$_APPSECRET;
            //把所有参数名和参数值串在一起
            foreach ($params as $k => $v) {
                $stringToBeSigned .= "$k$v";
            }
            unset($k, $v);
            //把venderKey加在字符串的两端
            $stringToBeSigned .= self::$_APPSECRET;
        } else {
            //定义字符串开始 结尾所包括的字符串
            $stringToBeSigned = self::$_APPSECRET;
            //把venderKey加在字符串的两端
            $stringToBeSigned .= self::$_APPSECRET;
        }
        //使用MD5进行加密，再转化成大写
//        echo $stringToBeSigned;
//        $stringToBeSigned = mb_convert_encoding($stringToBeSigned, "UTF-8");
        return strtoupper(md5($stringToBeSigned));
    }
    

    /**
     * 拼装URL
     */
    private static  function buildUrl($params){
        $requestUrl = self::$_APPCALLBURL . "?";
        foreach ($params as $sysParamKey => $sysParamValue) {
            $requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);
        return $requestUrl;
    }

    
    
    
    

}

