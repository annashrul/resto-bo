<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utility extends CI_Controller {

	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
		$site_data = $this->m_website->site_data();
		$this->site = str_replace(' ', '', strtolower($site_data->title));
		$this->control = 'Utility';
		
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
    public function log_aktivitas(){
        $this->access_denied(273);

        $data = $this->data;
        $function = 'log_aktivitas';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = 'Aktivitas';
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Log Aktivitas';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date']));
        }

        $search = $this->session->search['any']; $date = $this->session->search['field-date'];
        if (isset($date) && $date != null) {
            $explode_date = explode(' - ', $date);
            $date1 = str_replace('/','-',$explode_date[0]);
            $date2 = str_replace('/','-',$explode_date[1]);
            $tgl_awal = $date1;
            $tgl_akhir = $date2;
        }
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "CONVERT(DATE, Tgl) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        }else{
            ($where == null) ? null : $where .= " and ";
            $where .= "CONVERT(DATE, Tgl) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(Kd_kasir like '%".$search."%' or nm_kasir like '%".$search."%' or Aktivitas like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data($table, 'Tgl', $where, 'Tgl DESC');
        $config['per_page'] = 20;
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
        $data['report'] = $this->m_crud->read_data($table, "*", $where, 'Tgl desc', null, $config['per_page'], ($page-1)*$config['per_page'],null);
