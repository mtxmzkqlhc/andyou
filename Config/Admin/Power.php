<?php
/**
 * 权限相关配置
 */
return array(
    'USER_PRE_AUDIT' => array('zhongwt','fuhl','wuwei','li.jing1','tian.yazhu'),#具有用户预审核权限的人 毒龙那边的人
    'USER_AUDIT'     => array('zhongwt','flora','fuhl','quyw'),#具有用户审核权限的人 黄姐那边的人
    'USER_PRE_AUDIT_OK_MAIL' => array('gao.weiwei@zol.com.cn','qu.yanwen@zol.com.cn'),#预审核通过后，需要给谁发邮件 'zhong.weitao@zol.com.cn','shen.tong@zol.com.cn',
    'USER_TEST'      => array('zhongwt','wangmc1','lvjian','liwenqing','zhangxc','mengkf','fuhl'),  #测试用户权限组，只有这个组的人，能看到一些测试数据
    //后台财务充值邮件发送
    'FINANCE_MAIL'       => array('zhong.weitao@zol.com.cn')
);
