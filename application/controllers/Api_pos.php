<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_pos extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '2048M');
        ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

        $this->setting = $this->m_website->setting();

        $this->poin_setting = json_decode($this->setting->poin_setting, true);
    }

    public function cek_koneksi() {
        echo json_encode(array('status'=>true));
    }

    /*API RKB*/
    public function get_data($table, $perpage=10, $page=1) {
        $action = null;
        if(substr($table,0,6)=='return') {
            $table = str_replace('return_','',$table);
            $action = 'return';
        }
		$customer = isset($_POST['kd_cust'])?$_POST['kd_cust']:'-';
        if (isset($_POST['lokasi'])) {
            $lokasi = $_POST['lokasi'];
        } else {
            $lokasi = null;
        }

        $start = ($page-1)*$perpage+1;
        $end = $perpage*$page;

        $select="*";
        $where=null;

        if ($table == 'barang') {
            if (isset($_POST['param']) && $_POST['param'] == 'edit') {
                $where = null;
            } else {
                $where = "kcp=''";
            }
        } else if ($table == 'customer') {
            $where = "kd_cust <> '1000001' AND status = '1'";
        }

        if(isset($_POST['where']) && $_POST['where']!=null){ $post_where=$_POST['where']; }

        if(isset($post_where) && $post_where!=null){ ($where==null)?null:$where.=" and "; $where.="(".$post_where.")"; }

        $order = array(
            'barang' => 'nm_brg asc',
            'customer' => 'nama asc',
            'master_kas_masuk' => 'nama asc',
            'master_kas_keluar' => 'nama asc',
            'kel_brg' => 'nm_kel_brg asc',
            'sales' => 'Nama asc',
            'bank' => 'Nama asc',
            'compliment' => 'nama asc',
            'kitchen_printer' => 'nama asc'
        );

        $get_data = $this->m_crud->select_limit($table, $select, $where, (isset($order[$table])?$order[$table]:null), null, $start, $end);

        if($action=='return'){
            if($get_data != null){
                $get_data = $this->m_website->tambah_data($table, $get_data, $lokasi,$customer);
                return json_encode(array('status'=>true, 'data'=>$get_data));
            } else {
                return json_encode(array('status'=>false));
            }
        } else {
            if($get_data != null){
                $get_data = $this->m_website->tambah_data($table, $get_data, $lokasi,$customer);
                echo json_encode(array('status'=>true, 'data'=>$get_data));
            } else {
                echo json_encode(array('status'=>false));
            }
        }
    }

    public function cek_setoran($kassa, $kasir, $lokasi, $tanggal) {
        return $this->m_crud->count_data("Setoran", "id", "kassa='".$kassa."' AND kd_kasir='".$kasir."' AND lokasi='".$lokasi."' AND left(convert(varchar, Tanggal, 120), 10)='".date('Y-m-d', strtotime($tanggal))."'");
    }

    public function login() {
        $response = array();
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $lokasi_login = $_POST['lokasi'];
        $kassa = $_POST['kassa'];

        $cek = $this->m_website->login($username, $password);
        if ($cek != false) {
            $cek = (array)$cek;
            $status = true;
            $pesan = 'Login Berhasil!';

            $cek_setoran = $this->cek_setoran($kassa, $cek['user_id'], $lokasi_login, $_POST['tgl']);
            $cek['setoran'] = $cek_setoran;
            if ($cek_setoran > 0) {
                $status = false;
                $pesan = 'Anda telah menutup toko pada sesi ini';
            }

            $get_data = $this->m_crud->get_data("master_trx", "kd_trx", "left(convert(varchar, tgl, 120), 10)='" . $_POST['tgl'] . "' and kassa='" . $kassa . "' and lokasi='" . $lokasi_login . "'", "kd_trx ASC");

            //$get_seting = $this->m_crud->get_data("lokasi", "nama header1, ket header2, kota header3, web header4, footer1, footer2, footer3, footer4", "kode='" . $cek['lokasi'] . "'");

            //$response['header_footer'] = $get_seting;

            if ($get_data != null) {
                $response['max_kode'] = $get_data['kd_trx'];
            } else {
                $response['max_kode'] = false;
            }

            $lokasi = json_decode($cek['lokasi'], true);

            $lokasi_in = array();
            foreach ($lokasi['lokasi_list'] as $item) {
                array_push($lokasi_in, '\''.$item['kode'].'\'');
            }

            $get_lokasi = $this->m_crud->read_data('lokasi', "kode, nama_toko nama, ket alamat, serial, nama header1, ket header2, kota header3, web header4, footer1, footer2, footer3, footer4", "kode in (".implode(',', $lokasi_in).")");

            $lokasi_list = array();
            $data_lokasi = array();
            foreach ($get_lokasi as $item) {
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
                array_push($lokasi_list, array(
                    'kode' => $item['kode'],
                    'nama' => $item['nama'],
                    'alamat' => $item['alamat'],
                    'serial' => $item['serial'],
                    'header_footer' => $head_foot
                ));
                array_push($data_lokasi, $item['kode']);
            }

            if ($lokasi_login != '') {
                if (in_array($lokasi_login, $data_lokasi)) {
                    $response['select_lokasi'] = false;
                } else {
                    $response['select_lokasi'] = true;
                }
            } else {
                $response['select_lokasi'] = true;
            }

            $cek['lokasi'] = $lokasi_list;

            $cek['foto'] = base_url().'assets/images/user-default.png';

            unset($cek['password']);

            $response['data'] = $cek;
            $response['status'] = $status;
            $response['pesan'] = $pesan;
        } else {
            $response['status'] = false;
            $response['pesan'] = 'Username atau Password Salah!';
        }

        echo json_encode($response);
    }

    public function get_lokasi() {
        $response = array();

        $user = $_POST['kd_kasir'];

        $get_data = $this->m_crud->get_data("user_akun", "lokasi", "user_id='".$user."'");

        if ($get_data != null) {
            $lokasi = json_decode($get_data['lokasi'], true);

            $lokasi_in = array();
            foreach ($lokasi['lokasi_list'] as $item) {
                array_push($lokasi_in, '\''.$item['kode'].'\'');
            }

            $get_lokasi = $this->m_crud->read_data('lokasi', "kode, nama_toko nama, ket alamat, serial, nama header1, ket header2, kota header3, web header4, footer1, footer2, footer3, footer4", "kode in (".implode(',', $lokasi_in).")");

            $lokasi_list = array();
            foreach ($get_lokasi as $item) {
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
                array_push($lokasi_list, array(
                    'kode' => $item['kode'],
                    'nama' => $item['nama'],
                    'alamat' => $item['alamat'],
                    'serial' => $item['serial'],
                    'header_footer' => $head_foot
                ));
            }

            $response['status'] = true;
            $response['data'] = $lokasi_list;
        } else {
            $response['status'] = false;
            $response['pesan'] = 'Data tidak tersedia';
        }

        echo json_encode($response);
    }

    public function kartu_stock($perpage=10, $page=1) {
        $response = array();
        $where = null;
        $lokasi = $_POST['lokasi'];
        $cari = $_POST['cari'];

        $start = ($page-1)*$perpage+1;
        $end = $perpage*$page;

        if (isset($lokasi) || $lokasi != '') {
            /*$where .= $where==null?'':' AND ';
            $where .= "ks.lokasi='".$lokasi."'";*/
            $on_lokasi = " AND ks.lokasi='".$lokasi."'";
        }

        if (isset($cari) || $cari != '') {
            $where .= $where==null?'':' AND ';
            $where .= "(br.barcode like '%".$cari."%' OR br.nm_brg like '%".$cari."%')";
        }

        $read_data = $this->m_website->tambah_data('hrg_ks', $this->m_crud->select_limit_join('barang br', "br.kd_brg, br.barcode, br.nm_brg, br.hrg_jual_1, br.satuan, isnull(sum(stock_in-stock_out), 0) stok", array(array('table'=>'Kartu_stock ks', 'type'=>'LEFT')), array("ks.kd_brg=br.kd_brg".$on_lokasi), $where, 'br.kd_brg ASC', "br.kd_brg, br.barcode, br.nm_brg, br.hrg_jual_1, br.satuan", $start, $end), $_POST['lokasi']);

        if ($read_data == null) {
            $response['status'] = false;
        } else {
            $response['status'] = true;
            $response['data'] = $read_data;
        }

        echo json_encode($response);
    }

    public function detail_stock($perpage=10, $page=1) {
        $response = array();

        $start = ($page-1)*$perpage+1;
        $end = $perpage*$page;

        $read_data = $this->m_crud->select_limit('kartu_stock ks', "kd_trx, left(convert(varchar, tgl, 120), 19) tgl, stock_in, stock_out, keterangan", "kd_brg='".$_POST['kd_brg']."' and lokasi='".$_POST['lokasi']."'", 'tgl desc', null, $start, $end);

        if ($read_data == null) {
            $response['status'] = false;
        } else {
            $response['status'] = true;
            $response['data'] = $read_data;
        }

        echo json_encode($response);
    }

