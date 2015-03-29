<?php
/**
* ǿ��ķ�ҳ��
* ����ģ�幦��
* @example 
*	$page = new Page(array('total'=>500));
*	echo $page->display(1);
*	echo '<br/>' . $page->display('{PREV:[��һҳ]}{NEXT:[��һҳ]}');
* @author wiki
* @copyright (c) 2009-4-1
*/
class Libs_Global_Page {
	
	protected $pageKey = 'page';

    protected $placeHd = '{PAGE}';

	/**
	* ģ��
	* @var string
	*/
	protected $Template = '';
	
	/**
	* ��ǰҳ
	* @var integer
	*/
	protected $Page = 1;
	
	/**
	* ÿҳ��ʾ����Ŀ
	* @var integer
	*/
	protected $RowNum = 20;
	
	/**
	* ����
	* @var integer
	*/
	protected $Total = 0;
	
	/**
	* ��ҳ��
	*/
	protected $TotalPage = 0;
	
	/**
	* ��û��ҳ��Bar
	*/
	protected $existBar = false;

	/**
	* ��ǰҳ�뽹��ƫ����
	* @var integer
	*/
	protected $OffsetNum = 0;
	
	/**
	* ����ҳ�����м���ʾ��
	* @var integer
	*/
	protected $BarLength = 0;
	
	protected $Url = '';
	
	
	protected $target = '';
	protected $jsOnclick = '';
    
	/**
	* ϵͳ��ǩ
	*/
	protected $FirstTag   = 'FIRST';
	protected $PrevTag    = 'PREV';
	protected $PrevHdTag  = 'PREVHD';#��һҳ��������
	protected $BarTag     = 'BAR';
	protected $NextTag    = 'NEXT';
	protected $NextHdTag  = 'NEXTHD';#��һҳ��������
	protected $LasttTag   = 'LAST';
	protected $CurrentTag = 'CURRENT';
	protected $TotalTag   = 'TOTAL';
	
	/**
	* ҳ���ǩ
	*/
	protected $NumTag  = '[NUM]';
	
	protected $TagsVal = array();
	protected $Label   = array();
	/**
	* ��ǩ�ָ��
	* 
	*/
	protected $TagDelimiter = '|';
	
	/**
	* ����-ֵ�ָ��
	*/
	protected $AttrDelimiter = ':';
	
	/**
	* ����ģ����ʽģ��
	* {TAGS} = $this->Tag
	* {ATTRDELIMITER} = $this->AttrDelimiter
	*/
	protected $RegexTemplate = '{(({TAGS}){ATTRDELIMITER}([^}]+))}';
	
	protected  $Style = array(
					'FIRST'   => '',
					'PREV'    => '',
					'PREVHD'  => '',
					'BAR'     => '',
					'NEXT'    => '',
					'NEXTHD'  => '',
					'LAST'    => '',
					'CURRENT' => '',
					'TOTAL'   => '',
				);
	
	/**
	* �Ƿ���������ģʽ �Զ����������ǩ��ʾ
	* 
	* @var mixed
	*/
	protected $SmartShow = array(
					'FIRST'   => true,
					'PREV'    => false,
					'PREVHD'  => true,
					'BAR'     => true,
					'NEXT'    => false,
					'NEXTHD'  => true,
					'LAST'    => true,
					'CURRENT' => true,
					'TOTAL'   => true,
				);
	
	#��ʼҳ��
	private $startPage;
	
	#����ҳ��
	private $endPage;
	
	private $startOver = true;#��ͷ
	
	private $endOver = false;#����
	
