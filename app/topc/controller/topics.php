<?php
class topc_ctl_topics extends topc_controller{

    public function __construct($app)
    {
        parent::__construct();
        $this->setLayoutFlag('topics');
    }

    function index($catId)
    {
        $catId = intval($catId);
        $data = app::get('topc')->rpcCall('category.cat.get.info',array('cat_id'=>$catId,'fields'=>'cat_name,cat_template'));
        $this->setLayout($data[$catId]['cat_template']);
        return $this->page('topc/topics.html');
    }
}
