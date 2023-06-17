<div class="modal fade" id="modalRequestDetail" style="display: none;" aria-hidden="true">
  <div class="modal-dialog" style="min-width: 75%">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{__('Detail')}} <span id="nameDetail"></span></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body" >
        <span role="alert" class="hide loading">
          <strong style="color: red">{{__('Loading')}}...</strong>
        </span>
        <div class="product-name" style="text-align:center ; font-size: 25px">
        </div>
        <br>
        <div class="row">
          <div class="col-md-6">
            <label>{{__('Detail')}}</label>
              <table class="table table-hover table-bordered table-detail" id="tableDetail" width="50%">
                <thead>
                  <th>{{__('Materials')}}</th>
                  <th>{{__('Quantity')}}(kg)</th>
                  <th>{{__('Pallet_ID')}}</th>
                  <th>{{__('Box ID')}}</th>
                  <th>{{__('Roll Number')}}</th>
                </thead>
                <tbody>
                </tbody>
              </table>
          </div>
          <div class="col-md-6">
            <label>{{__('History')}} {{__('Location')}}</label>
              <table class="table table-hover table-bordered table-history" id="tableHistory" width="50%">
                <thead>
                  <th>{{__('ID')}}</th>
                  <th>{{__('Materials')}}</th>
                  <th>{{__('Quantity')}}(kg)</th>
                  <th>{{__('Pallet')}}</th>
                  <th>{{__('Roll Number')}}</th>
                  <th>{{__('Box ID')}}</th>
                  <th>{{__('Type')}}</th>
                </thead>
                <tbody>
                </tbody>
              </table>
          </div>
         </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
      </div>
    </div>
  </div>
</div>
