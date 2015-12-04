<?php
/**
 * 对评价进行新增，更新，获取
 */
class sysrate_traderate {

    public function __construct()
    {
        $this->objMdlTraderate = app::get('sysrate')->model('traderate');
    }

    /**
     * 新增一个订单的评价
     *
     * @param array $data 评价的内容
     */
    public function add($data)
    {
        $params['tid'] = $data['tid'];
        $params['fields'] = 'tid,shop_id,user_id,status,end_time,orders.title,orders.status,orders.spec_nature_info,orders.end_time,orders.price,orders.pic_path,orders.buyer_rate,orders.user_id,orders.aftersales_status,orders.shop_id,orders.oid,orders.anony,orders.item_id';
        $tradeData = app::get('sysrate')->rpcCall('trade.get', $params);

        //验证订单是否可以评价
        $this->__checkTradeRate($tradeData,$data);

        $isBuyerRateCount = 0;//需要评价的子订单数量
        $isShopDsr = true; //默认需要店铺动态评分
        foreach( $tradeData['orders'] as $key=>$value )
        {
            //没申请售后并且订单完成，没有评价的子订单可以进行评价
            if( !$value['aftersales_status'] && $value['buyer_rate'] == '0' && $value['status'] == 'TRADE_FINISHED' )
            {
                $oid = $value['oid'];
                $tradeOrderData[$oid] = $value;
                $isBuyerRateCount++;//需要评价的子订单数量
            }
            elseif( $value['buyer_rate'] == '1')
            {
                //有子订单进行过评价，那么则不需要进行店铺动态评分
                $isShopDsr = false;
            }
        }

        //没有需要评价的子订单
        if( !$isBuyerRateCount )
        {
            throw new \LogicException(app::get('sysrate')->_('不需要评价'));
        }

        if( $isShopDsr )//需要进行店铺动态评分
        {
            //添加店铺动态评分
            if( !kernel::single('sysrate_shopScore')->add($tradeData['tid'], $tradeData['shop_id'],$tradeData['user_id'], $data) )
            {
                throw new \LogicException(app::get('sysrate')->_('店铺动态评分失败'));
            }
        }

        $successRateCount = 0;//此次评价成功的数量
        $successRateOid = array();//此次成功评价的子订单号
        foreach( $data['rate_data'] as $rateData )
        {
            if( $tradeOrderData[$rateData['oid']] )
            {
                $rateId = $this->__createItemRate($tradeData['tid'], $rateData, $tradeOrderData[$rateData['oid']]);
                $successRateOid[] = $rateData['oid'];
                $successRateCount++;
            }
            else
            {
                throw new \LogicException(app::get('sysrate')->_('参数错误'));
            }
        }

        if( $isBuyerRateCount && $isBuyerRateCount == $successRateCount )
        {
            $tradeBuyerRate = 1;
            //2 将子订单表中的是否评价字段修改为已评价 tradeApi改造点
            if( !app::get('systrade')->model('trade')->update(['buyer_rate'=>'1'],['tid'=>$tradeData['tid']]))
            {
                throw new \LogicException(app::get('sysrate')->_('更新子订单评价状态失败'));
            }
        }

        if( $successRateOid )
        {
            if( !app::get('systrade')->model('order')->update(['buyer_rate'=>'1'],['oid'=>$successRateOid]) )
            {
                throw new \LogicException(app::get('sysrate')->_('更新订单评价状态失败'));
            }
        }

        #if( $scoreId && !$tradeOrderData['anony'] && $rateAnony )
        #{
        #    //1 订单不匿名，但是评价匿名，那么将订单更新为匿名
        #}

        return true;
    }

