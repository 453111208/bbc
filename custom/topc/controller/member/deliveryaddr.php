<?php
class topc_ctl_member_deliveryaddr extends topc_ctl_member {

public function setAddrDef(){
        $postData = input::get();
        $addritem=app::get('sysshoppubt')->model('deliveryaddr');
         try
        {
       $sql="update sysshoppubt_deliveryaddr set def_addr = 0 where uniqid = '".$postData['uniqid']."'";
        app::get('sysshoppubt')->database()->executeUpdate($sql);
       $sql="update sysshoppubt_deliveryaddr set def_addr = 1 where deliveryaddr_id = '".$postData['deliveryaddr_id']."'";
       app::get('sysshoppubt')->database()->executeUpdate($sql);
       	$filter['uniqid']=$postData['uniqid'];
        $userAddrList=$addritem->getList('*',$filter);
        foreach ($userAddrList as &$addr) {
            list($regions,$region_id) = explode(':', $addr['area']);
            $addr['region_id'] = str_replace('/', ',', $region_id);
        }
        $pagedata['userAddrList'] = $userAddrList;
        $msg=view::make('topc/member/shoppubt/add_edit.html',$pagedata)->render();
        return $this->splash('success',null,$msg,true);
        }
            catch(Exception $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
            catch(\LogicException $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
	}
}