<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model
{
	public $count;
	
	public function __construct()
	{
		$this->load->database();
		$this->load->library('session');	
	}

	//取得資料物件
	public function get_count()
	{
		return $this->count;
	}
	
	//取得資料列表
	public function get_list($page = 1,$limit = 50)
	{
		$this->db->from('report');
		$this->count = $this->db->count_all_results();
		
		$start = ($page-1)*$limit;
		$this->db->select('report.*,authority.name as authority');
		$this->db->from('report');
		$this->db->join('authority', 'report.authority=authority.aId');
		$this->db->order_by('report.reportDate','desc');
		$this->db->limit($limit,$start);
		$query = $this->db->get();
		
		return $query->result();
	}

	//取得搜索資料列表
	public function get_search($key = null,$page = 1,$limit = 50)
	{
		
		$this->db->select('report.*,authority.name as authority');
		$this->db->from('report');
		$this->db->join('authority', 'report.authority=authority.aId');
		$this->db->like('report.name',$key);
		$this->db->or_like('report.sysid',$key);
		$this->db->or_like('report.keyword',$key);
		$this->db->or_like('report.report',$key);
		//$this->db->or_like('report.periodStart',$key);
		//$this->db->or_like('report.periodEnd',$key);
		$this->db->or_like('authority.name',$key);
		$this->count = $this->db->count_all_results();

		//$query = $this->db->query($sql,array($key));		
		//$row = $query->row_array();
		//$this->count = $row['counts'];
		
		$start = ($page-1)*$limit;
		$this->db->select('report.*,authority.name as authority');
		$this->db->from('report');
		$this->db->join('authority', 'report.authority=authority.aId');
		$this->db->like('report.name',$key);
		$this->db->or_like('report.sysid',$key);
		$this->db->or_like('report.keyword',$key);
		$this->db->or_like('report.report',$key);
		//$this->db->or_like('report.periodStart',$key);
		//$this->db->or_like('report.periodEnd',$key);
		$this->db->or_like('authority.name',$key);
		$this->db->order_by('report.reportDate','desc');
		$this->db->limit($limit,$start);
		$query = $this->db->get();
		
		return $query->result();
	}	
	
	//取得資料物件	
	public function get_report($id = FALSE)
	{
		if ($id === FALSE)
		{
			//$query = $this->db->get('report');
			//return $query->result_array();
			return;
		}
		
		$sql = "SELECT report.*,a1.name as authority,a2.name as scId,a3.name as pcId,a4.name as aCategory FROM report
		LEFT JOIN authority a1 on report.authority=a1.aId
		LEFT JOIN authority a2 on report.scId=a2.aId
		LEFT JOIN authority a3 on report.pcId=a3.aId
		LEFT JOIN authority a4 on report.aCategory=a4.aId
		where report.id=? ";		
		$query = $this->db->query($sql, array($id));
		$row = $query->row_array();
		
		$sql = "SELECT a1.name FROM country
		LEFT JOIN authority a1 on country.aId=a1.aId
		where country.rId=?";
		$query = $this->db->query($sql, array($id));
		$row['country'] = $query->result();
		
		$sql = "SELECT abroad.*,a1.name as agencies,a2.name as units FROM abroad
		LEFT JOIN authority a1 on abroad.agencies=a1.aId
		LEFT JOIN authority a2 on abroad.units=a2.aId
		where abroad.rId=?";
		$query = $this->db->query($sql, array($id));
		$row['abroad'] = $query->result();		

		$sql = "SELECT * FROM file where rId=?";
		$query = $this->db->query($sql, array($id));
		$row['file'] = $query->result();
		
		return $row;
	}
	
	public function searchterm_handler($searchterm)
	{
	    if($searchterm)
	    {
		$this->session->set_userdata('searchterm', $searchterm);
		return $searchterm;
	    }
	    elseif($this->session->userdata('searchterm'))
	    {
		$searchterm = $this->session->userdata('searchterm');
		return $searchterm;
	    }
	    else
	    {
		$searchterm ="";
		return $searchterm;
	    }
	}	
}