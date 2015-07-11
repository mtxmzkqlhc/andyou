<?php
/**
* �û����
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c)
*/

class API_Libs_Global_User
{
	const ZOL_USER_KEY = 'sa^2fa*%mdpyw$@4';
	/**
	* @var ZOL_Product_Lib_User
	*/
	private static $_instance;
	
	private static $_userId;
	
	private static $_userInfo;
	/**
	* ��̳��
	* @var ZOL_Db_User
	*/
	private static $_dbDiscussion;
	
	/**
	* �û���
	* @var ZOL_Db_User
	*/
	private static $_dbUser;
    private static $_dbUserWrite;

	private static $_userLevelArr = array(
		'god'  => array('img' => 'super_sun', 'name' => '����̫��'),
		'sun'  => array('img' => 'time_sun', 'name' => '̫��'),
		'moon' => array('img' => 'time_yueliang', 'name' => '����'),
		'star' => array('img' => 'time_star', 'name' => '����'),
	);
	/**
	* ���淽�����
	* 
	* @var array
	*/
	private static $_cache;
	
	public function __construct($userId = 0)
	{
		if ($userId) {
			self::$_userInfo = self::getUserFace($userId);
			self::$_userId = $userId;
		}
	}
	
	/**
	* ����
	* @return ZOL_Product_Lib_User
	*/
	public static function instance()
	{
		if (self::$_instance == null) {
			$className = get_called_class();
			self::$_instance = new $className;
		}
		return self::$_instance;
	}
	
	/**
	* ��ʼ��
	*/
	public static function init()
	{
		self::loadDb();
	}
	
	/**
	* �������ݿ�
	*/
	public static function loadDb()
	{
		self::$_dbDiscussion = API_Db_Discussion::instance();
		self::$_dbUser       = API_Db_User::instance();
        self::$_dbUserWrite  = API_Db_UserWrite::instance();
	}
	
	/**
	* ��ȡ�û�ͷ�������
	* @param userid $userId �û�ID
	* @return array
	*/
	public static function getUserFaceData($userId)
	{
		self::init();
		$sql = "SELECT id,isphoto isPhoto FROM z_user_extend WHERE userid='{$userId}'";
		return self::$_dbUser->getRow($sql);
	}

	/**
	* ������ȡ�û�ͷ�������
	* @param $userIdArr �û�ID����
	* @return array
	*/
	public static function getMulUserFaceData($userIdArr)
	{
		self::init();
		$userIds = implode("','",$userIdArr);		
		$sql = "SELECT id,isphoto isPhoto ,userid userId FROM z_user_extend WHERE userid in ('{$userIds}')";
		$datas = self::$_dbUser->getAll($sql);
		$outArr = array();
		if($datas){
			foreach($datas as $d){
				$outArr[$d['userId']] = $d;
			}
		}
		return $outArr;
	}
	
	/**
	* ��ȡ�û�������Ϣ
	*/
	public static function getUserInfo($userId = 0)
	{
		#�ϴ�ȡ��,����һ��
		$ckey = 'getUserInfo';
		if(!isset(self::$_cache[$ckey]))self::$_cache[$ckey] = array();

		if (isset(self::$_cache[$ckey][$userId])) {
			return self::$_cache[$ckey][$userId];
		}

		self::init();
		$faceInfo = self::getUserFaceData($userId);
		
		$sql = "SELECT u.userid,u.nickname nickName, u.fullName, u.Sex sex, u.RegisterDate regDate, u.Birthday birthday, u.Employment emp, u.Education edu, u.Province province, s.score
				FROM UserInfo u LEFT JOIN z_user_score s ON u.UserID=s.userid
				WHERE u.UserID='{$userId}'";
		$userInfo = self::$_dbUser->getRow($sql);
		
		if ($userInfo) {
			$userInfo['id']      = $faceInfo['id'];
			$userInfo['isPhoto'] = $faceInfo['isPhoto'];
			$userInfo['face']    = self::getUserFace($userInfo['id'], $userInfo['isPhoto']);			
		}
		self::$_cache[$ckey][$userId] = $userInfo;
		return $userInfo;
	}


