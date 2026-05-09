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
                'name'              => $name,
                'phone'             => $phone,
                'email'             => $row['email'] ?? null,
                'company'           => $row['perusahaan'] ?? $row['company'] ?? null,
                'source'            => $row['sumber'] ?? $row['source'] ?? 'import',
                'status'            => $this->mapStatus($row['kategori'] ?? $row['status'] ?? 'new'),
                'city'              => $row['kota'] ?? $row['city'] ?? $row['lokasi_minat'] ?? null,
                'address'           => $row['alamat'] ?? $row['address'] ?? null,
                'notes'             => $row['catatan'] ?? $row['notes'] ?? null,
                'wa_phone'          => $waPhone,
                'created_by'        => auth()->id(),
                'assigned_to'       => auth()->id(),
                // Field properti
                'interest_type'     => $row['minat_tipe'] ?? $row['minat tipe'] ?? null,
                'budget_range'      => $row['range_budget'] ?? $row['range budget'] ?? null,
                'location_interest' => $row['lokasi_minat'] ?? $row['lokasi minat'] ?? null,
                'survey_plan'       => $row['rencana_survey'] ?? $row['rencana survey'] ?? null,
                'survey_result'     => $row['hasil_survey'] ?? $row['hasil survey'] ?? null,
                'cancel_reason'     => $row['alasan_pending'] ?? $row['alasan pending/batal'] ?? null,
                'utj_status'        => isset($row['utj']) && strtolower($row['utj']) === 'ya',
                'follow_up_date'    => isset($row['tanggal_fu']) ? $this->parseDate($row['tanggal_fu']) : null,
            ]);

            $this->imported++;
        }
    }
    private function mapStatus(?string $status): string
{
    if (!$status) return 'new';
    $s = strtolower(trim($status));
    return match(true) {
        str_contains($s, 'prospek')    => 'new',
        str_contains($s, 'no respon')  => 'contacted',
        str_contains($s, 'follow')     => 'contacted',
        str_contains($s, 'survey')     => 'qualified',
        str_contains($s, 'nego')       => 'negotiation',
        str_contains($s, 'deal')       => 'won',
        str_contains($s, 'utj')        => 'won',
        str_contains($s, 'batal')      => 'lost',
        str_contains($s, 'cancel')     => 'lost',
        default                        => 'new',
    };
}

private function parseDate($value): ?string
{
    if (!$value) return null;
    try {
        if ($value instanceof \DateTime) return $value->format('Y-m-d');
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    } catch (\Exception $e) {
        return null;
    }
}
}