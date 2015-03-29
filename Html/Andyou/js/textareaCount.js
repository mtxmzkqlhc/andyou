/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($) {

	$.fn.textareaCount = function(options) {
		
		var defaults = {
			maxlength: 120,													//限制的字数
			rows: 0,														//获得焦点增加的行数
			lineHeight: 18,													//行高
			isReset: false,													//失去焦点是否需要重置高度
			style: 'float:right; margin:-23px 5px 0 0; height:18px;',		//设置字数显示的位置,视情况而定，可以是定位可以是浮动
			font: '14px/18px Georgia,Tahoma,Arial;',						//字数的字体
			color: '#999;'													//字数的颜色
		}
		
		var self = $(this)
		var settings = $.extend({}, defaults, options)
		//创建输入框环境
		var creatTextareaCount = function(){
			var countHtml = '<var style="display:none;'
				+ settings.style 
				+ 'font:' + settings.font 
				+ 'color:' + settings.color
				+ '">0/' + settings.maxlength + '</var>'
			var textareaWrap = '<div style="position:relative;zoom:1"></div>'
			self.wrap(textareaWrap).after(countHtml)
		}
		//计算字数
		var wordsCount = function(){
			var value = self.val()
            var valueArr = value.split("");
			var len = value.length
			var count = 0
			var numContainer = self.parent().find('var')
			numContainer.show()
			//再输入或者粘贴的时候记录值
			var tempValue = ''
			for (var i = 0; i < len; i++){
				//GBK编码下面，汉字是两个字节
				if(value.charCodeAt(i) < 0 || value.charCodeAt(i) > 255){
					count += 2
				} else {
					count += 1
				}
				if(count <= settings.maxlength*2){
					tempValue += valueArr[i]
				}
			}
			var wordsCount = settings.maxlength - Math.floor((settings.maxlength*2 - count) / 2)
			if(wordsCount <= settings.maxlength){
				numContainer.text(wordsCount + '/' + settings.maxlength)
			} else {
				self.val(tempValue)
			}
		}
		
		return this.each(function(){	
			creatTextareaCount()
			var defaultHeight = self.height()
			var defaultValue = self.val()
			var paddingBottom = parseInt(self.css('padding-bottom'))
			self.on({
				'focus': function(){
					if(!($.trim(self.val()) == defaultValue)){return}
					self.css({
						'height': defaultHeight + (settings.lineHeight * settings.rows),
						'padding-bottom': paddingBottom + settings.lineHeight
					})
				},
				'blur': function(){
					self.parent().find('var').hide()
					if(!($.trim(self.val()) == defaultValue)){return}
					self.val(defaultValue)
					if(settings.isReset){
						self.css({'height': defaultHeight, 'padding-bottom':paddingBottom})
					}
				}
			})
			self.on('keyup change input focus', wordsCount)
		})
	}
})(jQuery);