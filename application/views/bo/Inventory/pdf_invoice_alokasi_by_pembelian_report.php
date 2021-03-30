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
						<th style="border:1px solid black">Kode Barang</th>
						<th style="border:1px solid black">Barcode/<?=substr($menu_group['as_deskripsi'],0,3)?></th>
						<th style="border:1px solid black">Nama Barang</th>
						<th style="border:1px solid black">Qty</th>
						<th style="border:1px solid black">Harga</th>
					</tr>
				</thead>
				<tbody>
				<?php $i=0; $total = 0; foreach($report_detail as $row){ $i++; ?>
					<tr>
						<td style="border:1px solid black"><?=$i?></td>
						<td style="border:1px solid black"><?=$row['kode_barang']?></td>
						<td style="border:1px solid black"><?=($row['kode_barang']==$row['barcode'])?$row['Deskripsi']:$row['barcode']?></td>
						<td style="border:1px solid black"><?=$row['nm_brg']?></td>
						<td style="border:1px solid black; text-align: center"><?=(int)$row['jumlah_beli']-(int)$row['jumlah_retur'].(((int)$row['jumlah_bonus']>0)?" + ".(int)$row['jumlah_bonus']:"")?></td>
						<td style="border:1px solid black; text-align:right;"><?=number_format($row['hrg_jual_1'])?></td>
					</tr>
				<?php
				$total = $total + (int)$row['jumlah_beli']-(int)$row['jumlah_retur']+(int)$row['jumlah_bonus'];
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
					<th style="border:1px solid black; text-align: left" colspan="4">TOTAL</th>
					<th style="border:1px solid black; text-align: center"><?=$total?></th>
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
            <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($report['no_faktur_beli'])?></span>
		</div>
	</div>
</body>
</html>

