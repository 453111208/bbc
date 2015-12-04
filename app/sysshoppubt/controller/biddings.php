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
        $admName = $sa->get_login_name();
        $infoData = input::get();
        $infoData['create_time']=time();
        $infoData['name'] = $admName;
        $info = app::get('sysshoppubt')->model('info');
        $info->save($infoData);
        $this->end(true);
    }

}