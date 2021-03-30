<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory extends CI_Controller {

	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
		$site_data = $this->m_website->site_data();
		$this->site = str_replace(' ', '', strtolower($site_data->title));
		$this->control = 'Inventory';
		
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
	
	/*Start modul delivery_note*/
	public function delivery_note() {
        $this->access_denied(37);
        $data = $this->data;
        $function = 'delivery_note';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Delivery Note';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data("Lokasi", "Kode, Nama, serial");
        //$data['data_pembelian'] = $this->m_crud->read_data("master_beli", "no_faktur_beli", null, "tgl_beli DESC", null, 50);

        $this->load->view('bo/index', $data);
    }
	
	public function edit_delivery_note($tmp_kode_alokasi){
        //$this->access_denied(13);
        $kode_alokasi = base64_decode($tmp_kode_alokasi);
        $data = $this->data;
        $function = 'delivery_note';
        $view = $this->control . '/';
		
        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Edit Delivery Note';
        $data['page'] = $function;
        $data['content'] = $view.$function;

        $this->db->trans_begin();
        $get_data_alokasi = $this->m_crud->get_data("master_delivery_note", "no_delivery_note, tanggal, keterangan, isnull(no_faktur_beli, '-') no_faktur_beli, kd_lokasi_1 lk1, kd_lokasi_2 lk2, kd_lokasi_1", "no_delivery_note='".$kode_alokasi."'");

        if (substr($get_data_alokasi['no_delivery_note'], 0, 2) == 'DN') {
            $jns_trx = 'alokasi';
            $where_stock = "";
        } else {
            $jns_trx = 'branch';
            $where_stock = " AND Kartu_stock.kd_trx <> '".$get_data_alokasi['no_delivery_note']."'";
        }

        $read_data_alokasi = $this->m_crud->read_data("det_delivery_note dm, barang br", "dm.*, br.barcode, br.nm_brg, br.Deskripsi, br.satuan, br.kd_packing, br.qty_packing, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE lokasi NOT IN ('MUTASI', 'Retur') AND Kartu_stock.kd_brg = br.kd_brg AND lokasi='".$get_data_alokasi['kd_lokasi_1']."'".$where_stock.") stock", "dm.kd_brg=br.kd_brg AND dm.no_delivery_note='".$get_data_alokasi['no_delivery_note']."'");
		
        $data['data_lokasi'] = $this->m_crud->read_data("Lokasi", "Kode, Nama, serial");
        //$data['data_pembelian'] = $this->m_crud->read_data("master_beli", "no_faktur_beli", null, "tgl_beli DESC", null, 50);
		
        $get_tmp_data = $this->m_crud->count_data("tr_temp_m", "m1", "m2='".$this->user."' AND m1='".$get_data_alokasi['no_delivery_note']."'");

        //if ($get_tmp_data == 0) {
            $this->m_crud->delete_data("tr_temp_m", "(SUBSTRING(m1,1,3) in ('DN-')) and m2 = '".$this->user."'");
            $this->m_crud->delete_data("tr_temp_d", "(SUBSTRING(d1,1,3) in ('DN-')) and d12 = '".$this->user."'");
            /*Add to master temporary*/
            $data_tmp_m = array(
                'm1' => $get_data_alokasi['no_delivery_note'],
                'm2' => $this->user,
                'm3' => substr($get_data_alokasi['tanggal'], 0, 10),
                'm4' => $get_data_alokasi['lk1'],
                'm5' => $get_data_alokasi['lk2'],
                'm6' => $get_data_alokasi['keterangan'],
                'm7' => $get_data_alokasi['no_faktur_beli'],
                'm9' => 'edit',
                'm10' => $get_data_alokasi['no_delivery_note'],
				'm11' => 1,
				'm12' => 1
            );

            $this->m_crud->create_data("tr_temp_m", $data_tmp_m);

            $id = 1;

            /*Add to detail temporary*/
            foreach ($read_data_alokasi as $get_barang) {
                $data_tmp_d = array(
                    'd1' => $get_barang['no_delivery_note'],
                    'd2' => $get_barang['kd_brg'],
                    'd3' => $get_barang['Deskripsi'],
                    'd4' => $get_barang['satuan'],
                    'd5' => $get_barang['hrg_beli'],
                    'd6' => $get_barang['hrg_jual'],
                    'd7' => 0,
                    'd8' => 0,
                    'd9' => 0,
                    'd10' => $get_barang['qty'],
                    'd11' => $get_barang['barcode'],
                    'd12' => $this->user,
                    'd13' => $get_barang['stock'],
                    'd14' => $get_barang['no_faktur_beli'],
                    'd15' => $id++,
                    'd16' => $get_barang['nm_brg'],
                    'd17' => $get_barang['kd_packing'],
                    'd18' => $get_barang['qty_packing'],
                    'd19' => 'edit',
                    'd20' => $get_barang['no_delivery_note']
                );

                $this->m_crud->create_data("tr_temp_d", $data_tmp_d);
            }
        //}

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        $this->load->view('bo/index', $data);
    }
	
	public function get_tr_temp_m_dn($tmp_trx = null) {
		$trx = base64_decode($tmp_trx);
		
        $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m1='" . $trx . "' AND m2 = '" . $this->user . "'");
        
		if($get_data==null){ 
			$this->m_crud->create_data('tr_temp_m', array('m1'=>$trx, 'm2'=>$this->user, 'm3'=>date('Y-m-d'))); 
			$get_data = $this->m_crud->get_data("tr_temp_m", "*", "m1='" . $trx . "' AND m2 = '" . $this->user . "'");
		}
		
        echo json_encode(array('status' => 1, 'temp' => $get_data));
    }
	
	public function update_tr_temp_m_dn($tmp_trx, $tmp_col, $tmp_val) {
	    $trx = base64_decode($tmp_trx);
	    $col = base64_decode($tmp_col);
	    $val = base64_decode($tmp_val);

	    if ($val != '') {
	        $this->m_crud->update_data("tr_temp_m", array($col => $val), "m1='" . $trx . "' AND m2 = '" . $this->user . "'");
        }
    }
	
	public function get_tr_temp_d_dn($param = null) {
        $param = base64_decode($param);
        $list_barang = '';
        if ($param == 'edit') {
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d19 = 'edit' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,3) in ('DN-'))", "CONVERT(INTEGER, d15) ASC");
            $get_nota = $this->m_crud->get_data("tr_temp_m", "isnull(m7, '-') m7", "(m2 = '".$this->user."') AND (SUBSTRING(m1,1,3) in ('DN-'))");
        } else {
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d19 = 'add' AND (d12 = '".$this->user."') AND (d1 in ('DN'))", "CONVERT(INTEGER, d15) ASC");
            $get_nota = $this->m_crud->get_data("tr_temp_m", "isnull(m7, '-') m7", "(m2 = '".$this->user."') AND (m1 in ('DN'))");
        } 
        $no = 1;
        $col = 0;
        $qty = 1;
        $total_qty = 0;
        $sub_total = 0;
        $length = count($read_data);
        foreach ($read_data as $row) {
            $total_qty = $total_qty + (int)$row['d10'];
            $hitung_sub_total = (float)$row['d5'] * (int)$row['d10'];
            $sub_total = $sub_total + ((float)$row['d5'] * (int)$row['d10']);
            if ((int)$row['d13'] <= 0 || (int)$row['d13']-(int)$row['d10'] < 0 || (int)$row['d10'] <= 0) {
                $id = $no;
                $qty = -1;
                $value = (int)$row['d10'];
            }

            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d11'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button></td>
                                <td>' . $row['d2'] . '<input type="hidden" id="d2'.$no.'" name="d2'.$no.'" value="'.$row['d2'].'" /></td>
                                <td>' . $row['d11'] . '</td>
                                <td>' . $row['d16'] . '</td>
                                <td>' . $row['d3'] . '</td>
                                <td>' . $row['d4'] . '</td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d5\', $(this).val())" onkeyup="hitung_barang(\'d5\', \'' . $no . '\', $(this).val(), '.$length.')" type="number" id="d5' . $no . '" name="d5' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d5'], 2, '.', '') . '" readonly></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d6\', $(this).val())" type="number" id="d6' . $no . '" name="d6' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d6'], 2, '.', '') . '" readonly></td>
                                <input onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d7\', $(this).val())" type="hidden" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d7'], 2, '.', '') . '" readonly>
                             	<input onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d8\', $(this).val())" type="hidden" id="d8' . $no . '" name="d8' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d8'], 2, '.', '') . '" readonly>
                            	<input onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d9\', $(this).val())" type="hidden" id="d9' . $no . '" name="d9' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d9'], 2, '.', '') . '" readonly>
                                <td><input type="number" id="d13' . $no . '" name="d13' . $no . '" class="form-control width-diskon" value="' . ($row['d13'] + 0) . '" readonly></td>
                             	<td><input onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d10\', $(this).val())" onkeyup="update_tmp_detail(\'' . $row['d11'] . '\', \'d10\', $(this).val()); hitung_barang(\'d10\', \'' . $no . '\', $(this).val(), '.$length.'); return to_barcode(event)" onclick="$(this).select()" type="number" id="d10' . $no . '" name="d10' . $no . '" class="form-control width-diskon" value="' . ($row['d10'] + 0) . '"><b class="error" id="alr_jumlah_' . $no . '"></b></td>
                                <td><input type="number" id="sub_total' . $no . '" name="sub_total' . $no . '" class="form-control width-uang" value="'.$hitung_sub_total.'" readonly></td>
                            </tr>'; 
            $col = $no;
            $no++;
        }

        if ($get_nota != null) {
            $kode_pembelian = $get_nota['m7'];
        } else {
            $kode_pembelian = '-';
        }

        $list_barang .= '
        <tr>
            <th colspan="10" class="text-right">TOTAL QTY</th>
            <th class="text-center"><b id="total_qty">'.($total_qty+0).'</b></th>
            <th class="text-right"></th>
        </tr>
        ';
        $list_barang .= '<input type="hidden" id="col" name="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang, 'sub_total' => number_format((float)$sub_total, 2, '.', ''), 'kode_pembelian' => $kode_pembelian, 'qty' => $qty, 'id' => $id, 'length' => $length, 'value' => $value));
    }
	
	public function update_tr_temp_d_dn($tmp_barcode, $tmp_column, $tmp_value, $param = null) {
        $param = base64_decode($param);
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        if ($param == 'edit') {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "(SUBSTRING(d1,1,3) in ('DN-')) AND (d11 = '".$barcode."') AND (d12 = '".$this->user."')");
        } else {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "(d1 in ('DN')) AND (d11 = '".$barcode."') AND (d12 = '".$this->user."')");
        }
    }
	
	public function get_data_pembelian_dn($tmp_lokasi) {
        $lokasi = base64_decode($tmp_lokasi);
        
		$read_kode_pembelian = $this->m_crud->join_data("master_beli mb", "mb.no_faktur_beli", "det_beli dbl", "dbl.no_faktur_beli=mb.no_faktur_beli", "mb.Lokasi = '".$lokasi."'", "mb.no_faktur_beli DESC", "mb.no_faktur_beli", 0, 0, "SUM(dbl.jumlah_beli) > isnull((select sum(dmu.qty) from master_delivery_note mu join det_delivery_note dmu on mu.no_delivery_note = dmu.no_delivery_note where mu.no_delivery_note <> '".$get_nota['m1']."' AND mu.no_delivery_note = mb.no_faktur_beli), 0)");
		//$read_kode_pembelian = $this->m_crud->read_data("master_beli mb", "mb.no_faktur_beli", "mb.Lokasi = '".$lokasi."' and ((isnull((select sum(d.jumlah_beli) from det_beli d where d.no_faktur_beli=mb.no_faktur_beli),0))>(isnull((select sum(d.qty) from det_delivery_note d join master_delivery_note m on d.no_delivery_note=m.no_delivery_note where m.no_faktur_beli=mb.no_faktur_beli),0)))", "mb.no_faktur_beli DESC", "mb.no_faktur_beli", 1000);
        
        $list_kode = '<option value="-">Pilih</option>';
        if (count($read_kode_pembelian) != 0) {
            foreach ($read_kode_pembelian as $row) {
                $list_kode .= '<option value="'.$row['no_faktur_beli'].'">'.$row['no_faktur_beli'].'</option>';
            }
        }

        echo json_encode(array('list' => $list_kode));
    }
	
	public function delete_tmp_d_dn($param = null) {
        $this->m_crud->delete_data("tr_temp_d", "(d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('DN'))");
    }
	
	public function insert_tr_temp_d_dn($nota_sistem, $get_barang, $barcode, $param = null, $qty=1, $kode_pembelian='-') {
        $data = array( 
            'd1' => $nota_sistem,
            'd2' => $get_barang['kd_brg'],
            'd3' => $get_barang['Deskripsi'],
            'd4' => $get_barang['satuan'],
            'd5' => $get_barang['hrg_beli'],
            'd6' => $get_barang['hrg_jual_1'],
            'd7' => $get_barang['hrg_jual_2'],
            'd8' => $get_barang['hrg_jual_3'],
            'd9' => $get_barang['hrg_jual_4'],
            'd10' => $qty,
            'd11' => $barcode,
            'd12' => $this->user,
            'd13' => $get_barang['stock'],
            'd14' => $kode_pembelian,
            'd16' => $get_barang['nm_brg'],
            'd17' => $get_barang['kd_packing'],
            'd18' => $get_barang['qty_packing']
        );

        if ($param == 'edit') {
            $get_tmp_d = $this->m_crud->get_data("tr_temp_d", "d20", "d12='".$this->user."' AND d19='edit'");
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d15)) id", "(d12 = '".$this->user."') AND (SUBSTRING(d1,1,3) in ('DN-'))");
            $data['d15'] = ((int)$get_max_id['id']+1);
            $data['d19'] = 'edit';
            $data['d20'] = $get_tmp_d['d20'];
        } else {
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d15)) id", "(d12 = '".$this->user."') AND (d1 in ('DN'))");
            $data['d15'] = ((int)$get_max_id['id']+1);
            $data['d19'] = 'add';
        }

        $this->m_crud->create_data("tr_temp_d", $data);
    }
	
	public function add_data_pembelian_dn($param = null) {
        $param = base64_decode($param);
        if ($param == 'edit') {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m1, m4, m5, m12", "(m2 = '".$this->user."') AND (SUBSTRING(m1,1,3) in ('DN-'))");
        } else {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m1, m4, m5, m12", "(m2 = '".$this->user."') AND (m1 in ('DN'))");
        }
        $no_mutasi = $get_nota['m1'];
        $lokasi = $get_nota['m4'];
        $lokasi_to = $get_nota['m5'];
        $kode_pembelian = $_POST['kode_pembelian'];//$get_nota['m7'];
        $faktur_mutasi = $get_nota['m1'];
        
		$po = $this->m_crud->get_join_data('master_beli mb', "jenis_po", array('master_po mp'), array("mb.no_po=mp.no_po"), "mb.no_faktur_beli = '".$kode_pembelian."'");
		
        $this->db->trans_begin();
		
        $this->delete_tmp_d_dn();
		
		$qty_poc = "isnull((select sum(dqpc.qty) from detail_qty_po_cabang dqpc join master_receive_order mro on dqpc.no_receive_order=mro.no_receive_order join master_order mo on mro.no_order=mo.no_order where dqpc.no_po=bl.no_po and dqpc.kd_brg=dbl.kode_barang and mo.lokasi='".$lokasi_to."'),0)";
        if ($param == 'edit') {
            if($po['jenis_po']=='POC'){
				$read_data_pembelian = $this->m_crud->join_data("master_beli bl", 
					"br.barcode, (".$qty_poc." - isnull((select sum(dmu.qty) from master_delivery_note mu join det_delivery_note dmu on mu.no_delivery_note = dmu.no_delivery_note where mu.kd_lokasi_2='".$lokasi_to."' and mu.no_faktur_beli = bl.no_faktur_beli and mu.no_delivery_note <> '".$faktur_mutasi."' and dmu.kd_brg=dbl.kode_barang), 0)) qty", 
					array("det_beli dbl", "barang br", "master_po mp"), array("bl.no_faktur_beli=dbl.no_faktur_beli", "br.kd_brg=dbl.kode_barang", "bl.no_po=mp.no_po"), 
					"bl.no_faktur_beli='".$kode_pembelian."' AND (".$qty_poc." - isnull((select sum(dmu.qty) from master_delivery_note mu join det_delivery_note dmu on mu.no_delivery_note = dmu.no_delivery_note where and mu.no_faktur_beli = bl.no_faktur_beli and mu.no_delivery_note <> '".$faktur_mutasi."' and dmu.kd_brg=dbl.kode_barang), 0)) > 0"
				);
            } else {
				$read_data_pembelian = $this->m_crud->join_data("master_beli bl", "br.barcode, (dbl.jumlah_beli - isnull((select sum(dmu.qty) from master_delivery_note mu join det_delivery_note dmu on mu.no_delivery_note = dmu.no_delivery_note where mu.no_faktur_beli = bl.no_faktur_beli and mu.no_delivery_note <> '".$faktur_mutasi."' and dmu.kd_brg=dbl.kode_barang), 0)) qty", array("det_beli dbl", "barang br"), array("bl.no_faktur_beli=dbl.no_faktur_beli", "br.kd_brg=dbl.kode_barang"), "bl.no_faktur_beli='".$kode_pembelian."' AND (dbl.jumlah_beli - isnull((select sum(dmu.qty) from master_delivery_note mu join det_delivery_note dmu on mu.no_delivery_note = dmu.no_delivery_note where mu.no_faktur_beli = bl.no_faktur_beli and mu.no_delivery_note <> '".$faktur_mutasi."' and dmu.kd_brg=dbl.kode_barang), 0)) > 0");
            }
			$where_stock = " AND Kartu_stock.kd_trx <> '".$faktur_mutasi."'";
        } else {
            if($po['jenis_po']=='POC'){ 
				$read_data_pembelian = $this->m_crud->join_data("master_beli bl", 
					"br.barcode, (".$qty_poc." - isnull((select sum(dmu.qty) from master_delivery_note mu join det_delivery_note dmu on mu.no_delivery_note = dmu.no_delivery_note where mu.kd_lokasi_2='".$lokasi_to."' and mu.no_faktur_beli = bl.no_faktur_beli and dmu.kd_brg=dbl.kode_barang), 0)) qty", 
					array("det_beli dbl", "barang br", "master_po mp"), array("bl.no_faktur_beli=dbl.no_faktur_beli", "br.kd_brg=dbl.kode_barang", "bl.no_po=mp.no_po"), 
					"bl.no_faktur_beli='".$kode_pembelian."' AND (".$qty_poc." - isnull((select sum(dmu.qty) from master_delivery_note mu join det_delivery_note dmu on mu.no_delivery_note = dmu.no_delivery_note where mu.no_faktur_beli = bl.no_faktur_beli and dmu.kd_brg=dbl.kode_barang), 0)) > 0"
				);
			} else {
				$read_data_pembelian = $this->m_crud->join_data("master_beli bl", "br.barcode, (dbl.jumlah_beli - isnull((select sum(dmu.qty) from master_delivery_note mu join det_delivery_note dmu on mu.no_delivery_note = dmu.no_delivery_note where mu.no_faktur_beli = bl.no_faktur_beli and dmu.kd_brg=dbl.kode_barang), 0)) qty", array("det_beli dbl", "barang br"), array("bl.no_faktur_beli=dbl.no_faktur_beli", "br.kd_brg=dbl.kode_barang"), "bl.no_faktur_beli='".$kode_pembelian."' AND (dbl.jumlah_beli - isnull((select sum(dmu.qty) from master_delivery_note mu join det_delivery_note dmu on mu.no_delivery_note = dmu.no_delivery_note where mu.no_faktur_beli = bl.no_faktur_beli and dmu.kd_brg=dbl.kode_barang), 0)) > 0");
            }
			$where_stock = "";
        }

        foreach ($read_data_pembelian as $row) {
            if($get_nota['m12']==null || $get_nota['m12']=='' || $get_nota['m12']==1 || $get_nota['m12']=='1'){
				$qty = $row['qty'];	
			} else {
				$qty = 0;
			}
			
            $get_barang = $this->m_crud->get_data("barang", "kd_brg, nm_brg, Deskripsi, satuan, hrg_beli, hrg_jual_1, hrg_jual_2, hrg_jual_3, hrg_jual_4, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE Kartu_stock.kd_brg = barang.kd_brg AND Kartu_stock.lokasi = '".$lokasi."'".$where_stock.") stock", "barcode = '".$row['barcode']."'");
			
            $this->insert_tr_temp_d_dn($no_mutasi, $get_barang, $row['barcode'], $param, $qty, $kode_pembelian);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        }else {
            $this->db->trans_commit();
            echo true;
        }
    }
	
	public function delete_tr_temp_d_dn($tmp_barcode, $param = null) {
        $param = base64_decode($param);
        $barcode = base64_decode($tmp_barcode);
		
        if ($param == 'edit') {
            $this->m_crud->delete_data("tr_temp_d", "(d12 = '".$this->user."') AND (d11 = '".$barcode."') AND (SUBSTRING(d1,1,3) in ('DN-'))");
        } else {
            $this->m_crud->delete_data("tr_temp_d", "(d12 = '".$this->user."') AND (d11 = '".$barcode."') AND (d1 in ('DN'))");
        }

        echo true;
    }
	
	public function get_barang_dn($tmp_no_mutasi, $tmp_barcode, $tmp_lokasi_asal, $tmp_cat_cari, $param = null) {
        $param = base64_decode($param);
        $cat_cari = base64_decode($tmp_cat_cari);
        $no_mutasi = base64_decode($tmp_no_mutasi);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi_asal));
        $lokasi_asal = $explode_lokasi[0];

        if ($cat_cari == 1) {
            $col_barang = 'barang.kd_brg';
            $col_tmp = 'd2';
        } else if ($cat_cari == 2) {
            $col_barang = 'barang.barcode';
            $col_tmp = 'd11';
        } else if ($cat_cari == 3) {
            $col_barang = 'barang.Deskripsi';
            $col_tmp = 'd3';
        } else {
            $col_barang = 'barang.kd_packing';
            $col_tmp = 'd17';
        }

        if ($param == 'edit') {
            $get_tmp_data_m = $this->m_crud->get_data("tr_temp_m", "m1", "(m2 = '".$this->user."') AND (SUBSTRING(m1,1,3) in ('DN-'))");
            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d10, d18, d15", "(SUBSTRING(d1,1,3) in ('DN-')) AND (".$col_tmp." = '".$barcode."') AND (d12 = '".$this->user."')");
            $where_stock = " AND Kartu_stock.kd_trx <> '".$get_tmp_data_m['m1']."'";
        } else {
            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d10, d18, d15", "(d1 in ('DN')) AND (".$col_tmp." = '".$barcode."') AND (d12 = '".$this->user."')");
            $where_stock = "";
        }

        if ($get_tmp_data != '') {
            if ($cat_cari == 4) {
                $data = array (
                    'd10' => (int)$get_tmp_data['d10'] + (int)$get_tmp_data['d18']
                );
            } else {
                $data = array (
                    'd10' => (int)$get_tmp_data['d10'] + 1
                );
            }

            if ($param == 'edit') {
                $this->m_crud->update_data("tr_temp_d", $data, "(d12 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND (SUBSTRING(d1,1,3) in ('DN-'))");
            } else {
                $this->m_crud->update_data("tr_temp_d", $data, "(d12 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND (d1 in ('DN'))");
            }
            echo json_encode(array('status' => 1, 'barang' => 'tersedia', 'col' => $get_tmp_data['d15']));
        }else {
            /*AND (barang_hrg.lokasi = '".$lokasi_asal."') */
            $get_barang = $this->m_crud->get_data("barang", "kd_brg, Deskripsi, barcode, nm_brg, satuan, hrg_beli, hrg_jual_1, hrg_jual_2, hrg_jual_3, hrg_jual_4, kd_packing, qty_packing, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE lokasi NOT IN ('MUTASI', 'Retur') AND Kartu_stock.kd_brg = barang.kd_brg AND lokasi='".$lokasi_asal."'".$where_stock.") stock", "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
            if ($get_barang != '') {
                if ($cat_cari == 4) {
                    $qty = $get_barang['qty_packing'];
                } else {
                    $qty = 1;
                }
                if ($param == 'edit') {
                    $get_nota = $this->m_crud->get_data("tr_temp_m", "m7, m11", "(m2 = '".$this->user."') AND (SUBSTRING(m1,1,3) in ('DN-'))");
                } else {
                    $get_nota = $this->m_crud->get_data("tr_temp_m", "m7, m11", "(m2 = '".$this->user."') AND (m1 in ('DN'))");
                }
				
				if($get_nota['m11']=='2'){
					$qty=0;
				}
				
                $this->insert_tr_temp_d_dn($no_mutasi, $get_barang, $get_barang['barcode'], $param, $qty, $get_nota['m7']);
                if ($get_barang['stock'] > 0) {
                }
                echo json_encode(array('status' => 1, 'barang' => 'baru'));
            }else {
                echo json_encode(array('status' => 2, 'notif' => "Barang dari lokasi ".$lokasi_asal." tidak tersedia!"));
            }
        }
    }
	
	public function get_list_barang_dn() {
        $param = $_POST['param'];
        if ($param == 'edit') {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m1", "(m2 = '".$this->user."') AND (SUBSTRING(m1,1,3) in ('DN-'))");
            $where_stock = " AND Kartu_stock.kd_trx <> '".$get_nota['m1']."'";
        } else {
            $where_stock = "";
        }
        //AND Lokasi = '".$_POST['lokasi_asal_']."  AND (barang_hrg.lokasi = '".$_POST['lokasi_asal_']."' "(barang_hrg.barang = barang.kd_brg)"
        $read_barang = $this->m_crud->read_data("barang, barang_hrg", "barang.kd_brg, barang.barcode, barang.nm_brg, barang.Deskripsi, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE lokasi NOT IN ('MUTASI', 'Retur') AND kd_brg = barang.kd_brg".$where_stock.") stock", "barang.kd_brg=barang_hrg.barang", null, null, 50);
        $list_barang = '';

        foreach ($read_barang as $row) {
            if ($row['stock'] > 0) {
                $list_barang .= '<tr>
                                <td class="text-center td_check"><label class="label_check"><input type="checkbox" id="barang" name="barang" value="' . $row['barcode'] . '"></label></td>
                                <td>' . $row['kd_brg'] . '</td>
                                <td>' . $row['barcode'] . '</td>
                                <td>' . $row['nm_brg'] . '</td>
                                <td>' . $row['Deskripsi'] . '</td>
                             </tr>';
            }
        }

        if (count($read_barang) == 0) {
            $list_barang .= '<tr><td colspan="5" class="text-center">Barang dari lokasi '.$_POST['lokasi_asal_']. ' tidak tersedia!</td></tr>';
        }

        echo json_encode(array('list_barang' => $list_barang, 'lokasi' => $_POST['lokasi_asal_']));
    }
	
	public function add_list_barang_dn() {
        $param = $_POST['param'];
        $no_mutasi = $_POST['no_mutasi_'];
        $list_barcode = $_POST['list_'];

        if ($param == 'edit') {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m7, m1", "(m2 = '".$this->user."') AND (SUBSTRING(m1,1,3) in ('DN-'))");
            $where_stock = " AND Kartu_stock.kd_trx <> '".$get_nota['m1']."'";
        } else {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m7", "(m2 = '".$this->user."') AND (m1 in ('DN'))");
            $where_stock = "";
        }

        for ($i = 0; $i < count($list_barcode); $i++) {
            if ($param == 'edit') {
                $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d10, d11", "(SUBSTRING(d1,1,3) in ('DN-')) AND (d11 = '".$list_barcode[$i]."') AND (d12 = '".$this->user."')");
            } else {
                $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d10, d11", "(d1 in ('DN')) AND (d11 = '".$list_barcode[$i]."') AND (d12 = '".$this->user."')");
            }

            if ($cek_tr_temp_d == '') {
                $get_barang = $this->m_crud->get_data("barang", "kd_brg, Deskripsi, nm_brg, satuan, hrg_beli, hrg_jual_1, hrg_jual_2, hrg_jual_3, hrg_jual_4, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE lokasi NOT IN ('MUTASI', 'Retur') AND Kartu_stock.kd_brg = barang.kd_brg".$where_stock.") stock", "barcode = '".$list_barcode[$i]."'");
                $this->insert_tr_temp_d_dn($no_mutasi, $get_barang, $list_barcode[$i], $param,1, $get_nota['m8']);
            } else {
                $this->update_tr_temp_d_dn(base64_encode($cek_tr_temp_d['d11']), base64_encode('d10'), base64_encode($cek_tr_temp_d['d10'] + 1), base64_encode($param));
            }
        }

        echo true;
    }
	
	public function delete_trans_delivery_note($param = null) {
        $param = base64_decode($param);

        if ($param == 'edit') {
            $this->m_crud->delete_data("tr_temp_m", "(m2 = '".$this->user."') AND (SUBSTRING(m1,1,3) in ('DN-'))");
            $this->m_crud->delete_data("tr_temp_d", "(d12 = '".$this->user."') AND (SUBSTRING(d1,1,4) in ('DN-'))");
        } else {
            $this->m_crud->delete_data("tr_temp_m", "(m2 = '".$this->user."') AND (m1 in ('DN'))");
            $this->m_crud->delete_data("tr_temp_d", "(d12 = '".$this->user."') AND (d1 in ('DN'))");
        }

        $this->m_crud->delete_data("master_delivery_note", "no_delivery_note not in (select no_delivery_note from det_delivery_note)");
    }
	
	public function get_sub_total_dn($param = null) {
        if ($param == 'edit') {
            $read_data = $this->m_crud->read_data("tr_temp_d", "d5, d10", "(d12 = '".$this->user."') AND (SUBSTRING(d1,1,3) in ('DN-'))", "d2");
        } else {
            $read_data = $this->m_crud->read_data("tr_temp_d", "d5, d10", "(d12 = '".$this->user."') AND (d1 in ('DN'))", "d2");
        }
        $sub_total = 0;
        foreach ($read_data as $row) {
            $hitung_sub_total = $row['d5'] * $row['d10'];
            $sub_total = $sub_total + $hitung_sub_total;
        }

        return $sub_total;
    }
	
	public function trans_delivery_note() {
        $this->access_denied(37);
        $param = $_POST['param'];
        $tgl_mutasi = $_POST['tanggal'];
        $lokasi_asal = $_POST['lokasi_asal'];
        $serial_asal = $this->m_crud->get_data('lokasi', 'serial', "kode='".$lokasi_asal."'")['serial'];
        $lokasi_tujuan = $_POST['lokasi_tujuan'];
        $kode = 'DN';
		$status = 0;
        $catatan = $_POST['catatan'];
        $kode_pembelian = $_POST['kode_pembelian'];

        if ($param == 'edit') {
            $no_mutasi = $_POST['no_delivery_note'];
            $get_kode = $this->m_crud->get_data("master_delivery_note", "no_delivery_note, tanggal", "no_delivery_note = '".$no_mutasi."'");
            if (substr($get_kode['tanggal'],0,10)!=$tgl_mutasi) {
                $no_mutasi = $this->m_website->generate_kode($kode, $lokasi_asal, substr(str_replace('-', '', $tgl_mutasi), 2));
            } 
        } else {
            $no_mutasi = $this->m_website->generate_kode($kode, $lokasi_asal, substr(str_replace('-', '', $tgl_mutasi), 2));
        }

        $this->db->trans_begin();
        if ($param == 'edit') {
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d12 = '".$this->user."') AND d1='".$_POST['no_delivery_note']."'");

            $this->m_crud->delete_data("master_delivery_note", "no_delivery_note='".$get_kode['no_delivery_note']."'");
            $this->m_crud->delete_data("det_delivery_note", "no_delivery_note='".$get_kode['no_delivery_note']."'");
            $sub_total = $this->get_sub_total_dn('edit');
        } else {
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d12 = '".$this->user."') AND (d1 in ('DN'))");
            $sub_total = $this->get_sub_total_dn('add');
        }
		
        $data_mutasi = array(
            'tanggal' => $tgl_mutasi . " " . date("H:i:s"),
            'no_delivery_note' => $no_mutasi,
            'kd_lokasi_1' => $lokasi_asal,
            'kd_lokasi_2' => $lokasi_tujuan,
            'status' => $status,
            'keterangan' => $catatan,
            'kd_kasir' => $this->user,
            'total' => $sub_total,
            'no_faktur_beli' => $kode_pembelian
        );
        $this->m_crud->create_data("master_delivery_note", $data_mutasi);

        $det_log = array();
        //foreach ($read_temp_d as $row) {
        for($i=1; $i<=$_POST['col']; $i++){
			if($_POST['d10'.$i] > 0){
				$data_detail_mutasi = array(
					'no_delivery_note' => $no_mutasi,
					'kd_brg' => $_POST['d2'.$i],//$row['d2'],
					'qty' => $_POST['d10'.$i],//$row['d10'],
					'hrg_beli' => $_POST['d5'.$i],//$row['d5'],
					'hrg_jual' => $_POST['d6'.$i]//$row['d6']
				);
				$this->m_crud->create_data("det_delivery_note", $data_detail_mutasi);
				array_push($det_log, $data_detail_mutasi);
			}
        }

        if ($param == 'edit') {
            $data_mutasi['trx_old'] = $get_kode['no_delivery_note'];
        }
        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$no_mutasi,'jenis'=>ucfirst($param),'transaksi'=>ucfirst('Delivery Note')), array('master'=>$data_mutasi,'detail'=>$det_log));

        $this->delete_trans_delivery_note(base64_encode($param));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status'=>false));
        }else {
            $this->db->trans_commit();
            echo json_encode(array('status'=>true, 'kode'=>$no_mutasi));
        }
    }
	
	public function cek_mutasi() {
	    $kode_mutasi = $_POST['kode_mutasi'];

	    $status = $this->m_crud->count_data("master_mutasi", "no_faktur_mutasi", "status<>0 AND no_faktur_beli='".$kode_mutasi."'");

	    if ($status > 0) {
	        echo false;
        } else {
	        echo true;
        }
    }
	
	public function delete_trx_delivery_note() {
        $kode_mutasi = $_POST['kode_mutasi'];
        $read_kode_packing = $this->m_crud->read_data("master_mutasi", "no_faktur_mutasi", "no_faktur_beli='".$kode_mutasi."'");

        $this->m_crud->delete_data("master_delivery_note", "no_delivery_note='".$kode_mutasi."'");
        $this->m_crud->delete_data("det_delivery_note", "no_delivery_note='".$kode_mutasi."'");
        foreach ($read_kode_packing as $row) {
            $this->m_crud->delete_data("Kartu_stock", "kd_trx='" . $row['no_faktur_mutasi'] . "'");
            $this->m_crud->delete_data("det_mutasi", "no_faktur_mutasi='" . $row['no_faktur_mutasi'] . "'");
            $this->m_crud->delete_data("master_mutasi", "no_faktur_mutasi='" . $row['no_faktur_mutasi'] . "'");
        }

        echo true;
    }
	/*End modul delivery_note*/
	
	/*Start modul alokasi*/
	public function alokasi() {
        $this->access_denied(31);
        $data = $this->data;
        $function = 'alokasi';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Alokasi';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data("Lokasi", "Kode, nama_toko Nama, serial", $this->where_lokasi);
        $data['data_lokasi_tujuan'] = $this->m_crud->read_data("Lokasi", "Kode, nama_toko Nama, serial");
        $data['data_pembelian'] = array();//$this->m_crud->read_data("master_beli", "no_faktur_beli", null, "tgl_beli DESC", null, 50);

        $this->load->view('bo/index', $data);
    }

    public function edit_alokasi($tmp_kode_alokasi){
        //$this->access_denied(13);
        $kode_alokasi = base64_decode($tmp_kode_alokasi);
        $data = $this->data;
        $function = 'edit_alokasi';
        $view = $this->control . '/';
		
        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Edit Alokasi';
        $data['page'] = $function;
        $data['content'] = $view.$function;

        $this->db->trans_begin();
        $get_data_alokasi = $this->m_crud->get_data("Master_Mutasi", "no_faktur_mutasi, tgl_mutasi, keterangan, isnull(no_faktur_beli, '-') no_faktur_beli, kd_lokasi_1+'|'+(SELECT serial FROM Lokasi WHERE kode=kd_lokasi_1) lk1, kd_lokasi_2+'|'+(SELECT serial FROM Lokasi WHERE kode=kd_lokasi_2) lk2, kd_lokasi_1", "no_faktur_mutasi='".$kode_alokasi."'");

        if (substr($get_data_alokasi['no_faktur_mutasi'], 0, 2) == 'MU') {
            $jns_trx = 'alokasi';
            $where_stock = "";
        } else {
            $jns_trx = 'branch';
            $where_stock = " AND Kartu_stock.kd_trx <> '".$get_data_alokasi['no_faktur_mutasi']."'";
        }

        //$read_data_alokasi = $this->m_crud->read_data("Det_Mutasi dm, barang br", "dm.*, br.barcode, br.nm_brg, br.Deskripsi, br.satuan, br.kd_packing, br.qty_packing, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE lokasi NOT IN ('MUTASI', 'Retur') AND Kartu_stock.kd_brg = br.kd_brg AND lokasi='".$get_data_alokasi['kd_lokasi_1']."'".$where_stock.") stock", "dm.kd_brg=br.kd_brg AND dm.no_faktur_mutasi='".$get_data_alokasi['no_faktur_mutasi']."'");
        $read_data_alokasi = $this->m_crud->read_data("Det_Mutasi dm, barang br", "dm.*, br.barcode, br.nm_brg, br.Deskripsi, br.satuan, br.kd_packing, br.qty_packing, (select ddn.qty from det_delivery_note ddn where ddn.kd_brg=dm.kd_brg and ddn.no_delivery_note='".$get_data_alokasi['no_faktur_beli']."') stock", "dm.kd_brg=br.kd_brg AND dm.no_faktur_mutasi='".$get_data_alokasi['no_faktur_mutasi']."'");

        $data['data_lokasi'] = $this->m_crud->read_data("Lokasi", "Kode, Nama, serial");
        $data['data_pembelian'] = $this->m_crud->read_data("master_beli", "no_faktur_beli", null, "tgl_beli DESC", null, 50);

        $get_tmp_data = $this->m_crud->count_data("tr_temp_m", "m1", "m6='".$this->user."' AND m10='".$get_data_alokasi['no_faktur_mutasi']."' AND m9='edit'");

        if ($get_tmp_data == 0) {
            $this->m_crud->delete_data("tr_temp_m", array('m9' => 'edit', 'm6' => $this->user));
            $this->m_crud->delete_data("tr_temp_d", array('d19' => 'edit', 'd12' => $this->user));
            /*Add to master temporary*/
            $data_tmp_m = array(
                'm1' => $get_data_alokasi['no_faktur_mutasi'],
                'm2' => substr($get_data_alokasi['tgl_mutasi'], 0, 10),
                'm3' => $get_data_alokasi['lk1'],
                'm4' => $get_data_alokasi['lk2'],
                'm5' => $get_data_alokasi['keterangan'],
                'm6' => $this->user,
                'm7' => $jns_trx,
                'm8' => $get_data_alokasi['no_faktur_beli'],
                'm9' => 'edit',
                'm10' => $get_data_alokasi['no_faktur_mutasi'],
				'm11' => 1
            );

            $this->m_crud->create_data("tr_temp_m", $data_tmp_m);

            $id = 1;

            /*Add to detail temporary*/
            foreach ($read_data_alokasi as $get_barang) {
                $data_tmp_d = array(
                    'd1' => $get_barang['no_faktur_mutasi'],
                    'd2' => $get_barang['kd_brg'],
                    'd3' => $get_barang['Deskripsi'],
                    'd4' => $get_barang['satuan'],
                    'd5' => $get_barang['hrg_beli'],
                    'd6' => $get_barang['hrg_jual'],
                    'd7' => 0,
                    'd8' => 0,
                    'd9' => 0,
                    'd10' => $get_barang['qty'],
                    'd11' => $get_barang['barcode'],
                    'd12' => $this->user,
                    'd13' => $get_barang['stock'],
                    'd14' => $get_barang['no_faktur_beli'],
                    'd15' => $id++,
                    'd16' => $get_barang['nm_brg'],
                    'd17' => $get_barang['kd_packing'],
                    'd18' => $get_barang['qty_packing'],
                    'd19' => 'edit',
                    'd20' => $get_barang['no_faktur_mutasi']
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
            'm1' => $_POST['no_mutasi'],
            'm2' => $_POST['tgl_mutasi'],
            'm6' => $this->user
        );

        /*
            'm1' => $_POST['no_mutasi'],
            'm2' => $_POST['tgl_mutasi'],
            'm3' => $_POST['lokasi_asal'],
            'm4' => $_POST['lokasi_tujuan'],
            'm5' => $_POST['catatan'],
            'm6' => $this->user,
            'm7' => $_POST['jns_trx']
        */
		
        if ($param == 'edit') {
            $get_tmp_m = $this->m_crud->get_data("tr_temp_m", "m10", "m6='".$this->user."' AND m9='edit'");
            $data['m10'] = $get_tmp_m['m10'];
            $data['m9'] = 'edit';
            $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "m9 = 'edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
        } else {
            $data['m9'] = 'add';
            $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "m9 = 'add' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
        }

        if ($cek_data == 1) {
            if ($param == 'edit') {
                $this->m_crud->update_data("tr_temp_m", $data, "m9 = 'edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
                $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['no_mutasi']), "d19 = 'edit' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
            } else {
                $this->m_crud->update_data("tr_temp_m", $data, "m9 = 'add' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
                $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['no_mutasi']), "d19 = 'add' AND (d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
            }
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }
    }

    public function get_tr_temp_m($param = null) {
        $param = base64_decode($param);
        if ($param == 'edit') {
            $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m9 = 'edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
        } else {
            $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m9 = 'add' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
        }

        if (count($get_data) != 0) {
            echo json_encode(array('status' => 1, 'temp' => $get_data));
        } else {
            echo json_encode(array('status' => 0));
        }
    }

    public function get_list_barang() {
        $param = $_POST['param'];
        if ($param == 'edit') {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m10", "m9 = 'edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
            $where_stock = " AND Kartu_stock.kd_trx <> '".$get_nota['m10']."'";
        } else {
            $where_stock = "";
        }
        //AND Lokasi = '".$_POST['lokasi_asal_']."  AND (barang_hrg.lokasi = '".$_POST['lokasi_asal_']."' "(barang_hrg.barang = barang.kd_brg)"
        $read_barang = $this->m_crud->read_data("barang, barang_hrg", "barang.kd_brg, barang.barcode, barang.nm_brg, barang.Deskripsi, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE lokasi NOT IN ('MUTASI', 'Retur') AND kd_brg = barang.kd_brg".$where_stock.") stock", "barang.kd_brg=barang_hrg.barang", null, null, 50);
        $list_barang = '';

        foreach ($read_barang as $row) {
            if ($row['stock'] > 0) {
                $list_barang .= '<tr>
                                <td class="text-center td_check"><label class="label_check"><input type="checkbox" id="barang" name="barang" value="' . $row['barcode'] . '"></label></td>
                                <td>' . $row['kd_brg'] . '</td>
                                <td>' . $row['barcode'] . '</td>
                                <td>' . $row['nm_brg'] . '</td>
                                <td>' . $row['Deskripsi'] . '</td>
                             </tr>';
            }
        }

        if (count($read_barang) == 0) {
            $list_barang .= '<tr><td colspan="5" class="text-center">Barang dari lokasi '.$_POST['lokasi_asal_']. ' tidak tersedia!</td></tr>';
        }

        echo json_encode(array('list_barang' => $list_barang, 'lokasi' => $_POST['lokasi_asal_']));
    }

    public function get_tr_temp_d($param = null) {
        $param = base64_decode($param);
        $list_barang = '';
        if ($param == 'edit') {
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d19 = 'edit' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))", "CONVERT(INTEGER, d15) ASC");
            $get_nota = $this->m_crud->get_data("tr_temp_m", "isnull(m8, '-') m8", "m9 = 'edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
        } else {
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d19 = 'add' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))", "CONVERT(INTEGER, d15) ASC");
            $get_nota = $this->m_crud->get_data("tr_temp_m", "isnull(m8, '-') m8", "m9 = 'add' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
        } 
        $no = 1;
        $col = 0;
        $qty = 1;
        $total_qty = 0;
        $sub_total = 0;
        $length = count($read_data);
        foreach ($read_data as $row) {
            $total_qty = $total_qty + (int)$row['d10'];
            $hitung_sub_total = (float)$row['d5'] * (int)$row['d10'];
            $sub_total = $sub_total + ((float)$row['d5'] * (int)$row['d10']);
            if ((int)$row['d13'] <= 0 || (int)$row['d13']-(int)$row['d10'] < 0 || (int)$row['d10'] <= 0) {
                $id = $no;
                $qty = -1;
                $value = (int)$row['d10'];
            }
			
			if(($row['d10']+0)<($row['d13']+0) && ($row['d10']+0)!=0){
				$style = 'style="background: #ff4d4d;"'; 
			} else if(($row['d10']+0)>($row['d13']+0)){
				$style = 'style="background: #ffff00;"'; 
			} else {
				$style = 'style="background: ;"';
			}
			
            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d11'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button></td>
                                <td>' . $row['d2'] . '</td>
                                <td>' . $row['d11'] . '</td>
                                <td>' . $row['d16'] . '</td>
                                <td>' . $row['d3'] . '</td>
                                <td>' . $row['d4'] . '</td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d5\', $(this).val())" onkeyup="hitung_barang(\'d5\', \'' . $no . '\', $(this).val(), '.$length.')" type="number" id="d5' . $no . '" name="d5' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d5'], 2, '.', '') . '" readonly></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d6\', $(this).val())" type="number" id="d6' . $no . '" name="d6' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d6'], 2, '.', '') . '" readonly></td>
                                <input onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d7\', $(this).val())" type="hidden" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d7'], 2, '.', '') . '" readonly>
                                <input onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d8\', $(this).val())" type="hidden" id="d8' . $no . '" name="d8' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d8'], 2, '.', '') . '" readonly>
                                <input onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d9\', $(this).val())" type="hidden" id="d9' . $no . '" name="d9' . $no . '" class="form-control width-uang" value="' . number_format((float)$row['d9'], 2, '.', '') . '" readonly>
                                <td><input type="number" id="d13' . $no . '" name="d13' . $no . '" class="form-control width-diskon" value="' . ($row['d13'] + 0) . '" readonly></td>
                                <td><input '.$style.' onblur="update_tmp_detail(\'' . $row['d11'] . '\', \'d10\', $(this).val())" onkeyup="hitung_barang(\'d10\', \'' . $no . '\', $(this).val(), '.$length.'); return to_barcode(event)" onclick="$(this).select()" type="number" id="d10' . $no . '" name="d10' . $no . '" class="form-control width-diskon" value="' . ($row['d10'] + 0) . '"><b class="error" id="alr_jumlah_' . $no . '"></b></td>
                                <td><input type="number" id="sub_total' . $no . '" name="sub_total' . $no . '" class="form-control width-uang" value="'.$hitung_sub_total.'" readonly></td>
                            </tr>';
            $col = $no;
            $no++;
        }

        if ($get_nota != null) {
            $kode_pembelian = $get_nota['m8'];
        } else {
            $kode_pembelian = '-';
        }

        $list_barang .= '
        <tr>
            <th colspan="10" class="text-right">TOTAL QTY</th>
            <th class="text-center"><b id="total_qty">'.($total_qty+0).'</b></th>
            <th class="text-right"></th>
        </tr>
        ';
        $list_barang .= '<input type="hidden" id="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang, 'sub_total' => number_format((float)$sub_total, 2, '.', ''), 'kode_pembelian' => $kode_pembelian, 'qty' => $qty, 'id' => $id, 'length' => $length, 'value' => $value));
    }

    public function add_list_barang() {
        $param = $_POST['param'];
        $no_mutasi = $_POST['no_mutasi_'];
        $list_barcode = $_POST['list_'];

        if ($param == 'edit') {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m8, m10", "m9 = 'edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
            $where_stock = " AND Kartu_stock.kd_trx <> '".$get_nota['m10']."'";
        } else {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m8", "m9 = 'add' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
            $where_stock = "";
        }

        for ($i = 0; $i < count($list_barcode); $i++) {
            if ($param == 'edit') {
                $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d10, d11", "d19 = 'edit' AND (SUBSTRING(d1,1,2) in ('MU', 'MC')) AND (d11 = '".$list_barcode[$i]."') AND (d12 = '".$this->user."')");
            } else {
                $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d10, d11", "d19 = 'add' AND (SUBSTRING(d1,1,2) in ('MU', 'MC')) AND (d11 = '".$list_barcode[$i]."') AND (d12 = '".$this->user."')");
            }

            if ($cek_tr_temp_d == '') {
                $get_barang = $this->m_crud->get_data("barang", "kd_brg, Deskripsi, nm_brg, satuan, hrg_beli, hrg_jual_1, hrg_jual_2, hrg_jual_3, hrg_jual_4, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE lokasi NOT IN ('MUTASI', 'Retur') AND Kartu_stock.kd_brg = barang.kd_brg".$where_stock.") stock", "barcode = '".$list_barcode[$i]."'");
                $this->insert_tr_temp_d($no_mutasi, $get_barang, $list_barcode[$i], $param,1, $get_nota['m8']);
            } else {
                $this->update_tr_temp_d(base64_encode($cek_tr_temp_d['d11']), base64_encode('d10'), base64_encode($cek_tr_temp_d['d10'] + 1), base64_encode($param));
            }
        }

        echo true;
    }

    public function update_tr_temp_m($tmp_trx, $tmp_col, $tmp_val, $param = null) {
        $param = base64_decode($param);
	    $trx = base64_decode($tmp_trx);
	    $col = base64_decode($tmp_col);
	    $val = base64_decode($tmp_val);

	    if ($val != '') {
	        if ($param == 'edit') {
                $this->m_crud->update_data("tr_temp_m", array($col => $val), "m9 = 'edit' AND m1='" . $trx . "' AND m6 = '" . $this->user . "'");
            } else {
                $this->m_crud->update_data("tr_temp_m", array($col => $val), "m9 = 'add' AND m1='" . $trx . "' AND m6 = '" . $this->user . "'");
            }
        }
    }

    public function update_tr_temp_d($tmp_barcode, $tmp_column, $tmp_value, $param = null) {
        $param = base64_decode($param);
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        if ($param == 'edit') {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "d19 = 'edit' AND (SUBSTRING(d1,1,2) in ('MU', 'MC')) AND (d11 = '".$barcode."') AND (d12 = '".$this->user."')");
        } else {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "d19 = 'add' AND (SUBSTRING(d1,1,2) in ('MU', 'MC')) AND (d11 = '".$barcode."') AND (d12 = '".$this->user."')");
        }
    }

    public function delete_tr_temp_d($tmp_barcode, $param = null) {
        $param = base64_decode($param);
        $barcode = base64_decode($tmp_barcode);

        /*$get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d10", "(SUBSTRING(d1,1,2) in ('MU', 'MC')) AND (d11 = '".$barcode."') AND (d12 = '".$this->user."')");

        if ($get_tmp_data['d10'] > 1) {
            $data = array(
                'd10' => (int)$get_tmp_data['d10'] - 1
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d12 = '".$this->user."') AND (d11 = '".$barcode."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
        }else {
            $this->m_crud->delete_data("tr_temp_d", "(d12 = '".$this->user."') AND (d11 = '".$barcode."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
        }*/

        if ($param == 'edit') {
            $this->m_crud->delete_data("tr_temp_d", "d19 = 'edit' AND (d12 = '".$this->user."') AND (d11 = '".$barcode."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
        } else {
            $this->m_crud->delete_data("tr_temp_d", "d19 = 'add' AND (d12 = '".$this->user."') AND (d11 = '".$barcode."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
        }

        echo true;
    }

    public function insert_tr_temp_d($nota_sistem, $get_barang, $barcode, $param = null, $qty=1, $kode_pembelian='-') {
        $data = array( 
            'd1' => $nota_sistem,
            'd2' => $get_barang['kd_brg'],
            'd3' => $get_barang['Deskripsi'],
            'd4' => $get_barang['satuan'],
            'd5' => $get_barang['hrg_beli'],
            'd6' => $get_barang['hrg_jual_1'],
            'd7' => $get_barang['hrg_jual_2'],
            'd8' => $get_barang['hrg_jual_3'],
            'd9' => $get_barang['hrg_jual_4'],
            'd10' => $qty,
            'd11' => $barcode,
            'd12' => $this->user,
            'd13' => $get_barang['stock'],
            'd14' => $kode_pembelian,
            'd16' => $get_barang['nm_brg'],
            'd17' => $get_barang['kd_packing'],
            'd18' => $get_barang['qty_packing']
        );

        if ($param == 'edit') {
            $get_tmp_d = $this->m_crud->get_data("tr_temp_d", "d20", "d12='".$this->user."' AND d19='edit'");
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d15)) id", "d19='edit' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
            $data['d15'] = ((int)$get_max_id['id']+1);
            $data['d19'] = 'edit';
            $data['d20'] = $get_tmp_d['d20'];
        } else {
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d15)) id", "d19='add' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
            $data['d15'] = ((int)$get_max_id['id']+1);
            $data['d19'] = 'add';
        }

        $this->m_crud->create_data("tr_temp_d", $data);
    }

    public function add_data_pembelian($param = null) {
        $param = base64_decode($param);
        if ($param == 'edit') {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m1, m3, m4, m8, m10", "m9 = 'edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
        } else {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m1, m3, m4, m8, m10", "m9 = 'add' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
        }
        $no_mutasi = $get_nota['m1'];
        $tmp_lokasi = $get_nota['m3'];
        $tmp_lokasi_to = $get_nota['m4'];
        $kode_pembelian = $get_nota['m8'];
        $faktur_mutasi = $get_nota['m10'];
        $explode_lokasi = explode('|', $tmp_lokasi);
        $explode_lokasi_to = explode('|', $tmp_lokasi_to);
        $lokasi = $explode_lokasi[0];
        $lokasi_to = $explode_lokasi_to[0];
		
		//$po = $this->m_crud->get_join_data('master_beli mb', "jenis_po", array('master_po mp'), array("mb.no_po=mp.no_po"), "mb.no_faktur_beli = '".$kode_pembelian."'");
		
        $this->db->trans_begin();
		
        $this->delete_tmp_d();
		
		//$qty_poc = "isnull((select sum(dqpc.qty) from detail_qty_po_cabang dqpc join master_receive_order mro on dqpc.no_receive_order=mro.no_receive_order join master_order mo on mro.no_order=mo.no_order where dqpc.no_po=bl.no_po and dqpc.kd_brg=dbl.kode_barang and mo.lokasi='".$lokasi_to."'),0)";
        if ($param == 'edit') {
            /*if($po['jenis_po']=='POC'){
				$read_data_pembelian = $this->m_crud->join_data("master_beli bl", 
					"br.barcode, (".$qty_poc." - isnull((select sum(dmu.qty) from Master_Mutasi mu join Det_Mutasi dmu on mu.no_faktur_mutasi = dmu.no_faktur_mutasi where mu.kd_lokasi_2='".$lokasi_to."' and mu.no_faktur_beli = bl.no_faktur_beli and mu.no_faktur_mutasi <> '".$faktur_mutasi."' and dmu.kd_brg=dbl.kode_barang), 0)) qty", 
					array("det_beli dbl", "barang br", "master_po mp"), array("bl.no_faktur_beli=dbl.no_faktur_beli", "br.kd_brg=dbl.kode_barang", "bl.no_po=mp.no_po"), 
					"bl.no_faktur_beli='".$kode_pembelian."' AND (".$qty_poc." - isnull((select sum(dmu.qty) from Master_Mutasi mu join Det_Mutasi dmu on mu.no_faktur_mutasi = dmu.no_faktur_mutasi where and mu.no_faktur_beli = bl.no_faktur_beli and mu.no_faktur_mutasi <> '".$faktur_mutasi."' and dmu.kd_brg=dbl.kode_barang), 0)) > 0"
				);
            } else*/ {
				$read_data_pembelian = $this->m_crud->join_data("master_delivery_note bl", "br.barcode, (CONVERT(INTEGER, dbl.qty) - isnull((select sum(dmu.qty) from Master_Mutasi mu join Det_Mutasi dmu on mu.no_faktur_mutasi = dmu.no_faktur_mutasi where mu.no_faktur_beli = bl.no_delivery_note and mu.no_faktur_mutasi <> '".$faktur_mutasi."' and dmu.kd_brg=dbl.kd_brg), 0)) qty", array("det_delivery_note dbl", "barang br"), array("bl.no_delivery_note=dbl.no_delivery_note", "br.kd_brg=dbl.kd_brg"), "bl.no_delivery_note='".$kode_pembelian."' AND (dbl.qty - isnull((select sum(dmu.qty) from Master_Mutasi mu join Det_Mutasi dmu on mu.no_faktur_mutasi = dmu.no_faktur_mutasi where mu.no_faktur_beli = bl.no_delivery_note and mu.no_faktur_mutasi <> '".$faktur_mutasi."' and dmu.kd_brg=dbl.kd_brg), 0)) > 0");
            }
			$where_stock = " AND Kartu_stock.kd_trx <> '".$faktur_mutasi."'";
        } else {
            /*if($po['jenis_po']=='POC'){
				$read_data_pembelian = $this->m_crud->join_data("master_beli bl", 
					"br.barcode, (".$qty_poc." - isnull((select sum(dmu.qty) from Master_Mutasi mu join Det_Mutasi dmu on mu.no_faktur_mutasi = dmu.no_faktur_mutasi where mu.kd_lokasi_2='".$lokasi_to."' and mu.no_faktur_beli = bl.no_faktur_beli and dmu.kd_brg=dbl.kode_barang), 0)) qty", 
					array("det_beli dbl", "barang br", "master_po mp"), array("bl.no_faktur_beli=dbl.no_faktur_beli", "br.kd_brg=dbl.kode_barang", "bl.no_po=mp.no_po"), 
					"bl.no_faktur_beli='".$kode_pembelian."' AND (".$qty_poc." - isnull((select sum(dmu.qty) from Master_Mutasi mu join Det_Mutasi dmu on mu.no_faktur_mutasi = dmu.no_faktur_mutasi where mu.no_faktur_beli = bl.no_faktur_beli and dmu.kd_brg=dbl.kode_barang), 0)) > 0"
				);
			} else*/ {
				$read_data_pembelian = $this->m_crud->join_data("master_delivery_note bl", "br.barcode, (CONVERT(INTEGER, dbl.qty) - isnull((select sum(dmu.qty) from Master_Mutasi mu join Det_Mutasi dmu on mu.no_faktur_mutasi = dmu.no_faktur_mutasi where mu.no_faktur_beli = bl.no_delivery_note and dmu.kd_brg=dbl.kd_brg), 0)) qty", array("det_delivery_note dbl", "barang br"), array("bl.no_delivery_note=dbl.no_delivery_note", "br.kd_brg=dbl.kd_brg"), "bl.no_delivery_note='".$kode_pembelian."' AND (dbl.qty - isnull((select sum(dmu.qty) from Master_Mutasi mu join Det_Mutasi dmu on mu.no_faktur_mutasi = dmu.no_faktur_mutasi where mu.no_faktur_beli = bl.no_delivery_note and dmu.kd_brg=dbl.kd_brg), 0)) > 0");
            }
			$where_stock = "";
        }

        foreach ($read_data_pembelian as $row) {
            $qty = $row['qty'];

            //$get_barang = $this->m_crud->get_data("barang", "kd_brg, nm_brg, Deskripsi, satuan, hrg_beli, hrg_jual_1, hrg_jual_2, hrg_jual_3, hrg_jual_4, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE Kartu_stock.kd_brg = barang.kd_brg AND Kartu_stock.lokasi = '".$lokasi."'".$where_stock.") stock", "barcode = '".$row['barcode']."'");
            $get_barang = $this->m_crud->get_data("barang", "kd_brg, nm_brg, Deskripsi, satuan, hrg_beli, hrg_jual_1, hrg_jual_2, hrg_jual_3, hrg_jual_4, CONVERT(INTEGER, ".$qty.") stock", "barcode = '".$row['barcode']."'");
			
            $this->insert_tr_temp_d($no_mutasi, $get_barang, $row['barcode'], $param, ($qty-$qty), $kode_pembelian);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        }else {
            $this->db->trans_commit();
            echo true;
        }
    }

    public function get_data_pembelian($tmp_lokasi, $param = null) {
        $param = base64_decode($param);
        $decode_lokasi = base64_decode($tmp_lokasi);
        $explode_lokasi = explode('|', $decode_lokasi);
        $lokasi = $explode_lokasi[0];
		$decode_lokasi_tujuan = base64_decode($_POST['lokasi_tujuan']);
        $explode_lokasi_tujuan = explode('|', $decode_lokasi_tujuan);
        $lokasi_tujuan = $explode_lokasi_tujuan[0];

        if ($param == 'edit') {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m1, m3, m10, isnull(m8, '-') m8", "m9 = 'edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
        } else {
            $get_nota = $this->m_crud->get_data("tr_temp_m", "m1, m3, isnull(m8, '-') m8", "m9 = 'add' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
        }

        if ($decode_lokasi==$get_nota['m3']) {
            $status = '1';
            $read_kode_pembelian = $this->m_crud->join_data("master_delivery_note mb", "mb.no_delivery_note", "det_delivery_note dbl", "dbl.no_delivery_note=mb.no_delivery_note", "mb.kd_lokasi_1 = '".$lokasi."' and mb.kd_lokasi_2 = '".$lokasi_tujuan."'", "mb.no_delivery_note DESC", "mb.no_delivery_note", 0, 0, "SUM(dbl.qty) > isnull((select sum(dmu.qty) from Master_Mutasi mu join Det_Mutasi dmu on mu.no_faktur_mutasi = dmu.no_faktur_mutasi where mu.no_faktur_mutasi <> '".$get_nota['m10']."' AND mu.no_faktur_beli = mb.no_delivery_note), 0)");
        } else {
            $status = '0';
            $read_kode_pembelian = $this->m_crud->join_data("master_delivery_note mb", "mb.no_delivery_note", "det_delivery_note dbl", "dbl.no_delivery_note=mb.no_delivery_note", "mb.kd_lokasi_1 = '".$lokasi."' and mb.kd_lokasi_2 = '".$lokasi_tujuan."'", "mb.no_delivery_note DESC", "mb.no_delivery_note", 0, 0, "SUM(dbl.qty) > isnull((select sum(dmu.qty) from Master_Mutasi mu join Det_Mutasi dmu on mu.no_faktur_mutasi = dmu.no_faktur_mutasi where mu.no_faktur_beli = mb.no_delivery_note), 0)");
        }

        $list_kode = '<option value="-">Pilih</option>';

        if (count($read_kode_pembelian) != 0) {
            foreach ($read_kode_pembelian as $row) {
                $list_kode .= '<option value="'.$row['no_delivery_note'].'">'.$row['no_delivery_note'].'</option>';
            }
        }

        echo json_encode(array('list' => $list_kode, 'kode' => $get_nota['m8'], 'status' => $status));
    }

    public function get_barang($tmp_no_mutasi, $tmp_barcode, $tmp_lokasi_asal, $tmp_cat_cari, $param = null) {
        $param = base64_decode($param);
        $cat_cari = base64_decode($tmp_cat_cari);
        $no_mutasi = base64_decode($tmp_no_mutasi);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi_asal));
        $lokasi_asal = $explode_lokasi[0];

        if ($cat_cari == 1) {
            $col_barang = 'barang.kd_brg';
            $col_tmp = 'd2';
        } else if ($cat_cari == 2) {
            $col_barang = 'barang.barcode';
            $col_tmp = 'd11';
        } else if ($cat_cari == 3) {
            $col_barang = 'barang.Deskripsi';
            $col_tmp = 'd3';
        } else {
            $col_barang = 'barang.kd_packing';
            $col_tmp = 'd17';
        }

        if ($param == 'edit') {
            $get_tmp_data_m = $this->m_crud->get_data("tr_temp_m", "m10", "m9 = 'edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d10, d18, d15", "d19 = 'edit' AND (SUBSTRING(d1,1,2) in ('MU', 'MC')) AND (".$col_tmp." = '".$barcode."') AND (d12 = '".$this->user."')");
            $where_stock = " AND Kartu_stock.kd_trx <> '".$get_tmp_data_m['m10']."'";
        } else {
            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d10, d18, d15", "d19 = 'add' AND (SUBSTRING(d1,1,2) in ('MU', 'MC')) AND (".$col_tmp." = '".$barcode."') AND (d12 = '".$this->user."')");
            $where_stock = "";
        }

        if ($get_tmp_data != '') {
            if ($cat_cari == 4) {
                $data = array (
                    'd10' => (int)$get_tmp_data['d10'] + (int)$get_tmp_data['d18']
                );
            } else {
                $data = array (
                    'd10' => (int)$get_tmp_data['d10'] + 1
                );
            }

            if ($param == 'edit') {
                $this->m_crud->update_data("tr_temp_d", $data, "d19 = 'edit' AND (d12 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
            } else {
                $this->m_crud->update_data("tr_temp_d", $data, "d19 = 'add' AND (d12 = '".$this->user."') AND (".$col_tmp." = '".$barcode."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
            }
            echo json_encode(array('status' => 1, 'barang' => 'tersedia', 'col' => $get_tmp_data['d15']));
        }else {
            /*AND (barang_hrg.lokasi = '".$lokasi_asal."') */
            $get_barang = $this->m_crud->get_data("barang", "kd_brg, Deskripsi, barcode, nm_brg, satuan, hrg_beli, hrg_jual_1, hrg_jual_2, hrg_jual_3, hrg_jual_4, kd_packing, qty_packing, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE lokasi NOT IN ('MUTASI', 'Retur') AND Kartu_stock.kd_brg = barang.kd_brg AND lokasi='".$lokasi_asal."'".$where_stock.") stock", "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
            if ($get_barang != '') {
                if ($cat_cari == 4) {
                    $qty = $get_barang['qty_packing'];
                } else {
                    $qty = 1;
                }
                if ($param == 'edit') {
                    $get_nota = $this->m_crud->get_data("tr_temp_m", "m8", "m9 = 'edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
                } else {
                    $get_nota = $this->m_crud->get_data("tr_temp_m", "m8", "m9 = 'add' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
                }
                $this->insert_tr_temp_d($no_mutasi, $get_barang, $get_barang['barcode'], $param, $qty, $get_nota['m8']);
                if ($get_barang['stock'] > 0) {
                }
                echo json_encode(array('status' => 1, 'barang' => 'baru'));
            }else {
                echo json_encode(array('status' => 2, 'notif' => "Barang dari lokasi ".$lokasi_asal." tidak tersedia!"));
            }
        }
    }

    public function delete_tmp_d($param = null) {
        $param = base64_decode($param);
        if ($param == 'edit') {
            $this->m_crud->delete_data("tr_temp_d", "d19 = 'edit' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
        } else {
            $this->m_crud->delete_data("tr_temp_d", "d19 = 'add' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
        }
    }

    public function delete_trans_mutasi($param = null) {
        $param = base64_decode($param);

        if ($param == 'edit') {
            $this->m_crud->delete_data("tr_temp_m", "m9 = 'edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
            $this->m_crud->delete_data("tr_temp_d", "d19 = 'edit' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
        } else {
            $this->m_crud->delete_data("tr_temp_m", "m9 = 'add' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
            $this->m_crud->delete_data("tr_temp_d", "d19 = 'add' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
        }

        $this->m_crud->delete_data("Master_Mutasi", "no_faktur_mutasi not in (select no_faktur_mutasi from Det_Mutasi)");
    }

    public function trans_mutasi_x() {
        $this->access_denied(31);
        $param = $_POST['param'];
        $tgl_mutasi = $_POST['tgl_mutasi'];
        $tmp_lokasi_asal = $_POST['lokasi_asal'];
        $explode_lokasi_asal = explode('|', $tmp_lokasi_asal);
        $lokasi_asal = $explode_lokasi_asal[0];
        $serial_asal = $explode_lokasi_asal[1];
        $tmp_lokasi_tujuan = $_POST['lokasi_tujuan'];
        $explode_lokasi_tujuan = explode('|', $tmp_lokasi_tujuan);
        $lokasi_tujuan = $explode_lokasi_tujuan[0];
        $lokasi = array($lokasi_asal, $lokasi_tujuan);
        $jns_trx = $_POST['jns_trx'];
        if ($jns_trx == 'alokasi') {
            $kode = 'MU';
            $status = 2;
        } else {
            $kode = 'MC';
            $status = 2;
        }
        $catatan = $_POST['catatan'];
        $kode_pembelian = $_POST['kode_pembelian'];

        if ($param == 'edit') {
            $no_mutasi = $_POST['no_mutasi'];
            $get_kode = $this->m_crud->get_data("tr_temp_m", "m10", "m6 = '".$this->user."' AND (LEFT(m10, 2) = '".substr($no_mutasi, 0, 2)."') AND (SUBSTRING(m10, 4, 6) = '".substr($no_mutasi, 3, 6)."') AND (RIGHT(m10, 1) = '".substr($no_mutasi, 14, 1)."') AND m9 = 'edit'");
            if ($get_kode == '') {
                $no_mutasi = $this->m_website->generate_kode($kode, $serial_asal, substr(str_replace('-', '', $tgl_mutasi), 2));
            } else {
                $no_mutasi = $get_kode['m10'];
            }
        } else {
            $no_mutasi = $this->m_website->generate_kode($kode, $serial_asal, substr(str_replace('-', '', $tgl_mutasi), 2));
        }

        $this->db->trans_begin();
        if ($param == 'edit') {
            $get_temp_m = $this->m_crud->get_data("tr_temp_m", "m10", "m9='edit' AND (m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d19 = 'edit' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");

            $get_data_lokasi = $this->m_crud->get_data("Master_Mutasi", "kd_lokasi_1, kd_lokasi_2", "no_faktur_mutasi='".$get_temp_m['m10']."'");
            $data_lokasi_log = array($get_data_lokasi['kd_lokasi_1'], $get_data_lokasi['kd_lokasi_2']);
            $this->m_crud->delete_data("Master_Mutasi", "no_faktur_mutasi='".$get_temp_m['m10']."'");
            $this->m_crud->delete_data("Det_Mutasi", "no_faktur_mutasi='".$get_temp_m['m10']."'");
            $this->m_crud->delete_data("Kartu_stock", "kd_trx='".$get_temp_m['m10']."'");

            foreach ($data_lokasi_log as $item) {
                $log = array(
                    'type' => 'D',
                    'table' => "Kartu_stock",
                    'data' => "",
                    'condition' => "kd_trx='" . $get_temp_m['m10'] . "'"
                );

                $data_log = array(
                    'lokasi' => $item,
                    'hostname' => '',
                    'db_name' => '',
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);
            }

            $sub_total = $this->get_sub_total('edit');
        } else {
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d19 = 'add' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");
            $sub_total = $this->get_sub_total('add');
        }

        /*$get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "(m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");;
        $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");;
        $sub_total = $this->get_sub_total();*/
		
        $data_mutasi = array(
            'tgl_mutasi' => $tgl_mutasi . " " . date("H:i:s"),
            'no_faktur_mutasi' => $no_mutasi,
            'kd_lokasi_1' => $lokasi_asal,
            'kd_lokasi_2' => $lokasi_tujuan,
            'status' => $status,
            'keterangan' => $catatan,
            'kd_kasir' => $this->user,
            'total' => $sub_total,
            'no_faktur_beli' => $kode_pembelian
        );
        $this->m_crud->create_data("Master_Mutasi", $data_mutasi);

        $det_log_mutasi = array();
        foreach ($read_temp_d as $row) {
            $data_detail_mutasi = array(
                'no_faktur_mutasi' => $no_mutasi,
                'kd_brg' => $row['d2'],
                'qty' => $row['d10'],
                'hrg_beli' => $row['d5'],
                'hrg_jual' => $row['d6']
            );
            $this->m_crud->create_data("Det_Mutasi", $data_detail_mutasi);
            array_push($det_log_mutasi, $data_detail_mutasi);

            if ($jns_trx == 'branch') {
                $data_kartu_stok_out = array(
                    'kd_trx' => $no_mutasi,
                    'tgl' => $tgl_mutasi . " " . date("H:i:s"),
                    'kd_brg' => $row['d2'],
                    'saldo_awal' => 0,
                    'stock_in' => 0,
                    'stock_out' => $row['d10'],
                    'lokasi' => $lokasi_asal,
                    'keterangan' => 'Mutasi Ke '.$lokasi_tujuan,
                    'hrg_beli' => $row['d5']
                );
                $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);
            } else {
                $data_kartu_stok_out = array(
                    'kd_trx' => $no_mutasi,
                    'tgl' => $tgl_mutasi . " " . date("H:i:s"),
                    'kd_brg' => $row['d2'],
                    'saldo_awal' => 0,
                    'stock_in' => 0,
                    'stock_out' => $row['d10'],
                    'lokasi' => $lokasi_asal,
                    'keterangan' => 'Mutasi Ke '.$lokasi_tujuan,
                    'hrg_beli' => $row['d5']
                );
                $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);
                $log = array(
                    'type' => 'I',
                    'table' => "Kartu_stock",
                    'data' => $data_kartu_stok_out,
                    'condition' => ""
                );

                $data_log = array(
                    'lokasi' => $lokasi_asal,
                    'hostname' => '',
                    'db_name' => '',
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);

                $data_kartu_stok_in = array(
                    'kd_trx' => $no_mutasi,
                    'tgl' => $tgl_mutasi . " " . date("H:i:s"),
                    'kd_brg' => $row['d2'],
                    'saldo_awal' => 0,
                    'stock_in' => $row['d10'],
                    'stock_out' => 0,
                    'lokasi' => $lokasi_tujuan,
                    'keterangan' => 'Mutasi Dari '.$lokasi_asal,
                    'hrg_beli' => $row['d5']
                );
                $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_in);

                $log = array(
                    'type' => 'I',
                    'table' => "Kartu_stock",
                    'data' => $data_kartu_stok_in,
                    'condition' => ""
                );

                $data_log = array(
                    'lokasi' => $lokasi_tujuan,
                    'hostname' => '',
                    'db_name' => '',
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);
            }

            /*$data_kartu_stok_out = array(
                'kd_trx' => $no_mutasi,
                'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => 0,
                'stock_out' => $row['d10'],
                'lokasi' => $lokasi_asal,
                'keterangan' => 'Mutasi Ke '.$lokasi_tujuan,
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);

            $data_kartu_stok_in = array(
                'kd_trx' => $no_mutasi,
                'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => $row['d10'],
                'stock_out' => 0,
                'lokasi' => 'MUTASI',
                'keterangan' => 'Mutasi Dari '.$lokasi_asal,
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_in);*/

            /*$this->m_crud->update_data("barang", array('hrg_beli' => $row['d5']), "(kd_brg = '".$row['d2']."')");

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

            if ($kode_pembelian != '-') {
                $this->m_crud->update_data('det_delivery_note', array('status' => 1), "no_delivery_note = '" . $kode_pembelian . "' and kd_brg = '" . $row['d2'] . "'");
            }
        }

		//$this->m_crud->update_data('master_delivery_note', array('status'=>1), "no_delivery_note = '".$kode_pembelian."'");

        if ($param == 'edit') {
            $data_mutasi['trx_old'] = $get_temp_m['m10'];
        }

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$no_mutasi,'jenis'=>ucfirst($param),'transaksi'=>ucfirst($jns_trx)), array('master'=>$data_mutasi,'detail'=>$det_log_mutasi));

        $this->delete_trans_mutasi(base64_encode($param));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status'=>false));
        }else {
            $this->db->trans_commit();
            echo json_encode(array('status'=>true, 'pdf'=>($jns_trx == 'alokasi'?'alokasi_report':'alokasi_by_cabang_report'), 'print'=>($jns_trx == 'alokasi'?'alokasi':'alokasi_by_cabang'), 'kode'=>$no_mutasi));
        }
    }

    /*public function trans_mutasi($tmp_no_mutasi) {
        $this->access_denied(31);
        $no_mutasi = base64_decode($tmp_no_mutasi);
        $get_kode = $this->m_crud->get_data("Master_Mutasi", "no_faktur_mutasi", "(no_faktur_mutasi = '".$no_mutasi."')");

        if ($get_kode != '') {
            $no_mutasi = $this->m_website->generate_kode(substr($get_kode['no_faktur_mutasi'], 0, 2), substr($get_kode['no_faktur_mutasi'], 14), substr($get_kode['no_faktur_mutasi'], 3, 6));
        }

        $this->db->trans_begin();

        $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "(m6 = '".$this->user."') AND (SUBSTRING(m1,1,2) in ('MU', 'MC'))");;
        $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))");;
        $sub_total = $this->get_sub_total();
        $explode_lokasi_asal = explode('|', $get_temp_m['m3']);
        $lokasi_asal = $explode_lokasi_asal[0];
        $explode_lokasi_tujuan = explode('|', $get_temp_m['m4']);
        $lokasi_tujuan = $explode_lokasi_tujuan[0];

        $data_mutasi = array(
            'tgl_mutasi' => $get_temp_m['m2'] . " " . date("H:i:s"),
            'no_faktur_mutasi' => $no_mutasi,
            'kd_lokasi_1' => $lokasi_asal,
            'kd_lokasi_2' => $lokasi_tujuan,
            'status' => 0,
            'keterangan' => $get_temp_m['m5'],
            'kd_kasir' => $get_temp_m['m6'],
            'total' => $sub_total,
            'no_faktur_beli' => $get_temp_m['m8']
        );
        $this->m_crud->create_data("Master_Mutasi", $data_mutasi);

        foreach ($read_temp_d as $row) {
            $data_detail_mutasi = array(
                'no_faktur_mutasi' => $no_mutasi,
                'kd_brg' => $row['d2'],
                'qty' => $row['d10'],
                'hrg_beli' => $row['d5'],
                'hrg_jual' => $row['d6']
            );
            $this->m_crud->create_data("Det_Mutasi", $data_detail_mutasi);

            if ($get_temp_m['m7'] == 'branch') {
                $data_kartu_stok_out = array(
                    'kd_trx' => $no_mutasi,
                    'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                    'kd_brg' => $row['d2'],
                    'saldo_awal' => 0,
                    'stock_in' => 0,
                    'stock_out' => $row['d10'],
                    'lokasi' => $lokasi_asal,
                    'keterangan' => 'Mutasi Ke '.$lokasi_tujuan,
                    'hrg_beli' => $row['d5']
                );
                $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);
            }

            /*$data_kartu_stok_out = array(
                'kd_trx' => $no_mutasi,
                'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => 0,
                'stock_out' => $row['d10'],
                'lokasi' => $lokasi_asal,
                'keterangan' => 'Mutasi Ke '.$lokasi_tujuan,
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);

            $data_kartu_stok_in = array(
                'kd_trx' => $no_mutasi,
                'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => $row['d10'],
                'stock_out' => 0,
                'lokasi' => 'MUTASI',
                'keterangan' => 'Mutasi Dari '.$lokasi_asal,
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_in);/

            $this->m_crud->update_data("master_beli", array('catatan' => $no_mutasi), "no_faktur_beli='".$row['d14']."'");

            /*$this->m_crud->update_data("barang", array('hrg_beli' => $row['d5']), "(kd_brg = '".$row['d2']."')");

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

        $this->delete_trans_mutasi();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        }else {
            $this->db->trans_commit();
            echo true;
        }
    }*/

    public function get_sub_total($param = null) {
        if ($param == 'edit') {
            $read_data = $this->m_crud->read_data("tr_temp_d", "d5, d10", "d19 = 'edit' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))", "d2");
        } else {
            $read_data = $this->m_crud->read_data("tr_temp_d", "d5, d10", "d19 = 'add' AND (d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) in ('MU', 'MC'))", "d2");
        }
        $sub_total = 0;
        foreach ($read_data as $row) {
            $hitung_sub_total = $row['d5'] * $row['d10'];
            $sub_total = $sub_total + $hitung_sub_total;
        }

        return $sub_total;
    }

    public function cek_packing() {
	    $kode_mutasi = $_POST['kode_mutasi'];

	    $status = $this->m_crud->count_data("master_packing", "kd_packing", "status<>0 AND no_faktur_mutasi='".$kode_mutasi."'");

	    if ($status > 0) {
	        echo false;
        } else {
	        echo true;
        }
    }

    public function delete_trx_alokasi() {
        $kode_mutasi = $_POST['kode_mutasi'];
        $read_kode_packing = $this->m_crud->read_data("master_packing", "kd_packing", "no_faktur_mutasi='".$kode_mutasi."'");

        $this->m_crud->delete_data("Master_Mutasi", "no_faktur_mutasi='".$kode_mutasi."'");
        $this->m_crud->delete_data("Det_Mutasi", "no_faktur_mutasi='".$kode_mutasi."'");
        foreach ($read_kode_packing as $row) {
            $this->m_crud->delete_data("Kartu_stock", "kd_trx='" . $row['kd_packing'] . "'");
            $this->m_crud->delete_data("det_packing", "kd_packing='" . $row['kd_packing'] . "'");
            $this->m_crud->delete_data("master_packing", "kd_packing='" . $row['kd_packing'] . "'");
        }

        echo true;
    }
	/*End modul alokasi*/

    //Start Modul Pemrosesan  Paspor
    public function expedisi($action = null, $page=1){
        $this->access_denied(36);
        $data = $this->data;
        $function = 'expedisi';
        $table = 'master_expedisi';
        $view = $this->control.'/';
        $title = 'Expedisi';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['page'] = $function;
        $data['title'] = 'Tambah '.$title;
        $data['content'] = $view.$function;
        $data['table'] = $table;
        $data['data_lokasi'] = $this->m_crud->read_data('lokasi', 'Kode, Nama');
        $this->form_validation->set_rules('tgl_expedisi', 'Tanggal', 'trim|required', array('required' => '%s harus diisi'));
        $where = null;

        if($action == 'edit'){
            $data['title'] = 'Edit '.$title;
            $kode = base64_decode($_GET['trx']);
            $get_data = $this->m_crud->get_data("pemrosesan_paspor", "*", "id_pemrosesan_paspor = '".$kode."'");
            $read_data = $this->m_crud->join_data("det_pemrosesan_paspor dsp", "dsp.permohonan_paspor, pp.id_permohonan_paspor, pp.tanggal, pp.jenis, j.no_ktp, j.nama", array("permohonan_paspor pp","pendaftaran p","jemaah j"), array("pp.id_permohonan_paspor=dsp.permohonan_paspor","p.id_pendaftaran=pp.pendaftaran","j.no_ktp=p.jemaah"), "dsp.pemrosesan_paspor='".$kode."'");
            $this->m_crud->delete_data("tr_temp_m", "LEFT(m1, 3) = 'SP-' AND m2='".$this->user."'");
            $this->m_crud->delete_data("tr_temp_d", "LEFT(d1, 3) = 'SP-' AND d2='".$this->user."'");

            $this->m_crud->create_data("tr_temp_m", array('m1'=>$kode, 'm2'=>$this->user, 'm3'=>$get_data['tanggal'], 'm4'=>$get_data['lokasi']));
            foreach ($read_data as $row) {
                $this->m_crud->create_data("tr_temp_d", array(
                    'd1'=>$kode,
                    'd2'=>$this->user,
                    'd3'=>$row['permohonan_paspor'],
                    'd4'=>$row['tanggal'],
                    'd5'=>$row['jenis'],
                    'd6'=>$row['no_ktp'],
                    'd7'=>$row['nama']
                ));
            }
        } else {
            $kode = 'EX';
            $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m1 = '".$kode."' AND m2 = '".$this->user."'");

            if ($get_data == null) {
                $this->m_crud->create_data("tr_temp_m", array('m1'=>$kode, 'm2'=>$this->user, 'm3'=>date('Y-m-d')));
            }
        }

        if(isset($_POST['save'])) {

            $this->db->trans_begin();
            $param = 'Add';
            $det_log = array();

            if(isset($_POST['update'])) {
                $count_data = $this->m_crud->count_data("pemrosesan_paspor", "id_pemrosesan_paspor", "tanggal='".$_POST['tanggal']."' AND lokasi='".$_POST['lokasi']."'");
                if ($count_data == 0) {
                    $trx_no = $this->m_umroh->generate_kode('SP', $_POST['lokasi'], date("ymd", strtotime($_POST['tanggal'])));
                } else {
                    $trx_no = base64_decode($_GET['trx']);
                }

                $this->m_crud->update_data($table, array(
                    'id_pemrosesan_paspor' => $trx_no,
                    'tanggal' => $_POST['tanggal'],
                    'jam' => date('H:i:s'),
                    'lokasi' => $_POST['lokasi'],
                    'user_detail' => $this->user
                ), "id_pemrosesan_paspor = '".base64_decode($_GET['trx'])."'");

                if ($_POST['param'] != '') {
                    $this->m_crud->update_data("acc_general_journal", array(
                        'id_trx' => $trx_no,
                        'tanggal' => $_POST['tanggal'] . " " . date("H:i:s")
                    ), "id_trx = '" . base64_decode($_GET['trx']) . "' AND RIGHT(descript, LENGTH(descript)-20) IN (" . $_POST['param'] . ")");

                    $this->m_crud->delete_data("det_pemrosesan_paspor", "pemrosesan_paspor='".base64_decode($_GET['trx'])."' AND permohonan_paspor NOT IN (".$_POST['param'].")");

                    $this->m_crud->delete_data("acc_general_journal", "id_trx='".base64_decode($_GET['trx'])."' AND RIGHT(descript, LENGTH(descript)-20) NOT IN (".$_POST['param'].")");
                } else {
                    $this->m_crud->delete_data("det_pemrosesan_paspor", "pemrosesan_paspor='".base64_decode($_GET['trx'])."'");

                    $this->m_crud->delete_data("acc_general_journal", "id_trx='".base64_decode($_GET['trx'])."'");
                }

                for ($i=0; $i<$_POST['max_permohonan']; $i++) {
                    $this->m_crud->create_data("det_pemrosesan_paspor", array(
                        'pemrosesan_paspor' => $trx_no,
                        'permohonan_paspor' => $_POST['permohonan_'.$i]
                    ));
                }

                $this->delete_tr_temp_pemrosesan($_GET['trx']);
            } else {
                $trx_no = $this->m_website->generate_kode('EX', $_POST['lokasi_asal'], date("ymd", strtotime($_POST['tgl_expedisi'])));
                $tanggal = $_POST['tgl_expedisi']. ' ' .date('H:i:s');
                $master_expedisi = array(
                    'kd_expedisi' => $trx_no,
                    'tgl_expedisi' => $tanggal,
                    'lokasi_asal' => $_POST['lokasi_asal'],
                    'lokasi_tujuan' => $_POST['lokasi_tujuan'],
                    'pengirim' => $_POST['pengirim'],
                    'operator' => $this->user,
                    'status' => 0
                );
                $this->m_crud->create_data($table, $master_expedisi);

                for ($i=0; $i<$_POST['max_expedisi']; $i++) {
                    $detail_expedisi = array(
                        'kd_expedisi' => $trx_no,
                        'kd_packing' => $_POST['kd_packing'.$i],
                        'ket' => $_POST['ket'.$i],
                        'jml_koli' => $_POST['koli'.$i],
                        'status' => 0
                    );
                    $this->m_crud->create_data("det_expedisi", $detail_expedisi);
                    array_push($det_log, $detail_expedisi);

					$master_packing = $this->m_crud->get_join_data('master_mutasi mm', "convert(datetime2(0),mp.tgl_packing) tgl_packing, mm.kd_lokasi_1, mm.kd_lokasi_2, mp.no_faktur_mutasi", "master_packing mp", "mm.no_faktur_mutasi=mp.no_faktur_mutasi", "mp.kd_packing='".$_POST['kd_packing'.$i]."'");
					$det_packing = $this->m_crud->join_data('det_packing dp', 'dp.kd_brg, dp.qty, dm.hrg_beli', "det_mutasi dm", "dp.kd_brg=dm.kd_brg", "dm.no_faktur_mutasi='".$master_packing['no_faktur_mutasi']."' and dp.kd_packing='".$_POST['kd_packing'.$i]."' and dp.kd_brg not in (select top 1 ks.kd_brg from kartu_stock ks where ks.kd_trx=dp.kd_packing and ks.kd_brg=dp.kd_brg)");
					foreach($det_packing as $row){
						$data_kartu_stok_out = array(
							'kd_trx' => $_POST['kd_packing'.$i],
							'tgl' => $master_packing['tgl_packing'],
							'kd_brg' => $row['kd_brg'],
							'saldo_awal' => 0,
							'stock_in' => 0,
							'stock_out' => $row['qty'],
							'lokasi' => $master_packing['kd_lokasi_1'],
							'keterangan' => 'Mutasi Ke '.$master_packing['kd_lokasi_2'].' ('.$trx_no.')',
							'hrg_beli' => $row['hrg_beli']
						);
						$this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);

						$data_kartu_stok_in = array(
							'kd_trx' => $_POST['kd_packing'.$i],
							'tgl' => $master_packing['tgl_packing'],
							'kd_brg' => $row['kd_brg'],
							'saldo_awal' => 0,
							'stock_in' => $row['qty'],
							'stock_out' => 0,
							'lokasi' => 'MUTASI',
							'keterangan' => 'Mutasi Dari '.$master_packing['kd_lokasi_1'].' ('.$trx_no.')',
							'hrg_beli' => $row['hrg_beli']
						);
						$this->m_crud->create_data("Kartu_stock", $data_kartu_stok_in);
					}
				}

                $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$trx_no,'jenis'=>ucfirst($param),'transaksi'=>ucfirst('Expedisi')), array('master'=>$master_expedisi,'detail'=>$det_log));

                $this->delete_tr_temp_expedisi();
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('status'=>false));
            } else {
                $this->db->trans_commit();
                if(isset($_POST['update'])) {
                    echo json_encode(array('status'=>true, 'kode'=>$trx_no, 'trx'=>'edit'));
                } else {
                    echo json_encode(array('status'=>true, 'kode'=>$trx_no, 'trx'=>'add'));
                }
            }
        } else {
            $this->load->view('bo/index', $data);
        }

        /*if($this->form_validation->run() == false){
            $this->load->view('bo/index', $data);
        } else {
            if(isset($_POST['save'])){

                $this->db->trans_begin();

                if(isset($_POST['update'])){
                    $count_data = $this->m_crud->count_data("pemrosesan_paspor", "id_pemrosesan_paspor", "tanggal='".$_POST['tanggal']."' AND lokasi='".$_POST['lokasi']."'");
                    if ($count_data == 0) {
                        $trx_no = $this->m_umroh->generate_kode('EX', $_POST['lokasi'], date("ymd", strtotime($_POST['tanggal'])));
                    } else {
                        $trx_no = base64_decode($_GET['trx']);
                    }

                    $this->m_crud->update_data($table, array(
                        'id_pemrosesan_paspor' => $trx_no,
                        'tanggal' => $_POST['tanggal'],
                        'jam' => date('H:i:s'),
                        'lokasi' => $_POST['lokasi'],
                        'user_detail' => $this->user
                    ), "id_pemrosesan_paspor = '".base64_decode($_GET['trx'])."'");

                    if ($_POST['param'] != '') {
                        $this->m_crud->update_data("acc_general_journal", array(
                            'id_trx' => $trx_no,
                            'tanggal' => $_POST['tanggal'] . " " . date("H:i:s")
                        ), "id_trx = '" . base64_decode($_GET['trx']) . "' AND RIGHT(descript, LENGTH(descript)-20) IN (" . $_POST['param'] . ")");

                        $this->m_crud->delete_data("det_pemrosesan_paspor", "pemrosesan_paspor='".base64_decode($_GET['trx'])."' AND permohonan_paspor NOT IN (".$_POST['param'].")");

                        $this->m_crud->delete_data("acc_general_journal", "id_trx='".base64_decode($_GET['trx'])."' AND RIGHT(descript, LENGTH(descript)-20) NOT IN (".$_POST['param'].")");
                    } else {
                        $this->m_crud->delete_data("det_pemrosesan_paspor", "pemrosesan_paspor='".base64_decode($_GET['trx'])."'");

                        $this->m_crud->delete_data("acc_general_journal", "id_trx='".base64_decode($_GET['trx'])."'");
                    }

                    for ($i=0; $i<$_POST['max_permohonan']; $i++) {
                        $this->m_crud->create_data("det_pemrosesan_paspor", array(
                            'pemrosesan_paspor' => $trx_no,
                            'permohonan_paspor' => $_POST['permohonan_'.$i]
                        ));
                    }

                    $this->delete_tr_temp_expedisi($_GET['trx']);
                } else {
                    $trx_no = $this->m_umroh->generate_kode('EX', $_POST['lokasi'], date("ymd", strtotime($_POST['tanggal'])));
                    $this->m_crud->create_data($table, array(
                        'id_pemrosesan_paspor' => $trx_no,
                        'tanggal' => $_POST['tanggal'],
                        'jam' => date('H:i:s'),
                        'lokasi' => $_POST['lokasi'],
                        'user_detail' => $this->user
                    ));

                    for ($i=0; $i<$_POST['max_permohonan']; $i++) {
                        $this->m_crud->create_data("det_pemrosesan_paspor", array(
                            'pemrosesan_paspor' => $trx_no,
                            'permohonan_paspor' => $_POST['permohonan_'.$i]
                        ));

                        $get_permohonan = $this->m_crud->get_join_data("permohonan_paspor pp", "pp.bayar, pp.coa, pp.coa2, ph.paket", array('pendaftaran pd', 'paket_harga ph'), array('pd.id_pendaftaran=pp.pendaftaran', 'ph.id_paket_harga=pd.paket_harga'), "pp.id_permohonan_paspor='".$_POST['permohonan_'.$i]."'");
                        $this->m_crud->create_data("acc_general_journal", array(
                            'tanggal' => $_POST['tanggal'] . " " . date("H:i:s"),
                            'id_trx' => $trx_no,
                            'link_report' => 'Report/pemrosesan_paspor/',
                            'descrip' => 'Pemrosesan Paspor | '.$_POST['permohonan_'.$i],
                            'lokasi' => $_POST['lokasi'],
                            'coa' => $get_permohonan['coa2'],
                            'debit' => 0,
                            'credit' => $get_permohonan['bayar'],
                            'currency' => 1,
                            'rate' => 1,
                            'depart' => $get_permohonan['paket']
                        ));
                        $this->m_crud->create_data("acc_general_journal", array(
                            'tanggal' => $_POST['tanggal'] . " " . date("H:i:s"),
                            'id_trx' => $trx_no,
                            'link_report' => 'Report/pemrosesan_paspor/',
                            'descrip' => 'Pemrosesan Paspor | '.$_POST['permohonan_'.$i],
                            'lokasi' => $_POST['lokasi'],
                            'coa' => $get_permohonan['coa'],
                            'debit' => $get_permohonan['bayar'],
                            'credit' => 0,
                            'currency' => 1,
                            'rate' => 1,
                            'depart' => $get_permohonan['paket']
                        ));
                    }

                    $this->delete_tr_temp_expedisi();
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                    if(isset($_POST['update'])) {
                        echo '<script>alert("Data berhasil disimpan");window.location="' . base_url() . 'Report/' . $function . '"</script>';
                    } else {
                        echo '<script>alert("Data berhasil disimpan");window.location="' . base_url() . $this->control . '/' . $function . '"</script>';
                    }
                }
            }
            $this->load->view('bo/index', $data);
        }*/
    }

    public function get_kode_packing($packing, $lokasi, $lokasi2, $trx=null) {
        $packing = base64_decode($packing);
        $lokasi = base64_decode($lokasi);
        $lokasi2 = base64_decode($lokasi2);
        $kode = ($trx!=null?base64_decode($trx):'EX');

        $count_data = $this->m_crud->count_data("tr_temp_d", "d1", "d1 = '".$kode."' and d3='".$packing."'");

        if ($count_data > 0) {
            $status = 0;
            $notif = "Kode packing sudah dimasukan!";
        } else {
            $get_data = $this->m_crud->get_join_data("master_packing mp", "mp.kd_packing", "Master_Mutasi mm", "mm.no_faktur_mutasi=mp.no_faktur_mutasi", "kd_packing = '".$packing."' AND mp.status='0' AND mm.kd_lokasi_1 = '".$lokasi."' AND mm.kd_lokasi_2 = '".$lokasi2."' AND mp.kd_packing NOT IN (SELECT kd_packing FROM det_expedisi)");
            if ($get_data != null) {
                $this->insert_tr_temp_d_expedisi($get_data['kd_packing'], $trx);
                $status = 1;
                $notif = "";
            } else {
                $status = 0;
                $notif = "Kode packing tidak tersedia!";
            }
        }

        echo json_encode(array('status'=>$status, 'notif'=>$notif));
    }

    public function cari_permohonan($lokasi, $lokasi2, $trx=null) {
        $lokasi = base64_decode($lokasi);
        $lokasi2 = base64_decode($lokasi2);
        $kode = ($trx!=null?base64_decode($trx):'EX');
        $list = '';

        $read_data = $this->m_crud->join_data("master_packing mp", "mp.kd_packing, mp.no_faktur_mutasi", "Master_Mutasi mm", "mm.no_faktur_mutasi=mp.no_faktur_mutasi", "mp.status='0' AND mm.kd_lokasi_1 = '" . $lokasi . "' AND mm.kd_lokasi_2 = '" . $lokasi2 . "' AND mp.kd_packing NOT IN (SELECT kd_packing FROM det_expedisi WHERE kd_expedisi <> '".$kode."') AND mp.kd_packing NOT IN (SELECT d3 FROM tr_temp_d WHERE LEFT(d1, 2) = 'MX')");

        if (count($read_data) > 0) {
            $no = 0;
            foreach ($read_data as $row) {
                $field = 'permohonan_'.$no;
                $list .= '
                <tr>
                    <td>
                    <div class="checkbox checkbox-primary">
                        <input class="form-control permohonan" type="checkbox" id="'.$field.'" name="permohonan" value="'.$row['kd_packing'].'">
                        <label for="'.$field.'"></label>
                    </div>
                    </td>
                    <td>'.$row['kd_packing'].'</td>
                    <td>'.$row['no_faktur_mutasi'].'</td>
                </tr>
                ';
                $no++;
            }
        } else {
            $list = '<tr><td class="text-center" colspan="3">Kode packing tidak tersedia</td></tr>';
        }

        echo json_encode(array('list'=>$list));
    }

    public function tambah_expedisi() {
        $list = $_POST['list_'];
        $kode = $_POST['trx_edit_'];

        for ($i=0; $i<count($list); $i++) {
            $this->insert_tr_temp_d_expedisi($list[$i], $kode);
        }
    }

    public function get_tr_temp_m_expedisi($trx=null) {
        $kode = ($trx!=null?base64_decode($trx):'EX');

        $get_data = $this->m_crud->get_data("tr_temp_m", "*", "m1 = '".$kode."' and m2 = '".$this->user."'");

        echo json_encode(array('status' => 1, 'temp' => $get_data));
    }

    public function update_tr_temp_m_expedisi($col, $val, $trx=null) {
        $kode = ($trx!=null?base64_decode($trx):'EX');
        $col = base64_decode($col);
        $val = base64_decode($val);

        $this->m_crud->update_data("tr_temp_m", array($col => $val), "m1='" . $kode . "' AND m2 = '" . $this->user . "'");
    }

    public function delete_tr_temp_m_expedisi($packing, $trx=null) {
        $packing = base64_decode($packing);
        $kode = ($trx!=null?base64_decode($trx):'EX');

        if ($packing == 'all') {
            $this->m_crud->delete_data("tr_temp_m", "m1='" . $kode . "' AND m2='" . $this->user . "'");
        } else {
            $this->m_crud->delete_data("tr_temp_m", "m1='" . $kode . "' AND m2='" . $this->user . "' AND m3='" . $packing . "'");
        }
    }

    public function insert_tr_temp_d_expedisi($packing, $trx=null) {
        $kode = ($trx!=null?base64_decode($trx):'EX');

        $get_data = $this->m_crud->get_join_data("master_packing mp", "mp.kd_packing, mm.no_faktur_mutasi", "Master_Mutasi mm", "mm.no_faktur_mutasi=mp.no_faktur_mutasi", "kd_packing = '".$packing."'");

        $this->m_crud->create_data("tr_temp_d", array(
            'd1'=>$kode,
            'd2'=>$this->user,
            'd3'=>$get_data['kd_packing'],
            'd4'=>$get_data['no_faktur_mutasi'],
            'd5'=>''
        ));
    }

    public function update_tr_temp_d_expedisi($col, $val, $id, $trx=null) {
        $kode = ($trx!=null?base64_decode($trx):'EX');
        $col = base64_decode($col);
        $val = base64_decode($val);
        $id = base64_decode($id);

        $this->m_crud->update_data("tr_temp_d", array($col => $val), "d1='" . $kode . "' AND d2 = '" . $this->user . "' AND d3 = '".$id."'");
    }

    public function delete_tr_temp_d_expedisi($packing, $trx=null) {
        $packing = base64_decode($packing);
        $kode = ($trx!=null?base64_decode($trx):'EX');

        if ($packing == 'all') {
            $this->m_crud->delete_data("tr_temp_d", "d1='" . $kode . "' AND d2='" . $this->user . "'");
        } else {
            $this->m_crud->delete_data("tr_temp_d", "d1='" . $kode . "' AND d2='" . $this->user . "' AND d3='" . $packing . "'");
        }
    }

    public function get_tr_temp_d_expedisi($trx=null) {
        $kode = ($trx!=null?base64_decode($trx):'EX');
        $list = '';

        $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d1 = '".$kode."' and d2 = '".$this->user."'");

        if (count($read_data) > 0) {
            $status = 1;
            $no = 0;
            $param = [];
            foreach ($read_data as $row) {
                $aksi = '<button type="button" class="btn btn-primary btn-sm" onclick="delete_tmp_detail(\''.$row['d3'].'\')"><span class="md md-close"></span></button>';

                if ($kode != 'EX') {
                    $get_beres = $this->m_crud->get_join_data("det_pemrosesan_paspor dsp", "bp.id_beres_paspor", "beres_paspor bp", "bp.det_pemrosesan_paspor=dsp.id_det_pemrosesan_paspor", "dsp.permohonan_paspor='".$row['d3']."'");
                    if ($get_beres['id_beres_paspor'] != '') {
                        $aksi = '';
                        array_push($param, '\''.$row['d3'].'\'');
                        $no--;
                    } else {
                        $list .= '<input type="hidden" name="packing_'.$no.'" value="'.$row['d3'].'">';
                    }
                } else {
                    $list .= '<input type="hidden" name="packing_'.$no.'" value="'.$row['d3'].'">';
                }

                $count_paket = $this->m_crud->count_data("master_packing", "kd_packing", "no_faktur_mutasi='".$row['d4']."'");
                if ($trx == null) {
                    $count_expedisi1 = $this->m_crud->count_data("det_expedisi", "kd_packing", "kd_packing = '" . $row['d3'] . "'");
                } else {
                    $count_expedisi1 = $this->m_crud->count_data("det_expedisi", "kd_packing", "kd_expedisi != '".$kode."' AND kd_packing = '" . $row['d3'] . "'");
                }
                $count_expedisi2 = $this->m_crud->count_data("tr_temp_d", "d3", "LEFT(d1, 2) = 'EX' AND d3 = '".$row['d3']."'");
                $expedisi = (int)$count_expedisi1 + (int)$count_expedisi2;
                $koli = $expedisi.' / '.$count_paket;

                $list .= '
                    <tr>
                        <td>'.($no+1).'</td>
                        <td class="text-center">'.$aksi.'</td>
                        <td><input type="hidden" name="kd_packing'.$no.'" value="'.$row['d3'].'">'.$row['d3'].'</td>
                        <td>'.$row['d4'].'</td>
                        <td><input type="text" class="form-control" value="'.$row['d5'].'" onchange="update_tmp_detail(\'d5\', $(this).val(), \''.$row['d3'].'\')" id="ket'.$no.'" name="ket'.$no.'"></td>
                        <td><input type="hidden" name="koli'.$no.'" value="'.$koli.'">'.$koli.'</td>
                    </tr>
                ';
                $no++;
            }
            $list .= '<input type="hidden" name="max_expedisi" id="max_expedisi" value="'.$no.'"><input type="hidden" id="param" name="param" value="'.implode(',',$param).'">';
        } else {
            $status = 0;
            $list = '<tr><td class="text-center" colspan="6">Expedisi tidak tersedia</td></tr>';
        }

        echo json_encode(array('status' => $status, 'list' => $list));
    }

    public function delete_tr_temp_expedisi($trx = null) {
        $this->delete_tr_temp_m_expedisi(base64_encode('all'), $trx);
        $this->delete_tr_temp_d_expedisi(base64_encode('all'), $trx);
    }
    //End Modul Pemrosesan Paspor

    /*Start modul approval order*/
    public function approval_order($kd_trx = null){
        $this->access_denied(35);
        $data = $this->data;

        if (isset($_POST['save'])) {
            $max_data = (int)$_POST['max_data'];
            $kd_trx = base64_decode($kd_trx);

            $this->db->trans_begin();

            $param = 'Add';
            $no_mutasi = $this->m_website->generate_kode('MU', 'G', date('ymd'));
            $no_receive = $this->m_website->generate_kode('RO', 'HO', date('ymd'));

            $data_receive = array(
                'no_receive_order' => $no_receive,
                'tgl_receive_order' => date('Y-m-d H:i:s'),
                'operator' => $this->user,
                'no_faktur_mutasi' => $no_mutasi,
                'no_order' => $kd_trx
            );
            $this->m_crud->create_data("master_receive_order", $data_receive);
            $this->m_crud->update_data("master_order", array('status'=>'1'), "no_order = '".$kd_trx."'");
            $get_lokasi = $this->m_crud->get_data("master_order", "lokasi", "no_order='".$kd_trx."'");

            $det_log = array();
            $total = 0;
            for ($i=0; $i<$max_data; $i++) {
                if ($_POST['approval'.$i]!='' && $_POST['approval'.$i]>0) {
                    $data_aprove = array(
                        'no_receive_order' => $no_receive,
                        'kd_brg' => $_POST['kd_brg' . $i],
                        'qty' => $_POST['approval' . $i]
                    );
                    $this->m_crud->create_data("det_receive_order", $data_aprove);
                    array_push($det_log, $data_aprove);
                    $data_detail_mutasi = array(
                        'no_faktur_mutasi' => $no_mutasi,
                        'kd_brg' => $_POST['kd_brg' . $i],
                        'qty' => $_POST['approval' . $i],
                        'hrg_beli' => $_POST['hrg_beli' . $i],
                        'hrg_jual' => $_POST['hrg_jual' . $i]
                    );
                    //$this->m_crud->create_data("Det_Mutasi", $data_detail_mutasi);

                    $total = $total + ((int)$_POST['approval' . $i] * (float)$_POST['hrg_beli' . $i]);
                }
            }

            $data_mutasi = array(
                'tgl_mutasi' => date("Y-m-d H:i:s"),
                'no_faktur_mutasi' => $no_mutasi,
                'kd_lokasi_1' => 'HO',
                'kd_lokasi_2' => $get_lokasi['lokasi'],
                'status' => '0',
                'keterangan' => 'Approve Order',
                'kd_kasir' => $this->user,
                'total' => $total,
                'no_faktur_beli' => '-'
            );
            //$this->m_crud->create_data("Master_Mutasi", $data_mutasi);

            $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$no_receive,'jenis'=>ucfirst($param),'transaksi'=>ucfirst('Approve Order')), array('master'=>$data_receive,'detail'=>$det_log));

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
                echo '<script>alert("Data has been Saved"); window.location="' . base_url() . $this->control . '/approval_order"</script>';
            }
        } else {
            if ($kd_trx == null) {
                $function = 'approval_order';
                $view = $this->control . '/';
                $table = null;
                $data['title'] = 'Approve Order';
                $data['page'] = $function;
                $data['content'] = $view . $function;
                $data['table'] = $table;

                $where = "mo.status = '0' AND mo.lokasi=lk.Kode";
                if (isset($_POST['search'])) {
                    if (isset($_POST['lokasi']) && $_POST['lokasi'] != null) {
                        $lokasi = $_POST['lokasi'];
                        ($where == null) ? null : $where .= " and mo.lokasi = '" . $lokasi . "'";
                    }
                    if (isset($_POST['no_trx']) && $_POST['no_trx'] != null) {
                        $search = $_POST['no_trx'];
                        ($where == null) ? null : $where .= " and mo.no_order like '%" . $search . "%'";
                    }
                }
                $total_item = " ,(SELECT COUNT(qty) FROM det_order do WHERE do.no_order=mo.no_order) total_item";
                $total_qty = " ,(SELECT SUM(qty) FROM det_order do WHERE do.no_order=mo.no_order) total_qty";
                $data['report'] = $this->m_crud->read_data("master_order mo, Lokasi lk", "no_order, tgl_order, lk.Nama" . $total_item . $total_qty, $where);
            } else {
                $function = 'approving_order';
                $view = $this->control . '/';
                $table = null;
                $data['title'] = 'Approving Order';
                $data['page'] = $function;
                $data['content'] = $view . $function;
                $data['table'] = $table;

                $kd_trx = base64_decode($kd_trx);

                $where = "no_order = '" . $kd_trx . "'";
                $data['report'] = $kd_trx;
                $data['report_det'] = $this->m_crud->join_data('det_order do', "SUM(do.qty) qty, br.kd_brg, br.barcode, br.nm_brg, br.hrg_beli, br.hrg_jual_1", 'barang as br', 'do.kd_brg = br.kd_brg', $where, null, 'br.kd_brg, br.barcode, br.nm_brg, br.hrg_beli, br.hrg_jual_1');
            }

            if ($this->form_validation->run() == false) {
                $this->load->view('bo/index', $data);
            } else {
                $this->load->view('bo/index', $data);
            }
        }
    }
    /*End modul approval order*/

	/*Start modul approval alokasi*/
    public function approval_alokasi(){
        $this->access_denied(34);
        $data = $this->data;
        $function = 'approval_alokasi';
        $view = $this->control . '/';
        $table = null;
        $data['title'] = 'Approval Mutasi';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "mm.status = '0' AND mm.kd_lokasi_1=lk.Kode AND mm.kd_lokasi_2 = 'HO' AND LEFT(no_faktur_mutasi, 2) = 'MC'";
        if(isset($_POST['search'])){
            if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; ($where==null)?null:$where.=" and mm.kd_lokasi_1 = '".$lokasi."'"; }
            if(isset($_POST['no_trx'])&&$_POST['no_trx']!=null){ $search = $_POST['no_trx']; ($where==null)?null:$where.=" and mm.no_faktur_mutasi like '%".$search."%'"; }
        }
        $total_item = " ,(SELECT COUNT(qty) FROM Det_Mutasi dm WHERE dm.no_faktur_mutasi=mm.no_faktur_mutasi) total_item";
        $total_qty = " ,(SELECT SUM(qty) FROM Det_Mutasi dm WHERE dm.no_faktur_mutasi=mm.no_faktur_mutasi) total_qty";
        $total_approval = " ,(SELECT SUM(qty) FROM Det_Mutasi dm WHERE dm.no_faktur_mutasi=mm.no_faktur_mutasi AND dm.status='1') total_approval";
        $data['report'] = $this->m_crud->read_data("Master_Mutasi mm, Lokasi lk", "no_faktur_mutasi, tgl_mutasi, lk.Nama".$total_item.$total_qty.$total_approval, $where);

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function approving_alokasi($kd_trx = null) {
        //$this->access_denied(0);
        $data = $this->data;
        $function = 'approving_alokasi';
        $view = $this->control . '/';
        $table = null;
        $data['title'] = 'Approving Mutasi';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $kd_trx = base64_decode($kd_trx);

        $where = "no_faktur_mutasi = '".$kd_trx."'";
        if(isset($_POST['search'])) {
            if(isset($_POST['barcode'])&&$_POST['barcode']!=null){ $search = $_POST['barcode']; ($where==null)?null:$where.=" and kd_brg = '".$search."'"; }
        }
        $data['report'] = $kd_trx;
        $total_qty = " ,(SELECT SUM(qty) FROM Det_Mutasi dms WHERE dms.no_faktur_mutasi='".$kd_trx."' AND dms.kd_brg=dm.kd_brg) total_qty";
        $total_approval = " ,(SELECT SUM(qty) FROM Det_Mutasi dms WHERE dms.no_faktur_mutasi='".$kd_trx."' AND dm.status='1' AND dms.kd_brg=dm.kd_brg) total_approval";
        $data['report_det'] = $this->m_crud->join_data('Det_Mutasi dm', "br.kd_brg, br.barcode, br.nm_brg, dm.hrg_beli".$total_qty.$total_approval, 'barang as br', 'dm.kd_brg = br.kd_brg', $where);

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }
	/*End modul approval alokasi*/

	/*Start modul adjusment*/
    public function adjusment() {
        $this->access_denied(32);
        $data = $this->data;
        $function = 'adjusment';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Adjusment';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_lokasi'] = $this->m_crud->read_data('Lokasi','Kode, nama_toko Nama, serial', $this->where_lokasi);

        $this->load->view('bo/index', $data);
    }

    public function add_tr_temp_m_adj() {
        $data = array(
            'm1' => $_POST['kode_adjusment'],
            'm2' => $_POST['tgl_adjusment'],
            'm5' => $this->user
        );

        /*
            'm3' => $_POST['lokasi'],
            'm4' => $_POST['keterangan']
        */

        $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "(m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'AA')");

        if ($cek_data == 1) {
            $this->m_crud->update_data("tr_temp_m", $data, "(m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'AA')");
            $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['kode_adjusment']), "(d17 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'AA')");
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }
    }

    public function get_tr_temp_m_adj() {
        $get_data = $this->m_crud->get_data("tr_temp_m", "*", "(m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'AA')");

        if (count($get_data) != 0) {
            echo json_encode(array('status' => 1, 'temp' => $get_data));
        } else {
            echo json_encode(array('status' => 0));
        }
    }

    public function update_tr_temp_m_adj($tmp_trx, $tmp_col, $tmp_val) {
        $trx = base64_decode($tmp_trx);
        $col = base64_decode($tmp_col);
        $val = base64_decode($tmp_val);

        if ($val != '') {
            $this->m_crud->update_data("tr_temp_m", array($col => $val), "m1='" . $trx . "' AND m5 = '" . $this->user . "'");
        }
    }

    public function insert_tr_temp_d_adj($nota_sistem, $get_barang, $barcode, $qty=0) {
        $data = array(
            'd1' => $nota_sistem,
            'd2' => $get_barang['kd_brg'],
            'd3' => $get_barang['Deskripsi'],
            'd4' => $get_barang['satuan'],
            'd5' => $get_barang['hrg_beli'],
            'd6' => 'Tambah',
            'd10' => $qty,
            'd11' => $barcode,
            'd12' => $this->user,
            'd13' => $get_barang['stock'],
            'd16' => $get_barang['nm_brg']
        );

        $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d15)) id", "(d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'AA')");
        $data['d15'] = ((int)$get_max_id['id']+1);

        $this->m_crud->create_data("tr_temp_d", $data);
    }

    public function get_tr_temp_d_adj() {
        $list_barang = '';
        $read_data = $this->m_crud->read_data("tr_temp_d", "*", "(d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'AA')", "CONVERT(INTEGER, d15) ASC");
        $col = 0;
        foreach ($read_data as $row) {
            $col++;
            if ($row['d6'] == "Tambah") {
                $hitung_saldo = (int)$row['d10']+(int)$row['d13'];
                $tambah = "selected";
                $kurang = "";
            } else {
                $hitung_saldo = (int)$row['d13']-(int)$row['d10'];
                $kurang = "selected";
                $tambah = "";
            }

            $list_barang .= '<tr>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d11'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button></td>
                                <td>' . $row['d2'] . '</td>
                                <td>' . $row['d11'] . '</td>
                                <td>' . $row['d3'] . '</td>
                                <td>' . $row['d16'] . '</td>
                                <td>' . $row['d4'] . '</td>
                                <td><input type="number" class="form-control text-right" id="d5' . $col . '" name="d5' . $col . '" value="'.number_format((float)$row['d5'], 2, '.', '').'" readonly></td>
                                <!--d13 stock sistem--><td><input type="number" class="form-control text-center" id="d13' . $col . '" name="d13' . $col . '" value="'.($row['d13'] + 0).'" readonly></td>
                                <!--d6 jenis--><td><select class="form-control" style="width: 100px" id="d6' . $col . '" name="d6' . $col . '" onchange="hitung(\''.$col.'\'); update_tmp_detail(\'' . $row['d11'] . '\', \'d6\', $(this).val())"><option value="Tambah" '.$tambah.'>Tambah</option><option value="Kurang" '.$kurang.'>Kurang</option></select></td>
                                <!--d10 qty adjust--><td><input type="number" class="form-control text-center" id="d10' . $col . '" name="d10' . $col . '" onchange="update_tmp_detail(\'' . $row['d11'] . '\', \'d10\', $(this).val())" onfocus="this.select()" onkeyup="hitung(\''.$col.'\'); return to_barcode(event)" value="' . ($row['d10'] + 0) . '"><b class="error" id="alr_stock_adjusment' . $col . '"></b></td>
                                <td><input type="number" class="form-control text-center" id="saldo_stock' . $col . '" name="saldo_stock' . $col . '" value="'.$hitung_saldo.'" readonly></td>
                            </tr>';
        }

        $list_barang .= '<input type="hidden" id="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang));
    }

    public function update_tr_temp_d_adj($tmp_barcode, $tmp_column, $tmp_value) {
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);

        $this->m_crud->update_data("tr_temp_d", array($column => $value), "(SUBSTRING(d1,1,2) = 'AA') AND (d11 = '".$barcode."') AND (d12 = '".$this->user."')");
    }

    public function delete_tr_temp_d_adj($tmp_barcode) {
        $barcode = base64_decode($tmp_barcode);

        $this->m_crud->delete_data("tr_temp_d", "(d12 = '".$this->user."') AND (d11 = '".$barcode."') AND (SUBSTRING(d1,1,2) = 'AA')");

        echo true;
    }

    public function get_adjusment_barang($tmp_no_adj, $tmp_barcode, $tmp_lokasi_asal, $tmp_cat_cari) {
        $cat_cari = base64_decode($tmp_cat_cari);
        $no_adj = base64_decode($tmp_no_adj);
        $barcode = base64_decode($tmp_barcode);
        $explode_lokasi = explode('|', base64_decode($tmp_lokasi_asal));
        $lokasi_asal = $explode_lokasi[0];

        if ($cat_cari == 1) {
            $col_barang = 'barang.kd_brg';
            $col_tmp = 'd2';
        } else if ($cat_cari == 2) {
            $col_barang = 'barang.barcode';
            $col_tmp = 'd11';
        } else if ($cat_cari == 3) {
            $col_barang = 'barang.Deskripsi';
            $col_tmp = 'd3';
        } else {
            $col_barang = 'barang.nm_brg';
            $col_tmp = 'd16';
        }

        $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d1", "(SUBSTRING(d1,1,2) = 'AA') AND (".$col_tmp." = '".$barcode."') AND (d12 = '".$this->user."')");

        if ($get_tmp_data != '') {
            echo json_encode(array('status' => 2, 'notif' => "Barang sudah di masukan!"));
        }else {
            /*AND (barang_hrg.lokasi = '".$lokasi_asal."') */
            $get_barang = $this->m_crud->get_data("barang", "kd_brg, barcode, Deskripsi, nm_brg, satuan, hrg_beli, (SELECT SUM(stock_in - stock_out) FROM Kartu_stock WHERE lokasi NOT IN ('MUTASI', 'Retur') AND Kartu_stock.kd_brg = barang.kd_brg AND lokasi='".$lokasi_asal."') stock", "(rtrim(ltrim(".$col_barang.")) = '".$barcode."')");
            if ($get_barang != '') {
                $this->insert_tr_temp_d_adj($no_adj, $get_barang, $get_barang['barcode']);
                echo json_encode(array('status' => 1));
            }else {
                echo json_encode(array('status' => 2, 'notif' => "Barang dari lokasi ".$lokasi_asal." tidak tersedia!"));
            }
        }
    }

    public function delete_trans_adjusment() {
        $this->m_crud->delete_data("tr_temp_m", "(m5= '".$this->user."') AND (SUBSTRING(m1,1,2) = 'AA')");
        $this->m_crud->delete_data("tr_temp_d", "(d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'AA')");
    }

    public function trans_adjusment_x() {
        $this->access_denied(31);

        $param = 'Add';
        $tgl_adjust = $_POST['tgl_adjusment'];
        $explode_lokasi = explode('|', $_POST['lokasi']);
        $lokasi = $explode_lokasi[0];
        $serial = $explode_lokasi[1];
        $keterangan = $_POST['keterangan'];
        $no_adjusment = $this->m_website->generate_kode("AA", $serial, substr(str_replace('-', '', $tgl_adjust), 2));

        $this->db->trans_begin();

        $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'AA')");;

        $master_adjusment = array(
            'kd_trx' => $no_adjusment,
            'tgl' => $tgl_adjust . " " . date("H:i:s"),
            'kd_kasir' => $this->user,
            'lokasi' => $lokasi,
            'keterangan' => $keterangan
        );
        $this->m_crud->create_data("adjust", $master_adjusment);

        $det_log = array();
        foreach ($read_temp_d as $row) {
            if ($row['d6'] == 'Tambah') {
                $stock_in = $row['d10'];
                $stock_out = 0;
                $saldo_stock = (int)$row['d13'] + (int)$row['d10'];
            } else {
                $stock_in = 0;
                $stock_out = $row['d10'];
                $saldo_stock = (int)$row['d13'] - (int)$row['d10'];
            }

            $data_adjusment = array(
                'kd_trx' => $no_adjusment,
                'kd_brg' => $row['d2'],
                'status' => $row['d6'],
                'qty_adjust' => $row['d10'],
                'stock_terakhir' => $row['d13'],
                'saldo_stock' => $saldo_stock
            );
            $this->m_crud->create_data("det_adjust", $data_adjusment);
            array_push($det_log, $data_adjusment);

            $data_kartu_stok_out = array(
                'kd_trx' => $no_adjusment,
                'tgl' => $tgl_adjust . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => $stock_in,
                'stock_out' => $stock_out,
                'lokasi' => $lokasi,
                'keterangan' => 'Adjustment',
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);
        }

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$no_adjusment,'jenis'=>ucfirst($param),'transaksi'=>ucfirst('Adjustment')), array('master'=>$master_adjusment,'detail'=>$det_log));

        $this->delete_trans_adjusment();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status'=>false));
        }else {
            $this->db->trans_commit();
            echo json_encode(array('status'=>true, 'kode'=>$no_adjusment));
        }
    }

    public function trans_adjusment($tmp_no_adjusment) {
        $this->access_denied(31);
        $no_adjusment = base64_decode($tmp_no_adjusment);
        $get_kode = $this->m_crud->get_data("adjust", "kd_trx", "(kd_trx = '".$no_adjusment."')");

        if ($get_kode != '') {
            $no_adjusment = $this->m_website->generate_kode(substr($get_kode['kd_trx'], 0, 2), substr($get_kode['kd_trx'], 14), substr($get_kode['kd_trx'], 3, 6));
        }

        $this->db->trans_begin();

        $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "(m5 = '".$this->user."') AND (SUBSTRING(m1,1,2) = 'AA')");;
        $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d12 = '".$this->user."') AND (SUBSTRING(d1,1,2) = 'AA')");;

        $explode_lokasi = explode('|', $get_temp_m['m3']);
        $lokasi = $explode_lokasi[0];

        $data_adjusment = array(
            'kd_trx' => $no_adjusment,
            'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
            'kd_kasir' => $this->user,
            'lokasi' => $lokasi,
            'keterangan' => $get_temp_m['m4']
        );
        $this->m_crud->create_data("adjust", $data_adjusment);

        foreach ($read_temp_d as $row) {
            if ($row['d6'] == 'Tambah') {
                $stock_in = $row['d10'];
                $stock_out = 0;
                $saldo_stock = (int)$row['d13'] + (int)$row['d10'];
            } else {
                $stock_in = 0;
                $stock_out = $row['d10'];
                $saldo_stock = (int)$row['d13'] - (int)$row['d10'];
            }

            $data_adjusment = array(
                'kd_trx' => $no_adjusment,
                'kd_brg' => $row['d2'],
                'status' => $row['d6'],
                'qty_adjust' => $row['d10'],
                'stock_terakhir' => $row['d13'],
                'saldo_stock' => $saldo_stock
            );
            $this->m_crud->create_data("det_adjust", $data_adjusment);

            $data_kartu_stok_out = array(
                'kd_trx' => $no_adjusment,
                'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kd_brg' => $row['d2'],
                'saldo_awal' => 0,
                'stock_in' => $stock_in,
                'stock_out' => $stock_out,
                'lokasi' => $lokasi,
                'keterangan' => 'Adjusment',
                'hrg_beli' => $row['d5']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);
        }

        $this->delete_trans_adjusment();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        }else {
            $this->db->trans_commit();
            echo true;
        }
    }
	/*End modul adjusment*/

	/*Start modul packing*/
	public function packing($action = null, $id = null) {
        $this->access_denied(33); 
        $data = $this->data;
        $function = 'packing';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Packing';
        $data['page'] = $function;
        $data['content'] = $view.$function;
        $data['data_mutasi'] = $this->m_crud->read_data("Master_Mutasi", "no_faktur_mutasi", "left(no_faktur_mutasi, 2) = 'MU' AND status='0'", "tgl_mutasi DESC", null);

        $this->load->view('bo/index', $data);
    }

    public function max_kode_packing($tmp_no_faktur_mutasi) {
        $no_faktur_mutasi = base64_decode($tmp_no_faktur_mutasi);

        $get_lokasi = $this->m_crud->get_data("Master_Mutasi", "kd_lokasi_1, kd_lokasi_2, (SELECT serial FROM Lokasi WHERE Kode=kd_lokasi_1) serial1, (SELECT serial FROM Lokasi WHERE Kode=kd_lokasi_2) serial2", "no_faktur_mutasi='".$no_faktur_mutasi."'");
        $barcode = $get_lokasi['serial1'].date('ymd').$get_lokasi['serial2'];
        $get_max_kd_packing = $this->m_crud->get_data("master_packing", "MAX(CONVERT(INTEGER, RIGHT(kd_packing,4))) kd_packing", "LEFT(kd_packing, 8)='".$barcode."'");
        $new_kd_packing = $barcode.sprintf('%04d', $get_max_kd_packing['kd_packing']+1);

        /*$max_kode_packing = $this->m_crud->count_data("master_packing","kd_packing","no_faktur_mutasi='".$no_faktur_mutasi."'");

        $get_kode_mutasi = $this->m_crud->get_data("Master_Mutasi","(SELECT serial FROM Lokasi WHERE Kode=kd_lokasi_1) lokasi_asal, (SELECT serial FROM Lokasi WHERE Kode=kd_lokasi_2) lokasi_tujuan, SUBSTRING(no_faktur_mutasi,4,10) kode_mutasi","no_faktur_mutasi='".$no_faktur_mutasi."'");

        $kode_packing = $get_kode_mutasi['lokasi_asal'].$get_kode_mutasi['kode_mutasi'].$get_kode_mutasi['lokasi_tujuan'].date('ymd').sprintf('%03d', $max_kode_packing+1);*/

        echo $new_kd_packing;
    }

    public function get_barang_packing($tmp_kd_packing, $tmp_no_faktur_mutasi, $tmp_barcode, $tmp_cat_cari, $param = null) {
	    $param = base64_decode($param);
        $cat_cari = base64_decode($tmp_cat_cari);
        $kd_packing = base64_decode($tmp_kd_packing);
        $no_faktur_mutasi = base64_decode($tmp_no_faktur_mutasi);
        $barcode = base64_decode($tmp_barcode);

        if ($cat_cari == 1) {
            $col_barang = 'barang.kd_brg';
            $col_tmp = 'd3';
        } else if ($cat_cari == 2) {
            $col_barang = 'barang.barcode';
            $col_tmp = 'd2';
        } else if ($cat_cari == 3) {
            $col_barang = 'barang.Deskripsi';
            $col_tmp = 'd5';
        } else if($cat_cari == 4){
			$col_barang = 'barang.kd_packing';
            $col_tmp = 'd15';
		}

        if ($param == 'edit') {
            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d9, d10, d12, d14", "(" . $col_tmp . " = '" . $barcode . "') AND (d11 = '" . $this->user . "') AND d13 = 'edit'");
        } else {
            $get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d9, d10, d12", "(" . $col_tmp . " = '" . $barcode . "') AND (d11 = '" . $this->user . "') AND d13 = 'add'");
        }
		
		if ($cat_cari == 4) {
			$qty = $this->m_crud->get_data('barang', 'isnull((qty_packing),0) qty_packing', $col_barang." = '".$barcode."'")['qty_packing'];
		} else {
			$qty = 1;
		}
		
        if ($get_tmp_data != '') { 
			$data = array(
                'd10' => (int)$get_tmp_data['d10'] + $qty
            );

            if ($get_tmp_data['d9'] >= ($get_tmp_data['d10']+$qty)) {
                if ($param == 'edit') {
                    $this->m_crud->update_data("tr_temp_d", $data, "(d11 = '" . $this->user . "') AND (" . $col_tmp . " = '" . $barcode . "') AND d13 = 'edit'");
                } else {
                    $this->m_crud->update_data("tr_temp_d", $data, "(d11 = '" . $this->user . "') AND (" . $col_tmp . " = '" . $barcode . "') AND d13 = 'add'");
                }
            }
			
            echo json_encode(array('status' => 1, 'barang'=>'tersedia', 'col'=>$get_tmp_data['d12']));
        } else { 
            /*AND (barang_hrg.lokasi = '".$lokasi_asal."') */
            if ($param == 'edit') {
                $get_barang = $this->m_crud->get_data("barang, Det_Mutasi", "barang.kd_packing, isnull((qty_packing),0) qty_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, Det_Mutasi.hrg_beli, Det_Mutasi.hrg_jual, Det_Mutasi.qty, isnull((SELECT SUM(dp.qty) FROM det_packing dp, master_packing mp WHERE mp.no_faktur_mutasi=Det_Mutasi.no_faktur_mutasi AND dp.kd_packing=mp.kd_packing AND dp.kd_packing <> '".$get_tmp_data['d14']."' AND ltrim(rtrim(dp.kd_brg))=ltrim(rtrim(barang.kd_brg))), 0) qty_packing", "(ltrim(rtrim(barang.kd_brg)) = ltrim(rtrim(Det_Mutasi.kd_brg))) AND (Det_Mutasi.no_faktur_mutasi = '" . $no_faktur_mutasi . "') AND (rtrim(ltrim(" . $col_barang . ")) = '" . $barcode . "')");
            } else {
                $get_barang = $this->m_crud->get_data("barang, Det_Mutasi", "barang.kd_packing, isnull((qty_packing),0) qty_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, Det_Mutasi.hrg_beli, Det_Mutasi.hrg_jual, Det_Mutasi.qty, isnull((SELECT SUM(dp.qty) FROM det_packing dp, master_packing mp WHERE mp.no_faktur_mutasi=Det_Mutasi.no_faktur_mutasi AND dp.kd_packing=mp.kd_packing AND ltrim(rtrim(dp.kd_brg))=ltrim(rtrim(barang.kd_brg))), 0) qty_packing", "(ltrim(rtrim(barang.kd_brg)) = ltrim(rtrim(Det_Mutasi.kd_brg))) AND (Det_Mutasi.no_faktur_mutasi = '" . $no_faktur_mutasi . "') AND (rtrim(ltrim(" . $col_barang . ")) = '" . $barcode . "')");
            }
            if ($get_barang != '') {
                if (((int)$get_barang['qty']-(int)$get_barang['qty2']) > 0) {
                    if ($param == 'edit') {
                        $this->insert_tr_temp_d_packing($kd_packing, $get_barang, $get_barang['barcode'], 'edit', $qty);
                    } else {
                        $this->insert_tr_temp_d_packing($kd_packing, $get_barang, $get_barang['barcode'], 'add', $qty);
                    }
                }
                echo json_encode(array('status' => 1));
            }else {
                echo json_encode(array('status' => 2, 'notif' => "Barang dari alokasi ".$no_faktur_mutasi." tidak tersedia!"));
            }
        }
    }

    public function get_list_barang_packing() {
        $no_faktur_mutasi = $_POST['no_faktur_mutasi_'];
        $param = $_POST['param_'];

        if ($param == 'edit') {
            $get_tmp_data = $this->m_crud->get_data("tr_temp_m", "m7", "(m5 = '" . $this->user . "') AND m6 = 'edit'");
            $read_barang = $this->m_crud->read_data("barang br, Det_Mutasi dm", "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, dm.qty, isnull((SELECT SUM(dp.qty) FROM det_packing dp, master_packing mp WHERE mp.no_faktur_mutasi=dm.no_faktur_mutasi AND dp.kd_packing=mp.kd_packing AND mp.kd_packing <> '".$get_tmp_data['m7']."' AND ltrim(rtrim(dp.kd_brg))=ltrim(rtrim(br.kd_brg))), 0) qty2", "dm.kd_brg=br.kd_brg AND dm.no_faktur_mutasi='" . $no_faktur_mutasi . "'");
        } else {
            $read_barang = $this->m_crud->read_data("barang br, Det_Mutasi dm", "br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, dm.qty, isnull((SELECT SUM(dp.qty) FROM det_packing dp, master_packing mp WHERE mp.no_faktur_mutasi=dm.no_faktur_mutasi AND dp.kd_packing=mp.kd_packing AND ltrim(rtrim(dp.kd_brg))=ltrim(rtrim(br.kd_brg))), 0) qty2", "dm.kd_brg=br.kd_brg AND dm.no_faktur_mutasi='" . $no_faktur_mutasi . "'");
        }
        $list_barang = '';

        foreach ($read_barang as $row) {
            if (((int)$row['qty']-(int)$row['qty2']) > 0) {
                $list_barang .= '<tr>
                                <td class="text-center td_check">
                                <div class="checkbox checkbox-primary">
                                    <input class="form-control cek_lokasi" type="checkbox" id="barang" name="barang" value="' . $row['barcode'] . '">
                                    <label for="barang"></label>
                                </div>
                                </td>
                                <td>' . $row['kd_brg'] . '</td>
                                <td>' . $row['barcode'] . '</td>
                                <td>' . $row['nm_brg'] . '</td>
                                <td>' . $row['Deskripsi'] . '</td>
                             </tr>';
            }
        }

        echo json_encode(array('list_barang' => $list_barang));
    }

    public function edit_packing($tmp_kode_packing){
        //$this->access_denied(13);
        $kode_packing = base64_decode($tmp_kode_packing);
        $data = $this->data;
        $function = 'edit_packing';
        $view = $this->control . '/';

        //if($this->session->userdata('admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata('admin_menu', $function); }
        $data['title'] = 'Edit Packing';
        $data['page'] = $function;
        $data['content'] = $view.$function;

        $this->db->trans_begin();
        $get_data_packing = $this->m_crud->get_data("master_packing", "*", "kd_packing='".$kode_packing."'");
        $read_data_packing = $this->m_crud->read_data("det_packing dp, Det_Mutasi dm, barang br", "br.kd_packing kode_packing, dp.*, br.barcode, br.nm_brg, br.Deskripsi, br.satuan, dm.hrg_beli, dm.hrg_jual, dm.qty qty_mutasi, isnull((SELECT SUM(dps.qty) FROM det_packing dps, master_packing mps WHERE mps.kd_packing=dps.kd_packing AND mps.no_faktur_mutasi=dm.no_faktur_mutasi AND dps.kd_brg=dp.kd_brg), 0) qty_packing", "dp.kd_brg=br.kd_brg AND dm.no_faktur_mutasi='".$get_data_packing['no_faktur_mutasi']."' AND dp.kd_brg=dm.kd_brg AND dp.kd_packing='".$kode_packing."'");
        $data['data_mutasi'] = $this->m_crud->select_union("no_faktur_mutasi", "Master_Mutasi", "left(no_faktur_mutasi, 2) = 'MU' AND status=0", "no_faktur_mutasi", "Master_Mutasi", "no_faktur_mutasi='".$get_data_packing['no_faktur_mutasi']."'", "no_faktur_mutasi DESC");

        $get_tmp_data = $this->m_crud->count_data("tr_temp_m", "m1", "m5='".$this->user."' AND m7='".$get_data_packing['kd_packing']."' AND m6='edit'");

        if ($get_tmp_data == 0) {
            $this->m_crud->delete_data("tr_temp_m", array('m6' => 'edit', 'm5' => $this->user));
            $this->m_crud->delete_data("tr_temp_d", array('d13' => 'edit', 'd11' => $this->user));
            /*Add to master temporary*/
            $data_tmp_m = array(
                'm1' => $get_data_packing['kd_packing'],
                'm2' => substr($get_data_packing['tgl_packing'], 0, 10),
                'm3' => $get_data_packing['no_faktur_mutasi'],
                'm4' => $get_data_packing['pengirim'],
                'm5' => $this->user,
                'm6' => 'edit',
                'm7' => $get_data_packing['kd_packing'],
                'm8' => $get_data_packing['no_faktur_mutasi'],
                'm9' => 1
            );

            $this->m_crud->create_data("tr_temp_m", $data_tmp_m);

            $id = 1;

            /*Add to detail temporary*/
            foreach ($read_data_packing as $get_barang) {
                $data_tmp_d = array(
                    'd1' => $get_barang['kd_packing'],
                    'd2' => $get_barang['barcode'],
                    'd3' => $get_barang['kd_brg'],
                    'd4' => $get_barang['nm_brg'],
                    'd5' => $get_barang['Deskripsi'],
                    'd6' => $get_barang['satuan'],
                    'd7' => $get_barang['hrg_beli'],
                    'd8' => $get_barang['hrg_jual'],
                    'd9' => $get_barang['qty_mutasi']-$get_barang['qty_packing']+$get_barang['qty'],
                    'd10' => $get_barang['qty'],
                    'd11' => $this->user,
                    'd12' => $id++,
                    'd13' => 'edit',
                    'd14' => $get_data_packing['kd_packing'],
					'd15' => $get_barang['kode_packing']
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

    public function add_tr_temp_m_packing() {
        $param = $_POST['param'];
        $data = array(
            'm1' => $_POST['kd_packing'],
            'm2' => $_POST['tgl_packing'],
            'm3' => $_POST['no_faktur_mutasi'],
            'm4' => $_POST['pengirim'],
            'm5' => $this->user,
			'm9' => $_POST['set_focus']
        );

        if ($param == 'edit') {
            $get_tmp_m = $this->m_crud->get_data("tr_temp_m", "m7", "m5='".$this->user."' AND m6='edit'");
            $data['m7'] = $get_tmp_m['m7'];
            $data['m6'] = 'edit';
            $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "m6='edit' AND (m5 = '".$this->user."')");
        } else {
            $data['m6'] = 'add';
            $cek_data = $this->m_crud->check_data("m1", "tr_temp_m", "m6='add' AND (m5 = '".$this->user."')");
        }

        if ($cek_data == 1) {
            if ($param == 'edit') {
                $get_data = $this->m_crud->get_data("tr_temp_m", "m3", "m6 = 'edit' AND (m5 = '".$this->user."')");
                if ($get_data['m3'] != $_POST['no_faktur_mutasi']) {
                    $this->m_crud->delete_data("tr_temp_d", "d13 = 'edit' AND d11 = '".$this->user."'");
                }
                $this->m_crud->update_data("tr_temp_m", $data, "m6='edit' AND (m5 = '".$this->user."')");
                $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['kd_packing']), "d13='edit' AND (d11 = '".$this->user."')");
            } else {
                $get_data = $this->m_crud->get_data("tr_temp_m", "m3", "m6 = 'add' AND (m5 = '".$this->user."')");
                if ($get_data['m3'] != $_POST['no_faktur_mutasi']) {
                    $this->m_crud->delete_data("tr_temp_d", "d13 = 'add' AND d11 = '".$this->user."'");
                }
                $this->m_crud->update_data("tr_temp_m", $data, "m6='add' AND (m5 = '".$this->user."')");
                $this->m_crud->update_data("tr_temp_d", array("d1" => $_POST['kd_packing']), "d13='add' AND (d11 = '".$this->user."')");
            }
        }else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }

        echo true;

        /*if ($cek_data == 1) {
            if ($get_data['m1'] != $_POST['kd_packing']) {
                $this->m_crud->delete_data("tr_temp_d", "d11 = '".$this->user."'");
            }
            $this->m_crud->update_data("tr_temp_m", $data, "(m5 = '" . $this->user . "')");
        } else {
            $this->m_crud->create_data("tr_temp_m", $data);
        }*/
    }

    public function get_tr_temp_m_packing($param = null) {
        $param = base64_decode($param);
        if ($param == 'edit') {
            $get_data = $this->m_crud->get_data("tr_temp_m", "*", "(m5 = '" . $this->user . "') AND m6 = 'edit'");
        } else {
            $get_data = $this->m_crud->get_data("tr_temp_m", "*", "(m5 = '" . $this->user . "') AND m6 = 'add'");
        }

        if (count($get_data) != 0) {
            echo json_encode(array('status' => 1, 'temp' => $get_data));
        } else {
            echo json_encode(array('status' => 0));
        }
    }

    public function get_tr_temp_d_packing($param = null) {
        $param = base64_decode($param);
        $list_barang = '';
        if ($param == 'edit') {
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d13 = 'edit' AND (d11 = '" . $this->user . "')", "d12");
        } else {
            $read_data = $this->m_crud->read_data("tr_temp_d", "*", "d13 = 'add' AND (d11 = '" . $this->user . "')", "d12");
        }

        $kode_pembelian = '';
        $no = 1;
        $col = 0;
        $qty = 1;
        $sub_total = 0;
        $length = count($read_data);
        foreach ($read_data as $row) {
            $hitung_sub_total = $row['d7'] * $row['d10'];
            $sub_total = $sub_total + $hitung_sub_total;
            if ((int)$row['d13'] <= 0 || (int)$row['d13']-(int)$row['d10'] < 0 || (int)$row['d10'] <= 0) {
                $id = $no;
                $qty = -1;
                $value = (int)$row['d10'];
            }

            $list_barang .= '<tr>
                                <td>' . $no . '</td>
                                <td><button type="button" onclick="hapus_barang(\'' . $row['d2'] . '\')" class="btn btn-primary"><i class="md md-close"></i></button></td>
                                <td>' . $row['d3'] . '</td>
                                <td>' . $row['d4'] . '</td>
                                <td>' . $row['d6'] . '</td>
                                <td><input type="number" id="d7' . $no . '" name="d7' . $no . '" class="form-control width-uang" value="' . ($row['d7'] + 0) . '" readonly></td>
                                <td><input type="number" id="d8' . $no . '" name="d8' . $no . '" class="form-control width-uang" value="' . ($row['d8'] + 0) . '" readonly></td>
                                <td><input type="number" id="d9' . $no . '" name="d9' . $no . '" class="form-control width-diskon" value="' . ($row['d9'] + 0) . '" readonly></td>
                                <td><input onblur="update_tmp_detail(\'' . $row['d2'] . '\', \'d10\', $(this).val())" onkeyup="hitung_barang(\'d10\', \'' . $no . '\', $(this).val(), '.$length.'); return to_barcode(event)" onclick="$(this).select()" type="number" id="d10' . $no . '" name="d10' . $no . '" class="form-control width-diskon" value="' . ($row['d10'] + 0) . '"><b class="error" id="alr_jumlah_' . $no . '"></b></td>
                            </tr>';
            $col = $no;
            $no++;
        }
        $list_barang .= '<input type="hidden" id="col" value="'.$col.'">';

        echo json_encode(array('status' => count($read_data), 'list_barang' => $list_barang, 'length' => $length));
    }

    public function add_list_barang_packing() {
        $kd_packing = $_POST['kd_packing_'];
        $no_faktur_mutasi = $_POST['no_faktur_mutasi_'];
        $list_barcode = $_POST['list_'];
        $param = $_POST['param_'];

        for ($i = 0; $i < count($list_barcode); $i++) {
            if ($param == 'edit') {
                $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d9, d10, d2", "d13 = 'edit' AND (d2 = '" . $list_barcode[$i] . "') AND (d11 = '" . $this->user . "')");
                $get_tmp_data = $this->m_crud->get_data("tr_temp_m", "m7", "(m5 = '" . $this->user . "') AND m6 = 'edit'");
                $get_barang = $this->m_crud->get_data("barang, Det_Mutasi", "barang.kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, Det_Mutasi.hrg_beli, Det_Mutasi.hrg_jual, Det_Mutasi.qty, isnull((SELECT SUM(dp.qty) FROM det_packing dp, master_packing mp WHERE mp.no_faktur_mutasi=Det_Mutasi.no_faktur_mutasi AND dp.kd_packing=mp.kd_packing AND dp.kd_packing <> '".$get_tmp_data['m7']."' AND ltrim(rtrim(dp.kd_brg))=ltrim(rtrim(barang.kd_brg))), 0) qty_packing", "(ltrim(rtrim(barang.kd_brg)) = ltrim(rtrim(Det_Mutasi.kd_brg))) AND (Det_Mutasi.no_faktur_mutasi = '" . $no_faktur_mutasi . "') AND (barang.barcode = '".$list_barcode[$i]."')");
                //$get_barang = $this->m_crud->get_data("barang, Det_Mutasi", "rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, Det_Mutasi.hrg_beli, Det_Mutasi.hrg_jual, Det_Mutasi.qty, isnull((SELECT SUM(dp.qty) FROM det_packing dp, master_packing mp WHERE mp.no_faktur_mutasi=Det_Mutasi.no_faktur_mutasi AND dp.kd_packing=mp.kd_packing AND ltrim(rtrim(dp.kd_brg))=ltrim(rtrim(barang.kd_brg))), 0) qty_packing", "(ltrim(rtrim(barang.kd_brg)) = ltrim(rtrim(Det_Mutasi.kd_brg))) AND (Det_Mutasi.no_faktur_mutasi = '".$no_faktur_mutasi."') AND (barang.barcode = '".$list_barcode[$i]."')");
            } else {
                $cek_tr_temp_d = $this->m_crud->get_data("tr_temp_d", "d9, d10, d2", "d13 = 'add' AND (d2 = '" . $list_barcode[$i] . "') AND (d11 = '" . $this->user . "')");
                $get_barang = $this->m_crud->get_data("barang, Det_Mutasi", "barang.kd_packing, rtrim(ltrim(barang.kd_brg)) kd_brg, rtrim(ltrim(barang.barcode)) barcode, rtrim(ltrim(barang.Deskripsi)) Deskripsi, barang.nm_brg, barang.satuan, Det_Mutasi.hrg_beli, Det_Mutasi.hrg_jual, Det_Mutasi.qty, isnull((SELECT SUM(dp.qty) FROM det_packing dp, master_packing mp WHERE mp.no_faktur_mutasi=Det_Mutasi.no_faktur_mutasi AND dp.kd_packing=mp.kd_packing AND ltrim(rtrim(dp.kd_brg))=ltrim(rtrim(barang.kd_brg))), 0) qty_packing", "(ltrim(rtrim(barang.kd_brg)) = ltrim(rtrim(Det_Mutasi.kd_brg))) AND (Det_Mutasi.no_faktur_mutasi = '".$no_faktur_mutasi."') AND (barang.barcode = '".$list_barcode[$i]."')");
            }

            if ($cek_tr_temp_d == null) {
                $this->insert_tr_temp_d_packing($kd_packing, $get_barang, $list_barcode[$i], $param, ($get_barang['qty']-$get_barang['qty_packing']));
            } else {
                if ($cek_tr_temp_d['d9'] > $cek_tr_temp_d['d10']) {
                    $this->update_tr_temp_d_packing(base64_encode($cek_tr_temp_d['d2']), base64_encode('d10'), base64_encode(($get_barang['qty']-$get_barang['qty_packing'])), base64_encode($param));
                }
            }
        }

        echo true;
    }

    public function update_tr_temp_d_packing($tmp_barcode, $tmp_column, $tmp_value, $param = null) {
        $barcode = base64_decode($tmp_barcode);
        $column = base64_decode($tmp_column);
        $value = base64_decode($tmp_value);
        $param = base64_decode($param);

        if ($param == 'edit') {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "d13 = 'edit' AND (d2 = '" . $barcode . "') AND (d11 = '" . $this->user . "')");
        } else {
            $this->m_crud->update_data("tr_temp_d", array($column => $value), "d13 = 'add' AND (d2 = '" . $barcode . "') AND (d11 = '" . $this->user . "')");
        }
    }

    public function delete_tr_temp_d_packing($tmp_barcode, $param = null) {
        $barcode = base64_decode($tmp_barcode);
        $param = base64_decode($param);

        /*$get_tmp_data = $this->m_crud->get_data("tr_temp_d", "d10", "(d2 = '".$barcode."') AND (d11 = '".$this->user."')");

        if ($get_tmp_data['d10'] > 1) {
            $data = array(
                'd10' => (int)$get_tmp_data['d10'] - 1
            );

            $this->m_crud->update_data("tr_temp_d", $data, "(d11 = '".$this->user."') AND (d2 = '".$barcode."')");
        }else {
            $this->m_crud->delete_data("tr_temp_d", "(d11 = '".$this->user."') AND (d2 = '".$barcode."')");
        }*/

        if ($param == 'edit') {
            $this->m_crud->delete_data("tr_temp_d", "d13 = 'edit' AND (d11 = '".$this->user."') AND (d2 = '".$barcode."')");
        } else {
            $this->m_crud->delete_data("tr_temp_d", "d13 = 'add' AND (d11 = '".$this->user."') AND (d2 = '".$barcode."')");
        }


        echo true;
    }

    public function insert_tr_temp_d_packing($kd_packing, $get_barang, $barcode, $param = null, $qty=1) {
        $data = array(
            'd1' => $kd_packing,
            'd2' => $barcode,
            'd3' => $get_barang['kd_brg'],
            'd4' => $get_barang['nm_brg'],
            'd5' => $get_barang['Deskripsi'],
            'd6' => $get_barang['satuan'],
            'd7' => $get_barang['hrg_beli'],
            'd8' => $get_barang['hrg_jual'],
            'd9' => $get_barang['qty']-$get_barang['qty_packing'],
            'd10' => $qty,
            'd11' => $this->user,
			'd15' => $get_barang['kd_packing']
        );

        /*$get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d12)) id", "d11='".$this->user."'");
        $data['d12'] = ((int)$get_max_id['id']+1);*/

        if ($param == 'edit') {
            $get_tmp_d = $this->m_crud->get_data("tr_temp_d", "d14", "d11='".$this->user."' AND d13='edit'");
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d12)) id", "d11='".$this->user."' AND d13='edit'");
            $data['d14'] = $get_tmp_d['d14'];
            $data['d13'] = 'edit';
            $data['d12'] = ((int)$get_max_id['id']+1);
        } else {
            $get_max_id = $this->m_crud->get_data("tr_temp_d", "MAX(CONVERT(INTEGER, d12)) id", "d11='".$this->user."' AND d13='add'");
            $data['d13'] = 'add';
            $data['d12'] = ((int)$get_max_id['id']+1);
        }

        $this->m_crud->create_data("tr_temp_d", $data);
    }

    public function delete_trans_packing($param = null) {
        $param = base64_decode($param);
        if ($param == 'edit') {
            $this->m_crud->delete_data("tr_temp_m", "m6 = 'edit' AND (m5 = '" . $this->user . "')");
            $this->m_crud->delete_data("tr_temp_d", "d13 = 'edit' AND (d11 = '" . $this->user . "')");
        } else {
            $this->m_crud->delete_data("tr_temp_m", "m6 = 'add' AND (m5 = '" . $this->user . "')");
            $this->m_crud->delete_data("tr_temp_d", "d13 = 'add' AND (d11 = '" . $this->user . "')");
        }

        $this->m_crud->delete_data("master_packing", "kd_packing not in (select kd_packing from det_packing)");
    }

    public function remove_trans_packing() {
	    $this->db->trans_begin();

        $table = $_POST['table'];
        $condition = $_POST['condition'];
        $kode_trx = $_POST['kd_trx_'];

        $get_no_mutasi = $this->m_crud->get_data("master_packing", "no_faktur_mutasi", "kd_packing='".$kode_trx."'");

        $this->m_crud->update_data("Master_Mutasi", array('status'=>'0'), "no_faktur_mutasi='".$get_no_mutasi['no_faktur_mutasi']."'");

        for ($i=0; $i<count($table); $i++) {
            $this->m_crud->delete_data($table[$i], $condition[$i]);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        }else {
            $this->db->trans_commit();
            echo true;
        }
    }

    public function trans_packing_x() {
        $this->access_denied(33);

        $param = $_POST['param'];
        $tgl_packing = $_POST['tgl_packing'];
        $no_faktur_mutasi = $_POST['no_faktur_mutasi'];
        $pengirim = $_POST['pengirim'];
        $get_lokasi = $this->m_crud->get_data("Master_Mutasi", "kd_lokasi_1, kd_lokasi_2, (SELECT serial FROM Lokasi WHERE Kode=kd_lokasi_1) serial1, (SELECT serial FROM Lokasi WHERE Kode=kd_lokasi_2) serial2", "no_faktur_mutasi='".$no_faktur_mutasi."'");
        $barcode = $get_lokasi['serial1'].date('ymd').$get_lokasi['serial2'];
		
        $this->db->trans_begin();
        if ($param == 'edit') {
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d13 = 'edit' AND (d11 = '".$this->user."')");
            $new_kd_packing = $_POST['kd_packing'];
            $get_kode_x = $this->m_crud->get_data("tr_temp_m", "m7, m8", "m5 = '".$this->user."' AND (LEFT(m7, 8) = '".substr($new_kd_packing, 0, 8)."') AND m6 = 'edit'");
            $get_kode = $this->m_crud->get_data("tr_temp_m", "m7, m8", "m5 = '".$this->user."' AND m6 = 'edit'");
            $count_box = $this->m_crud->count_data("master_packing", "kd_packing", "kd_packing <> '".$get_kode['m7']."' AND no_faktur_mutasi='".$no_faktur_mutasi."'");
            if ($get_kode_x == '') {
                $get_max_kd_packing = $this->m_crud->get_data("master_packing", "MAX(CONVERT(INTEGER, RIGHT(kd_packing,4))) kd_packing", "left(kd_packing, len(kd_packing) - 4)='".$barcode."'");
                $new_kd_packing = $barcode.sprintf('%04d', $get_max_kd_packing['kd_packing']+1);
            } else {
                $new_kd_packing = $get_kode_x['m7'];
            }

            $this->m_crud->delete_data("master_packing", "kd_packing='".$get_kode['m7']."'");
            $this->m_crud->delete_data("det_packing", "kd_packing='".$get_kode['m7']."'");
            $this->m_crud->delete_data("Kartu_stock", "kd_trx='".$get_kode['m7']."'");
            $this->m_crud->update_data("Master_Mutasi",array("status"=>"0"),"no_faktur_mutasi='".$get_kode['m8']."'");
        } else {
            $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "d13 = 'add' AND (d11 = '".$this->user."')");
            $count_box = $this->m_crud->count_data("master_packing", "kd_packing", "no_faktur_mutasi='".$no_faktur_mutasi."'");
            $get_max_kd_packing = $this->m_crud->get_data("master_packing", "MAX(CONVERT(INTEGER, RIGHT(kd_packing,4))) kd_packing", "left(kd_packing, len(kd_packing) - 4)='".$barcode."'");
            $new_kd_packing = $barcode.sprintf('%04d', $get_max_kd_packing['kd_packing']+1);
        }

        $data_packing = array(
            'kd_packing' => $new_kd_packing,
            'no_faktur_mutasi' => $no_faktur_mutasi,
            'tgl_packing' => $tgl_packing . " " . date("H:i:s"),
            'pengirim' => $pengirim,
            'operator' => $this->user,
            'status' => '0',
            'box' => $count_box+1
        );
        $this->m_crud->create_data("master_packing", $data_packing);

        $det_log = array();
        foreach ($read_temp_d as $row) {
            $data_detail_packing = array(
                'kd_packing' => $new_kd_packing,
                'kd_brg' => $row['d3'],
                'qty' => $row['d10'],
                'status' => '0'
            );
            $this->m_crud->create_data("det_packing", $data_detail_packing);
            array_push($det_log, $data_detail_packing);
			/*
            $data_kartu_stok_out = array(
                'kd_trx' => $new_kd_packing,
                'tgl' => $tgl_packing . " " . date("H:i:s"),
                'kd_brg' => $row['d3'],
                'saldo_awal' => 0,
                'stock_in' => 0,
                'stock_out' => $row['d10'],
                'lokasi' => $get_lokasi['kd_lokasi_1'],
                'keterangan' => 'Mutasi Ke '.$get_lokasi['kd_lokasi_2'],
                'hrg_beli' => $row['d7']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);

            $data_kartu_stok_in = array(
                'kd_trx' => $new_kd_packing,
                'tgl' => $tgl_packing . " " . date("H:i:s"),
                'kd_brg' => $row['d3'],
                'saldo_awal' => 0,
                'stock_in' => $row['d10'],
                'stock_out' => 0,
                'lokasi' => 'MUTASI',
                'keterangan' => 'Mutasi Dari '.$get_lokasi['kd_lokasi_1'],
                'hrg_beli' => $row['d7']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_in);
			*/

            $check_stock = $this->m_crud->get_data("Det_Mutasi dm","SUM(dm.qty) qty1, isnull((SELECT SUM(qty) FROM master_packing mp, det_packing dp WHERE mp.kd_packing=dp.kd_packing AND mp.no_faktur_mutasi=dm.no_faktur_mutasi),0) qty2","dm.no_faktur_mutasi='".$no_faktur_mutasi."'",null,"dm.no_faktur_mutasi");

            if ($check_stock['qty1']==$check_stock['qty2']) {
                $this->m_crud->update_data("Master_Mutasi",array("status"=>"1"),"no_faktur_mutasi='".$no_faktur_mutasi."'");
            } else {
                $this->m_crud->update_data("Master_Mutasi",array("status"=>"0"),"no_faktur_mutasi='".$no_faktur_mutasi."'");
            }

            /*$this->m_crud->update_data("barang", array('hrg_beli' => $row['d5']), "(kd_brg = '".$row['d2']."')");

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

        if ($param == 'edit') {
            $data_packing['trx_old'] = $get_kode['m7'];
        }

        $this->m_website->insert_log(array('admin'=>$this->user,'kd_trx'=>$new_kd_packing,'jenis'=>ucfirst($param),'transaksi'=>ucfirst('Packing')), array('master'=>$data_packing,'detail'=>$det_log));

        $this->delete_trans_packing(base64_encode($param));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status' => 0));
        }else {
            $this->db->trans_commit();
            echo json_encode(array('status' => 1, 'kd_packing' => $new_kd_packing, 'item' => count($read_temp_d), 'lokasi_asal' => $get_lokasi['kd_lokasi_1'], 'lokasi_tujuan' => $get_lokasi['kd_lokasi_2']));
        }
    }

    /*public function trans_packing() {
        $this->access_denied(33);

        $this->db->trans_begin();

        $get_temp_m = $this->m_crud->get_data("tr_temp_m", "*", "(m5 = '".$this->user."')");
        $read_temp_d = $this->m_crud->read_data("tr_temp_d", "*", "(d11 = '".$this->user."')");
        $get_lokasi = $this->m_crud->get_data("Master_Mutasi", "kd_lokasi_1, kd_lokasi_2, (SELECT serial FROM Lokasi WHERE Kode=kd_lokasi_1) serial1, (SELECT serial FROM Lokasi WHERE Kode=kd_lokasi_2) serial2", "no_faktur_mutasi='".$get_temp_m['m3']."'");
        $barcode = $get_lokasi['serial1'].date('ymd').$get_lokasi['serial2'];
        $get_max_kd_packing = $this->m_crud->get_data("master_packing", "MAX(CONVERT(INTEGER, RIGHT(kd_packing,4))) kd_packing", "LEFT(kd_packing, 8)='".$barcode."'");
        $count_box = $this->m_crud->count_data("master_packing", "kd_packing", "no_faktur_mutasi='".$get_temp_m['m3']."'");

        $new_kd_packing = $barcode.sprintf('%04d', $get_max_kd_packing['kd_packing']+1);

        $data_packing = array(
            'kd_packing' => $new_kd_packing,
            'no_faktur_mutasi' => $get_temp_m['m3'],
            'tgl_packing' => $get_temp_m['m2'] . " " . date("H:i:s"),
            'pengirim' => $get_temp_m['m4'],
            'operator' => $get_temp_m['m5'],
            'status' => '0',
            'box' => $count_box+1
        );
        $this->m_crud->create_data("master_packing", $data_packing);

        foreach ($read_temp_d as $row) {
            $data_detail_packing = array(
                'kd_packing' => $new_kd_packing,
                'kd_brg' => $row['d3'],
                'qty' => $row['d10'],
                'status' => '0'
            );
            $this->m_crud->create_data("det_packing", $data_detail_packing);

            $data_kartu_stok_out = array(
                'kd_trx' => $new_kd_packing,
                'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kd_brg' => $row['d3'],
                'saldo_awal' => 0,
                'stock_in' => 0,
                'stock_out' => $row['d10'],
                'lokasi' => $get_lokasi['kd_lokasi_1'],
                'keterangan' => 'Mutasi Ke '.$get_lokasi['kd_lokasi_2'],
                'hrg_beli' => $row['d7']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_out);

            $data_kartu_stok_in = array(
                'kd_trx' => $new_kd_packing,
                'tgl' => $get_temp_m['m2'] . " " . date("H:i:s"),
                'kd_brg' => $row['d3'],
                'saldo_awal' => 0,
                'stock_in' => $row['d10'],
                'stock_out' => 0,
                'lokasi' => 'MUTASI',
                'keterangan' => 'Mutasi Dari '.$get_lokasi['kd_lokasi_1'],
                'hrg_beli' => $row['d7']
            );
            $this->m_crud->create_data("Kartu_stock", $data_kartu_stok_in);

            $check_stock = $this->m_crud->get_data("Det_Mutasi dm","SUM(dm.qty) qty1, isnull((SELECT SUM(qty) FROM master_packing mp, det_packing dp WHERE mp.kd_packing=dp.kd_packing AND mp.no_faktur_mutasi=dm.no_faktur_mutasi),0) qty2","dm.no_faktur_mutasi='".$get_temp_m['m3']."'",null,"dm.no_faktur_mutasi");

            if ($check_stock['qty1']==$check_stock['qty2']) {
                $this->m_crud->update_data("Master_Mutasi",array("status"=>"1"),"no_faktur_mutasi='".$get_temp_m['m3']."'");
            }

            $this->delete_trans_packing();
            /*$this->m_crud->update_data("barang", array('hrg_beli' => $row['d5']), "(kd_brg = '".$row['d2']."')");

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

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status' => 0));
        }else {
            $this->db->trans_commit();
            echo json_encode(array('status' => 1, 'kd_packing' => $new_kd_packing, 'item' => count($read_temp_d), 'lokasi_asal' => $get_lokasi['kd_lokasi_1'], 'lokasi_tujuan' => $get_lokasi['kd_lokasi_2']));
        }
    }*/
	/*End modul packing*/

	/*Start modul report*/
	public function delivery_note_report($action = null, $id = null){
        $this->access_denied(121);
        $data = $this->data;
        $function = 'delivery_note_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Delivery Note';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "LEFT(mm.no_delivery_note, 2)='DN' AND mm.no_delivery_note=dm.no_delivery_note";
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'status' => $_POST['status']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $status = $this->session->search['status'];
        $explode_date =  explode(' - ',$date);
        if (isset($date) && $date != null) {
            $tgl_awal = str_replace('/','-',$explode_date[0]);
            $tgl_akhir = str_replace('/','-',$explode_date[1]);
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, tanggal, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
        } else {
            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, tanggal, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($status)&&$status!=null){ ($where==null)?null:$where.=" and "; $where.="(mm.status = '".$status."')"; }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="kd_lokasi_2 = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mm.no_delivery_note like '%".$search."%' or mm.no_faktur_beli like '%".$search."%' or dm.kd_brg like '%".$search."%' or kd_lokasi_1 like '%".$search."%' or kd_lokasi_2 like '%".$search."%' or kd_kasir like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_over("master_delivery_note mm, det_delivery_note dm", 'mm.no_delivery_note', ($where==null?'':$where), null, 'mm.no_delivery_note');
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
        $data['report'] = $this->m_crud->select_limit('master_delivery_note mm, det_delivery_note dm', "tanggal, mm.no_delivery_note, kd_lokasi_1, kd_lokasi_2, kd_kasir, mm.status, isnull(mm.no_faktur_beli, '-') no_faktur_beli, keterangan", ($where==null?'':$where), 'mm.no_delivery_note desc', "tanggal, mm.no_delivery_note, kd_lokasi_1, kd_lokasi_2, kd_kasir, mm.status, mm.no_faktur_beli, keterangan", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $data['det_report'] = $this->m_crud->read_data('master_delivery_note mm, det_delivery_note dm, barang br', "mm.tanggal, mm.no_delivery_note, isnull(mm.no_faktur_beli, '-') no_faktur_beli, mm.kd_lokasi_1, mm.kd_lokasi_2, mm.kd_kasir, mm.status, mm.keterangan, br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual, (SELECT COUNT(no_delivery_note) FROM det_delivery_note WHERE det_delivery_note.no_delivery_note=mm.no_delivery_note) baris", "dm.kd_brg=br.kd_brg".($where==null?' ' : ' AND '.$where), 'mm.tanggal desc', "mm.tanggal, mm.no_delivery_note, mm.kd_lokasi_1, mm.kd_lokasi_2, mm.kd_kasir, mm.status, mm.no_faktur_beli, mm.keterangan, br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual");
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
                    'A'=>'Tanggal', 'B'=>'No. Delivery Note', 'C'=>'Jenis', 'D'=>'Nota Pembelian', 'E'=>'Lokasi Asal', 'F'=>'Lokasi Tujuan', 'G'=>'Operator', 'H'=>'Status', 'I'=>'Kode Barang', 'J'=>'Barcode', 'K'=>'Nama Barang', 'L'=>'Qty', 'M'=>'Harga Jual'
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
                    $value['tanggal'], $value['no_delivery_note'], '-', $value['no_faktur_beli'], $value['kd_lokasi_1'], $value['kd_lokasi_2'], $value['kd_kasir'], ($value['status']==0?'Belum Alokasi':'Sudah Alokasi'), $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['qty'], $value['hrg_jual']
                );
            }

            $header['alignment']['A6:H'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['I6:K'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']).'.xls', $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('master_delivery_note', "tanggal, no_delivery_note, keterangan, ISNULL(no_faktur_beli, '-') no_faktur_beli, kd_lokasi_1, kd_lokasi_2, kd_kasir", "no_delivery_note = '".$id."'");
            $data['report_detail'] = $this->m_crud->join_data('det_delivery_note as dm', 'br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual', 'barang as br', 'br.kd_brg = dm.kd_brg', "no_delivery_note = '".$data['report']['no_delivery_note']."'");
            $data['keterangan'] = $data['report']['keterangan'];
			
            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_delivery_note']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header =
                '<div class="row"><img style="float: right; margin-top: -10px" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['no_delivery_note'].'"></div>'.
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Delivery Note</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table cellpadding="1" width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="18%"></th>
                                <th width="2%"></th>
                                <th width="30%"></th>
                                
                                <th width="7%"></th>
                                <th width="15%"></th>
                                <th width="2%"></th>
                                <th width="25%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>Tanggal</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tanggal'], 0, 10).'</td>
                                <td></td>
                                <td><b>Operator</b></td>
                                <td><b>:</b></td>
                                <td>'.$this->m_website->get_nama_user($data['report']['kd_kasir']).'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>No. Delivery Note</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['no_delivery_note'].'</td>
                                <td></td>
                                <td><b></b></td>
                                <td><b></b></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi Asal</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_lokasi_1'].'</td>
                                <td></td>
                                <td><b>Kode Pembelian</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['no_faktur_beli'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi Tujuan</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_lokasi_2'].'</td>
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
                'mode'=>'utf-8','paper'=>array(210,297),'font_size'=>10,'font_family'=>'sans-serif',
                'left'=>10,'right'=>10,'top'=>52,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }
	public function alokasi_report($action = null, $id = null){
		$this->access_denied(111);
		$data = $this->data;
		$function = 'alokasi_report';
		$view = $this->control . '/';
		if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
		$table = null;
		//if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
		$data['title'] = 'Laporan Alokasi';
		$data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
		$data['content'] = $view.$function;
		$data['table'] = $table;
		
		$where = "LEFT(mm.no_faktur_mutasi, 2)='MU' and lk.kode=mm.kd_lokasi_2 AND mm.no_faktur_mutasi=dm.no_faktur_mutasi";
		if(isset($_POST['search'])||isset($_POST['to_excel'])){
			$this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'status' => $_POST['status']));
		}
		
		$search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $status = $this->session->search['status'];
		$explode_date =  explode(' - ',$date);
		if (isset($date) && $date != null) {
			$tgl_awal = str_replace('/','-',$explode_date[0]);
			$tgl_akhir = str_replace('/','-',$explode_date[1]);
			($where == null) ? null : $where .= " and ";
			$where .= "LEFT(CONVERT(VARCHAR, tgl_mutasi, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
		} else {
			$tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
			($where == null) ? null : $where .= " and ";
			$where .= "LEFT(CONVERT(VARCHAR, tgl_mutasi, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
		}
		if(isset($status)&&$status!=null){ ($where==null)?null:$where.=" and "; $where.="(mm.status = '".$status."')"; }
		if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="kd_lokasi_2 = '".$lokasi."'"; }
		if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mm.no_faktur_mutasi like '%".$search."%' or mm.no_faktur_beli like '%".$search."%' or dm.kd_brg like '%".$search."%' or kd_lokasi_1 like '%".$search."%' or kd_lokasi_2 like '%".$search."%' or kd_kasir like '%".$search."%')"; }
		
		$page = ($id==null?1:$id);
		$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
		$config['total_rows'] = $this->m_crud->count_data_over("Master_Mutasi mm, Det_Mutasi dm, lokasi lk", 'mm.no_faktur_mutasi', ($where==null?'':$where), null, 'mm.no_faktur_mutasi');
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
		$data['report'] = $this->m_crud->select_limit(
			'Master_Mutasi mm, Det_Mutasi dm, lokasi lk',
			"lk.nama, tgl_mutasi, mm.no_faktur_mutasi, kd_lokasi_1, kd_lokasi_2, kd_kasir, mm.status, isnull(mm.no_faktur_beli, '-') no_faktur_beli, keterangan",
			($where==null?'':$where),
			'mm.no_faktur_mutasi desc',
			"lk.nama, tgl_mutasi, mm.no_faktur_mutasi, kd_lokasi_1, kd_lokasi_2, kd_kasir, mm.status, mm.no_faktur_beli, keterangan",
			($page-1)*$config['per_page']+1, ($config['per_page']*$page));
		
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
		
		if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
			isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
			$data['content'] = $view.'pdf_invoice_'.$function;
			$data['report'] = $this->m_crud->get_data('Master_Mutasi', "tgl_mutasi, no_faktur_mutasi, keterangan, ISNULL(no_faktur_beli, '-') no_faktur_beli, kd_lokasi_1, kd_lokasi_2, kd_kasir", "no_faktur_mutasi = '".$id."'");
			$data['report_detail'] = $this->m_crud->join_data('Det_Mutasi as dm', 'br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual', 'barang as br', 'br.kd_brg = dm.kd_brg', "no_faktur_mutasi = '".$data['report']['no_faktur_mutasi']."'");
			$data['keterangan'] = $data['report']['keterangan'];
			
			$t_row = count($data['report_detail']);
			//$t_row = $t_row + 23;
			$data['row_per_page'] = 30;
			$data['row_one_page'] = 25;
			($action=='download')?($method='D'):($method='I');
			//$method='I';
			$file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
			$html = $this->load->view('bo/'.$data['content'], $data, true);
			
			$header =
				'<div class="row"><img style="float: right" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['no_faktur_mutasi'].'"></div>'.
				$this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Alokasi Barang</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
				'<div style="margin-bottom: 10px;">
                    <table cellpadding="1" width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="18%"></th>
                                <th width="2%"></th>
                                <th width="30%"></th>
                                
                                <th width="7%"></th>
                                <th width="15%"></th>
                                <th width="2%"></th>
                                <th width="25%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>Tanggal</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_mutasi'], 0, 10).'</td>
                                <td></td>
                                <td><b>Operator</b></td>
                                <td><b>:</b></td>
                                <td>'.$this->m_website->get_nama_user($data['report']['kd_kasir']).'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>No. Alokasi</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['no_faktur_mutasi'].'</td>
                                <td></td>
                                <td><b>Jenis Trans</b></td>
                                <td><b>:</b></td>
                                <td>'.(substr($data['report']['no_faktur_mutasi'], 0, 2)=='MU'?'Mutasi':'Branch').'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi Asal</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_lokasi_1'].'</td>
                                <td></td>
                                <td><b>Delivery Note</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['no_faktur_beli'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi Tujuan</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_lokasi_2'].'</td>
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
				'mode'=>'utf-8','paper'=>array(210,297),'font_size'=>10,'font_family'=>'sans-serif',
				'left'=>10,'right'=>10,'top'=>52,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
			);
			
			$this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
			
		}
		
		if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
		else { $this->load->view('bo/index', $data); }
	}
	
