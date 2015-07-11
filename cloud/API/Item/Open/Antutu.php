<?php

/**
 * 获得 antutu 接口数据
 * code wangmc
 */

class  API_Item_Open_Antutu{

    protected  static $apiObj  =  null;                  #单例对象
    protected  static $Secret  = '8ckat51bbi6ul4hi';      #秘钥

    
    /**
     * 生成单例对象
     * code wangmc
     * 
     */
    public  static  function   getIntance(){
        if(self::$apiObj===null){
                $api = new ApiClient(self::$Secret);
                if($api){
                    self::$apiObj  = $api;
                    return  self::$apiObj;
                }else{
                    return false;
                }
        }else{
                return  self::$apiObj;
        }
                
    }
    
    /**
     * 获得厂商参数
     * code wangmc
     */
    public  static   function   getBrandInfo($paramArr){
        $options = array(
			'brand'               => '', #品牌
			'model'               => '', #模式
            'device'              => '', #
			'cpuId'               => '', #sina开放平台的ID
            'dataVersion'         => '', #用户ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$brand && !$model)return false;

        $obj  = self::getIntance(); 
        $data = array();
        $data = $obj->doSearch($brand,$model,$device,$cpuId,$dataVersion);
        
        return  $data;
        
    }
    
        /**
     * 获得厂商参数
     * code wangmc
     */
    public  static   function   getList($paramArr){
        $options = array(
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


        $obj  = self::getIntance(); 
        $data = array();
        $data = $obj->doList();
        
        return  $data;
        
    }
    
    
    
    
}


/**
 * API客户端PHP版本
 *
 * @copyright Copyright (c) 2005 - 2012 Antutu.com (www.Antutu.com)
 * @author 漠北 4620498
 * @package Cilent
 * @version 0.2
 */
/**
 * 2013-12-26  增加反馈功能
 */
class ApiClient {

    private $iv = '0000000000000000';
    private $lang = 'EN';
    private $_secret;
    //private $_api = 'http://api.antutu.net/i/api/sdk/threesearch';
    //private $_api = 'http://certinfo.pctvtv.com/i/api/sdk/threesearch';
    private $_api='http://our.antutu.net/api/?action=interface&act=threesearch';
    private $_listapi='http://our.antutu.net/api/?action=interface&act=threelist';
    private $_modellist_api = "http://api.antutu.net/i/api/sdk/model_list";
    private $_feedback_api='http://api.antutu.net/i/api/sdk/feedback';

    public function __construct($secret, $lang='EN') {
        $this->lang = strtoupper($lang);
        $this->_secret = $secret;
    }


    public function doSearch($brand='', $model='',$device='',$cpuid='',$dataversion=-1) {
        $xml = $this->_post(array('brand' => $brand, 'model' => $model,'device'=>$device,'str1'=>$cpuid,'dataversion'=>$dataversion),'score');
        if (strpos($xml, '<') !== 0) {
            $xml = $this->decrypt($xml);
            
        }
        $data = @$this->parseXml($xml);
        return $data;
    }
    
    
    public function doList($dataversion=-1) {
        $xml = $this->_post(array('dataversion'=>$dataversion),'list');
        $data = $this->xml_to_array($xml);
        $outData = array();
        if(!empty($data['result'])){
              foreach($data['result']  as  $value){
                  $outData['result'][]  = $value['@attributes'];
              }
              $outData['info']['count']      =  $data['@attributes']['count'];
              $outData['info']['lastModify']  =  $data['@attributes']['lastModify'];
        }else{
              return false;
        }
        
        
        return $outData;
    }
    
    public function doFeedback($brand,$model,$cpuid='',$content,$email=''){
        $xml=$this->_post(array('brand' => $brand, 'model' => $model,'cpuid'=>$cpuid,'content'=>$content,'email'=>$email));
        
        if (strpos($xml, '<') !== 0) {
            $xml = $this->decrypt($xml);
        }
        $data = @$this->parseXml($xml);
        return $data;
    }
    private function parseXML($xml) {
        $root = simplexml_load_string($xml);
        if ($root->getName() == "error") {
            $errinfo = $root->attributes();

            return array('code' => intval($errinfo['code']), 'message' => (string) $errinfo['message'][0]);
        }
        $children = $root->children();
        $data = array('code' => 200);
        if (is_object($children->result)) {
//foreach ()
            $attributes = $children->result->attributes();
            $data['result'] = array();
// 遍历节点上的所有属性
            foreach ($attributes as $attrname => $attrvalue) {
                $attrvalue = (string) $attrvalue;
                $data['result'][$attrname] = trim($attrvalue);
            }
        }
        if (is_object($children->hardinfo)) {
//foreach ()
            $attributes = $children->hardinfo->attributes();
            $data['hardinfo'] = array();
// 遍历节点上的所有属性
            foreach ($attributes as $attrname => $attrvalue) {
                $attrvalue = (string) $attrvalue;
                $data['hardinfo'][$attrname] = trim($attrvalue);
            }
        }
        return $data;
#$array = $this->createXMLArray($root, $parent_node);
    }

    private function _post($data, $type='score') {
        switch($type){
            case 'score':
                $url=parse_url($this->_api. (strpos($this->_api,'?')?'&':'?')."lang=" . $this->lang);
                break;
            case 'feedback':
                $url=  parse_url($this->_feedback_api. "?lang=" . $this->lang);
                break;
            case 'list':
                $url=  parse_url($this->_listapi);
                break;
            default:
                $url = parse_url($this->_modellist_api. "?lang=" . $this->lang);
                break;
                
        }
        if (!$url) {
            return "couldn't parse url";
        }
        if (!isset($url['port'])) {
            $url['port'] = "";
        }
        if (!isset($url['query'])) {
            $url['query'] = "";
        }
// Build POST string
        $encoded = "";
        $data['_secret'] = $this->_secret;
        $data['model'] = !empty($data['model']) ? str_replace(' ', '|', $data['model']):'';
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $encoded .= ( $encoded ? "&" : "");
                $encoded .= rawurlencode($k) . "=" . rawurlencode($v);
            }
        }
// Open socket on host
        $fp = @fsockopen($url['host'], $url['port'] ? $url['port'] : 80);
        if (!$fp) {
            return "failed to open socket to {$url['host']}";
        }
// Send HTTP 1.0 POST request to host
        $out = sprintf("POST %s%s%s HTTP/1.0\r\n", $url['path'], $url['query'] ? "?" : "", $url['query']);
        $out .= "Host: {$url['host']}\r\n";
        $out .="Content-type: application/x-www-form-urlencoded\r\n";
        $out .= "Content-length: " . strlen($encoded) . "\r\n";
        $out .="Connection: close\r\n\r\n";
        $out .= "$encoded\r\n";
//echo $out;
        fwrite($fp, $out);
// Read the first line of data, only accept if 200 OK is sent
        $line = fgets($fp, 1024);
        if (!preg_match('#^HTTP/1\\.. 200#', $line)) {
            return;
        }
// Put everything, except the headers to $results
        $results = "";
        $inheader = TRUE;
        while (!feof($fp)) {
            $line = fgets($fp, 1024);
            if ($inheader && ($line == "\r\n" || $line == "\r\n")) {
                $inheader = FALSE;
            } elseif (!$inheader) {
                $results .= $line;
            }
        }
        fclose($fp);
// Return with data received
        return $results;
    }

