<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Report;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\VerificationRequest;
use App\Http\Controllers\Controller;
use App\Models\Request as ModelRequest;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mendapatkan total pengguna
        $totalUsers = User::count();

        // Mendapatkan total pekerjaan (jobs) yang tersedia atau aktif
        // Asumsi ada kolom 'status' di tabel jobs jika Anda ingin memfilter
        // $totalJobs = Job::count();

        // Mendapatkan total permintaan (requests)
        $totalRequests = ModelRequest::count();

        // Mendapatkan total transaksi
        $totalTransactions = Transaction::count();

        // Mendapatkan jumlah laporan yang perlu ditinjau
        $pendingReports = Report::where('status', 'Not Reviewed')->count();

        // Mendapatkan jumlah permintaan verifikasi yang tertunda
        $pendingVerifications = VerificationRequest::where('status', 'pending')->count();

        // Data terbaru (opsional, bisa disesuaikan)
        $latestUsers = User::latest()->take(5)->get();
        $latestTransactions = Transaction::latest()->take(5)->get();
        $latestReports = Report::where('status', 'Not Reviewed')->latest()->take(5)->get();

        $totalUserSaldokerjain = User::sum('balance');

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalRequests',
            'totalTransactions',
            'pendingReports',
            'pendingVerifications',
            'latestUsers',
            'latestTransactions',
            'latestReports',
            'totalUserSaldokerjain',
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
