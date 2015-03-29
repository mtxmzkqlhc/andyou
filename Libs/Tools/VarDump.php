<?php
/**
* ��ʽ������
* @author zhongwt<zhogn.weitao@zol.com.cn>
* @copyright (c) 2011-1-16
*/
class Libs_Tools_VarDump{
    /**
     * ��ʾ�����Ľṹ
     */
    public static function showVar($data){
        return '<style>
                .clearfix:after{content: ".";display: block;height: 0;clear: both;visibility: hidden;}
                .clearfix {display: inline-block;}
                * html .clearfix {height: 1%;}
                .clearfix {display: block;}
                .vardump_domain{float:none;line-height:22px;font-size:12px;padding-bottom:5px;}
                .vardump_area{border: 1px dotted #ccc;  margin: 2px 15px;}
                .vardump_title{padding-left: 10px;color:#0085E7;border-bottom: 1px dotted #CCCCCC;margin-bottom:5px;}
                .vardump_contnt{padding-left: 15px;}
                .vardump_contnt_row{float:none;clear:both;}
                .vardump_var{padding:0 10px;}
                .vardump_varkey{float:left;}
                .vardump_varval{float:left;clear:right;}

                </style>
                <div class="vardump_domain clearfix">'
               .self::_showVar($data)
               .'</div>';
    }

    public static function _showVar($data){
        $outStr = '';
        if(is_object($data)){ //����
            $outStr .= self::showObject($data);
        }elseif (is_array($data)){//����
            $outStr .= self::showArray($data);
        }elseif(is_resource($data)){
            $outStr .= self::showResource($data);
        }elseif(is_string($data)){
            $outStr .= self::showString($data);
        }elseif(is_float($data)){
            $outStr .= self::showFloat($data);
        }elseif(is_integer($data)){
            $outStr .= self::showInt($data);
        }elseif(is_bool($data)){
            $outStr .= self::showBool($data);
        }elseif(is_null($data)){
            $outStr .= self::showNull($data);
        }
        return $outStr ;
    }
    /**
     * ��ʾ����
     */
    public static function showObject($obj){
        $outStr = '';
        if(is_object($obj)){
            $outStr .= '<div class="vardump_area clearfix">
                       <div class="vardump_title">����:' . get_class($data) . '</div>
                       <div class="vardump_contnt clearfix">';
            $attrCnt = count($obj);
            //������ж��������
            $attrKeys = get_object_vars($obj);
            foreach($attrKeys as $key => $val){
                $val = self::_showVar($val);
                $outStr .= "<div class='vardump_contnt_row clearfix'><div class='vardump_varkey'>[{$key}] => </div><div  class='vardump_varval'>{$val}</div></div>";
            }
            $outStr .= '</div></div>';
        }

        return $outStr;

    }
    /**
     * ��ʾ����
     */
    public static function showArray($arr){
        $outStr = '';
        if(is_array($arr)){
            $attrCnt = count($arr);
            $outStr .= '<div class="vardump_area clearfix">
                        <div class="vardump_title">����  <font color="#aaa">������' . $attrCnt . '</font></div>
                        <div class="vardump_contnt clearfix">';
            //������ж��������
            foreach($arr as $key => $val){
                $val = self::_showVar($val);
                $outStr .= "<div class='vardump_contnt_row clearfix'><div class='vardump_varkey'>[{$key}] => </div><div  class='vardump_varval'>{$val}</div></div>";
            }
            $outStr .= '</div></div>';
        }

        return $outStr;

    }
    /**
     * ��ʾ��Դ
     */
    public static function showResource($res){
        return self::_wrapShowStr('<font color="#52BA00">(Resource)</font> ' .get_resource_type ($res));

    }
    /**
     * ��ʾ�ַ���
     */
    public static function showString($str){
        $str1 = preg_replace('/(\.jpeg)|(\.jpg)|(\.png)|(\.gif)/', '', $str);
        if ($str1 ==$str )
            return self::_wrapShowStr('<font color="#52BA00">(String)</font> ' .htmlSpecialChars($str));
        else
            return self::_wrapShowStr('<font color="#52BA00">(String)</font><img src="'.$str.'" width="50" style="margin-top: 10px;"><br> ' .htmlSpecialChars($str));
    }

    public static function showFloat($data){
        return self::_wrapShowStr('<font color="#52BA00">(Float)</font> ' .$data);
    }

    public static function showInt($data){
        return self::_wrapShowStr('<font color="#52BA00">(Int)</font> ' .$data);
    }
    public static function showBool($data){
        return self::_wrapShowStr('<font color="#52BA00">(bool)</font> ' .($data ? true : false));
    }

    public static function showNull($data){
        return self::_wrapShowStr('Null');
    }

    private static function _wrapShowStr($str){
        return '<div class="vardump_area">
                   <div class="vardump_var">'.$str.'</font></div>
               </div>';
    }
}
?>
