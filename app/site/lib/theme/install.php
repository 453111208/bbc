<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_theme_install{

    public function __construct() 
    {
        $this->themesdir = array('wap'=>WAP_THEME_DIR, 'pc'=>THEME_DIR);
    }

    public function check_install($platform){
        $this->check_dir();
        $d = dir(THEME_DIR);
        while (false !== ($entry = $d->read())) {
            if(in_array($entry, array('.', '..', '.svn')))   continue;
            if(is_dir(THEME_DIR . '/' . $entry)){
                $qb = app::get('site')->database()->createQueryBuilder();
                $themeData = $qb->select('*')->from('site_themes')->where('theme='.$qb->createPositionalParameter($entry))->execute()->fetch();
                if(empty($themeData)){
                    $this->init_theme($entry);
                }
            }
            // if(!kernel::single('site_theme_base')->get_default($platform)){
            //     kernel::single('site_theme_base')->set_default($platform, $entry);
            // }
        }
        $d->close();
    }//End Function

    public function check_dir()
    {
        if(!is_dir(THEME_DIR))
            utils::mkdir_p(THEME_DIR);
    }//End Function

    public function allow_upload(&$message){
        if(!function_exists("gzinflate")){
            $message = 'gzip';
            return $message;
        }
        if(!is_writable(THEME_DIR)){
            $message = 'writeable';
            return $message;
        }
        return true;
    }

    public function remove_rf_theme($theme){
        $dir = THEME_DIR . '/' . $theme;
        $this->flush_theme($theme);
        kernel::single('site_theme_base')->delete_theme_widgets($theme);        //todo:删除模板挂件
        if(is_dir($dir)){
            $this->__remove_db_theme($theme);//删除theme_files的模板文件
            return $this->__remove_dir_theme($dir);
        }else{
            return true;
        }
    }//End Function

    public function flush_theme($theme){
        app::get('site')->model('themes')->delete(array('theme'=>$theme));
        kernel::single('site_theme_tmpl')->delete_tmpl_by_theme($theme);
        kernel::single('site_theme_widget')->delete_widgets_by_theme($theme);
    }//End Function

    public function install($file, &$msg){
        $this->check_dir();
        if(!$this->allow_upload($msg)) return false;
        $tar = kernel::single('base_tar');
        $handle = fopen($file['tmp_name'], "r");
        $contents = file_get_contents($file['tmp_name']);
        preg_match('/\<id\>([a-zA-Z0-9]*)(.*?)\<\/id\>/',$contents,$tar_name);
        $filename=$tar_name[1]?$tar_name[1]:time();
        if(is_dir(THEME_DIR.'/'.trim($filename))){
           $filename=time();
        }
        $sDir=$this->__build_dir(str_replace('\\','/',THEME_DIR.'/'.trim($filename)));
        if($tar->openTAR($file['tmp_name'], $sDir)){
            if($tar->containsFile('theme.xml')) {
                //提前实例化，通过引用传递，减少foreach中循环实例化类导致的开销
                $obj = app::get('site')->model('themes_file');
                $storager = kernel::single('base_storager');
                foreach($tar->files as $id => $file) {
                    $fpath = $sDir.$file['name'];
                    if(!is_dir(dirname($fpath))){
                        if(mkdir(dirname($fpath), 0755, true)){
                            file_put_contents($fpath,$tar->getContents($file));
                        }else{
                            $msg = app::get('site')->_('权限不允许');
                            return false;
                        }
                    }else{
                        file_put_contents($fpath,$tar->getContents($file));
                    }

                }

                $tar->closeTAR();
                if(!$config=$this->init_theme(basename($sDir),'','upload')){
                    $this->__remove_dir_theme($sDir);
                    $msg=app::get('site')->_('shopEx模板包创建失败');
                    return false;
                }

                kernel::single('site_theme_base')->install_theme_widgets($config['platform'], $filename);        //todo:安装模板挂件

                foreach(kernel::servicelist('site_theme.post_install') AS $service){
                    if(is_object($service) && method_exists($service, 'post_theme_install')){
                        $service->post_theme_install($filename);
                    }
                }

                return $config;
            }else{
                $msg = app::get('site')->_('不是标准的shopEx模板包');
                return false;
            }
        }else{
            $msg = app::get('site')->_('模板包已损坏，不是标准的shopEx模板包').$file['tmp_name'];
            return false;
        }
    }

    public function init_theme($theme, $replaceWg=false, $upload='', $loadxml=''){
        if(empty($loadxml)){
            $loadxml='theme.xml';
        }
        $sDir=THEME_DIR.'/'.$theme.'/';
        $xml = kernel::single('site_utility_xml');
        // $loadxml_content = kernel::single('site_theme_file')->get_xml_content($sDir, $loadxml);
        $loadxml_content = file_get_contents($sDir . $loadxml);

        if($loadxml_content){
            $wightes_info = $xml->xml2arrayValues($loadxml_content);
        }

        if(!empty($wightes_info)){
            $config = $wightes_info;
        }else{
            return false;
        }

        if($upload=="upload" && $config['theme']['id']['value']){
            $config['theme']['id']['value']=preg_replace('@[^a-zA-Z0-9]@','_',$config['theme']['id']['value']);
            if($this->file_rename(THEME_DIR.'/'.$theme,THEME_DIR.'/'.$config['theme']['id']['value'])){
                $sDir=THEME_DIR.'/'.$config['theme']['id']['value'];
                $theme=$config['theme']['id']['value'];
                $replaceWg=false;
            }
        }
        $aTheme=array(
            'platform'=>trim($config['theme']['platform']['value']),
            'name'=>$config['theme']['name']['value'],
            'id'=>$config['theme']['id']['value'],
            'version'=>$config['theme']['version']['value'],
            'info'=>$config['theme']['info']['value'],
            'author'=>$config['theme']['author']['value'],
            'site'=>$config['theme']['site']['value'],
            'config'=>array(
                'config'=>$this->__change_xml_array($config['theme']['config']['set']),
                //'views'=>$this->__change_xml_array($config['theme']['views']['set'])
            )
        );

        $aWidgets=$wightes_info['theme']['widgets']['widget'];
        if(isset($aWidgets['value'])){
            $aWidgetsTmep = $aWidgets;
            unset($aWidgets);
            $aWidgets[0] = $aWidgetsTmep;
        }
        $aTheme['theme']=$theme;
        $aTheme['stime']=time();

        //todo: views

        for($i=0;$i<count($aWidgets);$i++){
            if($aWidgets[$i]['attr']['coreid']) {
                $aTmp[$i]['core_file']=$aTheme['theme'].'/'.$aWidgets[$i]['attr']['file'];
                $aTmp[$i]['core_slot']=$aWidgets[$i]['attr']['slot'];
                $aTmp[$i]['core_id']=$aWidgets[$i]['attr']['coreid'];
            } else {
                $aTmp[$i]['base_file']='user:'.$aTheme['theme'].'/'.$aWidgets[$i]['attr']['file'];
                $aTmp[$i]['base_slot']=$aWidgets[$i]['attr']['slot'];
                $aTmp[$i]['base_id']=$aWidgets[$i]['attr']['baseid'];
            }
            $aTmp[$i]['widgets_type']=$aWidgets[$i]['attr']['type'];
            $aTmp[$i]['widgets_order']=$aWidgets[$i]['attr']['order'];
            $aTmp[$i]['title']=$aWidgets[$i]['attr']['title'];
            $aTmp[$i]['domid']=$aWidgets[$i]['attr']['domid'];
            $aTmp[$i]['classname']=$aWidgets[$i]['attr']['classname'];
            $aTmp[$i]['tpl']=$aWidgets[$i]['attr']['tpl'];
            $aTmp[$i]['app']=($aWidgets[$i]['attr']['app']) ? $aWidgets[$i]['attr']['app'] : ((empty($aWidgets[$i]['attr']['theme'])) ? 'b2c' : '');
            $aTmp[$i]['theme']=(empty($aWidgets[$i]['attr']['theme']))?'':$theme;
            $aTmp[$i]['platform']= $aWidgets[$i]['attr']['platform'];

            $params = unserialize(htmlspecialchars_decode($aWidgets[$i]['value']));
            $aTmp[$i]['params']= $params;
        }
        $aWidgets=$aTmp;


        //确定修改theme的同时，不修改theme表中的is_used字段的值。
        $theme_objs = app::get('site')->model('themes')->dump(array('theme'=>$aTheme['theme']));
        if($theme_objs){
            $aTheme['is_used'] = $theme_objs['is_used'];
        }

        $this->flush_theme($theme); //flush数据

        $aNumber= kernel::single('site_theme_widget')->count_widgets_by_theme($theme);
        $nNumber=intval($aNumber);
        $iNumber=intval($iNumber);
        $insertWidgets=false;
        // $insertInstances=false;

        if($replaceWg){
            if($nNumber){
                kernel::single('site_theme_widget')->delete_widgets_by_theme($theme);
            }
            $insertWidgets=true;
            // $insertInstances=true;
        }else{
            if($nNumber==0){
                $insertWidgets=true;
            }
            if($iNumber==0){
                // $insertInstances=true;
            }
        }
        if($insertWidgets && count($aWidgets)>0){
            foreach($aWidgets as $k=>$wg){
                kernel::single('site_theme_widget')->insert_widgets($wg);
            }
        }


        kernel::single('site_theme_tmpl')->install(trim($config['theme']['platform']['value']), $theme);

        if(!kernel::single('site_theme_base')->update_theme($aTheme)){
            return false;
        }else{
            // 初始化时设置默认模板
            if(!kernel::single('site_theme_base')->get_default(trim($config['theme']['platform']['value']))){
                kernel::single('site_theme_base')->set_default(trim($config['theme']['platform']['value']), $theme);
            }
            kernel::single('site_theme_base')->update_theme_widgets(trim($config['theme']['platform']['value']), $theme);
            return $aTheme;
        }
    }

    private function __remove_db_theme($theme) {
        $filter = array('theme'=>$theme);
        return app::get('site')->model('themes_file')->delete($filter);
    }

    private function __remove_dir_theme($sDir) {
        if($rHandle=opendir($sDir)){
            while(false!==($sItem=readdir($rHandle))){
                if ($sItem!='.' && $sItem!='..'){
                    if(is_dir($sDir.'/'.$sItem)){
                        $this->__remove_dir_theme($sDir.'/'.$sItem);
                    }else{
                        if(!unlink($sDir.'/'.$sItem)){
                            trigger_error(app::get('site')->_('因权限原因，模板文件').$sDir.'/'.$sItem.app::get('site')->_('无法删除'),E_USER_NOTICE);
                        }
                    }
                }
            }
            closedir($rHandle);
            rmdir($sDir);
            return true;
        }else{
            return false;
        }
    }

    private function __build_dir($sDir){
        if(file_exists($sDir)){
            $aTmp=explode('/',$sDir);
            $sTmp=end($aTmp);
            if(strpos($sTmp,'(')){
                $i=substr($sTmp,strpos($sTmp,'(')+1,-1);
                $i++;
                $sDir=str_replace('('.($i-1).')','('.$i.')',$sDir);
            }else{
                $sDir.='(1)';
            }
            return $this->__build_dir($sDir);
        }else{
            if(!is_dir($sDir)){
                mkdir($sDir,0755,true);
            }
            return $sDir.'/';
        }
    }

    private function __change_xml_array($aArray){
        $aData = array();
        if(isset($aArray['attr'])){
            $aArray = array('0'=>$aArray);
        }
        if(is_array($aArray)){
            foreach($aArray as $i=>$v){
                unset($v['attr']);
                $aData[$i]=array_merge($v,$aArray[$i]['attr']);
            }
        }
        return $aData;
    }

    private function file_rename($source,$dest){
        if(is_file($dest)){
            if(PHP_OS=='WINNT'){
                @copy($source,$dest);
                @unlink($source);
                if(file_exists($dest)) return true;
                else return false;
            }else{
                return @rename($source,$dest);
            }
        }else{
            return false;
        }
    }

    public function ini_get_size($sName){
        $sSize = ini_get($sName);
        $sUnit = substr($sSize, -1);
        $iSize = (int) substr($sSize, 0, -1);
        switch (strtoupper($sUnit)){
            case 'Y' : $iSize *= 1024; // Yotta
            case 'Z' : $iSize *= 1024; // Zetta
            case 'E' : $iSize *= 1024; // Exa
            case 'P' : $iSize *= 1024; // Peta
            case 'T' : $iSize *= 1024; // Tera
            case 'G' : $iSize *= 1024; // Giga
            case 'M' : $iSize *= 1024; // Mega
            case 'K' : $iSize *= 1024; // kilo
                       break;
            default: $iSize = 5 * 1024 * 1024; //todo Default 2M
        };
        return $iSize;
    }

    public function get_file_ext($file_name){
        $ftype = array(
            'html' => app::get('site')->_('模板文件'),
            'gif'  => app::get('site')->_('图片文件'),
            'jpg'  => app::get('site')->_('图片文件'),
            'jpeg' => app::get('site')->_('图片文件'),
            'png'  => app::get('site')->_('图片文件'),
            'bmp'  => app::get('site')->_('图片文件'),
            'css'  => app::get('site')->_('样式表文件'),
            'js'   => app::get('site')->_('脚本文件'),
            'xml'  => app::get('site')->_('theme.xml'),
            'php'  => app::get('site')->_('模板挂件'),
        );
        if(strrpos($file_name,'.')===false) return false;
        $fext = strtolower(substr($file_name,strrpos($file_name,'.')+1));
        if(!$ftype[$fext])  return false;
        return array('ext'=>$fext,'memo'=>$ftype[$fext]);
    }

    public function initthemes(){
        if ($dh = opendir(THEME_DIR)){
            while (($file = readdir($dh)) !== false){
                if(substr($file,-4,4)=='.tgz'){
                    //$filename_arr[] = $file;
                    $theme_file['tmp_name'] = THEME_DIR.'/'.$file;
                    $theme_file['name'] = $file;
                    $theme_file['type'] = 'application/octet-stream';
                    $theme_file['error'] = '0';
                    $theme_file['size'] = filesize(THEME_DIR.'/'.$file);
                    $res = $this->install($theme_file,$msg);
                }
            }
            closedir($dh);
        }
    }
}//End Class
