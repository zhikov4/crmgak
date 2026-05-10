<?php

namespace App\Imports;

use App\Models\Lead;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class LeadsImport implements ToCollection
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
        $headerRowIndex = null;
        $headers = [];

        // Cari baris header (ada kolom NAMA USER atau NAMA CUSTOMER)
        foreach ($rows as $index => $row) {
            $rowValues = $row->toArray();
            foreach ($rowValues as $cell) {
                if ($cell && (
                    stripos((string)$cell, 'nama user') !== false ||
                    stripos((string)$cell, 'nama customer') !== false ||
                    stripos((string)$cell, 'nama marketing') !== false
                )) {
                    $headerRowIndex = $index;
                    $headers = array_map(fn($h) => strtolower(trim((string)($h ?? ''))), $rowValues);
                    break 2;
                }
            }
        }

        if ($headerRowIndex === null) {
            $this->errors[] = 'Header row tidak ditemukan';
            return;
        }

        // Proses data setelah header
        foreach ($rows as $index => $row) {
            if ($index <= $headerRowIndex) continue;

            $rowData = $row->toArray();
            $mapped  = [];
            foreach ($headers as $i => $header) {
                $mapped[$header] = $rowData[$i] ?? null;
            }

            $name = $mapped['nama user'] ?? $mapped['nama customer'] ?? null;
            if (empty($name)) {
                $this->skipped++;
                continue;
            }

            $phone = $mapped['no hp'] ?? $mapped['nomor wa/hp'] ?? $mapped['no_hp'] ?? null;
            $phone = $phone ? (string)$phone : null;

            $waPhone = null;
            if ($phone) {
                $clean = preg_replace('/\D/', '', $phone);
                if (str_starts_with($clean, '0')) {
                    $clean = '62' . substr($clean, 1);
                } elseif (str_starts_with($clean, '8')) {
                    $clean = '62' . $clean;
                }
                $waPhone = $clean;
            }

            try {
                Lead::create([
                    'name'              => trim((string)$name),
                    'phone'             => $waPhone ?? $phone,
                    'wa_phone'          => $waPhone,
                    'email'             => $mapped['email'] ?? null,
                    'source'            => $mapped['sumber'] ?? $mapped['sumber leads'] ?? 'iklan meta',
                    'status'            => $this->mapStatus($mapped['kategori'] ?? $mapped['status'] ?? null),
                    'notes'             => $mapped['report fu'] ?? $mapped['catatan'] ?? null,
                    'created_by'        => auth()->id(),
                    'assigned_to'       => $this->findStaffByName($mapped['nama sales'] ?? $mapped['nama marketing'] ?? null),
                    'product_id'        => $this->findProductByName($mapped['produk'] ?? null),
                    'interest_type'     => $mapped['minat tipe'] ?? null,
                    'budget_range'      => $mapped['range budget'] ?? null,
                    'location_interest' => $mapped['lokasi minat'] ?? null,
                    'survey_plan'       => $mapped['rencana survey'] ?? null,
                    'survey_result'     => $mapped['hasil survey'] ?? null,
                    'cancel_reason'     => $mapped['alasan pending/batal'] ?? null,
                    'utj_status'        => isset($mapped['utj']) && strtolower((string)($mapped['utj'] ?? '')) === 'ya',
                    'follow_up_date'    => isset($mapped['tanggal follow up terakhir']) ? $this->parseDate($mapped['tanggal follow up terakhir']) : null,
                ]);
                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                $this->skipped++;
            }
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
