<?php
/**
 * �����ز���
 *
 * LICENSE:
 * @author ��ͨ shen.tong@zol.com.cn
 * @version 1.0
 * @copyright  http://www.zol.com.cn
 * @todo
 * @changelog 
 * 2012-11-29 created by shen.tong@zol.com.cn
 */
class API_Table extends ZOL_Abstract_Pdo {
    //��¼���SQL
    public static $_strsql = '';
        
    //��ѯʱʹ��
    const ROW = 'RRRRRRRRRRRR';

    //��ȡ���SQL(���������)
    public static function getSql($tablename = '', $field_arr = array(), $where_arr = array(), $where_style_arr = array()) {
        if (!$field_arr || !$tablename)
            return;
        //��ϳ�SQL���
        $strsql = self::mergeSql($tablename, $field_arr, $where_arr, $where_style_arr);
        
        //��¼���SQL
        self::$_strsql = $strsql;
        
        return $strsql;
    }

    //��ȡ���SQL(ɾ��)
    public static function getDelSql($tablename = '', $where_arr = array(), $where_style_arr = array()) {
        if (!$where_arr || !$tablename)
            return;
        //��ϳ�SQL���
        $strsql = self::mergeDelSql($tablename, $where_arr, $where_style_arr);
        
        //��¼���SQL
        self::$_strsql = $strsql;
        
        return $strsql;
    }

    //����SQL
    public static function mergeSql($tablename = '', $field_arr = array(), $where_arr = array(), $where_style_arr = array()) {
        if (!$tablename || !$field_arr)
            return;
        //����SQL���
        $strsql = '';
        //�޶�δ��$where_arr��Ϊ����
        if ($where_arr) {
            //�ֶδ�
            $field_str = '';
            foreach ($field_arr as $field => $value) {
                //���˷�ֹSQL
                $value = self::escapeStr($value);

                $field_str .= "`{$field}`='{$value}',";
            }
            $field_str = rtrim($field_str, ',');
            //������
            $where_str = '';
            foreach ($where_arr as $field => $value) {
                if ($where_str) {
                    $where_str .= " and ";
                }
                //�ж����� 
                $where_style = isset($where_style_arr[$field]) ? trim($where_style_arr[$field]) : '=';
                if ('in' == $where_style) {
                    $where_str .= "`{$field}` in ({$value})";
                } else {
                    //���˷�ֹSQL
                    $value = self::escapeStr($value);

                    $where_str .= "`{$field}`='{$value}'";
                }
            }
            //��ϳ�SQL���
            $strsql = "update " . $tablename . " set {$field_str} where {$where_str}";
        } else {
            //�ֶδ�,ֵ��
            $field_str = $val_str = '';
            foreach ($field_arr as $field => $value) {
                //���˷�ֹSQL
                $value = self::escapeStr($value);

                $field_str .= "`{$field}`,";
                $val_str .= "'{$value}',";
            }
            $field_str = rtrim($field_str, ',');
            $val_str = rtrim($val_str, ',');
            //��ϳ�SQL���
            $strsql = "insert into " . $tablename . "({$field_str}) values ({$val_str})";
        }
        return $strsql;
    }

    //����SQL
    public static function mergeDelSql($tablename = '', $where_arr = array(), $where_style_arr = array()) {
        if (!$tablename || !$where_arr)
            return;

        //����SQL���
        $strsql = '';
        //������
        $where_str = '';
        foreach ($where_arr as $field => $value) {
            if ($where_str) {
                $where_str .= " and ";
            }
            //�ж����� 
            $where_style = isset($where_style_arr[$field]) ? trim($where_style_arr[$field]) : '=';
            if ('in' == $where_style) {
                $where_str .= "`{$field}` in ({$value})";
            } else {
                //���˷�ֹSQL
                $value = self::escapeStr($value);

                $where_str .= "`{$field}`='{$value}'";
            }
        }
        //��ϳ�SQL���
        $strsql = "delete from " . $tablename . " where {$where_str}";

        //echo $strsql.'<hr />';
        return $strsql;
    }

