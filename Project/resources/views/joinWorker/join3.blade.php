<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi - Jadi Bagian KerjaIn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px; /* Lebar maksimum seperti di gambar */
            padding: 30px 40px;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
            margin: 0;
            font-weight: 600;
        }
        .header .logo img {
            height: 30px; /* Sesuaikan ukuran logo */
        }
        .progress-bar {
            display: flex;
            justify-content: space-around;
            margin-bottom: 40px;
            position: relative;
        }
        .progress-bar::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 10%;
            right: 10%;
            height: 2px;
            background-color: #e0e0e0;
            transform: translateY(-50%);
            z-index: 0;
        }
        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            font-size: 14px;
            color: #888;
        }
        .progress-step .circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .progress-step.active .circle {
            background-color: #007bff; /* Warna biru untuk langkah aktif */
        }
        .progress-step.completed .circle {
            background-color: #28a745; /* Warna hijau untuk langkah selesai */
        }
        .progress-step.active, .progress-step.completed {
            color: #333;
            font-weight: 600;
        }
        .form-section h2 {
            font-size: 20px;
            color: #444;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .upload-area {
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin-bottom: 20px;
            cursor: pointer;
            position: relative;
            transition: border-color 0.3s ease;
            height: 180px; /* Sesuaikan tinggi agar sesuai gambar */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden; /* Untuk handle gambar preview */
        }
        .upload-area:hover {
            border-color: #007bff;
        }
        .upload-area input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .upload-icon {
            font-size: 40px;
            color: #bbb;
            margin-bottom: 10px;
        }
        .upload-text {
            color: #888;
            margin-bottom: 5px;
        }
        .browse-button {
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ccc;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .upload-preview {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: none; /* Sembunyikan secara default */
        }
        .upload-area.has-image .upload-preview {
            display: block;
        }
        .upload-area.has-image .upload-icon,
        .upload-area.has-image .upload-text,
        .upload-area.has-image .browse-button {
            display: none;
        }

        .grid-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .form-group input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }
        .payment-logos {
            display: flex;
            gap: 20px;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .payment-logos img {
            height: 30px; /* Sesuaikan ukuran logo pembayaran */
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        .payment-logos img:hover {
            opacity: 1;
        }
        .submit-button {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
        .text-danger {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
        .upload-area.has-image {
            border-style: solid; /* Ganti dashed jadi solid setelah ada gambar */
        }
        .upload-area .overlay-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 255, 255, 0.8);
            padding: 5px 10px;
            border-radius: 5px;
            display: none;
            font-size: 12px;
            white-space: nowrap;
        }
        .upload-area:hover .overlay-text {
            display: block; /* Tampilkan saat hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Jadi Bagian KerjaIn</h1>
            <div class="logo">
                {{-- Anda bisa menempatkan logo LinkedIn atau logo aplikasi Anda di sini --}}
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/LinkedIn_logo_initials.png/480px-LinkedIn_logo_initials.png" alt="KerjaIn Logo" style="height: 30px;">
            </div>
        </div>

        <div class="progress-bar">
            <div class="progress-step completed">
                <div class="circle">1</div>
                <span>Data Pribadi</span>
            </div>
            <div class="progress-step completed">
                <div class="circle">2</div>
                <span>Detail Kontrak</span>
            </div>
            <div class="progress-step active">
                <div class="circle">3</div>
                <span>Verifikasi</span>
            </div>
        </div>

        <form action="{{ route('worker.register.finalize') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Menampilkan semua error validasi dari controller --}}
            @if ($errors->any())
                <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-section">
                <h2>Verifikasi</h2>

                <div class="form-group">
                    <label>Upload Foto Diri</label>
                    <div class="upload-area" id="selfie-upload-area">
                        {{-- Menggunakan name="photo_url" sesuai validasi controller --}}
                        <input type="file" name="photo_url" id="selfie_photo" accept="image/png, image/jpeg, image/jpg">
                        <span class="upload-icon">&#x2191;</span> {{-- Unicode up arrow --}}
                        <span class="upload-text">Max 120 MB, PNG, JPEG</span>
                        <span class="browse-button">Browse File</span>
                        <img src="" alt="Selfie Preview" class="upload-preview" id="selfie-preview">
                        <span class="overlay-text" id="selfie-overlay-text">Ganti Foto Diri</span>
                    </div>
                    @error('photo_url') {{-- Error tag sesuai name baru --}}
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid-columns">
                    <div class="form-group">
                        <label>Upload Foto KTP</label>
                        <div class="upload-area" id="ktp-upload-area">
                            {{-- Menggunakan name="id_card_url" dan ID "id_card_photo" sesuai panggilan JS --}}
                            <input type="file" name="id_card_url" id="id_card_photo" accept="image/png, image/jpeg, image/jpg">
                            <span class="upload-icon">&#x2191;</span>
                            <span class="upload-text">Max 120 MB, PNG, JPEG</span>
                            <span class="browse-button">Browse File</span>
                            <img src="" alt="KTP Preview" class="upload-preview" id="ktp-preview">
                            <span class="overlay-text" id="ktp-overlay-text">Ganti Foto KTP</span>
                        </div>
                        @error('id_card_url') {{-- Error tag sesuai name --}}
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Upload Foto Diri dan KTP</label>
                        <div class="upload-area" id="selfie-ktp-upload-area">
                            {{-- Menggunakan name="selfie_with_id_card_url" dan ID "selfie_with_id_card_photo" --}}
                            <input type="file" name="selfie_with_id_card_url" id="selfie_with_id_card_photo" accept="image/png, image/jpeg, image/jpg">
                            <span class="upload-icon">&#x2191;</span>
                            <span class="upload-text">Max 120 MB, PNG, JPEG</span>
                            <span class="browse-button">Browse File</span>
                            <img src="" alt="Selfie with KTP Preview" class="upload-preview" id="selfie-ktp-preview">
                            <span class="overlay-text" id="selfie-ktp-overlay-text">Ganti Foto Diri dan KTP</span>
                        </div>
                        @error('selfie_with_id_card_url') {{-- Error tag sesuai name --}}
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <h2>Akun Pembayaran <span style="font-weight: 400; color: #888;">(opsional tapi penting untuk nanti)</span></h2>
                </div>

                <div class="form-group">
                    <label for="account_name">Nama Rekening</label>
                    <input type="text" name="account_name" id="account_name" placeholder="Nama rekening" value="{{ old('account_name', $step3Data['account_name'] ?? '') }}">
                    @error('account_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="payment-logos">
                    {{-- Anda bisa mengganti ini dengan path asset Anda --}}
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/44/BCA_Bank_Central_Asia.svg/1200px-BCA_Bank_Central_Asia.svg.png" alt="BCA">
                    {{-- Tambahkan logo lain jika diperlukan --}}
                </div>

                <div class="form-group">
                    <label for="account_number">Nomor Rekening / E-Wallet</label>
                    <input type="text" name="account_number" id="account_number" placeholder="Nomor rekening" value="{{ old('account_number', $step3Data['account_number'] ?? '') }}">
                    @error('account_number')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="submit-button">Simpan dan verifikasi</button>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to handle file preview
        function setupImagePreview(inputId, previewId, uploadAreaId, overlayTextId) {
            const inputElement = document.getElementById(inputId);
            const previewElement = document.getElementById(previewId);
            const uploadArea = document.getElementById(uploadAreaId);
            const overlayText = document.getElementById(overlayTextId);

            inputElement.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewElement.src = e.target.result;
                        uploadArea.classList.add('has-image');
                    }
                    reader.readAsDataURL(file);
                } else {
                    previewElement.src = "";
                    uploadArea.classList.remove('has-image');
                }
            });

            // Optional: Show overlay text on hover if an image exists
            uploadArea.addEventListener('mouseenter', function() {
                if (uploadArea.classList.contains('has-image')) {
                    overlayText.style.display = 'block';
                }
            });
            uploadArea.addEventListener('mouseleave', function() {
                overlayText.style.display = 'none';
            });
        }

        // Pastikan ID ini cocok dengan atribut 'id' pada elemen input file di HTML
        setupImagePreview('selfie_photo', 'selfie-preview', 'selfie-upload-area', 'selfie-overlay-text');
        setupImagePreview('id_card_photo', 'ktp-preview', 'ktp-upload-area', 'ktp-overlay-text'); // ID input berubah menjadi 'id_card_photo'
        setupImagePreview('selfie_with_id_card_photo', 'selfie-ktp-preview', 'selfie-ktp-upload-area', 'selfie-ktp-overlay-text');
    });
    </script>
</body>
</html>