<?php
/**
 * ajax调用vba操作模块
 */
class Andyou_Page_Ajax_VbaModel extends ZOL_Abstract_Page{

    public function __construct(){}

    public function validate(ZOL_Request $input, ZOL_Response $output){
        $output->pageType     = 'Ajax_VbaModel';
        return true;
    }
    
      /**
     * 获取数据表结构
     */
    public function doShowDb(ZOL_Request $input, ZOL_Response $output){             
        $dbName = $input->get('db');
        $tblName = $input->get('tbl');
        //$dbName ='Db_'.$dbName;
        if($dbName){
            $dbName = str_replace('ZOL_', '', $dbName);
        }
        $userdb  = $dbName::instance();
        $all = $userdb->getAll('SHOW FULL COLUMNS FROM '.$tblName);
          //只获取需要的字段
        $needArr =array('Field','Type','Null','Key','Default','Extra','Comment');
         //print_r($all);
         if($all){
             $out = '';
             foreach($all as $v){
                 if($v){
                     $out .= '|';
                     foreach($v as $k=>$vv){
                         if(in_array($k,$needArr)){
                            $out .= $k.'#'.$vv.'@';
                         }
                     }
                 }
             }
         }
         echo substr(trim($out),1);
         exit;
    }
     /**
     * 存入VBA配置
     */
	 public function doSetConfig(ZOL_Request $input, ZOL_Response $output) {
        $Arr = $upArr = array();
        $Arr['dbName']    = $input->post('db');
        $Arr['tableName']    = $input->post('tbl');
        $upArr['content'] = $Arr['content']   = $input->post('con', true)?substr(ZOL_String::u8conv($input->post('con', true)),1):'';
        
        $flag = Helper_Dao::getOne(array(
                'dbName'        =>  'Db_UserData',    #数据库名
		        'tblName'       =>  'vba_config',    #表名
                'cols'          =>  'count(*)',    #列名
                'whereSql'      =>  ' and dbName="'.$Arr['dbName'].'" and tableName="'.$Arr['tableName'].'"',    #where条件
		));
        if($flag){
             $data = Helper_Dao::updateItem(array(
                'editItem'      =>  $upArr, #更新数据
                'dbName'        =>  'Db_UserData',    #数据库名
                'tblName'       =>  'vba_config',   #表名
                'where'      =>  ' dbName="'.$Arr['dbName'].'" and tableName="'.$Arr['tableName'].'"',    #where条件
            ));
        }else{
            $data = Helper_Dao::insertItem(array(
                    'addItem'       =>  $Arr, #数据列
                    'dbName'        =>  'Db_UserData',    #数据库名
                    'tblName'       =>  'vba_config',    #表名
            ));
        
        }
        
        if($data){
            echo 1;
        }

		exit;  
	 }
     
      /**
     * 读取VBA配置
     */
	 public function doGetConfig(ZOL_Request $input, ZOL_Response $output) {
        $dbName = $input->get('db');
        $tblName = $input->get('tbl');
		$data = Helper_Dao::getOne(array(
                'dbName'        =>  'Db_UserData',    #数据库名
		        'tblName'       =>  'vba_config',    #表名
                'cols'          =>  'content',    #列名
                'whereSql'      =>  ' and dbName="'.$dbName.'" and tableName="'.$tblName.'"',    #where条件
		));
        echo mb_convert_encoding($data,"UTF-8","GBK");
		exit;
	 }
}


