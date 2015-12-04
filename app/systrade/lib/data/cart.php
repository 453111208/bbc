<?php

class systrade_data_cart {

    public $objects = array();

	/**
	 * user Id
	 *
	 * @var int
	 */
    protected $userId = null;

    public function __construct($userId)
    {
        if (!$userId) throw \InvalidArgumentException('user id cannot null.');
        $this->userId = $userId;
        $this->objMdlCart = app::get('systrade')->model('cart');
        $this->objLibItemInfo = kernel::single('sysitem_item_info');
        $this->objMath = kernel::single('ectools_math');
        $this->__instance();
    }

    //初始化加入购物车的处理类
    private function __instance()
    {
        if( !$this->objects )
        {
            $objs = kernel::servicelist('cart_object_apps');
            foreach( $objs as $obj )
            {
                //购物车类型
                $type = $obj->getItemType();
                //排序
                $index = $obj->getCheckSort();
                if( isset($tmp[$index]) ) $index++;

                $tmpIndex[$index] = $type;
                $tmpObjects[$type] = $obj;
            }
            ksort($tmpIndex);
            foreach( $tmpIndex as $type )
            {
                $this->objects[$type] = $tmpObjects[$type];
            }
        }
        return $this->objects;
    }

    /**
     * @brief 检查是否可以购买
     *
     * @param array $params 加入购物车参数
     * @param array $itemData 加入购物车的基本商品数据
     * @param array $skuData 加入购物车的基本SKU数据
     *
     * @return bool
     */
    private function __check($checkParams, $itemData, $skuData)
    {
        foreach( $this->objects as $obj )
        {
            $obj->check($checkParams, $itemData, $skuData);
        }

        return $params;
    }

    /**
     * 检查加入购物车的商品是否有效
     *
     * @param array $itemsData 加入购物车的基本商品数据集合
     * @param array $skuData 加入购物车的基本SKU数据集合
     *
     * @return bool
     */
    private function __checkItemValid($itemsData, $skuData)
    {
        if( empty($itemsData) || empty($skuData) ) return false;

        //违规商品
        if( $itemsData['violation'] ) return false;

        //未启商品
        if( $itemsData['disabled'] ) return false;

        //未上架商品
        if($itemsData['approve_status'] == 'instock' ) return false;

        //已删除SKU
        if( $skuData['status'] == 'delete' )
        {
            return false;
        }

        if( $skuData['store'] <= 0 )
        {
            return false;
        }

        return true;
    }

    /**
     * 如果加入购物车的商品在购物车中已存在，则进行合并
     *
     * @param $cartBasicData 根据加入购物车的的参数，获取到的基本的购物车数据
     * @param $params  加入购物车的的参数
     *
     * @return array
     */
    private function __mergeAddCartData($cartBasicData, $params)
    {
        //购买方式分为加入购物车模式，和立即购买模式
        //加入购物车模式需要判断购物车是否已经有该次购买的商品
        if( $cartBasicData && (empty($params['mode']) || $params['mode'] == 'cart') )
        {
            //总购买数量
            $params['totalQuantity'] = $params['quantity'];
            if( $params['obj_type'] && $cartBasicData['obj_type'] == $cartBasicData['obj_type'] )
            {
                $params['totalQuantity'] += intval($cartBasicData['quantity']);
                $params['cart_id'] = $cartBasicData['cart_id'];
            }
        }
        else
        {
            $params['totalQuantity'] = intval($params['quantity']);
        }

        return $params;
    }

    /**
     * @brief 加入购物车
     *
     * @param array $params 加入购物车参数
     *
     * @return bool
     */
    public function addCart($params)
    {
        $params = utils::_filter_input($params);

        //检查加入购物的商品是否有效
        if( empty($params['sku_id']) )
        {
            throw new \LogicException(app::get('systrade')->_("加入购物车的商品不存在"));
        }

        $skuData = $this->objLibItemInfo->getSkuInfo($params['sku_id']);

        $items['item_id'] = $skuData['item_id'];
        $itemData = $this->objLibItemInfo->getItemInfo($items);

        //检查加入购物的商品是否有效
        if( !$this->__checkItemValid($itemData, $skuData) )
        {
            throw new \LogicException(app::get('systrade')->_("无效商品，加入购物车失败"));
        }

        //如果加入购物车的商品，在购物车中已存在则合并
        $filter['sku_id'] = intval($params['sku_id']);
        $filter['obj_type'] = $params['obj_type'];
        $cartBasicData = $this->getBasicCart($filter);
        $mergeParams = $this->__mergeAddCartData($cartBasicData[0], $params);

        //检查商品是否能加入购物车
        $this->__check($mergeParams, $itemData, $skuData);
        $data = $this->__preAddCartData($mergeParams, $itemData, $skuData);

        if( $params['mode'] == 'fastbuy' )
        {
            return $this->fastBuyStore($data);
        }

        $result = $this->objMdlCart->save($data);
        return $result ? $data : false;
    }

