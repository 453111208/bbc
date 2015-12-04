<?php

/**
 * @brief 商品列表
 */
class sysshoppubt_ctl_biddings extends desktop_controller{
	
 	public function index()
    {
        return $this->finder('sysshoppubt_mdl_biddings',array(
            'use_buildin_filter'=>true,
            'use_view_tab'=>true,
            'title' => app::get('sysshoppubt')->_('竞价列表'),
            'use_buildin_delete'=>false,
        ));
    }
    public function save(){
        $advice = $_POST['advice'];
        $saveItem = app::get('sysshoppubt')->model('standard_item');
        foreach ($advice as $key => $value){
            $change = $saveItem->getRow('*',array('standardg_item_id'=>$key));
            $changes = $change;
            $change['advice'] = $value;
            $saveItem->update($change,$changes);
        }
        return view::make('sysshoppubt/biddings/advice.html')->render();
    }

    public function check(){
        $sa=kernel::single('desktop_user');
        $admName = $sa->get_login_name();
        $ideas = $_POST["ideas"];
        $is_through = $_POST["is_through"];
        $saveItem = app::get('sysshoppubt')->model('checks');
        $postData = input::get();
        $postData['create_time']=time();
        $postData['name'] = $admName;
        $saveItem->save($postData);
        if($is_through == '1'){
            $saveItem = app::get('sysshoppubt')->model('biddings');
            $paminfo = $saveItem->getRow('*',array('bidding_id'=>$ideas));
            $paminfo1 = $paminfo;
            $seegoods_stime = strtotime($postData['seegoods_stime']);
            $paminfo['seegoods_stime'] = $seegoods_stime;
            $paminfo['through_time'] =$postData['create_time'];
            $paminfo['is_through'] = '1';
            $saveItem->update($paminfo,$paminfo1);
            return view::make('sysshoppubt/check/passsucc.html')->render();
        }
        else return view::make('sysshoppubt/check/checksucc.html')->render();
    }

    public function _views()
    {
        $mdl_all = app::get('sysshoppubt')->model('biddings');
        $passed = array('is_through' => 1);
        $unpassed = array('is_through' => 2);
        $all = $mdl_all->count();
        $pass =  $mdl_all->count($passed);
        $unpass = $mdl_all->count($unpassed);
        $prev = $mdl_all->count(array('start_time|than'=>time(),'isok'=>0,'is_through'=>1,));
        $now = $mdl_all->count(array('start_time|lthan'=>time(),'stop_time|than'=>time(),'isok'=>0,'is_through'=>1));
        $succ = $mdl_all->count(array('isok'=>1));
        $subMenu = array(
            0=>array(
                'label'=>app::get('sysshoppubt')->_("全部 ( $all )"),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('sysshoppubt')->_("已通过审核 ( $pass )"),
                'optional'=>false,
                'filter'=>array(
                    'is_through'=>1,
                ),
            ),
            2=>array(
                'label'=>app::get('sysshoppubt')->_("未审核 ( $unpass )"),
                'optional'=>false,
                'filter'=>array(
                    'is_through'=>2,
                ),
            ),
            3=>array(
                'label'=>app::get('sysshoppubt')->_("预告交易 ( $prev )"),
                'optional'=>false,
                'filter'=>array(
                    'start_time|than'=>time(),
                    'is_through'=>1,
                    'isok'=>0,
                ),
            ),
            4=>array(
                'label'=>app::get('sysshoppubt')->_("正在交易 ( $now )"),
                'optional'=>false,
                'filter'=>array(
                    'start_time|lthan'=>time(),'stop_time|than'=>time(),'isok'=>0,'is_through'=>1,
                ),
            ),
            5=>array(
                'label'=>app::get('sysshoppubt')->_("已完成 ( $succ )"),
                'optional'=>false,
                'filter'=>array(
                   'isok'=>1,
                ),
            ),

        );
        return $subMenu;
    }


    
    public function infocontent($bidding_id,$uniqid){
        if(!$bidding_id)
        {
            $bidding_ids = input::get('bidding_id');
            $bidding_id = implode(',',$bidding_ids);
        }
        if(!$uniqid)
        {
            $info_uniqids = input::get('uniqid');
            $uniqid = implode(',',$info_uniqids);
        }
        $pagedatas['bidding_id'] = $bidding_id;
        $pagedatas['info_uniqid'] = $uniqid;
        return $this->page('sysshoppubt/info/bidinfo.html', $pagedatas);
        /*return view::make('sysshoppubt/info/info.html')->render();*/
    }

    public function info(){
        $this->begin("?app=sysshoppubt&ctl=biddings&act=index");
        $sa=kernel::single('desktop_user');
        $admid = $sa->get_id();
        $bidding = app::get('sysshoppubt')->model('biddings');
        $info = app::get('sysshop')->model('shop_notice');
        $infoData = input::get();
        $data = $bidding->getRow('*',array('bidding_id'=>$infoData['bidding_id']));
        $str['notice_title'] = $infoData['title'];
        $str['notice_content'] = $infoData['content'];
        $str['notice_type'] = "平台通知";
        $str['shop_id'] = $data['shop_id'];
        $str['createtime']=time();
        $str['admin_id'] = $admid;
        $str['is_read'] = 0;
        try {
        $info->save($str);
        } catch (Exception $e) {
            $msg = $e->getMessage();
      return $this->splash('error',null,$msg);
        }
        $this->end(true);
    }

}