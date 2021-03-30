<html>
<head>
	<link rel="shortcut icon" href="<?=base_url().'assets/'?>images/site/<?=$site->fav_icon?>" />
	<title><?=$title." | ".$site->title?></title>
	<style type="text/css">
		body {
			font-family: "Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace;
		}
		.pull-right {
			text-align: right;
		}
	</style>
</head>
<body>
<div>
	<table width="100%" border="0">
	<?php
	for ($i=0; $i<count($result['sales_report_by_financial']); $i++) {
		if ($i % 2 == 0) {
		?>
		<tr>
		<?php
		}
	?>
		<td style="width: 48%">
		<table width="100%" border="0">
			<tr>
				<td>Kassa</td>
				<th class="pull-right"><?=$result['sales_report_by_financial'][$i]['Kassa']?></th>
			</tr>
			<tr>
				<td>Total Sales</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['total_sales'])?></th>
			</tr>
			<tr>
				<td>Discount Item</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['diskon_item'])?></th>
			</tr>
			<tr>
				<td>Discount Total</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['diskon_total'])?></th>
			</tr>
			<tr>
				<td>Net Omset</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['net_omset'])?></th>
			</tr>
			<tr>
				<td>Tax</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['tax_total'])?></th>
			</tr>
			<tr>
				<td>Total Omset</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['total_omset'])?></th>
			</tr>
			<tr>
				<td>Cash</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['cash_total'])?></th>
			</tr>
			<tr>
				<td>Edc Seetle</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['edc_total'])?></th>
			</tr>
			<!---->
			<tr>
				<td style="border-top: solid; border-width: thin">Receive Amount</td>
				<th style="border-top: solid; border-width: thin; text-align: right"><?=number_format($result['sales_report_by_financial'][$i]['receive_amount'])?></th>
			</tr>
			<tr>
				<td>Other Income</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['other_income'])?></th>
			</tr>
			<tr>
				<td>Total Income</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['total_income'])?></th>
			</tr>
			<!---->
			<tr>
				<td style="border-top: solid; border-width: thin">Cash In Hand</td>
				<th style="border-top: solid; border-width: thin; text-align: right"><?=number_format($result['sales_report_by_financial'][$i]['cash_in_hand'])?></th>
			</tr>
			<!---->
			<tr>
				<td style="border-top: solid; border-width: thin">Return</td>
				<th style="border-top: solid; border-width: thin; text-align: right"><?=number_format($result['sales_report_by_financial'][$i]['return'])?></th>
			</tr>
			<tr>
				<td>Pain Out</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['paid_out'])?></th>
			</tr>
			<tr>
				<td>Total Outcome</td>
				<th class="pull-right"><?=number_format($result['sales_report_by_financial'][$i]['total_outcome'])?></th>
			</tr>
			<!---->
			<tr>
				<td style="border-top: solid; border-width: thin">Total Cash Sales</td>
				<th style="border-top: solid; border-width: thin; text-align: right"><?=number_format($result['sales_report_by_financial'][$i]['total_cash_sales'])?></th>
			</tr>
			<!---->
			<tr>
				<td style="border-top: solid; border-width: thin">Cashier Cash</td>
				<th style="border-top: solid; border-width: thin; text-align: right"><?=number_format($result['sales_report_by_financial'][$i]['cashier_cash'])?></th>
			</tr>
			<!---->
			<tr>
				<td style="border-top: solid; border-width: thin">Status</td>
				<th style="border-top: solid; border-width: thin; text-align: right"><?=$result['sales_report_by_financial'][$i]['status_report']?></th>
			</tr>
		</table>
		</td>
	<?php
		if ($i % 2 == 0) {
			if ($i % 2 != 0 || $i == 0) {
				?>
				<td style="width: 4%">&nbsp;</td>
				<?php
			}
			?>
			</tr>
			<?php
		}
	}
	?>
	</table>
</div>
</body>
</html>