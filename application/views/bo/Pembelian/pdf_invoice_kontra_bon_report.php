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
				<th style="border:1px solid black"><?=$report['jenis']=='Konsinyasi'?'Periode Konsinyasi':'Nota Pembelian'?></th>
				<th style="border:1px solid black">Nota Supplier</th>
				<th style="border:1px solid black"><?=$report['jenis']=='Konsinyasi'?'Nilai Konsinyasi':'Nilai Pembelian'?></th>
				<th style="border:1px solid black">Nilai Kontra Bon</th>
			</tr>
			</thead>
			<tbody>
			<?php $i=0; $no = 0; $tdp = 0; foreach($report_detail as $row){ $i++; ?>
				<tr>
					<td style="border:1px solid black"><?=$i?></td>
					<td style="border:1px solid black"><?=$row['master_beli']?></td>
					<td style="border:1px solid black"><?=$row['noNota']?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['nilai_pembelian'], 2)?></td>
					<td style="border:1px solid black; text-align:right;"><?=number_format($row['nilai_kontra'], 2)?></td>
				</tr>
			<?php
				$tdp = $tdp + $row['nilai_kontra'];
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
	<div>
		<table width="100%">
			<thead>
				<tr>
					<th width="60%"></th>
					<th width="20%"></th>
					<th width="20%"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td></td>
					<td>Nilai Kontra Bon</td>
					<td style="text-align: right"><?=number_format($tdp, 2)?></td>
				</tr>
				<tr>
					<td></td>
					<td>Retur</td>
					<td style="text-align: right"><?=number_format($report['retur'], 2)?></td>
				</tr>
				<tr>
					<td></td>
					<td>Biaya Adm</td>
					<td style="text-align: right"><?=number_format($report['biaya_adm'], 2)?></td>
				</tr>
				<tr>
					<td></td>
					<td>Total Pembayaran</td>
					<td style="text-align: right"><?=number_format($report['pembayaran'], 2)?></td>
				</tr>
				<tr>
					<td></td>
					<td>Pembulatan</td>
					<td style="text-align: right"><?=number_format($report['pembulatan'], 2)?></td>
				</tr>
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
				<td><span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($report['id_master_kontra'])?></span></td>
				<td style="text-align: center;">
					Yang Menerima<br/><br/><br/><br/><b>_____________<br/>&nbsp;</b><br/>
				</td>
			</tr>
			</tbody>
		</table>
        <hr/>
		<table width="100%">
			<thead>
			<tr>
				<th width="100%"></th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td style="text-align: center;">
					<b>Tanda Terima</b> <br/><br/>
					Bank / Cash : _________________________________________________________________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Penerima&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
					No. Seri BG : _________________________________________________________________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
					Jumlah &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: _________________________________________________________________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
</body>
</html>