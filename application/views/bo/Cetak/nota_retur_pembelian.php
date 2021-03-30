<!doctype html>
<html lang="en">
<head>
    <?php $this->load->view('bo/head'); ?>
    <style type="text/css">
        body {
			font-family: "OpenSans-Regular", "Lucida Sans Typewriter", "Lucida Typewriter", "Arial", "Helvetica", "sans-serif";
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
        }

        .isi {
            padding: 1mm 1mm 1mm 1mm;
        }

        th, td{
            font-size: 10pt;
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
<div class="row"><img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$no_retur?>"></div>
<table style="height: 10cm" width="100%" border="1">
    <thead>
    <tr>
        <th colspan="4" class="h1"><img class="img_head" src="<?=$this->config->item('url').$this->m_website->site_data()->logo?>"></th>
        <th rowspan="3" class="judul">NOTA RETUR</th>
        <th class="h2" colspan="3">Tgl Retur</th>
        <th class="h2">: <?php echo $tanggal ?></th>
    </tr>
    <tr>
        <th rowspan="2" colspan="4" class="h2 alamat"><?php echo $this->m_website->address() ?></th>
        <th class="h2" colspan="3">No. Retur</th>
        <th class="h2">: <?php echo $no_retur ?></th>
    </tr>
    <tr>
        <th class="h2" colspan="3">Kode Supplier</th>
        <th class="h2">: <?php echo $kode_supplier ?></th>
    </tr>
    <tr>
        <th colspan="2" class="h3">Retur ke</th>
        <th class="h3" colspan="3">: <?php echo $nama_supplier ?></th>
        <th rowspan="2" colspan="4" class="h3"></th>
    </tr>
    <tr>
        <th colspan="2" class="h3" style="border-top-color: transparent">Nota Supplier</th>
        <th class="h3" colspan="4" style="border-top-color: transparent">: <?php echo $nota_supplier ?></th>
    </tr>
    <tr>
        <th colspan="2" class="h3" style="border-top-color: transparent">Operator</th>
        <th class="h3" colspan="4" style="border-top-color: transparent">: <?php echo $this->m_website->get_nama_user($operator) ?></th>
    </tr>
    <tr>
        <th colspan="2" class="h3" style="border-top-color: transparent">Lokasi Cabang</th>
        <th class="h3" colspan="4" style="border-top-color: transparent">: <?php echo $lokasi_cabang ?></th>
    </tr>
    </thead>
</table>
<table width="100%" border="1">
    <thead>
    <tr>
        <th class="tengah" style="width: 5%">NO</th>
        <th class="tengah">KODE BARANG</th>
        <th class="tengah">BARCODE</th>
        <th class="tengah">DESKRIPSI BARANG</th>
        <th class="tengah">KELOMPOK BARANG</th>
        <th class="tengah">QTY</th>
        <th class="tengah">SATUAN</th>
        <th class="tengah">KONDISI</th>
        <th class="tengah">HARGA BELI</th>
        <th class="tengah">SUB TOTAL</th>
    </tr>
    </thead>
    <tbody>
    <?php
    echo $table;
    ?>
    </tbody>
    <tr>
        <th class="kanan" colspan="5">TOTAL</th>
        <th class="tengah"><?php echo $total_qty; ?></th>
        <th colspan="3"></th>
        <th class="kanan"><?php echo number_format($total); ?></th>
    </tr>
    </table>
    <table width="100%" border="1">
    <thead>
    <tr>
        <td colspan="4" class="borderLR borderTB"><div class="container_terbilang"><div class="terbilang"><i><?php echo $terbilang; ?></i></div></div></td>
        <td class="h3 borderTB"></td>
        <td class="tebal borderLR borderTB kanan"></td>
        <td class="tengah tebal borderLR borderTB kanan" style="width: 3cm"></td>
    </tr>
    <!--<tr>
        <th colspan="5" class="h3 isi atas borderTB borderLR"></th>
        <td class="kanan atas borderLR borderLR" colspan="2" style="border: transparent"><?php echo date("d M Y"); ?></td>
    </tr>
    <tr>
        <td class="borderLR borderTB" colspan="2" style="text-align: center">Penerima</td>
        <td class="borderLR borderTB" colspan="2" style="text-align: center">Pengirim</td>
        <td class="borderLR borderTB" colspan="3" style="text-align: center">Admin</td>
    </tr>
    <tr>
        <td class="borderLR borderTB" colspan="2" style="text-align: center; padding-top: 2cm"><?php echo '_____________' ?></td>
        <td class="borderLR borderTB" colspan="2" style="text-align: center; padding-top: 2cm"><?php echo '_____________' ?></td>
        <td class="borderLR borderTB" colspan="3" style="text-align: center; padding-top: 2cm"><?php echo '_____________' ?></td>
    </tr>
    <tr>
        <td class="borderLR borderTB" colspan="7" style="padding-top: 2mm">Ket : <?=$keterangan?></td>
    </tr>-->
    </thead>
</table>
<table width="100%">
	<tr>
		<td style="text-align:center;"></td>
		<td style="text-align:center;"></td>
		<td style="text-align:center;"></td>
		<td style="text-align:center;" class="atas borderLR borderLR"><?php echo date("d M Y"); ?></td>
	</tr>
	<tr>
		<td style="padding-top: 2cm; text-align:center;">____________________<br/>Bag. Penerimaan</td>
		<td style="padding-top: 2cm; text-align:center;">____________________<br/>EDP In</td>
		<td style="padding-top: 2cm; text-align:center;">____________________<br/>Bag. Gudang Retur</td>
		<td style="padding-top: 2cm; text-align:center;">____________________<br/>Mengetahui</td>
	</tr>
	<tr>
        <td class="borderLR borderTB" colspan="4" style="padding-top: 2mm">Ket : <?=$keterangan?></td>
    </tr>
</table>
<span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($no_retur)?></span>
</body>
</html>