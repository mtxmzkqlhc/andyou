<?php
/**
* QQ�ӿ�
 *
* @author zhongwt
* @copyright (c) 2013-09-29
*/
class API_Item_Open_QQ
{
    private static $_APPID       = "208936";
    private static $_APPKEY      = "99d207b90670adceaaa416b63528d92c";
    private static $_APPCALLBACK = "http://service.zol.com.cn/user/api/qq/libs/oauth/get_access_token.php"; 
    private static $_qqObj       = null;
    
    /**
     * ����û���Ϣ
     * ����ӿڣ�http://wiki.open.qq.com/wiki/website/get_user_info
     */    
    public static function getUserInfo($paramArr) {
		$options = array(
			'openId'         => '', #�û�ID
			'accessToken'    => '', #�û����ʵ�Access Token
			'debug'          => false,
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
         $obj  = new QC(self::$_APPID, $accessToken , $openId,$debug);
         $info = $obj->get_user_info();
         if($info){//�ַ�ת��
             array_walk_recursive($info, "api_json_convert_encoding_u2g");
         }
         return $info;
    }
    
    
    /**
     * ��ȡ��Ѷ΢����¼�û����û�����
     * ����ӿڣ�http://wiki.open.qq.com/wiki/website/get_info
     * 
     */    
    public static function getWeiboUserInfo($paramArr) {
		$options = array(
			'openId'         => '', #�û�ID
			'accessToken'    => '', #�û����ʵ�Access Token
			'debug'          => false,
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
         $obj  = new QC(self::$_APPID, $accessToken , $openId,$debug);
         $info = $obj->get_info();
         if($info){//�ַ�ת��
             array_walk_recursive($info, "api_json_convert_encoding_u2g");
         }
         return $info;
    }
        
    /**
     * ��ȡ��Ѷ΢�������û���ϸ��Ϣ
     * ����ӿڣ�http://wiki.open.qq.com/wiki/website/get_other_info
     * 
     */    
    public static function getWeiboOtherUserInfo($paramArr) {
		$options = array(
			'openId'         => '', #�û�ID
			'accessToken'    => '', #�û����ʵ�Access Token
			'name'           => '', #�����û����˻���
			'fopenid'        => '', #�����û���openid name��fopenid����ѡһ������ͬʱ��������nameֵΪ����
			'debug'          => false,
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
         $obj  = new QC(self::$_APPID, $accessToken , $openId,$debug);
         $info = $obj->get_other_info(array("name" => $name,"fopenid" => $fopenid));
         if($info){//�ַ�ת��
             array_walk_recursive($info, "api_json_convert_encoding_u2g");
         }
         return $info;
    }
    
    /**
     * ��ȡ��Ѷ΢����˿�б�
     * ����ӿڣ�http://wiki.open.qq.com/wiki/website/get_fanslist
     * 
     */    
    public static function getWeiboFansList($paramArr) {
		$options = array(
			'openId'         => '', #�û�ID
			'accessToken'    => '', #�û����ʵ�Access Token
			'num'            => 30, #��˿�� ȡֵ��ΧΪ1-30��
			'offset'         => 0, #
			'debug'          => false,
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
         $obj  = new QC(self::$_APPID, $accessToken , $openId,$debug);
         $info = $obj->get_fanslist(array("reqnum" => $num,"startindex" => $offset));
         if($info){//�ַ�ת��
             array_walk_recursive($info, "api_json_convert_encoding_u2g");
         }
         return $info;
    }
    
    /**
     * ��ȡ��Ѷ΢��ż���б�
     * ����ӿڣ�http://wiki.open.qq.com/wiki/website/get_idollist
     * 
     */    
    public static function getWeiboIdolList($paramArr) {
		$options = array(
			'openId'         => '', #�û�ID
			'accessToken'    => '', #�û����ʵ�Access Token
			'num'            => 30, #��˿�� ȡֵ��ΧΪ1-30��
			'offset'         => 0, #��˿�� ȡֵ��ΧΪ1-30��
			'debug'          => false,
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
         $obj  = new QC(self::$_APPID, $accessToken , $openId,$debug);
         $info = $obj->get_idollist(array("reqnum" => $num,"startindex" => $offset));
         if($info){//�ַ�ת��
             array_walk_recursive($info, "api_json_convert_encoding_u2g");
         }
         return $info;
    }

}




/*
 * @brief QC�࣬api�ⲿ���󣬵��ýӿ�ȫ�������ڴ˶���
 * */
class QC extends QCOauth{
    private $kesArr, $APIMap,$debug;

    /**
     * _construct
     *
     * ���췽��
     */
    public function __construct($appid,$accessToken = "", $openid = "",$debug=false){
        parent::__construct();
        $this->debug = $debug;

        //���ݲ����ıر��Ĳ���
        $this->keysArr = array(
            "oauth_consumer_key" => (int)$appid,
            "access_token"       => $accessToken,
            "openid"             => $openid
        );
        

        //��ʼ��APIMap
        /*
         * ��#��ʾ�Ǳ��룬���򲻴���url(url�в�����ָò���)�� "key" => "val" ��ʾkey���û�ж�����ʹ��Ĭ��ֵval
         * ���� array( baseUrl, argListArr, method)
         */
        $this->APIMap = array(        
            
            /*                       qzone                    */
            "add_blog" => array(
                "https://graph.qq.com/blog/add_one_blog",
                array("title", "format" => "json", "content" => null),
                "POST"
            ),
            "add_topic" => array(
                "https://graph.qq.com/shuoshuo/add_topic",
                array("richtype","richval","con","#lbs_nm","#lbs_x","#lbs_y","format" => "json", "#third_source"),
                "POST"
            ),
            "get_user_info" => array(
                "https://graph.qq.com/user/get_user_info",
                array("format" => "json"),
                "GET"
            ),
            "add_one_blog" => array(
                "https://graph.qq.com/blog/add_one_blog",
                array("title", "content", "format" => "json"),
                "GET"
            ),
            "add_album" => array(
                "https://graph.qq.com/photo/add_album",
                array("albumname", "#albumdesc", "#priv", "format" => "json"),
                "POST"
            ),
            "upload_pic" => array(
                "https://graph.qq.com/photo/upload_pic",
                array("picture", "#photodesc", "#title", "#albumid", "#mobile", "#x", "#y", "#needfeed", "#successnum", "#picnum", "format" => "json"),
                "POST"
            ),
            "list_album" => array(
                "https://graph.qq.com/photo/list_album",
                array("format" => "json")
            ),
            "add_share" => array(
                "https://graph.qq.com/share/add_share",
                array("title", "url", "#comment","#summary","#images","format" => "json","#type","#playurl","#nswb","site","fromurl"),
                "POST"
            ),
            "check_page_fans" => array(
                "https://graph.qq.com/user/check_page_fans",
                array("page_id" => "314416946","format" => "json")
            ),
            /*                    wblog                             */

            "add_t" => array(
                "https://graph.qq.com/t/add_t",
                array("format" => "json", "content","#clientip","#longitude","#compatibleflag"),
                "POST"
            ),
            "add_pic_t" => array(
                "https://graph.qq.com/t/add_pic_t",
                array("content", "pic", "format" => "json", "#clientip", "#longitude", "#latitude", "#syncflag", "#compatiblefalg"),
                "POST"
            ),
            "del_t" => array(
                "https://graph.qq.com/t/del_t",
                array("id", "format" => "json"),
                "POST"
            ),
            "get_repost_list" => array(
                "https://graph.qq.com/t/get_repost_list",
                array("flag", "rootid", "pageflag", "pagetime", "reqnum", "twitterid", "format" => "json")
            ),
            "get_info" => array(
                "https://graph.qq.com/user/get_info",
                array("format" => "json")
            ),
            "get_other_info" => array(
                "https://graph.qq.com/user/get_other_info",
                array("format" => "json", "#name", "fopenid")
            ),
            "get_fanslist" => array(
                "https://graph.qq.com/relation/get_fanslist",
                array("format" => "json", "reqnum", "startindex", "#mode", "#install", "#sex")
            ),
            "get_idollist" => array(
                "https://graph.qq.com/relation/get_idollist",
                array("format" => "json", "reqnum", "startindex", "#mode", "#install")
            ),
            "add_idol" => array(
                "https://graph.qq.com/relation/add_idol",
                array("format" => "json", "#name-1", "#fopenids-1"),
                "POST"
            ),
            "del_idol" => array(
                "https://graph.qq.com/relation/del_idol",
                array("format" => "json", "#name-1", "#fopenid-1"),
                "POST"
            ),
            /*                           pay                          */

            "get_tenpay_addr" => array(
                "https://graph.qq.com/cft_info/get_tenpay_addr",
                array("ver" => 1,"limit" => 5,"offset" => 0,"format" => "json")
            )
        );
    }

    //������Ӧapi
    private function _applyAPI($arr, $argsList, $baseUrl, $method){
        $pre = "#";
        $keysArr = $this->keysArr;

        $optionArgList = array();//һЩ����ѡ�������ѡһ������
        foreach($argsList as $key => $val){
            $tmpKey = $key;
            $tmpVal = $val;

            if(!is_string($key)){
                $tmpKey = $val;

                if(strpos($val,$pre) === 0){
                    $tmpVal = $pre;
                    $tmpKey = substr($tmpKey,1);
                    if(preg_match("/-(\d$)/", $tmpKey, $res)){
                        $tmpKey = str_replace($res[0], "", $tmpKey);
                        $optionArgList[$res[1]][] = $tmpKey;
                    }
                }else{
                    $tmpVal = null;
                }
            }

            //-----���û��������Ӧ�Ĳ���
            if(!isset($arr[$tmpKey]) || $arr[$tmpKey] === ""){

                if($tmpVal == $pre){//��ʹ��Ĭ�ϵ�ֵ
                    continue;
                }else if($tmpVal){
                    $arr[$tmpKey] = $tmpVal;
                }else{
                    if($v = $_FILES[$tmpKey]){

                        $filename = dirname($v['tmp_name'])."/".$v['name'];
                        move_uploaded_file($v['tmp_name'], $filename);
                        $arr[$tmpKey] = "@$filename";

                    }else{
                        $this->error->showError("api���ò�������","δ�������$tmpKey");
                    }
                }
            }

            $keysArr[$tmpKey] = $arr[$tmpKey];
        }
        //���ѡ���������һ������
        foreach($optionArgList as $val){
            $n = 0;
            foreach($val as $v){
                if(in_array($v, array_keys($keysArr))){
                    $n ++;
                }
            }

            if(! $n){
                $str = implode(",",$val);
                $this->error->showError("api���ò�������",$str."����һ��");
            }
        }

        if($method == "POST"){
            if($baseUrl == "https://graph.qq.com/blog/add_one_blog") $response = $this->urlUtils->post($baseUrl, $keysArr, 1);
            else $response = $this->urlUtils->post($baseUrl, $keysArr, 0);
        }else if($method == "GET"){
            $response = $this->urlUtils->get($baseUrl, $keysArr);
        }

        return $response;

    }

    /**
     * _call
     * ħ����������api����ת��
     * @param string $name    ���õķ�������
     * @param array $arg      �����б�����
     * @since 5.0
     * @return array          ���ӵ��ý������
     */
    public function __call($name,$arg){
        //���APIMap��������Ӧ��api
        if(empty($this->APIMap[$name])){
            $this->error->showError("api�������ƴ���","�����ڵ�API: <span style='color:red;'>$name</span>");
        }

        //��APIMap��ȡapi��Ӧ����
        $baseUrl = $this->APIMap[$name][0];
        $argsList = $this->APIMap[$name][1];
        $method = isset($this->APIMap[$name][2]) ? $this->APIMap[$name][2] : "GET";

        if(empty($arg)){
            $arg[0] = null;
        }

        //����get_tenpay_addr�����⴦��php json_decode��\xA312�����ַ�֧�ֲ���
        if($name != "get_tenpay_addr"){
            $response = json_decode($this->_applyAPI($arg[0], $argsList, $baseUrl, $method));
            $responseArr = $this->objToArr($response);
        }else{
            $responseArr = $this->simple_json_parser($this->_applyAPI($arg[0], $argsList, $baseUrl, $method));
        }


        //��鷵��ret�ж�api�Ƿ�ɹ�����
        if($responseArr['ret'] == 0){
            return $responseArr;
        }else{
            if($this->debug){
                $this->error->showError($response->ret, $response->msg);
            }else{
                return false;
            }
        }

    }

    //php ��������ת��
    private function objToArr($obj){
        if(!is_object($obj) && !is_array($obj)) {
            return $obj;
        }
        $arr = array();
        foreach($obj as $k => $v){
            $arr[$k] = $this->objToArr($v);
        }
        return $arr;
    }

   
    /**
     * get_access_token
     * ���access_token
     * @param void
     * @since 5.0
     * @return string ����access_token
     */
    public function get_access_token(){
        return $this->recorder->read("access_token");
    }

    //��ʵ��json��php����ת������
    private function simple_json_parser($json){
        $json = str_replace("{","",str_replace("}","", $json));
        $jsonValue = explode(",", $json);
        $arr = array();
        foreach($jsonValue as $v){
            $jValue = explode(":", $v);
            $arr[str_replace('"',"", $jValue[0])] = (str_replace('"', "", $jValue[1]));
        }
        return $arr;
    }
}


class QCOauth{

    const VERSION = "2.0";
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";

    protected $recorder;
    public $urlUtils;
    protected $error;
    

    function __construct(){
        $this->recorder = new Recorder();
        $this->urlUtils = new QCURL();
        $this->error = new ErrorCase();
    }

    public function qq_login(){
        $appid = $this->recorder->readInc("appid");
        $callback = $this->recorder->readInc("callback");
        $scope = $this->recorder->readInc("scope");

        //-------����Ψһ�������CSRF����
        $state = md5(uniqid(rand(), TRUE));
        $this->recorder->write('state',$state);

        //-------������������б�
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $appid,
            "redirect_uri" => $callback,
            "state" => $state,
            "scope" => $scope
        );

        $login_url =  $this->urlUtils->combineURL(self::GET_AUTH_CODE_URL, $keysArr);

        header("Location:$login_url");
    }

    public function qq_callback(){
        $state = $this->recorder->read("state");

        //--------��֤state��ֹCSRF����
        if($_GET['state'] != $state){
            $this->error->showError("30001");
        }

        //-------��������б�
        $keysArr = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->recorder->readInc("appid"),
            "redirect_uri" => urlencode($this->recorder->readInc("callback")),
            "client_secret" => $this->recorder->readInc("appkey"),
            "code" => $_GET['code']
        );

        //------��������access_token��url
        $token_url = $this->urlUtils->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response = $this->urlUtils->get_contents($token_url);

        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);

            if(isset($msg->error)){
                $this->error->showError($msg->error, $msg->error_description);
            }
        }

        $params = array();
        parse_str($response, $params);

        $this->recorder->write("access_token", $params["access_token"]);
        return $params["access_token"];

    }

    public function get_openid(){

        //-------��������б�
        $keysArr = array(
            "access_token" => $this->recorder->read("access_token")
        );

        $graph_url = $this->urlUtils->combineURL(self::GET_OPENID_URL, $keysArr);
        $response = $this->urlUtils->get_contents($graph_url);

        //--------�������Ƿ���
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }

        $user = json_decode($response);
        if(isset($user->error)){
            $this->error->showError($user->error, $user->error_description);
        }

        //------��¼openid
        $this->recorder->write("openid", $user->openid);
        return $user->openid;

    }
}

