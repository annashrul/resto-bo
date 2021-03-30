<style type="text/css">
	body {
		font-family: "OpenSans-Regular", "Lucida Sans Typewriter", "Lucida Typewriter", "Arial", "Helvetica", "sans-serif";
	}
</style>
<html>
<head>
	<link rel="shortcut icon" href="<?=base_url().'assets/'?>images/site/<?=$site->fav_icon?>" />
	<title><?=$title?></title>
</head>
<body>
	<div>
		<div>
			<table width="100%" cellpadding="5" cellspacing="0" style="border:1px solid black; font-size:11px;">
				<thead>
					<tr>
						<th style="border:1px solid black">No</th>
						<th style="border:1px solid black">No. Transaksi</th>
						<th style="border:1px solid black">Tgl. Transaksi</th>
						<th style="border:1px solid black">Lokasi</th>
						<th style="border:1px solid black">Stock In</th>
						<th style="border:1px solid black">Stock Out</th>
						<th style="border:1px solid black">Keterangan</th>
					</tr>
				</thead>
				<tbody>
				<?php $i=0; foreach($report_detail as $row){ $i++; ?>
					<tr>
						<td style="border:1px solid black; width: 1cm"><?=$i?></td>
						<td style="border:1px solid black"><?=$row['kd_trx']?></td>
						<td style="border:1px solid black"><?=substr($row['tgl'], 0, 10)?></td>
						<td style="border:1px solid black"><?=$row['lokasi']?></td>
						<td style="border:1px solid black; text-align:center; width: 2cm"><?=($row['stock_in'] + 0)?></td>
						<td style="border:1px solid black; text-align:center; width: 2cm"><?=($row['stock_out'] + 0)?></td>
						<td style="border:1px solid black"><?=$row['keterangan']?></td>
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
							<br/><br/><br/><br/><br/><b>_____________<br/>&nbsp;<br/>&nbsp;</b><br/><br/>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>

