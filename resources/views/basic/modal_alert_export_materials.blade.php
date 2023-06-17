<div class="modal fade" id="modalRequestExport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">{{ __('Export') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                    <strong style="font-size: 23px">{{ __('Do You Want To Export') }} <span id="nameExport"
                            style="color: blue"></span> ?</strong>
                    <input type="text" name="ID" id="idExport" class="form-control d-none">
                    <input type="text" name="idCommand" id="idCommand" class="form-control d-none">
                </div>
                {{-- <div class="modal-quantity">
                    <input type="number" name="Quantity_Export" id="Quantity_Export" class="form-control">
                </div> --}}
                <div class="form-group col-12 quantity_export_change">
                    
                </div>
                <div class="erorr">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-success confirm">{{ __('Export') }}</button>
                </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        var user_id = {{ Auth::user()->id }};
        var id_command = {{ $request->ID }};

        function export_data_click() {
            return $.ajax({
                method: 'get',
                url: `{{ route('warehouse_management.export.export_data') }}`,
                data: dataPost,
                dataType: 'json',
            })
        }
        $('.confirm').on('click', function() {
            $('.loading').show();
            // console.log(type_action)
            dataPost = {
                'Command_ID'            : id_command,
                'ID'                    : $('#idExport').val(),
                'Quantity_Export_Change': $('#Quantity_Export_Change').val(),
                'user_id'               : user_id,
                'Array_ID'              : array_id_check
            };
            

            export_data_click(dataPost).done(function(data) {
                console.log(dataPost)
                if (data.status) {
                    $('.loading').hide();
                    $(this).show();
                    $('.close-modal').click()
                    table.ajax.reload()
                } else {
                    $('.loading').hide();
                    $('.btn-success').show();
                    let a = ``;
                    jQuery.each(data.data, function(k, v) {
                        a = a + `<p style="color:red">` + v + `</p>`
                    })
                    $('.erorr').append(`
                    <div class="div-er">
                    ` + a + `
                    </div>
                `)
                }
            }).fail(function(err) {

            })
        })
    </script>
@endpush
