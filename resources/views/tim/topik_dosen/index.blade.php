@extends('layout.app')

@section('contents')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Topik Dosen</h1>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0">Data</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-bordered table-hover"
                            style="width: 100%;">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 100px">No</th>
                                    <th>Judul</th>
                                    <th>Instansi / Organisasi / Perusahaan Objek</th>
                                    <th>Dosen</th>
                                    <th>Jumlah Tim Maksimal</th>
                                    <th>Jumlah Tim Terdaftar</th>
                                    @can('isTim')
                                        <th>Status</th>
                                    @endcan
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                                            <tr>
                                                                <td class="text-center" style="width: 100px">{{ $loop->iteration }}</td>
                                                                <td class="text-center">{{ $item->judul }}</td>
                                                                <td class="text-center">{{ $item->instansi }}</td>
                                                                <td class="text-center">{{ $item->dosen->nama }}</td>
                                                                <td class="text-center">{{ $item->jumlah_tim }}</td>
                                                                <td class="text-center">{{ $item->daftar_topik_dosen->count() }}</td>
                                                                @can('isTim')
                                                                                                @php
                                                                                                    $daftarTopikDosen = $item->daftar_topik_dosen->where('status', 'assigned');
                                                                                                @endphp
                                                                                                <td class="text-center">
                                                                                                    @if ($daftarTopikDosen->first() && $daftarTopikDosen->first()->daftar_topik && $daftarTopikDosen->first()->daftar_topik->where('status', 'approved')->count() >= $item->jumlah_tim)
                                                                                                        <span class="badge bg-success">Disetujui Tim Capstone</span>
                                                                                                    @elseif ($daftarTopikDosen->count() >= $item->jumlah_tim)
                                                                                                        <span class="badge bg-success">Penuh</span>
                                                                                                    @elseif ($item->status == "assigned")
                                                                                                        <span class="badge bg-success">Disetujui</span>
                                                                                                    @else
                                                                                                        <span class="badge bg-warning">Butuh Validasi</span>
                                                                                                    @endif
                                                                                                </td>
                                                                @endcan
                                                                <td class="text-center" style="width: 100px;">
                                                                    <a href="{{ route('topik_dosen.show', $item->id) }}" class="btn btn-warning">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Data tidak ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
