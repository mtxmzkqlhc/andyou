<?php
/**
* ��ȡ����
*/

class API_ZOL_Config
{
    /**
    * ���û���
    * @var array
    */
	private static $_cache;

	/**
	* ��ȡ
	*
	* @param mixed $key ���õ�ַ
	* @param mixed $arrKeyName �����Ӽ����ɶ�ά����.�ŷָ�
	* @param enum $type PHP|INI|EVAL
	* @return array|false
	*/
	public static function get($key, $arrKeyName = '', $type = 'PHP')
	{        
        $fileExt = array( #��ͬ�����ļ�����չ��ӳ���ϵ
            'ini'   => 'ini',
            'php'   => 'php',
            'eval'  => 'php',
        );
		$path = ZOL_API_ROOT . '/Config/' . str_replace('_', '/', $key) . '.' . $fileExt[strtolower($type)];
		if (isset(self::$_cache[$path])) {
			$config = self::$_cache[$path];
		} else {
			if (!ZOL_File::exists($path)) {
				self::$_cache[$path] = false;
				return false;
			}
            $type = strtoupper($type);
			switch ($type) {
				case 'INI':
					$config = parse_ini_file($path);
					break;
                case 'EVAL': #config�п����б������������Խ��������뵽config��
                    if(is_array($arrKeyName)){
                        extract($arrKeyName);
                    }
					$config = include($path);
                    break;
				default:
				case 'PHP':
					$config = include($path);
					break;
			}
			self::$_cache[$path] = $config;
		}

		if ($arrKeyName && $type != 'EVAL') {
			if (strpos('.', $arrKeyName)) {
				$keyArr = explode('.', $arrKeyName);
				foreach ($keyArr as $key) {
					if (!$config) {
						break;
					}

					$config = empty($config[$key]) ? false : $config[$key];
				}
			} else {
				$config = empty($config[$arrKeyName]) ? false : $config[$arrKeyName];
			}
		}
		return $config;
	}
}