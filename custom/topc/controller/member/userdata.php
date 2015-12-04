<?php
class topc_ctl_member_userdata extends topc_ctl_member{
    public function index()
    {

        $userInfo = userAuth::getUserInfo();
        $params["user_id"]=$userInfo["userId"];
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        if($shopInfo == null){
            $shopInfo["shop_id"] = 0;
        }

        if(!$shopInfo){$pagedata["IsInfo"]=1;}else{$pagedata["IsInfo"]=0;}
        $sellerInfo=app::get("sysshop")->model("seller")->getRow("*",array("login_account"=>$userInfo["login_account"]));
        $shoptype=app::get("sysshop")->model("shop_type")->getList("*",array("use_type"=>0,"shop_type"=>0,"status"=>1));
        $shophangye=app::get("sysshop")->model("shop_type")->getList("*",array("use_type"=>1,"shop_type"=>0,"status"=>1));
        $shopxingzhi=app::get("sysshop")->model("shop_type")->getList("*",array("use_type"=>2,"shop_type"=>0,"status"=>1));
        $shopchanping=app::get("sysshop")->model("shop_type")->getList("*",array("use_type"=>3,"shop_type"=>0,"status"=>1));
        $shopyuanyin=app::get("sysshop")->model("shop_type")->getList("*",array("use_type"=>4,"shop_type"=>0,"status"=>1));
        $shopguimo=app::get("sysshop")->model("shop_type")->getList("*",array("use_type"=>5,"shop_type"=>0,"status"=>1));
        $shopcat=app::get("syscategory")->model("cat")->getList("*",array("level"=>1));

        $shop_info = app::get("sysshop")->model("shop_info")->getRow("*",array("shop_id"=>$shopInfo["shop_id"]));
        $product_name = app::get("sysshop")->model("shop_product")->getList("*",array("shop_id"=>$shopInfo["shop_id"]));
        $shop_yuanyin = app::get("sysshop")->model("shop_yuanyin")->getList("*",array("shop_id"=>$shopInfo["shop_id"]));
        $shop_rel_lv1cat = app::get("sysshop")->model("shop_rel_lv1cat")->getList("*",array("shop_id"=>$shopInfo["shop_id"]));
        
        $shop_certificate = app::get("sysshop")->model("shop_certificate")->getList("*",array("shop_id"=>$shopInfo["shop_id"]));
        $shop_manage = app::get("sysshop")->model("shop_manage")->getList("*",array("shop_id"=>$shopInfo["shop_id"]));
        //会员账号信息
        $pagedata["userInfo"]=$userInfo;
        //企业信息
        $pagedata["shopInfo"]=$shopInfo;//shop表数据

        $shop_info["shop_hangye"] = trim($shop_info["shop_hangye"]);
        $pagedata["shop_info"]=$shop_info;//shop_info表数据
        if($shop_info != null){
            $pagedata["shop_info"]["establish_date"] = date('Y-m-d',$pagedata["shop_info"]["establish_date"]);
        }
        
        //公司位置
        // $pagedata["area"]=array($pagedata["shop_info"]["company_area"]);
        //企业账号信息
        $pagedata["sellerInfo"]=$sellerInfo;
        //企业类型信息
        $pagedata["shoptype"]=$shoptype;
        //判断shoptype是否为自定义
        $pagedata["shoptype_self"] = 1;
        foreach ($shoptype as $key => $value) {
            if($value["name"] === $shopInfo["shop_type"]){
                $pagedata["shoptype_self"] = 0;
                break;
            }
        }
        //规模
        $pagedata["shopguimo"]=$shopguimo;
        //行业信息
        foreach ($shophangye as $key => $value) {
            $shophangye[$key]["name"] = trim($value["name"]);
        }
        $pagedata["shophangye"]=$shophangye;
        //判断行业是否为自定义
        $pagedata["shophangye_self"] = 1;
        foreach ($shophangye as $key => $value) {
            if(trim($value["name"]) === trim($shop_info["shop_hangye"])){
                $pagedata["shophangye_self"] = 0;
                break;
            }
        }

        //企业性质
        $pagedata["shopxingzhi"]=$shopxingzhi;
        //判断已保存的企业性质是数据库里的还是自定义的
        foreach ($shopxingzhi as $key => $value) {
            $pagedata["shopxingzhi_self"] = 1;
            if($value["name"] === $shop_info["shop_xingzhi"]){
                $pagedata["shopxingzhi_self"] = 0;
                break;
            }

        }
        //主要产品
        $pagedata["shopchanping"]=$shopchanping;
        $pagedata["product_name"]=$product_name;
        if(empty($product_name)){
            $pagedata["ishas_shopchanping"] = 0;
        }else{
            $pagedata["ishas_shopchanping"] = 1;
        }
        //注册原因
        $pagedata["shopchanping"]=$shopchanping;
        //分类
        $pagedata["shopcat"]=$shopcat;
        //原因
        $pagedata["shopyuanyin"]=$shopyuanyin;
        $pagedata["shop_yuanyin"]=$shop_yuanyin;
        if(empty($shop_yuanyin)){
            $pagedata["ishas_shopyuanyin"] = 0;
        }else{
            $pagedata["ishas_shopyuanyin"] = 1;
        }
        //处置能力
        $pagedata["shop_rel_lv1cat"] = $shop_rel_lv1cat;
        //处置能力证书
        $pagedata["shop_manage"] = $shop_manage;
        if(!empty($shop_manage)){
            $pagedata["shop_manage_ishas"] = 1;
        }else{
            $pagedata["shop_manage_ishas"] = 0;
        }
        //资质证照
        $pagedata["shop_certificate"] = $shop_certificate;
        if(!empty($shop_certificate)){
            $pagedata["shop_certificate_ishas"] = 1;
        }else{
            $pagedata["shop_certificate_ishas"] = 0;
        }
        
        $pagedata['action'] = 'topc_ctl_member_userdata@index';
        $this->action_view = "userdata/index.html";
        return $this->output($pagedata);
    }

