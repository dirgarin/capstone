<?php

namespace App\Http\Controllers;

use App\Models\Tim;
use Illuminate\Http\Request;

class TimController extends Controller
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
                $data = Tim::orderBy('created_at', 'desc')->get();
                return view('tim.tim.index', compact('data'));
            } elseif (auth()->user()->role == 'mahasiswa') {
                $data = Tim::where('mahasiswa1_id', auth()->user()->mahasiswa->id)
                    ->orWhere('mahasiswa2_id', auth()->user()->mahasiswa->id)
                    ->orWhere('mahasiswa3_id', auth()->user()->mahasiswa->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                return view('mahasiswa.tim.index', compact('data'));
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

        if (auth()->user()->role == 'mahasiswa') {
            $mahasiswa = \App\Models\Mahasiswa::where('user_id', '!=', auth()->user()->id)
                ->where('status', 'approved')
                ->where(function ($query) {
                    $query->whereDoesntHave('tim1', function ($query) {
                        $query->where('status', 'approved');
                    })->WhereDoesntHave('tim2', function ($query) {
                        $query->where('status', 'approved');
                    })->WhereDoesntHave('tim3', function ($query) {
                        $query->where('status', 'approved');
                    });
                })
                ->get();
            return view('mahasiswa.tim.create', compact('mahasiswa'));
        } else if (auth()->user()->role == 'tim') {
            $mahasiswa = \App\Models\Mahasiswa::where('status', 'approved')
                ->where(function ($query) {
                    $query->whereDoesntHave('tim1', function ($query) {
                        $query->where('status', 'approved');
                    })->WhereDoesntHave('tim2', function ($query) {
                        $query->where('status', 'approved');
                    })->whereDoesntHave('tim3', function ($query) {
                        $query->where('status', 'approved');
                    });
                })
                ->get();

            return view('tim.tim.create', compact('mahasiswa'));
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
                    'nama_ketua' => 'required',
                    'mahasiswa2_id' => 'required|exists:mahasiswas,id',
                    'mahasiswa3_id' => 'required|exists:mahasiswas,id',
                ],
                [
                    'nama_ketua.required' => 'Nama ketua harus diisi',
                    'mahasiswa2_id.required' => 'Mahasiswa harus dipilih',
                    'mahasiswa2_id.exists' => 'Mahasiswa tidak valid',
                    'mahasiswa3_id.required' => 'Mahasiswa harus dipilih',
                    'mahasiswa3_id.exists' => 'Mahasiswa tidak valid',
                ]
            );

            $tim = new Tim();
            $tim->nama_ketua = $request->nama_ketua;
            $tim->mahasiswa1_id = auth()->user()->mahasiswa->id;
            $tim->mahasiswa2_id = $request->mahasiswa2_id;
            $tim->mahasiswa3_id = $request->mahasiswa3_id;
            $tim->save();

            return redirect()->route('home')->with('success', 'Pendaftaran tim berhasil, silahkan menunggu validasi dari tim capstone');
        } else if (auth()->user()->role == 'tim') {
            $request->validate(
                [
                    'nama_ketua' => 'required',
                    'mahasiswa1_id' => 'required|exists:mahasiswas,id',
                    'mahasiswa2_id' => 'required|exists:mahasiswas,id',
                    'mahasiswa3_id' => 'required|exists:mahasiswas,id',
                ],
                [
                    'nama_ketua.required' => 'Nama ketua harus diisi',
                    'mahasiswa1_id.required' => 'Mahasiswa 1 harus dipilih',
                    'mahasiswa1_id.exists' => 'Mahasiswa 1 tidak valid',
                    'mahasiswa2_id.required' => 'Mahasiswa 2 harus dipilih',
                    'mahasiswa2_id.exists' => 'Mahasiswa 2 tidak valid',
                    'mahasiswa3_id.required' => 'Mahasiswa 3 harus dipilih',
                    'mahasiswa3_id.exists' => 'Mahasiswa 3 tidak valid',
                ]
            );

            $tim = new Tim();
            $tim->nama_ketua = $request->nama_ketua;
            $tim->mahasiswa1_id = $request->mahasiswa1_id;
            $tim->mahasiswa2_id = $request->mahasiswa2_id;
            $tim->mahasiswa3_id = $request->mahasiswa3_id;
            $tim->status = 'approved';
            $tim->save();

            return redirect()->route('tim.index')->with('success', 'Pendaftaran tim berhasil');
        }

        return abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tim $tim)
    {
        if (auth()->guest()) {
            return abort(404);
        }

        if (auth()->user()->role == 'tim') {
            $data = $tim;
            return view('tim.tim.show', compact('data'));
        } elseif (auth()->user()->role == 'mahasiswa') {
            $data = $tim;
            return view('mahasiswa.tim.show', compact('data'));
        } elseif (auth()->user()->role == 'dosen_pembimbing' || auth()->user()->role == 'dosen_penguji') {
            $data = $tim;
            return view('dosen.tim.show', compact('data'));
        }

        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tim $tim)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tim $tim)
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

        $tim->status = $request->status;
        $tim->save();

        return redirect()->route('tim.index')->with('success', 'Status tim berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tim $tim)
    {
        //
    }
}
