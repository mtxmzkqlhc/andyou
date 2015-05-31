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
            '前台收银' => array(
                'url' => '?c=Checkout',
                'ctrl' => 'Checkout',
                'target' => '_self',
            ),
            '积分兑换' => array(
                'url' => '?c=CheckoutFromScore',
                'ctrl' => 'CheckoutFromScore',
                'target' => '_self',
            ),
            '次卡消费' => array(
                'url' => '?c=CheckoutOtherPro&ctype=2',
                'ctrl' => 'CheckoutOtherPro',
                'target' => '_self',
            ),
        ),
    ),
    '商品管理' => array(
        'icon' => 'menu-item.png',
        'class' => 'open2',
        'items' => array(
            '查看商品' => array(
                'permission' => array(0),//只有普通管理员可见
                'url' => '?c=ProductSm&a=Default',
                'ctrl' => 'ProductSm',
                'target' => '_self',
            ),
            '商品入库' => array(
                'permission' => array(1),
                'url' => '?c=InStorage',
                'ctrl' => 'InStorage',
                'target' => '_self',
            ),
            '商品列表' => array(
                'permission' => array(1),
                'url' => '?c=Product&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            '商品分类' => array(
                'permission' => array(1),
                'url' => '?c=ProductCate&a=Default',
                'ctrl' => 'ProductCate',
                'target' => '_self',
            ),
            '次卡管理' => array(
                'permission' => array(1),
                'url' => '?c=MemeberOtherPro&ctype=2',
                'ctrl' => 'MemeberOtherPro',
                'target' => '_self',
            ),
        ),
    ),
    '会员管理' => array(
        'icon' => 'users.png',
        'class' => 'open2',
        'items' => array(
            '添加会员' => array(
                'url' => '?c=Bills&isAddUser=1',
                'ctrl' => 'addUserFb',
                'target' => '_self',
            ),
            '充值办会员' => array(
                'url' => '?c=Member&a=ToAddUserFromBill&andCard=1',
                'ctrl' => 'ToAddUserFromBill',
                'target' => '_self',
            ),
            '会员管理' => array(
                'url' => '?c=Member&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            '会员分类' => array(
                'permission' => array(1),
                'url' => '?c=MemberCate&a=Default',
                'ctrl' => 'MemberCate',
                'target' => '_self',
            ),
        ),
    ),
    '员工管理' => array(
        'permission' => array(1),
        'icon' => 'user.png',
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
    '消费记录' => array(
        'permission' => array(0),
        'icon' => 'menu.png',
        'items' => array(
            '消费订单' => array(
                'url' => '?c=Bills',
                'ctrl' => 'Bills',
                'target' => '_self',
            ),
        ),
    ),
    '查询统计' => array(
        'permission' => array(1),
        'icon' => 'menu.png',
        'items' => array(
            '消费订单' => array(
                'url' => '?c=Bills',
                'ctrl' => 'Bills',
                'target' => '_self',
            ),
            '异常收款' => array(
                'url' => '?c=Bills&hasChangePrice=1',
                'ctrl' => 'Bills',
                'target' => '_self',
            ),
            '订单明细' => array(
                'url' => '?c=BillsItem',
                'ctrl' => 'BillsItem',
                'target' => '_self',
            ),
            '积分统计' => array(
                'url' => '?c=LogScoreChange',
                'ctrl' => 'LogScoreChange',
                'target' => '_self',
            ),
            '卡内消费统计' => array(
                'url' => '?c=LogCardChange',
                'ctrl' => 'LogCardChange',
                'target' => '_self',
            ),
            '入库统计' => array(
                'url' => '?c=LogInStorage',
                'ctrl' => 'LogInStorage',
                'target' => '_self',
            ),
            '次卡消费' => array(
                'url' => '?c=LogUseOtherPro&ctype=2',
                'ctrl' => 'LogUseOtherPro',
                'target' => '_self',
            ),
        ),
    ),
    '系统管理' => array(
        'permission' => array(1),
        'icon' => 'run.png',
        'items' => array(
            '管理员' => array(
                'url' => '?c=AdminUser',
                'ctrl' => 'AdminUser'
            ),
            '配置管理' => array(
                'url' => '?c=Options',
                'ctrl' => 'Options'
            ),
            '数据备份' => array(
                'url' => '?c=Data&a=BackUp',
                'ctrl' => 'BackUp'
            ),
        ),
    ),
);
