<?php

/**
 * 上传公共类
 * 
 * 
 */


class Helper_Func_Image extends Helper_Abstract {
    # 私有云上传业务标识
    //1.mercrt 经销商资质认证图片 （默认）

    private static $uploadModuleName = 'common';

    # 私有云上传业务链接★上线时去掉test★
    private static $uploadModuleUrl = 'http://upload.fd.zol.com.cn/test/upload.php';

    # 允许图片类型
    private static $allowImgType = array(
        '.png' => array(
            'image/x-png', 'image/png'
        ),
        '.jpg' => array(
            'image/pjpeg', 'image/jpeg'
        ),
        '.jpeg' => array(
            'image/pjpeg', 'image/jpeg'
        ),
        '.bmp' => array(
            'image/bmp'
        ),
        '.gif' => array(
            'image/gif'
        ),
    );
    #允许文件类型
    private static   $allowFileType  =   array(
            '.txt' => array('text/plain'), 
            '.doc' => array('application/msword'),     
            '.ppt' => array('application/vnd.ms-powerpoint'), 
            '.wps' => array('application/kswps'),
            '.odt' => array('application/vnd.oasis.opendocument.text'),
            '.docx'=> array('application/vnd.openxmlformats-officedocument.wordprocessingml.document')
    );
    

    # 允许图片尺寸单位
    private static $allowSizeType = array(
        'B' => 1,
        'KB' => 1024,
        'MB' => 1048576,
    );
    
    
    /***
     * 上传文件
     * code wangmc
     * date 20140504
     */
    public static function uploadFile($params = array()) {
        # 初始化参数
        $outArr = array();
        # 默认参数
        $default = array(
            'imgType'       => array('.doc', '.txt','.docx'), // 上传格式限制
            'limitSize'     => '3', // 上传尺寸限制
            'limitSizeUnit' => 'MB', // 上传尺寸限制单位，默认为MB
            'fileInfo'      => '', // $_FILE['filename'] 或 $input->file('filename')
            'waterType'     => 0, // 水印类型：0：无水印；1：店铺图片水印
            'userId'        => '',
            'dirPath'       => ''
        );
        # 合并参数
        if (is_array($params) && !empty($params)) {
            $default = array_merge($default, $params);
        }
        extract($default);

        if (is_array($fileInfo) && !empty($fileInfo)) {
            # 初始化图片参数
            $fileName     = $fileInfo['name'];
            $fileType     =  $fileInfo['type'];
            
            $fileTmpName = $fileInfo['tmp_name'];
            $fileError = $fileInfo['error'];
            $fileSize = $fileInfo['size'];
            $waterType  = 0;
            # 判断系统错误
            switch ($fileError) {
                case 1:
                    $outArr = array('flag' => false, 'msg' => '上传的文件尺寸过大');
                    return $outArr;
                    break;
                case 2:
                    $outArr = array('flag' => false, 'msg' => '上传的文件尺寸超过最大值');
                    return $outArr;
                    break;
                case 3:
                    $outArr = array('flag' => false, 'msg' => '上传的文件不完整');
                    return $outArr;
                    break;
                case 4:
                    $outArr = array('flag' => false, 'msg' => '未上传文件');
                    return $outArr;
                    break;
                case 5:
                    $outArr = array('flag' => false, 'msg' => '上传文件的大小为0');
                    return $outArr;
                    break;
            }
            # 获取文件内容
            $fileMd5 = md5_file($fileTmpName);
            # 如果MD5相同优先取
            $localFileInfo = array();

            # 判断文件格式
            $allowTypeArr = self::getAllowType($imgType,true);

            if (!in_array($fileType, $allowTypeArr)) {
                $outArr = array('flag' => false, 'msg' => '文件格式不正确');
                return $outArr;
            }
            # 判断文件大小
            $imgLimitSize = self::getAllowSize($limitSize, $limitSizeUnit);
            if ($fileSize > $imgLimitSize) {
                $outArr = array('flag' => false, 'msg' => '文件大小超过限制，请上传' . $limitSize . 'M以下的文件');
                return $outArr;
            }

            # 缓存文件路径
            $fileRoot   = "/www/";
            $cacheDir   = "upload/articleOrgDoc/";
            
  
            
            $uploadPath = $dirPath ? $dirPath:$cacheDir;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            if($userId){
                $strOrd        = ord($userId);
                $dirStr        = ceil($strOrd/1000);
                $uploadPath    = $uploadPath."{$dirStr}/";
                
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
            }
            $allPath  = $fileRoot.$uploadPath;
            
            if (!is_dir($allPath)) {
                mkdir($allPath, 0777, true);
            }
            $cacheName     =   date('YmdHis').'_'.rand(100,999) . self::getFileType($fileType,true);
            # 生成缓存文件
            $cacheFileName = $allPath. $cacheName;
            # 保存地址
            $savePath      = $uploadPath .$cacheName;
            
            $isOk          = false;
            #echo  "####{$fileTmpName}#####{$cacheFileName}###";
            if(is_uploaded_file($fileTmpName)){
                $isOk = move_uploaded_file($fileTmpName, $cacheFileName);
            }
   
            if ($isOk) {
                $outArr = array('flag' => true, 'msg' => '文件已上传成功', 'fileName' => $savePath,'fileSize'=>$fileSize,'orgName'=>$fileName);
                return $outArr;
            } else {
                $outArr = array('flag' => false, 'msg' => '上传文件失败，请重新上传');
                return $outArr;
            }
            
        } else {
            $outArr = array('flag' => false, 'msg' => '请上传文件');
            return $outArr;
        }
    }

