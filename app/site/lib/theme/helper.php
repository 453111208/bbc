<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_theme_helper
{

    public function __construct()
    {
        $this->themesdir = array('wap'=>WAP_THEME_DIR, 'pc'=>THEME_DIR);
    }

    function function_header(){
        $ret='<base href="'.kernel::base_url(1).'"/>';
        $path = app::get('site')->res_full_url;
        $pathtopc = app::get('topc')->res_full_url;

        $debug_css = config::get('app.debug', false);
        $debug_js = config::get('app.debug', false);
        $css_mini = $debug_css ? '' : '.min';
        $js_mini = $debug_js ? '' : '.min';
        $cssver = view::ui()->getVer($debug_css);
        $jsver = view::ui()->getVer($debug_js);
        if($debug_js){
            $ret.='<link rel="stylesheet" href="'.$pathtopc.'/stylesheets/layout.css'.$cssver.'" />';
            $ret.='<link rel="stylesheet" href="'.$pathtopc.'/stylesheets/theme.css'.$cssver.'" />';
        } else {
            $ret.='<link rel="stylesheet" href="'.$pathtopc.'/stylesheets/basic'.$css_mini.'.css'.$cssver.'" />';
        }
        $ret.='<link rel="stylesheet" href="'.$path.'/stylesheets/widgets_edit'.$css_mini.'.css'.$cssver.'" />';
        $ret.= view::ui()->lang_script(array('src'=>'lang.js', 'app'=>'site'));
        $ret.= view::ui()->lang_script(array('src'=>'lang.js', 'app'=>'b2c'));
        if($debug_js){
            $ret.= '<script src="'.$path.'/scripts/lib/jquery.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/lib/jquery.cookie.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/tools.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/switchable.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/shopwidgets.js'.$jsver.'"></script>';
        }else{
            $ret.= '<script src="'.$path.'/scripts/lib/jquery'.$js_mini.'.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/ui'.$js_mini.'.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/shopwidgets'.$js_mini.'.js'.$jsver.'"></script>';
        }
        if($theme_info=(app::get('site')->getConf('site.theme_'.app::get('site')->getConf('current_theme').'_color'))){
            $theme_color_href=kernel::base_url(1).'/themes/'.app::get('site')->getConf('current_theme').'/'.$theme_info;
            $ret.="<script>
            window.addEvent('domready',function(){
                new Element('link',{rel:'stylesheet',href:'".$theme_color_href."'}).inject(document.head);
            });
            </script>";
        }
       /* $ret .= '<script>
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

    function function_wapheader(){
        $ret='<base href="'.kernel::base_url(1).'"/>';
        $path = app::get('site')->res_full_url;
        $pathtopm = app::get('topm')->res_full_url;

        $debug_css = config::get('app.debug', false);
        $debug_js = config::get('app.debug', false);
        $css_mini = $debug_css ? '' : '.min';
        $js_mini = $debug_js ? '' : '.min';
        $cssver = view::ui()->getVer($debug_css);
        $jsver = view::ui()->getVer($debug_js);
        if($debug_css){
            $ret.='<link rel="stylesheet" href="'.$pathtopm.'/stylesheets/base.css'.$cssver.'" />';
        } else {
            $ret.='<link rel="stylesheet" href="'.$pathtopm.'/stylesheets/base'.$css_mini.'.css'.$cssver.'" />';
        }
        $ret.='<link rel="stylesheet" href="'.$path.'/stylesheets/widgets_edit'.$css_mini.'.css'.$cssver.'" />';
        $ret.= view::ui()->lang_script(array('src'=>'lang.js', 'app'=>'site'));
        $ret.= view::ui()->lang_script(array('src'=>'lang.js', 'app'=>'b2c', 'pdir'=>'js_mini'));
        if($debug_js){
            $ret.= '<script src="'.$path.'/scripts/lib/jquery.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/lib/jquery.cookie.js'.$jsver.'"></script>
            <script src="'.$pathtopm.'/scripts/gmu.js'.$jsver.'"></script>
            <script src="'.$pathtopm.'/scripts/common.js'.$jsver.'"></script>
            <script src="'.$pathtopm.'/scripts/zepto.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/tools.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/shopwidgets.js'.$jsver.'"></script>';
        }else{
            $ret.= '<script src="'.$path.'/scripts/jquery'.$js_mini.'.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/ui'.$js_mini.'.js'.$jsver.'"></script>
            <script src="'.$path.'/scripts/shopwidgets'.$js_mini.'.js'.$jsver.'"></script>';
        }
        if($theme_info=(app::get('site')->getConf('site.theme_'.app::get('site')->getConf('current_theme').'_color'))){
            $theme_color_href=kernel::base_url(1).'/themes/'.app::get('site')->getConf('current_theme').'/'.$theme_info;
            $ret.="<script>
            window.addEvent('domready',function(){
                new Element('link',{rel:'stylesheet',href:'".$theme_color_href."'}).inject(document.head);
            });
            </script>";
        }
       /* $ret .= '<script>
                    window.addEvent(\'domready\',function(){(parent.loadedPart[1])++});
                    parent.document.getElementById(\'loadpart\').style.display="none";
                    parent.document.getElementById(\'body\').style.display="block";
                </script>';
*/
        foreach(kernel::serviceList('site_theme_view_helper') AS $service){
            if(method_exists($service, 'function_wapheader')){
                $ret .= $service->function_wapheader();
            }
        }

        return $ret;
    }

    function function_footer(){
        return '<div id="drag_operate_box" class="drag_operate_box" style="visibility:hidden;">
        <div class="drag_handle_box">
          <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td width="117" align="left" style="text-align:left;"><span class="add-widgets-wrap"><a class="btn-operate btn-edit-widgets" title="'.app::get('site')->_('编辑此挂件').'">'.app::get('site')->_('编辑').'</a><!--<a class="btn-operate btn-save-widgets">'.app::get('site')->_('另存为样例').'</a>--> <span style="position:relative;display:inline;" id="btn_add_widget"><a class="btn-operate btn-add-widgets"><i class="icon"></i>'.app::get('site')->_('添加挂件').'</a><ul class="widget-drop-menu" id="add_widget_dropmenu" style="margin-top:-4px;"><li class="before" title="'.app::get('site')->_('添加到此挂件上方').'">'.app::get('site')->_('添加到上方').'</li><li class="after" title="'.app::get('site')->_('添加到此挂件下方').'">'.app::get('site')->_('添加到下方').'</li></ul></span></span></td>
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
        <!--<div class="content"></div>-->
        </div>
        <div id="drag_ghost_box" class="drag_ghost_box" style="visibility:hidden"></div>
        <script>
        $("#btn_add_widget").hover(function() {
            $("#add_widget_dropmenu").show();
        },
        function(){
            $("#add_widget_dropmenu").hide();
        });
        </script>';
    }

    function function_wapfooter(){
        return '<div id="drag_operate_box" class="drag_operate_box" style="visibility:hidden;">
        <div class="drag_handle_box">
          <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td width="117" align="left" style="text-align:left;"><span class="add-widgets-wrap"><a class="btn-operate btn-edit-widgets" title="'.app::get('site')->_('编辑此挂件').'">'.app::get('site')->_('编辑').'</a><!--<a class="btn-operate btn-save-widgets">'.app::get('site')->_('另存为样例').'</a>--> <span style="position:relative;display:inline;" id="btn_add_widget"><a class="btn-operate btn-add-widgets"><i class="icon"></i>'.app::get('site')->_('添加挂件').'</a><ul class="widget-drop-menu" id="add_widget_dropmenu" style="margin-top:-4px;"><li class="before" title="'.app::get('site')->_('添加到此挂件上方').'">'.app::get('site')->_('添加到上方').'</li><li class="after" title="'.app::get('site')->_('添加到此挂件下方').'">'.app::get('site')->_('添加到下方').'</li></ul></span></span></td>
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
        <!--<div class="content"></div>-->
        </div>
        <div id="drag_ghost_box" class="drag_ghost_box" style="visibility:hidden"></div>
        <script>
        $("#btn_add_widget").hover(function() {
            $("#add_widget_dropmenu").show();
        },
        function(){
            $("#add_widget_dropmenu").hide();
        });
        </script>';
    }

}//End Class
