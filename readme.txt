** 1.消费的时候，没有扣除库存
** 2.消费的时候，记录积分抵了多少钱，
** 3. 用户消费的时候，记录用户所有的账户状态
** 4.积分不要弄成30的倍数了
** 6.登录和权限
** 7.数量修改，要重新计算
** 8. 打印页面
**10. 应收款四舍五入 （到元，不要小数点）
** 11. 积分换的钱也要乘以折扣
** 12. 应收款 可以修改，需要记录到数据库，折扣和会员的折扣都要记录
** 13 左侧加一个备注
** 14 商品最低折扣变成0，隐藏掉
** 15，右侧折扣readonly，根据左侧的修改改变
** 17 加一行商品数量合计
** 18 删除，添加提示
** 19 添加清除会员
** 20 清除所有商品
** 21 添加简单的商品查询列表，没有操作，没有进货价
** 24 入库功能，输条码，写数量，点按钮，可能多条码
** 25 积分修改页面 （增加和减少）    备注 + 单号
** 26 会员卡充值功能  （增加和减少） 备注 + 单号
** 27 会员修改功能里面，去掉积分和会员卡
** 29 从消费记录，新建会员（js验证和php验证还没有）
** 32 以后不用积分当钱用了，把这个功能隐藏，小票修改
** 33 设置了折扣，如果在添加产品的时候，折扣显示1
** 34 数据库备份
===== 不紧急，略麻烦 ====
5.以后不同店的使用
** 16 扫条码如果有了，就加1
30 vba 的 ###edit_content### 更换为   $input->request('discut')!=''
** 31 添加会员的时候PHP没有唯一性验证
22 负库存情况出现的时候，作记录
9.自动填写卡内扣款功能更
** 23 盘库，搬着电脑去盘库



OK -- 商品入库：扫描后清空
OK -- 收银：扫描条码后清空，
OK -- 去掉回车提交功能，
OK -- 隐藏使用积分框；
OK -- 更改会员后下方信息折扣相关处刷新
OK -- 收银：打开时鼠标定位在会员卡号中，
OK -- 收银：获得积分不对：消费1元1积分
OK -- 收银：会员卡内余额不更新
OK -- 会员管理：“会员卡”改为“会员充值”，点击关闭没反应，确认时提示充值成功或失败现在的提示店员看不懂
OK -- 把“财务管理”改为“查询统计”
OK -- 收银：点击确认收款，提示是否确认收费？选是则直接打印2份，否则不打印
OK -- 收银：增加清除当前会员功能：清除后右上方会员信息被清除
OK -- 收银：增加清除商品功能：提示是否确认清除当前所有商品，是则清除左下侧信息及右下方列表信息
OK -- 收银：去掉会员信息和账单信息右侧收起及关闭按钮，将账单信息改为消费信息
OK -- 收银：输入左侧折扣时对右侧列表中折扣无影响，列表中的折扣框不允许修改
OK -- “使用会员卡”改为“消费卡内余额”，
OK -- 商品管理：增加字段最低折扣、是否参与积分兑换(默认为否)
OK -- 收银：增加无折扣产品，输入无折扣条码修改左侧折扣商品正常未折扣，再输入有折扣商品则无折扣商品也变成折扣
OK -- 收银：右上角会员信息区域“当前积分”前面加显示：累计消费金额，是指本单之前消费的金额之和
OK -- 会员管理：进入充值界面增加显示当前会员的卡内余额当为减少时减少金额大于余额时提示，
OK -- 在会员管理：增加添加会员功能，进入后显示消费记录中所有待录入会员的记录（页面显示保持相同），将添加用户按钮改为添加会员，进入的页面标题改为添加会员
OK -- 数据导出的;在最上面，
OK -- 添加会员页面：改为先输入手机号，点击验证按钮，如该手机号系统中已存在则在姓名、生日、分类、介绍人手机号、备注中显示相应信息并不允许修改，
OK -- 如不存在则用户输入这些信息。增加输入介绍人手机号，输入后点击验证按钮，检查介绍人手机号是否存在于系统中如不存在则提示该介绍人不存在并清空该介绍人，如存在则保存该介绍人手机号。点击确认添加按钮后如不存在则新增会员信息，如存在则更新既有会员的积分。
OK -- 配置管理：增加介绍消费送介绍人？积分，输入值为百分比，如0.25则为四分之一积分，如2则为双倍积分
OK -- 获得积分
OK -- 在查询统计中增加-积分统计：查询条件包括会员电话、会员姓名，日期。查询列表显示内容为：会员卡号、会员姓名、日期、变化积分、备注、积分余额，消费时变化积分为正数备注为“消费订单20150300068”，当为介绍朋友消费则变化积分为朋友消费的设置倍数积分备注为“朋友消费订单20150300067”，当为兑换积分时积分为负数备注为“积分兑换订单20150300066”,日期为所有积分变化动作产生时的日期，积分余额为上条记录的积分余额+本记录的变化积分
OK -- 具体消费获得积分的时候，要记录这个表
OK -- 介绍人消费的时候也是要获得
OK -- 添加会员的时候，就得记录以下
OK -- 消费记录：去掉添加会员功能，在“收款”前增加显示“兑换积分”、“兑换金额”，把“收款”改为“本次收取金额”
OK -- 在查询统计中增加-充值统计：查询条件包括会员电话、会员姓名，日期。查询列表内容为：会员卡号、会员姓名、日期、变化金额、备注、卡内余额，卡内余额为上条记录的卡内余额+本记录的变化金额
OK -- 具体消费会员卡的时候，要记录






