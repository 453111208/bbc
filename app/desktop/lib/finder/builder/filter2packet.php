<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_finder_builder_filter2packet extends desktop_finder_builder_prototype{
    function main(){
        $pagedata['app'] = $_GET['app'];
        $pagedata['act'] = $_GET['act'];
        $pagedata['ctl'] = $_GET['ctl'];
        $pagedata['model'] = $this->object_name;
        
        $filterquery = $_POST['filterquery'];
        $tabs = $this->getViews();
        if($tabs&&$_GET['view']){
            $filterquery = $filterquery.'&'.http_build_query($tabs[$_GET['view']]['filter']);
        }
        $pagedata['filterquery'] = $filterquery;
        return view::make('desktop/finder/view/filter2packet.html', $pagedata)->render();
    }
}