/*
 * @brief ErrorCase�࣬����쳣
 * */
class ErrorCase{
    private $errorMsg;

    public function __construct(){
        $this->errorMsg = array(
            "20001" => "<h2>�����ļ��𻵻��޷���ȡ��������ִ��intall</h2>",
            "30001" => "<h2>The state does not match. You may be a victim of CSRF.</h2>",
            "50001" => "<h2>�����Ƿ������޷�����httpsЭ��</h2>����δ����curl֧��,�볢�Կ���curl֧�֣�����web�����������������δ���������ϵ����"
            );
    }

    /**
     * showError
     * ��ʾ������Ϣ
     * @param int $code    �������
     * @param string $description ������Ϣ����ѡ��
     */
    public function showError($code, $description = '$'){
        //die();
        //return false;
        
        
        
      //  $recorder = new Recorder();
//        if(! $recorder->readInc("errorReport")){
//            die();//die quietly
//        }


        echo "<meta charset=\"UTF-8\">";
        if($description == "$"){
            die($this->errorMsg[$code]);
        }else{
            echo "<h3>error:</h3>$code";
            echo "<h3>msg  :</h3>$description";
            exit(); 
        }
    }
    public function showTips($code, $description = '$'){
    }
}


class Recorder{
    private static $data;
    private $inc;
    private $error;

