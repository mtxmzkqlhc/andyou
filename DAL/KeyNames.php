<?php
class DAL_KeyNames extends ZOL_DAL_KeyNames
{
	protected $_keyNames = array(#���ֲ�Ҫ��˳�򣬷�������˵�~~������Ҳ��Ҫ�ģ������Ҫ������ 2009-12-02�գ�������
		'proId'        => 'intval',#��ƷID
		'cateId'       => 'intval',#����ID
		'subcateId'    => 'intval',#����ID
		'manuId'       => 'intval',#Ʒ��ID
		'merId'        => 'intval',#������ID
		'seriesId'     => 'intval',#ϵ��ID
		'priceId'      => 'trim',#�۸�����
		'paramId'      => 'intval',#����Id
		'paramValId'   => 'intval',#����ֵId
		'paramValType' => 'intval',#����ֵ����
		'picId'        => 'intval',#ͼƬID
		'docId'        => 'intval',#����ID
		'classId'      => 'intval',#�������ID
		'subClassId'   => 'intval',#���������ID
		'areaId'       => 'intval',#����ID
		'provinceId'   => 'intval',#ʡ��ID
		'cityId'       => 'intval',#����ID
		'channelId'    => 'intval',#�������۶�Ӧ��ID
		'locationId'   => 'intval',#��������ID
		'classTypeId'  => 'intval',#��������
		'page'         => 'intval',#ҳ��
		'num'          => 'intval',#����
		'type'         => 'trim',#����
		'dataType'     => 'trim',#��������
		'spell'        => 'trim',#ƴ��
		'year'         => 'intval',#���
		'month'        => 'intval',#�·�
		'queryType'	   => 'intval',#��ѯ����
		'userId'	   => 'trim',#�û���
		'moduleId'	   =>'intval',#�ֹ�����ID
		'topNum'	   =>'intval',#��������
		'pageNum'	   =>'intval',#ҳ������
		'lensId'	   =>'intval',#��ͷID
        'eCateId'       =>'intval', #���������Ԫ��ID
        'eSubId'        =>'intval', #���������Ԫ��ID
		'paramVal'      => 'trim',#����ֵ

	);
}
