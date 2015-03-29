<?php
class DAL_KeyNames extends ZOL_DAL_KeyNames
{
	protected $_keyNames = array(#老兄不要改顺序，否则会死人的~~，类型也不要改，否则更要死人了 2009-12-02日，阿亮书
		'proId'        => 'intval',#产品ID
		'cateId'       => 'intval',#大类ID
		'subcateId'    => 'intval',#子类ID
		'manuId'       => 'intval',#品牌ID
		'merId'        => 'intval',#经销商ID
		'seriesId'     => 'intval',#系列ID
		'priceId'      => 'trim',#价格区间
		'paramId'      => 'intval',#参数Id
		'paramValId'   => 'intval',#参数值Id
		'paramValType' => 'intval',#参数值类型
		'picId'        => 'intval',#图片ID
		'docId'        => 'intval',#文章ID
		'classId'      => 'intval',#文章类别ID
		'subClassId'   => 'intval',#文章子类别ID
		'areaId'       => 'intval',#地区ID
		'provinceId'   => 'intval',#省份ID
		'cityId'       => 'intval',#城市ID
		'channelId'    => 'intval',#地区报价对应的ID
		'locationId'   => 'intval',#地区报价ID
		'classTypeId'  => 'intval',#类别的类型
		'page'         => 'intval',#页码
		'num'          => 'intval',#数量
		'type'         => 'trim',#类型
		'dataType'     => 'trim',#数据类型
		'spell'        => 'trim',#拼音
		'year'         => 'intval',#年份
		'month'        => 'intval',#月份
		'queryType'	   => 'intval',#查询类型
		'userId'	   => 'trim',#用户名
		'moduleId'	   =>'intval',#手工内容ID
		'topNum'	   =>'intval',#排行数量
		'pageNum'	   =>'intval',#页面数量
		'lensId'	   =>'intval',#镜头ID
        'eCateId'       =>'intval', #评测解析大元素ID
        'eSubId'        =>'intval', #评测解析子元素ID
		'paramVal'      => 'trim',#参数值

	);
}
