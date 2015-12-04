<?php


/*
 * @package content
 * @subpackage article
 * @copyright Copyright (c) 2015, shopex. inc
 * @author gongjiapeng@shopex.cn
 * @license 
 */
class syscontent_article_node
{


    private $_all_nodes = null;
    private $_node_maps = array();
    //获取节点列表
    public function getNodeList($nodeId=0)
    {
        $rows = $this->get_maps($nodeId);
        return $this->parse_listmaps($rows);
    }
    //获取节点列表给挂件用
    public function nodeListWidget($nodeId=0)
    {
        $rows = $this->get_maps($nodeId);
        $nodelist = $this->parse_listmaps($rows);
        foreach ($nodelist as $key => $value)
        {
            $selectmaps[$key]['node_id'] = $value['node_id'];
            $selectmaps[$key]['step'] = $value['node_depth'];
            $selectmaps[$key]['node_name'] = $value['node_name'];
        }
        return $selectmaps;
    }

    /**
    * 节点的map
    * @param int $node_id 节点id
    * 
    * @return array 节点路由
    */
    public function get_maps($nodeId=0)
    {
        $rows = $this->get_nodes($nodeId);
        foreach($rows AS $k=>$v)
        {
            if($v['has_children'])
            {
                $rows[$k]['childrens'] = $this->get_maps($v['node_id']);
            }
        }
        $this->_node_maps[$nodeId] = $rows;

        return $this->_node_maps[$nodeId];
    }
    /**
    * 格式化节点的map 是否首页，标题名
    * @param array $rows 节点MAP
    * @param array
    */
    private function parse_listmaps($rows) 
    {
        $data = array();
        foreach((array)$rows AS $k=>$v)
        {
            $children = $v['childrens'];
            if(isset($v['childrens']))  unset($v['childrens']);
            $data[] = $v;
            if($children){
                $data = array_merge($data, $this->parse_listmaps($children));
            }
        }
        return $data;
    }

    /**
    * 父节点下的子节点数据
    * @param int $parent_id 父节点id
    * @return 节点数组值
    */
    public function get_nodes($parent_id=0) 
    {
        if(!$fields)
        {
            $fields = '*';
        }
        $nodeMdl = app::get('syscontent')->model('article_nodes');
        $parent_id = intval($parent_id);
        if(is_null($this->_all_nodes)){
            $this->_all_nodes = array();
            $nodeList = $nodeMdl->getList($fields,null,'0','-1','order_sort ASC');
            foreach($nodeList AS $node){
                $this->_all_nodes[$node['parent_id']][] = $node;
            }
        }
        return $this->_all_nodes[$parent_id]; 
    }


    /**
    * 获取节点的map
    * @param string $node_id
    * @param int $setp 路径
    * @param array
    */
    public function getSelectmaps() 
    {
        $nodeMdl = app::get('syscontent')->model('article_nodes');
        $fields = 'node_id,node_depth,node_name';
        $rows = $nodeMdl->getList($fields,array('parent_id'=>0));
        foreach ($rows as $key => $value)
        {
            $data[$value['node_id']]['node_id'] = $value['node_id'];
            $data[$value['node_id']]['step'] = $value['node_depth'];
            $data[$value['node_id']]['node_name'] = $value['node_name'];
        }

        return $data;
    }
    //保存节点
    public function savaNode($data)
    {
        $nodeMdl = app::get('syscontent')->model('article_nodes');
        if($data)
        {
            $saveData = $this->checkData($data);
            
            $db = app::get('syscontent')->database();
            $transaction_status = $db->beginTransaction();
            try
            {
                if($data['node_id']=='')
                {
                    $nodeId = $nodeMdl->insert($saveData);
                    if($nodeId)
                    {
                        $nodePath = $saveData['parent_id'].','.$nodeId;
                        $parentId = $saveData['parent_id'];
                        $nodeMdl->update(array('node_path'=>$nodePath),array('node_id'=>$nodeId));
                        if($saveData['parent_id']!=0)
                        {
                            $nodeMdl->update(array('has_children'=>1),array('node_id'=>$parentId));
                        }
                    }
                }
                else
                {
                    //echo '<pre>';print_r($data);exit();
                    //$parentId = $nodeMdl->getRow('parent_id',array('node_id'=>$data['node_id']));
                    if($data['parent_id']!=0)
                    {
                        $nodeList = $nodeMdl->getList('node_id',array('parent_id'=>$data['node_id']));
                        if(!empty($nodeList))
                        {
                            throw new \LogicException('不能将父节点修改成子节点!');
                        }
                    }
                    $nodeMdl->save($saveData);
                    $nodePath = $saveData['parent_id'].','.$nodeId;
                    $nodeMdl->update(array('node_path'=>$nodePath),array('node_id'=>$data['node_id']));
                }
                $db->commit($transaction_status);
            }
            catch(LogicException $e)
            {
                $db->rollback();
                throw $e;
            }
        }
        else
        {
            throw new \LogicException('数据错误!');
        }
    }
    //数据检查
    public  function  checkData($data)
    {
        if($data['parent_id']==0)
        {
            $data['node_depth']=1;
            $data['has_children'] = 1;
        }
        else
        {
            $data['node_depth']=2;
            $data['has_children'] = 0;
        }
        $data['modified'] = time();

        return $data;
    }

