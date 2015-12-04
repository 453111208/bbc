<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class system_prism_queue_script
{
    //这里是入口，如果需要调用的话，走这里
    //这里会判断是否需要进行初始化和更新
    //
    //如果符合条件：prismMode开启，并且队列采用prism提供的，
    //会调用updatePrismQueue方法
    public function update()
    {
        //只有打开prism模式，且队列使用prism的adapter，才会进行更新
        if(    config::get('prism.prismMode', false)
            && config::get('queue.default') == 'system_prism_queue_adapter' )
        {
            logger::info('Queues info is updated to prism, ready. ');
            $this->updatePrismQueue();
            logger::info('Queues info is updated to prism, ok. ');
        }
        return true;
    }

    //初始化和变更可以作为一体来思维，
    //因为key是已经生成好的，所以把初始化当成变更就好了，只不过变更比较大而已
    //
    //流程是这样的：
    //1、从配置文件获取新的队列情况，并格式化成以app为逻辑结构的模式
    //2、循环上面那个以app为逻辑结构的数组，取对应app的已经在prism上的队列
    //3、对比队列，得出哪个要删哪个要增加
    //3、补充 后来觉得做diff麻烦，所以不做diff了，反正这里没有性能问题
    //
    //4、根据上一步得出的差异表进行增减操作
    public function updatePrismQueue()
    {
        $queue = (array)config::get('queue.queues', array());
        $newQueues = $this->formatQueue($queue);
        foreach($newQueues as $appId => $newQueue)
        {
            //不高兴做diff了，所以逻辑变成：
            //从prism上拉下来队列列表，和现有列表对比，如果有需要删除的，再去删除
            //让后把所有会用的新增一次

            $oldQueue = $this->getQueueList($appId);
            foreach($oldQueue as $queueTodelete)
            {
                if( !in_array($queueTodelete, $newQueue) )
                {
                    $this->removeQueue($appId, $queueTodelete);
                }
            }

            foreach($newQueue as $queueToAdd)
            {
                $this->addQueue($appId, $queueToAdd);
            }

        }
        return true;
    }

    //把队列的数组进行
    //就是以app分类队列
    public function formatQueue($queue)
    {
        $formatQueue = array();
        foreach($queue as $queueName=>$queueConfig)
        {
            $app = $queueConfig['app'];
            $formatQueue[$app][] = $queueName;
        }

        return $formatQueue;
    }

    //这里获取prism上面这个app对应的队列，可以拿来做对比。
    public function getQueueList($appId)
    {
        logger::info('Get prism queue list: ' . $appId);
        return kernel::single('system_prism_init_queue')->queueList($appId);
    }

    //添加一个队列
    public function addQueue($appId, $queueName)
    {
        logger::info('Add prism queue: "'. $queueName . '" into "' . $appId . '".');
        return kernel::single('system_prism_init_queue')->queueCreate($appId, $queueName);
    }

    //删除一个队列
    public function removeQueue($appId, $queueName)
    {
        logger::info('Remove prism queue: "'. $queueName . '" from "' . $appId . '".');
        $queueInfo = kernel::single('system_prism_init_queue')->queueStaus($appId, $queueName);
        if($queueInfo['Backing_queue_status']['Len'] > 0)
        {
            logger::info('Some data is in queue, queue "'.$queueName.'" won\'t be removed.');
            return false;
        }
        return kernel::single('system_prism_init_queue')->queueDrop($appId, $queueName);
    }

}
