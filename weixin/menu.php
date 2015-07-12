<?php

$path = dirname(__FILE__);
require($path . '/config.php'); //引入私有云入口文件

$str = '{
     "button":[
     {	
          "type":"click",
          "name":"今日歌曲",
          "key":"V1001_TODAY_MUSIC"
      },
      {
           "name":"菜单",
           "sub_button":[
           {	
               "type":"view",
               "name":"搜索",
               "url":"http://www.soso.com/"
            },
            {
               "type":"view",
               "name":"视频",
               "url":"http://v.qq.com/"
            },
            {
               "type":"click",
               "name":"赞一下我们",
               "key":"V1001_GOOD"
            }]
       }]
 }';


//获得一个AccessToken
$accessToken = API_Item_Open_Weixin::getAccessToken( array(
                                            'appId'        => AppID,    #服务号的 appId
                                            'appSecret'    => AppSecret,    #服务号的 appSecret
                                        ));
$menuData = array(
    'button' => array(
            array(
                'name'=>'会员',
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
//创建菜单
$msg = API_Item_Open_Weixin::createManu(array(
                                            'appId'        => AppID,    #服务号的 appId
                                            'appSecret'    => AppSecret,    #服务号的 appSecret
                                            'data'         => $menuData
		));

var_dump($msg);