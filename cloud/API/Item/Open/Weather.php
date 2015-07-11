<?php
/**
* �й��������ӿ�
* @author weixj
* @copyright (c) 2014-11-04
*/
class API_Item_Open_Weather
{
    #appid��˽Կ
    private static $appId       = '4e73162a01479047';
    private static $privateKey  = '0d8caa_SmartWeatherAPI_6054bb9';
    #����Ļ���url
    private static $baseUrl     = 'http://open.weather.com.cn/data/';
    #��������
    private static $windDirection   = array(
        '0'=>array('cn'=>'�޳�������', 'en'=>'No wind'),
        '1'=>array('cn'=>'������', 'en'=>'Northeast'),
        '2'=>array('cn'=>'����', 'en'=>'East'),
        '3'=>array('cn'=>'���Ϸ�', 'en'=>'Southeast'),
        '4'=>array('cn'=>'�Ϸ�', 'en'=>'South'),
        '5'=>array('cn'=>'���Ϸ�', 'en'=>'Southwest'),
        '6'=>array('cn'=>'����', 'en'=>'West'),
        '7'=>array('cn'=>'������', 'en'=>'Northwest'),
        '8'=>array('cn'=>'����', 'en'=>'North'),
        '9'=>array('cn'=>'��ת��', 'en'=>'Whirl wind'),
    );
    
    #���������
    private static $windForce = array ( 
        '0' => array ( 'cn' => '΢��', 'en' => '<10m/h', ), 
        '1' => array ( 'cn' => '3-4 ��', 'en' => '10~17m/h', ), 
        '2' => array ( 'cn' => '4-5 ��', 'en' => '17~25m/h', ), 
        '3' => array ( 'cn' => '5-6 ��', 'en' => '25~34m/h', ), 
        '4' => array ( 'cn' => '6-7 ��', 'en' => '34~43m/h', ), 
        '5' => array ( 'cn' => '7-8 ��', 'en' => '43~54m/h', ), 
        '6' => array ( 'cn' => '8-9 ��', 'en' => '54~65m/h', ), 
        '7' => array ( 'cn' => '9-10 ��', 'en' => '65~77m/h', ), 
        '8' => array ( 'cn' => '10-11 ��', 'en' => '77~89m/h', ), 
        '9' => array ( 'cn' => '11-12 ��', 'en' => '89~102m/h', ), 
    );

    #������������
    private static $weatchConf = array (
        '00' => array ( 'cn' => '��', 'en' => 'Sunny', ),
        '01' => array ( 'cn' => '����', 'en' => 'Cloudy', ), 
        '02' => array ( 'cn' => '��', 'en' => 'Overcast', ), 
        '03' => array ( 'cn' => '����', 'en' => 'Shower', ), 
        '04' => array ( 'cn' => '������', 'en' => 'Thundershower', ), 
        '05' => array ( 'cn' => '��������б���', 'en' => 'Thundershower with hail', ), 
        '06' => array ( 'cn' => '���ѩ', 'en' => 'Sleet', ), 
        '07' => array ( 'cn' => 'С��', 'en' => 'Light rain', ), 
        '08' => array ( 'cn' => '����', 'en' => 'Moderate rain', ), 
        '09' => array ( 'cn' => '����', 'en' => 'Heavy rain', ), 
        '10' => array ( 'cn' => '����', 'en' => 'Storm', ), 
        '11' => array ( 'cn' => '����', 'en' => 'Heavy storm', ), 
        '12' => array ( 'cn' => '�ش���', 'en' => 'Severe storm', ), 
        '13' => array ( 'cn' => '��ѩ', 'en' => 'Snow flurry', ), 
        '14' => array ( 'cn' => 'Сѩ', 'en' => 'Light snow', ), 
        '15' => array ( 'cn' => '��ѩ', 'en' => 'Moderate snow', ), 
        '16' => array ( 'cn' => '��ѩ', 'en' => 'Heavy snow', ), 
        '17' => array ( 'cn' => '��ѩ', 'en' => 'Snowstorm', ), 
        '18' => array ( 'cn' => '��', 'en' => 'Foggy', ), 
        '19' => array ( 'cn' => '����', 'en' => 'Ice rain', ), 
        '20' => array ( 'cn' => 'ɳ����', 'en' => 'Duststorm', ), 
        '21' => array ( 'cn' => 'С������', 'en' => 'Light to moderate rain', ), 
        '22' => array ( 'cn' => '�е�����', 'en' => 'Moderate to heavy rain', ), 
        '23' => array ( 'cn' => '�󵽱���', 'en' => 'Heavy rain to storm', ), 
        '24' => array ( 'cn' => '���굽����', 'en' => 'Storm to heavy storm', ), 
        '25' => array ( 'cn' => '���굽�ش���', 'en' => 'Heavy to severe storm', ), 
        '26' => array ( 'cn' => 'С����ѩ', 'en' => 'Light to moderate snow', ), 
        '27' => array ( 'cn' => '�е���ѩ', 'en' => 'Moderate to heavy snow', ), 
        '28' => array ( 'cn' => '�󵽱�ѩ', 'en' => 'Heavy snow to snowstorm', ), 
        '29' => array ( 'cn' => '����', 'en' => 'Dust', ), 
        '30' => array ( 'cn' => '��ɳ', 'en' => 'Sand', ), 
        '31' => array ( 'cn' => 'ǿɳ����', 'en' => 'Sandstorm', ), 
        '53' => array ( 'cn' => '��', 'en' => 'Haze', ), 
        '99' => array ( 'cn' => '��', 'en' => 'Unknown', )
    );
    #ֱϽ��
    private static $municipalities  = array('10101','10102','10103','10104');
    private static $municipal       = array('����','�Ϻ�','���','����');
    #����ʡ
    private static $hainanProvincId = '10131';

