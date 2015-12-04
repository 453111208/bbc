
<?php
class topc_ctl_standard extends topc_controller
{
    
    //标准
    
    public function __construct(&$app) {
        $this->sprodrelease_model = app::get('sysshoppubt')->model('sprodrelease');
        $this->standard_item_model = app::get('sysshoppubt')->model('standard_item');
        $this->sysitem_item_model = app::get('sysitem')->model('item');
        $this->setLayoutFlag('standard');
    }
    
    public function index() {
        $standardid = intval(input::get('standard_id')); // code...
        // $sprodrelease=$this->sprodrelease_model->getRow('*',array('standard_id'=>$standardid));
        // $standard_items=$this->standard_item_model->getList('*',array('uniqid'=>$sprodrelease['uniqid']));
        //交易信息
        $sprodrelease = $this->sprodrelease_model->getRow('*', array('standard_id' => $standardid));
        
        //交易商品信息
        $standardSql = "select * from sysshoppubt_standard_item a
                    left join sysshoppubt_sprodrelease b on a.uniqid=b.uniqid
                    where b.standard_id = " . $standardid . "";
        $standard_items = app::get("base")->database()->executeQuery($standardSql)->fetchAll();
        
        $totalPrice = 0;
        $i = 0;
        foreach ($standard_items as $key => $val) {
            $item = $this->sysitem_item_model->getRow('*', array('item_id' => $val['item_id']));
            $standard_items[$i]['list_image'] = explode(',', $item['list_image']);
            $standard_items[$i]['image_default_id'] = $item['image_default_id'];
            $standard_items[$i]['goods_total_price'] = $val['num'] * $val['net_price'];
            $standard_items[$i]["item"] = $item;
            $totalPrice = $totalPrice + $standard_items[$i]['goods_total_price'];
            $prop=app::get('syscategory')->model('item_prop_value')->getList("*",array("item_id"=>$val['item_id']));
            $standard_items[$i]["prop"]=$prop;
            $i++;
          
        }
        
        //卖方信息
        $shopId = $sprodrelease["shop_id"];
        $shopInfo = app::get("sysshop")->model("shop")->getRow("*", array("shop_id" => $shopId));
        
        //购买记录
        $tradeorderSql = "select * from sysshoppubt_tradeorder where standard_id=" . $standardid . " ORDER BY create_time desc LIMIT 4";
        $tradeorderList = app::get("base")->database()->executeQuery($tradeorderSql)->fetchAll();
        
        //返回信息
        $pagedata['standard_items'] = $standard_items;
         // 商品详情
        $pagedata['totalPrice'] = $totalPrice;
         //整个交易的总价
        $pagedata['row'] = $sprodrelease;
         //交易信息
        $pagedata['now_time'] = time();
         //当前时间
        $pagedata["shop"] = $shopInfo;
        $pagedata['type'] = 0;
        $pagedata["tradeList"] = $tradeorderList;
        if ($sprodrelease['seegoods_stime'] < time() && $sprodrelease['seegoods_stime'] != null) {
            $pagedata['sample_end'] = '1';
        } 
        elseif ($sprodrelease['seegoods_stime'] == null) {
            $pagedata['sample_end'] = '0';
        } 
        else {
            $pagedata['sample_end'] = '2';
        }
        
        if (userAuth::check()) {
            $pagedata['nologin'] = 1;
        }
        
        return $this->page('topc/standard/index.html', $pagedata);
    }
    
    //看样申请
    public function sample() {
        $userId = userAuth::id();
        if (!$userId) {
            $url = url::action('topc_ctl_passport@signin');
            return $this->splash('error', $url);
        }
        $standard_id = $_POST['standard_id'];
        $params['standard_id'] = $standard_id;
        $params['shop_id'] = $userId;
        $sysshoppubt_sample_model = app::get('sysshoppubt')->model('sample');
        $arr = $sysshoppubt_sample_model->getRow('*', $params);
        $params['create_time'] = time();
        if ($arr) {
            return $this->splash('error', null, app::get('topc')->_('已参加'));
        } 
        else {
            $sysshoppubt_sample_model->save($params);
            $total = $sysshoppubt_sample_model->count(array('standard_id' => $standard_id));
            $sql = "update sysshoppubt_sprodrelease set attendcount='" . $total . "' where standard_id=" . $standard_id;
            $db = app::get('sysshoppubt')->database();
            $db->exec($sql);
            return $this->splash('success', null, app::get('topc')->_('参加成功'));
        }
    }
    
