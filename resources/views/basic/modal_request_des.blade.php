<div class="modal fade" id="modalRequestDes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">{{ __('Destroy') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ $route }}" method="get">
                @csrf
                <div class="modal-body">
                    <strong style="font-size: 23px">{{ __('Do You Want To Destroy') }} <span id="nameDes"
                            style="color: blue"></span> ?</strong>
                    <input type="text" name="ID" id="idDes" class="form-control  d-none">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Destroy') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