    public function countCart()
    {
        $filter['user_ident'] = $this->objMdlCart->getUserIdentMd5($this->userId);
        $cartNumber = $this->objMdlCart->count($filter);
        return $cartNumber;
    }

    public function setCartCookieNum($cartNumber)
    {
        $cookie_path = kernel::base_url().'/';
        $expire = time()+315360000;
        setcookie('CARTNUMBER',$cartNumber,$expire,$cookie_path);
        return true;
    }

    /**
     * @brief 立即购买流程存储购物数据
     *
     * @param array $params
     *
     * @return bool
     */
    public function fastBuyStore($params)
    {
        kernel::single('base_session')->start();
        $userIdent = $this->objMdlCart->getUserIdentMd5($this->userId);
        $params['is_checked'] = '1';
        $_SESSION['cart_objects_fastbuy'][$userIdent] = $params;
        kernel::single('base_session')->close();
        return true;
    }

    public function fastBuyFetch()
    {
        $userIdent = $this->objMdlCart->getUserIdentMd5($this->userId);
        return $_SESSION['cart_objects_fastbuy'][$userIdent];
    }

    /**
     * @brief 加入购物车数据处理
     *
     * @param array $params 加入购物车基本（合并已有购物车）数据
     *
     * @return array
     */
    private function __preAddCartData($mergeParams, $itemData, $skuData)
    {
        kernel::single('base_session')->start();
        $userIdent = $this->objMdlCart->getUserIdentMd5($this->userId);

        if( $mergeParams['cart_id'] )
        {
            $data['cart_id'] = $mergeParams['cart_id'];
        }
        else
        {
            $data['created_time'] = time();
        }

        // 是否购物车选中了
        $data['is_checked'] = $mergeParams['is_checked'];


        // 保存购物车选中的促销信息状态
        if(isset($mergeParams['selected_promotion']))
        {
            $data['selected_promotion'] = intval($mergeParams['selected_promotion']);
        }
        // else
        // {
        //     $data['selected_promotion'] = '0';
        // }
        $data['user_id'] = $mergeParams['user_id'];
        $data['user_id'] = $data['user_id'] ? $data['user_id'] : '-1';
        $data['user_ident'] = $userIdent;
        $data['shop_id'] = $itemData['shop_id'];
        $data['obj_type'] = $mergeParams['obj_type'] ? $mergeParams['obj_type'] : 'item';
        $data['item_id'] = $itemData['item_id'];
        $data['sku_id'] = $mergeParams['sku_id'];
        $data['title'] = $skuData['title'];
        $data['image_default_id'] = $itemData['image_default_id'];
        $data['quantity'] = $mergeParams['totalQuantity'];

        // 活动，剩余购买数量
        $restActivityNum = $this->restBuyNum($itemData['item_id'], $data['user_id']);
        if( $restActivityNum['ifactivity'] )
        {
            if($mergeParams['totalQuantity'] >= $restActivityNum['restActivityNum'])
            {
                $data['quantity'] = $restActivityNum['restActivityNum']>0 ? $restActivityNum['restActivityNum'] : 0;
            }
        }

        $data['modified_time'] = time();
        return $data;
    }

    // 活动剩余购买数量
    public function restBuyNum($itemId, $userId)
    {
        // 活动，剩余购买数量
        $promotionDetail = app::get('systrade')->rpcCall('promotion.activity.item.info',array('item_id'=>$itemId, 'valid'=>1));
        if($promotionDetail['item_id'])
        {
            $objMdlPromDetail = app::get('systrade')->model('promotion_detail');
            $filter = array('promotion_id'=>$promotionDetail['activity_id'], 'promotion_type'=>'activity', 'user_id'=>$userId);
            $oids = $objMdlPromDetail->getList('oid,item_id', $filter);
            $objMdlOrder = app::get('systrade')->model('order');
            $activityNum = 0;
            foreach($oids as $v)
            {
                $orderInfo = $objMdlOrder->getRow('status,num',array('oid'=>$v['oid']));
                if( !in_array( $orderInfo['status'], array('TRADE_CLOSED_BY_SYSTEM', 'TRADE_CLOSED') ) )
                {
                    $activityNum += $orderInfo['num'];
                }
            }
            $restActivityNum = $promotionDetail['activity_info']['buy_limit']-$activityNum;
            return array('ifactivity'=>$promotionDetail['item_id']?true:false,'restActivityNum'=>$restActivityNum);
        }
    }