	/**
	* ��ȡ����û��Ļ�����Ϣ
	*/
	public static function getMulUserInfo($userIdArr)
	{
		if(empty($userIdArr))return false;

		$userIds = $comma = '';
		$ckey = 'getUserInfo';
		if(!isset(self::$_cache[$ckey]))self::$_cache[$ckey] = array();
		
		$outArr = array();
		foreach($userIdArr as $u){
			if (isset(self::$_cache[$ckey][$u])) {#�ж��Ƿ���ڴ���ȡ���û�
				$outArr[$u] = self::$_cache[$ckey][$u];
				continue;
			}
			$userIds .= $comma . $u;
			$comma = "','";
		}
		if($userIds){#û���ڴ��е��û�
			self::init();
			$sql = "SELECT u.userid,u.nickname nickName, u.fullName, u.Sex sex, u.RegisterDate regDate, u.Birthday birthday, u.Employment emp, u.Education edu, u.Province province, s.score
					FROM UserInfo u LEFT JOIN z_user_score s ON u.UserID=s.userid
					WHERE u.UserID IN ('{$userIds}')";
			$data = self::$_dbUser->getAll($sql);
			if($data){

				#������е��û�ͷ����Ϣ
				$allFaceData = self::getMulUserFaceData(explode("','", $userIds));

				foreach($data as $userInfo){
					$userId = $userInfo['userid'];
					if(isset($allFaceData[$userId])){
						$faceInfo = $allFaceData[$userId];
						$userInfo['id']      = $faceInfo['id'];
						$userInfo['isPhoto'] = $faceInfo['isPhoto'];
						$userInfo['face']    = self::getUserFace($userInfo['id'], $userInfo['isPhoto']);
					}else{
						$userInfo['face']    = self::getUserFace(0,0);
					}
					$outArr[$userId] = $userInfo;
					self::$_cache[$ckey][$userId] = $userInfo;;
				}
			}
		}

		return $outArr;

	}

    /**
	* ��ȡ�û��ǳ�
	*/
	public static function getNickName($userStr)
	{
		self::init();
		$sql = "SELECT UserID,nickname FROM UserInfo WHERE UserID in ({$userStr})";
		$rs  = self::$_dbUser->getAll($sql);
        $userArr = '';
        if ($rs) {
            foreach ($rs as $value) {
                $userArr[$value['UserID']] = 
                $value['nickname'] ? $value['nickname'] : $value['UserID'];
            }
        }
		return $userArr;
	}
    
    /**
    * ��ȡ�û�ͷ���Ƿ�����ʵͷ��
    */
    public static function getUserRealPhoto($userId = 0)
    {
        if ($userId && $userId == self::$_userId && self::$_userInfo) {
            return self::$_userInfo;
        }
        self::init();
        
        $sql = "SELECT real_photo from z_user_extend where userid ='{$userId}'";
        $realPhoto = self::$_dbUser->getRow($sql);

        if ($realPhoto) {
            $isRealPhoto = $realPhoto['real_photo'];
        } else {
            $isRealPhoto = '0';
        }
        return $isRealPhoto;
    }
	
	
	/**
	* ��ȡ�û�����
	*/
	public static function getUserArea($userId)
	{
		if (!$userId) {
			return false;
		}
		
		$sql = "SELECT e.Name provinceName, c.name townName,d.name cityName 
				from UserInfo a 
					LEFT JOIN UserInfo_pro_town_city b ON a.UserID=b.userid
					LEFT JOIN UserInfo_town c ON b.town_id=c.id 
					LEFT JOIN UserInfo_city d ON b.city_id=d.id
					LEFT JOIN Province e ON a.Province=e.SID
			WHERE a.UserID='{$userId}'";
		self::loadDb();
		return self::$_dbUser->getRow($sql);
	}
	
	/**
	* �û�������Ϣ���
	* @param int ѧ��ID
	* @param enum ��� {edu|emp|income|industry|province}
	* @return array
	*/
	public static function getCate($id = 0, $type = 'edu')
	{
		$tables = array(
			'edu' => 'Education',
			'emp' => 'Employment',
			'income' => 'Income',
			'industry' => 'Industry',
			'province' => 'Province'
		);
		
		if (!isset($tables[$type])) {
			return false;
		}
		$table = $tables[$type];
		$key = 'get' . $table;
		if (isset(self::$_cache[$key][$id])) {
			return self::$_cache[$key][$id];
		}
		
		if ($id) {
			$conditions = " AND SID='{$id}'";
			$orderBy = '';
			$cols = "Name";
			$method = 'getOne';
		} else {
			$orderBy = " ORDER BY Sequence";
			$conditions = '';
			$cols = 'SID id, Name `name`';
			$method = 'getAll';
		}
		$conditions .= ' AND Status=1';
		
		$sql = "SELECT {$cols} FROM {$table} WHERE 1 {$conditions} {$orderBy}";
		$data = API_Db_User::instance()->$method($sql);
		$_data = array();
		if (is_array($data)) {
			foreach ($data as $row) {
				$_data[$row['id']] = $row['name'];
			}
		}
		self::$_cache[$key][$id] = $_data;
		return self::$_cache[$key][$id];
	}
	
