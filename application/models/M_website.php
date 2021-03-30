<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_website extends CI_Model {
	
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		/*if(date('H:i:s')>'13:00:00' && date('H:i:s')<'15:00:00'){
			$this->load->dbutil();
			$prefs = array(
				//'tables'        => array('table1', 'table2'),   // Array of tables to backup.
				'ignore'        => array(),                     // List of tables to omit from the backup
				'format'        => 'txt',                       // gzip, zip, txt
				'filename'      => 'mybackup_'.date('md').'.sql',              // File name - NEEDED ONLY WITH ZIP FILES
				'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
				'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
				'newline'       => "\n"                         // Newline character used in backup file
			);
			if(date('d') > 10){ delete_files('assets/database/'.(date('m')-1), TRUE); }
			if(read_file('assets/database/'.date('m').'/'.$prefs['filename']) == null){
				$backup = $this->dbutil->backup($prefs);
				//$backup = $this->dbutil->backup();
				//$this->load->helper('file');
				write_file('assets/database/'.date('m').'/'.$prefs['filename'], $backup);
			}
			//$this->load->helper('download');
			//force_download($prefs['filename'], $backup);
		}*/
	}
	
	public function meta_data($meta_data=null){
		if($meta_data!=null){
			//meta website
			if(isset($meta_data['website'])){
				//<meta name="google-site-verification" content="bExkQFnEooJVIoZIm70CO8H8Yjx_FfyyCC6hNE_SeoA" />
				//<meta name="msvalidate.01" content="3123382E32539EBE8C53C2CA69F7510D" />
				$meta['website']['Content-Type']	= "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />";
				$meta['website']['keywords']		= "<meta name='keywords' content='".$this->m_website->domain().', '.$meta_data['website']['keywords']."' />";
				$meta['website']['title']			= "<meta name='title' content='".$meta_data['website']['title']."' />";
				$meta['website']['image_src']		= "<link rel='image_src' href='".$meta_data['website']['image_src']."' />";
				$meta['website']['description']		= "<meta name='description' content='".strip_tags($meta_data['website']['description'])."' />";
				$meta['website']['author']			= "<meta name='author' content='".$this->m_website->domain()."' />";
			}
			if(isset($meta_data['facebook'])){
			//meta facebook https://developers.facebook.com/tools/debug/ - https://developers.facebook.com/tools/debug/og/object/
				//<meta property="fb:app_id" content="191402794307447" />
				//<meta property="og:site_name" content="NACTS" />
				//$meta['facebook']['admins']			= "<meta property='fb:admins' content='".$meta_data['facebook']['admins']."' />";
				$meta['facebook']['url']			= "<meta property='og:url' content='".$meta_data['facebook']['url']."' />";
				$meta['facebook']['type']			= "<meta property='og:type' content='".$meta_data['facebook']['type']."' />";
				$meta['facebook']['title']			= "<meta property='og:title' content='".$meta_data['facebook']['title']."' />";
				$meta['facebook']['image']			= "<meta property='og:image' content='".$meta_data['facebook']['image']."' />";
				$meta['facebook']['description']	= "<meta property='og:description' content='".strip_tags($meta_data['facebook']['description'])."' />";
			}
			//meta twitter belum dicoba
			/*<meta name="twitter:card" content="summary" />
			<meta name="twitter:url" content="<?=base_url()?>artikel/detail/<?=$artikel['id_artikel']?>" />
			<meta name="twitter:title" content="<?=$artikel['nama']?>" />
			<meta name="twitter:description" content="<?=strip_tags($artikel['keterangan'])?>" />
			<meta name="twitter:image" content="<?=base_url()?>uploads/images/artikel/<?=$artikel['foto']?>" />*/
			return $meta;
		} else { return $meta = null; }
	}
	
	public function curl_store_online($param, $data="", $header=null){
		$server = 'https://www.indokids.co.id/api/';
		//$server = '192.168.100.9/idk_store_2/api/';
		$api = $this->request_api($server.$param, $data, $header);
		return $api;
	}
	
	public function request_api($param="check_server", $data="", $header=null) {
        $ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL,$this->api.$param);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		if ($header != null) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec ($ch);

        curl_close ($ch);

        return $result;
    }
	
	public function logo($width="200px", $height="50px"){
		return '<img width="'.$width.'" height="'.$height.'" src="'.$this->config->item('url').$this->m_website->site_data()->logo.'"/>';
	}
	
	public function address(){
	    return ' ';
	    //return 'Jl Burangrang No 35 A, Malabar, Lengkong, Kota Bandung, Jawa Barat 40262';
		/*return $this->m_crud->get_data('Lokasi', 'Nama', "kode = '".$this->m_website->get_lokasi()."'")['Nama'];*/
	}
	
	public function address_2(){
	    //return 'Jl Burangrang No 35 A, Malabar, Lengkong, Kota Bandung, Jawa Barat 40262';
		return $this->m_crud->get_data('Lokasi', 'ket', "kode = 'HO'")['ket'];
	}
	
	public function get_lokasi($id = null){
		return $this->session->lokasi;
	}
	
	public function lokasi($id = null, $field = '*'){
		if($id==null){ $id = $id = $this->m_website->get_lokasi(); }
		$data = $this->m_crud->get_data('Lokasi', $field, "kode = '".$id."'");
		if(substr($field,0,1)=='*'){ return $data; }
		else{ return $data[$field]; }
	}
	
	public function grand_total_ppn($total=0, $diskon=0, $ppn=0){
		$total_diskon = ($total * $diskon) / 100;
		$ppn = ($total - $total_diskon) * $ppn / 100;
		$grand_total = ($total - $total_diskon + $ppn);
		return $grand_total;
	}
	
	public function after_diskon($total=0, $diskon=0){
		$total_diskon = ($total * $diskon) / 100;
		$after_diskon = ($total - $total_diskon);
		return $after_diskon;
	}

    public function diskon($total=0, $diskon=0){
        $total_diskon = $total * ($diskon / 100);
        return $total_diskon;
    }

	public function double_diskon($total, $diskon) {
	    $hitung_diskon = (float)$total;
	    for ($i=0; $i<count($diskon); $i++) {
			if(((float)$diskon[$i]) > 0){
            	$hitung_diskon = $hitung_diskon - ($hitung_diskon * (((float)$diskon[$i]) / 100));
			}
		}

        return $hitung_diskon;
    }

    public function hitung_total($total, $diskon, $ppn) {
        $hitung_diskon = $total - ($total * ($diskon / 100));
        $hitung_total = $hitung_diskon + ($hitung_diskon * ($ppn / 100));

        return $hitung_total;
    }
	
	public function ppn($total=0, $diskon=0, $ppn=0){
		$total_diskon = ($total * $diskon) / 100;
		$ppn = ($total - $total_diskon) * $ppn / 100;
		return $ppn;
	}

	public function generate_kode($jenis, $status, $tanggal) {
	    $kode_baru = '';
        if ($jenis == "BL") {
            $get_max_kode = $this->m_crud->get_data("master_beli","MAX(SUBSTRING(no_faktur_beli, 10, 4)) AS max_kode","(SUBSTRING(no_faktur_beli, 15, 1) = '".$status."') AND (SUBSTRING(no_faktur_beli, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$status;
        } else if ($jenis == "MU") {
            $get_max_kode = $this->m_crud->get_data("Master_Mutasi","MAX(SUBSTRING(no_faktur_mutasi, 10, 4)) AS max_kode","(SUBSTRING(no_faktur_mutasi, 15, 1) = '".$status."') AND (SUBSTRING(no_faktur_mutasi, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$status;
        } else if ($jenis == "MC") {
            $get_max_kode = $this->m_crud->get_data("Master_Mutasi","MAX(SUBSTRING(no_faktur_mutasi, 10, 4)) AS max_kode","(SUBSTRING(no_faktur_mutasi, 15, 1) = '".$status."') AND (SUBSTRING(no_faktur_mutasi, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$status;
        } else if ($jenis == "AA") {
            $get_max_kode = $this->m_crud->get_data("adjust","MAX(SUBSTRING(kd_trx, 10, 4)) AS max_kode","(kd_trx like '%-".$status."') AND (SUBSTRING(kd_trx, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$status;
        } else if ($jenis == "MO") {
            $get_max_kode = $this->m_crud->get_data("master_order","MAX(SUBSTRING(no_order, 10, 4)) AS max_kode","(SUBSTRING(no_order, 15, 1) = '".$status."') AND (SUBSTRING(no_order, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$status;
        } else if ($jenis == "PO") {
            $get_max_kode = $this->m_crud->get_data("Master_PO","MAX(SUBSTRING(no_po, 10, 4)) AS max_kode","(SUBSTRING(no_po, 15, 1) = '".$status."') AND (SUBSTRING(no_po, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$status;
        } else if ($jenis == "NB") {
            $get_max_kode = $this->m_crud->get_data("Master_Retur_Beli","MAX(SUBSTRING(No_Retur, 10, 4)) AS max_kode","(SUBSTRING(No_Retur, 15, 1) = '".$status."') AND (SUBSTRING(No_Retur, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$status;
        } else if ($jenis == "RR") {
            $get_max_kode = $this->m_crud->get_data("Kartu_stock","MAX(SUBSTRING(kd_trx, 10, 4)) AS max_kode","lokasi='Retur' AND (SUBSTRING(kd_trx, 15, 1) = '".$status."') AND (SUBSTRING(kd_trx, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$status;
        } else if ($jenis == "JL") {
            $get_max_kode = $this->m_crud->get_data("Master_Trx","MAX(SUBSTRING(kd_trx, 10, 4)) AS max_kode","(SUBSTRING(kd_trx, 15, 1) = '".$status."') AND (SUBSTRING(kd_trx, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$status;
        } else if ($jenis == "BH") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");

            $get_max_kode = $this->m_crud->get_data("bayar_hutang","MAX(SUBSTRING(no_nota, 10, 4)) AS max_kode","(SUBSTRING(no_nota, 15, 1) = '".$lokasi['serial']."') AND (SUBSTRING(no_nota, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "BP") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");

            $get_max_kode = $this->m_crud->get_data("bayar_piutang","MAX(SUBSTRING(no_nota, 10, 4)) AS max_kode","(SUBSTRING(no_nota, 15, 1) = '".$lokasi['serial']."') AND (SUBSTRING(no_nota, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "PN") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");

            $get_max_kode = $this->m_crud->get_data("Kartu_stock","MAX(SUBSTRING(kd_trx, 10, 4)) AS max_kode","(SUBSTRING(kd_trx, 15, 1) = '".$lokasi['serial']."') AND (SUBSTRING(kd_trx, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "PR") {
            $get_max_kode = $this->m_crud->get_data("master_promo","MAX(SUBSTRING(id_promo, 10, 4)) AS max_kode","(SUBSTRING(id_promo, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1);
        } else if ($jenis == "KB") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");

            $get_max_kode = $this->m_crud->get_data("master_kontra","MAX(SUBSTRING(id_master_kontra, 10, 4)) AS max_kode","(SUBSTRING(id_master_kontra, 15, 1) = '".$lokasi['serial']."') AND (SUBSTRING(id_master_kontra, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "BK") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");

            $get_max_kode = $this->m_crud->get_data("master_byr_kontra","MAX(SUBSTRING(id_master_byr_kontra, 10, 4)) AS max_kode","(SUBSTRING(id_master_byr_kontra, 15, 1) = '".$lokasi['serial']."') AND (SUBSTRING(id_master_byr_kontra, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "KK") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");

            $get_max_kode = $this->m_crud->get_data("kas_keluar","MAX(SUBSTRING(kd_trx, 10, 4)) AS max_kode","(SUBSTRING(kd_trx, 15, 1) = '".$lokasi['serial']."') AND (SUBSTRING(kd_trx, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "KM") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");

            $get_max_kode = $this->m_crud->get_data("kas_masuk","MAX(SUBSTRING(kd_trx, 10, 4)) AS max_kode","(SUBSTRING(kd_trx, 15, 1) = '".$lokasi['serial']."') AND (SUBSTRING(kd_trx, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "RO") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");

            $get_max_kode = $this->m_crud->get_data("master_receive_order","MAX(SUBSTRING(no_receive_order, 10, 4)) AS max_kode","(SUBSTRING(no_receive_order, 15, 1) = '".$lokasi['serial']."') AND (SUBSTRING(no_receive_order, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "EX") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");

            $get_max_kode = $this->m_crud->get_data("master_expedisi", "MAX(SUBSTRING(kd_expedisi, 10, 4)) AS max_kode", "lokasi_asal = '".$status."' AND (SUBSTRING(kd_expedisi, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "DN") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");
			
			$get_max_kode = $this->m_crud->get_data("master_delivery_note","MAX(SUBSTRING(no_delivery_note, 10, 4)) AS max_kode","(SUBSTRING(no_delivery_note, 15, 1) = '".$lokasi['serial']."') AND (SUBSTRING(no_delivery_note, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "OP") {
			$get_max_kode = $this->m_crud->get_data("opname","MAX(SUBSTRING(kd_trx, 10, 4)) AS max_kode","(SUBSTRING(kd_trx, 15, 1) = '".$status."') AND (SUBSTRING(kd_trx, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];
			
            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$status;
        } else if ($jenis == "TO") {
            $serial = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'")['serial'];
            $get_max_kode = $this->m_crud->get_data("master_to","MAX(SUBSTRING(no_to, 10, 4)) AS max_kode","lokasi = '".$status."' AND (SUBSTRING(no_to, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$serial;
        } else if ($jenis == "KP") {
            $get_max_kode = $this->m_crud->get_data("kitchen_printer","MAX(SUBSTRING(id_printer, 3, 4)) AS max_kode");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis.sprintf('%04d', $max_kode+1);
        } else if ($jenis == "LOG") {
            $status = $this->config->item('lokasi');
            $serial = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'")['serial'];
            $get_max_kode = $this->m_crud->get_data("log_api","MAX(SUBSTRING(id_log, 7, 5)) AS max_kode","param = 'send' and (LEFT(id_log, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $tanggal.sprintf('%05d', $max_kode+1).$serial;
        } else if ($jenis == "LOGTR") {
            $serial = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'")['serial'];
            $get_max_kode = $this->m_crud->get_data("log_api", "MAX(SUBSTRING(id_log, 7, 5)) AS max_kode","param = 'send' and (LEFT(id_log, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $tanggal.sprintf('%05d', $max_kode+1).$serial;
        } else if ($jenis == "PRINT") {
            $get_max_kode = $this->m_crud->get_data("log_print","MAX(SUBSTRING(id_log, 7, 5)) AS max_kode","(LEFT(id_log, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $tanggal.sprintf('%05d', $max_kode+1);
        } else if ($jenis == "CL") {
            $get_max_kode = $this->m_crud->get_data("compliment","MAX(RIGHT(compliment_id, 5)) AS max_kode");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis.sprintf('%05d', $max_kode+1);
        } else if ($jenis == "member") {
            $id = $status;
            $max_code = $this->m_crud->get_data("customer", "RIGHT(MAX(ol_code), 5) max_code", "substring(ol_code, 2, 6)='".$id."'")['max_code'];

            return 'M'.$id.sprintf('%05d', $max_code+1);
        } else if ($jenis == "poin") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");

            $get_max_kode = $this->m_crud->get_data("poin","MAX(SUBSTRING(kode_trx, 10, 4)) AS max_kode","(SUBSTRING(kode_trx, 15, 1) = '".$lokasi['serial']."') AND (SUBSTRING(kode_trx, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = "PN-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "RS") {
            $lokasi = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'");

            $get_max_kode = $this->m_crud->get_data("master_reservasi","MAX(SUBSTRING(kd_trx, 10, 4)) AS max_kode","(SUBSTRING(kd_trx, 15, 1) = '".$lokasi['serial']."') AND (SUBSTRING(kd_trx, 4, 6) = '".$tanggal."')");
            $max_kode = $get_max_kode['max_kode'];

            $kode_baru = $jenis."-".$tanggal.sprintf('%04d', $max_kode+1)."-".$lokasi['serial'];
        } else if ($jenis == "rekening") {
            $id = $status;
            $max_code = $this->m_crud->get_data("rekening", "RIGHT(MAX(id_rekening), 5) max_code", "substring(id_rekening, 3, 6)='".$id."'")['max_code'];

            return 'RK'.$id.sprintf('%05d', $max_code+1);
        } else if ($jenis == "kel_brg_online") {
            $id = $status;
            $max_code = $this->m_crud->get_data("kel_brg_online", "RIGHT(MAX(id_kel_brg), 5) max_code", "substring(id_kel_brg, 4, 6)='".$id."'")['max_code'];

            return 'KBO'.$id.sprintf('%05d', $max_code+1);
        } else if ($jenis == "barang_online") {
            $id = $status;
            $max_code = $this->m_crud->get_data("barang_online", "RIGHT(MAX(id_barang), 5) max_code", "substring(id_barang, 4, 6)='".$id."'")['max_code'];

            return 'BRO'.$id.sprintf('%05d', $max_code+1);
        } else if ($jenis == "wishlist") {
            $id = $status;
            $max_code = $this->m_crud->get_data("wishlist", "RIGHT(MAX(id_wishlist), 5) max_code", "substring(id_wishlist, 3, 6)='".$id."'")['max_code'];

            return 'WS'.$id.sprintf('%05d', $max_code+1);
        } else if ($jenis == "fasilitas") {
            $max_code = $this->m_crud->get_data("fasilitas", "RIGHT(MAX(id_fasilitas), 5) max_code")['max_code'];

            return 'FS'.sprintf('%05d', $max_code+1);
        } else if ($jenis == "fasilitas_lokasi") {
            $id = $status;
            $max_code = $this->m_crud->get_data("fasilitas_lokasi", "RIGHT(MAX(id_fasilitas_lokasi), 5) max_code", "substring(id_fasilitas_lokasi, 3, 6)='".$id."'")['max_code'];

            return 'FL'.$id.sprintf('%05d', $max_code+1);
        } else if ($jenis == "deposit") {
            $id = $status;
            $max_code = $this->m_crud->get_data("deposit", "RIGHT(MAX(id_deposit), 5) max_code", "substring(id_deposit, 3, 6)='".$id."'")['max_code'];

            return 'DP'.$id.sprintf('%05d', $max_code+1);
        } else if ($jenis == "ppob") {
            $id = $status;
            $max_code = $this->m_crud->get_data("tr_ppob", "RIGHT(MAX(kd_trx), 6) max_code", "substring(kd_trx, 3, 6)='".$id."'")['max_code'];

            return 'OP'.$id.sprintf('%06d', $max_code+1);
        } else if ($jenis == "area") {
            $id = $status;
            $max_code = $this->m_crud->get_data("area", "RIGHT(MAX(id_area), 5) max_code", "substring(id_area, 3, 6)='".$id."'")['max_code'];

            return 'AR'.$id.sprintf('%05d', $max_code+1);
        } else if ($jenis == "meja") {
            $id = $status;
            $max_code = $this->m_crud->get_data("meja", "RIGHT(MAX(id_meja), 5) max_code", "substring(id_meja, 3, 6)='".$id."'")['max_code'];

            return 'MJ'.$id.sprintf('%05d', $max_code+1);
        } else if ($jenis == "gabung") {
            $serial = $this->m_crud->get_data("Lokasi", "serial", "Kode='".$status."'")['serial'];
            $id = $tanggal;
            $max_code = $this->m_crud->get_data("master_to", "RIGHT(MAX(gabung), 5) max_code", "substring(gabung, 3, 6)='".$id."'")['max_code'];

            return 'GM'.$id.$serial.sprintf('%05d', $max_code+1);
        }

        return $kode_baru;
    }

    public function generate_kode_barang($kode, $length) {
	    $get_max_kode = $this->m_crud->get_data("barang", "MAX(CONVERT(INTEGER, SUBSTRING(kd_brg, ".($length+1).", 4))) max_kode", "ltrim(rtrim(LEFT(kd_brg, ".$length.")))='".ltrim(rtrim($kode))."' and ISNUMERIC(SUBSTRING(kd_brg, ".($length+1).", 4)) = 1");
	    $max_kode = $get_max_kode['max_kode'];

	    $kode_baru = $kode.sprintf('%04d', $max_kode+1);

	    return $kode_baru;
    }

    public function generate_barcode($kode, $length) {
        $get_max_kode = $this->m_crud->get_data("barang", "MAX(CONVERT(INTEGER, SUBSTRING(barcode, ".($length+1).", 4))) max_kode", "ltrim(rtrim(LEFT(barcode, ".$length.")))='".ltrim(rtrim($kode))."' and ISNUMERIC(SUBSTRING(barcode, ".($length+1).", 4)) = 1");
        $max_kode = $get_max_kode['max_kode'];

        $kode_baru = $kode.sprintf('%04d', $max_kode+1);

        return $kode_baru;
    }

    public function generate_kode_kelompok($kode, $length) {
        $get_max_kode = $this->m_crud->get_data("kel_brg", "MAX(CONVERT(INTEGER, SUBSTRING(kel_brg, ".($length+1).", 2))) max_kode", "ltrim(rtrim(LEFT(kel_brg, ".$length.")))='".ltrim(rtrim($kode))."'");
        $max_kode = $get_max_kode['max_kode'];

        $kode_baru = str_replace(" ", "",$kode.sprintf('%02d', $max_kode+1));

        return $kode_baru;
    }
	
	public function selisih_bulan($date1, $date2){
		$date1 = new DateTime($date1);
		$date2 = new DateTime($date2);
		$diff =  $date1->diff($date2);
		
		//$months = $diff->y * 12 + $diff->m + $diff->d / 30;
		//return (int) round($months);
		
		$months = (($diff->format('%y') * 12) + $diff->format('%m'));
		return $months;
    }
	
	public function selisih_hari($date1, $date2){
		$hari = strtotime($date1)-strtotime($date2);
		$jumlah	= ($hari/3600)/24;
		
		$jumlah = explode('.',$jumlah);
		$jumlah = $jumlah[0];
		
		return $jumlah;
	}
	
	public function kurs($id, $field='*'){
		$data = $this->m_crud->get_data('acc_kurs_uang', $field, 'id_kurs_uang = '.$id);
		if(substr($field,0,1)=='*'){ return $data; }
		else{ return $data[$field]; }
	}

	public function get_nama_user($kode) {
	    $get_user = $this->m_crud->get_data("user_detail", "nama", "user_id='".$kode."'");

	    return $get_user['nama'];
    }
	
	public function multi_periode($date1, $date2){
		$y_awal = (int) substr($date1, 0, 4);
		$y_akhir = (int) substr($date2, 0, 4);
		$m_awal = (int) substr($date1, 5, 2);
		$m_akhir = (int) substr($date2, 5, 2);
		$year = ($y_akhir - $y_awal) * 12;
		
		$awal = $m_awal; 
		$akhir = $m_akhir + $year;
		$year = $y_awal;
		$month = $m_awal;
		
		$data = null;
		$array = 0;
		for($i=$awal; $i<=$akhir; $i++){
			if($i % 12 == 1 && $i != 1){ $year = $year + 1; $month = $month - 12; }
			$data[$array] = $year.'-'.$month.'-01';
			if($y_awal == $year && $m_awal == $month){ 
				$end = date('Y-m-d', strtotime('+1 month', strtotime($data[$array])));
				$data[$array] = array($date1, date('Y-m-d', strtotime('-1 day', strtotime($end)))); 
			} else if($y_akhir == $year && $m_akhir == $month){ 
				$data[$array] = array($data[$array], $date2); 
			} else{ 
				$end = date('Y-m-d', strtotime('+1 month', strtotime($data[$array])));
				$data[$array] = array($data[$array], date('Y-m-d', strtotime('-1 day', strtotime($end)))); 
			}
			$array++;
			$month++;
		}
		return $data;
	}
	
	public function login($username, $password){
		$query = "select *
					from user_akun
					join user_detail on user_akun.user_id=user_detail.user_id
					where username = '$username'
					and password = '$password'
					and status = '1'";
		$data = $this->db->query($query);
		if($data->num_rows()==1){
			return $data->row();
		}else{
			return false;
		}
	}
	
	public function user($user){
		$query = "select *
					from user_akun
					where user_id = '$user';";
		$data = $this->db->query($query);
		if($data){
			return $data->row();
		}else{
			return false;
		}
	}
	
	public function user_data($user){
		$data = $this->m_crud->join_data('user_akun', 'user_akun.user_id, nama, tgl_lahir, alamat, email, nohp, lvl, access', 
									array('user_detail', 'user_lvl'), 
									array('user_akun.user_id = user_detail.user_id', 'user_lvl = id'), 
									"user_akun.user_id = '".$user."'");
		
		if($data){
			return $data[0];
		}else{
			return false;
		}
	}
	
	public function user_access_data($user){

		$query = "select *
					from user_akun, user_lvl 
					where user_akun.user_lvl = user_lvl.id 
					and user_id = '$user';";
		$data = $this->db->query($query);
		if($data){
			return $data->row();
		}else{
			return false;
		}
	}
	
	public function site_data(){
		$query = "select * from site where site_id = '1'";
		$data = $this->db->query($query);
		if($data){
			return $data->row();
		}else{
			return false;
		}
	}

    public function setting(){
        $query = "select * from setting where kode = '1111'";
        $data = $this->db->query($query);
        if($data){
            return $data->row();
        }else{
            return false;
        }
    }
	
	public function edit_access_user($id, $new){
		$data = array (
		'access'  => $new, 
		);
		$this->db->where('id', $id);
		if($this->db->update('user_lvl', $data)){
			return true;
		}else{
			return false;
		}
	}
	
	public function ubah_profil_user($user){
		
		$nama  = $this->input->post('nama');
		$alamat  = $this->input->post('alamat');
		$email  = $this->input->post('email');
		$notlp  = $this->input->post('notlp');
		$data = array (
		'nama'  => $nama, 
		'alamat'=> $alamat,
		'email'=> $email,
		'nohp'=> $notlp
		);
		$this->db->where('user_id',$user);
		if($this->db->update('user_detail', $data)){
			return true;
		}else{
			return false;
		}
	}
	
	public function cek_passlama($user){
		$query = "select *
					from user_akun
					where user_id = '$user'";
		$data = $this->db->query($query);
		if($data){
			return $data->row();
		}else{
			return false;
		}
	}
	
	public function ubah_password($user){
		
		$pass  = md5($this->input->post('konf_passbaru'));
		$data = array (
		'password'  => $pass, 
		);
		$this->db->where('user_id',$user);
		if($this->db->update('user_akun', $data)){
			return true;
		}else{
			return false;
		}
	}

	public function insert_log($master, $detail) {
	    $max_code = $this->m_crud->get_data("log_transaksi", "MAX(RIGHT(id_log, 4)) max_code", "CONVERT(DATE, tanggal) = '".date('Y-m-d')."'");
	    $data_log = array(
	        'id_log' => date('ymd').sprintf('%04d', (int)$max_code['max_code']+1),
	        'tanggal' => date('Y-m-d H:i:s'),
	        'id_user' => $this->get_nama_user($master['admin']),
	        'kd_trx' => $master['kd_trx'],
	        'jenis' => $master['jenis'],
	        'transaksi' => $master['transaksi'],
	        'detail_trx' => json_encode($detail)
        );

	    $this->m_crud->create_data("log_transaksi", $data_log);
    }

    public function reprint($kode, $param=null) {
	    $get_data = $this->m_crud->count_data("Aktivitas", "Aktivitas", "status='R' AND jenis='BO' AND Aktivitas='".$kode."'");

	    echo ($param==null?$get_data>1:$get_data>=1)?'(Reprint)':'';
    }

    public function add_activity($message, $status,$sebelum=array(),$sesudah=array()) {
        $data = array(
            'Tgl' => date('Y-m-d H:i:s'),
            'Jam' => date('Y-m-d H:i:s'),
            'Kd_kasir' => $this->user,
            'nm_kasir' => $this->m_website->get_nama_user($this->user),
            'Aktivitas' => $message,
            'jenis' => 'BO',
            'sebelum' => $sebelum,
            'sesudah' => $sesudah,
            'status' => $status
        );
        $this->m_crud->create_data("Aktivitas", $data);
    }
	
	public function send_mail($data){
		$to = isset($data['to'])?strip_tags($data['to']):null;
		$subject = isset($data['subject'])?strip_tags($data['subject']):null;
		$message = isset($data['message'])?$data['message']:null;
		$file_path = isset($data['file_path'])?$data['file_path']:null;
		$file_name = isset($data['file_name'])?$data['file_name']:null;
		
		$this->load->library("PHPMailer_Library");
		
		$email = $this->phpmailer_library->load();
		$email->isHTML(true);
		$email->From      = 'purchasing@indokids.co.id';
		$email->FromName  = $this->site_data()->title;
		$email->Subject   = $subject;
		$email->Body      = $message;
		$email->AddAddress($to);

		if($file_path!=null){ $email->AddAttachment($file_path, $file_name); }

		if ($email->Send()) {
			unlink($file_path);
			return true;
		} else {
			return false;
		}
	}

    public function insert_stock($kd_trx, $tgl, $kd_brg, $in, $out, $lokasi, $ket, $ket2, $hrg_beli=null) {
        $stok = array(
            'kd_trx' => $kd_trx,
            'tgl' => $tgl,
            'kd_brg' => $kd_brg,
            'saldo_awal' => 0,
            'stock_in' => $in,
            'stock_out' => $out,
            'lokasi' => $lokasi,
            'keterangan' => $ket,
            'hrg_beli' => $hrg_beli==null?$this->m_crud->get_data("barang", "hrg_beli", "kd_brg='".$kd_brg."'")['hrg_beli']:$hrg_beli,
            'keterangan2' => $ket2
        );

        $this->m_crud->create_data("kartu_stock", $stok);

        /*Insert Log*/
        $log = array(
            'type' => 'I',
            'table' => "kartu_stock",
            'data' => $stok,
            'condition' => ""
        );

        $data_log = array(
            'lokasi' => $this->config->item('lokasi'),
            'hostname' => '-',
            'db_name' => '-',
            'query' => json_encode($log)
        );
        $this->m_website->insert_log_tr($data_log);
        /*End insert log*/

        return true;
    }

    public function file_thumb($file){
        $file_ori = explode('.', $file);
        $jml = count($file_ori);
        $file_thumb = null;
        for($i=0; $i<$jml; $i++){
            if($i == ($jml-1)){
                $file_thumb .= '_thumb.'.$file_ori[$i];
            } else {
                $file_thumb .= '.'.$file_ori[$i];
            }
        }
        return substr($file_thumb, 1);
    }

    public function no_img(){
        return base_url().'assets/images/no_image.png';
    }

    public function user_img_default(){
        return base_url().'assets/images/default.png';
    }

    public function cek_barang_poin($kd_brg=null) {
        $get_data = $this->m_crud->get_data("barang", "kd_brg", "poin=1 AND kd_brg='".$kd_brg."'");

        if ($get_data != null) {
            return true;
        } else {
            return false;
        }
    }

    public function tambah_data($table, $get_data, $param=null, $customer='-') {
        $tanggal = date('Y-m-d');
        if($table=='barang') {
            $lokasi = $param;
            foreach($get_data as $i => $item){
                $get_diskon = $this->m_crud->get_data("Promo", "pildiskon, diskon", "kd_brg='".$item['kd_brg']."' and '".$tanggal."' between daritgl and sampaitgl");
                $get_hrg_lokasi = $this->m_crud->get_data("barang_hrg", "*", "barang='".$item['kd_brg']."' and lokasi='".$lokasi."'");

                if ($get_hrg_lokasi != null) {
                    $get_data[$i]['hrg_jual_1'] = $get_hrg_lokasi['hrg_jual_1'];
                    $get_data[$i]['hrg_jual_2'] = $get_hrg_lokasi['hrg_jual_2'];
                    $get_data[$i]['hrg_jual_3'] = $get_hrg_lokasi['hrg_jual_3'];
                    $get_data[$i]['hrg_jual_4'] = $get_hrg_lokasi['hrg_jual_4'];
                    $get_data[$i]['service'] = $get_hrg_lokasi['service']==null?0.0:$get_hrg_lokasi['service'];
                    $get_data[$i]['PPN'] = $get_hrg_lokasi['ppn']==null?0.0:$get_hrg_lokasi['ppn'];
                }
if ($customer != '-') {
                    $get_hrg_customer = $this->m_crud->get_data("brg_customer", "hrg_jual", "kd_cust='".$customer."' and kd_brg='".$item['kd_brg']."'");

                    if ($get_hrg_customer != null) {
                        $get_data[$i]['hrg_jual'] = $get_hrg_customer['hrg_jual'];
                        $get_data[$i]['hrg_jual_1'] = $get_hrg_customer['hrg_jual'];
                        $get_data[$i]['hrg_jual_2'] = $get_hrg_customer['hrg_jual'];
                        $get_data[$i]['hrg_jual_3'] = $get_hrg_customer['hrg_jual'];
                        $get_data[$i]['hrg_jual_4'] = $get_hrg_customer['hrg_jual'];
                    }
                }
                if ($item['hrg_jual']==null) {
                    if ($get_hrg_lokasi != null) {
                        $get_data[$i]['hrg_jual'] = $get_hrg_lokasi['hrg_jual_1'];
                    } else {
                        $get_data[$i]['hrg_jual'] = $item['hrg_jual_1'];
                    }
                }
				
				

                if ($get_diskon != null) {
                    $get_data[$i]['jenis_diskon'] = $get_diskon['pildiskon'];
                    $get_data[$i]['diskon'] = $get_diskon['diskon'];
                } else {
                    $get_data[$i]['jenis_diskon'] = '%';
                    $get_data[$i]['diskon'] = 0;
                }

                if($item['gambar']!=null && $item['gambar']!='' && $item['gambar']!='-'){
                    $str = $item['gambar'];
                    $explode = explode('/', $str);
                    if ($explode[0].'/' == $this->config->item('site')) {
                        unset($explode[0]);
                        $gambar = implode('/', $explode);
                    } else {
                        $gambar = $item['gambar'];
                    }

                    $get_data[$i]['nm_kcp'] = $this->m_crud->get_data("kitchen_printer", "nama", "id_printer='".$item['kcp']."'")['nama'];
                    $get_data[$i]['nm_kel_brg'] = $this->m_crud->get_data("kel_brg", "dbo.TRIM(nm_kel_brg) nm_kel_brg", "kel_brg='".$item['kel_brg']."'")['nm_kel_brg'];
                    $get_data[$i]['stok'] = $this->m_crud->get_data("kartu_stock", "isnull(sum(stock_in-stock_out), 0) stock", "kd_brg='".$item['kd_brg']."'")['stock'];
                    $get_data[$i]['gambar'] = base_url().$gambar;
                    $get_data[$i]['gambar_thumb'] = $this->file_thumb($get_data[$i]['gambar']);
                } else {
                    $get_data[$i]['nm_kcp'] = $this->m_crud->get_data("kitchen_printer", "nama", "id_printer='".$item['kcp']."'")['nama'];
                    $get_data[$i]['nm_kel_brg'] = $this->m_crud->get_data("kel_brg", "dbo.TRIM(nm_kel_brg) nm_kel_brg", "kel_brg='".$item['kel_brg']."'")['nm_kel_brg'];
                    $get_data[$i]['stok'] = $this->m_crud->get_data("kartu_stock", "isnull(sum(stock_in-stock_out), 0) stock", "kd_brg='".$item['kd_brg']."'")['stock'];
                    $get_data[$i]['gambar'] = $this->no_img();
                    $get_data[$i]['gambar_thumb'] = $this->no_img();
                }
            }
        } else if($table=='barang_online') {
            $member = $param;
            foreach($get_data as $i => $item) {
                if ($member != null) {
                    $get_wishlist = $this->m_crud->get_data("wishlist", "id_wishlist", "kd_brg='".$item['id_barang']."' and member='".$member."'");

                    $fav = ($get_wishlist==null?0:1);
                } else {
                    $fav = 0;
                }

                $get_data[$i]['nm_kel_brg'] = $this->m_crud->get_data("kel_brg_online", "dbo.TRIM(nama) nm_kel_brg", "id_kel_brg='".$item['kel_brg_online']."'")['nm_kel_brg'];
                $get_data[$i]['kd_brg'] = $item['id_barang'];
                $get_data[$i]['nm_brg'] = $item['nama'];
                $get_data[$i]['hrg_jual_1'] = $item['hrg_jual'];
                $get_data[$i]['kel_brg'] = $item['kel_brg_online'];
                $get_data[$i]['Deskripsi'] = $item['deskripsi'];
                $get_data[$i]['Group2'] = '-';
                $get_data[$i]['fav'] = $fav;
                $get_data[$i]['online'] = $item['status'];
                $get_data[$i]['status'] = '1';

                if($item['gambar']!=null && $item['gambar']!='' && $item['gambar']!='-'){
                    $str = $item['gambar'];
                    $explode = explode('/', $str);
                    if ($explode[0].'/' == $this->config->item('site')) {
                        unset($explode[0]);
                        $gambar = implode('/', $explode);
                    } else {
                        $gambar = $item['gambar'];
                    }

                    $get_data[$i]['gambar'] = base_url().$gambar;
                    $get_data[$i]['gambar_thumb'] = $this->file_thumb($get_data[$i]['gambar']);
                } else {
                    $get_data[$i]['gambar'] = $this->no_img();
                    $get_data[$i]['gambar_thumb'] = $this->no_img();
                }
            }
        } else if($table=='brg_act') {
            foreach($get_data as $i => $item){
                if($item['gambar']!=null || $item['gambar']!=''){
                    $get_data[$i]['gambar'] = base_url().$item['gambar'];
                    $get_data[$i]['gambar_thumb'] = $this->file_thumb($get_data[$i]['gambar']);
                } else {
                    $get_data[$i]['gambar'] = $this->no_img();
                    $get_data[$i]['gambar_thumb'] = $this->no_img();
                }
            }
        } else if($table=='bill') {
            foreach($get_data as $i => $item) {
                $get_diskon = $this->m_crud->get_data("Promo", "pildiskon, diskon", "kd_brg='".$item['kd_brg']."' and '".$tanggal."' between daritgl and sampaitgl");

                $lokasi = $param;
                $get_hrg_lokasi = $this->m_crud->get_data("barang_hrg", "*", "barang='".$item['kd_brg']."' and lokasi='".$lokasi."'");
				if ($get_hrg_lokasi != null) {
                    $get_data[$i]['hrg_jual'] = $get_hrg_lokasi['hrg_jual_1'];
                    $get_data[$i]['service'] = $get_hrg_lokasi['service']==null?0.0:$get_hrg_lokasi['service'];
                    $get_data[$i]['ppn'] = $get_hrg_lokasi['ppn']==null?0.0:$get_hrg_lokasi['ppn'];
                }

                //if ($get_hrg_lokasi != null) {
                 //   $get_data[$i]['hrg_jual'] = $get_hrg_lokasi['hrg_jual_1'];
                  //  $get_data[$i]['service'] = $get_hrg_lokasi['service'];
                    //$get_data[$i]['ppn'] = $get_hrg_lokasi['ppn'];
                //}

                if ($get_diskon != null) {
                    $get_data[$i]['jenis_diskon'] = $get_diskon['pildiskon'];
                    $get_data[$i]['diskon'] = $get_diskon['diskon'];
                } else {
                    $get_data[$i]['jenis_diskon'] = '%';
                    $get_data[$i]['diskon'] = 0;
                }

                if($item['gambar']!=null || $item['gambar']!='') {
                    $get_data[$i]['gambar'] = base_url().$item['gambar'];
                    $get_data[$i]['gambar_thumb'] = $this->file_thumb($get_data[$i]['gambar']);
                } else {
                    $get_data[$i]['gambar'] = $this->no_img();
                    $get_data[$i]['gambar_thumb'] = $this->no_img();
                }
            }
        } else if ($table=='adjust') {
            foreach ($get_data as $i => $item) {
                $get_data[$i]['type'] = $item['status']=='+'?'Tambah':'Kurang';
            }
        } else if ($table=='customer') {
            foreach ($get_data as $i => $item) {
                $get_data[$i]['id'] = $item['kd_cust'];
                if ($item['foto'] == '' || $item['foto'] == null || $item['foto'] == '-') {
                    $get_data[$i]['foto'] = $this->user_img_default();
                } else {
                    $get_data[$i]['foto'] = base_url().$item['foto'];
                }
            }
        } else if ($table=='kel_brg') {
            foreach ($get_data as $key => $item) {
                $get_data[$key]['nm_kel_brg'] = strtoupper($item['nm_kel_brg']);
                if ($item['gambar'] == '' || $item['gambar'] == null || $item['gambar'] == '-') {
                    $get_data[$key]['foto'] = $this->no_img();
                    $get_data[$key]['foto_thumb'] = $this->no_img();
                } else {
                    $get_data[$key]['foto'] = base_url().$item['gambar'];
                    $get_data[$key]['foto_thumb'] = $this->file_thumb($get_data[$key]['foto']);
                }

                unset($get_data[$key]['gambar']);
            }
        } else if ($table=='group2') {
            foreach ($get_data as $key => $item) {
                $get_data[$key]['Nama'] = strtoupper($item['Nama']);
            }
        } else if ($table=='kel_brg_online') {
            foreach ($get_data as $key => $item) {
                $get_data[$key]['kel_brg'] = $item['id_kel_brg'];
                $get_data[$key]['nm_kel_brg'] = $item['nama'];
                if ($item['gambar'] == '' || $item['gambar'] == null || $item['gambar'] == '-') {
                    $get_data[$key]['foto'] = $this->no_img();
                    $get_data[$key]['foto_thumb'] = $this->no_img();
                } else {
                    $get_data[$key]['foto'] = base_url().$item['gambar'];
                    $get_data[$key]['foto_thumb'] = $this->file_thumb($get_data[$key]['foto']);
                }

                unset($get_data[$key]['gambar']);
            }
        } else if ($table=='bank') {
            foreach ($get_data as $key => $item) {
                if ($item['foto'] == '' || $item['foto'] == null || $item['foto'] == '-') {
                    $get_data[$key]['foto'] = $this->no_img();
                    $get_data[$key]['foto_thumb'] = $this->no_img();
                } else {
                    $get_data[$key]['foto'] = base_url().$item['foto'];
                    $get_data[$key]['foto_thumb'] = $this->file_thumb($get_data[$key]['foto']);
                }
            }
        } else if ($table=='berita') {
            foreach ($get_data as $key => $item) {
                if ($item['foto'] == '' || $item['foto'] == null || $item['foto'] == '-') {
                    $get_data[$key]['foto'] = $this->no_img();
                    $get_data[$key]['foto_thumb'] = $this->no_img();
                } else {
                    $get_data[$key]['foto'] = base_url().$item['foto'];
                    $get_data[$key]['foto_thumb'] = $this->file_thumb($get_data[$key]['foto']);
                }
            }
        } else if ($table=='intro') {
            foreach ($get_data as $key => $item) {
                if ($item['tipe'] == 'foto') {
                    $get_data[$key]['background'] = base_url().$item['background'];
                }
            }
        } else if ($table=='riwayat_belanja') {
            foreach ($get_data as $key => $item) {
                $diskon = $item['disc_item']+$item['dis_rp'];
                $net = $item['st']-$diskon;
                $gs = $net+$item['tax']+$item['service'];

                $get_data[$key]['net'] = $net;
                $get_data[$key]['diskon_all'] = $diskon;
                $get_data[$key]['total_belanja'] = $gs;
            }
        } else if ($table=='report_feedback') {
            foreach ($get_data as $key => $item) {
                $get_data[$key]['tgl_transaksi'] = date('Y-m-d', strtotime($item['tgl'])).' '.date('H:i:s', strtotime($item['jam']));
            }
        } else if ($table=='detail_belanja') {
            foreach ($get_data as $key => $item) {
                if($item['gambar']!=null && $item['gambar']!='' && $item['gambar']!='-'){
                    $str = $item['gambar'];
                    $explode = explode('/', $str);
                    if ($explode[0].'/' == $this->config->item('site')) {
                        unset($explode[0]);
                        $gambar = implode('/', $explode);
                    } else {
                        $gambar = $item['gambar'];
                    }

                    $get_data[$key]['gambar'] = base_url().$gambar;
                    $get_data[$key]['gambar_thumb'] = $this->file_thumb($get_data[$i]['gambar']);
                } else {
                    $get_data[$key]['gambar'] = $this->no_img();
                    $get_data[$key]['gambar_thumb'] = $this->no_img();
                }
            }
        } else if ($table=='hrg_ks') {
            $lokasi = $param;
            foreach ($get_data as $key => $item) {
                $get_hrg_lokasi = $this->m_crud->get_data("barang_hrg", "*", "barang='".$item['kd_brg']."' and lokasi='".$lokasi."'");

                if ($get_hrg_lokasi != null) {
                    $get_data[$key]['hrg_jual_1'] = $get_hrg_lokasi['hrg_jual_1'];
                }
            }
        } else if ($table=='area') {
            foreach ($get_data as $key => $item) {
                if ($item['gambar'] == '' || $item['gambar'] == null || $item['gambar'] == '-') {
                    $get_data[$key]['foto'] = $this->no_img();
                    $get_data[$key]['foto_thumb'] = $this->no_img();
                } else {
                    $get_data[$key]['foto'] = base_url().$item['gambar'];
                    $get_data[$key]['foto_thumb'] = $this->file_thumb($get_data[$key]['foto']);
                }
                unset($get_data[$key]['gambar']);
            }
        }

        return $get_data;
	}

    public function insert_log_api($data=null) {
        if ($data != null && is_array($data)) {
            $kode = $this->generate_kode('LOG', null, date('ymd'));
            $data['id_log'] = $kode;
            $data['tanggal'] = date('Y-m-d H:i:s');
            $data['status'] = '0';
            $data['param'] = 'send';
           // $this->m_crud->create_data("log_api", $data);
//            $this->curl->simple_get($this->config->item('urlNode'));
        }
        return true;
	}

    public function insert_log_tr($data=null) {
        if ($data != null && is_array($data)) {
            $kode = $this->generate_kode('LOGTR', $this->config->item('lokasi'), date('ymd'));
            $data['id_log'] = $kode;
            $data['tanggal'] = date('Y-m-d H:i:s');
            $data['status'] = '0';
            $data['param'] = 'send';
           // $this->m_crud->create_data("log_api", $data);
//            $this->curl->simple_get($this->config->item('urlNode'));
        }
        return true;
    }

    public function insert_log_print($data=null, $app, $lokasi) {
        $item = $this->m_crud->get_data('lokasi', "kode, nama_toko nama, ket alamat, serial, nama header1, ket header2, kota header3, web header4, footer1, footer2, footer3, footer4", "kode = '".$lokasi."'");

        $head_foot = array(
            'header1' => $item['header1'],
            'header2' => $item['header2'],
            'header3' => $item['header3'],
            'header4' => $item['header4'],
            'footer1' => $item['footer1'],
            'footer2' => $item['footer2'],
            'footer3' => $item['footer3'],
            'footer4' => $item['footer4']
        );
        if ($data != null && is_array($data)) {
            foreach ($data as $key => $item) {
                if (count($item['list']) > 0) {
                    $kode = $this->generate_kode('PRINT', null, date('ymd'));
                    $data[$key]['log_print'] = $kode;
                    $item['log_print'] = $kode;
                    $data_log = array(
                        'id_log' => $kode,
                        'tanggal' => date('Y-m-d H:i:s'),
                        'data_print' => json_encode($item),
                        'status' => '0',
                        'app' => $app,
                        'head_foot' => json_encode($head_foot)
                    );
                    $this->m_crud->create_data("log_print", $data_log);
                }
            }
        }
        return $data;
    }

    public function replace_kutip($string, $tipe='replace') {
        if ($tipe == 'replace') {
            $string = str_replace("'", "`", $string);
        } else if($tipe == 'restore') {
            $string = str_replace("`", "'", $string);
        }

        return $string;
    }

    /*Member*/
    public function email_invoice($email, $pesan) {
        $decode = json_decode($pesan, true);
        $situs = $this->site_data();

        $to = strip_tags($email);
        $subject = 'Invoice '.$situs->nama.':'.$decode['id_orders'];
        $logo = base_url().$this->file_thumb($situs->logo);
        $message = '
	    <!doctype html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Invoice</title>
            
            <style>
            .invoice-box {
                max-width: 800px;
                margin: auto;
                padding: 30px;
                border: 1px solid #eee;
                box-shadow: 0 0 10px rgba(0, 0, 0, .15);
                font-size: 16px;
                line-height: 24px;
                font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif;
                color: #555;
            }
            
            .invoice-box table {
                width: 100%;
                line-height: inherit;
                text-align: left;
            }
            
            .invoice-box table td {
                padding: 5px;
                vertical-align: top;
            }
            
            .invoice-box table tr td:nth-child(2) {
                text-align: right;
            }
            
            .invoice-box table tr.top table td {
                padding-bottom: 20px;
            }
            
            .invoice-box table tr.top table td.title {
                font-size: 45px;
                line-height: 45px;
                color: #333;
            }
            
            .invoice-box table tr.information table td {
                padding-bottom: 40px;
            }
            
            .invoice-box table tr.heading td {
                background: #eee;
                border-bottom: 1px solid #ddd;
                font-weight: bold;
            }
            
            .invoice-box table tr.details td {
                padding-bottom: 20px;
            }
            
            .invoice-box table tr.item td{
                border-bottom: 1px solid #eee;
            }
            
            .invoice-box table tr.item.last td {
                border-bottom: none;
            }
            
            .invoice-box table tr.total td:nth-child(2) {
                border-top: 2px solid #eee;
                font-weight: bold;
            }
            
            @media only screen and (max-width: 600px) {
                .invoice-box table tr.top table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }
                
                .invoice-box table tr.information table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }
            }
            
            /** RTL **/
            .rtl {
                direction: rtl;
                font-family: Tahoma, \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif;
            }
            
            .rtl table {
                text-align: right;
            }
            
            .rtl table tr td:nth-child(2) {
                text-align: left;
            }
            </style>
        </head>
        
        <body>
            <div class="invoice-box">
                <table cellpadding="0" cellspacing="0">
                    <tr class="top">
                        <td colspan="2">
                            <table>
                                <tr>
                                    <td class="title">
                                        <img src="'.$logo.'" style="width:100%; max-width:200px;">
                                    </td>                                    
                                    <td>
                                        Invoice : '.$decode['id_orders'].'<br>
                                        Dipesan: '.date('d F, Y', strtotime($decode['tanggal'])).'<br>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <tr class="information">
                        <td colspan="2">
                            <table>
                                <tr>
                                    <td>
                                        '.$situs->nama.'
                                    </td>                                    
                                    <td>
                                        '.$decode['penerima'].'<br>
                                        '.$decode['tlp'].'
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>                    
                    <tr class="heading">
                        <td>Payment Method</td>                        
                        <td></td>
                    </tr>                    
                    <tr class="details">
                        <td>Transfer '.$decode['bank'].' ('.$decode['no_rek'].' a.n. '.$decode['an_rek'].')</td>
                        <td>'.number_format($decode['total']).'</td>
                    </tr>                    
                    <tr class="heading">
                        <td>Item</td>
                        <td>Price</td>
                    </tr>
                    '.$decode['list'].'                    
                    <tr class="item">
                        <td>'.$decode['kurir'].'</td>
                        <td>'.number_format($decode['ongkir']).'</td>
                    </tr>
                    <tr class="item last">
                        <td>Kode Unik</td>
                        <td>'.number_format($decode['kode_unik']).'</td>
                    </tr>                    
                    <tr class="total">
                        <td></td>                        
                        <td>Total: '.number_format($decode['total']).'</td>
                    </tr>
                </table>
            </div>
        </body>
        </html>
	    ';

        $headers = "From: ".$situs->nama." <" . strip_tags('no-reply@evieeffendi.com') . "> \r\n";
        //$headers .= "CC: agrowisata_n8@yahoo.com \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


        if (mail($to,$subject,$message,$headers) == true) {
            return true;
        } else {
            return false;
        }
    }

    public function rajaongkir_provinsi() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/province",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: d9270415ac1ccd0f5a61cda8d7e1e82d"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function rajaongkir_kota() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/city",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: d9270415ac1ccd0f5a61cda8d7e1e82d"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function rajaongkir_kecamatan($id) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/subdistrict?city=".$id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: d9270415ac1ccd0f5a61cda8d7e1e82d"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function rajaongkir_cost($data) {
        $asal = $this->setting();
        $post = json_decode($data, true);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=".$asal->kecamatan_pengirim."&originType=subdistrict&destination=".$post['tujuan']."&destinationType=subdistrict&weight=".$post['berat']."&courier=".$post['kurir']."",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: d9270415ac1ccd0f5a61cda8d7e1e82d"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function rajaongkir_resi($data) {
        $post = json_decode($data, true);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/waybill",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "waybill=".$post['resi']."&courier=".$post['kurir']."",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: d9270415ac1ccd0f5a61cda8d7e1e82d"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function numberToRomanRepresentation($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    public function date_romawi ($param=null) {
        if ($param == 'time') {
            return date('ymd') . '/' . $this->m_website->numberToRomanRepresentation(date('H')) . $this->m_website->numberToRomanRepresentation(date('i')) . $this->m_website->numberToRomanRepresentation(date('s'));
        } else {
            return $this->m_website->numberToRomanRepresentation(date('y')) . $this->m_website->numberToRomanRepresentation(date('m')) . $this->m_website->numberToRomanRepresentation(date('d')) . '/' . $this->m_website->numberToRomanRepresentation(date('H')) . $this->m_website->numberToRomanRepresentation(date('i')) . $this->m_website->numberToRomanRepresentation(date('s'));
        }
    }

    public function batas_waktu($tipe=null, $tanggal=null){
        if($tipe=='-'){ $tipe='-'; } else{ $tipe='+'; }
        if($tanggal==null){ $tanggal=date('Y-m-d H:i:s'); }

        //$batas_hari = date('Y-m-d').' 23:59:59';
        $batas_waktu = date('Y-m-d H:i:s', strtotime($tipe.'6 hour', strtotime($tanggal)));
        //if($batas_waktu > $batas_hari){ $batas_waktu = $batas_hari; }

        return $batas_waktu;
    }

    public function get_kodeunik($nominal=0, $rekening=null){
        /* //kodeunik ngacak
        $kode_unik = 10;
        $param = true;
        while ($param) {
            $kode_unik = mt_rand( 10, 999 );
            $cek_kodeunik_donasi = $this->m_crud->get_data("bayar_donasi", "id_bayar_donasi", ($rekening!=null?"no_rek = '".$rekening."' and ":null)."nominal=".$nominal." AND kodeunik=".$kode_unik." AND status IN ('0')");
            $cek_kodeunik_kafalah = $this->m_crud->get_data("bayar_kafalah", "id_bayar_kafalah", ($rekening!=null?"no_rek = '".$rekening."' and ":null)."ifnull((select sum(nominal) from bayar_kafalah_anggota where bayar_kafalah=id_bayar_kafalah),0)=".$nominal." AND kodeunik=".$kode_unik." AND status IN ('0')");
            if ($cek_kodeunik_donasi==null && $cek_kodeunik_kafalah==null) {
            $param = false;
            } else {
            $param = true;
            }
        } */

        //kodeunik berurut asc
        //$cek_kodeunik = $this->m_crud->select_union('kodeunik','bayar_donasi',($rekening!=null?"no_rek = '".$rekening."' and ":null)."nominal=".$nominal." AND status IN ('0')", 'kodeunik','bayar_kafalah', ($rekening!=null?"no_rek = '".$rekening."' and ":null)."ifnull((select sum(nominal) from bayar_kafalah_anggota where bayar_kafalah=id_bayar_kafalah),0)=".$nominal." AND status IN ('0')", 'kodeunik asc');
        $cek_kodeunik = $this->m_crud->read_data("data_kodeunik", "kodeunik", ($rekening!=null?"no_rek like '%".$rekening."%' and ":null)."nominal=".$nominal, "kodeunik ASC");
        $kode_unik = 11;
        if ($cek_kodeunik != null) {
            for ($i=$kode_unik; $i<=999; $i++) {
                if ($i != $cek_kodeunik[$i-$kode_unik]['kodeunik']) {
                    $kode_unik = $i;
                    break;
                }
            }
        }

        return $kode_unik;
    }

    public function get_poin($member) {
        $get_poin = $this->m_crud->get_data("kartu_poin", "ISNULL(SUM(poin_masuk-poin_keluar), 0) poin", "kd_cust='".$member."'");

        return $get_poin['poin'];
    }

    public function get_deposit($member) {
        $get_saldo = $this->m_crud->get_data("kartu_deposit", "ISNULL(SUM(saldo_masuk-saldo_keluar), 0) saldo", "member='".$member."'");

        return $get_saldo['saldo'];
    }

    public function sms_otentikasi($data) {
        $pesan = $this->site_data()->title.' - Otentikasi '.$data['kode'].' . Demi keamanan jangan berikan Otentikasi ini kepada siapa pun.';

        return $this->kirim_sms($data['tlp'], $pesan);
    }

    public function kirim_sms($tujuan='08112233253', $pesan='Cek Sms') {
        $user_key = "7jvogb";
        $pass_key = "i1mmc9htbm";

        $data = array(
            "userkey" => $user_key,
            "passkey" => $pass_key,
            "nohp" => $tujuan,
            "pesan" => $pesan
        );

        file_get_contents("https://reguler.zenziva.net/apps/smsapi.php?".http_build_query($data));

        return true;
    }

    //start api one signal
    public function curl_onesignal($data_notif){
        $fields = array(
            'app_id' => "dc1693fb-571a-486d-9d46-a53c66ae5d2b",
            'data' => $data_notif['data'],
            'headings' => $data_notif['headings'],
            'contents' => $data_notif['contents']
        );
        if(isset($data_notif['include_player_ids']) && $data_notif['include_player_ids']!=null){
            $fields['include_player_ids'] = $data_notif['include_player_ids'];
        }
        if(isset($data_notif['big_picture']) && $data_notif['big_picture']!=null){
            $fields['big_picture'] = $data_notif['big_picture'];
        }
        if(isset($data_notif['included_segments']) && $data_notif['included_segments']!=null){
            //'included_segments' => array('All'),
            //'included_segments' => array('Active Users'),
            $fields['included_segments'] = $data_notif['included_segments'];
        }

        $fields = json_encode($fields);
        //print("\nJSON sent:\n");
        //print($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $data_notif['url']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic NzBjMWQ1NDYtOTgzMS00ZTliLTg5MmItNmFlOWY0NjYxOTBj'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function create_notif($data){
        $data_notif = array(
            'url' => "https://onesignal.com/api/v1/notifications",
            'included_segments' => isset($data['segment'])?array($data['segment']):null,
            'include_player_ids' => isset($data['member'])?array($data['member']):null,
            'data' => isset($data['data'])?$data['data']:array(),
            'big_picture' => isset($data['big_picture'])?$data['big_picture']:null,
            'headings' => array("en" => $data['head']),
            'contents' => array("en" => $data['content'])
        );
        $response = $this->curl_onesignal($data_notif);
        return $response;
    }
    //end api one signal

    public function get_distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) + pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }

    public function api_npayment($param="cek_server", $data="", $header=null) {
        $url = "http://ppob.patungantanah.com/api/";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url.$param);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if ($header != null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec ($ch);

        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $result;
        }
    }
}
