<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class replaceItemDescImage extends PHPUnit_Framework_TestCase {

    public function testGet()
    {
        $shopItemMdl = app::get('sysitem')->model('item');
        $shopItemData = $shopItemMdl->getList("item_id,shop_id", array(),0,-1,'item_id desc');
        if( empty($shopItemData) ) exit;
        $i=0;
        foreach( $shopItemData as $row )
        {
            $i++;
            $this->__run($row, $row['shop_id'], $row['item_id']);
            echo 'update item_id :'. $row['item_id'].'--------------------------------------------------'."{$i}\n";
        }

        #$row = $shopItemMdl->getRow("item_id,shop_id", array('item_id'=>'673'));
        #$this->__run($row, $row['shop_id'], $row['item_id']);
    }

    private function __run($shopItemData, $shopId, $itemId)
    {
        pamAccount::setAuthType('sysshop');
        pamAccount::setSession($shopId, 'test');

        $objMdlItemDesc = app::get('sysitem')->model('item_desc');
        $itemInfoDesc = $objMdlItemDesc->getRow("*", array('item_id'=>$itemId));

        $update = [];
        if( $itemInfoDesc['pc_desc'] )
        {
            $pcDesc = $this->__replaceHost($itemInfoDesc['pc_desc']);
            //$pcDesc = $this->__replaceHref($this->__replaceImage($itemInfoDesc['pc_desc']));
            if( $pcDesc != stripslashes($itemInfoDesc['pc_desc']) )
            {
                $update['pc_desc'] = $pcDesc;
            }
        }

        if( $itemInfoDesc['wap_desc'] )
        {
            $wapDesc = $this->__replaceHost($itemInfoDesc['wap_desc']);
            //$wapDesc = $this->__replaceHref($this->__replaceImage($itemInfoDesc['wap_desc']));
            if( $wapDesc != stripslashes($itemInfoDesc['wap_desc']) )
            {
                $update['wap_desc'] = $wapDesc;
            }
        }

        if( $update )
        {
            $objMdlItemDesc->update($update, array('item_id'=>$itemId));
        }

        return true;
    }

    private function __replaceHost($desc)
    {
        $desc = stripslashes($desc);
        return str_replace('http://localhost/bbc/public', 'http://images.bbc.shopex123.com', $desc);
    }

    private function __replaceHref($desc)
    {
        $desc = stripslashes($desc);
        return preg_replace("/href=[\'|\"].+?[\'|\"]/",'href="#"',$desc);
    }

    private function __replaceImage($desc)
    {
        $desc = stripslashes($desc);
        preg_match_all("/src=[\'|\"](.+?)[\'|\"]/",$desc,$match);
        foreach( $match[1] as $imageUrl )
        {
            if( strpos($imageUrl,'images.bbc.shopex123.com') ) continue;
            if( strpos($imageUrl,'localhost') ) continue;

            ini_set("memory_limit","120M");
            try
            {
                $localImageUrl = kernel::single('image_data_image')->storeNetworkImage($imageUrl,'shop','item');
                if( $localImageUrl )
                {
                    $url = $localImageUrl['url'];
                    kernel::single('image_data_image')->rebuild($localImageUrl['ident']);
                    $desc = str_replace($imageUrl, $url, $desc);
                }
                else
                {
                    $desc = str_replace($imageUrl, '#', $desc);
                }
            }
            catch( \Exception $e)
            {
                $desc = str_replace($imageUrl, '#', $desc);
            }
        }

        return $desc;
    }
}
