<?php
/**
 * url
 * Ǯ־ΰ 2013-5-28
 */
class Libs_Global_Urls
{
    public static function getArticleCommentUrl($paramArr=array())
    {
        $options = array(
            'class_id'      => 0,   #Ƶ��id
            'docId'         => 0,   #����id
            'type'          => 0,   #˳��ʽ 0=>���� 1=>����
        );
        $options = array_merge($options, $paramArr);
        extract($options);
        
        #���Ƶ��ID������ID�Ķ�Ӧ
        $Cls2cmtArr = ZOL_Api::run("Article.Comment.classToComment" , array(
            'typeId'         => 1,               #�����������ͣ�0Ϊ����
        ));
        
        $url = "http://comments.zol.com.cn/";
        if(!isset($Cls2cmtArr[$class_id])) return $url;
        $cmtClsId = $Cls2cmtArr[$class_id];
        $url .= "{$cmtClsId}/{$docId}_{$type}_1.html";
        return $url;
    }
}