    public function saveShop()
    {
            // $db->query('START TRANSACTION');
            $db = app::get('sysshop')->database();
            $db->beginTransaction();

            $shopMdl=app::get("sysshop")->model("shop");
            $shopInfoMdl=app::get("sysshop")->model("shop_info");
            $shopCatMdl=app::get("sysshop")->model("shop_rel_lv1cat");
            $shopCertificateMdl=app::get("sysshop")->model("shop_certificate");
            $shopManageMdl=app::get("sysshop")->model("shop_manage");
            $shopProductMdl=app::get("sysshop")->model("shop_product");
            $shopYuanyinMdl=app::get("sysshop")->model("shop_yuanyin");

            $postdata=$_POST;
            try{

            $seller=$postdata['seller'];
            $area=$postdata['area'];
            if(!$area){$area="100";}

            $shopInfo=$postdata['shop_info'];
            $establish_date=$shopInfo['establish_date'];
            $time=strtotime($establish_date);
            if($time<0){
                  return $this->splash('error',null,'请填写正确的公司成立时间！');
            }
            $sellerInfo=app::get("sysshop")->model("seller")->getRow("*",array("seller_id"=>$seller["seller_id"]));
            //shop表
            $shop=$postdata['shop'];
            // if($shop["shop_id"]){return $this->splash('error',null,"你已经完成过企业信息完善，请勿重复提交！");}
            // $shop["shop_id"]="";
            if(empty($shop["shop_id"])){
                unset($shop["shop_id"]);
            }
            $shop["seller_id"]=$seller["seller_id"];
            if(empty($shop["shop_logo"])){
                $shop["shop_logo"]="";
            }
            $shop["shop_logo"]=$shop["shop_logo"][0];
            $shop["status"]="dead";
            $shop["shopuser_name"]=$sellerInfo["name"];
            $shop["email"]=$sellerInfo["email"];
            $shop["mobile"]=$sellerInfo["mobile"];
            $shop["shop_area"]=$area[0];
            $shop["shop_addr"]=$shopInfo["company_addr"];
            $shop['open_time']=time();
            if(!empty($shop["shop_type_use"])){
                $shop['shop_type'] = $shop["shop_type_use"];
            }
            $shopMdl->save($shop);
            
            $shop_id=$shop["shop_id"];
           //更新 seller 表
            $sellerInfo["shop_id"]=$shop_id;
            app::get("sysshop")->model("seller")->save($sellerInfo);
            
            //shopInfo表
            $shopInfo=$postdata['shop_info'];

            if(empty($shopInfo["info_id"])){
                unset($shopInfo["info_id"]);
            }
            $shopInfo["seller_id"]=$seller["seller_id"];
            $shopInfo["shop_id"]=$shop["shop_id"];
            $shopInfo["company_name"]=$sellerInfo["name"];
            $shopInfo["seller_id"]=$seller["seller_id"];
            if(empty($shopInfo["license_img"])){
                $shopInfo["license_img"] = "";
                return $this->splash('error',null,"必须上传营业执照副本复印件");
            }else{
                $shopInfo["license_img"]=$shopInfo["license_img"][0];
            }
            if(empty($shopInfo['corporate_identity_img'])){
                $shopInfo['corporate_identity_img'] = "";
            }else{
                $shopInfo['corporate_identity_img']=$shopInfo['corporate_identity_img'][0];
            }
            if(empty($shopInfo['tissue_code_img'])){
                $shopInfo['tissue_code_img'] = "";
            }else{
                $shopInfo['tissue_code_img']=$shopInfo['tissue_code_img'][0];
            }
            $shopInfo["enroll_capital"]=$shopInfo['shop_ziben'];
            $shopInfo["company_area"]=$area[0];
            
            $shopInfo['establish_date']=$time;

            if(!empty($shopInfo['shop_hangye_use'])){
                $shopInfo["shop_hangye"]=$shopInfo['shop_hangye_use'];
            }
            if(!empty($shopInfo['shop_xingzhi_use'])){
                $shopInfo["shop_xingzhi"] = $shopInfo['shop_xingzhi_use'];
            }
            $shopInfoMdl->save($shopInfo);

            $info_id=$shopInfo["info_id"];
            //产品
            $product=$postdata['product'];
            foreach ($product["product_name"] as $key => $value) {
                $pro["shop_product_id"]=NULL;
                $pro['product_name']=$value;
                $pro["shop_id"]=$shop["shop_id"];
                $pro["modified_time"]=time();
                $b=1;
                $shopProductMdl->save($pro);
            }
            foreach ($product['shop_chanping_use'] as $key => $value) {
                $pro["shop_product_id"]=NULL;
                $pro['product_name']=$value;
                $pro["shop_id"]=$shop["shop_id"];
                $pro["modified_time"]=time();
                $b=1;
                $shopProductMdl->save($pro);
            }
            
            
            //注册原因
            $shop_yuanyin=$postdata['shop_yaunyin'];
            foreach ($shop_yuanyin['shopyuanyin'] as $key => $value) {
                $yy["shop_yuanyin_id"]=null;
                $yy['yuanyin']=$value;
                $yy["shop_id"]=$shop["shop_id"];
                $yy["modified_time"]=time();
                $c=1;
                $shopYuanyinMdl->save($yy);
            }
            foreach ($shop_yuanyin['shopyuanyin_use'] as $key => $value) {
                $yy["shop_yuanyin_id"]=null;
                $yy['yuanyin']=$value;
                $yy["shop_id"]=$shop["shop_id"];
                $yy["modified_time"]=time();
                $c=1;
                $shopYuanyinMdl->save($yy);
            }
            
            //证书
            //先删除所有数据再插入
            $shopCertificateMdl->delete(array("shop_id"=>$shop["shop_id"]));

            $shop_certificate=$postdata['shop_certificate'];
            foreach ($shop_certificate['certificate_img'] as $key => $value) {
                $certificate["certificate_id"]=null;
                $certificate["shop_id"]=$shop["shop_id"];
                $certificate["certificate_img"]=$value;
                $certificate["modified_time"]=time();
                $d=1;       
                $shopCertificateMdl->save($certificate);
            }
            
            $shopCatMdl->delete(array("shop_id"=>$shop["shop_id"]));
            $shop_rel_lv1cat=$postdata['shop_rel_lv1cat'];
            foreach ($shop_rel_lv1cat as $key => $value) {
                $cat["rel_id"]=null;
                $cat["shop_id"]=$shop["shop_id"];
                $cat["cat_id"]=$value;
                $shopCatMdl->save($cat);
                $e=1;
            }

            //处置能力资质
            $shopManageMdl->delete(array("shop_id"=>$shop["shop_id"]));
            $shop_manage=$postdata['shop_manage'];
            foreach ($shop_manage['manage_img'] as $key => $value) {
                $manage["manage_id"]=null;
                $manage['manage_img']=$value;
                $manage["shop_id"]=$shop["shop_id"];
                $manage["modified_time"]=time();
                $c=1;
                $shopManageMdl->save($manage);
            }
             
            }catch(Exception $e){
                $db->rollback();
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
            $db->commit();
            $url = url::action('topc_ctl_member@index');
            return $this->splash('success', $url,'企业入住信息完善成功，请静待审核！');
    }

}