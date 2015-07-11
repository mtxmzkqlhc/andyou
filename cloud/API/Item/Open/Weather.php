<?php
/**
* 中国天气网接口
* @author weixj
* @copyright (c) 2014-11-04
*/
class API_Item_Open_Weather
{
    #appid和私钥
    private static $appId       = '4e73162a01479047';
    private static $privateKey  = '0d8caa_SmartWeatherAPI_6054bb9';
    #请求的基础url
    private static $baseUrl     = 'http://open.weather.com.cn/data/';
    #风向编码表
    private static $windDirection   = array(
        '0'=>array('cn'=>'无持续风向', 'en'=>'No wind'),
        '1'=>array('cn'=>'东北风', 'en'=>'Northeast'),
        '2'=>array('cn'=>'东风', 'en'=>'East'),
        '3'=>array('cn'=>'东南风', 'en'=>'Southeast'),
        '4'=>array('cn'=>'南风', 'en'=>'South'),
        '5'=>array('cn'=>'西南风', 'en'=>'Southwest'),
        '6'=>array('cn'=>'西风', 'en'=>'West'),
        '7'=>array('cn'=>'西北风', 'en'=>'Northwest'),
        '8'=>array('cn'=>'北风', 'en'=>'North'),
        '9'=>array('cn'=>'旋转风', 'en'=>'Whirl wind'),
    );
    
    #风力编码表
    private static $windForce = array ( 
        '0' => array ( 'cn' => '微风', 'en' => '<10m/h', ), 
        '1' => array ( 'cn' => '3-4 级', 'en' => '10~17m/h', ), 
        '2' => array ( 'cn' => '4-5 级', 'en' => '17~25m/h', ), 
        '3' => array ( 'cn' => '5-6 级', 'en' => '25~34m/h', ), 
        '4' => array ( 'cn' => '6-7 级', 'en' => '34~43m/h', ), 
        '5' => array ( 'cn' => '7-8 级', 'en' => '43~54m/h', ), 
        '6' => array ( 'cn' => '8-9 级', 'en' => '54~65m/h', ), 
        '7' => array ( 'cn' => '9-10 级', 'en' => '65~77m/h', ), 
        '8' => array ( 'cn' => '10-11 级', 'en' => '77~89m/h', ), 
        '9' => array ( 'cn' => '11-12 级', 'en' => '89~102m/h', ), 
    );

