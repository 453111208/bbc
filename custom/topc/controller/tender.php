<?php
  class topc_ctl_tender extends topc_controller{

    public function index(){
      $tenderId = intval(input::get('tender_id'));
      $comment = app::get('sysshoppubt')->model('comment');
      $this->tender_model=app::get('sysshoppubt')->model('tender');
      $this->standard_item_model=app::get('sysshoppubt')->model('standard_item');
      $this->sysitem_item_model=app::get('sysitem')->model('item');
      $this->tenderenter=app::get('sysshoppubt')->model('tenderenter');
      $recorder = app::get('sysshoppubt')->model('moneyrecoder');
    $this->setLayoutFlag('tender');
    $tender=$this->tender_model->getRow('*',array('tender_id'=>$tenderId));
    $standard_items=$this->standard_item_model->getList('*',array('uniqid'=>$tender['uniqid']));
    foreach ($standard_items as $key => $value) {
      $item = $this->sysitem_item_model->getRow('*', array('item_id' => $value['item_id']));
      $standard_items[$key]['goods_total_price'] = intval($value['num'])*intval($value['net_price']);
      $standard_items[$key]["image_default_id"]=$item["image_default_id"];
      $prop=app::get('syscategory')->model('item_prop_value')->getList("*",array("item_id"=> $value["item_id"]));
      $standard_items[$key]["prop"]=$prop;
    }
          $pagedata['standard_items']=$standard_items; // 商品详情
          $pagedata['row']=$tender; //交易信息
          $pagedata['type']=2;
          //企业信息
          $shopId=$tender["shop_id"];
          $shopinfo=app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shopId));
          $pagedata["shopinfo"]=$shopinfo;
          //已投标
          $tendetenterList=$this->tenderenter->getList("*",array("tender_id"=>$tenderId));
          $tenderentercount=count($tendetenterList);
          $pagedata["tendetenterList"]=$tendetenterList;
          $pagedata["tenderentercount"]=$tenderentercount;
    if($tender['seegoods_stime']<time()&&$tender['seegoods_stime']!=null){
        $pagedata['sample_end']='1';
        }elseif($tender['seegoods_stime']==null){
        $pagedata['sample_end']='0';
        }else{
        $pagedata['sample_end']='2';
        }
    if(userAuth::check()){
      $pagedata['nologin'] = 1;
    }
    if(userAuth::id()){
    $params["user_id"]=userAuth::id();
    $shopInfoGet=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
    $pagedata['result'] = $recorder->getRow('*',array('shop_id'=>$shopId,'type'=>0,'item_id'=>$tenderId,'user_id'=>$shopInfoGet['shop_id']));
    $pagedata['tenderinfoment'] = $this->tenderenter->getRow('*',array('shop_id'=>$shopId,'tender_man_id'=>$shopInfoGet['shop_id'],'tender_id'=>$tenderId));
    }
    $starttime = $tender['start_time']-time();
    $stoptime = $tender['stop_time']-time();
    if($tender['start_time']>time()&&$tender['isok']!=1){
      $pagedata['state'] = 0;
      $pagedata['totaltime'] = $starttime;
    }elseif($tender['stop_time']>time()&&$tender['isok']!=1){
      $pagedata['state'] = 1;
      $pagedata['totaltime'] = $stoptime;
    }else{
      $pagedata['state'] = 2;
      $pagedata['totaltime'] = 0;
    }
    $commentnum = $comment->count(array('shop_id'=>$shopId,'item_id'=>$tenderId));
    $pagedata['commentnum'] = $commentnum;
    return $this->page('topc/tender/index.html', $pagedata);
    }

  public function tender(){
      if(!userAuth::id()){
        return 0;
      }
    $uniqid = input::get('uniqid');
    $tender_id = input::get('tender_id');
    $user = app::get('sysuser')->model('user');
    $recorder = app::get('sysshoppubt')->model('moneyrecoder');
    $tender = app::get('sysshoppubt')->model('tender');
    $userinfo = $user->getRow('*',array('user_id'=>userAuth::id()));
    $userinfoold = $userinfo;
    $ensurence = $tender->getRow('*',array('tender_id'=>$tender_id));
    $userinfo['hjadvance'] -= $ensurence['ensurence'];
    $params["user_id"]=userAuth::id();
    $shopInfoGet=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
    $reco['item_id'] = $tender_id;
    $reco['type'] = 0;
    $reco['shop_id'] = $ensurence['shop_id'];
    $reco['shop_name'] = $ensurence['shop_name'];
    $reco['user_id'] = $shopInfoGet["shop_id"];
    $reco['money'] = $ensurence['ensurence'];
    $reco['create_time'] = time();
    $moneyreco = array(
        "user_id"=>userAuth::id(),
        "changemoney" =>$ensurence['ensurence'],
        "name"=>$shopInfoGet["shop_name"],
        "types"=>1,
        "username"=>userAuth::getLoginName(),
        "pay"=>2,
        "tender_id"=>$tender_id,
        "create_time"=>time()
      );
      try{
        $result = $user->update($userinfo,$userinfoold);
        $recorder->save($reco);
        app::get("sysuser")->model("moneyreco")->save($moneyreco);
      }
       catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $msg;
        }
      return 1;
    }

    public function newpage(){
      $tender_id = input::get('tender_id');
      $tender = app::get('sysshoppubt')->model('tender');
      $chrule = app::get('sysshoppubt')->model('chrule');
      $tenderule = app::get('sysshoppubt')->model('tenderule');
      $uniqids = $tender->getRow('uniqid',array('tender_id'=>$tender_id));
      $tendid = $tenderule->getList('tenderrule_id');
      $tendrsul =array();
      foreach ($tendid as $key => $value) {
        $tendrsul[] = $value['tenderrule_id'];
      }
      $pagedata['allrule'] = $chrule->getList('*',array('tenderrule_id'=>$tendrsul,'uniqid'=>$uniqids['uniqid']));
      $score = $chrule->getList('score,category',array('detail'=>null,'uniqid'=>$uniqids['uniqid']));
        foreach ($score as $key => $value) {
          if($value['category'] == '必要资质')$sumn = $sumn + $value['score'];
          if($value['category'] == '可选资质')$sumc = $sumc + $value['score'];
          if($value['category'] == '设备能力')$summ = $summ + $value['score'];
          if($value['category'] == '处置能力')$sumd = $sumd + $value['score'];
          if($value['category'] == '服务能力')$sums = $sums + $value['score'];
        }
      $pagedata['sumn'] = $sumn;
      $pagedata['sumc'] = $sumc;
      $pagedata['summ'] = $summ;
      $pagedata['sumd'] = $sumd;
      $pagedata['sums'] = $sums;
      $pagedata['newrow'] = $tender->getRow('*',array('tender_id'=>$tender_id));
      $pagedata['tender_id'] = $tender_id;
      $this->setLayoutFlag('tenderrule');
      return $this->page('topc/tender/tenderrule.html',$pagedata);
      /*return view::make('topc/tender/tenderrule.html',$pagedata);*/
    }
    public function save(){
      $data = input::get();
      $sa=kernel::single('desktop_user');
      $admName = $sa->get_login_name();
      $tenderinfo = app::get('sysshoppubt')->model('tenderinfo');
      $tenderenter = app::get('sysshoppubt')->model('tenderenter');
      $tenders = app::get('sysshoppubt')->model('tender');
      $userinfo = app::get('sysuser')->model('user'); //暂用会员信息代替企业信息
      if(!userAuth::id()){
       return $this->splash('error',null,"请先登录");
      }
      $tenderthrough = $tenders->getRow('*',array('tender_id'=>$data['tender_id']));
      if($tenderthrough['is_through']!=1){
      return $this->splash('error',null,"该交易暂未通过审核，不可投标");
      }
      $params["user_id"]=userAuth::id();
      $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
      $getmoney = $userinfo->getRow('hjadvance,name',array('user_id'=>userAuth::id()));
      $checks = $tenderinfo->getList('*',array('tender_man_id'=>$shopInfo["shop_id"],'tender_id'=>$data['tender_id'],'shop_id'=>$data['shop_id']));
      if(!$checks){
      foreach ($data as $key => $value) {
        $chruleid = split('_', $key);
        if($value){
        if($chruleid[0] == 'data'&& is_array($value)){
          foreach ($value as $key1 => $value1) {
            if($value1&&$key1!=0){
            $value[0] = $value[0].','.$value1;
            }
          }
          $sql="insert into sysshoppubt_tenderinfo (shop_id,shop_name,tender_man_id,tender_id,price,tender_time,chrule_id,data) values(". $data['shop_id'] .",'". $data['shop_name'] ."',". $shopInfo["shop_id"] .",". $data['tender_id'] .",". $data['price'] .",". time() .",". $chruleid[1] .",'". $value[0] ."')";
          $db = app::get('sysshoppubt')->database();
          $db->exec($sql);
        }elseif($chruleid[0] == 'data'&&!is_array($value)){
          $sql="insert into sysshoppubt_tenderinfo (shop_id,shop_name,tender_man_id,tender_id,price,tender_time,chrule_id,data) values(". $data['shop_id'] .",'". $data['shop_name'] ."',". $shopInfo["shop_id"] .",". $data['tender_id'] .",". $data['price'] .",". time() .",". $chruleid[1] .",'". $value ."')";
          $db = app::get('sysshoppubt')->database();
          $db->exec($sql);
        }
      }
      }
      $tendertitile = $tenders->getRow('trading_title',array('tender_id'=>$data['tender_id']));
      $gettenderinfo['tender_man_id'] = $shopInfo["shop_id"];
      $gettenderinfo['tender_id'] = $data['tender_id'];
      $gettenderinfo['price'] = $data['price'];
      $gettenderinfo['shop_id'] = $data['shop_id'];
      $gettenderinfo['shop_name'] = $data['shop_name'];
      $gettenderinfo['tender_title'] = $tendertitile['trading_title'];
      $gettenderinfo['tender_time'] = time();
      $gettenderinfo['create_time'] = time();
      $gettenderinfo['name'] = $admName;
      $gettenderinfo['tender_man'] = $shopInfo["shop_name"];
      try {
      $tenderenter->save($gettenderinfo);
      } catch (Exception $e) {
      $msg = $e->getMessage();
      return $this->splash('error',null,$msg);
      }
      return $this->splash('success',null,"投标成功！");

      }else{return $this->splash('error',null,"您已投标成功，请勿重复投标");}
    }


    public function checkmoney(){
      $data = input::get();
      $userid = userAuth::id();
      if(!$userid){
        return 4;
      }
      $params["user_id"]=userAuth::id();
      $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
      $recoder = app::get('sysshoppubt')->model('moneyrecoder');
      $user = app::get('sysuser')->model('user');
      $tenderenter = app::get('sysshoppubt')->model('tenderenter');
      $tenders = app::get('sysshoppubt')->model('tender');
      $tenderall = $tenders->getRow('*',array('tender_id'=>$data['tender_id']));
      if($tenderall['start_time']>time()){
        return 6;
      }elseif ($tenderall['stop_time']<time()) {
        return 7;
      }
      if($tenderall['shop_id']==$shopInfo['shop_id']){
        return 3;
      }else{
      $checks = $tenderenter->getList('*',array('tender_man_id'=>$shopInfo['shop_id'],'tender_id'=>$data['tender_id']));
      if(!$checks){
      $recomoney = $recoder->getRow('*',array('shop_id'=>$data['shop_id'],'type'=>0,'item_id'=>$data['tender_id'],'user_id'=>$shopInfo["shop_id"]));
      if($recomoney){
        return 5;
      }
      $usermoney = $user->getRow('hjadvance',array('user_id'=>$userid));
      $ensurence = $tenders->getRow('ensurence',array('tender_id'=>$data['tender_id']));
      if(floatval($usermoney['hjadvance']) >= floatval($ensurence['ensurence'])){
        return 1;
      }else{return 0;}
    }else{return 2;}
    }
  }

  
 }