    /*
     * 上传图片公用方法
     * @param       Array     $params
     * @retrun      Json
     */

    public static function uploadImage($params = array()) {
        # 初始化参数
        $outArr = array();
        # 默认参数
        $default = array(
            'imgType' => array('.png', '.jpg', '.jpeg', '.gif'), // 上传格式限制
            'limitSize' => '3', // 上传尺寸限制
            'limitSizeUnit' => 'MB', // 上传尺寸限制单位，默认为MB
            'fileInfo' => '', // $_FILE['filename'] 或 $input->file('filename')
            'waterType' => 0, // 水印类型：0：无水印；1：店铺图片水印
        );
        # 合并参数
        if (is_array($params) && !empty($params)) {
            $default = array_merge($default, $params);
        }
        extract($default);
        
        if (is_array($fileInfo) && !empty($fileInfo)) {
            # 初始化图片参数
            $fileName = $fileInfo['name'];
            $fileType = $fileInfo['type'] == 'application/octet-stream' ? 'image/jpeg' : $fileInfo['type'];

            $fileTmpName = $fileInfo['tmp_name'];
            $fileError = $fileInfo['error'];
            $fileSize = $fileInfo['size'];
            $waterType  = 0;
            # 判断系统错误
            switch ($fileError) {
                case 1:
                    $outArr = array('flag' => false, 'msg' => '上传的图片尺寸过大');
                    return $outArr;
                    break;
                case 2:
                    $outArr = array('flag' => false, 'msg' => '上传的图片尺寸超过最大值');
                    return $outArr;
                    break;
                case 3:
                    $outArr = array('flag' => false, 'msg' => '上传的图片不完整');
                    return $outArr;
                    break;
                case 4:
                    $outArr = array('flag' => false, 'msg' => '未上传图片');
                    return $outArr;
                    break;
                case 5:
                    $outArr = array('flag' => false, 'msg' => '上传图片的大小为0');
                    return $outArr;
                    break;
            }
            # 获取文件内容
            $fileMd5 = md5_file($fileTmpName);
            # 如果MD5相同优先取
            $localFileInfo = array();

            # 判断图片格式
            $allowTypeArr = self::getAllowType($imgType);
//            if (!in_array($fileType, $allowTypeArr)) {
//                $outArr = array('flag' => false, 'msg' => '图片格式不正确');
//                return $outArr;
//            }
            # 判断图片宽高
            $imageSize = getimagesize($fileTmpName);
            if ($imageSize[0] <= 0 || $imageSize[1] <= 0) {
                $outArr = array('flag' => false, 'msg' => '图片尺寸不正确');
                return $outArr;
            }
            # 判断图片大小
            $imgLimitSize = self::getAllowSize($limitSize, $limitSizeUnit);
            if ($fileSize > $imgLimitSize) {
                $outArr = array('flag' => false, 'msg' => '图片大小超过限制，请上传' . $limitSize . 'M以下的图片');
                return $outArr;
            }
            # 缓存文件名称
            $cacheName = $fileMd5 . self::getFileType($fileType);
            # 缓存文件路径
            $cacheDir = "/www/CacheUploadImage/";
            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0777, true);
            }
            # 生成缓存文件
            $cacheFileName = $cacheDir . $cacheName;
            if ($waterType) {
                @system("convert -geometry 800x600 $fileTmpName $cacheFileName");
            } else {
                @move_uploaded_file($fileTmpName, $cacheFileName);
            }
            chmod($cacheFileName, 0777);
            $backInfo = self::apiUploadImage(array('cacheFileName' => $cacheFileName));
            # 反序列化结果
            $backInfoArr = json_decode($backInfo, true);
    
