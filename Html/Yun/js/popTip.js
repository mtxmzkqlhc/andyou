var ETIPS = {
    'businesslicensepic':'<img src="http://icon.zol-img.com.cn/star/image/yingyezhizhao.png" width="150" height="100" /><br/>要求：<br/>1、图片尺寸大于800x600px<br/>2、请保证证件有效，清晰',
    'accountlevel':'账号等级越高享受的服务就越多，<a href="#">了解详情</a>',
    'mofangattention':'用户关注度：<br />指数化的用户访问量，反映的是用户在ZOL网站的访问趋势，不等同于用户的真实访问量。',
    'mofangexposure':'媒体曝光：<br />指数化的媒体资讯用户访问量，反映的是ZOL科技媒体发布资讯、评测、导购、行情等内容时的访问趋势，不等同于用户的媒体真实访问量。',
    'mofangusersearch':'用户搜索指数：<br />指数化的用户搜索量，反映的是百度阿拉丁导入和用户在站内主动搜索的综合访问趋势，不等于用户的真实搜索量。',
    'mofangreview':'整体用户评价：<br />某时间段网站用户对该品牌下产品线所有产品或者某款产品的综合评分，反映的是近段时间用户对品牌形象或者产品认可的趋势。'
};
(function($) {
	$.fn.popTip = function(options){
        var __self  = $(this);
        var set_help_time,set_help_htime;
        __self.live('mouseenter',function(){
            clearTimeout(set_help_htime);
            clearTimeout(set_help_time);
            var direction    = __self.attr('help-direction');
            if (!direction)  direction = 'left';
            var width        = __self.width();
            var height       = __self.height();
            var data         = __self.attr('help-data');
            var pos          = getPos(__self[0]);
            var posleft      = pos[0];
            var postop       = pos[1];
            var contentWidth = __self.attr('help-width');
            var style = "";
            if (typeof(contentWidth)!='undefined') style = 'line-height:22px;width:' + contentWidth + 'px;display:block;';
            if (typeof(ETIPS[data])!='undefined') data = ETIPS[data];
            if (!data) return false;
            set_help_htime   = setTimeout(function(){
                if($('.poptip').html() == null) $('body').append('<div class="poptip"></div>');
                $('.poptip').html('<span class="poptip-arrow"><em>◆</em><i>◆</i></span><span class="poptip-content" style="' + style + '">' + data + '</span>');
                if(direction == 'topright') {
                    postop = postop + height + 7;
                    posleft = posleft - $('.poptip').outerWidth() + 20;
                }
                if(direction == 'top') {
                    postop = postop + height + 6;
                    posleft = posleft - 6;
                }
                if(direction == 'bottom') {
                    postop = postop - $('.poptip').outerHeight() - 6;
                    posleft = posleft - 6;
                }
                //42为误差
                if(direction == 'left' && (posleft - $(window).scrollLeft() + $('.poptip-content').outerWidth() + 42) > $(window).width()){
                    direction = 'right';
                }
                if(direction == 'left') {
                    posleft = posleft + width + 7;
                    postop = postop - 6;
                }
                if(direction == 'right') {
                    posleft = posleft - $('.poptip').outerWidth() - 6;
                    postop = postop - 6;
                }
                $('.poptip .poptip-arrow').addClass('poptip-arrow-'+direction);
                $('.poptip').show().css('left',posleft).css('top',postop);
            },300)
        }).live('mouseleave',function(){
            clearTimeout(set_help_htime);
            set_help_time = setTimeout(function(){$('.poptip').hide();},300);
        });
        $('.poptip').live('mouseenter',function(){
            clearTimeout(set_help_time);
        }).live('mouseleave',function(){
            set_help_time = setTimeout(function(){
                $('.poptip').hide();
            },300);
        });
        function getPos(obj) {
            var pos = [];
            pos[0] = obj.offsetLeft;
            pos[1] = obj.offsetTop;
            while(obj = obj.offsetParent) {
                pos[0] += obj.offsetLeft;
                pos[1] += obj.offsetTop;
            }
            return pos;
        }
	};
})(jQuery);
//小提示插件
$('.help-tip').each(function(){$(this).popTip();});