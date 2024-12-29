@extends('layout.app')

@section('contents')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Pendaftaran Tim</h1>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tim.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_ketua" class="form-label">Nama Ketua/Tim</label>
                            <input type="text" class="form-control @error('nama_ketua') is-invalid @enderror"
                                id="nama_ketua" name="nama_ketua" value="{{ old('nama_ketua') }}" required>
                            @error('nama_ketua')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="mahasiswa2_id" class="form-label">Mahasiswa Pertama</label>
                            <select class="form-select @error('mahasiswa2_id') is-invalid @enderror" id="mahasiswa2_id"
                                name="mahasiswa2_id" required>
                                <option value="">Pilih Mahasiswa</option>
                                @foreach ($mahasiswa as $mhs)
                                    <option value="{{ $mhs->id }}">{{ $mhs->nama }} ({{ $mhs->bidang_minat }})</option>
                                @endforeach
                            </select>
                            @error('mahasiswa2_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="mahasiswa3_id" class="form-label">Mahasiswa Kedua</label>
                            <select class="form-select @error('mahasiswa3_id') is-invalid @enderror" id="mahasiswa3_id"
                                name="mahasiswa3_id" required>
                                <option value="">Pilih Mahasiswa</option>
                                @foreach ($mahasiswa as $mhs)
                                    <option value="{{ $mhs->id }}">{{ $mhs->nama }} ({{ $mhs->bidang_minat }})</option>
                                @endforeach
                            </select>
                            @error('mahasiswa3_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Daftar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection