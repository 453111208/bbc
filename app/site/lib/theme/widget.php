<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_theme_widget
{

    var $widgets_exists;

    public function __construct() 
    {
        $this->themesdir = array('wap'=>WAP_THEME_DIR, 'pc'=>THEME_DIR);
    }

    public function count_widgets_by_theme($sTheme)
    {
        $db = app::get('site')->model('widgets_instance')->database();
        return $db->executeQuery('select count("widgets_id") as num from site_widgets_instance where core_file like ?', [$sTheme.'%'])->fetchColumn();
    }

    public function delete_widgets_by_theme($sTheme)
    {
        if (!is_string($sTheme) || $sTheme==='' ) return false;
        $db = app::get('site')->database();
        $db->beginTransaction();
        try
        {
            app::get('site')->model('widgets_instance')->database()->executeQuery('delete from site_widgets_instance where core_file like ?', [$sTheme.'/%']);
            app::get('site')->model('themes_tmpl')->delete(array('theme'=>$sTheme));
            $db->commit();
        }
        catch(\Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        
        return true;
    }

    public function insert_widgets($aData)
    {
        //modfity by EDwin 2010/5/7
        if($aData['base_file']){
            $aData['core_file'] = substr($aData['base_file'], strpos($aData['base_file'], ':')+1);
            $aData['core_slot'] = $aData['base_slot'];
            $aData['core_id'] = $aData['base_id'];
            unset($aData['base_file']);
            unset($aData['base_slot']);
            unset($aData['base_id']);
        }//fix template install
        $aData['modified'] = time();
        return app::get('site')->model('widgets_instance')->insert($aData);
    }

    public function save_widgets($widgets_id, $aData)
    {
        if(!is_numeric($widgets_id)) return false;
        $aData['widgets_id'] = $widgets_id;
        $aData['modified'] = time();
        return app::get('site')->model('widgets_instance')->save($aData);
    }//End Function

    public function save_preview_all($widgetsSet, $files)
    {
        $i=0;
        $slots = array();
        $return = array();
        $_SESSION['WIDGET_TMP_DATA'] = array();

        $model = app::get('site')->model('widgets_instance');
        foreach((array)$widgetsSet as $widgets_id=>$widgets){
            $widgets['modified'] = time();
            $widgets['widgets_order'] = $i++;
            $sql = '';
            if(is_numeric($widgets_id)){
                $slots[$widgets['core_file']][]=$widgets_id;
                if(isset($_SESSION['_tmp_wg_update'][$widgets_id])){
                    $sData = $_SESSION['_tmp_wg_update'][$widgets_id];
                }else{
                    $sData = $model->getList('*',array('widgets_id'=>$widgets_id));
                    $sData = $sData[0];
                }
                $sData = array_merge($sData,$widgets);
                $sData['widgets_id'] = $widgets_id;
                $_SESSION['WIDGET_TMP_DATA'][$widgets['core_file']][$sData['widgets_id']] = $sData;
            }elseif(preg_match('/^tmp_([0-9]+)$/i',$widgets_id,$match)){

                $wg = $_SESSION['_tmp_wg_insert'][$match[1]];

                $setting = $this->widgets_info($wg['widgets_type'], $wg['app'], $wg['theme']);

                $widgets = array_merge(
                    $widgets,
                    $wg,
                    array(  'vary'=>$setting['vary'],
                            'scope'=> is_array($setting['scope'])?(','.implode($setting['scope'],',').','):$setting['scope'])
                );

                if(!$widgets_id){
                    return false;
                }else{
                    $return[$_SESSION['_tmp_wg_insert'][$match[1]]['_domid']] = $widgets_id;
                    $slots[$widgets['core_file']][]=$widgets_id;

                    $_SESSION['WIDGET_TMP_DATA'][$widgets['core_file']][$widgets_id] = $widgets;
                }
            }
        }

        return $return;
    }//End Function

    public function save_all($widgetsSet, $files)
    {
        $i=0;
        $slots = array();
        $return = array();
        $model = app::get('site')->model('widgets_instance');
        foreach((array)$widgetsSet as $widgets_id=>$widgets){
            $widgets['modified'] = time();
            $widgets['widgets_order'] = $i++;
            $sql = '';
            if(is_numeric($widgets_id)){
                $slots[$widgets['core_file']][]=$widgets_id;
                $sData = $_SESSION['_tmp_wg_update'][$widgets_id];
                $sData['widgets_id'] = $widgets_id;
                $sData['widgets_order'] = $widgets['widgets_order'];
                if(!$model->save($sData)){
                    return false;
                }
            }elseif(preg_match('/^tmp_([0-9]+)$/i',$widgets_id,$match)){

                $wg = $_SESSION['_tmp_wg_insert'][$match[1]];
                $setting = $this->widgets_info($wg['widgets_type'], $wg['app'], $wg['theme']);

                $widgets = array_merge(
                    $widgets,
                    $wg,
                    array(  'vary'=>$setting['vary'],
                            'scope'=> is_array($setting['scope'])?(','.implode($setting['scope'],',').','):$setting['scope'])
                );

                $widgets_id = $model->insert($widgets);

                if(!$widgets_id){
                    return false;
                }else{
                    $return[$_SESSION['_tmp_wg_insert'][$match[1]]['_domid']] = $widgets_id;
                    unset($_SESSION['_tmp_wg_insert'][$match[1]]);
                    $count = count($slots[$widgets['core_file']]);
                    $slots[$widgets['core_file']][$count]=$widgets_id;
                }
            }
            if(!strpos($widgets['core_file'],':')){
                kernel::single('site_theme_tmpl')->touch_tmpl_file($widgets['core_file']);
            }
        }
        if(is_array($files)){
            foreach($files as $file){
                if(is_array($slots[$file])&&count($slots[$file])>0){
                    $model->database()->executeUpdate('delete from site_widgets_instance where widgets_id not in(?) and core_file=?', [$slots[$file], $file], [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, \PDO::PARAM_STR]);
                }else{
                    $model->database()->delete('site_widgets_instance', ['core_file' => $file]);
                }
                if(!strpos($file, ':')){
                    kernel::single('site_theme_tmpl')->touch_tmpl_file($file);
                }
            }
        }
        return $return;
    }//End Function

    public function widgets_exists($name, $app, $theme)
    {
        $data = $this->widgets_config($name, $app, $theme);
        if(is_dir($data['dir'])){
            return $data['dir'];
        }else{
            return false;
        }
    }//End Function


    public function widgets_info($name, $app, $theme, $key=null)
    {

        if($name&&$widgets_dir = $this->widgets_exists($name, $app, $theme)){
            include($widgets_dir . '/widgets.php');
            $setting['type'] = $name;
            return (is_null($key)) ? $setting : (isset($setting[$key]) ? $setting[$key] : '');
        }else{
            return false;
        }
    }//End Function

    public function get_widgets_info($name, $app, $key=null)
    {
        //todo:兼容老版本，无模板挂件
        return $this->widgets_info($name, $app, '', $key);
    }//End Function

    public function widgets_config($name, $app, $theme)
    {
        if(empty($theme)){
            return false;
        }else{
            $data['dir'] = THEME_DIR . '/' . $theme . '/widgets/' . $name;
            $data['app'] = null;
            $data['url'] = kernel::base_url(1) . '/themes/' . $theme . '/widgets/' . $name;
            $data = ecos_cactus('site','theme_widget_widgets_config_theme',$name, $data, $theme);
        }
        return $data;
    }//End Function

    public function get_libs($theme)
    {
        $qb = app::get('site')->database()->createQueryBuilder();
        $data = $qb->select('*')->from('site_widgets')->where('app!=\'\'')->orWhere('theme='.$qb->createPositionalParameter($theme))->execute()->fetchAll();
        $widgetsLib1 = array();
        foreach($data AS $val){
            if($val['app']==''){
                $info1 = $this->widgets_info($val['name'], $val['app'], $val['theme']);
                $widgetsLib1 = ecos_cactus('site','theme_widget_widgets_get_libs_notype',$info1, $val, $widgetsLib1);
            }
        }
        $widgetsLib['themelist'] = $widgetsLib1['list'];
        $widgetsLib['usual'] = $widgetsLib1['usual'];
        return $widgetsLib;
    }//End Function

    public function get_libs_extend($theme, $type='')
    {
        if($theme){
            $qb = app::get('site')->database()->createQueryBuilder();
            $data = $qb->select('*')->from('site_widgets')->where('theme='.$qb->createPositionalParameter($theme))->execute()->fetchAll();
        }
        $widgetsLib = array();
        $order=array();
        if($type==null){
            foreach($data AS $val){
                $info = $this->widgets_info($val['name'], $val['app'], $val['theme']);
                $widgetsLib = ecos_cactus('site','theme_widget_widgets_get_libs_notype',$info, $val, $widgetsLib);
            }
        }else{
            foreach($data AS $val){
                $info = $this->widgets_info($val['name'], $val['app'], $val['theme']);
                $widgetsLib = ecos_cactus('site','theme_widget_widgets_get_libs_type',$info, $type, $val, $widgetsLib);
            }
            array_multisort($order, SORT_DESC, $widgetsLib['list']);
        }
        return $widgetsLib;

    }//End Function

    public function get_this_widgets_info($widgets, $app, $theme){
        $info = $this->widgets_info($widgets, $app, $theme);
        $widgetsLib = array('description'=>$info['description'],'catalog'=>$info['catalog'],'label'=>$info['name']);
        return $widgetsLib;
    }

    public function admin_load($file, $slot, $id=null, $edit_mode=false){
        
        if(!$this->fastmode && $edit_mode){
            $this->fastmode=true;
        }

        $model = app::get('site')->model('widgets_instance');
        $qb = $model->database()->createQueryBuilder();
        $qb->select('*')->from('site_widgets_instance')->where('core_file='.$qb->createPositionalParameter($file))->orderBy('widgets_order', 'asc');
        if(!$id){
            $qb->andWhere('core_slot=', $qb->createPositionalParameter($slot));
        }else{
            $qb->andWhere('core_id='.$qb->createPositionalParameter($id));
        }
        // 因为数据有serializes数据. 因此取出后要经过 tidy_data处理
        $rows = $model->tidy_data($qb->execute()->fetchAll());
        
        if(!strpos($file, ':')){
            $theme= substr($file,0,strpos($file,'/'));
        }else{
            $theme = kernel::single('site_theme_base')->get_default();
        }
        $obj_session = kernel::single('base_session');
        $obj_session->start();

        foreach($rows as $widgets){
            //$_SESSION['WIDGET_TMP_DATA'][$widgets['core_file']][$widgets['widgets_id']] = $widgets;
            $_SESSION['_tmp_wg_update'][$widgets['widgets_id']] = null;
            if($widgets['widgets_type']=='html')$widgets['widgets_type']='usercustom';
            $widgets['html'] = $this->fetch($widgets);

            $title=$widgets['title']?$widgets['title']:$widgets['widgets_type'];

            // 无边框
            $widgets_box= '<div class="shopWidgets_box clearfix" widgets_id="'.$widgets['widgets_id'].'" title="'.$title.'" widgets_theme="'.$theme.'">'.$this->admin_wg_border($widgets,$theme).'</div>';

            $widgets_box=preg_replace("/<object[^>]*>([\s\S]*?)<\/object>/i","<div class='sWidgets_flash' title='Flash'>&nbsp;</div>",$widgets_box);
            $replacement=array("'onmouse'i","'onkey'i","'onmousemove'i","'onload'i","'onclick'i","'onselect'i","'unload'i");
            $widgets_box=preg_replace($replacement,array_fill(0,count($replacement),'xshopex'),$widgets_box);
			$theme_url = kernel::get_themes_host_url().'/'.$theme;
            $widgets_box = str_replace('%THEME%', $theme_url, $widgets_box);
            echo preg_replace("/<script[^>]*>([\s\S]*?)<\/script>/i","",$widgets_box);

        }

        $obj_session->close();
    }//End Function

    public function fetch($widgets, $widgets_id=null){

        $widgets_config = $this->widgets_config($widgets['widgets_type'], $widgets['app'], $widgets['theme']);
        $widgets_dir = $widgets_config['dir'];

        $func_file = $widgets_config['func'];
        $cur_theme = kernel::single('site_theme_base')->get_default();


        if(file_exists($func_file)){
            $this->_errMsg = null;
            $this->_run_failed = false;
            include_once($func_file);
            if(function_exists($widgets_config['run'])){
                $menus = array();
                $func = $widgets_config['run'];

                $pagedata['data'] = $func($widgets['params']);
            }
            if($this->_run_failed)
                return $this->_errMsg;
        }

        $pagedata['setting'] = $widgets['params'];
        $pagedata['widgets_id'] = $widgets_id;

        try
        {
            theme::uses($widgets['theme']);
            $previewPath = theme::getThemeNamespace('widgets/'.$widgets['widgets_type'].'/_preview.html');
            view::getFinder()->find($previewPath);
            $return = view::make($previewPath, $pagedata);
            if($return!==false){
                $return = ecos_cactus('site','theme_widget_prefix_content',$return, $widgets_config['url']);
            }
            return $return;
        }
        catch (\InvalidArgumentException $e)
        {
            if($this->fastmode){
                return '<div class="widgets-preview">'.$widgets['widgets_type'].'</div>';
            }
            try
            {
                theme::uses($widgets['theme']);
                $previewPath = theme::getThemeNamespace('widgets/'.$widgets['widgets_type'].'/'.$widgets['tpl']);

                $return = view::make($previewPath, $pagedata);
            }
            catch (\InvalidArgumentException $e)
            {
                $pagedata['tpl'] = $tpl;
                $content = view::make('site/admin/theme/widgets_tpl_lost.html', $pagedata);    //todo: 无模板提示
            }
        }
        if($return!==false)
        {
            $return = ecos_cactus('site','theme_widget_prefix_content',$return, $widgets_config['url']);
        }
        return $return;


    }//End Function

    public function admin_wg_border($widgets, $theme, $type=false)
    {
        if($type)
        {
            $content="{$widgets['html']}";
            $wReplace=Array('<{$body}>','<{$title}>','<{$widgets_classname}>','"<{$widgets_id}>"');
            $title=$widgets['title']?$widgets['title']:$widgets['widgets_type'];
            $wArt=Array($content,$widgets['title'],
                        $widgets['classname']
                        ,($widgets['domid']?$widgets['domid']:'widgets_'.$widgets['widgets_id']).' widgets_id="'.$widgets['widgets_id'].'"  title="'.$title.'"'.' widgets_theme="' . $theme . '"');
            $tpl='<div class="shopWidgets_box clearfix" widgets_id="'.$widgets['widgets_id'].'" title="'.$title.'" widgets_theme="'.$theme.'">'.$content.'</div>';
        }
        else
        {
            $tpl="{$widgets['html']}";
        }

        return trim(preg_replace('!\s+!', ' ', $tpl));
    }

    public function editor($widgets, $widgets_app, $widgets_theme, $theme, $values=false)
    {
        $return = array();
        $widgets_config = $this->widgets_config($widgets, $widgets_app, $widgets_theme);
        $widgets_dir = $widgets_config['dir'];

        $setting = $this->widgets_info($widgets, $widgets_app, $widgets_theme);



        is_array($values) or $values=array();
        $values = array_merge($setting, $values);

        if(!empty($setting['template'])){
            if(!is_array($setting['template'])){
                $setting['template'] = array($setting['template']=>'DEFAULT');
            }
            $return['tpls'][$file]=$setting['template'];
        }else{
            if($widgets=='html'){
                $widgets='usercustom';
                if(!$values['usercustom']) $values['usercustom']= $values['html'];
            }
            if ($handle = opendir($widgets_dir)) {
                while (false !== ($file = readdir($handle))) {
                    if(substr($file,0,1)!='_' && strtolower(substr($file,-5))=='.html' && file_exists($widgets_dir.'/'.$file)){
                        $return['tpls'][$file]=$file;
                    }
                }
                closedir($handle);
            }else{
                return false;
            }
        }

        $cur_theme = $theme;

        try
        {
            $isFind =true;
            theme::uses($theme);
            $configPath = theme::getThemeNamespace('widgets/'.$widgets.'/_config.html');
            view::getFinder()->find($configPath);
        }
        catch (\InvalidArgumentException $e)
        {
            $isFind = false;
        }

        if($isFind)
        {
            $sFunc=$widgets_config['crun'];
            $sFuncFile = $widgets_config['cfg'];
            if(file_exists($sFuncFile)){
                include_once($sFuncFile);
                if(function_exists($sFunc))
                {
                    $pagedata['data'] = $sFunc($widgets_config['app']);
                }
            }

            $pagedata['setting'] = &$values;

            //$compile_code = $smarty->fetch_admin_widget($widgets_dir.'/_config.html',$widgets_app);
            $compile_code = view::make($configPath, $pagedata)->render();
            if($compile_code){
                $compile_code = ecos_cactus('site','theme_widget_prefix_content',$compile_code, $widgets_config['url']);
            }
            $return['html'] = $compile_code;
        }
        return $return;
    }

}//End Class
