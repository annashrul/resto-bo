<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Penjualan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '2048M');
        ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

        $site_data = $this->m_website->site_data();
        $this->site = str_replace(' ', '', strtolower($site_data->title));
        $this->control = 'Penjualan';

        $this->user = $this->session->userdata($this->site . 'user');
        $this->username = $this->session->userdata($this->site . 'username');
        $this->menu_group = $this->m_crud->get_data('Setting', 'as_deskripsi, as_group1, as_group2', "Kode = '1111'");

        /*Session lokasi*/
        $lok = $this->session->userdata($this->site.'lokasi');
//        var_dump($lok);die();
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

    public function getRePrint(){
        $read = $this->db->query("SELECT TOP 1 * FROM Master_Trx where kd_kasir='".$this->user."' and left(convert(varchar, tgl, 120), 10) = '".date('Y-m-d')."' ORDER BY  kd_trx desc")->row_array();
        echo json_encode(array("res"=>$read));
    }

    function access_denied($str){
        if(substr($this->m_website->user_access_data($this->user)->access,$str,1) == 0){
            echo "<script>alert('Access Denied'); window.location='".base_url()."site';</script>";
        }
    }
	
	
	/*Start modul bayar piutang*/
    public function get_kartu_piutang() {
        if (isset($_POST['customer_'])) {
            $where = " cs.nama like '%".$_POST['customer_']."%'";
        } else {
            $where = null;
        }

        $id = 1;
        $list_piutang = '';
        $total_piutang = 0;

        $read_kartu_piutang = $this->m_crud->join_data("kartu_piutang kp", "cs.kd_cust, cs.Nama, SUM(kp.total_jual-kp.total_bayar) sisa_piutang", array(array('table' => 'Customer cs', 'type' => 'LEFT')), array('cs.kd_cust=kp.kd_cust'), $where, null, "cs.kd_cust, cs.Nama", 0, 0, "SUM(kp.total_jual-kp.total_bayar) > 0");

        foreach ($read_kartu_piutang as $row) {
            $list_detail_piutang = '';
            $no = 1;
            $read_detail_piutang = $this->m_crud->join_data("kartu_piutang kp","kp.tgl,kp.kd_trx,kp.kd_cust,kp.total_bayar,kp.total_jual, mt.bayar",array(array('table'=>'master_trx mt','type'=>'LEFT')),array('mt.kd_trx=kp.kd_trx'),"kp.kd_cust='".$row['kd_cust']."' AND (kp.total_jual-kp.total_bayar) > 0", "kp.tgl ASC, kp.kd_trx ASC");
//            $read_detail_piutang = $this->m_crud->read_data("kartu_piutang", "*", "kd_cust='".$row['kd_cust']."' AND (total_jual-total_bayar) > 0", "tgl ASC, kd_trx ASC");
            foreach ($read_detail_piutang as $row2) {
                $list_detail_piutang .= '
                    <tr>
                        <td>'.$no.'</td>
                        <td>'.substr($row2['tgl'], 0, 10).'</td>
                        <td>'.$row2['kd_trx'].'</td>
                        <td>'.number_format($row2['total_jual']).'</td>
                        <td>'.number_format($row2['bayar']).'</td>
                        <td>'.number_format($row2['total_jual']-$row2['bayar']).'</td>
                        <td><a href="'.base_url().'penjualan/bayar_piutang/bayar_nota_jual/'.base64_encode($row2['kd_trx']).'" class="btn btn-primary"><i class="md md-payment"></i> Bayar Piutang</a></td>
                    </tr>
                ';
                $no++;
            }

            $list_piutang .= '
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#list-piutang" href="#collapse'.$id.'" class="collapsed">
                                '.$row['Nama'].' | Jumlah Piutang Rp '.number_format($row['sisa_piutang']).'
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
                                    <th>Nota Penjualan</th>
                                    <th>Piutang</th>
                                    <th>Dibayar</th>
                                    <th>Sisa Piutang</th>
                                    <th>Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                '.$list_detail_piutang.'
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
	        ';

            $total_piutang = $total_piutang + $row['sisa_piutang'];
            $id++;
        }

        echo json_encode(array('list_piutang'=>$list_piutang, 'total_piutang'=>'TOTAL PIUTANG Rp '.number_format($total_piutang)));
    }

	public function search_nota_jual(){
		$keyword = $this->uri->segment(3); // tangkap variabel keyword dari URL
		//$data = $this->m_crud->read_data('Master_Trx mt', "mt.kd_trx", "mt.HR = 'S' and mt.kd_trx like '%".$keyword."%' and Jenis_trx='Kredit'", null, null, 20); // cari di database
		$data = $this->m_crud->read_data('Master_Trx mt, Det_Trx dt', "mt.kd_trx", "mt.HR = 'S' and mt.kd_trx=dt.kd_trx and mt.kd_trx like '%".$keyword."%' and Jenis_trx='Kredit' and (isnull((select sum(jumlah-bulat) from bayar_piutang where fak_jual = mt.kd_trx),0) < ((isnull((select (SUM(dtw.qty * dtw.hrg_jual)-SUM(dtw.dis_persen)) from Det_Trx dtw where dtw.kd_trx=mt.kd_trx),0) - mt.dis_rp - mt.kas_lain) - (mt.bayar + mt.jml_kartu + mt.voucher)))", null, 'mt.kd_trx, mt.dis_rp, mt.voucher, mt.bayar, mt.jml_kartu, mt.kas_lain', 20); // cari di database
		foreach($data as $row){ // format keluaran di dalam array
			$arr['query'] = $keyword;
			$arr['suggestions'][] = array(
				'value'	=> $row['kd_trx'],
			);
		}
		echo json_encode($arr);
	}
	
	public function bayar_piutang($action = null, $param1 = null){
		$this->access_denied(251);
		$data = $this->data;
		$function = 'bayar_piutang';
		$view = $this->control . '/';
		
		//if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }  
		
		$data['title'] = 'Bayar Piutang';
		$data['page'] = $function;
		$data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, nama_toko Nama, serial', $this->where_lokasi);
        //$data['data_supplier'] = $this->m_crud->read_data('Supplier','Kode, Nama');
        $data['data_bank'] = $this->m_crud->read_data('bank','Nama');
		$data['data_penjualan'] = array();
        
		if((isset($_POST['cari'])||isset($_POST['simpan'])) || ($action=='bayar_nota_jual'&&$param1!=null)){
			$nota_jual = (isset($_POST['cari'])||isset($_POST['simpan']))?$_POST['nota_jual']:(($action=='bayar_nota_jual'&&$param1!=null)?base64_decode($param1):null);
			$data['data_penjualan'] = $this->m_crud->read_data('Master_Trx mt, Det_Trx dt, Customer cs', "mt.kd_trx, cs.Nama, mt.Tempo, SUM(dt.qty * dt.hrg_jual) omset, SUM(dt.dis_persen) diskon_item, mt.dis_rp, mt.voucher, mt.bayar, mt.jml_kartu, mt.kas_lain, isnull((select sum(jumlah-bulat) from bayar_piutang where fak_jual = mt.kd_trx),0) jumlah_bayar", "mt.HR = 'S' AND dt.qty > 0 AND mt.kd_trx=dt.kd_trx AND mt.kd_cust=cs.kd_cust and mt.kd_trx = '".$nota_jual."' and Jenis_trx='Kredit' and (isnull((select sum(jumlah-bulat) from bayar_piutang where fak_jual = mt.kd_trx),0) < ((isnull((select (SUM(dtw.qty * dtw.hrg_jual)-SUM(dtw.dis_persen)) from Det_Trx dtw where dtw.kd_trx=mt.kd_trx),0) - mt.dis_rp - mt.kas_lain) - (isnull(mt.bayar, 0) + isnull(mt.jml_kartu, 0) + isnull(mt.voucher, 0))))", null, "mt.kd_trx, cs.Nama, mt.Tempo, mt.dis_rp, mt.voucher, mt.bayar, mt.jml_kartu, mt.kas_lain");
		}
		if(isset($_POST['simpan'])){ 
			$this->db->trans_begin();
			$this->m_crud->create_data('bayar_piutang', array(
				'no_nota' => $_POST['nota_sistem'],
				'fak_jual' => $_POST['nota_jual'],
				'tgl_byr' => $_POST['tanggal'].' '.date('H:i:s'),
				'cara_byr' => $_POST['cara_byr'],
				'jumlah' => $_POST['jumlah_bayar'],
				'kasir' => $this->user,
				'tgl_jatuh_tempo' => $_POST['Tempo'],
				'nm_bank' => $_POST['cara_byr']=='Cek/Giro'?$_POST['bank']:'-',
				'bulat' => $_POST['pembulatan'],
				'nogiro' => $_POST['cara_byr']=='Cek/Giro'?$_POST['nogiro']:'-',
				'tgl_cair_giro' => $_POST['cara_byr']=='Cek/Giro'?$_POST['tanggal_cair']:null,
				'ket' => $_POST['ket']
			));
			$sisa_piutang = (floatval(str_replace(',','',$_POST['jumlah_piutang'])) + floatval(str_replace(',','',$_POST['pembulatan']))) - floatval(str_replace(',','',$_POST['jumlah_bayar']));
			if($sisa_piutang==0){
				$this->m_crud->update_data('master_trx', array('status'=>'Lunas'), "kd_trx = '".$_POST['nota_jual']."'");
			}
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit(); redirect($data['content']);
			}
		} 
		
        $this->load->view('bo/index', $data);
	}
	/*End modul bayar piutang*/

	/*Start modul POS*/
    public function pos_web(){
        $this->access_denied(191);
        $data = $this->data;
        $function = 'pos_web';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Point Of Sales';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, Nama, serial');
        $data['data_supplier'] = $this->m_crud->read_data('Customer','kd_cust, Nama');

        $this->load->view('bo/index', $data);
    }
	/*End modul POS*/

    /*Start modul penjualan*/
    public function penjualan_barang(){
        $this->access_denied(191);
        $data = $this->data;
        $function = 'penjualan_barang';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Penjualan Barang';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, nama_toko Nama, serial', $this->where_lokasi);
        $data['data_supplier'] = $this->m_crud->read_data('Customer','kd_cust, Nama');

        $this->load->view('bo/index', $data);
    }

    public function add_tr_temp_m_jual() {
        $param = $_POST['param'];
        $data = array(
            'm1' => 'Jual',
            'm2' => $_POST['tgl_jual'],
            'm5' => $this->user
        );

       /* 'm1' => 'Jual',
            'm2' => $_POST['tgl_jual'],
            'm3' => $_POST['lokasi'],
            'm4' => $_POST['customer'],
            'm5' => $this->user,
            'm10' => $_POST['ket']*/

        if ($param == 'edit') {
            $get_tmp_m = $this->m_crud->get_data("tr_temp_m", "m6", "m5='".$this->user."' AND m7='edit'");
            $data['m6'] = $get_tmp_m['m6'];
            $data['m7'] = 'edit';
        } else {
            $data['m7'] = 'add';
        }

        if ($param == 'edit') {
            $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "m7='edit' AND (m5 = '".$this->user."') AND m1 = 'Jual')");
        } else {
            $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "m7='add' AND (m5 = '".$this->user."') AND m1 = 'Jual'");
        }

        if ($cek_data == 1) {
            if ($param == 'edit') {
                $this->m_crud->update_data("tr_temp_m", $data, "m7='edit' AND (m5 = '".$this->user."') AND m1 = 'Jual'");
            } else {
                $this->m_crud->update_data("tr_temp_m", $data, "m7='add' AND (m5 = '".$this->user."') AND m1 = 'Jual'");
            }
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }
    }

    public function get_tr_temp_m_jual($tmp_param=null) {
        $param = base64_decode($tmp_param);
        if ($param == 'edit') {
            $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m7='edit' AND (m5 = '".$this->user."') AND m1 = 'Jual'");
        } else {
            $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m7='add' AND (m5 = '".$this->user."') AND m1 = 'Jual'");
        }

        echo json_encode($get_data);
    }

    public function update_tr_temp_m_jual($tmp_column, $tmp_data, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $column = base64_decode($tmp_column);
        $data = base64_decode($tmp_data);

        if ($param == 'edit') {
            $this->m_crud->update_data("tr_temp_m", array($column => $data), "m7='edit' AND (m5 = '".$this->user."') AND m1 = 'Jual'");
        } else {
            $this->m_crud->update_data("tr_temp_m", array($column => $data), "(m7='add' AND m5 = '".$this->user."') AND m1 = 'Jual'");
        }
    }

    public function get_tr_temp_d_jual($tmp_param = null, $cust=null, $kode=null) {
        $param = base64_decode($tmp_param);
        $customer = base64_decode($cust);
        $kd_brg = base64_decode($kode);
        $list_barang = '';

        $cekCustomer = $this->m_crud->read_data("brg_customer","*","kd_cust='".$customer."'");



        if ($param == 'edit') {
            $get_data = $get_data = $this->m_crud->get_data("tr_temp_m", "m8, m9", "m7='edit' AND (m5 = '".$this->user."') AND (m1='Jual')");
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d12='edit' AND (d10 = '".$this->user."') AND d1 = 'Jual'", "d20 DESC");
        } else {
            $get_data = $get_data = $this->m_crud->get_data("tr_temp_m", "m8, m9", "m7='add' AND (m5 = '".$this->user."') AND (m1='Jual')");
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d12='add' AND (d10 = '".$this->user."') AND d1 = 'Jual'", "d20 DESC");
        }

        $no = 1;
        $col = 0;
        $qty_jual = 0;
        $sub_total = 0;
        $length = count($read_data);
        /*
         <td data-priority="0">' . $row['d4'] . '</td>
        <td data-priority="0"><input type="number" id="konversi' . $no . '" name="konversi' . $no . '" class="form-control width-uang" value="" readonly></td>
        */
        foreach ($read_data as $row) {
            $qty_jual = $qty_jual + $row['d8'];
            $jumlah_jual = $row['d5'] * $row['d8'];
            $diskon = $this->m_website->double_diskon($jumlah_jual, array($row['d6'], $row['d7']));
            $hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $row['d14']);
            $sub_total = $sub_total + $hitung_sub_total;
            $kd_bill = $row['d11'];
            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d9'] . '\')" class="btn btn-danger btn-sm"><i class="md md-close"></i></button></td>
                                <td data-priority="1">' . $row['d2'] . '</td>
                                <td data-priority="1">' . $row['d9'] . '</td>
                                <td data-priority="1">' . $row['d4'] . '</td>
                                <td data-priority="1">' . $row['d3'] . '</td>
                                <td>
                                <div class="input-group" style="width: 100%">
                                    <span class="input-group-addon"><input title="Open Price" value="1" onclick="open_price(\'' . $row['d9'] . '\', \''.$no.'\')" id="open_price'.$no.'" name="open_price'.$no.'" type="checkbox"></span>
                                    <input onblur="update_tmp_detail(\'' . $row['d9'] . '\', \'d5\', $(this).val())" onkeydown="return isNumber(event)" onkeyup="hitung_barang(\'d5\', \'' . $no . '\', $(this).val(), '.$length.'); isMoney(\'d5' . $no . '\', \'+\'); return to_qty(event, '.$no.')" onfocus="this.select()" type="text" id="d5' . $no . '" name="d5' . $row['d5'] . '" class="form-control width-uang2 input-sm" value="' . number_format((float)$row['d5'], 2, '.', ',') . '" readonly >
                                </div>
                                </td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d9'] . '\', \'d6\', $(this).val())" onkeyup="hitung_barang(\'d6\', \'' . $no . '\', $(this).val(), '.$length.'); return to_col(event, '.$no.', \'d7\');" onfocus="this.select()" type="number" id="d6' . $no . '" name="d6' . $row['d6'] . '" class="form-control width-diskon input-sm" value="' . number_format((float)$row['d6'], 2, '.', '') . '"></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d9'] . '\', \'d7\', $(this).val())" onkeyup="hitung_barang(\'d7\', \'' . $no . '\', $(this).val(), '.$length.'); return to_col(event, '.$no.', \'d8\');" onfocus="this.select()" type="number" id="d7' . $no . '" name="d7' . $row['d7'] . '" class="form-control width-diskon input-sm" value="' . number_format((float)$row['d7'], 2, '.', '') . '"></td>
                                <input onblur="update_tmp_detail(\'' . $row['d9'] . '\', \'d14\', $(this).val())" onkeyup="hitung_barang(\'d14\', \'' . $no . '\', $(this).val(), '.$length.'); return to_barcode(event)" onfocus="this.select()" type="number" id="d14' . $no . '" name="d14' . $row['d14'] . '" class="form-control width-diskon input-sm"  value="' . number_format((float)$row['d14'], 2, '.', '') . '">
                                <td><input onblur="update_tmp_detail(\'' . $row['d9'] . '\', \'d8\', $(this).val())" onkeyup="hitung_barang(\'d8\', \'' . $no . '\', $(this).val(), '.$length.'); return to_col(event, '.$no.', \'d6\');" onfocus="this.select()" type="number" id="d8' . $no . '" name="d8' . $no . '" class="form-control width-diskon input-sm" value="' . ($row['d8'] + 0) . '"></td>

                                <td><input type="text" id="nilai_jual' . $no . '" name="nilai_jual' . $no . '" class="form-control width-uang input-sm" value="'.number_format((float)$hitung_sub_total, 2, '.', ',').'" readonly></td>
                                <input type="hidden" id="d15'.$no.'" value="'.$row['d15'].'">
                                <input type="hidden" id="d9'.$no.'" value="'.$row['d9'].'">
                            </tr>';
            $col = $no;
            $no++;
        }
        $list_barang .= '<tr>
            <th colspan="9" class="text-right">TOTAL<p id="par_bill">'.$kd_bill.'</p></th>
            <th class="text-center"><b id="total_qty_jual">'.$qty_jual.'</b></th>
            <th class="text-right"><b id="total_nilai_jual">'.number_format((float)$sub_total, 2, '.', ',').'</b></th>
        </tr>';
        $list_barang .= '<input type="hidden" id="col" value="'.$col.'">';

        $total = $this->m_website->hitung_total(($sub_total-$get_data['m8']), 0, $get_data['m9']);
        $discount_harga = $this->m_website->diskon($sub_total, $get_data['m8']);
        $jumlah = array(
            'sub_total' => $sub_total,
            'discount_persen' => 0,
            'discount_harga' => (float)$get_data['m8'],
            'pajak' => (float)$get_data['m9'],
            'total' => $total
        );

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang, 'jumlah' => $jumlah,'cust'=>$customer,'kd_brg'=>$kd_brg));
    }

    public function insert_tr_temp_d_jual($get_barang, $barcode, $param=null, $qty=1,$cust) {
        $cekHrgCustomer = $this->m_crud->get_data("brg_customer","*","kd_cust='".$cust."' and kd_brg = '".$get_barang['kd_brg']."'");

        $data = array(
            'd1' => 'Jual',
            'd2' => $get_barang['kd_brg'],
            'd3' => $get_barang['nm_brg'],
            'd4' => $get_barang['Deskripsi'],
//            'd4' => $cekHrgCustomer['kd_cust']?$cekHrgCustomer['kd_cust']:'acuy',
            'd5' => $cekHrgCustomer['hrg_jual']?$cekHrgCustomer['hrg_jual']:$get_barang['hrg_jual'],
            'd6' => $get_barang['disc1'],
            'd7' => 0,
            'd8' => $qty,
            'd9' => $barcode,
            'd10' => $this->user,
            'd14' => $get_barang['ppn'],
            'd15' => 0,
            'd16' => $get_barang['hrg_beli'],
            'd17' => $get_barang['kategori'],
            'd18' => $get_barang['satuan'],
            'd19' => $get_barang['kd_packing'],
            'd20' => date('Y-m-d H:i:s')
        );

        if ($param == 'edit') {
            $get_tmp_d = $this->m_crud->get_data("tr_temp_d", "d11", "d10='".$this->user."' AND d12='edit'");
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d13)) id", "d10='".$this->user."' AND d12='edit'");
            $data['d11'] = $get_tmp_d['d11'];
            $data['d12'] = 'edit';
            $data['d13'] = ((int)$get_max_id['id']+1);
        } else {
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d13)) id", "d10='".$this->user."' AND d12='add'");
            $data['d12'] = 'add';
            $data['d13'] = ((int)$get_max_id['id']+1);
        }

        $this->m_crud->create_data("tr_temp_d", $data);
    }

    public function update_tr_temp_d_jual($tmp_barcode, $tmp_column, $tmp_value, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        if ($param == 'edit') {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "d12='edit' AND d1 = 'Jual' AND (d9 = '".$barcode."') AND (d10 = '".$this->user."')");
//            $this->m_crud->update_data("tr_temp_m", array($column => $value), "(m9 = '".$barcode."') AND (m15 = '".$this->user."')");
        } else {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "d12='add' AND d1 = 'Jual' AND (d9 = '".$barcode."') AND (d10 = '".$this->user."')");
