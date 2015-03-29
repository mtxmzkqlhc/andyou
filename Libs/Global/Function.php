<?php
/**
* 各种开发所需要函数
* @author 钱志伟 <qian.zhiwei@zol.com.cn>
* @copyright (c) 2013-02-18
* @version v1.0
*/
class Libs_Global_Function
{
    /**
     * 输出各种类型的标签
     * @link http://www.bootcss.com/components.html#labels-badges
     * @param type $paramArr
     * @return string 
     */
    function outputHtmlLabel($paramArr=array())
    {
        $options = array(
            'msg'    => '', #文本
            'type'   => '', #样式
        );
        $options = array_merge($options, $paramArr);
        extract($options);

        $class = 'label';
        switch ($type) {
            case "success":
            case "warning":
            case "important":
            case "info":
            case "inverse":
                $class .= ' label-'.$type;break;
        }

        return sprintf("<span class='%s'>%s</span>", $class, $msg);
    }
    
    
    /**
     * 对二维数组，按指定字段排序，默认降序
     * @param array $array
     * @param string $field
     * @param int $asc
     * @return array 
     */
    public static function sort2Array($paramArr = array()) 
    {
        $options = array(
            'data'  => array(), #数组
            'field' => 'time',  #排序依据的字段
            'asc'   => 0,       #0=>降序 1=>升序
        );
        $options = array_merge($options, $paramArr);
        extract($options);

        $data=array_values($data);
        #插入排序
        $len = count($data);#最后一个元素下标
        for ($i=1; $i<$len; $i++) {
            $tmp  = $data[$i];
            $j = $i - 1;
            if ($asc) { #升序
                while ($j>=0 && $data[$j][$field]>$tmp[$field]) {
                    $data[$j+1] = $data[$j];
                    $j--;
                }
            } else { #降序
                while ($j>=0 && $data[$j][$field]<$tmp[$field]) {
                    $data[$j+1] = $data[$j];
                    $j--;
                }
            }

            $data[$j+1] = $tmp;
        }
        return $data;
    }
    
    /**
     * 将一维数组变成二维数组，主要用于一行的数据变成多行
     * @author qianzhiwei
     * @param type $paramArr
     * @return type 
     */
    public static function splitArray($paramArr = array()) 
    {
        $options = array(
            'data'  => array(), #数组
            'colNum'=> 5,       #列数
            'pad'   => 1,       #不填充
        );
        $options = array_merge($options, $paramArr);
        extract($options);
        
        $ret = array();

        $cnt = count($data);
        $rowNum = ceil($cnt/$colNum);
        $ret = array();
        for($i=0; $i<$rowNum; $i++) {
            $ret[] = array_slice($data, $i*$colNum, $colNum);
        }
        if($pad && $cnt%$colNum!=0 && $cnt>$colNum) {
            for($i=$cnt%$colNum; $i<$colNum; $i++) {
                $ret[$rowNum-1][] = array();
            }
        }
        return $ret;
    }
    
    /**
     * 将数组值转化为图表所需要字符串，例如 array('北京', '上海', '重庆') 转化 "['北京', '上海', '重庆']"
     * @param type $paramArr
     * @return type 
     */
    public static function array2ChartString($paramArr)
    {
        $options = array(
            'dataArr'   => $paramArr, #数据
            'dataType'  => 0,         #0=>当前字符串 1=>数值
        );
        $options = array_merge($options, $paramArr);
        extract($options);
        
        $chartString = '[';
        foreach ($dataArr as $val) { if(!is_array($val)) {
            switch ($dataType) {
                case 0: $chartString .= "'{$val}', ";break;
                case 1: $chartString .= "{$val}, ";break;
            }
        } } 
        $chartString .= ']';
        $chartString = str_replace(', ]', ']', $chartString);
        return $chartString;
    }

    /**
     * 获取某年某月的天数
     * @param type $year
     * @param type $month
     * @return int
     */
    public static function dayNum($year,$month)
    {
        $big_month=array(1,3,5,7,8,10,12);
        $sm_month=array(4,6,9,11);
        if(in_array($month,$big_month)){
            $day_num=31;
        }  else if(in_array($month,$sm_month))  {
            $day_num=30;
        }  else  {
            if($year%4==0 && ($year%100!=0 || $year%400==0)) {//闰年
                $day_num=29;
            }   else    {
                $day_num=28;
            }
        }
        return $day_num;
    }
    
