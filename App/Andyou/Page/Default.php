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
        
        
             
        //��ý�����������
        $output->todayBillInfo = Helper_Bill::getDayIncome();
        
        //�¶�����
        $output->monthBillInfo = Helper_Bill::getDayIncome(array(
            'startTm'             => strtotime(date("Y-m-d 00:00:00",SYSTEM_TIME-30*86400)), #��ʼʱ��
            'groupDay'            => 1, #�Ƿ����ջ���
        ));
        
        
        $output->setTemplate('Default');
    }
    
    
}
