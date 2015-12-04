<?php
class system_tasks_sendsms extends base_task_abstract implements base_interface_task{
    public function exec($params=null)
    {
        $result = messenger::sendSms($params['sms'],$params['tmpl'],$params['content']);
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


