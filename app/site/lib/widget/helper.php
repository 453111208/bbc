<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_widget_helper
{
    function function_header(){
        $ret='<base href="'.kernel::base_url(1).'"/>';
        $path = app::get('site')->res_full_url;

        $debug_css = config::get('app.debug', false);
        $debug_js = config::get('app.debug', false);
        $css_mini = $debug_css ? '' : '.min';
        $cssver = view::ui()->getVer($debug_css);
        $jsver = view::ui()->getVer($debug_js);
        $ret.='<link rel="stylesheet" href="'.$path.'/stylesheets/widgets_edit'.$css_mini.'.css'.$cssver.'" />';
        $ret.= view::ui()->lang_script(array('src'=>'lang.js', 'app'=>'site'));
        if($debug_js){
            $ret.= '<script src="'.$path.'/scripts/lib/mootools.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/lib/moomore.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/jstools.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/switchable.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/dragdropplus.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/widgetsinstance.js'.$jsver.'"></script>';
        }else{
            $ret.= '<script src="'.$path.'/scripts/lib/mootools.min.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/ui.min.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/widgetsinstance.min.js'.$jsver.'"></script>';
        }

        if($theme_info=(app::get('site')->getConf('site.theme_'.app::get('site')->getConf('current_theme').'_color'))){
            $theme_color_href=kernel::base_url(1).'/themes/'.app::get('site')->getConf('current_theme').'/'.$theme_info;
            $ret.="<script>
            window.addEvent('domready',function(){
                new Element('link',{href:'".$theme_color_href."',rel:'stylesheet'}).injectBottom(document.head);
             });
            </script>";
        }
        /*$ret .= '<script>
                    window.addEvent(\'domready\',function(){(parent.loadedPart[1])++});
                    parent.document.getElementById(\'loadpart\').style.display="none";
                    parent.document.getElementById(\'body\').style.display="block";
                </script>';
*/
        foreach(kernel::serviceList('site_theme_view_helper') AS $service){
            if(method_exists($service, 'function_header')){
                $ret .= $service->function_header();
            }
        }

        return $ret;
    }

    function function_footer(){
        return '<div id="drag_operate_box" class="drag_operate_box" style="visibility:hidden;">
        <div class="drag_handle_box">
          <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td width="117" align="left" style="text-align:left;"><span class="add-widgets-wrap"><a class="btn-operate btn-edit-widgets" title="'.app::get('site')->_('编辑此挂件').'">'.app::get('site')->_('编辑').'</a><!--<a class="btn-operate btn-save-widgets">'.app::get('site')->_('另存为样例').'</a>--> <a class="btn-operate btn-add-widgets" id="btn_add_widget"><i class="icon"></i>'.app::get('site')->_('添加挂件').'</a><ul class="widget-drop-menu" id="add_widget_dropmenu"><li class="before" title="'.app::get('site')->_('添加到此挂件上方').'">'.app::get('site')->_('添加到上方').'</li><li class="after" title="'.app::get('site')->_('添加到此挂件下方').'">'.app::get('site')->_('添加到下方').'</li></ul></span></td>
              <td class="operate-title" style="_width:85px;" align="center"><a class="btn-operate btn-up-slot" title="'.app::get('site')->_('上移').'">&#12288;</a> <a class="btn-operate btn-down-slot" title="'.app::get('site')->_('下移').'">&#12288;</a></td>
              <td width="36" align="right" style="text-align:right;"><a class="btn-operate btn-del-widgets" title="'.app::get('site')->_('删除此挂件').'">'.app::get('site')->_('删除').'</a></td>
            </tr>
          </table>
        </div>
        <div class="drag_rules" style="display:none;">
          <div class="drag_left_arrow">&larr;</div>
          <div class="drag_annotation"><em></em></div>
          <div class="drag_right_arrow">&rarr;</div>
        </div>
        </div>
        <div id="drag_ghost_box" class="drag_ghost_box" style="visibility:hidden"></div>
        <script>new top.DropMenu($("btn_add_widget"), {menu:$("add_widget_dropmenu"),eventType:"mouse",offset:{x:-1, y:0},temppos:true,relative:$$(".add-widgets-wrap")[0]});</script>';
    }


}//End Class
