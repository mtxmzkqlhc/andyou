<?php

abstract class Yun_Page_M_Abstract extends ZOL_Abstract_Page{

	/**
	 * ¸¸ÀàµÄValidate
	 */
	public function baseValidate(ZOL_Request $input, ZOL_Response $output){
         
		$output->execName   = $input->execName    = $input->getExecName();
		$output->actName    = $input->actName     = $input->getActionName();
		$output->ctlName    = $input->ctlName     = $input->getControllerName();

        return true;
	}
    

    protected function showMessage(ZOL_Request $input, ZOL_Response $output){
        
        echo $output->fetchCol("M/Message");
        
        exit;
    }
}
