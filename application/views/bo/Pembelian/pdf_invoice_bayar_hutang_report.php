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
<div class="row"><img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$report['no_nota']?>"></div>
<table style="height: 10cm" width="100%" border="1">
    <thead>
    <tr>
        <th colspan="4" class="h1"><img class="img_head" src="<?=base_url().'assets/images/site/'.$this->m_website->site_data()->logo?>"></th>
        <th rowspan="3" class="judul">NOTA BAYAR HUTANG</th>
        <th class="h2" colspan="3">Tanggal</th>
        <th class="h2">: <?=substr($report['tgl_byr'],0,10)?></th>
    </tr>
    <tr>
        <th rowspan="2" colspan="4" class="h2 alamat"><?php echo $this->m_website->address() ?></th>
        <th class="h2" colspan="3">No. Nota</th>
        <th class="h2">: <?=$report['no_nota']?></th>
    </tr>
    <tr>
        <th class="h2" colspan="3">Supplier</th>
        <th class="h2">: <?=$report['Nama']?></th>
    </tr>
	
	<tr>
        <th colspan="3" class="h3">Nota Pembelian</th>
        <th class="h3" colspan="2">: <?=$report['fak_beli']?></th>
        <th rowspan="" colspan="4" class="h3"></th>
    </tr>
    <tr>
        <th colspan="3" class="h3" style="border-top-color: transparent">Tanggal Jatuh Tempo</th>
        <th class="h3" colspan="2" style="border-top-color: transparent">: <?=substr($report['tgl_jatuh_tempo'],0,10)?></th>
        <th colspan="2" class="h3" style="border-top-color: transparent">Bank</th>
        <th class="h3" colspan="2" style="border-top-color: transparent">: <?=$report['nm_bank']?></th>
    </tr>
    <tr>
        <th colspan="3" class="h3" style="border-top-color: transparent">Jenis Pembayaran</th>
        <th class="h3" colspan="2" style="border-top-color: transparent">: <?=$report['cara_byr']?></th>
		<th colspan="2" class="h3" style="border-top-color: transparent">Cek/Giro</th>
        <th class="h3" colspan="2" style="border-top-color: transparent">: <?=$report['nogiro']?></th>
    </tr>
    <tr>
        <th colspan="3" class="h3" style="border-top-color: transparent">Pembayaran</th>
        <th class="h3" colspan="2" style="border-top-color: transparent">: <?=number_format($report['jumlah'],2)?></th>
		<th colspan="2" class="h3" style="border-top-color: transparent">Tanggal Cair Giro</th>
        <th class="h3" colspan="2" style="border-top-color: transparent">: <?=substr($report['tgl_cair_giro'],0,10)?></th>
    </tr>
	<tr>
        <th colspan="3" class="h3" style="border-top-color: transparent">Pembulatan</th>
        <th class="h3" colspan="2" style="border-top-color: transparent">: <?=number_format($report['bulat'],2)?></th>
		<th colspan="2" class="h3" style="border-top-color: transparent">Keterangan</th>
        <th class="h3" colspan="2" style="border-top-color: transparent">: <?=$report['ket']?></th>
    </tr>
    </thead>
</table>

    <table width="100%" border="1">
    
	<tbody>
    <tr>
        <td colspan="4" class="borderLR borderTB"><div style="<?=($i>35)?'position: absolute !important; margin-top: 5cm !important':''?>" class="container_terbilang"><div class="terbilang"><i><?=number_to_words($report['jumlah'])?></i></div></div></td>
        <td class="h3 borderTB"></td>
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
<span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($report['no_nota'])?></span>
</body>
</html>