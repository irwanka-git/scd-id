<div class="mb-3">
	<label class="form-label">{!! $label !!} @if($required) <star>*</star> @endif</label>
	<select class="form-control select2" name="{{$name}}" id="{{$name}}" @if($required==true) required="required" @endif data-toggle="select2" style="width: 100%;">
		@if($data)
			
		  	@foreach($data as $d)
		  		<option value="{{$d->value}}" @if($value==$d->value) selected="selected" @endif>{{$d->text}}</option>
		  	@endforeach
	  	@endif
	</select>
</div>