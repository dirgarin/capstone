<?php

namespace App\Http\Controllers;

use App\Models\TopikDosen;
use Illuminate\Http\Request;

class TopikDosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->guest()) {
            abort(404);
        } else {
            if (auth()->user()->role == 'tim') {
                $data = TopikDosen::orderBy('created_at', 'desc')->get();
                return view('tim.topik_dosen.index', compact('data'));
            } elseif (auth()->user()->role == 'mahasiswa') {
                $data = TopikDosen::where('status', 'assigned')->orderBy('created_at', 'desc')->get();
                return view('mahasiswa.topik_dosen.index', compact('data'));
            } elseif (auth()->user()->role == 'dosen_pembimbing' || auth()->user()->role == 'dosen_penguji') {
                $data = TopikDosen::orderBy('created_at', 'desc')->get();
                return view('dosen.topik_dosen.index', compact('data'));
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
        if (auth()->guest()) {
            abort(404);
        }

        if (auth()->user()->role == 'dosen_pembimbing') {
            return view('dosen.topik_dosen.create');
        }

        return abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if (auth()->guest()) {
            abort(404);
        }

        if (auth()->user()->role == 'dosen_pembimbing') {
            $request->validate(
                [
                    'judul' => 'required',
                    'instansi' => 'required',
                    'jumlah_tim' => 'required|numeric',
                ],
                [
                    'judul.required' => 'Judul harus diisi',
                    'instansi.required' => 'Instansi harus diisi',
                    'jumlah_tim.required' => 'Jumlah tim harus diisi',
                    'jumlah_tim.numeric' => 'Jumlah tim harus berupa angka',
                ]
            );

            $topikDosen = new TopikDosen();
            $topikDosen->dosen_id = auth()->user()->dosen->id;
            $topikDosen->judul = $request->judul;
            $topikDosen->instansi = $request->instansi;
            $topikDosen->jumlah_tim = $request->jumlah_tim;
            $topikDosen->save();

            return redirect()->route('topik_dosen.index')->with('success', 'Topik Dosen berhasil dibuat');
        }

        return abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(TopikDosen $topikDosen)
    {
        if (auth()->guest()) {
            return abort(404);
        }

        if (auth()->user()->role == 'mahasiswa') {
            $data = $topikDosen;
            $tim = auth()->user()->mahasiswa->tim1->where('status', 'approved')->merge(auth()->user()->mahasiswa->tim2->where('status', 'approved'))->merge(auth()->user()->mahasiswa->tim3->where('status', 'approved'));
            $can_register = true;
            // foreach ($topikDosen->daftar_topik_dosen as $daftarTopikDosen) {
            //     if ($daftarTopikDosen->tim->mahasiswa1_id == auth()->user()->mahasiswa->id || $daftarTopikDosen->tim->mahasiswa2_id == auth()->user()->mahasiswa->id) {
            //         $can_register = false;
            //     }
            // }
            if ($data->daftar_topik_dosen->where('status', 'assigned')->count() >= $data->jumlah_tim) {
                $can_register = false;
            }
            return view('mahasiswa.topik_dosen.show', compact('data', 'can_register', 'tim'));
        } else if (auth()->user()->role == 'dosen_pembimbing' || auth()->user()->role == 'dosen_penguji') {
            $data = $topikDosen;
            return view('dosen.topik_dosen.show', compact('data'));
        } else if (auth()->user()->role == 'tim') {
            $data = $topikDosen;
            return view('tim.topik_dosen.show', compact('data'));
        }

        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TopikDosen $topikDosen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TopikDosen $topikDosen)
    {
        if (auth()->guest()) {
            return abort(404);
        }

        if (auth()->user()->role != 'tim') {
            return abort(404);
        }

        $request->validate(
            [
                'status' => 'required|in:assigned,approved,rejected',
            ],
            [
                'status.required' => 'Status harus diisi',
                'status.in' => 'Status tidak valid',
            ]
        );

        if ($topikDosen->status == 'pending') {
            $topikDosen->status = $request->status;
            $topikDosen->save();
        } elseif ($topikDosen->status == 'assigned') {
            foreach ($topikDosen->daftar_topik_dosen as $daftarTopikDosen) {
                $daftar_topik = $daftarTopikDosen->daftar_topik;
                $d_id = $daftar_topik->id;
                $daftar_topik = \App\Models\DaftarTopik::find($d_id);
                $daftar_topik->status = $request->status;
                $daftar_topik->save();
            }
        }

        return redirect()->route('topik_dosen.index')->with('success', 'Status topik dosen berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TopikDosen $topikDosen)
    {
        //
    }
}
