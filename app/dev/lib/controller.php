<?php
class dev_controller extends base_routing_controller
{

    var $workground = 'project';
    
    function page($html, $pagedata=array()){
        $menus = array(
                'project'=>app::get('dev')->_('项目'),
                //'tools'=>'工具',
                'apps'=>app::get('dev')->_('应用程序'),
                'doc'=>app::get('dev')->_('文档'),
                'setting'=>app::get('dev')->_('系统设置'),
            );
        $pagedata['__CUR_MENU__'] = $this->workground;
        $pagedata['__MENUS__'] = $menus;
        $pagedata['__PAGE__'] = $html;
        return view::make('dev/frame.html', $pagedata);
    }
    
}
