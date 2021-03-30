<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Konsinyasi extends CI_Controller {

	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
		$site_data = $this->m_website->site_data();
		$this->site = str_replace(' ', '', strtolower($site_data->title));
		$this->control = 'Konsinyasi';
		
		$this->user = $this->session->userdata($this->site . 'user');
		$this->username = $this->session->userdata($this->site . 'username');
        $this->menu_group = $this->m_crud->get_data('Setting', 'as_deskripsi, as_group1, as_group2', "Kode = '1111'");

        $this->data = array(
			'site' => $site_data,
			'account' => $this->m_website->user_data($this->user),
			'access' => $this->m_website->user_access_data($this->user),
            'menu_group' => $this->menu_group
		);
		
		$this->output->set_header("Cache-Control: no-store, no-cache, max-age=0, post-check=0, pre-check=0");
	}
	
	public function index(){
		redirect(strtolower($this->control).'/dashboard');
	}

    function access_denied($str){
        if(substr($this->m_website->user_access_data($this->user)->access,$str,1) == 0){
            echo "<script>alert('Access Denied'); window.location='".base_url()."site';</script>";
        }
    }
	
	public function persediaan_konsinyasi($page=1){
		$this->access_denied(91);
		$data = $this->data;
		$function = 'persediaan_konsinyasi';
		$view = $this->control . '/';
		$table = null;
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }  
		$data['title'] = 'Persediaan Konsinyasi';
		$data['page'] = $this->session->userdata($this->site . 'admin_menu');
		$data['content'] = $view.$function;
		$data['table'] = $table;
		
		$where = "group2 = 'KS'";
		//$where = null; 
		$where_stock = "kartu_stock.kd_brg = barang.kd_brg and kartu_stock.Lokasi NOT IN ('MUTASI', 'Retur')"; 
		$tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');

        if(isset($_POST['search']) || isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'supplier' => $_POST['supplier']));
        } 

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $supplier = $this->session->search['supplier']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        if (isset($date) && $date != null) {
            $tgl_awal = $date1; $tgl_akhir = $date2;
        }

        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=""; $where.=" and kd_brg like '%".$search."%' or barcode like '%".$search."%' or nm_brg like '%".$search."%'"; }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where_stock.=""; $where_stock.=" and lokasi = '".$lokasi."'"; }
        if(isset($supplier)&&$supplier!=null){ ($where==null)?null:$where.=""; $where.=" and Group1 = '".$supplier."'"; }

        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/';
        $config['total_rows'] = $this->m_crud->count_data("barang", 'kd_brg', ($where==null?null:$where));
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

		$stock_awal = "isnull((select sum(stock_in - stock_out) from kartu_stock where ".$where_stock." and tgl < '".$tgl_awal." 00:00:00'),0) as stock_awal";
		$stock_masuk = "isnull((select sum(stock_in) from kartu_stock where ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_masuk";
		$stock_keluar = "isnull((select sum(stock_out) from kartu_stock where ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_keluar";
		$data['report'] = $this->m_crud->select_limit('barang', "kd_brg, barcode, nm_brg, ".$stock_awal.", ".$stock_masuk.", ".$stock_keluar, $where, "kd_brg ASC", null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
		$get_total = $this->m_crud->read_data('barang', "kd_brg, barcode, nm_brg, ".$stock_awal.", ".$stock_masuk.", ".$stock_keluar, $where, "kd_brg ASC", null);
		
        $tstaw = 0; $tstma = 0; $tstke = 0; $tstak = 0;
        foreach ($get_total as $row) {
            $tstaw = $tstaw + (int)$row['stock_awal'];
            $tstma = $tstma + (int)$row['stock_masuk'];
            $tstke = $tstke + (int)$row['stock_keluar'];
            $tstak = $tstak + ((int)$row['stock_awal']+(int)$row['stock_masuk']-(int)$row['stock_keluar']);
        }

        $data['tstaw'] = $tstaw;
        $data['tstma'] = $tstma;
        $data['tstke'] = $tstke;
        $data['tstak'] = $tstak;
		
		if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->read_data('barang', "kd_brg, barcode, nm_brg, ".$stock_awal.", ".$stock_masuk.", ".$stock_keluar, $where, "kd_brg ASC");
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
                    'A'=>'Kode Barang', 'B'=>'Barcode', 'C'=>'Nama Barang', 'D'=>'Stock Awal', 'E'=>'Stock Masuk', 'F'=>'Stock Keluar', 'G'=>'Stock Total'
                )
            );

            $i = 0;
            foreach($baca as $row => $value){
                $i++;
                $body[$row] = array(
                    $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['stock_awal']+0, $value['stock_masuk']+0, $value['stock_keluar']+0, ($value['stock_awal']+$value['stock_masuk']-$value['stock_keluar'])+0
                );
            } 
			
            $body[$i] = array('TOTAL', '', '', $tstaw, $tstma, $tstke, $tstak);
            array_push($header['merge'], 'A'.($i+6).':C'.($i+6).'');
            $header['font']['A'.($i+6).':G'.($i+6).''] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }
		/*$data['report'] = $this->m_crud->join_data('kartu_stock as ks',
			"br.kd_brg, br.barcode, br.nm_brg, isnull(sum(saldo_awal + stock_in - stock_out), 0) as stock", 
			'barang as br', 'ks.kd_brg = br.kd_brg',
			$where, 'br.nm_brg', 'br.kd_brg, br.barcode, br.nm_brg'
		); */
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else { $this->load->view('bo/index', $data); }
	}

	public function penjualan_konsinyasi($action = null, $id = null, $filter = null) {
        $this->access_denied(92);
        $data = $this->data;
        $function = 'penjualan_konsinyasi';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = 'Group1';
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Penjualan Konsinyasi';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $periode = 'Periode : None'; $q_lokasi = 'Semua Lokasi';

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'periode'=>$_POST['periode'], 'lokasi' => $_POST['lokasi'], 'supplier' => $_POST['supplier']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $supplier = $this->session->search['supplier'];  $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);

        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= "";
            $where .= " and LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";$periode = "Periode : ".$tgl_awal." - ".$tgl_akhir;
        } else {
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
            ($where == null) ? null : $where .= "";
            $where .= " and LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";$periode = "Periode : ".$tgl_awal." - ".$tgl_akhir;
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=""; $where.=" and mt.Lokasi = '".$lokasi."'"; $q_lokasi = "Lokasi : ".$lokasi.""; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=""; $where.=" and (gr.Kode like '%".$search."%' or gr.Nama like '%".$search."%')"; }

        $data['periode'] = $periode;
        $data['q_lokasi'] = $q_lokasi;
        $data['lokasi'] = $lokasi;
        $data['tgl_awal'] = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data("Master_Trx mt, Det_Trx dt, barang br, Group1 gr", 'gr.Kode', "mt.kd_trx=dt.kd_trx AND br.Group1=gr.Kode AND br.Group2='KS' AND dt.kd_brg=br.kd_brg".($where==null?'':$where), "gr.Kode asc", "gr.Kode");
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

        $q_tgl=" AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";

        $data['report'] = $this->m_crud->select_limit("Master_Trx mt, Det_Trx dt, barang br, Group1 gr", "gr.Kode, gr.Nama, SUM(dt.qty) qty, SUM(dt.qty * dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, SUM(mt.dis_rp) dis_trans, SUM(dt.qty*dt.hrg_beli) total_beli", "mt.HR = 'S' AND dt.qty > 0 AND mt.kd_trx=dt.kd_trx AND br.Group1=gr.Kode AND br.Group2='KS' AND dt.kd_brg=br.kd_brg".($where==null?'':$where), "gr.Kode asc", "gr.Nama, gr.Kode", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        $det_report = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br, Group1 gr", "gr.Kode, gr.Nama, SUM(dt.qty) qty, SUM(dt.qty * dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, SUM(mt.dis_rp) dis_trans, SUM(dt.qty*dt.hrg_beli) total_beli", "mt.HR = 'S' AND dt.qty > 0 AND mt.kd_trx=dt.kd_trx AND br.Group1=gr.Kode AND br.Group2='KS' AND dt.kd_brg=br.kd_brg".($where==null?'':$where), "gr.Kode asc", "gr.Nama, gr.Kode");
        $tqt = 0; $tgs = 0; $tdi = 0; $tns = 0; $ttb = 0;
        foreach ($det_report as $row) {
            $tqt = $tqt + (int)$row['qty'];
            $tgs = $tgs + (float)$row['gross_sales'];
            $tdi = $tdi + (float)$row['diskon_item'];
            $tns = $tns + (float)$row['gross_sales']-(float)$row['diskon_item'];
            $ttb = $ttb + (float)$row['total_beli'];
        }

        $data['tqt'] = $tqt;
        $data['tgs'] = $tgs;
        $data['tdi'] = $tdi;
        $data['tns'] = $tns;
        $data['ttb'] = $ttb;

        if(isset($_POST['to_excel'])){
            $baca = $det_report;
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
                    'A'=>'Kode Supplier', 'B'=>'Nama Supplier', 'C'=>'Qty Jual', 'D'=>'Gross Sales', 'E'=>'Diskon Item', 'F'=>'Net Sales', 'G'=>'Jumlah Beli'
                )
            );

            $i = 0;
            foreach($baca as $row => $value){
                $i++;
                $body[$row] = array(
                    $value['Kode'], $value['Nama'], $value['qty']+0, $value['gross_sales']+0, $value['diskon_item']+0, $value['gross_sales']-$value['diskon_item']+0, $value['total_beli']+0
                );
            }

            $body[$i] = array('TOTAL', '', $tqt, $tgs, $tdi, $tns, $ttb);
            array_push($header['merge'], 'A'.($i+6).':B'.($i+6).'');
            $header['font']['A'.($i+6).':G'.($i+6).''] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            $q_filter = '';
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            if ($filter!=null) {$filter=json_decode(base64_decode($filter)); $q_filter = ' AND '.$filter[0].' like \'%'.$filter[1].'%\'';}
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('Group1', "*", "Kode = '".$id."'");

            $q_tgl = "AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";
            $where = ""; ($lokasi==null)?"":$where.=" AND mt.Lokasi='".$lokasi."'";
            $lokasi_trx = ""; ($lokasi==null)?"":$lokasi_trx=" AND mtrx.lokasi='".$lokasi."'";
            $where_stock = "kd_brg=br.kd_brg "; ($lokasi==null)?$where_stock.=" AND Lokasi NOT IN ('MUTASI', 'Retur')":$where_stock.=" AND Lokasi='".$lokasi."'";
            $stock_awal = "isnull((select sum(stock_in - stock_out) from kartu_stock where ".$where_stock." and tgl < '".$tgl_awal." 00:00:00'),0) as stock_awal";
            $stock_periode = "isnull((select sum(stock_in) from kartu_stock where ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_periode";
            $stock_periode2 = ", isnull((select sum(stock_in-stock_out) from kartu_stock where kd_brg=br.kd_brg and tgl > '".$tgl_awal." 00:00:00'), 0) as stock_periode";
            $retur = "isnull((select sum(dtrx.qty) from Det_Trx dtrx, Master_Trx mtrx where dtrx.kd_brg=br.kd_brg and dtrx.qty < 0 and dtrx.kd_trx=mtrx.kd_trx and mtrx.tgl >= '".$tgl_awal." 00:00:00' and mtrx.tgl <= '".$tgl_akhir." 23:59:59'".$lokasi_trx."),0) as retur";
            $jual = "isnull((select sum(dtrx.qty) from Det_Trx dtrx, Master_Trx mtrx where dtrx.kd_brg=br.kd_brg and dtrx.qty > 0 and dtrx.kd_trx=mtrx.kd_trx and mtrx.tgl >= '".$tgl_awal." 00:00:00' and mtrx.tgl <= '".$tgl_akhir." 23:59:59'".$lokasi_trx."),0) as jual";
            $data['report_detail'] = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "SUM(dt.dis_persen) diskon_item, AVG(dt.hrg_beli) hrg_beli, AVG(dt.hrg_jual) hrg_jual, br.kd_brg, br.Deskripsi, br.nm_brg, br.barcode, br.satuan, ".$stock_awal.",".$stock_periode.",".$retur.",".$jual."", "mt.HR = 'S' AND dt.qty > 0 AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND br.Group2='KS' AND br.Group1 = '".$id."' ".$q_tgl." ".$where, null, "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Detail Laporan Penjualan Konsinyasi</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
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
                                <td>'.$data['report']['Kode'].'</td>
                                <td></td>
                                <td colspan="3"><b>'.$periode.'</b></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Nama Supplier</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['Nama'].'</td>
                                <td></td>
                                <td colspan="3"><b>'.$q_lokasi.'</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>32,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function filter_barang () {
        $value = $_POST['value_'];
        $filter = $_POST['filter_'];
        $kode = $_POST['kode_'];
        $tgl_awal = date("Y-m-d");
        $tgl_akhir = date("Y-m-d");
        $q_filter = $_POST['q_filter_'];
        $lokasi = $_POST['lokasi_'];
        $q_tgl = $_POST['q_tgl_'];

        if ($_POST['tgl_awal_'] != null) {
            $tgl_awal = $_POST['tgl_awal_'];
        }

        if ($_POST['tgl_akhir_'] != null) {
            $tgl_akhir = $_POST['tgl_akhir_'];
        }

        ($q_tgl == null)?$q_tgl="":$q_tgl="AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";

        $list_tabel = '';
        $total = 0;
        $no = 1;

        $where = ""; ($lokasi==null)?"":$where.=" AND mt.Lokasi='".$lokasi."'";
        $where_stock = "kd_brg=br.kd_brg "; ($lokasi==null)?"":$where_stock.=" AND Lokasi='".$lokasi."' ";
        $stock_awal = "isnull((select sum(saldo_awal + stock_in - stock_out) from kartu_stock where ".$where_stock." and tgl < '".$tgl_awal." 00:00:00'),0) as stock_awal";
        $stock_masuk = "isnull((select sum(stock_in) from kartu_stock where ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_masuk";
        $stock_keluar = "isnull((select sum(stock_out) from kartu_stock where ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_keluar";
        $detail = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "SUM(dt.qty) jumlah, SUM(dt.qty * dt.hrg_beli) jumlah_beli, SUM(dt.dis_persen) diskon_item, SUM(dt.qty * hrg_jual) sub_total, AVG(dt.hrg_beli) hrg_beli, AVG(dt.hrg_jual) hrg_jual, br.kd_brg, br.Deskripsi, br.nm_brg, br.barcode, br.satuan, ".$stock_awal.",".$stock_masuk.",".$stock_keluar."", "mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND br.Group1 = '".$kode."' AND ".$filter." like '%".$value."%' ".$q_filter.$q_tgl." ".$where, null, "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan");

        foreach ($detail as $row) {
            $list_tabel .= '
            <tr>
                <td>'.$no.'</td>
                <td>'.$row['kd_brg'].'</td>
                <td>'.$row['barcode'].'</td>
                <td>'.$row['Deskripsi'].'</td>
                <td>'.$row['nm_brg'].'</td>
                <td class="text-right">'.($row['stock_awal']+0).'</td>
                <td class="text-right">'.($row['stock_masuk']+0).'</td>
                <td class="text-right">'.($row['stock_keluar']+0).'</td>
                <td class="text-right">'.($row['stock_awal']+$row['stock_masuk']-$row['stock_keluar']+0).'</td>
                <td class="text-right">'.($row['jumlah']+0).'</td>
                <td>'.$row['satuan'].'</td>
                <td class="text-right">'.number_format($row['hrg_beli']).'</td>
                <td class="text-right">'.number_format($row['hrg_jual']).'</td>
                <td class="text-right">'.number_format($row['diskon_item']).'</td>
                <td class="text-right">'.number_format($row['jumlah_beli']).'</td>
                <td class="text-right">'.number_format($row['sub_total']-$row['diskon_item']).'</td>
            </tr>
            ';
            $no++;
            $total = $total + ($row['sub_total']-$row['diskon_item']);
        }

        echo json_encode(array('table' => $list_tabel, 'jumlah' => number_format($total)));
    }
	
}

