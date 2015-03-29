<?php
/**
 * url
 * 钱志伟 2013-5-28
 */
class Libs_Global_Urls
{
    public static function getArticleCommentUrl($paramArr=array())
    {
        $options = array(
            'class_id'      => 0,   #频道id
            'docId'         => 0,   #文章id
            'type'          => 0,   #顺序方式 0=>最新 1=>最热
        );
        $options = array_merge($options, $paramArr);
        extract($options);
        
        #获得频道ID与评论ID的对应
        $Cls2cmtArr = ZOL_Api::run("Article.Comment.classToComment" , array(
            'typeId'         => 1,               #返回数据类型，0为数组
        ));
        
        $url = "http://comments.zol.com.cn/";
        if(!isset($Cls2cmtArr[$class_id])) return $url;
        $cmtClsId = $Cls2cmtArr[$class_id];
        $url .= "{$cmtClsId}/{$docId}_{$type}_1.html";
        return $url;
    }
}