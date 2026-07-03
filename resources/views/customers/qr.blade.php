@extends('layouts.app')

@section('page-title', 'QR Code')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-qrcode me-2"></i>QR Code for {{ $customer->name }}</h5>
                </div>
                <div class="card-body">
                    <div id="qrcode" style="display: inline-block;"></div>
                    <div class="mt-3">
                        <p><strong>{{ $customer->name }}</strong></p>
                        <p class="text-muted">{{ $customer->customer_code }}</p>
                        <p class="text-muted">{{ $customer->email }}</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Customers
                        </a>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrContainer = document.getElementById('qrcode');
        const qrData = '{{ route('qr.customer', $customer->id) }}';
        
        new QRCode(qrContainer, {
            text: qrData,
            width: 250,
            height: 250,
            colorDark: '#6F4E37',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    });
</script>
@endpush
@endsection