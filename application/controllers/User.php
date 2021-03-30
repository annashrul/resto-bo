<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

public $user = null;

public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
		$site_data = $this->m_website->site_data();
		$this->site = str_replace(' ', '', strtolower($site_data->title));
		$this->control = 'Site';
		
		$this->user = $this->session->userdata($this->site . 'user');
		$this->username = $this->session->userdata($this->site . 'username');
		
		$this->data = array(
			'site' => $site_data,
			'account' => $this->m_website->user_data($this->user),
			'access' => $this->m_website->user_access_data($this->user)
		);
		
		$this->output->set_header("Cache-Control: no-store, no-cache, max-age=0, post-check=0, pre-check=0");
	
}



	public function nojs(){
	$data = $this->data;
	$data['title'] = 'Javascript Required';
	$data['redirect'] = base_url();
		$this->load->view('site/nojs');		
	}

	
	
	public function index(){
	$data = $this->data;
	$data['title'] = 'Dashboard';
	$data['page'] = 'dashboard';
	$data['content'] = 'dashboard';
		$this->load->view('user/header', $data);
		$this->load->view('user/content', $data);
	}
	
	public function data_output($output,$output_extra,$subject,$page)
	{
	$data = $this->data;
	$data['title'] = $subject;
	$data['page'] = $page;
	$data['content_title'] = $subject;
	$data['output'] = $output;
	$data['output_extra'] = $output_extra;
	
		$this->load->view('user/header2', $data);
		$this->load->view('user/header_grocery', $output);
		$this->load->view('user/gc_output', $data);
		
	}
	
	function access_denied($str){
		if(substr($this->m_website->user_access_data($this->user)->access,$str,1) == 0){
			echo "<script>
					alert('Access Denied');
					window.location='".base_url()."user';
					</script>";
		}	
	}
	
	public function profil(){
	$data = $this->data;
	$data['title'] = 'Profile';
	$data['page'] = '';
	$data['content'] = 'profil';
		$this->load->view('user/header', $data);
		$this->load->view('user/content', $data);
	}
	
	
	
	function ubah_profil()
	{
	$data = $this->data;
	$data["title"] = "Change Profile";
	$data['page'] = '';
	$data['content'] = 'ubah_profil';
		$this->load->view('user/header', $data);
		$this->load->view('user/content', $data);
	}
	
	
	
	function ubah_profil_do()
	{
	$user = $this->input->post('user');
	$this->m_website->ubah_profil_user($user);
		$data['isi'] = "Profil has Changed";
		$data['aksi'] = "window.location.href='".base_url()."user/profil'";
		$this->load->view('site/header_bootbox');
		$this->load->view('site/bootbox/alert', $data);
	}
	
	
	
	function ubah_password()
	{
	$this->form_validation->set_rules('passlama', '', 'required');
		if ($this->form_validation->run() === FALSE){
			$data = $this->data;
			$data["title"] = "Change Password";
			$data['page'] = '';
			$data['content'] = 'ubah_password';
				$this->load->view('user/header', $data);
				$this->load->view('user/content', $data);
		}else{
			$pass = md5($this->input->post('passlama'));
			if($pass != $this->m_website->cek_passlama($this->user)->password){
				$data['isi'] = "Wrong Old Password.";
				$data['aksi'] = "history.back(-1);";
				$this->load->view('site/header_bootbox');
				$this->load->view('site/bootbox/alert', $data);
			}else{
				$data = $this->data;
				$data["title"] = "Change Password";
				$data['page'] = '';
				$data['content'] = 'ubah_password_input';
					$this->load->view('user/header', $data);
					$this->load->view('user/content', $data);	
			}
		}
	}
	
	
	
	function ubah_password_do()
	{
	$user = $this->input->post('user');
	$this->m_website->ubah_password($user);
		$data['isi'] = "Change Password Success. Please Login Again.";
		$data['aksi'] = "window.location.href='".base_url()."site/logout'";
		$this->load->view('site/header_bootbox');
		$this->load->view('site/bootbox/alert', $data);
	}
	
	
	
	public function preference($action = null){
		//$this->access_denied(0);
		$data = $this->data;
		$function = 'preference';
		$table = 'site';
		$view = 'user/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Setting Preferences';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data['coa_group'] = array();
		$this->form_validation->set_rules('title', 'Name', 'trim|required', array('required' => '%s don`t empty'));
		/*if($action == 'delete' && isset($_GET['trx'])){
			$file = $this->m_crud->get_data($table, 'logo, fav_icon', "site_id = '".$_GET['trx']."'");
			if($file['logo']!=''){ unlink('assets/images/site/'.$file['logo']); }
			if($file['fav_icon']!=''){ unlink('assets/images/site/'.$file['fav_icon']); }
			$this->m_crud->delete_data($table, "site_id = '".$_GET['trx']."'");
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else*/ if($action == 'edit'){
			$data['preference'] = $this->m_crud->get_data($table, '*', "site_id = '".$_GET['trx']."'", 'site_id asc');
		} else { $data['preference'] = $this->m_crud->read_data($table, '*', null, 'site_id asc'); }
		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['save'])){
				$config['upload_path']          = './assets/images/site';
				$config['allowed_types']        = 'gif|jpg|jpeg|png';
				$config['max_size']             = 5120;
				$this->load->library('upload', $config);
				$input_file = array('1'=>'logo', '2'=>'fav_icon');
				$valid = true;
				foreach($input_file as $row){
					$file[$row] = $this->upload->data();
					if( (! $this->upload->do_upload($row)) && $file[$row]['file_name']!=null){
						$file[$row]['file_name']=null;
						$file[$row] = $this->upload->data();
						$valid = false; 
						if(isset($file[$row]['file_name'])&&($file[$row]['file_name']!=null||$file[$row]['file_name']!='')){ unlink($file[$row]['full_path']); }
						$data['error_'.$row] = $this->upload->display_errors(); 
					} else{
						$file[$row] = $this->upload->data();
						$data[$row] = $file;
						/*if($file[$row]['file_name']!=null){
							$manipulasi['image_library'] = 'gd2';
							$manipulasi['source_image'] = $file[$row]['full_path'];
							$manipulasi['maintain_ratio'] = true;
							$manipulasi['width']         = 500;
							//$manipulasi['height']       = 300;
							$manipulasi['new_image']       = $file[$row]['full_path'];
							$this->load->library('image_lib', $manipulasi);
							$this->image_lib->resize();
						}*/
					}
				}
				if($valid==true){
					if(isset($_POST['update'])){
						if($file['logo']['file_name']!=null && $file['fav_icon']['file_name']!=null){
							if($this->input->post('logo_gambar')!=null){unlink($file['logo']['file_path'].$this->input->post('logo_gambar'));}
							if($this->input->post('fav_icon_gambar')!=null){unlink($file['fav_icon']['file_path'].$this->input->post('fav_icon_gambar'));}
							$this->m_crud->update_data($table, array(
								'title' => $_POST['title'],
								'logo' => $file['logo']['file_name'],
								'fav_icon' => $file['fav_icon']['file_name'],
								'meta_key' => $_POST['meta_key'],
								'meta_descr' => $_POST['meta_descr']
							), "site_id = '".$_GET['trx']."'");
						} else {
							$this->m_crud->update_data($table, array(
								'title' => $_POST['title'],
								'meta_key' => $_POST['meta_key'],
								'meta_descr' => $_POST['meta_descr']
							), "site_id = '".$_GET['trx']."'");
						}
					} else {
						if($file['logo']['file_name']!=null&&$file['fav_icon']['file_name']!=null){
							$this->m_crud->create_data($table, array(
								'site_id' => $_POST['site_id'],
								'title' => $_POST['title'],
								'logo' => $file['logo']['file_name'],
								'fav_icon' => $file['fav_icon']['file_name'],
								'meta_key' => $_POST['meta_key'],
								'meta_descr' => $_POST['meta_descr']
							));
						} else {
							$this->m_crud->create_data($table, array(
								'site_id' => $_POST['site_id'],
								'title' => $_POST['title'],
								'logo' => '',
								'fav_icon' => '',
								'meta_key' => $_POST['meta_key'],
								'meta_descr' => $_POST['meta_descr']
							));
						}
					}
					if($valid == true){ echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>'; }
				}
			}
			$this->load->view('user/content', $data);
		}
	}
	
	
	public function user_level($action = null){
		$this->access_denied(1);
		$data = $this->data;
		$function = 'user_level';
		$table = 'user_lvl';
		$view = 'user/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'User Level';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data['coa_group'] = array();
		$this->form_validation->set_rules('nama', 'Name', 'trim|required', array('required' => '%s don`t empty'));
		if($action == 'access' && isset($_GET['trx'])){
			$access = $this->db->query("select * from user_lvl where id = '".$_GET['trx']."';");
			$data['access'] = $access->row();
			$data['content'] = $view.'access_'.$function;
		} else if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data($table, "id = '".$_GET['trx']."'");
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['user_level'] = $this->m_crud->get_data($table, '*', "id = '".$_GET['trx']."'", 'id asc');
		} else { $data['user_level'] = $this->m_crud->read_data($table, '*', "id <> 1", 'id asc'); }
		if($action == "add" || $action == "edit"){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){
				if(isset($_POST['update'])){
					$this->m_crud->update_data($table, array(
						'lvl' => $_POST['nama']
					), "id = '".$_GET['trx']."'");
				} else {
					$this->m_crud->create_data($table, array(
						'lvl' => $_POST['nama'],
						'access' => 0
					));
				}
				echo '<script>alert("Data has been Saved");window.location="'.$function.'"</script>';
			}
			$this->load->view('bo/index', $data);
		}
	}
	
	public function user_level_edit(){
		$this->access_denied(1);
		$id = $this->input->post('id');
		$new = null;
		for ($i=0;$i<=$this->input->post('jumlah');$i++){
			$access = $this->input->post('access-'.$i);	
			if(empty($access)){
				$new = $new .= '0';
			}else{
				$new = $new .= $access;
			}
		}
	
		if($this->m_website->edit_access_user($id, $new) === true){
			$data['isi'] = "User Access has Changed.";
			$data['aksi'] = "window.location.href = '".base_url()."user/user-level';";
			$this->load->view('site/header_bootbox');
			$this->load->view('site/bootbox/alert', $data);
		}else{
			$data['isi'] = "Error. Please try again.";
			$data['aksi'] = "window.location.href = '".base_url()."user/user-level';";
			$this->load->view('site/header_bootbox');
			$this->load->view('site/bootbox/alert', $data);
		}
	}
	
	
	public function check_username($str){ //check po_number di sales_order
		$check = $this->m_crud->get_data('user_akun', 'username', "username = '".$str."'");
		if($str!=$check['username']){ return true; } else{ $this->form_validation->set_message('check_username', 'Duplicate %s'); return false; }
	}
	
	public function user_list($action = null){
		$this->access_denied(2);
		$data = $this->data;
		$function = 'user_list';
		$table = 'user_akun';
		$view = 'user/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'User List';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data['coa_group'] = array();
		$this->form_validation->set_rules('username', 'Username', 'trim|required|callback_check_username', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('password', 'Password', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('nama', 'Name', 'trim|required', array('required' => '%s don`t empty'));
		if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data('user_detail', "user_id = '".$_GET['trx']."'");
			$this->m_crud->delete_data($table, "user_id = '".$_GET['trx']."'");
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['user_list'] = $this->m_crud->join_data($table.' as akun', '*, lev.lvl as level', array('user_detail as det', 'user_lvl as lev'), array("akun.user_id = det.user_id", "akun.user_lvl = lev.id"), "akun.user_id = '".$_GET['trx']."'")[0];
		} else { $data['user_list'] = $this->m_crud->join_data($table.' as akun', '*, lev.lvl as level', array('user_detail as det', 'user_lvl as lev'), array("akun.user_id = det.user_id", "akun.user_lvl = lev.id"), "akun.user_lvl <> 1", 'det.nama asc'); }
		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){
				if(isset($_POST['update'])){
					$change_password = $this->m_crud->get_data($table, 'user_id', "user_id = '".$_POST['user_id']."' and password = '".$_POST['password']."'");
					if($change_password == null){
						$this->m_crud->update_data($table, array(
							'user_id' => $_POST['user_id'],
							'username'  => $_POST['username'],
							'password'  => md5($_POST['password']),
							'user_lvl'  => $_POST['user_lvl']
						), "user_id = '".$_GET['trx']."'");
					}else{
						$this->m_crud->update_data($table, array(
							'user_id' => $_POST['user_id'],
							'username'  => $_POST['username'],
							'user_lvl'  => $_POST['user_lvl']
						), "user_id = '".$_GET['trx']."'");
					}
					$this->m_crud->update_data('user_detail', array(
						'user_id' => $_POST['user_id'],
						'nama'  => $_POST['nama'],
						'alamat'  => $_POST['alamat'],
						'email'  => $_POST['email'],
						'nohp'  => $_POST['nohp']
					), "user_id = '".$_GET['trx']."'");
				} else {
					$this->m_crud->create_data($table, array(
						'user_id' => $_POST['user_id'],
						'username'  => $_POST['username'],
						'password'  => md5($_POST['password']),
						'user_lvl'  => $_POST['user_lvl']
					));
					$this->m_crud->create_data('user_detail', array(
						'user_id' => $_POST['user_id'],
						'nama'  => $_POST['nama'],
						'alamat'  => $_POST['alamat'],
						'email'  => $_POST['email'],
						'nohp'  => $_POST['nohp']
					));
				}
				echo '<script>alert("Data has been Saved");window.location="'.$function.'"</script>';
			}
			$this->load->view('user/content', $data);
		}
	}
	
	
}

