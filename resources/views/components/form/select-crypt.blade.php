<div class="form-group">
	<div class="col-md-3 text-right">
		<span>{!! $label !!}</span>
		@if($required) <star>*</star> @endif
	</div>
	<div class="col-{{$md_size}}">
	  <select class="form-control select2" name="{{$name}}" id="{{$name}}" @if($required==true) required="required" @endif style="width: 100%;">
	  		<option value="">[Pilihan]</option>
	  	@if($data)
		  	@foreach($data as $d)
		  		<option value="{{Crypt::encrypt($d->value)}}" @if($value==$d->value) selected="selected" @endif>{{$d->text}}</option>
		  	@endforeach
	  	@endif
	  </select>
	</div>
</div>