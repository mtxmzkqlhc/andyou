<?php
/**
* MongoDB��������
* ��ΰ�� 2012-11-23
*/
class API_Item_Kv_MongoCenter
{
    private static $_cacheCfg = array(
        #����ϵͳ
        'comment' => array(
            'read' => array(
                'server'  => 'commentsmongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'commentsmongodb:30000'
            ),

        ),
        #�ֻ�
        'sj' => array(
            'read' => array(
                'server'  => 'othermongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'
            ),
        ),
        #Ц��
        'xiaohua' => array(
            'read' => array(
                'server'  => 'othermongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'
            ),
        ),
        //desk
        'desk' => array(
            'read' => array(
                'server'  => 'othermongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'
            ),
        ),
        #����
        'xiazai' => array(
            'read' => array(
                'server'  => 'othermongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'
            ),
        ),
        #�̳�
        'shop' => array(
            'read' => array(
                'server'  => 'othermongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'
            ),

        ),
        #������
        'dealer' => array(
            'read' => array(
                'server'  => 'othermongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'
            ),

        ),
        
        #��̳
        'bbs' => array(
            'read' => array(
                'server'  => 'bbsmongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'bbsmongodb:30000'
            ),

        ),

        #��̳
        'bbs_session' => array(
            'ttl'  => true,#����TTL����
            'read' => array(
                'server'  => 'bbsmongodb:27017',//bbsmongodbnew:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'bbsmongodb:30000'//bbsmongodbnew:27017'
            ),
        ),
        #��ά
        'ea3w' => array(
            'read' => array(
                'server'  => 'ea3wmongodb:27017',//'127.0.0.1:27017',
            ),
            'write' => array(
                'server'  => 'ea3wmongodb:27017'
            ),

        ),
        #wap
        'wap' => array(
            'ttl'  => true,#����TTL����
            'read' => array(
                'server'  => 'othermongodb:27017',//bbsmongodbnew:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'//bbsmongodbnew:27017'
            ),
        ),
        #CMS
        'cms' => array(
            'read' => array(
                'server'  => 'othermongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'
            ),

        ),
        #��������
        'my' => array(
            'read' => array(
                'server'  => 'othermongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'
            ),

        ),
        #����
        'fengniao' => array(
            'read' => array(
                'server'  => 'othermongodb:27017',//'127.0.0.1:27017',
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'
            ),

        ),
        #ħ��
        'mojing' => array(
            'read' => array(
                'server'  => 'othermongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'
            ),
        ),
        #�Ƽ�
        'test' => array(
            'ttl'  => true,#����TTL����
            'read' => array(
                'server'  => 'othermongodb:27017',//'127.0.0.1:27017',
                'slaveOk' => 1,
            ),
            'write' => array(
                'server'  => 'othermongodb:30000'
            ),
        ),
    );
    private static $_cachePool = array();
    
