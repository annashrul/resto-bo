<!doctype html>
<html lang="en">
<head>
    <?php $this->load->view('bo/head'); ?>
    <style type="text/css">
        body {
            font-family: "Courier New";
        }

        .img_head {
            width: 2.5cm;
        }

        .h1 {
            font-size: 12pt;
            font-weight: bold;
            text-align: left;
            color: black;
            border: transparent;
            padding-bottom: 3pt;
        }

        .h2 {
            font-size: 10pt;
            font-weight: normal;
            text-align: left;
            color: black;
            border: transparent;
            padding-bottom: 3pt;
        }

        .h3 {
            font-size: 10pt;
            font-weight: normal;
            text-align: left;
            color: black;
            padding-bottom: 3pt;
            border-bottom-color: transparent;
            border-left-color: transparent;
            border-right-color: transparent;
        }

        .judul {
            width: 7.5cm;
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            color: black;
            border: transparent;
        }

        .tebal {
            font-size: 9pt;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .tengah {
            font-size: 8pt;
            text-align: center;
            padding: 1mm 1mm 1mm 1mm;
        }

        .atas {
            padding-top: 20px;
        }

        .kiri {
            text-align: left;
        }

        .kanan {
            text-align: right;
            padding-right: 1mm;
        }

        .isi {
            padding-left: 1mm;
        }

        th, td{
            font-size: 7pt !important;
            color: black;
        }

        .borderTB {
            border-bottom-color: transparent;
            border-top-color: transparent;
        }

        .borderLR {
            border-left-color: transparent;
            border-right-color: transparent;
        }

        .alamat {
            font-size: 8pt;
            vertical-align: top;
            width: 3cm;
            table-layout: fixed
        }

        .container_terbilang {
            position: fixed;
            margin-top: 0.2cm;
            width: 100%;
        }

        .terbilang {
            padding: 5px;
            border: dotted;
            border-color: black;
            width: 55.4%;
        }

        .detail -> thead -> tr -> th {
            font-size: 8pt;
        }

        /*td{
            padding: 5px 5px 5px;
        }

        th{
            padding: 2px 2px 2px;
            text-align: center;
        }*/

        .foot {
            text-align: left;
        }
    </style>
</head>
<body>
<table style="height: 10cm" width="100%" border="1">
    <thead>
    <tr>
        <th colspan="4" class="h1"><img class="img_head" src="<?=base_url().'assets/images/site/'.$this->m_website->site_data()->logo?>"></th>
        <th rowspan="3" class="judul">NOTA PEMBELIAN</th>
        <th class="h2" colspan="3">Tgl Pembelian</th>
        <th class="h2">: <?php echo $tanggal ?></th>
    </tr>
    <tr>
        <th rowspan="2" colspan="4" class="h2 alamat"><?php echo $this->m_website->address() ?></th>
        <th class="h2" colspan="3">Faktur No.</th>
        <th class="h2">: <?php echo $no_faktur ?></th>
    </tr>
    <tr>
        <th class="h2" colspan="3">Kode Supplier</th>
        <th class="h2">: <?php echo $kode_supplier ?></th>
    </tr>
    <tr>
        <th colspan="2" class="h3">Pembelian dari</th>
        <th class="h3" colspan="3">: <?php echo $nama_supplier ?></th>
        <th rowspan="2" colspan="4" class="h3"></th>
    </tr>
    <tr>
        <th colspan="2" class="h3" style="border-top-color: transparent">Nota Supplier</th>
        <th class="h3" colspan="3" style="border-top-color: transparent">: <?php echo $no_nota ?></th>
    </tr>
    <tr>
        <th colspan="2" class="h3" style="border-top-color: transparent">Tipe</th>
        <th class="h3" colspan="3" style="border-top-color: transparent">: <?php echo $tipe ?></th>
    </tr>
    <tr>
        <th colspan="2" class="h3" style="border-top-color: transparent">Sejumlah uang</th>
        <th class="h3" colspan="3" style="border-top-color: transparent">: <?php echo $total_pembelian ?></th>
    </tr>
    </thead>
</table>
<table width="100%" border="1">
    <thead>
    <tr>
        <th class="tengah" style="width: 5%">NO</th>
        <th class="tengah">KODE BARANG</th>
        <th class="tengah">DESKRIPSI BARANG</th>
        <th class="tengah">SATUAN</th>
        <th class="tengah">HARGA BELI</th>
        <th class="tengah">HARGA JUAL</th>
        <th class="tengah">MARGIN %</th>
        <th class="tengah">DISKON %</th>
        <th class="tengah">QTY</th>
        <th class="tengah">PPN</th>
        <th class="tengah">SUB TOTAL</th>
    </tr>
    </thead>
    <tbody>
    <?php

    $sub_total = 0;
    $i = 1;

    foreach ($list as $row){
        $hitung_netto = ((int)$row['jumlah_beli']-(int)$row['jumlah_retur']) * $row['harga_beli'];
        $disc = $this->m_website->double_diskon($hitung_netto, array($row['disc1'], $row['disc2'], $row['disc3'], $row['disc4']));
        $hitung_sub_total = $this->m_website->grand_total_ppn($disc, 0, $row['ppn']);
        $sub_total = $sub_total + $hitung_sub_total;
        echo '<tr><td class="isi center">'.$i.'</td><td class="isi">'.$row['kode_barang'].'</td><td class="isi">'.$row['nm_brg'].'</td><td class="isi">'.$row['satuan'].'</td><td class="isi kanan">'.number_format($row['harga_beli'],2).'</td><td class="isi kanan">'.number_format($row['harga_jual'],2).'</td><td class="isi center">'.(($row['harga_beli']>0 && $row['harga_beli']<$row['harga_jual'])?round((1 - ($row['harga_beli']/$row['harga_jual']))*100, 2):'').'</td><td class="isi center">'.($row['disc1']+0).'</td><td class="isi center">'.((int)$row['jumlah_beli']-(int)$row['jumlah_retur']).'</td><td class="isi center">'.($row['ppn']+0).'</td><td class="isi kanan">'.number_format($hitung_sub_total,2).'</td></tr>';
        $i++;
    }
    $total = ($sub_total - $get_data['disc']) + (($get_data['ppn']/100) * $sub_total);
    ?>
    </tbody>
    </table>
    <table width="100%" border="1">
    <tbody>
    <tr>
        <td colspan="4" class="borderLR borderTB"><div style="<?=($i>35)?'position: absolute !important; margin-top: 5cm !important':''?>" class="container_terbilang"><div class="terbilang"><i><?php echo $terbilang; ?></i></div></div></td>
        <td class="h3 borderTB"></td>
        <td class="tebal borderLR borderTB kanan">SUB TOTAL</td>
        <td class="tengah tebal borderLR borderTB kanan" style="width: 3cm"><?php echo number_format($sub_total, 2); ?></td>
    </tr>
    <tr>
        <td colspan="5" class="h3 borderTB"></td>
        <td class="tebal borderLR borderTB kanan">DISKON</td>
        <td class="tengah tebal borderLR borderTB kanan"><?php echo ($diskon==null?0:number_format($diskon,2)); ?></td>
    </tr>
    <tr>
        <td colspan="5" class="h3 borderTB"></td>
        <td class="tebal borderLR borderTB kanan">PPN</td>
        <td class="tengah tebal borderLR borderTB kanan"><?php echo ($ppn==null?0:($ppn+0)); ?></td>
    </tr>
    <tr>
        <td colspan="5" class="h3 borderTB"></td>
        <td class="tebal borderLR borderTB kanan">GRAND TOTAL</td>
        <td class="tengah tebal borderLR borderTB kanan"><?php echo number_format($total, 2); ?></td>
    </tr>
    </tbody>	
    <tr>
        <th colspan="5" class="h3 isi atas borderTB borderLR"></th>
        <td class="kanan atas borderLR borderLR" colspan="2" style="border: transparent"><?php echo date("d M Y"); ?></td>
    </tr>
    <tr>
        <th colspan="5" class="h3 isi borderLR borderTB" style="padding-top: 3cm"></th>
        <td class="kanan borderLR borderTB" colspan="2"><?php echo '_____________' ?></td>
    </tr>
</table>
</body>
</html>