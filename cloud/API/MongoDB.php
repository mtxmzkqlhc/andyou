<?php
/**
 * MongoDB��
 * @author ��ΰ��
 * 2011-7
 */
class API_MongoDB{

    protected static $serverArr  = array();#������Mongo����ļ���
    protected static $dbArr      = array();#���ӵ�DB
    protected static $colArr     = array();#Collection������
    public static $serverCfgArr  = array(
        'localhost' => 'localhost',//:27017
        'proAdmin'  => '10.15.184.41',
    );

    /**
     * ����������
     */
    public static function connect($server = 'localhost'){
        if(isset(self::$serverCfgArr[$server])){
            $serverCnn = 'mongodb://' .self::$serverCfgArr[$server];
        }else{
            $serverCnn = 'mongodb://' .$server;
        }
        if (!isset(self::$serverArr[$server])){
            if (class_exists("Mongo")) {
                self::$serverArr[$server] = new Mongo($serverCnn, array('timeout' => 1000, 'persist' => $server));
                
            } else {
                die("Mongo�ӿ�ģ�鲻����");
            }
        }
        return self::$serverArr[$server];
    }

    /**
     * ���ݿ�����
     */
    public static function selectDB($server,$db,$extParam=false){
        
        if (!isset(self::$dbArr[$server]) || !isset(self::$dbArr[$server][$db])){            
            $mongo = self::connect($server);
            $dbIns = $mongo->$db;
            if($extParam && isset($extParam['slaveOkay'])){
                if($extParam['slaveOkay']){
                    $dbIns->setSlaveOkay(true);
                }else{
                    $dbIns->setSlaveOkay(false);
                }
            }
            self::$dbArr[$server][$db] = $dbIns;
        }
        return self::$dbArr[$server][$db];

    }

    /**
     * ���ĳ��Collection
     */
    public static function selectCollection($server,$db,$collection,$extParam=false){

        if (!isset(self::$colArr[$server]) || !isset(self::$colArr[$server][$db]) || !isset(self::$colArr[$server][$db][$collection])){
            $dbIns = self::selectDB($server, $db,$extParam);
            self::$colArr[$server][$db][$collection] = $dbIns->$collection;
        }
        return self::$colArr[$server][$db][$collection];
    }

    /**
     * ���һ������
     */
    public static function findOne($server,$db,$collection,$key,$extParam=false){
        #���collection
        $cols = self::selectCollection($server,$db,$collection,$extParam);
        $data = false;
        try{
            $data = $cols->findOne(array('_id' => $key));
        } catch (MongoCursorException $e) {
        }
        return $data;
        
    }

    /**
     * ����һ������[������insert ����update]
     */
    public static function save($server,$db,$collection,$key,$value){
        #���collection
        $cols = self::selectCollection($server,$db,$collection);
        if(is_array($value)){
            $value['_id'] = $key;
        }else{
            $value = array('_id' => $key,$value);
        }
        return $cols->save($value);
        
    }
    /*
     * �ڶ��ֲ��뷽������ִ��remore��insert
     */
    public static function save2($server,$db,$collection,$key,$value){
        self::remove($server, $db, $collection, $key);
        self::insert($server, $db, $collection, $key, $value);
    }
    /**
     * ����һ������
     */
    public static function insert($server,$db,$collection,$key,$value){
        #���collection
        $cols = self::selectCollection($server,$db,$collection);
        if(is_array($value)){
            $value['_id'] = $key;
        }else{
            $value = array('_id' => $key,$value);
        }
        return $cols->insert($value);

    }

    /**
     * ɾ��һ������
     */
    public static function remove($server,$db,$collection,$key){
        #���collection
        $cols = self::selectCollection($server,$db,$collection);
        $cols->remove(array('_id' => $key), array("justOne" => true));
        
    }
    /**
     * ���״̬
     */
    public static function stats($server,$db=false){
        $data = array();
        if($server && $db){
            $dbIns = self::selectDB($server, $db);
            $data['server'] = $dbIns->execute("db.serverStatus()");
            $data['db']     = $dbIns->execute("db.stats()");
        }
        return $data;
        
    }
    /**
     * ִ������
     */
    public static function doCmd($server,$db=false,$cmd){
            if($db){
                $dbIns = self::selectDB($server, $db);
                if(is_array($cmd)){
                    $data = $dbIns->command($cmd);
                }else{
                    $data = $dbIns->execute($cmd);
                }
            
            }else{
                $mongo = self::connect($server);
                if(is_array($cmd)){
                    $data = $mongo->command($cmd);
                }else{
                    $data = $mongo->execute($cmd);
                }
            }

            return $data;
    }
    /**
     * ����ϴεĴ�����Ϣ
     */
    public static function lastError($server,$db){
         $dbIns = self::selectDB($server, $db);
         return $dbIns->lastError();
    }
    /**
     * �رղ���
     */
    public static function close($server){
        if(isset(self::$serverCfgArr[$server])){
            $cnn = self::$serverCfgArr[$server];
            if($cnn){
                $cnn.close();
            }
        }
        return true;
    }


}

