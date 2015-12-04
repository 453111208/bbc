<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_admin_controller extends desktop_controller
{

    /*
     * @param object $app
     */
    function __construct($app)
    {
        parent::__construct($app);
        $this->_response = kernel::single('base_component_response');

        $this->themesdir = array('wap'=>WAP_THEME_DIR, 'pc'=>THEME_DIR);
        //判断操作的模板是pc还是wap
        if(input::get('platform') == 'wap')
        {
            $this->platform = 'wap';
            $this->themes_dir = THEME_DIR;
        }
        else
        {
            $this->platform = 'pc';
            $this->themes_dir = WAP_THEME_DIR;
        }
    }//End Function

    /*
     * 错误
     * @param string $msg
     */
    public function _error($msg='非法操作')
    {
        return $msg;
    }//End Function

    protected function check($theme,&$msg='')
    {
        if(kernel::single('site_theme_file')->check($theme,$msg)){
            return true;
        }else{
            return false;
        }
    }//End Function

}//End Class
