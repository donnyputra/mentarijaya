@once
<style>
    .sales-summary {
        --summary-accent: #8a8f98;
        --summary-bg: #efefef;
        --summary-card-bg: #ffffff;
        background: linear-gradient(180deg, #f3f3f3 0%, #ededed 100%);
        border-radius: 18px;
        padding: 2rem 1.75rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .sales-summary-title {
        margin-bottom: 1.5rem;
        line-height: 0.92;
    }

    .sales-summary-date {
        display: block;
        font-size: clamp(1.125rem, 3.75vw, 0.75rem);
        font-weight: 300;
        letter-spacing: -0.06em;
        color: #101010;
    }

    .sales-summary-heading {
        display: block;
        font-size: clamp(1.125rem, 4.5vw, 1.125rem);
        font-weight: 300;
        letter-spacing: -0.07em;
        color: #101010;
    }

    .sales-summary-grid {
        display: grid;
        grid-template-columns: 0.8fr 1.2fr 1.2fr 1.2fr;
        gap: 1rem;
    }

    .sales-summary-card {
        position: relative;
        background: var(--summary-card-bg);
        border-radius: 0;
        min-height: 100%;
        padding: 1rem 1.1rem 1.15rem 1.55rem;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
    }

    .sales-summary-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 14px;
        height: 100%;
        background: var(--summary-accent);
    }

    .sales-summary-card-title {
        margin-bottom: 0.7rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-align: right;
        color: #111;
    }

    .sales-summary-card-title.left {
        text-align: left;
    }

    .sales-summary-type-row,
    .sales-summary-metric-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .sales-summary-type-row + .sales-summary-type-row,
    .sales-summary-metric-row + .sales-summary-metric-row {
        margin-top: 0.55rem;
    }

    .sales-summary-type-code {
        font-size: 1.0875rem;
        font-weight: 700;
        letter-spacing: -0.03em;
        color: #111;
    }

    .sales-summary-type-count {
        font-size: 0.975rem;
        font-weight: 500;
        color: #111;
    }

    .sales-summary-metric-label {
        display: block;
        margin-bottom: 0.1rem;
        font-size: 0.54rem;
        font-weight: 500;
        text-align: right;
        color: #444;
    }

    .sales-summary-metric-value {
        display: block;
        width: 100%;
        font-size: 1.425rem;
        line-height: 1;
        font-weight: 500;
        letter-spacing: -0.04em;
        text-align: right;
        color: #111;
    }

    .sales-summary-metric-row.total .sales-summary-metric-value {
        border-bottom: 2px solid var(--summary-accent);
        padding-bottom: 0.35rem;
        font-size: 1.7625rem;
        font-weight: 800;
    }

    .sales-summary-muted {
        color: #505050;
        font-size: 0.62em;
        font-weight: 500;
    }

    @media (max-width: 991.98px) {
        .sales-summary-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .sales-summary {
            padding: 1.35rem 1rem 1rem;
        }

        .sales-summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endonce

<div class="sales-summary">
    <div class="sales-summary-title">
        <span class="sales-summary-date">{{ $salesSummary['display_date'] }}</span>
        <span class="sales-summary-heading">Sales Summary</span>
    </div>

    <div class="sales-summary-grid">
        <div class="sales-summary-card">
            @foreach($salesSummary['category_counts'] as $categoryCount)
            <div class="sales-summary-type-row">
                <span class="sales-summary-type-code">{{ $categoryCount['code'] }}</span>
                <span class="sales-summary-type-count">{{ $categoryCount['count'] }}</span>
            </div>
            @endforeach
        </div>

        <div class="sales-summary-card">
            <div class="sales-summary-card-title">Sales W</div>
            @foreach($salesSummary['metric_rows'] as $metricRow)
            <div class="sales-summary-metric-row {{ $loop->first ? 'total' : '' }}">
                <div class="w-100">
                    <span class="sales-summary-metric-label">{{ $metricRow['label'] }}</span>
                    <span class="sales-summary-metric-value">{{ number_format($metricRow['weight'], 2, ',', '.') }} <span class="sales-summary-muted">gr</span></span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="sales-summary-card">
            <div class="sales-summary-card-title">Sales Today</div>
            @foreach($salesSummary['metric_rows'] as $metricRow)
            <div class="sales-summary-metric-row {{ $loop->first ? 'total' : '' }}">
                <div class="w-100">
                    <span class="sales-summary-metric-label">{{ $metricRow['label'] }}</span>
                    <span class="sales-summary-metric-value">{{ number_format($metricRow['sales'], 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="sales-summary-card">
            <div class="sales-summary-card-title">Sales Avg</div>
            @foreach($salesSummary['metric_rows'] as $metricRow)
            <div class="sales-summary-metric-row {{ $loop->first ? 'total' : '' }}">
                <div class="w-100">
                    <span class="sales-summary-metric-label">{{ $metricRow['label'] }}</span>
                    <span class="sales-summary-metric-value">{{ number_format($metricRow['average'], 0, ',', '.') }}<span class="sales-summary-muted">/gr</span></span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
