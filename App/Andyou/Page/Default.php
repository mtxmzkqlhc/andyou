<?php
/**
 * ��̨��Ĭ����ҳ
 * ��ΰ��
 * 2012-12-02
 */
class Andyou_Page_Default extends Andyou_Page_Abstract{

    public function __construct(){}

    public function validate(ZOL_Request $input, ZOL_Response $output){
        $output->pageType = 'Default';
        if (!parent::baseValidate($input, $output)) { return false; }
        return true;
    }

    /**
     * Ĭ�Ϸ���
     */
    public function doDefault(ZOL_Request $input, ZOL_Response $output){
          $db = Db_Andyou::instance();
          $res = $db->getAll("select * from member");
         var_dump($res);
         $output->setTemplate('Default');
    }
	

}
