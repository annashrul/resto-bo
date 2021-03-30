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
				<th style="border:1px solid black; text-align: left" colspan="11">Sub Departemen - Kelompok Barang</th>
			</tr>
			<tr>
				<th style="border:1px solid black">Kode Barang</th>
				<th style="border:1px solid black">Nama Barang</th>
				<th style="border:1px solid black">Harga Beli</th>
				<th style="border:1px solid black">Harga Jual</th>
				<th style="border:1px solid black">Margin %</th>
				<th style="border:1px solid black">Stock Qty</th>
				<th style="border:1px solid black">Stock Value</th>
				<th style="border:1px solid black">Total Jual</th>
				<th style="border:1px solid black">Total Alokasi</th>
				<th style="border:1px solid black">Last Jual</th>
				<th style="border:1px solid black">Last Alokasi</th>
			</tr>
			</thead>
			<tbody>
			<?php echo $result; ?>
			</tbody>
			<tfoot>
			<tr>
				<th style="border:1px solid black; text-align: right" colspan="4">TOTAL</th>
				<th style="border:1px solid black; text-align: center"><?=$mrg?></th>
				<th style="border:1px solid black; text-align: center"><?=$stq?></th>
				<th style="border:1px solid black; text-align: right"><?=number_format($stv, 2)?></th>
				<th style="border:1px solid black; text-align: center"><?=$tj?></th>
				<th style="border:1px solid black; text-align: center"><?=$ta?></th>
				<th style="border:1px solid black" colspan="2"></th>
			</tr>
			</tfoot>
		</table>
	</div>
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