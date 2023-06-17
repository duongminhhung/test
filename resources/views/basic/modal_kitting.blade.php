<div class="modal fade" id="modalKitting" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{__('Kitting')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{$route}}" method="post">
        @csrf
        <div class="modal-body">
          <div class="form-group col-md-12">
            <label for="symbolsPlan">{{__('Name')}} {{__('Delivery Note')}}</label>
            <input type="text" value="{{ old('Kitting_Name') ? old('Kitting_Name') : ''  }}" class="form-control" id="kittingName" name="Kitting_Name" placeholder="{{__('Enter')}} {{__('Name')}}">
            @if($errors->any())
              <span role="alert" class="errr-kit">
                <strong style="color: red">
                  {{$errors->first('Kitting_Name')}}
                </strong>
              </span>
            @endif
          </div>
          <input type="text" class="form-control hide" id="planlID" value="{{isset($_GET['ID']) ? $_GET['ID'] : (isset($_GET['Plan_ID']) ? $_GET['Plan_ID'] : 0 ) }}" name="Plan_ID">
          <input type="text" class="form-control hide" id="productID" value="{{isset($_GET['Product_ID']) ? $_GET['Product_ID'] : '' }}" name="Product_ID">
          <input type="text" class="form-control hide" id="machineID" value="{{isset($_GET['Machine_ID']) ? $_GET['Machine_ID'] : '' }}" name="Machine_ID">
          <input type="text" class="form-control hide" id="productShift" value="{{isset($_GET['Product_Shift']) ? $_GET['Product_Shift'] : '' }}" name="Product_Shift">
          <input type="text" class="form-control hide" id="face" value="{{isset($_GET['Face']) ? $_GET['Face'] : '' }}" name="Face">
          <input type="text" class="form-control hide" id="planDetailID" value="{{ old('Plan_Detail_ID') ? old('Plan_Detail_ID') : ''  }}" name="Plan_Detail_ID">
          <input type="text" class="form-control hide" id="fromKitting" value="{{ old('From_Kitting') ? old('From_Kitting') : ''  }}" name="From_Kitting">
          <input type="text" class="form-control hide" id="toKitting" value="{{ old('To_Kitting') ? old('To_Kitting') : ''  }}" name="To_Kitting">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
          <button type="submit" class="btn btn-primary btn-save-kitting">{{__('Save')}}</button>
        </div>
      </form>
    </div>
  </div>
</div>

