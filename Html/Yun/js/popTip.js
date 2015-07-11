var ETIPS = {
    'businesslicensepic':'<img src="http://icon.zol-img.com.cn/star/image/yingyezhizhao.png" width="150" height="100" /><br/>Ҫ��<br/>1��ͼƬ�ߴ����800x600px<br/>2���뱣֤֤����Ч������',
    'accountlevel':'�˺ŵȼ�Խ�����ܵķ����Խ�࣬<a href="#">�˽�����</a>',
    'mofangattention':'�û���ע�ȣ�<br />ָ�������û�����������ӳ�����û���ZOL��վ�ķ������ƣ�����ͬ���û�����ʵ��������',
    'mofangexposure':'ý���ع⣺<br />ָ������ý����Ѷ�û�����������ӳ����ZOL�Ƽ�ý�巢����Ѷ�����⡢���������������ʱ�ķ������ƣ�����ͬ���û���ý����ʵ��������',
    'mofangusersearch':'�û�����ָ����<br />ָ�������û�����������ӳ���ǰٶȰ�����������û���վ�������������ۺϷ������ƣ��������û�����ʵ��������',
    'mofangreview':'�����û����ۣ�<br />ĳʱ�����վ�û��Ը�Ʒ���²�Ʒ�����в�Ʒ����ĳ���Ʒ���ۺ����֣���ӳ���ǽ���ʱ���û���Ʒ��������߲�Ʒ�Ͽɵ����ơ�'
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
                $('.poptip').html('<span class="poptip-arrow"><em>��</em><i>��</i></span><span class="poptip-content" style="' + style + '">' + data + '</span>');
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
                //42Ϊ���
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
//С��ʾ���
$('.help-tip').each(function(){$(this).popTip();});