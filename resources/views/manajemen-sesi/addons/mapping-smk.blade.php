<div class="col-auto ms-auto mt-n1">
	<p>
		<small> Catatan: Sebelum Menambahkan / Menghapus Pilihan SMK, Pastikan Sesi Tes dalam Status <b>Ditutup (Closed). Maksimal Jumlah Pilihan Adalah 5 Pilihan Jurusan</b></small>
	</p>
	@if(!$quiz->open)
	<button class="btn-tambah-pilihan-smk btn btn-primary btn-sm"><i class="la la-plus"></i> Tambah Pilihan</button>
	@endif
</div>
<hr>
<table id="tabel-pilihan-smk"  class="dataTableLayout table table-striped table-hover table-sm" style="width:100%">
	<thead>
		<tr>							
			<th width="90%">Pilihan</th>
			<th width="10%"></th>
		</tr>
	</thead>
	<tbody>

	</tbody>
</table>
<?php 
loadHelper('akses');
$list_pilihan_smk =   DB::select("SELECT  a.id_kegiatan as value, concat(nomor,' - ', keterangan, ' , Paket: ', paket) as text 
							FROM soal_peminatan_smk as a order by nomor asc");
?>

@if(ucu())
<!-- MODAL FORM TAMBAH -->
{{ Form::bsOpen('form-tambah-pilihan-smk',url("manajemen-sesi/insert-mapping-smk")) }}
	{{Html::mOpenLG('modal-tambah-pilihan-smk','Tambah Pilihan SMK')}}
		{{ Form::bsSelect2('Pilihan SMK','id_kegiatan',$list_pilihan_smk,'',true,'md-8')}}
		{{ Form::bsHidden('id_quiz',$quiz->uuid)}}
	{{Html::mCloseSubmitLG('Simpan')}}
{{ Form::bsClose()}}

{{ Form::bsOpen('form-hapus-pilihan-smk',url("manajemen-sesi/delete-mapping-smk")) }}
{{ Form::bsHidden('uuid','')}}
{{ Form::bsClose()}}
@endif

<script type="text/javascript">
	$(function(){


		$('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {

			  if ($(this).data('bs-target') == '#addons-pilihan-smk') {
			    $tabel_pilihan_smk.columns.adjust().draw();
			  }
		});

		var $tabel_pilihan_smk =  
		$('#tabel-pilihan-smk').DataTable({
			processing: true,
			responsive: true,
			fixedHeader: true,
			serverSide: true,
			ajax: "{{url('manajemen-sesi/dt-mapping-smk/'.$quiz->uuid)}}",
			"iDisplayLength": 10,
			columns: [
			{data: 'nomor',name: "user",orderable: false,searchable: false,sClass: ""},
			{data:'action' , orderable:false, searchable: false,sClass:"text-center"},
			],
			"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).addClass(aData["rowClass"]);
				return nRow;
			},
			"drawCallback": function(settings) {
					initRemovePilihan();
					//$tabel_pilihan_smk.columns.adjust().draw();
					//$(".result-jumlah-pilihan-smk").text('('+settings.json.jumlah_peserta+')');

				},
				"language": {
					"emptyTable": "Belum Ada Pilihan.."
				}
		});

		@if(ucu())

		$('#form-hapus-pilihan-smk').ajaxForm({
			beforeSubmit: function() {},
			success: function($respon) {
				if ($respon.status == true) {
					successNotify($respon.message);
					//location.reload();
					$tabel_pilihan_smk.ajax.reload(null, true);
					} else {
						errorNotify($respon.message);
					}
				},
				error: function() {
					errorNotify('Terjadi Kesalahan!');
				}
		});

		var initRemovePilihan = function(){
			$(".btn-delete-pilihan-smk").on('click', function(){
				$uuid = $(this).data('uuid');
				$pilihan = $(this).data('pilihan');
				$("#form-hapus-pilihan-smk #uuid").val($uuid);
				$.confirm({
					title: "Hapus Pilihan SMK Berikut?",
					content: $pilihan,
					buttons: {
						cancel: {
							text: 'Batalkan'
						},
						confirm: {
							text: 'Ya, Lanjutkan',
							btnClass: 'btn-success',
							action: function() {
								$("#form-hapus-pilihan-smk").submit();
							}
						},
					}
				});
			});

		}

		$(".btn-tambah-pilihan-smk").on('click', function(){
			$("#modal-tambah-pilihan-smk").modal('show');
		});

		$validator_form_tambah_smk = $("#form-tambah-pilihan-smk").validate();
		$("#modal-tambah-pilihan-smk").on('show.bs.modal', function(e) {
			$validator_form_tambah_smk.resetForm();
			$("#form-tambah-pilihan-smk").clearForm();
			$('#form-tambah-pilihan-smk #id_kegiatan').selectize()[0].selectize.clear();
			enableButton("#modal-tambah-pilihan-smk button[type=submit]")
		});

		$('#form-tambah-pilihan-smk').ajaxForm({
			beforeSubmit: function() {
				disableButton("#form-tambah-pilihan-smk button[type=submit]")
			},
			success: function($respon) {
				if ($respon.status == true) {
					$("#modal-tambah-pilihan-smk").modal('hide');
					successNotify($respon.message);
					$tabel_pilihan_smk.ajax.reload(null, true);
				} else {
					errorNotify($respon.message);
				}
				enableButton("#form-tambah-pilihan-smk button[type=submit]")
			},
			error: function() {
				$("#form-tambah-pilihan-smk button[type=submit]").button('reset');
				$("#modal-tambah-pilihan-smk").modal('hide');
				errorNotify('Terjadi Kesalahan!');
			}
		});
		@endif

	});
</script>
