<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Label</title>
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	  <link rel="icon" type="image/png" sizes="180x180" href="{{asset('dist/img/sti.png')}}">
	  <!-- Sweet Alert 2 -->
	  <link rel="stylesheet" href="{{ asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
	  <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
	  <!-- Bootstrap-toggle -->
	  <link rel="stylesheet" href="{{ asset('dist/bootstrap-toggle/bootstrap-toggle.min.css') }}">
	  <!-- I-check -->
	  <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}" media="all">
	  <!-- Font Awesome -->
	  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
	  <!-- Color -->
	  <link rel="stylesheet" href="{{ asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
	  <!-- Select2 -->
	  <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
	  <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
	  
	  <!-- Ionicons -->
	  <link rel="stylesheet" href="{{ asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">
	  
	  <link rel="stylesheet" href="{{ asset('css/multi-select.css') }}">
	  <!-- <link rel="stylesheet" href=""> -->
	  <!-- dataTable https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css-->
	  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
	  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	  <!-- font awesome  -->
	  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
	  <!-- Daterange picker -->
	  <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">

	  <link rel="stylesheet" href="{{ asset('css/ionicons.min.css') }}">

	  <!-- Theme style -->
	  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

	  <style>
	    table > tbody > tr > th,td {
	      text-align: center;
	      vertical-align: middle;
	    }
	    table > thead > tr > th {
	      text-align: center;
	      vertical-align: middle;
	    }
	    .hide {
	      display: none;
	    }

		@media print
	    {
	        @page {
	            size   : 70mm 48mm;
	            margin : 0;
	        }

	        html, body {
	        	height: 99%;
	        	/*width: 100%;*/
	        }

	        #tableLabel {
	            margin-left: 0px !important;
	            /*margin-top : -42px !important;*/
	            width      : 395px !important;
	            page-break-inside: avoid;
	            /*page-break-after: inherit !important;*/
	            /*border-radius: 2px;*/
	        }

	        #code_hosiden, #lot_number, #code_sti, #code_maker {
	        	margin-top: 2px !important;
	        	margin-bottom: 1px !important;
	        }
	    }
	</style>
</head>
<body>
	<div id="symbols-hosiden" style="display: none">
    	{{
    		$data->label ?
    		(
    			$data->label->materials ? 
    			$data->label->materials->Symbols
    			: ''
    		) : ''
    	}}
    </div>
	<div id="code-lot" style="display: none">
    	{{
    		$data->label ?
    		$data->label->Lot_Number
    		: ''
    	}}
    </div>
	<div id="symbols-sti" style="display: none">
    	{{
    		$data->label ?
    		$data->label->ID
    		: ''
    	}}
    </div>
	<div id="model-maker" style="display: none">
    	{{
    		$data->label ?
    		(
    			$data->label->materials ? 
    			$data->label->materials->Model
    			: ''
            ) : ''
    	}}
    </div>
    <table class="" id="tableLabel" style="width: 450px; border: 2px solid black; margin: 0 auto">
        <tbody>
            <tr style="border: 2px solid black">
                <th colspan="3" style="padding: 0;">
                    <div>
                        <div class="hosiden"style="font-size: 20px; width: 30%; float: left; margin-top: 4%;">
                            HOSIDEN
                        </div>
                        <div class="code-hos" style="border-left: 2px solid black; float: left;">
                            <svg id="code_hosiden"></svg>
                        </div>
                    </div>
                </th>
            </tr>
            <tr>
                <th colspan="3" style="padding: 0;">
                    <div style="border-bottom: 2px solid black; float: left;  width: 100%">
                        <svg id="code_sti"></svg>
                    </div>
                </th>
            </tr>
            <tr>
                <th colspan="3" style="padding: 0;">
                    <div>
                        <div class="" style="float: left; width: 100%">
                            <svg id="lot_number"></svg>
                        </div>
                    </div>
                </th>
            </tr>
            <tr style="border-top: 2px solid black">
                <th colspan="3" style="padding: 0;">
                    <svg id="code_maker"></svg>
                </th>
            </tr>
            <tr style="border: 2px solid black">
                <th colspan="3" style="padding: 0">
                    <div class="" style="font-size: 12px">
                        <div class="count" style="border-right: 2px solid black; float: left; width: 25%; margin-left: 2px">
                            <span class="count">Quantity : </span> <span id="count" class="count">
                            	{{
		                    		floatval($data->Quantity)
		                    	}}
                            </span>
                        </div>
                        <div class="time" style="border-right: 2px solid black; float: left; width: 43%; text-align: center; margin-left: 2px">
                            <span class="time">Time : </span> <span id="time" class="time">
                            	{{
		                    		$data->Time_Created
		                    	}}
                            </span>
                        </div>
                        <div class=" user"  style="float: left; width: 30%; text-align: center; margin-left: 2px">
                            <span class="user">User : </span> <span class="user">
                            	{{
                            		$data->user_created ?
                            		$data->user_created->username : ''
                            	}}
                            </span>
                        </div>
                    </div>
                </th>
            </tr>
        </tbody>
    </table>
