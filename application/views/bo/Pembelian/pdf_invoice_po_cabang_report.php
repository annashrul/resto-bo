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
				<th style="border:1px solid black">Barcode</th>
				<th style="border:1px solid black">Nama Barang</th>
				<th style="border:1px solid black">Artikel</th>
				<th style="border:1px solid black">Satuan</th>
				<th style="border:1px solid black">Qty</th>
				<th style="border:1px solid black">Harga</th>
			</tr>
			</thead>
			<tbody>
			<?php $i=0; foreach($report_detail as $row){ $i++; ?>
				<tr>
					<td style="border:1px solid black"><?=$i?></td>
					<td style="border:1px solid black"><?=$row['kode_barang']?></td>
					<td style="border:1px solid black"><?=$row['barcode']?></td>
					<td style="border:1px solid black"><?=$row['nm_brg']?></td>
					<td style="border:1px solid black"><?=$row['deskripsi']?></td>
					<td style="border:1px solid black"><?=$row['satuan']?></td>
					<td style="border:1px solid black"><?=(int)$row['jumlah_beli']?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['harga_beli'])?></td>
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
            <!--<tfoot>
            <tr>
                <td colspan="8"><?=$catatan?></td>
            </tr>
            </tfoot>-->
		</table>
	</div>
	<?php if($i > $row_one_page && ($i % $row_per_page) > $row_one_page){ 
		echo '<div><table>';
		for($x=(($i % $row_per_page)+1); $x<=($row_per_page); $x++){ 
			echo '<tr><td colspan="8" style="color:white;">row padding</td></tr>';
			//echo '&nbsp;<br/>';
		} 
		echo '</table></div>';
	} ?>
	<div>
		<table width="100%">
			<thead>
			<tr>
				<th width="25%"></th>
				<th width="25%"></th>
				<th width="25%"></th>
				<th width="25%"></th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td style="text-align:center;">
					Purchasing<br/><br/><br/><br/><b>_____________<br/></b><br/>
				</td>
				<td style="text-align:center;">
					Buyer<br/><br/><br/><br/><b>_____________<br/></b><br/>
				</td>
				<td style="text-align:center;">
					Sales<br/><br/><br/><br/><b>_____________<br/></b><br/>
				</td>
				<td style="text-align:center;">
					Mengetahui<br/><br/><br/><br/><b>_____________<br/></b><br/>
				</td>
			</tr>
			</tbody>
		</table>
		<table width="100%">
			<thead>
			<tr>
				<th width="100%"></th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td style="font-size:10px;">
					<!--Pengiriman : <?=substr($report['tglkirim'], 0, 10)?><br/><br/>-->
					Catatan : <br/>
					- Pengiriman terlambat 5 (lima) hari dari tanggal tersebut di atas, maka pembelian dapat dibatalkan.<br/>
					- "PO" harap dilampirkan, tanpa bon ini barang tidak akan diterima.<br/>
					- Retur tidak diambil maks. 3 (tiga) bulan, dianggap hangus.
				</td>
			</tr>
			</tbody>
		</table>
        <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($report['no_po'])?></span>
	</div>
</div>
</body>
</html>