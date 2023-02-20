<div class="mb-3">
	<label class="form-label">{!! $label !!} @if($required) <star>*</star> @endif</label>
	<input type="text" name="{{$name}}" id="{{$name}}" 
		value="{{$default}}" @if($required) required="required" @endif class="form-control">
</div>