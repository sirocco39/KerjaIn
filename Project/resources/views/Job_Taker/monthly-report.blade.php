@extends('Master.master-job_taker')

@section('content')
    <!-- Banner Section -->
    <section class="banner-section">
    <div class="container-fluid p-0">
        <div class="d-flex flex-wrap flex-lg-nowrap position-relative" style="min-height: 500px;">

        <!-- Konten Teks -->
        <div class="text-content custom-translate mb-5" style= "margin-left: 10px; margin-top: 40px;">
            <h1 style="font-weight: bold;">
                Hai <span style="color: #D3FA0D;">IwanJelek</span>, ini Rekap Hebatmu di Bulan Mei 2025!
            </h1>
        </div>
        
        <!-- Bulat-bulat Warna-warni -->
        <div class="color-dots" style="width: 300px; min-width: 300px; position: relative; height: 400px; z-index: 1;">
            <div class="dot dot-1"></div>
            <div class="dot dot-2"></div>
            <div class="dot dot-3"></div>
        </div>
        
        <!-- Create user avatar with name in line with avatar -->
        <div class="d-flex align-items-center" style="
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 2;
            margin-left: 10px; margin-top: 20px;">
            <img src="https://cdn-icons-png.freepik.com/512/9203/9203764.png" alt="" class="rounded-circle" style="width: 60px; height: 60px;">
            <div class="d-flex flex-column ms-2">
                <span style="font-size: 16px; font-weight: bold; color: white;">Iwan Jelek</span>
                <span style="font-size: 14px; color: #dfe6e9;">Pekerja</span>
            </div>
        </div>

        
    </div>
    
</div>
</section>

    <h1 style="margin-left: 120px; margin-top: 20px; font-size: 25px; font-weight: bold;">Aktivitas Saya</h1>

    <div class="stat-container" style="margin-right: 30px; background-color: white" >
        <div class="stat-card">
            <div class="stat-number">20</div>
            <p class="stat-label">Total pekerjaan diselesaikan</p>
        </div>
        <div class="stat-card">
            <div class="stat-number">20</div>
            <p class="stat-label">Total jam kerja</p>
        </div>
        <div class="stat-card">
            <div class="stat-number">20</div>
            <p class="stat-label">Total pendapatan</p>
        </div>
        <div class="stat-card">
            <div class="stat-number">20</div>
            <p class="stat-label">Rating rata-rata</p>
        </div>
        <div class="stat-card">
            <div class="stat-number">20</div>
            <p class="stat-label">Total Klien berbeda</p>
        </div>
    </div>

<div class="card-custom" style="margin-left: 120px; margin-top: 20px; margin-right: 120px">
        <h5>Pekerjaan</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pekerjaan</th>
                        <th>Lokasi</th>
                        <th>Klien</th>
                        <th>Durasi</th>
                        <th>Upah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2 Mei 2025</td>
                        <td>Jemput anak</td>
                        <td>Bogor</td>
                        <td>Bapak Wilson</td>
                        <td>1 jam</td>
                        <td>Rp 15.000</td>
                        <td>Selesai</td>
                    </tr>
                    <tr>
                        <td>2 Mei 2025</td>
                        <td>Jemput anak</td>
                        <td>Bogor</td>
                        <td>Bapak Wilson</td>
                        <td>1 jam</td>
                        <td>Rp 15.000</td>
                        <td>Selesai</td>
                    </tr>
                    <tr>
                        <td>2 Mei 2025</td>
                        <td>Jemput anak</td>
                        <td>Bogor</td>
                        <td>Bapak Wilson</td>
                        <td>1 jam</td>
                        <td>Rp 15.000</td>
                        <td>Selesai</td>
                    </tr>
                    <tr>
                        <td>2 Mei 2025</td>
                        <td>Jemput anak</td>
                        <td>Bogor</td>
                        <td>Bapak Wilson</td>
                        <td>1 jam</td>
                        <td>Rp 15.000</td>
                        <td>Selesai</td>
                    </tr>
                    <tr>
                        <td>2 Mei 2025</td>
                        <td>Jemput anak</td>
                        <td>Bogor</td>
                        <td>Bapak Wilson</td>
                        <td>1 jam</td>
                        <td>Rp 15.000</td>
                        <td>Selesai</td>
                    </tr>
                    <tr>
                        <td>2 Mei 2025</td>
                        <td>Jemput anak</td>
                        <td>Bogor</td>
                        <td>Bapak Wilson</td>
                        <td>1 jam</td>
                        <td>Rp 15.000</td>
                        <td>Selesai</td>
                    </tr>
                    <tr>
                        <td>2 Mei 2025</td>
                        <td>Jemput anak</td>
                        <td>Bogor</td>
                        <td>Bapak Wilson</td>
                        <td>1 jam</td>
                        <td>Rp 15.000</td>
                        <td>Selesai</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-5" style="margin-left: 120px; margin-top: 20px;">
            <div class="card-custom">
                <h5>Pendapatan</h5>
                
                <canvas id="revenue-chart-canvas" height="600" style="height: 300px; display: block; width: 485px;" width="970" class="chartjs-render-monitor"></canvas>
            </div>
        </div>
        <div class="col-md-5" style="margin-top: 20px; margin-left: 10px;">
            <div class="card-custom">
                <h5>Ulasan</h5>
                <div class="reviews-container">
                    <div class="review-item">
                        <img src="https://cdn-icons-png.freepik.com/512/9203/9203764.png" alt="Profile" class="rounded-circle" style="width: 60px; height: 60px;">
                        <div>
                            <strong>Bapak Wilson</strong><br>
                            <small>Pekerja</small><br>
                            ⭐⭐⭐⭐⭐
                            <p class="review-text">“Bapak ini baik sekali, sangat membantu, ramah sehingga saya memberikan bintang lima kemudian dia juga sanggup membangunkan toto yang tertidur lelap.”</p>
                        </div>
                    </div>
                    <div class="review-item">
                        <img src="https://cdn-icons-png.freepik.com/512/9203/9203764.png" alt="Profile" class="rounded-circle" style="width: 60px; height: 60px;">
                        <div>
                            <strong>Bapak Wilson</strong><br>
                            <small>Pekerja</small><br>
                            ⭐⭐⭐⭐⭐
                            <p class="review-text">“Bapak ini baik sekali, sangat membantu, ramah sehingga saya memberikan bintang lima kemudian dia juga sanggup membangunkan toto yang tertidur lelap.”</p>
                        </div>
                    </div>
                                        <div class="review-item">
                        <img src="https://cdn-icons-png.freepik.com/512/9203/9203764.png" alt="Profile" class="rounded-circle" style="width: 60px; height: 60px;">
                        <div>
                            <strong>Bapak Wilson</strong><br>
                            <small>Pekerja</small><br>
                            ⭐⭐⭐⭐⭐
                            <p class="review-text">“Bapak ini baik sekali, sangat membantu, ramah sehingga saya memberikan bintang lima kemudian dia juga sanggup membangunkan toto yang tertidur lelap.”</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="card-custom" style="margin-left: 120px; margin-top: 20px; margin-right: 120px">
        <h5>Laporan Bulanan</h5>

        <div class="content" style="text-align:center;">
            <p>Max 120 MB, PNG, JPEG</p>
            <i class="fas fa-download download-icon"></i><br>
            <button type="button" class="btn btn-primary btn-sm">Download</button>
        </div>
</div>   

@endsection