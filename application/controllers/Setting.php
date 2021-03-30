<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends CI_Controller {

	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
		$site_data = $this->m_website->site_data();
		$this->site = str_replace(' ', '', strtolower($site_data->title));
		$this->control = 'Setting';
		
		$this->user = $this->session->userdata($this->site . 'user');
		$this->username = $this->session->userdata($this->site . 'username');
		
		$this->data = array(
			'site' => $site_data,
			'account' => $this->m_website->user_data($this->user),
			'access' => $this->m_website->user_access_data($this->user)
		);
		
		$this->output->set_header("Cache-Control: no-store, no-cache, max-age=0, post-check=0, pre-check=0");
	}
	
	
	public function index(){
		redirect(base_url());
	}
	
	function access_denied($str){
		if(substr($this->m_website->user_access_data($this->user)->access,$str,1) == 0){
			echo "<script>alert('Access Denied'); window.location='".base_url()."site';</script>";
		}	
	}
	
	public function preference($action = null){
		$this->access_denied(0);
		$data = $this->data;
		$function = 'preference';
		$table = 'site';
		$view = $this->control . '/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = 'Pengaturan Perusahaan';
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
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){
				$config['upload_path']          = './assets/images/site';
				$config['allowed_types']        = 'gif|jpg|jpeg|png';
				$config['max_size']             = 5120;
				$this->load->library('upload', $config);
				$input_file = array('1'=>'logo', '2'=>'fav_icon');
				$valid = true;
				foreach($input_file as $row){
					if( (! $this->upload->do_upload($row)) && $_FILES[$row]['name']!=null){
						$file[$row]['file_name']=null;
						$file[$row] = $this->upload->data();
						$valid = false; 
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
						if($_FILES['logo']['name']!=null){
							if($this->input->post('logo_gambar')!=null){ unlink($file['logo']['file_path'].$this->input->post('logo_gambar')); }
							$this->m_crud->update_data($table, array(
								'logo' => $this->config->item('site').'assets/images/site/'.$file['logo']['file_name'],
							), "site_id = '".$_GET['trx']."'");
						} if($_FILES['fav_icon']['name']!=null){
							if($this->input->post('fav_icon_gambar')!=null){ unlink($file['fav_icon']['file_path'].$this->input->post('fav_icon_gambar')); }
							$this->m_crud->update_data($table, array(
								'fav_icon' => $this->config->item('site').'assets/images/site/'.$file['fav_icon']['file_name'],
							), "site_id = '".$_GET['trx']."'");
						} 
						$this->m_crud->update_data($table, array(
							'title' => $_POST['title'],
							'meta_key' => $_POST['meta_key'],
							'meta_descr' => $_POST['meta_descr']
						), "site_id = '".$_GET['trx']."'");
					} else {
						if($_FILES['logo']['name']!=null&&$_FILES['fav_icon']['name']!=null){
							$this->m_crud->create_data($table, array(
								'site_id' => $_POST['site_id'],
								'title' => $_POST['title'],
								'logo' => $this->config->item('site').'assets/images/site/'.$file['logo']['file_name'],
								'fav_icon' => $this->config->item('site').'assets/images/site/'.$file['fav_icon']['file_name'],
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
			$this->load->view('bo/index', $data);
		}
	}

    public function devices($action = null, $page=1){
        $this->access_denied(1);
        $data = $this->data;
        $function = 'devices';
        $table = $function;
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Devices';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('lokasi', 'Lokasi', 'trim|required', array('required' => '%s don`t empty'));

        $where = null;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }

        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "Kode = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->read_data($table, '*',
                "id = '".base64_decode($_GET['trx']."'")."'"
            )[0];
            $get_lokasi = $this->m_crud->get_data("devices","lokasi","id=".base64_decode($_GET['trx']));
            $get_kassa = $this->m_crud->read_data("devices", "kassa", "lokasi='".$get_lokasi['lokasi']."' AND id<>".base64_decode($_GET['trx']));
            $kassa = array();
            foreach ($get_kassa as $row) {
                array_push($kassa, $row['kassa']);
            }
            $data['k'] = $kassa;
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_read_data($table, 'id', $where);
            $config['per_page'] = 10;
            //$config['attributes'] = array('class' => ''); //attributes anchors
            $config['first_url'] = $config['base_url'];
            $config['num_links'] = 5;
            $config['use_page_numbers'] = TRUE;
            //$config['display_pages'] = FALSE;
            $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
            $config['first_tag_open'] = '<li>'; $config['first_link'] = '&laquo;'; $config['first_tag_close'] = '</li>';
            $config['prev_tag_open'] = '<li>'; $config['prev_link'] = '&lt;'; $config['prev_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="active"><a href="#" />'; $config['cur_tag_close'] = '</li>';
            $config['num_tag_open'] = '<li>'; $config['num_tag_close'] = '</li>';
            $config['next_tag_open'] = '<li>'; $config['next_link'] = '&gt;';  $config['next_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>'; $config['last_link'] = '&raquo;'; $config['last_tag_close'] = '</li>';
            $config['full_tag_close'] = '</ul>';
            $this->pagination->initialize($config);
            $data['master_data'] = $this->m_crud->read_data($table, '*, isnull((SELECT printer_series FROM data_printer WHERE printer_model=devices.printer_model AND printer_id=devices.printer_series),\'\') printer_name',
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'id ASC'), null, $config['per_page'], ($page-1)*$config['per_page']
            );
        }
        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                if ($_POST['printer_address'] == '') {
                    $printer_address = null;
                } else {
                    $printer_address = $_POST['printer_address'];
                }
                if ($_POST['printer_model'] == '-') {
                    $printer_model = null;
                } else {
                    $printer_model = $_POST['printer_model'];
                }
                if ($_POST['printer_series'] == '-') {
                    $printer_series = null;
                } else {
                    $printer_series = $_POST['printer_series'];
                }
                if(isset($_POST['update'])){
                    //[Kode] [Nama] [serial] [Ket] [Footer1] [Footer2] [Footer3] [Footer4] [kota] [email] [web] [nama_toko] [phone]
                    if ($_POST['mac_address'] == '__:__:__:__:__:__' || $_POST['mac_address'] == '') {
                        $mac_address = null;
                    } else {
                        $mac_address = $_POST['mac_address'];
                    }
                    $this->m_crud->update_data($table, array(
                        'lokasi' => $_POST['lokasi'],
                        'mac_address' => $mac_address,
                        'jenis_device' => $_POST['jenis_device'],
                        'printer_address' => $printer_address,
                        'printer_model' => $printer_model,
                        'printer_series' => $printer_series,
                        'open_drawer' => $_POST['open_drawer'],
                        'auto_cutter' => $_POST['auto_cutter'],
                        'paper' => $_POST['paper'],
                        'scanner' => $_POST['scanner'],
                        'kassa' => $_POST['kassa'],
                        'fast_pay' => $_POST['fast_pay']
                    ), "id = '".base64_decode($_GET['trx']."'")."'");
                } else {
                    $this->m_crud->create_data($table, array(
                        'lokasi' => $_POST['lokasi'],
                        'device_id' => $_POST['device_id'],
                        'jenis_device' => $_POST['jenis_device'],
                        'printer_address' => $printer_address,
                        'printer_model' => $printer_model,
                        'printer_series' => $printer_series,
                        'open_drawer' => $_POST['open_drawer'],
                        'auto_cutter' => $_POST['auto_cutter'],
                        'paper' => $_POST['paper'],
                        'scanner' => $_POST['scanner'],
                        'kassa' => $_POST['kassa'],
                        'fast_pay' => $_POST['fast_pay']
                    ));

                    $this->m_crud->update_data('device_id', array('status'=>'1'), "device_id='".$_POST['device_id']."'");
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function poin($action = null){
        $this->access_denied(2);
        $data = $this->data;
        $function = 'poin';
        $table = 'setting';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Pengaturan Poin';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['coa_group'] = array();
        $this->form_validation->set_rules('kelipatan', 'Kelipatan', 'trim|required', array('required' => '%s don`t empty'));
        /*if($action == 'delete' && isset($_GET['trx'])){
            $file = $this->m_crud->get_data($table, 'logo, fav_icon', "site_id = '".$_GET['trx']."'");
            if($file['logo']!=''){ unlink('assets/images/site/'.$file['logo']); }
            if($file['fav_icon']!=''){ unlink('assets/images/site/'.$file['fav_icon']); }
            $this->m_crud->delete_data($table, "site_id = '".$_GET['trx']."'");
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else*/ if($action == 'edit'){
            $data['preference'] = $this->m_crud->get_data($table, 'Kode, poin_setting', "Kode = '".base64_decode($_GET['trx'])."'");
        } else {
            $data['preference'] = $this->m_crud->get_data($table, 'Kode, poin_setting', "Kode='1111'");
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }

        if($this->form_validation->run() == false){
            $this->load->view('bo/index', $data);
        } else {
            if(isset($_POST['save'])) {
                $poin_setting = array(
                    'kelipatan' => $_POST['kelipatan'],
                    'nilai' => $_POST['nilai'],
                    'berlaku' => $_POST['berlaku'],
                    'masa' => $_POST['masa'],
                    'minimal' => $_POST['minimal'],
                    'maksimal' => $_POST['maksimal']
                );

                $master = array(
                    'poin_setting' => json_encode($poin_setting)
                );

                $this->m_crud->update_data($table, $master, "Kode='".base64_decode($_GET['trx'])."'");

                $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                foreach ($read_lokasi as $item) {
                    $log = array(
                        'type' => 'U',
                        'table' => $table,
                        'data' => $master,
                        'condition' => "Kode = '".base64_decode($_GET['trx']."'")."'"
                    );

                    $data_log = array(
                        'hostname' => $item['server'],
                        'db_name' => $item['db_name'],
                        'query' => json_encode($log)
                    );
                    $this->m_website->insert_log_api($data_log);
                }

                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function intro($action = null){
        $this->access_denied(3);
        $data = $this->data;
        $function = 'intro';
        $table = 'intro';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Pengaturan Intro';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['coa_group'] = array();
        $this->form_validation->set_rules('judul', 'Judul', 'trim|required', array('required' => '%s don`t empty'));

        if($action == 'edit'){
            $data['res_data'] = $this->m_crud->get_data($table, '*', "id_intro = '".base64_decode($_GET['trx'])."'");
        } else if ($action == 'delete') {
            $this->m_crud->delete_data($table, "id_intro='".base64_decode($_GET['trx'])."'");
            echo '<script>alert("Data berhasil dihapus");window.location="' . base_url() . $this->control . '/' . $function . '"</script>';
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){
            $data['content'] = $view.'form_'.$function;
        } else {
            $data['res_data'] = $this->m_crud->read_data($table, '*');
        }

        if($this->form_validation->run() == false){
            $this->load->view('bo/index', $data);
        } else {
            if(isset($_POST['save'])) {
                $row = 'file_upload';
                $config['upload_path']          = './assets/images/foto';
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
                $config['max_size']             = 5120;
                $this->load->library('upload', $config);
                $valid = true;
                if( (!$this->upload->do_upload($row)) && $_FILES[$row]['name']!=null){
                    $file[$row]['file_name']=null;
                    $file[$row] = $this->upload->data();
                    $valid = false;
                    $data['error_'.$row] = $this->upload->display_errors();
                } else{
                    $file[$row] = $this->upload->data();
                    $data[$row] = $file;
                    if($file[$row]['file_name']!=null){
                        $manipulasi['image_library'] = 'gd2';
                        $manipulasi['source_image'] = $file[$row]['full_path'];
                        $manipulasi['maintain_ratio'] = true;
                        $manipulasi['width']         = 300;
                        //$manipulasi['height']       = 250;
                        $manipulasi['new_image']       = $file[$row]['full_path'];
                        $manipulasi['create_thumb']       = true;
                        //$manipulasi['thumb_marker']       = '_thumb';
                        $this->load->library('image_lib', $manipulasi);
                        $this->image_lib->resize();
                    }
                }

                $master = array(
                    'judul' => $_POST['judul'],
                    'keterangan' => $_POST['keterangan']
                );

                if (isset($_POST['tipe']) && $_POST['tipe']=='1') {
                    if ($_FILES['file_upload']['name']!=null) {
                        $master['background'] = 'assets/images/foto/'.$file['file_upload']['file_name'];
                    }
                    $master['tipe'] = 'foto';
                } else {
                    $master['background'] = $_POST['warna'];
                    $master['tipe'] = 'warna';
                }

                $this->db->trans_begin();

                if (isset($_POST['update'])) {
                    $this->m_crud->update_data($table, $master, "id_intro='" . base64_decode($_GET['trx']) . "'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "Kode = '" . base64_decode($_GET['trx'] . "'") . "'"
                        );

                        $data_log = array(
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                } else {
                    $this->m_crud->create_data($table, $master);

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'I',
                            'table' => $table,
                            'data' => $master,
                            'condition' => ""
                        );

                        $data_log = array(
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                }

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    echo '<script>alert("Save data failed");window.location="' . base_url() . $this->control . '/' . $function . '"</script>';
                } else {
                    $this->db->trans_commit();
                    echo '<script>alert("Data has been Saved");window.location="' . base_url() . $this->control . '/' . $function . '"</script>';
                }
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function slider($action = null){
        $this->access_denied(4);
        $data = $this->data;
        $function = 'slider';
        $table = 'setting';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Pengaturan Slider';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['coa_group'] = array();

        $this->form_validation->set_rules('foto', 'Foto', 'trim', array('required' => '%s don`t empty'));

        if($action == 'edit'){
            $data['preference'] = $this->m_crud->get_data($table, 'Kode, slider', "Kode = '".base64_decode($_GET['trx'])."'");
        } else {
            $data['preference'] = $this->m_crud->get_data($table, 'Kode, slider', "Kode='1111'");
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }

        if($this->form_validation->run() == false){
            $this->load->view('bo/index', $data);
        } else {
            if(isset($_POST['save'])) {
                $row = 'file_upload';
                $config['upload_path']          = './assets/images/foto';
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
                $config['max_size']             = 5120;
                $this->load->library('upload', $config);
                $valid = true;
                if( (!$this->upload->do_upload($row)) && $_FILES[$row]['name']!=null){
                    $file[$row]['file_name']=null;
                    $file[$row] = $this->upload->data();
                    $valid = false;
                    $data['error_'.$row] = $this->upload->display_errors();
                } else{
                    $file[$row] = $this->upload->data();
                    $data[$row] = $file;
                    if($file[$row]['file_name']!=null){
                        $manipulasi['image_library'] = 'gd2';
                        $manipulasi['source_image'] = $file[$row]['full_path'];
                        $manipulasi['maintain_ratio'] = true;
                        $manipulasi['width']         = 300;
                        //$manipulasi['height']       = 250;
                        $manipulasi['new_image']       = $file[$row]['full_path'];
                        $manipulasi['create_thumb']       = true;
                        //$manipulasi['thumb_marker']       = '_thumb';
                        $this->load->library('image_lib', $manipulasi);
                        $this->image_lib->resize();
                    }
                }

                $poin_setting = array(
                    'kelipatan' => $_POST['kelipatan'],
                    'nilai' => $_POST['nilai'],
                    'berlaku' => $_POST['berlaku'],
                    'masa' => $_POST['masa'],
                    'minimal' => $_POST['minimal'],
                    'maksimal' => $_POST['maksimal']
                );

                $master = array(
                    'poin_setting' => json_encode($poin_setting)
                );

                $this->m_crud->update_data($table, $master, "Kode='".base64_decode($_GET['trx'])."'");

                $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                foreach ($read_lokasi as $item) {
                    $log = array(
                        'type' => 'U',
                        'table' => $table,
                        'data' => $master,
                        'condition' => "Kode = '".base64_decode($_GET['trx']."'")."'"
                    );

                    $data_log = array(
                        'hostname' => $item['server'],
                        'db_name' => $item['db_name'],
                        'query' => json_encode($log)
                    );
                    $this->m_website->insert_log_api($data_log);
                }

                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function edit_slide($req = null) {
	    $response = array();
        $param = $_POST['param'];
        $data_slider = $this->m_crud->get_data("setting", "slider", "kode='1111'")['slider'];

        $decode = json_decode($data_slider, true);

        $slider = $decode[$param];

        if ($req == 'get_list') {
            $list = '';
            foreach ($slider as $item) {
                $list .= '
                <tr>
                <td><img src="' . base_url() . $item['foto'] . '" height="80px"></td>
                <td><button class="btn btn-danger" id="hapus' . $item['id'] . '" onclick="hapus_slide(\'' . $item['id'] . '\')"><span class="fa fa-trash"></span> Hapus</button></td>
                </tr>
                ';
            }

            $response['status'] = true;
            $response['list'] = $list;
        } else if ($req == 'hapus') {
            $id = $_POST['id'];

            $key = array_search($id, array_column($slider, 'id'));

            unset($slider[$key]);

            $decode[$param] = array_values($slider);

            $this->m_crud->update_data("setting", array('slider' => json_encode($decode)), "kode='1111'");

            $response['status'] = true;
        } else if ($req == 'simpan') {
            $row = 'gambar';
            $config['upload_path']          = './assets/images/foto';
            $config['allowed_types']        = 'gif|jpg|jpeg|png';
            $config['max_size']             = 5120;
            $this->load->library('upload', $config);
            $valid = false;
            if( (!$this->upload->do_upload($row)) && $_FILES[$row]['name']!=null){
                $file[$row]['file_name']=null;
                $file[$row] = $this->upload->data();
                $response['error'] = $this->upload->display_errors();
            } else{
                $valid = true;
                $file[$row] = $this->upload->data();
                $data[$row] = $file;
                if($file[$row]['file_name']!=null){
                    $manipulasi['image_library'] = 'gd2';
                    $manipulasi['source_image'] = $file[$row]['full_path'];
                    $manipulasi['maintain_ratio'] = true;
                    $manipulasi['width']         = 300;
                    //$manipulasi['height']       = 250;
                    $manipulasi['new_image']       = $file[$row]['full_path'];
                    $manipulasi['create_thumb']       = true;
                    //$manipulasi['thumb_marker']       = '_thumb';
                    $this->load->library('image_lib', $manipulasi);
                    $this->image_lib->resize();
                }
            }

            $gambar = 'assets/images/foto/'.$file['gambar']['file_name'];

            array_push($slider, array('id'=>date('ymdHis'), 'foto'=>$gambar));

            $decode[$param] = $slider;

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $response['result'] = $finfo->file($_FILES[$row]['tmp_name']);

            if ($valid && $_FILES[$row]['name']!=null) {
                $this->m_crud->update_data("setting", array('slider' => json_encode($decode)), "kode='1111'");
            }

            $response['status'] = true;
        }

        echo json_encode($response);
    }

    public function pengirim($action = null){
        $this->access_denied(5);
        $data = $this->data;
        $function = 'pengirim';
        $table = 'setting';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Pengaturan Lokasi Pengirim';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['coa_group'] = array();

        $this->form_validation->set_rules('kecamatan_pengirim', 'Kecamatan', 'trim', array('required' => '%s don`t empty'));

        if($action == 'edit'){
            $data['preference'] = $this->m_crud->get_data($table, 'Kode, kecamatan_pengirim', "Kode = '".base64_decode($_GET['trx'])."'");
        } else {
            $data['preference'] = $this->m_crud->get_data($table, 'Kode, kecamatan_pengirim', "Kode='1111'");
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }

        if($this->form_validation->run() == false){
            $this->load->view('bo/index', $data);
        } else {
            if(isset($_POST['save'])) {
                $master = array(
                    'kecamatan_pengirim' => $_POST['kecamatan_pengirim']
                );

                $this->m_crud->update_data($table, $master, "Kode='".base64_decode($_GET['trx'])."'");

                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function get_kecamatan($req=null) {
	    if ($req == 'simpan') {
            $this->m_crud->update_data("setting", array('kecamatan_pengirim' => $_POST['kecamatan_id']), "kode='1111'");

            echo true;
        } else {
            $get_produk = $this->m_crud->join_data("kecamatan_rajaongkir kr", "('Kecamatan '+kr.kecamatan+' '+kto.tipe+' '+kto.kota+', '+po.provinsi) value, kr.kecamatan_id, kr.kecamatan, (kto.tipe + ' ' + kto.kota) kota, po.provinsi", array("kota_rajaongkir kto", "provinsi_rajaongkir po"), array("kto.kota_id=kr.kota", "po.provinsi_id=kto.provinsi"), "kr.kecamatan like '%" . $_POST['query'] . "%' or kto.kota like '%" . $_POST['query'] . "%' or po.provinsi like '%" . $_POST['query'] . "%'");

            if ($get_produk != null) {
                $result = $get_produk;
            } else {
                $result = array(array('kecamatan' => 'not_found', 'value' => 'Lokasi Tidak Tersedia!'));
            }

            echo json_encode(array("suggestions" => $result));
        }
    }

    public function update_device_id($device_id_) {
        $device_id = base64_decode($device_id_);

        $this->m_crud->update_data('device_id', array('status'=>'0'), "device_id='".$device_id."'");
    }

    public function get_kassa($lokasi_, $id_) {
        $lokasi = base64_decode($lokasi_);
        $id = base64_decode($id_);

        if ($id != '-') {
            $where = "AND id<>'".$id."'";
        } else {
            $where = null;
        }

        $read_lokasi = $this->m_crud->read_data("devices","kassa","lokasi='".$lokasi."' ".$where);

        $s = 'A';
        $k = array();
        foreach ($read_lokasi as $row) {
            array_push($k, $row['kassa']);
        }

        $list = '<option value="-">-- Kassa --</option>';

        while($s != '[')
        {
            if (!in_array($s, $k, true)) {
                $list .= '<option value="'.$s.'">'.$s.'</option>';
            }
            $s = chr(ord($s) + 1);
        }

        echo $list;
    }

    public function get_printer_series($id) {
        $printer_model = base64_decode($id);

        $read_printer_series = $this->m_crud->read_data("data_printer","printer_id, printer_series","printer_model='".$printer_model."'");

        $list = '<option value="-">-- Printer Series --</option>';

        foreach ($read_printer_series as $row) {
            $list .= '<option value="'.$row['printer_id'].'">'.$row['printer_series'].'</option>';
        }

        echo $list;
    }

	public function edit_profile() {
        $data = $this->data;
        $function = 'form_edit_profile';
        $table = 'user_detail';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Edit Profile';
        $data['page'] = 'edit_profile';
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['coa_group'] = array();
        $this->form_validation->set_rules('username', 'username', 'trim|required', array('required' => '%s don`t empty'));
        /*if($action == 'delete' && isset($_GET['id'])){
            $file = $this->m_crud->get_data($table, 'logo, fav_icon', "site_id = '".$_GET['id']."'");
            if($file['logo']!=''){ unlink('assets/images/site/'.$file['logo']); }
            if($file['fav_icon']!=''){ unlink('assets/images/site/'.$file['fav_icon']); }
            $this->m_crud->delete_data($table, "site_id = '".$_GET['id']."'");
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else*/

        $data['preference'] = $this->m_crud->get_data('user_akun, user_detail', 'user_detail.*, user_akun.*', "user_akun.user_id=user_detail.user_id AND user_akun.user_id = '".$_GET['id']."'", 'user_akun.user_id asc');

        if($this->form_validation->run() == false){
            $this->load->view('bo/index', $data);
        } else {
            if(isset($_POST['save'])){
                $row = 'foto';
                $config['upload_path']          = './assets/images/foto';
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
                $config['max_size']             = 5120;
                $this->load->library('upload', $config);
                $valid = true;
                if( (! $this->upload->do_upload($row)) && $_FILES[$row]['name']!=null){
                    $file[$row]['file_name']=null;
                    $file[$row] = $this->upload->data();
                    $valid = false;
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
                if($valid==true){
                    if($_FILES['foto']['name']!=null){
                        $this->m_crud->update_data($table, array(
                            'foto' => $file['foto']['file_name'],
                        ), "user_id = '".$_GET['id']."'");
                    } if($_POST['password']!=null){
                        $this->m_crud->update_data('user_akun', array(
                            'password' => md5($_POST['password']),
                        ), "user_id = '".$_GET['id']."'");
                    }
                    $this->m_crud->update_data($table, array(
                        'nama' => $_POST['nama'],
                        'alamat' => $_POST['alamat'],
                        'email' => $_POST['email'],
                        'nohp' => $_POST['nohp'],
                        'tgl_lahir' => $_POST['tgl_lahir']
                    ), "user_id = '".$_GET['id']."'");
                }
                if($valid == true){ echo '<script>alert("Data has been Saved");window.location="'.base_url().'"</script>'; }
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function deposit($action = null){
        $this->access_denied(6);
        $data = $this->data;
        $function = 'deposit';
        $table = 'setting';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Pengaturan Deposit';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['coa_group'] = array();
        $this->form_validation->set_rules('minimal', 'Minimal', 'trim|required', array('required' => '%s don`t empty'));
        /*if($action == 'delete' && isset($_GET['trx'])){
            $file = $this->m_crud->get_data($table, 'logo, fav_icon', "site_id = '".$_GET['trx']."'");
            if($file['logo']!=''){ unlink('assets/images/site/'.$file['logo']); }
            if($file['fav_icon']!=''){ unlink('assets/images/site/'.$file['fav_icon']); }
            $this->m_crud->delete_data($table, "site_id = '".$_GET['trx']."'");
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else*/ if($action == 'edit'){
            $data['preference'] = $this->m_crud->get_data($table, 'Kode, deposit', "Kode = '".base64_decode($_GET['trx'])."'");
        } else {
            $data['preference'] = $this->m_crud->get_data($table, 'Kode, deposit', "Kode='1111'");
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }

        if($this->form_validation->run() == false){
            $this->load->view('bo/index', $data);
        } else {
            if(isset($_POST['save'])) {
                $deposit_setting = array(
                    'minimal' => $_POST['minimal']
                );

                $master = array(
                    'deposit' => json_encode($deposit_setting)
                );

                $this->m_crud->update_data($table, $master, "Kode='".base64_decode($_GET['trx'])."'");

                $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                foreach ($read_lokasi as $item) {
                    $log = array(
                        'type' => 'U',
                        'table' => $table,
                        'data' => $master,
                        'condition' => "Kode = '".base64_decode($_GET['trx']."'")."'"
                    );

                    $data_log = array(
                        'hostname' => $item['server'],
                        'db_name' => $item['db_name'],
                        'query' => json_encode($log)
                    );
                    $this->m_website->insert_log_api($data_log);
                }

                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }
}

