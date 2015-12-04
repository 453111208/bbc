<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/*
|--------------------------------------------------------------------------
| api
|--------------------------------------------------------------------------
 */
route::group(array('prefix' => 'api'), function()
{
    route::match(array('GET','POST'),'/api.json',['as'=>'api/api.json','uses'=>'system_ctl_getApiJson@index']);
    route::match(array('GET','POST'),'/', ['uses'=>'base_rpc_server@process']);
});

/*
|--------------------------------------------------------------------------
| PC端消费者平台
|--------------------------------------------------------------------------
*/

route::group(array('middleware' => ['topc_middleware_redirectIfFromWap', 'theme_middleware_preview']), function() {
    /*
    |--------------------------------------------------------------------------
    | 会员登录注册相关
    |--------------------------------------------------------------------------
    */
    route::group(array(), function() {
        # 登陆页
        route::get('passport-signin.html', [ 'middleware' => 'topc_middleware_redirectIfAuthenticated',
                                             'uses' => 'topc_ctl_passport@signin' ]);
        # 登陆
        route::post('passport-signin.html', [ 'middleware' => 'topc_middleware_redirectIfAuthenticated',
                                              'uses' => 'topc_ctl_passport@login' ]);
        # 注册页
        route::get('passport-signup.html', ['middleware' => 'topc_middleware_redirectIfAuthenticated',
                                            'uses' => 'topc_ctl_passport@signup' ]);
        route::get('passport-signupc.html', ['middleware' => 'topc_middleware_redirectIfAuthenticated',
                                            'uses' => 'topc_ctl_passport@signupc' ]);
        
        # 注册成功页
        route::get('passport-signup-success.html', ['middleware' => 'topc_middleware_authenticate',
                                                    'uses' => 'topc_ctl_passport@signupSuccess']);
        # 注册
        route::post('passport-signup.html', [ 'uses' => 'topc_ctl_passport@create' ]);
        route::post('comp-signup.html', [ 'uses' => 'topc_ctl_passport@createc' ]);
        
        route::post('passport-sendVcode.html', [ 'uses' => 'topc_ctl_passport@sendVcode' ]);
        # 注册验证
        route::post('passport-signupcheck.html', [ 'uses' => 'topc_ctl_passport@checkLoginAccount' ]);
        route::post('passport-emailcheck.html', [ 'uses' => 'topc_ctl_passport@checkEmailAccount' ]);
        # 找回密码1
        route::get('passport-findpwd.html', [ 'uses' => 'topc_ctl_passport@findPwd' ]);
        # 找回密码2
        route::post('passport-findpwdtwo.html', [ 'uses' => 'topc_ctl_passport@findPwdTwo' ]);
        route::get('passport-findpwdtwo.html', [ 'uses' => 'topc_ctl_passport@findPwdTwo' ]);
        # 找回密码3
        route::match(array('GET', 'POST'), 'passport-findpwdthree.html', ['uses' => 'topc_ctl_passport@findPwdThree']);
        # #找回密码短信验证码发送
        route::post('passport-findpwdfour.html', [ 'uses' => 'topc_ctl_passport@findPwdFour' ]);
        # 找回密码短信验证码发送
        route::post('passport-sendVcode.html', [ 'uses' => 'topc_ctl_passport@sendVcode' ]);
        # 信任登陆
        # callback
        /*
        route::get('trustlogin-bind.html', [ 'uses' => 'topc_ctl_trustlogin@callBack' ]);
        # 设置新的账号
        route::post('trustlogin.html', [ 'uses' => 'topc_ctl_trustlogin@setLogin' ]);
        # 绑定已有账户
        route::post('trustlogin-binds.html', [ 'uses' => 'topc_ctl_trustlogin@checkLogin' ]);
        # 模拟登陆
        route::get('trustlogin-postlogin.html', [ 'uses' => 'topc_ctl_trustlogin@postLogin' ]);
        */
        route::get('trustlogin-bind.html', [ 'middleware' => 'topc_middleware_redirectIfAuthenticated',
                                             'uses' => 'topc_ctl_trustlogin@callBack' ]);
        route::post('bindDefaultCreateUser.html', [ 'middleware' => 'topc_middleware_redirectIfAuthenticated',
                                                    'uses' => 'topc_ctl_trustlogin@bindDefaultCreateUser' ]);
        route::post('bindExistsUser.html', [  'middleware' => 'topc_middleware_redirectIfAuthenticated',
                                              'uses' => 'topc_ctl_trustlogin@bindExistsUser' ]);
        route::post('bindSignupUser.html', [ 'middleware' => 'topc_middleware_redirectIfAuthenticated',
                                             'uses' => 'topc_ctl_trustlogin@bindSignupUser' ]);

        # 退出
        route::get('passport-logout.html', [ 'middleware' => 'topc_middleware_authenticate',
                                             'uses' => 'topc_ctl_passport@logout' ]);
    });

    /*
    |--------------------------------------------------------------------------
    | 文章相关
    |--------------------------------------------------------------------------
    */
    route::group(array(), function() {

        route::get('content-index.html', [ 'uses' => 'topc_ctl_content@index' ]);
        route::get('content-info.html', [ 'uses' => 'topc_ctl_content@getContentInfo' ]);
    });
    /*
    |--------------------------------------------------------------------------
    | 商品首页详细页相关
    |--------------------------------------------------------------------------
    */
    route::group(array(), function() {
        # 首页
        route::get('/', [ 'as' => 'topc', 'uses' => 'topc_ctl_default@index']);
#new by  焕境
        route::get('articles', [ 'uses' => 'topc_ctl_addpage@articles' ]);    
        route::get('articleslist', [ 'uses' => 'topc_ctl_addpage@articleslist' ]);    
        route::get('articlesdtl', [ 'uses' => 'topc_ctl_addpage@articlesdtl' ]);    
        route::get('supplylist', [ 'uses' => 'topc_ctl_addpage@supplylist' ]);    
        route::get('supplydtl', [ 'uses' => 'topc_ctl_addpage@supplydtl' ]);   
        route::post('mffb.html', [ 'uses' => 'topc_ctl_mffb@save' ]);       
        //成功案例
        route::get('plan.html',['uses'=>'topc_ctl_plan@index']);
        route::get('plan-literary.html', [ 'uses' => 'topc_ctl_plan@literary' ]); 
        //解决方案
        route::get('case.html',['uses'=>'topc_ctl_case@index']);
        route::get('case-essay.html', [ 'uses' => 'topc_ctl_case@essay' ]);  

       // route::get('biddingdtl', [ 'uses' => 'topc_ctl_addpage@biddingdtl' ]);    
       // route::get('tenderdtl', [ 'uses' => 'topc_ctl_addpage@tenderdtl' ]);    
       //route::get('productdtl', [ 'uses' => 'topc_ctl_addpage@productdtl' ]);  
        //----------
        #咨询
        route::get('standard-gack.html', [ 'uses' => 'topc_ctl_standard@getItemConsultation' ]); 
        route::get('standard-gack-list.html', [ 'uses' => 'topc_ctl_standard@getItemConsultationList' ]); 
        route::get('standard-commit.html', [ 'uses' => 'topc_ctl_standard@commitConsultation' ]); 

        #商品详情页，咨询
        route::get('item-gack.html', [ 'uses' => 'topc_ctl_item@getItemConsultation' ]);
        route::get('item-gack-list.html', [ 'uses' => 'topc_ctl_item@getItemConsultationList' ]);
  route::get('gallery-list.html', [ 'uses' => 'topc_ctl_gallery@index' ]);
        # 商城一级类目页
         route::get('standard', [ 'uses' => 'topc_ctl_standard@index' ]); #环保超市
        route::get('tender', [ 'uses' => 'topc_ctl_tender@index' ]);
        route::get('commrate', [ 'uses' => 'topc_ctl_comment@getItemRate' ]);
        route::get('commratelist', [ 'uses' => 'topc_ctl_comment@getItemRateList' ]);
        route::post('commsave', [ 'uses' => 'topc_ctl_comment@save' ]);


        route::get('hjgallery', [ 'uses' => 'topc_ctl_hjgallery@index' ]); #幻境列表页
        route::get('hjproduct', [ 'uses' => 'topc_ctl_hjproduct@index' ]); #幻境商品详情
        route::get('trading', [ 'uses' => 'topc_ctl_trading@index' ]); #交易中心
        route::get('market', [ 'uses' => 'topc_ctl_addpage@markets' ]); #行情中心
        //route::get('market-details', [ 'uses' => 'topc_ctl_addpage@index' ]); #行情中心详情
        // route::get('market-list', [ 'uses' => 'topc_ctl_marketList@index' ]); #行情中心列表
        route::get('bidding', [ 'uses' => 'topc_ctl_bidding@index' ]); #竞价
        route::post('wish', [ 'uses' => 'topc_ctl_bidding@wish' ]); #收藏意向
        route::get('enterprise', [ 'uses' => 'topc_ctl_enterprise@enterprise' ]); #企业库
        route::get('third', [ 'uses' => 'topc_ctl_third@third' ]);
        route::get('thirdlist', [ 'uses' => 'topc_ctl_third@thirdlist' ]);
        //企业展台
        route::post('shopcoll.html', [ 'uses' => 'topc_ctl_shopcenter@ajaxFavshop' ]);
        //名人专家
        route::get('expert', [ 'uses' => 'topc_ctl_expert@index' ]);
        route::get('expert-literary.html', [ 'uses' => 'topc_ctl_expert@literary' ]);
        route::get('expert-expert.html', [ 'uses' => 'topc_ctl_expert@expert' ]);
        route::get('expert-expertList.html', [ 'uses' => 'topc_ctl_expert@expertList' ]);
        
        # 商品收藏
        route::post('member_fav.html', [ 'middleware' => 'topc_middleware_authenticate',
                                         'uses' => 'topc_ctl_collect@ajaxFav' ]);
        route::post('member-collectdel.html', [ 'middleware' => 'topc_middleware_authenticate',
                                                'uses' => 'topc_ctl_collect@ajaxFavDel' ]);
        # 店铺收藏
        route::post('member_favshop.html', [ 'middleware' => 'topc_middleware_authenticate',
                                             'uses' => 'topc_ctl_collect@ajaxFavshop' ]);
        route::post('member-collectshopdel.html', [ 'middleware' => 'topc_middleware_authenticate',
                                                    'uses' => 'topc_ctl_collect@ajaxFavshopDel' ]);
        # 商品列表
        route::get('list.html', [ 'uses' => 'topc_ctl_list@index' ]);
        # 商城一级类目页
        route::get('topics-{cat_id}.html', [ 'uses' => 'topc_ctl_topics@index' ]);
        # 品牌列表
        route::get('brand.html', [ 'uses' => 'topc_ctl_brand@index' ]);

        # 商品详情
        route::get('item.html', ['as' =>'topc.item.detail', 'uses' => 'topc_ctl_item@index' ]);
        #商品详情页，评价列表
        route::get('item-rate.html', [ 'uses' => 'topc_ctl_item@getItemRate' ]);
        route::get('item-rate-list.html', [ 'uses' => 'topc_ctl_item@getItemRateList' ]);
               #供需详情 by litong 
        route::get('require.html', [ 'uses' => 'topc_ctl_require@index' ]);
         route::get('supply.html', [ 'uses' => 'topc_ctl_supply@index' ]);
         #交易详情 by litong
         route::get('bidding.html', [ 'uses' => 'topc_ctl_bidding@index' ]);
         route::get('trande.html', [ 'uses' => 'topc_ctl_trande@index' ]);
        #商品详情页，促销
        route::get('promotion-item.html', [ 'uses' => 'topc_ctl_promotion@getPromotionItem' ]);
        #商品详情页,到货通知
        route::post('user-item.html', [ 'uses' => 'topc_ctl_memberItem@userNotifyItem' ]);

        #提交资讯信息
        route::post('item-gack-add.html', [ 'uses' => 'topc_ctl_item@commitConsultation' ]);
        #活动列表页
        route::get('activity/list.html', [ 'uses' => 'topc_ctl_activity@index' ]);
        route::get('activity/search.html', [ 'uses' => 'topc_ctl_activity@search' ]);
        route::get('activity/item/list.html', [ 'uses' => 'topc_ctl_activity@itemlist' ]);
        route::get('activity/detail.html', [ 'uses' => 'topc_ctl_activity@detail' ]);
        route::get('activity/lv3.html', [ 'uses' => 'topc_ctl_activity@getCatLv3' ]);
    });

    /*
    |--------------------------------------------------------------------------
    | 店铺相关
    |--------------------------------------------------------------------------
    */
    route::group(array(), function() {
        # 店铺首页
        route::get('shopcenter.html', [ 'uses' => 'topc_ctl_shopcenter@index' ]);
        route::get('zizhicenter.html', [ 'uses' => 'topc_ctl_shopcenter@zizhi' ]);
        
        #固废信息
        route::get('gufeicenter.html', [ 'uses' => 'topc_ctl_shopcenter@gfinfo' ]);

        #技术设备
        route::get('equipmentcenter.html', [ 'uses' => 'topc_ctl_shopcenter@equipmentinfo' ]);

        #产品信息
        route::get('productcenter.html', [ 'uses' => 'topc_ctl_shopcenter@productinfo' ]);
        #交易记录
        route::get('shoptrading.html', [ 'uses' => 'topc_ctl_shopcenter@shoptrading' ]);
        #联系方式
        route::get('shopmap.html', [ 'uses' => 'topc_ctl_shopcenter@shopmap' ]);
        
        # 店铺前台优惠券列表页
        route::get('shopCouponList.html', [ 'uses' => 'topc_ctl_shopcenter@shopCouponList' ]);
        # 领取优惠券
        route::post('getCoupon.html', [ 'middleware' => 'topc_middleware_authenticate',
                                        'uses' => 'topc_ctl_shopcenter@getCouponCode' ]);
        # 领取优惠券成功页
        route::get('getCouponResult.html', [ 'uses' => 'topc_ctl_shopcenter@getCouponResult' ]);
        route::get('search.html', [ 'uses' => 'topc_ctl_shopcenter@search' ]);

        #搜索
        route::post('search.html', [ 'uses' => 'topc_ctl_search@index' ]);

        #sitemap
        # 列表
        //route::get('sitemaps.html', [ 'uses' => 'site_ctl_sitemaps@catalog' ]);
        # 目录明细
        //route::get('sitemaps/{id}.html', [ 'uses' => 'site_ctl_sitemaps@index' ]);

    });

    /*
    |--------------------------------------------------------------------------
    | 会员中心
    |--------------------------------------------------------------------------
    */
    route::group(array('middleware' => 'topc_middleware_authenticate'), function() {
        # 会员中心首页
        route::get('member-index.html', [ 'as' => 'home', 'uses' => 'topc_ctl_member@index' ]);
        # 会员中心个人资料
        route::get('member-seinfoset.html', [ 'uses' => 'topc_ctl_member@seInfoSet' ]);
        # 会员中心个人资料
        route::post('member-saveinfoset.html', [ 'uses' => 'topc_ctl_member@saveInfoSet' ]);
        # 会员中心信任登陆密码修改
        route::get('member-pwdset.html', [ 'uses' => 'topc_ctl_member@pwdSet' ]);
        # 会员中心信任登陆密码修改
        route::post('member-savepwdset.html', [ 'uses' => 'topc_ctl_member@savePwdSet' ]);
        # 会员中心安全中心
        route::get('member-security.html', [ 'uses' => 'topc_ctl_member@security' ]);
        # 会员中心安全中心密码修改
        route::get('member-modifypwd.html', [ 'uses' => 'topc_ctl_member@modifyPwd' ]);
        # 会员中心安全中心密码修改
        route::post('member-savemodifypwd.html', [ 'uses' => 'topc_ctl_member@saveModifyPwd' ]);
        # 会员中心手机/邮箱绑定
        route::get('member-setuserinfo.html', [ 'uses' => 'topc_ctl_member@verify' ]);
        # 会员中心登录检测
        route::post('member-checkUserLogin.html', [ 'uses' => 'topc_ctl_member@CheckSetInfo' ]);
        route::get('member-setinfoone.html', [ 'uses' => 'topc_ctl_member@setUserInfoOne' ]);# 会员中心手机
        # 会员中心短信验证码发送
        route::post('member-sendVcode.html', [ 'uses' => 'topc_ctl_member@sendVcode' ]);
        # 会员中心个人信息最后保存
        route::post('member-bindMobile.html', [ 'uses' => 'topc_ctl_member@bindMobile' ]);
        route::get('member-bindEmail.html', [ 'uses' => 'topc_ctl_member@bindEmail' ]);
        # 会员中心个人信息最后保存后的跳转
        route::get('member-setinfolast.html', [ 'uses' => 'topc_ctl_member@setUserInfoLast' ]);
        # 会员中心解绑手机邮箱
        route::get('member-unverify.html', [ 'uses' => 'topc_ctl_member@unVerifyOne' ]);
        route::post('member-checkvcode.html', [ 'uses' => 'topc_ctl_member@checkVcode' ]);
        route::get('member-unverifytwo.html', [ 'uses' => 'topc_ctl_member@unVerifyTwo' ]);
        route::post('member-unverifymobile.html', [ 'uses' => 'topc_ctl_member@unVerifyMobile' ]);
        route::get('member-unverifyemail.html', [ 'uses' => 'topc_ctl_member@unVerifyEmail' ]);
        route::get('member-unverifylast.html', [ 'uses' => 'topc_ctl_member@unVerifyLast' ]);
        # 会员中心收货地址管理
        route::get('member-address.html', [ 'uses' => 'topc_ctl_member@address' ]);
        # 会员中心收货地址修改
        route::post('member-updateaddr.html', [ 'uses' => 'topc_ctl_member@ajaxAddrUpdate' ]);
        # 会员中心默认收货地址
        route::post('member-addrdef.html', [ 'uses' => 'topc_ctl_member@ajaxAddrDef' ]);
        # 删除会员中心收货地址
        route::post('member-deladdr.html', [ 'uses' => 'topc_ctl_member@ajaxDelAddr' ]);
        #会员中心收货地址添加
        route::post('member-address.html', [ 'uses' => 'topc_ctl_member@saveAddress' ]);
        # 会员中心店铺收藏
        route::get('member-collectshops.html', [ 'uses' => 'topc_ctl_member@shopsCollect' ]);
        # 会员中心商品收藏
        route::get('member-collectitems.html', [ 'uses' => 'topc_ctl_member@itemsCollect' ]);
        # 会员中心优惠券列表
        route::get('member-coupon.html', [ 'uses' => 'topc_ctl_member_coupon@couponList' ]);

        #会员中心售后申请
        route::get('member-aftersales-apply.html', [ 'uses' => 'topc_ctl_member_aftersales@aftersalesApply' ]);
        route::post('member-aftersales-commit.html', [ 'uses' => 'topc_ctl_member_aftersales@commitAftersalesApply' ]);
        route::get('member-aftersales-list.html', [ 'uses' => 'topc_ctl_member_aftersales@aftersalesList' ]);
        route::get('member-aftersales-detail.html', [ 'uses' => 'topc_ctl_member_aftersales@aftersalesDetail' ]);
        route::get('member-aftersales-godetail.html', [ 'uses' => 'topc_ctl_member_aftersales@goAftersalesDetail' ]);
        route::post('member-aftersales-sendback.html', [ 'uses' => 'topc_ctl_member_aftersales@sendback' ]);
        route::get('trade-aftersales-logistics.html', [ 'uses' => 'topc_ctl_member_aftersales@ajaxLogistics' ]);

        #订单投诉
        route::get('complaints-view.html', [ 'uses' => 'topc_ctl_member_complaints@complaintsView']);
        route::post('complaints-ci.html', [ 'uses' => 'topc_ctl_member_complaints@complaintsCi']);
        route::get('complaints-detail.html', [ 'uses' => 'topc_ctl_member_complaints@detail']);
        route::post('complaints-close.html', [ 'uses' => 'topc_ctl_member_complaints@closeComplaints']);

        #会员中心评价
        route::get('member-rate-add.html',  [ 'uses' => 'topc_ctl_member_rate@createRate' ]);
        route::post('member-rate-add.html', [ 'uses' => 'topc_ctl_member_rate@doCreateRate' ]);
        route::get('member-rate-index.html', [ 'uses' => 'topc_ctl_member_rate@index' ]);
        route::get('member-rate-list.html', [ 'uses' => 'topc_ctl_member_rate@ratelist' ]);
        route::get('member-rate-setAnony.html', [ 'uses' => 'topc_ctl_member_rate@setAnony' ]);
        route::get('member-rate-del.html',  [ 'uses' => 'topc_ctl_member_rate@doDelete' ]);
        route::post('member-rate-update.html', [ 'uses' => 'topc_ctl_member_rate@update' ]);
        route::get('member-rate-edit.html', [ 'uses' => 'topc_ctl_member_rate@edit' ]);

        #会员中心咨询
        route::get('member-gack-index.html', [ 'uses' => 'topc_ctl_member_consultation@index' ]);
        route::post('member-gack-del.html', [ 'uses' => 'topc_ctl_member_consultation@doDelete' ]);

        # 会员中心成长值及极分
        route::get('member-myexp.html', [ 'uses' => 'topc_ctl_member_experience@experience' ]);
        route::get('member-mypoint.html', [ 'uses' => 'topc_ctl_member_point@point' ]);
        route::get('member-mygrade.html', [ 'uses' => 'topc_ctl_member@grade' ]);

        # 会员中心我的订单
        route::get('trade-list.html', [ 'uses' => 'topc_ctl_member_trade@tradeList' ]);
        route::post('logi.html', [ 'uses' => 'topc_ctl_member_trade@ajaxGetLogi' ]);
        
        # 会员中心订单详情
        route::get('trade-detail.html', [ 'uses' => 'topc_ctl_member_trade@tradeDetail' ]);
        route::get('trade-cancel.html', [ 'uses' => 'topc_ctl_member_trade@ajaxCancelTrade' ]);
        route::get('trade-confirm.html', [ 'uses' => 'topc_ctl_member_trade@ajaxConfirmTrade' ]);
        route::match(array('GET', 'POST'), 'confirm-buyer.html', ['uses' => 'topc_ctl_member_trade@confirmReceipt']);
        route::match(array('GET', 'POST'), 'cancel-buyer.html', ['uses' => 'topc_ctl_member_trade@cancelOrderBuyer']);
     #商品发布 by litong
        route::get('member-goods-fbgoods.html',['uses' => 'topc_ctl_member_goods@index']);
        #供应信息 by litong
        route::get('member-needgoods.html', [ 'uses' => 'topc_ctl_member@needgoods' ]);
        route::get('member-needgoodsEdit.html', [ 'uses' => 'topc_ctl_member@editGoods' ]);
        route::post('member-needgoodsEdit.html', [ 'uses' => 'topc_ctl_member@SaveGoods' ]);
        //route::post('member-needgoodsEdit.html', [ 'uses' => 'topc_ctl_member@getCat' ]);
        #求购信息 by litong
        route::get('member-wantgoods.html', [ 'uses' => 'topc_ctl_member@wantgoods' ]);
        route::get('member-wantgoodsEdit.html', [ 'uses' => 'topc_ctl_member@eidtRequireGoods' ]);
        route::post('member-wantgoodsEdit.html', [ 'uses' => 'topc_ctl_member@SaveRequireGoods' ]);
   # 商品管理 by:litong
        route::get('addGoods.html', [ 'uses' => 'topc_ctl_member_goods@addGoods' ]);
         route::post('addGoods.html', [ 'uses' => 'topc_ctl_member_goods@save' ]);
        route::post('addGoon.html', [ 'uses' => 'topc_ctl_member_goods@getOption' ]);
        route::post('addGood.html', [ 'uses' => 'topc_ctl_member_goods@getProp' ]);
        #选择商品数据
        route::post('member-tenderule.html', [ 'uses' => 'topc_ctl_member_tenderule@tenderule' ]);
#资讯中心 by dlc
        route::get('publishInfo.html', [ 'uses' => 'topc_ctl_member_info@publishInfo' ]);  //我发布的资讯
        route::get('editInfo.html', [ 'uses' => 'topc_ctl_member_info@editInfo' ]); //编辑资讯信息
        route::post('editInfo.html', [ 'uses' => 'topc_ctl_member_info@saveInfo' ]);  //资讯保存
        route::post('enquireInfo.html', [ 'uses' => 'topc_ctl_member_enquireInfo@enquireInfo' ]); //询价信息保存
        route::get('enquire.html', [ 'uses' => 'topc_ctl_member_enquireInfo@enquire' ]);  //我发布的询价
        route::get('enquireEdit.html', [ 'uses' => 'topc_ctl_member_enquireInfo@enquireEdit' ]);  //我发布的询价
        route::get('inreqsupp.html', [ 'uses' => 'topc_ctl_member_enquireinfo@inreqsupp' ]);  //我参与的供求  
#一口价
        route::post('once.html', [ 'uses' => 'topc_ctl_bidding@once' ]);



        #标准
       
                # 提交  
        route::post('standard-save', [ 'uses' => 'topc_ctl_standard@save' ]);  
        # 申请看样
        route::post('standard-sample', [ 'uses' => 'topc_ctl_standard@sample' ]); 
        #看样
        route::post('sample_dialog', [ 'uses' => 'topc_ctl_sample@sample_dialog' ]);   
        route::post('save_sample', [ 'uses' => 'topc_ctl_sample@save_sample' ]);   

        # 商品管理
        route::get('addGoods.html', [ 'uses' => 'topc_ctl_member_goods@addGoods' ]);
         route::post('addGoods.html', [ 'uses' => 'topc_ctl_member_goods@save' ]);
        route::post('addGoo.html', [ 'uses' => 'topc_ctl_member_goods@getOption' ]);
        route::post('addGood.html', [ 'uses' => 'topc_ctl_member_goods@getProp' ]);


        # 我发布的交易
        route::get('shoppubt-list.html', [ 'uses' => 'topc_ctl_member_shoppubt@shoppubtList' ]);
        route::get('shoppubt-addStandards.html', [ 'uses' => 'topc_ctl_member_shoppubt@addStandards' ]);
        route::get('shoppubt-addBidding.html', [ 'uses' => 'topc_ctl_member_biddings@addBidding' ]);


        route::get('shoppubt-addTender.html', [ 'uses' => 'topc_ctl_member_tender@addTender']);
        route::post('shoppubt-saveS.html', [ 'uses' => 'topc_ctl_member_shoppubt@saveS' ]); 
        route::post('biddings-saveS.html', [ 'uses' => 'topc_ctl_member_biddings@saveS']); 
        route::post('tender-saveS.html', [ 'uses' => 'topc_ctl_member_tender@saveS']); 

        #招标  by  z
        route::post('tender-rule.html', [ 'uses' => 'topc_ctl_tender@tender' ]);
        route::post('tender-save.html', [ 'uses' => 'topc_ctl_tender@save' ]);
        route::get('tender-newpage.html', [ 'uses' => 'topc_ctl_tender@newpage' ]);
        route::get('tender-select.html', [ 'uses' => 'topc_ctl_member_shoppubt@select' ]);
        route::post('tender-win.html', [ 'uses' => 'topc_ctl_member_shoppubt@win' ]);
        route::post('tender-detail.html', [ 'uses' => 'topc_ctl_member_shoppubt@detail' ]);
        route::post('tender-checkmoney.html', [ 'uses' => 'topc_ctl_tender@checkmoney' ]);
        route::post('tender-msave.html', [ 'uses' => 'topc_ctl_member_shoppubt@save' ]);
        route::get('shoppubt-tenderList.html', [ 'uses' => 'topc_ctl_member_shoppubt@tenderList']);
        route::post('shoppubt-tender.html', [ 'uses' => 'topc_ctl_member_tenderule@save']);
        route::post('member-tenderule.html', [ 'uses' => 'topc_ctl_member_tenderule@tenderule' ]);
        route::get('shoppubt-biddingList.html', [ 'uses' => 'topc_ctl_member_biddings@biddingList']);
        route::get('shoppubt-mytrade.html', [ 'uses' => 'topc_ctl_member_mytrade@mytrade']);
        route::get('shoppubt-standardList.html', [ 'uses' => 'topc_ctl_member_shoppubt@standardList']);

        # 交割地址
        route::post('member-pAddrDialog.html', [ 'uses' => 'topc_ctl_member_shoppubt@addr_dialog' ]);   #模态框 
        route::post('member-pSaveAddress.html', [ 'uses' => 'topc_ctl_member_shoppubt@saveAddress' ]); #保存地址 
        route::post('member-setAddrDef.html', [ 'uses' => 'topc_ctl_member_deliveryaddr@setAddrDef' ]); #修改默认状态 

        #选择商品
        route::post('member-sGoods.html', [ 'uses' => 'topc_ctl_member_shoppubt@sGodds' ]); #选择商品模态框 
        route::post('member-sGoodsData.html', [ 'uses' => 'topc_ctl_member_goods@sGoodsData' ]); #选择商品数据

        #选择感兴趣的信息 DLC 2015/11/13
        route::post('member-search.html', [ 'uses' => 'topc_ctl_member@search' ]); #选择信息 
        route::post('member-saveInfo.html', [ 'uses' => 'topc_ctl_member@saveInfo' ]); #保存信息

        route::post('member-upsort.html', [ 'uses' => 'topc_ctl_member@getupsort' ]); #查询下级分类 
        

        #保存商品
        route::post('member-saveitem.html', [ 'uses' => 'topc_ctl_member_standarditem@saveItem' ]); #保存明细

        #test frame
        route::get('add_item_frame.html', [ 'uses' => 'topc_ctl_member_frame@add_index' ]); #保存明细
        route::get('item_list_frame.html', [ 'uses' => 'topc_ctl_member_frame@list_index' ]); #保存明细

        #入驻信息修改
        route::get('perfectData.html', [ 'uses' => 'topc_ctl_member_userdata@index' ]); 
        route::post('perfectData.html', [ 'uses' => 'topc_ctl_member_userdata@saveShop' ]); 

        #企业展台配置
        route::get('configCenter.html', [ 'uses' => 'topc_ctl_member_configCenter@index' ]); 
        route::post('configCenter.html', [ 'uses' => 'topc_ctl_member_configCenter@save' ]); 

        route::post('enquireInfo.html', [ 'uses' => 'topc_ctl_member_enquireinfo@enquireinfo' ]); //询价信息保存
        route::get('enquire.html', [ 'uses' => 'topc_ctl_member_enquireinfo@enquire' ]);  //我发布的询价
        route::get('enquireEdit.html', [ 'uses' => 'topc_ctl_member_enquireinfo@enquireEdit' ]);  //我发布的询价

        # 供求管理  by litong
        route::get('needgoods.html', [ 'uses' => 'topc_ctl_member_supplyman@needgoods' ]);
        route::get('needgoodsEdit.html', [ 'uses' => 'topc_ctl_member_supplyman@editGoods' ]); //编辑供应信息
        route::post('needgoodsEdit.html', [ 'uses' => 'topc_ctl_member_supplyman@SaveGoods' ]);

        route::get('wantgoods.html', [ 'uses' => 'topc_ctl_member_supplyman@wantgoods' ]);
        route::get('wantgoodsEdit.html', [ 'uses' => 'topc_ctl_member_supplyman@eidtRequireGoods' ]); //编辑供求信息
        route::post('wantgoodsEdit.html', [ 'uses' => 'topc_ctl_member_supplyman@SaveRequireGoods' ]);
        
        #定制信息详情跳转
        route::get('action.html', [ 'uses' => 'topc_ctl_member@pubact' ]);
       
        #自定义商品类型
        route::get('configcat-list.html', [ 'uses' => 'topc_ctl_member_configcat@index' ]);
        route::get('configcat.html', [ 'uses' => 'topc_ctl_member_configcat@add' ]);
        route::post('configcat.html', [ 'uses' => 'topc_ctl_member_configcat@save' ]);

        #系统通知
        route::get('shopnotice-list.html', [ 'uses' => 'topc_ctl_member_notice@index' ]);
        route::post('shopnotice-list.html', [ 'uses' => 'topc_ctl_member_notice@update' ]);
        
       
    });
 /*
    |--------------------------------------------------------------------------
    | 资讯相关  DLC 2015/10/16
    |--------------------------------------------------------------------------
    */
    route::group(array(), function() {

        route::get('sysinfo-index.html', [ 'uses' => 'topc_ctl_info@index' ]);
        route::get('sysinfo-info.html', [ 'uses' => 'topc_ctl_info@getContentInfo' ]);
        route::get('sysinfo.html', [ 'uses' => 'topc_ctl_info@getInfo' ]);
        route::get('sysnode.html', [ 'uses' => 'topc_ctl_info@getNode' ]);
    });
    route::group(array(), function() {
        route::get('marketTrend.html', [ 'uses' => 'topc_ctl_market@marketTrend' ]);
        route::get('details.html', [ 'uses' => 'topc_ctl_market@details' ]);
        route::get('marketInfo.html', [ 'uses' => 'topc_ctl_market@getInfo' ]);
        route::post('updateList.html', [ 'uses' => 'topc_ctl_market@updateList' ]);
        route::get('marketList.html', [ 'uses' => 'topc_ctl_market@getList' ]);
        route::get('marketNode.html', [ 'uses' => 'topc_ctl_market@getNode' ]);
        route::get('marketOffer.html', [ 'uses' => 'topc_ctl_market@getOffer' ]);
    });
    /*
    |--------------------------------------------------------------------------
    | 交易
    |--------------------------------------------------------------------------
    */
    route::group(array('middleware' => 'topc_middleware_authenticate'), function() {
        // 购物车
        route::get('cart.html', [ 'uses' => 'topc_ctl_cart@index' ]);
        #显示购物车
        route::post('cart-add.html', [ 'uses' => 'topc_ctl_cart@add' ]); #加入购物车
        route::post('cart-update.html', [ 'uses' => 'topc_ctl_cart@updateCart' ]); #更新购物车
        route::post('cart-remove.html', [ 'uses' => 'topc_ctl_cart@removeCart' ]); #删除
        route::post('cart.html', [ 'uses' => 'topc_ctl_cart@ajaxBasicCart' ]); #购物车页
        route::post('trade-create.html', [ 'uses' => 'topc_ctl_trade@create' ]); #生成订单
        route::post('cart-total.html', [ 'uses' => 'topc_ctl_cart@total' ]); #统计总金额

        // 订单确认页
        route::get('cart-checkout.html', [ 'uses' => 'topc_ctl_cart@checkout' ]); #立即购买
        route::post('cart-checkout.html', [ 'uses' => 'topc_ctl_cart@checkout' ]); #购物信息结算页
        route::post('cart-saveAddress.html', [ 'uses' => 'topc_ctl_cart@saveAddress' ]); #购物信息结算页
        route::post('cart-addrDialog.html', [ 'uses' => 'topc_ctl_cart@addr_dialog' ]); #收货地址弹框
        route::post('cart-getCoupons.html', [ 'uses' => 'topc_ctl_cart@getCoupons' ]); #获取用户的优惠券
        route::post('cart-useCoupon.html', [ 'uses' => 'topc_ctl_cart@useCoupon' ]); #使用优惠券
        route::post('cart-cancelCoupon.html', [ 'uses' => 'topc_ctl_cart@cancelCoupon' ]); #取消优惠券
        route::post('cart-getDtyList.html', [ 'uses' => 'topc_ctl_cart@getDtyList' ]); #获取指定店铺的配送方式列表

        //获取自提列表
        route::post('trade-ziti.html', [ 'uses' => 'topc_ctl_cart@getZitiList' ]); #生成订单
    });

    /*
    |--------------------------------------------------------------------------
    | 支付中心
    |--------------------------------------------------------------------------
    */
    route::group(array('middleware' => 'topc_middleware_authenticate'), function() {
        #支付中心
        route::get('payment.html', [ 'uses' => 'topc_ctl_paycenter@index' ]);
        route::match(array('GET', 'POST'), 'create.html', ['uses' => 'topc_ctl_paycenter@createPay']);
        route::post('dopayment.html', [ 'uses' => 'topc_ctl_paycenter@dopayment' ]);
        route::get('finish.html', [ 'uses' => 'topc_ctl_paycenter@finish' ]);
    });
 });


