@extends('master.master-job-req')
<!-- This file name is join2.blade.php -->

@section('content')
<x-join-worker.join-template :step="2">
    <div class="row justify-content-center">
        <h5 class="col-12 col-md-10 fw-bold my-1">{{ __('join-worker.contract_details') }}</h5>
        <div class="col-md-10 bg-white p-4">
            <form action="{{ route('worker.register.store2') }}" method="POST" id="contractForm">
                @csrf
                <ol class="px-1 text-justify">
                    <li>
                        <strong>{{ __('join-worker.general_requirements_title') }}</strong><br>
                        @foreach (__('join-worker.general_requirements_list') as $item)
                        {{ $item }}<br>
                        @endforeach
                        <br>
                    </li>
                    <li>
                        <strong>{{ __('join-worker.account_and_verification_title') }}</strong><br>
                        @foreach (__('join-worker.account_and_verification_list') as $item)
                        {{ $item }}<br>
                        @endforeach
                        <br>
                    </li>
                    <li>
                        <strong>{{ __('join-worker.commitment_and_work_ethic_title') }}</strong><br>
                        @foreach (__('join-worker.commitment_and_work_ethic_list') as $item)
                        {{ $item }}<br>
                        @endforeach
                        <br>
                    </li>
                    <li>
                        <strong>{{ __('join-worker.payment_system_title') }}</strong><br>
                        @foreach (__('join-worker.payment_system_list') as $item)
                        {{ $item }}<br>
                        @endforeach
                        <br>
                    </li>
                    <li>
                        <strong>{{ __('join-worker.responsibility_and_risk_title') }}</strong><br>
                        @foreach (__('join-worker.responsibility_and_risk_list') as $item)
                        {{ $item }}<br>
                        @endforeach
                        <br>
                    </li>
                    <li>
                        <strong>{{ __('join-worker.termination_of_cooperation_title') }}</strong><br>
                        @foreach (__('join-worker.termination_of_cooperation_list') as $item)
                        {{ $item }}<br>
                        @endforeach
                        <br>
                    </li>
                    <li>
                        <strong>{{ __('join-worker.terms_changes_title') }}</strong><br>
                        @foreach (__('join-worker.terms_changes_list') as $item)
                        {{ $item }}<br>
                        @endforeach
                        <br>
                    </li>
                </ol>


                <div class="mt-5">

                    <input type="checkbox" name="agree_terms" id="agree_terms" class="form-checkbox"
                        {{ old('agree_terms') ? 'checked' : '' }}>
                    <span class="text-dark-gray">{{ __('join-worker.i_agree_to_terms') }} <a href="#"
                            class="text-blue-600 hover:underline">{{ __('join-worker.terms_and_conditions_link') }}</a></span>
                    <p id="error-agree_terms"
                        class="text-danger @unless ($errors->has('agree_terms')) hidden @endunless">
                        {{ $errors->first('agree_terms') }}
                    </p>


                    <input type="checkbox" name="agree_data_usage" id="agree_data_usage" class="form-checkbox"
                        {{ old('agree_data_usage') ? 'checked' : '' }}>
                    <span class="text-dark-gray">{{ __('join-worker.i_agree_to_data_usage') }}</span>

                    <p id="error-agree_data_usage"
                        class="text-danger @unless ($errors->has('agree_data_usage')) hidden @endunless">
                        {{ $errors->first('agree_data_usage') }}
                    </p>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" id="submitButton" class="btn bg-secondary text-white px-4">
                        {{ __('join-worker.continue_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-join-worker.join-template>
@endsection