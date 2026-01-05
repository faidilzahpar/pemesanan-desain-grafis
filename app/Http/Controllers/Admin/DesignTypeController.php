<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\DesignType;
use App\Http\Controllers\Controller;

class DesignTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DesignType::query();

        // 1. LOGIKA SEARCH
        // (Search meliputi: Nama Jenis, Harga, Durasi, Deskripsi. Status tidak termasuk)
        if ($request->filled('tableSearch')) {
            $search = $request->tableSearch;
            $query->where(function($q) use ($search) {
                $q->where('nama_jenis', 'like', "%{$search}%")
                ->orWhere('harga', 'like', "%{$search}%")
                ->orWhere('durasi', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // 2. LOGIKA SORTING
        if ($request->filled('tableSortColumn') && $request->filled('tableSortDirection')) {
            $column = $request->tableSortColumn;
            $direction = $request->tableSortDirection === 'desc' ? 'desc' : 'asc';

            // Validasi kolom agar aman (sesuai nama kolom di DB)
            $validColumns = ['nama_jenis', 'harga', 'durasi', 'is_active'];

            if (in_array($column, $validColumns)) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $types = $query->paginate(10)->withQueryString();

        return view('admin.design-types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.design-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'durasi' => 'required|integer|min:1',
            'harga' => 'required|integer|min:0',
        ]);

        DesignType::create([
            'nama_jenis' => $request->nama_jenis,
            'deskripsi' => $request->deskripsi,
            'durasi' => $request->durasi,
            'harga' => $request->harga,
            'is_active' => true,
        ]);

        return redirect()->route('design-types.index')
                         ->with('success', 'Jenis desain berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $type = DesignType::findOrFail($id);

        return view('admin.design-types.show', compact('type'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $type = DesignType::findOrFail($id);

        return view('admin.design-types.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'durasi' => 'required|integer|min:1',
            'harga' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $type = DesignType::findOrFail($id);

        $type->update([
            'nama_jenis' => $request->nama_jenis,
            'deskripsi' => $request->deskripsi,
            'durasi' => $request->durasi,
            'harga' => $request->harga,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('design-types.index')
                         ->with('success', 'Jenis desain berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $type = DesignType::findOrFail($id);
        $type->delete();

        return redirect()->route('design-types.index')
                         ->with('success', 'Jenis desain berhasil dihapus.');
    }

    public function toggle($id)
    {
        $design = DesignType::findOrFail($id);

        $design->is_active = !$design->is_active;
        $design->save();

        return response()->json([
            'success' => true,
            'is_active' => $design->is_active
        ]);
    }
}
