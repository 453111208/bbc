<?php

/**
 * @brief 评论列表
 */
class sysshoppubt_ctl_comment extends desktop_controller{
	
 	public function index()
    {
        return $this->finder('sysshoppubt_mdl_comment',array(
            'use_buildin_filter'=>true,
            'use_view_tab'=>true,
            'title' => app::get('sysshoppubt')->_('评论列表'),
            'use_buildin_delete'=>true,
        ));
    }
    public function edit($comment_id){
    	if(!$comment_id)
        {
            $comment_ids = input::get('comment_id');
            $comment_id = implode(',',$comment_ids);
        }
        $comment = app::get('sysshoppubt')->model('comment');
        $commentall = $comment->getRow('*',array('comment_id'=>$comment_id));
        $pagedata['commentall'] = $commentall;
        return view::make('sysshoppubt/comment/detail.html', $pagedata);
    }
    public function deal(){
        $comment = app::get('sysshoppubt')->model('comment');
    	$data = input::get();
    	$newcomm = $comment->getRow('*',array('comment_id'=>$data['comment_id']));
    		if($newcomm){
    			$oldcomm = $newcomm;
    			if($data['is_lock']==1){
    				$newcomm['is_lock'] = 0;
    			}else{
    				$newcomm['is_lock'] = 1;
    			}
    			try {
    				$comment->update($newcomm,$oldcomm);
    			} catch (Exception $e) {
            		$msg = $e->getMessage();
            		return $this->splash('error',null,$msg);
    			}
    		}else{
    			$msg = "评论不存在";
            	return $this->splash('error',null,$msg);
    		}
    		return $this->splash('success',null,"操作成功");
    }
}