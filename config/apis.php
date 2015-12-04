<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
    /*
    |--------------------------------------------------------------------------
    | 定义所有luckymall预设的api接口路由
    |--------------------------------------------------------------------------
    |
    | key代表, api method name.
    | rpc::call('method', array($param1, $param2));
    |
     */
    'routes' => array(
        /*
         *=======================================
         *  售后服务API
         *=======================================
         */
        //创建售后服务
        'aftersales.apply' => ['uses' => 'sysaftersales_api_apply@create', 'version'=>['v1']],
        //获取单个售后详情
        'aftersales.get' => ['uses' => 'sysaftersales_api_info@getData', 'version'=>['v1']],
        'aftersales.get.bn' => ['uses' => 'sysaftersales_api_infobn@getData', 'version'=>['v1']],
        //获取售后列表
        'aftersales.list.get' => ['uses' => 'sysaftersales_api_list@getData', 'version'=>['v1']],
        //根据子订单编号，验证该组子订单号是否可以申请售后过售后
        'aftersales.verify' => ['uses' => 'sysaftersales_api_verify@verify', 'version'=>['v1']],
        //商家审核售后服务
        'aftersales.check' => ['uses' => 'sysaftersales_api_check@check', 'version'=>['v1']],
        //平台对退款申请进行驳回处理
        'aftersales.refunds.reject' => ['uses' => 'sysaftersales_api_refunds_reject@reject', 'version'=>['v1']],
        //平台对退款申请进行退款处理
        'aftersales.refunds.restore' => ['uses' => 'sysaftersales_api_refunds_restore@restore', 'version'=>['v1']],
        //消费者回寄退货物流信息
        'aftersales.send.back' => ['uses' => 'sysaftersales_api_sendBack@send', 'version'=>['v1']],
        //消费者申请换货，商家确认收到回寄商品，进行重新进行发货
        'aftersales.send.confirm' => ['uses' => 'sysaftersales_api_sendConfirm@send', 'version'=>['v1']],

        /*
         *=======================================
         *  交易相关API
         *=======================================
         */
        //获取单笔订单
        'trade.get' => ['uses' => 'systrade_api_getTradeInfo@getData', 'version'=>['v1']],
        //商家获取单笔订单的数据
        'trade.shop.get' => ['uses' => 'systrade_api_getTradeInfoByShop@getData', 'version'=>['v1'], 'oauth'=>true],
        //获取单笔子订单交易信息
        'trade.order.get' => ['uses' => 'systrade_api_getOrderInfo@getData', 'version'=>['v1']],
        //获取多条子订单列表信息
        'trade.order.list.get' => ['uses' => 'systrade_api_getOrderList@getData', 'version'=>['v1']],

        //商家在用户已付款未发货的情况下，申请取消异常订单
        'trade.abnormal.create' => ['uses' => 'systrade_api_tradeAbnormal_create@create', 'version'=>['v1']],
        //私有的API，不对外公开 平台审核异常订单
        //'trade.abnormal.check' => ['uses' => 'systrade_api_tradeAbnormal_check@check', 'version'=>['v1']],
        //获取单条异常订单的详情
        'trade.abnormal.get' => ['uses' => 'systrade_api_tradeAbnormal_info@getData', 'version'=>['v1']],
        //获取异常订单列表
        'trade.abnormal.list.get' => ['uses' => 'systrade_api_tradeAbnormal_list@getData', 'version'=>['v1']],
        //获取订单金额
        'trade.money.get' => ['uses' => 'systrade_api_tradeMoney@getList', 'version'=>['v1']],
        // 统计会员使用某促销的次数
        'trade.promotion.applynum' => ['uses' => 'systrade_api_countPromotion@countPromotion', 'version'=>['v1']],
        // 更新优惠券到购物车
        'trade.cart.cartCouponAdd' => ['uses' => 'systrade_api_cartCouponAdd@cartCouponAdd', 'version'=>['v1']],
        // 取消结算页使用优惠券
        'trade.cart.cartCouponCancel' => ['uses' => 'systrade_api_cartCouponCancel@cartCouponCancel', 'version'=>['v1']],
        // 获取购物车信息
        'trade.cart.getCartInfo' => ['uses' => 'systrade_api_cart_getCartInfo@getCartInfo', 'version'=>['v1']],
        // 获取简单购物车信息
        'trade.cart.getBasicCartInfo' => ['uses' => 'systrade_api_cart_getBasicCartInfo@getBasicCartInfo', 'version'=>['v1']],
        // 获取购物车商品数量
        'trade.cart.getCount' => ['uses' => 'systrade_api_cart_getCount@getCount', 'version'=>['v1']],
        //子订单售后状态更新
        'order.aftersales.status.update' => ['uses' => 'systrade_api_order_status@update', 'version'=>['v1']],

        //买家对订单发起投诉
        'trade.order.complaints.create' => ['uses' => 'systrade_api_complaints_create@create', 'version'=>['v1']],
        //平台对订单投诉同步处理结果
        'trade.order.complaints.process' => ['uses' => 'systrade_api_complaints_process@process', 'version'=>['v1']],
        //根据自订单号获取单个订单投诉详情
        'trade.order.complaints.info' => ['uses' => 'systrade_api_complaints_info@get', 'version'=>['v1']],
        //买家撤销投诉
        'trade.order.complaints.buyer.close' => ['uses' => 'systrade_api_complaints_buyerClose@close', 'version'=>['v1']],

        //获取订单列表
        'trade.get.list' => ['uses' => 'systrade_api_trade_list@tradeList', 'version'=>['v1']],

        //商家获取订单列表
        'trade.get.shop.list' => ['uses' => 'systrade_api_trade_listByShop@tradeList', 'version'=>['v1'], 'oauth'=>true],
        //订单取消状态更改
        'trade.cancel' => ['uses' => 'systrade_api_trade_cancel@cancelTrade', 'version'=>['v1']],
        //订单完成状态更改
        'trade.confirm' => ['uses' => 'systrade_api_trade_confirm@confirmTrade', 'version'=>['v1']],
        //订单价格调整
        'trade.update.price' => ['uses' => 'systrade_api_trade_updatePrice@tradePriceUpdate', 'version'=>['v1']],
        //订单发货状态更改
        'trade.delivery' => ['uses' => 'systrade_api_trade_delivery@deliveryTrade', 'version'=>['v1']],
        //订单支付状态更改
        'trade.pay.finish' => ['uses' => 'systrade_api_trade_payFinish@tradePay', 'version'=>['v1']],

        //购物车数据删除
        'trade.cart.delete' => ['uses' => 'systrade_api_cart_deleteCart@deleteCart', 'version'=>['v1']],
        //购物车数据更新
        'trade.cart.update' => ['uses' => 'systrade_api_cart_updateCart@updateCart', 'version'=>['v1']],
        //购物车数据增加
        'trade.cart.add' => ['uses' => 'systrade_api_cart_addCart@addCart', 'version'=>['v1']],
        //订单创建
        'trade.create' => ['uses' => 'systrade_api_trade_create@createTrade', 'version'=>['v1']],
        //计算订单金额（包含运费）
        'trade.price.total' => ['uses' => 'systrade_api_trade_totalPrice@total', 'version'=>['v1']],
        //计算订单数量
        'trade.count' => ['uses' => 'systrade_api_trade_count@tradeCount', 'version'=>['v1']],
        //未评价订单统计
        'trade.notrate.count' => ['uses' => 'systrade_api_trade_notRateCount@count', 'version'=>['v1']],
        //商家添加订单备注
        'trade.add.memo' => ['uses' => 'systrade_api_trade_addMemo@add', 'version'=>['v1']],
        //用户购买记录
        'trade.user.buyerList' => ['uses' => 'systrade_api_getUserBuyerList@get', 'version' => ['v1']],

        //订单状态改变
        'trade.update.delivery.status' => ['uses' => 'systrade_api_tradeDelivery@doDelivery', 'version' => ['v1']],
        //订单发货
        'logistics.trade.delivery' => ['uses' => 'syslogistics_api_tradeDelivery@tradeDelivery', 'version' => ['v1']],

        //erp发货时，订单发货状态改变
        'trade.shop.update.delivery.status' => ['uses' => 'systrade_api_tradeDeliveryForOpen@doDelivery', 'version' => ['v1'], 'oauth'=>true],
        //erp联通后订单发货
        'logistics.shop.trade.delivery' => ['uses' => 'syslogistics_api_tradeDeliveryForOpen@tradeDelivery', 'version' => ['v1'], 'oauth'=>true],

        //当线下支付时，商家有权进行确认收款和确认收货
        'trade.moneyAndGoods.receipt' => ['uses' => 'systrade_api_trade_moneyAndGoods@receipt', 'version' => ['v1'], 'oauth'=>true],

        /*
         *  商品相关API
         *=======================================
         */
        //订单取消时恢复库存
        'item.store.recover' => ['uses' => 'sysitem_api_item_recoverStore@storeRecover', 'version'=>['v1']],
        //下单或支付时扣减库存
        'item.store.minus' => ['uses' => 'sysitem_api_item_minusStore@storeMinus', 'version'=>['v1']],
        //商家通过bn修改商品库存
        'item.shop.store.update' => ['uses' => 'sysitem_api_item_updateStore@updateStore', 'version'=>['v1'], 'oauth'=>true],
        //修改商品销量
        'item.updateSoldQuantity' => ['uses' => 'sysitem_api_updateSoldQuantity@updateSoldQuantity', 'version'=>['v1']],
        //修改评论数量
        'item.updateRateQuantity' => ['uses' => 'sysitem_api_updateRateQuantity@update', 'version'=>['v1']],
        //获取单个商品详细信息
        'item.get' => ['uses' => 'sysitem_api_item_get@get', 'version'=>['v1']],
        //商品搜索
        'item.search' => ['uses' => 'sysitem_api_item_search@getList', 'version'=>['v1']],
        //获取指定商品的货品
        'item.sku.list' => ['uses' => 'sysitem_api_item_getSkuList@getList', 'version'=>['v1']],

        //搜索商品给出渐进式的筛选项
        'item.search.filterItems' => ['uses' => 'sysitem_api_search_filterItems@get', 'version'=>['v1']],

        'item.promotion.addTag' => ['uses' => 'sysitem_api_promotion_itemPromotionTagAdd@itemPromotionTagAdd', 'version'=>['v1']],
        'item.promotion.deleteTag' => ['uses' => 'sysitem_api_promotion_itemPromotionTagDelete@itemPromotionTagDelete', 'version'=>['v1']],
        'item.promotion.getTag' => ['uses' => 'sysitem_api_promotion_itemPromotionTagGet@itemPromotionTagGet', 'version'=>['v1']],

        // 更新商品促销标签
        'item.promotiontag.update' => ['uses' => 'sysitem_api_itemPromotionTagUpdate@itemPromotionTagUpdate', 'version'=>['v1']],
        // 删除商品的某个促销标签
        'item.promotiontag.delete' => ['uses' => 'sysitem_api_itemPromotionTagDelete@itemPromotionTagDelete', 'version'=>['v1']],
        // 获取商品的促销标签及促销信息
        'item.promotiontag.get' => ['uses' => 'sysitem_api_itemPromotionTagGet@itemPromotionTagGet', 'version'=>['v1']],
        //统计商品的数量
        'item.count' => ['uses' => 'sysitem_api_item_count@itemCount', 'version'=>['v1']],
        //获取商品统计数据
        'item.get.count' => ['uses' => 'sysitem_api_item_getCount@get', 'version'=>['v1']],
        //商品添加
        'item.create' => ['uses' => 'sysitem_api_item_create@itemCreate', 'version'=>['v1']],
        //商品上下架状态修改
        'item.sale.status' => ['uses' => 'sysitem_api_item_updateStatus@updateStatus', 'version'=>['v1']],
        'item.delete' => ['uses' => 'sysitem_api_item_delete@itemDelete', 'version'=>['v1']],
        //获取商品的自然属性
        'item.get.nature.prop' =>['uses' => 'sysitem_api_itemNatureProp@getItemNatutrProp','version'=>['v1']],
        //获取通过bn货品的id
        'item.sku.get.sku_id.bybn' =>['uses' => 'sysitem_api_sku_getSkuIdByBn@getIds','version'=>['v1']],


        /*
         *=======================================
         *  评价系统相关API
         *=======================================
         */
        //新增订单评论，包含多个子订单的评论一起,店铺评分
        'rate.add' => ['uses' => 'sysrate_api_add@add', 'version'=>['v1']],
        //删除评价
        'rate.delete' => ['uses' => 'sysrate_api_delete@del', 'version'=>['v1']],
        //商家申诉修改评价成功
        'rate.update' => ['uses' => 'sysrate_api_update@update', 'version'=>['v1']],
        //获取单条评论详情
        'rate.get' => ['uses' => 'sysrate_api_info@getData', 'version'=>['v1']],
        //获取评论列表，支持分页
        'rate.list.get' => ['uses' => 'sysrate_api_list@getData', 'version'=>['v1']],
        //商家解释，回复评论
        'rate.reply.add' => ['uses' => 'sysrate_api_reply@add', 'version'=>['v1']],
        //将评论的实名修改为匿名，但是修改为匿名之后则不能再次修改为实名
        'rate.set.anony' => ['uses' => 'sysrate_api_anony@set', 'version'=>['v1']],

        //商家对评论进行申诉
        'rate.appeal.add' => ['uses' => 'sysrate_api_appeal_add@add', 'version'=>['v1']],
        //平台对商家评论申诉进行审核
        'rate.appeal.check' => ['uses' => 'sysrate_api_appeal_check@check', 'version'=>['v1']],

        //获取店铺动态评分
        'rate.dsr.get' => ['uses' => 'sysrate_api_dsr_get@getData', 'version'=>['v1']],

        'feedback.add' => ['uses' =>'sysrate_api_addFeedback@doSave', 'version'=>['v1']],
        //评分统计
        'rate.count' => ['uses' => 'sysrate_api_countRate@countRate','version' =>['v1']],

        //获取咨询列表，支持分页
        'rate.gask.list' =>['uses'=>'sysrate_api_consultation_list@getData','version'=>['v1']],
//获取咨询列表，支持分页
'shoppubt.gask.list' =>['uses'=>'sysshoppubt_api_list@getData','version'=>['v1']],
        //删除商品咨询或者回复
        'rate.gask.delete' =>['uses'=>'sysrate_api_consultation_delete@deleteConsultation','version'=>['v1']],
        //商品咨询新增
        'rate.gask.create' =>['uses'=>'sysrate_api_consultation_create@create','version'=>['v1']],
        //统计咨询
        'rate.gask.count' =>['uses'=>'sysrate_api_consultation_count@countAsk','version'=>['v1']],

        //统计咨询
        'shoppubt.gask.count' =>['uses'=>'sysshoppubt_api_count@countAsk','version'=>['v1']],
        //商家回复咨询
        'rate.gask.reply' =>['uses'=>'sysrate_api_consultation_reply@doReply','version'=>['v1']],
        'rate.gask.display' =>['uses'=>'sysrate_api_consultation_update@update','version'=>['v1']],
//交易咨询新增
        'sysshoppubt.gask.create' =>['uses'=>'sysshoppubt_api_create@create','version'=>['v1']],
        /*
         *=======================================
         *  会员相关
         *=======================================
         */
        //更新会员的积分总额
        'user.updateUserPoint' => ['uses' => 'sysuser_api_point_update@updateUserPoint', 'version' => ['v1']],
        //更新会员的成长值总额
        'user.updateUserExp' => ['uses' => 'sysuser_api_exp_update@updateUserExp', 'version' => ['v1']],
        //获取积分记录列表
        'user.pointGet' => ['uses' => 'sysuser_api_point_list@getList', 'version' => ['v1']],
        //获取成长值记录列表
        'user.experienceGet' => ['uses' => 'sysuser_api_exp_list@getList', 'version' => ['v1']],
        //获取等级列表
        'user.grade.fullinfo' => ['uses' => 'sysuser_api_grade_fullinfo@fullinfo', 'version' => ['v1']],
        // 获取会员基本等级信息
        'user.grade.basicinfo' => ['uses' => 'sysuser_api_grade_basicinfo@basicinfo', 'version' => ['v1']],
        // 获取系统会员等级列表
        'user.grade.list' => ['uses' => 'sysuser_api_grade_list@gradeList', 'version' => ['v1']],
        //获取等级列表
        'user.pointcount' => ['uses' => 'sysuser_api_point_count@count', 'version' => ['v1']],
        //获取用户的优惠券列表
        'user.coupon.list' => ['uses' => 'sysuser_api_couponList@couponList', 'version' => ['v1']],
        //获取用户领取的优惠券信息
        'user.coupon.get' => ['uses' => 'sysuser_api_couponGet@couponGet', 'version' => ['v1']],
        // 取消订单返还优惠券
        'user.coupon.back' => ['uses' => 'sysuser_api_couponBack@couponBack', 'version' => ['v1']],
        //领取优惠券
        'user.coupon.getCode' => ['uses' => 'sysuser_api_getCoupon@getCoupon', 'version' => ['v1']],
        // 删除用户的优惠券
        'user.coupon.remove' => ['uses' => 'sysuser_api_couponDelete@couponDelete', 'version' => ['v1']],
        // 更新会员的优惠券信息
        'user.coupon.useLog' => ['uses' => 'sysuser_api_couponUseLog@couponUseLog', 'version' => ['v1']],
        //获取商品收藏列表
        'user.itemcollect.list' => ['uses' => 'sysuser_api_getItemCollectList@getItemCollectList', 'version' => ['v1']],
        'user.itemcollect.count' => ['uses' => 'sysuser_api_countCollectItem@getCount', 'version' => ['v1']],
        //商品收藏添加
        'user.itemcollect.add' => ['uses' => 'sysuser_api_addCollectItem@addItemCollect', 'version' => ['v1']],
        //商品收藏删除
        'user.itemcollect.del' => ['uses' => 'sysuser_api_delCollectItem@delItemCollect', 'version' => ['v1']],
        //获取商品收藏列表
        'user.shopcollect.list' => ['uses' => 'sysuser_api_getShopCollectList@getShopCollectList', 'version' => ['v1']],
        'user.shopcollect.count' => ['uses' => 'sysuser_api_countCollectShop@getCount', 'version' => ['v1']],
        //店铺收藏添加
        'user.shopcollect.add' => ['uses' => 'sysuser_api_addCollectShop@addCollectShop', 'version' => ['v1']],
        //店铺收藏删除
        'user.shopcollect.del' => ['uses' => 'sysuser_api_delCollectShop@delCollectShop', 'version' => ['v1']],
        //会员地址添加
        'user.address.add' => ['uses' => 'sysuser_api_addUserAdress@addUserAdress', 'version' => ['v1']],
        //会员地址默认设置
        'user.address.setDef' => ['uses' => 'sysuser_api_addressSetDef@addressSetDef', 'version' => ['v1']],
        //删除会员地址
        'user.address.del' => ['uses' => 'sysuser_api_delUserAddress@delUserAddress', 'version' => ['v1']],
        //获取会员目前地址数量和地址最大限制数量
        'user.address.count' => ['uses' => 'sysuser_api_getAddrCount@getAddrCount', 'version' => ['v1']],
        //获取会员地址列表
        'user.address.list' => ['uses' => 'sysuser_api_getAddrList@getAddrList', 'version' => ['v1']],
        'user.address.info' => ['uses' => 'sysuser_api_getAddrInfo@getAddrInfo', 'version' => ['v1']],
        //会员添加
        'user.create' => ['uses' => 'sysuser_api_user_create@add', 'version' => ['v1']],

        //获取用户登录信息
        'user.get.account.info' => ['uses' => 'sysuser_api_user_account_getInfo@get', 'version' => ['v1']],
        //根据会员ID获取对应的用户名
        'user.get.account.name' => ['uses' => 'sysuser_api_user_account_getName@getName', 'version' => ['v1']],

        // 信任登陆绑定
        'user.trust.authorize' => ['uses' => 'sysuser_api_user_trust_authorize@authorize', 'version' => ['v1']],
        //

        //获取用户详细信息
        'user.get.info' => ['uses' => 'sysuser_api_user_getUserInfo@getList', 'version' => ['v1']],

        //用户基本信息更新
        'user.basics.update' => ['uses' => 'sysuser_api_user_basicsUpdate@update', 'version' => ['v1']],
        //用户密码修改
        'user.pwd.update' => ['uses' => 'sysuser_api_user_account_updatePwd@passwordUpdate', 'version' => ['v1']],
        //修改用户登录信息
        'user.account.update' => ['uses' => 'sysuser_api_user_account_updateAccount@accountUpdate', 'version' => ['v1']],
//      //用户是否登陆状态
//      'user.check' => ['uses' => 'sysuser_api_user_account_check@check', 'version' => ['v1']],
        //用户登录
        'user.login' => ['uses' => 'sysuser_api_user_account_login@userLogin', 'version' => ['v1']],
        //用户退出
        //'user.logout' => ['uses' => 'sysuser_api_user_account_logout@userLogout', 'version' => ['v1']],
        //检测会员登录密码
        'user.login.pwd.check' => ['uses' => 'sysuser_api_user_account_checkLoginPwd@checkPwd', 'version' => ['v1']],
        //邮箱验证
        'user.email.verify' => ['uses' => 'sysuser_api_user_verifyEmail@verifyEmail', 'version' => ['v1']],
        //邮件订阅
        'user.notifyitem' => ['uses' => 'sysuser_api_addUserNotifyItem@addUserNotifyItem', 'version' => ['v1']],
        'user.updatenotifyitem' => ['uses' => 'sysuser_api_updateUserNotifyItem@updateUserNotifyItem', 'version' => ['v1']],
        'user.notifyItemList' => ['uses' => 'sysuser_api_getUserNotifyItemList@getUserNotifyItemList', 'version' => ['v1']],



        /*
         *=======================================
         *类目相关
         *=======================================
         */
        //获取类目单条信息
        'category.cat.get.info' => ['uses' => 'syscategory_api_cat_getinfo@getList', 'version' => ['v1']],
        //获取指定一级类目以及他的二三级类目树形结构
        'category.cat.get' => ['uses' => 'syscategory_api_cat_get@getList', 'version' => ['v1']],
        //获取类目列表（所有类目树形结构）
        'category.cat.get.list' => ['uses' => 'syscategory_api_cat_list@getList', 'version' => ['v1']],
        //类目删除
        'category.cat.remove' => ['uses' => 'syscategory_api_cat_remove@toRemove', 'version' => ['v1']],
        //获取品牌详情
        'category.brand.get.info' => ['uses' => 'syscategory_api_brand_getInfo@get', 'version' => ['v1']],
        //获取品牌列表
        'category.brand.get.list' => ['uses' => 'syscategory_api_brand_getList@get', 'version' => ['v1']],
        //运营商品牌添加
        'category.brand.add' => ['uses' => 'syscategory_api_brand_add@addBrand', 'version' => ['v1']],
        //运营商品牌修改
        'category.brand.update' => ['uses' => 'syscategory_api_brand_update@updateBrand', 'version' => ['v1']],
        //获取指定店铺或者指定类目关联的品牌(cat_id 必填)
        'category.get.cat.rel.brand' => ['uses' => 'syscategory_api_getCatRelBrand@getData', 'version' => ['v1']],

        //获取指定的三级类目关联的属性
        'category.catprovalue.get' => ['uses' => 'syscategory_api_getCatProValue@getCatProValue', 'version' => ['v1']],
        //获取属性列表
        'category.prop.list' => ['uses' => 'syscategory_api_getPropList@getList', 'version' => ['v1']],


        /*
         *=======================================
         *店铺相关
         *=======================================
         */
        //获取店铺签约的类目和品牌的id（只对内）
        'shop.authorize.catbrandids.get' => ['uses' => 'sysshop_api_shopAuthorize@getCatBrand', 'version' => ['v1']],
        //获取店铺自有类目
        'shop.cat.get' => ['uses' => 'sysshop_api_getShopCat@getShopCat', 'version' => ['v1']],
        //获取店铺基本信息
        'shop.get' => ['uses' => 'sysshop_api_shop_get@get', 'version' => ['v1']],
        //根据店铺名称查询店铺列表数据
        'shop.get.search' => ['uses' => 'sysshop_api_shop_search@getList', 'version' => ['v1']],
        //根据店铺ID获取店铺列表数据
        'shop.get.list' => ['uses' => 'sysshop_api_shop_list@getList', 'version' => ['v1']],
        //获取店铺详细信息
        'shop.get.detail' => ['uses' => 'sysshop_api_shop_detail@getList', 'version' => ['v1']],
        //获取店铺签约的所有类目费率
        'shop.get.cat.fee' => ['uses' => 'sysshop_api_shop_getCatFee@getCatFee', 'version' => ['v1']],
        //批量获取店铺名称
        'shop.get.shopname' => ['uses' => 'sysshop_api_shop_getName@getList', 'version' => ['v1']],
        //更新店铺基本信息
        'shop.update' => ['uses' => 'sysshop_api_shop_update@update', 'version' => ['v1']],
        //保存店铺通知
        'shop.savenotice' => ['uses' => 'sysshop_api_shop_saveNotice@saveNotice', 'version' => ['v1']],
        //获取店铺通知一条数据
        'shop.get.shopnoticeinfo' => ['uses' => 'sysshop_api_shop_getNoticeInfo@getNoticeInfo', 'version' => ['v1']],
        //获取店铺通数据
        'shop.get.shopnoticelist' => ['uses' => 'sysshop_api_shop_getNoticeList@getNoticeList', 'version' => ['v1']],
        //获取店铺签约品牌
        //'shop.authorize.brand' => ['uses' => 'sysshop_api_getShopAuthorizeBrand@getAuthorizeBrand', 'version' => ['v1']],
        //获取店铺签约类目
        'shop.authorize.cat' => ['uses' => 'sysshop_api_getShopAuthorizeCat@getAuthorizeCat', 'version' => ['v1']],
        //获取当前卖家的店铺id
        'shop.get.loginId' => ['uses' => 'sysshop_api_getShopId@getSellerShopId', 'version' => ['v1']],
        // by  litong
        'shop.get.shopInfo' =>['uses'=>'sysshop_api_account_getShopInfo@getShopInfo','varsion'=>['v1']],
        'user.get.userInfo' =>['uses'=>'sysuser_api_account_getUserInfo@getUserInfo','varsion'=>['v1']],
        #LT
        'seller.get.sellertype' =>['uses'=>'sysshop_api_account_getSellerType@getSellerType','varsion'=>['v1']],
        /*
         *=======================================
         *店铺入驻相关
         *=======================================
         */
        //获取入驻申请信息
        'shop.get.enterapply' => ['uses' => 'sysshop_api_enterapply_get@get', 'version' => ['v1']],
        //入驻申请创建
        'shop.create.enterapply' => ['uses' => 'sysshop_api_enterapply_create@create', 'version' => ['v1']],
        //入驻申请修改
        'shop.update.enterapply' => ['uses' => 'sysshop_api_enterapply_update@update', 'version' => ['v1']],
        //检测该品牌是否以后店铺签约为旗舰店
        'shop.check.brand.sign' => ['uses' => 'sysshop_api_enterapply_getSignBrand@getSignBrand', 'version' => ['v1']],
        'shop.type.get' => ['uses' => 'sysshop_api_getShopType@getList', 'version' => ['v1']],


        /*
         *=======================================
         *营销相关
         *=======================================
         */
        // 获取各促销信息的中转api
        'promotion.promotion.get' => ['uses' => 'syspromotion_api_promotionGet@promotionGet', 'version' => ['v1']],
        // 优惠券接口
        'promotion.coupon.add' => ['uses' => 'syspromotion_api_coupon_couponAdd@couponAdd', 'version' => ['v1']],
        'promotion.coupon.update' => ['uses' => 'syspromotion_api_coupon_couponUpdate@couponUpdate', 'version' => ['v1']],
        'promotion.coupon.delete' => ['uses' => 'syspromotion_api_coupon_couponDelete@couponDelete', 'version' => ['v1']],
        'promotion.coupon.get' => ['uses' => 'syspromotion_api_coupon_couponGet@couponGet', 'version' => ['v1']],
        'promotion.coupon.list' => ['uses' => 'syspromotion_api_coupon_couponList@couponList', 'version' => ['v1']],
        'promotion.coupon.gencode' => ['uses' => 'syspromotion_api_coupon_couponGenCode@couponGenCode', 'version' => ['v1']],
        'promotion.coupon.use' => ['uses' => 'syspromotion_api_coupon_couponUse@couponUse', 'version' => ['v1']],// 结算页使用优惠券
        'promotion.coupon.apply' => ['uses' => 'syspromotion_api_coupon_couponApply@couponApply', 'version' => ['v1']],
        'promotion.couponitem.list' => ['uses' => 'syspromotion_api_coupon_couponItemList@couponItemList', 'version' => ['v1']],
        'promotion.coupon.list.byid' => ['uses' => 'syspromotion_api_coupon_couponListById@getList', 'version' => ['v1']],
        // 满减接口
        'promotion.fullminus.add' => ['uses' => 'syspromotion_api_fullminus_fullminusAdd@fullminusAdd', 'version' => ['v1']],
        'promotion.fullminus.update' => ['uses' => 'syspromotion_api_fullminus_fullminusUpdate@fullminusUpdate', 'version' => ['v1']],
        'promotion.fullminus.delete' => ['uses' => 'syspromotion_api_fullminus_fullminusDelete@fullminusDelete', 'version' => ['v1']],
        'promotion.fullminus.get' => ['uses' => 'syspromotion_api_fullminus_fullminusGet@fullminusGet', 'version' => ['v1']],
        'promotion.fullminus.list' => ['uses' => 'syspromotion_api_fullminus_fullminusList@fullminusList', 'version' => ['v1']],
        'promotion.fullminus.apply' => ['uses' => 'syspromotion_api_fullminus_fullminusApply@fullminusApply', 'version' => ['v1']],
        'promotion.fullminusitem.list' => ['uses' => 'syspromotion_api_fullminus_fullminusItemList@fullminusItemList', 'version' => ['v1']],
        // 满折接口
        'promotion.fulldiscount.add' => ['uses' => 'syspromotion_api_fulldiscount_fulldiscountAdd@fulldiscountAdd', 'version' => ['v1']],
        'promotion.fulldiscount.update' => ['uses' => 'syspromotion_api_fulldiscount_fulldiscountUpdate@fulldiscountUpdate', 'version' => ['v1']],
        'promotion.fulldiscount.delete' => ['uses' => 'syspromotion_api_fulldiscount_fulldiscountDelete@fulldiscountDelete', 'version' => ['v1']],
        'promotion.fulldiscount.get' => ['uses' => 'syspromotion_api_fulldiscount_fulldiscountGet@fulldiscountGet', 'version' => ['v1']],
        'promotion.fulldiscount.list' => ['uses' => 'syspromotion_api_fulldiscount_fulldiscountList@fulldiscountList', 'version' => ['v1']],
        'promotion.fulldiscount.apply' => ['uses' => 'syspromotion_api_fulldiscount_fulldiscountApply@fulldiscountApply', 'version' => ['v1']],
        'promotion.fulldiscountitem.list' => ['uses' => 'syspromotion_api_fulldiscount_fulldiscountItemList@fulldiscountItemList', 'version' => ['v1']],
        // 免邮接口
        'promotion.freepostage.add' => ['uses' => 'syspromotion_api_freepostage_freepostageAdd@freepostageAdd', 'version' => ['v1']],
        'promotion.freepostage.update' => ['uses' => 'syspromotion_api_freepostage_freepostageUpdate@freepostageUpdate', 'version' => ['v1']],
        'promotion.freepostage.delete' => ['uses' => 'syspromotion_api_freepostage_freepostageDelete@freepostageDelete', 'version' => ['v1']],
        'promotion.freepostage.get' => ['uses' => 'syspromotion_api_freepostage_freepostageGet@freepostageGet', 'version' => ['v1']],
        'promotion.freepostage.list' => ['uses' => 'syspromotion_api_freepostage_freepostageList@freepostageList', 'version' => ['v1']],
        'promotion.freepostage.apply' => ['uses' => 'syspromotion_api_freepostage_freepostageApply@freepostageApply', 'version' => ['v1']],
        'promotion.freepostageitem.list' => ['uses' => 'syspromotion_api_freepostage_freepostageItemList@freepostageItemList', 'version' => ['v1']],
        // X件Y折接口
        'promotion.xydiscount.add' => ['uses' => 'syspromotion_api_xydiscount_xydiscountAdd@xydiscountAdd', 'version' => ['v1']],
        'promotion.xydiscount.update' => ['uses' => 'syspromotion_api_xydiscount_xydiscountUpdate@xydiscountUpdate', 'version' => ['v1']],
        'promotion.xydiscount.delete' => ['uses' => 'syspromotion_api_xydiscount_xydiscountDelete@xydiscountDelete', 'version' => ['v1']],
        'promotion.xydiscount.get' => ['uses' => 'syspromotion_api_xydiscount_xydiscountGet@xydiscountGet', 'version' => ['v1']],
        'promotion.xydiscount.list' => ['uses' => 'syspromotion_api_xydiscount_xydiscountList@xydiscountList', 'version' => ['v1']],
        'promotion.xydiscount.apply' => ['uses' => 'syspromotion_api_xydiscount_xydiscountApply@xydiscountApply', 'version' => ['v1']],
        'promotion.xydiscountitem.list' => ['uses' => 'syspromotion_api_xydiscount_xydiscountItemList@xydiscountItemList', 'version' => ['v1']],

        //获取参与活动的商品列表
        'promotion.activity.item.list' => ['uses' => 'syspromotion_api_activity_itemList@getList', 'version' => ['v1']],
        //获取参与活动的商品详情
        'promotion.activity.item.info' => ['uses' => 'syspromotion_api_activity_itemInfo@getInfo', 'version' => ['v1']],
        //获取活动列表
        'promotion.activity.list' => ['uses' => 'syspromotion_api_activity_list@getList', 'version' => ['v1']],
        //获取活动详情
        'promotion.activity.info' => ['uses' => 'syspromotion_api_activity_info@getInfo', 'version' => ['v1']],
        // 报名活动
        'promotion.activity.register' => ['uses' => 'syspromotion_api_activity_registerActivity@registerActivity', 'version' => ['v1']],
        // 报名列表
        'promotion.activity.register.list' => ['uses' => 'syspromotion_api_activity_registerList@registerList', 'version' => ['v1']],
        // 活动报名审核
        'promotion.activity.register.approve' => ['uses' => 'syspromotion_api_activity_registerApprove@registerApprove', 'version' => ['v1']],


        /*
         *=======================================
         *营销相关
         *=======================================
         */
        //修改支付单中的应支付的金额
        'payment.money.update' => ['uses' => 'ectools_api_paymentMoney@update', 'version' => ['v1']],
        //获取支付方式列表
        'payment.get.list' => ['uses' => 'ectools_api_getPayments@getList', 'version' => ['v1']],
        //获取支付方式的配置信息
        'payment.get.conf' => ['uses' => 'ectools_api_getPaymentConf@getConf', 'version' =>['v1']],
        //获取支付单信息
        'payment.bill.get' => ['uses' => 'ectools_api_getPaymentBill@getInfo', 'version' => ['v1']],
        //支付单创建
        'payment.bill.create' => ['uses' => 'ectools_api_payment_createBill@create', 'version' => ['v1']],
        //订单支付请求支付网关
        'payment.trade.pay' => ['uses' => 'ectools_api_payment_pay@doPay', 'version' => ['v1']],
        //创建并完成支付单
        'payment.trade.payandfinish' => ['uses' => 'ectools_api_payment_payAndFinish@payAndFinish', 'version' => ['v1']],
        //退款单创建
        'refund.create' => ['uses' => 'ectools_api_refund_create@create', 'version' => ['v1']],

        /*
         *=======================================
         *统计相关
         *=======================================
         */
        //商家统计数据
        'sysstat.data.get' => ['uses' => 'sysstat_api_getStatData@getStatData', 'version' => ['v1']],
        //获取商家统计时间
        'sysstat.datatime.get' => ['uses' => 'sysstat_api_getPageTime@getPageTime', 'version' => ['v1']],
        //获取指定时间商家的统计数据
        'stat.trade.data.count.get' => ['uses' => 'sysstat_api_getTradeDataCount@getTradeInfo', 'version' => ['v1']],

        /*
         *=======================================
         *物流及运费和运费模板
         *=======================================
         */

        //获取物流公司列表
        'logistics.dlycorp.get.list' => ['uses' => 'syslogistics_api_dlycorp_getlist@getList', 'version' => ['v1']],
        'logistics.dlycorp.get' => ['uses' => 'syslogistics_api_dlycorp_get@getList', 'version' => ['v1']],
        //获取运费模板（根据店铺id）
        'logistics.dlytmpl.get.list' => ['uses' => 'syslogistics_api_dlytmpl_getlist@getList', 'version' => ['v1']],
        'logistics.dlytmpl.get' => ['uses' => 'syslogistics_api_dlytmpl_get@getList', 'version' => ['v1']],
        'logistics.dlytmpl.add' => ['uses' => 'syslogistics_api_dlytmpl_add@create', 'version' => ['v1']],
        'logistics.dlytmpl.update' => ['uses' => 'syslogistics_api_dlytmpl_update@update', 'version' => ['v1']],
        'logistics.dlytmpl.delete' => ['uses' => 'syslogistics_api_dlytmpl_delete@delete', 'version' => ['v1']],
        //计算运费（根据运费模板）
        'logistics.fare.count' => ['uses' => 'syslogistics_api_fare@countFare', 'version' => ['v1']],
        //获取地区数据
        'logistics.area' =>['uses' => 'syslogistics_api_getAreaList@getList', 'version' => ['v1']],
        //发货单创建
        'delivery.create' => ['uses' => 'syslogistics_api_delivery_create@create', 'version' => ['v1']],
        //发货单更新
        'delivery.update' => ['uses' => 'syslogistics_api_delivery_update@update', 'version' => ['v1']],
        //获取华强宝物流跟踪
        'logistics.tracking.get.hqepay' => ['uses' => 'syslogistics_api_getHqepayTracking@getTracking', 'version' => ['v1']],
        'delivery.logistics.tracking.get' => ['uses' => 'syslogistics_api_delivery_tracking@getTracking', 'version' => ['v1']],

        //获取自提点列表
        'logistics.ziti.add' => ['uses' => 'syslogistics_api_ziti_addNew@create','version' => ['v1']],
        'logistics.ziti.list' => ['uses' => 'syslogistics_api_ziti_list@get','version' => ['v1']],
        'logistics.ziti.get' => ['uses' => 'syslogistics_api_ziti_get@get','version' => ['v1']],
        'logistics.ziti.update' => ['uses' => 'syslogistics_api_ziti_update@update','version' => ['v1']],

        /*
         *=======================================
         *文章相关
         *=======================================
         */
        'syscontent.node.get.list' => ['uses' => 'syscontent_api_getNodeList@getNodeList', 'version' => ['v1']],
        'syscontent.content.get.list' => ['uses' => 'syscontent_api_getContentList@getContentList', 'version' => ['v1']],
        'syscontent.content.get.info' => ['uses' => 'syscontent_api_getContentInfo@getContentInfo', 'version' => ['v1']],
        'sysinfo.content.get.list' => ['uses' => 'sysinfo_api_getContentInfo@getContentInfo', 'version' => ['v1']],
        'sysinfo.node.get.list'=> ['uses' => 'sysinfo_api_getNodeList@getNodeList', 'version' => ['v1']],
        'sysinfo.content.get.info' => ['uses' => 'sysinfo_api_getContentInfo@getContentInfo', 'version' => ['v1']],

        /*
         *=======================================
         * 图片相关
         *=======================================
         */
         //获取商家图片列表
         'image.shop.list' => ['uses' => 'image_api_shop_list@get', 'version'=>['v1']],
         //修改图片名称
         'image.shop.upImageName' => ['uses' => 'image_api_shop_upImageName@up', 'version'=>['v1']],
         //数据库中删除图片链接，但是不删除真实图片文件
         'image.delete.imageLink' => ['uses' => 'image_api_deleteImage@delete', 'version'=>['v1']],
         //删除指定回收站的图片，删除图片文件
         //'image.delete.imageFile' => ['uses' => 'image_api_shop_delImgFile@delete', 'version'=>['v1']],
        /*
         *=======================================
         *子帐号，角色相关
         *=======================================
         */
         'account.shop.roles.add' => ['uses' => 'sysshop_api_account_rolesAdd@save', 'version'=>['v1']],
         'account.shop.roles.update' => ['uses' => 'sysshop_api_account_rolesUpdate@update', 'version'=>['v1']],
         'account.shop.roles.list' => ['uses' => 'sysshop_api_account_rolesList@get', 'version'=>['v1']],
         'account.shop.roles.get' => ['uses' => 'sysshop_api_account_rolesInfo@get', 'version'=>['v1']],
         'account.shop.roles.delete' => ['uses' => 'sysshop_api_account_rolesDel@delete', 'version'=>['v1']],

         //对子账号进行操作
         'account.shop.user.add' => ['uses' => 'sysshop_api_account_sellerAdd@save', 'version'=>['v1']],
         'account.shop.user.update' => ['uses' => 'sysshop_api_account_sellerUpdate@update', 'version'=>['v1']],
         'account.shop.user.list' => ['uses' => 'sysshop_api_account_sellerList@get', 'version'=>['v1']],
         'account.shop.user.get' => ['uses' => 'sysshop_api_account_sellerInfo@get', 'version'=>['v1']],
         'account.shop.user.delete' => ['uses' => 'sysshop_api_account_sellerDel@delete', 'version'=>['v1']],

         //oauth需要用到的接口
         'account.shop.oauth.login' => ['uses'=>'sysshop_api_oauth_sellerLogin@login', 'version'=>['v1']],




         //这里给开放接口用的
         //oauth登陆
         'open.oauth.login' => ['uses'=>'sysopen_api_oauth_login@login', 'version'=>['v1']],
         'open.shop.develop.info' => ['uses'=>'sysopen_api_open_shopInfo@get', 'version'=>['v1']],

         'open.shop.develop.conf' => ['uses'=>'sysopen_api_open_getShopConf@get', 'version'=>['v1']],
         'open.shop.develop.setConf' => ['uses'=>'sysopen_api_open_setShopConf@set', 'version'=>['v1']],

         'open.shop.develop.apply' => ['uses'=>'sysopen_api_open_applyForOpen@apply', 'version'=>['v1']],
    ),

    /*
    |--------------------------------------------------------------------------
    | 定义所有luckymall预设的app的api依赖关系
    |--------------------------------------------------------------------------
    |
    | 其实就是哪个app调用那个app的api,这里可以做prism上的权限调配
    | limit_count和limit_seconds是做流量限制的，以后流量限制会调用这里的，现在的话，只能到prism上调整
    |
     */
    'depends'=> array (
        'ectools' => array (
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'ectools' => array ( 'appName' => 'ectools', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'image' => array (
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'pam' => array (
            'sysuser' => array ( 'appName' => 'sysuser', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'sysaftersales' => array (
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'ectools' => array ( 'appName' => 'ectools', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysaftersales' => array ( 'appName' => 'sysaftersales', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysuser' => array ( 'appName' => 'sysuser', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'syscategory' => array (
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syscategory' => array ( 'appName' => 'syscategory', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysitem' => array ( 'appName' => 'sysitem', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'syscontent' => array (
            'syscontent' => array ( 'appName' => 'syscontent', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'sysdecorate' => array (
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysitem' => array ( 'appName' => 'sysitem', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'sysitem' => array (
            'syscategory' => array ( 'appName' => 'syscategory', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syspromotion' => array ( 'appName' => 'syspromotion', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysitem' => array ( 'appName' => 'sysitem', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysuser' => array ( 'appName' => 'sysuser', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'syslogistics' => array (
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syslogistics' => array ( 'appName' => 'syslogistics', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'sysopen' => array (
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'syspromotion' => array (
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syspromotion' => array ( 'appName' => 'syspromotion', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysitem' => array ( 'appName' => 'sysitem', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syscategory' => array ( 'appName' => 'syscategory', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysuser' => array ( 'appName' => 'sysuser', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'sysrate' => array (
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysitem' => array ( 'appName' => 'sysitem', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysrate' => array ( 'appName' => 'sysrate', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'sysshop' => array (
            'syscategory' => array ( 'appName' => 'syscategory', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            
        ),
        'sysstat' => array (
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'systrade' => array (
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syslogistics' => array ( 'appName' => 'syslogistics', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysuser' => array ( 'appName' => 'sysuser', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysitem' => array ( 'appName' => 'sysitem', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syspromotion' => array ( 'appName' => 'syspromotion', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'ectools' => array ( 'appName' => 'ectools', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'sysuser' => array (
            'syspromotion' => array ( 'appName' => 'syspromotion', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'topc' => array (
            'syspromotion' => array ( 'appName' => 'syspromotion', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysitem' => array ( 'appName' => 'sysitem', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysrate' => array ( 'appName' => 'sysrate', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syscategory' => array ( 'appName' => 'syscategory', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syslogistics' => array ( 'appName' => 'syslogistics', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysuser' => array ( 'appName' => 'sysuser', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syscontent' => array ( 'appName' => 'syscontent', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysaftersales' => array ( 'appName' => 'sysaftersales', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'ectools' => array ( 'appName' => 'ectools', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'topm' => array (
            'syspromotion' => array ( 'appName' => 'syspromotion', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysitem' => array ( 'appName' => 'sysitem', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysrate' => array ( 'appName' => 'sysrate', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syscategory' => array ( 'appName' => 'syscategory', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysuser' => array ( 'appName' => 'sysuser', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syslogistics' => array ( 'appName' => 'syslogistics', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syscontent' => array ( 'appName' => 'syscontent', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysaftersales' => array ( 'appName' => 'sysaftersales', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'ectools' => array ( 'appName' => 'ectools', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'topshop' => array (
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysaftersales' => array ( 'appName' => 'sysaftersales', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysuser' => array ( 'appName' => 'sysuser', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syslogistics' => array ( 'appName' => 'syslogistics', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysrate' => array ( 'appName' => 'sysrate', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syscategory' => array ( 'appName' => 'syscategory', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysitem' => array ( 'appName' => 'sysitem', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysstat' => array ( 'appName' => 'sysstat', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysopen' => array ( 'appName' => 'sysopen', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syspromotion' => array ( 'appName' => 'syspromotion', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'image' => array ( 'appName' => 'image', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'toputil' => array (
            'syscategory' => array ( 'appName' => 'syscategory', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
        'openstandard' => array (
            'sysshop' => array ( 'appName' => 'sysshop', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'systrade' => array ( 'appName' => 'systrade', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'syslogistics' => array ( 'appName' => 'syslogistics', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
            'sysitem' => array ( 'appName' => 'sysitem', 'path' => '*', 'limit_count' => 1000, 'limit_seconds' => 60,),
        ),
    ),
);
