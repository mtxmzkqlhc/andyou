<?php
/**
* ���е�URL
* @author ��ΰ��
* @copyright (c) 2011-10-20
*/
class API_Libs_Global_Url
{
	
	/**
	* ��ȡ������������
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
	* ��ȡBLOG����
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
	* ������������
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
	* ��ȡƷ��ר���б�
	*/
	public static function getManuSpecAreaUrl($paramArr){
        $options = array(
            'hostName'       => '',#������
            'manuId'         => 0, #Ʒ��ID
            'subcateId'      => 0, #��Ʒ�ƶ�Ӧ������
            'mainSubcateId'  => 0, #������Ƶ��������������
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


		#��ЩƵ���µ�Ʒ��ר��,������Ƶ����Ŀ¼��
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
	 * ���pk������ҳURL
	 */
	public static function getPKUrl($paramArr){
        $options = array(
            'proId'       => 0, #����Ʒ
            'pkProId'     => 0, #pk�Ĳ�Ʒ
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
	* ��ȡ��Ʒ����ҳ����
	*/
	public static function getProUrl($paramArr){
        $options = array(
            'proId'             => 0,  #����Ʒ
            'subcateEnName'     => '', #����Ӣ����
            'type'              => 'default', #����ҳ����
            'param'          => '',        #���Ӳ���
            'rewrite'           => true,      #�Ƿ����α��̬
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		$type = strtolower($type);
		if (!$rewrite) {
			return '/?c=Detail&a=' . ucfirst($type) . '&proId=' . $proId . $param;
		}

		if ($type == 'index' || $type == 'default') {
			$url = '/' . $subcateEnName . '/index' . $proId . '.shtml';
		}elseif('grouppic' == $type){#��ͼ�б� /{proId}/grouppic_{picClassId}_{groupId}_{page}.shtml
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
	 * ��ò�ƷͼƬ����
	 */
	public static function getPicUrl($paramArr){
        $options = array(
            'picId'             => 0,     #ͼƬID
            'proId'             => 0,     #��ƷID
            'type'              => 'PRO', #����
            'param'             => '',    #���ӵĲ���
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
            case 'GROUP':#��ͼ����ʱ$paramΪgroupId
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
	* ������еĸ�������
	*/
	public static function getTopUrl($paramArr){
        $options = array(
            'subcateId'          => 0,
            'subcateEnName'      => '',
            'range'              => 1, #�۸�Ρ���������ʱ��Ҫ 1��2��3��4��5
            'needId'             => 0,
            'cateId'             => 0, #����id������ҳ�� /compositor/cate_64.html
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
	* ��þ����̲�ƷҳURL
	*/
	public static function getMerchantProUrl($paramArr){
        $options = array(
            'merId'             => 0,     #ͼƬID
            'proId'             => 0,     #��ƷID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		$dearleBuyUrl = "http://dealer.zol.com.cn/detail/" . ceil($merId/100) . "/{$merId}_{$proId}.html";
		return $dearleBuyUrl;
	}
	/**
	* ��ô����б�ҳ��URL
	* wolf ����
	* @param integer $cateId ����ID
	* @return �����ַ���
	*/
	public static function getProCateUrl($paramArr){
        $options = array(
            'cateId'         => 0,     #����ID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if(!$cateId)return '';

		return "/price_cate_{$cateId}.html";
	}
    
	/**
	* ��ȡ��������
	*/
	public static function getDocUrl($paramArr){
        $options = array(
            'docId'             => 0,     #����ID
            'classUrl'          => '',    #Ƶ��url
            'date'              => '',    #����ʱ��,ID��205000������
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


		if ($docId >= 205000) {
			$docUrl = $classUrl . '/' . floor($docId/10000) . '/' . $docId . '.html';
		} else {#������
			list($year, $month, $day) = explode('-', substr($date, 0, 10));
			$docUrl = sprintf($classUrl . '/%04d/%02d%02d/' . $docId . '.shtml', $year, $month, $day);
		}

		return $docUrl;
	}

	/**
	* ��ȡ����Ƶ����ҳ����
	* @param integer $classId �������ID
	* @param boolean $full �Ƿ�ȫ·��
	*/
	public static function getDocClassUrl($paramArr)
	{
        $options = array(
            'classId'   => 0,  #�������ID
            'full'    => 0,    #�Ƿ�ȫ·��
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
	* ��ȡ���¸���·��
	* @param integer $subclassId ����ID
	* @param intger|string $classId ����ID ��������·��
	*/
	public static function getDocMorePath($paramArr)
	{
        $options = array(
            'subclassId' => 0,  #����ID
            'classId'    => 0,  #�������ID
            'full'       => 0,  #�Ƿ�ȫ·��
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
	 * ��ȡϵ������ҳ����
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
	* ��ȡ���URL
	*/
	public static function getDriverUrl($paramArr){
		$driverId = (int)$paramArr['driverId'];
		$url = 'http://driver.zol.com.cn/detail/' . ceil($driverId / 10000) . '/' . $driverId . '.shtml';
		return $url;
	}




	/**
	* ��ȡ��̳����
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
			'isNormal'  => false,#�Ƿ��е������� ��BbsInfo��
			'bbsProId'  => 0,    #���������proInfo����seriesInfo������
			'bookType'  => 0,
			'rewrite'   => true, #�Ƿ�α��̬,�������Է���
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        
		if ($baseUrl) {
			$baseUrl .= substr($baseUrl, -1) !== '/' ? '/' : '';
		} else {
			$baseUrl = 'http://group.zol.com.cn/';
		}

		$url = $baseUrl;
		#����Ʒ��
		if ($subcateId && $manuId) {
			if ( (!$boardId && !in_array($subcateId, array(15, 16, 57) ))  || $isSpec == true) {
				$url = $baseUrl . 'manu_index_' . $subcateId . '_' . $manuId . '.html';
			} else {
				$url = $isNormal ? ($baseUrl . 'subcate_list_' . $boardId . '.html') : $baseUrl;
			}
			#ϵ��ҳ
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
		#��Ʒҳ
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
	* ��ȡ��̳���ӵ�ַ
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
	 * ��ò�������������
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
	* ��ȡװ����Ʒ�б�����
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
	* ���������б�
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
	 * ����ֻ�����ĵ�ַ
	 */
	public static function getMobileSoftUrl($paramArr)
	{
		$msId = (int)$paramArr['msId'];
		return 'http://sj.zol.com.cn/detail/'.ceil($msId/1000).'/'.$msId.'.shtml';
	}

	/**
	* ��þ�����URL
	*/
	public static function getMerchantUrl($paramArr)
	{
		$merId = (int)$paramArr['merId'];
		return 'http://dealer.zol.com.cn/d_' . $merId . '/';
	}
	/**
	* ��ȡ������Ϣ����
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
	* ��ȡ��������
	*/
	public static function getTrendUrl($paramArr)
	{
        $proId = (int)$paramArr['proId'];
		return '/'.ceil($proId/1000).'/'.$proId.'/pro_hit.shtml';
	}

	/**
	* ����ʴ���ר�ҵ�url
	*/
	public static function getAskExpertUrl($paramArr)
	{
        $editorId = $paramArr['uid'];
		return  'http://ask.zol.com.cn/editor/' . $editorId . '/';
	}

    /**
	 * ͨ��ͼƬID��ȡͼƬSrc
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
     * ��öԱ�ҳ������ӵ�ַ
     * @param string $type ҳ�����pk������Աȣ�PK),param�������Աȣ�pic����۶Աȣ�review�����۶Ա�
     * @param array $proIdArr
     */
    public static function getProductCompUrl($type,$proIdArr)
    {
        if(empty($proIdArr) || !is_array($proIdArr)){
            return '';
        }
        $url = '';
        //��ID���������򣬱�֤С��ID��ǰ��
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
	 * ��ȡ�����̴�������
	 * @param array $param ����
	 * <pre>
	 * 	@param int $param['merId'] ������ID
	 * 	@param int $param['kindId'] ��Ϣ����
	 * 	@param int $param['promoId'] ��ϢID
	 * </pre>
	 * @return string �����̴�������
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
	* ��ȡ�б�ҳ����
	* @param array �������
	*/
	public static function getListUrl($paramArr)
	{
        $options = array(
            'subcateId'     => 0,    #����ID
            'subcateEnName' => 0,    #����Ӣ����
            'manuId'        => 0,    #Ʒ��ID
            'priceId'       => 'noPrice', #�۸�
            'paramVal'      => '',   #���ϲ���
            'queryType'     => 0,    #����
            'style'         => 0,    #��ʾ��ʽ
            'locationId'    => 0,    #����
            'keyword'       => 0,    #�ؼ���
            'page'          => 1,    #ҳ��
            'rewrite'       => 1,    #�Ƿ�α��̬
            'isHot'         => 0,    #�����Ƽ��������⴦��
            'isLong'        => 0,    #�Ƿ����ó�����
            'isHistory'     => 0,    #�Ƿ�ȡ��ʷ�б�
            'appendParam'   => 0,    #���ݾɴ������
        );
        if (!isset($paramArr['subcateEnName']) && $paramArr['subcateId']) {
            $Db_Product = API_Db_Product::instance();
            $sql = "select brief from subcategory_extra_info where subcategory_id={$paramArr['subcateId']}";
            $paramArr['subcateEnName'] = $Db_Product->getOne($sql);
        }
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        if ($appendParam && is_array($appendParam)) { #���ݾɴ��������������ļ��϶�������©���Գ�����
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
            $urlcate        = $subcateId ? $subcateId : '';                 #����
            $urlManu        = $manuId ? "_" . $manuId : '';                 #Ʒ��
            $urlPrice       = 'noPrice'!==$priceId ? "_" . $priceId : '';   #�۸�
            $urlParam       = $paramVal ? "_" . $paramVal : '';             #���ϲ���
            $urlQuery       = $queryType ? "_" . $queryType : '_1';         #����
            $urlStyle       = $style ? "_" . $style : '_1';                 #�б���ʾ��ʽ
            $urlLocation    = $locationId ? "_" . $locationId : '_0';       #����
            $urlHot         = $isHot ? "_hot"  : '';                        #�����Ƽ��������⴦��
            $urlPage        = $page ? "_" . $page : '_1';                   #ҳ��

            #�ؼ��ַ�ҳ�滻�ã�����ת��
            if ($keyword && '{keyword}' != $keyword) {
                $keyword = ZOL_String::escape($keyword);
                $keyword = str_replace('%', '@', $keyword);
            }
            if ($paramVal && $keyword) {
                $urlParam .= "-k" . $keyword;   #�ؼ���
            } else if (!$paramVal && $keyword) {
                $urlParam .= "_k" . $keyword;   #�ؼ���
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
	* ��ȡ�б�ҳ����
	* @param array �������
	*/
	public static function getListShortUrl($paramArr)
	{
        $options = array(
            'subcateId'     => 0,    #����ID
            'subcateEnName' => 0,    #����Ӣ����
            'enManu'        => '',   #Ʒ��ID
            'priceId'       => 'noPrice', #�۸�
            'paramVal'      => '',   #���ϲ���
            'enQuery'       => '',   #����
            'enStyle'       => '',   #��ʾ��ʽ
            'enLocation'    => '',   #����
            'keyword'       => '',   #�ؼ���
            'page'          => 1,    #ҳ��
            'rewrite'       => 1,    #�Ƿ�α��̬
            'isHistory'     => 0,    #�Ƿ�ȡ��ʷ�б�
            'appendParam'   => 0,    #���ݾɴ������
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        if ($appendParam && is_array($appendParam)) { #���ݾɴ��������������ļ��϶�������©���Գ�����
            extract($appendParam);
            $paramVal = is_array($paramVal) ? implode('-', $paramVal) : $paramVal;
        }
        $subcateEnName = strtolower($subcateEnName);
        $enManu = str_replace(chr(32), '',$enManu);
        if (!$enManu && isset($paramArr['manuId']) && $manuId) {
            #��ѯ˳�� $enManuArr > helper > ���ݿ�
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
            #��ѯ˳�� $enLocationArr > helper > ���ݿ�
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

		if (!$rewrite) { #δ��
			$url = '/index.php?c=List&subcateId=' . $subcateId;
			$url .= $manuId ? '&manuId=' . $manuId : '';

			if (is_array($appendParam)) {
				$url .= '&' . http_build_query($appendParam);
			} else if ($appendParam) {
				$url .= '&' . $appendParam;
			}
			return $url;
		} else {
            $urlManu        = $enManu ? strtolower($enManu).'/' : '';                   #Ʒ��
            $urlPrice       = 'noPrice' !==$priceId ? ($paramVal ? $priceId.'_' : $priceId.'/') : ''; #�۸�
            $urlParam       = $paramVal ? str_replace('-', '_', $paramVal).'/' : '';                   #���ϲ���
            $urlQuery       = $enQuery ? $enQuery : '';      #����
            $urlStyle       = $enStyle ? ($enQuery ? '_'.$enStyle : $enStyle) : '';              #�б���ʾ��ʽ
            $urlLocation    = $enLocation ? $enLocation.'/' : '';         #����
            $urlPage        = $page != 1 ? ($enQuery || $enStyle ? '_'.$page : $page) : '';      #ҳ��
            $urlkword       = '{keyword}' != $keyword ? str_replace('%', '@', ZOL_String::escape($keyword)) : $keyword; #�ؼ���
            
            $url = '/'.$subEnName.$urlManu.$urlPrice.$urlParam.$urlLocation.$urlQuery.$urlStyle.$urlPage;
            if ($urlQuery || $urlStyle || $urlPage) $url .= '.html';
            if ($urlkword) $url .= "?k=$urlkword";

        }
		return $url;
	}

    /**
	* ��ȡ��ʷ�б�ҳ���� 
	* @param array �������
	*/
	public static function getHistoryListUrl($paramArr)
	{
        $options = array(
            'subcateId'     => 0,    #����ID
            'subcateEnName' => 0,    #����Ӣ����
            'manuId'        => 0,    #Ʒ��ID
            'priceId'       => 1,    #�۸�
            'paramVal'      => '',   #���ϲ���
            'queryType'     => 0,    #����
            'keyword'       => 0,    #�ؼ���
            'page'          => 1,    #ҳ��
            'rewrite'       => 1,    #�Ƿ�α��̬
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
            $urlParam = $paramVal ? "_" . $paramVal : '_0'; #���ϲ���
            #�ؼ��ַ�ҳ�滻�ã�����ת��
            if ($keyword && '{keyword}' != $keyword) {
                $keyword = ZOL_String::escape($keyword);
                $keyword = str_replace('%', '@', $keyword);
            }
            if ($paramVal && $keyword) {
                $urlKeyword = "-k" . $keyword;   #�ؼ���
            } else if (!$paramVal && $keyword) {
                $urlKeyword = "_k" . $keyword;   #�ؼ���
            } else {
                $urlKeyword = '';
            }

            $url = '/history/subcate'.$subcateId.'_'.$manuId.'_'.$priceId.$urlParam.$urlKeyword.'_'.$queryType.'_'.$page.".html";
        }
		return $url;
	}
    
    /**
	* ������еĸ�������
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
	* ��ȡ���а����ҳ����
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
	 * �õ�ϵ�а����ҳ����
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
	* ��ȡ��Ʒ���������
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
     * �õ�wap������
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
	* ��ȡ���ҳ����
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

