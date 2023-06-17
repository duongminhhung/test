<div class="modal fade" id="modalDetailWarehouse" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-detail" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{__('Detail')}} {{__('Location')}} : <span id="nameWarehouse"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <span role="alert" class="hide loading">
          <strong style="color: red">{{__('Loading')}}...</strong>
        </span>
        <br>
        <table class="table table-bordred table-hover">
        </table>
        <div class="row">
          <div class="form-group col-md-4 hide">
            <label for="idWarehouse">{{__('ID')}}</label>
            <input type="text" value="" class="form-control" id="idWarehouse" name="ID" readonly>
          </div>

          <div class="form-group col-md-4">
            <label for="groupMaterialsWare">
              {{__('Group Materials')}}
            </label>
            <select id="groupMaterialsWare" name="Group_Materials_ID" class="form-control select2">
              <option value="">
                {{__('Choose')}} {{__('Group Materials')}}
              </option>
              @foreach($groups as $value)
              <option value="{{$value->ID}}">
                {{$value->Symbols}}
              </option>
              @endforeach
            </select>
            <span role="alert" class="hide error-group">
              <strong style="color: red" class="error-group"></strong>
            </span>
          </div>

          <!-- <div class="form-group col-md-4">
            <label for="mac">{{__('MAC')}}</label>
            <input type="text" value="" class="form-control" id="mac" name="MAC" placeholder="{{__('Enter')}} {{__('MAC')}}">
          </div>

          <div class="form-group col-md-4">
            <label for="positionLed">{{__('Position')}} {{__('Led')}}</label>
            <input type="number" min="0" value="" class="form-control" id="positionLed" name="Position_Led" placeholder="{{__('Enter')}} {{__('Position')}} {{__('Led')}}">
            <span role="alert" class="hide error-position-led">
              <strong style="color: red" class="error-position-led"></strong>
            </span>
          </div> -->

          <!-- <div class="form-group col-md-3">
            <label for="quantityUnit">{{__('Quantity Unit')}}</label>
            <input type="number" min="1" step="0.0000001" value="" class="form-control" id="quantityUnit" name="Quantity_Unit" placeholder="{{__('Enter')}} {{__('Quantity Unit')}}">
            <span role="alert" class="hide error-quantity-unit">
              <strong style="color: red" class="error-quantity-unit"></strong>
            </span>
          </div>

          <div class="form-group col-md-3">
            <label for="unitWare">{{__('Unit')}}</label>
            <select id="unitID" name="Unit_ID" class="form-control select2">
              <option value="">
                {{__('Choose')}} {{__('Unit')}}
              </option>
              @foreach($units as $value)
              <option value="{{$value->ID}}">
                {{$value->Symbols}}
              </option>
              @endforeach
            </select>
            <span role="alert" class="hide error-unit">
              <strong style="color: red" class="error-unit"></strong>
            </span>
          </div> -->
          <div class="form-group col-md-4">
            <label for="quantityPacking">{{__('Quantity Packing')}}</label>
            <input type="number" min="1" step="0.0000001" value="" class="form-control" id="quantityPacking" name="Quantity_Packing" placeholder="{{__('Enter')}} {{__('Quantity Packing')}}">
            <span role="alert" class="hide error-quantity-packing">
              <strong style="color: red" class="error-quantity-packing"></strong>
            </span>
          </div>
          <!-- <div class="form-group col-md-3">
            <label for="note">{{__('Quantity')}} {{__('Min')}}</label>
            <input type="number" value="0" class="form-control" id="minUnit" name="Min_Unit" placeholder="{{__('Enter')}} {{__('Quantity')}} {{__('Min')}}">
          </div> -->
          <div class="form-group col-md-4">
            <label for="Accept">{{__('Accept')}}</label>
            <select id="Accept" name="Accept" class="form-control select2">
              <option value="0">
                {{__('Accept')}}
              </option>
              <option value="1">
                {{__('Dont')}} {{__('Accept')}}
              </option>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label for="Email">{{__('Email')}}</label>
            <input type="text"  class="form-control" id="Email" name="Email" placeholder="{{__('Enter')}} {{__('Email')}}">
          </div>
          <div class="form-group col-md-3">
            <label for="Email2">{{__('Email')}}2</label>
            <input type="text"  class="form-control" id="Email2" name="Email2" placeholder="{{__('Enter')}} {{__('Email')}} 2">
          </div>
          @if(false)
          <div class="form-group col-md-8">
              <label for="note">{{__('Note')}}</label>
              <input type="text" value="" class="form-control" id="note" name="Note" placeholder="{{__('Enter')}} {{__('Note')}}">
          </div>
          @endif
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
        <button type="button" class="btn btn-primary btn-save-detail">{{__('Save')}}</button>
      </div>
    </div>
  </div>
</div>