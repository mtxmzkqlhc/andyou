/**
 * select选择的级联
 */
;(function($){    
    	$.fn.rselect = function(options) {
            
            //默认配置
            var defaults = {				
                url:        ''  //请求的数据URL
                ,topTxt:    '请选择' //默认的文本
                ,topVal:    '0' //默认的值
                ,initVal:   '0' //初始值
                ,relSel:    false //关联的选择框
                ,relVal:    false //关联的选择框的选择值
                ,randStr:   false //请求是否添加随机字符，避免ajax缓存
                ,callBack:  function(){}
            };
            var options = $.extend(defaults, options);
        
            var nowObj = $(this);
            
            if(options.relSel){//是否级联上一级菜单
                $(options.relSel).change(function(){
                    options.relVal = $(this).val();
                    getData(nowObj,options);
                });
                
            }
            getData(nowObj,options);
            
            
            
    		return this;
    	}
        /**
         * 详细的获得数据
         */
        var getData = function(obj,options){            
            
            var url = options.url;
            if(options.relVal){
                url += '&val=' + options.relVal;
            }
            if(options.randStr){
                url += '&r=' + Math.random();
            }
            
            $.get(url,function(html){    
                //替换初始化值
                if(options.initVal){
                    html.replace("value='"+options.initVal+"'", "selected='selected' value='"+options.initVal+"'");
                }
                
                obj.html("<option value='" + options.topVal + "'>" + options.topTxt + "</option>" + html);
                obj.val(options.initVal);                
            });            
        }
})( jQuery );