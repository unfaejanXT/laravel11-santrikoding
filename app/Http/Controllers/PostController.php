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
        $request->validate([
            'image'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
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
    
    public function show($id) {
        // Mengambil data post berdasarkan ID yang diberikan
        // Menggunakan metode 'find' untuk mencari post dengan ID yang spesifik
        $post = Post::find($id);
    
        // Mengirimkan data post ke tampilan 'posts.show'
        // 'compact' digunakan untuk mengirimkan variabel $post ke view dengan nama yang sama
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post){
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request,Post $post){

        $request->validate([
            'image'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/'.$post->image);

            //update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content
            ]);

        } else {

            //update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        }

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);

    }

}