    //��ѯ��
    public static function searchTable($param_arr = array()) {
        $options = array(
            'cdb' => '', //����-������(�������ⲿ���� , �ɺ��������� , ����)
            'cols' => '', //����-��ͨ��(�������ⲿ���� , �ɺ��������� , ����)
            'table' => '', //����-��ͨ��(�������ⲿ���� , �ɺ��������� , ����)
            'cols_num' => '', //����-��ͨ��(������)(�������ⲿ���� , �ɺ���������)
            //ֱ��SQL
            'sql' => '', //��չ-ָ��SQLģ��(�������ⲿ���� , �ɺ��������� , ʹ��extend������)
            //leftjoin
            'leftjoin_cols' => '', //��չ-leftjoin���ȡ������(�������ⲿ���� , �ɺ��������� , ʹ��extend������)
            'leftjoin_table' => '', //��չ-leftjoin����(�������ⲿ���� , �ɺ��������� , ʹ��extend������)
            'leftjoin_condition' => '', //��չ-leftjoin������(�������ⲿ���� , �ɺ��������� , ʹ��extend������)
            //innerjoin
            'innerjoin_cols' => '', //��չ-innerjoin���ȡ������(�������ⲿ���� , �ɺ��������� , ʹ��extend������)
            'innerjoin_table' => '', //��չ-innerjoin����(�������ⲿ���� , �ɺ��������� , ʹ��extend������)
            'innerjoin_condition' => '', //��չ-innerjoin������(�������ⲿ���� , �ɺ��������� , ʹ��extend������)
            //groupby
            'groupby_cols' => '', //��չ-groupby���ȡ������(�������ⲿ���� , �ɺ��������� , ʹ��extend������)
            'groupby_condition' => '', //��չ-groupby������(�������ⲿ���� , �ɺ��������� , ʹ��extend������)
            'extend' => '', //ʹ����չ����:�ڱ���Ժ���������(�����ⲿ����)
            'return' => 'all', #���ؽ��:all-ȫ��,row-һ��,one-��һ�е�һ��(�����ⲿ����),pair-ָ���ֶ�Ϊ����ֵ�������
            'orderby' => '', #����:�Զ����ֶ�(�����ⲿ����)
            'count' => 0, //�Ƿ�ֻ��������(�����ⲿ����)
            'is_limit' => 0, //�Ƿ����ƶ�ȡ��������(�����ⲿ����)
            'limit_offset' => 0, #���Ʒ�������ʱ,��ʼ���(�����ⲿ����)
            'limit_num' => 20, #���Ʒ�������ʱ,��ȡ����(�����ⲿ����)
        );
        //����ѯ���ֶ�����
        $search_options = array();
        if (is_array($param_arr)) {
            $search_options = array_diff_key($param_arr, $options);
            $options = array_merge($options, $param_arr);
        }
        extract($options);
        //ȥ�����Ҷ���
        $cols = trim($cols, ',');
        $cols_num = trim($cols_num, ',');

        //������
        if (!$cols || !$table || !$cdb) {
            return;
        }

        //������
        $cols_num_arr = array();
        if ($cols_num) {
            $cols_num_arr = explode(',', $cols_num);
        }
        //�Ƿ�innerjoin
        $is_innerjoin = 0;

        //�����innerjoin , �����leftjoin��������,ֻ������ʹ��inner join
        if ($innerjoin_cols && $innerjoin_table && $innerjoin_condition) {
            //���Ǹ�ֵ
            $leftjoin_cols = $innerjoin_cols;
            $leftjoin_table = $innerjoin_table;
            $leftjoin_condition = $innerjoin_condition;

            $is_innerjoin = 1;
        }


        //�Ƿ�leftjoin
        $is_leftjoin = 0;

        //leftjoin��Ϣ
        if ($leftjoin_cols && $leftjoin_table && $leftjoin_condition) {
            //�ı���
            $cols = 'a.' . str_replace(',', ',a.', $cols);
            $cols .= ',b.' . str_replace(',', ',b.', $leftjoin_cols);

            //�ı�����,����a��b,��a��b
            $temp_arr = explode('=', $leftjoin_condition);
            $leftjoin_condition = (false === strpos($temp_arr[0], '.') ? 'a.' : '') . $temp_arr[0] . '=' . (false === strpos($temp_arr[1], '.') ? 'b.' : '') . $temp_arr[1];

            if ($orderby) {
                //�ı�orderby����,����a��b
                $temp_arr = explode(',', $orderby);
                $orderby_arr = array();
                if ($temp_arr) {
                    foreach ($temp_arr as $val) {
                        if (false === strpos($val, '.')) {
                            $orderby_arr[] = 'a.' . $val;
                        } else {
                            $orderby_arr[] = $val;
                        }
                    }
                }
                $orderby = implode(',', $orderby_arr);
            }

            //����ģ��
            $sql = 'select  #COLS#  from  #TABLE# as a ' . ($is_innerjoin ? ' inner join ' : ' left join ') . $leftjoin_table . ' as b on ' . $leftjoin_condition . '  #WHERE# #ORDERBY# #LIMIT#';

            //leftjoin
            $is_leftjoin = 1;
        }

        //��ѯ���� 
        $where = self::mergeWhere($search_options, $cols_num_arr, $is_leftjoin);

        //groupby��Ϣ
        $groupby = '';
        if ($groupby_cols) {
            //�ı���
            $cols = $groupby_cols;

            //�Ƿ���������
            if ($groupby_condition) {
                //��������
                $groupby = "group by " . $groupby_condition;
            }
        }


        //orderby��Ϣ
        $orderby = $orderby ? 'order by ' . $orderby : '';

        //��ȡ����
        $limit = '';
        if ($is_limit) {//������ƶ�ȡ����
            $limit = " limit {$limit_offset},{$limit_num}";
        }

        //���ֻ��ȡ����
        if ($count) {
            //�ı���
            $cols = "count('x')";
            //ȥ��orderby,limit,groupby��
            $limit = '';
            $orderby = '';
            $groupby = '';
            //ֻ����һ������ֵ
            $return = 'one';
        }

        //���ָ��SQLģ��
        $template = 'select  #COLS#  from  #TABLE#  #WHERE#  #GROUPBY# #ORDERBY# #LIMIT#';
        if ($sql) {
            $template = $sql;
        }

        //����sql
        $strsql = str_replace(
                array('#COLS#', '#TABLE#', '#WHERE#', '#GROUPBY#', '#ORDERBY#', '#LIMIT#'), array($cols, $table, $where, $groupby, $orderby, $limit), $template);

        //returnΪ����ʱ,���ؼ�ֵ��
        $pair_key = $pair_val = '';
        if (is_array($return)) {
            $pair_key = $return[0];
            $pair_val = $return[1];
            //�Ƿ����ֶ�---���ֶ�ʱΪ����
            if (is_array($pair_val)) {
                //����Ϊ�ַ���
                $return = 'pair_extend';
            } else if ($pair_val == self::ROW) {//ֵΪÿ��
                //����Ϊ�ַ���
                $return = 'pair_extend_row';
            } else {
                //����Ϊ�ַ���
                $return = 'pair';
            }
        }

        //��¼���SQL
        self::$_strsql = $strsql;

        //����
        $res = array();
        switch ($return) {
            case 'row':
                $res = $cdb->getRow($strsql);
                break;
            case 'one':
                $res = $cdb->getOne($strsql);
                break;
            case 'pair':
                $res = $cdb->getPairs($strsql, $pair_key, $pair_val);
                break;
            case 'pair_extend':
                $temp_res = $cdb->getAll($strsql);
                foreach ($temp_res as $row) {
                    $tarr = array();
                    foreach ($pair_val as $v) {
                        $tarr[$v] = $row[$v];
                    }
                    $res[$row[$pair_key]] = $tarr;
                }
                break;
            case 'pair_extend_row':
                $temp_res = $cdb->getAll($strsql);
                foreach ($temp_res as $row) {
                    $res[$row[$pair_key]] = $row;
                }
                break;
            case 'sql'://����SQL
                $res = $strsql;
                break;
            default:
                $res = $cdb->getAll($strsql);
                break;
        }
        return $res;
    }

