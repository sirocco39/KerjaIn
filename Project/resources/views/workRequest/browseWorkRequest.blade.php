<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Kerja - Kerjain</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Your CSS from previous responses goes here */
        /* Make sure to externalize this CSS in a real project! */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f0f2f5;
        }
        .navbar {
            background-color: #ffffff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.2em;
            color: #333;
            text-decoration: none;
        }
        .navbar-links a {
            margin-left: 25px;
            text-decoration: none;
            color: #555;
            font-weight: 500;
        }
        .navbar-links a.active {
            color: #5b6de2; /* A shade of blue similar to the image */
            border-bottom: 2px solid #5b6de2;
            padding-bottom: 5px;
        }
        .navbar-right {
            display: flex;
            align-items: center;
        }
        .navbar-right .lang-select {
            margin-right: 25px;
            display: flex;
            align-items: center;
            color: #555;
        }
        .navbar-right .lang-select select {
            border: none;
            background: transparent;
            font-size: 1em;
            margin-left: 5px;
            outline: none;
            cursor: pointer;
        }
        .navbar-right .profile-icon {
            font-size: 1.5em;
            color: #555;
            cursor: pointer;
        }

        /* Main content area */
        .main-content {
            padding: 20px 30px;
        }

        /* Search bar section */
        .search-bar-container {
            background-color: #5b6de2; /* Blue background */
            padding: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            position: relative; /* For the subtle green/yellow curve */
            overflow: hidden;
        }
        .search-bar-container::before {
            content: '';
            position: absolute;
            bottom: -50px; /* Adjust to position the curve */
            right: -50px; /* Adjust to position the curve */
            width: 200px;
            height: 200px;
            background-color: #a7f3d0; /* Light green */
            border-radius: 50%;
            opacity: 0.3;
        }
        .search-bar-container::after {
            content: '';
            position: absolute;
            top: -30px; /* Adjust to position the curve */
            left: -30px; /* Adjust to position the curve */
            width: 150px;
            height: 150px;
            background-color: #fef08a; /* Light yellow */
            border-radius: 50%;
            opacity: 0.3;
        }

        .search-input-wrapper {
            background-color: #ffffff;
            border-radius: 25px;
            display: flex;
            align-items: center;
            padding: 8px 20px;
            flex-grow: 1;
            max-width: 600px; /* Limit search bar width */
        }
        .search-input-wrapper input {
            border: none;
            outline: none;
            padding: 5px 10px;
            flex-grow: 1;
            font-size: 1em;
        }
        .search-input-wrapper .search-icon {
            color: #888;
            margin-right: 10px;
        }
        .search-button {
            background-color: #ffd700; /* Gold color for button */
            color: #333;
            border: none;
            border-radius: 25px;
            padding: 10px 30px;
            font-weight: bold;
            cursor: pointer;
            margin-left: 20px;
            transition: background-color 0.3s ease;
        }
        .search-button:hover {
            background-color: #e6c200;
        }

        /* Job listing and details layout */
        .job-section {
            display: flex;
            gap: 20px;
            align-items: flex-start; /* Align items to the top */
        }

        .job-listings {
            flex: 2; /* Takes 2 parts of the available space */
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .job-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background-color: #fdfdfd;
            /* Removed cursor: pointer from here */
            transition: background-color 0.2s ease;
        }
        .job-card:hover {
            background-color: #f5f5f5;
        }
        .job-card-details {
            flex-grow: 1;
        }
        .job-card h3 {
            margin-top: 0;
            margin-bottom: 8px;
            color: #333;
            font-size: 1.1em;
        }
        .job-card p {
            margin: 3px 0;
            color: #666;
            font-size: 0.9em;
            display: flex;
            align-items: center;
        }
        .job-card p .icon {
            margin-right: 8px;
            color: #888;
        }
        .job-card .detail-button {
            background-color: #5b6de2; /* Main accent color */
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 0.85em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            white-space: nowrap; /* Prevent text wrapping */
            display: block; /* Make sure the button is displayed */
        }
        .job-card .detail-button:hover {
            background-color: #4a5bd1;
        }

        .job-details-placeholder {
            flex: 1; /* Takes 1 part of the available space */
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-align: left; /* Align text to left */
            display: flex;
            flex-direction: column;
            min-height: 400px; /* Give it a minimum height */
            position: sticky; /* Make it sticky */
            top: 20px; /* Stick it 20px from the top */
        }
        .job-details-placeholder .back-arrow {
            align-self: flex-start;
            font-size: 1.5em;
            color: #555;
            cursor: pointer;
            margin-bottom: 20px;
            visibility: hidden; /* Hidden by default */
        }
        .job-details-placeholder h2 {
            color: #333;
            font-size: 1.5em;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .job-details-placeholder p {
            color: #777;
            font-size: 0.9em;
            margin-bottom: 10px; /* Adjust spacing */
        }
        .job-details-placeholder .placeholder-img {
            max-width: 80%;
            height: auto;
            margin: 20px auto; /* Center the image */
            display: block; /* Ensure it behaves as a block for margin auto */
        }
        .job-details-placeholder .detail-description {
            margin-top: 15px;
            margin-bottom: 20px;
            line-height: 1.6;
            color: #444;
            white-space: pre-wrap; /* Preserve whitespace and breaks */
        }
        .job-details-placeholder .detail-price {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .detail-buttons {
            display: flex;
            gap: 10px;
            margin-top: auto; /* Push buttons to the bottom */
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .detail-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .detail-buttons .button-tawar {
            background-color: #e0f2f7; /* Light blue/cyan */
            color: #2196f3; /* Blue */
        }
        .detail-buttons .button-tawar:hover {
            background-color: #cdebf5;
        }
        .detail-buttons .button-hubungi {
            background-color: #fff3e0; /* Light orange */
            color: #ff9800; /* Orange */
        }
        .detail-buttons .button-hubungi:hover {
            background-color: #ffe8cc;
        }
        .detail-buttons .button-terima {
            background-color: #4CAF50; /* Green */
            color: white;
        }
        .detail-buttons .button-terima:hover {
            background-color: #45a049;
        }

        /* Pagination styles */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination nav {
            display: flex;
            gap: 5px;
        }
        .pagination nav .relative.inline-flex {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #5b6de2;
            background-color: #fff;
        }
        .pagination nav .relative.inline-flex span {
            color: #555;
        }
        .pagination nav .relative.inline-flex.bg-blue-500 {
            background-color: #5b6de2;
            color: white;
            border-color: #5b6de2;
        }
        .pagination nav a {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #5b6de2;
            background-color: #fff;
            transition: background-color 0.2s ease;
        }
        .pagination nav a:hover {
            background-color: #f0f0f0;
        }
        .pagination nav span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #aaa;
            background-color: #f9f9f9;
        }


        /* Media queries for responsiveness */
        @media (max-width: 768px) {
            .job-section {
                flex-direction: column;
            }
            .job-details-placeholder {
                margin-top: 20px;
                position: static; /* Remove sticky on mobile */
            }
            .search-bar-container {
                flex-direction: column;
                padding: 20px;
            }
            .search-input-wrapper {
                width: 100%;
                margin-bottom: 15px;
            }
            .search-button {
                width: 100%;
                margin-left: 0;
            }
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }
            .navbar-links {
                margin-top: 10px;
            }
            .navbar-links a {
                margin-left: 0;
                margin-right: 15px;
            }
            .navbar-right {
                margin-top: 10px;
            }
            .detail-buttons {
                flex-direction: column;
            }
            .detail-buttons button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#" class="navbar-brand">KerjaIN<br><small>Semua Bisa, Semua Kerja</small></a>
        <div class="navbar-links">
            <a href="#">Beranda</a>
            <a href="#" class="active">Cari Kerja</a>
            <a href="#">Pesan</a>
            <a href="#">Riwayat</a>
        </div>
        <div class="navbar-right">
            <div class="lang-select">
                <i class="fas fa-globe"></i>
                <select>
                    <option value="id">Bahasa</option>
                    <option value="en">English</option>
                </select>
                <i class="fas fa-caret-down"></i>
            </div>
            <i class="fas fa-user-circle profile-icon"></i>
        </div>
    </div>

    <div class="main-content">
        <form action="{{ route('browse.work.requests.index') }}" method="GET" class="search-bar-container">
            <div class="search-input-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" placeholder="Cari Pekerjaan" value="{{ request('search') }}">
            </div>
            <button type="submit" class="search-button">CARI</button>
        </form>

        <div class="job-section">
            <div class="job-listings">
                @forelse ($workRequests as $request)
                    {{-- Removed onclick from job-card div --}}
                    <div class="job-card">
                        <div class="job-card-details">
                            <h3>{{ $request->title }}</h3>
                            <p><i class="fas fa-map-marker-alt icon"></i>{{ $request->location }}</p>
                            <p><i class="fas fa-calendar-alt icon"></i>{{ $request->start_time->format('d M Y') }}</p>
                            <p><i class="fas fa-clock icon"></i>{{ $request->start_time->format('H.i') }} - {{ $request->end_time->format('H.i') }}</p>
                            <p><i class="fas fa-dollar-sign icon"></i>Rp{{ number_format($request->price, 2, ',', '.') }}</p>
                        </div>
                        {{-- Added onclick to the button itself --}}
                        <button class="detail-button" onclick="showRequestDetails({{ $request->id }})">DETAIL</button>
                    </div>
                @empty
                    <p>Tidak ada lowongan kerja yang ditemukan.</p>
                @endforelse

                {{-- Pagination Links --}}
                <div class="pagination">
                    {{ $workRequests->links() }}
                </div>
            </div>

            <div class="job-details-placeholder" id="job-details-panel">
                {{-- Initial content for the right panel --}}
                <i class="fas fa-chevron-left back-arrow" id="back-arrow" onclick="hideRequestDetails()"></i>
                <h2 id="detail-title">Pilih Lowongan Kerja di Kiri</h2>
                <p id="detail-instruction">Tampilkan Detail Di sini</p>
                <img src="https://i.ibb.co/C0wR5s1/holding-magnifying-glass-cartoon.png" alt="holding magnifying glass cartoon" class="placeholder-img" id="detail-image">

                {{-- These elements will be dynamically populated by JavaScript --}}
                {{-- Hide them initially, show when details are loaded --}}
                <div id="dynamic-details-content" style="display: none;">
                    <p><i class="fas fa-map-marker-alt icon"></i><span id="detail-location"></span></p>
                    <p><i class="fas fa-calendar-alt icon"></i><span id="detail-date"></span></p>
                    <p><i class="fas fa-clock icon"></i><span id="detail-time"></span></p>
                    <h3 class="detail-description">Deskripsi:</h3>
                    <p id="detail-description-text"></p>
                    <p class="detail-price">Rp<span id="detail-price-value"></span></p>

                    <div class="detail-buttons">
                        <button class="button-tawar">Tawar</button>
                        <button class="button-hubungi">Hubungi</button>
                        <button class="button-terima">Terima</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk format mata uang Rupiah
        const formatRupiah = (amount) => {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        };

        // Fungsi untuk menampilkan detail permintaan kerja
        function showRequestDetails(requestId) {
            const detailsPanel = document.getElementById('job-details-panel');
            const backArrow = document.getElementById('back-arrow');
            const detailTitle = document.getElementById('detail-title');
            const detailInstruction = document.getElementById('detail-instruction');
            const detailImage = document.getElementById('detail-image');
            const dynamicContent = document.getElementById('dynamic-details-content');

            // Show loading state or clear previous content
            detailTitle.textContent = 'Memuat Detail...';
            detailInstruction.textContent = 'Silakan tunggu.';
            detailInstruction.style.display = 'block'; // Ensure instruction is visible during loading
            detailImage.style.display = 'block'; // Show placeholder image
            dynamicContent.style.display = 'none'; // Hide dynamic content
            backArrow.style.visibility = 'hidden'; // Hide back arrow temporarily

            // Construct the URL using Laravel's route helper (via JS global var or direct string)
            const url = `{{ route('work_requests.show', ['request' => ':requestId']) }}`.replace(':requestId', requestId);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Populate the detail panel with fetched data
                    detailTitle.textContent = data.title;
                    detailInstruction.style.display = 'none'; // Hide "Tampilkan Detail Di sini" after loading
                    detailImage.style.display = 'none'; // Hide the default image

                    document.getElementById('detail-location').textContent = data.location;
                    document.getElementById('detail-date').textContent = data.display_date;
                    document.getElementById('detail-time').textContent = data.display_time_range;
                    document.getElementById('detail-description-text').textContent = data.description;
                    document.getElementById('detail-price-value').textContent = formatRupiah(data.price);

                    dynamicContent.style.display = 'block'; // Show the populated dynamic content
                    backArrow.style.visibility = 'visible'; // Show back arrow
                })
                .catch(error => {
                    console.error('Error fetching request details:', error);
                    detailTitle.textContent = 'Gagal Memuat Detail';
                    detailInstruction.textContent = 'Terjadi kesalahan saat memuat detail pekerjaan. Silakan coba lagi.';
                    detailInstruction.style.display = 'block';
                    detailImage.style.display = 'block';
                    dynamicContent.style.display = 'none';
                    backArrow.style.visibility = 'hidden';
                });
        }

        // Fungsi untuk menyembunyikan detail permintaan kerja dan mengembalikan ke tampilan awal
        function hideRequestDetails() {
            const backArrow = document.getElementById('back-arrow');
            const detailTitle = document.getElementById('detail-title');
            const detailInstruction = document.getElementById('detail-instruction');
            const detailImage = document.getElementById('detail-image');
            const dynamicContent = document.getElementById('dynamic-details-content');

            detailTitle.textContent = 'Pilih Lowongan Kerja di Kiri';
            detailInstruction.textContent = 'Tampilkan Detail Di sini';
            detailInstruction.style.display = 'block';
            detailImage.style.display = 'block'; // Show placeholder image
            dynamicContent.style.display = 'none'; // Hide dynamic content
            backArrow.style.visibility = 'hidden'; // Hide back arrow
        }

        // Call hideRequestDetails on page load to ensure initial state is correct
        document.addEventListener('DOMContentLoaded', hideRequestDetails);
    </script>
</body>
</html>