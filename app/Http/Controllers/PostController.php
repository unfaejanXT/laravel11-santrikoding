<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index() {
        // Mengambil data post terbaru dari database
        // Menggunakan metode 'latest' untuk mendapatkan post terbaru berdasarkan tanggal pembuatan
        // Menggunakan 'paginate' untuk membatasi hasil per halaman sebanyak 5 post
        $posts = Post::latest()->paginate(5);
    
        // Mengirimkan data posts ke tampilan 'posts.index'
        // 'compact' digunakan untuk mengirimkan variabel $posts ke view dengan nama yang sama
        return view('posts.index', compact('posts'));
    }
    
    public function create() {
        // Menampilkan formulir untuk membuat post baru
        // Mengembalikan tampilan 'posts.create' yang berisi formulir pembuatan post
        return view('posts.create');
    }

    public function store(Request $request) {

        // Validasi input dari pengguna
        // Memastikan bahwa gambar, judul, dan konten disertakan dan memenuhi kriteria minimum
        $validated = $request->validate([
            'image'     => 'required|image',  // Menambahkan validasi tipe file untuk gambar
            'title'     => 'required|min:5',  // Judul harus ada dan minimal 5 karakter
            'content'   => 'required|min:10'  // Konten harus ada dan minimal 10 karakter
        ]);
    
        // Simpan gambar ke dalam direktori penyimpanan publik 'posts'
        $image = $request->file('image');
        // Menggunakan hashName untuk menghindari konflik nama file
        $imagePath = $image->storeAs('public/posts', $image->hashName());
    
        // Buat entri baru di database menggunakan model Post
        Post::create([
            'image'     => $image->hashName(),  // Nama file gambar yang disimpan di database
            'title'     => $request->title,     // Judul dari input pengguna
            'content'   => $request->content    // Konten dari input pengguna
        ]);
    
        // Redirect ke halaman index posts dengan pesan sukses
        return redirect()->route('posts.index')->with('success', 'Data Berhasil Disimpan');
    }
    
    

}
