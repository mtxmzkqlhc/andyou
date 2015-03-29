<?php

/**
 * �ϴ�������
 * 
 * 
 */


class Helper_Func_Image extends Helper_Abstract {
    # ˽�����ϴ�ҵ���ʶ
    //1.mercrt ������������֤ͼƬ ��Ĭ�ϣ�

    private static $uploadModuleName = 'common';

    # ˽�����ϴ�ҵ�����ӡ�����ʱȥ��test��
    private static $uploadModuleUrl = 'http://upload.fd.zol.com.cn/test/upload.php';

    # ����ͼƬ����
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
    #�����ļ�����
    private static   $allowFileType  =   array(
            '.txt' => array('text/plain'), 
            '.doc' => array('application/msword'),     
            '.ppt' => array('application/vnd.ms-powerpoint'), 
            '.wps' => array('application/kswps'),
            '.odt' => array('application/vnd.oasis.opendocument.text'),
            '.docx'=> array('application/vnd.openxmlformats-officedocument.wordprocessingml.document')
    );
    

    # ����ͼƬ�ߴ絥λ
    private static $allowSizeType = array(
        'B' => 1,
        'KB' => 1024,
        'MB' => 1048576,
    );
    
    
    /***
     * �ϴ��ļ�
     * code wangmc
     * date 20140504
     */
    public static function uploadFile($params = array()) {
        # ��ʼ������
        $outArr = array();
        # Ĭ�ϲ���
        $default = array(
            'imgType'       => array('.doc', '.txt','.docx'), // �ϴ���ʽ����
            'limitSize'     => '3', // �ϴ��ߴ�����
            'limitSizeUnit' => 'MB', // �ϴ��ߴ����Ƶ�λ��Ĭ��ΪMB
            'fileInfo'      => '', // $_FILE['filename'] �� $input->file('filename')
            'waterType'     => 0, // ˮӡ���ͣ�0����ˮӡ��1������ͼƬˮӡ
            'userId'        => '',
            'dirPath'       => ''
        );
        # �ϲ�����
        if (is_array($params) && !empty($params)) {
            $default = array_merge($default, $params);
        }
        extract($default);

        if (is_array($fileInfo) && !empty($fileInfo)) {
            # ��ʼ��ͼƬ����
            $fileName     = $fileInfo['name'];
            $fileType     =  $fileInfo['type'];
            
            $fileTmpName = $fileInfo['tmp_name'];
            $fileError = $fileInfo['error'];
            $fileSize = $fileInfo['size'];
            $waterType  = 0;
            # �ж�ϵͳ����
            switch ($fileError) {
                case 1:
                    $outArr = array('flag' => false, 'msg' => '�ϴ����ļ��ߴ����');
                    return $outArr;
                    break;
                case 2:
                    $outArr = array('flag' => false, 'msg' => '�ϴ����ļ��ߴ糬�����ֵ');
                    return $outArr;
                    break;
                case 3:
                    $outArr = array('flag' => false, 'msg' => '�ϴ����ļ�������');
                    return $outArr;
                    break;
                case 4:
                    $outArr = array('flag' => false, 'msg' => 'δ�ϴ��ļ�');
                    return $outArr;
                    break;
                case 5:
                    $outArr = array('flag' => false, 'msg' => '�ϴ��ļ��Ĵ�СΪ0');
                    return $outArr;
                    break;
            }
            # ��ȡ�ļ�����
            $fileMd5 = md5_file($fileTmpName);
            # ���MD5��ͬ����ȡ
            $localFileInfo = array();

            # �ж��ļ���ʽ
            $allowTypeArr = self::getAllowType($imgType,true);

            if (!in_array($fileType, $allowTypeArr)) {
                $outArr = array('flag' => false, 'msg' => '�ļ���ʽ����ȷ');
                return $outArr;
            }
            # �ж��ļ���С
            $imgLimitSize = self::getAllowSize($limitSize, $limitSizeUnit);
            if ($fileSize > $imgLimitSize) {
                $outArr = array('flag' => false, 'msg' => '�ļ���С�������ƣ����ϴ�' . $limitSize . 'M���µ��ļ�');
                return $outArr;
            }

            # �����ļ�·��
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
            # ���ɻ����ļ�
            $cacheFileName = $allPath. $cacheName;
            # �����ַ
            $savePath      = $uploadPath .$cacheName;
            
            $isOk          = false;
            #echo  "####{$fileTmpName}#####{$cacheFileName}###";
            if(is_uploaded_file($fileTmpName)){
                $isOk = move_uploaded_file($fileTmpName, $cacheFileName);
            }
   
            if ($isOk) {
                $outArr = array('flag' => true, 'msg' => '�ļ����ϴ��ɹ�', 'fileName' => $savePath,'fileSize'=>$fileSize,'orgName'=>$fileName);
                return $outArr;
            } else {
                $outArr = array('flag' => false, 'msg' => '�ϴ��ļ�ʧ�ܣ��������ϴ�');
                return $outArr;
            }
            
        } else {
            $outArr = array('flag' => false, 'msg' => '���ϴ��ļ�');
            return $outArr;
        }
    }

