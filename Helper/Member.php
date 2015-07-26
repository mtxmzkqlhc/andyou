<?php
/**
 * ��Ա���
 */
class Helper_Member extends Helper_Abstract {
   
    public static $strCipher = "admin_cipher";
    public static $strUid    = "admin_uid";
    
    //�˳���¼
    public static function loginOut(){        
        setcookie(self::$strCipher,'xxx',1,"/","");
        setcookie(self::$strCipher,"xxx", null, "/", "", null, true);
    }
    
    /**
     * ���е�¼��֤�ж�
     */
    public static function checkLogin($paramArr) {
		$options = array(
            'userid'       => '',      #�û�ID
            'cipher'       => '',      #��ȫ��
		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        
        if(!$userid || !$cipher)return false;
        
        $ckCipher = self::createCipher(array('userid' => $userid));        
        $flag = in_array($cipher, $ckCipher['ARR']) ? true : false;
        if($flag){
            setcookie(self::$strCipher, $ckCipher['THIS'], SYSTEM_TIME + 86400, "/", "", null, true);   
        }
        
        return $flag;
    }
    
    /**
     * �����û��ļ��ܴ�
     */
    public static function createCipher($paramArr) {
		$options = array(
            'userid'       => '',      #�û�ID
		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        if(!$userid)return false;
        
        $salt     = "L0veZ0L&Y0U-";  #������salt
        #$ip       = ZOL_Api::run("Service.Area.getClientIp" , array()); #��ǰ�û���cookie
        $ip       = $_SERVER["REMOTE_ADDR"];#����IP��ַ�򷽷���������Щ�ͻ��޷���¼
        #Ϊ�˼���e.test.zol.com.cn e.zol.com.cn����ͬʱ��¼״̬
        if($ip == "10.15.184.14") $ip = "10.19.12.122";
        $dateStr1 = date("ymd",SYSTEM_TIME); 
        $dateStr2 = date("ymd",SYSTEM_TIME-3600); #�ϸ�Сʱ��ʱ����Ϣ
        $string   = $userid . $ip . $salt;// . $_SERVER['HTTP_USER_AGENT'];
        $thisCf   = substr(md5($string . $dateStr1),2,12);
        $lastCf   = substr(md5($string . $dateStr2),2,12);
        
        return array(
            'THIS' => $thisCf, #���Сʱ�ļ��ܴ�
            'LAST' => $lastCf, #�ϸ�Сʱ�ļ��ܴ�
            'ARR'  => array($thisCf,$lastCf),
        );
    }
    /**
     * �û������������֤
     */
    public static function checkPasswd($paramArr) {
		$options = array(
            'userId'       => '',      #�û�ID
            'password'     => '',      #�û�����
            'backUrl'      => '',      #��¼�ɹ�����תҳ��
            'logFlag'      => false    #�Ƿ��¼�û���¼��־
		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        
         $password = Helper_AdminUser::passwdEncrypt($password);
         $userInfo = Helper_AdminUser::getAdminUserInfo(array('userId'=>$userId));
         $rtnFlag = 0;
         if($userInfo){
             if($password == $userInfo["passwd"]){
                 $rtnFlag = 1;//��¼�ɹ�
             }else{
                 $rtnFlag = 2;//�������
             }
         }else{
             $rtnFlag = 3;//�޸��û�
         }
         return $rtnFlag;
    }
    
    /**
     * ��¼
     */
    public static function login($paramArr) {
		$options = array(
            'userId'       => '',      #�û�ID
            'password'     => '',      #�û�����
		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        
        //��֤�û�������
        $checkFlag = self::checkPasswd(array('userId'=>$userId,'password'=>$password));
        
        if ($checkFlag === 1) {#��¼�ɹ�
            
            $ckCipher            = self::createCipher(array('userid' => $userId));
                
            setcookie(self::$strUid, $userId,SYSTEM_TIME + 31536000, "/", "");
            setcookie(self::$strCipher, $ckCipher['THIS'], SYSTEM_TIME + 86400, "/", "", null, true);
            
        } else {#��¼ʧ��
        
        }
        
        return $checkFlag;
    }
    
    
    
    
    
    /**
     * ��û�Ա��������б�
     */
    public static function getMemberCatePairs(){
        
        $db = Db_Andyou::instance();
        return $db->getPairs("select id,name from membercate ","id","name");
            
    }


    /**
     * ��û�Ա��������б�
     */
    public static function getMemberCateInfoPairs(){
        
        $db  = Db_Andyou::instance();
        $res =  $db->getAll("select * from membercate ");
        $outArr = array();
        if($res){
            foreach($res as $re){
                $outArr[$re['id']] = $re;
            }
        }
        return $outArr;
            
    }
    
    /**
     * ��û�Ա��������б�
     */
    public static function getMemberCateList($params){
        $options = array(
            'num'             => 10,    #����
            'name'            => false, #������
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($name)$whereSql .= "and name = '{$name}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'membercate',    #����
                    'cols'          => '*',   #����
                    'limit'         => $num,    #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }



    /**
     * ���һ����Ա���������Ϣ
     */
    public static function getMemberCateInfo($params){
        $options = array(
            'id'              => false, #ID
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';

        if($id)$whereSql .= "and id = '{$id}' " ;

        $data = Helper_Dao::getRow(array(
                'dbName'        => 'Db_Andyou',    #���ݿ���
                'tblName'       => 'membercate',    #����
                'cols'          => 'id id,name name',   #����
                'whereSql'      => $whereSql,    #where����
                #'debug'        => 1,    #����
       ));
       
       return $data;
    }

  /**
     * ���һ����Ա��Ϣ
     */
    public static function getMemberInfo($params){
        $options = array(
            'id'              => false, #ID
            'phone'           => false, 
            'cardno'          => false, #����
            'phoneOrCardno'   => false, #�绰������߿���
            'name'            => false, 
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';
        if(!$id && !$phone && !$name && !$phoneOrCardno)return false;
        
        if($id)$whereSql .= "and id = '{$id}' " ;
        if($phone)$whereSql .= "and phone = '{$phone}' " ;
        if($cardno)$whereSql .= "and cardno = '{$cardno}' " ;
        if($phoneOrCardno)$whereSql .= "and (cardno = '{$phoneOrCardno}' or phone = '{$phoneOrCardno}') " ;
        
        if($name)$whereSql .= "and name like '%{$name}%' " ;
        $data = Helper_Dao::getRow(array(
                'dbName'        => 'Db_Andyou',    #���ݿ���
                'tblName'       => 'member',    #����
                'cols'          => '*',   #����
                'whereSql'      => $whereSql,    #where����
                #'debug'        => 1,    #����
       ));
        //��û�Ա����
       $memberCate = Helper_Member::getMemberCateInfoPairs();
       if($data){
           if(isset( $memberCate[ $data["cateId"] ])){
               $data['cateName'] = $memberCate[$data["cateId"]]["name"];
               $data['discount'] = $memberCate[$data["cateId"]]["discount"];
               if($memberCate[$data["cateId"]]['discountStr']){//�ۿ۵ķ���
                   $data['discountArr'] = json_decode($memberCate[$data["cateId"]]['discountStr'], true);
               }
               
           }else{
               $data['cateName'] = "δ����";
               $data['discount'] = 1;
           }
       }
       return $data;
    }

    /**
     *  ��¼��Ա��û�����ʷ
     */
    public static function addScoreLog($params){
        $options = array(
            'memberId'         => false, #ID
            'direction'        => 1, #1 �� 0 ��
            'score'            => 0, #����
            'orgScore'         => -1, #ԭʼ����
            'bno'              => 0, #������
            'remark'           => '', #
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
        
        if(!$memberId)return false;
        
        $arr = $options;
        $arr['dateTm'] = SYSTEM_TIME;
        
        if($orgScore === -1){
            $info = self::getMemberInfo(array("id"=>$memberId));
            if($info){
                $arr['orgScore'] = $info["score"];
            }
        }
        
		Helper_Dao::insertItem(array(
            'addItem'       =>  $arr, #������
            'dbName'        =>  'Db_Andyou',    #���ݿ���
            'tblName'       =>  'log_scorechange',    #����
		));
        
        return true;
    }
    
    

    /**
     *  ��¼��Ա��û�Ա�������ʷ
     */
    public static function addCardLog($params){
        $options = array(
            'memberId'         => false, #ID
            'direction'        => 1, #1 �� 0 ��
            'card'             => 0, #����
            'orgCard'         => -1, #ԭʼ����
            'bno'              => 0, #������
            'remark'           => '', #
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
        
        if(!$memberId)return false;
        
        $arr = $options;
        $arr['dateTm'] = SYSTEM_TIME;
        
        if($orgScore === -1){
            $info = self::getMemberInfo(array("id"=>$memberId));
            if($info){
                $arr['orgCard'] = $info["balance"];
            }
        }
        
		Helper_Dao::insertItem(array(
            'addItem'       =>  $arr, #������
            'dbName'        =>  'Db_AndyouYun',    #���ݿ���
            'tblName'       =>  'member_login',    #����
		));
        
        
        return true;
    }
    
    /**
     *  ��¼��Ա��û�Ա�������ʷ
     */
    public static function getOtherPros($params){
        $options = array(
            'id'              => false, #ID
            'phone'           => false, 
            'cardno'          => false, #����
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
        
        $whereSql   = '';
        if(!$id && !$phone && !$name && !$phoneOrCardno)return false;
        
        if($id)$whereSql .= "and m.id = '{$id}' " ;
        if($phone)$whereSql .= "and m.phone = '{$phone}' " ;
        if($cardno)$whereSql .= "and m.cardno = '{$cardno}' " ;
        if($phoneOrCardno)$whereSql .= "and (m.cardno = '{$phoneOrCardno}' or m.phone = '{$phoneOrCardno}') " ;
        
        $db  = Db_Andyou::instance();
        $sql = "select p.id,p.proId,p.name,p.proName,p.num,p.ctype,p.buytm from member m left join memeberotherpro p on m.id = p.memberId where p.num > 0  ".$whereSql;
        $data = $db->getAll($sql);
        
        return $data;
    }
}
