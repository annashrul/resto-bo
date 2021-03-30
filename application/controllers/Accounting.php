<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accounting extends CI_Controller {

	//public $user = null;

	public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $site_data = $this->m_website->site_data();
        $this->site = str_replace(' ', '', strtolower($site_data->title));
        $this->control = 'Accounting';

        $this->user = $this->session->userdata($this->site . 'user');
        $this->username = $this->session->userdata($this->site . 'username');
        $this->menu_group = $this->m_crud->get_data('Setting', 'as_deskripsi, as_group1, as_group2', "Kode = '1111'");

        /*Session lokasi*/
        $lok = $this->session->userdata($this->site.'lokasi');
        $lokasi_in = array();
        foreach ($lok as $item) {
            array_push($lokasi_in, '\''.$item['kode'].'\'');
        }

        $this->lokasi_in = implode(',', $lokasi_in);
        $this->where_lokasi = "Kode in (".$this->lokasi_in.")";
        /*End session lokasi*/

        $this->data = array(
            'site' => $site_data,
            'account' => $this->m_website->user_data($this->user),
            'access' => $this->m_website->user_access_data($this->user),
            'menu_group' => $this->menu_group
        );

        $this->output->set_header("Cache-Control: no-store, no-cache, max-age=0, post-check=0, pre-check=0");
	}
	
	public function delete_ajax($table, $column, $id){
		//$id = $_POST['delete_id'];
		//$this->m_crud->delete_data($table, $column." = '".$id."'");
		echo json_encode(array('status'=>true));
	}
	
	public function trx_number(){
		$table		= $_GET['table']; 
		$column		= $_GET['column']; 
		$trx		= $_GET['trx']; 
		$tanggal	= $_GET['tanggal']; 
		$digit_seri	= $_GET['digit_seri'];
		$seri = (int) $this->m_crud->get_data($table, "max(substring(".$column.", ".(strlen($trx.$tanggal)+1).", ".$digit_seri.")) as id", $column." like '%".$trx.$tanggal."%'")['id'];
		$seri++; $seri = str_pad($seri, $digit_seri, '0', STR_PAD_LEFT);
		echo $trx.$tanggal.$seri;
	}
	
	public function trx_number_2(){
		$table		= $_GET['table']; 
		$column		= $_GET['column']; 
		$trx		= $_GET['trx']; 
		$coa		= $_GET['coa']; 
		$tanggal	= $_GET['tanggal']; 
		$digit_seri	= $_GET['digit_seri'];
		$seri = (int) $this->m_crud->get_data($table, "max(substring(".$column.", ".(strlen($trx.$coa.$tanggal)+1).", ".$digit_seri.")) as id", $column." like '%".$trx.$coa.$tanggal."%'")['id'];
		$seri++; $seri = str_pad($seri, $digit_seri, '0', STR_PAD_LEFT);
		echo $trx.$coa.$tanggal.$seri;
	}
	
	public function nojs(){
		$data = $this->data;
		$data['title'] = 'Javascript Required';
		$data['redirect'] = base_url();
		$this->load->view('site/nojs');		
	}

	public function index(){
		redirect(base_url());
	}

    function access_denied($str){
        if(substr($this->m_website->user_access_data($this->user)->access,$str,1) == 0){
            echo "<script>alert('Access Denied'); window.location='".base_url()."site';</script>";
        }
    }
	
	public function account_category($action = null){
		$this->access_denied(3);
		$data = $this->data;
		$function = 'account_category';
		$table = 'coa_kategori';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Account Category';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data['coa_group'] = array();
		$this->form_validation->set_rules('code', 'Code', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('nama', 'Name', 'trim|required', array('required' => '%s don`t empty'));
		/*if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data($table, "kategori_id = '".$_GET['trx']."'");
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['coa_kategori'] = $this->m_crud->get_data('coa_kategori', '*', "kategori_id = '".$_GET['trx']."'", 'kategori_id asc');
		} else*/ { $data['coa_kategori'] = $this->m_crud->read_data('coa_kategori', '*', null, 'kategori_id asc'); }
		if($action == "add" || $action == "edit"){ $data['content'] = $view.'form_'.$function; }
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['save'])){
				if(isset($_POST['update'])){
					$this->m_crud->update_data('coa_kategori', array(
						'kategori_id' => $_POST['code'],
						'nama' => $_POST['nama']
					), "kategori_id = '".$_GET['trx']."'");
				} else {
					$this->m_crud->create_data('coa_kategori', array(
						'kategori_id' => $_POST['code'],
						'nama' => $_POST['nama']
					));
				}
				echo '<script>alert("Data has been Saved");window.location="'.$function.'"</script>';
			}
			$this->load->view('user/content', $data);
		}
	}
	
	public function account_group($action = null){
		$this->access_denied(4);
		$data = $this->data;
		$function = 'account_group';
		$table = 'coa_group';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Account Group';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data['coa_group'] = array();
		$this->form_validation->set_rules('code', 'Code', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('nama', 'Name', 'trim|required', array('required' => '%s don`t empty'));
		/*if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data($table, "group_id = '".$_GET['trx']."'");
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['coa_group'] = $this->m_crud->join_data('coa_group as grp', 'group_id, grp.nama as group_nama, kat.nama as kat_nama, kat.kategori_id as kat_id', 'coa_kategori as kat', 'grp.kategori_id = kat.kategori_id', "group_id = '".$_GET['trx']."'", 'grp.kategori_id asc')[0];
		} else*/ { $data['coa_group'] = $this->m_crud->join_data('coa_group as grp', 'group_id, grp.nama as group_nama, kat.nama as kat_nama, kat.kategori_id as kat_id', 'coa_kategori as kat', 'grp.kategori_id = kat.kategori_id', null, 'grp.kategori_id asc'); }
		if($action == "add" || $action == "edit"){ $data['content'] = $view.'form_'.$function; }
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['save'])){
				if(isset($_POST['update'])){
					$this->m_crud->update_data('coa_group', array(
						'kategori_id' => $_POST['kategori'],
						'group_id' => $_POST['code'],
						'nama' => $_POST['nama']
					), "group_id = '".$_GET['trx']."'");
				} else {
					$this->m_crud->create_data('coa_group', array(
						'kategori_id' => $_POST['kategori'],
						'group_id' => $_POST['code'],
						'nama' => $_POST['nama']
					));
				}
				echo '<script>alert("Data has been Saved");window.location="'.$function.'"</script>';
			}
			$this->load->view('user/content', $data);
		}
	}
	
	public function code_of_account($action = null){	
		$this->access_denied(5);
		$data = $this->data;
		$function = 'code_of_account';
		$table = 'coa';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Code of Account';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data['coa'] = array();
		$this->form_validation->set_rules('code', 'Code', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('nama', 'Name', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('balance', 'Balance', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('jenis', 'Type', 'trim|required', array('required' => '%s don`t empty'));
		if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data($table, "coa_id = '".$_GET['trx']."'");
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['coa'] = $this->m_crud->join_data('coa', 'coa_id, coa_group.nama as nama_group, coa.nama as nama, coa.group_id as group_id, balance, jenis, currency, kurs.nama as nama_kurs', array('coa_group', 'acc_kurs_uang as kurs'), array('coa.group_id = coa_group.group_id', 'coa.currency = kurs.id_kurs_uang'), "coa_id = '".$_GET['trx']."'", 'coa_id asc')[0];
		} else { $data['coa'] = $this->m_crud->join_data('coa', 'coa_id, coa_group.nama as nama_group, coa.nama as nama, coa.group_id as group_id, balance, jenis, currency, kurs.nama as nama_kurs', array('coa_group', 'acc_kurs_uang as kurs'), array('coa.group_id = coa_group.group_id', 'coa.currency = kurs.id_kurs_uang'), null, 'coa_id asc'); }
		if($action == "add" || $action == "edit"){ $data['content'] = $view.'form_'.$function; }
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['save'])){
				if(isset($_POST['update'])){
					$this->m_crud->update_data('coa', array(
						'coa_id' => $_POST['code'],
						'group_id' => $_POST['group'],
						'nama' => $_POST['nama'],
						'balance' => $_POST['balance'],
						'jenis' => $_POST['jenis'],
						'currency' => $_POST['currency']
					), "coa_id = '".$_GET['trx']."'");
				} else {
					$this->m_crud->create_data('coa', array(
						'coa_id' => $_POST['code'],
						'group_id' => $_POST['group'],
						'nama' => $_POST['nama'],
						'balance' => $_POST['balance'],
						'jenis' => $_POST['jenis'],
						'currency' => $_POST['currency']
					));
				}
				echo '<script>alert("Data has been Saved");window.location="'.$function.'"</script>';
			}
			$this->load->view('user/content', $data);
		}
	}
	
	public function set_periode(){
		$this->access_denied(6);
		$data = $this->data;
		$function = 'set_periode';
		$table = 'acc_periode';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Set Periode';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('jenis', 'Type', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('tgl_awal', 'From Date', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('tgl_akhir', 'To Date', 'trim|required', array('required' => '%s don`t empty'));
		$data[$function] = $this->m_crud->get_data($table, '*', "status = 2 and lokasi = '".$this->m_website->get_lokasi()."'");
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['save'])){
				//$beginning = $this->m_website->selisih_hari($this->input->post('tgl_awal'), $this->input->post('beginning'));
				$beginning = $this->m_website->selisih_hari($this->input->post('tgl_awal'), $this->m_crud->max_data($table, 'tanggal_akhir', "status = 4 and lokasi = '".$this->m_website->get_lokasi()."'"));
				$periode = $this->m_website->selisih_hari($this->input->post('tgl_akhir'), $this->input->post('tgl_awal'));
				if($beginning <= 0){
					//echo '<script>alert("From Date Periode must be higher from Beginning Periode");</script>';
					echo '<script>alert("From Date Periode must be higher from Last Periode");</script>';
				} else if($periode <= 0){
					echo '<script>alert("To Date must be higher from From Date Periode");</script>';
				}else { 
					/*if($data['beginning'] == null){
						$this->m_crud->create_data($table, array(
							'id_periode' => 1,
							'jenis' => $this->input->post('jenis'),
							'tanggal_awal' => $this->input->post('beginning'),
							'tanggal_akhir' => $this->input->post('beginning')
						));
					} else {
						$this->m_crud->update_data($table, array(
							'jenis' => $this->input->post('jenis'),
							'tanggal_awal' => $this->input->post('beginning'),
							'tanggal_akhir' => $this->input->post('beginning')
						), 'id_periode = 1');
					} */
					if($data[$function] == null){
						$this->m_crud->create_data($table, array(
							'jenis' => $this->input->post('jenis'),
							'tanggal_awal' => $this->input->post('tgl_awal'),
							'tanggal_akhir' => $this->input->post('tgl_akhir'),
							'status' => 2,
							'lokasi' => $this->m_website->get_lokasi()
						));
						$this->m_crud->create_data($table, array(
							'jenis' => $this->input->post('jenis'),
							'tanggal_awal' => $this->input->post('tgl_awal'),
							'tanggal_akhir' => $this->input->post('tgl_akhir'),
							'status' => 3,
							'lokasi' => $this->m_website->get_lokasi()
						));
					} else {
						$this->m_crud->update_data($table, array(
							'jenis' => $this->input->post('jenis'),
							'tanggal_awal' => $this->input->post('tgl_awal'),
							'tanggal_akhir' => $this->input->post('tgl_akhir')
						), 'id_periode = '.$data[$function]['id_periode']);
						$this->m_accounting->setting_periode('update', array(
							'jenis'=>$this->input->post('jenis'),
							'awal'=>$this->input->post('tgl_awal'),
							'akhir'=>$this->input->post('tgl_akhir'),
							'lokasi' => $this->m_website->get_lokasi()
						));
					}
					echo '<script>alert("Set Periode has been saved"); window.location = "'.$function.'";</script>';
				}
			}
			$this->load->view('user/content', $data); 
		}
	} 
	
	public function exchange_money($action = null){
		$this->access_denied(7);	
		$data = $this->data;
		$function = 'exchange_money';
		$table = 'acc_kurs_uang';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Exchange Money';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data['coa_group'] = array();
		$this->form_validation->set_rules('rate', 'Rate', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('nama', 'Name', 'trim|required', array('required' => '%s don`t empty'));
		if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data($table, "id_kurs_uang = '".$_GET['trx']."' and id_kurs_uang <> 1");
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['kurs'] = $this->m_crud->get_data($table, '*', "id_kurs_uang = '".$_GET['trx']."'", 'id_kurs_uang asc');
		} else { $data['kurs'] = $this->m_crud->read_data($table, '*', null, 'id_kurs_uang asc'); }
		if($action == "add" || $action == "edit"){ $data['content'] = $view.'form_'.$function; }
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['save'])){
				if(isset($_POST['update'])){
					$this->m_crud->update_data($table, array(
						'nama' => $_POST['nama'],
						'rate' => $_POST['rate']
					), "id_kurs_uang = '".$_GET['trx']."'");
				} else {
					$this->m_crud->create_data($table, array(
						'nama' => $_POST['nama'],
						'rate' => $_POST['rate']
					));
				}
				echo '<script>alert("Data has been Saved");window.location="'.$function.'"</script>';
			}
			$this->load->view('user/content', $data);
		}
	}
	
	public function beginning_balance($action = null){
		$this->access_denied(8);
		$data = $this->data;
		$function = 'beginning_balance';
		$table = 'acc_'.$function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Beginning Balance';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		if(isset($_POST['save'])){
			$balance = $this->m_crud->get_data($table, 'id_'.$function, array('coa' => $this->input->post('coa'), 'lokasi'=>$this->m_website->get_lokasi()));
			if($balance == null){
				$this->m_crud->create_data($table, array(
					'coa' => $this->input->post('coa'), 
					'balance' => $this->input->post('balance'),
					'rate' => $this->input->post('exchange'),
					'lokasi'=>$this->m_website->get_lokasi(),
					'status' => 0
				));
			} else {
				$this->m_crud->update_data($table, array(
					'coa' => $this->input->post('coa'), 
					'balance' => $this->input->post('balance'),
					'rate' => $this->input->post('exchange')
				), 'id_'.$function.' = '.$balance['id_'.$function]);
			}
			echo '<script>alert("Beginning Balance has Saved");window.location="'.$function.'"</script>';
		} else if(isset($_POST['fix'])) {
			if($_POST['debit'] == $_POST['credit']){
				$this->m_crud->update_data($table, array('status'=>1), "lokasi = '".$this->m_website->get_lokasi()."'");
				echo '<script>window.location="'.$function.'";</script>';
			} else {
				echo '<script>alert("Debit and Credit Must Balance");window.location="'.$function.'";</script>';
			}
				
		}
		/* $this->form_validation->set_rules('tgl_awal', 'From Date', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('tgl_akhir', 'To Date', 'trim|required', array('required' => '%s don`t empty'));
		if(isset($_POST['search'])){ 
			$where = 'tanggal >= "'.$this->input->post('tgl_awal').' 00:00:00" and tanggal <= "'.$this->input->post('tgl_akhir').' 23:59:59"'; 
		} else { $where = null; } */
		$data[$function] = $this->m_accounting->account('*', 'coa_kategori.kategori_id >= 11 and coa_kategori.kategori_id <= 31');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function general_journal(){
		$this->access_denied(9);
		$data = $this->data;
		$function = 'general_journal';
		$table = 'acc_'.$function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'General Journal';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('tgl_awal', 'From Date', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('tgl_akhir', 'To Date', 'trim|required', array('required' => '%s don`t empty'));
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			if($_POST['tgl_awal'] != null && $_POST['tgl_akhir'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00' and tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')";
			} else if($_POST['tgl_awal'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00')"; 
			} else if($_POST['tgl_akhir'] != null){ 
				$where = "(tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')"; 
			} else { $where = null; } 
		} else { 	
			$ongoing = $this->m_accounting->ongoing_periode(); 
			$where = "(tanggal >= '".substr($ongoing['tanggal_awal'], 0, 10)." 00:00:00' and tanggal <= '".substr($ongoing['tanggal_akhir'], 0, 10)." 23:59:59')"; 
		} 
		if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
		if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
		
		if(isset($_POST['to_excel'])){
			$baca = $this->m_crud->read_data($table, '*', $where, 'tanggal desc, id_'.$function.' asc');
			$debit = 0; $credit = 0;
			$i=0; foreach($baca as $row => $value){
				if($value['debit']!=0 || $value['credit']!=0){
					$body[$i] = array(
						$value['id_trx'], $value['descrip'], $value['tanggal'], $value['coa'], 
						$value['debit'] > 0 ? $this->m_accounting->coa($value['coa'], 'nama') : '', $value['credit'] > 0 ? $this->m_accounting->coa($value['coa'], 'nama') : '', 
						$value['debit'], $value['credit']
					); 
					$debit = $debit + $value['debit']; $credit = $credit + $value['credit'];
					$i++;
				}
			}
			$i++;
			$body[$i] = array('', '', '', '', '', '', $debit, $credit);
			$header = array(
				'merge' 	=> array('A1:H1','A2:H2','A3:H3', 'E5:F5'),
				'auto_size' => true,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:H5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => $_POST['tgl_awal'].' - '.$_POST['tgl_akhir']),
				'5' => array(
					'A' => 'Transaction', 'B' => 'Description', 'C' => 'Date', 'D' => 'Code', 'E' => 'Account Name',
					'G' => 'Debit', 'H' => 'Credit'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		$data[$function] = $this->m_crud->read_data($table, '*', $where, 'tanggal desc, id_'.$function.' asc');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function ledger(){
		$this->access_denied(10);
		$data = $this->data;
		$function = 'ledger';
		$table = 'acc_general_journal';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Ledger';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('tgl_awal', 'From Date', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('tgl_akhir', 'To Date', 'trim|required', array('required' => '%s don`t empty'));
		if(isset($_POST['to_excel'])){
			if($this->input->post('coa') != null){ $coa = "coa = '".$this->input->post('coa')."' and "; } else { $coa = null; }
			if(isset($_POST['currency']) && $_POST['currency']>1){ 
				$closing = 'id_trx not in (select id_periode from acc_periode where status = 4) and ';
				$currency = 'currency = '.(isset($_POST['currency'])?$_POST['currency']:$_GET['currency']).' and '; 
			} else { 
				$closing = null;
				$currency = null; 
			}
			if($_POST['tgl_awal'] != null && $_POST['tgl_akhir'] != null){ 
				$where = $closing.$coa.$currency."(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00' and tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')";
			} else if($_POST['tgl_awal'] != null){ 
				$where = $closing.$coa.$currency."(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00')"; 
			} else if($_POST['tgl_akhir'] != null){ 
				$where = $closing.$coa.$currency."(tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')"; 
			} else { 
				$periode = $this->m_accounting->ongoing_periode();
				$where = $coa.$currency."tanggal >= '".substr($periode['tanggal_awal'], 0, 10)." 00:00:00' and tanggal <= '".substr($periode['tanggal_akhir'], 0, 10)." 23:59:59'"; 
			}
			if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
			if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
			$baca = $this->m_crud->read_data($table, '*', $where, 'tanggal asc, id_general_journal asc');
			$adjustment = $this->m_crud->read_data('acc_adjustment_journal', '*', $where, 'tanggal asc, id_adjustment_journal asc');
			foreach($adjustment as $row){
				array_push($baca, $row);
			}
			$i=0; $debit = 0; $credit = 0; $balance = 0;
			foreach($baca as $row => $value){ 
				if(isset($_POST['currency']) && $_POST['currency']>1){ $rate = $value['rate']; } else { $rate = 1; }
				if($this->m_accounting->coa($this->input->post('coa'), 'balance') == "D"){ $balance = $balance + $value['debit'] - $value['credit']; }
				else { $balance = $balance + $value['credit'] - $value['debit']; }
				$debit = $debit + ($value['debit']/$rate); $credit = $credit + ($value['credit']/$rate);
				$body[$i] = array(
					$value['id_trx'], $value['tanggal'], $value['descrip'], $value['debit'] / $rate, $value['credit'] / $rate, 
					$balance / $rate
				);
				$i++;
			}
			$i++; $body[$i] = array('Total', '', '', $debit, $credit, $balance / $rate); 
			$beginning = 0; $balance = 0;
			if(isset($_POST['coa'])){ 
				if($this->m_accounting->coa($_POST['coa'], 'jenis') == 'Neraca'){
					if(isset($_POST['currency']) && $_POST['currency']>1){ 
						$beginning = $this->m_accounting->saldo_awal_asing($this->input->post('coa'), $this->input->post('tgl_awal')); 
					} else { 
						$beginning = $this->m_accounting->saldo_awal($this->input->post('coa'), $this->input->post('tgl_awal')); 
					}
				}
				if($this->m_accounting->coa($this->input->post('coa'), 'balance') == "D"){ $balance = $debit - $credit; }
				else { $balance = $credit - $debit; }
			}
			$i++; $body[$i] = array('Beginning Balance', '', '', $beginning);
			$i++; $body[$i] = array('Endinging Balance', '', '', $beginning + $balance);
			$i = $i + 7; $imin1 = $i-1;
			$header = array(
				'merge' 	=> array('A1:F1','A2:F2','A3:F3','A5:F5','D'.$imin1.':F'.$imin1, 'D'.$i.':F'.$i),
				'auto_size' => true,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:F5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana'),
						'A6:F6' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					), 
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => (($_POST['tgl_awal']=='2017-01-01'||$_POST['tgl_awal']==null)?'As of ':$_POST['tgl_awal'].' - ').$_POST['tgl_akhir']),
				'5' => array('A' => 'Account : '.$_POST['coa'].' - '.$this->m_accounting->coa($_POST['coa'], 'nama')),
				'6' => array(
					'A' => 'Transaction', 'B' => 'Date', 'C' => 'Description', 'D' => 'Debit', 'E' => 'Credit', 'F' => 'Balance'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		if(isset($_POST['search'])||isset($_GET['account'])){
			isset($_POST['coa'])?$account=$_POST['coa']:$account=$_GET['account'];
			isset($_POST['tgl_awal'])?$tgl_awal=$_POST['tgl_awal']:$tgl_awal=$_GET['from'];
			isset($_POST['tgl_akhir'])?$tgl_akhir=$_POST['tgl_akhir']:$tgl_akhir=$_GET['to'];
			
			if($account != null){ $coa = "coa = '".$account."' and "; } else { $coa = null; }
			if((isset($_GET['currency']) && $_GET['currency']>1)||(isset($_POST['currency']) && $_POST['currency']>1)){ 
				$currency = 'currency = '.(isset($_POST['currency'])?$_POST['currency']:$_GET['currency']).' and '; 
				$closing = 'id_trx not in (select id_periode from acc_periode where status = 4) and ';
			} else { 
				$currency = null; 
				$closing = null; 
			}
			if($tgl_awal != null || $tgl_akhir != null){
				$where = $closing.$coa.$currency."tanggal >= '".$tgl_awal." 00:00:00' and tanggal <= '".$tgl_akhir." 23:59:59'"; 
			} else { 
				$periode = $this->m_accounting->ongoing_periode();
				$where = $closing.$coa.$currency."tanggal >= '".substr($periode['tanggal_awal'], 0, 10)." 00:00:00' and tanggal <= '".substr($periode['tanggal_akhir'], 0, 10)." 23:59:59'";
			} 
			if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
			if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
			$data[$function] = $this->m_crud->read_data($table, '*', $where, 'tanggal asc, id_general_journal asc');
			$adjustment = $this->m_crud->read_data('acc_adjustment_journal', "*, 'accounting/adjustment_journal_report/detail/' as link_report", $where, 'tanggal asc, id_adjustment_journal asc');
			foreach($adjustment as $row){
				array_push($data[$function], $row);
			}
		} else { $where = null; }
		
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function cash_mutation($action = null, $id = null){
		$this->access_denied(11);
		$data = $this->data;
		$function = 'cash_mutation';
		$table = 'acc_'.$function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Cash Mutation';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('id_cm', 'Cash Mutation No.', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('currency', 'Currency', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('exchange', 'Exchange', 'trim|required|greater_than[0]', array('required' => '%s don`t empty'));
		/*if(isset($_POST['tambah'])){
			$this->form_validation->set_rules('coa', 'Account Name', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('descrip', 'Descrip', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('debit', 'Debit', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('credit', 'Credit', 'trim|required', array('required' => '%s don`t empty'));
		} else*/ if(isset($_POST['remove'])){
			$remove = $this->input->post('remove');
			for($i=1;$i<$this->input->post('jumlah');$i++){
				if(isset($remove[$i])){
					$this->cart->remove($remove[$i]); 
				}
			}
		}
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['tambah'])){
				for($i=1; $i<=$_POST['jumlah']; $i++){
					$item = array(
						'id'   		=> $i.$this->input->post('coa'.$i), //harus ada
						'qty'  		=> 1, //harus ada
						'price'   	=> 1, //harus ada
						'name'    	=> $this->input->post('coa'.$i), //harus ada
						'descrip'	=> $this->input->post('descrip'.$i),
						'debit'		=> $this->input->post('debit'.$i),
						'credit'	=> $this->input->post('credit'.$i)
					);
					$this->cart->insert($item);
				}
			} else if(isset($_POST['save'])){
				if($this->input->post('tot_debit') == $this->input->post('tot_credit')){
					$this->db->trans_begin();
					$this->m_crud->create_data($table, array(
						'id_cash_mutation' => $this->input->post('id_cm'),
						'currency' => $this->input->post('currency'),
						'rate' => $this->input->post('exchange'),
						'descrip' => $this->input->post('descrip'),
						'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
						'user_id' => $this->user,
						'lokasi' => $this->m_website->get_lokasi()
					));
					for($i=1; $i<=$_POST['jumlah']; $i++){
						$this->m_crud->create_data('acc_general_journal', array(
							'id_trx' => $this->input->post('id_cm'),
							'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
							'coa' => $this->input->post('coa'.$i),
							'descrip' => $this->input->post('descrip'.$i),
							'debit' => $this->input->post('debit'.$i)*$_POST['exchange'],
							'credit' => $this->input->post('credit'.$i)*$_POST['exchange'],
							'link_report' => 'accounting/cash_mutation_report/detail/',
							'currency' => $_POST['currency'], 
							'rate' => $this->input->post('exchange'),
							'lokasi' => $this->m_website->get_lokasi()
						));
					}
					if ($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
					} else {
						$this->db->trans_commit();
						$this->cart->destroy();
						echo '<script>alert("Cash Mutation has been saved");window.location = "'.$function.'";</script>';
					}
				} else { echo '<script>alert("Debit and Credit must balance")</script>'; }
			}
			$this->load->view('user/content', $data); 
		}
	}
	
	public function cash_mutation_report($action = null, $id = null){
		$this->access_denied(12);
		$data = $this->data;
		$function = 'cash_mutation_report';
		$table = 'acc_cash_mutation';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Cash Mutation Report';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('tgl_awal', 'From Date', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('tgl_akhir', 'To Date', 'trim|required', array('required' => '%s don`t empty'));
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			if($_POST['tgl_awal'] != null && $_POST['tgl_akhir'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00' and tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')";
			} else if($_POST['tgl_awal'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00')"; 
			} else if($_POST['tgl_akhir'] != null){ 
				$where = "(tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')"; 
			} else { $where = null; } 
		} else { 	
			$ongoing = $this->m_accounting->ongoing_periode(); 
			$where = "(tanggal >= '".substr($ongoing['tanggal_awal'], 0, 10)." 00:00:00' and tanggal <= '".substr($ongoing['tanggal_akhir'], 0, 10)." 23:59:59')"; 
		} 
		if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
		if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
		
		if(isset($_POST['to_excel'])){
			$baca = $this->m_crud->read_data($table, '*, (select sum(debit) from acc_general_journal where id_trx = id_cash_mutation) as total', $where, 'tanggal desc');
			$total=0;
			$i=0; foreach($baca as $row => $value){
				$body[$i] = array(
					$value['id_cash_mutation'], $value['tanggal'], $value['descrip'], $this->m_website->user_data($value['user_id'])->nama, 
					$value['total']
				); 
				$total = $total + $value['total'];
				$i++;
			}
			$i++;
			$body[$i] = array('', '', '', 'Total', $total);
			$header = array(
				'merge' 	=> array('A1:E1','A2:E2','A3:E3'),
				'auto_size' => true,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:E5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => $_POST['tgl_awal'].' - '.$_POST['tgl_akhir']),
				'5' => array(
					'A' => 'Cash Mutation No.', 'B' => 'Date', 'C' => 'Description', 'D' => 'User', 'E' => 'Total'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		if($action == 'print' && isset($_GET['trx'])){
			$data['content'] = $view.'invoice_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', "id_cash_mutation = '".$_GET['trx']."'");
		} else if($action == 'detail' && isset($_GET['trx'])){
			$data['content'] = $view.'detail_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', "id_cash_mutation = '".$_GET['trx']."'");
		} 
		$data[$function] = $this->m_crud->read_data($table, '*, (select sum(debit) from acc_general_journal where id_trx = id_cash_mutation) as total', $where, 'tanggal desc');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function bank_voucher($action = null, $id = null){
		$this->access_denied(13);
		$data = $this->data;
		$function = 'bank_voucher';
		$table = 'acc_'.$function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Bank Voucher';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('id_cv', 'Bank Voucher No.', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('receiver', 'Receiver', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('currency', 'Currency', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('exchange', 'Exchange', 'trim|required|greater_than[0]', array('required' => '%s don`t empty'));
		/*if(isset($_POST['tambah'])){
			$this->form_validation->set_rules('coa', 'Account Name', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('descrip', 'Descrip', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('debit', 'Debit', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('credit', 'Credit', 'trim|required', array('required' => '%s don`t empty'));
		} else*/ if(isset($_POST['remove'])){
			$remove = $this->input->post('remove');
			for($i=1;$i<$this->input->post('jumlah');$i++){
				if(isset($remove[$i])){
					$this->cart->remove($remove[$i]); 
				}
			}
		}
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['tambah'])){
				for($i=1; $i<=$_POST['jumlah']; $i++){
					$item = array(
						'id'   		=> $this->input->post('coa'.$i), //harus ada
						'qty'  		=> 1, //harus ada
						'price'   	=> 1, //harus ada
						'name'    	=> $this->m_accounting->coa($this->input->post('coa'.$i), 'nama'), //harus ada
						'descrip'	=> $this->input->post('descrip'.$i),
						'debit'		=> $this->input->post('debit'.$i)
					);
					$this->cart->insert($item);
				}
			} else if(isset($_POST['save']) && $_POST['tot_debit'] > 0){
				$this->db->trans_begin();
				$this->m_crud->create_data($table, array(
					'id_bank_voucher' => $this->input->post('id_cv'),
					'descrip' => $this->input->post('descrip'),
					'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
					'currency' => $this->input->post('currency'),
					'rate' => $this->input->post('exchange'),
					'coa' 	=> $this->input->post('coa'),
					'penerima' 	=> $this->input->post('receiver'),
					'user_id' => $this->user,
					'lokasi' => $this->m_website->get_lokasi()
				));
				$this->m_crud->create_data('acc_general_journal', array(
					'id_trx' => $this->input->post('id_cv'),
					'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
					'coa' => $this->input->post('coa'),
					'descrip' => $this->input->post('descrip'),
					'debit' => 0 * $_POST['exchange'],
					'credit' => $this->input->post('tot_debit')*$_POST['exchange'],
					'link_report' => 'accounting/bank_voucher_report/detail/',
					'currency' => $_POST['currency'],
					'rate' => $this->input->post('exchange'),
					'lokasi' => $this->m_website->get_lokasi()
				));
				for($i=1; $i<=$_POST['jumlah']; $i++){
					$this->m_crud->create_data('acc_general_journal', array(
						'id_trx' => $this->input->post('id_cv'),
						'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
						'coa' => $this->input->post('coa'.$i),
						'descrip' => $this->input->post('descrip'.$i),
						'debit' => $this->input->post('debit'.$i) * $_POST['exchange'],
						'credit' => 0 * $_POST['exchange'],
						'link_report' => 'accounting/bank_voucher_report/detail/',
						'currency' => $_POST['currency'],
						'rate' => $this->input->post('exchange'),
						'lokasi' => $this->m_website->get_lokasi()
					));
				}
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
				} else {
					$this->db->trans_commit();
					$this->cart->destroy();
					echo '<script>alert("Bank Voucher has been saved");window.location = "'.$function.'";</script>';
				}
			}
			$this->load->view('user/content', $data); 
		}
	}
	
	public function bank_voucher_report($action = null, $id = null){
		$this->access_denied(14);
		$data = $this->data;
		$function = 'bank_voucher_report';
		$table = 'acc_bank_voucher';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Bank Voucher Report';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('tgl_awal', 'From Date', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('tgl_akhir', 'To Date', 'trim|required', array('required' => '%s don`t empty'));
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			if($_POST['tgl_awal'] != null && $_POST['tgl_akhir'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00' and tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')";
			} else if($_POST['tgl_awal'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00')"; 
			} else if($_POST['tgl_akhir'] != null){ 
				$where = "(tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')"; 
			} else { $where = null; } 
		} else { 	
			$ongoing = $this->m_accounting->ongoing_periode(); 
			$where = "(tanggal >= '".substr($ongoing['tanggal_awal'], 0, 10)." 00:00:00' and tanggal <= '".substr($ongoing['tanggal_akhir'], 0, 10)." 23:59:59')"; 
		} 
		if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
		if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
		if(isset($_POST['to_excel'])){
			$baca = $this->m_crud->read_data($table, '*, (select sum(debit) from acc_general_journal where id_trx = id_bank_voucher) as total', $where, 'tanggal desc');
			$total=0;
			$i=0; foreach($baca as $row => $value){
				$body[$i] = array(
					$value['id_bank_voucher'], $value['tanggal'], $value['coa'].'-'.$this->m_accounting->coa($value['coa'], 'nama'),
					$value['descrip'], $value['penerima'], $this->m_website->user_data($value['user_id'])->nama, 
					$value['total']
				); 
				$total = $total + $value['total'];
				$i++;
			}
			$i++;
			$body[$i] = array('', '', '', '', '', 'Total', $total);
			$header = array(
				'merge' 	=> array('A1:G1','A2:G2','A3:G3'),
				'auto_size' => true,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:G5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => $_POST['tgl_awal'].' - '.$_POST['tgl_akhir']),
				'5' => array(
					'A' => 'Bank Voucher No.', 'B' => 'Date', 'C' => 'Account', 'D' => 'Description', 'E' => 'Receiver', 
					'F' => 'User', 'G' => 'Total'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		if($action == 'print' && isset($_GET['trx'])){
			$data['content'] = $view.'invoice_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', "id_bank_voucher = '".$_GET['trx']."'");
		} if($action == 'detail' && isset($_GET['trx'])){
			$data['content'] = $view.'detail_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', "id_bank_voucher = '".$_GET['trx']."'");
		} 
		$data[$function] = $this->m_crud->read_data($table, '*, (select sum(debit) from acc_general_journal where id_trx = id_bank_voucher) as total', $where, 'tanggal desc');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function cash_voucher($action = null, $id = null){
		$this->access_denied(15);
		$data = $this->data;
		$function = 'cash_voucher';
		$table = 'acc_'.$function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Cash Voucher';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('id_cv', 'Cash Voucher No.', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('receiver', 'Receiver', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('currency', 'Currency', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('exchange', 'Exchange', 'trim|required|greater_than[0]', array('required' => '%s don`t empty'));
		/*if(isset($_POST['tambah'])){
			$this->form_validation->set_rules('coa', 'Account Name', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('descrip', 'Descrip', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('debit', 'Debit', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('credit', 'Credit', 'trim|required', array('required' => '%s don`t empty'));
		} else*/ if(isset($_POST['remove'])){
			$remove = $this->input->post('remove');
			for($i=1;$i<$this->input->post('jumlah');$i++){
				if(isset($remove[$i])){
					$this->cart->remove($remove[$i]); 
				}
			}
		}
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['tambah'])){
				for($i=1; $i<=$_POST['jumlah']; $i++){
					$item = array(
						'id'   		=> $this->input->post('coa'.$i), //harus ada
						'qty'  		=> 1, //harus ada
						'price'   	=> 1, //harus ada
						'name'    	=> $this->m_accounting->coa($this->input->post('coa'.$i), 'nama'), //harus ada
						'descrip'	=> $this->input->post('descrip'.$i),
						'debit'		=> $this->input->post('debit'.$i)
					);
					$this->cart->insert($item);
				}
			} else if(isset($_POST['save']) && $_POST['tot_debit'] > 0){
				$this->db->trans_begin();
				$this->m_crud->create_data($table, array(
					'id_cash_voucher' => $this->input->post('id_cv'),
					'descrip' => $this->input->post('descrip'),
					'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
					'currency' => $this->input->post('currency'),
					'rate' => $this->input->post('exchange'),
					'coa' 	=> $this->input->post('coa'),
					'penerima' 	=> $this->input->post('receiver'),
					'user_id' => $this->user,
					'lokasi' => $this->m_website->get_lokasi()
				));
				$this->m_crud->create_data('acc_general_journal', array(
					'id_trx' => $this->input->post('id_cv'),
					'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
					'coa' => $this->input->post('coa'),
					'descrip' => $this->input->post('descrip'),
					'debit' => 0 * $_POST['exchange'],
					'credit' => $this->input->post('tot_debit')*$_POST['exchange'],
					'link_report' => 'accounting/cash_voucher_report/detail/',
					'currency' => $_POST['currency'],
					'rate' => $this->input->post('exchange'),
					'lokasi' => $this->m_website->get_lokasi()
				));
				for($i=1; $i<=$_POST['jumlah']; $i++){
					$this->m_crud->create_data('acc_general_journal', array(
						'id_trx' => $this->input->post('id_cv'),
						'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
						'coa' => $this->input->post('coa'.$i),
						'descrip' => $this->input->post('descrip'.$i),
						'debit' => $this->input->post('debit'.$i) * $_POST['exchange'],
						'credit' => 0 * $_POST['exchange'],
						'link_report' => 'accounting/cash_voucher_report/detail/',
						'currency' => $_POST['currency'],
						'rate' => $this->input->post('exchange'),
						'lokasi' => $this->m_website->get_lokasi()
					));
				}
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
				} else {
					$this->db->trans_commit();
					$this->cart->destroy();
					echo '<script>alert("Cash Voucher has been saved");window.location = "'.$function.'";</script>';
				}
			}
			$this->load->view('user/content', $data); 
		}
	}
	
	public function cash_voucher_report($action = null, $id = null){
		$this->access_denied(16);
		$data = $this->data;
		$function = 'cash_voucher_report';
		$table = 'acc_cash_voucher';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Cash Voucher Report';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('tgl_awal', 'From Date', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('tgl_akhir', 'To Date', 'trim|required', array('required' => '%s don`t empty'));
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			if($_POST['tgl_awal'] != null && $_POST['tgl_akhir'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00' and tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')";
			} else if($_POST['tgl_awal'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00')"; 
			} else if($_POST['tgl_akhir'] != null){ 
				$where = "(tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')"; 
			} else { $where = null; } 
		} else { 	
			$ongoing = $this->m_accounting->ongoing_periode(); 
			$where = "(tanggal >= '".substr($ongoing['tanggal_awal'], 0, 10)." 00:00:00' and tanggal <= '".substr($ongoing['tanggal_akhir'], 0, 10)." 23:59:59')"; 
		} 
		if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
		if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
		if(isset($_POST['to_excel'])){
			$baca = $this->m_crud->read_data($table, '*, (select sum(debit) from acc_general_journal where id_trx = id_cash_voucher) as total', $where, 'tanggal desc');
			$total=0;
			$i=0; foreach($baca as $row => $value){
				$body[$i] = array(
					$value['id_cash_voucher'], $value['tanggal'], $value['coa'].'-'.$this->m_accounting->coa($value['coa'], 'nama'),
					$value['descrip'], $value['penerima'], $this->m_website->user_data($value['user_id'])->nama, 
					$value['total']
				); 
				$total = $total + $value['total'];
				$i++;
			}
			$i++;
			$body[$i] = array('', '', '', '', '', 'Total', $total);
			$header = array(
				'merge' 	=> array('A1:G1','A2:G2','A3:G3'),
				'auto_size' => true,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:G5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => $_POST['tgl_awal'].' - '.$_POST['tgl_akhir']),
				'5' => array(
					'A' => 'Cash Voucher No.', 'B' => 'Date', 'C' => 'Account', 'D' => 'Description', 'E' => 'Receiver', 
					'F' => 'User', 'G' => 'Total'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		if($action == 'print' && isset($_GET['trx'])){
			$data['content'] = $view.'invoice_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', "id_cash_voucher = '".$_GET['trx']."'");
		} if($action == 'detail' && isset($_GET['trx'])){
			$data['content'] = $view.'detail_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', "id_cash_voucher = '".$_GET['trx']."'");
		} 
		$data[$function] = $this->m_crud->read_data($table, '*, (select sum(debit) from acc_general_journal where id_trx = id_cash_voucher) as total', $where, 'tanggal desc');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function tico_voucher($action = null, $id = null){
		$this->access_denied(17);
		$data = $this->data;
		$function = 'tico_voucher';
		$table = 'acc_'.$function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Tico Voucher';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('id_tv', 'Tico Voucher No.', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('receiver', 'Receiver', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('currency', 'Currency', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('exchange', 'Exchange', 'trim|required|greater_than[0]', array('required' => '%s don`t empty'));
		/*if(isset($_POST['tambah'])){
			$this->form_validation->set_rules('coa', 'Account Name', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('descrip', 'Descrip', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('debit', 'Debit', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('credit', 'Credit', 'trim|required', array('required' => '%s don`t empty'));
		} else*/ if(isset($_POST['remove'])){
			$remove = $this->input->post('remove');
			for($i=1;$i<$this->input->post('jumlah');$i++){
				if(isset($remove[$i])){
					$this->cart->remove($remove[$i]); 
				}
			}
		}
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['tambah'])){
				for($i=1; $i<=$_POST['jumlah']; $i++){
					$item = array(
						'id'   		=> $this->input->post('coa'.$i), //harus ada
						'qty'  		=> 1, //harus ada
						'price'   	=> 1, //harus ada
						'name'    	=> $this->m_accounting->coa($this->input->post('coa'.$i), 'nama'), //harus ada
						'descrip'	=> $this->input->post('descrip'.$i),
						'debit'		=> $this->input->post('debit'.$i)
					);
					$this->cart->insert($item);
				}
			} else if(isset($_POST['save']) && $_POST['tot_debit'] > 0){
				$this->db->trans_begin();
				$this->m_crud->create_data($table, array(
					'id_tico_voucher' => $this->input->post('id_tv'),
					'descrip' => $this->input->post('descrip'),
					'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
					'currency' => $this->input->post('currency'),
					'rate' => $this->input->post('exchange'),
					'coa' 	=> $this->input->post('coa'),
					'penerima' 	=> $this->input->post('receiver'),
					'user_id' => $this->user,
					'lokasi' => $this->m_website->get_lokasi()
				));
				$this->m_crud->create_data('acc_general_journal', array(
					'id_trx' => $this->input->post('id_tv'),
					'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
					'coa' => $this->input->post('coa'),
					'descrip' => $this->input->post('descrip'),
					'credit' => $this->input->post('tot_debit')*$_POST['exchange'],
					'link_report' => 'accounting/tico_voucher_report/detail/',
					'currency' => $_POST['currency'],
					'rate' => $this->input->post('exchange'),
					'lokasi' => $this->m_website->get_lokasi()
				));
				for($i=1; $i<=$_POST['jumlah']; $i++){
					$this->m_crud->create_data('acc_general_journal', array(
						'id_trx' => $this->input->post('id_tv'),
						'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
						'coa' => $this->input->post('coa'.$i),
						'descrip' => $this->input->post('descrip'.$i),
						'debit' => $this->input->post('debit'.$i) * $_POST['exchange'],
						'link_report' => 'accounting/tico_voucher_report/detail/',
						'currency' => $_POST['currency'],
						'rate' => $this->input->post('exchange'),
						'lokasi' => $this->m_website->get_lokasi()
					));
				}
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
				} else {
					$this->db->trans_commit();
					$this->cart->destroy();
					echo '<script>alert("Tico Voucher has been saved");window.location = "'.$function.'";</script>';
				}
			}
			$this->load->view('user/content', $data); 
		}
	}
	
	public function tico_voucher_report($action = null, $id = null){
		$this->access_denied(18);
		$data = $this->data;
		$function = 'tico_voucher_report';
		$table = 'acc_tico_voucher';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Tico Voucher Report';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('tgl_awal', 'From Date', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('tgl_akhir', 'To Date', 'trim|required', array('required' => '%s don`t empty'));
		if(isset($_POST['to_excel'])){
			if($_POST['tgl_awal'] != null && $_POST['tgl_akhir'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00' and tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')";
			} else if($_POST['tgl_awal'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00')"; 
			} else if($_POST['tgl_akhir'] != null){ 
				$where = "(tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')"; 
			} else { $where = null; }
			if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
			if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
			$baca = $this->m_crud->read_data($table, '*, (select sum(debit) from acc_general_journal where id_trx = id_tico_voucher) as total', $where, 'tanggal desc');
			$total=0;
			$i=0; foreach($baca as $row => $value){
				$body[$i] = array(
					$value['id_tico_voucher'], $value['tanggal'], $value['coa'].'-'.$this->m_accounting->coa($value['coa'], 'nama'),
					$value['descrip'], $value['penerima'], $this->m_website->user_data($value['user_id'])->nama, 
					$value['total']
				); 
				$total = $total + $value['total'];
				$i++;
			}
			$i++;
			$body[$i] = array('', '', '', '', '', 'Total', $total);
			$header = array(
				'merge' 	=> array('A1:G1','A2:G2','A3:G3'),
				'auto_size' => true,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:G5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => $_POST['tgl_awal'].' - '.$_POST['tgl_akhir']),
				'5' => array(
					'A' => 'Tico Voucher No.', 'B' => 'Date', 'C' => 'Account', 'D' => 'Description', 'E' => 'Receiver', 
					'F' => 'User', 'G' => 'Total'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		if($action == 'print' && isset($_GET['trx'])){
			$data['content'] = $view.'invoice_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', 'id_tico_voucher = "'.$_GET['trx'].'"');
		} else if($action == 'detail' && isset($_GET['trx'])){
			$data['content'] = $view.'detail_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', 'id_tico_voucher = "'.$_GET['trx'].'"');
		} 
		if(isset($_POST['search'])){ 
			$where = 'tanggal >= "'.$this->input->post('tgl_awal').' 00:00:00" and tanggal <= "'.$this->input->post('tgl_akhir').' 23:59:59"'; 
		} else { $where = null; }
		if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
		if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
		$data[$function] = $this->m_crud->read_data($table, '*, (select sum(debit) from acc_general_journal where id_trx = id_tico_voucher) as total', $where, 'tanggal desc');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function fixed_asset($action = null, $id = null){
		$this->access_denied(19);
		$data = $this->data;
		$function = 'fixed_asset';
		$table = 'acc_'.$function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Fixed Asset';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('coa', 'Asset Account', 'trim|required', array('required' => '%s don`t empty'));
		if(isset($_POST['to_excel'])){
			$where = null;
			if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
			if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
			$baca = $this->m_crud->read_data($table, '*', $where, 'tanggal desc');
			$i=0; $debit = 0; $credit = 0;
			foreach($baca as $row => $value){ 
				$body[$i] = array(
					$value['id_fixed_asset'], $this->m_accounting->coa($value['coa'],'nama'), $value['asset'], substr($value['tanggal'], 0, 10), $value['qty'],
					$this->cart->format_number($value['perolehan']), $value['estimasi'].' Month', $this->m_accounting->depr($value['qty']*$value['perolehan'], $value['estimasi'], $value['entry'])['susut'],
					$this->m_accounting->depr($value['qty']*$value['perolehan'], $value['estimasi'], $value['entry'], 'sisa'), $value['status']==1?'Active':'Not Active'
				);
				$i++;
			}
			$header = array(
				'merge' 	=> array('A1:J1','A2:J2','A3:J3'),
				'auto_size' => true,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:J5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => $_POST['tgl_akhir']),
				'5' => array(
					'A' => 'Asset Number', 'B' => 'Asset Account', 'C' => 'Asset', 'D' => 'Acquisition Date', 'E' => 'Qty', 'F' => 'Acquisition Value', 
					'G' => 'Estimated Life', 'H' => 'Accum Depr', 'I' => 'Value of Benefits', 'J' => 'Status'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		if($action == 'deactive' && $id != null){
			$this->m_crud->update_data($table, array('status' => 2), "id_fixed_asset = '".$id."'");
			echo '<script>window.location = "../";</script>';
		} else if($action == 'delete' && $id != null){
			$this->m_crud->delete_data($table, "id_fixed_asset = '".$id."' and entry <= 0");
			echo '<script>window.location = "../";</script>';
		}
		$where = null;
		if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
		if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
		$data[$function] = $this->m_crud->read_data($table, '*', $where, 'tanggal desc');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { 
			if(isset($_POST['save'])){
				$this->db->trans_begin();
				$this->m_crud->create_data($table, array(
					'id_fixed_asset' => $_POST['fix_no'],
					'coa' => $_POST['coa'],
					'tanggal' => $_POST['tanggal'],
					'asset' => $_POST['asset'],
					'qty' => $_POST['qty'],
					'perolehan' => $_POST['perolehan'],
					'estimasi' => $_POST['estimasi'],
					'accum' => $_POST['accum'],
					'expense' => $_POST['expense'],
					'status' => 1,
					'lokasi' => $this->m_website->get_lokasi(),
					'user_id' => $this->user
				));
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
				} else {
					$this->db->trans_commit();
					echo '<script>alert("Fixed Asset has been saved");window.location = "'.$function.'";</script>';
				}
			}
			$this->load->view('user/content', $data); 
		}
	}
	
	public function journal_entry($action = null, $id = null){
		$this->access_denied(20);
		$data = $this->data;
		$function = 'journal_entry';
		$table = 'acc_'.$function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Journal Entry';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('id_je', 'Journal Entry No.', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('currency', 'Currency', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('exchange', 'Exchange', 'trim|required|greater_than[0]', array('required' => '%s don`t empty'));
		/*if(isset($_POST['tambah'])){
			$this->form_validation->set_rules('coa', 'Account Name', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('descrip', 'Descrip', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('debit', 'Debit', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('credit', 'Credit', 'trim|required', array('required' => '%s don`t empty'));
		} else*/ if($action == 'delete' && $id != null){
			$this->cart->remove($id);
		}
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['tambah'])){
				for($i=1; $i<=$_POST['jumlah']; $i++){
					$item = array(
						'id'   		=> $this->input->post('coa'.$i), //harus ada
						'qty'  		=> 1, //harus ada
						'price'   	=> 1, //harus ada
						'name'    	=> $this->m_accounting->coa($this->input->post('coa'.$i), 'nama'), //harus ada
						'descrip'	=> $this->input->post('descrip'.$i),
						'debit'		=> $this->input->post('debit'.$i),
						'credit'	=> $this->input->post('credit'.$i)
					);
					$this->cart->insert($item);
				}
			} else if(isset($_POST['save'])){
				if($this->input->post('tot_debit') == $this->input->post('tot_credit')){
					$this->db->trans_begin();
					$this->m_crud->create_data($table, array(
						'id_journal_entry' => $this->input->post('id_je'),
						'currency' => $this->input->post('currency'),
						'rate' => $this->input->post('exchange'),
						'descrip' => $this->input->post('descrip'),
						'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
						'user_id' => $this->user,
						'lokasi' => $this->m_website->get_lokasi()
					));
					for($i=1; $i<=$_POST['jumlah']; $i++){
						$this->m_crud->create_data('acc_general_journal', array(
							'id_trx' => $this->input->post('id_je'),
							'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
							'coa' => $this->input->post('coa'.$i),
							'descrip' => $this->input->post('descrip'.$i),
							'link_report' => 'accounting/journal_entry_report/detail/',
							'debit' => $this->input->post('debit'.$i),
							'credit' => $this->input->post('credit'.$i),
							'currency' => $_POST['currency'],
							'rate' => $this->input->post('exchange'),
							'lokasi' => $this->m_website->get_lokasi()
						));
					}
					if ($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
					} else {
						$this->db->trans_commit();
						$this->cart->destroy();
						echo '<script>alert("Journal Entry has been saved");window.location = "'.$function.'";</script>';
					}
				} else { echo '<script>alert("Debit and Credit must balance")</script>'; }
			}
			$this->load->view('user/content', $data); 
		}
	}
	
	public function journal_entry_report($action = null, $id = null){
		$this->access_denied(21);
		$data = $this->data;
		$function = 'journal_entry_report';
		$table = 'acc_journal_entry';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Journal Entry Report';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('tgl_awal', 'From Date', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('tgl_akhir', 'To Date', 'trim|required', array('required' => '%s don`t empty'));
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			if($_POST['tgl_awal'] != null && $_POST['tgl_akhir'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00' and tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')";
			} else if($_POST['tgl_awal'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00')"; 
			} else if($_POST['tgl_akhir'] != null){ 
				$where = "(tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')"; 
			} else { $where = null; } 
		} else { 	
			$ongoing = $this->m_accounting->ongoing_periode(); 
			$where = "(tanggal >= '".substr($ongoing['tanggal_awal'], 0, 10)." 00:00:00' and tanggal <= '".substr($ongoing['tanggal_akhir'], 0, 10)." 23:59:59')"; 
		} 
		if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
		if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
		
		if(isset($_POST['to_excel'])){
			$baca = $this->m_crud->read_data($table, '*, (select sum(debit) from acc_general_journal where id_trx = id_journal_entry) as total', $where, 'tanggal desc');
			$total=0;
			$i=0; foreach($baca as $row => $value){
				$body[$i] = array(
					$value['id_journal_entry'], $value['tanggal'], $value['descrip'], $this->m_website->user_data($value['user_id'])->nama, 
					$value['total']
				); 
				$total = $total + $value['total'];
				$i++;
			}
			$i++;
			$body[$i] = array('', '', '', 'Total', $total);
			$header = array(
				'merge' 	=> array('A1:E1','A2:E2','A3:E3'),
				'auto_size' => true,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:E5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => $_POST['tgl_awal'].' - '.$_POST['tgl_akhir']),
				'5' => array(
					'A' => 'Journal Entry No.', 'B' => 'Date', 'C' => 'Description', 'D' => 'User', 'E' => 'Total'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		if($action == 'print' && isset($_GET['trx'])){
			$data['content'] = $view.'invoice_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', "id_journal_entry = '".$_GET['trx']."'");
		} else if($action == 'detail' && isset($_GET['trx'])){
			$data['content'] = $view.'detail_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', "id_journal_entry = '".$_GET['trx']."'");
		} 
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			if($_POST['tgl_awal'] != null && $_POST['tgl_akhir'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00' and tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')";
			} else if($_POST['tgl_awal'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00')"; 
			} else if($_POST['tgl_akhir'] != null){ 
				$where = "(tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')"; 
			} else { $where = null; } 
		} else { 	
			$ongoing = $this->m_accounting->ongoing_periode(); 
			$where = "(tanggal >= '".substr($ongoing['tanggal_awal'], 0, 10)." 00:00:00' and tanggal <= '".substr($ongoing['tanggal_akhir'], 0, 10)." 23:59:59')"; 
		} 
		if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
		if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
		$data[$function] = $this->m_crud->read_data($table, '*, (select sum(debit) from acc_general_journal where id_trx = id_journal_entry) as total', $where, 'tanggal desc');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function adjustment_journal($action = null, $id = null){
		$this->access_denied(22);
		$data = $this->data;
		$function = 'adjustment_journal';
		$table = 'acc_'.$function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Adjustment Journal';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('id_aj', 'Adjustment Journal No.', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('currency', 'Currency', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('exchange', 'Exchange', 'trim|required|greater_than[0]', array('required' => '%s don`t empty'));
		/*if(isset($_POST['tambah'])){
			$this->form_validation->set_rules('coa', 'Account Name', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('descrip', 'Descrip', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('debit', 'Debit', 'trim|required', array('required' => '%s don`t empty'));
			$this->form_validation->set_rules('credit', 'Credit', 'trim|required', array('required' => '%s don`t empty'));
		} else*/ if($action == 'delete' && $id != null){
			$this->cart->remove($id);
		}
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else {
			if(isset($_POST['tambah'])){
				for($i=1; $i<=$_POST['jumlah']; $i++){
					$item = array(
						'id'   		=> $this->input->post('coa'.$i), //harus ada
						'qty'  		=> 1, //harus ada
						'price'   	=> 1, //harus ada
						'name'    	=> $this->m_accounting->coa($this->input->post('coa'.$i), 'nama'), //harus ada
						'descrip'	=> $this->input->post('descrip'.$i),
						'debit'		=> $this->input->post('debit'.$i),
						'credit'	=> $this->input->post('credit'.$i)
					);
					$this->cart->insert($item);
				}
			} else if(isset($_POST['save'])){
				if($this->input->post('tot_debit') == $this->input->post('tot_credit')){
					$this->db->trans_begin();
					for($i=1; $i<=$_POST['jumlah']; $i++){
						$this->m_crud->create_data($table, array(
							'id_trx' => $this->input->post('id_aj'),
							'tanggal' => $_POST['tgl_quo'].' '.date('H:i:s'),
							'coa' => $this->input->post('coa'.$i),
							'descrip' => $this->input->post('descrip'.$i),
							'currency' => $this->input->post('currency'),
							'rate' => $this->input->post('exchange'),
							'debit' => $this->input->post('debit'.$i),
							'credit' => $this->input->post('credit'.$i),
							'user_id' => $this->user,
							'lokasi' => $this->m_website->get_lokasi()
						));
					}
					if ($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
					} else {
						$this->db->trans_commit();
						$this->cart->destroy();
						echo '<script>alert("Adjustment Journal has been saved");window.location = "'.$function.'";</script>';
					}
				} else { echo '<script>alert("Debit and Credit must balance")</script>'; }
			}
			$this->load->view('user/content', $data); 
		}
	}
	
	public function adjustment_journal_report($action = null, $id = null){
		$this->access_denied(23);
		$data = $this->data;
		$function = 'adjustment_journal_report';
		$table = 'acc_adjustment_journal';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Adjustment Journal Report';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->form_validation->set_rules('tgl_awal', 'From Date', 'trim|required', array('required' => '%s don`t empty'));
		$this->form_validation->set_rules('tgl_akhir', 'To Date', 'trim|required', array('required' => '%s don`t empty'));
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			if($_POST['tgl_awal'] != null && $_POST['tgl_akhir'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00' and tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')";
			} else if($_POST['tgl_awal'] != null){ 
				$where = "(tanggal >= '".$this->input->post('tgl_awal')." 00:00:00')"; 
			} else if($_POST['tgl_akhir'] != null){ 
				$where = "(tanggal <= '".$this->input->post('tgl_akhir')." 23:59:59')"; 
			} else { $where = null; } 
		} else { 	
			$ongoing = $this->m_accounting->ongoing_periode(); 
			$where = "(tanggal >= '".substr($ongoing['tanggal_awal'], 0, 10)." 00:00:00' and tanggal <= '".substr($ongoing['tanggal_akhir'], 0, 10)." 23:59:59')"; 
		} 
		if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
		if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
		
		if(isset($_POST['to_excel'])){
			$baca = $this->m_crud->read_data($table, 'id_trx, tanggal, currency, rate, user_id, sum(debit/rate) as total', $where, 'tanggal desc', 'id_trx, tanggal, currency, rate, user_id');
			$total=0;
			$i=0; foreach($baca as $row => $value){
				$body[$i] = array(
					$value['id_trx'], $value['tanggal'], $this->m_website->user_data($value['user_id'])->nama, 
					$value['total']
				); 
				$total = $total + $value['total'];
				$i++;
			}
			$i++;
			$body[$i] = array('', '', 'Total', $total);
			$header = array(
				'merge' 	=> array('A1:D1','A2:D2','A3:D3'),
				'auto_size' => true,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:D5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => $_POST['tgl_awal'].' - '.$_POST['tgl_akhir']),
				'5' => array(
					'A' => 'Adjustment Journal No.', 'B' => 'Date', 'C' => 'User', 'D' => 'Total'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		if($action == 'print' && isset($_GET['trx'])){
			$data['content'] = $view.'invoice_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', "id_trx = '".$_GET['trx']."'");
		} 
		$data[$function] = $this->m_crud->read_data($table, 'id_trx, tanggal, currency, rate, user_id, sum(debit/rate) as total', $where, 'tanggal desc', 'id_trx, tanggal, currency, rate, user_id');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function work_sheet(){
		$this->access_denied(24);
		$data = $this->data;
		$function = 'work_sheet';
		$table = $function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Work Sheet';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = $this->m_accounting->account();
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function trial_balance(){
		$this->access_denied(25);
		$data = $this->data;
		$function = 'trial_balance';
		$table = $function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Trial Balance';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		if(isset($_POST['to_excel'])){
			if($_POST['tgl_akhir'] != null){ $periode = $this->input->post('tgl_akhir'); } else { $periode = $this->m_accounting->periode(); }
			$baca = $this->m_crud->read_data('coa', '*', null, 'coa_id asc');
			$i=0; $debit = 0; $credit = 0;
			foreach($baca as $row => $value){ 
				$ending = $this->m_accounting->saldo_akhir($value['coa_id'], $periode);
				if($value['balance'] == 'D'){ $debit = $debit + $ending; } else { $credit = $credit + $ending; }
				
				$body[$i] = array(
					$value['coa_id'], $this->m_accounting->coa($value['coa_id'], 'nama'), 
					$value['balance']=='D'?$ending:'', $value['balance']=='C'?$ending:''
				);
				$i++;
			}
			$i++;
			$body[$i] = array('Total', '', $debit, $credit);
			$header = array(
				'merge' 	=> array('A1:D1','A2:D2','A3:D3'),
				'auto_size' => true,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:D5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => $_POST['tgl_akhir']),
				'5' => array(
					'A' => 'Account Code', 'B' => 'Account Name', 'C' => 'Debit', 'D' => 'Credit'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		$data[$function] = $this->m_crud->read_data('coa', '*', null, 'coa_id asc');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function profit_loss(){
		$this->access_denied(26);
		$data = $this->data;
		$function = 'profit_loss';
		$table = $function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Profit and Loss';
		$data['page'] = $function;
		if(isset($_POST['multi']) && $_POST['multi']==1){ $data['content'] = $view.$function.'_multi'; }
		else{ $data['content'] = $view.$function; }
		$data['table'] = $table;
		$data[$function] = array();
		
		if(isset($_POST['to_excel'])){
			if(isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir'])){ 
				$periode = array(($_POST['tgl_awal']!=null)?$_POST['tgl_awal']:'2017-01-01', ($_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:date('Y-m-d')); 
			} else { $periode = $this->m_accounting->periode(); }
			$multi = array(); if(isset($_POST['multi']) && $_POST['multi']==1){ 
				$multi = $this->m_website->multi_periode(($_POST['tgl_awal']!=null)?$_POST['tgl_awal']:'2017-01-01', ($_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:date('Y-m-d')); 
			}
			$profit_loss = $this->m_accounting->account('*', 'coa_kategori.kategori_id >= 41 and coa_kategori.kategori_id <= 72');
			$i=0; $body[$i] = array('', '', '', ''); $m=0; foreach($multi as $mp){ $m++; array_push($body[$i], ($m==1)?$mp[0]:$mp[1]); } 
			$i++; $body[$i] = array('OPERATING REVENUE');
			$i++; $body[$i] = array('', 'Revenue');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $revenue[$m] = 0; } }
			else { $revenue = 0; } 
			$account = $this->m_accounting->plbs_account('group', 'revenue');
			foreach($profit_loss as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '');
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
							array_push($body[$i], $ending);
							$revenue[$m] = $revenue[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$revenue = $revenue + $ending;
					}
				} 
			} 
			$i++; $body[$i] = array('Total OPERATING REVENUE', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $revenue[$m]); }
			} else { array_push($body[$i], $revenue); }
			
			$i++; $body[$i] = array('Cost of Goods Sold');
			$i++; $body[$i] = array('', 'Cost of Goods Sold');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $cogs[$m] = 0; } }
			else { $cogs = 0; } 
			$account = $this->m_accounting->plbs_account('group', 'cogs');
			foreach($profit_loss as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '');
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
							array_push($body[$i], $ending);
							$cogs[$m] = $cogs[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$cogs = $cogs + $ending;
					} 
				} 
			} 
			$i++; $body[$i] = array('', 'Overhead Expenses');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $overhead[$m] = 0; } }
			else { $overhead = 0; } 
			$account = $this->m_accounting->plbs_account('group', 'overhead');
			foreach($profit_loss as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '');
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
							array_push($body[$i], $ending);
							$overhead[$m] = $overhead[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$overhead = $overhead + $ending;
					} 
				} 
			} 
			$i++; $body[$i] = array('Total Cost of Goods Sold', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $cogs[$m] + $overhead[$m]); }
			} else { array_push($body[$i], $cogs + $overhead); }
			
			$i++; $body[$i] = array('GROOS PROFIT', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$groos_profit[$m] = $revenue[$m] - ($cogs[$m] + $overhead[$m]); array_push($body[$i], $groos_profit[$m]); 
				}
			} else { 
				$groos_profit = $revenue - ($cogs + $overhead); array_push($body[$i], $groos_profit); 
			}
			
			$i++; $body[$i] = array('Operating Expenses');
			$i++; $body[$i] = array('', 'Expense');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $expense[$m] = 0; } }
			else { $expense = 0; }  
			$account = $this->m_accounting->plbs_account('group', 'expense');
			foreach($profit_loss as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '');
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
							array_push($body[$i], $ending);
							$expense[$m] = $expense[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$expense = $expense + $ending;
					} 
				} 
			} 
			$i++; $body[$i] = array('Total Operating Expense', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $expense[$m]); }
			} else { array_push($body[$i], $expense); }
			
			$i++; $body[$i] = array('INCOME FROM OPERATION', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$income[$m] = $groos_profit[$m] - $expense[$m]; array_push($body[$i], $income[$m]); 
				}
			} else { 
				$income = $groos_profit - $expense; array_push($body[$i], $income); 
			}
			
			$i++; $body[$i] = array('Other Income and Expenses', '', '', '');
			$i++; $body[$i] = array('', 'Other Income');
			$i++; $body[$i] = array('', '', 'Other Profit / Loss');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $other_income[$m] = 0; } }
			else { $other_income = 0; }  
			$account = $this->m_accounting->plbs_account('kategori', 'other_income');
			foreach($profit_loss as $row){ 
				if($row['kategori_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
							array_push($body[$i], $ending);
							$other_income[$m] = $other_income[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$other_income = $other_income + $ending;
					} 
				} 
			} 
			$i++; $body[$i] = array('', 'Total Other Income', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $other_income[$m]); }
			} else { array_push($body[$i], $other_income); }
			
			$i++; $body[$i] = array('', 'Other Expenses');
			$i++; $body[$i] = array('', '', 'Other Expense');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $other_expense[$m] = 0; } }
			else { $other_expense = 0; }  
			$account = $this->m_accounting->plbs_account('group', 'other_expense'); 
			foreach($profit_loss as $row){ 
				if($row['group_id'] == $account){ 
					$i++;
					$body[$i] = array('', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
							array_push($body[$i], $ending);
							$other_expense[$m] = $other_expense[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$other_expense = $other_expense + $ending;
					}
				} 
			} 
			$i++; $body[$i] = array('', 'Total Other Expenses', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $other_expense[$m]); }
			} else { array_push($body[$i], $other_expense); }
			
			$i++; $body[$i] = array('', 'Total Other Income and Expenses', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$other_inex[$m] = $other_income[$m] - $other_expense[$m]; array_push($body[$i], $other_inex[$m]); 
				}
			} else { 
				$other_inex = $other_income - $other_expense; array_push($body[$i], $other_inex); 
			}
			
			$i++; $body[$i] = array('NET PROFIT/LOSS (Before Tax)', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$net_profit[$m] = $income[$m] + $other_inex[$m]; array_push($body[$i], $net_profit[$m]); 
				}
			} else { 
				$net_profit = $income + $other_inex; array_push($body[$i], $net_profit); 
			}
			
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $tax[$m] = 0; } }
			else { $tax = 0; }
			$account = $this->m_accounting->plbs_account('group', 'tax_income'); 
			foreach($profit_loss as $row){ 
				if($row['group_id'] == $account){ $i++; //'72301'
					$body[$i] = array('('.$row['coa_id'].') '.$row['nama_coa'], '', '', '');
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
							array_push($body[$i], $ending);
							$tax[$m] = $tax[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$tax = $tax + $ending;
					}
				} 
			} 
			$i++; $body[$i] = array('NET PROFIT/LOSS (After Tax)', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $net_profit[$m] - $tax[$m]); }
			} else { array_push($body[$i], $net_profit - $tax); }
			
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $sharing[$m] = 0; } }
			else { $sharing = 0; }
			$account = $this->m_accounting->plbs_account('group', 'profit_sharing'); 
			foreach($profit_loss as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('('.$row['coa_id'].') '.$row['nama_coa'], '', '', '');
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
							array_push($body[$i], $ending);
							$sharing[$m] = $sharing[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$sharing = $sharing + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('NET INCOME)', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $net_profit[$m] - $tax[$m] - $sharing[$m]); }
			} else { array_push($body[$i], $net_profit - $tax - $sharing); }
			
			$header = array(
				'merge' 	=> array('A1:E1','A2:E2','A3:E3'),
				'auto_size' => false,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:E5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => $_POST['tgl_awal'].' - '.$_POST['tgl_akhir']),
				'5' => array(
					//'A' => 'Adjustment Journal No.', 'B' => 'Date', 'C' => 'Description', 'D' => 'User', 'E' => 'Total'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		
		$data[$function] = $this->m_accounting->account('*', 'coa_kategori.kategori_id >= 41 and coa_kategori.kategori_id <= 72');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function balance_sheet(){
		$this->access_denied(27);
		$data = $this->data;
		$function = 'balance_sheet';
		$table = $function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Balance Sheet';
		$data['page'] = $function;
		if(isset($_POST['multi']) && $_POST['multi']==1){ $data['content'] = $view.$function.'_multi'; }
		else{ $data['content'] = $view.$function; }
		$data['table'] = $table;
		$data[$function] = array(); 
		if(isset($_POST['to_excel'])){
			if(isset($_POST['tgl_akhir']) && $_POST['tgl_akhir']!=null){ $periode = $this->input->post('tgl_akhir'); } else { $periode = $this->m_accounting->periode(); }
			$multi = array(); if(isset($_POST['multi']) && $_POST['multi']==1){ 
				$multi = $this->m_website->multi_periode(($_POST['tgl_awal']!=null)?$_POST['tgl_awal']:'2017-01-01', ($_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:date('Y-m-d')); 
			}
			$balance_sheet = $this->m_accounting->account('*', 'coa_kategori.kategori_id >= 11 and coa_kategori.kategori_id <= 31');
			$i=0; $body[$i] = array('', '', '', '', ''); $m=0; foreach($multi as $mp){ $m++; array_push($body[$i], ($m==1)?$mp[0]:$mp[1]); } 
			$i++; $body[$i] = array('ASSETS');
			$i++; $body[$i] = array('', 'CURRENT ASSETS');
			$i++; $body[$i] = array('', '', 'Cash and Bank');
			$i++; $body[$i] = array('', '', '', 'Cash');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $cash_bank[$m] = 0; } }
			else { $cash_bank = 0; }
			$account = $this->m_accounting->plbs_account('group', 'cash');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$cash_bank[$m] = $cash_bank[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$cash_bank = $cash_bank + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('', '', '', 'Bank');
			$account = $this->m_accounting->plbs_account('group', 'bank');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$cash_bank[$m] = $cash_bank[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$cash_bank = $cash_bank + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('', '', '', 'Other Cash and Bank');
			$account = $this->m_accounting->plbs_account('group', 'other_cash_bank');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$cash_bank[$m] = $cash_bank[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$cash_bank = $cash_bank + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('', '', '', 'Deposit');
			$account = $this->m_accounting->plbs_account('group', 'deposit');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$cash_bank[$m] = $cash_bank[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$cash_bank = $cash_bank + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('', '', 'Total Cash and Bank', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $cash_bank[$m]); }
			} else { array_push($body[$i], $cash_bank); }
			
			$i++; $body[$i] = array('', '', 'Account Receivable');
			$i++; $body[$i] = array('', '', '', 'Account Receivable');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $ar[$m] = 0; } }
			else { $ar = 0; }
			$account = $this->m_accounting->plbs_account('group', 'ar');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$ar[$m] = $ar[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$ar = $ar + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('', '', 'Total Account Receivable', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $ar[$m]); }
			} else { array_push($body[$i], $ar); }
			
			$i++; $body[$i] = array('', '', 'Inventory');
			$i++; $body[$i] = array('', '', '', 'Inventory');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $inventory[$m] = 0; } }
			else { $inventory = 0; }  
			$account = $this->m_accounting->plbs_account('group', 'inventory');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$inventory[$m] = $inventory[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$inventory = $inventory + $ending;
					}
				} 
			}	
			$i++; $body[$i] = array('', '', 'Total Inventory', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $inventory[$m]); }
			} else { array_push($body[$i], $inventory); }
			
			$i++; $body[$i] = array('', '', 'Other Current Assets');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $other[$m] = 0; } }
			else { $other = 0; }  
			$account = $this->m_accounting->plbs_account('group', 'oca');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$other[$m] = $other[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$other = $other + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('', '', '', 'Prepaid');
			$account = $this->m_accounting->plbs_account('group', 'prepaid');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$other[$m] = $other[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$other = $other + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('', '', 'Total Other Current Assets', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $other[$m]); }
			} else { array_push($body[$i], $other); }
			
			$i++; $body[$i] = array('', 'Total CURRENT ASSETS', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$current_assets[$m] = $cash_bank[$m] + $ar[$m] + $inventory[$m] + $other[$m]; array_push($body[$i], $current_assets[$m]); 
				}
			} else { 
				$current_assets = $cash_bank + $ar + $inventory + $other; array_push($body[$i], $current_assets); 
			}
			
			$i++; $body[$i] = array('', 'FIXED ASSETS');
			$i++; $body[$i] = array('', '', 'Historical Value');
			$i++; $body[$i] = array('', '', '', 'Fixed Assets');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $history[$m] = 0; } }
			else { $history = 0; }  
			$account = $this->m_accounting->plbs_account('group', 'fixed_assets');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$history[$m] = $history[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$history = $history + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('', '', 'Total Historical Value', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $history[$m]); }
			} else { array_push($body[$i], $history); }
			
			$i++; $body[$i] = array('', '', 'Accumulated Depreciation');
			$i++; $body[$i] = array('', '', '', 'Accumulated Depreciation');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $accum[$m] = 0; } }
			else { $accum = 0; }   
			$account = $this->m_accounting->plbs_account('group', 'depreciation');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$accum[$m] = $accum[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$accum = $accum + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('', '', 'Total Accumulated Depreciation', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $accum[$m]); }
			} else { array_push($body[$i], $accum); }
			
			$i++; $body[$i] = array('', 'Total FIXED ASSETS', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$fixed_assets[$m] = $history[$m] - $accum[$m]; array_push($body[$i], $fixed_assets[$m]); 
				}
			} else { 
				$fixed_assets = $history - $accum; array_push($body[$i], $fixed_assets); 
			}
			
			$i++; $body[$i] = array('', ' OTHER ASSETS',);
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $other_assets[$m] = 0; } }
			else { $other_assets = 0; }   
			$i++; $body[$i] = array('', 'Total OTHER ASSETS', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $other_assets[$m]); }
			} else { array_push($body[$i], $other_assets); }
			
			$i++; $body[$i] = array('Total ASSETS', '', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$total_assets[$m] = $current_assets[$m] + $fixed_assets[$m] + $other_assets[$m]; array_push($body[$i], $total_assets[$m]); 
				}
			} else { 
				$total_assets = $current_assets + $fixed_assets + $other_assets; array_push($body[$i], $total_assets); 
			}
			
			$i++; $body[$i] = array('LIABILITIES and EQUITIES');
			$i++; $body[$i] = array('', 'LIABILITIES');
			$i++; $body[$i] = array('', '', 'Current Liabilities');
			$i++; $body[$i] = array('', '', '', 'Account Payables');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $ap[$m] = 0; } }
			else { $ap = 0; }   
			$account = $this->m_accounting->plbs_account('group', 'ap');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$ap[$m] = $ap[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$ap = $ap + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('', '', '', 'Total Account Payables', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $ap[$m]); }
			} else { array_push($body[$i], $ap); }
			
			$i++; $body[$i] = array('', '', '', 'Other Current Liabilities');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $ocl[$m] = 0; } }
			else { $ocl = 0; }   
			$account = $this->m_accounting->plbs_account('group', 'ocl');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							array_push($body[$i], $ending);
							$ocl[$m] = $ocl[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						array_push($body[$i], $ending);
						$ocl = $ocl + $ending;
					}
				} 
			} 
			$i++; $body[$i] = array('', '', '', 'Total Other Current Liabilities', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $ocl[$m]); }
			} else { array_push($body[$i], $ocl); }
	
			$i++; $body[$i] = array('', '', 'Total Current Liabilities', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$current_liabilities[$m] = $ap[$m] + $ocl[$m]; array_push($body[$i], $current_liabilities[$m]); 
				}
			} else { 
				$current_liabilities = $ap + $ocl; array_push($body[$i], $current_liabilities); 
			}
			
			$i++; $body[$i] = array('', '', 'Long Term Liabilities');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $longterm_liabilities[$m] = 0; } }
			else { $longterm_liabilities = 0; }   
			$i++; $body[$i] = array('', '', 'Total Long Term Liabilities', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; array_push($body[$i], $longterm_liabilities[$m]); }
			} else { array_push($body[$i], $longterm_liabilities); }
			 
			$i++; $body[$i] = array('', 'Total LIABILITIES', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$liabilities[$m] = $current_liabilities[$m] + $longterm_liabilities[$m]; array_push($body[$i], $liabilities[$m]); 
				}
			} else { 
				$liabilities = $current_liabilities + $longterm_liabilities; array_push($body[$i], $liabilities); 
			}
			
			$i++; $body[$i] = array('', 'EQUITIES');
			$i++; $body[$i] = array('', '', 'Capital');
			if(isset($_POST['multi']) && $_POST['multi']==1){ $m=0; foreach($multi as $mp){ $m++; $capital[$m] = 0; } }
			else { $capital = 0; }   
			$account = $this->m_accounting->plbs_account('group', 'capital');
			foreach($balance_sheet as $row){ 
				if($row['group_id'] == $account){ $i++;
					$body[$i] = array('', '', '', '', '('.$row['coa_id'].') '.$row['nama_coa']);
					if(isset($_POST['multi']) && $_POST['multi']==1){
						$m = 0; foreach($multi as $mp){ $m++;
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($m==1)?$mp[0]:$mp[1]); 
							if($row['coa_id']=='3104'){ $ending = $ending + $this->m_accounting->net_profit_pl(null, (substr(($i==1)?$mp[0]:$mp[1], 0, 4)>=substr(date('Y'), 0, 4) )?date('Y-m-d', strtotime('-1 day', strtotime(date('Y').'-01-01'))):date('Y-m-d', strtotime('-1 day', strtotime(substr(($i==1)?$mp[0]:$mp[1], 0, 4).'-01-01'))) ); } 
							array_push($body[$i], $ending);
							$capital[$m] = $capital[$m] + $ending;
						}
					} else {
						$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
						if($row['coa_id']=='3104'){ $ending = $ending + $this->m_accounting->net_profit_pl(null, (substr($periode, 0, 4)>=substr(date('Y'), 0, 4) )?date('Y-m-d', strtotime('-1 day', strtotime(date('Y').'-01-01'))):date('Y-m-d', strtotime('-1 day', strtotime(substr($periode, 0, 4).'-01-01'))) ); } 
						array_push($body[$i], $ending);
						$capital = $capital + $ending;
					}
				} 
			}
			$i++; $body[$i] = array('', '', 'Current Earning of The Year', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$net_profit[$m] = $this->m_accounting->net_profit_pl(null, (($m==1)?$mp[0]:$mp[1]>date('Y').'-01-01')?array(date('Y').'-01-01', ($m==1)?$mp[0]:$mp[1]):array(substr(($m==1)?$mp[0]:$mp[1], 0, 4).'-01-01', ($m==1)?$mp[0]:$mp[1]));
					array_push($body[$i], $net_profit[$m]); 
				}
			} else { 
				$net_profit = $this->m_accounting->net_profit_pl(null, ($periode>date('Y').'-01-01')?array(date('Y').'-01-01', $periode):array(substr($periode, 0, 4).'-01-01', $periode));
				array_push($body[$i], $net_profit); 
			}
			
			$i++; $body[$i] = array('', '', 'Total Capital', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$capital[$m] = $capital[$m] + $net_profit[$m]; array_push($body[$i], $capital[$m]); 
				}
			} else { 
				$capital = $capital + $net_profit; array_push($body[$i], $capital); 
			}
			
			$i++; $body[$i] = array('', 'Total EQUITIES', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$equities[$m] = $capital[$m] + 0; array_push($body[$i], $equities[$m]); 
				}
			} else { 
				$equities = $capital + 0; array_push($body[$i], $equities); 
			}
			
			$i++; $body[$i] = array('Total LIABILITIES and EQUITIES', '', '', '', '');
			if(isset($_POST['multi']) && $_POST['multi']==1){
				$m = 0; foreach($multi as $mp){ $m++; 
					$liab_equit[$m] = $liabilities[$m] + $equities[$m]; array_push($body[$i], $liab_equit[$m]); 
				}
			} else { 
				$liab_equit = $liabilities + $equities; array_push($body[$i], $liab_equit); 
			}
			
			$header = array(
				'merge' 	=> array('A1:F1','A2:F2','A3:F3'),
				'auto_size' => false,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:F5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => (isset($_POST['multi']) && $_POST['multi']==1)?$_POST['tgl_awal'].' - '.$_POST['tgl_akhir']:'As of '.$_POST['tgl_akhir']),
				'5' => array(
					//'A' => 'Adjustment Journal No.', 'B' => 'Date', 'C' => 'Description', 'D' => 'User', 'E' => 'Total'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		$data[$function] = $this->m_accounting->account('*', 'coa_kategori.kategori_id >= 11 and coa_kategori.kategori_id <= 31');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function cash_flow(){
		$this->access_denied(28);
		$data = $this->data;
		$function = 'cash_flow';
		$table = $function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Cash Flow';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		if(isset($_POST['to_excel'])){
			if(isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir'])){ 
				$periode = array(($_POST['tgl_awal']!=null)?$_POST['tgl_awal']:'2017-01-01', ($_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:date('Y-m-d')); 
			} else { $periode = $this->m_accounting->periode(); }
			$cash_flow = $this->m_accounting->account('*');
			
			$i=0; $body[$i] = array('Cash Flows from Operating Activities');
			$cash_sales = $this->m_accounting->cash_sales(null, $periode);
			$i++; $body[$i] = array('', 'Cash from Sales', '', '', $cash_sales);
			$i++; $body[$i] = array('', 'Other Profit / Loss');
			$other_profit = 0; $account = $this->m_accounting->plbs_account('kategori', 'other_income');
			foreach($cash_flow as $row){ 
				if($row['kategori_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '', $ending);
					$other_profit = $other_profit + $ending; 
				} 
			} 
			//$i++; $body[$i] = array('total other profit / loss', '', '', '', $other_profit);
			$cash_cogs = $this->m_accounting->cash_cogs(null, $periode);
			$i++; $body[$i] = array('', 'Cash to COGS', '', '', $cash_cogs);
			$i++; $body[$i] = array('', 'Overhead Expenses');
			$overhead = 0; $account = $this->m_accounting->plbs_account('group', 'overhead');
			foreach($cash_flow as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '', $ending);
					$overhead = $overhead + $ending; 
				} 
			} 
			$i++; $body[$i] = array('', 'Expense');
			$expense = 0; $account = $this->m_accounting->plbs_account('group', 'expense');
			foreach($cash_flow as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '', $ending);
					$expense = $expense + $ending; 
				} 
			}
			$i++; $body[$i] = array('', 'Other Expense');
			$other_expense = 0; $account = $this->m_accounting->plbs_account('kategori', 'other_expense');
			foreach($cash_flow as $row){ 
				if($row['kategori_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '', $ending);
					$other_expense = $other_expense + $ending; 
				} 
			}
			$oprt_ass_liab = $cash_sales + $other_profit - $cash_cogs - $overhead - $expense - $other_expense;
			$i++; $body[$i] = array('', 'Operating Profit(Loss) before changes in operating assets and liabilities', '', '', $oprt_ass_liab);
			$i++; $body[$i] = array('', 'Decrease (increase) in operating assets');
			$i++; $body[$i] = array('', '', 'Account Receivable');
			$ar = 0; $account = $this->m_accounting->plbs_account('group', 'ar');
			foreach($cash_flow as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); 
					$body[$i] = array('', '', '', '('.$row['coa_id'].') '.$row['nama_coa'], $ending);
					$ar = $ar + $ending; 
				} 
			}
			$i++; $body[$i] = array('', '', 'Inventory');
			$inventory = 0; $account = $this->m_accounting->plbs_account('group', 'inventory');
			foreach($cash_flow as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); 
					$body[$i] = array('', '', '', '('.$row['coa_id'].') '.$row['nama_coa'], $ending);
					$inventory = $inventory + $ending; 
				} 
			}
			$other_ar = 0; $account = $this->m_accounting->plbs_account('group', 'oca');
			foreach($cash_flow as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '', $ending);
					$other_ar = $other_ar + $ending;
				} 
			}
			$i++; $body[$i] = array('', '', 'Prepaid');
			$prepaid = 0; $account = $this->m_accounting->plbs_account('group', 'prepaid');
			foreach($cash_flow as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); 
					$body[$i] = array('', '', '', '('.$row['coa_id'].') '.$row['nama_coa'], $ending);
					$prepaid = $prepaid + $ending;
				} 
			}
			$oprt_ass = $ar + $inventory + $other_ar + $prepaid;
			$i++; $body[$i] = array('', 'Total Decrease (increase) in operating assets', '', '', $oprt_ass);
			$i++; $body[$i] = array('', 'Increase (decrease) in operating liabilities');
			$i++; $body[$i] = array('', '', 'Account Payable');
			$ap = 0; $account = $this->m_accounting->plbs_account('group', 'ap');
			foreach($cash_flow as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); 
					$body[$i] = array('', '', '', '('.$row['coa_id'].') '.$row['nama_coa'], $ending);
					$ap = $ap + $ending;
				} 
			}
			$i++; $body[$i] = array('', '', 'Other Current Liebility');
			$liebility = 0; $account = $this->m_accounting->plbs_account('group', 'ocl');
			foreach($cash_flow as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); 
					$body[$i] = array('', '', '', '('.$row['coa_id'].') '.$row['nama_coa'], $ending);
					$liebility = $liebility + $ending;
				} 
			}
			$oprt_liab = $ap + $liebility;
			$i++; $body[$i] = array('', 'Total Increase (decrease) in operating liabilities', '', '', $oprt_liab);
			$operating = $oprt_ass_liab - $oprt_ass + $oprt_liab;
			$i++; $body[$i] = array('Net Cash (used in) / Provided by operating activities', '', '', '', $operating);
			$i++; $body[$i] = array('Cash Flows from Investing Activities');
			$i++; $body[$i] = array('', 'Fixed Asset');
			$asset = 0; $account = $this->m_accounting->plbs_account('group', 'fixed_assets');
			foreach($cash_flow as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '', $ending);
					$asset = $asset + $ending;
				} 
			}
			$i++; $body[$i] = array('', 'Accumulated Depreciation');
			$accum = 0; $account = $this->m_accounting->plbs_account('group', 'depreciation');
			foreach($cash_flow as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '', $ending);
					$accum = $accum + $ending;
				} 
			}
			$investing = $asset - $accum;
			$i++; $body[$i] = array('Net cash provided by / (used in) investing activities', '', '', '', $investing);
			$i++; $body[$i] = array('Cash Flows from financing Activities');
			$i++; $body[$i] = array('', 'Capital');
			$capital = 0; $account = $this->m_accounting->plbs_account('group', 'capital');
			foreach($cash_flow as $row){ 
				if($row['group_id'] == $account){ $i++; 
					$ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); 
					if($row['coa_id']=='3104'){ $ending = $ending + $this->m_accounting->net_profit_pl(null, (substr($periode[0], 0, 4)>=substr(date('Y'), 0, 4) )?date('Y-m-d', strtotime('-1 day', strtotime(date('Y').'-01-01'))):date('Y-m-d', strtotime('-1 day', strtotime(substr($periode[0], 0, 4).'-01-01'))) ); }
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], '', $ending);
					$capital = $capital + $ending;
				} 
			}
			$financing = $capital;
			$i++; $body[$i] = array('Net Cash provided by / (used in) financing activities', '', '', '', $financing);
			$net_cash_period = $operating - $investing + $financing;
			$i++; $body[$i] = array('Net Cash provided / (used in) in this period', '', '', '', $net_cash_period);
			$beginning_periode = $this->m_accounting->net_profit_pl(null, ($periode[0]>date('Y').'-01-01')?array(date('Y').'-01-01', $periode[0]):array(substr($periode[0], 0, 4).'-01-01', $periode[0])); 
			$i++; $body[$i] = array('Cash & equivalent at the Beginning of Period', '', '', '', $beginning_periode);
			$end_periode = $net_cash_period + $beginning_periode;
			$i++; $body[$i] = array('Cash & equivalent at the End of period', '', '', '', $end_periode);
			
			
			$header = array(
				'merge' 	=> array('A1:E1','A2:E2','A3:E3'),
				'auto_size' => false,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:E5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => $_POST['tgl_awal'].' - '.$_POST['tgl_akhir']),
				'5' => array(
					//'A' => 'Adjustment Journal No.', 'B' => 'Date', 'C' => 'Description', 'D' => 'User', 'E' => 'Total'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		$data[$function] = $this->m_accounting->account('*');
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function capital_change(){
		$this->access_denied(29);
		$data = $this->data;
		$function = 'capital_change';
		$table = $function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Capital Change';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function currency_balance(){
		$this->access_denied(30);
		$data = $this->data;
		$function = 'currency_balance';
		$table = $function;
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Currency Balance';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		if(isset($_POST['to_excel'])){
			if(isset($_POST['tgl_akhir']) && $_POST['tgl_akhir']!=null){ $periode = $this->input->post('tgl_akhir'); } else { $periode = $this->m_accounting->periode(); }
			$currency_balance = $this->m_accounting->account('*', "currency <> 1");
			/*$coa_currency = '"1"';
			$coa = $this->m_crud->read_data('acc_general_journal', 'coa', 'rate <> 1', null, 'coa');
			foreach($coa as $row){
				$coa_currency .= ',"'.$row['coa'].'"';
			}
			$coa = $this->m_crud->read_data('acc_beginning_balance', 'coa', 'rate <> 1', null, 'coa');
			foreach($coa as $row){
				$coa_currency .= ',"'.$row['coa'].'"';
			}
			$currency_balance = $this->m_accounting->account('*', 'coa_id in ('.$coa_currency.')');
			$i=-1;
			$cash_bank = 0; $account = $this->m_accounting->plbs_account('group', 'cash');
			foreach($currency_balance as $row){ 
				//if($row['group_id'] == $account){ 
					$i++;
					$ending = $this->m_accounting->saldo_akhir_asing($row['coa_id'], $periode); 
					$body[$i] = array('('.$row['coa_id'].') '.$row['nama_coa'], $ending);
					$cash_bank = $cash_bank + $ending;
				//} 
			}
			*/
			$i=0; $body[$i] = array('Cash and Bank');
			$i++; $body[$i] = array('', 'Cash');
			$cash_bank = 0; $account = $this->m_accounting->plbs_account('group', 'cash');
			foreach($currency_balance as $row){ 
				if($row['group_id'] == $account){ $i++;
					$ending = $this->m_accounting->saldo_akhir_asing($row['coa_id'], $periode); 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], $ending);
					$cash_bank = $cash_bank + $ending;
				} 
			}
			$i++; $body[$i] = array('', 'Bank');
			$account = $this->m_accounting->plbs_account('group', 'bank');
			foreach($currency_balance as $row){ 
				if($row['group_id'] == $account){ $i++;
					$ending = $this->m_accounting->saldo_akhir_asing($row['coa_id'], $periode); 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], $ending);
					$cash_bank = $cash_bank + $ending;
				} 
			}
			$i++; $body[$i] = array('Account Receivable');
			$i++; $body[$i] = array('', 'Account Receivable');
			$account = $this->m_accounting->plbs_account('group', 'ar');
			foreach($currency_balance as $row){ 
				if($row['group_id'] == $account){ $i++;
					$ending = $this->m_accounting->saldo_akhir_asing($row['coa_id'], $periode); 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], $ending);
					$cash_bank = $cash_bank + $ending;
				} 
			}
			$i++; $body[$i] = array('Liabilities');
			$i++; $body[$i] = array('', 'Account Payable');
			$account = $this->m_accounting->plbs_account('group', 'ap');
			foreach($currency_balance as $row){ 
				if($row['group_id'] == $account){ $i++;
					$ending = $this->m_accounting->saldo_akhir_asing($row['coa_id'], $periode); 
					$body[$i] = array('', '', '('.$row['coa_id'].') '.$row['nama_coa'], $ending);
					$cash_bank = $cash_bank + $ending;
				} 
			}
			$header = array(
				'merge' 	=> array('A1:F1','A2:F2','A3:F3'),
				'auto_size' => false,
				'font' 		=> array(
						'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
						'A3' => array('bold'=>true,'name'=>'Verdana'), 'A5:F5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
					),
				'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
				'1' => array('A' => $data['site']->title),
				'2' => array('A' => $data['title']),
				'3' => array('A' => 'As of '.$_POST['tgl_akhir']),
				'5' => array(
					//'A' => 'Adjustment Journal No.', 'B' => 'Date', 'C' => 'Description', 'D' => 'User', 'E' => 'Total'
				)
			);
			$this->m_export_file->to_excel(str_replace('/', '-', str_replace(' ', '_', $data['title'])), $header, $body); 
		}
		$data[$function] = $this->m_accounting->account('*', "currency <> 1");
		/*
		$coa_currency = '"1"';
		$coa = $this->m_crud->read_data('acc_general_journal', 'coa', 'rate <> 1', null, 'coa');
		foreach($coa as $row){
			$coa_currency .= ',"'.$row['coa'].'"';
		}
		$coa = $this->m_crud->read_data('acc_beginning_balance', 'coa', 'rate <> 1', null, 'coa');
		foreach($coa as $row){
			$coa_currency .= ',"'.$row['coa'].'"';
		}
		$data[$function] = $this->m_accounting->account('*', 'coa_id in ('.$coa_currency.')');
		*/
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { $this->load->view('user/content', $data); }
	}
	
	public function closing_entries($action = null){
		$this->access_denied(31);
		$data = $this->data;
		$function = 'closing_entries';
		$table = 'acc_periode';
		$view = 'accounting/';
		if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Closing Entries';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$data[$function] = array();
		if(isset($_POST['closing'])){ 
			for($i=1;$i<=$_POST['jumlah'];$i++){
				$this->form_validation->set_rules('exchange'.$i, 'Exchange', 'trim|required|greater_than[0]', array('required' => '%s don`t empty'));
			}	
		}else if($action == 'detail'&& isset($_GET['trx'])){
			$data['content'] = $view.'detail_'.$function;
			$data['print'] = $this->m_crud->get_data($table, '*', 'id_periode = "'.$_GET['trx'].'"');
		}
		$this->load->view('user/header', $data);
		if($this->form_validation->run() == false){ $this->load->view('user/content', $data); } 
		else { 
			if(isset($_POST['closing'])){ 
				$this->db->trans_begin();
				if($this->m_accounting->setting_periode('closing') == true){ 
					$data['closing'] = true;
				} else {
					$data['closing'] = false;
				}
				if ($this->db->trans_status() === FALSE && $data['closing'] == false){
					$this->db->trans_rollback();
				} else {
					$this->db->trans_commit();
				}
			}
			$this->load->view('user/content', $data); 
		}
	}

	public function laba_rugi() {
        $this->access_denied(201);
        $data = $this->data;
        $function = 'laba_rugi';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Laba Rugi';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['lokasi'] = '';

        $date1 = date('Y-m-d'); $date2 = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);

        if (isset($date) && $date != null) {
            $date1 = str_replace('/','-',$explode_date[0]);
            $date2 = str_replace('/','-',$explode_date[1]);
        }

        $q_lokasi = '';

        if(isset($lokasi)&&$lokasi!=null){ $q_lokasi=" and lokasi='".$lokasi."'"; }

        $data['lokasi'] = ($lokasi==null)?'':$lokasi;
        $data['tgl_awal'] = $date1;
        $data['tgl_akhir'] = $date2;

        $result = array();

        $get_penjualan = $this->m_crud->get_data("Master_Trx mt, Det_Trx dt", "SUM(dt.qty*hrg_jual)-SUM(dt.dis_persen) penjualan, SUM(qty*hrg_beli) hpp, SUM(mt.dis_rp) dis_penjualan", "mt.kd_trx=dt.kd_trx and LEFT(convert(varchar, mt.tgl, 120), 10) between '".$date1."' and '".$date2."'".$q_lokasi);
        $get_dis_pembelian = $this->m_crud->read_data("master_beli", "SUM(ISNULL(disc, 0)) disc", "LEFT(convert(varchar, tgl_beli, 120), 10) between '".$date1."' and '".$date2."'".$q_lokasi);
        $get_kas_masuk = $this->m_crud->get_data("kas_masuk", "SUM(jumlah) kas_masuk", "LEFT(convert(varchar, tgl, 120), 10) between '".$date1."' and '".$date2."'".$q_lokasi);
        $get_kas_keluar = $this->m_crud->get_data("kas_keluar", "SUM(jumlah) kas_keluar", "LEFT(convert(varchar, tgl, 120), 10) between '".$date1."' and '".$date2."'".$q_lokasi);

        $result['penjualan'] = $get_penjualan['penjualan'];
        $result['hpp'] = $get_penjualan['hpp'];
        $result['dis_penjualan'] = $get_penjualan['dis_penjualan'];
        $result['dis_pembelian'] = $get_dis_pembelian['disc'];
        $result['kas_masuk'] = $get_kas_masuk['kas_masuk'];
        $result['kas_keluar'] = $get_kas_keluar['kas_keluar'];


        $data['result'] = $result;

        if(isset($_POST['to_excel'])){
            $header = array(
                'merge' 	=> array('A1:C1', 'A2:C2', 'A3:C3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:C5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:C5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $date1.' - '.$date2),
                '5' => array(
                    'A'=>'Kode Supplier', 'B'=>'Nama Supplier', 'C'=>'Total Pembelian'
                )
            );

            $body[""] = array(

            );

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function kas_masuk($action = null, $id = null){
        $this->access_denied(202);
        $data = $this->data;
        $function = 'kas_masuk';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Kas Masuk';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "km.kd_kasir=ud.user_id and lk.kode=km.lokasi and km.kd_kas_masuk=mkm.kode";
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'jns_kas' => $_POST['jns_kas']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $jns_kas = $this->session->search['jns_kas'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, km.tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, km.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="km.lokasi = '".$lokasi."'";
        } else {
            ($where==null)?null:$where.=" and "; $where.="km.lokasi in (".$this->lokasi_in.")";
        }
        if(isset($jns_kas)&&$jns_kas!=null){
            ($where==null)?null:$where.=" and "; $where.="km.kd_kas_masuk = '".$jns_kas."'";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(km.kd_trx like '%".$search."%' or km.Lokasi like '%".$search."%' or ud.nama like '%".$search."%')"; }

        $page = ($id==null?1:$id);

        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_read_data('kas_masuk km, user_detail ud, lokasi lk, master_kas_masuk mkm', "km.kd_trx", $where);
        $config['per_page'] = 15;
        //$config['attributes'] = array('class' => ''); //attributes anchors
        $config['first_url'] = $config['base_url'];
        $config['num_links'] = 5;
        $config['use_page_numbers'] = TRUE;
        //$config['display_pages'] = FALSE;
        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['first_tag_open'] = '<li>'; $config['first_link'] = '&laquo;'; $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>'; $config['prev_link'] = '&lt;'; $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#"> '; $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>'; $config['num_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>'; $config['next_link'] = '&gt;';  $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>'; $config['last_link'] = '&raquo;'; $config['last_tag_close'] = '</li>';
        $config['full_tag_close'] = '</ul>';
        $this->pagination->initialize($config);

        $data['report'] = $this->m_crud->select_limit('kas_masuk km, user_detail ud, lokasi lk, master_kas_masuk mkm', "km.tgl, km.kd_trx, km.Keterangan, lk.nama Lokasi, mkm.nama nama_kas, km.jumlah, ud.nama", $where, 'km.kd_trx desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        $row = $this->m_crud->get_data('kas_masuk km, user_detail ud, lokasi lk, master_kas_masuk mkm', "sum(km.jumlah) jumlah", $where);

        $data['tkas'] = $row['jumlah'];

        if(isset($_POST['to_excel'])){
            $detail = $this->m_crud->read_data('kas_masuk km, user_detail ud, lokasi lk, master_kas_masuk mkm', "km.tgl, km.kd_trx, km.Keterangan, lk.nama Lokasi, mkm.nama nama_kas, km.jumlah, ud.nama", $where, 'km.kd_trx desc');
            $baca = $detail;
            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['tgl'], $value['kd_trx'], $value['nama_kas'], $value['Lokasi'], $value['keterangan'], $value['nama'], $value['jumlah']
                );
            }
            $header = array(
                'merge' 	=> array('A1:G1','A2:G2','A3:G3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:G5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Kode Transaksi', 'C'=>'Jenis Kas', 'D'=>'Lokasi', 'E'=>'Keteramgam', 'F'=>'Operator', 'G'=>'Jumlah'
                )
            );
            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function kas_keluar($action = null, $id = null){
        $this->access_denied(203);
        $data = $this->data;
        $function = 'kas_keluar';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Kas Keluar';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "kk.kd_kasir=ud.user_id AND lk.kode=kk.Lokasi AND kk.kd_kas_keluar=mkk.kode";
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, kk.tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, kk.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="kk.lokasi = '".$lokasi."'";
        } else {
            ($where==null)?null:$where.=" and "; $where.="kk.lokasi in (".$this->lokasi_in.")";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(kk.kd_trx like '%".$search."%' or kk.Lokasi like '%".$search."%' or ud.nama like '%".$search."%')"; }

        $page = ($id==null?1:$id);

        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_read_data('kas_keluar kk, user_detail ud, lokasi lk, master_kas_keluar mkk', "kk.kd_trx", $where);
        $config['per_page'] = 15;
        //$config['attributes'] = array('class' => ''); //attributes anchors
        $config['first_url'] = $config['base_url'];
        $config['num_links'] = 5;
        $config['use_page_numbers'] = TRUE;
        //$config['display_pages'] = FALSE;
        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['first_tag_open'] = '<li>'; $config['first_link'] = '&laquo;'; $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>'; $config['prev_link'] = '&lt;'; $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#"> '; $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>'; $config['num_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>'; $config['next_link'] = '&gt;';  $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>'; $config['last_link'] = '&raquo;'; $config['last_tag_close'] = '</li>';
        $config['full_tag_close'] = '</ul>';
        $this->pagination->initialize($config);

        $data['report'] = $this->m_crud->select_limit('kas_keluar kk, user_detail ud, lokasi lk, master_kas_keluar mkk', "kk.tgl, kk.kd_trx, kk.Keterangan, lk.nama Lokasi, mkk.nama nama_kas, kk.jumlah, ud.nama", $where, 'kk.kd_trx desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        $row = $this->m_crud->get_data('kas_keluar kk, user_detail ud, lokasi lk, master_kas_keluar mkk', "sum(kk.jumlah) jumlah", $where);

        $data['tkas'] = $row['jumlah'];

        if(isset($_POST['to_excel'])){
            $detail = $this->m_crud->read_data('kas_keluar kk, user_detail ud, lokasi lk, master_kas_keluar mkk', "kk.tgl, kk.kd_trx, kk.Keterangan, lk.nama Lokasi, mkk.nama nama_kas, kk.jumlah, ud.nama", $where, 'kk.kd_trx desc');
            $baca = $detail;
            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['tgl'], $value['kd_trx'], $value['nama_kas'], $value['Lokasi'], $value['keterangan'], $value['nama'], $value['jumlah']
                );
            }
            $header = array(
                'merge' 	=> array('A1:G1','A2:G2','A3:G3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:G5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Kode Transaksi', 'C'=>'Jenis Kas', 'D'=>'Lokasi', 'E'=>'Keteramgam', 'F'=>'Operator', 'G'=>'Jumlah'
                )
            );
            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function deposit_member($action=null, $id=null) {
        $this->access_denied(204);
        $data = $this->data;
        $function = 'deposit_member';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Deposit Member';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any']));
        }

        $search = $this->session->search['any'];

        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(cs.kode like '%".$search."%' or cs.nama like '%".$search."%')"; }

        if ($action==null) {
            $page = ($id == null ? 1 : $id);

            $config['base_url'] = base_url() . strtolower($this->control) . '/' . $function . '/' . ($action != null ? $action : '-') . '/';
            $config['total_rows'] = $this->m_crud->count_data_join('customer cs', "cs.kd_cust", array(array('table'=>"kartu_deposit kd",'type'=>'LEFT')), array("cs.kd_cust=kd.member"), $where, null, "cs.kd_cust");
            $config['per_page'] = 15;
            //$config['attributes'] = array('class' => ''); //attributes anchors
            $config['first_url'] = $config['base_url'];
            $config['num_links'] = 5;
            $config['use_page_numbers'] = TRUE;
            //$config['display_pages'] = FALSE;
            $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
            $config['first_tag_open'] = '<li>';
            $config['first_link'] = '&laquo;';
            $config['first_tag_close'] = '</li>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_link'] = '&lt;';
            $config['prev_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="active"><a href="#"> ';
            $config['cur_tag_close'] = '</a></li>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['next_tag_open'] = '<li>';
            $config['next_link'] = '&gt;';
            $config['next_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>';
            $config['last_link'] = '&raquo;';
            $config['last_tag_close'] = '</li>';
            $config['full_tag_close'] = '</ul>';
            $this->pagination->initialize($config);

            $data['report'] = $this->m_crud->select_limit_join("customer cs", "cs.nama, cs.kd_cust kode, sum(kd.saldo_masuk-kd.saldo_keluar) saldo", array(array('table'=>'kartu_deposit kd', 'type'=>'LEFT')), array("cs.kd_cust=kd.member"), $where, 'cs.kd_cust asc', "cs.nama, cs.kd_cust", ($page - 1) * $config['per_page'] + 1, ($config['per_page'] * $page));
            $data['total_saldo'] = $this->m_crud->get_join_data("customer cs", "sum(kd.saldo_masuk-kd.saldo_keluar) saldo", array(array('table'=>'kartu_deposit kd', 'type'=>'LEFT')), array("cs.kd_cust=kd.member"), $where);
        }

        if(isset($_POST['to_excel'])){
            $detail = $this->m_crud->read_data('kas_keluar kk, user_detail ud', "kk.tgl, kk.kd_trx, kk.Keterangan, kk.Lokasi, kk.jumlah, ud.nama", $where, 'kk.kd_trx desc');
            $baca = $detail;
            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['tgl'], $value['kd_trx'], $value['Lokasi'], $value['keterangan'], $value['nama'], $value['jumlah']
                );
            }
            $header = array(
                'merge' 	=> array('A1:F1','A2:F2','A3:F3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:F5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Kode Transaksi', 'C'=>'Lokasi', 'D'=>'Keteramgam', 'E'=>'Operator', 'F'=>'Jumlah'
                )
            );
            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($this->form_validation->run() == false) {
            $this->load->view('bo/index', $data);
        } else {
            $this->load->view('bo/index', $data);
        }
    }

    public function ppob($action = null, $id = null){
        $this->access_denied(205);
        $data = $this->data;
        $function = 'ppob';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan PPOB';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'status' => $_POST['status']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $status = $this->session->search['status'];
        $explode_date =  explode(' - ',$date);
        if (isset($date) && $date != null) {
            $tgl_awal = str_replace('/','-',$explode_date[0]);
            $tgl_akhir = str_replace('/','-',$explode_date[1]);
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, p.tgl, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
        } else {
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, p.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($status)&&$status!=null){ ($where==null)?null:$where.=" and "; $where.="(p.status = '".$status."')"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(cs.nama like '%".$search."%' or p.kd_trx like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_join_over("tr_ppob p", 'p.kd_trx', "customer cs", "cs.kd_cust=p.member", ($where==null?'':$where));
        $config['per_page'] = 30;
        //$config['attributes'] = array('class' => ''); //attributes anchors
        $config['first_url'] = $config['base_url'];
        $config['num_links'] = 5;
        $config['use_page_numbers'] = TRUE;
        //$config['display_pages'] = FALSE;
        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['first_tag_open'] = '<li>'; $config['first_link'] = '&laquo;'; $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>'; $config['prev_link'] = '&lt;'; $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#"> '; $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>'; $config['num_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>'; $config['next_link'] = '&gt;';  $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>'; $config['last_link'] = '&raquo;'; $config['last_tag_close'] = '</li>';
        $config['full_tag_close'] = '</ul>';
        $this->pagination->initialize($config);
        $data['report'] = $this->m_crud->select_limit_join("tr_ppob p", "p.*, cs.nama nama_member", "customer cs", "cs.kd_cust=p.member", ($where==null?'':$where), 'p.tgl desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $data['det_report'] = $this->m_crud->read_data('Master_Mutasi mm, Det_Mutasi dm, barang br', "mm.tgl_mutasi, mm.no_faktur_mutasi, isnull(mm.no_faktur_beli, '-') no_faktur_beli, mm.kd_lokasi_1, mm.kd_lokasi_2, mm.kd_kasir, mm.status, mm.keterangan, br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual, (SELECT COUNT(no_faktur_mutasi) FROM Det_Mutasi WHERE Det_Mutasi.no_faktur_mutasi=mm.no_faktur_mutasi) baris", "dm.kd_brg=br.kd_brg".($where==null?' ' : ' AND '.$where), 'mm.tgl_mutasi desc', "mm.tgl_mutasi, mm.no_faktur_mutasi, mm.kd_lokasi_1, mm.kd_lokasi_2, mm.kd_kasir, mm.status, mm.no_faktur_beli, mm.keterangan, br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual");
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:M1','A2:M2','A3:M3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:M5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:M5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'No. Alokasi', 'C'=>'Jenis', 'D'=>'Nota Delivery Note', 'E'=>'Lokasi Asal', 'F'=>'Lokasi Tujuan', 'G'=>'Operator', 'H'=>'Status', 'I'=>'Kode Barang', 'J'=>'Barcode', 'K'=>'Nama Barang', 'L'=>'Qty', 'M'=>'Harga Jual'
                )
            );

            $rowspan = 1;
            $start = 6;
            $end = 0;

            foreach($baca as $row => $value){
                if ($rowspan <= 1) {
                    $start = $start + $end;
                    $end = $start + $value['baris'] -1;
                    array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'F'.$start.':F'.$end.'', 'G'.$start.':G'.$end.'', 'H'.$start.':H'.$end.'');
                    $rowspan = $value['baris'];
                    if ($value['baris'] == 1) {
                        $start = 1;
                    }
                }else {
                    $rowspan = $rowspan - 1;
                    $start = 1;
                }

                $body[$row] = array(
                    $value['tgl_mutasi'], $value['no_faktur_mutasi'], (substr($value['no_faktur_mutasi'], 0, 2)=='MU'?'Mutasi':'Branch'), $value['no_faktur_beli'], $value['kd_lokasi_1'], $value['kd_lokasi_2'], $value['kd_kasir'], ($value['status']==0?'Approval':'Approved'), $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['qty'], $value['hrg_jual']
                );
            }

            $header['alignment']['A6:H'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['I6:K'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