    #天气现象编码表
    private static $weatchConf = array (
        '00' => array ( 'cn' => '晴', 'en' => 'Sunny', ),
        '01' => array ( 'cn' => '多云', 'en' => 'Cloudy', ), 
        '02' => array ( 'cn' => '阴', 'en' => 'Overcast', ), 
        '03' => array ( 'cn' => '阵雨', 'en' => 'Shower', ), 
        '04' => array ( 'cn' => '雷阵雨', 'en' => 'Thundershower', ), 
        '05' => array ( 'cn' => '雷阵雨伴有冰雹', 'en' => 'Thundershower with hail', ), 
        '06' => array ( 'cn' => '雨夹雪', 'en' => 'Sleet', ), 
        '07' => array ( 'cn' => '小雨', 'en' => 'Light rain', ), 
        '08' => array ( 'cn' => '中雨', 'en' => 'Moderate rain', ), 
        '09' => array ( 'cn' => '大雨', 'en' => 'Heavy rain', ), 
        '10' => array ( 'cn' => '暴雨', 'en' => 'Storm', ), 
        '11' => array ( 'cn' => '大暴雨', 'en' => 'Heavy storm', ), 
        '12' => array ( 'cn' => '特大暴雨', 'en' => 'Severe storm', ), 
        '13' => array ( 'cn' => '阵雪', 'en' => 'Snow flurry', ), 
        '14' => array ( 'cn' => '小雪', 'en' => 'Light snow', ), 
        '15' => array ( 'cn' => '中雪', 'en' => 'Moderate snow', ), 
        '16' => array ( 'cn' => '大雪', 'en' => 'Heavy snow', ), 
        '17' => array ( 'cn' => '暴雪', 'en' => 'Snowstorm', ), 
        '18' => array ( 'cn' => '雾', 'en' => 'Foggy', ), 
        '19' => array ( 'cn' => '冻雨', 'en' => 'Ice rain', ), 
        '20' => array ( 'cn' => '沙尘暴', 'en' => 'Duststorm', ), 
        '21' => array ( 'cn' => '小到中雨', 'en' => 'Light to moderate rain', ), 
        '22' => array ( 'cn' => '中到大雨', 'en' => 'Moderate to heavy rain', ), 
        '23' => array ( 'cn' => '大到暴雨', 'en' => 'Heavy rain to storm', ), 
        '24' => array ( 'cn' => '暴雨到大暴雨', 'en' => 'Storm to heavy storm', ), 
        '25' => array ( 'cn' => '大暴雨到特大暴雨', 'en' => 'Heavy to severe storm', ), 
        '26' => array ( 'cn' => '小到中雪', 'en' => 'Light to moderate snow', ), 
        '27' => array ( 'cn' => '中到大雪', 'en' => 'Moderate to heavy snow', ), 
        '28' => array ( 'cn' => '大到暴雪', 'en' => 'Heavy snow to snowstorm', ), 
        '29' => array ( 'cn' => '浮尘', 'en' => 'Dust', ), 
        '30' => array ( 'cn' => '扬沙', 'en' => 'Sand', ), 
        '31' => array ( 'cn' => '强沙尘暴', 'en' => 'Sandstorm', ), 
        '53' => array ( 'cn' => '霾', 'en' => 'Haze', ), 
        '99' => array ( 'cn' => '无', 'en' => 'Unknown', )
    );
    #直辖市
    private static $municipalities  = array('10101','10102','10103','10104');
    private static $municipal       = array('北京','上海','天津','重庆');
    #海南省
    private static $hainanProvincId = '10131';