    private function __K() {
        if ($this->_secret == '')
            return str_repeat('0', 8);
        $out = '';
        for ($i = 0; $i < strlen($this->_secret); $i++) {
            $out^=ord($this->_secret[$i]);
        }
        return substr(str_pad($this->SetToHexString($this->_secret) . $out, 8, "0", STR_PAD_LEFT), -8);
    }

    /**
     * 字符串转换
     *
     * @param string $str
     * @return string
     */
    private function byte2str($str) {
        $result = unpack("H*", $str);
        return $result[1];
    }

    /**
     * 转换为字符串
     *
     * @param string $byte
     * @return string
     */
    private function str2byte($byte) {
        $len = strlen($byte);
        $ln = '';
        for ($i = 0; $i < $len; $i++, $i++) {
            $ln .= pack("c*", hexdec('0x' . $byte{$i} . $byte{$i + 1}));
        }
        return $ln;
    }

    public function encrypt($input) {
        $size = mcrypt_get_block_size('des', 'ecb');
        $input = $this->pkcs5_pad($input, $size);

        $key = $this->__K();
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        //var_dump(base64_encode($data));
        $data = $this->byte2str($data);
        return $data;
    }

    private function decrypt($encrypted) {
        $encrypted = $this->str2byte($encrypted);
        $key = $this->__K();
        $td = mcrypt_module_open('des', '', 'ecb', '');
        //使用MCRYPT_DES算法,cbc模式
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        //初始处理
        $decrypted = mdecrypt_generic($td, $encrypted);
        //解密
        mcrypt_generic_deinit($td);
        //结束
        mcrypt_module_close($td);
        $y = $this->pkcs5_unpad($decrypted);
        return $y;
    }

    private function pkcs5_pad($text, $blocksize) {

        $pad = $blocksize - (strlen($text) % $blocksize);

        return $text . str_repeat(chr($pad), $pad);
    }

    private function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text) - 1});

        if ($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;
        return substr($text, 0, -1 * $pad);
    }

    private function SingleDecToHex($dec) {
        $tmp = "";
        $dec = $dec % 16;
        if ($dec < 10)
            return $tmp . $dec;
        $arr = array("a", "b", "c", "d", "e", "f");
        return $tmp . $arr[$dec - 10];
    }

    private function SingleHexToDec($hex) {
        $v = Ord($hex);
        if (47 < $v && $v < 58)
            return $v - 48;
        if (96 < $v && $v < 103)
            return $v - 87;
    }

    private function SetToHexString($str) {
        if (!$str

            )return false;
        $tmp = "";
        for ($i = 0; $i < strlen($str); $i++) {
            $ord = Ord($str[$i]);
            $tmp.=$this->SingleDecToHex(($ord - $ord % 16) / 16);
            $tmp.=$this->SingleDecToHex($ord % 16);
        }
        return $tmp;
    }

    private function UnsetFromHexString($str) {
        if (!$str

            )return false;
        $tmp = "";
        for ($i = 0; $i < strlen($str); $i+=2) {
            $tmp.=chr(SingleHexToDec(substr($str, $i, 1)) * 16 + SingleHexToDec(substr($str, $i + 1, 1)));
        }
        return $tmp;
    }
    
    
    function xml_to_array($xml)                              
    {                                                        
          $array = (array)(simplexml_load_string($xml));         
          foreach ($array as $key=>$item){                       
            $array[$key]  =  $this->struct_to_array((array)$item);      
          }                                                      
          return $array;                                         
    }                                                        
    function struct_to_array($item) {                        
          if(!is_string($item)) {                                
            $item = (array)$item;                                
            foreach ($item as $key=>$val){                       
              $item[$key]  =  $this->struct_to_array($val);             
            }                                                    
          }                                                      
          return $item;                                          
    }                                                        


}




