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
						<th style="border:1px solid black">Stock Terakhir</th>
						<th style="border:1px solid black">Jenis</th>
						<th style="border:1px solid black">Qty Adjust</th>
						<th style="border:1px solid black">Saldo Stock</th>
					</tr>
				</thead>
				<tbody>
				<?php $i=0; $total = 0; foreach($report_detail as $row){ $i++; ?>
					<tr>
						<td style="border:1px solid black"><?=$i?></td>
						<td style="border:1px solid black"><?=$row['kd_brg']?></td>
						<td style="border:1px solid black"><?=$row['barcode']?></td>
						<td style="border:1px solid black"><?=$row['nm_brg']?></td>
						<td style="border:1px solid black; text-align: center"><?=(int)$row['stock_terakhir']?></td>
                        <td style="border:1px solid black"><?=$row['status']?></td>
                        <td style="border:1px solid black; text-align: center"><?=(int)$row['qty_adjust']?></td>
                        <td style="border:1px solid black; text-align: center"><?=(int)$row['saldo_stock']?></td>
                    </tr>
				<?php
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
						<th width="50%"></th>
						<th width="50%"></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="text-align:center;">
							<b><br/>Operator<br/><br/><br/><br/>_____________</b>
						</td>
						<td style="text-align:center;">
							<b><br/>Mengetahui<br/><br/><br/><br/>_____________</b>
						</td>
					</tr>
				</tbody>
			</table>
            <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($report['kd_trx'])?></span>
		</div>
	</div>
</body>
</html>

