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
		<?php echo $q_tgl;?>
		<table width="100%" cellpadding="5" cellspacing="0" style="border:1px solid black; font-size:11px;">
			<thead>
			<tr>
				<th style="border:1px solid black">No</th>
				<th style="border:1px solid black">Kode Barang</th>
				<th style="border:1px solid black">Barcode</th>
				<th style="border:1px solid black"><?=$menu_group['as_deskripsi']?></th>
				<th style="border:1px solid black">Nama Barang</th>
				<th style="border:1px solid black">Stock Awal</th>
				<th style="border:1px solid black">Stock Masuk</th>
				<th style="border:1px solid black">Stock Akhir</th>
				<th style="border:1px solid black">Jual</th>
				<th style="border:1px solid black">Retur</th>
				<th style="border:1px solid black">Satuan</th>
				<th style="border:1px solid black">Harga Beli</th>
				<th style="border:1px solid black">Harga Jual</th>
				<th style="border:1px solid black">Diskon Item</th>
				<th style="border:1px solid black">Jumlah Beli</th>
				<th style="border:1px solid black">Jumlah Jual</th>
			</tr>
			</thead>
			<tbody>
			<?php $i=0; $jumlah = 0; $sa = 0; $sp = 0; $st = 0; $jl = 0; $rt = 0; $di = 0; $j_jual = 0; foreach($report_detail as $row){ $i++; ?>
				<tr>
					<td style="border:1px solid black"><?=$i?></td>
					<td style="border:1px solid black"><?=$row['kd_brg']?></td>
					<td style="border:1px solid black"><?=$row['barcode']?></td>
					<td style="border:1px solid black"><?=$row['Deskripsi']?></td>
					<td style="border:1px solid black"><?=$row['nm_brg']?></td>
					<td style="border:1px solid black"><?=($row['stock_awal']+0)?></td>
					<td style="border:1px solid black"><?=($row['stock_periode']+0)?></td>
					<td style="border:1px solid black"><?=($row['stock_awal']+$row['stock_periode']-$row['jual']-$row['retur']+0)?></td>
					<td style="border:1px solid black"><?=($row['jual']+0)?></td>
					<td style="border:1px solid black"><?=($row['retur']+0)?></td>
					<td style="border:1px solid black"><?=$row['satuan']?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['hrg_beli'])?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['hrg_jual'])?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['diskon_item'])?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['hrg_beli']*$row['jual'])?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['hrg_jual']*$row['jual']-$row['diskon_item'])?></td>
				</tr>
			<?php
                $j_jual = $j_jual + $row['hrg_jual']*$row['jual']-$row['diskon_item'];
				$jumlah = $jumlah + ($row['hrg_beli'] * $row['jual']);
				$sa = $sa + ($row['stock_awal']+0); $sp = $sp + ($row['stock_periode']+0); $st = $st + ($row['stock_awal']+$row['stock_periode']-$row['jual']-$row['retur']+0); $jl = $jl + ($row['jual']+0); $rt = $rt + ($row['retur']+0); $di = $di + $row['diskon_item'];
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
				<td style="border:1px solid black" colspan="5">TOTAL</td>
				<td style="border:1px solid black"><?=$sa?></td>
				<td style="border:1px solid black"><?=$sp?></td>
				<td style="border:1px solid black"><?=$st?></td>
				<td style="border:1px solid black"><?=$jl?></td>
				<td style="border:1px solid black"><?=$rt?></td>
				<td style="border:1px solid black"></td>
				<td style="border:1px solid black"></td>
				<td style="border:1px solid black"></td>
				<td style="border:1px solid black" class="text-right"><?=number_format($di)?></td>
				<td style="border:1px solid black" class="text-right"><?=number_format($jumlah)?></td>
				<td style="border:1px solid black" class="text-right"><?=number_format($j_jual)?></td>
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