<?php

/*
|---------------------------------------------------------------
| Response for output data
|---------------------------------------------------------------
| @package ZOL
|
*/

class ZOL_Response
{

	/*
	|---------------------------------------------------------------
	| Response data
	|---------------------------------------------------------------
	| @var array
	|
	*/
	protected $_aProps;

	/*
	|---------------------------------------------------------------
	| HTTP status code
	|---------------------------------------------------------------
	| @var integer
	|
	*/
	protected $_code;

	/*
	|---------------------------------------------------------------
	| Stores output string to be returned to user
	|---------------------------------------------------------------
	| @var string
	|
	*/
	protected $_data;

	/*
	|---------------------------------------------------------------
	| List of messages to be returned to user
	|---------------------------------------------------------------
	| @var array
	|
	*/
	protected $_aMessages;
	
	protected $pageCharset = 'GB2312';

	/*
	|---------------------------------------------------------------
	| HTTP headers
	|---------------------------------------------------------------
	| @var array
	|
	*/
	protected $aHeaders = array();

	protected $contentType = 'html';
	
	protected $_template;

	public $_skinPath = '';#方便指定其他app的模板,tools中使用
	
	public function __construct()
	{
		if (defined('SYSTEM_CHARSET'))
		{
			$this->pageCharset = SYSTEM_CHARSET;
		}
	}
	
	public function set($k, $v)
	{
		$this->_aProps[$k] = $v;
	}

	public function add(array $aData)
	{
		foreach ($aData as $k => $v) {
			$this->_aProps[$k] = $v;
		}
	}
	
	public function setTemplate($string, $tmp = 0)
	{
        //add by lvj 2014-4-9 ZOL框架多人同时开发的支持
		if($this->_skinPath != ''){
            $this->_template = $this->_skinPath . $string . '.tpl.php';
		}else{
            //定义了FE_TEST_TPL_USER并且是开发环境
            $this->_template = ((defined('FE_TEST_TPL_USER') && defined('FE_TEST_TPL_PATH') && !IS_PRODUCTION) ? (str_replace('{USER}', FE_TEST_TPL_USER, FE_TEST_TPL_PATH). '/App/' . APP_NAME) : APP_PATH) . '/Skin/' . $string . '.tpl.php';
		}
	}
	
	public function getTemplate()
	{
		return $this->_template;
	}
	
	public function setMessages(array $aMessages)
	{
		$this->_aMessages = $aMessages;
	}

	/*
	|---------------------------------------------------------------
	| If object attribute does not exist, magically set it to data array
	|---------------------------------------------------------------
	| @param unknown_type $k
	| @param unknown_type $v
	|
	*/
	public function __set($k, $v)
	{
		if (!isset($this->$k)) {
			$this->_aProps[$k] = $v;
		}
	}

	public function __get($k)
	{
		if (isset($this->_aProps[$k])) {
			return $this->_aProps[$k];
		}
	}

	public function getHeaders()
	{
		return $this->aHeaders;
	}

	public function getBody()
	{
		return $this->_aProps;
	}
	public function getOutputBody()
	{
		return $this->_data;
	}
	public function setBody($body)
	{
		$this->_data = $body;
	}

	public function addHeader($header)
	{
		if (!in_array($header, $this->aHeaders)) {
			$this->aHeaders[] = $header;
		}
	}

	public function json($data = '', $message = '', $error = false)
	{
		$this->set('error', $error);
		$this->set('data', $data);
		$this->set('message', $message);
		$this->contentType = 'json';
	}
	public function getContentType()
	{
		return $this->contentType;
	}
	public function setCode($code)
	{
		$this->_code = $code;
	}

	public function __toString()
	{
		return $this->fetch();
	}

    public function session($key, $var)
    {
        session_start();
        $_SESSION[$key] = $var;
    }

    public function cookie($name, $value = null, $expire = 0, $path = null, $domain = '.zol.com.cn', $secure = false, $httponly = false)
    {
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

	public function buildStaticPage(array $data, $template, $filePath)
	{
		if (empty($data))
		{
			trigger_error('$data dose not empty!');

			return false;
		}
		if (empty($template))
		{
			trigger_error('$template dose not empty!');

			return false;
		}
		if (empty($filePath))
		{
			trigger_error('$filePath dose not empty!');

			return false;
		}
		$output = new ZOL_Response();
		$output->add($data);
		$output->template = $template;
		$view = new ZOL_View_Simple($output);

		ZOL_File::write($view->render(), $filePath);

		return false;
	}

	public function fetch()
	{
		if ('' == $this->getTemplate())
		{
			trigger_error('$template dose not empty!');

			return false;
		}
		$view = new ZOL_View_Simple($this);

		return $view->render();
	}
	public function fetchForAjax($template){

		if (empty($template))
		{
			trigger_error('$template dose not empty!');

			return false;
		}
		$this->setTemplate($template);
		$view = new ZOL_View_Simple($this);
		$data = $view->render();
		return iconv('GBK', 'UTF-8//TRANSLIT', $data);
	}
	public function fetchCol($template,$js=false)
	{
		if (empty($template))
		{
			trigger_error('$template dose not empty!');

			return false;
		}
		$this->setTemplate($template);
		$view = new ZOL_View_Simple($this);
		if($js){#是否输出为js代码
			return '<script>document.write("'.  addslashes(str_replace(array("\r","\n"),"",$view->render())) . '");</script>';
		}else{
			return $view->render();
		}
	}

	public function display()
	{
		if (function_exists('ob_gzhandler') && defined("IS_PRODUCTION") && IS_PRODUCTION === true)#生产环境开启
		{
			#ob_start('ob_gzhandler');
		}
		if (!headers_sent())
		{
			//header('Last-Modified: ' . gmdate('D, d M Y H:i:s', SYSTEM_TIME) . ' CST');
			//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			header('Content-Type: text/html; charset=' . $this->pageCharset);
			
			foreach ($this->getHeaders() as $header)
			{
					header($header);
			}       
		}
		$html = $this->fetch();
		echo $html;
        return $html;
	}
}
