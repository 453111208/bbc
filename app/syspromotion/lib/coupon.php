<?php

class syspromotion_coupon extends syspromotion_abstract_promotions {

    public function getCouponList($filter)
    {
        $filter['shop_id'] = $filter['shop_id'];
        return app::get('syspromotion')->model('coupon')->getList('*', $filter, '0', '-1', 'coupon_id DESC');
    }

    public function getCoupon($couponId)
    {
        return app::get('syspromotion')->model('coupon')->getRow('*', array('coupon_id'=>$couponId));
    }

    public function getCouponItems($couponId)
    {
        return app::get('syspromotion')->model('coupon_item')->getList('*', array('coupon_id'=>$couponId));
    }

    /**
     * @brief 删除优惠券
     * @author lujy
     * @param $params array
     *
     * @return
     */
    public function deleteCoupon($params)
    {
        $couponId = $params['coupon_id'];
        if(!$couponId)
        {
            throw new \LogicException('优惠券id不能为空！');
            return false;
        }


        $objMdlCoupon = app::get('syspromotion')->model('coupon');
        $couponInfo = $objMdlCoupon->getRow('shop_id, canuse_start_time',array('coupon_id'=>$couponId));
        if( $couponInfo['shop_id'] != $params['shop_id'] )
        {
            throw new \LogicException('只能删除店铺所属的优惠券！');
            return false;
        }
        if( time() > $couponInfo['canuse_start_time'] )
        {
            throw new \LogicException('优惠券生效后则不可删除！');
            return false;
        }

        $db = app::get('syspromotion')->database();
        $db->beginTransaction();
        try
        {
            if( !$objMdlCoupon->delete( array('coupon_id'=>$couponId) ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除优惠券失败!'));
            }
            $objMdlCouponItem = app::get('syspromotion')->model('coupon_item');
            if( !$objMdlCouponItem->delete( array('coupon_id'=>$couponId) ) )
            {
                throw new \LogicException(app::get('syspromotion')->_('删除优惠券失败!'));
            }
            $db->commit();
        }
        catch(\Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * 保存优惠券
     * @param  array $data 优惠券传入数据
     * @return bool       是否保存成功
     */
    public function saveCoupon($data)
    {
        $couponData = $this->__preareData($data);
        $objMdlCoupon = app::get('syspromotion')->model('coupon');

        $db = app::get('syspromotion')->database();
        $db->beginTransaction();
        try
        {
            if( !$objMdlCoupon->save($couponData) )
            {
                throw \LogicException('优惠券保存失败');
            }
            if(!$this->__saveCouponItem($couponData))
            {
                throw new \LogicException('优惠券促销关联商品保存失败!');
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;

    }

    /**
     * 保存满减促销关联的商品信息
     */
    private function __saveCouponItem(&$couponData)
    {
        //ajx改为调用search接口
        $searchParams = array(
            'item_id' => implode(',',$couponData['rel_item_ids']),
            'fields' => 'item_id,title,image_default_id,cat_id,brand_id,price',
        );
        $itemsList = app::get('syspromotion')->rpcCall('item.search',$searchParams);
        if( empty($itemsList) ) return false;

        $newItemsList = array();
        foreach($itemsList['list'] as $item)
        {
            $newItemsList[$item['item_id']] = $item;
        }
        $objMdlCouponItem = app::get('syspromotion')->model('coupon_item');
        // 先删除优惠券关联的商品
        $objMdlCouponItem->delete(array('coupon_id'=>$couponData['coupon_id']));
        foreach($couponData['rel_item_ids'] as $itemid)
        {
            $couponRelationItem = array(
                'coupon_id' => $couponData['coupon_id'],
                'item_id' => $itemid,
                'shop_id' =>$couponData['shop_id'],
                'promotion_tag' =>'优惠券',
                'leaf_cat_id' => $newItemsList[$itemid]['cat_id'],
                'brand_id' => $newItemsList[$itemid]['brand_id'],
                'title' => $newItemsList[$itemid]['title'],
                'price' => $newItemsList[$itemid]['price'],
                'image_default_id' => $newItemsList[$itemid]['image_default_id'],
                'canuse_start_time' => $couponData['canuse_start_time'],
                'canuse_end_time' => $couponData['canuse_end_time'],
            );
            $objMdlCouponItem->save($couponRelationItem);
        }
        return true;
    }

    private function __preareData($data) {
        $aResult = array();
        $aResult = $data;

        if($data['coupon_id'])
        {
            $objMdlCoupon = app::get('syspromotion')->model('coupon');
            $couponInfo = $objMdlCoupon->getRow('*',array('coupon_id'=>$data['coupon_id']));
            if( time() >= $couponInfo['cansend_start_time'] )
            {
                throw new \LogicException('优惠券发放开始时间内则不可进行编辑!');
            }
        }
        else
        {
            $aResult['coupon_prefix'] = $this->makePrefixKey();
            $aResult['created_time'] = time();
        }
        if( !$aResult['coupon_key'] )
        {
            $aResult['coupon_key'] = substr( base64_encode(serialize($data)), rand(0,10),10 );
        }
        if( $data['limit_money'] <= 0 )
        {
            throw new \LogicException('满足条件金额必须大于0!');
        }
        if( $data['deduct_money'] <= 0 )
        {
            throw new \LogicException('优惠金额必须大于0!');
        }
        if( $data['deduct_money'] >= $data['limit_money'] )
        {
            throw new \LogicException('满足条件金额必须大于优惠金额!');
        }
        if( $data['max_gen_quantity'] < $data['userlimit_quantity'])
        {
            throw new \LogicException('优惠券用户可领取数量不能大于优惠券生成总量！');
        }
        if( $data['canuse_start_time'] <= time() )
        {
            throw new \LogicException('优惠券生效开始时间不能小于当前时间！');
        }
        if( $data['canuse_end_time'] <= $data['canuse_start_time'] )
        {
            throw new \LogicException('优惠券的生效结束时间不能小于开始时间！');
        }
        if( $data['cansend_start_time'] >= $data['canuse_start_time'] )
        {
            throw new \LogicException('优惠券可领取的开始时间不能小于优惠券的生效开始时间！');
        }
        if( $data['cansend_end_time'] > $data['canuse_end_time'] )
        {
            throw new \LogicException('优惠券可领取的结束时间不能小于优惠券的生效结束时间！');
        }
        if( !$data['valid_grade'])
        {
            throw new \LogicException('至少选择一个会员等级！');
        }
        if( !$data['coupon_rel_itemids'])
        {
            throw new \LogicException('至少添加一个商品！');
        }

        $aResult['coupon_name'] = strip_tags($data['coupon_name']);
        $aResult['coupon_desc'] = strip_tags($data['coupon_desc']);
        $forPlatform = intval($data['used_platform']);
        $aResult['used_platform'] = $forPlatform ? $forPlatform : '0';

        $aResult['rel_item_ids'] = explode(',', $data['coupon_rel_itemids']);
        $aResult['promotion_tag'] = '优惠券';
        $aResult['coupon_status'] = 'agree';//@todo需要加规则进行检查，例如促销规则离谱需要平台审核

        return $aResult;
    }

    public function makePrefixKey($length=4, $prefixFlag='B') {
        $returnStr='';
        $pattern = '1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        for($i = 0; $i < $length; $i ++) {
            $returnStr .= $pattern {mt_rand ( 0, 35 )};
        }
        return $prefixFlag.$returnStr;
    }

    // 生成优惠券号码
    public function _makeCouponCode($params, $gen_quantity) {
        $couponInfo = app::get('syspromotion')->rpcCall('promotion.coupon.get', array('coupon_id'=>$params['coupon_id']));
        if(!$couponInfo)
        {
            throw new \LogicException('无此优惠券！');
        }
        if($couponInfo['cansend_start_time'] > time())
        {
            throw new \LogicException('优惠券领取时间尚未开始，不能领取！');
        }
        if($couponInfo['cansend_end_time'] < time())
        {
            throw new \LogicException('优惠券领取时间已过，不能领取！');
        }
        if($couponInfo['canuse_end_time'] < time())
        {
            throw new \LogicException('优惠券已过期，无法领取！');
        }
        // 已领优惠券和总领次数顺序不要颠倒
        if( ecmath::number_plus(array(intval($gen_quantity), $couponInfo['send_couponcode_quantity']) ) > $couponInfo['max_gen_quantity'] )
        {
            throw new \LogicException('优惠券已经领完！');
        }
        if($couponInfo['userlimit_quantity'] <= $params['old_quantity'])
        {
            throw new \LogicException('您的领用次数已过！');
        }
        $valid_grade = explode(',', $couponInfo['valid_grade']);
        if(!in_array($params['grade_id'], $valid_grade))
        {
            throw new \LogicException('您的会员等级不可以领取此优惠券！');
        }

        $prefix = $couponInfo['coupon_prefix'];
        $key = $couponInfo['coupon_key'];
        $iNo = bcadd(intval($gen_quantity),$couponInfo['send_couponcode_quantity'],0);
        $coupon_code_count_len = 5;
        $coupon_code_encrypt_len = 5;
        // if ($this->app->getConf('coupon.code.count_len') >= strlen(strval($iNo))) {
        if ($coupon_code_count_len >= strlen(strval($iNo))) {
            $iNo = str_pad($this->dec2b36($iNo), $coupon_code_count_len, '0', STR_PAD_LEFT);
            $checkCode = md5($key.$iNo.$prefix);
            // $checkCode = strtoupper(substr($checkCode, 0, $this->app->getConf('coupon.code.encrypt_len')));
            $checkCode = strtoupper(substr($checkCode, 0, $coupon_code_encrypt_len));
            $memberCouponCode = $couponInfo['coupon_code']= $prefix.$checkCode.$iNo;

            // $objMdlCoupon = app::get('syspromotion')->model('coupon');


            $db = app::get('syspromotion')->model('coupon')->database();
            $sqlStr = "UPDATE syspromotion_coupon SET send_couponcode_quantity=ifnull(send_couponcode_quantity,0)+? WHERE coupon_id=? ";
            if ($db->executeUpdate($sqlStr, [$gen_quantity, $params['coupon_id']]))
            {
                return $couponInfo;
            }
            // if($objMdlCoupon->update(array('send_couponcode_quantity'=>$iNo), array('coupon_id'=>$params['coupon_id'])))
            // {
            //     return $couponInfo;
            // }
            else
            {
                return false;
            }
        }else{
            throw new \LogicException('优惠券已领完！');
            return false;
        }
    }

    private function dec2b36($int)
    {
        $b36 = array(0=>"0",1=>"1",2=>"2",3=>"3",4=>"4",5=>"5",6=>"6",7=>"7",8=>"8",9=>"9",10=>"A",11=>"B",12=>"C",13=>"D",14=>"E",15=>"F",16=>"G",17=>"H",18=>"I",19=>"J",20=>"K",21=>"L",22=>"M",23=>"N",24=>"O",25=>"P",26=>"Q",27=>"R",28=>"S",29=>"T",30=>"U",31=>"V",32=>"W",33=>"X",34=>"Y",35=>"Z");
        $retstr = "";
        if($int>0)
        {
            while($int>0)
            {
                $retstr = $b36[($int % 36)].$retstr;
                $int = floor($int/36);
            }
        }
        else
        {
            $retstr = "0";
        }

        return $retstr;
    }

    public function getFlagFromCouponCode($couponCode) {
        return substr($couponCode, 0, 1);
    }

    public function getCouponCode($params)
    {

    }

}
