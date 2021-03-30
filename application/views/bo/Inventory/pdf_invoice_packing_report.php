<style type="text/css">
	body {
		font-family: "OpenSans-Regular", "Lucida Sans Typewriter", "Lucida Typewriter", "Arial", "Helvetica", "sans-serif";
	}

	.border_top_bottom {
		border: 1px;
		border-top: 1px;
		border-bottom: 1px;
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
			<table width="100%" border="1" cellpadding="5" cellspacing="0" style="font-size:11px;">
				<thead>
					<tr>
						<th>No</th>
						<th>Kode Barang</th>
						<th>Barcode/<?=substr($menu_group['as_deskripsi'],0,3)?></th>
						<th>Nama Barang</th>
						<th>Qty</th>
						<th>Harga</th>
					</tr>
				</thead>
				<tbody>
				<?php $i=0; $tqt=0; foreach($report_detail as $row){ $i++; ?>
					<tr>
						<td><?=$i?></td>
						<td><?=$row['kd_brg']?></td>
						<td><?=$row['barcode']?></td>
						<td><?=$row['nm_brg']?></td>
						<td style="text-align: center"><?=(int)$row['qty']?></td>
						<td style="text-align: right"><?=number_format($row['hrg_jual'])?></td>
					</tr>
				<?php $tqt = $tqt + $row['qty']; } ?>
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
				<tr>
					<th colspan="4">TOTAL</th>
					<th><?=$tqt?></th>
					<th></th>
				</tr>
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
					<th width="33%"></th>
					<th width="33%"></th>
					<th width="33%"></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td style="text-align:center;">
						<b><br/>Pengirim<br/><br/><br/><br/>_____________</b>
					</td>
					<td style="text-align:center;">
						<b><br/>Mengetahui<br/><br/><br/><br/>_____________</b>
					</td>
					<td style="text-align:center;">
						<b><br/>Penerima<br/><br/><br/><br/>_____________</b>
					</td>
				</tr>
				</tbody>
			</table>
            <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($report['kd_packing'])?></span>
		</div>
	</div>
</body>
</html>

