#!/bin/bash
cd /var/www/html/sicerdas-release
php artisan skoring:peminatan_sma
php artisan skoring:peminatan_man
php artisan skoring:peminatan_smk
php artisan skoring:penjurusan_kuliah
php artisan skoring:peminatan_lengkap
php artisan skoring:peminatan_sma_v2
php artisan skoring:penjurusan_kuliah_v2
php artisan skoring:test_iq_dan_eq
php artisan skoring:clear_table_jawaban
