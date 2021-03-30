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
				<th style="border:1px solid black"><?=$menu_group['as_deskripsi']?></th>
				<th style="border:1px solid black">Qty Kirim</th>
				<th style="border:1px solid black">Qty Retur</th>
				<th style="border:1px solid black">Qty Laku</th>
				<th style="border:1px solid black">Harga Jual</th>
				<th style="border:1px solid black">Diskon</th>
				<th style="border:1px solid black">Sub Total</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$i=0;
			$total_qty_kirim = 0;
			$total_qty_retur = 0;
			$total_qty_laku = 0;
			$total_diskon_persen = 0;
			$total_sub_total = 0;

			for ($x = 1; $x<=$loop; $x++) {

				foreach($report_detail[$x] as $row){ $i++; ?>
					<tr>
						<td style="border:1px solid black"><?=$i?></td>
						<td style="border:1px solid black"><?=$row['kd_trx']?></td>
						<td style="border:1px solid black"><?=substr($row['tgl'], 0, 10)?></td>
						<td style="border:1px solid black"><?=$row['kd_brg']?></td>
						<td style="border:1px solid black"><?=$row['barcode']?></td>
						<td style="border:1px solid black"><?=$row['nm_brg']?></td>
						<td style="border:1px solid black"><?=$row['Deskripsi']?></td>
						<td style="border:1px solid black; text-align:center;"><?=($row['qty']+0)?></td>
						<td style="border:1px solid black; text-align:center;"><?=($row['qty_retur']+0)?></td>
						<td style="border:1px solid black; text-align:center;"><?=($row['qty']-$row['qty_retur']+0)?></td>
						<td style="border:1px solid black; text-align:right;"><?=number_format($row['harga_jual'], 2)?></td>
						<td style="border:1px solid black; text-align:right;"><?=number_format($row['dis_persen'], 2)?></td>
						<td style="border:1px solid black; text-align:right;"><?=number_format((($row['qty']-$row['qty_retur'])*$row['hrg_jual'])-$row['diskon_item'], 2)?></td>
					</tr>
					<?php
					$total_qty_kirim = $total_qty_kirim + ($row['qty']+0);
					$total_qty_retur = $total_qty_retur + ($row['qty_retur']+0);
					$total_qty_laku = $total_qty_laku + ($row['qty']-$row['qty_retur']+0);
					$total_diskon_persen = $total_diskon_persen + $row['dis_persen'];
					$total_sub_total = $total_sub_total + (($row['qty']-$row['qty_retur'])*$row['hrg_jual'])-$row['diskon_item'];
				}
			}
		 	?>
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
				<th colspan="7" style="border:1px solid black; text-align: right">JUMLAH</th>
				<th style="border:1px solid black; text-align: center"><?=($total_qty_kirim)?></th>
				<th style="border:1px solid black; text-align: center"><?=($total_qty_retur)?></th>
				<th style="border:1px solid black; text-align: center"><?=($total_qty_laku)?></th>
				<th style="border:1px solid black;"></th>
				<th style="border:1px solid black; text-align: right"><?=number_format($total_diskon_persen, 2)?></th>
				<th style="border:1px solid black; text-align: right"><?=number_format($total_sub_total, 2)?></th>
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