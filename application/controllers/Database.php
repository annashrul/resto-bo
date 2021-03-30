<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Database extends CI_Controller {
	
	public $password = null;
	
	public function __construct(){
		parent::__construct();
		$this->password = 'a02046ded1c00d2db6dbc299d8fecb46';
	}
	
	public function index(){
		if(isset($_POST['password'])){ $this->session->set_userdata('hasbi', md5($this->input->post('password'))); }
		
		if(isset($_POST['menu'])){ redirect('database/'. $this->input->post('menu')); }
		
		if($this->session->hasbi == $this->password){
			redirect('database/create_query');
		} else {
			$this->session->unset_userdata('hasbi');
			$this->load->view('database/form_login');
		}
	}
	
	public function download_backup(){	
		$this->logged();
		$this->load->dbutil();
		$prefs = array(
			//'tables'        => array('table1', 'table2'),   // Array of tables to backup.
			'ignore'        => array(),                     // List of tables to omit from the backup
			'format'        => 'txt',                       // gzip, zip, txt
			'filename'      => 'mybackup.sql',              // File name - NEEDED ONLY WITH ZIP FILES
			'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
			'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
			'newline'       => "\n"                         // Newline character used in backup file
		);
		$backup = $this->dbutil->backup($prefs);
		//$backup = $this->dbutil->backup();
		//$this->load->helper('file');
		//delete_files('assets/database/'.$prefs['filename'], TRUE);
		//write_file('assets/database/'.$prefs['filename'], $backup);
		//$this->load->helper('download');
		force_download($prefs['filename'], $backup);
		$this->load->view('database/index');
	}
	
	public function sksksy(){
		
	}
	
	public function create_query(){
		$this->logged();
		if(isset($_POST['query']) || isset($_POST['execute'])){
			if($this->db->query($this->input->post('query'))){
				$string = null;
				$file = "database_query.txt";
				$string = read_file('./'.$file);
				$string = $string . PHP_EOL . $this->input->post('query') . ';';
				if (write_file('./'.$file, $string)){
					echo 'Query success execute and written!';
				} else {
					echo 'Unable to write the file';
				}
			} else {
				echo $this->db->error();
			}
		}
		$this->load->view('database/index', array('content' => 'form_create_query'));
	}
	
	public function execute_query(){
		$this->logged();
		$done = "Query has been execute";
		$file = "database_query.txt";
		$string = explode(';', read_file('./'.$file));
		foreach($string as $row){
			if($row != null && $row != '' && $row != PHP_EOL && $row != PHP_EOL . PHP_EOL  && $row != PHP_EOL . PHP_EOL . PHP_EOL){
				if( ! $this->db->query(str_replace(';', '', $row))){
					$done = 'Execute not complete';
					$data = null;
					$file = "database_failur.txt";
					$data = read_file('./'.$file);
					$data = $data . $row  . ';';
					if ( ! write_file('./'.$file, $data)){
						redirect('database');
					}
				} 
			} 
		}
		$this->load->view('database/index');
		echo $done;
	}
	
	public function logout(){
		$this->session->unset_userdata('hasbi');
		redirect('database');
	}
	
	public function logged(){
		if($this->session->hasbi == $this->password){ return true; } else { redirect('database'); }
	}
	
	
}

