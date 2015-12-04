<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class base_command_task extends base_shell_prototype{

    var $command_list = '列出所有计划任务';
    function command_list(){

        $task_type = array('week','minute','hour','day','month');

        foreach(kernel::servicelist('autotask') as $k=>$o){
            foreach($task_type as $type){
                if(method_exists($o,$type)){
                    $task[$type][] = $k;
                }
            }
        }

        $list = app::get('base')->model('task')->getlist('*');

        foreach($list as $k=>$v){
            $this->output_line($k);
            foreach($v as $c){
                echo "\t".$c."\n";
            }
        }


    }

    var $command_exec = '按计划执行任务';
    function command_exec(){
        //kernel::single('base_misc_autotask')->trigger();
        base_crontab_schedule::trigger_all();
    }

}
