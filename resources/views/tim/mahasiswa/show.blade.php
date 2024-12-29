@extends('layout.app')

@section('contents')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Mahasiswa</h1>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0">Detail</h5>
                    <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary btn-sm ms-auto rounded-3"><i
                            class="fas fa-arrow-left fa-2x"></i></a>
                </div>
                <div class="card-body">
                    <div class="row align-items-stretch g-3">
                        <div class="col-md-2 h-100">
                            <div class="card p-3 h-100">
                                <h5 class="card-title">Nama Lengkap</h5>
                                <p class="card-text">{{ $data->nama }}</p>
                                <h5 class="card-title">NIM</h5>
                                <p class="card-text">{{ $data->nim }}</p>
                                <h5 class="card-title">Email</h5>
                                <p class="card-text">{{ $data->user->email }}</p>
                                <h5 class="card-title">No. Telepon</h5>
                                <p class="card-text">{{ $data->no_telp }}</p>
                                <h5 class="card-title">Bidang Minat</h5>
                                <p class="card-text">{{ $data->bidang_minat }}</p>
                                <h5 class="card-title">Kompetensi Keahlian</h5>
                                <p class="card-text">{{ $data->kompetensi }}</p>
                                <h5 class="card-title">Tanggal Pendaftaran</h5>
                                <p class="card-text">{{ $data->created_at->format('d F Y H:i:s') }}</p>
                                <h5 class="card-title">Status Validasi</h5>
                                <p class="card-text">
                                    @if ($data->status == "approved")
                                        <span class="badge bg-success">Terdaftar</span>
                                    @elseif($data->status == "rejected")
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-warning">Butuh Validasi</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-10 h-100">
                            <div class="row h-100">
                                <div class="col-md-4">
                                    <div class="card p-3">
                                        <h5 class="card-title">File KTP</h5>
                                        <object class="pdf" data="{{ Storage::url($data->file_ktp) }}" width="100%"
                                            height="400px"></object>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card p-3">
                                        <h5 class="card-title">File KHS</h5>
                                        <object class="pdf" data="{{ Storage::url($data->file_khs) }}" width="100%"
                                            height="400px"></object>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card p-3">
                                        <h5 class="card-title">File Prasyarat</h5>
                                        <object class="pdf" data="{{ Storage::url($data->file_prasyarat) }}"
                                            width="100%" height="400px"></object>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($data->status == "pending")
                            <div class="col-12 d-flex">
                                <div class="ms-auto d-flex gap-2">
                                    <form action="{{ route('mahasiswa.update', $data->id) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-success"
                                            onclick="return confirmMessage('Apakah anda yakin untuk menerima mahasiswa ini?', event)">
                                            <i class="fas fa-check"></i> Terima
                                        </button>
                                    </form>
                                    <form action="{{ route('mahasiswa.update', $data->id) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirmMessage('Apakah anda yakin untuk menolak mahasiswa ini?', event)">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection