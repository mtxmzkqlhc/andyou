<?php
/**
 * ajax����vba����ģ��
 */
class Andyou_Page_Ajax_VbaModel extends ZOL_Abstract_Page{

    public function __construct(){}

    public function validate(ZOL_Request $input, ZOL_Response $output){
        $output->pageType     = 'Ajax_VbaModel';
        return true;
    }
    
      /**
     * ��ȡ���ݱ�ṹ
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
          //ֻ��ȡ��Ҫ���ֶ�
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
     * ����VBA����
     */
	 public function doSetConfig(ZOL_Request $input, ZOL_Response $output) {
        $Arr = $upArr = array();
        $Arr['dbName']    = $input->post('db');
        $Arr['tableName']    = $input->post('tbl');
        $upArr['content'] = $Arr['content']   = $input->post('con', true)?substr(ZOL_String::u8conv($input->post('con', true)),1):'';
        
        $flag = Helper_Dao::getOne(array(
                'dbName'        =>  'Db_UserData',    #���ݿ���
		        'tblName'       =>  'vba_config',    #����
                'cols'          =>  'count(*)',    #����
                'whereSql'      =>  ' and dbName="'.$Arr['dbName'].'" and tableName="'.$Arr['tableName'].'"',    #where����
		));
        if($flag){
             $data = Helper_Dao::updateItem(array(
                'editItem'      =>  $upArr, #��������
                'dbName'        =>  'Db_UserData',    #���ݿ���
                'tblName'       =>  'vba_config',   #����
                'where'      =>  ' dbName="'.$Arr['dbName'].'" and tableName="'.$Arr['tableName'].'"',    #where����
            ));
        }else{
            $data = Helper_Dao::insertItem(array(
                    'addItem'       =>  $Arr, #������
                    'dbName'        =>  'Db_UserData',    #���ݿ���
                    'tblName'       =>  'vba_config',    #����
            ));
        
        }
        
        if($data){
            echo 1;
        }

		exit;  
	 }
     
      /**
     * ��ȡVBA����
     */
	 public function doGetConfig(ZOL_Request $input, ZOL_Response $output) {
        $dbName = $input->get('db');
        $tblName = $input->get('tbl');
		$data = Helper_Dao::getOne(array(
                'dbName'        =>  'Db_UserData',    #���ݿ���
		        'tblName'       =>  'vba_config',    #����
                'cols'          =>  'content',    #����
                'whereSql'      =>  ' and dbName="'.$dbName.'" and tableName="'.$tblName.'"',    #where����
		));
        echo mb_convert_encoding($data,"UTF-8","GBK");
		exit;
	 }
}