	/**
	* ��ȡְҵ
	* @param int $id ְҵID
	* @return array
	*/
	public static function getEmp($id = 0)
	{
		return self::getCate($id, 'emp');
	}
	
	/**
	* ��ȡѧ��
	* @param int $id ѧ��ID
	* @return array
	*/
	public static function getEdu($id = 0)
	{
		return self::getCate($id, 'edu');
	}
	
	/**
	* ��ȡʡ��
	* 
	* @param int $id ʡ��ID
	* @return array
	*/
	public static function getProvince($id = 0)
	{
		return self::getCate($id, 'province');
	}

	/**
	* ��ȡ����ʡ��
    * @author wang.tao5@zol.com.cn
	* @return array
    * @copyright 2011��3��8��13:53:55
	*/
	public static function getAllProvince()
	{
        self::init();
        $allProvinceSql = 'select SID id, Name name from Province where Status = 1 order by Sequence ASC';
        $allProvinceRes = self::$_dbUser->getAll($allProvinceSql);
		return $allProvinceRes;
	}

	/**
	* ��ȡ��ǰʡ�������г���
    * @author wang.tao5@zol.com.cn
	* @return array
    * @copyright 2011��3��8��14:14:17
	*/
	public static function getAllTown($provinceId)
	{
        $allCitySql = 'select id, name from UserInfo_town where pro_id = ' . $provinceId . ' order by sequence';
        $allCityRes = self::$_dbUser->getAll($allCitySql);
		return $allCityRes;
	}

	/**
	* ��ȡ��ǰʡ�������г���
    * @author wang.tao5@zol.com.cn
	* @return array
    * @copyright 2011��3��8��14:14:17
	*/
	public static function getAllCity($townId)
	{
        $allCitySql = 'select id, name from UserInfo_city where town_id = ' . $townId . ' order by sequence';
        $allCityRes = self::$_dbUser->getAll($allCitySql);
		return $allCityRes;
	}

	/**
	* �����û���Ϣ
    * @author wang.tao5@zol.com.cn
	* @return bool
    * @copyright 2011��3��8��16:49:01
	*/
	public static function getUpdateUserInfo($userInfo)
	{
        self::init();

        #�����û���Ϣ
        $brithday = $userInfo['year'] . '-' . $userInfo['month'] . '-' . $userInfo['day'];
        'select UserID, FullName, nickname, Sex, Birthday, Province from UserInfo where userid = ';
        $updateInfoSql = 'update UserInfo set FullName = "' . $userInfo['fullName'] .
                                            '", nickname = "' . $userInfo['nickName'] .
                                            '", Sex = ' . $userInfo['sex'] .
                                            ', Birthday = "' . $brithday .
                                            '" where UserID = "' . $userInfo['id'] . '"';
        self::$_dbUserWrite->query($updateInfoSql);

        #ʡ��ID
        $provinceIdSql = 'select SID from Province where Name = "' . $userInfo['province'] . '"';
        $provinceIdRes = self::$_dbUser->getAll($provinceIdSql);

        #����ID
        $townIdSql     = 'select id from UserInfo_town where pro_id = ' . $provinceIdRes[0]['SID'] . ' and name = "' . $userInfo['town'] . '"';
        $townIdRes     = self::$_dbUser->getAll($townIdSql);

        #�ؼ�����ID
        $cityIdSql     = 'select id from UserInfo_city where pro_id = ' . $provinceIdRes[0]['SID'] . ' and town_id = ' . $townIdRes[0]['id'] . ' and name = "' . $userInfo['city'] .'"';
        $cityIdRes     = self::$_dbUser->getAll($cityIdSql);

        #�����û���������
        $updateAreaSql = 'update UserInfo_pro_town_city set pro_id = ' . $provinceIdRes[0]['SID'] . ', town_id = ' . $townIdRes[0]['id'] . ', city_id = ' . $cityIdRes[0]['id'] .' where userid = "' . $userInfo['id'] . '"';
        self::$_dbUserWrite->query($updateAreaSql);
		return $allCityRes;
	}
	
