<?php
/**
* 微信接口
* 文档：http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97
* @author zhongwt
* @copyright (c) 2013-08-07
*/

class API_Item_Open_Weixin
{
    private static $TOKEN      = "ZOL_CLOUD_WEIXIN_TOKEN";
    private static $ourWxId    = "";#我们的微信ID
    private static $userOpenId = "";#用户的OPENID
    private static $EventKey   = "";#用户的OPENID
    /**
     * 签名的验证
     */
	private static function checkSignature($paramArr) {
		$options = array(
			'signature'    => '', #微信加密签名
			'timestamp'    => '', #时间戳
			'nonce'        => '', #随机数
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
		$token = self::$TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

    /**
     * 申请消息接口的验证
     * 网址接入 公众平台用户提交信息后，微信服务器将发送GET请求到 这个参数
     */
	public static function verification($paramArr) {
		$options = array(
			'token'        => '', #TOKEN
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options); 
        self::$TOKEN = $token;
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];
        $echoStr   = $_GET["echostr"];
        //进行验证
        if(self::checkSignature(array(
			'signature'    => $signature,
			'timestamp'    => $timestamp,
			'nonce'        => $nonce,
		))){
            echo $echoStr;
        }
    }


    /**
     * 接受用户发送的消息
     */
    public static function receiveMsg($paramArr) {
		$options = array(
			'subscribeCallback'        => false, #订阅回调函数
			'unsubscribeCallback'      => false, #取消订阅回调函数
			'clickCallback'            => false, #自定义菜单点击事件调函数
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        
        if(!$postStr)return array();
        
        $outArr = array();
        $postObj           = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

        self::$userOpenId  = $postObj->FromUserName ? (string)$postObj->FromUserName : "";
        self::$ourWxId     = $postObj->ToUserName ? (string)$postObj->ToUserName : "";
        $msgType           = (string)$postObj->MsgType;
        
        $outArr = (array)$postObj;
        $outArr["OurWeixinId"] = (string)self::$ourWxId;
        $outArr["UserOpenId"]  = (string)self::$userOpenId;        
        if('event' == $msgType){#事件的处理            
            switch((string)$postObj->Event){
                case "subscribe": #订阅事件
                    if($subscribeCallback){
                        call_user_func($subscribeCallback, $outArr);
                    }
                    break;
                case "unsubscribe": #取消订阅
                    if($unsubscribeCallback){
                        call_user_func($unsubscribeCallback, $outArr);
                    }
                    break;
                case "CLICK": #自定义菜单点击事件
                    self::$EventKey    = $postObj->EventKey ? (string)$postObj->EventKey : "";
                    $outArr["MenuKey"]  = (string)self::$EventKey;
                    if($clickCallback){
                        call_user_func($clickCallback, $outArr);
                    }
                    break;

            }
        }else{#普通消息
            if("text"== $msgType){                
                $outArr["Content"]     = mb_convert_encoding((string)$postObj->Content, "GBK","UTF-8");
            }elseif("image"== $msgType){
                $outArr["PicUrl"]     = (string)$postObj->PicUrl;
            }
            return $outArr;
        }

        
    }

    /**
     * 获得回复消息的通用部分
     */
    private static function getAnswerBaseXml($type){
        return "<xml>
                <ToUserName><![CDATA[". self::$userOpenId ."]]></ToUserName>
                <FromUserName><![CDATA[". self::$ourWxId ."]]></FromUserName>
                <CreateTime>".SYSTEM_TIME."</CreateTime>
                ";
    }

    /**
     * 回复文本消息
     */
    public static function answerText($paramArr) {
		$options = array(
			'content'        => '', #内容
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$content)return '';
        
        $xmlStr  = self::getAnswerBaseXml();
        $content = mb_convert_encoding(trim($content), "UTF-8","GBK");
        
        return $xmlStr . "<MsgType><![CDATA[text]]></MsgType>
               <Content><![CDATA[{$content}]]></Content>
               </xml>";        
    }

     /**
     * 回复音乐消息
     */
    public static function answerMusic($paramArr) {
		$options = array(
			'title'           => '', #标题
			'desc'            => '', #描述
			'musicUrl'        => '', #音乐链接
			'hQMusicUrl'      => '', #高质量音乐链接，WIFI环境优先使用该链接播放音乐
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


        $xmlStr  = self::getAnswerBaseXml();
		$title = mb_convert_encoding(trim($title), "UTF-8","GBK");
		if($desc){
			$desc = mb_convert_encoding(trim($desc), "UTF-8","GBK");
		}
        return $xmlStr . "<MsgType><![CDATA[music]]></MsgType>
                         <Music>
                         <Title><![CDATA[{$title}]]></Title>
                         <Description><![CDATA[{$desc}]]></Description>
                         <MusicUrl><![CDATA[{$musicUrl}]]></MusicUrl>
                         <HQMusicUrl><![CDATA[{$hQMusicUrl}]]></HQMusicUrl>
                         </Music>
                         </xml>";
    }

    /**
     * 回复列表
     */
    public static function answerList($paramArr) {
		$options = array(
			'dataArr'        => array(), #列表内容 array('Title'=>''，'Description'=>''，'PicUrl'=>''，'Url'=>'');
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$dataArr)return '';

        $xmlStr  = self::getAnswerBaseXml();
        $xmlStr .= "<MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>".count($dataArr)."</ArticleCount>
                    <Articles>";
        foreach($dataArr as $d){
        	
			$title = mb_convert_encoding(trim($d['Title']), "UTF-8","GBK");
			if(isset($d['Description']) && $d['Description']){
				$desc = mb_convert_encoding(trim($d['Description']), "UTF-8","GBK");
			}
            $textTpl = "
                         <item>
                         <Title><![CDATA[%s]]></Title>
                         <Description><![CDATA[%s]]></Description>
                         <PicUrl><![CDATA[%s]]></PicUrl>
                         <Url><![CDATA[%s]]></Url>
                         </item>";

            $xmlStr .= sprintf($textTpl,$title, $desc, $d['PicUrl'], $d['Url']);
        }
        $xmlStr .= "
                    </Articles>
                    </xml>";
        
        return $xmlStr;
    }
    /**
     * 根据OPENID获得ZOL的用户ID
     */
    public static function getZolUserByOpenId($paramArr) {
		$options = array( 
			'appId'         => '', #公众账号 ID 如 zol,jishuren
			'openId'        => '', #OPEN ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $db = API_Db_User::instance();
        return $db->getOne("select zoluserid from z_weixin_user_map where appid = '{$appId}' and openid = '{$openId}'");
    }
    
    
    /**
     * 设置zol用户id和微信OPENID的映射
     */
    public static function setZolUserOpenIdMap($paramArr) {
		$options = array( 
			'appId'         => '', #公众账号 ID 如 zol,jishuren
			'openId'        => '', #OPEN ID
			'userId'        => '', #ZOL用户ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $db = API_Db_User::instance();
        $db->query("insert into z_weixin_user_map(appid,openid,zoluserid,tm) 
                    values( '{$appId}','{$openId}','{$userId}',".SYSTEM_TIME.")");
        return true;
    }
    
    /**
     * 修改zol用户id和微信OPENID的映射
     */
    public static function updateZolUserOpenIdMap($paramArr) {
		$options = array( 
			'appId'         => '', #公众账号 ID 如 zol,jishuren
			'openId'        => '', #OPEN ID
			'userId'        => '', #ZOL用户ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $db = API_Db_User::instance();
        $id = $db->getOne("select id from z_weixin_user_map where appid = '{$appId}' and openid = '{$openId}'");
        if(!$id) {
            return false;
        }
        $updateSql = "UPDATE z_weixin_user_map SET zoluserid='{$userId}',tm=".SYSTEM_TIME." WHERE id={$id} AND appid='{$appId}' AND openid='{$openId}' LIMIT 1";
        $db->query($updateSql);
        return true;
    }
    
    /**
     * 修改zol用户id和微信OPENID的映射
     */
    public static function deleteZolUserOpenIdMap($paramArr) {
		$options = array( 
			'appId'         => '', #公众账号 ID 如 zol,jishuren
			'openId'        => '', #OPEN ID
			'userId'        => '', #ZOL用户ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $db = API_Db_User::instance();
        $deleteSql = "DELETE FROM z_weixin_user_map WHERE zoluserid='{$userId}' AND appid='{$appId}' AND openid='{$openId}' LIMIT 1";
        $db->query($deleteSql);
        return true;
    }
    
    /**
     * 获得 access_token  
     * 
     */
    
    public  static    function  getAccessToken($paramArr){        
        $options = array(
            'appId'        => '',    #服务号的 appId
            'appSecret'    => '',    #服务号的 appSecret
            'exp'          => 0
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($appId)  || empty($appSecret)){
            return  false;
        }
        $kvKey = "WeixinAccessToken_".$appId;
        
        //首先尝试在KV中过得数据
        $token = API_Item_Kv_Db::get(array('key'=> $kvKey ));
        #var_dump($token);
        if($token)return $token;
        
        //请求接口获得
        $jsonStr   =   ZOL_Http::curlPage(array(
            'url'   =>"https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appId}&secret={$appSecret}"
        ));
        $jsonArr        = array();
        if($jsonStr){
             $jsonArr  = json_decode($jsonStr,true);
        }
        
        if(!empty($jsonArr['access_token'])){
               $exp   = $exp ? $exp : $jsonArr['expires_in']-100;
               $token = $jsonArr['access_token'];
               API_Item_Kv_Db::set(array('key'=>$kvKey,'val'=>$token,'life'=>$exp));
               return $token;
        }else{
               return false;
        }  
    }
    
    /**
     * 发送客服消息
     */
    
    public  static  function  sendMsg($paramArr){
        $options = array(
            'appId'        => '',    #服务号的 appId
            'appSecret'    => '',    #服务号的 appSecret
            'data'         => ''
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(empty($data)){
            return false;
        }
        #获得access_token
        $dataArr = self::getAccessToken(array(
                'appId'        => $appId,    #服务号的 appId
                'appSecret'    => $appSecret,    #服务号的 appSecret
        ));
        #获得 access_token
        $accessToken  = "";
        if(!empty($dataArr['state'])){
            $accessToken  =  $dataArr['data'];
        }else{
            return false;
        }
        $jsonStr   =   "";
        $jsonStr   =   API_Http::curlPost(array(
                            'url'       =>'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$accessToken,
                            'postdata'  => !empty($data) ? api_json_encode($data):''
                       ));
        
        $jsonArr  = json_decode($jsonStr,true);
        if(empty($jsonArr['errcode'])){
                return array('state'=>1,'data'=>$jsonArr['errmsg']);
        }else{
                return array('state'=>0,'data'=>$jsonArr['errmsg']);
        }

        
    }
    
    /**
     * 获得用户基本信息
     * 
     */
     public  static  function getUserInfo($paramArr){
        $options = array(
            'appId'        => '',    #服务号的 appId
            'appSecret'    => '',    #服务号的 appSecret
            'openId'       => ''
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($openId)){
            return false;
        }
        #获得access_token
        $dataArr = self::getAccessToken(array(
                'appId'        => $appId,    #服务号的 appId
                'appSecret'    => $appSecret,    #服务号的 appSecret
        ));
        #获得 access_token
        $accessToken  = "";
        if(!empty($dataArr['state'])){
            $accessToken  =  $dataArr['data'];
        }else{
            return false;
        }
         
        $jsonStr   =   "";
        $jsonStr   =   API_Http::curlPost(array(
                            'url'       =>'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$accessToken.'&openid='.$openId.'&lang=zh_CN'
                       ));
        $jsonArr   = array();     
        $jsonArr   = api_json_decode($jsonStr);   
        return  !empty ($jsonArr) ? $jsonArr : false;
     }
     
     /**
      * 获得用户组
      * 
      */
      public  static  function  getUserGroup($paramArr){
        $options = array(
            'appId'        => '',    #服务号的 appId
            'appSecret'    => '',    #服务号的 appSecret
            'nextOpenId'   => ''
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($appId) || empty($appSecret)){
            return false;
        }
        #获得access_token
        $dataArr = self::getAccessToken(array(
                'appId'        => $appId,        #服务号的 appId
                'appSecret'    => $appSecret,    #服务号的 appSecret
        ));
        #获得 access_token
        $accessToken  = "";
        if(!empty($dataArr['state'])){
            $accessToken  =  $dataArr['data'];
        }else{
            return false;
        }                
        $jsonStr   =   "";
        $jsonStr   =   API_Http::curlPost(array(
                            'url'       =>'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$accessToken.'&next_openid='.$nextOpenId
                       ));
        $jsonArr   = array();     
        $jsonArr   = api_json_decode($jsonStr);   
        return  !empty ($jsonArr) ? $jsonArr : false;
      }   
      
      /**
       * 创建自定义菜单
       * 
       */
      public  static   function   createManu($paramArr){
        $options = array(
            'appId'        => '',    #服务号的 appId
            'appSecret'    => '',    #服务号的 appSecret
            'data'         => ''
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($data) || empty($appId)  || empty($appSecret)){
            return false;
        }
        #获得access_token
        $accessToken = self::getAccessToken(array(
                'appId'        => $appId,    #服务号的 appId
                'appSecret'    => $appSecret,    #服务号的 appSecret
        ));
        #获得 access_token
        if(!$accessToken){            
            return array('state'=>0,'data'=>'access token error!!');
        }
        
        $jsonStr   =   API_Http::curlPost(array(
                            'url'       =>'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$accessToken,
                            'postdata'  => !empty($data) ? ($data):''
                       ));
        var_dump($jsonStr);
        $jsonArr  = json_decode($jsonStr,true);
        if(empty($jsonArr['errcode'])){
                return array('state'=>1,'data'=>$jsonArr['errmsg']);
        }else{
                return array('state'=>0,'data'=>$jsonArr['errmsg']);
        }   
      }
      
      /**
       * 获得自定义菜单
       */
      public   static  function  getManu($paramArr){
        $options = array(
            'appId'        => '',    #服务号的 appId
            'appSecret'    => '',    #服务号的 appSecret
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($appId)){
            return false;
        }
        #获得access_token
        $dataArr = self::getAccessToken(array(
                'appId'        => $appId,    #服务号的 appId
                'appSecret'    => $appSecret,    #服务号的 appSecret
        ));
        #获得 access_token
        $accessToken  = "";
        if(!empty($dataArr['state'])){
            $accessToken  =  $dataArr['data'];
        }else{
            return false;
        }
         
        $jsonStr   =   "";
        $jsonStr   =   API_Http::curlPost(array(
                            'url'       =>'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$accessToken
                       ));
        $jsonArr   = array();     
        $jsonArr   = api_json_decode($jsonStr);   
        return  !empty ($jsonArr) ? $jsonArr : false; 
      }
      
      /**
       * 生成二维码
       */
      
      public  static  function  createQR($paramArr){
        $options = array(
            'appId'        => '',    #服务号的 appId
            'appSecret'    => '',    #服务号的 appSecret
            'isForever'    =>1,      #是否是永久的
            'sceneId'      =>''      #是否
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($appId) || empty($appSecret) || empty ($sceneId)){
            return false;
        }
        #获得access_token
        $dataArr = self::getAccessToken(array(
                'appId'        => $appId,    #服务号的 appId
                'appSecret'    => $appSecret,    #服务号的 appSecret
        ));
        #获得 access_token
        $accessToken  = "";
        if(!empty($dataArr['state'])){
            $accessToken  =  $dataArr['data'];
        }else{
            return false;
        }
        $data      =   array();
        
        $data['action_name']    =  !empty($isForever) ? 'QR_LIMIT_SCENE':'QR_LIMIT_SCENE';
        $data['action_info']    =  array('scene'=>array('scene_id'=>$sceneId)); 
        
        $jsonStr   =   "";
        $jsonStr   =   API_Http::curlPost(array(
                            'url'       =>'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$accessToken,
                            'postdata'  => !empty($data) ? api_json_encode($data):''
                       ));
        $jsonArr   = array();     
        $jsonArr   = api_json_decode($jsonStr);   
        return  !empty ($jsonArr) ? $jsonArr : false; 
          
          
      }
      
      /**
       * 发送模板信息接口
       * @param type $paramArr
       * @return type 
       */
      public static function sendTemplateMessage($paramArr){
          $options = array(
            'appId'             => '', #服务号的appId
            'appSecret'         => '', #服务号的appSecret
            'dataArr'           => '', #根据模板定义的参数发送的信息，具体的需要参考微信模板消息参数说明，说明在微信公众号后台
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($appId) || empty($appSecret)  || empty($dataArr)){
            return false;
        }
        
        #获得access_token
        $tokenDataArr = self::getAccessToken(array(
                'appId'        => $appId,       #服务号的 appId
                'appSecret'    => $appSecret,   #服务号的 appSecret
        ));
        #获得 access_token
        $accessToken  = "";
        if(!empty($tokenDataArr['state'])){
            $accessToken  =  $tokenDataArr['data'];
        }else{
            return false;
        }
        
        
        $jsonStr   =   "";
        $jsonStr   =   API_Http::curlPost(array(
                            'url'       =>'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken,
                            'postdata'  => !empty($dataArr) ? api_json_encode($dataArr):''
                       ));
        $jsonArr   = array();     
        $jsonArr   = api_json_decode($jsonStr); 
        
        if(!empty($jsonArr['errmsg'])) {
            if($jsonArr['errmsg']=='ok' && $jsonArr['errcode']=='0') {
                return true;
            }
        }else {
            return false;
        }
      }
      
    
}