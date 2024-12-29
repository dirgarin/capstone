<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->guest()) {
            return redirect()->route('mahasiswa.create');
        } else {
            if (auth()->user()->role == 'tim') {
                $data = Mahasiswa::orderBy('created_at', 'desc')->get();
                return view('tim.mahasiswa.index', compact('data'));
            } else {
                abort(404);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('guest.mahasiswa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'nama' => 'required',
                'nim' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'no_telp' => 'required',
                'bidang_minat' => 'required',
                'kompetensi' => 'required',
                'file_ktp' => 'required|file|mimes:pdf',
                'file_khs' => 'required|file|mimes:pdf',
                'file_prasyarat' => 'required|file|mimes:pdf',
            ],
            [
                'nama.required' => 'Nama harus diisi',
                'nim.required' => 'NIM harus diisi',
                'email.required' => 'Email harus diisi',
                'email.email' => 'Email tidak valid',
                'email.unique' => 'Email sudah terdaftar',
                'password.required' => 'Password harus diisi',
                'no_telp.required' => 'No. Telp harus diisi',
                'bidang_minat.required' => 'Bidang Minat harus diisi',
                'kompetensi.required' => 'Kompetensi harus diisi',
                'file_ktp.required' => 'File KTP harus diisi',
                'file_ktp.file' => 'File KTP harus berupa file',
                'file_ktp.mimes' => 'File KTP harus berformat PDF',
                'file_khs.required' => 'File KHS harus diisi',
                'file_khs.file' => 'File KHS harus berupa file',
                'file_khs.mimes' => 'File KHS harus berformat PDF',
                'file_prasyarat.required' => 'File Prasyarat harus diisi',
                'file_prasyarat.file' => 'File Prasyarat harus berupa file',
                'file_prasyarat.mimes' => 'File Prasyarat harus berformat PDF',
            ]
        );

        $user = new \App\Models\User();
        $user->name = $request->nama;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'mahasiswa';
        $user->save();

        $mahasiswa = new Mahasiswa();
        $mahasiswa->user_id = $user->id;
        $mahasiswa->nama = $request->nama;
        $mahasiswa->nim = $request->nim;
        $mahasiswa->no_telp = $request->no_telp;
        $mahasiswa->bidang_minat = $request->bidang_minat;
        $mahasiswa->kompetensi = $request->kompetensi;
        $mahasiswa->file_ktp = $request->file_ktp->store('file_ktp', 'public');
        $mahasiswa->file_khs = $request->file_khs->store('file_khs', 'public');
        $mahasiswa->file_prasyarat = $request->file_prasyarat->store('file_prasyarat', 'public');
        $mahasiswa->save();

        return redirect()->route('home')->with('success', 'Pendaftaran berhasil, silahkan menunggu konfirmasi dari tim capstone');
    }

    /**
     * Display the specified resource.
     */
    public function show(Mahasiswa $mahasiswa)
    {
        if (auth()->guest()) {
            return abort(404);
        }

        if (auth()->user()->role != 'tim') {
            return abort(404);
        }

        $data = $mahasiswa;
        return view('tim.mahasiswa.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mahasiswa $mahasiswa)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        if (auth()->guest()) {
            return abort(404);
        }

        if (auth()->user()->role != 'tim') {
            return abort(404);
        }

        $request->validate(
            [
                'status' => 'required|in:approved,rejected',
            ],
            [
                'status.required' => 'Status harus diisi',
                'status.in' => 'Status tidak valid',
            ]
        );

        $mahasiswa->status = $request->status;
        $mahasiswa->save();

        return redirect()->route('mahasiswa.index')->with('success', 'Status mahasiswa berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mahasiswa $mahasiswa)
    {
        abort(404);
    }
}