	/**
	* ��ȡ�û�ͷ��
	*/
	public static function getUserFace($uid = 0, $isPhoto = 0, $size = 50)
	{
		if ($isPhoto === 'USERID') {
			$faceInfo = self::getUserFaceData($uid);
			$uid = $faceInfo['id'];
			$isPhoto = $faceInfo['isPhoto'];
		}
		
		$face = $isPhoto
			? 'http://8.zol-img.com.cn/bbs/user_photo/' . ceil($uid / 1000) . '/'.$size.'/' . $uid . '_'.$size.'.jpg'
			: 'http://icon.zol-img.com.cn/photo/zoler_50.jpg';
		return $face;
	}
	
	/**
	* ��ȡ�û�ͷ��
	*/
	public static function getFace($uid)
	{
		$face = 'http://8.zol-img.com.cn/bbs/user_photo/' . ceil($uid / 1000) . '/' . $uid . '_s.jpg';
		return $face;
	}	
	
	/**
	* ����û�״̬
	* 
	* @param mixed $userId ����id $_COOKIE['zol_userid']
	* @param mixed $checkId ��¼�����֤�� $_COOKIE['zol_check']
	* @param mixed $cipher userid �� checkid�ļ����ַ��� $_COOKIE['zol_cipher']
	* @return boolean ��¼״̬
	*/
	public static function checkUserStatus($userId, $checkId, $cipher)
	{
		if(!$userId || !$checkId || !$cipher) {
			return false;
		}
		
		$zcipher = md5(md5(self::ZOL_USER_KEY . $checkId) . $userId . self::ZOL_USER_KEY);
		return ($zcipher == $cipher);
	}

    /**
     * �õ��û���ʡ��ID
     * @param <type> $userId
     * @author wang.tao5@zol.com.cn
     * @copyright 2011��3��10��11:59:22
     */
    public static function getUserProvince ($userId)
    {
        self::init();
        $provinceSql = 'select province from UserInfo where UserID = "' . $userId . '"';
        $provinceRes = self::$_dbUser->getOne($provinceSql);
        return $provinceRes;
    }
   /**
     * �õ��û����Ա������
     * @param <type> $userId
     * @author wang.tao5@zol.com.cn
     * @copyright 2011��3��11��16:31:24
     */
    public static function getUserSexBirth ($userId)
    {
        self::init();
        $sexBirthSql = 'select Sex sex, Birthday birthday from UserInfo where UserID = "' . $userId . '"';
        $sexBirthRes = self::$_dbUser->getRow($sexBirthSql);
        return $sexBirthRes;
    }

   /**
     * �õ��û��ļ���
     * @param string $userId
     * @param string $sex
     * @author wang.tao5@zol.com.cn
     * @copyright 2011��3��11��16:31:24
     */
    public static function getUserLevel($score,$sex)
    {

		if($score<50) return "����";
		elseif($score<150) return "���";
		elseif($score<350) return "����";
		elseif($score<750) return "��ʿ";
		elseif($score<1550) return "��Ա";
		elseif($score<3150) return "̽��";
		elseif($score<6350) return "����";
		elseif($score<12750) return "״Ԫ";
		elseif($score<25550) return "��Ʒ";
		elseif($score<51150) return "��Ʒ";
		elseif($score<102350) return "��Ʒ";
		elseif($score<204750) return "��Ʒ";
		elseif($score<409550) return "��Ʒ";
		elseif($score<819150) return "��Ʒ";
		elseif($score<1639350) return "��Ʒ";
		elseif($score<3278750) return "��Ʒ";
		elseif($score<6557500) return "һƷ";
		elseif($score<13115000) return "�ʵ�";
			
    }

