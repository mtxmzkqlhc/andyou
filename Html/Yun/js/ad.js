/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){

    /*���º������ڲ��*/
    $.datepicker.regional['zh-CN'] =
    {
        clearText: '���', 
        clearStatus: '�����ѡ����',
        closeText: '�ر�', 
        closeStatus: '���ı䵱ǰѡ��',
        prevText: '&lt;����', 
        prevStatus: '��ʾ����',
        nextText: '����&gt;', 
        nextStatus: '��ʾ����',
        currentText: '����', 
        currentStatus: '��ʾ����',
        monthNames: ['һ��', '����', '����', '����', '����', '����',
        '����', '����', '����', 'ʮ��', 'ʮһ��', 'ʮ����'],
        monthNamesShort: ['һ', '��', '��', '��', '��', '��',
        '��', '��', '��', 'ʮ', 'ʮһ', 'ʮ��'],
        monthStatus: 'ѡ���·�', 
        yearStatus: 'ѡ�����',
        weekHeader: '��', 
        weekStatus: '�����ܴ�',
        dayNames: ['������', '����һ', '���ڶ�', '������', '������', '������', '������'],
        dayNamesShort: ['����', '��һ', '�ܶ�', '����', '����', '����', '����'],
        dayNamesMin: ['��', 'һ', '��', '��', '��', '��', '��'],
        dayStatus: '���� DD Ϊһ����ʼ', 
        dateStatus: 'ѡ�� m�� d��, DD',
        dateFormat: 'yy-mm-dd', 
        firstDay: 1,
        initStatus: '��ѡ������', 
        isRTL: false
    };
    $.datepicker.setDefaults($.datepicker.regional['zh-CN']);
    /*��ɺ������ڲ��*/
    
    /*���ڿ���*/
    $(".datePlugin").datepicker({
        dateFormat: "yy-mm-dd"
    });
    
    /*ȫҳ�湫��btn*/
    $('.btn-close').click(function(e){
        e.preventDefault();
        $(this).parent().parent().parent().fadeOut();
    });
    $('.btn-minimize').click(function(e){
        e.preventDefault();
        var $target = $(this).parent().parent().next('.box-content');
        if($target.is(':visible')) $('i',$(this)).removeClass('chevron-up').addClass('chevron-down');
        else 					   $('i',$(this)).removeClass('chevron-down').addClass('chevron-up');
        $target.slideToggle();
    });
});