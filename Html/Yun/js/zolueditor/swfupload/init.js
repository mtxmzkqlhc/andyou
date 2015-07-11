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
		//�ϴ��ļ�����
		file_post_name : 'Filedata',//��������ύ���ļ���������
		flash_url : "http://article.zol.com.cn/admin/zolueditor/swfupload/swfupload.swf",//swfupload·��
		upload_url : "http://image.zol.com.cn/article_bdu_upload.php",//�����ϴ��ļ���URL
		post_params : {"document_id":$CONFIG['document_id'],"class_id":$CONFIG['class_id']},
		file_size_limit : "12288",//�����ϴ������ļ��Ĵ�С����λ��M��
		file_types : "*.jpg;*.jpeg;*.gif;*.png;",//�����ϴ��ļ�������
		file_types_description : "ͼƬ",
		file_upload_limit : 0,
		file_queue_limit : 100,//�����ϴ��ļ��ĸ���
		custom_settings : {
			progressTarget : "swfUploadProgress",
			cancelButtonId : "btnCancel"
		},
		debug : false,
		//ѡ���ļ���ť����
		button_image_url : "",
		button_width : 75,
		button_height : 28,
		button_placeholder_id : "spanButtonPlaceHolder",
		button_text_style : "",
		button_text_top_padding : 3,
		button_text_left_padding : 12,
		button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_cursor : SWFUpload.CURSOR.HAND,
		//�¼���������
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_complete_handler : uploadComplete,
		queue_complete_handler : queueComplete,	//�����ļ��ϴ���ɺ�ˢ�±���ͼ
		upload_success_handler : uploadSuccess
	};
	swfu = new SWFUpload(settings);
 };