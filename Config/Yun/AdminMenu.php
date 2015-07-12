<?php

/**
 * 后台菜单配置
 * 图标图片选择可以参照 http://clabs.co/projects/metro/icon.html
 */
return array(
    
    '常用操作' => array(
        'icon' => 'donate.png',
        'class' => 'open2',
        'items' => array(
            '会员管理' => array(
                'url' => '?c=Member&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            '订单管理' => array(
                'url' => '?c=Bills&a=Default',
                'ctrl' => 'Bills',
                'target' => '_self',
            ),
            '商品管理' => array(
                'url' => '?c=Product&a=Default',
                'ctrl' => 'Product',
                'target' => '_self',
            ),
            '员工管理' => array(
                'url' => '?c=Staff&a=Default',
                'ctrl' => 'Staff',
                'target' => '_self',
            ),
        ),
    ),
);
