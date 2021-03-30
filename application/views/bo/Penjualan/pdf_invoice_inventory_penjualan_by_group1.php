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
				<th style="border:1px solid black; text-align: left" colspan="11">Sub Dept - Kel Brg</th>
			</tr>
			<tr>
				<th style="border:1px solid black">Kd Brg</th>
				<th style="border:1px solid black">Nm Brg</th>
				<th style="border:1px solid black">Hrg Bl</th>
				<th style="border:1px solid black">Hrg Jl</th>
				<th style="border:1px solid black">Mrg %</th>
				<th style="border:1px solid black">Stk Qty</th>
				<th style="border:1px solid black">Stk Val</th>
				<th style="border:1px solid black">Jl Qty</th>
				<th style="border:1px solid black">Alks Qty</th>
				<th style="border:1px solid black">Last Alks</th>
				<th style="border:1px solid black">Last Jl</th>
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
				<th style="border:1px solid black; text-align: right"><?=number_format($stv)?></th>
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