# GAK CRM — AI Marketing Suite

CRM (Customer Relationship Management) untuk PT. Graha Agung Kencana & PT. Graha Agung Perkasa, developer properti di Surabaya.

## Fitur Utama

- **Dashboard** — KPI real-time, grafik trend leads, leads per sumber
- **Manajemen Leads** — CRUD lengkap, search & filter, field properti (survey, UTJ, budget)
- **Pipeline Kanban** — 7 stage (New → Won/Lost)
- **Proyek** — tracking progress dan deadline
- **Aktivitas** — jadwal follow up dan meeting
- **Analytics** — grafik performa penjualan
- **Laporan** — laporan bulanan + print/PDF
- **Import Excel** — import data leads dari file Excel
- **Manajemen Produk** — Wisata Semanggi, Grand Semanggi Residence, Blukid Residence 3, dll
- **RBAC** — 3 role: Direktur, Manajer, Staff dengan data privacy
- **Team View** — monitor performa staff per individu dan overview

## Tech Stack

- **Backend** — Laravel 12, PHP 8.2
- **Database** — PostgreSQL (Supabase)
- **Frontend** — Tailwind CSS, Chart.js
- **Auth** — Laravel Breeze

## Produk Properti

1. Wisata Semanggi
2. Grand Semanggi Residence
3. Semanggi Residence
4. Semanggi Mangrove
5. Blukid Residence 3
6. Wisata Bukit Sentul

## Instalasi

```bash
git clone https://github.com/zhikov4/crmgak.git
cd crmgak
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Developer

Dikembangkan untuk PT. Graha Agung Kencana & PT. Graha Agung Perkasa
