<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysspfb_ctl_admin_brand extends desktop_controller {

    public $workground = 'sysspfb.workground.category';

    /**
     * 品牌管理列表
     */
    public function index()
    {
        return $this->finder(
            'sysitem_mdl_item',
            array(
                'title'=>app::get('sysspfb')->_('商品发布列表'),
                'actions'=>array(
                    array(
                        'label'=>app::get('sysspfb')->_('审核'),
                         'icon' => 'download.gif',
                        'submit' => '?app=sysspfb&ctl=admin_brand&act=approve',
                        'confirm' => app::get('sysspfb')->_('是否开始审核'),
                         'target'=>'dialog::{title:\''.app::get('sysspfb')->_('审核').'\',width:800,height:600}'
                      ),
                )
            )
        );
    }
   /**
     * 审核页面
     *
     * @param int $brandId 商品ID
     */
   public function approve()
   {
     $this->begin('?app=sysspfb&ctl=admin_brand&cat=index');
        $postdata = $_POST;
        $ojbMdlItem = app::get('sysitem')->model('item');
        $itemRow =  $ojbMdlItem ->getRow("*",array('item_id'=>$postdata['item_id']));
        $pagedata['itemRow'] = $itemRow;
         return view::make('sysspfb/admin/brand.html', $pagedata);
   }

      public function doApprove()
   {
        $this->begin();
        $data = $_POST;
           if( !empty($data['item_id']) )
        {
            $itemId = $data['item_id'];
            $time = time();
            $sql = "UPDATE sysspfb_item set state=1,modified_time=".$time." where item_id=".$itemId."";
            app::get('sysspfb')->database()->executeUpdate($sql);
            $a=1;

        }
        $this->end();
   }
    /**
     * 添加品牌页面
     *
     * @param int $brandId 品牌ID
     */
    public function create($brandId)
    {
        if( $brandId )
        {
            $brandInfo = app::get('sysspfb')->model('brand')->getBrandRow($brandId);
            $pagedata['brandInfo'] = $brandInfo;
        }

        return view::make('sysspfb/admin/brand.html', $pagedata);
    }

    public function saveBrand()
    {
        $this->begin();
        $data = $_POST;
        if( !empty($data['brand_id']) )
        {
            $data['brand_id'] = intval($data['brand_id']);
            $flag = app::get('sysspfb')->rpcCall('category.brand.update',$data);
            $msg = $flag ? app::get('sysspfb')->_('添加品牌成功') :app::get('sysspfb')->_('添加品牌失败');
            $this->adminlog("添加品牌[{$data['brand_name']}]", $flag ? 1 : 0);
        }
        else
        {
            $flag = app::get('sysspfb')->rpcCall('category.brand.add',$data);
            $msg = $flag ? app::get('sysspfb')->_('保存品牌成功') :app::get('sysspfb')->_('保存品牌失败');
            $this->adminlog("编辑品牌[{$data['brand_name']}]", $flag ? 1 : 0);
        }

        $this->end($msg);
    }

    public function relcat($brandId)
    {
        if( !$brandId )
        {
            return false;
        }

        $brandInfo = app::get('sysspfb')->model('brand')->getBrandRow($brandId);
        $pagedata['brandInfo'] = $brandInfo;
        return view::make('sysspfb/admin/brand.html', $pagedata);
    }
    //获取所有的类目
    public function brandRelCat($brandId)
    {
        $catRelBrandMdl = app::get('sysspfb')->model('cat_rel_brand');
        $catList = $catRelBrandMdl->getList('cat_id,brand_id',array('brand_id'=>$brandId));
        foreach ($catList as $key => $value) 
        {
            $catIds[$value['cat_id']] = $value['cat_id'];
        }

        $params = 'cat_id,cat_name';
        $catList = app::get('sysspfb')->rpcCall('category.cat.get.list',$params);
        foreach ($catList as $key => $value)
        {
            foreach ($value['lv2'] as $ke => $lv2)
            {
                foreach ($lv2['lv3'] as $k => $lv3) 
                {
                    if($catIds[$k])
                    {
                        $catList[$key]['lv2'][$ke]['lv3'][$k]['ck'] = 'checked';
                    }
                    else
                    {
                        $catList[$key]['lv2'][$ke]['lv3'][$k]['ck'] = 'mochecked';
                    }
                }
            }
        }
        $pagedata['catList'] = json_encode($catList,true);
        $pagedata['brandId'] = $brandId;
        return view::make('sysspfb/admin/brandRelCat.html', $pagedata);
    }
    //保存品牌关联类目
    public function saveBrandRelCat()
    {
        $pageData = input::get();
        $catRelBrandMdl = app::get('sysspfb')->model('cat_rel_brand');

        $db = app::get('sysspfb')->database();
        $transaction_status = $db->beginTransaction();
        try
        {
            $result = $catRelBrandMdl->delete(array('brand_id'=>$pageData['brandId']));

            foreach ($pageData['lv3'] as $key =>$catId)
            {
                $param = array('cat_id'=>$catId,'brand_id'=>$pageData['brandId']);

                $catRelBrandMdl->save($param);
            }
            $this->adminlog("编辑品牌关联类目[ID:{$pageData['brandId']}]", 1);
            $db->commit($transaction_status);
        }
        catch (Exception $e)
        {
            $this->adminlog("编辑品牌关联类目[ID:{$pageData['brandId']}]", 0);
            $db->rollback();
            throw $e;
        }
        $msg = app::get('sysspfb')->_('保存成功');
        return $this->splash('success',null,$msg);
    }
    //根据父级id获取子级类目id(给劲歌用的)
    /*public function getCatInfoByParentId()
    {
        $catId = $_POST['catId'];
        $brandId = $_POST['brandId'];
        $catRelBrandMdl = app::get('sysspfb')->model('cat_rel_brand');
        $catList = $catRelBrandMdl->getList('cat_id,brand_id',array('brand_id'=>$brandId));
        foreach ($catList as $key => $value) 
        {
            $catIds[$value['cat_id']] = $value['cat_id'];
        }

        $params = array('parent_id'=>$catId,'fields'=>'cat_id,cat_name,parent_id');
        $catInfo = app::get('sysspfb')->rpcCall('category.cat.get.info',$params);
        //判断是否被选择
        foreach ($catInfo as $key => $value)
        {
            if($catIds[$key]==$key)
            {
                $catInfo[$key]['ck']='checked';
            }
            else
            {
                $catInfo[$key]['ck']='nochecked';
            }
        }

        //echo '<pre>';print_r($catInfo);exit();
        $catList = json_encode($catInfo,true);
        return $catList;
    }*/

}
