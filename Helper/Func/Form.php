<?php
/**
 * Form表单相关函数
 * 仲伟涛
 * 2013-11-27
 */
class Helper_Func_Form extends Helper_Abstract {
    
    
    /**
     * 获得省份信息，封装成select层的形式
     */
    public static function getProvinceSelect($paramArr) {
		$options = array(
			'type'       =>  1,    #select类型 0:返回原始数组数据 1:默认select 2:div形式  3,select的原生option
            'sel'        =>  false,#选择的元素
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        #获得省份信息
        $data = Helper_Func_Data::getProvince(array());
        if($type == 0){
            return $data;
        }
        #封装返回
        return self::transFirstLetterArr(array('data'=>$data,'type'=>$type,'sel'=>$sel));
    }
    
     /**
     * 获得省份信息，封装成select层的形式
     */
    public static function getCitySelect($paramArr) {
		$options = array(
			'provinceId'       =>  0,    #城市ID
			'type'             =>  1,    #select类型 1:默认select 2:div形式 
            'sel'              =>  false,#选择的元素
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        #获得城市信息
        if($provinceId){
            $data = API_Item_Pro_Area::getCityInfo(array('provinceId'=>$provinceId));
        }
        if(!$provinceId || !$data){
            $data = array(0=>'请选择');
        }
        #封装返回
        return self::transFirstLetterArr(array('data'=>$data,'type'=>$type,'sel'=>$sel));
    }
    
    
     /**
     * 获得产品子类信息，封装成select层的形式
     */
    public static function getProCateSelect($paramArr) {
		$options = array(
			'type'       =>  1,    #select类型 1:默认select 2:div形式  4:原生态select，按照字母排序的
			'sel'        =>  false,      #选中状态
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        #获得城市信息
        $data = API_Item_Pro_Cate::getSubCate(array('showHiden'=>0));
        $outArr = array();
        if($data){
            foreach($data as $k => $d){
                $outArr[$k] = trim($d["name"]);
            }
        }
        #封装返回
        return self::transFirstLetterArr(array('data'=>$outArr,'type'=>$type,'sel'=>$sel));
    }
    
    /**
     * 获得广告频道信息，封装select层形式
     * lvj 2014-4-9
     */
    public static function getChannelSelect($paramArr) {
        $options = array(
                'type'       =>  1,    #select类型 1:默认select 2:div形式  4:原生态select，按照字母排序的
                'sel'        =>  false,      #选中状态
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        #频道信息
        $data = Helper_Advertise_Adspace::getChannelInfo();
        $outArr = array();
        if($data){
            foreach($data as $k => $d){
                $outArr[$k] = trim($d);
            }
        }
        #封装返回
        return self::transFirstLetterArr(array('data'=>$outArr,'type'=>$type,'sel'=>$sel));       
    }
    /**
     * 获得广告频道下页面类型信息，封装select层形式
     * lvj 2014-4-9
     */
    public static function getPageTypeSelect($paramArr) {
        $options = array(
                'type'      =>  1,    #select类型 1:默认select 2:div形式  4:原生态select，按照字母排序的
                'sel'       =>  false,      #选中状态
                'channelId' =>  0,  #频道ID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        #频道下页面类型信息
        $data = Helper_Advertise_Adspace::getPageTypeInfo(array('channelId'=>$channelId));
        $outArr = array();
        if($data){
            foreach($data as $k => $d){
                $outArr[$k] = trim($d);
            }
        }
        #封装返回
        return self::transFirstLetterArr(array('data'=>$outArr,'type'=>$type,'sel'=>$sel));       
    }
    /**
     * 获取用户品牌信息，封装select层形式
     * lvj 2014-4-16
     */
    public static function getUserManuSelect($paramArr) {
        $options = array(
                'type'      =>  1,    #select类型 1:默认select 2:div形式  4:原生态select，按照字母排序的
                'userId'    => '',
                'sel'       =>  false,      #选中状态
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        #频道下页面类型信息
        $data = Helper_User_Info::getUserManu(array(
            'userId'    => $userId,
        ));   
        $outArr = array();

        if($data){
            foreach($data as $k => $d){
                $outArr[$d['manuId']] = trim($d['manuName']);
            }
        }
        #封装返回
        return self::transFirstLetterArr(array('data'=>$outArr,'type'=>$type,'sel'=>$sel));       
    }    
    /**
     * 获取某品牌下的用户产品线信息，封装select层形式
     * lvj 2014-4-16
     */
    public static function getUserSubcateSelect($paramArr) {
        $options = array(
                'type'      =>  1,    #select类型 1:默认select 2:div形式  4:原生态select，按照字母排序的
                'userId'    => '',
                'manuId'    => 0,
                'sel'       =>  false,      #选中状态
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        #频道下页面类型信息
        $data = Helper_Pro_Pro::getManuSetSubcate(array(
                    'manuId'      => $manuId,    #where条件
                    'userId'      => $userId,
                ));
        $outArr = array();
        if($data){
            foreach($data as $k => $d){
                $outArr[$d['subcateId']] = trim($d['subcateName']);
            }
        }
        #封装返回
        return self::transFirstLetterArr(array('data'=>$outArr,'type'=>$type,'sel'=>$sel));       
    }    
    /**
     * 将数组装换的首字母分装的数组
     */
    public static function transFirstLetterArr($paramArr) {
		$options = array(
			'data'       =>  array(),    #数组
			'type'       =>  1,          #select类型 1:默认select 2:div形式 
			'sel'        =>  false,      #选中状态
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $outArr = array();
        
        if($data){
            
            if($type == 1){#1:默认select 形式
                return api_json_encode($data);
            }elseif($type == 3){ #原生态select的option
                $out = '';                
                foreach($data as $k => $wd){
                    $seled = ($sel == $k) ? "selected" : "";
                    $out .= "<option value='{$k}' {$seled}>{$wd}</option>";
                }                
                return mb_convert_encoding($out, "UTF-8","GBK");
                
            }elseif($type == 2){ #div select 形式
                foreach($data as $k => $wd){
                    $l = API_Item_Base_String::getFirstLetter(array('input'=>$wd)); #获得首字母
                    if(!isset($outArr[$l])){
                        $outArr[$l] = array(
                            'name' => $l,
                            'cons' => array()
                        );
                    }
                    $outArr[$l]['cons'][] = $wd;
                }
                //var_dump($outArr);
                sort($outArr);
                return api_json_encode($outArr);
            }elseif($type == 4){#原生态select，按照字母排序的
                foreach($data as $k => $wd){
                    $l = API_Item_Base_String::getFirstLetter(array('input'=>$wd)); #获得首字母
                    $outArr[$k] = $l."_".$wd;
                }
                asort($outArr);
                $out = '';                
                foreach($outArr as $k => $wd){
                    $seled = ($sel == $k) ? "selected" : "";
                    $out .= "<option value='{$k}' {$seled}>{$wd}</option>";
                    
                }
                return $out;
                
            }
        }
        return '';        
        
    }
    
}
