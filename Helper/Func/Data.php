<?php
/**
 * 数据相关函数
 * 仲伟涛
 * 2013-11-26
 */
class Helper_Func_Data extends Helper_Abstract {
    
    #根据产品线的名称 获取 产品线 id
    
    public static function getSubcateIdByName($paramArr){
        $options = array(
            'nameData'       => array(),    
         );
        if(is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        if(!$nameData) return fasle;
        $returnData= array();
        $errorData = array();
        #获取所有产品线 数据
        $dataArr = ZOL_Api::run("Pro.Cate.getSubCate" , array('showHiden'=>0));
        foreach($nameData as $key=>$val){
            $key = trim($key);
            $keyId = 0;
            $valId = 'array(';
            $val = trim($val);
            $nameArr = explode("、", $val);
             
            foreach($dataArr as $sdata){
                if($key == $sdata['name']){
                    $keyId = $sdata['id'];
                    break;
                }
            }
            foreach($nameArr as $name){
                $finded = false;
                foreach($dataArr as $sdata){
                    if(trim($name) == trim($sdata['name'])){
                        $valId .= $sdata['id'].',';
                        $finded = true;
                        break;
                    }
                }
                if(false == $finded){
                    #如果没有找到 去otherName   里面找
                    foreach($dataArr as $sdata){
                        if(isset($sdata['otherName'])){
                            $otherName = explode(",", $sdata['otherName']);
                            if($sdata['otherName'] && $otherName && in_array(trim($name), $otherName)){
                                $valId .= $sdata['id'].',';
                                $finded = true;
                                break;
                            }
                        }
                        
                    }
                }
                if(false == $finded){
                    if(isset($errorData[$key])){
                        $errorData[$key].=$name.',';
                    }else{
                        $errorData[$key] =$name.',';
                    }
                    
                }
            } 
            $valId.=$keyId.'),#'.$key.':'.$val;#最后加上自己本身的产品线
            $returnData['src'][$key] = $val;   #元数据做对比
            $returnData['error'] = $errorData; #错误数据，即没有找到匹配的数据
            $returnData['val'][$keyId] = $valId;   #匹配的数据，如果error数组里没数据，直接用
             
        }
        return $returnData;
    }


    /**
     * 获得省份信息
     */
    public static function getProvince() {
        $data = API_Item_Pro_Area::getProvinceInfo(array('areaId'=>1));
        if($data){
            $data[28] = '香港';
            $data[29] = '澳门';
            $data[9]  = '内蒙古';
            unset($data[150]);//去掉其他
        }
        
        return $data;
    }
    
    /**
     * 获得城市列表
     */
    public static function getCity($paramArr){
        $options = array(
            'provinceId'       => false,   #省份ID 
            'group'            => false,   #是否按照省份分组
        );
        if(is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        $whereSql = "";
        if($provinceId) $whereSql .= " and province_id = {$provinceId}";
		$res = Helper_Dao::getRows(array(
			'dbName'        => "Db_Product",      #数据库名
			'tblName'       => "_city",     #表名
			'cols'          => "id,name,province_id provinceId",            #列名
            'whereSql'      =>  $whereSql,    #where条件
		));
        
        $data = array();
        if($res){
            foreach($res as $re){
                
                $re["name"] = str_replace("市", "", $re["name"]);
                if($re['id'] == 342)$re["name"] = "津市";
                if($group || $provinceId){#是否按照省份分组
                    $data[$re["provinceId"]][$re['id']] = $re;
                }else{
                    $data[$re['id']] = $re;
                }
            }
            unset($res);
        }
        
        if($provinceId){
            return $data[$provinceId];
        }else{
            return $data;
        }
    }
    
    /**
     * 网站内链资讯手工导入恒星
     */
    public static function getSeoModuleSyncStar($paramArr){
        $options = array(
            'typeId'      => '',   #手工子类ID,多条逗号分隔
            'moduleId'    => '',   #手工ID,多条逗号分隔
            'mode'        => 1,    #导入模式 1产品库 2资讯 对应配置文件里objId
            'configArr'   => array(),#配置数组 array('list'=>1,'detail'=>2)
        );
        if(is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        if(!$mode) return false;
        if($mode==1){
            if(empty($configArr)) return false; #如果是导产品库，一定要手工和位置类型对应数组
        }
        $whereSql = ' AND d.status=0 ';
        if($typeId){
            $whereSql .= ' AND m.type_id in('.$typeId.') ';
        }
        if($moduleId){
            $whereSql .= ' AND m.module_id in('.$moduleId.') ';
        }
        $tempModuleList = Helper_Dao::getRows(array(
                'dbName'   => "Db_Document", #数据库名
                'tblName'  => " template_module_class m left join template_module_data d on m.module_id=d.module_id ", #表名
                'cols'     => "m.module_name,d.title,d.url,d.date", #列名
                'whereSql' =>  $whereSql,
        ));
        if(empty($tempModuleList)) return false;
        foreach($tempModuleList as $val){
            $nameArr = explode('#', $val['module_name']);
            if(empty($nameArr[1]) || empty($nameArr[2])) continue;
            if(empty($configArr[$nameArr[1]])) continue;
            $insertArr = array(
                'title'=>$val['title'],
                'subId'=>$nameArr[2],
                'url'=>$val['url'],
                'addTime'=>strtotime($val['date']),
                'startTime'=>strtotime($val['date']),
                'endTime'=>strtotime('2014-12-31 23:59:59'),
                'areaId'=>$configArr[$nameArr[1]],
                'objId'=>$mode
            );
            Helper_Dao::insertItem(array(
		        'addItem'       =>  $insertArr, #数据列
		        'dbName'        =>  'Db_Star',    #数据库名
		        'tblName'       =>  'seo_weblink_list',    #表名
		    ));
        }
        return true;
    }
    
    #对数组内按指定键排序
    public static function  sortForKey($arr,$sortkey,$asc=0){
        $len = count($arr);
        if($asc){
            for ($i=0;$i<$len;$i++){
                for($j=$i+1;$j<$len;$j++){
                    if($arr[$i][$sortkey] > $arr[$j][$sortkey]){
                        $temp = $arr[$i];
                        $arr[$i] = $arr[$j];
                        $arr[$j] = $temp;
                    }
                }
            }
        }else{
             for ($i=0;$i<$len;$i++){
                for($j=$i+1;$j<$len;$j++){
                    if($arr[$i][$sortkey] < $arr[$j][$sortkey]){
                        $temp = $arr[$i];
                        $arr[$i] = $arr[$j];
                        $arr[$j] = $temp;
                    }
                }
            }
        }
        return $arr;        
    }

}
