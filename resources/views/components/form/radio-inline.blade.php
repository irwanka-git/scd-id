<div class="form-group">
	<span class="control-label col-md-3 col-sm-3 col-xs-12">{{$label}} @if($required==true)<star>*</star>@endif
	</span>
	<div class="col-md-9 col-sm-9 col-xs-12">
	    <div class="radio">
	    	@foreach($data as $d)
			  	<label class="radio-inline"><input type="radio" value="{{$d->value}}" name="{{$name}}" @if($required==true) required="required" @endif>{{$d->text}}</label>
			 @endforeach
	    </div>
	</div>
</div>