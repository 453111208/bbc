<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class syscategory_mdl_cat extends dbeav_model {


    public $defaultOrder = array('order_sort',' asc',',cat_id',' DESC');

    public $has_many = array(
        'prop' => 'cat_rel_prop:replace',
    );

    public $subSdf = array(
        'default' => array(
            'prop' => array('*'),
        )
    );

    /**
     * 构造方法
     * @param object model相应app的对象
     * @return null
     */
    public function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * 保存的方法
     * @param  mixed  $aData      保存的数据内容
     * @param  boolean  $mustUpdate 是否必须更新
     * @param  boolean $mustInsert 是否必须插入
     * @return boolea              是否保存成功
     */
    public function save(&$aData, $mustUpdate = null, $mustInsert = false)
    {
        if($aData['cat_id'])
        {
            $oldData = $this->getRow('cat_id, parent_id, level',array('cat_id'=>$aData['cat_id']) );
        }

        if(!$oldData)
        {
            $flag = 'add'; // 添加分类
        }
        else
        {
            $flag = 'edit'; // 编辑分类
        }

        if($flag == 'edit')
        {
            // 如果编辑后的parent_id与原来分类的parent_id不一直则报错
            if($aData['parent_id'] && ($oldData['parent_id'] != $aData['parent_id']))
            {
                $msg = '您不能修改分类的上级分类parent_id';
                throw new \LogicException($msg);
                return false;
            }
        }

        if($flag == 'add')
        {
            // 添加子节点后更新父节点的子节点数量字段
            if($aData['parent_id'] != 0)
            {
                $row = $this->getRow('child_count, level', array('cat_id'=>$aData['parent_id']) );
                // 如果节点已经是三级节点了，则不允许添加子节点了
                if($row['level'] == '3')
                {
                    $msg = '最多只能添加三级分类';
                    throw new \LogicException($msg);
                    return false;
                }
                // 如果父节点是二级分类，则此节点则为叶子节点
                if($row['level'] == '2')
                {
                    $aData['is_leaf'] = 1;
                }
                $parentData['child_count'] = $row['child_count']+1;
                $parentData['cat_id'] = $aData['parent_id'];
                parent::save($parentData);
            }
        }
        if($aData['parent_id'])
        {
            $aData['cat_path'] = $this->genCatPath($aData['parent_id']); // 分类路径
            $aData['level'] = substr_count($aData['cat_path'],','); // 分类层级
        }

        parent::save($aData);
        return $this->cat2json();
    }

    /**
     * 生成分类节点的路径
     * @param  int $parent_id 父节点分类id
     * @return string 分类节点路径
     */
    public function genCatPath($parent_id)
    {
        if($parent_id == 0)
        {
            return ',';
        }
        $cat_sdf = $this->getRow('cat_id, cat_path', array('cat_id'=>$parent_id));
        return $cat_sdf['cat_path'].$cat_sdf['cat_id'].",";
    }

    /**
     * 得到整个分类树形结构
     * @param null
     * @return mixed 返回的数据
     */
    public function getTree()
    {
        $fields = 'cat_name AS text,cat_id AS id,parent_id AS pid,order_sort,level,cat_path,is_leaf,child_count as type_name ';
        return $this->getList($fields, array(), 0, -1, 'order_sort ASC, cat_id DESC');
    }

    public function getMapTree($ss=0, $str='└')
    {
        $var_ss = $ss;
        $var_str = $str;
        if(isset($this->catMapTree[$var_ss][$var_str]))
        {
            return $this->catMapTree[$var_ss][$var_str];
        }
        $retCat = $this->map($this->getTree(), $ss, $str, $no, $num);
        $this->catMapTree[$var_ss][$var_str] = $retCat;
        global $step, $cat;
        $step = '';
        $cat = array();
        return $retCat;
    }

    public function map($data, $sID=0, $preStr='', &$cat_cuttent, &$step)
    {
        set_time_limit(2000);
        $step++;
        if($data)
        {
            $tmpCat = array();
            foreach($data as $i => $value)
            {
                $count = substr_count( $data[$i]['cat_path'],',' );
                $id = $data[$i]['id'];
                $cls = ($data[$i]['child_count']?'true':'false');

                $tmpCat[$value['pid']][] = array(
                    'cat_name' => $data[$i]['text'],
                    'cat_id' => $data[$i]['id'],
                    'pid' => $data[$i]['pid'],
                    'level' => $data[$i]['level'],
                    'type' => $data[$i]['type'],
                    'type_name' => $data[$i]['type_name'],
                    'step' => $count?$count:1,
                    'order_sort' => $data[$i]['order_sort'],
                    'cat_path' => $data[$i]['cat_path'],
                    'cls' => $cls,
                );
            }
            $this->_map($cat_cuttent, $tmpCat, 0);
        }
        $step--;
        return $cat_cuttent;
    }

    public function _map(&$cat_cuttent, $data, $key)
    {
        if(is_array($data[$key]))
        {
            foreach($data[$key] as $k => $v)
            {
                $cat_cuttent[] = $v;
                if($data[$v['cat_id']])
                {
                    $this->_map($cat_cuttent, $data, $v['cat_id']);
                }
            }
        }
    }

    public function cat2json($return=false)
    {
        $contents = $this->getMapTree(0, '');
        base_kvstore::instance('category')->store('goods_cat.data', $contents);
        if($return)
        {
            return $contents;
        }
        else
        {
            return true;
        }
    }

    public function getCatList($show_stable=false)
    {
        if( base_kvstore::instance('category')->fetch('goods_cat.data', $contents) !== false )
        {
            if(is_array($contents))
            {
                $result = $contents;
            }
            else
            {
                $result = json_decode($contents,true);
            }

            if($result)
            {
                return $result;
            }
            else
            {
                return $this->cat2json(true);
            }
        }
        else
        {
            return $this->cat2json(true);
        }
    }
}