	private $startPageOver = true;#��ǰҳ�Ƿ�����ҳ
	private $endPageOver   = false;#��ǰҳ�Ƿ�����β
    private $firstPageUrl  = '';#��һҳURL
	/**
	* @param array $opiton = array('page'=>1, 'rownum'=>20, 'total'=>542, 'template'=>'');
	*/
	public function __construct($option)
	{
		if (is_array($option)) {
			if (!array_key_exists('total', $option)) {
				return false;
			}
			
			$this->Total = intval($option['total']);
			isset($option['rownum'])     && ($this->RowNum = intval($option['rownum']));
			isset($option['offsetnum'])  && ($this->OffsetNum = intval($option['offsetnum']));
			isset($option['barlength'])  && ($this->BarLength = intval($option['barlength']));
			isset($option['pagekey'])    && ($this->pageKey = $option['pagekey']);
			isset($option['template'])   && ($this->Template = $option['template']);
			isset($option['target'])     && ($this->target = $option['target']);
            isset($option['jsOnclick'])      && ($this->jsOnclick = $option['jsOnclick']);
			
			$page = isset($option['page']) ? (int)$option['page'] : 0;
			$url = isset($option['url']) ? $option['url'] : '';

		} else {
			$this->Total = intval($option);
			$page = 0;
			$url = '';
		}
		
		if (!$this->Total) {
			return false;
		}
		$this->_setPage($page);
		$this->_setUrl($url);
		$this->TotalPage = ceil($this->Total/$this->RowNum);
		
		$this->startPageOver = ($page <= 1);
		$this->endPageOver = ($page >= $this->TotalPage);
	}
    /**
     * ���õ�һҳurl��Ϊ��seo����
     */
	public function setFirstPageUrl($url){
        $this->firstPageUrl = $url;
    }
	/**
	* ������ʽ
	* @return ZOL_Product_Lib_Page
	*/
	public function setStyle(array $style) {
		if (!$style) {
			return false;
		}
		$this->Style = array_merge($this->Style, $style);
		return $this;
	}
	
	/**
	* ���ñ�ǩ��ʾģʽ
	* @return ZOL_Product_Lib_Page
	*/
	public function setSmartShow($smartShow) {
		$this->SmartShow = is_array($smartShow) ? array_merge($this->SmartShow, $smartShow) : $smartShow;
		return $this;
	}
	
	public function get($var)
	{
		if (property_exists($this, $var)) {
			return $this->$var;
		} else {
			//trow error
		}
	}
	
	/**
	* ��������ֵ
	*/
	public function set($var, $value)
	{
		if (property_exists($this, $var)) {
			$this->$var = $value;
		} else {
			//trow error
		}
		return $this;
	}
	
	/**
	* ���õ�ǰҳ
	*/
	protected function _setPage($page)
	{
		$page = (int)$page;
		
		if ($page>0) {
			$this->Page = $page;
		} else {
			$this->Page = 1;
		}
	}
	
	/**
	* ����URLͷ
	*/
	protected function _setUrl($url='')
	{
        if (strpos($url, $this->placeHd) !== false) {
            $this->Url = $url;
            return true;
        }

		$queryString = '';
		
		if (!empty($url)) {
			if (($offset = strpos($url, '?')) !== false) {
				$queryString = substr($url, $offset+1);
				$url = substr($url, 0, $offset);
			}
		} else {
			$queryString = $_SERVER['QUERY_STRING'];
			$url = $_SERVER['PHP_SELF'];
		}
		
		if ($queryString) {
			parse_str($queryString, $query);
            unset($query[$this->pageKey]);
			
			if ($query) {
				$this->Url = $url . '?' . str_replace('&', '&amp;', http_build_query($query)) . '&amp;' . $this->pageKey . '=';
			} else {
				$this->Url = $url . '?' . $this->pageKey . '=';
			}
		} else {
			$this->Url = $url;
		}
        return true;
	}
	
	/**
	* ����ģ��
	* @param string $code ģ������
	*/
	protected function parseTemplate($code='')
	{
		$existBar = false;
		$label = array();
		$code || ($code = $this->Template);
		$tags[] = $this->FirstTag;
		$tags[] = $this->PrevTag;
		$tags[] = $this->PrevHdTag;
		$tags[] = $this->BarTag;
		$tags[] = $this->NextTag;
		$tags[] = $this->NextHdTag;
		$tags[] = $this->LasttTag;
		$tags[] = $this->CurrentTag;
		$tags[] = $this->TotalTag;

		$regex = str_replace(array('{TAGS}', '{ATTRDELIMITER}'),
							array(join('|', $tags), $this->AttrDelimiter),
							$this->RegexTemplate);
		preg_match_all("/{$regex}/s", $code, $result, PREG_SET_ORDER);
		foreach ($result as $re) {
			$_tags[$re[2]] = $re[3];
			//�����м��ҳ������
			if ($re[3] && strpos($re[3], $this->AttrDelimiter) && $re[2] == $this->BarTag) {
				$_result = explode($this->AttrDelimiter, $re[3]);
				$_tags[$re[2]]   = $_result[0];
				
				$this->BarLength || ($this->BarLength = $_result[1]);
				$this->OffsetNum || ($this->OffsetNum = $_result[2]);
				$existBar = true;
			}
			$label[$re[2]] = $re[0];
		}
		$this->existBar = $existBar;
		$this->Label = $label;
		$this->TagsVal = $_tags;
		
		$this->interval();
		return $_tags;
	}
	
