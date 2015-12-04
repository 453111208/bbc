<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class system_mdl_queue_mysql extends dbeav_model
{

    function __construct($app)
    {
        parent::__construct($app);
        $db = app::get('system')->database();
        $db->exec('set SESSION autocommit=1;');
        $db->exec('set @msgID = -1;');
    }
    
    /**
     * 获取一个队列任务信息
     *
     * @param string $queue_name 队列名称
     *
     * @return array $row
     */
    public function get($queue_name)
    {
        if (app::get('system')->database()->executeUpdate('UPDATE system_queue_mysql force index(PRIMARY) SET owner_thread_id=GREATEST(CONNECTION_ID() ,(@msgID:=id)*0),last_cosume_time=? WHERE queue_name=? and owner_thread_id=-1 order by id LIMIT 1;', [time(), $queue_name]))
        {
            $row = app::get('system')->database()->executeQuery('select id, worker, params from system_queue_mysql where id=@msgID')->fetch();
            return $row;
        }
        
        return false;
    }

    /**
     * 清空一个队列的内容
     * 
     * @param string $queue_name
     * 
     * @return bool
     */
    public function purge($queue_name){
        return $this->delete(array('queue_name' => $queue_name));
    }

    public function is_end($queue_name){
        if (!$this->getRow('id', array('queue_name' => $queue_name, 'owner_thread_id' => -1))){
            return true;
        } else {
            return false;
        }
    }
}


