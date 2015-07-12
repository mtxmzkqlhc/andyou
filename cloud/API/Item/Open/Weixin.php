<?php
/**
* ΢�Žӿ�
* �ĵ���http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97
* @author zhongwt
* @copyright (c) 2013-08-07
*/

class API_Item_Open_Weixin
{
    private static $TOKEN      = "ZOL_CLOUD_WEIXIN_TOKEN";
    private static $ourWxId    = "";#���ǵ�΢��ID
    private static $userOpenId = "";#�û���OPENID
    private static $EventKey   = "";#�û���OPENID
    /**
     * ǩ������֤
     */
	private static function checkSignature($paramArr) {
		$options = array(
			'signature'    => '', #΢�ż���ǩ��
			'timestamp'    => '', #ʱ���
			'nonce'        => '', #�����
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
     * ������Ϣ�ӿڵ���֤
     * ��ַ���� ����ƽ̨�û��ύ��Ϣ��΢�ŷ�����������GET���� �������
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
        //������֤
        if(self::checkSignature(array(
			'signature'    => $signature,
			'timestamp'    => $timestamp,
			'nonce'        => $nonce,
		))){
            echo $echoStr;
        }
    }


    /**
     * �����û����͵���Ϣ
     */
    public static function receiveMsg($paramArr) {
		$options = array(
			'subscribeCallback'        => false, #���Ļص�����
			'unsubscribeCallback'      => false, #ȡ�����Ļص�����
			'clickCallback'            => false, #�Զ���˵�����¼�������
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
        if('event' == $msgType){#�¼��Ĵ���            
            switch((string)$postObj->Event){
                case "subscribe": #�����¼�
                    if($subscribeCallback){
                        call_user_func($subscribeCallback, $outArr);
                    }
                    break;
                case "unsubscribe": #ȡ������
                    if($unsubscribeCallback){
                        call_user_func($unsubscribeCallback, $outArr);
                    }
                    break;
                case "CLICK": #�Զ���˵�����¼�
                    self::$EventKey    = $postObj->EventKey ? (string)$postObj->EventKey : "";
                    $outArr["MenuKey"]  = (string)self::$EventKey;
                    if($clickCallback){
                        call_user_func($clickCallback, $outArr);
                    }
                    break;

            }
        }else{#��ͨ��Ϣ
            if("text"== $msgType){                
                $outArr["Content"]     = mb_convert_encoding((string)$postObj->Content, "GBK","UTF-8");
            }elseif("image"== $msgType){
                $outArr["PicUrl"]     = (string)$postObj->PicUrl;
            }
            return $outArr;
        }

        
    }

    /**
     * ��ûظ���Ϣ��ͨ�ò���
     */
    private static function getAnswerBaseXml($type){
        return "<xml>
                <ToUserName><![CDATA[". self::$userOpenId ."]]></ToUserName>
                <FromUserName><![CDATA[". self::$ourWxId ."]]></FromUserName>
                <CreateTime>".SYSTEM_TIME."</CreateTime>
                ";
    }

    /**
     * �ظ��ı���Ϣ
     */
    public static function answerText($paramArr) {
		$options = array(
			'content'        => '', #����
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
     * �ظ�������Ϣ
     */
    public static function answerMusic($paramArr) {
		$options = array(
			'title'           => '', #����
			'desc'            => '', #����
			'musicUrl'        => '', #��������
			'hQMusicUrl'      => '', #�������������ӣ�WIFI��������ʹ�ø����Ӳ�������
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
     * �ظ��б�
     */
    public static function answerList($paramArr) {
		$options = array(
			'dataArr'        => array(), #�б����� array('Title'=>''��'Description'=>''��'PicUrl'=>''��'Url'=>'');
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
     * ����OPENID���ZOL���û�ID
     */
    public static function getZolUserByOpenId($paramArr) {
		$options = array( 
			'appId'         => '', #�����˺� ID �� zol,jishuren
			'openId'        => '', #OPEN ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $db = API_Db_User::instance();
        return $db->getOne("select zoluserid from z_weixin_user_map where appid = '{$appId}' and openid = '{$openId}'");
    }
    
    
    /**
     * ����zol�û�id��΢��OPENID��ӳ��
     */
    public static function setZolUserOpenIdMap($paramArr) {
		$options = array( 
			'appId'         => '', #�����˺� ID �� zol,jishuren
			'openId'        => '', #OPEN ID
			'userId'        => '', #ZOL�û�ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $db = API_Db_User::instance();
        $db->query("insert into z_weixin_user_map(appid,openid,zoluserid,tm) 
                    values( '{$appId}','{$openId}','{$userId}',".SYSTEM_TIME.")");
        return true;
    }
    
    /**
     * �޸�zol�û�id��΢��OPENID��ӳ��
     */
    public static function updateZolUserOpenIdMap($paramArr) {
		$options = array( 
			'appId'         => '', #�����˺� ID �� zol,jishuren
			'openId'        => '', #OPEN ID
			'userId'        => '', #ZOL�û�ID
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
     * �޸�zol�û�id��΢��OPENID��ӳ��
     */
    public static function deleteZolUserOpenIdMap($paramArr) {
		$options = array( 
			'appId'         => '', #�����˺� ID �� zol,jishuren
			'openId'        => '', #OPEN ID
			'userId'        => '', #ZOL�û�ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $db = API_Db_User::instance();
        $deleteSql = "DELETE FROM z_weixin_user_map WHERE zoluserid='{$userId}' AND appid='{$appId}' AND openid='{$openId}' LIMIT 1";
        $db->query($deleteSql);
        return true;
    }
    
    /**
     * ��� access_token  
     * 
     */
    
    public  static    function  getAccessToken($paramArr){        
        $options = array(
            'appId'        => '',    #����ŵ� appId
            'appSecret'    => '',    #����ŵ� appSecret
            'exp'          => 0
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($appId)  || empty($appSecret)){
            return  false;
        }
        $kvKey = "WeixinAccessToken_".$appId;
        
        //���ȳ�����KV�й�������
        $token = API_Item_Kv_Db::get(array('key'=> $kvKey ));
        #var_dump($token);
        if($token)return $token;
        
        //����ӿڻ��
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
     * ���Ϳͷ���Ϣ
     */
    
    public  static  function  sendMsg($paramArr){
        $options = array(
            'appId'        => '',    #����ŵ� appId
            'appSecret'    => '',    #����ŵ� appSecret
            'data'         => ''
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(empty($data)){
            return false;
        }
        #���access_token
        $dataArr = self::getAccessToken(array(
                'appId'        => $appId,    #����ŵ� appId
                'appSecret'    => $appSecret,    #����ŵ� appSecret
        ));
        #��� access_token
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
     * ����û�������Ϣ
     * 
     */
     public  static  function getUserInfo($paramArr){
        $options = array(
            'appId'        => '',    #����ŵ� appId
            'appSecret'    => '',    #����ŵ� appSecret
            'openId'       => ''
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($openId)){
            return false;
        }
        #���access_token
        $dataArr = self::getAccessToken(array(
                'appId'        => $appId,    #����ŵ� appId
                'appSecret'    => $appSecret,    #����ŵ� appSecret
        ));
        #��� access_token
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
      * ����û���
      * 
      */
      public  static  function  getUserGroup($paramArr){
        $options = array(
            'appId'        => '',    #����ŵ� appId
            'appSecret'    => '',    #����ŵ� appSecret
            'nextOpenId'   => ''
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($appId) || empty($appSecret)){
            return false;
        }
        #���access_token
        $dataArr = self::getAccessToken(array(
                'appId'        => $appId,        #����ŵ� appId
                'appSecret'    => $appSecret,    #����ŵ� appSecret
        ));
        #��� access_token
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
       * �����Զ���˵�
       * 
       */
      public  static   function   createManu($paramArr){
        $options = array(
            'appId'        => '',    #����ŵ� appId
            'appSecret'    => '',    #����ŵ� appSecret
            'data'         => ''
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($data) || empty($appId)  || empty($appSecret)){
            return false;
        }
        #���access_token
        $accessToken = self::getAccessToken(array(
                'appId'        => $appId,    #����ŵ� appId
                'appSecret'    => $appSecret,    #����ŵ� appSecret
        ));
        #��� access_token
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
       * ����Զ���˵�
       */
      public   static  function  getManu($paramArr){
        $options = array(
            'appId'        => '',    #����ŵ� appId
            'appSecret'    => '',    #����ŵ� appSecret
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($appId)){
            return false;
        }
        #���access_token
        $dataArr = self::getAccessToken(array(
                'appId'        => $appId,    #����ŵ� appId
                'appSecret'    => $appSecret,    #����ŵ� appSecret
        ));
        #��� access_token
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
       * ���ɶ�ά��
       */
      
      public  static  function  createQR($paramArr){
        $options = array(
            'appId'        => '',    #����ŵ� appId
            'appSecret'    => '',    #����ŵ� appSecret
            'isForever'    =>1,      #�Ƿ������õ�
            'sceneId'      =>''      #�Ƿ�
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($appId) || empty($appSecret) || empty ($sceneId)){
            return false;
        }
        #���access_token
        $dataArr = self::getAccessToken(array(
                'appId'        => $appId,    #����ŵ� appId
                'appSecret'    => $appSecret,    #����ŵ� appSecret
        ));
        #��� access_token
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
       * ����ģ����Ϣ�ӿ�
       * @param type $paramArr
       * @return type 
       */
      public static function sendTemplateMessage($paramArr){
          $options = array(
            'appId'             => '', #����ŵ�appId
            'appSecret'         => '', #����ŵ�appSecret
            'dataArr'           => '', #����ģ�嶨��Ĳ������͵���Ϣ���������Ҫ�ο�΢��ģ����Ϣ����˵����˵����΢�Ź��ںź�̨
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($appId) || empty($appSecret)  || empty($dataArr)){
            return false;
        }
        
        #���access_token
        $tokenDataArr = self::getAccessToken(array(
                'appId'        => $appId,       #����ŵ� appId
                'appSecret'    => $appSecret,   #����ŵ� appSecret
        ));
        #��� access_token
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