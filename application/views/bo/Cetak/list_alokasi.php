<!doctype html>
<html lang="en">
<head>
    <?php $this->load->view('bo/head'); ?>
    <style type="text/css">
        body {
            font-family: "Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace;
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

        .kosong {
            width: 1cm;
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
        <th rowspan="4" class="judul">LIST ALOKASI</th>
        <th class="h2" colspan="3">Tgl Pembelian</th>
        <th class="h2">: <?php echo $tanggal ?></th>
    </tr>
    <tr>
        <th rowspan="3" colspan="4" class="h2 alamat"><?php echo $this->m_website->address() ?></th>
        <th class="h2" colspan="3">Kode Pembelian</th>
        <th class="h2">: <?php echo $no_beli ?></th>
    </tr>
    <tr>
        <th class="h2" colspan="3">Nota Supplier</th>
        <th class="h2">: <?php echo $nota_supplier ?></th>
    </tr>
    <tr>
        <th class="h2" colspan="3">Supplier</th>
        <th class="h2">: <?php echo $supplier ?></th>
    </tr>
    </thead>
</table>
<table width="100%" border="1">
    <thead>
    <tr>
        <th class="tengah" style="width: 5%">NO</th>
        <th class="tengah">KODE BARANG</th>
        <th class="tengah">BARCODE</th>
        <th class="tengah">NAMA BARANG</th>
        <th class="tengah">QTY</th>
        <th class="tengah">HARGA JUAL</th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
        <th class="kosong"></th>
    </tr>
    </thead>
    <tbody>
    <?php
    echo $table;
    ?>
    </tbody>
    </table>
</body>
</html>