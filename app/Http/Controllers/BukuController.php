<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()

    {
        return Buku::all();
    }

    /**
     *
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'harga' => 'required|integer|min:1000',
            'stok' => 'required|integer',
            'kategori_id' => 'required|integer|exists:kategoris,id'
        ]);

        $buku = Buku::create($request->all());
        return response()->json($buku, 201);
    }

    public function search(Request $request)
    {
        try {
            $kategori = $request->input('kategori');
            $judul = $request->input('judul');
            $penulis = $request->input('penulis');

            $buku = Buku::when($kategori, function ($query) use ($kategori) {
                $query->whereHas('kategori', function ($query) use ($kategori) {
                    $query->where('nama_kategori', 'like', '%' . $kategori . '%');
                });
            })
            ->when($judul, function ($query) use ($judul) {
                $query->where('judul', 'like', '%' . $judul . '%');
            })
            ->when($penulis, function($query)use($penulis) {
                $query->where('penulis', 'like', '%' . $penulis . '%');
            }
            )
            ->get();

            return response()->json($buku, 202);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat mencari buku',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $buku = Buku::findOrFail($id);

        return response()->json($buku);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'judul' => 'string|max:255',
                'penulis' => 'string|max:255',
                'harga'=>'integer|min:1000',
                'stok' => 'integer',
                'kategori_id' => 'integer|exists:kategoris,id'
            ]);

            $buku = Buku::findOrFail($id);
            $buku->update($request->all());

            return response()->json($buku);
        }catch(\Exception $e){
            return response()->json(['error'=> $e -> getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $id = $request->input('id');
            $judul = $request->input('judul');

            if ($id) {
                $buku = buku ::findOrFail($id);
            } elseif($judul) {
                $buku = buku::where('judul', 'like', '%' . $judul . '%')->firstOrFail();
            }else {
                return response()->json([
                    'error'=> 'harap masukkan ID atau judul buku yang ingin dihapus'
                ], 444);
            }

            $buku->delete();
            return response()->json(['message' => 'buku telah berhasil dihapus'],203);
        } catch(\Exception $e) {
            return response()->json([
                'error'=> 'terjadi kesalahan saat mengghapus buku!!!',
                'message' => $e->getMessage()
            ], 504);
        }
    }
}
