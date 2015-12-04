<?php
class desktop_service_login{

    function __construct($app){
        $this->app = $app;
    }

    public function listener_login($params)
    {
        $account_type = pamAccount::getAuthType('desktop');
        if($account_type === $params['type'] && $params['member_id'])
        {
            $users = app::get('desktop')->model('users') ;
            if($row = $users->getList('*',array('user_id'=>$params['member_id'])))
            {
                $sdf['lastlogin'] = time();
                $sdf['lastip'] = request::getClientIp();
                $sdf['logincount'] = $row[0]['logincount']+1;
                $users->update($sdf,array('user_id'=>$params['member_id']));
            }
        }
    }
}
?>
