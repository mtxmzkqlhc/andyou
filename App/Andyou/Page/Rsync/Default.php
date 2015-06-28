<?php
/**
 * ͬ����Ĭ��ҳ
 *
 */
class  Andyou_Page_Rsync_Default  extends Andyou_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Rsync_Default';
        if (!parent::baseValidate($input, $output)) { return false; }
        
        //���վ��
        $output->sysName    = $output->sysCfg['SysId']["value"] ;
        
		return true;
	}
    
    public function doDefault(ZOL_Request $input, ZOL_Response $output){
        
		$output->setTemplate('Rsync/Default');
    }
    
    
	
}

