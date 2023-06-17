@if(session()->has('success'))
	<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
	  	<strong>{{session()->get('success')}}</strong>
	  	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    	<span aria-hidden="true">&times;</span>
	  	</button>
	</div>
@endif
@if(session()->has('danger'))
	@if(session()->get('danger') != '')
	<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
	  	<strong>{{session()->get('danger')}}</strong>
	  	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    	<span aria-hidden="true">&times;</span>
	  	</button>
	</div>
	@endif
@endif

@if(session()->has('danger_array'))
	@if(session()->get('danger_array') != '')
	<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
		@foreach(session()->get('danger_array') as $value)
	  	<strong>{{$value}}</strong>
		<br>
		@endforeach
	  	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    	<span aria-hidden="true">&times;</span>
	  	</button>
	</div>
	@endif
@endif