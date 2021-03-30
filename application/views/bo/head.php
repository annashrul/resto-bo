
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="" />
	<meta name="author" content="" />

	<link rel="shortcut icon" type="image/png" href="<?=$this->config->item('url').$site->fav_icon?>" />
	<title><?=$title." | ".$site->title?></title>

	<!-- Base Css Files -->
	<link href="<?=base_url().'assets/'?>css/bootstrap.min.css" rel="stylesheet" />
	<!-- Font Icons -->
	<link href="<?=base_url().'assets/'?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="<?=base_url().'assets/'?>assets/ionicon/css/ionicons.min.css" rel="stylesheet" />
	<link href="<?=base_url().'assets/'?>css/material-design-iconic-font.min.css" rel="stylesheet" />
	<!-- animate css -->
	<link href="<?=base_url().'assets/'?>css/animate.css" rel="stylesheet" />
	<!-- Waves-effect -->
	<link href="<?=base_url().'assets/'?>css/waves-effect.css" rel="stylesheet" />
	<!-- DataTables -->
    <link href="<?=base_url().'assets/'?>assets/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!-- Responsive-table -->
    <link href="<?=base_url().'assets/'?>assets/responsive-table/rwd-table.min.css" rel="stylesheet" type="text/css" media="screen"/>
	<!-- sweet alerts -->
	<!--<link href="<?/*=base_url().'assets/'*/?>assets/sweet-alert/sweet-alert.min.css" rel="stylesheet" />-->
	<!-- Plugins css-->
	<link href="<?=base_url().'assets/'?>assets/tagsinput/jquery.tagsinput.css" rel="stylesheet" />
	<link href="<?=base_url().'assets/'?>assets/toggles/toggles.css" rel="stylesheet" />
	<link href="<?=base_url().'assets/'?>assets/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" />
	<link href="<?=base_url().'assets/'?>assets/timepicker/bootstrap-datepicker.min.css" rel="stylesheet" />
	<link href="<?=base_url().'assets/'?>assets/colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" type="text/css" />
	<link href="<?=base_url().'assets/'?>assets/jquery-multi-select/multi-select.css"  rel="stylesheet" type="text/css" />
	<link href="<?=base_url().'assets/'?>assets/select2/select2.css" rel="stylesheet" type="text/css" />
	<!-- Custom Files -->
	<link href="<?=base_url().'assets/'?>css/helper.css" rel="stylesheet" type="text/css" />
	<link href="<?=base_url().'assets/'?>css/style.css" rel="stylesheet" type="text/css" />

	<!--<link href="<?/*=base_url().'assets/'*/?>css/bootstrap-datetimepicker.css" rel="stylesheet" />-->

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->

    <!--daterangepicker-->
    <link rel="stylesheet" type = "text/css" href="<?=base_url().'assets/'?>assets/daterangepicker/daterangepicker.css" />
    <!--<script src="<?/*=base_url().'assets/'*/?>assets/daterangepicker/moment.js"></script>
    <script src="<?/*=base_url().'assets/'*/?>assets/daterangepicker/daterangepicker.js"></script>-->

    <script src="<?=base_url().'assets/'?>js/jquery.min.js"></script>
    <script src="<?=base_url().'assets/'?>js/bootstrap.min.js"></script>
    <script src="<?=base_url().'assets/'?>js/modernizr.min.js"></script>

    <!--<link rel="stylesheet" type = "text/css" href="<?/*=base_url().'assets/'*/?>assets/auto-complete/jquery.autocomplete.css" />
	<script src="<?/*=base_url().'assets/'*/?>assets/auto-complete/jquery.autocomplete.js" type="text/javascript"></script>-->

    <script src="<?=base_url().'assets/'?>assets/jQuery-autocomplete/jquery.autocomplete.js" type="text/javascript"></script>

    <style>
        .autocomplete-suggestions { border: 1px solid #999; background: #fff; cursor: default; overflow: auto; }
        .autocomplete-suggestion { padding: 10px 5px; font-size: 1.2em; white-space: nowrap; overflow: hidden; }
        .autocomplete-selected { background: #f0f0f0; }
        .autocomplete-suggestions strong { font-weight: normal; color: #3399ff; }
        .autocomplete-loading { background:url('<?=base_url().'assets/images/spin.svg'?>') no-repeat right center }
    </style>

	<!--Daterangepicker-->
	<script src="<?=base_url().'assets/'?>assets/daterangepicker/moment.js" type="text/javascript"></script>
	<script src="<?=base_url().'assets/'?>assets/daterangepicker/daterangepicker.js" type="text/javascript"></script>

	<!--Chart Js-->
	<script src="<?=base_url().'assets/'?>assets/chartjs/Chart.js"></script>

    <link href="<?=base_url().'assets/'?>assets/notifications/notification.css" rel="stylesheet" />

	<!--Barcode-->
	<script src="<?=base_url().'assets/js/'?>JsBarcode_all.js"></script>

    <!--CKEDITOR-->
    <script src="<?=base_url().'assets/'?>assets/ckeditor/ckeditor.js"></script>

    <!-- bootstrap color picker -->
    <script src="<?=base_url().'assets/'?>assets/colorpicker/js/bootstrap-colorpicker.min.js"></script>

    <!-- Form Validation -->
    <script type="text/javascript" src="<?=base_url().'assets/'?>assets/jquery-validation/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?=base_url().'assets/'?>assets/jquery-validation/additional-methods.min.js"></script>

    <!--NodeJS-->
    <script src="http://<?=$_SERVER['SERVER_NAME']?>:3010/socket.io/socket.io.js"></script>

    <script>
        var hostname = '<?=$this->config->item('hostname')?>';
        var user_login;
        var user_detail = JSON.stringify({parameter:"admin", id:'<?=$this->user?>', user:'<?=strtoupper($account['nama'])?>'});
        var socket;
        socket = io.connect("http://<?=$_SERVER['SERVER_NAME']?>:3010");
        socket.emit('login', user_detail);

        socket.on('new message', function(msg){
            //$.Notification.autoHideNotify('info', 'top right', 'New Message From '+msg.user, msg.message);
        });

        socket.on('user joined', function(msg){
            user_login = JSON.parse(msg.username);

            if (typeof(user_login) === 'object') {
                if (user_login.parameter === 'admin') {
                    //$.Notification.autoHideNotify('success', 'top right', user_login.user + ' is online.');
                } else {
                    if (typeof(user_login.kasir) != 'undefined' && user_login.kasir != " " && typeof(user_login.lokasi) != 'undefined' && user_login.lokasi != " ") {
                        //$.Notification.autoHideNotify('success', 'top right', 'Kasir ' + user_login.kasir + ' is online.', 'Lokasi ' + user_login.lokasi);
                    }
                }
            }
        });

        socket.on('user left', function(msg){
            user_login = JSON.parse(msg.username);

            if (typeof(user_login) === 'object') {
                if (user_login.parameter === 'admin') {
                    //$.Notification.autoHideNotify('error', 'top right', user_login.user + ' is offline.');
                } else {
                    if (typeof(user_login.kasir) != 'undefined' || user_login.kasir != " " || typeof(user_login.lokasi) != 'undefined' || user_login.lokasi != " ") {
                        //$.Notification.autoHideNotify('error', 'top right', 'Kasir ' + user_login.kasir + ' is offline.', 'Lokasi ' + user_login.lokasi);
                    }
                }
            }
        });
    </script>

	<noscript>
		 <meta HTTP-EQUIV="REFRESH" content="0; url=<?=base_url().'site/nojs'?>" /> 
	</noscript>

	<style>
		.daterange { position: relative; text-align: center }
		.daterange i {
			position: absolute; bottom: 10px; right: 24px; top: auto; cursor: pointer;
		}

		.width-uang {
			width: 95px;
			text-align: right;
		}

		.width-diskon {
			width: 50px;
			text-align: center;
		}

		input[type=number]::-webkit-inner-spin-button,
		input[type=number]::-webkit-outer-spin-button {
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;
			margin: 0;
		}

		input[type=number] {
			-moz-appearance:textfield;
		}

		.table_check {
			border-collapse:collapse;
		}
		.td_check {
			padding: -8px -8px -8px -8px;
		}
		.label_check {
			display:block;
			margin: -8px;
			padding: 8px 8px 8px 8px;
		}

		.datepicker table tr.week:hover{
			background: #eee;
		}

		.datepicker table tr.week-active,
		.datepicker table tr.week-active td,
		.datepicker table tr.week-active td:hover,
		.datepicker table tr.week-active.week td,
		.datepicker table tr.week-active.week td:hover,
		.datepicker table tr.week-active.week,
		.datepicker table tr.week-active:hover{
			background-color: #006dcc;
			background-image: -moz-linear-gradient(top, #0088cc, #0044cc);
			background-image: -ms-linear-gradient(top, #0088cc, #0044cc);
			background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));
			background-image: -webkit-linear-gradient(top, #0088cc, #0044cc);
			background-image: -o-linear-gradient(top, #0088cc, #0044cc);
			background-image: linear-gradient(top, #0088cc, #0044cc);
			background-repeat: repeat-x;
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#0088cc', endColorstr='#0044cc', GradientType=0);
			border-color: #0044cc #0044cc #002a80;
			border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
			filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
			color: #fff;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
		}
		.label_colom {
			position:  absolute;
			left: 0;
			top: 0; /* set these so Chrome doesn't return 'auto' from getComputedStyle */
			background: transparent;
			border: 0px  solid rgba(0,0,0,0.5);
		}


        /* Absolute Center Spinner */
        .loading {
            position: fixed;
            z-index: 999;
            height: 2em;
            width: 2em;
            overflow: show;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }

        /* Transparent Overlay */
        .loading:before {
            content: '';
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3);
        }

        /* :not(:required) hides these rules from IE9 and below */
        .loading:not(:required) {
            /* hide "loading..." text */
            font: 0/0 a;
            color: transparent;
            text-shadow: none;
            background-color: transparent;
            border: 0;
        }

        .loading:not(:required):after {
            content: '';
            display: block;
            font-size: 10px;
            width: 1em;
            height: 1em;
            margin-top: -0.5em;
            -webkit-animation: spinner 1500ms infinite linear;
            -moz-animation: spinner 1500ms infinite linear;
            -ms-animation: spinner 1500ms infinite linear;
            -o-animation: spinner 1500ms infinite linear;
            animation: spinner 1500ms infinite linear;
            border-radius: 0.5em;
            -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
            box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        }

        /* Animation */

        @-webkit-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @-moz-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @-o-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @media print {
            #Header, #Footer { display: none !important; }
        }

        ul.tab-buttons {
            margin-bottom: 30px;
            margin-top: 20px;
            padding: 0;
        }
        /* For mobile phones: */
        ul.tab-buttons li {
            background: #ff2b42 none repeat scroll 0 0;
            box-shadow: 0 11px 10px 0 rgba(0, 0, 0, 0.1);
            display: inline-block;
            height: 130px;
            transition: all 0.4s ease 0s;
            width: 100%;
        }
        @media only screen and (min-width: 600px) {
            /* For tablets: */
            ul.tab-buttons li {
                background: #ff2b42 none repeat scroll 0 0;
                box-shadow: 0 11px 10px 0 rgba(0, 0, 0, 0.1);
                display: inline-block;
                height: 130px;
                transition: all 0.4s ease 0s;
                width: 49.6%;
            }
        }
        @media only screen and (min-width: 768px) {
            /* For desktop: */
            ul.tab-buttons li {
                background: #ff2b42 none repeat scroll 0 0;
                box-shadow: 0 11px 10px 0 rgba(0, 0, 0, 0.1);
                display: inline-block;
                height: 130px;
                transition: all 0.4s ease 0s;
                width: 16.35%;
            }
        }

        .font-status {
            font-weight: bold;
            font-size: 18pt;
        }

        ul.tab-buttons li.selected {
            background: #ff2b42 none repeat scroll 0 0;
            transition: all 0.4s ease 0s;
        }
        ul.tab-buttons li.selected:hover { background: #FD4559 }
        /*END TAB MENU ONE*/
        ul.tab-buttons li.selected2 {
            background: #1cbac8;
            -webkit-transition: all 0.4s ease 0s;
            transition: all 0.4s ease 0s;
        }
        ul.tab-buttons li.selected2:hover { background: #2AC7D5 }
        /*END TAB MENU TWO*/
        ul.tab-buttons li.selected3 {
            background: #00cccc;
            -webkit-transition: all 0.4s ease 0s;
            transition: all 0.4s ease 0s;
        }
        ul.tab-buttons li.selected3:hover { background: #00E0E0 }
        /*END TAB MENU THREE*/
        ul.tab-buttons li.selected4 {
            background: #21bb9d;
            -webkit-transition: all 0.4s ease 0s;
            transition: all 0.4s ease 0s;
        }
        ul.tab-buttons li.selected4:hover { background: #30CCAE }
        /*END TAB MENU FOUR*/
        ul.tab-buttons li.selected5 {
            background: #09afdf;
            -webkit-transition: all 0.4s ease 0s;
            transition: all 0.4s ease 0s;
        }
        ul.tab-buttons li.selected5:hover { background: #20C6F6 }
        /*END TAB MENU FIVE*/
        ul.tab-buttons li.selected6 {
            background: #F7AD17 none repeat scroll 0 0;
            margin-right: 0;
            transition: all 0.4s ease 0s;
        }
        ul.tab-buttons li.selected6:hover { background: #FFBC34 }
        /*END TAB MENU SIX*/
        /*END TAB MENU*/
        /*START TAB CONTAINER*/
        .tab-container {
            background: #fff none repeat scroll 0 0;
            border: 1px solid #e8e8e9;
            box-shadow: 0 11px 10px 0 rgba(0, 0, 0, 0.1);
            height: 100%;
            margin-bottom: 30px;
            padding: 20px;
        }
        .tab-container > div { display: none }
        .tab-buttons li span {
            display: block;
            color: #fff;
            font-family: "Berkshire Swash",sans-serif;
        }
        .tab-buttons li a i {
            color: #fff;
            display: block;
            font-size: 36px;
            margin: 30px auto 5px;
        }
        .tab-buttons li a {
            display: block;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
        }
        .tab-buttons li a:hover { color: #fff }

        /*Scrollbar*/
        .scrollbar
        {
            width: 100%;
            height: 100%;
            overflow-y: scroll;
            overflow-x: hidden;
        }
        .scrollbar::-webkit-scrollbar-track
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            border-radius: 0px;
            background-color: #F5F5F5;
        }

        .scrollbar::-webkit-scrollbar
        {
            width: 0px;
            background-color: #F5F5F5;
        }

        .scrollbar::-webkit-scrollbar-thumb
        {
            border-radius: 0px;
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: rgba(0, 151, 167, 1);
        }

        .first-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1050;
            background: rgba(168, 168, 168, .5)
        }
        .first-loader img {
            position: absolute;
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px
        }
	</style>

	<script type="text/javascript" src="<?=base_url().'assets/'?>assets/barcode/jquery-barcode.js"></script>
	<script type="text/javascript">
		function generateBarcode(barcode=''){
			if(typeof barcode['divbarcode'] !== 'undefined'){ var divbarcode = barcode['divbarcode']; } else { var divbarcode = 'divbarcode'; }
			if(typeof barcode['canvasbarcode'] !== 'undefined'){ var canvasbarcode = barcode['canvasbarcode']; } else { var canvasbarcode = 'canvasbarcode'; }
			if(typeof barcode['value'] !== 'undefined'){ var value = barcode['value']; } else { var value = '12345678'; }
			if(typeof barcode['btype'] !== 'undefined'){ var btype = barcode['btype']; } else { var btype = 'code128'; }
			if(typeof barcode['renderer'] !== 'undefined'){ var renderer = barcode['renderer']; } else { var renderer = 'css'; }
			if(typeof barcode['width'] !== 'undefined'){ var width = barcode['width']; } else { var width = 1; }
			if(typeof barcode['height'] !== 'undefined'){ var height = barcode['height']; } else { var height = 40; }
			if(typeof barcode['bcolor'] !== 'undefined'){ var bcolor = barcode['bcolor']; } else { var bcolor = 'transparant'; }
			if(typeof barcode['fcolor'] !== 'undefined'){ var fcolor = barcode['fcolor']; } else { var fcolor = '#000000'; }
			if(typeof barcode['rectangular'] !== 'undefined'){ var rectangular = barcode['rectangular']; } else { var rectangular = false; }
			if(typeof barcode['quietZone'] !== 'undefined'){ var quietZone = barcode['quietZone']; } else { var quietZone = false; }
			if(typeof barcode['posx'] !== 'undefined'){ var posx = barcode['posx']; } else { var posx = 10; }
			if(typeof barcode['posy'] !== 'undefined'){ var posy = barcode['posy']; } else { var posy = 20; }
			if(typeof barcode['addQuietZone'] !== 'undefined'){ var addQuietZone = barcode['addQuietZone']; } else { var addQuietZone = 1; }
			if(typeof barcode['showHRI'] !== 'undefined'){ var showHRI = barcode['showHRI']; } else { var showHRI = true; }
			if(typeof barcode['marginHRI'] !== 'undefined'){ var marginHRI = barcode['marginHRI']; } else { var marginHRI = 5; }
			if(typeof barcode['fontSize'] !== 'undefined'){ var fontSize = barcode['fontSize']; } else { var fontSize = 8; }
			
			var settings = {
				output: renderer,
				bgColor: bcolor,
				color: fcolor,
				barWidth: width,
				barHeight: height,
				moduleSize: 5,
				showHRI: showHRI,
				marginHRI: marginHRI,
				fontSize: fontSize,
				posX: posx,
				posY: posy,
				addQuietZone: addQuietZone
			};
			if (rectangular==true){
				value = {code:value, rect: true};
			}
			if (renderer == 'canvas'){
				clearCanvas(canvasbarcode);
				$("#"+divbarcode).hide();
				$("#"+canvasbarcode).show().barcode(value, btype, settings);
			} else {
				$("#"+canvasbarcode).hide();
				$("#"+divbarcode).html("").show().barcode(value, btype, settings);
			}
		}
		  
		function clearCanvas(id){
			var canvas = $('#'+id).get(0);
			var ctx = canvas.getContext('2d');
			ctx.lineWidth = 1;
			ctx.lineCap = 'butt';
			ctx.fillStyle = '#FFFFFF';
			ctx.strokeStyle  = '#000000';
			ctx.clearRect (0, 0, canvas.width, canvas.height);
			ctx.strokeRect (0, 0, canvas.width, canvas.height);
		}

        function set_ckeditor(id){
            $(function () {
                // Replace the <textarea id="editor1"> with a CKEditor
                // instance, using default configuration.
                CKEDITOR.replace(id, {
                    toolbar: [
                        //{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
                        //{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
                        //'/',
                        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript' ] },
                        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
                        { name: 'links', items: [ 'Link', 'Unlink' ] },
                        { name: 'insert', items: [ 'Image', 'Table' ] },
                        '/',
                        { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                        { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                        { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
                        { name: 'others', items: [ '-' ] },
                        { name: 'about', items: [ 'About' ] }
                    ]
                });
                //bootstrap WYSIHTML5 - text editor
                //$(".textarea").wysihtml5();
            });

            $.fn.modal.Constructor.prototype.enforceFocus = function() {
                modal_this = this
                $(document).on('focusin.modal', function (e) {
                    if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
                        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select')
                        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
                        modal_this.$element.focus()
                    }
                })
            };
        }
	</script>
	
	<?php if($this->session->userdata($this->site . 'isLogin')==false){ redirect(base_url()); }
	?>
	
