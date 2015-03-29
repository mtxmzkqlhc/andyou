/* 
 * author zhangxc
 * desc ： 根据页面元素 属性值 自动绑定事件
 */


!function($){
	var Autobind = function(elemment,options){
		this.$elemment = $(elemment);
		this.options   = $.extend({},$.fn.autobind.defaults,options);
	}
	//将方法定义在prototype原型上
	Autobind.prototype = {
		init : function (){
			var bindEvent = $(this.$elemment).attr(this.options.bindEvent) ; 
			var bindFunc = $(this.$elemment).attr(this.options.bindFunc);
			var elemParam =  $(this.$elemment).attr(this.options.elemParam);
			//默认为click 事件
			if(!bindEvent){
				bindEvent = this.options.defaultEvent;
			}
			var paramSplit = bindFunc.split("."); //  使用 - 分割 而不是 使用 . 避免某些情况下和jquery冲突
			//获取执行函数
			var func = window;
			var eventNameSpace = '';
			for(var index = 0 ; index < paramSplit.length ;index++){
				var t = typeof (func);
                                
				if('function' === t || 'object' === t){
					func = func[paramSplit[index]];
					eventNameSpace  = eventNameSpace+'.'+paramSplit[index];
				}
			}
			if('function' === typeof (func)){
				//是否将绑定的元素传给所绑定的函数
				var giveElemParam = true;
				if(this.options.domDefaultParam){
					if(0 == parseInt(elemParam)){
						giveElemParam = false;
					}
				}else{
					giveElemParam = false;
					if(1 == parseInt(elemParam)){
						giveElemParam = true;
					}
				}
				
				var that = this.$elemment;
				//绑定-事件-函数
				$(this.$elemment).on(bindEvent+'.'+eventNameSpace ,function(e){
					if(giveElemParam){
						func(that);
					}else{
						func();
					}
                                });
			}else {
					if(this.options.debug){
						this.debug(bindFunc);
					}
					 
				}

		},
		//调试
		debug : function(funcName){
			console.log('sorry,function:'+funcName+' not found');
		},
		
	}

	var old = $.fn.autobind;

	$.fn.autobind = function(option){
		return this.each(function () {
	      var $this = $(this)
	        , data = $this.data('autobind')
	        , options = typeof option == 'object' && option
	      if (!data) $this.data('autobind', (data = new Autobind(this, options)))
	      	if (typeof option == 'string') data[option]() 
	      	else data.init()
	    })
	}
	//可通过传参数改变
	$.fn.autobind.defaults = {
		domDefaultParam : true,     //是否默认 将对应元素dom对象传递给所绑定的函数
		    			            //这样数据可以存放在dom 上，函数调用的时候可以获取,默认都开启
		defaultEvent    :'click',   //默认绑定的事件 click
		debug           : true,     // 是否开启调试，若未找到所需绑定的函数将 console.log 信息 
                                    //这三个值 ,仅当以下属性名与其他插件 冲突时修改
		bindFunc : 'bind-func',     //默认的函数值属性
		bindEvent  : 'bind-event',  //默认的事件值属性
		elemParam  : 'data-param'  //是否将元素传给函数   如果带这个属性
                                    //用于单独控制  1 传  0 不传   和 elemDaultParam 分开，
                                    //即，无论elemDefaultParam怎么设置，这个值都有效							
	}

    $.fn.autobind.noConflict = function () {
      $.fn.autobind = old
      return this
    }

    $.fn.autobind.constructor = Autobind;
}(window.jQuery)

$(document).ready(function(){
	$('[bind-func]').autobind();
 });