    /*
     * �ϴ�ͼƬ���÷���
     * @param       Array     $params
     * @retrun      Json
     */

    public static function uploadImage($params = array()) {
        # ��ʼ������
        $outArr = array();
        # Ĭ�ϲ���
        $default = array(
            'imgType' => array('.png', '.jpg', '.jpeg', '.gif'), // �ϴ���ʽ����
            'limitSize' => '3', // �ϴ��ߴ�����
            'limitSizeUnit' => 'MB', // �ϴ��ߴ����Ƶ�λ��Ĭ��ΪMB
            'fileInfo' => '', // $_FILE['filename'] �� $input->file('filename')
            'waterType' => 0, // ˮӡ���ͣ�0����ˮӡ��1������ͼƬˮӡ
        );
        # �ϲ�����
        if (is_array($params) && !empty($params)) {
            $default = array_merge($default, $params);
        }
        extract($default);
        
        if (is_array($fileInfo) && !empty($fileInfo)) {
            # ��ʼ��ͼƬ����
            $fileName = $fileInfo['name'];
            $fileType = $fileInfo['type'] == 'application/octet-stream' ? 'image/jpeg' : $fileInfo['type'];

            $fileTmpName = $fileInfo['tmp_name'];
            $fileError = $fileInfo['error'];
            $fileSize = $fileInfo['size'];
            $waterType  = 0;
            # �ж�ϵͳ����
            switch ($fileError) {
                case 1:
                    $outArr = array('flag' => false, 'msg' => '�ϴ���ͼƬ�ߴ����');
                    return $outArr;
                    break;
                case 2:
                    $outArr = array('flag' => false, 'msg' => '�ϴ���ͼƬ�ߴ糬�����ֵ');
                    return $outArr;
                    break;
                case 3:
                    $outArr = array('flag' => false, 'msg' => '�ϴ���ͼƬ������');
                    return $outArr;
                    break;
                case 4:
                    $outArr = array('flag' => false, 'msg' => 'δ�ϴ�ͼƬ');
                    return $outArr;
                    break;
                case 5:
                    $outArr = array('flag' => false, 'msg' => '�ϴ�ͼƬ�Ĵ�СΪ0');
                    return $outArr;
                    break;
            }
            # ��ȡ�ļ�����
            $fileMd5 = md5_file($fileTmpName);
            # ���MD5��ͬ����ȡ
            $localFileInfo = array();

            # �ж�ͼƬ��ʽ
            $allowTypeArr = self::getAllowType($imgType);
//            if (!in_array($fileType, $allowTypeArr)) {
//                $outArr = array('flag' => false, 'msg' => 'ͼƬ��ʽ����ȷ');
//                return $outArr;
//            }
            # �ж�ͼƬ���
            $imageSize = getimagesize($fileTmpName);
            if ($imageSize[0] <= 0 || $imageSize[1] <= 0) {
                $outArr = array('flag' => false, 'msg' => 'ͼƬ�ߴ粻��ȷ');
                return $outArr;
            }
            # �ж�ͼƬ��С
            $imgLimitSize = self::getAllowSize($limitSize, $limitSizeUnit);
            if ($fileSize > $imgLimitSize) {
                $outArr = array('flag' => false, 'msg' => 'ͼƬ��С�������ƣ����ϴ�' . $limitSize . 'M���µ�ͼƬ');
                return $outArr;
            }
            # �����ļ�����
            $cacheName = $fileMd5 . self::getFileType($fileType);
            # �����ļ�·��
            $cacheDir = "/www/CacheUploadImage/";
            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0777, true);
            }
            # ���ɻ����ļ�
            $cacheFileName = $cacheDir . $cacheName;
            if ($waterType) {
                @system("convert -geometry 800x600 $fileTmpName $cacheFileName");
            } else {
                @move_uploaded_file($fileTmpName, $cacheFileName);
            }
            chmod($cacheFileName, 0777);
            $backInfo = self::apiUploadImage(array('cacheFileName' => $cacheFileName));
            # �����л����
            $backInfoArr = json_decode($backInfo, true);
    
