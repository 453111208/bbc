<?php

/**
 * @brief 商品列表
 */
class sysshoppubt_ctl_tenderule extends desktop_controller{
    
    public function index()
    {
        return $this->finder('sysshoppubt_mdl_tenderule',array(
            'use_buildin_filter'=>true,
            'use_view_tab'=>true,
            'title' => app::get('sysshoppubt')->_('招标规则表'),
            'use_buildin_delete'=>true,
            'actions'=>array(
                    0=>array(
                        'label'=>app::get('sysshoppubt')->_('添加规则'),
                        'href'=>'?app=sysshoppubt&ctl=tenderule&act=create','target'=>'dialog::{title:\''.app::get('sysshoppubt')->_('添加规则').'\',width:500,height:350}'
                    ),
                )
        ));
    }
    public function create($tenderrule_id){

        if($tenderrule_id !=""){
            $tenderule_model = app::get('sysshoppubt')->model('tenderule');
            $row = $tenderule_model->getRow('*',array('tenderrule_id'=>$tenderrule_id)); 
            $pagedata['row'] =  $row;
        }
         return view::make('sysshoppubt/tenderule/create.html', $pagedata);
    }

    public function save(){
            $data=input::get();
            $details = app::get('sysshoppubt')->model('detail');
            $tenderule = app::get('sysshoppubt')->model('tenderule');
            $keyinfo = 0;
            foreach ($data as $key => $value) {
                $keys = split("_", $key);
                if($keys[0] !="detail")
                $newdata[$key] = $value;
                else if($keys[0] =="detail"&&$value!='')$keyinfo = 1;
            }
             if($newdata['tenderrule_id'] != ""){
                //update
                $tenderule_model = app::get('sysshoppubt')->model('tenderule');
                $tenderuleOld = $tenderule_model->getRow('*',array('tenderrule_id'=>$newdata['tenderrule_id'])); 
                $tenderule_model->update($newdata,$tenderuleOld);
            }else{
                //save
                $newdata = input::get();
                $newdata['create_time'] = time();
                $newdata['serial'] = 1;
                $tenderule->save($newdata);
            }
            if($keyinfo){
                $getdetail = $details ->getRow('*',array('tenderrule_id'=>$data['tenderrule_id']));
                $detailsave = app::get('sysshoppubt')->model('detail');
                    $tenderule = app::get('sysshoppubt')->model('tenderule');
                    $ids = $tenderule->getRow('tenderrule_id');
                    $num = $tenderule->count();
                    $result = floatval($ids['tenderrule_id']) + floatval($num) - 1;
                if($getdetail){
                    $details->delete(array('tenderrule_id'=>$data['tenderrule_id']));
                    }
                    $indetail['tenderrule_id'] = $result;
                    $indetail['create_time'] = time();
                    foreach ($data as $key => $value) {
                        $keys = split("_", $key);
                        if($keys[0] == "detail"){
                        $indetail['detail'] = $value;
                        $sql="insert into sysshoppubt_detail (tenderrule_id,detail,create_time) values (". $result . ",'" . $value . "'," . time() . ")";

                        $db = app::get('sysshoppubt')->database();
                        $db->exec($sql);
                        }
                    }
                }
                    $msg = app::get('sysshoppubt')->_('全部保存成功');
                return $this->splash('success',null,$msg);
        }

    }