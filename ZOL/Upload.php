<?php
/**
* �ļ��ϴ���
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2010-03-02
* @version v1.0
*/

class ZOL_Upload
{
	/**
	* data ��Ҫ���ݵĶ�������
	* 
	* @var array
	*/
	private $_data = array();
	
	/**
	* ���ļ��ϴ�
	* 
	* @var boolean
	*/
	private $_isMultiFile = true;
	
	/**
	* �ϴ�Ŀ¼
	* 
	* @var string
	*/
	private $_uploadDir = '';
	
	/**
	* �����ϴ����ļ�����
	* 
	* @var array
	*/
	private $_allowFileTypeArr = array('jpg', 'png', 'gif', 'bmp');
	
	/**
	* ��ֹ�ϴ����ļ�����
	* 
	* @var array
	*/
	private $_forbidFileTypeArr = array('exe');
	
	/**
	* ����ϴ��ļ���С
	* 
	* @var integer
	*/
	private $_maxFileSize = 1048576;#1M
	
	/**
	* ��С�ϴ��ļ���С
	* 
	* @var integer
	*/
	private $_minFileSize = 0;
	
	/**
	* �ϴ���ת���ļ������ͣ�����˵����ת��������ԭ��ʽ
	* 
	* @var string 
	*/
	private $_convFileType = '';
	
	/**
	* ˮӡ����
	* 
	* @var string
	*/
	private $_watermark = array(
		'file'   => '',   #ˮӡ�ļ�
		'offset' => 'RB', #LT����, LC����, LB����, RT����, RC����, RB����, C�в�
		'alpha'  => 100,  #ˮӡ͸����
	);
	
	/**
	* ���ɵ�����ͼ�ߴ�
	* 
	* @var array
	*/
	private $_thumbSizeArr = array();
	
	private $_fileHandle = null;
	
	/**
	* �������ļ���Ϣ
	* 
	* @var array
	*/
	private $_tidyFiles = array();
	
	/**
	* �ļ�����·��
	* 
	* @var array
	*/
	private $_filePathArr = array();
	
	/**
	* �洢�ļ�����
	* 
	* @var ZOL_Abstract_Upload
	*/
	private $_saveUploadObj;
	
	/**
	* ��������ͼ�ص�����
	* 
	* @var callback
	*/
	private $_makeThumbCallback;
	
	/**
	* �������
	* 
	* @var array
	*/
	private $_errorCode = 0;
	
	/**
	* �ļ��ϴ�������
	*/
	const ERR_FILE_DATA_SICK = 1;#���ݲ�����
	const ERR_FILE_SIZE_OVER = 2;#�ļ���С������Χ
	const ERR_FILE_TYPE      = 4;#�ļ����Ͳ���ȷ
	
	/**
	* ����
	* 
	* @var ZOL_Upload
	*/
	private static $_instance = null;
	
	/**
	* ��ʼ��
	* 
	* @param array $config
	* <pre>
	* 	$_FILES $config['fileHandle']     �ļ����
	* 	string  $config['uploadDir']      �ϴ�Ŀ¼
	* 	array   $config['allowFileType']  �����ϴ����ļ�����
	* 	array   $config['forbidFileType'] ��ֹ�ϴ����ļ�����
	* </pre>
	* @return ZOL_Upload
	*/
	public function __construct(array $config = null)
	{
		if (is_array($config)) {
			$this->_set($config);
		}
	}
	
	public static function instance(array $config = null)
	{
		if (self::$_instance === null) {
			self::$_instance = new self($config);
		}
		return self::$_instance;
	}
	