    /**
     * 根据传入参数获取加密后的接口地址
     */
    public static function getCryptUrl($paramArr){
        $options = array(
            'areaid'=>0,         #地区id
            'type'  =>'observe', #类型 预警:alarm 实况:observe 指数:index   常规预报:forecast3d
            'date'  =>'',        #YmdHis
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        if(!$date) $date = date('');
        #拼接令牌
        $publicKey  = self::$baseUrl.'?areaid='.$areaid.'&type='.$type.'&date='.$date.'&appid='.self::$appId;
        #加密key
        $key        = base64_encode(hash_hmac('sha1',$publicKey ,self::$privateKey ,TRUE));
        #拼接url
        $appidParam = substr(self::$appId, 0,6);
        $url 		= self::$baseUrl.'?areaid='.$areaid.'&type='.$type.'&date='.$date.'&appid='.$appidParam.'&key='.urlencode($key); 
        return $url;
    }
    
    
    /**
     * 获取对应类型的天气信息
     */
    public static function getInfo($paramArr){
        $options = array(
            'areaid'    =>0,         #地区id
            'type'      =>'observe', #类型 预警:alarm 实况:observe 指数:index   常规预报:forecast3d
            'date'      =>'',        #YmdHis
            'dataType'  =>0,         #数据类型 0:原始json 1:关联数组
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        $cryptUrl   = self::getCryptUrl($options);
        $outArr     = array();
        if(!$cryptUrl) return $outArr;
        switch ($type){
            #实况
            case 'observe':
                return self::getObserve(array(
                    'url'       =>$cryptUrl,  #url
                    'dataType'  =>$dataType,  #0:原始json 1:关联数组
                ));
                break;
                #指数
            case 'index':
                return self::getIndex(array(
                    'url'       =>$cryptUrl,  #url
                    'dataType'  =>$dataType,  #0:原始json 1:关联数组
                ));
                break;
                #3天内预报
            case 'forecast3d':
                return self::getForecast3d(array(
                    'url'       =>$cryptUrl,  #url
                    'dataType'  =>$dataType,  #0:原始json 1:关联数组
                ));
                break;
                #预警基本无数据
            case 'alarm':
            default:
                return $outArr;
                break;
        }
    
    }
    

    /**
     * 获取实况天气
     */
    public static function getObserve($paramArr){
        $options = array(
            'url'       =>'', #url
            'dataType'  =>0,  #0:原始json 1:关联数组
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        if(!$url) return null;
        $json = self::getCurlData(array(
            'url'   =>$url,#url
            'retry' =>3, #请求失败时的重试次数
        ));
        if($dataType==0){
           return  $json;
        }
        #转换为数组
        $data = api_json_decode($json, true);
        if(empty($data['l'])) return null;
        $data = $data['l'];
        $outArr = array();
        #当前温度
        $outArr['temperature']      = isset($data['l1']) ? $data['l1'] : '';
        #当前湿度
        $outArr['humidity']         = isset($data['l2']) ? $data['l2'] : '';
        #当前风力
        $outArr['windScale']        = isset($data['l3']) ? $data['l3'] : '';
        #当前风向
        $outArr['windDirection']    = isset($data['l4']) ? strval($data['l4']) : '';
        $outArr['windDirectionName']= '';
        if(array_key_exists($outArr['windDirection'], self::$windDirection)){
            $outArr['windDirectionName']= self::$windDirection[$outArr['windDirection']]['cn'];
        }
        #发布时间
        $outArr['pubDate']          = isset($data['l7']) ? $data['l7'] : '';
        return $outArr;
    }
    
    /**
     * 获取天气指数
     */
    public static function getIndex($paramArr){
        $options = array(
            'url'       =>'', #url
            'dataType'  =>0,  #0:原始json 1:关联数组
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        if(!$url) return null;
        $json = self::getCurlData(array(
            'url'   =>$url,#url
            'retry' =>3, #请求失败时的重试次数
        ));
        if($dataType==0){
            return  $json;
        }
        #转换为数组
        $data = api_json_decode($json, true);
        if(empty($data['i'])) return null;
        $data = $data['i'];
        $outArr = array();
        foreach ($data as $key=>$val){
            $tempArr = array();
            $tempArr['en']      = isset($val['i1']) ? $val['i1'] : '';
            $tempArr['cn']      = isset($val['i2']) ? $val['i2'] : '';
            $tempArr['alias']   = isset($val['i3']) ? $val['i3'] : '';
            $tempArr['level']   = isset($val['i4']) ? $val['i4'] : '';
            $tempArr['info']    = isset($val['i5']) ? $val['i5'] : '';
            $outArr[]           = $tempArr;
        }
        return $outArr; 
    }
    
    /**
     * 获取实时天气信息
     */
    public static function getForecast3d($paramArr){
        $options = array(
            'url'       =>'', #url
            'dataType'  =>0,  #0:原始json 1:关联数组
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        if(!$url) return null;
        $json = self::getCurlData(array(
            'url'   =>$url,#url
            'retry' =>3, #请求失败时的重试次数
        ));
        if($dataType==0){
            return  $json;
        }
        #转换为数组
        $data = api_json_decode($json, true);
        if(!$data || empty($data['f'])) return null;
        $outArr = array(
            'cityInfo'=>array(),
            'forecast'=>array()
        );
        #城市信息
        if(!empty($data['c'])){
            $cityInfo =$data['c'];
            $tempArr = array();
            $tempArr['areaid']      = isset($cityInfo['c1']) ? $cityInfo['c1'] : '';
            $tempArr['cityEn']      = isset($cityInfo['c2']) ? $cityInfo['c2'] : '';
            $tempArr['cityCn']      = isset($cityInfo['c3']) ? $cityInfo['c3'] : '';
            $tempArr['districtEn']  = isset($cityInfo['c4']) ? $cityInfo['c4'] : '';
            $tempArr['districtCn']  = isset($cityInfo['c5']) ? $cityInfo['c5'] : '';
            $tempArr['provinceEn']  = isset($cityInfo['c6']) ? $cityInfo['c6'] : '';
            $tempArr['provinceCn']  = isset($cityInfo['c7']) ? $cityInfo['c7'] : '';
            $tempArr['countryEn']   = isset($cityInfo['c8']) ? $cityInfo['c8'] : '';
            $tempArr['countryCn']   = isset($cityInfo['c9']) ? $cityInfo['c9'] : '';
            $tempArr['cityLevel']   = isset($cityInfo['c10']) ? $cityInfo['c10'] : '';
            $tempArr['areaCode']    = isset($cityInfo['c11']) ? $cityInfo['c11'] : '';
            $tempArr['postCode']    = isset($cityInfo['c12']) ? $cityInfo['c12'] : '';
            $tempArr['longitude']   = isset($cityInfo['c13']) ? $cityInfo['c13'] : '';
            $tempArr['latitude']    = isset($cityInfo['c14']) ? $cityInfo['c14'] : '';
            $tempArr['altitude']    = isset($cityInfo['c15']) ? $cityInfo['c15'] : '';
            $tempArr['radar']       = isset($cityInfo['c16']) ? $cityInfo['c16'] : '';
            $outArr['cityInfo']     = $tempArr;
        }
        #预报信息
        $foreArr    = array();
        $forecast   = $data['f'];
        $tempArr['pubDate']         = isset($data['f0']) ? $data['f0'] : '';
        if(!empty($forecast['f1'])){
            foreach ($forecast['f1'] as $val){
                $row = array();
                $row['dayNo']       = isset($val['fa']) ? strval($val['fa']) : '';
                $row['dayWeather']  = '';
                if(array_key_exists($row['dayNo'], self::$weatchConf)){
                    $row['dayWeather'] = self::$weatchConf[$row['dayNo']]['cn'];
                }
                $row['eveNo']     = isset($val['fb']) ? strval($val['fb']) : '';
                $row['eveWeather']  = '';
                if(array_key_exists($row['eveNo'], self::$weatchConf)){
                    $row['eveWeather'] = self::$weatchConf[$row['eveNo']]['cn'];
                }
                #天气类型
                $row['dayTemperature']      = isset($val['fc']) ? $val['fc'] : '';
                $row['eveTemperature']      = isset($val['fd']) ? $val['fd'] : '';
                #早晚风向
                $row['dayDirection']        = isset($val['fe']) ? strval($val['fe']) : '';
                $row['dayDirectionName']    = '';
                if(array_key_exists($row['dayDirection'], self::$windDirection)){
                    $row['dayDirectionName']= self::$windDirection[$row['dayDirection']]['cn'];
                }
                $row['eveDirection']        = isset($val['ff']) ? strval($val['ff']) : '';
                $row['eveDirectionName']    = '';
                if(array_key_exists($row['eveDirection'], self::$windDirection)){
                    $row['eveDirectionName']= self::$windDirection[$row['eveDirection']]['cn'];
                }
                #风力
                $row['dayScale']    = isset($val['fg']) ? $val['fg'] : '';
                $row['dayScaleName']='';
                if(array_key_exists($row['dayScale'], self::$windForce)){
                    $row['dayScaleName']= self::$windForce[$row['dayScale']]['cn'];
                }
                $row['eveScale']    = isset($val['fh']) ? $val['fh'] : '';
                $row['eveScaleName']= '';
                if(array_key_exists($row['eveScale'], self::$windForce)){
                    $row['eveScaleName']= self::$windForce[$row['eveScale']]['cn'];
                }
                #日出日落
                $row['sunrise']     = isset($val['fi']) ? $val['fi'] : '';
                $foreArr[]  = $row;
            }
        }
        $outArr['forecast'] = $foreArr;
        return $outArr;
    }
    
    /**
     * 请求天气网数据
     */
    private static function getCurlData($paramArr){
        $options = array(
            'url'   =>'',   #url
            'retry' =>3,    #请求失败时的重试次数
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        if(!$url) return null;
        #最多通信3次
        $requestFlag = 0;
        do{
            $json = API_Http::curlPage(array(
                'url'    =>$url,
                'timeout'=>5,
            ));
            $requestFlag++;
        }while(!$json && $requestFlag < $retry);
        if(!$json || $json=='data error'){
            return null;
        }
        return $json;
    }

    /**
     * 获取天气网地区信息
     * @note  中国天气网地区奇葩点： 1、直辖市地级市id为00   2、海南省 县区id是9位完整地区编号
     * @param 获取省份列表    $provinceId=''  $districtId='00' $cityId='00'
     * @param 获取某省的地级市 $provinceId=num $districtId=num  $cityId='00'
     * @param 获取某省市的县区 $provinceId=num $districtId=num  $cityId=''
     */
    public static function getAreaInfo($paramArr){
        $options = array(
            'provinceId'    =>'',   #省份id 或 ''
            'districtId'    =>'',   #地级市id 或''
            'cityId'        =>'',   #县区id 或''
            'name'          =>'',   #城市名称 整数或''
            'limit'         =>'100',#limit条件
            'isDebug'       =>0,    #是否打印sql
            'refresh'       =>0,    #是否刷新缓存
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        #缓存key
        $cacheKey = 'zol_weather_area_p'.$provinceId.'_d'.$districtId.'_c'.$cityId.'_n'.$name.'_l'.$limit;
        #不刷新时默认返回缓存
        if(!$refresh){
            $dataCache = API_Item_Kv_MongoCenter::get(array(
                'module'=> 'cms',
                'key'	=> $cacheKey,
            ));
            if($dataCache){
                return $dataCache;
            }
        }
        
        $whereSql ='';
        #是否直辖市
        $isMunicipalit = in_array($provinceId, self::$municipalities);
        #传入了省份id 这么做是为了取列表的情况，下同
        if($provinceId!==''){
            $whereSql .=" and province_id='{$provinceId}' ";
            #直辖市地区id始终为00
            if($isMunicipalit){
                $districtId='00';
            }
        }
        #传入了地区id
        if($districtId!==''){
            $whereSql .=" and district_id='{$districtId}' ";
        }else if(!$isMunicipalit && $cityId==='00'){
            #非直辖市 获取地级市列表传参 $districtId='' $cityId='00'
            $whereSql .=" and district_id<>'00' ";
        } 
        #传入了城市id
        if($cityId!==''){
            $whereSql .=" and city_id='{$cityId}' ";
        }else{
            #获取县区列表传参 $districtId=num $cityId=''
            $whereSql .=" and city_id<>'00' ";
        }  
        #传入了城市名
        if($name!==''){
            $whereSql .=" and name='{$name}' ";
        }

        $sql =" select province_id as provinceId, district_id as districtId, city_id as cityId, name from z_weather_area where 1 {$whereSql}";
        #limit
        if($limit)  $sql .=" limit {$limit} ";
        #打印sql
        if($isDebug) echo $sql;
        $db     = API_Db_ArticleNpro::instance();
        $data   = $db->getAll($sql);
        
        $data   = $data ? $data : null;
        #设置缓存
        API_Item_Kv_MongoCenter::set(array(
            'module'=> 'cms',	#生命期
            'key'	=> $cacheKey,	#key
            'life'  => 86400,     	#生命期 86400
            'data'  => $data,       #数据
        ));
        return $data;
    }
    
    /**
     * 获取天气网的地区id 
     */
    public static function getAreaId($paramArr){
        $options = array(
            'provinceId'    =>'',   #省份id
            'districtId'    =>'',   #地级市id 
            'cityId'        =>'',   #县区id
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        $areaId = '';
        #海南省id是9位
        if(strlen($cityId)==9){
            return $cityId;
        }
        #直辖市不走寻常路
        if(in_array($provinceId, self::$municipalities)){
            $areaId = $provinceId.$cityId.$districtId;
        }else{
            $areaId = $provinceId.$districtId.$cityId;
        }
        return $areaId;
    }

}