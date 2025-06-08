<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran - Data Pribadi</title>
    {{-- Pastikan ini di-uncomment dan Anda sudah menjalankan `npm install && npm run dev` --}}
    {{-- @vite('resources/css/app.css') --}}
    {{-- Jika Anda tidak menggunakan Vite/Tailwind CLI, tambahkan CDN Tailwind: --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">
    <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="absolute top-0 right-0 p-6 flex items-center text-blue-700 font-semibold text-lg z-20">
            <span>Jadi bagian Kerja</span>
            <div class="ml-1 bg-blue-700 text-white w-8 h-8 flex items-center justify-center rounded text-xl font-bold">
                In
            </div>
        </div>

        {{-- Background gradient, pastikan z-index diatur dengan baik --}}
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-green-300 via-blue-300 to-indigo-300 opacity-50 z-0"></div>

        <div class="relative z-10 p-10 mt-10">
            <div class="flex justify-center mb-10 text-gray-500">
                <div class="flex flex-col items-center mx-4">
                    {{-- Active step --}}
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold mb-2">1</div>
                    <span class="text-blue-600 font-semibold">Data Pribadi</span>
                </div>
                <div class="flex flex-col items-center mx-4">
                    {{-- Inactive steps --}}
                    <div class="w-10 h-10 bg-gray-300 text-gray-700 rounded-full flex items-center justify-center font-bold mb-2">2</div>
                    <span>Detail Kontrak</span>
                </div>
                <div class="flex flex-col items-center mx-4">
                    {{-- Inactive steps --}}
                    <div class="w-10 h-10 bg-gray-300 text-gray-700 rounded-full flex items-center justify-center font-bold mb-2">3</div>
                    <span>Verifikasi</span>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 mb-6">Data Pribadi</h2>

            {{-- Menampilkan semua error dari backend --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form for Step 1 --}}
            <form action="{{ route('worker.register.store1') }}" method="post" id="personalDataForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="nama_depan" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="text" name="first_name" id="nama_depan" placeholder="Nama depan"
                                    value="{{ old('first_name', $data['first_name'] ?? '') }}"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('first_name') border-red-500 @enderror">
                                <p id="error-first_name" class="text-red-500 text-xs mt-1 @unless($errors->has('first_name')) hidden @endunless">
                                    {{ $errors->first('first_name') }}
                                </p>
                            </div>
                            <div>
                                <input type="text" name="last_name" id="nama_belakang" placeholder="Nama belakang"
                                    value="{{ old('last_name', $data['last_name'] ?? '') }}"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('last_name') border-red-500 @enderror">
                                <p id="error-last_name" class="text-red-500 text-xs mt-1 @unless($errors->has('last_name')) hidden @endunless">
                                    {{ $errors->first('last_name') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal lahir</label>
                        <input type="text" name="birthdate" id="tanggal_lahir" placeholder="DD/MM/YYYY (e.g., 20/03/2000)"
                            value="{{ old('birthdate', $data['birthdate'] ?? '') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('birthdate') border-red-500 @enderror">
                        <p id="error-birthdate" class="text-red-500 text-xs mt-1 @unless($errors->has('birthdate')) hidden @endunless">
                            {{ $errors->first('birthdate') }}
                        </p>
                    </div>

                    {{-- Nomor KTP --}}
                    <div>
                        <label for="nomor_ktp" class="block text-sm font-medium text-gray-700 mb-1">Nomor KTP</label>
                        <input type="text" name="nik" id="nomor_ktp" placeholder="Nomor KTP/NIK"
                            value="{{ old('nik', $data['nik'] ?? '') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nik') border-red-500 @enderror">
                        <p id="error-nik" class="text-red-500 text-xs mt-1 @unless($errors->has('nik')) hidden @endunless">
                            {{ $errors->first('nik') }}
                        </p>
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <input type="text" name="address" id="alamat" placeholder="Alamat Domisili"
                            value="{{ old('address', $data['address'] ?? '') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('address') border-red-500 @enderror">
                        <p id="error-address" class="text-red-500 text-xs mt-1 @unless($errors->has('address')) hidden @endunless">
                            {{ $errors->first('address') }}
                        </p>
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="gender"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md @error('gender') border-red-500 @enderror">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ (old('gender', $data['gender'] ?? '') == 'Laki-laki') ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ (old('gender', $data['gender'] ?? '') == 'Perempuan') ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        <p id="error-gender" class="text-red-500 text-xs mt-1 @unless($errors->has('gender')) hidden @endunless">
                            {{ $errors->first('gender') }}
                        </p>
                    </div>

                    {{-- Nomor Telepon --}}
                    <div>
                        <label for="nomor_telepon" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="tel" name="phone_number" id="nomor_telepon" placeholder="Contoh: 081234567890"
                            value="{{ old('phone_number', $data['phone_number'] ?? '') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('phone_number') border-red-500 @enderror">
                        <p id="error-phone_number" class="text-red-500 text-xs mt-1 @unless($errors->has('phone_number')) hidden @endunless">
                            {{ $errors->first('phone_number') }}
                        </p>
                    </div>
                </div>

                <div class="mt-10 text-center">
                    <input type="submit" value="Lanjutkan"
                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 cursor-pointer">
                </div>
            </form>
        </div>
    </div>
</body>
</html>