/*
|--------------------------------------------------------------------------
| WAP端消费者平台
|--------------------------------------------------------------------------
 */
route::group(array('prefix' => 'wap', 'middleware' => 'theme_middleware_preview'), function() {
    /*
    |--------------------------------------------------------------------------
    | 会员登录注册相关
    |--------------------------------------------------------------------------
    */
    route::group(array(), function() {
        # 登录
        route::get('passport-signin.html', [ 'middleware' => 'topm_middleware_redirectIfAuthenticated',
                                             'uses' => 'topm_ctl_passport@signin' ]);
        route::post('passport-signin.html', [ 'middleware' => 'topm_middleware_redirectIfAuthenticated',
                                              'uses' => 'topm_ctl_passport@login' ]);
        route::post('passport-saveuname.html', [ 'uses' => 'topm_ctl_passport@saveUname' ]); # 保存登陆设置用户名
        # 退出
        route::get('passport-logout.html', [ 'uses' => 'topm_ctl_passport@logout' ]);
        # 注册
        route::get('passport-signup.html', [ 'middleware' => 'topm_middleware_redirectIfAuthenticated',
                                             'uses' => 'topm_ctl_passport@signup' ]);
        route::get('passport-license.html', [ 'uses' => 'topm_ctl_passport@license' ]);
        route::post('passport-signup.html', [ 'uses' => 'topm_ctl_passport@create' ]);
        route::post('passport-signupcheck.html', [ 'uses' => 'topm_ctl_passport@checkLoginAccount' ]);# 注册验证
        # 找回密码
        route::get('passport-findpwd.html', [ 'uses' => 'topm_ctl_passport@findPwd' ]);#找回密码1
        route::post('passport-findpwdtwo.html', [ 'uses' => 'topm_ctl_passport@findPwdTwo' ]);#找回密码2
        route::get('passport-findpwdtwo.html', [ 'uses' => 'topm_ctl_passport@findPwdTwo' ]);
        route::post('passport-findpwdthree.html', [ 'uses' => 'topm_ctl_passport@findPwdThree' ]);#找回密码3
        route::get('passport-findpwdthree.html', [ 'uses' => 'topm_ctl_passport@findPwdThree' ]);#找回密码3
        route::post('passport-sendVcode.html', [ 'uses' => 'topm_ctl_passport@sendVcode' ]);#找回密码短信验证码发送
        route::post('passport-findpwdfour.html', [ 'uses' => 'topm_ctl_passport@findPwdFour' ]);#找回密码4

        # 信任登陆
        route::get('trustlogin-bind.html', [ 'uses' => 'topm_ctl_trustlogin@callBack' ]);
        route::post('bindDefaultCreateUser.html', [ 'uses' => 'topm_ctl_trustlogin@bindDefaultCreateUser' ]);
        route::post('bindExistsUser.html', [ 'uses' => 'topm_ctl_trustlogin@bindExistsUser' ]);
        route::post('bindSignupUser.html', [ 'uses' => 'topm_ctl_trustlogin@bindSignupUser' ]);


        /*
        route::get('trustwaplogin-bind.html', [ 'uses' => 'topm_ctl_trustlogin@callBack' ]);
        route::post('trustwaplogin.html', [ 'uses' => 'topm_ctl_trustlogin@setLogin' ]);
        route::post('trustwaplogin-binds.html', [ 'uses' => 'topm_ctl_trustlogin@checkLogin' ]);
        route::get('trustwaplogin-postlogin.html', [ 'uses' => 'topm_ctl_trustlogin@postLogin' ]);
        */
    });

    /*
    |--------------------------------------------------------------------------
    | 文章相关
    |--------------------------------------------------------------------------
    */
    route::group(array(), function() {

        route::get('node-list.html', [ 'uses' => 'topm_ctl_content@nodeList' ]);
        route::get('content-list.html', [ 'uses' => 'topm_ctl_content@contentList' ]);
        route::get('content-info.html', [ 'uses' => 'topm_ctl_content@getContentInfo' ]);
    });

    /*
    |--------------------------------------------------------------------------
    | 首页,商品详细页,类目相关
    |--------------------------------------------------------------------------
    */
    route::group(array(), function() {
        # 系统分类
        route::get('/', [ 'as' => 'topm', 'uses' => 'topm_ctl_default@index' ]);
        # 切换到手机端
        route::get('to-pc.html', [ 'as' => 'siwtchToPc', 'uses' => 'topm_ctl_default@switchToPc']);
        #商品搜索
        route::post('search.html', [ 'uses' => 'topm_ctl_search@index' ]);
        route::get('search.html', [ 'uses' => 'topm_ctl_shopcenter@search' ]);
        #商品搜索结果页
        route::get('list.html', [ 'uses' => 'topm_ctl_list@index' ]);
        route::get('ajaxitemshow.html', [ 'uses' => 'topm_ctl_list@ajaxItemShow' ]);

        # 一级类目页
        route::get('categroy.html', [ 'uses' => 'topm_ctl_category@index' ]);
        route::get('catlistinfo.html', [ 'uses' => 'topm_ctl_category@catList' ]);#一级下面类目页

        # 商品详情
        route::get('item.html', ['as' => 'topm.item.detail', 'uses' => 'topm_ctl_item@index' ]);
        route::post('item-collect.html', [ 'uses' => 'topm_ctl_collect@ajaxFav' ]);#收藏
        route::post('shop-collect.html', [ 'uses' => 'topm_ctl_collect@ajaxFavshop' ]);#收藏
        route::get('itempic.html', [ 'uses' => 'topm_ctl_item@itemPic' ]);#图文详情
        route::get('itemparams.html', [ 'uses' => 'topm_ctl_item@itemParams' ]);#图文详情

        #交易详情 by litong
         route::get('bidding.html', [ 'uses' => 'topc_ctl_bidding@index' ]);
         route::post('bidding.html', [ 'uses' => 'topc_ctl_bidding@sendPrice' ]);
         route::get('trande.html', [ 'uses' => 'topc_ctl_trande@index' ]);
        #商品详情页，评价列表
        route::get('item-rate.html', [ 'uses' => 'topm_ctl_item@getItemRate' ]);
        route::get('item-rate-list.html', [ 'uses' => 'topm_ctl_item@getItemRateList' ]);

        # 店铺收藏删除,商品收藏删除
        route::post('member-collectdel.html', [ 'uses' => 'topm_ctl_collect@ajaxFavDel' ]);
        route::post('member-collectshopdel.html', [ 'uses' => 'topm_ctl_collect@ajaxFavshopDel' ]);
        # 商品列表

        #商品详情页，促销
        route::get('promotion-item.html', [ 'uses' => 'topm_ctl_promotion@getPromotionItem' ]);
        route::get('promotion-itemlist.html', [ 'uses' => 'topm_ctl_promotion@ajaxPromotionItemShow' ]);

        #商品详情页,到货通知
        route::post('user-item.html', [ 'uses' => 'topm_ctl_memberItem@userNotifyItem' ]);
        #活动列表页
        route::get('activity-list.html', [ 'uses' => 'topm_ctl_activity@index' ]);
        route::get('activity-search.html', [ 'uses' => 'topm_ctl_activity@search' ]);
        route::get('activity-data.html', [ 'uses' => 'topm_ctl_activity@datalist' ]);
        route::get('activity-ajax.html', [ 'uses' => 'topm_ctl_activity@ajaxItemShow' ]);
        route::get('activity-detail.html', [ 'uses' => 'topm_ctl_activity@detail' ]);
        route::get('activity-lv3.html', [ 'uses' => 'topm_ctl_activity@getCatLv3' ]);

    });

    /*
    |--------------------------------------------------------------------------
    | 会员中心
    |--------------------------------------------------------------------------
    */
    route::group(array('middleware' => 'topm_middleware_authenticate'), function() {
        # 会员中心
        route::get('member-index.html', [ 'as' => 'wap.home', 'uses' => 'topm_ctl_member@index' ]);
        route::get('member-infoset.html', [ 'uses' => 'topm_ctl_member@userinfoSet' ]);# 会员中心个人资料
        route::post('member-saveinfoset.html', [ 'uses' => 'topm_ctl_member@saveInfoSet' ]);# 会员中心个人资料
        route::get('member-collectshops.html', [ 'uses' => 'topm_ctl_member@shopsCollect' ]);# 会员中心商品收藏
        route::get('member-collectitems.html', [ 'uses' => 'topm_ctl_member@itemsCollect' ]);# 会员中心店铺收藏
        route::get('ajaxtradeshow.html', [ 'uses' => 'topm_ctl_member_trade@ajaxTradeShow' ]);

        # 会员中心安全中心
        route::get('member-security.html', [ 'uses' => 'topm_ctl_member@security' ]);
        route::get('member-modifypwd.html', [ 'uses' => 'topm_ctl_member@modifyPwd' ]);# 会员中心安全中心密码修改
        route::post('member-savemodifypwd.html', [ 'uses' => 'topm_ctl_member@saveModifyPwd' ]);# 会员中心安全中心密码修改

        route::get('member-setuserinfo.html', [ 'uses' => 'topm_ctl_member@verify' ]); # 会员中心手机/邮箱绑定
        route::post('member-checkUserLogin.html', [ 'uses' => 'topm_ctl_member@CheckSetInfo' ]);# 会员中心登录检测
        route::get('member-setinfoone.html', [ 'uses' => 'topm_ctl_member@setUserInfoOne' ]);# 会员中心手机
        route::post('member-sendVcode.html', [ 'uses' => 'topm_ctl_member@sendVcode' ]);# 会员中心短信验证码发送
        route::post('member-saveSetInfo.html', [ 'uses' => 'topm_ctl_member@saveSetInfo' ]); # 会员中心个人信息最后保存
        route::get('member-setinfotwo.html', [ 'uses' => 'topm_ctl_member@setUserInfoTwo' ]);# 会员中心个人信息最后保存后的跳转
        route::get('member-bindEmail.html', [ 'uses' => 'topm_ctl_member@bindEmail' ]);# 邮箱认证
        route::get('member-verifyRoute.html', [ 'uses' => 'topm_ctl_member@verifyRoute' ]);# 安全中心绑定手机页面路由
         # 会员中心解绑手机邮箱
        route::get('member-unverify.html', [ 'uses' => 'topm_ctl_member@unVerifyOne' ]);
        route::post('member-checkvcode.html', [ 'uses' => 'topm_ctl_member@checkVcode' ]);
        route::get('member-unverifytwo.html', [ 'uses' => 'topm_ctl_member@unVerifyTwo' ]);
        route::post('member-unverifymobile.html', [ 'uses' => 'topm_ctl_member@unVerifyMobile' ]);
        route::get('member-unverifyemail.html', [ 'uses' => 'topm_ctl_member@unVerifyEmail' ]);
        route::get('member-unverifylast.html', [ 'uses' => 'topm_ctl_member@unVerifyLast' ]);
        # 户名设置
        route::post('member-updateaccount.html', [ 'uses' => 'topm_ctl_member@saveUserAccount' ]);
        # 会员中心收货地址管理
        route::get('member-address.html', [ 'uses' => 'topm_ctl_member@address' ]);
        route::get('member-updateaddr.html', [ 'uses' => 'topm_ctl_member@addrUpdate' ]);# 会员中心收货地址修改
        route::post('member-addrdef.html', [ 'uses' => 'topm_ctl_member@ajaxAddrDef' ]);# 会员中心默认收货地址
        route::post('member-deladdr.html', [ 'uses' => 'topm_ctl_member@ajaxDelAddr' ]);# 删除会员中心收货地址
        route::post('member-address.html', [ 'uses' => 'topm_ctl_member@saveAddress' ]);#会员中心收货地址添加

        #会员中心评价
        route::get('member-rate-add.html',  [ 'uses' => 'topm_ctl_member_rate@createRate' ]);
        route::post('member-rate-add.html', [ 'uses' => 'topm_ctl_member_rate@doCreateRate' ]);

        # 会员中心优惠券列表
        route::get('member-couponList.html', [ 'uses' => 'topm_ctl_member_coupon@couponList' ]);
        route::get('member-ajaxCouponData.html', [ 'uses' => 'topm_ctl_member_coupon@ajaxCouponData' ]);
        #删除优惠券
        route::post('member-deleteCoupon.html', [ 'uses' => 'topm_ctl_member_coupon@deleteCoupon' ]);
        // 会员中心查看优惠券详情
        route::get('member-couponDetail.html', [ 'uses' => 'topm_ctl_member_coupon@couponDetail' ]);

        #会员中心售后申请
        route::get('member-aftersales-exchange.html', [ 'uses' => 'topm_ctl_member_aftersales@exchange' ]);
        route::get('member-aftersales-apply.html', [ 'uses' => 'topm_ctl_member_aftersales@aftersalesApply' ]);
        route::post('member-aftersales-commit.html', [ 'uses' => 'topm_ctl_member_aftersales@commitAftersalesApply' ]);
        //route::get('member-aftersales-list.html', [ 'uses' => 'topm_ctl_member_aftersales@aftersalesList' ]);
        route::get('member-aftersales-detail.html', [ 'uses' => 'topm_ctl_member_aftersales@aftersalesDetail' ]);
        route::get('member-aftersales-godetail.html', [ 'uses' => 'topm_ctl_member_aftersales@goAftersalesDetail' ]);
        route::post('member-aftersales-sendback.html', [ 'uses' => 'topm_ctl_member_aftersales@sendback' ]);

        #订单投诉
        route::get('complaints-view.html', [ 'uses' => 'topm_ctl_member_complaints@complaintsView']);
        route::post('complaints-ci.html', [ 'uses' => 'topm_ctl_member_complaints@complaintsCi']);
        route::get('complaints-detail.html', [ 'uses' => 'topm_ctl_member_complaints@detail']);
        route::post('complaints-close.html', [ 'uses' => 'topm_ctl_member_complaints@closeComplaints']);
        route::get('complaints-close.html', [ 'uses' => 'topm_ctl_member_complaints@closeView']);


        #会员积分成长值
        route::get('myexp.html', [ 'uses' => 'topm_ctl_member_experience@experience' ]);
        route::get('mypoint.html', [ 'uses' => 'topm_ctl_member_point@point' ]);
        route::get('exp-detail.html', [ 'uses' => 'topm_ctl_member_experience@grade' ]);

        # 会员中心我的订单  订单详情
        route::get('tradelist.html', [ 'uses' => 'topm_ctl_member_trade@index']);  #会员中心订单列表）
        route::get('trade-list.html', [ 'uses' => 'topm_ctl_member_trade@tradeList']);  #会员中心订单列表(tab)
        route::get('trade-detail.html', [ 'uses' => 'topm_ctl_member_trade@detail' ]);
        route::get('cancel.html', [ 'uses' => 'topm_ctl_member_trade@cancel' ]);
        route::get('confirm.html', [ 'uses' => 'topm_ctl_member_trade@confirm' ]);
        route::post('logim.html', [ 'uses' => 'topm_ctl_member_trade@ajaxGetLogi' ]);
        route::match(array('GET', 'POST'), 'confirm-buyer.html', ['uses' => 'topm_ctl_member_trade@confirmReceipt']);
        route::match(array('GET', 'POST'), 'cancel-buyer.html', ['uses' => 'topm_ctl_member_trade@cancelBuyer']);
    });

    /*
    |--------------------------------------------------------------------------
    | 店铺相关
    |--------------------------------------------------------------------------
    */
    route::group(array(), function() {
        # 店铺首页
        route::get('shopcenter.html', [ 'uses' => 'topm_ctl_shopcenter@index' ]);
        /*route::get('getTagsInfo.html', [ 'uses' => 'topm_ctl_shopcenter@getTagsInfo' ]);
          route::get('ajaxTagsShow.html', [ 'uses' => 'topm_ctl_shopcenter@ajaxTagsShow' ]);*/
        # 店铺前台优惠券列表页
        route::get('shopCouponList.html', [ 'uses' => 'topm_ctl_shopcenter@shopCouponList' ]);
        # 领取优惠券
        route::post('getCoupon.html', [ 'uses' => 'topm_ctl_shopcenter@getCouponCode' ]);
        # 领取优惠券成功页
        route::get('getCouponResult.html', [ 'uses' => 'topm_ctl_shopcenter@getCouponResult' ]);
        route::get('ajaxshopitemshow.html', [ 'uses' => 'topm_ctl_shopcenter@ajaxItemShow' ]);
        route::get('vcode.html', [ 'as' => 'toputil.vcode', 'uses' => 'toputil_ctl_vcode@gen_vcode' ]);
    });

    /*
    |--------------------------------------------------------------------------
    | 交易
    |--------------------------------------------------------------------------
    */
    route::group(array(), function() {
        //购物车
        route::post('cart-add.html', [ 'uses' => 'topm_ctl_cart@add' ]); #加入购物车
        route::get('cart.html',['uses' => 'topm_ctl_cart@index']);   #购物车页
        route::post('cart-update.html',['uses' => 'topm_ctl_cart@updateCart']);  #更新购物车
        route::post('cart.html',['uses' => 'topm_ctl_cart@ajaxBasicCart']);    #购物车页
        route::post('cart-remove.html', [ 'uses' => 'topm_ctl_cart@removeCart' ]); #删除
        route::post('cart-total.html', [ 'uses' => 'topm_ctl_cart@total' ]); #统计总金额

        // 订单确认页
        route::post('cart-checkout.html',['uses' => 'topm_ctl_cart@checkout']);  #立即购买
        route::get('cart-checkout.html',['uses' => 'topm_ctl_cart@checkout']);  #购物信息结算页
        route::post('cart-saveAddress.html', [ 'uses' => 'topm_ctl_cart@saveAddress' ]); #购物信息结算页
        route::get('cart-editaddr.html', [ 'uses' => 'topm_ctl_cart@editAddr' ]); #收货地址弹框
        route::get('cart-addrList.html', [ 'uses' => 'topm_ctl_cart@getAddrList' ]); #收货地址弹框
        route::get('cart-payTypeList.html', [ 'uses' => 'topm_ctl_cart@getPayTypeList' ]); #收货地址弹框
        route::get('deladdr.html', [ 'uses' => 'topm_ctl_cart@delAddr' ]);# 删除会员中心收货地址
        route::post('cart-useCoupon.html', [ 'uses' => 'topm_ctl_cart@useCoupon' ]); #使用优惠券
        route::post('cart-cancelCoupon.html', [ 'uses' => 'topm_ctl_cart@cancelCoupon' ]); #取消优惠券

        //订单处理
        route::post('trade-create.html', [ 'uses' => 'topm_ctl_trade@create' ]); #生成订单

        #支付中心
        route::get('payment.html', [ 'uses' => 'topm_ctl_paycenter@index' ]);
        route::match(array('GET', 'POST'), 'create.html', ['uses' => 'topm_ctl_paycenter@createPay']);
        route::post('dopayment.html', [ 'uses' => 'topm_ctl_paycenter@dopayment' ]);
        route::get('finish.html', [ 'uses' => 'topm_ctl_paycenter@finish' ]);

        //微信的数据做转发。。。
        route::match(array('GET', 'POST', 'PUT', 'DELETE'), 'wxpayjsapi.html', ['uses' => 'topm_ctl_wechat@wxpayjsapi']);
    });
});

