<?php
/**
* ���ļ����������ҳ��HTML��صĺ���
* @author ��ΰ�� <zhong.weitao@zol.com.cn>
* @copyright (c) 2011-06-20
* @version v1.0
*/
class Libs_Global_PageHtml
{
    /**
    * ���ҳ���Meta��Ϣ
    *
    * @param array $paramArr ��������
    * @return string �������е�meta��ǩ
    * @example $paramArr = array(
    *                  'title'=>$seo['title'],
    *                  'keywords'=>$seo['keywords'],
    *                  'description'=>$seo['description'],
    *             );
    *             echo Libs_Global_PageHtml::getPageMeta($paramArr);
    */
    public static function getPageMeta($paramArr) {
        if (is_array($paramArr)) {
			$options = array(
				'noFollow' => 0,#�Ƿ�������������ץȡ
                'noCache'=>0,#�Ƿ񻺴�
                'chartSet'=>'GBK',#Ĭ���ַ���
                'pageType'=>'',#ҳ�����ͣ���ʱû�õ�
                'title'=>'',#ҳ�����
                'keywords'=>'',#ҳ��ؼ���
                'description'=>'',#ҳ�����
			);
			$options = array_merge($options, $paramArr);
			extract($options);
		}
        $metaStr="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$chartSet."\" />\n";
        //$metaStr.="<meta http-equiv=\"X-UA-Compatible\" content=\"IE=EmulateIE7\" />\n";
         if($noFollow){
            $metaStr.="<meta name=\"ROBOTS\" content=\"NOINDEX, NOFOLLOW\" />\n";
        }
        if($noCache){
            $metaStr.="<meta http-equiv=\"pragma\" content=\"no-cache\" />\n";
        }
        $metaStr.="<title>".$title."</title>\n";
        if($keywords){
            $metaStr.="<meta name=\"keywords\" content=\"".$keywords."\" />\n";
        }
        if($description){
            $metaStr.="<meta name=\"description\" content=\"".$description."\" />\n";
        }
        return $metaStr;
    }
    
    /**
     * ��ȡ�ϲ���ǰ̨JS CSS ����
     * @param string|array $link
     * @param string $type �ļ�����
     * @return string
     */
    public static function getMergeFrontendLink($file,$type)
    {

        $ver = (int)ZOL_File::get(PRODUCTION_ROOT . '/version.txt');

        if (IS_PRODUCTION) {#��������
            #$file = strtolower($file);
            $fileArr = array("http://s.zol-img.com.cn/d/".APP_NAME."/".APP_NAME."_{$file}.{$type}?v={$ver}");
            #$fileArr = array("http://s.zol-img.com.cn/product2011/{$type}/".APP_NAME."_{$file}.{$type}?v={$ver}");
        }else{#���Ի���
            #��ȡ�����ļ�
            $cssJsCfg = parse_ini_file(PRODUCTION_ROOT . "/Config/CssJs.ini",true);
            $files = $cssJsCfg[APP_NAME . "_" .$type][$file];
            if(!$files)return '';
            
            //add by lvj 2014-4-9 ZOL��ܶ���ͬʱ������֧�� css��JS���ֿ�
            if($type == 'css' && defined('FE_TEST_CSS_USER') && defined('FE_TEST_CSS_URL')){
                $files = str_replace(',',",".str_replace('{USER}', FE_TEST_CSS_USER, FE_TEST_CSS_URL)."/{$type}/","/{$type}/".$files);
            }  elseif ($type == 'js' && defined('FE_TEST_JS_USER') && defined('FE_TEST_JS_URL')) {
                 $files = str_replace(',',",".str_replace('{USER}', FE_TEST_JS_USER, FE_TEST_JS_URL)."/{$type}/","/{$type}/".$files);
            } else {
                #ÿ���ļ�ǰ����� /
                $files = str_replace(',',",/{$type}/","/{$type}/".$files);                
            }

            $fileArr = explode(",",$files);
        }
        $html = '';
        if($fileArr){
            foreach( $fileArr as $url){
                switch (strtolower($type)) {
                    case 'css':
                        $html .= '<link href="' . $url . '" rel="stylesheet" type="text/css" />';
                        break;
                    case 'js':
                        $html .= '<script type="text/javascript" src="' . $url . '" charset="gbk"></script>';
                        break;
                    default :
                        return false;
                }
                $html .= "\r\n";
            }
        }
        return $html;
    }

    /**
	* ���ù���ʱ��
	*
	* @param integer $sec ��
	* @param boolen $duly �Ƿ��������
	*/
	public static function setExpires($sec, $duly = false)
	{
		$lastModified = $duly ? (SYSTEM_TIME - (SYSTEM_TIME % $sec)) : (SYSTEM_TIME);
		$expireTime   = $lastModified + $sec;
		if(0 == $sec){
			header('Cache-Control: no-cache');
		}else{
			header('Cache-Control: max-age=' . $sec);
		}
		header('Expires:' . gmdate('D, d M Y H:i:s', $expireTime) . ' GMT');
		header('Last-Modified:' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
	}

}
