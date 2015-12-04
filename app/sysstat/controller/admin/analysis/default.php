<?php
class sysstat_ctl_admin_analysis_default extends desktop_controller
{
	var $workground = 'sysstat.workground.analysis';

	public function index(){
		return $this->page("sysstat/admin/analysis/overview.html");
	} 

	public function chart_view()
	{
		$type=$_GET['type'];

		$pagedata['categories']="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
							'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']";

		if($type=='volume')
		{
			$pagedata['data']='[{
						name: \'London\',
						data: [4.9, 9.2, 5.7, 11.5, 2222.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
					}]';
		}
		else
		{
			$pagedata['data']='[{
						name: \'bbb\',
						data: [3.9, 4.2, 5.7, 8.5, 2222.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
						}]';
		}

		return view::make("sysstat/admin/analysis/chart_type_default.html", $pagedata);
	}
}
