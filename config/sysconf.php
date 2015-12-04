<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 * 商家基本设置
 */

return array(
    'pc端商店基本设置'=>array(
        'site.logo'=>array('type'=>SET_T_IMAGE,'default'=>'669f61c74cc8624dc1156939682aacd3','desc'=>'商城Logo','backend'=>'public','extends_attr'=>array('width'=>200,'height'=>95)),
        'site.name'=>array('type'=>SET_T_STR,'vtype'=>'maxLength','default'=>'点此设置您商店的名称','desc'=>'商城名称','javascript'=>'validatorMap.set("maxLength",["最大长度32个字",function(el,v){return v.length < 33;}]);'),
        'site.loginlogo'=>array('type'=>SET_T_IMAGE,'default'=>'','desc'=>'登录注册页左侧大图','backend'=>'public','extends_attr'=>array('width'=>200,'height'=>95),'helpinfo'=>'<span class=\'notice-inline\'>图片标准宽度为600*600</span>'),
    ),
    '交易设置' => array(
        'trade.cancel.spacing.time' => array( 'type'=>SET_T_INT,'default'=>72,'desc'=>'交易关闭间隔时间','vtype'=>'number','helpinfo'=>'<span class=\'notice-inline\'>单位：小时(h)</span>'),
        'trade.finish.spacing.time' => array( 'type'=>SET_T_INT,'default'=>7, 'desc'=>'交易完成间隔时间','vtype'=>'number','helpinfo'=>'<span class=\'notice-inline\'>单位：天(d)</span>'),
    ),
    '积分设置' => array(
        'point.ratio' => array('type'=>SET_T_STR,'default'=>1,'desc'=>'积分换算比率:','vtype'=>'required&&positive','helpinfo'=>'<span class=\'notice-inline\'>默认1元 = 1积分</span>'),
        'point.expired.month' => array('type'=>SET_T_STR,'default'=>12,'desc'=>'积分过期月份:','vtype'=>'required&&positive','helpinfo'=>'<span class=\'notice-inline\'>默认12【12代表每年的12月最后一天】 </span>'),
    ),

    #'购物设置'=>array(
    #    'site.buy.target',
    #    'system.money.decimals',
    #    'system.money.operation.carryset',
    #    'site.trigger_tax', //是否开启发票
    #    'site.personal_tax_ratio',
    #    'site.company_tax_ratio',
    #    'site.tax_content',
    #    'site.checkout.zipcode.required.open',
    #    'site.checkout.receivermore.open',
    #    'site.combination.pay',//组合支付
    #    'cart.show_order_sales.type',
    #),
    #'购物显示设置'=>array(
    #    'site.login_type',
    #    'site.register_valide',
    #    'site.login_valide',
    #    'gallery.default_view',
    #    'site.show_mark_price',
    #    'site.market_price',
    #    'site.market_rate',
    #    'selllog.display.switch',
    #    'selllog.display.limit',
    #    'selllog.display.listnum',
    #    'site.save_price',
    #    'goods.show_order_sales.type',
    #    'site.member_price_display',
    #    'site.show_storage',
    #    'goodsbn.display.switch',
    #    'goods.recommend',
    #    'goodsprop.display.position',
    #    'site.isfastbuy_display',
    #    'gallery.display.listnum',
    #    'gallery.display.pagenum',
    #    'gallery.deliver.time',
    #    'gallery.comment.time',
    #    'site.cat.select',
    #    'gallery.display.buynum',
    #    'gallery.display.price',
    #    'gallery.display.tag.goods',
    #    'gallery.display.tag.promotion',
    #    'gallery.display.promotion',
    #    'gallery.display.store_status',
    #    'gallery.store_status.num',
    #    'gallery.display.stock_goods',
    #    'site.imgzoom.show',
    #    'site.imgzoom.width',
    #    'site.imgzoom.height',
    #),
    #'其他设置'=>array(
    #    'system.product.alert.num',
    #    'system.goods.freez.time',
    #),
);

