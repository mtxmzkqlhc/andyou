<?php
/**
 * 前台结账
 *
 */

//error_reporting(0);
class  Andyou_Page_Checkout  extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Checkout';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}

    /**
     * 获得数据列表
     */
	public function doDefault(ZOL_Request $input, ZOL_Response $output){
		
        //获得所有会员类型
        $output->memberCate = Helper_Member::getMemberCatePairs();
		//获得所有的员工
        $output->staffArr  = Helper_Staff::getStaffPairs();
        
        //获得所有配置
        $output->sysOptions = Helper_Option::getAllOptions();        
        $output->scoreRatio = !empty($output->sysOptions["ScoreRatio"]) ? $output->sysOptions["ScoreRatio"]["value"] : 0;
        
        
        //获得商品的所有分类
        $output->productCateArr = Helper_Product::getProductCatePairs();
        $output->productCateJson = api_json_encode($output->productCateArr);
        
		$output->setTemplate('Checkout');
	}
	
    public function doDone(ZOL_Request $input, ZOL_Response $output){
        $billInfo    = $input->post("bill"); 
        $itemIdArr   = $input->post("item_id");//所有产品ID 
        $itemDiscArr = $input->post("item_disc");//所有产品折扣
        $itemNumArr  = $input->post("item_num");//所有产品产品数量
        $staffid     = (int)$input->post("staffid");//员工
        $isBuyScore  = (int)$input->post("isBuyScore");//是否是从积分兑换页面的提交
        $remark      = $input->post("remark");//填写的备注
        $endSumModifyFlag = (int)$input->post("endSumModifyFlag");//是否手工调整了最总价格
        $endBillPrice = $billInfo["bill_end_sum"]; //最终的价格，这个价格是可以修改的
        $db = Db_Andyou::instance();
        //----------------------
        //获得会员信息
        //----------------------
        $memberId    = (int)$input->post("memberId");//会员ID
        $memberInfo  = Helper_Member::getMemberInfo(array("id"=>$memberId));
        if($memberInfo){ 
            $memberScore = $memberInfo["score"];   //会员的积分
            $memberCard  = $memberInfo["balance"]; //会员的卡内余额
        }else{//如果没有这个会员
            $memberScore = $memberCard = 0;
            $billInfo["bill_member_card"]  = 0;//传递过来的会员扣费，强制失效掉
            $billInfo["bill_member_score"] = 0;
        }
        
        //----------------------
        //获得说有的商品信息
        //----------------------
        $proInfoArr = array();
        $sumPrice   = 0;
        //原价总金额
        $orgSumPrice = 0; //商品总额
        //折扣获得的金额
        $discGetMoney = 0;
        //记录所有商品的总价格
        $itemSumPrice = 0;
        //判断是否购买了其他的服务，如次卡
        $hasBuyOtherPro  = false;
        $otherProItemArr = array();
        if($itemIdArr){
            foreach($itemIdArr as $idx => $pid){
               $proInfo = Helper_Product::getProductInfo(array('id'=>$pid));
               $num = (int)$itemNumArr[$idx];
               $price       = $num * $proInfo["oprice"] * $itemDiscArr[$idx];
               $orgSumPrice += $num * $proInfo["oprice"];
               $sumPrice += $price;
               $itemSumPrice += $price;
               $proInfoArr[] = array(
                   'proId'      => $pid,
                   'num'        => $num,
                   'discount'   => $itemDiscArr[$idx],
                   'price'      => $price,
                   'staffid'    => $staffid,
               );
               if($proInfo["ctype"]!=1){//购买非商品类的服务
                   $hasBuyOtherPro = true;
                   $otherProItemArr[] = array(
                       'info' => $proInfo,
                       'num'  => $num,
                   );
               }
            }
        }
        //生成一个单号
        #$bno = Helper_Bill::getMaxBno();
        $bno = Helper_Bill::getCommonMaxBno();
        
        //----------------------
        //计算总金额
        //----------------------
        $billDisc        = $billInfo["bill_disc"];//总折扣
        $sumPriceAftDisc = $sumPrice;//$orgSumPrice * $billDisc;//总折扣的价格  $sumPrice * $billDisc; //不可以给商品设置单价了
        $discGetMoney    = $orgSumPrice - $sumPriceAftDisc;//折扣省下来的金额
        
        //扣除会员卡内余额
        $leftCard = $memberCard;//扣除后，卡内还有的余额
        $useCardFlag = false;
        $useCardMoney = 0;
        if($sumPriceAftDisc && $memberCard && $billInfo["bill_member_card"]){
            
            $useCard = min($memberCard,$billInfo["bill_member_card"]);
            $useCard = $useCard * 100;
            $useCard = min($useCard,$sumPriceAftDisc);//卡余额和收费的金额比较
            $leftCard = $memberCard - $useCard /100;
            
            $useCardMoney = $useCard /100;//记录用了多少卡的金额
            $sumPriceAftDisc = $sumPriceAftDisc - $useCard;
            
            $useCardFlag = true;
        }
        //会员积分计算
        $sysOptions = Helper_Option::getAllOptions();        
        $scoreRatio = !empty($sysOptions["ScoreRatio"]) ? $sysOptions["ScoreRatio"]["value"] : 0;
        $useScore   = (int)$billInfo["bill_member_score"];
        if($useScore && $memberScore > 0){
            $duihuanRatio = !empty($sysOptions["DuihuanRatio"]) ? $sysOptions["DuihuanRatio"]["value"] : 0;
            $sumPriceAftDisc = $sumPriceAftDisc - round($useScore/$duihuanRatio)*100;
        }
        /*
         * 先不用会员积分了
        if($sumPriceAftDisc && $memberScore && $billInfo["bill_member_score"]){
            $useScore = min($memberScore,$billInfo["bill_member_score"]);//需要花多少积分，避免比用户的积分还多
            
            //将用户的积分转换成钱
            $scoreMoney = round($useScore / $scoreRatio,2);// 9 = 270 /30
            $scoreMoney = $scoreMoney * 100;             // 900 = 9 * 100
            $scoreMoney = min($scoreMoney,$sumPriceAftDisc); //避免花费的积分比剩余金额还多 
            
            $sumPriceAftDisc = $sumPriceAftDisc - $scoreMoney;
            
        }*/
        
        //获得订单的信息
        $sumPriceAftDisc = round($sumPriceAftDisc/100)*100; //金额的四舍五入        
        $billDetail = array(
            //######### 注意如果这里要数据，务必要确认和数据的字段是否一致，否则不能插入到数据库 ########
            'useScore' => $useScore,
            'useScoreAsMoney' => round($scoreMoney/100,2),
            'useCard'  => $billInfo["bill_member_card"],
            'price'    => $sumPriceAftDisc,  //客户需要交纳的金额            
            'discount' => $billDisc,
            'orgPrice' => $orgSumPrice, //所有商品折扣前的金额
            'staffid'  => $staffid,
            'memberId' => $memberId,
            'bno'      => $bno,
            'tm'       => SYSTEM_TIME,
            'dateDay'  => date("Ymd"),
            'memberScore'     => $memberScore,
            'memberCard'      => $memberCard,
            'remark'          => $remark,
            'isBuyScore'      => $isBuyScore, //是否是积分兑换
        );
        if($memberInfo){
            $billDetail["phone"] = $memberInfo["phone"];
        }
        //如果销售眼前台修改了应收款，不是计算出来的，就记录
        if($endSumModifyFlag){
           if($sumPriceAftDisc != $endBillPrice){
               $billDetail["priceTrue"] = $billDetail["price"];
               $billDetail["price"]     = $endBillPrice*100;//使用销售员修改的
               
           } 
        }
        
        //积分的重新计算
        //         =  积分剩余额                                    +  实际消费产品的积分
        $leftScore = ($memberScore - (int)$billDetail["useScore"]) +  ($billDetail["price"] / 100 + $billDetail["useCard"]) * $scoreRatio ;
        $memLeftInfo = array();
        $memLeftInfo['score'] = round($leftScore);
        
        if($useCardFlag){//使用了会员卡
            $memLeftInfo['balance'] = $leftCard;
        }
        //计算用户的总消费额
        $memLeftInfo['allsum'] = $memberInfo['allsum'] + $endBillPrice + $billInfo["bill_member_card"];
        //记入订单库
        
        $output->newScore       = (int)(($billDetail['useCard'] + ($billDetail['price']/100)) * $scoreRatio);//获得的积分
        $billDetail["getScore"] = $output->newScore;
        $bid = Helper_Dao::insertItem(array(
		        'addItem'       =>  $billDetail,
		        'dbName'        =>  'Db_Andyou',
		        'tblName'       =>  'bills',
		));
        
        //记入订单详情
        if($proInfoArr){
            foreach($proInfoArr as $item){
                //补充信息
                $item["bid"] = $bid;
                $item["bno"] = $bno;
                $item["memberId"] = $memberId;
                $item["tm"]       = SYSTEM_TIME;
                
                 Helper_Dao::insertItem(array(
                        'addItem'       =>  $item,
                        'dbName'        =>  'Db_Andyou',
                        'tblName'       =>  'billsitem',
                ));
            }
        }
        
        //更新用户详情信息
        Helper_Dao::updateItem(array(
	            'editItem'       =>  $memLeftInfo,
	            'dbName'         =>  'Db_Andyou',
	            'tblName'        =>  'member',
	            'where'          =>  ' id=' . $memberId, 
	    ));
        
        $staffArr  = Helper_Staff::getStaffPairs();
         
        //更新商品的库存情况
        if($itemIdArr){
            foreach($itemIdArr as $idx => $pid){               
                $num = (int)$itemNumArr[$idx];
                $db->query("update product set stock = stock - {$num} where id =  {$pid} ");
                /*
               $proInfo = Helper_Product::getProductInfo(array('id'=>$pid));
               $stock   = (int)$proInfo["stock"];
               if($stock){
                   $num = (int)$itemNumArr[$idx];
                   $stock = $stock - $num;
                   //if($stock < 0)$stock = 0; 不限制负数了，一直减下去
                   
                    Helper_Dao::updateItem(array(
                            'editItem'       =>  array("stock"=>$stock),
                            'dbName'         =>  'Db_Andyou',
                            'tblName'        =>  'product',
                            'where'          =>  ' id=' . $pid, 
                    ));
                   
               }
                */
            }
        }
        
        //记录积分历史
        if($output->newScore && $memberId){
            //记录自己的积分历史
            Helper_Member::addScoreLog(array(
                'memberId'         => $memberId, #ID
                'direction'        => 0, #1 减 0 加
                'score'            => $output->newScore, #积分
                'orgScore'         => $memberInfo["score"], #原始积分
                'bno'              => $bno, #订单号
                'remark'           => '消费', #
            ));
            
            //给介绍人增加积分
            if($sysOptions && !empty($sysOptions["MemberParentRatio"])&& !empty($sysOptions["MemberParentRatio"]["value"])){
                $introducerId = $memberInfo["introducerId"];
                $introducer   = $memberInfo["introducer"];
                if(empty($memberInfo["allsum"])){//如果用户从来没有消费过，也就是第一次消费才给介绍人增加积分
                    if(!$introducerId || !$introducer){#如果没有ID,就尝试活儿

                        $introInfo = Helper_Member::getMemberInfo(array('phone'=>$introducer,'id'=>$introducerId));
                        $introducerId = $introInfo["id"];
                        $iscore = $output->newScore * $sysOptions["MemberParentRatio"]["value"];

                        //更新积分
                        $sql = "update member set score = score + {$iscore}  where id = {$introducerId}";
                        $db->query($sql);
                    
                        //记录积分
                        Helper_Member::addScoreLog(array(
                            'memberId'         => $introducerId, #ID
                            'direction'        => 0, #1 减 0 加
                            'score'            => $iscore, #积分
                            'orgScore'         => $introInfo["score"], #原始积分
                            'bno'              => $bno, #订单号
                            'remark'           => '介绍【'.$memberInfo["phone"]."-".$memberInfo["name"].'】消费得积分'.$output->newScore.'*' .$sysOptions["MemberParentRatio"]["value"], #
                        ));

                    }
                }
            }
        }
        
        //记录会员会员卡使用记录 
        if($memberId && $billDetail["useCard"]){
            Helper_Member::addCardLog(array(
                'memberId'         => $memberId, #ID
                'direction'        => 1, #1 减 0 加
                'card'             => $billDetail["useCard"], #
                'orgCard'          => $memberInfo["balance"], 
                'bno'              => $bno, #订单号
                'remark'           => '消费', #
            ));
        }
        
        //记录会员会员卡使用记录 
        if($memberId && $billDetail["useScore"]){
            Helper_Member::addScoreLog(array(
                'memberId'         => $memberId, #ID
                'direction'        => 1, #1 减 0 加
                'score'             => $billDetail["useScore"], #
                'orgScore'          => $memberInfo["score"], 
                'bno'              => $bno, #订单号
                'remark'           => '积分兑换', #
            ));
        }
        
        //记录订单的一些额外的信息，但是不记录到数据库
        $billDetail['itemSumPrice']   = $itemSumPrice; //所有商品折扣后的累计金额
        
        //购买了其他商品的服务，如次卡
        if($hasBuyOtherPro && $memberId){
            foreach($otherProItemArr as $re){
                $info = $re["info"];
                $num  = (int)$re["num"];
                $tmpRow = array(
                      'memberId' => $memberId,
                      'phone'    => $memberInfo["phone"], 
                      'proId'    => $info["id"],
                      'name'     => $info["othername"],
                      'proName'  => $info["name"],
                      'num'      => $info["num"],
                      'ctype'    => $info["ctype"],
                      'buytm'    => SYSTEM_TIME,
                );
                $tmpLogRow = array(
                      'memberId'      => $memberId,
                      'phone'         => $memberInfo["phone"], 
                      'otherproId'    => $info["id"],
                      'name'          => $info["othername"],
                      'direction'     => 0,
                      'cvalue'        => $info["num"],
                      'orgcvalue'     => 0,
                      'ctype'         => $info["ctype"],
                      'dateTm'        => SYSTEM_TIME,
                      'staffid'       => $staffid,
                      'bno'           => $bno,
                );
                //记录用户的所有服务
                for($i=0;$i<$num;$i++){                    
                    Helper_Dao::insertItem(array(
                           'addItem'       =>  $tmpRow,
                           'dbName'        =>  'Db_Andyou',
                           'tblName'       =>  'memeberotherpro',
                   ));
                   //数据变化日志 
                   Helper_Dao::insertItem(array(
                           'addItem'       =>  $tmpLogRow,
                           'dbName'        =>  'Db_Andyou',
                           'tblName'       =>  'log_useotherpro',
                   ));
                }
            }
        }
        
        
        
        
         //准备进入打印页面
        $output->bno          = $bno;
        $output->bid          = $bid;
        $output->bsn          = substr(md5($bid."HOOHAHA"), 0,10);
        $output->billDetail   = $billDetail;
        $output->proInfoArr   = $proInfoArr;
        $output->memLeftInfo  = $memLeftInfo;
        $output->staffid      = $staffid;
        $output->staffName    = $staffArr[$staffid];
        $output->memberInfo   = $memberInfo;//会员信息
        
        $output->discGetMoney = $discGetMoney; //折扣省下的钱
        $output->orgSumPrice  = $orgSumPrice; //原始总价        
        $output->isBuyScore   = $isBuyScore;
        Helper_Bill::createOneCommonBno();//生成一个通用订单号
		$output->setTemplate('BillPrint');
        
        
    }
}

