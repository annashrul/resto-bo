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
				<th style="border:1px solid black">Qty</th>
				<th style="border:1px solid black">Qty Approval</th>
				<th style="border:1px solid black">Selisih</th>
				<th style="border:1px solid black">Status</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$i=0;
			$dqt = 0;
			$dqa = 0;
			$dsl = 0;
			foreach($report_detail as $row){
				$selisih = (int) $row['qty']-(int) $row['qty_approval'];
				$i++; ?>
				<tr>
					<td style="border:1px solid black"><?=$i?></td>
					<td style="border:1px solid black"><?=$row['kd_brg']?></td>
					<td style="border:1px solid black"><?=$row['barcode']?></td>
					<td style="border:1px solid black"><?=$row['nm_brg']?></td>
					<td style="border:1px solid black; text-align:center;"><?=($row['qty']+0)?></td>
					<td style="border:1px solid black; text-align:center;"><?=($row['qty_approval']+0)?></td>
					<td style="border:1px solid black; text-align:center;"><?=$selisih?></td>
					<td style="border:1px solid black"><?php if($selisih == 0){echo 'Approved';}else if((int)$row['qty_approval'] > 0 && $selisih != 0){echo 'Approved In Part';}else{echo 'Approval Process';}?></td>
				</tr>
			<?php
				$dqt = $dqt + $row['qty'];
				$dqa = $dqa + $row['qty_approval'];
				$dsl = $dsl + $selisih;
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
				<th style="border:1px solid black"><?=$dqt?></th>
				<th style="border:1px solid black"><?=$dqa?></th>
				<th style="border:1px solid black"><?=$dsl?></th>
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
        <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($report['kd_trx'])?></span>
	</div>
</div>
</body>
</html>