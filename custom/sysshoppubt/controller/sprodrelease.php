<?php

/**
 * @brief 商品列表
 */
class sysshoppubt_ctl_sprodrelease extends desktop_controller{

 public $workground = 'sysshoppubt.workground.sprodrelease';
 public function index()
    {
        return $this->finder('sysshoppubt_mdl_sprodrelease',array(
            'use_buildin_filter'=>true,
            'use_view_tab'=>true,
            'title' => app::get('sysshoppubt')->_('交易明细列表'),
            'use_buildin_delete'=>false,
        ));
    }
public function check(){
        $sa=kernel::single('desktop_user');
        $admName = $sa->get_login_name();
        $ideas = $_POST["ideas"];
        $is_through = $_POST["is_through"];
        $saveItem = app::get('sysshoppubt')->model('checks');
        $postData = input::get();
        $postData['name'] = $admName;
        $saveItem->save($postData);
        if($is_through == '1'){
            $saveItem = app::get('sysshoppubt')->model('sprodrelease');
            $paminfo = $saveItem->getRow('*',array('standard_id'=>$ideas));
            $paminfo1 = $paminfo;
            $seegoods_stime = strtotime($postData['seegoods_stime']);
            $paminfo['through_time'] =time();
            $paminfo['seegoods_stime'] = $seegoods_stime;
            $paminfo['is_through'] = '1';
            $saveItem->update($paminfo,$paminfo1);
            return view::make('sysshoppubt/check/passsucc.html')->render();
        }
        else return view::make('sysshoppubt/check/checksucc.html')->render();
    }

    public function _views()
    {
        $mdl_all = app::get('sysshoppubt')->model('sprodrelease');
        $passed = array('is_through' => 1);
        $unpassed = array('is_through' => 2);
        $all = $mdl_all->count();
        $pass =  $mdl_all->count($passed);
        $unpass = $mdl_all->count($unpassed);
        $now = $mdl_all->count(array('isok'=>0));
        $succ = $mdl_all->count(array('isok|noequal'=>0));
        $subMenu = array(
            0=>array(
                'label'=>app::get('sysshoppubt')->_("全部 ( $all )"),
                'optional'=>false,
            ),

            1=>array(
                'label'=>app::get('sysshoppubt')->_("未审核 ( $unpass )"),
                'optional'=>false,
                'filter'=>array(
                    'is_through'=>2,
                ),
            ),
            2=>array(
                'label'=>app::get('sysshoppubt')->_("已通过审核 ( $pass )"),
                'optional'=>false,
                'filter'=>array(
                    'is_through'=>1,
                ),
            ),
            3=>array(
                'label'=>app::get('sysshoppubt')->_("正在交易 ( $now )"),
                'optional'=>false,
                'filter'=>array(
                    'isok'=>0,
                ),
            ),
            4=>array(
                'label'=>app::get('sysshoppubt')->_("已完成 ( $succ )"),
                'optional'=>false,
                'filter'=>array(
                    'isok|noequal'=>0,
                ),
            ),

        );
        return $subMenu;
    }
    

    public function infocontent($standard_id,$uniqid){
        if(!$standard_id)
        {
            $standard_ids = input::get('standard_id');
            $standard_id = implode(',',$standard_ids);
        }
        if(!$uniqid)
        {
            $info_uniqids = input::get('uniqid');
            $uniqid = implode(',',$info_uniqids);
        }
        $pagedatas['standard_id'] = $standard_id;
        $pagedatas['info_uniqid'] = $uniqid;
        return $this->page('sysshoppubt/info/info.html', $pagedatas);
        /*return view::make('sysshoppubt/info/info.html')->render();*/
    }

    public function info(){
        $this->begin("?app=sysshoppubt&ctl=sprodrelease&act=index");
        $sa=kernel::single('desktop_user');
        $admid = $sa->get_id();
        $standard = app::get('sysshoppubt')->model('sprodrelease');
        $info = app::get('sysshop')->model('shop_notice');
        $infoData = input::get();
        $data = $standard->getRow('*',array('standard_id'=>$infoData['standard_id']));
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