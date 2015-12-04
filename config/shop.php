<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 * 企业管理中心菜单定义
 */

return array(
    /*
    |--------------------------------------------------------------------------
    | 企业管理中心之首页
    |--------------------------------------------------------------------------
     */
    'index' => array(
        'label' => '首页',
        'display' => false,
        'shopIndex' => true,
        'action' => 'topshop_ctl_index@index',
        'icon' => 'glyphicon glyphicon-home',
        'menu' => array(
            array('label'=>'首页','display'=>false, 'as'=>'topshop.index', 'action'=>'topshop_ctl_index@index','url'=>'/','method'=>'get'),
            array('label'=>'浏览器检测','display'=>false, 'as'=>'topshop.browserTip', 'action'=>'topshop_ctl_index@browserTip','url'=>'browserTip.html','method'=>'get'),
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | 企业管理中心之交易管理
    |--------------------------------------------------------------------------
     */
    'trade' => array(
        'label' => '交易',
        'display' => true,
        'action' => 'topshop_ctl_trade_list@index',
        'icon' => 'glyphicon glyphicon-stats',
        'menu' => array(
            array('label'=>'发布交易','display'=>true,'as'=>'topshop.trade.index','action'=>'topc_ctl_member_shoppubt@addStandards','url'=>'addStandards.html','method'=>'get'),       
            array('label'=>'订单管理','display'=>false,'as'=>'topshop.trade.index','action'=>'topshop_ctl_trade_list@index','url'=>'list.html','method'=>'get'),
            array('label'=>'订单搜索','display'=>false,'as'=>'topshop.trade.postsearch','action'=>'topshop_ctl_trade_list@search','url'=>'trade/search.html','method'=>'post'),
            array('label'=>'订单搜索','display'=>false,'as'=>'topshop.trade.search','action'=>'topshop_ctl_trade_list@search','url'=>'trade/search.html','method'=>'get'),
            array('label'=>'订单详情','display'=>false,'as'=>'topshop.trade.detail','action'=>'topshop_ctl_trade_detail@index','url'=>'detail.html','method'=>'get'),
            array('label'=>'订单物流','display'=>false,'as'=>'topshop.trade.detail.logi','action'=>'topshop_ctl_trade_detail@ajaxGetLogi','url'=>'detail.html','method'=>'post'),
            array('label'=>'添加订单备注','display'=>false,'as'=>'topshop.trade.detail.memo','action'=>'topshop_ctl_trade_detail@setTradeMemo','url'=>'setMemo.html','method'=>'post'),
            array('label'=>'订单取消','display'=>false,'as'=>'topshop.trade.postclose','action'=>'topshop_ctl_trade_list@closeTrade','url'=>'close.html','method'=>'post'),
            //ajax 请求订单信息以取消
            array('label'=>'ajax请求订单取消页面','display'=>false,'as'=>'topshop.trade.close','action'=>'topshop_ctl_trade_list@ajaxCloseTrade','url'=>'ajaxclose.html','method'=>'get'),
            array('label'=>'修改订单价格页面','display'=>false,'as'=>'topshop.trade.modifyPrice','action'=>'topshop_ctl_trade_list@modifyPrice','url'=>'modifyprice.html','method'=>'get'),
            array('label'=>'保存修改订单价格','display'=>false,'as'=>'topshop.trade.modifyPrice.post','action'=>'topshop_ctl_trade_list@updatePrice','url'=>'updateprice.html','method'=>'post'),

            //订单货到付款时订单完成操作
            array('label'=>'ajax请求订单完成页面','display'=>false,'as'=>'topshop.trade.finish','action'=>'topshop_ctl_trade_list@ajaxFinishTrade','url'=>'ajaxfinish.html','method'=>'get'),
            array('label'=>'订单收钱并收货','display'=>false,'as'=>'topshop.trade.postfinish','action'=>'topshop_ctl_trade_list@finishTrade','url'=>'finish.html','method'=>'post'),

            //异常订单取消，退款
            //array('label'=>'取消异常订单页面','display'=>false,'action'=>'topshop_ctl_trade_abnormal@closeView','url'=>'closeAbnormal.html','method'=>'get'),
            //array('label'=>'异常订单提交申请','display'=>false,'action'=>'topshop_ctl_trade_abnormal@applyClose','url'=>'applyCloseAbnormal.html','method'=>'post'),
            //array('label'=>'异常订单取消管理','display'=>true,'action'=>'topshop_ctl_trade_abnormal@index','url'=>'abnormal.html','method'=>'get'),
            //array('label'=>'异常订单取消详情','display'=>false,'action'=>'topshop_ctl_trade_abnormal@detail','url'=>'abnormal-detail.html','method'=>'get'),

            //企业模板配置
            array('label'=>'快递模板配置','display'=>false,'as'=>'topshop.dlytmpl.index','action'=>'topshop_ctl_shop_dlytmpl@index','url'=>'wuliu/logis/templates.html','method'=>'get'),
            array('label'=>'快递模板配置编辑','display'=>false,'as'=>'topshop.dlytmpl.edit','action'=>'topshop_ctl_shop_dlytmpl@editView','url'=>'wuliu/logis/templates/create.html','method'=>'get'),
            array('label'=>'快递运费模板保存','display'=>false,'as'=>'topshop.dlytmpl.save','action'=>'topshop_ctl_shop_dlytmpl@savetmpl','url'=>'wuliu/logis/templates.html','method'=>'post'),
            array('label'=>'快递运费模板删除','display'=>false,'as'=>'topshop.dlytmpl.delete','action'=>'topshop_ctl_shop_dlytmpl@remove','url'=>'wuliu/logis/remove.html','method'=>'get'),
            array('label'=>'判断快递运费模板名称是否存在','display'=>false,'as'=>'topshop.dlytmpl.isExists','action'=>'topshop_ctl_shop_dlytmpl@isExists','url'=>'wuliu/logis/isExists.html','method'=>'post'),
        ),
    ),
   
    /*
    |--------------------------------------------------------------------------
    | 企业管理中心之企业商品管理
    |--------------------------------------------------------------------------
     */
    'item' => array(
        'label' => '商品',
        'display' => true,
        'action'=> 'topshop_ctl_item@itemList',
        'icon' => 'glyphicon glyphicon-edit',
        'menu' => array(
            array('label'=>'商品列表','display'=>true,'as'=>'topshop.item.list','action'=>'topshop_ctl_item@itemList','url'=>'item/itemList.html','method'=>'get'),
            array('label'=>'商品搜索','display'=>false,'as'=>'topshop.item.search','action'=>'topshop_ctl_item@searchItem','url'=>'item/search.html','method'=>'post'),
            array('label'=>'发布商品','display'=>true,'as'=>'topshop.item.add','action'=>'topshop_ctl_item@add','url'=>'item/add.html','method'=>'get'),
            array('label'=>'编辑商品','display'=>false,'as'=>'topshop.item.edit','action'=>'topshop_ctl_item@edit','url'=>'item/edit.html','method'=>'get'),
            array('label'=>'设置商品状态','display'=>false,'as'=>'topshop.item.setStatus','action'=>'topshop_ctl_item@setItemStatus','url'=>'item/setItemStatus.html','method'=>'post'),
            array('label'=>'删除商品','display'=>false,'as'=>'topshop.item.delete','action'=>'topshop_ctl_item@deleteItem','url'=>'item/deleteItem.html','method'=>'post'),
            array('label'=>'创建商品','display'=>false,'as'=>'topshop.item.create','action'=>'topshop_ctl_item@storeItem','url'=>'item/storeItem.html','method'=>'post'),

            array('label'=>'企业分类','display'=>true,'as'=>'topshop.item.cat.index','action'=>'topshop_ctl_item_cat@index','url'=>'categories.html','method'=>'get'),
            array('label'=>'企业分类保存','display'=>false,'as'=>'topshop.item.cat.store','action'=>'topshop_ctl_item_cat@storeCat','url'=>'categories.html','method'=>'post'),
            array('label'=>'企业分类删除','display'=>false,'as'=>'topshop.item.cat.delete','action'=>'topshop_ctl_item_cat@removeCat','url'=>'categories/remove.html','method'=>'post'),
            array('label'=>'获取企业支持品牌','display'=>false,'as'=>'topshop.item.brand','action'=>'topshop_ctl_item@ajaxGetBrand','url'=>'categories/getbrand.html','method'=>'post'),

            //图片管理
            array('label'=>'图片管理','display'=>true,'as'=>'topshop.image.index','action'=>'topshop_ctl_shop_image@index','url'=>'image.html','method'=>'get'),
            array('label'=>'根据条件搜索图片,tab切换','as'=>'topshop.image.search','display'=>false,'action'=>'topshop_ctl_shop_image@search','url'=>'image/search.html','method'=>'post'),
            array('label'=>'删除图片','display'=>false,'as'=>'topshop.image.delete','action'=>'topshop_ctl_shop_image@delImgLink','url'=>'image/delimglink.html','method'=>'post'),
            array('label'=>'修改图片名称','display'=>false,'as'=>'topshop.image.upname','action'=>'topshop_ctl_shop_image@upImgName','url'=>'image/upimgname.html','method'=>'post'),
            array('label'=>'企业使用图片加载modal','display'=>false,'as'=>'topshop.image.loadModal','action'=>'topshop_ctl_shop_image@loadImageModal','url'=>'image/loadimagemodal.html','method'=>'get'),
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | 企业管理中心之供求信息管理 by litong 
    |--------------------------------------------------------------------------
     */

 'supply' => array(
        'label' => '供应信息',
        'display' => true,
        'action' => 'topshop_ctl_sysstat_sysstat@index',
        'icon' => 'glyphicon glyphicon-list-alt',
        'menu' => array(
            array('label'=>'供应信息','display'=>true,'as'=>'topshop.sysstat','action'=>'topc_ctl_member_supplyman@needgoods','url'=>'sysstat/sysstat.html','method'=>'get'),
         )
    ),
      'require' => array(
        'label' => '求购信息',
        'display' => true,
        'action' => 'topshop_ctl_sysstat_sysstat@index',
        'icon' => 'glyphicon glyphicon-list-alt',
        'menu' => array(
            array('label'=>'供应信息','display'=>true,'as'=>'topshop.sysstat','action'=>'topc_ctl_member_supplyman@wantgoods','url'=>'sysstat/sysstat.html','method'=>'get'),
         )
    ),
 'gongying' => array(
        'label' => '供求信息',
        'display' => true,
        'action' => 'topshop_ctl_gongqiu@gongyingList',
        'icon' => 'glyphicon glyphicon-list-alt',
        'menu' => array(
            array('label'=>'供应列表','display'=>true,'action'=>'topshop_ctl_gongqiu@gongyingList','url'=>'gongyingList.html','method'=>'get'),
            array('label'=>'添加供应信息','display'=>true,'action'=>'topshop_ctl_gongqiu@addGongying','url'=>'addGongying.html','method'=>'get'),
            array('label'=>'保存供应信息','display'=>false,'action'=>'topshop_ctl_gongqiu@saveGongying','url'=>'addGongying.html','method'=>'post'),
            
            array('label'=>'求购列表','display'=>true,'action'=>'topshop_ctl_gongqiu@qiugouList','url'=>'qiugouList.html','method'=>'get'),
            array('label'=>'添加求购信息','display'=>true,'action'=>'topshop_ctl_gongqiu@addQiugou','url'=>'addQiugou.html','method'=>'get'),
            array('label'=>'保存求购信息','display'=>false,'action'=>'topshop_ctl_gongqiu@saveqiugou','url'=>'addQiugou.html','method'=>'post'),
            
         )
    ),

    /*
    |--------------------------------------------------------------------------
    | 企业管理中心之营销管理
    |--------------------------------------------------------------------------
     */
    'promotion' => array(
        'label' => '营销',
        'display' => false,
        'action' => 'topshop_ctl_promotion_fullminus@list_fullminus',
        'icon' => 'glyphicon glyphicon-bookmark',
        'menu' => array(
            //满减促销
            array('label'=>'满减管理','display'=>true,'as'=>'topshop.fullminus.list','action'=>'topshop_ctl_promotion_fullminus@list_fullminus','url'=>'list_fullminus.html','method'=>'get'),
            array('label'=>'添加/编辑满减','display'=>false,'as'=>'topshop.fullminus.edit','action'=>'topshop_ctl_promotion_fullminus@edit_fullminus','url'=>'edit_fullminus.html','method'=>'get'),
            array('label'=>'保存满减','display'=>false,'as'=>'topshop.fullminus.save','action'=>'topshop_ctl_promotion_fullminus@save_fullminus','url'=>'save_fullminus.html','method'=>'post'),
            array('label'=>'删除满减','display'=>false,'as'=>'topshop.fullminus.delete','action'=>'topshop_ctl_promotion_fullminus@delete_fullminus','url'=>'delete_fullminus.html','method'=>'post'),
            //满折促销
            array('label'=>'满折管理','display'=>true,'as'=>'topshop.fulldiscount.list','action'=>'topshop_ctl_promotion_fulldiscount@list_fulldiscount','url'=>'list_fulldiscount.html','method'=>'get'),
            array('label'=>'添加/编辑满折','display'=>false,'as'=>'topshop.fulldiscount.edit','action'=>'topshop_ctl_promotion_fulldiscount@edit_fulldiscount','url'=>'edit_fulldiscount.html','method'=>'get'),
            array('label'=>'保存满折','display'=>false,'as'=>'topshop.fulldiscount.save','action'=>'topshop_ctl_promotion_fulldiscount@save_fulldiscount','url'=>'save_fulldiscount.html','method'=>'post'),
            array('label'=>'删除满折','display'=>false,'as'=>'topshop.fulldiscount.delete','action'=>'topshop_ctl_promotion_fulldiscount@delete_fulldiscount','url'=>'delete_fulldiscount.html','method'=>'post'),
            // 优惠券促销
            array('label'=>'优惠券管理','display'=>true,'as'=>'topshop.coupon.list','action'=>'topshop_ctl_promotion_coupon@list_coupon','url'=>'list_coupon.html','method'=>'get'),
            array('label'=>'添加/编辑优惠券','display'=>false,'as'=>'topshop.coupon.edit','action'=>'topshop_ctl_promotion_coupon@edit_coupon','url'=>'edit_coupon.html','method'=>'get'),
            array('label'=>'保存优惠券','display'=>false,'as'=>'topshop.coupon.save','action'=>'topshop_ctl_promotion_coupon@save_coupon','url'=>'save_coupon.html','method'=>'post'),
            array('label'=>'删除优惠券','display'=>false,'as'=>'topshop.coupon.delete','action'=>'topshop_ctl_promotion_coupon@delete_coupon','url'=>'delete_coupon.html','method'=>'post'),
            // 免邮促销
            array('label'=>'免邮管理','display'=>true,'as'=>'topshop.freepostage.list','action'=>'topshop_ctl_promotion_freepostage@list_freepostage','url'=>'list_freepostage.html','method'=>'get'),
            array('label'=>'添加/编辑免邮','display'=>false,'as'=>'topshop.freepostage.edit','action'=>'topshop_ctl_promotion_freepostage@edit_freepostage','url'=>'edit_freepostage.html','method'=>'get'),
            array('label'=>'保存免邮','display'=>false,'as'=>'topshop.freepostage.save','action'=>'topshop_ctl_promotion_freepostage@save_freepostage','url'=>'save_freepostage.html','method'=>'post'),
            array('label'=>'删除免邮','display'=>false,'as'=>'topshop.freepostage.delete','action'=>'topshop_ctl_promotion_freepostage@delete_freepostage','url'=>'delete_freepostage.html','method'=>'post'),
            // X件Y折促销
            array('label'=>'X件Y折管理','display'=>true,'as'=>'topshop.xydiscount.list','action'=>'topshop_ctl_promotion_xydiscount@list_xydiscount','url'=>'list_xydiscount.html','method'=>'get'),
            array('label'=>'添加/编辑X件Y折','display'=>false,'as'=>'topshop.xydiscount.edit','action'=>'topshop_ctl_promotion_xydiscount@edit_xydiscount','url'=>'edit_xydiscount.html','method'=>'get'),
            array('label'=>'保存X件Y折','display'=>false,'as'=>'topshop.xydiscount.save','action'=>'topshop_ctl_promotion_xydiscount@save_xydiscount','url'=>'save_xydiscount.html','method'=>'post'),
            array('label'=>'删除X件Y折','display'=>false,'as'=>'topshop.xydiscount.delete','action'=>'topshop_ctl_promotion_xydiscount@delete_xydiscount','url'=>'delete_xydiscount.html','method'=>'post'),
            // 活动报名
            array('label'=>'活动报名','display'=>true,'as'=>'topshop.activity.registeredlist','action'=>'topshop_ctl_promotion_activity@registered_activity','url'=>'registered.html','method'=>'get'),
            array('label'=>'活动列表','display'=>false,'as'=>'topshop.activity.activitylist','action'=>'topshop_ctl_promotion_activity@activity_list','url'=>'activitylist.html','method'=>'get'),
            array('label'=>'历史报名','display'=>false,'as'=>'topshop.activity.historyregisteredlist','action'=>'topshop_ctl_promotion_activity@historyregistered_activity','url'=>'historyregistered.html','method'=>'get'),
            array('label'=>'历史报名详情','display'=>false,'as'=>'topshop.activity.historyregistereddetial','action'=>'topshop_ctl_promotion_activity@historyregistered_detail','url'=>'historyregistered_detail.html','method'=>'get'),
            array('label'=>'添加/编辑活动申请','display'=>false,'as'=>'topshop.activity.edit','action'=>'topshop_ctl_promotion_activity@canregistered_apply','url'=>'edit_activity.html','method'=>'get'),
            array('label'=>'保存申请活动','display'=>false,'as'=>'topshop.activity.save','action'=>'topshop_ctl_promotion_activity@canregistered_apply_save','url'=>'save_activity.html','method'=>'post'),
            array('label'=>'活动列表页活动详情','display'=>false,'as'=>'topshop.activity.noregistered.detail','action'=>'topshop_ctl_promotion_activity@noregistered_detail','url'=>'noregistered_detail.html','method'=>'post'),
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | 企业管理中心之企业管理
    |--------------------------------------------------------------------------
     */
    'shop' => array(
        'label' => '企业展台',
        'display' => true,
        'action' => 'topshop_ctl_shop_setting@index',
        'icon' => 'glyphicon glyphicon-cog',
        'menu' => array(
            //企业配置
            array('label'=>'企业展台配置','display'=>true,'as'=>'topshop.shopsetting.index','action'=>'topshop_ctl_shop_setting@index','url'=>'setting.html','method'=>'get'),
            array('label'=>'企业配置保存','display'=>false,'as'=>'topshop.shopsetting.save','action'=>'topshop_ctl_shop_setting@saveSetting','url'=>'setting/save.html','method'=>'post'),

            array('label'=>'企业通知','display'=>false,'as'=>'topshop.shopnotice','action'=>'topshop_ctl_shop_notice@index','url'=>'shop/shopnotice.html','method'=>'get'),
            array('label'=>'企业通知详情','display'=>false,'as'=>'topshop.shopnotice.detail','action'=>'topshop_ctl_shop_notice@noticeInfo','url'=>'shop/shopnoticeinto.html','method'=>'get'),

            //企业装修
            array('label'=>'企业展台装修','display'=>true,'as'=>'topshop.decorate.index','action'=>'topshop_ctl_shop_decorate@index','url'=>'decorate.html','method'=>'get'),
            array('label'=>'企业装修弹出框','display'=>false,'as'=>'topshop.decorate.dialog','action'=>'topshop_ctl_shop_decorate@dialog','url'=>'decorate/dialog.html','method'=>'get'),
            array('label'=>'企业装修配置','display'=>false,'as'=>'topshop.decorate.save','action'=>'topshop_ctl_shop_decorate@save','url'=>'decorate/save.html','method'=>'post'),

            //wap端企业配置
            array('label'=>'wap端企业装修','display'=>false,'as'=>'topshop.wap.decorate.index','action'=>'topshop_ctl_wap_decorate@index','url'=>'wapdecorate.html','method'=>'get'),
            array('label'=>'wap端企业装修弹出框','display'=>false,'as'=>'topshop.wap.decorate.dialog','action'=>'topshop_ctl_wap_decorate@dialog','url'=>'wapdecorate/dialogs.html','method'=>'get'),
            array('label'=>'wap端企业装修顺序保存','display'=>false,'as'=>'topshop.wap.decorate.saveSort','action'=>'topshop_ctl_wap_decorate@saveSort','url'=>'wapdecorate/saveSort.html','method'=>'post'),
            array('label'=>'wap端企业装修标签配置','display'=>false,'as'=>'topshop.wap.decorate.addTags','action'=>'topshop_ctl_wap_decorate@addTags','url'=>'wapAddTags.html','method'=>'get'),
            array('label'=>'wap企业装修配置','display'=>false,'as'=>'topshop.wap.decorate.save','action'=>'topshop_ctl_wap_decorate@save','url'=>'wapdecorate/save.html','method'=>'post'),
            array('label'=>'wap企业装修标签配置删除','display'=>false,'as'=>'topshop.wap.decorate.ajaxWidgetsDel','action'=>'topshop_ctl_wap_decorate@ajaxWidgetsDel','url'=>'wapdecorate/ajaxWidgetsDel.html','method'=>'post'),
            array('label'=>'wap企业装修标签配置开启','display'=>false,'as'=>'topshop.wap.decorate.openTags','action'=>'topshop_ctl_wap_decorate@openTags','url'=>'wapdecorate/opentags.html','method'=>'post'),
            array('label'=>'wap企业装修前台商品显示','display'=>false,'as'=>'topshop.wap.decorate.ajaxCheckShowItems','action'=>'topshop_ctl_wap_decorate@ajaxCheckShowItems','url'=>'wapdecorate/ajaxCheckShowItems.html','method'=>'post'),
            array('label'=>'wap企业装修前台广告商品显示检查','display'=>false,'as'=>'topshop.wap.decorate.checkImageSlider','action'=>'topshop_ctl_wap_decorate@checkImageSlider','url'=>'wapdecorate/checkImageSlider.html','method'=>'post'),

            array('label'=>'企业入驻信息','display'=>false,'as'=>'topshop.shopapply.info','action'=>'topshop_ctl_shop_shopinfo@index','url'=>'shop/shopapplyinfo.html','method'=>'get'),

            //开发者中心
            array('label'=>'开发者中心','display'=>false,'as'=>'topshop.open.developer.center','action'=>'topshop_ctl_open@index','url'=>'developer.html','method'=>'get', 'middleware'=>['topshop_middleware_selfManagement']),
            array('label'=>'开发者中心企业参数配置保存','display'=>false,'as'=>'topshop.open.developer.shop.conf.save','action'=>'topshop_ctl_open@setConf','url'=>'saveDevelopConf.html','method'=>'post', 'middleware'=>['topshop_middleware_selfManagement']),
            array('label'=>'开发者中心企业申请开通','display'=>false,'as'=>'topshop.open.developer.shop.apply','action'=>'topshop_ctl_open@applyForOpen','url'=>'applyDevelop.html','method'=>'get', 'middleware'=>['topshop_middleware_selfManagement']),
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | 企业管理中心之客户服务
    |--------------------------------------------------------------------------
     */
    'aftersales' => array(
        'label' => '客服',
        'display' => false,
        'action' => 'topshop_ctl_aftersales@index',
        'icon' => 'icon-chatbubbles',
        'menu' => array(
            array('label'=>'退换货管理','display'=>false,'as'=>'topshop.aftersales.list','action'=>'topshop_ctl_aftersales@index','url'=>'aftersales-list.html','method'=>'get'),
            array('label'=>'退换货详情','display'=>false,'as'=>'topshop.aftersales.detail','action'=>'topshop_ctl_aftersales@detail','url'=>'aftersales-detail.html','method'=>'get'),
            array('label'=>'退换货搜索','display'=>false,'as'=>'topshop.aftersales.search','action'=>'topshop_ctl_aftersales@search','url'=>'aftersales-search.html','method'=>'post'),
            array('label'=>'审核退换货申请','display'=>false,'as'=>'topshop.aftersales.verification','action'=>'topshop_ctl_aftersales@verification','url'=>'aftersales-verification.html','method'=>'post'),
            array('label'=>'换货重新发货','display'=>false,'as'=>'topshop.aftersales.sendConfirm','action'=>'topshop_ctl_aftersales@sendConfirm','url'=>'aftersales-send.html','method'=>'post'),

            //评价管理&DSR管理
            array('label'=>'评价列表','display'=>false,'as'=>'topshop.rate.list','action'=>'topshop_ctl_rate@index','url'=>'rate-list.html','method'=>'get'),
            array('label'=>'评价搜索','display'=>false,'as'=>'topshop.rate.search','action'=>'topshop_ctl_rate@search','url'=>'rate-search.html','method'=>'get'),
            array('label'=>'评价详情','display'=>false,'as'=>'topshop.rate.detail','action'=>'topshop_ctl_rate@detail','url'=>'rate-detail.html','method'=>'get'),
            array('label'=>'评价回复','display'=>false,'as'=>'topshop.rate.reply','action'=>'topshop_ctl_rate@reply','url'=>'rate-reply.html','method'=>'post'),

            array('label'=>'申诉列表','display'=>false,'as'=>'topshop.rate.appeal.list','action'=>'topshop_ctl_rate_appeal@appealList','url'=>'rate-appeal-list.html','method'=>'get'),
            array('label'=>'申诉搜索','display'=>false,'as'=>'topshop.rate.appeal.search','action'=>'topshop_ctl_rate_appeal@search','url'=>'rate-appeal-search.html','method'=>'get'),
            array('label'=>'申诉详情','display'=>false,'as'=>'topshop.rate.appeal.detail','action'=>'topshop_ctl_rate_appeal@appeaInfo','url'=>'rate-appeal-info.html','method'=>'get'),
            array('label'=>'评价申诉','display'=>false,'as'=>'topshop.rate.appeal','action'=>'topshop_ctl_rate_appeal@appeal','url'=>'rate-appeal.html','method'=>'post'),

            array('label'=>'评价概况','display'=>false,'as'=>'topshop.rate.count','action'=>'topshop_ctl_rate_count@index','url'=>'rate-count.html','method'=>'get'),

            //咨询管理
            array('label'=>'咨询列表','display'=>false,'as'=>'topshop.gask.list','action'=>'topshop_ctl_consultation@index','url'=>'gask-list.html','method'=>'get'),
            array('label'=>'咨询回复','display'=>false,'as'=>'topshop.gask.reply','action'=>'topshop_ctl_consultation@doReply','url'=>'gask-reply.html','method'=>'post'),
            array('label'=>'咨询筛选','display'=>false,'as'=>'topshop.gask.screening','action'=>'topshop_ctl_consultation@screening','url'=>'gask-screening.html','method'=>'get'),
            array('label'=>'回复删除','display'=>false,'as'=>'topshop.gask.delete','action'=>'topshop_ctl_consultation@doDelete','url'=>'gask-del.html','method'=>'post'),
            array('label'=>'显示或关闭咨询与回复','display'=>false,'as'=>'topshop.gask.display','action'=>'topshop_ctl_consultation@doDisplay','url'=>'gask-display.html','method'=>'post'),
        ),
    ),

    'shopinfo' => array(
        'label' => '结算',
        'display' => false,
        'action' => 'topshop_ctl_shop_shopinfo@index',
        'icon' => 'glyphicon glyphicon-cloud',
        'menu' => array(
            array('label'=>'企业结算汇总','display'=>true,'as'=>'topshop.settlement','action'=>'topshop_ctl_clearing_settlement@index','url'=>'shop/settlement.html','method'=>'get'),
            array('label'=>'企业结算明细','display'=>true,'as'=>'topshop.settlement.detail','action'=>'topshop_ctl_clearing_settlement@detail','url'=>'shop/settlement_detail.html','method'=>'get'),
        )
    ),

    'sysstat' => array(
        'label' => '报表',
        'display' => false,
        'action' => 'topshop_ctl_sysstat_sysstat@index',
        'icon' => 'glyphicon glyphicon-list-alt',
        'menu' => array(
            array('label'=>'企业运营概况','display'=>true,'as'=>'topshop.sysstat','action'=>'topshop_ctl_sysstat_sysstat@index','url'=>'sysstat/sysstat.html','method'=>'get'),
            array('label'=>'交易数据分析','display'=>true,'as'=>'topshop.stattrade','action'=>'topshop_ctl_sysstat_stattrade@index','url'=>'sysstat/stattrade.html','method'=>'get'),
            array('label'=>'业务数据分析','display'=>true,'as'=>'topshop.sysbusiness','action'=>'topshop_ctl_sysstat_sysbusiness@index','url'=>'sysstat/sysbusiness.html','method'=>'get'),
            array('label'=>'商品销售分析','display'=>true,'as'=>'topshop.sysstat.itemtrade.index','action'=>'topshop_ctl_sysstat_itemtrade@index','url'=>'sysstat/itemtrade.html','method'=>'get'),
        )
    ),

    'account' => array(
        'label' => '账号',
        'display' => false,
        'action' => 'topshop_ctl_account_list@index',
        'icon' => 'glyphicon glyphicon-lock',
        'menu' => array(
            array('label'=>'账号管理','display'=>true,'as'=>'topshop.account.list','action'=>'topshop_ctl_account_list@index','url'=>'account/list.html','method'=>'get'),
            array('label'=>'编辑账号','display'=>false,'as'=>'topshop.account.edit','action'=>'topshop_ctl_account_list@edit','url'=>'account/edit.html','method'=>'get'),
            array('label'=>'修改账号密码','display'=>false,'as'=>'topshop.account.modifyPwd','action'=>'topshop_ctl_account_list@modifyPwd','url'=>'account/modifypwd.html','method'=>'post'),
            array('label'=>'保存账号','display'=>false,'as'=>'topshop.account.save','action'=>'topshop_ctl_account_list@save','url'=>'account/add.html','method'=>'post'),
            array('label'=>'删除账号','display'=>false,'as'=>'topshop.account.delete','action'=>'topshop_ctl_account_list@delete','url'=>'account/del.html','method'=>'get'),

            array('label'=>'角色管理','display'=>true,'as'=>'topshop.roles.list','action'=>'topshop_ctl_account_roles@index','url'=>'roles/list.html','method'=>'get'),
            array('label'=>'保存角色保存','display'=>false,'as'=>'topshop.roles.save','action'=>'topshop_ctl_account_roles@save','url'=>'roles/save.html','method'=>'post'),
            array('label'=>'编辑角色页面','display'=>false,'as'=>'topshop.roles.edit','action'=>'topshop_ctl_account_roles@edit','url'=>'roles/edit.html','method'=>'get'),
            array('label'=>'删除角色','display'=>false,'as'=>'topshop.roles.delete','action'=>'topshop_ctl_account_roles@delete','url'=>'roles/del.html','method'=>'get'),
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | 企业管理中心之企业入驻申请
    |--------------------------------------------------------------------------
     */
    'enterapply' => array(
        'label' => '企业入驻',
        'display' => false,
        'action'=>'topshop_ctl_enterapply@apply',
        'menu' => array(
            array('label'=>'入驻申请页','display'=>false,'as'=>'topshop.apply','action'=>'topshop_ctl_enterapply@apply','url'=>'apply.html','method'=>'get'),
            array('label'=>'入驻申请页保存','display'=>false,'as'=>'topshop.apply.save','action'=>'topshop_ctl_enterapply@saveApply','url'=>'apply/save.html','method'=>'post'),
            array('label'=>'入驻申请更改','display'=>false,'as'=>'topshop.apply.update','action'=>'topshop_ctl_enterapply@updateApply','url'=>'apply/update.html','method'=>'get'),
            array('label'=>'入驻申请查看','display'=>false,'as'=>'topshop.apply.checkplan','action'=>'topshop_ctl_enterapply@checkPlan','url'=>'apply/checkplan.html','method'=>'get'),
            # 入驻申请-ajax请求类目下的品牌
            array('label'=>'获取分类下品牌','display'=>false,'as'=>'topshop.ajax.cat.brand','action'=>'topshop_ctl_enterapply@ajaxCatBrand','url'=>'ajax/cat/brand.html','method'=>'get'),
        )
    ),


);