    /**
     * @brief 更新购物车信息
     *
     * @param array $params
     *
     * @return bool
     */
    public function updateCart($params)
    {

        if( $params['mode'] == 'fastbuy' ) return false;

        if( !$params['cart_id'] )
        {
            throw new \LogicException(app::get('systrade')->_("无效参数"));
        }
        $filter['cart_id'] = $params['cart_id'];
        $basicCartData = $this->getBasicCart($filter);
        if( !$basicCartData )
        {
            throw new \LogicException(app::get('systrade')->_("无效参数"));
        }

        $params['sku_id'] = $basicCartData[0]['sku_id'];
        $skuData = $this->objLibItemInfo->getSkuInfo($params['sku_id']);
        $items['item_id'] = $skuData['item_id'];
        $itemData = $this->objLibItemInfo->getItemInfo($items);

        //检查加入购物的商品是否有效
        if( !$this->__checkItemValid($itemData, $skuData) )
        {
            $return['valid'] = false;
            return $return;
        }

        //检查商品是否能加入购物车
        $data = $this->__check($params, $itemData, $skuData);

        $data = $this->__preAddCartData($params, $itemData, $skuData);

        $result = $this->objMdlCart->save($data);


        return $result;
    }

    /**
     * @brief 根据条件删除购物车信息，如果条件为空，则清空购物车
     *
     * @param array $params
     *
     * @return bool
     */
    public function removeCart($params, $mode='')
    {
        if($mode=='fastbuy')
        {
            kernel::single('base_session')->start();
            $userIdent = $this->objMdlCart->getUserIdentMd5($this->userId);
            unset( $_SESSION['cart_objects_fastbuy'][$userIdent] );
            kernel::single('base_session')->close();
            return true;
        }
        if( $params )
        {
            $filter['cart_id'] = $params['cart_id'];
        }
        $filter['user_ident'] = $this->objMdlCart->getUserIdentMd5($this->userId);

        $result = $this->objMdlCart->delete($filter);

        $this->countCart();

        return $result;
    }

    /**
     * @brief 根据条件获取购物车信息
     *
     * @param array $filter
     *
     * @return array
     */
    public function getCartInfo($filter=array(), $needInvalid=true, $platform='pc')
    {
        $aCart = array();
        $data = $this->getBasicCartInfo($filter);
        if(!$data) return $aCart;

        $itemsData = $data['itemsData'];
        $skusData  = $data['skusData'];

        $shopIds = implode(',',$data['shopIds']);
        $shopNameArr = app::get('systrade')->rpcCall('shop.get.list',array('shop_id'=>$shopIds,'fields'=>'shop_id,shop_type,shop_name'));
        $shopNameArr = array_bind_key($shopNameArr,'shop_id');

        $result = $this->__preCartInfo($data['cartData'], $shopNameArr, $itemsData, $skusData, $needInvalid, $platform);

        $aCart['resultCartData'] = $result['resultCartData'];
        $aCart['totalCart'] = $result['totalCart'];

        if( empty($aCart['resultCartData']) ) $aCart = array();

        return $aCart;
    }

    /**
     * 获取一条购物车项的数据
     *
     */
    public function getItemCartInfo($filter=array())
    {
        $data = $this->getBasicCartInfo($filter);
        $itemsData = $data['itemsData'];
        $skusData  = $data['skusData'];
        $shopCartData = current($data['cartData']);

        $shopObjectData = $this->__preShopCartInfo($shopCartData, $itemsData, $skusData);
        return $shopObjectData;
    }

