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
                <th style="border:1px solid black">Operator</th>
                <th style="border:1px solid black">Jumlah Transaksi</th>
                <th style="border:1px solid black">Qty Produk</th>
                <th style="border:1px solid black">Qty Bonus</th>
                <th style="border:1px solid black">Diskon</th>
                <th style="border:1px solid black">Pajak</th>
                <th style="border:1px solid black">Total Pembelian</th>
			</tr>
			</thead>
			<tbody>
            <?php $i=0; $st = 0; $qty = 0; $bonus = 0; $disc = 0; $ppn = 0; $trx = 0;
            foreach($report_detail as $row){
                $total = $row['total']-$row['disc']+$row['ppn'];
                $i++; ?>
                <tr>
                    <td style="border:1px solid black"><?=$i?></td>
                    <td style="border:1px solid black"><?=$row['operator']?></td>
                    <td style="border:1px solid black"><?=(int)$row['trx']?></td>
                    <td style="border:1px solid black"><?=(int)$row['qty']?></td>
                    <td style="border:1px solid black"><?=(int)$row['bonus']?></td>
                    <td style="border:1px solid black; text-align:right;"><?=number_format($row['disc'])?></td>
                    <td style="border:1px solid black; text-align:right;"><?=number_format($row['ppn'])?></td>
                    <td style="border:1px solid black; text-align:right;"><?=number_format($total, 2)?></td>
                </tr>
                <?php
                $st = $st + (float)$total;
                $trx = $trx + (float)$row['trx'];
                $qty = $qty + (float)$row['qty'];
                $bonus = $bonus + (float)$row['bonus'];
                $disc = $disc + (float)$row['disc'];
                $ppn = $ppn + (float)$row['ppn'];
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
                <th style="border:1px solid black" colspan="2">TOTAL</th>
                <th style="border:1px solid black"><?=$trx?></th>
                <th style="border:1px solid black"><?=$qty?></th>
                <th style="border:1px solid black"><?=$bonus?></th>
                <th style="border:1px solid black; text-align: right"><?=number_format($disc, 2)?></th>
                <th style="border:1px solid black; text-align: right"><?=number_format($ppn, 2)?></th>
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