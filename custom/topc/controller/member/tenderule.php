<?php
   class topc_ctl_member_tenderule extends topc_ctl_member {
   		public function tenderule(){
            $uniqid = input::get('uniqid');
            $pagedata['uniqid'] = $uniqid;
            $tender = app::get('sysshoppubt')->model('tender');
   			$rule = app::get('sysshoppubt')->model('tenderule');
            $chrule = app::get('sysshoppubt')->model('chrule');
   			$rulelist = $rule->getList('*');
            $getdetail = app::get('sysshoppubt')->model('detail');
            $show = $getdetail->getList("*");
            foreach ($show as $key => $value) {
               $newproject[$key] = $value['tenderrule_id'];
            }
            $all = $rule->getList('*');
            //选择型资质
            $newpro =array();
            $project = $rule->getList('*',array('tenderrule_id'=>$newproject));
            foreach ($project as $key => $value) {
               $fun = $getdetail->getList('detail',array('tenderrule_id'=>$value['tenderrule_id']));
               $value['detail'] = $fun;
               $newpro[] = $value;
            }
            $pagedata['newpro'] = $newpro;
            //打分型资质
            $pro = $rule->getList('*',array('have_detail'=>0));
            foreach ($pro as $key => $value) {
               $oldscore = $chrule->getRow('score',array('tenderrule_id'=>$value['tenderrule_id'],'detail'=>null,'uniqid'=>$uniqid));
               $pro[$key]['score'] = $oldscore['score'];
            }
            $pagedata['pro'] = $pro;
            $pagedata['num'] = $rule->count(array('have_detail'=>0));
            //算总分
            $score = $chrule->getList('score',array('category' => '必要资质','detail'=>null,'uniqid'=>$pagedata['uniqid']));
            foreach ($score as $key => $value) {
               $sums = $sums + $value['score'];
            }
            $pagedata['sums']= $sums;

            $scorec = $chrule->getList('score',array('category' => '可选资质','detail'=>null,'uniqid'=>$pagedata['uniqid']));
            foreach ($scorec as $key => $value) {
               $sumc = $sumc + $value['score'];
            }
            $pagedata['sumc']= $sumc;

            $scorem = $chrule->getList('score',array('category' => '设备能力','detail'=>null,'uniqid'=>$pagedata['uniqid']));
            foreach ($scorem as $key => $value) {
               $summ = $summ + $value['score'];
            }
            $pagedata['summ']= $summ;

            $scored = $chrule->getList('score',array('category' => '处置能力','detail'=>null,'uniqid'=>$pagedata['uniqid']));
            foreach ($scored as $key => $value) {
               $sumd = $sumd + $value['score'];
            }
            $pagedata['sumd']= $sumd;

            $scoresv = $chrule->getList('score',array('category' => '服务能力','detail'=>null,'uniqid'=>$pagedata['uniqid']));
            foreach ($scoresv as $key => $value) {
               $sumsv = $sumsv + $value['score'];
            }
            $pagedata['sumsv']= $sumsv;
   			return view::make('topc/member/shoppubt/tenderule.html',$pagedata);
   		}
   		public function save(){
            if(!userAuth::id()){
            $msg = app::get('topc')->_('请先登录');
            return $this->splash('error',null,$msg);
            }
            $result = input::get();
   			$rule = app::get('sysshoppubt')->model('chrule');
            $uniq = $rule->getList('uniqid,chrule_id',array('uniqid'=>$result['uniqid']));
            if($uniq){
                  $rule->delete(array('uniqid'=>$result['uniqid']));
            }
            $tenderule = app::get('sysshoppubt')->model('tenderule');
            $tendetail = app::get('sysshoppubt')->model('detail');
            foreach ($result as $key => $value) {
               $kyeone = split('_', $key);
               if($value == 'on'){
               $get = $tenderule->getRow('category,project,tenderrule_id,type',array('tenderrule_id'=>$kyeone[0]));
                  if($get){
                  $get['detail'] = $kyeone[2];
                  $get['uniqid'] = $result['uniqid'];
                  $get['create_time'] = time();
                  $rule->save($get);
                  }
               }
               else if($value =='check'){
                  $get = $tenderule->getRow('category,project,tenderrule_id,type',array('tenderrule_id'=>$key));
                  if($get){
                     $get['score'] = $result["a".$key];
                     $get['uniqid'] = $result['uniqid'];
                     $get['create_time'] = time();
                     
                     $rule->save($get);
                  }
               }
               else{
               }
            }

            $msg = app::get('topc')->_('添加成功');
            return $this->splash('success','',$msg);
   		}
   }