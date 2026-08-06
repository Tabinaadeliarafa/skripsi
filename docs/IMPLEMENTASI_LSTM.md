# Implementasi Prediksi LSTM

## Arsitektur
Laravel tetap menangani filter, query PostgreSQL, dan visualisasi. Data laporan hasil filter diagregasi per bulan lalu dikirim sebagai JSON ke `scripts/lstm_forecast.py`. Python melatih LSTM PyTorch, memprediksi 12 bulan berikutnya, dan mengembalikan total prediksi tahun berikutnya.

Pendekatan bulanan dipilih karena lima total tahunan hanya menghasilkan sangat sedikit sampel untuk LSTM. Lima tahun bulanan dapat menyediakan hingga 60 titik tanpa mengubah sumber data.

## Instalasi

```bash
composer install
python -m venv .venv
# Windows
.venv\Scripts\activate
# Linux/macOS
source .venv/bin/activate
pip install -r requirements-lstm.txt
```

Atur `.env`:

```env
LSTM_PYTHON_BINARY=/path/ke/project/.venv/bin/python
# Windows contoh: C:\path\project\.venv\Scripts\python.exe
LSTM_LOOK_BACK=12
LSTM_EPOCHS=250
LSTM_TIMEOUT=120
LSTM_CACHE_SECONDS=3600
LSTM_SEED=42
```

Lalu jalankan:

```bash
php artisan optimize:clear
php artisan serve
```

## Catatan
- Target tahun tidak lagi dikunci ke 2026; target selalu tahun setelah data terakhir.
- Filter jenis bencana, kecamatan, dan tanggal tetap berlaku pada data yang diprediksi.
- Hasil dicache satu jam agar halaman tidak melatih ulang model pada setiap refresh.
- Bila Python/PyTorch belum siap atau data terlalu sedikit, halaman tetap terbuka dan menampilkan pesan; fitur lain tidak ikut gagal.
- Nilai RMSE yang tampil adalah galat pada data latih, bukan jaminan akurasi masa depan. Dengan dataset kecil, hasil LSTM harus dijelaskan sebagai estimasi eksperimental.