/*
|--------------------------------------------------------------------------
| 商家管理中心
|--------------------------------------------------------------------------
*/

route::group(array('prefix' => 'shop','middleware' => 'topshop_middleware_permission'), function() {


    # 首页
    route::get('/', [ 'as' => 'topshop.home', 'uses' => 'topshop_ctl_index@index' ]);
    
        


    route::get('nopermission.html', [ 'as' => 'topshop.nopermission', 'uses' => 'topshop_ctl_index@nopermission' ]);
    route::get('onlySelfManagement.html', [ 'as' => 'topshop.onlySelfManagement', 'uses' => 'topshop_ctl_index@onlySelfManagement' ]);

    # 登录
    route::get('passport/signin.html', [ 'as' => 'topshop.signin', 'uses' => 'topshop_ctl_passport@signin' ]);
    route::post('passport/signin.html', [ 'as' => 'topshop.postsignin', 'uses' => 'topshop_ctl_passport@login' ]);
    # 注册
    route::get('passport/signup.html', [ 'as' => 'topshop.signup', 'uses' => 'topshop_ctl_passport@signup' ]);
    route::post('passport/signup.html', [ 'as' => 'topshop.postsignup', 'uses' => 'topshop_ctl_passport@create' ]);
    # 退出
    route::get('passport/logout.html', [ 'as' => 'topshop.logout', 'uses' => 'topshop_ctl_passport@logout' ]);
    # 账户是否存在
    route::get('passport/isexists.html', [ 'as' => 'topshop.userexists', 'uses' => 'topshop_ctl_passport@isExists' ]);
    # 商家修改密码
    route::get('passport/update.html', [ 'as' => 'topshop.update', 'uses' => 'topshop_ctl_passport@update' ]);
    route::post('passport/update.html', [ 'as' => 'topshop.postupdatepwd', 'uses' => 'topshop_ctl_passport@updatepwd' ]);
    #商家信息查看
    route::get('enterapply/applyInfo.html', [ 'as' => 'topshop.getApplyInfo', 'uses' => 'topshop_ctl_enterapply@getApplyInfo' ]);
    #促销管理
    #满减
    route::post('promotion/fullminuslist.html', [ 'as' => 'topshop.promotion.fullminuslist', 'uses' => 'topshop_ctl_promotion_fullminus@searchItem' ]);
    route::post('promotion/fullminusbrand.html', [ 'as' => 'topshop.promotion.fullminus', 'uses' => 'topshop_ctl_promotion_fullminus@getBrandList' ]);
    #满折
    route::post('promotion/fulldiscountlist.html', [ 'as' => 'topshop.promotion.fulldiscountlist', 'uses' => 'topshop_ctl_promotion_fulldiscount@searchItem' ]);
    route::post('promotion/fulldiscountbrand.html', [ 'as' => 'topshop.promotion.fulldiscount', 'uses' => 'topshop_ctl_promotion_fulldiscount@getBrandList' ]);
    #优惠券
    route::post('promotion/couponItemSearch.html', [ 'as' => 'topshop.promotion.couponlist', 'uses' => 'topshop_ctl_promotion_coupon@searchItem' ]);
    route::post('promotion/couponbrand.html', [ 'as' => 'topshop.promotion.coupon', 'uses' => 'topshop_ctl_promotion_coupon@getBrandList' ]);
    #免邮
    route::post('promotion/freepostageItemSearch.html', [ 'as' => 'topshop.promotion.freepostagelist', 'uses' => 'topshop_ctl_promotion_freepostage@searchItem' ]);
    route::post('promotion/freepostagebrand.html', [ 'as' => 'topshop.promotion.freepostage', 'uses' => 'topshop_ctl_promotion_freepostage@getBrandList' ]);
    #免邮
    route::post('promotion/xydiscountItemSearch.html', [ 'as' => 'topshop.promotion.xydiscountlist', 'uses' => 'topshop_ctl_promotion_xydiscount@searchItem' ]);
    route::post('promotion/xydiscountbrand.html', [ 'as' => 'topshop.promotion.xydiscount', 'uses' => 'topshop_ctl_promotion_xydiscount@getBrandList' ]);
    # 不可报名活动详情
    route::get('activity/noregistered_detail.html', [ 'as' => 'topshop.activity.noregistered_detail', 'uses' => 'topshop_ctl_promotion_activity@noregistered_detail' ]);
    # 活动报名表单
    route::get('activity/canregistered_apply.html', [ 'as' => 'topshop.activity.canregistered_apply', 'uses' => 'topshop_ctl_promotion_activity@canregistered_apply' ]);
    # 保存活动报名表单
    route::post('activity/canregistered_apply_save.html', [ 'as' => 'topshop.activity.canregistered_apply_save', 'uses' => 'topshop_ctl_promotion_activity@canregistered_apply_save' ]);
    # 根据活动搜索报名的商品
    route::post('activity/activityItemSearch.html', [ 'as' => 'topshop.activity.itemsearch', 'uses' => 'topshop_ctl_promotion_activity@searchItem' ]);
    # 入驻申请页
    route::post('apply.html', [ 'as' => 'topshop.apply.create', 'uses' => 'topshop_ctl_enterapply@apply' ]);
    # 入驻申请页保存
    route::post('apply/save.html', [ 'as' => 'topshop.apply.save', 'uses' => 'topshop_ctl_enterapply@saveApply' ]);
    # 入驻申请更改
    route::get('apply/update.html', [ 'as' => 'topshop.apply.update', 'uses' => 'topshop_ctl_enterapply@updateApply' ]);
    # 入驻申请查看
    route::get('apply/checkplan.html', [ 'as' => 'topshop.apply.checkplan', 'uses' => 'topshop_ctl_enterapply@checkPlan' ]);
    # 入驻申请-ajax请求类目下的品牌
    route::post('ajax/cat/brand.html', [ 'as' => 'topshop.ajax.catbrand', 'uses' => 'topshop_ctl_enterapply@ajaxCatBrand' ]);
    # 获取自然属性页面
    route::post('natureprops.html', [ 'as' => 'toputil.syscat.nature', 'uses' => 'topshop_ctl_sku@getNatureProps' ]);
    #获取属性页面 by litong
    route::post('proprs.html', [  'uses' => 'topshop_ctl_sku@getprop' ]);
    route::post('Item_Add.html', [  'uses' => 'topshop_ctl_item@addItem' ]);
    route::post('Item_update.html', [  'uses' => 'topshop_ctl_item@updateItem' ]);
    
 
   
    # 获取详细参数页面
    route::post('params.html', [ 'as' => 'toputil.syscat.params', 'uses' => 'topshop_ctl_sku@getParams' ]);
    # 获取销售属性页面
    route::post('spec/props.html', [ 'as' => 'toputil.syscat.spec.props', 'uses' => 'topshop_ctl_sku@getSpecProps' ]);
    # 获取销售属性选择信息
    route::post('spec/selectprops.html', [ 'as' => 'toputil.syscat.spec.selectprops', 'uses' => 'topshop_ctl_sku@set_spec_index' ]);
    # 商家后台报表
    route::post('sysstat/sysstat.html', [ 'as' => 'topshop.sysstat.sysstat', 'uses' => 'topshop_ctl_sysstat_sysstat@ajaxTrade' ]);
    route::post('sysstat/stattrade.html', [ 'as' => 'topshop.sysstat.stattrade', 'uses' => 'topshop_ctl_sysstat_stattrade@ajaxTrade' ]);
    route::post('sysstat/sysbusiness.html', [ 'as' => 'topshop.sysstat.sysbusiness', 'uses' => 'topshop_ctl_sysstat_sysbusiness@ajaxTrade' ]);
    route::post('sysstat/itemtrade.html', [ 'as' => 'topshop.sysstat.itemtrade', 'uses' => 'topshop_ctl_sysstat_itemtrade@ajaxTrade' ]);
    # 商家发货
    route::group(array('middleware'=>'topshop_middleware_developerMode'), function() {
        route::get('trade/godelivery.html', [ 'as' => 'topshop.trade.godelivery', 'uses' => 'topshop_ctl_trade_flow@godelivery' ]);
        route::post('trade/dodelivery.html', [ 'as' => 'topshop.trade.dodelivery', 'uses' => 'topshop_ctl_trade_flow@dodelivery' ]);
    });

    //wap配置
    route::post('wap/searchItem.html', [ 'as' => 'topshop.wap.decorate.searchItem', 'uses' => 'topshop_ctl_wap_decorate@searchItem' ]);
    route::post('wap/getBrandList.html', [ 'as' => 'topshop.wap.decorate.getBrandList', 'uses' => 'topshop_ctl_wap_decorate@getBrandList' ]);
    #意见反馈
    route::post('feedback.html', [ 'as' => 'topshop.feedback', 'uses' => 'topshop_ctl_index@feedback' ]);

    #编辑常用菜单
    route::post('common/user/menu.html', [ 'as' => 'topshop.commonUserMenu', 'uses' => 'topshop_ctl_menu@index' ]);

    route::get('export.html', [ 'as' => 'toputil.export.view', 'uses' => 'topshop_ctl_export@view' ]);
    route::post('export.html', [ 'as' => 'toputil.export.do', 'uses' => 'topshop_ctl_export@export' ]);

    $menus = config::get('shop');
    foreach($menus as $subMenus)
    {
        foreach($subMenus['menu'] as $menu)
        {
            $method = array_key_exists('method', $menu) ? strtolower($menu['mehtod']) : 'get';
            $parameters = array($menu['url'], ['as' => $menu['as'], 'uses' => $menu['action'], 'middleware'=>$menu['middleware']]);
            forward_static_call_array(array('route', $menu['method']), $parameters);
        }
    }
});

