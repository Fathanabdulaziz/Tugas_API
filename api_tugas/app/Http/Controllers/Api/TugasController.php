<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function index(Request $request)
{
    $query = Tugas::query();

    if ($request->has('nama_tugas')) {
        $query->where('nama_tugas', 'like', '%' . $request->nama_tugas . '%');
    }

    return $query->get();
}

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'deadline' => 'required|date',
        ]);

        $path = $request->file('gambar')->store('tugas', 'public');

        $tugas = Tugas::create([
            'nama_tugas' => $request->nama_tugas,
            'gambar' => $path,
            'deadline' => $request->deadline,
        ]);

        return response()->json($tugas, 201);
    }

    public function show($id)
    {
        return Tugas::findOrFail($id);
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);
        Storage::disk('public')->delete($tugas->gambar);
        $tugas->delete();

        return response()->json(null, 204);
    }
    public function update(Request $request, $id)
{
    $request->validate([
        'nama_tugas' => 'sometimes|required|string|max:255',
        'gambar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        'deadline' => 'sometimes|required|date',
    ]);

    $tugas = Tugas::findOrFail($id);

    if ($request->hasFile('gambar')) {
        Storage::disk('public')->delete($tugas->gambar);
        $path = $request->file('gambar')->store('tugas', 'public');
        $tugas->gambar = $path;
    }

    if ($request->has('nama_tugas')) {
        $tugas->nama_tugas = $request->nama_tugas;
    }

    if ($request->has('deadline')) {
        $tugas->deadline = $request->deadline;
    }

    $tugas->save();

    return response()->json($tugas);
}
}
