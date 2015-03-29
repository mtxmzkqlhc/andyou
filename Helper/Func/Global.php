<?php
/**
 * ȫ��ͨ�õĺ���
 * ��ΰ��
 */
class Helper_Func_Global extends Helper_Abstract {
         

    //����ĳ��ĵڼ������ڼ����ؾ�������
    public   static   function getWeekDate($paramArr){
            $options = array(
                'week'          =>  '', #�ڼ���
                'year'          =>  date('Y'), #���
                'format'        =>'m-d'
            );
            if (is_array($paramArr))$options = array_merge($options, $paramArr);
            extract($options);
            if(empty($week)){
                return false;
            }
            #$week,$year
            $timestamp = mktime(0,0,0,1,1,$year);
            $dayofweek = date("w",$timestamp);
            if($week != 1){
                $distance = ($week - 1)*7 - $dayofweek + 1;
            }else{
                if($dayofweek == 0){
                    $dayofweek =7;
                }
                $distance =1 - $dayofweek;
            }
            $passed_seconds = $distance * 86400;
            $timestamp +=$passed_seconds;
            $first_date_of_week = date($format,$timestamp);
            $timestamp += 6 * 86400;
            $last_date_of_week = date($format,$timestamp);
            return array($first_date_of_week,$last_date_of_week);
    }
    
    /**
     * ���ѹ��
     * code wangmc
     */

     public  static    function setZipFile($paramArr){ 
        $options = array(
                'filePath'          =>  '', #���·��    
                'zipName'           =>  ''  #����ļ���
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        if(empty($filePath)){
            return false;
        }
        
        $uploadRoot =  "/tmp/CreditZip/";
        if(!is_dir($uploadRoot)){
            mkdir($uploadRoot,0777);
        }

        $zipName  =  $filePath.$zipName;
        if(is_file($zipName)){
               unlink($zipName);
        }
        $zip      =  new ZipArchive(); 
        $zip->open($zipName, ZipArchive::OVERWRITE);
        $handler  =  opendir($filePath); //�򿪵�ǰ�ļ�����$pathָ����
        while(($fileName = readdir($handler))!==false){
            if($fileName != "." && $fileName != ".."){//�ļ����ļ�����Ϊ'.'�͡�..������Ҫ�����ǽ��в���
                $fName  = $filePath.$fileName;
                list($name,$type)  = explode('.', $fName);
                $type  = strtolower($type);
                if(is_file($fName) || $type!='zip'){ //���ļ�����zip����
                    $zip->addFile($fName);
                }
            }
        }
        @closedir($handler);  
        return  $zipName;
    }
    
    
    /**
     * �����ļ�
     * 
     */
    
    public   static    function  downFile($paramArr){
        $options = array(
                'filePath'          =>  '',  #�����ļ�·��    
                'fileType'          =>  '',  #�ļ�����
                'fileName'          =>  ''   #�ļ���
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        if(empty($filePath) || empty($fileType)){
            return false;
        }
        if(is_file($filePath)){
            $fileType        = strtolower($fileType);
            $fileTypeConf    = ZOL_Config::get('Star_FileType');
            $fileTypeStr     = !empty($fileTypeConf[$fileType])?$fileTypeConf[$fileType]:'text/plain'; 
            $fileName        = $fileName?$fileName:basename($filePath);
            header('Content-type: '.$fileTypeStr);//���������
            header('Content-Disposition: attachment; filename="'.$fileName.'"'); //������ʾ������,ע���ʽ
            
            readfile($filePath);
        }else{
            return false;
        }
        
        
    }
    
    
}
