<?php
/**
 * 前台通用函数
 */
class Helper_Front extends Helper_Abstract {
   
    
    /**
     * 跳转到登录页面
     */
    public static function JumpToLogin($paramArr) {
        $options = array(
            'backUrl' => '', #
            'msg'     => '', #消息内容
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);

        $backUrl = $backUrl ? "&backUrl=" . urlencode($backUrl) : "";
        $backUrl .= $msg ? "&msg=" . urlencode($msg) : "";
        header("Location:?c=Login&a=ToLogin" . $backUrl);
        exit;
    }

    /**
     * 跳转Home页面
     */
    public static function JumpToHome() {
        header("Location:?c=Default");
        exit;
    }





    
    
}
