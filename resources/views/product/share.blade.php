<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $product->name }}</title>
    @vite('resources/css/app.css')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            width: 800px;
            height: 800px;
            overflow: hidden;
            background: #f1f5f9;
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
        }

        /* Outer wrapper */
        .share-wrapper {
            width: 800px;
            height: 800px;
            background: #ffffff;
        }

        /* Main card — 800×800, no margins */
        .card {
            width: 800px;
            height: 800px;
            border-radius: 0;
            background: #ffffff;
            box-shadow: none;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── Hero: image left + info right (420px) ─────────── */
        .hero-area {
            flex: 0 0 420px;
            display: flex;
            flex-direction: row;
            border-bottom: 1px solid #f1f5f9;
            overflow: hidden;
        }

        /* Left: image column (340px fixed) */
        .image-area {
            flex: 0 0 340px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border-right: 1px solid #f1f5f9;
            padding: 20px;
        }
        .image-area img {
            max-height: 380px;
            max-width: 300px;
            object-fit: contain;
        }
        .image-placeholder {
            color: #cbd5e1;
        }

        /* Right: info column */
        .info-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 32px 36px 32px 32px;
            min-width: 0;
        }
        .brand {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .product-name {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .price-from-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }
        .price-from-label {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }
        .price-by-label {
            font-size: 16px;
            color: #64748b;
            font-weight: 500;
            margin-right: 4px;
        }
        .price-row {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: nowrap;
        }
        .price-current {
            font-size: 44px;
            font-weight: 800;
            color: #2563eb;
            line-height: 1;
            white-space: nowrap;
        }
        .price-old {
            font-size: 20px;
            color: #94a3b8;
            text-decoration: line-through;
            white-space: nowrap;
        }
        .discount-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: #dcfce7;
            color: #16a34a;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }
        .lowest-price {
            margin-top: 10px;
            font-size: 12px;
            color: #94a3b8;
        }
        .lowest-price strong {
            color: #475569;
            font-weight: 600;
        }
        .price-stats {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 12px;
            padding: 10px 14px;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #f1f5f9;
        }
        .price-stat {
            display: flex;
            align-items: center;
            gap: 6px;
            flex: 1;
        }
        .price-stat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .price-stat-dot--low  { background: #22c55e; }
        .price-stat-dot--high { background: #f97316; }
        .price-stat-label {
            font-size: 11px;
            color: #94a3b8;
            white-space: nowrap;
        }
        .price-stat-value {
            font-size: 13px;
            font-weight: 700;
            margin-left: auto;
        }
        .price-stat-value--low  { color: #16a34a; }
        .price-stat-value--high { color: #ea580c; }
        .price-stat-divider {
            width: 1px;
            height: 28px;
            background: #e2e8f0;
            flex-shrink: 0;
        }

        /* ── Chart (fills remaining space) ────────────────── */
        .chart-area {
            flex: 1 1 0;
            display: flex;
            flex-direction: row;
            border-bottom: 1px solid #f1f5f9;
            overflow: hidden;
        }
        .chart-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 14px 16px 10px 40px;
            min-width: 0;
        }
        .chart-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .chart-canvas-wrap {
            flex: 1;
            min-height: 0;
            position: relative;
        }
        .chart-stats {
            flex: 0 0 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 12px;
            padding: 16px 20px 16px 16px;
            border-left: 1px solid #f1f5f9;
        }
        .chart-stat-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .chart-stat-header {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .chart-stat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .chart-stat-dot--low  { background: #22c55e; }
        .chart-stat-dot--high { background: #f97316; }
        .chart-stat-label {
            font-size: 10px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 600;
        }
        .chart-stat-value {
            font-size: 15px;
            font-weight: 700;
            padding-left: 13px;
        }
        .chart-stat-value--low  { color: #16a34a; }
        .chart-stat-value--high { color: #ea580c; }
        .chart-stat-count {
            font-size: 10px;
            color: #cbd5e1;
            padding-left: 13px;
        }

        /* Chart placeholder (no history) */
        .chart-no-history {
            flex: 1 1 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
        }
        .chart-no-history-inner {
            text-align: center;
        }
        .chart-no-history-inner p {
            font-size: 13px;
            color: #cbd5e1;
            margin-top: 8px;
        }

        /* ── CTA banner ─────────────────────────────────── */
        .cta-banner {
            flex: 0 0 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #eff6ff;
            border-top: 1px solid #dbeafe;
            border-bottom: 1px solid #dbeafe;
        }
        .cta-icon {
            color: #2563eb;
            flex-shrink: 0;
        }
        .cta-banner span {
            font-size: 13px;
            color: #1e40af;
        }
        .cta-banner strong {
            font-weight: 700;
        }

        /* ── Footer (50px) ─────────────────────────────────── */
        .footer-brand {
            flex: 0 0 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #1e3a8a;
        }
        .footer-brand img {
            height: 20px;
        }
        .footer-brand span {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="share-wrapper">
        <div class="card">

            {{-- ── Hero (image left + info right) ── --}}
            <div class="hero-area">

                {{-- Image --}}
                <div class="image-area">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                    @else
                        <div class="image-placeholder">
                            <svg width="80" height="80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="info-area">
                    @if($product->brand)
                        <p class="brand">{{ $product->brand }}</p>
                    @endif

                    <h1 class="product-name">{{ $product->name }}</h1>

                    @if($product->old_price && $product->old_price > $product->price)
                        @php $discountPct = round((1 - $product->price / $product->old_price) * 100); @endphp
                        <div class="price-from-row">
                            <span class="price-from-label">de</span>
                            <span class="price-old">R$&nbsp;{{ number_format($product->old_price, 2, ',', '.') }}</span>
                            <span class="discount-badge">{{ $discountPct }}% OFF</span>
                        </div>
                    @endif

                    <div class="price-row">
                        <span class="price-by-label">por</span>
                        <span class="price-current">R$&nbsp;{{ number_format($product->price, 2, ',', '.') }}</span>
                    </div>
                </div>

            </div>{{-- end hero-area --}}

            {{-- ── Chart ── --}}
            @php $chartData = $priceHistory['has_history'] && count($priceHistory['data']) > 1 ? $priceHistory['data'] : null; @endphp

            @if($chartData)
                <div class="chart-area">
                    <div class="chart-main">
                        <p class="chart-label">Histórico de preços</p>
                        <div class="chart-canvas-wrap">
                            <canvas id="shareChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-stats">
                        <div class="chart-stat-item">
                            <div class="chart-stat-header">
                                <span class="chart-stat-dot chart-stat-dot--low"></span>
                                <span class="chart-stat-label">Menor preço</span>
                            </div>
                            <span class="chart-stat-value chart-stat-value--low">R$&nbsp;{{ number_format($priceHistory['lowest_price'], 2, ',', '.') }}</span>
                        </div>
                        <div class="chart-stat-item">
                            <div class="chart-stat-header">
                                <span class="chart-stat-dot chart-stat-dot--high"></span>
                                <span class="chart-stat-label">Maior preço</span>
                            </div>
                            <span class="chart-stat-value chart-stat-value--high">R$&nbsp;{{ number_format($priceHistory['highest_price'], 2, ',', '.') }}</span>
                        </div>
                        <span class="chart-stat-count">{{ count($chartData) }} registros</span>
                    </div>
                </div>
            @else
                <div class="chart-no-history">
                    <div class="chart-no-history-inner">
                        <svg width="36" height="36" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p>Histórico em breve</p>
                    </div>
                </div>
            @endif

            {{-- ── CTA ── --}}
            <div class="cta-banner">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="cta-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>Quer pagar mais barato? <strong>Crie um alerta de preço.</strong></span>
            </div>

            {{-- ── Footer ── --}}
            <div class="footer-brand">
                <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="Monitor de Preços">
                <span>monitordeprecos.com.br</span>
            </div>

        </div>
    </div>

    @if($chartData)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                const data = @json($chartData);
                const labels = data.map(function (d) { return d.formatted_date; });
                const prices = data.map(function (d) { return parseFloat(d.price); });

                const minPrice = Math.min.apply(null, prices);
                const maxPrice = Math.max.apply(null, prices);
                const padding  = (maxPrice - minPrice) * 0.15 || maxPrice * 0.1;

                const canvas  = document.getElementById('shareChart');
                const wrap    = canvas.parentElement;
                canvas.width  = wrap.offsetWidth;
                canvas.height = wrap.offsetHeight;

                new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: prices,
                            borderColor: '#06b6d4',
                            backgroundColor: (ctx) => {
                                const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, wrap.offsetHeight);
                                gradient.addColorStop(0, 'rgba(6,182,212,0.18)');
                                gradient.addColorStop(1, 'rgba(6,182,212,0)');
                                return gradient;
                            },
                            borderWidth: 2.5,
                            pointRadius: 0,
                            pointHoverRadius: 0,
                            fill: true,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false },
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: {
                                    color: '#9ca3af',
                                    font: { size: 11 },
                                    maxTicksLimit: 8,
                                    maxRotation: 0,
                                }
                            },
                            y: {
                                position: 'right',
                                min: minPrice - padding,
                                max: maxPrice + padding,
                                grid: { color: '#f1f5f9' },
                                border: { display: false },
                                ticks: {
                                    color: '#94a3b8',
                                    font: { size: 11 },
                                    maxTicksLimit: 4,
                                    callback: function (value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 0 });
                                    }
                                }
                            }
                        }
                    }
                });
            })();
        </script>
    @endif
</body>
</html>