    /**
     * 检查日期是否合法
     * @param type $year
     * @param type $month
     * @param type $day
     * @return bool
     */
    public static function checkDate($year, $month, $day) 
    {
        if($year<0) { return false; }
        if($month<0 || $month>12) { return false; }
        if($day<1) { return false;}
        if(self::dayNum($year, $month)<$day) { return false; }
        return true;
    }
    /**
     * 拆分二元组
     */
    public static function splitTuple($paramArr = array())
    {
        $options = array(
            'tuple'     =>'',       #元组字符串
            'unitSep'    => ';',    #单位分隔符
            'fieldSep'  =>','       #字段分隔
        );
        $options = array_merge($options, $paramArr);
        extract($options);
        
        $ret = array();
        if($tuple) {
            $tuple = str_replace(array('(', ')', '{', '}'), '', $tuple);
            $unitArr = explode($unitSep, $tuple);
            foreach ($unitArr as $row) {
                $tmp = explode($fieldSep, $row);
                $val = count($tmp)>2 ? $tmp : $tmp[1];
                $ret[$tmp[0]] = $val;
            }
        }
        return $ret;
    }
    /**
     * 扫描文件
     */
    public static function myscandir($paramArr = array()){
        $options = array(
            'pathName' => "",
            'includeExt' => array('php'),
        );
        $options = array_merge($options, $paramArr);
        extract($options);

        $fileArr = array();
        foreach( glob($pathName) as $filename ){
            if (is_dir($filename)){
                $tmpFileArr = self::myscandir(array("pathName"=>$filename.'/*', 'includeExt'=>$includeExt));
                if ($tmpFileArr) { $fileArr = array_merge($fileArr, $tmpFileArr); }
            } else {
                $fileExt = end(explode('.', $filename));
                if($includeExt && in_array($fileExt, $includeExt) || !$includeExt) $fileArr[] = $filename;
            }
        }
        return $fileArr;
    }
    /**
     * 图片路径替换
     * @param type $src
     * @param type $size
     * @return string 
     */
	public static function getImgSrc($src, $size = '_80x60')
	{
		$defaultSize = '_80x60';
		$size = ($size{0} != '_') ? ('_' . $size) : $size;
        #$newDefaultSize，针对fastDFS上的图片，尺寸格式为s80x60、s160x120等 add by qianzw
        #例如http://i2.prosmall.fd.zol-img.com.cn/t_s80x60/g4/M04/09/06/Cg-4WVC0tROIWxwSAAAa0cdttYgAAClWQEWdxIAABrp712.jpg
        $newDefaultSize = 's80x60';
        $newSize = 's'.ltrim($size, '_');
        $newSizeRange = array('s40x30','s60x45','s80x60','s100x75','s120x90','s160x120','s280x210','s240x180','s200x150','s400x300');

		if ($src) {
            #旧链接格式或非指定尺寸
            $oldMethod = preg_match("/\d+\/[a-zA-Z0-9]{1,15}\.[a-z]{3,}$/", $src) || !in_array($newSize, $newSizeRange);
            if($oldMethod) {
                $src = $size != $defaultSize ? str_replace($defaultSize, $size, $src) : $src;
            } else {
                $src = $newSize != $newDefaultSize ? str_replace($newDefaultSize, $newSize, $src) : $src;
            }

			if(strpos($src,".img") !== false){ #旧图片的特殊处理
				$src = 'http://icon.zol-img.com.cn/detail/no_pic/no' . $size . '.gif';
			}
		} else {
			$src = 'http://icon.zol-img.com.cn/detail/no_pic/no' . $size . '.gif';
		}
		return $src;
	}
    /**
     * 将k-val数组值的首字符显示出来
     */
    public static function firstCharArr($paramArr = array())
    {
        $options = array(
            'data' => array()
        );
        $options = array_merge($options, $paramArr);
        extract($options);
        
        if($data) {
            foreach ($data as $key=>&$val){
                $val = trim($val);
                $firstChar = ZOL_Api::run("Base.String.getFirstLetter" , array('input'=>$val));
                $val = $firstChar.'_'.$val;
            }
            asort($data);
        }
        return $data;
    }
    /**
     * 人性化显示时间(今天 13:20)
     */
    public static function getHumanDate($tm)
    {
        if(date('Y-m-d', $tm)==date('Y-m-d')) {
            return "今天 ".date('H:i', $tm);
        }
        if($tm<0)$tm = SYSTEM_TIME;
        return date('m月d日', $tm);
    }

