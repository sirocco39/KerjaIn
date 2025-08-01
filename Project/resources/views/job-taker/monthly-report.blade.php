@extends('Master.master-job-taker')
@section('content')
    <!-- Banner Section -->
    <section class="banner-section">
        <div class="container-fluid p-0 position-relative">
            <div class="d-flex flex-column flex-md-row align-items-start p-1" style="min-height: 500px;">

                <!-- Konten Teks -->
                <div class="text-content custom-translate mb-5" style="margin-top: 15px;">
                    <h1 class="display-6 display-md-3 display-lg-2" style="font-weight: bold;">
                        {{ __('monthly-report.greeting_prefix') }} <span
                            style="color: #D3FA0D;">{{ $worker->first_name . ' ' . $worker->last_name }}</span>{{ __('monthly-report.report_title_suffix') }}
                        {{ $reportPeriod }}
                    </h1>
                </div>

                <!-- Bulat-bulat Warna-warni -->
                <div class="color-dots"
                    style="width: 300px; min-width: 300px; position: relative; height: 400px; z-index: 1;">
                    <div class="dot dot-1"></div>
                    <div class="dot dot-2"></div>
                    <div class="dot dot-3"></div>
                </div>

                <!-- Create user avatar with name in line with avatar -->
                <div class="d-flex align-items-center mb-4 mb-md-0"
                    style="
            position: static;
            align-self: flex-start;
            margin-top: 20px;
            margin-left: 20px;
            right: 20px;
            padding: 12px;
            z-index: 3;
            ">
                    <img src="https:
                        style="width: 60px; height: 60px;">
                    <div class="d-flex flex-column ms-2">
                        <span
                            style="font-size: 16px; font-weight: bold; color: white;">{{ $worker->first_name . ' ' . $worker->last_name }}</span>
                        <span style="font-size: 14px; color: #dfe6e9;">{{ __('monthly-report.worker_role') }}</span>
                        <span style="font-size: 14px; color: #dfe6e9;">{{ __('monthly-report.verification_status') }}
                            <span
                                class="font-semibold {{ $verificationStatus == __('monthly-report.verified') ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ __('monthly-report.verified') }}
                            </span>
                        </span>
                    </div>
                </div>


            </div>

        </div>
    </section>

    <div class="mt-2">
        <label for="reportMonth"
            style="margin-left: 60px; margin-top: 20px; font-size: 25px; font-weight: bold;">{{ __('monthly-report.my_activity') }}</label>
        <select id="reportMonth" name="reportMonth"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            onchange="this.form.submit()" style="margin-left: 10px">
            
            @foreach ($availableMonths as $monthOption)
                <option value="{{ $monthOption['value'] }}"
                    {{ $monthOption['value'] == request('report_month', \Carbon\Carbon::now()->format('Y-m')) ? 'selected' : '' }}>
                    {{ $monthOption['label'] }}
                </option>
            @endforeach
        </select>
    </div>
    <form id="reportForm" action="{{ route('monthly.report') }}" method="GET" class="hidden">
        <input type="hidden" name="report_month" id="hiddenReportMonth">
    </form>

    <div class="container mt-4">
        <div class="row g-3 justify-content-center">

            <div class="col-6 col-md-3 col-lg-2">
                <div class="stat-card text-center">
                    <div class="stat-number">{{ $totalJobsCompleted }}</div>
                    <p class="stat-label">{{ __('monthly-report.total_jobs_completed') }}</p>
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <div class="stat-card text-center">
                    <div class="stat-number">{{ number_format($totalHoursWorked, 1) }}</div>
                    <p class="stat-label">{{ __('monthly-report.total_hours_worked') }}</p>
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-3">
                <div class="stat-card text-center">
                    <div class="stat-number">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</div>
                    <p class="stat-label">{{ __('monthly-report.total_earnings') }}</p>
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-3">
                <div class="stat-card text-center">
                    <div class="stat-number">⭐ {{ $averageRating }} / 5</div>
                    <p class="stat-label">{{ __('monthly-report.average_rating') }}</p>
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <div class="stat-card text-center">
                    <div class="stat-number">{{ $distinctClients }}</div>
                    <p class="stat-label">{{ __('monthly-report.total_distinct_clients') }}</p>
                </div>
            </div>

        </div>
    </div>


    <div class="card-custom" style="margin-left: 60px; margin-top: 20px; margin-right: 60px">
        <h5>{{ __('monthly-report.jobs_table_title') }}</h5>
        @if ($jobHistory->isEmpty())
            <p class="text-gray-600">{{ __('monthly-report.no_jobs_completed_this_month') }}</p>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                style="text-align: center">{{ __('monthly-report.table_header_date') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                style="text-align: center">{{ __('monthly-report.table_header_job') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                style="text-align: center">{{ __('monthly-report.table_header_location') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                style="text-align: center">{{ __('monthly-report.table_header_client') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                style="text-align: center">{{ __('monthly-report.table_header_duration') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                style="text-align: center">{{ __('monthly-report.table_header_wage') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                style="text-align: center">{{ __('monthly-report.table_header_status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobHistory as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                                    {{ $transaction->updated_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                                    {{ $transaction->request->title ?? __('monthly-report.not_available_abbr') }}
                                </td> 
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                                    {{ $transaction->request->location ?? __('monthly-report.not_available_abbr') }}
                                </td> 
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                                    {{ $transaction->request->requester->first_name . ' ' . $transaction->request->requester->last_name ?? __('monthly-report.not_available_abbr') }}
                                </td> 
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                                    @php
                                        $durationMinutes = 0;
                                        if ($transaction->start_work && $transaction->finish_work) {
                                            $durationMinutes = $transaction->start_work->diffInMinutes(
                                                $transaction->finish_work,
                                            );
                                        }
                                    @endphp
                                    {{ $durationMinutes > 0 ? number_format($durationMinutes / 60, 1) . ' ' . __('monthly-report.hours_unit') : __('monthly-report.not_available_abbr') }}

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                                    Rp {{ number_format($transaction->request->price ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    </div>
    @endif

    <div class="row g-4 mb-5" style="padding-top: 3%">

        
        <div class="col-md-6 mt-3" style="padding-left: 5%">
            <div class="card-custom h-100" style="max-height: 400px; overflow: hidden;">
                <h5 class="p-3 mb-0">{{ __('monthly-report.earnings_chart_title') }}</h5>
                <div class="chart-container" style="position: relative; height: calc(100% - 60px); width: 100%;">
                    <canvas id="earningsChart" data-chart-labels='{!! $chartLabels !!}'
                        data-chart-earnings='{!! $chartEarningsData !!}' data-chart-jobs='{!! $chartJobsCompletedData ?? ' []' !!}'>
                    </canvas>
                </div>
            </div>
        </div>

        
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    
                    
                    const chartElement = document.getElementById('earningsChart');

                    
                    if (chartElement) {
                        const ctx = chartElement.getContext('2d');

                        
                        const labels = JSON.parse(chartElement.dataset.chartLabels);
                        console.log(labels); 
                        const earningsData = JSON.parse(chartElement.dataset.chartEarnings);
                        const jobsCompletedData = JSON.parse(chartElement.dataset.chartJobs);

                        new Chart(ctx, {
                            type: 'line', 
                            data: {
                                labels: labels,
                                datasets: [{
                                        label: '{{ __('monthly-report.daily_earnings_chart_label') }}', 
                                        data: earningsData,
                                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        borderWidth: 2,
                                        tension: 0.3,
                                        fill: true,
                                    },
                                    { 
                                        label: '{{ __('monthly-report.jobs_completed_chart_label') }}',
                                        data: jobsCompletedData,
                                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 2,
                                        tension: 0.3,
                                        fill: false, 
                                        yAxisID: 'yJobs', 
                                        hidden: true 
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: { 
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: '{{ __('monthly-report.earnings_y_axis_label') }}'
                                        },
                                        ticks: {
                                            callback: function(value, index, values) {
                                                return 'Rp ' + value.toLocaleString('id-ID');
                                            }
                                        }
                                    },
                                    yJobs: { 
                                        type: 'linear',
                                        display: true,
                                        position: 'right', 
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: '{{ __('monthly-report.jobs_y_axis_label') }}'
                                        },
                                        grid: {
                                            drawOnChartArea: false, 
                                        },
                                        
                                        min: 0, 
                                        max: Math.max(...jobsCompletedData) +
                                            1 
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: '{{ __('monthly-report.x_axis_date_label') }}' 
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                if (context.parsed.y !== null) {
                                                    
                                                    if (context.dataset.label ===
                                                        '{{ __('monthly-report.daily_earnings_chart_label') }}'
                                                    ) {
                                                        label += 'Rp ' + context.parsed.y.toLocaleString(
                                                            'id-ID');
                                                    } else if (context.dataset.label ===
                                                        '{{ __('monthly-report.jobs_completed_chart_label') }}'
                                                    ) {
                                                        label += context.parsed.y +
                                                            ' {{ __('monthly-report.jobs_table_title') }}';
                                                    } else {
                                                        label += context.parsed.y;
                                                    }
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        console.error('{{ __('monthly-report.error_loading_chart_element') }}');
                    }
                });
            </script>
        @endpush

        <div class="col-md-6 mt-3" style="padding-right: 5%">
            <div class="card-custom h-100" style="max-height: 400px; overflow-y: auto;">
                <h5 class="p-3">{{ __('monthly-report.reviews_card_title') }}</h5>
                <div class="reviews-container px-3 pb-3">
                    @if ($clientReviews->isEmpty())
                        <p class="text-gray-600">{{ __('monthly-report.no_reviews_this_month') }}</p>
                    @else
                        @foreach ($clientReviews as $review)
                            <div class="review-item d-flex align-items-start mb-3">
                                <img src="https:
                                    class="rounded-circle" style="width: 60px; height: 60px; margin-right: 10px;">
                                <div>
                                    <strong>{{ $review->reviewer->first_name . ' ' . $review->reviewer->last_name ?? __('monthly-report.anonymous_client') }}</strong><br>
                                    <small>{{ __('monthly-report.client_role') }}</small><br>
                                    {{ str_repeat('тнР', $review->rating) }}
                                    <p class="review-text">"{{ $review->comment }}"</p>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card-custom" style="margin-left: 60px; margin-right: 60px">
        <h5>{{ __('monthly-report.monthly_report_card_title') }}</h5>

        <div class="content" style="text-align:center;">
            <p>{{ __('monthly-report.download_report_text') }}</p>
            <i class="fas fa-download download-icon"></i><br>
            <a href="{{ route('monthly.report.download.pdf', ['report_month' => app('request')->input('report_month', \Carbon\Carbon::now()->format('Y-m'))]) }}"
                class="">
                <button type="button" class="btn btn-primary btn-sm">{{ __('monthly-report.download_button') }}</button>
            </a>
        </div>
    </div>

    <script>
        document.getElementById('reportMonth').addEventListener('change', function() {
            document.getElementById('hiddenReportMonth').value = this.value;
            document.getElementById('reportForm').submit();
        });
    </script>



@endsection