增加积分兑换：在常用操作-前台收银下方增加积分兑换，进入界面同前台收银，区别：用界面颜色或什么方法区别收银界面；扫描条码只能检索参与积分兑换的商品，左下侧增加本次兑换积分应收款根据输入
积分减少，确认收款后打印小票并进入消费记录和积分统计中



添加商品，IE8下周末不能删除一行呢
弹出窗口的高度，没有滚动条


==
tab To enter 
 onkeydown="if(event.keyCode==13)return false;"

class="entrnext" data-tab-index="5"

===
执行SQL
alter table `andyou`.`product` add column `canByScore` tinyint (1)   NOT NULL  COMMENT '是否可以参与积分兑换' after `addtm`
alter table `andyou`.`member` add column `introducer` varchar (20)   NOT NULL  COMMENT '介绍人手机号' after `remark`, add column `introducerId` int (11)   NOT NULL  COMMENT '介绍人的ID' after `introducer`, add column `allsum` int (11)  DEFAULT '0' NOT NULL  COMMENT '消费总额' after `introducerId`,change `id` `id` int (11)   NOT NULL AUTO_INCREMENT , change `name` `name` varchar (10)   NOT NULL , change `phone` `phone` varchar (20)   NOT NULL 
alter table `andyou`.`log_scorechange` add column `bno` varchar (20)   NOT NULL  COMMENT 'bill的no' after `remark`
alter table `andyou`.`log_cardchange` add column `bno` varchar (20)   NOT NULL  COMMENT 'bill no' after `remark`
alter table `andyou`.`bills` add column `getScore` int (11)   NOT NULL  COMMENT '用户获得多少积分' after `priceTrue`
alter table `andyou`.`bills` add column `isBuyScore` tinyint (1)   NOT NULL  COMMENT '这是积分兑换的消费' after `getScore`

alter table `andyou`.`staff` add column `ryear` int (4)   NOT NULL  COMMENT '入职年' after `percentage`, add column `rmonth` tinyint (2)   NOT NULL  COMMENT '入职月' after `ryear`, add column `rday` tinyint (2)   NOT NULL  COMMENT '入职日' after `rmonth`,change `bmonth` `bmonth` tinyint (2)   NULL  COMMENT '生日月'
alter table `andyou`.`membercate` add column `discountStr` varchar (5000)   NOT NULL  COMMENT '折扣字符串' after `discount`

