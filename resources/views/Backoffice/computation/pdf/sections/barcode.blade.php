@php
    $barcode = $computation->media->where('type', 'barcode')->first();
    $qrcode = $computation->media->where('type', 'qrcode')->first();
@endphp

@if($barcode || $qrcode)
    <div class="section-title">SYSTEM GENERATED BARCODE / QR CODE</div>
    <div class="barcode-container">
        @if($barcode)
            <div style="display:inline-block;margin-right:20px;">
                <strong>Barcode</strong><br>
                <img src="{{ asset('storage/' . $barcode->file_path) }}" alt="Barcode">
            </div>
        @endif
        @if($qrcode)
            <div style="display:inline-block;">
                <strong>QR Code</strong><br>
                <img src="{{ asset('storage/' . $qrcode->file_path) }}" alt="QR Code">
            </div>
        @endif
        <div style="font-size:8px;color:#666;margin-top:5px;">
            Generated: {{ $computation->updated_at ?? now() }}
        </div>
    </div>
@endif