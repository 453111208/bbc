<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_widgets_appolymer implements desktop_interface_widget{
    
    
    function __construct($app){
    }
    
    function get_title(){
            
        return app::get('desktop')->_("应用程序");
        
    }
    function get_html(){ 
        $pagedata['data'] = '';
        return view::make('desktop/widgets/appolymer.html', $pagedata)->render();
    }
    function get_className(){
        
          return "";
    }
    function get_width(){
          
          return "normal";
        
    }
    
}

?>
