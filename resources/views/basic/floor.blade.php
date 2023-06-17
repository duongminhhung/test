
	<label for="Floor">
    	{{__('Warehouse Floor')}}
    </label>
   	<select name="Floor" class="form-control select2" id="floor" required>
    	<option value="">
    		{{__('Choose')}} {{__('Warehouse Floor')}}
    	</option>

    	<option value="1" {{old('Floor') ? (old('Floor') == 1 ? 'selected' : '') : ($warehouse ? ($warehouse->Floor == 1 ? 'selected' : '') : '')}}>
    		{{__('Warehouse Floor')}} 1.1
    	</option>

    	<option value="3" {{old('Floor') ? (old('Floor') == 3 ? 'selected' : '') : ($warehouse ? ($warehouse->Floor == 3 ? 'selected' : '') : '')}}>
    		{{__('Warehouse Floor')}} 1.2
    	</option>

    	<option value="2" {{old('Floor') ? (old('Floor') == 2 ? 'selected' : '') : ($warehouse ? ($warehouse->Floor == 2 ? 'selected' : '') : '')}}>
    		{{__('Warehouse Floor')}} 2.1
    	</option>

    	<option value="4" {{old('Floor') ? (old('Floor') == 4 ? 'selected' : '') : ($warehouse ? ($warehouse->Floor == 4 ? 'selected' : '') : '')}}>
    		{{__('Warehouse Floor')}} 2.2
    	</option>
    </select>