<?php

namespace App\Imports;

use App\Models\Lead;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadsImport implements ToCollection, WithHeadingRow
{
    public array $errors   = [];
    public int   $imported = 0;
    public int   $skipped  = 0;

    private array $staffCache   = [];
    private array $productCache = [];

    private function findStaffByName(?string $name): ?int
    {
        if (!$name) return auth()->id();
        $name = trim(strtolower($name));
        if (isset($this->staffCache[$name])) return $this->staffCache[$name];
        $user = \App\Models\User::where('role', 'staff')
            ->whereRaw('LOWER(name) LIKE ?', ['%'.$name.'%'])
            ->first();
        $id = $user ? $user->id : auth()->id();
        $this->staffCache[$name] = $id;
        return $id;
    }

    private function findProductByName(?string $name): ?int
    {
        if (!$name) return null;
        $name = trim(strtolower($name));
        if (isset($this->productCache[$name])) return $this->productCache[$name];
        $product = \App\Models\Product::whereRaw('LOWER(name) LIKE ?', ['%'.$name.'%'])->first();
        $id = $product ? $product->id : null;
        $this->productCache[$name] = $id;
        return $id;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $name = $row['nama_user'] ?? $row['nama user'] ?? $row['nama_customer'] ?? $row['nama customer'] ?? $row['nama'] ?? $row['name'] ?? null;

            if (empty($name)) {
                $this->skipped++;
                continue;
            }

            $phone = $row['no_hp'] ?? $row['nomor_wa_hp'] ?? $row['nomor wa_hp'] ?? $row['phone'] ?? $row['telepon'] ?? $row['hp'] ?? null;
            $phone = $phone ? (string) $phone : null;

            $waPhone = null;
            if ($phone) {
                $clean = preg_replace('/\D/', '', $phone);
                if (str_starts_with($clean, '0')) {
                    $clean = '62' . substr($clean, 1);
                } elseif (str_starts_with($clean, '8')) {
                    $clean = '62' . $clean;
                } elseif (str_starts_with($clean, '62')) {
                    // sudah benar
                }
                $waPhone = $clean;
            }

            Lead::create([
                'name'              => $name,
                'phone'             => $waPhone ?? $phone,
                'email'             => $row['email'] ?? null,
                'company'           => $row['perusahaan'] ?? $row['company'] ?? null,
                'source'            => $row['sumber_leads'] ?? $row['sumber leads'] ?? $row['sumber'] ?? $row['source'] ?? 'iklan meta',
                'status'            => $this->mapStatus($row['kategori'] ?? $row['status'] ?? 'new'),
                'city'              => $row['kota'] ?? $row['city'] ?? null,
                'address'           => $row['alamat'] ?? $row['address'] ?? null,
                'notes'             => $row['catatan'] ?? $row['notes'] ?? $row['report_fu'] ?? $row['report fu'] ?? null,
                'wa_phone'          => $waPhone,
                'created_by'        => auth()->id(),
                'assigned_to'       => $this->findStaffByName(
                    $row['nama_sales'] ?? $row['nama sales'] ??
                    $row['nama_marketing'] ?? $row['nama marketing'] ??
                    $row['sales'] ?? null
                ),
                'product_id'        => $this->findProductByName($row['produk'] ?? $row['product'] ?? null),
                'interest_type'     => $row['minat_tipe'] ?? $row['minat tipe'] ?? null,
                'budget_range'      => $row['range_budget'] ?? $row['range budget'] ?? null,
                'location_interest' => $row['lokasi_minat'] ?? $row['lokasi minat'] ?? null,
                'survey_plan'       => $row['rencana_survey'] ?? $row['rencana survey'] ?? null,
                'survey_result'     => $row['hasil_survey'] ?? $row['hasil survey'] ?? null,
                'cancel_reason'     => $row['alasan_pending'] ?? $row['alasan pending/batal'] ?? null,
                'utj_status'        => isset($row['utj']) && strtolower((string)($row['utj'] ?? '')) === 'ya',
                'utj_date'          => isset($row['tanggal_utj']) ? $this->parseDate($row['tanggal_utj']) : null,
                'follow_up_date'    => isset($row['tanggal_fu']) ? $this->parseDate($row['tanggal_fu']) :
                                      (isset($row['tanggal follow up terakhir']) ? $this->parseDate($row['tanggal follow up terakhir']) : null),
            ]);

            $this->imported++;
        }
    }

    private function mapStatus(?string $status): string
    {
        if (!$status) return 'new';
        $s = strtolower(trim((string)$status));
        return match(true) {
            str_contains($s, 'prospek')   => 'new',
            str_contains($s, 'no respon') => 'contacted',
            str_contains($s, 'follow')    => 'contacted',
            str_contains($s, 'survey')    => 'qualified',
            str_contains($s, 'nego')      => 'negotiation',
            str_contains($s, 'deal')      => 'won',
            str_contains($s, 'utj')       => 'won',
            str_contains($s, 'batal')     => 'lost',
            str_contains($s, 'cancel')    => 'lost',
            default                       => 'new',
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