    // 简单购物车信息
    public function getBasicCartInfo($filter)
    {
        $cartData = $this->getBasicCart($filter);
        if( empty($cartData) ) return array();
        foreach( $cartData as $row)
        {
            $data['shopIds'][] = $row['shop_id'];
            $itemIds[] = $row['item_id'];
            $skuIds[]  = $row['sku_id'];

            $data['cartData'][$row['shop_id']][] = $row;
        }

        $itemRows = 'item_id,cat_id,title,weight,image_default_id,sub_stock,violation,disabled';
        $itemFields['status'] = 'approve_status';
        $itemFields['promotion'] = 'promotion_ids';
        $data['itemsData'] = kernel::single('sysitem_item_info')->getItemList($itemIds, $itemRows, $itemFields);

        $data['skusData'] = kernel::single('sysitem_item_info')->getSkusList($skuIds,'sku_id,bn,item_id,spec_info,price,weight,status');

        return $data;
    }

    /**
     * @brief 加载购物车显示数据结构
     *
     * @param array $data  加入购物车数据参数
     * @param array $shopNameArr 加入购物车商品的店铺名称集合
     * @param array $itemsData 加入购物车的基本商品数据集合
     * @param array $skusData 加入购物车的基本SKU数据集合
     *
     * @return array
     */
    private function __preCartInfo($data, $shopNameArr, $itemsData, $skusData, $needInvalid, $platform)
    {
        foreach( $data as $shopId=>$shopCartData )
        {
            //如果不存在则表示该店铺，已关闭，那么就不必要再查下该店铺的已加入购物车商品信息
            if( $shopNameArr[$shopId] )
            {
                $shopObjectData = $this->__preShopCartInfo($shopCartData, $itemsData, $skusData, $needInvalid, $platform);
                if( $shopObjectData )
                {
                    $resultCartData[$shopId]['shop_id'] = $shopId;
                    $resultCartData[$shopId]['shop_name'] = $shopNameArr[$shopId]['shopname'];
                    $resultCartData[$shopId]['shop_type'] = $shopNameArr[$shopId]['shop_type'];
                    // 统计购物车的总数量，总价格，促销价格等综合信息
                    $cartTotalInfo = $this->__cartTotal($shopObjectData, $shopId, $needInvalid, $platform);

                    $resultCartData[$shopId]['cartCount'] = $cartTotalInfo['cartCount'];
                    $resultCartData[$shopId]['basicPromotionListInfo'] = $cartTotalInfo['basicPromotionListInfo'];
                    $resultCartData[$shopId]['usedCartPromotion'] = $cartTotalInfo['usedCartPromotion'];
                    $resultCartData[$shopId]['usedCartPromotionWeight'] = $cartTotalInfo['usedCartPromotionWeight'];
                    $resultCartData[$shopId]['cartByPromotion'] = $cartTotalInfo['cartByPromotion'];
                    $resultCartData[$shopId]['object'] = $shopObjectData;
                }
            }
        }

        $return['resultCartData'] = $resultCartData;

        // 统计购物车所有勾选商品的总重量，总数量，总价格，总促销价格
        $totalWeight   = 0;
        $totalNumber   = 0;
        $totalPrice    = 0;
        $totalDiscount = 0;
        foreach($resultCartData as $v)
        {
            $totalWeight   += $v['cartCount']['total_weight'];
            $totalNumber   += $v['cartCount']['itemnum'];
            $totalPrice    += $v['cartCount']['total_fee'];
            $totalDiscount += $v['cartCount']['total_discount'];
        }
        $return['totalCart'] = array(
            'totalWeight'        => $totalWeight,
            'number'             => $totalNumber,
            'totalPrice'         => $totalPrice,
            'totalAfterDiscount' => ecmath::number_minus( array($totalPrice, $totalDiscount) ),
            'totalDiscount'      => $totalDiscount,
        );

        return $return;
    }

