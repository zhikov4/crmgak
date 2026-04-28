<?php

namespace App\Http\Controllers;

use App\Imports\LeadsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('file');
        $data = Excel::toArray([], $file);
        $rows = $data[0] ?? [];

        $headers = array_shift($rows);
        $preview = array_slice($rows, 0, 10);

        session(['import_path' => $file->store('imports')]);

        return view('import.preview', compact('headers', 'preview'));
    }

    public function import(Request $request)
    {
        $path = session('import_path');

        if (!$path) {
            return redirect()->route('import.index')
                ->with('error', 'File tidak ditemukan. Upload ulang.');
        }

        $import = new LeadsImport();
        Excel::import($import, storage_path('app/private/' . $path));

        session()->forget('import_path');

        return redirect()->route('leads.index')
            ->with('success', "Import selesai! {$import->imported} leads berhasil, {$import->skipped} dilewati.");
    }
}