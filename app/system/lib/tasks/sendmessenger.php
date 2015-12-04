<?php
class system_tasks_sendmessenger extends base_task_abstract implements base_interface_task{
    public function exec($params=null)
    {
        $objMessenger = kernel::single($params['sendMethod']);
        $objMessenger->send($params['type'],$params['tmpl_name'],$params['data'],$params['sendTtype']);
    }
}
