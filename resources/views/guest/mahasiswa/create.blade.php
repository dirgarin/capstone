@extends('layout.app')

@section('contents')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Pendaftaran Mahasiswa</h1>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('mahasiswa.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input type="number" class="form-control @error('nim') is-invalid @enderror" id="nim"
                                name="nim" value="{{ old('nim') }}" required>
                            @error('nim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">No. Telepon</label>
                            <input type="number" class="form-control @error('no_telp') is-invalid @enderror"
                                id="no_telp" name="no_telp" value="{{ old('no_telp') }}" required>
                            @error('no_telp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="bidang_minat" class="form-label">Bidang Minat</label>
                            <input type="text" class="form-control @error('bidang_minat') is-invalid @enderror"
                                id="bidang_minat" name="bidang_minat" value="{{ old('bidang_minat') }}" required>
                            @error('bidang_minat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="kompetensi" class="form-label">Kompetensi yang Dimiliki</label>
                            <textarea class="form-control @error('kompetensi') is-invalid @enderror" id="kompetensi"
                                name="kompetensi" required>{{ old('kompetensi') }}</textarea>
                            @error('kompetensi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="file_ktp" class="form-label">File KTP (PDF)</label>
                            <input type="file" class="form-control @error('file_ktp') is-invalid @enderror"
                                id="file_ktp" name="file_ktp" required accept="application/pdf"
                            @error('file_ktp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="file_khs" class="form-label">File KHS (PDF)</label>
                            <input type="file" class="form-control @error('file_khs') is-invalid @enderror"
                                id="file_khs" name="file_khs" required accept="application/pdf"
                            @error('file_khs')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="file_prasyarat" class="form-label">File Prasyarat (PDF)</label>
                            <input type="file" class="form-control @error('file_prasyarat') is-invalid @enderror"
                                id="file_prasyarat" name="file_prasyarat" required accept="application/pdf"
                            @error('file_prasyarat')
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
