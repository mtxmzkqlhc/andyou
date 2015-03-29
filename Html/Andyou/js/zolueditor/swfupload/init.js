/*
 * @description : swfupload init
 * @copyright   : http://www.zol.com.cn/
 * @author      : xuzongsheng
 * @version     : 1.0 (2013-04-09 10:09:35)
 * @modifier    : xuzongsheng
 */
var swfu;
window.onload = function() {
	var settings = {
		//上传文件配置
		file_post_name : 'Filedata',//向服务器提交的文件属性名称
		flash_url : "http://article.zol.com.cn/admin/zolueditor/swfupload/swfupload.swf",//swfupload路径
		upload_url : "http://image.zol.com.cn/article_bdu_upload.php",//处理上传文件的URL
		post_params : {"document_id":$CONFIG['document_id'],"class_id":$CONFIG['class_id']},
		file_size_limit : "12288",//限制上传单个文件的大小，单位（M）
		file_types : "*.jpg;*.jpeg;*.gif;*.png;",//限制上传文件的类型
		file_types_description : "图片",
		file_upload_limit : 0,
		file_queue_limit : 100,//限制上传文件的个数
		custom_settings : {
			progressTarget : "swfUploadProgress",
			cancelButtonId : "btnCancel"
		},
		debug : false,
		//选择文件按钮配置
		button_image_url : "",
		button_width : 75,
		button_height : 28,
		button_placeholder_id : "spanButtonPlaceHolder",
		button_text_style : "",
		button_text_top_padding : 3,
		button_text_left_padding : 12,
		button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_cursor : SWFUpload.CURSOR.HAND,
		//事件函数调用
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_complete_handler : uploadComplete,
		queue_complete_handler : queueComplete,	//队列文件上传完成后刷新本文图
		upload_success_handler : uploadSuccess
	};
	swfu = new SWFUpload(settings);
 };