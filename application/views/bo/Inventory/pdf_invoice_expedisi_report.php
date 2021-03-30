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
			<table width="100%" border="1" cellpadding="5" cellspacing="0" style="font-size:12px">
				<thead>
					<tr>
						<th class="width-table" rowspan="2">No</th>
						<th class="width-table" rowspan="2">Kode Packing</th>
						<th class="width-table" rowspan="2">Kode Mutasi</th>
						<th class="width-table" rowspan="2">Nama Supp/Jns Brg</th>
						<th class="width-table" rowspan="2" width="60px;">Jumlah Koli</th>
						<th class="width-table" colspan="3">Menerima</th>
						<th class="width-table" rowspan="2">Keterangan</th>
					</tr>
                    <tr>
                        <th class="width-table">BRG</th>
                        <th class="width-table">SJ</th>
                        <th class="width-table">TTD</th>
                    </tr>
				</thead>
				<tbody>
				<?php $i=0; foreach($report_detail as $row){ $i++; ?>
					<tr>
						<td class="width-table" ><?=$i?></td>
						<td class="width-table" ><?=$row['kd_packing']?></td>
						<td class="width-table" ><?=$row['no_faktur_mutasi']?></td>
						<td class="width-table" ><?=$row['ket']?></td>
						<td class="width-table" style="text-align:center;"><?=$row['jml_koli']?></td>
                        <td class="width-table" ></td>
                        <td class="width-table" ></td>
                        <td class="width-table" ></td>
                        <td class="width-table" ></td>
					</tr>
				<?php
				} ?>
				</tbody>
			</table>
		</div>
		<?php if($i > $row_one_page && ($i % $row_per_page) > $row_one_page){ for($x=(($i % $row_per_page)+1); $x<=$row_per_page; $x++){ 
			//echo '<tr><td>&nbsp;<br/></td><td></td><td></td><td></td><td></td><td></td></tr>'; 
			echo '&nbsp;<br/>'; 
		} } ?>
		<div>
			<table width="100%">
				<tr>
					<td style="text-align:center;"></td>
					<td style="text-align:center;"></td>
					<td style="text-align:center;"></td>
					<td style="text-align:center;"></td>
					<td style="text-align:center;" class="atas borderLR borderLR"><?php echo date("d M Y"); ?></td>
				</tr>
				<tr>
					<td style="padding-top: 2cm; text-align:center;">____________________<br/>Operator</td>
					<td style="padding-top: 2cm; text-align:center;">____________________<br/>Supir</td>
					<td style="padding-top: 2cm; text-align:center;">____________________<br/>Checker</td>
					<td style="padding-top: 2cm; text-align:center;">____________________<br/>Penerima</td>
					<td style="padding-top: 2cm; text-align:center;">____________________<br/>Mengetahui</td>
				</tr>
			</table>
            <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($report['kd_expedisi'])?></span>
		</div>
	</div>
</body>
</html>

