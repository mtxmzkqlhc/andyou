//���ļ��Ǳ༭�������ļ��������������ɶ�Ӧ������ӿ��ĵ�
/**
 * @file
 * @name �༭������ӿ�
 * @short Commands
 * @desc
 *
 * UEditor��ִ�������ͳһ���ø�ʽΪ
 * <code>editor.execCommand("cmdName"[,opt]);</code>
 *
 *
 * ��⵱ǰ�����Ƿ���õķ�����
 * <code>editor.queryCommandState("cmdName");</code>
 *
 *
 * ����������Է�������ֵ�����ʽΪ
 * <code>editor.queryCommandValue("cmdName");</code>
 */
/**
 * ����ê��
 * @name anchor
 * @grammar editor.execCommand("anchor","name"); //ê�������
 */
/**
 * Ϊ��ǰѡ��������Ӵ���Ч��
 * @name bold
 * @grammar editor.execCommand("bold");
 */
/**
 * Ϊ��ǰѡ���������б��Ч��
 * @name italic
 * @grammar editor.execCommand("italic");
 */
/**
 * Ϊ��ǰѡ����������»���Ч��
 * @name underline
 * @grammar editor.execCommand("underline");
 */


/**
 * Ϊ��ǰѡ���������ɾ����Ч��
 * @name strikethrough
 * @grammar editor.execCommand("strikethrough");
 */
/**
 * ����ǰѡ������ת�����ϱ�
 * @name superscript
 * @grammar editor.execCommand("superscript");
 */
/**
 * ����ǰѡ������ת�����±�
 * @name subscript
 * @grammar editor.execCommand("subscript");
 */
/**
 * Ϊ��ǰѡ�����������ɫ
 * @name foreColor
 * @grammar editor.execCommand("foreColor","#ffffff");
 */
/**
 * Ϊ��ǰѡ��������ӱ�����ɫ
 * @name backColor
 * @grammar editor.execCommand("backColor","#dddddd");
 */
/**
 * ���õ�ǰѡ�����ֵ�����
 * @name fontFamily
 * @grammar editor.execCommand("fontFamily","΢���ź�,Microsoft YaHei");
 */
/**
 * ���õ�ǰѡ�����ֵ��ֺ�
 * @name fontSize
 * @grammar editor.execCommand("fontSize","32px");
 */
/**
 * ���õ�ǰѡ���Ķ����ʽ,��p,h1,h2,h3,...
 * @name paragraph
 * @grammar editor.execCommand("paragraph","h1");
 */
/**
 * ����ǰѡ���任��������������б�
 * @name insert(Un)OrderedList
 * @grammar editor.execCommand("insertOrderedList");
 */
/**
 * ���õ�ǰѡ�����м��
 * @name lineHeight
 * @grammar editor.execCommand("lineHeight");
 */
/**
 * ���õ�ǰѡ���е�������뷽ʽ
 * @name justify
 * @grammar editor.execCommand("justify",align);  //align��ΪLeft��Right��Center��Justify
 */
/**
 * ����ǰѡ�������е���ĸת���ɴ�д
 * @name toUppercase
 * @grammar editor.execCommand("toUppercase");
 */
/**
 * ����ǰѡ�������е���ĸת����Сд
 * @name toLowercase
 * @grammar editor.execCommand("toLowercase");
 */
/**
 * Ϊ��ǰѡ�����ڵĿ鼶Ԫ��������ñ��
 * @name blockquote
 * @grammar editor.execCommand("blockquote");
 */
/**
 * ���õ�ǰѡ�����ڿ鼶Ԫ�ص��������뷽��
 * @name directionality
 * @grammar editor.execCommand("directionality",dir);  //dir��ΪLTR,RTL
 */
/**
 * �����ǰѡ�������ϵ�������ʽ����ָ����ʽ
 * @name removeFormat
 * @grammar editor.execCommand("removeFormat")   //����editor_config.js���removeFormatTags��removeFormatAttributes����������Ϊ����
 * @grammar editor.execCommand("removeFormat",tags,style);   //���ָ��tags�ϵ�ָ��style
 * @example
 * editor.execCommand("removeFormat",'span,a','color,background-color')
 */
/**
 * �л����ı�ճ��ģʽ
 * @name pastePlain
 * @grammar ue.execCommand("pastePlain");
 */
/**
 * ������ʽˢ����
 * @name formatMatch
 * @grammar editor.execCommand("formatMatch");
 */
/**
 * ����ĵ�
 * @name clearDoc
 * @grammar editor.execCommand("clearDoc");
 */
