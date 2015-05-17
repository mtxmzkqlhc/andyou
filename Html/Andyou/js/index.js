//菜单选中效果
 
var str_url=window.location.search;
var parsed = parseURL(str_url);
var matchUrl = "?c="+ parsed.params.c;
//内网后台用户管理单独处理 
if(parsed.params.uc){
    matchUrl+="&uc="+parsed.params.uc;
}
//广告位链接处理
if(parsed.params.type){
    matchUrl+="&type="+parsed.params.type;
}
var currentSelect = $('ul.sub-menu').find("a[href='/"+matchUrl+"']");
//解析 url 
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

//系统界面相关初始化
$(document).ready(function(){
    /* ---------- Datable ---------- */
    $('.datatable').dataTable({
        "bFilter":false,
        "bPaginate": false,
        "bLengthChange": false,
        "bInfo": false,
        "bSort": false,
        "oLanguage": {
            "sEmptyTable":"暂无数据"
        }
    //"aaSorting": [[ 0, "desc" ]]

    } );
    
    /*以下汉化日期插件*/
    $.datepicker.regional['zh-CN'] =
    {
        clearText: '清除', 
        clearStatus: '清除已选日期',
        closeText: '关闭', 
        closeStatus: '不改变当前选择',
        prevText: '&lt;上月', 
        prevStatus: '显示上月',
        nextText: '下月&gt;', 
        nextStatus: '显示下月',
        currentText: '今天', 
        currentStatus: '显示本月',
        monthNames: ['一月', '二月', '三月', '四月', '五月', '六月',
        '七月', '八月', '九月', '十月', '十一月', '十二月'],
        monthNamesShort: ['一', '二', '三', '四', '五', '六',
        '七', '八', '九', '十', '十一', '十二'],
        monthStatus: '选择月份', 
        yearStatus: '选择年份',
        weekHeader: '周', 
        weekStatus: '年内周次',
        dayNames: ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
        dayNamesShort: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
        dayNamesMin: ['日', '一', '二', '三', '四', '五', '六'],
        dayStatus: '设置 DD 为一周起始', 
        dateStatus: '选择 m月 d日, DD',
        dateFormat: 'yy-mm-dd', 
        firstDay: 1,
        initStatus: '请选择日期', 
        isRTL: false
    };
    $.datepicker.setDefaults($.datepicker.regional['zh-CN']);
    /*完成汉化日期插件*/
    
    /*全页面公共btn*/
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
/*生成程序专用*/
/*--- 获取数据详细信息 ---*/
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
/*--- ajax更新数据 ---*/
function updata(id,c,ac,pamStr){ 
    var r = new Date().getTime();
    $.ajax({ 
        type:"POST",
        url:"?c="+c+"&a="+ac+"&dataid="+id+pamStr+"&rnd="+r
    });
}
//可更改列
$(".editColumn").die().live('dblclick',function(e){
   if(e.button == 2) {return false;}
   var columnVal = $(this).html();
   var name = $(this).attr('data');
   $(this).html('').removeClass('editColumn');
   $(this).append('<input type="text" name="'+name+'" class="tempEdit" value="'+columnVal+'" / style="width:80px;">');
});
$('#addbtn').live('click',function(){
    $(this).html('添加中...');
    $('#addform').submit();
});
$('#savebtn').live('click',function(){
    $(this).html('保存中...');
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
    $(this).html('删除中...');
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
        //获得当前的input的tabindex
        var idx = $(this).attr("data-tab-index");
        idx++;

        //找到下一个输入
        $(".entrnext").each(function(){
            if($(this).attr("data-tab-index") == idx){
                $(this).focus();
                return true;
            }
        });
        return false;
    }
});



