/**
 * �˵��л����
 * code  wangmc
 *
 **/
$.fn.changeTab  = function(option){
    var defaults  = {
        'callback':function(){},
        'prefix'  :'_ul',
        'actClass':'current'
    }
    var that = $(this);
    var options     = $.extend(defaults,option);
    //��ǰul id����
    var thisId      = that.attr('id');
    //��ǰid ����
    var curId       = 0;
    //���ݱ仯ul����
    var conClass    = '.'+thisId+options.prefix;
    var ulObj       = $(conClass);
    //��tab ���¼�
    that.find('li').bind('click',function(){
               var _that = $(this);
               _that.siblings('li').removeClass(options.actClass);
               _that.addClass(options.actClass);
               curId  = _that.index();
               ulObj.siblings(conClass).hide();
               ulObj.eq(curId).show();
               //�ص�����
               options.callback(_that,that,curId);
    });

}