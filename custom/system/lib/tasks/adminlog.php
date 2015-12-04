<?php
class system_tasks_adminlog extends base_task_abstract implements base_interface_task{
    public function exec($params=null)
    {
        $queue_params = array(
            'admin_userid'   => $params['admin_userid'],
            'admin_username' => $params['admin_username'],
            'created_time'   => $params['created_time'],
            'memo'           => $params['memo'],
            'status'         => $params['status'],
            'router'         => $params['router'],
            'ip'             => $params['ip'],
        );
        app::get('system')->model('adminlog')->insert($params);
    }
}