    /**
     * 但店铺的购物车的信息统计，及应用促销规则
     * 购物车的cartid根据促销进行分组，索引为0的为不应用促销规则的分组
     * @param  array &$shopObjectData 购物车元数据
     * @return array 应用促销规则后的购物车统计信息
     */
    private function __cartTotal(&$shopObjectData, $shop_id, $needInvalid, $platform)
    {
        // 统一购物车内所有商品关联的促销唯一标识，方便通过促销排列购物车商品
        $allPromotionIdsArray = array();
        foreach ($shopObjectData as $vPromIds)
        {
            if( $vPromIds['promotion_ids'] )
            {
                $withPromotionIds = explode(',', $vPromIds['promotion_ids']);
                foreach ($withPromotionIds as $vPromId)
                {
                    $allPromotionIdsArray[] = $vPromId;
                }
            }
        }
        // 去除重复的促销规则
        $allCurrentCartPromotionIds = array_unique($allPromotionIdsArray);

        $basicPromotionListInfo = array();
        foreach($allCurrentCartPromotionIds as $promotionId)
        {
            $basicPromotionInfo = app::get('systrade')->rpcCall('promotion.promotion.get', array('promotion_id'=>$promotionId, 'platform'=>$platform), 'buyer');
            if($basicPromotionInfo['valid']===true)
            {
                $basicPromotionListInfo[$promotionId] = $basicPromotionInfo;
            }
        }

        // 根据促销分类购物车数据(包括所有勾选及未勾选的，只要选择了促销规则的商品都分组)
        $cartByPromotion = array(); //索引为0的代表是不应用促销规则的购物车id分组
        foreach ($shopObjectData as $k=>$basicCartInfo)
        {
            if( $basicCartInfo['valid'])
            {
                // 根据促销规则，将拥有这个促销的商品id都塞进此数组
                if($basicCartInfo['selected_promotion']==='0')
                {
                    $cartByPromotion['0']['cart_ids'][] = $basicCartInfo['cart_id'];
                }
                elseif($basicCartInfo['selected_promotion'] && $basicPromotionListInfo[$basicCartInfo['selected_promotion']]['valid']==true)
                {
                    // 如果购物车手动选过促销规则，则调用这个选过的促销规则为当前商品应用的促销规则
                    $cartByPromotion[$basicCartInfo['selected_promotion']]['cart_ids'][] = $basicCartInfo['cart_id'];
                }
                else
                {
                    // 根据促销的创建时间进行倒序排序，则最新的默认选为商品的促销规则
                    $withPromotionIds = explode(',', $basicCartInfo['promotion_ids']);
                    if($withPromotionIds)
                    {
                        $promotionTimeSort = array();
                        foreach($withPromotionIds as $vPromId)
                        {
                            // 优惠券促销不参与购物车页面选取@todo
                            if($basicPromotionListInfo[$vPromId]['valid']===true)
                            {
                                $promotionTimeSort[$vPromId] = $basicPromotionListInfo[$vPromId]['created_time'];
                            }
                        }
                        asort($promotionTimeSort);
                        end($promotionTimeSort);
                        $priorityPromotion = key($promotionTimeSort);
                        // 设置最优先的促销规则
                        if($priorityPromotion)
                        {
                            $cartByPromotion[$priorityPromotion]['cart_ids'][] = $basicCartInfo['cart_id'];
                            $shopObjectData[$k]['selected_promotion'] = $priorityPromotion;
                        }
                        else
                        {
                            $cartByPromotion['0']['cart_ids'][] += $basicCartInfo['cart_id'];
                        }
                    }
                    else
                    {
                        $cartByPromotion['0']['cart_ids'][] += $basicCartInfo['cart_id'];
                    }
                }
            }
            else
            {
                $cartByPromotion['0']['cart_ids'][] += $basicCartInfo['cart_id'];
            }
        }

        // 应用促销规则，只包括购物车勾选了商品及有效的商品才应用
        // 未勾选但是选了促销规则的商品不进行应用促销规则
        $cartCount = array();
        $total_fee = $itemnum = $total_weight = 0;
        $itemWithTotalPriceArr = array();
        foreach ($shopObjectData as $k1=>$v1)
        {
            //统计购物车价格，数量，价格等
            if( $v1['valid'] && $v1['is_checked']=='1' )
            {
                $total_weight += $v1['weight'];
                $itemnum += $v1['quantity'];
                $total_fee += $v1['price']['total_price'];
                $itemWithTotalPriceArr[] = $v1['item_id'].'_'.$v1['price']['total_price'];
            }
        }

        $shop_discount_fee = 0; //店铺总的优惠金额
        $shop_promotion_totalWeight = 0; //用于免邮，符合促销的商品的总重量
        $usedCartPromotion = array(); //使用的促销集合
        foreach($cartByPromotion as $k2=>$v2)
        {
            $forPromotionTotalPrice = 0; // 对应促销商品的总价
            $forPromotionTotalQuantity = 0; // 对应促销商品的总数量
            $forPromotionTotalWeight = 0; // 对应促销商品的总重量
            $discount_price = 0;
            foreach ($v2['cart_ids'] as $cartId)
            {
                if($shopObjectData[$cartId]['valid'] && $shopObjectData[$cartId]['is_checked']=='1')
                {
                    $forPromotionTotalPrice += $shopObjectData[$cartId]['price']['total_price'];
                    $forPromotionTotalQuantity += $shopObjectData[$cartId]['quantity'];
                    $forPromotionTotalWeight += $shopObjectData[$cartId]['weight'];
                }
            }

            if($k2=='0')
            {
                $cartByPromotion[$k2]['discount_price'] = 0;
            }
            else
            {
                // 促销门槛的信息各自组织并传递到促销用用接口
                // 给促销规则去做各种验证、应用
                if($basicPromotionListInfo[$k2]['promotion_type'] == 'fullminus')
                {
                    // 应用满减促销
                    $applyData['promotion_id'] = $k2;
                    $applyData['fullminus_id'] = $basicPromotionListInfo[$k2]['rel_promotion_id'];
                    $applyData['forPromotionTotalPrice'] = $forPromotionTotalPrice;
                    $discount_price = app::get('systrade')->rpcCall('promotion.fullminus.apply', $applyData, 'buyer');
                    $cartByPromotion[$k2]['discount_price'] = $discount_price;
                    if($discount_price>0)
                    {
                        $usedCartPromotion[] = $k2;
                        $shop_discount_fee += $discount_price;
                    }
                }
                elseif($basicPromotionListInfo[$k2]['promotion_type'] == 'fulldiscount')
                {
                    // 应用满折促销
                    $applyData['promotion_id'] = $k2;
                    $applyData['fulldiscount_id'] = $basicPromotionListInfo[$k2]['rel_promotion_id'];
                    $applyData['forPromotionTotalPrice'] = $forPromotionTotalPrice;
                    $discount_price = app::get('systrade')->rpcCall('promotion.fulldiscount.apply', $applyData, 'buyer');
                    $cartByPromotion[$k2]['discount_price'] = $discount_price;
                    if($discount_price>0)
                    {
                        $usedCartPromotion[] = $k2;
                        $shop_discount_fee += $discount_price;
                    }
                }
                elseif($basicPromotionListInfo[$k2]['promotion_type'] == 'freepostage')
                {
                    // 应用免邮促销@todo
                    $applyData['promotion_id'] = $k2;
                    $applyData['freepostage_id'] = $basicPromotionListInfo[$k2]['rel_promotion_id'];
                    $applyData['forPromotionTotalPrice'] = $forPromotionTotalPrice;
                    $applyData['forPromotionTotalQuantity'] = $forPromotionTotalQuantity;
                    $freePostageFlag = app::get('systrade')->rpcCall('promotion.freepostage.apply', $applyData, 'buyer');

                    if($freePostageFlag)
                    {
                        $usedCartPromotion[] = $k2;
                        $shop_promotion_totalWeight += $forPromotionTotalWeight;
                    }
                }elseif($basicPromotionListInfo[$k2]['promotion_type'] == 'xydiscount')
                {
                    // 应用x件y折促销@todo
                    $applyData['promotion_id'] = $k2;
                    $applyData['xydiscount_id'] = $basicPromotionListInfo[$k2]['rel_promotion_id'];
                    $applyData['forPromotionTotalPrice'] = $forPromotionTotalPrice;
                    $applyData['forPromotionTotalQuantity'] = $forPromotionTotalQuantity;
                    $discount_price = app::get('systrade')->rpcCall('promotion.xydiscount.apply', $applyData, 'buyer');
                    $cartByPromotion[$k2]['discount_price'] = $discount_price;
                    if($discount_price>0)
                    {
                        $usedCartPromotion[] = $k2;
                        $shop_discount_fee += $discount_price;
                    }
                }
            }
        }

        // 应用优惠券
        $coupon_discount_fee = 0;
        if(!$needInvalid && ($couponCode = $this->getCouponCode($shop_id)) && $itemWithTotalPriceArr)
        {
            $apiParams = array(
                'coupon_code' => $couponCode,
                'cartItemsInfo' => implode('|', $itemWithTotalPriceArr),
            );
            if( $coupon_discount_fee = app::get('systrade')->rpcCall('promotion.coupon.apply', $apiParams,'buyer') )
            {
                $shop_discount_fee += $coupon_discount_fee;
            }
        }
        $return['basicPromotionListInfo'] = $basicPromotionListInfo;
        $return['usedCartPromotion'] = $usedCartPromotion;
        $return['usedCartPromotionWeight'] = $shop_promotion_totalWeight;
        $return['cartByPromotion'] = $cartByPromotion;
        $return['cartCount'] = array(
            'total_weight' => $total_weight,
            'itemnum' => $itemnum,
            'total_fee' => $total_fee,
            'total_discount' => $shop_discount_fee,
            'total_coupon_discount' => $coupon_discount_fee,
        );

        return $return;

    }

