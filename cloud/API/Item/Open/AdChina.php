<?php
/**
* �״�ý�ӿ�
* �ĵ���http://afpapi.adchina.com/
* ��ΰ��
* 2013-12-19
* log ������ 2014-01-03
*/

class API_Item_Open_AdChina{
    
    //�Ʒѷ�ʽ
    public static $_paymentTypeArr = array(
        'cpm' => 1,
        'cpc' => 2,
        'cpd' => 3,
    );
    
    //������ʽ
    public static $_operateArr = array(
        'add'  => 1, #����
        'edit' => 2, #�޸�
        'del'  => 3, #ɾ��
    );
    
    //��������
    public static $_creativeTypeArr = array(
        'html' => 1, #Html/Js
        'pic'  => 2, #ͼƬ
        'flash'=> 3, #Flash
    );

    //����ID
    public static $_orderId = 112;
    
    //վ��ID
    public static $_siteId = 52;

    /*
     * ������е�վ��
     */
	public static function getSite($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'num'        => 0, #��������
			'offset'     => 0,  #��ҳoffset
			'orderBy'    => 1,  #1 �������� 2 ����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        
        $whereArr = false;
        if($id){#�������id����ɸѡ
            $whereArr = array('Id'=>array('o'=>2,'v'=>$id)); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
            
        $data = self::postData(array(
            'url'        => "AdspaceService/GetSite",
			'postdata'   => self::createSelect(array(#POST����
                                                        'num'     => $num,
                                                        'offset'  => $offset,
                                                        'fild'    => array('Id','Name','Url','Description'),
                                                        'where'   => $whereArr,
                                                        'orderBy' => $orderArr
                                                      )
            ), 
        ));
        
        if($data){
            $data = api_json_decode($data,true);
            
            if($id && $data){#����ǰ���IDɸѡ����ֻ����һ��
                return $data[0];
            }else{
                return $data;
            }
            
        }
    }    
    
    /*
     * ����վ��
     */
	public static function mutateSite($paramArr) {
		$options = array(            
			'op'         => '', #add ���� edit �޸� del ɾ��
			'id'         => 0,  #վ��ID
			'name'       => '', #վ����
			'url'        => '', #URL
			'desc'       => '', #����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //��������
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        $item = array(
            'Id'          => $id ,
            'Name'        => $name,
            'Url'         => $url,
            'Description' => $desc
        );
        
        $json = '[{"Site":' . api_json_encode($item) . ',"Operator":' . $op . '}]';        
         
        $data = self::postData(array(
            'url'        => "AdspaceService.svc/MutateSite",
			'postdata'   => $json           
        ));
        
        if($data){
            $data =  api_json_decode($data,true);        
            if($data && $data['Value']){
                return $data['Value'][0];
            }
        }
    }
    
    /*
     * ������е�Ƶ��
     */
	public static function getChannel($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'siteId'     => self::$_siteId, #վ��ID
			'num'        => 10, #��������
			'offset'     => 0, #��ҳoffset
			'orderBy'    => 0, #1 �������� 2 ����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $isMore = false;
        if ($id && false !== strpos($id, ',')) {
            $isMore = true;
        }
        
        $orderArr = false;
        if($orderBy){
            $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        }
        
        $whereArr = array();
        if($id){#�������id����ɸѡ
            $whereArr['Id'] = array('o'=>0,'v'=>$id); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
        
        if($siteId){#����վ��ѡ��
            $whereArr['SiteId'] = array('o'=>2,'v'=>$siteId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
            
        $data = self::postData(array(
            'url'        => "AdspaceService.svc/GetChannel",
			'postdata'   => self::createSelect(array(#POST����
                                                        'num'     => $num,
                                                        'offset'  => $offset,
                                                        'fild'    => array('Id','Name','SiteId'),
                                                        'where'   => $whereArr,
                                                        'orderBy' => $orderArr
                                                      )
            ), 
        ));
        
        if($data){
            $data = api_json_decode($data,true);
            
            if($id && $data){#����ǰ���IDɸѡ����ֻ����һ��
                if ($isMore) {
                    return $data;
                } else {
                    return $data[0];
                }
            }else{
                return $data;
            }
            
        }
        
    }
    
    /*
     * ����Ƶ����Ϣ
     */
	public static function mutateChannel($paramArr) {
		$options = array(            
			'op'         => '', #add ���� edit �޸� del ɾ��
			'id'         => 0,  #վ��ID
			'name'       => '', #վ����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //��������
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        $item = array(
            'Id'          => $id ,
            'Name'        => $name,
            'SiteId'      => self::$_siteId,
        );
        if($op == 1)unset($item['Id']); #����Ǵ�����unsetId
        
        $json = '[{"Channel":' . api_json_encode($item) . ',"Operator":' . $op . '}]';        
         
        $data = self::postData(array(
            'url'        => "AdspaceService.svc/MutateChannel",
			'postdata'   => $json           
        ));
        
        if($data){
            $data =  api_json_decode($data,true);        
            if($data && $data['Value']){
                return $data['Value'][0];
            }
        }
    }
    
    /*
     * ���ҳ����Ϣ
     */
	public static function getPage($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'siteId'     => self::$_siteId, #վ��ID
			'channelId'  => 0, #Ƶ��ID
			'num'        => 0, #��������
			'offset'     => 0, #��ҳoffset
			'orderBy'    => 0, #1 �������� 2 ����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $orderArr = false;
        if($orderBy){
            $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        }
        
        $whereArr = array();
        if($id){#�������id����ɸѡ
            $whereArr['Id'] = array('o'=>2,'v'=>$id); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
        
        if($siteId){#����վ��ѡ��
            $whereArr['SiteId'] = array('o'=>2,'v'=>$siteId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
            
        if($channelId){#����Ƶ��ѡ��
            $whereArr['ChannelId'] = array('o'=>2,'v'=>$channelId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
        
        $data = self::postData(array(
            'url'        => "AdspaceService.svc/GetPage",
			'postdata'   => self::createSelect(array(#POST����
                                                        'num'     => $num,
                                                        'offset'  => $offset,
                                                        'fild'    => array('Id','Name','SiteId',"ChannelId"),
                                                        'where'   => $whereArr,
                                                        'orderBy' => $orderArr
                                                      )
            ), 
        ));
        
        if($data){
            $data = api_json_decode($data,true);
            
            if($id && $data){#����ǰ���IDɸѡ����ֻ����һ��
                return $data[0];
            }else{
                return $data;
            }
            
        }
        
    }
    
    /*
     * ����ҳ����Ϣ
     */
	public static function mutatPage($paramArr) {
		$options = array(            
			'op'         => '', #add ���� edit �޸� del ɾ��
			'id'         => 0,  #ҳ��ID
			'name'       => '', #ҳ����
			'channelId'  => 0,  #Ƶ��ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //��������
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        $item = array(
            'Id'          => $id ,
            'Name'        => $name,
            'SiteId'      => self::$_siteId,
            'ChannelId'   => $channelId,
        );
        
        $json = '[{"Page":' . api_json_encode($item) . ',"Operator":' . $op . '}]';        
         
        $data = self::postData(array(
            'url'        => "AdspaceService.svc/MutatePage",
			'postdata'   => $json           
        ));
        
        if($data){
            $data =  api_json_decode($data,true);        
            if($data && $data['Value']){
                return $data['Value'][0];
            }
        }
    }
    
    /*
     * ��ù��λ��Ϣ
     */
	public static function getAdspace($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'siteId'     => self::$_siteId, #վ��ID
			'channelId'  => 0, #Ƶ��ID
			'pageId'     => 0, #ҳ��ID
			'num'        => 0, #��������
			'offset'     => 0, #��ҳoffset
			'orderBy'    => 0, #1 �������� 2 ����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $isMore = false;
        if ($id && false !== strpos($id, ',')) {
            $isMore = true;
        }
        
        $orderArr = false;
        if($orderBy){
            $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        }
        
        $whereArr = array();
        if($id){#�������id����ɸѡ
            $whereArr['Id'] = array('o'=>0,'v'=>$id); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
        
        if($siteId){#����վ��ѡ��
            $whereArr['SiteId'] = array('o'=>2,'v'=>$siteId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
        if($channelId){#����Ƶ��ѡ��
            $whereArr['ChannelId'] = array('o'=>2,'v'=>$channelId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
        if($pageId){#����ҳ������ѡ��
            $whereArr['PageId'] = array('o'=>2,'v'=>$pageId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
            
        $data = self::postData(array(
            'url'        => "AdspaceService.svc/GetAdspace",
			'postdata'   => self::createSelect(array(#POST����
                                                        'num'     => $num,
                                                        'offset'  => $offset,
                                                        'fild'    => array('Id','Name','SiteId','ChannelId','PageId','AdspaceSize','AdFormat','EnabledExchange','DefaultCreatives'),
                                                        'where'   => $whereArr,
                                                        'orderBy' => $orderArr
                                                      )
            ), 
        ));
        
        if($data){
            $data = api_json_decode($data,true);
            
            if($id && $data){#����ǰ���IDɸѡ����ֻ����һ��
                if ($isMore) {
                    return $data;
                } else {
                    return $data[0];
                }
            }else{
                return $data;
            }
            
        }
        
    }
    
    /*
     * ���¹��λ��Ϣ
     */
	public static function mutateAdspace($paramArr) {
		$options = array(            
			'op'         => '', #add ���� edit �޸� del ɾ��
			'id'         => 0,  #���λID
			'name'       => '', #����
			'sizeWidth'  => 0,  #��
			'sizeHeight' => 0,  #��
			'channelId'  => 0,  #Ƶ��ID
			'pageId'     => 0,  #ҳ��ID
            'pvStatus'   => 1,  #������˽�н���ƽ̨ NoSet=0 Open=1 Close=-1
            'siteId'     => self::$_siteId, #վ��ID
            'defaultCode'   => false,  #�Ƿ�����Ĭ�Ϲ��
            'defaultCreative'   => false,  #Ĭ�ϴ���ID
            'adxPrice'   => false,  #adx�ĵ׼�
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //��������
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        $item = array( 
            'Id'          => $id ,
			'AdFormat'    => 1, #��ע
        );
        
        
        //�����
        if ($name) {
            $item['Name'] = $name;
        }
        
        //����վ��
        if ($siteId) {
            $item['SiteId'] = $siteId;
        }
        
        //������˽�н���ƽ̨
        if ($pvStatus) {
            $item['EnabledExchange'] = $pvStatus;
        }
        
        
        //Ƶ��
        if ($channelId) {
            $item['ChannelId'] = $channelId;
        }
        
        //ҳ��
        if ($pageId) {
            $item['PageId'] = $pageId;
        }
        
        //���
        if ($sizeWidth && $sizeHeight) {
            $item['AdspaceSize'] = array('Width'=>$sizeWidth,'Height'=>$sizeHeight);
        }
        //��˽���г��ĵ׼�
        if($adxPrice){
            $item['BuyPrice'] = $adxPrice;            
        }
        
        //�Ƿ���Ĭ�Ϲ��
        if($defaultCreative){
            $item['DefaultCreatives'] = array(
                array(
                    'Creative' => array(
                                            //'Name'         => $name."Ĭ�Ϲ��",
                                            '__type'       => "HtmlCreative:#Asp.Business",
                                            'Id'           => $defaultCreative,
                        
                                            //'AdFormat'     => self::$_creativeTypeArr['html'],
                                            //'HtmlContents' => $defaultCode,
                                        ),
                    'Weight'   => 1,
                    'Enabled'  => true,
                )
            );
        }
        
        $json = '[{"Adspace":' . api_json_encode($item) . ',"Operator":' . $op . '}]';
        
        $data = self::postData(array(
            'url'        => "AdspaceService/MutateAdspace",
			'postdata'   => $json           
        ));
        
        if($data){
            $data =  api_json_decode($data,true);        
            if($data && $data['Value']){
                return $data['Value'][0];
            }
        }
        
    }
    
    /*
     * ��ù����
     */
	public static function getAdvertiser($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'num'        => 0, #��������
			'offset'     => 0,  #��ҳoffset
			'orderBy'    => 1,  #1 �������� 2 ����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        
        $whereArr = false;
        if($id){#�������id����ɸѡ
            $whereArr = array('Id'=>array('o'=>2,'v'=>$id)); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
            
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/GetAdvertiser",
			'postdata'   => self::createSelect(array(#POST����
                                                        'num'     => $num,
                                                        'offset'  => $offset,
                                                        'fild'    => array('Id','Name'),
                                                        'where'   => $whereArr,
                                                        'orderBy' => $orderArr
                                                      )
            ), 
        ));
        
        if($data){
            $data = api_json_decode($data,true);
            
            if($id && $data){#����ǰ���IDɸѡ����ֻ����һ��
                return $data[0];
            }else{
                return $data;
            }
            
        }
    }    
    
    /*
     * ���¹������Ϣ
     */
	public static function mutateAdvertiser($paramArr) {
		$options = array(            
			'op'         => '', #add ���� edit �޸� del ɾ��
			'id'         => 0,  #ID
			'name'       => '', #�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //��������
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        $item = array(
            'Id'          => $id ,
            'Name'        => $name,
        );
        
        $json = '[{"Advertiser":' . api_json_encode($item) . ',"Operator":' . $op . '}]';        
         
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/MutateAdvertiser",
			'postdata'   => $json           
        ));
        
        if($data){
            $data =  api_json_decode($data,true);        
            if($data && $data['Value']){
                return $data['Value'][0];
            }
        }
    }
     
    /*
     * ��ö���
     */
	public static function getCampaign($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'num'        => 0, #��������
			'offset'     => 0,  #��ҳoffset
			'orderBy'    => 1,  #1 �������� 2 ����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        
        $whereArr = false;
        if($id){#�������id����ɸѡ
            $whereArr = array('Id'=>array('o'=>2,'v'=>$id)); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
            
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/GetCampaign",
			'postdata'   => self::createSelect(array(#POST����
                                                        'num'     => $num,
                                                        'offset'  => $offset,
                                                        'fild'    => array('Id','Name','ContractNo','Advertiser','StartTime','EndTime','Notes'),
                                                        'where'   => $whereArr,
                                                        'orderBy' => $orderArr
                                                      )
            ), 
        ));
        
        if($data){
            $data = api_json_decode($data,true);
            
            if($id && $data){#����ǰ���IDɸѡ����ֻ����һ��
                return $data[0];
            }else{
                return $data;
            }
            
        }
    }    
       
    /*
     * ���¶�����Ϣ
     */
	public static function mutateCampaign($paramArr) {
		$options = array(            
			'op'             => '', #add ���� edit �޸� del ɾ��
			'id'             => 0,  #ID
			'name'           => '', #������
			'contractNo'     => '', #��ͬ��
			'advertiserId'   => '', #�����ID
			'advertiserName' => '', #�������
			'startTime'      => '', #��ʼʱ��
			'endTime'        => '', #����ʱ��
			'notes'          => '', #��ע
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //��������
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        $item = array(
            'Id'          => $id ,
            'Name'        => $name,
			'ContractNo'  => $contractNo, #��ͬ��
			'Advertiser'  => array('Id'=>$advertiserId,'Name'=>$advertiserName), #�����
			'StartTime'   => $startTime, #��ʼʱ��
			'EndTime'     => $endTime, #����ʱ��
			'Notes'       => $notes, #��ע
        );
        
        $json = '[{"Campaign":' . api_json_encode($item) . ',"Operator":' . $op . '}]';        
         
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/MutateCampaign",
			'postdata'   => $json
        ));
        
        if($data){
            $data =  api_json_decode($data,true);        
            if($data && $data['Value']){
                return $data['Value'][0];
            }
        }
    }
    
    
    
     
    /*
     * ���Ĭ�Ϲ�洴��
     */
	public static function getDefaultCreative($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'num'        => 0, #��������
			'offset'     => 0,  #��ҳoffset
			'orderBy'    => 1,  #1 �������� 2 ����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $isMore = false;
        if ($id && false !== strpos($id, ',')) {
            $isMore = true;
        }
        
        $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        
        $whereArr = false;
        if($id){#�������id����ɸѡ
            $whereArr = array('Id'=>array('o'=>0,'v'=>$id)); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
            
        $data = self::postData(array(
            'url'        => "AdspaceService/GetDefaultCreative",
			'postdata'   => self::createSelect(array(#POST����
                                                        'num'     => $num,
                                                        'offset'  => $offset,
                                                        'fild'    => array('Id','Name','CampaignId'),
                                                        'where'   => $whereArr,
                                                        'orderBy' => $orderArr
                                                      )
            ), 
        ));
        
        if($data){
            $data = api_json_decode($data,true);
            
            if($id && $data){#����ǰ���IDɸѡ����ֻ����һ��
                if ($isMore) {
                    return $data;
                } else {
                    return $data[0];
                }
            }else{
                return $data;
            }
            
        }
    }    
    
    /*
     * ���¹�洴��
     */
	public static function mutateDefaultCreative($paramArr) {
		$options = array(            
			'op'               => '',  #add ���� edit �޸� del ɾ��
			'id'               => 0,  #ID
			'name'             => '', #�������� �����ظ�
			'campaignId'       => self::$_orderId, #��������Id
            'height'           => 0,  #��
            'width'            => 0,  #��
            'html'             => '', #����������
            'fileUrl'          => '', #�زĵ�ַ
            'clickUrl'         => '', #�����ַ
            'isTransparent'    => 0,  #flash�Ƿ�͸�� Ĭ��0-��͸�� 1-͸�� 2-����ģʽ
			'type'             => '', #������� html��Html/Js, pic��ͼƬ, flash��Flash
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //��������
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        //��������
        $item = array();
        
        $typeStr = '';
        //
        switch ($type) {
            case 'html':
                $typeStr = 'HtmlCreative';
                break;
            case 'pic':
                $typeStr = 'ImageCreative';
                break;
            case 'flash':
                $typeStr = 'FlashCreative';
                break;
            default :
                break;
        }
        
        $item['__type'] = $typeStr . ':#Asp.Business';
        
        $item['Id'] = $id;
        
        if ($name) { #����
            $item['Name'] = $name;
        }
        if ($campaignId) { #����id
            $item['CampaignId'] = $campaignId;
        }
        if ($type) { #�������
            $item['AdFormat'] = isset(self::$_creativeTypeArr[$type]) ? self::$_creativeTypeArr[$type] : '';
        }
        if ('flash' == $type) { #flash����
            $item['UrlType'] = 0;
            $item['Transparent'] = $isTransparent;
        }
        if ($html) { #html/js������
            $item['HtmlContents'] = $html;
        }
        if ($fileUrl) { #�زĵ�ַ
            $item['FileUrl'] = $fileUrl;
        }
        if ($clickUrl) { #�����ַ
            $item['ClickUrl'] = $clickUrl;
        }
        if ($width && $height) { #���
            $item['CreativeSize'] = array('Width'=>$width,'Height'=>$height);
        }
        
        $json = '[{"Creative":' . api_json_encode($item) . ',"Operator":' . $op . '}]';        
         
        $data = self::postData(array(
            'url'        => "AdspaceService/MutateDefaultCreative",
			'postdata'   => $json           
        ));
        
        if($data){
            $data =  api_json_decode($data,true);        
            if($data && $data['Value']){
                return $data['Value'][0];
            }
        }
    }
    
    
     
    /*
     * ��ù�洴��
     */
	public static function getCreative($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'num'        => 0, #��������
			'offset'     => 0,  #��ҳoffset
			'orderBy'    => 1,  #1 �������� 2 ����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $isMore = false;
        if ($id && false !== strpos($id, ',')) {
            $isMore = true;
        }
        
        $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        
        $whereArr = false;
        if($id){#�������id����ɸѡ
            $whereArr = array('Id'=>array('o'=>0,'v'=>$id)); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
            
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/GetCreative",
			'postdata'   => self::createSelect(array(#POST����
                                                        'num'     => $num,
                                                        'offset'  => $offset,
                                                        'fild'    => array('Id','Name','CampaignId'),
                                                        'where'   => $whereArr,
                                                        'orderBy' => $orderArr
                                                      )
            ), 
        ));
        
        if($data){
            $data = api_json_decode($data,true);
            
            if($id && $data){#����ǰ���IDɸѡ����ֻ����һ��
                if ($isMore) {
                    return $data;
                } else {
                    return $data[0];
                }
            }else{
                return $data;
            }
            
        }
    }    
    
    /*
     * ���¹�洴��
     */
	public static function mutateCreative($paramArr) {
		$options = array(            
			'op'               => '',  #add ���� edit �޸� del ɾ��
			'id'               => 0,  #ID
			'name'             => '', #�������� �����ظ�
			'campaignId'       => self::$_orderId, #��������Id
            'height'           => 0,  #��
            'width'            => 0,  #��
            'html'             => '', #����������
            'fileUrl'          => '', #�زĵ�ַ
            'clickUrl'         => '', #�����ַ
            'isTransparent'    => 0,  #flash�Ƿ�͸�� Ĭ��0-��͸�� 1-͸�� 2-����ģʽ
			'type'             => '', #������� html��Html/Js, pic��ͼƬ, flash��Flash
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //��������
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        //��������
        $item = array();
        
        $typeStr = '';
        //
        switch ($type) {
            case 'html':
                $typeStr = 'HtmlCreative';
                break;
            case 'pic':
                $typeStr = 'ImageCreative';
                break;
            case 'flash':
                $typeStr = 'FlashCreative';
                break;
            default :
                break;
        }
        
        $item['__type'] = $typeStr . ':#Asp.Business';
        
        $item['Id'] = $id;
        
        if ($name) { #����
            $item['Name'] = $name;
        }
        if ($campaignId) { #����id
            $item['CampaignId'] = $campaignId;
        }
        if ($type) { #�������
            $item['AdFormat'] = isset(self::$_creativeTypeArr[$type]) ? self::$_creativeTypeArr[$type] : '';
        }
        if ('flash' == $type) { #flash����
            $item['UrlType'] = 0;
            $item['Transparent'] = $isTransparent;
        }
        if ($html) { #html/js������
            $item['HtmlContents'] = $html;
        }
        if ($fileUrl) { #�زĵ�ַ
            $item['FileUrl'] = $fileUrl;
        }
        if ($clickUrl) { #�����ַ
            $item['ClickUrl'] = $clickUrl;
        }
        if ($width && $height) { #���
            $item['CreativeSize'] = array('Width'=>$width,'Height'=>$height);
        }
        
        $json = '[{"Creative":' . api_json_encode($item) . ',"Operator":' . $op . '}]';        
         
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/MutateCreative",
			'postdata'   => $json           
        ));
        
        if($data){
            $data =  api_json_decode($data,true);        
            if($data && $data['Value']){
                return $data['Value'][0];
            }
        }
    }
    
    /*
     * ���Ͷ���б���Ϣ
     */
	public static function getMediabuy($paramArr) {
		$options = array(            
			'id'         => 0,  #ID
			//'name'       => '', #Ͷ������
			//'orderId'    => 0,  #��������ID
            'paymentType'=> 0,  #�Ʒѷ�ʽ 1-CPM 2-CPC 3-CPD
            //'chargeType' => 0,  #Ͷ�ŷ�ʽ 1-��չ�� 2-����� 3-������
            //'startTime'  => '', #Ͷ�����ڿ�ʼ
            //'endTime'    => '', #Ͷ�����ڽ���
            //'placeId'    => 0,  #Ͷ�ŵĹ��λID
            //'adspaceId'  => 0,  #Ͷ�Ŵ���ID
            'status'     => 0,  #Ͷ��״̬ 1-���� 3-��ͣ 4-�ݸ�
            'num'        => 0,  #��������
			'offset'     => 0,  #��ҳoffset
			'orderBy'    => 0,  #���� 1-�������� 2-����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $isMore = false;
        if ($id && false != strpos($id, ',')) {
            $isMore = true;
        }
        
        $orderArr = false;
        if($orderBy){
            $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        }
        
        $whereArr = array();
        //Ĭ�϶���
        $whereArr['CampaignId'] = array('o'=>2,'v'=>self::$_orderId);
        if($id){#�������id����ɸѡ
            $whereArr['Id'] = array('o'=>0,'v'=>$id); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
        //if($orderId){#���ն���ѡ��
        //    $whereArr['CampaignId'] = array('o'=>2,'v'=>$orderId);
        //}
        if($paymentType && isset(self::$_paymentTypeArr[$paymentType])){#���ռƷѷ�ʽѡ��
            $whereArr['Payment'] = array('o'=>2,'v'=>self::$_paymentTypeArr[$paymentType]);
        }
        //if($chargeType){#����Ͷ�ŷ�ʽѡ��
        //    $whereArr['ChargeType'] = array('o'=>2,'v'=>$chargeType);
        //}
        if($status){#����Ͷ��״̬ѡ��
            $whereArr['Status'] = array('o'=>2,'v'=>$status);
        }
        
        #�Ż�������
        $fildArr = array(
            'Id','Name','CampaignId','Chargeable','Payment','ChargeType'
            ,'AdspaceMediabuys','PartialDates','AdspaceMediabuyCreatives','Priority'
            ,'Budget','Frequency','Targets','Status'
        );
        
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/GetMediabuy",
			'postdata'   => self::createSelect(array(#POST����
                                                        'num'     => $num,
                                                        'offset'  => $offset,
                                                        'fild'    => $fildArr,
                                                        'where'   => $whereArr,
                                                        'orderBy' => $orderArr
                                                      )
            ), 
        ));
        
        if($data){
            $data = api_json_decode($data,true);
            
            if($id && $data){
                if ($isMore) {
                    return $data;
                } else {#����ǰ���IDɸѡ����ֻ����һ��
                    return $data[0];
                }
            }else{
                return $data;
            }
        }
    }
     
    /*
     * ����Ͷ����Ϣ
     */
	public static function mutateMediabuy($paramArr) {
		$options = array(            
			'op'            => '',       #add ���� edit �޸� del ɾ��
			'id'            => 0,        #ID
			'name'          => '',       #Ͷ������ �����ظ�
			'orderId'       => self::$_orderId,   #��������ID
			'price'         => 0,        #�����۸�
            'paymentType'   => '',       #�Ʒѷ�ʽ 1-CPM 2-CPC 3-CPD
            'dateArr'       => array(),  #Ͷ������ ��ά���� array(array('StartTime','EndTime'),)
            'target'        => '',       #�������� �������� '����,����,...'
            'placeIdArr'    => array(),  #Ͷ�ŵĹ��λ��Ϣ array('id'=>1,'id'=>0)
            'adspaceIdArr'  => array(),  #Ͷ�ŵĴ�����Ϣ array('id'=>1,'id'=>0)
            'status'        => 0,        #Ͷ��״̬ 1-���� 3-��ͣ 4-�ݸ�
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //��������
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        //�Ʒѷ�ʽ
        if ($paymentType && isset(self::$_paymentTypeArr[$paymentType])) {
            $paymentType = self::$_paymentTypeArr[$paymentType];
        }
        
        //�ж��Ƿ�Ϊ������ͣͶ��
        $isPause = (1 == $op && 3 == $status) ? 1 : 0;
        
        $item = array(
            'Id'          => $id ,
            //'Frequency'   => array('Enabled'=>FALSE, $frequencyArr),
            //'Budget'      => $budgetArr, #Type 0-������(ֻ������CPM,CPCͶ��) 1-����(ֻ������CPM,CPCͶ��) 2-������(ֻ������CPDͶ��)
        );
        if ($name) { #Ͷ������
            $item['Name'] = $name;
        }
        if ($orderId) { #����id
            $item['CampaignId'] = $orderId;
        }
        if ($price) { #Ͷ�Ž��
            $item['Chargeable'] = $price;
        }
        if ($paymentType) { #Ͷ�żƷѷ�ʽ
            $item['Payment'] = $paymentType;
        }
        if ($status) { #Ͷ��״̬
            $item['Status'] = $isPause ? 1 : $status;
        }
        
        //�Ʒѷ�ʽ����Ĭ��
        $chargeType = false; #Ͷ�ŷ�ʽ 1-��չ�� 2-����� 3-������
        $priority = false; #���ȼ�
        $budgetArr = array(); #�ƻ�Ͷ����
        if ($paymentType) {
            switch ($paymentType) {
                case 1: #CPM
                    $chargeType = 1; #Ͷ�ŷ�ʽ ��չ��
                    $priority = 8; #���ȼ� 8Ĭ��
                    $budgetArr = array('Type'=>0,'Count'=>0);
                    break;
                case 2: #CPC
                    $chargeType = 2; #Ͷ�ŷ�ʽ �����
                    $priority = 8; #���ȼ� 8Ĭ��
                    $budgetArr = array('Type'=>0,'Count'=>0);
                    break;
                case 3: #CPD
                    $chargeType = 3; #Ͷ�ŷ�ʽ ������
                    $priority = 0; #���ȼ�
                    $budgetArr = array('Type'=>2,'Count'=>100);
                    break;
                default :
                    break;
            }
        }
        
        if ($chargeType) { #Ͷ�ŷ�ʽ
            $item['ChargeType'] = $chargeType;
        }
        if ($priority) { #���ȼ�
            $item['Priority'] = $priority;
        }
        if ($budgetArr) { #�ƻ�Ͷ����
            $item['Budget'] = $budgetArr;
        }
        if ($dateArr) { #����
            $tempDateArr = array();
            foreach ($dateArr as $row) {
                $tempDateArr[] = array(
                    'StartTime' => $row[0] . ' 00:00:00',
                    'EndTime' => $row[1] . ' 23:59:59'
                );
            }
            $item['PartialDates'] = $tempDateArr;
        }
        if ($target) { #����
            $item['Targets'] = array(
                array(
                    '__type'=>'RegionTarget:#Asp.Business',
                    'Type'=>1,
                    'Enabled'=>'true',
                    'Value'=>$target,
                    'IsInclude'=>'true'
                    )
            );
        } else {
            if (2 == $op) {
                $item['Targets'] = array(
                    array(
                        '__type'=>'RegionTarget:#Asp.Business',
                        'Type'=>1,
                        'Enabled'=>'false',
                        )
                );
            }
        }
        if ($placeIdArr) { #���λ��Ϣ
            $tempArr = array();
            $i = 0;
            foreach ($placeIdArr as $id=>$enabled) {
                $tempArr[$i]['Adspace']['Id'] = $id;
                $tempArr[$i]['Enabled'] = $enabled ? 'true' : 'false';;
                $i++;
            }
            $item['AdspaceMediabuys'] = $tempArr;
        }
        if ($adspaceIdArr) { #��ϴ�����Ϣ
            #��ȡ������Ϣ
            $tempIdArr = array_keys($adspaceIdArr);
            $isMoreId = 1 == count($tempIdArr) ? 0 : 1;
            $adspaceArr = self::getCreative(array('id' => implode(',', $tempIdArr)));
            $creativeTypeArr = array();
            if ($isMoreId) {
                foreach ($adspaceArr as $row) {
                    $creativeTypeArr[$row['Id']] = $row['__type'];
                }
            } else {
                $creativeTypeArr[$adspaceArr['Id']] = $adspaceArr['__type'];
            }
            
            $tempArr = array();
            $i = 0;
            foreach ($adspaceIdArr as $id=>$enabled) {
                $tempArr[$i]['Creative']['__type'] = isset($creativeTypeArr[$id]) ? $creativeTypeArr[$id] : 'HtmlCreative:#Asp.Business';
                $tempArr[$i]['Creative']['Id'] = $id;
                $tempArr[$i]['Enabled'] = $enabled ? 'true' : 'false';
                $i++;
            }
            $item['AdspaceMediabuyCreatives'] = $tempArr;
        }
        /*if ($placeId) { #���λ��Ϣ
            $tempArr = array();
            $placeIdArr = explode(',', $placeId);
            foreach ($placeIdArr as $key=>$id) {
                $tempArr[$key]['Adspace']['Id'] = $id;
                $tempArr[$key]['Enabled'] = 'true';
            }
            $item['AdspaceMediabuys'] = $tempArr;
        } 
        if ($adspaceId) {
            //��ϴ�����Ϣ
            $tempArr = array();
            $adspaceIdArr = explode(',', $adspaceId);
            foreach ($adspaceIdArr as $key=>$id) {
                $tempArr[$key]['Creative']['__type'] = 'HtmlCreative:#Asp.Business';
                $tempArr[$key]['Creative']['Id'] = $id;
                $tempArr[$key]['Enabled'] = 'true';
            }
            $item['AdspaceMediabuyCreatives'] = $tempArr;
        }*/
        //if ($frequencyArr) { #Ͷ��Ƶ��
        //    $item['Frequency'] = array('Enabled'=>FALSE, $frequencyArr);
        //}
        
        $json = '[{"Mediabuy":' . api_json_encode($item) . ',"Operator":' . $op . '}]';        
         
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/MutateMediabuy",
			'postdata'   => $json           
        ));
        if($data){
            $data =  api_json_decode($data,true);   
            if(!empty($data['PartialFailureErrors'])){
                API_Libs_Global_Log::set(array(
                       'module'   =>'adChinaPlace',    
                       'content'  =>array('#I#'=> is_array($options) ? api_json_encode($options):'','#P#'=>$json,'#E#'=>api_json_encode($data['PartialFailureErrors']))
                ));
            }
            if($data && $data['Value']){
                $retArr = $data['Value'][0];
                
                //������ͣ�ƹ�ʱ
                if ($isPause) {
                    if (isset($retArr['Id'])) {
                        $editData = self::mutateMediabuy(array(
                            'op' => 'edit',
                            'id' => $retArr['Id'],
                            'status' => 3,
                        ));
                        if ($editData) {
                            $retArr['Status'] = $editData['Status'];
                        }
                    }
                }
                
                return $retArr;
            }
        }else{
            API_Libs_Global_Log::set(array(
                       'module'   =>'adChinaPlace',    
                       'content'  =>array('#I#'=> is_array($options) ? api_json_encode($options):'','#P#'=>$json,'#E#'=>api_json_encode($data['PartialFailureErrors']))
            ));     
        }
    }
    
    /*
     * ���Ч��������Ϣ
     */
	public static function getReport($paramArr) {
		$options = array(            
			//'dimension'=> '1,7',  #ά��
			//'metric'   => '2,3,4,5',  #ָ�� 1-չʾ�� 2-����� 3-PV 4-UV 5-���UV
            'startTime'   => '',    #��ʼʱ��
            'endTime'     => '',    #����ʱ��
            'hour'        => '',    #Сʱ
            'num'         => 0,     #��������
			'offset'      => 0,     #��ҳoffset
            'orderField'  => '',    #�����ֶ�
			'orderBy'     => 0,     #���� 1-�������� 2-����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $whereArr = array();
        //if($dimension){#����ά��ѡ��
        //    $whereArr[] = '"Dimension":['.$dimension.']';           
        //}
        //if($metric){#����ָ��ѡ��
        //    $whereArr[] = '"Metric":['.$metric.']';
        //}
        $dateStyle = 7;#Сʱ
        if ($startTime && $endTime) {#ʱ�䷶Χѡ��
            if ('' !== $hour) {
                $dateStyle = 19;#����
                
                $hour = (int)$hour;
                if (10 > $hour) {
                    $hour = '0' . $hour;
                }
                //$next_hour = date('H',  strtotime('+1 hours', strtotime($startTime.' '.$hour.':00:00')));
                
                $startTime = $startTime.' '.$hour.':00:00';
                $endTime = $endTime.' '.$hour.':59:59';
            }
            $whereArr[] = '"DateRange":{"End":"'.$endTime.'","Start":"'.$startTime.'"}';
        }
        if ($num || $offset) {#��ҳ��Χ
            $whereArr[] = '"Paging":{"RowsNumber":'.$num.',"StartIndex":'.$offset.'}';
        }
        if ($orderField) {#����
            $whereArr[] = '"Orderings":{"Field":"'.$orderField.'","SortOrder":"'.($orderBy == 1 ? 'DESC' : 'ASC').'"}';
        }
        
        $data = false;
        if ($whereArr) {
            $postData = '{"Dimension":[5,'.$dateStyle.'],"Metric":[2,3],'.implode(',', $whereArr).'}';
        
            $data = self::postData(array(
                'url'        => "ReportService.svc/Get",
                'postdata'   => $postData 
            ));
        }
        
        if($data){
            $data = api_json_decode($data,true);
            
            $outArr = array();
            if ($data) {
                $dataValueArr = $data['Rows'];
                if ($dataValueArr) {
                    foreach ($dataValueArr as $row) {
                        $mediabuyId = $row[0];
                        $dateArr = explode(' ', $row[1]);
                        $hour = $dateArr[1];
                        $outArr[$mediabuyId][$hour] = array(
                            'clickPv' => $row[2], #���pv
                            'impPv'   => $row[3], #pv
                            'clickUv' => 0,       #���uv
                            'impUv'   => 0,       #uv
                        );
                    }
                }
            }
            
            return $outArr;
        }
    }
    
    /**
     * ����select��ɸѡ����
     */
    private static function createSelect($paramArr) {
		$options = array(
			'num'        => '', #��������
			'offset'     => 0,  #��ҳoffset
			'fild'       => false,  #��ҳoffset
            'where'      => false,  #ɸѡ����
			'orderBy'    => false,  #����ʽ key => DESC|ASC  ����ʽ
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $selectArr = array();
        #��ҳ����
        if($num || $offset){
            $selectArr[] = '"Paging":{"RowsNumber":'.$num.',"StartIndex":'.$offset.'}';
        }
        #�����ֶβ���
        if($fild){
            $selectArr[] = '"SelectFields":["'.implode('","', $fild).'"]';
        }
        #���򲿷�  "Orderings":{[{"Field":"Id","SortOrder":1}]}}
        if($orderBy){
            $tmpStr = $comma = '';
            foreach($orderBy as $k => $v){
                $tmpStr .= $comma . "{\"Field\":\"{$k}\",\"SortOrder\":".($v == 'ASC' ? 1 : 2)."}";
                $comma   = ",";
            }
            $selectArr[] = '"Orderings":['.$tmpStr.']';
        }
        #ɸѡ����
        if($where){        
            $tmpStr = $comma = '';    
            foreach($where as $k => $v){                
                $tmpStr .= $comma . "{\"Field\":\"{$k}\",\"Operator\":".$v["o"].",\"Values\":\"".$v["v"]."\"}";
                $comma   = ",";
            }
            $selectArr[] = '"Predicates":['.$tmpStr.']';
        }
        return "{" . ($selectArr ? implode(",", $selectArr) : "") . "}";
    }
    
    /**
     * ���snoopy����
     */
    private static $snoopyObj = null;
    private static function getSnoopyObj(){
        if(!self::$snoopyObj){
            require_once ZOL_API_ROOT . '/Libs/FetchHtml/Snoopy.php';
        }
        self::$snoopyObj = new Snoopy();
    }

    /**
     * �ӿ�����
     */
	public static function postData($paramArr) {
		$options = array(
			'url'             => '', #�� AdspaceService/GetSite
			'postdata'        => '', #POST����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $customerId = 2;
        $authToken  = "VsvEbkySGnqAdvFH7CHJkK";
        //$baseUrl = "http://10.15.184.249/RestService/v2014032/";
        $baseUrl = "http://10.15.184.249/RestService/201404/";//����Զ���Ĭ�Ϲ�棻 ���λ����private Ad exchangeʱ�����Ĭ�ϵͼۣ�
        //$customerId = 1866;
        //$authToken  = "62khx3YhWb7m5bj/eNfefe";
        //$baseUrl = "http://testapi.adchina.com/RestService/v2014012/";
        
        #echo  $url . "<br/>\n";
        //echo $baseUrl . $url . "<br/>";
        //echo $postdata . "\n";
        //$postdata = mb_convert_encoding($postdata, "UTF-8", "GBK" );
        
        self::getSnoopyObj();
        
        self::$snoopyObj->rawheaders["Accept"]            = "text/json";
        self::$snoopyObj->rawheaders["Content-Type"]      = "text/json";
        self::$snoopyObj->rawheaders["ContentType"]       = "text/json";
        self::$snoopyObj->rawheaders["CustomerId"]        = $customerId;
        self::$snoopyObj->rawheaders["AuthToken"]         = $authToken;
        self::$snoopyObj->rawheaders["Content-Length"]    = strlen($postdata);
        self::$snoopyObj->_submit_type                    = "text/json";
        self::$snoopyObj->read_timeout                    = 10;
        self::$snoopyObj->submit($baseUrl . $url,"","",$postdata);
        
        #echo $baseUrl . $url."<br/>";
        $content =  self::$snoopyObj->results;
        #echo "<br/>".$content."<br/>\n";
        if(!in_array(substr($content, 0,1),array("[","{"))){#������ص����ݲ����ϸ�ʽ��ǿ��Ϊ��
            return false;
        }
        if($content){
           #$content = mb_convert_encoding($content, "GBK", "UTF-8" );
        }
        
        return $content;       
        
            
    }

}
