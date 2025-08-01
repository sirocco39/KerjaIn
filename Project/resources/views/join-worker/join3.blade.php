@extends('master.master-job-req')
<!-- This file name is join2.blade.php -->

@section('content')
<x-join-worker.join-template :step="3">
    <div class="row justify-content-center">
        <h5 class="col-12 fw-bold my-1">{{ __('join-worker.verification_title') }}</h5>
        <form class="row gy-2 px-0 justify-content-center" action="{{ route('worker.register.finalize') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="col-12">
                <label class="manrope fw-bold fs-6">{{ __('join-worker.upload_selfie_photo') }}</label>
                <div class="upload-area" id="selfie-upload-area">
                    
                    <input type="file" name="photo_url" id="selfie_photo" accept="image/png, image/jpeg, image/jpg">

                    
                    <img class="upload-icon" src="{{ asset('Image/Icon/icon-upload.png') }}" alt="Upload Icon"
                        class="upload-icon">

                    <span class="upload-text">{{ __('join-worker.max_file_size_info') }}</span>
                    <span class="browse-button">{{ __('join-worker.browse_file') }}</span>

                    
                    <img src="" alt="Selfie Preview" class="upload-preview" id="selfie-preview">

                </div>

                @error('photo_url')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="manrope fw-bold">{{ __('join-worker.upload_id_card_photo') }}</label>
                <div class="upload-area" id="ktp-upload-area">
                    
                    <input type="file" name="id_card_url" id="id_card_photo"
                        accept="image/png, image/jpeg, image/jpg">
                    <img class="upload-icon" src="{{ asset('Image/Icon/icon-upload.png') }}" alt="Upload Icon"
                        class="upload-icon">
                    <span class="upload-text">{{ __('join-worker.max_file_size_info') }}</span>
                    <span class="browse-button">{{ __('join-worker.browse_file') }}</span>
                    <img src="" alt="KTP Preview" class="upload-preview" id="ktp-preview">
                </div>
                @error('id_card_url')
                
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-4">
                <label class="manrope fw-bold">{{ __('join-worker.upload_selfie_with_id_card') }}</label>
                <div class="upload-area" id="selfie-ktp-upload-area">
                    
                    <input type="file" name="selfie_with_id_card_url" id="selfie_with_id_card_photo"
                        accept="image/png, image/jpeg, image/jpg">
                    <img class="upload-icon" src="{{ asset('Image/Icon/icon-upload.png') }}" alt="Upload Icon"
                        class="upload-icon">
                    <span class="upload-text">{{ __('join-worker.max_file_size_info') }}</span>
                    <span class="browse-button">{{ __('join-worker.browse_file') }}</span>
                    <img src="" alt="Selfie with KTP Preview" class="upload-preview" id="selfie-ktp-preview">
                </div>
                @error('selfie_with_id_card_url')
                
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <p class="manrope fw-bold fs-6 my-0">{{ __('join-worker.payment_account') }}</p>
            <div class="col-12 py-3">
                
                <img src="{{ asset('Image/Icon/icon-bca.png') }}" class="payment-logo" alt="BCA">
                
            </div>
            <div class="col-md-6">
                <label class="fw-bold manrope fs-6" for="account_name">{{ __('join-worker.account_holder_name') }}</label>
                <input class="form-control inter bg-white border border-dark" type="text" name="account_name"
                    id="account_name" placeholder="{{ __('join-worker.account_holder_name_placeholder') }}"
                    value="{{ old('account_name', $step3Data['account_name'] ?? '') }}">
                @error('account_name')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>


            <div class="col-md-6">
                <label class="fw-bold manrope fs-6" for="account_number">{{ __('join-worker.account_number') }}</label>
                <input class="form-control inter bg-white border border-dark" type="text" name="account_number"
                    id="account_number" placeholder="{{ __('join-worker.account_number_placeholder') }}"
                    value="{{ old('account_number', $step3Data['account_number'] ?? '') }}">
                @error('account_number')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="col-md-4 col-8 btn bg-secondary text-white px-4 mt-5">{{ __('join-worker.save_and_verify') }}</button>
        </form>
    </div>
</x-join-worker.join-template>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const ktpImageInput = document.getElementById('id_card_photo'); 

        ktpImageInput.addEventListener('change', function(event) {
            const file = this.files[0];

            if (!file) {
                
                document.getElementById('ktp-preview').src = "";
                document.getElementById('ktp-upload-area').classList.remove('has-image');
                return;
            }

            const formData = new FormData();
            
            formData.append('image', file);
            formData.append('_token', '{{ csrf_token() }}'); 

            fetch('{{ route('ktp.ocr.ajax') }}', {
                        method: 'POST',
                        body: formData,
                    })
                .then(response => {
                    
                    if (!response.ok) {
                        
                        return response.text().then(text => {
                            console.error('Server responded with non-OK status:', response.status, text);
                            
                            throw new Error(`Server Error (${response.status}): ${text.substring(0, 200)}...`); 
                        });
                    }
                    
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        
                        console.log('OCR successful:', data);
                        
                        
                        
                        
                    } else {
                        console.error('OCR failed:', data.message);
                        
                        ktpImageInput.value = ''; 
                        document.getElementById('ktp-preview').src = "";
                        document.getElementById('ktp-upload-area').classList.remove('has-image');
                    }
                })
                .catch(error => {
                    console.error('Error during OCR fetch:', error); 
                    
                    ktpImageInput.value = '';
                    document.getElementById('ktp-preview').src = "";
                    document.getElementById('ktp-upload-area').classList.remove('has-image');
                });
        });

        
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

        
        setupImagePreview('selfie_photo', 'selfie-preview', 'selfie-upload-area');
        setupImagePreview('id_card_photo', 'ktp-preview', 'ktp-upload-area');
        setupImagePreview('selfie_with_id_card_photo', 'selfie-ktp-preview', 'selfie-ktp-upload-area');
    });
</script>
@endsection