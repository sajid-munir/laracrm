@include('partials.header', ['title' => 'Dashboard'])

<div class="container-fluid">
    <div class="row">
        @foreach ([
            ['label' => 'Total Users', 'value' => $totalUsers, 'icon' => 'ri-user-add-line', 'bg' => 'custome-1-bg'],
            ['label' => 'Total Agents', 'value' => $totalAgents, 'icon' => 'ri-user-add-line', 'bg' => 'custome-2-bg'],
            ['label' => 'Total Leads', 'value' => $totalLeads, 'icon' => 'ri-user-received-line', 'bg' => 'custome-3-bg'],
            ['label' => 'Total Notes', 'value' => $totalNotes, 'icon' => 'ri-chat-3-line', 'bg' => 'custome-4-bg'],
        ] as $tile)
            <div class="col-sm-6 col-xxl-3 col-lg-6">
                <div class="main-tiles border-5 border-0 card-hover card o-hidden">
                    <div class="{{ $tile['bg'] }} b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="media-body p-0">
                                <span class="m-0">{{ $tile['label'] }}</span>
                                <h4 class="mb-0 counter">{{ $tile['value'] }}</h4>
                            </div>
                            <div class="align-self-center text-center">
                                <i class="{{ $tile['icon'] }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Recent leads -->
        <div class="col-xl-6">
            <div class="card o-hidden card-hover">
                <div class="card-header card-header-top card-header--2 px-0 pt-0">
                    <div class="card-header-title">
                        <h4>Recent Leads</h4>
                    </div>
                    <div class="best-selling-box d-sm-flex d-none">
                        <a href="{{ route('leads.index') }}" class="btn btn-sm btn-theme">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="best-selling-table table border-0">
                            <tbody>
                                @forelse ($recentLeads as $lead)
                                    <tr>
                                        <td>
                                            <div class="best-product-box">
                                                <div class="product-name">
                                                    <h5>{{ $lead->lead_title }}</h5>
                                                    <h6>{{ $lead->customer_name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="product-detail-box">
                                                <h6>Agent</h6>
                                                <h5>{{ $lead->agent?->employee_name ?? 'Unassigned' }}</h5>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="product-detail-box">
                                                <h6>Status</h6>
                                                <h5><span class="badge-status status-{{ str_replace(' ', '-', $lead->status) }}">{{ $lead->status }}</span></h5>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="product-detail-box">
                                                <h6>Received</h6>
                                                <h5>{{ $lead->created_at?->format('Y-m-d') }}</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-4">No leads yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leads by status -->
        <div class="col-xxl-4 col-md-6">
            <div class="h-100">
                <div class="card o-hidden card-hover">
                    <div class="card-header border-0">
                        <div class="card-header-title">
                            <h4>Leads by Status</h4>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="pie-chart">
                            <div id="leadsStatusChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
<script>
    new ApexCharts(document.querySelector('#leadsStatusChart'), {
        series: @json(array_values($statusCounts)),
        labels: @json(array_keys($statusCounts)),
        colors: ['#1d8a4a', '#b3261e', '#2454b8', '#888888'],
        chart: { type: 'donut', height: 320 },
        legend: { position: 'bottom' },
        dataLabels: { enabled: true }
    }).render();
</script>

@include('partials.footer')
