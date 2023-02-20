<div class="mb-3">
	<label class="form-label">{!! $label !!} @if($required) <star>*</star> @endif</label>
	<textarea class="form-control" name="{{$name}}" id="{{$name}}" @if($required) required="required" @endif rows="2">{{$default}}</textarea>
</div>