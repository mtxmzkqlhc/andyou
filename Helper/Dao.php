<?php
/**
 * ���ݷ���������
 * @author ��ΰ��
 * @copyright (c) 2012-02-06
 */
class Helper_Dao extends Helper_Abstract {
    private static $initDbName  = ""; #��ʼ�����ݿ�������,�Ժ󷽷���ȡ��ʼ���ĸ�ֵ
    private static $initTblName = ""; #��ʼ�����ݱ���
    
    /**
     * ��ʼ������
     */
    public static function init($paramArr) {
        $options = array(
            'dbName'        =>  'Db_UserData',    #���ݿ���
            'tblName'       =>  '',    #����
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        self::$initDbName  = $dbName;
        self::$initTblName = $tblName;
        return true;
    }
    
    /**
     *  ���һ������
     */
    public static function getRow($paramArr) {
        $options = array(
            'dbName'        =>  self::$initDbName,    #���ݿ���
            'tblName'       =>  self::$initTblName,    #����
            'cols'          =>  '*',   #����
            'limit'         =>  1,    #����
            'offset'        =>  0,     #offset
            'whereSql'      =>  '',    #where����
            'orderSql'      =>  '',    #orderby
            'debug'         =>  0 ,      #��ʾsql
            'isWrite'       => ''
            
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        $db = ZOL_Db::instance($dbName);
        if($isWrite) $db->forceReadMaster();  #ǿ�������⣬��ֹ ��д���뵼�����ݲ�ͬ��
        $limitSql = "";
        if($limit){
            $limitSql = " limit ";
            if($offset)$limitSql .= $offset . ",";
            $limitSql .= $limit;
        }
        
        $sql      = "select {$cols}  from {$tblName} where 1 {$whereSql} {$orderSql} {$limitSql}";
        if($debug)echo $sql ;
        
            $data     = $db->getAll($sql);

        return $data && !empty($data[0]) ? $data[0] : false;
        
    }
    
    /**
     * ��ö�������
     */
    public static function getRows($paramArr) {
        $options = array(
            'dbName'        =>  self::$initDbName,    #���ݿ���
            'tblName'       =>  self::$initTblName,    #����
            'cols'          =>  '*',   #����
            'offset'        =>  0,     #offset
            'limit'         =>  '',    #����
            'whereSql'      =>  '',    #where����
            'groupBy'       =>  '',    #group by
            'orderSql'      =>  '',    #where����
            'debug'         =>  0,      #��ʾsql
            'isWrite'       => ''
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        $db = ZOL_Db::instance($dbName);
        if($isWrite) {
            $db->forceReadMaster();  #ǿ�������⣬��ֹ ��д���뵼�����ݲ�ͬ��
        }
        $limitSql = "";
        if($limit){
            $limitSql = " limit ";
            if($offset)$limitSql .= $offset . ",";
            $limitSql .= $limit;
        }
        if($groupBy) $groupBy = ' group by ' . $groupBy;
        $sql      = "select {$cols}  from {$tblName} where 1 {$whereSql} {$groupBy} {$orderSql} {$limitSql}";
        if($debug) { echo $sql.'<br>';}
        $data     = $db->getAll($sql);

        return $data;
    }
    /**
     * ����������õ�������
     */
    public static function getOne($paramArr) {
		$options = array(
			'dbName'        =>  self::$initDbName,    #���ݿ���
			'tblName'       =>  self::$initTblName,    #����
			'cols'          =>  '',    #����
			'whereSql'      =>  '',    #where����
            'debug'         =>  0      #��ʾsql  
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if($cols)$col = $cols; #���ݴ���col�����
        if(!$col || strpos($col, ',')) return false;
		$db = ZOL_Db::instance($dbName);
        $sql      = "select {$col}  from {$tblName} where 1 {$whereSql} ";
        if($debug) { echo $sql.'<br>';}
        $data     = $db->getOne($sql);

        return $data;
    }
    /**
     * ��������б�
     */
    public static function getList($paramArr) {
        $options = array(
            'dbName'        =>  self::$initDbName,    #���ݿ���
            'tblName'       =>  self::$initTblName,   #����
            'cols'          =>  '*',   #����
            'pageSize'      =>  20,    #ÿҳ����
            'page'          =>  1,     #��ǰҳ
            'pageUrl'       =>  '',    #ҳ��URL����
            'whereSql'      =>  '',    #where����
            'orderSql'      =>  '',    #orderby����
            'iswrite'       =>  false, #ǿ��ʹ��д���ȡ
            'getAll'        =>  false, #�Ƿ�����������
            'pageTpl'       =>  9,     #��ҳģ��
            'jsOnclick' 	=> '',
            'limit'         => '',
            'groupbySql'    => '',
            'debug'         =>  0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        $db     = ZOL_Db::instance($dbName);
        if($iswrite) $db->forceReadMaster();  #ǿ�������⣬��ֹ ��д���뵼�����ݲ�ͬ��
        if($limit){
            $limit = ' limit '.$limit;
            #���������Ϣ
            $sql     = "select {$cols} from {$tblName} where 1 {$whereSql} {$groupbySql} {$limit}";
            $allCnt  = count($db->getAll($sql));
        }else{
            #���������Ϣ
            $sql     = "select count('x') cnt from {$tblName} where 1 {$whereSql} {$groupbySql} {$limit}";
            if ($groupbySql) {
                $allCnt  = count($db->getAll($sql));
            } else {
                $allCnt  = $db->getOne($sql);
            }
        }
        #��÷�ҳ��Ϣ
        $pageCfg = array(
            'page'   => $page,                  #��ǰҳ��
            'rownum' => $pageSize,               #һҳ��ʾ������
            'target' => '_self',                #���Ӵ���ʽ
            'total'  => $allCnt,                #����
            'url'      => $pageUrl,               #��ǰҳ����
            'jsOnclick'=> $jsOnclick
        );
        $pageObj = new Libs_Global_Page($pageCfg);

        $pageBar = $pageObj->display($pageTpl);
        $offset  = ($page -1) * $pageSize;
            
        $limitSql  = $getAll ? "" : " limit {$offset},{$pageSize}";
        $sql       = "select {$cols}  from {$tblName} where 1 {$whereSql} {$groupbySql} {$orderSql} {$limitSql} ";
        $data      = $db->getAll($sql);
        if($debug)echo $sql;
        return array(
            'allCnt'  => $allCnt,
            'pageBar' => $pageBar,
            'data'    => $data,
        );
       
    }
    /**
     * ִ�в������ݵ����ݿ���
     */
    public static function insertItem($paramArr) {
        $options = array(
            'colArr'        =>  false, #��֤����
            'addItem'       =>  false, #������
            'isReplace'     =>  false, #�Ƿ�ִ��replace into
            'dbName'        =>  self::$initDbName,    #���ݿ���
            'tblName'       =>  self::$initTblName,   #����
            'debug'         =>  0
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        if(!$addItem || !$dbName || !$tblName)return false;

        #��֤�ֶ���Ч��
        if(!empty($colArr)){
            foreach ($addItem as $key => $val) {
                if(!in_array($key,$colArr)){
                    echo "�ֶ���Ч:��{$key}��";exit;
                }
            }
        }

        #ƴװSQL
        $iSql1 = $iSql2 = $comma = "";
        foreach ($addItem as $key => $val) {
            $iSql1 .= $comma."`{$key}`";
            $iSql2 .= $comma."'{$val}'";
            $comma = ",";
        }
        $iSql  = $isReplace ? "REPLACE " : "INSERT ";       
        $iSql .= "INTO {$tblName} ({$iSql1}) VALUES ({$iSql2})";
        $db = ZOL_Db::instance($dbName);
        $db->query($iSql);
        if($debug){  echo  $iSql;};
        return $db->lastInsertId();
    }


    /**
     * ִ�и������ݿ��е�����
     */
    public static function updateItem($paramArr) {
        $options = array(
            'colArr'        =>  false, #��֤����
            'editItem'      =>  false, #��������
            'dbName'        =>  self::$initDbName,    #���ݿ���
            'tblName'       =>  self::$initTblName,   #����
            'where'         =>  '',    #����
            'debug'         =>  0
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        if(!$editItem || !$dbName || !$tblName || !$where)return false;

        #��֤�ֶ���Ч��
        if(!empty($colArr)){
            foreach ($editItem as $key => $val) {
                if(!in_array($key,$colArr)){
                    echo "�ֶ���Ч:��{$key}��";exit;
                }
            }
        }

        #ƴװSQL
        $subSql = $s = "";
        foreach($editItem as $key=>$value){
            $subSql .= $s." `$key` ='".$value."'";
            $s = ",";
        }
        $sql = "UPDATE {$tblName} SET {$subSql} WHERE {$where}";
        if($debug)echo $sql;

        $db = ZOL_Db::instance($dbName);
        $db->query($sql);
        return true;
    }
        
     /**
     * ִ��ɾ�����ݿ��е�����
     */
    public static function delItem($paramArr) {
        $options = array(
            'dbName'        =>  self::$initDbName,    #���ݿ���
            'tblName'       =>  self::$initTblName,   #����
            'where'         =>  '',    #����
            'debug'         =>  '',     #where����
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        if(!$dbName || !$tblName || !$where)return false;

        $sql = "DELETE FROM {$tblName} WHERE {$where}";
        if($debug) { echo $sql.'<br>';}
        $db = ZOL_Db::instance($dbName);
        $db->query($sql);
        return true;
    }

    
    public static function getRandomRows($paramArr=array()) {
        $options = array(
            'dbName'        => self::$initDbName,  #���ݿ�
            'tblName'       => self::$initTblName, #����
            'cols'          => '*', #����
            'limit'         => 1,   #��������
            'whereSql'      => '',  #where����
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        $db = ZOL_Db::instance($dbName);
        
        $sql = "select count(*) num from {$tblName} where 1 {$whereSql}";
        $cnt = $db->getOne($sql);
        $offset = rand(0, $cnt-1);
        $sql = "select * from {$tblName} where 1 {$whereSql} limit {$offset}, {$limit}";

        $data  = $db->getAll($sql);
        return $data;
    }
    
    public static function getCount($paramArr) {
        $options = array(
            'dbName'        =>  self::$initDbName,    #���ݿ���
            'tblName'       =>  self::$initTblName,   #����
            'whereSql'      =>  '',    #where����
            'groupBy'       =>  '',    #group by
            'debug'         => '',     #where����
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        $db = ZOL_Db::instance($dbName);
        if($groupBy) $groupBy = ' group by ' . $groupBy;
        $sql     = "select count(*)  from {$tblName} where 1 {$whereSql} {$groupBy}";
        if($debug) { echo $sql.'<br>';}
        $cnt     = $db->getOne($sql);

        return intval($cnt);
    }
    
    /**
     * ��ȡһ��
     */
    public static function getCol($paramArr)
    {
        $options = array(
            'dbName'        =>  self::$initDbName,    #���ݿ���
            'tblName'       =>  self::$initTblName,    #����
            'cols'          =>  '*',   #����
            'offset'        =>  0,     #offset
            'limit'         =>  '',    #����
            'whereSql'      =>  '',    #where����
            'groupBy'       =>  '',    #group by
            'orderSql'      =>  '',    #where����
            'column'        =>  0,     #
            'debug'         =>  0      #��ʾsql
            
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        $db = ZOL_Db::instance($dbName);
        $limitSql = "";
        if($limit){
            $limitSql = " limit ";
            if($offset)$limitSql .= $offset . ",";
            $limitSql .= $limit;
        }
        if($groupBy) $groupBy = ' group by ' . $groupBy;
        $sql      = "select {$cols}  from {$tblName} where 1 {$whereSql} {$groupBy} {$orderSql} {$limitSql}";
        if($debug) { echo $sql.'<br>';}
        $query = $db->query($sql);
        $fetchStyle = is_numeric($column) ? PDO::FETCH_NUM : PDO::FETCH_ASSOC;
        $results = false;
        while ($row = $query->fetch($fetchStyle)) {
            $results[] = $row[$column];
        }
        return $results;
    }
    
    
    /**
     * ��ȡһ�Զ�
     */
    public static function getPairs($paramArr)
    {
        $options = array(
            'dbName'        =>  self::$initDbName,    #���ݿ���
            'tblName'       =>  self::$initTblName,    #����
            'cols'          =>  '*',   #����
            'offset'        =>  0,     #offset
            'limit'         =>  '',    #����
            'whereSql'      =>  '',    #where����
            'groupBy'       =>  '',    #group by
            'orderSql'      =>  '',    #where����
            'keyName'       =>  '',    #��Ϊkey����
            'valName'       =>  '',    #��Ϊֵ����
            'debug'         =>  0      #��ʾsql
            
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        $db = ZOL_Db::instance($dbName);
        $limitSql = "";
        if($limit){
            $limitSql = " limit ";
            if($offset)$limitSql .= $offset . ",";
            $limitSql .= $limit;
        }
        if($groupBy) $groupBy = ' group by ' . $groupBy;
        $sql      = "select {$cols}  from {$tblName} where 1 {$whereSql} {$groupBy} {$orderSql} {$limitSql}";
        if($debug) echo $sql;
        
        return $db->getPairs($sql,$keyName,$valName);
        
    }
}
?>