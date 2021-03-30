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
                <th style="border:1px solid black">Tanggal</th>
                <th style="border:1px solid black">No. Transaksi</th>
                <th style="border:1px solid black">Nota Supplier</th>
                <th style="border:1px solid black">Type</th>
                <th style="border:1px solid black">Pelunasan</th>
                <th style="border:1px solid black">Supplier</th>
                <th style="border:1px solid black">Lokasi</th>
                <th style="border:1px solid black">Penerima</th>
                <th style="border:1px solid black">Diskon</th>
                <th style="border:1px solid black">PPN</th>
                <th style="border:1px solid black">Total Pembelian</th>
                <th style="border:1px solid black">Check</th>
			</tr>
			</thead>
			<tbody>
            <?php $i=0; $no = 0; $qt = 0; $d1 = 0; $d2 = 0; $d3 = 0; $d4 = 0; $ppn = 0; $st = 0; $disct = 0; $ppnt = 0;
            foreach($report_detail as $row){ $i++; ?>
                <tr>
                    <td style="border:1px solid black"><?=$i?></td>
                    <td style="border:1px solid black"><?=date('Y-m-d', strtotime($row['tgl_beli']))?></td>
                    <td style="border:1px solid black"><?=$row['no_faktur_beli']?></td>
                    <td style="border:1px solid black"><?=$row['noNota']?></td>
                    <td style="border:1px solid black"><?=$row['type']?></td>
                    <td style="border:1px solid black"><?=$row['Pelunasan']?></td>
                    <td style="border:1px solid black"><?=$row['supplier']?></td>
                    <td style="border:1px solid black"><?=$row['lokasi']?></td>
                    <td style="border:1px solid black"><?=$row['nama_penerima']?></td>
                    <td style="border:1px solid black; text-align:right;"><?=number_format($row['disc'])?></td>
                    <td style="border:1px solid black; text-align:right;"><?=number_format($row['ppn'])?></td>
                    <td style="border:1px solid black; text-align:right;"><?=number_format($row['total_beli'], 2)?></td>
                    <td style="border:1px solid black;"></td>
                </tr>
                <?php
                $st = $st + (float)$row['total_beli'];
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
                <th style="border:1px solid black" colspan="11">TOTAL</th>
                <th style="border:1px solid black; text-align: right"><?=number_format($st, 2)?></th>
                <th style="border:1px solid black;"></th>
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