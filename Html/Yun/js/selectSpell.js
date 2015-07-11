/**
 * ������ĸselect
 * code wangmc
 * date 20140512
 ***/



;(function($){
        $.fn.selectSpell  = function(option){
              var  defaults  = {
                  callback:function(){}   
              }
              var  options  = $.extend(defaults,option);
              var _this     = $(this);
              var cache     = {};
              var sel       = 0;
              $(document).keyup(function(event){ 
                 //��ȡ��ǰ�����ļ�ֵ 
                 //jQuery��event��������һ��which�����Կ��Ի�ü��̰����ļ�ֵ 
                 var keycode = event.which; 
                 //����س������ 
                 if(keycode>=65 && keycode<=90){
                              var spell  = String.fromCharCode(keycode);
                              _this.find('option').each(function(){
                                    var that  = $(this);
                                    that.attr('selected',false); 
                                    if(typeof cache[spell]!='undefined'){
                                             cache[spell].attr('selected',true);
                                             options.callback(that,spell);
                                             return true;
                                    }  
                                    var con   = that.html();
                                    var Reg    = new RegExp("^"+spell);  
                                    if(Reg.test(con) && typeof cache[spell]=='undefined'){
                                        that.attr('selected',true); 
                                        options.callback(that,spell);
                                        cache[spell] = that;
                                    }
                            }); 
                 }
             });  
             
             
             
        }
    
    
    
})(jQuery);