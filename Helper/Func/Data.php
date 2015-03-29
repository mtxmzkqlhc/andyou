<?php
/**
 * ������غ���
 * ��ΰ��
 * 2013-11-26
 */
class Helper_Func_Data extends Helper_Abstract {
    
    #���ݲ�Ʒ�ߵ����� ��ȡ ��Ʒ�� id
    
    public static function getSubcateIdByName($paramArr){
        $options = array(
            'nameData'       => array(),    
         );
        if(is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        if(!$nameData) return fasle;
        $returnData= array();
        $errorData = array();
        #��ȡ���в�Ʒ�� ����
        $dataArr = ZOL_Api::run("Pro.Cate.getSubCate" , array('showHiden'=>0));
        foreach($nameData as $key=>$val){
            $key = trim($key);
            $keyId = 0;
            $valId = 'array(';
            $val = trim($val);
            $nameArr = explode("��", $val);
             
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
                    #���û���ҵ� ȥotherName   ������
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
            $valId.=$keyId.'),#'.$key.':'.$val;#�������Լ�����Ĳ�Ʒ��
            $returnData['src'][$key] = $val;   #Ԫ�������Ա�
            $returnData['error'] = $errorData; #�������ݣ���û���ҵ�ƥ�������
            $returnData['val'][$keyId] = $valId;   #ƥ������ݣ����error������û���ݣ�ֱ����
             
        }
        return $returnData;
    }


    /**
     * ���ʡ����Ϣ
     */
    public static function getProvince() {
        $data = API_Item_Pro_Area::getProvinceInfo(array('areaId'=>1));
        if($data){
            $data[28] = '���';
            $data[29] = '����';
            $data[9]  = '���ɹ�';
            unset($data[150]);//ȥ������
        }
        
        return $data;
    }
    
    /**
     * ��ó����б�
     */
    public static function getCity($paramArr){
        $options = array(
            'provinceId'       => false,   #ʡ��ID 
            'group'            => false,   #�Ƿ���ʡ�ݷ���
        );
        if(is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        $whereSql = "";
        if($provinceId) $whereSql .= " and province_id = {$provinceId}";
		$res = Helper_Dao::getRows(array(
			'dbName'        => "Db_Product",      #���ݿ���
			'tblName'       => "_city",     #����
			'cols'          => "id,name,province_id provinceId",            #����
            'whereSql'      =>  $whereSql,    #where����
		));
        
        $data = array();
        if($res){
            foreach($res as $re){
                
                $re["name"] = str_replace("��", "", $re["name"]);
                if($re['id'] == 342)$re["name"] = "����";
                if($group || $provinceId){#�Ƿ���ʡ�ݷ���
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
     * ��վ������Ѷ�ֹ��������
     */
    public static function getSeoModuleSyncStar($paramArr){
        $options = array(
            'typeId'      => '',   #�ֹ�����ID,�������ŷָ�
            'moduleId'    => '',   #�ֹ�ID,�������ŷָ�
            'mode'        => 1,    #����ģʽ 1��Ʒ�� 2��Ѷ ��Ӧ�����ļ���objId
            'configArr'   => array(),#�������� array('list'=>1,'detail'=>2)
        );
        if(is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        if(!$mode) return false;
        if($mode==1){
            if(empty($configArr)) return false; #����ǵ���Ʒ�⣬һ��Ҫ�ֹ���λ�����Ͷ�Ӧ����
        }
        $whereSql = ' AND d.status=0 ';
        if($typeId){
            $whereSql .= ' AND m.type_id in('.$typeId.') ';
        }
        if($moduleId){
            $whereSql .= ' AND m.module_id in('.$moduleId.') ';
        }
        $tempModuleList = Helper_Dao::getRows(array(
                'dbName'   => "Db_Document", #���ݿ���
                'tblName'  => " template_module_class m left join template_module_data d on m.module_id=d.module_id ", #����
                'cols'     => "m.module_name,d.title,d.url,d.date", #����
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
		        'addItem'       =>  $insertArr, #������
		        'dbName'        =>  'Db_Star',    #���ݿ���
		        'tblName'       =>  'seo_weblink_list',    #����
		    ));
        }
        return true;
    }
    
    #�������ڰ�ָ��������
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