    /**
     * 人性化显示时间（时分秒）
     * 1. 1小时之内显示多少分钟之前
     * 2. 24小时之内显示多少小时之前
     * 3. 其它显示日期(7月2日)
     */
    public static function getHumanHour($tm)
    {
        $text = '';

        $diffTm = time()-$tm;
        if($diffTm<3600) { $text = ceil($diffTm/60) . '分钟前'; }
        elseif($diffTm<24*3600) { $text = ceil($diffTm/3600) . '小时前'; }
        else {
            if($tm<0)$tm = SYSTEM_TIME;
            $text = date('m月d日', $tm);
        }
        return $text;
    }
    
    
    /**
     * 得到所有最大时间辍
     */
    public static function getTopTmArr($paramArr=array())
    {
        $options = array(
            'dataArr'       => array(),
            'tmField'       => 'tm',
            'direct'        => 'max',       #最大 or 最小
        );
        $options =  array_merge($options, $paramArr);
        extract($options);
        
        $tmArr = array();
        if($dataArr) {
            foreach ($dataArr as $row) {
                if(isset($row['sourceSite']) && $row['sourceSite']=='allnet') { continue; }
                $type = $row['type'];
                $tm = $row[$tmField];
                
                if(!isset($tmArr[$type])) { $tmArr[$type] = $tm; }
                else {
                    if($direct=='max' && $tmArr[$type]<$tm) { $tmArr[$type] = $tm; }
                    elseif($direct=='min' && $tmArr[$type]>$tm) { $tmArr[$type] = $tm; }
                }
            }
        }
        return $tmArr;
    }
    /**
     * 得到最大时间辍字符串
     */
    public static function getTmStr($paramArr)
    {
        $options = array(
            'tmArr'=>array()
        );
        $options = array_merge($options, $paramArr);
        extract($options);
        
        $tmStr = "";
        if($tmArr) {
            foreach ($tmArr as $type=>$tm) {
                if($tmStr) { $tmStr .= ","; }
                $tmStr .= $type.'-'.$tm;
            }
        }
        return $tmStr;
    }
	/**
	* 解析rewrite后的JS参数
	*
	* @param string $queryString
	*/
	public static function parseRewriteJsUrl($queryString)
	{
		$queryString = str_replace('^', '&', $queryString);
		parse_str($queryString, $queryString);
		return $queryString;
	}
    /**
     * 得到科技头条文章链接
     */
    public static function getArticleUrl($paramArr=array())
    {
        $options = array(
            'docId' => 0,       #文章id
            'cid'   => '',      #类别
        );
        $options = array_merge($options, $paramArr);
        extract($options);

        $url = $cid ? '/'.$cid.$docId : '/'.$docId;
        return $url;
    }
    /**
     * 去除文章杂7杂8内容
     */
    public static function getFormatContent($paramArr) {
         $options = array(
            'content'       => '', #文章内容
        );
        $options = array_merge($options, $paramArr);
        extract($options);

        if (!$content) return '';
        $content = preg_replace("'<script[^>]*?>.*?</script>'si", "", $content);
        /* 过滤掉除去p 图片，换行 表格 视频 以外的所有标签 */
        $content = strip_tags($content,"<p><img><br><table><th><tr><td><EMBED><a>");
        /*对剩余标签中的一些属性过滤掉*/
        $content = preg_replace("/<p([^>]*)>/is","<p>", $content);
        $content = preg_replace("/<br([^>]*)>/is","<br/>", $content);
        $content = preg_replace("/<table([^>]*)>/is","", $content);
        $content = preg_replace("/<tr([^>]*)>/is","<tr>", $content);
        $content = preg_replace("/\s(style|width|align|class|id|name|bgcolor|background)=([\"][^\"]*[\"]|['][^']*[']|[^\s\">']*)/is","", $content);
        $content = str_ireplace(array("</table>", "</tr>", "</td>"), array("</table>", "</tr>", "</td>"), $content);
        $content = preg_replace("/<img.+?src=\"?([^\" ]+)\"?[^>]+>/is", "<img src=\"\\1\" />", $content);
        $content = preg_replace("/src=\"http:\/\/img2.zol.com.cn\/product\/(\d+)(_\d+x\d+)?\/(\d+)\/([^\"]+)\"/is", "src=\"http://img2.zol.com.cn/product/\\1_120x90/\\3/\\4\"", $content);
        /*过滤掉重复的换行，空格，特殊字符*/
        $content = str_replace("\$", "\$\$", $content);
        $content = str_replace("\r", "\n", $content);
        $content = str_ireplace(array("&nbsp;","&nbsp"), " ", $content);
        $content = str_ireplace(array("&#160;", "&amp;#160;"), " ", $content);
        $content = preg_replace("/(\n)+/is", "\n",$content);
        /*将所有的P标签换成br标签*/
        $content = str_ireplace(array("<p>", "</p>"), "<br/>", $content);
        $content = preg_replace("/(<br\/>\s*){2,}/", "<br/>", $content);
        /*将所有的br进行整理*/
        $content_arr = array();
        $content_row = explode('<br/>',$content);
        $imageTag = 0;
        foreach ($content_row as $key => $values) {
            if ($values) {
                $values = str_replace('&#160;',' ', $values);
                $values = str_replace('&amp;#160;',' ', $values);
                $values = trim(htmlspecialchars_decode($values), " ");
                $values = mb_ereg_replace('^(　| )+', '', $values);  //通过正则强制删除空格
                if ( strstr($values,'img')) { //对图片下面的图片标题做判断
                    $content_arr[] = '<p>'.$values.'</p>';
                    $imageTag = 1;
                } elseif ($imageTag) {
                    if (strlen($values) < 60) {
                            $content_arr[] = '<b style="display:block; text-align: center;margin-top:-8px;font-size:14px">'.$values.'</b>';
                    } else {
                            $content_arr[] = '<p>'.$values.'</p>';
                    }
                    $imageTag = 0;
                } else {
                    $content_arr[] = '<p>'.$values.'</p>';
                }
            }
        }
        $content = implode('',$content_arr);
        return $content;
    }
    
