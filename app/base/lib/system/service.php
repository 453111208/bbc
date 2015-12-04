<?php

class base_system_service{

    function check_writeable($list,$show_name){
        foreach($list as $key=>$value){
            foreach($value as $val){
                $result = 'true';
                $dir_name_url = ROOT_DIR . '/' . $val['name'];
                $file = $dir_name_url.'/test.html';
                $dir_eable = $val['value'];
                if($fp = @fopen($file, 'w')){
                    @fclose($fp);
                    @unlink($file);                   
                }else{
                    $result = 'false';
                }
                //$get_dir_eable = substr(sprintf('%o',fileperms($dir_name_url)),-3);

                $check_result[] = array(
                    'value'=>sprintf('%s目录需要有写入权限',$val['name'],$show_name),
                    'result'=>$result,
                );
                
            }
        }
        return $check_result;
    }

    function check_version($list,$show_name){
        foreach($list as $key=>$value){
            if(isset($value['exec_function']) && $value['exec_function']){
                $check_result[] = $this->_pubaction($value['exec_function'],$value,$show_name);
            }
        }
        return $check_result;
    }

    function check_php_configure($list,$show_name){
        foreach($list as $key=>$value){
            foreach($value as $val){
               //$config_value = get_cfg_var($val['name']); 
               $config_value = ini_get($val['name']); 
               $check_result[]=array(
                    'value'=>sprintf('%s%s是%s,应是%s',$val['name'],$show_name,$config_value,$val['value']),
                    'result'=>'true',
                   );
            }
        }
        return $check_result;
    }

    function check_extension_library($list,$show_name){
        foreach($list['list'] as $key=>$value){
            if(isset($value['exec_function']) && $value['exec_function']){
                $check_result[] = $this->_pubaction($value['exec_function'],$value,$show_name);
            }else{

                $result = extension_loaded($value['name']);
                $check_result[]=array(
                    'value'=>sprintf('%s%s,%s',$value['name'],$show_name,($result)?'OK':'未安装'),
                    'result'=>($result)?'true':'false',
                );

            }
        }
        return $check_result;
    }

    function check_common_function($list,$show_name){

        $disabled_functions = ini_get('disable_functions');                                                                                                                                          
        if($disabled_functions !=''){
            $disabledarr=explode(',',$disabled_functions);
        }
        foreach($list['list'] as $key=>$value){
            if(in_array($value['name'],$disabledarr)){
                $check_result ['ifopen'][]=array(
                    'value'=>sprintf('%s%s%s',$show_name,$value['name'],'未开启,请检测php.ini '),
                    'result'=>'false',
                );
            }else{
                $check_result['ifopen'][]=array(
                    'value'=>sprintf('%s%s%s',$show_name,$value['name'],'已启用'),
                    'result'=>'false',
                );
                if(isset($value['exec_function']) && $value['exec_function']){
                    $check_result['ifavailable'][] = $this->_pubaction($value['exec_function'],$value,$show_name);
                }
            }
        }
        return $check_result;
    }

    function check_other($list,$show_name){
        foreach($list['list'] as $key=>$value){
            if(isset($value['exec_function']) && $value['exec_function']){
                $check_result[] = $this->_pubaction($value['exec_function'],$value,$show_name);
            }
        }
        return $check_result;
    }

    function _pubaction($exec_function,$value,$show_name){
        list($class,$function) = explode('@',$exec_function);
        $object = kernel::single($class);
        if(is_object($object) && method_exists($object,$function)){
            $check_result = $object->$function($value,$show_name);
        }
        return $check_result;
    }
}