    /**
     * ������ݿ����
     */
    public static function getDb($paramArr){
		$options = array(
            'module'      => false, #ģ������������comment
            'readWrite'   => 1, #��д 1�� 2д
            'retry'       => true,  #�Ƿ�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$module || !isset(self::$_cacheCfg[$module]))return false;
        $cfg = $readWrite == 2 ? self::$_cacheCfg[$module]["write"] : self::$_cacheCfg[$module]["read"];
        $extParam = array();

        if(isset($cfg["slaveOk"]))$extParam['slaveOkay'] = 1;
        #����������
        $server     = $cfg["server"];
        $db         = $module;

        return API_MongoDB::selectDB($server,$db,$extParam);

    }

    /**
     * ��û�������
     */
    public static function get($paramArr){
		$options = array(
            'module'    => false, #ģ������������comment
            'tbl'       => 'tbl', #����
            'key'       => false, #key
            'getMeta'   => false, #�Ƿ���meta����
            'write'     => false, #�Ƿ�ǿ����д��
            'minTm'     => 0,     #���ƶ೤ʱ�����ڵĻ���
            'retry'     => true,  #�Ƿ�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(!$module || !isset(self::$_cacheCfg[$module]))return false;
        $cfg = $write ? self::$_cacheCfg[$module]["write"]:self::$_cacheCfg[$module]["read"];
        $extParam = array();

        if(isset($cfg["slaveOk"]))$extParam['slaveOkay'] = 1;
        if($write)$extParam['slaveOkay'] = 0;

        #����������
        $server     = $cfg["server"];
        $db         = $module;
        $md5key     = md5($key);
        $tbl        = $tbl ? $tbl : "tbl";
        $collection = $tbl . '_' . substr($md5key, 0,2);

        #�������
        try{
            $data = API_MongoDB::findOne($server,$db,$collection,$md5key,$extParam);
        } catch (Exception $e){
            if(!empty($options['retry'])){
                $options['retry'] = false;
                return self::get($options);
            }
        }

        $out  = false;
        if(isset($data) && $data && isset($data['data'])){
            
            #�ж���Ч��,����Ѿ������ˣ��ͷ���false
            if(!isset($data['life']) || !$data['life'] || $data['life'] > SYSTEM_TIME){
                $out = $data['data'];
                if($minTm && $data['life'] < $minTm){
                    $out = false;
                }
                if($getMeta){
                    $out = array(
                        'data' => $data['data'],
                        'meta' => isset($data['meta']) ? $data['meta'] : false,
                    );
                }
            }
            
        }
        return $out;

    }

     /**
     * ���û�������
     */
    public static function set($paramArr){
		$options = array(
            'module'    => false, #ģ������������comment
            'tbl'       => 'tbl', #����
            'key'       => false, #key
            'data'      => false, #����
            'meta'      => false, #meta���ݣ����Դ洢����������
            'life'      => 0,     #��������
            'retry'     => true,  #�Ƿ�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


        if(!$module || !isset(self::$_cacheCfg[$module]))return false;
        $cfg = self::$_cacheCfg[$module]["write"];
        $ttlFlag = isset(self::$_cacheCfg[$module]['ttl']) && self::$_cacheCfg[$module]['ttl'] ? true : false; //�Ƿ���TTL
        #����������
        $server     = $cfg["server"];
        $db         = $module;
        $md5key     = md5($key);
        $tbl        = $tbl ? $tbl : "tbl";
        $collection = $tbl . '_' . substr($md5key, 0,2);
        $data       = array(
            'key'   => $key,
            'data'  => $data,
            'life'  => $life ? $life + SYSTEM_TIME : 0,
        );
        //����TTL��ʱ��
        if($ttlFlag)$data['ttldt'] = new MongoDate(SYSTEM_TIME);
        if($meta)$data['meta'] = $meta;
        try{
            API_MongoDB::save($server,$db,$collection,$md5key,$data);
        } catch(Exception $e){
            if(!empty($options['retry'])){
                $options['retry'] = false;
                self::set($options);
            }
        }
        return true;

    }
    
    /**
     * ʹ����ʧЧ
     */
    public static function invalid($paramArr){
		$options = array(
            'module'    => false, #ģ������������comment
            'tbl'       => 'tbl', #����
            'key'       => false, #key
            'retry'     => true,  #�Ƿ�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


        if(!$module || !isset(self::$_cacheCfg[$module]))return false;
        $cfg = self::$_cacheCfg[$module]["write"];

        #����������
        $server     = $cfg["server"];
        $db         = $module;
        $md5key     = md5($key);
        $tbl        = $tbl ? $tbl : "tbl";
        $collection = $tbl . '_' . substr($md5key, 0,2);


        try{
            $colObj = API_MongoDB::selectCollection($server,$db,$collection);
            $colObj->update(array("_id" => $md5key),array('$set' => array("life" => SYSTEM_TIME)));

        } catch(Exception $e){
            if(!empty($options['retry'])){
                $options['retry'] = false;
                self::invalid($options);
            }
        }
    }

     /**
     * ɾ������
     */
    public static function delete($paramArr){
		$options = array(
            'module'    => false, #ģ������������comment
            'tbl'       => 'tbl', #����
            'key'       => false, #key
            'retry'     => true,  #�Ƿ�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


        if(!$module || !isset(self::$_cacheCfg[$module]))return false;
        $cfg = self::$_cacheCfg[$module]["write"];

        #����������
        $server     = $cfg["server"];
        $db         = $module;
        $md5key     = md5($key);
        $tbl        = $tbl ? $tbl : "tbl";
        $collection = $tbl . '_' . substr($md5key, 0,2);

        try{
            API_MongoDB::remove($server,$db,$collection,$md5key);
        } catch(Exception $e){
            if(!empty($options['retry'])){
                $options['retry'] = false;
                self::delete($options);
            }
        }
        return true;

    }
    //

     /**
     * ִ������
     * db.ttl.ensureIndex({"Date": 1}, {expireAfterSeconds: 300})
     */
    public static function cmd($paramArr){
		$options = array(
            'module'    => false, #ģ������������comment
            'cmd'       => false, #ִ�е�����
            'retry'     => true,  #�Ƿ�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


        if(!$module || !isset(self::$_cacheCfg[$module]))return false;
        $cfg = self::$_cacheCfg[$module]["write"];

        #����������
        $server     = $cfg["server"];
        $db         = $module;
        $data = false;
        try{
           $data    = API_MongoDB::doCmd($server,$db,$cmd);
        } catch(Exception $e){
            if(!empty($options['retry'])){
                $options['retry'] = false;
                 $data  = self::cmd($options);
            }
        }
        return $data;

    }

    /**
     * ִ��TTL
     */
    public static function setTtl($paramArr){
		$options = array(
            'module'    => false, #ģ������������comment
            'tbl'       => 'tbl', #����ǰ׺
            'tm'        => 86400*15, #����ttl��ʱ��
            'retry'     => true,  #�Ƿ�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        #����������
        for($i=0;$i<256;$i++){
            $chrs = dechex($i);
            if(strlen($chrs)==1) $chrs = "0".$chrs;
            $cmd = "db.{$tbl}_{$chrs}.ensureIndex({ttldt: 1}, {expireAfterSeconds: {$tm}})";
            self::cmd(array(
                'module'    => $module,
                'cmd'       => $cmd, #ִ�е�����

            ));
        }

        return true;
    }

    /**
     * remove TTL
     */
    public static function removeTtl($paramArr){
		$options = array(
            'module'    => false, #ģ������������comment
            'tbl'       => 'tbl', #����ǰ׺
            'retry'     => true,  #�Ƿ�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        #����������
        for($i=0;$i<256;$i++){
            $chrs = dechex($i);
            if(strlen($chrs)==1) $chrs = "0".$chrs;
            $cmd = "db.{$tbl}_{$chrs}.dropIndexes(\"ttldt\")";
            self::cmd(array(
                'module'    => $module,
                'cmd'       => $cmd, #ִ�е�����

            ));
        }

        return true;
    }


    /**
     * ����capped����
     */
    public static function doCapped ($paramArr){
		$options = array(
            'module'    => false, #ģ������������comment
            'tbl'       => 'tbl', #����ǰ׺
            'size'      => 0, #���������
            'max'       => 0, #���������
            'retry'     => true,  #�Ƿ�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        $cmdArr = array();
        #���������� db.runCommand( { convertTocapped��"test",size:10000,max:1000 } );
        for($i=0;$i<256;$i++){
            $chrs = dechex($i);
            if(strlen($chrs)==1) $chrs = "0".$chrs;
            $cmdArr['convertTocapped'] = "{$tbl}_{$chrs}";
            if($size)$cmdArr['size']   = $size;
            if($max) $cmdArr['max']    = $max;

            self::cmd(array(
                'module'    => $module,
                'cmd'       => $cmdArr, #ִ�е�����

            ));
        }

        return true;
    }
}

?>