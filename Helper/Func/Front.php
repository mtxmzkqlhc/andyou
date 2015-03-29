<?php

/**
 * ǰ̨��غ���
 * ��ΰ��
 * 2013-11-26
 */
class Helper_Func_Front extends Helper_Abstract {

    public static $uploadModuleName = "star";

    /**
     * ��ת��404
     */
    public static function JumpTo404() {
        ZOL_Http::send404Header();
        header("Location:/NoPage/");
        exit;
    }

    /**
     * ��ת����¼ҳ��
     */
    public static function JumpToLogin($paramArr) {
        $options = array(
            'backUrl' => '', #��Ϣ����
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $backUrl = $backUrl ? "?backUrl=" . urlencode($backUrl) : "";
        header("Location:/" . $backUrl);
        exit;
    }

    /**
     * ��ת��Ϣ��֤ҳ��(���������Ӳ�����ת)
     */
    public static function JumpToVerify($prmStr = '') {
        header("Location:/Verify/" . $prmStr);
        exit;
    }

    /**
     * ��תHomeҳ��
     */
    public static function JumpToHome() {
        header("Location:/Home/");
        exit;
    }

    /**
     * ��ת�����Homeҳ��
     */
    public static function JumpToAgentHome() {
        header("Location:/Agent_Home/");
        exit;
    }

    /**
     * ��ת�������޸�ҳ
     */
    public static function JumpToAccountCert() {
        header("Location:/Account_Cert/");
        exit;
    }

    /**
     * ��ʾ��Ϣ��ʾҳ��
     */
    public static function showMsg($paramArr) {
        $options = array(
            'message' => '', #��Ϣ����
            'level' => 0, #0:��ʾ��1:�ɹ� 2��ʧ��
            'jumpSec' => 0, #�������0������ת
            'showClose' => false, #�Ƿ���ʾ�رհ�ť
            'jumpUrl' => array(), #������ת��name url
            'showBLack' => '', #����url
            'urlArr' => false, #������ת�ĵ�ַ����
            'footerHtml' => '', #�ײ����Զ������õ�Html
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $output = ZOL_Registry::get('response'); #���output����
        $output->footerCss = 'footerFixed';
        $output->header = $output->fetchCol("Part/Header");
        $output->footer = $output->fetchCol("Part/Footer");
        $output->message = $message;
        $output->options = $options;
        $output->showClose = $showClose;
        $output->showBLack = $showBLack;
        $output->footerHtml = $footerHtml;
        $output->setTemplate("Base/Prompt");    #������Ϣģ��
        $output->display();
        exit;
    }

    /**
     * ����ͷ����ؼ���
     */
    public static function setTitle($paramArr) {
        $options = array(
            'title' => '', #����
            'description' => '', #����
            'keywords' => '', #�ؼ���
            'css' => '' #css��ʽ 
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $outStr = "<!DOCTYPE html>\n";
        $outStr .= '<html lang="zh-CN">' . "\n";
        $outStr .= "<head>\n";
        $outStr .= '<meta charset="gbk">' . "\n";
        $outStr .= '<meta name="renderer" content="webkit">' . "\n"; #Ϊ360������ö��ں�����Ϊchrome

        if ($title)
            $outStr .= '<title>' . $title . '</title>' . "\n";
        if ($description)
            $outStr .= '<meta name="description" content="' . $description . '">' . "\n";
        if ($keywords)
            $outStr .= '<meta name="keywords" content="' . $keywords . '">' . "\n";
        if ($css)
            $outStr .= Libs_Global_PageHtml::getMergeFrontendLink($css, "css");
        $outStr .= "</head><body>";

        return $outStr;
    }

    /**
     * ͼƬ����
     * code wangmc
     * date 2014-03-20
     */
    public static function getUploadImage($fileName, $size = false) {
        if (!$fileName)
            return '';

        $partUri = "/" . ltrim($fileName, "/");
        if ($size)
            $partUri = "/t_s" . $size . $partUri;

        $rd = ord(substr($partUri, -5, 1)) % 6;
        return "http://i{$rd}." . self::$uploadModuleName . ".fd.zol-img.com.cn" . $partUri;
    }

}