	/**
	* ҳ��������
	*/
	private function interval()
	{
		if (!$this->existBar || $this->BarLength < 1) {
			return false;
		}
		
		if ($this->BarLength >= $this->TotalPage) {
			$start = 1;
			$end   = $this->TotalPage;
		} else {
			$start = $this->Page - $this->OffsetNum;
			$end   = $start + $this->BarLength;
			$end   = $start < 1 ? $this->BarLength : ($end - 1);
			
			$start = ($end > $this->TotalPage) ? ($start - ($end - $this->TotalPage)) : $start;
			
			$start = max($start, 1);
			$end   = min($end, $this->TotalPage);
		}
		
		$this->startPage = $start;
		$this->endPage   = $end;
		
		$this->startOver = ($start == 1);
		$this->endOver   = ($end == $this->TotalPage);
	}
	
	/**
	* ��һҳ
	*/
	public function firstTag($style='')
	{
		if (empty($this->TagsVal[$this->FirstTag])) {
			return false;
		}
		
		if ((!empty($this->SmartShow['FIRST']) || $this->SmartShow === true) && $this->startOver) {
			return false;
		}
		
		if ($this->startPageOver) {
			return false;
		}
		
		$style = isset($this->Style['FIRST']) ? $this->Style['FIRST'] : $style;
		
		
		$num = 1;
		if ($this->TotalPage==1) {
			return false;
		}
		$btn = $this->_getText($this->FirstTag, $num);
		
		if ($this->Page > 1) {
			return $this->_getLink($this->_getUrl($num), $btn, $style);
		}
		return $this->_getNoLink($btn, $style);
	}
	
	/**
	* ��һҳ
	*/
	public function prevTag($style='')
	{
		if (empty($this->TagsVal[$this->PrevTag])) {
			return false;
		}
		
		if ((!empty($this->SmartShow['PREV']) || $this->SmartShow === true) && $this->startOver) {
			
			return false;
		}
		
		if ($this->startPageOver) {
			return false;
		}
		
		$style = isset($this->Style['PREV']) ? $this->Style['PREV'] : $style;
		$num = $this->Page-1;
		$btn = $this->_getText($this->PrevTag, $num);
		if ($this->Page > 1) {
			return $this->_getLink($this->_getUrl($num), $btn, $style);
		}
		return $this->_getNoLink($btn, $style);
	}
	
	/**
	* ��һҳ������
	*/
	public function prevhdTag($style='')
	{
		if (empty($this->TagsVal[$this->PrevHdTag])) {
			return false;
		}
		
		if ((!empty($this->SmartShow['PREVHD']) || $this->SmartShow === true) && $this->startOver) {
			return false;
		}
		
		if ($this->startPageOver) {
			return false;
		}
		
		if ($this->startPage < 3) {
			return false;
		}
		
		$style = isset($this->Style['PREVHD']) ? $this->Style['PREVHD'] : $style;
		
		$num = $this->Page-1;
		$btn = $this->_getText($this->PrevHdTag, $num);
		return $this->_getNoLink($btn, $style);
	}
	
	/**
	* ��ǰ��ҳ��
	*/
	public function barTag($style='')
	{
		if (!$this->existBar || $this->BarLength < 1) {
			return false;
		}
		
		$style = isset($this->Style['BAR']) ? $this->Style['BAR'] : $style;
		
		$this->interval();
		
		$bar = '';
		
		for ($i = $this->startPage; $i <= $this->endPage; $i++) {
			if ($i > $this->TotalPage) {
				break;
			} elseif ($i < 1) {
				continue;
			}
			$btn = $this->_getText($this->BarTag, $i);
			if ($i == $this->Page) {
				$bar .= $this->_getNoLink($btn, 'sel');
			} else {
				$bar .= $this->_getLink($this->_getUrl($i), $btn, $style);
			}
			

		}
		return $bar;
	}
		
