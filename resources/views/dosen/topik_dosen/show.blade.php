@extends('layout.app')

@section('contents')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Topik Dosen</h1>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0">Detail</h5>
                    <a href="{{ route('topik_dosen.index') }}" class="btn btn-secondary btn-sm ms-auto rounded-3"><i
                            class="fas fa-arrow-left fa-2x"></i></a>
                </div>
                <div class="card-body">
                    <div class="row align-items-stretch g-3">
                        <div class="col-md-2 h-100">
                            <div class="card p-3 h-100">
                                <h5 class="card-title">Judul</h5>
                                <p class="card-text">{{ $data->judul }}</p>
                                <h5 class="card-title">Dosen</h5>
                                <p class="card-text">{{ $data->dosen->nama }}</p>
                                <h5 class="card-title">Instansi / Organisasi / Perusahaan Objek</h5>
                                <p class="card-text">{{ $data->instansi }}</p>
                                <h5 class="card-title">Jumlah Tim Maksimal</h5>
                                <p class="card-text">{{ $data->jumlah_tim }}</p>
                                <h5 class="card-title">Jumlah Tim yang Disetujui</h5>
                                <p class="card-text">
                                    {{ $data->daftar_topik_dosen->where('status', 'assigned')->count() }}
                                </p>
                                @if ($data->daftar_topik_dosen->where('status', 'assigned')->count() >= $data->jumlah_tim)
                                    <h5 class="card-title">Status</h5>
                                    <p class="card-text">
                                        <span class="badge bg-success">Sudah Terpilih</span>
                                        <br>
                                        <span class="small text-muted">
                                            Topik dosen ini sudah memiliki tim yang terpilih oleh dosen pembimbing.
                                        </span>
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-10 h-100">
                            <div class="card p-3 h-100">
                                <h5 class="card-title">Tim</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" style="width: 100%;">
                                        <thead>
                                            <tr class="text-center">
                                                <th style="width: 100px">No</th>
                                                <th>Nama Ketua/Tim</th>
                                                <th>Mahasiswa 1</th>
                                                <th>Mahasiswa 2</th>
                                                <th>Mahasiswa 3</th>
                                                <th>Terpilih</th>
                                                @can('isDosenPembimbing')
                                                    <th>Aksi</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($data->daftar_topik_dosen as $item)
                                                <tr>
                                                    <td class="text-center" style="width: 100px">{{ $loop->iteration }}</td>
                                                    <td class="text-center">{{ $item->tim->nama_ketua }}</td>
                                                    <td class="text-center">{{ $item->tim->mahasiswa1->user->name }}
                                                        ({{ $item->tim->mahasiswa1->nim }})</td>
                                                    <td class="text-center">{{ $item->tim->mahasiswa2->user->name }}
                                                        ({{ $item->tim->mahasiswa2->nim }})</td>
                                                    <td class="text-center">
                                                        {{ $item->tim->mahasiswa3->user->name }}
                                                        ({{ $item->tim->mahasiswa3->nim }})
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($item->status == "assigned")
                                                            <span class="badge bg-success">Terpilih</span>
                                                        @elseif ($item->status == "rejected")
                                                            <span class="badge bg-danger">Ditolak</span>
                                                        @else
                                                            <span class="badge bg-warning">Belum Terpilih</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('tim.show', $item->tim->id) }}"
                                                            class="btn btn-warning">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @can('isDosenPembimbing')
                                                            @if ($item->status == "pending")
                                                                <form action="{{ route('daftar_topik_dosen.update', $item->id) }}"
                                                                    method="post">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="assigned">
                                                                    <button type="submit" class="btn btn-success"
                                                                        onclick="return confirmMessage('Apakah anda yakin untuk memilih tim ini dan menolak tim lain?', event)">
                                                                        <i class="fas fa-check"></i> Pilih
                                                                    </button>
                                                                </form>
                                                                <form action="{{ route('daftar_topik_dosen.update', $item->id) }}"
                                                                    method="post">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="rejected">
                                                                    <button type="submit" class="btn btn-danger"
                                                                        onclick="return confirmMessage('Apakah anda yakin untuk menolak tim ini?', event)">
                                                                        <i class="fas fa-times"></i> Tolak
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">Belum ada tim yang terdaftar</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @can('isMahasiswa')
                            @if($can_register)
                                <div class="col-12 d-flex">
                                    <div class="ms-auto d-flex gap-2">
                                        <form action="{{ route('daftar_topik_dosen.store') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="topik_dosen_id" value="{{ $data->id }}">
                                            <select name="tim_id" class="form-select mb-2" required>
                                                <option value="">Pilih Rekan Tim</option>
                                                @foreach ($tim as $item)
                                                    <option value="{{ $item->id }}">{{ $item->mahasiswa2->nama }}
                                                        ({{ $item->mahasiswa2->nim }})</option>
                                                @endforeach
                                            </select>
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-primary"
                                                    onclick="return confirmMessage('Apakah anda yakin untuk mendaftar topik dosen ini?', event)">
                                                    Daftar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endcan
                        @can('isTim')
                            @if ($data->status == "pending")
                                <div class="col-12 d-flex">
                                    <div class="ms-auto d-flex gap-2">
                                        <form action="{{ route('topik_dosen.update', $data->id) }}" method="post">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="assigned">
                                            <button type="submit" class="btn btn-success"
                                                onclick="return confirmMessage('Apakah anda yakin untuk menyetujui topik dosen ini?', event)">
                                                <i class="fas fa-check"></i> Setuju
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection