<?php
/**
 * ������
 * @author wiki<charmfocus@gmail.com>
 * @copyright(c) 2010-11-23
 * @version v1.0
 */
class ZOL_Debugger
{
    /**
     * ��ӡ�������
     * @param mixed $var ����
     * @param bool $exit ��ӡ���Ƿ��˳�
     * @return void
     */
    public static function dump($var, $exit = true)
    {
        if (!IS_PRODUCTION) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
            $exit && exit();
        }
    }
    
    public static function stop($code = 0)
    {
        if (!IS_PRODUCTION) {
            exit($code);
        }
    }
}