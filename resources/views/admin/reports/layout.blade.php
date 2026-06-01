<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laporan CAMPIFY')</title>
    <style>
        @page {
            margin: 25mm 18mm 30mm 18mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.5;
        }

        /* ===== HEADER ===== */
        .report-header {
            border-bottom: 3px solid #059669;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .brand-name {
            font-size: 20pt;
            font-weight: bold;
            color: #065f46;
            letter-spacing: 3px;
        }
        .brand-tagline {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 2px;
        }
        .report-title-text {
            font-size: 13pt;
            font-weight: bold;
            color: #065f46;
            margin-top: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .report-meta {
            font-size: 8pt;
            color: #475569;
            margin-top: 4px;
        }

        /* ===== SECTION ===== */
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #065f46;
            margin-top: 18px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid #d1fae5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== DATA TABLE ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }
        .data-table th {
            background-color: #065f46;
            color: #ffffff;
            padding: 7px 8px;
            text-align: left;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #064e3b;
        }
        .data-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            font-size: 8pt;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f0fdf4;
        }

        /* ===== SUMMARY TABLE ===== */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .summary-table td {
            padding: 6px 10px;
            border: 1px solid #d1fae5;
            font-size: 8.5pt;
        }
        .summary-table .label {
            background-color: #f0fdf4;
            color: #065f46;
            font-weight: bold;
            width: 55%;
        }
        .summary-table .value {
            text-align: right;
            font-weight: bold;
            color: #1e293b;
        }

        /* ===== STAT BOX ===== */
        .stat-boxes {
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-box {
            border: 1px solid #d1fae5;
            background-color: #f0fdf4;
            padding: 10px 12px;
            text-align: center;
        }
        .stat-box .stat-value {
            font-size: 16pt;
            font-weight: bold;
            color: #065f46;
        }
        .stat-box .stat-label {
            font-size: 7pt;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 3px;
        }

        /* ===== SIGNATURE ===== */
        .signature-area {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            text-align: center;
            padding: 10px;
        }
        .signature-line {
            border-bottom: 1px solid #1e293b;
            width: 180px;
            margin: 50px auto 5px auto;
        }
        .signature-name {
            font-size: 8pt;
            font-weight: bold;
            color: #1e293b;
        }
        .signature-title {
            font-size: 7pt;
            color: #475569;
        }

        /* ===== BADGES ===== */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 6.5pt;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-green { background-color: #d1fae5; color: #065f46; }
        .badge-red { background-color: #fee2e2; color: #991b1b; }
        .badge-yellow { background-color: #fef3c7; color: #92400e; }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-gray { background-color: #f1f5f9; color: #475569; }

        /* ===== UTILITIES ===== */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .text-orange { color: #d97706; }
        .text-sm { font-size: 7pt; }
        .page-break { page-break-after: always; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="report-header">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="vertical-align: top;">
                    <div class="brand-name">CAMPIFY</div>
                    <div class="brand-tagline">Marketplace Penyewaan & Jual Beli Alat Camping</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-size: 7.5pt; color: #475569;">Tanggal Cetak</div>
                    <div style="font-size: 9pt; font-weight: bold; color: #1e293b;">{{ $printDate }}</div>
                    @if(isset($from) && isset($to) && $from && $to)
                        <div style="font-size: 7.5pt; color: #475569; margin-top: 4px;">Periode Laporan</div>
                        <div style="font-size: 8pt; font-weight: bold; color: #065f46;">{{ $from }} s/d {{ $to }}</div>
                    @endif
                </td>
            </tr>
        </table>
        <div class="report-title-text">@yield('report_title')</div>
    </div>

    {{-- Content --}}
    @yield('content')

    {{-- Signature --}}
    <table class="signature-area" cellpadding="0" cellspacing="0">
        <tr>
            <td width="50%"></td>
            <td width="50%" class="signature-box">
                <div style="font-size: 7.5pt; color: #475569;">Mengetahui,</div>
                <div class="signature-line"></div>
                <div class="signature-name">Administrator CAMPIFY</div>
                <div class="signature-title">Platform Administrator</div>
            </td>
        </tr>
    </table>

    {{-- Footer with page numbers (DomPDF PHP scripting) --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Dokumen ini dicetak secara otomatis dari sistem CAMPIFY  |  Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 6.5;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 28;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.27, 0.35, 0.43));
        }
    </script>
</body>
</html>
