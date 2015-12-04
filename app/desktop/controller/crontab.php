<?php

class desktop_ctl_crontab extends desktop_controller {

    var $workground = 'desktop_ctl_system';
    function index() {
        $params = array (
            'title' => app::get('desktop')->_('计划任务管理'),
            'use_buildin_refresh' => true,
            'actions' => array(

            ),
	    );
        return $this->finder('base_mdl_task', $params);
    }

    function edit($task_id) {
        $model = app::get('base')->model('task');
        $task = $model->dump($task_id);

        $pagedata['task'] = $task;
        return $this->page('desktop/crontab/detail.html', $pagedata);
    }

    function save() {
	$this->begin('?app=desktop&ctl=crontab&act=index');
	$model = app::get('base')->model('task');
	if( $model->save($_POST) ) {
	    $this->end(true, '保存成功');
	} else {
	    $this->end(false, '保存失败');
	}
    }

    function exec($task_id) {
	$this->begin('?app=desktop&ctl=crontab&act=index');
	$model = app::get('base')->model('task');
	$task = $model->dump($task_id);
	if(!$task) {
	    $this->end(false, '执行失败');
	}
	$task = new $task['task'];
	$task->exec();
	$this->end(true, '执行成功');
    }

}
