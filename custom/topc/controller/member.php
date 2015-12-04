<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_ctl_member extends topc_controller {

    public function __construct(&$app)
    {
        parent::__construct();
        kernel::single('base_session')->start();
        if(!$this->action) $this->action = 'index';
        $this->action_view = $this->action.".html";
        // 检测是否登录
        if( !userAuth::check() )
        {
            redirect::action('topc_ctl_passport@signin')->send();exit;
        }
        $this->limit = 20;
        $this->setLayoutFlag('member');
        $this->passport = kernel::single('topc_passport');
    }

    public function getShopId()
    {
        $userId = userAuth::id();
        $params["user_id"]=$userId;
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        return $shopInfo['shop_id'];
    }

    

    //获取会员定制信息
    public function getCustomizeds($catparent,$cat)
    {
        //$catarray = array();   
        if(!empty($cat)){
            $catarray = explode(',',$cat);
        }
        if($catparent === "1"){//行情
            $hqsql = "select *,data_id 'id',third_sort 'lablestr',concat(SUBSTRING(date,6),area,second_sort) 'lablecontent' from sysinfo_marketdata where 1=1";
            if(!empty($catarray[0])){
                $hqsql.=" and first_sort='";
                $hqsql.=($catarray[0]."'");
            }
            if(!empty($catarray[1])){
                $hqsql.=" and second_sort='";
                $hqsql.=($catarray[1]."'");
            }
            if(!empty($catarray[2])){
                $hqsql.=" and third_sort='";
                $hqsql.=($catarray[2]."'");
            }
            $hqsql.=" order by first_sort desc LIMIT 5";
            $list=app::get("base")->database()->executeQuery($hqsql)->fetchAll();
            foreach ($list as $key => $value){
                $str = $value["date"];
                $str = str_replace('年','-',$str);
                $str = str_replace('月','-',$str);
                $str = str_replace('日','',$str);
                $list[$key]["datepub"] = $str;
                $list[$key]["actionpar"] = "topc_ctl_market@details";
            }
            return  $list;
        }else if($catparent === "2"){//资讯
            $zxsql = "select sa.*,article_id 'id',sn.node_name 'lablestr',title 'lablecontent' from sysinfo_article sa join sysinfo_article_nodes sn on sa.node_id=sn.node_id where 1=1";
            if(!empty($cat)){
                $zxsql.=" and sa.node_id=";
                $zxsql.=$cat;
            }
            $zxsql.=" ORDER BY sa.pubtime desc LIMIT 5";
            $list = app::get("base")->database()->executeQuery($zxsql)->fetchAll();
            foreach ($list as $key => $value) {
                $time = $value["pubtime"];
                $list[$key]["datepub"] = date("Y-m-d ", $time);
            }
            return $list;
        }else if($catparent === "3"){//供求
            $gqsql = "select * from (select supply_id 'id','供应' lablestr,cat_id,cat_name,variety_name 'lablecontent',product_intro,create_time from sysspfb_supplyInfo UNION All select require_id 'id','求购' lablestr,cat_id,cat_name,variety_name 'lablecontent',product_intro,create_time from sysspfb_requireInfo) aaa ORDER BY aaa.create_time desc LIMIT 5";
            $list = app::get("base")->database()->executeQuery($gqsql)->fetchAll();
            foreach ($list as $key => $value) {
                $time = $value["create_time"];
                $list[$key]["datepub"] = date("Y-m-d ", $time);
            }
            return $list;
        }else if($catparent === "4"){//交易
            $jysql = "select * from (select bidding_id 'id','竞价' lablestr,shop_id,uniqid,trading_title 'lablecontent',start_time from sysshoppubt_biddings UNION All select tender_id 'id','招标' lablestr,shop_id,uniqid,trading_title,start_time from sysshoppubt_tender) aaa ORDER BY aaa.start_time desc LIMIT 5";
            $list = app::get("base")->database()->executeQuery($jysql)->fetchAll();
            foreach ($list as $key => $value) {
                $time = $value["start_time"];
                $list[$key]["datepub"] = date("Y-m-d ", $time);
            }
            return $list;
        }else if($catparent === "5"){//名人专家
            $litsql = "select sy.*,literary_id 'id',st.name 'lablestr',sl.literarycat,title 'lablecontent',pubtime 'datepub' from sysexpert_literary sy left join sysexpert_expert st on sy.expert_id=st.expert_id left join sysexpert_literarycat sl on sy.literarycat_id=sl.literarycat_id where 1=1";
            if(!empty($cat)){
                $litsql.=" and sy.literarycat_id=";
                $litsql.=$cat;
            }
            $litsql.=" ORDER BY pubtime desc LIMIT 5";
            $list = app::get("base")->database()->executeQuery($litsql)->fetchAll();
            foreach ($list as $key => $value) {
                $time = $value["pubtime"];
                $list[$key]["datepub"] = date("Y-m-d ", $time);
            }
            return $list;
        }

    }

    //根据兴趣类型查询标题
    public function getpositiondivtitile(&$divpage,$instr_catparent)
    {
        if($instr_catparent === "1"){
            $divpage = "行情信息";
        }else if($instr_catparent === "2"){
            $divpage = "资讯信息";
        }else if($instr_catparent === "3"){
            $divpage = "供求信息";
        }else if($instr_catparent === "4"){
            $divpage = "交易信息";
        }else if($instr_catparent === "5"){
            $divpage = "名人专家";
        }
    }

    //根据定制信息类型查询下级列表
    public function getupsort(){
        // $userId = userAuth::id();
        if($_POST["type"] === "1"){
            $hqsql = "select first_sort 'id',first_sort 'name' from sysinfo_marketdata GROUP BY first_sort";
            return $this->splash('success',null,app::get("base")->database()->executeQuery($hqsql)->fetchAll());
        }else if($_POST["type"] === "2"){
            $zxsql = "select node_id 'id',node_name 'name' from sysinfo_article_nodes where node_depth=2 GROUP BY node_id,node_name";
            return $this->splash('success',null,app::get("base")->database()->executeQuery($zxsql)->fetchAll());
        }else if($_POST["type"] === "3"){
            return $this->splash('nodata',null,null);
        }else if($_POST["type"] === "4"){
            return $this->splash('nodata',null,null);
        }else if($_POST["type"] === "5"){
            $mrzjsql = "select literarycat_id 'id',literarycat 'name' from sysexpert_literarycat group by literarycat_id,literarycat";
            return $this->splash('success',null,app::get("base")->database()->executeQuery($mrzjsql)->fetchAll());
        }else{
            return "";
        }
    }

    public function index()
    {
        $userId = userAuth::id();
        $userInfo = userAuth::getUserInfo();
        $pagedata['userInfo'] = $userInfo;
        
        

        $instrInfo=app::get("sysuser")->model("instr_user")->getList("*",array("user_id"=>$userId));
        //var_dump($instrInfo);
        foreach ($instrInfo as $key => $value) {
            $title = "";
            switch ($value["order_sort"]){
                case '1':
                    $zuoshangList=$this->getCustomizeds($value["instr_catparent"],$value["instr_cat"]);
                    $this->getpositiondivtitile($title,$value["instr_catparent"]);
                    $pagedata["zuoshang"]["title"] = $title;
                    break;
                case '2':
                    $youshangList=$this->getCustomizeds($value["instr_catparent"],$value["instr_cat"]);
                    $this->getpositiondivtitile($title,$value["instr_catparent"]);
                    $pagedata["youshang"]["title"] = $title;
                    break;
                case '3':
                    $zuoxiaList=$this->getCustomizeds($value["instr_catparent"],$value["instr_cat"]);
                    $this->getpositiondivtitile($title,$value["instr_catparent"]);
                    $pagedata["zuoxia"]["title"] = $title;
                    break;
                case '4':
                    $youxiaList=$this->getCustomizeds($value["instr_catparent"],$value["instr_cat"]);
                    $this->getpositiondivtitile($title,$value["instr_catparent"]);
                    $pagedata["youxia"]["title"] = $title;
                    break;

            }
            # code...
        }

        $zuoshangId=app::get("sysuser")->model("instr_user")->getRow("*",array("user_id"=>$userId,"order_sort"=>"1"));
        $pagedata["zuoshang"]["instrID"] = $zuoshangId["instr_id"];
        $pagedata["zuoshang"]["instrcatparent"] = $zuoshangId["instr_catparent"];
        // var_dump($pagedata["zuoshang"]["instrID"]);
        $youshangId=app::get("sysuser")->model("instr_user")->getRow("*",array("user_id"=>$userId,"order_sort"=>"2"));
        $pagedata["youshang"]["instrID"] = $youshangId["instr_id"];
        $pagedata["youshang"]["instrcatparent"] = $youshangId["instr_catparent"];

        $zuoxiaId=app::get("sysuser")->model("instr_user")->getRow("*",array("user_id"=>$userId,"order_sort"=>"3"));
        $pagedata["zuoxia"]["instrID"] = $zuoxiaId["instr_id"];
        $pagedata["zuoxia"]["instrcatparent"] = $zuoxiaId["instr_catparent"];

        $youxiaId=app::get("sysuser")->model("instr_user")->getRow("*",array("user_id"=>$userId,"order_sort"=>"4"));
        $pagedata["youxia"]["instrID"] = $youxiaId["instr_id"];
        $pagedata["youxia"]["instrcatparent"] = $youxiaId["instr_catparent"];
        // var_dump($pagedata["zuoshang"]["title"]);
        $pagedata["zuoshang"]["infolist"]=$zuoshangList;
        $pagedata["youshang"]["infolist"]=$youshangList;
        $pagedata["zuoxia"]["infolist"]=$zuoxiaList;
        $pagedata["youxia"]["infolist"]=$youxiaList;

 

        //会员类型
        $params["user_id"]=$userId;
        $sellertype=app::get('topc')->rpcCall('seller.get.sellertype',$params,'buyer');
        $userInfo=app::get("sysuser")->model("account")->getRow("*",array("user_id"=>$userId));
        $usercom=app::get("sysuser")->model("user")->getRow("*",array("user_id"=>$userId));
        $loginAccount = $userInfo["login_account"];
        $sellerInfo=app::get("sysshop")->model("account")->getList("*",array("login_account"=>$loginAccount));
        $sellerId = $sellerInfo[0]["seller_id"];
        $shopInfo = app::get("sysshop")->model("seller")->getRow("*",array("seller_id"=>$sellerId));
        //企业通知
        $shopnotice=app::get("sysshop")->model("shop_notice")->getList("*",array("shop_id"=>$shopInfo["shop_id"],"is_read"=>false));
        $pagedata["shopnotice"]=count($shopnotice);
        $pagedata["shopInfo"]=$shopInfo;
        $pagedata["seller_type"]=$sellertype;
        $pagedata["userInfo"]=$usercom;
        return $this->output($pagedata);
    }


    public function pubact(){
        // $this->action_view = "pubact.html";
        // return $this->output($pagedata);
        $Id = input::get("id");
        $Catparent = input::get("catparent");
        $type = input::get("type");
        if($Catparent === "1"){//行情
            return redirect::action("topc_ctl_market@details",array("dataId"=>$Id));
        }else if($Catparent === "2"){//资讯
            return redirect::action("topc_ctl_info@getContentInfo",array("article_id"=>$Id));
        }else if($Catparent === "3"){//供求
            if($type === "求购"){
                return redirect::action("topc_ctl_require@index",array("require_id"=>$Id));
            }else if($type === "供应"){
                return redirect::action("topc_ctl_supply@index",array("supply_id"=>$Id));
            }
        }else if($Catparent === "4"){//交易
            if($type === "竞价"){
                return redirect::action("topc_ctl_bidding@index",array("bidding_id"=>$Id));
            }else if($type === "招标"){
                return redirect::action("topc_ctl_tender@index",array("tender_id"=>$Id));
            }
        }else if($Catparent === "5"){//名人专家
            return redirect::action("topc_ctl_expert@literary",array("literaryid"=>$Id));
        }
        return $this->splash('error',"","数据异常");
    }
    
    //选择感兴趣的信息 DLC 2015/11/12
    public function search(){
        $Id = input::get("id");
        $instrid = input::get("instrid");
        // var_dump($instrid);
        $first=array('行情信息','供求信息','资讯信息','交易信息','名人专家');
        
        $firstSql="select sort FROM sysinfo_otherData  group by sort desc;";
        $selectList= app::get("base")->database()->executeQuery($firstSql)->fetchAll();
        foreach ($selectList as $key => $value) {
            $selectList[$key]['first_sort']='行情信息';
        }
        
        //var_dump($selectList);
        
        $pagedata['selectList'] =$selectList;
        $pagedata['first'] =$first;
        $pagedata["order_sort"] = $Id;
        $pagedata["instr_id"] = $instrid;
        
        return view::make('topc/member/search.html',$pagedata);
    }

    public function getSort()
    {
        # code...
    }
      //保存感兴趣的信息 
   public function saveInfo(){
       $userId = userAuth::id();

       if($_POST['instr_catparent'] === "行情信息"){
            $instr_catparent = "1";
            if(empty($_POST['instr_cat'])){
                return $this->splash('error',"","请填写二级分类");
            }
       }else if($_POST['instr_catparent'] === "资讯信息"){
            $instr_catparent = "2";
            if(empty($_POST['instr_cat'])){
                return $this->splash('error',"","请填写二级分类");
            }
       }else if($_POST['instr_catparent'] === "供求信息"){
            $instr_catparent = "3";
       }else if($_POST['instr_catparent'] === "交易信息"){
            $instr_catparent = "4";
       }else if($_POST['instr_catparent'] === "名人专家"){
            $instr_catparent = "5";
            if(empty($_POST['instr_cat'])){
                return $this->splash('error',"","请填写二级分类");
            }
       }
       
       $postdata=$_POST;
       $postdata["user_id"]=$userId;
       $postdata["instr_catparent"] = $instr_catparent;
       try{
        if(empty($postdata["instr_id"])){
            unset($postdata["instr_id"]);
            app::get("sysuser")->model("instr_user")->save($postdata);
        }else{
            app::get("sysuser")->model("instr_user")->save($postdata);
        }
       }
       catch(Exception $e){
        $msg=$e->getMessage();
        return $this->splash('error',"",$msg);
       }
       
       // $savesql = "insert into sysuser_instr_user(user_id,instr_cat,instr_catparent,order_sort) values("+$userId+",'"+$_POST['instr_cat']+"','"+$_POST['instr_catparent']+"','"+$_POST['order_sort']+"')";
       // app::get("base")->database()->execute($hqsql)->fetchAll();
       // //return $this->page('topc/member/index/otherData.html',$pagedata);
       return $this->splash('success',"","保存成功");
   }

    /**
     * @brief 页面输出的统一页面
     *
     * @return html
     */
    public function output($pagedata)
    {
        $pagedata['cpmenu'] = config::get('usermenu');
        //判断会员类型
        $userId = userAuth::id();
        $params["user_id"]=$userId;
        $sellertype=app::get('topc')->rpcCall('seller.get.sellertype',$params,'buyer');
        $pagedata["seller_type"]=$sellertype;
        if( $pagedata['_PAGE_'] ){
            $pagedata['_PAGE_'] = 'topc/member/'.$pagedata['_PAGE_'];
        }else{
            $pagedata['_PAGE_'] = 'topc/member/'.$this->action_view;
        }
        return $this->page('topc/member/main.html', $pagedata);
    }
    /**
     * @brief 会员地址输出
     *
     * @return html
     */
    public function address()
    {
        $userId = userAuth::id();
        $params['user_id'] = $userId;
        $userAddrList = app::get('topc')->rpcCall('user.address.list',$params,'buyer');
        $count = $userAddrList['count'];
        $userAddrList = $userAddrList['list'];
        foreach ($userAddrList as $key => $value) {
            $userAddrList[$key]['area'] = explode(":",$value['area'])[0];
        }

        $pagedata['userAddrList'] = $userAddrList;
        $pagedata['userAddrCount'] = $count;
        $pagedata['action'] = 'topc_ctl_member@address';
        $this->action_view = "address.html";
        return $this->output($pagedata);
    }
    /**
     * @brief 会员地址保存
     *
     * @return html
     */
    public function saveAddress()
    {
        $userId = userAuth::id();
        $postData =utils::_filter_input(input::get());
        $postData['area'] = input::get()['area'][0];
        $postData['user_id'] = $userId;
        $area = app::get('topc')->rpcCall('logistics.area',array('area'=>$postData['area']));
        $validator = validator::make(
            [
             'area' => $area,
             'addr' => $postData['addr'] ,
             'name' => $postData['name'],
             'mobile' => $postData['mobile'],
             'user_id' =>$postData['user_id']
            ],
            [
            'area' => 'required|max:20',
            'addr' => 'required',
            'name' => 'required',
            'mobile' => 'required|mobile',
            'user_id' => 'required'
            ],
            [
             'area' => '地区不存在!',
             'addr' => '会员街道地址必填!',
             'name' => '收货人姓名未填写!',
             'mobile' => '手机号码必填!|手机号码格式不正确!',
             'user_id' => '缺少参数!'
            ]
        );
        if ($validator->fails())
        {
            $messages = $validator->messagesInfo();

            foreach( $messages as $error )
            {
                return $this->splash('error',null,$error[0]);
            }
        }
        $areaId =  str_replace(",","/", $postData['area']);
        $postData['area'] = $area . ':' . $areaId;

        try
        {
            app::get('topc')->rpcCall('user.address.add',$postData,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $url = url::action('topc_ctl_member@address');
        $msg = app::get('topc')->_('添加成功');
        return $this->splash('success',$url,$msg);

    }
    /**
     * @brief 会员地址编辑
     *
     * @return html
     */
    public function ajaxAddrUpdate()
    {
        $params['addr_id'] = input::get('addr_id');
        $params['user_id'] = userAuth::id();
        $addrInfo = app::get('topc')->rpcCall('user.address.info',$params);
        list($regions,$region_id) = explode(':', $addrInfo['area']);
        $addrInfo['area'] = $regions;
        $addrInfo['region_id'] = str_replace('/', ',', $region_id);
        return response::json($addrInfo);
    }

    /**
     * @brief 设置默认会员地址
     *
     * @return html
     */
    public function ajaxAddrDef()
    {
        $userId = userAuth::id();

        $params['addr_id'] = $_POST['addr_id'];
        $params['user_id'] = $userId;

        try
        {
            app::get('topc')->rpcCall('user.address.setDef',$params,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        $msg = app::get('topc')->_('设置成功');
        return $this->splash('success',null,$msg);

    }
    /**
     * @brief 删除会员地址
     *
     * @return html
     */
    public function ajaxDelAddr()
    {
        $userId = userAuth::id();
        $params['addr_id'] = $_POST['addr_id'];
        $params['user_id'] = $userId;

        try
        {
            app::get('topc')->rpcCall('user.address.del',$params,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        $url = url::action('topc_ctl_member@address');
        $msg = app::get('topc')->_('删除成功');
        return $this->splash('success',$url,$msg);
    }

    /**
     * @brief 安全中心
     *
     * @return html
     */
    public function security()
    {
        $userId = userAuth::id();
        //会员信息
        $userInfo = userAuth::getUserInfo();
        $pagedata['userInfo'] = $userInfo;
        $pagedata['action']= 'topc_ctl_member@security';
        $this->action_view = "security.html";
        return $this->output($pagedata);
    }
    /**
     * @brief 会员中心安全中心密码修改
     *
     * @return html
     */
    public function modifyPwd()
    {
        $pagedata['action']= 'topc_ctl_member@modifyPwd';
        $this->action_view = "modifypwd.html";
        return $this->output($pagedata);
    }
    /**
     * @brief 会员中心安全中心密码修改保存
     *
     * @return html
     */
    public function saveModifyPwd()
    {
        try{
            $userId = userAuth::id();
            $postData = utils::_filter_input(input::get());
            $validator = validator::make(
                ['oldpassword' => $postData['old_password'] ,'password' => $postData['new_password'] , 'password_confirmation' =>$postData['confirm_password']],
                ['oldpassword' => 'required' ,'password' => 'min:6|max:20|confirmed','password_confirmation' =>'required'],
                ['oldpassword' => '老密码不能为空！' ,'password' => '密码长度不能小于6位!|密码长度不能大于20位!|输入的密码不一致!','password_confirmation' =>'确认密码不能为空!']
            );
            if ($validator->fails())
            {
                $messages = $validator->messagesInfo();
                foreach( $messages as $error )
                {
                    return $this->splash('error',null,$error[0]);
                }
            }
            $data = array(
                'new_pwd' => $postData['new_password'],
                'confirm_pwd' => $postData['confirm_password'],
                'old_pwd' => $postData['old_password'],
                'user_id' => $userId,
                'type' => "update",
            );
            app::get('topc')->rpcCall('user.pwd.update',$data,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $url = url::action("topc_ctl_member@security");
        $msg = app::get('topc')->_('修改成功');

        return $this->splash('success',$url,$msg);
    }
    /**
     * @brief 会员信息设置
     *
     * @return html
     */
    public function seInfoSet()
    {
        $userId = userAuth::id();
        //会员信息
        $userInfo = userAuth::getUserInfo();
        list($regions,$region_id) = explode(':', $userInfo['area']);
        $userInfo['area'] = $regions;
        $userInfo['region_id'] = str_replace('/', ',', $region_id);
        $pagedata['userInfo'] = $userInfo;
        $pagedata['action']= 'topc_ctl_member@seInfoSet';

        $this->action_view = "infoset.html";
        return $this->output($pagedata);
    }
    /**
     * @brief 会员信息设置保存
     *
     * @return html
     */
    public function saveInfoSet()
    {
        $userId = userAuth::id();
        $postData = input::get();
        $postData['user']['user_id'] = $userId;
        $area = app::get('topc')->rpcCall('logistics.area',array('area'=>$postData['area'][0]));

        $validator = validator::make(
            ['name' => $postData['user']['name'] ,
             'username' => $postData['user']['username'],
             'user_id' => $postData['user']['user_id'],
             'area' => $area
            ],
            ['name' => 'required|min:4|max:20' ,
            'username' => 'required|max:20',
            'user_id' => 'required',
            'area' => 'required'
            ],
            ['name' => '用户昵称不能为空!|用户昵称最少4个字符!|用户昵最多20个字符!' ,
             'username' => '用户姓名不能为空!|用户姓名过长,请输入20个英文或10个汉字!',
             'user_id' => '您还没有登陆，请先登陆!',
             'area' => '地区不存在!'
            ]
        );
        if ($validator->fails())
        {
            $messages = $validator->messagesInfo();

            foreach( $messages as $error )
            {
                return $this->splash('error',null,$error[0]);
            }
        }

        $areaId =  str_replace(",","/", $postData['area'][0]);
        $postData['area'] = $area . ':' . $areaId;
        try
        {
            $data = array('user_id'=>$userId,'data'=>json_encode($postData));
            $result = app::get('topc')->rpcCall('user.basics.update',$data,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $url = url::action('topc_ctl_member@seInfoSet');
        $msg = app::get('topc')->_('修改成功');
        return $this->splash('success',$url,$msg);

    }

    /**
     * @brief 解绑第一步
     *
     * @return html
     */
    public function unVerifyOne()
    {
        $postData = utils::_filter_input(input::get());

        $userId = userAuth::id();
        //会员信息
        $userInfo = userAuth::getUserInfo();

        $pagedata['userInfo']= $userInfo;
        $pagedata['verifyType']= $postData['verifyType'];
        $pagedata['type']= $postData['type'];
        $this->action_view = "unverify.html";
        return $this->output($pagedata);
    }
    //解绑的验证码检测
    public function checkVcode()
    {
        $postData =utils::_filter_input(input::get());
        if(empty($postData['verifycode']) || !base_vcode::verify('topc_unverify', $postData['verifycode']))
        {
            $msg = app::get('topc')->_('验证码填写错误') ;
            return $this->splash('error',null,$msg,true);
        }
        $verifyType = $postData['verifyType'];
        $url = url::action("topc_ctl_member@unVerifyTwo",array('verifyType'=>$verifyType,'op'=>$postData['type']));
        return $this->splash('success',$url,null);

    }
    /**
     * @brief 解绑第二步
     *
     * @return html
     */
    public function unVerifyTwo()
    {
        //会员信息
        $userInfo = userAuth::getUserInfo();
        $postdata = input::get();

        if($postdata['op'] == "delete" && !$userInfo['login_account'] && $postdata['verifyType']=='mobile')
        {
            return redirect::action('topc_ctl_member@pwdSet');
        }
        $pagedata['userInfo'] = $userInfo;
        $pagedata['op'] = $postdata['op'];
        $pagedata['verifyType']= $postdata['verifyType'];
        if($postdata['verifyType']=='email'&&$userInfo['email_verify'])
        {
            $this->action_view = "unemail.html";
        }
        elseif($postdata['verifyType']=='mobile'&&$userInfo['mobile'])
        {
            $this->action_view = "unmobile.html";
        }
        else
        {
            $msg = app::get('topc')->_('参数错误');
            return $this->splash('error',$url,$msg);
        }
        return $this->output($pagedata);
    }

    //解绑mobile
    public function unVerifyMobile()
    {
        $postData = utils::_filter_input(input::get());
        $sendType = $postData['verifyType'];
        $userId = userAuth::id();
        try
        {
            if(!userVcode::verify($postData['vcode'],$postData['uname'],$postData['type']))
            {
                throw new \LogicException(app::get('topc')->_('验证码错误'));
            }

            $data['user_id'] = $userId;
            $data['user_name'] = $postData['uname'];
            $data['type'] = $postData['op'];
            app::get('topc')->rpcCall('user.account.update',$data,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $url = url::action("topc_ctl_member@unVerifyLast",array('sendType'=>$sendType));
        return $this->splash('success',$url,null);
    }

    //绑定邮箱
    public function unVerifyEmail()
    {
        $postData = utils::_filter_input(input::get());
        $userId = userAuth::id();
        try
        {
            if(md5($userId) != $postData['verify'])
            {
                throw new \LogicException(app::get('topc')->_('用户不一致！'));
            }
            if(!userVcode::verify($postData['vcode'],$postData['uname'],$postData['type']))
            {
                throw new \LogicException(app::get('topc')->_('验证码错误'));
            }

            $data['user_id'] = $userId;
            $data['user_name'] = $postData['uname'];
            $data['type'] = 'delete';
            app::get('topc')->rpcCall('user.account.update',$data,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $pagedata['sendType']= 'email';
        $pagedata['action']= 'topc_ctl_member@unVerifyEmail';
        $this->action_view = "unverifylast.html";
        return $this->output($pagedata);

    }

    /**
     * @brief 解绑最后一步
     *
     * @return html
     */
    public function unVerifyLast()
    {
        $sendType = input::get();
        $pagedata['sendType']= $sendType;
        $pagedata['action']= 'topc_ctl_member@unVerifyLast';
        $this->action_view = "unverifylast.html";
        return $this->output($pagedata);
    }


    /**
     * @brief 绑定第一步
     *
     * @return html
     */
    public function verify()
    {

        $postData = utils::_filter_input(input::get());

        $userId = userAuth::id();
        //会员信息
        $userInfo = userAuth::getUserInfo();

        $pagedata['userInfo']= $userInfo;
        $pagedata['verifyType']= $postData['verifyType'];
        $pagedata['type']= $postData['type'];
        $this->action_view = "verify.html";
        return $this->output($pagedata);
    }
    /**
     * @brief 验证登陆密码
     *
     * @return html
     */
    public function CheckSetInfo()
    {
        $postData =utils::_filter_input(input::get());
        $validator = validator::make(
            ['password' => $postData['password']],
            ['password' => 'required'],
            ['password' => '密码不能为空!']
        );
        if ($validator->fails())
        {
            $messages = $validator->messagesInfo();
            foreach( $messages as $error )
            {
                return $this->splash('error',null,$error[0]);
            }
        }
        $data['password'] = $postData['password'];
        try
        {
            app::get('topc')->rpcCall('user.login.pwd.check',$data,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $verifyType = $postData['verifyType'];
        $type = $postData['type'];
        $url = url::action("topc_ctl_member@setUserInfoOne",array('verifyType'=>$verifyType,'type'=>$type));

        return $this->splash('success',$url,null);
    }

    /**
     * @brief 验证第二步
     *
     * @return html
     */
    public function setUserInfoOne()
    {
        //会员信息
        $userInfo = userAuth::getUserInfo();
        $postdata = input::get();
        $pagedata['type'] = $postdata['type'];

        if($postdata['type'] && $postdata['type'] = "update" && !$userInfo['login_account'])
        {
            $msg = app::get('topc')->_('您还没有设置用户名，请前往设置用户名!');
            return $this->splash('error',$url,$msg);
        }

        $pagedata['userInfo']= $userInfo;
        $pagedata['verifyType']= $postdata['verifyType'];
        if($postdata['verifyType']=='email')
        {
            $this->action_view = "emailfirst.html";
        }
        elseif($postdata['verifyType']=='mobile')
        {
            $this->action_view = "mobilefirst.html";
        }
        else
        {
            $msg = app::get('topc')->_('参数错误');
            return $this->splash('error',$url,$msg);
        }

        return $this->output($pagedata);
    }

    //绑定mobile
    public function bindMobile()
    {
        $postData = utils::_filter_input(input::get());
        $sendType = $postData['verifyType'];
        $postData['user_id'] = userAuth::id();
        try
        {
            if(!userVcode::verify($postData['vcode'],$postData['uname'],$postData['type']))
            {
                throw new \LogicException(app::get('topc')->_('验证码错误'));
            }

            $data['user_id'] = $postData['user_id'];
            $data['user_name'] = $postData['uname'];
            app::get('topc')->rpcCall('user.account.update',$data,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $url = url::action("topc_ctl_member@setUserInfoLast",array('sendType'=>$sendType));
        return $this->splash('success',$url,null);
    }

    //绑定邮箱
    public function bindEmail()
    {
        $postData = utils::_filter_input(input::get());
        $userId = userAuth::id();
        try
        {
            if(md5($userId) != $postData['verify'])
            {
                throw new \LogicException(app::get('topc')->_('用户不一致！'));
            }
            if(!userVcode::verify($postData['vcode'],$postData['uname'],$postData['type']))
            {
                throw new \LogicException(app::get('topc')->_('验证码错误'));
                return false;
            }

            $data['user_id'] = $userId;
            $data['email'] = $postData['uname'];
            app::get('topc')->rpcCall('user.email.verify',$data,'buyer');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $pagedata['sendType']= 'email';
        $pagedata['action']= 'topc_ctl_member@bindEmail';
        $this->action_view = "setinfolast.html";
        return $this->output($pagedata);
    }

    /**
     * @brief 发送短信验证码
     *
     * @return html
     */
    public function sendVcode()
    {
        $postData = utils::_filter_input(input::get());

        if($postData['verifyType'] == "email")
        {
            $validator = validator::make(
                [$postData['uname']],['required|email'],['您的邮箱号不能为空!|邮箱号格式不对!']
            );
            if ($validator->fails())
            {
                $messages = $validator->messagesInfo();

                foreach( $messages as $error )
                {
                    return $this->splash('error',null,$error[0]);
                }
            }
        }
        if($postData['verifyType'] == "mobile")
        {
            $validator = validator::make(
                [$postData['uname']],['required|mobile'],['您的手机号不能为空!|手机号格式不对!']
            );
            if ($validator->fails())
            {
                $messages = $validator->messagesInfo();
                foreach( $messages as $error )
                {
                    return $this->splash('error',null,$error[0]);
                }
            }
        }
        try
        {
            $this->passport->sendVcode($postData['uname'],$postData['type']);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        if($accountType == "email")
        {
            return $this->splash('success',null,"邮箱验证链接已经发送至邮箱，请登录邮箱验证");
        }
        else
        {
            return $this->splash('success',null,"验证码发送成功");
        }
    }
    /**
     * @brief 验证最后一步
     *
     * @return html
     */
    public function setUserInfoLast()
    {
        $sendType = input::get();
        $pagedata['sendType']= $sendType;
        $pagedata['action']= 'topc_ctl_member@setUserInfoLast';
        $this->action_view = "setinfolast.html";
        return $this->output($pagedata);
    }


    public function shopsCollect()
    {
        $filter = input::get();
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $pageSize = 20;
        $params = array(
            'page_no' => $pageSize*($filter['pages']-1),
            'page_size' => $pageSize,
            'fields' =>'*',
            'user_id'=>userAuth::id(),
        );
        $favData = app::get('topc')->rpcCall('user.shopcollect.list',$params,'buyer');

        $count = $favData['shopcount'];
        $favList = $favData['shopcollect'];

        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_member@shopsCollect',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );
        $pagedata['favshop_info']= $favList;
        $pagedata['count'] = $count;
        $pagedata['action']= 'topc_ctl_member@shopsCollect';


        $this->action_view = "shops.html";
        return $this->output($pagedata);
    }

    public function itemsCollect()
    {
        $filter = input::get();
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $pageSize = 20;
        $params = array(
            'page_no' => $pageSize*($filter['pages']-1),
            'page_size' => $pageSize,
            'fields' =>'*',
            'user_id'=>userAuth::id(),
            'cat_id'=>$filter['cat_id'],
        );
        $favData = app::get('topc')->rpcCall('user.itemcollect.list',$params,'buyer');
        $count = $favData['itemcount'];
        $favList = $favData['itemcollect'];

        //获取类目
        $catInfo = $this->getCatInfo();
        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_member@itemsCollect',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );
        $pagedata['fav_info']= $favList;
        $pagedata['catInfo']= $catInfo;
        $pagedata['count'] = $count;
        $pagedata['action']= 'topc_ctl_member@itemsCollect';
        $this->action_view = "items.html";
        return $this->output($pagedata);
    }

    public function getCatInfo()
    {
        $params = array(
            'user_id'=>userAuth::id(),
        );

        $favData = app::get('topc')->rpcCall('user.itemcollect.list',$params, 'buyer');
        $infoList = $favData['itemcollect'];

        if(!$infoList) return "";

        foreach ($infoList as $key => $value)
        {
            $catId[] = $value['cat_id'];
        }

        $catNum = array_count_values($catId);
        $catInfo = app::get('topc')->rpcCall('category.cat.get.info',array('cat_id'=>implode(',',$catId),'fields'=>'cat_id,cat_name'),'buyer');
        foreach($catInfo as $k=>$val)
        {
            $catname[$k]['num'] = $catNum[$k];
            $catname[$k]['cat_id'] = $val['cat_id'];
            $catName[$k]['cat_name'] = $val['cat_name'];
        }
        return $catName;
    }

    /**
     * 信任登陆用户名密码设置
     */
    public function pwdSet()
    {
        //会员信息
        $userInfo = userAuth::getUserInfo();
        $pagedata['userInfo'] = $userInfo;
        $pagedata['action'] = 'topc_ctl_member@pwdSet';
        $this->action_view = "pwdset.html";
        return $this->output($pagedata);
    }
    /**
     * 信任登陆用户名密码设置
     */
    public function savePwdSet()
    {
        $postData = input::get();

        $userId = userAuth::id();
        //会员信息
        $userInfo = userAuth::getUserInfo();
        $url = url::action("topc_ctl_member@pwdSet");
        if($userInfo['login_type']=='trustlogin')
        {
            try
            {
                $this->__checkAccount($postData['username']);
                $data = array(
                    'new_pwd' => $postData['new_password'],
                    'confirm_pwd' => $postData['confirm_password'],
                    'old_pwd' => $postData['old_password'],
                    'uname' => $postData['username'],
                    'user_id' => $userId,
                    'type' => ($userInfo['login_type']=='trustlogin') ? "reset" : "update",
                );
                app::get('topc')->rpcCall('user.pwd.update',$data,'buyer');
            }
            catch(\Exception $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg,true);
            }
        }
        else
        {
            try
            {
                $this->__checkAccount($postData['username']);
                $data = array(
                    'user_name'   => $postData['username'],
                    'user_id' => $userId,
                );
                app::get('topc')->rpcCall('user.account.update',$data,'buyer');
            }
            catch(\Exception $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg,true);
            }
        }

        return $this->splash('success',$url,app::get('topc')->_('修改成功'),true);
    }

    private function __checkAccount($username)
    {
        $validator = validator::make(
            ['username' => $username],
            ['username' => 'numeric|email'],
            ['username' => '用户名不能为纯数字!|用户名不能为邮箱!']
        );
        if ($validator->fails())
        {
            $messages = $validator->messagesInfo();
            foreach( $messages as $error )
            {
                throw new LogicException( $error[0] );
            }
        }
        return true;
    }

}


