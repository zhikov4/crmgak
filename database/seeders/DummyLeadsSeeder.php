<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;

class DummyLeadsSeeder extends Seeder
{
    public function run(): void
    {
        $leads = [
            ['name' => 'Budi Santoso', 'company' => 'PT Maju Bersama', 'phone' => '081234567890', 'source' => 'referral', 'status' => 'qualified', 'value' => 15000000, 'city' => 'Jakarta'],
            ['name' => 'Siti Rahayu', 'company' => 'CV Sukses Makmur', 'phone' => '082345678901', 'source' => 'instagram', 'status' => 'proposal', 'value' => 25000000, 'city' => 'Surabaya'],
            ['name' => 'Ahmad Fauzi', 'company' => 'PT Karya Abadi', 'phone' => '083456789012', 'source' => 'google', 'status' => 'negotiation', 'value' => 50000000, 'city' => 'Bandung'],
            ['name' => 'Dewi Lestari', 'company' => 'UD Rejeki Lancar', 'phone' => '084567890123', 'source' => 'facebook', 'status' => 'won', 'value' => 35000000, 'city' => 'Semarang'],
            ['name' => 'Eko Prasetyo', 'company' => 'PT Bintang Timur', 'phone' => '085678901234', 'source' => 'whatsapp', 'status' => 'contacted', 'value' => 12000000, 'city' => 'Yogyakarta'],
            ['name' => 'Fitri Handayani', 'company' => 'CV Harapan Jaya', 'phone' => '086789012345', 'source' => 'referral', 'status' => 'new', 'value' => 8000000, 'city' => 'Malang'],
            ['name' => 'Gunawan Wibowo', 'company' => 'PT Global Nusantara', 'phone' => '087890123456', 'source' => 'instagram', 'status' => 'proposal', 'value' => 75000000, 'city' => 'Jakarta'],
            ['name' => 'Heni Susanti', 'company' => 'UD Makmur Sentosa', 'phone' => '088901234567', 'source' => 'google', 'status' => 'qualified', 'value' => 20000000, 'city' => 'Medan'],
            ['name' => 'Irwan Kusuma', 'company' => 'PT Dinamika Usaha', 'phone' => '089012345678', 'source' => 'website', 'status' => 'negotiation', 'value' => 45000000, 'city' => 'Makassar'],
            ['name' => 'Joko Hartono', 'company' => 'CV Prima Karya', 'phone' => '081123456789', 'source' => 'referral', 'status' => 'won', 'value' => 60000000, 'city' => 'Solo'],
            ['name' => 'Kartini Putri', 'company' => 'PT Mandiri Sejahtera', 'phone' => '082234567890', 'source' => 'facebook', 'status' => 'new', 'value' => 5000000, 'city' => 'Denpasar'],
            ['name' => 'Lukman Hakim', 'company' => 'UD Berkah Abadi', 'phone' => '083345678901', 'source' => 'instagram', 'status' => 'contacted', 'value' => 18000000, 'city' => 'Palembang'],
            ['name' => 'Maya Sari', 'company' => 'PT Utama Jaya', 'phone' => '084456789012', 'source' => 'whatsapp', 'status' => 'proposal', 'value' => 30000000, 'city' => 'Tangerang'],
            ['name' => 'Nanda Pratama', 'company' => 'CV Cahaya Terang', 'phone' => '085567890123', 'source' => 'google', 'status' => 'lost', 'value' => 22000000, 'city' => 'Bekasi'],
            ['name' => 'Okta Riski', 'company' => 'PT Anugerah Mulia', 'phone' => '086678901234', 'source' => 'referral', 'status' => 'qualified', 'value' => 40000000, 'city' => 'Bogor'],
        ];

        foreach ($leads as $i => $lead) {
            $phone = preg_replace('/\D/', '', $lead['phone']);
            if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
            Lead::create(array_merge($lead, [
                'wa_phone'   => $phone,
                'created_by' => 1,
                'assigned_to'=> 1,
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now(),
            ]));
        }
    }
}