    //交易单保存
    public function save() {
        
        $userId = userAuth::id();
        if (!$userId) {
            $url = url::action('topc_ctl_passport@signin');
            return $this->splash('error', $url);
        }
        $postData = input::get();
        $spreadrelease = app::get('sysshoppubt')->model('spreadrelease');
        $standard = app::get('sysshoppubt')->model('tradeorder');
        $standardinfo = $spreadrelease->getRow('*',array('standard_id'=>$postData['standard_id']));
        if($standardinfo['is_through']!=1){
            return $this->splash('error',null,"该交易暂未通过审核，不可购买");
        }
        $params["user_id"]=$userId;
        $shopInfoGet=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        $postData['user_id'] = $shopInfoGet["shop_id"];
        //$userInfo = app::get("sysuser")->model("account")->getRow("*", array("user_id" => $userId));
        $postData["user_name"] = $shopInfoGet["shop_name"];
        $postData['create_time'] = time();
        $shopId = $postData["shop_id"];
        if($shopId==$shopInfoGet["shop_id"]){
              return $this->splash('error',null,"不可购买自己发布的标准交易");
        }
        $shopInfo = app::get("sysshop")->model("shop")->getRow("*", array("shop_id" => $shopId));
        $postData["shop_name"] = $shopInfo["shop_name"];
        $sysshoppubt_tradeorder_model = app::get('sysshoppubt')->model('tradeorder');
        $sysshoppubt_tradeorder_model->save($postData);
        return $this->splash('success', null, app::get('topc')->_('提交成功'));
    }
    
    //以下为交易咨询
    public function getItemConsultation() {
        $userid = userAuth::id();
        $itemId = input::get();
        if (empty($itemId)) return '';
        foreach ($itemId as $key => $value) {
            if ($key == 'standard_id') $type = 0;
            elseif ($key == 'bidding_id') {
                $type = 1;
            } 
            elseif ($key == 'tender_id') {
                $type = 2;
            }
        }
        $pagedata = $this->__searchConsultation($id, $infos['shop_id'], $type);
        $info = app::get('sysshoppubt')->model('sprodrelease');
        $bid = app::get('sysshoppubt')->model('biddings');
        $tend = app::get('sysshoppubt')->model('tender');
        if ($type == 0) {
            $infos = $info->getRow('*', array('standard_id' => $itemId['standard_id']));
            $id = $itemId['standard_id'];
            $pagedata['type'] = 0;
        } 
        elseif ($type == 1) {
            $infos = $bid->getRow('*', array('bidding_id' => $itemId['bidding_id']));
            $pagedata['type'] = 1;
            $id = $itemId['bidding_id'];
        } 
        elseif ($type == 2) {
            $infos = $tend->getRow('*', array('tender_id' => $itemId['tender_id']));
            $pagedata['type'] = 2;
            $id = $itemId['tender_id'];
        }
        $pagedata['item_id'] = $id;
        $pagedata['rows'] = $infos;
        $pagedata['user_id'] = $userid;
        return view::make('topc/standard/consultation.html', $pagedata);
    }
    
    public function getItemConsultationList() {
        $itemId = input::get();
        if (empty($itemId)) return '';
        foreach ($itemId as $key => $value) {
            if ($key == 'standard_id') $type = 0;
            elseif ($key == 'bidding_id') {
                $type = 1;
            } 
            elseif ($key == 'tender_id') {
                $type = 2;
            }
        }
        $info = app::get('sysshoppubt')->model('sprodrelease');
        $bid = app::get('sysshoppubt')->model('biddings');
        $tend = app::get('sysshoppubt')->model('tender');
        if ($type == 0) {
            $infos = $info->getRow('shop_id', array('standard_id' => $itemId['standard_id']));
            $id = $itemId['standard_id'];
        } 
        elseif ($type == 1) {
            $infos = $bid->getRow('shop_id', array('bidding_id' => $itemId['bidding_id']));
            $id = $itemId['bidding_id'];
        } 
        elseif ($type == 2) {
            $infos = $tend->getRow('shop_id', array('tender_id' => $itemId['tender_id']));
            $id = $itemId['tender_id'];
        }
        $pagedata = $this->__searchConsultation($id, $infos['shop_id'], $type);
        return view::make('topc/standard/consultation/list.html', $pagedata);
    }
    