            if ($backInfoArr['errno'] == 0) {
                # 插入日志                  
                $insertArr   = array(
                    'fullName'      =>$backInfoArr['fullName'],
                     'groupName'    =>$backInfoArr['groupName'],
                     'fileName'     =>$backInfoArr['fileName'],
                     'height'       =>$backInfoArr['height'],
                     'width'        =>$backInfoArr['width'],
                      'ext'         =>$backInfoArr['ext'],
                     'size'         =>$backInfoArr['size'],
                    'water_type'    =>$waterType,
                    'localMd5'      =>$fileMd5,
                    'add_time'      =>time()

                );
                $outArr = array('flag' => true, 'msg' => '图片已上传成功', 'fileName' => $backInfoArr['fullName']);
                return $outArr;
            } else {
                $outArr = array('flag' => false, 'msg' => '上传图片失败，请重新上传');
                return $outArr;
            }
            
        } else {
            $outArr = array('flag' => false, 'msg' => '请上传图片');
            return $outArr;
        }
    }

    /**
     * 上传图片至私有云
     * @author yuhx
     * @return array
     */
    public static function apiUploadImage($params) {
        $backInfo = array();
        $options = array(
            'cacheFileName' => '',
        );
        if (is_array($params)) {
            $options = array_merge($options, $params);
        }
        extract($options);
        # 发送缓存文件至私有云图片服务器
        $data = array("uploadModuleName" => self::$uploadModuleName, "file" => "@" . $cacheFileName); //文件路径，前面要加@，表明是文件上传.
        $ch = curl_init();
        if (defined(CURLOPT_IPRESOLVE) && defined(CURL_IPRESOLVE_V4)) {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        curl_setopt($ch, CURLOPT_URL, self::$uploadModuleUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $backInfo = curl_exec($ch);
        curl_close($ch);
        # 如果上传成功删除本地缓存
        sleep(1);
        @unlink($cacheFileName);
        return $backInfo;
    }

    /**
     * 获得新的图片地址
     * @param $fileName 文件 /g3/M05/0E/03/Cg-4WFCkmziIaA9KAABYzyJLDfwAAB4lgPCnb0AAFjn012.jpg
     * @param $size 尺寸 如120x90
     */
    public static function getUploadImage($fileName, $size = false) {
        if (!$fileName)
            return '';

        $partUri = "/" . ltrim($fileName, "/");
        if ($size)
            $partUri = "/t_s" . $size . $partUri;

        $rd = ord(substr($partUri, -5, 1)) % 6;
        return "http://i{$rd}." . self::$uploadModuleName . ".fd.zol-img.com.cn" . $partUri;
    }

    # 获取文件格式

    private static function getFileType($fileType,$isFile=false) {
        if (!empty($fileType)) {
           if($isFile){
                $limitType = self::$allowFileType;
            }else{
                $limitType = self::$allowImgType;
            }
            
            foreach ($limitType as $allowImgTypeKey => $allowImgTypeVal) {
                if (in_array($fileType, $allowImgTypeVal)) {
                    return $allowImgTypeKey;
                }
            }
        }
    }

    # 获取允许上传图片尺寸

    private static function getAllowSize($limitSize = '3', $limitSizeUnit = 'MB') {
        # 允许上传的图片尺寸
        $imgLimitSize = 0;
        # 允许上传的图片尺寸单位
        $allowKey = array_keys(self::$allowSizeType);
        if (in_array($limitSizeUnit, $allowKey)) {
            # 获取单位信息
            $sizeType = self::$allowSizeType[$limitSizeUnit];
            # 计算允许图片尺寸
            if (!empty($sizeType)) {
                $imgLimitSize = $limitSize * $sizeType;
            }
        }
        return $imgLimitSize;
    }

    # 获取允许上传图片类型

    private static function getAllowType($imgType = array(),$isFile=false) {
        # 允许上传的type
        $allowTypeArr = array();
        $limitType    = array();
        if($isFile){
            $limitType = self::$allowFileType;
        }else{
            $limitType = self::$allowImgType;
        }
        # 获取允许类型的数组
        $allowKey = array_keys($limitType);
        # 获取类型的交集
        $intersectArr = array_intersect($allowKey, $imgType);
        # 取出相应type
        if (is_array($intersectArr) && !empty($intersectArr)) {
            foreach ($intersectArr as $intersectVal) {
                if (is_array($limitType[$intersectVal])) {
                    $allowTypeArr = array_merge($limitType[$intersectVal], $allowTypeArr);
                }
            }
        }
        return $allowTypeArr;
    }

    # 设置业务名称

    public static function setUploadModuleName($moduleName = '') {
        if ($moduleName) {
            self::$uploadModuleName = $moduleName;
        }
    }
    
    /**
     * 获得远程图片到本地
     * code wangmc
     * date 2014
     */
    
    public  static  function   getLocationPic($paramArr){
        $options = array(
                'picUrl'          =>  '', #图片地址
                'picPath'         =>  'temp'
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        if(empty($picUrl)){
            return false;
        }
        
        $picPath  = !empty($picPath) ? $picPath."/" :'';
        $uploadRoot =  "/tmp/CreditZip/";
        if(!is_dir($uploadRoot)){
            mkdir($uploadRoot,0777);
        }
        $uploadDir  = $uploadRoot."{$picPath}";
        if(!is_dir($uploadDir)){
            mkdir($uploadDir,0777);
        }
        $locationPath  = array();
        if(is_array($picUrl)){
                    foreach ($picUrl  as $val){
                          $temp            = $uploadDir.basename($picUrl);
                          $locationPath[]  = $temp;
                          file_put_contents($temp, file_get_contents($picUrl));  
                    }
        }else{
                 $temp            = $uploadDir.basename($picUrl);
                 $locationPath    = $temp;
                 file_put_contents($temp, file_get_contents($picUrl));       
        } 
        return $locationPath;
    }
    

}

?>
