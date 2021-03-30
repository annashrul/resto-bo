<html>
<head>
	<link rel="shortcut icon" href="<?=base_url().'assets/'?>images/site/<?=$site->fav_icon?>" />
	<title><?=$title." | ".$site->title?></title>
	<style>
		body {
			font-family: "OpenSans-Regular", "Lucida Sans Typewriter", "Lucida Typewriter", "Arial", "Helvetica", "sans-serif";
		}
		.width-table {
			border-left: solid; border-width: thin;
		}
	</style>
</head>
<body>
	<div>
		<div>
			<table width="100%" cellpadding="1" cellspacing="0" style="font-size:11px">
				<thead>
					<tr>
						<th class="width-table" >No</th>
						<th class="width-table" >Kode Barang</th>
						<th class="width-table" >Barcode/<?=substr($menu_group['as_deskripsi'],0,3)?></th>
						<th class="width-table" >Nama Barang</th>
						<th class="width-table" >Qty Order</th>
						<th class="width-table" >Qty Approve</th>
					</tr>
				</thead>
				<tbody>
				<?php $i=0; $qty_order = 0; $qty_approve = 0; foreach($report_detail as $row){ $i++; ?>
					<tr>
						<td class="width-table" ><?=$i?></td>
						<td class="width-table" ><?=$row['kd_brg']?></td>
						<td class="width-table" ><?=$row['kd_brg']==$row['barcode']?$row['Deskripsi']:$row['barcode']?></td>
						<td class="width-table" ><?=$row['nm_brg']?></td>
						<td class="width-table"  style="text-align:center;"><?=(int)$row['qty_order']?></td>
						<td class="width-table"  style="text-align:center;"><?=(int)$row['qty_approve']?></td>
					</tr>
				<?php
                    $qty_order = $qty_order + (int)$row['qty_order'];
                    $qty_approve = $qty_approve + (int)$row['qty_approve'];
				} ?>
				</tbody>
                <tfoot>
                <tr>
                    <td colspan="4">TOTAL</td>
                    <td style="text-align:center;"><?=$qty_order?></td>
                    <td style="text-align:center;"><?=$qty_approve?></td>
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

					</td>
					<td style="text-align:center;">

					</td>
					<td style="text-align:center;">
						<b><br/>Operator<br/><br/><br/><br/>_____________</b>
					</td>
				</tr>
				</tbody>
			</table>
            <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($report['no_receive_order'])?></span>
		</div>
	</div>
</body>
</html>

