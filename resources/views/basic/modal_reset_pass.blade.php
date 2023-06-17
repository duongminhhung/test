<div class="modal fade" id="modalResetPass" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  	<div class="modal-dialog" role="document">
    	<div class="modal-content">
      	<div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{__('Reset')}} {{__('Password')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      	</div>
	      	<form method="post" action="{{ route('account.resetPassword') }}">
	      		@csrf
		      	<div class="modal-body">
			      	<input type="text" name="Password" class="form-control" placeholder="{{__('Enter')}} {{__('Password')}}" required>
			      	<input type="text" name="idUser" class="form-control hide" id="idReset" required>
		      	</div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
		        	<button type="submit" class="btn btn-success">{{__('Save')}}</button>
		      	</div>
	      	</form>
    	</div>
  	</div>
</div>