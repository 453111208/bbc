<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_ctl_shopcenter extends topc_controller
{

    public $limit = 20;

    public function __construct($app)
    {
        parent::__construct();
        $this->app = $app;
        $this->setLayoutFlag('shopcenter');

        if( !$this->__checkShop(input::get('shop_id')) )
        {
             //= input::get('shop_id');
            $shopid=input::get('shop_id');
            $shopdata = app::get('topc')->rpcCall('shop.get',array('shop_id'=>$shopid));
            
            $pagedata['shopid']=$shopid;
            $pagedata['is_center']=$shopdata["is_shopcenter"];
            $pagedata['status']=$shopdata["status"];
            $this->page('topc/shop/error.html', $pagedata)->send();
        }
    }

    /**
     * 检查shopId是否存在
     *
     * @param int $shopId 企业ID
     */
    private function __checkShop($shopId)
    {
        $shopId = intval($shopId);
        if($shopId)
        {
            $shopdata = app::get('topc')->rpcCall('shop.get',array('shop_id'=>$shopId));
            if( empty($shopdata) || $shopdata['status'] == "dead" ||$shopdata['is_shopcenter']=="0")
            {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 获取企业模板页面头部共用部分的数据
     *
     * @param int $shopId 企业ID
     * @return array
     */
    private function __common($shopId)
    {
        $shopId = intval($shopId);

        //企业信息
        $shopdata = app::get('topc')->rpcCall('shop.get',array('shop_id'=>$shopId));
        
        $commonData['shopdata'] = $shopdata;
        $shopInfodata = app::get("sysshop")->model("shop_info")->getList("*",array("shop_id"=>$shopId));
        $commonData["shopInfo"] = $shopInfodata[0];
        $seller_id = app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shopId))["seller_id"];
        $seller_type=app::get("sysshop")->model("seller")->getRow("*",array("shop_id"=>$shopId))["seller_type"];
        $commonData["seller_type"]=$seller_type;
        $login_account=app::get("sysshop")->model("account")->getRow("*",array("seller_id"=>$seller_id))["login_account"];
        $userid=app::get("sysuser")->model("account")->getRow("*",array("login_account"=>$login_account))["user_id"];
        $sql = "select  id ,variety_name, type from (
                    select require_id as id,variety_name  ,1 as type,create_time from sysspfb_requireInfo where user_id = ".$userid." 
                    union ALL
                    select supply_id,variety_name,2,create_time from sysspfb_supplyInfo where user_id=".$userid." )a
                    order by create_time desc";
        $gqlistall =app::get("base")->database()->executeQuery($sql)->fetchAll();
        $gqlist=array_slice($gqlistall,0,4);
        $commonData["gqList"]=$gqlist;
        $commonData["userid"]=$userid;
        $gqcount=count($gqlistall);
        //供求数量
        $commonData["gqcount"]=$gqcount;
        //交易量
        $trandeSql = "select title,id,type,create_time from(
                            select trading_title title,bidding_id id ,'1' type,create_time from sysshoppubt_biddings where shop_id=".$shopId."
                             and  isok<>1 and is_through=1 
                            union ALL 
                            SELECT trading_title,tender_id id,'2' type,create_time from sysshoppubt_tender where shop_id=".$shopId." and   isok<>1 and is_through=1)a
                            ORDER BY create_time desc;";
       $trandeList = app::get("base")->database()->executeQuery($trandeSql)->fetchAll();      
        $biddingAry = array();
        $tendAry = array();
       foreach ($trandeList as $key => $value) {
           if($value["type"]=="1"){
                $tenderList=app::get("sysshoppubt")->model("tradeorder")->getList("*",array("bidding_id"=>$value['id']));
                $trandeList[$key]["Tcount"]=count($tenderList);
                array_push($biddingAry,$values);
           }
           else{
                $tenderList=app::get("sysshoppubt")->model("tenderenter")->getList("*",array("tender_id"=>$value['id']));
                $trandeList[$key]["Tcount"]=count($tenderList);
                array_push($tendAry,$values);
           }
       }
       foreach ($trandeList as $key => $value) {
            $trandeList[$key]["create_time"] = date('Y-m-d',$value["create_time"]);
        }
       $commonData["trandeList"] =array_slice($trandeList,0,6); 
       $commonData["biddcount"]=count($biddingAry);
       $commonData["tendcount"]=count($tendAry);
       $b=  $trandeList[0];
       //标准商品产品
        $BzitemSql = "select * from sysitem_item where shop_id=".$shopId ." and type=0 and product_type=0 and is_shop_show=1";
        $BzItemList =  app::get("base")->database()->executeQuery($BzitemSql)->fetchAll();
        $commonData["BzItemList"] = $BzItemList;
        //标准商品生产设备
        $BzitemSbSql = "select * from sysitem_item where shop_id=".$shopId ." and type=0 and product_type=1 and is_shop_show=1";
        $BzItemSbList =  app::get("base")->database()->executeQuery($BzitemSbSql)->fetchAll();
        $commonData["BzItemSbList"] = $BzItemSbList;
       
        //非标准商品 固废
        $NBzitemSql = "select * from sysitem_item where shop_id=".$shopId ." and type=1 and product_type=2 and is_shop_show=1";
        $NBzItemList =  app::get("base")->database()->executeQuery($NBzitemSql)->fetchAll();
        $commonData["NBzItemList"] = $NBzItemList;
        //非标准商品 储存状态
        $NBzitemCcSql = "select * from sysitem_item where shop_id=".$shopId ." and type=1 and product_type=3 and is_shop_show=1";
        $NBzItemCcList =  app::get("base")->database()->executeQuery($NBzitemCcSql)->fetchAll();
        $commonData["NBzItemCcList"] = $NBzItemCcList;
        
        //企业招牌背景色
        $commonData['background_image'] = shopWidgets::getWidgetsData('shopsign',$shopId);
        $a = $commonData['background_image'];
        //企业菜单
        //$navData = shopWidgets::getWidgetsData('nav',$shopId);
        //$commonData['navdata'] = $navData;

        //获取默认图片信息
        $commonData['defaultImageId']= app::get('image')->getConf('image.set');
        if( userAuth::check() )
        {
            $commonData['nologin'] = 1;
        }
        //资质证书图片信息
        $qualificationsSql = "select shop_id,certificate_img 'img',certificate 'imgname' from sysshop_shop_certificate where shop_id=".$shopId." UNION ALL select shop_id,manage_img 'img',manage 'imgname' from sysshop_shop_manage where shop_id=".$shopId;
        $QualificationsImgs =  app::get("base")->database()->executeQuery($qualificationsSql)->fetchAll();
        $commonData["QualificationsImgList"] = $QualificationsImgs;

        //产品出售
        $projectitemSql = "select * from sysshoppubt_sprodrelease sd join sysshoppubt_standard_item si on sd.uniqid=si.uniqid join sysitem_item st on st.item_id=si.item_id where sd.shop_id=".$shopId;
        $projectitemImgs =  app::get("base")->database()->executeQuery($projectitemSql)->fetchAll();
        $commonData["projectitemList"] = $projectitemImgs;
        //主营
        $product=app::get("sysshop")->model("shop_product")->getList("*",array("shop_id"=>$shopId));
        $commonData["product"]=$product;
        return $commonData;
    }

    //企业首页
    public function index()
    {
        //$this->setLayoutFlag('shopcenter');
        $shopId = input::get('shop_id');
        if($shopId){
        $shopdata = app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shopId));
        $is_open=$shopdata["status"];
        $is_center=$shopdata["is_shopcenter"];
        $pagedata = $this->__common($shopId);
        if(userAuth::check()){
        $pagedata['nologin']=1;
        }
        //企业自定义区域
        // $params = shopWidgets::getWidgetsData('custom',$shopId);
        // if($params)
        // {
        //     $pagedata['params'] = $params['custom'];
        // }
        $a=$pagedata['shopdata'];
        $b=1;
          if($is_open=="active"&&$is_center=="1"){
           return $this->page('topc/shop/index.html', $pagedata);
         }
        
    }
    else{
         return $this->splash('error',null,"请先完善企业入驻信息！");
    }

    }

    //产品信息
    public function productinfo(){
        $shopId = input::get('shop_id');
        //$shopdata = app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shopId));
        $pagedata = $this->__common($shopId);
        //产品出售
        $projectitemSql = "select * from sysshoppubt_sprodrelease sd join sysshoppubt_standard_item si on sd.uniqid=si.uniqid join sysitem_item st on st.item_id=si.item_id where sd.shop_id=".$shopId;
        $projectitemImgs =  app::get("base")->database()->executeQuery($projectitemSql)->fetchAll();
        $pagedata["projectitemList"] = $projectitemImgs;
        //产品总数
        $projectcountSql = "select count(*) 'projectnum' from sysshoppubt_sprodrelease sd join sysshoppubt_standard_item si on sd.uniqid=si.uniqid join sysitem_item st on st.item_id=si.item_id where sd.shop_id=".$shopId;
        $projectitemcount =  app::get("base")->database()->executeQuery($projectcountSql)->fetchAll();
        $pagedata["projectitemcount"] = $projectitemcount;

        return $this->page('topc/shop/product.html',$pagedata);
    }
    
    //企业档案
    public function zizhi()
    {
        $shopId = input::get('shop_id');
        //$shopdata = app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shopId));
        $pagedata = $this->__common($shopId);
        //公司信息
        $shopSql = "select * from sysshop_shop where shop_id=".$shopId;
        $shops =  app::get("base")->database()->executeQuery($shopSql)->fetchAll();
        $pagedata["shopList"] = $shops;
        //公司详细信息
        $shopinfoSql = "select * from sysshop_shop_info where shop_id=".$shopId;
        $shopinfos =  app::get("base")->database()->executeQuery($shopinfoSql)->fetchAll();
        $pagedata["shopinfoList"] = $shopinfos;

        //资质证书图片信息
        $qualificationsSql = "select shop_id,certificate_img 'img',certificate 'imgname' from sysshop_shop_certificate where shop_id=".$shopId." UNION ALL select shop_id,manage_img 'img',manage 'imgname' from sysshop_shop_manage where shop_id=".$shopId;
        $QualificationsImgs =  app::get("base")->database()->executeQuery($qualificationsSql)->fetchAll();
        $pagedata["QualificationsImgList"] = $QualificationsImgs;

        //企业资讯
        $articleSql = "select (@rowNO := @rowNo+1) AS rowno,a.* from (select st.* from sysshop_seller sr join sysshop_account sa on sr.seller_id=sa.seller_id join sysuser_account so on sa.login_account=so.login_account join sysinfo_article st on so.user_id=st.user_id where sr.shop_id=".$shopId.") a,(select @rowNO :=0) b";
        $articles =  app::get("base")->database()->executeQuery($articleSql)->fetchAll();
        $pagedata["articleList"] = $articles;


        return $this->page('topc/shop/zizhi.html',$pagedata);# code...
    }

    //固废信息
    public function gfinfo(){
        $shopId = input::get('shop_id');
        //$shopdata = app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shopId));
        $pagedata = $this->__common($shopId);
        //固废
        $shopitemSql = "select * from sysitem_item where shop_id=".$shopId." and type=1 and product_type=2 and state=1";
        $shopitems =  app::get("base")->database()->executeQuery($shopitemSql)->fetchAll();
        $pagedata["shopitemList"] = $shopitems;
        //储存状态
        $shopitemSql1 = "select * from sysitem_item where shop_id=".$shopId." and type=1 and product_type=3 and state=1";
        $shopitems1 =  app::get("base")->database()->executeQuery($shopitemSql1)->fetchAll();
        $pagedata["StorageitemList"] = $shopitems1;

        return $this->page('topc/shop/gufei.html',$pagedata);
    }

    //技术设备信息
    public function equipmentinfo(){
        $shopId = input::get('shop_id');
        //$shopdata = app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shopId));
        $pagedata = $this->__common($shopId);
        //成品
        $productSql = "select * from sysitem_item where shop_id=".$shopId." and type=0 and product_type=0 and state=1";
        $productitems =  app::get("base")->database()->executeQuery($productSql)->fetchAll();
        $pagedata["productList"] = $productitems;
        //生产设备
        $equipmentSql = "select * from sysitem_item where shop_id=".$shopId." and type=0 and product_type=1 and state=1";
        $equipmentitems =  app::get("base")->database()->executeQuery($equipmentSql)->fetchAll();
        $pagedata["equipmentList"] = $equipmentitems;

        return $this->page('topc/shop/equipment.html',$pagedata);
    }
    //联系方式
     public function shopmap(){
        $shopId = input::get('shop_id');
        //$shopdata = app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shopId));
        $pagedata = $this->__common($shopId);
        //成品
        
        return $this->page('topc/shop/map_index.html',$pagedata);
    }
    //交易记录
    public function shoptrading()
    {
        $shopId = input::get('shop_id');
        $params["shop_id"] = $shopId;
        $userInfo=app::get('topc')->rpcCall('user.get.userInfo',$params,'buyer');
        $pagedata = $this->__common($shopId);
        
        $supplylist = app::get("sysspfb")->model("supplyInfo")->getList("*",array("user_id"=>$userInfo["user_id"]));
        $requirelist = app::get("sysspfb")->model("requireInfo")->getList("*",array("user_id"=>$userInfo["user_id"]));
        $biddingslist = app::get("sysshoppubt")->model("biddings")->getList("*",array("shop_id"=>$shopId));
        $tenderlist = app::get("sysshoppubt")->model("tender")->getList("*",array("shop_id"=>$shopId));

        $enquireinfolist = app::get("sysspfb")->model("enquireinfo")->getList("*",array());
        //招标
        $tenderenterlist = app::get("sysshoppubt")->model("tenderenter")->getList("*",array("type"=>1));
        //竞价
        $tradeorderlist = app::get("sysshoppubt")->model("tradeorder")->getList("*",array());

        //供应
        foreach ($supplylist as $key => $value) {
            $supplylist[$key]["create_time"] = date('Y-m-d',$value["create_time"]);
            $num = 0;
            foreach ($enquireinfolist as $key1 => $value1) {
                if($value1["ifrequire"] == 1 && $value1["reqsupp_id"] == $value["supply_id"]){
                    $num++;
                }
            }
            $supplylist[$key]["enquirenum"] = $num;
        }
        //求购
        foreach ($requirelist as $key => $value) {
            $requirelist[$key]["create_time"] = date('Y-m-d',$value["create_time"]);
            $num = 0;
            foreach ($enquireinfolist as $key1 => $value1) {
                if($value1["ifrequire"] == 2 && $value1["reqsupp_id"] == $value["require_id"]){
                    $num++;
                }
            }
            $requirelist[$key]["enquirenum"] = $num;
        }
        //竞价
        foreach ($biddingslist as $key => $value) {
            $biddingslist[$key]["create_time"] = date('Y-m-d',$value["create_time"]);
            $num = 0;
            foreach ($tradeorderlist as $key1 => $value1) {
                if($value1["bidding_id"] == $value["bidding_id"]){
                    $num++;
                }
            }
            $biddingslist[$key]["enquirenum"] = $num;
        }
        //招标
        foreach ($tenderlist as $key => $value) {
            $tenderlist[$key]["create_time"] = date('Y-m-d',$value["create_time"]);
            $num = 0;
            foreach ($tenderenterlist as $key1 => $value1) {
                if($value1["tender_id"] == $value["tender_id"]){
                    $num++;
                }
            }
            $tenderlist[$key]["enquirenum"] = $num;
        }

        $pagedata["supplylist"] = $supplylist;
        $pagedata["requirelist"] = $requirelist;
        $pagedata["biddingslist"] = $biddingslist;
        $pagedata["tenderlist"] = $tenderlist;

        $pagedata["supplylistnum"] = count($supplylist);
        $pagedata["requirelistnum"] = count($requirelist);
        $pagedata["biddingslistnum"] = count($biddingslist);
        $pagedata["tenderlistnum"] = count($tenderlist);

        return $this->page('topc/shop/trading_index.html',$pagedata);# code...
    }

