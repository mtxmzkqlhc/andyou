<?php
/* *
 * �����ļ�
 * �汾��3.3
 * ���ڣ�2012-07-19
 * ˵����
 * ���´���ֻ��Ϊ�˷����̻����Զ��ṩ���������룬�̻����Ը����Լ���վ����Ҫ�����ռ����ĵ���д,����һ��Ҫʹ�øô��롣
 * �ô������ѧϰ���о�֧�����ӿ�ʹ�ã�ֻ���ṩһ���ο���
	
 * ��ʾ����λ�ȡ��ȫУ����ͺ��������id
 * 1.������ǩԼ֧�����˺ŵ�¼֧������վ(www.alipay.com)
 * 2.������̼ҷ���(https://b.alipay.com/order/myorder.htm)
 * 3.�������ѯ���������(pid)��������ѯ��ȫУ����(key)��
	
 * ��ȫУ����鿴ʱ������֧�������ҳ��ʻ�ɫ��������ô�죿
 * ���������
 * 1�������������ã������������������������
 * 2���������������ԣ����µ�¼��ѯ��
 */
 
//�����������������������������������Ļ�����Ϣ������������������������������
//���������id����2088��ͷ��16λ������
$alipayConfig['partner']		= '2088311469445753';

//��ȫ�����룬�����ֺ���ĸ��ɵ�32λ�ַ�
$alipayConfig['key']			= 'lztjlq69uhcxqnf3sljvzgktxj0hwi46';

// �տ��˺�
$alipayConfig['sellerEmail']	= 'hengxing@zol.com.cn';

// �������첽֪ͨҳ��·��
$alipayConfig['notifyUrl']	    = 'http://e.zol.com.cn/alipayNotifyUrl.php';

// ҳ����תͬ��֪ͨҳ��·��
$alipayConfig['returnUrl']	    = 'http://e.zol.com.cn/Pay_Pay/AlipayReturnUrl/';

// �տ��˺�
$alipayConfig['sellerEmail']	= 'hengxing@zol.com.cn';

//�����������������������������������Ļ�����Ϣ������������������������������
//ǩ����ʽ �����޸�
$alipayConfig['sign_type']    = strtoupper('MD5');

//�ַ������ʽ Ŀǰ֧�� gbk �� utf-8
$alipayConfig['input_charset']= strtolower('gbk');

//ca֤��·����ַ������curl��sslУ��
//�뱣֤cacert.pem�ļ��ڵ�ǰ�ļ���Ŀ¼��
$alipayConfig['cacert']    = PRODUCTION_ROOT . '/Libs/Pay/cacert.pem';

//����ģʽ,�����Լ��ķ������Ƿ�֧��ssl���ʣ���֧����ѡ��https������֧����ѡ��http
$alipayConfig['transport']    = 'http';
?>