	private function _set(array $config = null)
	{
		foreach ($config as $key => $val) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($val);
			}
		}
		return $this;
	}
	
	public function getErrorCode()
	{
		return $this->_errorCode;
	}
	
	/**
	* ���ö������� ��Ҫ���ڴ��ݸ��Ӵ�����
	* 
	* @param array $fileHandle
	* @return ZOL_Upload
	*/
	public function setData(array $data)
	{
		$this->_data = $data;
		return $this;
	}
	
	public function setIsMultiFile($isMultiFile)
	{
		$this->_isMultiFile = (bool)$isMultiFile;
		return $this;
	}
	
	/**
	* �����ϴ��ļ����
	* 
	* @param array $fileHandle
	* @return ZOL_Upload
	*/
	public function setFileHandle(array $fileHandle)
	{
		$this->_fileHandle = $fileHandle;
		$this->_sortFiles();
		return $this;
	}
	
	/**
	* �����ϴ�Ŀ¼
	* 
	* @param string $dir
	* @return ZOL_Upload
	*/
	public function setUploadDir($dir)
	{
		$this->_uploadDir = $dir;
		return $this;
	}
	
	/**
	* ���������ϴ����ļ�����
	* 
	* @param array $typeArr
	* @return ZOL_Upload
	*/
	public function setAllowFileType(array $typeArr) {
		$this->_allowFileTypeArr = $typeArr;
		return $this;
	}
	
	/**
	* ���ý�ֹ�ϴ����ļ�����
	* 
	* @param array $typeArr
	* @return ZOL_Upload
	*/
	public function setForbidFileType(array $typeArr) {
		$this->_forbidFileTypeArr = $typeArr;
		return $this;
	}
	
	/**
	* �����ļ��ϴ��������
	* 
	* @param string $type
	* @return ZOL_Upload
	*/
	public function setConvFileType($type)
	{
		$this->_convFileType = $type;
		return $this;
	}
	
	/**
	* ���ô洢�Ļص�����
	* 
	* @param callback $callback
	* @return ZOL_Upload
	*/
	public function setSaveUploadObj(ZOL_Interface_Upload $object)
	{
		$this->_saveUploadObj = &$object;
		return $this;
	}
	
	/**
	* ����ˮӡ����
	* 
	* @param array $watermark ˮӡ����
	* @return ZOL_Upload
	*/
	public function setWatermark(array $watermark)
	{
		$this->_watermark = $watermark;
		return $this;
	}
	
	/**
	* ������������ͼ�ߴ�
	* 
	* @param array $size
	* @return ZOL_Upload
	*/
	public function setThumbSize(array $size)
	{
		$this->_thumbSizeArr = $size;
		return $this;
	}
	
	/**
	* �����ϴ�
	* 
	* @param callback $callback
	* @return ZOL_Upload
	*/
	public function save()
	{
		#���
		$files = $this->_tidyFiles;
		if (empty($files)) {
			return false;
		}
		
		foreach ($files as $file) {
			#��֤
			if (!$this->_validate($file)) {
				continue;
			}
			
			
			/**
			* ����·��
			* 
			* @var mixed
			*/
			$pathInfo = $this->_saveUploadObj->save($file, $this->_data);
			
			if (!$pathInfo) {
				return false;
			}
			
			list($path, $thumbPath) = $pathInfo;
			$thumbPath = $this->_uploadDir . '/' . $thumbPath;
			$path      = $this->_uploadDir . '/' . $path;
			$dir       = dirname($path);
			
			is_dir($dir)|| ZOL_File::mkdir(dirname($path));
			
			#�ƶ��ļ�
			if (!move_uploaded_file($file['tmp_name'], $path)) {
				$this->_saveUploadObj->rm($path);
				continue;
			}
			chmod($path, 0777);
			if ($this->_thumbSizeArr) {
				foreach ($this->_thumbSizeArr as $size) {
					$_thumbPath = str_replace('{SIZE}', $size, $thumbPath);
					$this->makeThumb($path, $_thumbPath, $size);
				}
			}
			$this->_filePathArr[] = $path;
		}
		return $this;
	}
	
	/**
	* ��������ͼ
	* 
	* @param string $path ԭͼ
	* @param string $toPath ���ɺ��ͼ
	* @param string $size ͼƬ�ߴ�
	*/
	public function makeThumb($path, $toPath, $size)
	{
		$size = strtolower($size);
		
		$toDir = dirname($toPath);
		is_dir($toDir) || ZOL_File::mkdir($toDir);
		system("convert -geometry {$size} {$path} {$toPath} ");
		return $this;
	}
	
	public function getFilePathArr()
	{
		return $this->_filePathArr;
	}
	
	/**
	* �����ļ�
	* 
	*/
	private function _sortFiles()
	{
		$files = $this->_fileHandle;
		if (empty($files)) {
			return false;
		}
		
		if (!$this->_isMultiFile) {
			$this->_tidyFiles = array($files);
			return $this;
		}
		$tidyFiles = array();
		foreach ($files as $attr => $group) {
			foreach ($group as $key => $one) {
				if (!$one) {
					continue;
				}
				$tidyFiles[$key][$attr] = $one;
			}
		}
		$this->_tidyFiles = $tidyFiles;
		return $this;
	}
	
	/**
	* ��֤�ļ�
	* 
	* @param array $file �����ļ���Ϣ
	* @return boolean
	*/
	private function _validate(array $file)
	{
		if (empty($file['name']) || empty($file['tmp_name']) || !empty($file['error'])) {
			$this->_errorCode = self::ERR_FILE_DATA_SICK;#���ݲ�����
			return false;
		}
		
		#�ļ���С��֤
		if (empty($file['size']) || $file['size'] > $this->_maxFileSize) {
			$this->_errorCode = self::ERR_FILE_SIZE_OVER;#�ļ���������
			return false;
		}
		
		
		#�ļ�������֤
		$extName = self::_getExtName($file['name']);
		if (!in_array($extName, $this->_allowFileTypeArr) || in_array($extName, $this->_forbidFileTypeArr)) {
			$this->_errorCode = self::ERR_FILE_TYPE;#�ļ����Ͳ���ȷ
			return false;
		}
		return true;
	}
	
	/**
	* ��ȡ�ļ���չ��
	* 
	* @param string $file
	* @return string
	*/
	private static function _getExtName($file)
	{
		return strtolower(substr(strrchr($file, '.'), 1));
	}
	
}
