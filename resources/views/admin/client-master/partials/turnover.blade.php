{{-- ============================================================
    TURNOVER – Final with 403 handling
============================================================ --}}

<div class="row g-4">

    {{-- Monthly Turnover Entry (unchanged) --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 py-2">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-graph-up-arrow me-2"></i> Monthly Turnover Entry
                </h6>
            </div>
            <div class="card-body pt-0 pb-2">
                <div class="row g-2 mb-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-uppercase text-muted">Financial Year</label>
                        <select id="financialYearSelect" class="form-select form-select-sm">
                            @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 5;
                            @endphp
                            @for ($y = $currentYear + 1; $y >= $startYear; $y--)
                                @php $fy = $y . '-' . ($y + 1); @endphp
                                <option value="{{ $fy }}" {{ $fy == ($currentYear . '-' . ($currentYear + 1)) ? 'selected' : '' }}>
                                    FY {{ $fy }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-8 text-end">
                        <div class="d-inline-block bg-light px-3 py-1 rounded-2">
                            <span class="text-muted small text-uppercase">Total Turnover</span><br>
                            <span id="totalTurnoverDisplay" class="fw-bold fs-4 text-primary">₹ 0.00</span>
                        </div>
                    </div>
                </div>

                <form id="turnoverForm">
                    @csrf
                    <input type="hidden" id="selectedFinancialYear" name="financial_year" value="{{ date('Y') . '-' . (date('Y') + 1) }}">
                    <div class="row g-1">
                        @php
                            $months = [1=>'APR',2=>'MAY',3=>'JUN',4=>'JUL',5=>'AUG',6=>'SEP',7=>'OCT',8=>'NOV',9=>'DEC',10=>'JAN',11=>'FEB',12=>'MAR'];
                        @endphp
                        @foreach ($months as $num => $label)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="form-floating mb-1">
                                    <input type="number" step="0.01" min="0"
                                           class="form-control form-control-sm month-input"
                                           id="month_{{ $num }}"
                                           name="months[{{ $num }}]"
                                           placeholder="0.00"
                                           data-month="{{ $num }}"
                                           style="height: 34px; font-size: 13px; padding: 4px 8px;">
                                    <label for="month_{{ $num }}" class="small text-uppercase fw-bold text-muted" style="font-size: 10px; padding: 4px 8px;">{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2 text-end">
                        <button type="submit" class="btn btn-primary btn-sm px-3" id="saveTurnoverBtn">
                            <i class="bi bi-save me-1"></i> Save Turnover
                        </button>
                    </div>
                </form>

                <div class="mt-2">
                    <canvas id="turnoverChart" style="height: 160px !important; max-height: 160px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Year-Wise Summary --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 py-2">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-table me-2"></i> Year‑wise Summary
                </h6>
            </div>
            <div class="card-body pt-0 pb-2">
                <div id="yearlySummaryContainer">
                    <div class="text-center py-2 text-muted small">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Loading summary...
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- SCRIPTS --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    $(document).ready(function() {
        let chartInstance = null;
        let currentFY = $('#financialYearSelect').val();

        var clientId = {{ $client->id }};
        var dataUrlTemplate = "{{ route('admin.client-master.turnovers.data', ['client' => $client->id, 'fy' => 'PLACEHOLDER']) }}";
        var summaryUrl = "{{ route('admin.client-master.turnovers.summary', ['client' => $client->id]) }}";
        var bulkSaveUrl = "{{ route('admin.client-master.turnovers.bulk', ['client' => $client->id]) }}";

        function getDataUrl(fy) {
            return dataUrlTemplate.replace('PLACEHOLDER', fy);
        }

        function loadTurnoverData(fy) {
            $('#selectedFinancialYear').val(fy);
            $.ajax({
                url: getDataUrl(fy),
                type: 'GET',
                success: function(data) {
                    if (typeof data === 'object' && data !== null) {
                        $('.month-input').each(function() {
                            let month = $(this).data('month');
                            let value = data[month];
                            if (value !== undefined && value !== null && !isNaN(parseFloat(value))) {
                                $(this).val(parseFloat(value).toFixed(2));
                            } else {
                                $(this).val('');
                            }
                        });
                        updateTotal();
                        updateChart();
                    }
                },
                error: function(xhr) {
                    console.error('Load error:', xhr);
                    if (xhr.status === 403) {
                        alert('403 Forbidden – please refresh the page and try again.');
                    } else {
                        alert('Error loading turnover data. Check console for details.');
                    }
                }
            });
        }

        function updateTotal() {
            let total = 0;
            $('.month-input').each(function() {
                let val = parseFloat($(this).val()) || 0;
                total += val;
            });
            $('#totalTurnoverDisplay').text('₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }

        function updateChart() {
            const labels = ['APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 'JAN', 'FEB', 'MAR'];
            const data = [];
            $('.month-input').each(function() {
                data.push(parseFloat($(this).val()) || 0);
            });

            if (chartInstance) chartInstance.destroy();

            const ctx = document.getElementById('turnoverChart').getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Turnover (₹)',
                        data: data,
                        backgroundColor: 'rgba(13, 110, 253, 0.6)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 3,
                        barPercentage: 0.7,
                        categoryPercentage: 0.85,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 9 } } },
                        y: {
                            beginAtZero: true,
                            grid: { display: true, color: 'rgba(0,0,0,0.05)' },
                            ticks: {
                                font: { size: 9 },
                                callback: function(value) {
                                    if (value >= 10000000) return '₹' + (value / 10000000).toFixed(1) + 'Cr';
                                    if (value >= 100000) return '₹' + (value / 100000).toFixed(1) + 'L';
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    animation: { duration: 400 }
                }
            });
        }

        // ---------- Load yearly summary ----------
        function loadYearlySummary() {
            $.ajax({
                url: summaryUrl,
                type: 'GET',
                success: function(data) {
                    let html = '';
                    if (data && data.years && data.years.length > 0) {
                        html = `
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Financial Year</th>
                                            <th class="text-end">Total Turnover</th>
                                            <th>Relative</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        data.years.forEach(function(item) {
                            let barWidth = item.percentage + '%';
                            html += `
                                <tr>
                                    <td><strong>${item.financial_year}</strong></td>
                                    <td class="text-end fw-semibold">₹ ${item.total.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td>
                                        <div class="progress" style="height: 18px; background: #e9ecef;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: ${barWidth};" aria-valuenow="${item.percentage}" aria-valuemin="0" aria-valuemax="100">
                                                ${item.percentage}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        html += `</tbody></table></div>`;
                    } else {
                        html = '<div class="text-center py-2 text-muted small"><i class="bi bi-folder2-open me-1"></i>No turnover data available.</div>';
                    }
                    $('#yearlySummaryContainer').html(html);
                },
                error: function(xhr) {
                    console.error('Summary error:', xhr);
                    if (xhr.status === 403) {
                        $('#yearlySummaryContainer').html(`
                            <div class="alert alert-danger small py-2">
                                <i class="bi bi-shield-lock me-1"></i>
                                <strong>403 Forbidden</strong> – The summary route is blocked. 
                                Please add <code>->withoutMiddleware(['verified'])</code> to the route 
                                <code>admin.client-master.turnovers.summary</code> in <code>web.php</code> 
                                and run <code>php artisan route:clear</code>.
                            </div>
                        `);
                    } else {
                        $('#yearlySummaryContainer').html('<div class="text-center py-2 text-danger small">Failed to load summary.</div>');
                    }
                }
            });
        }

        // ---------- Save turnover ----------
        $('#turnoverForm').on('submit', function(e) {
            e.preventDefault();
            let fy = $('#selectedFinancialYear').val();
            let months = {};
            $('.month-input').each(function() {
                let month = $(this).data('month');
                let val = $(this).val().trim();
                months[month] = val !== '' ? parseFloat(val) : null;
            });

            $.ajax({
                url: bulkSaveUrl,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    financial_year: fy,
                    months: months
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message || 'Turnover saved successfully!');
                        loadYearlySummary();
                    } else {
                        alert(response.message || 'Error saving turnover.');
                    }
                },
                error: function(xhr) {
                    console.error('Save error:', xhr);
                    if (xhr.status === 403) {
                        alert('403 Forbidden – please refresh the page and try again.');
                    } else {
                        alert('Error saving turnover. Check console.');
                    }
                }
            });
        });

        $('#financialYearSelect').on('change', function() {
            currentFY = $(this).val();
            loadTurnoverData(currentFY);
        });

        $(document).on('input', '.month-input', function() {
            updateTotal();
            updateChart();
        });

        loadTurnoverData(currentFY);
        loadYearlySummary();
    });
</script>
@endpush