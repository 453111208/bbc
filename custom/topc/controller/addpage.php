
<?php
class topc_ctl_addpage extends topc_controller{
	 public function trading()
	{
	 $this->setLayoutFlag('trading');
	 return $this->page();
	}
	 public function articles()
	{
	 $this->setLayoutFlag('articles');
	 return $this->page();
	}
	
	 public function articleslist()
	{
	 $this->setLayoutFlag('articleslist');
	 return $this->page();
	}
	 public function articlesdtl()
	{
	 $this->setLayoutFlag('articlesdtl');
	 return $this->page();
	}
	public function supplylist()
	{
	 $this->setLayoutFlag('supplylist');
	 return $this->page();	# code...
	}
	public function supplydtl()
	{
	 $this->setLayoutFlag('supplydtl');
	 return $this->page();	# code...
	}
	public function markets()
	{
	 $this->setLayoutFlag('market');
	 return $this->page();	# code...

	}
	public function marketslist()
	{
	 $this->setLayoutFlag('marketslist');
	 return $this->page();	# code...
	}
	public function marketsdtl()
	{
	$this->setLayoutFlag('marketsdtl');
	 return $this->page();	# code...
	}
	public function marketstrad()
	{
	$this->setLayoutFlag('marketstrad');
	 return $this->page();	# code...
	}
	public function biddingdtl()
	{
	  
	    $article_id = "1";
            $artList = app::get("syscontent")->model("article")->getList("*",array('article_id'=>$article_id));
            $pagedata["artList"]=$artList[0]['content'];
            
            $article_id = "2";
            $artList = app::get("syscontent")->model("article")->getList("*",array('article_id'=>$article_id));
            $pagedata["dialogPrice"]=$artList[0]['content'];
            
            $this->setLayoutFlag('biddingdtl');
	    return $this->page('topc/article/margin.html', $pagedata);
	}
	public function tenderdtl()
	{
	 $article_id = "122";
            $artList = app::get("syscontent")->model("article")->getList("*",array('article_id'=>$article_id));
            $pagedata["tender"]=$artList[0]['content'];
            
            $this->setLayoutFlag('tenderdtl');
            return $this->page('topc/article/tender.html', $pagedata);
	}

	public function productdtl()
	{
	$this->setLayoutFlag('productdtl');
	 return $this->page();	# code...	# code...
	}
	
	
}
