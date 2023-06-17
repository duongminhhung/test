<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{__('Import By File Excel')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{$route}}" method="post" name="fileImport" enctype="multipart/form-data">
          @csrf
          <input type="text" class="form-control d-none" name="Product_ID" id="product_id">
          <div class="form-group">
            <label for="importFile"></label>
            <div class="input-group">
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="importFile" name="fileImport" required>
                <label class="custom-file-label input-text" for="importFile">{{__('Choose')}} {{__('File')}}</label>
              </div>
            </div>
            <span role="alert" class="d-none error-file">
              <strong style="color: red"><i class="fas fa-times-circle"></i> {{__('Choose')}} {{__('File')}} {{__('.Xlsx')}} {{__('Or')}} {{__('.Xls')}}</strong>
            </span>
          </div>
          {{-- <div class="form-group">
            <label f>{{__('Note')}}</label>
            <br>
              <div class="custom-file">
                <textarea type="file" class="form-control"  name="Note" > </textarea>
              </div>
            </div> --}}
          <button type="submit" class="btn btn-success d-none btn-submit-file">{{__('Submit')}}</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
        <button type="button" class="btn btn-primary btn-save-file">{{__('Save')}}</button>
      </div>
    </div>
  </div>
</div>