<?php
/**
 * ǰ̨ͨ�ú���
 */
class Helper_Front extends Helper_Abstract {
   
    
    /**
     * ��ת����¼ҳ��
     */
    public static function JumpToLogin($paramArr) {
        $options = array(
            'backUrl' => '', #
            'msg'     => '', #��Ϣ����
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);

        $backUrl = $backUrl ? "&backUrl=" . urlencode($backUrl) : "";
        $backUrl .= $msg ? "&msg=" . urlencode($msg) : "";
        header("Location:?c=Login&a=ToLogin" . $backUrl);
        exit;
    }

    /**
     * ��תHomeҳ��
     */
    public static function JumpToHome() {
        header("Location:?c=Default");
        exit;
    }





    
    
}
