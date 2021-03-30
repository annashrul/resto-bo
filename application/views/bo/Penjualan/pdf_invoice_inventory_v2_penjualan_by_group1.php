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
				<th style="border:1px solid black; text-align: left" colspan="13">Sub Dept - Kel Brg</th>
			</tr>
			<tr>
				<th style="border:1px solid black">Kd Brg</th>
				<th style="border:1px solid black">Nm Brg</th>
				<th style="border:1px solid black">Hrg Bl</th>
				<th style="border:1px solid black">Hrg Jl</th>
				<th style="border:1px solid black">Stk Awl</th>
				<th style="border:1px solid black">Stk Msk</th>
				<th style="border:1px solid black">Jual</th>
				<th style="border:1px solid black">Rtr</th>
				<th style="border:1px solid black">Adj</th>
				<th style="border:1px solid black">Mts</th>
				<th style="border:1px solid black">Stk Akr</th>
				<th style="border:1px solid black">Lst Alk</th>
				<th style="border:1px solid black">Lst Jl</th>
			</tr>
			</thead>
			<tbody>
			<?php echo $result; ?>
			</tbody>
			<tfoot>
			<tr>
				<th style="border:1px solid black; text-align: right" colspan="4">TOTAL</th>
				<th style="border:1px solid black; text-align: center"><?=$stk_awl?></th>
				<th style="border:1px solid black; text-align: center"><?=$stk_msk?></th>
				<th style="border:1px solid black; text-align: center"><?=$jual?></th>
				<th style="border:1px solid black; text-align: center"><?=$rtr?></th>
				<th style="border:1px solid black; text-align: center"><?=$adj?></th>
				<th style="border:1px solid black; text-align: center"><?=$mts?></th>
				<th style="border:1px solid black; text-align: center"><?=$stk_akr?></th>
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