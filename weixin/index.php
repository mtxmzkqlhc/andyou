<?php

defined('ZOL_API_ISFW') || define('ZOL_API_ISFW', false);//是否使用ZOL新框架，true为使用
$path = dirname(dirname(__FILE__)) . "/cloud/";
require_once($path . 'Api.php'); //引入私有云入口文件

#微信用的
define("TOKEN", "547381");
define("ZOLSTEPKEY", "1f7QGGgjuzZ7Ykc0u3Njy7F0jayVM4cl3HehZ29myPF");

$openId = ""; #与咱们聊天的网友是谁
$zolUid = ""; #与咱们聊天的网友是谁 ZOL USERID
$zolUName = ""; #与咱们聊天的网友是谁 ZOL USERID
$inMsgType = "text";//输入的消息类型

#require(dirname(__FILE__)."/apps/functions.php");

$welcomeMsg = "您好，欢迎关注安之秀！";

if(isset($_GET["echostr"])){//是否首次进行验证
     echo $_GET["echostr"]; // ZOL_Api::run("Open.Weixin.verification" , array('token'=>$TOKEN));
     exit;
}else{//接收信息
    $data = API_Item_Open_Weixin::receiveMsg(array("subscribeCallback"=>"welcomeNewMan","unsubscribe"=>"byebyeMan"));
    #var_dump($data);
    if($data){
        $openId  = $data["FromUserName"];
        $ourId   = $data["ToUserName"];
        switch ($data["MsgType"]){
        	case "image":#图片
        		$inMsgType = "image";
        		//var_dump($data);
       		    $content = $data["PicUrl"];
                processImages(array("content"=>$content));			     
        		break;
            case "text":#文本信息处理
       		    $content = trim($data["Content"]);
        		$inMsgType = "text";
                processText(array("content"=>$content));
                break;
            case "link":#URL处理
       		    $title = $data["Title"];
       		    $desc = $data["Description"];
       		    if($title) $title = mb_convert_encoding((string)$title, "GBK","UTF-8");
       		    if($desc) $desc = mb_convert_encoding((string)$desc, "GBK","UTF-8");
       		    $url   = $data["Url"];
        		$inMsgType = "link";
                processUrl(array("title"=>$title,'desc'=>$desc,'url'=>$url));
        		
                break;
        }
        
    }
    exit;
    
  
}
/**
 * 有人关注我们
 */
function welcomeNewMan(){
	global $welcomeMsg;
	#"您好，感谢您的关注！您可以点右上角查看历史消息，也可以如下获得内容：\n" .
	$answerStr = sendMessage( $welcomeMsg);
    echo $answerStr;
}
/**
 * 有人离开了我们
 */
function byebyeMan(){
	
}

/**
 * 菜单点击
 */
function menuCheck($outArr){
	global $openId;
	$openId = $outArr['UserOpenId'];
	processText(array('content'=>$outArr['MenuKey']));
}

/**
 * 文本处理函数
 */
function processText($paramArr,$openId = '') {
		global $welcomeMsg;
		$options = array(
			'content'    => '', #内容
		);
		
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		$answerStr = "";
		

		
        if($content){
        	$answerStr = sendMessage($content);
        }
        echo $answerStr;
}
function sendMessage($msg){
	return API_Item_Open_Weixin::answerText( array(
                    'content' => $msg
            ));
}
