<?php

abstract class ZOL_Abstract_Page
{
	/*
	|---------------------------------------------------------------
	| Array of action permitted by mgr subclass.
	|---------------------------------------------------------------
	| @access  private
	| @var     array
	|
	*/

    /**
     * �Ƿ񻺴�
     * @var bool
     */
    protected $_isCache = false;


    /**
     * ����ʱ�� ��λ ��
     * @var int
     */
    protected $_expire  = 3600;

	protected $_aActionsMapping = array();
	
	public function addActionMapping(array $aActionMap)
	{
		$this->_aActionsMapping = $aActionMap;
	}
	
	public function getActionMapping()
	{
		return $this->_aActionsMapping;
	}

    /**
     * ҳ�����ʱ�� 0Ϊ��������
     * @return int
     */
    public function getExpire()
    {
        return $this->_expire;
    }

    /**
     * ҳ���Ƿ񻺴�
     * @return bool
     */
    public function isCache()
    {
        return $this->_isCache;
    }
	
	/*
	|---------------------------------------------------------------
	| Specific validations are implemented in sub classes.
	|---------------------------------------------------------------
	| @param   ZOL_Request     $req    ZOL_Request object received from user agent
	| @return  boolean
	|
	*/
	public function validate(ZOL_Request $input, ZOL_Response $output)
	{
		return true;
	}
}
