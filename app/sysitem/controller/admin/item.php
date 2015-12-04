<?php

/**
 * @brief 商品列表
 */
class sysitem_ctl_admin_item extends desktop_controller{

    public $workground = 'sysitem.workground.item';

    /**
     * @brief 列表
     *
     * @return
     */
    public function index()
    {
        $actions = array(
            array(
                'label'=>app::get('sysitem')->_('下架商品22'),
                'icon' => 'download.gif',
                'submit' => '?app=sysitem&ctl=admin_item&act=disable',
                'confirm' => app::get('sysitem')->_('确定要下架选中商品？'),
            ),
            array(
                'label'=>app::get('sysitem')->_('删除'),
                'icon' => 'download.gif',
                'submit' => '?app=sysitem&ctl=admin_item&act=doDelete',
                'confirm' => app::get('sysitem')->_('确定要删除选中商品？'),
            ),
        );
        return $this->finder('sysitem_mdl_item',array(
            'use_buildin_set_tag' => true,
            'use_buildin_tagedit' => true,
            'use_buildin_filter'=> true,
            'use_buildin_refresh' => true,
            'use_buildin_delete' => false,
            //'allow_detail_popup' => true,
            'title' => app::get('sysitem')->_('商品列表'),
            'actions' => $actions,
        ));

    }
    public function doDelete()
    {
        $this->begin('?app=sysitem&ctl=admin_item&cat=index');
        $postdata = $_POST;
        $ojbMdlItem = app::get('sysitem')->model('item');
        $result = $ojbMdlItem->delete($postdata);
        $this->adminlog("删除商品", $result ? 1 : 0);
        $this->end($result,$msg);
    }


    /**
        * @brief 下架违规商品
        *
        * @return
     */
    public function disable()
    {
        $this->begin('?app=sysitem&ctl=admin_item&cat=index');
        $postdata = $_POST;
        $ojbItem = kernel::single('sysitem_data_item');
        $result = $ojbItem->batchCloseItem($postdata,'instock',$msg);
        $this->adminlog("下架商品", $result ? 1 : 0);
        $this->end($result,$msg);
    }


    public function _views()
    {
        $subMenu = array(
            0=>array(
                'label'=>app::get('sysitem')->_('全部'),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('sysitem')->_('已上架'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'onsale',
                ),
            ),
            2=>array(
                'label'=>app::get('sysitem')->_('已下架'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'instock',
                ),
            ),
            3=>array(
                'label'=>app::get('sysitem')->_('自营商品'),
                'optional'=>false,
                'filter'=>array(
                    'is_selfshop'=>1,
                ),
            ),
        );
        return $subMenu;
    }
}


