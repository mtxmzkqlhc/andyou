<?php
/**
* 所有的URL
* @author 仲伟涛
* @copyright (c) 2011-10-20
*/
class API_Libs_Global_Url
{
	
	/**
	* 获取驱动下载链接
	*/
	public static function getProDriveUrl($paramArr){
        $options = array(
            'proId'    => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        self::$_dbSoft       = API_Db_Soft::instance();
        
        $sql = 'select z_product_id from z_soft_to_product where z_product_id = ' . $proId;
        $res = self::$_dbSoft->getRow($sql);
        $url = '';
		if ($res) {
			$url = 'http://driver.zol.com.cn/series/' . $proId . '.html';
		}
		return $url;
	}

	/**
	* 获取BLOG链接
	*/
	public static function getBlogUrl($paramArr){
        $options = array(
            'userId'    => '',
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


		$userId = trim($userId);
		$url = '';
		if ($userId) {
			$url = 'http://blog.zol.com.cn/'. $userId . '/';
		}
		return $url;
	}

	/**
	* 个人中心链接
	*/
	public static function getMyUrl($paramArr){
        $options = array(
            'userId'    => '',
            'type'      => '',
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		$url = 'http://my.zol.com.cn/';
		$url .= $userId ? ($userId .'/') : '';
		$url .= $type ? ($type .'/') : '';
		return $url;
	}


	/**
	* 获取品牌专区列表
	*/
	public static function getManuSpecAreaUrl($paramArr){
        $options = array(
            'hostName'       => '',#主机名
            'manuId'         => 0, #品牌ID
            'subcateId'      => 0, #该品牌对应的子类
            'mainSubcateId'  => 0, #该文章频道关联的主子类
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


		#这些频道下的品牌专区,都是在频道根目录下
		$multiSub = array('mouse.zol.com.cn','net.zol.com.cn','power.zol.com.cn');
		if($manuId){
			if($mainSubcateId && $mainSubcateId != $subcateId && !in_array($hostName,$multiSub) ){
				$url = "http://{$hostName}/{$subcateId}/manu_{$manuId}.shtml";
			}else{
				$url = "http://{$hostName}/manu_{$manuId}.shtml";
			}
		}else{
			$url = "http://{$hostName}/";
		}
		return $url;
	}

	/**
	 * 获得pk的最终页URL
	 */
	public static function getPKUrl($paramArr){
        $options = array(
            'proId'       => 0, #本产品
            'pkProId'     => 0, #pk的产品
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
	    if (empty($proId) || empty($pkProId) || $proId==$pkProId) {
	        return false;
	    }

        if($pkProId  <  $proId){
            $tmp     = $pkProId;
            $pkProId = $proId;
            $proId   = $tmp;
        }
	    return '/pk/' . $proId. '_' . $pkProId. '.shtml';
    }

	/**
	* 获取产品最终页链接
	*/
	public static function getProUrl($paramArr){
        $options = array(
            'proId'             => 0,  #本产品
            'subcateEnName'     => '', #子类英文名
            'type'              => 'default', #最终页类型
            'param'          => '',        #附加参数
            'rewrite'           => true,      #是否进行伪静态
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		$type = strtolower($type);
		if (!$rewrite) {
			return '/?c=Detail&a=' . ucfirst($type) . '&proId=' . $proId . $param;
		}

		if ($type == 'index' || $type == 'default') {
			$url = '/' . $subcateEnName . '/index' . $proId . '.shtml';
		}elseif('grouppic' == $type){#组图列表 /{proId}/grouppic_{picClassId}_{groupId}_{page}.shtml
			$url = '/' . $proId . '/grouppic' . $param . '.shtml';
        } else {
			$type = $type == 'picture' ? 'pic' : $type;
			$url = '/' . ceil($proId / 1000) . '/' . $proId . '/' . $type . $param . '.shtml';
		}

		if($subcateEnName && ($subcateEnName=='china')){// || $subcateEnName=='other'
			$url = "http://product.xgo.com.cn".$url;
		}
		return $url;
	}


	/**
	 * 获得产品图片链接
	 */
	public static function getPicUrl($paramArr){
        $options = array(
            'picId'             => 0,     #图片ID
            'proId'             => 0,     #产品ID
            'type'              => 'PRO', #类型
            'param'             => '',    #附加的参数
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


		$PicUrl = '';
		switch ($type) {
			case 'PRO':
			default:
                $proUrl= '';
                if ($proId) {
                    $proUrl = (!$param?"_0":'').'_p'.$proId;
                }
				$proRelPath = "/picture_index_" . ceil($picId/10000) ."/";
				$PicUrl = $proRelPath . 'index' . $picId .$param.$proUrl.".shtml";
				break;
            case 'GROUP':#组图，此时$param为groupId
				$proRelPath = "/picture_index_" . ceil($picId/10000) ."/";
				$PicUrl = $proRelPath . 'group' . $picId .$param.".shtml";
                break;
			case 'DCBBS':
			case 'SJBBS':
			case 'PHOTO':
				$picTypeArr = strtolower($type);
				$proRelPath = "/picture_index_" . ceil($picId/10000) . "/";
				$PicUrl = $proRelPath . $picTypeArr . $picId . $param . ".shtml";
				break;
			case 'EXHIBIT':
				$proRelPath = ceil($picId/10000);
				$PicUrl = "/picture_index_" . $proRelPath . '/exhibit' . $picId . '.shtml';
				break;
		}

		return $PicUrl;
	}
/**
	* 获得排行的更多链接
	*/
	public static function getTopUrl($paramArr){
        $options = array(
            'subcateId'          => 0,
            'subcateEnName'      => '',
            'range'              => 1, #价格段、参数链接时需要 1、2、3、4、5
            'needId'             => 0,
            'cateId'             => 0, #大类id，大类页用 /compositor/cate_64.html
            'type'               => '',
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $host = defined('TOP_HOST') ? TOP_HOST : 'top.zol.com.cn';
		$baseUrl = "http://{$host}/compositor/";
		if($type != '') {
			switch($type){
				case 'manu':
					$moreUrl = $baseUrl . $subcateId . '/manu_attention.html';
					break;
				case 'manuPro':
					$moreUrl = $baseUrl . $subcateId . '/manu_' . $needId . '.html';
                    if($subcateEnName=='ultrabook') {
                        $moreUrl = $baseUrl.'16/notebook_Ultrabook_'.$needId.'.html';
                    }
					break;
				case 'subcate':
					$moreUrl = $baseUrl . $subcateId . '/' . $subcateEnName . '.html';
                    if($subcateEnName=='ultrabook') {
                        $moreUrl    =   $baseUrl.'Ultrabook.html';
                    }
					break;
				case 'price':
					$moreUrl = $baseUrl . $subcateId . "/price_{$range}.html";
					break;
				case 'param':
					$moreUrl = $baseUrl . $subcateId . '/param_' . $needId . "_{$range}.html";
					break;
				case 'series':
					if($subcateId && $needId){
						$moreUrl = $baseUrl . $subcateId . "/series_".$needId.".html";
					}else if($subcateId){
						$moreUrl = $baseUrl . $subcateId . "/series_attention.html";
					}
                    break;
                case 'upQuick':
                    $moreUrl = $baseUrl . $subcateId . '/hit_wave.html';
                    break;
                case 'cate':
                    $moreUrl = $baseUrl . "cate_{$cateId}.html";
                    break;
                case 'subcateAll':
                    $moreUrl = $baseUrl . "subcateAll.html";
                    break;
                case 'trend':
                    $moreUrl = $baseUrl . "trend_{$subcateId}.html";
                    break;
				default :
					break;
			}
		} else {
			$moreUrl = $baseUrl . $subcateEnName . '.html';
		}
		return $moreUrl;
	}

	/**
	* 获得经销商产品页URL
	*/
	public static function getMerchantProUrl($paramArr){
        $options = array(
            'merId'             => 0,     #图片ID
            'proId'             => 0,     #产品ID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		$dearleBuyUrl = "http://dealer.zol.com.cn/detail/" . ceil($merId/100) . "/{$merId}_{$proId}.html";
		return $dearleBuyUrl;
	}
	/**
	* 获得大类列表页的URL
	* wolf 加入
	* @param integer $cateId 大类ID
	* @return 链接字符串
	*/
	public static function getProCateUrl($paramArr){
        $options = array(
            'cateId'         => 0,     #大类ID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if(!$cateId)return '';

		return "/price_cate_{$cateId}.html";
	}
    
	/**
	* 获取文章链接
	*/
	public static function getDocUrl($paramArr){
        $options = array(
            'docId'             => 0,     #文章ID
            'classUrl'          => '',    #频道url
            'date'              => '',    #文章时间,ID在205000下有用
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


		if ($docId >= 205000) {
			$docUrl = $classUrl . '/' . floor($docId/10000) . '/' . $docId . '.html';
		} else {#旧链接
			list($year, $month, $day) = explode('-', substr($date, 0, 10));
			$docUrl = sprintf($classUrl . '/%04d/%02d%02d/' . $docId . '.shtml', $year, $month, $day);
		}

		return $docUrl;
	}

	/**
	* 获取文章频道首页链接
	* @param integer $classId 文章类别ID
	* @param boolean $full 是否全路径
	*/
	public static function getDocClassUrl($paramArr)
	{
        $options = array(
            'classId'   => 0,  #文章类别ID
            'full'    => 0,    #是否全路径
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if (!empty(self::$_cache['docClass'][$classId]['classUrl'])) {
			return self::$_cache['docClass'][$classId]['classUrl'];
		}

		$classArr = self::getDocClassArr(0);

		if (empty($classArr[$classId]['hostName'])) {
			return false;
		}

		$hostName = $classArr[$classId]['hostName'];
		$url = $classArr[$classId]['url'];
		$classUrl = $full ?  ('http://' . $hostName . $url) :  $url;

		if (113 == $classId) {
			$classUrl = 'http://dealer.zol.com.cn/dealer_article';
		}

		if ($classUrl && $classUrl != 'http://') {
			self::$_cache['docClass'][$classId]['classUrl'] = $classUrl;
			return $classUrl;
		} else {
			return false;
		}
	}

    /**
	* 获取文章更多路径
	* @param integer $subclassId 子类ID
	* @param intger|string $classId 大类ID 不填返回相对路径
	*/
	public static function getDocMorePath($paramArr)
	{
        $options = array(
            'subclassId' => 0,  #子类ID
            'classId'    => 0,  #文章类别ID
            'full'       => 0,  #是否全路径
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if (is_numeric($classId)) {
			$classUrl = self::getDocClassUrl(array('classId'=>$classId));
		} elseif (is_string($classId)) {
			$classUrl = $classId;
		} else {
			$classUrl = '';
		}

		return $classUrl . '/more/2_' . $subclassId . '.shtml';
	}


	/**
	 * 获取系列最终页链接
	 */
	public static function getSeriesUrl($paramArr){
        $options = array(
            'subcateId'         => 0,
            'seriesId'          => 0,
            'manuId'            => 0,
            'type'              => 'default',
            'param'             => array(),
            'rewrite'           => true,
            'showType'          => 0,
            'orderType'         => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		

		if (empty($subcateId) || empty($seriesId)) {
			return false;
		}
		$type = strtolower($type);

		$url = '';
		switch ($type) {
			case 'default':
			case 'detail':
				if ($rewrite == true) {
					$url = '/series/' .$subcateId. '/' .$seriesId. '_1.html';
				} else {
					$url = '/index.php?c=Series&a=Detail&seriesId=' .$seriesId;
				}
				break;
			case 'param':
				$paramStr = $showStr = '';
				if ($rewrite == true) {
					if (!empty($param)) {
						$paramStr = '_'.$param;
					}
					if (!empty($showType)) {
						$showStr = '_' . $orderType.'_'.$showType;
					}
					$url = '/series/'.$subcateId.'/'.$manuId.'/param_'.$seriesId.$paramStr.$showStr.'.html';
				} else {
					if (!empty($param)) {
						$paramStr = '&paramVal=' . implode('-', $param);
					}
					if (!empty($showType)) {
						$paramShowType = $showType;
						$showStr = '&paramShowType=' . $paramShowType;
					}
					$url = '/index.php?c=Series&a=Param&seriesId=' .$seriesId. $paramStr. $showStr;
				}
			   break;
			   case 'price':
                    $paramStr = is_string($param) ? $param :  '';
                    if ($rewrite == true) {
                        $url = '/series/' . $subcateId . '/' . $manuId . '/price_' . $seriesId . $paramStr . '.html';
                    } else {
                        $url = '/index.php?c=Series&a=Param&subcateId=' . $subcateId . '&manuId=' . $manuId . '&seriesId=' . $seriesId . '&locationId=' . (int)$param;
                    }
			   break;
			   case 'param_comp':
				$paramStr = '';
				if ($rewrite == true) {
					if (!empty($param)) {
						$paramStr = !is_string($param) ? '_' . implode('-', $param) : $param;
					}
					$url = '/series/'.$subcateId.'/'.$manuId.'/param_comp_'.$seriesId.$paramStr.'.html';
				} else {
					if (!empty($param)) {
						$paramStr = '&paramVal=' . implode('-', $param);
					}
					$url = '/index.php?c=Series&a=Param&compType=comp&seriesId=' .$seriesId. $paramStr;
				}
				break;
			   case 'param_comp_other':
				if ($rewrite == true) {
					if (!empty($param)) {
						$paramStr = !is_string($param) ? '_' . implode('-', $param) : $param;
					}
					$url = '/series/'.$subcateId.'/'.$manuId.'/param_comp_other_'.$seriesId.$paramStr.'.html';
				}
				break;
				case 'param_all':
				if ($rewrite == true) {
					if (!empty($param)) {
						$paramStr = '_' . implode('-', $param);
					}
					$url = '/series/'.$subcateId.'/'.$manuId.'/param_all_'.$seriesId.$paramStr.'.html';
				}
				break;
			case 'review':
				if ($rewrite == true) {
					if ($param && is_array($param)) {
						$param = '_' . join('_', $param);
					}
					if (!$param) {
						$param = '';
					}
					$url = '/series/'.$subcateId.'/'.$manuId.'/review_'.$seriesId. $param . '.html';
				} else {
					$url = '/index.php?c=Series&a=' .$type. '&seriesId=' .$seriesId;
				}
				break;
			case 'picture':
				$paramStr = '';
				if ($rewrite == true) {
					if(!$param)$param='';
					$url = '/series/'.$subcateId.'/'.$manuId.'/pic_'.$seriesId.$param.'.html';
				} else {
					if (is_array($param) && !empty($param)) {
						foreach ($param as $key => $val) {
							$paramStr .= '&' . $key .'='. $val;
						}
					}
					$url = '/index.php?c=Series&a=' .$type. '&seriesId=' .$seriesId .$paramStr;
				}
				break;
            case 'video':
                $paramStr = '';
                if ($rewrite == true) {
                    if (is_array($param) && !empty($param)) {
                        foreach ($param as $val) {
                            $paramStr .= '_' . $val;
                        }
                    }
                    $url = '/series/'.$subcateId.'/'.$manuId.'/video_'.$seriesId.$paramStr.'.html';
                } else {
                    if (is_array($param) && !empty($param)) {
                        foreach ($param as $key => $val) {
                            $paramStr .= '&' . $key .'='. $val;
                        }
                    }
                    $url = '/index.php?c=Series&a=' .$type. '&seriesId=' .$seriesId .$paramStr;
                }
                break;
             case 'article':
                $paramStr = '';
                if ($rewrite == true) {
                    if (is_array($param) && !empty($param)) {
                        foreach ($param as $val) {
                            $paramStr .= '_' . $val;
                        }
                    }
                    $url = '/series/'.$subcateId.'/'.$manuId.'/article_'.$seriesId.$paramStr.'.html';
                } else {
                    if (is_array($param) && !empty($param)) {
                        foreach ($param as $key => $val) {
                            $paramStr .= '&' . $key .'='. $val;
                        }
                    }
                    $url = '/index.php?c=Series&a=' .$type. '&seriesId=' .$seriesId .$paramStr;
                }
		}
		return $url;
	}


	/**
	* 获取软件URL
	*/
	public static function getDriverUrl($paramArr){
		$driverId = (int)$paramArr['driverId'];
		$url = 'http://driver.zol.com.cn/detail/' . ceil($driverId / 10000) . '/' . $driverId . '.shtml';
		return $url;
	}




	/**
	* 获取论坛链接
	*/
	public static function getBbsUrl($paramArr){
        $options = array(
			'baseUrl'   => '',
			'subcateId' => 0,
			'manuId'    => 0,
			'proId'     => 0,
			'seriesId'  => 0,
			'isSpec'    => false,
			'boardId'   => 0,
			'isNormal'  => false,#是否有单独域名 在BbsInfo中
			'bbsProId'  => 0,    #这个数据是proInfo或者seriesInfo缓存中
			'bookType'  => 0,
			'rewrite'   => true, #是否伪静态,基本可以废弃
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        
		if ($baseUrl) {
			$baseUrl .= substr($baseUrl, -1) !== '/' ? '/' : '';
		} else {
			$baseUrl = 'http://group.zol.com.cn/';
		}

		$url = $baseUrl;
		#子类品牌
		if ($subcateId && $manuId) {
			if ( (!$boardId && !in_array($subcateId, array(15, 16, 57) ))  || $isSpec == true) {
				$url = $baseUrl . 'manu_index_' . $subcateId . '_' . $manuId . '.html';
			} else {
				$url = $isNormal ? ($baseUrl . 'subcate_list_' . $boardId . '.html') : $baseUrl;
			}
			#系列页
			if ($seriesId) {
				$url = $baseUrl . 'xilie_list_' . $subcateId . '_' . $manuId. '_' . $seriesId . '.html';
			}
		}else{
			if($subcateId){
                if($boardId){
                     $url = $isNormal ? ($baseUrl . 'subcate_list_' . $boardId . '.html') : $baseUrl;
                }
			}
		}
		#产品页
		if ($proId) {
			if (!$rewrite) {
				$url = $baseUrl . 'comment.php?productid=' . $proId . '&type=' . $bookType;
			} else {
				$url = $baseUrl . 'comment_' . ($bookType ? "type_{$bookType}_" : '') . $proId . '.html';
			}
		}

		if (($proId && $manuId && $subcateId) || $seriesId ){
			if ($seriesId) {
				$url = $baseUrl.'xilie_list_'.$subcateId.'_'.$manuId.'_'.$seriesId.'.html';
			} else {
				$url =$baseUrl.'comment_' . ($bookType ? "type_{$bookType}_" : '') . $proId . '.html';
			}
			if (!$rewrite) {
				$url = $baseUrl . 'comment.php?productid=' . $proId;

			}

            if ($seriesId && $manuId && $subcateId && ($bookType || !$rewrite)) {
                $url = $baseUrl . "xilie_list.php?subcatid={$subcateId}&manuid={$manuId}&xilieid={$seriesId}&type={$bookType}";
            }
		}
		if($bbsProId){
			$url =$baseUrl.'comment_' . ($bookType ? "type_{$bookType}_" : '') . $bbsProId . '.html';
		}
		return $url;
	}
	/**
	* 获取论坛帖子地址
	*/
	public static function getBookUrl($paramArr){
        $options = array(
			'baseUrl'    => '',
			'bookId'     => 0,
			'boardId'    => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if($baseUrl == '')return '';

		$baseUrl .= substr($baseUrl, -1) == '/' ? '' : '/';
		$dir      = ceil($bookId/10000);
		return $baseUrl . $dir . '/' . $boardId . '_' . $bookId . '.html';
	}
	/**
	 * 获得参数的术语链接
	 */
	public static function getParamIntroLink($paramArr){
        $options = array(
            'linkId'         => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		
		if (!$linkId) return false;
		return '/product_param/index' . $linkId . '.html';
	}

	/**
	* 获取装备产品列表链接
	*/
	public static function getEquipProUrl($paramArr)
	{
        $options = array(
            'proId'         => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if (!$proId) return false;
		return 'http://zb.zol.com.cn/product/' . $proId . '/1/';
	}

	/**
	* 作者文章列表
	*/
	public static function getEditorUrl($paramArr)
	{
        $options = array(
            'userName'         => '',
            'classId'          => '',
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		return 'http://service.zol.com.cn/doclist_'.$classId.'_3_1_'.  urlencode($userName).'.html';
		
	}

	/**
	 * 获得手机软件的地址
	 */
	public static function getMobileSoftUrl($paramArr)
	{
		$msId = (int)$paramArr['msId'];
		return 'http://sj.zol.com.cn/detail/'.ceil($msId/1000).'/'.$msId.'.shtml';
	}

	/**
	* 获得经销商URL
	*/
	public static function getMerchantUrl($paramArr)
	{
		$merId = (int)$paramArr['merId'];
		return 'http://dealer.zol.com.cn/d_' . $merId . '/';
	}
	/**
	* 获取促销信息链接
	*/
	public static function getPromotionInfoUrl($paramArr)
	{
        $options = array(
            'promId'         => 0,
            'merId'          => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		
		$url = 'http://dealer.zol.com.cn/d_' . $merId . '/market_' . $promId . '.html';
		return $url;
	}

    /**
	* 获取趋势链接
	*/
	public static function getTrendUrl($paramArr)
	{
        $proId = (int)$paramArr['proId'];
		return '/'.ceil($proId/1000).'/'.$proId.'/pro_hit.shtml';
	}

	/**
	* 获得问答堂专家的url
	*/
	public static function getAskExpertUrl($paramArr)
	{
        $editorId = $paramArr['uid'];
		return  'http://ask.zol.com.cn/editor/' . $editorId . '/';
	}

    /**
	 * 通过图片ID获取图片Src
	 */
	public static function getOnePicSrc($picId, $size = '_80x60', $dir = 'product', $extName = '')
	{
        $PIC_HOST = 'http://2.zol-img.com.cn';
		if ((int)$picId < 1) {
			return false;
		}

		if('' != trim($size)) {
			$size = $size[0] != '_' ? ('_' . $size) : $size;
		}

		$picUrl = $PIC_HOST . '/product/no.jpg';

		if ($size == '_100x75') {
			$picUrl = $PIC_HOST . '/product/no100x75.jpg';
		}


		$subDir   = floor($picId / 100000);
		$grandDir = floor($picId % 1000);

		$table = $dir . '_' . floor($picId / 10000);
		if (empty($extName)) {
			self::init();
			$sql = "SELECT ext_name FROM {$table} WHERE sid='{$picId}'";
			$extName = self::$_dbPicture->getOne($sql);
		}

		if ($extName && $extName!='txt') {
			$cryptName = crypt($picId, 'ceshi');
			$cryptName = str_replace(array('.', '/'), array('', ''), $cryptName);

			$cryptName = $cryptName . '.' . $extName;
			$picPath = $dir . '/' . $subDir . $size . '/' .  $grandDir . '/' . $cryptName;
			$picUrl = 'http://2' . chr($picId%6+97) . '.zol-img.com.cn/' . $picPath;
		}

		unset($picId, $dir, $size, $subDir, $grandDir, $extName, $cryptName);
		return $picUrl;
	}

    /**
     * 获得对比页面的链接地址
     * @param string $type 页面类别，pk：整体对比（PK),param：参数对比，pic：外观对比，review：评价对比
     * @param array $proIdArr
     */
    public static function getProductCompUrl($type,$proIdArr)
    {
        if(empty($proIdArr) || !is_array($proIdArr)){
            return '';
        }
        $url = '';
        //对ID，进行排序，保证小的ID在前面
        sort($proIdArr);
        if('pk' == $type){
            if(count($proIdArr) >= 2){
                $url = '/pk/' . $proIdArr[0] . '_' . $proIdArr[1] . '.shtml';
            }
        }else{
            $url = '/ProductComp_' . $type . '_' . implode('-',$proIdArr) . '.html';
        }
        return $url;
    }
    
	/**
	 * 获取经销商促销链接
	 * @param array $param 参数
	 * <pre>
	 * 	@param int $param['merId'] 经销商ID
	 * 	@param int $param['kindId'] 信息类型
	 * 	@param int $param['promoId'] 信息ID
	 * </pre>
	 * @return string 经销商促销链接
	 */
	public static function getMerPromo(array $param = array())
	{
		$merId     = isset($param['merId']) ? $param['merId'] : 0;
		$kindId    = isset($param['kindId']) ? $param['kindId'] : 0;
		$promoId   = isset($param['promoId']) ? $param['promoId'] : 0;
		
		$url = self::getMer($merId);
		$url .= $kindId ? 'market_bulletin.php?infoKind=' . $kindId : '';
		$url .= $promoId ? 'market_' . $promoId . '.html' : '';
		return $url;
	}
    
    /**
	* 获取列表页链接
	* @param array 数组参数
	*/
	public static function getListUrl($paramArr)
	{
        $options = array(
            'subcateId'     => 0,    #子类ID
            'subcateEnName' => 0,    #子类英文名
            'manuId'        => 0,    #品牌ID
            'priceId'       => 'noPrice', #价格
            'paramVal'      => '',   #复合参数
            'queryType'     => 0,    #排序
            'style'         => 0,    #显示样式
            'locationId'    => 0,    #地区
            'keyword'       => 0,    #关键字
            'page'          => 1,    #页码
            'rewrite'       => 1,    #是否伪静态
            'isHot'         => 0,    #主板推荐链接特殊处理
            'isLong'        => 0,    #是否启用长链接
            'isHistory'     => 0,    #是否取历史列表
            'appendParam'   => 0,    #兼容旧代码参数
        );
        if (!isset($paramArr['subcateEnName']) && $paramArr['subcateId']) {
            $Db_Product = API_Db_Product::instance();
            $sql = "select brief from subcategory_extra_info where subcategory_id={$paramArr['subcateId']}";
            $paramArr['subcateEnName'] = $Db_Product->getOne($sql);
        }
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        if ($appendParam && is_array($appendParam)) { #兼容旧代码参数处理，相关文件较多怕有遗漏所以程序处理
            extract($appendParam);
            $paramVal = is_array($paramVal) ? implode('-', $paramVal) : $paramVal;
        }
        
        $tabSubArr = array(57,16,15);
        if (!in_array($subcateId, $tabSubArr) && $subcateEnName) return self::getListShortUrl($paramArr);
        $subEnName = $isHistory ? 'history' : $subcateEnName.'_index';
        
		if (!$rewrite) {
			$url = '/index.php?c=List&subcateId=' . $subcateId;
			$url .= $manuId ? '&manuId=' . $manuId : '';

			if (is_array($appendParam)) {
				$url .= '&' . http_build_query($appendParam);
			} else if ($appendParam) {
				$url .= '&' . $appendParam;
			}
			return $url;
		} else {
            //ZOL_Debugger::dump($priceId);
            $urlcate        = $subcateId ? $subcateId : '';                 #子类
            $urlManu        = $manuId ? "_" . $manuId : '';                 #品牌
            $urlPrice       = 'noPrice'!==$priceId ? "_" . $priceId : '';   #价格
            $urlParam       = $paramVal ? "_" . $paramVal : '';             #复合参数
            $urlQuery       = $queryType ? "_" . $queryType : '_1';         #排序
            $urlStyle       = $style ? "_" . $style : '_1';                 #列表显示形式
            $urlLocation    = $locationId ? "_" . $locationId : '_0';       #地区
            $urlHot         = $isHot ? "_hot"  : '';                        #主板推荐链接特殊处理
            $urlPage        = $page ? "_" . $page : '_1';                   #页码

            #关键字分页替换用，不需转换
            if ($keyword && '{keyword}' != $keyword) {
                $keyword = ZOL_String::escape($keyword);
                $keyword = str_replace('%', '@', $keyword);
            }
            if ($paramVal && $keyword) {
                $urlParam .= "-k" . $keyword;   #关键字
            } else if (!$paramVal && $keyword) {
                $urlParam .= "_k" . $keyword;   #关键字
            }

            if (('noPrice'!==$priceId && $paramVal)) {
                $isLong = 1;
            }
            
            //ZOL_Debugger::dump($urlParam.$urlQuery.$urlStyle.$urlLocation);
            if ($queryType > 1 || $style > 1 || $locationId || $isLong || $keyword) {
                $urlManu  = $urlManu ? $urlManu : '_0';
                $urlPrice = $urlPrice ? $urlPrice : '_1';
                $urlParam = $urlParam ? $urlParam : '_0';
                $url = '/'.$subEnName.'/subcate'.$urlcate.$urlManu."_list".$urlPrice.$urlParam.$urlQuery.$urlStyle.$urlLocation.$urlPage.".html";
            } else {
                $url = '/'.$subEnName.'/subcate'.$urlcate.$urlManu."_list".$urlPrice.$urlParam.$urlHot.$urlPage.".html";
            }

        }
		return $url;
	}
    
    /**
	* 获取列表页链接
	* @param array 数组参数
	*/
	public static function getListShortUrl($paramArr)
	{
        $options = array(
            'subcateId'     => 0,    #子类ID
            'subcateEnName' => 0,    #子类英文名
            'enManu'        => '',   #品牌ID
            'priceId'       => 'noPrice', #价格
            'paramVal'      => '',   #复合参数
            'enQuery'       => '',   #排序
            'enStyle'       => '',   #显示样式
            'enLocation'    => '',   #地区
            'keyword'       => '',   #关键字
            'page'          => 1,    #页码
            'rewrite'       => 1,    #是否伪静态
            'isHistory'     => 0,    #是否取历史列表
            'appendParam'   => 0,    #兼容旧代码参数
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        if ($appendParam && is_array($appendParam)) { #兼容旧代码参数处理，相关文件较多怕有遗漏所以程序处理
            extract($appendParam);
            $paramVal = is_array($paramVal) ? implode('-', $paramVal) : $paramVal;
        }
        $subcateEnName = strtolower($subcateEnName);
        $enManu = str_replace(chr(32), '',$enManu);
        if (!$enManu && isset($paramArr['manuId']) && $manuId) {
            #查询顺序 $enManuArr > helper > 数据库
            static $enManuArr = array();
            if (!$enManuArr) {
                $enManuArr = API_Item_Pro_List::getManuArr(array('subcateId'=>$subcateId));
            }
            if (!isset($enManuArr[$manuId])) {
                $Db_Product = API_Db_Product::instance();
                $sql = "select en_name from manufacturer where id={$manuId}";
                $enManuArr[$manuId]['enManu'] = $Db_Product->getOne($sql);
            }
            $enManu = str_replace(chr(32), '',$enManuArr[$manuId]['enManu']);
        }
        if (!$enLocation && isset($paramArr['locationId']) && $locationId) {
            #查询顺序 $enLocationArr > helper > 数据库
            static $enLocationArr = array();
            if (!isset($enLocationArr[$locationId])) {
                $arr = API_Item_Pro_Area::getLocationInfo(array('locationId'=>$locationId));
                if (isset($arr['enName']) && $arr['enName']) {
                    $enLocationArr[$locationId] = $arr['enName'];
                } else {
                    $Db_Product = API_Db_Product::instance();
                    $sql = "select en_name from merchant_recommend_channel where base_url={$locationId}";
                    $enLocationArr[$locationId] = $Db_Product->getOne($sql);
                }
            }
            $enLocation = $enLocationArr[$locationId];
        }
        
        $subEnName = $isHistory ? $subcateEnName.'/history/' : $subcateEnName.'/';

		if (!$rewrite) { #未改
			$url = '/index.php?c=List&subcateId=' . $subcateId;
			$url .= $manuId ? '&manuId=' . $manuId : '';

			if (is_array($appendParam)) {
				$url .= '&' . http_build_query($appendParam);
			} else if ($appendParam) {
				$url .= '&' . $appendParam;
			}
			return $url;
		} else {
            $urlManu        = $enManu ? strtolower($enManu).'/' : '';                   #品牌
            $urlPrice       = 'noPrice' !==$priceId ? ($paramVal ? $priceId.'_' : $priceId.'/') : ''; #价格
            $urlParam       = $paramVal ? str_replace('-', '_', $paramVal).'/' : '';                   #复合参数
            $urlQuery       = $enQuery ? $enQuery : '';      #排序
            $urlStyle       = $enStyle ? ($enQuery ? '_'.$enStyle : $enStyle) : '';              #列表显示形式
            $urlLocation    = $enLocation ? $enLocation.'/' : '';         #地区
            $urlPage        = $page != 1 ? ($enQuery || $enStyle ? '_'.$page : $page) : '';      #页码
            $urlkword       = '{keyword}' != $keyword ? str_replace('%', '@', ZOL_String::escape($keyword)) : $keyword; #关键字
            
            $url = '/'.$subEnName.$urlManu.$urlPrice.$urlParam.$urlLocation.$urlQuery.$urlStyle.$urlPage;
            if ($urlQuery || $urlStyle || $urlPage) $url .= '.html';
            if ($urlkword) $url .= "?k=$urlkword";

        }
		return $url;
	}

    /**
	* 获取历史列表页链接 
	* @param array 数组参数
	*/
	public static function getHistoryListUrl($paramArr)
	{
        $options = array(
            'subcateId'     => 0,    #子类ID
            'subcateEnName' => 0,    #子类英文名
            'manuId'        => 0,    #品牌ID
            'priceId'       => 1,    #价格
            'paramVal'      => '',   #复合参数
            'queryType'     => 0,    #排序
            'keyword'       => 0,    #关键字
            'page'          => 1,    #页码
            'rewrite'       => 1,    #是否伪静态
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		if (!$rewrite) {
			$url = '/index.php?c=List&subcateId=' . $subcateId;
			$url .= $manuId ? '&manuId=' . $manuId : '';

			if (is_array($appendParam)) {
				$url .= '&' . http_build_query($appendParam);
			} else if ($appendParam) {
				$url .= '&' . $appendParam;
			}
			return $url;
		} else {
            $urlParam = $paramVal ? "_" . $paramVal : '_0'; #复合参数
            #关键字分页替换用，不需转换
            if ($keyword && '{keyword}' != $keyword) {
                $keyword = ZOL_String::escape($keyword);
                $keyword = str_replace('%', '@', $keyword);
            }
            if ($paramVal && $keyword) {
                $urlKeyword = "-k" . $keyword;   #关键字
            } else if (!$paramVal && $keyword) {
                $urlKeyword = "_k" . $keyword;   #关键字
            } else {
                $urlKeyword = '';
            }

            $url = '/history/subcate'.$subcateId.'_'.$manuId.'_'.$priceId.$urlParam.$urlKeyword.'_'.$queryType.'_'.$page.".html";
        }
		return $url;
	}
    
    /**
	* 获得排行的更多链接
	*/
	public static function getEvaPicUrl($paramArr){
        $options = array(
            'proId'      => 0,
            'picId'      => '',
            'picType'    => 0,
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        $url = "/".$picType."/eva_".$proId."_".$picId.".html";
        return $url;
    }

    /**
	* 获取排行榜更多页链接
	*/
	public static function getTopMoreUrl($subcateId,$subcateEnName,$manuId)
	{
		$baseUrl = 'http://top.zol.com.cn/compositor/';
		if (!$manuId && $subcateEnName) {
			$url = $baseUrl . $subcateId . '/' . $subcateEnName . '.html';
		}

		if ($manuId) {
			$url = $baseUrl . $subcateId . '/manu_' . $manuId . '.html';
		}
		return $url;
	}

    /**
	 * 得到系列榜更多页链接
	 */
	public static function getSeriesRankUrl($subcateId,$manuId){
		if($subcateId && $manuId){
			$url = "http://top.zol.com.cn/compositor/".$subcateId."/series_".$manuId.".html";
		}else if($subcateId){
			$url = "http://top.zol.com.cn/compositor/".$subcateId."/series_attention.html";
		}
		return $url;
	}

    /**
	* 获取产品库调查链接
	*/
	public static function getIndaUrl($subcateId, $result = FALSE)
	{
        if ($subcateId && $result) {
            $url = '/indagate_result_' . $subcateId  . '.html';
        } elseif ($subcateId) {
            $url = '/indagate_' . $subcateId  . '.html';
        }
		return $url;
	}
    
    /**
     * 得到wap的连接
     * @param type $paramArr
     * @return string 
     */
    public static function getWapUrl($paramArr){
        $options = array(
            'subcateId'=> 0,
            'manuId'   => 0,
            'proId'    => 0,
            'pageType' =>'',
            'wapExt' => 'html'
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
		$wapUrl = "http://wap.zol.com.cn/";
        $url = '';
        switch ($pageType) {
            case 'list':
                $manuStr = '';
                if($manuId) {
                    $manuStr = "_".$manuId;
                }
                $url = $wapUrl."list/".$subcateId.$manuStr.".".$wapExt;
                break;
            case 'index':
                $url = $wapUrl;
                break;
            case 'detail':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/index.".$wapExt;
                break;
            case 'price':
                 $url = $wapUrl.ceil($proId/1000)."/".$proId."/price.".$wapExt;
                break;
            case 'param':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/param.".$wapExt;
                break;
            case 'pic':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/pic.".$wapExt;
                break;
            case 'article':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/article.".$wapExt;
                break;
            default:
                break;
        }
        return $url;
    }
 
    /**
	* 获取配件页链接
	*/
	public static function getFittingUrl($paramArr)
	{
        $options = array(
            'proId'      => 0,
            'subcateId'  => 0
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

        if ($subcateId) {
            $url = '/'.ceil($proId/1000).'/'.$proId.'/fitting_'.$subcateId.'.shtml';
        } else {
            $url = '/'.ceil($proId/1000).'/'.$proId.'/fitting.shtml';
        }
		return $url;
	}
}

