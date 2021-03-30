<center><h4><b><?=$access->lvl?></b></h4></center>
<div class="row">
	<?=form_open('user/user_level_edit')?>
	<input type="hidden" value="<?=$access->id?>" name="id">
	
	<div class="col-lg-12">
		<div class="box box-default">
			<div class="box-header"><b>Other</b></div>
			<div class="box-body">
				<div class="col-lg-3">
					<input type="checkbox" name="access-0" value="1" <?php if (substr($access->access,0,1) == 1){ echo 'checked'; } ?>>&nbsp; Preference</input><br/>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-12">
		<div class="box box-default">
			<div class="box-header"><b>Master Data</b></div>
			<div class="box-body">
				<div class="col-lg-3">
					<input type="checkbox" name="access-1" value="1" <?php if (substr($access->access,1,1) == 1){ echo 'checked'; } ?>>&nbsp;User Level</input><br/>
				</div>
				<div class="col-lg-3">
					<input type="checkbox" name="access-2" value="1" <?php if (substr($access->access,2,1) == 1){ echo 'checked'; } ?>>&nbsp;User List</input><br/>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-12">
		<div class="box box-default">
			<div class="box-header"><b>Accounting</b></div>
			<div class="box-body">
				<div class="col-lg-3">
					<input type="checkbox" name="access-3" value="1" <?php if (substr($access->access,3,1) == 1){ echo 'checked'; } ?>>&nbsp;Account Category</input><br/>
					<input type="checkbox" name="access-4" value="1" <?php if (substr($access->access,4,1) == 1){ echo 'checked'; } ?>>&nbsp;Account Group</input><br/>
					<input type="checkbox" name="access-5" value="1" <?php if (substr($access->access,5,1) == 1){ echo 'checked'; } ?>>&nbsp;Code of Account</input><br/>
					<input type="checkbox" name="access-6" value="1" <?php if (substr($access->access,6,1) == 1){ echo 'checked'; } ?>>&nbsp;Set Periode</input><br/>
					<input type="checkbox" name="access-7" value="1" <?php if (substr($access->access,7,1) == 1){ echo 'checked'; } ?>>&nbsp;Exchange Money</input><br/>
					<input type="checkbox" name="access-8" value="1" <?php if (substr($access->access,8,1) == 1){ echo 'checked'; } ?>>&nbsp;Beginning Balance</input><br/>
					<input type="checkbox" name="access-9" value="1" <?php if (substr($access->access,9,1) == 1){ echo 'checked'; } ?>>&nbsp;General Journal</input><br/>
				</div>
				<div class="col-lg-3">
					<input type="checkbox" name="access-10" value="1" <?php if (substr($access->access,10,1) == 1){ echo 'checked'; } ?>>&nbsp;Ledger</input><br/>
					<input type="checkbox" name="access-11" value="1" <?php if (substr($access->access,11,1) == 1){ echo 'checked'; } ?>>&nbsp;Cash Mutation</input><br/>
					<input type="checkbox" name="access-12" value="1" <?php if (substr($access->access,12,1) == 1){ echo 'checked'; } ?>>&nbsp;Cash Mutation Report</input><br/>
					<input type="checkbox" name="access-13" value="1" <?php if (substr($access->access,13,1) == 1){ echo 'checked'; } ?>>&nbsp;Bank Voucher</input><br/>
					<input type="checkbox" name="access-14" value="1" <?php if (substr($access->access,14,1) == 1){ echo 'checked'; } ?>>&nbsp;Bank Voucher Report</input><br/>
					<input type="checkbox" name="access-15" value="1" <?php if (substr($access->access,15,1) == 1){ echo 'checked'; } ?>>&nbsp;Cash Voucher</input><br/>
					<input type="checkbox" name="access-16" value="1" <?php if (substr($access->access,16,1) == 1){ echo 'checked'; } ?>>&nbsp;Cash Voucher Report</input><br/>
					<!--
					<input type="checkbox" name="access-17" value="1" <?php if (substr($access->access,17,1) == 1){ echo 'checked'; } ?>>&nbsp;Tico Voucher</input><br/>
					<input type="checkbox" name="access-18" value="1" <?php if (substr($access->access,18,1) == 1){ echo 'checked'; } ?>>&nbsp;Tico Voucher Report</input><br/>
					-->
				</div>
				<div class="col-lg-3">
					<input type="checkbox" name="access-19" value="1" <?php if (substr($access->access,19,1) == 1){ echo 'checked'; } ?>>&nbsp;Fixed Asset</input><br/>
					<input type="checkbox" name="access-20" value="1" <?php if (substr($access->access,20,1) == 1){ echo 'checked'; } ?>>&nbsp;Journal Entry</input><br/>
					<input type="checkbox" name="access-21" value="1" <?php if (substr($access->access,21,1) == 1){ echo 'checked'; } ?>>&nbsp;Journal Entry Report</input><br/>
					<input type="checkbox" name="access-22" value="1" <?php if (substr($access->access,22,1) == 1){ echo 'checked'; } ?>>&nbsp;Adjustment Journal</input><br/>
					<input type="checkbox" name="access-23" value="1" <?php if (substr($access->access,23,1) == 1){ echo 'checked'; } ?>>&nbsp;Adjustment Journal Report</input><br/>
					<input type="checkbox" name="access-24" value="1" <?php if (substr($access->access,24,1) == 1){ echo 'checked'; } ?>>&nbsp;Work Sheet</input><br/>
					<input type="checkbox" name="access-25" value="1" <?php if (substr($access->access,25,1) == 1){ echo 'checked'; } ?>>&nbsp;Trial Balance</input><br/>
				</div>
				<div class="col-lg-3">
					<input type="checkbox" name="access-26" value="1" <?php if (substr($access->access,26,1) == 1){ echo 'checked'; } ?>>&nbsp;Profit & Lost</input><br/>
					<input type="checkbox" name="access-27" value="1" <?php if (substr($access->access,27,1) == 1){ echo 'checked'; } ?>>&nbsp;Balance Sheet</input><br/>
					<input type="checkbox" name="access-28" value="1" <?php if (substr($access->access,28,1) == 1){ echo 'checked'; } ?>>&nbsp;Cash FLow</input><br/>
					<input type="checkbox" name="access-29" value="1" <?php if (substr($access->access,29,1) == 1){ echo 'checked'; } ?>>&nbsp;Capital Change</input><br/>
					<input type="checkbox" name="access-30" value="1" <?php if (substr($access->access,30,1) == 1){ echo 'checked'; } ?>>&nbsp;Currency Balance</input><br/>
					<input type="checkbox" name="access-31" value="1" <?php if (substr($access->access,31,1) == 1){ echo 'checked'; } ?>>&nbsp;Closing Entries</input><br/>
				</div>
			</div>
		</div>
	</div>
	
	
	<Center>
		<input type="hidden" name="jumlah" value="31" />
		<button type="submit" name="save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
	</centeR>
	
	<?=form_close()?>
</div>