    /**
     * ���ݴ��������ȡ���ܺ�Ľӿڵ�ַ
     */
    public static function getCryptUrl($paramArr){
        $options = array(
            'areaid'=>0,         #����id
            'type'  =>'observe', #���� Ԥ��:alarm ʵ��:observe ָ��:index   ����Ԥ��:forecast3d
            'date'  =>'',        #YmdHis
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        if(!$date) $date = date('');
        #ƴ������
        $publicKey  = self::$baseUrl.'?areaid='.$areaid.'&type='.$type.'&date='.$date.'&appid='.self::$appId;
        #����key
        $key        = base64_encode(hash_hmac('sha1',$publicKey ,self::$privateKey ,TRUE));
        #ƴ��url
        $appidParam = substr(self::$appId, 0,6);
        $url 		= self::$baseUrl.'?areaid='.$areaid.'&type='.$type.'&date='.$date.'&appid='.$appidParam.'&key='.urlencode($key); 
        return $url;
    }
    
    
    /**
     * ��ȡ��Ӧ���͵�������Ϣ
     */
    public static function getInfo($paramArr){
        $options = array(
            'areaid'    =>0,         #����id
            'type'      =>'observe', #���� Ԥ��:alarm ʵ��:observe ָ��:index   ����Ԥ��:forecast3d
            'date'      =>'',        #YmdHis
            'dataType'  =>0,         #�������� 0:ԭʼjson 1:��������
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        $cryptUrl   = self::getCryptUrl($options);
        $outArr     = array();
        if(!$cryptUrl) return $outArr;
        switch ($type){
            #ʵ��
            case 'observe':
                return self::getObserve(array(
                    'url'       =>$cryptUrl,  #url
                    'dataType'  =>$dataType,  #0:ԭʼjson 1:��������
                ));
                break;
                #ָ��
            case 'index':
                return self::getIndex(array(
                    'url'       =>$cryptUrl,  #url
                    'dataType'  =>$dataType,  #0:ԭʼjson 1:��������
                ));
                break;
                #3����Ԥ��
            case 'forecast3d':
                return self::getForecast3d(array(
                    'url'       =>$cryptUrl,  #url
                    'dataType'  =>$dataType,  #0:ԭʼjson 1:��������
                ));
                break;
                #Ԥ������������
            case 'alarm':
            default:
                return $outArr;
                break;
        }
    
    }
    

    /**
     * ��ȡʵ������
     */
    public static function getObserve($paramArr){
        $options = array(
            'url'       =>'', #url
            'dataType'  =>0,  #0:ԭʼjson 1:��������
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        if(!$url) return null;
        $json = self::getCurlData(array(
            'url'   =>$url,#url
            'retry' =>3, #����ʧ��ʱ�����Դ���
        ));
        if($dataType==0){
           return  $json;
        }
        #ת��Ϊ����
        $data = api_json_decode($json, true);
        if(empty($data['l'])) return null;
        $data = $data['l'];
        $outArr = array();
        #��ǰ�¶�
        $outArr['temperature']      = isset($data['l1']) ? $data['l1'] : '';
        #��ǰʪ��
        $outArr['humidity']         = isset($data['l2']) ? $data['l2'] : '';
        #��ǰ����
        $outArr['windScale']        = isset($data['l3']) ? $data['l3'] : '';
        #��ǰ����
        $outArr['windDirection']    = isset($data['l4']) ? strval($data['l4']) : '';
        $outArr['windDirectionName']= '';
        if(array_key_exists($outArr['windDirection'], self::$windDirection)){
            $outArr['windDirectionName']= self::$windDirection[$outArr['windDirection']]['cn'];
        }
        #����ʱ��
        $outArr['pubDate']          = isset($data['l7']) ? $data['l7'] : '';
        return $outArr;
    }
    
    /**
     * ��ȡ����ָ��
     */
    public static function getIndex($paramArr){
        $options = array(
            'url'       =>'', #url
            'dataType'  =>0,  #0:ԭʼjson 1:��������
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        if(!$url) return null;
        $json = self::getCurlData(array(
            'url'   =>$url,#url
            'retry' =>3, #����ʧ��ʱ�����Դ���
        ));
        if($dataType==0){
            return  $json;
        }
        #ת��Ϊ����
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
     * ��ȡʵʱ������Ϣ
     */
    public static function getForecast3d($paramArr){
        $options = array(
            'url'       =>'', #url
            'dataType'  =>0,  #0:ԭʼjson 1:��������
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        if(!$url) return null;
        $json = self::getCurlData(array(
            'url'   =>$url,#url
            'retry' =>3, #����ʧ��ʱ�����Դ���
        ));
        if($dataType==0){
            return  $json;
        }
        #ת��Ϊ����
        $data = api_json_decode($json, true);
        if(!$data || empty($data['f'])) return null;
        $outArr = array(
            'cityInfo'=>array(),
            'forecast'=>array()
        );
        #������Ϣ
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
        #Ԥ����Ϣ
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
                #��������
                $row['dayTemperature']      = isset($val['fc']) ? $val['fc'] : '';
                $row['eveTemperature']      = isset($val['fd']) ? $val['fd'] : '';
                #�������
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
                #����
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
                #�ճ�����
                $row['sunrise']     = isset($val['fi']) ? $val['fi'] : '';
                $foreArr[]  = $row;
            }
        }
        $outArr['forecast'] = $foreArr;
        return $outArr;
    }
    
    /**
     * ��������������
     */
    private static function getCurlData($paramArr){
        $options = array(
            'url'   =>'',   #url
            'retry' =>3,    #����ʧ��ʱ�����Դ���
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        if(!$url) return null;
        #���ͨ��3��
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
     * ��ȡ������������Ϣ
     * @note  �й���������������㣺 1��ֱϽ�еؼ���idΪ00   2������ʡ ����id��9λ�����������
     * @param ��ȡʡ���б�    $provinceId=''  $districtId='00' $cityId='00'
     * @param ��ȡĳʡ�ĵؼ��� $provinceId=num $districtId=num  $cityId='00'
     * @param ��ȡĳʡ�е����� $provinceId=num $districtId=num  $cityId=''
     */
    public static function getAreaInfo($paramArr){
        $options = array(
            'provinceId'    =>'',   #ʡ��id �� ''
            'districtId'    =>'',   #�ؼ���id ��''
            'cityId'        =>'',   #����id ��''
            'name'          =>'',   #�������� ������''
            'limit'         =>'100',#limit����
            'isDebug'       =>0,    #�Ƿ��ӡsql
            'refresh'       =>0,    #�Ƿ�ˢ�»���
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        #����key
        $cacheKey = 'zol_weather_area_p'.$provinceId.'_d'.$districtId.'_c'.$cityId.'_n'.$name.'_l'.$limit;
        #��ˢ��ʱĬ�Ϸ��ػ���
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
        #�Ƿ�ֱϽ��
        $isMunicipalit = in_array($provinceId, self::$municipalities);
        #������ʡ��id ��ô����Ϊ��ȡ�б���������ͬ
        if($provinceId!==''){
            $whereSql .=" and province_id='{$provinceId}' ";
            #ֱϽ�е���idʼ��Ϊ00
            if($isMunicipalit){
                $districtId='00';
            }
        }
        #�����˵���id
        if($districtId!==''){
            $whereSql .=" and district_id='{$districtId}' ";
        }else if(!$isMunicipalit && $cityId==='00'){
            #��ֱϽ�� ��ȡ�ؼ����б��� $districtId='' $cityId='00'
            $whereSql .=" and district_id<>'00' ";
        } 
        #�����˳���id
        if($cityId!==''){
            $whereSql .=" and city_id='{$cityId}' ";
        }else{
            #��ȡ�����б��� $districtId=num $cityId=''
            $whereSql .=" and city_id<>'00' ";
        }  
        #�����˳�����
        if($name!==''){
            $whereSql .=" and name='{$name}' ";
        }

        $sql =" select province_id as provinceId, district_id as districtId, city_id as cityId, name from z_weather_area where 1 {$whereSql}";
        #limit
        if($limit)  $sql .=" limit {$limit} ";
        #��ӡsql
        if($isDebug) echo $sql;
        $db     = API_Db_ArticleNpro::instance();
        $data   = $db->getAll($sql);
        
        $data   = $data ? $data : null;
        #���û���
        API_Item_Kv_MongoCenter::set(array(
            'module'=> 'cms',	#������
            'key'	=> $cacheKey,	#key
            'life'  => 86400,     	#������ 86400
            'data'  => $data,       #����
        ));
        return $data;
    }
    
    /**
     * ��ȡ�������ĵ���id 
     */
    public static function getAreaId($paramArr){
        $options = array(
            'provinceId'    =>'',   #ʡ��id
            'districtId'    =>'',   #�ؼ���id 
            'cityId'        =>'',   #����id
        );
        if(is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        $areaId = '';
        #����ʡid��9λ
        if(strlen($cityId)==9){
            return $cityId;
        }
        #ֱϽ�в���Ѱ��·
        if(in_array($provinceId, self::$municipalities)){
            $areaId = $provinceId.$cityId.$districtId;
        }else{
            $areaId = $provinceId.$districtId.$cityId;
        }
        return $areaId;
    }

}