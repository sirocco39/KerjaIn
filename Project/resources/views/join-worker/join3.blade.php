@extends('master.master-job-req')
@section('content')
    <x-join-worker.join-template :step="3">
        <div class="row justify-content-center">
            <h5 class="col-12 fw-bold my-1">Verifikasi</h5>
            <form class="row gy-2 px-0 justify-content-center" action="{{ route('worker.register.finalize') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="col-12">
                    <label class="manrope fw-bold fs-6">Upload Foto Diri</label>
                    <div class="upload-area" id="selfie-upload-area">
                        {{-- Input file --}}
                        <input type="file" name="photo_url" id="selfie_photo" accept="image/png, image/jpeg, image/jpg">

                        {{-- Ganti icon upload unicode jadi gambar --}}
                        <img class="upload-icon" src="{{ asset('Image/Icon/icon-upload.png') }}" alt="Upload Icon"
                            class="upload-icon">

                        <span class="upload-text">Max 5 MB, PNG, JPEG</span>
                        <span class="browse-button">Browse File</span>

                        {{-- Preview image (bisa tetap digunakan jika ada fitur preview) --}}
                        <img src="" alt="Selfie Preview" class="upload-preview" id="selfie-preview">

                    </div>

                    @error('photo_url')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="manrope fw-bold">Upload Foto KTP</label>
                    <div class="upload-area" id="ktp-upload-area">
                        {{-- Menggunakan name="id_card_url" dan ID "id_card_photo" sesuai panggilan JS --}}
                        <input type="file" name="id_card_url" id="id_card_photo"
                            accept="image/png, image/jpeg, image/jpg">
                        <img class="upload-icon" src="{{ asset('Image/Icon/icon-upload.png') }}" alt="Upload Icon"
                            class="upload-icon">
                        <span class="upload-text">Max 5 MB, PNG, JPEG</span>
                        <span class="browse-button">Browse File</span>
                        <img src="" alt="KTP Preview" class="upload-preview" id="ktp-preview">
                    </div>
                    @error('id_card_url')
                        {{-- Error tag sesuai name --}}
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-4">
                    <label class="manrope fw-bold">Upload Foto Diri dan KTP</label>
                    <div class="upload-area" id="selfie-ktp-upload-area">
                        {{-- Menggunakan name="selfie_with_id_card_url" dan ID "selfie_with_id_card_photo" --}}
                        <input type="file" name="selfie_with_id_card_url" id="selfie_with_id_card_photo"
                            accept="image/png, image/jpeg, image/jpg">
                        <img class="upload-icon" src="{{ asset('Image/Icon/icon-upload.png') }}" alt="Upload Icon"
                            class="upload-icon">
                        <span class="upload-text">Max 5 MB, PNG, JPEG</span>
                        <span class="browse-button">Browse File</span>
                        <img src="" alt="Selfie with KTP Preview" class="upload-preview" id="selfie-ktp-preview">
                    </div>
                    @error('selfie_with_id_card_url')
                        {{-- Error tag sesuai name --}}
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <p class="manrope fw-bold fs-6 my-0">Akun Pembayaran</p>
                <div class="col-12 py-3">
                    {{-- Anda bisa mengganti ini dengan path asset Anda --}}
                    <img src="{{ asset('Image/Icon/icon-bca.png') }}" class="payment-logo" alt="BCA">
                    {{-- Tambahkan logo lain jika diperlukan --}}
                </div>
                <div class="col-md-6">
                    <label class="fw-bold manrope fs-6" for="account_name">Nama Pemilik Rekening</label>
                    <input class="form-control inter bg-white border border-dark" type="text" name="account_name"
                        id="account_name" placeholder="Nama pemilik rekening"
                        value="{{ old('account_name', $step3Data['account_name'] ?? '') }}">
                    @error('account_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                <div class="col-md-6">
                    <label class="fw-bold manrope fs-6" for="account_number">Nomor Rekening</label>
                    <input class="form-control inter bg-white border border-dark" type="text" name="account_number"
                        id="account_number" placeholder="Nomor rekening"
                        value="{{ old('account_number', $step3Data['account_number'] ?? '') }}">
                    @error('account_number')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="col-md-4 col-8 btn bg-secondary text-white px-4 mt-5">Simpan dan
                    verifikasi</button>
            </form>
        </div>
    </x-join-worker.join-template>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto OCR when user uploads KTP
            const ktpImageInput = document.getElementById('id_card_photo'); // Correct ID for KTP input

            ktpImageInput.addEventListener('change', function(event) {
                const file = this.files[0];

                if (!file) {
                    // Also clear the preview if the file is removed
                    document.getElementById('ktp-preview').src = "";
                    document.getElementById('ktp-upload-area').classList.remove('has-image');
                    return;
                }

                const formData = new FormData();
                // FIX: Changed 'ktp_image' to 'image' to match the backend validation
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}'); // Laravel CSRF token

                fetch('{{ route('ktp.ocr.ajax') }}', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => {
                        // FIX: Check if the response is OK (2xx status) before trying to parse as JSON
                        if (!response.ok) {
                            // If not OK, read the response as text to get the HTML error page content
                            return response.text().then(text => {
                                console.error('Server responded with non-OK status:', response.status, text);
                                // You might want to display 'text' in a user-friendly way or log it.
                                throw new Error(`Server Error (${response.status}): ${text.substring(0, 200)}...`); // Limit error message length
                            });
                        }
                        // If OK, parse as JSON
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // NIK is now saved to session on backend, no need for frontend alert
                            console.log('OCR successful:', data);
                            // You might want to update a field with parsed data here, e.g., NIK
                            // if (data.parsed_data && data.parsed_data.nik) {
                            //     document.getElementById('nik_input_id').value = data.parsed_data.nik;
                            // }
                        } else {
                            console.error('OCR failed:', data.message);
                            // Clear file input and preview on failure
                            ktpImageInput.value = ''; // Reset file input
                            document.getElementById('ktp-preview').src = "";
                            document.getElementById('ktp-upload-area').classList.remove('has-image');
                        }
                    })
                    .catch(error => {
                        console.error('Error during OCR fetch:', error); // Changed message for clarity
                        // Clear file input and preview on error
                        ktpImageInput.value = '';
                        document.getElementById('ktp-preview').src = "";
                        document.getElementById('ktp-upload-area').classList.remove('has-image');
                    });
            });

            // Function to handle file preview (remains the same)
            function setupImagePreview(inputId, previewId, uploadAreaId) {
                const inputElement = document.getElementById(inputId);
                const previewElement = document.getElementById(previewId);
                const uploadArea = document.getElementById(uploadAreaId);

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
            }

            // Pastikan ID ini cocok dengan atribut 'id' pada elemen input file di HTML
            setupImagePreview('selfie_photo', 'selfie-preview', 'selfie-upload-area');
            setupImagePreview('id_card_photo', 'ktp-preview', 'ktp-upload-area');
            setupImagePreview('selfie_with_id_card_photo', 'selfie-ktp-preview', 'selfie-ktp-upload-area');
        });
    </script>
@endsection
