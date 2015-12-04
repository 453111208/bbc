<?php
class system_tasks_sendemail extends base_task_abstract implements base_interface_task{
    public function exec($params=null)
    {
        $result = messenger::sendEmail($params['email'],$params['tmpl'],$params['content']);
        if($result['rsp'] == "succ")
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}