    public function __construct(){
        /*
        $this->error = new ErrorCase();

        //-------��ȡ�����ļ�
        $incFileContents = file_get_contents(ROOT."comm/inc.php");
        $this->inc = json_decode($incFileContents);
        if(empty($this->inc)){
            $this->error->showError("20001");
        }

        if(empty($_SESSION['QC_userData'])){
            self::$data = array();
        }else{
            self::$data = $_SESSION['QC_userData'];
        }*/
    }

    public function write($name,$value){
        self::$data[$name] = $value;
    }

    public function read($name){
        if(empty(self::$data[$name])){
            return null;
        }else{
            return self::$data[$name];
        }
    }

    public function readInc($name){
        if(empty($this->inc->$name)){
            return null;
        }else{
            return $this->inc->$name;
        }
    }

    public function delete($name){
        unset(self::$data[$name]);
    }

    function __destruct(){
        $_SESSION['QC_userData'] = self::$data;
    }
}



/*
 * @brief url��װ�࣬�����õ�url���������װ��һ��
 * */
class QCURL{
    private $error;

    public function __construct(){
        $this->error = new ErrorCase();
    }

    /**
     * combineURL
     * ƴ��url
     * @param string $baseURL   ���ڵ�url
     * @param array  $keysArr   �����б�����
     * @return string           ����ƴ�ӵ�url
     */
    public function combineURL($baseURL,$keysArr){
        $combined = $baseURL."?";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode("&",$valueArr);
        $combined .= ($keyStr);
        
        return $combined;
    }