            if ($backInfoArr['errno'] == 0) {
                # ������־                  
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
                $outArr = array('flag' => true, 'msg' => 'ͼƬ���ϴ��ɹ�', 'fileName' => $backInfoArr['fullName']);
                return $outArr;
            } else {
                $outArr = array('flag' => false, 'msg' => '�ϴ�ͼƬʧ�ܣ��������ϴ�');
                return $outArr;
            }
            
        } else {
            $outArr = array('flag' => false, 'msg' => '���ϴ�ͼƬ');
            return $outArr;
        }
    }

    /**
     * �ϴ�ͼƬ��˽����
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
        # ���ͻ����ļ���˽����ͼƬ������
        $data = array("uploadModuleName" => self::$uploadModuleName, "file" => "@" . $cacheFileName); //�ļ�·����ǰ��Ҫ��@���������ļ��ϴ�.
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
        # ����ϴ��ɹ�ɾ�����ػ���
        sleep(1);
        @unlink($cacheFileName);
        return $backInfo;
    }

    /**
     * ����µ�ͼƬ��ַ
     * @param $fileName �ļ� /g3/M05/0E/03/Cg-4WFCkmziIaA9KAABYzyJLDfwAAB4lgPCnb0AAFjn012.jpg
     * @param $size �ߴ� ��120x90
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

    # ��ȡ�ļ���ʽ

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

    # ��ȡ�����ϴ�ͼƬ�ߴ�

    private static function getAllowSize($limitSize = '3', $limitSizeUnit = 'MB') {
        # �����ϴ���ͼƬ�ߴ�
        $imgLimitSize = 0;
        # �����ϴ���ͼƬ�ߴ絥λ
        $allowKey = array_keys(self::$allowSizeType);
        if (in_array($limitSizeUnit, $allowKey)) {
            # ��ȡ��λ��Ϣ
            $sizeType = self::$allowSizeType[$limitSizeUnit];
            # ��������ͼƬ�ߴ�
            if (!empty($sizeType)) {
                $imgLimitSize = $limitSize * $sizeType;
            }
        }
        return $imgLimitSize;
    }

    # ��ȡ�����ϴ�ͼƬ����

    private static function getAllowType($imgType = array(),$isFile=false) {
        # �����ϴ���type
        $allowTypeArr = array();
        $limitType    = array();
        if($isFile){
            $limitType = self::$allowFileType;
        }else{
            $limitType = self::$allowImgType;
        }
        # ��ȡ�������͵�����
        $allowKey = array_keys($limitType);
        # ��ȡ���͵Ľ���
        $intersectArr = array_intersect($allowKey, $imgType);
        # ȡ����Ӧtype
        if (is_array($intersectArr) && !empty($intersectArr)) {
            foreach ($intersectArr as $intersectVal) {
                if (is_array($limitType[$intersectVal])) {
                    $allowTypeArr = array_merge($limitType[$intersectVal], $allowTypeArr);
                }
            }
        }
        return $allowTypeArr;
    }

    # ����ҵ������

    public static function setUploadModuleName($moduleName = '') {
        if ($moduleName) {
            self::$uploadModuleName = $moduleName;
        }
    }
    
    /**
     * ���Զ��ͼƬ������
     * code wangmc
     * date 2014
     */
    
    public  static  function   getLocationPic($paramArr){
        $options = array(
                'picUrl'          =>  '', #ͼƬ��ַ
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
