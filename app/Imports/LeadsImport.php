<?php

namespace App\Imports;

use App\Models\Lead;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LeadsImport implements ToCollection, WithHeadingRow
{
    public array $errors   = [];
    public int   $imported = 0;
    public int   $skipped  = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $name = $row['nama'] ?? $row['name'] ?? null;

            if (empty($name)) {
                $this->skipped++;
                continue;
            }

            $phone = $row['phone'] ?? $row['no_hp'] ?? $row['telepon'] ?? $row['hp'] ?? null;
            $phone = $phone ? (string) $phone : null;

            $waPhone = null;
            if ($phone) {
                $clean = preg_replace('/\D/', '', $phone);

                // Berawalan 0 -> ganti dengan 62
                if (str_starts_with($clean, '0')) {
                    $clean = '62' . substr($clean, 1);
                }
                // Berawalan 8 -> tambah 62 di depan
                elseif (str_starts_with($clean, '8')) {
                    $clean = '62' . $clean;
                }
                // Sudah ada 62 di depan -> biarkan
                elseif (str_starts_with($clean, '62')) {
                    // sudah benar
                }
                // Kode negara lain -> biarkan
                
                $waPhone = $clean;
            }

            Lead::create([
                'name'       => $name,
                'phone'      => $phone,
                'email'      => $row['email'] ?? null,
                'company'    => $row['perusahaan'] ?? $row['company'] ?? null,
                'source'     => $row['sumber'] ?? $row['source'] ?? 'import',
                'status'     => 'new',
                'city'       => $row['kota'] ?? $row['city'] ?? null,
                'address'    => $row['alamat'] ?? $row['address'] ?? null,
                'notes'      => $row['catatan'] ?? $row['notes'] ?? null,
                'wa_phone'   => $waPhone,
                'created_by' => auth()->id(),
                'assigned_to'=> auth()->id(),
            ]);

            $this->imported++;
        }
    }
}