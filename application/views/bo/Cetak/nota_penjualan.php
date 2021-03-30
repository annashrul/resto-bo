<!doctype html>
<html lang="en">
<head>
    <?php $this->load->view('bo/head'); ?>
    <style type="text/css">
        body {
			font-family: "OpenSans-Regular", "Lucida Sans Typewriter", "Lucida Typewriter", "Arial", "Helvetica", "sans-serif";
		}

        .img_head {
            height: 2.5cm;
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
            width: 7cm;
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
<table style="height: 10cm; position: relative;" width="100%" border="1">
    <thead>
    <tr>
        <th colspan="4" rowspan="3" class="h1"><img class="img_head" style="position: absolute;" src="<?=$this->config->item('url').$this->m_website->site_data()->logo?>"></th>
        <th rowspan="3" class="judul">NOTA PENJUALAN</th>
        <th class="h2" colspan="3">Tgl Jual</th>
        <th class="h2">: <?php echo $tanggal ?></th>
    </tr>
    <tr>
        <th class="h2" colspan="3">Kode Trx.</th>
        <th class="h2">: <?php echo $no_jual ?></th>
    </tr>
    <tr>
        <th class="h2" colspan="3">Customer</th>
        <th class="h2">: <?php echo $customer ?></th>
    </tr>
    <tr>
        <th colspan="2" class="h3">Jenis Trx.</th>
        <th class="h3" colspan="3">: <?php echo $jns_trx ?></th>
        <th rowspan="2" colspan="4" class="h3"></th>
    </tr>
    <tr>
        <th colspan="2" class="h3" style="border-top-color: transparent">Keterangan</th>
        <th class="h3" colspan="4" style="border-top-color: transparent">: <?php echo $keterangan ?></th>
    </tr>
    <?php
    if ($jns_trx == 'Kredit') {
        ?>
        <tr>
            <th colspan="2" class="h3" style="border-top-color: transparent">Jatuh Tempo</th>
            <th class="h3" colspan="4" style="border-top-color: transparent">: <?php echo $jatuh_tempo ?></th>
        </tr>
        <?php
    }
    ?>
    </thead>
</table>
<table width="100%" border="1">
    <thead>
    <tr>
        <th class="tengah">Banyaknya</th>
        <th class="tengah">KODE BARANG</th>
        <th class="tengah">NAMA BARANG</th>
        <th class="tengah">HARGA @ Rp</th>
        <th class="tengah">Jumlah</th>
    </tr>
    </thead>
    <tbody>
    <?php
    echo $table;
    ?>
    </tbody>
    <tr>
        <th colspan="3"><?php echo $terbilang; ?></th>
        <th class="kanan">Total Rp</th>
        <th class="kanan"><?php echo number_format($total); ?></th>
    </tr>
</table>
    <table width="100%" border="1" style="margin-top: 0.5cm">
    <thead>
    <tr>
        <th class="isi atas tengah borderTB borderLR" style="width: 3cm">Tanda Terima,</th>
        <th class="isi tengah" style="width: 4cm">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</th>
        <th class="isi atas tengah borderTB borderLR" style="width: 3cm">Hormat kami,</th>
    </tr>
    <tr>
        <td class="borderLR borderTB tengah" style="height: 2cm"><?php echo '(_____________)' ?></td>
        <td class="borderLR borderTB"></td>
        <td class="borderLR borderTB tengah"><?php echo '(_____________)' ?></td>
    </tr>
    </thead>
</table>
</body>
</html>