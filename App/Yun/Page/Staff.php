<?php
/**
 * Ա���������
 *
 */
error_reporting(E_ALL);
ini_set("display_errors",1);
class  Yun_Page_Staff  extends Yun_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Staff';
        $output->permission = array(1);//ָ��Ȩ��
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}

    /**
     * ��������б�
     */
	public function doDefault(ZOL_Request $input, ZOL_Response $output){
		$wArr     = array();#�����ֶ�
		$whereSql = "";
		$page = (int)$input->get('page')<1?1:(int)$input->get('page');
		$output->sername = $wArr['name'] = $input->get('name');
        $output->sercateId = $wArr['cateId'] = $input->get('cateId');
        
	    if(!empty ($wArr)){
		    foreach($wArr as $k=>$v){
		        if(gettype($v) == 'string'){
                     $whereSql .= !empty($v)?' AND '.$k.' like binary "%'.$v.'%" ':'';
                  }else{
                     $whereSql .= !empty($v)?' AND '.$k.'='.$v:'';
                }    
		    }
		}
		$pageUrl  = "?c={$output->ctlName}&a={$output->actName}&page={$page}&name={$wArr['name']}&cateId={$wArr['cateId']}";
		$pageSize = 30;
		$orderSql = "order by id desc";
		
		$data = Helper_Dao::getList(array(
			'dbName'        => "Db_AndyouYun",  #���ݿ���
			'tblName'       => "staff",       #����
			'cols'          => "*",            #����
			'pageSize'      => $pageSize,      #ÿҳ����
			'page'          => $page,          #��ǰҳ
			'pageUrl'       => $pageUrl,       #ҳ��URL����
			'whereSql'      => $whereSql,       #where����
			'orderSql'      => $orderSql,
		    'iswrite'       =>  true,
		    'pageTpl'       =>  9,     #��ҳģ��
		    #'debug'        =>1
		));
		
		if($data){
		    $output->pageBar = $data['pageBar'];
		    $output->allCnt  = $data['allCnt'];
		    $output->data    = $data['data'];
			$output->pageUrl= $pageUrl;
		}
		
        $output->staffCate = Helper_Yun_Staff::getStaffCatePairs();
		$output->setTemplate('Staff');
	}
	
    /**
     * ��Ӽ�¼
     */
	public function doAddItem(ZOL_Request $input, ZOL_Response $output){
	                        
		$Arr['name'] = $input->post('name');
        $Arr['inDate'] = $input->post('inDate');
        $Arr['byear'] = (int)$input->post('byear');
        $Arr['bmonth'] = (int)$input->post('bmonth');
        $Arr['bday'] = (int)$input->post('bday');
        $Arr['ryear'] = (int)$input->post('ryear');
        $Arr['rmonth'] = (int)$input->post('rmonth');
        $Arr['rday'] = (int)$input->post('rday');
        $Arr['cateId'] = $input->post('cateId');
        $Arr['salary'] = $input->post('salary');
        $Arr['percentage'] = $input->post('percentage');
        
		$pageUrl = $input->request('pageUrl');
		$data = Helper_Dao::insertItem(array(
		        'addItem'       =>  $Arr, #������
		        'dbName'        =>  'Db_AndyouYun',    #���ݿ���
		        'tblName'       =>  'staff',    #����
		));
		/*backUrl*/
        $urlStr = $pageUrl ? $pageUrl : "?c={$output->ctlName}&t={$output->rnd}";
	    echo "<script>document.location='{$urlStr}';</script>";
		exit;
	}
    
    /**
     * ��������
     */
	 public function doUpItem(ZOL_Request $input, ZOL_Response $output){
	    $Arr = array();
	    
	    $input->request('name')?$Arr['name'] = $input->request('name'):'';
        $input->request('inDate')?$Arr['inDate'] = $input->request('inDate'):'';
        $input->request('byear')?$Arr['byear'] = (int)$input->request('byear'):'';
        $input->request('bmonth')?$Arr['bmonth'] = (int)$input->request('bmonth'):'';
        $input->request('bday')?$Arr['bday'] = (int)$input->request('bday'):'';
        $input->request('ryear')?$Arr['ryear'] = (int)$input->request('ryear'):'';
        $input->request('rmonth')?$Arr['rmonth'] = (int)$input->request('rmonth'):'';
        $input->request('rday')?$Arr['rday'] = (int)$input->request('rday'):'';
        $input->request('cateId')?$Arr['cateId'] = $input->request('cateId'):'';
        $input->request('salary')?$Arr['salary'] = $input->request('salary'):'';
        $input->request('percentage')?$Arr['percentage'] = $input->request('percentage'):'';
        
	    $pageUrl = $input->request('pageUrl');
	    $data = Helper_Dao::updateItem(array(
	            'editItem'       =>  $Arr, #������
	            'dbName'         =>  'Db_AndyouYun',    #���ݿ���
	            'tblName'        =>   'staff',    #����
	            'where'          =>   ' id='.$input->request('dataid'), #��������
	    ));
	    /*backUrl*/
        $urlStr = $pageUrl ? $pageUrl : "?c={$output->ctlName}&t={$output->rnd}";
	    echo "<script>document.location='{$urlStr}';</script>";
	    exit;
	 }
    /**
     * ɾ������
     */
	 public function doDelItem(ZOL_Request $input, ZOL_Response $output) {

		Helper_Dao::delItem(array(
                'dbName'=> 'Db_AndyouYun',#���ݿ���
                'tblName' => 'staff',#����
                'where'=> 'id='.$input->post('dataid'),#��������
        ));
		$pageUrl = $input->request('pageUrl');
		/*backUrl*/
        $urlStr = $pageUrl ? $pageUrl : "?c={$output->ctlName}&t={$output->rnd}";
	    echo "<script>document.location='{$urlStr}';</script>";
		exit;
	 }

	
    /**
     * ajax���ָ������
     */
	 public function doAjaxData(ZOL_Request $input, ZOL_Response $output) {
		$id = (int)$input->get('id');
		$arr = Helper_Dao::getRows(array(
		        'dbName'   => "Db_AndyouYun", #���ݿ���
		        'tblName'  => "staff", #����
		        'cols'     => "*", #����
		        'whereSql' =>  ' and id='.$id
		));
		$data = ZOL_String::convToU8($arr);
		if(isset($data[0])){
		  echo json_encode($data[0]);
		}
		exit();
	 }
	
}

