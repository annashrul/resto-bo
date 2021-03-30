<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_member extends CI_Controller {

    /*
    alter table customer add one_signal_id varchar(250), biografi varchar(max), last_login datetime;
    alter table customer add email varchar(100), password varchar(100);
    alter table customer add verify varchar(2);
    alter table customer add id_register varchar(100);
    alter table customer add register varchar(10), foto varchar(100);
    alter table customer add tgl_register datetime, hash varchar(100);
    alter table customer add jenis_kelamin varchar(10);
    alter table customer add token_resset_password varchar(100);
    alter table site add splash varchar(max);
    alter table lokasi add gambar varchar(max), jam_buka time, jam_tutup time, lat varchar(max), lng varchar(max);
    create table poin (
        kode_trx varchar(50),
        tanggal datetime,
        customer varchar(50),
        poin_in money,
        poin_out money,
        lokasi varchar(30),
        keterangan varchar(max)
    );
    CREATE TABLE intro (
        id_intro int NOT NULL IDENTITY(1,1) primary key,
        tipe varchar(10) NOT NULL,
        background varchar(max) NOT NULL,
        judul varchar(max) NOT NULL,
        keterangan text not null
    );
    CREATE TABLE berita (
        id_berita int NOT NULL IDENTITY(1,1) primary key,
        data_donasi int DEFAULT NULL,
        judul varchar(250) NOT NULL,
        tanggal datetime NOT NULL,
        deskripsi text NOT NULL,
        foto varchar(250) NOT NULL,
        video text,
        sumber varchar(250) NOT NULL,
        slide varchar(10) NOT NULL,
    );
    */

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $site_data = $this->m_website->site_data();
        $this->site = str_replace(' ', '', strtolower($site_data->title));
        $this->user = $this->session->userdata($this->site . 'user');
        $this->share = "http://google.co.id";
        $this->dompet = 'BgGl1slbncH4WDFKcC64iUmpw81jZW';
        $this->pin = md5('123123');

        $this->data = array(
            'site' => $site_data
        );

    }

    public function get_site() {
        $response = array();

        $get_site = $this->m_crud->get_data("site", "title judul, meta_descr deskripsi, logo gambar", "site_id='2'");

        $response['status'] = true;
        $explode = explode('/', $get_site['gambar']);
        unset($explode[0]);
        $gambar = implode('/', $explode);
        $get_site['gambar'] = base_url().$gambar;
        $response['data'] = $get_site;

        echo json_encode($response);
    }

    public function req() {
        $this->m_crud->create_data("log", array('jenis'=>'A','code'=>'B','tgl'=>date('Y-m-d H:i:s'), 'keterangan'=>'C','user'=>'D'));

        echo json_encode(array('status'=>true));
    }

    public function update_onesignal($action){
        if($action=='member'){
            $this->db->trans_begin();

            $id_member = $_POST['id_member'];
            $one_signal_id = $_POST['one_signal_id'];

            $master = array('one_signal_id'=>$one_signal_id);

            $this->m_crud->update_data('customer', $master, "kd_cust = '".$id_member."'");

            $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
            foreach ($read_lokasi as $item) {
                $log = array(
                    'type' => 'U',
                    'table' => 'customer',
                    'data' => $master,
                    'condition' => "kd_cust = '".$id_member."'"
                );

                $data_log = array(
                    'lokasi' => $item['Kode'],
                    'hostname' => $item['server'],
                    'db_name' => $item['db_name'],
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('status'=>false));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status'=>1));
            }
        }
    }

    public function login() {
        $result = array();

        $username = $_POST['username'];
        $password = $_POST['password'];

        $get_user = $this->m_crud->get_data("customer", "*, kd_cust id_member", "tlp1='".$username."' AND status='1'");

        if ($get_user != null) {
            if (password_verify($password, $get_user['password'])) {
                if ($get_user['verify'] == '1') {
                    $result['otentikasi'] = false;
                } else {
                    $result['otentikasi'] = true;
                    $otentikasi = mt_rand(10000, 99999);

                    $data_member = array(
                        'hash' => $otentikasi,
                        'verify' => '0'
                    );

                    $this->m_crud->update_data("customer", $data_member, "kd_cust='".$get_user['id_member']."'");

                    $this->m_website->sms_otentikasi(array('tlp'=>$get_user['tlp1'], 'kode'=>$otentikasi));
                }
                $update_member = array('last_login'=>date('Y-m-d H:i:s'));
                if(isset($_POST['one_signal_id'])) {
                    $update_member['one_signal_id'] = $_POST['one_signal_id'];
                }
                $this->m_crud->update_data('customer', $update_member, "kd_cust = '".$get_user['id_member']."'");

                $result['status'] = true;
                $result['res_login'] = array('id_member'=>$get_user['kd_cust'],'tlp'=>$get_user['tlp1'],'tgl_register'=>date('Y-m-d', strtotime($get_user['tgl_register'])),'nama'=>strtoupper($get_user['Nama']),'foto'=>base_url().$get_user['foto'],'pesan'=>'Login berhasil.');
            } else {
                $result['status'] = false;
                $result['res_login'] = array('pesan'=>'Invalid username or password!');
            }
        } else {
            $result['status'] = false;
            $result['res_login'] = array('pesan'=>'Invalid username or password!');
        }

        echo json_encode($result);
    }

    public function register() {
        $result = array();

        $options = array('cost' => 12);
        $nama = ucwords($_POST['nama']);
        $tgl = date('Y-m-d', strtotime($_POST['tgl_lahir']));
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);
        $otentikasi = mt_rand(10000, 99999);
        $ol_code = $this->m_website->generate_kode("member", date('ymd'), null);

        $cek_user = $this->m_crud->get_data("customer", "kd_cust", "tlp1='".$username."'");

        if ($cek_user == null) {
            $this->db->trans_begin();

            $data_member = array(
                'kd_cust'=>$ol_code,
                'password'=>$password,
                'nama'=>$nama,
                'tgl_ultah'=>$tgl,
                'tlp1'=>$username,
                'status'=>'1',
                'foto'=>'assets/images/member/default.png',
                'tgl_register'=>date('Y-m-d H:i:s'),
                'verify'=>'1',//DIUBAH
                'register'=>'apps',
                'cust_type'=>'UMUM',
                'hash'=>$otentikasi,
                'ol_code' => $ol_code
            );

            $this->m_crud->create_data("customer", $data_member);

            $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
            foreach ($read_lokasi as $item) {
                $log = array(
                    'type' => 'I', //I insert, U update, D delete
                    'table' => "customer",
                    'data' => $data_member,
                    'condition' => ""
                );

                $data_log = array(
                    'lokasi' => $item['Kode'],
                    'hostname' => $item['server'],
                    'db_name' => $item['db_name'],
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);
            }

            if ($this->db->trans_status() === true) {
                $this->db->trans_commit();
                // $this->m_website->sms_otentikasi(array('tlp'=>$username, 'kode'=>$otentikasi)); //DIUBAH
                $result['status'] = true;
                $result['pesan'] = 'Registrasi berhasil!';
                $result['res_login'] = array('id_member'=>$ol_code,'tlp'=>$username,'nama'=>strtoupper($nama),'tgl_register'=>date('Y-m-d'),'foto'=>base_url().$data_member['foto']);
            } else {
                $this->db->trans_rollback();
                $result['status'] = false;
                $result['pesan'] = 'Registrasi gagal!';
            }
        } else {
            $result['status'] = false;
            $result['pesan'] = 'Nama pengguna sudah digunakan!';
        }

        echo json_encode($result);
    }

    public function forgot_password() {
        $result = array();

        $tlp = $_POST['tlp'];
        $otentikasi = mt_rand(10000, 99999);

        $get_cust = $this->m_crud->get_data("customer", "kd_cust", "tlp1='".$tlp."'");

        if ($get_cust != null) {
            $this->db->trans_begin();

            $this->m_crud->update_data("customer", array("hash"=>$otentikasi, "token_resset_password"=>"0"), "kd_cust='".$get_cust['kd_cust']."'");

            if ($this->db->trans_status() === true) {
                $this->db->trans_commit();
                $this->m_website->sms_otentikasi(array('tlp'=>$tlp, 'kode'=>$otentikasi));
                $result['status'] = true;
                $result['pesan'] = 'Forgot password berhasil';
                $result['data'] = array('id_member'=>$get_cust['kd_cust'], 'tlp'=>$tlp);
            } else {
                $this->db->trans_rollback();
                $result['status'] = false;
                $result['pesan'] = 'Data gagal disimpan';
            }
        } else {
            $result['status'] = false;
            $result['pesan'] = 'Nama pengguna tidak terdaftar';
        }

        echo json_encode($result);
    }

    public function new_password() {
        $result = array();

        $options = array('cost' => 12);
        $id_member = $_POST['id_member'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);

        $this->db->trans_begin();

        $this->m_crud->update_data("customer", array('password'=>$password), "kd_cust='".$id_member."'");

        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            $result['status'] = true;
            $result['pesan'] = 'Password berhasil dirubah.';
        } else {
            $this->db->trans_rollback();
            $result['status'] = false;
            $result['pesan'] = 'Data gagal disimpan';
        }

        echo json_encode($result);
    }

    public function otentikasi() {
        $result = array();
        $id_member = $_POST['id_member'];
        $otentikasi = $_POST['otentikasi'];
        $param = $_POST['param'];

        if ($param == 'register') {
            $cek_otentikasi = $this->m_crud->get_data("customer", "kd_cust", "kd_cust='".$id_member."' and hash='".$otentikasi."' and verify='0'");
        } else if ($param == 'forgot') {
            $cek_otentikasi = $this->m_crud->get_data("customer", "kd_cust", "kd_cust='".$id_member."' and hash='".$otentikasi."' and token_resset_password='0'");
        } else if ($param == 'telepon') {
            $cek_otentikasi = $this->m_crud->get_data("customer", "kd_cust, tlp2", "kd_cust='".$id_member."' and hash='".$otentikasi."' and '".date('Y-m-d H:i:s')."'<=left(convert(varchar, tglakhir, 120), 19)");
        }

        if ($cek_otentikasi != null) {
            $this->db->trans_begin();

            if ($param == 'register') {
                $data_member = array(
                    'verify' => '1'
                );
            } else if ($param == 'forgot') {
                $data_member = array(
                    'token_resset_password' => '1'
                );
            } else if ($param == 'telepon') {
                $data_member = array(
                    'tglakhir' => '',
                    'tlp1' => $cek_otentikasi['tlp2']
                );
            }

            if (isset($param)) {
                $this->m_crud->update_data("customer", $data_member, "kd_cust='" . $id_member . "'");
            }

            if ($this->db->trans_status() === true) {
                $this->db->trans_commit();
                $result['status'] = true;
                $result['pesan'] = 'Otentikasi berhasil!';
            } else {
                $this->db->trans_rollback();
                $result['status'] = false;
                $result['pesan'] = 'Otentikasi gagal!';
            }
        } else {
            $result['status'] = false;
            $result['pesan'] = 'Kode otentikasi salah';
        }

        echo json_encode($result);
    }

    public function kirim_otentikasi() {
        $result = array();

        $this->db->trans_begin();

        $id_member = $_POST['id_member'];
        $tlp = $_POST['tlp'];
        $param = $_POST['param'];
        $otentikasi = mt_rand(10000, 99999);

        $data_member = array(
            'hash' => $otentikasi
        );

        if ($param == 'register') {
            $data_member['verify'] = '0';
        } else if ($param == 'forgot') {
            $data_member['token_resset_password'] = '0';
        } else if ($param == 'telepon') {
            $data_member['tglakhir'] = $this->m_website->batas_waktu('+', date('Y-m-d H:i:s'));
        }

        $this->m_crud->update_data("customer", $data_member, "kd_cust='".$id_member."'");

        if ($this->db->trans_status() === true && $this->m_website->sms_otentikasi(array('tlp'=>$tlp, 'kode'=>$otentikasi)) === true) {
            $this->db->trans_commit();
            $result['status'] = true;
            $result['pesan'] = 'Kode akan dikirimkan ke nomor ponsel anda.';
        } else {
            $this->db->trans_rollback();
            $result['status'] = false;
            $result['pesan'] = 'Kode gagal terkirim, silahkan ulangi lagi!';
        }

        echo json_encode($result);
    }

    public function register_old() {
        $result = array();

        $register = $_POST['register'];
        $email = $_POST['username'];
        $nama = $_POST['nama'];
        $id = md5($email);
        $id_member = '';

        $options = array('cost' => 12);
        $password = '-';

        $this->db->trans_begin();

        $get_email = null;
        if ($register == 'email') {
            $tlp = $email;
            $check_email = $this->m_crud->get_data("customer", "kd_cust id_member, verify, register", "register = 'email' AND email='" . $email . "'");
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);
        } else if ($register != '') {
            $id = $_POST['id'];
            $check_email = $this->m_crud->get_data("customer", "kd_cust id_member", "register='" . $register. "' AND id_register='".$id."'");
            $id_member = $check_email['id_member'];

            if($check_email==null){
                $get_email = $this->m_crud->get_data('customer', 'email', "register<>'" . $register. "' AND email = '".$email."'");
            }
        } else {
            $check_email = null;
        }

        $url = '-';

        if ($check_email==null && $get_email==null) {
            $result['status'] = true;

            $ol_code = $this->m_website->generate_kode("member", date('ym'));
            $data_member = array(
                'kd_cust'=>$ol_code,
                'email'=>$email,
                'email_notif'=>$email,
                'password'=>$password,
                'nama'=>$nama,
                'tlp1'=>$tlp,
                'status'=>'1',
                'foto'=>'assets/images/member/default.png',
                'tgl_register'=>date('Y-m-d H:i:s'),
                'verify'=>'0',
                'register'=>$register,
                'id_register'=>$id,
                'hash'=>password_hash($_POST['password'], PASSWORD_BCRYPT, $options),
                'ol_code' => $ol_code
            );

            $this->m_crud->create_data("customer", $data_member);
            $id_member = $this->db->insert_id();

            $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
            foreach ($read_lokasi as $item) {
                $log = array(
                    'type' => 'I', //I insert, U update, D delete
                    'table' => "customer",
                    'data' => $data_member,
                    'condition' => ""
                );

                $data_log = array(
                    'lokasi' => $item['Kode'],
                    'hostname' => $item['server'],
                    'db_name' => $item['db_name'],
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);
            }

            if(isset($_POST['one_signal_id'])){ $this->m_crud->update_data('customer', array('one_signal_id'=>$_POST['one_signal_id']), "kd_cust = '".$id_member."'"); }

            $url = base_url().'api/email_confirmation/'.base64_encode($id_member);
        } else {
            if ($register == 'email') {
                $result['status'] = false;
                if ($check_email['verify'] == '0' && $check_email['register'] == 'email') {
                    $url = base_url() . 'email_confirmation/' . base64_encode($check_email['id_member']);
                    $result['res_register'] = array('res' => 1, 'email' => $email, 'url' => $url, 'pesan' => 'Please verify your email address! Check inbox or spam!');
                } else if ($register != '') {
                    $result['res_register'] = array('res' => 0, 'email' => $email, 'url' => $url, 'pesan' => 'Email address already exist!');
                } else {
                    $result['res_register'] = array('res' => 0, 'email' => $email, 'url' => $url, 'pesan' => 'Registration failed!');
                }
            } else {
                if($get_email==null){
                    $result['status'] = true;
                    if(isset($_POST['one_signal_id'])){ $this->m_crud->update_data('customer', array('one_signal_id'=>$_POST['one_signal_id']), "kd_cust = '".$id_member."'"); }
                } else {
                    $result['status'] = false;
                    $result['res_register'] = array('res' => 0, 'email' => $email, 'url' => $url, 'pesan' => 'Silahkan Masuk menggunakan email!');
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            $result['status'] = false;
            $result['res_register'] = array('res'=>2, 'email'=>$email, 'url'=>$url, 'pesan'=>'Registration failed!');
        } else {
            //$this->db->trans_commit();
            if ($result['status'] == true) {
                if ($register != 'email') {
                    $this->db->trans_commit();
                    $result['res_register'] = array('id_member' => $id_member, 'pesan' => 'Successful!');
                } else {
                    if ($this->email_verification(base64_encode($email), base64_encode($url), 'register') == true) {
                        $this->db->trans_commit();
                        $result['res_register'] = array('send_email' => 'success', 'email' => $email, 'url' => $url, 'pesan' => 'Registration successful, please check your email address! Check inbox or spam!');
                    } else {
                        $result['status'] = false; $result['res_register'] = array('res'=>2, 'email'=>$email, 'url'=>$url, 'pesan'=>'Registration failed! Send mail failed');
                        //$result['res_register'] = array('send_email' => 'failed', 'email' => $email, 'url' => $url, 'pesan' => 'Registration successful, send email failed!');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function register_member() {
        $response = array();
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $tlp = $_POST['tlp'];
        $id = md5($email);
        $new_password = $this->random_char();
        $options = array('cost' => 12);
        $password = password_hash($new_password, PASSWORD_BCRYPT, $options);
        $ol_code = $this->m_website->generate_kode("member", date('ym'));

        $data_member = array(
            'email'=>$email,
            'email_notif'=>$email,
            'password'=>$password,
            'nama'=>$nama,
            'tlp1'=>$tlp,
            'status'=>'1',
            'foto'=>'assets/images/member/default.png',
            'tgl_register'=>date('Y-m-d H:i:s'),
            'verify'=>'1',
            'register'=>'email',
            'id_register'=>$id,
            'hash'=>password_hash($new_password, PASSWORD_BCRYPT, $options),
            'ol_code' => $ol_code
        );

        $check_email = $this->m_crud->get_data("customer", "kd_cust id_member", "id_register='".$id."'");
        if ($check_email == null) {
            $response['status'] = true;
            $response['data'] = $ol_code;
            $this->m_crud->create_data("customer", $data_member);
            $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
            foreach ($read_lokasi as $item) {
                $log = array(
                    'type' => 'I', //I insert, U update, D delete
                    'table' => "customer",
                    'data' => $data_member,
                    'condition' => ""
                );

                $data_log = array(
                    'lokasi' => $item['Kode'],
                    'hostname' => $item['server'],
                    'db_name' => $item['db_name'],
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);
            }
            $this->m_website->email_new_account(array('email'=>$email, 'password'=>$new_password));
        } else {
            $response['status'] = false;
            $response['pesan'] = 'Email sudah terdaftar';
        }

        echo json_encode($response);
    }

    public function email_verification($email, $url, $param='resend') {
        $email = base64_decode($email);
        $url = base64_decode($url);

        $to = strip_tags($email);
        $subject = "Email Verification";

        $pesan = '
            <p class="hero">Ini adalah email konfirmasi untuk aktivasi akun</p>
			<p>Silahkan klik tombol dibawah ini untuk aktivasi akun anda</p>
			<p>
			  <a href="'.$url.'" class="btn" mc:disable-tracking="">Aktivasi akun</a>
			</p>
			<p>Anda bisa melanjutkan masuk aplikasi setelah menekan tombol Aktivasi akun</p>
			<hr style="margin-top: 56px">
			<p class="mb-0">Terima kasih,</p>
			<p class="mb-0">'.$this->m_website->site_data()->nama.'</p>
        ';

        if ($this->m_website->email_to($to,$subject,$pesan) == true) {
            if ($param == 'resend') {
                echo json_encode(array('status'=>true));
            } else {
                return true;
            }
        } else {
            if ($param == 'resend') {
                echo json_encode(array('status'=>false));
            } else {
                return false;
            }
        }
    }

    public function email_confirmation($id_member) {
        $result = array();
        $id_member = base64_decode($id_member);

        $check_email = $this->m_crud->get_data("customer", "kd_cust", "verify='0' AND kd_cust='".$id_member."'");
        if ($check_email != null) {
            $this->db->trans_begin();

            $this->m_crud->update_data("customer", array('verify'=>'1'), "kd_cust='".$id_member."'");

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                $result['status'] = false;
                $this->load->view('site/email_failed');
            } else {
                $this->db->trans_commit();

                $result['status'] = true;
                $this->load->view('site/email_success');
            }
        } else {
            $result['status'] = false;
            $this->load->view('site/email_failed');
        }
    }

    /*Ubah Profile*/
    public function get_profile() {
        $result = array();
        $member = $_POST['id_member'];

        $get_data = $this->m_crud->get_data("customer", "email, alamat, biografi, email_notif, nama, jenis_kelamin, tgl_ultah tgl_lahir, tlp1 telepon, foto", "kd_cust='".$member."'");

        if ($get_data != null) {
            $result['status'] = true;
            $result['res_profile'] = array(
                'email' => $get_data['email'],
                'alamat' => $get_data['alamat'],
                'biografi' => $get_data['biografi'],
                'email_notif' => $get_data['email_notif'],
                'nama' => $get_data['nama'],
                'jenis_kelamin' => $get_data['jenis_kelamin'],
                'tgl_lahir' => $get_data['tgl_lahir'],
                'telepon' => $get_data['telepon'],
                'foto' => base_url().$get_data['foto']
            );
        } else {
            $result['status'] = false;
        }

        echo json_encode($result);
    }

    public function update_profile() {
        $result = array();

        $param = $_POST['param'];
        $member = $_POST['id_member'];
        $email_notif = $_POST['email_notif'];
        $nama = ucwords($_POST['nama']);
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $tgl_lahir = date('Y-m-d', strtotime($_POST['tgl_lahir']));
        $telepon = $_POST['telepon'];
        $alamat = isset($_POST['alamat'])?$_POST['alamat']:null;
        $biografi = isset($_POST['biografi'])?$_POST['biografi']:null;

        $row = 'foto';
        $config['upload_path']          = './assets/images/member';
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['max_size']             = 5120;
        $this->load->library('upload', $config);
        $valid = true;

        if( (!$this->upload->do_upload($row)) && $_FILES[$row]['name']!=null){
            $valid = false;
            $file[$row]['file_name']=null;
            $file[$row] = $this->upload->data();
            $data['error_'.$row] = $this->upload->display_errors();
        } else{
            $file[$row] = $this->upload->data();
            $data[$row] = $file;
            if($file[$row]['file_name']!=null){
                $manipulasi['image_library'] = 'gd2';
                $manipulasi['source_image'] = $file[$row]['full_path'];
                $manipulasi['maintain_ratio'] = true;
                $manipulasi['width']         = 100;
                //$manipulasi['height']       = 250;
                $manipulasi['new_image']       = $file[$row]['full_path'];
                $manipulasi['create_thumb']       = true;
                //$manipulasi['thumb_marker']       = '_thumb';
                $this->load->library('image_lib', $manipulasi);
                $this->image_lib->resize();
            }
        }

        $this->db->trans_begin();

        $status_tlp = true;
        if ($param == 'identitas') {
            $tlp_tersedia = $this->m_crud->get_data("customer", "kd_cust", "tlp1='" . $telepon . "' and kd_cust<>'" . $member . "'");
            if ($tlp_tersedia != null) {
                $status_tlp = false;
            }
        }

        if($valid==true && $status_tlp==true) {
            $data_member = array();
            $result['otp'] = false;
            if ($param == 'identitas') {
                $data_member = array(
                    'email_notif' => $email_notif,
                    'nama' => $nama,
                    'jenis_kelamin' => $jenis_kelamin,
                    'tgl_ultah' => $tgl_lahir,
                    'alamat' => $alamat,
                    'biografi' => $biografi
                );

                $cek_tlp = $this->m_crud->get_data("customer", "kd_cust", "kd_cust='".$member."' AND tlp1='".$telepon."'");

                if ($cek_tlp == null) {
                    $otentikasi = mt_rand(10000, 99999);;
                    $data_member['tlp1'] = $telepon;//DIUBAH
                    $data_member['hash'] = $otentikasi;
                    $data_member['tglakhir'] = $this->m_website->batas_waktu('+', date('Y-m-d H:i:s'));
                    $result['otp'] = true;
                }
            }

            if($_FILES[$row]['name']!=null){
                $data_member['foto'] = 'assets/images/member/'.$file[$row]['file_name'];
                $this->session->set_userdata($this->site . 'foto', base_url().'assets/images/member/'.$file[$row]['file_name']);
                $result['foto'] = base_url().'assets/images/member/'.$file[$row]['file_name'];
            }

            $this->m_crud->update_data("customer", $data_member, "kd_cust='".$member."'");
            $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO'");
            foreach ($read_lokasi as $item) {
                $log = array(
                    'type' => 'U', //I insert, U update, D delete
                    'table' => "customer",
                    'data' => $data_member,
                    'condition' => "kd_cust='".$member."'"
                );

                $data_log = array(
                    'lokasi' => $item['Kode'],
                    'hostname' => $item['server'],
                    'db_name' => $item['db_name'],
                    'query' => json_encode($log)
                );
                $this->m_website->insert_log_api($data_log);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['status'] = false;
            $result['pesan'] = 'Data gagal diubah';
        } else {
            $this->db->trans_commit();
            if ($status_tlp == false) {
                $result['status'] = false;
                $result['pesan'] = 'Nama pengguna sudah digunakan!';
            } else {
                $this->session->set_userdata($this->site . 'nama', $nama);
                $result['status'] = true;
                $result['pesan'] = 'Data berhasil diubah';
            }
        }

        echo json_encode($result);
    }
    /*End*/

    /*Ubah Password*/
    public function ubah_password() {
        $result = array();

        $member = $_POST['id_member'];
        $options = array('cost' => 12);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);

        $this->db->trans_begin();

        $data_member = array(
            'password' => $password
        );

        $this->m_crud->update_data("customer", $data_member, "kd_cust='".$member."'");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['status'] = false;
        } else {
            $this->db->trans_commit();
            $result['status'] = true;
        }

        echo json_encode($result);
    }
    /*End*/

    public function ganti_password() {
        $result = array();

        $member = $_POST['id_member'];
        $p_lama = $_POST['password_lama'];

        $get_data = $this->m_crud->get_data("customer", "password", "kd_cust = '".$member."'");

        if ($get_data != null) {
            if (password_verify($p_lama, $get_data['password'])) {
                $options = array('cost' => 12);
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);

                $this->db->trans_begin();

                $data_member = array(
                    'password' => $password
                );

                $this->m_crud->update_data("customer", $data_member, "kd_cust='".$member."'");

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = false;
                    $result['pesan'] = "Password gagal diubah";
                } else {
                    $this->db->trans_commit();
                    $result['status'] = true;
                    $result['pesan'] = "Password berhasil diubah";
                }
            } else {
                $result['status'] = false;
                $result['pesan'] = "Password lama salah";
            }
        } else {
            $result['status'] = false;
            $result['pesan'] = "Member tidak tersedia!";
        }

        echo json_encode($result);
    }

    public function cek_password() {
        $result = false;

        $member = $_POST['member'];
        $p_lama = $_POST['p_lama'];

        $get_data = $this->m_crud->get_data("customer", "password", "kd_cust = '".$member."'");

        if ($get_data != null) {
            if (password_verify($p_lama, $get_data['password'])) {
                $result = true;
            }
        }

        echo $result;
    }

    function random_char($length = 6) {
        $str = "";
        $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function forgot_password_old() {
        $result = array();
        $email = $_POST['email'];

        $get_data = $this->m_crud->get_data("customer", "kd_cust id_member", "email='".$email."'");

        $token = $this->random_char(20);

        if ($get_data != null) {
            $data_email = array(
                'email' => $email,
                'id_member' => $get_data['id_member'],
                'token' => $token
            );
            $this->m_crud->update_data("customer", array('token_resset_password'=>$token), "kd_cust='".$get_data['id_member']."'");
            $send = $this->m_website->email_forgot_password($data_email);

            if ($send) {
                $result['pesan'] = 'Silahkan cek inbox atau spam email anda untuk konfirmasi reset password';
            } else {
                $result['pesan'] = 'Email gagal terkirim silahkan masukan ulang email anda';
            }
            $result['status'] = $send;
        } else {
            $result['status'] = false;
            $result['pesan'] = 'Email tidak terdaftar';
        }

        echo json_encode($result);
    }

    public function resset_password_old($response=null) {
        $result = array(
            'redirect' => base_url()
        );

        if ($response != null) {
            $response = json_decode(base64_decode($response), true);
            $get_data = $this->m_crud->get_data("customer", "kd_cust id_member", "kd_cust='".$response['id_member']."' AND token_resset_password='" . $response['token'] . "'");

            if ($get_data != null) {
                $new_password = $this->random_char();
                $options = array('cost' => 12);
                $password = password_hash($new_password, PASSWORD_BCRYPT, $options);

                $this->m_crud->update_data("customer", array('password' => $password, 'token_resset_password'=>''), "kd_cust='" . $get_data['id_member'] . "'");
                $data_email = array(
                    'email' => $response['email'],
                    'password' => $new_password
                );
                $this->m_website->email_resset_password($data_email);

                $result['status'] = 'success';
                $result['pesan'] = 'Silahkan cek email anda untuk mendapatkan password baru';
            } else {
                $result['status'] = 'failed';
            }
        } else {
            $result['status'] = 'failed';
        }

        $this->load->view('site/resset_password', $result);
    }

    public function batas_waktu($tanggal=null){
        setlocale (LC_TIME, 'id_ID');
        return strftime('%A, %d %b %Y, %H:%M', strtotime($this->m_website->batas_waktu(null,$tanggal))).' WIB';
    }

    private function waktu_lalu($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'tahun',
            'm' => 'bulan',
            'w' => 'minggu',
            'd' => 'hari',
            'h' => 'jam',
            'i' => 'menit',
            's' => 'detik',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? (isset($string['m'])||isset($string['y'])?date('d M Y, H:i A', strtotime($datetime)):implode(', ', $string) . ' yang lalu') : 'baru saja';
    }

    public function time_ago($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'tahun',
            'm' => 'bulan',
            'w' => 'minggu',
            'd' => 'hari',
            'h' => 'jam',
            'i' => 'menit',
            's' => 'detik',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        echo $string ? (isset($string['m'])||isset($string['y'])?date('d M Y, H:i A', strtotime($datetime)):implode(', ', $string) . ' yang lalu') : 'baru saja';
    }

    public function set_wishlist() {
        $response = array('status' => true);
        $member = $_POST['id_member'];
        $kd_brg = $_POST['kd_brg'];

        $this->m_crud->create_data("wishlist",
            array(
                'id_wishlist' => $this->m_website->generate_kode('wishlist', date('ymd'), null),
                'kd_brg' => $kd_brg,
                'member' => $member,
                'tanggal' => date('Y-m-d H:i:s')
            )
        );

        echo json_encode($response);
    }

    public function remove_wishlist() {
        $response = array('status' => true);
        $member = $_POST['id_member'];
        $kd_brg = $_POST['kd_brg'];

        $this->m_crud->delete_data("wishlist", "member='".$member."' and kd_brg='".$kd_brg."'");

        echo json_encode($response);
    }

    public function get_wishlist($perpage=10, $page=1) {
        $response = array();

        $member = $_POST['id_member'];

        $start = ($page-1)*$perpage+1;
        $end = ($perpage*$page);

        $get_data = $this->m_crud->select_limit_join("barang_online bo", "bo.*", "wishlist ws", "ws.kd_brg=bo.id_barang", "member='".$member."'", "ws.tanggal asc", null, $start, $end);

        if ($get_data != null) {
            $response['status'] = true;
            $response['data'] = $this->m_website->tambah_data("barang_online", $get_data, $member);
        } else {
            $response['status'] = false;
        }

        echo json_encode($response);
    }

    public function get_data($table, $action=null, $id=1, $perpage=10){
        $member = $_POST['id_member'];

        if (substr($action, 0, 5) == 'where') {
            $split = explode('|', $action);
            $post_where = $split[1];
        }

        if(substr($table,0,6)=='return'){
            $table = str_replace('return_','',$table);
            $action = 'return';
        }

        if($table=='kel_brg'){
            $table = "kel_brg_online";
            $where="status = '1'";
        } else if ($table=='barang') {
            $table = 'barang_online';
            $where=null;
            if (isset($_POST['search'])) {
                $where .= " AND nama like '%".$_POST['search']."%'";
            }
        }

        if(isset($_POST['where']) && $_POST['where']!=null){ $post_where=$_POST['where']; }

        if(isset($post_where) && $post_where!=null){ ($where==null)?null:$where.=" and "; $where.="(".$post_where.")"; }

        $page=$id;
        $config['per_page'] = $perpage;
        /*
        $config['base_url'] = base_url().strtolower($this->control).'/'.$function.'/'.($action!=null?$action:'-').'/';
        $config['total_rows'] = $this->m_crud->count_data($table.', Group1 as g1, Group2 as g2, kel_brg as kb', 'kd_brg', $where);
        //$config['attributes'] = array('class' => ''); //attributes anchors
        $config['first_url'] = $config['base_url'];
        $config['num_links'] = 5;
        $config['use_page_numbers'] = TRUE;
        //$config['display_pages'] = FALSE;
        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['first_tag_open'] = '<li>'; $config['first_link'] = '&laquo;'; $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>'; $config['prev_link'] = '&lt;'; $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#" />'; $config['cur_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>'; $config['num_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>'; $config['next_link'] = '&gt;';  $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>'; $config['last_link'] = '&raquo;'; $config['last_tag_close'] = '</li>';
        $config['full_tag_close'] = '</ul>';
        $this->pagination->initialize($config);
        */

        $start = ($page-1)*$config['per_page']+1;
        $end = ($config['per_page']*$page);

        $select="*";
        $order = array(
            'kel_brg'=>'nm_kel_brg',
            'kel_brg_online'=>'nama',
            'barang_online' => 'nama asc',
            'kecamatan_rajaongkir' => 'kecamatan asc',
            'kota_rajaongkir' => 'kota asc',
            'provinsi' => 'provinsi asc',
            'bank' => 'nama asc',
            'customer' => 'kd_cust asc',
            'berita' => 'tanggal desc',
            'intro' => 'id_intro asc',
            'kartu_poin' => 'tanggal desc',
            'kartu_deposit' => 'tgl desc',
            'rekening' => 'bank asc'
        );

        $get_data = $this->m_crud->select_limit($table, $select, $where, (isset($order[$table])?$order[$table]:null), null, $start, $end);

        if($action=='return'){
            if($get_data!=null){
                $get_data = $this->m_website->tambah_data($table, $get_data, $member);
                return json_encode(array('status'=>true, 'data'=>$get_data));
            } else {
                return json_encode(array('status'=>false));
            }
        } else {
            if($get_data!=null){
                $get_data = $this->m_website->tambah_data($table, $get_data, $member);
                $data_return = array('status'=>true, 'data'=>$get_data);
                echo json_encode($data_return);
            } else {
                echo json_encode(array('status'=>false));
            }
        }
    }

    public function tambah_data($table, $get_data){
        if($table=='site'){
            for($i=0; $i<count($get_data); $i++){
                if($get_data[$i]['logo']!=null || $get_data[$i]['logo']!=''){
                    $get_data[$i]['logo'] = base_url().$get_data[$i]['logo'];
                }
                if($get_data[$i]['icon']!=null || $get_data[$i]['icon']!=''){
                    $get_data[$i]['icon'] = base_url().$get_data[$i]['icon'];
                }
                if($get_data[$i]['splash']!=null || $get_data[$i]['splash']!=''){
                    $get_data[$i]['splash'] = base_url().$get_data[$i]['splash'];
                }
            }
        } else if($table=='berita' || $table=='slide' || $table=='galeri_album' || $table=='galeri' || $table=='bank'){
            for($i=0; $i<count($get_data); $i++){
                if($table=='berita'){
                    $deskripsi = str_replace('&nbsp;', ' ', strip_tags($get_data[$i]['deskripsi']));
                    $get_data[$i]['ringkasan'] = (strlen($deskripsi)>250?substr($deskripsi, 0, 250).'...':$deskripsi);
                    $get_data[$i]['tanggal_asli'] = $get_data[$i]['tanggal'];
                    $get_data[$i]['tanggal'] = $this->waktu_lalu($get_data[$i]['tanggal']);
                }

                if($get_data[$i]['foto']!=null || $get_data[$i]['foto']!=''){
                    $get_data[$i]['foto'] = base_url().$get_data[$i]['foto'];
                    $get_data[$i]['foto_thumb'] = $this->m_website->file_thumb($get_data[$i]['foto']);
                } else {
                    $get_data[$i]['foto'] = $this->m_website->no_img();
                    $get_data[$i]['foto_thumb'] = $this->m_website->no_img();
                }
            }
        } else if($table=='wallpaper'){
            for($i=0; $i<count($get_data); $i++){
                if($get_data[$i]['gambar']!=null || $get_data[$i]['gambar']!=''){
                    $get_data[$i]['foto'] = base_url().$get_data[$i]['gambar'];
                    $get_data[$i]['foto_thumb'] = $this->m_website->file_thumb($get_data[$i]['foto']);
                } else {
                    $get_data[$i]['foto'] = $this->m_website->user_img_src();
                    $get_data[$i]['foto_thumb'] = $this->m_website->user_img_src();
                }
            }
        } else if ($table=='about') {
            for($i=0; $i<count($get_data); $i++){
                $deskripsi = json_decode($get_data[$i]['deskripsi'], true);
                $deskripsi['foto'] = base_url().$deskripsi['foto'];
                $get_data[$i]['deskripsi'] = $deskripsi;
            }
        } else if ($table=='iklan') {
            for($i=0; $i<count($get_data); $i++){
                $get_data[$i]['foto'] = base_url().$get_data[$i]['gambar'];
                $get_data[$i]['foto_thumb'] = $this->m_website->file_thumb($get_data[$i]['foto']);
            }
        }
        return $get_data;
    }

    public function get_bank() {
        $response = array();
        $get_data = $this->m_crud->join_data("bank", "bank.nama id_bank, bank.nama, bank.foto, rekening.atas_nama, rekening.norek no_rek", "rekening", "bank.nama=rekening.bank", "bank.status='1'");

        if ($get_data == null) {
            $response['status'] = false;
        } else {
            $response['status'] = true;
            $get_data = $this->m_website->tambah_data('bank', $get_data);
        }

        $response['data'] = $get_data;

        echo json_encode($response);
    }

    public function get_bank_pengirim() {
        $response = array();
        $get_data = $this->m_crud->read_data("bank", "nama id_bank, nama, foto");

        if ($get_data == null) {
            $response['status'] = false;
        } else {
            $response['status'] = true;
            $get_data = $this->m_website->tambah_data('bank', $get_data);
        }

        $response['data'] = $get_data;

        echo json_encode($response);
    }

    public function upload_bukti_transfer() {
        $row = 'bukti_transfer';
        $path = 'assets/images/bukti_transfer/';
        $config['upload_path']          = './'.$path;
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['max_size']             = 5120;
        $this->load->library('upload', $config);
        $valid = true;

        if( (!$this->upload->do_upload($row)) && $_FILES[$row]['name']!=null){
            $valid = false;
            $file[$row]['file_name']=null;
            $file[$row] = $this->upload->data();
            $data['error_'.$row] = $this->upload->display_errors();
        } else{
            $file[$row] = $this->upload->data();
            $data[$row] = $file;
        }

        $result = array();
        if($_FILES[$row]['name']!=null){
            $table = "bayar_donasi";
            $column = "id_bayar_donasi";
            $jenis = array(
                'KF' => 'Kafalah',
                'WF' => 'Wakaf',
                'DN' => 'Donasi',
                'ZT' => 'ZIS',
                'QB' => 'Qurban'
            );
            if (substr($_POST['id'], 0, 2) == 'KF') {
                $table = "bayar_kafalah";
                $column = "id_bayar_kafalah";
                $get_data_bayar = $this->m_crud->get_join_data("bayar_kafalah bk", "bk.program_kafalah, bk.member, bk.tanggal, SUM(bka.poin) poin, SUM(bka.jumlah_bulan*bka.nominal)+bk.kodeunik total_transfer", "bayar_kafalah_anggota bka", "bka.bayar_kafalah=bk.id_bayar_kafalah", "bk.id_bayar_kafalah='".$_POST['id']."'");
                $data_email = array(
                    'kode_trx' => $_POST['id'],
                    'donasi' => $jenis[substr($_POST['id'],0,2)],
                    'images' => base_url().$path.$file[$row]['file_name'],
                    'judul' => 'Program Kafalah '.$get_data_bayar['program_kafalah'],
                    'total_transfer' => number_format($get_data_bayar['total_transfer'])
                );
            } else {
                $get_data_bayar = $this->m_crud->get_data("bayar_donasi", "data_donasi, member, tanggal, poin, (nominal+kodeunik) total_transfer", "id_bayar_donasi='".$_POST['id']."'");
                $get_donasi = $this->m_crud->get_data('data_donasi', 'judul', "id_data_donasi = '".$get_data_bayar['data_donasi']."'");

                $data_email = array(
                    'kode_trx' => $_POST['id'],
                    'donasi' => $jenis[substr($_POST['id'],0,2)],
                    'images' => base_url().$path.$file[$row]['file_name'],
                    'judul' => $get_donasi['judul'],
                    'total_transfer' => number_format($get_data_bayar['total_transfer'])
                );
            }
            $this->m_crud->update_data($table, array('bukti_transfer'=>$path.$file[$row]['file_name']), $column."='".$_POST['id']."'");

            /*Email konfirmasi*/
            $this->m_website->email_konfirmasi_donasi($data_email);
            $result['status'] = true;
        } else {
            $result['status'] = false;
        }

        echo json_encode($result);
    }

    public function get_intro(){
        $get_intro = $this->m_crud->read_data("intro", "*");
        $intro = array();
        foreach ($get_intro as $row) {
            if ($row['tipe'] == 'foto') {
                $background = base_url().$row['background'];
            } else {
                $background = $row['background'];
            }
            array_push($intro, array(
                'backgroud' => $background,
                'judul' => $row['judul'],
                'keterangan' => $row['keterangan']
            ));
        }
        echo json_encode($intro);
    }

    public function get_lokasi($page=1, $return=false) {
        $result = array();

        $start = ($page - 1) * 5;
        $get_lokasi = $this->m_crud->read_data("lokasi", "kode, nama_toko nama, isnull(('".base_url()."'+gambar),'') gambar, phone tlp, (cast(CONVERT(TIME(0), jam_buka) as varchar(50))+' - '+cast(CONVERT(TIME(0), jam_tutup) as varchar(50))) operation, lat, lng long, ket, kota", "status_show='1'");
        foreach ($get_lokasi as $key => $row) {
            $get_fasilitas = $this->m_crud->join_data("fasilitas_lokasi fl", "fl.id_fasilitas_lokasi, fl.gambar, f.nama, f.gambar gambar_fasilitas", "fasilitas f", "f.id_fasilitas=fl.fasilitas", "fl.lokasi='".$row['kode']."'");
            if ($get_fasilitas != null) {
                foreach ($get_fasilitas as $id => $item) {
                    $get_fasilitas[$id]['gambar_fasilitas'] = $this->m_website->file_thumb(base_url().$item['gambar_fasilitas']);
                    $list_gambar = array();
                    $gambar = json_decode($item['gambar'], true);
                    if (is_array($gambar) && count($gambar) > 0) {
                        foreach ($gambar as $d_gambar) {
                            array_push($list_gambar, array('gambar'=>$this->m_website->file_thumb(base_url().$d_gambar)));
                        }
                        $get_fasilitas[$id]['gambar'] = $list_gambar;
                    }
                }
            } else {
                $get_fasilitas = false;
            }
            $get_lokasi[$key]['fasilitas'] = $get_fasilitas;
        }

        if ($get_lokasi) {
            $result['res_lokasi'] = $get_lokasi;
            $result['status'] = true;
        } else {
            $result['status'] = false;
        }

        if ($return) {
            return $result;
        } else {
            echo json_encode($result);
        }
    }

    public function get_poin() {
        $result = array();

        $member = $_POST['id_member'];//'377';//$_POST['member'];

        $result['status'] = true;
        $result['res_poin'] = $this->m_crud->get_data("kartu_poin", "ISNULL(SUM(poin_masuk-poin_keluar), 0) poin", "kd_cust='".$member."'")['poin']+0;
        $result['res_deposit'] = $this->m_crud->get_data("kartu_deposit", "ISNULL(SUM(saldo_masuk-saldo_keluar), 0) deposit", "member='".$member."'")['deposit']+0;

        echo json_encode($result);
    }

    public function get_member_poin($param=null) {
        $response = array();
        $kode = $_POST['ol_code'];
        $get_poin = $this->m_crud->get_join_data("customer m", "m.ol_code, m.nama, ISNULL(SUM(isnull(p.poin_in,0)-isnull(p.poin_out,0)), 0) poin", array(array("table"=>"poin p", "type"=>"LEFT")), array("p.customer=m.kd_cust"), "m.ol_code='".$kode."'", null, "m.ol_code, m.nama,");

        if ($get_poin != null) {
            $response['status'] = true;
            $response['data'] = $get_poin;
        } else {
            $response['status'] = false;
            $response['pesan'] = "Member tidak tersedia";
        }

        if ($param == 'return') {
            return json_encode($response);
        } else {
            echo json_encode($response);
        }
    }

    public function insert_poin() {
        $min_order = 100000;
        $id_orders = $_POST['kd_trx'];
        $tanggal = date('Y-m-d H:i:s', strtotime($_POST['tanggal']));
        $get_total = (float)$_POST['total'];
        $kode_online = $_POST['ol_code'];

        $member = $this->m_crud->get_data("customer", "kd_cust id_member", "ol_code='".$kode_online."'");

        $check_data = $this->m_crud->get_data("poin", "kode_trx", "kode_trx='".$id_orders."'");

        if ($check_data==null && $get_total>=$min_order && $member!=null) {
            $this->m_crud->create_data("poin", array('kode_trx'=>$id_orders, 'tanggal'=>$tanggal, 'customer'=>$member['id_member'], 'poin_in'=>floor($get_total/$min_order), 'keterangan'=>'Penjualan NPOS'));
            echo json_encode(array('status'=>true));
        } else {
            echo json_encode(array('status'=>false));
        }
    }

    public function tukar_poin() {
        $response = array();
        $ol_code = $_POST['ol_code'];
        $tanggal = date('Y-m-d H:i:s', strtotime($_POST['tanggal']));
        $tukar_poin = abs($_POST['poin']);
        $lokasi = $_POST['lokasi'];
        $ket = 'Penukaran Poin : ' . $_POST['ket'];
        $kd_trx = $_POST['kd_trx'];//$this->m_website->generate_kode("poin", $lokasi, date('ymd'));

        $member = $this->m_crud->get_data("customer", "kd_cust id_member", "ol_code='".$ol_code."'");
        if ($member != null) {
            $get_poin = json_decode($this->get_member_poin('return'), true)['data'];
            $poin = $get_poin['poin'];
            if ($_POST['poin'] > $poin) {
                $response['status'] = false;
                $response['pesan'] = "Poin anda tidak cukup untuk ditukarkan";
            } else {
                $this->m_crud->create_data("poin", array('kode_trx'=>$kd_trx, 'tanggal'=>$tanggal, 'customer'=>$member['id_member'], 'poin_out'=>$tukar_poin, 'keterangan'=>$ket));
                $response['status'] = true;
                $response['pesan'] = "Poin berhasil ditukarkan";
            }
        } else {
            $response['status'] = false;
            $response['pesan'] = "Poin gagal ditukarkan. Member tidak valid";
        }

        echo json_encode($response);
    }

    public function to_cart($param=null) {
        $result = array();
        $this->db->trans_begin();

        //$this->reset_pembayaran();
        $member = $param['member'];
        $det_produk = $param['det_produk'];
        $catatan= $param['catatan'];
        $berat = (float)$param['berat'];
        $jumlah = (int)$param['jumlah'];
        $hrg_beli = (float)$param['hrg_beli'];
        $hrg_jual = (float)$param['hrg_jual'];
        $hrg_coret = (float)$param['hrg_coret'];
        $hrg_varian = (float)$param['hrg_varian'];
        $tgl = date('Y-m-d H:i:s');
        $code = 'CART/'.$member;

        if ($hrg_coret==0) {
            $diskon = 0;
        } else {
            $diskon = ($hrg_coret+$hrg_varian)-$hrg_jual;
        }

        $get_cart = $this->m_crud->get_data("orders", "id_orders", "id_orders='".$code."' AND status='0'");
        if ($get_cart == null) {
            $data_order = array(
                'id_orders' => $code,
                'tgl_orders' => $tgl,
                'tipe' => '1',
                'member' => $member,
                'status' => '0'
            );
            $this->m_crud->create_data("orders", $data_order);

            $det_order = array(
                'orders' => $code,
                'det_produk' => $det_produk,
                'qty' => $jumlah,
                'berat' => $berat,
                'hrg_beli' => $hrg_beli,
                'hrg_jual' => $hrg_jual+$diskon-$hrg_varian,
                'hrg_varian' => $hrg_varian,
                'diskon' => $diskon,
                'charge' => '0',
                'catatan' => $catatan
            );
            $this->m_crud->create_data("det_orders", $det_order);
        } else {
            $get_produk = $this->m_crud->get_data("det_orders", "qty", "orders='".$code."' AND det_produk='".$det_produk."'");
            if ($get_produk == null) {
                $det_order = array(
                    'orders' => $code,
                    'det_produk' => $det_produk,
                    'qty' => $jumlah,
                    'berat' => $berat,
                    'hrg_beli' => $hrg_beli,
                    'hrg_jual' => $hrg_jual+$diskon-$hrg_varian,
                    'hrg_varian' => $hrg_varian,
                    'diskon' => $diskon,
                    'charge' => '0',
                    'catatan' => $catatan
                );
                $this->m_crud->create_data("det_orders", $det_order);
            } else {
                $this->m_crud->update_data("det_orders", array('qty'=>$jumlah), "orders='".$code."' AND det_produk='".$det_produk."'");
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = false;
        } else {
            $this->db->trans_commit();
            $status = true;
            $result['count'] = $this->m_crud->count_data_join("orders o", "o.id_orders", "det_orders do", "do.orders=o.id_orders", "o.status='0' AND o.member='".$this->user."'");
        }

        return $status;
    }

    public function reset_pembayaran($member) {
        $cek_data = $this->m_crud->get_data("pembayaran", "id_pembayaran", "member='".$member."' AND status = '0'");

        if ($cek_data != null) {
            $this->m_crud->delete_data("pembayaran", "id_pembayaran='".$cek_data['id_pembayaran']."'");
        }
    }

    public function hasbi(){
        echo json_encode(array(
            'checkout'=>array(
                'member'=>'M180800002', 'kd_alamat_jual'=>'baru', 'kota'=>'11', 'nama_penerima'=>'hasbi', 'alamat'=>'bandung', 'kd_prov'=>'1', 'kd_kota'=>'11', 'kd_kec'=>'111',
                'telepon'=>'085', 'kurir'=>'jne', 'data_layanan'=>'oke', 'ongkir'=>17000, 'bank_tujuan'=>'1', 'nama_bank_tujuan'=>'bca', 'nomor_rekening_tujuan'=>'0011111',
                'atas_nama_tujuan'=>'ade hasbi asidik', 'bank_pengirim'=>'2', 'nama_bank_pengirim'=>'bri', 'nomor_rekening_pengirim'=>'0022222', 'atas_nama_pengirim'=>'hasbi asidik',
            ),
            'to_cart'=> array(
                array('member'=>'M180800002', 'catatan'=>'-', 'berat'=>0.5, 'hrg_beli'=>500, 'hrg_coret'=>1500, 'hrg_jual'=>1000, 'hrg_varian'=>1200, 'jumlah'=>2, 'det_produk'=>'00010001'),
                array('member'=>'M180800002', 'catatan'=>'-', 'berat'=>1.3, 'hrg_beli'=>1000, 'hrg_coret'=>2500, 'hrg_jual'=>2000, 'hrg_varian'=>2200, 'jumlah'=>3, 'det_produk'=>'00020001')
            )
        ));
    }

    public function checkout_bayar() {
        $result = array();

        /*
        $_POST['kota'] = ;
        $_POST['nama_alamat'] = ;
        $_POST['checkout'] = array(
            'checkout'=>array(
                'member'=>'', 'kd_alamat_jual'=>'baru', 'kota'=>'', 'nama_penerima'=>'', 'alamat'=>'', 'kd_prov'=>'', 'kd_kota'=>'', 'telepon'=>'',
                'kurir'=>'', 'data_layanan'=>'', 'ongkir'=>'', 'bank_tujuan'=>'', 'nama_bank_tujuan'=>'', 'nomor_rekening_tujuan'=>'',
                'atas_nama_tujuan'=>'', 'bank_pengirim'=>'', 'nama_bank_pengirim'=>'', 'nomor_rekening_pengirim'=>'', 'atas_nama_pengirim'=>'',
            ),
            'to_cart'=> array('member'=>'', 'catatan'=>'', 'berat'=>'', 'hrg_beli'=>'', 'hrg_coret'=>'', 'hrg_jual'=>1000, 'hrg_varian'=>1200, 'jumlah'=>3, 'det_produk'=>'')
        );
        */

        $data_post = json_decode($_POST['checkout'], true);
        $to_cart = $data_post['to_cart'];

        $list = '';
        $sub_total = 0;
        foreach ($to_cart as $item) {
            $hitung_jumlah = ((float)$item['hrg_jual']+(float)$item['hrg_varian'])*(int)$item['jumlah'];
            $sub_total = $sub_total + $hitung_jumlah;
            $this->to_cart($item);
            $get_produk = $this->m_crud->get_data("barang b", "b.nm_brg nama", "b.kd_brg='".$item['det_produk']."'");
            $list .= '
                <tr class="item">
                    <td>'.$get_produk['nama'].' x'.(int)$item['jumlah'].'</td>
                    <td>'.number_format($hitung_jumlah).'</td>
                </tr>
	        ';
        }

        $checkout = $data_post['checkout'];

        $member = $checkout['member'];
        $tipe_alamat = $checkout['kd_alamat_jual'];
        $nama_penerima = $checkout['nama_penerima'];
        $alamat = $checkout['alamat'];
        $kd_prov = $checkout['kd_prov'];
        $prov = $this->m_crud->get_data("provinsi_rajaongkir", "provinsi", "provinsi_id='".$kd_prov."'")['provinsi'];
        $kd_kota = $checkout['kd_kota'];
        $kota = $this->m_crud->get_data("kota_rajaongkir", "kota", "kota_id='".$kd_kota."'")['kota'];
        $kd_kec = $checkout['kd_kec'];
        $kecamatan = $this->m_crud->get_data("kecamatan_rajaongkir", "kecamatan", "kecamatan_id='".$kd_kec."'")['kecamatan'];
        $tlp_penerima = $checkout['telepon'];
        $kurir = strtoupper($checkout['kurir']);
        $layanan_kurir = strtoupper($checkout['data_layanan']);
        $ongkir = str_replace(',', '', $checkout['ongkir']);
        $jumlah = (float)$sub_total+(float)$ongkir;
        $bank2 = $checkout['bank_tujuan'];
        $bank = $checkout['nama_bank_tujuan'];
        $rekening = $checkout['nomor_rekening_tujuan'];
        $pemilik = $checkout['atas_nama_tujuan'];
        $bank1 = $checkout['bank_pengirim'];
        $bank_pengirim = $checkout['nama_bank_pengirim'];
        $rekening_pengirim = $checkout['nomor_rekening_pengirim'];
        $pemilik_pengirim = $checkout['atas_nama_pengirim'];
        $tanggal = date('Y-m-d H:i:s');

        $kode_unik = $this->m_website->get_kodeunik($jumlah, $rekening);
        /*$kode_unik = 11;
        $param = true;
        while ($param) {
            $kode_unik = mt_rand( 11, 999 );
            $cek_kode_unik = $this->m_crud->get_data("pembayaran", "id_pembayaran", "jumlah=".$jumlah." AND kode_unik=".$kode_unik." AND status IN ('0', '1')");
            if ($cek_kode_unik == null) {
                $param = false;
            } else {
                $param = true;
            }
        }*/

        $this->db->trans_begin();

        if ($tipe_alamat == 'baru') {
            $kota = $_POST['kota'];
            $nama_alamat = $_POST['nama_alamat'];

            $data_lokasi = array(
                'nama' => $nama_alamat,
                'alamat' => $alamat,
                'member' => $member,
                'penerima' => $nama_penerima,
                'telepon' => $tlp_penerima,
                'kota' => $kd_kota,
                'provinsi' => $kd_prov,
                'kecamatan' => $kd_kec,
                'status' => '1'
            );

            $this->m_crud->create_data("alamat_member", $data_lokasi);
        }

        $this->reset_pembayaran($member);
        $code_pembayaran = 'TF/'.$this->m_website->date_romawi().'/'.$member;
        $data_pembayaran = array(
            'id_pembayaran' => $code_pembayaran,
            'member' => $member,
            'tgl_bayar' => $tanggal,
            'bank2' => $bank2,
            'bank_tujuan' => $bank,
            'no_rek_tujuan' => $rekening,
            'atas_nama_tujuan' => $pemilik,
            'jumlah' => $jumlah,
            'kode_unik' => $kode_unik,
            'bank1' => $bank1,
            'bank' => $bank_pengirim,
            'no_rek' => $rekening_pengirim,
            'atas_nama' => $pemilik_pengirim,
            'status' => '1'
        );
        $this->m_crud->create_data("pembayaran", $data_pembayaran);

        $get_orders = $this->m_crud->read_data("orders", "id_orders", "member='".$member."' AND status = '0'");
        foreach ($get_orders as $row) {
            $romawi = $this->m_website->date_romawi('time');
            $tanggal = $tanggal;
            $max_order = $this->m_crud->get_data("orders", "MAX(RIGHT(id_orders, 3)) max_data", "isnumeric(RIGHT(id_orders, 3))=1 AND tgl_orders='".$tanggal."'")['max_data'];
            $code = '/'.$romawi.'/'.sprintf('%03d', (int)$max_order+1);
            $this->m_crud->update_data("orders", array('id_orders'=>'TR'.$code, 'tgl_orders'=>$tanggal, 'status'=>'1'), "id_orders='".$row['id_orders']."'");
            $this->m_crud->update_data("det_orders", array('orders'=>'TR'.$code), "orders='".$row['id_orders']."'");
            $this->m_crud->create_data("det_pembayaran", array('pembayaran'=> $code_pembayaran, 'orders'=>'TR'.$code));
        }

        $data_pengiriman = array(
            'id_pengiriman' => 'DO'.$code,
            'orders' => 'TR'.$code,
            'penerima' => $nama_penerima,
            'alamat' => $alamat,
            'id_provinsi' => $kd_prov,
            'provinsi' => $prov,
            'id_kota' => $kd_kota,
            'kota' => $kota,
            'id_kecamatan' => $kd_kec,
            'kecamatan' => $kecamatan,
            'telepon' => $tlp_penerima,
            'kurir' => $kurir,
            'service' => $layanan_kurir,
            'biaya' => $ongkir
        );
        $this->m_crud->create_data("pengiriman", $data_pengiriman);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['status'] = false;
            $result['judul'] = "Checkout Gagal.";
            $result['deskripsi'] = "Silahkan ulangi transaksi anda.";
        } else {
            $this->db->trans_commit();

            $result['status'] = true;
            $result['judul'] = "Checkout Berhasil.";
            $result['deskripsi'] = "Silahkan lakukan transfer ke rekening yang tertera di atas, dan segera lakukan konfirmasi jika telah melakukan transfer.";
            $result['code'] = $code_pembayaran;
            $result['bank'] = $bank;
            $result['norek'] = $rekening;
            $result['atasnama'] = $pemilik;
            $result['total'] = (float)$jumlah+(float)$kode_unik;

            $data = array(
                'id_orders' => 'TR'.$code,
                'tanggal' => $tanggal,
                'penerima' => $nama_penerima,
                'tlp' => $tlp_penerima,
                'total' => (float)$jumlah+(float)$kode_unik,
                'bank' => $bank,
                'no_rek' => $rekening,
                'an_rek' => $pemilik,
                'kurir' => $kurir.' ~ '.$layanan_kurir,
                'ongkir' => $ongkir,
                'kode_unik' => $kode_unik,
                'list' => $list
            );

            $get_email = $this->m_crud->get_data("customer", "email", "kd_cust='".$member."'");
            //$this->m_website->email_invoice($get_email['email'], json_encode($data));
            //$this->m_website->email_invoice('npos.cs@gmail.com', json_encode($data));
            //$this->m_website->email_invoice('evieeffendieappsofficial@gmail.com', json_encode($data));
            //$this->m_website->email_invoice($this->m_website->site_data()->email, json_encode($data));
            $get_onsignal = $this->m_crud->get_data("customer", "one_signal_id", "kd_cust='".$member."'");

            $data_notif = array(
                'member'=>$get_onsignal['one_signal_id'],
                /*'segment'=>'All',*/
                'data' => array("param" => "checkout", "kd_trx" => $code_pembayaran),
                'head'=>'Checkout Berhasil',
                'content'=>'Silahkan lakukan transfer sebesar Rp '.number_format((float)$jumlah+(float)$kode_unik, 0, ',', '.').' ke rekening '.$bank.' ('.$rekening.') atas nama '.$pemilik
            );

            $this->m_website->create_notif($data_notif);

        }

        echo json_encode($result);
    }

    public function get_rekening() {
        $result = array();
        $member = $_POST['member'];
        $bank = $_POST['bank'];

        $get_rekening = $this->m_crud->get_data("pembayaran", "bank, no_rek, atas_nama", "member='".$member."' AND bank1='".$bank."'");

        if ($get_rekening == null) {
            $result['status'] = false;
            $result['res_rekening'] = $this->m_crud->get_data("bank", "nama", "nama='".$bank."'");
        } else {
            $result['status'] = true;
            $result['res_rekening'] = $get_rekening;
        }

        echo json_encode($result);
    }

    public function get_ongkir() {
        $data = array(
            'tujuan' => $_POST['kecamatan'],
            'berat' => (int)$_POST['berat'],
            'kurir' => $_POST['kurir']
        );
        $req_api = $this->m_website->rajaongkir_cost(json_encode($data));
        $decode = json_decode($req_api, true);

        echo json_encode($decode['rajaongkir']['results'][0]);
    }

    public function get_alamat() {
        $member = $_POST['member'];
        $read_data = $this->m_crud->join_data("alamat_member am", "am.id_alamat_member, am.nama nama_alamat, am.alamat, am.provinsi, am.kota, am.penerima, am.telepon, am.kecamatan, kcr.kecamatan nama_kecamatan, pr.provinsi nama_provinsi, kr.tipe, kr.kota nama_kota", array("provinsi_rajaongkir pr", "kota_rajaongkir kr", "kecamatan_rajaongkir kcr"), array("am.provinsi=pr.provinsi_id", "am.kota=kr.kota_id", "am.kecamatan=kcr.kecamatan_id"), "am.member='".$member."'");

        $status = true;
        if ($read_data == null) {
            $status = false;
        }

        $res = array(
            'status' => $status,
            'data' => $read_data
        );

        echo json_encode($res);
    }

    public function save_alamat() {
        $result = array();

        $data_lokasi = array(
            'nama' => $_POST['nama_lokasi'],
            'alamat' => $_POST['alamat'],
            'member' => $_POST['member'],
            'penerima' => $_POST['penerima'],
            'telepon' => $_POST['telepon'],
            'kota' => $_POST['id_kota'],
            'kecamatan' => $_POST['id_kecamatan'],
            'provinsi' => $_POST['id_provinsi'],
            'status' => '1'
        );

        $this->m_crud->create_data("alamat_member", $data_lokasi);
        $id = $this->db->insert_id();
        $result['status'] = true;
        $result['id_lokasi'] = $id;

        echo json_encode($result);
    }

    public function get_transfer() {
        $result = array();
        $id_pembayaran = $_POST['id_pembayaran'];

        $get_bukti = $this->m_crud->get_data("pembayaran", "atas_nama, bank, no_rek, (jumlah + kode_unik) total, CONCAT('".base_url()."', bukti_transfer) gambar", "id_pembayaran='".$id_pembayaran."'");

        if ($get_bukti != null) {
            $result['status'] = true;
            $result['res_transfer'] = $get_bukti;
        } else {
            $result['status'] = false;
        }

        echo json_encode($result);
    }

    public function get_kurir() {
        $get_data = $this->m_crud->read_data("kurir", "id_kurir, kurir", "status='1'");
        $status = true;
        if ($get_data == null) {
            $status = false;
        }

        echo json_encode(array('status' => $status, 'data'=>$get_data));
    }

    public function get_history_pembelian($action=5, $id=1) {
        $member = $_POST['id_member'];

        $page=$id;
        $config['per_page'] = $action;

        $start = ($page-1)*$config["per_page"]+1;
        $end = ($config["per_page"]*$page);

        $get_data = $this->m_crud->select_limit_join("orders o", "o.id_orders, o.tgl_orders, o.status, SUM(do.qty * (do.hrg_jual+do.hrg_varian-do.diskon)) total, dp.pembayaran, pb.status status_bayar, pb.kode_unik, pn.id_pengiriman", array("det_orders do", "det_pembayaran dp", "pembayaran pb", "pengiriman pn"), array("do.orders=o.id_orders", "dp.orders=o.id_orders", "pb.id_pembayaran=dp.pembayaran", "pn.orders=o.id_orders"), "o.member='".$member."' AND o.status <> '0'", "o.tgl_orders DESC", "o.id_orders, o.tgl_orders, o.status, dp.pembayaran, pb.status, pb.kode_unik, pn.id_pengiriman", $start, $end);

        if ($get_data != null) {
            $status = true;
            foreach ($get_data as $key => $row) {
                $action = array(array('api'=>'get_detail_pembelian', 'text'=>'Detail Pembelian'));
                if ($row['status'] == '1') {
                    if ($row['status_bayar'] == '1') {
                        $status_trx = array('hex'=>'#80FF9800', 'text'=>'Waiting Payment');
                        array_push($action, array('api'=>'konfirmasi_pembayaran', 'text'=>'Konfirmasi Pembayaran'));
                        array_push($action, array('api'=>'cancel_order', 'text'=>'Batalkan Pembelian'));
                    } else if ($row['status_bayar'] == '3') {
                        $status_trx = array('hex'=>'#8003A9F4', 'text'=>'On Process');
                    } else {
                        $status_trx = array('hex'=>'#80673AB7', 'text'=>'Waiting Payment Verified');
                    }
                } else if ($row['status'] == '2' || $row['status'] == '3') {
                    if ($row['status'] == '3') {
                        array_push($action, array('api'=>'finish_order', 'text'=>'Terima Pesanan'));
                    }
                    array_push($action, array('api'=>'lacak_resi', 'text'=>'Lacak Pengiriman'));

                    $status_trx = array('hex'=>'#8003A9F4', 'text'=>'On Process');
                } else if ($row['status'] == '4') {
                    $status_trx = array('hex'=>'#804CAF50', 'text'=>'Success');
                } else {
                    $status_trx = array('hex'=>'#80f44336', 'text'=>'Cancel');
                }
                $get_ongkir = $this->m_crud->get_data("pengiriman", "biaya", "orders='" . $row['id_orders'] . "'");

                $get_data[$key]['status_trx'] = $status_trx;
                $get_data[$key]['action'] = $action;
                $get_data[$key]['ongkir'] = $get_ongkir['biaya'];
            }
        } else {
            $status = false;
        }

        echo json_encode(array('status'=>$status, 'data'=>$get_data));
    }

    public function konfirmasi_pembayaran() {
        $result = array();
        $id_pembayaran = $_POST['id_pembayaran'];
        $member = $_POST['id_member'];

        $row = 'bukti_transfer';
        $path = 'assets/images/bukti_transfer/';
        $config['upload_path']          = './'.$path;
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['max_size']             = 5120;
        $this->load->library('upload', $config);
        $valid = true;

        if( (!$this->upload->do_upload($row)) && $_FILES[$row]['name']!=null){
            $valid = false;
            $file[$row]['file_name']=null;
            $file[$row] = $this->upload->data();
            $data['error_'.$row] = $this->upload->display_errors();
        } else{
            $file[$row] = $this->upload->data();
            $data[$row] = $file;
        }

        $this->db->trans_begin();

        $data_pembayaran = array("tgl_konfirmasi"=>date('Y-m-d H:i:s'), "status"=>'2');
        if($_FILES[$row]['name']!=null){
            $data_pembayaran['bukti_transfer'] = $path.$file[$row]['file_name'];
        }

        if ($valid) {
            $this->m_crud->update_data("pembayaran", $data_pembayaran, "id_pembayaran='" . $id_pembayaran . "'");
        }

        $result['id'] = $id_pembayaran;
        if ($this->db->trans_status() === FALSE || $valid === false) {
            $this->db->trans_rollback();
            $result['status'] = false;
            $result['pesan'] = "Terjadi kesalahan, silahkan ulangi lagi.";
        } else {
            $this->db->trans_commit();
            $result['status'] = true;
            $result['pesan'] = "Konfirmasi pembayaran berhasil, tunggu beberapa saat untuk verifikasi pembayaran anda.";

            $get_onsignal = $this->m_crud->get_data("customer", "one_signal_id", "kd_cust='".$member."'");

            $data_notif = array(
                'member'=>$get_onsignal['one_signal_id'],
                /*'segment'=>'All',*/
                'data' => array("param" => "bukti_transfer", "kd_trx" => $id_pembayaran),
                'head'=>'Menunggu Verifikasi Pembayaran',
                'content'=>'Konfirmasi pembayaran berhasil, tunggu beberapa saat untuk verifikasi pembayaran anda.'
            );

            $this->m_website->create_notif($data_notif);
        }

        echo json_encode($result);
    }

    public function send_notif()
    {
        $get_onsignal = $this->m_crud->get_data("customer", "one_signal_id", "kd_cust='M18092400001'");

        $data_notif = array(
            'member'=>$get_onsignal['one_signal_id'],
            /*'segment'=>'All',*/
            'data' => array(
                "param" => "bukti_transfer",
                "kd_trx" => "HAHAHA"
            ),
            'head'=>'Bukti Transfer',
            'content'=>'Kirim ongkir terbaru'
        );

        echo $this->m_website->create_notif($data_notif);
    }

    public function get_item_cart($param=null) {
        $result = array();
        $orders = json_decode($param, true);

        $cart = array();
        foreach ($orders as $row) {
            $id = str_replace("_", "/", $row);

            $get_produk = $this->m_crud->join_data("det_orders do", "do.det_produk, do.qty, do.berat, do.hrg_jual, do.hrg_varian, do.diskon, do.catatan, bo.id_barang id_produk, bo.nama nama_produk, bo.id_barang sku, bo.gambar, '-' ukuran, '-' warna, 0 stok", "barang_online bo", "do.det_produk=bo.id_barang", "do.orders='".$id."'");
            $get_pengiriman = $this->m_crud->get_data("pengiriman", "*", "orders='".$id."'");

            $cart_list = array();
            $data_produk = array();
            $data_pengiriman = array();
            if ($get_produk!=null) {
                foreach ($get_produk as $row_produk) {
                    $list_produk = array(
                        'det_produk' => $row_produk['det_produk'],
                        'qty' => $row_produk['qty'],
                        'berat' => $row_produk['berat'],
                        'catatan' => $row_produk['catatan'],
                        'hrg_jual' => $row_produk['hrg_jual'],
                        'hrg_varian' => $row_produk['hrg_varian'],
                        'diskon' => $row_produk['diskon'],
                        'id_produk' => $row_produk['id_produk'],
                        'nama_produk' => $row_produk['nama_produk'].' ('.$row_produk['ukuran'].' ~ '.$row_produk['warna'].')',
                        'sku' => $row_produk['sku'],
                        /*'stok' => $row_produk['stok']*/
                        'stok' => 99
                    );
                    /*Get gambar produk*/
                    $list_produk['gambar_produk'] = array(base_url() . $row_produk['gambar']);

                    array_push($data_produk, $list_produk);
                }
            }

            if ($get_pengiriman != null) {
                $data_pengiriman = $get_pengiriman;
            }

            $cart_list['orders'] = $id;
            $cart_list['id'] = $row;
            $cart_list['list_produk'] = $data_produk;
            $cart_list['list_pengiriman'] = $data_pengiriman;
            array_push($cart, $cart_list);
        }

        $result['res_cart'] = $cart;
        $result['status'] = true;

        return json_encode($result);
    }

    public function get_detail_pembelian() {
        $result = array();

        $id_pembayaran = $_POST['id_pembayaran'];
        $get_pembayaran = $this->m_crud->get_data("pembayaran", "bank, no_rek, atas_nama, jumlah, kode_unik", "id_pembayaran='" . $id_pembayaran . "'");

        if ($get_pembayaran == null) {
            $result['status'] = false;
        } else {
            $result['status'] = true;
            $data_produk = array();

            $kode_unik = $get_pembayaran['kode_unik'];
            $get_penjualan = $this->m_crud->read_data("det_pembayaran", "orders", "pembayaran='" . $id_pembayaran . "'");
            $kode_orders = array();
            foreach ($get_penjualan as $row) {
                array_push($kode_orders, str_replace("/", "_", $row['orders']));
                $get_status = $this->m_crud->get_data("orders", "status", "id_orders='".$row['orders']."'");
                if ($get_status['status'] == '3') {
                    $result['finish_button'] = true;
                } else {
                    $result['finish_button'] = false;
                }
            }

            $get_data_cart = json_decode($this->get_item_cart(json_encode($kode_orders)), true);

            $res_cart = $get_data_cart['res_cart'];

            $ongkir = 0;
            foreach ($res_cart as $row) {
                foreach ($row['list_produk'] as $row_produk) {
                    $produk = array(
                        'nama_produk' => $row_produk['nama_produk'],
                        'qty' => $row_produk['qty'],
                        'catatan' => $row_produk['catatan'],
                        'hrg_jual' => $row_produk['hrg_jual']+$row_produk['hrg_varian'],
                        'diskon' => $row_produk['diskon'],
                        'gambar' => $row_produk['gambar_produk'][0]
                    );

                    array_push($data_produk, $produk);
                }

                $ongkir = $ongkir + $row['list_pengiriman']['biaya'];
            }

            $tagihan = 0;
            $diskon = 0;
            foreach ($data_produk as $row) {
                $tagihan = $tagihan + ($row['qty']*$row['hrg_jual']);
                $diskon = $diskon + ($row['qty']*$row['diskon']);
            }

            $result['res_detail'] = $res_cart;
        }

        echo json_encode($result);
    }

    public function cancel_order() {
        $result = array();
        $id_pembayaran = $_POST['id_pembayaran'];
        $this->db->trans_begin();

        $this->m_crud->update_data("pembayaran", array('status'=>'4'), "id_pembayaran='".$id_pembayaran."'");
        $this->m_crud->update_data("orders", array('status'=>'5'), "id_orders IN (SELECT orders FROM det_pembayaran WHERE pembayaran='".$id_pembayaran."')");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['status'] = false;
            $result['pesan'] = "Terjadi kesalahan, silahkan ulangi lagi.";
        } else {
            $this->db->trans_commit();
            $result['status'] = true;
            $result['pesan'] = "Transaksi berhasil dibatalkan.";
        }

        echo json_encode($result);
    }

    public function finish_order() {
        $result = array();
        $id_orders = $_POST['id_orders'];
        $this->db->trans_begin();

        $this->m_crud->update_data("orders", array('status'=>'4'), "id_orders='".$id_orders."'");

        $get_total = $this->m_crud->get_data("det_orders", "SUM(qty*(hrg_jual+hrg_varian-diskon)) total", "orders='".$id_orders."'");
        /*$check_data = $this->m_crud->get_data("poin", "kode_transaksi", "kode_transaksi='".$id_orders."'");
        if ($check_data == null) {
            $this->m_crud->create_data("poin", array('kode_transaksi'=>$id_orders, 'member'=>$this->user, 'poin'=>floor($get_total['total']/100000), 'keterangan'=>'Pembelian'));
        }*/

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['status'] = false;
            $result['pesan'] = "Terjadi kesalahan, silahkan ulangi lagi.";
        } else {
            $this->db->trans_commit();
            $result['status'] = true;
            $result['pesan'] = "Terimakasih, transaksi anda berhasil.";
        }

        echo json_encode($result);
    }

    public function lacak_resi() {
        $result = array();
        $id_pengiriman = $_POST['id_pengiriman'];
        $get_data = $this->m_crud->get_data("pengiriman", "orders, kurir, no_resi", "id_pengiriman='".$id_pengiriman."'");

        $resi = $this->m_website->rajaongkir_resi(json_encode(array('resi'=>$get_data['no_resi'], 'kurir'=>strtolower($get_data['kurir']))));
        $decode = json_decode($resi, true);
        $status_resi = $decode['rajaongkir']['status']['code'];

        if ($status_resi == '200') {
            $result = $decode['rajaongkir']['result'];
            $delivered = $result['delivered'];
            $summary = $result['summary'];
            $details = $result['details'];
            $manifest = $result['manifest'];

            if ($delivered) {
                $this->m_crud->update_data("orders", array('status'=>'4'), "id_orders='".$get_data['orders']."'");
                $result['message'] = "Paket telah tiba di tujuan";
            } else {
                $this->m_crud->update_data("orders", array('status'=>'3'), "id_orders='".$get_data['orders']."'");
                $result['message'] = "Paket dalam proses pengiriman";
            }

            $result['delivered'] = $delivered;
            $result['summary'] = $summary;
            $result['details'] = $details;
            $result['manifest'] = $manifest;
            $result['status'] = true;
        } else {
            $result['status'] = false;
            $result['pesan'] = "Nomor resi salah atau belum tercatat di sistem kurir";
        }

        echo json_encode($result);
    }

    public function insert_feedback(){
        $this->db->trans_begin();

        $kd_trx = $_POST['kd_trx'];
        $tgl = date('Y-m-d H:i:s');
        $email = '-';
        $customer = $_POST['id_member'];
        $response = $_POST['response']; //1,2,3,4,5
        $comment = str_replace("'",'`',$_POST['comment']);

        $data_res_customer = array(
            'kd_trx' => $kd_trx,
            'tgl' => $tgl,
            'email' => $email,
            'customer' => $customer,
            'response' => $response,
            'comment' => $comment
        );

        $this->m_crud->create_data('res_customer', $data_res_customer);

        $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO' and kode='".$lokasi."'");
        foreach ($read_lokasi as $item) {
            $log = array(
                'type' => 'I', //I insert, U update, D delete
                'table' => "res_customer",
                'data' => $data_res_customer,
                'condition' => ""
            );

            $data_log = array(
                'lokasi' => $item['Kode'],
                'hostname' => $item['server'],
                'db_name' => $item['db_name'],
                'query' => json_encode($log)
            );
            $this->m_website->insert_log_api($data_log);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status'=>false, 'pesan'=>'Rating gagal di input'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('status'=>true, 'pesan'=>'Terimakasih atas feedback nya'));
        }
    }

    public function notif() {
        $data_notif = array(
            'member'=>'7b85481c-8bc7-492b-94db-e169e30eb4d0',
            /*'segment'=>'All',*/
            'data' => array("kd_trx" => "TA1809190001A"),
            'head'=>'Program Kafalah',
            'content'=>'Silahkan melakukan santunan kafalah di menu riwayat'
        );

        //echo $this->m_website->create_notif($data_notif);

        $this->setting = $this->m_website->setting();

        $this->poin_setting = json_decode($this->setting->poin_setting, true);

        $tgl = '2018-09-19 14:46:41';
        $masa_berlaku = $this->poin_setting['berlaku'].' '.$this->poin_setting['masa'];

        $masa_aktif = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', strtotime($tgl)) . " + ".$masa_berlaku));
        echo $masa_aktif;
    }

    public function report_feedback($per_page=10, $page=1) {
        $response = array();
        $start = ($page-1)*$per_page+1;
        $end = $per_page*$page;

        $id_member = $_POST['id_member'];

        $get_feedback = $this->m_crud->select_limit_join("tr_sukses ts", "ts.kd_trx, ts.tgl, convert(time, ts.jam) jam, rc.tgl tgl_rating, rc.response, rc.comment", array(array('table'=>'res_customer rc', 'type'=>'LEFT')), array("rc.kd_trx=ts.kd_trx"), "ts.kd_cust='".$id_member."'", "ts.kd_trx desc", null, $start, $end);

        if ($get_feedback != null) {
            $response['status'] = true;
            $response['data'] = $this->m_website->tambah_data('report_feedback', $get_feedback);
        } else {
            $response['status'] = false;
            $response['data'] = array();
        }

        echo json_encode($response);
    }

    public function get_feedback($lokasi_ = null) {
        $lokasi = base64_decode($lokasi_);

        if ($lokasi=='-' || $lokasi_==null) {
            $where_lokasi = "";
        } else {
            $where_lokasi = " AND mt.lokasi = '".$lokasi."'";
        }

        $get_feedback = $this->m_crud->read_data("res_customer rc, master_trx mt", "rc.response, COUNT(response) res", "mt.kd_trx=rc.kd_trx ".$where_lokasi, null, "rc.response");

        $total_data = 0;
        $countExcl = 0;
        $countGood = 0;
        $countFair = 0;
        $countBad = 0;

        foreach ($get_feedback as $row) {
            if ($row['response'] == 'Excellent') {
                $countExcl = $row['res'];
            } else if ($row['response'] == 'Good') {
                $countGood = $row['res'];
            } else if ($row['response'] == 'Fair') {
                $countFair = $row['res'];
            } else if ($row['response'] == 'Bad') {
                $countBad = $row['res'];
            }

            $total_data = $total_data + $row['res'];
        }

        if ($total_data != 0) {
            $progExcl = ($countExcl / $total_data) * 100;
            $progGood = ($countGood / $total_data) * 100;
            $progFair = ($countFair / $total_data) * 100;
            $progBad = ($countBad / $total_data) * 100;
        } else {
            $progExcl = 0;
            $progGood = 0;
            $progFair = 0;
            $progBad = 0;
        }

        echo json_encode(array('countExcl'=>$countExcl,'progExcl'=>$progExcl,'countGood'=>$countGood,'progGood'=>$progGood,'countFair'=>$countFair,'progFair'=>$progFair,'countBad'=>$countBad,'progBad'=>$progBad));
    }

    public function insert_reservasi(){
        $this->db->trans_begin();

        $lokasi = $_POST['lokasi'];
        $tgl = date('Y-m-d H:i:s');
        $kd_trx = $this->m_website->generate_kode('RS', $lokasi, date('ymd',strtotime($tgl)));
        $email = $_POST['email'];
        $atas_nama = $_POST['atas_nama'];
        $jumlah_pengunjung = $_POST['jumlah_pengunjung'];
        $customer = $_POST['member'];
        $hp = $_POST['hp'];
        $catatan = str_replace("'",'',$_POST['catatan']);
        $status = '0';

        $data_reservasi = array(
            'kd_trx' => $kd_trx,
            'tgl' => $tgl,
            'email' => $email,
            'customer' => $customer,
            'jumlah_pengunjung' => $jumlah_pengunjung,
            'catatan' => $catatan,
            'atas_nama' => $atas_nama,
            'hp' => $hp,
            'status' => $status,
            'lokasi' => $lokasi
        );

        $this->m_crud->create_data('master_reservasi', $data_reservasi);

        $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO' and kode='".$lokasi."'");
        foreach ($read_lokasi as $item) {
            $log = array(
                'type' => 'I', //I insert, U update, D delete
                'table' => "master_reservasi",
                'data' => $data_reservasi,
                'condition' => ""
            );

            $data_log = array(
                'lokasi' => $item['Kode'],
                'hostname' => $item['server'],
                'db_name' => $item['db_name'],
                'query' => json_encode($log)
            );
            $this->m_website->insert_log_api($data_log);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status'=>false));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('status'=>true));
        }
    }

    public function aprov_reservasi(){
        $this->db->trans_begin();

        $kd_trx = $_POST['kd_trx'];
        $tgl = date('Y-m-d H:i:s');
        $user = $_POST['user'];
        $status = '1';

        $data_reservasi = array(
            'status' => $status,
            'tgl_aprov' => $tgl,
            'user_aprov' => $user
        );

        $this->m_crud->update_data('master_reservasi', $data_reservasi, "kd_trx='".$kd_trx."'");

        $read_lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama, server, db_name', "Kode<>'HO' and kode='".$lokasi."'");
        foreach ($read_lokasi as $item) {
            $log = array(
                'type' => 'U', //I insert, U update, D delete
                'table' => "master_reservasi",
                'data' => $data_reservasi,
                'condition' => "kd_trx='".$kd_trx."'"
            );

            $data_log = array(
                'lokasi' => $item['Kode'],
                'hostname' => $item['server'],
                'db_name' => $item['db_name'],
                'query' => json_encode($log)
            );
            $this->m_website->insert_log_api($data_log);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status'=>false));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('status'=>true));
        }
    }

    public function get_home() {
        $result = array();

        /*Get Favorit*/
        $produk = $this->get_data('return_barang', "where|best='1'", 1, 5);
        $result['favorit'] = $produk;

        /*Get Berita*/
        $berita = $this->get_data('return_berita', 'page', 1, 1);
        $result['berita'] = $berita;

        /*Get Lokasi*/
        $lokasi = $this->get_lokasi(1, true);
        $result['lokasi'] = $lokasi;

        echo json_encode($result);
    }

    public function get_slider() {
        $result = array('status'=>true);
        $param = $_POST['param'];

        $get_data = $this->m_crud->get_data("Setting", "slider", "kode = '1111'");
        $data_slide = json_decode($get_data['slider'], true);

        if (count($data_slide[$param]) > 0) {
            $res_data = array();
            foreach ($data_slide[$param] as $item) {
                array_push($res_data, array('gambar'=>$this->m_website->file_thumb(base_url().$item['foto'])));
            }
        } else {
            $res_data = array(
                array(
                    'gambar' => $this->m_website->no_img()
                )
            );
        }

        $result['data'] = $res_data;

        echo json_encode($result);
    }

    public function riwayat_belanja($perpage=10, $page=1) {
        $result = array();
        $member = $_POST['id_member'];
        $start = ($page-1)*$perpage+1;
        $end = ($perpage*$page);
        $where = "rt.kd_cust='".$member."'";

        $get_riwayat = $this->m_crud->select_limit_join('report_trx rt', "rt.*, cs.nama nama_customer, sl.nama nama_waitres, ud.nama nama_kasir, isnull(cm.nama, '-') nama_compliment, lk.nama nama_lokasi", array("customer cs", "sales sl", "user_detail ud", "lokasi lk", array('table'=>'compliment cm', 'type'=>'LEFT')), array("cs.kd_cust=rt.kd_cust", "sl.kode=rt.kd_sales", "ud.user_id=rt.kd_kasir", "lk.kode=rt.lokasi", "cm.compliment_id=rt.compliment"), $where, 'rt.kd_trx desc', null, $start, $end);

        if ($get_riwayat != null && isset($member)) {
            $result['status'] = true;
            $result['data'] = $this->m_website->tambah_data('riwayat_belanja', $get_riwayat);
        } else {
            $result['status'] = false;
        }

        echo json_encode($result);
    }

    public function detail_belanja() {
        $result = array();
        $kd_trx = $_POST['kd_trx'];

        $get_detail = $this->m_crud->join_data("det_trx dt", "dt.*, br.nm_brg, br.deskripsi, br.gambar", "barang br", "br.kd_brg=dt.kd_brg", "dt.kd_trx='".$kd_trx."'");

        if ($get_detail != null) {
            $result['status'] = true;
            $result['data'] = $this->m_website->tambah_data('detail_belanja', $get_detail);
        } else {
            $result['status'] = false;
        }


        echo json_encode($result);
    }

    public function contact_us() {
        $response = array();

        $master = array(
            'tanggal' => date('Y-m-d H:i:s'),
            'nama' => $_POST['nama'],
            'tlp' => $_POST['tlp'],
            'email' => $_POST['email'],
            'pesan' => $_POST['pesan']
        );

        $this->db->trans_begin();

        $this->m_crud->create_data("contact", $master);

        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            $response['status'] = true;
            $response['pesan'] = 'Terimakasih';
        } else {
            $this->db->trans_rollback();
            $response['status'] = false;
            $response['pesan'] = 'Gagal, silahkan ulangi lagi';
        }

        echo json_encode($response);
    }

    public function new_deposit() {
        $member = $_POST['id_member'];

        $get_setting = $this->m_website->setting();
        $get_deposit = json_decode($get_setting->deposit, true);
        $get_saldo = $this->m_crud->get_data("kartu_deposit", "ISNULL(SUM(saldo_masuk-saldo_keluar), 0) deposit", "member='".$member."'");

        $response = array(
            'status' => true,
            'min_deposit' => $get_deposit['minimal'],
            'saldo' => $get_saldo['deposit']+0
        );

        echo json_encode($response);
    }

    public function rekening_deposit() {
        $member = $_POST['id_member'];
        $bank = $_POST['bank'];
        $response = array();

        $get_rekening = $this->m_crud->get_data("deposit", "det_bank2", "rekening2='".$bank."' and member='".$member."'", "tgl_deposit ASC");

        if ($get_rekening != null) {
            $response['status'] = true;
            $response['res_rekening'] = json_decode($get_rekening['det_bank2'], true);
        } else {
            $response['status'] = false;
            $response['pesan'] = "Data rekening masih kosong";
        }

        echo json_encode($response);
    }

    public function simpan_deposit() {
        $response = array();
        $tanggal = date('Y-m-d H:i:s');
        $member = $_POST['id_member'];
        $nominal = $_POST['nominal'];
        $rekening1 = $_POST['bank1'];
        $bank1 = array(
            'bank' => $_POST['bank1'],
            'no_rek' => $_POST['no_rek1'],
            'atas_nama' => $_POST['atas_nama1']
        );
        $rekening2 = $_POST['bank2'];
        $bank2 = array(
            'bank' => $_POST['bank2'],
            'no_rek' => $_POST['no_rek2'],
            'atas_nama' => $_POST['atas_nama2']
        );

        $this->db->trans_begin();

        $kode_unik = $this->m_website->get_kodeunik($nominal, $_POST['no_rek1']);

        $code_pembayaran = $this->m_website->generate_kode('deposit', date('ymd'), null);
        $master = array(
            'id_deposit' => $code_pembayaran,
            'member' => $member,
            'tgl_deposit' => $tanggal,
            'nominal' => $nominal,
            'kode_unik' => $kode_unik,
            'rekening1' => $rekening1,
            'det_bank1' => json_encode($bank1),
            'rekening2' => $rekening2,
            'det_bank2' => json_encode($bank2),
            'status' => '0'
        );
        $this->m_crud->create_data("deposit", $master);

        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            $response['status'] = true;
            $response['pesan'] = 'Transaksi berhasil';

            $response['judul'] = "Request Deposit Berhasil.";
            $response['deskripsi'] = "Silahkan lakukan transfer ke rekening yang tertera di atas, dan segera lakukan konfirmasi jika telah melakukan transfer.";
            $response['code'] = $code_pembayaran;
            $response['bank'] = $rekening1;
            $response['norek'] = $_POST['no_rek1'];
            $response['atasnama'] = $_POST['atas_nama1'];
            $response['total'] = (float)$nominal+(float)$kode_unik;

            $get_onsignal = $this->m_crud->get_data("customer", "one_signal_id", "kd_cust='".$member."'");

            $data_notif = array(
                'member'=>$get_onsignal['one_signal_id'],
                /*'segment'=>'All',*/
                'data' => array("param" => "deposit", "kd_trx" => $code_pembayaran),
                'head'=>'Request Deposit Berhasil',
                'content'=>'Silahkan lakukan transfer sebesar Rp '.number_format((float)$nominal+(float)$kode_unik, 0, ',', '.').' ke rekening '.$rekening1.' ('.$_POST['no_rek1'].') atas nama '.$_POST['atas_nama1']
            );

            $this->m_website->create_notif($data_notif);

        } else {
            $this->db->trans_rollback();
            $response['status'] = false;
            $response['pesan'] = 'Transaksi gagal, silahkan ulangi lagi';
        }

        echo json_encode($response);
    }

    public function confirm_deposit() {
        $response = array();
        $id_deposit = $_POST['id_deposit'];
        $member = $_POST['id_member'];
        $row = 'bukti_transfer';
        $path = 'assets/images/bukti_transfer/';
        $config['upload_path']          = './'.$path;
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['max_size']             = 5120;
        $this->load->library('upload', $config);
        $valid = true;

        if( (!$this->upload->do_upload($row)) && $_FILES[$row]['name']!=null){
            $valid = false;
            $file[$row]['file_name']=null;
            $file[$row] = $this->upload->data();
            $data['error_'.$row] = $this->upload->display_errors();
        } else{
            $file[$row] = $this->upload->data();
            $data[$row] = $file;
        }

        $this->db->trans_begin();

        $master = array(
            'bukti_transfer' => $path.$file[$row]['file_name'],
            'status' => '2',
            'tgl_confirm' => date('Y-m-d H:i:s')
        );

        $this->m_crud->update_data("deposit", $master, "id_deposit='".$id_deposit."'");

        if ($this->db->trans_status() === true && $valid) {
            $this->db->trans_commit();
            $response['status'] = true;
            $response['pesan'] = "Data berhasil disimpan";
            $get_onsignal = $this->m_crud->get_data("customer", "one_signal_id", "kd_cust='".$member."'");

            $data_notif = array(
                'member'=>$get_onsignal['one_signal_id'],
                /*'segment'=>'All',*/
                'data' => array("param" => "confirm_deposit", "kd_trx" => $id_deposit),
                'head'=>'Konfirmasi Berhasil',
                'content'=>'Silahkan tunggu beberapa saat untuk verifikasi pembayaran anda'
            );

            $this->m_website->create_notif($data_notif);
        } else {
            $this->db->trans_rollback();
            $response['status'] = false;
            $response['pesan'] = "Upload gagal, silahkan ulangi lagi!";
        }

        echo json_encode($response);
    }

    public function cancel_deposit() {
        $response = array();
        $member = $_POST['id_member'];
        $id_deposit = $_POST['id_deposit'];

        $this->db->trans_begin();

        $this->m_crud->update_data("deposit", array('status'=>'3'), "id_deposit='".$id_deposit."'");

        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            $response['status'] = true;
            $response['pesan'] = "Deposit berhasil di batalkan!";

            $get_onsignal = $this->m_crud->get_data("customer", "one_signal_id", "kd_cust='".$member."'");

            $data_notif = array(
                'member'=>$get_onsignal['one_signal_id'],
                /*'segment'=>'All',*/
                'data' => array("param" => "confirm_deposit", "kd_trx" => $id_deposit),
                'head'=>'Deposit Dibatalkan',
                'content'=>'Transaksi deposit anda telah dibatalkan'
            );

            $this->m_website->create_notif($data_notif);
        } else {
            $this->db->trans_rollback();
            $response['status'] = false;
            $response['pesan'] = "Transaksi gagal, silahkan ulangi lagi";
        }

        echo json_encode($response);
    }

    public function riwayat_deposit($perpage=10, $page=1) {
        $response = array();
        $member = $_POST['id_member'];
        $start = ($page-1)*$perpage+1;
        $end = ($perpage*$page);

        $get_riwayat = $this->m_crud->select_limit("deposit", "*", "member='".$member."'", "tgl_deposit desc", null, $start, $end);

        if ($get_riwayat != null) {
            $status = true;
            foreach ($get_riwayat as $key => $row) {
                $action = array();
                if ($row['status'] == '0') {
                    $status_trx = array('hex'=>'#80FF9800', 'text'=>'Waiting Payment');
                    array_push($action, array('api'=>'confirm_deposit', 'text'=>'Konfirmasi Deposit'));
                    array_push($action, array('api'=>'cancel_deposit', 'text'=>'Batalkan Deposit'));
                } else if ($row['status'] == '1') {
                    $status_trx = array('hex'=>'#804CAF50', 'text'=>'Success');
                } else if ($row['status'] == '2') {
                    array_push($action, array('api'=>'cancel_deposit', 'text'=>'Batalkan Deposit'));
                    $status_trx = array('hex'=>'#80673AB7', 'text'=>'Waiting Payment Verified');
                } else {
                    $status_trx = array('hex'=>'#80f44336', 'text'=>'Cancel');
                }

                $get_riwayat[$key]['status_trx'] = $status_trx;
                $get_riwayat[$key]['action'] = $action;
            }
        } else {
            $status = false;
        }

        $response['status'] = $status;
        $response['data'] = $get_riwayat;

        echo json_encode($response);
    }

    /*API PPOB*/
    public function tr_ppob() {
        $response = array();
        $param = $_POST['param'];
        $url = $param;
        $member = $_POST['id_member'];
        $data = json_decode($_POST['data'], true);
        $saldo = $this->m_website->get_deposit($member);
        $generate_token = json_decode($this->m_website->api_npayment("req_token", array('id_user'=>$member, 'dompet'=>$this->dompet)), true);

        $data['dompet'] = $this->dompet;
        $data['pin'] = $this->pin;
        $data['token'] = $generate_token['token'];

        $status_saldo = true;

        if ($param == 'trx_portalpulsa' || $param == 'trx_duniapulsa') {
            if ($saldo < $data['harga_jual']) {
                $status_saldo = false;
            }
        } else if ($param == 'riwayat_transaksi') {
            $start = ($_POST['page']-1)*$_POST['perpage']+1;
            $end = $_POST['perpage']*$_POST['page'];
            $where = "member='".$member."' and transaksi = '".$data['transaksi']."'";

            if (isset($data['kode']) && $data['kode']!='') {
                $where .= " AND kd_trx='".$data['kode']."'";
            }

            if (!isset($_POST['kode']) && $_POST['kode']=='') {
                $riwayat = $this->m_crud->select_limit("tr_ppob", "kd_trx", $where, "kd_trx desc", null, $start, $end);

                $in_array = array();
                if ($riwayat != null) {
                    foreach ($riwayat as $item) {
                        array_push($in_array, "'" . $item['kd_trx'] . "'");
                    }
                    $data['in_trx'] = base64_encode(implode(',', $in_array));
                    $url = $param . '/all/' . $_POST['page'];
                } else {
                    $status_saldo = false;
                }
            }
        }

        if ($status_saldo) {
            $req_api = $this->m_website->api_npayment($url, $data);
        } else {
            $req_api = json_encode(array('status'=>false, 'pesan'=>'Saldo deposit anda tidak cukup!'));
        }

        $res_decode = json_decode($req_api, true);
        if ($param == 'trx_portalpulsa' || $param == 'trx_duniapulsa') {
            if ($res_decode['status']) {
                $master_ppob = array(
                    'kd_trx' => $res_decode['kode'],
                    'member' => $member,
                    'tgl' => date('Y-m-d H:i:s'),
                    'nominal' => $data['harga_jual'],
                    'status' => '0',
                    'request' => $res_decode['data'],
                    'transaksi' => $data['transaksi']
                );

                $this->m_crud->create_data("tr_ppob", $master_ppob);
            }
        } else if ($param == 'riwayat_transaksi') {
            if (!isset($_POST['kode']) && $_POST['kode']=='') {
                $res_decode['status'] = $status_saldo;

                if ($status_saldo == false) {
                    $res_decode['pesan'] = "Data kosong!";
                }

                $req_api = json_encode($res_decode);
            }
        }

        echo $req_api;
    }

    public function callback_ppob() {
        $data = json_decode($_POST['data'], true);

        $get_onesignal = $this->m_crud->get_join_data("tr_ppob tp", "transaksi, cs.one_signal_id", "customer cs", "cs.kd_cust=tp.member", "tp.kd_trx='".$data['id']."'");

        if ($get_onesignal != null) {
            $res = $data['data'];
            if ($res['status'] == '1') {
                $title = "Transaksi ".$get_onesignal['transaksi']." Berhasil";
            } else {
                $title = "Transaksi ".$get_onesignal['transaksi']." Gagal";
            }
            $data_notif = array(
                'member'=>$get_onesignal['one_signal_id'],
                /*'segment'=>'All',*/
                'data' => array("data" => $get_onesignal['transaksi'], "param" => "ppob", "kd_trx" => $data['id']),
                'head'=>'PPOB '.$this->data['site']->title,
                'content'=>$title
            );

            $this->m_website->create_notif($data_notif);

            $this->m_crud->update_data("tr_ppob", $res, "kd_trx='" . $data['id'] . "'");
        }
    }
    /*END API PPOB*/

    public function req_api_ppob() {
        $json = '{
            "commands" : "pay-pasca",
            "username" : "08112233253",
            "tr_id"    : "1010202d0",
            "sign"     : "'.md5("08112233253"."4285bd03709111f2"."1010202d0").'"
        }';

        $url = "https://testpostpaid.mobilepulsa.net/api/v1/bill/check";

        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);
        curl_close($ch);

        print_r($data);
    }
}

