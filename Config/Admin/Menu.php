<?php

/**
 * ��̨�˵�����
 * ͼ��ͼƬѡ����Բ��� http://clabs.co/projects/metro/icon.html
 */
return array(
    
    '���ò���' => array(
        'icon' => 'donate.png',
        'class' => 'open2',
        'items' => array(
            'ǰ̨����' => array(
                'url' => '?c=Checkout',
                'ctrl' => 'Checkout',
                'target' => '_self',
            ),
            '���ֶһ�' => array(
                'url' => '?c=CheckoutFromScore',
                'ctrl' => 'CheckoutFromScore',
                'target' => '_self',
            ),
            '�ο�����' => array(
                'url' => '?c=CheckoutOtherPro&ctype=2',
                'ctrl' => 'CheckoutOtherPro',
                'target' => '_self',
            ),
        ),
    ),
    '��Ʒ����' => array(
        'icon' => 'menu-item.png',
        'class' => 'open2',
        'items' => array(
            '�鿴��Ʒ' => array(
                'permission' => array(0),//ֻ����ͨ����Ա�ɼ�
                'url' => '?c=ProductSm&a=Default',
                'ctrl' => 'ProductSm',
                'target' => '_self',
            ),
            '��Ʒ���' => array(
                'permission' => array(1),
                'url' => '?c=InStorage',
                'ctrl' => 'InStorage',
                'target' => '_self',
            ),
            '��Ʒ�б�' => array(
                'permission' => array(1),
                'url' => '?c=Product&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            '��Ʒ����' => array(
                'permission' => array(1),
                'url' => '?c=ProductCate&a=Default',
                'ctrl' => 'ProductCate',
                'target' => '_self',
            ),
            '�ο�����' => array(
                'permission' => array(1),
                'url' => '?c=MemeberOtherPro&ctype=2',
                'ctrl' => 'MemeberOtherPro',
                'target' => '_self',
            ),
        ),
    ),
    '��Ա����' => array(
        'icon' => 'users.png',
        'class' => 'open2',
        'items' => array(
            '��ӻ�Ա' => array(
                'url' => '?c=Bills&isAddUser=1',
                'ctrl' => 'addUserFb',
                'target' => '_self',
            ),
            '��ֵ���Ա' => array(
                'url' => '?c=Member&a=ToAddUserFromBill&andCard=1',
                'ctrl' => 'ToAddUserFromBill',
                'target' => '_self',
            ),
            '��Ա����' => array(
                'url' => '?c=Member&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            '��Ա����' => array(
                'permission' => array(1),
                'url' => '?c=MemberCate&a=Default',
                'ctrl' => 'MemberCate',
                'target' => '_self',
            ),
        ),
    ),
    'Ա������' => array(
        'permission' => array(1),
        'icon' => 'user.png',
        'items' => array(
            'Ա���б�' => array(
                'url' => '?c=Staff&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            'Ա������' => array(
                'url' => '?c=StaffCate&a=Default',
                'ctrl' => 'StaffCate',
                'target' => '_self',
            ),
        ),
    ),
    '���Ѽ�¼' => array(
        'permission' => array(0),
        'icon' => 'menu.png',
        'items' => array(
            '���Ѷ���' => array(
                'url' => '?c=Bills',
                'ctrl' => 'Bills',
                'target' => '_self',
            ),
        ),
    ),
    '��ѯͳ��' => array(
        'permission' => array(1),
        'icon' => 'menu.png',
        'items' => array(
            '���Ѷ���' => array(
                'url' => '?c=Bills',
                'ctrl' => 'Bills',
                'target' => '_self',
            ),
            '�쳣�տ�' => array(
                'url' => '?c=Bills&hasChangePrice=1',
                'ctrl' => 'Bills',
                'target' => '_self',
            ),
            '������ϸ' => array(
                'url' => '?c=BillsItem',
                'ctrl' => 'BillsItem',
                'target' => '_self',
            ),
            '����ͳ��' => array(
                'url' => '?c=LogScoreChange',
                'ctrl' => 'LogScoreChange',
                'target' => '_self',
            ),
            '��������ͳ��' => array(
                'url' => '?c=LogCardChange',
                'ctrl' => 'LogCardChange',
                'target' => '_self',
            ),
            '���ͳ��' => array(
                'url' => '?c=LogInStorage',
                'ctrl' => 'LogInStorage',
                'target' => '_self',
            ),
            '�ο�����' => array(
                'url' => '?c=LogUseOtherPro&ctype=2',
                'ctrl' => 'LogUseOtherPro',
                'target' => '_self',
            ),
        ),
    ),
    'ϵͳ����' => array(
        'permission' => array(1),
        'icon' => 'run.png',
        'items' => array(
            '����Ա' => array(
                'url' => '?c=AdminUser',
                'ctrl' => 'AdminUser'
            ),
            '���ù���' => array(
                'url' => '?c=Options',
                'ctrl' => 'Options'
            ),
            '���ݱ���' => array(
                'url' => '?c=Data&a=BackUp',
                'ctrl' => 'BackUp'
            ),
        ),
    ),
);
