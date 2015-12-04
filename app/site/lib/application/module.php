<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_application_module extends base_application_prototype_xml
{
    var $xml='site.xml';
    var $xsd='site_content';
    var $path = 'module';

    public function current(){
        $this->current = $this->iterator()->current();
        return $this;
    }

    final public function install($update=false)
    {
        if(is_array($this->current['allow_seo'])){
            foreach($this->current['allow_seo'] AS $param){
                $this->insert_seo($this->parse_seo_params($param), $update);
            }
        }
    }//End Function

    final public function update($app_id)
    {
        $this->update_modified = time();
        foreach($this->detect($app_id) as $name=>$item){
            $item->install(true);
        }
        $this->post_update($app_id);
        return true;
    }//End Function

    final public function post_update($app_id)
    {
       $rows = app::get('site')->model('seo')->getList('id', array('app'=>$app_id, 'update_modified|noequal'=>$this->update_modified));
        foreach($rows AS $row){
            app::get('site')->model('seo')->delete(array('id'=>$row['id']));
        }
    }//End Function

   private function insert_seo($data, $update)
    {
        $data['update_modified'] = $this->update_modified;
        if($update == true){
            $rows = app::get('site')->model('seo')->getList('id', array('app'=>$data['app'], 'ctl'=>$data['ctl'], 'act'=>$data['act']));
            if($rows[0]['id'] > 0){
		unset($data['param']);
                return app::get('site')->model('seo')->update($data, array('id'=>$rows[0]['id']));
            }
        }
        return app::get('site')->model('seo')->insert($data);
    }//End Function

    private function parse_seo_params($param){
        $data['title'] = $param['title'];
        $data['app'] = $this->target_app->app_id;
        $data['route'] = $param['route'];
        $data['seo_key'] = ($param['seo_key']) ? $param['seo_key'] : '';
        if(is_array($param['config'])){
            foreach($param['config'] AS $key=>$val){
                $data['config'][$key]['id'] = $val['id'];
                $data['config'][$key]['value'] = $val['value'];
            }
        }
        $data['param'] = $param['param'][0];
        $data['hidden'] = ($param['hidden'] === 'true') ? 'true' : 'false';
        return $data;
    }

    function clear_by_app($app_id){
        if(!$app_id){
            return false;
        }
        app::get('site')->model('seo')->delete(array( 'app'=>$app_id));
    }
}//End Class
