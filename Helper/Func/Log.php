<?php
/**
 * ��־��غ���
 * ��ΰ��
 * 2013-11-26
 */
class Helper_Func_Log extends Helper_Abstract {
    
    /**
     * ��¼��־-�������������Ӧֵǰ��仯
     */
    public static function recordArr($param_arr = array()) {
        $options = array(
            'appName'       => '',      #APP��
			'subId'         => 0,       #����ID
			'objId'         => 0,       #objid
            'currentArr'    => array(), #��ǰ�޸�����
            'originArr'     => array(), #ԭʼ��¼����
            'isLogEmpty'    => 1,       #�Ƿ��¼�ޱ䶯�ļ�¼,1-��,0-��
			'userId'        => false,   #ָ��userId
        );
        if (is_array($param_arr))$options = array_merge($options, $param_arr);
        extract($options);
        
        //��־����
        $content = '';
        
        //������ڵ�ǰ����
        if ($currentArr) {
            foreach ($currentArr as $key => $value) {
                //���������
                if (is_array($value)) {
                    //��������
                    arsort($value);
                    //��ϳ��ַ���
                    $value = json_encode($value);
                }
                
                //���û��ԭʼ����,����Ϊ�ǲ���
                if (empty($originArr)) {
                    $content .= $key . 'Ϊ' . $value . "\r\n";
                } else {
                    //�����ԭʼ������ , ��Ϊ�����ݿ��ȡ����֮��,�� POST �����addslashes
                    if (isset($originArr[$key])) {
                        //���������
                        if (is_array($originArr[$key])) {
                            arsort($originArr[$key]);
                            //��ϳ��ַ���
                            $cvalue = json_encode($originArr[$key]);
                        } else {
                            $cvalue = addslashes($originArr[$key]);
                        }
                    } else {
                        $cvalue = '';
                    }
                    //�ж����ޱ仯
                    if ($value != $cvalue) {
                        $content .= $key . '��' . $cvalue . '���' . $value . "\r\n";
                    }
                }
            }
        }
        
        //���δָ����¼��ʽ ���� �ǿղż�¼
        if ($isLogEmpty || $content) {
            self::record(array(
                'appName'   =>  $appName,    #APP��
                'subId'     =>  $subId,     #����ID
                'objId'     =>  $objId,     #objid
                'log'       =>  $content,   #��־����
                'userId'    =>  $userId,    #ָ���û���
            ));
        }
    }
    
    /**
     * ��¼��־
     */    
    public static function record($paramArr) {
		$options = array(
			'appName'   =>  '',    #APP��
			'subId'     =>  0,     #����ID
			'objId'     =>  0,     #objid
			'log'       =>  '',    #��־����
			'userId'    => false,   #ָ��userId
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $idMap = ZOL_Config::get("Star/InnerApp","ID");        #�����ķ���        
        if(!isset($idMap[$appName]))return false;
        
        $subIdMap = ZOL_Config::get("Star/InnerApp","SUBID");  #�����ķ��� 
        #��subid��Ӣ����ʽת��Ϊ����
        if(isset($subIdMap[$appName]) && isset($subIdMap[$appName][$subId])) $subId = $subIdMap[$appName][$subId]["id"];
        $output = ZOL_Registry::get('response'); #���output���� 
        #�û�ID,���û�д��룬�ʹӸ����ö��û�ID
        if(!$userId){ 
            $userId = $output->userId;
        }
        $item = array(
            "appId"    => $idMap[$appName]["id"],
            "subId"    => $subId,
            "objId"    => $objId,
            "content"  => addslashes($log),
            "tm"       => SYSTEM_TIME,
            "adder"    => $userId,
            "pageType" => $output->pageType,
        );
        
        Helper_Dao::insertItem(array(
            'addItem'  =>  $item,
            'dbName'   =>  "Db_Star",      #���ݿ���
            'tblName'  =>  "log_operations",   #����
            'debug'    =>  0,
        ));
        
        return true;
    }
    
     
    /**
     * �����־�б�
     */    
    public static function getList($paramArr) {
		$options = array(
			'appName'   =>  '',    #APP��
			'subId'     =>  0,     #����ID
			'objId'     =>  0,     #objid
			'num'       =>  10,    #����
            'offset'    => '',
			'getNum'    =>  0,     #�Ƿ���������
            'whereSql'  => ""      #����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $idMap = ZOL_Config::get("Star/InnerApp","ID");        #�����ķ���    
        if(!isset($idMap[$appName]))return false;
        
        $subIdMap = ZOL_Config::get("Star/InnerApp","SUBID");  #�����ķ��� 
        #��subid��Ӣ����ʽת��Ϊ����
        if(isset($subIdMap[$appName]) && isset($subIdMap[$appName][$subId])) $subId = $subIdMap[$appName][$subId]["id"];   
        
         
        if($appName)$whereSql .= " and appId = " . $idMap[$appName]["id"];
        if($subId)  $whereSql .= " and subId = " . $subId;
        if($objId)  $whereSql .= " and objId = '" . $objId."'";
        
        $res = Helper_Dao::getRows(array(
                                            'dbName'   =>  "Db_Star",      #���ݿ���
                                            'tblName'  =>  "log_operations",   #����
                                            'offset'   =>  $offset,     #offset
                                            'limit'    =>  $num,    #����
                                            'whereSql' =>  $whereSql . " order by id desc ",    #where����
                                            'debug'    =>  0,
                                    ));
        
        if($getNum){#�Ƿ�������
            $cnt = Helper_Dao::getCount(array(
                                            'dbName'   =>  "Db_Star",      #���ݿ���
                                            'tblName'  =>  "log_operations",   #����
                                            'whereSql' =>  $whereSql ,    #where����
                                            'debug'    =>  0,
                                    ));
            return array('allNum' => $cnt , 'data' => $res );
            
        }
        return $res;
    }
        
}