    //修改节点
    public function editNode($nodeId)
    {
        $nodeMdl = app::get('syscontent')->model('article_nodes');
        if($nodeId=='')
        {
            throw new \LogicException('节点id不能为空!');
        }
        $nodeInfo = $nodeMdl->getRow('node_id,node_name,parent_id,node_depth,node_path,order_sort',array('node_id'=>$nodeId));
        return $nodeInfo;
    }

    //删除节点
    public function deleteNode($data)
    {
        $nodeMdl = app::get('syscontent')->model('article_nodes');
        $articleMdl = app::get('syscontent')->model('article');
        $validator = validator::make(
            ['node_id' => $data['node_id']],
            ['node_id' => 'required'],
            ['node_id' => '节点id不能为空!']
        );
        if ($validator->fails())
        {
            $messages = $validator->messagesInfo();
            foreach( $messages as $error )
            {
                throw new LogicException( $error[0] );
            }
        }
        /*if(!$data['node_id'])
        {
            throw new \LogicException('节点id不能为空!');
        }*/
        $article = $articleMdl->getList('article_id',array('node_id'=>$data['node_id']));

        if($article)
        {
            throw new \LogicException('该节点下面存在文章，请先删除文章!');
        }
        if($data['parent_id']==0)
        {
            $hasChildren = $nodeMdl->getList('node_id',array('parent_id'=>$data['node_id']));
            if($hasChildren)
            {
                throw new \LogicException('该节点下面存在子节点，请先删除子节点!');
            }
        }

        $nodeDelete = $nodeMdl->delete(array('node_id'=>$data['node_id']));
        if($nodeDelete)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    //保存修改的排序
    public function updateNode($data)
    {
        $nodeMdl = app::get('syscontent')->model('article_nodes');
        if(!$data)
        {
            throw new \LogicException('数据错误!');
        }
        else
        {
            $tmp = $data['order_sort'];
            is_array($tmp) or $tmp = array();
            foreach($tmp as $key => $val)
            {
                $filter = array('order_sort'=>$val, 'node_id'=>$key);
                $nodeMdl->save($filter);
            }
        }
    }

    //获取节点列表，并且按照父类分好
    public function getNodeData($fields,$parentId,$orderBy)
    {
        if(!$fields)
        {
            $fields = '*';
        }
        if($parentId=='')
        {
            $parentId = 0;
        }
        if($orderBy=='')
        {
            $orderBy = 'modified DESC';
        }
        if(!is_numeric($parentId))
        {
            throw new \LogicException('请传入正确的数据!');
        }
        $data = array();
        $nodeMdl = app::get('syscontent')->model('article_nodes');
        $nodeList = $nodeMdl->getList($fields,array('parent_id'=>$parentId),'0','-1',$orderBy);
        foreach ($nodeList as $key => $value)
        {
            $nodeChilde = $nodeMdl->getList($fields,array('parent_id'=>$value['node_id']),'0','-1',$orderBy);
            $data[$value['node_id']]['node_id'] = $value['node_id'];
            $data[$value['node_id']]['parent_id'] = $value['parent_id'];
            $data[$value['node_id']]['node_name'] = $value['node_name'];
            $data[$value['node_id']]['children'] = $nodeChilde;
        }
        return $data;
    }

}
