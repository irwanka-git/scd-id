<?php 
loadHelper('skoring,function');
?>
<table class="table table-striped table-sm table-bordered  table-x">
	<thead>
		<tr class="table-primary">
			<th class="text-center sm">ASPEK KOGNITIF</th>
			<th class="text-center sm">SANGAT RENDAH</th>
			<th class="text-center sm">RENDAH</th>
			<th class="text-center sm">SEDANG</th>
			<th class="text-center sm">TINGGI</th>
			<th class="text-center sm">SANGAT TINGGI</th>
		</tr>
	</thead>
	<tbody>
		
		<tr>
			<td>01 - Informasi Umum</td>
			<?php 
			$klasifikasi = get_skor_predikat($data_skoring->tpa_iu,'skor','klasifikasi','ref_skala_kd_informasi_umum');
			?>
			<td  align="center">@if($klasifikasi=='SANGAT_RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SEDANG') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='TINGGI') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SANGAT_TINGGI') <i class="fas fa-check"></i> @endif</td>
		</tr>
		<tr>
			<td>02 - Penalaran Verbal</td>
			<?php 
			$klasifikasi = get_skor_predikat($data_skoring->tpa_pv,'skor','klasifikasi','ref_skala_kd_penalaran_verbal');
			?>
			<td  align="center">@if($klasifikasi=='SANGAT_RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SEDANG') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='TINGGI') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SANGAT_TINGGI') <i class="fas fa-check"></i> @endif</td>
		</tr>
		<tr>
			<td>03 - Penalaran Kuantitatif</td>
			<?php 
			$klasifikasi = get_skor_predikat($data_skoring->tpa_pk,'skor','klasifikasi','ref_skala_kd_penalaran_kuantitatif');
			?>
			<td  align="center">@if($klasifikasi=='SANGAT_RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SEDANG') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='TINGGI') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SANGAT_TINGGI') <i class="fas fa-check"></i> @endif</td>
		</tr>
		<tr>
			<td>
				04 - Penalaran Abstrak
			</td>
			<?php 
			$klasifikasi = get_skor_predikat($data_skoring->tpa_pa,'skor','klasifikasi','ref_skala_kd_penalaran_abstrak');
			?>
			<td  align="center">@if($klasifikasi=='SANGAT_RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SEDANG') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='TINGGI') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SANGAT_TINGGI') <i class="fas fa-check"></i> @endif</td>
		</tr>
		<tr>
			<td>
				05 - Penalaran Spasial
			</td>
			<?php 
			$klasifikasi = get_skor_predikat($data_skoring->tpa_ps,'skor','klasifikasi','ref_skala_kd_penalaran_spasial');
			?>
			<td  align="center">@if($klasifikasi=='SANGAT_RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SEDANG') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='TINGGI') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SANGAT_TINGGI') <i class="fas fa-check"></i> @endif</td>
		</tr>
		<tr>
			<td>
				06 - Pengertian Mekanika
			</td>
			<?php 
			$klasifikasi = get_skor_predikat($data_skoring->tpa_pm,'skor','klasifikasi','ref_skala_kd_penalaran_mekanika');
			?>
			<td  align="center">@if($klasifikasi=='SANGAT_RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SEDANG') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='TINGGI') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SANGAT_TINGGI') <i class="fas fa-check"></i> @endif</td>
		</tr>
		<tr>
			<td>
				07 - Ketelitian
			</td>
			<?php 
			$klasifikasi = get_skor_predikat($data_skoring->tpa_kt,'skor','klasifikasi','ref_skala_kd_cepat_teliti');
			?>
			<td  align="center">@if($klasifikasi=='SANGAT_RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='RENDAH') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SEDANG') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='TINGGI') <i class="fas fa-check"></i> @endif</td>
			<td  align="center">@if($klasifikasi=='SANGAT_TINGGI') <i class="fas fa-check"></i> @endif</td>
		</tr>
		<tr>
			<td>
				Kecerdasan Umum (IQ-konversi)
			</td>
			<td align="center" colspan="5">
				Skor: {{$data_skoring->skor_iq}} &nbsp;&nbsp;|&nbsp;&nbsp; Klasifikasi: {{skoring_replace_(get_skor_predikat($data_skoring->tpa_iq,'skor_x','klasifikasi','ref_konversi_iq'))}}
			</td>
		</tr>
	</tbody>
</table>