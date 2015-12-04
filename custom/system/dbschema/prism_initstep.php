<?php
return array(

    'columns'=>array(
        'step_id'=>array(
            'type'=>'number',
            'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('system')->_('执行任务的步骤'),
            'label' => app::get('system')->_('执行任务的步骤'),
            'in_list'=>true,
            'default_in_list'=>true,
        ),

        'state'=>array(
            'type'=>array(
                'ready' => '准备执行中',
                'complete' => '执行完毕',
            ),
            'default' => 'ready',
            'comment' => app::get('system')->_('当前任务是否已经执行过'),
            'label' => app::get('system')->_('当前任务是否已经执行过'),
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'handlar'=>array(
            'type'=>'string',
            'length' => 50,
            'comment' => app::get('system')->_('执行任务的类和方法'),
            'label' => app::get('system')->_('执行任务的类和方法'),
            'in_list' => true,
            'default_in_list' => true
        ),
        'params' => array (
            'type' => 'serialize',
            'default' => null,
            'comment' => app::get('system')->_('请求参数'),
            'label' => app::get('system')->_('请求参数'),
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'result' => array(
            'type' => 'serialize',
            'default' => null,
            'comment' => app::get('system')->_('返回参数'),
            'label' => app::get('system')->_('返回参数'),
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'create_time' => array (
            'type' => 'time',
            'default' => 0,
            'comment' => app::get('system')->_('进入队列的时间'),
            'label' => app::get('system')->_('进入队列的时间'),
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'start_time' => array(
            'type' => 'time',
            'default' => 0,
            'comment' => app::get('system')->_('任务开始执行时间'),
            'label' => app::get('system')->_('任务开始执行时间'),
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'complete_time' => array(
            'type' => 'time',
            'default' => 0,
            'comment' => app::get('system')->_('任务执行结束时间'),
            'label' => app::get('system')->_('任务执行结束时间'),
            'in_list'=>true,
            'default_in_list'=>true,
        ),
    ),
    'primary' => ['step_id'],
);