	/**
	* ����û������Ǽ�
	*/
	public static function getUserLevelStar($score)
	{
		$levelInfo = self::getLevelInfo( $score);
		$imgUrl = 'http://icon.zol-img.com.cn/bbs/detail/';
		if(!is_array($levelInfo)){
			return false;
		}

		$levelInfo['userStar'] = '';
		$imgStr = '<img src="' . $imgUrl . '{IMG}.gif" alt="'.$levelInfo['z_name'] . '[' . $levelInfo['z_score'] . '����]" />';
		$userStar = '';
		foreach (self::$_userLevelArr as $key => $val) {
			$col = 'z_' . $key;
			if (!empty($levelInfo[$col])) {
				$_img = str_replace('{IMG}', $val['img'], $imgStr);
				$userStar .= str_repeat($_img, $levelInfo[$col]);
			}
		}

		return $userStar;
	}
	/**
	 * ��ȡ�û��ĵȼ��ľ�����Ϣ,����̫��������
	 */
	public static function getLevelInfo($score)
	{

		$_dbDiscussion = API_Db_Discussion::instance();
		//��õ�ǰ�û��ȼ�
		$level	= (int)self::getLevelValue($score);

		#�Ƿ�ղ��Ѿ�ȡ��
		$ckey = 'getLevelInfo';
		if (isset(self::$_cache[$ckey]) && isset(self::$_cache[$ckey][$level])) {
			return self::$_cache[$ckey][$level];
		}
		
		//��õ�ǰ�Ǽ���Ϣ
		$sql = "SELECT `z_level`,`z_name`,`z_score`,`z_god`,`z_sun`,`z_moon`,`z_star`
				FROM z_rank WHERE `z_level`={$level}";

		$levelInfo = $_dbDiscussion->getRow($sql);
		
		self::$_cache[$ckey][$level] = $levelInfo;

		return $levelInfo;
	}

	/**
	 * ��ȡ�û��ĵȼ�ֵ
	 */
	public static function getLevelValue($score)
	{
		$score = (!empty($score) ? (int)$score : 0);

		$userScore = $score;
		for($level=1; $level<=18; $level++) {
			$levelScore = pow(2,$level-1)*100-50;
			if( $userScore	< $levelScore ){
				return $level;
			}
		}
		return 18;
	}

