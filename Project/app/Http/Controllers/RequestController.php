<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Request as JobRequest;

use App\Models\Request as RequestModel; 
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        #get 5 latest open requests and deleted_at is null

        $fiveLatestRequests = RequestModel::whereNull('deleted_at')
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();
        
        return view('job-requester.home', compact('fiveLatestRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return view('job-requester.post-work');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'workTitleLabel' => 'required|string|max:255',
            'workDetailLabel' => 'required|string',
            'workPriceLabel' => 'required|numeric|min:5000',
            'workAddressLabel' => 'required|string',
            'workStartDateLabel' => 'required|date',
            'workEndDateLabel' => 'required|date',
            'workStartTimeLabel' => 'required',
            'workEndTimeLabel' => 'required',
        ]);

        $workRequest = new RequestModel();

        
        $workRequest->requester_id = Auth::id();

        
        $startDateTimeString = "{$request->workStartDateLabel} {$request->workStartTimeLabel}";
        $endDateTimeString = "{$request->workEndDateLabel} {$request->workEndTimeLabel}";

        $startDatetime = Carbon::createFromFormat('Y-m-d H:i', $startDateTimeString, 'UTC');
        $endDatetime = Carbon::createFromFormat('Y-m-d H:i', $endDateTimeString, 'UTC');

        
        if ($startDatetime->isPast('UTC')) {
            return back()->withErrors([
                'workStartDateLabel' => 'Waktu mulai tidak boleh di masa lalu (UTC).'
            ])->withInput();
        }

        
        if ($startDatetime->gte($endDatetime)) {
            return back()->withErrors([
                'datetime' => 'Waktu mulai harus sebelum waktu selesai.'
            ])->withInput();
        }

        $user = User::find(Auth::id());
        $jobCost = $request->workPriceLabel + 2500;
        $escrowAmount = $request->workPriceLabel;

        
        if ($user->balance < $jobCost) {
            return back()->withErrors(['workPriceLabel' => __('alerts.saldo_tidak_cukup_untuk_pekerjaan')])->withInput();
        }

        $user->balance -= $jobCost;
        $user->locked_balance += $escrowAmount;
        $user->save();

        activity()
            ->inLog('Finance')
            ->on($user)
            ->causedBy($user)
            ->withProperties(['amount' => $escrowAmount, 'request_title' => $request->workTitleLabel])
            ->log("Dana dari {$user->first_name} sebesar Rp" . number_format($escrowAmount) . " telah ditahan untuk pekerjaan baru '{$request->workTitleLabel}'.");


        
        $workRequest->title = $request->workTitleLabel;
        $workRequest->slug = Str::slug($workRequest->title);
        $workRequest->description = $request->workDetailLabel;
        $workRequest->price = $request->workPriceLabel;
        $workRequest->final_price = $request->workPriceLabel;
        $workRequest->service_fee = 2500;
        $workRequest->location = $request->workAddressLabel;
        $workRequest->start_time = $startDatetime; 
        $workRequest->end_time = $endDatetime;   

        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $request->workPriceLabel,
            'type' => 'credit',
            'description_id' => 'Penahanan saldo untuk pekerjaan: ' . $workRequest->title,
            'description_en' => 'Balance reserved for the job: ' . $workRequest->title,
        ]);

        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => 2500,
            'type' => 'credit',
            'description_id' => 'Pembayaran biaya layanan aplikasi untuk pekerjaan: ' . $workRequest->title,
            'description_en' => 'Service fee payment for request: ' . $workRequest->title,
        ]);

        $result = $workRequest->save();
        Payment::create([
            'request_id' => $workRequest->id,
            'amount' => $request->workPriceLabel,
            'status' => 'holding',
        ]);
        if ($result) {
            return redirect()->route('job-req.home')->with('custom_success_alert', __('alerts.pekerjaan_berhasil_dibuat'));
        } else {
            return back()->with('custom_error_alert', __('alerts.terjadi_kesalahan'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        
        $workRequest = RequestModel::where('slug', $slug)->with('transaction')->firstOrFail();
        
        
        if (!$workRequest || $workRequest->deleted_at) {
            abort(404, 'Request not found or has been deleted.');
        }

        return response()->json($workRequest);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        
        $workRequest = RequestModel::where('slug', $slug)->firstOrFail();

        
        if (Carbon::parse($workRequest->start_time)->isPast('UTC')) {
            activity()
                ->inLog('Security')
                ->on($workRequest)
                ->causedBy(Auth::user())
                ->log("Percobaan akses tidak sah ke halaman edit pekerjaan '{$workRequest->id}'. Pekerjaan sudah dimulai/lewat waktu.");
            return redirect()->route('job-req.home')->with('custom_error_alert', __('alerts.pekerjaan_sudah_dimulai'));
        }

        if (Auth::id() !== $workRequest->requester_id) {
            
            activity()
                ->inLog('Security')
                ->on($workRequest) 
                ->causedBy(Auth::user()) 
                ->log("Percobaan akses tidak sah ke halaman edit pekerjaan '{$workRequest->id}'.");

            
            return redirect()->route('job-req.home')->with('custom_error_alert', __('alerts.anda_tidak_berwenang'));
        }
        
        if (!$workRequest || $workRequest->deleted_at) {
            abort(404, 'Request not found or has been deleted.');
        }

        
        return view('job-requester.edit', compact('workRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        try {
            DB::transaction(function () use ($request, $slug) {
                $workRequest = RequestModel::where('slug', $slug)->firstOrFail();

                
                if (Auth::id() !== $workRequest->requester_id) {
                    throw new \Exception('Anda tidak berwenang untuk mengubah pekerjaan ini.');
                }

                
                if (Carbon::parse($workRequest->start_time)->isPast('UTC')) {
                    throw new \Exception('Pekerjaan ini sudah dimulai atau telah melewati waktu mulai (UTC) dan tidak dapat diubah.');
                }

                
                if ($workRequest->status !== 'open') {
                    throw new \Exception('Pekerjaan yang sudah berjalan tidak dapat diubah.');
                }

                
                $request->validate([
                    'workTitleLabel' => 'required|string|max:255',
                    'workDetailLabel' => 'required|string',
                    'workPriceLabel' => 'required|numeric|min:5000',
                    'workAddressLabel' => 'required|string',
                    'workStartDateLabel' => 'required|date',
                    'workEndDateLabel' => 'required|date',
                    'workStartTimeLabel' => 'required',
                    'workEndTimeLabel' => 'required',
                ]);


                
                $user = User::findOrFail(Auth::id());
                $originalPrice = $workRequest->price;
                $newPrice = $request->workPriceLabel;
                $priceDifference = $newPrice - $originalPrice;
                
                if ($priceDifference > 0) {
                    if ($user->balance < $priceDifference) {
                        throw new \Exception('Saldo Anda tidak cukup untuk menaikkan harga pekerjaan.');
                    }
                    $user->balance -= $priceDifference;
                    $user->locked_balance += $priceDifference;
                    $user->save();
                    WalletTransaction::create([
                        'user_id' => $user->id,
                        'amount' => $priceDifference,
                        'type' => 'credit',
                        'description_id' => 'Penambahan saldo ditahan untuk perubahan harga pada: ' . $workRequest->title,
                        'description_en' => 'Extra balance on hold for price update on: ' . $workRequest->title,
                    ]);
                    activity()->inLog('Finance')->causedBy($user)->on($user)
                        ->log("Dana tambahan sebesar Rp" . number_format($priceDifference) . " ditahan dari {$user->first_name} karena perubahan harga.");
                }
                
                else if ($priceDifference < 0) {
                    $refundAmount = abs($priceDifference);
                    $user->balance += $refundAmount;
                    $user->locked_balance -= $refundAmount;
                    $user->save();

                    WalletTransaction::create([
                        'user_id' => $user->id,
                        'amount' => $refundAmount,
                        'type' => 'debit',
                        'description_id' => 'Pengembalian saldo ditahan untuk perubahan harga pada: ' . $workRequest->title,
                        'description_en' => 'Balance refund on hold due to price adjustment on: ' . $workRequest->title,
                    ]);
                    activity()->inLog('Finance')->causedBy($user)->on($user)
                        ->log("Dana sebesar Rp" . number_format($refundAmount) . " dikembalikan ke {$user->first_name} karena perubahan harga.");
                }

                
                
                $startDateTimeString = "{$request->workStartDateLabel} {$request->workStartTimeLabel}";
                $endDateTimeString = "{$request->workEndDateLabel} {$request->workEndTimeLabel}";

                $startDatetime = Carbon::createFromFormat('Y-m-d H:i', $startDateTimeString, 'UTC');
                $endDatetime = Carbon::createFromFormat('Y-m-d H:i', $endDateTimeString, 'UTC');

                
                if ($startDatetime->isPast('UTC')) {
                    throw new \Exception('Waktu mulai yang diperbarui tidak boleh di masa lalu (UTC).');
                }

                if ($startDatetime->gte($endDatetime)) {
                    throw new \Exception('Waktu mulai harus sebelum waktu selesai.');
                }

                $workRequest->title = $request->workTitleLabel;
                $workRequest->slug = Str::slug($workRequest->title) . '-' . $workRequest->id; 
                $workRequest->description = $request->workDetailLabel;
                $workRequest->price = $newPrice;
                $workRequest->final_price = $newPrice; 
                $workRequest->location = $request->workAddressLabel;
                $workRequest->start_time = $startDatetime; 
                $workRequest->end_time = $endDatetime;   
                $workRequest->save();

                
                $workRequest->payment->update(['amount' => $newPrice]);
            });
        } catch (\Exception $e) {
            return back()->with('custom_error_alert', $e->getMessage())->withInput();
        }

        
        return redirect()->route('job-req.home')->with('custom_success_alert', __('alerts.pekerjaan_berhasil_diperbarui'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        
        try {
            DB::transaction(function () use ($slug) {
                $workRequest = RequestModel::where('slug', $slug)->firstOrFail();

                
                if (Auth::id() !== $workRequest->requester_id) {
                    return back()->with('custom_error_alert', __('alerts.anda_tidak_berwenang'));
                }

                
                
                if ($workRequest->status !== 'open') {
                    throw new \Exception('Pekerjaan yang sedang berjalan atau sudah selesai tidak dapat dibatalkan.');
                }

                
                
                

                
                $user = $workRequest->requester; 
                $refundAmount = $workRequest->price; 

                
                $user->balance += $refundAmount;
                $user->locked_balance -= $refundAmount;
                $user->save();

                
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $refundAmount,
                    'type' => 'debit',
                    'description_id' => 'Pengembalian saldo dari pembatalan pekerjaan: ' . $workRequest->title,
                    'description_en' => 'Balance refund from job cancellation: ' . $workRequest->title,
                ]);

                activity()
                    ->inLog('Finance') 
                    ->on($workRequest) 
                    ->causedBy($user)  
                    ->withProperties(['amount' => $refundAmount, 'refunded_to' => $user->id])
                    ->log("{$user->first_name} telah membatalkan pekerjaan '{$workRequest->title}', dan dana sebesar Rp" . number_format($refundAmount) . " telah dikembalikan.");

                
                $workRequest->payment->update(['status' => 'refunded_to_requester']); 
                $workRequest->chatRooms()->update(['is_open' => false]); 

                
                $workRequest->delete();

                
                session()->flash('refund_amount', $refundAmount);
            });
        } catch (\Exception $e) {
            return back()->with('custom_error_alert', $e->getMessage());
        }

        
        $refundAmount = session('refund_amount', 0); 
        $formattedRefundAmount = 'Rp' . number_format($refundAmount, 0, ',', '.');
        return redirect()->route('job-req.home')->with('custom_success_alert', __('alerts.pekerjaan_dibatalkan_refund', ['amount' => $formattedRefundAmount]));
    }

    public function showOngoing($id)
    {
        $request = JobRequest::findOrFail($id);
        return view('job-requester.on-going-work-request', compact('request'));
    }

    public function acceptRequest(RequestModel $request) 
    {
        $worker = Auth::user();
        
        $id = $request->id; 
        $jobrequest = JobRequest::findOrFail($id); 

        
        $jobrequest->status = 'closed';
        $jobrequest->save(); 


        
        if (!$worker) {
            return response()->json(['success' => false, 'message' => 'User tidak terautentikasi.'], 401);
        }        
        $winningChatRoom = RequestModel::hireAndFinalize($request, $worker);

        
        return response()->json([
            'success' => true,
            'message'      => __('alerts.job_accepted_success_chat_redirect'), 
            'redirect_url' => route('job-taker.home')
        ]);
    }
    public function validateRequest(Request $request)
    {
        $rules = [
            'workTitleLabel' => 'required|string|max:255',
            'workDetailLabel' => 'required|string',
            'workPriceLabel' => 'required|numeric|min:5000',
            'workAddressLabel' => 'required|string',
            'workStartDateLabel' => 'required|date',
            'workEndDateLabel' => 'required|date',
            'workStartTimeLabel' => 'required',
            'workEndTimeLabel' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $startDate = $request->input('workStartDateLabel');
            $startTime = $request->input('workStartTimeLabel');
            $endDate = $request->input('workEndDateLabel');
            $endTime = $request->input('workEndTimeLabel');

            
            if ($startDate && $startTime && $endDate && $endTime) {
                
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$startDate} {$startTime}", 'UTC');
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$endDate} {$endTime}", 'UTC');

                
                if ($startDateTime->isPast('UTC')) {
                    return back()->withErrors([
                        'workStartDateLabel' => __('validation.custom.workStartDateLabel.past_utc')
                    ])->withInput();
                }

                
                if ($startDateTime->gte($endDateTime)) { 
                    $validator->errors()->add(
                        'datetime',
                        __('validation.custom.datetime.after_start_time') 
                    );
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return response()->json(['success' => true]);
    }
}
