<?php

namespace App\Http\Controllers;

use App\Models\TopikMandiri;
use Illuminate\Http\Request;

class TopikMandiriController extends Controller
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
                $data = TopikMandiri::orderBy('created_at', 'desc')->get();
                return view('tim.topik_mandiri.index', compact('data'));
            } elseif (auth()->user()->role == 'mahasiswa') {
                $data = TopikMandiri::orderBy('created_at', 'desc')->get();
                return view('mahasiswa.topik_mandiri.index', compact('data'));
            } elseif (auth()->user()->role == 'dosen_pembimbing') {
                $data = TopikMandiri::with('daftar_topik_mandiri')->where('dosen_id', auth()->user()->dosen->id)->orderBy('created_at', 'desc')->get();
                return view('dosen.topik_mandiri.index', compact('data'));
            } elseif (auth()->user()->role == 'dosen_penguji') {
                $data = TopikMandiri::with('daftar_topik_mandiri')->orderBy('created_at', 'desc')->get();
                return view('dosen.topik_mandiri.index', compact('data'));
            } else {
                abort(404);
            }
        }
    }

    public function pilih_dosen(TopikMandiri $topikMandiri)
    {
        if (auth()->guest()) {
            abort(404);
        }

        if (auth()->user()->role == 'mahasiswa') {
            $data = $topikMandiri;
            $dosen = \App\Models\Dosen::where('role', 'pembimbing')->get();
            return view('mahasiswa.topik_mandiri.pilih_dosen', compact('data', 'dosen'));
        } elseif (auth()->user()->role == 'tim') {
            $data = $topikMandiri;
            $dosen = \App\Models\Dosen::where('role', 'pembimbing')->get();
            return view('tim.topik_mandiri.pilih_dosen', compact('data', 'dosen'));
        }

        return abort(404);
    }

    public function proses_dosen(Request $request, TopikMandiri $topikMandiri)
    {
        if (auth()->guest()) {
            abort(404);
        }

        if (auth()->user()->role == 'mahasiswa') {
            $request->validate([
                'dosen_id' => 'required',
            ]);

            $topikMandiri->dosen_id = $request->dosen_id;
            $topikMandiri->status = 'pending';
            $topikMandiri->save();

            return redirect()->route('topik_mandiri.index')->with('success', 'Berhasil memilih dosen, menunggu persetujuan dosen');
        } elseif (auth()->user()->role == 'tim') {
            $request->validate([
                'dosen_id' => 'required',
            ]);

            $topikMandiri->dosen_id = $request->dosen_id;
            $topikMandiri->status = 'assigned';
            $topikMandiri->save();

            $daftarTopikMandiri = \App\Models\DaftarTopikMandiri::find($topikMandiri->daftar_topik_mandiri->id);
            $daftarTopikMandiri->status = 'assigned';
            $daftarTopikMandiri->save();

            $daftarTopik = new \App\Models\DaftarTopik();
            $daftarTopik->registerable_id = $topikMandiri->daftar_topik_mandiri->id;
            $daftarTopik->registerable_type = 'App\Models\DaftarTopikMandiri';
            $daftarTopik->save();

            return redirect()->route('topik_mandiri.index')->with('success', 'Berhasil assign dosen');
        }

        return abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->guest()) {
            abort(404);
        }

        if (auth()->user()->role == 'mahasiswa') {
            $tim = auth()->user()->mahasiswa->tim1->where('status', 'approved')->merge(auth()->user()->mahasiswa->tim2->where('status', 'approved'))->merge(auth()->user()->mahasiswa->tim3->where('status', 'approved'));
            return view('mahasiswa.topik_mandiri.create', compact('tim'));
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

        if (auth()->user()->role == 'mahasiswa') {
            $request->validate(
                [
                    'tim_id' => 'required|exists:tims,id',
                    'judul' => 'required',
                    'instansi' => 'required',
                ],
                [
                    'tim_id.required' => 'Tim harus dipilih',
                    'judul.required' => 'Judul harus diisi',
                    'instansi.required' => 'Instansi harus diisi',
                ]
            );

            $topikMandiri = new TopikMandiri();
            $topikMandiri->judul = $request->judul;
            $topikMandiri->instansi = $request->instansi;
            $topikMandiri->save();

            $daftarTopikMandiri = new \App\Models\DaftarTopikMandiri();
            $daftarTopikMandiri->tim_id = $request->tim_id;
            $daftarTopikMandiri->topik_mandiri_id = $topikMandiri->id;
            $daftarTopikMandiri->save();

            return redirect()->route('topik_mandiri.index')->with('success', 'Topik Mandiri berhasil dibuat');
        }

        return abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(TopikMandiri $topikMandiri)
    {
        if (auth()->guest()) {
            return abort(404);
        }

        if (auth()->user()->role == 'mahasiswa') {
            $data = $topikMandiri;
            $tim = auth()->user()->mahasiswa->tim1->where('status', 'approved')->merge(auth()->user()->mahasiswa->tim2->where('status', 'approved'))->merge(auth()->user()->mahasiswa->tim3->where('status', 'approved'));
            $can_register = true;
            // foreach ($topikMandiri->daftar_topik_dosen as $daftarTopikDosen) {
            //     if ($daftarTopikDosen->tim->mahasiswa1_id == auth()->user()->mahasiswa->id || $daftarTopikDosen->tim->mahasiswa2_id == auth()->user()->mahasiswa->id) {
            //         $can_register = false;
            //     }
            // }
            if ($data->daftar_topik_dosen->where('status', 'assigned')->count() >= $data->jumlah_tim) {
                $can_register = false;
            }
            return view('mahasiswa.topik_mandiri.show', compact('data', 'can_register', 'tim'));
        } else if (auth()->user()->role == 'dosen_pembimbing' || auth()->user()->role == 'dosen_penguji') {
            $data = $topikMandiri;
            return view('dosen.topik_mandiri.show', compact('data'));
        } else if (auth()->user()->role == 'tim') {
            $data = $topikMandiri;
            return view('tim.topik_mandiri.show', compact('data'));
        }

        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TopikMandiri $topikMandiri)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TopikMandiri $topikMandiri)
    {
        if (auth()->guest()) {
            return abort(404);
        }

        if (auth()->user()->role == 'dosen_pembimbing') {
            $request->validate(
                [
                    'status' => 'required|in:assigned,rejected',
                ],
                [
                    'status.required' => 'Status harus diisi',
                    'status.in' => 'Status tidak valid',
                ]
            );

            $topikMandiri->status = $request->status;
            $topikMandiri->save();

            if ($request->status == 'assigned') {
                $daftarTopikMandiri = \App\Models\DaftarTopikMandiri::find($topikMandiri->daftar_topik_mandiri->id);
                $daftarTopikMandiri->status = 'assigned';
                $daftarTopikMandiri->save();

                $daftarTopik = new \App\Models\DaftarTopik();
                $daftarTopik->registerable_id = $topikMandiri->daftar_topik_mandiri->id;
                $daftarTopik->registerable_type = 'App\Models\DaftarTopikMandiri';
                $daftarTopik->save();

                return redirect()->route('topik_mandiri.index')->with('success', 'Topik Mandiri berhasil disetujui');
            }

            return redirect()->route('topik_mandiri.index')->with('success', 'Topik Mandiri berhasil ditolak');

        } elseif (auth()->user()->role == 'tim') {
            $request->validate(
                [
                    'status' => 'required|in:pending,approved,rejected',
                ],
                [
                    'status.required' => 'Status harus diisi',
                    'status.in' => 'Status tidak valid',
                ]
            );

            $topikMandiri->daftar_topik_mandiri->daftar_topik->status = $request->status;
            $topikMandiri->daftar_topik_mandiri->daftar_topik->save();

            if ($request->status == 'approved') {
                return redirect()->route('topik_mandiri.show', $topikMandiri->id)->with('success', 'Pendaftaran Topik Mandiri berhasil disetujui');
            } else {
                return redirect()->route('topik_mandiri.show', $topikMandiri->id)->with('success', 'Pendaftaran Topik Mandiri berhasil ditolak');
            }
        }

        return abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TopikMandiri $topikMandiri)
    {
        //
    }
}