    /**
     * get_contents
     * ������ͨ��get����������
     * @param string $url       �����url,ƴ�Ӻ��
     * @return string           ���󷵻ص�����
     */
    public function get_contents($url){
        //if (ini_get("allow_url_fopen") == "1") {
        //    $response = file_get_contents($url);
        //}else{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response =  curl_exec($ch);
            curl_close($ch);
        //}

        //-------����Ϊ��
        if(empty($response)){
            $this->error->showError("50001");
        }

        return $response;
    }

    /**
     * get
     * get��ʽ������Դ
     * @param string $url     ���ڵ�baseUrl
     * @param array $keysArr  �����б�����      
     * @return string         ���ص���Դ����
     */
    public function get($url, $keysArr){
        $combined = $this->combineURL($url, $keysArr);
        return $this->get_contents($combined);
    }

    /**
     * post
     * post��ʽ������Դ
     * @param string $url       ���ڵ�baseUrl
     * @param array $keysArr    ����Ĳ����б�
     * @param int $flag         ��־λ
     * @return string           ���ص���Դ����
     */
    public function post($url, $keysArr, $flag = 0){

        $ch = curl_init();
        if(! $flag) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ch, CURLOPT_POST, TRUE); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr); 
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);

        curl_close($ch);
        return $ret;
    }
}