    /**
     * 用户进行评价的时候调用此方法验证，验证订单中的子订单是否可以评价
     * 是否可以评价的规则为
     * 1 订单状态为已完成并且在15天之内
     * 2 该订单没有评价过
     *
     */
    private function __checkTradeRate($tradeData,$data)
    {
        if( empty($tradeData) )
        {
            throw new \LogicException(app::get('sysrate')->_('评价的订单不存在'));
        }

        if( $tradeData['status'] != 'TRADE_FINISHED' )
        {
            throw new \LogicException(app::get('sysrate')->_('评价的订单未完成'));
        }

        if( $tradeData['end_time'] && (time() - $tradeData['end_time']) > (15*24*3600) )
        {
            throw new \LogicException(app::get('sysrate')->_('该订单完成已超过15天'));
        }

        if( !$data['user_id'] || $tradeData['user_id'] != $data['user_id'] )
        {
            throw new \LogicException(app::get('sysrate')->_('无操作权限,可能已退出登录，请重新登录'));
        }

        return true;
    }

    /**
     * 新增单个子订单的评价
     *
     * @param array $data 评价的内容
     */
    public function __createItemRate($tid, $data, $tradeOrderData, $test=false)
    {
        if( !$test )//测试模式不需要进行验证判断,主要用于测试用例和评价自动完成
        {
            //检查评价提交的数据是否合法
            $this->__checkRateData($data);
        }

        //评论参数
        $traderateInsert['tid'] = $tid;
        $traderateInsert['oid'] = $data['oid'];
        $traderateInsert['user_id'] = $tradeOrderData['user_id'];
        $traderateInsert['shop_id'] = $tradeOrderData['shop_id'];
        $traderateInsert['item_id'] = $tradeOrderData['item_id'];
        $traderateInsert['item_title'] = $tradeOrderData['title'];//冗余商品名称，用于查询
        $traderateInsert['spec_nature_info'] = $tradeOrderData['spec_nature_info'];//冗余货品描述
        $traderateInsert['item_pic'] = $tradeOrderData['pic_path'];//冗余图片
        $traderateInsert['item_price'] = $tradeOrderData['price'];//冗余单价
        $traderateInsert['content'] = trim($data['content']) ? utils::_RemoveXSS(trim($data['content'])) : trim($data['content']);
        $traderateInsert['rate_pic'] = $data['rate_pic'];
        $traderateInsert['result'] = $data['result'];
        $traderateInsert['is_appeal'] = ($data['result'] == 'good') ? 0 : 1;//如果为好评则不需要申诉
        $traderateInsert['anony'] = $data['anony'];
        $traderateInsert['created_time'] = time();
        $traderateInsert['modified_time'] = time();

        $rateId = $this->objMdlTraderate->insert($traderateInsert);
        if(!$rateId)
        {
            throw new \LogicException(app::get('sysrate')->_('评价提交失败'));
        }

        $filter['item_id'] = $tradeOrderData['item_id'];
        if( $data['result'] == 'good' )
        {
            $filter['rate_good_count'] = 1;
        }
        elseif( $data['result'] == 'bad' )
        {
            $filter['rate_bad_count'] = 1;
        }
        else
        {
            $filter['rate_neutral_count'] = 1;
        }

        $updateResult = app::get('sysrate')->rpcCall('item.updateRateQuantity', $filter);
        if( !$updateResult )
        {
            throw new \LogicException(app::get('sysrate')->_('更新评价数量失败'));
        }

        return $rateId;
    }

    /**
     * 用户在开启修改评论权限的情况下
     */
    public function update($rateId, $data)
    {
        $this->__checkUpdateData($rateId, $data);
        $data['is_lock'] = 1;
        $data['modified_time'] = time();
        return $this->objMdlTraderate->update($data, ['rate_id'=>$rateId]);
    }