	/**
	* ��һҳ
	*/
	public function nextTag($style='')
	{
		if (empty($this->TagsVal[$this->NextTag])) {
			return false;
		}
		
		$num = $this->Page+1;
		if ((!empty($this->SmartShow['NEXT']) || $this->SmartShow === true) && $this->endOver) {
			return false;
		}
		if ($this->endPageOver) {
			return false;
		}
		
		$style = isset($this->Style['NEXT']) ? $this->Style['NEXT'] : $style;
		$btn = $this->_getText($this->NextTag, $num);
		if ($this->Page < $this->TotalPage) {
			return $this->_getLink($this->_getUrl($num), $btn, $style);
		}
		return $this->_getNoLink($btn, $style);
	}
	
	/**
	* ��һҳ������
	*/
	public function nexthdTag($style='')
	{
		if (empty($this->TagsVal[$this->NextHdTag])) {
			return false;
		}
		
		$num = $this->Page+1;
		
		if ((!empty($this->SmartShow['NEXTHD']) || $this->SmartShow === true) && $this->endOver) {
			return false;
		}
		
		if ($this->endPageOver) {
			return false;
		}
		
		if ($this->endPage + 2 > $this->TotalPage) {
			return false;
		}
		
		$style = isset($this->Style['NEXTHD']) ? $this->Style['NEXTHD'] : $style;
		
		$btn = $this->_getText($this->NextHdTag, $num);
		return $this->_getNoLink($btn, $style);
	}

	
	/**
	* ���һҳ
	*/
	public function lastTag($style='')
	{
		if (empty($this->TagsVal[$this->LasttTag])) {
			return false;
		}
		
		$num = $this->TotalPage;
		
		if ((!empty($this->SmartShow['LAST']) || $this->SmartShow === true) && $this->endOver) {
			return false;
		}
		
		if ($this->endPageOver) {
			return false;
		}
		
		$style = isset($this->Style['LAST']) ? $this->Style['LAST'] : $style;
		
		$btn = $this->_getText($this->LasttTag, $num);
		if ($this->Page < $this->TotalPage) {
			return $this->_getLink($this->_getUrl($num), $btn, $style);
		}
		return $this->_getNoLink($btn, $style);
	}
	
	/**
	* ��ǰҳ
	*/
	public function currentTag($style='')
	{
		if (empty($this->TagsVal[$this->CurrentTag])) {
			return false;
		}
		
		$style = isset($this->Style['CURRENT']) ? $this->Style['CURRENT'] : $style;
		
		$num = $this->Page;
		$btn = $this->_getText($this->TotalTag, $num);
		if ($style) {
			$btn = $this->_getNoLink($btn, $style);
		}
		
		return $btn;
	}
	
	/**
	* ��ҳ
	*/
	public function totalTag($style='')
	{
		if (empty($this->TagsVal[$this->TotalTag])) {
			return false;
		}
		
		$style = isset($this->Style['TOTAL']) ? $this->Style['TOTAL'] : $style;
		
		$num = $this->TotalPage;
		$btn = $this->_getText($this->TotalTag, $num);
		if ($style) {
			$btn = $this->_getNoLink($btn, $style);
		}
		
		return $btn;
	}
	
	
	public function pageInfo($style='')
	{
		return '��<span class="' . $style . '">' . $this->Total . '</span>�� ��<span class="' . $style . '">' . $this->Page . '</span>/' . $this->TotalPage . 'ҳ';
	}
	
