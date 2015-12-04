<?php
/**
 * @brief 第三方文章
 */
class sysshop_ctl_admin_service extends desktop_controller {

    /**
     * @brief  第三方服务列表
     *
     * @return
     */
    public function index()
    {
        return $this->finder('sysshop_mdl_service',array(
            'use_buildin_delete' => true,
            'title' => app::get('sysshop')->_('第三方服务列表'),
            'actions'=>array(
                /*
                 * 暂时注释此处，遗留后用
                 array(
                     'label'=>'发送邮件短信',
                     'submit'=>'?app=sysshop&ctl=admin_seller&act=messenger',
                 ),
                 */
                array(
                    'label'=>app::get('sysshop')->_('添加第三方服务'),
                    'href'=>'?app=sysshop&ctl=admin_service&act=addArticle',
                    'target'=>'dialog::{title:\''.app::get('sysshop')->_('添加第三方服务').'\',  width:600,height:320}',
                    ),
                array(
                        'label'=>app::get('sysshop')->_('置顶文章'),
                        'submit'=>'?app=sysshop&ctl=admin_service&act=dotop',
                    ),
                    array(
                        'label'=>app::get('sysshop')->_('设置热门文章'),
                        'submit'=>'?app=sysshop&ctl=admin_service&act=dohot',
                    ),
            ),
        ));
    }

    public function addArticle(){
        $this->contentHeaderTitle = '添加第三方服务';
        return view::make('sysshop/admin/shop/service.html',$pagedata);
    }

    public function save(){
        $data = input::get();
        $third = app::get('sysshop')->model('service');
        $data['modified'] = time();
        if(!$data['article_id']){
        $data['pubtime'] = time();
        $this->begin("?app=sysshop&ctl=admin_service&act=index");
        try {
            $third->save($data);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(success,"保存成功");
        }else{
            if($data['pubtime']){
             $data['pubtime']=strtotime( $data['pubtime']);
            }else{
                unset($data['pubtime']);
            }
            $service = $third->getRow('*',array('article_id'=>$data['article_id']));
        $this->begin("?app=sysshop&ctl=admin_service&act=index");
            try {
            $third->update($data,$service);
            } catch (Exception $e) {
            $msg = $e->getMessage();
            $this->end(false,$msg);
            }
        $this->end(success,"保存成功");
        }
    }

    public function edit(){
        $data = input::get();
        $service = app::get('sysshop')->model('service');
        $serviceinfo = $service->getRow('*',array('article_id'=>$data['article_id']));
        $pagedata['row'] = $serviceinfo;
        $this->contentHeaderTitle = '修改第三方服务';
        return view::make('sysshop/admin/shop/service.html',$pagedata);
    }

         public function _views()
    {
         $mdl_all = app::get('sysshop')->model('service');
         $upfliter = array('istop' => 1);
         $hotfliter = array('ishot' => 1);
         $all=$mdl_all->count();
         $up=$mdl_all->count($upfliter);
         $hot=$mdl_all->count($hotfliter);
         $subMenu = array(
            0=>array(
                'label'=>app::get('sysshop')->_("全部文章 ( $all )"),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('sysshop')->_("置顶文章 ( $up )"),
                'optional'=>false,
                'filter'=>array(
                    'istop'=>1,
                ),
            ),
            2=>array(
                'label'=>app::get('sysshop')->_("热门文章 ( $hot )"),
                'optional'=>false,
                'filter'=>array(
                    'ishot'=>1,
                ),
            ),
            );
          return $subMenu;
    }

      public function dotop()
      {
        $this->begin('?app=sysshop&ctl=admin_service&act=index');
        $postdata=$_POST;
        //var_dump($postdata);
        try {
             foreach ($postdata["article_id"] as $key => $value) {
                $service= app::get('sysshop')->model('service')->getRow("*",array("article_id"=>$value));
            if($service["istop"]=="1"){
                $service["istop"]="0";
                app::get('sysshop')->model('service')->save($service);
            }
            else{
                $service["istop"]="1";
                app::get('sysshop')->model('service')->save($service);
            }
          }
        } catch (Exception $e) {
            $msg=$e->getMessage();
             $this->end(false,$msg);
        }
          $this->end("设置成功");
      }

      public function dohot()
      {
        $this->begin('?app=sysshop&ctl=admin_service&act=index');
        $postdata=$_POST;
        //var_dump($postdata);
        try {
             foreach ($postdata["article_id"] as $key => $value) {
                $service= app::get('sysshop')->model('service')->getRow("*",array("article_id"=>$value));
            if($service["ishot"]=="1"){
                $service["ishot"]="0";
                app::get('sysshop')->model('service')->save($service);
            }
            else{
                $service["ishot"]="1";
                app::get('sysshop')->model('service')->save($service);
            }
          }
        } catch (Exception $e) {
            $msg=$e->getMessage();
             $this->end(false,$msg);
        }
          $this->end("设置成功");
      }
}