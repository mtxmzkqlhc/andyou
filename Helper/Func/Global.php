<?php
/**
 * 全局通用的函数
 * 仲伟涛
 */
class Helper_Func_Global extends Helper_Abstract {
         

    //根据某年的第几周星期几返回具体日期
    public   static   function getWeekDate($paramArr){
            $options = array(
                'week'          =>  '', #第几周
                'year'          =>  date('Y'), #年份
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
     * 打包压缩
     * code wangmc
     */

     public  static    function setZipFile($paramArr){ 
        $options = array(
                'filePath'          =>  '', #打包路径    
                'zipName'           =>  ''  #打包文件名
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
        $handler  =  opendir($filePath); //打开当前文件夹由$path指定。
        while(($fileName = readdir($handler))!==false){
            if($fileName != "." && $fileName != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
                $fName  = $filePath.$fileName;
                list($name,$type)  = explode('.', $fName);
                $type  = strtolower($type);
                if(is_file($fName) || $type!='zip'){ //将文件加入zip对象
                    $zip->addFile($fName);
                }
            }
        }
        @closedir($handler);  
        return  $zipName;
    }
    
    
    /**
     * 下载文件
     * 
     */
    
    public   static    function  downFile($paramArr){
        $options = array(
                'filePath'          =>  '',  #本地文件路径    
                'fileType'          =>  '',  #文件类型
                'fileName'          =>  ''   #文件名
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
            header('Content-type: '.$fileTypeStr);//输出的类型
            header('Content-Disposition: attachment; filename="'.$fileName.'"'); //下载显示的名字,注意格式
            
            readfile($filePath);
        }else{
            return false;
        }
        
        
    }
    
    
}
