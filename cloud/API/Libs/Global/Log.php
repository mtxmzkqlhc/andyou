<?php
/**
* ��־��
* @author wangmc
* @copyright (c) 2011-10-20
*/
class API_Libs_Global_Log
{   
    
    public  static  $logGroup  = array(
                'adChinaPlace'  =>array(
                        'mail'=>array(
                               'tpl'      =>"�״�ý���ɹ��λ����\n==����=====================================================================\n#P#\n==option===================================================================\n#I#\n==����=====================================================================\n#E#\n",
                               'title'    =>'�״�ýͬ������',
                               'userList' =>array("wang.mingchao@zol.com.cn")
                        )
                ),
                'payInvoice'  =>array(
                        'mail'=>array(
                               'tpl'      =>"��Ʊͨ���ӿڼ��\n==����=====================================================================\n#P#\n==option===================================================================\n#I#\n==����=====================================================================\n#E#\n",
                               'title'    =>'��Ʊͨ���ӿڼ��',
                               'userList' =>array("wang.mingchao@zol.com.cn")
                        )
                ),
    );

    /**
	* ��ȡ���ҳ����
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
              return  array('state' =>0,'msg'=>'�㻹û��Ȩ�ޣ���Ҫ����');
        }
        
        foreach (self::$logGroup[$module]  as  $type=>$value){
            switch ($type){
                #�������Ҫ���ʼ��ͷ��͸������
                case 'mail':self::sendEmailLog(array('content'=>is_array($content)?self::toTpl(array(
                        'tpl'       =>$value['tpl'],
                        'strArr'    =>$content
                )):$content,'title'=>$value['title'],'toArr'=>$value['userList']));break;
                
                
            }   
        }
        
        return  array('state'=>1,'msg' =>'������־�ɹ�');
	}
    
    
    /**
     * �����ʼ���װ
     * 
     */
    public  static  function   sendEmailLog($paramArr){
        $options = array(
            'content'    => '',
            'toArr'      => '',
            'title'      => '˽������־����'
        );
        
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(empty($content) || empty($toArr) || !is_array($toArr))  return false; 
        API_Item_Service_Message::mailFile(array(
            'mailto'       => implode('#', $toArr),#����ʼ���ַ�룬�ָ�
            'mailfrom'     => 'service@zol.com.cn',#����Դ��ַ
            'fromname'     => '˽������־ϵͳ',#�����ˣ�
            'subject'      => "��˽������־ϵͳ��".$title,#����
            'content'      => $content,#����
            'type'         => 2,#�ʼ����� 1 html 2�ı�   
        ));
        
    }
    
    
    /**
     * ���ͺ�ϻ�ӷ�װ
     * 
     */
    
    
    /**
     * �����ֻ�
     */
    
    /**
     * ģ�����
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

