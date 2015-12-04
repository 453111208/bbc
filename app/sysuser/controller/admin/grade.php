<?php
class sysuser_ctl_admin_grade extends desktop_controller{

    public $workground = 'sysuser.wrokground.user';

    public function index()
    {
        $mdl = app::get('sysuser')->model('user_grade');
        $count = $mdl->count();
        if($count < 8)
        {
            $actions[] = array(
                'label' => '添加等级',
                'href' => '?app=sysuser&ctl=admin_grade&act=create',
                'target'=>'dialog::{title:\''.app::get('sysuser')->_('会员等级添加').'\',width:500,height:400}'
            );
        }
        return $this->finder('sysuser_mdl_user_grade',array(
            'title' => app::get('sysuser')->_('会员等级列表'),
            'actions'=>$actions,
        ));
    }

    public function create($gradeId)
    {
        if($gradeId)
        {
            $objMdlGrade = app::get('sysuser')->model('user_grade');
            $pagedata['grade'] = $objMdlGrade->getRow('*',array('grade_id'=>$gradeId));
        }
        return view::make('sysuser/admin/user/grade.html',$pagedata);
    }

    public function saveGrade()
    {
        $postdata = input::get('grade');
        $objGrade = kernel::single('sysuser_grade');
        $db = app::get('sysuser')->database();
        $db->beginTransaction();
        try
        {
            $objGrade->saveGrade($postdata);
            $this->adminlog("添加、编辑会员等级[{$postdata['grade_name']}]", 1);
            $db->commit();
        }
        catch(Exception $e)
        {
            $this->adminlog("添加、编辑会员等级[{$postdata['grade_name']}]", 0);
            $msg = $e->getMessage();
            $db->rollback();
            return $this->splash('error',null,$msg);
        }
        return $this->splash('success',null,"会员等级保存成功");
    }
}
