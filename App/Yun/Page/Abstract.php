<?php

abstract class Yun_Page_Abstract extends ZOL_Abstract_Page{
	
	/**
	 * 父类的Validate
	 */
	public function baseValidate(ZOL_Request $input, ZOL_Response $output){
        

		$output->execName   = $input->execName    = $input->getExecName();
		$output->actName    = $input->actName     = $input->getActionName();
		$output->ctlName    = $input->ctlName     = $input->getControllerName();
        
        return true;
	}
    

    protected function showHtml($msg){
        echo '<!DOCTYPE html><html lang="en"><head><title>我了个去...</title></head>';
        echo "<div style='margin:0 auto;text-align:center;padding-top:30px;line-height:40px'>{$msg}</div>";
        echo '</body></html>';
        exit;
    }
}