//    public function alokasi_report($action = null, $id = null){
//        $this->access_denied(111);
//        $data = $this->data;
//        $function = 'alokasi_report';
//        $view = $this->control . '/';
//        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
//        $table = null;
//        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
//        $data['title'] = 'Laporan Alokasi';
//        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
//        $data['content'] = $view.$function;
//        $data['table'] = $table;
//
//        $where = "LEFT(mm.no_faktur_mutasi, 2)='MU' AND mm.no_faktur_mutasi=dm.no_faktur_mutasi";
//        if(isset($_POST['search'])||isset($_POST['to_excel'])){
//            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'status' => $_POST['status']));
//        }
//
//        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $status = $this->session->search['status'];
//        $explode_date =  explode(' - ',$date);
//        if (isset($date) && $date != null) {
//            $tgl_awal = str_replace('/','-',$explode_date[0]);
//            $tgl_akhir = str_replace('/','-',$explode_date[1]);
//            ($where == null) ? null : $where .= " and ";
//            $where .= "LEFT(CONVERT(VARCHAR, tgl_mutasi, 120), 10) BETWEEN '" . $tgl_awal . "' AND '" . $tgl_akhir . "'";
//        } else {
//            $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
//            ($where == null) ? null : $where .= " and ";
//            $where .= "LEFT(CONVERT(VARCHAR, tgl_mutasi, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
//        }
//        if(isset($status)&&$status!=null){ ($where==null)?null:$where.=" and "; $where.="(mm.status = '".$status."')"; }
//        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="kd_lokasi_2 = '".$lokasi."'"; }
//        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mm.no_faktur_mutasi like '%".$search."%' or mm.no_faktur_beli like '%".$search."%' or dm.kd_brg like '%".$search."%' or kd_lokasi_1 like '%".$search."%' or kd_lokasi_2 like '%".$search."%' or kd_kasir like '%".$search."%')"; }
//
//        $page = ($id==null?1:$id);
//        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
//        $config['total_rows'] = $this->m_crud->count_data_over("Master_Mutasi mm, Det_Mutasi dm", 'mm.no_faktur_mutasi', ($where==null?'':$where), null, 'mm.no_faktur_mutasi');
//        $config['per_page'] = 30;
//        //$config['attributes'] = array('class' => ''); //attributes anchors
//        $config['first_url'] = $config['base_url'];
//        $config['num_links'] = 5;
//        $config['use_page_numbers'] = TRUE;
//        //$config['display_pages'] = FALSE;
//        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
//        $config['first_tag_open'] = '<li>'; $config['first_link'] = '&laquo;'; $config['first_tag_close'] = '</li>';
//        $config['prev_tag_open'] = '<li>'; $config['prev_link'] = '&lt;'; $config['prev_tag_close'] = '</li>';
//        $config['cur_tag_open'] = '<li class="active"><a href="#"> '; $config['cur_tag_close'] = '</a></li>';
//        $config['num_tag_open'] = '<li>'; $config['num_tag_close'] = '</li>';
//        $config['next_tag_open'] = '<li>'; $config['next_link'] = '&gt;';  $config['next_tag_close'] = '</li>';
//        $config['last_tag_open'] = '<li>'; $config['last_link'] = '&raquo;'; $config['last_tag_close'] = '</li>';
//        $config['full_tag_close'] = '</ul>';
//        $this->pagination->initialize($config);
//        $data['report'] = $this->m_crud->select_limit('Master_Mutasi mm, Det_Mutasi dm', "tgl_mutasi, mm.no_faktur_mutasi, kd_lokasi_1, kd_lokasi_2, kd_kasir, mm.status, isnull(mm.no_faktur_beli, '-') no_faktur_beli, keterangan", ($where==null?'':$where), 'mm.no_faktur_mutasi desc', "tgl_mutasi, mm.no_faktur_mutasi, kd_lokasi_1, kd_lokasi_2, kd_kasir, mm.status, mm.no_faktur_beli, keterangan", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
//
//        if(isset($_POST['to_excel'])){
//            $data['det_report'] = $this->m_crud->read_data('Master_Mutasi mm, Det_Mutasi dm, barang br', "mm.tgl_mutasi, mm.no_faktur_mutasi, isnull(mm.no_faktur_beli, '-') no_faktur_beli, mm.kd_lokasi_1, mm.kd_lokasi_2, mm.kd_kasir, mm.status, mm.keterangan, br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual, (SELECT COUNT(no_faktur_mutasi) FROM Det_Mutasi WHERE Det_Mutasi.no_faktur_mutasi=mm.no_faktur_mutasi) baris", "dm.kd_brg=br.kd_brg".($where==null?' ' : ' AND '.$where), 'mm.tgl_mutasi desc', "mm.tgl_mutasi, mm.no_faktur_mutasi, mm.kd_lokasi_1, mm.kd_lokasi_2, mm.kd_kasir, mm.status, mm.no_faktur_beli, mm.keterangan, br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual");
//            $baca = $data['det_report'];
//            $header = array(
//                'merge' 	=> array('A1:M1','A2:M2','A3:M3'),
//                'auto_size' => true,
//                'font' 		=> array(
//                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
//                    'A3' => array('bold'=>true,'name'=>'Verdana'),
//                    'A5:M5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
//                ),
//                'alignment' => array(
//                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
//                    'A5:M5' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
//                ),
//                '1' => array('A' => $data['site']->title),
//                '2' => array('A' => $data['title']),
//                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
//                '5' => array(
//                    'A'=>'Tanggal', 'B'=>'No. Alokasi', 'C'=>'Jenis', 'D'=>'Nota Delivery Note', 'E'=>'Lokasi Asal', 'F'=>'Lokasi Tujuan', 'G'=>'Operator', 'H'=>'Status', 'I'=>'Kode Barang', 'J'=>'Barcode', 'K'=>'Nama Barang', 'L'=>'Qty', 'M'=>'Harga Jual'
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
//                    array_push($header['merge'], 'A'.$start.':A'.$end.'', 'B'.$start.':B'.$end.'', 'C'.$start.':C'.$end.'', 'D'.$start.':D'.$end.'', 'E'.$start.':E'.$end.'', 'F'.$start.':F'.$end.'', 'G'.$start.':G'.$end.'', 'H'.$start.':H'.$end.'');
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
//                    $value['tgl_mutasi'], $value['no_faktur_mutasi'], (substr($value['no_faktur_mutasi'], 0, 2)=='MU'?'Mutasi':'Branch'), $value['no_faktur_beli'], $value['kd_lokasi_1'], $value['kd_lokasi_2'], $value['kd_kasir'], ($value['status']==0?'Approval':'Approved'), $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['qty'], $value['hrg_jual']
//                );
//            }
//
//            $header['alignment']['A6:H'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//            $header['alignment']['I6:K'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//
//            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
//        }
//
//        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
//            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
//            $data['content'] = $view.'pdf_invoice_'.$function;
//            $data['report'] = $this->m_crud->get_data('Master_Mutasi', "tgl_mutasi, no_faktur_mutasi, keterangan, ISNULL(no_faktur_beli, '-') no_faktur_beli, kd_lokasi_1, kd_lokasi_2, kd_kasir", "no_faktur_mutasi = '".$id."'");
//            $data['report_detail'] = $this->m_crud->join_data('Det_Mutasi as dm', 'br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual', 'barang as br', 'br.kd_brg = dm.kd_brg', "no_faktur_mutasi = '".$data['report']['no_faktur_mutasi']."'");
//            $data['keterangan'] = $data['report']['keterangan'];
//
//            $t_row = count($data['report_detail']);
//            //$t_row = $t_row + 23;
//            $data['row_per_page'] = 30;
//            $data['row_one_page'] = 25;
//            ($action=='download')?($method='D'):($method='I');
//            //$method='I';
//            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
//            $html = $this->load->view('bo/'.$data['content'], $data, true);
//
//            $header =
//                '<div class="row"><img style="float: right" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['no_faktur_mutasi'].'"></div>'.
//                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Alokasi Barang</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
//                '<div style="margin-bottom: 10px;">
//                    <table cellpadding="1" width="100%">
//                        <thead>
//                            <tr>
//                                <th width="2%"></th>
//                                <th width="18%"></th>
//                                <th width="2%"></th>
//                                <th width="30%"></th>
//
//                                <th width="7%"></th>
//                                <th width="15%"></th>
//                                <th width="2%"></th>
//                                <th width="25%"></th>
//                            </tr>
//                        </thead>
//                        <tbody>
//                            <tr>
//                                <td></td>
//                                <td><b>Tanggal</b></td>
//                                <td><b>:</b></td>
//                                <td>'.substr($data['report']['tgl_mutasi'], 0, 10).'</td>
//                                <td></td>
//                                <td><b>Operator</b></td>
//                                <td><b>:</b></td>
//                                <td>'.$this->m_website->get_nama_user($data['report']['kd_kasir']).'</td>
//                            </tr>
//                            <tr>
//                                <td></td>
//                                <td><b>No. Alokasi</b></td>
//                                <td><b>:</b></td>
//                                <td>'.$data['report']['no_faktur_mutasi'].'</td>
//                                <td></td>
//                                <td><b>Jenis Trans</b></td>
//                                <td><b>:</b></td>
//                                <td>'.(substr($data['report']['no_faktur_mutasi'], 0, 2)=='MU'?'Mutasi':'Branch').'</td>
//                            </tr>
//                            <tr>
//                                <td></td>
//                                <td><b>Lokasi Asal</b></td>
//                                <td><b>:</b></td>
//                                <td>'.$data['report']['kd_lokasi_1'].'</td>
//                                <td></td>
//                                <td><b>Delivery Note</b></td>
//                                <td><b>:</b></td>
//                                <td>'.$data['report']['no_faktur_beli'].'</td>
//                            </tr>
//                            <tr>
//                                <td></td>
//                                <td><b>Lokasi Tujuan</b></td>
//                                <td><b>:</b></td>
//                                <td>'.$data['report']['kd_lokasi_2'].'</td>
//                                <td></td>
//                                <td></td>
//                                <td></td>
//                                <td></td>
//                            </tr>
//                        </tbody>
//                    </table>
//                </div>';
//            $footer = null;
//            $conf = array( //'paper'=>array(200,100)
//                'mode'=>'utf-8','paper'=>array(210,297),'font_size'=>10,'font_family'=>'sans-serif',
//                'left'=>10,'right'=>10,'top'=>52,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
//            );
//
//            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
//
//        }
//
//        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
//        else { $this->load->view('bo/index', $data); }
//    }

    public function alokasi_by_cabang_report($action = null, $id = null){
        $this->access_denied(116);
        $data = $this->data;
        $function = 'alokasi_by_cabang_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Branch Alokasi';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "LEFT(mm.no_faktur_mutasi, 2)='MC' AND mm.no_faktur_mutasi=dm.no_faktur_mutasi";
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'status' => $_POST['status']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $status = $this->session->search['status'];
        $explode_date =  explode(' - ',$date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mm.tgl_mutasi, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mm.tgl_mutasi, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($status)&&$status!=null){ ($where==null)?null:$where.=" and "; $where.="(mm.status = '".$status."')"; }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="kd_lokasi_2 = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mm.no_faktur_mutasi like '%".$search."%' or dm.kd_brg like '%".$search."%' or kd_lokasi_1 like '%".$search."%' or kd_lokasi_2 like '%".$search."%' or kd_kasir like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_over("Master_Mutasi mm, Det_Mutasi dm", 'mm.no_faktur_mutasi', ($where==null?'':$where), null, 'mm.no_faktur_mutasi');
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
        $data['report'] = $this->m_crud->select_limit('Master_Mutasi mm, Det_Mutasi dm', "mm.tgl_mutasi, mm.no_faktur_mutasi, kd_lokasi_1, kd_lokasi_2, kd_kasir, mm.status", ($where==null?'':$where), 'mm.tgl_mutasi desc', "mm.tgl_mutasi, mm.no_faktur_mutasi, kd_lokasi_1, kd_lokasi_2, kd_kasir, mm.status", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $data['det_report'] = $this->m_crud->read_data('Master_Mutasi mm, Det_Mutasi dm, barang br', "mm.tgl_mutasi, mm.no_faktur_mutasi, mm.kd_lokasi_1, mm.kd_lokasi_2, mm.kd_kasir, mm.status, br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual, (SELECT COUNT(no_faktur_mutasi) FROM Det_Mutasi WHERE Det_Mutasi.no_faktur_mutasi=mm.no_faktur_mutasi) baris", "dm.kd_brg=br.kd_brg".($where==null?' ' : ' AND '.$where), 'mm.tgl_mutasi desc', "mm.tgl_mutasi, mm.no_faktur_mutasi, mm.kd_lokasi_1, mm.kd_lokasi_2, mm.kd_kasir, mm.status, br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual");
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
                    'A'=>'Tanggal', 'B'=>'No. Alokasi', 'C'=>'Lokasi Asal', 'D'=>'Lokasi Tujuan', 'E'=>'Operator', 'F'=>'Status', 'G'=>'Kode Barang', 'H'=>'Barcode', 'I'=>'Nama Barang', 'J'=>'Qty', 'K'=>'Harga Jual'
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
                    $value['tgl_mutasi'], $value['no_faktur_mutasi'], $value['kd_lokasi_1'], $value['kd_lokasi_2'], $value['kd_kasir'], ($value['status']==0?'Approval':'Approved'), $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['qty'], $value['hrg_jual']
                );
            }

            $header['alignment']['A6:F'.$end.''] = array('vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $header['alignment']['G6:I'.$end.''] = array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('Master_Mutasi', "tgl_mutasi, no_faktur_mutasi, kd_lokasi_1, kd_lokasi_2, kd_kasir", "no_faktur_mutasi = '".$id."'");
            $data['report_detail'] = $this->m_crud->join_data('Det_Mutasi as dm', 'br.kd_brg, br.barcode, br.nm_brg, dm.qty, dm.hrg_jual', 'barang as br', 'br.kd_brg = dm.kd_brg', "no_faktur_mutasi = '".$data['report']['no_faktur_mutasi']."'");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header =
                '<div class="row"><img style="float: right; margin-top: -10px" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['no_faktur_mutasi'].'"></div>'.
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Alokasi By Cabang</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table cellpadding="1" width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="18%"></th>
                                <th width="2%"></th>
                                <th width="30%"></th>
                                
                                <th width="10%"></th>
                                <th width="12%"></th>
                                <th width="2%"></th>
                                <th width="25%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>Tanggal</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_mutasi'], 0, 10).'</td>
                                <td></td>
                                <td><b>Operator</b></td>
                                <td><b>:</b></td>
                                <td>'.$this->m_website->get_nama_user($data['report']['kd_kasir']).'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>No. Alokasi</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['no_faktur_mutasi'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi Asal</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_lokasi_1'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi Tujuan</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_lokasi_2'].'</td>
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
                'mode'=>'utf-8','paper'=>array(210,297),'font_size'=>10,'font_family'=>'sans-serif',
                'left'=>10,'right'=>10,'top'=>52,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function alokasi_by_pembelian_report($action = null, $id = null){
        $this->access_denied(113);
        $data = $this->data;
        $function = 'alokasi_by_pembelian_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Alokasi By Pembelian';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "mb.no_faktur_beli=db.no_faktur_beli";
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
            $where .= "LEFT(CONVERT(VARCHAR, mb.tgl_beli, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mb.tgl_beli, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="lokasi = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mb.no_faktur_beli like '%".$search."%' or db.kode_barang like '%".$search."%' or noNota like '%".$search."%' or lokasi like '%".$search."%' or operator like '%".$search."%')"; }

        $page = ($id==null?1:$id);

        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_over('master_beli mb, det_beli db', "mb.no_faktur_beli", $where, null, "mb.no_faktur_beli");
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

        $data['report'] = $this->m_crud->select_limit('master_beli mb, det_beli db', "mb.tgl_beli, mb.no_faktur_beli, noNota, Lokasi, Operator", $where, 'mb.no_faktur_beli desc', "mb.tgl_beli, mb.no_faktur_beli, noNota, Lokasi, Operator", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $detail = $this->m_crud->read_data('master_beli mb, det_beli db', "mb.tgl_beli, mb.no_faktur_beli, noNota, Lokasi, Operator", $where, 'mb.no_faktur_beli desc', "mb.tgl_beli, mb.no_faktur_beli, noNota, Lokasi, Operator");
            $baca = $detail;
            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['tgl_beli'], $value['master_beli mb, det_beli db'], $value['noNota'], $value['Lokasi'], $value['Operator']
                );
            }
            $header = array(
                'merge' 	=> array('A1:E1','A2:E2','A3:E3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:E5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'No. Alokasi', 'C'=>'No Nota', 'D'=>'Lokasi', 'E'=>'Operator'
                )
            );
            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('master_beli', "tgl_beli, no_faktur_beli, noNota, Lokasi, Operator", "no_faktur_beli = '".$id."'");
            $data['report_detail'] = $this->m_crud->join_data('det_beli as db', 'kode_barang, barcode, nm_brg, Deskripsi, jumlah_beli, hrg_jual_1, isnull(db.jumlah_bonus, 0) jumlah_bonus, isnull(dr.jml,0) jumlah_retur', array(array('table'=>'barang as br', 'type'=>'LEFT'), array('table'=>'Master_Retur_Beli mr', 'type'=>'LEFT'), array('table'=>'Det_Retur_Beli dr', 'type'=>'LEFT')), array('kode_barang = kd_brg','db.no_faktur_beli=mr.no_beli','dr.No_Retur=mr.No_Retur AND dr.kd_brg=db.kode_barang'), "no_faktur_beli = '".$id."'");

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
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Alokasi Barang</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
						<table width="100%">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="15%"></th>
									<th width="2%"></th>
									<th width="25%"></th>
									
									<th width="10%"></th>
									<th width="15%"></th>
									<th width="2%"></th>
									<th width="29%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td><b>Tanggal</b></td>
									<td><b>:</b></td>
									<td>'.substr($data['report']['tgl_beli'], 0, 10).'</td>
									<td></td>
									<td><b>Operator</b></td>
									<td><b>:</b></td>
									<td>'.$this->m_website->get_nama_user($data['report']['Operator']).'</td>
								</tr>
								<tr>
									<td></td>
									<td><b>No. Alokasi</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['no_faktur_beli'].'</td>
									<td></td>
									<td><b>Nota Supp.</b></td>
									<td><b>:</b></td>
									<td>'.$data['report']['noNota'].'</td>
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
                'left'=>10,'right'=>10,'top'=>47,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

	public function stock_report($action = null, $id = null) {
        $this->access_denied(112);
        $data = $this->data;
        $function = 'stock_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
		
		ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '2048M');
		
		ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
		ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
		
        if (isset($_POST['to_excel'])) {
            //ini_set('max_execution_time', 3600);
            //ini_set('memory_limit', '1024M');
            //$this->form_validation->set_rules('lokasi', 'Lokasi', 'trim|required', array('required' => '%s don`t empty'));
        }

        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Stock';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $where2 = null;
        $group = null;
        $filter_table = null;
        $tgl_akhir = date('Y-m-d');
        $tgl_awal = date('Y-m-d');

        $where_stock = "kd_brg = br.kd_brg";

        if(isset($_POST['search']) || isset($_POST['to_excel'])) {
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'filter' => $_POST['filter'], 'filter2' => $_POST['filter2'], 'order' => $_POST['order'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'supplier' => $_POST['supplier']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $supplier = $this->session->search['supplier']; $filter = $this->session->search['filter']; $filter2 = $this->session->search['filter2']; $order = $this->session->search['order'];$date = $this->session->search['field-date'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            $tgl_awal = $date1; $tgl_akhir = $date2;
        }

        if(isset($supplier) && $supplier != null){ ($where==null)?null:$where.=" AND "; $where.="br.Group1='".$supplier."'";}
        if(isset($lokasi) && $lokasi != null){
            //$having = "(select count(kd_brg) from kartu_stock where lokasi = '".$lokasi."' and kartu_stock.kd_brg=br.kd_brg) > 0";
            $having = null;
            $get_lokasi = $this->m_crud->get_data("Lokasi", "Nama", "Kode='".$lokasi."'");
            $where_stock .= " and kartu_stock.lokasi = '".$lokasi."' ";
            $data['where_lokasi'] = " and lokasi = '".$lokasi."' ";
            $data['lokasi'] = $lokasi;
            $data['nama_lokasi'] = $get_lokasi['Nama'];
        } else {
            $having = null;
            $where_stock .= " and kartu_stock.lokasi <> 'HO' and kartu_stock.lokasi <> '' and kartu_stock.lokasi is not null ";
            $data['where_lokasi'] = " and lokasi <> 'HO' and lokasi <> '' and lokasi is not null ";
            $data['nama_lokasi'] = "";
            $data['lokasi'] = "";
        }

        if ($filter == '>') {
            ($having!=null)?$having.=' and ':null;
            $having .= "isnull((select sum(stock_in - stock_out) from kartu_stock where kartu_stock.kd_brg=br.kd_brg and lokasi NOT IN ('MUTASI', 'Retur') AND ".$where_stock." and left(convert(varchar, tgl, 120), 10) <= '".$tgl_akhir."'),0) > 0";
        } else if ($filter == '<') {
            ($having!=null)?$having.=' and ':null;
            $having .= "isnull((select sum(stock_in - stock_out) from kartu_stock where kartu_stock.kd_brg=br.kd_brg and lokasi NOT IN ('MUTASI', 'Retur') AND ".$where_stock." and left(convert(varchar, tgl, 120), 10) <= '".$tgl_akhir."'),0) < 0";
        } else if ($filter == '=') {
            ($having!=null)?$having.=' and ':null;
            $having .= "isnull((select sum(stock_in - stock_out) from kartu_stock where kartu_stock.kd_brg=br.kd_brg and lokasi NOT IN ('MUTASI', 'Retur') AND ".$where_stock." and left(convert(varchar, tgl, 120), 10) <= '".$tgl_akhir."'),0) = 0";
        } else if ($filter == '<<') {
            ($having!=null)?$having.=' and ':null;
            $having .= "isnull((select sum(stock_in - stock_out) from kartu_stock where kartu_stock.kd_brg=br.kd_brg and lokasi NOT IN ('MUTASI', 'Retur') AND ".$where_stock." and left(convert(varchar, tgl, 120), 10) <= '".$tgl_akhir."'),0) < isnull(br.stock_min, 0)";
        }

        if(isset($search) && $search != null){
			($where==null)?null:$where.=" AND ";
			if(strlen($search) != 1){
				$where.=$filter2." like '%".$search."%'";
			} else {
				$where.=$filter2." like '".$search."%'";
			}
		}
        //if(isset($filter) && $filter != null){ ($where==null)?null:$where.=""; $where.=" and ks.kd_brg=br.kd_brg "; $where.=$where2; $group=" GROUP BY br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama HAVING SUM(ks.stock_in-ks.stock_out)".$filter."0"; $filter_table="Kartu_stock ks"; }

		$getFilter = ($filter2==null?"br.kd_brg":$filter2);
		$getOrder = ($order==null?"ASC":$order);
        $page = ($id==null?1:$id);

		$config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
		// $config['total_rows'] = $this->m_crud->count_data_join_over('barang br', "br.kd_brg", "Kartu_stock ks", "ks.kd_brg=br.kd_brg", ($where==null?'':$where), 'br.kd_brg ASC', "br.kd_brg, br.stock_min", 0, 0, $having);
        $config['total_rows'] = $this->m_crud->count_data_join_over('barang br', "br.kd_brg", array(array('table'=>'Group1 gr1', 'type'=>'LEFT'), array('table'=>'Kartu_stock ks', 'type'=>'LEFT')), array("br.Group1=gr1.Kode", "ks.kd_brg=br.kd_brg"), ($where==null?'':$where), null, "br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama, br.hrg_beli, br.hrg_jual_1, br.stock_min", null, null, $having);
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

        $stock_awal = "isnull((select sum(stock_in - stock_out) from kartu_stock where kartu_stock.kd_brg=br.kd_brg and lokasi NOT IN ('MUTASI', 'Retur') AND ".$where_stock." and tgl < '".$tgl_awal." 00:00:00'),0) as stock_awal";
        $stock_masuk = "isnull((select sum(stock_in) from kartu_stock where kartu_stock.kd_brg=br.kd_brg and lokasi NOT IN ('MUTASI', 'Retur') AND ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_masuk";
        $stock_keluar = "isnull((select sum(stock_out) from kartu_stock where kartu_stock.kd_brg=br.kd_brg and lokasi NOT IN ('MUTASI', 'Retur') AND ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_keluar";
		// select_limit_join($table, $column, $table_join, $on, $condition=null, $order, $group=null, $limit_start, $limit_end, $having=null)
		// $data['report'] = $this->m_crud->select_limit_join('barang br', "br.kd_brg, br.barcode, br.nm_brg, br.satuan, br.hrg_beli, br.hrg_jual_1, gr1.Nama, ".$stock_masuk.", ".$stock_keluar.", ".$stock_awal."", array(array('table'=>'Group1 gr1', 'type'=>'LEFT'), array('table'=>'Kartu_stock ks', 'type'=>'LEFT')), array("br.Group1=gr1.Kode", "ks.kd_brg=br.kd_brg"), ($where==null?'':$where), 'gr1.Nama ASC, br.kd_brg ASC', "br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama, br.hrg_beli, br.hrg_jual_1, br.stock_min", ($page-1)*$config['per_page']+1, ($config['per_page']*$page), $having);
		$data['report'] = $this->m_crud->select_limit_join(
			'barang br',
			"br.kd_brg, br.barcode, br.nm_brg, br.satuan, br.hrg_beli, br.hrg_jual_1, gr1.Nama, ".$stock_masuk.", ".$stock_keluar.", ".$stock_awal."",
			array(array('table'=>'Group1 gr1', 'type'=>'LEFT'), array('table'=>'Kartu_stock ks', 'type'=>'LEFT')),
			array("br.Group1=gr1.Kode", "ks.kd_brg=br.kd_brg"),
			($where==null?'':$where),
			"br.nm_brg $getOrder",
			"br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama, br.hrg_beli, br.hrg_jual_1, br.stock_min",
			($page-1)*$config['per_page']+1,
			($config['per_page']*$page),
			$having);
        
        $total = $this->m_crud->join_data('barang br', "br.hrg_beli, br.hrg_jual_1, ".$stock_masuk.", ".$stock_keluar.", ".$stock_awal."", array(array('table'=>'Group1 gr1', 'type'=>'LEFT')), array("br.Group1=gr1.Kode"), ($where==null?'':$where), null, "br.kd_brg, br.hrg_beli, br.hrg_jual_1, br.stock_min", 0, 0, $having);
		
        /*$get_total1 = $this->m_crud->select_limit('barang br, Group1 gr1, Kartu_stock ks', "br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama, ".$stock_masuk.", ".$stock_keluar.", ".$stock_awal."", "br.Group1=gr1.Kode AND lokasi NOT IN ('MUTASI', 'Retur') AND keterangan<>'Input Barang' AND ks.kd_brg=br.kd_brg ".($where==null?'':$where), 'br.kd_brg ASC', "br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama", 1, 20000, $having);
        $get_total2 = $this->m_crud->select_limit('barang br, Group1 gr1, Kartu_stock ks', "br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama, ".$stock_masuk.", ".$stock_keluar.", ".$stock_awal."", "br.Group1=gr1.Kode AND lokasi NOT IN ('MUTASI', 'Retur') AND keterangan<>'Input Barang' AND ks.kd_brg=br.kd_brg ".($where==null?'':$where), 'br.kd_brg ASC', "br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama", 20001, 40000, $having);
        $get_total3 = $this->m_crud->select_limit('barang br, Group1 gr1, Kartu_stock ks', "br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama, ".$stock_masuk.", ".$stock_keluar.", ".$stock_awal."", "br.Group1=gr1.Kode AND lokasi NOT IN ('MUTASI', 'Retur') AND keterangan<>'Input Barang' AND ks.kd_brg=br.kd_brg ".($where==null?'':$where), 'br.kd_brg ASC', "br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama", 40001, 60000, $having);
        $get_total4 = $this->m_crud->select_limit('barang br, Group1 gr1, Kartu_stock ks', "br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama, ".$stock_masuk.", ".$stock_keluar.", ".$stock_awal."", "br.Group1=gr1.Kode AND lokasi NOT IN ('MUTASI', 'Retur') AND keterangan<>'Input Barang' AND ks.kd_brg=br.kd_brg ".($where==null?'':$where), 'br.kd_brg ASC', "br.kd_brg, br.barcode, br.nm_brg, br.satuan, gr1.Nama", 60001, 80000, $having);*/

        $tstaw = 0; $tstma = 0; $tstke = 0; $tstak = 0; $tbeli = 0; $tjual = 0;

        foreach ($total as $row) {
            $stok_akhir = ((float)$row['stock_awal']+(float)$row['stock_masuk']-(float)$row['stock_keluar']);
            $tstaw = $tstaw + (float)$row['stock_awal'];
            $tstma = $tstma + (float)$row['stock_masuk'];
            $tstke = $tstke + (float)$row['stock_keluar'];
            $tstak = $tstak + $stok_akhir;
            $tbeli = $tbeli + ($stok_akhir * (float)$row['hrg_beli']);
            $tjual = $tjual + ($stok_akhir * (float)$row['hrg_jual_1']);
        }
		/*foreach ($get_total1 as $row) {
            $tstaw = $tstaw + (int)$row['stock_awal'];
            $tstma = $tstma + (int)$row['stock_masuk'];
            $tstke = $tstke + (int)$row['stock_keluar'];
            $tstak = $tstak + ((int)$row['stock_awal']+(int)$row['stock_masuk']-(int)$row['stock_keluar']);
        }

        foreach ($get_total2 as $row) {
            $tstaw = $tstaw + (int)$row['stock_awal'];
            $tstma = $tstma + (int)$row['stock_masuk'];
            $tstke = $tstke + (int)$row['stock_keluar'];
            $tstak = $tstak + ((int)$row['stock_awal']+(int)$row['stock_masuk']-(int)$row['stock_keluar']);
        }

        foreach ($get_total3 as $row) {
            $tstaw = $tstaw + (int)$row['stock_awal'];
            $tstma = $tstma + (int)$row['stock_masuk'];
            $tstke = $tstke + (int)$row['stock_keluar'];
            $tstak = $tstak + ((int)$row['stock_awal']+(int)$row['stock_masuk']-(int)$row['stock_keluar']);
        }

        foreach ($get_total4 as $row) {
            $tstaw = $tstaw + (int)$row['stock_awal'];
            $tstma = $tstma + (int)$row['stock_masuk'];
            $tstke = $tstke + (int)$row['stock_keluar'];
            $tstak = $tstak + ((int)$row['stock_awal']+(int)$row['stock_masuk']-(int)$row['stock_keluar']);
        }*/

        $data['tstaw'] = $tstaw;
        $data['tstma'] = $tstma;
        $data['tstke'] = $tstke;
        $data['tstak'] = $tstak;
        $data['ttbeli'] = $tbeli;
        $data['ttjual'] = $tjual;

        if(1==1) {
            if (isset($_POST['to_excel'])) {
                $result = $this->m_crud->join_data('barang br', "br.kd_brg, br.barcode, br.Deskripsi, br.nm_brg, br.satuan, br.hrg_beli, br.hrg_jual_1, gr1.Nama, ".$stock_masuk.", ".$stock_keluar.", ".$stock_awal."", array(array('table'=>'Group1 gr1', 'type'=>'LEFT')), array("br.Group1=gr1.Kode"), ($where==null?'':$where), 'gr1.Nama ASC, br.kd_brg ASC', "br.kd_brg, br.barcode, br.Deskripsi, br.nm_brg, br.satuan, br.stock_min, br.hrg_beli, br.hrg_jual_1, gr1.Nama", 0, 0, $having);
                /*$baca1 = $get_total1;
                $baca2 = $get_total2;
                $baca3 = $get_total3;
                $baca4 = $get_total4;*/

                $header = array(
                    'merge' => array('A1:M1', 'A2:M2', 'A3:M3'),
                    'auto_size' => true,
                    'font' => array(
                        'A1:A2' => array('bold' => true, 'color' => array('rgb' => '000000'), 'size' => 12, 'name' => 'Verdana'),
                        'A3' => array('bold' => true, 'name' => 'Verdana'),
                        'A6:M6' => array('bold' => true, 'size' => 9, 'name' => 'Verdana')
                    ),
                    'alignment' => array(
                        'A1:A3' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                        'A6:M6' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                    ),
                    '1' => array('A' => $data['site']->title),
                    '2' => array('A' => $data['title']),
                    '3' => array('A' => $tgl_awal . ' - ' . $tgl_akhir)
                );

                $i = 0;
                $supplier = '';
                $baris = 0;
                /*foreach ($baca1 as $row => $value) {
                    $supplier = $value['Nama'];
                    $i++;
                    $body[$baris] = array(
                        $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['satuan'], ($value['stock_awal'] + 0), ($value['stock_masuk'] + 0), ($value['stock_keluar'] + 0), ($value['stock_awal'] + $value['stock_masuk'] - $value['stock_keluar'] + 0)
                    );
                    $baris++;
                }

                foreach ($baca2 as $row => $value) {
                    $supplier = $value['Nama'];
                    $i++;
                    $body[$baris] = array(
                        $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['satuan'], ($value['stock_awal'] + 0), ($value['stock_masuk'] + 0), ($value['stock_keluar'] + 0), ($value['stock_awal'] + $value['stock_masuk'] - $value['stock_keluar'] + 0)
                    );
                    $baris++;
                }

                foreach ($baca3 as $row => $value) {
                    $supplier = $value['Nama'];
                    $i++;
                    $body[$baris] = array(
                        $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['satuan'], ($value['stock_awal'] + 0), ($value['stock_masuk'] + 0), ($value['stock_keluar'] + 0), ($value['stock_awal'] + $value['stock_masuk'] - $value['stock_keluar'] + 0)
                    );
                    $baris++;
                }

                foreach ($baca4 as $row => $value) {
                    $supplier = $value['Nama'];
                    $i++;
                    $body[$baris] = array(
                        $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['satuan'], ($value['stock_awal'] + 0), ($value['stock_masuk'] + 0), ($value['stock_keluar'] + 0), ($value['stock_awal'] + $value['stock_masuk'] - $value['stock_keluar'] + 0)
                    );
                    $baris++;
                }*/

                foreach ($result as $row => $value) {
                    $i++;
                    $stok_akhir = ($value['stock_awal'] + $value['stock_masuk'] - $value['stock_keluar'] + 0);
                    $body[$baris] = array(
                        $value['kd_brg'], ($value['kd_brg']==$value['barcode'])?$value['Deskripsi']:$value['barcode'], $value['nm_brg'], $value['satuan'], ($value['stock_awal'] + 0), ($value['stock_masuk'] + 0), ($value['stock_keluar'] + 0), $stok_akhir, ($value['hrg_beli']+0), ($value['hrg_jual_1']+0), ($stok_akhir*$value['hrg_beli']), ($stok_akhir*$value['hrg_jual_1']), $value['Nama']
                    );
                    $baris++;
                }

                $header['5'] = array('G' => $lokasi == null ? 'Semua Lokasi' : 'Lokasi : ' . $lokasi);
                $header['6'] = array(
                    'A' => 'Kode Barang', 'B' => 'Bcd/Art', 'C' => 'Nama Barang', 'D' => 'Satuan', 'E' => 'Stock Awal', 'F' => 'Stock Masuk', 'G' => 'Stock Keluar', 'H' => 'Stock Akhir', 'I' => 'Harga Beli', 'J' => 'Harga Jual', 'K' => 'Jumlah Beli', 'L' => 'Jumlah Jual', 'M' => 'Supplier'
                );
                $body[$i] = array('TOTAL', '', '', '', $tstaw, $tstma, $tstke, $tstak, '', '', $tbeli, $tjual);
                array_push($header['merge'], 'A' . ($i + 7) . ':D' . ($i + 7) . '');
                array_push($header['merge'], 'A5:C5');
                array_push($header['merge'], 'G5:M5');
                $header['alignment']['G5:M5'] = array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $header['font']['A' . ($i + 7) . ':M' . ($i + 7) . ''] = array('bold' => true, 'size' => 9, 'name' => 'Verdana');

                $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
            }
        }


        $data['tgl_awal'] = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }
	
	public function export_stock($lokasi=null,$tanggal=null,$kode=null,$file){
		$lokasi = base64_decode($lokasi);
		$tanggal = base64_decode($tanggal);
		$kode = base64_decode($kode);
		
		if ($tanggal != null) {
            $explode_date = explode(' - ', $tanggal);
            $tgl_awal = str_replace('/','-',$explode_date[0]);
            $tgl_akhir = str_replace('/','-',$explode_date[1]);
        }
		$where = "lokasi NOT IN ('MUTASI', 'Retur')";
		
		if($file=='excel'){
			if(isset($lokasi)&&$lokasi!=null&&$lokasi!='semua'){ ($where==null)?null:$where.=" and "; $where.="lokasi = '".$lokasi."'"; }
			if(isset($tanggal)&&$tanggal!=null){ ($where==null)?null:$where.=" and "; $where.="tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'"; }
			if(isset($kode)&&$kode!=null){ ($where==null)?null:$where.=" and "; $where.="kd_brg = '".$kode."'"; }
			
			if(isset($lokasi)&&$lokasi!=null&&$lokasi=='semua'){
				$barang = $this->m_crud->get_data('barang', 'nm_brg, hrg_jual_1', "kd_brg = '".$kode."'");
			} else {
				$barang = $this->m_crud->get_data('barang_hrg brh, barang br', 'br.nm_brg, brh.hrg_jual_1', "br.kd_brg=brh.barang and brh.barang = '".$kode."' and brh.lokasi = '".$lokasi."'");
			}
			
			$kartu_stock = $this->m_crud->read_data("Kartu_stock", "kd_trx, tgl, keterangan, stock_in, stock_out", $where, "tgl asc");
			
			$where_sa = "lokasi NOT IN ('MUTASI', 'Retur')";
			if(isset($lokasi)&&$lokasi!=null&&$lokasi!='semua'){ ($where_sa==null)?null:$where_sa.=" and "; $where_sa.="lokasi = '".$lokasi."'"; }
			if(isset($tanggal)&&$tanggal!=null){ ($where_sa==null)?null:$where_sa.=" and "; $where_sa.="tgl < '".$tgl_awal." 00:00:00'"; }
			if(isset($kode)&&$kode!=null){ ($where_sa==null)?null:$where_sa.=" and "; $where_sa.="kd_brg = '".$kode."'"; }
			
			$saldo_awal = $this->m_crud->get_data("Kartu_stock", "sum(stock_in-stock_out) saldo_awal", $where_sa); 
			
			$header = array(
                'merge' 	=> array('A1:G1','A2:G2','A3:G3','A5:B5','C5:D5','E5:F5'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A1' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A2:A3' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>11, 'name'=>'Verdana'),
                    'A5:G5' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>11, 'name'=>'Verdana'),
                    'A6:G6' => array('bold'=>true, 'size'=>11, 'name'=>'Verdana'),
                ),
                'alignment' => array(
                    'A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'A6:G6' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ),
                '1' => array('A' => $this->data['site']->title),
                '2' => array('A' => 'Kartu Stock'),
                '3' => array('A' => 'Periode : '.$tgl_awal.' - '.$tgl_akhir),
                '5' => array('A' => 'Lokasi : '.$lokasi, 'C' => 'Nama Barang : '.$barang['nm_brg'], 'E' => 'Harga : '.($barang['hrg_jual_1']+0)),
                '6' => array(
                    'A'=>'No', 'B'=>'Tanggal', 'C'=>'No Nota', 'D'=>'Keterangan', 'E'=>'Masuk', 'F'=>'Keluar', 'G'=>'Sisa'
                )
            );
			
			$no = 0; $sisa = 0;
			if($saldo_awal!=null){ 
				$no++;
				$sisa = $saldo_awal['saldo_awal']; 
				$body[0]=array(
					$no, substr($tgl_awal, 0, 10), '-', 'Saldo Awal', 0, 0, ($sisa + 0)
				); 
			}
			foreach ($kartu_stock as $row => $value) {
				$no++;
				$sisa = $sisa + $value['stock_in'] - $value['stock_out'];
				$body[$row+1] = array(
					$no, substr($value['tgl'], 0, 10), $value['kd_trx'], $value['keterangan'], ($value['stock_in'] + 0), ($value['stock_out'] + 0), ($sisa + 0)
				);
			}
			
			if($saldo_awal==null && $kartu_stock==null){ $body=array(); }
			$this->m_export_file->to_excel(str_replace(' ', '_', 'Laporan Stock Barang.xlsx'), $header, $body);
			//echo '<script>alert("'.$lokasi.$tgl_awal.$tgl_akhir.$file.'");</script>';
		}
	}
	
    public function detail_by_lokasi($object) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '10240M');
		//ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
		//ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M
		
		$tgl_awal = date("Y-m-d");
        $tgl_akhir = date("Y-m-d");
        $data = json_decode(base64_decode($object));
        $detail_list = '';

        if ($data[2] == '') {
            $where_lokasi = " and ks.lokasi <> 'HO' and ks.lokasi <> '' and ks.lokasi is not null"; 
        } else {
            $where_lokasi = "";
        }

        if ($data[0] != null) {
            $explode_date = explode(' - ', $data[0]);
            $tgl_awal = str_replace('/','-',$explode_date[0]);
            $tgl_akhir = str_replace('/','-',$explode_date[1]);
        }

        $where_stock = "kd_brg = '".$data[1]."'";

        $stock_awal = "isnull((select sum(stock_in - stock_out) from kartu_stock where lokasi=ks.lokasi AND ".$where_stock." and tgl < '".$tgl_awal." 00:00:00'),0) as stock_awal";
        $stock_masuk = "isnull((select sum(stock_in) from kartu_stock where lokasi=ks.lokasi AND ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_masuk";
        $stock_keluar = "isnull((select sum(stock_out) from kartu_stock where lokasi=ks.lokasi AND ".$where_stock." and tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59'),0) as stock_keluar";
        $q_detail = $this->m_crud->read_data("Kartu_stock ks, Lokasi lk", "ks.lokasi, lk.Nama,".$stock_awal.",".$stock_masuk.",".$stock_keluar, "ks.lokasi=lk.Kode".$where_lokasi, "ks.lokasi ASC", "ks.lokasi, lk.Nama");
		
        $no = 1;
        $staw = 0;
        $stma = 0;
        $stke = 0;
        $stak = 0;
        foreach ($q_detail as $row) {
            if (($row['stock_awal']+$row['stock_masuk']-$row['stock_keluar']+0) != 0 || ($row['stock_awal']+0) != 0) {
                $detail_list .= '
                <tr>
                    <td>' . $no . '</td>
                    <td>' . $row['Nama'] . '</td>
                    <td>' . ((float)$row['stock_awal'] + 0) . '</td>
                    <td>' . ((float)$row['stock_masuk'] + 0) . '</td>
                    <td>' . ((float)$row['stock_keluar'] + 0) . '</td>
                    <td>' . ((float)$row['stock_awal'] + (float)$row['stock_masuk'] - (float)$row['stock_keluar'] + 0) . '</td>
                    <td class="text-center"><button class="btn btn-primary" onclick="detail_by_transaksi(\'' . $data[1] . '\', \'' . $row['lokasi'] . '\')"><i class="md md-visibility"></i> Detail</button></td>
                </tr>
                ';
                $no++;
                $staw = $staw + ((float)$row['stock_awal'] + 0);
                $stma = $stma + ((float)$row['stock_masuk'] + 0);
                $stke = $stke + ((float)$row['stock_keluar'] + 0);
                $stak = $stak + ((float)$row['stock_awal'] + (float)$row['stock_masuk'] - (float)$row['stock_keluar'] + 0);
            }
        }
        $detail_list .= '
        <tr>
            <th colspan="2">TOTAL</th>
            <th>'.$staw.'</th>
            <th>'.$stma.'</th>
            <th>'.$stke.'</th>
            <th>'.$stak.'</th>
            <th></th>
        </tr>
        ';

        echo $detail_list;
    }




   function detail_by_transaksi($object) {
        $tgl_awal = date("Y-m-d");
        $tgl_akhir = date("Y-m-d");
        $data = json_decode(base64_decode($object));
        $detail_list = '';

        if ($data[0] != null) {
            $explode_date = explode(' - ', $data[0]);
            $tgl_awal = str_replace('/','-',$explode_date[0]);
            $tgl_akhir = str_replace('/','-',$explode_date[1]);
        }

        $q_title = $this->m_crud->get_data("Kartu_stock ks, Lokasi lk, barang br", "lk.Nama, br.kd_brg, br.barcode, br.nm_brg", "ks.lokasi=lk.Kode AND ks.kd_brg=br.kd_brg AND ks.kd_brg = '".$data[1]."' AND ks.lokasi = '".$data[2]."'");
        $q_detail = $this->m_crud->read_data("Kartu_stock", "kd_trx, tgl, keterangan, stock_in, stock_out", "kd_brg = '".$data[1]."' AND lokasi = '".$data[2]."' AND tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59' ", "tgl asc");
        $no = 1;
        $stma = 0;
        $stke = 0;
        foreach ($q_detail as $row) {
            $detail_list .= '
            <tr>
                <td>'.$no.'</td>
                <td>'.$row['kd_trx'].'</td>
                <td>'.substr($row['tgl'], 0, 10).'</td>
                <td>'.(float)$row['stock_in'].'</td>
                <td>'.(float)$row['stock_out'].'</td>
                <td>'.$row['keterangan'].'</td>
            </tr>
            ';
            $stma = $stma + (float)$row['stock_in'];
            $stke = $stke + (float)$row['stock_out'];
            $no++;
        }

        $detail_list .= '
        <tr>
            <th colspan="3">TOTAL</th>
            <th>'.$stma.'</th>
            <th>'.$stke.'</th>
            <th></th>
        </tr>
        ';

        $title = [$q_title['Nama'], $q_title['kd_brg'], $q_title['barcode'], $q_title['nm_brg']];

        $pdf = '<a href="'.base_url().strtolower($this->control).'/pdf_detail_stock/'.base64_encode($tgl_awal).'/'.base64_encode($tgl_akhir).'/'.base64_encode($data[2]).'/'.base64_encode($data[3]).'" target="_blank" class="btn btn-primary waves-effect"><i class="md md-print"></i> To PDF</a>';

        echo json_encode(array('list' => $detail_list, 'pdf' => $pdf, 'title' => $title));
    }

    function pdf_detail_stock($tmp_tgl_awal, $tmp_tgl_akhir, $tmp_kode_barang, $tmp_lokasi) {
        $kode_barang = base64_decode($tmp_kode_barang);
        $lokasi = base64_decode($tmp_lokasi);
        $tgl_awal = base64_decode($tmp_tgl_awal);
        $tgl_akhir = base64_decode($tmp_tgl_akhir);

        $get_data = $this->m_crud->get_data("barang", "kd_brg, barcode, nm_brg", "kd_brg='".$kode_barang."'");

        $function = 'stock_report';
        $view = $this->control . '/';
        $data['title'] = 'Report Stock Detail';
        $data['content'] = $view.'pdf_'.$function;
        $data['report_detail'] = $this->m_crud->read_data("Kartu_stock", "kd_trx, tgl, keterangan, lokasi, stock_in, stock_out", "kd_brg='".$kode_barang."' AND tgl >= '".$tgl_awal." 00:00:00' and tgl <= '".$tgl_akhir." 23:59:59' AND lokasi = '".$lokasi."'", "tgl desc");

        $t_row = count($data['report_detail']);
        //$t_row = $t_row + 23;
        $data['row_per_page'] = 30;
        $data['row_one_page'] = 25;
        $method='I';
        $file = str_replace('/', '-', str_replace(' ', '_', 'Stock Report').'-'.str_replace('/', '-', $get_data['kd_brg']));
        $html = $this->load->view('bo/'.$data['content'], $data, true);

        $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
            '<div><h3 style="text-align:center;"><b>Report Stock Detail</b></h3></div>
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
									<td><b>Kode Barang</b></td>
									<td><b>:</b></td>
									<td>'.$get_data['kd_brg'].'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Barcode</b></td>
									<td><b>:</b></td>
									<td>'.$get_data['barcode'].'</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td><b>Nama Barang</b></td>
									<td><b>:</b></td>
									<td>'.$get_data['nm_brg'].'</td>
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
            'left'=>10,'right'=>10,'top'=>56,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
        );
        $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);
    }

    public function adjusment_report($action = null, $id = null){
        $this->access_denied(117);
        $data = $this->data;
        $function = 'adjusment_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Adjustment';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "ad.kd_kasir=ud.user_id AND ad.kd_trx=da.kd_trx";
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
            $where .= "LEFT(CONVERT(VARCHAR, ad.tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, ad.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="ad.lokasi = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(ad.kd_trx like '%".$search."%' or da.kd_brg like '%".$search."%' or ad.lokasi like '%".$search."%' or ud.nama like '%".$search."%')"; }

        $page = ($id==null?1:$id);

        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data('adjust ad, user_detail ud, det_adjust da', "ad.kd_trx", $where, null, "ad.kd_trx");
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

        $data['report'] = $this->m_crud->select_limit('adjust ad, user_detail ud, det_adjust da', "ad.tgl, ad.kd_trx, ad.keterangan, ad.lokasi, ud.nama", $where, 'ad.kd_trx desc', "ad.tgl, ad.kd_trx, ad.keterangan, ad.lokasi, ud.nama", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $detail = $this->m_crud->read_data('adjust ad, user_detail ud, det_adjust da', "ad.tgl, ad.kd_trx, ad.keterangan, ad.lokasi, ud.nama", $where, 'ad.kd_trx desc', "ad.tgl, ad.kd_trx, ad.keterangan, ad.lokasi, ud.nama");
            $baca = $detail;
            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['tgl'], $value['kd_trx'], $value['lokasi'], $value['keterangan'], $value['nama']
                );
            }
            $header = array(
                'merge' 	=> array('A1:E1','A2:E2','A3:E3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:E5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'No. Adjusment', 'C'=>'Lokasi', 'D'=>'Keteramgam', 'E'=>'Operator'
                )
            );
            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('adjust ad, user_detail ud', "ad.tgl, ad.kd_trx, ad.keterangan, ad.lokasi, ud.nama", "ad.kd_kasir=ud.user_id AND ad.kd_trx='".$id."'");
            $data['report_detail'] = $this->m_crud->join_data('det_adjust da', 'br.kd_brg, br.barcode, br.nm_brg, da.status, isnull(da.stock_terakhir, 0) stock_terakhir, da.qty_adjust, da.saldo_stock', array(array('table'=>'barang br', 'type'=>'LEFT')), array('da.kd_brg = br.kd_brg'), "da.kd_trx = '".$id."'");

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
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Adjusment Stock</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
					<table width="100%">
						<thead>
							<tr>
								<th width="2%"></th>
								<th width="15%"></th>
								<th width="2%"></th>
								<th width="30%"></th>

								<th width="5%"></th>
								<th width="12%"></th>
								<th width="2%"></th>
								<th width="32%"></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td></td>
								<td><b>Tanggal</b></td>
								<td><b>:</b></td>
								<td>'.substr($data['report']['tgl'], 0, 10).'</td>
								<td></td>
								<td><b>Operator</b></td>
								<td><b>:</b></td>
								<td>'.$data['report']['nama'].'</td>
							</tr>
							<tr>
								<td></td>
								<td><b>No. Adjust</b></td>
								<td><b>:</b></td>
								<td>'.$data['report']['kd_trx'].'</td>
								<td></td>
								<td><b>Keterangan</b></td>
								<td><b>:</b></td>
								<td rowspan="2">'.$data['report']['keterangan'].'</td>
							</tr>
							<tr>
								<td></td>
								<td><b>Lokasi</b></td>
								<td><b>:</b></td>
								<td>'.$data['report']['lokasi'].'</td>
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

    public function stock_opname_report($action = null, $id = null){
        $this->access_denied(114);
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $data = $this->data;
        $function = 'stock_opname_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Stock Opname';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $config['upload_path'] = realpath(APPPATH."../assets");
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 5120;
        $this->load->library('upload',$config);
        if(isset($_POST['import']) && $this->upload->do_upload('file')){
            $excel = array('upload_data' => $this->upload->data());
            $this->load->library('PHPExcel');
            $objPHPExcel = PHPExcel_IOFactory::load(APPPATH.'../assets/'.$excel['upload_data']['file_name']);
            unlink($config['upload_path'].'/'.$excel['upload_data']['file_name']);
            $cell_collection = $objPHPExcel->setActiveSheetIndexByName('Import Opname')->getCellCollection();
            $header = array();
            $arr_data = array();
            /*
            kd_trx v20	kd_kasir v10	stock_terakhir m	hrg_beli m		status c1
            kd_brg v15	qty_fisik m		tgl dt		lokasi v100

            a			b			c						d
            kd_brg		qty_fisik	tgl						lokasi
            kdbrg001	10			2018-05-18 14:26:10		IDK-ATP
            */
            foreach ($cell_collection as $cell){
                $baris = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                $kolom = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                $isi = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                if($baris >= 2){ if($baris != null && $kolom != null && $isi != null){ $arr_data[$baris][$kolom] = $isi; } }
                //if($baris != null && $kolom != null && $isi != null){ $arr_data[$baris][$kolom] = $isi; }
            }
            $error=0;
            //$no=0;
            $this->db->trans_begin();
            foreach($arr_data as $row){	//$no++;
                if($row['A']!=null && $row['B']!=null && $row['C']!=null && $row['D']!=null){
                    $kd_brg = $row['A']!=null?substr(ltrim(rtrim(str_replace("'",'`',$row['A']))),0,15):'';
                    $qty_fisik = $row['B']!=null?(int)substr(ltrim(rtrim(str_replace(",",'',$row['B']))),0,10):0;
                    $tgl = $row['C']!=null?date('Y-m-d H:i:s',strtotime(substr(ltrim(rtrim(str_replace("'",'`',$row['C']))),0,100))):date('Y-m-d H:i:s');
                    $lokasi = $row['D']!=null?substr(ltrim(rtrim(str_replace("'",'`',$row['D']))),0,100):'';

                    $stock = "isnull((select sum(stock_in - stock_out) from kartu_stock where kartu_stock.kd_brg=barang.kd_brg and lokasi NOT IN ('MUTASI', 'Retur') and kartu_stock.lokasi = '".$lokasi."' and tgl < '".$tgl."'),0) as stock";
                    $barang = $this->m_crud->get_data('barang', "hrg_beli, ".$stock.", (select serial from lokasi where kode = '".$lokasi."') serial", "ltrim(rtrim(kd_brg)) = '".$kd_brg."'");

                    if($barang!=null && $barang['serial']!=null && $barang['serial']!=''){
                        $kd_trx=$this->m_website->generate_kode('OP', $barang['serial'], date('ymd', strtotime($tgl)));
                        //$kd_trx='OP-'.date('ymd', strtotime($tgl)).str_pad($no, 4, '0', STR_PAD_LEFT).'-'.$barang['serial'];

                        $this->m_crud->create_data('opname', array(
                            'kd_brg'=>$kd_brg,
                            'qty_fisik'=>$qty_fisik,
                            'tgl'=>$tgl,
                            'lokasi'=>$lokasi,
                            'kd_trx'=>$kd_trx,
                            'stock_terakhir'=>$barang['stock'],
                            'hrg_beli'=>($barang['hrg_beli']+0),
                            'kd_kasir'=>$this->user,
                            'status'=>'0'
                        ));
                    }
                }
            }
            if ($this->db->trans_status() === FALSE){ $this->db->trans_rollback(); $error = 1; }
            else{ $this->db->trans_commit(); }
            //$data['excel'] = $arr_data;
            if ($error==0){ echo '<script>alert("Import Berhasil");</script>'; }
            else { echo '<script>alert("Import Gagal");</script>'; }
        } else if(isset($_POST['import'])) {
            echo '<script>alert("Upload File Error");</script>';
        }

        $where = "op.kd_brg = br.kd_brg AND br.kel_brg = kb.kel_brg";
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d'); $tgl_periode = date('Y-m-d');

        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'],'field-date'=>$_POST['field-date'], 'tgl_periode' => $_POST['tgl_periode'], 'lokasi' => $_POST['lokasi'], 'posting' => $_POST['posting'], 'selisih'=>$_POST['selisih'], 'group1'=>$_POST['group1'], 'order'=>$_POST['order'], 'filter2'=>$_POST['filter2']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $tgl_periode = ($this->session->search['tgl_periode']==null)?$tgl_periode:$this->session->search['tgl_periode']; $posting = $this->session->search['posting']; $date = $this->session->search['field-date']; $selisih = $this->session->search['selisih']; $group1 = $this->session->search['group1']; $order = $this->session->search['order']; $filter2 = $this->session->search['filter2'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= "";
            $where .= " and LEFT(CONVERT(VARCHAR, op.tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";$periode = "Periode : ".$date1." - ".$date2;
        } else {
            ($where == null) ? null : $where .= "";
            $where .= " and LEFT(CONVERT(VARCHAR, op.tgl, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";$periode = "Periode : ".$tgl_awal." - ".$tgl_akhir;
        }
        //($where==null)?null:$where.=" and "; $where.="(op.tgl >= '".$tgl_periode." 00:00:00' and op.tgl <= '".$tgl_periode." 23:59:59')";
		$getFilter = ($filter2==null?"op.tgl":$filter2);
		$getOrder = ($order==null?"ASC":$order);

        if($selisih=='<'){$q_selisih="(op.qty_fisik-op.stock_terakhir)<0";}else if($selisih=='>'){$q_selisih="(op.qty_fisik-op.stock_terakhir)>0";}else if($selisih=='='){$q_selisih="(op.qty_fisik-op.stock_terakhir)=0";}else{$q_selisih="";}
        if(isset($group1)&&$group1!=null){ ($where==null)?null:$where.=" and "; $where.="(br.Group1 = '".$group1."')"; }
        if(isset($posting)&&$posting!=null){ ($where==null)?null:$where.=" and "; $where.="(op.status = '".$posting."')"; }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="op.lokasi = '".$lokasi."'"; }
		if(isset($selisih)&&$selisih!=null&&$selisih!='-'){($where==null)?null:$where.=" and "; $where.=$q_selisih; }
		
        if(isset($search)&&$search!=null){
			($where==null)?null:$where.=" and ";
			if(strlen($search) != 1){
				$where.="(op.kd_trx like '%".$search."%' or br.kd_brg like '%".$search."%' or br.barcode like '%".$search."%' or op.kd_kasir like '%".$search."%')";
			} else {
				$where.="(op.kd_trx like '".$search."%' or br.kd_brg like '".$search."%' or br.barcode like '".$search."%' or op.kd_kasir like '".$search."%')";
			}
		}

        $page = ($id==null?1:$id);

        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_over('OPNAME op, barang br, kel_brg kb', "op.kd_trx", $where, 'op.tgl desc');
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

        $data['report'] = $this->m_crud->select_limit('OPNAME op, barang br, kel_brg kb', "op.*, br.barcode, br.nm_brg, kb.nm_kel_brg", $where, "br.nm_brg $getOrder", null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        $detail = $this->m_crud->read_data('OPNAME op, barang br, kel_brg kb', "op.*", $where, 'op.tgl desc');

        $i = 1; $tsf = 0; $tst = 0; $tsv = 0;
        foreach ($detail as $row) {
            $tsf = $tsf + (float)$row['qty_fisik'];
            $tst = $tst + (float)$row['stock_terakhir'];
            $tsv = $tsv + ($row['hrg_beli'] * ((float)$row['qty_fisik']-(float)$row['stock_terakhir']));
            $i = $i * (float)$row['status'];
        }

        if (count($data['report']) == 0 || $i != 0) {
            $status = 'disabled';
        } else {
            $status = 'enabled';
        }

        $data['tst'] = $tst;
        $data['tsf'] = $tsf;
        $data['tsv'] = $tsv;
        $data['status'] = $status;

        if(isset($_POST['to_excel'])){
            $detail = $this->m_crud->read_data('OPNAME op, barang br, kel_brg kb', "op.*, br.barcode, br.nm_brg, kb.nm_kel_brg", $where, 'op.tgl desc');
            $baca = $detail;

            $header = array(
                'merge' 	=> array('A1:N1','A2:N2','A3:N3'),
                'auto_size' => true,
                'font' 		=> array(
                    'A1:A2' => array('bold'=>true, 'color'=>array('rgb'=>'000000'), 'size'=>12, 'name'=>'Verdana'),
                    'A3' => array('bold'=>true,'name'=>'Verdana'),
                    'A5:N5' => array('bold'=>true, 'size'=>9, 'name'=>'Verdana')
                ),
                'alignment' => array('A1:A3' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
                '1' => array('A' => $data['site']->title),
                '2' => array('A' => $data['title']),
                '3' => array('A' => $periode),
                '5' => array(
                    'A'=>'No. Transaksi', 'B'=>'Tanggal', 'C'=>'Kode Barang', 'D'=>'Barcode', 'E'=>'Nama Barang', 'F'=>'Kel. Barang', 'G'=>'Stock Terakhir', 
					'H'=>'Stock Fisik', 'I'=>'Selisih', 'J'=>'Value', 'K'=>'Value Selisih', 'L'=>'Operator', 'M'=>'Status', 'N'=>'Keterangan' 
                )
            );

            $i = 0;
            foreach($baca as $row => $value){
                $i++;
                $body[$row] = array(
                    $value['kd_trx'], substr($value['tgl'], 0, 10), $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['nm_kel_brg'], $value['stock_terakhir'], 
					$value['qty_fisik'], ($value['qty_fisik']-$value['stock_terakhir']), $value['hrg_beli'], ($value['hrg_beli'] * ((float)$value['qty_fisik']-(float)$value['stock_terakhir'])), 
					$value['kd_kasir'], ($value['status']==0)?'Un Posting':'Posting', ($value['qty_fisik']-$value['stock_terakhir'])>0?'Plus':(($value['qty_fisik']-$value['stock_terakhir'])<0?'Minus':'Klop') 
                );
            }

            $body[$i] = array('TOTAL', '', '', '', '', '', $tst, $tsf, ($tsf-$tst), '', $tsv);
            array_push($header['merge'], 'A'.($i+6).':F'.($i+6).'');
            $header['font']['A'.($i+6).':K'.($i+6).''] = array('bold'=>true, 'size'=>9, 'name'=>'Verdana');

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

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

            $header = $this->m_website->logo().'<br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div><h3 style="text-align:center;"><b>Alokasi Barang</b></h3></div>
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
									<td><b>No. Alokasi</b></td>
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
                'left'=>10,'right'=>10,'top'=>55,'bottom'=>(($t_row>$data['row_one_page'])?35:5),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    function penyesuaian_opname_plus() {
        $date = $_POST['tgl_periode_'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        $lokasi = $_POST['lokasi_'];

        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');

        $get_barang_opname = $this->m_crud->get_data("OPNAME","LEFT(CONVERT(VARCHAR, tgl, 120), 10) tgl", "lokasi='".$lokasi."' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'", "tgl DESC", null, 1);
        $date_stock = $get_barang_opname['tgl'];
        $where = "br.kel_brg=kb.kel_brg AND LEFT(CONVERT(VARCHAR, br.tgl_input, 120), 10)<'".$date2."' AND br.kd_brg IN (SELECT kd_brg FROM Kartu_stock WHERE lokasi='" . $lokasi . "' AND keterangan <> 'Input Barang') AND kd_brg NOT IN (SELECT kd_brg FROM OPNAME WHERE lokasi='" . $lokasi . "' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' GROUP BY kd_brg)";
        $q_selisih = "(SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE LEFT(CONVERT(VARCHAR, tgl, 120), 10) <= '".$date_stock."' AND lokasi='" . $lokasi . "' AND kd_brg=br.kd_brg)>0";
        $group = "br.kd_brg, br.hrg_beli";
        $stock_sistem = "isnull((SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE LEFT(CONVERT(VARCHAR, tgl, 120), 10) <= '".$date_stock."' AND lokasi='" . $lokasi . "' AND kd_brg=br.kd_brg),0) stock_sistem";
        $read_data = $this->m_crud->read_data("barang br, kel_brg kb", "br.kd_brg, br.hrg_beli, " . $stock_sistem, $where, "br.kd_brg ASC", $group, 0, 0, $q_selisih);

        $this->db->trans_begin();

        foreach ($read_data as $get_opname) {
            $kode_trx = $this->m_website->generate_kode("PN", $lokasi, substr(str_replace('-','',$date_stock), 2, 8));

            //$this->m_crud->delete_data("Kartu_stock", "keterangan <> 'Input Barang' AND lokasi <> 'Retur' AND lokasi='" . $lokasi . "' AND kd_brg = '" . $get_opname['kd_brg'] . "' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) < '" . substr($date_stock, 0, 10) . "'");

            $stock_in1 = $get_opname['stock_sistem'];
            $stock_out1 = 0;

            $stock_in2 = 0;
            $stock_out2 = $get_opname['stock_sistem'];

            /*
			$stock_terakhir = array(
                'kd_trx' => $kode_trx,
                'tgl' => $date_stock,
                'kd_brg' => $get_opname['kd_brg'],
                'saldo_awal' => 0,
                'stock_in' => $stock_in1,
                'stock_out' => $stock_out1,
                'lokasi' => $lokasi,
                'keterangan' => 'Stock Terakhir',
                'hrg_beli' => $get_opname['hrg_beli']
            );
            $this->m_crud->create_data("Kartu_stock", $stock_terakhir);
			*/
				
            $penyesuaian = array(
                'kd_trx' => $kode_trx,
                'tgl' => $date_stock,
                'kd_brg' => $get_opname['kd_brg'],
                'saldo_awal' => 0,
                'stock_in' => $stock_in2,
                'stock_out' => $stock_out2,
                'lokasi' => $lokasi,
                'keterangan' => 'Penyesuaian',
                'hrg_beli' => $get_opname['hrg_beli']
            );
            $this->m_crud->create_data("Kartu_stock", $penyesuaian);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo 'failed';
        } else {
            $this->db->trans_commit();
            echo 'success';
        }
    }

    function penyesuaian_opname_minus() {
        $date = $_POST['tgl_periode_'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        $lokasi = $_POST['lokasi_'];

        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');

        $get_barang_opname = $this->m_crud->get_data("OPNAME","LEFT(CONVERT(VARCHAR, tgl, 120), 10) tgl", "lokasi='".$lokasi."' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'", "tgl DESC", null, 1);
        $date_stock = $get_barang_opname['tgl'];
        $where = "br.kel_brg=kb.kel_brg AND LEFT(CONVERT(VARCHAR, br.tgl_input, 120), 10)<'".$date2."' AND br.kd_brg IN (SELECT kd_brg FROM Kartu_stock WHERE lokasi='" . $lokasi . "' AND keterangan <> 'Input Barang') AND kd_brg NOT IN (SELECT kd_brg FROM OPNAME WHERE lokasi='" . $lokasi . "' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' GROUP BY kd_brg)";
        $q_selisih = "(SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE LEFT(CONVERT(VARCHAR, tgl, 120), 10) <= '".$date_stock."' AND lokasi='" . $lokasi . "' AND kd_brg=br.kd_brg)<0";
        $group = "br.kd_brg, br.hrg_beli";
        $stock_sistem = "isnull((SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE LEFT(CONVERT(VARCHAR, tgl, 120), 10) <= '".$date_stock."' AND lokasi='" . $lokasi . "' AND kd_brg=br.kd_brg),0) stock_sistem";
        $read_data = $this->m_crud->read_data("barang br, kel_brg kb", "br.kd_brg, br.hrg_beli, " . $stock_sistem, $where, "br.kd_brg ASC", $group, 0, 0, $q_selisih);

        $this->db->trans_begin();

        foreach ($read_data as $get_opname) {
            $kode_trx = $this->m_website->generate_kode("PN", $lokasi, substr(str_replace('-','',$date_stock), 2, 8));

            //$this->m_crud->delete_data("Kartu_stock", "keterangan <> 'Input Barang' AND lokasi <> 'Retur' AND lokasi='" . $lokasi . "' AND kd_brg = '" . $get_opname['kd_brg'] . "' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) < '" . substr($date_stock, 0, 10) . "'");

            $stock_in1 = 0;
            $stock_out1 = $get_opname['stock_sistem'] * -1;

            $stock_in2 = $get_opname['stock_sistem'] * -1;
            $stock_out2 = 0;

            /*
			$stock_terakhir = array(
                'kd_trx' => $kode_trx,
                'tgl' => $date_stock,
                'kd_brg' => $get_opname['kd_brg'],
                'saldo_awal' => 0,
                'stock_in' => $stock_in1,
                'stock_out' => $stock_out1,
                'lokasi' => $lokasi,
                'keterangan' => 'Stock Terakhir',
                'hrg_beli' => $get_opname['hrg_beli']
            );
            $this->m_crud->create_data("Kartu_stock", $stock_terakhir);
			*/
				
            $penyesuaian = array(
                'kd_trx' => $kode_trx,
                'tgl' => $date_stock,
                'kd_brg' => $get_opname['kd_brg'],
                'saldo_awal' => 0,
                'stock_in' => $stock_in2,
                'stock_out' => $stock_out2,
                'lokasi' => $lokasi,
                'keterangan' => 'Penyesuaian',
                'hrg_beli' => $get_opname['hrg_beli']
            );
            $this->m_crud->create_data("Kartu_stock", $penyesuaian);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo 'failed';
        } else {
            $this->db->trans_commit();
            echo 'success';
        }
    }

    function posting_opname($jenis) {

        $this->db->trans_begin();

        if ($jenis == 'item') {
            $kode_transaksi = $_POST['kode_'];
            $get_opname = $this->m_crud->get_data("OPNAME", "*", "status = '0' AND kd_trx='".$kode_transaksi."'");

            //$this->m_crud->delete_data("Kartu_stock", "keterangan <> 'Input Barang' AND lokasi <> 'Retur' AND lokasi='".$get_opname['lokasi']."' AND kd_brg = '".$get_opname['kd_brg']."' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) < '".substr($get_opname['tgl'],0,10)."'");
            $get_stock = $this->m_crud->get_data("Kartu_stock", "isnull(sum(stock_in - stock_out), 0) stock_terakhir", "lokasi NOT IN ('MUTASI', 'Retur') AND tgl < '".$get_opname['tgl']."' AND lokasi = '".$get_opname['lokasi']."' AND kd_brg = '".$get_opname['kd_brg']."'");


            if ($get_stock['stock_terakhir'] < 0) {
                $stock_in1 = 0;
                $stock_out1 = $get_stock['stock_terakhir'] * -1;

                $stock_in2 = $get_opname['qty_fisik'] + ($get_stock['stock_terakhir'] * -1);
                $stock_out2 = 0;
            } else {
                $stock_in1 = $get_stock['stock_terakhir'];
                $stock_out1 = 0;

                $selisih = $get_opname['qty_fisik'] - $get_stock['stock_terakhir'];

                if ($selisih >= 0) {
                    $stock_in2 = $selisih;
                    $stock_out2 = 0;
                } else {
                    $stock_in2 = 0;
                    $stock_out2 = $selisih * -1;
                }
            }

            /*$stock_terakhir = array(
                'kd_trx' => $get_opname['kd_trx'],
                'tgl' => $get_opname['tgl'],
                'kd_brg' => $get_opname['kd_brg'],
                'saldo_awal' => 0,
                'stock_in' => $stock_in1,
                'stock_out' => $stock_out1,
                'lokasi' => $get_opname['lokasi'],
                'keterangan' => 'Stock Terakhir',
                'hrg_beli' => $get_opname['hrg_beli']
            );
            $this->m_crud->create_data("Kartu_stock", $stock_terakhir);*/

            $penyesuaian = array(
                'kd_trx' => $get_opname['kd_trx'],
                'tgl' => $get_opname['tgl'],
                'kd_brg' => $get_opname['kd_brg'],
                'saldo_awal' => 0,
                'stock_in' => $stock_in2,
                'stock_out' => $stock_out2,
                'lokasi' => $get_opname['lokasi'],
                'keterangan' => 'Penyesuaian Opname',
                'hrg_beli' => $get_opname['hrg_beli']
            );
            $this->m_crud->create_data("Kartu_stock", $penyesuaian);

            $this->m_crud->update_data("OPNAME", array('status' => '1'), "kd_trx = '".$kode_transaksi."'");
        } else {
            $date = $_POST['tgl_periode_'];
            $explode_date = explode(' - ', $date);
            $date1 = str_replace('/','-',$explode_date[0]);
            $date2 = str_replace('/','-',$explode_date[1]);

            ini_set('max_execution_time', 3600);
            ini_set('memory_limit', '1024M');

            $read_opname = $this->m_crud->read_data("OPNAME", "*", "status = '0' AND lokasi='".$_POST['lokasi_']."' AND tgl BETWEEN '".$date1." 00:00:00' AND '".$date2." 23:59:59'");

            foreach ($read_opname as $get_opname) {
                //$this->m_crud->delete_data("Kartu_stock", "keterangan <> 'Input Barang' AND lokasi <> 'Retur' AND lokasi='".$get_opname['lokasi']."' AND kd_brg = '".$get_opname['kd_brg']."' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) < '".substr($get_opname['tgl'],0,10)."'");
                $get_stock = $this->m_crud->get_data("Kartu_stock", "isnull(sum(stock_in - stock_out), 0) stock_terakhir", "lokasi NOT IN ('MUTASI', 'Retur') AND tgl < '".$get_opname['tgl']."' AND lokasi = '".$get_opname['lokasi']."' AND kd_brg = '".$get_opname['kd_brg']."'");

                if ($get_stock['stock_terakhir'] < 0) {
                    $stock_in1 = 0;
                    $stock_out1 = $get_stock['stock_terakhir'] * -1;

                    $stock_in2 = $get_opname['qty_fisik'] + ($get_stock['stock_terakhir'] * -1);
                    $stock_out2 = 0;
                } else {
                    $stock_in1 = $get_stock['stock_terakhir'];
                    $stock_out1 = 0;

                    $selisih = $get_opname['qty_fisik'] - $get_stock['stock_terakhir'];

                    if ($selisih >= 0) {
                        $stock_in2 = $selisih;
                        $stock_out2 = 0;
                    } else {
                        $stock_in2 = 0;
                        $stock_out2 = $selisih * -1;
                    }
                }

                /*$stock_terakhir = array(
                    'kd_trx' => $get_opname['kd_trx'],
                    'tgl' => $get_opname['tgl'],
                    'kd_brg' => $get_opname['kd_brg'],
                    'saldo_awal' => 0,
                    'stock_in' => $stock_in1,
                    'stock_out' => $stock_out1,
                    'lokasi' => $get_opname['lokasi'],
                    'keterangan' => 'Stock Terakhir',
                    'hrg_beli' => $get_opname['hrg_beli']
                );
                $this->m_crud->create_data("Kartu_stock", $stock_terakhir);*/

                $penyesuaian = array(
                    'kd_trx' => $get_opname['kd_trx'],
                    'tgl' => $get_opname['tgl'],
                    'kd_brg' => $get_opname['kd_brg'],
                    'saldo_awal' => 0,
                    'stock_in' => $stock_in2,
                    'stock_out' => $stock_out2,
                    'lokasi' => $get_opname['lokasi'],
                    'keterangan' => 'Penyesuaian Opname',
                    'hrg_beli' => $get_opname['hrg_beli']
                );
                $this->m_crud->create_data("Kartu_stock", $penyesuaian);

                $this->m_crud->update_data("OPNAME", array('status' => '1'), "kd_trx = '" . $get_opname['kd_trx'] . "'");
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            echo 'failed';
        } else {
            $this->db->trans_commit();

            echo 'success';
        }
    }

    public function list_barang_opname($tipe=null, $lokasi_=null, $selisih_=null, $tgl_periode_=null, $search_=null) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '1024M');
        $lokasi = $_POST['lokasi_'];
        $date = $_POST['tgl_periode_'];

        if ($tgl_periode_ != null) {
            $date = base64_decode($tgl_periode_);
        }

        if ($lokasi_ != null) {
            $lokasi = base64_decode($lokasi_);
        }

        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);

        $get_barang_opname = $this->m_crud->get_data("OPNAME","LEFT(CONVERT(VARCHAR, tgl, 120), 10) tgl", "lokasi='".$lokasi."' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) BETWEEN '".$date1."' AND '".$date2."'", "tgl DESC", null, 1);

        if ($get_barang_opname != null) {
            $date_stock = $get_barang_opname['tgl'];
            $where = "br.kel_brg=kb.kel_brg AND br.kd_brg IN (SELECT kd_brg FROM Kartu_stock WHERE lokasi='" . $lokasi . "' AND keterangan <> 'Input Barang') AND kd_brg NOT IN (SELECT kd_brg FROM OPNAME WHERE lokasi='" . $lokasi . "' AND LEFT(CONVERT(VARCHAR, tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "' GROUP BY kd_brg)";

            if ($search_ != null) {
                $search = base64_decode($search_);
                $where .= " AND (br.kd_brg like '%" . $search . "%' or br.barcode like '%" . $search . "%' or br.nm_brg like '%" . $search . "%' or kb.nm_kel_brg like '%" . $search . "%')";
            }

            if ($selisih_ != null) {
                $selisih = base64_decode($selisih_);
                if ($selisih == '<') {
                    $q_selisih = "(SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE LEFT(CONVERT(VARCHAR, tgl, 120), 10) <= '".$date_stock."' AND lokasi='" . $lokasi . "' AND kd_brg=br.kd_brg)<0";
                    $group = "br.kd_brg, br.barcode, br.nm_brg, kb.nm_kel_brg";
                } else if ($selisih == '>') {
                    $q_selisih = "(SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE LEFT(CONVERT(VARCHAR, tgl, 120), 10) <= '".$date_stock."' AND lokasi='" . $lokasi . "' AND kd_brg=br.kd_brg)>0";
                    $group = "br.kd_brg, br.barcode, br.nm_brg, kb.nm_kel_brg";
                } else if ($selisih == '=') {
                    $q_selisih = "(SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE LEFT(CONVERT(VARCHAR, tgl, 120), 10) <= '".$date_stock."' AND lokasi='" . $lokasi . "' AND kd_brg=br.kd_brg)=0";
                    $group = "br.kd_brg, br.barcode, br.nm_brg, kb.nm_kel_brg";
                } else {
                    $q_selisih = null;
                    $group = null;
                }
            }

            $stock_sistem = "isnull((SELECT SUM(stock_in-stock_out) FROM Kartu_stock WHERE LEFT(CONVERT(VARCHAR, tgl, 120), 10) <= '".$date_stock."' AND lokasi='" . $lokasi . "' AND kd_brg=br.kd_brg),0) stock_sistem";
            $read_data = $this->m_crud->read_data("barang br, kel_brg kb", "br.kd_brg, br.barcode, br.nm_brg, kb.nm_kel_brg, " . $stock_sistem, $where, "br.kd_brg ASC", $group, 0, 0, $q_selisih);

            $no = 1;
            $list_barang = '';
            foreach ($read_data as $row) {
                $list_barang .= '
                    <tr>
                        <td>' . $no . '</td>
                        <td>' . $row['kd_brg'] . '</td>
                        <td>' . $row['barcode'] . '</td>
                        <td>' . $row['nm_brg'] . '</td>
                        <td>' . $row['nm_kel_brg'] . '</td>
                        <td>' . (float)$row['stock_sistem'] . '</td>
                    </tr>
                    ';
                $no++;
            }
            $list_barang .= "<input type='hidden' id='param_opname' value='1'>";

            if ($tipe == 'excel' && $lokasi_ != null) {
                $baca = $read_data;

                $header = array(
                    'merge' => array('A1:E1', 'A2:E2', 'A3:E3'),
                    'auto_size' => true,
                    'font' => array(
                        'A1:A2' => array('bold' => true, 'color' => array('rgb' => '000000'), 'size' => 12, 'name' => 'Verdana'),
                        'A3' => array('bold' => true, 'name' => 'Verdana'),
                        'A5:E5' => array('bold' => true, 'size' => 9, 'name' => 'Verdana')
                    ),
                    'alignment' => array('A1:A3' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)),
                    '1' => array('A' => 'Back Office'),
                    '2' => array('A' => 'Barang Belum Di Opname'),
                    '3' => array('A' => $lokasi),
                    '5' => array(
                        'A' => 'Kode Barang', 'B' => 'Barcode', 'C' => 'Nama Barang', 'D' => 'Kel. Barang', 'E' => 'Stock Sistem'
                    )
                );

                $i = 0;
                $stock = 0;
                foreach ($baca as $row => $value) {
                    $i++;
                    $body[$row] = array(
                        $value['kd_brg'], $value['barcode'], $value['nm_brg'], $value['nm_kel_brg'], (float)$value['stock_sistem']
                    );
                    $stock = $stock + (float)$value['stock_sistem'];
                }

                $body[$i] = array('TOTAL', '', '', '', $stock);
                array_push($header['merge'], 'A' . ($i + 6) . ':D' . ($i + 6) . '');
                $header['font']['A' . ($i + 6) . ':E' . ($i + 6) . ''] = array('bold' => true, 'size' => 9, 'name' => 'Verdana');

                $this->m_export_file->to_excel(str_replace(' ', '_', 'Barang Belum Di Opname'), $header, $body);
            }
        } else {
            $list_barang = "<tr><td style='text-align: center' colspan='6'>Tidak Ada Opname di Periode Ini.</td></tr>";
            $list_barang .= "<input type='hidden' id='param_opname' value='0'>";
        }

        echo $list_barang;
    }

    public function packing_report($action = null, $id = null){
        $this->access_denied(115);
        $data = $this->data;
        $function = 'packing_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Packing';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = "mm.no_faktur_mutasi=mp.no_faktur_mutasi AND mp.kd_packing=dp.kd_packing";
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'status' => $_POST['status']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $status = $this->session->search['status'];
        $explode_date = explode(' - ', $date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
			$tgl_awal=$date1; $tgl_akhir=$date2;
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mp.tgl_packing, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
			$tgl_awal=date('Y-m-d'); $tgl_akhir=date('Y-m-d');
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mp.tgl_packing, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($status)&&$status!=null){ 
			($where==null)?null:$where.=" and "; 
			if($status=='0P'){
				$where.="(mp.status = '0' and isnull((select top 1 kd_packing from det_expedisi where kd_packing=mp.kd_packing),'0') = '0')"; 
			} else if($status=='0S'){
				$where.="(mp.status = '0' and isnull((select top 1 kd_packing from det_expedisi where kd_packing=mp.kd_packing),'0') <> '0')"; 
			} else {
				$where.="(mp.status = '".$status."')"; 
			}
		}
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="mm.kd_lokasi_2 = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mp.kd_packing like '%".$search."%' or dp.kd_brg like '%".$search."%' or mm.kd_lokasi_1 like '%".$search."%' or mm.kd_lokasi_2 like '%".$search."%' or mp.operator like '%".$search."%' or mp.pengirim like '%".$search."%' or mm.no_faktur_mutasi like '%".$search."%')"; }
		
        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_over("Master_Mutasi mm, master_packing mp, det_packing dp", 'mp.kd_packing', ($where==null?'':$where), null, "mp.kd_packing");
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
        $data['report'] = $this->m_crud->select_limit('Master_Mutasi mm, master_packing mp, det_packing dp', "isnull((select dn.no_faktur_beli from master_delivery_note dn where dn.no_delivery_note=mm.no_faktur_beli),'-') no_faktur_beli, mp.tgl_packing, mp.kd_packing, mp.status, mp.pengirim, mp.penerima, mp.operator, mm.no_faktur_mutasi, mm.kd_lokasi_1, mm.kd_lokasi_2, isnull((select top 1 kd_packing from det_expedisi where kd_packing=mp.kd_packing),'0') expedisi", ($where==null?'':$where), 'mp.kd_packing desc', "mp.tgl_packing, mp.kd_packing, mp.status, mp.pengirim, mp.penerima, mp.operator, mm.no_faktur_mutasi, mm.kd_lokasi_1, mm.kd_lokasi_2, mm.no_faktur_beli", ($page-1)*$config['per_page']+1, ($config['per_page']*$page));
        if(isset($_POST['to_excel'])){
            $data['det_report'] = $this->m_crud->read_data('Master_Mutasi mm, master_packing mp, det_packing dp', "isnull((select dn.no_faktur_beli from master_delivery_note dn where dn.no_delivery_note=mm.no_faktur_beli),'-') no_faktur_beli, mp.tgl_packing, mp.kd_packing, mp.status, mp.pengirim, mp.penerima, mp.operator, mm.no_faktur_mutasi, mm.kd_lokasi_1, mm.kd_lokasi_2, isnull((select top 1 kd_packing from det_expedisi where kd_packing=mp.kd_packing),'0') expedisi", ($where==null?'':$where), 'mp.kd_packing desc', "mp.tgl_packing, mp.kd_packing, mp.status, mp.pengirim, mp.penerima, mp.operator, mm.no_faktur_mutasi, mm.kd_lokasi_1, mm.kd_lokasi_2, mm.no_faktur_beli");
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
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Kode Packing', 'C'=>'Faktur Mutasi', 'D'=>'Faktur Pembelian', 'E'=>'Lokasi Asal', 'F'=>'Lokasi Tujuan', 'G'=>'Pengirim', 'H'=>'Operator', 'I'=>'Status'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['tgl_packing'], $value['kd_packing'], $value['no_faktur_mutasi'], $value['no_faktur_beli'], $value['kd_lokasi_1'], $value['kd_lokasi_2'], $value['pengirim'], $value['operator'], 
					($value['status']==0?($value['expedisi']=='0'?'Processing':'Sending'):($value['status']==1?'Received In Part':'Received'))
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_data('master_packing mp, Master_Mutasi mm', "mp.tgl_packing, mp.kd_packing, mp.pengirim, mp.operator, mm.no_faktur_mutasi, mm.kd_lokasi_1, mm.kd_lokasi_2", "mm.no_faktur_mutasi=mp.no_faktur_mutasi AND mp.kd_packing = '".$id."'");
            $data['report_detail'] = $this->m_crud->read_data("det_packing dp, barang br","dp.qty, br.kd_brg, br.barcode, br.nm_brg, (SELECT hrg_jual FROM Det_Mutasi WHERE no_faktur_mutasi='".$data['report']['no_faktur_mutasi']."' AND kd_brg=br.kd_brg GROUP BY hrg_jual) hrg_jual", "dp.kd_brg=br.kd_brg AND dp.kd_packing = '".$id."'");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header =
                '<div class="row"><img style="float: right; margin-top: -10px" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['kd_packing'].'"></div>'.
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Packing Barang</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="18%"></th>
                                <th width="2%"></th>
                                <th width="30%"></th>
                                
                                <th width="7%"></th>
                                <th width="18%"></th>
                                <th width="2%"></th>
                                <th width="22%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>Tanggal</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_packing'], 0, 10).'</td>
                                <td></td>
                                <td><b>Faktur Mutasi</b></td>
                                <td></td>
                                <td>'.$data['report']['no_faktur_mutasi'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Kode Packing</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_packing'].'</td>
                                <td></td>
                                <td><b>Pengirim</b></td>
                                <td></td>
                                <td>'.$data['report']['pengirim'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi Asal</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_lokasi_1'].'</td>
                                <td></td>
                                <td><b>Operator</b></td>
                                <td></td>
                                <td>'.$this->m_website->get_nama_user($data['report']['operator']).'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi Tujuan</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_lokasi_2'].'</td>
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
                'mode'=>'utf-8','paper'=>array(210,297),'font_size'=>10,'font_family'=>null,
                'left'=>10,'right'=>10,'top'=>52,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function order_report($action = null, $id = null){
        $this->access_denied(118);
        $data = $this->data;
        $function = 'order_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Order Cabang';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'status' => $_POST['status']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $status = $this->session->search['status'];
        $explode_date =  explode(' - ',$date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mo.tgl_order, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mo.tgl_order, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($status)&&$status!=null){ ($where==null)?null:$where.=" and "; $where.="(mo.status = '".$status."')"; }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="mo.lokasi = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mo.no_order like '%".$search."%' or ud.nama like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_join("master_order mo", 'mo.no_order', array('lokasi l', 'user_detail ud'), array('l.kode=mo.lokasi', 'ud.user_id=mo.kd_kasir'), ($where==null?'':$where));
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
        $data['report'] = $this->m_crud->select_limit_join('master_order mo', "mo.no_order, mo.tgl_order, mo.status, l.nama nama_lokasi, ud.nama nama_kasir", array('lokasi l', 'user_detail ud'), array('l.kode=mo.lokasi', 'ud.user_id=mo.kd_kasir'), ($where==null?'':$where), 'mo.no_order desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $data['det_report'] = $this->m_crud->join_data('master_order mo', "mo.no_order, mo.tgl_order, mo.status, l.nama nama_lokasi, ud.nama nama_kasir", array('lokasi l', 'user_detail ud'), array('l.kode=mo.lokasi', 'ud.user_id=mo.kd_kasir'), ($where==null?'':$where), 'mo.no_order desc');
            $baca = $data['det_report'];
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
                    'A'=>'Tanggal', 'B'=>'No. Order', 'C'=>'Lokasi Asal', 'D'=>'Operator', 'E'=>'Status'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['tgl_order'], $value['no_order'], $value['nama_lokasi'], $value['nama_operator'], ($value['status']==0?'Belum Diproses':'Selesai')
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function approval_order_report($action = null, $id = null){
        $this->access_denied(119);
        $data = $this->data;
        $function = 'approval_order_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Approve Order';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'status' => $_POST['status']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $status = $this->session->search['status'];
        $explode_date =  explode(' - ',$date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mro.tgl_receive_order, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, mro.tgl_receive_order, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="l.kode = '".$lokasi."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(mro.no_receive_order like '%".$search."%' or mro.no_order like '%".$search."%' or mro.no_faktur_mutasi like '%".$search."%' or ud.user_id like '%".$search."%')"; }

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_join("master_receive_order mro", 'mro.no_order', array('master_order mo', 'lokasi l', 'user_detail ud'), array('mo.no_order=mro.no_order', 'l.kode=mo.lokasi', 'ud.user_id=mro.operator'), ($where==null?'':$where));
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
        $count_packing = ",(select count(mp.kd_packing) from master_packing mp where mp.no_faktur_mutasi=mro.no_faktur_mutasi) status_mutasi";
        $data['report'] = $this->m_crud->select_limit_join('master_receive_order mro', "mro.tgl_receive_order, mro.no_receive_order, mro.no_faktur_mutasi, mo.no_order, l.nama nama_lokasi, ud.nama nama_operator".$count_packing, array('master_order mo', 'lokasi l', 'user_detail ud'), array('mo.no_order=mro.no_order', 'l.kode=mo.lokasi', 'ud.user_id=mro.operator'), ($where==null?'':$where), 'mro.no_receive_order desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $data['det_report'] = $this->m_crud->join_data('master_receive_order mro', "mro.tgl_receive_order, mro.no_receive_order, mro.no_faktur_mutasi, mo.no_order, l.nama nama_lokasi, ud.nama nama_operator", array('master_order mo', 'lokasi l', 'user_detail ud'), array('mo.no_order=mro.no_order', 'l.kode=mo.lokasi', 'ud.user_id=mro.operator'), ($where==null?'':$where), 'mro.no_receive_order desc');
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
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'No. Receive Order', 'C'=>'No. Order', 'D'=>'No. Alokasi', 'E'=>'Lokasi Asal', 'F'=>'Operator'
                )
            );

            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['tgl_receive_order'], $value['no_receive_order'], $value['no_order'], $value['no_mutasi'], $value['nama_lokasi'], $value['nama_operator']
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_join_data('master_receive_order mro', "mro.tgl_receive_order, mro.no_receive_order, mro.no_faktur_mutasi, mo.no_order, l.nama nama_lokasi, ud.nama nama_operator", array('master_order mo', 'lokasi l', 'user_detail ud'), array('mo.no_order=mro.no_order', 'l.kode=mo.lokasi', 'ud.user_id=mro.operator'), "mro.no_receive_order = '".$id."'");
            $data['report_detail'] = $this->m_crud->join_data('det_order do', 'br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, ISNULL(dro.qty, 0) qty_approve, do.qty qty_order', array('barang br', array('table'=>'master_receive_order mro','type'=>'LEFT'), array('table'=>'det_receive_order dro','type'=>'LEFT')), array('br.kd_brg=do.kd_brg','mro.no_order=do.no_order','dro.no_receive_order=mro.no_receive_order and dro.kd_brg=do.kd_brg'), "mro.no_receive_order = '".$id."'");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header =
                '<div class="row"><img style="float: right; margin-top: -10px" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['no_receive_order'].'"></div>'.
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Alokasi By Cabang</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table cellpadding="1" width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="20%"></th>
                                <th width="2%"></th>
                                <th width="30%"></th>
                                
                                <th width="8%"></th>
                                <th width="12%"></th>
                                <th width="2%"></th>
                                <th width="25%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>Tanggal</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_receive_order'], 0, 10).'</td>
                                <td></td>
                                <td><b>Lokasi</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['nama_lokasi'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>No. Approve Order</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['no_receive_order'].'</td>
                                <td></td>
                                <td><b>Operator</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['nama_operator'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>No. Order</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['no_order'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>No. Alokasi</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['no_faktur_mutasi'].'</td>
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
                'mode'=>'utf-8','paper'=>array(210,297),'font_size'=>10,'font_family'=>'sans-serif',
                'left'=>10,'right'=>10,'top'=>52,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function delete_receive_order($kode) {
        $kode = base64_decode($kode);

        $this->db->trans_begin();
        $get_data_receive = $this->m_crud->get_data("master_receive_order", "no_order, no_faktur_mutasi", "no_receive_order = '".$kode."'");
        $this->m_crud->update_data("master_order", array('status'=>0), "no_order = '".$get_data_receive['no_order']."'");
        $this->m_crud->delete_data("master_mutasi", "no_faktur_mutasi = '".$get_data_receive['no_faktur_mutasi']."'");
        $this->m_crud->delete_data("det_mutasi", "no_faktur_mutasi = '".$get_data_receive['no_faktur_mutasi']."'");
        $this->m_crud->delete_data("master_receive_order", "no_receive_order = '".$kode."'");
        $this->m_crud->delete_data("det_receive_order", "no_receive_order = '".$kode."'");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        } else {
            $this->db->trans_commit();
            echo true;
        }
    }

    public function expedisi_report($action = null, $id = null){
        $this->access_denied(120);
        $data = $this->data;
        $function = 'expedisi_report';
        $view = $this->control . '/';
        if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->session->unset_userdata('search'); $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $table = null;
        //if($this->session->userdata($this->site . 'admin_menu')!=$function){ $this->cart->destroy(); $this->session->set_userdata($this->site . 'admin_menu', $function); }
        $data['title'] = 'Laporan Expedisi';
        $data['page'] = $function; //$this->session->userdata($this->site . 'admin_menu');
        $data['content'] = $view.$function;
        $data['table'] = $table;

        $where = null;
        $tgl_awal = date('Y-m-d'); $tgl_akhir = date('Y-m-d');
        if(isset($_POST['search'])||isset($_POST['to_excel'])){
            $this->session->set_userdata('search', array('any' => $_POST['any'], 'field-date'=>$_POST['field-date'], 'lokasi' => $_POST['lokasi'], 'status' => $_POST['status']));
        }

        $search = $this->session->search['any']; $lokasi = $this->session->search['lokasi']; $date = $this->session->search['field-date']; $status = $this->session->search['status'];
        $explode_date =  explode(' - ',$date);
        $date1 = str_replace('/','-',$explode_date[0]);
        $date2 = str_replace('/','-',$explode_date[1]);
        if (isset($date) && $date != null) {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, me.tgl_expedisi, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        } else {
            ($where == null) ? null : $where .= " and ";
            $where .= "LEFT(CONVERT(VARCHAR, me.tgl_expedisi, 120), 10) BETWEEN '" . date('Y-m-d') . "' AND '" . date('Y-m-d') . "'";
        }
        if(isset($lokasi)&&$lokasi!=null){ ($where==null)?null:$where.=" and "; $where.="l2.kode = '".$lokasi."'"; }
        if(isset($status)&&$status!=null){ ($where==null)?null:$where.=" and "; $where.="me.status = '".$status."'"; }
        if(isset($search)&&$search!=null){ ($where==null)?null:$where.=" and "; $where.="(me.kd_expedisi like '%".$search."%' or me.pengirim like '%".$search."%' or ud.user_id like '%".$search."%')"; } 

        $page = ($id==null?1:$id);
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data_join("master_expedisi me", 'me.kd_expedisi', array('lokasi l1', 'lokasi l2', 'user_detail ud'), array('l1.kode=me.lokasi_asal', 'l2.kode=me.lokasi_tujuan', 'ud.user_id=me.operator'), ($where==null?'':$where));
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

        $data['report'] = $this->m_crud->select_limit_join('master_expedisi me', "me.status, me.kd_expedisi, me.tgl_expedisi, me.pengirim, l1.nama nama_lokasi_asal, l2.nama nama_lokasi_tujuan, ud.nama nama_operator", array('lokasi l1', 'lokasi l2', 'user_detail ud'), array('l1.kode=me.lokasi_asal', 'l2.kode=me.lokasi_tujuan', 'ud.user_id=me.operator'), ($where==null?'':$where), 'me.kd_expedisi desc', null, ($page-1)*$config['per_page']+1, ($config['per_page']*$page));

        if(isset($_POST['to_excel'])){
            $data['det_report'] = $this->m_crud->join_data('master_expedisi me', "me.status, me.kd_expedisi, me.tgl_expedisi, me.pengirim, l1.nama nama_lokasi_asal, l2.nama nama_lokasi_tujuan, ud.nama nama_operator", array('lokasi l1', 'lokasi l2', 'user_detail ud'), array('l1.kode=me.lokasi_asal', 'l2.kode=me.lokasi_tujuan', 'ud.user_id=me.operator'), ($where==null?'':$where), 'me.kd_expedisi desc');
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
                '3' => array('A' => $tgl_awal.' - '.$tgl_akhir),
                '5' => array(
                    'A'=>'Tanggal', 'B'=>'Kode Expedisi', 'C'=>'Lokasi Asal', 'D'=>'Lokasi Tujuan', 'E'=>'Pengirim', 'F'=>'Operator', 'G'=>'Status'
                )
            );
			
			$status = array('0'=>'Sending', '1'=>'Received in Part', '2'=>'Received');
            foreach($baca as $row => $value){
                $body[$row] = array(
                    $value['tgl_expedisi'], $value['kd_expedisi'], $value['nama_lokasi_asal'], $value['nama_lokasi_tujuan'], $value['pengirim'], $value['nama_operator'], $status[$value['status']]
                );
            }

            $this->m_export_file->to_excel(str_replace(' ', '_', $data['title']), $header, $body);
        }

        if(($action=='download'||$action=='print') && (isset($_GET['trx']) || $id!=null)){
            isset($_GET['trx']) ? $id=$_GET['trx'] : $id=base64_decode($id);
            $data['content'] = $view.'pdf_invoice_'.$function;
            $data['report'] = $this->m_crud->get_join_data('master_expedisi me', "me.kd_expedisi, me.tgl_expedisi, me.pengirim, l1.nama nama_lokasi_asal, l2.nama nama_lokasi_tujuan, ud.nama nama_operator", array('lokasi l1', 'lokasi l2', 'user_detail ud'), array('l1.kode=me.lokasi_asal', 'l2.kode=me.lokasi_tujuan', 'ud.user_id=me.operator'), "me.kd_expedisi = '".$id."'");
            $data['report_detail'] = $this->m_crud->join_data('det_expedisi de', 'de.*, mp.no_faktur_mutasi', array('master_packing mp'), array('mp.kd_packing=de.kd_packing'), "de.kd_expedisi = '".$id."'");

            $t_row = count($data['report_detail']);
            //$t_row = $t_row + 23;
            $data['row_per_page'] = 30;
            $data['row_one_page'] = 25;
            ($action=='download')?($method='D'):($method='I');
            //$method='I';
            $file = str_replace('/', '-', str_replace(' ', '_', $data['title']).'-'.str_replace('/', '-', $data['report']['no_faktur_mutasi']));
            $html = $this->load->view('bo/'.$data['content'], $data, true);

            $header =
                '<div class="row"><img style="float: right; margin-top: -10px" src="'.base_url().'barcode.php?size=30&sizefactor=2&text='.$data['report']['kd_expedisi'].'"></div>'.
                $this->m_website->logo(null, "1cm").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Alokasi By Cabang</b><br/><font style="font-size:10px;">'.$this->m_website->address().'</font>'.
                '<div style="margin-bottom: 10px;">
                    <table cellpadding="1" width="100%">
                        <thead>
                            <tr>
                                <th width="2%"></th>
                                <th width="18%"></th>
                                <th width="2%"></th>
                                <th width="30%"></th>
                                
                                <th width="10%"></th>
                                <th width="12%"></th>
                                <th width="2%"></th>
                                <th width="25%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td><b>Tanggal</b></td>
                                <td><b>:</b></td>
                                <td>'.substr($data['report']['tgl_expedisi'], 0, 10).'</td>
                                <td></td>
                                <td><b>Pengirim</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['pengirim'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Kode Expedisi</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['kd_expedisi'].'</td>
                                <td></td>
                                <td><b>Operator</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['nama_operator'].'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi Asal</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['nama_lokasi_asal'].'</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><b>Lokasi Tujuan</b></td>
                                <td><b>:</b></td>
                                <td>'.$data['report']['nama_lokasi_tujuan'].'</td>
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
                'mode'=>'utf-8','paper'=>array(210,297),'font_size'=>10,'font_family'=>'sans-serif',
                'left'=>10,'right'=>10,'top'=>52,'bottom'=>(($t_row>$data['row_one_page'])?35:10),'header'=>5,'footer'=>5
            );
            $this->m_export_file->to_pdf($method, $file, $html, $header, $footer, $conf);

        }

        if($this->form_validation->run() == false){ $this->load->view('bo/index', $data); }
        else { $this->load->view('bo/index', $data); }
    }

    public function delete_expedisi($kode) {
        $kode = base64_decode($kode);

        $this->db->trans_begin();
        $get_data_receive = $this->m_crud->get_data("master_receive_order", "no_order, no_faktur_mutasi", "no_receive_order = '".$kode."'");
        $this->m_crud->update_data("master_order", array('status'=>0), "no_order = '".$get_data_receive['no_order']."'");
        $this->m_crud->delete_data("master_mutasi", "no_faktur_mutasi = '".$get_data_receive['no_faktur_mutasi']."'");
        $this->m_crud->delete_data("det_mutasi", "no_faktur_mutasi = '".$get_data_receive['no_faktur_mutasi']."'");
        $this->m_crud->delete_data("master_receive_order", "no_receive_order = '".$kode."'");
        $this->m_crud->delete_data("det_receive_order", "no_receive_order = '".$kode."'");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo false;
        } else {
            $this->db->trans_commit();
            echo true;
        }
    }

	/*End modul report*/
}

