let data;
        let id = 0;

        function get_materials()
        {
            return $.ajax({
                method  : 'get',
                url     : '{{ route("printLabel.getMaterials") }}',
                data    : {},
                dataType: 'json'
            });
        }

        function create_label(dataCreate)
        {
            return $.ajax({
                method  : 'post',
                url     : '{{ route("printLabel.create") }}',
                data    : dataCreate,
                dataType: 'json',
                async   : true,
            });
        }

        function printLabel(count, hosiden, maker, lot)
        {
            $('.hide').hide();
            let date = moment().format('YYYY-MM-DD HH:mm:ss');
            let id   = 0;
            dataCreate = {
                '_token'      : $('meta[name="csrf-token"]').attr('content'),
                'Part_ID'     : id.toString().padStart(9, "0"),
                'Materials_ID': $('#idMaterials').val(),
                'Lot_Number'  : $('#lotNumber').val(),
                'Quantity'    : $('#quantity').val()
            }
            create_label(dataCreate).done(function(dat)
            {
                id = dat.data.ID;

                let id_text = id.toString().padStart(9, "0");

                $('#symbols-hosiden').text(hosiden);
                $('#model-maker').text(maker);
                $('#count').text(count);
                $('#time').text(date);
                $('#code-lot').text(lot);
                $('#symbols-sti').text(id_text);

                let width = 1;

                if (maker.length <= 18 && hosiden.length <= 18) 
                {
                    width = 2;
                }
                let option = {
                    width       : width,
                    height      : 40,
                    displayValue: false,
                    marginLeft  : 0,
                    marginTop   : 0,
                    marginBottom: 0,
                    marginRight : 0
                };

                let option1 = {
                    width       : 1,
                    height      : 40,
                    displayValue: false,
                    marginLeft  : 0,
                    marginTop   : 0,
                    marginBottom: 0,
                    marginRight : 0
                }

                JsBarcode("#code_hosiden", hosiden,option1);

                JsBarcode("#code_maker", maker,option);

                JsBarcode("#code_sti", id_text,option1);

                JsBarcode("#lot_number", lot,option);

                $('#tableLabel').show();
                // $('.head').hide();
                window.print();
            }).fail(function(err){});
        }

        window.onafterprint = function() {
           // console.log("Printing completed...");
            $('.head').show();
            // $('#tableLabel').hide();
            $('#lotNumber').val('');
            $('#idMaterials').val('');
            $('#maker').click();  
        }

        window.onbeforeprint = function()
        {
            $('.head').hide();
           // console.log("Before print...");
           // $('#maker').click();          
        }

        function check()
        {
            let count      = $('#quantity').val();
            let hosiden    = $('#materials').val();
            let maker      = $('#maker').val();
            let lot_number = $('#lotNumber').val();
            // console.log(count,hosiden,maker,lot_number)

            if (count == '' || count <= 0 || hosiden == '' || maker == '' || lot_number == '') 
            {
                if (count == '' || count <= 0) 
                {
                    $('.error-quantity').show();
                } else
                {
                    $('.error-quantity').hide();
                }

                if (hosiden == '') 
                {
                    $('.error-materials').show();
                } else
                {
                    $('.error-materials').hide();
                }

                if (maker == '') 
                {
                    $('.error-maker').show();
                } else
                {
                    $('.error-maker').hide();
                }

                if (lot_number == '') 
                {
                    $('.error-lot-number').show();
                } else
                {
                    $('.error-lot-number').hide();
                }

                return [];
            } else
            {
                return [count, hosiden, maker, lot_number];
            }

        }

        $("#maker").on("click", function () 
        {
           $(this).select();
        });

        get_materials().done(function(dat)
        {
            data = dat.data;
        }).fail(function(err)
        {
            console.log(err);
        });

        let regime = $('#regime').val();

        if (regime == '0') 
        {
            $('#quantity').attr('readonly', true);
            $('#materials').attr('readonly', true);
            $('.btn-print').hide();
        } else
        {
            $('#quantity').removeAttr('readonly');
            $('#materials').removeAttr('readonly');
            $('.btn-print').show();
        }

        $('#regime').on('change', function()
        {
            regime = $('#regime').val();
            $('#quantity').val(0);
            $('#materials').val('');
            $('#maker').val('');
            $('#lotNumber').val('');
            $('#idMaterials').val('')

            if (regime == '0') 
            {
                $('#quantity').attr('readonly', true);
                $('#materials').attr('readonly', true);
                $('.btn-print').hide();
            } else
            {
                $('#quantity').removeAttr('readonly');
                $('#materials').removeAttr('readonly');
                $('.btn-print').show();
            }
        });

        $('.btn-print').on('click', function()
        {
            dat = check();
            // console.log(dat)

            if (dat.length == 4) 
            {
                printLabel(dat[0], dat[1], dat[2], dat[3]);
            }
        });

        $('#quantity').on('keyup', function(e)
        {
            if (e.which == 13) 
            {
                dat = check();
                if (dat) 
                {
                    printLabel(dat[0], dat[1], dat[2], dat[3]);
                }
            }
        });

        let time;

        $('#materials').on('keyup', function(e)
        {
            clearTimeout(time);
            $('#quantity').val(0);
            $('#idMaterials').val('');           

            time = setTimeout(function()
            {
                let val = $('#materials').val();
                $('#maker').val('')

                for(let dat of data)
                {
                    if (dat.Symbols == val ) 
                    {
                        $('#maker').val(dat.Model);
                        $('#idMaterials').val(dat.ID);
                    }
                }
            }, 230)
        });

        $('#maker').on('keyup', function(e)
        {
            clearTimeout(time);
            $('#idMaterials').val('');
            $('#quantity').val(0);

            time = setTimeout(function()
            {
                let val = $('#maker').val();
                $('#materials').val('')
                $('#quantity').val(0)

                for(let dat of data)
                {
                    if (dat.Model == val )
                    {
                        $('#materials').val(dat.Symbols);
                        $('#idMaterials').val(dat.ID);

                        if (regime == 0) 
                        {
                            $('#quantity').val(dat.Standard_Packing);
                            $('#lotNumber').select();
                            $('.btn-print').click();
                        }
                    }
                }
            }, 230)
        });

        $('#lotNumber').on('keyup', function(e)
        {

            let val = $('#lotNumber').val();

            if (e.which == 13) 
            {
                $('.btn-print').click()
            }

        });

        setInterval(function()
        {
            get_materials().done(function(dat)
            {
                data = dat.data;

            }).fail(function(err)
            {
                console.log(err);
            });
        }, 30000);