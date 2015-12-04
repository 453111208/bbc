<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class base_application_service extends base_application_prototype_xml{

    var $xml='services.xml';
    var $xsd='base_app';
    var $path='service';

    function set_current($current){
        $this->current = $current;
    }

    public function current() {
        $this->current = $this->iterator()->current();
        $this->key = $this->current['id'];
        return $this;
    }

    public function install(){
        logger::info('Installing '.$this->content_typename().' '.$this->key());
        
        $data = $this->row();
        $data['content_type'] = 'service_category';
        if($this->current['optname']){
            $data['content_title'] = $this->current['optname'];
        }
        if($this->current['opttype']){
            $data['content_path'] = $this->current['opttype'];
        }
		$obj_app_content = app::get('base')->model('app_content');
        $obj_app_content->insert($data);
        
		$index = 0;
		$time = time();
		$service_define = array();
        foreach((array)$this->current['class'] as $class){
            $row = $this->row();
            $row['content_path'] = $class['value'];
			if ($class['orderby'])
				$row['ordernum'] = $class['orderby'];
			else
				$row['ordernum'] = 50;
			$row['input_time'] = $time+$index;
            $obj_app_content->insert($row);
            //$service_define['list'][$class['value']] = $class['value'];
            //todo: interface... check
			$index++;
        }

		$arr_servicelist = $obj_app_content->getList('content_path',array('content_name'=>$this->key,'content_type'=>'service'), 0, -1, 'ordernum ASC, input_time DESC');
		foreach ((array)$arr_servicelist as $arr){
			$service_define['list'][$arr['content_path']] = $arr['content_path'];
		}
        base_kvstore::instance('service')->store($this->key,$service_define);
        
        //更新service资源最后变更时间
        syscache::instance('service')->set_last_modify();
    }
    
    function clear_by_app($app_id){
        if(!$app_id){
            return false;
        }

        $to_remove = array();
        $service_list = app::get('base')->model('app_content')->getlist('content_name,content_path,app_id', array('app_id'=>$app_id, 'content_type'=>'service'));
        foreach($service_list as $service){
            $to_remove[$service['content_name']][] = $service['content_path'];
        }
        foreach($to_remove as $service_name=>$rows){
            if(base_kvstore::instance('service')->fetch($service_name,$service_define)){
                foreach($rows as $row){
                    unset($service_define['list'][$row]);
                }
                base_kvstore::instance('service')->store($service_name,$service_define);
            }
        }
        
        
        app::get('base')->model('app_content')->delete(array(
            'app_id'=>$app_id,'content_type'=>'service'));
            
        app::get('base')->model('app_content')->delete(array(
            'app_id'=>$app_id,'content_type'=>'service_category'));

        //更新service资源最后变更时间
        syscache::instance('service')->set_last_modify();
    }

    private function check_depends($app_id, &$queue){
        $depends_app = app::get($app_id)->define('depends/app');
        foreach((array)$depends_app as $depend_app_id){
            $this->check_depends($depend_app_id['value'], $queue);
        }
        $queue[$app_id] = $app_id;
    }//End Function

    private function check_service_level($app_id) 
    {
        if($app_id == 'base')   return 0;
        $queue = array();
        $this->check_depends($app_id, $queue);
        $queue = array_keys($queue);
        $apps = array_flip($queue);
        return $apps[$app_id];
    }//End Function

}
 
