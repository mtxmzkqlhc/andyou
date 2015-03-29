<?php

/**
 * 前台相关函数
 * 仲伟涛
 * 2013-11-26
 */
class Helper_Func_Front extends Helper_Abstract {

    public static $uploadModuleName = "star";

    /**
     * 跳转到404
     */
    public static function JumpTo404() {
        ZOL_Http::send404Header();
        header("Location:/NoPage/");
        exit;
    }

    /**
     * 跳转到登录页面
     */
    public static function JumpToLogin($paramArr) {
        $options = array(
            'backUrl' => '', #消息内容
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $backUrl = $backUrl ? "?backUrl=" . urlencode($backUrl) : "";
        header("Location:/" . $backUrl);
        exit;
    }

    /**
     * 跳转信息验证页面(新增可增加参数跳转)
     */
    public static function JumpToVerify($prmStr = '') {
        header("Location:/Verify/" . $prmStr);
        exit;
    }

    /**
     * 跳转Home页面
     */
    public static function JumpToHome() {
        header("Location:/Home/");
        exit;
    }

    /**
     * 跳转代理的Home页面
     */
    public static function JumpToAgentHome() {
        header("Location:/Agent_Home/");
        exit;
    }

    /**
     * 跳转到资质修改页
     */
    public static function JumpToAccountCert() {
        header("Location:/Account_Cert/");
        exit;
    }

    /**
     * 显示消息提示页面
     */
    public static function showMsg($paramArr) {
        $options = array(
            'message' => '', #消息内容
            'level' => 0, #0:提示，1:成功 2：失败
            'jumpSec' => 0, #如果大于0，将跳转
            'showClose' => false, #是否显示关闭按钮
            'jumpUrl' => array(), #进行跳转的name url
            'showBLack' => '', #返回url
            'urlArr' => false, #设置跳转的地址数组
            'footerHtml' => '', #底部可以额外设置的Html
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $output = ZOL_Registry::get('response'); #获得output对象
        $output->footerCss = 'footerFixed';
        $output->header = $output->fetchCol("Part/Header");
        $output->footer = $output->fetchCol("Part/Footer");
        $output->message = $message;
        $output->options = $options;
        $output->showClose = $showClose;
        $output->showBLack = $showBLack;
        $output->footerHtml = $footerHtml;
        $output->setTemplate("Base/Prompt");    #设置消息模板
        $output->display();
        exit;
    }

    /**
     * 公共头标题关键字
     */
    public static function setTitle($paramArr) {
        $options = array(
            'title' => '', #标题
            'description' => '', #描述
            'keywords' => '', #关键字
            'css' => '' #css样式 
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $outStr = "<!DOCTYPE html>\n";
        $outStr .= '<html lang="zh-CN">' . "\n";
        $outStr .= "<head>\n";
        $outStr .= '<meta charset="gbk">' . "\n";
        $outStr .= '<meta name="renderer" content="webkit">' . "\n"; #为360浏览器置顶内核设置为chrome

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
     * 图片连接
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
