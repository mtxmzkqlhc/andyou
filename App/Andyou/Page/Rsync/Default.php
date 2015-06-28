<?php
/**
 * 同步的默认页
 *
 */
class  Andyou_Page_Rsync_Default  extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Rsync_Default';
        if (!parent::baseValidate($input, $output)) { return false; }
        
        //获得站点
        $output->sysName    = $output->sysCfg['SysId']["value"] ;
        
		return true;
	}
    
    public function doDefault(ZOL_Request $input, ZOL_Response $output){
        
		$output->setTemplate('Rsync/Default');
    }
    
    
	
}

