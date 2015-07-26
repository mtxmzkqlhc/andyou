<?php
/**
 * 会员相关
 */
class Helper_Member extends Helper_Abstract {
   
    public static $strCipher = "admin_cipher";
    public static $strUid    = "admin_uid";
    
    //退出登录
    public static function loginOut(){        
        setcookie(self::$strCipher,'xxx',1,"/","");
        setcookie(self::$strCipher,"xxx", null, "/", "", null, true);
    }
    
    /**
     * 进行登录验证判断
     */
    public static function checkLogin($paramArr) {
		$options = array(
            'userid'       => '',      #用户ID
            'cipher'       => '',      #安全串
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
     * 生成用户的加密串
     */
    public static function createCipher($paramArr) {
		$options = array(
            'userid'       => '',      #用户ID
		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        if(!$userid)return false;
        
        $salt     = "L0veZ0L&Y0U-";  #加密用salt
        #$ip       = ZOL_Api::run("Service.Area.getClientIp" , array()); #当前用户的cookie
        $ip       = $_SERVER["REMOTE_ADDR"];#更换IP地址或方法，发现有些客户无法登录
        #为了兼容e.test.zol.com.cn e.zol.com.cn不能同时登录状态
        if($ip == "10.15.184.14") $ip = "10.19.12.122";
        $dateStr1 = date("ymd",SYSTEM_TIME); 
        $dateStr2 = date("ymd",SYSTEM_TIME-3600); #上个小时的时间信息
        $string   = $userid . $ip . $salt;// . $_SERVER['HTTP_USER_AGENT'];
        $thisCf   = substr(md5($string . $dateStr1),2,12);
        $lastCf   = substr(md5($string . $dateStr2),2,12);
        
        return array(
            'THIS' => $thisCf, #这个小时的加密串
            'LAST' => $lastCf, #上个小时的加密串
            'ARR'  => array($thisCf,$lastCf),
        );
    }
    /**
     * 用户名和密码的验证
     */
    public static function checkPasswd($paramArr) {
		$options = array(
            'userId'       => '',      #用户ID
            'password'     => '',      #用户密码
            'backUrl'      => '',      #登录成功后跳转页面
            'logFlag'      => false    #是否记录用户登录日志
		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        
         $password = Helper_AdminUser::passwdEncrypt($password);
         $userInfo = Helper_AdminUser::getAdminUserInfo(array('userId'=>$userId));
         $rtnFlag = 0;
         if($userInfo){
             if($password == $userInfo["passwd"]){
                 $rtnFlag = 1;//登录成功
             }else{
                 $rtnFlag = 2;//密码错误
             }
         }else{
             $rtnFlag = 3;//无该用户
         }
         return $rtnFlag;
    }
    
    /**
     * 登录
     */
    public static function login($paramArr) {
		$options = array(
            'userId'       => '',      #用户ID
            'password'     => '',      #用户密码
		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        
        //验证用户和密码
        $checkFlag = self::checkPasswd(array('userId'=>$userId,'password'=>$password));
        
        if ($checkFlag === 1) {#登录成功
            
            $ckCipher            = self::createCipher(array('userid' => $userId));
                
            setcookie(self::$strUid, $userId,SYSTEM_TIME + 31536000, "/", "");
            setcookie(self::$strCipher, $ckCipher['THIS'], SYSTEM_TIME + 86400, "/", "", null, true);
            
        } else {#登录失败
        
        }
        
        return $checkFlag;
    }
    
    
    
    
    
    /**
     * 获得会员分类管理列表
     */
    public static function getMemberCatePairs(){
        
        $db = Db_Andyou::instance();
        return $db->getPairs("select id,name from membercate ","id","name");
            
    }


    /**
     * 获得会员分类管理列表
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
     * 获得会员分类管理列表
     */
    public static function getMemberCateList($params){
        $options = array(
            'num'             => 10,    #数量
            'name'            => false, #分类名
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($name)$whereSql .= "and name = '{$name}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #数据库名
                    'tblName'       => 'membercate',    #表名
                    'cols'          => '*',   #列名
                    'limit'         => $num,    #条数
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }



    /**
     * 获得一条会员分类管理信息
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
                'dbName'        => 'Db_Andyou',    #数据库名
                'tblName'       => 'membercate',    #表名
                'cols'          => 'id id,name name',   #列名
                'whereSql'      => $whereSql,    #where条件
                #'debug'        => 1,    #调试
       ));
       
       return $data;
    }

  /**
     * 获得一条会员信息
     */
    public static function getMemberInfo($params){
        $options = array(
            'id'              => false, #ID
            'phone'           => false, 
            'cardno'          => false, #卡号
            'phoneOrCardno'   => false, #电话号码或者卡号
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
                'dbName'        => 'Db_Andyou',    #数据库名
                'tblName'       => 'member',    #表名
                'cols'          => '*',   #列名
                'whereSql'      => $whereSql,    #where条件
                #'debug'        => 1,    #调试
       ));
        //获得会员类型
       $memberCate = Helper_Member::getMemberCateInfoPairs();
       if($data){
           if(isset( $memberCate[ $data["cateId"] ])){
               $data['cateName'] = $memberCate[$data["cateId"]]["name"];
               $data['discount'] = $memberCate[$data["cateId"]]["discount"];
               if($memberCate[$data["cateId"]]['discountStr']){//折扣的分类
                   $data['discountArr'] = json_decode($memberCate[$data["cateId"]]['discountStr'], true);
               }
               
           }else{
               $data['cateName'] = "未分类";
               $data['discount'] = 1;
           }
       }
       return $data;
    }

    /**
     *  记录会员获得积分历史
     */
    public static function addScoreLog($params){
        $options = array(
            'memberId'         => false, #ID
            'direction'        => 1, #1 减 0 加
            'score'            => 0, #积分
            'orgScore'         => -1, #原始积分
            'bno'              => 0, #订单号
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
            'addItem'       =>  $arr, #数据列
            'dbName'        =>  'Db_Andyou',    #数据库名
            'tblName'       =>  'log_scorechange',    #表名
		));
        
        return true;
    }
    
    

    /**
     *  记录会员获得会员卡余额历史
     */
    public static function addCardLog($params){
        $options = array(
            'memberId'         => false, #ID
            'direction'        => 1, #1 减 0 加
            'card'             => 0, #积分
            'orgCard'         => -1, #原始积分
            'bno'              => 0, #订单号
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
            'addItem'       =>  $arr, #数据列
            'dbName'        =>  'Db_AndyouYun',    #数据库名
            'tblName'       =>  'member_login',    #表名
		));
        
        
        return true;
    }
    
    /**
     *  记录会员获得会员卡余额历史
     */
    public static function getOtherPros($params){
        $options = array(
            'id'              => false, #ID
            'phone'           => false, 
            'cardno'          => false, #卡号
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
