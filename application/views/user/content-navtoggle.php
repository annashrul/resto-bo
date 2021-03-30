      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

          <!-- Sidebar user panel (optional) -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="<?php echo base_url();?>assets/images/user-default.png" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
              <p><?php echo $account->nama; ?></p>
              <!-- Status -->
              <small>
				  <?php echo $account->alamat; ?>
			  </small>
            </div>
          </div>

          <!-- search form (Optional)
          <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </form>
          /.search form -->

          <!-- Sidebar Menu -->
			<ul class="sidebar-menu">
				<li class="header">MENU</li>
				<!-- Optionally, you can add icons to the links -->
				<li class="<?php if($page == 'dashboard'){ echo 'active'; } ?>">
					<a href="<?=base_url()?>user">  <i class="fa fa-fw fa-dashboard"></i> <span>Dashboard</span> </a>                       
				</li>
				<li class="<?php if($page == 'preference'){ echo 'active'; } ?>">
					<a href="<?=base_url()?>user/preference" style="<?php if (substr($access->access,0,1) != 1 ){ echo 'display:none;'; } ?>">  <i class="fa fa-fw fa-cog"></i> <span>Preference</span></a>
					<!--<a href="<?=base_url()?>user/preference/edit/?trx=1" style="<?php if (substr($access->access,0,1) != 1 ){ echo 'display:none;'; } ?>">  <i class="fa fa-fw fa-cog"></i> <span>Preference</span></a>-->
				</li>
				
				<?php if ((substr($access->access,1,2) != 0)){ ?>
				<li class="treeview 
					<?php if($page == 'user_level' || $page == 'user_list'){ echo 'active'; } ?>">
					<a href="#"><i class="fa fa-fw fa-briefcase"></i> <span>Master Data</span> <i class="fa fa-angle-left pull-right"></i></a>
					<ul class="treeview-menu">
						<li class="<?php if($page == 'user_level'){ echo 'active'; } ?>"><a href="<?=base_url()?>user/user-level" style="<?php if (substr($access->access,1,1) != 1 ){ echo 'display:none;'; } ?>">User Level</a></li>
						<li class="<?php if($page == 'user_list'){ echo 'active'; } ?>"><a href="<?=base_url()?>user/user-list" style="<?php if (substr($access->access,2,1) != 1 ){ echo 'display:none;'; } ?>">User List</a></li>
					</ul>
				</li>
				<?php } ?>
				
				<?php if (substr($access->access,3,31) != 0){ ?>
				<li class="treeview 
					<?php if($page == 'account_category' || $page == 'account_group' || 
								$page == 'code_of_account' || $page == 'set_periode' ||
								$page == 'exchange_money' || 
								$page == 'beginning_balance' || $page == 'general_journal' ||
								$page == 'bank_voucher' || $page == 'bank_voucher_report' || 
								$page == 'cash_mutation' || $page == 'cash_mutation_report' || 
								$page == 'cash_voucher' || $page == 'cash_voucher_report' || 
								$page == 'tico_voucher' || $page == 'tico_voucher_report' || 
								$page == 'journal_entry' || $page == 'journal_entry_report' || 
								$page == 'ledger' || $page == 'trial_balance' ||
								$page == 'adjustment_journal' || $page == 'adjustment_journal_report' ||
								$page == 'work_sheet' || $page == 'profit_loss' || 
								$page == 'balance_sheet' || $page == 'capital_change' ||
								$page == 'closing_entries' || $page == 'currency_balance' ||
								$page == 'cash_flow' || $page == 'fixed_asset' 
							){ echo 'active'; } ?>">
					<a href="#"><i class="fa fa-fw fa-money"></i> <span>Accounting</span> <i class="fa fa-angle-left pull-right"></i></a>
					<ul class="treeview-menu">
						<li class="<?php if($page == 'account_category'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/account-category" style="<?php if (substr($access->access,3,1) != 1 ){ echo 'display:none;'; } ?>">Account Category</a></li>
						<li class="<?php if($page == 'account_group'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/account-group" style="<?php if (substr($access->access,4,1) != 1 ){ echo 'display:none;'; } ?>">Account Group</a></li>
						<li class="<?php if($page == 'code_of_account'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/code-of-account" style="<?php if (substr($access->access,5,1) != 1 ){ echo 'display:none;'; } ?>">Code of Account</a></li>
						<li class="<?php if($page == 'set_periode'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/set_periode" style="<?php if (substr($access->access,6,1) != 1 ){ echo 'display:none;'; } ?>">Set Periode</a></li>
						<li class="<?php if($page == 'exchange_money'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/exchange-money" style="<?php if (substr($access->access,7,1) != 1 ){ echo 'display:none;'; } ?>">Exchange Money</a></li>
						<li class="<?php if($page == 'beginning_balance'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/beginning-balance" style="<?php if (substr($access->access,8,1) != 1 ){ echo 'display:none;'; } ?>">Beginning Balance</a></li>
						<li class="<?php if($page == 'general_journal'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/general-journal" style="<?php if (substr($access->access,9,1) != 1 ){ echo 'display:none;'; } ?>">General Journal</a></li>
						<li class="<?php if($page == 'ledger'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/ledger" style="<?php if (substr($access->access,10,1) != 1 ){ echo 'display:none;'; } ?>">Ledger</a></li>
						<li class="<?php if($page == 'cash_mutation'||$page == 'cash_mutation_report'){ echo 'active'; } ?>" style="<?php if (substr($access->access,11,1) != 1 && substr($access->access,12,1) != 1){ echo 'display:none;'; } ?>">
							<a href="#">Cash Mutation <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li class="<?php if($page == 'cash_mutation'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/cash_mutation" style="<?php if (substr($access->access,11,1) != 1 ){ echo 'display:none;'; } ?>">Cash Mutation</a></li>
								<li class="<?php if($page == 'cash_mutation_report'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/cash-mutation-report" style="<?php if (substr($access->access,12,1) != 1 ){ echo 'display:none;'; } ?>">Cash Mutation Report</a></li>
							</ul>
						</li>
						<li class="<?php if($page == 'bank_voucher'||$page == 'bank_voucher_report'){ echo 'active'; } ?>" style="<?php if (substr($access->access,13,1) != 1 && substr($access->access,14,1) != 1){ echo 'display:none;'; } ?>">
							<a href="#">Bank Voucher <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li class="<?php if($page == 'bank_voucher'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/bank_voucher" style="<?php if (substr($access->access,13,1) != 1 ){ echo 'display:none;'; } ?>">Bank Voucher</a></li>
								<li class="<?php if($page == 'bank_voucher_report'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/bank_voucher_report" style="<?php if (substr($access->access,14,1) != 1 ){ echo 'display:none;'; } ?>">Bank Voucher Report</a></li>
							</ul>
						</li>
						<li class="<?php if($page == 'cash_voucher'||$page == 'cash_voucher_report'){ echo 'active'; } ?>" style="<?php if (substr($access->access,15,1) != 1 && substr($access->access,16,1) != 1){ echo 'display:none;'; } ?>">
							<a href="#">Cash Voucher <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li class="<?php if($page == 'cash_voucher'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/cash_voucher" style="<?php if (substr($access->access,15,1) != 1 ){ echo 'display:none;'; } ?>">Cash Voucher</a></li>
								<li class="<?php if($page == 'cash_voucher_report'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/cash-voucher-report" style="<?php if (substr($access->access,16,1) != 1 ){ echo 'display:none;'; } ?>">Cash Voucher Report</a></li>
							</ul>
						</li>
						<li class="<?php if($page == 'tico_voucher'||$page == 'tico_voucher_report'){ echo 'active'; } ?>" style="<?php if (substr($access->access,17,1) != 1 && substr($access->access,18,1) != 1){ echo 'display:none;'; } ?>">
							<a href="#">Tico Voucher <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li class="<?php if($page == 'tico_voucher'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/tico_voucher" style="<?php if (substr($access->access,17,1) != 1 ){ echo 'display:none;'; } ?>">Tico Voucher</a></li>
								<li class="<?php if($page == 'tico_voucher_report'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/tico-voucher-report" style="<?php if (substr($access->access,18,1) != 1 ){ echo 'display:none;'; } ?>">Tico Voucher Report</a></li>
							</ul>
						</li>
						<li class="<?php if($page == 'fixed_asset'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/fixed_asset" style="<?php if (substr($access->access,19,1) != 1 ){ echo 'display:none;'; } ?>">Fixed Asset</a></li>
						<li class="<?php if($page == 'journal_entry'||$page == 'journal_entry_report'){ echo 'active'; } ?>" style="<?php if (substr($access->access,20,1) != 1 && substr($access->access,21,1) != 1){ echo 'display:none;'; } ?>">
							<a href="#">Journal Entry <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li class="<?php if($page == 'journal_entry'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/journal-entry" style="<?php if (substr($access->access,20,1) != 1 ){ echo 'display:none;'; } ?>">Journal Entry</a></li>
								<li class="<?php if($page == 'journal_entry_report'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/journal-entry-report" style="<?php if (substr($access->access,21,1) != 1 ){ echo 'display:none;'; } ?>">Journal Entry Report</a></li>
							</ul>
						</li>
						<li class="<?php if($page == 'adjustment_journal'||$page == 'adjustment_journal_report'){ echo 'active'; } ?>" style="<?php if (substr($access->access,22,1) != 1 && substr($access->access,23,1) != 1){ echo 'display:none;'; } ?>">
							<a href="#">Adjustment Journal <i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
								<li class="<?php if($page == 'adjustment_journal'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/adjustment-journal" style="<?php if (substr($access->access,22,1) != 1 ){ echo 'display:none;'; } ?>">Adjustment Journal</a></li>
								<li class="<?php if($page == 'adjustment_journal_report'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/adjustment-journal-report" style="<?php if (substr($access->access,23,1) != 1 ){ echo 'display:none;'; } ?>">Adjustment Journal Report</a></li>
							</ul>
						</li>
						<li class="<?php if($page == 'work_sheet'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/work-sheet" style="<?php if (substr($access->access,24,1) != 1 ){ echo 'display:none;'; } ?>">Work Sheet</a></li>
						<li class="<?php if($page == 'trial_balance'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/trial-balance" style="<?php if (substr($access->access,25,1) != 1 ){ echo 'display:none;'; } ?>">Trial Balance</a></li>
						<li class="<?php if($page == 'profit_loss'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/profit-loss" style="<?php if (substr($access->access,26,1) != 1 ){ echo 'display:none;'; } ?>">Profit & Loss</a></li>
						<li class="<?php if($page == 'balance_sheet'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/balance-sheet" style="<?php if (substr($access->access,27,1) != 1 ){ echo 'display:none;'; } ?>">Balance Sheet</a></li>								
						<li class="<?php if($page == 'cash_flow'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/cash-flow" style="<?php if (substr($access->access,28,1) != 1 ){ echo 'display:none;'; } ?>">Cash Flow</a></li>
						<li class="<?php if($page == 'capital_change'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/capital-change" style="<?php if (substr($access->access,29,1) != 1 ){ echo 'display:none;'; } ?>">Capital Change</a></li>
						<li class="<?php if($page == 'currency_balance'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/currency_balance" style="<?php if (substr($access->access,30,1) != 1 ){ echo 'display:none;'; } ?>">Currency Balance</a></li>
						<li class="<?php if($page == 'closing_entries'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>accounting/closing-entries" style="<?php if (substr($access->access,31,1) != 1 ){ echo 'display:none;'; } ?>">Closing Entries</a></li>
					</ul>
				</li>
				<?php } ?>					
			</ul><!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
      </aside>