/*
|--------------------------------------------------------------------------
| 店铺通用显示数据处理
|--------------------------------------------------------------------------
 */
route::group(array('prefix' => 'utils'), function() {
    # 系统分类
    route::post('syscat.html', [ 'as' => 'toputil.syscat', 'uses' => 'toputil_ctl_syscat@getChildrenCatList' ]);
    route::get('vcode.html', [ 'as' => 'toputil.vcode', 'uses' => 'toputil_ctl_vcode@gen_vcode' ]);
    route::post('util/upload_images.html', [ 'as' => 'toputil.uploadImages', 'uses' => 'toputil_ctl_image@uploadImages' ]);
     route::post('util/upload_imageses.html', [ 'as' => 'toputil.uploadImages', 'uses' => 'toputil_ctl_image@uploadImageses' ]);
    route::get('util/item_pic.html', [ 'as' => 'toputil.getDefaultItemPic', 'uses' => 'toputil_ctl_image@getItemPic' ]);
});

/*
|--------------------------------------------------------------------------
| 后台通用route
|--------------------------------------------------------------------------
 */
route::group(array('prefix' => 'shopadmin'), function() {

    # 系统分类
    route::match(array('GET', 'POST'), '/', array('as' => 'shopadmin', 'uses' => 'desktop_router@dispatch'));
});

