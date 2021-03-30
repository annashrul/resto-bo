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
				<th style="border:1px solid black">Faktur Beli</th>
				<th style="border:1px solid black">Supplier</th>
				<th style="border:1px solid black">Tanggal</th>
				<th style="border:1px solid black">Qty</th>
				<th style="border:1px solid black">Satuan</th>
				<th style="border:1px solid black">Harga Beli</th>
				<th style="border:1px solid black">Disc 1</th>
				<th style="border:1px solid black">Disc 2</th>
				<th style="border:1px solid black">PPN</th>
				<th style="border:1px solid black">Disc Trx</th>
				<th style="border:1px solid black">PPN Trx</th>
				<th style="border:1px solid black">Sub Total</th>
			</tr>
			</thead>
			<tbody>
			<?php $i=0; $no = 0; $qt = 0; $d1 = 0; $d2 = 0; $d3 = 0; $d4 = 0; $ppn = 0; $st = 0; $disct = 0; $ppnt = 0; foreach($report_detail as $row){ $i++; ?>
				<tr>
					<td style="border:1px solid black"><?=$i?></td>
					<td style="border:1px solid black"><?=$row['no_faktur_beli']?></td>
					<td style="border:1px solid black"><?=$row['nama']?></td>
					<td style="border:1px solid black"><?=substr($row['tgl_blei'], 0, 10)?></td>
					<td style="border:1px solid black"><?=(int)$row['qty']?></td>
					<td style="border:1px solid black"><?=$row['satuan']?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['harga_beli'])?></td>
					<td style="border:1px solid black"><?=($row['disc1']+0)?></td>
					<td style="border:1px solid black"><?=($row['disc2']+0)?></td>
					<td style="border:1px solid black"><?=($row['ppn']+0)?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['disct'])?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['ppnt'])?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['sub_total']-$row['disct']+$row['ppnt'])?></td>
				</tr>
			<?php
				$qt = $qt + (int)$row['qty'];
				$st = $st + (float)$row['sub_total']-$row['disct']+$row['ppnt'];
				$disct = $disct + $row['disct'];
				$ppnt = $ppnt + $row['ppnt'];
				$d1 = $d1 + ($row['disc1']+0);
				$d2 = $d2 + ($row['disc2']+0);
				$ppn = $ppn + ($row['ppn']+0);
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
				<th style="border:1px solid black"><?=$qt?></th>
				<th style="border:1px solid black"></th>
				<th style="border:1px solid black"></th>
				<th style="border:1px solid black"><?=$d1?></th>
				<th style="border:1px solid black"><?=$d2?></th>
                <th style="border:1px solid black"><?=$ppn?></th>
                <th style="border:1px solid black"><?=$disct?></th>
                <th style="border:1px solid black"><?=$ppnt?></th>
				<th style="border:1px solid black; text-align: right"><?=number_format($st, 2)?></th>
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