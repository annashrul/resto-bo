<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pembelian extends CI_Controller {

	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
		$site_data = $this->m_website->site_data();
		$this->site = str_replace(' ', '', strtolower($site_data->title));
		$this->control = 'Pembelian';

        $this->user = $this->session->userdata($this->site . 'user');
        $this->username = $this->session->userdata($this->site . 'username');
        $this->menu_group = $this->m_crud->get_data('Setting', 'as_deskripsi, as_group1, as_group2, status_barang', "Kode = '1111'");

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
		
		ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');

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
	
	/*Start modul kontra bon*/
	public function kontra_bon() {
        $this->access_denied(242);
        $data = $this->data;
        $function = 'kontra_bon';
        $view = $this->control . '/';
		
        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Kontra Bon';
        $data['page'] = $function;
        $data['content'] = $view.$function;
		
		$data['data_lokasi'] = $this->m_crud->read_data('lokasi', 'Kode, Nama');
		$data['data_supplier'] = $this->m_crud->read_data('supplier', 'kode, Nama');
		
        $this->load->view('bo/index', $data);
    }

    public function get_tr_temp_m_kb() {
        $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m1 = 'KB' and m2 = '".$this->user."'");

        if ($get_data != null) {
            echo json_encode(array('status' => 1, 'temp' => $get_data));
        } else {
            $this->m_crud->create_data("tr_temp_m", array('m1'=>'KB', 'm2'=>$this->user, 'm3'=>date('Y-m-d')));
			echo json_encode(array('status' => 0));
        }
    }
	
	public function update_tr_temp_m_kb($tmp_trx, $tmp_col, $tmp_val) {
	    $trx = base64_decode($tmp_trx);
	    $col = base64_decode($tmp_col);
	    $val = base64_decode($tmp_val);
		
		$this->m_crud->update_data("tr_temp_m", array($col => $val), "m1='" . $trx . "' AND m2 = '" . $this->user . "'");
    }
	
	public function nilai_retur_kb($tmp_supplier=null, $tmp_tgl=null, $tmp_periode=null) {
		$supplier = base64_decode($tmp_supplier);
		$tgl = base64_decode($tmp_tgl);
		$periode = base64_decode($tmp_periode);
		$where = null;
		if(isset($supplier) && $supplier != null) { ($where == null) ? null : $where .= " and "; $where .= "vrb.Supplier = '".$supplier."'"; }
		if(isset($tgl) && $tgl != null) { ($where == null) ? null : $where .= " and "; $where .= "vrb.Tgl <= '".$tgl." 23:59:59'"; }
		if(isset($periode) && $periode != null) { 
			$date = $periode;
			$explode_date = explode(' - ', $date);
			$date1 = str_replace('/','-',$explode_date[0]);
			$date2 = str_replace('/','-',$explode_date[1]);
			($where == null) ? null : $where .= " and "; $where .= "Tgl >= '".$date1." 00:00:00' and vrb.Tgl <= '".$date2." 23:59:59'"; 
		}
        //$read_data = $this->m_crud->get_data("v_retur_beli", "sum(mjl*hrg_beli) nilai_retur, isnull((),0) nilai_kontra", $where);
        $read_data = $this->m_crud->read_data("v_retur_beli vrb", "(isnull((sum(jml*hrg_beli)),0)-isnull((select sum(retur) from master_kontra mk where mk.supplier=vrb.Supplier and tgl_kontra <= '".$tgl." 23:59:59'),0)) nilai_retur", $where, null, "Supplier");
		echo json_encode(array('retur'=>((isset($read_data[0]))?($read_data[0]):(0))));
	}
	
	public function read_nota_beli_kb($tmp_supplier=null, $tmp_tgl_bayar=null, $tmp_nota=null, $tmp_jenis=null) {
		$supplier = base64_decode($tmp_supplier);
		$tgl_bayar = base64_decode($tmp_tgl_bayar);
		$nota = base64_decode($tmp_nota);
		$jenis = base64_decode($tmp_jenis);
        $list_nota_beli = '';
		
		if($jenis == 'Konsinyasi'){
			$periode = $nota;
			$where = null;
			if(isset($supplier) && $supplier != null) { ($where == null) ? null : $where .= " and "; $where .= "br.Group1 = '".$supplier."'"; }
			if(isset($tgl) && $tgl != null) { ($where == null) ? null : $where .= " and "; $where .= "Tgl <= '".$tgl." 23:59:59'"; }
			if(isset($periode) && $periode != null) { 
				$date = $periode;
				$explode_date = explode(' - ', $date);
				$date1 = str_replace('/','-',$explode_date[0]);
				$date2 = str_replace('/','-',$explode_date[1]);
				($where == null) ? null : $where .= " and "; $where .= "mt.tgl >= '".$date1." 00:00:00' and mt.tgl <= '".$date2." 23:59:59'"; 
			}
			
			$query_nilai_kontra = "isnull((select sum(nilai_kontra) from det_kontra where master_beli = '".$nota."'),0)";
			$read_data = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br", 
				"'".$nota."' no_faktur_beli, '-' noNota, '-' tgl_beli, '".$tgl_bayar."' tgl_jatuh_tempo, SUM(dt.qty*br.hrg_beli) nilai_pembelian, (".$query_nilai_kontra.") nilai_kontra", 
				"mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg".($where==null?'':' AND '.$where), 
				null, "br.Group1", null, null,
				"(".$query_nilai_kontra.") < (SUM(dt.qty*br.hrg_beli))"
													   
			);
		} else {
			$where = "type='Kredit' and no_faktur_beli not in (select fak_beli from bayar_hutang)";
			if(isset($supplier) && $supplier != null) { ($where == null) ? null : $where .= " and "; $where .= "kode_supplier = '".$supplier."'"; }
			if(isset($tgl_bayar) && $tgl_bayar != null) { ($where == null) ? null : $where .= " and "; $where .= "tgl_jatuh_tempo <= '".$tgl_bayar." 23:59:59'"; }
			if(isset($nota) && $nota != null) { ($where == null) ? null : $where .= " and "; $where .= "no_faktur_beli = '".$nota."'"; }
			
			$query_nilai_kontra = "isnull((select sum(nilai_kontra) from det_kontra where master_beli = no_faktur_beli),0)";
			$read_data = $this->m_crud->read_data("pembelian_report", "no_faktur_beli, noNota, tgl_beli, tgl_jatuh_tempo, (total_beli + ppn - disc) nilai_pembelian, (".$query_nilai_kontra.") nilai_kontra", $where, 
				'tgl_beli asc', 'no_faktur_beli, noNota, tgl_beli, tgl_jatuh_tempo, total_beli, ppn, disc', null, null, 
				"(".$query_nilai_kontra.") < (total_beli + ppn - disc)"
			);
		}
		$no=0; $total=0;
        foreach ($read_data as $row){ $no++;
			$list_nota_beli .= '
				<input type="hidden" id="nota_beli'.$no.'" name="nota_beli'.$no.'" value="'.$row['no_faktur_beli'].'" />
				<input type="hidden" id="nota_supplier'.$no.'" name="nota_supplier'.$no.'" value="'.$row['noNota'].'" />
				<input type="hidden" id="tgl_beli'.$no.'" name="tgl_beli'.$no.'" value="'.$row['tgl_beli'].'" />
				<input type="hidden" id="tgl_tempo'.$no.'" name="tgl_tempo'.$no.'" value="'.$row['tgl_jatuh_tempo'].'" />
				<input type="hidden" id="nilai_beli'.$no.'" name="nilai_beli'.$no.'" value="'.$row['nilai_pembelian'].'" />
				<input type="hidden" id="nilai_kontra'.$no.'" name="nilai_kontra'.$no.'" value="'.$row['nilai_kontra'].'" />
				<tr>
					<td>'.$no.'</td>
					<td style="text-align:center;">
						<div class="checkbox checkbox-primary">
							<input class="form-control checklist_nota" type="checkbox" id="checklist_nota'.$no.'" name="checklist_nota'.$no.'" />
							<label for="checklist_nota'.$no.'"></label>
						</div>
					</td>
					<td>'.$row['no_faktur_beli'].'</td>
					<td>'.$row['noNota'].'</td>
					<td>'.substr($row['tgl_beli'],0,10).'</td>
					<td>'.substr($row['tgl_jatuh_tempo'],0,10).'</td>
					<td style="text-align:right;">'.number_format($row['nilai_pembelian'],2).'</td>
				</tr>
			';
			$total = $total + $row['nilai_pembelian'];
        }
		$list_nota_beli .= '<input type="hidden" id="jumlah_nota_beli" name="jumlah_nota_beli" value="'.$no.'" />';
		$list_nota_beli .= '<tr>
							<td colspan="6" style="text-align:center;"><b>Total</b></td>
							<td style="text-align:right;"><b>'.number_format($total,2).'</b></td>
						</tr>';
			
        echo json_encode(array('status'=>count($read_data), 'list_nota_beli'=>$list_nota_beli));
    }
	
	public function add_list_kontra(){
		$this->db->trans_begin();
		
		for($i=1; $i<=$_POST['jumlah_nota_beli']; $i++){
			if(isset($_POST['checklist_nota'.$i])){
				$this->m_crud->delete_data('tr_temp_d', "d1='KB' and d2='".$this->user."' and d3='".$_POST['nota_beli'.$i]."'");
				$this->m_crud->create_data('tr_temp_d', array(
					'd1'=>'KB', 
					'd2'=>$this->user, 
					'd3'=>$_POST['nota_beli'.$i],
					'd4'=>$_POST['tgl_beli'.$i],
					'd5'=>$_POST['tgl_tempo'.$i],
					'd6'=>$_POST['nilai_beli'.$i],
					'd7'=>$_POST['nilai_kontra'.$i],
					'd8'=>$_POST['nilai_beli'.$i]-$_POST['nilai_kontra'.$i],
					'd9'=>$_POST['nota_supplier'.$i]
				));
			}
		}
		
		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        }else {
            $this->db->trans_commit();
            echo true;
        }
	}
	
	public function get_tr_temp_d_kb() {
        $list_nota_beli_kontra = '';
        $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d1 = 'KB' and d2 = '".$this->user."'");
		$no = 0; $total = 0;
        foreach ($read_data as $row){ $no++;
			$total = $total + str_replace(',','',($row['d8']!=null?$row['d8']:0));
			$list_nota_beli_kontra .= '
				<input type="hidden" id="master_beli'.$no.'" name="master_beli'.$no.'" value="'.$row['d3'].'" />
				<input type="hidden" id="nilai_pembelian'.$no.'" name="nilai_pembelian'.$no.'" value="'.$row['d6'].'" />
				<input type="hidden" id="sudah_kontrabon'.$no.'" name="sudah_kontrabon'.$no.'" value="'.$row['d7'].'" />
				<tr>
					<td>'.$no.'</td>
					<td><button type="button" onclick="hapus_list_kontra(\'' . $row['d3'] . '\')" class="btn btn-primary btn-sm"><i class="md md-close"></i></button></td>
					<td>'.$row['d3'].'</td>
					<td>'.$row['d9'].'</td>
					<td>'.substr($row['d4'],0,10).'</td>
					<td>'.substr($row['d5'],0,10).'</td>
					<td style="text-align:right;">'.number_format($row['d6'],2).'</td>
					<td style="text-align:right;">'.number_format($row['d7'],2).'</td>
					<td>
						<input class="form-control" type="text" onkeydown="return isNumber(event);" onkeyup="isMoney(\'nilai_kontrabon'.$no.'\',\'+\'); hitung_kontra(); validasi(\'nilai_kontrabon'.$no.'\');" onblur="update_tmp_detail(\''.$row['d3'].'\',\'d8\',$(this).val())" id="nilai_kontrabon'.$no.'" name="nilai_kontrabon'.$no.'" value="'.($row['d8']!=null?number_format(str_replace(',','',$row['d8']),2):null).'"/>
						<b class="error" id="alr_nilai_kontrabon'.$no.'"></b>
					</td>
				</tr>
			';
        }
		$list_nota_beli_kontra .= '<input type="hidden" id="jumlah_nota_beli_kontra" name="jumlah_nota_beli_kontra" value="'.$no.'" />';
			
        echo json_encode(array('status'=>count($read_data), 'list_nota_beli_kontra'=>$list_nota_beli_kontra, 'total'=>$total));
    }
	
	public function delete_tr_temp_d_kb($tmp_id) {
        $id = base64_decode($tmp_id);
		if($id=='all'){
			$this->m_crud->delete_data("tr_temp_d", "d1 = 'KB' and d2 = '".$this->user."'");
		} else { 
			$this->m_crud->delete_data("tr_temp_d", "d1 = 'KB' and d2 = '".$this->user."' and d3 = '".$id."'");
		}
		echo true;
    }
	
	public function update_tr_temp_d_kb($tmp_id, $tmp_column, $tmp_value) {
        $id = base64_decode($tmp_id);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);
		
		$this->m_crud->update_data("tr_temp_d", array($column => $value), "d1 = 'KB' and d2 = '".$this->user."' and d3 = '".$id."'");
		
    }
	
	public function delete_trx_kb($delete_image=true) {
        $this->m_crud->delete_data("tr_temp_m", "m1 = 'KB' and m2 = '".$this->user."'");
		/*if($delete_image==true){
			$data_image = $this->m_crud->read_data('tr_temp_d', 'd6', "d1 = 'PO' and d2 = '".$this->user."'");
			foreach($data_image as $image){ if($image['d6']!=null){ unlink($image['d6']); } }
		}*/
		$this->m_crud->delete_data("tr_temp_d", "d1 = 'KB' and d2 = '".$this->user."'");
    }
	
	
    public function simpan_kb() {
        $this->access_denied(242);
        $param = 'Add';
        
		$trx_no = $this->m_website->generate_kode('KB', $_POST['lokasi'], date("ymd", strtotime($_POST['tgl'])));
        
        $this->db->trans_begin();
		
        $data_master = array(
            'id_master_kontra' => $trx_no,
            'tgl_kontra' => $_POST['tgl'] . " " . date("H:i:s"),
            'jenis' => $_POST['jenis'],
            'lokasi' => $_POST['lokasi'],
			'supplier' => $_POST['supplier'],
			'tgl_bayar' => $_POST['tgl_bayar'],
			'retur' => $_POST['retur'],
			'biaya_adm' => $_POST['adm'],
			'pembayaran' => $_POST['pembayaran'],
			'pembulatan' => $_POST['pembulatan'],
			'ket' => str_replace("'","`",$_POST['descrip']),
            'user_akun' => $this->user,
            'status' => '0',
        );
        $this->m_crud->create_data("master_kontra", $data_master);

        $det_log = array();
		for ($i=1; $i<=$_POST['jumlah_nota_beli_kontra']; $i++) {
			$data_detail_mutasi = array(
				'master_kontra' => $trx_no,
				'master_beli' => $_POST['master_beli'.$i],
				'nilai_kontra' => $_POST['nilai_kontrabon'.$i],
				'status' => '0'
			);
			$this->m_crud->create_data("det_kontra", $data_detail_mutasi);
			array_push($det_log, $data_detail_mutasi);
		}

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$trx_no,'jenis'=>ucfirst($param),'transaksi'=>'Kontra Bon'), array('master'=>$data_master,'detail'=>$det_log));

        $this->delete_trx_kb(false);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status'=>false));
        }else {
            $this->db->trans_commit();
            echo json_encode(array('status'=>true, 'kode'=>$trx_no));
        }
    }
	/*End modul kontra bon*/
	
	/*Start modul bayar kontra bon*/
	public function bayar_kontra_bon() {
        $this->access_denied(243);
        $data = $this->data;
        $function = 'bayar_kontra_bon';
        $view = $this->control . '/';
		
        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Bayar Kontra Bon';
        $data['page'] = $function;
        $data['content'] = $view.$function;
		
		$data['data_bank'] = $this->m_crud->read_data('bank', 'Nama');
		$data['data_lokasi'] = $this->m_crud->read_data('lokasi', 'Kode, Nama');
		$data['data_supplier'] = $this->m_crud->read_data('supplier', 'kode, Nama');
		
        $this->load->view('bo/index', $data);
    }

    public function get_tr_temp_m_bkb() {
        $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m1 = 'BK' and m2 = '".$this->user."'");

        if ($get_data != null) {
            echo json_encode(array('status' => 1, 'temp' => $get_data));
        } else {
            $this->m_crud->create_data("tr_temp_m", array('m1'=>'BK', 'm2'=>$this->user, 'm3'=>date('Y-m-d')));
			echo json_encode(array('status' => 0));
        }
    }
	
	public function update_tr_temp_m_bkb($tmp_trx, $tmp_col, $tmp_val) {
	    $trx = base64_decode($tmp_trx);
	    $col = base64_decode($tmp_col);
	    $val = base64_decode($tmp_val);
		
		$this->m_crud->update_data("tr_temp_m", array($col => $val), "m1='" . $trx . "' AND m2 = '" . $this->user . "'");
    }
	
	public function get_rekening($tmp_supplier){
		$supplier = base64_decode($tmp_supplier);
		$read_data = $this->m_crud->read_data('supplier_rek', '*', "supplier = '".$supplier."'");
		$list_rekening = '';
		$list_rekening .= '<option value="">Pilih</option>';
		foreach($read_data as $row){
			$list_rekening .= '<option value="'.$row['rekening'].'">'.$row['rekening'].'</option>';
		}
		echo json_encode(array('status'=>count($read_data), 'list_rekening'=>$list_rekening));
	}
	
	public function pilih_rekening($tmp_rek){
		$rek = base64_decode($tmp_rek);
		$get_data = $this->m_crud->get_data('supplier_rek', '*', "rekening = '".$rek."'");
		echo json_encode(array('status'=>1,'data_rekening'=>$get_data));
	}
	
	public function read_nota_kontra_bkb($tmp_supplier=null, $tmp_tgl_bayar=null, $tmp_nota=null) {
		$supplier = base64_decode($tmp_supplier);
		$tgl_bayar = base64_decode($tmp_tgl_bayar);
		$nota = base64_decode($tmp_nota);
        $list_nota_kontra = '';
		$where = null;
		if(isset($supplier) && $supplier != null) { ($where == null) ? null : $where .= " and "; $where .= "supplier = '".$supplier."'"; }
		//if(isset($tgl_bayar) && $tgl_bayar != null) { ($where == null) ? null : $where .= " and "; $where .= "tgl_bayar <= '".$tgl_bayar." 23:59:59'"; }
		if(isset($nota) && $nota != null) { ($where == null) ? null : $where .= " and "; $where .= "id_master_kontra = '".$nota."'"; }
		$query_amount_kontra = "sum(pembayaran)";
		$query_bayar_kontra = "isnull((select sum(bayar_kontra) from det_byr_kontra where master_kontra = id_master_kontra),0)";
        $read_data = $this->m_crud->read_data("master_kontra", "id_master_kontra, tgl_kontra, tgl_bayar, (".$query_amount_kontra.") amount_kontra, (".$query_bayar_kontra.") bayar_kontra", $where, 
			'tgl_kontra asc', 'id_master_kontra, tgl_kontra, tgl_bayar', null, null, 
			$query_bayar_kontra." < ".$query_amount_kontra
		);
		$no=0; $total=0;
        foreach ($read_data as $row){ $no++;
			$list_nota_kontra .= '
				<input type="hidden" id="id_master_kontra'.$no.'" name="id_master_kontra'.$no.'" value="'.$row['id_master_kontra'].'" />
				<input type="hidden" id="tgl_kontra'.$no.'" name="tgl_kontra'.$no.'" value="'.$row['tgl_kontra'].'" />
				<input type="hidden" id="tgl_bayar'.$no.'" name="tgl_bayar'.$no.'" value="'.$row['tgl_bayar'].'" />
				<input type="hidden" id="amount_kontra'.$no.'" name="amount_kontra'.$no.'" value="'.$row['amount_kontra'].'" />
				<input type="hidden" id="bayar_kontra'.$no.'" name="bayar_kontra'.$no.'" value="'.$row['bayar_kontra'].'" />
				<tr>
					<td>'.$no.'</td>
					<td style="text-align:center;">
						<div class="radio radio-primary">
							<input class="form-control checklist_nota" type="radio" id="checklist_nota'.$no.'" name="checklist_nota" value="'.$no.'" onclick="add_list_nota_beli_kontra()" />
							<label for="checklist_nota'.$no.'"></label>
						</div>
					</td>
					<td>'.$row['id_master_kontra'].'</td>
					<td>'.substr($row['tgl_kontra'],0,10).'</td>
					<td>'.substr($row['tgl_bayar'],0,10).'</td>
					<td style="text-align:right;">'.number_format($row['amount_kontra'],2).'</td>
				</tr>
			';
			$total = $total + $row['amount_kontra'];
        }
		$list_nota_kontra .= '<input type="hidden" id="jumlah_nota_kontra" name="jumlah_nota_kontra" value="'.$no.'" />';
		$list_nota_kontra .= '<tr>
							<td colspan="5" style="text-align:center;"><b>Total</b></td>
							<td style="text-align:right;"><b>'.number_format($total,2).'</b></td>
						</tr>';
			
        echo json_encode(array('status'=>count($read_data), 'list_nota_kontra'=>$list_nota_kontra));
    }
	
	public function add_list_bayar_kontra(){
		$this->db->trans_begin();
		
		for($i=1; $i<=$_POST['jumlah_nota_kontra']; $i++){
			if($_POST['checklist_nota']==$i){
				$this->m_crud->delete_data('tr_temp_d', "d1='BK' and d2='".$this->user."'");
				$this->m_crud->create_data('tr_temp_d', array(
					'd1'=>'BK', 
					'd2'=>$this->user, 
					'd3'=>$_POST['id_master_kontra'.$i],
					'd4'=>$_POST['tgl_kontra'.$i],
					'd5'=>$_POST['tgl_bayar'.$i],
					'd6'=>$_POST['amount_kontra'.$i],
					'd7'=>$_POST['bayar_kontra'.$i],
					'd8'=>$_POST['amount_kontra'.$i]-$_POST['bayar_kontra'.$i]
				));
				$this->m_crud->update_data("tr_temp_m", array("m7" => $_POST['id_master_kontra'.$i]), "m1='BK' AND m2 = '" . $this->user . "'");
			}
		}
		
		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        }else {
            $this->db->trans_commit();
            echo true;
        }
	}
	
	public function get_tr_temp_d_bkb() {
        $list_nota_beli_kontra = '';
        $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d1 = 'BK' and d2 = '".$this->user."'");
		$no = 0; $total = 0;
        foreach ($read_data as $row){ $no++;
			$total = $total + str_replace(',','',($row['d8']!=null?$row['d8']:0));
			$list_nota_beli_kontra .= '
				<div class="row">
					<div class="panel-body">	
						<div class="col-sm-6">
							<div class="row" style="margin-bottom: 3px">
								<label class="col-sm-4">No. Kontra Bon</label>
								<div class="col-sm-6">
									<input class="form-control" type="text" readonly id="master_kontra'.$no.'" name="master_kontra'.$no.'" value="'.$row['d3'].'" />
									<b class="error" id="alr_master_kontra'.$no.'"></b>
								</div>
							</div>
							<div class="row" style="margin-bottom: 3px">
								<label class="col-sm-4">Tanggal Kontra Bon</label>
								<div class="col-sm-6">
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control pull-right" readonly name="tgl_kontra'.$no.'" id="tgl_kontra'.$no.'" type="text" value="'.substr($row['d4'],0,10).'">
									</div>
								</div>
							</div>
							<div class="row" style="margin-bottom: 3px">
								<label class="col-sm-4">Tanggal Bayar</label>
								<div class="col-sm-6">
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>															
										<input class="form-control pull-right delay_datepicker_date_from" readonly name="tgl_bayar'.$no.'" id="tgl_bayar'.$no.'" type="text" onchange="update_tmp_detail(\''.$row['d3'].'\',\'d5\',$(this).val())" value="'.substr($row['d5'],0,10).'">
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="row" style="margin-bottom: 3px">
								<label class="col-sm-4">Amount Kontra Bon</label>
								<div class="col-sm-6">
									<input class="form-control" type="text" readonly id="nilai_kontrabon'.$no.'" name="nilai_kontrabon'.$no.'" value="'.number_format($row['d6'],2).'" />
									<b class="error" id="alr_nilai_kontrabon'.$no.'"></b>
								</div>
							</div>
							<div class="row" style="margin-bottom: 3px">
								<label class="col-sm-4">Sudah Bayar</label>
								<div class="col-sm-6">
									<input class="form-control" type="text" readonly id="sudah_bayar'.$no.'" name="sudah_bayar'.$no.'" value="'.number_format($row['d7'],2).'" />
									<b class="error" id="alr_sudah_bayar'.$no.'"></b>
								</div>
							</div>
							<div class="row" style="margin-bottom: 3px">
								<label class="col-sm-4">Bayar Kontra Bon</label>
								<div class="col-sm-6">
									<input class="form-control" type="text" onkeydown="return isNumber(event);" onkeyup="isMoney(\'bayar_kontrabon'.$no.'\',\'+\'); hitung_kontra(); validasi(\'bayar_kontrabon'.$no.'\');" onblur="update_tmp_detail(\''.$row['d3'].'\',\'d8\',$(this).val())" id="bayar_kontrabon'.$no.'" name="bayar_kontrabon'.$no.'" value="'.($row['d8']!=null?number_format(str_replace(',','',$row['d8']),2):null).'"/>
									<b class="error" id="alr_bayar_kontrabon'.$no.'"></b>
								</div>
							</div>
						</div>
					</div>
				</div>
			';
				/*
				<input type="hidden" id="master_kontra'.$no.'" name="master_kontra'.$no.'" value="'.$row['d3'].'" />
				<input type="hidden" id="nilai_kontrabon'.$no.'" name="nilai_kontrabon'.$no.'" value="'.$row['d6'].'" />
				<input type="hidden" id="bayar_kontrabon'.$no.'" name="bayar_kontrabon'.$no.'" value="'.$row['d7'].'" />
				<tr>
					<td>'.$no.'</td>
					<td><button type="button" onclick="hapus_list_kontra(\'' . $row['d3'] . '\')" class="btn btn-primary btn-sm"><i class="md md-close"></i></button></td>
					<td>'.$row['d3'].'</td>
					<td>'.substr($row['d4'],0,10).'</td>
					<td>'.substr($row['d5'],0,10).'</td>
					<td style="text-align:right;">'.number_format($row['d6'],2).'</td>
					<td style="text-align:right;">'.number_format($row['d7'],2).'</td>
					<td>
						<input class="form-control" type="text" onkeydown="return isNumber(event);" onkeyup="isMoney(\'nilai_kontrabon'.$no.'\',\'+\'); hitung_kontra(); validasi(\'nilai_kontrabon'.$no.'\');" onblur="update_tmp_detail(\''.$row['d3'].'\',\'d8\',$(this).val())" id="bayar_kontrabon'.$no.'" name="bayar_kontrabon'.$no.'" value="'.($row['d8']!=null?number_format(str_replace(',','',$row['d8']),2):null).'"/>
						<b class="error" id="alr_bayar_kontrabon'.$no.'"></b>
					</td>
				</tr>
				*/
        }
		$list_nota_beli_kontra .= '<input type="hidden" id="jumlah_nota_beli_kontra" name="jumlah_nota_beli_kontra" value="'.$no.'" />';
			
        echo json_encode(array('status'=>count($read_data), 'list_nota_beli_kontra'=>$list_nota_beli_kontra, 'total'=>$total));
    }
	
	public function delete_tr_temp_d_bkb($tmp_id) {
        $id = base64_decode($tmp_id);
		if($id==null || $id==''){
			$this->m_crud->delete_data("tr_temp_d", "d1 = 'BK' and d2 = '".$this->user."'");
		} else {
			$this->m_crud->delete_data("tr_temp_d", "d1 = 'BK' and d2 = '".$this->user."' and d3 = '".$id."'");
		}
		
		echo true;
    }
	
	public function update_tr_temp_d_bkb($tmp_id, $tmp_column, $tmp_value) {
        $id = base64_decode($tmp_id);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);
		
		$this->m_crud->update_data("tr_temp_d", array($column => $value), "d1 = 'BK' and d2 = '".$this->user."' and d3 = '".$id."'");
		
    }
	
	public function delete_trx_bkb($delete_image=true) {
        $this->m_crud->delete_data("tr_temp_m", "m1 = 'BK' and m2 = '".$this->user."'");
		/*if($delete_image==true){
			$data_image = $this->m_crud->read_data('tr_temp_d', 'd6', "d1 = 'PO' and d2 = '".$this->user."'");
			foreach($data_image as $image){ if($image['d6']!=null){ unlink($image['d6']); } }
		}*/
		$this->m_crud->delete_data("tr_temp_d", "d1 = 'BK' and d2 = '".$this->user."'");
    }
	
	
    public function simpan_bkb() {
        $this->access_denied(243);
        $param = 'Add';
		$trx_no = $this->m_website->generate_kode('BK', $_POST['lokasi'], date("ymd", strtotime($_POST['tgl'])));
        
        $this->db->trans_begin();
		
        $data_master = array(
            'id_master_byr_kontra' => $trx_no,
            'tgl' => $_POST['tgl'] . " " . date("H:i:s"),
            'lokasi' => $_POST['lokasi'],
			'supplier' => $_POST['supplier'],
			'ket' => str_replace("'","`",$_POST['descrip']),
			'acc_no' => str_replace("'","`",$_POST['acc_no']),
			'bank' => str_replace("'","`",$_POST['bank']),
			'bi_code' => str_replace("'","`",$_POST['bi_code']),
			'bank_branch' => str_replace("'","`",$_POST['bank_branch']),
			'jenis' => str_replace("'","`",$_POST['jenis']),
			'rec' => str_replace("'","`",$_POST['rec']),
			'receiv' => str_replace("'","`",$_POST['receiv']),
            'user_akun' => $this->user,
            'status' => '0',
        );
        $this->m_crud->create_data("master_byr_kontra", $data_master);

        $det_log = array();
		for ($i=1; $i<=$_POST['jumlah_nota_beli_kontra']; $i++) {
			$data_detail_mutasi = array(
				'master_byr_kontra' => $trx_no,
				'master_kontra' => $_POST['master_kontra'.$i],
				'bayar_kontra' => $_POST['bayar_kontrabon'.$i],
				'status' => '0'
			);
			$this->m_crud->create_data("det_byr_kontra", $data_detail_mutasi);
			array_push($det_log, $data_detail_mutasi);
		}

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$trx_no,'jenis'=>ucfirst($param),'transaksi'=>'Bayar Kontra Bon'), array('master'=>$data_master,'detail'=>$det_log));

        $this->delete_trx_bkb(false);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        }else {
            $this->db->trans_commit();
            echo true;
        }
    }
	/*End modul bayar kontra bon*/
	
	
	
    /*Start modul retur tanpa nota*/
    public function retur_tanpa_nota(){
        $this->access_denied(53);
        $data = $this->data;
        $function = 'retur_tanpa_nota';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Retur Tanpa Nota';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, nama_toko Nama, serial', $this->where_lokasi);
        $data['data_supplier'] = $this->m_crud->read_data('Supplier','Kode, Nama');

        $this->load->view('bo/index', $data);
    }

    public function edit_retur_tanpa_nota($nota_sistem) {
        $nota_sistem = base64_decode($nota_sistem);

        $this->access_denied(53);
        $data = $this->data;
        $function = 'edit_retur_tanpa_nota';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Edit Retur Tanpa Nota';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, Nama, serial');
        $data['data_supplier'] = $this->m_crud->read_data('Supplier','Kode, Nama');

        $this->db->trans_begin();
        $get_data_retur = $this->m_crud->get_data("Master_Retur_Beli", "No_Retur, Tgl, kd_kasir, Supplier, keterangan, Lokasi lk, Lokasi+'|'+(SELECT serial FROM Lokasi WHERE kode=Lokasi) lokasi", "No_Retur='".$nota_sistem."'");
        $read_data_retur = $this->m_crud->read_data("Det_Retur_Beli drb, barang br", "drb.*, br.barcode, br.nm_brg, br.Deskripsi, br.satuan, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE lokasi NOT IN ('MUTASI', 'Retur') AND Kartu_stock.kd_brg = br.kd_brg AND lokasi='".$get_data_retur['lk']."' AND Kartu_stock.kd_trx <> '".$get_data_retur['No_Retur']."') stock", "drb.kd_brg=br.kd_brg AND drb.No_Retur='".$nota_sistem."'");

        $get_tmp_data = $this->m_crud->count_data("tr_temp_m", "m1", "m5='".$this->user."' AND m7='".$nota_sistem."' AND m6='edit_retur'");

        if ($get_tmp_data == 0) {
            $this->m_crud->delete_data("tr_temp_m", array('m7' => 'edit_retur', 'm5' => $this->user));
            $this->m_crud->delete_data("tr_temp_d", array('d12' => 'edit_retur', 'd10' => $this->user));
            /*Add to master temporary*/
            $data_tmp_m = array(
                'm1' => $get_data_retur['No_Retur'],
                'm2' => substr($get_data_retur['Tgl'], 0, 10),
                'm3' => $get_data_retur['lokasi'],
                'm4' => $get_data_retur['Supplier'],
                'm5' => $this->user,
                'm6' => $get_data_retur['No_Retur'],
                'm7' => 'edit_retur',
                'm8' => $get_data_retur['keterangan'],
				'm9' => 1,
                'm10' => $get_data_retur['lokasi_cabang'],
            );

            $this->m_crud->create_data("tr_temp_m", $data_tmp_m);

            $id = 1;

            /*Add to detail temporary*/
            foreach ($read_data_retur as $get_barang) {
                $data_tmp_d = array(
                    'd1' => $get_barang['No_Retur'],
                    'd2' => $get_barang['kd_brg'],
                    'd3' => $get_barang['nm_brg'],
                    'd4' => $get_barang['Deskripsi'],
                    'd5' => $get_barang['hrg_beli'],
                    'd6' => $get_barang['satuan'],
                    'd7' => $get_barang['stock'],
                    'd8' => $get_barang['jml'],
                    'd9' => $get_barang['barcode'],
                    'd10' => $this->user,
                    'd11' => $get_barang['No_Retur'],
                    'd12' => 'edit_retur',
                    'd13' => $id++,
                    'd14' => $get_barang['keterangan'],
                    'd15' => $get_barang['kd_packing'],
                    'd16' => $get_barang['kondisi']
                );

                $this->m_crud->create_data("tr_temp_d", $data_tmp_d);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        $this->load->view('bo/index', $data);
    }

    public function add_tr_temp_m_retur() {
        $param = $_POST['param'];
        $data = array(
            'm1' => $_POST['nota_sistem'],
            'm2' => $_POST['tgl_retur'],
            'm3' => $_POST['lokasi'],
            'm4' => $_POST['supplier'],
            'm5' => $this->user,
            'm8' => $_POST['keterangan'],
            'm9' => $_POST['set_focus'],
            'm10' => $_POST['lokasi_cabang']
        );

        if ($param == 'edit_retur') {
            $get_tmp_m = $this->m_crud->get_data("tr_temp_m", "m6", "m5='".$this->user."' AND m7='edit_retur'");
            $data['m6'] = $get_tmp_m['m6'];
            $data['m7'] = 'edit_retur';
        } else {
            $data['m7'] = 'add_retur';
        }

        if ($param == 'edit_retur') {
            $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "m7='edit_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");
        } else {
            $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "m7='add_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");
        }

        if ($cek_data == 1) {
            if ($param == 'edit_retur') {
                $this->m_crud->update_data("tr_temp_m", $data, "m7='edit_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");
                $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['nota_sistem']), "d7='edit_retur' AND (d5 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')");
            } else {
                $this->m_crud->update_data("tr_temp_m", $data, "m7='add_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");
                $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['nota_sistem']), "d7='add_retur' AND (d5 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')");
            }
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }
    }

    public function get_tr_temp_m_retur($tmp_param=null) {
        $param = base64_decode($tmp_param);
        if ($param == 'edit_retur') {
            $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m7='edit_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");
        } else {
            $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m7='add_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");
        }

        echo json_encode($get_data);
    }

    public function update_tr_temp_m_retur($tmp_column, $tmp_data, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $column = base64_decode($tmp_column);
        $data = base64_decode($tmp_data);

        if ($param == 'edit_retur') {
            $this->m_crud->update_data("tr_temp_m", array($column => $data), "m7='edit_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");
        } else {
            $this->m_crud->update_data("tr_temp_m", array($column => $data), "(m7='add_retur' AND m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");
        }
    }

    public function get_tr_temp_d_retur($tmp_param = null) {
        $param = base64_decode($tmp_param);

        $get_status_barang = json_decode($this->m_crud->get_data("Setting", "status_barang", "Kode='1111'")['status_barang'], true);
        $status_barang = $get_status_barang['status_barang_ho'];

        $list_barang = '';
        if ($param == 'edit_retur') {
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d12='edit_retur' AND (d10 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')", "CONVERT(INTEGER, d13) ASC");
        } else {
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d12='add_retur' AND (d10 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')", "CONVERT(INTEGER, d13) ASC");
        }

        $no = 1;
        $col = 0;
        $total_stock = 0;
        $qty_retur = 0;
        $grand_total = 0;
        $length = count($read_data);
        /*
         <td data-priority="0">' . $row['d4'] . '</td>
        <td data-priority="0"><input type="number" id="konversi' . $no . '" name="konversi' . $no . '" class="form-control width-uang" value="" readonly></td>
        */
        foreach ($read_data as $row) {
            $total_retur = $row['d5'] * $row['d8'];
            $qty_retur = $qty_retur + $row['d8'];
            $total_stock = $total_stock + $row['d7'];
            $grand_total = $grand_total + $total_retur;

            $kondisi_barang = '<option value="">Pilih Kondisi</option>';
            foreach ($status_barang as $row_kondisi) {
                $kondisi_barang .= '<option '.($row_kondisi['status']==$row['d16']?'selected':'').' value="'.$row_kondisi['status'].'">'.$row_kondisi['status'].'</option>';
            }

            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d9'] . '\')" class="btn btn-primary btn-sm"><i class="md md-close"></i></button></td>
                                <td data-priority="1">' . $row['d2'] . '</td>
                                <td data-priority="1">' . $row['d9'] . '</td>
                                <td data-priority="1">' . $row['d4'] . '</td>
                                <td data-priority="1">' . $row['d3'] . '</td>                                
                                <td data-priority="1">' . $row['d6'] . '</td>
                                <td data-priority="1"><select style="width: auto" class="form-control" id="d16' . $no . '" name="d16' . $no . '" onchange="update_tmp_detail(\'' . $row['d9'] . '\', \'d16\', $(this).val())">' . $kondisi_barang . '</select></td>
                                <td data-priority="1"><input onblur="update_tmp_detail(\'' . $row['d9'] . '\', \'d14\', $(this).val())" onkeyup="return to_barcode(event)" onfocus="this.select()" type="text" id="d14' . $no . '" name="d14' . $no . '" style="width: 100px" class="form-control input-sm" value="' . $row['d14'] . '"></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d9'] . '\', \'d5\', $(this).val())" onkeydown="return isNumber(event)" onkeyup="hitung_barang(\'d5\', \'' . $no . '\', $(this).val(), '.$length.'); isMoney(\'d5' . $no . '\', \'+\'); return to_qty(event, '.$no.')" onfocus="this.select()" type="text" id="d5' . $no . '" name="d5' . $row['d5'] . '" class="form-control width-uang input-sm" value="' . number_format((float)$row['d5'], 2, '.', ',') . '"></td>
                                <td data-priority="1" class="text-center"><input type="number" id="stock' . $no . '" name="stock' . $no . '" class="form-control width-diskon input-sm" value="'.($row['d7']+0).'" readonly></td>                                
                                <td><input onblur="update_tmp_detail(\'' . $row['d9'] . '\', \'d8\', $(this).val())" onkeyup="hitung_barang(\'d8\', \'' . $no . '\', $(this).val(), '.$length.'); return to_barcode(event)" onfocus="this.select()" type="number" id="d8' . $no . '" name="d8' . $no . '" class="form-control width-diskon input-sm" value="' . ($row['d8'] + 0) . '"></td>
                                <td><input type="text" id="nilai_retur' . $no . '" name="nilai_retur' . $no . '" class="form-control width-uang input-sm" value="'.number_format((float)$total_retur, 2, '.', ',').'" readonly></td>
                            </tr>';
            $col = $no;
            $no++;
        }
        $list_barang .= '<tr>
            <th colspan="10" class="text-right">TOTAL</th>
            <th class="text-center"><b id="total_stock">'.$total_stock.'</b></th>
            <th class="text-center"><b id="total_qty_retur">'.$qty_retur.'</b></th>
            <th class="text-right"><b id="total_nilai_retur">'.number_format((float)$grand_total, 2, '.', ',').'</b></th>
        </tr>';
        $list_barang .= '<input type="hidden" id="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang));
    }

    public function insert_tr_temp_d_retur($nota_sistem, $get_barang, $barcode, $param=null, $qty=1) {

        $data = array(
            'd1' => $nota_sistem,
            'd2' => $get_barang['kd_brg'],
            'd3' => $get_barang['nm_brg'],
            'd4' => $get_barang['Deskripsi'],
            'd5' => $get_barang['hrg_beli'],
            'd6' => $get_barang['satuan'],
            'd7' => $get_barang['stock'],
            'd8' => $qty,
            'd9' => $barcode,
            'd10' => $this->user,
            'd14' => '',
			'd15' => $get_barang['kd_packing'],
            'd16' => ''
        );

        if ($param == 'edit_retur') {
            $get_tmp_d = $this->m_crud->get_data("tr_temp_d", "d11", "d10='".$this->user."' AND d12='edit_retur'");
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d13)) id", "d10='".$this->user."' AND d12='edit_retur'");
            $data['d11'] = $get_tmp_d['d11'];
            $data['d12'] = 'edit_retur';
            $data['d13'] = ((int)$get_max_id['id']+1);
        } else {
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d13)) id", "d10='".$this->user."' AND d12='add_retur'");
            $data['d12'] = 'add_retur';
            $data['d13'] = ((int)$get_max_id['id']+1);
        }

        $this->m_crud->create_data("tr_temp_d", $data);
    }

    public function update_tr_temp_d_retur($tmp_barcode, $tmp_column, $tmp_value, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        if ($param == 'edit_retur') {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "d12='edit_retur' AND (SUBSTRING(d1,1,2) = 'NB') AND (d9 = '".$barcode."') AND (d10 = '".$this->user."')");
        } else {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "d12='add_retur' AND (SUBSTRING(d1,1,2) = 'NB') AND (d9 = '".$barcode."') AND (d10 = '".$this->user."')");
        }
    }

    public function delete_tr_temp_d_retur($tmp_barcode, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $barcode = base64_decode($tmp_barcode);

        if ($param == 'edit_retur') {
            /*$get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d15", "d19='edit_retur' AND (SUBSTRING(d1,1,2) = 'BL') AND (d16 = '".$barcode."') AND (d17 = '".$this->user."')");

            if ($get_tmp_data['d15'] > 1) {
                $data = array(
                    'd15' => (int)$get_tmp_data['d15'] - 1
                );

                $this->m_crud->update_data("tr_temp_d", $data, "d19='edit_retur' AND (d17 = '".$this->user."') AND (d16 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'BL')");
            }else {
                $this->m_crud->delete_data("tr_temp_d", "d19='edit_retur' AND (d17 = '".$this->user."') AND (d16 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'BL')");
            }*/

            $this->m_crud->delete_data("tr_temp_d", "d12='edit_retur' AND (d10 = '".$this->user."') AND (d9 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'NB')");

        } else {
            /*$get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d15", "d19='add_retur' AND (SUBSTRING(d1,1,2) = 'BL') AND (d16 = '".$barcode."') AND (d17 = '".$this->user."')");

            if ($get_tmp_data['d15'] > 1) {
                $data = array(
                    'd15' => (int)$get_tmp_data['d15'] - 1
                );

                $this->m_crud->update_data("tr_temp_d", $data, "d19='add_retur' AND (d17 = '".$this->user."') AND (d16 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'BL')");
            }else {
                $this->m_crud->delete_data("tr_temp_d", "d19='add_retur' AND (d17 = '".$this->user."') AND (d16 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'BL')");
            }*/

            $this->m_crud->delete_data("tr_temp_d", "d12='add_retur' AND (d10 = '".$this->user."') AND (d9 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'NB')");

        }

        echo true;
    }

    public function get_barang_retur($tmp_nota_sistem, $tmp_barcode, $tmp_lokasi_beli, $tmp_supplier, $tmp_cat_cari, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $cat_cari = base64_decode($tmp_cat_cari);
        $nota_sistem = base64_decode($tmp_nota_sistem);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi_beli));
        $lokasi_beli = $explode_lokasi[0];
        $supplier = base64_decode($tmp_supplier);

        if ($cat_cari == 1) {
            $col_barang = 'barang.kd_brg';
            $col_tmp = 'd2';
        } else if ($cat_cari == 2) {
            $col_barang = 'barang.barcode';
            $col_tmp = 'd9';
        } else if ($cat_cari == 3) {
            $col_barang = 'barang.Deskripsi';
            $col_tmp = 'd4';
        } else if ($cat_cari == 4) {
            $col_barang = 'barang.kd_packing';
            $col_tmp = 'd15';
        }
		
		if ($cat_cari == 4) {
			$qty = $this->m_crud->get_data('barang', 'isnull((qty_packing),0) qty_packing', $col_barang." = '".$barcode."'")['qty_packing'];
		} else {
			$qty = 1;
		}
		
        if ($param == 'edit_retur') {
            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d8", "d12='edit_retur' AND (SUBSTRING(d1,1,2) = 'NB') AND (".$col_tmp." = '".$barcode."') AND (d10 = '".$this->user."')");
        } else {
            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d8", "d12='add_retur' AND (SUBSTRING(d1,1,2) = 'NB') AND (".$col_tmp." = '".$barcode."') AND (d10 = '".$this->user."')");
        }

        if ($get_tmp_data != null) {
            $data = array( 
                'd8' => (int)$get_tmp_data['d8'] + $qty
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d10 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'NB')");
            echo json_encode(array('status' => 1));
        }else {
            $get_stock = "isnull((SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE kd_brg=barang.kd_brg AND lokasi='".$lokasi_beli."'), 0) stock";
            $get_barang = $this->m_crud->get_data("barang", "barang.kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, barang.Deskripsi, ".$get_stock, "(rtrim(ltrim(".$col_barang.")) = '".$barcode."') AND (Group1='".$supplier."')");
            if ($get_barang != null) {
                if ($param == 'edit_retur') {
                    $this->insert_tr_temp_d_retur($nota_sistem, $get_barang, $get_barang['barcode'], 'edit_retur', $qty);
                } else {
                    $this->insert_tr_temp_d_retur($nota_sistem, $get_barang, $get_barang['barcode'], null, $qty);
                }
                echo json_encode(array('status' => 1));
            }else {
                $get_barang = $this->m_crud->get_data("barang", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, barang.Deskripsi, ".$get_stock, "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                if ($get_barang != '') {
                    /*if ($param == 'edit_retur') {
                        $this->insert_tr_temp_d($nota_sistem, $get_barang, $barcode, 'edit_retur');
                    } else {
                        $this->insert_tr_temp_d($nota_sistem, $get_barang, $barcode);
                    }*/
                    echo json_encode(array('status' => 2, 'notif' => "Barang dari supplier ".$supplier." tidak tersedia!"));
                }else {
                    echo json_encode(array('status' => 3, 'notif' => "Barang tidak tersedia!"));
                }
            }
        }
    }

    public function delete_trans_retur($tmp_param=null) {
        $param = base64_decode($tmp_param);

        if ($param == 'edit_retur') {
            $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "m7='edit_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");
            $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "d12='edit_retur' AND (d10 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')");
        } else {
            $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "m7='add_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");
            $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "d12='add_retur' AND (d10 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')");
        }

        if ($delete_data_master && $delete_data_detail) {
            echo true;
        }

        $this->m_crud->delete_data("Master_Retur_Beli", "No_Retur not in (select No_Retur from Det_Retur_Beli)");
    }

    public function get_sub_total_retur($param=null) {
        if ($param == 'edit_retur') {
            $read_data = $this->m_crud->read_data("tr_temp_d", "isnull(d5, 0) d5, isnull(d8, 0) d8", "d12='edit_retur' AND (d10 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')");
        } else {
            $read_data = $this->m_crud->read_data("tr_temp_d", "isnull(d5, 0) d5, isnull(d8, 0) d8", "d12='add_retur' AND (d10 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')");
        }
        $sub_total = 0;
        foreach ($read_data as $row) {
            $hitung_netto = $row['d5'] * $row['d8'];
            $sub_total = $sub_total + $hitung_netto;
        }

        return $sub_total;
    }

    public function trans_retur_tanpa_nota_x() {
        $param = $_POST['param'];
        $tgl_retur = $_POST['tgl_retur'];
        $supplier = $_POST['supplier'];
        $keterangan = $_POST['keterangan'];
        $explode_lokasi = explode('|', $_POST['lokasi']);
        $lokasi = $explode_lokasi[0];
        $serial = $explode_lokasi[1];
        $explode_lokasi_cabang = explode('|', $_POST['lokasi_cabang']);
        $lokasi_cabang = $explode_lokasi_cabang[0];
        $serial_cabang = $explode_lokasi_cabang[1];

        if ($param == 'edit_retur') {
            $nota_sistem = $_POST['nota_sistem'];
            $get_kode = $this->m_crud->get_data("tr_temp_m", "m6", "m5 = '".$this->user."' AND (LEFT(m6, 2) = '".substr($nota_sistem, 0, 2)."') AND (SUBSTRING(m6, 4, 6) = '".substr($nota_sistem, 3, 6)."') AND (RIGHT(m6, 1) = '".substr($nota_sistem, 14, 1)."') AND m7 = 'edit_retur'");
            if ($get_kode == '') {
                $nota_sistem = $this->m_website->generate_kode("NB", $serial, substr(str_replace('-', '', $tgl_retur), 2));
            } else {
                $nota_sistem = $get_kode['m6'];
            }
        } else {
            $nota_sistem = $this->m_website->generate_kode("NB", $serial, substr(str_replace('-', '', $tgl_retur), 2));
        }

        $this->db->trans_begin();

        if ($param == 'edit_retur') {
            $param = 'edit';
            $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "m7='edit_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");;
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d12='edit_retur' AND (d10 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')");;

            $this->m_crud->delete_data("Master_Retur_Beli", "No_Retur='".$get_temp_m['m6']."'");
            $this->m_crud->delete_data("Det_Retur_Beli", "No_Retur='".$get_temp_m['m6']."'");
            $this->m_crud->delete_data("Kartu_stock", "kd_trx='".$get_temp_m['m6']."'");
            $nilai_retur = $this->get_sub_total_retur('edit_retur');
        } else {
            $param = 'add';
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d12='add_retur' AND (d10 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')");;
            $nilai_retur = $this->get_sub_total_retur('add_retur');
        }

        $master_retur = array(
            'No_Retur' => $nota_sistem,
            'Tgl' => $tgl_retur . " " . date("H:i:s"),
            'kd_kasir' => $this->user,
            'Lokasi' => $lokasi,
            'Supplier' => $supplier,
            'Total' => $nilai_retur,
            'no_beli' => 'Tanpa Nota',
            'keterangan' => $keterangan,
            'lokasi_cabang' => $lokasi_cabang
        );

        $this->m_crud->create_data('Master_Retur_Beli', $master_retur);

        $det_log = array();
        foreach ($read_temp_d as $row) {
            $det_retur = array(
                'No_Retur' => $nota_sistem,
                'kd_brg' => $row['d2'],
                'jml' => $row['d8'],
                'hrg_beli' => $row['d5'],
                'keterangan' => $row['d14'],
                'kondisi' => $row['d16']
            );
            $this->m_crud->create_data('Det_Retur_Beli', $det_retur);
            array_push($det_log, $det_retur);

            $kartu_stok = array(
                'kd_trx' => $nota_sistem,
                'tgl' => $tgl_retur . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => 0,
                'stock_out' => $row['d8'],
                'lokasi' => $lokasi,
                'keterangan' => 'Retur Pembelian',
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $kartu_stok);
        }

        if ($param == 'edit') {
            $master_retur['trx_old'] = $get_temp_m['m6'];
        }

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$nota_sistem,'jenis'=>ucfirst($param),'transaksi'=>'Retur Pembelian Tanpa Nota'), array('master'=>$master_retur,'detail'=>$det_log));

        $this->delete_trans_retur();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo '0';
        } else {
            $this->db->trans_commit();
            echo $nota_sistem;
        }
    }

    /*public function trans_retur_tanpa_nota() {
        $param = $_POST['param_'];
        $nota_sistem = $_POST['nota_sistem_'];
        $nilai_retur = $_POST['nilai_retur_'];

        $get_kode = $this->m_crud->get_data("Master_Retur_Beli", "No_Retur", "(No_Retur = '".$nota_sistem."')");

        if ($get_kode != '' && $param == 'add_retur') {
            $nota_sistem = $this->m_website->generate_kode(substr($get_kode['No_Retur'], 0, 2), substr($get_kode['No_Retur'], 14), substr($get_kode['No_Retur'], 3, 6));
        }

        $this->db->trans_begin();

        if ($param == 'edit_retur') {
            $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "m7='edit_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");;
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d12='edit_retur' AND (d10 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')");;

            $this->m_crud->delete_data("master_beli", "No_Retur='".$get_temp_m['m6']."'");
            $this->m_crud->delete_data("det_beli", "No_Retur='".$get_temp_m['m6']."'");
            $this->m_crud->delete_data("Kartu_stock", "kd_trx='".$get_temp_m['m6']."'");
        } else {
            $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "m7='add_retur' AND (m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'NB')");;
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d12='add_retur' AND (d10 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'NB')");;
        }

        $explode_lokasi = explode('|', $get_temp_m['m3']);
        $lokasi = $explode_lokasi[0];

        $master_retur = array(
            'No_Retur' => $nota_sistem,
            'Tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
            'kd_kasir' => $this->user,
            'Lokasi' => $lokasi,
            'Supplier' => $get_temp_m['m4'],
            'Total' => $nilai_retur,
            'no_beli' => 'Tanpa Nota'
        );

        $this->m_crud->create_data('Master_Retur_Beli', $master_retur);

        foreach ($read_temp_d as $row) {
            $det_retur = array(
                'No_Retur' => $nota_sistem,
                'kd_brg' => $row['d2'],
                'jml' => $row['d8'],
                'hrg_beli' => $row['d5'],
                'keterangan' => $row['d14']
            );
            $this->m_crud->create_data('Det_Retur_Beli', $det_retur);

            $kartu_stok = array(
                'kd_trx' => $nota_sistem,
                'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => 0,
                'stock_out' => $row['d8'],
                'lokasi' => $lokasi,
                'keterangan' => 'Retur Pembelian',
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $kartu_stok);
        }

        $this->delete_trans_retur();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo '0';
        } else {
            $this->db->trans_commit();
            echo $nota_sistem;
        }
    }*/

    /*End modul retur tanpa nota*/
	
	/*Start modul bayar hutang*/
	public function search_nota_beli(){
		$keyword = $this->uri->segment(3); // tangkap variabel keyword dari URL
		$data = $this->m_crud->read_data('master_beli mb', 'no_faktur_beli', "mb.no_faktur_beli not in (select master_beli from det_kontra) and no_faktur_beli like '%".$keyword."%' and type = 'Kredit' and (isnull((select sum(jumlah-bulat) from bayar_hutang where fak_beli = mb.no_faktur_beli),0)) < (nilai_pembelian - isnull((SELECT SUM(Total) FROM Master_Retur_Beli WHERE no_beli=mb.no_faktur_beli),0))", null, null, 20); // cari di database
		foreach($data as $row){ // format keluaran di dalam array
			$arr['query'] = $keyword;
			$arr['suggestions'][] = array(
				'value'	=> $row['no_faktur_beli'],
			);
		}
		echo json_encode($arr);
	}
	
	public function bayar_hutang($action = null, $param1 = null){
		$this->access_denied(241);
		$data = $this->data;
		$function = 'bayar_hutang';
		$view = $this->control . '/';
		
		//if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		
		$data['title'] = 'Bayar Hutang';
		$data['page'] = $function;
		$data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, nama_toko Nama, serial', $this->where_lokasi);
        //$data['data_supplier'] = $this->m_crud->read_data('Supplier','Kode, Nama');
        $data['data_bank'] = $this->m_crud->read_data('bank','Nama');
		$data['data_pembelian'] = array();
        
		if((isset($_POST['cari'])||isset($_POST['simpan'])) || ($action=='bayar_nota_beli'&&$param1!=null)){
			$nota_beli = (isset($_POST['cari'])||isset($_POST['simpan']))?$_POST['nota_beli']:(($action=='bayar_nota_beli'&&$param1!=null)?base64_decode($param1):null);
			$data['data_pembelian'] = $this->m_crud->join_data("kartu_hutang kh", "mb.no_faktur_beli, mb.tgl_jatuh_tempo, kh.total_beli nilai_pembelian, kh.total_bayar jumlah_bayar, sp.Nama", array(array('table'=>'master_beli mb', 'type'=>'LEFT'), array('table'=>'supplier sp', 'type'=>'LEFT')), array("kh.no_faktur_beli=mb.no_faktur_beli", "kh.kode_supplier=sp.kode"), "mb.no_faktur_beli not in (select master_beli from det_kontra) and mb.no_faktur_beli='".$nota_beli."' AND kh.total_bayar < kh.total_beli");
			//$data['data_pembelian'] = $this->m_crud->join_data('master_beli mb', 'mb.no_faktur_beli, Nama, mb.tgl_jatuh_tempo, mb.nilai_pembelian, mb.PPN, mb.disc, isnull((SELECT SUM(Total) FROM Master_Retur_Beli WHERE no_beli=mb.no_faktur_beli),0) jumlah_retur, isnull((select sum(jumlah-bulat) from bayar_hutang where fak_beli = mb.no_faktur_beli),0) jumlah_bayar', 'Supplier', 'kode_supplier = Kode', "no_faktur_beli = '".$nota_beli."' and (isnull((select sum(jumlah-bulat) from bayar_hutang where fak_beli = mb.no_faktur_beli),0)) < (nilai_pembelian - isnull((SELECT SUM(Total) FROM Master_Retur_Beli WHERE no_beli=mb.no_faktur_beli),0))");
		}
		
		if(isset($_POST['simpan'])){ 
			$this->db->trans_begin();
			$kd_trx = $_POST['nota_sistem'];
			$param = 'Add';
			$data_hutang = array(
                'no_nota' => $kd_trx,
                'fak_beli' => $_POST['nota_beli'],
                'tgl_byr' => $_POST['tanggal'].' '.date('H:i:s'),
                'cara_byr' => $_POST['cara_byr'],
                'jumlah' => $_POST['jumlah_bayar'],
                'kasir' => $this->user,
                'tgl_jatuh_tempo' => $_POST['tgl_jatuh_tempo'],
                'nm_bank' => $_POST['cara_byr']=='Cek/Giro'?$_POST['bank']:'-',
                'bulat' => $_POST['pembulatan'],
                'nogiro' => $_POST['cara_byr']=='Cek/Giro'?$_POST['nogiro']:'-',
                'tgl_cair_giro' => $_POST['cara_byr']=='Cek/Giro'?$_POST['tanggal_cair']:null,
                'ket' => $_POST['ket']
            );
			$this->m_crud->create_data('bayar_hutang', $data_hutang);

            $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$kd_trx,'jenis'=>ucfirst($param),'transaksi'=>'Bayar Hutang'), array('master'=>$data_hutang,'detail'=>array()));

			$sisa_hutang = (floatval(str_replace(',','',$_POST['jumlah_hutang'])) + floatval(str_replace(',','',$_POST['pembulatan']))) - floatval(str_replace(',','',$_POST['jumlah_bayar']));
			if($sisa_hutang==0){
				$this->m_crud->update_data('master_beli', array('Pelunasan'=>'Lunas'), "no_faktur_beli = '".$_POST['nota_beli']."'");
			}
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit(); redirect($data['content']);
			}
		} 
		
        $this->load->view('bo/index', $data);
	}
	/*End modul bayar hutang*/
	
	/*Start modul pembelian barang*/
	public function pembelian_barang(){
        $this->access_denied(52);
		$data = $this->data;
		$function = 'pembelian_barang';
		$view = $this->control . '/';

		//if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		$data['title'] = 'Pembelian Barang';
		$data['page'] = $function;
		$data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, nama_toko Nama, serial', $this->where_lokasi);
        $data['data_supplier'] = $this->m_crud->read_data('Supplier','Kode, Nama');
        $data['data_po'] = $this->m_crud->read_data('Master_PO', 'no_po', 'status = 0');

        $this->load->view('bo/index', $data);
	}

	public function check_nota($tmp_no_nota, $param=null) {
	    $no_nota = strtoupper(base64_decode($tmp_no_nota));

        if ($param == null) {
            $get_no_nota = $this->m_crud->get_data("master_beli", "noNota", "UPPER(noNota) = '" . $no_nota . "'");
        } else {
            $get_tmp = $this->m_crud->get_data("tr_temp_m", "m6", array('m13' => 'edit', 'm9' => $this->user));
            $get_no_nota = $this->m_crud->get_data("master_beli", "noNota", "noNota<>'".$get_tmp['m6']."' AND UPPER(noNota) = '" . $no_nota . "'");
        }

        if (count($get_no_nota) != 0) {
            $status = '1';
        } else {
            $status = '0';
        }

        echo $status;
    }

    public function edit_pembelian_barang($tmp_kode_pembelian){
        //$this->access_denied(13);
        $kode_pembelian = base64_decode($tmp_kode_pembelian);
        $data = $this->data;
        $function = 'edit_pembelian_barang';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Edit Pembelian Barang';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, Nama, serial');
        $data['data_supplier'] = $this->m_crud->read_data('Supplier','Kode, Nama');
        //$data['data_po'] = $this->m_crud->read_data('Master_PO', 'no_po', 'status = 0');

        $this->db->trans_begin();
        $get_data_pembelian = $this->m_crud->get_data("master_beli mb, Lokasi lk", "mb.*, lk.Kode, lk.serial", "mb.Lokasi=lk.Kode AND no_faktur_beli='".$kode_pembelian."'");
        $read_data_pembelian = $this->m_crud->read_data("det_beli db, barang br", "db.*, br.kd_packing, br.barcode, br.nm_brg, br.Deskripsi, br.satuan, br.hrg_jual_1, br.hrg_jual_2, br.hrg_jual_3, br.hrg_jual_4", "db.kode_barang=br.kd_brg AND no_faktur_beli='".$kode_pembelian."'");
		
		$data['data_po'] = $this->m_crud->read_data('Master_PO', 'no_po', "(status=0 or no_po='".$get_data_pembelian['no_po']."')");
		
        $get_tmp_data = $this->m_crud->count_data("tr_temp_m", "m1", "m9='".$this->user."' AND m12='".$get_data_pembelian['no_faktur_beli']."' AND m13='edit'");

        if ($get_tmp_data == 0) {
            $this->m_crud->delete_data("tr_temp_m", array('m13' => 'edit', 'm9' => $this->user));
            $this->m_crud->delete_data("tr_temp_d", array('d19' => 'edit', 'd17' => $this->user));
            /*Add to master temporary*/
            $data_tmp_m = array(
                'm1' => $get_data_pembelian['no_faktur_beli'],
                'm2' => substr($get_data_pembelian['tgl_beli'], 0, 10),
                'm3' => $get_data_pembelian['Kode'] . '|' . $get_data_pembelian['serial'],
                'm4' => $get_data_pembelian['type'],
                'm5' => $get_data_pembelian['kode_supplier'],
                'm6' => $get_data_pembelian['noNota'],
                'm7' => $get_data_pembelian['no_po'],
                'm8' => substr($get_data_pembelian['tgl_jatuh_tempo'], 0, 10),
                'm9' => $this->user,
                'm10' => $get_data_pembelian['disc'],
                'm11' => $get_data_pembelian['PPN'],
                'm12' => $get_data_pembelian['no_faktur_beli'],
                'm13' => 'edit',
				'm14' => 1
            );

            $this->m_crud->create_data("tr_temp_m", $data_tmp_m);

            $id = 1;

            /*Add to detail temporary*/
            foreach ($read_data_pembelian as $get_barang) {
                $data_tmp_d = array(
                    'd1' => $get_data_pembelian['no_faktur_beli'],
                    'd2' => $get_barang['kode_barang'],
                    'd3' => $get_barang['nm_brg'],
                    'd4' => $get_barang['satuan'],
                    'd5' => $get_barang['harga_beli'],
                    'd6' => $get_barang['hrg_jual_1'],
                    'd7' => $get_barang['hrg_jual_2'],
                    'd8' => $get_barang['hrg_jual_3'],
                    'd9' => $get_barang['hrg_jual_4'],
                    'd10' => $get_barang['diskon'],
                    'd11' => $get_barang['disc2'],
                    'd12' => 0,
                    'd13' => 0,
                    'd14' => $get_barang['PPN'],
                    'd15' => $get_barang['jumlah_beli'],
                    'd16' => $get_barang['barcode'],
                    'd17' => $this->user,
                    'd18' => $get_data_pembelian['no_faktur_beli'],
                    'd19' => 'edit',
                    'd20' => $get_barang['Deskripsi'],
                    'd21' => $id++,
                    'd22' => $get_barang['harga_beli'],
                    'd23' => $get_barang['jumlah_bonus'],
                    'd24' => $get_barang['kd_packing']
                );

                $this->m_crud->create_data("tr_temp_d", $data_tmp_d);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        $this->load->view('bo/index', $data);
    }
	
	public function add_tr_temp_m() {
	    $param = $_POST['param'];
	    $data = array(
	        'm1' => $_POST['nota_sistem'],
	        'm2' => $_POST['tgl_pembelian'],
            'm3' => $_POST['lokasi_beli'],
            'm4' => $_POST['jenis_transaksi'],
            'm5' => $_POST['supplier'],
            'm6' => $_POST['nota_supplier'],
            'm7' => $_POST['no_po'],
            'm8' => $_POST['tgl_jatuh_tempo'],
            'm9' => $this->user,
            'm10' => $_POST['discount_harga'],
            'm11' => $_POST['ppn'],
            'm14' => $_POST['nama_penerima']
        );

        if ($param == 'edit') {
            $get_tmp_m = $this->m_crud->get_data("tr_temp_m", "m12", "m9='".$this->user."' AND m13='edit'");
            $data['m12'] = $get_tmp_m['m12'];
            $data['m13'] = 'edit';
            $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "m13='edit' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
        } else {
            $data['m13'] = 'add';
            $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "m13='add' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
        }

        if ($cek_data == 1) {
            if ($param == 'edit') {
                $this->m_crud->update_data("tr_temp_m", $data, "m13='edit' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
                $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['nota_sistem']), "d19='edit' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')");
            } else {
                $this->m_crud->update_data("tr_temp_m", $data, "m13='add' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
                $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['nota_sistem']), "d19='add' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')");
            }
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }
    }

    public function get_tr_temp_m($tmp_param=null) {
        $param = base64_decode($tmp_param);
        if ($param == 'edit') {
            $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m13='edit' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
        } else {
            $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m13='add' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
        }

        echo json_encode($get_data);
    }

    public function update_tr_temp_m($tmp_column, $tmp_data, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $column = base64_decode($tmp_column);
        $data = base64_decode($tmp_data);

        if ($param == 'edit') {
            $this->m_crud->update_data("tr_temp_m", array($column => $data), "m13='edit' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
        } else {
            $this->m_crud->update_data("tr_temp_m", array($column => $data), "(m13='add' AND m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
        }
    }

    public function get_tr_temp_d($tmp_param = null) {
        $param = base64_decode($tmp_param);
        $list_barang = '';
        if ($param == 'edit') {
            $get_data = $this->m_crud->get_data("tr_temp_m", "m10, m11", "m13='edit' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d19='edit' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')", "CONVERT(INTEGER, d21) ASC");
        } else {
            $get_data = $this->m_crud->get_data("tr_temp_m", "m10, m11", "m13='add' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d19='add' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')", "CONVERT(INTEGER, d21) ASC");
        }

        $no_ = 1;
        $col = 0;
        $sub_total = 0;
        $length = count($read_data);
        /*
         <td data-priority="0">' . $row['d4'] . '</td>
        <td data-priority="0"><input type="number" id="konversi' . $no . '" name="konversi' . $no . '" class="form-control width-uang" value="" readonly></td>
        */
        foreach ($read_data as $row) {
            $no = $row['d21'];
            $jumlah_beli = $row['d5'] * $row['d15'];
            $diskon = $this->m_website->double_diskon($jumlah_beli, array($row['d10'], $row['d11']));
            $hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $row['d14']);
            $sub_total = $sub_total + $hitung_sub_total;
            $list_barang .= '<tr>
                                <td>' . $no_ . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d16'] . '\')" class="btn btn-primary btn-sm"><i class="md md-close"></i></button></td>
                                <td data-priority="1">' . $row['d2'] . '</td>
                                <td data-priority="1">' . $row['d16'] . '</td>
                                <td data-priority="1">' . $row['d3'] . '</td>
                                <td data-priority="1">' . $row['d20'] . '</td>                                
                                <!--hrg beli d5--><td><input onblur="update_tmp_detail(\'' . $row['d16'] . '\', \'d5\', $(this).val())" onkeydown="return isNumber(event)" onkeyup="hitung_barang(\'d5\', \'' . $no . '\', $(this).val(), '.$length.'); isMoney(\'d5' . $no . '\', \'+\'); return to_col(event, '.$no.', \'d10\');" onfocus="this.select()" type="text" id="d5' . $no . '" name="d5' . $row['d5'] . '" class="form-control width-uang input-sm" value="' . number_format((float)$row['d5'], 2, '.', ',') . '"></td>
                                <!--margin d13--><td><input onblur="hitung_margin($(\'#netto'.$no.'\').val(), \'d13'.$no.'\', \'persen\', '.$no.', \'' . $row['d16'] . '\'); update_tmp_detail(\'' . $row['d16'] . '\', \'d13\', $(this).val())" onkeyup="return to_col(event, '.$no.', \'d6\');" type="number" id="d13' . $no . '" name="d13' . $no . '" class="form-control width-diskon input-sm" onfocus="this.select()" value="' . round($row['d13'], 2) . '"></td>
                                <!--hrg jual d6--><td><input onblur="hitung_margin($(\'#netto'.$no.'\').val(), $(this).val(), \'uang\', '.$no.', \'' . $row['d16'] . '\'); update_tmp_detail(\'' . $row['d16'] . '\', \'d6\', $(this).val())" onkeydown="return isNumber(event)" onkeyup="isMoney(\'d6' . $no . '\', \'+\'); return to_barcode(event)" type="text" id="d6' . $no . '" name="d6' . $no . '" class="form-control width-uang input-sm" onfocus="this.select()" value="' . number_format((float)$row['d6'], 2, '.', ',') . '"></td>
                                <input onblur="update_tmp_detail(\'' . $row['d16'] . '\', \'d7\', $(this).val())" type="hidden" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d7'], 2, '.', '') . '">
                                <input onblur="update_tmp_detail(\'' . $row['d16'] . '\', \'d8\', $(this).val())" type="hidden" id="d8' . $no . '" name="d8' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d8'], 2, '.', '') . '">
                                <input onblur="update_tmp_detail(\'' . $row['d16'] . '\', \'d9\', $(this).val())" type="hidden" id="d9' . $no . '" name="d9' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d9'], 2, '.', '') . '">
                                <!--diskon 1 d10--><td><input onblur="update_tmp_detail(\'' . $row['d16'] . '\', \'d10\', $(this).val())" onkeyup="hitung_barang(\'d10\', \'' . $no . '\', $(this).val(), '.$length.'); return to_col(event, '.$no.', \'d11\');" type="number" id="d10' . $no . '" name="d10' . $no . '" class="form-control width-diskon input-sm" onfocus="this.select()" value="' . ($row['d10'] + 0) . '"></td>
                                <!--diskon 2 d11--><td><input onblur="update_tmp_detail(\'' . $row['d16'] . '\', \'d11\', $(this).val())" onkeyup="hitung_barang(\'d11\', \'' . $no . '\', $(this).val(), '.$length.'); return to_col(event, '.$no.', \'d14\');" type="number" id="d11' . $no . '" name="d11' . $no . '" class="form-control width-diskon input-sm" onfocus="this.select()" value="' . ($row['d11'] + 0) . '"></td>
                                <input onblur="update_tmp_detail(\'' . $row['d16'] . '\', \'d12\', $(this).val())" onkeyup="hitung_barang(\'d12\', \'' . $no . '\', $(this).val(), '.$length.')" type="hidden" id="d12' . $no . '" name="d12' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d12'], 2, '.', '') . '">
                                
                                <!--ppn d14--><td><input onblur="update_tmp_detail(\'' . $row['d16'] . '\', \'d14\', $(this).val())" onkeyup="hitung_barang(\'d14\', \'' . $no . '\', $(this).val(), '.$length.'); return to_col(event, '.$no.', \'d13\');" onfocus="this.select()" type="number" id="d14' . $no . '" name="d14' . $no . '" class="form-control width-diskon input-sm" value="' . ($row['d14'] + 0) . '"></td>
                                <!--jumlah d15--><td><input onblur="update_tmp_detail(\'' . $row['d16'] . '\', \'d15\', $(this).val())" onkeyup="hitung_barang(\'d15\', \'' . $no . '\', $(this).val(), '.$length.'); return to_col(event, '.$no.', \'d5\');" onfocus="this.select()" type="number" id="d15' . $no . '" name="d15' . $no . '" class="form-control width-diskon input-sm" value="' . ($row['d15'] + 0) . '"></td>
                                <!--bonus d23--><td><input onblur="update_tmp_detail(\'' . $row['d16'] . '\', \'d23\', $(this).val())" onkeyup="return to_barcode(event);" onfocus="this.select()" type="number" id="d23' . $no . '" name="d23' . $no . '" class="form-control width-diskon input-sm" value="' . ($row['d23'] + 0) . '"></td>
                                <td><input type="text" id="sub_total' . $no . '" name="sub_total' . $no . '" class="form-control width-uang input-sm" value="'.number_format((float)$hitung_sub_total, 2, '.', ',').'" readonly></td>
                                <td><input type="text" id="netto' . $no . '" name="netto' . $no . '" class="form-control width-uang input-sm"  value="' . number_format((float)$row['d22'], 2, '.', ',') . '" readonly></td>
                                <input type="hidden" id="d22' . $no . '" name="d22' . $no . '" value="' . ($row['d22'] + 0) . '">
                                <input type="hidden" id="d16' . $no . '" name="d16' . $no . '" value="' . $row['d16'] . '">
                            </tr>';
            $col = $no;
            $no_++;
            /*<input onchange="update_tmp_detail(\'' . $row['d16'] . '\', \'d13\', $(this).val())" onkeyup="hitung_barang(\'d13\', \'' . $no . '\', $(this).val(), '.$length.')" type="hidden" id="d13' . $no . '" name="d13' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d13'], 2, '.', '') . '">*/
        }
        $list_barang .= '<input type="hidden" id="col" value="'.$col.'">';

        $total = $this->m_website->hitung_total(($sub_total-$get_data['m10']), 0, $get_data['m11']);
        $discount_harga = $this->m_website->diskon($sub_total, $get_data['m10']);
        $total_return = 0;
        $grand_total = $total - $total_return;
        $jumlah = array(
            'sub_total' => $sub_total,
            'discount_persen' => 0,
            'discount_harga' => (float)$get_data['m10'],
            'pajak' => (float)$get_data['m11'],
            'total' => $total,
            'grand_total' => $grand_total
        );
		
        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang, 'jumlah' => $jumlah));
    }

    public function insert_tr_temp_d($nota_sistem, $get_barang, $barcode, $param=null, $qty=1) {

        $data = array(
            'd1' => $nota_sistem,
            'd2' => $get_barang['kd_brg'],
            'd3' => $get_barang['nm_brg'],
            'd4' => $get_barang['satuan'],
            'd5' => $get_barang['hrg_beli'],
            'd6' => $get_barang['hrg_jual_1'],
            'd7' => 0,
            'd8' => 0,
            'd9' => 0,
            'd10' => $get_barang['disc1'],
            'd11' => $get_barang['disc2'],
            'd12' => 0,
            'd13' => 0,
            'd14' => $get_barang['ppn'],
            'd15' => $qty,
            'd16' => $barcode,
            'd17' => $this->user,
            'd20' => $get_barang['Deskripsi'],
            'd22' => $get_barang['hrg_beli'],
            'd23' => 0,
			'd24' => $get_barang['kd_packing']
        );

        if ($param == 'edit') {
            $get_tmp_d = $this->m_crud->get_data("tr_temp_d", "d18", "d17='".$this->user."' AND d19='edit'");
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d21)) id", "d17='".$this->user."' AND d19='edit'");
            $data['d18'] = $get_tmp_d['d18'];
            $data['d19'] = 'edit';
            $data['d21'] = ((int)$get_max_id['id']+1);
        } else {
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d21)) id", "d17='".$this->user."' AND d19='add'");
            $data['d19'] = 'add';
            $data['d21'] = ((int)$get_max_id['id']+1);
        }

        $this->m_crud->create_data("tr_temp_d", $data);
    }

    public function update_tr_temp_d($tmp_barcode, $tmp_column, $tmp_value, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        if ($param == 'edit') {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "d19='edit' AND (SUBSTRING(d1,1,2) = 'BL') AND (d16 = '".$barcode."') AND (d17 = '".$this->user."')");
        } else {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "d19='add' AND (SUBSTRING(d1,1,2) = 'BL') AND (d16 = '".$barcode."') AND (d17 = '".$this->user."')");
        }
    }

    public function delete_tr_temp_d($tmp_barcode, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $barcode = base64_decode($tmp_barcode);

        if ($param == 'edit') {
            /*$get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d15", "d19='edit' AND (SUBSTRING(d1,1,2) = 'BL') AND (d16 = '".$barcode."') AND (d17 = '".$this->user."')");

            if ($get_tmp_data['d15'] > 1) {
                $data = array(
                    'd15' => (int)$get_tmp_data['d15'] - 1
                );

                $this->m_crud->update_data("tr_temp_d", $data, "d19='edit' AND (d17 = '".$this->user."') AND (d16 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'BL')");
            }else {
                $this->m_crud->delete_data("tr_temp_d", "d19='edit' AND (d17 = '".$this->user."') AND (d16 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'BL')");
            }*/

            $this->m_crud->delete_data("tr_temp_d", "d19='edit' AND (d17 = '".$this->user."') AND (d16 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'BL')");

        } else {
            /*$get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d15", "d19='add' AND (SUBSTRING(d1,1,2) = 'BL') AND (d16 = '".$barcode."') AND (d17 = '".$this->user."')");

            if ($get_tmp_data['d15'] > 1) {
                $data = array(
                    'd15' => (int)$get_tmp_data['d15'] - 1
                );

                $this->m_crud->update_data("tr_temp_d", $data, "d19='add' AND (d17 = '".$this->user."') AND (d16 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'BL')");
            }else {
                $this->m_crud->delete_data("tr_temp_d", "d19='add' AND (d17 = '".$this->user."') AND (d16 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'BL')");
            }*/

            $this->m_crud->delete_data("tr_temp_d", "d19='add' AND (d17 = '".$this->user."') AND (d16 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'BL')");

        }

        echo true;
    }

    public function get_no_po($tmp_supplier) {
        $supplier = base64_decode($tmp_supplier);

        $read_po = $this->m_crud->read_data("Master_PO", "no_po", "kode_supplier = '".$supplier."' AND status = 0");
        $list_po = '<option value="-">Pilih</option>';

        foreach ($read_po as $row) {
            $list_po .= '<option value="'.$row['no_po'].'">'.$row['no_po'].'</option>';
        }

        echo $list_po;
    }

    public function add_po_list_barang($tmp_nota_sistem, $tmp_no_po, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $nota_sistem = base64_decode($tmp_nota_sistem);
        $no_po = base64_decode($tmp_no_po);
		$po = $this->m_crud->get_data("master_po", "jenis, jenis_po", "no_po = '".$no_po."'");
        if($po['jenis_po']=='PO'){
			$list_po = $this->m_crud->read_data("Detail_PO, barang", "Detail_PO.kode_barang, barang.barcode", "barang.kd_brg = Detail_PO.kode_barang AND Detail_PO.no_po = '".$no_po."'");
		} else if($po['jenis_po']=='POC'){
			$list_po = $this->m_crud->read_data("detail_po_cabang dpc, barang br", "dpc.kd_brg kode_barang, br.barcode", "br.kd_brg = dpc.kd_brg AND dpc.no_po = '".$no_po."'");
		}
		
        if ($param == 'edit') {
            for ($i = 0; $i < count($list_po); $i++) {
                $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d15, d16", "d19='edit' AND (SUBSTRING(d1,1,2) = 'BL') AND (d16 = '" . $list_po[$i]['barcode'] . "') AND (d17 = '" . $this->user . "')");
                if($po['jenis_po']=='PO'){
					$get_barang = $this->m_crud->get_data("Detail_PO, barang", "Detail_PO.kode_barang kd_brg, Detail_PO.harga_beli hrg_beli, Detail_PO.harga_jual hrg_jual_1, 0 hrg_jual_2, 0 hrg_jual_3, 0 hrg_jual_4, Detail_PO.PPN ppn, Detail_PO.jumlah_beli, Detail_PO.diskon, barang.nm_brg, barang.Deskripsi, barang.satuan, barang.barcode", "barang.kd_brg = Detail_PO.kode_barang AND Detail_PO.no_po = '" . $no_po . "' AND Detail_PO.kode_barang = '" . $list_po[$i]['kode_barang'] . "'");
				} else if($po['jenis_po']=='POC'){
					$get_barang = $this->m_crud->get_data("detail_po_cabang dpc, barang br", "dpc.kd_brg, dpc.harga_beli hrg_beli, dpc.harga_jual hrg_jual_1, 0 hrg_jual_2, 0 hrg_jual_3, 0 hrg_jual_4, dpc.ppn, (isnull(qty_ho,0)+isnull(qty_buffer,0)+(isnull((select sum(dqpc.qty) from detail_qty_po_cabang dqpc where dqpc.no_po = '".$no_po."' and dqpc.kd_brg = '".$list_po[$i]['kode_barang']."'),0))) jumlah_beli, dpc.diskon, br.nm_brg, br.Deskripsi, br.satuan, br.barcode", "br.kd_brg = dpc.kd_brg AND dpc.no_po = '" . $no_po . "' AND dpc.kd_brg = '" . $list_po[$i]['kode_barang'] . "'");
				}
				
                if ($cek_tr_temp_d == '') {
                    $this->insert_tr_temp_d($nota_sistem, $get_barang, $list_po[$i]['barcode'], base64_encode('edit'), $get_barang['jumlah_beli']);
                } else {
                    $this->update_tr_temp_d(base64_encode($cek_tr_temp_d['d16']), base64_encode('d15'), base64_encode($cek_tr_temp_d['d15'] + $get_barang['jumlah_beli']), base64_encode('edit'));
                }
            }
        } else {
            for ($i = 0; $i < count($list_po); $i++) {
                $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d15, d16", "d19='add' AND (SUBSTRING(d1,1,2) = 'BL') AND (d16 = '" . $list_po[$i]['barcode'] . "') AND (d17 = '" . $this->user . "')");
                if($po['jenis_po']=='PO'){
					$get_barang = $this->m_crud->get_data("Detail_PO, barang", "Detail_PO.kode_barang kd_brg, Detail_PO.harga_beli hrg_beli, Detail_PO.harga_jual hrg_jual_1, 0 hrg_jual_2, 0 hrg_jual_3, 0 hrg_jual_4, Detail_PO.PPN ppn, Detail_PO.jumlah_beli, Detail_PO.diskon, barang.nm_brg, barang.Deskripsi, barang.satuan, barang.barcode", "barang.kd_brg = Detail_PO.kode_barang AND Detail_PO.no_po = '" . $no_po . "' AND Detail_PO.kode_barang = '" . $list_po[$i]['kode_barang'] . "'");
				} else if($po['jenis_po']=='POC'){
					$get_barang = $this->m_crud->get_data("detail_po_cabang dpc, barang br", "dpc.kd_brg, dpc.harga_beli hrg_beli, dpc.harga_jual hrg_jual_1, 0 hrg_jual_2, 0 hrg_jual_3, 0 hrg_jual_4, dpc.ppn, (isnull(qty_ho,0)+isnull(qty_buffer,0)+(isnull((select sum(dqpc.qty) from detail_qty_po_cabang dqpc where dqpc.no_po = '".$no_po."' and dqpc.kd_brg = '".$list_po[$i]['kode_barang']."'),0))) jumlah_beli, dpc.diskon, br.nm_brg, br.Deskripsi, br.satuan, br.barcode", "br.kd_brg = dpc.kd_brg AND dpc.no_po = '" . $no_po . "' AND dpc.kd_brg = '" . $list_po[$i]['kode_barang'] . "'");
				}
				
				if ($cek_tr_temp_d == '') {
                    $this->insert_tr_temp_d($nota_sistem, $get_barang, $list_po[$i]['barcode'], base64_encode('add'), $get_barang['jumlah_beli']);
                } else {
                    $this->update_tr_temp_d(base64_encode($cek_tr_temp_d['d16']), base64_encode('d15'), base64_encode($cek_tr_temp_d['d15'] + $get_barang['jumlah_beli']));
                }
            }
        }

        //echo true;
		echo json_encode(array('status'=>true, 'po'=>$po));
    }

    public function add_list_barang() {
        $param = $_POST['param_'];
        $nota_sistem = $_POST['nota_sistem_'];
        $list_barcode = $_POST['list_'];

        if ($param == 'edit') {
            for ($i = 0; $i < count($list_barcode); $i++) {
                $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d15, d16", "d19='edit' AND (SUBSTRING(d1,1,2) = 'BL') AND (d16 = '".$list_barcode[$i]."') AND (d17 = '".$this->user."')");

                if ($cek_tr_temp_d == '') {
                    $get_barang = $this->m_crud->get_data("barang", "kd_brg, nm_brg, Deskripsi, satuan, hrg_beli, hrg_jual_1, hrg_jual_2, hrg_jual_3, hrg_jual_4, ppn", "barcode = '".$list_barcode[$i]."'");
                    $this->insert_tr_temp_d($nota_sistem, $get_barang, $list_barcode[$i], base64_encode('edit'));
                } else {
                    $this->update_tr_temp_d(base64_encode($cek_tr_temp_d['d16']), base64_encode('d15'), base64_encode($cek_tr_temp_d['d15'] + 1), base64_encode('edit'));
                }
            }
        } else {
            for ($i = 0; $i < count($list_barcode); $i++) {
                $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d15, d16", "d19='add' AND (SUBSTRING(d1,1,2) = 'BL') AND (d16 = '".$list_barcode[$i]."') AND (d17 = '".$this->user."')");

                if ($cek_tr_temp_d == '') {
                    $get_barang = $this->m_crud->get_data("barang", "kd_brg, nm_brg, satuan, Deskripsi, hrg_beli, hrg_jual_1, hrg_jual_2, hrg_jual_3, hrg_jual_4, ppn", "barcode = '".$list_barcode[$i]."'");
                    $this->insert_tr_temp_d($nota_sistem, $get_barang, $list_barcode[$i]);
                } else {
                    $this->update_tr_temp_d(base64_encode($cek_tr_temp_d['d16']), base64_encode('d15'), base64_encode($cek_tr_temp_d['d15'] + 1));
                }
            }
        }

        echo true;
    }

    public function get_list_barang() {
        $read_barang = $this->m_crud->read_data("barang br, det_beli db, master_beli mb", "br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi", "db.no_faktur_beli=mb.no_faktur_beli AND db.kode_barang=br.kd_brg AND mb.kode_supplier = '".$_POST['supplier_']."'", "br.kd_brg", "br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi", 100);
        $list_barang = '';

        foreach ($read_barang as $row) {
            $list_barang .= '<tr>
                                <td class="text-center td_check"><label class="label_check"><input type="checkbox" id="barang" name="barang" value="'.$row['barcode'].'"></label></td>
                                <td>'.$row['kd_brg'].'</td>
                                <td>'.$row['barcode'].'</td>
                                <td>'.$row['nm_brg'].'</td>
                                <td>'.$row['Deskripsi'].'</td>
                             </tr>';
        }

        echo json_encode(array('list_barang' => $list_barang, 'supplier' => $_POST['supplier_']));
    }

    public function get_barang($tmp_nota_sistem, $tmp_barcode, $tmp_lokasi_beli, $tmp_supplier, $tmp_cat_cari, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $cat_cari = base64_decode($tmp_cat_cari);
        $nota_sistem = base64_decode($tmp_nota_sistem);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi_beli));
        $lokasi_beli = $explode_lokasi[0];
        $supplier = base64_decode($tmp_supplier);

		/*		
	   	'd1' => $get_data_pembelian['no_faktur_beli'],
		'd2' => $get_barang['kode_barang'],
		'd3' => $get_barang['nm_brg'],
		'd4' => $get_barang['satuan'],
		'd5' => $get_barang['harga_beli'],
		'd6' => $get_barang['hrg_jual_1'],
		'd7' => $get_barang['hrg_jual_2'],
		'd8' => $get_barang['hrg_jual_3'],
		'd9' => $get_barang['hrg_jual_4'],
		'd10' => $get_barang['diskon'],
		'd11' => $get_barang['disc2'],
		'd12' => 0,
		'd13' => 0,
		'd14' => $get_barang['PPN'],
		'd15' => $get_barang['jumlah_beli'],
		'd16' => $get_barang['barcode'],
		'd17' => $this->user,
		'd18' => $get_data_pembelian['no_faktur_beli'],
		'd19' => 'edit',
		'd20' => $get_barang['Deskripsi'],
		'd21' => $id++,
		'd22' => $get_barang['harga_beli'],
		'd23' => $get_barang['jumlah_bonus']*/
		
		$qty = 1;
		
        if ($cat_cari == 1) {
            $col_barang = 'barang.kd_brg';
            $col_tmp = 'd2';
        } else if ($cat_cari == 2) {
            $col_barang = 'barang.barcode';
            $col_tmp = 'd16';
        } else if ($cat_cari == 3) {
            $col_barang = 'barang.Deskripsi';
            $col_tmp = 'd20';
        } else if ($cat_cari == 4){
			$col_barang = 'barang.kd_packing';
            $col_tmp = 'd24';
			
			$qty = $this->m_crud->get_data('barang', 'qty_packing', "kd_packing = '".$barcode."'")['qty_packing'];
			if($qty==null || $qty==0){ $qty=1; }
		}
		
        if ($param == 'edit') {
            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d15, d21", "d19='edit' AND (SUBSTRING(d1,1,2) = 'BL') AND (".$col_tmp." = '".$barcode."') AND (d17 = '".$this->user."')");
        } else {
            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d15, d21", "d19='add' AND (SUBSTRING(d1,1,2) = 'BL') AND (".$col_tmp." = '".$barcode."') AND (d17 = '".$this->user."')");
        }

        if ($get_tmp_data != '') {
            $data = array(
                'd15' => (int)$get_tmp_data['d15'] + $qty
            );

            if ($param == 'edit') {
                $this->m_crud->update_data("tr_temp_d", $data, "(d17 = '" . $this->user . "') AND (" . $col_tmp . " = '" . $barcode . "') AND (SUBSTRING(d1,1,2) = 'BL') AND d19 = 'edit'");
            } else {
                $this->m_crud->update_data("tr_temp_d", $data, "(d17 = '" . $this->user . "') AND (" . $col_tmp . " = '" . $barcode . "') AND (SUBSTRING(d1,1,2) = 'BL') AND d19 = 'add'");
            }
            echo json_encode(array('status' => 1, 'barang' => 'tersedia', 'col' => $get_tmp_data['d21']));
        }else {
            //$get_barang = $this->m_crud->get_data("barang, barang_hrg", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, barang_hrg.hrg_jual_1, barang.Deskripsi, barang_hrg.hrg_jual_2, barang_hrg.hrg_jual_3, barang_hrg.hrg_jual_4, barang_hrg.ppn", "(barang.kd_brg = barang_hrg.barang) AND (barang_hrg.lokasi = '".$lokasi_beli."') AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
            $get_barang = $this->m_crud->get_data("Det_Beli db, barang", "barang.kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_jual_1, db.diskon disc1, db.disc2, db.PPN ppn, db.harga_beli hrg_beli", "barang.Group1 = '".$supplier."' AND kode_barang=barang.kd_brg AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')", "tgl_beli DESC", null, 1);
            if ($get_barang != '') {
                if ($param == 'edit') {
                    $this->insert_tr_temp_d($nota_sistem, $get_barang, $get_barang['barcode'], 'edit', $qty);
                } else {
                    $this->insert_tr_temp_d($nota_sistem, $get_barang, $get_barang['barcode'], null, $qty);
                }
                echo json_encode(array('status' => 1, 'barang' => 'baru'));
            }else {
                $get_barang = $this->m_crud->get_data("barang", "kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, barang.hrg_jual_1, barang.Deskripsi, barang.hrg_jual_2, barang.hrg_jual_3, barang.hrg_jual_4, barang.ppn, 0 disc2, 0 disc2", "barang.Group1='".$supplier."' AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                //$get_barang = $this->m_crud->get_data("barang, barang_hrg", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, barang_hrg.hrg_jual_1, barang.Deskripsi, barang_hrg.hrg_jual_2, barang_hrg.hrg_jual_3, barang_hrg.hrg_jual_4, barang_hrg.ppn, 0 disc1, 0 disc2", "(barang.kd_brg = barang_hrg.barang) AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                if ($get_barang != '') {
                    if ($param == 'edit') {
                        $this->insert_tr_temp_d($nota_sistem, $get_barang, $get_barang['barcode'], 'edit', $qty);
                    } else {
                        $this->insert_tr_temp_d($nota_sistem, $get_barang, $get_barang['barcode'], null, $qty);
                    }
                    echo json_encode(array('status' => 1, 'barang' => 'baru'));
                }else {
                    $get_barang = $this->m_crud->get_data("barang", "kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, barang.hrg_jual_1, barang.Deskripsi, barang.hrg_jual_2, barang.hrg_jual_3, barang.hrg_jual_4, barang.ppn, 0 disc2, 0 disc2", "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                    if ($get_barang != '') {
                        /*if ($param == 'edit') {
                            $this->insert_tr_temp_d($nota_sistem, $get_barang, $get_barang['barcode'], 'edit');
                        } else {
                            $this->insert_tr_temp_d($nota_sistem, $get_barang, $get_barang['barcode']);
                        }*/
                        //echo json_encode(array('status' => 1, 'barang' => 'baru'));
                        echo json_encode(array('status' => 2, 'notif' => "Barang dari supplier ".$supplier." tidak tersedia!"));
                    }else {
                        echo json_encode(array('status' => 3, 'notif' => "Barang tidak tersedia!"));
                    }
                }
            }
        }
    }

    public function delete_trans_pembelian($tmp_param=null) {
        $param = base64_decode($tmp_param);

        if ($param == 'edit') {
            $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "m13='edit' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
            $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "d19='edit' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')");
        } else {
            $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "m13='add' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
            $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "d19='add' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')");
        }

        if ($delete_data_master && $delete_data_detail) {
            echo true;
        }

        $this->m_crud->delete_data("master_beli", "no_faktur_beli not in (select no_faktur_beli from det_beli)");
    }

    public function get_sub_total($param=null) {
        if ($param == 'edit') {
            $read_data = $this->m_crud->read_data("tr_temp_d", "d5, d10, d11, d12, d13, d14, d15", "d19='edit' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')", "d2");
        } else {
            $read_data = $this->m_crud->read_data("tr_temp_d", "d5, d10, d11, d12, d13, d14, d15", "d19='add' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')", "d2");
        }
        $sub_total = 0;
        foreach ($read_data as $row) {
            $hitung_netto = $row['d5'] * $row['d15'];
            $diskon = $this->m_website->double_diskon($hitung_netto, array($row['d10'], $row['d11'], $row['d12'], $row['d13']));
            $hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $row['d14']);
            $sub_total = $sub_total + $hitung_sub_total;
        }

        return $sub_total;
    }

    public function trans_pembelian_x() {
        $param = $_POST['param'];
        $tgl_pembelian = $_POST['tgl_pembelian'];
        $jns_trx = $_POST['jenis_transaksi'];
        if ($jns_trx == "Kredit") {
            $tgl_tempo = $_POST['tgl_jatuh_tempo'];
            $pelunasan = 'Belum Lunas';
        } else {
            $tgl_tempo = null;
            $pelunasan = 'Lunas';
        }
        if ($_POST['no_po'] == '' || $_POST['no_po'] == null) {
            $no_po = '-';
        } else {
            $no_po = $_POST['no_po'];
        }
        $sub_total = (float)str_replace(',', '', $_POST['sub_total']);
        $supplier = $_POST['supplier'];
        $nota_supplier = $_POST['nota_supplier'];
        $penerima = $_POST['nama_penerima'];
        $disc = (float)str_replace(',', '', $_POST['discount_harga']);
        $ppn = $_POST['pajak'];
        $explode_lokasi = explode('|', $_POST['lokasi_beli']);
        $lokasi = $explode_lokasi[0];
        $serial = $explode_lokasi[1];

        if ($param == 'edit') {
            $nota_sistem = $_POST['nota_sistem'];
            $get_kode = $this->m_crud->get_data("tr_temp_m", "m1, m12", "m9 = '".$this->user."' AND (LEFT(m12, 2) = '".substr($nota_sistem, 0, 2)."') AND (SUBSTRING(m12, 4, 6) = '".substr($nota_sistem, 3, 6)."') AND (RIGHT(m12, 1) = '".substr($nota_sistem, 14, 1)."') AND m13 = 'edit'");
            if ($get_kode == '') {
                $nota_sistem = $this->m_website->generate_kode("BL", $serial, substr(str_replace('-', '', $tgl_pembelian), 2));
            } else {
                $nota_sistem = $get_kode['m12'];
            }
        } else {
            $nota_sistem = $this->m_website->generate_kode("BL", $serial, substr(str_replace('-', '', $tgl_pembelian), 2));
        }

        $this->db->trans_begin();

        if ($param == 'edit') {
            $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "m13='edit' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d19='edit' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')", "CONVERT(INTEGER, d21) ASC");

            $this->m_crud->delete_data("master_beli", "no_faktur_beli='".$get_temp_m['m12']."'");
            $this->m_crud->delete_data("det_beli", "no_faktur_beli='".$get_temp_m['m12']."'");
            $this->m_crud->delete_data("Kartu_stock", "kd_trx='".$get_temp_m['m12']."'");
			$no_po=$get_temp_m['m7'];
            //$sub_total = $this->get_sub_total('edit');
        } else {
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d19='add' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')", "CONVERT(INTEGER, d21) ASC");
            //$sub_total = $this->get_sub_total('add');
        }

        $hitung_ppn = ($sub_total-$disc)*($ppn/100);

        $data_pembelian = array(
            'tgl_beli' => $tgl_pembelian,
            'no_faktur_beli' => $nota_sistem,
            'type' => $jns_trx,
            'nilai_pembelian' => $sub_total,
            'kode_supplier' => $supplier,
            'tgl_jatuh_tempo' => $tgl_tempo,
            'PPN' => $hitung_ppn,
            'DP' => 0,
            'total_pembelian' => $sub_total-$disc+$hitung_ppn,
            'Pelunasan' => $pelunasan,
            'terbayar' => 0,
            'Operator' => $this->user,
            'Lokasi' => $lokasi,
            'bulat' => 0,
            'noNota' => $nota_supplier,
            'no_po' => $no_po,
            'catatan' => '-',
            'disc' => $disc,
            'nama_penerima' => $penerima
        );
        $this->m_crud->create_data("master_beli", $data_pembelian);

        if ($no_po != '-') {
            $this->m_crud->update_data("Master_PO", array('status' => 1), "no_po = '".$no_po."'");
        }

        $det_log = array();
        foreach ($read_temp_d as $row) {
            $data_detail_pembelian = array(
                'no_faktur_beli' => $nota_sistem,
                'tgl_beli' => $tgl_pembelian . " " . date("H:i:s"),
                'kode_barang' => $row['d2'],
                'diskon' => ($row['d10']==null)?0:$row['d10'],
                'disc2' => ($row['d11']==null)?0:$row['d11'],
                'disc3' => 0,
                'disc4' => 0,
                'PPN' => $row['d14'],
                'harga_beli' => $row['d5'],
                'jumlah_beli' => $row['d15'],
                'jumlah_retur' => 0,
                'sisa' => $row['d15'],
                'jam' => $tgl_pembelian . " " . date("H:i:s"),
                'jumlah_bonus' => $row['d23']
            );
            $this->m_crud->create_data("det_beli", $data_detail_pembelian);
            array_push($det_log, $data_detail_pembelian);

            $data_kartu_stok = array(
                'kd_trx' => $nota_sistem,
                'tgl' => $tgl_pembelian . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => $row['d15']+$row['d23'],
                'stock_out' => 0,
                'lokasi' => $lokasi,
                'keterangan' => 'Pembelian',
                'hrg_beli' => $row['d5']
            );

            /*$check_opname = $this->m_crud->count_data("Kartu_stock", "kd_trx", "kd_brg='".$row['d2']."' AND keterangan='Penyesuaian' AND tgl>='".$tgl_pembelian."'");
            if ($check_opname < 1) {
                $this->m_crud->create_data("Kartu_stock", $data_kartu_stok);
            }*/
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok);

            $hrg_lama = $this->m_crud->get_data("barang", "hrg_jual_1", "kd_brg = '".$row['d2']."'");

            $update_barang = array(
                'hrg_beli' => $row['d22'],
                'hrg_jual_1' => (int)$row['d6'],
                'tgl_update' => date('Y-m-d H:i:s')
            );

            if ($hrg_lama['hrg_jual_1'] != $row['d6']) {
                $update_barang['hrg_sebelum'] = $hrg_lama['hrg_jual_1'];
            }

            $this->m_crud->update_data("barang", $update_barang, "(kd_brg = '".$row['d2']."')");

            $this->m_crud->delete_data("barang_hrg", "barang='".$row['d2']."'");
            $get_lokasi = $this->m_crud->read_data("Lokasi", "Kode");
            foreach ($get_lokasi as $row_lokasi) {
                $data_barang_hrg = array(
                    'barang' => $row['d2'],
                    'hrg_jual_1' => (int)$row['d6'],
                    'hrg_jual_2' => (int)$row['d7'],
                    'hrg_jual_3' => (int)$row['d8'],
                    'hrg_jual_4' => (int)$row['d9'],
                    'lokasi' => $row_lokasi['Kode']
                );
                $this->m_crud->create_data("barang_hrg", $data_barang_hrg);
            }
        }

        if ($param == 'edit') {
            $data_pembelian['trx_old'] = $get_temp_m['m12'];
        }

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$nota_sistem,'jenis'=>ucfirst($param),'transaksi'=>'Pembelian'), array('master'=>$data_pembelian,'detail'=>$det_log));

        $this->delete_trans_pembelian(base64_encode($param));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo '0';
        } else {
            $this->db->trans_commit();
            echo $nota_sistem;
        }
    }

    /*public function trans_pembelian() {
        $param = $_POST['param_'];
        $nota_sistem = $_POST['nota_sistem_'];
        $get_kode = $this->m_crud->get_data("master_beli", "no_faktur_beli", "(no_faktur_beli = '".$nota_sistem."')");

        if ($get_kode != '' && $param == 'add') {
            $nota_sistem = $this->m_website->generate_kode(substr($get_kode['no_faktur_beli'], 0, 2), substr($get_kode['no_faktur_beli'], 14), substr($get_kode['no_faktur_beli'], 3, 6));
        }

        $this->db->trans_begin();

        if ($param == 'edit') {
            $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "m13='edit' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");;
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d19='edit' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')", "CONVERT(INTEGER, d21) ASC");;

            $this->m_crud->delete_data("master_beli", "no_faktur_beli='".$get_temp_m['m12']."'");
            $this->m_crud->delete_data("det_beli", "no_faktur_beli='".$get_temp_m['m12']."'");
            $this->m_crud->delete_data("Kartu_stock", "kd_trx='".$get_temp_m['m12']."'");
            $sub_total = $this->get_sub_total('edit');
        } else {
            $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "m13='add' AND (m9 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'BL')");;
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d19='add' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'BL')", "CONVERT(INTEGER, d21) ASC");;
            $sub_total = $this->get_sub_total();
        }

        $explode_lokasi = explode('|', $get_temp_m['m3']);
        $lokasi = $explode_lokasi[0];

        $pelunasan = 'Lunas';

        if ($get_temp_m['m4'] == 'Kredit') {
            $pelunasan = 'Belum Lunas';
        }

        $data_pembelian = array(
            'tgl_beli' => $get_temp_m['m2'] . " " . date("H:i:s"),
            'no_faktur_beli' => $nota_sistem,
            'type' => $get_temp_m['m4'],
            'nilai_pembelian' => $sub_total,
            'kode_supplier' => $get_temp_m['m5'],
            'tgl_jatuh_tempo' => ($get_temp_m['m4']=='Kredit')?$get_temp_m['m8']:null,
            'PPN' => $get_temp_m['m11'],
            'DP' => 0,
            'total_pembelian' => (($sub_total - $get_temp_m['m10']) + (($get_temp_m['m11']/100) * $sub_total)),
            'Pelunasan' => $pelunasan,
            'terbayar' => 0,
            'Operator' => $get_temp_m['m9'],
            'Lokasi' => $lokasi,
            'bulat' => 0,
            'noNota' => $get_temp_m['m6'],
            'no_po' => $get_temp_m['m7'],
            'catatan' => '-',
            'disc' => $get_temp_m['m10']
        );
        $this->m_crud->create_data("master_beli", $data_pembelian);

        if ($get_temp_m['m7'] != '-') {
            $this->m_crud->update_data("Master_PO", array('status' => 1), "no_po = '".$get_temp_m['m7']."'");
        }

        foreach ($read_temp_d as $row) {
            $data_detail_pembelian = array(
                'no_faktur_beli' => $nota_sistem,
                'tgl_beli' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kode_barang' => $row['d2'],
                'diskon' => $row['d10'],
                'disc2' => $row['d11'],
                'disc3' => 0,
                'disc4' => 0,
                'PPN' => $row['d14'],
                'harga_beli' => $row['d5'],
                'jumlah_beli' => $row['d15'],
                'jumlah_retur' => 0,
                'sisa' => $row['d15'],
                'jam' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'jumlah_bonus' => $row['d23']
            );
            $this->m_crud->create_data("det_beli", $data_detail_pembelian);

            $data_kartu_stok = array(
                'kd_trx' => $nota_sistem,
                'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => $row['d15']+$row['d23'],
                'stock_out' => 0,
                'lokasi' => $lokasi,
                'keterangan' => 'Pembelian',
                'hrg_beli' => $row['d5']
            );

            $check_opname = $this->m_crud->count_data("Kartu_stock", "kd_trx", "kd_brg='".$row['d2']."' AND keterangan='Penyesuaian' AND tgl>='".$get_temp_m['m2']."'");
            if ($check_opname < 1) {
                $this->m_crud->create_data("Kartu_stock", $data_kartu_stok);
            }

            $this->m_crud->update_data("barang", array('hrg_beli' => $row['d22'], 'hrg_jual_1' => $row['d6']), "(kd_brg = '".$row['d2']."')");

            $this->m_crud->delete_data("barang_hrg", "barang='".$row['d2']."'");
            $get_lokasi = $this->m_crud->read_data("Lokasi", "Kode");
            foreach ($get_lokasi as $row_lokasi) {
                $data_barang_hrg = array(
                    'barang' => $row['d2'],
                    'hrg_jual_1' => $row['d6'],
                    'hrg_jual_2' => $row['d7'],
                    'hrg_jual_3' => $row['d8'],
                    'hrg_jual_4' => $row['d9'],
                    'lokasi' => $row_lokasi['Kode']
                );
                $this->m_crud->create_data("barang_hrg", $data_barang_hrg);
            }
        }

        $this->delete_trans_pembelian();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo '0';
        } else {
            $this->db->trans_commit();
            echo $nota_sistem;
        }
    }*/

    public function trans_return_pembelian() {
        $list_data = $_POST['list_data_'];
        $no_return = $_POST['no_transaksi_'];
        $tgl_return = $_POST['tgl_return_'].' '.date("H:i:s");
        $lokasi = $_POST['lokasi_'];
        $kode_supplier = $_POST['kode_supplier_'];
        $no_pembelian = $_POST['no_pembelian_'];
        $keterangan = $_POST['keterangan_'];

        $no_return = $this->m_website->generate_kode(substr($no_return, 0, 2), substr($no_return, 14), substr($no_return, 3, 6));

        $this->db->trans_begin();

        $total = 0;

        $det_log = array();
        for ($i=0; $i<count($list_data); $i++) {
            $total = $total + $list_data[$i]['nilai_return_'];
            $det_retur = array(
                'No_Retur' => $no_return,
                'kd_brg' => $list_data[$i]['kode_barang_'],
                'jml' => $list_data[$i]['qty_return_'],
                'hrg_beli' => $list_data[$i]['harga_beli_'],
                'keterangan' => $list_data[$i]['keterangan_']
            );

            $this->m_crud->create_data('Det_Retur_Beli', $det_retur);
            array_push($det_log, $det_retur);

            $kartu_stok = array(
                'kd_trx' => $no_return,
                'tgl' => $tgl_return,
                'kd_brg' => $list_data[$i]['kode_barang_'],
                'saldo_awal' => 0,
                'stock_in' => 0,
                'stock_out' => $list_data[$i]['qty_return_'],
                'lokasi' => $lokasi,
                'keterangan' => 'Retur Pembelian',
                'hrg_beli' => $list_data[$i]['harga_beli_']
            );
            $this->m_crud->create_data("Kartu_stock", $kartu_stok);
        }

        $master_retur = array(
            'No_Retur' => $no_return,
            'Tgl' => $tgl_return,
            'kd_kasir' => $this->user,
            'Lokasi' => $lokasi,
            'Supplier' => $kode_supplier,
            'Total' => $total,
            'no_beli' => $no_pembelian,
            'keterangan' => $keterangan
        );

        $this->m_crud->create_data('Master_Retur_Beli', $master_retur);

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$no_return,'jenis'=>ucfirst('Add'),'transaksi'=>'Retur Pembelian'), array('master'=>$master_retur,'detail'=>$det_log));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        } else {
            $this->db->trans_commit();
            echo $no_return;
        }
    }

    /*End modul pembelian barang*/

    /*Start modul po_by_supplier*/
    public function purchase_order_supplier() {
        $this->access_denied(51);
        $data = $this->data;
        $function = 'purchase_order_supplier';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Purchase Order Supplier';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, nama_toko Nama, serial');
        $data['data_supplier'] = $this->m_crud->read_data('Supplier','Kode, Nama');

        $this->load->view('bo/index', $data);
    }
    /*End modul po_by_supplier*/
	
	//start modul po_cabang
	public function po_cabang() {
        $this->access_denied(54);
        $data = $this->data;
        $function = 'po_cabang';
        $view = $this->control . '/';
        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Purchase Order Cabang';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, Nama, serial');
        $data['data_supplier'] = $this->m_crud->read_data('Supplier','Kode, Nama');
		
        $this->load->view('bo/index', $data);
    }
	
	public function add_tr_temp_m_po_cabang() {
        $data = array(
            'm1' => $_POST['nota_po'],
            'm2' => $_POST['tgl_order'],
            'm3' => $_POST['tgl_kirim'],
            'm4' => $_POST['lokasi'],
            'm5' => $_POST['supplier'],
            'm6' => 0,
            'm7' => $_POST['catatan'],
            'm8' => $this->user,
			'm9' => 1
        );

        $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");

        if ($cek_data == 1) {
            $this->m_crud->update_data("tr_temp_m", $data, "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");
            $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['nota_po']), "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')");
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }
    }

    public function get_tr_temp_m_po_cabang() {
        $get_data = $this->m_crud->get_data("tr_temp_m", "*", "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");

        echo json_encode($get_data);
    }

    public function update_tr_temp_m_po_cabang($tmp_column, $tmp_data) {
        $column = base64_decode($tmp_column);
        $data = base64_decode($tmp_data);

        $this->m_crud->update_data("tr_temp_m", array($column => $data), "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");
    }

    public function get_tr_temp_d_po_cabang() {
        if($_POST['supplier']!=null && $_POST['supplier']!=''){ $supplier = $_POST['supplier']; }
		$list_po_cabang = '';
		
		$data_lokasi_order = $this->m_crud->join_data('master_order mo','mo.no_order, mo.lokasi, mro.no_receive_order, mro.no_faktur_mutasi', 
			array('master_receive_order mro'), array("mo.no_order=mro.no_order"), 
			"mo.status = '1' and (select sum(dro.qty) from det_receive_order dro join barang br on dro.kd_brg=br.kd_brg where br.group1='".$supplier."' and dro.no_receive_order=mro.no_receive_order) > 0", 
			null, "mo.no_order, mo.lokasi, mro.no_receive_order, mro.no_faktur_mutasi"
		);
        $data_barang_order = $this->m_crud->join_data('barang br', "rtrim(ltrim(br.kd_brg)) kd_brg, rtrim(ltrim(br.nm_brg)) nm_brg, rtrim(ltrim(br.barcode)) barcode, rtrim(ltrim(br.deskripsi)) deskripsi, br.stock_min, br.hrg_beli, br.hrg_jual_1", 
			array("det_receive_order dro", "master_receive_order mro", "master_order mo", "det_order do"), 
			array("br.kd_brg=dro.kd_brg", "dro.no_receive_order=mro.no_receive_order", "mro.no_order=mo.no_order", "mo.no_order=do.no_order"),
			//"dro.kd_brg = '".$row['kd_brg']."' and mo.lokasi = '".$rows['lokasi']."' and mo.status='1' and dro.kd_brg=do.kd_brg"
			"group1='".$supplier."' and mo.status='1' and dro.kd_brg=do.kd_brg", null, "br.kd_brg, br.nm_brg, br.barcode, br.deskripsi, br.stock_min, br.hrg_beli, br.hrg_jual_1"
		); 
		
		$lokasi_order = '';
		foreach($data_lokasi_order as $row){ 
			$lokasi_order .= '<th>'.$row['lokasi'].'</th>';
		} 
		$list_po_cabang .= '<thead>
								<tr>
									<th style="width: 10px">No</th>
									<th>Kode Barang</th>
									<th>Nama Barang</th>
									<th>Harga Beli</th>
									<th>Harga Jual</th>
									'.$lokasi_order.'
									<th>HO</th>
									<th>Buffer</th>
									<th>Qty</th>
									<th>Sub Total</th>
								</tr>
							</thead>
							<tbody>';
		
		$sub_total=0; 
		$no=0; foreach($data_barang_order as $row){ $no++;
			$i=0; $qty_order = ''; $jumlah_qty=$row['stock_min']; 
			foreach($data_lokasi_order as $rows){ 
				$qty = $this->m_crud->get_join_data('det_receive_order dro', "dro.qty", 
					array("master_receive_order mro", "master_order mo", "det_order do"), 
					array("dro.no_receive_order=mro.no_receive_order", "mro.no_order=mo.no_order", "mo.no_order=do.no_order"),
					"dro.kd_brg = '".$row['kd_brg']."' and mo.lokasi = '".$rows['lokasi']."' and mo.status='1' and dro.kd_brg=do.kd_brg"
				)['qty']+0; $jumlah_qty = $jumlah_qty + $qty; 
				$qty_order .= '<td>'.$qty.'</td>';
				$qty_order .= '<input type="hidden" id="qty'.$no.'_'.$i.'" name="qty'.$no.'_'.$i.'" value="'.$qty.'" />';
				$qty_order .= '<input type="hidden" id="lokasi'.$no.'_'.$i.'" name="lokasi'.$no.'_'.$i.'" value="'.$rows['lokasi'].'" />';
				$qty_order .= '<input type="hidden" id="no_order'.$no.'_'.$i.'" name="no_order'.$no.'_'.$i.'" value="'.$rows['no_order'].'" />';
				$qty_order .= '<input type="hidden" id="no_receive_order'.$no.'_'.$i.'" name="no_receive_order'.$no.'_'.$i.'" value="'.$rows['no_receive_order'].'" />';
				$qty_order .= '<input type="hidden" id="no_faktur_mutasi'.$no.'_'.$i.'" name="no_faktur_mutasi'.$no.'_'.$i.'" value="'.$rows['no_faktur_mutasi'].'" />';
				$i++;
			} 
			$list_po_cabang .= '<tr>
									<input type="hidden" id="kd_brg'.$no.'" name="kd_brg'.$no.'" value="'.$row['kd_brg'].'" />
									<td>'.$no.'</td>
									<td>'.$row['kd_brg'].'</td>
									<td>'.$row['nm_brg'].'</td>
									<td><input type="text" onfocus="this.select()" onkeydown="return isNumber(event);" onkeyup="to_col(event, \'hrg_jual'.$no.'\', \'hrg_beli'.($no+1).'\'); hitung_qty(); isMoney(\'hrg_beli'.$no.'\');" id="hrg_beli'.$no.'" name="hrg_beli'.$no.'" class="form-control width-uang" value="'.number_format(($row['hrg_beli']+0),2).'"></td>
									<td><input type="text" onfocus="this.select()" onkeydown="return isNumber(event);" onkeyup="to_col(event, \'ho'.$no.'\', \'hrg_jual'.($no+1).'\'); hitung_qty(); isMoney(\'hrg_jual'.$no.'\');" id="hrg_jual'.$no.'" name="hrg_jual'.$no.'" class="form-control width-uang" value="'.number_format(($row['hrg_jual_1']+0),2).'"></td>
									'.$qty_order.'
									<td><input type="number" onfocus="this.select()" onkeydown="return isNumber(event);" onkeyup="to_col(event, \'buffer'.$no.'\', \'ho'.($no+1).'\'); hitung_qty()" id="ho'.$no.'" name="ho'.$no.'" class="form-control width-diskon" value="'.($row['stock_min']+0).'"></td>
									<td><input type="number" onfocus="this.select()" onkeydown="return isNumber(event);" onkeyup="to_col(event, \'hrg_beli'.$no.'\', \'buffer'.($no+1).'\'); hitung_qty()" id="buffer'.$no.'" name="buffer'.$no.'" class="form-control width-diskon" value="0"></td>
									<td><input type="number" id="jumlah'.$no.'" name="jumlah'.$no.'" class="form-control width-diskon" readonly value="'.$jumlah_qty.'"></td>
									<td><input type="text" id="subtotal'.$no.'" name="subtotal'.$no.'" class="form-control width-uang" readonly value="'.number_format(($row['hrg_beli']+0)*$jumlah_qty,2).'"></td>
								</tr>';	
			$sub_total = $sub_total + ($row['hrg_beli']+0)*$jumlah_qty;
		}
		$list_po_cabang .= '<input type="hidden" id="jumlah_lokasi" name="jumlah_lokasi" value="'.$i.'" />';
		$list_po_cabang .= '<input type="hidden" id="jumlah_barang" name="jumlah_barang" value="'.$no.'" />';
		$list_po_cabang .= '</tbody>';
        
		echo json_encode(array('status' => count($data_barang_order), 'list_po_cabang' => $list_po_cabang, 'sub_total'=>$sub_total));
    }

    public function insert_tr_temp_d_po_cabang($nota_sistem, $get_barang, $barcode) {
        $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d15)) id", "(SUBSTRING(d1,1,2) = 'PO') AND (d11 = '".$this->user."')");
        $data = array(
            'd1' => $nota_sistem,
            'd2' => $get_barang['kd_brg'],
            'd3' => $get_barang['hrg_beli'],
            'd4' => 0,
            'd5' => 0,
            'd6' => 0,
            'd7' => 0,
            'd8' => $get_barang['ppn'],
            'd9' => 1,
            'd10' => $barcode,
            'd11' => $this->user,
            'd12' => $get_barang['Deskripsi'],
            'd13' => $get_barang['satuan'],
            'd14' => $get_barang['hrg_jual_1'],
            'd15' => ((int)$get_max_id['id']+1),
            'd16' => $get_barang['nm_brg']
        );

        $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d17)) id", "d11='".$this->user."' AND (SUBSTRING(d1,1,2) = 'PO')");
        $data['d17'] = ((int)$get_max_id['id']+1);

        $this->m_crud->create_data("tr_temp_d", $data);
    }

    public function update_tr_temp_d_po_cabang($tmp_barcode, $tmp_column, $tmp_value) {
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        $this->m_crud->update_data("tr_temp_d", array($column => $value), "(SUBSTRING(d1,1,2) = 'PO') AND (d10 = '".$barcode."') AND (d11 = '".$this->user."')");
    }

    public function delete_tr_temp_d_po_cabang($tmp_barcode) {
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

        $this->m_crud->delete_data("tr_temp_d", "(d11 = '".$this->user."') AND (d10 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'PO')");

        echo true;
    }

    public function add_list_barang_po_cabang() {
        $nota_po = $_POST['nota_po_'];
        $list_barcode = $_POST['list_'];

        for ($i = 0; $i < count($list_barcode); $i++) {
            $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d9, d10", "(SUBSTRING(d1,1,2) = 'PO') AND (d10 = '".$list_barcode[$i]."') AND (d11 = '".$this->user."')");

            if ($cek_tr_temp_d == '') {
                $get_barang = $this->m_crud->get_data("barang", "kd_brg, nm_brg, Deskripsi, satuan, hrg_beli, hrg_jual_1, ppn", "barcode = '".$list_barcode[$i]."'");
                $this->insert_tr_temp_d_po($nota_po, $get_barang, $list_barcode[$i]);
            } else {
                $this->update_tr_temp_d_po(base64_encode($cek_tr_temp_d['d10']), base64_encode('d9'), base64_encode($cek_tr_temp_d['d9'] + 1));
            }
        }

        echo true;
    }

    public function get_barang_po_cabang($tmp_nota_sistem, $tmp_barcode, $tmp_lokasi_beli, $tmp_supplier, $tmp_cat_cari) {
        $cat_cari = base64_decode($tmp_cat_cari);
        $nota_sistem = base64_decode($tmp_nota_sistem);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi_beli));
        $lokasi_beli = $explode_lokasi[0];
        //$supplier = base64_decode($tmp_supplier);

        if ($cat_cari == 1) {
            $col_barang = 'barang.kd_brg';
            $col_tmp = 'd2';
        } else if ($cat_cari == 2) {
            $col_barang = 'barang.barcode';
            $col_tmp = 'd10';
        } else if ($cat_cari == 3) {
            $col_barang = 'barang.Deskripsi';
            $col_tmp = 'd12';
        }

        $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d9, d17", "(SUBSTRING(d1,1,2) = 'PO') AND (".$col_tmp." = '".$barcode."') AND (d11 = '".$this->user."')");

        if ($get_tmp_data != '') {
            $data = array(
                'd9' => (int)$get_tmp_data['d9'] + 1
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d11 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'PO')");
            echo json_encode(array('status' => 1, 'col_jumlah'=>$get_tmp_data['d17']));
        }else {
            $get_barang = $this->m_crud->get_join_data("barang", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, ISNULL(barang_hrg.hrg_jual_1, barang.hrg_jual_1) hrg_jual_1, ISNULL(barang_hrg.ppn, barang.ppn) ppn", array(array('table'=>'barang_hrg', 'type'=>'LEFT')), array("(barang.kd_brg = barang_hrg.barang) AND (barang_hrg.lokasi = '".$lokasi_beli."')"), "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
            if ($get_barang != '') {
                $this->insert_tr_temp_d_po($nota_sistem, $get_barang, $get_barang['barcode']);
                echo json_encode(array('status' => 1));
            }else {
                $get_barang = $this->m_crud->get_join_data("barang", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, ISNULL(barang_hrg.hrg_jual_1, barang.hrg_jual_1) hrg_jual_1, ISNULL(barang_hrg.ppn, barang.ppn) ppn", array(array('table'=>'barang_hrg', 'type'=>'LEFT')), array("(barang.kd_brg = barang_hrg.barang)"), "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                if ($get_barang != '') {
                    $this->insert_tr_temp_d_po($nota_sistem, $get_barang, $get_barang['barcode']);
                    echo json_encode(array('status' => 1));
                }else {
                    $get_barang = $this->m_crud->get_join_data("barang", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, ISNULL(barang_hrg.hrg_jual_1, barang.hrg_jual_1) hrg_jual_1, ISNULL(barang_hrg.ppn, barang.ppn) ppn", array(array('table'=>'barang_hrg', 'type'=>'LEFT')), array("(barang.kd_brg = barang_hrg.barang)"), "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                    if ($get_barang != '') {
                        $this->insert_tr_temp_d_po($nota_sistem, $get_barang, $get_barang['barcode']);
                        //echo json_encode(array('status' => 2, 'notif' => "Barang dari supplier ".$supplier." tidak tersedia!"));
                    }else {
                        echo json_encode(array('status' => 3, 'notif' => "Barang tidak tersedia!"));
                    }
                }
            }
        }
    }

    public function get_sub_total_po_cabang() {
        $read_data = $this->m_crud->read_data("tr_temp_d", "d3, d4, d5, d6, d7, d8, d9", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')", "d2");
        $sub_total = 0;
        foreach ($read_data as $row) {
            $hitung_netto = $row['d3'] * $row['d9'];
            $diskon = $this->m_website->double_diskon($hitung_netto, array($row['d4'], $row['d5'], $row['d6'], $row['d7']));
            $hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $row['d8']);
            $sub_total = $sub_total + $hitung_sub_total;
        }

        return $sub_total;
    }

    public function trans_po_cabang() {
        $tgl_order = $_POST['tgl_order'];
        $tgl_kirim = $_POST['tgl_kirim'];
        $catatan = $_POST['catatan'];
        $jenis = $_POST['jenis_transaksi'];
        $supplier = $_POST['supplier'];
        $explode_lokasi = explode('|', $_POST['lokasi']);
        $lokasi = $explode_lokasi[0];
        $serial = $explode_lokasi[1];
        $nota_po = $this->m_website->generate_kode("PO", $serial, substr(str_replace('-', '', $tgl_order), 2));
		
        $this->db->trans_begin(); 
		
        $sub_total = str_replace(',','',$_POST['sub_total']);

        $data_po = array(
            'tgl_po' => $tgl_order . " " . date("H:i:s"),
            'no_po' => $nota_po,
            'kode_supplier' => $supplier,
            'lokasi' => $lokasi,
			'jenis' => $jenis,
			'jenis_po' => 'POC',
            'kd_kasir' => $this->user,
            'status' => 0,
            'catatan' => $catatan,
            'GT' => $sub_total,
            'tglkirim' => $tgl_kirim . " " . date("H:i:s"),
        );
        $this->m_crud->create_data("Master_PO", $data_po);

        $det_log = array();
        for($jb=1; $jb<=$_POST['jumlah_barang']; $jb++){
			$data_detail_po = array(
                'no_po' => $nota_po,
                'kd_brg' => $_POST['kd_brg'.$jb],
                'diskon' => 0,
                'disc2' => 0,
                'PPN' => 0,
                'harga_beli' => str_replace(',','',$_POST['hrg_beli'.$jb]),
                'harga_jual' => str_replace(',','',$_POST['hrg_jual'.$jb]),
                'qty_ho' => $_POST['ho'.$jb],
                'qty_buffer' => $_POST['buffer'.$jb]
            );
            $this->m_crud->create_data("detail_po_cabang", $data_detail_po);

            $det_log_lokasi = array();
			for($jl=0; $jl<$_POST['jumlah_lokasi']; $jl++){
				$data_detail_po = array(
					'no_po' => $nota_po,
					'kd_brg' => $_POST['kd_brg'.$jb],
					'qty' => $_POST['qty'.$jb.'_'.$jl],
					'no_receive_order' => $_POST['no_receive_order'.$jb.'_'.$jl],
				);
				$this->m_crud->create_data("detail_qty_po_cabang", $data_detail_po);
				$this->m_crud->update_data("master_order", array('status'=>'2'), "no_order='".$_POST['no_order'.$jb.'_'.$jl]."'");
				$this->m_crud->delete_data("master_mutasi", "no_faktur_mutasi='".$_POST['no_faktur_mutasi'.$jb.'_'.$jl]."'");
				$this->m_crud->delete_data("det_mutasi", "no_faktur_mutasi='".$_POST['no_faktur_mutasi'.$jb.'_'.$jl]."'");
				$this->m_crud->update_data("master_receive_order", array('no_faktur_mutasi'=>'-'), "no_receive_order='".$_POST['no_receive_order'.$jb.'_'.$jl]."'");
				array_push($det_log_lokasi, $data_detail_po);
			}

			$data_detail_po['detail'] = $det_log_lokasi;
            array_push($det_log, $data_detail_po);
		}

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$nota_po,'jenis'=>'Add','transaksi'=>'Purchase Order Cabang'), array('master'=>$data_po,'detail'=>$det_log));
		
        $this->delete_trans_po_cabang('no_respon');

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status'=>false));
        }else {
			$this->db->trans_commit();
            echo json_encode(array('status'=>true, 'kode'=>$nota_po));
        }
    }
	
    public function delete_trans_po_cabang($param = null) {
        $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");
        $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')");

        if ($delete_data_master && $delete_data_detail && $param!='no_respon') {
            echo true;
        }

        $this->m_crud->delete_data("Master_PO", "no_po not in (select no_po from Detail_PO) and no_po not in (select no_po from detail_po_cabang)");
    } 
	//end modul po_cabang
	
	/*Start modul po by cabang*/
    public function po_by_cabang() {
        $this->access_denied(55);
        $data = $this->data;
        $function = 'po_by_cabang';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'PO Cabang';
        $data['page'] = $function;
        $data['content'] = $view.$function;
		
		if(isset($_GET['trx'])){ 
			$this->edit_po_by_cabang($_GET['trx']); 
		} 
		
		$values = is_array($this->session->userdata($this->site.'lokasi'))?array_map('array_pop', $this->session->userdata($this->site.'lokasi')):array();
		$lokasi_in = "'" . implode("','", $values) . "'";
		$data['lokasi_in'] = $lokasi_in;
		
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, Nama, serial', "kode in (".$lokasi_in.")");
        $data['data_supplier'] = $this->m_crud->read_data('Supplier','Kode, Nama');

        $this->load->view('bo/index', $data);
    }
	
	public function add_barang_po_by_cabang(){
		$lokasi = explode('|', $_POST['lokasi'])[0];
		$group1 = $_POST['group1'];
		
		$this->db->trans_begin();

		//$this->m_crud->delete_data('tr_temp_m', "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");
		$this->m_crud->delete_data('tr_temp_d', "(SUBSTRING(d1,1,2) = 'PO') AND (d11 = '".$this->user."')");
		
		$stock_cabang = "isnull((select sum(ks.stock_in - ks.stock_out) from kartu_stock ks where ks.kd_brg=br.kd_brg and ks.lokasi NOT IN ('MUTASI', 'Retur') AND ks.lokasi=bh.lokasi and ks.tgl <= '".date('Y-m-d H:i:s')."'),0)";
		$dd = $this->m_crud->join_data('barang br', "br.hrg_beli, br.ppn, 0 qty, br.kd_brg, br.hrg_jual_1 hrg_jual, ".$stock_cabang." stock_cabang, br.ppn, br.Deskripsi, br.satuan, br.nm_brg, br.kd_packing", 
			array('barang_hrg bh'), array('br.kd_brg=bh.barang'), "bh.lokasi = '".$lokasi."' and br.group1 = '".$group1."'", 
			null, "br.kd_brg, bh.lokasi, bh.stock_min, br.hrg_beli, br.ppn, br.hrg_jual_1, br.deskripsi, br.satuan, br.nm_brg, br.kd_packing", null, null, $stock_cabang." <= isnull((bh.stock_min),0)"
		);
		$no=0;
		foreach($dd as $d){ $no++;
			$data = array(
				'd1' => 'PO',
				'd2' => $d['kd_brg'],
				'd3' => $d['hrg_beli']+0,
				'd4' => 0,
				'd5' => 0,
				'd6' => 0,
				'd7' => 0,
				'd8' => $d['ppn']+0,
				'd9' => $d['qty'],
				'd10' => $d['kd_brg'],
				'd11' => $this->user,
				'd12' => $d['Deskripsi'],
				'd13' => $d['satuan'],
				'd14' => $d['hrg_jual']+0,
				'd15' => $no,
				'd16' => $d['nm_brg'],
				'd18' => $d['kd_packing'],
				'd19' => $d['stock_cabang']+0
			);
			$this->m_crud->create_data('tr_temp_d', $data);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			echo json_encode(array('status'=>0));
		} else {
			$this->db->trans_commit();
			echo json_encode(array('status'=>1));
		}
	}
	
	public function edit_po_by_cabang($id){
		$id = base64_decode($id);
		
		$this->db->trans_begin();
		
		$this->m_crud->delete_data('tr_temp_m', "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");
		$this->m_crud->delete_data('tr_temp_d', "(SUBSTRING(d1,1,2) = 'PO') AND (d11 = '".$this->user."')");
		
		$m = $this->m_crud->get_data('master_order', '*', "no_order='".$id."' and status='0'");
		$data = array(
            'm1' => 'PO',
            'm2' => date('Y-m-d', strtotime($m['tgl_order'])),
            'm3' => date('Y-m-d'),
            'm4' => $m['lokasi'].'|'.substr($m['no_order'],14,2),
            'm5' => $m['kode_supplier'],
            'm6' => 0,
            'm7' => $m['catatan'],
            'm8' => $this->user,
			'm9' => 1
        );
		$this->m_crud->create_data('tr_temp_m', $data);
		
		$stock = "isnull((select sum(ks.stock_in - ks.stock_out) from kartu_stock ks where ks.kd_brg=br.kd_brg and ks.lokasi NOT IN ('MUTASI', 'Retur') AND ks.lokasi=master_order.lokasi and ks.tgl <= '".date('Y-m-d H:i:s')."'),0) as stock";
		$dd = $this->m_crud->join_data('det_order', "det_order.*, ".$stock.", br.ppn, br.Deskripsi, br.satuan, br.nm_brg, br.kd_packing", array('master_order','barang br'), array('master_order.no_order=det_order.no_order','det_order.kd_brg=br.kd_brg'), "master_order.no_order='".$m['no_order']."'");
		$no=0;
		foreach($dd as $d){ $no++;
			$data = array(
				'd1' => 'PO',
				'd2' => $d['kd_brg'],
				'd3' => $d['hrg_beli'],
				'd4' => 0,
				'd5' => 0,
				'd6' => 0,
				'd7' => 0,
				'd8' => $d['ppn'],
				'd9' => $d['qty'],
				'd10' => $d['kd_brg'],
				'd11' => $this->user,
				'd12' => $d['Deskripsi'],
				'd13' => $d['satuan'],
				'd14' => $d['hrg_jual'],
				'd15' => $no,
				'd16' => $d['nm_brg'],
				'd18' => $d['kd_packing'],
				'd19' => $d['stock']
			);
			$this->m_crud->create_data('tr_temp_d', $data);
		}
		
		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
			$this->db->trans_commit();
        }
	}
	
	public function get_tr_temp_d_po_by_cabang() {
        $lokasi = explode('|', $_POST['lokasi'])[0];
		$list_barang = '';
        //$read_data = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')", "CONVERT(INTEGER, d17) ASC");
		$stock = "isnull((select sum(ks.stock_in - ks.stock_out) from kartu_stock ks where ks.kd_brg=barang and ks.lokasi NOT IN ('MUTASI', 'Retur') AND ks.lokasi=lokasi and ks.tgl <= '".date('Y-m-d H:i:s')."'),0)";
		$stock = "isnull((d19),(".$stock.")) as stock";
		$read_data = $this->m_crud->join_data('tr_temp_d', "tr_temp_d.*, ".$stock.", isnull(stock_min,0) stock_min, isnull(stock_max,0) stock_max", 
			array('barang_hrg'), array("d2=barang"), 
			"(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO') and lokasi='".$lokasi."'", "CONVERT(INTEGER, d17) ASC"
		);
        
		$no = 1;
        $col = 0;
        $sub_total = 0;
        $length = count($read_data);
        foreach ($read_data as $row) {
			if($row['d19']==null){ $this->m_crud->update_data('tr_temp_d', array('d19'=>$row['stock']), "d2='".$row['d2']."' and (d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')"); }
            $hitung_netto = $row['d3'] * $row['d9'];
            $diskon = $this->m_website->double_diskon($hitung_netto, array($row['d4'], $row['d5'], $row['d6'], $row['d7']));
            $hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $row['d8']);
            $sub_total = $sub_total + $hitung_sub_total;
            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d10'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button></td>
                                <td>' . $row['d2'] . '<input type="hidden" id="d2'.$no.'" name="d2'.$no.'" value="'.$row['d2'].'" /></td>
                                <td>' . $row['d10'] . '</td>
                                <td>' . $row['d16'] . '</td>
                                <td>' . $row['d12'] . '</td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d3\', $(this).val())" onkeydown="return isNumber(event)" onkeyup="update_tmp_detail(\'' . $row['d10'] . '\', \'d3\', $(this).val()); hitung_barang(\'d3\', \'' . $no . '\', $(this).val(), '.$length.'); isMoney(\'d3' . $no . '\', \'+\'); return to_qty(event, '.$no.')" onfocus="this.select()" type="text" id="d3' . $no . '" name="d3' . $no . '" class="form-control width-uang" value="' . number_format((float)($row['d3']+0), 2, '.', ',') . '"></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d14\', $(this).val())" onkeydown="return isNumber(event)" onkeyup="isMoney(\'d14' . $no . '\', \'+\')" onfocus="this.select()" value="' . number_format((float)($row['d14']+0), 2, '.', ',') . '" type="text" id="d14' . $no . '" name="d14' . $no . '" class="form-control width-uang"></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d4\', $(this).val())" onkeyup="hitung_barang(\'d4\', \'' . $no . '\', $(this).val(), '.$length.')" type="number" onfocus="this.select()" id="d4' . $no . '" name="d4' . $no . '" class="form-control width-diskon" value="' . ($row['d4'] + 0) . '"></td>
                                <input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d5\', $(this).val())" onkeyup="hitung_barang(\'d5\', \'' . $no . '\', $(this).val(), '.$length.')" type="hidden" id="d5' . $no . '" name="d5' . $no . '" class="form-control width-diskon" value="' . ($row['d5'] + 0) . '">
                                <input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d6\', $(this).val())" onkeyup="hitung_barang(\'d6\', \'' . $no . '\', $(this).val(), '.$length.')" type="hidden" id="d6' . $no . '" name="d6' . $no . '" class="form-control width-diskon" value="' . ($row['d6'] + 0) . '">
                                <input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d7\', $(this).val())" onkeyup="hitung_barang(\'d7\', \'' . $no . '\', $(this).val(), '.$length.')" type="hidden" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-diskon" value="' . ($row['d7'] + 0) . '">
                                <td>'.($row['stock']+0).'</td>
                                <td id="stock_min'.$no.'">'.$row['stock_min'].'</td>
                                <td id="stock_max'.$no.'">'.$row['stock_max'].'</td>
								<td>
									<input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d9\', $(this).val())" onfocus="this.select()" onkeyup="qty_max(); hitung_barang(\'d9\', \'' . $no . '\', $(this).val(), '.$length.'); return to_barcode(event)" type="number" id="d9' . $no . '" name="d9' . $no . '" class="form-control width-diskon" value="' . $row['d9'] . '">
									<b class="error" id="alr_qty'.$no.'"></b>
								</td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d8\', $(this).val())" onfocus="this.select()" onkeyup="hitung_barang(\'d8\', \'' . $no . '\', $(this).val(), '.$length.')" type="number" id="d8' . $no . '" name="d8' . $no . '" class="form-control width-diskon" value="' . ($row['d8'] + 0) . '"></td>
                                <td><input type="text" id="sub_total' . $no . '" name="sub_total' . $no . '" class="form-control width-uang" value="'.number_format((float)$hitung_sub_total, 2, '.', ',').'" readonly></td>
                            </tr>';
            $col = $no;
            $no++;
        }
        $list_barang .= '<input type="hidden" id="col" name="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang, 'sub_total' => $sub_total));
    }
	
	//tr tmp disamakan dengan purchase_order
	
	public function trans_po_by_cabang() {
        $tgl_order = $_POST['tgl_order'];
        $tgl_kirim = $_POST['tgl_kirim'];
        $catatan = $_POST['catatan'];
        $jenis = $_POST['jenis_transaksi'];
        $supplier = $_POST['supplier'];
        $explode_lokasi = explode('|', $_POST['lokasi']);
        $lokasi = $explode_lokasi[0];
        $serial = $explode_lokasi[1];
        $nota_po = $this->m_website->generate_kode("MO", $serial, substr(str_replace('-', '', $tgl_order), 2));
		
        $this->db->trans_begin();
		
		if(isset($_POST['update'])){
			$m = $this->m_crud->get_data('master_order', 'no_order, tgl_order, lokasi', "no_order='".$_POST['update']."'");
			if($tgl_order==substr($m['tgl_order'],0,10) && $lokasi==$m['lokasi']){ $nota_po=$m['no_order']; }
			$this->m_crud->delete_data('master_order', "no_order='".$m['no_order']."'");
			$this->m_crud->delete_data('det_order', "no_order='".$m['no_order']."'");
		}
		
        $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')");
        $sub_total = $this->get_sub_total_po();

        $data_po = array(
			'tgl_order' => $tgl_order . " " . date("H:i:s"),
            'no_order' => $nota_po,
            'kode_supplier' => $supplier,
            'lokasi' => $lokasi,
			'kd_kasir' => $this->user,
            'status' => 0,
            'catatan' => $catatan
        );
        $this->m_crud->create_data("master_order", $data_po);

        $det_log = array();
        //foreach ($read_temp_d as $row) {
        for ($i=1; $i<=$_POST['col']; $i++) {
			if($_POST['d9'.$i]>0){
				$data_detail_po = array(
					'no_order' => $nota_po,
					'kd_brg' => $_POST['d2'.$i],//$row['d2'],
					//'diskon' => $row['d4'],
					//'disc2' => $row['d5'],
					//'disc3' => $row['d6'],
					//'disc4' => $row['d7'],
					//'PPN' => $row['d8'],
					'hrg_beli' => $_POST['d3'.$i],//$row['d3'],
					'hrg_jual' => $_POST['d14'.$i],//$row['d14'],
					'qty' => $_POST['d9'.$i]//$row['d9']
				);
				$this->m_crud->create_data("det_order", $data_detail_po);
				array_push($det_log, $data_detail_po);
			}
        }
		
        if (isset($_POST['update'])){ 
            $data_po['trx_old'] = $_POST['update'];
        	$this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$nota_po,'jenis'=>'Edit','transaksi'=>'Purchase Order Cabang'), array('master'=>$data_po,'detail'=>$det_log));
		} else {
			$this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$nota_po,'jenis'=>'Add','transaksi'=>'Purchase Order Cabang'), array('master'=>$data_po,'detail'=>$det_log));
		}

        
        $this->delete_trans_po('no_respon');

        if ($this->db->trans_status()===FALSE || count($det_log)<=0) {
            $this->db->trans_rollback();
            echo false;
        }else {
			$this->db->trans_commit();
            echo $nota_po;
        }
    }
	//end modul po by cabang
	
    /*Start modul purchase order*/
    public function purchase_order() {
        $this->access_denied(51);
        $data = $this->data;
        $function = 'purchase_order';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Purchase Order';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, nama_toko Nama, serial', $this->where_lokasi);
        $data['data_supplier'] = $this->m_crud->read_data('Supplier','Kode, Nama');

        $this->load->view('bo/index', $data);
    }

    public function add_tr_temp_m_po() {
        $data = array(
            'm1' => $_POST['nota_po'],
            'm2' => $_POST['tgl_order'],
            'm3' => $_POST['tgl_kirim'],
            'm4' => $_POST['lokasi'],
            'm5' => $_POST['supplier'],
            'm6' => 0,
            'm7' => $_POST['catatan'],
            'm8' => $this->user,
			'm9' => 1
        );

        $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");

        if ($cek_data == 1) {
            $this->m_crud->update_data("tr_temp_m", $data, "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");
            $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['nota_po']), "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')");
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }
    }

    public function get_tr_temp_m_po() {
        $get_data = $this->m_crud->get_data("tr_temp_m", "*", "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");

        echo json_encode($get_data);
    }

    public function update_tr_temp_m_po($tmp_column, $tmp_data) {
        $column = base64_decode($tmp_column);
        $data = base64_decode($tmp_data);

        $this->m_crud->update_data("tr_temp_m", array($column => $data), "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");
    }

    public function get_tr_temp_d_po() {
        $list_barang = '';
        $read_data = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')", "CONVERT(INTEGER, d17) ASC");
		
        $no = 1;
        $col = 0;
        $sub_total = 0;
        $length = count($read_data);
        foreach ($read_data as $row) {
            $hitung_netto = $row['d3'] * $row['d9'];
            $diskon = $this->m_website->double_diskon($hitung_netto, array($row['d4'], $row['d5'], $row['d6'], $row['d7']));
            $hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $row['d8']);
            $sub_total = $sub_total + $hitung_sub_total;
            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d10'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button></td>
                                <td>' . $row['d2'] . '<input type="hidden" id="d2'.$no.'" name="d2'.$no.'" value="'.$row['d2'].'" /></td>
                                <td>' . $row['d10'] . '</td>
                                <td>' . $row['d16'] . '</td>
                                <td>' . $row['d12'] . '</td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d3\', $(this).val())" onkeydown="return isNumber(event)" onkeyup="update_tmp_detail(\'' . $row['d10'] . '\', \'d3\', $(this).val()); hitung_barang(\'d3\', \'' . $no . '\', $(this).val(), '.$length.'); isMoney(\'d3' . $no . '\', \'+\'); return to_qty(event, '.$no.')" onfocus="this.select()" type="text" id="d3' . $no . '" name="d3' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d3'], 2, '.', ',') . '"></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d14\', $(this).val())" onkeydown="return isNumber(event)" onkeyup="isMoney(\'d14' . $no . '\', \'+\')" onfocus="this.select()" value="' . number_format((float)$row['d14'], 2, '.', ',') . '" type="text" id="d14' . $no . '" name="d14' . $no . '" class="form-control width-uang"></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d4\', $(this).val())" onkeyup="hitung_barang(\'d4\', \'' . $no . '\', $(this).val(), '.$length.')" type="number" onfocus="this.select()" id="d4' . $no . '" name="d4' . $no . '" class="form-control width-diskon" value="' . ($row['d4'] + 0) . '"></td>
                                <input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d5\', $(this).val())" onkeyup="hitung_barang(\'d5\', \'' . $no . '\', $(this).val(), '.$length.')" type="hidden" id="d5' . $no . '" name="d5' . $no . '" class="form-control width-diskon" value="' . ($row['d5'] + 0) . '">
                                <input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d6\', $(this).val())" onkeyup="hitung_barang(\'d6\', \'' . $no . '\', $(this).val(), '.$length.')" type="hidden" id="d6' . $no . '" name="d6' . $no . '" class="form-control width-diskon" value="' . ($row['d6'] + 0) . '">
                                <input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d7\', $(this).val())" onkeyup="hitung_barang(\'d7\', \'' . $no . '\', $(this).val(), '.$length.')" type="hidden" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-diskon" value="' . ($row['d7'] + 0) . '">
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d9\', $(this).val())" onfocus="this.select()" onkeyup="hitung_barang(\'d9\', \'' . $no . '\', $(this).val(), '.$length.'); return to_barcode(event)" type="number" id="d9' . $no . '" name="d9' . $no . '" class="form-control width-diskon" value="' . $row['d9'] . '"></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d8\', $(this).val())" onfocus="this.select()" onkeyup="hitung_barang(\'d8\', \'' . $no . '\', $(this).val(), '.$length.')" type="number" id="d8' . $no . '" name="d8' . $no . '" class="form-control width-diskon" value="' . ($row['d8'] + 0) . '"></td>
                                <td><input type="text" id="sub_total' . $no . '" name="sub_total' . $no . '" class="form-control width-uang" value="'.number_format((float)$hitung_sub_total, 2, '.', ',').'" readonly></td>
                            </tr>';
            $col = $no;
            $no++;
        }
        $list_barang .= '<input type="hidden" id="col" name="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang, 'sub_total' => $sub_total));
    }

    public function insert_tr_temp_d_po($nota_sistem, $get_barang, $barcode, $param=null, $qty=1) {
        $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d15)) id", "(SUBSTRING(d1,1,2) = 'PO') AND (d11 = '".$this->user."')");
        $data = array(
            'd1' => $nota_sistem,
            'd2' => $get_barang['kd_brg'],
            'd3' => $get_barang['hrg_beli'],
            'd4' => 0,
            'd5' => 0,
            'd6' => 0,
            'd7' => 0,
            'd8' => $get_barang['ppn'],
            'd9' => $qty,
            'd10' => $barcode,
            'd11' => $this->user,
            'd12' => $get_barang['Deskripsi'],
            'd13' => $get_barang['satuan'],
            'd14' => $get_barang['hrg_jual_1'],
            'd15' => ((int)$get_max_id['id']+1),
            'd16' => $get_barang['nm_brg'],
            'd18' => $get_barang['kd_packing']
        );

        $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d17)) id", "d11='".$this->user."' AND (SUBSTRING(d1,1,2) = 'PO')");
        $data['d17'] = ((int)$get_max_id['id']+1);

        $this->m_crud->create_data("tr_temp_d", $data);
    }

    public function update_tr_temp_d_po($tmp_barcode, $tmp_column, $tmp_value) {
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        $this->m_crud->update_data("tr_temp_d", array($column => $value), "(SUBSTRING(d1,1,2) = 'PO') AND (d10 = '".$barcode."') AND (d11 = '".$this->user."')");
    }

    public function delete_tr_temp_d_po($tmp_barcode) {
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

        $this->m_crud->delete_data("tr_temp_d", "(d11 = '".$this->user."') AND (d10 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'PO')");

        echo true;
    }

    public function add_list_barang_po() {
        $nota_po = $_POST['nota_po_'];
        $list_barcode = $_POST['list_'];

        for ($i = 0; $i < count($list_barcode); $i++) {
            $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d9, d10", "(SUBSTRING(d1,1,2) = 'PO') AND (d10 = '".$list_barcode[$i]."') AND (d11 = '".$this->user."')");

            if ($cek_tr_temp_d == '') {
                $get_barang = $this->m_crud->get_data("barang", "kd_packing, kd_brg, nm_brg, Deskripsi, satuan, hrg_beli, hrg_jual_1, ppn", "barcode = '".$list_barcode[$i]."'");
                $this->insert_tr_temp_d_po($nota_po, $get_barang, $list_barcode[$i]);
            } else {
                $this->update_tr_temp_d_po(base64_encode($cek_tr_temp_d['d10']), base64_encode('d9'), base64_encode($cek_tr_temp_d['d9'] + 1));
            }
        }

        echo true;
    }

    public function get_barang_po($tmp_nota_sistem, $tmp_barcode, $tmp_lokasi_beli, $tmp_supplier, $tmp_cat_cari) {
        $cat_cari = base64_decode($tmp_cat_cari);
        $nota_sistem = base64_decode($tmp_nota_sistem);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi_beli));
        $lokasi_beli = $explode_lokasi[0];
        //$supplier = base64_decode($tmp_supplier);

        if ($cat_cari == 1) {
            $col_barang = 'barang.kd_brg';
            $col_tmp = 'd2';
        } else if ($cat_cari == 2) {
            $col_barang = 'barang.barcode';
            $col_tmp = 'd10';
        } else if ($cat_cari == 3) {
            $col_barang = 'barang.Deskripsi';
            $col_tmp = 'd12';
        } else if ($cat_cari == 4) {
            $col_barang = 'barang.kd_packing';
            $col_tmp = 'd18';
        }

        $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d9, d17", "(SUBSTRING(d1,1,2) = 'PO') AND (".$col_tmp." = '".$barcode."') AND (d11 = '".$this->user."')");
		
		if ($cat_cari == 4) {
			$qty = $this->m_crud->get_data('barang', 'isnull((qty_packing),0) qty_packing', $col_barang." = '".$barcode."'")['qty_packing'];
		} else {
			$qty = 1;
		}
		
        if ($get_tmp_data != '') {
            
			$data = array(
                'd9' => (int)$get_tmp_data['d9'] + $qty
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d11 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'PO')");
            echo json_encode(array('status' => 1, 'col_jumlah'=>$get_tmp_data['d17']));
        } else {
            $get_barang = $this->m_crud->get_join_data("barang", "barang.kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, ISNULL(barang_hrg.hrg_jual_1, barang.hrg_jual_1) hrg_jual_1, ISNULL(barang_hrg.ppn, barang.ppn) ppn", array(array('table'=>'barang_hrg', 'type'=>'LEFT')), array("(barang.kd_brg = barang_hrg.barang) AND (barang_hrg.lokasi = '".$lokasi_beli."')"), "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
            if ($get_barang != '') {
                $this->insert_tr_temp_d_po($nota_sistem, $get_barang, $get_barang['barcode'], null, $qty);
                echo json_encode(array('status' => 1));
            }else {
                $get_barang = $this->m_crud->get_join_data("barang", "barang.kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, ISNULL(barang_hrg.hrg_jual_1, barang.hrg_jual_1) hrg_jual_1, ISNULL(barang_hrg.ppn, barang.ppn) ppn", array(array('table'=>'barang_hrg', 'type'=>'LEFT')), array("(barang.kd_brg = barang_hrg.barang)"), "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                if ($get_barang != '') {
                    $this->insert_tr_temp_d_po($nota_sistem, $get_barang, $get_barang['barcode'], null, $qty);
                    echo json_encode(array('status' => 1));
                }else {
                    $get_barang = $this->m_crud->get_join_data("barang", "barang.kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, barang.hrg_beli, ISNULL(barang_hrg.hrg_jual_1, barang.hrg_jual_1) hrg_jual_1, ISNULL(barang_hrg.ppn, barang.ppn) ppn", array(array('table'=>'barang_hrg', 'type'=>'LEFT')), array("(barang.kd_brg = barang_hrg.barang)"), "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                    if ($get_barang != '') {
                        $this->insert_tr_temp_d_po($nota_sistem, $get_barang, $get_barang['barcode'], null, $qty);
                        //echo json_encode(array('status' => 2, 'notif' => "Barang dari supplier ".$supplier." tidak tersedia!"));
                    }else {
                        echo json_encode(array('status' => 3, 'notif' => "Barang tidak tersedia!"));
                    }
                }
            }
        }
    }

    public function get_sub_total_po() {
        $read_data = $this->m_crud->read_data("tr_temp_d", "d3, d4, d5, d6, d7, d8, d9", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')", "d2");
        $sub_total = 0;
        foreach ($read_data as $row) {
            $hitung_netto = $row['d3'] * $row['d9'];
            $diskon = $this->m_website->double_diskon($hitung_netto, array($row['d4'], $row['d5'], $row['d6'], $row['d7']));
            $hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $row['d8']);
            $sub_total = $sub_total + $hitung_sub_total;
        }

        return $sub_total;
    }

    public function trans_po_x() {
        $tgl_order = $_POST['tgl_order'];
        $tgl_kirim = $_POST['tgl_kirim'];
        $catatan = $_POST['catatan'];
        $jenis = $_POST['jenis_transaksi'];
        $supplier = $_POST['supplier'];
        $explode_lokasi = explode('|', $_POST['lokasi']);
        $lokasi = $explode_lokasi[0];
        $serial = $explode_lokasi[1];
        $nota_po = $this->m_website->generate_kode("PO", $serial, substr(str_replace('-', '', $tgl_order), 2));
		
        $this->db->trans_begin();
		
        $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')");;
        $sub_total = $this->get_sub_total_po();

        $data_po = array(
            'tgl_po' => $tgl_order . " " . date("H:i:s"),
            'no_po' => $nota_po,
            'kode_supplier' => $supplier,
            'lokasi' => $lokasi,
			'jenis' => $jenis,
			'jenis_po' => 'PO',
            'kd_kasir' => $this->user,
            'status' => 0,
            'catatan' => $catatan,
            'GT' => $sub_total,
            'tglkirim' => $tgl_kirim . " " . date("H:i:s"),
        );
        $this->m_crud->create_data("Master_PO", $data_po);

        $det_log = array();
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
                'harga_jual' => $row['d14'],
                'jumlah_beli' => $row['d9']
            );
            $this->m_crud->create_data("Detail_PO", $data_detail_po);
            array_push($det_log, $data_detail_po);
        }

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$nota_po,'jenis'=>'Add','transaksi'=>'Purchase Order'), array('master'=>$data_po,'detail'=>$det_log));

        $this->delete_trans_po('no_respon');

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status'=>false));
        }else {
			$this->db->trans_commit();
            echo json_encode(array('status'=>true, 'kode'=>$nota_po));
        }
    }

    /*public function trans_po($tmp_nota_po) {
        $nota_po = base64_decode($tmp_nota_po);
        $get_kode = $this->m_crud->get_data("Master_PO", "no_po", "(no_po = '".$nota_po."')");

        if ($get_kode != '') {
            $kode_baru = $this->m_website->generate_kode(substr($get_kode['no_po'], 0, 2), substr($get_kode['no_po'], 14), substr($get_kode['no_po'], 3, 6));
            $this->trans_pembelian(base64_encode($kode_baru));
        }else {
            $this->db->trans_begin();

            $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");;
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')");;
            $sub_total = $this->get_sub_total_po();
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
                }/
            }

            $this->delete_trans_po();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo false;
            }else {
                $this->db->trans_commit();
                echo true;
            }
        }
    }*/

    public function delete_trans_po($param = null) {
        $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");
        $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')");

        if ($delete_data_master && $delete_data_detail && $param!='no_respon') {
            echo true;
        }

        $this->m_crud->delete_data("Master_PO", "no_po not in (select no_po from Detail_PO)");
    }
    /*End modul purchase order*/


    /*Start modul report*/
	public function kontra_bon_report($action = null, $id = null){
        $this->access_denied(139);
        $data = $this->data;
        $function = 'kontra_bon_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Kontra Bon';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['lokasi'] = '';

        $where = "sp.kode = mk.supplier";
        $date1 = date('Y-m-d'); $date2 = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'supplier' => $_POST['supplier']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $supplier = $this->session->search['supplier']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mk.tgl_kontra, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mk.tgl_kontra, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
		
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="mk.lokasi = '".$lokasi."'"; }
        if(isset($supplier)&&$supplier!=null){ ($where==null)?null:$where.=" and "; $where.="mk.supplier = '".$supplier."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mk.id_master_kontra like '%".$search."%')"; }

        $data['lokasi'] = ($lokasi==null)?'':$lokasi;
        $data['tgl_awal'] = $date1;
        $data['tgl_akhir'] = $date2;

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data("Supplier sp, master_kontra mk", 'mk.id_master_kontra', ($where==null?null:$where), null, "mk.id_master_kontra");
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
		
        $column = "mk.id_master_kontra, mk.tgl_kontra, mk.tgl_bayar, sp.Nama nama_supplier, jenis, retur, biaya_adm, pembayaran, pembulatan, (select sum(nilai_kontra) from det_kontra where master_kontra=mk.id_master_kontra) nilai_kontrabon";
        $data['report'] = $this->m_crud->select_limit('Supplier sp, master_kontra mk', $column, ($where==null?null:$where), 'mk.tgl_kontra desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
		
        $data['detail'] = $this->db->query("
        with total as (
            select (sum(pembayaran)) nilai_pembayaran 
            from Supplier sp, master_kontra mk
            ".($where==null?'':'where '.$where)."
        )
        SELECT SUM(nilai_pembayaran) total_pembayaran FROM total
        ")->row_array();
		
        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->read_data('Supplier sp, pembelian_report pr', $column, ($where==null?null:$where), 'sp.kode ASC', 'sp.kode, sp.Nama, pr.disc, pr.ppn');
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

            $end = 0;

            foreach($baca as $row => $value){
                $end++;
                $body[$row] = array(
                    $value['kode'], $value['Nama'], $value['total_pembelian']
                );
            }

            $body[$end] = array('TOTAL','', $data['detail']['total_pembelian']);
            array_push($header['merge'], 'A'.($end+6).':B'.($end+6));
            $header['font']['A'.($end+6).':C'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;

            $data['report'] = $this->m_crud->get_data('Supplier sp, master_kontra mk', $column, "sp.kode = mk.supplier and mk.id_master_kontra = '".$id."'");
			
			if($data['report']['jenis']=='Konsinyasi'){
				$data['report_detail'] = $this->m_crud->read_data("master_kontra mk, det_kontra", "master_beli, nilai_kontra, '-' noNota, (
					select SUM(dt.qty*br.hrg_beli) from Master_Trx mt, Det_Trx dt, barang br where mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg and br.Group1=mk.supplier and mt.tgl >= (SUBSTRING(replace(master_beli, '/', '-'), 1, 10)+' 00:00:00') and mt.tgl <= (SUBSTRING(replace(master_beli, '/', '-'), 14, 10)+' 23:59:59') group by br.Group1
				) nilai_pembelian", "mk.id_master_kontra = master_kontra and master_kontra = '".$id."'");
			} else {
				$data['report_detail'] = $this->m_crud->read_data("det_kontra, master_beli", "master_beli, nilai_kontra, noNota, (select top 1 (total_beli + ppn - disc) from pembelian_report where no_faktur_beli = master_beli) nilai_pembelian", "master_beli=no_faktur_beli and master_kontra = '".$id."'");
			}
            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['id_master_beli']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header =
                '<div class="row"><img style="float: right; margin-top: -10px" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['id_master_kontra'].'"></div>'.
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Kontra Bon</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="18%"></th>
                                <th width="2%"></th>
                                <th width="20%"></th>
                                
                                <th width="6%"></th>
                                <th width="25%"></th>
                                <th width="2%"></th>
                                <th width="25%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>No. Kontra Bon</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['id_master_kontra'].'</td>
                                <td></td>
                                <td><b>Tanggal Kontra Bon</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_kontra'],0,10).'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Nama Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['nama_supplier'].'</td>
                                <td></td>
                                <td><b>Tanggal Jatuh Tempo</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_bayar'],0,10).'</td>
                            </tr>
							<tr>
                                <td></td>
                                <td><b>Jenis</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['jenis'].'</td>
                                <td></td>
                                <td><b></b></td>
                                <td><b></b></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>45,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }
	public function read_detail_kontra($tmp_nota=null, $tmp_jenis=null) {
		$nota = base64_decode($tmp_nota);
		$jenis = base64_decode($tmp_jenis);
        $list_nota_beli = '';
		if($jenis=='Konsinyasi'){
			$where = "mk.id_master_kontra = master_kontra";
			if(isset($nota) && $nota != null) { ($where == null) ? null : $where .= " and "; $where .= "master_kontra = '".$nota."'"; }
			$read_data = $this->m_crud->read_data("master_kontra mk, det_kontra", "master_beli, nilai_kontra, '-' noNota, (
				select SUM(dt.qty*br.hrg_beli) from Master_Trx mt, Det_Trx dt, barang br where mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg and br.Group1=mk.supplier and mt.tgl >= (SUBSTRING(replace(master_beli, '/', '-'), 1, 10)+' 00:00:00') and mt.tgl <= (SUBSTRING(replace(master_beli, '/', '-'), 14, 10)+' 23:59:59') group by br.Group1
			) nilai_pembelian", $where);
		} else {
			$where = "master_beli=no_faktur_beli";
			if(isset($nota) && $nota != null) { ($where == null) ? null : $where .= " and "; $where .= "master_kontra = '".$nota."'"; }
			$read_data = $this->m_crud->read_data("det_kontra, master_beli", "master_beli, nilai_kontra, noNota, (select top 1 (total_beli + ppn - disc) from pembelian_report where no_faktur_beli = master_beli) nilai_pembelian", $where);
		} 
		$no=0; $total=0;
        foreach ($read_data as $row){ $no++;
			$list_nota_beli .= '
				<tr>
					<td>'.$no.'</td>
					<td>'.$row['master_beli'].'</td>
					<td>'.$row['noNota'].'</td>
					<td style="text-align:right;">'.number_format($row['nilai_pembelian'],2).'</td>
					<td style="text-align:right;">'.number_format($row['nilai_kontra'],2).'</td>
				</tr>
			';
			$total = $total + $row['nilai_kontra'];
        }
			
        echo json_encode(array('status'=>count($read_data), 'list_detail'=>$list_nota_beli, 'total'=>$total));
    }
	
	public function bayar_kontra_bon_report($action = null, $id = null){
        $this->access_denied(140);
        $data = $this->data;
        $function = 'bayar_kontra_bon_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Bayar Kontra Bon';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['lokasi'] = '';

        $where = "sp.kode = bk.supplier and bk.id_master_byr_kontra=dbk.master_byr_kontra";
        $date1 = date('Y-m-d'); $date2 = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'supplier' => $_POST['supplier']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $supplier = $this->session->search['supplier']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, bk.tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, bk.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
		
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="bk.lokasi = '".$lokasi."'"; }
        if(isset($supplier)&&$supplier!=null){ ($where==null)?null:$where.=" and "; $where.="bk.supplier = '".$supplier."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(bk.id_master_byr_kontra like '%".$search."%' or dbk.master_kontra like '%".$search."%' or sp.Nama like '%".$search."%')"; }
		
        $data['lokasi'] = ($lokasi==null)?'':$lokasi;
        $data['tgl_awal'] = $date1;
        $data['tgl_akhir'] = $date2;

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data("Supplier sp, master_byr_kontra bk, det_byr_kontra dbk", 'bk.id_master_byr_kontra', ($where==null?null:$where), null, "bk.id_master_byr_kontra");
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
		
        $column = "dbk.master_kontra, bk.id_master_byr_kontra, bk.tgl, sp.Nama nama_supplier, bk.acc_no, bk.bank, (select an from supplier_rek where rekening=bk.acc_no) an, bk.bi_code, bk.bank_branch, bk.jenis, bk.rec, bk.receiv, (select sum(nilai_kontra) from det_kontra dk where dk.master_kontra=dbk.master_kontra) nilai_kontrabon, (select retur from master_kontra where id_master_kontra=dbk.master_kontra) retur_kontrabon, dbk.bayar_kontra";
        $data['report'] = $this->m_crud->select_limit('Supplier sp, master_byr_kontra bk, det_byr_kontra dbk', $column, ($where==null?null:$where), 'bk.tgl desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        $data['detail'] = $this->db->query("
        with total as (
            select (select sum(bayar_kontra) from det_byr_kontra dbk where dbk.master_byr_kontra=bk.id_master_byr_kontra) nilai_kontrabon 
            from Supplier sp, master_byr_kontra bk, det_byr_kontra dbk
            ".($where==null?'':'where '.$where)."
        )
        SELECT SUM(nilai_kontrabon) total_bayar_kontrabon FROM total
        ")->row_array();
		
        if(isset($_POST['to_excel'])){
            $baca = $data['report'];
            $header = array(
                'merge' 	=> array('A1:O1', 'A2:O2', 'A3:O3'),
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
                '3' => array('A' => $date1.' - '.$date2),
                '5' => array(
                    'A'=>'No', 'B'=>'Name', 'C'=>'Trans Date', 'D'=>'Trans Amount', 'E'=>'Retur', 'F'=>'Acc. No.', 'G'=>'Bank Nama', 'H'=>'BI Code', 'I'=>'Bank Branch Name', 
					'J'=>'Remark 1', 'K'=>'Remark 2', 'L'=>'Jenis', 'M'=>'Rec', 'N'=>'Receiv', 'O'=>'TTD'
                )
            );

            $end = 0;

            foreach($baca as $row => $value){
                $end++;
                $body[$row] = array(
                    $end, $value['an'], substr($value['tgl'],0,10), $value['bayar_kontra'], $value['retur_kontrabon'], $value['acc_no'], $value['bank'], $value['bi_code'], $value['bank_branch'],
					$value['master_kontra'], $value['nama_supplier'], $value['jenis'], $value['rec'], $value['receiv'], ''
                );
            }

            $body[$end] = array('TOTAL', '', '', $data['detail']['total_bayar_kontrabon']);
            array_push($header['merge'], 'A'.($end+6).':C'.($end+6));
            $header['font']['A'.($end+6).':O'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;

            $data['report'] = $this->m_crud->get_data('Supplier sp, master_kontra mk', $column, "mk.id_master_kontra = '".$id."'");

            $data['report_detail'] = $this->m_crud->read_data("det_kontra, master_beli", "master_beli, nilai_kontra, noNota, (select top 1 (total_beli + ppn - disc) from pembelian_report where no_faktur_beli = master_beli) nilai_pembelian", "master_beli=no_faktur_beli and master_kontra = '".$id."'");
			
            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['id_master_beli']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Laporan Kontra Bon</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="18%"></th>
                                <th width="2%"></th>
                                <th width="20%"></th>
                                
                                <th width="6%"></th>
                                <th width="25%"></th>
                                <th width="2%"></th>
                                <th width="25%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>No. Kontra Bon</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['id_master_kontra'].'</td>
                                <td></td>
                                <td><b>Tanggal Kontra Bon</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_kontra'],0,10).'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Nama Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['nama_supplier'].'</td>
                                <td></td>
                                <td><b>Tanggal Jatuh Tempo</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_bayar'],0,10).'</td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>33,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }
	
	public function bayar_hutang_report($action = null, $id = null){
        $this->access_denied(138);
        $data = $this->data;
        $function = 'bayar_hutang_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Bayar Hutang';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;
		
		if($action != 'print'){
			$where = null;
			$tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
			if(isset($_POST['search'])||isset($_POST['to_excel'])){
				$this->session->set_userdata('search', array('any' => $_POST['any'],'field-date'=> $_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'tipe' => $_POST['tipe']));
			}

			$search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date =$this->session->search['field-date']; $tipe = $this->session->search['tipe'];
			$explode_date = explode(' - ', $date);
			$date1 = str_replace('/','-',$explode_date[0]);
			$date2 = str_replace('/','-',$explode_date[1]);
			if (isset($date) && $date != null) {
				($where == null) ? null : $where .= " and ";
				$where .= "LEFT(CONVERT(VARCHAR, bh.tgl_byr, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
			}else{
				($where == null) ? null : $where .= " and ";
				$where .= "LEFT(CONVERT(VARCHAR, bh.tgl_byr, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
			}
			if(isset($tipe)&&$tipe!=null){ ($where==null)?null:$where.=" and "; $where.="bh.cara_byr = '".$tipe."'"; }
			if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(bh.no_nota like '%".$search."%' or bh.fak_beli like '%".$search."%' or sp.Nama like '%".$search."%' or mb.noNota like '%".$search."%')"; }

			$page = ($id==null?1:$id);
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data_over("bayar_hutang bh, master_beli mb, supplier sp", 'bh.no_nota', "bh.fak_beli=mb.no_faktur_beli and mb.kode_supplier=sp.kode".($where==null?'':' AND '.$where), "bh.tgl_byr desc");
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
			
			//$data['report'] = $this->m_crud->select_limit('bayar_hutang bh, master_beli mb, supplier sp', "bh.no_nota, bh.fak_beli, bh.tgl_byr, bh.cara_byr, bh.jumlah, bh.kasir, bh.nm_bank, mb.tgl_jatuh_tempo, bh.bulat, bh.nogiro, bh.tgl_cair_giro, bh.ket, sp.Nama", "bh.fak_beli=mb.no_faktur_beli and mb.kode_supplier=sp.kode".($where==null?'':' AND '.$where), 'bh.tgl_byr desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
			$data['report'] = $this->m_crud->read_data('bayar_hutang bh, master_beli mb, supplier sp', "bh.no_nota, bh.fak_beli, bh.tgl_byr, bh.cara_byr, bh.jumlah, bh.kasir, bh.nm_bank, mb.tgl_jatuh_tempo, mb.noNota, bh.bulat, bh.nogiro, bh.tgl_cair_giro, bh.ket, sp.Nama", "bh.fak_beli=mb.no_faktur_beli and mb.kode_supplier=sp.kode".($where==null?'':' AND '.$where), 'bh.tgl_byr desc', null, $config['per_page'], ($page-1)*$config['per_page']);
			
			$detail = $this->m_crud->read_data('bayar_hutang bh, master_beli mb, supplier sp', "bh.jumlah", "bh.fak_beli=mb.no_faktur_beli and mb.kode_supplier=sp.kode".($where==null?'':' AND '.$where), 'bh.tgl_byr desc');

			$ttp = 0;
			foreach ($detail as $row) {
				$ttp = $ttp + ($row['jumlah']);
			}

			$data['ttp'] = $ttp;
		}

        if(isset($_POST['to_excel'])){
            $detail_ex = $this->m_crud->read_data('bayar_hutang bh, master_beli mb, supplier sp', "bh.no_nota, bh.fak_beli, bh.tgl_byr, bh.cara_byr, bh.jumlah, bh.kasir, bh.nm_bank, mb.tgl_jatuh_tempo, bh.bulat, bh.nogiro, bh.tgl_cair_giro, bh.ket, sp.Nama", "bh.fak_beli=mb.no_faktur_beli and mb.kode_supplier=sp.kode".($where==null?'':' AND '.$where), 'bh.tgl_byr desc');
            $baca = $detail_ex;
            $header = array(
                'merge' 	=> array('A1:K1','A2:K2','A3:K3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:K5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:K5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Kode Pembelian', 'C'=>'Type', 'D'=>'Lokasi', 'E'=>'Nota Supplier', 'F'=>'Operator', 'G'=>'Kode Barang', 'H'=>'Barcode', 'I'=>'Nama Barang', 'J'=>'Jumlah Beli', 'K'=>'Harga Beli', 'L'=>'Pelunasan'
                )
            );

            $rowspan = 1;
            $start = 6;
            $end = 0;

            foreach($baca as $row => $value){
                if ($rowspan <= 1) {
                    $start = $start + $end;
                    $end = $start + $value['baris'] -1;
                    array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'F'.$start.':F'.$end.'');
                    $rowspan = $value['baris'];
                    if ($value['baris'] == 1) {
                        $start = 1;
                    }
                }else {
                    $rowspan = $rowspan - 1;
                    $start = 1;
                }

                $body[$row] = array(
                    $value['tgl_beli'], $value['no_faktur_beli'], $value['type'], $value['Lokasi'], $value['noNota'], $value['Operator'], $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['jumlah_beli'], $value['harga_beli'], $value['Pelunasan']
                );
            }

            $header['alignment']['A6:F'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['G6:I'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('bayar_hutang bh, master_beli mb, supplier sp', "bh.no_nota, bh.fak_beli, bh.tgl_byr, bh.cara_byr, bh.jumlah, bh.kasir, bh.nm_bank, mb.tgl_jatuh_tempo, bh.bulat, bh.nogiro, bh.tgl_cair_giro, bh.ket, sp.Nama", "bh.fak_beli=mb.no_faktur_beli and mb.kode_supplier=sp.kode and bh.no_nota = '".$id."'");
            //$data['report_detail'] = $this->m_crud->join_data('det_beli as db', 'kode_barang, barcode, nm_brg, jumlah_beli, hrg_jual_1', 'barang as br', 'kode_barang = kd_brg', "no_faktur_beli = '".$data['report']['no_faktur_beli']."'");
			
            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_beli']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = null;
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>10,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }
		
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function get_kartu_hutang() {
	    if (isset($_POST['supplier_'])) {
	        $where = " sp.nama like '%".$_POST['supplier_']."%'";
        } else {
	        $where = null;
        }

        $id = 1;
        $list_hutang = '';
        $total_hutang = 0;

        $read_kartu_hutang = $this->m_crud->join_data("kartu_hutang kh", "sp.kode, sp.Nama, SUM(kh.total_beli-kh.total_bayar) sisa_hutang", array(array('table' => 'supplier sp', 'type' => 'LEFT')), array('sp.kode=kh.kode_supplier'), $where, null, "sp.kode, sp.Nama", 0, 0, "SUM(kh.total_beli-kh.total_bayar) > 0");

	    foreach ($read_kartu_hutang as $row) {
	        $list_detail_hutang = '';
	        $no = 1;
	        $read_detail_hutang = $this->m_crud->read_data("kartu_hutang", "*", "kode_supplier='".$row['kode']."' AND (total_beli-total_bayar) > 0", "tgl_beli ASC, no_faktur_beli ASC");
            foreach ($read_detail_hutang as $row2) {
                $list_detail_hutang .= '
                    <tr>
                        <td>'.$no.'</td>
                        <td>'.substr($row2['tgl_beli'], 0, 10).'</td>
                        <td>'.$row2['no_faktur_beli'].'</td>
                        <td>'.$row2['nota_supplier'].'</td>
                        <td>'.number_format($row2['total_beli']).'</td>
                        <td>'.number_format($row2['total_bayar']).'</td>
                        <td>'.number_format($row2['total_beli']-$row2['total_bayar']).'</td>
                        <td><a href="'.base_url().'pembelian/bayar_hutang/bayar_nota_beli/'.base64_encode($row2['no_faktur_beli']).'" class="btn btn-primary"><i class="md md-payment"></i> Bayar Hutang</a></td>
                    </tr>
                ';
                $no++;
            }

	        $list_hutang .= '
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#list-hutang" href="#collapse'.$id.'" class="collapsed">
                                '.$row['Nama'].' | Jumlah Hutang Rp '.number_format($row['sisa_hutang']).'
                            </a>
                        </h4>
                    </div>
                    <div id="collapse'.$id.'" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 50px">No</th>
                                    <th>Tanggal</th>
                                    <th>Nota Pembelian</th>
                                    <th>Nota Supplier</th>
                                    <th>Hutang</th>
                                    <th>Dibayar</th>
                                    <th>Sisa Hutang</th>
                                    <th>Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                '.$list_detail_hutang.'
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
	        ';

            $total_hutang = $total_hutang + $row['sisa_hutang'];
            $id++;
        }

        echo json_encode(array('list_hutang'=>$list_hutang, 'total_hutang'=>'TOTAL HUTANG Rp '.number_format($total_hutang)));
    }
	
    public function pembelian_barang_report($action = null, $id = null){
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $this->access_denied(132);
        $data = $this->data;
        $function = 'pembelian_barang_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Arsip Pembelian';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $join = array(array('table'=>'Supplier sp', 'type'=>'LEFT'), array('table'=>'Lokasi lk', 'type'=>'LEFT'), array('table'=>'user_detail ud', 'type'=>'LEFT'));
        $on = array('pr.kode_supplier=sp.kode', 'pr.Lokasi=lk.Kode', 'pr.Operator=ud.user_id');
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'],'field-date'=> $_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'tipe' => $_POST['tipe'], 'order_by'=>$_POST['order_by'], 'order_sort'=>$_POST['order_sort']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date =$this->session->search['field-date']; $tipe = $this->session->search['tipe']; $order_by = $this->session->search['order_by']; $order_sort = $this->session->search['order_sort'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, pr.tgl_beli, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        }else{
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, pr.tgl_beli, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" AND ";
            $where.="pr.Lokasi = '".$lokasi."'";
        }
        if(isset($tipe)&&$tipe!=null){
            ($where==null)?null:$where.=" AND ";
            $where.="pr.type = '".$tipe."'";
        }
        if(isset($search)&&$search!=null){
            ($where==null)?null:$where.=" AND ";
            $where.="(".$order_by." like '%".$search."%' or pr.no_faktur_beli like '%".$search."%' or pr.noNota like '%".$search."%' or sp.nama like '%".$search."%' or ud.Nama like '%".$search."%' or pr.operator like '%".$search."%')";
        }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_join_over("pembelian_report pr", "pr.no_faktur_beli", $join, $on, ($where==null?'':$where), null, "pr.no_faktur_beli");
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

        $column = "pr.no_faktur_beli, pr.tgl_beli, pr.noNota, pr.nama_penerima, pr.type, pr.Pelunasan, pr.disc, pr.ppn, sp.Kode kode_supplier, sp.Nama supplier, ud.Nama operator, pr.lokasi kd_lokasi, lk.Nama lokasi, lk.serial";
        $group = "pr.no_faktur_beli, pr.tgl_beli, pr.noNota, pr.nama_penerima, pr.type, pr.Pelunasan, pr.disc, pr.ppn, sp.Kode, sp.Nama, ud.Nama, lk.Nama, pr.lokasi, lk.serial, pr.operator";
        $data['report'] = $this->m_crud->select_limit_join("pembelian_report pr", $column." ,SUM(jumlah_beli) qty_beli, SUM(sub_total)-disc+ppn total_beli", $join, $on, ($where==null?'':$where), (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'pr.no_faktur_beli DESC'), $group, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        $data['detail'] = $this->db->query("
        with total as (
            select SUM(sub_total)-disc+ppn total_beli 
            from pembelian_report pr
            left join Supplier sp on pr.kode_supplier=sp.kode
            left join Lokasi lk on pr.Lokasi=lk.Kode
            left join user_detail ud on pr.Operator=ud.user_id
            ".($where==null?'':'where '.$where)."
            group by disc, ppn
        )
        SELECT SUM(total_beli) total_beli FROM total
        ")->row_array();
        //$data['detail'] = $this->m_crud->get_join_data("pembelian_report pr", "SUM(jumlah_beli) qty_beli, SUM(sub_total) total_beli", $join, $on, ($where==null?'':$where));

        /*$data['report'] = $this->m_crud->select_limit('master_beli mb, supplier sp', "mb.tgl_beli, mb.Pelunasan, mb.no_faktur_beli, mb.type, mb.kode_supplier, mb.Lokasi, mb.noNota, sp.nama, mb.Operator, isnull(mb.PPN, 0) ppn, isnull(mb.disc, 0) disc, isnull((SELECT SUM(Total) FROM Master_Retur_Beli WHERE no_beli=mb.no_faktur_beli),0) jumlah_retur, (SELECT COUNT(no_faktur_mutasi) FROM Master_Mutasi WHERE Master_Mutasi.no_faktur_beli=mb.no_faktur_beli) alokasi", "mb.kode_supplier=sp.kode".($where==null?'':' AND '.$where), (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'mb.no_faktur_beli desc'), null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        $detail = $this->m_crud->read_data('master_beli mb, supplier sp', "mb.tgl_beli, mb.Pelunasan, mb.no_faktur_beli, mb.type, mb.kode_supplier, mb.Lokasi, mb.noNota, sp.nama, mb.Operator, mb.nilai_pembelian, isnull(mb.PPN, 0) ppn, isnull(mb.disc, 0) disc, isnull((SELECT SUM(Total) FROM Master_Retur_Beli WHERE no_beli=mb.no_faktur_beli),0) jumlah_retur", "mb.kode_supplier=sp.kode".($where==null?'':' AND '.$where), (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'mb.no_faktur_beli desc'));

        $ttp = 0;
        foreach ($detail as $row) {
            $sub_total = 0;
            $get_detail = $this->m_crud->read_data("det_beli", "harga_beli, jumlah_beli, jumlah_retur, diskon disc1, disc2, PPN ppn", "no_faktur_beli='".$row['no_faktur_beli']."'");
            foreach ($get_detail as $row_detail) {
                $hitung_netto = ((float)$row_detail['jumlah_beli']) * $row_detail['harga_beli'];
                $disc = $this->m_website->double_diskon($hitung_netto, array($row_detail['disc1'], $row_detail['disc2']));
                $hitung_sub_total = $this->m_website->grand_total_ppn($disc, 0, $row_detail['ppn']);
                $sub_total = $sub_total + $hitung_sub_total;
            }
            //$sub_total = $sub_total-$row['disc']+$row['PPN'];
            $ttp = $ttp + $sub_total;
        }
        $ppn_dis = $this->m_crud->get_data("master_beli mb, supplier sp", "sum(isnull(PPN, 0)-isnull(disc, 0)) ppn_dis", "mb.kode_supplier=sp.kode".($where==null?'':' AND '.$where));

        $data['ttp'] = $ttp + $ppn_dis['ppn_dis'];*/

        if(isset($_POST['to_excel'])){
            $detail_xc = $this->m_crud->join_data("pembelian_report pr", $column." ,SUM(jumlah_beli) qty_beli, SUM(sub_total)-disc+ppn total_beli", $join, $on, ($where==null?'':$where), (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'pr.no_faktur_beli DESC'), $group);
            $baca = $detail_xc;
            $header = array(
                'merge' 	=> array('A1:J1','A2:J2','A3:J3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:J5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:J5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'No. Transaksi', 'C'=>'Nota Supplier', 'D'=>'Type', 'E'=>'Pelunasan', 'F'=>'Lokasi', 'G'=>'Operator', 'H'=>'Diskon Transaksi', 'I'=>'PPN', 'J'=>'Total Pembelian'
                )
            );

            $end = 0;

            foreach($baca as $row => $value) {
                $end++;
                $body[$row] = array(
                    $value['tgl_beli'], $value['no_faktur_beli'], $value['noNota'], $value['type'], $value['pelunasan'], $value['lokasi'], $value['operator'], $value['disc'], $value['ppn'], $value['total_beli']
                );
            }

            $body[$end] = array('TOTAL','','','','','','','','',$data['detail']['total_beli']);
            array_push($header['merge'], 'A'.($end+6).':I'.($end+6));
            $header['font']['A'.($end+6).':J'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        /*if(isset($_POST['to_excel'])){
            $detail_xc = $this->m_crud->join_data("pembelian_report pr", "pr.kd_brg, pr.nm_brg, pr.barcode, pr.Deskripsi, pr.no_faktur_beli, pr.tgl_beli, pr.noNota, pr.type, pr.Pelunasan, pr.disc, pr.ppn, sp.Nama supplier, ud.Nama operator, lk.Nama lokasi, jumlah_beli qty_beli, hrg_beli, (sub_total)-(sub_total * CONVERT(decimal(38, 8), disc)/isnull(NULLIF(total_beli, 0), 1)) + ((sub_total - (sub_total * CONVERT(decimal(38, 8), disc)/isnull(NULLIF(total_beli, 0), 1))) * CONVERT(decimal(38, 8), ppn)/isnull(NULLIF(total_beli, 0), 1)) total_beli, (select COUNT(no_faktur_beli) from det_beli dbl where dbl.no_faktur_beli=pr.no_faktur_beli) baris", $join, $on, ($where==null?'':$where), (($order_by!=null&&$order_sort!=null)?$order_by." ".$order_sort:'pr.no_faktur_beli DESC'));
            $baca = $detail_xc;
            $header = array(
                'merge' 	=> array('A1:L1','A2:L2','A3:L3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:L5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:L5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Kode Pembelian', 'C'=>'Type', 'D'=>'Lokasi', 'E'=>'Nota Supplier', 'F'=>'Operator', 'G'=>'Kode Barang', 'H'=>'Barcode', 'I'=>'Nama Barang', 'J'=>'Jumlah Beli', 'K'=>'Harga Beli', 'L'=>'Pelunasan'
                )
            );

            $rowspan = 1;
            $start = 6;
            $end = 0;

            foreach($baca as $row => $value) {
                if ($rowspan <= 1) {
                    $start = $start + $end;
                    $end = $start + $value['baris'] -1;
                    array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'F'.$start.':F'.$end.'');
                    $rowspan = $value['baris'];
                    if ($value['baris'] == 1) {
                        $start = 1;
                    }
                }else {
                    $rowspan = $rowspan - 1;
                    $start = 1;
                }

                $body[$row] = array(
                    $value['tgl_beli'], $value['no_faktur_beli'], $value['type'], $value['lokasi'], $value['noNota'], $value['operator'], $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['qty_beli'], $value['hrg_beli'], $value['Pelunasan']
                );
            }

            $header['alignment']['A6:F'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['G6:I'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }*/

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('master_beli', "tgl_beli, no_faktur_beli, Lokasi, Operator", "no_faktur_beli = '".$id."'");
            $data['report_detail'] = $this->m_crud->join_data('det_beli as db', 'kode_barang, barcode, nm_brg, jumlah_beli, hrg_jual_1', 'barang as br', 'kode_barang = kd_brg', "no_faktur_beli = '".$data['report']['no_faktur_beli']."'");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_beli']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header =
                '<div class="row"><img style="float: right; margin-top: -10px" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['no_faktur_beli'].'"></div>'.
                $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Pembelian Barang</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="15%"></th>
									<th width="2%"></th>
									<th width="30%"></th>
									
									<th width="10%"></th>
									<th width="12%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Tanggal</b></td>
									<td><b>:</b></td>
									<td>'.substr($data['report']['tgl_beli'], 0, 10).'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td><b>No. Nota</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['no_faktur_beli'].'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Lokasi</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['Lokasi'].'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>50,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }
	
	public function po_by_cabang_report($action = null, $id = null){
        $this->access_denied(143);
        $data = $this->data;
        $function = 'po_by_cabang_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Purchase Order Cabang';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;
		
		$values = is_array($this->session->userdata($this->site.'lokasi'))?array_map('array_pop', $this->session->userdata($this->site.'lokasi')):array();
		$lokasi_in = "'" . implode("','", $values) . "'";
		$data['lokasi_in'] = $lokasi_in;
		
        $where = "mp.lokasi in (".$lokasi_in.")";
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date =  explode(' - ',$date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mp.tgl_order, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mp.tgl_order, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="mp.lokasi = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mp.no_order like '%".$search."%' or mp.lokasi like '%".$search."%' or mp.kd_kasir like '%".$search."%' or sp.nama like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_over('master_order mp, supplier sp', "mp.no_order", "sp.kode=mp.kode_supplier".($where==null?'':' and '.$where), 'mp.tgl_order desc');
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

        $data['report'] = $this->m_crud->select_limit_join('master_order mp', "mp.*, sp.nama nama_supplier, sp.alamat alamat_supplier, sp.telp telp_supplier", "supplier sp", "sp.kode=mp.kode_supplier", ($where==null?'':$where), 'mp.no_order desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $data['det_report'] = $this->m_crud->read_data('Master_order mp, det_order dp, barang br, supplier sp', "sp.nama nama_supplier, mp.tgl_order, mp.no_order, mp.lokasi, 'Cabang' jenis, mp.kd_kasir, mp.status, br.kd_brg, br.barcode, br.nm_brg, dp.qty jumlah_beli, dp.hrg_beli harga_beli, (SELECT COUNT(no_order) FROM det_order WHERE det_order.no_order=mp.no_order) baris", "mp.no_order=dp.no_order and sp.kode=mp.kode_supplier AND dp.kd_brg=br.kd_brg".($where==null?' ' : ' AND '.$where), 'mp.tgl_order desc');
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:L1','A2:L2','A3:L3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:L5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:L5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Kode PO', 'C'=>'Nama Supplier', 'D'=>'Lokasi', 'E'=>'Jenis', 'F'=>'Operator', 'G'=>'Status', 'H'=>'Kode Barang', 'I'=>'Barcode', 'J'=>'Nama Barang', 'K'=>'Jumlah Beli', 'L'=>'Harga Beli'
                )
            );

            $rowspan = 1;
            $start = 6;
            $end = 0;

            foreach($baca as $row => $value){
                if ($rowspan <= 1) {
                    $start = $start + $end;
                    $end = $start + $value['baris'] -1;
                    array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'F'.$start.':F'.$end.'', 'G'.$start.':G'.$end.'');
                    $rowspan = $value['baris'];
                    if ($value['baris'] == 1) {
                        $start = 1;
                    }
                }else {
                    $rowspan = $rowspan - 1;
                    $start = 1;
                }

                $body[$row] = array(
                    $value['tgl_order'], $value['no_order'], $value['nama_supplier'], $value['lokasi'], $value['jenis'], $value['kd_kasir'], ($value['status']==0?'Processing':'Ordered'), $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['jumlah_beli'], $value['harga_beli']
                );
            }

            $header['alignment']['A6:G'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['G6:J'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_join_data('master_order mp', "mp.*, sp.nama nama_supplier, sp.alamat alamat_supplier, sp.telp telp_supplier", "supplier sp", "sp.kode=mp.kode_supplier", "no_order = '".$id."'");
            $data['report_detail'] = $this->m_crud->join_data('det_order dp', 'dp.kd_brg kode_barang, br.barcode, br.nm_brg, dp.qty jumlah_beli, dp.hrg_beli harga_beli, br.satuan, br.deskripsi', 'barang as br', 'dp.kd_brg = br.kd_brg', "no_order = '".$data['report']['no_order']."'");
            $data['catatan'] = $data['report']['catatan'];

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 34; 
            $data['row_one_page'] = 27; 
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_po']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);
			
            $header =
                '<div class="row"><img style="float: right; margin-top: -10px" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['no_order'].'"></div>'.
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Purchase Order Cabang</b>'.
                '<div style="margin-bottom: 10px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="17%"></th>
                                <th width="2%"></th>
                                <th width="25%"></th>
                                
                                <th width="10%"></th>
                                <th width="12%"></th>
                                <th width="2%"></th>
                                <th width="30%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>No. PO</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['no_order'].'</td>
                                <td></td>
                                <td><b>Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['nama_supplier'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Tgl PO</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_order'], 0, 10).'</td>
                                <td></td>
                                <td><b>Alamat</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['alamat_supplier'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['lokasi'].'</td>
                                <td></td>
                                <td><b>Telepon</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['telp_supplier'].'</td>
                            </tr>
							<tr>
                                <td></td>
                                <td><b>Operator</b></td>
                                <td><b>:</b></td>
                                <td>'.$this->m_website->get_nama_user($data['report']['kd_kasir']).'</td>
                                <td></td>
                                <td><b>Keterangan</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['catatan'].'</td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>70,'bottom'=>(($t_row>$data['row_one_page'])?10:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }
	
	public function po_cabang_report($action = null, $id = null){
        $this->access_denied(141);
        $data = $this->data;
        $function = 'po_cabang_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan PO Pusat';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "jenis_po = 'POC'";
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date =  explode(' - ',$date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mp.tgl_po, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mp.tgl_po, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="mp.lokasi = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mp.no_po like '%".$search."%' or mp.lokasi like '%".$search."%' or mp.kd_kasir like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_over('Master_PO mp', "mp.no_po", ($where==null?'':$where), 'mp.tgl_po desc');
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

        $data['report'] = $this->m_crud->select_limit_join('Master_PO mp', "mp.*, sp.email, sp.nama nama_supplier, sp.alamat alamat_supplier, sp.telp telp_supplier", "supplier sp", "sp.kode=mp.kode_supplier", ($where==null?'':$where), 'mp.no_po desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $data['det_report'] = $this->m_crud->read_data('Master_PO mp, detail_po_cabang dpc, barang br', "mp.tgl_po, mp.no_po, mp.lokasi, mp.jenis, mp.kd_kasir, mp.status, br.kd_brg, br.barcode, br.nm_brg, (dpc.qty_ho + dpc.qty_buffer + isnull((select sum(dqpc.qty) from detail_qty_po_cabang dqpc where dqpc.no_po=dpc.no_po and dqpc.kd_brg=dpc.kd_brg),0)) jumlah_beli, dpc.harga_beli, (SELECT COUNT(no_po) FROM detail_po_cabang WHERE detail_po_cabang.no_po=mp.no_po) baris", "mp.no_po=dpc.no_po AND dpc.kd_brg=br.kd_brg".($where==null?' ' : ' AND '.$where), 'mp.tgl_po desc');
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:K1','A2:K2','A3:K3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:K5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:K5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Kode PO', 'C'=>'Lokasi', 'D'=>'Jenis', 'E'=>'Operator', 'F'=>'Status', 'G'=>'Kode Barang', 'H'=>'Barcode', 'I'=>'Nama Barang', 'J'=>'Jumlah Beli', 'K'=>'Harga Beli'
                )
            );

            $rowspan = 1;
            $start = 6;
            $end = 0;

            foreach($baca as $row => $value){
                if ($rowspan <= 1) {
                    $start = $start + $end;
                    $end = $start + $value['baris'] -1;
                    array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'F'.$start.':F'.$end.'');
                    $rowspan = $value['baris'];
                    if ($value['baris'] == 1) {
                        $start = 1;
                    }
                }else {
                    $rowspan = $rowspan - 1;
                    $start = 1;
                }

                $body[$row] = array(
                    $value['tgl_po'], $value['no_po'], $value['lokasi'], $value['jenis'], $value['kd_kasir'], ($value['status']==0?'Processing':'Ordered'), $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['jumlah_beli'], $value['harga_beli']
                );
            }

            $header['alignment']['A6:F'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['F6:I'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='send'||$action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_join_data('Master_PO mp', "mp.*, sp.nama nama_supplier, sp.email, sp.alamat alamat_supplier, sp.telp telp_supplier", "supplier sp", "sp.kode=mp.kode_supplier", "no_po = '".$id."'");
            $data['report_detail'] = $this->m_crud->join_data('detail_po_cabang dpc', 'dpc.kd_brg kode_barang, br.barcode, br.nm_brg, (dpc.qty_ho + dpc.qty_buffer + isnull((select sum(dqpc.qty) from detail_qty_po_cabang dqpc where dqpc.no_po=dpc.no_po and dqpc.kd_brg=dpc.kd_brg),0)) jumlah_beli, dpc.harga_beli, br.satuan, br.deskripsi', 'barang br', 'dpc.kd_brg = br.kd_brg', "dpc.no_po = '".$data['report']['no_po']."'");
            $data['catatan'] = $data['report']['catatan'];

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 34; 
            $data['row_one_page'] = 27; 
            ($action=='send')?($method='F'):(($action=='download')?($method='D'):($method='I'));
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_po']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);
			
            $header =
                '<div class="row"><img style="float: right; margin-top: -10px" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['no_po'].'"></div>'.
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Purchase Order</b><br/><font style="font-size:10px;">'.$this->m_website->address_2().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="17%"></th>
                                <th width="2%"></th>
                                <th width="25%"></th>
                                
                                <th width="5%"></th>
                                <th width="15%"></th>
                                <th width="2%"></th>
                                <th width="32%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>No. PO</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['no_po'].'</td>
                                <td></td>
                                <td><b>Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['nama_supplier'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Tgl PO</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_po'], 0, 10).'</td>
                                <td></td>
                                <td><b>Alamat</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['alamat_supplier'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Tgl Expired</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tglkirim'], 0, 10).'</td>
                                <td></td>
                                <td><b>Telepon</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['telp_supplier'].'</td>
                            </tr>
							<tr>
                                <td></td>
                                <td><b>Lokasi</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['lokasi'].'</td>
								<td></td>
								<td><b>Pembayaran</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['jenis'].'</td>
                            </tr>
							<tr>
                                <td></td>
                                <td><b>Operator</b></td>
                                <td><b>:</b></td>
                                <td>'.$this->m_website->get_nama_user($data['report']['kd_kasir']).'</td>
                                <td></td>
                                <td><b>Keterangan</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['catatan'].'</td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>65,'bottom'=>(($t_row>$data['row_one_page'])?10:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
			if($method=='F'){ 
				$to = strip_tags($data['report']['email']);
				$subject = $file;
				$message = '
					Pengiriman : '.substr($report['tglkirim'], 0, 10).'<br/><br/>
					Catatan : <br/>
					- Pengiriman terlambat 5 (lima) hari dari tanggal tersebut di atas, maka pembelian dapat dibatalkan.<br/>
					- "PO" harap dilampirkan, tanpa bon ini barang tidak akan diterima.<br/>
					- Retur tidak diambil maks. 3 (tiga) bulan, dianggap hangus.
				';
				
				$email =  array(
					'to'=>$to,
					'subject' => $subject,
					'message' => $message,
					'file_path' => APPPATH.'../'.$file.'.pdf',
					'file_name' => $file.'.pdf'
				);
				if ($this->m_website->send_mail($email) == true) {
					echo "<script>alert('Email has been send')</script>";
					//return true;
				} else {
					echo "<script>alert('Send mail failed. " . $mail->ErrorInfo . "')</script>";
					//return false;
				}
				//echo "<script>window.location = '".base_url().$view.$function."';</script>"; 
				echo "<script>window.close();</script>"; 
			}
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }
	
    public function purchase_order_report($action = null, $id = null){
        $this->access_denied(131);
        $data = $this->data;
        $function = 'purchase_order_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Purchase Order';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "jenis_po = 'PO'";
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date =  explode(' - ',$date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mp.tgl_po, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mp.tgl_po, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="mp.lokasi = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mp.no_po like '%".$search."%' or mp.lokasi like '%".$search."%' or mp.kd_kasir like '%".$search."%' or sp.nama like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_over('Master_PO mp, supplier sp', "mp.no_po", "sp.kode=mp.kode_supplier".($where==null?'':' and '.$where), 'mp.tgl_po desc');
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

        $data['report'] = $this->m_crud->select_limit_join('Master_PO mp', "mp.*, sp.email, sp.nama nama_supplier, sp.alamat alamat_supplier, sp.telp telp_supplier", "supplier sp", "sp.kode=mp.kode_supplier", ($where==null?'':$where), 'mp.no_po desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $data['det_report'] = $this->m_crud->read_data('Master_PO mp, Detail_PO dp, barang br, supplier sp', "sp.nama nama_supplier, mp.tgl_po, mp.no_po, mp.lokasi, mp.jenis, mp.kd_kasir, mp.status, br.kd_brg, br.barcode, br.nm_brg, dp.jumlah_beli, dp.harga_beli, (SELECT COUNT(no_po) FROM Detail_PO WHERE Detail_PO.no_po=mp.no_po) baris", "mp.no_po=dp.no_po and sp.kode=mp.kode_supplier AND dp.kode_barang=br.kd_brg".($where==null?' ' : ' AND '.$where), 'mp.tgl_po desc');
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:L1','A2:L2','A3:L3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:L5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:L5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Kode PO', 'C'=>'Nama Supplier', 'D'=>'Lokasi', 'E'=>'Jenis', 'F'=>'Operator', 'G'=>'Status', 'H'=>'Kode Barang', 'I'=>'Barcode', 'J'=>'Nama Barang', 'K'=>'Jumlah Beli', 'L'=>'Harga Beli'
                )
            );

            $rowspan = 1;
            $start = 6;
            $end = 0;

            foreach($baca as $row => $value){
                if ($rowspan <= 1) {
                    $start = $start + $end;
                    $end = $start + $value['baris'] -1;
                    array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'F'.$start.':F'.$end.'', 'G'.$start.':G'.$end.'');
                    $rowspan = $value['baris'];
                    if ($value['baris'] == 1) {
                        $start = 1;
                    }
                }else {
                    $rowspan = $rowspan - 1;
                    $start = 1;
                }

                $body[$row] = array(
                    $value['tgl_po'], $value['no_po'], $value['nama_supplier'], $value['lokasi'], $value['jenis'], $value['kd_kasir'], ($value['status']==0?'Processing':'Ordered'), $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['jumlah_beli'], $value['harga_beli']
                );
            }

            $header['alignment']['A6:G'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['G6:J'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='send'||$action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_join_data('Master_PO mp', "mp.*, sp.email, sp.nama nama_supplier, sp.alamat alamat_supplier, sp.telp telp_supplier", "supplier sp", "sp.kode=mp.kode_supplier", "no_po = '".$id."'");
            $data['report_detail'] = $this->m_crud->join_data('Detail_PO dp', 'dp.kode_barang, br.barcode, br.nm_brg, dp.jumlah_beli, dp.harga_beli, br.satuan, br.deskripsi', 'barang as br', 'dp.kode_barang = br.kd_brg', "no_po = '".$data['report']['no_po']."'");
            $data['catatan'] = $data['report']['catatan'];

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 34; 
            $data['row_one_page'] = 27; 
            ($action=='send')?($method='F'):(($action=='download')?($method='D'):($method='I'));
            //$method='I';
            $file = str_replace('-', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_po']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);
			
            $header =
                '<div class="row"><img style="float: right; margin-top: -10px" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['no_po'].'"></div>'.
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Purchase Order</b><br/><font style="font-size:10px;">'.$this->m_website->address_2().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="17%"></th>
                                <th width="2%"></th>
                                <th width="25%"></th>
                                
                                <th width="10%"></th>
                                <th width="12%"></th>
                                <th width="2%"></th>
                                <th width="30%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>No. PO</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['no_po'].'</td>
                                <td></td>
                                <td><b>Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['nama_supplier'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Tgl PO</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_po'], 0, 10).'</td>
                                <td></td>
                                <td><b>Alamat</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['alamat_supplier'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Tgl Expired</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tglkirim'], 0, 10).'</td>
                                <td></td>
                                <td><b>Telepon</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['telp_supplier'].'</td>
                            </tr>
							<tr>
                                <td></td>
                                <td><b>Lokasi</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['lokasi'].'</td>
								<td></td>
								<td><b>Pembayaran</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['jenis'].'</td>
                            </tr>
							<tr>
                                <td></td>
                                <td><b>Operator</b></td>
                                <td><b>:</b></td>
                                <td>'.$this->m_website->get_nama_user($data['report']['kd_kasir']).'</td>
                                <td></td>
                                <td><b>Keterangan</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['catatan'].'</td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>70,'bottom'=>(($t_row>$data['row_one_page'])?10:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
			if($method=='F'){ 
				$to = strip_tags($data['report']['email']);
				$subject = $file;
				$message = '
					Pengiriman : '.substr($report['tglkirim'], 0, 10).'<br/><br/>
					Catatan : <br/>
					- Pengiriman terlambat 5 (lima) hari dari tanggal tersebut di atas, maka pembelian dapat dibatalkan.<br/>
					- "PO" harap dilampirkan, tanpa bon ini barang tidak akan diterima.<br/>
					- Retur tidak diambil maks. 3 (tiga) bulan, dianggap hangus.
				';
				
				$email =  array(
					'to'=>$to,
					'subject' => $subject,
					'message' => $message,
					'file_path' => APPPATH.'../'.$file.'.pdf',
					'file_name' => $file.'.pdf'
				);
				if ($this->m_website->send_mail($email) == true) {
					echo "<script>alert('Email has been send')</script>";
					//return true;
				} else {
					echo "<script>alert('Send mail failed. " . $mail->ErrorInfo . "')</script>";
					//return false;
				}
				//echo "<script>window.location = '".base_url().$view.$function."';</script>"; 
				echo "<script>window.close();</script>"; 
			}
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function pembelian_by_barang($action = null, $id = null){
        $this->access_denied(134);
        $data = $this->data;
        $function = 'pembelian_by_barang';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Pembelian By Barang';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['lokasi'] = '';

        $where = null;
        $t_where = '';

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " AND ";
            $where .= "LEFT(CONVERT(VARCHAR, tgl_beli, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            $date1 = date('Y-m-d'); $date2 = date('Y-m-d');
            ($where == null) ? null : $where .= " AND ";
            $where .= "LEFT(CONVERT(VARCHAR, tgl_beli, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" AND ";
            $where.="Lokasi = '".$lokasi."'";
        }
        if(isset($search)&&$search!=null){
            ($where==null)?null:$where.=" AND ";
            $where.="(nm_brg like '%".$search."%' or kd_brg like '%".$search."%' or barcode like '%".$search."%' or no_faktur_beli like '%".$search."%')";
        }

        $data['lokasi'] = ($lokasi==null)?'':$lokasi;
        $data['tgl_awal'] = $date1;
        $data['tgl_akhir'] = $date2;

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data("pembelian_by_barang", 'kd_brg', ($where==null?'':$where), null, "kd_brg");
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

        //$column = "barang.kd_brg,barang.barcode, Barang.Nm_brg, Sum((det_beli.Jumlah_Beli-det_beli.Jumlah_Retur)) qty_beli, barang.satuan, Sum((det_beli.Jumlah_Beli-det_beli.Jumlah_Retur)*(det_beli.Harga_Beli*(1-det_beli.Diskon/100)*(1-det_beli.Disc2/100)*(1-det_beli.Disc3/100)*(1-det_beli.Disc4/100)*(1+det_beli.PPN/100))) nilai_beli, Sum((det_beli.Jumlah_Beli-det_beli.Jumlah_Retur)*(det_beli.Harga_Beli*(1-det_beli.Diskon/100)*(1-det_beli.Disc2/100)*(1-det_beli.Disc3/100)*(1-det_beli.Disc4/100)*(1+det_beli.PPN/100)))/Sum((det_beli.Jumlah_Beli-det_beli.Jumlah_Retur)) harga_rata";
        //$data['report'] = $this->m_crud->select_limit('det_beli, barang, master_beli', $column, "det_beli.Kode_Barang= barang.kd_brg and master_beli.no_faktur_beli=det_beli.no_faktur_beli".($where==null?'':$where), 'barang.kd_brg asc', $column, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        $column = "kd_brg, barcode, nm_brg, satuan";
        $data['report'] = $this->m_crud->select_limit('pembelian_by_barang', $column." ,SUM(jumlah_beli) jumlah_beli, SUM(sub_total-disc+ppn) total_beli", ($where==null?'':$where), "kd_brg ASC", $column, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        $data['detail'] = $this->m_crud->get_data('pembelian_by_barang', "SUM(jumlah_beli) qty_beli, SUM(sub_total-disc+ppn) total_beli", ($where==null?'':$where));

        /*$tqt = 0; $tst = 0;
        foreach ($detail as $row) {
            $sub_total = 0;
            $get_detail = $this->m_crud->read_data("det_beli", "harga_beli, jumlah_beli, jumlah_retur, isnull(diskon, 0) disc1, isnull(disc2, 0) disc2, isnull(PPN, 0) ppn", "left(CONVERT(varchar, tgl_beli, 120), 10) between '".$date1."' and '".$date2."' and kode_barang='".$row['kd_brg']."'");
            foreach ($get_detail as $row_detail) {
                $hitung_netto = ((int)$row_detail['jumlah_beli']-(int)$row_detail['jumlah_retur']) * $row_detail['harga_beli'];
                $disc = $this->m_website->double_diskon($hitung_netto, array($row_detail['disc1'], $row_detail['disc2']));
                $hitung_sub_total = $this->m_website->grand_total_ppn($disc, 0, $row_detail['ppn']);
                $sub_total = $sub_total + $hitung_sub_total;
            }
            //$sub_total = $this->m_website->grand_total_ppn($sub_total-$row['disc'], 0, $row['PPN']);
            $tst = $tst + $sub_total;
            $tqt = $tqt + (int)$row['qty_beli'];
        }
        $ppn_dis = $this->m_crud->get_data("master_beli", "sum(isnull(PPN, 0)-isnull(disc, 0)) ppn_dis", "left(CONVERT(varchar, tgl_beli, 120), 10) between '".$date1."' and '".$date2."'");

        $data['tqt'] = $tqt;
        $data['tst'] = $tst + $ppn_dis['ppn_dis'];*/


        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->read_data('pembelian_by_barang', $column." ,SUM(jumlah_beli) jumlah_beli, SUM(sub_total-disc+ppn) total_beli", ($where==null?'':$where), "kd_brg ASC", $column);
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
                '3' => array('A' => $date1.' - '.$date2),
                '5' => array(
                    'A'=>'Kode Barang', 'B'=>'Barcode', 'C'=>'Nama Barang', 'D'=>'Qty Beli', 'E'=>'Satuan', 'F'=>'Nilai Pembelian', 'G'=>'Harga Rata-rata'
                )
            );

            $end = 0;

            foreach($baca as $row => $value){
                $end++;
                $body[$row] = array(
                    $value['kd_brg'], $value['barcode'], $value['nm_brg'], ($value['jumlah_beli']+0), $value['satuan'], $value['total_beli'], $value['total_beli']/$value['jumlah_beli']
                );
            }

            $body[$end] = array('TOTAL','','',$data['detail']['qty_beli'],'',$data['detail']['total_beli'],'');
            array_push($header['merge'], 'A'.($end+6).':C'.($end+6));
            $header['font']['A'.($end+6).':G'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;

            $data['report'] = $this->m_crud->get_data('barang', "kd_brg, barcode, nm_brg", "kd_brg = '".$id."'");

            $condition = " AND LEFT(CONVERT(varchar, pbb.tgl_beli, 120), 10) BETWEEN '".$date1."' AND '".$date2."'";
            $condition2 = ($lokasi=='')?"":" AND pbb.lokasi='".$lokasi."' ";
            $data['report_detail'] = $this->m_crud->read_data("pembelian_by_barang pbb, det_beli db, Supplier sp", "pbb.no_faktur_beli, pbb.tgl_beli, sp.nama, pbb.jumlah_beli qty, pbb.satuan, db.harga_beli, db.diskon disc1, db.disc2, db.ppn, pbb.sub_total, pbb.disc disct, pbb.ppn ppnt", "pbb.kd_brg = '".$id."' AND pbb.kd_brg=db.kode_barang AND pbb.kode_supplier=sp.kode AND pbb.no_faktur_beli=db.no_faktur_beli".$condition.$condition2, "pbb.no_faktur_beli asc");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_beli']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Detail Laporan Pembelian By Barang</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="15%"></th>
                                <th width="2%"></th>
                                <th width="30%"></th>
                                
                                <th width="10%"></th>
                                <th width="12%"></th>
                                <th width="2%"></th>
                                <th width="27%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>Kode Barang</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_brg'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Barcode</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['barcode'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Nama Barang</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['nm_brg'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>37,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function return_pembelian_report($action = null, $id = null) {
        $this->access_denied(133);
        $data = $this->data;
        $function = 'return_pembelian_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Return Pembelian';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            ini_set('max_execution_time', 3600);
            ini_set('memory_limit', '1000M');
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'lokasi_cabang' => $_POST['lokasi_cabang'], 'kondisi' => $_POST['kondisi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $lokasi_cabang = $this->session->search['lokasi_cabang']; $kondisi = $this->session->search['kondisi']; $date = $this->session->search['field-date'];

        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rb.Tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rb.Tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="rb.Lokasi = '".$lokasi."'"; }
        if(isset($lokasi_cabang)&&$lokasi_cabang!=null){ ($where==null)?null:$where.=" and "; $where.="rb.Lokasi_cabang = '".$lokasi_cabang."'"; }
        if(isset($kondisi)&&$kondisi!=null){ ($where==null)?null:$where.=" and "; $where.="drb.kondisi = '".$kondisi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(rb.No_Retur like '%".$search."%' or drb.kd_brg like '%".$search."%' or rb.Lokasi like '%".$search."%' or sp.nama like '%".$search."%' or mb.noNota like '%".$search."%' or rb.kd_kasir like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_join_over('Master_Retur_Beli rb', "rb.No_Retur", array(array("table"=>"Supplier sp", "type"=>"LEFT"), array("table"=>"master_beli mb", "type"=>"LEFT"), array("table"=>"Det_Retur_Beli drb", "type"=>"LEFT")), array("rb.Supplier=sp.kode", "rb.no_beli=mb.no_faktur_beli", "rb.No_Retur=drb.No_Retur"), ($where==null?'':$where), null, "rb.No_Retur");
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

        $data['report'] = $this->m_crud->select_limit_join('Master_Retur_Beli rb', "rb.No_Retur, rb.Tgl, convert(varchar, rb.keterangan) keterangan, rb.kd_kasir, rb.Lokasi, rb.lokasi_cabang, sp.Kode, sp.Nama, rb.no_beli, isnull(mb.noNota, 'Tanpa Nota') noNota, rb.Total", array(array("table"=>"Supplier sp", "type"=>"LEFT"), array("table"=>"master_beli mb", "type"=>"LEFT"), array("table"=>"Det_Retur_Beli drb", "type"=>"LEFT")), array("rb.Supplier=sp.kode", "rb.no_beli=mb.no_faktur_beli", "rb.No_Retur=drb.No_Retur"), ($where==null?null:$where), 'rb.No_Retur desc', "rb.No_Retur, rb.Tgl, convert(varchar, rb.keterangan), rb.kd_kasir, rb.Lokasi, rb.lokasi_cabang, sp.Kode, sp.Nama, rb.no_beli, mb.noNota, rb.Total", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            /*$data['det_report'] = $this->m_crud->join_data('Master_Retur_Beli rb', "rb.No_Retur, rb.Tgl, rb.kd_kasir, rb.Lokasi, sp.Kode, sp.Nama, rb.no_beli, isnull(mb.noNota, 'Tanpa Nota') noNota, (SELECT SUM(jml*hrg_beli) FROM Det_Retur_Beli WHERE No_Retur=rb.No_Retur) total, drb.jml, drb.keterangan, drb.hrg_beli, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, (SELECT COUNT(No_Retur) FROM Det_Retur_Beli WHERE Det_Retur_Beli.No_Retur=rb.No_Retur) baris", array(array("table"=>"Det_Retur_Beli drb", "type"=>"LEFT"), array("table"=>"barang br", "type"=>"LEFT"), array("table"=>"Supplier sp", "type"=>"LEFT"), array("table"=>"master_beli mb", "type"=>"LEFT")), array("rb.No_Retur=drb.No_Retur", "drb.kd_brg=br.kd_brg", "rb.Supplier=sp.kode", "rb.no_beli=mb.no_faktur_beli"), ($where==null?null:$where), 'rb.Tgl desc, rb.No_Retur desc');
            $baca = $data['det_report'];*/
            $header = array(
                'merge' 	=> array('A1:Q1','A2:Q2','A3:Q3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:Q5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:Q5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'No. Retur', 'C'=>'No. Pembelian', 'D'=>'Nota Supplier', 'E'=>'Supplier', 'F'=>'Lokasi', 'G'=>'Lokasi Cabang', 'H'=>'Kode Barang', 'I'=>'Barcode', 'J'=>'Nama Barang', 'K'=>$this->menu_group['as_deskripsi'], 'L'=>'Qty Retur', 'M'=>'Harga Beli', 'N'=>'Nilai Retur', 'O'=>'Total Retur', 'P'=>'Ket 1', 'Q'=>'Ket 2'
                )
            );

            $rowspan = 1;
            $start = 6;
            $end = 0;
            $qty = 0;

            $page=1;
            $limit = 10;
            $x = 10;
            $row = 0;
            while ($x == 10) {
                $x=0;
                $data['det_report'] = $this->m_crud->select_limit_join('Master_Retur_Beli rb', "rb.No_Retur, rb.Tgl, rb.kd_kasir, rb.Lokasi, rb.lokasi_cabang, sp.Kode, sp.Nama, rb.no_beli, isnull(mb.noNota, 'Tanpa Nota') noNota, (SELECT SUM(jml*hrg_beli) FROM Det_Retur_Beli WHERE No_Retur=rb.No_Retur) total, drb.jml, convert(varchar(max), rb.keterangan) keterangan, drb.hrg_beli, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, (SELECT COUNT(No_Retur) FROM Det_Retur_Beli WHERE Det_Retur_Beli.No_Retur=rb.No_Retur) baris", array(array("table"=>"Det_Retur_Beli drb", "type"=>"LEFT"), array("table"=>"barang br", "type"=>"LEFT"), array("table"=>"Supplier sp", "type"=>"LEFT"), array("table"=>"master_beli mb", "type"=>"LEFT")), array("rb.No_Retur=drb.No_Retur", "drb.kd_brg=br.kd_brg", "rb.Supplier=sp.kode", "rb.no_beli=mb.no_faktur_beli"), ($where==null?null:$where), 'rb.Tgl desc, rb.No_Retur desc', null, ($page-1)*$limit+1, ($limit*$page));
                $baca = $data['det_report'];
                foreach ($baca as $value) {
                    $x++;
                    $sub_total = (int)$value['jml']*(float)$value['hrg_beli'];
                    if ($rowspan <= 1) {
                        $start = $start + $end;
                        $end = $start + $value['baris'] -1;
                        array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'F'.$start.':F'.$end.'', 'G'.$start.':G'.$end.'', 'O'.$start.':O'.$end.'', 'P'.$start.':P'.$end.'');
                        $rowspan = $value['baris'];
                        if ($value['baris'] == 1) {
                            $start = 1;
                        }
                    } else {
                        $rowspan = $rowspan - 1;
                        $start = 1;
                    }

                    $body[$row] = array(
                        $value['Tgl'], $value['No_Retur'], $value['no_beli'], $value['noNota'], $value['Nama'], $value['Lokasi'], $value['lokasi_cabang'], $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['Deskripsi'], $value['jml'], $value['hrg_beli'], number_format($sub_total, 2, '.', ''), number_format($value['total'], 2, '.', ''), $value['keterangan'], ''
                    );
                    $row++;
                }
                $page++;
            }
            /*foreach($baca as $row => $value){
                $sub_total = (int)$value['jml']*(float)$value['hrg_beli'];
                if ($rowspan <= 1) {
                    $start = $start + $end;
                    $end = $start + $value['baris'] -1;
                    array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'F'.$start.':F'.$end.'', 'N'.$start.':N'.$end.'');
                    $rowspan = $value['baris'];
                    if ($value['baris'] == 1) {
                        $start = 1;
                    }
                } else {
                    $rowspan = $rowspan - 1;
                    $start = 1;
                }

                $body[$row] = array(
                    $value['Tgl'], $value['No_Retur'], $value['no_beli'], $value['noNota'], $value['Nama'], $value['Lokasi'], $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['Deskripsi'], $value['jml'], $value['hrg_beli'], number_format($sub_total, 2, '.', ''), number_format($value['total'], 2, '.', ''), $value['keterangan'], ''
                );
            }*/

            $header['alignment']['A6:F'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['N6:N'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $header['alignment']['G6:J'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function pembelian_by_supplier($action = null, $id = null){
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $this->access_denied(135);
        $data = $this->data;
        $function = 'pembelian_by_supplier';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Pembelian Supplier';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['lokasi'] = '';

        $where = "sp.kode=pr.kode_supplier";
        $date1 = date('Y-m-d'); $date2 = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
		$explode_date = explode(' - ', $date);
		
        if (isset($date) && $date != null) {
			$date1 = str_replace('/','-',$explode_date[0]);
			$date2 = str_replace('/','-',$explode_date[1]);
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, pr.tgl_beli, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, pr.tgl_beli, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="pr.lokasi = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(sp.kode like '%".$search."%' or sp.Nama like '%".$search."%')"; }

        $data['lokasi'] = ($lokasi==null)?null:$lokasi;
        $data['tgl_awal'] = $date1;
        $data['tgl_akhir'] = $date2;

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data("Supplier sp, pembelian_report pr", 'sp.kode', ($where==null?null:$where), null, "sp.kode");
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

        $column = "sp.kode, sp.Nama, SUM(total_pembelian) total_pembelian";
        $data['report'] = $this->m_crud->select_limit('Supplier sp, total_pembelian_supplier pr', $column, ($where==null?null:$where), 'sp.kode ASC', 'sp.kode, sp.Nama', ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        $data['total'] = $this->m_crud->get_data('Supplier sp, total_pembelian_supplier pr', "SUM(total_pembelian) total_pembelian", ($where==null?null:$where));

        /*$detail = $this->m_crud->read_data('Supplier sp, master_beli mb, det_beli db', $column, ($where==null?null:$where), 'sp.kode ASC', 'sp.kode, sp.Nama');

        $ttp = 0;
        foreach ($detail as $row) {
            $sub_total = 0;
            $get_detail = $this->m_crud->read_data("det_beli", "harga_beli, jumlah_beli, jumlah_retur, diskon disc1, disc2, PPN ppn", "left(CONVERT(varchar, tgl_beli, 120), 10) between '".$date1."' and '".$date2."' and no_faktur_beli in (select no_faktur_beli from master_beli where kode_supplier = '".$row['kode']."')");
            foreach ($get_detail as $row_detail) {
                $hitung_netto = ((int)$row_detail['jumlah_beli']-(int)$row_detail['jumlah_retur']) * $row_detail['harga_beli'];
                $disc = $this->m_website->double_diskon($hitung_netto, array($row_detail['disc1'], $row_detail['disc2']));
                $hitung_sub_total = $this->m_website->grand_total_ppn($disc, 0, $row_detail['ppn']);
                $sub_total = $sub_total + $hitung_sub_total;
            }
            $ttp = $ttp + $sub_total;
        }
        $ppn_dis = $this->m_crud->get_data("master_beli", "sum(isnull(PPN, 0)-isnull(disc, 0)) ppn_dis", "left(CONVERT(varchar, tgl_beli, 120), 10) between '".$date1."' and '".$date2."'");

        $data['ttp'] = $ttp + $ppn_dis['ppn_dis'];*/

        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->read_data('Supplier sp, total_pembelian_supplier pr', $column, ($where==null?null:$where), 'sp.kode ASC', 'sp.kode, sp.Nama');
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

            $end = 0;

            foreach($baca as $row => $value){
                $end++;
                $body[$row] = array(
                    $value['kode'], $value['Nama'], $value['total_pembelian']
                );
            }

            $body[$end] = array('TOTAL','', $data['detail']['total_pembelian']);
            array_push($header['merge'], 'A'.($end+6).':B'.($end+6));
            $header['font']['A'.($end+6).':C'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;

            $data['report'] = $this->m_crud->get_data('Supplier', "kode, Nama", "kode = '".$id."'");

            $condition = "sp.kode='".$id."' AND sp.kode=mb.kode_supplier AND mb.no_faktur_beli=db.no_faktur_beli and (mb.tgl_beli >= '".$date1." 00:00:00' and mb.tgl_beli <= '".$date2." 23:59:59')";
            ($lokasi==null)?null:$condition .= " and mb.lokasi='".$lokasi."'";
            $column = "mb.no_faktur_beli, mb.tgl_beli, mb.noNota, mb.type, mb.tgl_jatuh_tempo, isnull(mb.ppn, 0) ppn, isnull(mb.disc, 0) disc";
            $data['report_detail'] = $this->m_crud->read_data("master_beli mb, det_beli db, Supplier sp", $column, $condition, "mb.no_faktur_beli ASC", "mb.no_faktur_beli, mb.tgl_beli, mb.noNota, mb.type, mb.tgl_jatuh_tempo, mb.disc, mb.ppn");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_beli']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Detail Laporan Pembelian By Supplier</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="18%"></th>
                                <th width="2%"></th>
                                <th width="27%"></th>
                                
                                <th width="10%"></th>
                                <th width="12%"></th>
                                <th width="2%"></th>
                                <th width="27%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>Kode Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kode'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Nama Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['Nama'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>33,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function pembelian_by_kel_barang($action = null, $id = null){
        $this->access_denied(136);
        $data = $this->data;
        $function = 'pembelian_by_kel_barang';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Pembelian By Kelompok Barang';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['lokasi'] = '';

        $where = "pbb.kel_brg=kb.kel_brg";
        $date1 = date('Y-m-d'); $date2 = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
		if(isset($date) && $date != null){
			$explode_date = explode(' - ', $date);
			$date1 = str_replace('/','-',$explode_date[0]);
			$date2 = str_replace('/','-',$explode_date[1]);
		}
		
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, pbb.tgl_beli, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, pbb.tgl_beli, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="pbb.lokasi = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(kb.kel_brg like '%".$search."%' or kb.nm_kel_brg like '%".$search."%')"; }

        $data['lokasi'] = ($lokasi==null)?'':$lokasi;
        $data['tgl_awal'] = $date1;
        $data['tgl_akhir'] = $date2;

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data("pembelian_by_barang pbb, kel_brg kb", 'kb.kel_brg', ($where==null?null:$where), null, "kb.kel_brg");
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

        $column = "kb.kel_brg, kb.nm_kel_brg";
        $data['report'] = $this->m_crud->select_limit('pembelian_by_barang pbb, kel_brg kb', $column." ,SUM(jumlah_beli) jumlah_beli, SUM(sub_total-disc+ppn) total_beli", ($where==null?'':$where), "kb.kel_brg ASC", $column, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        $data['detail'] = $this->m_crud->get_data('pembelian_by_barang pbb, kel_brg kb', "SUM(jumlah_beli) qty_beli, SUM(sub_total-disc+ppn) total_beli", ($where==null?'':$where));

        /*$data['report'] = $this->m_crud->select_limit('master_beli mb, det_beli db, barang br, kel_brg kb', $column, ($where==null?null:$where), 'kb.kel_brg ASC', 'kb.kel_brg, kb.nm_kel_brg', ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        $detail = $this->m_crud->read_data('master_beli mb, det_beli db, barang br, kel_brg kb', $column, ($where==null?null:$where), 'kb.kel_brg ASC', 'kb.kel_brg, kb.nm_kel_brg');

        $ttp = 0; $tqt = 0;
        foreach ($detail as $row) {
            $sub_total = 0;
            $get_detail = $this->m_crud->read_data("det_beli", "harga_beli, jumlah_beli, jumlah_retur, diskon disc1, disc2, PPN ppn", "left(CONVERT(varchar, tgl_beli, 120), 10) between '".$date1."' and '".$date2."' and kode_barang in (select kd_brg from barang where kel_brg = '".$row['kel_brg']."')");
            foreach ($get_detail as $row_detail) {
                $hitung_netto = ((int)$row_detail['jumlah_beli']-(int)$row_detail['jumlah_retur']) * $row_detail['harga_beli'];
                $disc = $this->m_website->double_diskon($hitung_netto, array($row_detail['disc1'], $row_detail['disc2']));
                $hitung_sub_total = $this->m_website->grand_total_ppn($disc, 0, $row_detail['ppn']);
                $sub_total = $sub_total + $hitung_sub_total;
            }
            $ttp = $ttp + $sub_total;
            $tqt = $tqt + (int)$row['qty'];
        }
        $ppn_dis = $this->m_crud->get_data("master_beli", "sum(isnull(PPN, 0)-isnull(disc, 0)) ppn_dis", "left(CONVERT(varchar, tgl_beli, 120), 10) between '".$date1."' and '".$date2."'");

        $data['ttp'] = $ttp + $ppn_dis['ppn_dis'];
        $data['tqt'] = $tqt;*/

        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->read_data('pembelian_by_barang pbb, kel_brg kb', $column." ,SUM(jumlah_beli) jumlah_beli, SUM(sub_total-disc+ppn) total_beli", ($where==null?'':$where), "kb.kel_brg ASC", $column);
            $header = array(
                'merge' 	=> array('A1:D1', 'A2:D2', 'A3:D3'),
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
                '3' => array('A' => $date1.' - '.$date2),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Kelompok', 'C'=>'Qty Beli', 'D'=>'Total Pembelian'
                )
            );

            $end = 0;

            foreach($baca as $row => $value){
                $end++;
                $sub_total = 0;
                $get_detail = $this->m_crud->read_data("det_beli", "harga_beli, jumlah_beli, jumlah_retur, diskon disc1, disc2, PPN ppn", "left(CONVERT(varchar, tgl_beli, 120), 10) between '".$date1."' and '".$date2."' and kode_barang in (select kd_brg from barang where kel_brg = '".$value['kel_brg']."')");
                foreach ($get_detail as $row_detail) {
                    $hitung_netto = ((int)$row_detail['jumlah_beli']-(int)$row_detail['jumlah_retur']) * $row_detail['harga_beli'];
                    $disc = $this->m_website->double_diskon($hitung_netto, array($row_detail['disc1'], $row_detail['disc2']));
                    $hitung_sub_total = $this->m_website->grand_total_ppn($disc, 0, $row_detail['ppn']);
                    $sub_total = $sub_total + $hitung_sub_total;
                }
                $body[$row] = array(
                    $value['kel_brg'], $value['nm_kel_brg'], (int)$value['jumlah_beli'], (float)$value['total_beli']
                );
            }

            $body[$end] = array('TOTAL','',$data['detail']['qty_beli'],$data['detail']['total_beli']);
            array_push($header['merge'], 'A'.($end+6).':B'.($end+6));
            $header['font']['A'.($end+6).':D'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', 'pembelian_by_kelompok_barang'), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;

            $data['report'] = $this->m_crud->get_data('kel_brg', "kel_brg, nm_kel_brg", "kel_brg = '".$id."'");

            $condition = " AND LEFT(CONVERT(varchar, pbb.tgl_beli, 120), 10) BETWEEN '".$date1."' AND '".$date2."'";
            $condition2 = ($lokasi=='')?"":" AND pbb.lokasi='".$lokasi."' ";
            $data['report_detail'] = $this->m_crud->read_data("pembelian_by_barang pbb, det_beli db, Supplier sp", "pbb.no_faktur_beli, pbb.tgl_beli, sp.nama, pbb.jumlah_beli qty, pbb.satuan, db.harga_beli, db.diskon disc1, db.disc2, db.ppn, pbb.sub_total, pbb.disc disct, pbb.ppn ppnt", "pbb.kel_brg = '".$id."' AND pbb.kd_brg=db.kode_barang AND pbb.kode_supplier=sp.kode AND pbb.no_faktur_beli=db.no_faktur_beli".$condition.$condition2, "pbb.no_faktur_beli asc");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_beli']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Detail Laporan Pembelian By Kel. Barang</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="15%"></th>
									<th width="2%"></th>
									<th width="30%"></th>
									
									<th width="10%"></th>
									<th width="12%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Kode</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['kel_brg'].'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Kelompok</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['nm_kel_brg'].'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>50,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function export_budget_location($supplier, $date) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');

        $supplier = base64_decode($supplier);
        $date = base64_decode($date);
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/', '-', $explode_date[0]);
        $date2 = str_replace('/', '-', $explode_date[1]);

        $qty_stock = "(SELECT SUM(stock) FROM current_stock WHERE Group1='".$supplier."' AND LEFT(CONVERT(VARCHAR, tgl, 120),10) <= '" . $date2 . "' AND lokasi = lk.Kode)";
        $value_stock = "(SELECT SUM(value) FROM current_stock WHERE Group1='".$supplier."' AND LEFT(CONVERT(VARCHAR, tgl, 120),10) <= '" . $date2 . "' AND lokasi = lk.Kode)";
        $qty_beli = "(SELECT SUM(qty_beli) FROM budget_receive WHERE kode_supplier='".$supplier."' AND LEFT(CONVERT(VARCHAR, tgl_beli, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' AND lokasi = lk.Kode)";
        $value_beli = "(SELECT SUM(total_pembelian) FROM budget_receive WHERE kode_supplier='".$supplier."' AND LEFT(CONVERT(VARCHAR, tgl_beli, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' AND lokasi = lk.Kode)";
        $qty_jual = "(SELECT SUM(qty) FROM sales_budget WHERE Group1='".$supplier."' AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' AND lokasi = lk.Kode)";
        $value_beli2 = "(SELECT SUM(val_beli) FROM sales_budget WHERE Group1='".$supplier."' AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' AND lokasi = lk.Kode)";
        $value_jual = "(SELECT SUM(val_jual-disc_jual) FROM sales_budget WHERE Group1='".$supplier."' AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' AND lokasi = lk.Kode)";
        $column = "isnull(" . $qty_stock . ", 0) qty_stock, isnull(" . $qty_jual . ", 0) qty_jual, isnull(" . $qty_beli . ", 0) qty_beli," . $value_stock . " value_stock," . $value_beli . " total_pembelian," . $value_beli2 . " value_beli," . $value_jual . " value_jual";

        $read_data = $this->m_crud->read_data('Lokasi lk', "lk.Kode, lk.nama_toko Nama, " . $column, null, 'lk.Kode ASC', 'lk.Kode, lk.nama_toko', 0, 0, $qty_beli . "<>0 OR " . $qty_stock . "<>0 OR " . $qty_jual . "<>0");

        $header = array(
            'merge' 	=> array('A1:M1','A2:M2','A3:M3','A5:A6','B5:B6','L5:L6','M5:M6','C5:D5','E5:F5','G5:K5'),
            'auto_size' => true,
            'font' 		=> array(
                'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                'A3' => array('bold'=>true,'name'=>'Verdana'),
                'A5:M6' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
            ),
            'alignment' => array(
                'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                'A5:M6' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            ),
            '1' => array('A' => $data['site']->title),
            '2' => array('A' => 'Budget Supplier ('.$supplier.')'),
            '3' => array('A' => $date1.' - '.$date2),
            '5' => array(
                'A'=>'Kode', 'B'=>'Lokasi', 'C'=>'Current Stock', 'E'=>'Receive', 'G'=>'Sales', 'L'=>'Qty Rasio', 'M'=>'Val Rasio'
            ),
            '6' => array(
                'C'=>'Qty', 'D'=>'Value', 'E'=>'Qty', 'F'=>'Value', 'G'=>'Qty', 'H'=>'Val Cost', 'I'=>'Val Price', 'J'=>'Margin Rp', 'K'=>'Margin %'
            )
        );

        $i = 0;
        $csq = 0;
        $csv = 0;
        $rq = 0;
        $rv = 0;
        $sq = 0;
        $svc = 0;
        $svp = 0;
		
        foreach($read_data as $row => $value){
            $i++;
            $body[$row] = array(
                $value['Kode'], $value['Nama'], ($value['qty_stock']-$value['qty_beli']), ($value['value_stock']-$value['total_pembelian']), $value['qty_beli'], $value['total_pembelian'], $value['qty_jual'], $value['value_beli'], $value['value_jual'], $value['value_jual'] - $value['value_beli'], (($value['value_beli'] > 0 && $value['value_beli'] < $value['value_jual']) ? round((1 - ($value['value_beli'] / $value['value_jual'])) * 100, 2) : 0), ($value['qty_jual'] != 0 ? round($value['qty_stock'] / $value['qty_jual'], 2) : 0), ($value['value_jual'] != 0 ? round($value['value_stock'] / $value['value_jual'], 2) : 0)
            );
            $csq = $csq + (int)$value['qty_stock'] - (int)$value['qty_beli'];
            $csv = $csv + $value['value_stock'] - $value['total_pembelian'];
            $rq = $rq + (int)$value['qty_beli'];
            $rv = $rv + $value['total_pembelian'];
            $sq = $sq + (int)$value['qty_jual'];
            $svc = $svc + $value['value_beli'];
            $svp = $svp + $value['value_jual'];
        }

        $body[$i] = array('TOTAL', '', $csq, $csv, $rq, $rv, $sq, $svc, $svp, $svp - $svc, (($svc > 0 && $svc < $svp) ? round((1 - ($svc / $svp)) * 100, 2) : 0), ($sq != 0 ? round(((float)$csq+(float)$rq) / (float)$sq, 2) : 0), ($svp != 0 ? round(((float)$csv+(float)$rv) / (float)$svp, 2) : 0));
        array_push($header['merge'], 'A'.($i+7).':B'.($i+7).'');
        $header['font']['A'.($i+7).':M'.($i+7).''] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

        $this->m_export_file->to_excel(str_replace(' ', '_', 'Budget Supplier'), $header, $body);
    }

    public function get_list_budget($param = null) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $where = null;
        $where_lokasi = null;
        if ($param == null) {
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date' => $_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'supplier' => $_POST['supplier']));
        }

        $search = $this->session->search['any'];
        $lokasi = $this->session->search['lokasi'];
        $supplier = $this->session->search['supplier'];
        $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);

        if (isset($date) && $date != null) {
            $date1 = str_replace('/', '-', $explode_date[0]);
            $date2 = str_replace('/', '-', $explode_date[1]);
        } else {
            $date1 = date('Y-m-d');
            $date2 = date('Y-m-d');
        }
        if (isset($lokasi) && $lokasi != null) {
            /*($where_lokasi == null) ? null : $where_lokasi .= "";*/
            $where_lokasi .= " and lokasi = '" . $lokasi . "'";
        }
        if (isset($supplier) && $supplier != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "(sp.kode = '" . $supplier . "')";
        }
        /*if (isset($search) && $search != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "(sp.kode like '%" . $search . "%' or sp.Nama like '%" . $search . "%')";
        }*/

        $data['lokasi'] = ($lokasi == null) ? '' : $lokasi;
        $data['tgl_awal'] = $date1;
        $data['tgl_akhir'] = $date2;

        $qty_stock = "(SELECT SUM(stock) FROM current_stock WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) <= '" . $date2 . "' " . $where_lokasi . ")";
        $value_stock = "(SELECT SUM(value) FROM current_stock WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) <= '" . $date2 . "' " . $where_lokasi . ")";
        $qty_beli = "(SELECT SUM(qty_beli) FROM budget_receive WHERE kode_supplier=sp.kode AND LEFT(CONVERT(VARCHAR, tgl_beli, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' " . $where_lokasi . ")";
        $value_beli = "(SELECT SUM(total_pembelian) FROM budget_receive WHERE kode_supplier=sp.kode AND LEFT(CONVERT(VARCHAR, tgl_beli, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' " . $where_lokasi . ")";
        $qty_jual = "(SELECT SUM(qty) FROM sales_budget WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' " . $where_lokasi . ")";
        $value_beli2 = "(SELECT SUM(val_beli) FROM sales_budget WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' " . $where_lokasi . ")";
        $value_jual = "(SELECT SUM(val_jual-disc_jual) FROM sales_budget WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' " . $where_lokasi . ")";
        $column = "isnull(" . $qty_stock . ", 0) qty_stock, isnull(" . $qty_jual . ", 0) qty_jual, isnull(" . $qty_beli . ", 0) qty_beli," . $value_stock . " value_stock," . $value_beli . " total_pembelian," . $value_beli2 . " value_beli," . $value_jual . " value_jual";

        $page=1;
        $limit = 100;
        $x = 100;
        $array = array();

        while ($x == 100) {
            $baca = $this->m_crud->select_limit('Supplier sp', "sp.kode, sp.Nama, " . $column, ($where == null ? null : $where), 'sp.kode ASC', 'sp.kode, sp.Nama', ($page-1)*$limit+1, ($limit*$page), $qty_beli . "<>0 OR " . $qty_stock . "<>0 OR " . $qty_jual . "<>0");
            $x = count($baca);
            $array = array_merge($array, $baca);
            $page++;
        }

        if ($param == null) {
            $no = 0;
            $csq = 0;
            $csv = 0;
            $rq = 0;
            $rv = 0;
            $sq = 0;
            $svc = 0;
            $svp = 0;
            $list = '';
            $total = '';
            foreach ($array as $row) {
                $no++;
                $list .= '
                <tr>
                    <td class="font_head">' . $no . '</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
                            <ul class="dropdown-menu" style="position: relative" role="menu">
                                <li><a href="#" onclick="detail_budget(\''.$row['kode'].'\')"><i class="md md-visibility"></i> Detail</a></li>
                                <li><a href="#" onclick="export_by_lokasi(\''.$row['kode'].'\')"><i class="md md-print"></i> Lokasi</a></li>
                            </ul>
                        </div>
                    </td>
                    <td class="font_head">' . $row['kode'] . '</td>
                    <td class="font_head">' . $row['Nama'] . '</td>
                    <td class="font_head">' . ($row['qty_stock'] - $row['qty_beli'] + 0) . '</td>
                    <td class="text-right font_head">' . number_format($row['value_stock'] - $row['total_pembelian'], 2) . '</td>
                    <td class="font_head">' . ($row['qty_beli'] + 0) . '</td>
                    <td class="text-right font_head">' . number_format($row['total_pembelian'], 2) . '</td>
                    <td class="font_head">' . ($row['qty_jual'] + 0) . '</td>
                    <td class="text-right font_head">' . number_format($row['value_beli'], 2) . '</td>
                    <td class="text-right font_head">' . number_format($row['value_jual'], 2) . '</td>
                    <td class="text-right font_head">' . number_format($row['value_jual'] - $row['value_beli'], 2) . '</td>
                    <td class="font_head">' . (($row['value_beli'] > 0 && $row['value_beli'] < $row['value_jual']) ? round((1 - ($row['value_beli'] / $row['value_jual'])) * 100, 2) : '0') . '</td>
                    <td>' . ($row['value_jual'] != 0 ? round($row['qty_stock'] / $row['qty_jual'], 2) : 0) . '</td>
                    <td>' . ($row['value_jual'] != 0 ? round($row['value_stock'] / $row['value_jual'], 2) : 0) . '</td>
                </tr>
                ';
                $csq = $csq + (int)$row['qty_stock'] - (int)$row['qty_beli'];
                $csv = $csv + $row['value_stock'] - $row['total_pembelian'];
                $rq = $rq + (int)$row['qty_beli'];
                $rv = $rv + $row['total_pembelian'];
                $sq = $sq + (int)$row['qty_jual'];
                $svc = $svc + $row['value_beli'];
                $svp = $svp + $row['value_jual'];
            }

            $margin = ($svc > 0 && $svc < $svp) ? round(((1 - ($svc / $svp)) * 100), 2) : 0;

            $total .= '
            <tr>
                <th class="font_head" colspan="4">TOTAL</th>
                <th class="font_head">' . $csq . '</th>
                <th class="text-right font_head">' . number_format($csv, 2) . '</th>
                <th class="font_head">' . $rq . '</th>
                <th class="text-right font_head">' . number_format($rv, 2) . '</th>
                <th class="font_head">' . $sq . '</th>
                <th class="text-right font_head">' . number_format($svc, 2) . '</th>
                <th class="text-right font_head">' . number_format($svp, 2) . '</th>
                <th class="text-right font_head">' . number_format($svp - $svc, 2) . '</th>
                <th class="font_head">' . $margin . '</th>
                <th></th>
                <th></th>
            </tr>
            ';

            echo json_encode(array('list_budget' => $list, 'total_budget' => $total));
        } else {
            $header = array(
                'merge' 	=> array('A1:M1','A2:M2','A3:M3','A5:A6','B5:B6','L5:L6','M5:M6','C5:D5','E5:F5','G5:K5'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:M6' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:M6' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => 'Budget Supplier'),
                '3' => array('A' => $date1.' - '.$date2),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Supplier', 'C'=>'Current Stock', 'E'=>'Receive', 'G'=>'Sales', 'L'=>'Qty Rasio', 'M'=>'Val Rasio'
                ),
                '6' => array(
                    'C'=>'Qty', 'D'=>'Value', 'E'=>'Qty', 'F'=>'Value', 'G'=>'Qty', 'H'=>'Val Cost', 'I'=>'Val Price', 'J'=>'Margin Rp', 'K'=>'Margin %'
                )
            );

            $i = 0;
            $csq = 0;
            $csv = 0;
            $rq = 0;
            $rv = 0;
            $sq = 0;
            $svc = 0;
            $svp = 0;

            foreach($array as $row => $value){
                $i++;
                $body[$row] = array(
                    $value['kode'], $value['Nama'], ($value['qty_stock']-$value['qty_beli']), ($value['value_stock']-$value['total_pembelian']), $value['qty_beli'], $value['total_pembelian'], $value['qty_jual'], $value['value_beli'], $value['value_jual'], $value['value_jual'] - $value['value_beli'], (($value['value_beli'] > 0 && $value['value_beli'] < $value['value_jual']) ? round((1 - ($value['value_beli'] / $value['value_jual'])) * 100, 2) : 0), ($value['qty_jual'] != 0 ? round($value['qty_stock'] / $value['qty_jual'], 2) : 0), ($value['value_jual'] != 0 ? round($value['value_stock'] / $value['value_jual'], 2) : 0)
                );
                $csq = $csq + (int)$value['qty_stock'] - (int)$value['qty_beli'];
                $csv = $csv + $value['value_stock'] - $value['total_pembelian'];
                $rq = $rq + (int)$value['qty_beli'];
                $rv = $rv + $value['total_pembelian'];
                $sq = $sq + (int)$value['qty_jual'];
                $svc = $svc + $value['value_beli'];
                $svp = $svp + $value['value_jual'];
            }

            $body[$i] = array('TOTAL', '', $csq, $csv, $rq, $rv, $sq, $svc, $svp, $svp - $svc, (($svc > 0 && $svc < $svp) ? round((1 - ($svc / $svp)) * 100, 2) : 0), ($sq != 0 ? round(((float)$csq+(float)$rq) / (float)$sq, 2) : 0), ($svp != 0 ? round(((float)$csv+(float)$rv) / (float)$svp, 2) : 0));
            array_push($header['merge'], 'A'.($i+7).':B'.($i+7).'');
            $header['font']['A'.($i+7).':L'.($i+7).''] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', 'Budget Supplier'), $header, $body);
        }
    }

    public function budget_supplier($action = null, $id = null){
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $this->access_denied(137);
        $data = $this->data;
        $function = 'budget_supplier';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Budget Supplier';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['lokasi'] = '';

        $where = null;
        $where_lokasi = null;

        if(isset($_POST['search'])||isset($_POST['to_excel'])) {
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date' => $_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'supplier' => $_POST['supplier']));

            $search = $this->session->search['any'];
            $lokasi = $this->session->search['lokasi'];
            $supplier = $this->session->search['supplier'];
            $date = $this->session->search['field-date'];
            $explode_date = explode(' - ', $date);

            if (isset($date) && $date != null) {
                $date1 = str_replace('/', '-', $explode_date[0]);
                $date2 = str_replace('/', '-', $explode_date[1]);
            } else {
                $date1 = date('Y-m-d');
                $date2 = date('Y-m-d');
            }
            if (isset($lokasi) && $lokasi != null) {
                /*($where_lokasi == null) ? null : $where_lokasi .= "";*/
                $where_lokasi .= " and lokasi = '" . $lokasi . "'";
            }
            if (isset($supplier) && $supplier != null) {
                ($where == null) ? null : $where .= " and ";
                $where .= "(sp.kode = '" . $supplier . "')";
            }
            /*if (isset($search) && $search != null) {
                ($where == null) ? null : $where .= " and ";
                $where .= "(sp.kode like '%" . $search . "%' or sp.Nama like '%" . $search . "%')";
            }*/

            $data['lokasi'] = ($lokasi == null) ? '' : $lokasi;
            $data['tgl_awal'] = $date1;
            $data['tgl_akhir'] = $date2;

            $qty_stock = "(SELECT SUM(stock) FROM current_stock WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) <= '" . $date2 . "' " . $where_lokasi . ")";
            $value_stock = "(SELECT SUM(value) FROM current_stock WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) <= '" . $date2 . "' " . $where_lokasi . ")";
            $qty_beli = "(SELECT SUM(qty_beli) FROM budget_receive WHERE kode_supplier=sp.kode AND LEFT(CONVERT(VARCHAR, tgl_beli, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' " . $where_lokasi . ")";
            $value_beli = "(SELECT SUM(total_pembelian) FROM budget_receive WHERE kode_supplier=sp.kode AND LEFT(CONVERT(VARCHAR, tgl_beli, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' " . $where_lokasi . ")";
            $qty_jual = "(SELECT SUM(qty) FROM sales_budget WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' " . $where_lokasi . ")";
            $value_beli2 = "(SELECT SUM(val_beli) FROM sales_budget WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' " . $where_lokasi . ")";
            $value_jual = "(SELECT SUM(val_jual-disc_jual) FROM sales_budget WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' " . $where_lokasi . ")";
            $column = "isnull(" . $qty_stock . ", 0) qty_stock, isnull(" . $qty_jual . ", 0) qty_jual, isnull(" . $qty_beli . ", 0) qty_beli," . $value_stock . " value_stock," . $value_beli . " total_pembelian," . $value_beli2 . " value_beli," . $value_jual . " value_jual";

            $page=1;
            $limit = 100;
            $x = 100;
            $array = array();

            while ($x == 100) {
                $baca = $this->m_crud->select_limit('Supplier sp', "sp.kode, sp.Nama, " . $column, ($where == null ? null : $where), 'sp.kode ASC', 'sp.kode, sp.Nama', ($page-1)*$limit+1, ($limit*$page), $qty_beli . "<>0 OR " . $qty_stock . "<>0 OR " . $qty_jual . "<>0");
                $x = count($baca);
                $array = array_merge($array, $baca);
                $page++;
            }

            $no = 0; $csq=0; $csv=0; $rq=0; $rv=0; $sq=0; $svc=0; $svp=0;
            $list = '';
            foreach ($array as $row) {
                $no++;
                $list .= '
                <tr>
                    <td class="font_head">'.$no.'</td>
                    <td class="font_head">'.$row['kode'].'</td>
                    <td class="font_head">'.$row['Nama'].'</td>
                    <td class="font_head">'.($row['qty_stock']-$row['qty_beli'] + 0).'</td>
                    <td class="text-right font_head">'.number_format($row['value_stock']-$row['total_pembelian'], 2).'</td>
                    <td class="font_head">'.($row['qty_beli'] + 0).'</td>
                    <td class="text-right font_head">'.number_format($row['total_pembelian'], 2).'</td>
                    <td class="font_head">'.($row['qty_jual'] + 0).'</td>
                    <td class="text-right font_head">'.number_format($row['value_beli'], 2).'</td>
                    <td class="text-right font_head">'.number_format($row['value_jual'], 2).'</td>
                    <td class="text-right font_head">'.number_format($row['value_jual'] - $row['value_beli'], 2).'</td>
                    <td class="font_head">'.(($row['value_beli'] > 0 && $row['value_beli'] < $row['value_jual']) ? round((1 - ($row['value_beli'] / $row['value_jual'])) * 100, 2) : '0').'</td>
                    <td>'.($row['value_jual'] != 0 ? round($row['value_stock'] / $row['value_jual']) : 0).'</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#" onclick="detail_budget(\''.$row['kode'].'\')"><i class="md md-visibility"></i> Detail</a></li>
                                <li><a href="#" onclick="export_by_lokasi(\''.$row['kode'].'\')"><i class="md md-print"></i> Lokasi</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                ';
                $csq = $csq + (int)$row['qty_stock']-(int)$row['qty_beli'];
                $csv = $csv + $row['value_stock']-$row['total_pembelian'];
                $rq = $rq + (int)$row['qty_beli'];
                $rv = $rv + $row['total_pembelian'];
                $sq = $sq + (int)$row['qty_jual'];
                $svc = $svc + $row['value_beli'];
                $svp = $svp + $row['value_jual'];
            }

            $total = '
            <tr>
                <th class="font_head" colspan="3">TOTAL</th>
                <th class="font_head">'.$csq.'</th>
                <th class="text-right font_head">'.number_format($csv, 2).'</th>
                <th class="font_head">'.$rq.'</th>
                <th class="text-right font_head">'.number_format($rv, 2).'</th>
                <th class="font_head">'.$sq.'</th>
                <th class="text-right font_head">'.number_format($svc, 2).'</th>
                <th class="text-right font_head">'.number_format($svp, 2).'</th>
                <th class="text-right font_head">'.number_format($svp - $svc, 2).'</th>
                <th class="font_head">'.($svc > 0 && $svc < $svp) ? round(((1 - ($svc / $svp)) * 100), 2) : '0'.'</th>
                <th></th>
                <th></th>
            </tr>
            ';

            echo json_encode(array('list_budget' => $list, 'total_budget' => $total));
            //$data['report'] = $array;
        } else {
            $data['report'] = '';
        }

        $csq=0; $csv=0; $rq=0; $rv=0; $sq=0; $svc=0; $svp=0;

        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->read_data('Supplier sp', "sp.kode, sp.Nama, ".$column, ($where==null?null:$where), 'sp.kode ASC', 'sp.kode, sp.Nama', 0, 0, $qty_beli."<>0 OR ".$qty_stock."<>0 OR ".$qty_jual."<>0");
            $header = array(
                'merge' 	=> array('A1:L1', 'A2:L2', 'A3:L3', 'A5:A6', 'B5:B6', 'C5:D5', 'E5:F5', 'G5:K5', 'L5:L6'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:L6' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:L6' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $date1.' - '.$date2),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama', 'C'=>'Current Stock', 'E'=>'Receive', 'G'=>'Sales', 'L'=>'Rasio'
                ),
                '6' => array(
                    'C'=>'Qty', 'D'=>'Value', 'E'=>'Qty', 'F'=>'Value', 'G'=>'Qty', 'H'=>'Val Cost', 'I'=>'Val Price', 'J'=>'Margin Rp', 'K'=>'Margin %'
                )
            );

            $end = 0;

            foreach($baca as $row => $value){
                $end++;
                $body[$row] = array(
                    $value['kode'], $value['Nama'], ($value['qty_stock']-$value['qty_beli']+0), number_format($value['value_stock']-$value['total_pembelian'], 2), ($value['qty_beli']+0), number_format($value['total_pembelian'],2), ($value['qty_jual']+0), number_format($value['value_beli'],2), number_format($value['value_jual'],2), number_format($value['value_jual']-$value['value_beli'],2), (($value['value_beli']>0 && $value['value_beli']<$value['value_jual'])?round((1 - ($value['value_beli']/$value['value_jual']))*100, 2):'0'), ($value['value_jual']!=0?round($value['value_stock']/$value['value_jual']):0)
                );
                $csq = $csq + (int)$value['qty_stock']-(int)$value['qty_beli'];
                $csv = $csv + $value['value_stock']-$row['total_pembelian'];
                $rq = $rq + (int)$value['qty_beli'];
                $rv = $rv + $value['total_pembelian'];
                $sq = $sq + (int)$value['qty_jual'];
                $svc = $svc + $value['value_beli'];
                $svp = $svp + $value['value_jual'];
            }

            $body[$end] = array('TOTAL','',($csq+0),number_format($csv,2),($rq+0),number_format($rv,2),($sq+0),number_format($svc,2),number_format($svp,2),number_format($svp-$svc,2),($svc>0 && $svc<$svp)?round((($svp-$svc) / $svp)*100, 2):'0');
            array_push($header['merge'], 'A'.($end+6).':B'.($end+6));
            $header['font']['A'.($end+7).':J'.($end+7)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;

            $data['report'] = $this->m_crud->get_data('Supplier', "kode, Nama", "kode = '".$id."'");

            $condition = "sp.kode='".$id."' AND sp.kode=mb.kode_supplier AND mb.no_faktur_beli=db.no_faktur_beli and (mb.tgl_beli >= '".$tgl_awal." 00:00:00' and mb.tgl_beli <= '".$tgl_akhir." 23:59:59')";
            ($lokasi==null)?null:$condition .= " and mb.lokasi='".$lokasi."'";
            $column = "mb.no_faktur_beli, mb.tgl_beli, mb.noNota, mb.type, mb.tgl_jatuh_tempo, SUM((db.Jumlah_Beli-db.Jumlah_Retur)*(db.Harga_Beli*(1-db.Diskon/100)*(1-db.Disc2/100)*(1-db.Disc3/100)*(1-db.Disc4/100)*(1+db.PPN/100))) nilai_pembelian";
            $data['report_detail'] = $this->m_crud->read_data("master_beli mb, det_beli db, Supplier sp", $column, $condition, "mb.no_faktur_beli ASC", "mb.no_faktur_beli, mb.tgl_beli, mb.noNota, mb.type, mb.tgl_jatuh_tempo");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_beli']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Detail Laporan Pembelian By Supplier</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="18%"></th>
                                <th width="2%"></th>
                                <th width="27%"></th>
                                
                                <th width="10%"></th>
                                <th width="12%"></th>
                                <th width="2%"></th>
                                <th width="27%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>Kode Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kode'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Nama Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['Nama'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>33,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function budget_supplier_back($action = null, $id = null){
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $this->access_denied(137);
        $data = $this->data;
        $function = 'budget_supplier';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Budget Supplier';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['lokasi'] = '';

        $where = null;
        $where_lokasi = null;

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);

        if (isset($date) && $date != null) {
            $date1 = str_replace('/','-',$explode_date[0]);
            $date2 = str_replace('/','-',$explode_date[1]);
        } else {
            $date1 = date('Y-m-d');
            $date2 = date('Y-m-d');
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where_lokasi==null)?null:$where_lokasi.=" and lokasi = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(sp.kode like '%".$search."%' or sp.Nama like '%".$search."%')"; }

        $data['lokasi'] = ($lokasi==null)?'':$lokasi;
        $data['tgl_awal'] = $date1;
        $data['tgl_akhir'] = $date2;

        $qty_stock = "(SELECT SUM(stock) FROM current_stock WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) <= '".$date2."' ".$where_lokasi.")";
        $value_stock = "(SELECT SUM(value) FROM current_stock WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) <= '".$date2."' ".$where_lokasi.")";
        $qty_beli = "(SELECT SUM(qty_beli) FROM budget_receive WHERE kode_supplier=sp.kode AND LEFT(CONVERT(VARCHAR, tgl_beli, 120),10) BETWEEN '".$date1."' AND '".$date2."' ".$where_lokasi.")";
        $value_beli = "(SELECT SUM(total_pembelian) FROM budget_receive WHERE kode_supplier=sp.kode AND LEFT(CONVERT(VARCHAR, tgl_beli, 120),10) BETWEEN '".$date1."' AND '".$date2."' ".$where_lokasi.")";
        $qty_jual = "(SELECT SUM(qty) FROM sales_budget WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '".$date1."' AND '".$date2."' ".$where_lokasi.")";
        $value_beli2 = "(SELECT SUM(val_beli) FROM sales_budget WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '".$date1."' AND '".$date2."' ".$where_lokasi.")";
        $value_jual = "(SELECT SUM(val_jual-disc_jual) FROM sales_budget WHERE Group1=sp.kode AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '".$date1."' AND '".$date2."' ".$where_lokasi.")";
        $column = "".$qty_stock." qty_stock,".$qty_jual." qty_jual,".$qty_beli." qty_beli,".$value_stock." value_stock,".$value_beli." total_pembelian,".$value_beli2." value_beli,".$value_jual." value_jual";

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data("Supplier sp", 'sp.kode', ($where==null?null:$where), null, "sp.kode", $qty_beli."<>0 OR ".$qty_stock."<>0 OR ".$qty_jual."<>0");
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

        $data['report'] = $this->m_crud->select_limit('Supplier sp', "sp.kode, sp.Nama, ".$column, ($where==null?null:$where), 'sp.kode ASC', 'sp.kode, sp.Nama', ($page-1)*$config['per_page']+1, ($config['per_page']*$page), $qty_beli."<>0 OR ".$qty_stock."<>0 OR ".$qty_jual."<>0");

        //$detail = $this->m_crud->read_data('Supplier sp', $column, ($where==null?null:$where), null, 'sp.kode, sp.Nama', 0, 0, $qty_beli."<>0 OR ".$qty_stock."<>0 OR ".$qty_jual."<>0");

        $data['detail'] = $this->db->query("
        with total as (
            select ".$column." from Supplier sp ".($where==null?'':'where '.$where)." group by sp.kode having ".$qty_beli."<>0 OR ".$qty_stock."<>0 OR ".$qty_jual."<>0
        )
        SELECT SUM(qty_stock) qty_stock, SUM(value_stock) value_stock, SUM(qty_beli) qty_beli, SUM(total_pembelian) total_pembelian, SUM(qty_jual) qty_jual, SUM(value_beli) value_beli, SUM(value_jual) value_jual FROM total
        ")->row_array();

        $csq=0; $csv=0; $rq=0; $rv=0; $sq=0; $svc=0; $svp=0;
        /*foreach ($detail as $row) {
            $csq = $csq + (int)$row['qty_stock'];
            $csv = $csv + $row['value_stock'];
            $rq = $rq + (int)$row['qty_beli'];
            $rv = $rv + $row['total_pembelian'];
            $sq = $sq + (int)$row['qty_jual'];
            $svc = $svc + $row['value_beli'];
            $svp = $svp + $row['value_jual'];
        }

        $data['tcsq'] = $csq;
        $data['tcsv'] = $csv;
        $data['trq'] = $rq;
        $data['trv'] = $rv;
        $data['tsq'] = $sq;
        $data['tsvc'] = $svc;
        $data['tsvp'] = $svp;*/

        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->read_data('Supplier sp', "sp.kode, sp.Nama, ".$column, ($where==null?null:$where), 'sp.kode ASC', 'sp.kode, sp.Nama', 0, 0, $qty_beli."<>0 OR ".$qty_stock."<>0 OR ".$qty_jual."<>0");
            $header = array(
                'merge' 	=> array('A1:L1', 'A2:L2', 'A3:L3', 'A5:A6', 'B5:B6', 'C5:D5', 'E5:F5', 'G5:K5', 'L5:L6'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:L6' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:L6' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $date1.' - '.$date2),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Nama', 'C'=>'Current Stock', 'E'=>'Receive', 'G'=>'Sales', 'L'=>'Rasio'
                ),
                '6' => array(
                    'C'=>'Qty', 'D'=>'Value', 'E'=>'Qty', 'F'=>'Value', 'G'=>'Qty', 'H'=>'Val Cost', 'I'=>'Val Price', 'J'=>'Margin Rp', 'K'=>'Margin %'
                )
            );

            $end = 0;

            foreach($baca as $row => $value){
                $end++;
                $body[$row] = array(
                    $value['kode'], $value['Nama'], ($value['qty_stock']+0), number_format($value['value_stock'], 2), ($value['qty_beli']+0), number_format($value['total_pembelian'],2), ($value['qty_jual']+0), number_format($value['value_beli'],2), number_format($value['value_jual'],2), number_format($value['value_jual']-$value['value_beli'],2), (($value['value_beli']>0 && $value['value_beli']<$value['value_jual'])?round((1 - ($value['value_beli']/$value['value_jual']))*100, 2):'0'), ($value['value_jual']!=0?round($value['value_stock']/$value['value_jual']):0)
                );
            }

            $body[$end] = array('TOTAL',($data['detail']['qty_stock']+0),number_format($data['detail']['value_stock'],2),($data['detail']['qty_beli']+0),number_format($data['detail']['total_pembelian'],2),($data['detail']['qty_jual']+0),number_format($data['detail']['value_beli'],2),number_format($data['detail']['value_jual'],2),number_format($data['detail']['value_jual']-$data['detail']['value_beli'],2),($data['detail']['value_beli']>0 && $data['detail']['value_beli']<$data['detail']['value_jual'])?round((($data['detail']['value_jual']-$data['detail']['value_beli']) / $data['detail']['value_jual'])*100, 2):'0');
            $header['font']['A'.($end+7).':J'.($end+7)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;

            $data['report'] = $this->m_crud->get_data('Supplier', "kode, Nama", "kode = '".$id."'");

            $condition = "sp.kode='".$id."' AND sp.kode=mb.kode_supplier AND mb.no_faktur_beli=db.no_faktur_beli and (mb.tgl_beli >= '".$tgl_awal." 00:00:00' and mb.tgl_beli <= '".$tgl_akhir." 23:59:59')";
            ($lokasi==null)?null:$condition .= " and mb.lokasi='".$lokasi."'";
            $column = "mb.no_faktur_beli, mb.tgl_beli, mb.noNota, mb.type, mb.tgl_jatuh_tempo, SUM((db.Jumlah_Beli-db.Jumlah_Retur)*(db.Harga_Beli*(1-db.Diskon/100)*(1-db.Disc2/100)*(1-db.Disc3/100)*(1-db.Disc4/100)*(1+db.PPN/100))) nilai_pembelian";
            $data['report_detail'] = $this->m_crud->read_data("master_beli mb, det_beli db, Supplier sp", $column, $condition, "mb.no_faktur_beli ASC", "mb.no_faktur_beli, mb.tgl_beli, mb.noNota, mb.type, mb.tgl_jatuh_tempo");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_beli']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Detail Laporan Pembelian By Supplier</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="18%"></th>
                                <th width="2%"></th>
                                <th width="27%"></th>
                                
                                <th width="10%"></th>
                                <th width="12%"></th>
                                <th width="2%"></th>
                                <th width="27%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>Kode Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kode'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Nama Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['Nama'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>33,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function detail_budget($kode, $periode, $lokasi, $param = null) {
        $list_barang = '';
        $where_lokasi = null;
        $param = base64_decode($param);
        $lokasi = base64_decode($lokasi);
        $kode = base64_decode($kode);
        $explode_date = explode(' - ', base64_decode($periode));
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        if($lokasi != '-'){
            $where_lokasi.=" and lokasi = '".$lokasi."'";
            $ket_lokasi = 'Lokasi : '.$lokasi;
        } else {
            $ket_lokasi = 'Lokasi : Semua Lokasi';
        }

        $qty_stock = "(SELECT SUM(stock) FROM stock_report WHERE kd_brg = br.kd_brg AND LEFT(CONVERT(VARCHAR, tgl, 120),10) <= '".$date2."' ".$where_lokasi.")";
        $value_stock = "(SELECT SUM(value) FROM stock_report WHERE kd_brg = br.kd_brg AND LEFT(CONVERT(VARCHAR, tgl, 120),10) <= '".$date2."' ".$where_lokasi.")";
        $qty_beli = "(SELECT SUM(jumlah_beli) FROM pembelian_report WHERE kd_brg = br.kd_brg AND LEFT(CONVERT(VARCHAR, tgl_beli, 120),10) BETWEEN '".$date1."' AND '".$date2."' ".$where_lokasi.")";
        $value_beli = "(SELECT SUM(sub_total)-SUM(sub_total * CONVERT(decimal(38, 8), disc)/isnull(NULLIF(total_beli, 0), 1))+SUM((sub_total - (sub_total * CONVERT(decimal(38, 8), disc)/isnull(NULLIF(total_beli, 0), 1))) * CONVERT(decimal(38, 8), ppn)/isnull(NULLIF(total_beli, 0), 1)) FROM pembelian_report WHERE kd_brg = br.kd_brg AND LEFT(CONVERT(VARCHAR, tgl_beli, 120),10) BETWEEN '".$date1."' AND '".$date2."' ".$where_lokasi.")";
        $qty_jual = "(SELECT SUM(qty) FROM sales_budget WHERE kd_brg = br.kd_brg AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '".$date1."' AND '".$date2."' ".$where_lokasi.")";
        $value_beli2 = "(SELECT SUM(val_beli) FROM sales_budget WHERE kd_brg = br.kd_brg AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '".$date1."' AND '".$date2."' ".$where_lokasi.")";
        $value_jual = "(SELECT SUM(val_jual-disc_jual) FROM sales_budget WHERE kd_brg = br.kd_brg AND LEFT(CONVERT(VARCHAR, tgl, 120),10) BETWEEN '".$date1."' AND '".$date2."' ".$where_lokasi.")";
        $column = "isnull(".$qty_stock.", 0) qty_stock, isnull(".$qty_jual.", 0) qty_jual, isnull(".$qty_beli.", 0) qty_beli,".$value_stock." value_stock,".$value_beli." total_pembelian,".$value_beli2." value_beli, isnull(".$value_jual.", 0) value_jual";

        $read_data = $this->m_crud->read_data("barang br", "br.kd_brg, br.barcode, br.nm_brg, ".$column, "br.Group1='".$kode."'", "br.kd_brg asc", "br.kd_brg, br.barcode, br.nm_brg", 0, 0, $qty_beli."<>0 OR ".$qty_stock."<>0 OR ".$qty_jual."<>0");
        $no = 1; $csq=0; $csv=0; $rq=0; $rv=0; $sq=0; $svc=0; $svp=0;
        if ($param != 'export') {
            foreach ($read_data as $row) {
                $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td>' . $row['kd_brg'] . '</td>
                                <td>' . $row['barcode'] . '</td>
                                <td>' . $row['nm_brg'] . '</td>
                                <td>' . ((float)$row['qty_stock'] - (float)$row['qty_beli']) . '</td>
                                <td style="text-align: right">' . number_format($row['value_stock'] - $row['total_pembelian'], 2) . '</td>
                                <td>' . (float)$row['qty_beli'] . '</td>
                                <td style="text-align: right">' . number_format($row['total_pembelian'], 2) . '</td>
                                <td>' . (float)$row['qty_jual'] . '</td>
                                <td style="text-align: right">' . number_format($row['value_beli'], 2) . '</td>
                                <td style="text-align: right">' . number_format($row['value_jual'], 2) . '</td>
                                <td style="text-align: right">' . number_format($row['value_jual'] - $row['value_beli'], 2) . '</td>
                                <td>' . (($row['value_beli'] > 0 && $row['value_beli'] < $row['value_jual']) ? round((1 - ($row['value_beli'] / $row['value_jual'])) * 100, 2) : 0) . '</td>
                                <td>' . ($row['qty_jual'] != 0 ? round($row['qty_stock'] / $row['qty_jual'], 2) : 0) . '</td>
                                <td>' . ($row['value_jual'] != 0 ? round($row['value_stock'] / $row['value_jual'], 2) : 0) . '</td>
                            </tr>';
                $no++;
                $csq = $csq + (int)$row['qty_stock'] - (int)$row['qty_beli'];
                $csv = $csv + $row['value_stock'] - $row['total_pembelian'];
                $rq = $rq + (int)$row['qty_beli'];
                $rv = $rv + $row['total_pembelian'];
                $sq = $sq + (int)$row['qty_jual'];
                $svc = $svc + $row['value_beli'];
                $svp = $svp + $row['value_jual'];
            }
            $list_barang .= '<tr>
                            <th colspan="4">TOTAL</th>
                            <th>' . (float)$csq . '</th>
                            <th style="text-align: right">' . number_format($csv, 2) . '</th>
                            <th>' . (float)$rq . '</th>
                            <th style="text-align: right">' . number_format($rv, 2) . '</th>
                            <th>' . (float)$sq . '</th>
                            <th style="text-align: right">' . number_format($svc, 2) . '</th>
                            <th style="text-align: right">' . number_format($svp, 2) . '</th>
                            <th style="text-align: right">' . number_format($svp - $svc, 2) . '</th>
                            <th>' . (($svc > 0 && $svc < $svp) ? round((1 - ($svc / $svp)) * 100, 2) : 0) . '</th>
                            <th>' . ($sq != 0 ? round(((float)$csq+(float)$rq) / (float)$sq, 2) : 0) . '</th>
                            <th>' . ($svp != 0 ? round(((float)$csv+(float)$rv) / (float)$svp, 2) : 0) . '</th>
                        </tr>';
            echo json_encode(array('list_barang' => $list_barang, 'kode' => $kode, 'nama' => $this->db->query("select Nama from Supplier where kode='" . $kode . "'")->row_array()['Nama']));
        } else {
            $nm_supp = $this->m_crud->get_data("Supplier", "Nama", "Kode='".$kode."'");
            $data = $this->data;
            $data['det_report'] = $read_data;
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:M1','A2:M2','A3:M3','A5:A6','B5:B6','C5:C6','M5:M6','N5:N6','D5:E5','F5:G5','H5:L5','A4:C4','K4:N4'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:N6' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:N6' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'K4:N4' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => 'Detail Budget'),
                '3' => array('A' => $date1.' - '.$date2),
                '4' => array('A'=>'Supplier : '.$nm_supp['Nama'], 'K'=>$ket_lokasi),
                '5' => array(
                    'A'=>'Kode Barang', 'B'=>'Barcode', 'C'=>'Nama Barang', 'D'=>'Current Stock', 'F'=>'Receive', 'H'=>'Sales', 'M'=>'Qty Rasio', 'N'=>'Val Rasio'
                ),
                '6' => array(
                    'D'=>'Qty', 'E'=>'Value', 'F'=>'Qty', 'G'=>'Value', 'H'=>'Qty', 'I'=>'Val Cost', 'J'=>'Val Price', 'K'=>'Margin Rp', 'L'=>'Margin %'
                )
            );

            $i = 0;
            foreach($baca as $row => $value){
                $i++;
                $body[$row] = array(
                    $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['qty_stock'], $value['value_stock'], $value['qty_beli'], $value['total_pembelian'], $value['qty_jual'], $value['value_beli'], $value['value_jual'], $value['value_jual'] - $value['value_beli'], (($value['value_beli'] > 0 && $value['value_beli'] < $value['value_jual']) ? round((1 - ($value['value_beli'] / $value['value_jual'])) * 100, 2) : 0), ($value['qty_jual'] != 0 ? round($value['qty_stock'] / $value['qty_jual']) : 0), ($value['value_jual'] != 0 ? round($value['value_stock'] / $value['value_jual']) : 0)
                );
                $csq = $csq + (int)$value['qty_stock'] - (int)$value['qty_beli'];
                $csv = $csv + $value['value_stock'] - $value['total_pembelian'];
                $rq = $rq + (int)$value['qty_beli'];
                $rv = $rv + $value['total_pembelian'];
                $sq = $sq + (int)$value['qty_jual'];
                $svc = $svc + $value['value_beli'];
                $svp = $svp + $value['value_jual'];
            }

            $body[$i] = array('TOTAL', '', '', $csq, $csv, $rq, $rv, $sq, $svc, $svp, $svp - $svc, (($svc > 0 && $svc < $svp) ? round((1 - ($svc / $svp)) * 100, 2) : 0), ($sq != 0 ? round(((float)$csq+(float)$rq) / (float)$sq, 2) : 0), ($svp != 0 ? round(((float)$csv+(float)$rv) / (float)$svp, 2) : 0));
            array_push($header['merge'], 'A'.($i+7).':C'.($i+7).'');
            $header['font']['A'.($i+7).':M'.($i+7).''] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', 'Detail Budget'), $header, $body);
        }
    }
    /*End modul report*/
    
    /*Start modul pembelian by operator*/
    public function pembelian_by_operator($action = null, $id = null){
        $this->access_denied(142);
        $data = $this->data;
        $function = 'pembelian_by_operator';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Pembelian By Operator';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        if ($action == 'get_data') {
            $this->session->set_userdata('search', array('tgl_periode'=>$_POST['tgl'], 'lokasi' => $_POST['lokasi']));
            $lokasi = $this->session->search['lokasi']; $date = $this->session->search['tgl_periode'];

            $where = "CONVERT(DATE, tgl_beli) = '".$date."'";
            if (isset($lokasi) && $lokasi!=null) { $where==null?null:$where.=" AND "; $where .= "lokasi='".$lokasi."'"; }

            $pajak = "(select sum(ppn) from master_beli where operator = pr.operator and ".$where.")";
            $diskon = "(select sum(disc) from master_beli where operator = pr.operator and ".$where.")";
            $get_data = $this->m_crud->join_data("pembelian_report pr", "nama, operator, convert(float, ".$diskon.") disc, convert(float, ".$pajak.") ppn, count(distinct no_faktur_beli) trx, sum(jumlah_beli) qty, convert(float, isnull(sum(jumlah_bonus), 0)) bonus, sum(sub_total) total", "user_detail ud", "ud.user_id=pr.operator", $where, null, "nama, operator");

            if ($get_data != null) {
                $result = array('status'=>true, 'data'=>$get_data);
            } else {
                $result = array('status'=>false);
            }

            echo json_encode($result);
        } else if ($action == 'export') {
            $lokasi = $this->session->search['lokasi']; $date = $this->session->search['tgl_periode'];

            $where = "CONVERT(DATE, tgl_beli) = '".$date."'";
            $head_lokasi = "Semua Lokasi";
            if (isset($lokasi) && $lokasi!=null) { $where==null?null:$where.=" AND "; $where .= "lokasi='".$lokasi."'"; $head_lokasi = $lokasi; }

            $pajak = "(select sum(ppn) from master_beli where operator = pr.operator and ".$where.")";
            $diskon = "(select sum(disc) from master_beli where operator = pr.operator and ".$where.")";
            $data['report_detail'] = $this->m_crud->join_data("pembelian_report pr", "nama, operator, convert(float, ".$diskon.") disc, convert(float, ".$pajak.") ppn, count(distinct no_faktur_beli) trx, sum(jumlah_beli) qty, convert(float, isnull(sum(jumlah_bonus), 0)) bonus, sum(sub_total) total", "user_detail ud", "ud.user_id=pr.operator", $where, null, "nama, operator");
            $data['content'] = $view.'pdf_'.$function;

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = "Pembelian By Operator";
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Laporan Pembelian By Operator</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="15%"></th>
									<th width="2%"></th>
									<th width="30%"></th>
									
									<th width="10%"></th>
									<th width="12%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Tanggal</b></td>
									<td><b>:</b></td>
									<td>'.$date.'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Lokasi</b></td>
									<td><b>:</b></td>
									<td>'.$head_lokasi.'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>50,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        } else if ($action == 'detail') {
            $operator = $_POST['operator'];
            $lokasi = $this->session->search['lokasi'];
            $date = $this->session->search['tgl_periode'];

            $where = "pr.operator='".$operator."' AND CONVERT(DATE, pr.tgl_beli) = '".$date."'";
            $lokasi==''?null:$where.=" AND pr.Lokasi='".$lokasi."'";
            $list = '';

            $join = array(array('table'=>'Supplier sp', 'type'=>'LEFT'), array('table'=>'Lokasi lk', 'type'=>'LEFT'), array('table'=>'user_detail ud', 'type'=>'LEFT'));
            $on = array('pr.kode_supplier=sp.kode', 'pr.Lokasi=lk.Kode', 'pr.Operator=ud.user_id');
            $column = "pr.no_faktur_beli, pr.tgl_beli, pr.noNota, pr.nama_penerima, pr.type, pr.Pelunasan, pr.disc, pr.ppn, sp.Kode kode_supplier, sp.Nama supplier, ud.Nama operator, pr.lokasi kd_lokasi, lk.Nama lokasi, lk.serial";
            $group = "pr.no_faktur_beli, pr.tgl_beli, pr.noNota, pr.nama_penerima, pr.type, pr.Pelunasan, pr.disc, pr.ppn, sp.Kode, sp.Nama, ud.Nama, lk.Nama, pr.lokasi, lk.serial, pr.operator";
            $get_data = $this->m_crud->join_data("pembelian_report pr", $column." ,SUM(jumlah_beli) qty_beli, SUM(sub_total)-disc+ppn total_beli", $join, $on, $where, 'pr.no_faktur_beli DESC', $group);

            $total = 0;
            foreach ($get_data as $key => $item) {
                $total = $total + (float)$item['total_beli'];
                $list .= '
                    <tr>
                        <td>'.($key + 1).'</td>
                        <td>'.date('Y-m-d', strtotime($item['tgl_beli'])).'</td>
                        <td>'.$item['no_faktur_beli'].'</td>
                        <td>'.$item['noNota'].'</td>
                        <td>'.$item['type'].'</td>
                        <td>'.$item['Pelunasan'].'</td>
                        <td>'.$item['supplier'].'</td>
                        <td>'.$item['lokasi'].'</td>
                        <td>'.$item['nama_penerima'].'</td>
                        <td class="text-right">'.number_format($item['disc']).'</td>
                        <td class="text-right">'.number_format($item['ppn']).'</td>
                        <td class="text-right">'.number_format($item['total_beli'], 2).'</td>
                    </tr>
                ';
            }

            $list .= '
                <tr>
                    <th colspan="11">Total</th>
                    <th class="text-right">'.number_format($total, 2).'</th>
                </tr>
            ';

            echo json_encode(array('list'=>$list, 'tgl'=>$date, 'operator'=>$this->m_website->get_nama_user($operator)));
        } else if ($action == 'to_pdf') {
            $operator = base64_decode($id);
            $lokasi = $this->session->search['lokasi'];
            $date = $this->session->search['tgl_periode'];

            $where = "pr.operator='".$operator."' AND CONVERT(DATE, pr.tgl_beli) = '".$date."'";
            $lokasi==''?null:$where.=" AND pr.Lokasi='".$lokasi."'";
            $list = '';

            $join = array(array('table'=>'Supplier sp', 'type'=>'LEFT'), array('table'=>'Lokasi lk', 'type'=>'LEFT'), array('table'=>'user_detail ud', 'type'=>'LEFT'));
            $on = array('pr.kode_supplier=sp.kode', 'pr.Lokasi=lk.Kode', 'pr.Operator=ud.user_id');
            $column = "pr.no_faktur_beli, pr.tgl_beli, pr.noNota, pr.nama_penerima, pr.type, pr.Pelunasan, pr.disc, pr.ppn, sp.Kode kode_supplier, sp.Nama supplier, ud.Nama operator, pr.lokasi kd_lokasi, lk.Nama lokasi, lk.serial";
            $group = "pr.no_faktur_beli, pr.tgl_beli, pr.noNota, pr.nama_penerima, pr.type, pr.Pelunasan, pr.disc, pr.ppn, sp.Kode, sp.Nama, ud.Nama, lk.Nama, pr.lokasi, lk.serial, pr.operator";
            $data['report_detail'] = $this->m_crud->join_data("pembelian_report pr", $column." ,SUM(jumlah_beli) qty_beli, SUM(sub_total)-disc+ppn total_beli", $join, $on, $where, 'pr.no_faktur_beli DESC', $group);
            $data['content'] = $view.'pdf_invoice_'.$function;

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = "Detail Pembelian ".$this->m_website->get_nama_user($operator);
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Detail Laporan Pembelian By Operator</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="15%"></th>
									<th width="2%"></th>
									<th width="30%"></th>
									
									<th width="10%"></th>
									<th width="12%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Tanggal</b></td>
									<td><b>:</b></td>
									<td>'.$date.'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Opertaor</b></td>
									<td><b>:</b></td>
									<td>'.$this->m_website->get_nama_user($operator).'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>50,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        } else {
            $this->load->view('bo/index', $data);
        }
    }
    /*End modul pembelian by operator*/
	
	/*Start modul purchase order mingguan*/
	public function po_mingguan() {
        $this->access_denied(56);
        $data = $this->data;
        $function = 'po_mingguan';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'PO Mingguan';
        $data['page'] = $function;
        $data['content'] = $view.$function;
		
		if(isset($_GET['trx'])){ 
			$this->edit_po_mingguan($_GET['trx']); 
		} 
		
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, Nama, serial');
        $data['data_supplier'] = $this->m_crud->read_data('Supplier','Kode, Nama');

        $this->load->view('bo/index', $data);
    }
	
	public function edit_po_mingguan($id){
		$id = base64_decode($id);
		
		$this->db->trans_begin();
		
		$this->m_crud->delete_data('tr_temp_m', "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");
		$this->m_crud->delete_data('tr_temp_d', "(SUBSTRING(d1,1,2) = 'PO') AND (d11 = '".$this->user."')");
		
		$m = $this->m_crud->get_data('master_po', '*', "no_po='".$id."' and status='0'");
		$data = array(
            'm1' => 'PO',
            'm2' => date('Y-m-d', strtotime($m['tgl_po'])),
            'm3' => date('Y-m-d', strtotime($m['tglkirim'])),
            'm4' => $m['lokasi'].'|'.substr($m['no_po'],14,2),
            'm5' => $m['kode_supplier'],
            'm6' => 0,
            'm7' => $m['catatan'],
            'm8' => $this->user,
			'm9' => 1,
			'm10' => date('Y-m-d', strtotime($m['tgl_po']))
        );
		$this->m_crud->create_data('tr_temp_m', $data);
				
		$where_stock = " AND stk.lokasi NOT IN ('MUTASI', 'Retur', 'HO') and stk.lokasi <> '' and stk.lokasi is not null and stk.lokasi = '".$m['lokasi']."'";
        $q_tgl = " BETWEEN '".date('Y-m-d', strtotime($m['tgl_po']))." 00:00:00' AND '".date('Y-m-d', strtotime($m['tgl_po']))." 23:59:59'";
        
		$stock_awal = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE keterangan not like '%Adjustment%' AND kd_brg=d2 AND left(convert(varchar, tgl, 120), 10)<'".$tgl_awal."'".$where_stock.") ,0) stock_awal";
		$stock_masuk = "ISNULL((SELECT SUM(stock_in) FROM Kartu_stock stk WHERE keterangan not like '%Adjustment%' AND keterangan not like '%Mutasi%' AND kd_brg=d2 AND tgl ".$q_tgl.$where_stock.") ,0) stock_masuk";
		$jumlah_retur = "ISNULL((SELECT SUM(stock_out) FROM Kartu_stock stk WHERE kd_brg=d2 AND keterangan='Retur Pembelian' AND tgl ".$q_tgl.$where_stock."), 0) jumlah_retur";
		$jumlah_adjust = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE kd_brg=d2 AND keterangan like '%Adjustment%' AND tgl ".$q_tgl.$where_stock."), 0) jumlah_adjust";
		$jumlah_mutasi = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE kd_brg=d2 AND (keterangan like '%Mutasi%' or keterangan like '%Retur Non Approval%') AND tgl ".$q_tgl.$where_stock."), 0) jumlah_mutasi";
		$jumlah_jual = "ISNULL((SELECT SUM(qty_jual) FROM detail_penjualan stk WHERE kd_brg=d2 AND tgl ".$q_tgl.$where_stock."), 0) jumlah_jual";
		
		$dd = $this->m_crud->join_data('detail_po', "detail_po.*, ".$stock_awal.", ".$stock_masuk.", ".$jumlah_retur.", ".$jumlah_adjust.", ".$jumlah_mutasi.", ".$jumlah_jual.", br.Deskripsi, br.satuan, br.nm_brg, br.kd_packing", array('master_order','barang br'), array('master_order.no_order=det_order.no_order','det_order.kd_brg=br.kd_brg'), "master_order.no_order='".$m['no_order']."'");
		$no=0;
		foreach($dd as $d){ $no++;
			$data = array(
				'd1' => 'PO',
				'd2' => $d['kode_barang'],
				'd3' => $d['harga_beli'],
				'd4' => 0,
				'd5' => 0,
				'd6' => 0,
				'd7' => 0,
				'd8' => $d['PPN'],
				'd9' => $d['jumlah_beli'],
				'd10' => $d['kode_barang'],
				'd11' => $this->user,
				'd12' => $d['Deskripsi'],
				'd13' => $d['satuan'],
				'd14' => $d['harga_jual'],
				'd15' => $no,
				'd16' => $d['nm_brg'],
				'd18' => $d['kd_packing'],
				'd19' => $d['stock_awal']+0,
				'd20' => $d['stock_masuk']+0,
				'd21' => $d['jumlah_retur']+0,
				'd22' => $d['jumlah_adjust']+0,
				'd23' => $d['jumlah_mutasi']+0,
				'd24' => $d['jumlah_jual']+0
			);
			$this->m_crud->create_data('tr_temp_d', $data);
		}
		
		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
			$this->db->trans_commit();
        }
	}
	
	public function hapus_barang_po_mingguan(){
		$this->db->trans_begin();
		
		$this->m_crud->delete_data('tr_temp_d', "(SUBSTRING(d1,1,2) = 'PO') AND (d11 = '".$this->user."')");
		
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			echo json_encode(array('status'=>0));
		} else {
			$this->db->trans_commit();
			echo json_encode(array('status'=>1));
		}
	}
	
	public function add_barang_po_mingguan(){
		$lokasi = explode('|', $_POST['lokasi'])[0];
		$group1 = $_POST['group1'];
		$tgl_awal = $_POST['tgl_awal'];
		$tgl_akhir = $_POST['tgl_akhir'];
		
		$this->db->trans_begin();

		//$this->m_crud->delete_data('tr_temp_m', "(m8 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'PO')");
		$this->m_crud->delete_data('tr_temp_d', "(SUBSTRING(d1,1,2) = 'PO') AND (d11 = '".$this->user."')");
		
		$where_stock = " AND stk.lokasi NOT IN ('MUTASI', 'Retur', 'HO') and stk.lokasi <> '' and stk.lokasi is not null and stk.lokasi = '".$lokasi."'";
        $q_tgl = " BETWEEN '".date('Y-m-d', strtotime($tgl_awal))." 00:00:00' AND '".date('Y-m-d', strtotime($tgl_akhir))." 23:59:59'";
        
		$stock_awal = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE keterangan not like '%Adjustment%' AND kd_brg=br.kd_brg AND left(convert(varchar, tgl, 120), 10)<'".$tgl_awal."'".$where_stock.") ,0) stock_awal";
		$stock_masuk = "ISNULL((SELECT SUM(stock_in) FROM Kartu_stock stk WHERE keterangan not like '%Adjustment%' AND keterangan not like '%Mutasi%' AND kd_brg=br.kd_brg AND tgl ".$q_tgl.$where_stock.") ,0) stock_masuk";
		$jumlah_retur = "ISNULL((SELECT SUM(stock_out) FROM Kartu_stock stk WHERE kd_brg=br.kd_brg AND keterangan='Retur Pembelian' AND tgl ".$q_tgl.$where_stock."), 0) jumlah_retur";
		$jumlah_adjust = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE kd_brg=br.kd_brg AND keterangan like '%Adjustment%' AND tgl ".$q_tgl.$where_stock."), 0) jumlah_adjust";
		$jumlah_mutasi = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE kd_brg=br.kd_brg AND (keterangan like '%Mutasi%' or keterangan like '%Retur Non Approval%') AND tgl ".$q_tgl.$where_stock."), 0) jumlah_mutasi";
		$jumlah_jual = "ISNULL((SELECT SUM(qty_jual) FROM detail_penjualan stk WHERE kd_brg=br.kd_brg AND tgl ".$q_tgl.$where_stock."), 0) jumlah_jual";
		
		$dd = $this->m_crud->join_data('barang br', "br.hrg_beli, br.ppn, 0 qty, br.kd_brg, br.hrg_jual_1 hrg_jual, br.ppn, br.Deskripsi, br.satuan, br.nm_brg, br.kd_packing, ".$stock_awal.", ".$stock_masuk.", ".$jumlah_retur.", ".$jumlah_adjust.", ".$jumlah_mutasi.", ".$jumlah_jual, 
			array('barang_hrg bh'), array('br.kd_brg=bh.barang'), "bh.lokasi = '".$lokasi."' and br.group1 = '".$group1."'", 
			null, "br.kd_brg, bh.lokasi, bh.stock_min, br.hrg_beli, br.ppn, br.hrg_jual_1, br.deskripsi, br.satuan, br.nm_brg, br.kd_packing", null, null
		);
		$no=0;
		foreach($dd as $d){ $no++;
			$data = array(
				'd1' => 'PO',
				'd2' => $d['kd_brg'],
				'd3' => $d['hrg_beli']+0,
				'd4' => 0,
				'd5' => 0,
				'd6' => 0,
				'd7' => 0,
				'd8' => $d['ppn']+0,
				'd9' => $d['qty'],
				'd10' => $d['kd_brg'],
				'd11' => $this->user,
				'd12' => $d['Deskripsi'],
				'd13' => $d['satuan'],
				'd14' => $d['hrg_jual']+0,
				'd15' => $no,
				'd16' => $d['nm_brg'],
				'd18' => $d['kd_packing'],
				'd19' => $d['stock_awal']+0,
				'd20' => $d['stock_masuk']+0,
				'd21' => $d['jumlah_retur']+0,
				'd22' => $d['jumlah_adjust']+0,
				'd23' => $d['jumlah_mutasi']+0,
				'd24' => $d['jumlah_jual']+0
			);
			$this->m_crud->create_data('tr_temp_d', $data);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			echo json_encode(array('status'=>0));
		} else {
			$this->db->trans_commit();
			echo json_encode(array('status'=>1));
		}
	}
	
	public function get_tr_temp_d_po_mingguan() {
        $lokasi = explode('|', $_POST['lokasi'])[0];
		$tgl_awal = $_POST['tgl_awal'];
		$tgl_akhir = $_POST['tgl_akhir'];
		$list_barang = '';
        //$read_data = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')", "CONVERT(INTEGER, d17) ASC");
		
		$where_stock = " AND stk.lokasi NOT IN ('MUTASI', 'Retur', 'HO') and stk.lokasi <> '' and stk.lokasi is not null and stk.lokasi = '".$lokasi."'";
        $q_tgl = " BETWEEN '".$tgl_awal." 00:00:00' AND '".$tgl_akhir." 23:59:59'";
        
		$stock_awal = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE keterangan not like '%Adjustment%' AND kd_brg=d2 AND left(convert(varchar, tgl, 120), 10)<'".$tgl_awal."'".$where_stock.") ,0)";
        $stock_awal = "isnull((d19),(".$stock_awal.")) stock_awal";
		$stock_masuk = "ISNULL((SELECT SUM(stock_in) FROM Kartu_stock stk WHERE keterangan not like '%Adjustment%' AND keterangan not like '%Mutasi%' AND kd_brg=d2 AND tgl ".$q_tgl.$where_stock.") ,0)";
       	$stock_masuk = "isnull((d20),(".$stock_masuk.")) stock_masuk";
		$jumlah_retur = "ISNULL((SELECT SUM(stock_out) FROM Kartu_stock stk WHERE kd_brg=d2 AND keterangan='Retur Pembelian' AND tgl ".$q_tgl.$where_stock."), 0)";
        $jumlah_retur = "isnull((d21),(".$jumlah_retur.")) jumlah_retur";
		$jumlah_adjust = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE kd_brg=d2 AND keterangan like '%Adjustment%' AND tgl ".$q_tgl.$where_stock."), 0)";
        $jumlah_adjust = "isnull((d22),(".$jumlah_adjust.")) jumlah_adjust";
		$jumlah_mutasi = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE kd_brg=d2 AND (keterangan like '%Mutasi%' or keterangan like '%Retur Non Approval%') AND tgl ".$q_tgl.$where_stock."), 0)";
        $jumlah_mutasi = "isnull((d23),(".$jumlah_mutasi.")) jumlah_mutasi";
		$jumlah_jual = "ISNULL((SELECT SUM(qty_jual) FROM detail_penjualan stk WHERE kd_brg=d2 AND tgl ".$q_tgl.$where_stock."), 0)";
        $jumlah_jual = "isnull((d24),(".$jumlah_jual.")) jumlah_jual";
		
		$read_data = $this->m_crud->read_data('tr_temp_d', "tr_temp_d.*, ".$stock_awal.", ".$stock_masuk.", ".$jumlah_retur.", ".$jumlah_adjust.", ".$jumlah_mutasi.", ".$jumlah_jual, 
			//array('barang_hrg'), array("d2=barang"), 
			"(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')", "CONVERT(INTEGER, d17) ASC"
		);
        
		$no = 1;
        $col = 0;
        $sub_total = 0;
        $length = count($read_data);
        foreach ($read_data as $row) {
			if($row['d19']==null){ 
				$this->m_crud->update_data('tr_temp_d', array(
					'd19'=>$row['stock_awal'],
					'd20'=>$row['stock_masuk'],
					'd21'=>$row['jumlah_retur'],
					'd22'=>$row['jumlah_adjust'],
					'd23'=>$row['jumlah_mutasi'],
					'd24'=>$row['jumlah_jual']
				), "d2='".$row['d2']."' and (d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')"); 
			}
            $stock_akhir = ($row['stock_awal'] + $row['stock_masuk'] - $row['jumlah_jual'] - $row['jumlah_retur'] + $row['jumlah_adjust'] + $row['jumlah_mutasi'] + 0);
			$hitung_netto = $row['d3'] * $row['d9'];
            $diskon = $this->m_website->double_diskon($hitung_netto, array($row['d4'], $row['d5'], $row['d6'], $row['d7']));
            $hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $row['d8']);
            $sub_total = $sub_total + $hitung_sub_total;
            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d10'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button></td>
                                <td>' . $row['d2'] . '<input type="hidden" id="d2'.$no.'" name="d2'.$no.'" value="'.$row['d2'].'" /></td>
                                <td>' . $row['d10'] . '</td>
                                <td>' . $row['d16'] . '</td>
                                <td>' . $row['d12'] . '</td>
                                <td>'.($row['stock_awal']+0).'</td>
                                <td>'.($row['stock_masuk']+0).'</td>
                                <td>'.($row['jumlah_jual']+0).'</td>
                                <td>'.($row['jumlah_retur']+0).'</td>
                                <td>'.($row['jumlah_adjust']+0).'</td>
                                <td>'.($row['jumlah_mutasi']+0).'</td>
                                <td>'.($stock_akhir+0).'</td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d3\', $(this).val())" onkeydown="return isNumber(event)" onkeyup="update_tmp_detail(\'' . $row['d10'] . '\', \'d3\', $(this).val()); hitung_barang(\'d3\', \'' . $no . '\', $(this).val(), '.$length.'); isMoney(\'d3' . $no . '\', \'+\'); return to_qty(event, '.$no.')" onfocus="this.select()" type="text" id="d3' . $no . '" name="d3' . $no . '" class="form-control width-uang" value="' . number_format((float)($row['d3']+0), 2, '.', ',') . '"></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d14\', $(this).val())" onkeydown="return isNumber(event)" onkeyup="isMoney(\'d14' . $no . '\', \'+\')" onfocus="this.select()" value="' . number_format((float)($row['d14']+0), 2, '.', ',') . '" type="text" id="d14' . $no . '" name="d14' . $no . '" class="form-control width-uang"></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d4\', $(this).val())" onkeyup="hitung_barang(\'d4\', \'' . $no . '\', $(this).val(), '.$length.')" type="number" onfocus="this.select()" id="d4' . $no . '" name="d4' . $no . '" class="form-control width-diskon" value="' . ($row['d4'] + 0) . '"></td>
                                <input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d5\', $(this).val())" onkeyup="hitung_barang(\'d5\', \'' . $no . '\', $(this).val(), '.$length.')" type="hidden" id="d5' . $no . '" name="d5' . $no . '" class="form-control width-diskon" value="' . ($row['d5'] + 0) . '">
                                <input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d6\', $(this).val())" onkeyup="hitung_barang(\'d6\', \'' . $no . '\', $(this).val(), '.$length.')" type="hidden" id="d6' . $no . '" name="d6' . $no . '" class="form-control width-diskon" value="' . ($row['d6'] + 0) . '">
                                <input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d7\', $(this).val())" onkeyup="hitung_barang(\'d7\', \'' . $no . '\', $(this).val(), '.$length.')" type="hidden" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-diskon" value="' . ($row['d7'] + 0) . '">
                                <td>
									<input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d9\', $(this).val())" onfocus="this.select()" onkeyup="qty_max(); hitung_barang(\'d9\', \'' . $no . '\', $(this).val(), '.$length.'); return to_barcode(event)" type="number" id="d9' . $no . '" name="d9' . $no . '" class="form-control width-diskon" value="' . $row['d9'] . '">
									<b class="error" id="alr_qty'.$no.'"></b>
								</td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d8\', $(this).val())" onfocus="this.select()" onkeyup="hitung_barang(\'d8\', \'' . $no . '\', $(this).val(), '.$length.')" type="number" id="d8' . $no . '" name="d8' . $no . '" class="form-control width-diskon" value="' . ($row['d8'] + 0) . '"></td>
                                <td><input type="text" id="sub_total' . $no . '" name="sub_total' . $no . '" class="form-control width-uang" value="'.number_format((float)$hitung_sub_total, 2, '.', ',').'" readonly></td>
                            </tr>';
            $col = $no;
            $no++;
        }
        $list_barang .= '<input type="hidden" id="col" name="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang, 'sub_total' => $sub_total));
    }
	
	//tr tmp disamakan dengan purchase_order
	
	public function trans_po_mingguan() {
        $tgl_order = $_POST['tgl_order'];
        $tgl_kirim = $_POST['tgl_kirim'];
        $catatan = $_POST['catatan'];
        $jenis = $_POST['jenis_transaksi'];
        $supplier = $_POST['supplier'];
        $explode_lokasi = explode('|', $_POST['lokasi']);
        $lokasi = $explode_lokasi[0];
        $serial = $explode_lokasi[1];
        $nota_po = $this->m_website->generate_kode("PO", $serial, substr(str_replace('-', '', $tgl_order), 2));
		
        $this->db->trans_begin();
		
		if(isset($_POST['update'])){
			$m = $this->m_crud->get_data('master_order', 'no_order, tgl_order, lokasi', "no_order='".$_POST['update']."'");
			if($tgl_order==substr($m['tgl_order'],0,10) && $lokasi==$m['lokasi']){ $nota_po=$m['no_order']; }
			$this->m_crud->delete_data('master_order', "no_order='".$m['no_order']."'");
			$this->m_crud->delete_data('det_order', "no_order='".$m['no_order']."'");
		}
		
        $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'PO')");
        $sub_total = $this->get_sub_total_po();

        $data_po = array(
			'tgl_po' => $tgl_order . " " . date("H:i:s"),
            'no_po' => $nota_po,
            'kode_supplier' => $supplier,
            'lokasi' => $lokasi,
			'jenis' => $jenis,
			'jenis_po' => 'PO',
            'kd_kasir' => $this->user,
            'status' => 0,
            'catatan' => $catatan,
            'GT' => $sub_total,
            'tglkirim' => $tgl_kirim . " " . date("H:i:s")
        );
        $this->m_crud->create_data("master_po", $data_po);

        $det_log = array();
        //foreach ($read_temp_d as $row) {
        for ($i=1; $i<=$_POST['col']; $i++) {
			if($_POST['d9'.$i]>0){
				$data_detail_po = array(
					'no_po' => $nota_po,
					'kode_barang' => $_POST['d2'.$i],//$row['d2'],
					'diskon' => $_POST['d4'.$i],//$row['d4'],
					'disc2' => $_POST['d5'.$i],//$row['d5'],
					'disc3' => $_POST['d6'.$i],//$row['d6'],
					'disc4' => $_POST['d7'.$i],//$row['d7'],
					'PPN' => $_POST['d8'.$i],//$row['d8'],
					'harga_beli' => $_POST['d3'.$i],//$row['d3'],
					'harga_jual' => $_POST['d14'.$i],//$row['d14'],
					'jumlah_beli' => $_POST['d9'.$i]//$row['d9']
				);
				$this->m_crud->create_data("detail_po", $data_detail_po);
				array_push($det_log, $data_detail_po);
			}
        }
		
        if (isset($_POST['update'])){ 
            $data_po['trx_old'] = $_POST['update'];
        	$this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$nota_po,'jenis'=>'Edit','transaksi'=>'PO Mingguan'), array('master'=>$data_po,'detail'=>$det_log));
		} else {
			$this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$nota_po,'jenis'=>'Add','transaksi'=>'PO Mingguan'), array('master'=>$data_po,'detail'=>$det_log));
		}

        
        $this->delete_trans_po('no_respon');

        if ($this->db->trans_status()===FALSE || count($det_log)<=0) {
            $this->db->trans_rollback();
            echo false;
        }else {
			$this->db->trans_commit();
            echo $nota_po;
        }
    }
    /*End modul purchase order mingguan*/
	
}