    /**
     * @brief 加载每个店铺的商品数据信息
     *
     * @param array $shopCartData 店铺中加入购物车数据的
     * @param array $itemsData 加入购物车的基本商品数据集合
     * @param array $skusData 加入购物车的基本SKU数据集合
     *
     * @return array
     */
    private function __preShopCartInfo($shopCartData, $itemsData, $skusData, $needInvalid, $platform)
    {
        //现在只有普通商品购买流程，因此临时将商品结构写到此
        //如果有其他商品购买类型，则到各类型中进行商品获取
        foreach( $shopCartData as $row )
        {
            $k = $row['cart_id'];
            $itemId = $row['item_id'];
            $skuId = $row['sku_id'];
            $shopObjectData[$k]['cart_id'] = $row['cart_id'];
            $shopObjectData[$k]['item_id'] = $row['item_id'];
            $shopObjectData[$k]['selected_promotion'] = $row['selected_promotion'];
            $shopObjectData[$k]['sku_id'] = $row['sku_id'];
            $shopObjectData[$k]['cat_id'] = $itemsData[$itemId]['cat_id'];
            $shopObjectData[$k]['sub_stock'] = $itemsData[$itemId]['sub_stock'];
            $shopObjectData[$k]['spec_info'] = $skusData[$skuId]['spec_info'];
            $shopObjectData[$k]['bn'] = $skusData[$skuId]['bn'];
            //可售库存
            $shopObjectData[$k]['store'] = $skusData[$skuId]['realStore'];
            $shopObjectData[$k]['status'] = $itemsData[$itemId]['approve_status'];
            // 活动，剩余购买数量,如果剩余
            $restActivityNum = $this->restBuyNum($row['item_id'], $this->userId);
            if( $restActivityNum['ifactivity'] )
            {
                if($row['quantity'] >= $restActivityNum['restActivityNum'])
                {
                    $row['quantity'] = $restActivityNum['restActivityNum']>0 ? $restActivityNum['restActivityNum'] : 0;
                }
            }

            $shopObjectData[$k]['quantity'] = $row['quantity'];//购买数量

            $shopObjectData[$k]['sku_id'] = $row['sku_id'];
            if( $itemsData[$row['item_id']] && $skusData[$row['sku_id']] )
            {
                $shopObjectData[$k]['title'] = $itemsData[$itemId]['title'];
                $shopObjectData[$k]['image_default_id'] = $itemsData[$itemId]['image_default_id'];
            }
            else
            {
                $shopObjectData[$k]['title'] = $row['title'];
                $shopObjectData[$k]['image_default_id'] = $row['image_default_id'];
            }
            $shopObjectData[$k]['weight'] = $this->objMath->number_multiple(array($skusData[$skuId]['weight'],$row['quantity']));
            $activityDetail = $this->getItemActivityInfo($row['item_id'], $platform);
            if($activityDetail['activity_price']>0)
            {
                $shopObjectData[$k]['price']['price'] = $activityDetail['activity_price']; //购买促销后单价
                $shopObjectData[$k]['price']['total_price'] = $this->objMath->number_multiple(array($activityDetail['activity_price'],$row['quantity'])); //购买此SKU总价格

                $oldTotalPrice = $this->objMath->number_multiple(array($skusData[$skuId]['price'],$row['quantity'])); //购买此SKU总价格
                $shopObjectData[$k]['price']['discount_price'] = ecmath::number_minus(array($oldTotalPrice, $shopObjectData[$k]['price']['total_price']));
                $shopObjectData[$k]['activityDetail'] = $activityDetail;
                $shopObjectData[$k]['promotion_type'] = 'activity'; //活动类型（针对单品），
            }
            else
            {
                $shopObjectData[$k]['price']['price'] = $skusData[$skuId]['price']; //购买促销前单价
                $shopObjectData[$k]['price']['total_price'] = $this->objMath->number_multiple(array($skusData[$skuId]['price'],$row['quantity'])); //购买此SKU总价格
            }
            $shopObjectData[$k]['valid'] = $this->__checkItemValid($itemsData[$itemId], $skusData[$row['sku_id']] );//是否为有效数据
            // 如果可购买数量小于等于0（一般是活动限购会导致此情况），则商品失效
            if($shopObjectData[$k]['quantity']<=0)
            {
                $shopObjectData[$k]['valid'] = false;
            }
            if($shopObjectData[$k]['valid'])
            {
                $shopObjectData[$k]['is_checked'] = $row['is_checked'];
            }
            else
            {
                $shopObjectData[$k]['is_checked'] = 0;
            }
            // 获取商品关联的促销信息
            $shopObjectData[$k]['promotion_ids'] = $itemsData[$itemId]['promotion_ids'];
            $shopObjectData[$k]['promotions'] = $this->getItemPromotionInfo($row['item_id'], $platform);
            if( !$needInvalid && (!$shopObjectData[$k]['valid'] || !$row['is_checked']) )
            {
                unset($shopObjectData[$k]);
            }
        }

        return $shopObjectData;
    }