/**
 * ɾ����ǰѡ���ı�
 * @name delete
 * @grammar editor.execCommand("delete");
 */
/**
 * ȫ��ѡ��
 * @name selectAll
 * @grammar editor.execCommand("selectAll");
 */
/**
 * ��������
 * @name undo
 * @grammar editor.execCommand("undo");
 */
/**
 * �ָ�����
 * @name redo
 * @grammar editor.execCommand("redo");
 */
/**
 * �������༭�ĵ������Զ��Ű�
 * @name autoTypeset
 * @grammar editor.execCommand("autoTypeset");
 */
/**
 * �ڵ�ǰѡ��λ�ò���һ��html���룬��������ܡ��󲿷����������������ô�����������Ĳ���
 * @name insertHtml
 * @grammar editor.execCommand("insertHtml","��ӭʹ��UEditor��")
 */
/**
 * �ڵ�ǰѡ��λ�ò���һ��������
 * @name link
 * @grammar editor.execCommand("link",linkObj);
 * @example
 * editor.execCommand("link",{
 *     href: "http://ueditor.baidu.com",         //������ַ����ѡ
 *     data_ue_src: "http://ueditor.baidu.com",  //UE�ڲ�ʹ�ò�������href����һ�¼��ɣ���ѡ
 *     target: "_self",                          //Ŀ�괰�ڣ���ѡ
 *     textValue: "UEditor",                     //������ʾ�ı�����ѡ
 *     title: "�ٶȿ�Դ���ı��༭��UEditor����"     //���⣬��ѡ
 * })
 */
/**
 * �ڵ�ǰѡ��λ�ò���һ��ͼƬ
 * @name insertImage
 * @grammar editor.execCommand("insertImage",imageObj);
 * @example
 * editor.execCommand("insertImage",{
 *     src: "http://ueditor.baidu.com/logo.jpg",          //ͼƬ���ӵ�ַ,��ѡ
 *     data_ue_src: "http://ueditor.baidu.com/logo.jpg",  //UE�ڲ�ʹ�ò�������src����һ�¼��ɣ���ѡ
 *     width: 300,                                        //ͼƬ��ʾ��ȣ���ѡ
 *     height: 400,                                       //ͼƬ��ʾ�߶ȣ���ѡ
 *     border: 2,                                         //ͼƬ�߿򣬿�ѡ
 *     hspace: 5,                                         //ͼƬ���ұ߾࣬��ѡ
 *     vspace: 2,                                         //ͼƬ���±߾࣬��ѡ
 *     alt: 'UEditor-logo',                               //ͼƬ�滻���֣���ѡ
 *     title: "�ٶȿ�Դ���ı��༭��UEditor����"             //ͼƬ���⣬��ѡ
 * })
 */
/**
 * �ڵ�ǰѡ��λ�ò���һ����Ƶ
 * @name insertVideo
 * @grammar editor.execCommand("insertVideo",videoObj);
 * @example
 * editor.execCommand("insertVideo",{
 *     url: "http://youku.com/id?id=1233122",   //��Ƶ��ַ����ѡ
 *     width: 420,                              //��Ƶ��ȣ���ѡ
 *     height: 280,                             //��Ƶ�߶ȣ���ѡ
 *     align: "none"                            //���뷽ʽ��֧��right��left��center��none ����ѡ
 * })
 */
/**
 * �ڵ�ǰѡ��λ�ò���һ�����ڻ���ʱ��
 * @name date|time
 * @grammar editor.execCommand("date");
 */
/**
 * �ڵ�ǰѡ��λ�ò���һ����ҳ�����
 * @name pageBreak
 * @grammar editor.execCommand("pageBreak");
 */
/**
 * �л�Դ��༭ģʽ�͸��ı��༭ģʽ
 * @name source
 * @grammar editor.execCommand("source");
 */
/**
 * IE�½������ģʽ
 * @name snapScreen
 * @grammar editor.execCommand("snapScreen");
 */
/**
 * ������
 * @name insertTable
 * @grammar editor.execCommand("insertTable",rows,cols);
 */

/**
 * �����滻
 * @name searchreplace
 * @grammar editor.execCommand("searchreplace",opt);
 * @desc
 * opt�Ǹ�json����,��������
 * * ''all'' true��ʾ���������ĵ���false��ʾ���ϴε�λ�ÿ�ʼ����,Ĭ����false
 * * ''casesensitive'' ��Сд���У�true������,Ĭ����false
 * * ''dir'' 1��ʾ��ǰ����飬��1��ʾ�Ӻ���ǰ
 * * ''searchStr'' ���ҵ��ַ���
 * * ''replaceStr'' �滻�õ��ַ���
 */