//展台收藏
    function ajaxFavshop() {
        $userId = userAuth::id();
        if(!$userId)
        {
            $url = url::action('topc_ctl_passport@signin');
            return $this->splash('error',$url);
        }
        $userId = strval($userId);
        $params['shop_id'] = $_POST['shop_id'];
        $params['user_id'] = $userId;
        $params['create_time'] = time();
        $collect = app::get('sysuser')->model('usercollect');
        $collist = $collect->getList('shop_id',array('shop_id'=>$_POST['shop_id'],'user_id'=>$userId));
        if($collist){
            $del = $collect->delete(array('user_id'=>$userId,'shop_id'=>$_POST['shop_id']));
            if ($del)
            {
            return $this->splash('success',null, app::get('topc')->_('取消收藏成功！'));
            }else{
            return $this->splash('error',null, app::get('topc')->_('取消收藏失败！'));
            }
        }else{
            $db=app::get('sysuser')->database();
            $sql="insert into sysuser_usercollect (user_id,shop_id,create_time) values(".$userId.",".$_POST['shop_id'].",".time().")";
            $db->exec($sql);
            return $this->splash('success',null, app::get('topc')->_('收藏成功！'));
        }
    }

    public function shopCouponList()
    {
        $shopId = input::get('shop_id');
        $pagedata = $this->__common($shopId);

       // 企业优惠券信息,
        $params = array(
            'page_no' => 0,
            'page_size' => 100,
            'fields' => '*',
            'shop_id' => $shopId,
            'platform' => 'pc',
            'is_cansend' => 1,
        );
        $couponListData = app::get('topc')->rpcCall('promotion.coupon.list', $params, 'buyer');
        $pagedata['shopCouponList'] = $couponListData['coupons'];
        $pagedata['file'] = "topc/shop/shopCouponList.html";
        return $this->page('topc/shop/index.html', $pagedata);
    }

    public function getCouponResult()
    {
        $shopId = input::get('shop_id');
        $pagedata = $this->__common($shopId);
        $coupon_id = input::get('coupon_id');
        $pagedata['couponInfo'] = app::get('topc')->rpcCall('promotion.coupon.get', array('coupon_id'=>$coupon_id));
        $pagedata['file'] = "topc/shop/couponResult.html";
        return $this->page('topc/shop/index.html', $pagedata);
    }

    public function getCouponCode()
    {
        $apiData['shop_id'] = $shopId = input::get('shop_id');
        $pagedata = $this->__common($shopId);
        $user_id = userAuth::id();
        if(!$user_id)
        {
            $signinUrl =  url::action('topc_ctl_passport@signin');
            return $this->splash('success', $signinUrl, '', true);
        }
        $coupon_id = input::get('coupon_id');
        if(!$coupon_id)
        {
            return $this->splash('error', '', '领取优惠券参数错误', true);
        }
        try
        {
            $userInfo = app::get('topc')->rpcCall('user.get.info',array('user_id'=>$user_id),'buyer');
            $apiData = array(
                 'coupon_id' => $coupon_id,
                 'user_id' =>$user_id,
                 'shop_id' =>$shopId,
                 'grade_id' =>$userInfo['grade_id'],
            );
            if(app::get('topc')->rpcCall('user.coupon.getCode', $apiData))
            {
                $url = url::action('topc_ctl_shopcenter@getCouponResult', array('coupon_id'=>$coupon_id, 'shop_id'=>$shopId));
                return $this->splash('success', $url, '领取成功', true);
                // $pagedata['couponInfo'] = app::get('topc')->rpcCall('promotion.coupon.get', array('coupon_id'=>$coupon_id));
                // $pagedata['file'] = "topc/shop/couponResult.html";
                // return $this->page('topc/shop/index.html', $pagedata);
            }
            else
            {
                return $this->splash('error', '', '领取失败', true);
            }
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error', '', $msg, true);
        }
    }

}


