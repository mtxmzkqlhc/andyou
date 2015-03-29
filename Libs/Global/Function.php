<?php
/**
* ���ֿ�������Ҫ����
* @author Ǯ־ΰ <qian.zhiwei@zol.com.cn>
* @copyright (c) 2013-02-18
* @version v1.0
*/
class Libs_Global_Function
{
    /**
     * ����������͵ı�ǩ
     * @link http://www.bootcss.com/components.html#labels-badges
     * @param type $paramArr
     * @return string 
     */
    function outputHtmlLabel($paramArr=array())
    {
        $options = array(
            'msg'    => '', #�ı�
            'type'   => '', #��ʽ
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
     * �Զ�ά���飬��ָ���ֶ�����Ĭ�Ͻ���
     * @param array $array
     * @param string $field
     * @param int $asc
     * @return array 
     */
    public static function sort2Array($paramArr = array()) 
    {
        $options = array(
            'data'  => array(), #����
            'field' => 'time',  #�������ݵ��ֶ�
            'asc'   => 0,       #0=>���� 1=>����
        );
        $options = array_merge($options, $paramArr);
        extract($options);

        $data=array_values($data);
        #��������
        $len = count($data);#���һ��Ԫ���±�
        for ($i=1; $i<$len; $i++) {
            $tmp  = $data[$i];
            $j = $i - 1;
            if ($asc) { #����
                while ($j>=0 && $data[$j][$field]>$tmp[$field]) {
                    $data[$j+1] = $data[$j];
                    $j--;
                }
            } else { #����
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
     * ��һά�����ɶ�ά���飬��Ҫ����һ�е����ݱ�ɶ���
     * @author qianzhiwei
     * @param type $paramArr
     * @return type 
     */
    public static function splitArray($paramArr = array()) 
    {
        $options = array(
            'data'  => array(), #����
            'colNum'=> 5,       #����
            'pad'   => 1,       #�����
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
     * ������ֵת��Ϊͼ������Ҫ�ַ��������� array('����', '�Ϻ�', '����') ת�� "['����', '�Ϻ�', '����']"
     * @param type $paramArr
     * @return type 
     */
    public static function array2ChartString($paramArr)
    {
        $options = array(
            'dataArr'   => $paramArr, #����
            'dataType'  => 0,         #0=>��ǰ�ַ��� 1=>��ֵ
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
     * ��ȡĳ��ĳ�µ�����
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
            if($year%4==0 && ($year%100!=0 || $year%400==0)) {//����
                $day_num=29;
            }   else    {
                $day_num=28;
            }
        }
        return $day_num;
    }
    
    /**
     * ��������Ƿ�Ϸ�
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
     * ��ֶ�Ԫ��
     */
    public static function splitTuple($paramArr = array())
    {
        $options = array(
            'tuple'     =>'',       #Ԫ���ַ���
            'unitSep'    => ';',    #��λ�ָ���
            'fieldSep'  =>','       #�ֶηָ�
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
     * ɨ���ļ�
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
     * ͼƬ·���滻
     * @param type $src
     * @param type $size
     * @return string 
     */
	public static function getImgSrc($src, $size = '_80x60')
	{
		$defaultSize = '_80x60';
		$size = ($size{0} != '_') ? ('_' . $size) : $size;
        #$newDefaultSize�����fastDFS�ϵ�ͼƬ���ߴ��ʽΪs80x60��s160x120�� add by qianzw
        #����http://i2.prosmall.fd.zol-img.com.cn/t_s80x60/g4/M04/09/06/Cg-4WVC0tROIWxwSAAAa0cdttYgAAClWQEWdxIAABrp712.jpg
        $newDefaultSize = 's80x60';
        $newSize = 's'.ltrim($size, '_');
        $newSizeRange = array('s40x30','s60x45','s80x60','s100x75','s120x90','s160x120','s280x210','s240x180','s200x150','s400x300');

		if ($src) {
            #�����Ӹ�ʽ���ָ���ߴ�
            $oldMethod = preg_match("/\d+\/[a-zA-Z0-9]{1,15}\.[a-z]{3,}$/", $src) || !in_array($newSize, $newSizeRange);
            if($oldMethod) {
                $src = $size != $defaultSize ? str_replace($defaultSize, $size, $src) : $src;
            } else {
                $src = $newSize != $newDefaultSize ? str_replace($newDefaultSize, $newSize, $src) : $src;
            }

			if(strpos($src,".img") !== false){ #��ͼƬ�����⴦��
				$src = 'http://icon.zol-img.com.cn/detail/no_pic/no' . $size . '.gif';
			}
		} else {
			$src = 'http://icon.zol-img.com.cn/detail/no_pic/no' . $size . '.gif';
		}
		return $src;
	}
    /**
     * ��k-val����ֵ�����ַ���ʾ����
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
     * ���Ի���ʾʱ��(���� 13:20)
     */
    public static function getHumanDate($tm)
    {
        if(date('Y-m-d', $tm)==date('Y-m-d')) {
            return "���� ".date('H:i', $tm);
        }
        if($tm<0)$tm = SYSTEM_TIME;
        return date('m��d��', $tm);
    }

    /**
     * ���Ի���ʾʱ�䣨ʱ���룩
     * 1. 1Сʱ֮����ʾ���ٷ���֮ǰ
     * 2. 24Сʱ֮����ʾ����Сʱ֮ǰ
     * 3. ������ʾ����(7��2��)
     */
    public static function getHumanHour($tm)
    {
        $text = '';

        $diffTm = time()-$tm;
        if($diffTm<3600) { $text = ceil($diffTm/60) . '����ǰ'; }
        elseif($diffTm<24*3600) { $text = ceil($diffTm/3600) . 'Сʱǰ'; }
        else {
            if($tm<0)$tm = SYSTEM_TIME;
            $text = date('m��d��', $tm);
        }
        return $text;
    }
    
    
    /**
     * �õ��������ʱ���
     */
    public static function getTopTmArr($paramArr=array())
    {
        $options = array(
            'dataArr'       => array(),
            'tmField'       => 'tm',
            'direct'        => 'max',       #��� or ��С
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
     * �õ����ʱ����ַ���
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
	* ����rewrite���JS����
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
     * �õ��Ƽ�ͷ����������
     */
    public static function getArticleUrl($paramArr=array())
    {
        $options = array(
            'docId' => 0,       #����id
            'cid'   => '',      #���
        );
        $options = array_merge($options, $paramArr);
        extract($options);

        $url = $cid ? '/'.$cid.$docId : '/'.$docId;
        return $url;
    }
    /**
     * ȥ��������7��8����
     */
    public static function getFormatContent($paramArr) {
         $options = array(
            'content'       => '', #��������
        );
        $options = array_merge($options, $paramArr);
        extract($options);

        if (!$content) return '';
        $content = preg_replace("'<script[^>]*?>.*?</script>'si", "", $content);
        /* ���˵���ȥp ͼƬ������ ��� ��Ƶ ��������б�ǩ */
        $content = strip_tags($content,"<p><img><br><table><th><tr><td><EMBED><a>");
        /*��ʣ���ǩ�е�һЩ���Թ��˵�*/
        $content = preg_replace("/<p([^>]*)>/is","<p>", $content);
        $content = preg_replace("/<br([^>]*)>/is","<br/>", $content);
        $content = preg_replace("/<table([^>]*)>/is","", $content);
        $content = preg_replace("/<tr([^>]*)>/is","<tr>", $content);
        $content = preg_replace("/\s(style|width|align|class|id|name|bgcolor|background)=([\"][^\"]*[\"]|['][^']*[']|[^\s\">']*)/is","", $content);
        $content = str_ireplace(array("</table>", "</tr>", "</td>"), array("</table>", "</tr>", "</td>"), $content);
        $content = preg_replace("/<img.+?src=\"?([^\" ]+)\"?[^>]+>/is", "<img src=\"\\1\" />", $content);
        $content = preg_replace("/src=\"http:\/\/img2.zol.com.cn\/product\/(\d+)(_\d+x\d+)?\/(\d+)\/([^\"]+)\"/is", "src=\"http://img2.zol.com.cn/product/\\1_120x90/\\3/\\4\"", $content);
        /*���˵��ظ��Ļ��У��ո������ַ�*/
        $content = str_replace("\$", "\$\$", $content);
        $content = str_replace("\r", "\n", $content);
        $content = str_ireplace(array("&nbsp;","&nbsp"), " ", $content);
        $content = str_ireplace(array("&#160;", "&amp;#160;"), " ", $content);
        $content = preg_replace("/(\n)+/is", "\n",$content);
        /*�����е�P��ǩ����br��ǩ*/
        $content = str_ireplace(array("<p>", "</p>"), "<br/>", $content);
        $content = preg_replace("/(<br\/>\s*){2,}/", "<br/>", $content);
        /*�����е�br��������*/
        $content_arr = array();
        $content_row = explode('<br/>',$content);
        $imageTag = 0;
        foreach ($content_row as $key => $values) {
            if ($values) {
                $values = str_replace('&#160;',' ', $values);
                $values = str_replace('&amp;#160;',' ', $values);
                $values = trim(htmlspecialchars_decode($values), " ");
                $values = mb_ereg_replace('^(��| )+', '', $values);  //ͨ������ǿ��ɾ���ո�
                if ( strstr($values,'img')) { //��ͼƬ�����ͼƬ�������ж�
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
    * ����µ�ͼƬ��ַ
    * @param $module ģ�飬���� dg , pro
    * @param $fileName �ļ� /g3/M05/0E/03/Cg-4WFCkmziIaA9KAABYzyJLDfwAAB4lgPCnb0AAFjn012.jpg
    * @param $size �ߴ� ��120x90
    * ### ���÷��� ### getNewImgUrl("dg",$pic_1); dg�������ֹ�������ҵ��ͬ����
    */
    public static function getNewImgUrl($module,$fileName,$size=false){
        if(!$module || !$fileName)return '';


        $partUri = "/".ltrim($fileName,"/");
        if($size) $partUri = "/t_s" . $size . $partUri;


        $rd  = ord(substr($partUri, -5,1)) % 6;
        return "http://i{$rd}.{$module}.fd.zol-img.com.cn" . $partUri;
    }
    /**
     * �õ���׼ͼƬ
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