/*
|--------------------------------------------------------------------------
| setup
|--------------------------------------------------------------------------
 */
route::group(array('prefix' => 'setup'), function() {
    # 安装首页
    route::match(array('GET', 'POST'), '/', ['as' => 'setup', 'uses' => 'setup_ctl_default@index']);
    # 安装页
    route::match(array('GET', 'POST'), '/default/process', ['uses' => 'setup_ctl_default@process']);
    # 命令行安装
    route::match(array('GET', 'POST'), '/default/install_app', ['uses' => 'setup_ctl_default@install_app']);
    # console
    route::match(array('GET', 'POST'), '/default/console', ['uses' => 'setup_ctl_default@console']);
    # 激活
    route::match(array('GET', 'POST'), '/default/active', ['uses' => 'setup_ctl_default@active']);
    # 激活成功
    route::match(array('GET', 'POST'), '/default/success', ['uses' => 'setup_ctl_default@success']);
    # 环境初始化
    route::match(array('GET', 'POST'), '/default/initenv', ['uses' => 'setup_ctl_default@initenv']);
    # 女装初始化数据
    route::match(array('GET', 'POST'), '/default/install_demodata', ['uses' => 'setup_ctl_default@install_demodata']);
    route::match(array('GET', 'POST'), '/default/setuptools', ['uses' => 'setup_ctl_default@setuptools']);

});