</body>
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
<!-- Barcode -->
<script src="{{ asset('dist/js/JsBarcode.all.min.js') }}"></script>
<script src="{{ asset('plugins/qrcode/qrcode.min.js') }}"></script>
<script>
	let hosiden = $('#symbols-hosiden').text().trim();
	let maker   = $('#model-maker').text().trim();
	let lot     = $('#code-lot').text().trim();
	let id_text = $('#symbols-sti').text().trim().toString().padStart(9, "0");

    let widthM = widthH = widthL = widthI = 1.3;

    if (hosiden == '') 
    {
    	hosiden = 'null';
    }
    if (maker == '') 
    {
    	maker = 'null';
    }
    if (lot == '') 
    {
    	lot = 'null';
    }
    if (id_text == '') 
    {
    	id_text = 'null';
    }

    // if (maker.length <= 18) 
    // {
    //     widthM = 2;
    // }

    // if (hosiden.length <= 18) 
    // {
    //     widthH = 2;
    // }

    // if (lot.length <= 18) 
    // {
    //     widthL = 2;
    // }

    // if (id_text.length <= 18) 
    // {
    //     widthI = 2;
    // }

    JsBarcode("#code_hosiden", hosiden,{
        // format      : "CODE39",
        text           : 'Part Name:'+ hosiden,
        fontSize       : 13,
        width          : widthH,
        height         : 21,
        fontOptions    : "bold",
        // barWidth    : 00,
        // displayValue: false,
        marginLeft     : 0,
        marginTop      : 0,
        marginBottom   : 0,
        marginRight    : 0
    });

    JsBarcode("#code_maker", maker,{
        // format      : "CODE39",
        text           : 'Part Number:'+ maker,
        fontSize       : 13,
        width          : widthM,
        height         : 21,
        fontOptions    : "bold",
        // displayValue: false,
        marginLeft     : 0,
        marginTop      : 0,
        marginBottom   : 0,
        marginRight    : 0
    });

    JsBarcode("#code_sti", id_text,{
        // format      : "CODE39",
        text           : 'ID Label:'+ id_text,
        fontSize       : 12,
        width          : widthI,
        height         : 21,
        fontOptions    : "bold",
        // displayValue: false,
        marginLeft     : 0,
        marginTop      : 0,
        marginBottom   : 0,
        marginRight    : 0
    });

    JsBarcode("#lot_number", lot,{
        // format      : "CODE39",
        text           : 'Lot:'+ lot,
        fontSize       : 13,
        width          : widthL,
        height         : 21,
        fontOptions    : "bold",
        // displayValue: false,
        marginLeft     : 0,
        marginTop      : 0,
        marginBottom   : 0,
        marginRight    : 0
    });

    // JsBarcode("#code_hosiden", hosiden,{
    //     // format      : "CODE39",
    //     text           : 'Part Name : '+ hosiden,
    //     fontSize       : 13,
    //     width          : widthH,
    //     height         : 42,
    //     fontOptions    : "bold",
    //     // barWidth    : 00,
    //     // displayValue: false,
    //     marginLeft     : 0,
    //     marginTop      : 0,
    //     marginBottom   : 0,
    //     marginRight    : 0
    // });

    // JsBarcode("#code_maker", maker,{
    //     // format      : "CODE39",
    //     text           : 'Part Number : '+ maker,
    //     fontSize       : 13,
    //     width          : widthM,
    //     height         : 42,
    //     fontOptions    : "bold",
    //     // displayValue: false,
    //     marginLeft     : 0,
    //     marginTop      : 0,
    //     marginBottom   : 0,
    //     marginRight    : 0
    // });

    // JsBarcode("#code_sti", id_text,{
    //     // format      : "CODE39",
    //     text           : 'ID Label : '+ id_text,
    //     fontSize       : 12,
    //     width          : widthI,
    //     height         : 42,
    //     fontOptions    : "bold",
    //     // displayValue: false,
    //     marginLeft     : 0,
    //     marginTop      : 0,
    //     marginBottom   : 0,
    //     marginRight    : 0
    // });

    // JsBarcode("#lot_number", lot,{
    //     // format      : "CODE39",
    //     text           : 'Lot : '+ lot,
    //     fontSize       : 13,
    //     width          : widthL,
    //     height         : 42,
    //     fontOptions    : "bold",
    //     // displayValue: false,
    //     marginLeft     : 0,
    //     marginTop      : 0,
    //     marginBottom   : 0,
    //     marginRight    : 0
    // });

    // $('.head').hide();
    window.print();
</script>

</html>