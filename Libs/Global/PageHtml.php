<?php
/**
* 本文件存放所有与页面HTML相关的函数
* @author 仲伟涛 <zhong.weitao@zol.com.cn>
* @copyright (c) 2011-06-20
* @version v1.0
*/
class Libs_Global_PageHtml
{
    /**
    * 获得页面的Meta信息
    *
    * @param array $paramArr 参数数组
    * @return string 返回所有的meta标签
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
				'noFollow' => 0,#是否允许搜索引擎抓取
                'noCache'=>0,#是否缓存
                'chartSet'=>'GBK',#默认字符集
                'pageType'=>'',#页面类型，暂时没用到
                'title'=>'',#页面标题
                'keywords'=>'',#页面关键字
                'description'=>'',#页面表述
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
     * 获取合并的前台JS CSS 链接
     * @param string|array $link
     * @param string $type 文件类型
     * @return string
     */
    public static function getMergeFrontendLink($file,$type)
    {

        $ver = (int)ZOL_File::get(PRODUCTION_ROOT . '/version.txt');

        if (IS_PRODUCTION) {#生产环境
            #$file = strtolower($file);
            $fileArr = array("http://s.zol-img.com.cn/d/".APP_NAME."/".APP_NAME."_{$file}.{$type}?v={$ver}");
            #$fileArr = array("http://s.zol-img.com.cn/product2011/{$type}/".APP_NAME."_{$file}.{$type}?v={$ver}");
        }else{#测试环境
            #读取配置文件
            $cssJsCfg = parse_ini_file(PRODUCTION_ROOT . "/Config/CssJs.ini",true);
            $files = $cssJsCfg[APP_NAME . "_" .$type][$file];
            if(!$files)return '';
            
            //add by lvj 2014-4-9 ZOL框架多人同时开发的支持 css和JS区分开
            if($type == 'css' && defined('FE_TEST_CSS_USER') && defined('FE_TEST_CSS_URL')){
                $files = str_replace(',',",".str_replace('{USER}', FE_TEST_CSS_USER, FE_TEST_CSS_URL)."/{$type}/","/{$type}/".$files);
            }  elseif ($type == 'js' && defined('FE_TEST_JS_USER') && defined('FE_TEST_JS_URL')) {
                 $files = str_replace(',',",".str_replace('{USER}', FE_TEST_JS_USER, FE_TEST_JS_URL)."/{$type}/","/{$type}/".$files);
            } else {
                #每个文件前面添加 /
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
	* 设置过期时间
	*
	* @param integer $sec 秒
	* @param boolen $duly 是否正点过期
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