    /**
     * 根据商品返回其相关的促销信息
     * @param  int $itemId 商品id
     * @return array         促销信息数组
     */
    public function getItemPromotionInfo($itemId, $platform='pc')
    {
        $itemPromotionTagInfo = app::get('systrade')->rpcCall('item.promotiontag.get', array('item_id'=>$itemId),'buyer');
        if(!$itemPromotionTagInfo)
        {
            return false;
        }
        $itemPromotionTagInfo['promotion_ids'] = explode(',', $itemPromotionTagInfo['promotion_ids']);
        $allPromotion = array();
        foreach($itemPromotionTagInfo['promotion_ids'] as $promotionId)
        {
            $basicPromotionInfo = app::get('systrade')->rpcCall('promotion.promotion.get', array('promotion_id'=>$promotionId, 'platform'=>$platform),'buyer');
            if($basicPromotionInfo['valid']===true)
            {
                $allPromotion[$promotionId] = $basicPromotionInfo;
            }
        }
        return $allPromotion;
    }

    /**
     * 根据商品返回其单品促销活动，如团购价
     * @param  int $itemId 商品id
     * @return array         活动信息数组
     */
    public function getItemActivityInfo($itemId, $platform='pc')
    {
        $promotionDetail = app::get('topc')->rpcCall('promotion.activity.item.info',array('item_id'=>$itemId, 'platform'=>$platform, 'valid'=>1), 'buyer');
        if(!$promotionDetail)
        {
            return false;
        }
        return $promotionDetail;
    }