	/**
	* ��ȡ��������
	* @param string $tag �����ַ���
	* @param integer $page ҳ��ֵ
	*/
	public function _getText($tag, $page='')
	{
		return isset($this->TagsVal[$tag])
				? str_replace($this->NumTag, $page, $this->TagsVal[$tag])
				: $page;
	}
	/**
	* ��ȡURL����
	*/
	public function _getUrl($page=1)
	{
        if($this->firstPageUrl && $page== 1){
            return $this->firstPageUrl;
        }
		if (strpos($this->Url, '{PAGE}') !== false) {
			return str_replace('{PAGE}', $page, $this->Url);
		}
		
		return $this->Url . $page;
	}
	/**
	* ��ȡ���Ӱ�ť
	*/
	public function _getLink($url, $text, $style='')
	{
		$style = empty($style) ? '' : "class=\"{$style}\"";
		return '<a href="' . $url . '" ' . $style 
			. ($this->target ? (' target="' . $this->target . '"') : '') 
            . ($this->jsOnclick ? (' onclick="' . $this->jsOnclick . '"') : '') 
			. '>' . $text . '</a>';
	}
	
	public function _getNoLink($text, $style='')
	{
		$style = empty($style) ? '' : "class=\"{$style}\"";
		return '<span ' . $style . '>' . $text . '</span>';
	}
	
	
	public function display($mixed='')
	{
		$i = 0;
		if (is_string($mixed) && $mixed) {
			$code = $mixed;
		} elseif (is_integer($mixed)) {
			$i = $mixed;
		} else {
			$i = 1;
		}
		//Ĭ�ϼ���
		if ($i || !$code) {
			switch ($i) {
				case 1:
					$code = '{FIRST:[NUM]...}{PREV:<}{BAR:[NUM]:10:3}{NEXT:>}{LAST:...[NUM]}';//DZ��
					break;
				case 2:
					$code = '{FIRST:[��ҳ]}{PREV:[��ҳ]}{NEXT:[��ҳ]}{LAST:[βҳ]}';//�Զ����
					break;
				case 3:
					$code = '{PREV:[��һҳ]}{BAR:[[NUM]]:20:10}{NEXT:[��һҳ]}';//�ٶȵ�
					break;
				case 4 :
					$code = '{PREV:&lt; ��һҳ}{PREVHD:...}{BAR:[NUM]:10:3}{NEXTHD:...}{NEXT:��һҳ &gt;}{LAST:βҳ}';//ZOL��
					break;
				case 5 :
					$code = '{PREV:��һҳ}{BAR:[NUM]:10:8}{NEXT:��һҳ}';//��Ʒ��ȫ
					break;
                case 6 :    //�б�ҳ
					$code = '{PREV:<span class="pre">��һҳ</span>}{FIRST:<span>[NUM]</span>}{PREVHD:<span class="bgno">...</span>}{BAR:<span>[NUM]</span>:5:2}{NEXTHD:<span class="bgno">...</span>}{LAST:<span>[NUM]</span>}{NEXT:<span class="next">��һҳ&gt;</span>}';
					break;
                case 7 :    //�б�ҳ�Ż��ṹ
					$code = '{PREV:&lt;��һҳ}{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}{NEXT:��һҳ&gt;}';
					break;
                case 8 :    //���汾
					$code = "{PREV:��һҳ}<span class='pagenum'><b>{$this->Page}</b>/{TOTAL:[NUM]}</span>{NEXT:��һҳ&gt;}";
					break;
                case 9 :    //bootstrap
					$code = "<li class='disabled'><a href='#'><b>{$this->Page}</b>/{TOTAL:[NUM]}</a></li><li>{PREV:��һҳ}</li><li>{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}</li><li>{NEXT:��һҳ&gt;}</li><li>{LAST:βҳ}</li><li><span>������{$this->Total}</span></li>";
					break;
                case 10 :    //�����
					$code = '{PREV:&lt; ��һҳ}{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}{LAST:[NUM]}{NEXT:��һҳ &gt;}';
					break;
                case 11 :    //����ϵͳ      
                    $this->setStyle(array('PREV'=>'prev','NEXT'=>'next'));
					$code = '{PREV:��һҳ}{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}{LAST:[NUM]}{NEXT:��һҳ}';
					break;
			}
		}
        #�������ֻ��һҳ�Ļ�������ʾ��ҳ
        if(in_array($i, array(11))){
            if($this->TotalPage <= 1){
                return "";
            }
        }
		$this->parseTemplate($code);

		foreach ($this->TagsVal as $tag=>$val) {
			$funckey = strtolower($tag) . 'Tag';
			
			$code = str_replace($this->Label[$tag], $this->$funckey(), $code);
		}
		return $code;
	}
}
