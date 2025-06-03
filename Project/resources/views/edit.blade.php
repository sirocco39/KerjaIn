<form action="edit" method="post">
    @csrf
    $post = $request;
    <div class="work-title">
        <label for="work-title-label">Judul Pekerjaan</label>
        <input type="text" id="work-title-text" name="workTitleLabel" placeholder="Contoh: Masangin AC Ruang Tamu" value="{{ old('workTitleLabel', $request->title) }}">
        <span style="color:red">@error('workTitleLabel'){{ $message }}
            @enderror</span>
    </div>
    <div class="work-detail">
        <label for="work-detail-label">Detail Pekerjaan</label>
        <input type="text" id="work-detail-text" name="workDetailLabel" placeholder="Contoh: Pasang AC 1 PK di ruang tamu bagian atas korden" value="{{ old('workDetailLabel', $post->description) }}">
        <span style="color:red">@error('workDetailLabel'){{ $message }}
            @enderror</span>
    </div>
    <div class="work-address">
        <label for="work-address-label">Alamat</label>
        <input type="text" id="work-address-text" name="workAddressLabel" placeholder="Contoh: Jalan Pakuan No3, Sentul" value="{{ old('workAddressLabel', $post->location) }}">
        <span style="color:red">@error('workAddressLabel'){{ $message }}
            @enderror</span>
    </div>
    <div class="work-start-time">
        <label for="work-start-time-label">Waktu mulai pekerjaan</label>
        <input type="date" id="work-start-date-text" name="workStartDateLabel" value="{{ old('workStartDateLabel', $post->start_time) }}">
        <input type="time" id="work-start-time-text1" name="workStartTimeLabel" value="{{ old('workStartTimeLabel', $post->start_time) }}">
        <span style="color:red">@error('workStartDateLabel'){{ "The start date and time is required" }}
            @enderror</span>
    </div>
    <div class="work-end-time">
        <label for="work-end-time-label">Waktu selesai pekerjaan</label>
        <input type="date" id="work-end-date-text" name="workEndDateLabel" value="{{ old('workEndDateLabel') }}">
        <input type="time" id="work-end-time-text1" name="workEndTimeLabel" value="{{ old('workEndTimeLabel') }}">
        {{-- <span style="color:red">@error('workEndDateLabel'){{ "The end date and time is required" }}@enderror</span> --}}
        @error('datetime')<span style="color:red">{{ $message }}</span>@enderror
    </div>
    <div class="work-price">
        <label for="work-price-label">Harga</label>
        <input type="number" id="work-price-text" name="workPriceLabel" placeholder="Min 5000" value="{{ old('workPriceLabel', $post->price) }}">
        <span style="color:red">@error('workPriceLabel'){{ $message }}
            @enderror</span>
    </div>
    <button type="submit">Selesai Edit</button>
</form>