<?php
/**
 * Form����غ���
 * ��ΰ��
 * 2013-11-27
 */
class Helper_Func_Form extends Helper_Abstract {
    
    
    /**
     * ���ʡ����Ϣ����װ��select�����ʽ
     */
    public static function getProvinceSelect($paramArr) {
		$options = array(
			'type'       =>  1,    #select���� 0:����ԭʼ�������� 1:Ĭ��select 2:div��ʽ  3,select��ԭ��option
            'sel'        =>  false,#ѡ���Ԫ��
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        #���ʡ����Ϣ
        $data = Helper_Func_Data::getProvince(array());
        if($type == 0){
            return $data;
        }
        #��װ����
        return self::transFirstLetterArr(array('data'=>$data,'type'=>$type,'sel'=>$sel));
    }
    
     /**
     * ���ʡ����Ϣ����װ��select�����ʽ
     */
    public static function getCitySelect($paramArr) {
		$options = array(
			'provinceId'       =>  0,    #����ID
			'type'             =>  1,    #select���� 1:Ĭ��select 2:div��ʽ 
            'sel'              =>  false,#ѡ���Ԫ��
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        #��ó�����Ϣ
        if($provinceId){
            $data = API_Item_Pro_Area::getCityInfo(array('provinceId'=>$provinceId));
        }
        if(!$provinceId || !$data){
            $data = array(0=>'��ѡ��');
        }
        #��װ����
        return self::transFirstLetterArr(array('data'=>$data,'type'=>$type,'sel'=>$sel));
    }
    
    
     /**
     * ��ò�Ʒ������Ϣ����װ��select�����ʽ
     */
    public static function getProCateSelect($paramArr) {
		$options = array(
			'type'       =>  1,    #select���� 1:Ĭ��select 2:div��ʽ  4:ԭ��̬select��������ĸ�����
			'sel'        =>  false,      #ѡ��״̬
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        #��ó�����Ϣ
        $data = API_Item_Pro_Cate::getSubCate(array('showHiden'=>0));
        $outArr = array();
        if($data){
            foreach($data as $k => $d){
                $outArr[$k] = trim($d["name"]);
            }
        }
        #��װ����
        return self::transFirstLetterArr(array('data'=>$outArr,'type'=>$type,'sel'=>$sel));
    }
    
    /**
     * ��ù��Ƶ����Ϣ����װselect����ʽ
     * lvj 2014-4-9
     */
    public static function getChannelSelect($paramArr) {
        $options = array(
                'type'       =>  1,    #select���� 1:Ĭ��select 2:div��ʽ  4:ԭ��̬select��������ĸ�����
                'sel'        =>  false,      #ѡ��״̬
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        #Ƶ����Ϣ
        $data = Helper_Advertise_Adspace::getChannelInfo();
        $outArr = array();
        if($data){
            foreach($data as $k => $d){
                $outArr[$k] = trim($d);
            }
        }
        #��װ����
        return self::transFirstLetterArr(array('data'=>$outArr,'type'=>$type,'sel'=>$sel));       
    }
    /**
     * ��ù��Ƶ����ҳ��������Ϣ����װselect����ʽ
     * lvj 2014-4-9
     */
    public static function getPageTypeSelect($paramArr) {
        $options = array(
                'type'      =>  1,    #select���� 1:Ĭ��select 2:div��ʽ  4:ԭ��̬select��������ĸ�����
                'sel'       =>  false,      #ѡ��״̬
                'channelId' =>  0,  #Ƶ��ID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        #Ƶ����ҳ��������Ϣ
        $data = Helper_Advertise_Adspace::getPageTypeInfo(array('channelId'=>$channelId));
        $outArr = array();
        if($data){
            foreach($data as $k => $d){
                $outArr[$k] = trim($d);
            }
        }
        #��װ����
        return self::transFirstLetterArr(array('data'=>$outArr,'type'=>$type,'sel'=>$sel));       
    }
    /**
     * ��ȡ�û�Ʒ����Ϣ����װselect����ʽ
     * lvj 2014-4-16
     */
    public static function getUserManuSelect($paramArr) {
        $options = array(
                'type'      =>  1,    #select���� 1:Ĭ��select 2:div��ʽ  4:ԭ��̬select��������ĸ�����
                'userId'    => '',
                'sel'       =>  false,      #ѡ��״̬
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        #Ƶ����ҳ��������Ϣ
        $data = Helper_User_Info::getUserManu(array(
            'userId'    => $userId,
        ));   
        $outArr = array();

        if($data){
            foreach($data as $k => $d){
                $outArr[$d['manuId']] = trim($d['manuName']);
            }
        }
        #��װ����
        return self::transFirstLetterArr(array('data'=>$outArr,'type'=>$type,'sel'=>$sel));       
    }    
    /**
     * ��ȡĳƷ���µ��û���Ʒ����Ϣ����װselect����ʽ
     * lvj 2014-4-16
     */
    public static function getUserSubcateSelect($paramArr) {
        $options = array(
                'type'      =>  1,    #select���� 1:Ĭ��select 2:div��ʽ  4:ԭ��̬select��������ĸ�����
                'userId'    => '',
                'manuId'    => 0,
                'sel'       =>  false,      #ѡ��״̬
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        #Ƶ����ҳ��������Ϣ
        $data = Helper_Pro_Pro::getManuSetSubcate(array(
                    'manuId'      => $manuId,    #where����
                    'userId'      => $userId,
                ));
        $outArr = array();
        if($data){
            foreach($data as $k => $d){
                $outArr[$d['subcateId']] = trim($d['subcateName']);
            }
        }
        #��װ����
        return self::transFirstLetterArr(array('data'=>$outArr,'type'=>$type,'sel'=>$sel));       
    }    
    /**
     * ������װ��������ĸ��װ������
     */
    public static function transFirstLetterArr($paramArr) {
		$options = array(
			'data'       =>  array(),    #����
			'type'       =>  1,          #select���� 1:Ĭ��select 2:div��ʽ 
			'sel'        =>  false,      #ѡ��״̬
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $outArr = array();
        
        if($data){
            
            if($type == 1){#1:Ĭ��select ��ʽ
                return api_json_encode($data);
            }elseif($type == 3){ #ԭ��̬select��option
                $out = '';                
                foreach($data as $k => $wd){
                    $seled = ($sel == $k) ? "selected" : "";
                    $out .= "<option value='{$k}' {$seled}>{$wd}</option>";
                }                
                return mb_convert_encoding($out, "UTF-8","GBK");
                
            }elseif($type == 2){ #div select ��ʽ
                foreach($data as $k => $wd){
                    $l = API_Item_Base_String::getFirstLetter(array('input'=>$wd)); #�������ĸ
                    if(!isset($outArr[$l])){
                        $outArr[$l] = array(
                            'name' => $l,
                            'cons' => array()
                        );
                    }
                    $outArr[$l]['cons'][] = $wd;
                }
                //var_dump($outArr);
                sort($outArr);
                return api_json_encode($outArr);
            }elseif($type == 4){#ԭ��̬select��������ĸ�����
                foreach($data as $k => $wd){
                    $l = API_Item_Base_String::getFirstLetter(array('input'=>$wd)); #�������ĸ
                    $outArr[$k] = $l."_".$wd;
                }
                asort($outArr);
                $out = '';                
                foreach($outArr as $k => $wd){
                    $seled = ($sel == $k) ? "selected" : "";
                    $out .= "<option value='{$k}' {$seled}>{$wd}</option>";
                    
                }
                return $out;
                
            }
        }
        return '';        
        
    }
    
}
