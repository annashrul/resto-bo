<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master_data extends CI_Controller {
	

	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
		$site_data = $this->m_website->site_data();
		$this->site = str_replace(' ', '', strtolower($site_data->title));
		$this->control = 'Master_data';
		
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
	
	public function index(){
		redirect(base_url());
	} 
	
	function access_denied($str){
		if(substr($this->m_website->user_access_data($this->user)->access,$str,1) == 0){
			echo "<script>alert('Access Denied'); window.location='".base_url()."site';</script>";
		}	
	}
	
	public function user_level($action = null, $page=1){	
		$this->access_denied(11);
		$data = $this->data;
		$function = 'user_level';
		$table = 'user_lvl';
		$view = $this->control.'/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = 'User Level';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$this->form_validation->set_rules('lvl', 'Level', 'trim|required', array('required' => '%s don`t empty'));
		$where = "id <> 1"; 
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			$this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
		}
		$column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort']; 
		if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; } 
		
		if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data($table, "id = '".base64_decode($_GET['trx']."'"));
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['master_data'] = $this->m_crud->read_data($table, '*', 
				//array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'), 
				//array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'), 
				"id = '".base64_decode($_GET['trx']."'")."'"
			)[0];
		} else if($action != "add" && (!isset($_POST['save']))) { 
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data($table, 'id', $where);
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
			$data['master_data'] = $this->m_crud->read_data($table, '*', 
				//array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'), 
				//array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'), 
				$where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null), null, $config['per_page'], ($page-1)*$config['per_page']
			); 
		} 
		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){ 
				$super = null; $access = null;
				for($i=0;$i<=$_POST['jumlah'];$i++){
					$post = $this->input->post($i);	
					if(empty($post)){ $access .= '0'; }
					else{ $access .= $post; }
					$super .= '1';
				}

				$master = array(
                    'lvl' => $this->m_website->replace_kutip(ucwords($_POST['lvl'])),
                    'access' => $access
                );

				$this->m_crud->update_data($table, array('access' => $super), "id = '1'");
                $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                foreach ($read_lokasi as $item) {
                    $log = array(
                        'type' => 'U',
                        'table' => $table,
                        'data' => array('access' => $super),
                        'condition' => "id = '1'"
                    );

                    $data_log = array(
                            'lokasi' => $item['Kode'],
                        'hostname' => $item['server'],
                        'db_name' => $item['db_name'],
                        'query' => json_encode($log)
                    );
                    $this->m_website->insert_log_api($data_log);
                }

				if(isset($_POST['update'])){
					$this->m_crud->update_data($table, $master, "id = '".base64_decode($_GET['trx']."'")."'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "id = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
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
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				}
				echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
			}
			$this->load->view('bo/index', $data);
		}
	}
	
	public function user_list($action = null, $page=1){	
		$this->access_denied(12);
		$data = $this->data;
		$function = 'user_list';
		$table = 'user_akun';
		$view = $this->control.'/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = 'User List';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$this->form_validation->set_rules('username', 'Username', 'trim|required', array('required' => '%s don`t empty'));
		$where = "user_lvl = id and user_akun.user_id = user_detail.user_id and user_lvl <> 1";
//		$where = "user_lvl = id and user_akun.user_id = user_detail.user_id";
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			$this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
		}
		$column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort']; 
		if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; } 
		
		if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data($table, "id = '".base64_decode($_GET['trx']."'"));
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['master_data'] = $this->m_crud->join_data($table, 'user_akun.user_id, nama, username, user_lvl, lokasi, isnull(status, 0) status',
				array('user_lvl', 'user_detail'), 
				array('user_lvl = id', 'user_akun.user_id = user_detail.user_id'), 
				"user_akun.user_id = '".base64_decode($_GET['trx']."'")."'"
			)[0];
			$data['menu_pos'] = $this->m_crud->get_data('Menu_pos', 'Otorisasi', "Uid = '".base64_decode($_GET['trx'])."'");
		} else if($action != "add" && (!isset($_POST['save']))) { 
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data($table.', user_lvl, user_detail', 'user_akun.user_id', $where);
			/*$config['total_rows'] = $this->m_crud->count_data_join($table, 'user_akun.user_id', array('user_lvl', 'user_detail'), array('user_lvl = id', 'user_akun.user_id = user_detail.user_id'), $where);*/
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
            $data['master_data'] = $this->m_crud->select_limit($table.', user_lvl, user_detail', 'user_akun.user_id, nama, username, lvl, lokasi, isnull(status, 0) status, password_otorisasi',
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'nama asc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page)
            );
            /*$data['master_data'] = $this->m_crud->join_data($table, 'user_akun.user_id, nama, username, lvl',
				array('user_lvl', 'user_detail'), 
				array('user_lvl = id', 'user_akun.user_id = user_detail.user_id'), 
				$where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null), null, $config['per_page'], ($page-1)*$config['per_page']
			); */
		}
		if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->read_data($table.', user_lvl, user_detail', 'user_akun.user_id, nama, username, lvl, lokasi, isnull(status, 0) status, password_otorisasi',
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'nama asc'));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:F1','A2:F2','A3:F3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:F5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:F5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Nama', 'B'=>'Username', 'C'=>'User Level', 'D'=>'Lokasi', 'E'=>'Status', 'F'=>'Otorisasi'
                )
            );

            foreach($baca as $row => $value){
                $array_lokasi = array();
                $data_lokasi = json_decode($value['lokasi'], true);
                for ($i = 0; $i < count($data_lokasi['lokasi_list']); $i++) {
                    array_push($array_lokasi, $data_lokasi['lokasi_list'][$i]['kode']);
                }
                sort($array_lokasi);

                $body[$row] = array(
                    $value['nama'], $value['username'], $value['lvl'], implode(', ', $array_lokasi), ($value['status']==1?'Aktif':'Tidak Aktif'), ($value['password_otorisasi']==1?'Tersedia':'Tidak Tersedia')
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }
		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){
		    $this->load->view('bo/index', $data);
		} else {
			if(isset($_POST['save'])){ 
				$this->db->trans_begin();

                $data_lokasi = array();
                $lokasi["lokasi_list"] = array();
                for ($i=0; $i<count($_POST['lokasi']); $i++) {
                    $data = array();
                    $data["kode"] = $_POST['lokasi'][$i];
                    array_push($lokasi["lokasi_list"], $data);
                    array_push($data_lokasi, $_POST['lokasi'][$i]);
                }
				$super = null; $access = null;
				for($i=0;$i<=$_POST['jumlah'];$i++){
					$post = $this->input->post($i);	
					if(empty($post)){ $access .= '0'; }
					else{ $access .= $post; }
					$super .= '1';
				}
				//$this->m_crud->update_data($table, array('Otorisasi' => $super), "Uid = 'netindo'"); Uid] => ADMIN [Otorisasi]

                if ($this->user == base64_decode($_GET['trx'])) {
                    $this->session->set_userdata($this->site . 'lokasi', $lokasi['lokasi_list']);
                }

                $master = array(
                    'username' => $this->m_website->replace_kutip(strtolower($_POST['username'])),
                    'user_lvl' => $_POST['user_lvl'],
                    'status' => $_POST['status'],
                    'lokasi' => json_encode($lokasi)
                );

                $user_detail = array(
                    'nama' => $this->m_website->replace_kutip(ucwords($_POST['nama']))
                );

                $menu_pos = array(
                    'Otorisasi' => $access
                );

                $pemakaian = array(
                    'nama' => $_POST['nama'],
                    'otorisasi' => $this->m_crud->get_data('user_lvl','lvl',"id = ".$_POST['user_lvl'])['lvl']
                );

				if (isset($_POST['update'])) {
				    if($_POST['password_otorisasi']!=''){
                        $master['password_otorisasi'] = $_POST['password_otorisasi'];
                    }

					if($_POST['password']!=''){
                        $master['password'] = md5($_POST['password']);
					}

                    $this->m_crud->update_data($table, $master, "user_id = '".base64_decode($_GET['trx']."'")."'");

					$this->m_crud->update_data('user_detail', $user_detail, "user_id = '".base64_decode($_GET['trx']."'")."'");

					$this->m_crud->update_data('Menu_pos', $menu_pos, "Uid = '".base64_decode($_GET['trx']."'")."'");

					$this->m_crud->update_data('pemakaian', $pemakaian, "uid = '".base64_decode($_GET['trx'])."'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        if (in_array($item['Kode'], $data_lokasi)) {
                            $master['status'] = $_POST['status'];
                        } else {
                            $master['status'] = '0';
                        }
                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode(array(
                                    'type' => 'U',
                                    'table' => $table,
                                    'data' => $master,
                                    'condition' => "user_id = '".base64_decode($_GET['trx']."'")."'")
                            )
                        );
                        $this->m_website->insert_log_api($data_log);

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode(array(
                                    'type' => 'U',
                                    'table' => 'user_detail',
                                    'data' => $user_detail,
                                    'condition' => "user_id = '".base64_decode($_GET['trx']."'")."'")
                            )
                        );
                        $this->m_website->insert_log_api($data_log);

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode(array(
                                    'type' => 'U',
                                    'table' => 'menu_pos',
                                    'data' => $menu_pos,
                                    'condition' => "Uid = '".base64_decode($_GET['trx']."'")."'")
                            )
                        );
                        $this->m_website->insert_log_api($data_log);

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode(array(
                                    'type' => 'U',
                                    'table' => 'pemakaian',
                                    'data' => $pemakaian,
                                    'condition' => "uid = '".base64_decode($_GET['trx'])."'")
                            )
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				} else {
                    $master['user_id'] = $_POST['username'];
                    $master['password'] = md5($_POST['password']);
                    $master['password_otorisasi'] = $_POST['password_otorisasi']==''?'-':$_POST['password_otorisasi'];
					$this->m_crud->create_data($table, $master);

					$user_detail = array(
                        'user_id' => $_POST['username'],
                        'nama' => $_POST['nama'],
                        'alamat' => '-',
                        'email' => '-',
                        'nohp' => 0,
                        'tgl_lahir' => date('Y-m-d')
                    );
					$this->m_crud->create_data('user_detail', $user_detail);

					$menu_pos['Uid'] = $_POST['username'];
					$this->m_crud->create_data('Menu_pos', $menu_pos);

					$pemakaian['uid'] = $_POST['username'];
					$this->m_crud->create_data('pemakaian', $pemakaian);

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        if (in_array($item['Kode'], $data_lokasi)) {
                            $master['status'] = $_POST['status'];
                        } else {
                            $master['status'] = '0';
                        }
                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode(array(
                                'type' => 'I',
                                'table' => $table,
                                'data' => $master,
                                'condition' => "")
                            )
                        );
                        $this->m_website->insert_log_api($data_log);

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode(array(
                                'type' => 'I',
                                'table' => 'user_detail',
                                'data' => $user_detail,
                                'condition' => "")
                            )
                        );
                        $this->m_website->insert_log_api($data_log);

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode(array(
                                'type' => 'I',
                                'table' => 'menu_pos',
                                'data' => $menu_pos,
                                'condition' => "")
                            )
                        );
                        $this->m_website->insert_log_api($data_log);

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode(array(
                                'type' => 'I',
                                'table' => 'pemakaian',
                                'data' => $pemakaian,
                                'condition' => "")
                            )
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				}
				if ($this->db->trans_status() === FALSE){ $this->db->trans_rollback(); 
				} else {
					$this->db->trans_commit(); $this->cart->destroy();
					echo '<script>alert("Data has been Saved");window.location="'.base_url('Master_data/user_list').'"</script>';
				}
			}
			$this->load->view('bo/index', $data);
		}
	}
	
	public function kategori_lokasi($action = null, $page=1){	
		$this->access_denied(13);
		$data = $this->data;
		$function = 'kategori_lokasi';
		$table = 'lokasi_ktg';
		$view = $this->control.'/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = 'Kategori Lokasi';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$this->form_validation->set_rules('nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
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
				//array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'), 
				//array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'), 
				"id_".$table." = '".base64_decode($_GET['trx']."'")."'"
			)[0];
		} else if($action != "add" && (!isset($_POST['save']))) { 
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data($table, 'id_'.$table, $where);
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
			$data['master_data'] = $this->m_crud->read_data($table, '*', 
				//array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'), 
				//array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'), 
				$where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null), null, $config['per_page'], ($page-1)*$config['per_page']
			); 
		} 
		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){ 
				if(isset($_POST['update'])){
                    $master = array(
                        'nama' => $_POST['nama'],
                    );
					$this->m_crud->update_data($table, $master, "id_".$table." = '".base64_decode($_GET['trx']."'")."'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "id_".$table." = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				} else {
				    $master = array(
                        'nama' => $_POST['nama'],
                    );
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
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				}
				echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
			}
			$this->load->view('bo/index', $data);
		}
	}
	
	public function data_lokasi($action = null, $page=1){	
		$this->access_denied(14);
		$data = $this->data;
		$function = 'data_lokasi';
		$table = 'Lokasi';
		$view = $this->control.'/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = 'Data Lokasi';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$this->form_validation->set_rules('Nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
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
			$data['master_data'] = $this->m_crud->join_data($table, 'Kode, Lokasi.Nama, serial, lokasi_ktg, Ket, Footer1, Footer2, Footer3, Footer4, kota, email, web, nama_toko, phone, server, db_name, lat, lng, gambar, status_show',
				array('lokasi_ktg'), 
				array('lokasi_ktg = id_lokasi_ktg'), 
				"Kode = '".base64_decode($_GET['trx']."'")."'"
			)[0];
		} else if($action != "add" && (!isset($_POST['save']))) { 
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data_join($table, 'Kode', array('lokasi_ktg'), array('lokasi_ktg = id_lokasi_ktg'), $where);
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
			$data['master_data'] = $this->m_crud->select_limit_join($table, 'Kode, Lokasi.Nama, serial, lokasi_ktg.nama as kategori, Ket, Footer1, Footer2, Footer3, Footer4, kota, email, web, nama_toko, phone, server, db_name, gambar, status_show',
				array('lokasi_ktg'), 
				array('lokasi_ktg = id_lokasi_ktg'),
				$where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'Kode ASC'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page)
			); 
		}

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->join_data($table, 'Kode, Lokasi.Nama, serial, lokasi_ktg.nama as kategori, Ket, Footer1, Footer2, Footer3, Footer4, kota, email, web, nama_toko, phone',
                array('lokasi_ktg'),
                array('lokasi_ktg = id_lokasi_ktg'),
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'Kode ASC'));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:N1','A2:N2','A3:N3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:N5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:N5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama', 'C'=>'Serial', 'D'=>'Kategori', 'E'=>'Keterangan', 'F'=>'Footer 1', 'G'=>'Footer 2', 'H'=>'Footer 3', 'I'=>'Footer 4', 'J'=>'Kota', 'K'=>'Email', 'L'=>'Web', 'M'=>'Nama Toko', 'N'=>'Telepon'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['Kode'], $value['Nama'], $value['serial'], $value['kategori'], $value['ket'], $value['Footer1'], $value['Footer2'], $value['Footer3'], $value['Footer4'], $value['kota'], $value['email'], $value['web'], $value['nama_toko'], $value['phone']
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){
                $row = 'gambar';
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
                    'Nama' => $_POST['Nama'],
                    'lokasi_ktg' => $_POST['lokasi_ktg'],
                    'Ket' => $_POST['Ket'],
                    'Footer1' => $_POST['Footer1'],
                    'Footer2' => $_POST['Footer2'],
                    'Footer3' => $_POST['Footer3'],
                    'Footer4' => $_POST['Footer4'],
                    'kota' => $_POST['kota'],
                    'email' => $_POST['email'],
                    'web' => $_POST['web'],
                    'nama_toko' => $_POST['Nama'],
                    'phone' => $_POST['phone'],
                    'server' => $_POST['server'],
                    'db_name' => $_POST['db_name'],
                    'lat' => $_POST['lat'],
                    'lng' => $_POST['lng'],
                    'status_show' => $_POST['status_show']
                );

                if ($_FILES['gambar']['name']!=null) {
                    $master['gambar'] = 'assets/images/foto/'.$file['gambar']['file_name'];
                }

				if(isset($_POST['update'])){
					$this->m_crud->update_data($table, $master, "Kode = '".base64_decode($_GET['trx']."'")."'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "Kode = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				} else {
                    $get_serial = $this->m_crud->get_data("lokasi", "MAX(serial) serial");
                    $new_serial = ++$get_serial['serial'];

                    //$get_toko = $this->m_crud->get_data("bo_npos.dbo.toko tk, bo_npos.dbo.user_akun ua", "tk.id_toko, tk.nama_toko, tk.status, tk.tgl_jatuh_tempo", "ua.toko=tk.id_toko AND ua.user_id='".$this->user."'");
                    $max_code = $this->m_crud->get_data("Lokasi", "MAX(CAST(RIGHT(Kode, 4) as INT)) max_kode", "ISNUMERIC(RIGHT(Kode, 4))=1")['max_kode'];
                    $new_code = "LK/".sprintf('%04d', (int)$max_code+1);
                    $master['Kode'] = $new_code;
                    $master['serial'] = $new_serial;
                    $master['status'] = '1';
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
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				}
				echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
			}
			$this->load->view('bo/index', $data);
		}
	}
	
	public function group2($action = null, $page=1){	
		$this->access_denied(15);
		$data = $this->data;
		$function = 'group2';
		$table = 'Group2';
		$view = $this->control.'/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = $this->m_crud->get_data('Setting', 'as_group2', "Kode = '1111'")['as_group2'];
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$this->form_validation->set_rules('Kode', 'Kode', 'trim|required', array('required' => '%s don`t empty'));
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
			$data['master_data'] = $this->m_crud->get_data($table, '*', "Kode = '".base64_decode($_GET['trx']."'")."'");
		} else if($action != "add" && (!isset($_POST['save']))) { 
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data($table, 'Kode', $where);
			$config['per_page'] = 50;
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
			$data['master_data'] = $this->m_crud->select_limit($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'Kode asc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
		}

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->read_data($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:B1','A2:B2','A3:B3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:B5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:B5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['Kode'], $value['Nama']
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){ 
				if(isset($_POST['update'])){
				    $master = array(
                        'Kode' => $_POST['Kode'],
                        'Nama' => $_POST['Nama']
                    );
					$this->m_crud->update_data($table, $master, "Kode = '".base64_decode($_GET['trx']."'")."'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "Kode = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				} else {
				    $master = array(
                        'Kode' => $_POST['Kode'],
                        'Nama' => $_POST['Nama']
                    );
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
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				}
				echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
			}
			$this->load->view('bo/index', $data);
		}
	}
	
	public function group1($action = null, $page=1){	
		$this->access_denied(16);
		$data = $this->data;
		$function = 'group1';
		$table = 'Group1';
		$view = $this->control.'/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = $this->m_crud->get_data('Setting', 'as_group1', "Kode = '1111'")['as_group1'];
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$this->form_validation->set_rules('Kode', 'Kode', 'trim|required', array('required' => '%s don`t empty'));
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
			$data['master_data'] = $this->m_crud->get_data($table, '*', "Kode = '".base64_decode($_GET['trx']."'")."'");
		} else if($action != "add" && (!isset($_POST['save']))) { 
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data($table, 'Kode', $where);
			$config['per_page'] = 50;
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
			$data['master_data'] = $this->m_crud->select_limit($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'Kode asc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
		}

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->read_data($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:B1','A2:B2','A3:B3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:B5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:B5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['Kode'], $value['Nama']
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){
				$this->db->trans_begin();
				if(isset($_POST['update'])){
				    $master = array(
                        'Kode' => $_POST['Kode'],
                        'Nama' => $_POST['Nama'],
                    );
					$this->m_crud->update_data($table, $master, "Kode = '".base64_decode($_GET['trx']."'")."'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "Kode = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				} else {
				    $master = array(
                        'Kode' => $_POST['Kode'],
                        'Nama' => $_POST['Nama']
                    );
					$this->m_crud->create_data($table, $master);
					$this->m_crud->create_data('Supplier', $master);

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'I',
                            'table' => $table,
                            'data' => $master,
                            'condition' => ""
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				}
				if ($this->db->trans_status() === FALSE){ $this->db->trans_rollback(); 
				} else {
					$this->db->trans_commit(); $this->cart->destroy();
					echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
				}
			}
			$this->load->view('bo/index', $data);
		}
	}
	
	public function kelompok_barang($action = null, $page=1){	
		$this->access_denied(17);
		$data = $this->data;
		$function = 'kelompok_barang';
		$table = 'kel_brg';
		$view = $this->control.'/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = 'Kelompok Barang';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$this->form_validation->set_rules('kel_brg', 'Kode', 'trim|required', array('required' => '%s don`t empty'));
		$where = null; 
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			$this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
		}
		$column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort']; 
		if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; } 
		
		if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data($table, "kel_brg = '".base64_decode($_GET['trx']."'"));
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['master_data'] = $this->m_crud->get_data($table, '*', "kel_brg = '".base64_decode($_GET['trx']."'")."'");
		} else if($action != "add" && (!isset($_POST['save']))) { 
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data_join($table, 'kel_brg', 'Group2', 'Group2 = Kode', $where);
			$config['per_page'] = 50;
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
			$data['master_data'] = $this->m_crud->select_limit_join($table, '*', 'Group2', 'Group2 = Kode', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'kel_brg asc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
		}

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->join_data($table, '*', 'Group2', 'Group2 = Kode', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'kel_brg asc'));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:D1','A2:D2','A3:D3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:D5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:D5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama', 'C'=>'Status', 'D'=>'Sub Dept'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['kel_brg'], $value['nm_kel_brg'], ($value['status']==1?'Aktif':'Tidak Aktif'), $value['Nama']
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){
                $row = 'gambar';
                $config['upload_path']          = './assets/images/barang';
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
                    'nm_kel_brg' => $_POST['nm_kel_brg'],
                    'margin' => $_POST['margin'],
                    'status' => $_POST['status'],
                    'Group2' => $_POST['Group2']
                );

                if ($_FILES['gambar']['name']!=null) {
                    $master['gambar'] = 'assets/images/barang/'.$file['gambar']['file_name'];
                }

                if ($valid) {
                    if (isset($_POST['update'])) {
                        $this->m_crud->update_data($table, $master, "kel_brg = '" . base64_decode($_GET['trx'] . "'") . "'");

                        $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                        foreach ($read_lokasi as $item) {
                            $log = array(
                                'type' => 'U',
                                'table' => $table,
                                'data' => $master,
                                'condition' => "kel_brg = '" . base64_decode($_GET['trx'] . "'") . "'"
                            );

                            $data_log = array(
                                'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }
                    } else {
                        $master['kel_brg'] = $_POST['kel_brg'];
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
                                'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }
                    }
                    echo '<script>alert("Data has been Saved"); window.location="'.base_url().$this->control.'/'.$function.'"</script>';
                } else {
                    echo '<script>alert("Data gagal disimpan"); window.location="'.base_url().$this->control.'/'.$function.'"</script>';
                }
			}
			$this->load->view('bo/index', $data);
		}
	}
	
	public function delete_barang($table, $column, $id){
		$id = base64_decode($id);
		$cek = $this->m_crud->get_data('kartu_stock', 'kd_brg', "kd_brg = '".$id."' and keterangan <> 'Input Barang'");
		if($cek == null){
            $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
            $this->m_crud->delete_data($table, $column." = '".$id."'");
			$this->m_crud->delete_data('kartu_stock', $column." = '".$id."'");
			$this->m_crud->delete_data('barang_hrg', "barang = '".$id."'");
            foreach ($read_lokasi as $item) {
                $log = array(
                    'type' => 'D',
                    'table' => $table,
                    'data' => "",
                    'condition' => $column." = '".$id."'"
                );

                $data_log = array(
                    'lokasi' => $item['Kode'],
                    'hostname' => '',
                    'db_name' => '',
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);

                $log = array(
                    'type' => 'D',
                    'table' => "kartu_stock",
                    'data' => "",
                    'condition' => $column." = '".$id."'"
                );

                $data_log = array(
                    'lokasi' => $item['Kode'],
                    'hostname' => '',
                    'db_name' => '',
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);

                $log = array(
                    'type' => 'D',
                    'table' => "barang_hrg",
                    'data' => "",
                    'condition' => "barang = '".$id."'"
                );

                $data_log = array(
                    'lokasi' => $item['Kode'],
                    'hostname' => '',
                    'db_name' => '',
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);
            }
			//echo json_encode(array('status'=>true));
			echo true;
		} else {
			//echo json_encode(array('status'=>false));
			echo false;
		}
		
	}

    public function data_barang($action = null, $page=1){
        $this->access_denied(18);
        $data = $this->data;
        $function = 'data_barang';
        $table = 'barang';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Barang';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $this->form_validation->set_rules('kd_brg', 'Kode Barang', 'trim|required', array('required' => '%s don`t empty'));

        $where = 'barang.Group1 = g1.Kode and barang.Group2 = g2.Kode and barang.kel_brg = kb.kel_brg';

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort'], 'field-date'=>$_POST['field-date'], 'periode'=>$_POST['periode'], 'kd_brg'=>$_POST['kd_brg'], 'barcode'=>$_POST['barcode'], 'nm_brg'=>$_POST['nm_brg'], 'Deskripsi'=>$_POST['Deskripsi'], 'kb_nm_kel_brg'=>$_POST['kb_nm_kel_brg'], 'kd_packing'=>$_POST['kd_packing'], 'g1_Nama'=>$_POST['g1_Nama'], 'g2_Nama'=>$_POST['g2_Nama'], 'jns_brg'=>$_POST['jns_brg']));
        }

        $column = $this->session->search['column']; $any = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort']; $date = $this->session->search['field-date']; $periode = $this->session->search['periode']; $kd_brg = $this->session->search['kd_brg']; $kd_packing = $this->session->search['kd_packing']; $barcode = $this->session->search['barcode']; $nm_brg = $this->session->search['nm_brg']; $deskripsi = $this->session->search['Deskripsi']; $kb_nm_kel_brg = $this->session->search['kb_nm_kel_brg']; $g1_nama = $this->session->search['g1_Nama']; $g2_nama = $this->session->search['g2_Nama']; $jns_brg = $this->session->search['jns_brg'];

        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        if(!isset($periode) && $periode==null) {
            if (isset($date) && $date != null) {
                ($where == null) ? null : $where .= " and ";
                $where .= "LEFT(CONVERT(VARCHAR, tgl_input, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
            } else {
                ($where == null) ? null : $where .= " and ";
                $where .= "LEFT(CONVERT(VARCHAR, tgl_input, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
            }
        }
        //if(isset($any)&&$any!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$any."%')"; }
        if(isset($kd_brg)&&$kd_brg!=null){ ($where==null)?null:$where.=" and "; $where.="(kd_brg like '%".$kd_brg."%')"; }
        if(isset($barcode)&&$barcode!=null){ ($where==null)?null:$where.=" and "; $where.="(barcode like '%".$barcode."%')"; }
        if(isset($nm_brg)&&$nm_brg!=null){ ($where==null)?null:$where.=" and "; $where.="(nm_brg like '%".$nm_brg."%')"; }
        if(isset($deskripsi)&&$deskripsi!=null){ ($where==null)?null:$where.=" and "; $where.="(Deskripsi like '%".$deskripsi."%')"; }
        if(isset($kd_packing)&&$kd_packing!=null){ ($where==null)?null:$where.=" and "; $where.="(kd_packing like '%".$kd_packing."%')"; }
        if(isset($kb_nm_kel_brg)&&$kb_nm_kel_brg!=null){ ($where==null)?null:$where.=" and "; $where.="(kb.nm_kel_brg like '%".$kb_nm_kel_brg."%')"; }
        if(isset($g1_nama)&&$g1_nama!=null){ ($where==null)?null:$where.=" and "; $where.="(g1.Nama like '%".$g1_nama."%')"; }
        if(isset($g2_nama)&&$g2_nama!=null){ ($where==null)?null:$where.=" and "; $where.="(g2.Nama like '%".$g2_nama."%')"; }
        if(isset($jns_brg)&&$jns_brg!=null){
            if ($jns_brg == '1') {
                ($where==null)?null:$where.=" and "; $where.="(Jenis = 'Barang Dijual')";
            } else {
                ($where==null)?null:$where.=" and "; $where.="(Jenis = 'Barang Tidak Dijual')";
            }
        }
        if (isset($_POST['to_excel'])) {
            //            $baca = $this->m_crud->join_data(
            //                $table, 'kd_brg, barcode, nm_brg, Deskripsi, nm_kel_brg, kd_packing, berat, qty_packing, g1.Nama as nm_Group1, online, g2.Nama as nm_Group2, barang.gambar, satuan, hrg_beli, stock_min, kategori, Jenis, barang_online, isnull(fav, 0) fav, isnull(poin, 0) poin',
            //                $join,
            //                $on,
            //                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'kd_brg asc'),null
            //            );
            $baca = $this->m_crud->read_data(
                $table.', Group1 as g1, Group2 as g2, kel_brg as kb',
                'kd_brg, barcode, nm_brg, Deskripsi, nm_kel_brg, kd_packing, berat, qty_packing, g1.Nama as nm_Group1, online, g2.Nama as nm_Group2, barang.gambar, satuan, hrg_beli, stock_min, kategori, Jenis, barang_online, isnull(fav, 0) fav, isnull(poin, 0) poin',
                $where,
                (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'kd_brg asc'),
                null,
                null
            );
            //
            $header = array(
                'merge' 	=> array('A1:P1','A2:P2','A3:P3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:P5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:I5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kode Barang', 'B'=>'Barcode', 'C'=>'Nama Barang', 'D'=>'Deskripsi', 'E'=>'Kelompok Barang', 'F'=>'Group 1', 'G'=>'Group 2',
                    'H'=>'Satuan', 'I'=>'Harga Beli','J'=>'Stock Min','K'=>'Berat','L'=>'Kategori','M'=>'Jenis Barang','N'=>'Barang Online','O'=>'Favorite','P'=>'Poin'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['kd_brg'],
                    $value['barcode'],
                    $value['nm_brg'],
                    $value['Deskripsi'],
                    $value['nm_kel_brg'],
                    $value['nm_Group1'],
                    $value['nm_Group2'],
                    $value['satuan'],
                    $value['hrg_beli'],
                    $value['stock_min'],
                    $value['berat'],
                    $value['kategori'],
                    $value['Jenis'],
                    $value['online']=='1'?'Ya':'Tidak',
                    $value['fav']=='1'?'Ya':'Tidak',
                    $value['poin']=='1'?'Ya':'Tidak',

                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);

            //            $header = array(
            //                'merge' 	=> array('A1:P1','A2:P2','A3:P3'),
            //                'auto_size' => true,
            //                'font' 		=> array(
            //                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
            //                    'A3' => array('bold'=>true,'name'=>'Verdana'),
            //                    'A5:P5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
            //                ),
            //                'alignment' => array(
            //                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            //                    'A5:I5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            //                ),
            //                '1' => array('A' => $data['site']->title),
            //                '2' => array('A' => $data['title']),
            //                '5' => array(
            //                    'A'=>'Kode Barang', 'B'=>'Barcode', 'C'=>'Nama Barang', 'D'=>'Deskripsi', 'E'=>'Kelompok Barang', 'F'=>'Group 1', 'G'=>'Group 2',
            //                    'H'=>'Satuan', 'I'=>'Harga Beli','J'=>'Stock Min','K'=>'Berat','L'=>'Kategori','M'=>'Jenis Barang','N'=>'Barang Online','O'=>'Favorite','P'=>'Poin'
            //                )
            //            );
            //
            //            $rowspan = 1;
            //            $start = 6;
            //            $end = 0;
            //
            //            foreach($baca as $row => $value){
            //                if ($rowspan <= 1) {
            //                    $start = $start + $end;
            //                    $end = $start + $value['baris'] -1;
            //                    array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'F'.$start.':F'.$end.'');
            //                    $rowspan = $value['baris'];
            //                    if ($value['baris'] == 1) {
            //                        $start = 1;
            //                    }
            //                }else {
            //                    $rowspan = $rowspan - 1;
            //                    $start = 1;
            //                }
            //
            //                $body[$row] = array(
            //                    $value['kd_brg'],
            //                    $value['barcode'],
            //                    $value['nm_brg'],
            //                    $value['Deskripsi'],
            //                    $value['nm_kel_brg'],
            //                    $value['nm_Group1'],
            //                    $value['nm_Group2'],
            //                    $value['satuan'],
            //                    $value['hrg_beli'],
            //                    $value['stock_min'],
            //                    $value['berat'],
            //                    $value['kategori'],
            //                    $value['Jenis'],
            //                    $value['online']=='1'?'Ya':'Tidak',
            //                    $value['fav']=='1'?'Ya':'Tidak',
            //                    $value['poin']=='1'?'Ya':'Tidak',
            ////                    $value['tgl_beli'], $value['no_faktur_beli'], $value['type'], $value['Lokasi'], $value['noNota'], $value['Operator'], $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['jumlah_beli'], $value['harga_beli'], $value['Pelunasan']
            //                );
            //            }
            //
            //            $header['alignment']['A6:F'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            //            $header['alignment']['G6:I'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            //
            //            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);

        }
        $data['master_data_detail'] = array();
        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "kel_brg = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        }
        else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->join_data($table, 'kd_brg, barcode, nm_brg, Deskripsi, barang.gambar, barang.kel_brg, online, Group1, kd_packing, qty_packing, barang.Group2, satuan, isnull(hrg_beli, 0) hrg_beli, isnull(hrg_jual_1, 0) hrg_jual_1, diskon, service, poin, kcp, PPN, stock_min, kategori, Jenis, barang_online, berat',
                array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'),
                array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'),
                "kd_brg = '".base64_decode($_GET['trx']."'")."'"
            )[0];
            $data['master_data_detail'] = $this->m_crud->read_data('barang_hrg', 'id_barang_hrg, hrg_jual_1, hrg_jual_2, hrg_jual_3, hrg_jual_4, disc1, ppn, lokasi, service, ',
                "barang = '".base64_decode($_GET['trx']."'")."'"
            );
        }
        else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data($table.', Group1 as g1, Group2 as g2, kel_brg as kb', 'kd_brg', $where);
            /*$config['total_rows'] = $this->m_crud->count_data_join($table, 'kd_brg', array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'), array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'), $where);*/
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
            $data['master_data'] = $this->m_crud->select_limit(
                $table.', Group1 as g1, Group2 as g2, kel_brg as kb', 'kd_brg, barcode, nm_brg, Deskripsi, nm_kel_brg, kd_packing, berat, qty_packing, g1.Nama as nm_Group1, online, g2.Nama as nm_Group2, barang.gambar, satuan, hrg_beli, stock_min, kategori, Jenis, barang_online, isnull(fav, 0) fav, isnull(poin, 0) poin',
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'kd_brg asc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page)
            );
            /*$data['master_data'] = $this->m_crud->join_data($table, 'kd_brg, barcode, nm_brg, Deskripsi, nm_kel_brg, g1.Nama as nm_Group1, g2.Nama as nm_Group2, satuan, hrg_beli, stock_min, kategori, Jenis',
                array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'),
                array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'),
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null), null, $config['per_page'], ($page-1)*$config['per_page']
            );*/
        }
        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($action == 'import'){$data['content'] = $view.'form_import_barang';}

        if($this->form_validation->run() == false){
            $this->load->view('bo/index', $data);
        } else {
            if(isset($_POST['save'])){
                $max_data = $_POST['max_data'];
                $row = 'gambar';
                $config['upload_path']          = './assets/images/barang';
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

                $this->db->trans_begin();
                $nama_barang = ucwords(str_replace("'", "`", $_POST['nm_brg']));

                if(isset($_POST['update'])) {
                    $hrg_lama = $this->m_crud->get_data("barang", "hrg_jual_1", "kd_brg = '".base64_decode($_GET['trx']."'")."'");
                    if($_POST['Deskripsi']!='' || $_POST['Deskripsi']!=null || $_POST['Deskripsi']!='null') $deskripsi=$_POST['Deskripsi'];
                    else $deskripsi = '-';
                    $data_barang = array(
                        'barcode' => $_POST['barcode'],
                        'nm_brg' => $nama_barang,
                        'Deskripsi' => $deskripsi,
                        'kel_brg' => $_POST['kel_brg'],
                        'Group1' => $_POST['Group1'],
                        'Group2' => $_POST['Group2'],
                        'satuan' => strtoupper($_POST['satuan']),
                        'hrg_beli' => $_POST['hrg_beli'],
                        'stock_min' => $_POST['stock_min'],
                        'kategori' => $_POST['kategori'],
                        'Jenis' => $_POST['Jenis'],
                        'kd_packing' => $_POST['kd_packing'],
                        'qty_packing' => $_POST['qty_packing'],
                        'barang_online' => isset($_POST['online'])?1:0,
                        'tgl_update' => date('Y-m-d H:i:s'),
                        'hrg_jual_1' => ($_POST['master_hrg_jual_1']!=null)?$_POST['master_hrg_jual_1']:0,
                        'hrg_jual_2' => ($_POST['master_hrg_jual_1']!=null)?$_POST['master_hrg_jual_1']:0,
                        'hrg_jual_3' => 0,
                        'hrg_jual_4' => 0,
                        'diskon' => 0,
                        'service' => ($_POST['master_service']!=null)?$_POST['master_service']:0,
                        'PPN' => ($_POST['master_ppn']!=null)?$_POST['master_ppn']:0,
                        'hrg_sebelum' => $hrg_lama['hrg_jual_1'],
                        'kcp' => $_POST['kcp'],
                        'poin' => $_POST['poin'],
                        'berat' => $_POST['berat'],
                        'online' => isset($_POST['online'])?$_POST['online']:0
                    );

                    if($_FILES['gambar']['name']!=null){
                        $data_barang['gambar'] = $this->config->item('site').'assets/images/barang/'.$file['gambar']['file_name'];
                    }

                    $this->m_crud->update_data($table, $data_barang, "kd_brg = '".base64_decode($_GET['trx']."'")."'");

                    unset($data_barang['Jenis']);
                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => 'barang',
                            'data' => $data_barang,
                            'condition' => "kd_brg = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }

                    $no = 0;
                    for($i=1; $i<=$_POST['jumlah_lokasi']; $i++) {
                        if($_POST['cek_lokasi'.$i]==1){
                            $brg_lokasi = array(
                                'barang' => $_POST['kd_brg'],
                                'lokasi' => $_POST['lokasi'.$i],
                                'hrg_jual_1' => ($_POST['hrg_jual_1'.$i]!=null)?$_POST['hrg_jual_1'.$i]:0,
                                'hrg_jual_2' => 0,
                                'hrg_jual_3' => 0,
                                'hrg_jual_4' => 0,
                                'service' => ($_POST['service'.$i]!=null)?$_POST['service'.$i]:0,
                                'ppn' => ($_POST['ppn'.$i]!=null)?$_POST['ppn'.$i]:0
                            );

                            if($_POST['id_barang_hrg'.$i] != null){
                                $condition = "barang = '".base64_decode($_GET['trx']."'")."' and lokasi = '".$_POST['lokasi'.$i]."'";
                                $this->m_crud->update_data('barang_hrg', $brg_lokasi, $condition);
                                $log = array(
                                    'type' => 'U',
                                    'table' => 'barang_hrg',
                                    'data' => $brg_lokasi,
                                    'condition' => $condition
                                );
                            } else {
                                $this->m_crud->create_data('barang_hrg', $brg_lokasi);
                                $log = array(
                                    'type' => 'I',
                                    'table' => 'barang_hrg',
                                    'data' => $brg_lokasi,
                                    'condition' => ''
                                );
                            }
                        } else {
                            $condition = "barang = '".base64_decode($_GET['trx']."'")."' and lokasi = '".$_POST['lokasi'.$i]."'";
                            $this->m_crud->delete_data('barang_hrg', $condition);
                            //$this->m_crud->delete_data('kartu_stock', "kd_brg = '".base64_decode($_GET['trx']."'")."' and lokasi = '".$_POST['lokasi'.$i]."' and keterangan = 'Input Barang'");
                            $log = array(
                                'type' => 'D',
                                'table' => 'barang_hrg',
                                'data' => '',
                                'condition' => $condition
                            );
                        }

                        $data_log = array(
                            'lokasi' => $_POST['lokasi'.$i],
                            'hostname' => '-',
                            'db_name' => '-',
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }

                    //$this->m_crud->delete_data("harga_bertingkat", "kd_brg = '".$_POST['kd_brg']."'");
                    if ($max_data > 0) {
                        /*for ($i = 0; $i < $max_data; $i++) {
                            $this->m_crud->create_data('harga_bertingkat', array(
                                'kd_brg' => $_POST['kd_brg'],
                                'dari' => $_POST['q1_' . $i],
                                'sampai' => $_POST['q2_' . $i],
                                'harga' => str_replace(',','',$_POST['hrg_' . $i])
                            ));
                        }*/
                    }

                    if(isset($_POST['barang_online'])){
                        /*$this->m_website->curl_store_online('simpan_produk', array(
                            'produk'=>json_encode(array(
                                'kode_barang' => $_POST['kd_brg'],
                                'nama' => $_POST['nm_brg'],
                                'deskripsi' => $_POST['Deskripsi'],
                                'hrg_jual' => $_POST['master_hrg_jual_1']
                            ))
                        ));*/
                    }
                }
                else {
                    if($_FILES['gambar']['name']!=null){
                        $gambar = 'assets/images/barang/'.$file['gambar']['file_name'];
                    } else {
                        $gambar = '';
                    }
                    if($_POST['Deskripsi']!='' || $_POST['Deskripsi']!=null || $_POST['Deskripsi']!='null') $deskripsi=$_POST['Deskripsi'];
                    else $deskripsi = '-';

                    if(isset($_POST['bahan'])){
                        $data_bahan=array();
                        for ($i=0; $i<count($_POST['bahan']); $i++) {
                            array_push($data_bahan,['kode_paket'=>$_POST['kd_brg'],'kode_bahan'=>$_POST['bahan'][$i]]);
                        }
                        $this->db->insert_batch('bahan', $data_bahan);
                    }


                    $master = array(
                        'kd_brg' => $_POST['kd_brg'],
                        'barcode' => $_POST['barcode'],
                        'nm_brg' => $nama_barang,
                        'Deskripsi' => $deskripsi,
                        'kel_brg' => $_POST['kel_brg'],
                        'Group1' => $_POST['Group1'],
                        'Group2' => $_POST['Group2'],
                        'satuan' => strtoupper($_POST['satuan']),
                        'hrg_beli' => $_POST['hrg_beli'],
                        'stock_min' => $_POST['stock_min'],
                        'kategori' => $_POST['kategori'],
                        'Jenis' => $_POST['Jenis'],
                        'tgl_input' => date('Y-m-d H:i:s'),
                        'kd_packing' => $_POST['kd_packing'],
                        'qty_packing' => $_POST['qty_packing'],
                        'barang_online' => isset($_POST['barang_online'])?1:0,
                        'hrg_jual_1' => ($_POST['master_hrg_jual_1']!=null)?$_POST['master_hrg_jual_1']:0,
                        'hrg_jual_2' => ($_POST['master_hrg_jual_1']!=null)?$_POST['master_hrg_jual_1']:0,
                        'service' => ($_POST['master_service']!=null)?$_POST['master_service']:0,
                        'PPN' => ($_POST['master_ppn']!=null)?$_POST['master_ppn']:0,
                        'gambar' => $gambar,
                        'kcp' => $_POST['kcp'],
                        'berat' => $_POST['berat'],
                        'poin' => $_POST['poin'],
                        'online' => isset($_POST['online'])?$_POST['online']:0
                    );

                    $this->m_crud->create_data($table, $master);
                    $this->db->insert_batch('bahan', $data_bahan);


                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'I',
                            'table' => 'barang',
                            'data' => $master,
                            'condition' => ""
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }

                    $no = 0;
                    for($i=1; $i<=$_POST['jumlah_lokasi']; $i++) {
                        if($_POST['cek_lokasi'.$i]==1){
                            $brg_lokasi = array(
                                'barang' => $_POST['kd_brg'],
                                'lokasi' => $_POST['lokasi'.$i],
                                'hrg_jual_1' => ($_POST['hrg_jual_1'.$i]!=null)?$_POST['hrg_jual_1'.$i]:0,
                                'hrg_jual_2' => 0,
                                'hrg_jual_3' => 0,
                                'hrg_jual_4' => 0,
                                'service' => ($_POST['service'.$i]!=null)?$_POST['service'.$i]:0,
                                'ppn' => ($_POST['ppn'.$i]!=null)?$_POST['ppn'.$i]:0
                            );
                            $this->m_crud->create_data('barang_hrg', $brg_lokasi);

                            $log = array(
                                'type' => 'I',
                                'table' => 'barang_hrg',
                                'data' => $brg_lokasi,
                                'condition' => ""
                            );
                            $data_log = array(
                                'lokasi' => $_POST['lokasi'.$i],
                                'hostname' => '-',
                                'db_name' => '-',
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }
                    }

                    if ($max_data > 0) {
                        /*for ($i = 0; $i < $max_data; $i++) {
                            $this->m_crud->create_data('harga_bertingkat', array(
                                'kd_brg' => $_POST['kd_brg'],
                                'dari' => $_POST['q1_' . $i],
                                'sampai' => $_POST['q2_' . $i],
                                'harga' => str_replace(',','',$_POST['hrg_' . $i])
                            ));
                        }*/
                    }

                    if (isset($_POST['barang_online'])) {
                        /*$this->m_website->curl_store_online('simpan_produk', array(
                            'produk'=>json_encode(array(
                                'kode_barang' => $_POST['kd_brg'],
                                'nama' => $_POST['nm_brg'],
                                'deskripsi' => $_POST['Deskripsi'],
                                'hrg_jual' => $_POST['master_hrg_jual_1']
                            ))
                        ));*/
                    }
                }

                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                    $this->cart->destroy();
                    if ($_POST['update']) {
                        if ($hrg_lama['hrg_jual_1'] != $_POST['master_hrg_jual_1']) {
                            echo '<script>alert("Data has been Saved");window.location="' . base_url() . $this->control . '/' . $function . '/'.base64_encode(date('Y-m-d')).'"</script>';
                        } else {
                            echo '<script>alert("Data has been Saved");window.location="' . base_url() . $this->control . '/' . $function . '"</script>';
                        }
                    } else {
                        echo '<script>alert("Data has been Saved");window.location="' . base_url() . $this->control . '/' . $function . '"</script>';
                    }
                }
            }
            $this->load->view('bo/index', $data);
        }
    }

	public function get_harga_bertingkat($kode) {
	    $kode = base64_decode($kode);

	    $read_data = $this->m_crud->read_data("harga_bertingkat", "*", "kd_brg = '".$kode."'", "dari ASC");

	    echo json_encode(array('status' => count($read_data), 'list' => $read_data));
    }
	
	public function barang_harga($action = null, $page=1) {
		$this->access_denied(19);
		$data = $this->data;
		$function = 'barang_harga';
		$table = 'barang_hrg';
		$view = $this->control.'/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = 'Harga Barang';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$this->form_validation->set_rules('barang', 'Barang', 'trim|required', array('required' => '%s don`t empty'));
		$where = null; 
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			$this->session->set_userdata('search', array('lokasi'=>$_POST['lokasi'], 'any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
		} 
		$lokasi = $this->session->search['lokasi']; $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort']; 
		if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; } 
		if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="(lokasi = '".$lokasi."')"; } 
		
		if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data($table, "id_".$table." = '".base64_decode($_GET['trx']."'"));
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['master_data'] = $this->m_crud->join_data($table." as bh", 'id_barang_hrg, barang, barcode, nm_brg, Deskripsi, bh.hrg_jual_1, bh.hrg_jual_2, bh.hrg_jual_3, bh.hrg_jual_4, bh.disc1, bh.ppn, lokasi', 'barang', 'bh.barang = barang.kd_brg', "id_".$table." = '".base64_decode($_GET['trx']."'")."'")[0];
		} else if($action != "add" && (!isset($_POST['save']))) { 
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data_join($table, 'id_'.$table, 'barang', 'barang = kd_brg', $where);
			$config['per_page'] = 50;
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
			$data['master_data'] = $this->m_crud->join_data($table." as bh", 'id_barang_hrg, barang, barcode, nm_brg, Deskripsi, bh.hrg_jual_1, bh.hrg_jual_2, bh.hrg_jual_3, bh.hrg_jual_4, bh.service, bh.ppn, Nama as nm_lokasi',
				array('barang', 'Lokasi'), array('bh.barang = barang.kd_brg', 'bh.lokasi = Lokasi.Kode'), 
				$where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null), null, $config['per_page'], ($page-1)*$config['per_page']
			); 
		} 
		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){
				$barang = explode(' | ', $_POST['barang']);
				$this->db->trans_begin();
				if(isset($_POST['update'])){
					$this->m_crud->update_data($table, array(
						'barang' => $barang[0],
						'hrg_jual_1' => $_POST['hrg_jual_1'],
						'hrg_jual_2' => $_POST['hrg_jual_2'],
						'hrg_jual_3' => $_POST['hrg_jual_3'],
						'hrg_jual_4' => $_POST['hrg_jual_4'],
						'service' => $_POST['service'],
						'ppn' => $_POST['ppn'],
						'lokasi' => $_POST['lokasi']
					), "id_".$table." = '".base64_decode($_GET['trx']."'")."'");
				} else {
				    $cek_data = $this->m_crud->get_data($table, "id_".$table, "barang='".$barang[0]."' and lokasi='".$_POST['lokasi']."'");
				    if ($cek_data != null) {
				        $this->m_crud->delete_data($table, "id_".$table." = '".$cek_data['id_'.$table]."'");
                    }
					$this->m_crud->create_data($table, array(
						'barang' => $barang[0],
						'hrg_jual_1' => $_POST['hrg_jual_1'],
						'hrg_jual_2' => $_POST['hrg_jual_2'],
						'hrg_jual_3' => $_POST['hrg_jual_3'],
						'hrg_jual_4' => $_POST['hrg_jual_4'],
						'service' => $_POST['service'],
						'ppn' => $_POST['ppn'],
						'lokasi' => $_POST['lokasi']
					));
				}

				if ($this->db->trans_status() === false) {
				    $this->db->trans_rollback();
                    echo '<script>alert("Data gagal disimpan");window.location="' . base_url() . $this->control . '/' . $function . '"</script>';
                } else {
				    $this->db->trans_commit();
                    echo '<script>alert("Data berhasil disimpan");window.location="' . base_url() . $this->control . '/' . $function . '"</script>';
                }
			}
			$this->load->view('bo/index', $data);
		}
	}

    public function import_barang($param=null) {
	    $response = array();
        if ($param == 'upload') {
            $lokasi = $_POST['list_lokasi'];
            $config['upload_path']          = realpath(APPPATH."../assets");
            $config['allowed_types']        = 'xlsx|csv|xls';
            $config['max_size']             = 5120;

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload('excel')) {
                $response['status'] = false;
                $response['pesan'] = $this->upload->display_errors();
            } else {
                $uploaded = $this->upload->data();

                $objPHPExcel = PHPExcel_IOFactory::load(APPPATH.'../assets/'.$uploaded['file_name']);
                unlink($config['upload_path'].'/'.$uploaded['file_name']);
                $cell_collection = $objPHPExcel->setActiveSheetIndexByName('harga_lokasi')->getCellCollection();

                foreach ($cell_collection as $cell) {
                    $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                    $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                    $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();

                    //The header will/should be in row 1 only. of course, this can be modified to suit your need.
                    if ($row == 1) {
                        $header[$row][$column] = $data_value;
                    } else {
                        $arr_data[$row][$column] = $data_value;
                    }
                }

                $header = array_values($header);
                $arr_data = array_values($arr_data);

                if ($header[0]['A']=='barcode' && $header[0]['B']=='hrg_jual' && $header[0]['C']=='service' && $header[0]['D']=='ppn') {
                    $list_table = '';
                    $json = array();
                    $no = 1;
                    foreach ($arr_data as $item) {
                        $barcode = $item['A'];
                        $hrg_jual = $item['B'];
                        $service = $item['C'];
                        $ppn = $item['D'];

                        $cek_data = $this->m_crud->get_data("barang", "kd_brg, nm_brg", "barcode like '%".$barcode."'");

                        if ($cek_data != null) {
                            $list_table .= '
                            <tr>
                            <td>'.$no.'</td>
                            <td>'.$barcode.'</td>
                            <td>'.$cek_data['nm_brg'].'</td>
                            <td>'.number_format($hrg_jual).'</td>
                            <td>'.$service.'</td>
                            <td>'.$ppn.'</td>
                            </tr>
                            ';
                            array_push($json, array("lokasi"=>$lokasi, "kd_brg"=>$cek_data['kd_brg'], "hrg_jual"=>$hrg_jual, "service"=>$service, "ppn"=>$ppn));
                            $no++;
                        }
                    }

                    $response['status'] = true;
                    $response['list'] = $list_table;
                    $response['json'] = $json;
                } else {
                    $response['status'] = false;
                    $response['pesan'] = "Format data tidak sesuai";
                }
            }
        } else if ($param == 'insert') {

        } else {
            $response['status'] = false;
            $response['pesan'] = "Parameter tidak terdaftar";
        }

        echo json_encode($response);
	}
	
	public function barang_limit_stock($action = null, $page=1) {
		$this->access_denied(282);
		$data = $this->data;
		$function = 'barang_limit_stock';
		$table = 'barang_hrg';
		$view = $this->control.'/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = 'Limit Stock Barang';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$this->form_validation->set_rules('barang', 'Barang', 'trim|required', array('required' => '%s don`t empty'));
		
		$values = is_array($this->session->userdata($this->site.'lokasi'))?array_map('array_pop', $this->session->userdata($this->site.'lokasi')):array();
		$lokasi_in = "'" . implode("','", $values) . "'";
		$data['lokasi_in'] = $lokasi_in;
		$data['lokasi'] = $this->m_crud->read_data('Lokasi', 'Kode, Nama', "kode in (".$lokasi_in.")", 'Nama asc');
		$data['group1'] = $this->m_crud->read_data('group1', 'kode, nama', null, 'Nama asc');
		
		$where = "lokasi in (".$lokasi_in.")";
		
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			$this->session->set_userdata('search', array('lokasi'=>$_POST['lokasi'], 'group1'=>$_POST['group1'], 'any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
		} 
		$lokasi = $this->session->search['lokasi']; $group1 = $this->session->search['group1']; $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort']; 
		if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; } 
		if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="(lokasi = '".$lokasi."')"; } 
		if(isset($group1)&&$group1!=null){ ($where==null)?null:$where.=" and "; $where.="(group1 = '".$group1."')"; } 
		
		if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data($table, "id_".$table." = '".base64_decode($_GET['trx']."'"));
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['master_data'] = $this->m_crud->join_data($table." as bh", 'id_barang_hrg, barang, barcode, nm_brg, Deskripsi, bh.hrg_jual_1, lokasi, bh.stock_min, bh.stock_max', 'barang', 'bh.barang = barang.kd_brg', "id_".$table." = '".base64_decode($_GET['trx']."'")."'")[0];
		} else if($action != "add" && (!isset($_POST['save']))) { 
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data_join($table, 'id_'.$table, 'barang', 'barang = kd_brg', $where);
			$config['per_page'] = 15;
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
			
			$stock = "isnull((select sum(ks.stock_in - ks.stock_out) from kartu_stock ks where ks.kd_brg=bh.barang and ks.lokasi NOT IN ('MUTASI', 'Retur') AND ks.lokasi=bh.lokasi and ks.tgl <= '".date('Y-m-d H:i:s')."'),0) as stock";
			$data['master_data'] = $this->m_crud->join_data($table." as bh", 'id_barang_hrg, barang, barcode, nm_brg, Deskripsi, bh.hrg_jual_1, Nama as nm_lokasi, isnull(bh.stock_min,0) stock_min, isnull(bh.stock_max,0) stock_max, '.$stock, 
				array('barang', 'Lokasi'), array('bh.barang = barang.kd_brg', 'bh.lokasi = Lokasi.Kode'), 
				$where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null), null, $config['per_page'], ($page-1)*$config['per_page']
			); 
		} 
		
		if (isset($_POST['to_excel'])) {
            $stock = "isnull((select sum(ks.stock_in - ks.stock_out) from kartu_stock ks where ks.kd_brg=bh.barang and ks.lokasi NOT IN ('MUTASI', 'Retur') AND ks.lokasi=bh.lokasi and ks.tgl <= '".date('Y-m-d H:i:s')."'),0) as stock";
			$baca = $this->m_crud->join_data($table." as bh", 'id_barang_hrg, barang, barcode, nm_brg, Deskripsi, bh.hrg_jual_1, Nama as nm_lokasi, isnull(bh.stock_min,0) stock_min, isnull(bh.stock_max,0) stock_max, '.$stock, 
				array('barang', 'Lokasi'), array('bh.barang = barang.kd_brg', 'bh.lokasi = Lokasi.Kode'), 
				$where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null), null
			);
			
			$header = array(
                'merge' 	=> array('A1:I1','A2:I2','A3:I3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:I5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:I5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Lokasi', 'B'=>'Kode Barang', 'C'=>'Barcode', 'D'=>'Nama Barang', 'E'=>$this->menu_group['as_deskripsi'], 'F'=>'Harga Jual', 'G'=>'Stock Min', 
					'H'=>'Stock Max', 'I'=>'Stock'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                	$value['nm_lokasi'], $value['barang'], $value['barcode'], $value['nm_brg'], $value['Deskripsi'], $value['hrg_jual_1'], $value['stock_min'], 
					$value['stock_max'], $value['stock']
				);
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }
		
		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){
				$barang = explode('|', $_POST['barang']);
				if(isset($_POST['update'])){
					$this->m_crud->update_data($table, array(
						'stock_min' => $_POST['stock_min'],
						'stock_max' => $_POST['stock_max'],
						'lokasi' => $_POST['lokasi']
					), "id_".$table." = '".base64_decode($_GET['trx']."'")."'");
				} else {
					$this->m_crud->create_data($table, array(
						'stock_min' => $_POST['stock_min'],
						'stock_max' => $_POST['stock_max'],
						'lokasi' => $_POST['lokasi']
					));
				}
				echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
			}
			$this->load->view('bo/index', $data);
		}
	}
	public function update_barang_limit_stock(){
		$this->m_crud->update_data('barang_hrg', array(
			'stock_min' => $_POST['stock_min'],
			'stock_max' => $_POST['stock_max']
		), "id_barang_hrg = '".base64_decode($_POST['trx']."'")."'");
		echo json_encode(array('status'=>1));
	}
	
    public function harga_bertingkat($action = null, $page=1) {
        $this->access_denied(28);
        $data = $this->data;
        $function = 'harga_bertingkat';
        $table = 'harga_bertingkat';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Harga Bertingkat';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('kd_brg', 'Barang', 'trim|required', array('required' => '%s don`t empty'));
        $where = null;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }

        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "id_".$table." = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->join_data($table." as bh", 'id_barang_hrg, barang, barcode, nm_brg, Deskripsi, bh.hrg_jual_1, bh.hrg_jual_2, bh.hrg_jual_3, bh.hrg_jual_4, bh.disc1, bh.ppn, lokasi', 'barang', 'bh.barang = barang.kd_brg', "id_".$table." = '".base64_decode($_GET['trx']."'")."'")[0];
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data_join($table.' bt', 'bt.kd_brg', 'barang br', 'bt.kd_brg = br.kd_brg', $where, "bt.kd_brg");
            $config['per_page'] = 50;
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
            $data['master_data'] = $this->m_crud->join_data($table." as bt", 'br.kd_brg, br.barcode, br.nm_brg',
                array('barang br'), array('bt.kd_brg = br.kd_brg'),
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null), "br.kd_brg, br.barcode, br.nm_brg", $config['per_page'], ($page-1)*$config['per_page']
            );
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){
            $data['content'] = $view.'form_'.$function;
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                $barang = explode('|', $_POST['kd_brg']);
                if(isset($_POST['update'])){
                    $this->m_crud->update_data($table, array(
                        'barang' => $barang[0],
                        'hrg_jual_1' => $_POST['hrg_jual_1'],
                        'hrg_jual_2' => $_POST['hrg_jual_2'],
                        'hrg_jual_3' => $_POST['hrg_jual_3'],
                        'hrg_jual_4' => $_POST['hrg_jual_4'],
                        'disc1' => $_POST['disc1'],
                        'ppn' => $_POST['ppn'],
                        'lokasi' => $_POST['lokasi']
                    ), "id_".$table." = '".base64_decode($_GET['trx']."'")."'");
                } else {
                    $this->m_crud->create_data($table, array(
                        'kd_brg' => $barang[0],
                        'dari' => $_POST['dari'],
                        'sampai' => $_POST['sampai'],
                        'harga' => str_replace(',', '', $_POST['harga'])
                    ));
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function get_qty_max($data) {
	    $data = base64_decode($data);
	    $get_data = $this->m_crud->get_data("harga_bertingkat", "isnull(MAX(sampai), 0) qty", "kd_brg = '".$data."'");

	    echo (int)$get_data['qty']+1;
    }

    public function detail_harga($data) {
	    $data = base64_decode($data);
	    $list = '';
	    $no = 0;
	    $get_data = $this->m_crud->get_data("barang", "kd_brg, nm_brg, barcode, hrg_jual_1", "kd_brg = '".$data."'");
	    $read_data = $this->m_crud->read_data("harga_bertingkat", "*", "kd_brg = '".$data."'", "dari");
	    foreach ($read_data as $row) {
	        $no++;
	        $list .= '
	        <tr>
	            <td>'.$no.'</td>
	            <td>'.(int)$row['dari'].'</td>
	            <td>'.(int)$row['sampai'].'</td>
	            <td class="text-right">'.number_format($row['harga']).'</td>
	        </tr>
	        ';
        }
	    echo json_encode(array('list' => $list, 'kd_brg'=>$get_data['kd_brg'], 'nm_brg'=>$get_data['nm_brg'], 'barcode'=>$get_data['barcode'], 'hrg'=>number_format($get_data['hrg_jual_1'])));
    }
	
	public function data_bank($action = null, $page=1){	
		$this->access_denied(20);
		$data = $this->data;
		$function = 'data_bank';
		$table = 'Bank';
		$view = $this->control.'/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = 'Data Bank';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		$this->form_validation->set_rules('Nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
		$where = null; 
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			$this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
		} 
		$column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort']; 
		if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; } 
		
		if($action == 'delete' && isset($_GET['trx'])){
			$this->m_crud->delete_data($table, "Nama = '".base64_decode($_GET['trx']."'"));
			echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
		} else if($action == 'edit'){
			$data['master_data'] = $this->m_crud->get_join_data($table.' b', 'b.*, r.norek, r.atas_nama', array(array('table'=>'rekening r', 'type'=>'left')), array("r.bank=b.nama and member is null"), "b.Nama = '".base64_decode($_GET['trx']."'")."'");
		} else if($action != "add" && (!isset($_POST['save']))) { 
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data($table, 'Nama', $where);
			$config['per_page'] = 50;
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
			$data['master_data'] = $this->m_crud->select_limit_join($table.' b', 'b.*, r.norek, r.atas_nama', array(array('table'=>'rekening r', 'type'=>'left')), array("r.bank=b.nama and member is null"), $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:"b.nama asc"), null, ($page-1)*$config['per_page']+1, $config['per_page']*$page);
		} 
		if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else {
			if(isset($_POST['save'])){
                $row = 'foto';
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
                    'Nama' => $_POST['Nama'],
                    'EDC' => $_POST['EDC'],
                    'Charge_Debit' => $_POST['Charge_Debit'],
                    'Charge_Kredit' => $_POST['Charge_Kredit'],
                    'status' => $_POST['status']
                );

                if ($_FILES['foto']['name']!=null) {
                    $master['foto'] = 'assets/images/foto/'.$file['foto']['file_name'];
                }

			    $master_rekening = array(
                    'norek' => $_POST['norek'],
                    'atas_nama' => $_POST['atas_nama'],
                    'bank' => $_POST['Nama'],
                    'status' => $_POST['status']
                );

				if(isset($_POST['update'])){
					$get_rekening = $this->m_crud->get_data("rekening", "id_rekening", "bank='".base64_decode($_GET['trx']."'")."' and member is null");

					if ($get_rekening != null) {
					    $status_rek = 'U';
					    $this->m_crud->update_data('rekening', $master_rekening, "id_rekening='".$get_rekening['id_rekening']."'");
                    } else {
                        $status_rek = 'I';
					    $master_rekening['id_rekening'] = $this->m_website->generate_kode('rekening', date('ymd'), null);
					    $this->m_crud->create_data('rekening', $master_rekening);
                    }

				    $this->m_crud->update_data($table, $master, "Nama = '".base64_decode($_GET['trx']."'")."'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "Nama = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);

                        if ($status_rek == 'U') {
                            $log_rek = array(
                                'type' => 'U',
                                'table' => 'rekening',
                                'data' => $master_rekening,
                                'condition' => "id_rekening='".$get_rekening['id_rekening']."'"
                            );
                        } else {
                            $log_rek = array(
                                'type' => 'I',
                                'table' => 'rekening',
                                'data' => $master_rekening,
                                'condition' => ""
                            );
                        }

                        $data_log_rek = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log_rek)
                        );
                        $this->m_website->insert_log_api($data_log_rek);
                    }
				} else {
					$this->m_crud->create_data($table, $master);

                    $master_rekening['id_rekening'] = $this->m_website->generate_kode('rekening', date('ymd'), null);
                    $this->m_crud->create_data('rekening', $master_rekening);

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'I',
                            'table' => $table,
                            'data' => $master,
                            'condition' => ""
                        );
                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);

                        $log_rek = array(
                            'type' => 'I',
                            'table' => 'rekening',
                            'data' => $master_rekening,
                            'condition' => ""
                        );
                        $data_log_rek = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log_rek)
                        );
                        $this->m_website->insert_log_api($data_log_rek);
                    }
				}
				echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
			}
			$this->load->view('bo/index', $data);
		}
	}

    public function data_kas($action = null, $page=1){
        $this->access_denied(21);
        $data = $this->data;
        $function = 'data_kas';
        $table = 'Bank';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Kas';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('Nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
        $where = null;

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }

        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "Nama = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->get_data(base64_decode($_GET['tabel']), '*', "kode = '".base64_decode($_GET['trx']."'")."'");
        } else if($action != "add" && (!isset($_POST['save']))) {
            $data['master_data'] = $this->m_crud->select_union("*, 'Kas Masuk' tipe_kas, 'Master_Kas_Masuk' tabel","Master_Kas_Masuk",($where==null)?null:$where,"*, 'Kas Keluar' tipe_kas, 'Master_Kas_Keluar' tabel", "Master_Kas_Keluar", ($where==null)?null:$where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null));
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){
            $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                $master = array(
                    'Nama' => $_POST['Nama'],
                    'Jns_Kas' => $_POST['Jns_Kas']
                );

                if(isset($_POST['update'])){
                    $this->m_crud->update_data($_POST['tipe'], $master, "kode = '".base64_decode($_GET['trx']."'")."'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $_POST['tipe'],
                            'data' => $master,
                            'condition' => "kode = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                } else {
                    $kode = $this->m_crud->max_data($_POST['tipe'], "kode");
                    $master['kode'] = sprintf('%03d', ((int)$kode)+1);

                    $this->m_crud->create_data($_POST['tipe'], $master);

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'I',
                            'table' => $_POST['tipe'],
                            'data' => $master,
                            'condition' => ""
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function data_promo($action = null, $page=1){
        $this->access_denied(22);
        $data = $this->data;
        $function = 'data_promo';
        $table = 'master_promo';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Promo';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $where = null;

        //$this->form_validation->set_rules('kd_brg', 'kd_brg', 'trim|required', array('required' => '%s don`t empty'));

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "id_promo = '".base64_decode($_GET['trx']."'"));
            $this->m_crud->delete_data('master_promo', "id_promo = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->get_data($table, '*', "id_promo = '".base64_decode($_GET['trx']."'")."'");
        } else if($action != "add" && !isset($_POST['save'])) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data($table, 'id_promo', $where);
            $config['per_page'] = 50;
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
            $data['master_data'] = $this->m_crud->select_limit($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'kode asc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        }

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->read_data($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'kode asc'));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:H1','A2:H2','A3:H3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:H5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:H5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kategori', 'B'=>'Kode', 'C'=>'Tanggal Mulai', 'D'=>'Tanggal Selesai', 'E'=>'Lokasi', 'F'=>'Jenis Diskon', 'G'=>'Diskon', 'H'=>'Diskon 2'
                )
            );

            foreach($baca as $row => $value){
                $array_lokasi = array();
                $data_lokasi = json_decode($value['lokasi'], true);
                for ($i = 0; $i < count($data_lokasi['lokasi_list']); $i++) {
                    array_push($array_lokasi, $data_lokasi['lokasi_list'][$i]['kode']);
                }
                sort($array_lokasi);
                $body[$row] = array(
                    ($value['cat_promo']=='brg')?'Barang':(($value['cat_promo']=='kel_brg')?'Kelompok Barang':'Supplier'), $value['kode'], substr($value['dariTgl'],0,10), substr($value['sampaiTgl'],0,10), implode(', ', $array_lokasi), ($value['pildiskon']=='money')?'Rp':'%', $value['diskon'], $value['diskon2']
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        /*if($this->form_validation->run() == false) {
            $this->load->view('bo/index', $data);
        } else*/ {
            if(isset($_POST['save'])){
                $notif = 0;
                $lokasi = array();
                $explode_kd_brg = explode('|', $_POST['kd_brg']);
                $lokasi["lokasi_list"] = array();
                for ($i=0; $i<count($_POST['lokasi']); $i++) {
                    $data = array();
                    $data["kode"] = $_POST['lokasi'][$i];
                    array_push($lokasi["lokasi_list"], $data);
                }

                $row = 'gambar';
                $config['upload_path']          = './assets/images/promo';
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

                $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");

                if(isset($_POST['update'])) {
                    $id_promo = base64_decode($_GET['trx']);
                    if($_FILES['gambar']['name']!=null){
                        $data_gambar = array(
                            'gambar' => $this->config->item('site').'assets/images/promo/'.$file['gambar']['file_name']
                        );
                        $this->m_crud->update_data($table, $data_gambar, "id_promo = '".$id_promo."'");

                        foreach ($read_lokasi as $item) {
                            $log = array(
                                'type' => 'U',
                                'table' => $table,
                                'data' => $data_gambar,
                                'condition' => "id_promo = '".$id_promo."'"
                            );

                            $data_log = array(
                            'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }
                    }

                    $this->m_crud->delete_data("Promo", "id_promo = '".$id_promo."'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'D',
                            'table' => 'Promo',
                            'data' => "",
                            'condition' => "id_promo = '".$id_promo."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                    $kategori = $_POST['kategori'];
                    if ($kategori == 'kel_brg') {
                        $read_kel_brg = $this->m_crud->read_data("barang", "kd_brg", "kel_brg = '".$_POST['kel_brg']."'");

                        $data_promo = array(
                            'cat_promo' => $kategori,
                            'kode' => $_POST['kel_brg'],
                            'diskon' => $_POST['diskon'],
                            'diskon2' => $_POST['diskon2'],
                            'pildiskon' => $_POST['pildiskon'],
                            'lokasi' => json_encode($lokasi)
                        );

                        if (isset($_POST['periode'])) {
                            $data_promo['dariTgl'] = NULL;
                            $data_promo['sampaiTgl'] = NULL;
                            $data_promo['periode'] = $_POST['periode'];
                        } else {
                            $data_promo['dariTgl'] = $_POST['dariTgl'];
                            $data_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                            $data_promo['periode'] = '0';
                        }

                        if (isset($_POST['member'])) {
                            $data_promo['member'] = $_POST['member'];
                        } else {
                            $data_promo['member'] = '0';
                        }

                        $this->m_crud->update_data($table, $data_promo, "id_promo = '".$id_promo."'");
                        foreach ($read_lokasi as $item) {
                            $log = array(
                                'type' => 'U',
                                'table' => $table,
                                'data' => $data_promo,
                                'condition' => "id_promo = '".$id_promo."'"
                            );

                            $data_log = array(
                            'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }

                        foreach ($read_kel_brg as $row) {
                            $det_promo = array(
                                'kd_brg' => $row['kd_brg'],
                                'Diskon' => $_POST['diskon'],
                                'Diskon2' => $_POST['diskon2'],
                                'pildiskon' => $_POST['pildiskon'],
                                'lokasi' => json_encode($lokasi),
                                'id_promo' => $id_promo
                            );

                            if (!isset($_POST['periode'])) {
                                $det_promo['dariTgl'] = $_POST['dariTgl'];
                                $det_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                            }

                            $notif++;
                            $this->m_crud->create_data('Promo', $det_promo);
                            foreach ($read_lokasi as $item) {
                                $log = array(
                                    'type' => 'I',
                                    'table' => 'Promo',
                                    'data' => $det_promo,
                                    'condition' => ""
                                );

                                $data_log = array(
                            'lokasi' => $item['Kode'],
                                    'hostname' => $item['server'],
                                    'db_name' => $item['db_name'],
                                    'query' => json_encode($log)
                                );
                                $this->m_website->insert_log_api($data_log);
                            }
                        }
                    } else if ($kategori == 'supplier') {
                        $read_supplier = $this->m_crud->read_data("barang", "kd_brg", "Group1 = '".$_POST['supplier']."'");

                        $data_promo = array(
                            'cat_promo' => $kategori,
                            'kode' => $_POST['supplier'],
                            'diskon' => $_POST['diskon'],
                            'diskon2' => $_POST['diskon2'],
                            'pildiskon' => $_POST['pildiskon'],
                            'lokasi' => json_encode($lokasi)
                        );

                        if (isset($_POST['periode'])) {
                            $data_promo['dariTgl'] = NULL;
                            $data_promo['sampaiTgl'] = NULL;
                            $data_promo['periode'] = $_POST['periode'];
                        } else {
                            $data_promo['dariTgl'] = $_POST['dariTgl'];
                            $data_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                            $data_promo['periode'] = '0';
                        }

                        if (isset($_POST['member'])) {
                            $data_promo['member'] = $_POST['member'];
                        } else {
                            $data_promo['member'] = '0';
                        }

                        $this->m_crud->update_data($table, $data_promo, "id_promo = '".$id_promo."'");
                        foreach ($read_lokasi as $item) {
                            $log = array(
                                'type' => 'U',
                                'table' => $table,
                                'data' => $data_promo,
                                'condition' => "id_promo = '".$id_promo."'"
                            );

                            $data_log = array(
                            'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }

                        foreach ($read_supplier as $row) {
                            $det_promo = array(
                                'kd_brg' => $row['kd_brg'],
                                'Diskon' => $_POST['diskon'],
                                'Diskon2' => $_POST['diskon2'],
                                'pildiskon' => $_POST['pildiskon'],
                                'lokasi' => json_encode($lokasi),
                                'id_promo' => $id_promo
                            );

                            if (!isset($_POST['periode'])) {
                                $det_promo['dariTgl'] = $_POST['dariTgl'];
                                $det_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                            }

                            $notif++;
                            $this->m_crud->create_data('Promo', $det_promo);
                            foreach ($read_lokasi as $item) {
                                $log = array(
                                    'type' => 'I',
                                    'table' => 'Promo',
                                    'data' => $det_promo,
                                    'condition' => ""
                                );

                                $data_log = array(
                            'lokasi' => $item['Kode'],
                                    'hostname' => $item['server'],
                                    'db_name' => $item['db_name'],
                                    'query' => json_encode($log)
                                );
                                $this->m_website->insert_log_api($data_log);
                            }
                        }
                    } else {
                        $data_promo = array(
                            'cat_promo' => $kategori,
                            'kode' => $explode_kd_brg[0],
                            'diskon' => $_POST['diskon'],
                            'diskon2' => $_POST['diskon2'],
                            'pildiskon' => $_POST['pildiskon'],
                            'lokasi' => json_encode($lokasi)
                        );

                        $det_promo = array(
                            'kd_brg' => $explode_kd_brg[0],
                            'Diskon' => $_POST['diskon'],
                            'Diskon2' => $_POST['diskon2'],
                            'pildiskon' => $_POST['pildiskon'],
                            'lokasi' => json_encode($lokasi),
                            'id_promo' => $id_promo
                        );

                        if (isset($_POST['periode'])) {
                            $data_promo['dariTgl'] = NULL;
                            $data_promo['sampaiTgl'] = NULL;
                            $det_promo['dariTgl'] = NULL;
                            $det_promo['sampaiTgl'] = NULL;
                            $data_promo['periode'] = $_POST['periode'];
                        } else {
                            $data_promo['dariTgl'] = $_POST['dariTgl'];
                            $data_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                            $det_promo['dariTgl'] = $_POST['dariTgl'];
                            $det_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                            $data_promo['periode'] = '0';
                        }

                        if (isset($_POST['member'])) {
                            $data_promo['member'] = $_POST['member'];
                        } else {
                            $data_promo['member'] = '0';
                        }

                        $this->m_crud->update_data($table, $data_promo, "id_promo = '".$id_promo."'");
                        foreach ($read_lokasi as $item) {
                            $log = array(
                                'type' => 'U',
                                'table' => $table,
                                'data' => $data_promo,
                                'condition' => "id_promo = '".$id_promo."'"
                            );

                            $data_log = array(
                            'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }

                        $notif++;
                        $this->m_crud->create_data('Promo', $det_promo);
                        foreach ($read_lokasi as $item) {
                            $log = array(
                                'type' => 'I',
                                'table' => 'Promo',
                                'data' => $det_promo,
                                'condition' => ""
                            );

                            $data_log = array(
                            'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }
                    }
                } else {
                    $id_promo = $this->m_website->generate_kode("PR", "-", substr(str_replace('-', '', $_POST['dariTgl']), 2));
                    if($_FILES['gambar']['name']!=null) {
                        $gambar = $this->config->item('site').'assets/images/promo/' . $file['gambar']['file_name'];
                    } else {
                        $gambar = '-';
                    }

                    $kategori = $_POST['kategori'];
                    if ($kategori == 'kel_brg') {
                        $read_kel_brg = $this->m_crud->read_data("barang", "kd_brg", "kel_brg = '".$_POST['kel_brg']."'");

                        $data_promo = array(
                            'id_promo' => $id_promo,
                            'cat_promo' => $kategori,
                            'kode' => $_POST['kel_brg'],
                            'diskon' => $_POST['diskon'],
                            'diskon2' => $_POST['diskon2'],
                            'pildiskon' => $_POST['pildiskon'],
                            'lokasi' => json_encode($lokasi),
                            'gambar' => $gambar
                        );

                        if (isset($_POST['periode'])) {
                            $data_promo['periode'] = $_POST['periode'];
                        } else {
                            $data_promo['dariTgl'] = $_POST['dariTgl'];
                            $data_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                            $data_promo['periode'] = '0';
                        }

                        if (isset($_POST['member'])) {
                            $data_promo['member'] = $_POST['member'];
                        } else {
                            $data_promo['member'] = '0';
                        }

                        $this->m_crud->create_data($table, $data_promo);

                        foreach ($read_lokasi as $item) {
                            $log = array(
                                'type' => 'I',
                                'table' => $table,
                                'data' => $data_promo,
                                'condition' => ""
                            );

                            $data_log = array(
                            'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }

                        foreach ($read_kel_brg as $row) {
                            $det_promo = array(
                                'kd_brg' => $row['kd_brg'],
                                'Diskon' => $_POST['diskon'],
                                'Diskon2' => $_POST['diskon2'],
                                'pildiskon' => $_POST['pildiskon'],
                                'lokasi' => json_encode($lokasi),
                                'gambar' => $gambar,
                                'id_promo' => $id_promo
                            );

                            if (!isset($_POST['periode'])) {
                                $det_promo['dariTgl'] = $_POST['dariTgl'];
                                $det_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                            }

                            $notif++;
                            $this->m_crud->create_data('Promo', $det_promo);
                            foreach ($read_lokasi as $item) {
                                $log = array(
                                    'type' => 'I',
                                    'table' => 'Promo',
                                    'data' => $det_promo,
                                    'condition' => ""
                                );

                                $data_log = array(
                            'lokasi' => $item['Kode'],
                                    'hostname' => $item['server'],
                                    'db_name' => $item['db_name'],
                                    'query' => json_encode($log)
                                );
                                $this->m_website->insert_log_api($data_log);
                            }
                        }
                    } else if ($kategori == 'supplier') {
                        $read_supplier = $this->m_crud->read_data("barang", "kd_brg", "Group1 = '".$_POST['supplier']."'");

                        $data_promo = array(
                            'id_promo' => $id_promo,
                            'cat_promo' => $kategori,
                            'kode' => $_POST['supplier'],
                            'diskon' => $_POST['diskon'],
                            'diskon2' => $_POST['diskon2'],
                            'pildiskon' => $_POST['pildiskon'],
                            'lokasi' => json_encode($lokasi),
                            'gambar' => $gambar
                        );

                        if (isset($_POST['periode'])) {
                            $data_promo['periode'] = $_POST['periode'];
                        } else {
                            $data_promo['dariTgl'] = $_POST['dariTgl'];
                            $data_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                            $data_promo['periode'] = '0';
                        }

                        if (isset($_POST['member'])) {
                            $data_promo['member'] = $_POST['member'];
                        } else {
                            $data_promo['member'] = '0';
                        }

                        $this->m_crud->create_data($table, $data_promo);
                        foreach ($read_lokasi as $item) {
                            $log = array(
                                'type' => 'I',
                                'table' => $table,
                                'data' => $data_promo,
                                'condition' => ""
                            );

                            $data_log = array(
                            'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }

                        foreach ($read_supplier as $row) {
                            $det_promo = array(
                                'kd_brg' => $row['kd_brg'],
                                'Diskon' => $_POST['diskon'],
                                'Diskon2' => $_POST['diskon2'],
                                'pildiskon' => $_POST['pildiskon'],
                                'lokasi' => json_encode($lokasi),
                                'gambar' => $gambar,
                                'id_promo' => $id_promo
                            );

                            if (!isset($_POST['periode'])) {
                                $det_promo['dariTgl'] = $_POST['dariTgl'];
                                $det_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                            }

                            $notif++;
                            $this->m_crud->create_data('Promo', $det_promo);
                            foreach ($read_lokasi as $item) {
                                $log = array(
                                    'type' => 'I',
                                    'table' => 'Promo',
                                    'data' => $det_promo,
                                    'condition' => ""
                                );

                                $data_log = array(
                            'lokasi' => $item['Kode'],
                                    'hostname' => $item['server'],
                                    'db_name' => $item['db_name'],
                                    'query' => json_encode($log)
                                );
                                $this->m_website->insert_log_api($data_log);
                            }
                        }
                    } else {
                        $data_promo = array(
                            'id_promo' => $id_promo,
                            'cat_promo' => $kategori,
                            'kode' => $explode_kd_brg[0],
                            'diskon' => $_POST['diskon'],
                            'diskon2' => $_POST['diskon2'],
                            'pildiskon' => $_POST['pildiskon'],
                            'lokasi' => json_encode($lokasi),
                            'gambar' => $gambar
                        );

                        $det_promo = array(
                            'kd_brg' => $explode_kd_brg[0],
                            'Diskon' => $_POST['diskon'],
                            'Diskon2' => $_POST['diskon2'],
                            'pildiskon' => $_POST['pildiskon'],
                            'lokasi' => json_encode($lokasi),
                            'gambar' => $gambar,
                            'id_promo' => $id_promo
                        );

                        if (isset($_POST['periode'])) {
                            $data_promo['periode'] = $_POST['periode'];
                        } else {
                            $data_promo['dariTgl'] = $_POST['dariTgl'];
                            $data_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                            $data_promo['periode'] = '0';
                            $det_promo['dariTgl'] = $_POST['dariTgl'];
                            $det_promo['sampaiTgl'] = $_POST['sampaiTgl'];
                        }

                        if (isset($_POST['member'])) {
                            $data_promo['member'] = $_POST['member'];
                        } else {
                            $data_promo['member'] = '0';
                        }

                        $this->m_crud->create_data($table, $data_promo);
                        foreach ($read_lokasi as $item) {
                            $log = array(
                                'type' => 'I',
                                'table' => $table,
                                'data' => $data_promo,
                                'condition' => ""
                            );

                            $data_log = array(
                            'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }

                        $notif++;
                        $this->m_crud->create_data('Promo', $det_promo);
                        foreach ($read_lokasi as $item) {
                            $log = array(
                                'type' => 'I',
                                'table' => 'Promo',
                                'data' => $det_promo,
                                'condition' => ""
                            );

                            $data_log = array(
                            'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }
                    }
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'/'.base64_encode($notif).'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function konversi($action = null, $page=1){
        $this->access_denied(23);
        $data 		= $this->data;
        $function 	= 'konversi';
        $table 		= 'konversi';
        $view 		= $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){
            $this->session->unset_userdata('lokasi');
            $this->session->unset_userdata('any');
            $this->cart->destroy();
            $this->session->set_userdata($this->site . 'admin_menu', $function);
        }
        $data['title'] 	= 'Konversi';
        $data['page'] 	= $function;
        $data['content']= $view.$function;
        $data['table'] 	= $table;
        $this->form_validation->set_rules('kd_brg1', 'Column', 'trim|required', array('required' => '%s don`t empty'));
        $where = null;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('lokasi', $_POST['lokasi']);
            $this->session->set_userdata('any', $_POST['any']);
            $lokasi = $this->session->lokasi;
            if(isset($lokasi)&&$lokasi!=null){
                ($where==null)?null:$where.=" and ";
                $where.="lokasi = '".$lokasi."'";
            }
            $search = $this->session->any;
            if(isset($search)&&$search!=null){
                ($where==null)?null:$where.=" and ";
                $where.="(kd_brg1 like '%".$search."%' or kd_brg2 like '%".$search."%')";
            }
        }else if(isset($this->session->lokasi)||isset($this->session->any)){
            $lokasi = $this->session->lokasi; if(isset($lokasi)&&$lokasi!=null){
                ($where==null)?null:$where.=" and "; $where.="lokasi = '".$lokasi."'";
            }
            $search = $this->session->any;
            if(isset($search)&&$search!=null){
                ($where==null)?null:$where.=" and ";
                $where.="(kd_brg1 like '%".$search."%' or kd_brg2 like '%".$search."%')";
            }
        }
        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "kd_brg1 = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        }else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->get_data($table,"*","kd_brg1 = '".base64_decode($_GET['trx']."'")."'");
        }else if($action != "add" && !isset($_POST['save'])) {
            $config['base_url'] 		= base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] 		=$this->m_crud->count_data($table,'kd_brg1',$where);
            $config['per_page'] 		= 50;
            //$config['attributes'] = array('class' => ''); //attributes anchors
            $config['first_url'] 		= $config['base_url'];
            $config['num_links'] 		= 5;
            $config['use_page_numbers'] = TRUE;
            //$config['display_pages'] 	= FALSE;
            $config['full_tag_open'] 	= '<ul class="pagination pagination-sm">';
            $config['first_tag_open'] 	= '<li>'; $config['first_link'] = '&laquo;'; $config['first_tag_close'] = '</li>';
            $config['prev_tag_open'] 	= '<li>'; $config['prev_link'] = '&lt;'; $config['prev_tag_close'] = '</li>';
            $config['cur_tag_open'] 	= '<li class="active"><a href="#" />'; $config['cur_tag_close'] = '</li>';
            $config['num_tag_open'] 	= '<li>'; $config['num_tag_close'] = '</li>';
            $config['next_tag_open'] 	= '<li>'; $config['next_link'] = '&gt;';  $config['next_tag_close'] = '</li>';
            $config['last_tag_open'] 	= '<li>'; $config['last_link'] = '&raquo;'; $config['last_tag_close'] = '</li>';
            $config['full_tag_close'] 	= '</ul>';
            $this->pagination->initialize($config);
            $data['master_data'] = $this->m_crud->select_limit($table, "*,(select barang.nm_brg from barang where ltrim(rtrim(kd_brg)) = ltrim(rtrim(konversi.kd_brg1))) nama_barang1, (select barang.nm_brg from barang where ltrim(rtrim(kd_brg)) = ltrim(rtrim(konversi.kd_brg2))) nama_barang2 ", $where,"kd_brg1 DESC", null , ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        }
        if($action == "add" || $action == "edit" || isset($_POST['save'])){
            $data['content'] = $view.'form_'.$function;
        }
        if($this->form_validation->run() == false){
            $this->load->view('bo/index', $data);
        }else{
            if(isset($_POST['save'])){
                $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");

                $barang = explode('|', $_POST['kd_brg2']);
                $barang1 = explode('|', $_POST['kd_brg1']);

                $master = array(
                    'kd_brg2' 		=> $barang[0],
                    'kd_brg1' 		=> $barang1[0],
                    'nilai_konversi'=> $_POST['nilai_konversi']
                );

                if(isset($_POST['update'])){
                    $this->m_crud->update_data($table, $master, "kd_brg1 = '".base64_decode($_GET['trx']."'")."'");

                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "kd_brg1 = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                }else{
                    $this->m_crud->create_data($table, $master);

                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'I',
                            'table' => $table,
                            'data' => $master,
                            'condition' => ""
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function tipe_customer($action = null, $page=1){
        $this->access_denied(24);
        $data = $this->data;
        $function = 'tipe_customer';
        $table = 'Customer_Type';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Tipe Customer';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('NAMA', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
        $where = null;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "KODE = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->read_data($table, '*',
                //array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'),
                //array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'),
                "KODE = '".base64_decode($_GET['trx']."'")."'"
            )[0];
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data($table, 'KODE', $where);
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
            $data['master_data'] = $this->m_crud->read_data($table, '*',
                //array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'),
                //array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'),
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null), null, $config['per_page'], ($page-1)*$config['per_page']
            );
        }
        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");

                $master = array(
                    'NAMA' => $_POST['NAMA'],
                );

                if(isset($_POST['update'])){
                    $this->m_crud->update_data($table, $master, "KODE = '".base64_decode($_GET['trx']."'")."'");

                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "KODE = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                } else {
                    $max_kode = $this->m_crud->get_data("Customer_Type", "MAX(CONVERT(INTEGER, RIGHT(KODE, 5))) KODE", "KODE <> 'UMUM'");
                    $kode = "CT".sprintf('%05d', $max_kode['KODE']+1);

                    $master['KODE'] = $kode;

                    $this->m_crud->create_data($table, $master);

                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'I',
                            'table' => $table,
                            'data' => $master,
                            'condition' => ""
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function data_customer($action = null, $page=1){
        $this->access_denied(25);
        $data = $this->data;
        $function = 'data_customer';
        $table = 'Customer';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Customer';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('Nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
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
                "kd_cust = '".base64_decode($_GET['trx']."'")."'"
            )[0];
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data_join($table, 'kd_cust', array('Customer_Type'), array('Cust_Type = KODE'), $where);
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
            $data['master_data'] = $this->m_crud->select_limit_join($table, 'Customer.*, ct.NAMA nama_tipe',
                array('Customer_Type ct'),
                array('Cust_Type = ct.KODE'),
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:"kd_cust asc"), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page)
            );
        }

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->join_data($table, 'Customer.*, ct.NAMA nama_tipe',
                array('Customer_Type ct'),
                array('Cust_Type = ct.KODE'),
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:"kd_cust asc"));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:O1','A2:O2','A3:O3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:O5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:O5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama', 'C'=>'Tipe', 'D'=>'Alamat', 'E'=>'Disc', 'F'=>'Tgl Ultah', 'G'=>'Disc Ultah', 'H'=>'Telp 1', 'I'=>'Telp 2', 'J'=>'Telp 3', 'K'=>'Deposit', 'L'=>'Tgl Deposit', 'M'=>'Special Price', 'N'=>'Tgl Akhir', 'O'=>'Status'
                )
            );

            foreach($baca as $row => $value){
                $exp_alamat = explode("|", $value['alamat']);
                $body[$row] = array(
                    $value['kd_cust'], $value['Nama'], $value['nama_tipe'], $exp_alamat[0], $value['diskon'], $value['tgl_ultah'], $value['diskon_ultah'], $value['tlp1'], $value['tlp2'], $value['tlp3'], $value['deposit'], substr($value['tgldeposit'],0,10), ($value['SPECIAL_PRICE']==1?'Aktif':'Tidak Aktif'), substr($value['TGLAKHIR'],0,10), ($value['status']==1?'Aktif':'Tidak Aktif')
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");

                $master = array(
                    'Nama' => $_POST['Nama'],
                    'diskon' => $_POST['diskon'],
                    'alamat' => $_POST['alamat']."|".$_POST['nama_desa']."|".$_POST['nama_kecamatan']."|".$_POST['nama_kota']."|".$_POST['nama_provinsi'],
                    'status' => $_POST['status'],
                    'tgl_ultah' => $_POST['tgl_ultah'],
                    'diskon_ultah' => $_POST['diskon_ultah'],
                    'tlp1' => $_POST['tlp1'],
                    'tlp2' => $_POST['tlp2'],
                    'tlp3' => $_POST['tlp3'],
                    'deposit' => $_POST['deposit'],
                    'tgldeposit' => $_POST['tgldeposit'],
                    'SPECIAL_PRICE' => $_POST['SPECIAL_PRICE'],
                    'TGLAKHIR' => $_POST['TGLAKHIR'],
                    'Cust_Type' => $_POST['Cust_Type']
                );

                if(isset($_POST['update'])){
                    //[Kode] [Nama] [serial] [Ket] [Footer1] [Footer2] [Footer3] [Footer4] [kota] [email] [web] [nama_toko] [phone]
                    $this->m_crud->update_data($table, $master, "kd_cust = '".base64_decode($_GET['trx']."'")."'");

                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "kd_cust = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                } else {
                    $max_kode = $this->m_crud->get_data("Customer", "MAX(CONVERT(INTEGER, RIGHT(kd_cust, 6))) kd_cust");
                    $kode = "1".sprintf('%06d', $max_kode['kd_cust']+1);

                    $master['kd_cust'] = $kode;

                    $this->m_crud->create_data($table, $master);

                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'I',
                            'table' => $table,
                            'data' => $master,
                            'condition' => ""
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function data_sales($action = null, $page=1){
        $this->access_denied(26);
        $data = $this->data;
        $function = 'data_sales';
        $table = 'Sales';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Sales/SPG';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('Nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
        $where = 'lokasi.'.$this->where_lokasi.' or lokasi is null';
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
                //array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'),
                //array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'),
                "KODE = '".base64_decode($_GET['trx']."'")."'"
            )[0];
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data_join_over($table, 'sales.Kode', array(array('table'=>'lokasi', 'type'=>'LEFT')), array('sales.lokasi=lokasi.kode'), $where);
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
            $data['master_data'] = $this->m_crud->select_limit_join($table, "sales.*, isnull(lokasi.nama, 'Belum ada lokasi') nama_lokasi",
                array(array('table'=>'lokasi', 'type'=>'LEFT')),
                array('sales.lokasi=lokasi.kode'),
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:"sales.kode asc"), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page)
            );
        }
        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                $master = array(
                    'Nama' => $_POST['Nama'],
                    'lokasi' => $_POST['lokasi'],
                    'username' => $_POST['username'],
                    'status' => $_POST['status']
                );

                if(isset($_POST['update'])){
                    $this->m_crud->update_data($table, $master, "Kode = '".base64_decode($_GET['trx']."'")."'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "Kode = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                } else {
                    $max_kode = $this->m_crud->get_data($table, "MAX(CONVERT(INTEGER, RIGHT(Kode, 5))) Kode", "Kode <> 'UMUM'");
                    $kode = "SL".sprintf('%05d', $max_kode['Kode']+1);

                    $master['Kode'] = $kode;

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
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function cek_username() {
        $username = $_POST['username'];

        $where = "username='".$username."'";

        $_POST['param']==''?null:$where.=" AND kode<>'".base64_decode($_POST['param'])."'";

        $cek_username = $this->m_crud->get_data("sales", "kode", $where);

        if ($cek_username == null) {
            echo 'true';
        } else {
            echo 'false';
        }
    }
	
	public function data_supplier($action = null, $page=1){
        $this->access_denied(27);
        $data = $this->data;
        $function = 'data_supplier';
        $table = 'Supplier';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Supplier';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('Nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
        $where = null;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "kode = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->read_data($table, '*',
                "kode = '".base64_decode($_GET['trx']."'")."'"
            )[0];
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data($table, 'kode', $where);
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
            $data['master_data'] = $this->m_crud->select_limit($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'kode ASC'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page)
            );
        }

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->read_data($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'kode ASC'));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:I1','A2:I2','A3:I3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:I5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:I5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama', 'C'=>'Alamat', 'D'=>'Kota', 'E'=>'Telp', 'F'=>'Kontak', 'G'=>'No. Kontak', 'H'=>'Email', 'I'=>'Cara Bayar', 'J'=>'Status'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['kode'], $value['Nama'], $value['Alamat'], $value['kota'], $value['telp'], $value['kontak'], $value['no_kontak'], $value['email'], $value['carabyr'], ($value['status']==1?'Aktif':'Tidak Aktif')
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

		$data['data_bank'] = $this->m_crud->read_data('bank', 'Nama');
		$data['json_data_bank'] = json_encode($data['data_bank']);
        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){
            $this->load->view('bo/index', $data);
        } else {
            if(isset($_POST['save'])) {
				$this->db->trans_begin();

				$master = array(
                    'Nama' => $_POST['Nama'],
                    'Alamat' => $_POST['Alamat'],
                    'kota' => $_POST['kota'],
                    'telp' => $_POST['telp'],
                    'kontak' => $_POST['kontak'],
                    'no_kontak' => $_POST['no_kontak'],
                    'email' => $_POST['email'],
                    'status' => $_POST['status'],
                    'carabyr' => $_POST['carabyr']
                );

                $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");

                if(isset($_POST['update'])){
                    //[Kode] [Nama] [serial] [Ket] [Footer1] [Footer2] [Footer3] [Footer4] [kota] [email] [web] [nama_toko] [phone]
                    $this->m_crud->update_data($table, $master, "kode = '".base64_decode($_GET['trx']."'")."'");

                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "kode = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                } else {
                    $master['kode'] = $_POST['kode'];

                    $this->m_crud->create_data($table, $master);

                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'I',
                            'table' => $table,
                            'data' => $master,
                            'condition' => ""
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                }
                $this->m_crud->delete_data('supplier_rek', "supplier = '".$_POST['kode']."'");

                foreach ($read_lokasi as $item) {
                    $log = array(
                        'type' => 'D',
                        'table' => 'supplier_rek',
                        'data' => "",
                        'condition' => "supplier = '".$_POST['kode']."'"
                    );

                    $data_log = array(
                            'lokasi' => $item['Kode'],
                        'hostname' => $item['server'],
                        'db_name' => $item['db_name'],
                        'query' => json_encode($log)
                    );
                    $this->m_website->insert_log_api($data_log);
                }

				for($i=0; $i<$_POST['max_data']; $i++){
                    $sup_rek = array(
                        'rekening'=> $_POST['rek_'.$i],
                        'supplier'=> $_POST['kode'],
                        'an'=> $_POST['an_'.$i],
                        'bi_code'=> $_POST['bi_'.$i],
                        'branch_name'=> $_POST['branch_'.$i],
                        'bank'=> $_POST['bank_'.$i]
                    );

					$this->m_crud->create_data('supplier_rek', $sup_rek);

                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'I',
                            'table' => 'supplier_rek',
                            'data' => $sup_rek,
                            'condition' => ""
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
				}

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					echo false;
				}else {
					$this->db->trans_commit();
					echo true;
					echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
				}
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function data_compliment($action = null, $page=1){
        $this->access_denied(281);
        $data = $this->data;
        $function = 'data_compliment';
        $table = 'compliment';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Compliment';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('nama', 'nama', 'trim|required', array('required' => '%s don`t empty'));
        $where = null;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "kode = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->read_data($table, '*',
                "compliment_id = '".base64_decode($_GET['trx']."'")."'"
            )[0];
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data($table, 'compliment_id', $where);
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
            $data['master_data'] = $this->m_crud->select_limit($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'compliment_id ASC'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        }

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->read_data($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'compliment_id ASC'));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:G1','A2:G2','A3:G3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:G5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:G5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Company', 'C'=>'Nama', 'D'=>'Alamat', 'E'=>'Telp', 'F'=>'Ket', 'G'=>'Balance'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['compliment_id'], $value['company'], $value['nama'], $value['alamat'], $value['tlp'], $value['ket'], $value['balance']
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])) {
            $data['content'] = $view . 'form_' . $function;
        }
        if($this->form_validation->run() == false) {
            $this->load->view('bo/index', $data);
        } else {
            if(isset($_POST['save'])){
                $this->db->trans_begin();

                $master = array(
                    'nama' => $_POST['nama'],
                    'alamat' => $_POST['alamat'],
                    'company' => $_POST['company'],
                    'tlp' => $_POST['tlp'],
                    'status' => $_POST['status']
                );

                if(isset($_POST['update'])){
                    $this->m_crud->update_data($table, $master, "compliment_id = '".base64_decode($_GET['trx']."'")."'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "compliment_id = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                } else {
                    $master['compliment_id'] = $this->m_website->generate_kode('CL', null, null);
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
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                }
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo false;
                }else {
                    $this->db->trans_commit();
                    echo true;
                    echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
                }
            }
            $this->load->view('bo/index', $data);
        }
    }
	
	public function get_supplier_rek($kode) {
	    $supplier = base64_decode($kode);

	    $read_data = $this->m_crud->read_data("supplier_rek", "*", "supplier = '".$supplier."'");

	    echo json_encode(array('status' => count($read_data), 'list' => $read_data));
    }

    public function data_kitchen_printer($action = null, $page=1){
        $this->access_denied(30);
        $data = $this->data;
        $function = 'data_kitchen_printer';
        $table = 'kitchen_printer';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Kitchen Printer';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('nama', 'nama', 'trim|required', array('required' => '%s don`t empty'));
        $where = null;

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "Nama = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->get_data($table, '*', "id_printer = '".base64_decode($_GET['trx']."'")."'");
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data($table, 'id_printer', $where);
            $config['per_page'] = 50;
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
            $data['master_data'] = $this->m_crud->read_data($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null), null, $config['per_page'], ($page-1)*$config['per_page']);
        }
        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false) {
            $this->load->view('bo/index', $data);
        } else {
            if(isset($_POST['save'])){
                if(isset($_POST['update'])){
                    $this->m_crud->update_data($table, array(
                        'nama' => $_POST['nama'],
                        'konektor' => $_POST['konektor'],
                        'pid' => $_POST['pid'],
                        'vid' => $_POST['vid'],
                        'ip' => $_POST['ip']
                    ), "id_printer = '".base64_decode($_GET['trx']."'")."'");
                } else {
                    $kode = $this->m_website->generate_kode("KP", null, null);

                    $master = array(
                        'id_printer' => $kode,
                        'nama' => $_POST['nama'],
                        'konektor' => $_POST['konektor'],
                        'pid' => $_POST['pid'],
                        'vid' => $_POST['vid'],
                        'ip' => $_POST['ip']
                    );

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
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

	public function data_assembly($action = null, $page=1){
                    $this->access_denied(29);
                    $data = $this->data;
                    $function = 'data_assembly';
                    $table = 'detail_assembly';
                    $view = $this->control.'/';
                    if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
                    $data['title'] = 'Data Assembly';
                    $data['page'] = $function;
                    $data['content'] = $view.$function;
                    $data['table'] = $table;

                    $this->form_validation->set_rules('kd_brg_ass', 'Kode Barang', 'trim|required', array('required' => '%s don`t empty'));

                    $where = null;

                    if(isset($_POST['search'])||isset($_POST['to_excel'])){
                        $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
                    }
                    $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
                    if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

                    if($action == 'edit'){
                        $data['master_data'] = $this->m_crud->get_join_data($table.' asm', 'asm.kd_brg_ass, br.hrg_jual_1, br.hrg_beli_patokan hpp_tambahan', "barang br", "br.kd_brg=asm.kd_brg_ass", "asm.kd_brg_ass = '".base64_decode($_GET['trx']."'")."'", null, "asm.kd_brg_ass, br.hrg_beli_patokan, br.hrg_jual_1");
                        $data['detail_data'] = $this->m_crud->join_data($table.' asm', 'br.barcode, br.nm_brg, br.hrg_beli, asm.kd_brg_ass, asm.kd_brg, asm.qty', "barang br", "asm.kd_brg=br.kd_brg", "asm.kd_brg_ass = '".base64_decode($_GET['trx']."'")."'");
                    } else if($action != "add" && (!isset($_POST['save']))) {
                        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
                        $config['total_rows'] = $this->m_crud->count_data_join($table.' asm', 'br.kd_brg', "barang br", "asm.kd_brg_ass=br.kd_brg", $where, null, 'br.kd_brg');
                        /*$config['total_rows'] = $this->m_crud->count_join_data($table, 'kd_brg', array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'), array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'), $where);*/
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
                        $data['master_data'] = $this->m_crud->select_limit_join($table.' asm', 'br.kd_brg, br.barcode, br.nm_brg', "barang br", "asm.kd_brg_ass=br.kd_brg",
                            $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'br.kd_brg asc'), "br.kd_brg, br.barcode, br.nm_brg", ($page-1)*$config['per_page']+1, ($config['per_page']*$page)
                        );
                    }
                    if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
                    if($this->form_validation->run() == false){
                        $this->load->view('bo/index', $data);
                    } else {
                        if(isset($_POST['save'])){
                            $this->db->trans_begin();
                            $hpp_tambahan = (float)($_POST['hpp_tambahan']==''?0:str_replace(',', '', $_POST['hpp_tambahan']));
                            $hrg_jual = (float)($_POST['hrg_jual_1']==''?0:str_replace(',', '', $_POST['hrg_jual_1']));
                            if(isset($_POST['update'])) {
                                $id = base64_decode($_GET['trx']);
                                $this->m_crud->delete_data("detail_assembly", "kd_brg_ass='".$id."'");
                                $total = 0;
                                if ($_POST['max_barang'] > 0) {
                                    for ($i = 0; $i < $_POST['max_barang']; $i++) {
                                        $total = $total + ($_POST['qty_' . $i]*$_POST['hrg_beli_' . $i]);
                                        $this->m_crud->create_data('detail_assembly', array(
                                            'kd_brg_ass' => $id,
                                            'kd_brg' => $_POST['kd_brg_' . $i],
                                            'qty' => $_POST['qty_' . $i],
                                        ));
                                    }
                                }
                                $this->m_crud->update_data("barang", array('hrg_beli'=>($total+$hpp_tambahan), 'hrg_jual_1'=>$hrg_jual, 'hrg_beli_patokan'=>$hpp_tambahan), "kd_brg='".$id."'");
                                $brg_lokasi = array(
                                            'hrg_jual_1' => $hrg_jual,                      
                                        );   
                                        $this->m_crud->update_data('barang_hrg', $brg_lokasi, array(
                                            'barang'=>$id
                                        ));
                                            $log = array(
                                                'type' => 'U',
                                                'table' => 'barang_hrg',
                                                'data' => $brg_lokasi,
                                                'condition' => array('barang'=>$id)
                                            );                   
                                            $data_log = array(
                                                'lokasi' => $_POST['lokasi'.$i],
                                                'hostname' => '-',
                                                'db_name' => '-',
                                                'query' => json_encode($log)
                                            );

                                        $this->m_website->insert_log_api($data_log);
                            } else {
                                if ($_POST['max_barang'] > 0) {
                                    $total = 0;
                                    for ($i = 0; $i < $_POST['max_barang']; $i++) {
                                        $total = $total + ($_POST['qty_' . $i]*$_POST['hrg_beli_' . $i]);
                                        $this->m_crud->create_data('detail_assembly', array(
                                            'kd_brg_ass' => $_POST['kd_brg_ass'],
                                            'kd_brg' => $_POST['kd_brg_' . $i],
                                            'qty' => $_POST['qty_' . $i],
                                        ));
                                    }

                                    $this->m_crud->update_data("barang", 
                                    array(
                                        'hrg_beli'=>($total+$hpp_tambahan), 
                                        'hrg_jual_1'=>$hrg_jual, 
                                        'hrg_beli_patokan'=>$hpp_tambahan), 
                                        "kd_brg='".$_POST['kd_brg_ass']."'"
                                    );

                                        $brg_lokasi = array(
                                            'hrg_jual_1' => $hrg_jual,                      
                                        );   
                                        $this->m_crud->update_data('barang_hrg', $brg_lokasi, array(
                                            'barang'=>$_POST['kd_brg_ass']
                                        ));
                                            $log = array(
                                                'type' => 'U',
                                                'table' => 'barang_hrg',
                                                'data' => $brg_lokasi,
                                                'condition' => array('barang'=>$_POST['kd_brg_ass'])
                                            );                   
                                            $data_log = array(
                                                'lokasi' => $_POST['lokasi'.$i],
                                                'hostname' => '-',
                                                'db_name' => '-',
                                                'query' => json_encode($log)
                                            );

                                        $this->m_website->insert_log_api($data_log);


                                }
                            }
                            if ($this->db->trans_status() === FALSE){
                                $this->db->trans_rollback();
                            } else {
                                $this->db->trans_commit(); $this->cart->destroy();
                                echo '<script>alert("Data has been Saved");window.location="' . base_url() . $this->control . '/' . $function . '"</script>';
                            }
                        }
                        $this->load->view('bo/index', $data);
                    }
	}

	public function barang_assembly($kd_brg) {
                    $kd_brg = base64_decode($kd_brg);

                    $get_barang = $this->m_crud->get_data("barang", "kd_brg, barcode, nm_brg, hrg_beli", "kategori = 'Non Paket' AND jenis = 'Barang Tidak Dijual' AND (kd_brg = '".$kd_brg."' OR barcode = '".$kd_brg."')");

                    if (count($get_barang) > 0) {
                        $status = '1';
                    } else {
                        $status = '0';
                    }

                    echo json_encode(array('status' => $status, 'list' => $get_barang));
	}

    public function detail_assembly($kd_brg) {
        $kd_brg = base64_decode($kd_brg);
        $list = '';

        $master_data = $this->m_crud->get_data("barang", 'kd_brg, barcode, nm_brg', "kd_brg = '".$kd_brg."'", null, "kd_brg, barcode, nm_brg");
        $detail_data = $this->m_crud->join_data('detail_assembly asm', 'br.barcode, br.nm_brg, asm.kd_brg, asm.qty', "barang br", "asm.kd_brg=br.kd_brg", "asm.kd_brg_ass = '".$kd_brg."'");
        $no = 1;
        if (count($detail_data) > 0) {
            foreach ($detail_data as $row) {
                $list .= '
                <tr>
                    <td>'.$no.'</td>
                    <td>'.$row['kd_brg'].'</td>
                    <td>'.$row['barcode'].'</td>
                    <td>'.$row['nm_brg'].'</td>
                    <td>'.(int)$row['qty'].'</td>
                </tr>
                ';

                $no++;
            }
        } else {
            $list = '
            <tr>
                <td colspan="5">Detail Tidak Tersedia</td>
            </tr>
            ';
        }

        echo json_encode(array('list' => $list, 'master' => $master_data));
    }

    public function data_berita($action = null, $page=1){
        $this->access_denied(282);
        $data = $this->data;
        $function = 'data_berita';
        $table = 'berita';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Berita';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $this->form_validation->set_rules('judul', 'Judul', 'trim|required', array('required' => '%s don`t empty'));

        $where = null;

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'edit'){
            $data['master_data'] = $this->m_crud->get_data($table, "*", "id_berita = '".base64_decode($_GET['trx']."'")."'");
        } else if ($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data($table, 'id_berita', $where);
            /*$config['total_rows'] = $this->m_crud->count_join_data($table, 'kd_brg', array('Group1 as g1', 'Group2 as g2', 'kel_brg as kb'), array('barang.Group1 = g1.Kode', 'barang.Group2 = g2.Kode', 'barang.kel_brg = kb.kel_brg'), $where);*/
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
            $data['master_data'] = $this->m_crud->select_limit($table, '*',
                $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'id_berita asc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page)
            );
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }

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

                $this->db->trans_begin();

                $master = array(
                    'judul' => $_POST['judul'],
                    'tanggal' => date('Y-m-d H:i:s'),
                    'deskripsi' => $_POST['deskripsi'],
                    'sumber' => $_POST['sumber'],
                    'data_donasi' => 0,
                    'slide' => 0,
                    'video' => 0
                );

                if ($_FILES['foto']['name']!=null) {
                    $master['foto'] = 'assets/images/foto/'.$file['foto']['file_name'];
                }

                if (isset($_POST['update'])) {
                    $this->m_crud->update_data($table, $master, "id_berita = '" . base64_decode($_GET['trx']) . "'");
                    $id_berita = base64_decode($_GET['trx']);
                } else {
                    $this->m_crud->create_data($table, $master);
                    $id_berita = $this->db->insert_id();
                }

                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit(); $this->cart->destroy();
                    $data_notif = array(
                        'segment'=>'All',
                        'data' => array(
                            "param" => "poin",
                            "kd_trx" => $id_berita,
                            "data" => array(
                                "id_berita" => $id_berita,
                                "judul" => $master['judul'],
                                "tanggal" => $master['tanggal'],
                                "deskripsi" => $master['deskripsi'],
                                "foto" => base_url() . 'assets/images/foto/' . $file['foto']['file_name'],
                                "foto_thumb" => $this->m_website->file_thumb(base_url() . 'assets/images/foto/' . $file['foto']['file_name'])
                            )
                        ),
                        'head'=>'Berita Baru '.$this->m_website->site_data()->title,
                        'content'=>$master['judul']
                    );

                    if ($_FILES['foto']['name']!=null) {
                        $data_notif['big_picture'] = base_url() . 'assets/images/foto/' . $file['foto']['file_name'];
                    }

                    if (!isset($_POST['update'])) {
                        $this->m_website->create_notif($data_notif);
                    }

                    echo '<script>alert("Data has been Saved");window.location="' . base_url() . $this->control . '/' . $function . '"</script>';
                }
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function data_kurir($action = null, $page=1){
        $this->access_denied(283);
        $data = $this->data;
        $function = 'data_kurir';
        $table = 'kurir';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = "Data Kurir";
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('kurir', 'Kurir', 'trim|required', array('required' => '%s don`t empty'));
        $where = null;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(kurir like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "id_kurir = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->get_data($table, '*', "id_kurir = '".base64_decode($_GET['trx']."'")."'");
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data($table, 'id_kurir', $where);
            $config['per_page'] = 50;
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
            $data['master_data'] = $this->m_crud->select_limit($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'kurir asc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        }

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->read_data($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:null));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:B1','A2:B2','A3:B3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:B5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:B5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['kurir'], $value['gambar']
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                if(isset($_POST['update'])){
                    $master = array(
                        'Kode' => $_POST['Kode'],
                        'Nama' => $_POST['Nama']
                    );
                    $this->m_crud->update_data($table, $master, "Kode = '".base64_decode($_GET['trx']."'")."'");

                    $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                    foreach ($read_lokasi as $item) {
                        $log = array(
                            'type' => 'U',
                            'table' => $table,
                            'data' => $master,
                            'condition' => "Kode = '".base64_decode($_GET['trx']."'")."'"
                        );

                        $data_log = array(
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                } else {
                    $master = array(
                        'Kode' => $_POST['Kode'],
                        'Nama' => $_POST['Nama']
                    );
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
                            'lokasi' => $item['Kode'],
                            'hostname' => $item['server'],
                            'db_name' => $item['db_name'],
                            'query' => json_encode($log)
                        );
                        $this->m_website->insert_log_api($data_log);
                    }
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function kel_brg_online($action = null, $page=1){
        $this->access_denied(285);
        $data = $this->data;
        $function = 'kel_brg_online';
        $table = 'kel_brg_online';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Kelompok Barang Online';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
        $where = null;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "id_kel_brg = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->get_data($table, '*', "id_kel_brg = '".base64_decode($_GET['trx']."'")."'");
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data($table, 'id_kel_brg', $where);
            $config['per_page'] = 50;
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
            $data['master_data'] = $this->m_crud->select_limit($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'id_kel_brg asc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        }

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->read_data($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'id_kel_brg asc'));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:C1','A2:C2','A3:C3'),
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
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama', 'C'=>'Status'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['id_kel_brg'], $value['nama'], ($value['status']==1?'Aktif':'Tidak Aktif')
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                $row = 'gambar';
                $config['upload_path']          = './assets/images/barang';
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
                        $manipulasi['width']         = 600;
                        //$manipulasi['height']       = 250;
                        $manipulasi['new_image']       = $file[$row]['full_path'];
                        $manipulasi['create_thumb']       = true;
                        //$manipulasi['thumb_marker']       = '_thumb';
                        $this->load->library('image_lib', $manipulasi);
                        $this->image_lib->resize();
                    }
                }

                $master = array(
                    'nama' => ucwords($_POST['nama']),
                    'status' => $_POST['status']
                );

                if ($_FILES['gambar']['name']!=null) {
                    $master['gambar'] = 'assets/images/barang/'.$file['gambar']['file_name'];
                }

                if ($valid) {
                    if (isset($_POST['update'])) {
                        $this->m_crud->update_data($table, $master, "id_kel_brg = '" . base64_decode($_GET['trx'] . "'") . "'");
                    } else {
                        $master['id_kel_brg'] = $this->m_website->generate_kode('kel_brg_online', date('ymd'), null);
                        $this->m_crud->create_data($table, $master);
                    }
                    echo '<script>alert("Data has been Saved"); window.location="'.base_url().$this->control.'/'.$function.'"</script>';
                } else {
                    echo '<script>alert("Data gagal disimpan"); window.location="'.base_url().$this->control.'/'.$function.'"</script>';
                }
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function barang_online($action = null, $page=1){
        $this->access_denied(286);
        $data = $this->data;
        $function = 'barang_online';
        $table = 'barang_online';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Barang Online';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
        $where = null;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "id_barang = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->get_data($table, '*', "id_barang = '".base64_decode($_GET['trx']."'")."'");
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data_join($table.' br', 'br.id_barang', "kel_brg_online kb", "kb.id_kel_brg=br.kel_brg_online", $where);
            $config['per_page'] = 50;
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
            $data['master_data'] = $this->m_crud->select_limit_join($table.' br', 'br.*, kb.nama nama_kelompok', "kel_brg_online kb", "kb.id_kel_brg=br.kel_brg_online", $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'id_barang asc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        }

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->join_data($table.' br', 'br.*, kb.nama nama_kelompok', "kel_brg_online kb", "kb.id_kel_brg=br.kel_brg_online", $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'id_barang asc'));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:I1','A2:I2','A3:I3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:I5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:I5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama', 'C'=>'Kelompok', 'D'=>'Deskripsi', 'E'=>'Berat (gr)', 'F'=>'Satuan', 'G'=>'Harga Beli', 'H'=>'Harga Jual', 'I'=>'Status'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['id_barang'], $value['nama'], $value['nama_kelompok'], $value['deskripsi'], $value['berat'], $value['satuan'], $value['hrg_beli']+0, $value['hrg_jual']+0, ($value['status']==1?'Aktif':'Tidak Aktif')
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                $row = 'gambar';
                $config['upload_path']          = './assets/images/barang';
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
                        $manipulasi['width']         = 600;
                        //$manipulasi['height']       = 250;
                        $manipulasi['new_image']       = $file[$row]['full_path'];
                        $manipulasi['create_thumb']       = true;
                        //$manipulasi['thumb_marker']       = '_thumb';
                        $this->load->library('image_lib', $manipulasi);
                        $this->image_lib->resize();
                    }
                }

                $master = array(
                    'nama' => ucwords($_POST['nama']),
                    'deskripsi' => $_POST['deskripsi'],
                    'satuan' => strtoupper($_POST['satuan']),
                    'status' => $_POST['status'],
                    'hrg_beli' => $_POST['hrg_beli'],
                    'hrg_jual' => $_POST['hrg_jual'],
                    'berat' => $_POST['berat'],
                    'best' => $_POST['best'],
                    'kel_brg_online' => $_POST['kel_brg_online']
                );

                if ($_FILES['gambar']['name']!=null) {
                    $master['gambar'] = 'assets/images/barang/'.$file['gambar']['file_name'];
                }

                if ($valid) {
                    if (isset($_POST['update'])) {
                        $this->m_crud->update_data($table, $master, "id_barang = '" . base64_decode($_GET['trx'] . "'") . "'");
                    } else {
                        $master['id_barang'] = $this->m_website->generate_kode('barang_online', date('ymd'), null);
                        $master['tgl_input'] = date('Y-m-d H:i:s');
                        $this->m_crud->create_data($table, $master);
                    }
                    echo '<script>alert("Data has been Saved"); window.location="'.base_url().$this->control.'/'.$function.'"</script>';
                } else {
                    echo '<script>alert("Data gagal disimpan"); window.location="'.base_url().$this->control.'/'.$function.'"</script>';
                }
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function fasilitas($action = null, $page=1){
        $this->access_denied(287);
        $data = $this->data;
        $function = 'fasilitas';
        $table = 'fasilitas';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Fasilitas';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
        $where = null;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "kel_brg = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->get_data($table, '*', "id_fasilitas = '".base64_decode($_GET['trx']."'")."'");
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data($table, 'id_fasilitas', $where);
            $config['per_page'] = 50;
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
            $data['master_data'] = $this->m_crud->select_limit($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'id_fasilitas asc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        }

        if (isset($_POST['export'])) {
            $data['det_report'] = $this->m_crud->read_data($table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'id_fasilitas asc'));
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:C1','A2:C2','A3:C3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:C2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:C5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:C5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama', 'C'=>'Status'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['id_fasilitas'], $value['nama'], ($value['status']==1?'Aktif':'Tidak Aktif')
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                $row = 'gambar';
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
                        $this->load->library('image_lib', $manipulasi);
                        $this->image_lib->resize();
                    }
                }

                $master = array(
                    'nama' => ucwords($_POST['nama']),
                    'status' => $_POST['status']
                );

                if ($_FILES['gambar']['name']!=null) {
                    $master['gambar'] = 'assets/images/foto/'.$file['gambar']['file_name'];
                }

                if ($valid) {
                    if (isset($_POST['update'])) {
                        $this->m_crud->update_data($table, $master, "id_fasilitas = '" . base64_decode($_GET['trx'] . "'") . "'");

                        $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
                        foreach ($read_lokasi as $item) {
                            $log = array(
                                'type' => 'U',
                                'table' => $table,
                                'data' => $master,
                                'condition' => "id_fasilitas = '" . base64_decode($_GET['trx'] . "'") . "'"
                            );

                            $data_log = array(
                                'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }
                    } else {
                        $master['id_fasilitas'] = $this->m_website->generate_kode('fasilitas', null, null);
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
                                'lokasi' => $item['Kode'],
                                'hostname' => $item['server'],
                                'db_name' => $item['db_name'],
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }
                    }
                    echo '<script>alert("Data has been Saved"); window.location="'.base_url().$this->control.'/'.$function.'"</script>';
                } else {
                    echo '<script>alert("Data gagal disimpan"); window.location="'.base_url().$this->control.'/'.$function.'"</script>';
                }
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function get_fasilitas() {
        $id = $_POST['id'];

        $get_lokasi = $this->m_crud->get_data("lokasi", "kode, nama", "kode='".$id."'");
        $get_data = $this->m_crud->join_data("fasilitas_lokasi fl", "fl.*, f.nama", "fasilitas f", "f.id_fasilitas=fl.fasilitas", "fl.lokasi='".$id."'");

        $list = '';

        if ($get_data != null) {
            foreach ($get_data as $key => $item) {
                $list_gambar = '';
                $gambar = json_decode($item['gambar'], true);
                if (is_array($gambar) && count($gambar) > 0) {
                    foreach ($gambar as $path) {
                        $list_gambar .= '<img style="margin: 5px; max-width: 80px; max-height: 80px" src="'.base_url().$path.'">';
                    }
                }
                $list .= '
                <tr>
                <td>'.($key+1).'</td>
                <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                    <ul class="dropdown-menu" role="menu">
                        <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="edit_fasilitas(\''.$item['id_fasilitas_lokasi'].'\')"><i class="fa fa-edit"></i> Edit</button></div></li>
                        <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus_fasilitas(\''.$item['id_fasilitas_lokasi'].'\')"><i class="fa fa-trash"></i> Delete</button></div></li>
                    </ul>
                </div>
                </td>
                <td>'.$item['nama'].'</td>
                <td>'.$list_gambar.'</td>
                </tr>
                ';
            }
        } else {
            $list = '
            <tr>
            <td class="text-center" colspan="4">Data fasilitas masih kosong</td>
            </tr>
            ';
        }

        echo json_encode(array(
            'list' => $list,
            'kode' => $get_lokasi['kode'],
            'nama' => $get_lokasi['nama']
        ));
    }

    public function edit_fasilitas() {
        $id = $_POST['id'];
        $lokasi = $_POST['lokasi'];

        $get_fasilitas = $this->m_crud->read_data("fasilitas", "id_fasilitas, nama", "id_fasilitas not in (select fasilitas from fasilitas_lokasi where lokasi='".$lokasi."' AND id_fasilitas_lokasi<>'".$id."')");
        $get_fl = $this->m_crud->get_data("fasilitas_lokasi", "*", "id_fasilitas_lokasi='".$id."'");

        $gambar = json_decode($get_fl['gambar'], true);
        $list_gambar = '';
        if (is_array($gambar) && count($gambar) > 0) {
            foreach ($gambar as $key => $item) {
                $list_gambar .= '
                <div class="col-md-3 cliente" style="height: 100px;" id="cont'.$key.'">
                    <div class="center-block" style="text-align: center;">
                        <img src="'.base_url().$item.'" style="max-height: 80px; max-width: 140px" class="dropdown-toggle" data-toggle="dropdown">
                        <ul class="dropdown-menu" role="menu">
                            <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus_gambar(\'cont'.$key.'\', \''.$item.'\')"><i class="fa fa-trash"></i> Delete</button></div></li>
                        </ul>
                    </div>
                </div>
                ';
            }
        }

        if ($get_fasilitas != null) {
            $list = '<option value="">Pilih Fasilitas</option>';
            foreach ($get_fasilitas as $item) {
                $list .= '<option '.($item['id_fasilitas']==$get_fl['fasilitas']?'selected':'').' value="'.$item['id_fasilitas'].'">'.$item['nama'].'</option>';
            }
        } else {
            $list = '<option value="">Tidak Ada Data</option>';
        }

        echo json_encode(array('list'=>$list, 'list_gambar'=>$list_gambar, 'gambar_sekarang'=>($get_fl['gambar']=='null'?json_encode(array()):$get_fl['gambar'])));
    }

    public function hapus_fasilitas() {
        $id = $_POST['id'];

        $this->m_crud->delete_data("fasilitas_lokasi", "id_fasilitas_lokasi='".$id."'");

        echo json_encode(array('status'=>true));
    }

    public function fasilitas_tersedia() {
        $id = $_POST['id'];

        $get_fasilitas = $this->m_crud->read_data("fasilitas", "id_fasilitas, nama", "id_fasilitas not in (select fasilitas from fasilitas_lokasi where lokasi='".$id."')");

        if ($get_fasilitas != null) {
            $list = '<option value="">Pilih Fasilitas</option>';
            foreach ($get_fasilitas as $item) {
                $list .= '<option value="'.$item['id_fasilitas'].'">'.$item['nama'].'</option>';
            }
        } else {
            $list = '<option value="">Tidak Ada Data</option>';
        }

        echo json_encode(array('list'=>$list));
    }

    public function simpan_fasilitas() {
        $fasilitas = $_POST['fasilitas'];
        $lokasi = $_POST['lokasi'];
        $param = $_POST['param'];

        $upload = $this->upload_gambar('./assets/images/foto', 'fasilitas', $_FILES['gambar']);

        $master = array(
            'fasilitas' => $fasilitas,
            'lokasi' => $lokasi
        );

        if ($param == 'add') {
            $master['id_fasilitas_lokasi'] = $this->m_website->generate_kode('fasilitas_lokasi', date('ymd'), null);
            if ($upload != false) {
                $master['gambar'] = json_encode($upload);
            }

            $this->m_crud->create_data("fasilitas_lokasi", $master);
        } else {
            $id_fasilitas = $_POST['id_fasilitas'];

            $gambar_sekarang = json_decode($_POST['gambar_sekarang'], true);
            $gambar_hapus = json_decode($_POST['hapus_gambar'], true);

            $gambar = array_values(array_diff($gambar_sekarang, $gambar_hapus));

            if ($upload != false) {
                $gambar = array_merge($gambar, $upload);
            }

            $master['gambar'] = json_encode($gambar);

            $where = "id_fasilitas_lokasi='".$id_fasilitas."'";

            $this->m_crud->update_data("fasilitas_lokasi", $master, $where);
        }

        echo json_encode(array('status'=>true));
    }

    private function upload_gambar($path, $title, $files)
    {
        $config['upload_path']          = $path;
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['max_size']             = 5120;
        $this->load->library('upload', $config);
        $valid = true;

        $this->load->library('image_lib');

        $images = array();

        $row = 'images';
        foreach ($files['name'] as $key => $image) {
            $_FILES[$row]['name'] = $files['name'][$key];
            $_FILES[$row]['type'] = $files['type'][$key];
            $_FILES[$row]['tmp_name'] = $files['tmp_name'][$key];
            $_FILES[$row]['error'] = $files['error'][$key];
            $_FILES[$row]['size'] = $files['size'][$key];

            $fileName = $title .'_'. $image;

            $config['file_name'] = $fileName;

            $this->upload->initialize($config);

            if ($this->upload->do_upload($row)) {
                $file = $this->upload->data();

                if ($file['file_name'] != null) {
                    $images[] = substr($path, 2).'/'.$file['file_name'];
                    $manipulasi['image_library'] = 'gd2';
                    $manipulasi['source_image'] = $file['full_path'];
                    $manipulasi['maintain_ratio'] = true;
                    $manipulasi['width']         = 500;
                    //$manipulasi['height']       = 250;
                    $manipulasi['new_image']       = $file['full_path'];
                    $manipulasi['create_thumb']       = true;
                    $this->image_lib->initialize($manipulasi);
                    $this->image_lib->resize();
                    $this->image_lib->clear();
                } else {
                    $images[] = json_encode($file);
                }
            }
        }

        return $images;
    }

    public function area($action = null, $page=1){
        $this->access_denied(288);
        $data = $this->data;
        $function = 'area';
        $table = 'area';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Area';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
        $where = 'l.'.$this->where_lokasi.'';
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])){
            $this->m_crud->delete_data($table, "Nama = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->get_data($table, '*', "id_area = '".base64_decode($_GET['trx']."'")."'");
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data_join($table." a", 'a.id_area', "lokasi l", "l.kode=a.lokasi", $where);
            $config['per_page'] = 50;
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
            $data['master_data'] = $this->m_crud->select_limit_join($table." a", 'a.*, l.nama nama_lokasi', "lokasi l", "l.kode=a.lokasi", $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:"a.nama asc"), null, ($page-1)*$config['per_page']+1, $config['per_page']*$page);
        }
        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                $row = 'foto';
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
                    'nama' => strtoupper($_POST['nama']),
                    'lokasi' => $_POST['lokasi']
                );

                if ($_FILES['foto']['name']!=null) {
                    $master['gambar'] = 'assets/images/foto/'.$file['foto']['file_name'];
                }

                if(isset($_POST['update'])){
                    $this->m_crud->update_data($table, $master, "id_area = '".base64_decode($_GET['trx']."'")."'");

                    $log = array(
                        'type' => 'U',
                        'table' => $table,
                        'data' => $master,
                        'condition' => "id_area = '".base64_decode($_GET['trx']."'")."'"
                    );

                    $data_log = array(
                        'lokasi' => $_POST['lokasi'],
                        'hostname' => '-',
                        'db_name' => '-',
                        'query' => json_encode($log)
                    );
                    $this->m_website->insert_log_api($data_log);
                } else {
                    $master['id_area'] = $this->m_website->generate_kode('area', date('ymd'), null);
                    $this->m_crud->create_data($table, $master);

                    $log = array(
                        'type' => 'I',
                        'table' => $table,
                        'data' => $master,
                        'condition' => ""
                    );
                    $data_log = array(
                        'lokasi' => $_POST['lokasi'],
                        'hostname' => '-',
                        'db_name' => '-',
                        'query' => json_encode($log)
                    );
                    $this->m_website->insert_log_api($data_log);
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }

    public function meja($action = null, $page=1){
        $this->access_denied(289);
        $data = $this->data;
        $function = 'meja';
        $table = 'meja';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Data Meja';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required', array('required' => '%s don`t empty'));
        $where = $this->where_lokasi;
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }
        $column = $this->session->search['column']; $search = $this->session->search['any']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$search."%')"; }

        if($action == 'delete' && isset($_GET['trx'])) {
            $this->m_crud->delete_data($table, "Nama = '".base64_decode($_GET['trx']."'"));
            echo '<script>window.location="'.base_url().$this->control.'/'.$function.'";</script>';
        } else if($action == 'edit'){
            $data['master_data'] = $this->m_crud->get_data($table, '*', "id_meja = '".base64_decode($_GET['trx']."'")."'");
        } else if($action != "add" && (!isset($_POST['save']))) {
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data("data_".$table, 'id_meja', $where);
            $config['per_page'] = 50;
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
            $data['master_data'] = $this->m_crud->select_limit("data_".$table, '*', $where, (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:"nama_area asc"), null, ($page-1)*$config['per_page']+1, $config['per_page']*$page);
        }
        if($action == "add" || $action == "edit" || isset($_POST['save'])){ $data['content'] = $view.'form_'.$function; }
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else {
            if(isset($_POST['save'])){
                $master = array(
                    'area' => $_POST['area'],
                    'kapasitas' => $_POST['kapasitas'],
                    'height' => $_POST['height'],
                    'width' => $_POST['width'],
                    'bentuk' => $_POST['bentuk']
                );

                $get_lokasi = $this->m_crud->get_data("area", "lokasi", "id_area='".$_POST['area']."'");

                if(isset($_POST['update'])){
                    $master['nama'] = $_POST['nama'];
                    $this->m_crud->update_data($table, $master, "id_meja = '".base64_decode($_GET['trx']."'")."'");

                    $log = array(
                        'type' => 'U',
                        'table' => $table,
                        'data' => $master,
                        'condition' => "id_area = '".base64_decode($_GET['trx']."'")."'"
                    );

                    $data_log = array(
                        'lokasi' => $get_lokasi['lokasi'],
                        'hostname' => '-',
                        'db_name' => '-',
                        'query' => json_encode($log)
                    );
                    $this->m_website->insert_log_api($data_log);
                } else {
                    if ((int)$_POST['nama'] <= (int)$_POST['nama2']) {
                        for ($i=(int)$_POST['nama']; $i<=(int)$_POST['nama2']; $i++) {
                            $master['nama'] = $i;
                            $master['id_meja'] = $this->m_website->generate_kode('meja', date('ymd'), null);

                            $this->m_crud->create_data($table, $master);

                            $log = array(
                                'type' => 'I',
                                'table' => $table,
                                'data' => $master,
                                'condition' => ""
                            );
                            $data_log = array(
                                'lokasi' => $get_lokasi['lokasi'],
                                'hostname' => '-',
                                'db_name' => '-',
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_api($data_log);
                        }
                    }
                }
                echo '<script>alert("Data has been Saved");window.location="'.base_url().$this->control.'/'.$function.'"</script>';
            }
            $this->load->view('bo/index', $data);
        }
    }
	public function hargaCustomer() {
        $response = array();
        $kd_brg = $_POST['kd_brg'];

        $get_data = $this->m_crud->join_data("customer c", "c.kd_cust, c.nama, isnull(bc.hrg_jual, 0) hrg_jual, b.kd_brg, b.nm_brg, b.barcode, b.hrg_jual_1", array(array("table"=>"brg_customer bc", "type"=>"LEFT"), array("table"=>"barang b", "type"=>"LEFT")), array("bc.kd_cust=c.kd_cust and bc.kd_brg='".$kd_brg."'", "b.kd_brg='".$kd_brg."'"), "c.kd_cust<>'1000001'");

        if ($get_data != null) {
            $response['status'] = true;
            $response['data'] = $get_data;
        } else {
            $response['status'] = false;
        }

        echo json_encode($response);
    }

    public function simpanHargaCustomer() {
        $response = array();
        $list = json_decode($_POST['list'], true);

        $this->db->trans_begin();

        $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama', "Kode<>'HO'");
        foreach ($list as $key => $item) {
            $this->m_crud->delete_data("brg_customer", "kd_cust='".$item['kd_cust']."' and kd_brg='".$item['kd_brg']."'");


            if ($item['hrg_jual'] != '' && $item['hrg_jual'] != 0 && $item['hrg_jual'] != '0') {
                $data_master = array("kd_cust"=>$item['kd_cust'], "kd_brg"=>$item['kd_brg'], "hrg_jual"=>$item['hrg_jual']);
                $this->m_crud->create_data("brg_customer", $data_master);
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $response['status'] = false;
        } else {
            $this->db->trans_commit();
            $response['status'] = true;
        }

        echo json_encode($response);
    }
}