    private function __checkUpdateData($rateId, $data)
    {
        $rateData = $this->objMdlTraderate->getRow('rate_id,user_id,is_lock',array('rate_id'=>$rateId));
        if( empty($rateData) )
        {
            throw new \LogicException(app::get('sysrate')->_('还未评价'));
        }

        if( !$data['user_id'] || $rateData['user_id'] != $data['user_id'] )
        {
            throw new \LogicException(app::get('sysrate')->_('无操作权限,可能已退出登录，请重新登录'));
        }

        if( $rateData['is_lock'] )
        {
            throw new \LogicException(app::get('sysrate')->_('无修改评价权限'));
        }

        $ratePic = explode(',',$data['rate_pic']);
        if( count($ratePic) > 5 )
        {
            throw new \LogicException(app::get('sysrate')->_('晒单最多上传5张图片'));
        }

        if( !in_array($data['result'],['good','neutral','bad']) )
        {
            throw new \LogicException(app::get('sysrate')->_('请检查商品评分参数是否正确'));
        }

        return true;
    }

    /**
     * 检查评价提交的数据是否合法
     */
    private function __checkRateData($data)
    {
        $rateData = $this->objMdlTraderate->getRow('rate_id',array('oid'=>$data['oid']));
        if( !empty($rateData) )
        {
            throw new \LogicException(app::get('sysrate')->_('该订单已评价'));
        }

        if( $data['content'] && mb_strlen(trim($data['content']),'utf8') > 300 )
        {
            throw new \LogicException(app::get('sysrate')->_('评价内容不能超过300个字'));
        }

        $ratePic = explode(',',$data['rate_pic']);
        if( count($ratePic) > 5 )
        {
            throw new \LogicException(app::get('sysrate')->_('晒单最多上传5张图片'));
        }

        if( !in_array($data['result'],['good','neutral','bad']) )
        {
            throw new \LogicException(app::get('sysrate')->_('请检查评价结果参数是否正确'));
        }

        if( $data['result'] == 'bad' && empty($data['content']) )
        {
            throw new \LogicException(app::get('sysrate')->_('请填写差评理由'));
        }

        return true;
    }

    /**
     * 商家解释，回复评论
     *
     * @param int $rateId 评论ID
     * @param string $content 回复内容
     *
     */
    public function reply($rateId, $content, $shopId)
    {
        if( empty($content) || mb_strlen($content,'utf8') > 300 || mb_strlen($content,'utf8') < 5 )
        {
            throw new \LogicException(app::get('sysrate')->_('请填写5-300个字的回复内容'));
        }

        $rateData = $this->objMdlTraderate->getRow('rate_id,shop_id,is_reply', array('rate_id'=>$rateId));
        if( $rateData['shop_id'] != $shopId )
        {
            throw new \LogicException(app::get('sysrate')->_('无操作权限,可能已退出登录，请重新登录'));
        }
        if( $rateData['is_reply'] )
        {
            throw new \LogicException(app::get('sysrate')->_('该评论已回复'));
        }

        $updateData['reply_content'] = $content;
        $updateData['is_reply'] = 1;
        $updateData['reply_time'] = time();
        $updateData['modified_time'] = time();

        return $this->objMdlTraderate->update($updateData, array('rate_id'=>$rateId));
    }

    /**
     * 设置评价为匿名
     *
     * @param int $rateId 评价ID 1 匿名 0 实名
     */
    public function setAnony($rateId, $userId)
    {
        if( empty($rateId) ) return false;

        $rateData = $this->objMdlTraderate->getList('rate_id,user_id,anony,oid',array('rate_id'=>$rateId));
        if( empty($rateData) ) return true;
        if( !$verify )
        {
            $this->__verifySetAnony($rateData, $userId);
        }

        return $this->objMdlTraderate->update(['anony'=>'1'],['rate_id'=>$rateId]);
    }

    //检查是否可以设置匿名
    private function __verifySetAnony($rateData, $userId)
    {
        foreach( (array)$rateData as $row )
        {
            if( $row['user_id'] != $userId )
            {
                throw new \LogicException(app::get('sysrate')->_('无操作权限,可能已退出登录，请重新登录'));
            }

            if( $rateData['anony'] == '1' )
            {
                throw new \LogicException(app::get('sysrate')->_('已是匿名不需要设置'));
            }
        }

        return true;
    }

}