    /**
     * @brief 根据条件查询到基本的购物车数据
     *
     * @param array $filter
     *
     * @return array
     */
    private function getBasicCart($filter)
    {
        if( empty($filter['mode']) || $filter['mode'] == 'cart' )
        {
            unset($filter['mode']);
            $params = $this->objMdlCart->getList('*', $filter);
        }
        else
        {
            $params[0] = $this->fastBuyFetch();
        }
        return $params;
    }

    // 获取结算页使用的某店铺优惠券
    public function getCouponCode($shop_id)
    {
        $userIdent = $this->objMdlCart->getUserIdentMd5($this->userId);
        return $_SESSION['cart_use_coupon'][$userIdent][$shop_id];
    }

    /**
     * @brief 选择的优惠券放入购物车优惠券表
     *
     * @param array $data
     *
     * @return array
     */
    public function addCouponCart($coupon_code, $shop_id)
    {
        kernel::single('base_session')->start();
        $userIdent = $this->objMdlCart->getUserIdentMd5($this->userId);
        $_SESSION['cart_use_coupon'][$userIdent][$shop_id] = $coupon_code;
        kernel::single('base_session')->close();
        return true;

        // $objMdlCartCoupon = app::get('systrade')->model('cart_coupon');
        // $data = array(
        //     'coupon_id' => $coupon_id,
        //     'coupon_code' => $coupon_code,
        //     'user_id' => $user_id,
        //     'shop_id' => $shop_id,
        // );

        // return $objMdlCartCoupon->save($data);
    }

    /**
     * @brief 取消优惠券
     *
     * @param array $data
     *
     * @return array
     */
    public function cancelCouponCart($coupon_code, $shop_id)
    {
        if($coupon_code == '-1')
        {
            kernel::single('base_session')->start();
            $userIdent = $this->objMdlCart->getUserIdentMd5($this->userId);
            unset($_SESSION['cart_use_coupon'][$userIdent][$shop_id]);
            kernel::single('base_session')->close();
            return true;
            // $objMdlCartCoupon = app::get('systrade')->model('cart_coupon');
            // $data = array(
            //     'user_id' => "此处user_id需要重新传入",
            //     'shop_id' => $shop_id,
            // );
            // return $objMdlCartCoupon->delete($data);
        }
        else
        {
            return false;
        }

    }

}

