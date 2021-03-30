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
<?=$head?>
<?php
    $menu_group = $this->m_crud->get_data('Setting', 'as_group1, as_group2, as_deskripsi', "Kode = '1111'")
?>
<div>
	<div>
		<table width="100%" cellpadding="5" cellspacing="0" style="border:1px solid black; font-size:11px;">
			<thead>
			<tr>
				<th style="border:1px solid black">No</th>
				<th style="border:1px solid black">Kode Barang</th>
				<th style="border:1px solid black">Barcode</th>
				<th style="border:1px solid black"><?=$menu_group['as_deskripsi']?></th>
				<th style="border:1px solid black">Nama Barang</th>
				<th style="border:1px solid black; width: 50px">Stock Awal</th>
				<th style="border:1px solid black; width: 50px">Stock Masuk</th>
				<th style="border:1px solid black; width: 50px">Jual</th>
				<th style="border:1px solid black; width: 50px">Retur</th>
				<th style="border:1px solid black; width: 50px">Adjust</th>
				<th style="border:1px solid black; width: 50px">Mutasi</th>
				<th style="border:1px solid black; width: 50px">Stock Akhir</th>
				<th style="border:1px solid black">Harga Beli</th>
				<th style="border:1px solid black">Harga Jual</th>
				<!--<th style="border:1px solid black">Diskon Item</th>-->
				<th style="border:1px solid black">Jumlah Beli</th>
				<th style="border:1px solid black">Jumlah Jual</th>
			</tr>
			</thead>
			<tbody>
            <?php
            $no = 0;
            $jbeli = 0; $jjual = 0; $sa = 0; $sp = 0; $st = 0; $jl = 0; $rt = 0; $di = 0; $adj = 0; $mut = 0;
            foreach($detail as $rows){ $no++; ?>
			<tr>
				<td style="border:1px solid black"><?=$no?></td>
				<td style="border:1px solid black"><?=$rows['kd_brg']?></td>
				<td style="border:1px solid black"><?=$rows['barcode']?></td>
				<td style="border:1px solid black"><?=$rows['Deskripsi']?></td>
				<td style="border:1px solid black"><?=$rows['nm_brg']?></td>
				<td style="border:1px solid black"><?=($rows['stock_awal'] + 0)?></td>
				<td style="border:1px solid black"><?=($rows['stock_masuk'] + 0)?></td>
				<td style="border:1px solid black"><?=($rows['jual'] + 0)?></td>
				<td style="border:1px solid black"><?=($rows['retur'] + 0)?></td>
				<td style="border:1px solid black"><?=($rows['adjust'] + 0)?></td>
				<td style="border:1px solid black"><?=($rows['mutasi'] + 0)?></td>
				<td style="border:1px solid black"><?=($rows['stock_awal'] + $rows['stock_masuk'] - $rows['jual'] - $rows['retur'] + $rows['adjust'] + $rows['mutasi'] + 0)?></td>
				<td style="border:1px solid black; text-align: right"><?=number_format($rows['hrg_beli'])?></td>
				<td style="border:1px solid black; text-align: right"><?=number_format($rows['hrg_jual'])?></td>
				<td style="border:1px solid black; text-align: right"><?=number_format($rows['hrg_beli'] * $rows['jual'])?></td>
				<td style="border:1px solid black; text-align: right"><?=number_format($rows['hrg_jual'] * $rows['jual'])?></td>
			</tr>
            <?php
            $jbeli = $jbeli + ($rows['hrg_beli'] * $rows['jual']);
			$jjual = $jjual + ($rows['hrg_jual'] * $rows['jual']);
			$sa = $sa + ($rows['stock_awal'] + 0);
			$sp = $sp + ($rows['stock_masuk'] + 0);
			$st = $st + ($rows['stock_awal'] + $rows['stock_masuk'] - $rows['jual'] - $rows['retur'] + $rows['adjust'] + $rows['mutasi'] + 0);
			$jl = $jl + ($rows['jual'] + 0);
			$rt = $rt + ($rows['retur'] + 0);
			$adj = $adj + ($rows['adjust'] + 0);
			$mut = $mut + ($rows['mutasi'] + 0);
			$di = $di + $rows['diskon_item'];
			} ?>
            </tbody>
            <tfoot>
			<tr>
                <td style="border:1px solid black" colspan="5">TOTAL</td>
                <td style="border:1px solid black"><?=$sa?></td>
                <td style="border:1px solid black"><?=$sp?></td>
                <td style="border:1px solid black"><?=$jl?></td>
                <td style="border:1px solid black"><?=$rt?></td>
                <td style="border:1px solid black"><?=$adj?></td>
                <td style="border:1px solid black"><?=$mut?></td>
                <td style="border:1px solid black"><?=$st?></td>
                <td style="border:1px solid black"></td>
                <td style="border:1px solid black"></td>
                <!--<td style="border-top: solid; border-width: thin" class="text-right"><?/*=number_format($di)*/?></td>-->
                <td style="border:1px solid black; text-align: right"><?=number_format($jbeli)?></td>
                <td style="border:1px solid black; text-align: right"><?=number_format($jjual)?></td>
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