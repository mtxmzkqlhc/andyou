<?php
/**
* �������ݷ��ʽӿ�
*
 * һЩ�ĵ�
 * ��Ʒ��http://help.jd.com/jos/question-628.html
 * ��Ŀ��http://help.jd.com/jos/question-627.html
 * �۸񼰴�������http://help.jd.com/jos/question-809.html
 * 
*/

class API_Item_Open_JD
{
    private static $_APPKEY       = "71EAC7689F08DE6A8CEFC091D020BB2E";
    private static $_APPSECRET    = "a8ecaeeb45cf40ae8dce6ecc6746d634";
    private static $_APPCALLBURL  = "http://gw.api.360buy.com/routerjson"; 
        

    /**
     * ������еĲ�Ʒ��Ŀ
     *  http://help.jd.com/jos/question-627.html
     * �˽ӿڵĲ�ѯ�����Ǹ��ݸ����ѯ����
     */    
    public static function getCategorys($paramArr){
		$options = array(
			'catelogyId'   => 0,     #����ID
			'level'        => 0,     #level���ڵȼ�
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $apiParam = array(
            'catelogyId'   => $catelogyId, #����ID
            'level'        => $level, #level
            'isIcon'       => false, 
            'isDescription'=> false, 
            'client'       => 'jingdong', 
        );
        $out = self::fetchData(array('apiName'=>'jingdong.ware.product.catelogy.list.get','data'=>$apiParam));
        if(!$out)return false;
        //����д�����Ϣ�����Ե�ʱ����Դ�ӡ�����鿴
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
     * ����������Ŀ��ò�Ʒ�б�
     * http://help.jd.com/jos/question-628.html
     * �˽ӿڵĲ�ѯ�����Ǹ��ݸ����ѯ����
     */    
    public static function getProListByCate($paramArr){
		$options = array(
			'catelogyId'   => 0,     #����ID
			'page'         => 1,     #ҳ��
			'pageSize'     => 10,     #ÿҳ������ÿҳ���100��
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $cols = 'catelogyId,level,isIcon,isDescription,client';
        $apiParam = array(
            'catelogyId'   => $catelogyId, #����ID
            'page'         => $page, #ҳ��
            'pageSize'     => $pageSize, #ÿҳ�ڲ�Ʒ��
            'client'       => 'jingdong', 
        );
        $out = self::fetchData(array('apiName'=>'jingdong.ware.promotion.search.catelogy.list','data'=>$apiParam));
        if(!$out)return false;
        //����д�����Ϣ�����Ե�ʱ����Դ�ӡ�����鿴
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
     * ��ò�Ʒ������Ϣ
     */
    public static function getBaseProductInfo($paramArr){
		$options = array(
			'skuIds'         => '',    #Skuids ���Դ�����
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        /**
         * �ֶ�
         * state ���¹�״̬     0���¹�1���Ϲ�2�����Ϲ񣨻�����Ϣ�걸������û����ʽ�Ϲ񣩣�10��POPSKUɾ��
         * is_delete  �Ƿ���Ч  0��Ч��1��Ч 
         * 
         * �����ֶο��Բ鿴 http://help.jd.com/jos/question-628.html 
         */
        
        $cols = 'sku_id,name,brand_name,ebrand,image_path,state,wserve,category';
        $apiParam = array(
            'ids'  => $skuIds, #skuId
            'base' => $cols,
        );
        $out = self::fetchData(array('apiName'=>'jingdong.ware.baseproduct.get','data'=>$apiParam)); if(!$out)return false;
        //����д�����Ϣ�����Ե�ʱ����Դ�ӡ�����鿴
        if(isset($out['error_response'])){
            return false;
        }
        
        if(!empty($out['jingdong_ware_baseproduct_get_responce'])){
            $data = $out['jingdong_ware_baseproduct_get_responce']['product_base'];
            if(strpos($skuIds, ',') === false){#���ֻ���һ����Ʒ
                $data = $data[0];
            }
            return $data;
        }
        return $out;
    }
    
    /** 
     * �����ϸ�Ĳ�Ʒ��Ϣ
     */
    public static function getDetailProductInfo($paramArr){
		$options = array(
			'skuId'         => '',    #skuid
			'getAllImg'     => false, #�Ƿ񷵻����еĲ�ƷͼƬ
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
        //����д�����Ϣ�����Ե�ʱ����Դ�ӡ�����鿴
        if(isset($out['error_response'])){
            return false;
        }
        
        if(!empty($out['jingdong_ware_product_detail_search_list_get_responce'])){
            $data = $out['jingdong_ware_product_detail_search_list_get_responce']["productDetailList"];
            if(!$getAllImg){#��������ͼƬ����ɾ�����ͼƬ
                $data = $data['productInfo'];
            }
            return $data;
        }
        return $out;
        
        
    }
    
    
    /** 
     * �������Ʒ�������չ�����
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
        //����д�����Ϣ�����Ե�ʱ����Դ�ӡ�����鿴
        if(isset($out['error_response'])){
            return false;
        }
        
        if(!empty($out['jingdong_ware_product_detail_search_list_get_responce'])){
            $data = $out['jingdong_ware_product_detail_search_list_get_responce']["productDetailList"];
            if(!$getAllImg){#��������ͼƬ����ɾ�����ͼƬ
                $data = $data['productInfo'];
            }
            return $data;
        }
        return $out;
        
        
    }
    
    /** 
     * ��ò�Ʒ�Ĵ��ֶ�����
     */
    public static function getProductBigFild($paramArr){
		$options = array(
			'skuId'         => '',    #SKUID
            'getIntro'      => true,  #�Ƿ��ò�Ʒ����
            'getShouHou'    => false,  #�Ƿ����ۺ�
            'getGuige'      => false,  #�Ƿ��ù�����
            'getQingdan'    => false,  #�Ƿ��ð�װ�嵥 
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
        //����д�����Ϣ�����Ե�ʱ����Դ�ӡ�����鿴
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
     * ��ò�Ʒ������Ϣ
     */
    public static function getPromotionInfo($paramArr){
		$options = array(
			'skuId'         => '',    #Skuids ���Դ�����
			'webSite'        => 1,    #վ��,1-������2-ǧѰ��3-ept���⹺ 
			'origin'         => 1,    #��Դ,1-��վ���ס�2-�ֻ�����ipad 
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        /**
         * �ֶ�
         * state ���¹�״̬     0���¹�1���Ϲ�2�����Ϲ񣨻�����Ϣ�걸������û����ʽ�Ϲ񣩣�10��POPSKUɾ��
         * is_delete  �Ƿ���Ч  0��Ч��1��Ч 
         * 
         * �����ֶο��Բ鿴 http://help.jd.com/jos/question-628.html 
         */
        
        $cols = 'skuId,webSite,origin';
        $apiParam = array(
            'skuId'   => $skuId, #skuId
            'webSite' => $webSite,
            'origin'  => $origin,
        );
        $out = self::fetchData(array('apiName'=>'jingdong.ware.promotionInfo.get','data'=>$apiParam)); 
        if(!$out)return false;
        
        //����д�����Ϣ�����Ե�ʱ����Դ�ӡ�����鿴
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
     * ��ò�Ʒ�۸�
     */
    public static function getPrice($paramArr){
		$options = array(
			'skuId'         => '',    #Skuids ���Դ�����
		);        
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $skuIdStr = "J_".$skuId;
        $apiParam = array(
            'sku_id'   => $skuIdStr, #skuId
        );
        $out = self::fetchData(array('apiName'=>'jingdong.ware.price.get','data'=>$apiParam)); 
        if(!$out)return false;
        
        //����д�����Ϣ�����Ե�ʱ����Դ�ӡ�����鿴
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
        $sysParam = array(
            'app_key'    => self::$_APPKEY,
            'method'     => $apiName,
            'timestamp'  => date("Y-m-d H:i:s"),
            'v'          => "2.0",//�汾��
        );
        
        
        //����һ��api�Ĳ����Ĵ���
        $apiParam = array();
        ksort($data);
        $apiParam['360buy_param_json'] = json_encode($data);
        //������ǩ������
        $sysParam["sign"] = self::generateSign(array_merge($sysParam, $apiParam));
        $url               = self::buildUrl($sysParam); //ƴװURL
        
        //ȥ��������
        $responseData = self::doCurl(array('url'=> $url,'apiParams'=>$apiParam, 'timeout'=> 30));
        if($responseData){
            #���������������һЩ�����ַ�������Щ�ַ����д���
            $responseData = str_replace('	', '', $responseData);
            return api_json_decode($responseData);
        }else{
            return false;
        }
    }
    
    /**
     *
     * ����http����
     */
    private static function doCurl($paramArr){
		$options = array(
			'url'         => '', #����ĵ�ַ
			'apiParams'   => false,  #���ݲ���
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
     *  �Բ�������ǩ��
     */
    private static function generateSign($params){
        if ($params != null) { //�����������������ĸ�Ⱥ�˳������
            ksort($params);
            //�����ַ�����ʼ ��β���������ַ���
            $stringToBeSigned = self::$_APPSECRET;
            //�����в������Ͳ���ֵ����һ��
            foreach ($params as $k => $v) {
                $stringToBeSigned .= "$k$v";
            }
            unset($k, $v);
            //��venderKey�����ַ���������
            $stringToBeSigned .= self::$_APPSECRET;
        } else {
            //�����ַ�����ʼ ��β���������ַ���
            $stringToBeSigned = self::$_APPSECRET;
            //��venderKey�����ַ���������
            $stringToBeSigned .= self::$_APPSECRET;
        }
        //ʹ��MD5���м��ܣ���ת���ɴ�д
//        echo $stringToBeSigned;
//        $stringToBeSigned = mb_convert_encoding($stringToBeSigned, "UTF-8");
        return strtoupper(md5($stringToBeSigned));
    }
    

    /**
     * ƴװURL
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

