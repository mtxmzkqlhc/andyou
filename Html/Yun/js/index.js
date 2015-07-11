//�˵�ѡ��Ч��
 
var str_url=window.location.search;
var parsed = parseURL(str_url);
var matchUrl = "?c="+ parsed.params.c;
//������̨�û����������� 
if(parsed.params.uc){
    matchUrl+="&uc="+parsed.params.uc;
}
//���λ���Ӵ���
if(parsed.params.type){
    matchUrl+="&type="+parsed.params.type;
}
var currentSelect = $('ul.sub-menu').find("a[href='/"+matchUrl+"']");
//���� url 
function parseURL(url) {
 var a =  document.createElement('a');
 a.href = url;
 return {
 source: url,
 protocol: a.protocol.replace(':',''),
 host: a.hostname,
 port: a.port,
 query: a.search,
 params: (function(){
     var ret = {},
         seg = a.search.replace(/^\?/,'').split('&'),
         len = seg.length, i = 0, s;
     for (;i<len;i++) {
         if (!seg[i]) { continue; }
         s = seg[i].split('=');
         ret[s[0]] = s[1];
     }
     return ret;
 })(),
 file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
 hash: a.hash.replace('#',''),
 path: a.pathname.replace(/^([^\/])/,'/$1'),
 relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [,''])[1],
 segments: a.pathname.replace(/^\//,'').split('/')
 };
}


if(typeof currentSelect!=undefined){
    currentSelect.parent().removeClass().addClass('active');
    $('.sel').removeClass('active').find('.arrow').removeClass('open');
    currentSelect.parent().parent().parent().removeClass().addClass('active').find('.arrow').addClass('open');
}

//ϵͳ������س�ʼ��
$(document).ready(function(){
    /* ---------- Datable ---------- */
    $('.datatable').dataTable({
        "bFilter":false,
        "bPaginate": false,
        "bLengthChange": false,
        "bInfo": false,
        "bSort": false,
        "oLanguage": {
            "sEmptyTable":"��������"
        }
    //"aaSorting": [[ 0, "desc" ]]

    } );
    
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
/*���ɳ���ר��*/
/*--- ��ȡ������ϸ��Ϣ ---*/
function getdatainfo(id,c,fun){ 
    var r = new Date().getTime();
    $.ajax({ 
        type:"GET",
        dataType:"JSON",
        url:"?c="+c+"&a=AjaxData&id="+id+"&rnd="+r
    }).done(function(dat){
        if(dat!==null){
            fun(dat);
        }else{
            fun(null);
        }
        $('#dataid').val(id);
        $('#edit-box').modal('show');
    });
}
/*--- ajax�������� ---*/
function updata(id,c,ac,pamStr){ 
    var r = new Date().getTime();
    $.ajax({ 
        type:"POST",
        url:"?c="+c+"&a="+ac+"&dataid="+id+pamStr+"&rnd="+r
    });
}
//�ɸ�����
$(".editColumn").die().live('dblclick',function(e){
   if(e.button == 2) {return false;}
   var columnVal = $(this).html();
   var name = $(this).attr('data');
   $(this).html('').removeClass('editColumn');
   $(this).append('<input type="text" name="'+name+'" class="tempEdit" value="'+columnVal+'" / style="width:80px;">');
});
$('#addbtn').live('click',function(){
    $(this).html('�����...');
    $('#addform').submit();
});
$('#savebtn').live('click',function(){
    $(this).html('������...');
    $('#editform').submit();
});
$('.delbtn').live('click',function(e){
    e.preventDefault();
    var id = $(this).parent().attr('rel');
    if(id){ 
        $('#deldataid').val(id);
        $('#del-box').modal('show'); 
    }
});
$('#delbtn').live('click',function(){
    $(this).html('ɾ����...');
    $('#delform').submit();
});

if($.browser.msie){
    $('[placeholder]').focus(function() {
        var input = $(this);
        if (input.val() == input.attr('placeholder')) {
            input.val('');
        }
    }).blur(function() {
        var input = $(this);
        if (input.val() == '' || input.val() == input.attr('placeholder')) {
            input.val(input.attr('placeholder'));
        }
    }).blur();
}
$('.btn-ser').click(function(){
    var inputid = $('#serform [placeholder]');
    inputid.each(function(){
        if ($(this).val() == $(this).attr('placeholder')) {
            $(this).val('');
         }
    })
   
 $('#serform').submit();
});

$(".entrnext").keydown(function(e){
    if(e.which == 13){
        e.preventDefault();
        //��õ�ǰ��input��tabindex
        var idx = $(this).attr("data-tab-index");
        idx++;

        //�ҵ���һ������
        $(".entrnext").each(function(){
            if($(this).attr("data-tab-index") == idx){
                $(this).focus();
                return true;
            }
        });
        return false;
    }
});