//        var_dump($data['report']);die();
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }
	public function log_otorisasi($action = null, $id = null) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '2048M');
		ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
		ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
		
        $this->access_denied(271);
        $data = $this->data;
        $function = 'log_otorisasi';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Log Otorisasi';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "jenis='BO' AND status IS NULL";
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){ 
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date']));
        }

        $search = $this->session->search['any']; $date = $this->session->search['field-date']; 
        if (isset($date) && $date != null) {
        	$explode_date = explode(' - ', $date);
        	$date1 = str_replace('/','-',$explode_date[0]);
        	$date2 = str_replace('/','-',$explode_date[1]);
        	$tgl_awal = $date1;
			$tgl_akhir = $date2;
		}
		if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        }else{
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(nm_kasir like '%".$search."%' or aktivitas like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data("aktivitas", 'tgl', $where, 'tgl desc', null);
        $config['per_page'] = 20;
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
        $data['report'] = $this->m_crud->select_limit('aktivitas', "tgl, nm_kasir, aktivitas", $where, 'tgl desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
		
		if(isset($_POST['to_excel'])){
            $data['report'] = $this->m_crud->read_data('aktivitas', "tgl, nm_kasir, aktivitas", $where, 'tgl desc', null);
			$baca = $data['report'];
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
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Nama User', 'C'=>'Aktivitas'
				)
            );
			
            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['tgl'], $value['nm_kasir'], $value['aktivitas']
                );
            }
			
            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']).'.xls', $header, $body);
        }
		
        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function log_transaksi($action = null, $id = null) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '2048M');
        ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

        $this->access_denied(272);
        $data = $this->data;
        $function = 'log_transaksi';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Log Transaksi';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date']));
        }

        $search = $this->session->search['any']; $date = $this->session->search['field-date'];
        if (isset($date) && $date != null) {
            $explode_date = explode(' - ', $date);
            $date1 = str_replace('/','-',$explode_date[0]);
            $date2 = str_replace('/','-',$explode_date[1]);
            $tgl_awal = $date1;
            $tgl_akhir = $date2;
        }
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "CONVERT(DATE, tanggal) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        }else{
            ($where == null) ? null : $where .= " and ";
            $where .= "CONVERT(DATE, tanggal) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(transaksi like '%".$search."%' or kd_trx like '%".$search."%' or jenis like '%".$search."%' or id_user like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data("log_transaksi", 'tanggal', $where, 'tanggal DESC');
        $config['per_page'] = 20;
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
        $data['report'] = $this->m_crud->select_limit('log_transaksi', "*", $where, 'tanggal desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $data['report'] = $this->m_crud->read_data('log_transaksi', "*", $where, 'tanggal desc', null);
            $baca = $data['report'];
            $header = array(
                'merge' 	=> array('A1:E1','A2:E2','A3:E3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:E5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:E5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Transaksi', 'B'=>'Jenis', 'C'=>'Kode Transaksi', 'D'=>'Tanggal', 'E'=>'Nama User'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['transaksi'], $value['jenis'], $value['kd_trx'], date('Y-m-d H:i:s', strtotime($value['tanggal'])), $value['id_user']
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']).'.xls', $header, $body);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function detail_log() {
	    $id = $_POST['id'];

	    $get_data = $this->m_crud->get_data("log_transaksi", "detail_trx", "id_log='".$id."'");
	    $res = json_decode($get_data['detail_trx'], true);

	    $master = '';
	    foreach ($res['master'] as $key => $item) {
	        $master .= '
                <div class="col-sm-4"><b>'.$key.'</b></div><div class="col-sm-8"><b> : </b>'.$item.'</div>
	        ';
        }
	    $list = '';
	    $head = '';
	    foreach ($res['detail'] as $key => $item) {
            $list .= '<tr>';
            if ($key == 0) {
                $head .= '<tr>';
            }
	        foreach ($item as $title => $data) {
	            if ($key == 0) {
	                $head .= '<th>'.$title.'</th>';
                }
                $list .= '<td>'.$data.'</td>';
            }
            if ($key == 0) {
                $head .= '</tr>';
            }
            $list .= '</tr>';
        }

	    echo json_encode(array('master'=>$master,'head'=>$head,'list'=>$list));
    }

    public function cetak_price_tag(){
		ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
		ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
		ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
		
        $this->access_denied(183);
        $data = $this->data;
        $function = 'cetak_price_tag';
        $table = $function;
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Cetak Price Tag';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['barang'] = array();
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, Nama, serial');
        $data['data_group1'] = $this->m_crud->read_data('group1','Kode, Nama');
        $data['data_group2'] = $this->m_crud->read_data('group2','Kode, Nama');
        $data['data_kel_brg'] = $this->m_crud->read_data('kel_brg','kel_brg, nm_kel_brg');
		
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			$this->session->set_userdata('search', array('any'=>$_POST['any'], 'column'=>$_POST['column'], 'lokasi_barang'=>$_POST['lokasi_barang'], 'group1'=>$_POST['group1'], 'group2'=>$_POST['group2'], 'kel_brg'=>$_POST['kel_brg'], 'harga_baru'=>(isset($_POST['harga_baru'])?1:0)));
		}
		$column = $this->session->search['column']; $any = $this->session->search['any']; $lokasi_barang=$this->session->search['lokasi_barang']; $group1=$this->session->search['group1']; $group2=$this->session->search['group2']; $kel_brg=$this->session->search['kel_brg']; $harga_baru=$this->session->search['harga_baru'];
		
		$where=null;
		if(isset($any)&&$any!=null){ ($where==null)?null:$where.=" and "; $where.="(".$column." like '%".$any."%')"; }
		if(isset($lokasi_barang)&&$lokasi_barang!=null){ ($where==null)?null:$where.=" and "; $where.="(ks.lokasi = '".$lokasi_barang."')"; }
		if(isset($group1)&&$group1!=null){ ($where==null)?null:$where.=" and "; $where.="(br.group1 = '".$group1."')"; }
		if(isset($group2)&&$group2!=null){ ($where==null)?null:$where.=" and "; $where.="(br.group2 = '".$group2."')"; }
		if(isset($kel_brg)&&$kel_brg!=null){ ($where==null)?null:$where.=" and "; $where.="(br.kel_brg = '".$kel_brg."')"; }
		if(isset($harga_baru)&&$harga_baru==1){ ($where==null)?null:$where.=" and "; $where.="(br.hrg_jual_1 <> br.hrg_sebelum)"; }
		
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			$data['barang'] = $this->m_crud->join_data('barang br', 
				"br.kd_brg, br.barcode, br.nm_brg, br.deskripsi, br.hrg_jual_1, g1.nama nama_group1, g2.nama nama_group2, kb.nm_kel_brg nama_kelompok", 
				array("kartu_stock ks", "group1 g1", "group2 g2", "kel_brg kb"), array("br.kd_brg=ks.kd_brg", "br.group1=g1.kode", "br.group2=g2.kode", "br.kel_brg=kb.kel_brg"), 
				$where, "br.tgl_update desc", "br.kd_brg, br.barcode, br.nm_brg, br.deskripsi, br.hrg_jual_1, g1.nama, g2.nama, kb.nm_kel_brg, br.tgl_update");
        }
		
        $this->load->view('bo/index', $data);
    }
	
	public function get_tr_temp_m_pt() {
        $get_data = $this->m_crud->get_data("tr_temp_m", "*", "(m3 = '".$this->user."') AND (m1 = 'price_tag')");

        echo json_encode($get_data);
    }
	
	public function add_tr_temp_m_pt() {
        $data = array(
            'm1' => 'price_tag',
            'm2' => $_POST['lokasi_'],
            'm3' => $this->user
        );

        $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "(m3 = '".$this->user."') AND (m1 = 'price_tag')");

        if ($cek_data == 1) {
            $this->m_crud->update_data("tr_temp_m", array("m2" => $_POST['lokasi_']), "(m3 = '".$this->user."') AND (m1 = 'price_tag')");
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }
    }
	
	public function get_tr_temp_d_pt() {
        $list_barang = '';
        $read_data = $this->m_crud->read_data("tr_temp_d", "*", "(d9 = '".$this->user."') AND (d1 = 'price_tag')", "CONVERT(INTEGER, d10) ASC");

        $no = 1;
        $col = 0;
        foreach ($read_data as $row) {
            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d3'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button></td>
                                <td>' . $row['d2'] . '</td>
                                <td>' . $row['d3'] . '</td>
                                <td>' . $row['d4'] . '</td>
                                <td>' . $row['d6'] . '</td>
                                <td>' . $row['d5'] . '</td>
                                <td class="text-right">' . number_format($row['d8']) . '</td>
                            </tr>';
								//<td><input onblur="update_tmp_detail(\'' . $row['d3'] . '\', \'d7\', $(this).val())" onfocus="this.select()" onkeyup="return to_barcode(event)" type="number" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-diskon" value="' . $row['d7'] . '"></td>
            $col = $no;
            $no++;
        }
        $list_barang .= '<input type="hidden" id="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang));
    }
	
	public function update_tr_temp_d_pt($tmp_barcode, $tmp_column, $tmp_value) {
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        $this->m_crud->update_data("tr_temp_d", array($column => $value), "(d1 = 'price_tag') AND (d3 = '".$barcode."') AND (d9 = '".$this->user."')");
    }
	
	public function delete_tr_temp_d_pt($tmp_barcode) {
        $barcode = base64_decode($tmp_barcode);

        /*$get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d9", "(SUBSTRING(d1,1,2) = 'PO') AND (d10 = '".$barcode."') AND (d11 = '".$this->user."')");

        if ($get_tmp_data['d9'] > 1) {
            $data = array(
                'd9' => (int)$get_tmp_data['d9'] - 1
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d11 = '".$this->user."') AND (d10 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'PO')");
        }else {
            $this->m_crud->delete_data("tr_temp_d", "(d11 = '".$this->user."') AND (d10 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'PO')");
        }*/

        $this->m_crud->delete_data("tr_temp_d", "(d9 = '".$this->user."') AND (d3 = '".$barcode."') AND (d1 = 'price_tag')");

        echo true;
    }
	
	public function get_barang_pt($tmp_barcode, $tmp_cat_cari, $tmp_lokasi) {
        $cat_cari = base64_decode($tmp_cat_cari);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi));
        $lokasi_barang = $explode_lokasi[0];
        //$supplier = base64_decode($tmp_supplier);

        if ($cat_cari == 1) {
            $col_barang = 'barang.kd_brg';
            $col_tmp = 'd2';
        } else if ($cat_cari == 2) {
            $col_barang = 'barang.barcode';
            $col_tmp = 'd3';
        } else if ($cat_cari == 3) {
            $col_barang = 'barang.Deskripsi';
            $col_tmp = 'd5';
        }

        $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d7", "(d1 = 'price_tag') AND (".$col_tmp." = '".$barcode."') AND (d9 = '".$this->user."')");

        if ($get_tmp_data != '') {
            $data = array(
                'd7' => 1 //(int)$get_tmp_data['d7'] + 1
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d9 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND (d1 = 'price_tag')");
            echo json_encode(array('status' => 1));
        }else {
            $get_barang = $this->m_crud->get_data("barang, barang_hrg", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, Group1, barang.nm_brg, barang.satuan, barang.kel_brg, barang.hrg_beli, barang_hrg.hrg_jual_1", "(barang.kd_brg = barang_hrg.barang) AND (barang_hrg.lokasi = '".$lokasi_barang."') AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
            if ($get_barang != '') {
                $this->insert_tr_temp_d_pt($lokasi_barang, $get_barang, $barcode);
                echo json_encode(array('status' => 1));
            }else {
                $get_barang = $this->m_crud->get_data("barang, barang_hrg", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, Group1, barang.nm_brg, barang.kel_brg, barang.satuan, barang.hrg_beli, barang_hrg.hrg_jual_1, barang_hrg.ppn", "(barang.kd_brg = barang_hrg.barang) AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                if ($get_barang != '') {
                    $this->insert_tr_temp_d_pt($lokasi_barang, $get_barang, $barcode);
                    echo json_encode(array('status' => 1));
                }else {
                    $get_barang = $this->m_crud->get_data("barang, barang_hrg", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, Group1, barang.nm_brg, barang.kel_brg, barang.satuan, barang.hrg_beli, barang_hrg.hrg_jual_1, barang_hrg.ppn", "(barang.kd_brg = barang_hrg.barang) AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                    if ($get_barang != '') {
                        $this->insert_tr_temp_d_pt($lokasi_barang, $get_barang, $barcode);
                        //echo json_encode(array('status' => 2, 'notif' => "Barang dari supplier ".$supplier." tidak tersedia!"));
                    }else {
                        echo json_encode(array('status' => 3, 'notif' => "Barang tidak tersedia!"));
                    }
                }
            }
        }
    }
	
	public function insert_list_pt(){
		$this->m_crud->delete_data("tr_temp_d", "(d9 = '".$this->user."') AND (d1 = 'price_tag')");
		for($i=1; $i<=$_POST['jumlah']; $i++){
			if(isset($_POST['check'.$i])){
				$get_barang = array(
					'kd_brg' => $_POST['kd_brg'.$i],
					'nm_brg' => $_POST['nm_brg'.$i],
					'Group1' => $_POST['group1'],
					'Deskripsi' => $_POST['deskripsi'.$i],
					'hrg_jual_1' => $_POST['hrg_jual_1'.$i],
				);
				$this->insert_tr_temp_d_pt($_POST['lokasi_barang'], $get_barang, $_POST['barcode'.$i]);	
			}
		}
		echo true;
	}
	
	public function insert_tr_temp_d_pt($lokasi, $get_barang, $barcode) {
        $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d10)) id", "(d1 = 'price_tag') AND (d9 = '".$this->user."')");
        $data = array(
            'd1' => 'price_tag',
            'd2' => $get_barang['kd_brg'],
            'd3' => $barcode,
            'd4' => $get_barang['nm_brg'],
            'd5' => $get_barang['Group1'],
            'd6' => $get_barang['Deskripsi'],
            'd7' => 1,
            'd8' => $get_barang['hrg_jual_1'],
            'd9' => $this->user,
            'd10' => ((int)$get_max_id['id']+1),
            'd11' => $lokasi
        );

        $this->m_crud->create_data("tr_temp_d", $data);
    }
	
	public function delete_trans_pt() {
        $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "(m3 = '".$this->user."') AND (m1 = 'price_tag')");
        $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "(d9 = '".$this->user."') AND (d1 = 'price_tag')");

        if ($delete_data_master && $delete_data_detail) {
            echo true;
        }
    }
	
	public function generate_barcode($param='', $data=array(), $kode='', $article='') {
        /*if ($param == 'view') {
            $data = json_decode(base64_decode($data), true);
            $data['kode'] = base64_decode($kode);
            $data['article'] = base64_decode($article);
            $this->load->view("bo/Cetak/generate_barcode", array('data'=>$data));
        }*/
		
        if (isset($_POST['name'])) {
            $data = $_REQUEST['data'];
            $path = 'assets/images/barcode_item/';
            $name = $_POST['name'];
            $filename = $path.$name.'.png';
            $rawImage = $data;
            $removeheaders = substr($rawImage,strpos($rawImage,",")+1);
            $decode = base64_decode($removeheaders);
            $fopen = fopen($filename,'wb');
            fwrite($fopen,$decode);

            //$this->m_crud->update_data("det_approve", array('barcode_image'=>$filename), "trx_no='".$_POST['kode']."' AND article='".$_POST['article']."'");

            echo true;
        }
    }
	
	public function cetak_barcode_custom(){
        $this->access_denied(184);
        $data = $this->data;
        $function = 'cetak_barcode_custom';
        $table = $function;
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Cetak Barcode Custom';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
		
        $this->load->view('bo/index', $data);
    }
	
	public function get_tr_temp_d_custom() {
        $list_barang = '';
        $read_data = $this->m_crud->read_data("tr_temp_d", "*", "(d9 = '".$this->user."') AND (d1 = 'barcode_custom')", "CONVERT(INTEGER, d10) ASC");

        $no = 1;
        $col = 0;
        foreach ($read_data as $row) {
            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td>
									<a href="'.base_url().'assets/images/barcode_item/'.$row['d4'].'.png" download><button type="button" class="btn btn-primary"><i class="fa fa-download"></i></button></a>
									<button type="button" onclick="hapus_barang(\'' . $row['d3'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button>
								</td>
                                <td id="tdgb'.$no.'">
									<!--start area print-->
									<div id="generate_barcode'.$no.'" class="change_size'.$no.'" style="float:left; background-color: white;">
										<div class="row change_size'.$no.'" style="margin-left:1mm; margin-right: 1mm; font-size:15pt; color:black">
											<div class="col-sm-12">
												<b>'.$row['d4'].'</b>
											</div>
										</div>
										<div class="row change_size'.$no.'" style="margin-left:1mm; margin-right: 1mm; color:black;">
											<div class="col-sm-12">
												<img class="change_size'.$no.'" style="margin-left: 1mm" id="brcd'.$no.'">
											</div>
										</div>
										<!--<div class="row" style="margin-left:1mm; margin-right: 1mm; font-size:15pt; color:black;">
											<div class="col-sm-12">
												'.$row['d2'].'
											</div>
										</div>-->
										<div class="row change_size'.$no.'" style="margin-left:1mm; margin-right: 1mm; margin-top: 0px; font-size:15pt; color:black;">
											<div class="col-sm-12">
												Rp. <b class="change_size'.$no.'" style="font-size: 18pt">'.number_format($row['d8']).'</b>
											</div>
										</div>
									</div>
									<!--end area print-->
								</td>
                                <td id="barcode'.$no.'">' . $row['d2'] . '</td>
                                <td id="nama'.$no.'">' . $row['d4'] . '</td>
                                <td class="text-right">' . number_format($row['d8']) . '</td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d3'] . '\', \'d7\', $(this).val())" onfocus="this.select()" onkeyup="return to_barcode(event)" type="number" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-diskon" value="' . $row['d7'] . '"></td>
                            </tr>';
            $col = $no;
            $no++;
        }
        $list_barang .= '<input type="hidden" id="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang));
    }
	
	public function insert_tr_temp_d_custom() {
		$barcode = str_replace("'","`",$_POST['barcode']);
		$nama = str_replace("'","`",$_POST['nama']);
		$harga = 0+str_replace(',','',$_POST['harga']);
		
        $get_data = $this->m_crud->get_data('tr_temp_d', 'd1, d7', "(d9 = '".$this->user."') AND (d2 = '".$barcode."') AND (d1 = 'barcode_custom')");
		
		$data = array(
            'd1' => 'barcode_custom',
            'd2' => $barcode,
            'd3' => $barcode,
            //'d4' => str_replace("'","`",$_POST['nama']),
            //'d5' => $get_barang['Group1'],
            //'d6' => $get_barang['Deskripsi'],
            'd7' => 1 + ($get_data['d7']),
            //'d8' => str_replace(',','',$_POST['harga']),
            'd9' => $this->user,
            //'d10' => ((int)$get_max_id['id']+1),
            //'d11' => $lokasi
        );
		
		if($get_data!=null){
			$this->m_crud->update_data("tr_temp_d", $data, "(d9 = '".$this->user."') AND (d2 = '".$barcode."') AND (d1 = 'barcode_custom')");
		} else {
			$get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d10)) id", "(d1 = 'barcode_custom') AND (d9 = '".$this->user."')");
        	$data['d4'] = $nama;
        	$data['d8'] = $harga;
        	$data['d10'] = ((int)$get_max_id['id']+1);
        	$this->m_crud->create_data("tr_temp_d", $data);
		}
		echo json_encode(array('status' => 1));
    }
	
	public function delete_tr_temp_d_custom($tmp_barcode) {
        $barcode = base64_decode($tmp_barcode);
		
        $this->m_crud->delete_data("tr_temp_d", "(d9 = '".$this->user."') AND (d3 = '".$barcode."') AND (d1 = 'barcode_custom')");

        echo true;
    }
	
	public function delete_trans_custom() {
        $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "(m3 = '".$this->user."') AND (m1 = 'barcode_custom')");
        $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "(d9 = '".$this->user."') AND (d1 = 'barcode_custom')");
		
        if ($delete_data_master && $delete_data_detail) {
            echo true;
        }
    }
	
	public function cetak_barcode(){
        $this->access_denied(181);
        $data = $this->data;
        $function = 'cetak_barcode';
        $table = $function;
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Cetak Barcode';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, Nama, serial');

        $this->load->view('bo/index', $data);
    }

    public function add_tr_temp_m() {
        $data = array(
            'm1' => 'barcode',
            'm2' => $_POST['lokasi_'],
            'm3' => $this->user
        );

        $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "(m3 = '".$this->user."') AND (m1 = 'barcode')");

        if ($cek_data == 1) {
            $this->m_crud->update_data("tr_temp_m", array("m2" => $_POST['lokasi_']), "(m3 = '".$this->user."') AND (m1 = 'barcode')");
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }
    }

    public function get_tr_temp_m() {
        $get_data = $this->m_crud->get_data("tr_temp_m", "*", "(m3 = '".$this->user."') AND (m1 = 'barcode')");

        echo json_encode($get_data);
    }

    public function get_tr_temp_d() {
        $list_barang = '';
        $read_data = $this->m_crud->read_data("tr_temp_d", "*", "(d9 = '".$this->user."') AND (d1 = 'barcode')", "CONVERT(INTEGER, d10) ASC");

        $no = 1;
        $col = 0;
        foreach ($read_data as $row) {
            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d3'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button></td>
                                <td>' . $row['d2'] . '</td>
                                <td>' . $row['d3'] . '</td>
                                <td>' . $row['d4'] . '</td>
                                <td>' . $row['d6'] . '</td>
                                <td>' . $row['d5'] . '</td>
                                <td class="text-right">' . number_format($row['d8']) . '</td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d3'] . '\', \'d7\', $(this).val())" onfocus="this.select()" onkeyup="return to_barcode(event)" type="number" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-diskon" value="' . $row['d7'] . '"></td>
                            </tr>';
            $col = $no;
            $no++;
        }
        $list_barang .= '<input type="hidden" id="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang));
    }

    public function insert_tr_temp_d($lokasi, $get_barang, $barcode) {
        $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d10)) id", "(d1 = 'barcode') AND (d9 = '".$this->user."')");
        $data = array(
            'd1' => 'barcode',
            'd2' => $get_barang['kd_brg'],
            'd3' => $barcode,
            'd4' => $get_barang['nm_brg'],
            'd5' => $get_barang['Group1'],
            'd6' => $get_barang['Deskripsi'],
            'd7' => 1,
            'd8' => $get_barang['hrg_jual_1'],
            'd9' => $this->user,
            'd10' => ((int)$get_max_id['id']+1),
            'd11' => $lokasi
        );

        $this->m_crud->create_data("tr_temp_d", $data);
    }

    public function update_tr_temp_d($tmp_barcode, $tmp_column, $tmp_value) {
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        $this->m_crud->update_data("tr_temp_d", array($column => $value), "(d1 = 'barcode') AND (d3 = '".$barcode."') AND (d9 = '".$this->user."')");
    }

    public function delete_tr_temp_d($tmp_barcode) {
        $barcode = base64_decode($tmp_barcode);

        /*$get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d9", "(SUBSTRING(d1,1,2) = 'PO') AND (d10 = '".$barcode."') AND (d11 = '".$this->user."')");

        if ($get_tmp_data['d9'] > 1) {
            $data = array(
                'd9' => (int)$get_tmp_data['d9'] - 1
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d11 = '".$this->user."') AND (d10 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'PO')");
        }else {
            $this->m_crud->delete_data("tr_temp_d", "(d11 = '".$this->user."') AND (d10 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'PO')");
        }*/

        $this->m_crud->delete_data("tr_temp_d", "(d9 = '".$this->user."') AND (d3 = '".$barcode."') AND (d1 = 'barcode')");

        echo true;
    }

    public function get_barang($tmp_barcode, $tmp_cat_cari, $tmp_lokasi) {
        $cat_cari = base64_decode($tmp_cat_cari);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi));
        $lokasi_barang = $explode_lokasi[0];
        //$supplier = base64_decode($tmp_supplier);

        if ($cat_cari == 1) {
            $col_barang = 'barang.kd_brg';
            $col_tmp = 'd2';
        } else if ($cat_cari == 2) {
            $col_barang = 'barang.barcode';
            $col_tmp = 'd3';
        } else if ($cat_cari == 3) {
            $col_barang = 'barang.Deskripsi';
            $col_tmp = 'd5';
        }

        $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d7", "(d1 = 'barcode') AND (".$col_tmp." = '".$barcode."') AND (d9 = '".$this->user."')");

        if ($get_tmp_data != '') {
            $data = array(
                'd7' => (int)$get_tmp_data['d7'] + 1
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d9 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND (d1 = 'barcode')");
            echo json_encode(array('status' => 1));
        }else {
            $get_barang = $this->m_crud->get_data("barang, barang_hrg", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, Group1, barang.nm_brg, barang.satuan, barang.kel_brg, barang.hrg_beli, barang_hrg.hrg_jual_1", "(barang.kd_brg = barang_hrg.barang) AND (barang_hrg.lokasi = '".$lokasi_barang."') AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
            if ($get_barang != '') {
                $this->insert_tr_temp_d($lokasi_barang, $get_barang, $barcode);
                echo json_encode(array('status' => 1));
            }else {
                $get_barang = $this->m_crud->get_data("barang, barang_hrg", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, Group1, barang.nm_brg, barang.kel_brg, barang.satuan, barang.hrg_beli, barang_hrg.hrg_jual_1, barang_hrg.ppn", "(barang.kd_brg = barang_hrg.barang) AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                if ($get_barang != '') {
                    $this->insert_tr_temp_d($lokasi_barang, $get_barang, $barcode);
                    echo json_encode(array('status' => 1));
                }else {
                    $get_barang = $this->m_crud->get_data("barang, barang_hrg", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, Group1, barang.nm_brg, barang.kel_brg, barang.satuan, barang.hrg_beli, barang_hrg.hrg_jual_1, barang_hrg.ppn", "(barang.kd_brg = barang_hrg.barang) AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                    if ($get_barang != '') {
                        $this->insert_tr_temp_d($lokasi_barang, $get_barang, $barcode);
                        //echo json_encode(array('status' => 2, 'notif' => "Barang dari supplier ".$supplier." tidak tersedia!"));
                    }else {
                        echo json_encode(array('status' => 3, 'notif' => "Barang tidak tersedia!"));
                    }
                }
            }
        }
    }

    public function trans($tmp_nota_po) {
        $nota_po = base64_decode($tmp_nota_po);
        $get_kode = $this->m_crud->get_data("Master_PO", "no_po", "(no_po = '".$nota_po."')");

        if ($get_kode != '') {
            $kode_baru = $this->m_website->generate_kode(substr($get_kode['no_po'], 0, 2), substr($get_kode['no_po'], 14), substr($get_kode['no_po'], 3, 6));
            $this->trans_pembelian(base64_encode($kode_baru));
        }else {
            $this->db->trans_begin();

            $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");;
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')");;
            $sub_total = $this->get_sub_total();
            $explode_lokasi = explode('|', $get_temp_m['m4']);
            $lokasi = $explode_lokasi[0];

            $data_po = array(
                'tgl_po' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'no_po' => $nota_po,
                'kode_supplier' => $get_temp_m['m5'],
                'lokasi' => $lokasi,
                'kd_kasir' => $this->user,
                'status' => 0,
                'catatan' => $get_temp_m['m7'],
                'GT' => $sub_total,
                'tglkirim' => $get_temp_m['m3'] . " " . date("H:i:s"),
            );
            $this->m_crud->create_data("Master_PO", $data_po);

            foreach ($read_temp_d as $row) {
                $data_detail_po = array(
                    'no_po' => $nota_po,
                    'kode_barang' => $row['d2'],
                    'diskon' => $row['d4'],
                    'disc2' => $row['d5'],
                    'disc3' => $row['d6'],
                    'disc4' => $row['d7'],
                    'PPN' => $row['d8'],
                    'harga_beli' => $row['d3'],
                    'jumlah_beli' => $row['d9']
                );
                $this->m_crud->create_data("Detail_PO", $data_detail_po);

                /*$this->m_crud->update_data("barang", array('hrg_beli' => $row['d3']), "(kd_brg = '".$row['d2']."')");

                $get_barang_hrg = $this->m_crud->get_data("barang_hrg", "id_barang_hrg", "(barang = '".$row['d2']."' AND lokasi = '".$lokasi."')");
                if ($get_barang_hrg != '') {
                    $data_update_harga = array(
                        'hrg_jual_1' => $row['d6'],
                        'hrg_jual_2' => $row['d7'],
                        'hrg_jual_3' => $row['d8'],
                        'hrg_jual_4' => $row['d9']
                    );
                    $this->m_crud->update_data("barang_hrg", $data_update_harga, "(id_barang_hrg = '".$get_barang_hrg['id_barang_hrg']."')");
                } else {
                    $data_barang_hrg = array(
                        'barang' => $row['d2'],
                        'hrg_jual_1' => $row['d6'],
                        'hrg_jual_2' => $row['d7'],
                        'hrg_jual_3' => $row['d8'],
                        'hrg_jual_4' => $row['d9'],
                        'lokasi' => $lokasi
                    );
                    $this->m_crud->create_data("barang_hrg", $data_barang_hrg);
                }*/
            }

            $this->delete_trans();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo false;
            }else {
                $this->db->trans_commit();
                echo true;
            }
        }
    }

    public function delete_trans() {
        $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "(m3 = '".$this->user."') AND (m1 = 'barcode')");
        $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "(d9 = '".$this->user."') AND (d1 = 'barcode')");
		
        if ($delete_data_master && $delete_data_detail) {
            echo true;
        }
    }

    public function cetak_packing_barang(){
        $this->access_denied(182);
        $data = $this->data;
        $function = 'cetak_barcode_packing';
        $table = $function;
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Cetak Packing Barang';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, Nama, serial');


        $this->load->view('bo/index', $data);
    }

    public function add_tr_temp_m_packing() {
        $data = array(
            'm1' => 'barcode_packing',
            'm2' => $_POST['lokasi_'],
            'm3' => $this->user
        );

        $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "(m3 = '".$this->user."') AND (m1 = 'barcode_packing')");

        if ($cek_data == 1) {
            $this->m_crud->update_data("tr_temp_d", array("m2" => $_POST['lokasi_']), "(m3 = '".$this->user."') AND (m1 = 'barcode_packing')");
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }
    }

    public function get_tr_temp_m_packing() {
        $get_data = $this->m_crud->get_data("tr_temp_m", "*", "(m3 = '".$this->user."') AND (m1 = 'barcode_packing')");

        echo json_encode($get_data);
    }

    public function get_tr_temp_d_packing() {
        $list_barang = '';
        $read_data = $this->m_crud->read_data("tr_temp_d", "*", "(d9 = '".$this->user."') AND (d1 = 'barcode_packing')", "CONVERT(INTEGER, d10) ASC");

        $no = 1;
        $col = 0;
        foreach ($read_data as $row) {
            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d3'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button></td>
                                <td>' . $row['d12'] . '</td>
                                <td>' . $row['d2'] . '</td>
                                <td>' . $row['d3'] . '</td>
                                <td>' . $row['d4'] . '</td>
                                <td>' . $row['d6'] . '</td>
                                <td>' . $row['d5'] . '</td>
                                <td class="text-right">' . number_format($row['d8']) . '</td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d3'] . '\', \'d7\', $(this).val())" onfocus="this.select()" onkeyup="return to_barcode(event)" type="number" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-diskon" value="' . $row['d7'] . '"></td>
                            </tr>';
            $col = $no;
            $no++;
        }
        $list_barang .= '<input type="hidden" id="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang));
    }

    public function insert_tr_temp_d_packing($lokasi, $get_barang, $barcode) {
        $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d10)) id", "(d1 = 'barcode_packing') AND (d9 = '".$this->user."')");
        $data = array(
            'd1' => 'barcode_packing',
            'd2' => $get_barang['kd_brg'],
            'd3' => $get_barang['barcode'],
            'd4' => $get_barang['nm_brg'],
            'd5' => $get_barang['Group1'],
            'd6' => $get_barang['Deskripsi'],
            'd7' => 1,
            'd8' => $get_barang['hrg_jual_1'],
            'd9' => $this->user,
            'd10' => ((int)$get_max_id['id']+1),
            'd11' => $lokasi,
            'd12' => $get_barang['kd_packing'],
            'd13' => $get_barang['qty_packing']
        );

        $this->m_crud->create_data("tr_temp_d", $data);
    }

    public function update_tr_temp_d_packing($tmp_barcode, $tmp_column, $tmp_value) {
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        $this->m_crud->update_data("tr_temp_d", array($column => $value), "(d1 = 'barcode_packing') AND (d3 = '".$barcode."') AND (d9 = '".$this->user."')");
    }

    public function delete_tr_temp_d_packing($tmp_barcode) {
        $barcode = base64_decode($tmp_barcode);

        /*$get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d9", "(SUBSTRING(d1,1,2) = 'PO') AND (d10 = '".$barcode."') AND (d11 = '".$this->user."')");

        if ($get_tmp_data['d9'] > 1) {
            $data = array(
                'd9' => (int)$get_tmp_data['d9'] - 1
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d11 = '".$this->user."') AND (d10 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'PO')");
        }else {
            $this->m_crud->delete_data("tr_temp_d", "(d11 = '".$this->user."') AND (d10 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'PO')");
        }*/

        $this->m_crud->delete_data("tr_temp_d", "(d9 = '".$this->user."') AND (d3 = '".$barcode."') AND (d1 = 'barcode_packing')");

        echo true;
    }
	
	public function simpan_kode_packing(){
		$this->m_crud->update_data('barang', array('kd_packing'=>$_POST['kode_packing'], 'qty_packing'=>$_POST['qty_packing']), "kd_brg = '".$_POST['kode_barang']."'");
		
		$data = array(
			'd7' => $_POST['qty_packing'],
            'd12' => $_POST['kode_packing'],
            'd13' => $_POST['qty_packing']
        );
		$this->m_crud->update_data("tr_temp_d", $data, "(d1 = 'barcode_packing') AND (d9 = '".$this->user."') and d2 = '".$_POST['kode_barang']."'");
		
		echo true;
	}
	
	public function get_data_barang($kode_barang_){
		$kode_barang = base64_decode($kode_barang_);
		$barang = $this->m_crud->get_data('barang', 'kd_brg, barcode, nm_brg, deskripsi, kd_packing, qty_packing, hrg_jual_1', "kd_brg = '".$kode_barang."'");
		if($barang != null){
			echo json_encode(array('status'=>1, 'barang'=>$barang));
		} else {
			echo json_encode(array('status'=>0));
		}
	}
	
    public function get_barang_packing($tmp_barcode, $tmp_cat_cari, $tmp_lokasi) {
        $cat_cari = base64_decode($tmp_cat_cari);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi));
        $lokasi_barang = $explode_lokasi[0];
        //$supplier = base64_decode($tmp_supplier);

        if ($cat_cari == 1) {
            $col_barang = 'barang.kd_brg';
            $col_tmp = 'd2';
        } else if ($cat_cari == 2) {
            $col_barang = 'barang.barcode';
            $col_tmp = 'd3';
        } else if ($cat_cari == 3) {
            $col_barang = 'barang.Deskripsi';
            $col_tmp = 'd5';
        } else if ($cat_cari == 4) {
            $col_barang = 'barang.kd_packing';
            $col_tmp = 'd12';
        }

        $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d7", "(d1 = 'barcode_packing') AND (".$col_tmp." = '".$barcode."') AND (d9 = '".$this->user."')");

        if ($get_tmp_data != '') {
            $data = array(
                'd7' => (int)$get_tmp_data['d7'] + 1
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d9 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND (d1 = 'barcode_packing')");
            echo json_encode(array('status' => 1));
        }else {
            $get_barang = $this->m_crud->get_data("barang, barang_hrg", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.kd_packing)) kd_packing, isnull(qty_packing, 0) qty_packing, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, Group1, barang.nm_brg, barang.satuan, barang.kel_brg, barang.hrg_beli, barang_hrg.hrg_jual_1", "(barang.kd_brg = barang_hrg.barang) AND (barang_hrg.lokasi = '".$lokasi_barang."') AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
            if ($get_barang != '') {
                $this->insert_tr_temp_d_packing($lokasi_barang, $get_barang, $barcode);
                echo json_encode(array('status' => 1));
            }else {
                $get_barang = $this->m_crud->get_data("barang, barang_hrg", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.kd_packing)) kd_packing, isnull(qty_packing, 0) qty_packing, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, Group1, barang.nm_brg, barang.kel_brg, barang.satuan, barang.hrg_beli, barang_hrg.hrg_jual_1, barang_hrg.ppn", "(barang.kd_brg = barang_hrg.barang) AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                if ($get_barang != '') {
                    $this->insert_tr_temp_d_packing($lokasi_barang, $get_barang, $barcode);
                    echo json_encode(array('status' => 1));
                }else {
                    $get_barang = $this->m_crud->get_data("barang, barang_hrg", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.kd_packing)) kd_packing, isnull(qty_packing, 0) qty_packing, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, Group1, barang.nm_brg, barang.kel_brg, barang.satuan, barang.hrg_beli, barang_hrg.hrg_jual_1, barang_hrg.ppn", "(barang.kd_brg = barang_hrg.barang) AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                    if ($get_barang != '') {
                        $this->insert_tr_temp_d_packing($lokasi_barang, $get_barang, $barcode);
                        //echo json_encode(array('status' => 2, 'notif' => "Barang dari supplier ".$supplier." tidak tersedia!"));
                    }else {
                        echo json_encode(array('status' => 3, 'notif' => "Barang tidak tersedia!"));
                    }
                }
            }
        }
    }

    public function delete_trans_packing() {
        $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "(m3 = '".$this->user."') AND (m1 = 'barcode_packing')");
        $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "(d9 = '".$this->user."') AND (d1 = 'barcode_packing')");

        if ($delete_data_master && $delete_data_detail) {
            echo true;
        }
    }

    public function feedback($action = null, $id = null) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '2048M');
        ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

        $this->access_denied(206);
        $data = $this->data;
        $function = 'feedback';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Feedback Customer';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi'=>$_POST['lokasi']));
        }

        $search = $this->session->search['any']; $date = $this->session->search['field-date']; $lokasi = $this->session->search['lokasi'];
        if (isset($date) && $date != null) {
            $explode_date = explode(' - ', $date);
            $date1 = str_replace('/','-',$explode_date[0]);
            $date2 = str_replace('/','-',$explode_date[1]);
            $tgl_awal = $date1;
            $tgl_akhir = $date2;
        }
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rc.tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        }else{
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rc.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(cs.nama like '%".$search."%' or rc.kd_trx like '%".$search."%')"; }
        if(isset($lokasi)&&$lokasi!=null&&$lokasi!='-'){
            ($where==null)?null:$where.=" and "; $where.="(mt.lokasi = '".$lokasi."')";
        } else {
            ($where==null)?null:$where.=" and "; $where.="(mt.lokasi in (".$this->lokasi_in."))";
        }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_join("res_customer rc", "rc.kd_trx", array("master_trx mt", "customer cs", "lokasi lk"), array("mt.kd_trx=rc.kd_trx", "cs.kd_cust=rc.customer", "lk.kode=mt.lokasi"), $where);
        $config['per_page'] = 20;
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
        $data['report'] = $this->m_crud->select_limit_join('res_customer rc', "rc.kd_trx, rc.tgl, rc.response, rc.comment, cs.nama nama_customer, lk.nama nama_lokasi", array("master_trx mt", "customer cs", "lokasi lk"), array("mt.kd_trx=rc.kd_trx", "cs.kd_cust=rc.customer", "lk.kode=mt.lokasi"), $where, 'rc.tgl desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        $data['rating'] = $this->m_crud->join_data("res_customer rc", "response, count(rc.kd_trx) total", array("master_trx mt", "customer cs", "lokasi lk"), array("mt.kd_trx=rc.kd_trx", "cs.kd_cust=rc.customer", "lk.kode=mt.lokasi"), $where, null, "rc.response");

        if(isset($_POST['to_excel'])){
            $data['report'] = $this->m_crud->join_data('res_customer rc', "rc.kd_trx, rc.tgl, rc.response, rc.comment, cs.nama nama_customer, lk.nama nama_lokasi", array("master_trx mt", "customer cs", "lokasi lk"), array("mt.kd_trx=rc.kd_trx", "cs.kd_cust=rc.customer", "lk.kode=mt.lokasi"), $where, 'rc.tgl desc');
            $baca = $data['report'];
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
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Kode Transaksi', 'B'=>'Tanggal', 'C'=>'Lokasi', 'D'=>'Customer', 'E'=>'Rating', 'F'=>'Judul', 'G'=>'Komentar'
                )
            );

            foreach($baca as $row => $value){
                $explode = explode('|', $value['comment']);
                $body[$row] = array(
                    $value['kd_trx'], substr($value['tgl'], 0, 19), $value['nama_lokasi'], $value['nama_customer'], $value['response']+0, $explode[0], $explode[1]
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']).'.xls', $header, $body);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function contact($action = null, $id = null) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '2048M');
        ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

        $this->access_denied(207);
        $data = $this->data;
        $function = 'contact';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Contact Us';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi'=>$_POST['lokasi']));
        }

        $search = $this->session->search['any']; $date = $this->session->search['field-date']; $lokasi = $this->session->search['lokasi'];
        if (isset($date) && $date != null) {
            $explode_date = explode(' - ', $date);
            $date1 = str_replace('/','-',$explode_date[0]);
            $date2 = str_replace('/','-',$explode_date[1]);
            $tgl_awal = $date1;
            $tgl_akhir = $date2;
        }
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, tanggal, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, tanggal, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(nama like '%".$search."%' or tlp like '%".$search."%' or email like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data("contact", "id_contact", $where);
        $config['per_page'] = 20;
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
        $data['report'] = $this->m_crud->select_limit('contact', "*", $where, 'tanggal desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function report_kasir() {
        $result = array();
	    $lokasi = $_POST['lokasi'];
        $tgl = $_POST['tgl'];

        $where = "left(convert(varchar, st.tanggal, 120), 10)='".$tgl."'";
        $list = '';

        if ($lokasi != 'all') {
            $where .= " AND st.lokasi='".$lokasi."'";
        } else {
            $where .= " AND st.lokasi in (".$this->lokasi_in.")";
        }

        $get_report = $this->m_crud->join_data("setoran st", "st.*, ud.nama nama_kasir, lk.nama nama_lokasi", array("user_detail ud", "lokasi lk"), array("ud.user_id=st.kd_kasir", "st.lokasi=lk.kode"), $where);

        if ($get_report != null) {
            $result['status'] = true;
            foreach ($get_report as $item) {
                $get_omset = $this->m_crud->get_data("tr_sukses", "sum(st) st, sum(bayar-change) tunai, sum(dis_rp) disc_tr, sum(jml_kartu) card, sum(nominal_poin) poin, sum(nominal_deposit) deposit, sum(tax) tax, sum(serv) serv, sum(compliment_rp) compliment, sum(disc) disc", "left(convert(varchar, tgl, 120), 10) = '" . $tgl . "' AND kd_kasir='" . $item['Kd_Kasir'] . "' AND lokasi='" . $item['Lokasi'] . "'", null, "kd_kasir");
                $get_retur = $this->m_crud->get_join_data("Master_trx mt", "SUM(dt.qty * dt.hrg_jual) total, sum(dt.dis_persen) disc, sum(dt.tax) tax, sum(dt.service) service", "Det_trx dt", "dt.kd_trx=mt.kd_trx", "left(convert(varchar, mt.tgl, 120), 10) = '" . $tgl . "' AND mt.lokasi='" . $item['Lokasi'] . "' AND mt.HR='S' AND dt.qty<0", null, "kd_kasir");
                $get_kas_masuk = $this->m_crud->get_data("kas_masuk", "sum(jumlah) jumlah", "kd_kasir='" . $item['Kd_Kasir'] . "' AND left(convert(varchar, tgl, 120), 10)='" . $tgl . "'");
                $get_kas_keluar = $this->m_crud->get_data("kas_keluar", "sum(jumlah) jumlah", "kd_kasir='" . $item['Kd_Kasir'] . "' AND left(convert(varchar, tgl, 120), 10)='" . $tgl . "'");

                $total = (float)$get_omset['st'] - (float)$get_retur['total'] - (float)$get_omset['disc_tr'] - (float)$get_omset['disc'] + (float)$get_omset['tax'] + (float)$get_omset['serv'] + (float)$get_kas_masuk['jumlah'] - (float)$get_kas_keluar['jumlah'];

                $tunai = (float)$get_omset['tunai'];

                $net = $get_omset['st']-$get_omset['disc']-$get_omset['disc_tr'];
                $gs = $net+$get_omset['tax']+$get_omset['serv'];

                $uang = $tunai + (float)$get_omset['compliment'] + (float)$get_omset['card'] + (float)$get_omset['deposit'];

                $income = $get_kas_masuk['jumlah'];
                $outcome = $get_kas_keluar['jumlah']+$get_retur['total']+$get_retur['tax']+$get_retur['service']-$get_retur['disc'];

                $cashier_cash = $item['Setoran_tunai'];
                $cash_sales = $tunai+$income-abs($outcome);

                if ($cashier_cash == $cash_sales) {
                    $pesan = 'Balance';
                } else if ($cashier_cash > $cash_sales) {
                    $pesan = 'Surplus ('.number_format($cashier_cash-$cash_sales).')';
                } else if ($cashier_cash < $cash_sales) {
                    $pesan = 'Deficit ('.number_format($cashier_cash-$cash_sales).')';
                }
//                <button onclick="re_closing(\''.$item['ID'].'\', \''.date('Y-m-d', strtotime($item['Tanggal'])).'\')" class="btn btn-info btn-sm">Re Closing</button>

                $list .= /** @lang text */'
                <div class="col-lg-4">
                    <div class="panel panel-border panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title"> '.$item['nama_kasir'].' - '.$item['nama_lokasi'].' </h3>
                            <button style="float: right" onclick="re_closing(\''.$item['ID'].'\', \''.date('Y-m-d', strtotime($item['Tanggal'])).'\')" class="btn btn-info btn-sm">Re Closing</button>
                        </div>
                        <div class="panel-body">
                            
                            <table width="100%" border="0">
                                <tr>
                                    <td>Total Sales</td>
                                    <th class="pull-right"> '.number_format($get_omset['st']).' </th>
                                </tr>
                                <tr>
                                    <td>Discount Item</td>
                                    <th class="pull-right"> '.number_format($get_omset['disc']).' </th>
                                </tr>
                                <tr>
                                    <td>Discount Total</td>
                                    <th class="pull-right"> '.number_format($get_omset['disc_tr']).' </th>
                                </tr>
                                <tr>
                                    <td>Net Omset</td>
                                    <th class="pull-right"> '.number_format($net).' </th>
                                </tr>
                                <tr>
                                    <td>Tax</td>
                                    <th class="pull-right"> '.number_format($get_omset['tax']).' </th>
                                </tr>
                                <tr>
                                    <td>Service</td>
                                    <th class="pull-right"> '.number_format($get_omset['serv']).' </th>
                                </tr>
                                <tr>
                                    <td>Total Omset</td>
                                    <th class="pull-right"> '.number_format($gs).' </th>
                                </tr>
                                <tr>
                                    <td>Cash</td>
                                    <th class="pull-right"> '.number_format($tunai).' </th>
                                </tr>
                                <tr>
                                    <td>Edc Seatle</td>
                                    <th class="pull-right"> '.number_format($item['setoran_card']).' </th>
                                </tr>
                                <tr>
                                    <td>Compliment</td>
                                    <th class="pull-right"> '.number_format($item['setoran_compliment']).' </th>
                                </tr>
                                <tr>
                                    <td>Poin</td>
                                    <th class="pull-right"> '.number_format($get_omset['setoran_poin']).' </th>
                                </tr>
                                <tr>
                                    <td style="border-top: solid; border-width: thin">Receive Amount</td>
                                    <th style="border-top: solid; border-width: thin; text-align: right">'.number_format($get_kas_masuk['jumlah']).'</th>
                                </tr>
                                <tr>
                                    <td>Other Income</td>
                                    <th class="pull-right">0</th>
                                </tr>
                                <tr>
                                    <td>Total Income</td>
                                    <th class="pull-right">'.number_format($income).'</th>
                                </tr>
                                <tr>
                                    <td style="border-top: solid; border-width: thin">Cash In Hand</td>
                                    <th style="border-top: solid; border-width: thin; text-align: right">'.number_format($tunai+$income).'</th>
                                </tr>
                                <tr>
                                    <td style="border-top: solid; border-width: thin">Return</td>
                                    <th style="border-top: solid; border-width: thin; text-align: right">'.number_format($get_retur['total']).'</th>
                                </tr>
                                <tr>
                                    <td>Tax</td>
                                    <th style="text-align: right">'.number_format($get_retur['tax']).'</th>
                                </tr>
                                <tr>
                                    <td>Service</td>
                                    <th style="text-align: right">'.number_format($get_retur['service']).'</th>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <th style="text-align: right">'.number_format(abs($get_retur['disc'])).'</th>
                                </tr>
                                <tr>
                                    <td>Paid Out</td>
                                    <th class="pull-right">'.number_format($get_kas_keluar['jumlah']).'</th>
                                </tr>
                                <tr>
                                    <td>Total Outcome</td>
                                    <th class="pull-right">'.number_format($outcome).'</th>
                                </tr>
                                <tr>
                                    <td style="border-top: solid; border-width: thin">Total Cash Sales</td>
                                    <th style="border-top: solid; border-width: thin; text-align: right">'.number_format($cash_sales).'</th>
                                </tr>
                                <tr>
                                    <td style="border-top: solid; border-width: thin">Cashier Cash</td>
                                    <th style="border-top: solid; border-width: thin; text-align: right">'.number_format($cashier_cash).'</th>
                                </tr>
                                <tr>
                                    <td style="border-top: solid; border-width: thin">Status</td>
                                    <th style="border-top: solid; border-width: thin; text-align: right">'.$pesan.'</th>
                                </tr>
                                <tr>
                                    <td style="border-top: solid; border-width: thin">Note</td>
                                    <th style="border-top: solid; border-width: thin; text-align: right">'.$item['Keterangan'].'</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>';
            }

            $result['list'] = $list;
        } else {
            $result['status'] = false;
        }

        echo json_encode($result);
    }

//    public function report_kasir() {
//        $result = array();
//        $lokasi = $_POST['lokasi'];
//        $tgl = $_POST['tgl'];
//
//        $where = "left(convert(varchar, st.tanggal, 120), 10)='".$tgl."'";
//        $list = '';
//
//        if ($lokasi != 'all') {
//            $where .= " AND st.lokasi='".$lokasi."'";
//        } else {
//            $where .= " AND st.lokasi in (".$this->lokasi_in.")";
//        }
//
//        $get_report = $this->m_crud->join_data("setoran st", "st.*, ud.nama nama_kasir, lk.nama nama_lokasi", array("user_detail ud", "lokasi lk"), array("ud.user_id=st.kd_kasir", "st.lokasi=lk.kode"), $where);
//        if ($get_report != null) {
//            $result['status'] = true;
//            foreach ($get_report as $item) {
//                $get_omset = $this->m_crud->get_data("tr_sukses", "sum(st) st, sum(bayar-change) tunai, sum(dis_rp) disc_tr, sum(jml_kartu) card, sum(nominal_poin) poin, sum(nominal_deposit) deposit, sum(tax) tax, sum(serv) serv, sum(compliment_rp) compliment, sum(disc) disc", "left(convert(varchar, tgl, 120), 10) = '" . $tgl . "' AND kd_kasir='" . $item['Kd_Kasir'] . "' AND lokasi='" . $item['Lokasi'] . "'", null, "kd_kasir");
//                $get_retur = $this->m_crud->get_join_data("Master_trx mt", "SUM(dt.qty * dt.hrg_jual) total, sum(dt.dis_persen) disc, sum(dt.tax) tax, sum(dt.service) service", "Det_trx dt", "dt.kd_trx=mt.kd_trx", "left(convert(varchar, mt.tgl, 120), 10) = '" . $tgl . "' AND mt.lokasi='" . $item['Lokasi'] . "' and mt.kd_kasir='".$item['Kd_Kasir']."' AND mt.HR='S' AND dt.qty<0", null, "kd_kasir");
//                $get_kas_masuk = $this->m_crud->get_data("kas_masuk", "sum(jumlah) jumlah", "kd_kasir='" . $item['Kd_Kasir'] . "' AND left(convert(varchar, tgl, 120), 10)='" . $tgl . "'");
//                $get_kas_keluar = $this->m_crud->get_data("kas_keluar", "sum(jumlah) jumlah", "kd_kasir='" . $item['Kd_Kasir'] . "' AND left(convert(varchar, tgl, 120), 10)='" . $tgl . "'");
//
//                $total = (float)$get_omset['st'] - (float)$get_retur['total'] - (float)$get_omset['disc_tr'] - (float)$get_omset['disc'] + (float)$get_omset['tax'] + (float)$get_omset['serv'] + (float)$get_kas_masuk['jumlah'] - (float)$get_kas_keluar['jumlah'];
//
//                $tunai = (float)$get_omset['tunai'];
//
//                $net = $get_omset['st']-$get_omset['disc']-$get_omset['disc_tr'];
//                $gs = $net+$get_omset['tax']+$get_omset['serv'];
//
//                $uang = $tunai + (float)$get_omset['compliment'] + (float)$get_omset['card'] + (float)$get_omset['deposit'];
//
//                $income = $get_kas_masuk['jumlah'];
//                $outcome = $get_kas_keluar['jumlah']+$get_retur['total']+$get_retur['tax']+$get_retur['service']-$get_retur['disc'];
//
//                $cashier_cash = $item['Setoran_tunai'];
//                $cash_sales = $tunai+$income-abs($outcome);
//
//                if ($cashier_cash == $cash_sales) {
//                    $pesan = 'Balance';
//                } else if ($cashier_cash > $cash_sales) {
//                    $pesan = 'Surplus ('.number_format($cashier_cash-$cash_sales).')';
//                } else if ($cashier_cash < $cash_sales) {
//                    $pesan = 'Deficit ('.number_format($cashier_cash-$cash_sales).')';
//                }
//
//                $list .= /** @lang text */
//                    '
//                <div class="col-lg-4">
//                    <div class="panel panel-border panel-info">
//                        <div class="panel-heading">
//                            <h3 class="panel-title"> '.$item['nama_kasir'].' - '.$item['nama_lokasi'].' ('.$item['Kassa'].') </h3>
//                            <span>'.date('Y-m-d H:i:s', strtotime($item['Tanggal'])).'</span>
//                            <button style="float: right" onclick="print_closing(\''.$item['ID'].'\',\''.$item['Kd_Kasir'].'\', \''.$tgl.'\',\''.$item['Lokasi'].'\')"  class="btn btn-info btn-sm">Re Closing</button>
//
//                            <hr>
//                        </div>
//                        <div class="panel-body">
//                            <table width="100%" border="0">
//                                <tr>
//                                    <td>Total Sales</td>
//                                    <th class="pull-right"> '.number_format($get_omset['st']).' </th>
//                                </tr>
//                                <tr>
//                                    <td>Discount Item</td>
//                                    <th class="pull-right"> '.number_format($get_omset['disc']).' </th>
//                                </tr>
//                                <tr>
//                                    <td>Discount Total</td>
//                                    <th class="pull-right"> '.number_format($get_omset['disc_tr']).' </th>
//                                </tr>
//                                <tr>
//                                    <td>Net Omset</td>
//                                    <th class="pull-right"> '.number_format($net).' </th>
//                                </tr>
//                                <tr>
//                                    <td>Tax</td>
//                                    <th class="pull-right"> '.number_format($get_omset['tax']).' </th>
//                                </tr>
//                                <tr>
//                                    <td>Service</td>
//                                    <th class="pull-right"> '.number_format($get_omset['serv']).' </th>
//                                </tr>
//                                <tr>
//                                    <td>Total Omset</td>
//                                    <th class="pull-right"> '.number_format($gs).' </th>
//                                </tr>
//                                <tr>
//                                    <td>Cash</td>
//                                    <th class="pull-right"> '.number_format($tunai).' </th>
//                                </tr>
//                                <tr>
//                                    <td>Edc Seatle</td>
//                                    <th class="pull-right"> '.number_format($get_omset['card']).' </th>
//                                </tr>
//                                <tr>
//                                    <td>Compliment</td>
//                                    <th class="pull-right"> '.number_format($item['setoran_compliment']).' </th>
//                                </tr>
//                                <tr>
//                                    <td>Poin</td>
//                                    <th class="pull-right"> '.number_format($get_omset['setoran_poin']).' </th>
//                                </tr>
//                                <tr>
//                                    <td style="border-top: solid; border-width: thin">Cash In</td>
//                                    <th style="border-top: solid; border-width: thin; text-align: right">'.number_format($get_kas_masuk['jumlah']).'</th>
//                                </tr>
//                                <tr>
//                                    <td>Total Income</td>
//                                    <th class="pull-right">'.number_format($income).'</th>
//                                </tr>
//                                <tr>
//                                    <td style="border-top: solid; border-width: thin">Cash In Hand</td>
//                                    <th style="border-top: solid; border-width: thin; text-align: right">'.number_format($tunai+$income).'</th>
//                                </tr>
//                                <tr>
//                                    <td style="border-top: solid; border-width: thin">Return</td>
//                                    <th style="border-top: solid; border-width: thin; text-align: right">'.number_format($get_retur['total']).'</th>
//                                </tr>
//                                <tr>
//                                    <td>Tax</td>
//                                    <th style="text-align: right">'.number_format($get_retur['tax']).'</th>
//                                </tr>
//                                <tr>
//                                    <td>Service</td>
//                                    <th style="text-align: right">'.number_format($get_retur['service']).'</th>
//                                </tr>
//                                <tr>
//                                    <td>Discount</td>
//                                    <th style="text-align: right">'.number_format(abs($get_retur['disc'])).'</th>
//                                </tr>
//                                <tr>
//                                    <td>Cash Out</td>
//                                    <th class="pull-right">'.number_format($get_kas_keluar['jumlah']).'</th>
//                                </tr>
//                                <tr>
//                                    <td>Total Outcome</td>
//                                    <th class="pull-right">'.number_format($outcome).'</th>
//                                </tr>
//                                <tr>
//                                    <td style="border-top: solid; border-width: thin">Total Cash Sales</td>
//                                    <th style="border-top: solid; border-width: thin; text-align: right">'.number_format($cash_sales).'</th>
//                                </tr>
//                                <tr>
//                                    <td style="border-top: solid; border-width: thin">Cashier Cash</td>
//                                    <th style="border-top: solid; border-width: thin; text-align: right">'.number_format($cashier_cash).'</th>
//                                </tr>
//                                <tr>
//                                    <td style="border-top: solid; border-width: thin">Status</td>
//                                    <th style="border-top: solid; border-width: thin; text-align: right">'.$pesan.'</th>
//                                </tr>
//                                <tr>
//                                    <td style="border-top: solid; border-width: thin">Note</td>
//                                    <th style="border-top: solid; border-width: thin; text-align: right">'.$item['Keterangan'].'</th>
//                                </tr>
//                            </table>
//                        </div>
//                    </div>
//                </div>';
//            }
//
//            $result['list'] = $list;
//        } else {
//            $result['status'] = false;
//        }
//
//        echo json_encode($result);
//    }

    public function re_closing() {
        $id = $_POST['id'];
        $tgl = $_POST['tgl'];

        $where = "id='".$id."' and left(convert(varchar, tanggal, 120), 10)='".$tgl."'";

        $get_lokasi = $this->m_crud->get_data("setoran", "lokasi", $where);

        $this->m_crud->delete_data("setoran", $where);

        $log = array(
            'type' => 'D',
            'table' => "setoran",
            'data' => "",
            'condition' => $where
        );

        $data_log = array(
            'lokasi' => $get_lokasi['lokasi'],
            'hostname' => '-',
            'db_name' => '-',
            'query' => json_encode($log)
        );
        $this->m_website->insert_log_api($data_log);
        echo json_encode(array('res'=>''));
    }
}

