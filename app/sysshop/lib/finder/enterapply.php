<?php
class sysshop_finder_enterapply{

    public $column_edit = '操作';
    public $column_edit_order = 2;
    public $column_edit_width = 200;

    /**
     * @brief 编辑链接
     *
     * @param $row
     *
     * @return page
     */

    public function column_edit(&$colList, $list) {
        foreach($list as $k=>$row)
        {
            $colList[$k] = $this->_column_edit($row);
        }
    }

    public function _column_edit($row)
    {
        if($row['status'] == 'successful')
        {
            $url = '?app=sysshop&ctl=admin_enterapply&act=openShop&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['enterapply_id'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('审核入驻资料').'\', width:800, height:800}';
            $title = app::get('sysshop')->_('开通店铺');

            return '<a href="' . $url . '">' . $title . '</a>';
        }
        elseif($row['status'] == 'active')
        {
            $objMdlEnterapply = app::get('sysshop')->model('enterapply');
            $list =  $objMdlEnterapply->getRow('shop_type,new_brand,shop',array('enterapply_id'=>$row['enterapply_id']));
            $shop = unserialize($list['shop']);

            $url = '?app=sysshop&ctl=admin_enterapply&act=doExamine&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['enterapply_id'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('审核入驻资料').'\', width:800, height:500}';
            $title = app::get('sysshop')->_('审核');
            $result = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            if($list['new_brand'] && !$shop['shop_brand'] && $list['shop_type'] !="cat")
            {
                $url = '?app=sysshop&ctl=admin_enterapply&act=doRelevance&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['enterapply_id'];
                $target = 'dialog::  {title:\''.app::get('sysshop')->_('帮助商家关联商家新增的品牌').'\', width:350, height:200}';
                $title = app::get('sysshop')->_('关联品牌');
                $result .=  ' | <a href="' . $url . '" target="' . $target . '">' . $title . '</a> ';
            }
            return $result;
        }
    }
}


