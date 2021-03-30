<?php
 
class M_accounting extends CI_Model{

	public function __construct(){
		parent::__construct();
		$user = $this->session->userdata('username');
	}
	
	
	public function acc_trx($modul, $account, $type, $currency = 1, $field = '*'){
		$data = $this->m_crud->get_data('acc_trx', $field, 'id_trx_modul = '.$modul.' and id_trx_account = '.$account.' and type = "'.$type.'" and currency = '.$currency);
		return $data;
	}
	
	/*
	public function trx_bank_number($coa = null){
		$trx = null;
		if($coa == null){ 
			$data = $this->m_crud->read_data('acc_bank_voucher', 
				'coa, substring(coa, "-3", 3) as id_coa, max(substring(id_bank_voucher, "-3", 3))+1 as id_no', 
				'id_bank_voucher like "%/'.date('ym').'/%"', null, 'coa'
			); 
			$coa="11";
			$i=0; foreach($data as $row){
				$trx[$i] = array('coa'=>$row['coa'], 'trx'=>'BVO-'.$row['id_coa'].'/'.date('ym').'/'.str_pad($row['id_no'], 3, '0', STR_PAD_LEFT));
				$coa .= ',"'.$row['coa'].'"';
				$i++; 
			}
			$coa = $this->m_crud->read_data('coa', '*, substring(coa_id, "-3", 3) as id_coa', 'coa_id like "11%" and coa_id not in ('.substr($coa, 1).')', 'coa_id asc');
			foreach($coa as $row){ 
				$trx[$i] = array('coa'=>$row['coa_id'], 'trx'=>'BVO-'.$row['id_coa'].'/'.date('ym').'/'.str_pad(1, 3, '0', STR_PAD_LEFT)); 
				$i++;
			}
		} else{ 
			$data = $this->m_crud->read_data('acc_bank_voucher', 
				'coa, substring(coa, "-3", 3) as id_coa, max(substring(id_bank_voucher, "-3", 3))+1 as id_no', 
				'id_bank_voucher like "%/'.date('ym').'/%" and coa = "'.$coa.'"', null, 'coa'
			); 
			$i=0; foreach($data as $row){
				$trx[$i] = array('coa'=>$row['coa'], 'trx'=>'BVO-'.$row['id_coa'].'/'.date('ym').'/'.$row['id_no']);
			$i++; }
		}
		return $trx;
	}
	
	public function trx_cash_number($coa = null){
		$trx = null;
		if($coa == null){ 
			$data = $this->m_crud->read_data('acc_cash_voucher', 
				'coa, substring(coa, "-3", 3) as id_coa, max(substring(id_cash_voucher, "-3", 3))+1 as id_no', 
				'id_cash_voucher like "%/'.date('ym').'/%"', null, 'coa'
			); 
			$coa="11";
			$i=0; foreach($data as $row){
				$trx[$i] = array('coa'=>$row['coa'], 'trx'=>'CVO-'.$row['id_coa'].'/'.date('ym').'/'.str_pad($row['id_no'], 3, '0', STR_PAD_LEFT));
				$coa .= ',"'.$row['coa'].'"';
				$i++; 
			}
			$coa = $this->m_crud->read_data('coa', '*, substring(coa_id, "-3", 3) as id_coa', 'coa_id like "11%" and coa_id not in ('.substr($coa, 1).')', 'coa_id asc');
			foreach($coa as $row){ 
				$trx[$i] = array('coa'=>$row['coa_id'], 'trx'=>'CVO-'.$row['id_coa'].'/'.date('ym').'/'.str_pad(1, 3, '0', STR_PAD_LEFT)); 
				$i++;
			}
		} else{ 
			$data = $this->m_crud->read_data('acc_cash_voucher', 
				'coa, substring(coa, "-3", 3) as id_coa, max(substring(id_cash_voucher, "-3", 3))+1 as id_no', 
				'id_cash_voucher like "%/'.date('ym').'/%" and coa = "'.$coa.'"', null, 'coa'
			); 
			$i=0; foreach($data as $row){
				$trx[$i] = array('coa'=>$row['coa'], 'trx'=>'CVO-'.$row['id_coa'].'/'.date('ym').'/'.$row['id_no']);
			$i++; }
		}
		return $trx;
	}
	
	public function trx_tico_number($coa = null){
		$trx = null;
		if($coa == null){ 
			$data = $this->m_crud->read_data('acc_tico_voucher', 
				'coa, substring(coa, "-3", 3) as id_coa, max(substring(id_tico_voucher, "-3", 3))+1 as id_no', 
				'id_tico_voucher like "%/'.date('ym').'/%"', null, 'coa'
			); 
			$coa="11";
			$i=0; foreach($data as $row){
				$trx[$i] = array('coa'=>$row['coa'], 'trx'=>'TVO-'.$row['id_coa'].'/'.date('ym').'/'.str_pad($row['id_no'], 3, '0', STR_PAD_LEFT));
				$coa .= ',"'.$row['coa'].'"';
				$i++; 
			}
			$coa = $this->m_crud->read_data('coa', '*, substring(coa_id, "-3", 3) as id_coa', 'coa_id like "11%" and coa_id not in ('.substr($coa, 1).')', 'coa_id asc');
			foreach($coa as $row){ 
				$trx[$i] = array('coa'=>$row['coa_id'], 'trx'=>'TVO-'.$row['id_coa'].'/'.date('ym').'/'.str_pad(1, 3, '0', STR_PAD_LEFT)); 
				$i++;
			}
		} else{ 
			$data = $this->m_crud->read_data('acc_tico_voucher', 
				'coa, substring(coa, "-3", 3) as id_coa, max(substring(id_tico_voucher, "-3", 3))+1 as id_no', 
				'id_tico_voucher like "%/'.date('ym').'/%" and coa = "'.$coa.'"', null, 'coa'
			); 
			$i=0; foreach($data as $row){
				$trx[$i] = array('coa'=>$row['coa'], 'trx'=>'TVO-'.$row['id_coa'].'/'.date('ym').'/'.$row['id_no']);
			$i++; }
		}
		return $trx;
	}
	*/
	
