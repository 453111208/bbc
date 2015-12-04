<?php
class syspromotion_ctl_admin_activity_register extends desktop_controller{

    public function index()
    {
        return $this->finder('syspromotion_mdl_activity_register', array(
                'title' => app::get('syspromotion')->_('报名列表'),
                'use_buildin_delete' => false,
                'use_view_tab'=>true,
                'allow_detail_popup'=>true,
                'actions' => array(
                ),
            )
        );
    }

    public function _views(){
        $objMdlActivityRegister = app::get('syspromotion')->model('activity_register');
        $sub_menu = array(
            0=>array('label'=>app::get('syspromotion')->_('未审核'),'optional'=>false,'filter'=>array('verify_status'=>array('pending'),)),
            1=>array('label'=>app::get('syspromotion')->_('审核通过'),'optional'=>false,'filter'=>array('verify_status'=>array('agree'),)),
            2=>array('label'=>app::get('syspromotion')->_('审核未通过'),'optional'=>false,'filter'=>array('verify_status'=>array('refuse'),)),
            3=>array('label'=>app::get('syspromotion')->_('全部'),'optional'=>false,'filter'=>array()),
        );

        if(isset($_GET['optional_view'])) $sub_menu[$_GET['optional_view']]['optional'] = false;

        foreach($sub_menu as $k=>$v){
            if($v['optional']==false){
                $show_menu[$k] = $v;
                if(is_array($v['filter'])){
                    $v['filter'] = array_merge(array(),$v['filter']);
                }else{
                    $v['filter'] = array();
                }
                $show_menu[$k]['filter'] = $v['filter']?$v['filter']:null;
                if($k==$_GET['view']){
                    $show_menu[$k]['newcount'] = true;
                    $show_menu[$k]['addon'] = $objMdlActivityRegister->count($v['filter']);
                }
                $show_menu[$k]['href'] = '?app=syspromotion&ctl=admin_activity_register&act=index&view='.($k).(isset($_GET['optional_view'])?'&optional_view='.$_GET['optional_view'].'&view_from=dashboard':'');
            }elseif(($_GET['view_from']=='dashboard')&&$k==$_GET['view']){
                $show_menu[$k] = $v;
            }
        }
        return $show_menu;
    }

    public function approve()
    {
        $postdata = input::get();
        if( !trim($postdata['reason']) && $postdata['status'] == 'refuse' )
        {
            return $this->splash('error',null,'请填写驳回原因',true);
        }
        $apiData = array(
             'activity_id' => (int) $postdata['activity_id'],
             'shop_id' => (int) $postdata['shop_id'],
             'status' => $postdata['status'],
             'reason' => trim($postdata['reason']),
        );
        try{
            $flag = app::get('syspromotion')->rpcCall('promotion.activity.register.approve', $apiData);
            if($flag){
                return $this->splash('success',null,'操作成功',true);
            }else{
                return $this->splash('error',null,'操作失败',true);
            }
            return $this->splash('success',$url,$msg,true);
        } catch (\LogicException $e) {
            return $this->splash('error',null,$e->getMessage(),true);
        }
    }

    public function refuse()
    {
        $pagedata = input::get();
        return view::make('syspromotion/activity/register/refuse.html', $pagedata);
    }

}
