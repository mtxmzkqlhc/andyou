<?php
/**
 * 获得分页html,目前使用的bootstrap模板
 * @author 钱志伟 2013-1-30
 */
class ZOL_Page {
    public static function getPageDom($paramArr){
        $reccount = isset($paramArr['reccount']) ? (int)$paramArr['reccount'] : 0;
        $page     = isset($paramArr['page']) ? (int)$paramArr['page'] : 1;
        $pagesize = isset($paramArr['pagesize']) ? (int)$paramArr['pagesize'] : 20;
        $pageUrlParm = $paramArr['baseUrl'];

        $pagesize = $pagesize ? $pagesize : 20;
        $showCnt = 5;
        $showHalfCnt = ceil($showCnt / 2);

        $total_page = ceil($reccount / $pagesize);

        $i = 0; 
        $pagestr = '' ;
        if($total_page == 0 )return '';

        if(1<$page){
            $pagestr .= '<li><a href="'.self::_getPageUrl(1,$pageUrlParm).'">首页</a></li><li><a href="'.self::_getPageUrl($page-1,$pageUrlParm).'" >上一页</a></li>';
        }

        while($i<$total_page){
            $i++;   
            if($i < ($page-$showHalfCnt) && ($total_page-$i) > $showCnt) continue;
            elseif ($i > ($page+$showHalfCnt) && $i > $showCnt) break;
            elseif ($i == $page) $pagestr .= '<li class="disabled"><a>'.$i.'</a></li>';
            else {  
                $pagestr .= '<li><a href="'. self::_getPageUrl($i,$pageUrlParm).'">'.$i.'</a></li>';
            }
        }

        if($total_page >$page)$pagestr .= '<li><a href="'.self::_getPageUrl($page+1 , $pageUrlParm).'" >下一页</a></li>
                                           <li><a href="'.self::_getPageUrl($total_page ,$pageUrlParm).'" >尾页 ('.$total_page.')</a></li>';

        $start = ($page-1) * $pagesize + 1;
        $end = $page * $pagesize;
        $end = $end > $reccount ? $reccount : $end; 

        $pagestr = $total_page <= 1 ? '' : "<div class='page'><div class='pagebar'> " . $pagestr."</div>";

        $pagestr = "<ul class='pager'>".$pagestr."</ul>";

        //$pagestr = iconv('UTF8', 'gb2312', $pagestr);

        return $pagestr;

    }

    //生成分页链接
    public static function _getPageUrl($page){
        $url = self::getUrl();
        $page = (int)$page; 
        $url .= "&page={$page}";
        return $url;
    }

    public static function getUrl(){
        $server = $_SERVER;//print_r($server);exit;
        $scriptFile = $server['SCRIPT_NAME'];
        $uri = isset($server['REQUEST_URI']) ? $server['REQUEST_URI'] : '';
        $arr = explode('?', $uri);
        
        $url = $scriptFile.'?'.$arr[1];
        $url = preg_replace('/(&page=(\w+)?)?/', '', $url);
        $url = rtrim($url, '?');
        return $url;
    }
}