	public function coa($id, $field = '*'){
		$data = $this->m_crud->get_data('coa', $field, "coa_id = '".$id."'");
		if(substr($field,0,1)=='*'){ return $data; }
		else{ return $data[$field]; }
	}
	
	public function account($field = '*', $where = null){
		if($field == '*'){ $select = '*, coa.nama as nama_coa, coa_group.nama as nama_group, coa_kategori.nama as nama_kategori'; }
		else { $select = $field; }
		$data = $this->m_crud->join_data('coa', $select, array('coa_group', 'coa_kategori'), 
			array('coa.group_id = coa_group.group_id', 'coa_group.kategori_id = coa_kategori.kategori_id'), $where
		);
		if(substr($field,0,1)=='*'){ return $data; }
		else{ return $data[$field]; }
	}
	
	public function depr($perolehan, $estimasi, $susut, $caption=null){
		$accum_depr = null;
		$depr = $perolehan / $estimasi;
		
		$accum = $depr * $susut;
		
		$accum_depr = array('susut' => $accum, 'sisa' => $perolehan - $accum);
		
		if($caption == null){ return $accum_depr; }
		else{ return $accum_depr[$caption]; }
	} 
	
	public function accum_depr($tanggal, $perolehan, $estimasi, $caption=null){
		$accum_depr = null;
		$depr = $perolehan / $estimasi;
		$tgl_susut = date('Y-m-d', strtotime('-1 day', strtotime(date('Y') . '-' . date('m') . '-' . 01)));
		$tgl_perolehan = date('Y-m-d', strtotime('-1 day', strtotime(substr($tanggal, 0, 4) . '-' . substr($tanggal, 5, 2) . '-' . 01)));
		
		$selisih = $this->m_website->selisih_bulan($tgl_perolehan, $tgl_susut);
		
		if($selisih <= $estimasi){ $accum = $depr * $selisih; } 
		else { $accum = $depr * $estimasi; }
		
		$accum_depr = array('susut' => $accum, 'sisa' => $perolehan - $accum);
		
		if($caption == null){ return $accum_depr; }
		else{ return $accum_depr[$caption]; }
	} 
	
