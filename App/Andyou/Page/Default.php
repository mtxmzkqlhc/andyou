<?php
/**
 * 后台的默认首页
 * 仲伟涛
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
     * 默认方法
     */
    public function doDefault(ZOL_Request $input, ZOL_Response $output){
        
        
             
        //获得今天的收入情况
        $output->todayBillInfo = Helper_Bill::getDayIncome();
        
        //月度曲线
        $output->monthBillInfo = Helper_Bill::getDayIncome(array(
            'startTm'             => strtotime(date("Y-m-d 00:00:00",SYSTEM_TIME-30*86400)), #开始时间
            'groupDay'            => 1, #是否按照日汇总
        ));
        
        
        $output->setTemplate('Default');
    }
    
    
}
