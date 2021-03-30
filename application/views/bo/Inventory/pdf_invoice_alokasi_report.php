<html>
<head>
	<link rel="shortcut icon" href="<?=base_url().'assets/'?>images/site/<?=$site->fav_icon?>" />
	<title><?=$title." | ".$site->title?></title>
	<style>
		body {
			font-family: "OpenSans-Regular", "Lucida Sans Typewriter", "Lucida Typewriter", "Arial", "Helvetica", "sans-serif";
		}
		.width-table {
			border: solid; border-width: thin;
		}
	</style>
</head>
<body>
	<div>
		<div>
			<table width="100%" border="1" cellpadding="3" cellspacing="0" style="font-size:11px">
				<thead>
					<tr>
						<th class="width-table" >No</th>
						<th class="width-table" >Kode Barang</th>
						<th class="width-table" >Barcode/<?=substr($menu_group['as_deskripsi'],0,3)?></th>
						<th class="width-table" >Nama Barang</th>
						<th class="width-table" >Qty</th>
						<th class="width-table" >Harga</th>
					</tr>
				</thead>
				<tbody>
				<?php $i=0; foreach($report_detail as $row){ $i++; ?>
					<tr>
						<td class="width-table" ><?=$i?></td>
						<td class="width-table" ><?=$row['kd_brg']?></td>
						<td class="width-table" ><?=$row['barcode']?></td>
						<td class="width-table" ><?=$row['nm_brg']?></td>
						<td class="width-table"  style="text-align:center;"><?=(int)$row['qty']?></td>
						<td class="width-table"  style="text-align:right;"><?=number_format($row['hrg_jual'])?></td>
					</tr>
				<?php } ?>
				<?php /*for($r=1; $r<=23; $r++){ $i++; ?>
					<tr>
						<td><?=$i?></td>
						<td>a</td>
						<td>b</td>
						<td>c</td>
						<td>d</td>
						<td style="border:1px; text-align:right;">e</td>
					</tr>
				<?php } */ ?>
				</tbody>
                <tfoot><tr>
                    <td colspan="6"><?=$keterangan?></td>
                </tr></tfoot>
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
            <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($report['no_faktur_mutasi'])?></span>
		</div>
	</div>
</body>
</html>

