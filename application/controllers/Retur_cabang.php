<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retur_cabang extends CI_Controller {

	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
		$site_data = $this->m_website->site_data();
		$this->site = str_replace(' ', '', strtolower($site_data->title));
		$this->control = 'Retur_cabang';
		
		$this->user = $this->session->userdata($this->site . 'user');
		$this->username = $this->session->userdata($this->site . 'username');
        $this->menu_group = $this->m_crud->get_data('Setting', 'as_deskripsi, as_group1, as_group2, status_barang', "Kode = '1111'");

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
	
	public function penerimaan_retur_cabang(){
        $this->access_denied(71);
		$data = $this->data;
		$function = 'penerimaan_retur_cabang';
		$view = $this->control . '/';
		$table = null;
		$data['title'] = 'Penerimaan Retur Cabang';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		
		$where = "keterangan = 'Retur Non Approval' and lokasi = 'Retur'";
		if(isset($_POST['search'])){
			if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; ($where==null)?$where.="right(kd_trx, len(kd_trx)-14) = '".$lokasi."'":$where.=" and right(kd_trx, len(kd_trx)-14) = '".$lokasi."'"; }
			if(isset($_POST['no_trx'])&&$_POST['no_trx']!=null){ $search = $_POST['no_trx']; ($where==null)?$where.="kd_trx like '%".$search."%'":$where.=" and kd_trx like '%".$search."%'"; }
		}
		$data['report'] = $this->m_crud->read_data('kartu_stock as ks', "kd_trx, (select nama from lokasi where serial = right(kd_trx, len(kd_trx)-14)) lokasi, tgl, count(kd_brg) as tot_item, sum(stock_in) as tot_qty, isnull((select sum(stock_out) from kartu_stock where lokasi = 'Retur' and keterangan = ('Retur Approval ' + ks.kd_trx) ),0) as qty_approval", $where, 'tgl desc', 'kd_trx, tgl');
		
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else { $this->load->view('bo/index', $data); }
	}
	
	public function approval_retur_cabang($kd_trx = null) {
		//$this->access_denied(0);
		$data = $this->data;
		$function = 'approval_retur_cabang';
		$view = $this->control . '/';
		$table = null;
		$data['title'] = 'Approval Retur Cabang';
		$data['page'] = $function;
		$data['content'] = $view.$function;
		$data['table'] = $table;
		
		$kd_trx = base64_decode($kd_trx);
		
		$where = "kd_trx = '".$kd_trx."' and keterangan = 'Retur Non Approval' and lokasi = 'Retur'";
		if(isset($_POST['search'])){
			if(isset($_POST['barcode'])&&$_POST['barcode']!=null){ $search = $_POST['barcode']; ($where==null)?$where.="barcode = '".$search."'":$where.=" and barcode = '".$search."'"; }
		}
		$data['report'] = $kd_trx;
		$data['report_det'] = $this->m_crud->join_data('kartu_stock as ks', "ks.kd_brg, br.barcode, stock_in as qty, br.nm_brg, ks.hrg_beli, isnull((select sum(stock_out) from kartu_stock where kartu_stock.kd_brg = ks.kd_brg and keterangan = 'Retur Approval ".$kd_trx."'),0) as qty_approval", 'barang as br', 'ks.kd_brg = br.kd_brg', $where);
		
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); } 
		else { $this->load->view('bo/index', $data); }
	}

	public function retur_cabang_report($action = null, $id = null) {
		ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $this->access_denied(171);
        $data = $this->data;
        $function = 'retur_cabang_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Arsip Retur Cabang';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "replace(right(ks.kd_trx, 2),'-','')=lk.serial AND keterangan = 'Retur Non Approval' and lokasi = 'retur'";
        $having = null;
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'tipe' => $_POST['tipe'], 'kondisi' => $_POST['kondisi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $tipe = $this->session->search['tipe']; $kondisi = $this->session->search['kondisi'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        }else{
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }

        if(isset($tipe)) {
            if($tipe == '0') {
                $having = "isnull((select sum(stock_out) from kartu_stock where lokasi = 'Retur' and keterangan = ('Retur Approval ' + ks.kd_trx) ), 0) = 0";
            } else if ($tipe == '1') {
                $having = "isnull((select sum(stock_out) from kartu_stock where lokasi = 'Retur' and keterangan = ('Retur Approval ' + ks.kd_trx) ), 0) = sum(stock_in)";
            } else if ($tipe == '2'){
                $having = "isnull((select sum(stock_out) from kartu_stock where lokasi = 'Retur' and keterangan = ('Retur Approval ' + ks.kd_trx) ), 0) > 0 AND isnull((select sum(stock_out) from kartu_stock where lokasi = 'Retur' and keterangan = ('Retur Approval ' + ks.kd_trx) ), 0) <> sum(stock_in)";
            } else {
                $having = null;
            }
        }

        if(isset($kondisi)&&$kondisi!=null){ ($where==null)?null:$where.=" and "; $where.="keterangan2 like '%".$kondisi."%'"; }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="right(kd_trx, 1) = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(kd_trx like '%".$search."%' or kd_brg like '%".$search."%' or lokasi like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data("kartu_stock as ks, lokasi lk", 'kd_trx', $where, null, 'kd_trx', $having);
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

        $data['report'] = $this->m_crud->select_limit('kartu_stock as ks, lokasi lk', "kd_trx, lk.kode lokasi, tgl, count(kd_brg) as tot_item, sum(stock_in) as tot_qty, isnull((select sum(stock_out) from kartu_stock where lokasi = 'Retur' and keterangan = ('Retur Approval ' + ks.kd_trx) ),0) as qty_approval, keterangan2", $where, 'kd_trx desc', 'kd_trx, lk.kode, tgl, keterangan2', ($page-1)*$config['per_page']+1, ($config['per_page']*$page), $having);
		
        $detail = $this->m_crud->read_data('kartu_stock as ks, lokasi lk', "kd_trx, tgl, count(kd_brg) as tot_item, sum(stock_in) as tot_qty, isnull((select sum(stock_out) from kartu_stock where lokasi = 'Retur' and keterangan = ('Retur Approval ' + ks.kd_trx) ),0) as qty_approval, keterangan2", $where, 'kd_trx desc', 'kd_trx, tgl, keterangan2', 0, 0, $having);

        $ti = 0;
        $tq = 0;
        $qa = 0;
        $sl = 0;

        foreach ($detail as $row) {
            $selisih = (int)$row['tot_qty']-(int)$row['qty_approval'];
            $ti = $ti + $row['tot_item'];
            $tq = $tq + $row['tot_qty'];
            $qa = $qa + $row['qty_approval'];
            $sl = $sl + $selisih;
        }

        $data['tti'] = $ti;
        $data['ttq'] = $tq;
        $data['tqa'] = $qa;
        $data['tsl'] = $sl;

        if(isset($_POST['to_excel'])){
			/*
			<?php
			$no = 0;
			$dqt = 0;
			$dqa = 0;
			$dsl = 0;
			$where = "kd_trx = '".$row['kd_trx']."' and keterangan = 'Retur Non Approval' and lokasi = 'retur'";
			$detail = $this->m_crud->join_data('kartu_stock as ks', "ks.kd_brg, br.barcode, stock_in as qty, br.nm_brg, ks.hrg_beli, isnull((select sum(stock_out) from kartu_stock where kartu_stock.kd_brg = ks.kd_brg and keterangan = 'Retur Approval ".$row['kd_trx']."'),0) as qty_approval", 'barang as br', 'ks.kd_brg = br.kd_brg', $where);
			foreach($detail as $rows){
				$no++;
				$selisih = (int) $rows['qty']-(int) $rows['qty_approval'];
				?>
				<tr>
					<td><?=$no?></td>
					<td><?=$rows['kd_brg']?></td>
					<td><?=$rows['barcode']?></td>
					<td><?=$rows['nm_brg']?></td>
					<td><?=(int) $rows['qty']?></td>
					<td><?=(int) $rows['qty_approval']?></td>
					<td><?=$selisih?></td>
					<td><?php if($selisih == 0){echo 'Approved';}else if((int)$rows['qty_approval'] > 0 && $selisih != 0){echo 'Approved In Part';}else{echo 'Approval Process';}?></td>
				</tr>
			<?php
			$dqt = $dqt + $rows['qty'];
			$dqa = $dqa + $rows['qty_approval'];
			$dsl = $dsl + $selisih;
			} ?>
			</tbody>
			<tfoot>
			<tr>
				<th colspan="4">TOTAL</th>
				<th><?=$dqt?></th>
				<th><?=$dqa?></th>
				<th><?=$dsl?></th>
				<th></th>
			</tr>
			*/
			// export master detail barang
			$baca = $this->m_crud->read_data('kartu_stock as ks, lokasi lk, barang as br, group1 g1', "kd_trx, lk.kode lokasi, tgl, (select count(kartu_stock.kd_brg) from kartu_stock where kartu_stock.lokasi = 'Retur' and kartu_stock.kd_trx=ks.kd_trx) as tot_item, (select sum(kartu_stock.stock_in) from kartu_stock where kartu_stock.kd_trx=ks.kd_trx) as tot_qty, isnull((select sum(stock_out) from kartu_stock where lokasi = 'Retur' and keterangan = ('Retur Approval ' + ks.kd_trx) ),0) as tot_qty_approval, keterangan2, g1.nama nama_g1, ks.kd_brg, br.barcode, stock_in as qty, br.nm_brg, ks.hrg_beli, isnull((select sum(stock_out) from kartu_stock where kartu_stock.kd_brg = ks.kd_brg and keterangan = ('Retur Approval ' + ks.kd_trx)),0) as qty_approval", "ks.kd_brg = br.kd_brg and br.group1=g1.kode and ".$where, 'ks.kd_trx desc', "ks.kd_trx, lk.kode, ks.tgl, ks.keterangan2, g1.nama, ks.kd_brg, br.barcode, ks.stock_in, br.nm_brg, ks.hrg_beli", null, null, $having);
            $header = array(
                'merge' 	=> array('A1:R1','A2:R2','A3:R3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:R5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:R5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
					'A'=>'Kode Transaksi', 'B'=>'Tanggal', 'C'=>'Lokasi', 'D'=>'Kondisi', 'E'=>'Informasi', 'F'=>'Total Item', 'G'=>'Total Qty', 'H'=>'Qty Approval', 'I'=>'Qty Selisih', 'J'=>'Status', 
					'K'=>$this->menu_group['as_group1'], 'L'=>'Kode Barang', 'M'=>'Barcode', 'N'=>'Nama Barang', 'O'=>'Qty', 'P'=>'Qty Approval', 'Q'=>'Selisih', 'R'=>'Status'
				)
            );

            $rowspan = 1;
            $start = 6;
            $end = 0;
			
			$ti = 0; $tq = 0; $qa = 0; $sl = 0;
            foreach($baca as $row => $value){
				if ($rowspan <= 1) {
                    $start = $start + $end;
                    $end = $start + $value['tot_item'] -1;
                    array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'F'.$start.':F'.$end.'', 'G'.$start.':G'.$end.'', 'H'.$start.':H'.$end.'', 'I'.$start.':I'.$end.'', 'J'.$start.':J'.$end.'');
                    $rowspan = $value['tot_item'];
                    if ($value['tot_item'] == 1) {
                        $start = 1;
                    }
					
					$tot_selisih_ = (int)$value['tot_qty']-(int)$value['tot_qty_approval'];
					$ti = $ti + $value['tot_item'];
					$tq = $tq + $value['tot_qty'];
					$qa = $qa + $value['tot_qty_approval'];
					$sl = $sl + $tot_selisih_;
                }else {
                    $rowspan = $rowspan - 1;
                    $start = 1;
                }
				$keterangan = json_decode($value['keterangan2'], true);
                $selisih_ = (int)$value['qty']-(int)$value['qty_approval'];
				if($tot_selisih_ == 0){$tot_status = 'Approved';}else if((int)$value['tot_qty_approval'] > 0 && $tot_selisih_ != 0){$tot_status = 'Approved In Part';}else{$tot_status = 'Approval Process';};
                if($selisih_ == 0){$status = 'Approved';}else if((int)$value['qty_approval'] > 0 && $selisih_ != 0){$status = 'Approved In Part';}else{$status = 'Approval Process';};
                $body[$row] = array(
                    $value['kd_trx'], substr($value['tgl'], 0, 10), $value['lokasi'], $keterangan[0]['status'], $keterangan[0]['information'], $value['tot_item'], $value['tot_qty'], $value['tot_qty_approval'], $tot_selisih_, $tot_status,
					$value['nama_g1'], $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['qty'], $value['qty_approval'], $selisih_, $status
                );
            }
			$header['alignment']['A6:J'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['J6:R'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $body[$end-5] = array('TOTAL', '', '', '', '', $ti, $tq, $qa, $sl);
            array_push($header['merge'], 'A'.($end+1).':E'.($end+1).'');
            $header['font']['A'.($end+1).':J'.($end+1).''] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');
			/*
			// export masterna wungkul
			$baca = $detail;
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
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
					'A'=>'Kode Transaksi', 'B'=>'Tanggal', 'C'=>'Lokasi', 'D'=>'Total Item', 'E'=>'Total Qty', 'F'=>'Qty Approval', 'G'=>'Qty Selisih', 'H'=>'Status'
                )
            );

            $end = 0;

            foreach($baca as $row => $value){
                $selisih_ = (int)$value['tot_qty']-(int)$value['qty_approval'];
                if($selisih_ == 0){$status = 'Approved';}else if((int)$value['qty_approval'] > 0 && $selisih_ != 0){$status = 'Approved In Part';}else{$status = 'Approval Process';};
                $end++;
                $body[$row] = array(
                    $value['kd_trx'], substr($value['tgl'], 0, 10), $value['lokasi'], $value['tot_item'], $value['tot_qty'], $value['qty_approval'], $selisih_, $status
                );
            }

            $body[$end] = array('TOTAL', '', '', $ti, $tq, $qa, $sl);
            array_push($header['merge'], 'A'.($end+6).':C'.($end+6).'');
            $header['font']['A'.($end+6).':H'.($end+6).''] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');
			*/
            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;

            $where = "kd_trx = '".$id."' and keterangan = 'Retur Non Approval' and lokasi = 'retur'";
            $data['report'] = $this->m_crud->get_data('kartu_stock', "tgl, kd_trx, lokasi", $where);
            $data['report_detail'] = $this->m_crud->join_data('kartu_stock as ks', "ks.kd_brg, br.barcode, stock_in as qty, br.nm_brg, ks.hrg_beli, isnull((select sum(stock_out) from kartu_stock where kartu_stock.kd_brg = ks.kd_brg and keterangan = 'Retur Approval ".$id."'),0) as qty_approval", 'barang as br', 'ks.kd_brg = br.kd_brg', $where);

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_beli']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header =
                '<div class="row"><img style="float: right; margin-top: -10px" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['kd_trx'].'"></div>'.
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Arsip Retur Cabang</b>
                <div style="margin-bottom: 10px;">
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
                                <td><b>Tanggal</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl'], 0, 10).'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Kode Transaksi</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_trx'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['lokasi'].'</td>
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
                'left'=>10,'right'=>10,'top'=>47,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    /*Start modul retur cabang*/
    public function form_retur_cabang() {
        $this->access_denied(72);
        $data = $this->data;
        $function = 'form_retur_cabang';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Retur Cabang';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, Nama, serial',"kode <> 'HO'");

        $this->load->view('bo/index', $data);
    }

    public function add_tr_temp_m_retur() {
        $data = array(
            'm1' => $_POST['nota_retur'],
            'm2' => $_POST['tgl_retur'],
            'm3' => $_POST['lokasi'],
            'm4' => $this->user,
            'm5' => $_POST['set_focus']
        );

        $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "(m4 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'RR')");

        if ($cek_data >= 1) {
            $this->m_crud->update_data("tr_temp_m", $data, "(m4 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'RR')");
            $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['nota_retur']), "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'RR')");
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }
    }

    public function get_tr_temp_m_retur() {
        $get_data = $this->m_crud->get_data("tr_temp_m", "*", "(m4 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'RR')");

        echo json_encode($get_data);
    }

    public function update_tr_temp_m_retur($tmp_column, $tmp_data) {
        $column = base64_decode($tmp_column);
        $data = base64_decode($tmp_data);

        $this->m_crud->update_data("tr_temp_m", array($column => $data), "(m4 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'RR')");
    }

    public function get_tr_temp_d_retur() {
        $list_barang = '';
        $read_data = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'RR')", "CONVERT(INTEGER, d12) ASC");

        $no = 1;
        $col = 0;
        $sub_total = 0;
        $total_sub_total = 0;
        $length = count($read_data);
        foreach ($read_data as $row) {
            $sub_total = $row['d5'] * $row['d9'];
            $total_sub_total = $total_sub_total + $sub_total;
            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d10'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button></td>
                                <td>' . $row['d2'] . '</td>
                                <td>' . $row['d10'] . '</td>
                                <td>' . $row['d3'] . '</td>
                                <td>' . $row['d4'] . '</td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d5\', $(this).val())" type="number" id="d5' . $no . '" name="d5' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d5'], 2, '.', '') . '" readonly></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d6\', $(this).val())" type="number" id="d6' . $no . '" name="d6' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d6'], 2, '.', '') . '" readonly></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d7\', $(this).val())" type="number" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-diskon" value="' . number_format((float)$row['d7'], 2, '.', '') . '" readonly></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d10'] . '\', \'d9\', $(this).val())" onfocus="this.select()" onkeyup="hitung_barang(\'d9\', \'' . $no . '\', $(this).val(), '.$length.'); return to_barcode(event)" type="number" id="d9' . $no . '" name="d9' . $no . '" class="form-control width-diskon" value="' . ($row['d9'] + 0) . '"></td>
                                <td><input type="number" id="sub_total' . $no . '" name="sub_total' . $no . '" class="form-control width-uang" value="'.number_format((float)$sub_total, 2, '.', '').'" readonly></td>
                            </tr>';
            $col = $no;
            $no++;
        }
        $list_barang .= '<input type="hidden" id="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang, 'sub_total' => $total_sub_total));
    }

    public function insert_tr_temp_d_retur($nota_sistem, $get_barang, $barcode, $qty=1) {
        $data = array(
            'd1' => $nota_sistem,
            'd2' => $get_barang['kd_brg'],
            'd3' => $get_barang['nm_brg'],
            'd4' => $get_barang['Deskripsi'],
            'd5' => $get_barang['hrg_beli'],
            'd6' => $get_barang['hrg_jual_1'],
            'd7' => $get_barang['stock'],
            'd8' => $get_barang['kd_packing'],
            'd9' => $qty,
            'd10' => $barcode,
            'd11' => $this->user
        );

        $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d12)) id", "d11='".$this->user."' AND (SUBSTRING(d1,1,2) = 'RR')");
        $data['d12'] = ((int)$get_max_id['id']+1);

        $this->m_crud->create_data("tr_temp_d", $data);
    }

    public function update_tr_temp_d_retur($tmp_barcode, $tmp_column, $tmp_value) {
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        $this->m_crud->update_data("tr_temp_d", array($column => $value), "(SUBSTRING(d1,1,2) = 'RR') AND (d10 = '".$barcode."') AND (d11 = '".$this->user."')");
    }

    public function delete_tr_temp_d_retur($tmp_barcode) {
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

        $this->m_crud->delete_data("tr_temp_d", "(d11 = '".$this->user."') AND (d10 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'RR')");

        echo true;
    }

    public function delete_trans_retur() {
        $this->m_crud->delete_data("tr_temp_m", "(m4 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'RR')");
        $this->m_crud->delete_data("tr_temp_d", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'RR')");
    }

    public function update_stock_retur() {
        $tmp_lokasi = $_POST['lokasi_'];
        $explode_lokasi = explode('|', $tmp_lokasi);
        $lokasi_cabang = $explode_lokasi[0];
        $get_data_barang = $this->m_crud->read_data("tr_temp_d", "d2, d10", "d11='".$this->user."' AND (SUBSTRING(d1,1,2) = 'RR')");

        foreach ($get_data_barang as $row) {
            $stock = $this->m_crud->get_data("Kartu_stock", "isnull(SUM(stock_in-stock_out), 0) stock", "Kartu_stock.kd_brg='".$row['d2']."' AND lokasi='".$lokasi_cabang."'");
            $this->update_tr_temp_d_retur(base64_encode($row['d10']), base64_encode('d7'), base64_encode($stock['stock']));
        }
    }

    public function get_barang_retur($tmp_nota_sistem, $tmp_barcode, $tmp_lokasi_cabang, $tmp_cat_cari) {
        $cat_cari = base64_decode($tmp_cat_cari);
        $nota_sistem = base64_decode($tmp_nota_sistem);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi_cabang));
        $lokasi_cabang = $explode_lokasi[0];
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
            $col_tmp = 'd8';
        }

        $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d9, d12", "(SUBSTRING(d1,1,2) = 'RR') AND (".$col_tmp." = '".$barcode."') AND (d11 = '".$this->user."')");
		
		if ($cat_cari == 4) {
			$qty = $this->m_crud->get_data('barang', 'isnull((qty_packing),0) qty_packing', $col_barang." = '".$barcode."'")['qty_packing'];
		} else {
			$qty = 1;
		}
		
        if ($get_tmp_data != '') {
            $data = array(
                'd9' => (int)$get_tmp_data['d9'] + $qty
            );
			
            $this->m_crud->update_data("tr_temp_d", $data, "(d11 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'RR')");
            echo json_encode(array('status' => 1, 'barang'=>'tersedia', 'col'=>$get_tmp_data['d12']));
        } else {
            $stock = "isnull((SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE Kartu_stock.kd_brg=barang.kd_brg AND lokasi='".$lokasi_cabang."'), 0) stock";
            $get_barang = $this->m_crud->get_data("barang, barang_hrg", "barang.kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.qty_packing, barang.hrg_beli, barang.hrg_jual_1,".$stock, "(barang.kd_brg = barang_hrg.barang) AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
            if ($get_barang != null) {
                $this->insert_tr_temp_d_retur($nota_sistem, $get_barang, $barcode, $qty);
                echo json_encode(array('status' => 1));
            } else { 
                echo json_encode(array('status' => 3, 'notif' => "Barang tidak tersedia!"));
            }
        }
    }

    public function trans_retur_x() {
        $this->access_denied(72);
        $no_retur = $_POST['nota_retur'];
        $tgl_retur = $_POST['tgl_retur'] . " " . date("H:i:s");
        $tmp_lokasi = $_POST['lokasi'];
        $explode_lokasi = explode('|', $tmp_lokasi);
        $lokasi_asal = $explode_lokasi[0];
        $serial = $explode_lokasi[1];

        $no_retur = $this->m_website->generate_kode("RR", $serial, date('ymd', strtotime($tgl_retur)));

        $this->db->trans_begin();

        //$get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "(m4 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'RR')");;
        $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'RR')");;

        $master_retur = array(
            'kd_trx' => $no_retur,
            'tgl' => $tgl_retur
        );
        $det_log = array();
        foreach ($read_temp_d as $row) {
            $data_kartu_stok_out = array(
                'kd_trx' => $no_retur,
                'tgl' => $tgl_retur,
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => 0,
                'stock_out' => $row['d9'],
                'lokasi' => $lokasi_asal,
                'keterangan' => 'Retur Non Approval',
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);
            array_push($det_log, $data_kartu_stok_out);

            $data_kartu_stok_in = array(
                'kd_trx' => $no_retur,
                'tgl' => $tgl_retur,
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => $row['d9'],
                'stock_out' => 0,
                'lokasi' => 'Retur',
                'keterangan' => 'Retur Non Approval',
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_in);
            array_push($det_log, $data_kartu_stok_in);
        }

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$no_retur,'jenis'=>ucfirst('Add'),'transaksi'=>'Retur Cabang'), array('master'=>$master_retur,'detail'=>$det_log));

        $this->delete_trans_retur();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status'=>false));
        }else {
            $this->db->trans_commit();
            echo json_encode(array('status'=>true, 'kode'=>$no_retur));
        }
    }

    public function trans_retur($tmp_no_retur) {
        $this->access_denied(72);
        $no_retur = base64_decode($tmp_no_retur);
        $get_kode = $this->m_crud->get_data("Kartu_stock", "kd_trx", "(kd_trx = '".$no_retur."')");

        if ($get_kode != '') {
            $no_retur = $this->m_website->generate_kode(substr($get_kode['kd_trx'], 0, 2), substr($get_kode['kd_trx'], 14), substr($get_kode['kd_trx'], 3, 6));
        }

        $this->db->trans_begin();

        $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "(m4 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'RR')");;
        $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'RR')");;

        $explode_lokasi_asal = explode('|', $get_temp_m['m3']);
        $lokasi_asal = $explode_lokasi_asal[0];

        foreach ($read_temp_d as $row) {
            $data_kartu_stok_out = array(
                'kd_trx' => $no_retur,
                'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => 0,
                'stock_out' => $row['d9'],
                'lokasi' => $lokasi_asal,
                'keterangan' => 'Retur Non Approval',
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);

            $data_kartu_stok_in = array(
                'kd_trx' => $no_retur,
                'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => $row['d9'],
                'stock_out' => 0,
                'lokasi' => 'Retur',
                'keterangan' => 'Retur Non Approval',
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_in);
        }

        $this->delete_trans_retur();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        }else {
            $this->db->trans_commit();
            echo true;
        }
    }
    /*End modul retur cabang*/
	
}

