<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_view_helper{


    function block_tab($params, $content, $template){
        if(null!==$content){
            for($i=count($template->_tag_stack);$i>0;$i--){
                if($template->_tag_stack[$i-1][0]=='tabber'){
                    $id = $template->_tag_stack[$i-1][1]['_tabid'].'-'.intval($template->_tag_stack[$i-1][1]['_i']++);
                    foreach($params as $k=>$v){
                        if($k!='name' && $k!='url'){
                            $attrs[] = $k.'="'.htmlspecialchars($v).'"';
                        }
                    }
                    $template->_tag_stack[$i-1][1]['items'][$id]=$params;
                    if(!isset($template->_tag_stack[$i-1][1]['current']) || $params['current']){
                        $template->_tag_stack[$i-1][1]['current']=$id;
                    }
                    break;
                }
            }
            return '<div id="'.$id.'" style="display:none" '.implode(' ',(array)$attrs).'>'.$content.'</div>';
        }
    }

    function block_tabber($params, $content, $template){
        if(null===$content){
            $i = count($template->_tag_stack)-1;
            $template->_tag_stack[$i][1]['_tabid']=substr(md5(rand(0,time())),0,6);
            $template->_tag_stack[$i][1]['_i']=0;
        }else{
            foreach($params as $k=>$v){
                if($k!='items' && $k!='class'){
                    $attrs[] = $k.'="'.htmlspecialchars($v).'"';
                }
            }

            foreach($params['items'] as $k=>$v){
                $cls = $k==$params['current']?'tab current':'tab';
                $a = array_slice($params['items'],0,count($params['items']));
                unset($a[$k]);
                $a = "['".$k.'\',[\''.implode('\',\'',array_keys($a)).'\']]';
                $c="['current','tab']";
                $handle[]="<li class=\"{$cls} {$v['class']}\"".($v['url']?('url="'.$v['url'].'"'):'')." onclick=\"setTab({$a},{$c})\" id=\"_{$k}\"><span>{$v['name']}</span></li>";
            }
            return '<div class="tabs-wrap'.($params['class']?(' '.$params['class']):'').'" '.implode(' ',$attrs).'><ul>'.implode(' ',$handle).'</ul></div><div class="tabs">'.str_replace('id="'.$params['current'].'" style="display:none"','id="'.$params['current'].'"',$content).'</div>';
        }
    }


    function block_help($params, $content, $template){
        if(null!==$content){
            $help_types = array(
                'info'=>array('size'=>18,'icon'=>app::get('desktop')->res_url.'/images/bundle/tips_info.gif'),
                'help'=>array('size'=>18,'icon'=>app::get('desktop')->res_url.'/images/bundle/tips_help.gif'),
                'dialog'=>array('size'=>18,'icon'=>app::get('desktop')->res_url.'/images/bundle/tips_info.gif','dialog'=>1),
                'link'=>array('size'=>15,'icon'=>app::get('desktop')->res_url.'/images/bundle/tips_help.gif'),
                'link-mid'=>array('size'=>14,'icon'=>app::get('desktop')->res_url.'/images/bundle/tips_help_mid.gif'),
                'link-small'=>array('size'=>12,'icon'=>app::get('desktop')->res_url.'/images/bundle/tips_help_small.gif'),
            );
            $params['dom_id'] = view::ui()->new_dom_id();
            if($content=trim($content)){
                $params['text'] = preg_replace( array('/\n/','/\r/','/\"/','/\'/'), array('<br>','<br>','&quot;','&#39;'), $content);
            }
            $params['type'] = isset($help_types[$params['type']])?$help_types[$params['type']]:$help_types['help'];

            $pagedata = (array)$params;
            $output = view::make('desktop/helper.html', $pagedata)->render();
            return $output;
        }
    }

    function block_permission($params, $content, $template){
        //没有权限则增加属性diabled='true',以使不能编辑-@lujy
        if($params['perm_id'] && !kernel::single('desktop_user')->has_permission($perm_id))
        {
            if($params['noshow']){return null;}
            $content = preg_replace('/readonly\s*=?\s*((["\']?)[\w\s\r\n-]*\2)?/i', '', $content);
            $content = preg_replace('/(<input|<select|<textarea|<button)/i', '$1 readonly', $content);
            return $content;
        }
        return $content;
    }

    function function_filter($params, $template){
        $o = new desktop_finder_builder_filter_render();
        $o->name_prefix = $params['name'];
        if($params['app']){
            $app = app::get($params['app']);
        }else{
            throw new \InvalidArgumentException('filter tag missing app argument. detail:'.var_export($params, 1));
        }
        $html = $o->main($params['object'],$app,$filter);
        return $html;
    }

    public function function_desktop_header($params, $template)
    {
        $services = kernel::servicelist("desktop_view_helper");
        foreach($services AS $service){
            if(method_exists($service, 'function_desktop_header'))
                $html .= $service->function_desktop_header($params);
        }
        return $html;
    }//End Function

    public function function_desktop_footer($params, $template)
    {
        $services = kernel::servicelist("desktop_view_helper");
        foreach($services AS $service){
            $html .= $service->function_desktop_footer($params);
        }
        return $html;
    }//End Function

    function modifier_userdate($timestamp){
        return utils::mydate(app::get('desktop')->getConf('format.date'),$timestamp);
    }

    function modifier_usertime($timestamp){
        return utils::mydate(app::get('desktop')->getConf('format.time'),$timestamp);
    }

}