    /*翻页*/
    private function __searchConsultation($itemId, $shopid, $type) {
        $current = input::get('pages', 1);
        $params = ['item_id' => $itemId, 'types' => $type, 'shop_id' => $shopid, 'page_no' => $current, 'page_size' => 10, 'fields' => '*'];
        
        if (in_array(input::get('result'), ['item', 'store_delivery', 'payment', 'invoice'])) {
            $params['type'] = input::get('result');
            $pagedata['result'] = 'all';
        } 
        else {
            $pagedata['result'] = 'all';
        }
        
        /*$data = app::get('topc')->rpcCall('shoppubt.gask.list', $params);*/
        $chtype = app::get('sysshoppubt')->model('consultation');
        $choiceall = $chtype->getList('*', array('is_display' => 'true', 'item_id' => $itemId, 'shop_id' => $shopid, 'type' => $type));
        $choiceitem = $chtype->getList('*', array('consultation_type' => 'item', 'is_display' => 'true', 'item_id' => $itemId, 'shop_id' => $shopid, 'type' => $type));
        $choicedelivery = $chtype->getList('*', array('consultation_type' => 'store_delivery', 'is_display' => 'true', 'item_id' => $itemId, 'shop_id' => $shopid, 'type' => $type));
        $choicepayment = $chtype->getList('*', array('consultation_type' => 'payment', 'is_display' => 'true', 'item_id' => $itemId, 'shop_id' => $shopid, 'type' => $type));
        $choiceinvoice = $chtype->getList('*', array('consultation_type' => 'invoice', 'is_display' => 'true', 'item_id' => $itemId, 'shop_id' => $shopid, 'type' => $type));
        $pagedata['gask'] = $choiceall;
        $pagedata['items'] = $choiceitem;
        $pagedata['delivery'] = $choicedelivery;
        $pagedata['pay'] = $choicepayment;
        $pagedata['invoice'] = $choiceinvoice;
        $pagedata['count'] = app::get('topc')->rpcCall('shoppubt.gask.count', $params);
        
        //处理翻页数据
        $filter = input::get();
        $pagedata['filter'] = $filter;
        $filter['pages'] = time();
        if ($data['total_results'] > 0) $total = ceil($data['total_results'] / 10);
        $current = $total < $current ? $total : $current;
        $pagedata['pagers'] = array('link' => url::action('topc_ctl_standard@getItemConsultationList', $filter), 'current' => $current, 'total' => $total, 'token' => $filter['pages'],);
        return $pagedata;
    }
    
    /**
     * @brief 商品咨询提交
     *
     * @return
     */
    public function commitConsultation() {
        $sa = kernel::single('desktop_user');
        $consave = app::get('sysshoppubt')->model('consultation');
        $post = input::get('gask');
        $post['modified_time'] = intval($post['modified_time']);
        $post['is_anonymity'] = intval($post['is_anonymity']);
        $post['created_time'] = time();
        $post['author'] = $sa->get_login_name();
        $post['author_id'] = $sa->get_id();
        $post['is_reply'] = intval($post['is_reply']);
        $post['is_anonymity'] = $post['is_anonymity'] ? $post['is_anonymity'] : 0;
        
        if (userAuth::id()) {
            $post['user_name'] = userAuth::getLoginName();
            $post['be_reply_id'] = userAuth::id();
        } 
        else {
            if (!$post['contack']) {
                return $this->splash('error', $url, "由于您没有登录，咨询请填写联系方式", true);
            }
            $post['user_name'] = '游客';
            $post['be_reply_id'] = "0";
        }
        
        try {
            if ($post['contack']) {
                $type = kernel::single('pam_tools')->checkLoginNameType($params['contack']);
                if ($type != "login_account") {
                    throw new \LogicException('请填写正确的联系方式(手机号或邮箱)');
                }
            }
            $consave->save($post);
            $result = true;
        }
        catch(\Exception $e) {
            $result = false;
            $msg = $e->getMessage();
        }
        
        if (!$result) {
            return $this->splash('error', null, $msg);
        } 
        else {
            $msg = '咨询提交成功,请耐心等待企业审核、回复';
            return $this->splash('success', null, $msg);
        }
    }
}
