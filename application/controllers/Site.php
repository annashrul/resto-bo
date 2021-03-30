<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site extends CI_Controller {

	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
		$site_data = $this->m_website->site_data();
		$this->site = str_replace(' ', '', strtolower($site_data->title));
		$this->control = 'Site';
		
		$this->user = $this->session->userdata($this->site . 'user');
		$this->username = $this->session->userdata($this->site . 'username');
		$this->notif = $this->session->userdata($this->site . 'notif');

        /*Session lokasi*/
        $lok = $this->session->userdata($this->site.'lokasi');
        $lokasi_in = array();
        if (isset($lok)) {
            foreach ($lok as $item) {
                array_push($lokasi_in, '\'' . $item['kode'] . '\'');
            }
        }

        $this->lokasi_in = implode(',', $lokasi_in);
        if($this->lokasi_in!=""||$this->lokasi_in!=null){
            $this->where_lokasi = "Kode in (".$this->lokasi_in.")";
        }

        /*End session lokasi*/

		$this->data = array(
			'site' => $site_data,
			'account' => $this->m_website->user_data($this->user),
			'access' => $this->m_website->user_access_data($this->user)
		);
		
		$this->output->set_header("Cache-Control: no-store, no-cache, max-age=0, post-check=0, pre-check=0");
	}
    public function detail_activity(){
        $read = $this->m_crud->read_data("Aktivitas","*","Tgl='".$_POST['param']."'");
        $sebelum = '';
        $sesudah = '';
        foreach ($read as $row){
            $data1 = json_decode($row['sebelum'], true);
            $data2 = json_decode($row['sesudah'], true);
            if($data1 != null){
                foreach ($data1 as $valueSblm){
                    $array_lokasi = array();
                    $data_lokasi = json_decode($valueSblm['lokasi'], true);
                    if($data_lokasi != null){
                        for ($i = 0; $i < count($data_lokasi['lokasi_list']); $i++) {
                            array_push($array_lokasi, $data_lokasi['lokasi_list'][$i]['kode']);
                        }
                        sort($array_lokasi);
                        $sebelum.='<tr><th>lokasi = '.implode(', ', $array_lokasi).'</th></tr>';
                    }

                    foreach($valueSblm as $keysebelum => $valuesebelum){
                        if($keysebelum != 'lokasi'){
                            $sebelum.=/** @lang text */'<tr><th>'.$keysebelum.' = '.$valuesebelum.'</th></tr>';
                        }
                    }
                }
            }else{
                $sebelum.=/** @lang text */'<tr><th>data sebelum tidak tersedia</th></tr>';
            }
            if($data2 != null){
                foreach ($data2 as $valueSsdh){
                    $array_lokasi = array();
                    $data_lokasi = json_decode($valueSsdh['lokasi'], true);
                    if($data_lokasi != null){
                        for ($i = 0; $i < count($data_lokasi['lokasi_list']); $i++) {
                            array_push($array_lokasi, $data_lokasi['lokasi_list'][$i]['kode']);
                        }
                        sort($array_lokasi);
                        $sesudah.='<tr><th>lokasi = '.implode(', ', $array_lokasi).'</th></tr>';
                    }
                    foreach($valueSsdh as $keysesudah => $valuesesudah){
                        if($keysesudah != 'lokasi'){
                            $sesudah.=/** @lang text */'<tr><th>'.$keysesudah.' = '.$valuesesudah.'</th></tr>';

                        }
                    }
                }
            }else{
                $sesudah.=/** @lang text */'<tr><th>data sesudah tidak tersedia</th></tr>';
            }

        }
        echo json_encode(array("sebelum"=>$sebelum,"sesudah"=>$sesudah));
    }
	public function unset_session($session) {
        $this->session->unset_userdata($session);

        echo true;
    }
	
	public function nojs(){
		$data = $this->data;
		$data['title'] = 'Javascript not active';
		$data['redirect'] = base_url();
		$this->load->view('site/nojs');		
	}

    public function delete_ajax_trx() {
        $table = $_POST['table'];
        $condition = $_POST['condition'];
        $get_lokasi = $this->m_crud->read_data("lokasi", "kode");

        for ($i=0; $i<count($table); $i++) {
            $this->m_crud->delete_data($table[$i], $condition[$i]);

            foreach ($get_lokasi as $item) {
                $log = array(
                    'type' => 'D',
                    'table' => $table[$i],
                    'data' => "",
                    'condition' => $condition[$i]
                );

                $data_log = array(
                    'lokasi' => $item['kode'],
                    'hostname' => '-',
                    'db_name' => '-',
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);
            }
        }

        echo true;
    }

    public function get_dropdown($table, $column, $id, $default) {
        $read_data = $this->m_crud->read_data($table, "*", $column." = '".$id."'");
        $list = '<option value="">'.base64_decode($default).'</option>';

        foreach ($read_data as $row) {
            $list .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }

        echo $list;
    }

    public function get_list_dropdown($table, $column, $condition, $id, $default) {
        $read_data = $this->m_crud->read_data(base64_decode($table), base64_decode($column), base64_decode($condition)." = '".base64_decode($id)."'");
        $list = '<option value="">'.base64_decode($default).'</option>';

        foreach ($read_data as $row) {
            $list .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }

        echo $list;
    }

    public function get_list_join_dropdown($table, $column, $join, $on, $condition, $id, $default) {
        $read_data = $this->m_crud->join_data(base64_decode($table), base64_decode($column), base64_decode($join), base64_decode($on), base64_decode($condition)." = '".base64_decode($id)."'");
        $list = '<option value="">'.base64_decode($default).'</option>';

        foreach ($read_data as $row) {
            $list .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }

        echo $list;
    }

    public function delete_ajax($table, $column, $id){
        //$id = $_POST['delete_id'];
        $get_lokasi = $this->m_crud->read_data("lokasi", "kode");
        $get_activity = $this->m_crud->get_data($table,"*",$column." = '".$id."'");
        $this->m_website->add_activity(
            'aktivitas : hapus | modul : '.$table,
            $table,
            json_encode(array($get_activity)),
            json_encode(array())
        );
        if ($table == 'user_akun') {
            $this->m_crud->delete_data('user_detail', $column." = '".$id."'");

            foreach ($get_lokasi as $item) {
                $log = array(
                    'type' => 'D',
                    'table' => 'user_detail',
                    'data' => "",
                    'condition' => $column." = '".$id."'"
                );

                $data_log = array(
                    'lokasi' => $item['kode'],
                    'hostname' => '-',
                    'db_name' => '-',
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);
            }
        }
        $this->m_crud->delete_data($table, $column." = '".$id."'");
        foreach ($get_lokasi as $item) {
            $log = array(
                'type' => 'D',
                'table' => $table,
                'data' => "",
                'condition' => $column." = '".$id."'"
            );

            $data_log = array(
                'lokasi' => $item['kode'],
                'hostname' => '-',
                'db_name' => '-',
                'query' => json_encode($log)
            );
            $this->m_website->insert_log_api($data_log);
        }
        echo json_encode(array('status'=>true));
    }
	
	public function delete_ajax2($table, $where, $cek_query=null){
		//$id = $_POST['delete_id'];
		if($cek_query==null){
			$this->m_crud->delete_data($table, base64_decode($where));
			//echo json_encode(array('status'=>true));
			echo true;
		} else {
			$cek = $this->m_crud->my_query(base64_decode($cek_query));
			if($cek == null){
				$this->m_crud->delete_data($table, base64_decode($where));
				//echo json_encode(array('status'=>true));
				echo true;
			} else {
				//echo json_encode(array('status'=>false));
				echo false;
			}
		}
	}

    public function cek_data($table, $column, $id){
        if($this->m_crud->get_data($table, $column, "ltrim(rtrim(".$column.")) = '".ltrim(rtrim($id))."'")[$column] != null){
            echo true;
        } else {
            echo false;
        }
    }
	
	public function cek_data_2($table, $column, $id){
        $table = base64_decode($table);
        $column = base64_decode($column);
        $id = base64_decode($id);

		if($this->m_crud->get_data($table, $column, "ltrim(rtrim(".$column.")) = '".ltrim(rtrim($id))."'")[$column] != null){
			echo true;
		} else {
			echo false;
		}
	}

	public function count_data($table, $condition) {
	    if($this->m_crud->count_read_data(base64_decode($table), "*", base64_decode($condition)) == 0) {
	        echo 0;
        } else {
            echo 1;
        }

    }
	
	public function get_data($table, $column, $where, $id){
		$data = $this->m_crud->get_data($table, $column, $where." = '".$id."'");
		if($data[$column] != null){
			echo $data[$column];
		} else {
			echo false;
		}
	}
	
	public function search_autocomplete($table, $select, $where){
		$keyword = $this->uri->segment(6); // tangkap variabel keyword dari URL
		$select = str_replace('-', ',', $select);
		$where = str_replace('-', ',', $where);
		$where = explode(',', $where);
		$where = ((isset($where[0])?$where[0]." like '%".$keyword."%'":null).(isset($where[1])?' or '.$where[1]." like '%".$keyword."%'":null).(isset($where[2])?' or '.$where[2]." like '%".$keyword."%'":null));
		$data = $this->m_crud->read_data($table, $select, $where, null, null, 30); // cari di database
		$select = explode(',', $select);
		foreach($data as $row){ // format keluaran di dalam array
			$arr['query'] = $keyword;
			$arr['suggestions'][] = array(
				'value'	=> ((isset($select[0])?$row[$select[0]]:null).(isset($select[1])?'|'.$row[$select[1]]:null).(isset($select[2])?'|'.$row[$select[2]]:null)),
			);
		}
		echo json_encode($arr);
	}

    public function max_kode($tmp_jenis,$tmp_tanggal,$tmp_status) {
	    $jenis = base64_decode($tmp_jenis);
	    $replace_tanggal = str_replace('-','',base64_decode($tmp_tanggal));
        $tanggal = substr($replace_tanggal,2);
	    $status = base64_decode($tmp_status);

        $kode_baru = $this->m_website->generate_kode($jenis, $status, $tanggal);

        echo $kode_baru;
    }

    public function max_kode_barang($kode) {
        $kode = base64_decode($kode);
        $length = strlen(ltrim(rtrim($kode)));

        $kode_baru = $this->m_website->generate_kode_barang($kode, $length);

        echo $kode_baru;
    }

    public function max_kode_kelompok($kode) {
	    $kode = base64_decode($kode);
	    $length = strlen(ltrim(rtrim($kode)));

	    $kode_baru = $this->m_website->generate_kode_kelompok($kode, $length);

	    echo $kode_baru;
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
	
	public function approval_retur_cabang(){
		$trx = $_POST['trx_'];
		$tanggal = date('Y-m-d H:i:s');
		$this->db->trans_begin();
		$master_retur = array(
		    'kd_trx' => $trx,
            'tgl' => $tanggal
        );
		$det_log = array();
		$stock_in = array(
            'kd_trx' => $trx,
            'tgl' => $tanggal,
            'kd_brg' => $_POST['kd_brg_'],
            'saldo_awal' => 0,
            'stock_in' => $_POST['sisa_approval_'],
            'stock_out' => 0,
            'lokasi' => 'HO',
            'keterangan' => 'Retur Approval '.$_POST['kd_trx_'],
            'hrg_beli' => $_POST['hrg_beli_']
        );
		$this->m_crud->create_data('kartu_stock', $stock_in);
		array_push($det_log, $stock_in);

		$stock_out = array(
            'kd_trx' => $trx,
            'tgl' => $tanggal,
            'kd_brg' => $_POST['kd_brg_'],
            'saldo_awal' => 0,
            'stock_in' => 0,
            'stock_out' => $_POST['sisa_approval_'],
            'lokasi' => 'Retur',
            'keterangan' => 'Retur Approval '.$_POST['kd_trx_'],
            'hrg_beli' => $_POST['hrg_beli_']
        );
		$this->m_crud->create_data('kartu_stock', $stock_out);
		array_push($det_log, $stock_out);

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$trx,'jenis'=>ucfirst('Add'),'transaksi'=>'Approve Retur Cabang'), array('master'=>$master_retur,'detail'=>$det_log));

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		} else {
			$this->db->trans_commit(); $this->cart->destroy();
			//echo '<script>alert("Cash Mutation has been saved");window.location = "'.$function.'";</script>';
		}
		//redirect(base_url().$this->control .'/'. $function . '/'. base64_encode($kd_trx));
	}

    public function approval_alokasi(){
        $trx = $_POST['kd_trx_'];
        $this->db->trans_begin();

        $param = 'Add';
        $tanggal = date('Y-m-d H:i:s');
        $get_ket_1 = $this->m_crud->get_data("Kartu_stock", "keterangan", "kd_trx = '".$trx."' AND lokasi = 'MUTASI'");
        $get_ket_2 = $this->m_crud->get_data("Kartu_stock", "keterangan", "kd_trx = '".$trx."' AND lokasi <> 'MUTSI'");

        $data_approve = array(
            'kd_trx'=> $trx,
            'tgl'=> $tanggal
        );

        $det_log = array();

        $stok_in = array(
            'kd_trx' => $trx,
            'tgl' => $tanggal,
            'kd_brg' => $_POST['kd_brg_'],
            'saldo_awal' => 0,
            'stock_in' => $_POST['sisa_approval_'],
            'stock_out' => 0,
            'lokasi' => 'HO',
            'keterangan' => $get_ket_1['keterangan'],
            'hrg_beli' => $_POST['hrg_beli_']
        );
        $this->m_crud->create_data('kartu_stock', $stok_in);
        array_push($det_log, $stok_in);

        $stok_out = array(
            'kd_trx' => $trx,
            'tgl' => $tanggal,
            'kd_brg' => $_POST['kd_brg_'],
            'saldo_awal' => 0,
            'stock_in' => 0,
            'stock_out' => $_POST['sisa_approval_'],
            'lokasi' => 'MUTASI',
            'keterangan' => $get_ket_2['keterangan'],
            'hrg_beli' => $_POST['hrg_beli_']
        );
        $this->m_crud->create_data('kartu_stock', $stok_out);
        array_push($det_log, $stok_out);

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$trx,'jenis'=>ucfirst($param),'transaksi'=>ucfirst('Approval Mutasi')), array('master'=>$data_approve,'detail'=>$det_log));

        $this->m_crud->update_data("Det_Mutasi", array('status'=>'1'), "no_faktur_mutasi = '".$trx."' AND kd_brg = '".$_POST['kd_brg_']."'");

        $count_data = $this->m_crud->count_data("Det_Mutasi", "no_faktur_mutasi", "no_faktur_mutasi = '".$trx."' AND status <> '1'");
        if ($count_data == 0) {
            $this->m_crud->update_data("Master_Mutasi", array('status'=>'1'), "no_faktur_mutasi = '".$trx."'");
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit(); $this->cart->destroy();
            //echo '<script>alert("Cash Mutation has been saved");window.location = "'.$function.'";</script>';
        }
        //redirect(base_url().$this->control .'/'. $function . '/'. base64_encode($kd_trx));
    }
	
	
	public function index(){
		//redirect(strtolower($this->control).'/dashboard');
		$data = $this->data;
		$function = 'login';
		$view = null;
		$data['title'] = 'Login';
		$data['content'] = $view.$function;
		if($this->form_validation->run() == false){ $this->load->view('site/login', $data); } 
		else { $this->load->view('site/login', $data); }
	}
	
	public function dashboard(){
		//$this->access_denied(0);
		$data = $this->data;
		$function = 'dashboard';
		$view = null;
		$table = null;
		$data['title'] = 'Dashboard';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;

		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else { $this->load->view('bo/index', $data); }
	}
	
	public function log_in(){
		$username = $this->input->post('username');
		$password = md5($this->input->post('password'));
		
		$cek = $this->m_website->login($username, $password);
		if($cek <> 0){
			$lokasi = json_decode($cek->lokasi, true)['lokasi_list'];
			$this->session->set_userdata($this->site . 'isLogin', TRUE);
			$this->session->set_userdata($this->site . 'notif', '1');
			$this->session->set_userdata($this->site . 'user', $cek->user_id);
			$this->session->set_userdata($this->site . 'lokasi', $lokasi);
			$this->session->set_userdata($this->site . 'username', $username);
			$this->session->set_userdata($this->site . 'start', time());
			$this->session->set_userdata($this->site . 'expired', $this->session->userdata($this->site . 'start') + (30 * 60) );

			redirect('site/dashboard');
		} else{
			echo '<script>alert("Please check again your username and password");window.location = "'.base_url().'";</script>';
		}
	}
	
	public function logout(){
		$this->session->unset_userdata($this->site . 'isLogin');
		$this->session->unset_userdata($this->site . 'user');
		$this->session->unset_userdata($this->site . 'lokasi');
		$this->session->unset_userdata($this->site . 'username');
		$this->session->unset_userdata($this->site . 'start');
		$this->session->unset_userdata($this->site . 'expired');
		redirect(base_url());
	}

	public function set_session($session_name_, $value_) {
        $value = base64_decode($value_);
        $session_name = base64_decode($session_name_);
        $this->session->set_userdata($session_name, $value);
    }

    public function get_session($session_name_) {
        $session_name = base64_decode($session_name_);

        $session = $this->session->$session_name;

        echo $session;
    }

    public function set_session_date($session_name_, $value_) {
        $value = base64_decode($value_);
        $session_name = base64_decode($session_name_);
        $this->session->set_userdata('search', array($session_name=>$value));
    }

    public function get_session_date($type) {
        $field = 'field-date';
        $date = $this->session->search[$field];

        $explode_date = explode(' - ', $date);
        $get_date_1 = explode('/', $explode_date[0]);
        $get_date_2 = explode('/', $explode_date[1]);

        $date1 = $get_date_1[1].'/'.$get_date_1[2].'/'.$get_date_1[0];
        $date2 = $get_date_2[1].'/'.$get_date_2[2].'/'.$get_date_2[0];

        if (isset($date) && $date!=null) {
            if ($type == 'startDate') {
                echo $date1;
            } else {
                echo $date2;
            }
        } else {
            echo date('m/d/Y');
        }
    }

	public function get_dashboard($date_, $lokasi_) {
        $date = base64_decode($date_);
        $lokasi = base64_decode($lokasi_);

        $qlokasi = null;
        ($lokasi != '-')?$qlokasi=" AND mt.Lokasi='".$lokasi."'":$qlokasi=" AND mt.Lokasi in (".$this->lokasi_in.")";

        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
		
		$bulan_sekarang = substr($date2,0,7);//'2018-03-01';//base64_decode($tanggal);
        $diff = mktime(0,0,0,date('m', strtotime($bulan_sekarang)),0, date('Y', strtotime($bulan_sekarang)));
        $bulan_sebelum = date('Y-m', $diff);
		
        $omset_sekarang = " ,isnull((SELECT SUM(mt.st) FROM omset_report mt WHERE mt.lokasi=Kode AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 7) = '".$bulan_sekarang."' and mt.tgl <= '".$date2." 23:59:59'".$qlokasi."), 0) omset_sekarang";
        $omset_sebelum = " ,isnull((SELECT SUM(mt.st) FROM omset_report mt WHERE mt.lokasi=Kode AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 7) = '".$bulan_sebelum."'".$qlokasi."), 0) omset_sebelum";
        $transaksi_sekarang = " ,isnull((select count(mt.kd_trx) from Master_Trx mt where mt.Lokasi=Kode and mt.HR='S' AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 7) = '".$bulan_sekarang."' and mt.tgl <= '".$date2." 23:59:59'".$qlokasi."), 0) transaksi_sekarang";
        $transaksi_sebelum = " ,isnull((select count(mt.kd_trx) from Master_Trx mt where mt.Lokasi=Kode and mt.HR='S' AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 7) = '".$bulan_sebelum."'".$qlokasi."), 0) transaksi_sebelum";
		$read_lokasi = $this->m_crud->read_data("Lokasi", "Kode, Nama".$omset_sebelum.$omset_sekarang.$transaksi_sebelum.$transaksi_sekarang, null, "Kode, Nama");
		$label6=[]; $om_sekarang=[]; $om_sebelum=[]; $tr_sekarang=[]; $tr_sebelum=[];
		foreach ($read_lokasi as $row) { 
			if($row['omset_sebelum']>0 || $row['omset_sekarang']>0){
				if ($row['omset_sebelum'] == 0) {
					$persentase = 0;
				} else {
					$persentase = (($row['omset_sekarang'] - $row['omset_sebelum']) / $row['omset_sebelum']) * 100;
				}
				array_push($label6, $row['Nama']);
				array_push($om_sekarang, ($row['omset_sekarang']+0));
				array_push($om_sebelum, ($row['omset_sebelum']+0));
				array_push($tr_sekarang, ($row['transaksi_sekarang']+0));
				array_push($tr_sebelum, ($row['transaksi_sebelum']+0));
			}
		}
		$data6 = array(
			'omset' => array('sekarang'=>$om_sekarang, 'sebelum'=>$om_sebelum),
			'transaksi' => array('sekarang'=>$tr_sekarang, 'sebelum'=>$tr_sebelum)
		);
		
        $label = array('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
        $data = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

        $penjualan = 0;
        $dis_item = 0;
        $tax = 0;
        $srv = 0;
        $diskon = 0;
        $st = 0;

        $get_head = $this->m_crud->get_data("report_trx", "sum(st) st, sum(dis_rp + disc_item) diskon, sum(tax) tax, sum(service) service, count(kd_trx) trx", "tgl between '".$date1."' and '".$date2."'".($lokasi!='-'?" and lokasi = '".$lokasi."'":" and lokasi in (".$this->lokasi_in.")"));

        $count_data_penjualan = $this->m_crud->count_data_over("Master_Trx mt, Det_Trx dt", "mt.kd_trx", /*dt.qty>0 AND*/"mt.HR='S' AND mt.kd_trx=dt.kd_trx AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'".$qlokasi, null, "mt.kd_trx");
        $read_data_grafik = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt", "SUBSTRING(CONVERT(VARCHAR, jam, 120), 12, 2) jam, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) ddis, SUM(mt.dis_rp) mdis, SUM(mt.kas_lain) kln", /*dt.qty>0 AND*/"mt.HR='S' AND mt.kd_trx=dt.kd_trx AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'".$qlokasi, "SUBSTRING(CONVERT(VARCHAR, mt.jam, 120), 12, 2)", "SUBSTRING(CONVERT(VARCHAR, mt.jam, 120), 12, 2)");
        $get_diskon = $this->m_crud->get_data("Master_Trx mt", "SUM(mt.dis_rp) mdis, SUM(mt.kas_lain) kln", "mt.HR='S' AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'".$qlokasi);
        foreach ($read_data_grafik as $row) {
            $penjualan = $penjualan + ($row['gross_sales']+0);
            $data[(int)$row['jam']] = ($row['gross_sales']+0);
            $dis_item = $dis_item + $row['ddis'];
        }
        $diskon = $get_diskon['mdis'] + $row['kln'] + $dis_item;
        $transaksi = $count_data_penjualan;

        $label2 = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
        $data2 = array(0,0,0,0,0,0,0);
        $read_data_grafik2 = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt", "Datename(weekday, tgl) AS hari, case Datename(weekday, tgl) when 'monday' then 1 when 'tuesday' then 2 when 'wednesday' then 3 when 'thursday' then 4 when 'friday' then 5 when 'saturday' then 6 when 'sunday' then 7 end as hari_ke, SUM(dt.qty*dt.hrg_jual) gross_sales", "mt.HR='S' AND dt.qty>0 AND mt.kd_trx=dt.kd_trx AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'".$qlokasi, "hari_ke", "Datename(weekday, tgl)");
        foreach ($read_data_grafik2 as $row2) {
            $data2[(int)$row2['hari_ke']-1] = ($row2['gross_sales']+0);
        }

        $label3 = [];
        $data3 = [];
        $read_data_top_item = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br","ltrim(rtrim(br.nm_brg)) nm_brg, SUM(dt.qty) qty","mt.kd_trx=dt.kd_trx AND dt.qty>0 AND dt.kd_brg=br.kd_brg AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'".$qlokasi,"SUM(dt.qty) DESC","br.nm_brg",10);
        foreach ($read_data_top_item as $row3) {
            array_push($label3, $row3['nm_brg']);
            array_push($data3, $row3['qty']);
        }

        $label3_2 = [];
        $data3_2 = [];
        $read_data_top_item = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br","ltrim(rtrim(br.nm_brg)) nm_brg, SUM(dt.qty*dt.hrg_jual) gross_sales","mt.kd_trx=dt.kd_trx AND dt.qty>0 AND dt.kd_brg=br.kd_brg AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'".$qlokasi,"SUM(dt.qty*dt.hrg_jual) DESC","br.nm_brg",10);
        foreach ($read_data_top_item as $row3_2) {
            array_push($label3_2, $row3_2['nm_brg']);
            array_push($data3_2, ($row3_2['gross_sales']+0));
        }

        $label4 = [];
        $data4 = [];
        $read_data_top_cat = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br, kel_brg kb","ltrim(rtrim(kb.nm_kel_brg)) nm_kel_brg, SUM(dt.qty) qty","mt.kd_trx=dt.kd_trx AND dt.qty>0 AND dt.kd_brg=br.kd_brg AND br.kel_brg=kb.kel_brg AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'".$qlokasi,"SUM(dt.qty) DESC","kb.nm_kel_brg",10);
        foreach ($read_data_top_cat as $row4) {
            array_push($label4, $row4['nm_kel_brg']);
            array_push($data4, $row4['qty']);
        }

        $label4_2 = [];
        $data4_2 = [];
        $read_data_top_cat2 = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br, kel_brg kb","ltrim(rtrim(kb.nm_kel_brg)) nm_kel_brg, SUM(dt.qty*dt.hrg_jual) gross_sales","mt.kd_trx=dt.kd_trx AND dt.qty>0 AND dt.kd_brg=br.kd_brg AND br.kel_brg=kb.kel_brg AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'".$qlokasi,"SUM(dt.qty*dt.hrg_jual) DESC","kb.nm_kel_brg",10);
        foreach ($read_data_top_cat2 as $row4_2) {
            array_push($label4_2, $row4_2['nm_kel_brg']);
            array_push($data4_2, ($row4_2['gross_sales']+0));
        }

        $label5 = [];
        $data5 = [];
        $read_data_top_supp = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br, Group1 gr1","ltrim(rtrim(gr1.Nama)) nm_supplier, SUM(dt.qty) qty","mt.kd_trx=dt.kd_trx AND dt.qty>0 AND dt.kd_brg=br.kd_brg AND br.Group1=gr1.Kode AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'".$qlokasi,"SUM(dt.qty) DESC","gr1.Nama",10);
        foreach ($read_data_top_supp as $row5) {
            array_push($label5, $row5['nm_supplier']);
            array_push($data5, $row5['qty']);
        }

        $label5_2 = [];
        $data5_2 = [];
        $read_data_top_supp2 = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br, Group1 gr1","ltrim(rtrim(gr1.Nama)) nm_supplier, SUM(dt.qty*dt.hrg_jual) gross_sales","mt.kd_trx=dt.kd_trx AND dt.qty>0 AND dt.kd_brg=br.kd_brg AND br.Group1=gr1.Kode AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'".$qlokasi,"SUM(dt.qty*dt.hrg_jual) DESC","gr1.Nama",10);
        foreach ($read_data_top_supp2 as $row5_2) {
            array_push($label5_2, $row5_2['nm_supplier']);
            array_push($data5_2, ($row5_2['gross_sales']+0));
        }


        $net = $get_head['st']-$get_head['diskon'];
        $penjualan = $net + $get_head['tax'] + $get_head['service'];
        echo json_encode(array(
			"gross_sales" => array(
				"label" => $label, 
				"data" => $data
			), 
			"gross_sales2" => array(
				"label" => $label2, 
				"data" => $data2
			),
            "head" => array(
                "penjualan" => number_format($penjualan),
                "transaksi" => $get_head['trx'],
                "net" => number_format($net),
                "tax" => number_format($get_head['tax']),
                "srv" => number_format($get_head['service']),
                "avg" => number_format($penjualan/$get_head['trx']),
                "st"=>number_format($get_head['st']),
                "dsc"=>number_format($get_head['diskon'])
            ),
			"top_item" => array(
				"label" => $label3, 
				"data" => $data3, 
				"label2" => $label3_2, 
				"data2" => $data3_2
			), 
			"top_cat" => array(
				"label" => $label4, 
				"label2" => $label4_2, 
				"data" => $data4, 
				"data2" => $data4_2
			), 
			"top_supp" => array(
				"label" => $label5, 
				"label2" => $label5_2, 
				"data" => $data5, 
				"data2" => $data5_2
			),
			"report_bulan" => array(
				"label" => $label6,
				"data_omset" => $data6['omset'],
				"data_transaksi" => $data6['transaksi'],
			)
		));
    }

    public function valid_otorisasi($password) {
	    $password = base64_decode($password);

	    $valid = $this->m_crud->count_data("user_akun", "user_id", "password_otorisasi = '".$password."'");

        echo $valid;
    }

    public function add_activity($message, $status = null) {
	    $message = base64_decode($message);

	    $this->m_website->add_activity($message, $status);

	    return true;
    }

    public function get_nama($id) {
	    echo $this->m_website->get_nama_user($id);
    }

    public function insert_log() {
	    $trx_no = base64_decode($_POST['data']);
	    $id = explode('-', $trx_no);
	    $param = 'Delete';
	    $kd_trx = array(
	        'BL' => 'Pembelian',
            'MU' => 'Alokasi',
            'MC' => 'Branch',
            'PO' => 'Purchase Order',
            'NB' => 'Retur Pembelian',
            'RR' => 'Retur Cabang',
            'AV' => 'Approve Retur Cabang',
            'DN' => 'Delivery Note',
            'AA' => 'Adjustment',
            'EX' => 'Expedisi',
            'BH' => 'Bayar Hutang',
            'KB' => 'Kontra Bon',
            'BK' => 'Bayar Kontra Bon',
            'RO' => 'Approve Order',
			'MO' => 'Purchase Order Cabang'
        );

	    if (count($id) > 1) {
            $transaksi = $kd_trx[$id[0]];
        } else {
	        $transaksi = 'Packing';
        }

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$trx_no,'jenis'=>ucfirst($param),'transaksi'=>$transaksi), array('master'=>array(),'detail'=>array()));
    }
}

