<?php

/**
 * ��̨�˵�����
 * ͼ��ͼƬѡ����Բ��� http://clabs.co/projects/metro/icon.html
 */
return array(
    '��Ա����' => array(
        'icon' => 'group',
        'items' => array(
            '��Ա�б�' => array(
                'url' => '?c=Member&a=Default',
                'ctrl' => 'Member',
                'target' => '_self',
            ),
            'Ʒ�Ƶȼ�' => array(
                'url' => '?c=User_GrowScore',
                'ctrl' => 'User_GrowScore',
                'target' => '_self',
            ),
        ),
    ),
    'Ȩ�޹���' => array(
        'icon' => 'group',
        'items' => array(
            '�û�Ȩ������' => array(
                'url' => '/?c=Permission_UserRole&uc=user',
                'ctrl' => 'Permission_UserRole'
            ),
            '��ɫ����' => array(
                'url' => '/?c=Permission_Role&uc=user',
                'ctrl' => 'Permission_Role'
            ),
            'Ȩ����Դ����' => array(
                'url' => '/?c=Permission_Option&uc=user',
                'ctrl' => 'Permission_Option'
            ),
        ),
    ),
    '�������' => array(
        'icon' => 'inbox',
        'items' => array(
            '�û����' => array(
                'url' => '/?c=Pay_Bill',
                'ctrl' => 'Pay_Bill',
                'target' => '_blank',
            ),
            '��ֵ��¼' => array(
                'url' => '/?c=Pay_Pay',
                'ctrl' => 'Pay_Pay',
                'target' => '_blank',
            ),
            '���Ѽ�¼' => array(
                'url' => '/?c=Pay_Consume',
                'ctrl' => 'Pay_Consume',
                'target' => '_blank',
            ),
            '��Ʊ����' => array(
                'url' => '/?c=Pay_Invoice',
                'ctrl' => 'Pay_Invoice',
                'target' => '_blank',
            ),
            '�����ֵ' => array(
                'url' => '/?c=Pay_Finance',
                'target' => '_blank',
            ),
            '��Ʒ����' => array(
                'url' => '/?c=Pay_trophyInfo',
                'target' => '_blank',
            ),
            '��Ʒ�һ���¼' => array(
                'url' => '/?c=Pay_exchangeTrophyLog',
                'target' => '_blank',
            ),
        ),
    ),
);
