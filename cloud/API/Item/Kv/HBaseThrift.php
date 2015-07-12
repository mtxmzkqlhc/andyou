<?php
/**
* Hbase的Thrift访问形式
* @author zhongwt
* @copyright (c) 2013-03-05
*/
require ZOL_API_ROOT . '/Libs/HBase/Thrift.php';
require ZOL_API_ROOT . '/Libs/HBase/Hbase.php';
require ZOL_API_ROOT . '/Libs/HBase/Types.php';
require_once( $GLOBALS['THRIFT_ROOT'].'/transport/TSocket.php');
require_once( $GLOBALS['THRIFT_ROOT'].'/transport/TBufferedTransport.php');
require_once( $GLOBALS['THRIFT_ROOT'].'/protocol/TBinaryProtocol.php');

class API_Item_Kv_HBaseThrift
{

    private static $socket = false;
    private static $client = false;
    private static $transport = false;
    /**
     * 初始化HBase的连接
     */
    public static function init($host = 'localhost'){
        if(!self::$socket){
            self::$socket = new TSocket($host, '9090');
            self::$socket->setSendTimeout(2000);//ms
            self::$socket->setRecvTimeout(20000);
            self::$transport       = new TBufferedTransport(self::$socket);
            $protocol        = new TBinaryProtocol(self::$transport);
            self::$client    = new HbaseClient($protocol);
            self::$transport->open();
        }
    }

    /**
     * 获得所有表名
     */
    public static function getTableNames(){
        self::init($host);
        $tables = self::$client->getTableNames();
        return $tables;
    }
    
    public static function getRows(){
        self::init($host);
        return self::$client->getRows("zwt_table2","row_test_count_1");
        
        
    }
    /**
     * 获得一个数据
     */
    public static function get($paramArr){
		$options = array(
            'table'    => '', #表名
            'row'      => '', #行名
            'col'      => '', #列名
            'host'     => 'localhost', #服务器
            'port'     => '9090', #端口
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


        self::init($host);
        $data = self::$client->get($table,$row,$col,null);
        if($data){
            return array('value'=>$data[0]->value,'tm'=>$data[0]->timestamp);
        }
    }
    /**
     * 获得一个数据的多个版本
     */
    public static function getVers($paramArr){
		$options = array(
            'table'    => '', #表名
            'row'      => '', #行名
            'col'      => '', #列名
            'num'      => 10, #版本数量
            'maxtm'    => 0,  #最大的时间戳
            'host'     => 'localhost', #服务器
            'port'     => '9090', #端口
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        
        self::init($host);
        if($maxtm){
            $data = self::$client->getVerTs($table, $row, $col,$maxtm, $num, null);
        }else{
            $data = self::$client->getVer($table, $row, $col, $num, null);
        }
        return $data;
    }

    /**
     * 获得一行数据
     */
    public static function getRow($paramArr){
		$options = array(
            'table'    => '', #表名
            'row'      => '', #行名
            'col'      => '', #列名
            'host'     => 'localhost', #服务器
            'port'     => '9090', #端口
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


        self::init($host);
        $data = self::$client->getRow($table,$row,null);
        if($data){
            return $data[0]->columns;
        }
    }

    /**
     * 设置一个数据
     */
    public static function set($paramArr){
		$options = array(
            'table'    => '', #表名
            'row'      => '', #行名
            'col'      => '', #列名
            'value'    => '', #数值
            'host'     => 'localhost', #服务器
            'port'     => '9090', #端口
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        self::init($host);
        if(is_array($value)){
            $mutations = array();
            foreach($value as $k => $v){
                $mutations[] =  new Mutation(array(
                    'column' => $k,
                    'value'  => $v
                ));
            }
            $batchTmp = array(new BatchMutation(array('row'=>$row, 'mutations'=>$mutations)));
            self::$client->mutateRows($table, $batchTmp,null);
        }else{
            $mutations = array(
                new Mutation(array(
                    'column' => $col,
                    'value'  => $value
                )),
            );
            self::$client->mutateRow($table, $row, $mutations,null);
        }

    }
    /**
     * 扫描，取得多条数据
     */
    public static function scan($paramArr){
		$options = array(
            'table'      => '', #表名
            'startKey'   => '', #开始行名
            'endKey'     => '', #结束行名
            'col'        => '', #列名数组
            'host'     => 'localhost', #服务器
            'port'     => '9090', #端口
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        self::init($host);
        
        $columns = is_array($col) ? $col : array($col);
        $result =self::$client->scannerOpenWithStop($table, $startKey, $endKey, $columns,null);
        $resultArray = array();
        while (true) {
             $record = self::$client->scannerGetList($result,1);
             if ($record == NULL) break;

             foreach($record as $TRowResult) {
                 $rowKey = $TRowResult->row;
                 $column = $TRowResult->columns;
                 $recordArray = array();
                 foreach($column as $colName => $cell) {
                     $recordArray[$colName] = array('name'=>$cell->value,'tm'=>$cell->timestamp);
                 }
                 $resultArray[$rowKey] = $recordArray;
             }
         }
         self::$client->scannerClose($result);
         return $resultArray;
    }

    /**
     * 关闭连接
     */
    public static function close (){
        self::$transport->close();
    }

    /**
     * 创建表
     */
    public static function createTable(){

        self::init($host);
        $columns = array(
            new ColumnDescriptor(array(
                'name' => 'cf1:',
                'maxVersions' => 1
            )),
        );

        $tableName = "mojing_subcate_userhistory";
        try {
            self::$client->createTable($tableName, $columns);
        } catch (AlreadyExists $ae) {} 
    }
    

		//$row = $hbase->tables->zwt_table2->row('row_test_count_1');
    


}
