<style type="text/css">
	body {
		font-family: "OpenSans-Regular", "Lucida Sans Typewriter", "Lucida Typewriter", "Arial", "Helvetica", "sans-serif";
	}
</style>
<html>
<head>
	<link rel="shortcut icon" href="<?=base_url().'assets/'?>images/site/<?=$site->fav_icon?>" />
	<title><?=$title." | ".$site->title?></title>
</head>
<body>
<div>
	<div>
		<table width="100%" cellpadding="5" cellspacing="0" style="border:1px solid black; font-size:11px;">
			<thead>
			<tr>
				<th style="border:1px solid black">No</th>
				<th style="border:1px solid black">Tanggal</th>
				<th style="border:1px solid black">No. Transaksi</th>
				<th style="border:1px solid black">Nota Supplier</th>
				<th style="border:1px solid black">Nilai Pembelian</th>
				<th style="border:1px solid black">Jenis Transaksi</th>
				<th style="border:1px solid black">Jatuh Tempo</th>
			</tr>
			</thead>
			<tbody>
			<?php $i=0; $no = 0; $tdp = 0; foreach($report_detail as $row){
                $sub_total = 0;
                $get_detail = $this->m_crud->read_data("det_beli", "harga_beli, jumlah_beli, jumlah_retur, diskon disc1, disc2, PPN ppn", "left(CONVERT(varchar, tgl_beli, 120), 10) between '".$tgl_awal."' and '".$tgl_akhir."' and kode_barang in (select kd_brg from barang where no_faktur_beli = '".$row['no_faktur_beli']."')");
                foreach ($get_detail as $row_detail) {
                    $hitung_netto = ((int)$row_detail['jumlah_beli']-(int)$row_detail['jumlah_retur']) * $row_detail['harga_beli'];
                    $disc = $this->m_website->double_diskon($hitung_netto, array($row_detail['disc1'], $row_detail['disc2']));
                    $hitung_sub_total = $this->m_website->grand_total_ppn($disc, 0, $row_detail['ppn']);
                    $sub_total = $sub_total + $hitung_sub_total;
                }
			    $i++; ?>
				<tr>
					<td style="border:1px solid black"><?=$i?></td>
					<td style="border:1px solid black"><?=substr($row['tgl_beli'], 0, 10)?></td>
					<td style="border:1px solid black"><?=$row['no_faktur_beli']?></td>
					<td style="border:1px solid black"><?=$row['noNota']?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($sub_total-$row['disc']+$row['ppn'], 2)?></td>
					<td style="border:1px solid black"><?=$row['type']?></td>
					<td style="border:1px solid black"><?=substr($row['tgl_jatuh_tempo'], 0, 10)?></td>
				</tr>
			<?php
				$tdp = $tdp + $sub_total-$row['disc']+$row['ppn'];
			} ?>
			<?php /*for($r=1; $r<=23; $r++){ $i++; ?>
					<tr>
						<td style="border:1px solid black"><?=$i?></td>
						<td style="border:1px solid black">a</td>
						<td style="border:1px solid black">b</td>
						<td style="border:1px solid black">c</td>
						<td style="border:1px solid black">d</td>
						<td style="border:1px solid black; text-align:right;">e</td>
					</tr>
				<?php } */ ?>
			</tbody>
			<tfoot>
			<tr>
				<th style="border:1px solid black" colspan="4">TOTAL</th>
				<th style="border:1px solid black; text-align: right"><?=number_format($tdp, 2)?></th>
				<th style="border:1px solid black"></th>
				<th style="border:1px solid black"></th>
			</tr>
			</tfoot>
		</table>
	</div>
	<?php if($i > $row_one_page && ($i % $row_per_page) > $row_one_page){ for($x=(($i % $row_per_page)+1); $x<=$row_per_page; $x++){
		//echo '<tr><td>&nbsp;<br/></td><td></td><td></td><td></td><td></td><td></td></tr>';
		echo '&nbsp;<br/>';
	} } ?>
	<div>
		<table width="100%">
			<thead>
			<tr>
				<th width="80%"></th>
				<th width="20%"></th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td></td>
				<td>
					<br/><br/><br/><br/><b>_____________<br/>&nbsp;</b><br/>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
</body>
</html>