//            $this->m_crud->update_data("tr_temp_m", array($column => $value), "(m9 = '".$barcode."') AND (m15 = '".$this->user."')");
        }
    }

    public function delete_tr_temp_d_jual($tmp_barcode, $tmp_param=null) {
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

            $this->m_crud->delete_data("tr_temp_d", "d12='edit' AND (d10 = '".$this->user."') AND (d9 = '".$barcode."') AND d1 = 'Jual'");

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

            $this->m_crud->delete_data("tr_temp_d", "d12='add' AND (d10 = '".$this->user."') AND (d9 = '".$barcode."') AND d1 = 'Jual'");

        }

        echo true;
    }

    public function get_barang_jual($tmp_barcode, $tmp_lokasi_beli, $tmp_customer, $tmp_cat_cari, $tmp_param=null) {
        $param = base64_decode($tmp_param);
        $cat_cari = base64_decode($tmp_cat_cari);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi_beli));
        $lokasi_jual = $explode_lokasi[0];
        $customer = base64_decode($tmp_customer);

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
            $col_tmp = 'd19';
        }
		
		if ($cat_cari == 4) {
			$qty = $this->m_crud->get_data('barang', 'isnull((qty_packing),0) qty_packing', $col_barang." = '".$barcode."'")['qty_packing'];
		} else {
			$qty = 1;
		}

        if ($param == 'edit') {
            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d8, d13", "d1 = 'Jual' AND (".$col_tmp." = '".$barcode."') AND (d10 = '".$this->user."')");
        } else {

            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d8, d13", "d1 = 'Jual' AND (".$col_tmp." = '".$barcode."') AND (d10 = '".$this->user."')");
        }

        if ($get_tmp_data != '') {
            $data = array(
                'd8' => (int)$get_tmp_data['d8'] + $qty
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d10 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND d1 = 'Jual'");
            echo json_encode(array('status' => 1, 'barang'=>'tersedia', 'col'=>$get_tmp_data['d13'],'hrgcust'=>$cekHrgCustomer));
        }else {
            $get_stock = "isnull((SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE kd_brg=barang.kd_brg AND lokasi='".$lokasi_jual."'), 0) stock";
            $get_barang = $this->m_crud->get_data("barang, barang_hrg", "barang.kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.kategori, barang.hrg_beli, barang.nm_brg, barang.satuan, barang_hrg.hrg_jual_1 hrg_jual, barang_hrg.disc1, barang_hrg.ppn, barang.Deskripsi", "barang.kd_brg=barang_hrg.barang AND barang_hrg.lokasi='".$lokasi_jual."' AND (rtrim(ltrim(".$col_barang.")) = '".$barcode."')");

            if ($get_barang != null) {
                if ($param == 'edit') {
                    $this->insert_tr_temp_d_jual($get_barang, $get_barang['barcode'], 'edit', $qty,$customer);
                } else {
                    $this->insert_tr_temp_d_jual($get_barang, $get_barang['barcode'], null, $qty,$customer);
                }
                echo json_encode(array('status' => 1));
            }else {
                $get_barang = $this->m_crud->get_data("barang", "barang.kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.kategori, barang.hrg_beli, barang.nm_brg, barang.satuan, barang.hrg_jual_1 hrg_jual, barang.diskon disc1, barang.PPN ppn, barang.Deskripsi", "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
                if ($get_barang != '') {
                    if ($param == 'edit') {
                        $this->insert_tr_temp_d_jual($get_barang, $get_barang['barcode'], 'edit', $qty,$customer);
                    } else {
                        $this->insert_tr_temp_d_jual($get_barang, $get_barang['barcode'], null, $qty,$customer);
                    }
                    echo json_encode(array('status' => 1));
                }else {
                    echo json_encode(array('status' => 3, 'notif' => "Barang tidak tersedia!"));
                }
            }
        }
    }

    public function delete_trans_jual($tmp_param=null) {
        $param = base64_decode($tmp_param);

        if ($param == 'edit') {
            $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "m7='edit' AND (m5 = '".$this->user."') AND m1 = 'Jual'");
            $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "d12='edit' AND (d10 = '".$this->user."') AND d1 = 'Jual'");
        } else {
            $delete_data_master = $this->m_crud->delete_data("tr_temp_m", "m7='add' AND (m5 = '".$this->user."') AND m1 = 'Jual'");
            $delete_data_detail = $this->m_crud->delete_data("tr_temp_d", "d12='add' AND (d10 = '".$this->user."') AND d1 = 'Jual'");
        }
    }

    public function bill_trx($action=null){
        $explode_lokasi = explode('|', $_POST['lokasi_']);
        $serial = $explode_lokasi[1];
        $nota_sistem = $this->m_website->generate_kode("SB", $serial, substr(str_replace('-','',$_POST['tgl_jual_']), 2, 6));
        $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d12='add' AND (d10 = '".$this->user."') AND d1 = 'Jual'");
        if($action=='get_data'){
            $list="";
            foreach($read_temp_d as $row){
                $list.='
                <input type="text" name="kd_bill_" id="kd_bill_" class="form-control" value="'.$nota_sistem.'">   
                <input type="text" name="d2" class="form-control" value="'.$row["d2"].'">   
                <input type="text" name="d9" class="form-control" value="'.$row["d9"].'">   
                <input type="text" name="d4" class="form-control" value="'.$row["d4"].'">   
                <input type="text" name="d3" class="form-control" value="'.$row["d3"].'">   
                <input type="text" name="d5" class="form-control" value="'.$row["d5"].'">   
                <input type="text" name="d6" class="form-control" value="'.$row["d6"].'">   
                <input type="text" name="d7" class="form-control" value="'.$row["d7"].'">   
                <input type="text" name="d8" class="form-control" value="'.$row["d8"].'">   
                <input type="text" name="d10" class="form-control" value="'.$row["d10"].'">   
            ';
            }
            echo json_encode(array('list'=>$list));
        }
        elseif ($action=='simpan'){
            $this->db->trans_begin();
            foreach($read_temp_d as $val){
                $data=array(
                    "m1"=>$_POST['nama'],
                    "m14"=>$_POST['kd_bill_'],
                    "m2"=>$val['d2'],
                    "m9"=>$val['d9'],
                    "m4"=>$val['d4'],
                    "m3"=>$val['d3'],
                    "m5"=>$val['d5'],
                    "m6"=>$val['d6']!=null?$val['d6']:0,
                    "m7"=>$val['d7']!=null?$val['d7']:0,
                    "m8"=>$val['d8'],
                    "m15"=>$val['d10'],
                    "m13"=>$val['d16'],
                    "m12"=>"SB"
                );
                $this->m_crud->create_data("tr_temp_m",$data);
                $this->m_crud->delete_data("tr_temp_d", "d12='add' AND (d10 = '".$val['d10']."') AND (d9 = '".$val['d9']."') AND d1 = 'Jual'");
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('status' => 'failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status' => 'success'));
            }
        }
        elseif ($action=='get_data_bill'){
            $read_temp_m = $this->m_crud->read_data("tr_temp_m", "*", "m15='".$this->user."' and m14='".$_POST['kd_bill']."'");
            $this->m_crud->delete_data("tr_temp_d", "d12='add' AND (d10 = '".$this->user."') AND d1 = 'Jual'");

            foreach($read_temp_m as $val){
                $data=array(
                    "d1"=>'Jual',
                    "d2"=>$val['m2'],
                    "d9"=>$val['m9'],
                    "d4"=>$val['m4'],
                    "d3"=>$val['m3'],
                    "d5"=>$val['m5'],
                    "d6"=>$val['m6']!=null?$val['m6']:0,
                    "d7"=>$val['m7']!=null?$val['m7']:0,
                    "d8"=>$val['m8'],
                    "d10"=>$val['m15'],
                    "d12"=>'add',
                    "d13"=>1,
                    "d14"=>0,
                    "d11"=>$val['m14'],
                    "d15"=>0,
                    "d16"=>$val['m13'],
                    "d17"=>"Non Paket"
                );
//                $this->m_crud->update_data("tr_temp_m", array("m12"=>"SB"), "m14='".$_POST['kd_bill']."'");
//                $this->m_crud->update_data("tr_temp_m","m13='Retur'","m14='".$_POST['kd_bill']."'");
//                $this->m_crud->delete_data("tr_temp_m", "m15='".$this->user."' and m14='".$_POST['kd_bill']."'");
                $this->m_crud->create_data("tr_temp_d",$data);
//                $this->m_crud->update_data("tr_temp_d", array("m12"=>"SB"), "m14='".$_POST['kd_bill']."'");
            }
            echo json_encode($read_temp_m);
        }


    }

    public function trans_jual() {
        $param = $_POST['param_'];
        $jenis_trx = $_POST['jenis_trx_'];
        $data = $_POST['data_'];
        $explode_lokasi = explode('|', $data['lokasi_']);
        $lokasi = $explode_lokasi[0];
        $serial = $explode_lokasi[1];
        $bayar = 0;
        $change = 0;
        $kartu = '-';
        $jml_kartu = 0;
        $tax = 0;
        $jns_kartu = '-';
        $lama_tempo = '-';
        $status = 'Lunas';
        $tempo = $data['tgl_jual_'].' '.date("H:i:s");

        if ($param == 'add') {
            $nota_sistem = $this->m_website->generate_kode("JL", $serial, substr(str_replace('-','',$data['tgl_jual_']), 2, 6));
        }

        $this->db->trans_begin();

        if ($param == 'edit') {
            $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "m7='add' AND (m5 = '".$this->user."') AND m1 = 'Jual'");
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d12='add' AND (d10 = '".$this->user."') AND d1 = 'Jual'");

            $this->m_crud->delete_data("master_beli", "No_Retur='".$get_temp_m['m6']."'");
            $this->m_crud->delete_data("det_beli", "No_Retur='".$get_temp_m['m6']."'");
            $this->m_crud->delete_data("Kartu_stock", "kd_trx='".$get_temp_m['m6']."'");
        } else {
            $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "m7='add' AND (m5 = '".$this->user."') AND m1 = 'Jual'");
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d12='add' AND (d10 = '".$this->user."') AND d1 = 'Jual'");
        }

        $sub_total = 0;

        $det_log = array();
        foreach ($read_temp_d as $row) {
            $jumlah_jual = $row['d5'] * $row['d8'];
            $hitung_subtotal = $this->m_website->double_diskon($jumlah_jual, array($row['d6'], $row['d7']));
            $hitung_diskon = $jumlah_jual-$hitung_subtotal;
            $sub_total = $sub_total + $hitung_subtotal;

            $det_trx = array(
                'kd_trx' => $nota_sistem,
                'kd_brg' => $row['d2'],
                'qty' => $row['d8'],
                'hrg_jual' => $row['d5'],
                'hrg_beli' => $row['d16'],
                'dis_persen' => $hitung_diskon,
                'subtotal' => $hitung_subtotal,
                'kategori' => $row['d17'],
                'ket_diskon' => '0',
                'open_price' => $row['d15']
            );
            $this->m_crud->create_data('Det_Trx', $det_trx);
            array_push($det_log, $det_trx);

            $check_barang = $this->m_crud->get_data("barang", "kategori", "kd_brg='".$row['d2']."'");

            if ($check_barang['kategori'] == 'Paket') {
                $read_assembly = $this->m_crud->join_data("barang br", "br.hrg_beli, asm.kd_brg_ass, asm.kd_brg, asm.kd_brg, asm.qty", "detail_assembly asm", "asm.kd_brg_ass=br.kd_brg", "br.kd_brg='".$row['d2']."'");

                if (count($read_assembly) > 0) {
                    foreach ($read_assembly as $row_ass) {
                        $kartu_stok = array(
                            'kd_trx' => $nota_sistem,
                            'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                            'kd_brg' => $row_ass['kd_brg'],
                            'saldo_awal' => 0,
                            'stock_in' => 0,
                            'stock_out' => (int)$row['d8'] * (int)$row_ass['qty'],
                            'lokasi' => $lokasi,
                            'keterangan' => 'Penjualan',
                            'hrg_beli' => $row_ass['hrg_beli'],
                        );
                        $this->m_crud->create_data("Kartu_stock", $kartu_stok);

                        $kartu_stok = array(
                            'kd_trx' => $nota_sistem,
                            'kd_brg_ass' => $row_ass['kd_brg_ass'],
                            'kd_brg' => $row_ass['kd_brg'],
                            'qty' => (int)$row['d8'] * (int)$row_ass['qty'],
                            'hrg_beli' => $row_ass['hrg_beli'],
                        );
                        $this->m_crud->create_data("Trx_Det_Assembly", $kartu_stok);
                    }
                }

            }
            else {
                $kartu_stok = array(
                    'kd_trx' => $nota_sistem,
                    'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                    'kd_brg' => $row['d2'],
                    'saldo_awal' => 0,
                    'stock_in' => 0,
                    'stock_out' => $row['d8'],
                    'lokasi' => $lokasi,
                    'keterangan' => 'Penjualan',
                    'hrg_beli' => $row['d16'],
                );
                $this->m_crud->create_data("Kartu_stock", $kartu_stok);
            }
        }

        $grand_total = $hitung_sub_total = $this->m_website->grand_total_ppn($sub_total-(float)$get_temp_m['m8'], 0, (float)$get_temp_m['m9']);
        if ($jenis_trx == 'Tunai') {
            $bayar = (float)$data['jumlah_bayar_'];
            $change = $bayar-$grand_total;
        } else if ($jenis_trx == 'Kredit') {
            $bayar = $data['dp_'];
            $kartu = "-";
            $status = 'Belum Lunas';
            $datetime1 = new DateTime($get_temp_m['m2']);
            $datetime2 = new DateTime($data['jatuh_tempo_']);
            $difference = $datetime1->diff($datetime2);
            $lama_tempo = $difference->days;
            $tempo = $data['jatuh_tempo_'];
        }
        elseif($jenis_trx == 'EDC'){
            $status = 'Lunas';
            $kartu = $data['bank_'];
            $bayar = (float)$data['jumlah_bayar_'];
            $jml_kartu  = (float)$data['jumlah_bayar_edc_'];
            $jns_kartu = 'Debit';
            $jenis_trx = 'Gabungan';
        }
        else {
            $kartu = $data['bank_'];
            $jml_kartu = $grand_total;
        }

        if ($get_temp_m['m9']!=null) {
            $tax = $this->m_website->diskon($sub_total, (float)$get_temp_m['m9']);
        }

        $master_trx = array(
            'kd_trx' => $nota_sistem,
            'tgl' => $data['tgl_jual_'],
            'kd_kasir' => $this->user,
            'kd_cust' => $data['customer_'],
            'dis_rp' => ($data['disc_hrg_']==null)?0:$data['disc_hrg_'],
            'dis_persen' => 0,
            'ST' => $sub_total,
            'tax' => $tax,
            'rounding' => 0,
            'GT' => $grand_total,
            'bayar' => $bayar,
            'change' => $change,
            'kartu' => $kartu,
            'jml_kartu' => $jml_kartu,
            'jns_kartu' => $jns_kartu,
            'sisa' => $change,
            'tempo' => $tempo,
            'jam' => date("H:i:s"),
            'status' => $status,
            'HR' => 'S',
            'kassa' => 'L',
            'RegMember' => 0,
            'charge' => 0,
            'no_kartu' => $jns_kartu=='EDC'?190709:0,
            'pemilik_kartu' => $jns_kartu,
            'kas_lain' => 0,
            'ket_kas_lain' => $data['ket_'],
            'lokasi' => $lokasi,
            'kd_sales' => 'UMUM',
            'lama_tempo' => $lama_tempo,
            'jenis_trx' => $jenis_trx
        );

        $this->m_crud->create_data('Master_Trx', $master_trx);

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$nota_sistem,'jenis'=>ucfirst($param),'transaksi'=>'Penjualan BO'), array('master'=>$master_trx,'detail'=>$det_log));

        $this->delete_trans_jual();
        foreach($read_temp_d as $key){
            if($key['d11']!=null){
                $this->m_crud->delete_data("tr_temp_m","m14='".$key['d11']."'");
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array(
                'status' => 'failed'
            ));
        } else {
            $this->db->trans_commit();
            echo json_encode(array(
                'status' => 'success',
                'transaksi' => $jenis_trx,
                'kd_trx' => $nota_sistem,
                'change' => $change
            ));
        }
    }
    /*End modul penjualan*/

    /*Start modul report*/
    public function bayar_piutang_report($action = null, $id = null){
        $this->access_denied(163);
        $data = $this->data;
        $function = 'bayar_piutang_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Bayar Piutang';
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
            if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(bh.no_nota like '%".$search."%' or bh.fak_jual like '%".$search."%' or sp.Nama like '%".$search."%')"; }

            $page = ($id==null?1:$id);
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data("bayar_piutang bh, Master_Trx mb, customer sp", 'bh.no_nota', "bh.fak_jual=mb.kd_trx and mb.kd_cust=sp.kd_cust".($where==null?'':' AND '.$where));
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

            //$data['report'] = $this->m_crud->select_limit('bayar_piutang bh, master_beli mb, supplier sp', "bh.no_nota, bh.fak_jual, bh.tgl_byr, bh.cara_byr, bh.jumlah, bh.kasir, bh.nm_bank, mb.tempo tgl_jatuh_tempo, bh.bulat, bh.nogiro, bh.tgl_cair_giro, bh.ket, sp.Nama", "bh.fak_jual=mb.no_faktur_beli and mb.kode_supplier=sp.kode".($where==null?'':' AND '.$where), 'bh.tgl_byr desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
            $data['report'] = $this->m_crud->read_data('bayar_piutang bh, Master_Trx mb, customer sp', "bh.no_nota, bh.fak_jual, bh.tgl_byr, bh.cara_byr, bh.jumlah, bh.kasir, bh.nm_bank, mb.tempo tgl_jatuh_tempo, bh.bulat, bh.nogiro, bh.tgl_cair_giro, bh.ket, sp.Nama", "bh.fak_jual=mb.kd_trx and mb.kd_cust=sp.kd_cust".($where==null?'':' AND '.$where), 'bh.tgl_byr desc', null, $config['per_page'], ($page-1)*$config['per_page']);

            $detail = $this->m_crud->read_data('bayar_piutang bh, Master_Trx mb, customer sp', "bh.jumlah", "bh.fak_jual=mb.kd_trx and mb.kd_cust=sp.kd_cust".($where==null?'':' AND '.$where), 'bh.tgl_byr desc');

            $ttp = 0;
            foreach ($detail as $row) {
                $ttp = $ttp + ($row['jumlah']);
            }

            $data['ttp'] = $ttp;
        }

        if(isset($_POST['to_excel'])){
            $detail_ex = $this->m_crud->read_data('bayar_piutang bh, Master_Trx mb, customer sp', "bh.no_nota, bh.fak_jual, bh.tgl_byr, bh.cara_byr, bh.jumlah, bh.kasir, bh.nm_bank, mb.tempo tgl_jatuh_tempo, bh.bulat, bh.nogiro, bh.tgl_cair_giro, bh.ket, sp.Nama", "bh.fak_jual=mb.kd_trx and mb.kd_cust=sp.kd_cust".($where==null?'':' AND '.$where), 'bh.tgl_byr desc');
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
                    'A'=>'Tanggal', 'B'=>'Kode Penjualan', 'C'=>'Type', 'D'=>'Lokasi', 'E'=>'Nota Supplier', 'F'=>'Operator', 'G'=>'Kode Barang', 'H'=>'Barcode', 'I'=>'Nama Barang', 'J'=>'Jumlah Beli', 'K'=>'Harga Beli', 'L'=>'Pelunasan'
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
            $data['report'] = $this->m_crud->get_data('bayar_piutang bh, Master_Trx mb, supplier sp', "bh.no_nota, bh.fak_jual, bh.tgl_byr, bh.cara_byr, bh.jumlah, bh.kasir, bh.nm_bank, mb.tempo tgl_jatuh_tempo, bh.bulat, bh.nogiro, bh.tgl_cair_giro, bh.ket, sp.Nama", "bh.fak_jual=mb.kd_trx and mb.kode_supplier=sp.kode and bh.no_nota = '".$id."'");
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
public function arsip_penjualan($action = null, $id = null) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '2048M');
        ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

        $this->access_denied(151);
        $data = $this->data;
        $function = 'arsip_penjualan';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Arsip Penjualan';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'tipe' => $_POST['tipe']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $tipe = $this->session->search['tipe'];
        if (isset($date) && $date != null) {
            $explode_date = explode(' - ', $date);
            $date1 = str_replace('/','-',$explode_date[0]);
            $date2 = str_replace('/','-',$explode_date[1]);
        } else {
            $date1 = date('Y-m-d');
            $date2 = date('Y-m-d');
        }

        /*Where date*/
        ($where == null) ? null : $where .= " and ";
        $where .= "LEFT(CONVERT(VARCHAR, rt.tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";

        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="rt.Lokasi = '".$lokasi."'";
        } else {
            ($where==null)?null:$where.=" and "; $where.="rt.Lokasi in (".$this->lokasi_in.")";
        }
        if(isset($tipe)&&$tipe!=null){
            if ($tipe == 'Tunai') {
                ($where==null)?null:$where.=" and "; $where.="rt.bayar > 0";
            } else if ($tipe == 'Non_Tunai') {
                ($where==null)?null:$where.=" and "; $where.="rt.jml_kartu > 0";
            } else {
                ($where==null)?null:$where.=" and "; $where.="rt.voucher > 0";
            }
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(rt.kd_trx like '%".$search."%' or rt.Lokasi like '%".$search."%' or ud.nama like '%".$search."%')"; }

        if(isset($_POST['to_excel'])) {
            //$baca = $this->m_crud->join_data('report_trx rt', "rt.*, cs.nama nama_customer, sl.nama nama_waitres, ud.nama nama_kasir, isnull(cm.nama, '-') nama_compliment, lk.nama nama_lokasi", array(array("type"=>"left","table"=>"customer cs"),array("type"=>"left","table"=>"sales sl"), "user_detail ud", "lokasi lk",array('table'=>'compliment cm', 'type'=>'LEFT')), array("cs.kd_cust=rt.kd_cust", "sl.kode=rt.kd_sales", "ud.user_id=rt.kd_kasir", "lk.kode=rt.lokasi", "cm.compliment_id=rt.compliment"), $where, 'rt.kd_trx desc');
            $baca = $this->m_crud->join_data('report_trx rt', "rt.*, cs.nama nama_customer, sl.nama nama_waitres, ud.nama nama_kasir, isnull(cm.nama, '-') nama_compliment, lk.nama nama_lokasi", array(array("type"=>"left","table"=>"customer cs"),array("type"=>"left","table"=>"sales sl"), "user_detail ud", "lokasi lk",array('table'=>'compliment cm', 'type'=>'LEFT')), array("cs.kd_cust=rt.kd_cust", "sl.kode=rt.kd_sales", "ud.user_id=rt.kd_kasir", "lk.kode=rt.lokasi", "cm.compliment_id=rt.compliment"), $where, 'rt.kd_trx desc');
			$header = array(
                'merge' 	=> array('A1:Z1','A2:Z2','A3:Z3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:Z5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:Z5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $date1.' - '.$date2),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'No. Nota', 'C'=>'Jam', 'D'=>'Customer', 'E'=>'Kasir', 'F'=>'Waitres', 'G'=>'Keterangan', 'H'=>'Sub Total', 'I'=>'Diskon Item', 'J'=>'Diskon Total', 'K'=>'Net Sales', 'L'=>'Tax', 'M'=>'Service', 'N'=>'Gross Sales', 'O'=>'Tunai', 'P'=>'Change', 'Q'=>'Non Tunai', 'R'=>'Charge', 'S'=>'Poin', 'T'=>'Compliment', 'U'=>'Nama Kartu', 'V'=>'Nama Voucher', 'W'=>'Nama Compliment', 'X'=>'Status', 'Y'=>'Lokasi', 'Z'=>'Jenis Trx.'
                )
            );

            $start = 6;
            $end = 0;

            $omset = 0;
            $dis_item = 0;
            $sub_total = 0;
            $dis_rp = 0;
            $compliment = 0;
            $gt = 0;
            $bayar = 0;
            $jml_kartu = 0;
            $charge = 0;
            $change = 0;
            $voucher = 0;
            $tax = 0;
            $service = 0;

            foreach($baca as $row => $value) {
                $diskon = $value['disc_item']+$value['dis_rp'];
                $net = $value['st']-$diskon;
                $gs = $net+$value['tax']+$value['service'];

                $body[$row] = array(
                    $value['tgl'], $value['kd_trx'], substr($value['jam'], 0, 8), $value['nama_customer'], $value['nama_kasir'], $value['nama_waitres'], $value['ket_kas_lain'], $value['st'], $value['disc_item'], $value['dis_rp'], $net, $value['tax'], $value['service'], $gs, $value['bayar'], $value['change'], $value['jml_kartu'], $value['charge'], $value['nominal_poin'], $value['compliment_rp'], $value['kartu'], $value['nm_voucher'], $value['nama_compliment'], $value['status'], $value['nama_lokasi'], $value['Jenis_trx']
                );

                $omset = $omset + $value['st'];
                $dis_item = $dis_item + $value['disc_item'];
                $dis_rp = $dis_rp + $value['dis_rp'];
                $gt = $gt + $gs;
                $bayar = $bayar + $value['bayar'];
                $change = $change + $value['change'];
                $jml_kartu = $jml_kartu + $value['jml_kartu'];
                $charge = $charge + $value['charge'];
                $voucher = $voucher + $value['nominal_poin'];
                $compliment = $compliment + $value['compliment_rp'];
                $tax = $tax + $value['tax'];
                $service = $service + $value['service'];
                $sub_total = $sub_total + $net;

                $end = $row+1;
            }

            $body[$end] = array('TOTAL','','','','','','',$omset,$dis_item,$dis_rp,$sub_total,$tax,$service,$gt,$bayar,$change,$jml_kartu,$charge,$voucher,$compliment);
            $header['font']['A'.($end+$start).':Z'.($end+$start).''] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');
            $header['alignment']['A'.($end+$start).':G'.($end+$start).''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            array_push($header['merge'], 'A'.($end+$start).':G'.($end+$start));

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);

            /*$data['det_report'] = $this->m_crud->read_data('Master_Trx mt, Det_Trx dt, Customer cs, barang br', "mt.kd_trx ,mt.tgl, cs.Nama, br.kd_brg, br.nm_brg, br.barcode, dt.qty, dt.hrg_jual, dt.dis_persen, dt.hrg_beli, dt.subtotal, mt.kd_kasir, mt.dis_rp, mt.dis_persen, mt.ST, mt.tax, mt.rounding, mt.GT, mt.bayar, mt.change, mt.kartu, mt.jml_kartu, mt.jns_kartu, mt.sisa, mt.Tempo, mt.jam, mt.status, mt.HR, mt.kassa, mt.RegMember, mt.charge, mt.no_kartu, mt.pemilik_kartu, mt.kas_lain, mt.ket_kas_lain, mt.Lokasi, mt.kd_sales, mt.no_po, mt.voucher, mt.nm_voucher, mt.lama_tempo, mt.Jenis_trx, (SELECT COUNT(kd_trx) FROM Det_Trx WHERE Det_Trx.kd_trx=mt.kd_trx) baris", "mt.HR = 'S' AND dt.qty > 0 AND mt.kd_trx=dt.kd_trx AND mt.kd_cust=cs.kd_cust AND dt.kd_brg=br.kd_brg".($where==null?' ' : ' AND '.$where), 'mt.tgl desc');
            $baca = $data['det_report'];
            $header = array(
                'merge' 	=> array('A1:AB1','A2:AB2','A3:AB3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:AB5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:AB5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $date1.' - '.$date2),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'No. Nota', 'C'=>'Jam', 'D'=>'Customer', 'E'=>'Kasir', 'F'=>'Kode Barang', 'G'=>'Barcode', 'H'=>'Nama Barang', 'I'=>'Qty', 'J'=>'Harga Jual', 'K'=>'Diskon Item', 'L'=>'Sub Total', 'M'=>'Disc. Total(%)', 'N'=>'Disc. Total(Rp)', 'O'=>'Reg. Member', 'P'=>'Trx. Lain', 'Q'=>'Keterangan', 'R'=>'Grand Total', 'S'=>'Tunai', 'T'=>'Non Tunai', 'U'=>'Charge', 'V'=>'Voucher', 'W'=>'Nama Voucher', 'X'=>'Nama Kartu', 'Y'=>'Mesin EDC', 'Z'=>'Status', 'AA'=>'Lokasi', 'AB'=>'Jenis Trx.'
                )
            );

            $rowspan = 1;
            $start = 6;
            $end = 0;
            $qty = 0;

            foreach($baca as $row => $value){
                if ($rowspan <= 1) {
                    $start = $start + $end;
                    $end = $start + $value['baris'] -1;
                    array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'M'.$start.':M'.$end.'', 'N'.$start.':N'.$end.'', 'O'.$start.':O'.$end.'', 'P'.$start.':P'.$end.'', 'Q'.$start.':Q'.$end.'', 'R'.$start.':R'.$end.'', 'S'.$start.':S'.$end.'', 'T'.$start.':T'.$end.'', 'U'.$start.':U'.$end.'', 'V'.$start.':V'.$end.'', 'W'.$start.':W'.$end.'', 'X'.$start.':X'.$end.'', 'Y'.$start.':Y'.$end.'', 'Z'.$start.':Z'.$end.'', 'AA'.$start.':AA'.$end.'', 'AB'.$start.':AB'.$end.'');
                    $rowspan = $value['baris'];
                    if ($value['baris'] == 1) {
                        $start = 1;
                    }
                }else {
                    $rowspan = $rowspan - 1;
                    $start = 1;
                }

                $body[$row] = array(
                    substr($value['tgl'], 0, 10), $value['kd_trx'], substr($value['jam'], 10, 9), $value['Nama'], $value['kd_kasir'], $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['qty'], $value['hrg_jual'], $value['dis_persen'], $value['subtotal'], $value['dis_persen'], $value['dis_rp'], $value['RegMember'], $value['kas_lain'], $value['ket_kas_lain'], $value['GT'], $value['bayar'], $value['jml_kartu'], $value['charge'], $value['voucher'], $value['nm_voucher'], $value['kartu'], $value['pemilik_kartu'], $value['status'], $value['Lokasi'], $value['Jenis_trx']
                );

                $qty = $qty + $value['qty'];

                $end2 = $row+1;
            }

            $body[$end2] = array('TOTAL','','','','','','','',$qty,'',$dis_item,$omset,$dis_persen,$dis_rp,'',$kas_lain,'',$gt,$bayar,$jml_kartu,$charge,$voucher);
            array_push($header['merge'], 'A'.($end+1).':H'.($end+1));
            $header['font']['A'.($end+1).':AB'.($end+1)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $header['alignment']['A6:E'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['M6:AB'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['F6:H'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);*/
        }

		
        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('Master_Trx', "tgl, kd_trx, Lokasi, kd_kasir", "kd_trx = '".$id."'");
            $data['report_detail'] = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "dt.*, br.deskripsi, br.satuan", "mt.HR = 'S' AND dt.qty > 0 AND dt.kd_trx=mt.kd_trx AND dt.kd_brg=br.kd_brg AND dt.kd_trx = '".$id."'");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['kd_trx']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Arsip Penjualan</b></h3></div>
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
									<td>'.substr($data['report']['tgl'], 0, 10).'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td><b>No. Nota</b></td>
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
                'left'=>10,'right'=>10,'top'=>55,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        }
        else {
            $page = ($id==null?1:$id);
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data_join_over('report_trx rt', 'rt.kd_trx',array(array('type'=>'left','table'=>'customer cs'), array('type'=>'left','table'=>'sales sl'), 'user_detail ud', 'lokasi lk', array('table'=>'compliment cm', 'type'=>'LEFT')), array('cs.kd_cust=rt.kd_cust', 'sl.kode=rt.kd_sales', 'ud.user_id=rt.kd_kasir', 'lk.kode=rt.lokasi', 'cm.compliment_id=rt.compliment'), $where, null, 'rt.kd_trx',1);
         // $config['total_rows'] = $this->m_crud->count_data_join_over("report_trx rt", 'rt.kd_trx', array("customer cs", "sales sl", "user_detail ud", "lokasi lk", array('table'=>'complimen cm', 'type'=>'LEFT')), array("cs.kd_cust=rt.kd_cust", "sl.kode=rt.kd_sales", "ud.user_id=rt.kd_kasir", "lk.kode=rt.lokasi", "cm.compliment_id=rt.compliment"), $where, null, "rt.kd_trx");
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

            //$data['report'] = $this->m_crud->select_limit_join('Master_Trx mt, Det_Trx dt, Customer cs', "mt.kd_trx ,mt.tgl, cs.Nama, SUM(dt.qty * dt.hrg_jual) omset, SUM(dt.dis_persen) diskon_item, SUM((dt.qty * dt.hrg_jual)-(dt.qty * dt.hrg_beli)) profit, mt.kd_kasir, mt.dis_rp, mt.dis_persen, mt.ST, mt.tax, mt.rounding, mt.voucher, mt.nm_voucher, mt.GT, mt.bayar, mt.change, mt.kartu, mt.jml_kartu, mt.jns_kartu, mt.sisa, mt.Tempo, mt.jam, mt.status, mt.HR, mt.kassa, mt.RegMember, mt.charge, mt.no_kartu, mt.pemilik_kartu, mt.kas_lain, mt.ket_kas_lain, mt.Lokasi, mt.kd_sales, mt.no_po, mt.lama_tempo, mt.Jenis_trx", /*dt.qty > 0 AND*/"mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND mt.kd_cust=cs.kd_cust".($where==null?' ' : ' AND '.$where), 'mt.tgl desc, mt.jam desc', "mt.kd_trx, mt.tgl, cs.Nama,mt.dis_rp, mt.dis_persen, mt.ST, mt.tax, mt.rounding, mt.GT, mt.bayar, mt.change, mt.kartu, mt.jml_kartu, mt.jns_kartu, mt.sisa, mt.Tempo, mt.jam, mt.status, mt.HR, mt.kassa, mt.RegMember, mt.charge, mt.no_kartu, mt.pemilik_kartu, mt.kas_lain, mt.ket_kas_lain, mt.Lokasi, mt.kd_sales, mt.no_po, mt.lama_tempo, mt.Jenis_trx, mt.kd_kasir, mt.voucher, mt.nm_voucher", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
            $data['report'] = $this->m_crud->select_limit_join(
                'report_trx rt', "rt.*, cs.nama nama_customer, sl.nama nama_waitres, ud.nama nama_kasir, isnull(cm.nama, '-') nama_compliment, lk.nama nama_lokasi",
                array(array("type"=>"left","table"=>"customer cs"), array("type"=>"left","table"=>"sales sl"), "user_detail ud", "lokasi lk", array('table'=>'compliment cm', 'type'=>'LEFT')), array("cs.kd_cust=rt.kd_cust", "sl.kode=rt.kd_sales", "ud.user_id=rt.kd_kasir", "lk.kode=rt.lokasi", "cm.compliment_id=rt.compliment"), $where, 'rt.kd_trx asc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

            //$total = $this->m_crud->read_data('Master_Trx mt, Det_Trx dt, Customer cs', "SUM(dt.qty * dt.hrg_jual) omset, SUM(dt.dis_persen) diskon_item, SUM((dt.qty * dt.hrg_jual)-(dt.qty * dt.hrg_beli)) profit, mt.dis_rp, mt.dis_persen, mt.voucher, mt.bayar, mt.change, mt.jml_kartu, mt.charge, mt.kas_lain", /*dt.qty > 0 AND*/"mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND mt.kd_cust=cs.kd_cust".($where==null?' ' : ' AND '.$where), 'mt.tgl desc', "mt.tgl, mt.dis_rp, mt.dis_persen, mt.bayar, mt.change, mt.jml_kartu, mt.charge, mt.kas_lain, mt.voucher");
            // $total = $this->m_crud->get_join_data('report_trx rt', "SUM(rt.st) omset, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) dis_rp, sum(rt.nominal_poin) nominal_poin, sum(rt.compliment_rp) compliment, SUM(rt.bayar) bayar, sum(rt.change) change, sum(rt.jml_kartu) jml_kartu, sum(rt.charge) charge, sum(rt.tax) tax, sum(rt.service) service", array("customer cs", "sales sl", "user_detail ud", "lokasi lk", array('table'=>'compliment cm', 'type'=>'LEFT')), array("cs.kd_cust=rt.kd_cust", "sl.kode=rt.kd_sales", "ud.user_id=rt.kd_kasir", "lk.kode=rt.lokasi", "cm.compliment_id=rt.compliment"), $where);
            $total = $this->m_crud->get_join_data(
				'report_trx rt',
				 "SUM(rt.st) omset, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) dis_rp, sum(rt.nominal_poin) nominal_poin, sum(rt.compliment_rp) compliment, SUM(rt.bayar) bayar, sum(rt.change) change, sum(rt.jml_kartu) jml_kartu, sum(rt.charge) charge, sum(rt.tax) tax, sum(rt.service) service",
				 array(array("type"=>"left","table"=>"customer cs"), array("type"=>"left","table"=>"sales sl"), "user_detail ud", "lokasi lk", array('table'=>'compliment cm', 'type'=>'LEFT')), array("cs.kd_cust=rt.kd_cust", "sl.kode=rt.kd_sales", "ud.user_id=rt.kd_kasir", "lk.kode=rt.lokasi", "cm.compliment_id=rt.compliment"),
				 $where);

            $tsub = $total['omset']-$total['diskon_item']-$total['dis_rp'];
            $gt = $tsub+$total['tax']+$total['service'];

            $data['tomset'] = $total['omset'];
            $data['tdis_item'] = $total['diskon_item'];
            $data['tdis_rp'] = $total['dis_rp'];
            $data['tsub_total'] = $tsub;
            $data['ttax'] = $total['tax'];
            $data['tservice'] = $total['service'];
            $data['tgt'] = $gt;
            $data['tbayar'] = $total['bayar'];
            $data['tchange'] = $total['change'];
            $data['tjml_kartu'] = $total['jml_kartu'];
            $data['tcharge'] = $total['charge'];
            $data['tvoucher'] = $total['nominal_poin'];
            $data['tcompliment'] = $total['compliment'];
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function detil_penjualan_group1($param = null, $kode = null, $lokasi = null, $tgl = null, $all = null) {
        $data = $this->data;
		if ($param == null) {
            $kode = $_POST['kode_'];
            $lokasi = $_POST['lokasi_'];
            $tgl = $_POST['tgl_periode_'];
            $all = $_POST['all_'];
        } else {
            $kode = base64_decode($kode);
            $lokasi = base64_decode($lokasi);
            $tgl = base64_decode($tgl);
            $all = base64_decode($all);
        }
        $explode_date = explode(' - ', $tgl);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);

        $no = 0;
        $list_barang = '';
        $jbeli = 0; $jjual = 0; $sa = 0; $sp = 0; $st = 0; $jl = 0; $rt = 0; $di = 0; $adj = 0; $mut = 0;

        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $get_supplier = $this->m_crud->get_data("Supplier", "kode, Nama", "kode='".$kode."'");
        $q_tgl = " BETWEEN '".$tgl_awal." 00:00:00' AND '".$tgl_akhir." 23:59:59'";

        if (isset($_POST['filter_']) && isset($_POST['value_'])) {
            $filter = $_POST['filter_'];
            $value = $_POST['value_'];
            $where = "".$filter." like '%".$value."%' AND br.Group1 = '".$kode."'";
            if ($all == 1) {
                $on_join = " AND dp.tgl".$q_tgl;
            } else {
                $where .= " AND dp.tgl".$q_tgl;
            }
        } else {
            $where = "br.Group1 = '".$kode."'";
            if ($all == 1) {
                $on_join = " AND dp.tgl".$q_tgl;
            } else {
                $where .= " AND dp.tgl".$q_tgl;
            }
        }

        $where_stock = " AND stk.lokasi NOT IN ('MUTASI', 'Retur', 'HO') and stk.lokasi <> '' and stk.lokasi is not null";
        $view_lokasi = "Semua Lokasi (tanpa HO)";
        if($lokasi!=null && $lokasi!='' && $lokasi!='null') {
            $array_lokasi = array();
            $data_lokasi = $lokasi;
            if ($param != null) {
                $lokasi = explode(',', $lokasi);
            }
            for ($i = 0; $i < count($lokasi); $i++) {
                array_push($array_lokasi, '\''.$lokasi[$i].'\'');
            }
            sort($array_lokasi);
            if ($all == 1) {
                $on_join.=" AND dp.Lokasi IN (".implode(', ', $array_lokasi).")";
            } else {
                ($where==null)?null:$where.=" and ";
                $where.="dp.Lokasi IN (".implode(', ', $array_lokasi).")";
            }
            $where_stock .= " AND stk.lokasi IN (".implode(', ', $array_lokasi).")";
            $view_lokasi = "Lokasi ".implode(', ', $array_lokasi);
        }
        $stock_awal = " ,ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE keterangan not like '%Adjustment%' AND kd_brg=br.kd_brg AND left(convert(varchar, tgl, 120), 10)<'".$tgl_awal."'".$where_stock.") ,0) stock_awal";
        $stock_masuk = " ,ISNULL((SELECT SUM(stock_in) FROM Kartu_stock stk WHERE keterangan not like '%Adjustment%' AND  keterangan not like '%Mutasi%' AND kd_brg=br.kd_brg AND tgl ".$q_tgl.$where_stock.") ,0) stock_masuk";
        $jumlah_retur = " ,ISNULL((SELECT SUM(stock_out) FROM Kartu_stock stk WHERE kd_brg=br.kd_brg AND keterangan='Retur Pembelian' AND tgl ".$q_tgl.$where_stock."), 0) retur";
        $jumlah_adjust = " ,ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE kd_brg=br.kd_brg AND keterangan like '%Adjustment%' AND tgl ".$q_tgl.$where_stock."), 0) adjust";
        $jumlah_mutasi = " ,ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock stk WHERE kd_brg=br.kd_brg AND (keterangan like '%Mutasi%' or keterangan like '%Retur Non Approval%') AND tgl ".$q_tgl.$where_stock."), 0) mutasi";
        if ($all == 1) {
            $detail = $this->m_crud->join_data("barang br", "br.hrg_beli, br.hrg_jual_1 hrg_jual, br.kd_brg, br.barcode, br.Deskripsi, br.nm_brg, br.satuan, SUM(isnull(qty_jual, 0)) jual".$stock_awal.$stock_masuk.$jumlah_retur.$jumlah_adjust.$jumlah_mutasi, array(array("table"=>"detail_penjualan dp", "type"=>"LEFT")), array("dp.kd_brg=br.kd_brg".$on_join), $where, null, 'br.hrg_beli, br.hrg_jual_1, br.kd_brg, br.barcode, br.Deskripsi, br.nm_brg, br.satuan');
        } else {
            $detail = $this->m_crud->read_data("detail_penjualan dp, barang br", "br.hrg_beli, br.hrg_jual_1 hrg_jual, br.kd_brg, br.barcode, br.Deskripsi, br.nm_brg, br.satuan, SUM(qty_jual) jual".$stock_awal.$stock_masuk.$jumlah_retur.$jumlah_adjust.$jumlah_mutasi, $where." AND dp.kd_brg=br.kd_brg", null, 'br.hrg_beli, br.hrg_jual_1, br.kd_brg, br.barcode, br.Deskripsi, br.nm_brg, br.satuan');
        }

        if ($param == 'to_pdf') {
            $header = '
            <table width="100%" border="0">
                <tr>
                    <td>'.$this->m_website->logo(null, "1cm").'</td>
                    <td><b>Laporan Penjualan '.$this->menu_group['as_group1'].'</b></td>
                </tr>
            </table>
            <div style="margin-bottom: 10px;">
                <table width="100%" style="font-size: 10pt">
                    <thead>
                        <tr>
                            <th width="2%"></th>
                            <th width="17%"></th>
                            <th width="2%"></th>
                            <th width="30%"></th>
                            
                            <th width="10%"></th>
                            <th width="10%"></th>
                            <th width="2%"></th>
                            <th width="27%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td><b>Kode '.$this->menu_group['as_group1'].'</b></td>
                            <td><b>:</b></td>
                            <td>'.$get_supplier['kode'].'</td>
                            <td></td>
                            <td colspan="3"><b>'.$tgl.'</b></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><b>Nama '.$this->menu_group['as_group1'].'</b></td>
                            <td><b>:</b></td>
                            <td>'.$get_supplier['Nama'].'</td>
                            <td></td>
                            <td colspan="3"><b>'.$view_lokasi.'</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>';

            $data2 = array(
                'title' => 'Laporan Penjualan By Supplier',
                'head' => $header,
                'detail' => $detail
            );

            //load the view and saved it into $html variable
            $html=$this->load->view('bo/penjualan/pdf_invoice_penjualan_by_group1', $data2, true);

            //this the the PDF filename that user will get to download
            //$nama = $getSpk->kode_spk;
            $nama = 'laporan_penjualan';
            $pdfFilePath = $nama.".pdf";

            //load mPDF library
            $this->load->library('m_report_p');

            //generate the PDF from the given html
            $this->m_report_p->pdf->WriteHTML($html);

            //download it.
            $this->m_report_p->pdf->Output($pdfFilePath, "I");
        } else if($param == 'to_excel'){
			$baca = $detail;
            $header = array(
                'merge' 	=> array('A1:O1','A2:O2'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A4:F4' => array('bold'=>true, 'name'=>'Verdana'),
                    'A5:F5' => array('bold'=>true, 'name'=>'Verdana'),
                    'A6:O6' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array(
                    'A1:A2' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:O5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' =>'Penjualan '.$this->menu_group['as_group1']),
                '4' => array('A' => 'Kode '.$this->menu_group['as_group1'], 'B' => $get_supplier['kode'], 'N' => 'Periode', 'O' => $tgl), 
                '5' => array('A' => 'Nama '.$this->menu_group['as_group1'], 'B' => $get_supplier['Nama'], 'N' => 'Lokasi', 'O' => $view_lokasi), 
                '6' => array(
                    'A'=>'Kode Barang', 'B'=>'Barcode', 'C'=>$this->menu_group['as_deskripsi'], 'D'=>'Nama Barang', 'E'=>'Stock Awal', 'F'=>'Stock Masuk', 'G'=>'Jual', 'H'=>'Retur', 'I'=>'Adjust', 'J'=>'Mutasi', 'K'=>'Stock Akhir', 'L'=>'Harga Beli', 'M'=>'Harga Jual', 'N'=>'Jumlah Beli', 'O'=>'Jumlah Jual'
                )
            );
			
			foreach($baca as $row => $value){
                $body[$row] = array(
					$value['kd_brg'], $value['barcode'], $value['Deskripsi'], $value['nm_brg'], ($value['stock_awal'] + 0), ($value['stock_masuk'] + 0), ($value['jual'] + 0), 
					($value['retur'] + 0), ($value['adjust'] + 0), ($value['mutasi'] + 0), 
					($value['stock_awal'] + $value['stock_masuk'] - $value['jual'] - $value['retur'] + $value['adjust'] + $value['mutasi'] + 0), 
					$value['hrg_beli'], $value['hrg_jual'], ($value['hrg_beli'] * $value['jual']), ($value['hrg_jual'] * $value['jual'])
				);
				$jbeli = $jbeli + ($value['hrg_beli'] * $value['jual']);
				$jjual = $jjual + ($value['hrg_jual'] * $value['jual']);
				$sa = $sa + ($value['stock_awal'] + 0);
				$sp = $sp + ($value['stock_masuk'] + 0);
				$st = $st + ($value['stock_awal'] + $value['stock_masuk'] - $value['jual'] - $value['retur'] + $value['adjust'] + $value['mutasi'] + 0);
				$jl = $jl + ($value['jual'] + 0);
				$rt = $rt + ($value['retur'] + 0);
				$adj = $adj + ($value['adjust'] + 0);
				$mut = $mut + ($value['mutasi'] + 0);
				$di = $di + $value['diskon_item'];
            	$end = $row+1;
			}
            $body[$end] = array('TOTAL', '', '', '', $sa, $sp, $jl, $rt, $adj, $mut, $st, '', '', $jbeli, $jjual);
			
            $this->m_export_file->to_excel(str_replace(' ', '_', 'Penjualan '.$this->menu_group['as_group1']), $header, $body);
		} else {
            foreach($detail as $rows) {
                $no++;
                $list_barang .= '
                <tr>
                    <td>' . $no . '</td>
                    <td>' . $rows['kd_brg'] . '</td>
                    <td>' . $rows['barcode'] . '</td>
                    <td>' . $rows['Deskripsi'] . '</td>
                    <td>' . $rows['nm_brg'] . '</td>
                    <td>' . ($rows['stock_awal'] + 0) . '</td>
                    <td>' . ($rows['stock_masuk'] + 0) . '</td>
                    <td>' . ($rows['jual'] + 0) . '</td>
                    <td>' . ($rows['retur'] + 0) . '</td>
                    <td>' . ($rows['adjust'] + 0) . '</td>
                    <td>' . ($rows['mutasi'] + 0) . '</td>
                    <td>' . ($rows['stock_awal'] + $rows['stock_masuk'] - $rows['jual'] - $rows['retur'] + $rows['adjust'] + $rows['mutasi'] + 0) . '</td>
                    <td class="text-right">' . number_format($rows['hrg_beli']) . '</td>
                    <td class="text-right">' . number_format($rows['hrg_jual']) . '</td>
                    <td class="text-right">' . number_format($rows['hrg_beli'] * $rows['jual']) . '</td>
                    <td class="text-right">' . number_format($rows['hrg_jual'] * $rows['jual']) . '</td>
                </tr>
            ';
                $jbeli = $jbeli + ($rows['hrg_beli'] * $rows['jual']);
                $jjual = $jjual + ($rows['hrg_jual'] * $rows['jual']);
                $sa = $sa + ($rows['stock_awal'] + 0);
                $sp = $sp + ($rows['stock_masuk'] + 0);
                $st = $st + ($rows['stock_awal'] + $rows['stock_masuk'] - $rows['jual'] - $rows['retur'] + $rows['adjust'] + $rows['mutasi'] + 0);
                $jl = $jl + ($rows['jual'] + 0);
                $rt = $rt + ($rows['retur'] + 0);
                $adj = $adj + ($rows['adjust'] + 0);
                $mut = $mut + ($rows['mutasi'] + 0);
                $di = $di + $rows['diskon_item'];

                $foot = '
                <tr>
                    <th colspan="5">TOTAL</th>
                    <th>'.$sa.'</th>
                    <th>'.$sp.'</th>
                    <th>'.$jl.'</th>
                    <th>'.$rt.'</th>
                    <th>'.$adj.'</th>
                    <th>'.$mut.'</th>
                    <th>'.$st.'</th>
                    <th></th>
                    <th></th>
                    <!--<th class="text-right"><?/*=number_format($di)*/?></th>-->
                    <th class="text-right">'.number_format($jbeli).'</th>
                    <th class="text-right">'.number_format($jjual).'</th>
                </tr>
            ';
            }

            echo json_encode(array('list_barang' => $list_barang, 'foot' => $foot, 'kd' => $get_supplier['kode'], 'nm' => $get_supplier['Nama']));
        }
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
        $detail = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "SUM(dt.qty) jumlah, SUM(dt.qty * dt.dis_persen) diskon_item, SUM(dt.qty * hrg_jual) sub_total, br.kd_brg, br.Deskripsi, br.nm_brg, br.barcode, br.satuan", "mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND br.Group1 = '".$kode."' AND ".$filter." like '%".$value."%' ".$q_filter.$q_tgl.$where."", null, "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan");

        foreach ($detail as $row) {
            $list_tabel .= '
            <tr>
                <td>'.$no.'</td>
                <td>'.$row['kd_brg'].'</td>
                <td>'.$row['barcode'].'</td>
                <td>'.$row['Deskripsi'].'</td>
                <td>'.$row['nm_brg'].'</td>
                <td class="text-right">'.($row['jumlah']+0).'</td>
                <td>'.$row['satuan'].'</td>
                <td class="text-right">'.number_format($row['diskon_item']).'</td>
                <td class="text-right">'.number_format($row['sub_total']-$row['diskon_item']).'</td>
            </tr>
            ';
            $no++;
            $total = $total + ($row['sub_total']-$row['diskon_item']);
        }

        echo json_encode(array('table' => $list_tabel, 'jumlah' => number_format($total)));
    }

    public function penjualan_by_group1($action = null, $id = null, $filter = null, $tmp_lokasi = null, $jenis_export = null){
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '2048M');
		
        $this->access_denied(152);
        $data = $this->data;
        $function = 'penjualan_by_group1';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = 'Group1';
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Penjualan By '.$this->menu_group['as_group1'];
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $tgl = ''; $periode = 'Periode : None'; $q_lokasi = 'Semua Lokasi (tanpa HO)';

        $lokasi = array();
        $lokasi["lokasi_list"] = array();
        if (isset($_POST['lokasi']) && $_POST['lokasi']!='') {
            for ($i = 0; $i < count($_POST['lokasi']); $i++) {
                $data_lokasi = array();
                $data_lokasi["kode"] = $_POST['lokasi'][$i];
                array_push($lokasi["lokasi_list"], $data_lokasi);
            }
        }

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => json_encode($lokasi)));
        }

        $search = $this->session->search['any']; $list_lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        if (isset($date) && $date != null) {
            $tgl_awal = str_replace('/','-',$explode_date[0]);
            $tgl_akhir = str_replace('/','-',$explode_date[1]);
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
            $periode = "Periode : ".$tgl_awal." - ".$tgl_akhir;
        }else{
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
            $periode = "Periode : ".$tgl_awal." - ".$tgl_akhir;
        }
        if(isset($_POST['lokasi'])){
            $array_lokasi = array();
            $data_lokasi = json_decode($list_lokasi, true);
            for ($i = 0; $i < count($data_lokasi['lokasi_list']); $i++) {
                array_push($array_lokasi, '\''.$data_lokasi['lokasi_list'][$i]['kode'].'\'');
            }
            sort($array_lokasi);
            ($where==null)?null:$where.=" and ";
            $where.="mt.Lokasi IN (".implode(', ', $array_lokasi).")";
            $q_lokasi = "Lokasi : ".str_replace("'", "", implode(', ', $array_lokasi))."";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(gr.Kode like '%".$search."%' or gr.Nama like '%".$search."%')"; }

        $data['tgl'] = $tgl;
        $data['periode'] = $periode;
        $data['q_lokasi'] = $q_lokasi;
        $data['lokasi'] = $lokasi;
        $data['tgl_awal'] = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;

		if($filter==null && ($action!='download'||$action!='print')){
			$page = ($id==null?1:$id);
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
			$config['total_rows'] = $this->m_crud->count_data("Master_Trx mt, Det_Trx dt, barang br, Group1 gr", 'gr.Kode', "mt.kd_trx=dt.kd_trx AND br.Group1=gr.Kode AND dt.kd_brg=br.kd_brg".($where==null?'':' AND '.$where), "gr.Kode asc", "gr.Kode");
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

			$data['report'] = $this->m_crud->select_limit("Master_Trx mt, Det_Trx dt, barang br, Group1 gr", "gr.Kode, gr.Nama, SUM(dt.qty) qty, SUM(dt.qty * br.hrg_jual_1) gross_sales, SUM(dt.dis_persen) diskon_item, SUM(mt.dis_rp) dis_trans, SUM(dt.qty*br.hrg_beli) total_beli", "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND br.Group1=gr.Kode AND dt.kd_brg=br.kd_brg".($where==null?'':' AND '.$where), "gr.Kode asc", "gr.Nama, gr.Kode", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

			$total = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br, Group1 gr", "gr.Kode, gr.Nama, SUM(dt.qty) qty, SUM(dt.qty * br.hrg_jual_1) gross_sales, SUM(dt.dis_persen) diskon_item, SUM(mt.dis_rp) dis_trans, SUM(dt.qty*br.hrg_beli) total_beli", "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND br.Group1=gr.Kode AND dt.kd_brg=br.kd_brg".($where==null?'':' AND '.$where), "gr.Kode asc", "gr.Nama, gr.Kode");

			$qty = 0;
			$gs = 0;
			$di = 0;
			$ns = 0;
			$tb = 0;

			foreach ($total as $row) {
				$qty = $qty + $row['qty'];
				$gs = $gs + $row['gross_sales'];
				$di = $di + $row['diskon_item'];
				$ns = $ns + ($row['gross_sales'] - $row['diskon_item']);
				$tb = $tb + ($row['total_beli']);
			}

			$data['tqty'] = $qty;
			$data['tgs'] = $gs;
			$data['tdi'] = $di;
			$data['tns'] = $ns;
			$data['ttb'] = $tb;
		}
		
        if(isset($_POST['to_excel'])){
            $baca = $total;
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

            $body[$i] = array('TOTAL', '', $qty, $gs, $di, $ns, $tb);
            array_push($header['merge'], 'A'.($i+6).':B'.($i+6).'');
            $header['font']['A'.($i+6).':G'.($i+6).''] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $q_filter = '';
            $s_filter = '';
            $data['report'] = $this->m_crud->get_data('Group1', "*", "Kode = '".$id."'");
            if ($filter!=null) {
                $s_filter = base64_decode($filter);
                if ($s_filter == 'inventory') {

                } else {
                    $filter = json_decode(base64_decode($filter));
                    $q_filter = ' AND ' . $filter[0] . ' like \'%' . $filter[1] . '%\'';
                }
            }

            if ($s_filter == 'inventory') {
                $array_lokasi = array();
                $tmp_lokasi = json_decode(base64_decode($tmp_lokasi), true);
                for ($i = 0; $i < count($tmp_lokasi); $i++) {
                    array_push($array_lokasi, '\''.$tmp_lokasi[$i].'\'');
                }
                sort($array_lokasi);
                $data['content'] = $view.'pdf_invoice_inventory_'.$function;
                $result = '';

                if (count($array_lokasi)>0) {
                    $q_lokasi = 'Lokasi : '.implode(', ', $array_lokasi);
                }
				
                $where_lokasi = (count($array_lokasi)>0)?" AND lokasi IN (".implode(', ', $array_lokasi).") and lokasi <> 'HO'":" and lokasi <> 'HO'";
                $harga_jual = (count($array_lokasi)>0)?" ,isnull((SELECT AVG(hrg_jual_1) FROM barang_hrg WHERE barang=kd_brg AND lokasi IN (".implode(', ', $array_lokasi).")), hrg_jual_1) hrg_jual_1":" ,hrg_jual_1";
                $qty_stock = "isnull((SELECT SUM(stock) FROM inventory_stock invs WHERE invs.kd_brg=barang.kd_brg".$where_lokasi."), 0)";
                $qty_jual = "isnull((SELECT SUM(qty) FROM inventory_trx it WHERE it.kd_brg=barang.kd_brg AND tgl BETWEEN '".$tgl_awal." 00:00:00' AND '".$tgl_akhir." 23:59:59'".$where_lokasi."), 0)";
                $qty_alokasi = "isnull((SELECT SUM(qty) FROM inventory_mutasi im WHERE im.kd_brg=barang.kd_brg AND tgl BETWEEN '".$tgl_awal." 00:00:00' AND '".$tgl_akhir." 23:59:59'".$where_lokasi."), 0)";
                $tgl_jual = " ,(SELECT TOP 1 tgl FROM inventory_trx it WHERE it.kd_brg=barang.kd_brg AND tgl BETWEEN '".$tgl_awal." 00:00:00' AND '".$tgl_akhir." 23:59:59'".$where_lokasi." ORDER BY tgl DESC) tgl_jual";
                $tgl_alokasi = " ,(SELECT TOP 1 tgl FROM inventory_mutasi im WHERE im.kd_brg=barang.kd_brg AND tgl BETWEEN '".$tgl_awal." 00:00:00' AND '".$tgl_akhir." 23:59:59'".$where_lokasi." ORDER BY tgl DESC) tgl_alokasi";
                
				$jenis_export = base64_decode($jenis_export);
				if($jenis_export=='xls'){
					$detail_barang = $this->m_crud->read_data("barang, Group2, kel_brg", "ltrim(rtrim(Nama)) Nama, ltrim(rtrim(nm_kel_brg)) nm_kel_brg, ltrim(rtrim(barang.Group2)) Group2, ltrim(rtrim(barang.kel_brg)) kel_brg, kd_brg, nm_brg, hrg_beli, (SELECT COUNT(br.Group2) FROM barang br WHERE ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_dept, (SELECT COUNT(br.kel_brg) FROM barang br WHERE ltrim(rtrim(br.kel_brg))=ltrim(rtrim(barang.kel_brg)) AND ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_kel_brg".$harga_jual.",".$qty_stock." qty_stock,".$qty_jual." qty_jual,".$qty_alokasi." qty_alokasi".$tgl_jual.$tgl_alokasi, "ltrim(rtrim(barang.Group2))=ltrim(rtrim(Group2.Kode)) AND ltrim(rtrim(barang.kel_brg))=ltrim(rtrim(kel_brg.kel_brg)) AND ltrim(rtrim(Group1))=ltrim(rtrim('".$id."'))", "barang.Group2 ASC, barang.kel_brg ASC, kd_brg ASC", "Nama, nm_kel_brg, barang.Group2, barang.kel_brg, kd_brg, nm_brg, hrg_beli, hrg_jual_1");
                } else if($jenis_export=='pdf'){
					$limit = 1000; 
					$count_barang = $this->m_crud->count_data("barang, Group2, kel_brg", "kd_brg", "ltrim(rtrim(barang.Group2))=ltrim(rtrim(Group2.Kode)) AND ltrim(rtrim(barang.kel_brg))=ltrim(rtrim(kel_brg.kel_brg)) AND ltrim(rtrim(Group1))=ltrim(rtrim('".$id."'))");
					$interval_brg = (int)($count_barang/$limit); if($interval_brg < ($count_barang/$limit)){ $interval_brg++; }
					//echo $interval_brg.'<br/><br/>';
					for($i=1; $i<=$interval_brg; $i++){
						$limit_start = ($i-1)*$limit+1; $limit_end = ($limit*$i);
						//echo 'start:'.$limit_start.' - end:'.$limit_end.'<br/><br/>';
						$detail_barang[$i] = $this->m_crud->select_limit("barang, Group2, kel_brg", "kd_brg, nm_brg, hrg_beli".$harga_jual.",".$qty_stock." qty_stock,".$qty_jual." qty_jual,".$qty_alokasi." qty_alokasi".$tgl_jual.$tgl_alokasi, "ltrim(rtrim(barang.Group2))=ltrim(rtrim(Group2.Kode)) AND ltrim(rtrim(barang.kel_brg))=ltrim(rtrim(kel_brg.kel_brg)) AND ltrim(rtrim(Group1))=ltrim(rtrim('".$id."'))", "barang.Group2 ASC, barang.kel_brg ASC, kd_brg ASC", "Nama, nm_kel_brg, barang.Group2, barang.kel_brg, kd_brg, nm_brg, hrg_beli, hrg_jual_1", $limit_start, $limit_end);
					} 
				}
				
				$get_nama_supplier = $this->m_crud->get_data("Supplier", "nama", "kode='".$id."'");

                $row_1 = 1;
                $row_2 = 1;
                $smrg = 0; $sstq = 0; $sstv = 0; $stj = 0; $sta = 0;
                $kmrg = 0; $kstq = 0; $kstv = 0; $ktj = 0; $kta = 0;
                $mrg = 0; $stq = 0; $stv = 0; $tj = 0; $ta = 0;

				if($jenis_export=='xls'){
					$baca = $detail_barang;
					$header = array(
						'merge' 	=> array('A1:K1','A2:K2','A3:E3','F3:K3'),
						'auto_size' => true,
						'font' 		=> array(
							'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
							'A3:F3' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>10, 'name'=>'Verdana'),
							'A5:K6' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
						),
						'alignment' => array(
							'A1:A2' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
							'A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
							'F3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),
							'A5:K6' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
						),
						'1' => array('A' => $data['site']->title),
						'2' => array('A' => 'Inventory Supplier ('.$get_nama_supplier['nama'].')'),
						'3' => array('A' => 'Periode : '.$tgl_awal.' - '.$tgl_akhir, 'F' => str_replace("'", "", $q_lokasi)),
						'5' => array('A'=>'Sub Dept - Kel Brg'),
						'6' => array(
							'A'=>'Kd Brg', 'B'=>'Nm Brg', 'C'=>'Hrg Bl', 'D'=>'Hrg Jl', 'E'=>'Mrg %', 'F'=>'Stk Qty', 'G'=>'Stk Val', 'H'=>'Jl Qty', 'I'=>'Alks Qty', 'J'=>'Last Alks', 'K'=>'Last Jl'
						)
					);

					$end = 0;
					$i = 0;
					foreach($baca as $row => $value){

						if ($row_1 <= 1) {
							$row_1 = $value['row_dept'];
							if ($row_2 <= 1) {
								$row_2 = $value['row_kel_brg'];
								$body[$row+$i] = array(
									$value['Group2'] . '/' . $value['Nama'] . ' - ' . $value['kel_brg']
								);
								array_push($header['merge'], 'A'.($row+$i+7).':K'.($row+$i+7).'');
								$header['font']['A'.($row+$i+7).':K'.($row+$i+7).''] = array('bold'=>true);
								$i++;
							} else {
								$row_2 = $row_2 - 1;
							}
						} else {
							if ($row_2 <= 1) {
								$row_2 = $value['row_kel_brg'];
								$body[$row+$i] = array(
									$value['Group2'] . '/' . $value['Nama'] . ' - ' . $value['kel_brg']
								);
								array_push($header['merge'], 'A'.($row+$i+7).':K'.($row+$i+7).'');
								$header['font']['A'.($row+$i+7).':K'.($row+$i+7).''] = array('bold'=>true);
								$i++;
							} else {
								$row_2 = $row_2 - 1;
							}
							$row_1 = $row_1 - 1;
						}

						$body[$row+$i] = array(
							$value['kd_brg'], $value['nm_brg'], $value['hrg_beli'], $value['hrg_jual_1'], (($value['hrg_beli'] > 0 && $value['hrg_beli'] < $value['hrg_jual_1']) ? round((1 - ($value['hrg_beli'] / $value['hrg_jual_1'])) * 100, 2) : '0'), (int)$value['qty_stock'], $value['qty_stock'] * $value['hrg_beli'], $value['qty_jual'], $value['qty_alokasi'], $value['tgl_alokasi'], $value['tgl_jual']
						);

						$stq = $stq + ($value['qty_stock'] + 0);
						$stv = $stv + ($value['qty_stock'] * $value['hrg_beli']);
						$tj = $tj + ($value['qty_jual'] + 0);
						$ta = $ta + ($value['qty_alokasi'] + 0);
						$kstq = $kstq + ($value['qty_stock'] + 0);
						$kstv = $kstv + ($value['qty_stock'] * $value['hrg_beli']);
						$ktj = $ktj + ($value['qty_jual'] + 0);
						$kta = $kta + ($value['qty_alokasi'] + 0);
						$sstq = $sstq + ($value['qty_stock'] + 0);
						$sstv = $sstv + ($value['qty_stock'] * $value['hrg_beli']);
						$stj = $stj + ($value['qty_jual'] + 0);
						$sta = $sta + ($value['qty_alokasi'] + 0);

						if ($row_2 == 1) {
							$i++;
							$body[$row+$i] = array(
								'Total '.$value['Group2'] . '/' . $value['Nama'] . ' - ' . $value['kel_brg'] . '/' . $value['nm_kel_brg'], '', '', '', '', $kstq, $kstv, $ktj, $kta
							);
							array_push($header['merge'], 'A'.($row+$i+7).':E'.($row+$i+7).'');
							$header['font']['A'.($row+$i+7).':K'.($row+$i+7).''] = array('bold'=>true);

							$kmrg = 0;
							$kstq = 0;
							$kstv = 0;
							$ktj = 0;
							$kta = 0;
						}

						if ($row_1 == 1) {
							$i++;
							$body[$row+$i] = array(
								'Total '.$value['Group2'] . '/' . $value['Nama'], '', '', '', '', $sstq, $sstv, $stj, $sta
							);
							array_push($header['merge'], 'A'.($row+$i+7).':E'.($row+$i+7).'');
							$header['font']['A'.($row+$i+7).':K'.($row+$i+7).''] = array('bold'=>true);

							$smrg = 0;
							$sstq = 0;
							$sstv = 0;
							$stj = 0;
							$sta = 0;
						}
						$end = $row;
					}

					$i++;
					$body[$end+$i] = array(
						'Total', '', '', '', '', $stq, $stv, $tj, $ta
					);
					array_push($header['merge'], 'A'.($end+$i+7).':E'.($end+$i+7).'');
					$header['font']['A'.($end+$i+7).':K'.($end+$i+7).''] = array('bold'=>true);

					$this->m_export_file->to_excel(str_replace(' ', '_', 'Inventory Supplier'), $header, $body);
				} else if($jenis_export=='pdf'){
					for($i=1; $i<=$interval_brg; $i++){
						foreach ($detail_barang[$i] as $row2) {
							if ($row_1 <= 1) {
								if ($row_2 <= 1) {
									$barang = $this->m_crud->get_data("barang, Group2, kel_brg", "ltrim(rtrim(Nama)) Nama, ltrim(rtrim(nm_kel_brg)) nm_kel_brg, ltrim(rtrim(barang.Group2)) Group2, ltrim(rtrim(barang.kel_brg)) kel_brg, (SELECT COUNT(br.Group2) FROM barang br WHERE ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_dept, (SELECT COUNT(br.kel_brg) FROM barang br WHERE ltrim(rtrim(br.kel_brg))=ltrim(rtrim(barang.kel_brg)) AND ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_kel_brg", "ltrim(rtrim(barang.Group2))=ltrim(rtrim(Group2.Kode)) AND ltrim(rtrim(barang.kel_brg))=ltrim(rtrim(kel_brg.kel_brg)) AND ltrim(rtrim(Group1))=ltrim(rtrim('".$id."')) AND ltrim(rtrim(kd_brg))=ltrim(rtrim('".$row2['kd_brg']."'))");
									$row_1 = $barang['row_dept'];
									$row_2 = $barang['row_kel_brg'];
									$result .= '
									<tr>
										<th style="border:1px solid black; text-align: left" colspan="11">' . $barang['Group2'] . '/' . $barang['Nama'] . ' - ' . $barang['kel_brg'] . '/' . $barang['nm_kel_brg'] . '</th>
									</tr>
								';
								} else {
									$row_2 = $row_2 - 1;
								}
							} else {
								if ($row_2 <= 1) {
									$barang = $this->m_crud->get_data("barang, Group2, kel_brg", "ltrim(rtrim(Nama)) Nama, ltrim(rtrim(nm_kel_brg)) nm_kel_brg, ltrim(rtrim(barang.Group2)) Group2, ltrim(rtrim(barang.kel_brg)) kel_brg, (SELECT COUNT(br.Group2) FROM barang br WHERE ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_dept, (SELECT COUNT(br.kel_brg) FROM barang br WHERE ltrim(rtrim(br.kel_brg))=ltrim(rtrim(barang.kel_brg)) AND ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_kel_brg", "ltrim(rtrim(barang.Group2))=ltrim(rtrim(Group2.Kode)) AND ltrim(rtrim(barang.kel_brg))=ltrim(rtrim(kel_brg.kel_brg)) AND ltrim(rtrim(Group1))=ltrim(rtrim('".$id."')) AND ltrim(rtrim(kd_brg))=ltrim(rtrim('".$row2['kd_brg']."'))");
									$row_2 = $barang['row_kel_brg'];
									$result .= '
									<tr>
										<th style="border:1px solid black; text-align: left" colspan="11">' . $barang['Group2'] . '/' . $barang['Nama'] . ' - ' . $barang['kel_brg'] . '/' . $barang['nm_kel_brg'] . '</th>
									</tr>
								';
								} else {
									$row_2 = $row_2 - 1;
								}
								$row_1 = $row_1 - 1;
							}

							$result .= '
							<tr>
								<td style="border:1px solid black">' . $row2['kd_brg'] . '</td>
								<td style="border:1px solid black">' . $row2['nm_brg'] . '</td>
								<td style="border:1px solid black; text-align: right">' . number_format($row2['hrg_beli']) . '</td>
								<td style="border:1px solid black; text-align: right">' . number_format($row2['hrg_jual_1']) . '</td>
								<td style="border:1px solid black; text-align: center">' . (($row2['hrg_beli'] > 0 && $row2['hrg_beli'] < $row2['hrg_jual_1']) ? round((1 - ($row2['hrg_beli'] / $row2['hrg_jual_1'])) * 100, 2) : '0') . '</td>
								<td style="border:1px solid black; text-align: center">' . ($row2['qty_stock'] + 0) . '</td>
								<td style="border:1px solid black; text-align: right">' . number_format($row2['qty_stock'] * $row2['hrg_beli']) . '</td>
								<td style="border:1px solid black; text-align: center">' . ($row2['qty_jual'] + 0) . '</td>
								<td style="border:1px solid black; text-align: center">' . ($row2['qty_alokasi'] + 0) . '</td>
								<td style="border:1px solid black">' . substr($row2['tgl_alokasi'], 0, 10) . '</td>
								<td style="border:1px solid black">' . substr($row2['tgl_jual'], 0, 10) . '</td>
							</tr>
							';
							$stq = $stq + ($row2['qty_stock'] + 0);
							$stv = $stv + ($row2['qty_stock'] * $row2['hrg_beli']);
							$tj = $tj + ($row2['qty_jual'] + 0);
							$ta = $ta + ($row2['qty_alokasi'] + 0);
							$kstq = $kstq + ($row2['qty_stock'] + 0);
							$kstv = $kstv + ($row2['qty_stock'] * $row2['hrg_beli']);
							$ktj = $ktj + ($row2['qty_jual'] + 0);
							$kta = $kta + ($row2['qty_alokasi'] + 0);
							$sstq = $sstq + ($row2['qty_stock'] + 0);
							$sstv = $sstv + ($row2['qty_stock'] * $row2['hrg_beli']);
							$stj = $stj + ($row2['qty_jual'] + 0);
							$sta = $sta + ($row2['qty_alokasi'] + 0);
							if ($row_2 == 1) {
								$result .= '
								<tr>
									<th style="border:1px solid black; text-align: right" colspan="4">TOTAL ' . $barang['Group2'] . '/' . $barang['Nama'] . ' - ' . $barang['kel_brg'] . '/' . $barang['nm_kel_brg'] . '</th>
									<th style="border:1px solid black; text-align: center">' . $kmrg . '</th>
									<th style="border:1px solid black; text-align: center">' . $kstq . '</th>
									<th style="border:1px solid black; text-align: right">' . number_format($kstv) . '</th>
									<th style="border:1px solid black; text-align: center">' . $ktj . '</th>
									<th style="border:1px solid black; text-align: center">' . $kta . '</th>
									<th style="border:1px solid black" colspan="2"></th>
								</tr>
							';
								$kmrg = 0;
								$kstq = 0;
								$kstv = 0;
								$ktj = 0;
								$kta = 0;
							}
							if ($row_1 == 1) {
								$result .= '
								<tr>
									<th style="border:1px solid black; text-align: right" colspan="4">TOTAL ' . $barang['Group2'] . '/' . $barang['Nama'] . '</th>
									<th style="border:1px solid black; text-align: center">' . $smrg . '</th>
									<th style="border:1px solid black; text-align: center">' . $sstq . '</th>
									<th style="border:1px solid black; text-align: right">' . number_format($sstv) . '</th>
									<th style="border:1px solid black; text-align: center">' . $stj . '</th>
									<th style="border:1px solid black; text-align: center">' . $sta . '</th>
									<th style="border:1px solid black" colspan="2"></th>
								</tr>
							';
								$smrg = 0;
								$sstq = 0;
								$sstv = 0;
								$stj = 0;
								$sta = 0;
							}
						}
					}

					$data['mrg'] = $mrg; $data['stq'] = $stq; $data['stv'] = $stv; $data['tj'] = $tj; $data['ta'] = $ta;
					$data['result'] = $result;
					$t_row = 1;
					//$t_row = $t_row + 23;
					$data['row_per_page'] = 30;
					$data['row_one_page'] = 25;
					($action=='download')?($method='D'):($method='I');
					//$method='I';
					$file = str_replace('/', '-', str_replace(' ', '_', 'Laporan Inventory Supplier').'-'.str_replace('/', '-', $data['report']['Kode']));
					$html = $this->load->view('bo/'.$data['content'], $data, true);
					$header = $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Laporan Inventory '.$this->menu_group['as_group1'].'</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
						'<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="17%"></th>
									<th width="2%"></th>
									<th width="30%"></th>

									<th width="10%"></th>
									<th width="10%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Kode '.$this->menu_group['as_group1'].'</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['Kode'].'</td>
									<td></td>
									<td colspan="3"><b>'.$periode.'</b></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Nama '.$this->menu_group['as_group1'].'</b></td>
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
						'left'=>10,'right'=>10,'top'=>35,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
					);
					$this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
				}
            } else if ($s_filter == 'inventory_v2') {
                $array_lokasi = array();
                $tmp_lokasi = json_decode(base64_decode($tmp_lokasi), true);
                for ($i = 0; $i < count($tmp_lokasi); $i++) {
                    array_push($array_lokasi, '\''.$tmp_lokasi[$i].'\'');
                }
                sort($array_lokasi);
                $data['content'] = $view.'pdf_invoice_inventory_v2_'.$function;
                $result = '';

                if (count($array_lokasi)>0) {
                    $q_lokasi = 'Lokasi : '.implode(', ', $array_lokasi);
                }
				
                $where_lokasi = " and lokasi NOT IN ('MUTASI', 'Retur', 'HO') and lokasi <> '' and lokasi is not null";
                if(count($array_lokasi)>0){ $where_lokasi.=" AND lokasi IN (".implode(', ', $array_lokasi).")"; }
                $hrg_jual_1 = (count($array_lokasi)>0)?"isnull((SELECT AVG(hrg_jual_1) FROM barang_hrg WHERE barang=kd_brg AND lokasi IN (".implode(', ', $array_lokasi).")), hrg_jual_1) hrg_jual_1":"hrg_jual_1";
                $qty_jual = "isnull((SELECT SUM(qty) FROM inventory_trx it WHERE it.kd_brg=barang.kd_brg AND tgl BETWEEN '".$tgl_awal." 00:00:00' AND '".$tgl_akhir." 23:59:59'".$where_lokasi."), 0) qty_jual";
                $tgl_jual = "(SELECT TOP 1 tgl FROM inventory_trx it WHERE it.kd_brg=barang.kd_brg AND tgl BETWEEN '".$tgl_awal." 00:00:00' AND '".$tgl_akhir." 23:59:59'".$where_lokasi." ORDER BY tgl DESC) tgl_jual";
                $tgl_alokasi = "(SELECT TOP 1 tgl FROM inventory_mutasi im WHERE im.kd_brg=barang.kd_brg AND tgl BETWEEN '".$tgl_awal." 00:00:00' AND '".$tgl_akhir." 23:59:59'".$where_lokasi." ORDER BY tgl DESC) tgl_alokasi";
                $stock_awal = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE keterangan not like '%Adjustment%' AND Kartu_stock.kd_brg=barang.kd_brg AND left(convert(varchar, tgl, 120), 10)<'".$tgl_awal."' ".$where_lokasi.") ,0) stock_awal";
				$stock_masuk = "ISNULL((SELECT SUM(stock_in) FROM Kartu_stock WHERE keterangan not like '%Adjustment%' AND keterangan not like '%Mutasi%' AND Kartu_stock.kd_brg=barang.kd_brg AND tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59' ".$where_lokasi.") ,0) stock_masuk";
				$qty_retur = "ISNULL((SELECT SUM(stock_out) FROM Kartu_stock WHERE Kartu_stock.kd_brg=barang.kd_brg AND keterangan='Retur Pembelian' AND tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59' ".$where_lokasi."), 0) qty_retur";
				$qty_adjust = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE Kartu_stock.kd_brg=barang.kd_brg AND keterangan like '%Adjustment%' AND tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59' ".$where_lokasi."), 0) qty_adjust";
				$qty_mutasi = "ISNULL((SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE Kartu_stock.kd_brg=barang.kd_brg AND (keterangan like '%Mutasi%' or keterangan like '%Retur Non Approval%') AND tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59' ".$where_lokasi."), 0) qty_mutasi";
				
				$jenis_export = base64_decode($jenis_export);
				if($jenis_export=='xls'){
					$detail_barang = $this->m_crud->read_data("barang, Group2, kel_brg", 
						"ltrim(rtrim(Nama)) Nama, ltrim(rtrim(nm_kel_brg)) nm_kel_brg, ltrim(rtrim(barang.Group2)) Group2, ltrim(rtrim(barang.kel_brg)) kel_brg, 
						(SELECT COUNT(br.Group2) FROM barang br WHERE ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_dept, 
						(SELECT COUNT(br.kel_brg) FROM barang br WHERE ltrim(rtrim(br.kel_brg))=ltrim(rtrim(barang.kel_brg)) AND ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_kel_brg, 
						kd_brg, nm_brg, hrg_beli, ".$hrg_jual_1.", ".$qty_jual.", ".$tgl_jual.", ".$tgl_alokasi.", ".$stock_awal.", ".$stock_masuk.", ".$qty_retur.", ".$qty_adjust.", ".$qty_mutasi, 
						"ltrim(rtrim(barang.Group2))=ltrim(rtrim(Group2.Kode)) AND ltrim(rtrim(barang.kel_brg))=ltrim(rtrim(kel_brg.kel_brg)) AND ltrim(rtrim(Group1))=ltrim(rtrim('".$id."'))", 
						"barang.Group2 ASC, barang.kel_brg ASC, kd_brg ASC", "Nama, nm_kel_brg, barang.Group2, barang.kel_brg, kd_brg, nm_brg, hrg_beli, hrg_jual_1"
					);
                } else if($jenis_export=='pdf'){
					$limit = 1000; 
					$count_barang = $this->m_crud->count_data("barang, Group2, kel_brg", "kd_brg", "ltrim(rtrim(barang.Group2))=ltrim(rtrim(Group2.Kode)) AND ltrim(rtrim(barang.kel_brg))=ltrim(rtrim(kel_brg.kel_brg)) AND ltrim(rtrim(Group1))=ltrim(rtrim('".$id."'))");
					$interval_brg = (int)($count_barang/$limit); if($interval_brg < ($count_barang/$limit)){ $interval_brg++; }
					//echo $interval_brg.'<br/><br/>';
					for($i=1; $i<=$interval_brg; $i++){
						$limit_start = ($i-1)*$limit+1; $limit_end = ($limit*$i);
						//echo 'start:'.$limit_start.' - end:'.$limit_end.'<br/><br/>';
						$detail_barang[$i] = $this->m_crud->select_limit("barang, Group2, kel_brg", 
							"kd_brg, nm_brg, hrg_beli, ".$hrg_jual_1.", ".$qty_jual.", ".$tgl_jual.", ".$tgl_alokasi.", ".$stock_awal.", ".$stock_masuk.", ".$qty_retur.", ".$qty_adjust.", ".$qty_mutasi, 
							"ltrim(rtrim(barang.Group2))=ltrim(rtrim(Group2.Kode)) AND ltrim(rtrim(barang.kel_brg))=ltrim(rtrim(kel_brg.kel_brg)) AND ltrim(rtrim(Group1))=ltrim(rtrim('".$id."'))", 
							"barang.Group2 ASC, barang.kel_brg ASC, kd_brg ASC", "Nama, nm_kel_brg, barang.Group2, barang.kel_brg, kd_brg, nm_brg, hrg_beli, hrg_jual_1", 
							$limit_start, $limit_end
						);
					} 
				}
				
				$get_nama_supplier = $this->m_crud->get_data("Supplier", "nama", "kode='".$id."'");

                $row_1 = 1;
                $row_2 = 1;
				$sstk_awl=0; $sstk_msk=0; $sjual=0; $srtr=0; $sadj=0; $smts=0; $sstk_akr=0;
				$kstk_awl=0; $kstk_msk=0; $kjual=0; $krtr=0; $kadj=0; $kmts=0; $kstk_akr=0;
				$stk_awl=0; $stk_msk=0; $jual=0; $rtr=0; $adj=0; $mts=0; $stk_akr=0;
				
				if($jenis_export=='xls'){
					$baca = $detail_barang;
					$header = array(
						'merge' 	=> array('A1:M1','A2:M2','A3:E3','F3:M3'),
						'auto_size' => true,
						'font' 		=> array(
							'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
							'A3:M3' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>10, 'name'=>'Verdana'),
							'A5:M6' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
						),
						'alignment' => array(
							'A1:A2' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
							'A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
							'E3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),
							'A5:M6' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
						),
						'1' => array('A' => $data['site']->title),
						'2' => array('A' => 'Inventory Supplier ('.$get_nama_supplier['nama'].')'),
						'3' => array('A' => 'Periode : '.$tgl_awal.' - '.$tgl_akhir, 'F' => str_replace("'", "", $q_lokasi)),
						'5' => array('A'=>'Sub Dept - Kel Brg'),
						'6' => array(
							'A'=>'Kd Brg', 'B'=>'Nm Brg', 'C'=>'Hrg Bl', 'D'=>'Hrg Jl', 'E'=>'Stk Awl', 'F'=>'Stk Msk', 'G'=>'Jual', 'H'=>'Rtr', 'I'=>'Adj', 'J'=>'Mts', 'K'=>'Stk Akr', 'L'=>'Lst Alk', 'M'=>'Lst Jl'
						)
					);

					$end = 0;
					$i = 0;
					foreach($baca as $row => $value){

						if ($row_1 <= 1) {
							$row_1 = $value['row_dept'];
							if ($row_2 <= 1) {
								$row_2 = $value['row_kel_brg'];
								$body[$row+$i] = array(
									$value['Group2'] . '/' . $value['Nama'] . ' - ' . $value['kel_brg']
								);
								array_push($header['merge'], 'A'.($row+$i+7).':M'.($row+$i+7).'');
								$header['font']['A'.($row+$i+7).':M'.($row+$i+7).''] = array('bold'=>true);
								$i++;
							} else {
								$row_2 = $row_2 - 1;
							}
						} else {
							if ($row_2 <= 1) {
								$row_2 = $value['row_kel_brg'];
								$body[$row+$i] = array(
									$value['Group2'] . '/' . $value['Nama'] . ' - ' . $value['kel_brg']
								);
								array_push($header['merge'], 'A'.($row+$i+7).':M'.($row+$i+7).'');
								$header['font']['A'.($row+$i+7).':M'.($row+$i+7).''] = array('bold'=>true);
								$i++;
							} else {
								$row_2 = $row_2 - 1;
							}
							$row_1 = $row_1 - 1;
						}
						
						$stock_akhir = (int)$value['stock_awal'] + (int)$value['stock_masuk'] - (int)$value['qty_jual'] - (int)$value['qty_retur'] + (int)$value['qty_adjust'] + (int)$value['qty_mutasi'];
						$body[$row+$i] = array(
							$value['kd_brg'], $value['nm_brg'], $value['hrg_beli'], $value['hrg_jual_1'], (int)$value['stock_awal'], (int)$value['stock_masuk'], (int)$value['qty_jual'], (int)$value['qty_retur'], (int)$value['qty_adjust'], (int)$value['qty_mutasi'], (int)$stock_akhir, $value['tgl_alokasi']!=null?substr($value['tgl_alokasi'],0,10):'', $value['tgl_jual']!=null?substr($value['tgl_jual'],0,10):''
						);

						$stk_awl = $stk_awl + ($value['stock_awal'] + 0);
						$kstk_awl = $kstk_awl + ($value['stock_awal'] + 0);
						$sstk_awl = $sstk_awl + ($value['stock_awal'] + 0);
						$stk_msk = $stk_msk + ($value['stock_masuk'] + 0);
						$kstk_msk = $kstk_msk + ($value['stock_masuk'] + 0);
						$sstk_msk = $sstk_msk + ($value['stock_masuk'] + 0);
						$jual = $jual + ($value['qty_jual'] + 0);
						$kjual = $kjual + ($value['qty_jual'] + 0);
						$sjual = $sjual + ($value['qty_jual'] + 0);
						$rtr = $rtr + ($value['qty_retur'] + 0);
						$krtr = $krtr + ($value['qty_retur'] + 0);
						$srtr = $srtr + ($value['qty_retur'] + 0);
						$adj = $adj + ($value['qty_adjust'] + 0);
						$kadj = $kadj + ($value['qty_adjust'] + 0);
						$sadj = $sadj + ($value['qty_adjust'] + 0);
						$mts = $mts + ($value['qty_mutasi'] + 0);
						$kmts = $kmts + ($value['qty_mutasi'] + 0);
						$smts = $smts + ($value['qty_mutasi'] + 0);
						$stk_akr = $stk_akr + ($stock_akhir + 0);
						$kstk_akr = $kstk_akr + ($stock_akhir + 0);
						$sstk_akr = $sstk_akr + ($stock_akhir + 0);
						
						if ($row_2 == 1) {
							$i++;
							$body[$row+$i] = array(
								'Total '.$value['Group2'] . '/' . $value['Nama'] . ' - ' . $value['kel_brg'] . '/' . $value['nm_kel_brg'], '', '', '', $kstk_awl, $kstk_msk, $kjual, $krtr, $kadj, $kmts, $kstk_akr
							);
							array_push($header['merge'], 'A'.($row+$i+7).':D'.($row+$i+7).'');
							$header['font']['A'.($row+$i+7).':M'.($row+$i+7).''] = array('bold'=>true);
							
							$kstk_awl = 0;
							$kstk_msk = 0;
							$kjual = 0;
							$krtr = 0;
							$kadj = 0;
							$kmts = 0;
							$kstk_akr = 0;
						}

						if ($row_1 == 1) {
							$i++;
							$body[$row+$i] = array(
								'Total '.$value['Group2'] . '/' . $value['Nama'], '', '', '', $sstk_awl, $sstk_msk, $sjual, $srtr, $sadj, $smts, $sstk_akr
							);
							array_push($header['merge'], 'A'.($row+$i+7).':D'.($row+$i+7).'');
							$header['font']['A'.($row+$i+7).':M'.($row+$i+7).''] = array('bold'=>true);

							$sstk_awl = 0;
							$sstk_msk = 0;
							$sjual = 0;
							$srtr = 0;
							$sadj = 0;
							$smts = 0;
							$sstk_akr = 0;
						}
						$end = $row;
					}

					$i++;
					$body[$end+$i] = array(
						'Total', '', '', '', $stk_awl, $stk_msk, $jual, $rtr, $adj, $smts, $stk_akr
					);
					array_push($header['merge'], 'A'.($end+$i+7).':D'.($end+$i+7).'');
					$header['font']['A'.($end+$i+7).':M'.($end+$i+7).''] = array('bold'=>true);

					$this->m_export_file->to_excel(str_replace(' ', '_', 'Inventory Supplier'), $header, $body);
				} else if($jenis_export=='pdf'){
					for($i=1; $i<=$interval_brg; $i++){
						foreach ($detail_barang[$i] as $row2) {
							if ($row_1 <= 1) {
								if ($row_2 <= 1) {
									$barang = $this->m_crud->get_data("barang, Group2, kel_brg", "ltrim(rtrim(Nama)) Nama, ltrim(rtrim(nm_kel_brg)) nm_kel_brg, ltrim(rtrim(barang.Group2)) Group2, ltrim(rtrim(barang.kel_brg)) kel_brg, (SELECT COUNT(br.Group2) FROM barang br WHERE ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_dept, (SELECT COUNT(br.kel_brg) FROM barang br WHERE ltrim(rtrim(br.kel_brg))=ltrim(rtrim(barang.kel_brg)) AND ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_kel_brg", "ltrim(rtrim(barang.Group2))=ltrim(rtrim(Group2.Kode)) AND ltrim(rtrim(barang.kel_brg))=ltrim(rtrim(kel_brg.kel_brg)) AND ltrim(rtrim(Group1))=ltrim(rtrim('".$id."')) AND ltrim(rtrim(kd_brg))=ltrim(rtrim('".$row2['kd_brg']."'))");
									$row_1 = $barang['row_dept'];
									$row_2 = $barang['row_kel_brg'];
									$result .= '
									<tr>
										<th style="border:1px solid black; text-align: left" colspan="13">' . $barang['Group2'] . '/' . $barang['Nama'] . ' - ' . $barang['kel_brg'] . '/' . $barang['nm_kel_brg'] . '</th>
									</tr>
								';
								} else {
									$row_2 = $row_2 - 1;
								}
							} else {
								if ($row_2 <= 1) {
									$barang = $this->m_crud->get_data("barang, Group2, kel_brg", "ltrim(rtrim(Nama)) Nama, ltrim(rtrim(nm_kel_brg)) nm_kel_brg, ltrim(rtrim(barang.Group2)) Group2, ltrim(rtrim(barang.kel_brg)) kel_brg, (SELECT COUNT(br.Group2) FROM barang br WHERE ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_dept, (SELECT COUNT(br.kel_brg) FROM barang br WHERE ltrim(rtrim(br.kel_brg))=ltrim(rtrim(barang.kel_brg)) AND ltrim(rtrim(br.Group2))=ltrim(rtrim(barang.Group2)) AND ltrim(rtrim(br.Group1))='".$id."') row_kel_brg", "ltrim(rtrim(barang.Group2))=ltrim(rtrim(Group2.Kode)) AND ltrim(rtrim(barang.kel_brg))=ltrim(rtrim(kel_brg.kel_brg)) AND ltrim(rtrim(Group1))=ltrim(rtrim('".$id."')) AND ltrim(rtrim(kd_brg))=ltrim(rtrim('".$row2['kd_brg']."'))");
									$row_2 = $barang['row_kel_brg'];
									$result .= '
									<tr>
										<th style="border:1px solid black; text-align: left" colspan="13">' . $barang['Group2'] . '/' . $barang['Nama'] . ' - ' . $barang['kel_brg'] . '/' . $barang['nm_kel_brg'] . '</th>
									</tr>
								';
								} else {
									$row_2 = $row_2 - 1;
								}
								$row_1 = $row_1 - 1;
							}

							$stock_akhir = (int)$row2['stock_awal'] + (int)$row2['stock_masuk'] - (int)$row2['qty_jual'] - (int)$row2['qty_retur'] + (int)$row2['qty_adjust'] + (int)$row2['qty_mutasi'];
							$result .= '
							<tr>
								<td style="border:1px solid black">' . $row2['kd_brg'] . '</td>
								<td style="border:1px solid black">' . $row2['nm_brg'] . '</td>
								<td style="border:1px solid black; text-align: right">' . number_format($row2['hrg_beli']) . '</td>
								<td style="border:1px solid black; text-align: right">' . number_format($row2['hrg_jual_1']) . '</td>
								<td style="border:1px solid black; text-align: center">' . ($row2['stock_awal'] + 0) . '</td>
								<td style="border:1px solid black; text-align: center">' . ($row2['stock_masuk'] + 0) . '</td>
								<td style="border:1px solid black; text-align: center">' . ($row2['qty_jual'] + 0) . '</td>
								<td style="border:1px solid black; text-align: center">' . ($row2['qty_retur'] + 0) . '</td>
								<td style="border:1px solid black; text-align: center">' . ($row2['qty_adjust'] + 0) . '</td>
								<td style="border:1px solid black; text-align: center">' . ($row2['qty_mutasi'] + 0) . '</td>
								<td style="border:1px solid black; text-align: center">' . ($stock_akhir + 0) . '</td>
								<td style="border:1px solid black">' . substr($row2['tgl_alokasi'], 2, 8) . '</td>
								<td style="border:1px solid black">' . substr($row2['tgl_jual'], 2, 8) . '</td>
							</tr>
							';
							$stk_awl = $stk_awl + ($row2['stock_awal'] + 0);
							$kstk_awl = $kstk_awl + ($row2['stock_awal'] + 0);
							$sstk_awl = $sstk_awl + ($row2['stock_awal'] + 0);
							$stk_msk = $stk_msk + ($row2['stock_masuk'] + 0);
							$kstk_msk = $kstk_msk + ($row2['stock_masuk'] + 0);
							$sstk_msk = $sstk_msk + ($row2['stock_masuk'] + 0);
							$jual = $jual + ($row2['qty_jual'] + 0);
							$kjual = $kjual + ($row2['qty_jual'] + 0);
							$sjual = $sjual + ($row2['qty_jual'] + 0);
							$rtr = $rtr + ($row2['qty_retur'] + 0);
							$krtr = $krtr + ($row2['qty_retur'] + 0);
							$srtr = $srtr + ($row2['qty_retur'] + 0);
							$adj = $adj + ($row2['qty_adjust'] + 0);
							$kadj = $kadj + ($row2['qty_adjust'] + 0);
							$sadj = $sadj + ($row2['qty_adjust'] + 0);
							$mts = $mts + ($row2['qty_mutasi'] + 0);
							$kmts = $kmts + ($row2['qty_mutasi'] + 0);
							$smts = $smts + ($row2['qty_mutasi'] + 0);
							$stk_akr = $stk_akr + ($stock_akhir + 0);
							$kstk_akr = $kstk_akr + ($stock_akhir + 0);
							$sstk_akr = $sstk_akr + ($stock_akhir + 0);
							if ($row_2 == 1) {
								$result .= '
								<tr>
									<th style="border:1px solid black; text-align: right" colspan="4">TOTAL ' . $barang['Group2'] . '/' . $barang['Nama'] . ' - ' . $barang['kel_brg'] . '/' . $barang['nm_kel_brg'] . '</th>
									<th style="border:1px solid black; text-align: center">' . $kstk_awl . '</th>
									<th style="border:1px solid black; text-align: center">' . $kstk_msk . '</th>
									<th style="border:1px solid black; text-align: center">' . $kjual . '</th>
									<th style="border:1px solid black; text-align: center">' . $krtr . '</th>
									<th style="border:1px solid black; text-align: center">' . $kadj . '</th>
									<th style="border:1px solid black; text-align: center">' . $kmts . '</th>
									<th style="border:1px solid black; text-align: center">' . $kstk_akr . '</th>
									<th style="border:1px solid black" colspan="2"></th>
								</tr>
							';
								$kstk_awl = 0;
								$kstk_msk = 0;
								$kjual = 0;
								$krtr = 0;
								$kadj = 0;
								$kmts = 0;
								$kstk_akr = 0;
							}
							if ($row_1 == 1) {
								$result .= '
								<tr>
									<th style="border:1px solid black; text-align: right" colspan="4">TOTAL ' . $barang['Group2'] . '/' . $barang['Nama'] . '</th>
									<th style="border:1px solid black; text-align: center">' . $sstk_awl . '</th>
									<th style="border:1px solid black; text-align: center">' . $sstk_msk . '</th>
									<th style="border:1px solid black; text-align: center">' . $sjual . '</th>
									<th style="border:1px solid black; text-align: center">' . $srtr . '</th>
									<th style="border:1px solid black; text-align: center">' . $sadj . '</th>
									<th style="border:1px solid black; text-align: center">' . $smts . '</th>
									<th style="border:1px solid black; text-align: center">' . $sstk_akr . '</th>
									<th style="border:1px solid black" colspan="2"></th>
								</tr>
							';
								$sstk_awl = 0;
								$sstk_msk = 0;
								$sjual = 0;
								$srtr = 0;
								$sadj = 0;
								$smts = 0;
								$sstk_akr = 0;
							}
						}
					}
					
					$data['stk_awl']=$stk_awl; $data['stk_msk']=$stk_msk; $data['jual']=$jual; $data['rtr']=$rtr; $data['adj']=$adj; $data['mts']=$mts; $data['stk_akr']=$stk_akr;  
					
					$data['result'] = $result;
					$t_row = 1;
					//$t_row = $t_row + 23;
					$data['row_per_page'] = 30;
					$data['row_one_page'] = 25;
					($action=='download')?($method='D'):($method='I');
					//$method='I';
					$file = str_replace('/', '-', str_replace(' ', '_', 'Laporan Inventory Supplier').'-'.str_replace('/', '-', $data['report']['Kode']));
					$html = $this->load->view('bo/'.$data['content'], $data, true);
					$header = $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Laporan Inventory '.$this->menu_group['as_group1'].'</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
						'<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="17%"></th>
									<th width="2%"></th>
									<th width="30%"></th>

									<th width="10%"></th>
									<th width="10%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Kode '.$this->menu_group['as_group1'].'</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['Kode'].'</td>
									<td></td>
									<td colspan="3"><b>'.$periode.'</b></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Nama '.$this->menu_group['as_group1'].'</b></td>
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
						'left'=>10,'right'=>10,'top'=>35,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
					);
					$this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
				}
            } else {
                $q_tgl = "AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";
                $where = ""; (isset($lokasi) && $lokasi!='')?$where.=" AND mt.Lokasi='".$lokasi."'":"";
                $where_stock = "kd_brg=br.kd_brg AND lokasi NOT IN ('MUTASI', 'Retur') "; ($lokasi==null)?"":$where_stock.=" AND Lokasi='".$lokasi."'";
                $stock_awal = "isnull((select sum(saldo_awal + stock_in - stock_out) from kartu_stock where ".$where_stock." and tgl < '".$tgl_awal." 00:00:00'),0) as stock_awal";
                $stock_periode = "isnull((select sum(stock_in-stock_out) from kartu_stock where ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_periode";
                $stock_periode2 = ", isnull((select sum(stock_in-stock_out) from kartu_stock where ".$where_stock." and tgl > '".$tgl_awal." 00:00:00'), 0) as stock_periode";
                $retur = "isnull((select sum(dtrx.qty) from Det_Trx dtrx, Master_Trx mtrx where dtrx.kd_brg=br.kd_brg and dtrx.qty < 0 and dtrx.kd_trx=mtrx.kd_trx and mtrx.tgl >= '".$tgl_awal." 00:00:00' and mtrx.tgl <= '".$tgl_akhir." 23:59:59'),0) as retur";
                $jual = "isnull((select sum(dtrx.qty) from Det_Trx dtrx, Master_Trx mtrx where dtrx.kd_brg=br.kd_brg and dtrx.qty > 0 and dtrx.kd_trx=mtrx.kd_trx and mtrx.tgl >= '".$tgl_awal." 00:00:00' and mtrx.tgl <= '".$tgl_akhir." 23:59:59'),0) as jual";
                $data['report_detail'] = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "SUM(dt.dis_persen) diskon_item, AVG(dt.hrg_beli) hrg_beli, AVG(dt.hrg_jual) hrg_jual, br.kd_brg, br.Deskripsi, br.nm_brg, br.barcode, br.satuan, ".$stock_awal.",".$stock_periode2.",".$retur.",".$jual."", "mt.HR = 'S' AND dt.qty > 0 AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND br.Group1 = '".$id."' ".$q_tgl." ".$where, null, "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan");
                $data['content'] = $view.'pdf_invoice_'.$function;

                $t_row = count($data['report_detail']);
                //$t_row = $t_row + 23;
                $data['row_per_page'] = 30;
                $data['row_one_page'] = 25;
                ($action=='download')?($method='D'):($method='I');
                //$method='I';
                $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['Kode']));
                $html = $this->load->view('bo/'.$data['content'], $data, true);
                $header = $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Detail Laporan Penjualan By '.$this->menu_group['as_group1'].'</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                    '<div style="margin-bottom: 10px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="17%"></th>
                                <th width="2%"></th>
                                <th width="30%"></th>
                                
                                <th width="10%"></th>
                                <th width="10%"></th>
                                <th width="2%"></th>
                                <th width="27%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>Kode '.$this->menu_group['as_group1'].'</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['Kode'].'</td>
                                <td></td>
                                <td colspan="3"><b>'.$periode.'</b></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Nama '.$this->menu_group['as_group1'].'</b></td>
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
                    'left'=>10,'right'=>10,'top'=>35,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
                );
                $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
            }
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function arsip_return_penjualan($action = null, $id = null) {
        $this->access_denied(153);
        $data = $this->data;
        $function = 'arsip_return_penjualan';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Arsip Return Penjualan';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = 'dt.qty < 0';
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
            $where .= "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        }else{
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi = '".$lokasi."'";
        } else {
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi in (".$this->lokasi_in.")";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mt.kd_trx like '%".$search."%' or dt.kd_brg like '%".$search."%' or mt.Lokasi like '%".$search."%' or mt.kd_kasir like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_over("Master_Trx mt, Det_Trx dt, Customer cs", 'mt.kd_trx', "mt.kd_trx=dt.kd_trx AND mt.kd_cust=cs.kd_cust ".($where==null?'':' AND '.$where), null, "mt.kd_trx");
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

        $data['report'] = $this->m_crud->select_limit('Master_Trx mt, Det_Trx dt, Customer cs', "mt.kd_trx ,mt.tgl, mt.kd_kasir, cs.Nama, SUM(dt.qty * dt.hrg_jual * -1) nilai_retur, SUM(dt.dis_persen) diskon_item, mt.Lokasi", "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND mt.kd_cust=cs.kd_cust".($where==null?' ' : ' AND '.$where), 'mt.tgl desc', "mt.kd_kasir, mt.kd_trx, mt.tgl, cs.Nama, mt.Lokasi", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        $total = $this->m_crud->read_data('Master_Trx mt, Det_Trx dt, Customer cs', "mt.kd_trx ,mt.tgl, mt.kd_kasir, cs.Nama, SUM(dt.qty * dt.hrg_jual * -1) nilai_retur, SUM(dt.dis_persen) diskon_item, mt.Lokasi", "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND mt.kd_cust=cs.kd_cust".($where==null?' ' : ' AND '.$where), 'mt.tgl desc', "mt.kd_kasir, mt.kd_trx, mt.tgl, cs.Nama, mt.Lokasi");

        $nr = 0;
         foreach ($total as $row) {
             $nr = $nr + ($row['nilai_retur']-$row['diskon_item']);
         }

         $data['tnr'] = $nr;

        if(isset($_POST['to_excel'])){
            $baca = $data['report'];
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
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'No. Transaksi', 'C'=>'Customer', 'D'=>'Lokasi', 'E'=>'Operator', 'F'=>'Nilai Return'
                )
            );

            $end = 0;
            foreach($baca as $row => $value){
                $end++;
                $body[$row] = array(
                    substr($value['tgl'], 0, 10), $value['kd_trx'], $value['Nama'], $value['Lokasi'], $value['kd_kasir'], $value['nilai_retur']
                );
            }

            $body[$end] = array('TOTAL','','','','',$nr);
            array_push($header['merge'], 'A'.($end+6).':E'.($end+6));
            $header['font']['A'.($end+6).':F'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('Master_Trx', "tgl, kd_trx, Lokasi, kd_kasir", "kd_trx = '".$id."'");
            $data['report_detail'] = $this->m_crud->read_data('Det_Trx dt, barang br, kel_brg kb', 'br.kd_brg, br.barcode, br.nm_brg, kb.nm_kel_brg, br.satuan, dt.qty, dt.hrg_jual, dt.dis_persen', "dt.kd_brg = br.kd_brg AND br.kel_brg = kb.kel_brg AND dt.qty < 0 AND dt.kd_trx = '".$id."'");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['kd_trx']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Arsip Retur Penjualan</b></h3></div>
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
									<td>'.substr($data['report']['tgl'], 0, 10).'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td><b>No. Transaksi</b></td>
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
									<td>'.$data['report']['Lokasi'].'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Operator</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['kd_kasir'].'</td>
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
                'left'=>10,'right'=>10,'top'=>60,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function penjualan_by_kel_barang($action = null, $id = null) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
		
		$this->access_denied(154);
        $data = $this->data;
        $function = 'penjualan_by_kel_barang';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Penjualan By Kelompok Barang';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "mt.HR = 'S' AND dt.qty > 0";
        $periode = 'Periode : None'; $q_lokasi = 'Semua Lokasi';

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
        }else{
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi = '".$lokasi."'"; $q_lokasi = "Lokasi : ".$lokasi."";
        } else {
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi in (".$this->lokasi_in.")";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(kb.kel_brg like '%".$search."%' or kb.nm_kel_brg like '%".$search."%')"; }
        $join = array(array("type"=>"left","table"=>"Det_Trx dt"),array("type"=>"left","table"=>"barang br"),array("type"=>"left","table"=>"kel_brg kb"));
        $on = array("dt.kd_trx=mt.kd_trx","br.kd_brg=dt.kd_brg","br.kel_brg=kb.kel_brg");
		if(($action!='download'&&$action!='print')){
			$page = ($id==null?1:$id);
			$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_join_data("Master_Trx mt", 'kb.kel_brg',$join,$on, ($where==null)?null:$where, null, "kb.kel_brg");
//            $config['total_rows'] = $this->m_crud->count_data_over("Master_Trx mt, Det_Trx dt, barang br, kel_brg kb", 'kb.kel_brg', "mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND br.kel_brg=kb.kel_brg ".($where==null?'':' AND '.$where), null, "kb.kel_brg");
			$config['per_page'] = 10;
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

			$data['periode'] = $periode;
			$data['q_lokasi'] = $q_lokasi;
			$data['lokasi'] = $lokasi;
			$data['tgl_awal'] = $tgl_awal;
			$data['tgl_akhir'] = $tgl_akhir;

			$data['report'] = $this->m_crud->select_limit_join(
			    'Master_Trx mt',
                "kb.kel_brg, kb.nm_kel_brg, SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service",
                $join,$on,
                ($where==null)?null:$where, 'kb.kel_brg ASC', "kb.kel_brg, kb.nm_kel_brg", ($page-1)*$config['per_page']+1, ($config['per_page']*$page)
            );

			$row = $this->m_crud->get_join_data(
			    'Master_Trx mt',
                "SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service",
                $join,$on,($where==null)?null:$where
            );

			$tqt = (int)$row['qty_jual'];
			$tgs = (float)$row['gross_sales'];
			$tdi = (float)$row['diskon_item'];
            $tns = ((float)$row['gross_sales']-(float)$row['diskon_item']);
            $tax = (float)$row['tax'];
            $srv = (float)$row['service'];

            $data['tqt'] = $tqt;
			$data['tgs'] = $tgs;
			$data['tdi'] = $tdi;
			$data['tns'] = $tns;
			$data['ttax'] = $tax;
			$data['tsrv'] = $srv;
		}
        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->join_data(
                'Master_Trx mt',
                "kb.kel_brg, kb.nm_kel_brg, SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service",
                $join,$on,
                ($where==null)?null:$where, 'kb.kel_brg ASC', "kb.kel_brg, kb.nm_kel_brg"
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
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Kode', 'B'=>'Kelompok', 'C'=>'Qty Terjual', 'D'=>'Sub Total', 'E'=>'Diskon Item', 'F'=>'Net Sales', 'G'=>'Tax', 'H'=>'Service', 'I'=>'Gross Sales'
                )
            );

            $end = 0;
            $qt = 0; $gs = 0; $di = 0; $ns = 0; $tax = 0; $srv = 0;
            foreach($baca as $row => $value){
                $end++;
                $body[$row] = array(
                    $value['kel_brg'], $value['nm_kel_brg'], ($value['qty_jual']+0), $value['gross_sales'], $value['diskon_item'], ($value['gross_sales']-$value['diskon_item']), $value['tax'], $value['service'], ($value['gross_sales']-$value['diskon_item']+$value['tax']+$value['service'])
                );

                $qt = $qt + (int)$value['qty_jual'];
                $gs = $gs + (float)$value['gross_sales'];
                $di = $di + (float)$value['diskon_item'];
                $ns = $ns + ((float)$value['gross_sales']-(float)$value['diskon_item']);
                $tax = $tax + ((float)$value['tax']);
                $srv = $srv + ((float)$value['service']);
            }

            $body[$end] = array('TOTAL','',$qt,$gs,$di,$ns,$tax,$srv,$ns+$tax+$srv);
            array_push($header['merge'], 'A'.($end+6).':B'.($end+6));
            $header['font']['A'.($end+6).':I'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', 'penjualan_by_kelompok_barang'), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('kel_brg', "kel_brg, nm_kel_brg", "kel_brg = '".$id."'");

            $where = " AND mt.HR = 'S' AND dt.qty > 0 AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";
            ($lokasi==null)?null:$where.=" AND mt.Lokasi='".$lokasi."'";
            $data['report_detail'] = $this->m_crud->join_data('Det_Trx dt', 
				'mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.satuan, dt.qty, dt.hrg_jual, dt.dis_persen', 
				array("Master_Trx mt","barang br"),
				array("dt.kd_trx=mt.kd_trx","dt.kd_brg = br.kd_brg"),
				"br.kel_brg = '".$id."' ".$where."", "mt.kd_trx ASC"
			);

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 30;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['kd_trx']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Detail Laporan Penjualan By Kelompok Barang</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="20%"></th>
									<th width="2%"></th>
									<th width="30%"></th>
									
									<th width="5%"></th>
									<th width="12%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Kode Kel. Barang</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['kel_brg'].'</td>
									<td></td>
									<td><b>Periode</b></td>
									<td><b>:</b></td>
									<td>'.$tgl_awal.' - '.$tgl_akhir.'</td>
								</tr>
								<tr>
									<td></td>
									<td><b>Nama Kel. Barang</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['nm_kel_brg'].'</td>
									<td></td>
									<td><b>Lokasi</b></td>
									<td><b>:</b></td>
									<td>'.($lokasi==null?"Semua Lokasi":$lokasi).'</td>
								</tr>
							</tbody>
						</table>
					</div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>60,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function detail_by_kel_brg($kode, $tgl, $lokasi=null) {
        $kode = base64_decode($kode);
        $tgl = base64_decode($tgl);
        $explode_date = explode(' - ', $tgl);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);
        $where = " AND mt.HR = 'S' AND dt.qty > 0 AND mt.tgl BETWEEN '".$tgl_awal." 00:00:00' and '".$tgl_akhir." 23:59:59'";

        $ket_lokasi = 'Semua Lokasi';
        $periode = $tgl_awal.' - '.$tgl_akhir;
        if ($lokasi!=null) {
            $lokasi = base64_decode($lokasi);
            $where .= " AND mt.Lokasi='" . $lokasi . "'";
            $ket_lokasi = $lokasi;
        }

        $get_nama = $this->m_crud->get_data("kel_brg", "nm_kel_brg", "kel_brg='".$kode."'");

        $detail = $this->m_crud->read_data('Det_Trx dt, barang br, Master_Trx mt', 'mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.satuan, dt.qty, dt.tax, dt.service, dt.hrg_jual, dt.dis_persen, (SELECT COUNT(kd_trx) FROM Det_Trx, barang WHERE kd_trx=mt.kd_trx AND barang.kd_brg=Det_Trx.kd_brg AND barang.kel_brg=\''.$kode.'\') baris', "dt.kd_trx=mt.kd_trx AND dt.kd_brg = br.kd_brg AND br.kel_brg = '".$kode."' ".$where."", "mt.kd_trx ASC");
        $rowspan = 1;
        $list = '';
        $no = 1;
        $total = 0;
        $tax = 0;
        $service = 0;

        foreach ($detail as $rows){
            $sub_total = ($rows['qty'] * $rows['hrg_jual']) - $rows['dis_persen'];
            $list .= '<tr>';
            if ($rowspan <= 1) {
                $list .= '
                    <td rowspan="'.$rows['baris'].'">'.$no.'</td>
                    <td rowspan="'.$rows['baris'].'">'.$rows['kd_trx'].'</td>
                    <td rowspan="'.$rows['baris'].'">'.substr($rows['tgl'],0,10).'</td>
                ';
                $rowspan = $rows['baris'];
            }else {
                $rowspan = $rowspan - 1;
                $no--;
            }
            $list .= '
                    <td>'.$rows['kd_brg'].'</td>
                    <td>'.$rows['barcode'].'</td>
                    <td>'.$rows['nm_brg'].'</td>
                    <td>'.($rows['qty'] + 0).' '.$rows['satuan'].'</td>
                    <td style="text-align:right;">'.number_format($rows['hrg_jual']).'</td>
                    <td style="text-align:right;">'.number_format($rows['dis_persen']).'</td>
                    <td style="text-align:right;">'.number_format($sub_total).'</td>
                    <td style="text-align:right;">'.number_format($rows['tax']).'</td>
                    <td style="text-align:right;">'.number_format($rows['service']).'</td>
                    <td style="text-align:right;">'.number_format($sub_total+$rows['tax']+$rows['service']).'</td>
                </tr>
            ';
            $no++;
            $total = $total + $sub_total;
            $tax = $tax + $rows['tax'];
            $service = $service + $rows['service'];
        }
        $list .= '<tr><th colspan="9">Total</th><th style="text-align: right">'.number_format($total).'</th><th style="text-align: right">'.number_format($tax).'</th><th style="text-align: right">'.number_format($service).'</th><th style="text-align: right">'.number_format($total+$tax+$service).'</th></tr>';

        echo json_encode(array('list' => $list, 'det' => array('kode'=>$kode, 'nama'=>$get_nama['nm_kel_brg'], 'lokasi'=>$ket_lokasi, 'periode'=>$periode)));
    }

    public function omset_penjualan($action = null, $id = null) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $this->access_denied(154);
        $data = $this->data;
        $function = 'omset_penjualan';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Omset Penjualan';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('lokasi' => $_POST['lokasi'], 'field-date' => $_POST['field-date']));
        }

        $lokasi = $this->session->search['lokasi']; $periode = $this->session->search['field-date'];

        $periode_date = '';
        $explode_date = explode(' - ', $periode);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        if (isset($periode) && $periode != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rt.tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }

        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="rt.Lokasi = '".$lokasi."'"; $q_lokasi = "Lokasi : ".$lokasi."";
        } else {
            ($where==null)?null:$where.=" and "; $where.="rt.Lokasi in (".$this->lokasi_in.")";
        }

        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->read_data('report_trx rt', "LEFT(CONVERT(VARCHAR, rt.tgl, 120), 10) tanggal, SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null?null:$where), 'rt.tgl DESC', "rt.tgl");
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
                '3' => array('A' => $lokasi),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Sub Total', 'C'=>'Diskon Item', 'D'=>'Diskon Transaksi', 'E'=>'Net Sales', 'F'=>'Tax', 'G'=>'Service', 'H'=>'Gross Sales'
                )
            );

            $end = 0;
            $di = 0; $dt = 0; $gs = 0; $tax = 0; $srv = 0; $tns = 0;
            foreach($baca as $row => $value){
                $end++;
                $ns = $value['gross_sales']-$value['diskon_item']-$value['diskon_trx']+0;
                $body[$row] = array(
                    $value['tanggal'], $value['gross_sales']+0, $value['diskon_item']+0, $value['diskon_trx']+0, $ns, $value['tax'], $value['service'], $ns+$value['tax']+$value['service']
                );
                $gs = $gs + (float)$value['gross_sales'];
                $di = $di + (float)$value['diskon_item'];
                $dt = $dt + (float)$value['diskon_trx'];
                $tax = $tax + (float)$value['tax'];
                $srv = $srv + (float)$value['service'];
                $tns = $tns + $ns;
            }

            $body[$end] = array('TOTAL',$gs,$di,$dt,$tns,$tax,$srv,$tns+$tax+$srv);
            $header['font']['A'.($end+6).':H'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['page']), $header, $body);
        } else {
            $page = ($id==null?1:$id);
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data_over("report_trx rt", 'rt.tgl', ($where==null?null:$where), "rt.tgl DESC", "rt.tgl");
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

            //$data['report'] = $this->m_crud->select_limit('Master_Trx mt, Det_Trx dt', "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) tanggal ,SUM(dt.qty*dt.hrg_jual)-SUM(dt.dis_persen) omset, SUM(mt.dis_rp) diskon_nominal", ($where==null?null:$where), 'mt.tgl DESC', "mt.tgl", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
            //$detail = $this->m_crud->read_data('Master_Trx mt, Det_Trx dt', "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) tanggal ,SUM(dt.qty*dt.hrg_jual)-SUM(dt.dis_persen) omset ,SUM(mt.dis_rp) diskon_nominal", ($where==null?null:$where), 'mt.tgl DESC', "mt.tgl");
            $data['report'] = $this->m_crud->select_limit('report_trx rt', "LEFT(CONVERT(VARCHAR, rt.tgl, 120), 10) tanggal, SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null?null:$where), 'rt.tgl DESC', "rt.tgl", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

            $row = $this->m_crud->get_data('report_trx rt', "SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null?null:$where));

            $data['tgs'] = $row['gross_sales'];
            $data['tdi'] = $row['diskon_item'];
            $data['tdt'] = $row['diskon_trx'];
            $data['tns'] = $row['gross_sales']-$row['diskon_item']-$row['diskon_trx'];
            $data['ttax'] = $row['tax'];
            $data['tsrv'] = $row['service'];
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function omset_periode() {
        $this->access_denied(164);
        $data = $this->data;
        $function = 'omset_periode';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Omset Periode';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function get_omset_periode($param, $tanggal, $tanggal2) {
        $this->access_denied(164);
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');

        $list_omset = '';
        $no = 1;
        $param = base64_decode($param);
        $tanggal_sekarang = base64_decode($tanggal);
        $diff = mktime(0,0,0,date('m', strtotime($tanggal_sekarang)),0, date('Y', strtotime($tanggal_sekarang)));
        $tanggal_sebelum = base64_decode($tanggal2);//date('Y-m', $diff);
		$this->session->set_userdata('search', array('tanggal' => $tanggal_sekarang, 'tanggal2' => $tanggal_sebelum));
		
		$transaksi_sekarang = " ,isnull((select count(mt.kd_trx) from report_trx mt where mt.Lokasi=Kode AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 7) = '".$tanggal_sekarang."'), 0) transaksi_sekarang";
        $transaksi_sebelum = " ,isnull((select count(mt.kd_trx) from report_trx mt where mt.Lokasi=Kode AND LEFT(CONVERT(VARCHAR, mt.tgl, 120), 7) = '".$tanggal_sebelum."'), 0) transaksi_sebelum";
		$omset_sekarang = " ,isnull((SELECT SUM(st-disc_item-dis_rp+tax+service) FROM report_trx WHERE lokasi=Kode AND LEFT(CONVERT(VARCHAR, tgl, 120), 7) = '".$tanggal_sekarang."'), 0) omset_sekarang";;
        $omset_sebelum = " ,isnull((SELECT SUM(st-disc_item-dis_rp+tax+service) FROM report_trx WHERE lokasi=Kode AND LEFT(CONVERT(VARCHAR, tgl, 120), 7) = '".$tanggal_sebelum."'), 0) omset_sebelum";
        $read_omset = $this->m_crud->read_data("Lokasi", "Kode, Nama".$omset_sebelum.$omset_sekarang.$transaksi_sebelum.$transaksi_sekarang, $this->where_lokasi, "Kode, Nama");

        $omt_skr = 0; $tr_skr = 0;
        $omt_sbl = 0; $tr_sbl = 0;
        if ($param == 'search') {
            foreach ($read_omset as $row) {
                if ($row['omset_sebelum'] == 0) {
                    if ($row['omset_sekarang'] > 0) {
                        $persentase = 100;
                    } else {
                        $persentase = 0;
                    }
                } else {
                    $persentase = (($row['omset_sekarang'] - $row['omset_sebelum']) / $row['omset_sebelum']) * 100;
                }
                $list_omset .= '
                <tr>
                    <td>' . $no . '</td>
                    <td>' . $row['Nama'] . '</td>
                    <td class="text-right">' . number_format($row['omset_sebelum'], 2) . '</td>
                    <td class="text-right">' . $row['transaksi_sebelum'] . '</td>
                    <td class="text-right">' . number_format($row['omset_sebelum']/($row['transaksi_sebelum']>0?$row['transaksi_sebelum']:1), 2) . '</td>
                    <td class="text-right">' . number_format($row['omset_sekarang'], 2) . '</td>
                    <td class="text-right">' . $row['transaksi_sekarang'] . '</td>
                    <td class="text-right">' . number_format($row['omset_sekarang']/($row['transaksi_sekarang']>0?$row['transaksi_sekarang']:1), 2) . '</td>
					<td class="text-right">' . number_format($row['omset_sekarang'] - $row['omset_sebelum'], 2) . '</td>
                    <td class="text-center">' . number_format($persentase, 2, '.', '') . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
                            <ul class="dropdown-menu" style="position: relative" role="menu">
                                <li><a href="#" onclick="detail(\''.$row['Kode'].'\')"><i class="md md-visibility"></i> Detail</a></li>
                                <li><a href="#" onclick="detail(\''.$row['Kode'].'\', \'export\')"><i class="md md-print"></i> Export</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            ';
                $no++;
                $omt_skr = $omt_skr + $row['omset_sekarang'];
                $tr_skr = $tr_skr + $row['transaksi_sekarang'];
                $omt_sbl = $omt_sbl + $row['omset_sebelum'];
                $tr_sbl = $tr_sbl + $row['transaksi_sebelum'];
            }

            $list_omset .= '
            <tr>
                <th colspan="2">TOTAL</th>
                <th class="text-right">' . number_format($omt_sbl) . '</th>
                <th class="text-right">' . $tr_sbl . '</th>
                <th class="text-right">' . number_format($omt_sbl/($tr_sbl>0?$tr_sbl:1), 2) . '</th>
                <th class="text-right">' . number_format($omt_skr) . '</th>
                <th class="text-right">' . $tr_skr . '</th>
                <th class="text-right">' . number_format($omt_skr/($tr_skr>0?$tr_skr:1), 2) . '</th>
				<th class="text-right">' . number_format($omt_skr - $omt_sbl) . '</th>
                <th class="text-center">' . number_format(($omt_sbl==0?1:(($omt_skr - $omt_sbl) / $omt_sbl)) * 100, 2, '.', '') . '</th>
                <th></th>
            </tr>
        ';

        echo json_encode(array('result' => $list_omset));
        } else {
            $baca = $read_omset;
            $header = array(
                'merge' => array('A1:I1', 'A2:I2', 'A3:I3'),
                'auto_size' => false,
                'font' => array(
                    'A1:A2' => array('bold' => true, 'color' => array('rgb' => '000000'), 'size' => 12, 'name' => 'Verdana'),
                    'A3' => array('bold' => true, 'name' => 'Verdana'),
                    'A5:I5' => array('bold' => true, 'size' => 9, 'name' => 'Verdana')
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A5:I5' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => 'Omset Periode'),
                '2' => array('A' => 'Indokids'),
                '3' => array('A' => $tanggal_sebelum.' - '.$tanggal_sekarang),
                '5' => array(
                    'A'=>'Lokasi', 'B'=>'Omset Bulan Lalu', 'C'=>'Transaksi Bulan Lalu', 'D'=>'Rata2 Transaksi Bulan Lalu', 'E'=>'Omset Bulan Sekarang', 
					'F'=>'Transaksi Bulan Sekarang', 'G'=>'Rata2 Transaksi Bulan Sekarang', 'H'=>'Pertumbuhan', 'I'=>'Persentase Pertumbuhan'
                )
            );

            foreach ($baca as $row => $value) {
                if ($value['omset_sebelum'] == 0) {
                    $persentase = 0;
                } else {
                    $persentase = (($value['omset_sekarang'] - $value['omset_sebelum']) / $value['omset_sebelum']) * 100;
                }
                $body[$row] = array(
                    $value['Nama'], $value['omset_sebelum'], $value['transaksi_sebelum'], $value['omset_sebelum']/($value['transaksi_sebelum']>0?$value['transaksi_sebelum']:1), 
					$value['omset_sekarang'], $value['transaksi_sekarang'], $value['omset_sekarang']/($value['transaksi_sekarang']>0?$value['transaksi_sekarang']:1), 
					$value['omset_sekarang']-$value['omset_sebelum'], $persentase
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', 'Omset Periode.xlsx'), $header, $body);
        }
    }

    public function det_omset_periode($kode, $tgl, $tgl2, $param=null) {
        $kode = base64_decode($kode);
        $tgl = base64_decode($tgl);
        $diff = mktime(0,0,0,date('m', strtotime($tgl)),0, date('Y', strtotime($tgl)));
        $tgl_sbl = base64_decode($tgl2); //date('Y-m', $diff);
        $list_brg_qty = '';
        $list_brg_value = '';
        $list_brg_qty_l = '';
        $list_brg_value_l = '';

        $list_supp_qty = '';
        $list_supp_value = '';
        $list_supp_qty_l = '';
        $list_supp_value_l = '';

        $top_brg_qty = $this->m_crud->read_data("omset_report omr, barang br", "br.kd_brg, br.nm_brg, SUM(omr.qty) qty", "omr.kd_brg=br.kd_brg AND omr.Lokasi = '".$kode."' AND LEFT(CONVERT(VARCHAR, omr.tgl, 120), 7) = '".$tgl."'", "SUM(omr.qty) DESC", "br.kd_brg, br.nm_brg", 100);
        $top_brg_value = $this->m_crud->read_data("omset_report omr, barang br", "br.kd_brg, br.nm_brg, SUM(omr.st-omr.disc) value", "omr.kd_brg=br.kd_brg AND omr.Lokasi = '".$kode."' AND LEFT(CONVERT(VARCHAR, omr.tgl, 120), 7) = '".$tgl."'", "SUM(omr.st-omr.disc) DESC", "br.kd_brg, br.nm_brg", 100);
        $top_brg_qty_l = $this->m_crud->read_data("omset_report omr, barang br", "br.kd_brg, br.nm_brg, SUM(omr.qty) qty", "omr.kd_brg=br.kd_brg AND omr.Lokasi = '".$kode."' AND LEFT(CONVERT(VARCHAR, omr.tgl, 120), 7) = '".$tgl_sbl."'", "SUM(omr.qty) DESC", "br.kd_brg, br.nm_brg", 100);
        $top_brg_value_l = $this->m_crud->read_data("omset_report omr, barang br", "br.kd_brg, br.nm_brg, SUM(omr.st-omr.disc) value", "omr.kd_brg=br.kd_brg AND omr.Lokasi = '".$kode."' AND LEFT(CONVERT(VARCHAR, omr.tgl, 120), 7) = '".$tgl_sbl."'", "SUM(omr.st-omr.disc) DESC", "br.kd_brg, br.nm_brg", 100);

        $top_supp_qty = $this->m_crud->read_data("omset_report omr, barang br, Group1 gr1", "gr1.Nama, SUM(omr.qty) qty", "omr.kd_brg=br.kd_brg AND br.Group1=gr1.Kode AND omr.Lokasi = '".$kode."' AND LEFT(CONVERT(VARCHAR, omr.tgl, 120), 7) = '".$tgl."'", "SUM(omr.qty) DESC", "gr1.Nama", 100);
        $top_supp_value = $this->m_crud->read_data("omset_report omr, barang br, Group1 gr1", "gr1.Nama, SUM(omr.st-omr.disc) value", "omr.kd_brg=br.kd_brg AND br.Group1=gr1.Kode AND omr.Lokasi = '".$kode."' AND LEFT(CONVERT(VARCHAR, omr.tgl, 120), 7) = '".$tgl."'", "SUM(omr.st-omr.disc) DESC", "gr1.Nama", 100);
        $top_supp_qty_l = $this->m_crud->read_data("omset_report omr, barang br, Group1 gr1", "gr1.Nama, SUM(omr.qty) qty", "omr.kd_brg=br.kd_brg AND br.Group1=gr1.Kode AND omr.Lokasi = '".$kode."' AND LEFT(CONVERT(VARCHAR, omr.tgl, 120), 7) = '".$tgl_sbl."'", "SUM(omr.qty) DESC", "gr1.Nama", 100);
        $top_supp_value_l = $this->m_crud->read_data("omset_report omr, barang br, Group1 gr1", "gr1.Nama, SUM(omr.st-omr.disc) value", "omr.kd_brg=br.kd_brg AND br.Group1=gr1.Kode AND omr.Lokasi = '".$kode."' AND LEFT(CONVERT(VARCHAR, omr.tgl, 120), 7) = '".$tgl_sbl."'", "SUM(omr.st-omr.disc) DESC", "gr1.Nama", 100);

        if ($param == null) {
            $no = 1;
            foreach ($top_brg_qty as $row) {
                $list_brg_qty .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . $row['kd_brg'] . '</td>
                <td>' . $row['nm_brg'] . '</td>
                <td>' . (int)$row['qty'] . '</td>
            </tr>
            ';
            }

            $no = 1;
            foreach ($top_brg_value as $row) {
                $list_brg_value .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . $row['kd_brg'] . '</td>
                <td>' . $row['nm_brg'] . '</td>
                <td class="text-right">' . number_format($row['value']) . '</td>
            </tr>
            ';
            }

            $no = 1;
            foreach ($top_brg_qty_l as $row) {
                $list_brg_qty_l .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . $row['kd_brg'] . '</td>
                <td>' . $row['nm_brg'] . '</td>
                <td>' . (int)$row['qty'] . '</td>
            </tr>
            ';
            }

            $no = 1;
            foreach ($top_brg_value_l as $row) {
                $list_brg_value_l .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . $row['kd_brg'] . '</td>
                <td>' . $row['nm_brg'] . '</td>
                <td class="text-right">' . number_format($row['value']) . '</td>
            </tr>
            ';
            }

            $no = 1;
            foreach ($top_supp_qty as $row) {
                $list_supp_qty .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . $row['Nama'] . '</td>
                <td>' . (int)$row['qty'] . '</td>
            </tr>
            ';
            }

            $no = 1;
            foreach ($top_supp_value as $row) {
                $list_supp_value .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . $row['Nama'] . '</td>
                <td class="text-right">' . number_format($row['value']) . '</td>
            </tr>
            ';
            }

            $no = 1;
            foreach ($top_supp_qty_l as $row) {
                $list_supp_qty_l .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . $row['Nama'] . '</td>
                <td>' . (int)$row['qty'] . '</td>
            </tr>
            ';
            }

            $no = 1;
            foreach ($top_supp_value_l as $row) {
                $list_supp_value_l .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . $row['Nama'] . '</td>
                <td class="text-right">' . number_format($row['value']) . '</td>
            </tr>
            ';
            }

            echo json_encode(array('list_brg_qty' => $list_brg_qty, 'list_brg_value' => $list_brg_value, 'list_brg_qty_l' => $list_brg_qty_l, 'list_brg_value_l' => $list_brg_value_l, 'list_supp_qty' => $list_supp_qty, 'list_supp_value' => $list_supp_value, 'list_supp_qty_l' => $list_supp_qty_l, 'list_supp_value_l' => $list_supp_value_l));
        } else if($param=='export'){
            $header = array(
                'merge' 	=> array('A1:I1','A2:I2','A3:D3','F3:I3','A5:D5','F5:I5','A6:D6','F6:I6'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3:F3' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>10, 'name'=>'Verdana'),
                    'A5:K5' => array('bold'=>true, 'size'=>12, 'name'=>'Verdana'),
                    'A6:K6' => array('bold'=>true, 'size'=>11, 'name'=>'Verdana'),
                    'A7:I7' => array('bold'=>true)
                ),
                'alignment' => array(
                    'A1:A2' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
                    'F3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),
                    'A6:K6' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $this->data['site']->title),
                '2' => array('A' => 'Detail Omset Periode'),
                '3' => array('A' => 'Periode : '.$tgl, 'F' => 'Lokasi : '.$kode),
                '5' => array('A' => 'Periode Bulan Lalu', 'F' => 'Periode Sekarang'),
                '6' => array('A' => 'Top 100 Item By Qty', 'F' => 'Top 100 Item By Qty'),
                '7' => array(
                    'A'=>'No', 'B'=>'Kode', 'C'=>'Nama', 'D'=>'Qty', 'E'=>'', 'F'=>'No', 'G'=>'Kode', 'H'=>'Nama', 'I'=>'Qty'
                )
            );
			$row = 0; 
            for ($i=0; $i<100; $i++) {
                $no=$i+1; 
				$body[$row] = array(
                    $no, $top_brg_qty_l[$i]['kd_brg'], $top_brg_qty_l[$i]['nm_brg'], (int)$top_brg_qty_l[$i]['qty'], '', $no, $top_brg_qty[$i]['kd_brg'], $top_brg_qty[$i]['nm_brg'], (int)$top_brg_qty[$i]['qty']
                );
                $row++;
            }
			
			$row++; 
			array_push($header['merge'], 'A'.($row+8).':D'.($row+8)); array_push($header['merge'], 'F'.($row+8).':I'.($row+8));
			$header['font']['A'.($row+8).':K'.($row+8)] = array('bold'=>true, 'size'=>12, 'name'=>'Verdana');
			$body[$row] = array('Periode Bulan Lalu', '', '', '', '', 'Periode Sekarang');
			$row++;  
			array_push($header['merge'], 'A'.($row+8).':D'.($row+8)); array_push($header['merge'], 'F'.($row+8).':I'.($row+8));
			$header['font']['A'.($row+8).':K'.($row+8)] = array('bold'=>true, 'size'=>11, 'name'=>'Verdana');
			$header['alignment']['A'.($row+8).':K'.($row+8)] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$body[$row] = array('Top 100 Item By Value', '', '', '', '', 'Top 100 Item By Value');
			$row++; 
			$header['font']['A'.($row+8).':I'.($row+8)] = array('bold'=>true);
			$body[$row] = array('A'=>'No', 'B'=>'Kode', 'C'=>'Nama', 'D'=>'Value', 'E'=>'', 'F'=>'No', 'G'=>'Kode', 'H'=>'Nama', 'I'=>'Value');
			$row++; 
            for ($i=0; $i<100; $i++) {
                $no=$i+1;
				$body[$row] = array(
                    $no, $top_brg_value_l[$i]['kd_brg'], $top_brg_value_l[$i]['nm_brg'], $top_brg_value_l[$i]['value'], '', $no, $top_brg_value[$i]['kd_brg'], $top_brg_value[$i]['nm_brg'], $top_brg_value[$i]['value']
                );
                $row++;
            }
			
			$row++; 
			array_push($header['merge'], 'A'.($row+8).':G'.($row+8)); array_push($header['merge'], 'I'.($row+8).':O'.($row+8));
			$header['font']['A'.($row+8).':O'.($row+8)] = array('bold'=>true, 'size'=>12, 'name'=>'Verdana');
			$body[$row] = array('Periode Bulan Lalu', '', '', '', '', '', '', '', 'Periode Sekarang');
			$row++; 
			array_push($header['merge'], 'A'.($row+8).':C'.($row+8)); array_push($header['merge'], 'E'.($row+8).':G'.($row+8)); array_push($header['merge'], 'I'.($row+8).':K'.($row+8)); array_push($header['merge'], 'M'.($row+8).':O'.($row+8));
			$header['font']['A'.($row+8).':O'.($row+8)] = array('bold'=>true, 'size'=>11, 'name'=>'Verdana');
			$header['alignment']['A'.($row+8).':O'.($row+8)] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$body[$row] = array('Top 100 Supplier By Qty', '', '', '', 'Top 100 Supplier By Value', '', '', '', 'Top 100 Supplier By Qty', '', '', '', 'Top 100 Supplier By Value');
			$row++; 
			$header['font']['A'.($row+8).':O'.($row+8)] = array('bold'=>true);
			$body[$row] = array('A'=>'No', 'B'=>'Supplier', 'C'=>'Qty', 'D'=>'', 'E'=>'No', 'F'=>'Supplier', 'G'=>'Value', 'H'=>'', 'I'=>'No', 'J'=>'Supplier', 'K'=>'Qty', 'L'=>'', 'M'=>'No', 'N'=>'Supplier', 'O'=>'Value');
			$row++; 
            for ($i=0; $i<100; $i++) {
                $no=$i+1;
				$body[$row] = array(
                    $no, $top_supp_qty_l[$i]['Nama'], $top_supp_qty_l[$i]['qty'], '', $no, $top_supp_value_l[$i]['Nama'], $top_supp_value_l[$i]['value'], '', $no, $top_supp_qty[$i]['Nama'], $top_supp_qty[$i]['qty'], '', $no, $top_supp_value[$i]['Nama'], $top_supp_value[$i]['value']
                );
                $row++;
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', 'Detail Omset Periode.xlsx'), $header, $body);
        }
    }

    public function penjualan_by_customer($action = null, $id = null){
        $this->access_denied(156);
        $data = $this->data;
        $function = 'penjualan_by_customer';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = 'Group1';
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Penjualan By Customer';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "rt.kd_cust=cs.kd_cust";
        $tgl = ''; $q_lokasi = 'Semua Lokasi';

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rt.tgl, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
        }else{
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="rt.Lokasi = '".$lokasi."'"; $q_lokasi = "Lokasi : ".$lokasi."";
        } else {
            ($where==null)?null:$where.=" and "; $where.="rt.Lokasi in (".$this->lokasi_in.")";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(cs.kd_cust like '%".$search."%' or cs.Nama like '%".$search."%')"; }

        $data['tgl'] = $tgl;
        $data['periode'] = 'Periode : '.$tgl_awal.' - '.$tgl_akhir;
        $data['q_lokasi'] = $q_lokasi;
        $data['lokasi'] = $lokasi;
        $data['tgl_awal'] = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;

        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->read_data("report_trx rt, Customer cs", "cs.kd_cust, cs.Nama, SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null)?null:$where, "cs.kd_cust asc", "cs.kd_cust, cs.Nama");
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
                    'A'=>'Kode Customer', 'B'=>'Nama Customer', 'C'=>'Qty Terjual', 'D'=>'Sub Total', 'E'=>'Diskon Item', 'F'=>'Diskon Transaksi', 'G'=>'Net Sales', 'H'=>'Tax', 'I'=>'Service', 'J'=>'Gross Sales'
                )
            );

            $end = 0;
            $qt = 0; $di = 0; $dt = 0; $gs = 0; $tax = 0; $srv = 0; $tns = 0;
            foreach($baca as $row => $value){
                $end++;
                $ns = $value['gross_sales']-$value['diskon_item']-$value['diskon_trx']+0;
                $body[$row] = array(
                    $value['kd_cust'], $value['Nama'], $value['qty']+0, $value['gross_sales']+0, $value['diskon_item']+0, $value['diskon_trx']+0, $ns, $value['tax'], $value['service'], $ns+$value['tax']+$value['service']
                );
                $qt = $qt + (int)$value['qty'];
                $gs = $gs + (float)$value['gross_sales'];
                $di = $di + (float)$value['diskon_item'];
                $dt = $dt + (float)$value['diskon_trx'];
                $tax = $tax + (float)$value['tax'];
                $srv = $srv + (float)$value['service'];
                $tns = $tns + $ns;
            }

            $body[$end] = array('TOTAL','',$qt,$gs,$di,$dt,$tns,$tax,$srv,$tns+$tax+$srv);
            array_push($header['merge'], 'A'.($end+6).':B'.($end+6));
            $header['font']['A'.($end+6).':J'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){

            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);

            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('Customer', "*", "kd_cust = '".$id."'");

            ($tgl == null)?$q_tgl="":$q_tgl="AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";
            $where = ""; ($lokasi==null)?"":$where.=" AND mt.Lokasi='".$lokasi."'";
            $qty = "isnull((SELECT qty FROM Det_Trx WHERE qty>0 AND kd_trx=mt.kd_trx AND kd_brg=dt.kd_brg GROUP BY kd_trx, qty), 0) qty";
            $qty_retur = "isnull((SELECT qty FROM Det_Trx WHERE qty<0 AND kd_trx=mt.kd_trx AND kd_brg=dt.kd_brg GROUP BY kd_trx, qty)*-1, 0) qty_retur";
            $row = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, dt.hrg_jual, dt.dis_persen, ".$qty.", ".$qty_retur, "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND mt.kd_cust = '".$id."' ".$q_tgl.$where."", "mt.kd_trx ASC", null);

            $count = count($row);
            $data['loop'] = $count/100;
            for ($x=1; $x<=$data['loop']; $x++) {
                $data['report_detail'][$x] = $this->m_crud->select_limit("Det_Trx dt, barang br, Master_Trx mt", "mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, dt.hrg_jual, dt.dis_persen, ".$qty.", ".$qty_retur, "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND mt.kd_cust = '".$id."' ".$q_tgl.$where."", "mt.kd_trx ASC", null, ($x-1)*100+1, 100*$x);
            }

            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Detail Laporan Penjualan By Customer</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="22%"></th>
									<th width="2%"></th>
									<th width="23%"></th>
									
									<th width="10%"></th>
									<th width="12%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Kode Customer</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['kd_cust'].'</td>
									<td></td>
									<td colspan="3"><b>'.$tgl_awal.' - '.$tgl_akhir.'</b></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Nama Customer</b></td>
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
                'left'=>10,'right'=>10,'top'=>50,'bottom'=>(($count>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        } else {
            $page = ($id==null?1:$id);
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data_over("report_trx rt, Customer cs", 'cs.kd_cust', ($where==null)?null:$where, "cs.kd_cust asc", "cs.kd_cust");
            $config['per_page'] = 10;
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

            $data['report'] = $this->m_crud->select_limit("report_trx rt, Customer cs", "cs.kd_cust, cs.Nama, SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null)?null:$where, "cs.kd_cust asc", "cs.kd_cust, cs.Nama", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

            $row = $this->m_crud->get_data("report_trx rt, Customer cs", "SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null)?null:$where);

            $data['tqt'] = $row['qty'];
            $data['tgs'] = $row['gross_sales'];
            $data['tdi'] = $row['diskon_item'];
            $data['tdt'] = $row['diskon_trx'];
            $data['tns'] = $row['gross_sales']-$row['diskon_item']-$row['diskon_trx'];
            $data['ttax'] = $row['tax'];
            $data['tsrv'] = $row['service'];
        }

        if($this->form_validation->run() == false) {
            $this->load->view('bo/index', $data);
        } else {
            $this->load->view('bo/index', $data);
        }
    }

    public function detail_by_customer($kode, $tgl, $lokasi=null) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $kode = base64_decode($kode);
        $tgl = base64_decode($tgl);
        $explode_date = explode(' - ', $tgl);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);

        $ket_lokasi = 'Semua Lokasi';
        $periode = $tgl_awal.' - '.$tgl_akhir;
        if ($lokasi!=null) {
            $lokasi = base64_decode($lokasi);
            $where = " AND mt.Lokasi='".$lokasi."'";
            $ket_lokasi = $lokasi;
        }

        $get_nama = $this->m_crud->get_data("Customer", "Nama", "kd_cust='".$kode."'");

        $q_tgl="AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";
        $qty = "isnull((SELECT qty FROM Det_Trx WHERE qty>0 AND kd_trx=mt.kd_trx AND kd_brg=dt.kd_brg GROUP BY kd_trx, qty), 0) qty";
        $qty_retur = "isnull((SELECT qty FROM Det_Trx WHERE qty<0 AND kd_trx=mt.kd_trx AND kd_brg=dt.kd_brg GROUP BY kd_trx, qty)*-1, 0) qty_retur";
        $detail = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, dt.hrg_jual, dt.dis_persen, ".$qty.", ".$qty_retur, "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND mt.kd_cust = '".$kode."' ".$q_tgl.$where."");

        $no = 1;
        $jumlah = 0;
        $total_qty_kirim = 0;
        $total_qty_retur = 0;
        $total_qty_laku = 0;
        $total_diskon_persen = 0;
        $total_sub_total = 0;
        $list = '';
        foreach ($detail as $rows){
            $total_qty_kirim = $total_qty_kirim + ($rows['qty']+0);
            $total_qty_retur = $total_qty_retur + ($rows['qty_retur']+0);
            $total_qty_laku = $total_qty_laku + ($rows['qty']-$rows['qty_retur']+0);
            $total_diskon_persen = $total_diskon_persen + $rows['dis_persen'];
            $total_sub_total = $total_sub_total + (($rows['qty']-$rows['qty_retur'])*$rows['hrg_jual'])-$rows['dis_persen'];
            $list .= '
                <tr>
                    <td>'.$no.'</td>
                    <td>'.$rows['kd_trx'].'</td>
                    <td>'.substr($rows['tgl'], 1, 10).'</td>
                    <td>'.$rows['kd_brg'].'</td>
                    <td>'.$rows['nm_brg'].'</td>
                    <td>'.$rows['nm_brg'].'</td>
                    <td>'.$rows['Deskripsi'].'</td>
                    <td class="text-center">'.($rows['qty']+0).'</td>
                    <td class="text-center">'.($rows['qty_retur']+0).'</td>
                    <td class="text-center">'.($rows['qty']-$rows['qty_retur']+0).'</td>
                    <td class="text-right">'.number_format($rows['hrg_jual']).'</td>
                    <td class="text-right">'.number_format($rows['dis_persen']).'</td>
                    <td class="text-right">'.number_format((($rows['qty']-$rows['qty_retur'])*$rows['hrg_jual'])-$rows['dis_persen']).'</td>
                </tr>
            ';
            $no++;
        }
        $list .= '
            <tr>
                <th colspan="7">Total</th>
                <th class="text-center">'.$total_qty_kirim.'</th>
                <th class="text-center">'.$total_qty_retur.'</th>
                <th class="text-center">'.$total_qty_laku.'</th>
                <th></th>
                <th class="text-right">'.number_format($total_diskon_persen).'</th>
                <th class="text-right">'.number_format($total_sub_total).'</th>
            </tr>
        ';

        echo json_encode(array('list' => $list, 'det' => array('kode'=>$kode, 'nama'=>$get_nama['Nama'], 'lokasi'=>$ket_lokasi, 'periode'=>$periode)));
    }

    public function penjualan_by_kasir($action = null, $id = null){
        $this->access_denied(157);
        $datax = $this->data;
        $function = 'penjualan_by_kasir';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = 'Group1';
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $datax['title'] = 'Laporan Penjualan By Kasir';
        $datax['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $datax['content'] = $view.$function;
        $datax['table'] = $table;

        $where = null;

        if(isset($_POST['search'])||$action=='print'){
            $id = json_decode(base64_decode($id),true);
            $this->session->set_userdata('search', array('tgl_periode' => (isset($_POST['search']))?$_POST['tgl_periode']:$id['tgl_periode'], 'lokasi' => (isset($_POST['search']))?$_POST['lokasi']:$id['lokasi']));
        }

        $lokasi = $this->session->search['lokasi']; $ses_tgl_periode = $this->session->search['tgl_periode'];

        if($ses_tgl_periode != null){
            $tanggal = $ses_tgl_periode;
        }  else {
            $tanggal = date('Y-m-d');
        }

        if($lokasi!='all' && $lokasi!=''){
            $method = 'listSalesReportByFinancialLokasi&lokasi='.$lokasi.'';
        } else {
            $method = 'listSalesReportByFinancialAll&uid='.$this->user.'';
        }

        if ($_SERVER['SERVER_PORT'] == '1233') {
            $link = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
            $curl = 'http://'.$link.'/monitoring/?method='.$method.'&page=1&tgl='.$tanggal.'';
        } else {
            $link = $_SERVER['SERVER_NAME'];
            $curl = 'http://'.$link.'/monitoring/?method='.$method.'&page=1&tgl='.$tanggal.'';
        }

        /*$result_data = file_get_contents($curl);

        $datax['result'] = json_decode($result_data, true);*/

        /*$response = array();

        if (count($read_setoran) != 0) {

            $baris = array();
            $response["sales_report_by_financial"] = array();
            $total_outcome = 0;
            $net_omset = 0;

            foreach ($read_setoran as $row1){
                $data = array();

                $id_kasir = $row1["Kd_Kasir"];
                $tgl = $tanggal;
                $kassa = $row1["Kassa"];
                $cashier_cash = $row1["Setoran_tunai"]+null;
                $data["kode_kasir"]=$id_kasir;

                $query_total_main = $this->m_crud->read_data("Master_Trx", "SUM(Master_Trx.GT) AS total,SUM(Master_Trx.ST) AS subtotal,SUM(Master_Trx.dis_rp) as diskon_total,SUM(Master_Trx.tax) as tax_total, SUM(Master_Trx.bayar-Master_Trx.change) as cash_total, SUM(Master_Trx.jml_kartu) as edc_total, SUM(Master_Trx.kas_lain) as kas_lain", "Master_Trx.Lokasi='".$lokasi."' AND Master_Trx.kd_kasir = '".$id_kasir."' AND Master_Trx.kassa = '".$kassa."' AND HR = 'S' AND LEFT(CONVERT(VARCHAR, Master_Trx.tgl, 120), 10) BETWEEN '".$tgl."' AND '".$tgl."'");

                $query_disc_item = $this->m_crud->read_data("Det_Trx, Master_Trx","SUM(Det_Trx.dis_persen) as diskon_item","Master_Trx.kd_trx = Det_Trx.kd_trx AND Master_Trx.Lokasi = '".$lokasi."' AND Master_Trx.kd_kasir = '".$id_kasir."' AND Master_Trx.kassa = '".$kassa."' AND HR = 'S' AND LEFT(CONVERT(VARCHAR, Master_Trx.tgl, 120), 10) BETWEEN '".$tgl."' AND '".$tgl."'");
                $query_receive_amount = $this->m_crud->read_data("kas_masuk","SUM(jumlah) as receive_amount","Lokasi = '".$lokasi."' AND kd_kasir = '".$id_kasir."' AND tgl between '".$tgl."' and '".$tgl."'");

                $query_total_sales = $this->m_crud->read_data("Det_Trx,Master_Trx","SUM(Det_Trx.hrg_jual*Det_Trx.qty) as total_sales","Det_Trx.kd_trx = Master_Trx.kd_trx AND Master_Trx.Lokasi = '".$lokasi."' AND Master_Trx.kd_kasir = '".$id_kasir."' AND Master_trx.kassa = '".$kassa."' AND HR = 'S' AND LEFT(CONVERT(VARCHAR, Master_Trx.tgl, 120), 10) between '".$tgl."' and '".$tgl."'");
                $query_return = $this->m_crud->read_data("Det_Trx,Master_Trx","SUM(Det_Trx.hrg_jual*Det_Trx.qty) as total_return","Det_Trx.kd_trx = Master_Trx.kd_trx AND Det_Trx.qty <0 AND Master_Trx.Lokasi = '".$lokasi."' AND Master_Trx.kd_kasir = '".$id_kasir."'  AND Master_trx.kassa = '".$kassa."'  AND HR = 'S'  AND LEFT(CONVERT(VARCHAR, Master_Trx.tgl, 120), 10) between '".$tgl."' and '".$tgl."'");
                $query_paid_out = $this->m_crud->read_data("kas_keluar","SUM(jumlah) as paid_out","Lokasi = '".$lokasi."' AND kd_kasir = '".$id_kasir."' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) between '".$tgl."' and '".$tgl."'");

                foreach ($query_total_main as $baris){

                    foreach ($query_total_sales as $baris_total_sales){

                        if(empty(str_replace('.0000', '', $baris_total_sales["total_sales"]))){
                            $data["total_sales"]="0";
                        }else {
                            $data["total_sales"]=str_replace('.0000', '', $baris_total_sales["total_sales"]);
                        }

                        $net_omset = $baris_total_sales["total_sales"];

                    }


                    if(empty(str_replace('.0000', '', $baris["diskon_total"]))){
                        $data["diskon_total"]="0";
                    }else {
                        $data["diskon_total"]=str_replace('.0000', '', $baris["diskon_total"]);
                    }

                    $tax = 	0;


                    if(empty(str_replace('.0000', '', $tax))){
                        $data["tax_total"]="0";
                    }else {
                        $data["tax_total"]=str_replace('.0000', '', $tax);
                    }


                    if(empty(str_replace('.0000', '', $baris["edc_total"]))){
                        $data["edc_total"]="0";
                    }else {
                        $data["edc_total"]=str_replace('.0000', '', $baris["edc_total"]);
                    }

                    if(empty(str_replace('.0000', '', $baris["kas_lain"]))){
                        $data["other_income"]="0";
                    }else {
                        $data["other_income"]=str_replace('.0000', '', $baris["kas_lain"]);
                    }


                    foreach ($query_receive_amount as $baris1){

                        if(empty(str_replace('.0000', '', $baris1["receive_amount"]))){
                            $data["receive_amount"]="0";
                        }else {
                            $data["receive_amount"]=str_replace('.0000', '', $baris1["receive_amount"]);
                        }

                    }

                    $total_income = $baris1["receive_amount"] + $baris["kas_lain"];

                    $data["total_income"] = $total_income;




                    foreach ($query_disc_item as $baris1){

                        if(empty(str_replace('.0000', '', $baris1["diskon_item"]))){
                            $data["diskon_item"]="0";
                        }else {
                            $data["diskon_item"]=str_replace('.0000', '', $baris1["diskon_item"]);
                        }

                    }

                    $net_omset2 = $net_omset - ($data["diskon_item"] + $data["diskon_total"]);


                    $data["net_omset"] = $net_omset2;


                    $data["total_omset"] = $net_omset2+$tax;

                    $cash_total = ($net_omset2+$tax)-$baris["edc_total"];

                    $data["cash_total"]=$cash_total;

                    $cash_in_hand = $cash_total+ $total_income;

                    $data["cash_in_hand"] = $cash_in_hand;


                    foreach ($query_return as $baris){

                        if(empty(str_replace('.0000', '', $baris["total_return"]))){
                            $data["return"]="0";
                        }else {
                            $data["return"]=str_replace('.0000', '', $baris["total_return"]);
                        }

                        foreach ($query_paid_out as $baris2){

                            if(empty(str_replace('.0000', '', $baris2["paid_out"]))){
                                $data["paid_out"]="0";
                            }else {
                                $data["paid_out"]=str_replace('.0000', '', $baris2["paid_out"]);
                            }

                            $total_outcome = $baris2["paid_out"] + $baris["total_return"];

                            $data["total_outcome"] =  $total_outcome;

                            $total_cash_sales = $cash_in_hand - $baris2["paid_out"];

                            $data["total_cash_sales"] = $total_cash_sales;

                        }

                        $data["cashier_cash"]= $cashier_cash;

                        if(($cashier_cash - $total_cash_sales)==0){

                            $data["status_report"] = 'Balance';

                        }else if(($cashier_cash - $total_cash_sales)<0){

                            $data["status_report"] = 'Deficit ('.(number_format(($cashier_cash - $total_cash_sales),0,".",".")).')';

                        }else {

                            $data["status_report"] = 'Surplus ('.(number_format(($cashier_cash - $total_cash_sales),0,".",".")).')';

                        }

                    }

                }

                array_push($response["sales_report_by_financial"], $data);

            }

            $datax['status'] = 1;
            $datax['result'] = $response;
        } else {
            $datax['status'] = 0;
        }*/

        if($action=='print'){

            $datax['content'] = $view.'pdf_invoice_'.$function;

            $count = count($datax['result']["sales_report_by_financial"]);

            $datax['report_detail'] = $datax['result'];

            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='to_pdf')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $datax['title']).'-'.str_replace('/', '-', 'cashier_report'));
            $html = $this->load->view('bo/'.$datax['content'], $datax, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Detail Laporan Penjualan By Kasir</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="22%"></th>
									<th width="2%"></th>
									<th width="23%"></th>
									
									<th width="10%"></th>
									<th width="12%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Periode</b></td>
									<td><b>:</b></td>
									<td>'.$tanggal.'</td>
									<td></td>
									<td colspan="3"></td>
								</tr>
							</tbody>
						</table>
					</div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>45,'bottom'=>(($count>$datax['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $datax); }
        else { $this->load->view('bo/index', $datax); }
    }

    public function penjualan_by_barang($action = null, $id = null){
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '2048M');
		ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
		ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

		$this->access_denied(158);
        $data = $this->data;
        $function = 'penjualan_by_barang';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = 'Group1';
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Penjualan By Barang';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $set_urutkan = "br.kd_brg ASC";
        $data_urutan = array(
            "qty_asc" => "sum(dt.qty) asc",
            "qty_desc" => "sum(dt.qty) desc",
            "om_desc" => "sum(dt.qty*dt.hrg_jual) desc",
            "om_asc" => "sum(dt.qty*dt.hrg_jual) asc"
        );
        $where = "mt.HR = 'S'";
        $tgl = ''; $periode = 'Periode : None'; $q_lokasi = 'Semua Lokasi';

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date' => $_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'urutkan' => $_POST['urutkan']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $urutkan = $this->session->search['urutkan'];
		$explode_date = explode(' - ', $date);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $tgl = "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
            $where.=$tgl;
        }else{
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
            ($where == null) ? null : $where .= " and ";
            $tgl = "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'"; $where.=$tgl;
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi = '".$lokasi."'"; $q_lokasi = "Lokasi : ".$lokasi."";
        } else {
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi in (".$this->lokasi_in.")";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(br.kd_brg like '%".$search."%' or br.nm_brg like '%".$search."%' or br.barcode like '%".$search."%' or br.Deskripsi like '%".$search."%')"; }
        if (isset($urutkan)&&$urutkan!='') {
            $set_urutkan = $data_urutan[$urutkan];
        }

        $data['tgl'] = $tgl;
        $data['periode'] = $periode;
        $data['q_lokasi'] = $q_lokasi;
        $data['lokasi'] = $lokasi;
        $data['tgl_awal'] = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;
        $join = array(array("type"=>"left","table"=>"Det_Trx dt"),array("type"=>"left","table"=>"barang br"));
        $on = array("dt.kd_trx=mt.kd_trx","br.kd_brg=dt.kd_brg");

        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->join_data("Master_Trx mt", "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan, SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service",$join,$on, ($where==null)?null:$where, $set_urutkan, "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan");
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
                    'A'=>'Kode Barang', 'B'=>'Barcode', 'C'=>'Nama Barang', 'D'=>'Deskripsi', 'E'=>'Qty Terjual', 'F'=>'Sub Total', 'G'=>'Diskon Item', 'H'=>'Net Sales', 'I'=>'Tax', 'J'=>'Service', 'K'=>'Gross Sales'
                )
            );

            $end = 0;
            $qt = 0; $gs = 0; $di = 0; $ns = 0; $tax = 0; $srv = 0;
            foreach($baca as $row => $value){
                $end++;
                $body[$row] = array(
                    $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['Deskripsi'], ($value['qty_jual']+0), $value['gross_sales'], $value['diskon_item'], ($value['gross_sales']-$value['diskon_item']), $value['tax'], $value['service'], ($value['gross_sales']-$value['diskon_item']+$value['tax']+$value['service'])
                );

                $qt = $qt + (int)$value['qty_jual'];
                $gs = $gs + (float)$value['gross_sales'];
                $di = $di + (float)$value['diskon_item'];
                $ns = $ns + ((float)$value['gross_sales']-(float)$value['diskon_item']);
                $tax = $tax + ((float)$value['tax']);
                $srv = $srv + ((float)$value['service']);
            }

            $body[$end] = array('TOTAL','','','',$qt,$gs,$di,$ns,$tax,$srv,$ns+$tax+$srv);
            array_push($header['merge'], 'A'.($end+6).':D'.($end+6));
            $header['font']['A'.($end+6).':K'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');
            $header['alignment']['A6:D'.($end+6)] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){

            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);

            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('barang', "*", "kd_brg = '".$id."'");

            $where = "mt.HR='S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg='".$id."'";
            $where .= " AND ".$tgl;

            $where .= ($lokasi==null)?"":($where == null) ? null : $where .= " and "; $where.="mt.Lokasi='".$lokasi."'";

            $data['report_detail'] = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt","mt.kd_trx, mt.tgl, SUM(dt.qty) qty, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon",$where,"mt.kd_trx DESC","mt.kd_trx, mt.tgl");

            $count = count($data['report_detail']);

            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Detail Laporan Penjualan By Barang</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="22%"></th>
									<th width="2%"></th>
									<th width="23%"></th>

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
									<td colspan="3"><b>'.$periode.'</b></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Barcode</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['barcode'].'</td>
									<td></td>
									<td colspan="3"><b>'.$q_lokasi.'</b></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Nama Barang</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['nm_brg'].'</td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td><b>'.$this->menu_group['as_deskripsi'].'</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['Deskripsi'].'</td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>55,'bottom'=>(($count>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }
        else {

            $page = ($id==null?1:$id);
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
//            $config['total_rows'] = $this->m_crud->count_data_over("Master_Trx mt, Det_Trx dt, barang br", 'br.kd_brg', ($where==null)?null:$where, "br.kd_brg asc", "br.kd_brg");
            $config['total_rows'] = $this->m_crud->count_join_data("Master_Trx mt", 'br.kd_brg',$join,$on, ($where==null)?null:$where, "br.kd_brg asc", "br.kd_brg");
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

            $data['report'] = $this->m_crud->select_limit_join(
                "Master_Trx mt",
                "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan, SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service",
                $join,$on,
                ($where==null)?null:$where, $set_urutkan,
                "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan", ($page-1)*$config['per_page']+1, ($config['per_page']*$page)
            );

            $row = $this->m_crud->get_join_data(
                "Master_Trx mt",
                "SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service",
                $join,$on,
                ($where==null)?null:$where
            );

            $tqt = (int)$row['qty_jual'];
            $tgs = (float)$row['gross_sales'];
            $tdi = (float)$row['diskon_item'];
            $tns = ((float)$row['gross_sales']-(float)$row['diskon_item']);
            $tax = (float)$row['tax'];
            $srv = (float)$row['service'];

            $data['tqt'] = $tqt;
            $data['tgs'] = $tgs;
            $data['tdi'] = $tdi;
            $data['tns'] = $tns;
            $data['ttax'] = $tax;
            $data['tsrv'] = $srv;
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function detail_penjualan_by_barang(){
        $lokasi  = $_POST['lokasi'];
        $tanggal = $_POST['tanggal'];
        $periode = 'Periode : None';
        $q_lokasi = 'Semua Lokasi';
        $explode_date = explode(' - ', $tanggal);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);

        $where = "mt.HR='S' AND dt.kd_brg='".$_POST['kd_brg']."'";

        if (isset($tanggal) && $tanggal != null) {
            ($where == null) ? null : $where .= " and ";
            $where.= "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
        }else{
            ($where == null) ? null : $where .= " and ";
            $where.="LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }

        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi = '".$lokasi."'"; $q_lokasi = "Lokasi : ".$lokasi."";
        } else {
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi in (".$this->lokasi_in.")";
        }

        $join = array(array("type"=>"left","table"=>"Det_Trx dt"));
        $on = array("dt.kd_trx=mt.kd_trx");
        $head = ""; $body = ""; $footer = "";
        $no = 0;
        $total_qty = 0;
        $total_gross_sales = 0;
        $total_diskon_item = 0;
        $total_net_sales = 0;

        $read_data = $this->m_crud->join_data("Master_Trx mt","mt.kd_trx, mt.tgl, SUM(dt.qty) qty, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon",$join,$on,$where,"mt.kd_trx DESC","mt.kd_trx, mt.tgl");

        $head.=/** @lang text */'
            <div class="col-sm-8">
                <div class="col-sm-4"><b>Kode Barang</b></div><div class="col-sm-8"><b> : </b><b id="kd_brg">'.$_POST["kd_brg"].'</b></div>
                <div class="col-sm-4"><b>Barcode</b></div><div class="col-sm-8"><b> : </b><b>'.$_POST["barcode"].'</b></div>
                <div class="col-sm-4"><b>Nama Barang</b></div><div class="col-sm-8"><b> : </b><b>'.$_POST["nm_brg"].'</b></div>
                <div class="col-sm-4"><b>Deskripsi</b></div><div class="col-sm-8"><b> : </b><b>-</b></div>
            </div>
            <div class="col-sm-4">
                <div class="row">
                    <b>'.$tgl_awal.' - '.$tgl_akhir.'<b>
                </div>
                <div class="row">
                    <b>'.$q_lokasi.'<b>
                </div>
            </div>
        ';

        foreach($read_data as $row){$no++;
            $body.= /** @lang text */'
                <tr>
                    <td>'.$no.'</td>
                    <td>'.$row["kd_trx"].'</td>
                    <td>'.substr($row["tgl"], 0, 10).'</td>
                    <td class="text-center">'.($row["qty"]+0).'</td>
                    <td class="text-right">'.number_format($row["gross_sales"],2).'</td>
                    <td class="text-right">'.number_format($row["diskon"],2).'</td>
                    <td class="text-right">'.number_format($row["gross_sales"]-$row["diskon"],2).'</td>
                </tr>
            ';
            $total_qty = $total_qty + ($row['qty']+0);
            $total_gross_sales = $total_gross_sales + $row['gross_sales'];
            $total_diskon_item = $total_diskon_item + $row['diskon'];
            $total_net_sales = $total_net_sales + ($row['gross_sales']-$row['diskon']);
        }
        $footer.=/** @lang text */'
            <tr>
                <th colspan="3">TOTAL</th>
                <th class="text-center">'.$total_qty.'</th>
                <th class="text-right">'.number_format($total_gross_sales, 2).'</th>
                <th class="text-right">'.number_format($total_diskon_item, 2).'</th>
                <th class="text-right">'.number_format($total_net_sales, 2).'</th>
            </tr>
        ';
        echo json_encode(array("head"=>$head,"body"=>$body,"footer"=>$footer));
    }

    public function penjualan_by_group2($action = null, $id = null){
        $this->access_denied(159);
        $data = $this->data;
        $function = 'penjualan_by_group2';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = 'Group2';
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Penjualan By '.$this->menu_group['as_group2'];
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "mt.HR = 'S' AND dt.qty>0";
        $tgl = ''; $periode = 'Periode : None'; $q_lokasi = 'Semua Lokasi';

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $tgl = "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'"; $where .=$tgl;
        }else{
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
            ($where == null) ? null : $where .= " and ";
            $tgl = "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'"; $where .=$tgl;
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi = '".$lokasi."'"; $q_lokasi = "Lokasi : ".$lokasi."";
        } else {
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi in (".$this->lokasi_in.")";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(gr.Kodelike '%".$search."%' or gr.Nama like '%".$search."%')"; }


        $data['tgl'] = $tgl;
        $data['periode'] = $periode;
        $data['q_lokasi'] = $q_lokasi;
        $data['lokasi'] = $lokasi;
        $data['tgl_awal'] = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;

        $join = array(array("type"=>"left","table"=>"Det_Trx dt"),array("type"=>"left","table"=>"barang br"),array("type"=>"left","table"=>"Group2 gr"));
        $on = array("mt.kd_trx=dt.kd_trx","br.kd_brg=dt.kd_brg","br.Group2=gr.Kode");

        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->join_data(
                'Master_Trx mt',
                "gr.Kode, gr.Nama, SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service",
                $join,$on,
                ($where==null?null:$where), 'gr.kode ASC', "gr.Kode, gr.Nama"
            );
            // $baca = $this->m_crud->read_data('Master_Trx mt, Det_Trx dt, barang br, Group2 gr', "gr.Kode, gr.Nama, SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service", ($where==null?null:$where), 'gr.kode ASC', "gr.Kode, gr.Nama");
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
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Kode '.$this->menu_group['as_group2'], 'B'=>'Nama '.$this->menu_group['as_group2'], 'C'=>'Qty Terjual', 'D'=>'Sub Total', 'E'=>'Diskon Item', 'F'=>'Net Sales', 'G'=>'Tax', 'H'=>'Service', 'I'=>'Gross Sales'
                )
            );

            $end = 0;
            $qt = 0; $gs = 0; $di = 0; $ns = 0; $tax = 0; $srv = 0;
            foreach($baca as $row => $value){
                $end++;
                $body[$row] = array(
                    $value['Kode'], $value['Nama'], ($value['qty_jual']+0), $value['gross_sales'], $value['diskon_item'], ($value['gross_sales']-$value['diskon_item']), $value['tax'], $value['service'], ($value['gross_sales']-$value['diskon_item']+$value['tax']+$value['service'])
                );
                $qt = $qt + (int)$value['qty_jual'];
                $gs = $gs + (float)$value['gross_sales'];
                $di = $di + (float)$value['diskon_item'];
                $ns = $ns + ((float)$value['gross_sales']-(float)$value['diskon_item']);
                $tax = $tax + ((float)$value['tax']);
                $srv = $srv + ((float)$value['service']);
            }

            $body[$end] = array('TOTAL','',$qt,$gs,$di,$ns,$tax,$srv,$ns+$tax+$srv);
            array_push($header['merge'], 'A'.($end+6).':B'.($end+6));
            $header['font']['A'.($end+6).':I'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');
            $header['alignment']['A6:B'.($end+6)] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){

            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);

            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('Group2', "*", "Kode = '".$id."'");

            $where = "mt.HR='S' AND mt.kd_trx=dt.kd_trx AND dt.qty>0 AND dt.kd_brg=br.kd_brg AND br.Group2='".$id."'";
            $where .= " AND ".$tgl;
            $where .= ($lokasi==null)?"":$where.=" AND mt.Lokasi='".$lokasi."'";

            $data['report_detail'] = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br","mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, SUM(dt.qty) qty, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon",$where,"mt.kd_trx DESC","mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi");

            $count = count($data['report_detail']);

            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Detail Laporan Penjualan By '.$this->menu_group['as_group2'].'</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="22%"></th>
									<th width="2%"></th>
									<th width="23%"></th>
									
									<th width="10%"></th>
									<th width="12%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Kode '.$this->menu_group['as_group2'].'</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['Kode'].'</td>
									<td></td>
									<td colspan="3"><b>'.$periode.'</b></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Nama '.$this->menu_group['as_group2'].'</b></td>
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
                'mode'=>'utf-8','paper'=>'A4-L','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>55,'bottom'=>(($count>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }
        else {
            $page = ($id==null?1:$id);
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_join_data("Master_Trx mt", 'gr.Kode',$join,$on, ($where==null)?null:$where, "gr.Kode asc", "gr.Kode");
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

            $data['report'] = $this->m_crud->select_limit_join(
                'Master_Trx mt',
                "gr.Kode, gr.Nama, SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service",
                $join,$on,
                ($where==null?null:$where), 'gr.kode ASC', "gr.Kode, gr.Nama", ($page-1)*$config['per_page']+1, ($config['per_page']*$page)
            );

            $row = $this->m_crud->get_join_data(
                "Master_Trx mt",
                "SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service",
                $join,$on,
                ($where==null)?null:$where
            );

            $tqt = (int)$row['qty_jual'];
            $tgs = (float)$row['gross_sales'];
            $tdi = (float)$row['diskon_item'];
            $tns = ((float)$row['gross_sales']-(float)$row['diskon_item']);
            $tax = (float)$row['tax'];
            $srv = (float)$row['service'];

            $data['tqt'] = $tqt;
            $data['tgs'] = $tgs;
            $data['tdi'] = $tdi;
            $data['tns'] = $tns;
            $data['ttax'] = $tax;
            $data['tsrv'] = $srv;
        }

        if($this->form_validation->run() == false) { $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }


    public function detail_penjualan_by_group2(){
        $kode = $_POST['kode']; $lokasi=$_POST['lokasi']; $tanggal = $_POST['tanggal'];
        $where = "mt.HR='S' AND dt.qty>0 AND br.Group2='".$kode."'";
        $periode = 'Periode : None'; $q_lokasi = 'Semua Lokasi';
        $explode_date = explode(' - ', $tanggal);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);
        if (isset($tanggal) && $tanggal != null) {
            ($where == null) ? null : $where .= " and ";
            $where.="LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
        }else{
            ($where == null) ? null : $where .= " and ";
            $where.= "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi = '".$lokasi."'"; $q_lokasi = "Lokasi : ".$lokasi."";
        } else {
            ($where==null)?null:$where.=" and "; $where.="mt.Lokasi in (".$this->lokasi_in.")";
        }

        $no = 0;$body="";$footer="";
        $total_qty = 0;
        $total_gross_sales = 0;
        $total_diskon_item = 0;
        $total_net_sales = 0;

        $read_data = $this->m_crud->join_data(
            "Master_Trx mt",
            "mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, SUM(dt.qty) qty, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon",
            array(array("type"=>"left","table"=>"Det_Trx dt"),array("type"=>"left","table"=>"barang br")),
            array("mt.kd_trx=dt.kd_trx","dt.kd_brg=br.kd_brg"),
            ($where==null?null:$where),"mt.kd_trx DESC","mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi"
        );
        if($read_data!=null){
            foreach($read_data as $rows){ $no++;
                $body.= /** @lang text */'
                    <tr>
                        <td>'.$no.'</td>
                        <td>'.$rows["kd_trx"].'</td>
                        <td>'.substr($rows["tgl"], 1, 10).'</td>
                        <td>'.$rows["kd_brg"].'</td>
                        <td>'.$rows["barcode"].'</td>
                        <td>'.$rows["nm_brg"].'</td>
                        <td>'.$rows["Deskripsi"].'</td>
                        <td class="text-center">'.($rows["qty"]+0).'</td>
                        <td class="text-right">'.number_format($rows["gross_sales"],2).'</td>
                        <td class="text-right">'.number_format($rows["diskon"],2).'</td>
                        <td class="text-right">'.number_format($rows["gross_sales"]-$rows["diskon"],2).'</td>
                    </tr>
                ';
                $total_qty = $total_qty + ($rows['qty']+0);
                $total_gross_sales = $total_gross_sales + $rows['gross_sales'];
                $total_diskon_item = $total_diskon_item + $rows['diskon'];
                $total_net_sales = $total_net_sales + ($rows['gross_sales']-$rows['diskon']);
            }
            $footer.= /** @lang text */'
                <tr>
                    <th colspan="7">TOTAL</th>
                    <th class="text-center">'.$total_qty.'</th>
                    <th class="text-right">'.number_format($total_gross_sales).'</th>
                    <th class="text-right">'.number_format($total_diskon_item).'</th>
                    <th class="text-right">'.number_format($total_net_sales).'</th>
                </tr>
            ';
        }

        echo json_encode(array("body"=>$body,"footer"=>$footer));


    }

    public function penjualan_by_kassa($action = null, $id = null){
        $this->access_denied(160);
        $datax = $this->data;
        $function = 'penjualan_by_kassa';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = 'Group1';
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $datax['title'] = 'Laporan Penjualan By Kassa';
        $datax['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $datax['content'] = $view.$function;
        $datax['table'] = $table;

        $where = null;

        $tanggal = date('Y-m-d');

        if(isset($_POST['search'])||$action=='print'){
            $id = json_decode(base64_decode($id),true);
            $this->session->set_userdata('search', array('tgl_periode' => (isset($_POST['search']))?$_POST['tgl_periode']:$id['tgl_periode'], 'lokasi' => (isset($_POST['search']))?$_POST['lokasi']:$id['lokasi']));
        }

        $lokasi = $this->session->search['lokasi']; $ses_tgl_periode = $this->session->search['tgl_periode'];

        if($ses_tgl_periode != null){
            $tanggal = $ses_tgl_periode;
            ($where==null)?null:$where.=" and "; $where.="(LEFT(CONVERT(VARCHAR, Tanggal, 120), 10) = '".$tanggal."')";
        }  else {
            ($where==null)?null:$where.=" and "; $where.="(LEFT(CONVERT(VARCHAR, Tanggal, 120), 10) = '".$tanggal."')";
        }

        if($lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="Lokasi = '".$lokasi."'";
            $read_setoran = $this->m_crud->read_data("Setoran", "*", ($where==null?null:$where));
        } else {
            $read_setoran = array();
        }


        $response = array();

        if (count($read_setoran) != 0) {

            $baris = array();
            $response["sales_report_by_financial"] = array();
            $total_outcome = 0;
            $net_omset = 0;

            foreach ($read_setoran as $row1){
                $data = array();

                $id_kasir = $row1["Kd_Kasir"];
                $tgl = $tanggal;
                $kassa = $row1["Kassa"];
                $cashier_cash = $row1["Setoran_tunai"]+null;
                $data["Kassa"]=$kassa;

                $query_total_main = $this->m_crud->read_data("Master_Trx", "SUM(Master_Trx.GT) AS total,SUM(Master_Trx.ST) AS subtotal,SUM(Master_Trx.dis_rp) as diskon_total,SUM(Master_Trx.tax) as tax_total, SUM(Master_Trx.bayar-Master_Trx.change) as cash_total, SUM(Master_Trx.jml_kartu) as edc_total, SUM(Master_Trx.kas_lain) as kas_lain", "Master_Trx.Lokasi='".$lokasi."' AND Master_Trx.kd_kasir = '".$id_kasir."' AND Master_Trx.kassa = '".$kassa."' AND HR = 'S' AND LEFT(CONVERT(VARCHAR, Master_Trx.tgl, 120), 10) BETWEEN '".$tgl."' AND '".$tgl."'");

                $query_disc_item = $this->m_crud->read_data("Det_Trx, Master_Trx","SUM(Det_Trx.dis_persen) as diskon_item","Master_Trx.kd_trx = Det_Trx.kd_trx AND Master_Trx.Lokasi = '".$lokasi."' AND Master_Trx.kd_kasir = '".$id_kasir."' AND Master_Trx.kassa = '".$kassa."' AND HR = 'S' AND LEFT(CONVERT(VARCHAR, Master_Trx.tgl, 120), 10) BETWEEN '".$tgl."' AND '".$tgl."'");
                $query_receive_amount = $this->m_crud->read_data("kas_masuk","SUM(jumlah) as receive_amount","Lokasi = '".$lokasi."' AND kd_kasir = '".$id_kasir."' AND tgl between '".$tgl."' and '".$tgl."'");

                $query_total_sales = $this->m_crud->read_data("Det_Trx,Master_Trx","SUM(Det_Trx.hrg_jual*Det_Trx.qty) as total_sales","Det_Trx.kd_trx = Master_Trx.kd_trx AND Master_Trx.Lokasi = '".$lokasi."' AND Master_Trx.kd_kasir = '".$id_kasir."' AND Master_trx.kassa = '".$kassa."' AND HR = 'S' AND LEFT(CONVERT(VARCHAR, Master_Trx.tgl, 120), 10) between '".$tgl."' and '".$tgl."'");
                $query_return = $this->m_crud->read_data("Det_Trx,Master_Trx","SUM(Det_Trx.hrg_jual*Det_Trx.qty) as total_return","Det_Trx.kd_trx = Master_Trx.kd_trx AND Det_Trx.qty <0 AND Master_Trx.Lokasi = '".$lokasi."' AND Master_Trx.kd_kasir = '".$id_kasir."'  AND Master_trx.kassa = '".$kassa."'  AND HR = 'S'  AND LEFT(CONVERT(VARCHAR, Master_Trx.tgl, 120), 10) between '".$tgl."' and '".$tgl."'");
                $query_paid_out = $this->m_crud->read_data("kas_keluar","SUM(jumlah) as paid_out","Lokasi = '".$lokasi."' AND kd_kasir = '".$id_kasir."' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) between '".$tgl."' and '".$tgl."'");

                foreach ($query_total_main as $baris){

                    foreach ($query_total_sales as $baris_total_sales){

                        if(empty(str_replace('.0000', '', $baris_total_sales["total_sales"]))){
                            $data["total_sales"]="0";
                        }else {
                            $data["total_sales"]=str_replace('.0000', '', $baris_total_sales["total_sales"]);
                        }

                        $net_omset = $baris_total_sales["total_sales"];

                    }


                    if(empty(str_replace('.0000', '', $baris["diskon_total"]))){
                        $data["diskon_total"]="0";
                    }else {
                        $data["diskon_total"]=str_replace('.0000', '', $baris["diskon_total"]);
                    }

                    $tax = 	0;


                    if(empty(str_replace('.0000', '', $tax))){
                        $data["tax_total"]="0";
                    }else {
                        $data["tax_total"]=str_replace('.0000', '', $tax);
                    }


                    if(empty(str_replace('.0000', '', $baris["edc_total"]))){
                        $data["edc_total"]="0";
                    }else {
                        $data["edc_total"]=str_replace('.0000', '', $baris["edc_total"]);
                    }

                    if(empty(str_replace('.0000', '', $baris["kas_lain"]))){
                        $data["other_income"]="0";
                    }else {
                        $data["other_income"]=str_replace('.0000', '', $baris["kas_lain"]);
                    }


                    foreach ($query_receive_amount as $baris1){

                        if(empty(str_replace('.0000', '', $baris1["receive_amount"]))){
                            $data["receive_amount"]="0";
                        }else {
                            $data["receive_amount"]=str_replace('.0000', '', $baris1["receive_amount"]);
                        }

                    }

                    $total_income = $baris1["receive_amount"] + $baris["kas_lain"];

                    $data["total_income"] = $total_income;




                    foreach ($query_disc_item as $baris1){

                        if(empty(str_replace('.0000', '', $baris1["diskon_item"]))){
                            $data["diskon_item"]="0";
                        }else {
                            $data["diskon_item"]=str_replace('.0000', '', $baris1["diskon_item"]);
                        }

                    }

                    $net_omset2 = $net_omset - ($data["diskon_item"] + $data["diskon_total"]);


                    $data["net_omset"] = $net_omset2;


                    $data["total_omset"] = $net_omset2+$tax;

                    $cash_total = ($net_omset2+$tax)-$baris["edc_total"];

                    $data["cash_total"]=$cash_total;

                    $cash_in_hand = $cash_total+ $total_income;

                    $data["cash_in_hand"] = $cash_in_hand;


                    foreach ($query_return as $baris){

                        if(empty(str_replace('.0000', '', $baris["total_return"]))){
                            $data["return"]="0";
                        }else {
                            $data["return"]=str_replace('.0000', '', $baris["total_return"]);
                        }

                        foreach ($query_paid_out as $baris2){

                            if(empty(str_replace('.0000', '', $baris2["paid_out"]))){
                                $data["paid_out"]="0";
                            }else {
                                $data["paid_out"]=str_replace('.0000', '', $baris2["paid_out"]);
                            }

                            $total_outcome = $baris2["paid_out"] + $baris["total_return"];

                            $data["total_outcome"] =  $total_outcome;

                            $total_cash_sales = $cash_in_hand - $baris2["paid_out"];

                            $data["total_cash_sales"] = $total_cash_sales;

                        }

                        $data["cashier_cash"]= $cashier_cash  ;

                        if(($cashier_cash - $total_cash_sales)==0){

                            $data["status_report"] = 'Balance';

                        }else if(($cashier_cash - $total_cash_sales)<0){

                            $data["status_report"] = 'Deficit ('.(number_format(($cashier_cash - $total_cash_sales),0,".",".")).')';

                        }else {

                            $data["status_report"] = 'Surplus ('.(number_format(($cashier_cash - $total_cash_sales),0,".",".")).')';

                        }

                    }

                }

                array_push($response["sales_report_by_financial"], $data);

            }

            $datax['status'] = 1;
            $datax['result'] = $response;
        } else {
            $datax['status'] = 0;
        }

        if($action=='print'){

            $datax['content'] = $view.'pdf_invoice_'.$function;

            $count = count($datax['result']["sales_report_by_financial"]);

            $datax['report_detail'] = $datax['result'];

            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='to_pdf')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $datax['title']).'-'.str_replace('/', '-', 'cashier_report'));
            $html = $this->load->view('bo/'.$datax['content'], $datax, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Detail Laporan Penjualan By Kassa</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="22%"></th>
									<th width="2%"></th>
									<th width="23%"></th>
									
									<th width="10%"></th>
									<th width="12%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Periode</b></td>
									<td><b>:</b></td>
									<td>'.$tanggal.'</td>
									<td></td>
									<td colspan="3"></td>
								</tr>
							</tbody>
						</table>
					</div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>45,'bottom'=>(($count>$datax['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $datax); }
        else { $this->load->view('bo/index', $datax); }
    }

    public function penjualan_by_sales($action = null, $id = null){
        $this->access_denied(161);
        $data = $this->data;
        $function = 'penjualan_by_sales';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = 'Group1';
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Penjualan By Sales';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "rt.kd_sales=sl.kode";
        $tgl = ''; $periode = 'Periode : None'; $q_lokasi = 'Semua Lokasi';

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rt.tgl, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
        } else {
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="rt.Lokasi = '".$lokasi."'"; $q_lokasi = "Lokasi : ".$lokasi."";
        } else {
            ($where==null)?null:$where.=" and "; $where.="rt.Lokasi in (".$this->lokasi_in.")";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(sl.Kode like '%".$search."%' or sl.Nama like '%".$search."%')"; }

        $data['tgl'] = $tgl;
        $data['periode'] = $periode;
        $data['q_lokasi'] = $q_lokasi;
        $data['lokasi'] = $lokasi;
        $data['tgl_awal'] = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;

        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->read_data("report_trx rt, Sales sl", "sl.Kode, sl.Nama, SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null)?null:$where, "sl.Kode asc", "sl.Kode, sl.Nama");
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
                    'A'=>'Kode Sales', 'B'=>'Nama Sales', 'C'=>'Qty Terjual', 'D'=>'Sub Total', 'E'=>'Diskon Item', 'F'=>'Diskon Transaksi', 'G'=>'Net Sales', 'H'=>'Tax', 'I'=>'Service', 'J'=>'Gross Sales'
                )
            );

            $end = 0;
            $qt = 0; $di = 0; $dt = 0; $gs = 0; $tax = 0; $srv = 0; $tns = 0;
            foreach($baca as $row => $value){
                $end++;
                $ns = $value['gross_sales']-$value['diskon_item']-$value['diskon_trx']+0;
                $body[$row] = array(
                    $value['Kode'], $value['Nama'], $value['qty']+0, $value['gross_sales']+0, $value['diskon_item']+0, $value['diskon_trx']+0, $ns, $value['tax'], $value['service'], $ns+$value['tax']+$value['service']
                );
                $qt = $qt + (int)$value['qty'];
                $gs = $gs + (float)$value['gross_sales'];
                $di = $di + (float)$value['diskon_item'];
                $dt = $dt + (float)$value['diskon_trx'];
                $tax = $tax + (float)$value['tax'];
                $srv = $srv + (float)$value['service'];
                $tns = $tns + $ns;
            }

            $body[$end] = array('TOTAL','',$qt,$gs,$di,$dt,$tns,$tax,$srv,$tns+$tax+$srv);
            array_push($header['merge'], 'A'.($end+6).':B'.($end+6));
            $header['font']['A'.($end+6).':J'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){

            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);

            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('Customer', "*", "kd_cust = '".$id."'");

            ($tgl == null)?$q_tgl="":$q_tgl="AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";
            $where = ""; ($lokasi==null)?"":$where.=" AND mt.Lokasi='".$lokasi."'";
            $qty = "isnull((SELECT qty FROM Det_Trx WHERE qty>0 AND kd_trx=mt.kd_trx AND kd_brg=dt.kd_brg GROUP BY kd_trx, qty), 0) qty";
            $qty_retur = "isnull((SELECT qty FROM Det_Trx WHERE qty<0 AND kd_trx=mt.kd_trx AND kd_brg=dt.kd_brg GROUP BY kd_trx, qty)*-1, 0) qty_retur";
            $row = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, dt.hrg_jual, dt.dis_persen, ".$qty.", ".$qty_retur, "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND mt.kd_cust = '".$id."' ".$q_tgl.$where."", "mt.kd_trx ASC", null);

            $count = count($row);
            $data['loop'] = $count/100;
            for ($x=1; $x<=$data['loop']; $x++) {
                $data['report_detail'][$x] = $this->m_crud->select_limit("Det_Trx dt, barang br, Master_Trx mt", "mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, dt.hrg_jual, dt.dis_persen, ".$qty.", ".$qty_retur, "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND mt.kd_cust = '".$id."' ".$q_tgl.$where."", "mt.kd_trx ASC", null, ($x-1)*100+1, 100*$x);
            }

            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Detail Laporan Penjualan By Sales</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="22%"></th>
									<th width="2%"></th>
									<th width="23%"></th>
									
									<th width="10%"></th>
									<th width="12%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Kode Sales</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['Kode'].'</td>
									<td></td>
									<td colspan="3"><b>'.$periode.'</b></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Nama Sales</b></td>
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
                'left'=>10,'right'=>10,'top'=>50,'bottom'=>(($count>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        } else {
            $page = ($id==null?1:$id);
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data("report_trx rt, Sales sl", 'sl.Kode', ($where==null)?null:$where, "sl.Kode asc", "sl.Kode");
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

            $data['report'] = $this->m_crud->select_limit("report_trx rt, Sales sl", "sl.Kode, sl.Nama, SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null)?null:$where, "sl.Kode asc", "sl.Kode, sl.Nama", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

            $row = $this->m_crud->get_data("report_trx rt, Sales sl", "SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null)?null:$where);

            $data['tqt'] = $row['qty'];
            $data['tgs'] = $row['gross_sales'];
            $data['tdi'] = $row['diskon_item'];
            $data['tdt'] = $row['diskon_trx'];
            $data['tns'] = $row['gross_sales']-$row['diskon_item']-$row['diskon_trx'];
            $data['ttax'] = $row['tax'];
            $data['tsrv'] = $row['service'];
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function detail_by_sales($kode, $tgl, $lokasi=null) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $kode = base64_decode($kode);
        $tgl = base64_decode($tgl);
        $explode_date = explode(' - ', $tgl);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);

        $ket_lokasi = 'Semua Lokasi';
        $periode = $tgl_awal.' - '.$tgl_akhir;
        if ($lokasi!=null) {
            $lokasi = base64_decode($lokasi);
            $where = " AND mt.Lokasi='".$lokasi."'";
            $ket_lokasi = $lokasi;
        }

        $get_nama = $this->m_crud->get_data("Sales", "Nama", "Kode='".$kode."'");

        ($tgl == null)?$q_tgl="":$q_tgl="AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";
        $qty = "isnull((SELECT qty FROM Det_Trx WHERE qty>0 AND kd_trx=mt.kd_trx AND kd_brg=dt.kd_brg GROUP BY kd_trx, qty), 0) qty";
        $qty_retur = "isnull((SELECT qty FROM Det_Trx WHERE qty<0 AND kd_trx=mt.kd_trx AND kd_brg=dt.kd_brg GROUP BY kd_trx, qty)*-1, 0) qty_retur";
        $detail = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, dt.hrg_jual, dt.dis_persen, ".$qty.", ".$qty_retur, "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND mt.kd_sales = '".$kode."' ".$q_tgl.$where."");

        $no = 1;
        $jumlah = 0;
        $total_qty_kirim = 0;
        $total_qty_retur = 0;
        $total_qty_laku = 0;
        $total_diskon_persen = 0;
        $total_sub_total = 0;
        $list = '';
        foreach ($detail as $rows){
            $total_qty_kirim = $total_qty_kirim + ($rows['qty']+0);
            $total_qty_retur = $total_qty_retur + ($rows['qty_retur']+0);
            $total_qty_laku = $total_qty_laku + ($rows['qty']-$rows['qty_retur']+0);
            $total_diskon_persen = $total_diskon_persen + $rows['dis_persen'];
            $total_sub_total = $total_sub_total + (($rows['qty']-$rows['qty_retur'])*$rows['hrg_jual'])-$rows['dis_persen'];
            $list .= '
                <tr>
                    <td>'.$no.'</td>
                    <td>'.$rows['kd_trx'].'</td>
                    <td>'.substr($rows['tgl'], 1, 10).'</td>
                    <td>'.$rows['kd_brg'].'</td>
                    <td>'.$rows['nm_brg'].'</td>
                    <td>'.$rows['nm_brg'].'</td>
                    <td>'.$rows['Deskripsi'].'</td>
                    <td class="text-center">'.($rows['qty']+0).'</td>
                    <td class="text-center">'.($rows['qty_retur']+0).'</td>
                    <td class="text-center">'.($rows['qty']-$rows['qty_retur']+0).'</td>
                    <td class="text-right">'.number_format($rows['hrg_jual']).'</td>
                    <td class="text-right">'.number_format($rows['dis_persen']).'</td>
                    <td class="text-right">'.number_format((($rows['qty']-$rows['qty_retur'])*$rows['hrg_jual'])-$rows['dis_persen']).'</td>
                </tr>
            ';
            $no++;
        }
        $list .= '
            <tr>
                <th colspan="7">Total</th>
                <th class="text-center">'.$total_qty_kirim.'</th>
                <th class="text-center">'.$total_qty_retur.'</th>
                <th class="text-center">'.$total_qty_laku.'</th>
                <th></th>
                <th class="text-right">'.number_format($total_diskon_persen, 2).'</th>
                <th class="text-right">'.number_format($total_sub_total, 2).'</th>
            </tr>
        ';

        echo json_encode(array('list' => $list, 'det' => array('kode'=>$kode, 'nama'=>$get_nama['Nama'], 'lokasi'=>$ket_lokasi, 'periode'=>$periode)));
    }

    public function penjualan_by_edc($action = null, $id = null){
        $this->access_denied(162);
        $data = $this->data;
        $function = 'penjualan_by_edc';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = 'Group1';
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Penjualan By EDC';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "rt.kartu<>'-' and rt.jenis_trx='Non Tunai'";
        $tgl = ''; $periode = 'Periode : None'; $q_lokasi = 'Semua Lokasi';

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $tgl_awal = str_replace('/','-',$explode_date[0]);
        $tgl_akhir = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rt.tgl, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
        }else{
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, rt.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){
            ($where==null)?null:$where.=" and "; $where.="rt.Lokasi = '".$lokasi."'"; $q_lokasi = "Lokasi : ".$lokasi."";
        } else {
            ($where==null)?null:$where.=" and "; $where.="rt.Lokasi in (".$this->lokasi_in.")";
        }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(rt.kartu '%".$search."%')"; }

        $data['tgl'] = $tgl;
        $data['periode'] = $periode;
        $data['q_lokasi'] = $q_lokasi;
        $data['lokasi'] = $lokasi;
        $data['tgl_awal'] = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;

        if(isset($_POST['to_excel'])){
            $baca = $this->m_crud->read_data("report_trx rt", "rt.kartu, rt.jns_kartu, SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null)?null:$where, "rt.kartu asc", "rt.kartu, rt.jns_kartu");
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
                    'A'=>'Nama Bank', 'B'=>'Jenis Kartu', 'C'=>'Qty Terjual', 'D'=>'Sub Total', 'E'=>'Diskon Item', 'F'=>'Diskon Transaksi', 'G'=>'Net Sales', 'H'=>'Tax', 'I'=>'Service', 'J'=>'Gross Sales'
                )
            );

            $end = 0;
            $qt = 0; $di = 0; $dt = 0; $gs = 0; $tax = 0; $srv = 0; $tns = 0;
            foreach($baca as $row => $value){
                $end++;
                $ns = $value['gross_sales']-$value['diskon_item']-$value['diskon_trx']+0;
                $body[$row] = array(
                    $value['kartu'], $value['jns_kartu'], $value['qty']+0, $value['gross_sales']+0, $value['diskon_item']+0, $value['diskon_trx']+0, $ns, $value['tax'], $value['service'], $ns+$value['tax']+$value['service']
                );
                $qt = $qt + (int)$value['qty'];
                $gs = $gs + (float)$value['gross_sales'];
                $di = $di + (float)$value['diskon_item'];
                $dt = $dt + (float)$value['diskon_trx'];
                $tax = $tax + (float)$value['tax'];
                $srv = $srv + (float)$value['service'];
                $tns = $tns + $ns;
            }

            $body[$end] = array('TOTAL','',$qt,$gs,$di,$dt,$tns,$tax,$srv,$tns+$tax+$srv);
            array_push($header['merge'], 'A'.($end+6).':B'.($end+6).'');
            $header['font']['A'.($end+6).':J'.($end+6)] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){

            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);

            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('Customer', "*", "kd_cust = '".$id."'");

            ($tgl == null)?$q_tgl="":$q_tgl="AND mt.tgl >= '".$tgl_awal." 00:00:00' and mt.tgl <= '".$tgl_akhir." 23:59:59'";
            $where = ""; ($lokasi==null)?"":$where.=" AND mt.Lokasi='".$lokasi."'";
            $qty = "isnull((SELECT qty FROM Det_Trx WHERE qty>0 AND kd_trx=mt.kd_trx AND kd_brg=dt.kd_brg GROUP BY kd_trx, qty), 0) qty";
            $qty_retur = "isnull((SELECT qty FROM Det_Trx WHERE qty<0 AND kd_trx=mt.kd_trx AND kd_brg=dt.kd_brg GROUP BY kd_trx, qty)*-1, 0) qty_retur";
            $row = $this->m_crud->read_data("Det_Trx dt, barang br, Master_Trx mt", "mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, dt.hrg_jual, dt.dis_persen, ".$qty.", ".$qty_retur, "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND mt.kd_cust = '".$id."' ".$q_tgl.$where."", "mt.kd_trx ASC", null);

            $count = count($row);
            $data['loop'] = $count/100;
            for ($x=1; $x<=$data['loop']; $x++) {
                $data['report_detail'][$x] = $this->m_crud->select_limit("Det_Trx dt, barang br, Master_Trx mt", "mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, dt.hrg_jual, dt.dis_persen, ".$qty.", ".$qty_retur, "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND mt.kd_cust = '".$id."' ".$q_tgl.$where."", "mt.kd_trx ASC", null, ($x-1)*100+1, 100*$x);
            }

            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Detail Laporan Penjualan By EDC</b></h3></div>
					<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="22%"></th>
									<th width="2%"></th>
									<th width="23%"></th>
									
									<th width="10%"></th>
									<th width="12%"></th>
									<th width="2%"></th>
									<th width="27%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Nama Bank</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['kartu'].'</td>
									<td></td>
									<td colspan="3"><b>'.$periode.'</b></td>
								</tr>
							</tbody>
						</table>
					</div>';
            $footer = null;
            $conf = array( //'paper'=>array(200,100)
                'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>50,'bottom'=>(($count>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        } else {
            $page = ($id==null?1:$id);
            $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
            $config['total_rows'] = $this->m_crud->count_data_over("report_trx rt", 'rt.kartu', ($where==null)?null:$where, "rt.kartu asc", "rt.kartu");
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

            $data['report'] = $this->m_crud->select_limit("report_trx rt", "rt.kartu, rt.jns_kartu, SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null)?null:$where, "rt.kartu asc", "rt.kartu, rt.jns_kartu", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

            $row = $this->m_crud->get_data("report_trx rt", "SUM(rt.qty) qty, SUM(rt.st) gross_sales, SUM(rt.disc_item) diskon_item, SUM(rt.dis_rp) diskon_trx, SUM(rt.tax) tax, SUM(rt.service) service", ($where==null)?null:$where);

            $data['tqt'] = $row['qty'];
            $data['tgs'] = $row['gross_sales'];
            $data['tdi'] = $row['diskon_item'];
            $data['tdt'] = $row['diskon_trx'];
            $data['tns'] = $row['gross_sales']-$row['diskon_item']-$row['diskon_trx'];
            $data['ttax'] = $row['tax'];
            $data['tsrv'] = $row['service'];
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }
    /*End modul report*/

    /*Start orders*/
    public function pesanan_online($action=null, $page=1) {
        $this->access_denied(192);
        $data = $this->data;
        $function = 'pesanan_online';
        $table = 'orders';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function) {
            $this->session->unset_userdata('search');
            $this->cart->destroy();
            $this->session->set_userdata($this->site . 'admin_menu', $function);
        }
        $data['main'] = 'Penjualan';
        $data['title'] = 'Pesanan Online';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $where = "o.status NOT IN ('0', '5')";

        if(isset($_POST['search'])||isset($_POST['to_excel'])) {
            $filter = $_POST['filter'];

            if ($filter == 'pagging') {
                $filter = $this->session->search['filter'];
            }

            $this->session->set_userdata('search', array('any' => $_POST['any'], 'filter' => $filter, 'date' => $_POST['date'], 'bank' => $_POST['bank'], 'periode' => $_POST['periode']));
        }

        $search = $this->session->search['any']; $filter = $this->session->search['filter']; $date = $this->session->search['date']; $bank = $this->session->search['bank']; $periode = $this->session->search['periode'];
        if(isset($search)&&$search!=null) {
            ($where == null) ? null : $where .= " AND ";
            $where .= "(o.id_orders like '%".$search."%' OR m.nama like '%".$search."%' OR p.penerima like '%".$search."%')";
        }

        if(isset($date)&&$date!=null) {
            $explode_date = explode(' - ', $date);
            $tgl_awal = $explode_date[0]; $tgl_akhir = $explode_date[1];
        } else {
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        }
        if($periode==null) {
            ($where == null) ? null : $where .= " AND ";
            $where .= "LEFT(CONVERT(VARCHAR, o.tgl_orders, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
        }

        if(isset($bank)&&$bank!=null) {
            ($where == null) ? null : $where .= " AND ";
            $where .= "pb.bank2 = '".$bank."'";
        }

        $where_filter = "";
        if (isset($filter)&&$filter!=null) {
            ($where == null) ? null : $where_filter .= " AND ";
            if ($filter == 'belum_bayar') {
                $where_filter .= "pb.status = '1'";
            } else if ($filter == 'belum_proses') {
                $where_filter .= "pb.status = '2'";
            } else if ($filter == 'belum_resi') {
                $where_filter .= "pb.status = '3' AND (p.no_resi = '' OR p.no_resi IS NULL)";
            } else if ($filter == 'belum_lacak') {
                $where_filter .= "o.status = '2' AND p.no_resi <> '' AND p.no_resi IS NOT NULL";
            } else if ($filter == 'dalam_proses') {
                $where_filter .= "o.status = '3'";
            } else if ($filter == 'berhasil') {
                $where_filter .= "o.status = '4'";
            }
        }

        if ($action == 'get_data') {
            $config = array();
            $config["base_url"] = "#";
            //$config["total_rows"] = $this->ajax_pagination_model->count_all();
            $config["total_rows"] = count($this->m_crud->join_data($table." o", "o.id_orders", array("det_orders do", "pengiriman p", "customer m", "det_pembayaran dp", "pembayaran pb", "bank b"), array("do.orders=o.id_orders", "p.orders=o.id_orders", "m.kd_cust=o.member", "dp.orders=o.id_orders", "pb.id_pembayaran=dp.pembayaran", "b.nama=pb.bank2"), $where.$where_filter, null, "o.id_orders"));
            $config["per_page"] = 10;
            $config["uri_segment"] = 4;
            $config["num_links"] = 5;
            $config["use_page_numbers"] = TRUE;
            $config["full_tag_open"] = '<ul class="pagination pagination-sm">';
            $config["full_tag_close"] = '</ul>';
            $config['first_link'] = '&laquo;';
            $config["first_tag_open"] = '<li>';
            $config["first_tag_close"] = '</li>';
            $config['last_link'] = '&raquo;';
            $config["last_tag_open"] = '<li>';
            $config["last_tag_close"] = '</li>';
            $config['next_link'] = '&gt;';
            $config["next_tag_open"] = '<li>';
            $config["next_tag_close"] = '</li>';
            $config["prev_link"] = "&lt;";
            $config["prev_tag_open"] = "<li>";
            $config["prev_tag_close"] = "</li>";
            $config["cur_tag_open"] = "<li class='active'><a href='#'>";
            $config["cur_tag_close"] = "</a></li>";
            $config["num_tag_open"] = "<li>";
            $config["num_tag_close"] = "</li>";
            $this->pagination->initialize($config);

            $start = ($page-1)*$config["per_page"]+1;
            $end = ($config["per_page"]*$page);

            $output = '';
            $read_data = $this->m_crud->select_limit_join($table." o", "o.id_orders, o.tgl_orders, o.status status_order, m.nama nama_pemesan, p.id_pengiriman, p.penerima, p.alamat, p.provinsi, p.kota, p.kecamatan, p.kode_pos, p.telepon, p.biaya, p.kurir, p.service, p.no_resi, pb.id_pembayaran, pb.bank_tujuan, pb.tgl_konfirmasi, pb.status status_pembayaran, pb.kode_unik, b.foto gambar_bank", array("det_orders do", "pengiriman p", "customer m", "det_pembayaran dp", "pembayaran pb", "bank b"), array("do.orders=o.id_orders", "p.orders=o.id_orders", "m.kd_cust=o.member", "dp.orders=o.id_orders", "pb.id_pembayaran=dp.pembayaran", "b.nama=pb.bank2"), $where.$where_filter, "o.tgl_orders DESC", "o.id_orders, o.tgl_orders, o.status, m.nama, p.id_pengiriman, p.penerima, p.alamat, p.provinsi, p.kota, p.kecamatan, p.kode_pos, p.telepon, p.biaya, p.kurir, p.service, p.no_resi, pb.id_pembayaran, pb.bank_tujuan, pb.tgl_konfirmasi, pb.status, pb.kode_unik, b.foto", $start, $end);
            if ($read_data != null) {
                foreach ($read_data as $row) {
                    $status_order = $row['status_order'];
                    $status_pembayaran = $row['status_pembayaran'];
                    $list_produk = '';
                    $tagihan = 0;
                    $read_produk = $this->m_crud->join_data("det_orders do", "do.qty, do.hrg_jual, do.hrg_varian, do.diskon, do.catatan, bo.nama nama_produk, bo.id_barang code, '-' warna, '-' ukuran", "barang_online bo", "do.det_produk=bo.id_barang", "do.orders='".$row['id_orders']."'");
                    foreach ($read_produk as $row_produk) {
                        $harga = $row_produk['hrg_jual']+$row_produk['hrg_varian'];
                        $diskon = $row_produk['diskon'];
                        $list_produk .= '<p>'.$row_produk['code'].' '.$row_produk['nama_produk'].'<br>('.$row_produk['ukuran'].' / '.$row_produk['warna'].')<br>Catatan: '.$row_produk['catatan'].'<br>'.(int)$row_produk['qty'].' x '.number_format($harga).'<br>'.($diskon>0?'Diskon '.number_format($diskon).'<br>':'').'</p><p class="order-items"></p>';
                        $tagihan = $tagihan + ($row_produk['qty'] * ($harga-$diskon));
                    }
                    $tagihan = $tagihan + $row['biaya'] + $row['kode_unik'];
                    $id = str_replace('/', '_', $row['id_orders']);
                    if ($status_order=='1' && $status_pembayaran<'3') {
                        $input_resi = '';
                    } else {
                        if ($status_order=='4') {
                            $input_resi = '';
                        } else {
                            $input_resi = '
                            <div class="input-group input-group-sm" style="margin-top: 0px;">
                                <input type="text" class="form-control" id="data_resi' . $id . '" placeholder="Input Resi...">
                                <span class="input-group-btn"><button class="btn btn-primary" onclick="input_resi(\'' . $row['id_orders'] . '\')" type="button">' . ($row['no_resi'] == null ? 'Simpan' : 'Ubah') . '</button></span>
                            </div>
                            ';
                        }
                    }

                    if ($status_pembayaran=='1') {
                        $aksi = '<button id="verifikasi" onclick="verifikasi(\'' . $row['id_pembayaran'] . '\')" class="btn btn-block btn-sm btn-info">Verifikasi Pembayaran</button><button id="batalkan" onclick="batalkan(\'' . $row['id_orders'] . '\')" class="btn btn-block btn-sm btn-danger">Batalkan Pesanan</button>';
                    } else if ($status_pembayaran=='2') {
                        $aksi = '<button id="buktitransfer" onclick="bukti_tf(\''.$row['id_pembayaran'].'\')" class="btn btn-block btn-sm btn-primary">Bukti Transfer</button><button id="verifikasi" onclick="verifikasi(\'' . $row['id_pembayaran'] . '\')" class="btn btn-block btn-sm btn-info">Verifikasi Pembayaran</button><button id="batalkan" onclick="batalkan(\'' . $row['id_orders'] . '\')" class="btn btn-block btn-sm btn-danger">Batalkan Pesanan</button>';
                    } else {
                        $aksi = '<button id="buktitransfer" onclick="bukti_tf(\''.$row['id_pembayaran'].'\')" class="btn btn-block btn-sm btn-primary">Bukti Transfer</button><button id="batalkan" onclick="batalkan(\'' . $row['id_orders'] . '\')" class="btn btn-block btn-sm btn-danger">Batalkan Pesanan</button>';
                    }
                    $output .= '
                    <div class="panel panel-default">
                        <div class="panel-body" id="result_table">
                            <div id="head_order'.$id.'" class="col-md-3">
                                <p class="h5">
                                    <a href="#"><strong>'.$row['id_orders'].'</strong></a>
                                </p>
                                <small>Pemesan</small>
                                <p><span>'.$row['nama_pemesan'].'</span></p>
                                <small>Dikirim kepada</small>
                                <p><span>'.$row['penerima'].'</span></p>
                                <p><small>Tgl Pemesanan</small><br>'.date('d M Y H:i A', strtotime($row['tgl_orders'])).'</p>
                                <small>Alamat</small>
                                <p><span>'.$row['alamat'].', '.$row['kota'].', '.$row['provinsi'].'</span></p>
                                <small>Telepon</small>
                                <p><span>'.$row['telepon'].'</span></p>
                                <hr>
                                <input class="ck_print" id="print'.$id.'" type="checkbox" value="'.$row['id_orders'].'"><button class="btn btn-sm bg-aqua" onclick="print_label(\'single\', \''.$row['id_orders'].'\')" style="margin-left: 10px"><span class="fa fa-print"></span></button>
                            </div>
                            <div id="product'.$id.'" class="col-md-3">
                                <div class="scrollbar" style="height: 300px">
                                    <p><small>Produk</small><br>'.$list_produk.'</p>
                                    <p class="order-items"><small>Kode Unik</small><br>'.number_format($row['kode_unik']).'</p>
                                    <p class="order-items"><small>Ongkir</small><br>'.number_format($row['biaya']).'</p>
                                </div>
                                <hr>
                            </div>
                            <div id="address'.$id.'" class="col-md-3">
                                <div class="alert alert-'.($status_pembayaran=='1'?'danger':'success').'">
                                    <p><small>Tagihan</small><br></p>
                                    <p class="med mbtm-10" data-toggle="tooltip" data-placement="top" title="'.($status_pembayaran=='1'?'Belum Dibayar':'Sudah Dibayar').'"><span class="lnr"><img src="'.base_url().'assets/images/icon/clipboards.svg" alt="" class="svg icon2">Rp '.number_format($tagihan).'</span></p>
                                    <div class="row payment-stts">
                                        <div class="col-sm-6">'.($row['tgl_konfirmasi']!=null?date('d M Y', strtotime($row['tgl_konfirmasi'])):'').'</div>
                                        <div class="col-sm-6"><img src="'.base_url().$row['gambar_bank'].'" style="max-width: 90px; max-width: 70px"></div>
                                    </div>
                                </div>
                                <div id="action'.$id.'">
                                    '.$aksi.'
                                </div>
                                <hr>
                            </div>
                            <div id="ekspedisi'.$id.'" class="col-md-3">
                                <p class="mbtm-10"><small>Status Transaksi</small></p>
                                <div class="tr-status" id="status_transaksi'.$id.'">
                                    <ul>
                                        <li class="'.($status_pembayaran>='2'?'done':'undone').'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.($status_pembayaran>='2'?'Sudah Dibayar':'Belum Dibayar').'">
                                            <img id="dollar" class="svg icon" src="'.base_url().'assets/images/icon/dollar.svg"/>
                                        </li>
    
                                        <li class="'.($status_pembayaran>='3'?'done':'undone').'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.($status_pembayaran>='3'?'Sedang Diproses':'Belum Diproses').'">
                                            <img id="pack_delivery" class="svg icon" src="'.base_url().'assets/images/icon/pack_delivery.svg"/>
                                        </li>
    
                                        <li class="'.($row['no_resi']!=null?'done':'undone').'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.($row['no_resi']!=null?'Sudah Dikirim':'Belum Dikirim').'">
                                            <img id="truck" class="svg icon" src="'.base_url().'assets/images/icon/truck.svg"/>
                                        </li>
    
                                        <li class="'.($status_order>='3'?'done':'undone').'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.($status_order>='3'?'Dalam Pengiriman Kurir':'').'">
                                            <img id="truck_clock" class="svg icon" src="'.base_url().'assets/images/icon/truck_clock.svg"/>
                                        </li>
    
                                        <li class="'.($status_order>='4'?'done':'undone').'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.($status_order>='4'?'Paket Telah Diterima':'').'">
                                            <img id="pack_delivered" class="svg icon" src="'.base_url().'assets/images/icon/pack_delivered.svg"/>
                                        </li>
                                    </ul>
                                </div>
                                <p class="mbtm-10"><small>Expedisi</small></p>
                                <img src="'.base_url().'assets/images/icon/'.strtolower($row['kurir']).'.png" style="margin-right:10px; max-width: 100px; max-height: 50px">
                                <span class="label label-gray-blank">'.$row['kurir'].'-'.$row['service'].'</span>
                                <p class="mtop-20"><small>No. Resi</small><br><div id="no_resi'.$id.'">'.($row['no_resi']==null?'':'<a href="javascript:" data-toggle="tooltip" data-placement="right" title="" data-original-title="Lacak Pengiriman" onclick="lacak(\''.$row['id_pengiriman'].'\')">'.$row['no_resi'].'</a>').'</div></p>
                                <div id="input_resi'.$id.'">
                                '.$input_resi.'
                                </div>
                            </div>
                        </div>
                    </div>
                    ';
                }
            } else {
                $output .= '
                <div class="panel panel-default">
                    <div class="panel-body"><h4 class="text-center">Tidak ada data</h4></div>
                </div>
                ';
            }

            $result = array(
                'pagination_link' => $this->pagination->create_links(),
                'result_order' => $output
            );
            echo json_encode($result);
        } else if ($action == 'load_status') {
            $result = array();
            $orders = json_decode($_POST['orders'], true);

            $res_order = array();
            foreach ($orders as $row) {
                $get_data = $this->m_crud->get_join_data($table." o", "o.status status_order, p.id_pengiriman, p.no_resi, pb.status status_pembayaran", array("det_orders do", "pengiriman p", "det_pembayaran dp", "pembayaran pb"), array("do.orders=o.id_orders", "p.orders=o.id_orders", "dp.orders=o.id_orders", "pb.id_pembayaran=dp.pembayaran"), "o.id_orders='".$row['orders']."'", null, "o.id_orders, o.status, p.id_pengiriman, p.no_resi, pb.status");
                $status_order = $get_data['status_order'];
                $status_pembayaran = $get_data['status_pembayaran'];

                $list_status_order = '
                <ul>
                    <li class="'.($status_pembayaran>='2'?'done':'undone').'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.($status_pembayaran>='2'?'Sudah Dibayar':'Belum Dibayar').'">
                        <img id="dollar" class="svg icon" src="'.base_url().'assets/images/icon/dollar.svg"/>
                    </li>

                    <li class="'.($status_pembayaran>='3'?'done':'undone').'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.($status_pembayaran>='3'?'Sedang Diproses':'Belum Diproses').'">
                        <img id="pack_delivery" class="svg icon" src="'.base_url().'assets/images/icon/pack_delivery.svg"/>
                    </li>

                    <li class="'.($get_data['no_resi']!=null?'done':'undone').'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.($get_data['no_resi']!=null?'Sudah Dikirim':'Belum Dikirim').'">
                        <img id="truck" class="svg icon" src="'.base_url().'assets/images/icon/truck.svg"/>
                    </li>

                    <li class="'.($status_order>='3'?'done':'undone').'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.($status_order>='3'?'Dalam Pengiriman Kurir':'').'">
                        <img id="truck_clock" class="svg icon" src="'.base_url().'assets/images/icon/truck_clock.svg"/>
                    </li>

                    <li class="'.($status_order>='4'?'done':'undone').'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.($status_order>='4'?'Paket Telah Diterima':'').'">
                        <img id="pack_delivered" class="svg icon" src="'.base_url().'assets/images/icon/pack_delivered.svg"/>
                    </li>
                </ul>
                ';

                if ($status_order=='1' && $status_pembayaran<'3') {
                    $input_resi = '';
                } else {
                    if ($status_order=='4') {
                        $input_resi = '';
                    } else {
                        $input_resi = '
                            <div class="input-group input-group-sm" style="margin-top: 0px;">
                                <input type="text" class="form-control" id="data_resi'.str_replace('/', '_', $row['orders']).'" placeholder="Input Resi...">
                                <span class="input-group-btn"><button class="btn btn-primary" onclick="input_resi(\''.$row['orders'].'\')" type="button">'.($get_data['no_resi']==null?'Simpan':'Ubah').'</button></span>
                            </div>
                        ';
                    }
                }

                if ($status_pembayaran=='1') {
                    $aksi = '<button id="verifikasi" onclick="verifikasi(\'' . $row['id_pembayaran'] . '\')" class="btn btn-block btn-sm btn-info">Verifikasi Pembayaran</button><button id="batalkan" onclick="batalkan(\'' . $row['id_orders'] . '\')" class="btn btn-block btn-sm btn-danger">Batalkan Pesanan</button>';
                } else if ($status_pembayaran=='2') {
                    $aksi = '<button id="buktitransfer" onclick="bukti_tf(\''.$row['id_pembayaran'].'\')" class="btn btn-block btn-sm btn-primary">Bukti Transfer</button><button id="verifikasi" onclick="verifikasi(\'' . $row['id_pembayaran'] . '\')" class="btn btn-block btn-sm btn-info">Verifikasi Pembayaran</button><button id="batalkan" onclick="batalkan(\'' . $row['id_orders'] . '\')" class="btn btn-block btn-sm btn-danger">Batalkan Pesanan</button>';
                } else {
                    $aksi = '<button id="buktitransfer" onclick="bukti_tf(\''.$row['id_pembayaran'].'\')" class="btn btn-block btn-sm btn-primary">Bukti Transfer</button><button id="batalkan" onclick="batalkan(\'' . $row['id_orders'] . '\')" class="btn btn-block btn-sm btn-danger">Batalkan Pesanan</button>';
                }

                $data_order = array(
                    'id' => str_replace('/', '_', $row['orders']),
                    'status' => $list_status_order,
                    'input_resi' => $input_resi,
                    'aksi' => $aksi,
                    'no_resi' => ($get_data['no_resi']==null?'':'<a href="javascript:" data-toggle="tooltip" data-placement="right" title="" data-original-title="Lacak Pengiriman" onclick="lacak(\''.$get_data['id_pengiriman'].'\')">'.$get_data['no_resi'].'</a>')
                );

                array_push($res_order, $data_order);
            }

            $result['status'] = true;
            $result['res_order'] = $res_order;

            echo json_encode($result);
        } else if ($action == 'verifikasi') {
            $result = array();
            $id_pembayaran = base64_decode($_POST['id_pembayaran']);

            $update_pembayaran = $this->m_crud->update_data("pembayaran", array('status'=>'3', 'tgl_verify'=>date('Y-m-d H:i:s')), "id_pembayaran='".$id_pembayaran."'");

            if ($update_pembayaran) {
                $result['status'] = true;
                    $get_bayar = $this->m_crud->get_join_data("pembayaran p", "m.email, m.tlp1, sum(jumlah+kode_unik) total, dp.orders", array("det_pembayaran dp", "customer m"), array("dp.pembayaran=p.id_pembayaran", "m.kd_cust=p.member"), "p.id_pembayaran='".$id_pembayaran."'", null, "m.email, m.tlp1, dp.orders");
                $data_email = array(
                    'kode_trx'=>$get_bayar['orders'],
                    'email'=>$get_bayar['email'],
                    'total_transfer'=>number_format($get_bayar['total'])
                );
                //$this->m_website->email_verify_transfer($data_email);
                $get_transaksi = $this->m_crud->read_data("det_pembayaran", "orders", "pembayaran='".$id_pembayaran."'");
                $result['res_orders'] = $get_transaksi;
            } else {
                $result['status'] = false;
            }

            echo json_encode($result);
        } else if ($action == 'batalkan') {
            $result = array();
            $id_order = base64_decode($_POST['id_order']);

            $update_order = $this->m_crud->update_data("orders", array('status'=>'5'), "id_orders='".$id_order."'");

            if ($update_order) {
                $result['status'] = true;
                $get_bayar = $this->m_crud->get_join_data("pembayaran p", "m.email, m.tlp1, sum(jumlah+kode_unik) total, dp.orders", array("det_pembayaran dp", "customer m"), array("dp.pembayaran=p.id_pembayaran", "m.kd_cust=p.member"), "dp.orders='".$id_order."'", null, "m.email, m.tlp1, dp.orders");
                $data_email = array(
                    'kode_trx'=>$get_bayar['orders'],
                    'email'=>$get_bayar['email'],
                    'total_transfer'=>number_format($get_bayar['total'])
                );
                //$this->m_website->email_transaksi_batal($data_email);
                $result['res_orders'] = array(array('orders'=>$id_order));
            } else {
                $result['status'] = false;
            }

            echo json_encode($result);
        } else if ($action == 'input_resi') {
            $result = array();
            $id_order = base64_decode($_POST['id_order']);

            if ($_POST['no_resi']!='') {
                $this->m_crud->update_data("orders", array('status' => '2'), "id_orders='" . $id_order . "'");
            } else {
                $this->m_crud->update_data("orders", array('status' => '1'), "id_orders='" . $id_order . "'");
            }
            $update_resi = $this->m_crud->update_data("pengiriman", array('no_resi'=>$_POST['no_resi']), "orders='".$id_order."'");

            if ($update_resi) {
                $result['status'] = true;
                $result['res_orders'] = array(array('orders'=>$id_order));
            } else {
                $result['status'] = false;
            }

            echo json_encode($result);
        } else if ($action == 'lacak_resi') {
            $result = array();
            $id_pengiriman = $_POST['id_pengiriman'];
            $get_data = $this->m_crud->get_join_data("pengiriman p", "p.orders, p.kurir, p.no_resi, o.member", "orders o", "o.id_orders=p.orders", "p.id_pengiriman='".$id_pengiriman."'");

            $resi = $this->m_website->rajaongkir_resi(json_encode(array('resi'=>$get_data['no_resi'], 'kurir'=>strtolower($get_data['kurir']))));
            $decode = json_decode($resi, true);
            $status_resi = $decode['rajaongkir']['status']['code'];

            if ($status_resi == '200') {
                $result = $decode['rajaongkir']['result'];
                $delivered = $result['delivered'];
                $summary = $result['summary'];
                $details = $result['details'];
                $manifest = $result['manifest'];

                if ($delivered) {
                    $id_orders = $get_data['orders'];
                    $member = $get_data['member'];
                    $get_total = $this->m_crud->get_data("det_orders", "SUM(qty*(hrg_jual+hrg_varian-diskon)) total", "orders='".$id_orders."'")['total'];

                    $this->m_crud->update_data("orders", array('status'=>'4'), "id_orders='".$get_data['orders']."'");
                    $result['message'] = "Paket telah tiba di tujuan";
                } else {
                    $this->m_crud->update_data("orders", array('status'=>'3'), "id_orders='".$get_data['orders']."'");
                    $result['message'] = "Paket dalam proses pengiriman";
                }
                $result['res_orders'] = array(array('orders'=>$get_data['orders']));
                $result['status'] = true;
            } else {
                $result['status'] = false;
                $result['message'] = "Nomor resi salah atau belum terdaftar";
            }

            echo json_encode($result);
        } else if ($action == 'load_header') {
            $read_data = $this->m_crud->join_data($table." o", "o.status status_order, p.no_resi, pb.status status_pembayaran", array("det_orders do", "pengiriman p", "customer m", "det_pembayaran dp", "pembayaran pb"), array("do.orders=o.id_orders", "p.orders=o.id_orders", "m.kd_cust=o.member", "dp.orders=o.id_orders", "pb.id_pembayaran=dp.pembayaran"), $where, "o.tgl_orders DESC", "o.tgl_orders, o.status, p.no_resi, pb.status");
            $belum_bayar = 0; $belum_proses = 0; $belum_resi = 0; $belum_lacak = 0; $dalam_proses = 0; $berhasil = 0;
            foreach ($read_data as $row) {
                $status_order = $row['status_order'];
                $status_pembayaran = $row['status_pembayaran'];

                $status_pembayaran=='1'?$belum_bayar = $belum_bayar + 1:null;
                $status_pembayaran=='2'?$belum_proses = $belum_proses + 1:null;
                $status_pembayaran=='3' && $row['no_resi']==null?$belum_resi = $belum_resi + 1:null;
                $status_order=='2' && $row['no_resi']!=null?$belum_lacak = $belum_lacak + 1:null;
                $status_order=='3'?$dalam_proses = $dalam_proses + 1:null;
                $status_order=='4'?$berhasil = $berhasil + 1:null;
            }

            $result = array(
                'belum_bayar' => $belum_bayar,
                'belum_proses' => $belum_proses,
                'belum_resi' => $belum_resi,
                'belum_lacak' => $belum_lacak,
                'dalam_proses' => $dalam_proses,
                'berhasil' => $berhasil
            );

            echo json_encode($result);
        } else {
            $this->load->view('bo/index', $data);
        }
    }

    public function print_label($id=null) {
        $data_order = json_decode(base64_decode($id), true);
        if (is_array($data_order)) {
            $situs = $this->m_crud->get_data("site", "title nama, web, ('".$this->config->item('url')."' + logo) logo, web", "site_id='1'");

            $situs['tlp'] = $situs['web'];
            $data = implode(',', $data_order);
            $get_data = $this->m_crud->join_data("orders o", "o.id_orders, o.tgl_orders, m.nama nama_member, png.penerima, png.alamat, png.provinsi, png.kota, png.kecamatan, png.kode_pos, png.telepon, png.kurir, png.service, png.biaya, pb.kode_unik, pb.jumlah", array("customer m", "pengiriman png", "det_pembayaran dpb", "pembayaran pb"), array("m.kd_cust=o.member", "png.orders=o.id_orders", "dpb.orders=o.id_orders", "pb.id_pembayaran=dpb.pembayaran"), "o.id_orders IN (".$data.")");
            $this->load->view("bo/Penjualan/print_label", array('situs'=>$situs, 'data'=>$get_data));
        } else {
            redirect(base_url());
        }
    }
    /*End orders*/

    /*Start arsip penjualan*/
    public function arsip_penjualan_online($action=null, $page=1) {
        $this->access_denied(165);
        $data = $this->data;
        $function = 'arsip_penjualan_online';
        $table = 'orders';
        $view = $this->control.'/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function) {
            $this->session->unset_userdata('search');
            $this->cart->destroy();
            $this->session->set_userdata($this->site . 'admin_menu', $function);
        }
        $data['main'] = 'Laporan';
        $data['title'] = 'Arsip Penjualan Online';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $where = "o.status<>'0'";

        if(isset($_POST['search'])||isset($_POST['to_excel'])) {
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'date' => $_POST['date'], 'status' => $_POST['status'], 'periode' => $_POST['periode']));
        }

        $search = $this->session->search['any']; $date = $this->session->search['date']; $status = $this->session->search['status']; $periode = $this->session->search['periode'];
        if(isset($search)&&$search!=null) {
            ($where == null) ? null : $where .= " AND ";
            $where .= "(o.id_orders like '%".$search."%' OR m.nama like '%".$search."%')";
        }

        if(isset($date)&&$date!=null) {
            $explode_date = explode(' - ', $date);
            $tgl_awal = $explode_date[0]; $tgl_akhir = $explode_date[1];
        } else {
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        }

        if($periode==null) {
            ($where == null) ? null : $where .= " AND ";
            $where .= "LEFT(convert(varchar, o.tgl_orders, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
        }

        if(isset($status)&&$status!=null) {
            ($where == null) ? null : $where .= " AND ";
            $where .= "o.status = ".$status;
        }

        if ($action == 'get_data') {
            $config = array();
            $config["base_url"] = "#";
            //$config["total_rows"] = $this->ajax_pagination_model->count_all();
            $config["total_rows"] = $this->m_crud->count_data_join($table." o", "o.id_orders", array("det_orders dto", "pengiriman p", "customer m", "det_pembayaran dp", "pembayaran pb"), array("dto.orders=o.id_orders", "p.orders=o.id_orders", "m.kd_cust=o.member", "dp.orders=o.id_orders", "pb.id_pembayaran=dp.pembayaran"), $where);
            $config["per_page"] = 6;
            $config["uri_segment"] = 4;
            $config["num_links"] = 5;
            $config["use_page_numbers"] = TRUE;
            $config["full_tag_open"] = '<ul class="pagination pagination-sm">';
            $config["full_tag_close"] = '</ul>';
            $config['first_link'] = '&laquo;';
            $config["first_tag_open"] = '<li>';
            $config["first_tag_close"] = '</li>';
            $config['last_link'] = '&raquo;';
            $config["last_tag_open"] = '<li>';
            $config["last_tag_close"] = '</li>';
            $config['next_link'] = '&gt;';
            $config["next_tag_open"] = '<li>';
            $config["next_tag_close"] = '</li>';
            $config["prev_link"] = "&lt;";
            $config["prev_tag_open"] = "<li>";
            $config["prev_tag_close"] = "</li>";
            $config["cur_tag_open"] = "<li class='active'><a href='#'>";
            $config["cur_tag_close"] = "</a></li>";
            $config["num_tag_open"] = "<li>";
            $config["num_tag_close"] = "</li>";
            $this->pagination->initialize($config);

            $start = ($page-1)*$config["per_page"]+1;
            $end = ($config["per_page"]*$page);

            $output = '';
            $read_data = $this->m_crud->select_limit_join($table." o", "o.id_orders, o.tgl_orders, o.status, pb.status status_pembayaran, m.nama, p.kurir, p.service, p.biaya, p.no_resi, SUM(dto.qty*(dto.hrg_jual+dto.hrg_varian)) sub_total, SUM(dto.qty*dto.diskon) diskon", array("det_orders dto", "pengiriman p", "customer m", "det_pembayaran dp", "pembayaran pb"), array("dto.orders=o.id_orders", "p.orders=o.id_orders", "m.kd_cust=o.member", "dp.orders=o.id_orders", "pb.id_pembayaran=dp.pembayaran"), $where, "o.tgl_orders DESC", "o.id_orders, o.tgl_orders, o.status, pb.status, m.nama, p.kurir, p.service, p.biaya, p.no_resi", $start, $end);
            $output .= '
                <table class="table table-hover">
                <tr>
                    <th width="1%">No</th>
                    <th width="1%" class="text-center">#</th>
                    <th>Kode Orders</th>
                    <th>Tanggal</th>
                    <th>Member</th>
                    <th>Kurir</th>
                    <th>Service</th>
                    <th>Sub Total</th>
                    <th>Diskon</th>
                    <th>Omset</th>
                    <th>Ongkir</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            ';
            $no = $start;
            if ($read_data != null) {
                $sub_total = 0; $diskon = 0; $ongkir = 0;
                foreach ($read_data as $row) {
                    if ($row['status'] == '1') {
                        if($row['status_pembayaran']>=2){
                            $status = '<span class="label label-warning">Sudah Dibayar</span>';
                        } else {
                            $status = '<span class="label bg-pink">Menunggu Pembayaran</span>';
                        }
                    } else if ($row['status'] == '2') {
                        if($row['no_resi']!=null && $row['no_resi']!=''){
                            $status = '<span class="label label-info">Sudah Dikirim</span>';
                        } else {
                            $status = '<span class="label bg-purple">Belum Dikirim</span>';
                        }
                    } else if ($row['status'] == '3') {
                        $status = '<span class="label label-primary">Dalam Pengiriman Kurir</span>';
                    } else if ($row['status'] == '4') {
                        $status = '<span class="label label-success">Success</span>';
                    } else {
                        $status = '<span class="label label-danger">Batal</span>';
                    }
                    $output .= '
                    <tr>
                        <td>' . $no++ . '</td>
                        <td>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Pilihan <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu dropdown-position">
                                <li><a href="#" onclick="detail(\'' . $row['id_orders'] . '\')">Detail</a></li>
                            </ul>
                        </div>
                        </td>
                        <td>' . $row['id_orders'] . '</td>
                        <td>' . $row['tgl_orders'] . '</td>
                        <td>' . $row['nama'] . '</td>
                        <td>' . $row['kurir'] . '</td>
                        <td>' . $row['service'] . '</td>
                        <td>' . number_format($row['sub_total']) . '</td>
                        <td>' . number_format($row['diskon']) . '</td>
                        <td>' . number_format($row['sub_total']-$row['diskon']) . '</td>
                        <td>' . number_format($row['biaya']) . '</td>
                        <td>' . number_format($row['sub_total']-$row['diskon']+$row['biaya']) . '</td>
                        <td>' . $status . '</td>
                    </tr>
                ';
                    $sub_total = $sub_total + $row['sub_total'];
                    $diskon = $diskon + $row['diskon'];
                    $ongkir = $ongkir + $row['biaya'];
                }
                $total = $this->m_crud->get_join_data($table." o", "SUM(p.biaya) biaya, SUM(dto.qty*(dto.hrg_jual+dto.hrg_varian)) sub_total, SUM(dto.qty*dto.diskon) diskon", array("det_orders dto", "pengiriman p", "customer m", "det_pembayaran dp", "pembayaran pb"), array("dto.orders=o.id_orders", "p.orders=o.id_orders", "m.kd_cust=o.member", "dp.orders=o.id_orders", "pb.id_pembayaran=dp.pembayaran"), $where);
                $output .= '
                    <tr>
                        <th colspan="7">Total per halaman</th>
                        <th>' . number_format($sub_total) . '</th>
                        <th>' . number_format($diskon) . '</th>
                        <th>' . number_format($sub_total-$diskon) . '</th>
                        <th>' . number_format($ongkir) . '</th>
                        <th>' . number_format($sub_total-$diskon+$ongkir) . '</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="7">Total</th>
                        <th>' . number_format($total['sub_total']) . '</th>
                        <th>' . number_format($total['diskon']) . '</th>
                        <th>' . number_format($total['sub_total']-$total['diskon']) . '</th>
                        <th>' . number_format($total['biaya']) . '</th>
                        <th>' . number_format($total['sub_total']-$total['diskon']+$total['biaya']) . '</th>
                        <th></th>
                    </tr>
                ';
            } else {
                $output .= '
                <tr>
                    <td colspan="13" class="text-center">Tidak ada data</td>
                </tr>
                ';
            }
            $output .= '</table>';

            $result = array(
                'pagination_link' => $this->pagination->create_links(),
                'result_table' => $output
            );
            echo json_encode($result);
        } else if ($action == 'detail') {
            $get_data = $this->m_crud->join_data("det_orders do", "bo.nama, bo.id_barang code, '-' ukuran, '-' warna, (do.hrg_jual+do.hrg_varian) hrg_jual, do.qty, do.diskon", "barang_online bo", "do.det_produk=bo.id_barang", "do.orders='".$_POST['id']."'");
            $result = array();

            if ($get_data != null) {
                $result['status'] = true;

                $list_produk = '';
                foreach ($get_data as $row) {
                    $list_produk .= '
                    <tr>
                        <td>'.$row['code'].'</td>
                        <td>'.$row['nama'].'</td>
                        <td>'.$row['ukuran'].'</td>
                        <td>'.$row['warna'].'</td>
                        <td>'.(int)$row['qty'].'</td>
                        <td>'.number_format($row['hrg_jual']).'</td>
                        <td>'.number_format($row['diskon']).'</td>
                    </tr>
                    ';
                }
                $result['res_produk'] = $list_produk;
            } else {
                $result['status'] = false;
            }

            echo json_encode($result);
        } else {
            $this->load->view('bo/index', $data);
        }
    }
    /*End laporan penjualan*/

    public function req_deposit($action = null, $id = null){
        $this->access_denied(193);
        $data = $this->data;
        $function = 'req_deposit';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Request Deposit';
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
            $where .= "LEFT(CONVERT(VARCHAR, d.tgl_deposit, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
        } else {
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, d.tgl_deposit, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($status)&&$status!=null){ ($where==null)?null:$where.=" and "; $where.="(d.status = '".$status."')"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(cs.nama like '%".$search."%' or d.id_deposit like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_join_over("deposit d", 'd.id_deposit', "customer cs", "cs.kd_cust=d.member", ($where==null?'':$where));
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
        $data['report'] = $this->m_crud->select_limit_join("deposit d", "d.*, cs.nama nama_member", "customer cs", "cs.kd_cust=d.member", ($where==null?'':$where), 'd.tgl_deposit desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

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

    public function sukses_deposit() {
        $response = array();
        $member = $_POST['id_member'];
        $id_deposit = $_POST['id_deposit'];

        $this->db->trans_begin();

        $this->m_crud->update_data("deposit", array('status'=>'1', 'tgl_verify'=>date('Y-m-d H:i:s')), "id_deposit='".$id_deposit."'");

        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            $response['status'] = true;
            $response['pesan'] = "Deposit berhasil ditambahkan!";

            $get_onsignal = $this->m_crud->get_data("customer", "one_signal_id", "kd_cust='".$member."'");

            $data_notif = array(
                'member'=>$get_onsignal['one_signal_id'],
                /*'segment'=>'All',*/
                'data' => array("param" => "success_deposit", "kd_trx" => $id_deposit),
                'head'=>'Deposit Berhasil',
                'content'=>'Transaksi deposit anda berhasil ditambahkan'
            );

            $this->m_website->create_notif($data_notif);
        } else {
            $this->db->trans_rollback();
            $response['status'] = false;
            $response['pesan'] = "Transaksi gagal, silahkan ulangi lagi";
        }

        echo json_encode($response);
    }
}
