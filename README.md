# Aplikasi Mahasiswa - Single File PHP

Aplikasi web untuk manajemen data mahasiswa dengan desain modern dan responsif.

## Fitur

- ✅ CRUD (Create, Read, Update, Delete) Mahasiswa
- 🎨 UI Modern dengan animasi smooth
- 📱 Responsive design untuk semua device
- 🗄️ Database MySQL integration

## Teknologi

- PHP 8.0+
- MySQL
- HTML5 & CSS3
- Responsive CSS

## Instalasi

### Persyaratan
- PHP 8.0 atau lebih tinggi
- MySQL Server
- XAMPP atau web server dengan PHP support

### Setup Database

```sql
CREATE DATABASE belajardb;

CREATE TABLE mahasiswa (
  nim INT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  sex CHAR(1),
  prodi VARCHAR(50),
  tanggal_masuk DATE
);
```

### Konfigurasi
Edit file `mahasiswa.php` dan ubah konfigurasi database:

```php
$host = "localhost";
$user = "belajar";
$pass = "rahasia";
$db   = "belajardb";
```

### Menjalankan Aplikasi

```bash
# Jika menggunakan PHP built-in server
php -S localhost:8000

# Atau copy ke folder htdocs XAMPP dan akses via http://localhost/mahasiswa.php
```

## Penggunaan

1. **Data Mahasiswa** - Lihat daftar semua mahasiswa
2. **Tambah** - Tambah data mahasiswa baru
3. **Edit** - Ubah data mahasiswa yang sudah ada
4. **Hapus** - Hapus data mahasiswa

## Fitur Desain

- 🌈 Gradient background animasi
- ✨ Smooth transitions & animations
- 🎯 Ripple button effect
- 💫 Hover effects pada semua elemen
- 📦 Modern card design
- 🔤 Professional typography
- 📱 Fully responsive

## Author

Dibuat sebagai aplikasi pembelajaran PHP & MySQL

## License

MIT
