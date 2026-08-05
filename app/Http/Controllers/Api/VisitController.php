<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\VisitPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VisitController extends Controller
{
    /**
     * Daftar kunjungan milik sales yang login
     */
    public function index(Request $request)
    {
        $visits = Visit::with('photos')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('visited_at')
            ->paginate(20);

        return response()->json($visits);
    }

    /**
     * Simpan data kunjungan baru — langsung masuk CRM
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_name'      => 'required|string|max:255',
            'client_phone'     => 'nullable|string|max:20',
            'client_email'     => 'nullable|email|max:255',
            'client_company'   => 'nullable|string|max:255',
            'client_id'        => 'nullable|integer',
            'notes'            => 'nullable|string',
            'status'           => 'nullable|in:visited,follow_up,deal,cancel',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'location_address' => 'nullable|string|max:500',
            'visited_at'       => 'nullable|date',
        ]);

        $data['user_id']    = $request->user()->id;
        $data['visited_at'] = $data['visited_at'] ?? now();
        $data['status']     = $data['status'] ?? 'visited';

        $visit = Visit::create($data);

        return response()->json([
            'message' => 'Data kunjungan berhasil disimpan.',
            'visit'   => $visit->load('photos'),
        ], 201);
    }

    /**
     * Detail satu kunjungan
     */
    public function show(Request $request, Visit $visit)
    {
        // Pastikan hanya pemilik yang bisa lihat
        if ($visit->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json($visit->load('photos'));
    }

    /**
     * Upload foto proof ke Supabase Storage
     */
    public function uploadPhoto(Request $request, Visit $visit)
    {
        if ($visit->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'photo'   => 'required|image|max:5120', // maks 5MB
            'caption' => 'nullable|string|max:255',
        ]);

        $file    = $request->file('photo');
        $path    = "visits/{$visit->id}/" . uniqid() . '.' . $file->extension();

        // Upload ke Supabase Storage via Laravel filesystem (disk: supabase)
        Storage::disk('supabase')->put($path, file_get_contents($file));

        $url = Storage::disk('supabase')->url($path);

        $photo = VisitPhoto::create([
            'visit_id'   => $visit->id,
            'photo_url'  => $url,
            'photo_path' => $path,
            'caption'    => $request->caption,
        ]);

        return response()->json([
            'message' => 'Foto berhasil diupload.',
            'photo'   => $photo,
        ], 201);
    }

    /**
     * Sync batch — untuk data yang tersimpan offline di HP
     */
    public function sync(Request $request)
    {
        $request->validate([
            'visits'              => 'required|array',
            'visits.*.client_name' => 'required|string',
            'visits.*.visited_at' => 'required|date',
        ]);

        $saved = [];

        foreach ($request->visits as $item) {
            $item['user_id'] = $request->user()->id;
            $saved[]         = Visit::create($item);
        }

        return response()->json([
            'message' => count($saved) . ' kunjungan berhasil disinkronkan.',
            'visits'  => $saved,
        ]);
    }
}
