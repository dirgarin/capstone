<?php

namespace App\Http\Controllers;

use App\Models\DaftarTopikDosen;
use App\Models\TopikDosen;
use Illuminate\Http\Request;

class DaftarTopikDosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth()->guest()) {
            abort(404);
        }

        if (auth()->user()->role == 'mahasiswa') {
            $request->validate([
                'tim_id' => 'required',
                'topik_dosen_id' => 'required',
            ]);


            $topikDosen = TopikDosen::find($request->topik_dosen_id);
            $can_register = true;
            // foreach ($topikDosen->daftar_topik_dosen as $daftarTopikDosen) {
            //     if ($daftarTopikDosen->tim->mahasiswa1_id == auth()->user()->mahasiswa->id || $daftarTopikDosen->tim->mahasiswa2_id == auth()->user()->mahasiswa->id) {
            //         $can_register = false;
            //         break;
            //     }
            // }
            // if ($topikDosen->daftar_topik_dosen->where('status', '!=', 'rejected')->count() >= $topikDosen->jumlah_tim) {
            //     $can_register = false;
            // }
            if ($topikDosen->daftar_topik_dosen->where('status', 'assigned')->count() >= $topikDosen->jumlah_tim) {
                $can_register = false;
            }

            if (!$can_register) {
                return redirect()->route('topik_dosen.show', $request->topik_dosen_id)->with('error', 'Topik sudah penuh atau sudah terdaftar.');
            }

            $daftarTopikDosen = new DaftarTopikDosen();
            $daftarTopikDosen->tim_id = $request->tim_id;
            $daftarTopikDosen->topik_dosen_id = $request->topik_dosen_id;
            $daftarTopikDosen->save();

            return redirect()->route('topik_dosen.show', $request->topik_dosen_id)->with('success', 'Topik berhasil diajukan.');
        }

        return abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(DaftarTopikDosen $daftarTopikDosen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DaftarTopikDosen $daftarTopikDosen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DaftarTopikDosen $daftarTopikDosen)
    {
        if (auth()->guest()) {
            return abort(404);
        }

        if (auth()->user()->role != 'dosen_pembimbing') {
            return abort(404);
        }

        $request->validate(
            [
                'status' => 'required|in:assigned,rejected',
            ],
            [
                'status.required' => 'Status harus diisi',
                'status.in' => 'Status tidak valid',
            ]
        );

        if ($request->status == 'rejected') {
            $daftarTopikDosen->status = $request->status;
            $daftarTopikDosen->save();

            return redirect()->route('topik_dosen.show', $daftarTopikDosen->topik_dosen_id)->with('success', 'Tim berhasil ditolak.');
        }

        $daftarTopikDosen->status = $request->status;
        $daftarTopikDosen->save();

        $daftarTopik = new \App\Models\DaftarTopik();
        $daftarTopik->registerable_id = $daftarTopikDosen->id;
        $daftarTopik->registerable_type = 'App\Models\DaftarTopikDosen';
        $daftarTopik->save();

        // foreach ($daftarTopikDosen->topik_dosen->daftar_topik_dosen as $anotherDaftar) {
        //     if ($anotherDaftar->id != $daftarTopikDosen->id) {
        //         $anotherDaftar->status = 'rejected';
        //         $anotherDaftar->save();
        //     }
        // }

        return redirect()->route('topik_dosen.show', $daftarTopikDosen->topik_dosen_id)->with('success', 'Salah satu tim berhasil dipilih.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DaftarTopikDosen $daftarTopikDosen)
    {
        //
    }
}
