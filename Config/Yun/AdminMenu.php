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
        ),
    ),
);
