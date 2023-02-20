 <?php 
 $list_jenis_tes = DB::select("SELECT 
							a.id_quiz_template as value, concat(a.nama_sesi,', ',a.jenis) as text 
							FROM quiz_sesi_template as a order by jenis desc ");
 $list_biro = DB::select("select a.uuid as value, a.nama_pengguna as text  
							from users a , user_role as b 
								where a.id= b.id_user and b.id_role = $id_role_biro ");

 $list_lokasi = DB::select("select uuid as id , nama_lokasi as title,
								c.name as kabupaten , b.name as provinsi
								from lokasi as a, provinces as b, regencies as c 
								where a.kode_kabupaten = c.id and a.kode_provinsi= b.id 
								order by a.kode_kabupaten");
?>
{{ Form::bsOpen('form-tambah',url("manajemen-sesi/insert")) }}
	{{Html::mOpenLG('modal-tambah','Tambah Sesi Tes')}}
		{{ Form::bsSelect2('Biro','id_user_biro',$list_biro,'',true,'md-8')}}
		{{ Form::bsSelect2('Jenis Tes','id_quiz_template',$list_jenis_tes,'',true,'md-8')}}
		<small class="mb-2">Perhatian: 
		<b>Nama Sesi / Tujuan</b> akan Ditampilkan sebagai kolom <b>tujuan</b> di dokumen Laporan Hasil Tes Peserta.
	 	</small>
		{{ Form::bsTextField('Nama Sesi / Tujuan Tes','nama_sesi','',true,'md-8') }}
		
		{{ Form::bsTextField('Tanggal','tanggal','',false,'md-8') }}
		{{ Form::bsSelect2('Lokasi','id_lokasi',[],'',true,'md-8')}}
		<small class="mb-2">Perhatian:  <b>Nama Kota</b> Akan Ditampilkan pada <b>tanda tangan</b> di dokumen Laporan Hasil Tes Peserta.
					</small>
		{{ Form::bsTextField('Nama Kota (Tanda Tangan)','kota','',true,'md-8') }}
		
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}
	 
<script type="text/javascript">
	$(function(){

		$("#form-tambah #id_user_biro").selectize();
		$("#form-tambah #id_quiz_template").selectize();
		$('#form-tambah #id_lokasi').selectize({
		    valueField: 'id',
		    searchField: 'title',
		    options: {!! json_encode($list_lokasi) !!},
		    render: {
		        option: function(data, escape) {
		            return '<div class="option">' +
		                    '<span class="title">' + escape(data.title) + '</span><br>' +
		                    '<small>' + escape(data.kabupaten) + ', ' + escape(data.provinsi) + '</small>' +
		                '</div>';
		        },
		        item: function(data, escape) {
		            return '<div class="item">' + escape(data.title) + ', ' + escape(data.kabupaten) + '</div>';
		        }
		    },
		    create: false
		});

		$("#form-tambah #tanggal").daterangepicker({
			singleDatePicker: true,
			showDropdowns: true,
			locale: {"format": "YYYY-MM-DD"},
			autoApply:true
		});

		$validator_form_tambah = $("#form-tambah").validate();
		$("#modal-tambah").on('show.bs.modal', function(e) {
			$validator_form_tambah.resetForm();
			$("#form-tambah").clearForm();
			$('#form-tambah #id_quiz_template').selectize()[0].selectize.clear();
			$('#form-tambah #id_user_biro').selectize()[0].selectize.clear();
			$('#form-tambah #id_lokasi').selectize()[0].selectize.clear();
			enableButton("#form-tambah button[type=submit]")
		});

		$('#form-tambah').ajaxForm({
			beforeSubmit: function() {
				disableButton("#form-tambah button[type=submit]")
			},
			success: function($respon) {
				if ($respon.status == true) {
					$("#modal-tambah").modal('hide');
					successNotify($respon.message);
					location.reload();
				} else {
					errorNotify($respon.message);
				}
				enableButton("#form-tambah button[type=submit]")
			},
			error: function() {
				$("#form-tambah button[type=submit]").button('reset');
				$("#modal-tambah").modal('hide');
				errorNotify('Terjadi Kesalahan!');
			}
		});

		

	})
</script>
