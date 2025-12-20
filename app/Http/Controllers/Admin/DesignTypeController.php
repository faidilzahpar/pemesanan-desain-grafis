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
    public function index()
    {
        $types = DesignType::orderBy('created_at', 'desc')
            ->paginate(10);

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
            // design_type_id dibuat otomatis oleh model melalui boot()
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
