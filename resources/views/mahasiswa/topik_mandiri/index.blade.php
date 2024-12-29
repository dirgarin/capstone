@extends('layout.app')

@section('contents')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Topik Mandiri</h1>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0">Data</h5>
                    <a href="{{ route('topik_mandiri.create') }}"
                        class="btn btn-primary btn-sm ms-auto rounded-3 fw-bold">
                        Pengajuan Topik
                    </a>
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
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                    <tr>
                                        <td class="text-center" style="width: 100px">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $item->judul }}</td>
                                        <td class="text-center">{{ $item->instansi }}</td>
                                        <td class="text-center">{{ $item->dosen->nama ?? "-" }}</td>
                                        <td class="text-center">
                                            @if ($item->status == "assigned")
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif ($item->status == "pending" && $item->dosen_id == null)
                                                <span class="badge bg-danger">Harap Pilih Dosen</span>
                                            @elseif ($item->status == "pending" && $item->dosen_id != null)
                                                <span class="badge bg-warning">Menunggu Konfirmasi Dosen</span>
                                            @else
                                                <span class="badge bg-danger">Dosen Menolak, Harap Pilih Dosen Lain</span>
                                            @endif
                                        </td>
                                        <td class="text-center" style="width: 100px;">
                                            @if (($item->status == "pending" && $item->dosen_id == null) || $item->status == "rejected")
                                                <a href="{{ route('topik_mandiri.pilih_dosen', $item->id) }}"
                                                    class="btn btn-danger">
                                                    <i class="fas fa-user-tag"></i>
                                                </a>
                                            @endif
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