<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class system_command_runqueue extends base_shell_prototype{

    public $command_run = "启动执行队列的swoole server";
    public $command_run_options = array(
        'start' => array('title'=>'启动swoole_server'),
        'restart' => array('title' => '重启swoole_server'),
        'reload' => array('title' => '热启动swoole_server'),
        'shutdown' => array('title' => '结束swoole_server'),
        'client' => array('title' => '链接swooler server'),
    );
    public function command_run()
    {
        $args = func_get_args();
        $cmd = $args[0];
        if($args[1])
        {
            $queuelist = config::get('swoolequeue.type');
            if($queuelist[$args[1]])
            {
                $queue = $args[1];
            }
            else
            {
                echo "swoolequeue中不存在该值：$args[1] \n";
                $list = "现有的值有：\n   ";
                foreach($queuelist as $key=>$value)
                {
                    $list .= $value['title']."  “".$key."”\n   ";
                }
                echo $list;
                exit;
            }
        }
        return kernel::single('system_runswoole')->run($cmd,$queue);
    }
}
