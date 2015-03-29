<?php

/**
 * 后台菜单配置
 * 图标图片选择可以参照 http://clabs.co/projects/metro/icon.html
 */
return array(
    
    '商品管理' => array(
        'icon' => 'barcode',
        'items' => array(
            '商品列表' => array(
                'url' => '?c=Product&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            '商品分类' => array(
                'url' => '?c=ProductCate&a=Default',
                'ctrl' => 'ProductCate',
                'target' => '_self',
            ),
        ),
    ),
    '会员管理' => array(
        'icon' => 'group',
        'items' => array(
            '会员列表' => array(
                'url' => '?c=Member&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            '会员分类' => array(
                'url' => '?c=MemberCate&a=Default',
                'ctrl' => 'MemberCate',
                'target' => '_self',
            ),
        ),
    ),
    '员工管理' => array(
        'icon' => 'sitemap',
        'items' => array(
            '员工列表' => array(
                'url' => '?c=Staff&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            '员工分类' => array(
                'url' => '?c=StaffCate&a=Default',
                'ctrl' => 'StaffCate',
                'target' => '_self',
            ),
        ),
    ),
    '权限管理' => array(
        'icon' => 'user-md',
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
