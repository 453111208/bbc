<?php

class search_task
{

    public function pre_install()
    {
        app::get('base')->setConf('app_search_is_actived','true');
        app::get('search')->setConf('search_server_policy','search_policy_mysql');
        logger::info('Initial search');
    }//End Function

    public function post_uninstall(){
        app::get('base')->setConf('app_search_is_actived','false');
    }

}//End Class
