<center>
	<img src="{{url('gambar/'.$user->avatar)}}" width="100" height="100" class="rounded-circle me-2">
    
    <p>
    	<div class="info-user">
	    	<small>{{$user->username}}</small>
	    	<br><b>{{$user->nama_pengguna}}</b>
	    	@if($user->jenis_kelamin=='P')
	    	<br>Perempuan
	    	@else
	    	<br>Laki-Laki
	    	@endif
	    </div>
    </p>
    @if($user->organisasi)
    	<div class="info-user">
    	<i class="las la-building"></i> {{$user->organisasi}}
    	<br><small>{{$user->unit_organisasi}}</small>
    	</div><br>
    @endif
	@if($user->email)
	<i class="las la-envelope"></i> {{$user->email}}
	@endif
	@if($user->telp)
	<br><i class="las la-phone-square-alt"></i> {{$user->telp}}
	@endif
</center>