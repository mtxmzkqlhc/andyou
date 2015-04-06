<?php

/**
 * 后台菜单配置
 * 图标图片选择可以参照 http://clabs.co/projects/metro/icon.html
 */
return array(
    
    '常用操作' => array(
        'icon' => 'print',
        'items' => array(
            '前台收银' => array(
                'url' => '?c=Checkout',
                'ctrl' => 'Checkout',
                'target' => '_self',
            ),
        ),
    ),
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
    '财务管理' => array(
        'icon' => 'inbox',
        'items' => array(
            '消费订单' => array(
                'url' => '?c=Bills',
                'ctrl' => 'Bills',
                'target' => '_self',
            ),
            '订单明细' => array(
                'url' => '?c=BillsItem',
                'ctrl' => 'BillsItem',
                'target' => '_self',
            ),
        ),
    ),
    '系统管理' => array(
        'icon' => 'user-md',
        'items' => array(
            '管理员' => array(
                'url' => '?c=AdminUser',
                'ctrl' => 'AdminUser'
            ),
            '配置管理' => array(
                'url' => '?c=Option',
                'ctrl' => 'Option'
            ),
        ),
    ),
);