	public function entry_accum_depr(){
		$fixed_asset = $this->m_crud->read_data('acc_fixed_asset', '*', 'status = 1');
		$this->db->trans_begin();
		foreach($fixed_asset as $row){
			$susut = null; $status = 1;
			$tgl_susut = date('Y-m-d', strtotime('-1 day', strtotime(date('Y') . '-' . date('m') . '-' . 01)));
			$tgl_perolehan = date('Y-m-d', strtotime('-1 day', strtotime(substr($row['tanggal'], 0, 4) . '-' . substr($row['tanggal'], 5, 2) . '-' . 01)));
			$selisih = $this->m_website->selisih_bulan($tgl_perolehan, $tgl_susut);
			if($selisih <= $row['estimasi'] && $selisih > $row['entry']){
				$accum_depr = $this->m_accounting->accum_depr($row['tanggal'], $row['perolehan']*$row['qty'], $row['estimasi'], 'susut');
				$susut = ($accum_depr/$selisih) * ($selisih - $row['entry']);
				$this->m_crud->create_data('acc_general_journal', array(
					'tanggal' => $tgl_susut.' '.date('H:i:s'), 'id_trx' => $row['id_fixed_asset'], 'coa' => $row['expense'],
					'debit' => $susut, 'credit' => 0, 'currency' => 1, 'rate' => 1, 'link_report'=>'accounting/fixed_asset/detail/', 'descrip' => 'Accum Depr',
					'lokasi' => $this->m_website->get_lokasi()
				));
				$this->m_crud->create_data('acc_general_journal', array(
					'tanggal' => $tgl_susut.' '.date('H:i:s'), 'id_trx' => $row['id_fixed_asset'], 'coa' => $row['accum'],
					'debit' => 0, 'credit' => $susut, 'currency' => 1, 'rate' => 1, 'link_report'=>'accounting/fixed_asset/detail/', 'descrip' => 'Accum Depr',
					'lokasi' => $this->m_website->get_lokasi()
				));
				if($selisih >= $row['estimasi']){ $status = 2; }
				$this->m_crud->update_data('acc_fixed_asset', array(
					'entry' => $selisih,
					'status' => $status
				), "id_fixed_asset = '".$row['id_fixed_asset']."'");
			} 
		} 
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		} else { 
			$this->db->trans_commit();
		}
	}
	
	public function hitung_kurs($nominal = 0, $currency = null){ 
		$kurs = $this->m_crud->get_data('acc_kurs_uang', 'nama, rate', 'id_kurs_uang = "'.$currency.'"');
		isset($kurs['rate'])?$kurs = $kurs['rate']:$kurs=1;
		$data = $nominal * $kurs;
		return $data;
	} 
	
	public function hitung_kurs_account($nominal = 0, $id = null){ 
		$kurs = $this->m_crud->join_data('acc_kurs_uang', 'nama, rate', 'acc_kurs_account', 'id_kurs_uang = kurs_uang', 'coa = "'.$id.'"');
		isset($kurs[0]['rate'])?$kurs = $kurs[0]['rate']:$kurs=1;
		$data = $nominal * $kurs;
		return $data;
	}
	
	public function saldo_awal($id, $periode = null){
		$data = 0;
		if($periode == null){ $periode = $this->m_crud->get_data('acc_periode', 'tanggal_awal', 'status = 3')['tanggal_awal']; }
		
		$where = "coa = '".$id."'"; 
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		$beginning = $this->m_crud->get_data('acc_beginning_balance', 'sum(balance) as balance', $where);
		//mysqli
		//$select = "if((select balance from coa where coa_id = '".$id."') = 'D', sum(debit - credit), sum(credit - debit)) as saldo";
		//sqlsrv
		$select = "(case (select balance from coa where coa_id = '".$id."') when 'D' then sum(debit - credit) else sum(credit - debit) end) as saldo";
		$where = "coa = '".$id."' and tanggal < '".substr($periode, 0, 10)." 00:00:00'"; 
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		$saldo = $this->m_crud->read_data('acc_general_journal', $select, $where);
		$data = $beginning['balance'] + ($saldo[0]['saldo']);
		
		return $data;
	}
	
	public function saldo_awal_asing($id, $periode = null){
		$data = 0;
		if($periode == null){ $periode = $this->m_crud->get_data('acc_periode', 'tanggal_awal', 'status = 3')['tanggal_awal']; }
		
		$where = "coa = '".$id."'"; 
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		$beginning = $this->m_crud->get_data('acc_beginning_balance', 'sum(balance / rate) as saldo', $where)['saldo'];
		//mysqli
		//$select = "if((select balance from coa where coa_id = '".$id."') = 'D', sum(debit - credit), sum(credit - debit)) as saldo";
		//sqlsrv
		$select = "(case (select balance from coa where coa_id = '".$id."') when 'D' then sum((debit - credit) / rate) else sum((credit - debit) / rate) end) as saldo";
		$where = "coa = '".$id."' and tanggal < '".substr($periode, 0, 10)." 00:00:00'"; 
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		$saldo = $this->m_crud->read_data('acc_general_journal', $select, "coa = '".$id."' and tanggal < '".substr($periode, 0, 10)." 00:00:00'");
		$data = $beginning + ($saldo[0]['saldo']);
		
		return $data;
	}
	
	public function saldo_akhir($id, $periode = null){
		$data = 0;
		$where = "coa = '".$id."'"; 
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		$beginning = $this->m_crud->get_data('acc_beginning_balance', 'balance', $where)['balance'];
		
		if($periode != null && is_array($periode)){ 
			$where = "coa = '".$id."' and tanggal >= '".substr($periode[0], 0, 10)." 00:00:00' and tanggal <= '".substr($periode[1], 0, 10)." 23:59:59'";
		} else { 
			if($periode == null){ $periode = $this->m_crud->get_data('acc_periode', 'tanggal_awal', 'status = 3')['tanggal_awal']; } 
			$where = "coa = '".$id."' and tanggal <= '".substr($periode, 0, 10)." 23:59:59'";
		}
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		
		//mysqli
		//$select = "if((select balance from coa where coa_id = '".$id."') = 'D', sum(debit - credit), sum(credit - debit)) as saldo";
		
		//sqlsrv
		$select = "(case (select balance from coa where coa_id = '".$id."') when 'D' then sum(debit - credit) else sum(credit - debit) end) as saldo";
		$saldo = $this->m_crud->read_data('acc_general_journal', $select, $where);
		$adjustment = $this->m_accounting->saldo_adjustment($id, $periode);
		
		$data = $beginning + ($saldo[0]['saldo']) + $adjustment;
		
		return $data;
	}
	
	public function saldo_akhir_asing($id, $periode = null){
		$data = 0;
		$where = "coa = '".$id."'"; 
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		$beginning = $this->m_crud->get_data('acc_beginning_balance', '(balance / rate) as saldo', $where)['saldo'];
		
		if($periode != null && is_array($periode)){ 
			$where = "coa = '".$id."' and tanggal >= '".substr($periode[0], 0, 10)." 00:00:00' and tanggal <= '".substr($periode[1], 0, 10)." 23:59:59'";
		} else { 
			if($periode == null){ $periode = $this->m_crud->get_data('acc_periode', 'tanggal_awal', 'status = 3')['tanggal_awal']; } 
			$where = "coa = '".$id."' and tanggal <= '".substr($periode, 0, 10)." 23:59:59'";
		}
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		
		//mysqli
		//$select = "if((select balance from coa where coa_id = '".$id."') = 'D', sum((debit - credit) / rate), sum((credit - debit) / rate)) as saldo";
		//sqlsrv
		$select = "(case (select balance from coa where coa_id = '".$id."') when 'D' then sum((debit - credit) / rate) else sum((credit - debit) / rate) end) as saldo";
		
		$saldo = $this->m_crud->read_data('acc_general_journal', $select, $where);
		$adjustment = $this->m_accounting->saldo_adjustment_asing($id, $periode);
		
		$data = $beginning + ($saldo[0]['saldo']) + $adjustment;
		
		return $data;
	}
	
	public function modal_awal($periode = null){
		$data = 0;
		if($periode == null){ $periode = $this->m_crud->get_data('acc_periode', 'tanggal_awal', 'status = 3')['tanggal_awal']; }
		
		$select = 'sum(balance) as beginning';
		$where = "coa like '310%'"; 
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		$beginning = $this->m_crud->read_data('acc_beginning_balance', $select, $where);
		
		$select = 'sum(credit - debit) as saldo';
		$where = "coa like '310%' and tanggal < '".substr($periode,0,10)." 00:00:00'"; 
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		$saldo = $this->m_crud->read_data('acc_general_journal', $select, $where);
		$data = $beginning[0]['beginning'] + ($saldo[0]['saldo']);
		
		return $data;
	} 
	
	public function saldo_adjustment($id, $periode = null){
		$data = 0;
		if($periode != null && is_array($periode)){ 
			$where = "coa = '".$id."' and tanggal >= '".substr($periode[0], 0, 10)." 00:00:00' and tanggal <= '".substr($periode[1], 0, 10)." 23:59:59'";
		} else { 
			if($periode == null){ $periode = $this->m_crud->get_data('acc_periode', 'tanggal_awal', 'status = 3')['tanggal_awal']; } 
			$where = "coa = '".$id."' and tanggal <= '".substr($periode, 0, 10)." 23:59:59'";
		}
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		
		//mysqli
		//$select = 'if((select balance from coa where coa_id = "'.$id.'") = "D", sum(debit - credit), sum(credit - debit)) as saldo';
		//sqlsrv
		$select = "(case (select balance from coa where coa_id = '".$id."') when 'D' then sum(debit - credit) else sum(credit - debit) end) as saldo";
		
		$saldo = $this->m_crud->read_data('acc_adjustment_journal', $select, $where);
		$data = ($saldo[0]['saldo']);
		
		return $data;
	}
	
	public function saldo_adjustment_asing($id, $periode = null){
		$data = 0;
		if($periode != null && is_array($periode)){ 
			$where = "coa = '".$id."' and tanggal >= '".substr($periode[0], 0, 10)." 00:00:00' and tanggal <= '".substr($periode[1], 0, 10)." 23:59:59'";
		} else { 
			if($periode == null){ $periode = $this->m_crud->get_data('acc_periode', 'tanggal_awal', 'status = 3')['tanggal_awal']; } 
			$where = "coa = '".$id."' and tanggal <= '".substr($periode, 0, 10)." 23:59:59'";
		}
		if($this->m_website->get_lokasi()!='all'){ ($where==null)?$where="lokasi = '".$this->m_website->get_lokasi()."'":$where.=" and lokasi = '".$this->m_website->get_lokasi()."'"; }
		
		//mysqli
		//$select = 'if((select balance from coa where coa_id = "'.$id.'") = "D", sum((debit - credit) / rate), sum((credit - debit) / rate)) as saldo';
		//sqlsrv
		$select = "(case (select balance from coa where coa_id = '".$id."') when 'D' then sum((debit - credit) / rate) else sum((credit - debit) / rate) end) as saldo";
		
		$saldo = $this->m_crud->read_data('acc_adjustment_journal', $select, $where);
		$data = ($saldo[0]['saldo']);
		
		return $data;
	}
	
	public function plbs_account($jenis=null, $account=null){
		//pl
		$plbs_account['kategori']['revenue'] = 41; $plbs_account['group']['revenue'] = 41; $plbs_account['coa']['revenue'] = null;
		$plbs_account['kategori']['cogs'] = 51; $plbs_account['group']['cogs'] = 51; $plbs_account['coa']['cogs'] = null;
		$plbs_account['kategori']['overhead'] = 51; $plbs_account['group']['overhead'] = 52; $plbs_account['coa']['overhead'] = null;
		$plbs_account['kategori']['expense'] = 61; $plbs_account['group']['expense'] = 61; $plbs_account['coa']['expense'] = null;
		$plbs_account['kategori']['other_income'] = 71; $plbs_account['group']['other_income'] = 711; $plbs_account['coa']['other_income'] = null;
		$plbs_account['kategori']['other_expense'] = 72; $plbs_account['group']['other_expense'] = 721; $plbs_account['coa']['other_expense'] = null;
		$plbs_account['kategori']['other_expense'] = 72; $plbs_account['group']['profit_sharing'] = 722; $plbs_account['coa']['profit_sharing'] = null;
		$plbs_account['kategori']['other_expense'] = 72; $plbs_account['group']['tax_income'] = 723; $plbs_account['coa']['tax_income'] = '72301';
		//$plbs_account['kategori']['tax'] = null; $plbs_account['group']['tax'] = null; $plbs_account['coa']['tax'] = '72301';
		
		//bs
		$plbs_account['kategori']['cash'] = 11; $plbs_account['group']['cash'] = 111; $plbs_account['coa']['cash'] = null;
		$plbs_account['kategori']['bank'] = 11; $plbs_account['group']['bank'] = 112; $plbs_account['coa']['bank'] = null;
		$plbs_account['kategori']['other_cash_bank'] = 11; $plbs_account['group']['other_cash_bank'] = 113; $plbs_account['coa']['other_cash_bank'] = null;
		$plbs_account['kategori']['deposit'] = 11; $plbs_account['group']['deposit'] = 114; $plbs_account['coa']['deposit'] = null;
		$plbs_account['kategori']['ar'] = 12; $plbs_account['group']['ar'] = 12; $plbs_account['coa']['ar'] = null;
		$plbs_account['kategori']['inventory'] = 13; $plbs_account['group']['inventory'] = 13; $plbs_account['coa']['inventory'] = null;
		$plbs_account['kategori']['oca'] = 14; $plbs_account['group']['oca'] = 142; $plbs_account['coa']['oca'] = null;
		$plbs_account['kategori']['prepaid'] = 14; $plbs_account['group']['prepaid'] = 14; $plbs_account['coa']['prepaid'] = null;
		$plbs_account['kategori']['fixed_assets'] = 15; $plbs_account['group']['fixed_assets'] = 15; $plbs_account['coa']['fixed_assets'] = null;
		$plbs_account['kategori']['depreciation'] = 16; $plbs_account['group']['depreciation'] = 16; $plbs_account['coa']['depreciation'] = null;
		$plbs_account['kategori']['ap'] = 21; $plbs_account['group']['ap'] = 21; $plbs_account['coa']['ap'] = null;
		$plbs_account['kategori']['ocl'] = 22; $plbs_account['group']['ocl'] = 22; $plbs_account['coa']['ocl'] = null;
		$plbs_account['kategori']['capital'] = 31; $plbs_account['group']['capital'] = 31; $plbs_account['coa']['capital'] = null;
		
		return $plbs_account[$jenis][$account];
	}
	
	public function cash_sales($account = null, $periode = null){
		if($account == null){ $account = $this->m_accounting->account('*', 'coa_group.group_id = '.$this->m_accounting->plbs_account('group', 'revenue')); }
		if($periode == null){ $periode = $this->m_accounting->periode(); }
		$cash = 0;
		foreach($account as $row){  
			$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode);
			$cash = $cash + $ending; 
		}
		return $cash;
	}
	
	public function cash_cogs($account = null, $periode = null){
		if($account == null){ $account = $this->m_accounting->account('*', 'coa_group.group_id = '.$this->m_accounting->plbs_account('group', 'cogs')); }
		if($periode == null){ $periode = $this->m_accounting->periode(); }
		$cash = 0;
		foreach($account as $row){  
			$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode);
			$cash = $cash + $ending; 
		}
		return $cash;
	}
	
	public function net_profit_pl($account = null, $periode = null){
		if($account == null){ $account = $this->m_accounting->account('*', 'coa_kategori.kategori_id >= 41 and coa_kategori.kategori_id <= 72'); }
		if($periode == null){ $periode = $this->m_accounting->periode(); }
		
		$revenue = 0; $account['revenue'] = $this->m_accounting->plbs_account('group', 'revenue');
		$cogs = 0; $account['cogs'] = $this->m_accounting->plbs_account('group', 'cogs');
		$overhead = 0; $account['overhead'] = $this->m_accounting->plbs_account('group', 'overhead');
		$expense = 0; $account['expense'] = $this->m_accounting->plbs_account('group', 'expense');
		$other_income = 0; $account['other_income'] = $this->m_accounting->plbs_account('kategori', 'other_income');
		$other_expense = 0; $account['other_expense'] = $this->m_accounting->plbs_account('kategori', 'other_expense');
		foreach($account as $row){ 
			if($row['group_id'] == $account['revenue']){ 
				$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode);
				$revenue = $revenue + $ending; 
			} else if($row['group_id'] == $account['cogs']){ 
				$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode);
				$cogs = $cogs + $ending; 
			} else if($row['group_id'] == $account['overhead']){ 
				$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode);
				$overhead = $overhead + $ending; 
			} else if($row['group_id'] == $account['expense']){
			//} else if($row['group_id'] == $account['expense'] && $row['coa_id'] != 13126){ 
				$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode);
				$expense = $expense + $ending; 
			} else if($row['kategori_id'] == $account['other_income']){
				$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode);
				$other_income = $other_income + $ending; 
			} else if($row['kategori_id'] == $account['other_expense']){
				$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode);
				$other_expense = $other_expense + $ending; 
			}
		}
		$groos_profit = $revenue - ($cogs + $overhead);
		$income = $groos_profit - $expense;
		$other_inex = $other_income - $other_expense;
		$net_profit = $income + $other_inex;
		//$tax = $this->m_accounting->saldo_akhir(72301, $periode);
		//$net_profit = $income + $other_inex - $tax;
		return $net_profit;
	}
	
	public function periode(){
		return date('Y-m-d');
	}
	
	public function ongoing_periode($field = '*'){
		$last = $this->m_crud->max_data('acc_periode', 'id_periode', "lokasi = '".$this->m_website->get_lokasi()."' and status = 3");
		$data = $this->m_crud->get_data('acc_periode', $field, 'id_periode = '.$last);
		if($data==null){ $data = array('tanggal_awal' => date('Y').'-01-01', 'tanggal_akhir' => date('Y-m-d')); }
		if(substr($field,0,1)=='*'){ return $data; }
		else{ return $data[$field]; }
	}
	
	public function setting_periode($action = null, $data = null){ 
		// status periode : 1 = beginning, 2 = periode, 3 = ongoing, 4 = expiry
		$last = $this->m_crud->max_data('acc_periode', 'id_periode', "status = 3 and lokasi = '".$this->m_website->get_lokasi()."'");
		//mysqli
		//$periode = $this->m_crud->get_data('acc_periode', '*, DATE_ADD(tanggal_awal, INTERVAL 1 YEAR) as awal, DATE_ADD(tanggal_akhir, INTERVAL 1 YEAR) as akhir', 'id_periode = '.$last);
		//sqlsrv
		$periode = $this->m_crud->get_data('acc_periode', '*, DATEADD(year, 1, tanggal_awal) as awal, DATEADD(year, 1, tanggal_akhir) as akhir', 'id_periode = '.$last);
		
		$ongoing = $this->m_website->selisih_hari($periode['tanggal_akhir'], date('Y-m-d'));
		/*if($action == null && $ongoing < 0){
			$this->m_crud->update_data('acc_periode', array('status' => 4), 'id_periode = '.$periode['id_periode']);
			$this->m_crud->create_data('acc_periode', array(
				'jenis' => $periode['jenis'], 
				'tanggal_awal' => $periode['awal'], 
				'tanggal_akhir' => $periode['akhir'], 
				'status' => 3
			));
		} else*/ if($action == 'update'){
			if($last == 0){
				$this->m_crud->create_data('acc_periode', array(
					'jenis' => $data['jenis'], 
					'tanggal_awal' => $data['awal'], 
					'tanggal_akhir' => $data['akhir'], 
					'status' => 3,
					'lokasi' => $data['lokasi']
				));
			}
			$this->m_crud->update_data('acc_periode', array(
				'jenis' => $data['jenis'], 
				'tanggal_awal' => $data['awal'], 
				'tanggal_akhir' => $data['akhir']
			), 'id_periode = '.$periode['id_periode']);
		} else if($action == 'closing'){
			if($ongoing < 0 && $periode['id_periode'] != null){
				$this->m_crud->update_data('acc_periode', array(
					'status' => 4, 
					'tanggal_closing' => $_POST['tanggal'].' '.date('H:i:s'), 
					'user_id' => $this->user
				), 'id_periode = '.$periode['id_periode']);
				$coa_pl = 71102; //$coa = array(2 => array(1=>'%U%S%D%', 2 => '%U%S%D%'), 3 => array(1 => '%J%P%Y%', 2 => '%Y%E%N%'));
				for($i=1;$i<=$_POST['jumlah'];$i++){
					$this->m_crud->create_data('acc_periode_kurs', array(
						'periode' => $periode['id_periode'],
						'currency' => $_POST['currency'.$i],
						'rate' => $_POST['exchange'.$i]
					));
					$selisih = 0; 
					if($_POST['currency'.$i]>1){
						//$currency_balance = $this->m_accounting->account('*', "(coa_kategori.kategori_id = 11 or coa_kategori.kategori_id = 12 or coa_kategori.kategori_id = 21) and (coa.nama like '".$coa[$_POST['currency'.$i]][1]."' or coa.nama like '".$coa[$_POST['currency'.$i]][2]."')");
						$currency_balance = $this->m_accounting->account('*', "currency <> 1");
						foreach($currency_balance as $row){ 
							$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode['tanggal_akhir']); 
							$ending_asing = $this->m_accounting->saldo_akhir_asing($row['coa_id'], $periode['tanggal_akhir']); 
							$saldo = $ending_asing * $_POST['exchange'.$i];
							$pl = $ending - $saldo;
							if($saldo > 0 && $saldo != $ending){
								$this->m_crud->create_data('acc_general_journal', array(
									'id_trx' => $periode['id_periode'],
									'tanggal' => substr($periode['tanggal_akhir'], 0, 10).' '.date('H:i:s'),
									'coa' => $row['coa_id'],
									'descrip' => 'Closing',
									'debit' => ($saldo>$ending)?$pl*(-1):0,
									'credit' => ($saldo<$ending)?$pl:0,
									'link_report' => 'accounting/closing-entries/detail/',
									'currency' => $_POST['currency'.$i], 
									'rate' => $_POST['exchange'.$i],
									'lokasi' => $this->m_website->get_lokasi()
								));
								$selisih = $selisih + $pl;
							}
						}
						if($selisih != 0){
							$this->m_crud->create_data('acc_general_journal', array(
								'id_trx' => $periode['id_periode'],
								'tanggal' => substr($periode['tanggal_akhir'], 0, 10).' '.date('H:i:s'),
								'coa' => $coa_pl,
								'descrip' => 'Closing',
								'debit' => ($selisih>0)?$selisih:0,
								'credit' => ($selisih<0)?$selisih*(-1):0,
								'link_report' => 'accounting/closing-entries/detail/',
								'currency' => $_POST['currency'.$i], 
								'rate' => $_POST['exchange'.$i],
								'lokasi' => $this->m_website->get_lokasi()
							));
						}
					}
				}
				return true;
			} else { return false; }
		}
	}
	
}

