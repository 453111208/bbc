<?php

class base_static_lang  
{

    static function is_need_conv() {
        if (defined('LANG')) {
            return true;
        }
        return false;
    }

    static public function _($lang_dir, $key, $args=null)
    {
        if (self::is_need_conv())
        {
            $language = kernel::get_lang();
            putenv("LANG=$language");
            setlocale(LC_ALL, $language);

            $domain = 'lang';
            bindtextdomain($domain, $lang_dir);
            textdomain($domain);
            bind_textdomain_codeset($domain, 'utf-8');
            return gettext($key);
        }
        else
        {
            return $key;
        }
    }
    
    static public function set_res($app_id, $res) 
    {
        $app_res = (array)self::get_res($app_id);
        $app_res = array_merge($app_res, (array)$res);
        return base_kvstore::instance('lang/'.$app_id)->store('res', $app_res);
    }//End Function

    static public function get_res($app_id) 
    {
        if(base_kvstore::instance('lang/'.$app_id)->fetch('res', $app_res)){
            return $app_res;
        }else{
            return array();
        }
    }//End Function

    static public function del_res($app_id) 
    {
        return base_kvstore::instance('lang/'.$app_id)->store('res', array());
    }//End Function

}//End Class