public function activity($perpage=10, $page=1) {
        $response = array();
        $where = "mt.HR = 'S'";
        $where_kas = null;
        $param = $_POST['param'];
        $lokasi = $_POST['lokasi'];
        $kassa = $_POST['kassa'];
        $periode = $_POST['periode'];
        $cari = $_POST['cari'];

        $start = ($page-1)*$perpage+1;
        $end = $perpage*$page;

        if (isset($periode) && $periode != '') {
            $explode_date = explode('|', $periode);
            $date1 = str_replace('/', '-', $explode_date[0]);
            $date2 = str_replace('/', '-', $explode_date[1]);

            $where .= $where == null ? '' : ' AND ';
            $where .= "LEFT(CONVERT(VARCHAR, mt.tgl, 120), 10) BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
        }else{
            $where .= $where == null ? '' : ' AND ';
            $where .= "mt.tgl BETWEEN '".date('Y-m-d')."' AND '".date('Y-m-d')."'";
        }

        if (isset($cari) && $cari != '') {
            $where .= $where==null?'':' AND ';
            $where .= "(mt.kd_trx LIKE '%".$cari."%' OR cs.Nama LIKE '%".$cari."%')";

            $where_kas .= $where_kas==null?'':' AND ';
            $where_kas .= "(kd_trx LIKE '%".$cari."%' OR kasir LIKE '%".$cari."%' OR jenis_kas LIKE '%".$cari."%')";
        }

        if (isset($lokasi) && $lokasi != '') {
            $where .= $where==null?'':' AND ';
            $where .= "mt.Lokasi='".$lokasi."'";

            $where_kas .= $where_kas==null?'':' AND ';
            $where_kas .= "lokasi='".$lokasi."'";
        }

        if (isset($kassa) && $kassa != '') {
            $where .= $where==null?'':' AND ';
            $where .= "mt.kassa='".$kassa."'";

            $where_kas .= $where_kas==null?'':' AND ';
            $where_kas .= "kassa='".$kassa."'";
        }

        if ($param == 'kas') {
            $read_data = $this->m_crud->select_limit("arsip_kas", "*", $where_kas, "tgl desc", null, $start, $end);
        } else if ($param == 'transaksi') {
            $read_data = $this->m_crud->select_limit_join(
                'Master_Trx mt',
                "mt.kd_trx, mt.tgl, cs.Nama customer, isnull(sl.nama, 'UMUM') nama_waitres, ud.nama kasir, isnull(mt.dis_persen, 0) dis_persen, mt.dis_rp diskon_total, mt.bayar, mt.change, mt.jam, mt.status, mt.HR, mt.kassa, mt.ket_kas_lain, mt.Lokasi, mt.Jenis_trx, SUM(dt.qty * dt.hrg_jual) st, ISNULL(SUM(dt.dis_persen), 0) diskon, ISNULL(SUM(dt.tax), 0) tax, ISNULL(SUM(dt.service), 0) service, mt.compliment, mt.compliment_rp, mt.jml_kartu, mt.kartu, mt.jns_kartu, mt.no_kartu, mt.pemilik_kartu, mt.nominal_deposit, mt.poin_tukar, mt.nominal_poin, mt.no_po",
                array(
                    array("type"=>"LEFT","table"=>"Det_Trx dt"),
                    array("type"=>"LEFT","table"=>"Customer cs"),
                    array("type"=>"LEFT","table"=>"user_detail ud"),
                    array('type'=>'LEFT','table'=>'sales sl')
                ),
                array("mt.kd_trx=dt.kd_trx", "mt.kd_cust=cs.kd_cust", "mt.kd_kasir=ud.user_id", "mt.kd_sales=sl.kode"),
                $where,
                'convert(date, tgl) desc, convert(time, jam) desc',
                "mt.kd_trx, mt.tgl, mt.tempo, cs.Nama, ud.nama, mt.dis_rp, mt.dis_persen, mt.tax, mt.bayar, mt.change, mt.jam, mt.status, mt.HR, mt.kassa, mt.ket_kas_lain, mt.Lokasi, mt.Jenis_trx, mt.compliment, mt.compliment_rp, mt.jml_kartu, mt.kartu, mt.jns_kartu, mt.no_kartu, mt.pemilik_kartu, mt.nominal_deposit, mt.poin_tukar, mt.nominal_poin, mt.no_po, sl.nama",
                $start, $end
            );
        }

        if ($read_data == null) {
            $response['status'] = false;
        } else {
            if ($param == 'transaksi') {
                foreach ($read_data as $key => $item) {
                    $get_compliment = $this->m_crud->get_data("compliment", "nama", "compliment_id='".$item['compliment']."'");
                    $read_data[$key]['nama_compliment'] = ($get_compliment==null?'-':$get_compliment['nama']);
                    $read_data[$key]['tgl_formated'] = date('Y-m-d', strtotime($item['tgl'])) . ' ' . substr($item['jam'], 11, 8);
                    $read_data[$key]['jam_formated'] = substr($item['jam'], 11, 8);
                    $read_data[$key]['gt'] = $item['st'] - $item['diskon_total'] + $item['tax'] + $item['service'];
                    $read_data[$key]['barang'] = $this->m_website->tambah_data('brg_act', $this->m_crud->read_data("Det_Trx dt, barang br", "dbo.TRIM(dt.kd_brg) kd_brg, ISNULL(dt.tax, 0) tax, ISNULL(dt.dis_persen, 0) diskon_item, ISNULL(dt.service, 0) service, dt.hrg_beli, dt.hrg_jual, dt.qty, dbo.TRIM(br.barcode) barcode, dbo.TRIM(br.nm_brg) nm_brg, br.satuan, br.gambar, br.kategori", "dt.kd_brg=br.kd_brg AND dt.kd_trx = '" . $item['kd_trx'] . "'"));
                    $no_po = json_decode($item['no_po'], true);
                    $no_meja = '';
                    if (count($no_po) > 0) {
                        $no_meja = $this->m_crud->get_data("master_to", "no_meja", "no_to='".$no_po[0]['kd_trx']."'")['no_meja'];
                    }
                    $read_data[$key]['no_meja'] = $no_meja;

                    $to = json_decode($item['no_po'], true);
                    $jml_tamu = 0;
                    if (count($to) > 0) {
                        foreach ($to as $item) {
                            $get_to = $this->m_crud->get_data("master_to", "jml_tamu", "no_to='".$item['kd_trx']."'");
                            $jml_tamu = $jml_tamu + (int)$get_to['jml_tamu'];
                        }
                    } else {
                        $jml_tamu = 1;
                    }
                    $read_data[$key]['jml_tamu'] = $jml_tamu;
                }
            } else if ($param == 'kas') {
                foreach ($read_data as $key => $item) {
                    $read_data[$key]['tgl_formated'] = date('Y-m-d', strtotime($item['tgl']));
                }
            }

            $response['status'] = true;
            $response['data'] = $read_data;
        }

        echo json_encode($response);
    }
    public function edit_master() {
        $response = array();
        $where = null;
        $table = $_POST['table'];
        $id = $_POST['id'];

        $condition = array(
            'barang' => "kd_brg='".$id."'",
            'kel_brg' => "kel_brg='".$id."'",
            'customer' => "kd_cust='".$id."'"
        );

        $where .= $where==null?'':' AND ';
        $where .= $condition[$table];

        $get_data =  $this->m_crud->get_data($table, "*", $where);

        if ($get_data != null) {
            $response['status'] = true;
            $response['data'] = $get_data;
        } else {
            $response['status'] = false;
            $response['pesan'] = 'Data tidak tersedia!';
        }

        echo json_encode($response);
    }

            public function simpan_tr() {
                $response = array();

                $this->db->trans_begin();

                $data = json_decode($_POST['data'], true);

                $kode_bill = $data['join'];
                $data_split = $data['split'];
                $master = $data['master'];
                $detail = $data['detail'];

                if (count($kode_bill) > 0) {
                    foreach ($kode_bill as $item) {
                        if(array_search($item['kd_trx'], array_column($data_split, 'kd_trx')) === false) {
                            $this->m_crud->update_data("master_to", array('status' => 'S'), "no_to='" . $item['kd_trx'] . "'");
                            $this->m_crud->update_data("detail_to", array('status' => 'S'), "no_to='" . $item['kd_trx'] . "' and status='P'");
                        } else {
                            $this->m_crud->update_data("detail_to", array('status' => 'R'), "no_to='" . $item['kd_trx'] . "' and status='P'");
                        }
                    }
                }

                if (count($data_split) > 0) {
                    foreach ($data_split as $item) {
                        $kode = $item['kd_trx'];
                        $get_bill = $this->m_crud->get_data("detail_to", "sum(qty) qty, hrg_jual, kcp", "no_to='".$kode."' and kd_brg='".$item['kd_brg']."' and status='R'", null, "hrg_jual, kcp");
                        $qty_success = (int)$get_bill['qty']-(int)$item['qty'];

                        $this->m_crud->update_data("detail_to", array('status'=>'C', 'ket_void'=>'Split Bill'), "no_to='".$kode."' and kd_brg='".$item['kd_brg']."' and status='R'");

                        $max_urutan = $this->m_crud->get_data("detail_to", "max(urutan) urutan", "no_to='".$kode."'")['urutan'];
                        $urutan = $max_urutan+1;

                        if ($get_bill['hrg_jual'] == null) {
                            $get_harga = $this->m_crud->get_join_data("barang br", "br.hrg_jual_1, brh.hrg_jual_1 hrg_jual_l", array(array('table'=>'barang_hrg brh', 'type'=>'LEFT')), array("brh.barang=br.kd_brg and brh.lokasi = '".$this->config->item('lokasi')."'"), "br.kd_brg='".$item['kd_brg']."'");
                            if ($get_harga['hrg_jual_l'] == null) {
                                $hrg_jual = $get_harga['hrg_jual_1'];
                            } else {
                                $hrg_jual = $get_harga['hrg_jual_l'];
                            }
                        } else {
                            $hrg_jual = $get_bill['hrg_jual'];
                        }

                        if ($qty_success > 0) {
                            $this->m_crud->create_data("detail_to", array(
                                'no_to' => $kode,
                                'tanggal' => date('Y-m-d'),
                                'waktu' => date('H:i:s'),
                                'kd_brg' => $item['kd_brg'],
                                'qty' => (int)$qty_success,
                                'hrg_jual' => $hrg_jual,
                                'ket' => 'Split Bill',
                                'status' => 'S',
                                'kcp' => $get_bill['kcp'],
                                'atas_nama' => 'Split Bill',
                                'urutan' => $urutan,
                                'status_kcp' => '1'
                            ));
                            $this->m_crud->create_data("detail_to", array(
                                'no_to' => $kode,
                                'tanggal' => date('Y-m-d'),
                                'waktu' => date('H:i:s'),
                                'kd_brg' => $item['kd_brg'],
                                'qty' => (int)$item['qty'],
                                'hrg_jual' => $hrg_jual,
                                'ket' => 'Split Bill',
                                'status' => 'P',
                                'kcp' => $get_bill['kcp'],
                                'atas_nama' => 'Split Bill',
                                'urutan' => $urutan+1,
                                'status_kcp' => '1'
                            ));
                        } else {
                            $this->m_crud->create_data("detail_to", array(
                                'no_to' => $kode,
                                'tanggal' => date('Y-m-d'),
                                'waktu' => date('H:i:s'),
                                'kd_brg' => $item['kd_brg'],
                                'qty' => (int)$item['qty'],
                                'hrg_jual' => $hrg_jual,
                                'ket' => 'Split Bill',
                                'status' => 'P',
                                'kcp' => $get_bill['kcp'],
                                'atas_nama' => 'Split Bill',
                                'urutan' => $urutan,
                                'status_kcp' => '1'
                            ));
                        }
                    }
                    $this->m_crud->update_data("detail_to", array('status' => 'S'), "no_to='" . $item['kd_trx'] . "' and status='R'");
                }

                $cek_transaksi = $this->m_crud->count_data("master_trx", "kd_trx", "kassa='".$master['kassa']."' AND lokasi='".$master['lokasi']."' AND kd_trx='".$master['kode_trx']."'");

                $kd_trx = $master['kode_trx'];

                if ($cek_transaksi == 0 && isset($master['kode_trx']) && $master['kode_trx'] != null && $master['kode_trx'] != '') {
                    $data_master = array(
                        'kd_trx' => $kd_trx,
                        'tgl' => date('Y-m-d', strtotime($master['tgl'])),
                        'kd_kasir' => $master['kd_kasir'],
                        'kd_cust' => $master['kd_cust'] == '' ? '1000001' : $master['kd_cust'],
                        'dis_rp' => $master['diskon'],
                        'dis_persen' => $master['dis_persen'],
                        'st' => $master['subtotal'],
                        'tax' => $master['tax'],
                        'gt' => $master['gt'],
                        'bayar' => $master['tunai'],
                        'change' => $master['change'],
                        'jam' => $master['jam'],
                        'tempo' => $master['tempo'],
                        'status' => $master['status'],
                        'hr' => $master['hr'],
                        'kassa' => $master['kassa'],
                        'lokasi' => $master['lokasi'],
                        'jenis_trx' => $master['jenis_trx'],
                        'ket_kas_lain' => $master['optional_note'],
                        'kd_trx_old' => $master['kode_trx'],
                        'no_po' => json_encode($kode_bill),
                        'kd_sales' => $master['kd_sales'],
                        'kartu' => $master['kartu'],
                        'jml_kartu' => $master['jml_kartu'],
                        'jns_kartu' => $master['jns_kartu'],
                        'no_kartu' => $master['no_kartu'],
                        'pemilik_kartu' => $master['pemilik_kartu'],
                        'charge' => $master['charge'],
                        'rounding' => $master['rounding'],
                        'compliment' => $master['compliment'],
                        'compliment_rp' => $master['compliment_rp'],
                        'poin_tukar' => $master['poin_tukar'],
                        'nominal_poin' => $master['nominal_poin'],
                    );
                    $this->m_crud->create_data("master_trx", $data_master);

                    /*Insert log*/
                    $log = array(
                        'type' => 'I',
                        'table' => "master_trx",
                        'data' => $data_master,
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

                    $st_poin = 0;
                    $kelipatan = (float)$this->poin_setting['kelipatan'];
                    $masa_berlaku = $this->poin_setting['berlaku'].' '.$this->poin_setting['masa'];
                    foreach ($detail as $item) {
                        $cek_barang_poin = $this->m_website->cek_barang_poin($item['sku']);
                        if ($cek_barang_poin) {
                            $st_poin = $st_poin + (float)$item['subtotal'];
                        }
                        if ($master['compliment'] != '-') {
                            $d_i = 0;
                            $t_i = 0;
                            $s_i = 0;
                        } else {
                            $d_i = $item['diskon'];
                            $t_i = $item['tax'];
                            $s_i = $item['services'];
                        }
                        $data_detail = array(
                            'kd_trx' => $kd_trx,
                            'kd_brg' => $item['sku'],
                            'qty' => $item['qty'],
                            'hrg_jual' => $item['price'],
                            'hrg_beli' => $item['hrg_beli'],
                            'subtotal' => $item['subtotal'],
                            'kategori' => $item['kategori'],
                            'open_price' => $item['open_price'],
                            'service' => $s_i,
                            'tax' => $t_i,
                            'dis_persen' => $d_i
                        );
                        $this->m_crud->create_data("det_trx", $data_detail);

                        /*Insert Log*/
                        $log = array(
                            'type' => 'I',
                            'table' => "det_trx",
                            'data' => $data_detail,
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

                        if ($item['qty'] < 0) {
                            $in = abs($item['qty']); $out = 0; $ket = 'Return Penjualan';
                        } else {
                            $in = 0; $out = $item['qty']; $ket = 'Penjualan';
                        }

                        // STOCKIS
                        if($item['kategori']!='Paket'){
                            $this->m_website->insert_stock($kd_trx, $master['tgl'] . ' ' . $master['jam'], $item['sku'], $in, $out, $master['lokasi'], $ket, $master['kode_trx']);
                        }else{
                            $read_assembly = $this->m_crud->join_data("barang br", "br.hrg_beli, asm.kd_brg_ass, asm.kd_brg, asm.kd_brg, asm.qty", "detail_assembly asm", "asm.kd_brg_ass=br.kd_brg", "br.kd_brg='".$item['sku']."'");
                            
                            if (count($read_assembly) > 0) {
                                foreach ($read_assembly as $row_ass) {
                                    if ($item['qty'] < 0) {
                                        $in2 = (int)abs($item['qty'])* (int)$row_ass['qty']; $out2 = 0; $ket = 'Return Penjualan';
                                    } else {
                                        $in2 = 0; $out2 = (int)$item['qty']* (int)$row_ass['qty']; $ket = 'Penjualan';
                                    }
                                    $kartu_stok = array(
                                        'kd_trx' => $kd_trx,
                                        'tgl' => $master['tgl'],
                                        'kd_brg' => $row_ass['kd_brg'],
                                        'saldo_awal' => 0,
                                        'stock_in' => $in2,
                                        'stock_out' => $out2,
                                        'lokasi' => $master['lokasi'],
                                        'keterangan' => 'Penjualan',
                                        'hrg_beli' => $row_ass['hrg_beli'],
                                    );
                                     $this->m_crud->create_data("Kartu_stock", $kartu_stok);
                                    // $this->m_website->insert_stock($kd_trx, $master['tgl'] . ' ' . $master['jam'], $row_ass['kd_brg'], $in2, $out2, $master['lokasi'], $ket, $master['kode_trx']);

                        
                                     $kartu_stok = array(
                                        'kd_trx' => $kd_trx,
                                        'kd_brg_ass' => $row_ass['kd_brg_ass'],
                                        'kd_brg' => $row_ass['kd_brg'],
                                        'qty' => (int)abs($item['qty']) * (int)$row_ass['qty'],
                                        'hrg_beli' => $row_ass['hrg_beli'],
                                    );
                                    $this->m_crud->create_data("Trx_Det_Assembly", $kartu_stok);
                                    }
                                }
                        }
                    }

                    if ($master['kd_cust'] != '') {
                        $get_lokasi = $this->m_crud->get_data("lokasi", "nama", "kode='".$master['lokasi']."'");
                        $get_onsignal = $this->m_crud->get_data("customer", "one_signal_id", "kd_cust='".$master['kd_cust']."'");

                        $data_notif = array(
                            'member'=>$get_onsignal['one_signal_id'],
                            /*'segment'=>'All',*/
                            'data' => array("param" => "rating", "kd_trx" => $kd_trx),
                            'head'=>'Penilaian pelayanan '.$this->m_website->site_data()->title,
                            'content'=>'Silahkan berikan penilaian anda untuk pelayanan kami di '.$get_lokasi['nama']
                        );

                        $this->m_website->create_notif($data_notif);

                        if ($st_poin >= $kelipatan) {
                            $masa_aktif = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', strtotime($master['tempo'])) . " + ".$masa_berlaku));
                            $update_master = array(
                                'poin' => floor((float)$st_poin/$kelipatan),
                                'kadaluarsa_poin' => $masa_aktif
                            );
                            $this->m_crud->update_data("master_trx", $update_master, "kd_trx='".$kd_trx."'");

                            $data_notif = array(
                                'member'=>$get_onsignal['one_signal_id'],
                                /*'segment'=>'All',*/
                                'data' => array(
                                    "param" => "poin",
                                    "kd_trx" => $kd_trx,
                                    "data" => array(
                                        'poin' => $this->m_crud->get_data("kartu_poin", "ISNULL(SUM(poin_masuk-poin_keluar), 0) poin", "kd_cust='".$master['kd_cust']."'")['poin']+0
                                    )
                                ),
                                'head'=>'Reward Poin '.$this->m_website->site_data()->title,
                                'content'=>'Selamat anda mendapatkan poin sebanyak '.floor((float)$st_poin/$kelipatan)
                            );

                            $this->m_website->create_notif($data_notif);

                            /*Insert Log*/
                            $log = array(
                                'type' => 'U',
                                'table' => "master_trx",
                                'data' => $update_master,
                                'condition' => "kd_trx='".$kd_trx."'"
                            );

                            $data_log = array(
                                'lokasi' => $this->config->item('lokasi'),
                                'hostname' => '-',
                                'db_name' => '-',
                                'query' => json_encode($log)
                            );
                            $this->m_website->insert_log_tr($data_log);
                            /*End insert log*/
                        }
                    }

                }

                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $response['status'] = false;
                    $response['pesan'] = 'Data Gagal Disimpan!';
                } else {
                    $this->db->trans_commit();
                    $response['status'] = true;
                    $response['pesan'] = 'Data Berhasil Disimpan!';
                    $response['kode'] = $master['kode_trx'];
                }

                echo json_encode($response);
            }

    public function simpan_brg() {
        $response = array();
        $tanggal = date('Y-m-d H:i:s');
        $param = $_POST['param']; // add/edit
        $data = json_decode($_POST['data'], true);
        $id = $_POST['id'];
        $lokasi = $_POST['lokasi'];

        $barcode = strtoupper($_POST['barcode']);
        $nama = ucwords($this->m_website->replace_kutip($_POST['nm_brg'], 'replace'));
        $where_barcode = "barcode = '".$barcode."'";
        if ($param == 'edit') {
            $where_barcode = "barcode = '".$barcode."' and kd_brg <> '".$id."'";
        }
        $cek_barcode = $this->m_crud->get_data("barang", "barcode", $where_barcode);

        if ($cek_barcode != null) {
            $response['status'] = false;
            $response['pesan'] = 'Barcode sudah tersedia';
        } else {
            $this->db->trans_begin();

            $path = 'assets/images/barang';
            $config['upload_path'] = './' . $path;
            $config['allowed_types'] = 'bmp|gif|jpg|jpeg|png';
            $config['max_size'] = 5120;
            $this->load->library('upload', $config);
            $input_file = array('gambar');
            $valid = true;
            foreach ($input_file as $row) {
                if ((!$this->upload->do_upload($row)) && $_FILES[$row]['name'] != null) {
                    $file[$row]['file_name'] = null;
                    $file[$row] = $this->upload->data();
                    $valid = false;
                    $data['error_' . $row] = $this->upload->display_errors();
                } else {
                    $file[$row] = $this->upload->data();
                    $data[$row] = $file;
                    if ($file[$row]['file_name'] != null) {
                        $manipulasi['image_library'] = 'gd2';
                        $manipulasi['source_image'] = $file[$row]['full_path'];
                        $manipulasi['maintain_ratio'] = true;
                        $manipulasi['width'] = 500;
                        //$manipulasi['height']       = 250;
                        $manipulasi['new_image'] = $file[$row]['full_path'];
                        $manipulasi['create_thumb'] = true;
                        //$manipulasi['thumb_marker']       = '_thumb';
                        $this->load->library('image_lib', $manipulasi);
                        $this->image_lib->resize();
                    }
                }
            }

            $master = array(
                'barcode' => $barcode,
                'nm_brg' => $nama,
                'Deskripsi' => '-',
                'kel_brg' => $_POST['kel_brg'],
                'Group1' => '-',
                'Group2' => $this->m_crud->get_data("kel_brg", "group2", "kel_brg='".$_POST['kel_brg']."'")['group2'],
                'satuan' => strtoupper($_POST['satuan']),
                'hrg_beli' => $_POST['hrg_beli'],
                'hrg_jual_1' => $_POST['hrg_jual'],
                'hrg_jual_2' => 0,
                'hrg_jual_3' => 0,
                'hrg_jual_4' => 0,
                'stock_min' => '0',
                'PPN' => $_POST['ppn'],
                'service' => $_POST['service'],
                'diskon' => '0',
                'kategori' => 'Non Paket',
                'Jenis' => $_POST['jenis_brg'],
                'kcp' => $_POST['kcp'],
                'poin' => $_POST['poin'],
                'tgl_update' => $tanggal,
                'kd_packing' => '-',
                'qty_packing' => '0'
            );

            if (isset($_FILES['gambar']) && $_FILES['gambar']['name'] != null) {
                $master['gambar'] = $path . '/' . $file['gambar']['file_name'];
                $img_upload = curl_file_create($_FILES['gambar']['tmp_name'], $_FILES['gambar']['type'], $_FILES['gambar']['name']);
            } else {
                if ($param == 'add') {
                    $file = 'assets/images/no_image.png';
                    $master['gambar'] = $file;
                    $filename = realpath($file);
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mimetype = $finfo->file($filename);
                    $name = basename($filename);
                    $img_upload = curl_file_create($filename, $mimetype, $name);
                }
            }

            if ($param == 'add') {
                $upload_curl = array(
                    'ukm_id' => $this->ukm,
                    'nama_produk' => $nama,
                    'kategori_id' => $_POST['kel_brg'],
                    'harga' => $_POST['hrg_jual'],
                    'deskripsi' => '-',
                    'url_blanja' => '-',
                    'foto' => $img_upload
                );

                if ($valid) {
                    $curl_produk = json_decode($this->m_website->api_rkb($upload_curl, 'create_produk.php'), true);
                }

                if ($curl_produk['success']) {
                    $kd_brg = $curl_produk['data']['produk_id'];
                } else {
                    $kd_brg = $barcode;
                }

                $master['kd_brg'] = $kd_brg;
                $master['tgl_input'] = $tanggal;

                $this->m_crud->create_data("barang", $master);

                $this->m_website->insert_stock('STOK AWAL', $tanggal, $kd_brg, $_POST['stok'], 0, $lokasi, 'Stok Awal', 'Stok Awal', $_POST['hrg_beli']);
            } else if ($param == 'edit') {
                $this->m_crud->update_data("barang", $master, "kd_brg='".$id."'");
                $cek_barang_lokasi = $this->m_crud->get_data("barang_hrg", "id_barang_hrg", "barang='".$id."' and lokasi='".$lokasi."'");

                $brg_lokasi = array(
                    'barang' => $id,
                    'lokasi' => $lokasi,
                    'hrg_jual_1' => $_POST['hrg_jual'],
                    'hrg_jual_2' => 0,
                    'hrg_jual_3' => 0,
                    'hrg_jual_4' => 0,
                    'service' => $_POST['service'],
                    'ppn' => $_POST['ppn']
                );

                if ($cek_barang_lokasi == null) {
                    $this->m_crud->create_data('barang_hrg', $brg_lokasi);

                    $log = array(
                        'type' => 'I',
                        'table' => 'barang_hrg',
                        'data' => $brg_lokasi,
                        'condition' => ""
                    );
                } else {
                    $condition = "id_barang_hrg = '".$cek_barang_lokasi['id_barang_hrg']."' and barang = '".$id."'";
                    $this->m_crud->update_data('barang_hrg', $brg_lokasi, $condition);
                    $log = array(
                        'type' => 'U',
                        'table' => 'barang_hrg',
                        'data' => $brg_lokasi,
                        'condition' => $condition
                    );
                }

                $data_log = array(
                    'lokasi' => $this->config->item('lokasi'),
                    'hostname' => '-',
                    'db_name' => '-',
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);
            }

            if ($this->db->trans_status() === FALSE OR $valid === false && !isset($param)) {
                $this->db->trans_rollback();
                $response['status'] = false;
                $response['pesan'] = 'Data Gagal Disimpan!';
            } else {
                $this->db->trans_commit();
                $response['status'] = true;
                $response['pesan'] = 'Data Berhasil Disimpan!';
            }
        }

        echo json_encode($response);
    }

    public function simpan_brg_bak() {
        $response = array();
        $tanggal = date('Y-m-d H:i:s');
        $param = $_POST['param']; // add/edit
        $data = json_decode($_POST['data'], true);
        $id = $_POST['id'];

        $barcode = strtoupper($_POST['barcode']);
        $nama = ucwords($this->m_website->replace_kutip($_POST['nm_brg'], 'replace'));
        $where_barcode = "barcode = '".$barcode."'";
        if ($param == 'edit') {
            $where_barcode = "barcode = '".$barcode."' and kd_brg <> '".$id."'";
        }
        $cek_barcode = $this->m_crud->get_data("barang", "barcode", $where_barcode);

        if ($cek_barcode != null) {
            $response['status'] = false;
            $response['pesan'] = 'Barcode sudah tersedia';
        } else {
            $this->db->trans_begin();

            $path = 'assets/images/produk';
            $config['upload_path'] = './' . $path;
            $config['allowed_types'] = 'bmp|gif|jpg|jpeg|png';
            $config['max_size'] = 5120;
            $this->load->library('upload', $config);
            $input_file = array('gambar');
            $valid = true;
            foreach ($input_file as $row) {
                if ((!$this->upload->do_upload($row)) && $_FILES[$row]['name'] != null) {
                    $file[$row]['file_name'] = null;
                    $file[$row] = $this->upload->data();
                    $valid = false;
                    $data['error_' . $row] = $this->upload->display_errors();
                } else {
                    $file[$row] = $this->upload->data();
                    $data[$row] = $file;
                    if ($file[$row]['file_name'] != null) {
                        $manipulasi['image_library'] = 'gd2';
                        $manipulasi['source_image'] = $file[$row]['full_path'];
                        $manipulasi['maintain_ratio'] = true;
                        $manipulasi['width'] = 500;
                        //$manipulasi['height']       = 250;
                        $manipulasi['new_image'] = $file[$row]['full_path'];
                        $manipulasi['create_thumb'] = true;
                        //$manipulasi['thumb_marker']       = '_thumb';
                        $this->load->library('image_lib', $manipulasi);
                        $this->image_lib->resize();
                    }
                }
            }

            $master = array(
                'barcode' => $barcode,
                'nm_brg' => $nama,
                'Deskripsi' => '-',
                'kel_brg' => $_POST['kel_brg'],
                'Group1' => '-',
                'Group2' => '-',
                'satuan' => strtoupper($_POST['satuan']),
                'hrg_beli' => $_POST['hrg_beli'],
                'hrg_jual_1' => $_POST['hrg_jual'],
                'hrg_jual_2' => $_POST['hrg_jual'],
                'hrg_jual_3' => $_POST['hrg_jual'],
                'hrg_jual_4' => $_POST['hrg_jual'],
                'stock_min' => '0',
                'PPN' => '0',
                'diskon' => '0',
                'kategori' => 'Non Paket',
                'Jenis' => 'Barang Dijual',
                'tgl_update' => $tanggal,
                'kd_packing' => '-',
                'qty_packing' => '0'
            );

            if ($_FILES['gambar']['name'] != null) {
                $master['gambar'] = $path . '/' . $file['gambar']['file_name'];
                $img_upload = curl_file_create($_FILES['gambar']['tmp_name'], $_FILES['gambar']['type'], $_FILES['gambar']['name']);
            } else {
                if ($param == 'add') {
                    $file = 'assets/images/no_image.png';
                    $master['gambar'] = $file;
                    $filename = realpath($file);
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mimetype = $finfo->file($filename);
                    $name = basename($filename);
                    $img_upload = curl_file_create($filename, $mimetype, $name);
                }
            }

            if ($param == 'add') {
                $upload_curl = array(
                    'ukm_id' => $this->ukm,
                    'nama_produk' => $nama,
                    'kategori_id' => $_POST['kel_brg'],
                    'harga' => $_POST['hrg_jual'],
                    'deskripsi' => '-',
                    'url_blanja' => '-',
                    'foto' => $img_upload
                );

                if ($valid) {
                    $curl_produk = json_decode($this->m_website->api_rkb($upload_curl, 'create_produk.php'), true);
                }

                if ($curl_produk['success']) {
                    $kd_brg = $curl_produk['data']['produk_id'];
                } else {
                    $kd_brg = $barcode;
                }

                $master['kd_brg'] = $kd_brg;
                $master['tgl_input'] = $tanggal;

                $this->m_crud->create_data("barang", $master);

                $this->m_website->insert_stock('STOK AWAL', $tanggal, $kd_brg, $_POST['stok'], 0, $_POST['lokasi'], 'Stok Awal', 'Stok Awal', $_POST['hrg_beli']);
            } else if ($param == 'edit') {
                $this->m_crud->update_data("barang", $master, "kd_brg='".$id."'");
            }

            if ($this->db->trans_status() === FALSE OR $valid === false && !isset($param)) {
                $this->db->trans_rollback();
                $response['status'] = false;
                $response['pesan'] = 'Data Gagal Disimpan!';
            } else {
                $this->db->trans_commit();
                $response['status'] = true;
                $response['pesan'] = 'Data Berhasil Disimpan!';
            }
        }

        echo json_encode($response);
    }

    public function simpan_kbrg() {
        $response = array();
        $param = $_POST['param']; // add/edit

        $this->db->trans_begin();

        if ($param == 'add') {
            $data = array(
                'kel_brg' => $this->m_website->generate_kode("kelompok", null, null),
                'nm_kel_brg' => strtoupper($_POST['nama']),
                'status' => '1',
                'Group2' => '-'
            );

            $this->m_crud->create_data("kel_brg", $data);
        } else if ($param == 'edit') {
            $this->m_crud->update_data("kel_brg", array('nm_kel_brg' => $_POST['nama']), "kel_brg='".$_POST['id']."'");
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $response['status'] = false;
            $response['pesan'] = 'Data Gagal Disimpan!';
        } else {
            $this->db->trans_commit();
            $response['status'] = true;
            $response['pesan'] = 'Data Berhasil Disimpan!';
        }

        echo json_encode($response);
    }

    public function simpan_kas() {
        $response = array();
        $param = $_POST['param'];

        $this->db->trans_begin();

        $data = array(
            'kd_kasir' => $_POST['kasir'],
            'tgl' => $_POST['tgl'],
            'jumlah' => (float)$_POST['jumlah'],
            'Keterangan' => $_POST['keterangan'],
            'lokasi' => $_POST['lokasi'],
            'kassa' => $_POST['kassa']
        );

        if ($param == 'masuk') {
            $kode_trx = $this->m_website->generate_kode('KM', $_POST['lokasi'], date('ymd', strtotime($data['tgl'])));
            $data['kd_trx'] = $kode_trx;
            $data['kd_kas_masuk'] = $_POST['kode'];

            $this->m_crud->create_data("kas_masuk", $data);

            /*Insert log*/
            $log = array(
                'type' => 'I',
                'table' => "kas_masuk",
                'data' => $data,
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
        } else if ($param == 'keluar') {
            $kode_trx = $this->m_website->generate_kode('KK', $_POST['lokasi'], date('ymd', strtotime($data['tgl'])));
            $data['kd_trx'] = $kode_trx;
            $data['kd_kas_keluar'] = $_POST['kode'];

            $this->m_crud->create_data("kas_keluar", $data);
            /*Insert log*/
            $log = array(
                'type' => 'I',
                'table' => "kas_keluar",
                'data' => $data,
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
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $response['status'] = false;
            $response['pesan'] = 'Data Gagal Disimpan!';
        } else {
            $this->db->trans_commit();
            $response['status'] = true;
            $response['pesan'] = 'Data Berhasil Disimpan!';
            $response['data'] = array('kd_trx'=>$kode_trx, 'tgl', $_POST['tgl']);
        }

        echo json_encode($response);
    }

    public function simpan_cust() {
        $response = array();
        $param = $_POST['param']; // add/edit/delete
        $id = $_POST['id'];

        $this->db->trans_begin();

        $data = array(
            'Nama' => strtoupper($_POST['nama']),
            'alamat' => $_POST['alamat'],
            'status' => '1',
            'tlp1' => $_POST['tlp'],
            'tgl_ultah' => $_POST['tgl_ultah']
        );

        if ($param == 'add') {
            $data['kd_cust'] = $this->m_website->generate_kode('CS', $this->ukm, date('ymd'));
            $data['id'] = $this->m_website->generate_kode('CS', $this->ukm, 'id');
            $data['Cust_Type'] = 'UMUM';

            $this->m_crud->create_data("Customer", $data);
        } else if ($param == 'edit') {
            $this->m_crud->update_data("Customer", $data, "kd_cust='".$id."'");
        } else if ($param == 'delete') {
            $this->m_crud->update_data("Customer", array('status'=>'0'), "kd_cust='".$id."'");
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $response['status'] = false;
            $response['pesan'] = 'Data Gagal Disimpan!';
        } else {
            $this->db->trans_commit();
            $response['status'] = true;
            $response['pesan'] = 'Data Berhasil Disimpan!';
        }

        echo json_encode($response);
    }

    public function simpan_headfoot() {
        $response = array('status'=>true, 'pesan'=>'Data Berhasil Disimpan!');
        $param = $_POST['param'];
        $lokasi = $_POST['lokasi'];
        $d1 = $_POST['d1'];
        $d2 = $_POST['d2'];
        $d3 = $_POST['d3'];
        $d4 = $_POST['d4'];

        if ($param == 'header') {
            $data = array(
                'nama' => $d1,
                'ket' => $d2,
                'kota' => $d3,
                'web' => $d4
            );
        } else if ($param == 'footer') {
            $data = array(
                'footer1' => $d1,
                'footer2' => $d2,
                'footer3' => $d3,
                'footer4' => $d4
            );
        }

        $this->m_crud->update_data("lokasi", $data, "kode='".$lokasi."'");

        $get_seting = $this->m_crud->get_data("lokasi", "nama header1, ket header2, kota header3, web header4, footer1, footer2, footer3, footer4", "kode='" . $lokasi . "'");

        $response['header_footer'] = $get_seting;

        echo json_encode($response);
    }

    public function get_member_payment() {
        $member = $_POST['id_member'];
        $data = json_decode($_POST['data'], true);
        $poin = $this->m_website->get_poin($member);

        $st_poin = 0;
        foreach ($data['detail'] as $item) {
            $cek_barang_poin = $this->m_website->cek_barang_poin($item['sku']);
            if ($cek_barang_poin) {
                $st_poin = $st_poin + (float)$item['subtotal'];
            }
        }

        if ($st_poin >= (float)$this->poin_setting['minimal']) {
            $nilai_max = $st_poin*((float)$this->poin_setting['maksimal']/100);

            $max_poin = floor($nilai_max/(float)$this->poin_setting['nilai']);
        }

        $response = array(
            'poin' => $poin,
            'max_tukar' => ($max_poin==null?0:($max_poin>$poin?$poin:$max_poin)),
            'nilai' => $this->poin_setting['nilai'],
            'deposit' => $this->m_website->get_deposit($member)
        );

        echo json_encode($response);
    }



    public function closing_tr() {
        $response = array();
        $kasir = $_POST['kasir'];
        $kassa = $_POST['kassa'];
        $tgl = $_POST['tgl'];
        $lokasi = $_POST['lokasi'];
        $param = $_POST['param'];

        $cek_data = $this->cek_setoran($kassa, $kasir, $lokasi, $tgl);

        if ($cek_data == 0) {
            $this->db->trans_begin();

            //$get_omset = $this->m_crud->get_join_data("Master_trx mt", "sum(mt.dis_rp) dis_rp, sum(mt.tax) tax, SUM(dt.qty * dt.hrg_jual) total", "Det_trx dt", "dt.kd_trx=mt.kd_trx", "left(convert(varchar, mt.tgl, 120), 10) = '".date('Y-m-d', strtotime($_POST['tgl']))."' AND mt.lokasi='".$_POST['lokasi']."' AND mt.HR='S' AND dt.qty>0", null, "left(convert(varchar, mt.tgl, 120), 10)");
            $non_tunai = $this->m_crud->read_data("master_trx", "kartu, sum(jml_kartu) jml_kartu", "HR='S' and kartu<>'-' and left(convert(varchar, tgl, 120), 10) = '".date('Y-m-d', strtotime($_POST['tgl']))."' AND kd_kasir='".$_POST['kasir']."' AND lokasi='".$_POST['lokasi']."' AND kassa='".$_POST['kassa']."'", null, "kartu");
            $get_omset = $this->m_crud->get_data("tr_sukses", "sum(st) st, sum(bayar-change) tunai, sum(dis_rp) disc_tr, sum(jml_kartu) card, sum(nominal_poin) poin, sum(nominal_deposit) deposit, sum(tax) tax, sum(serv) serv, sum(compliment_rp) compliment, sum(disc) disc", "left(convert(varchar, tgl, 120), 10) = '".date('Y-m-d', strtotime($_POST['tgl']))."' AND kd_kasir='".$_POST['kasir']."' AND lokasi='".$_POST['lokasi']."' AND kassa='".$_POST['kassa']."'", null, "left(convert(varchar, tgl, 120), 10)");
            $get_retur = $this->m_crud->get_data("tr_void", "sum(st) st, sum(dis_rp) disc_tr, sum(tax) tax, sum(serv) serv, sum(disc) disc", "left(convert(varchar, tgl, 120), 10) = '".date('Y-m-d', strtotime($_POST['tgl']))."' AND kd_kasir='".$_POST['kasir']."' AND lokasi='".$_POST['lokasi']."' AND kassa='".$_POST['kassa']."'", null, "left(convert(varchar, tgl, 120), 10)");
            //$get_retur = $this->m_crud->get_join_data("Master_trx mt", "SUM(dt.qty * dt.hrg_jual) total", "Det_trx dt", "dt.kd_trx=mt.kd_trx", "left(convert(varchar, mt.tgl, 120), 10) = '".date('Y-m-d', strtotime($_POST['tgl']))."' AND mt.lokasi='".$_POST['lokasi']."' AND mt.HR='S' AND dt.qty<0", null, "left(convert(varchar, mt.tgl, 120), 10)");
            $get_kas_masuk = $this->m_crud->get_data("kas_masuk", "sum(jumlah) jumlah", "kd_kasir='".$_POST['kasir']."' AND kassa='".$_POST['kassa']."' AND left(convert(varchar, tgl, 120), 10)='".date('Y-m-d', strtotime($_POST['tgl']))."'");
            $get_kas_keluar = $this->m_crud->get_data("kas_keluar", "sum(jumlah) jumlah", "kd_kasir='".$_POST['kasir']."' AND kassa='".$_POST['kassa']."' AND left(convert(varchar, tgl, 120), 10)='".date('Y-m-d', strtotime($_POST['tgl']))."'");

            $total = (float)$get_omset['st']-(float)$get_retur['total']-(float)$get_omset['disc_tr']-(float)$get_omset['disc']+(float)$get_omset['tax']+(float)$get_omset['serv']+(float)$get_kas_masuk['jumlah']-(float)$get_kas_keluar['jumlah'];

            $tunai = (float)$_POST['tunai'];
            $data = array(
                'Tanggal' => $tgl,
                'Kd_Kasir' => $kasir,
                'Kassa' => $kassa,
                'Setoran_tunai' => $tunai,
                'setoran_card' => $get_omset['card'],
                'setoran_poin' => $get_omset['poin'],
                'setoran_compliment' => $get_omset['compliment'],
                'Lokasi' => $lokasi,
                'ID' => 'ST'.date('ymd', strtotime($tgl)).$lokasi.$kasir.$kassa,
                'Keterangan' => ''
            );

            if ($param == 'closing') {
                $this->m_crud->create_data("Setoran", $data);
            }

            /*Insert log*/
            $log = array(
                'type' => 'I',
                'table' => "setoran",
                'data' => $data,
                'condition' => ""
            );

            $data_log = array(
                'lokasi' => $this->config->item('lokasi'),
                'hostname' => '-',
                'db_name' => '-',
                'query' => json_encode($log)
            );
            if ($param == 'closing') {
                $this->m_website->insert_log_tr($data_log);
            }
            /*End insert log*/

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $response['status'] = false;
                $response['pesan'] = 'Data Gagal Disimpan!';
            } else {
                $this->db->trans_commit();
                $response['status'] = true;
                $response['pesan'] = 'Transaksi berhasil disimpan!';

                $uang = $tunai + (float)$get_omset['compliment'] + (float)$get_omset['card'] + (float)$get_omset['deposit'];

                if ($uang == $total) {
                    $pesan = 'Balance';
                } else if ($uang > $total) {
                    $pesan = 'Surplus';
                } else if ($uang < $total) {
                    $pesan = 'Deficit';
                }

                $list_print = $this->closing_by_barang(date('Y-m-d', strtotime($tgl)), $kasir, $kassa, $lokasi);

                $response['data'] = array(
                    'pesan' => $pesan,
                    'sales' => (float)$get_omset['st'],
                    'jml_kartu' => (float)$get_omset['card'],
                    'non_tunai' => $non_tunai,
                    'compliment' => (float)$get_omset['compliment'],
                    'tunai' => (float)$get_omset['tunai'],
                    'poin' => (float)$get_omset['poin'],
                    'retur' => (float)$get_retur['st']-$get_retur['disc_tr']+$get_retur['tax']+$get_retur['serv'],
                    'diskon' => (float)$get_omset['disc_tr'],
                    'pajak' => (float)$get_omset['tax'],
                    'service' => (float)$get_omset['serv'],
                    'dis_item' => (float)$get_omset['disc'],
                    'deposit' => (float)$get_omset['deposit'],
                    'kas_masuk' => (float)$get_kas_masuk['jumlah'],
                    'kas_keluar' => (float)abs($get_kas_keluar['jumlah'])*-1,
                    'cash_in_hand' => (float)$tunai,
                    'list_print' => $list_print['list'],
                    'total_print' => $list_print['total']
                );
            }
        } else {
            $response['status'] = false;
            $response['pesan'] = "Transaksi sudah di tutup!";
        }

        echo json_encode($response);
    }

    public function adjustment($perpage=10, $page=1) {
        $response = array();
        $param = $_POST['param'];
        $start = ($page-1)*$perpage+1;
        $end = $perpage*$page;

        if ($param == 'report') {
            $get_report = $this->m_crud->select_limit_join("adjust a", "a.*, br.barcode, br.nm_brg", "barang br", "br.kd_brg=a.kd_brg", null, "a.kd_trx", null, $start, $end);

            if ($get_report == null) {
                $response['status'] = false;
            } else {
                $response['status'] = true;
                $response['data'] = $this->m_website->tambah_data('adjust', $get_report);
            }
        } else if ($param == 'simpan') {
            $this->db->trans_begin();

            $tanggal = $_POST['tgl'];
            $kode = $this->m_website->generate_kode("AA", $this->ukm, date('ymd', strtotime($tanggal)));
            $kode_trx = $kode;

            $adjustment = array(
                'kd_trx' => $kode_trx,
                'kd_brg' => $_POST['kd_brg'],
                'status' => $_POST['status'],
                'qty_adjust' => $_POST['qty_adjust'],
                'tgl' => $tanggal,
                'kd_kasir' => $_POST['kasir'],
                'stock_terakhir' => $_POST['stock_terakhir'],
                'lokasi' => $_POST['lokasi'],
                'saldo_stock' => $_POST['saldo_stock'],
                'keterangan' => $_POST['keterangan']
            );

            $this->m_crud->create_data("adjust", $adjustment);

            if ($_POST['status'] == '+') {
                $in = $_POST['qty_adjust']; $out = 0;
            } else {
                $in = 0; $out = $_POST['qty_adjust'];
            }

            $this->m_website->insert_stock($kode_trx, $tanggal, $_POST['kd_brg'], $in, $out, $_POST['lokasi'], 'Adjustment', $kode);

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $response['status'] = false;
                $response['pesan'] = 'Data Gagal Disimpan!';
            } else {
                $this->db->trans_commit();
                $response['status'] = true;
                $response['pesan'] = 'Transaksi berhasil disimpan!';
            }
        }

        echo json_encode($response);
    }

    public function get_bill($perpage=10, $page=1) {
        $response = array();
        $start = ($page-1)*$perpage+1;
        $end = $perpage*$page;

        $get_data = $this->m_crud->select_limit("master_to", "*", "status='P'", "no_meja ASC", null, $start, $end);

        if ($get_data != null) {
            $response['status'] = true;
            foreach ($get_data as $key => $item) {
                $get_data[$key]['det_produk'] = $this->m_website->tambah_data('bill', $this->m_crud->join_data("detail_to dt", "dt.kd_brg, br.hrg_jual_1 hrg_jual, br.service, br.ppn, br.nm_brg, br.gambar, br.hrg_beli, sum(qty) qty", "barang br", "br.kd_brg=dt.kd_brg", "status='P' AND dt.no_to='".$item['no_to']."'", "dt.kd_brg ASC", "dt.kd_brg, br.hrg_jual_1, br.service, br.ppn, br.nm_brg, br.gambar, br.hrg_beli"), $_POST['lokasi']);
            }

            $response['data'] = $get_data;
        } else {
            $response['status'] = false;
        }

        echo json_encode($response);
    }
    /*END API RKB*/

    public function closing_by_barang($tanggal, $kasir, $kassa, $lokasi) {
        $where = "mt.HR = 'S' AND dt.qty>0 AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND kb.kel_brg=br.kel_brg";
        $where .= " AND left(convert(varchar, mt.tgl, 120), 10)='".$tanggal."' AND kd_kasir='".$kasir."' AND kassa='".$kassa."' AND lokasi='".$lokasi."'";
        $list = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br, kel_brg kb", "kb.nm_kel_brg, kb.kel_brg, br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan, SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service", ($where==null)?null:$where, "kb.nm_kel_brg ASC", "kb.nm_kel_brg, kb.kel_brg, br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan");

        $where_retur = "mt.HR = 'S' AND dt.qty < 0 AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND kb.kel_brg=br.kel_brg";
        $where_retur .= " AND left(convert(varchar, mt.tgl, 120), 10)='".$tanggal."' AND kd_kasir='".$kasir."' AND kassa='".$kassa."' AND lokasi='".$lokasi."'";
        $list_retur = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br, kel_brg kb", "kb.nm_kel_brg, kb.kel_brg, br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan, SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service", ($where_retur==null)?null:$where_retur, "kb.nm_kel_brg ASC", "kb.nm_kel_brg, kb.kel_brg, br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan");

        $tqt = 0;
        $tqtr = 0;
        $tgs = 0;
        $tgsr = 0;
        $kel_brg = '';
        $list_print = array();

        foreach ($list as $key => $item) {
            if ($key == 0) {
                array_push($list_print, array('kel_brg'=>$item['nm_kel_brg'], 'id'=>$item['kel_brg'], 'barang'=>array(array('nm_brg'=>$item['nm_brg'], 'qty'=>$item['qty_jual'], 'jumlah'=>$item['gross_sales'], 'text'=>sprintf("%'.-16s", substr($item['nm_brg'], 0,16)).' '.sprintf("%4s", $item['qty_jual']+0).' '.sprintf("%10s", number_format($item['gross_sales'], 0, ',', '.'))))));
            } else if ($kel_brg == $item['kel_brg']) {
                $key = array_search($item['kel_brg'], array_column($list_print, 'id'));
                array_push($list_print[$key]['barang'], array('nm_brg'=>$item['nm_brg'], 'qty'=>$item['qty_jual'], 'jumlah'=>$item['gross_sales'], 'text'=>sprintf("%'.-16s", substr($item['nm_brg'], 0,16)).' '.sprintf("%4s", $item['qty_jual']+0).' '.sprintf("%10s", number_format($item['gross_sales'], 0, ',', '.'))));
            } else {
                array_push($list_print, array('kel_brg'=>$item['nm_kel_brg'], 'id'=>$item['kel_brg'], 'barang'=>array(array('nm_brg'=>$item['nm_brg'], 'qty'=>$item['qty_jual'], 'jumlah'=>$item['gross_sales'], 'text'=>sprintf("%'.-16s", substr($item['nm_brg'], 0,16)).' '.sprintf("%4s", $item['qty_jual']+0).' '.sprintf("%10s", number_format($item['gross_sales'], 0, ',', '.'))))));
            }
            $kel_brg = $item['kel_brg'];
            $tqt = $tqt + $item['qty_jual'];
            $tgs = $tgs + $item['gross_sales'];
        }

        foreach ($list_retur as $key => $item) {
            if ($key == 0) {
                array_push($list_print, array('kel_brg'=>'Retur', 'id'=>'rtr', 'barang'=>array(array('nm_brg'=>$item['nm_brg'], 'qty'=>$item['qty_jual'], 'jumlah'=>$item['gross_sales'], 'text'=>sprintf("%'.-16s", substr($item['nm_brg'], 0,16)).' '.sprintf("%4s", $item['qty_jual']+0).' '.sprintf("%10s", number_format($item['gross_sales'], 0, ',', '.'))))));
            } else {
                $key = array_search('rtr', array_column($list_print, 'id'));
                array_push($list_print[$key]['barang'], array('nm_brg'=>$item['nm_brg'], 'qty'=>$item['qty_jual'], 'jumlah'=>$item['gross_sales'], 'text'=>sprintf("%'.-16s", substr($item['nm_brg'], 0,16)).' '.sprintf("%4s", $item['qty_jual']+0).' '.sprintf("%10s", number_format($item['gross_sales'], 0, ',', '.'))));
            }
            $tqtr = $tqtr + $item['qty_jual'];
            $tgsr = $tgsr + $item['gross_sales'];
        }

        $list_total = sprintf("%'.-16s", 'Total').' '.sprintf("%4s", $tqt).' '.sprintf("%10s", number_format($tgs, 0, ',', '.'));
        $list_total .= '|'.sprintf("%'.-16s", 'Retur').' '.sprintf("%4s", $tqtr).' '.sprintf("%10s", number_format($tgsr, 0, ',', '.'));
        $list_total .= '|'.sprintf("%'.-16s", 'Grand Total').' '.sprintf("%4s", $tqt+$tqtr).' '.sprintf("%10s", number_format($tgs+$tgsr, 0, ',', '.'));

        $result = array(
            'list' => $list_print,
            'total' => $list_total
        );

        return $result;
    }

    public function closing_barang() {
        $tanggal = $_POST['tgl'];
        $kasir = $_POST['kasir'];
        $kassa = $_POST['kassa'];
        $lokasi = $_POST['lokasi'];

        $where = "mt.HR = 'S' AND mt.kd_trx=dt.kd_trx AND dt.kd_brg=br.kd_brg AND kb.kel_brg=br.kel_brg";
        $where .= " AND left(convert(varchar, mt.tgl, 120), 10)='".$tanggal."' AND mt.kd_kasir='".$kasir."' AND mt.kassa='".$kassa."' AND mt.lokasi='".$lokasi."'";
        $list = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br, kel_brg kb", "kb.nm_kel_brg, kb.kel_brg, br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan, SUM(dt.qty) qty_jual, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon_item, sum(dt.tax) tax, sum(dt.service) service", ($where==null)?null:$where, "kb.nm_kel_brg ASC", "kb.nm_kel_brg, kb.kel_brg, br.kd_brg, br.nm_brg, br.barcode, br.Deskripsi, br.satuan");

        $kel_brg = '';
        $tqt = 0;
        $tgs = 0;
        $list_print = array();

        foreach ($list as $key => $item) {
            if ($key == 0) {
                array_push($list_print, array('kel_brg'=>$item['nm_kel_brg'], 'id'=>$item['kel_brg'], 'barang'=>array(array('nm_brg'=>$item['nm_brg'], 'qty'=>$item['qty_jual'], 'jumlah'=>$item['gross_sales'], 'text'=>sprintf("%'.-16s", substr($item['nm_brg'], 0,16)).' '.sprintf("%4s", $item['qty_jual']+0).' '.sprintf("%10s", number_format($item['gross_sales'], 0, ',', '.'))))));
            } else if ($kel_brg == $item['kel_brg']) {
                $key = array_search($item['kel_brg'], array_column($list_print, 'id'));
                array_push($list_print[$key]['barang'], array('nm_brg'=>$item['nm_brg'], 'qty'=>$item['qty_jual'], 'jumlah'=>$item['gross_sales'], 'text'=>sprintf("%'.-16s", substr($item['nm_brg'], 0,16)).' '.sprintf("%4s", $item['qty_jual']+0).' '.sprintf("%10s", number_format($item['gross_sales'], 0, ',', '.'))));
            } else {
                array_push($list_print, array('kel_brg'=>$item['nm_kel_brg'], 'id'=>$item['kel_brg'], 'barang'=>array(array('nm_brg'=>$item['nm_brg'], 'qty'=>$item['qty_jual'], 'jumlah'=>$item['gross_sales'], 'text'=>sprintf("%'.-16s", substr($item['nm_brg'], 0,16)).' '.sprintf("%4s", $item['qty_jual']+0).' '.sprintf("%10s", number_format($item['gross_sales'], 0, ',', '.'))))));
            }
            $tqt = $tqt + $item['qty_jual'];
            $tgs = $tgs + $item['gross_sales'];
            $kel_brg = $item['kel_brg'];
        }

        echo json_encode($list_print);
    }
}

