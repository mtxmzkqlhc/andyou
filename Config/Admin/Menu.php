<?php

/**
 * ��̨�˵�����
 * ͼ��ͼƬѡ����Բ��� http://clabs.co/projects/metro/icon.html
 */
return array(
    
    '���ò���' => array(
        'icon' => 'print',
        'items' => array(
            'ǰ̨����' => array(
                'url' => '?c=Checkout',
                'ctrl' => 'Checkout',
                'target' => '_self',
            ),
        ),
    ),
    '��Ʒ����' => array(
        'icon' => 'barcode',
        'items' => array(
            '��Ʒ�б�' => array(
                'url' => '?c=Product&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            '��Ʒ����' => array(
                'url' => '?c=ProductCate&a=Default',
                'ctrl' => 'ProductCate',
                'target' => '_self',
            ),
        ),
    ),
    '��Ա����' => array(
        'icon' => 'group',
        'items' => array(
            '��Ա�б�' => array(
                'url' => '?c=Member&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            '��Ա����' => array(
                'url' => '?c=MemberCate&a=Default',
                'ctrl' => 'MemberCate',
                'target' => '_self',
            ),
        ),
    ),
    'Ա������' => array(
        'icon' => 'sitemap',
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
    '�������' => array(
        'icon' => 'inbox',
        'items' => array(
            '���Ѷ���' => array(
                'url' => '?c=Bills',
                'ctrl' => 'Bills',
                'target' => '_self',
            ),
            '������ϸ' => array(
                'url' => '?c=BillsItem',
                'ctrl' => 'BillsItem',
                'target' => '_self',
            ),
        ),
    ),
    'ϵͳ����' => array(
        'icon' => 'user-md',
        'items' => array(
            '����Ա' => array(
                'url' => '?c=AdminUser',
                'ctrl' => 'AdminUser'
            ),
            '���ù���' => array(
                'url' => '?c=Option',
                'ctrl' => 'Option'
            ),
        ),
    ),
);