    public static function Login($User,$Password) {


	   self::$_dbUser       = API_Db_User::instance();
       if ($User){
          $backUrl=$_SERVER["HTTP_REFERER"];
          //��һ������֤�û���������
          $strsql="select UserID,Password,nickname,checkcode,UNIX_TIMESTAMP(LastLogin) as lastlogin,is_del , sid
                   from UserInfo where UserID = '{$User}'";
          $flag = 0;
          if ($rows = self::$_dbUser->getRow($strsql)){

                $UserID = $rows['UserID'];
                $pwd = $rows['Password'];
                $is_del = $rows['is_del'];
                $nickname = $rows['nickname'];

                $md_pwd = md5(md5($Password."zol").$User);
                $md_pwd = substr($md_pwd,0,16);
                if ((($pwd ==$Password)&&($is_del==0)) || (($pwd ==$md_pwd)&&($is_del==0))){
                    //$check = $rows['checkcode'];

					srand((double)microtime()*1000000);
					$check      = rand();

                    $cipher = md5(md5(self::ZOL_USER_KEY.$check).$UserID.self::ZOL_USER_KEY);
                    setcookie("zol_cipher", $cipher, SYSTEM_TIME + 86400,"/",".zol.com.cn");
                    setcookie("zol_userid", $UserID, SYSTEM_TIME + 86400,"/",".zol.com.cn");
                    setcookie("zol_check", $check, SYSTEM_TIME + 86400,"/",".zol.com.cn");
                    setcookie("zol_nickname", $nickname, SYSTEM_TIME + 86400,"/",".zol.com.cn");
					$sql = "update UserInfo set checkcode = '$check',LastLogin='".SYSTEM_DATE."' where UserID = '$UserID'";
					self::$_dbUser->query($sql);


					 //֧�ֶ໷����¼
					if ($rows['sid']) {
						$dateTime   = SYSTEM_TIME;
						$checkcodeTable = "z_checkcode_".ceil($rows['sid']/1000000);
						$sql = "CREATE TABLE if not exists `{$checkcodeTable}` (
							   `z_id` int(10) NOT NULL auto_increment,
							   `z_uid` int(10) unsigned NOT NULL default '0' COMMENT '�û�id',
							   `z_checkcode` bigint(20) unsigned default NULL COMMENT '��¼check��',
							   `z_time` int(10) unsigned NOT NULL default '0' COMMENT 'ʱ��',
							   PRIMARY KEY  (`z_id`),
							   KEY `uid` (`z_uid`,`z_checkcode`),
							   KEY `time` (`z_time`)
							 ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 comment '��¼check���'";
						self::$_dbUser->query($sql);

						$sql = "insert into {$checkcodeTable} (z_uid, z_checkcode, z_time) values ({$rows['sid']}, {$check}, {$dateTime}) ";
						self::$_dbUser->query($sql);

						$sql = "select count(*) from {$checkcodeTable} where z_uid={$rows['sid']} ";
						$totalCheckNum = (int)self::$_dbUser->getOne($sql);

						if ($totalCheckNum > 10) {
							$deleteNum = $totalCheckNum - 10;
							$sql = "delete from {$checkcodeTable} where z_uid={$rows['sid']} order by z_id asc limit {$deleteNum}";
							self::$_dbUser->query($sql);

						}
					}

					/* ��¼��¼ */
					$login_log_table = "user_login_log".date("Y");
					$sql = "CREATE TABLE if not exists $login_log_table (
							 `sid` int(11) NOT NULL auto_increment,
							 `userid` varchar(20) NOT NULL default '',
							 `ip` varchar(15) NOT NULL default '',
							 `wdate` datetime NOT NULL default '0000-00-00 00:00:00',
							 `ref_url` varchar(100) NOT NULL default '',
							 PRIMARY KEY  (`sid`),
							 KEY `userid` (`userid`),
							 KEY `wdate` (`wdate`)
							) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='���ѵ�¼��¼��'";
					self::$_dbUser->query($sql);
					
					$sql = "insert into $login_log_table (userid,ip,wdate,ref_url) values ('$UserID','".$_SERVER["REMOTE_ADDR"]."','".SYSTEM_DATE."','".$_SERVER['REQUEST_URL']."')";
					self::$_dbUser->query($sql);

                    $return_val = 1;  //��ʾ�û�����������ȷ����¼�ɹ�

                 }else{
                    $return_val = 0;  //�������
                 }
              }else{
                 $return_val = 0;  //�û���������
              }
           }else{
              $return_val = -1;  //û�������û���
           }

         return $return_val;

    }
    
    /**
	* ��ȡ�а���������û�����
	*/
	public static function getHelpUserInfo($reviewId)
	{
        $dbProduct = API_Db_Product::instance();
		$haveHelpSql = "select user_id from review_vote where rev_id=".$reviewId." order by user_id desc limit 40";
        $haveHelpArr = $dbProduct->getAll($haveHelpSql);
        $haveHelpAllUserInfo = array();
        if($haveHelpArr){
            #��������û�����Ϣ
            $haveHelpUserIdArr = array(); #�洢�û���ID
            $haveHelpUserIdArr_ = array(); #�洢�û���ID
            $haveHelpUserIdArr_t = array(); #�洢�û���ID
            foreach($haveHelpArr as $d){
                if ($d['user_id']) {
                    $haveHelpUserIdArr[] = $d['user_id'];
                }
            }
            $haveHelpAllUserInfo = API_Libs_Global_User::getMulUserInfo($haveHelpUserIdArr);
        }
        if (!$haveHelpAllUserInfo) {
            return FALSE;
        }
        foreach($haveHelpAllUserInfo as $id=>$vals){
            if($vals['face'] != "http://icon.zol-img.com.cn/photo/zoler_50.jpg"){
                $haveHelpUserIdArr_[$id]['face'] = $vals['face'];
                $haveHelpUserIdArr_[$id]['url'] = Libs_Global_Url::getMyUrl(array('userId'=>$vals['userid']));
                $haveHelpUserIdArr_[$id]['userId'] = $vals['userid'];
            }else{
                $haveHelpUserIdArr_t[$id]['face'] = "http://icon.zol-img.com.cn/photo/zoler_50.jpg";
                $haveHelpUserIdArr_t[$id]['url'] = Libs_Global_Url::getMyUrl(array('userId'=>$vals['userid']));
                $haveHelpUserIdArr_t[$id]['userId'] = $vals['userid'];
            }
        }
        
        return $haveHelpUserIdArr_+$haveHelpUserIdArr_t;
	}
}