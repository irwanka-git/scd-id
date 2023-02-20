<table id="tabel-upload" class="table table-striped table-hover table-sm" style="width:100%">
	<thead>
		<tr>							
			<th width="5%">No.</th>
			<th width="10%">Username</th>
			<th width="20%">Nama Lengkap</th>
			<th width="5%">L/P</th>
			<th width="20%">Sekolah/Organisasi</th>
			<th width="15%"></th>
		</tr>
	</thead>
	<tbody>
		<?php $no=1;?>
		@foreach($cek_data as $r)
			<tr>
				<td align="center">{{$no++}}</td>
				<td>{{$r->username}}</td>
				<td>{{$r->nama_pengguna}}</td>
				<td align="center">{{$r->jenis_kelamin}}</td>
				<td align="">{{$r->organisasi}}</td>
				<td align="center">
					@if($r->cek_peserta>0)
						<span class="badge bg-danger">Peserta Terdaftar</span>
					@elseif($r->cek_akun)
						<span class="badge bg-warning">Akun Terdaftar</span>
					@else
					<span class="badge bg-success">Valid</span>
					@endif
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
<script type="text/javascript">
	$(function(){
		$('#tabel-upload').DataTable({
							"iDisplayLength": 10, 
							"language": {
						      "emptyTable": "Data Tidak Tersedia.."
						    }
						});
	})
</script>