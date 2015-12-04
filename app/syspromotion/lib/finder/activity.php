<?php
class syspromotion_finder_activity{
    public $column_edit = "编辑";
    public $column_edit_order = 1;
    public $column_edit_width = 10;

    public function column_edit(&$colList,$list)
    {
        foreach($list as $k=>$row)
        {
            $url = url::route('shopadmin', ['app'=>'syspromotion','act'=>'editActivity','ctl'=>'admin_activity','finder_id'=>$_GET['_finder']['finder_id'],'id'=>$row['activity_id']]);
            $target = '_blank';
            $title = '编辑';
            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
    }

    public $column_href = "预览";
    public $column_href_order = 2;
    public function column_href(&$colList,$list)
    {
        foreach($list as $k=>$row)
        {
            $url = url::action('topc_ctl_activity@index', ['id'=>$row['activity_id']]);
            $target = '_blank';
            $title = '预览';
            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
    }

    public $detail_basic = '基本信息';
    public function detail_basic($Id)
    {
        $objActivity = kernel::single('syspromotion_activity');
        $activity = $objActivity->getInfo("*",array('activity_id'=>$Id));
        $pagedata = $activity;
        return view::make('syspromotion/activity/detail.html',$pagedata)->render();
    }
}