2015-5-20
alter table `andyou`.`log_cardchange` add column `staffid` int (11)   NOT NULL  COMMENT '员工ID' after `remark`

2015-5-21
alter table `andyou`.`bills` add column `isBuyScore` tinyint (1)   NOT NULL  after `priceTrue`
alter table `andyou`.`bills` add column `getScore` int (1)   NOT NULL  after `isBuyScore`

???cardno???字段呢？


会员卡号取自上次新增加的卡号  怎么取号？？



2015-5-30

alter table `log_productinstorage` add column `cateId` int (4)   NOT NULL  COMMENT '产品子类' after `addNum`
alter table `log_productinstorage` add column `name` varchar (200)   NOT NULL  COMMENT '产品名' after `cateId`, add column `code` varchar (50)   NOT NULL  COMMENT '产品条码' after `name`
alter table `product` add column `ctype` tinyint (1)  DEFAULT '1' NOT NULL  COMMENT '类型 1: 正常商品 2：此卡' after `canByScore`, add column `othername` varchar (50)   NOT NULL  COMMENT '其他商品用的另外名字' after `ctype`, add column `num` int (4)  DEFAULT '0' NOT NULL  COMMENT '其他商品用的一个数量' after `othername`
create table `memeberotherpro` (    `id` int (11)   NOT NULL AUTO_INCREMENT ,  `memberId` int (11)   NOT NULL  COMMENT '会员ID',  `proId` int (11)   NOT NULL  COMMENT '对应的商品名',  `name` varchar (50)   NOT NULL  COMMENT '对应的服务名',  `num` int (4)   NOT NULL  COMMENT '数量',  `buytm` int (11)   NOT NULL  COMMENT '购买时间',  `ctype` tinyint (1)   NOT NULL  COMMENT '对应商品的类型' , PRIMARY KEY ( `id` )  )
create table `log_useotherpro` (    `int` int (11)   NOT NULL AUTO_INCREMENT ,  `memberId` int (11)   NOT NULL  COMMENT '会员ID',  `otherproId` int (11)   NOT NULL  COMMENT '其他商品的ID',  `direction` tinyint (1)   NOT NULL  COMMENT '方向 1 减少 0 加',  `cvalue` int (11)   NOT NULL  COMMENT '变化的数量',  `orgcvalue` int (11)   NOT NULL  COMMENT '改变之前的数值',  `dateTm` int (11)   NULL  COMMENT '时间',  `ctype` tinyint (1)   NOT NULL  COMMENT '商品类型',  `name` varchar (50)   NOT NULL  COMMENT '名称',  `staffid` int (11)   NOT NULL  COMMENT '销售员ID',  `bno` varchar (20)   NOT NULL  COMMENT '订单ID' , PRIMARY KEY ( `int` )  )
 alter table `memeberotherpro` add column `proName` varchar (50)   NOT NULL  COMMENT '所属的商品名' after `name`
alter table `memeberotherpro` add index `memberId` ( `memberId` )
alter table `log_useotherpro` add index `memberId` ( `memberId` )
alter table `log_useotherpro` add column `remark` varchar (2000)   NOT NULL  COMMENT '备注' after `bno`



2015-6-18
create table `bno` (`id` int (11)   NOT NULL AUTO_INCREMENT  COMMENT 'ID',  `tm` int (11)   NOT NULL  COMMENT '日期' , PRIMARY KEY ( `id` )  )
alter table `bno` add index `tm` ( `tm` )




==== 同步部分需要注意几个点 ====
1. 配置的SysId设置了
2. 会员分类都是一致吗？



==== 问题 ====
看下 18222503887 这个会员的余额是多少，怎么是负数呢？