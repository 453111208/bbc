<?php
class importexport_finder_task
{
    var $column_control = '操作';

    public function column_control(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $returnValue = '';
            $href = '';
            $value = app::get('importexport')->model('task')->getList('*',array('task_id'=>$row['task_id']));
            if($value[0]['status'] == '2'){
                $href = '?app=importexport&ctl=admin_export&act=queue_download&task_id='.$row['task_id'].'&finder_id='.$_GET['_finder']['finder_id'];
            }elseif($value[0]['status'] == '6' || $value[0]['status'] == '8'){
                $href = '?app=importexport&ctl=admin_import&act=queue_download&task_id='.$row['task_id'].'&finder_id='.$_GET['_finder']['finder_id'];
            }
            if($href){
                $returnValue = "<a href='$href' onclick='location.href="."\"$href\""."'>".app::get('importexport')->_("下载")."</a>";
                $colList[$k] = $returnValue;
            }
        }
    }
}
