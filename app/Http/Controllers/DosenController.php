<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->guest()) {
            return redirect()->route('dosen.create');
        } else {
            if (auth()->user()->role == 'tim') {
                $data = Dosen::orderBy('created_at', 'desc')->get();
                return view('tim.dosen.index', compact('data'));
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
        return view('guest.dosen.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'nama' => 'required',
                'nik' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'no_telp' => 'required',
                'bidang_keahlian' => 'required',
                'role' => 'required',
            ],
            [
                'nama.required' => 'Nama harus diisi',
                'nik.required' => 'NIK/NIP harus diisi',
                'email.required' => 'Email harus diisi',
                'email.email' => 'Email tidak valid',
                'email.unique' => 'Email sudah terdaftar',
                'no_telp.required' => 'No. Telp harus diisi',
                'bidang_keahlian.required' => 'Bidang Keahlian harus diisi',
                'role.required' => 'Role harus diisi',
            ]
        );

        $user = new \App\Models\User();
        $user->name = $request->nama;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'dosen_' . $request->role;
        $user->save();

        $dosen = new Dosen();
        $dosen->user_id = $user->id;
        $dosen->nama = $request->nama;
        $dosen->nik = $request->nik;
        $dosen->no_telp = $request->no_telp;
        $dosen->bidang_keahlian = $request->bidang_keahlian;
        $dosen->role = $request->role;
        $dosen->status = 'approved';
        $dosen->save();

        return redirect()->route('home')->with('success', 'Pendaftaran berhasil, silahkan menunggu konfirmasi dari tim capstone');
    }

    /**
     * Display the specified resource.
     */
    public function show(Dosen $dosen)
    {
        if (auth()->guest()) {
            return abort(404);
        }

        if (auth()->user()->role != 'tim') {
            return abort(404);
        }

        $data = $dosen;
        return view('tim.dosen.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dosen $dosen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dosen $dosen)
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

        $dosen->status = $request->status;
        $dosen->save();

        return redirect()->route('dosen.index')->with('success', 'Status dosen berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dosen $dosen)
    {
        //
    }
}
