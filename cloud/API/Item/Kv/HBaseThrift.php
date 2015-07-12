<?php
/**
* Hbase��Thrift������ʽ
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
     * ��ʼ��HBase������
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
     * ������б���
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
     * ���һ������
     */
    public static function get($paramArr){
		$options = array(
            'table'    => '', #����
            'row'      => '', #����
            'col'      => '', #����
            'host'     => 'localhost', #������
            'port'     => '9090', #�˿�
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
     * ���һ�����ݵĶ���汾
     */
    public static function getVers($paramArr){
		$options = array(
            'table'    => '', #����
            'row'      => '', #����
            'col'      => '', #����
            'num'      => 10, #�汾����
            'maxtm'    => 0,  #����ʱ���
            'host'     => 'localhost', #������
            'port'     => '9090', #�˿�
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
     * ���һ������
     */
    public static function getRow($paramArr){
		$options = array(
            'table'    => '', #����
            'row'      => '', #����
            'col'      => '', #����
            'host'     => 'localhost', #������
            'port'     => '9090', #�˿�
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
     * ����һ������
     */
    public static function set($paramArr){
		$options = array(
            'table'    => '', #����
            'row'      => '', #����
            'col'      => '', #����
            'value'    => '', #��ֵ
            'host'     => 'localhost', #������
            'port'     => '9090', #�˿�
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
     * ɨ�裬ȡ�ö�������
     */
    public static function scan($paramArr){
		$options = array(
            'table'      => '', #����
            'startKey'   => '', #��ʼ����
            'endKey'     => '', #��������
            'col'        => '', #��������
            'host'     => 'localhost', #������
            'port'     => '9090', #�˿�
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
     * �ر�����
     */
    public static function close (){
        self::$transport->close();
    }

    /**
     * ������
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
