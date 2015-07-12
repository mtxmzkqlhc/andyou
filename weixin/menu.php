<?php

$path = dirname(__FILE__);
require($path . '/config.php'); //����˽��������ļ�

$str = '{
     "button":[
     {	
          "type":"click",
          "name":"���ո���",
          "key":"V1001_TODAY_MUSIC"
      },
      {
           "name":"�˵�",
           "sub_button":[
           {	
               "type":"view",
               "name":"����",
               "url":"http://www.soso.com/"
            },
            {
               "type":"view",
               "name":"��Ƶ",
               "url":"http://v.qq.com/"
            },
            {
               "type":"click",
               "name":"��һ������",
               "key":"V1001_GOOD"
            }]
       }]
 }';


//���һ��AccessToken
$accessToken = API_Item_Open_Weixin::getAccessToken( array(
                                            'appId'        => AppID,    #����ŵ� appId
                                            'appSecret'    => AppSecret,    #����ŵ� appSecret
                                        ));
$menuData = array(
    'button' => array(
            array(
                'name'=>'��Ա',
                'sub_button' => array(
                   array(
                       'type' => 'click',
                       'name' => 'AA1',
                       'key'  => 'BTN_MEMBER_VIEWSCORE',
                   ),
                   array(
                       'type' => 'view',
                       'name' => 'AA2',
                       'url'  => 'http://m.zol.com.cn/',
                   ),
                   array(
                       'type' => 'scancode_push',
                       'name' => 'AA3',
                        "key" => "rselfmenu_0_1", 
                        "sub_button" => array(),
                   ),
                ),
            ),
            array(
                'name'=>'BB',
                'sub_button' => array(
                   array(
                       'type' => 'pic_sysphoto',
                       'name' => 'BB1',
                        "key" => "rselfmenu_1_1", 
                        "sub_button" => array(),
                   ),
                   array(
                       'type' => 'pic_photo_or_album',
                       'name' => 'BB2',
                        "key" => "rselfmenu_1_2", 
                        "sub_button" => array(),
                   ),
                   array(
                       'type' => 'location_select',
                       'name' => 'BB3',
                        "key" => "rselfmenu_1_3", 
                        "sub_button" => array(),
                   ),
                ),
            ),
        ),
);
$menuData = api_json_encode($menuData);
//�����˵�
$msg = API_Item_Open_Weixin::createManu(array(
                                            'appId'        => AppID,    #����ŵ� appId
                                            'appSecret'    => AppSecret,    #����ŵ� appSecret
                                            'data'         => $menuData
		));

var_dump($msg);