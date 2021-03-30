<style type="text/css">
	body {
		font-family: "Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace;
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
				<th style="border:1px solid black">No. Faktur</th>
				<th style="border:1px solid black">Tanggal</th>
				<th style="border:1px solid black">Kode Barang</th>
				<th style="border:1px solid black">Barcode</th>
				<th style="border:1px solid black">Nama Barang</th>
				<th style="border:1px solid black">Qty</th>
				<th style="border:1px solid black">Satuan</th>
				<th style="border:1px solid black">Harga Jual</th>
				<th style="border:1px solid black">Diskon Item</th>
				<th style="border:1px solid black">Sub Total</th>
			</tr>
			</thead>
			<tbody>
			<?php $i=0; $total = 0; foreach($report_detail as $row){ $i++; $sub_total = ($row['qty'] * $row['hrg_jual']) - $row['dis_persen']; $total = $total + $sub_total; ?>
				<tr>
					<td style="border:1px solid black"><?=$i?></td>
					<td style="border:1px solid black"><?=$row['kd_trx']?></td>
					<td style="border:1px solid black"><?=substr($row['tgl'],0,10)?></td>
					<td style="border:1px solid black"><?=$row['kd_brg']?></td>
					<td style="border:1px solid black"><?=$row['barcode']?></td>
					<td style="border:1px solid black"><?=$row['nm_brg']?></td>
					<td style="border:1px solid black; text-align:center;"><?=($row['qty']+0)?></td>
					<td style="border:1px solid black"><?=$row['satuan']?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['hrg_jual'], 2)?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['diskon_item'], 2)?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($sub_total, 2)?></td>
				</tr>
			<?php } ?>
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
				<th colspan="9" style="border:1px solid black; text-align: right">TOTAL</th>
				<th colspan="2" style="border:1px solid black; text-align: right"><?=number_format($total, 2)?></th>
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