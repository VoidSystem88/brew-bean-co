@extends('layouts.app')

@section('page-title', 'QR Scanner')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">QR Code Scanner</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <p class="text-muted">Enter the QR code data or scan with a QR scanner</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="input-group mb-3">
                                <input type="text" id="qrInput" class="form-control" placeholder="Paste QR code data here...">
                                <button class="btn btn-primary" onclick="scanQR()">
                                    <i class="fas fa-search"></i> Scan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="customerInfo" style="display: none;" class="mt-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Customer Details</h5>
                                <div id="customerDetails"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="errorMessage" style="display: none;" class="mt-3">
                        <div class="alert alert-danger" id="errorText"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function scanQR() {
        const qrData = document.getElementById('qrInput').value;
        
        if (!qrData) {
            alert('Please enter QR code data');
            return;
        }
        
        document.getElementById('customerInfo').style.display = 'none';
        document.getElementById('errorMessage').style.display = 'none';
        
        fetch('{{ route("qr.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_data: qrData })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayCustomer(data.customer);
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            showError('Error scanning QR code: ' + error);
        });
    }
    
    function displayCustomer(customer) {
        const details = document.getElementById('customerDetails');
        details.innerHTML = `
            <div class="row">
                <div class="col-6"><strong>Name:</strong></div>
                <div class="col-6">${customer.name}</div>
                <div class="col-6"><strong>Email:</strong></div>
                <div class="col-6">${customer.email || 'N/A'}</div>
                <div class="col-6"><strong>Phone:</strong></div>
                <div class="col-6">${customer.phone || 'N/A'}</div>
                <div class="col-6"><strong>Loyalty Points:</strong></div>
                <div class="col-6"><span class="badge bg-warning">${customer.loyalty_points}</span></div>
                <div class="col-6"><strong>Total Purchases:</strong></div>
                <div class="col-6">${customer.total_purchases}</div>
                <div class="col-6"><strong>Total Spent:</strong></div>
                <div class="col-6">$${customer.total_spent.toFixed(2)}</div>
            </div>
            <div class="mt-3">
                <a href="/qr/customer/${customer.id}" class="btn btn-primary">
                    <i class="fas fa-eye"></i> View Full Profile
                </a>
            </div>
        `;
        document.getElementById('customerInfo').style.display = 'block';
    }
    
    function showError(message) {
        document.getElementById('errorText').textContent = message;
        document.getElementById('errorMessage').style.display = 'block';
    }
    
    // Auto-scan on Enter key
    document.getElementById('qrInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            scanQR();
        }
    });
</script>
@endpush
@endsection