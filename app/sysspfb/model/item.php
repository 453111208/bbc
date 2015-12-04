<?php
class sysspfb_mdl_item extends dbeav_model{
	   public function getItemRow($itemId)
    {
        $row = $this->getRow('*',array('item_id'=>$itemId));
        $row['order_sort'] = intval($row['order_sort']);
        return $row;
    }
}