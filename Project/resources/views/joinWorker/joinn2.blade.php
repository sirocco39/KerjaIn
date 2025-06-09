@extends('master.master-job_req')
@section('content')
    <x-join-worker.join-template :step="2">
        <div class = "row justify-content-center">
            <h5 class="col-12 col-md-10 fw-bold my-1">Detail Kontrak</h5>
            <div class="col-md-10 bg-white p-4">
                <form action="{{ route('worker.register.store2') }}" method="POST" id="contractForm">
                    @csrf
                    <ol class="px-1 text-justify">
                        <li>
                            <strong>Persyaratan Umum</strong><br>
                            - Pendaftar merupakan Warga Negara Indonesia (WNI) dan berusia minimal 17 tahun.<br>
                            - Memiliki identitas resmi yang masih berlaku, seperti KTP, SIM, atau Paspor.<br>
                            - Wajib menyediakan data diri yang valid, akurat, dan dapat diverifikasi.<br>
                            - Bersedia mengikuti seluruh prosedur pendaftaran dan verifikasi dari KerjaIn.<br>
                            - Tidak sedang menjalani hukuman pidana atau terlibat dalam kegiatan yang melanggar
                            hukum.<br><br>
                        </li>
                        <li>
                            <strong>Akun dan Verifikasi</strong><br>
                            - Pendaftar wajib memiliki akun yang terdaftar dan terverifikasi di platform KerjaIn.<br>
                            - Satu orang hanya diperbolehkan memiliki satu akun sebagai pekerja.<br>
                            - Akun bersifat pribadi dan tidak boleh dipindahtangankan atau digunakan oleh pihak lain.<br>
                            - KerjaIn berhak meminta dokumen tambahan untuk verifikasi identitas jika dibutuhkan.<br>
                            - Segala aktivitas di dalam akun menjadi tanggung jawab penuh pemilik akun.<br><br>
                        </li>
                        <li>
                            <strong>Komitmen dan Etika Kerja</strong><br>
                            - Pekerja wajib menyelesaikan setiap pekerjaan yang diterima sesuai dengan deskripsi dan waktu
                            yang telah disepakati.<br>
                            - Dilarang membatalkan pekerjaan secara sepihak tanpa alasan yang jelas atau pemberitahuan
                            sebelumnya.<br>
                            - Pekerja diharapkan menunjukkan sikap profesional, ramah, dan sopan dalam berinteraksi dengan
                            pengguna jasa.<br>
                            - Dilarang melakukan tindakan yang mengarah pada penipuan, pelecehan, kekerasan, atau hal lain
                            yang melanggar hukum.<br>
                            - KerjaIn berhak meninjau dan menangguhkan akun pekerja jika ditemukan pelanggaran etika
                            kerja.<br><br>
                        </li>
                        <li>
                            <strong>Sistem Pembayaran</strong><br>
                            - Upah atau imbalan atas pekerjaan diberikan sesuai kesepakatan yang dicantumkan dalam detail
                            pekerjaan.<br>
                            - Metode pembayaran dapat berupa tunai, transfer bank, atau dompet digital, sesuai yang
                            disetujui kedua belah pihak.<br>
                            - Pekerja bertanggung jawab terhadap pengelolaan penghasilan dan kewajiban perpajakan yang
                            berlaku.<br>
                            - KerjaIn dapat mengenakan biaya layanan atau potongan tertentu, yang akan diinformasikan secara
                            transparan.<br><br>
                        </li>
                        <li>
                            <strong>Tanggung Jawab dan Risiko</strong><br>
                            - Pekerja bertanggung jawab penuh atas hasil pekerjaan dan dampaknya kepada pengguna jasa.<br>
                            - KerjaIn tidak bertanggung jawab atas kerugian, kecelakaan, atau konflik yang terjadi di luar
                            platform.<br>
                            - Dalam hal terjadi sengketa, pekerja diharapkan menyelesaikannya dengan bijak dan dapat
                            menghubungi tim dukungan KerjaIn.<br>
                            - Pekerja wajib menjaga keamanan data pribadi milik pengguna jasa dan tidak menyebarkannya tanpa
                            izin.<br><br>
                        </li>
                        <li>
                            <strong>Pemutusan Kerja Sama</strong><br>
                            - KerjaIn berhak menonaktifkan akun pekerja secara sementara atau permanen jika ditemukan
                            pelanggaran terhadap syarat dan ketentuan ini.<br>
                            - Pekerja dapat menghapus akun atau mengundurkan diri kapan saja melalui pengaturan akun.<br>
                            - Dalam kondisi tertentu, KerjaIn dapat melakukan evaluasi berkala terhadap performa dan etika
                            pekerja.<br><br>
                        </li>
                        <li>
                            <strong>Perubahan Ketentuan</strong><br>
                            - KerjaIn dapat memperbarui atau mengubah syarat dan ketentuan ini sewaktu-waktu tanpa
                            pemberitahuan langsung.<br>
                            - Perubahan akan diinformasikan melalui aplikasi, situs web, atau email resmi.<br>
                            - Pekerja dianggap telah menyetujui perubahan tersebut jika tetap menggunakan layanan setelah
                            pembaruan dilakukan.<br><br>
                        </li>
                    </ol>


                    <div class="mt-5">

                        <input type="checkbox" name="agree_terms" id="agree_terms" class="form-checkbox"
                            {{ old('agree_terms') ? 'checked' : '' }}>
                        <span class="text-dark-gray">Saya setuju dengan <a href="#"
                                class="text-blue-600 hover:underline">Syarat dan Ketentuan KerjaIn</a></span>
                        <p id="error-agree_terms"
                            class="text-danger @unless ($errors->has('agree_terms')) hidden @endunless">
                            {{ $errors->first('agree_terms') }}
                        </p>


                        <input type="checkbox" name="agree_data_usage" id="agree_data_usage" class="form-checkbox"
                            {{ old('agree_data_usage') ? 'checked' : '' }}>
                        <span class="text-dark-gray">Saya bersedia data saya digunakan untuk keperluan
                            verifikasi dan keamanan</span>

                        <p id="error-agree_data_usage"
                            class="text-danger @unless ($errors->has('agree_data_usage')) hidden @endunless">
                            {{ $errors->first('agree_data_usage') }}
                        </p>
                    </div>
                    <div class="text-center mt-5">
                        <button type="submit" id="submitButton" class="btn bg-secondary text-white px-4">
                            Lanjut
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </x-join-worker.join-template>
@endsection
