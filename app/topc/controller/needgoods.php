<?php  

class topc_ctl_needgoods extends topc_controller{

	   public function __construct(&$app)
    {
        parent::__construct();
        kernel::single('base_session')->start();
        if(!$this->action) $this->action = 'index';
        $this->action_view = $this->action.".html";
        // 检测是否登录
        if( !userAuth::check() )
        {
            redirect::action('topc_ctl_passport@signin')->send();exit;
        }
        $this->limit = 20;

        $this->passport = kernel::single('topc_passport');
    }

	public function index(){
		 $pagedata['action'] = 'topc_ctl_needgoods@index';
		 $this->action_view = "needgoods.html";
		 return $this->output($pagedata);
	}
}