<?php
/**
 * Form表单的Ajax数据
 */
class StarAdmin_Page_Ajax_Select extends StarAdmin_Page_Abstract{

    public function __construct(){}

    
     /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Ajax_Select';
		$output->rnd      = SYSTEM_TIME;
		if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}

    /**
     * 获得地区的原生select的Options
     */
    public function doAreaOptions(ZOL_Request $input, ZOL_Response $output){
         
         $type       = (int)$input->get("datatype");
         $sel        = (int)$input->get("sel"); #选中状态
         if($type == 0){//省份
            echo Helper_Func_Form::getProvinceSelect(array('type'=>3,'sel'=>$sel));
         }elseif($type == 1){//城市
            $provinceId = (int)$input->get("val");
            if($provinceId){
                echo Helper_Func_Form::getCitySelect(array('type'=>3,'provinceId'=>$provinceId,'sel'=>$sel));   
            }
         }
         exit;
    }   
    
    /**
     * 获得所有子类的select的Options
     */
    public function doSubcateOptions(ZOL_Request $input, ZOL_Response $output){
         $manuId       = (int)$input->get("manuId");//品牌ID
         $sel          = (int)$input->get("sel");//已经选中
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
     * 获得可以推广的所有子类的select的Options
     */
    public function doPrompSubcateOptions(ZOL_Request $input, ZOL_Response $output){
         $manuId       = (int)$input->get("manuId");//品牌ID
         $sel          = (int)$input->get("sel");//已经选中
         if($manuId){
            $data = Helper_Pro_Pro::getSubcateCanPromotion(array('manuId'=>$manuId));
            if($data){
                echo Helper_Func_Form::transFirstLetterArr(array('data'=>$data,'type'=>3,'sel'=>$sel));
                exit;
            }
         }
    }
    
     /**
     * 获得所有品牌的select的Options
     */
    public function doManuOptions(ZOL_Request $input, ZOL_Response $output){
         $subcateId    = (int)$input->get("subcateId");//品牌ID
         if($input->exists("val") && $input->get("val")){
            $subcateId    = (int)$input->get("val");//子类ID
         }
         $sel          = (int)$input->get("sel");//已经选中
         
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
     * 获得所有产品的select的Options
     */
    public function doProOptions(ZOL_Request $input, ZOL_Response $output){
         $dataType     = (int)$input->get("dataType") ? (int)$input->get("dataType") : 1; #1 已知品牌，根据子类级联产品  2.已知子类，根据品牌级联产品
         if($dataType == 1 || $dataType == 0){##1 已知品牌，根据子类级联产品
             $manuId       = (int)$input->get("manuId");//品牌ID
             $subcateId    = (int)$input->get("val");//子类ID
             $sel          = (int)$input->get("sel");//已经选中
         }else{#2.已知子类，根据品牌级联产品
             $subcateId    = (int)$input->get("subcateId");//品牌ID
             $manuId       = (int)$input->get("val");//子类ID
             $sel          = (int)$input->get("sel");//已经选中             
         }
         $orderBy = (int)$input->get("order")  ? (int)$input->get("order") : 1;//排序  1.按照点击 2.按名称排名 3.最新排
         $noStop  = (int)$input->get("noStop") ? (int)$input->get("noStop") : 1;//是否排除停产
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
     * 显示地区Select数据
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
     * 产品库分类信息
     */
    public function doProCateDiv(ZOL_Request $input, ZOL_Response $output){
         echo Helper_Func_Form::getProCateSelect(array('type'=>2));
         exit;
    }
    
    /**
     * 品牌选择
     */
    public function doSelectManu(ZOL_Request $input, ZOL_Response $output) {
        #取得所有品牌数据
        $dataArr = ZOL_Api::run("Pro.Manu.getListByDb", array());
        if ($dataArr) {
            foreach($dataArr as $key => $val){
                $manuName = ZOL_String::trimWhitespace($val['name']);
                $manuName = trim($manuName, '[');
                #获得首字母
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
     * 手动搜索品牌
     */
    public function doSearchManu(ZOL_Request $input, ZOL_Response $output) {
         #取得所有品牌数据
         $keyword  = htmlspecialchars($input->get('keyword'));
         $outStr = '';
            if ($keyword) {
                $dataArr = ZOL_Api::run("Pro.Manu.getListByDb", array('name'=>$keyword,'num'=>10));
                 if ($dataArr) {
                    foreach($dataArr as $key => $val){
                        $manuName = ZOL_String::trimWhitespace($val['name']);
                        $manuName = trim($manuName, '[');
                        #获得首字母
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
     * 品牌搜索
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
     * 产品线选择
     */
    public function doSelectSubcate(ZOL_Request $input, ZOL_Response $output) {
        $manuId = (int)$input->get('manuId');
        $subcateIdStr = $input->get('subcateIdStr');
        if ($subcateIdStr) {
            $subcateIdArr = explode(',', $subcateIdStr);
        }
        #取得所有品牌数据
//        $dataArr = ZOL_Api::run("Pro.Cate.getSubcateByDb" , array(
//            'manuId'  => $manuId, #品牌ID
//            'noSecond'=> 1
//        ));
        #数据源数组配置
        $dataArr = Helper_Pro_Pro::getSubcateTemp(array());
        if ($dataArr) {
            foreach($dataArr as $key => $val){
                if (isset($subcateIdArr) && in_array($val['subcateId'], $subcateIdArr)) continue;
                $manuName = ZOL_String::trimWhitespace($val['name']);
                #获得首字母
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
     * 产品线搜索
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
     * 广告位频道
     * lvj 2014-4-9
     */
    public function doGetChannel(ZOL_Request $input, ZOL_Response $output){
         $sel = (int)$input->get('sel'); #选中状态
         echo Helper_Func_Form::getChannelSelect(array('type'=>3,'sel'=>$sel));
         exit ();
    }
    /**
     * 广告位频道下的页面类型
     * lvj 2014-4-9
     */
    public function doGetPageType(ZOL_Request $input, ZOL_Response $output){
         $sel = (int)$input->get('sel'); #选中状态
         $channelId = (int)$input->get('val');
         echo Helper_Func_Form::getPageTypeSelect(array('type'=>3,'channelId'=>$channelId,'sel'=>$sel));
         exit ();
    }
    
        /**
     * 用户品牌
     * lvj 2014-4-16
     */
    public function doGetUserManu(ZOL_Request $input, ZOL_Response $output){
         $sel    = (int)$input->get('sel'); #选中状态
         $userId = htmlspecialchars($input->get('userId'));
         echo Helper_Func_Form::getUserManuSelect(array('type'=>3,'userId'=>$userId,'sel'=>$sel));
         exit ();
    }     
    /**
     * 广告位频道
     * lvj 2014-4-9
     */
    public function doGetUserSubcate(ZOL_Request $input, ZOL_Response $output){
         $sel    = (int)$input->get('sel'); #选中状态
         $userId = htmlspecialchars($input->get('userId'));
         $manuId = (int)$input->get('val');
         echo Helper_Func_Form::getUserSubcateSelect(array('type'=>3,'userId'=>$userId,'manuId'=>$manuId,'sel'=>$sel));
         exit ();
    } 
}


