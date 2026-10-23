<div class="barcode-area">
    <div><strong>System Generated Barcode / QR Code</strong></div>
    <div class="barcode-box" id="barcode_display">
        @php
            $barcode = $computation->media->where('type', 'barcode')->first();
        @endphp
        @if($barcode)
            <img src="{{ asset('storage/'.$barcode->file_path) }}" alt="Barcode">
        @else
            ||| ██████ ████ ██████ |||
        @endif
    </div>
    <div class="barcode-upload">
        <button onclick="document.getElementById('barcode_upload').click()">📤 Upload Barcode</button>
        <button onclick="removeBarcode()">✕ Remove Barcode</button>
        <input type="file" id="barcode_upload" accept="image/*" onchange="uploadBarcode(event)">
        <span style="font-size:9px;color:#555;">Upload image (JPG/PNG, max 2MB)</span>
    </div>
    <div><span style="font-size:9px;color:#555;">Generated: <span id="barcode-date">{{ date('d/m/Y H:i') }}</span></span></div>
</div>