    //����where����
    public static function mergeWhere($options = array(), $cols_num_arr = array(), $is_leftjoin = 0) {
        $where = '';
        foreach ($options as $key => $val) {
            //����Ϊ�޷���ȡ
            if (is_numeric($key) && 0 == $val) {
                $where = "and 1=0";
            } else {
                //�����leftjoin
                $leftjoin_pre = ''; //ǰ׺
                if ($is_leftjoin && false !== strpos($key, '.')) {
                    list($leftjoin_pre, $key) = explode('.', $key, 2);
                    $leftjoin_pre .= '.';
                }

                //�ֽⷽʽ
                $key_arr = explode('#', $key);

                //��ֵ����
                $key = $key_arr[0];
                $key_str = '`' . $key_arr[0] . '`';

                //�����leftjoin
                if ($is_leftjoin) {
                    if (!$leftjoin_pre) {
                        $leftjoin_pre = 'a.';
                    }
                    $key_str = $leftjoin_pre . $key_str;
                }

                //��ʽ
                $type = '=';

                //���˷�ֹSQL
                if (is_array($val)) {
                    $filter_val = ''; //����ʱ,����ʽ���д���,Ĭ�϶�����Ч��ֵ
                } else {
                    $filter_val = self::escapeStr(trim($val));
                }

                //ֵʹ�����ֻ��ַ���
                $val_str = in_array($key, $cols_num_arr) ? intval($val) : "'" . $filter_val . "'";

                //����з�ʽѡ��
                if (isset($key_arr[1])) {
                    $type = $key_arr[1];
                    switch ($type) {
                        case 'like':
                        case 'not like':

                            $val_str = self::escapeStr(trim($val));
                            //likeͨ���ת��
                            $val_str = str_replace(array('%', '_', '[', ']'), array('\%', '\_', '\[', '\]'), $val_str);


                            //�ж�
                            if (isset($key_arr[2])) {
                                if ('end' == $key_arr[2]) {
                                    $where .= " and {$key_str} {$type} '%{$val_str}'";
                                } else if ('first' == $key_arr[2]) {
                                    $where .= " and {$key_str} {$type} '{$val_str}%'";
                                } else {
                                    $where .= " and 1=0";
                                }
                            } else {
                                $where .= " and {$key_str} {$type} binary '%{$val_str}%'";
                            }
                            break;
                        case 'in':
                        case 'not in':
                            //������ת��������
                            if (!is_array($val)) {
                                $val = explode(',', $val);
                            }
                            if (is_array($val)) {
                                //��ʱ����
                                $temp = '';
                                foreach ($val as $v) {
                                    //ȥ������˫����
                                    $v = trim($v, "'");
                                    //ʹ��ֵ
                                    $val_str = in_array($key, $cols_num_arr) ? intval($v) : "'" . self::escapeStr(trim($v)) . "'";
                                    $temp .= "{$val_str},";
                                }
                                $temp = rtrim($temp, ',');
                                //�����ֵʱ
                                if ($temp) {
                                    $where .= " and {$key_str} {$type} ({$temp})";
                                } else {//��ѯʧ��
                                    $where .= " and 1=0";
                                }
                            } else {
                                $where .= " and {$key_str} {$type} ({$val_str})";
                            }
                            break;
                        case 'or':
                            $where .= " or (";
                            if (is_array($val)) {
                                //��ʱ����
                                $temp = '';
                                foreach ($val as $v) {
                                    //ȥ������˫����
                                    $v = trim($v, "'");

                                    //ʹ��ֵ
                                    $val_str = in_array($key, $cols_num_arr) ? intval($v) : "'" . self::escapeStr(trim($v)) . "'";
                                    $temp .= "{$key_str}={$val_str} or";
                                }
                                $temp = rtrim($temp, 'or');
                                $where .= $temp;
                            } else {
                                $where .= "{$key_str}={$val_str}";
                            }
                            $where .= ")";
                            break;
                        case '=':
                        case '!=':
                        case '<=':
                        case '<':
                        case '>':
                        case '>=':
                            $where .= " and {$key_str}{$type}{$val_str}";
                            break;
                        default:
                            $where .= " and {$key_str}={$val_str}";
                    }
                } else {
                    $where .= " and {$key_str}={$val_str}";
                }
            }
        }
        //���ѯ����ʱ,ȥ����һ���� and ,����еĻ�
        $where = trim($where);
        if ($where) {
            //ȥ��ͷ����and
            if (0 === strpos($where, 'and'))
                $where = trim(substr($where, 3));
            else if (0 === strpos($where, 'or'))
                $where = trim(substr($where, 2));

            if ($where) {
                $where = ' where ' . $where . ' ';
            }
        }
        return $where;
    }

    //�����ַ���,��ֹSQLע��
    public static function escapeStr($string = '') {
        if (!defined('MAGIC_OPEN')) {
            define('MAGIC_OPEN',get_magic_quotes_gpc());
        }
        if (!$string)
            return $string;

        //�������
        if (MAGIC_OPEN) {
            $string = stripslashes($string); //ȥ��
        }
        //���ɹ���
        $string = mysql_escape_string($string);

        return $string;
    }

}

