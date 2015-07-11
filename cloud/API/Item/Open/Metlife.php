<?php

/**
 * 中美大都会免险
 * 注意：此文件为UTF-8编码
 *
 * @author lvj
 * @copyright (c) 2014-12-03
 * 
 */
class API_Item_Open_Metlife {

    public static function Request($paramArr) {
        $options = array(
            'serverUrl' => '', //请求地址
            'key' => '',
            'name' => '', //*姓名
            'sex' => '', //*性别 (男：Male 女：Female)
            'birthday' => '', //*出生日期 YYYY-MM-DD
            'document' => '', //证件号码'
            'documentType' => '', //证件类型 IdentityCard:身份证 SoldierCard:军人证 Passport:护照 EmigrationCard:侨胞证 OtherCard:其他 Enlistee:士兵证 Police:警官证 Hometown:返乡证 AccessCard:通行证 Foreigner:外国人居留证 Individual:特殊个人卡种类
            'email' => '', //电子邮箱
            'mobile' => '', //*手机
            'province' => '', //*省
            'city' => '', //*市
            'address' => '', //*地址
            'presentCode' => '', //赠品编号
            'occupation' => '', //职业放在备注里
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $serverUrl = $serverUrl ? $serverUrl : 'http://icare.metlife.com.cn/services/YSW2ICareSave?wsdl';

        //自己记录的数据
        $db = API_Db_Active::instance();

        //有记录的话不进行插入
        $info = $db->getRow('SELECT z_id,z_code FROM 20150112metlife WHERE z_mobile="' . $mobile . '"');
        //手机号验证 已经领取过
        if (!empty($info['z_code'])) {
            return array('flag' => 0, 'message' => ZOL_String::u8conv('承保失败，此手机号已承保过！'), 'FreeInsureNo' => $info['z_code']);
        }

        //证件验证
        $checkDocument = $db->getRow('SELECT z_id,z_code FROM 20150112metlife WHERE z_documentType="' . $documentType . '" AND z_document="' . $document . '"');
        if (!empty($checkDocument['z_code'])) {
            return array('flag' => 0, 'message' => ZOL_String::u8conv('承保失败，此证件号已承保过！'), 'FreeInsureNo' => $checkDocument['z_code']);
        }

        //编号
        $keyId = isset($info['z_id']) ? $info['z_id'] : '';

        if ($keyId) {
            //更新信息
            $sql = 'UPDATE 20150112metlife SET z_name="' . $name . '", z_occupation="' . $occupation . '", z_sex="' . $sex . '", z_birthday="' . $birthday . '", z_mobile="' . $mobile . '", z_email="' . $email . '", z_documentType="' . $documentType . '", z_document="' . $document . '", z_province="' . $province . '", z_city="' . $city . '", z_address="' . $address . '", z_presentCode="' . $presentCode . '", z_date="' . date('Y-m-d H:i:s', time()) . '", z_ip="' . API_Item_Service_Area::getClientIp() . '" WHERE z_id=' . $keyId . ' LIMIT 1';
            $db->query($sql);

            //发送的XML报文
            $key = 'ZOL' . $keyId;
            $paramArr['key'] = $key;

            $XMLStr = self::createXML($paramArr);

            /* 获取服务器端定义的方法及参数类型
              try {
              $client = new SoapClient($webServerUrl);
              $client->__getFunctions();
              $client->__getTypes();
              } catch (SOAPFault $e) {
              $e;
              }
             */

            $client = new SoapClient($serverUrl);

            $rst = ZOL_String::u8conv($client->doRequest($XMLStr));

            //解析返回的XML报文
            $xml = simplexml_load_string($rst);
            //转为数组
            $xmlData = json_decode(json_encode($xml), TRUE);

            //判断是否发送成功
            if (isset($xmlData['paq_body']['HolderIdentify'])) {
                if ($xmlData['paq_body']['HolderIdentify']['Flag'] === 'TRUE') {
                    //请求成功
                    $callbackData = $xmlData['paq_body']['HolderIdentify'];
                    //成功标识符
                    $data['flag'] = 1;
                    //成功提示
                    $data['message'] = ZOL_String::u8conv($callbackData['Message']);
                    //成功投保号
                    $data['FreeInsureNo'] = ZOL_String::u8conv($callbackData['FreeInsureNo']);
                    //投保成功记录投保号
                    $db->query('UPDATE 20150112metlife SET z_code="' . $data['FreeInsureNo'] . '" WHERE z_id=' . $keyId . ' LIMIT 1');
                } else {
                    //请求失败
                    $message = $xmlData['paq_body']['HolderIdentify']['Message'];

                    $data = array('flag' => 0, 'key' => $key, 'message' => ZOL_String::u8conv($message));
                }
            } else {
                $data = array('flag' => 0, 'key' => $key, 'message' => ZOL_String::u8conv('请求超时，请用此key重新请求！'));
            }

            return $data;
        }

        return array('flag' => 0, 'message' => ZOL_String::u8conv('承保失败'));
    }

    /**
     * 将数据封装成XML
     */
    private static function createXML($paramArr) {
        $options = array(
            'key' => '',
            'name' => '', //*姓名
            'sex' => '', //*性别 (男：Male 女：Female)
            'birthday' => '', //*出生日期 YYYY-MM-DD
            'document' => '', //证件号码'
            'documentType' => '', //证件类型 IdentityCard:身份证 SoldierCard:军人证 Passport:护照 EmigrationCard:侨胞证 OtherCard:其他 Enlistee:士兵证 Police:警官证 Hometown:返乡证 AccessCard:通行证 Foreigner:外国人居留证 Individual:特殊个人卡种类
            'email' => '', //电子邮箱
            'mobile' => '', //*手机
            'province' => '', //*省
            'city' => '', //*市
            'address' => '', //*地址
            'presentCode' => '', //赠品编号
            'occupation' => '', //职业放在备注里
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);

        extract($options);

        $XMLStr = '<?xml version="1.0" encoding="GBK"?>
                    <Records>
                        <Record>
                            <Customer>
                                <Key>' . $key . '</Key>
                                <FromSystem>ZOL</FromSystem>
                                <Name>' . $name . '</Name>
                                <Sex>' . $sex . '</Sex>
                                <Birthday>' . $birthday . '</Birthday>
                                <Document>' . $document . '</Document>
                                <DocumentType>' . $documentType . '</DocumentType>
                                <Email>' . $email . '</Email>
                                <Mobile>' . $mobile . '</Mobile>
                                <ContactState>
                                    <Name>' . $province . '</Name>
                                </ContactState>
                                <ContactCity>
                                    <Name>' . $city . '</Name>
                                </ContactCity>
                                <ContactAddress>' . $address . '</ContactAddress>
                                <Occupation>
                                    <Code>0001001</Code>
                                </Occupation>
                                <Description>职业:' . $occupation . ';详细地址:' . $province . $city . $address . '</Description>
                            </Customer>
                            <Task>
                                <CallList>
                                    <Name></Name>
                                </CallList>
                                <Campaign>
                                    <Name></Name>
                                </Campaign>
                            </Task>
                            <Activity>
                                <Code></Code>
                                <Present>
                                    <Code>' . $presentCode . '</Code>
                                </Present>
                                <TSR>
                                    <Code>805095</Code>
                                </TSR>
                                <DonateTime>' . date('Y-m-d', time()) . '</DonateTime>
                                <SMS>1</SMS>
                                <FlghtNo />
                                <ValidTime />
                            </Activity>
                        </Record>
                    </Records>';

        return ZOL_String::convToU8($XMLStr, 'GBK');
    }

}
