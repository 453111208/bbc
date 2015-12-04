<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
/*
 * @package site
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license
 */
class site_ctl_admin_errorpage extends site_admin_controller
{

     function index(){
        $arr_page_list = kernel::single('site_errorpage_list')->getList();
        $pagedata['list'] = $arr_page_list;
        return $this->page('site/admin/errorpage/index.html', $pagedata);

        #case 'searchempty':
        #    $pagedata['pagename'] = __('搜索为空时显示内容');
        #    $pagedata['code'] = 'searchempty';
        #    $pagedata['errorpage'] = app::get('b2c')->getConf('errorpage.searchempty');
        #    $templete='searchempty.html';
    }

    public function edit() {
        $key = $_GET['key'];
        if( $key ) {
            $errorpage = kernel::single('site_errorpage_get')->getConf($key);
            $info = $pagedata['info'] = kernel::single('site_errorpage_list')->getList($key);
            
            if( !$errorpage )
            {
                $errorpage = $info['errormsg'];
            }
            $pagedata['errorpage'] = $errorpage;

            return $this->singlepage('site/admin/errorpage/edit.html', $pagedata);
        } else {
            $this->begin();
            $this->end( false,'key值错误 ！' );
        }
    }

    private function get_index_url() {
        return url::route('shopadmin', array('app'=>'site','ctl'=>'admin_errorpage','act'=>'index'));
    }

    function save(){
        $this->begin();
        $this->app->setConf( $_POST['key'],$_POST['errorpage'] );
        $this->adminlog("编辑报错页面", 1);
        $this->end(true,app::get('site')->_("保存成功"));
    }



}//End Class
