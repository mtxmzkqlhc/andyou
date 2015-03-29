<?php
/**
 * Form����Ajax����
 */
class StarAdmin_Page_Ajax_Select extends StarAdmin_Page_Abstract{

    public function __construct(){}

    
     /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Ajax_Select';
		$output->rnd      = SYSTEM_TIME;
		if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}

    /**
     * ��õ�����ԭ��select��Options
     */
    public function doAreaOptions(ZOL_Request $input, ZOL_Response $output){
         
         $type       = (int)$input->get("datatype");
         $sel        = (int)$input->get("sel"); #ѡ��״̬
         if($type == 0){//ʡ��
            echo Helper_Func_Form::getProvinceSelect(array('type'=>3,'sel'=>$sel));
         }elseif($type == 1){//����
            $provinceId = (int)$input->get("val");
            if($provinceId){
                echo Helper_Func_Form::getCitySelect(array('type'=>3,'provinceId'=>$provinceId,'sel'=>$sel));   
            }
         }
         exit;
    }   
    
    /**
     * ������������select��Options
     */
    public function doSubcateOptions(ZOL_Request $input, ZOL_Response $output){
         $manuId       = (int)$input->get("manuId");//Ʒ��ID
         $sel          = (int)$input->get("sel");//�Ѿ�ѡ��
         if($manuId){
            $dataArr = ZOL_Api::run("Pro.Manu.getSubcateOfManu" , array('manuId'=>$manuId));
            if($dataArr){
                $data = array();
                foreach ($dataArr as $d){
                    $data[$d["subcateId"]] = $d["name"];
                }
                echo Helper_Func_Form::transFirstLetterArr(array('data'=>$data,'type'=>3,'sel'=>$sel));
                exit;
            }
         }
    }
    
    
    /**
     * ��ÿ����ƹ�����������select��Options
     */
    public function doPrompSubcateOptions(ZOL_Request $input, ZOL_Response $output){
         $manuId       = (int)$input->get("manuId");//Ʒ��ID
         $sel          = (int)$input->get("sel");//�Ѿ�ѡ��
         if($manuId){
            $data = Helper_Pro_Pro::getSubcateCanPromotion(array('manuId'=>$manuId));
            if($data){
                echo Helper_Func_Form::transFirstLetterArr(array('data'=>$data,'type'=>3,'sel'=>$sel));
                exit;
            }
         }
    }
    
     /**
     * �������Ʒ�Ƶ�select��Options
     */
    public function doManuOptions(ZOL_Request $input, ZOL_Response $output){
         $subcateId    = (int)$input->get("subcateId");//Ʒ��ID
         if($input->exists("val") && $input->get("val")){
            $subcateId    = (int)$input->get("val");//����ID
         }
         $sel          = (int)$input->get("sel");//�Ѿ�ѡ��
         
         if($subcateId){
            $dataArr =  ZOL_Api::run("Pro.Manu.getList" , array('subcateId'=>$subcateId));
            if($dataArr){
                $data = array();
                foreach ($dataArr as $d){
                    $data[$d["id"]] = $d["name"];
                }
                echo Helper_Func_Form::transFirstLetterArr(array('data'=>$data,'type'=>3,'sel'=>$sel));
            }
         }
         
         exit;
    }
     /**
     * ������в�Ʒ��select��Options
     */
    public function doProOptions(ZOL_Request $input, ZOL_Response $output){
         $dataType     = (int)$input->get("dataType") ? (int)$input->get("dataType") : 1; #1 ��֪Ʒ�ƣ��������༶����Ʒ  2.��֪���࣬����Ʒ�Ƽ�����Ʒ
         if($dataType == 1 || $dataType == 0){##1 ��֪Ʒ�ƣ��������༶����Ʒ
             $manuId       = (int)$input->get("manuId");//Ʒ��ID
             $subcateId    = (int)$input->get("val");//����ID
             $sel          = (int)$input->get("sel");//�Ѿ�ѡ��
         }else{#2.��֪���࣬����Ʒ�Ƽ�����Ʒ
             $subcateId    = (int)$input->get("subcateId");//Ʒ��ID
             $manuId       = (int)$input->get("val");//����ID
             $sel          = (int)$input->get("sel");//�Ѿ�ѡ��             
         }
         $orderBy = (int)$input->get("order")  ? (int)$input->get("order") : 1;//����  1.���յ�� 2.���������� 3.������
         $noStop  = (int)$input->get("noStop") ? (int)$input->get("noStop") : 1;//�Ƿ��ų�ͣ��
         if($manuId && $subcateId){
            $dataArr = ZOL_Api::run("Pro.Product.getListByDb" , array('subcateId'=>$subcateId,'manuId'=>$manuId,'num'=>800,'orderBy'=>$orderBy,'noStop'=>$noStop));
            if($dataArr){
                $data = array();
                foreach ($dataArr as $d){
                    $data[$d["proId"]] = $d["name"];
                }
                echo Helper_Func_Form::transFirstLetterArr(array('data'=>$data,'type'=>3,'sel'=>$sel));
            }
         }
         
         exit;
    }
    
    /**
     * ��ʾ����Select����
     */
    public function doArea(ZOL_Request $input, ZOL_Response $output){
         $data       = $input->get("data");
         $type       = $input->get("datatype");
         if('all' == $type){
             $provinceId = (int)$data[0];
             $outStr = "[";
             $outStr .= Helper_Func_Form::getProvinceSelect(array('type'=>1));
             $outStr .= ",";
             $outStr .= Helper_Func_Form::getCitySelect(array('type'=>1,'provinceId'=>$provinceId));
             $outStr .= "]";
             
             echo $outStr;
             
         }else{
            if(!$data){
                echo Helper_Func_Form::getProvinceSelect(array('type'=>1));
            }elseif(isset($data[0])){
                $provinceId = (int)$data[0];
                echo Helper_Func_Form::getCitySelect(array('type'=>1,'provinceId'=>$provinceId));
            }
         }
         exit;
    }    
    
    
    /**
     * ��Ʒ�������Ϣ
     */
    public function doProCateDiv(ZOL_Request $input, ZOL_Response $output){
         echo Helper_Func_Form::getProCateSelect(array('type'=>2));
         exit;
    }
    
    /**
     * Ʒ��ѡ��
     */
    public function doSelectManu(ZOL_Request $input, ZOL_Response $output) {
        #ȡ������Ʒ������
        $dataArr = ZOL_Api::run("Pro.Manu.getListByDb", array());
        if ($dataArr) {
            foreach($dataArr as $key => $val){
                $manuName = ZOL_String::trimWhitespace($val['name']);
                $manuName = trim($manuName, '[');
                #�������ĸ
                $tfl      = API_Item_Base_String::getFirstLetter(array('input'=>$manuName));
                if (!isset($outArr[$tfl])) {
                    $outArr[$tfl] = array(
                        'name' => $tfl,
                        'cons' => array()
                    );
                }
                $outArr[$tfl]['cons'][] = $val;
            }
            sort($outArr);
            $output->outArr = $outArr;
        }
        $output->setTemplate('Ajax/SelectManu');
    }
    
     /**
     * �ֶ�����Ʒ��
     */
    public function doSearchManu(ZOL_Request $input, ZOL_Response $output) {
         #ȡ������Ʒ������
         $keyword  = htmlspecialchars($input->get('keyword'));
         $outStr = '';
            if ($keyword) {
                $dataArr = ZOL_Api::run("Pro.Manu.getListByDb", array('name'=>$keyword,'num'=>10));
                 if ($dataArr) {
                    foreach($dataArr as $key => $val){
                        $manuName = ZOL_String::trimWhitespace($val['name']);
                        $manuName = trim($manuName, '[');
                        #�������ĸ
                        $tfl      = API_Item_Base_String::getFirstLetter(array('input'=>$manuName));
                        if (!isset($outArr[$tfl])) {
                            $outArr[$tfl] = array(
                                'name' => $tfl,
                                'cons' => array()
                            );
                        }
                        $outArr[$tfl]['cons'][] = $val;
                    }
                    sort($outArr);
                    if ($outArr) {
                        foreach ($outArr as $k => $v) {
                            if ($v['name'] != '' && $v['name'] != ' ') {
                                if ($v['cons']) {
                                    foreach ($v['cons'] as $key => $val) {
                                        $outStr .= '<li><a href="javascript:;" rel="'.$val['id'].'">'.$val['name'].'</a></li>';
                                    }
                                }
                            }
                        }
                    }
                    echo iconv('gbk','utf-8//ignore', $outStr);
                }
            }
            exit;
    }
    
    /**
     * Ʒ������
     */
    public function doManuSuggestion(ZOL_Request $input, ZOL_Response $output) {
        $callback = $input->get('jsoncallback');
        $keyword  = htmlspecialchars($input->get('keyword'));
        if ($callback && $keyword) {
            $json = array();
            if ($keyword) {
                $dataArr = ZOL_Api::run("Pro.Manu.getListByDb", array('name'=>$keyword,'num'=>10));
                if ($dataArr) {
                    foreach ($dataArr as $key => $val) {
                        $json[] = '<font rel="'.$val['id'].'">'.iconv('gbk','utf-8',$val['name']).'</font>';
                    }
                }
                $json = array_unique($json);
            }
            $json = $callback . '(' . json_encode($json) . ')';
            echo $json;
        }
        exit;
    }
    
    /**
     * ��Ʒ��ѡ��
     */
    public function doSelectSubcate(ZOL_Request $input, ZOL_Response $output) {
        $manuId = (int)$input->get('manuId');
        $subcateIdStr = $input->get('subcateIdStr');
        if ($subcateIdStr) {
            $subcateIdArr = explode(',', $subcateIdStr);
        }
        #ȡ������Ʒ������
//        $dataArr = ZOL_Api::run("Pro.Cate.getSubcateByDb" , array(
//            'manuId'  => $manuId, #Ʒ��ID
//            'noSecond'=> 1
//        ));
        #����Դ��������
        $dataArr = Helper_Pro_Pro::getSubcateTemp(array());
        if ($dataArr) {
            foreach($dataArr as $key => $val){
                if (isset($subcateIdArr) && in_array($val['subcateId'], $subcateIdArr)) continue;
                $manuName = ZOL_String::trimWhitespace($val['name']);
                #�������ĸ
                $tfl      = API_Item_Base_String::getFirstLetter(array('input'=>$manuName));
                if (!isset($outArr[$tfl])) {
                    $outArr[$tfl] = array(
                        'name' => $tfl,
                        'cons' => array()
                    );
                }
                $outArr[$tfl]['cons'][] = $val;
            }
            sort($outArr);
            $output->outArr = $outArr;
        }
        $output->setTemplate('Ajax/SelectSubcate');
    }
    
    /**
     * ��Ʒ������
     */
    public function doSubcateSuggestion(ZOL_Request $input, ZOL_Response $output) {
        $callback = $input->get('jsoncallback');
        $keyword  = htmlspecialchars($input->get('keyword'));
        if ($callback && $keyword) {
            $json = array();
            if ($keyword) {
                $dataArr = ZOL_Api::run("Pro.Cate.getSubcateByDb", array('name'=>$keyword,'num'=>10,'noSecond'=> 1));
                if ($dataArr) {
                    foreach ($dataArr as $key => $val) {
                        $json[] = '<font rel="'.$val['subcateId'].'">'.iconv('gbk','utf-8',$val['name']).'</font>';
                    }
                }
                $json = array_unique($json);
            }
            $json = $callback . '(' . json_encode($json) . ')';
            echo $json;
        }
        exit;
    }
    
    /**
     * ���λƵ��
     * lvj 2014-4-9
     */
    public function doGetChannel(ZOL_Request $input, ZOL_Response $output){
         $sel = (int)$input->get('sel'); #ѡ��״̬
         echo Helper_Func_Form::getChannelSelect(array('type'=>3,'sel'=>$sel));
         exit ();
    }
    /**
     * ���λƵ���µ�ҳ������
     * lvj 2014-4-9
     */
    public function doGetPageType(ZOL_Request $input, ZOL_Response $output){
         $sel = (int)$input->get('sel'); #ѡ��״̬
         $channelId = (int)$input->get('val');
         echo Helper_Func_Form::getPageTypeSelect(array('type'=>3,'channelId'=>$channelId,'sel'=>$sel));
         exit ();
    }
    
        /**
     * �û�Ʒ��
     * lvj 2014-4-16
     */
    public function doGetUserManu(ZOL_Request $input, ZOL_Response $output){
         $sel    = (int)$input->get('sel'); #ѡ��״̬
         $userId = htmlspecialchars($input->get('userId'));
         echo Helper_Func_Form::getUserManuSelect(array('type'=>3,'userId'=>$userId,'sel'=>$sel));
         exit ();
    }     
    /**
     * ���λƵ��
     * lvj 2014-4-9
     */
    public function doGetUserSubcate(ZOL_Request $input, ZOL_Response $output){
         $sel    = (int)$input->get('sel'); #ѡ��״̬
         $userId = htmlspecialchars($input->get('userId'));
         $manuId = (int)$input->get('val');
         echo Helper_Func_Form::getUserSubcateSelect(array('type'=>3,'userId'=>$userId,'manuId'=>$manuId,'sel'=>$sel));
         exit ();
    } 
}


