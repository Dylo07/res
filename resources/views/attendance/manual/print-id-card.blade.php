

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="no-print mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4>Staff ID Card - {{ $person->name }}</h4>
            <div>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="{{ route('staff.personal.profile', $person->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- ID Card Design -->
    <div class="id-card-container">
        <div class="id-card">
            <!-- Front of ID Card -->
            <div class="id-card-front">
                <div class="id-card-header">
                    <h5 class="company-name">Hotel Soba Lanka</h5>
                    <p class="card-type">STAFF ID CARD</p>
                </div>
                
                <div class="id-card-body">
                    <div class="photo-section">
                        <div class="photo-placeholder">
                            <i class="fas fa-user fa-3x"></i>
                        </div>
                    </div>
                    
                    <div class="info-section">
                        <div class="info-row">
                            <strong>{{ $person->name }}</strong>
                        </div>
                        
                        @if($person->position)
                            <div class="info-row">
                                <span class="label">Position:</span>
                                <span class="value">{{ $person->position }}</span>
                            </div>
                        @endif
                        
                        @if($person->staffCode)
                            <div class="info-row">
                                <span class="label">Staff ID:</span>
                                <span class="value">{{ $person->staffCode->staff_code }}</span>
                            </div>
                        @endif
                        
                        @if($person->staffCategory)
                            <div class="info-row">
                                <span class="label">Department:</span>
                                <span class="value">{{ ucfirst(str_replace('_', ' ', $person->staffCategory->category)) }}</span>
                            </div>
                        @endif
                        
                        <div class="info-row">
                            <span class="label">Person ID:</span>
                            <span class="value">{{ $person->id }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="id-card-footer">
                    <p class="issue-date">Issued: {{ \Carbon\Carbon::now()->format('M Y') }}</p>
                </div>
            </div>
            
            <!-- Back of ID Card -->
            <div class="id-card-back">
                <div class="id-card-header">
                    <h6>Emergency Information</h6>
                </div>
                
                <div class="emergency-info">
                    @if($person->emergency_contact || $person->emergency_phone)
                        <div class="emergency-row">
                            <span class="label">Emergency Contact:</span>
                            <span class="value">{{ $person->emergency_contact ?? 'N/A' }}</span>
                        </div>
                        <div class="emergency-row">
                            <span class="label">Emergency Phone:</span>
                            <span class="value">{{ $person->emergency_phone ?? 'N/A' }}</span>
                        </div>
                    @else
                        <p class="text-center text-muted">No emergency contact information available</p>
                    @endif
                    
                    @if($person->blood_group)
                        <div class="emergency-row">
                            <span class="label">Blood Group:</span>
                            <span class="value blood-group">{{ $person->blood_group }}</span>
                        </div>
                    @endif
                </div>
                
                <div class="terms">
                    <h6>Terms & Conditions</h6>
                    <ul>
                        <li>This card remains property of Hotel Soba Lanka</li>
                        <li>Must be returned upon termination</li>
                        <li>Report lost cards immediately</li>
                        <li>Valid for authorized personnel only</li>
                    </ul>
                </div>
                
                <div class="card-footer-back">
                    <p>Contact: info@hotelsobalanka.com</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .id-card-container {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
    
    .id-card {
        display: flex;
        gap: 20px;
    }
    
    .id-card-front, .id-card-back {
        width: 3.375in; /* Standard ID card width */
        height: 2.125in; /* Standard ID card height */
        border: 2px solid #333;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px;
        box-sizing: border-box;
        font-size: 10px;
        position: relative;
        overflow: hidden;
    }
    
    .id-card-front {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .id-card-back {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
    
    .id-card-header {
        text-align: center;
        margin-bottom: 8px;
    }
    
    .company-name {
        font-size: 14px;
        font-weight: bold;
        margin: 0;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }
    
    .card-type {
        font-size: 8px;
        margin: 0;
        letter-spacing: 1px;
    }
    
    .id-card-body {
        display: flex;
        gap: 8px;
        height: 80px;
    }
    
    .photo-section {
        flex: 0 0 50px;
    }
    
    .photo-placeholder {
        width: 50px;
        height: 60px;
        background: rgba(255,255,255,0.9);
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
    }
    
    .info-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    
    .info-row {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }
    
    .info-row strong {
        font-size: 11px;
        margin-bottom: 2px;
    }
    
    .label {
        font-size: 7px;
        opacity: 0.8;
    }
    
    .value {
        font-size: 8px;
        font-weight: bold;
    }
    
    .id-card-footer {
        position: absolute;
        bottom: 5px;
        left: 10px;
        right: 10px;
        text-align: center;
    }
    
    .issue-date {
        font-size: 7px;
        margin: 0;
        opacity: 0.8;
    }
    
    /* Back side styles */
    .emergency-info {
        margin-bottom: 10px;
    }
    
    .emergency-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 3px;
        font-size: 8px;
    }
    
    .blood-group {
        background: #ff4757;
        padding: 1px 4px;
        border-radius: 3px;
        font-weight: bold;
    }
    
    .terms {
        margin-bottom: 10px;
    }
    
    .terms h6 {
        font-size: 8px;
        margin: 0 0 3px 0;
    }
    
    .terms ul {
        margin: 0;
        padding-left: 10px;
        font-size: 6px;
        line-height: 1.2;
    }
    
    .terms li {
        margin-bottom: 1px;
    }
    
    .card-footer-back {
        position: absolute;
        bottom: 3px;
        left: 10px;
        right: 10px;
        text-align: center;
        font-size: 6px;
    }
    
    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
        
        .container {
            max-width: none;
            margin: 0;
            padding: 0;
        }
        
        .id-card-container {
            margin: 0;
            break-inside: avoid;
        }
        
        .id-card {
            break-inside: avoid;
        }
        
        body {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
    
    @media (max-width: 768px) {
        .id-card {
            flex-direction: column;
            align-items: center;
        }
        
        .id-card-front, .id-card-back {
            transform: scale(1.2);
            margin: 20px 0;
        }
    }
</style>
@endpush
@endsection