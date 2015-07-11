<?php
/**
* 日志类
* @author wangmc
* @copyright (c) 2011-10-20
*/
class API_Libs_Global_Log
{   
    
    public  static  $logGroup  = array(
                'adChinaPlace'  =>array(
                        'mail'=>array(
                               'tpl'      =>"易传媒生成广告位错误\n==参数=====================================================================\n#P#\n==option===================================================================\n#I#\n==错误=====================================================================\n#E#\n",
                               'title'    =>'易传媒同步错误',
                               'userList' =>array("wang.mingchao@zol.com.cn")
                        )
                ),
                'payInvoice'  =>array(
                        'mail'=>array(
                               'tpl'      =>"发票通过接口监控\n==参数=====================================================================\n#P#\n==option===================================================================\n#I#\n==错误=====================================================================\n#E#\n",
                               'title'    =>'发票通过接口监控',
                               'userList' =>array("wang.mingchao@zol.com.cn")
                        )
                ),
    );

    /**
	* 获取配件页链接
	*/
	public static function set($paramArr)
	{
        $options = array(
            'module'     => '',
            'content'    => ''
        );
        
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty(self::$logGroup[$module])){
              return  array('state' =>0,'msg'=>'你还没有权限，需要配置');
        }
        
        foreach (self::$logGroup[$module]  as  $type=>$value){
            switch ($type){
                #如果是需要发邮件就发送给相关人
                case 'mail':self::sendEmailLog(array('content'=>is_array($content)?self::toTpl(array(
                        'tpl'       =>$value['tpl'],
                        'strArr'    =>$content
                )):$content,'title'=>$value['title'],'toArr'=>$value['userList']));break;
                
                
            }   
        }
        
        return  array('state'=>1,'msg' =>'发送日志成功');
	}
    
    
    /**
     * 发送邮件封装
     * 
     */
    public  static  function   sendEmailLog($paramArr){
        $options = array(
            'content'    => '',
            'toArr'      => '',
            'title'      => '私有云日志反馈'
        );
        
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($content) || empty($toArr) || !is_array($toArr))  return false; 
        API_Item_Service_Message::mailFile(array(
            'mailto'       => implode('#', $toArr),#多个邮件地址请，分割
            'mailfrom'     => 'service@zol.com.cn',#发送源地址
            'fromname'     => '私有云日志系统',#发送人，
            'subject'      => "【私有云日志系统】".$title,#标题
            'content'      => $content,#内容
            'type'         => 2,#邮件类型 1 html 2文本   
        ));
        
    }
    
    
    /**
     * 发送黑匣子封装
     * 
     */
    
    
    /**
     * 发送手机
     */
    
    /**
     * 模板解析
     * 
     */
    public  static   function  toTpl($paramArr){
        $options = array(
            'tpl'         => '',
            'strArr'      => ''
        );
        
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty ($tpl)  ||  empty($strArr))  return false;
        
        $replaceKey  = array_keys($strArr);
        $replaceVal  = array_values($strArr);
        
        return  str_replace($replaceKey, $replaceVal, $tpl);
        
    }
    
    
    
}

