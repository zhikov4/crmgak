# GAK CRM — AI Marketing Suite

CRM (Customer Relationship Management) untuk **PT. Graha Agung Kencana** & **PT. Graha Agung Perkasa**, developer properti di Surabaya.

---

## Fitur Utama

### Manajemen Leads
- CRUD lengkap dengan field properti (survey, budget, UTJ, minat tipe, lokasi)
- Search & filter dinamis (status, sumber, produk)
- Filter cepat **"Perlu Follow Up"** — lead yang diam >3 hari
- Tombol satu-klik **"✓ Sudah Follow Up"** untuk update tanggal FU
- Tombol **WhatsApp** langsung ke wa.me dari daftar lead
- **Import Excel** dengan deteksi duplikat otomatis
- Badge **"⚠ Bentrok"** untuk lead yang dipegang >1 sales

### Dashboard
- KPI real-time: total leads, closing, conversion rate, pipeline value
- Panel **"⏰ Perlu Follow Up"** — daftar lead yang menunggu dikejar
- Grafik trend leads 6 bulan & leads per sumber
- Aktivitas mendatang

### Notifikasi Follow Up
- Badge angka oranye di sidebar menunjukkan berapa lead perlu dikejar (per role)
- Lead dianggap perlu FU jika: status aktif + belum pernah di-FU atau FU terakhir >3 hari
- Tombol "✓ Sudah FU" di daftar leads & panel dashboard

### Lead Bentrok
- Deteksi otomatis: nomor WA sama dipegang >1 sales
- Halaman khusus **"Lead Bentrok"** untuk manajer & direktur
- Tombol "Tetapkan / Edit" untuk assign ulang ke satu sales

### Pipeline Kanban
- 7 stage: New → Contacted → Survey → Proposal → Negotiation → Won → Lost
- Filter per role (staff hanya lihat pipeline miliknya)

### Proyek & Aktivitas
- Tracking progress dan deadline proyek
- Jadwal follow up dan meeting
- Filter RBAC per role

### Analytics & Laporan
- Grafik performa penjualan per status, sumber, dan stage
- Laporan bulanan + **print/PDF** dengan badge status terbaru
- Semua data difilter per role

### RBAC (Role-Based Access Control)
- 3 role: **Direktur** (semua data), **Manajer** (data tim), **Staff** (data sendiri)
- Policy di setiap model — staff tidak bisa akses lead orang lain
- Halaman Lead Bentrok & Team View hanya untuk manajer & direktur

### Import Excel
- Mapping otomatis: Nama Marketing → assigned_to, Produk → product_id
- Deteksi duplikat: **WA + Produk + Sales + Nama** (dobel-klik tidak menggandakan data)
- Lead bentrok (WA sama, sales beda) tetap masuk untuk ditinjau manajer
- Daftar detail duplikat yang dilewati ditampilkan di hasil import

---

## Status Lead

| Key (DB) | Label Tampilan |
|----------|---------------|
| `no_respon` | Belum Merespon |
| `respon` | Sudah Merespon |
| `kirim_pl` | Penawaran Terkirim |
| `survey` | Survei Lokasi |
| `utj` | Tanda Jadi |
| `closing` | Closing |
| `batal` | Tidak Jadi |

---

## Produk Properti

1. Wisata Semanggi
2. Grand Semanggi Residence
3. Semanggi Mangrove
4. Blukid Residence 3
5. Wisata Bukit Sentul

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2 |
| Database | PostgreSQL (Supabase) |
| Frontend | Tailwind CSS, Chart.js |
| Auth | Laravel Breeze |
| Import | Maatwebsite/Excel |

---

## Struktur Role & User

```
Direktur (Nurhadi, S.E.)  →  direktur@grahagung.com
└── Manajer (Wisnu)        →  manajer@grahagung.com
    ├── Luluk               →  luluk@grahagung.com
    ├── Andri               →  andri@grahagung.com
    ├── Adjie               →  adjie@grahagung.com
    ├── Aditya              →  aditya@grahagung.com
    ├── Avit                →  avit@grahagung.com
    ├── Wahyu               →  wahyu@grahagung.com
    ├── Tony                →  tony@grahagung.com
    └── Jervis              →  jervis@grahagung.com
```

---

## Instalasi & Setup

```bash
# 1. Clone & install
git clone https://github.com/zhikov4/crmgak.git
cd crmgak
composer install
npm install && npm run build

# 2. Environment
cp .env.example .env
php artisan key:generate
# Edit .env: isi DB_* dengan kredensial Supabase

# 3. Database
php artisan migrate
php artisan db:seed        # buat 5 produk + 10 user (idempotent)

# 4. Jalankan
php artisan serve
npm run dev
```

> **Catatan:** `php artisan db:seed` aman dijalankan berulang — tidak menggandakan data yang sudah ada dan tidak menimpa password user aktif.

---

## Import Data

1. Gunakan template **`3_TEMPLATE_KOSONG.xlsx`** untuk input data manual baru
2. Buka menu **Import Excel** → upload file → klik Import
3. Kolom wajib: `Nama Customer`, `Nomor WA/HP`, `Nama Marketing`, `Produk`, `Status`
4. Duplikat (WA + Produk + Sales + Nama sama) otomatis dilewati

---

## Developer

Dikembangkan untuk **PT. Graha Agung Kencana** & **PT. Graha Agung Perkasa**
