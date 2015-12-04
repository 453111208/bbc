<?php
class syscontent_api_getNodeList {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取文章节点列表';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'fields'=> ['type'=>'field_list','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
            'parent_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'父级分类id','default'=>'0','example'=>''],
            'orderBy' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序','default'=>'','example'=>''],
        );

        return $return;
    }

    public function getNodeList($params)
    {
        $syscontentLibNode = kernel::single('syscontent_article_node');
        try
        {
            $nodeList = $syscontentLibNode->getNodeData($params['fields'],$params['parent_id'],$params['orderBy']);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
        return $nodeList;
    }

}
