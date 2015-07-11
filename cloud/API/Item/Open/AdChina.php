<?php
/**
* 易传媒接口
* 文档：http://afpapi.adchina.com/
* 仲伟涛
* 2013-12-19
* log 王燕威 2014-01-03
*/

class API_Item_Open_AdChina{
    
    //计费方式
    public static $_paymentTypeArr = array(
        'cpm' => 1,
        'cpc' => 2,
        'cpd' => 3,
    );
    
    //操作方式
    public static $_operateArr = array(
        'add'  => 1, #创建
        'edit' => 2, #修改
        'del'  => 3, #删除
    );
    
    //创意类型
    public static $_creativeTypeArr = array(
        'html' => 1, #Html/Js
        'pic'  => 2, #图片
        'flash'=> 3, #Flash
    );

    //订单ID
    public static $_orderId = 112;
    
    //站点ID
    public static $_siteId = 52;

    /*
     * 获得所有的站点
     */
	public static function getSite($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'num'        => 0, #返回条数
			'offset'     => 0,  #分页offset
			'orderBy'    => 1,  #1 按照最新 2 正序
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        
        $whereArr = false;
        if($id){#如果按照id进行筛选
            $whereArr = array('Id'=>array('o'=>2,'v'=>$id)); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
            
        $data = self::postData(array(
            'url'        => "AdspaceService/GetSite",
			'postdata'   => self::createSelect(array(#POST内容
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
            
            if($id && $data){#如果是按照ID筛选，就只返回一条
                return $data[0];
            }else{
                return $data;
            }
            
        }
    }    
    
    /*
     * 更新站点
     */
	public static function mutateSite($paramArr) {
		$options = array(            
			'op'         => '', #add 创建 edit 修改 del 删除
			'id'         => 0,  #站点ID
			'name'       => '', #站点名
			'url'        => '', #URL
			'desc'       => '', #描述
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //操作类型
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
     * 获得所有的频道
     */
	public static function getChannel($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'siteId'     => self::$_siteId, #站点ID
			'num'        => 10, #返回条数
			'offset'     => 0, #分页offset
			'orderBy'    => 0, #1 按照最新 2 正序
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
        if($id){#如果按照id进行筛选
            $whereArr['Id'] = array('o'=>0,'v'=>$id); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
        
        if($siteId){#按照站点选择
            $whereArr['SiteId'] = array('o'=>2,'v'=>$siteId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
            
        $data = self::postData(array(
            'url'        => "AdspaceService.svc/GetChannel",
			'postdata'   => self::createSelect(array(#POST内容
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
            
            if($id && $data){#如果是按照ID筛选，就只返回一条
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
     * 更新频道信息
     */
	public static function mutateChannel($paramArr) {
		$options = array(            
			'op'         => '', #add 创建 edit 修改 del 删除
			'id'         => 0,  #站点ID
			'name'       => '', #站点名
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //操作类型
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        $item = array(
            'Id'          => $id ,
            'Name'        => $name,
            'SiteId'      => self::$_siteId,
        );
        if($op == 1)unset($item['Id']); #如果是创建，unsetId
        
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
     * 获得页面信息
     */
	public static function getPage($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'siteId'     => self::$_siteId, #站点ID
			'channelId'  => 0, #频道ID
			'num'        => 0, #返回条数
			'offset'     => 0, #分页offset
			'orderBy'    => 0, #1 按照最新 2 正序
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $orderArr = false;
        if($orderBy){
            $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        }
        
        $whereArr = array();
        if($id){#如果按照id进行筛选
            $whereArr['Id'] = array('o'=>2,'v'=>$id); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
        
        if($siteId){#按照站点选择
            $whereArr['SiteId'] = array('o'=>2,'v'=>$siteId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
            
        if($channelId){#按照频道选择
            $whereArr['ChannelId'] = array('o'=>2,'v'=>$channelId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
        
        $data = self::postData(array(
            'url'        => "AdspaceService.svc/GetPage",
			'postdata'   => self::createSelect(array(#POST内容
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
            
            if($id && $data){#如果是按照ID筛选，就只返回一条
                return $data[0];
            }else{
                return $data;
            }
            
        }
        
    }
    
    /*
     * 更新页面信息
     */
	public static function mutatPage($paramArr) {
		$options = array(            
			'op'         => '', #add 创建 edit 修改 del 删除
			'id'         => 0,  #页面ID
			'name'       => '', #页面名
			'channelId'  => 0,  #频道ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //操作类型
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
     * 获得广告位信息
     */
	public static function getAdspace($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'siteId'     => self::$_siteId, #站点ID
			'channelId'  => 0, #频道ID
			'pageId'     => 0, #页面ID
			'num'        => 0, #返回条数
			'offset'     => 0, #分页offset
			'orderBy'    => 0, #1 按照最新 2 正序
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
        if($id){#如果按照id进行筛选
            $whereArr['Id'] = array('o'=>0,'v'=>$id); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
        
        if($siteId){#按照站点选择
            $whereArr['SiteId'] = array('o'=>2,'v'=>$siteId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
        if($channelId){#按照频道选择
            $whereArr['ChannelId'] = array('o'=>2,'v'=>$channelId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
        if($pageId){#按照页面类型选择
            $whereArr['PageId'] = array('o'=>2,'v'=>$pageId); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=            
        }
            
        $data = self::postData(array(
            'url'        => "AdspaceService.svc/GetAdspace",
			'postdata'   => self::createSelect(array(#POST内容
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
            
            if($id && $data){#如果是按照ID筛选，就只返回一条
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
     * 更新广告位信息
     */
	public static function mutateAdspace($paramArr) {
		$options = array(            
			'op'         => '', #add 创建 edit 修改 del 删除
			'id'         => 0,  #广告位ID
			'name'       => '', #名称
			'sizeWidth'  => 0,  #宽
			'sizeHeight' => 0,  #高
			'channelId'  => 0,  #频道ID
			'pageId'     => 0,  #页面ID
            'pvStatus'   => 1,  #开启到私有交易平台 NoSet=0 Open=1 Close=-1
            'siteId'     => self::$_siteId, #站点ID
            'defaultCode'   => false,  #是否设置默认广告
            'defaultCreative'   => false,  #默认创意ID
            'adxPrice'   => false,  #adx的底价
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //操作类型
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        $item = array( 
            'Id'          => $id ,
			'AdFormat'    => 1, #备注
        );
        
        
        //广告名
        if ($name) {
            $item['Name'] = $name;
        }
        
        //所属站点
        if ($siteId) {
            $item['SiteId'] = $siteId;
        }
        
        //开启到私有交易平台
        if ($pvStatus) {
            $item['EnabledExchange'] = $pvStatus;
        }
        
        
        //频道
        if ($channelId) {
            $item['ChannelId'] = $channelId;
        }
        
        //页面
        if ($pageId) {
            $item['PageId'] = $pageId;
        }
        
        //宽高
        if ($sizeWidth && $sizeHeight) {
            $item['AdspaceSize'] = array('Width'=>$sizeWidth,'Height'=>$sizeHeight);
        }
        //到私有市场的底价
        if($adxPrice){
            $item['BuyPrice'] = $adxPrice;            
        }
        
        //是否开启默认广告
        if($defaultCreative){
            $item['DefaultCreatives'] = array(
                array(
                    'Creative' => array(
                                            //'Name'         => $name."默认广告",
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
     * 获得广告主
     */
	public static function getAdvertiser($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'num'        => 0, #返回条数
			'offset'     => 0,  #分页offset
			'orderBy'    => 1,  #1 按照最新 2 正序
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        
        $whereArr = false;
        if($id){#如果按照id进行筛选
            $whereArr = array('Id'=>array('o'=>2,'v'=>$id)); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
            
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/GetAdvertiser",
			'postdata'   => self::createSelect(array(#POST内容
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
            
            if($id && $data){#如果是按照ID筛选，就只返回一条
                return $data[0];
            }else{
                return $data;
            }
            
        }
    }    
    
    /*
     * 更新广告主信息
     */
	public static function mutateAdvertiser($paramArr) {
		$options = array(            
			'op'         => '', #add 创建 edit 修改 del 删除
			'id'         => 0,  #ID
			'name'       => '', #广告主名
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //操作类型
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
     * 获得订单
     */
	public static function getCampaign($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'num'        => 0, #返回条数
			'offset'     => 0,  #分页offset
			'orderBy'    => 1,  #1 按照最新 2 正序
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        
        $whereArr = false;
        if($id){#如果按照id进行筛选
            $whereArr = array('Id'=>array('o'=>2,'v'=>$id)); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
            
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/GetCampaign",
			'postdata'   => self::createSelect(array(#POST内容
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
            
            if($id && $data){#如果是按照ID筛选，就只返回一条
                return $data[0];
            }else{
                return $data;
            }
            
        }
    }    
       
    /*
     * 更新订单信息
     */
	public static function mutateCampaign($paramArr) {
		$options = array(            
			'op'             => '', #add 创建 edit 修改 del 删除
			'id'             => 0,  #ID
			'name'           => '', #订单名
			'contractNo'     => '', #合同号
			'advertiserId'   => '', #广告主ID
			'advertiserName' => '', #广告主名
			'startTime'      => '', #开始时间
			'endTime'        => '', #结束时间
			'notes'          => '', #备注
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //操作类型
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        $item = array(
            'Id'          => $id ,
            'Name'        => $name,
			'ContractNo'  => $contractNo, #合同名
			'Advertiser'  => array('Id'=>$advertiserId,'Name'=>$advertiserName), #广告主
			'StartTime'   => $startTime, #开始时间
			'EndTime'     => $endTime, #结束时间
			'Notes'       => $notes, #备注
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
     * 获得默认广告创意
     */
	public static function getDefaultCreative($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'num'        => 0, #返回条数
			'offset'     => 0,  #分页offset
			'orderBy'    => 1,  #1 按照最新 2 正序
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $isMore = false;
        if ($id && false !== strpos($id, ',')) {
            $isMore = true;
        }
        
        $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        
        $whereArr = false;
        if($id){#如果按照id进行筛选
            $whereArr = array('Id'=>array('o'=>0,'v'=>$id)); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
            
        $data = self::postData(array(
            'url'        => "AdspaceService/GetDefaultCreative",
			'postdata'   => self::createSelect(array(#POST内容
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
            
            if($id && $data){#如果是按照ID筛选，就只返回一条
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
     * 更新广告创意
     */
	public static function mutateDefaultCreative($paramArr) {
		$options = array(            
			'op'               => '',  #add 创建 edit 修改 del 删除
			'id'               => 0,  #ID
			'name'             => '', #创意名称 不能重复
			'campaignId'       => self::$_orderId, #所属订单Id
            'height'           => 0,  #高
            'width'            => 0,  #宽
            'html'             => '', #广告代码内容
            'fileUrl'          => '', #素材地址
            'clickUrl'         => '', #点击地址
            'isTransparent'    => 0,  #flash是否透明 默认0-不透明 1-透明 2-窗口模式
			'type'             => '', #广告类型 html：Html/Js, pic：图片, flash：Flash
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //操作类型
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        //参数数组
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
        
        if ($name) { #名称
            $item['Name'] = $name;
        }
        if ($campaignId) { #订单id
            $item['CampaignId'] = $campaignId;
        }
        if ($type) { #广告类型
            $item['AdFormat'] = isset(self::$_creativeTypeArr[$type]) ? self::$_creativeTypeArr[$type] : '';
        }
        if ('flash' == $type) { #flash属性
            $item['UrlType'] = 0;
            $item['Transparent'] = $isTransparent;
        }
        if ($html) { #html/js广告代码
            $item['HtmlContents'] = $html;
        }
        if ($fileUrl) { #素材地址
            $item['FileUrl'] = $fileUrl;
        }
        if ($clickUrl) { #点击地址
            $item['ClickUrl'] = $clickUrl;
        }
        if ($width && $height) { #宽高
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
     * 获得广告创意
     */
	public static function getCreative($paramArr) {
		$options = array(            
			'id'         => 0, #ID
			'num'        => 0, #返回条数
			'offset'     => 0,  #分页offset
			'orderBy'    => 1,  #1 按照最新 2 正序
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $isMore = false;
        if ($id && false !== strpos($id, ',')) {
            $isMore = true;
        }
        
        $orderArr = array('Id'=> ($orderBy == 1 ? 'DESC' : 'ASC') );
        
        $whereArr = false;
        if($id){#如果按照id进行筛选
            $whereArr = array('Id'=>array('o'=>0,'v'=>$id)); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
            
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/GetCreative",
			'postdata'   => self::createSelect(array(#POST内容
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
            
            if($id && $data){#如果是按照ID筛选，就只返回一条
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
     * 更新广告创意
     */
	public static function mutateCreative($paramArr) {
		$options = array(            
			'op'               => '',  #add 创建 edit 修改 del 删除
			'id'               => 0,  #ID
			'name'             => '', #创意名称 不能重复
			'campaignId'       => self::$_orderId, #所属订单Id
            'height'           => 0,  #高
            'width'            => 0,  #宽
            'html'             => '', #广告代码内容
            'fileUrl'          => '', #素材地址
            'clickUrl'         => '', #点击地址
            'isTransparent'    => 0,  #flash是否透明 默认0-不透明 1-透明 2-窗口模式
			'type'             => '', #广告类型 html：Html/Js, pic：图片, flash：Flash
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //操作类型
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        //参数数组
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
        
        if ($name) { #名称
            $item['Name'] = $name;
        }
        if ($campaignId) { #订单id
            $item['CampaignId'] = $campaignId;
        }
        if ($type) { #广告类型
            $item['AdFormat'] = isset(self::$_creativeTypeArr[$type]) ? self::$_creativeTypeArr[$type] : '';
        }
        if ('flash' == $type) { #flash属性
            $item['UrlType'] = 0;
            $item['Transparent'] = $isTransparent;
        }
        if ($html) { #html/js广告代码
            $item['HtmlContents'] = $html;
        }
        if ($fileUrl) { #素材地址
            $item['FileUrl'] = $fileUrl;
        }
        if ($clickUrl) { #点击地址
            $item['ClickUrl'] = $clickUrl;
        }
        if ($width && $height) { #宽高
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
     * 获得投放列表信息
     */
	public static function getMediabuy($paramArr) {
		$options = array(            
			'id'         => 0,  #ID
			//'name'       => '', #投放名称
			//'orderId'    => 0,  #所属订单ID
            'paymentType'=> 0,  #计费方式 1-CPM 2-CPC 3-CPD
            //'chargeType' => 0,  #投放方式 1-按展开 2-按点击 3-按比例
            //'startTime'  => '', #投放排期开始
            //'endTime'    => '', #投放排期结束
            //'placeId'    => 0,  #投放的广告位ID
            //'adspaceId'  => 0,  #投放创意ID
            'status'     => 0,  #投放状态 1-正常 3-暂停 4-草稿
            'num'        => 0,  #返回条数
			'offset'     => 0,  #分页offset
			'orderBy'    => 0,  #排序 1-按照最新 2-正序
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
        //默认订单
        $whereArr['CampaignId'] = array('o'=>2,'v'=>self::$_orderId);
        if($id){#如果按照id进行筛选
            $whereArr['Id'] = array('o'=>0,'v'=>$id); #0:IN 1:NOT IN 2:= 3:<> 4:< 5:<= 6:> 7:>=
            $num = $offset = 0;
        }
        //if($orderId){#按照订单选择
        //    $whereArr['CampaignId'] = array('o'=>2,'v'=>$orderId);
        //}
        if($paymentType && isset(self::$_paymentTypeArr[$paymentType])){#按照计费方式选择
            $whereArr['Payment'] = array('o'=>2,'v'=>self::$_paymentTypeArr[$paymentType]);
        }
        //if($chargeType){#按照投放方式选择
        //    $whereArr['ChargeType'] = array('o'=>2,'v'=>$chargeType);
        //}
        if($status){#按照投放状态选择
            $whereArr['Status'] = array('o'=>2,'v'=>$status);
        }
        
        #放回数据项
        $fildArr = array(
            'Id','Name','CampaignId','Chargeable','Payment','ChargeType'
            ,'AdspaceMediabuys','PartialDates','AdspaceMediabuyCreatives','Priority'
            ,'Budget','Frequency','Targets','Status'
        );
        
        $data = self::postData(array(
            'url'        => "MediabuyService.svc/GetMediabuy",
			'postdata'   => self::createSelect(array(#POST内容
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
                } else {#如果是按照ID筛选，就只返回一条
                    return $data[0];
                }
            }else{
                return $data;
            }
        }
    }
     
    /*
     * 更新投放信息
     */
	public static function mutateMediabuy($paramArr) {
		$options = array(            
			'op'            => '',       #add 创建 edit 修改 del 删除
			'id'            => 0,        #ID
			'name'          => '',       #投放名称 不能重复
			'orderId'       => self::$_orderId,   #所属订单ID
			'price'         => 0,        #售卖价格
            'paymentType'   => '',       #计费方式 1-CPM 2-CPC 3-CPD
            'dateArr'       => array(),  #投放排期 二维数组 array(array('StartTime','EndTime'),)
            'target'        => '',       #定向设置 地区定向 '北京,河南,...'
            'placeIdArr'    => array(),  #投放的广告位信息 array('id'=>1,'id'=>0)
            'adspaceIdArr'  => array(),  #投放的创意信息 array('id'=>1,'id'=>0)
            'status'        => 0,        #投放状态 1-正常 3-暂停 4-草稿
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        //操作类型
        $op = isset(self::$_operateArr[$op]) ? self::$_operateArr[$op] : 0;
        
        //计费方式
        if ($paymentType && isset(self::$_paymentTypeArr[$paymentType])) {
            $paymentType = self::$_paymentTypeArr[$paymentType];
        }
        
        //判断是否为创建暂停投放
        $isPause = (1 == $op && 3 == $status) ? 1 : 0;
        
        $item = array(
            'Id'          => $id ,
            //'Frequency'   => array('Enabled'=>FALSE, $frequencyArr),
            //'Budget'      => $budgetArr, #Type 0-无限量(只能用于CPM,CPC投放) 1-限量(只能用于CPM,CPC投放) 2-按比例(只能用于CPD投放)
        );
        if ($name) { #投放名称
            $item['Name'] = $name;
        }
        if ($orderId) { #订单id
            $item['CampaignId'] = $orderId;
        }
        if ($price) { #投放金额
            $item['Chargeable'] = $price;
        }
        if ($paymentType) { #投放计费方式
            $item['Payment'] = $paymentType;
        }
        if ($status) { #投放状态
            $item['Status'] = $isPause ? 1 : $status;
        }
        
        //计费方式对用默认
        $chargeType = false; #投放方式 1-按展开 2-按点击 3-按比例
        $priority = false; #优先级
        $budgetArr = array(); #计划投放量
        if ($paymentType) {
            switch ($paymentType) {
                case 1: #CPM
                    $chargeType = 1; #投放方式 按展开
                    $priority = 8; #优先级 8默认
                    $budgetArr = array('Type'=>0,'Count'=>0);
                    break;
                case 2: #CPC
                    $chargeType = 2; #投放方式 按点击
                    $priority = 8; #优先级 8默认
                    $budgetArr = array('Type'=>0,'Count'=>0);
                    break;
                case 3: #CPD
                    $chargeType = 3; #投放方式 按比例
                    $priority = 0; #优先级
                    $budgetArr = array('Type'=>2,'Count'=>100);
                    break;
                default :
                    break;
            }
        }
        
        if ($chargeType) { #投放方式
            $item['ChargeType'] = $chargeType;
        }
        if ($priority) { #优先级
            $item['Priority'] = $priority;
        }
        if ($budgetArr) { #计划投放量
            $item['Budget'] = $budgetArr;
        }
        if ($dateArr) { #排期
            $tempDateArr = array();
            foreach ($dateArr as $row) {
                $tempDateArr[] = array(
                    'StartTime' => $row[0] . ' 00:00:00',
                    'EndTime' => $row[1] . ' 23:59:59'
                );
            }
            $item['PartialDates'] = $tempDateArr;
        }
        if ($target) { #定向
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
        if ($placeIdArr) { #广告位信息
            $tempArr = array();
            $i = 0;
            foreach ($placeIdArr as $id=>$enabled) {
                $tempArr[$i]['Adspace']['Id'] = $id;
                $tempArr[$i]['Enabled'] = $enabled ? 'true' : 'false';;
                $i++;
            }
            $item['AdspaceMediabuys'] = $tempArr;
        }
        if ($adspaceIdArr) { #组合创意信息
            #获取创意信息
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
        /*if ($placeId) { #广告位信息
            $tempArr = array();
            $placeIdArr = explode(',', $placeId);
            foreach ($placeIdArr as $key=>$id) {
                $tempArr[$key]['Adspace']['Id'] = $id;
                $tempArr[$key]['Enabled'] = 'true';
            }
            $item['AdspaceMediabuys'] = $tempArr;
        } 
        if ($adspaceId) {
            //组合创意信息
            $tempArr = array();
            $adspaceIdArr = explode(',', $adspaceId);
            foreach ($adspaceIdArr as $key=>$id) {
                $tempArr[$key]['Creative']['__type'] = 'HtmlCreative:#Asp.Business';
                $tempArr[$key]['Creative']['Id'] = $id;
                $tempArr[$key]['Enabled'] = 'true';
            }
            $item['AdspaceMediabuyCreatives'] = $tempArr;
        }*/
        //if ($frequencyArr) { #投放频次
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
                
                //创建暂停推广时
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
     * 获得效果数据信息
     */
	public static function getReport($paramArr) {
		$options = array(            
			//'dimension'=> '1,7',  #维度
			//'metric'   => '2,3,4,5',  #指标 1-展示数 2-点击数 3-PV 4-UV 5-点击UV
            'startTime'   => '',    #开始时间
            'endTime'     => '',    #结束时间
            'hour'        => '',    #小时
            'num'         => 0,     #返回条数
			'offset'      => 0,     #分页offset
            'orderField'  => '',    #排序字段
			'orderBy'     => 0,     #排序 1-按照最新 2-正序
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $whereArr = array();
        //if($dimension){#按照维度选择
        //    $whereArr[] = '"Dimension":['.$dimension.']';           
        //}
        //if($metric){#按照指标选择
        //    $whereArr[] = '"Metric":['.$metric.']';
        //}
        $dateStyle = 7;#小时
        if ($startTime && $endTime) {#时间范围选择
            if ('' !== $hour) {
                $dateStyle = 19;#分钟
                
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
        if ($num || $offset) {#分页范围
            $whereArr[] = '"Paging":{"RowsNumber":'.$num.',"StartIndex":'.$offset.'}';
        }
        if ($orderField) {#排序
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
                            'clickPv' => $row[2], #点击pv
                            'impPv'   => $row[3], #pv
                            'clickUv' => 0,       #点击uv
                            'impUv'   => 0,       #uv
                        );
                    }
                }
            }
            
            return $outArr;
        }
    }
    
    /**
     * 创建select的筛选条件
     */
    private static function createSelect($paramArr) {
		$options = array(
			'num'        => '', #返回条数
			'offset'     => 0,  #分页offset
			'fild'       => false,  #分页offset
            'where'      => false,  #筛选条件
			'orderBy'    => false,  #排序方式 key => DESC|ASC  的形式
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $selectArr = array();
        #分页部分
        if($num || $offset){
            $selectArr[] = '"Paging":{"RowsNumber":'.$num.',"StartIndex":'.$offset.'}';
        }
        #返回字段部分
        if($fild){
            $selectArr[] = '"SelectFields":["'.implode('","', $fild).'"]';
        }
        #排序部分  "Orderings":{[{"Field":"Id","SortOrder":1}]}}
        if($orderBy){
            $tmpStr = $comma = '';
            foreach($orderBy as $k => $v){
                $tmpStr .= $comma . "{\"Field\":\"{$k}\",\"SortOrder\":".($v == 'ASC' ? 1 : 2)."}";
                $comma   = ",";
            }
            $selectArr[] = '"Orderings":['.$tmpStr.']';
        }
        #筛选条件
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
     * 获得snoopy对象
     */
    private static $snoopyObj = null;
    private static function getSnoopyObj(){
        if(!self::$snoopyObj){
            require_once ZOL_API_ROOT . '/Libs/FetchHtml/Snoopy.php';
        }
        self::$snoopyObj = new Snoopy();
    }

    /**
     * 接口请求
     */
	public static function postData($paramArr) {
		$options = array(
			'url'             => '', #如 AdspaceService/GetSite
			'postdata'        => '', #POST内容
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $customerId = 2;
        $authToken  = "VsvEbkySGnqAdvFH7CHJkK";
        //$baseUrl = "http://10.15.184.249/RestService/v2014032/";
        $baseUrl = "http://10.15.184.249/RestService/201404/";//添加自定义默认广告； 广告位开启private Ad exchange时，添加默认低价；
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
        if(!in_array(substr($content, 0,1),array("[","{"))){#如果返回的数据不符合格式，强制为空
            return false;
        }
        if($content){
           #$content = mb_convert_encoding($content, "GBK", "UTF-8" );
        }
        
        return $content;       
        
            
    }

}
