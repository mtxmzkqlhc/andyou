<?php


$path = dirname(__FILE__);
require($path . 'config.php'); //����˽��������ļ�

$openId = ""; #�����������������˭
$zolUid = ""; #�����������������˭ ZOL USERID
$zolUName = ""; #�����������������˭ ZOL USERID
$inMsgType = "text";//�������Ϣ����

#require(dirname(__FILE__)."/apps/functions.php");

$welcomeMsg = "���ã���ӭ��ע��֮�㣡";

if(isset($_GET["echostr"])){//�Ƿ��״ν�����֤
     echo $_GET["echostr"]; // ZOL_Api::run("Open.Weixin.verification" , array('token'=>$TOKEN));
     exit;
}else{//������Ϣ
    $data = API_Item_Open_Weixin::receiveMsg(array("subscribeCallback"=>"welcomeNewMan","unsubscribe"=>"byebyeMan"));
    #var_dump($data);
    if($data){
        $openId  = $data["FromUserName"];
        $ourId   = $data["ToUserName"];
        switch ($data["MsgType"]){
        	case "image":#ͼƬ
        		$inMsgType = "image";
        		//var_dump($data);
       		    $content = $data["PicUrl"];
                processImages(array("content"=>$content));			     
        		break;
            case "text":#�ı���Ϣ����
       		    $content = trim($data["Content"]);
        		$inMsgType = "text";
                processText(array("content"=>$content));
                break;
            case "link":#URL����
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
 * ���˹�ע����
 */
function welcomeNewMan(){
	global $welcomeMsg;
	#"���ã���л���Ĺ�ע�������Ե����Ͻǲ鿴��ʷ��Ϣ��Ҳ�������»�����ݣ�\n" .
	$answerStr = sendMessage( $welcomeMsg);
    echo $answerStr;
}
/**
 * �����뿪������
 */
function byebyeMan(){
	
}

/**
 * �˵����
 */
function menuCheck($outArr){
	global $openId;
	$openId = $outArr['UserOpenId'];
	processText(array('content'=>$outArr['MenuKey']));
}

/**
 * �ı�������
 */
function processText($paramArr,$openId = '') {
		global $welcomeMsg;
		$options = array(
			'content'    => '', #����
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
