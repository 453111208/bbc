<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array (
    'columns' =>
    array (
        'literary_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('sysplan')->_('成功案例案例ID'),
            'autoincrement' => true,   //序号自增
            'width' => 50,
            'order'=>1,
            ),
        
        'title' =>array (
            'type' => 'string',     //字段类型
            'searchtype' => 'has',    //搜索的类型
            'filtertype' => 'normal', //高级筛选的过滤类型，normal按type来生成过滤
            'filterdefault' => 'true',//默认在高级筛选中显示
            'required' => true,   //必填项，不能为空，默认为falsse
            'in_list' => true,  //显示在列表项中
            'default_in_list' => true,  //默认显示在列表项中
            'label' => app::get('sysplan')->_('案例名称'),  //显示的名称
            'order'=>2,   //列表中的权重，越小越靠前！
        ),
        'author' =>array (
            'type' => 'string',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysplan')->_('案例作者'),
            'order'=>3,
        ),
         'literarycat_id' => array(
            'type' => 'table:literarycat@sysplan',
            'required' => true,
            'in_list' => true,  //显示在列表项中
            'default_in_list' => true,  //默认显示在列表项中
            'label' => app::get('sysplan')->_('案例类型'),
            'comment' => app::get('sysplan')->_('案例类型ID'),
            'order'=>4,
        ),
         'literaryclass_id' => array(
            'type' => 'table:literaryclass@sysplan',
            'required' => true,
            'in_list' => true,  //显示在列表项中
            'default_in_list' => true,  //默认显示在列表项中
            'label' => app::get('sysplan')->_('案例分类'),
            'comment' => app::get('sysplan')->_('案例分类ID'),
            'order'=>5,
        ),
         'literarytarget_id' => array(
            'type' => 'table:literarytarget@sysplan',
            'required' => true,
            'in_list' => true,  //显示在列表项中
            'default_in_list' => true,  //默认显示在列表项中
            'label' => app::get('sysplan')->_('案例目标'),
            'comment' => app::get('sysplan')->_('案例目标ID'),
            'order'=>5,
        ),
         'literary_logo' => array(
            'type' => 'string',
            'in_list' => true,
            'label' => app::get('sysplan')->_('案例列表图片'),
            'order'=>9,
            'comment' => app::get('sysplan')->_('案例列表图片'),
        ),
       'pubtime' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysplan')->_('发布时间'),
            'label' => app::get('sysplan')->_('发布时间'),
            'editable' => true,
            'width' => 130,
            'order'=>6,
        ),
        'abstract' => array(
            'type' => 'text',
            'required' => true,
            'in_list' => true,
            'label' => app::get('sysplan')->_('案例摘要'),
            'comment' => app::get('sysplan')->_('案例摘要'),
            'order'=>7,
        ),
        'context' => array(
            'type' => 'text',
            'required' => true,
            'in_list' => true,
            'label' => app::get('sysplan')->_('案例内容'),
            'comment' => app::get('sysplan')->_('案例内容'),
            'order'=>5,
        ),
        'modified'=> array (
            'type' => 'time',
            'editable' => true,
            'in_list' => true,
            'label' => app::get('sysplan')->_('修改时间'),
            'comment' => app::get('sysplan')->_('修改时间'),
        ),
         'click_count' =>array (
            'type' => 'number',
            'in_list' => true,
            'default' => 0,
            'default_in_list' => true,
            'label' => app::get('sysplan')->_('点击量'),
            'comment'=> app::get('sysplan')->_('点击量'),
            'order'=>10,
        ),
         
        'towhere' =>array(
            'type' => array(
                '0' => app::get('sysplan')->_('否'),
                '1' => app::get('sysplan')->_('是'),
            ),
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysplan')->_('是否发布到成功案例'),
            'label' => app::get('sysplan')->_('是否发布到成功案例'),
            'width' => 40,
            'order'=>11,
        ),

        /*'promote' =>array (
            'type' => 'string',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysplan')->_('价值提升'),
            'order'=>7,
        ),
        'contribution' =>array (
            'type' => 'string',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysplan')->_('环保贡献'),
            'order'=>8,
        ),*/
        

       

  ),
    'primary' => 'literary_id',
    'comment' => app::get('sysplan')->_('专家案例表'),
);
