<?php
if (!defined('BASEPATH')) exit('No direct access allowed');
class Cetak extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $site_data = $this->m_website->site_data();
        $this->site = str_replace(' ', '', strtolower($site_data->title));
        $this->user = $this->session->userdata($this->site . 'user');
        if ($this->session->userdata($this->site . 'isLogin') == false) {
            redirect('site');
        }
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '2048M');
        ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
    }

    public function nota_pembelian($kode) {
        $id = base64_decode($kode);
        $get_data = $this->m_crud->get_data("master_beli mb, Supplier sp", "mb.no_po, mb.tgl_beli, mb.no_faktur_beli, mb.Operator, mb.type, mb.kode_supplier, mb.noNota, mb.nama_penerima, mb.Lokasi, mb.nilai_pembelian, mb.total_pembelian, mb.disc, mb.ppn, sp.Nama, isnull((SELECT SUM(Total) FROM Master_Retur_Beli WHERE no_beli=mb.no_faktur_beli),0) jumlah_retur", "mb.kode_supplier=sp.Kode AND mb.no_faktur_beli='".$id."'");

        $data = $this->db->query("SELECT db.kode_barang, br.nm_brg, br.Deskripsi, br.barcode, db.jumlah_beli, isnull(db.jumlah_bonus, 0) jumlah_bonus, db.harga_beli, db.ppn ppn_item, db.diskon disc1, db.disc2, db.disc3, db.disc4, br.satuan, isnull(dr.jml,0) jumlah_retur, isnull((SELECT hrg_jual_1 FROM barang_hrg WHERE barang=br.kd_brg AND lokasi='".$get_data['Lokasi']."' GROUP BY hrg_jual_1),0) harga_jual
                                            FROM det_beli db
                                            LEFT JOIN barang br ON ltrim(rtrim(db.kode_barang)) = ltrim(rtrim(br.kd_brg))
                                            LEFT JOIN Master_Retur_Beli mr ON db.no_faktur_beli=mr.no_beli
                                            LEFT JOIN Det_Retur_Beli dr ON dr.No_Retur=mr.No_Retur AND dr.kd_brg=db.kode_barang
                                            WHERE db.no_faktur_beli = '".$id."'")->result_array();


        $sub_total = 0;
        $qty = 0;
        $i = 1;
        $list = '';

        foreach ($data as $row){
            if ((int)$row['jumlah_bonus'] > 0) {
                $bonus = ' + '.(int)$row['jumlah_bonus'];
            } else {
                $bonus = '';
            }
            //$hitung_netto = ((int)$row['jumlah_beli']-(int)$row['jumlah_retur']) * $row['harga_beli'];
            $hitung_netto = ((int)$row['jumlah_beli']) * $row['harga_beli'];
            $disc = $this->m_website->double_diskon($hitung_netto, array($row['disc1'], $row['disc2']));
            $hitung_sub_total = $this->m_website->grand_total_ppn($disc, 0, $row['ppn_item']);
            $sub_total = $sub_total + $hitung_sub_total;
            //$qty = $qty + ((int)$row['jumlah_beli']-(int)$row['jumlah_retur'])+(int)$row['jumlah_bonus'];
            $qty = $qty + ((int)$row['jumlah_beli'])+(int)$row['jumlah_bonus'];

            $d1 = $row['harga_beli']*(1-($row['disc1']/100));
            $hrg_beli = $d1*(1-($row['disc2']/100));
            if (ltrim(rtrim($row['barcode']))==ltrim(rtrim($row['kode_barang']))) {
                $brcd_art = $row['Deskripsi'];
            } else {
                $brcd_art = $row['barcode'];
            }
            $list .= '<tr><td class="isi center">'.$i.'</td><td class="isi">'.$row['kode_barang'].'</td><td class="isi">'.$brcd_art.'</td><td class="isi">'.$row['nm_brg'].'</td><td class="isi">'.$row['satuan'].'</td><td class="isi kanan">'.number_format($row['harga_beli'], 0, ',', '.').'</td><td class="isi kanan">'.number_format($row['harga_jual'], 0, ',', '.').'</td><td class="isi center">'.(($row['harga_jual']!=0)?number_format((1 - ($hrg_beli/$row['harga_jual']))*100, 2, ',', '.'):'0').'</td><td class="isi center">'.($row['disc1']+0).'</td><td class="isi center">'.($row['disc2']+0).'</td><td class="isi center">'.($row['ppn_item']+0).'</td><td class="isi center">'.((int)$row['jumlah_beli']).$bonus.'</td><td class="isi kanan">'.number_format($hitung_sub_total, 0, ',', '.').'</td></tr>';
            $i++;
        }
        $total = ($sub_total - $get_data['disc']) + (($get_data['ppn']/100) * $sub_total);

        $data2 = array(
            'title' => 'Cetak Nota Pembelian Barang',
            'list' => $list,
            'diskon' => $get_data['disc'],
            'ppn' => $get_data['ppn'],
            'sub_total' => $sub_total,
            'qty' => $qty,
            'tanggal' => substr($get_data['tgl_beli'],0,10),
            'no_faktur' => $get_data['no_faktur_beli'],
            'nopo' => $get_data['no_po'],
            'no_nota' => $get_data['noNota'],
            'nama_penerima' => $get_data['nama_penerima'],
            'operator' => $this->m_website->get_nama_user($get_data['Operator']),
            'tipe' => ucwords($get_data['type']),
            'kode_supplier' => $get_data['kode_supplier'],
            'nama_supplier' => $get_data['Nama'],
            'get_data' => $get_data,
            'total' => $total,
            'total_pembelian' => number_format($total, 0, ',', '.'),
            'terbilang' => number_to_words(($total))
        );

        //load the view and saved it into $html variable
        $html=$this->load->view('bo/Cetak/nota_pembelian', $data2, true);

        //this the the PDF filename that user will get to download
        //$nama = $getSpk->kode_spk;
        $file = 'nota_pembelian';

        $footer = null;
        $conf = array( //'paper'=>array(200,100)
            'mode'=>'utf-8','paper'=>'A4','font_size'=>10,'font_family'=>null,
            'left'=>10,'right'=>10,'top'=>10,'bottom'=>10,'header'=>0,'footer'=>0
        );
        $this->m_export_file->to_tcpdf('I', $file, $html, null, null, $conf);
    }

    public function nota_retur_pembelian($kode) {
        $id = base64_decode($kode);
        //$this->m_website->add_activity($id, 'R');
        $get_data = $this->m_crud->get_join_data("Master_Retur_Beli rb", "rb.No_Retur, rb.Tgl, rb.no_beli, rb.Supplier, rb.lokasi_cabang, rb.keterangan, rb.kd_kasir, isnull(mb.noNota, 'Tanpa Nota') noNota, sp.Nama", array(array("table"=>"Supplier sp", "type"=>"LEFT"), array("table"=>"master_beli mb", "type"=>"LEFT")), array("rb.Supplier=sp.Kode", "rb.no_beli=mb.no_faktur_beli"), "rb.No_Retur='".$id."'");
        $output = '';
        $total = 0;
        $total_qty = 0;
        $i = 1;
        $data = $this->m_crud->read_data('Det_Retur_Beli drb, barang br, kel_brg kb', 'drb.kd_brg, drb.jml, drb.hrg_beli, drb.kondisi, drb.keterangan, br.barcode, br.nm_brg, kb.nm_kel_brg, br.satuan', "drb.kd_brg = br.kd_brg AND br.kel_brg = kb.kel_brg AND drb.No_Retur = '".$id."'");
        foreach ($data as $row){
            $sub_total = $row['jml'] * $row['hrg_beli'];
            $total = $total + $sub_total;
            $total_qty = $total_qty + $row['jml'];
            $output .= '<tr><td class="isi center">'.$i.'</td><td class="isi">'.$row['kd_brg'].'</td><td class="isi">'.$row['barcode'].'</td><td class="isi">'.$row['nm_brg'].'</td><td class="isi">'.$row['nm_kel_brg'].'</td><td class="isi center">'.($row['jml']+0).'</td><td class="isi">'.$row['satuan'].'</td><td class="isi">'.$row['kondisi'].'</td><td class="isi kanan">'.number_format($row['hrg_beli']).'</td><td class="isi kanan">'.number_format($sub_total).'</td>';
            $i++;
        }
        $data2 = array(
            'title' => 'Cetak Nota Retur Penjualan',
            'table' => $output,
            'total' => $total,
            'total_qty' => $total_qty,
            'tanggal' => substr($get_data['Tgl'],0,10),
            'no_retur' => $get_data['No_Retur'],
            'nota_supplier' => $get_data['noNota'],
            'kode_supplier' => $get_data['Supplier'],
            'nama_supplier' => $get_data['Nama'],
            'terbilang' => number_to_words($total),
            'keterangan' => $get_data['keterangan'],
            'operator' => $get_data['kd_kasir'],
            'lokasi_cabang' => $get_data['lokasi_cabang']
        );

        //load the view and saved it into $html variable
        $html=$this->load->view('bo/Cetak/nota_retur_pembelian', $data2, true);

        //this the the PDF filename that user will get to download
        //$nama = $getSpk->kode_spk;
        $nama = 'nota_retur_pembelian';
        $pdfFilePath = $nama.".pdf";

        //load mPDF library
        $this->load->library('m_report_p');

        //generate the PDF from the given html
        $this->m_report_p->pdf->WriteHTML($html);

        //download it.
        $this->m_report_p->pdf->Output($pdfFilePath, "I");
    }

    public function list_alokasi($kode) {
        $id = base64_decode($kode);
        $get_data = $this->m_crud->get_data("master_beli mb, Supplier sp", "mb.tgl_beli, mb.no_faktur_beli, mb.type, mb.kode_supplier, mb.noNota, mb.Lokasi, mb.nilai_pembelian, mb.total_pembelian, mb.disc, mb.ppn, sp.Nama, isnull((SELECT SUM(Total) FROM Master_Retur_Beli WHERE no_beli=mb.no_faktur_beli),0) jumlah_retur", "mb.kode_supplier=sp.Kode AND mb.no_faktur_beli='".$id."'");
        $output = '';
        $i = 1;

        $data = $this->db->query("SELECT db.kode_barang, br.nm_brg, br.Deskripsi, br.barcode, db.jumlah_beli, db.harga_beli, db.ppn ppn_item, db.diskon disc1, db.disc2, db.disc3, db.disc4, br.satuan, isnull(dr.jml,0) jumlah_retur, isnull((SELECT hrg_jual_1 FROM barang_hrg WHERE barang=br.kd_brg AND lokasi='".$get_data['Lokasi']."' GROUP BY hrg_jual_1),0) harga_jual
                                            FROM det_beli db
                                            LEFT JOIN barang br ON ltrim(rtrim(db.kode_barang)) = ltrim(rtrim(br.kd_brg))
                                            LEFT JOIN Master_Retur_Beli mr ON db.no_faktur_beli=mr.no_beli
                                            LEFT JOIN Det_Retur_Beli dr ON dr.No_Retur=mr.No_Retur AND dr.kd_brg=db.kode_barang
                                            WHERE db.no_faktur_beli = '".$id."'")->result_array();

        foreach ($data as $row){
            $output .= '<tr><td class="isi center">'.$i.'</td><td class="isi">'.$row['kode_barang'].'</td><td class="isi">'.$row['barcode'].'</td><td class="isi">'.$row['nm_brg'].'</td><td class="isi center">'.($row['jumlah_beli']-$row['jumlah_retur']+0).'</td><td class="isi kanan">'.number_format($row['harga_jual']).'</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
            $i++;
        }

        $data2 = array(
            'title' => 'Form Alokasi',
            'data' => $output,
            'tanggal' => substr($get_data['tgl_beli'],0,10),
            'no_beli' => $get_data['no_faktur_beli'],
            'nota_supplier' => $get_data['noNota'],
            'supplier' => $get_data['kode_supplier'].' | '.$get_data['Nama']
        );

        //load the view and saved it into $html variable
        $html=$this->load->view('bo/Cetak/form_alokasi', $data2, true);

        //this the the PDF filename that user will get to download
        //$nama = $getSpk->kode_spk;
        $nama = 'form_alokasi';
        $pdfFilePath = $nama.".pdf";

        //load mPDF library
        $this->load->library('m_report_l');

        //generate the PDF from the given html
        $this->m_report_l->pdf->WriteHTML($html);

        //download it.
        $this->m_report_l->pdf->Output($pdfFilePath, "I");
    }

    public function form_alokasi($kode) {
        $id = base64_decode($kode);
        $get_data = $this->m_crud->get_data("master_beli mb, Supplier sp", "mb.tgl_beli, mb.no_faktur_beli, mb.type, mb.kode_supplier, mb.noNota, mb.Lokasi, mb.nilai_pembelian, mb.total_pembelian, mb.disc, mb.ppn, sp.Nama, isnull((SELECT SUM(Total) FROM Master_Retur_Beli WHERE no_beli=mb.no_faktur_beli),0) jumlah_retur", "mb.kode_supplier=sp.Kode AND mb.no_faktur_beli='".$id."'");
        $output = '';
        $i = 1;

        $data = $this->db->query("SELECT db.kode_barang, br.nm_brg, br.Deskripsi, br.barcode, db.jumlah_beli, db.harga_beli, db.ppn ppn_item, db.diskon disc1, db.disc2, db.disc3, db.disc4, br.satuan, isnull(dr.jml,0) jumlah_retur, isnull((SELECT hrg_jual_1 FROM barang_hrg WHERE barang=br.kd_brg AND lokasi='".$get_data['Lokasi']."' GROUP BY hrg_jual_1),0) harga_jual
                                            FROM det_beli db
                                            LEFT JOIN barang br ON ltrim(rtrim(db.kode_barang)) = ltrim(rtrim(br.kd_brg))
                                            LEFT JOIN Master_Retur_Beli mr ON db.no_faktur_beli=mr.no_beli
                                            LEFT JOIN Det_Retur_Beli dr ON dr.No_Retur=mr.No_Retur AND dr.kd_brg=db.kode_barang
                                            WHERE db.no_faktur_beli = '".$id."'")->result_array();

        foreach ($data as $row){
            $output .= '<tr><td class="isi center">'.$i.'</td><td class="isi">'.$row['kode_barang'].'</td><td class="isi">'.$row['barcode'].'</td><td class="isi">'.$row['nm_brg'].'</td><td class="isi center">'.($row['jumlah_beli']-$row['jumlah_retur']+0).'</td><td class="isi kanan">'.number_format($row['harga_jual']).'</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
            $i++;
        }
        $data2 = array(
            'title' => 'Form Alokasi',
            'table' => $output,
            'tanggal' => substr($get_data['tgl_beli'],0,10),
            'no_beli' => $get_data['no_faktur_beli'],
            'nota_supplier' => $get_data['noNota'],
            'supplier' => $get_data['kode_supplier'].' | '.$get_data['Nama']
        );

        //load the view and saved it into $html variable
        $html=$this->load->view('bo/Cetak/form_alokasi', $data2, true);

        //this the the PDF filename that user will get to download
        //$nama = $getSpk->kode_spk;
        $nama = 'form_alokasi';
        $pdfFilePath = $nama.".pdf";

        //load mPDF library
        $this->load->library('m_report_l');

        //generate the PDF from the given html
        $this->m_report_l->pdf->WriteHTML($html);

        //download it.
        $this->m_report_l->pdf->Output($pdfFilePath, "I");
    }

    public function barcode_packing($tmp_kode_packing) {
        $kode_packing = base64_decode($tmp_kode_packing);
        $get_data = $this->m_crud->get_data("master_packing mp, det_packing dp, Master_Mutasi mm", "mp.kd_packing, mp.no_faktur_mutasi, mp.tgl_packing, mp.pengirim, mp.operator, mp.box, mm.kd_lokasi_1, mm.kd_lokasi_2, COUNT(dp.kd_brg) jumlah_barang, SUM(dp.qty) total_barang", "mp.kd_packing=dp.kd_packing AND mp.no_faktur_mutasi=mm.no_faktur_mutasi AND mp.kd_packing='".$kode_packing."'", null, "mp.kd_packing, mp.no_faktur_mutasi, mp.tgl_packing, mp.pengirim, mp.operator, mp.box, mm.kd_lokasi_1, mm.kd_lokasi_2");

        $data = array(
            'title' => 'Cetak Barcode Packing',
            'data' => array(
                'kd_packing'=>$get_data['kd_packing'],
                'tgl_packing'=>$get_data['tgl_packing'],
                'faktur_mutasi'=>$get_data['no_faktur_mutasi'],
                'operator'=>$get_data['operator'],
                'pengirim'=>$get_data['pengirim'],
                'jumlah_barang'=>$get_data['jumlah_barang'],
                'total_barang'=>$get_data['total_barang'],
                'box'=>$get_data['box'],
                'lokasi_1'=>$get_data['kd_lokasi_1'],
                'lokasi_2'=>$get_data['kd_lokasi_2']
            )
        );

        $this->load->view('bo/Cetak/barcode_packing', $data);
    }



    public function nota_penjualan($kode) {
        $id = base64_decode($kode);
        $get_data = $this->m_crud->get_join_data("Master_Trx mt", "mt.kd_trx, mt.tgl, ud.nama nama_kasir, mt.ket_kas_lain, cs.Nama nama_customer, mt.Tempo, mt.Lokasi, mt.Jenis_Trx, mt.dis_rp", array(array("table"=>"Customer cs", "type"=>"LEFT"), array("table"=>"user_detail ud", "type"=>"LEFT")), array("mt.kd_cust=cs.kd_cust", "mt.kd_kasir=ud.user_id"), "mt.kd_trx='".$id."'");
        $output = '';
        $total = 0;
        $total_qty = 0;
        $dis_item = 0;
        $i = 1;
        $data = $this->m_crud->read_data('Det_Trx dtrx, barang br', 'dtrx.qty, dtrx.hrg_jual, dtrx.dis_persen, br.kd_brg, br.satuan, br.nm_brg', "dtrx.kd_brg = br.kd_brg AND dtrx.kd_trx = '".$id."'");
        foreach ($data as $row){
            $sub_total = ($row['qty'] * $row['hrg_jual'])-$row['dis_persen'];
            $total = $total + $sub_total;
            $dis_item = $dis_item + $row['dis_persen'];
            $total_qty = $total_qty + $row['qty'];
            $output .= '<tr><td class="isi center">'.($row['qty']+0).' '.$row['satuan'].'</td><td class="isi">'.$row['kd_brg'].'</td><td class="isi">'.$row['nm_brg'].'</td><td class="isi kanan">'.number_format($row['hrg_jual']).'</td><td class="isi kanan">'.number_format($sub_total).'</td>';
            $i++;
        }
        $data2 = array(
            'title' => 'Cetak Nota Penjualan',
            'table' => $output,
            'dis_rp' => $get_data['dis_rp'],
            'diskon_item' => $dis_item,
            'total' => $total-$get_data['dis_rp'],
            'total_qty' => $total_qty,
            'tanggal' => substr($get_data['tgl'],0,10),
            'no_jual' => $get_data['kd_trx'],
            'customer' => $get_data['nama_customer'],
            'jns_trx' => $get_data['Jenis_Trx'],
            'jatuh_tempo' => $get_data['Tempo'],
            'keterangan' => $get_data['ket_kas_lain'],
            'terbilang' => number_to_words($total)
        );

        //load the view and saved it into $html variable
        $html=$this->load->view('bo/Cetak/nota_penjualan', $data2, true);

        //this the the PDF filename that user will get to download
        //$nama = $getSpk->kode_spk;
        $nama = $get_data['kd_trx'];
        $pdfFilePath = $nama.".pdf";

        //load mPDF library
        $this->load->library('m_report_p');

        //generate the PDF from the given html
        $this->m_report_p->pdf->WriteHTML($html);

        //download it.
        $this->m_report_p->pdf->Output($pdfFilePath, "D");
    }

    public function nota_retur_3ply($kode_retur_) {
        $kode_retur = base64_decode($kode_retur_);

        //$this->m_website->add_activity($kode_retur, 'R');

        $data = array(
            'title' => 'Nota Retur',
            'no_retur' => $kode_retur
        );
        $this->load->view('bo/Cetak/nota_retur_pembelian_3ply', $data);
    }

    public function barcode_barang($kode_trx_, $type_=null) {
        $kode_trx = base64_decode($kode_trx_);
        $type = base64_decode($type_);

        $barcode = array();

        if ($type == 'pembelian') {
            $data_barang = $this->m_crud->read_data("barang br, master_beli mb, det_beli db", "br.kd_brg, br.barcode, br.nm_brg, br.Group1, br.kel_brg, br.Deskripsi, br.hrg_jual_1, db.jumlah_beli qty", "mb.no_faktur_beli=db.no_faktur_beli AND db.kode_barang=br.kd_brg AND mb.no_faktur_beli='".$kode_trx."'");
        } else if ($type == 'p_order') {
            $data_barang = $this->m_crud->read_data("barang br, master_po mp, detail_po dp", "br.kd_brg, br.barcode, br.nm_brg, br.Group1, br.kel_brg, br.Deskripsi, dp.harga_jual hrg_jual_1, dp.jumlah_beli qty", "mp.no_po=dp.no_po AND dp.kode_barang=br.kd_brg AND mp.no_po='".$kode_trx."'");
        } else if ($type == 'alokasi') {
            $data_barang = $this->m_crud->read_data("barang br, Master_Mutasi mm, Det_Mutasi dm", "br.kd_brg, br.barcode, br.nm_brg, br.Group1, br.kel_brg, br.Deskripsi, br.hrg_jual_1, dm.qty", "mm.no_faktur_mutasi=dm.no_faktur_mutasi AND dm.kd_brg=br.kd_brg AND mm.no_faktur_mutasi='".$kode_trx."'");
        } else if ($type == 'delivery_note') {
            $data_barang = $this->m_crud->read_data("barang br, master_delivery_note mm, det_delivery_note dm", "br.kd_brg, br.barcode, br.nm_brg, br.Group1, br.kel_brg, br.Deskripsi, br.hrg_jual_1, dm.qty", "mm.no_delivery_note=dm.no_delivery_note AND dm.kd_brg=br.kd_brg AND mm.no_delivery_note='".$kode_trx."'");
        } else {
            $data_barang = $this->m_crud->read_data("barang br, master_packing mp, det_packing dp", "br.kd_brg, br.barcode, br.nm_brg, br.Group1, br.kel_brg, br.Deskripsi, br.hrg_jual_1, dp.qty", "mp.kd_packing=dp.kd_packing AND dp.kd_brg=br.kd_brg AND mp.kd_packing='".$kode_trx."'");
            //$data_barang = $this->m_crud->read_data("barang br, barang_hrg brh, master_beli mb, det_beli db", "br.kd_brg, br.barcode, br.nm_brg, br.Group1, br.kel_brg, brh.hrg_jual_1, db.jumlah_beli", "mb.no_faktur_beli=db.no_faktur_beli AND db.kode_barang=br.kd_brg AND br.kd_brg=brh.barang AND mb.Lokasi=brh.lokasi AND mb.no_faktur_beli='".$kode_trx."'");
        }

        foreach ($data_barang as $row) {
            for ($qty=0; $qty<$row['qty']; $qty++) {
                array_push($barcode, array('barcode' => $row['kd_brg'], 'nm_brg' => $row['nm_brg'], 'gr1' => $row['Group1'], 'kel_brg' => $row['Deskripsi'], 'hrg_jual' => ($row['hrg_jual_1'] + 0)));
            }
        }

        $data = array(
            'title' => 'Cetak Barcode Barang',
            'data_barcode' => $barcode
        );
        $this->load->view('bo/Cetak/barcode_barang', $data);
    }

    public function cetak_barcode_custom() {
        $get_barcode = $this->m_crud->read_data("tr_temp_d", "*", "d1='barcode_custom' AND d9='".$this->user."'");

        $barcode = array();

        foreach ($get_barcode as $row) {
            for ($qty=0; $qty<$row['d7']; $qty++) {
                array_push($barcode, array('barcode' => $row['d2'], 'nm_brg' => $row['d4'], 'gr1' => $row['d5'], 'kel_brg' => $row['d6'], 'hrg_jual' => ($row['d8'] + 0)));
            }
        }

        $data = array(
            'title' => 'Cetak Barcode Custom',
            'data_barcode' => $barcode
        );
        $this->load->view('bo/Cetak/barcode_barang', $data);
    }

    public function cetak_barcode() {
        $get_barcode = $this->m_crud->read_data("tr_temp_d", "*", "d1='barcode' AND d9='".$this->user."'");

        $barcode = array();

        foreach ($get_barcode as $row) {
            for ($qty=0; $qty<$row['d7']; $qty++) {
                array_push($barcode, array('barcode' => $row['d2'], 'nm_brg' => $row['d4'], 'gr1' => $row['d5'], 'kel_brg' => $row['d6'], 'hrg_jual' => ($row['d8'] + 0)));
            }
        }

        $data = array(
            'title' => 'Cetak Barcode Barang',
            'data_barcode' => $barcode
        );
        $this->load->view('bo/Cetak/barcode_barang', $data);
    }

    public function cetak_price_tag() {
        $get_barcode = $this->m_crud->read_data("tr_temp_d", "*", "d1='price_tag' AND d9='".$this->user."'");

        $barcode = array();

        foreach ($get_barcode as $row) {
            for ($qty=0; $qty<$row['d7']; $qty++) {
                array_push($barcode, array('kd_brg' => $row['d2'], 'barcode' => $row['d3'], 'nm_brg' => $row['d4'], 'gr1' => $row['d5'], 'kel_brg' => $row['d6'], 'hrg_jual' => ($row['d8'] + 0)));
            }
        }

        /*
        $lokasi_barang=$this->session->search['lokasi_barang']; $group1=$this->session->search['group1']; $group2=$this->session->search['group2']; $harga_baru=$this->session->search['harga_baru'];

        $where=null;
        if(isset($lokasi_barang)&&$lokasi_barang!=null){ ($where==null)?null:$where.=" and "; $where.="(ks.lokasi = '".$lokasi_barang."')"; }
        if(isset($group1)&&$group1!=null){ ($where==null)?null:$where.=" and "; $where.="(br.group1 = '".$group1."')"; }
        if(isset($group2)&&$group2!=null){ ($where==null)?null:$where.=" and "; $where.="(br.group2 = '".$group2."')"; }
        if(isset($harga_baru)&&$harga_baru==1){ ($where==null)?null:$where.=" and "; $where.="(br.hrg_jual_1 <> br.hrg_sebelum)"; }

        $barcode = $this->m_crud->join_data('barang br',
            "br.kd_brg, br.barcode, br.nm_brg, br.hrg_jual_1 hrg_jual",
            array("kartu_stock ks", "group1 g1", "group2 g2"), array("br.kd_brg=ks.kd_brg", "br.group1=g1.kode", "br.group2=g2.kode"),
            $where, "br.tgl_update desc", "br.kd_brg, br.barcode, br.nm_brg, br.hrg_jual_1, br.tgl_update"
        );
        */

        if($barcode==null){ $barcode = array(); }
        $data = array(
            'title' => 'Cetak Price Tag',
            'data_barcode' => $barcode
        );
        $this->load->view('bo/Cetak/price_tag', $data);
    }

    public function cetak_barcode_packing() {
        $get_barcode = $this->m_crud->read_data("tr_temp_d", "*", "d1='barcode_packing' AND d9='".$this->user."'");

        $barcode = array();

        foreach ($get_barcode as $row) {
            for ($qty=0; $qty<$row['d7']; $qty++) {
                array_push($barcode, array('barcode' => $row['d3'], 'kd_brg' => $row['d2'], 'art' => $row['d6'], 'packing' => $row['d12'], 'qty_packing' => $row['d13'], 'nm_brg' => $row['d4'], 'gr1' => $row['d5'], 'kel_brg' => $row['d6'], 'hrg_jual' => ($row['d8'] + 0)));
            }
        }

        $data = array(
            'title' => 'Cetak Barcode Packing Barang',
            'data_barcode' => $barcode
        );

        $this->load->view('bo/Cetak/barcode_packing_barang', $data);
    }

    public function delivery_note($id) {
        $data['row'] = $this->m_crud->get_data('master_delivery_note mm, det_delivery_note dm', "tanggal, mm.no_delivery_note, kd_lokasi_1, kd_lokasi_2, kd_kasir, mm.status, isnull(mm.no_faktur_beli, '-') no_faktur_beli, keterangan", "LEFT(mm.no_delivery_note, 2)='DN' AND mm.no_delivery_note=dm.no_delivery_note AND mm.no_delivery_note='".base64_decode($id)."'");

        $this->load->view('bo/Cetak/delivery_note', $data);
    }

    public function alokasi($id) {
        $data['row'] = $this->m_crud->get_data('Master_Mutasi mm, Det_Mutasi dm', "tgl_mutasi, mm.no_faktur_mutasi, kd_lokasi_1, kd_lokasi_2, kd_kasir, mm.status, isnull(mm.no_faktur_beli, '-') no_faktur_beli, keterangan", "LEFT(mm.no_faktur_mutasi, 2)='MU' AND mm.no_faktur_mutasi=dm.no_faktur_mutasi AND mm.no_faktur_mutasi='".base64_decode($id)."'");

        $this->load->view('bo/Cetak/alokasi', $data);
    }

    public function alokasi_by_cabang($id) {
        $data['row'] = $this->m_crud->get_data('Master_Mutasi mm, Det_Mutasi dm', "mm.tgl_mutasi, mm.no_faktur_mutasi, kd_lokasi_1, kd_lokasi_2, kd_kasir, mm.status", "LEFT(mm.no_faktur_mutasi, 2)='MC' AND mm.no_faktur_mutasi=dm.no_faktur_mutasi AND mm.no_faktur_mutasi='".base64_decode($id)."'");

        $this->load->view('bo/Cetak/alokasi_by_cabang', $data);
    }

    public function adjustment($id) {
        $data['row'] = $this->m_crud->get_data('adjust ad, user_detail ud, det_adjust da', "ad.tgl, ad.kd_trx, ad.keterangan, ad.lokasi, ud.nama", "ad.kd_kasir=ud.user_id AND ad.kd_trx=da.kd_trx AND ad.kd_trx='".base64_decode($id)."'");

        $this->load->view('bo/cetak/adjustment', $data);
    }

    public function packing($id) {
        $data['row'] = $this->m_crud->get_data('Master_Mutasi mm, master_packing mp, det_packing dp', "isnull((select dn.no_faktur_beli from master_delivery_note dn where dn.no_delivery_note=mm.no_faktur_beli),'-') no_faktur_beli, mp.tgl_packing, mp.kd_packing, mp.status, mp.pengirim, mp.penerima, mp.operator, mm.no_faktur_mutasi, mm.kd_lokasi_1, mm.kd_lokasi_2, isnull((select top 1 kd_packing from det_expedisi where kd_packing=mp.kd_packing),'0') expedisi", "mm.no_faktur_mutasi=mp.no_faktur_mutasi AND mp.kd_packing=dp.kd_packing AND mp.kd_packing='".base64_decode($id)."'");

        $this->load->view('bo/cetak/packing', $data);
    }

    public function expedisi($id) {
        $data['row'] = $this->m_crud->get_join_data('master_expedisi me', "me.status, me.kd_expedisi, me.tgl_expedisi, me.pengirim, l1.nama nama_lokasi_asal, l2.nama nama_lokasi_tujuan, ud.nama nama_operator", array('lokasi l1', 'lokasi l2', 'user_detail ud'), array('l1.kode=me.lokasi_asal', 'l2.kode=me.lokasi_tujuan', 'ud.user_id=me.operator'), "me.kd_expedisi='".base64_decode($id)."'");

        $this->load->view('bo/cetak/expedisi', $data);
    }

    public function bayar_hutang($id) {
        $data['row'] = $this->m_crud->get_data('bayar_hutang bh, master_beli mb, supplier sp', "bh.no_nota, bh.fak_beli, bh.tgl_byr, bh.cara_byr, bh.jumlah, bh.kasir, bh.nm_bank, mb.tgl_jatuh_tempo, mb.noNota, bh.bulat, bh.nogiro, bh.tgl_cair_giro, bh.ket, sp.Nama", "bh.fak_beli=mb.no_faktur_beli and mb.kode_supplier=sp.kode AND bh.no_nota='".base64_decode($id)."'");

        $this->load->view('bo/cetak/bayar_hutang', $data);
    }

    public function pembelian_barang($id) {
        $column = "pr.no_faktur_beli, pr.tgl_beli, pr.noNota, pr.nama_penerima, pr.type, pr.Pelunasan, pr.disc, pr.ppn, sp.Kode kode_supplier, sp.Nama supplier, ud.Nama operator, pr.lokasi kd_lokasi, lk.Nama lokasi, lk.serial";
        $join = array(array('table'=>'Supplier sp', 'type'=>'LEFT'), array('table'=>'Lokasi lk', 'type'=>'LEFT'), array('table'=>'user_detail ud', 'type'=>'LEFT'));
        $on = array('pr.kode_supplier=sp.kode', 'pr.Lokasi=lk.Kode', 'pr.Operator=ud.user_id');
        $group = "pr.no_faktur_beli, pr.tgl_beli, pr.noNota, pr.nama_penerima, pr.type, pr.Pelunasan, pr.disc, pr.ppn, sp.Kode, sp.Nama, ud.Nama, lk.Nama, pr.lokasi, lk.serial, pr.operator";
        $data['row'] = $this->m_crud->get_join_data("pembelian_report pr", $column." ,SUM(jumlah_beli) qty_beli, SUM(sub_total)-disc+ppn total_beli", $join, $on, "pr.no_faktur_beli='".base64_decode($id)."'", null, $group);

        $this->load->view('bo/cetak/pembelian_barang', $data);
    }

    public function cetak_pembelian($id) {
        $id = base64_decode($id);

        $get_data = $this->m_crud->get_data("master_beli mb, Supplier sp", "mb.no_po, mb.tgl_beli, mb.no_faktur_beli, mb.Operator, mb.type, mb.kode_supplier, mb.noNota, mb.nama_penerima, mb.Lokasi, mb.nilai_pembelian, mb.total_pembelian, mb.disc, mb.ppn, sp.Nama, isnull((SELECT SUM(Total) FROM Master_Retur_Beli WHERE no_beli=mb.no_faktur_beli),0) jumlah_retur", "mb.kode_supplier=sp.Kode AND mb.no_faktur_beli='".$id."'");

        $data = $this->db->query("
            SELECT db.kode_barang, br.nm_brg, br.Deskripsi, br.barcode, db.jumlah_beli, isnull(db.jumlah_bonus, 0) jumlah_bonus, db.harga_beli, db.ppn ppn_item, db.diskon disc1, db.disc2, db.disc3, db.disc4, br.satuan, isnull(dr.jml,0) jumlah_retur, isnull((SELECT hrg_jual_1 FROM barang_hrg WHERE barang=br.kd_brg AND lokasi='".$get_data['Lokasi']."' GROUP BY hrg_jual_1),0) harga_jual
            FROM det_beli db
            LEFT JOIN barang br ON ltrim(rtrim(db.kode_barang)) = ltrim(rtrim(br.kd_brg))
            LEFT JOIN Master_Retur_Beli mr ON db.no_faktur_beli=mr.no_beli
            LEFT JOIN Det_Retur_Beli dr ON dr.No_Retur=mr.No_Retur AND dr.kd_brg=db.kode_barang
            WHERE db.no_faktur_beli = '".$id."'
        ")->result_array();

        $sub_total = 0;
        $qty = 0;
        $list = '';

        foreach ($data as $key => $row){
            if ((int)$row['jumlah_bonus'] > 0) {
                $bonus = ' + '.(int)$row['jumlah_bonus'];
            } else {
                $bonus = '';
            }
            //$hitung_netto = ((int)$row['jumlah_beli']-(int)$row['jumlah_retur']) * $row['harga_beli'];
            $hitung_netto = ((int)$row['jumlah_beli']) * $row['harga_beli'];
            $disc = $this->m_website->double_diskon($hitung_netto, array($row['disc1'], $row['disc2']));
            $hitung_sub_total = $this->m_website->grand_total_ppn($disc, 0, $row['ppn_item']);
            $sub_total = $sub_total + $hitung_sub_total;
            //$qty = $qty + ((int)$row['jumlah_beli']-(int)$row['jumlah_retur'])+(int)$row['jumlah_bonus'];
            $qty = $qty + ((int)$row['jumlah_beli'])+(int)$row['jumlah_bonus'];

            $d1 = $row['harga_beli']*(1-($row['disc1']/100));
            $hrg_beli = $d1*(1-($row['disc2']/100));
            if (ltrim(rtrim($row['barcode']))==ltrim(rtrim($row['kode_barang']))) {
                $brcd_art = $row['Deskripsi'];
            } else {
                $brcd_art = $row['barcode'];
            }
            $list .= '<tr'.($key==count($data)-1?' class="border_bottom"':'').'>
                <td>'.($key+1).'</td>
                <td>('.$row['barcode'].') '.$row['nm_brg'].'</td>
                <td>'.((int)$row['jumlah_beli']).$bonus.' '.$row['satuan'].'</td>
                <td align="right">'.number_format($row['harga_beli'], 0, ',', '.').'</td>
                <td align="center">'.($row['disc1']+0).'</td>
                <td align="center">'.($row['disc2']+0).'</td>
                <td align="center">'.($row['ppn_item']+0).'</td>
                <td align="right">'.number_format($hitung_sub_total, 0, ',', '.').'</td></tr>';
        }
        $total = ($sub_total - $get_data['disc']) + (($get_data['ppn']/100) * $sub_total);

        $get_data["total_beli"] = $total;
        $get_data["terbilang"] = number_to_words($total);

        $list .= '<tr class="border_bottom">
            <td colspan="2">Total</td>
            <td>'.(int)$qty.'</td>
            <td colspan="4"></td>
            <td align="right">'.number_format($sub_total, 0, ',', '.').'</td>
        </tr>';

        $this->load->view("bo/Cetak/cetak_pembelian", array("title"=>$get_data, "list"=>$list));
    }

    public function cetak_penjualan($id) {
        $id = base64_decode($id);

        $get_data = $this->m_crud->get_join_data("Master_Trx mt", "mt.kd_trx, mt.tgl, ud.nama nama_kasir, mt.ket_kas_lain, cs.Nama nama_customer, mt.Tempo, mt.Lokasi, mt.Jenis_Trx, mt.dis_rp, mt.tax, mt.bayar, mt.change, mt.kartu, mt.jns_kartu, mt.jml_kartu", array(array("table"=>"Customer cs", "type"=>"LEFT"), array("table"=>"user_detail ud", "type"=>"LEFT")), array("mt.kd_cust=cs.kd_cust", "mt.kd_kasir=ud.user_id"), "mt.kd_trx='".$id."'");
        $list = '';
        $total = 0;
        $total_qty = 0;
        $dis_item = 0;
        $data = $this->m_crud->read_data('Det_Trx dtrx, barang br', 'dtrx.qty, dtrx.hrg_jual, dtrx.dis_persen, br.kd_brg, br.barcode, br.satuan, br.nm_brg', "dtrx.kd_brg = br.kd_brg AND dtrx.kd_trx = '".$id."'");
        foreach ($data as $key => $row){
            $sub_total = ($row['qty'] * $row['hrg_jual'])-$row['dis_persen'];
            $total = $total + $sub_total;
            $dis_item = $dis_item + $row['dis_persen'];
            $total_qty = $total_qty + $row['qty'];
            $list .= '<tr'.($key==count($data)-1?' class="border_bottom"':'').'>
                <td>'.($key+1).'</td>
                <td>('.$row['barcode'].') '.$row['nm_brg'].'</td>
                <td>'.((int)$row['qty']).' '.$row['satuan'].'</td>
                <td align="right">'.number_format($row['hrg_jual'], 0, ',', '.').'</td>
                <td align="right">'.number_format($sub_total, 0, ',', '.').'</td></tr>';
        }

        $get_data["total_jual"] = $total;
        $get_data["terbilang"] = number_to_words($total);

        $list .= '<tr class="border_bottom">
            <td colspan="2">Total</td>
            <td>'.(int)$total_qty.'</td>
            <td></td>
            <td align="right">'.number_format($total, 0, ',', '.').'</td>
        </tr>';

        $this->load->view("bo/Cetak/cetak_penjualan", array("title"=>$get_data, "list"=>$list));
    }
}