/**
 * 菜单切换插件
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
    //当前ul id名称
    var thisId      = that.attr('id');
    //当前id 索引
    var curId       = 0;
    //内容变化ul对象
    var conClass    = '.'+thisId+options.prefix;
    var ulObj       = $(conClass);
    //给tab 绑定事件
    that.find('li').bind('click',function(){
               var _that = $(this);
               _that.siblings('li').removeClass(options.actClass);
               _that.addClass(options.actClass);
               curId  = _that.index();
               ulObj.siblings(conClass).hide();
               ulObj.eq(curId).show();
               //回调函数
               options.callback(_that,that,curId);
    });

}