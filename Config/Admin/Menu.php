<?php

/**
 * 后台菜单配置
 * 图标图片选择可以参照 http://clabs.co/projects/metro/icon.html
 */
return array(
    '会员管理' => array(
        'icon' => 'group',
        'items' => array(
            '会员列表' => array(
                'url' => '?c=Member&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            '品牌等级' => array(
                'url' => '?c=User_GrowScore',
                'ctrl' => 'User_GrowScore',
                'target' => '_self',
            ),
        ),
    ),
    '权限管理' => array(
        'icon' => 'group',
        'items' => array(
            '用户权限设置' => array(
                'url' => '/?c=Permission_UserRole&uc=user',
                'ctrl' => 'Permission_UserRole'
            ),
            '角色管理' => array(
                'url' => '/?c=Permission_Role&uc=user',
                'ctrl' => 'Permission_Role'
            ),
            '权限资源管理' => array(
                'url' => '/?c=Permission_Option&uc=user',
                'ctrl' => 'Permission_Option'
            ),
        ),
    ),
    '财务管理' => array(
        'icon' => 'inbox',
        'items' => array(
            '用户余额' => array(
                'url' => '/?c=Pay_Bill',
                'ctrl' => 'Pay_Bill',
                'target' => '_blank',
            ),
            '充值记录' => array(
                'url' => '/?c=Pay_Pay',
                'ctrl' => 'Pay_Pay',
                'target' => '_blank',
            ),
            '消费记录' => array(
                'url' => '/?c=Pay_Consume',
                'ctrl' => 'Pay_Consume',
                'target' => '_blank',
            ),
            '发票管理' => array(
                'url' => '/?c=Pay_Invoice',
                'ctrl' => 'Pay_Invoice',
                'target' => '_blank',
            ),
            '财务充值' => array(
                'url' => '/?c=Pay_Finance',
                'target' => '_blank',
            ),
            '奖品设置' => array(
                'url' => '/?c=Pay_trophyInfo',
                'target' => '_blank',
            ),
            '奖品兑换记录' => array(
                'url' => '/?c=Pay_exchangeTrophyLog',
                'target' => '_blank',
            ),
        ),
    ),
);