    /**
    * 获得新的图片地址
    * @param $module 模块，比如 dg , pro
    * @param $fileName 文件 /g3/M05/0E/03/Cg-4WFCkmziIaA9KAABYzyJLDfwAAB4lgPCnb0AAFjn012.jpg
    * @param $size 尺寸 如120x90
    * ### 调用方法 ### getNewImgUrl("dg",$pic_1); dg表明是手工，其他业务不同名称
    */
    public static function getNewImgUrl($module,$fileName,$size=false){
        if(!$module || !$fileName)return '';


        $partUri = "/".ltrim($fileName,"/");
        if($size) $partUri = "/t_s" . $size . $partUri;


        $rd  = ord(substr($partUri, -5,1)) % 6;
        return "http://i{$rd}.{$module}.fd.zol-img.com.cn" . $partUri;
    }
    /**
     * 得到标准图片
     */
    public static function getStandImg($imgurl, $size='220x140'){
        $imgApi = "http://image.zol.com.cn/head/pic.php?imgUrl=" . $imgurl;
        //echo $imgApi,'<br>';
        $response = ZOL_Http::curlPage(array('url'=>$imgApi));                
        if($response) {
            $response = json_decode($response, true);
            if($size && isset($response[$size])) return $response[$size];
        }
        return '';
    }
    public static function getTechImg($imgurl, $size='220x140'){//_s400x2000
        return str_replace("_s400x2000","_s".$size,$imgurl);
    }
}
