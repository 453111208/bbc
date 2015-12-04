<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class desktop_view_input
{
    function input_image($params)
    {
        $params['type'] = 'image';
        $domid = view::ui()->new_dom_id();

        $input_name = $params['name'];
        $input_value = $params['value'];

        $image_src = $input_value;

        if(!$params['width']){
            $params['width']=50;
        }

        if(!$params['height']){
            $params['height']=50;
        }

        $imageInputWidth = $params['width']+24;
        $url="&quot;?app=desktop&act=alertpages&goto=".urlencode("?app=image&ctl=admin_manage&act=image_broswer")."&quot;";

        $html = '<div class="image-input clearfix" style="width:'.$imageInputWidth.'px;" gid="'.$domid.'">';
        $html.= '<div class="flt"><div class="image-input-view" style="font-size:12px;text-align:center;width:';
        $html.=  $params['width'].'px;line-height:'.$params['height'].'px;height:'.$params['height'].'px;overflow:hidden;">';
        if(!$image_src){
            $image_src = app::get('desktop')->res_url.'/images/transparent.gif';
        }
        $html.= '<img src="'.$image_src.'" onload="$(this).zoomImg('.$params['width'].','.$params['height'].',function(mw,mh,v){this.setStyle(&quot;marginTop&quot;,(mh-v.height)/2)});"/>';
        $html.= '</div></div>';
        $html.= '<div class="image-input-handle" onclick="Ex_Loader(&quot;modedialog&quot;,function(){new imgDialog('.$url.',{handle:this});}.bind(this));" style="width:20px;height:'.$params['height'].'px;">'.app::get('desktop')->_('选择')."".view::ui()->img(array('src'=>'bundle/arrow-down.gif','app'=>'desktop'));
        $html.= '</div>';
        $html.= '<input type="hidden" name="'.$input_name.'" value="'.$input_value.'"/>';
        $html.= '</div>';

        return $html;
    }

    function input_image_button($params)
    {
        /** button html **/
        $pagedata['name'] = $params['name']?$params['name']:'';
        $pagedata['value'] = $params['value']?$params['value']:'';
        $pagedata['link_name'] = $params['link_name'];
        $pagedata['link_cls'] = $params['link_cls']?$params['link_cls']:'';
        $pagedata['link_id'] = $params['link_id']?$params['link_id']:'';
        $pagedata['link_value'] = $params['link_value']?$params['link_value']:'';

        return view::make('desktop/ui/input_image_btn.html','desktop', $pagedata)->render();
    }

    function input_object($params){
        $return_url = $params['return_url']?$params['return_url']:'?app=desktop&ctl=editor&act=object_rows';
        $callback = $params['callback']?$params['callback']:'';
        $init = $params['data']['init']?$params['data']['init']:'';
        $params['breakpoint'] = isset($params['breakpoint'])?$params['breakpoint']:20;
        $params['limitmax'] = $params['limitmax'] ? $params['limitmax'] : '';
        $extendvalue = $params['extendvalue'] ? $params['extendvalue'] : '';

        $object = $params['object'];
        if(strpos($params['object'],'@')!==false){
            list($object,$app_id) = explode('@',$params['object']);
            $params['object'] = $object;
        }elseif($params['app']){
            $app_id = $params['app'];
        }else{
            throw new \InvalidArgumentException('missing app argument in typeo of object input tag in '.__FILE__.' on line '.__LINE__.'detail:'.var_export($params, 1));
        }

        $app = app::get($app_id);
        $o = $app->model($object);

        $dbschema = $o->get_schema();

        $params['app_id'] = $app_id;

        if(isset($params['filter'])){
            if(!is_array($params['filter'])){
                parse_str($params['filter'],$params['filter']);
            }
        }

        $params['domid'] = substr(md5(uniqid()),0,6);

        $key = $params['key']?$params['key']:$dbschema['idColumn'];
        $textcol = $params['textcol']?$params['textcol']:$dbschema['textColumn'];


        //显示列 可以多列显示 不完全修改 。。。。。。。
        $textcol = explode(',',$textcol);
        $_textcol = $textcol;
        $textcol = $textcol[0];


        $tmp_filter = $params['filter']?$params['filter']:null;
        $count = $o->count($tmp_filter);
        if($count<=$params['breakpoint']&&!$params['multiple']&&$params['select']!='checkbox'){
            if(strpos($textcol,'@')===false){
                $list = $o->getList($key.','.$textcol,$tmp_filter);
                if(!$list[0]) $type=array();
                foreach($list as $row){
                    $label = $row[$textcol];
                    if(!$label&&method_exists($o,'title_modifier')){
                        $label = $o->title_modifier($row[$key]);
                    }
                    $type[$row[$key]] = $label;
                }

            }else{
                list($name,$table,$app_id) = explode('@',$textcol);
                $app = $app_id?app::get($app_id):$app;
                $mdl = $app->model($table);
                $list = $o->getList($key,$tmp_filter);
                foreach($list as $row){
                    $tmp_row = $mdl->getList($name,array($mdl->idColumn=>$row[$key]),0,1);
                    $label = $tmp_row[0][$name];
                    if(!$label&&method_exists($o,'title_modifier')){
                        $label = $o->title_modifier($row[$key]);
                    }
                    $type[$row[$key]] = $label;
                }

            }
            $tmp_params['name'] = $params['name'];
            $tmp_params['value'] = $params['value'];
            $tmp_params['type'] = $type;
            if($callback)
                $tmp_params['onchange'] = $callback.'(this)';
            $str_filter = view::ui()->input($tmp_params);
            unset($tmp_params);
            return $str_filter;

        }

        $params['idcol'] = $keycol['keycol'] = $key;
        $params['textcol'] = implode(',',$_textcol);

        $params['_textcol'] = $_textcol;
        if($params['value']){
            if(strpos($params['view'],':')!==false){
                list($view_app,$view) = explode(':',$params['view']);
                $params['view_app'] = $view_app;
                $params['view'] = $view;
            }
            if(is_string($params['value'])){
                $params['value'] = explode(',',$params['value']);
            }
            $params['items'] = &$o->getList('*',array($key=>$params['value']),0,-1);

            //过滤不存在的值
            //某些数据被添加后 可能原表数据已删除，但此处value中还存在。
            $_params_items_row_key = array();
            foreach( $params['items'] as $k=>$_params_items_row ) {
                $_params_items_row_key[] = $_params_items_row[$key];
                if($extendvalue)
                {
                    $params['items'][$k]['extendvalue'] = $extendvalue[$_params_items_row[$key]];
                }
            }
            $params['value'] = implode(',',$_params_items_row_key);
        }

        if(isset($params['multiple']) && $params['multiple']){
            if(isset($params['items']) && count($params['items'])){
                $params['display_datarow'] = 'true';
            }
            $pagedata['_input'] = $params;
            return view::make('desktop/finder/input.html', $pagedata)->render();
        }else{
            if($params['value'] && $params['select'] != 'checkbox'){
                $string = $params['items'][0][$textcol];
            }else{
                $string = $params['emptytext']?$params['emptytext']:app::get('desktop')->_('请选择...');
            }

            $str_app = $params['app'];
            unset($params['app']);

            if($params['data']){
                $_params = (array)$params['data'];
                unset($params['data']);
                $params = array_merge($params,$_params);
            }

            if($params['select']=='checkbox'){
                if($params['default_id'] ) $params['domid'] = $params['default_id'];
                $params['type'] = 'checkbox';
            }else{
                $id = "handle_".$params['domid'];
                $params['type'] = 'radio';
                $getdata = '&singleselect=radio';
            }
            if(is_array($params['items'])){
                foreach($params['items'] as $key=>$item){
                    $items[$key] = $item[$params['idcol']];
                }
            }
            $params['return_url'] = urlencode($return_url);
            $vars = $params;
            $vars['items'] = $items;
            $getobject = array(
                'obj_filter'=>$vars['obj_filter'],
                'filter' =>$vars['filter'],
                'name'=>$vars['name'],
                'object'=>$vars['object'],
            );
            $getobject=http_build_query($getobject);

            $object = http_build_query($vars);

            //$url = '?app=desktop&act=alertpages&goto='.urlencode('?app=desktop&ctl=editor&act=finder_common&app_id='.$app_id.'&'.$object.$getdata);
            $url = '?app=desktop&act=alertpages&goto='.urlencode('?app=desktop&ctl=editor&act=finder_common&app_id='.$app_id.'&'.$getobject.$getdata);

            $pagedata['string'] = $string;
            $pagedata['url'] = $url;
            $pagedata['app'] = 'app='.$str_app;
            $pagedata['return_url'] = $return_url;
            $pagedata['id'] = $id;
            $pagedata['params'] = $params;
            $pagedata['object'] = $object;
            $pagedata['callback'] = $callback;
            $pagedata['init'] = $init;
            return view::make('desktop/finder/input_radio.html', $pagedata)->render();
        }
    }
    function input_html($params){

        if(defined('EDITOR_ALL_SOUCECODE')&&EDITOR_ALL_SOUCECODE){
            $params['width'] = $params['width']?$params['width']:'100%';
            $params['height'] = $params['height']?$params['height']:'100%';
            $html = "<div class='input-soucecode-panel' style='border:1px #e9e9e9 solid;background:#fff;height:300px;'>";
            $html.=$this->input_soucecode($params);
            $html.= "</div>";

            return $html;
        }


        $id = 'mce_'.substr(md5(rand(0,time())),0,6);
        $editor_type=app::get('desktop')->getConf("system.editortype");
        $editor_type==''?$editor_type='wysiwyg':$editor_type='wysiwyg';
        $includeBase=$params['includeBase']?$params['includeBase']:true;
        $params['id']=$id;

        $img_src = app::get('desktop')->res_url .'/images';
        $pagedata['id'] = $id;
        $pagedata['img_src'] = $img_src;
        $pagedata['includeBase'] = $includeBase;
        $pagedata['params'] = $params;

        $style2=view::make('desktop/editor/html_style2.html', $pagedata)->render();

        if($editor_type =='textarea'||$params['editor_type']=='textarea'){
            $html=$style2;
        }else{
            $style1 = view::make('desktop/editor/html_style1.html', $pagedata)->render();
            $html=$style1;
            $html.=$style2;
        }
        return $html;
    }
    function input_soucecode($params){

        $id = 'sh_'.substr(md5(rand(0,time())),0,6);
        $params['id']=$id;
        $params['res_url']=app::get('desktop')->res_url;

        $pagedata = $params;

        return view::make('desktop/editor/syntax_highlighter.html', $pagedata)->render();
    }

}
