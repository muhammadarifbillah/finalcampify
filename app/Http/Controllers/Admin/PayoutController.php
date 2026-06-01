<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $query = Payout::with(['sellerOrder.store', 'sellerOrder.seller', 'sellerOrder.order']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('sellerOrder', function ($q) use ($s) {
                $q->where('seller_order_number', 'like', "%$s%")
                  ->orWhereHas('order', fn($oq) => $oq->where('order_number', 'like', "%$s%"));
            });
        }

        $payouts = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.payouts', compact('payouts'));
    }

    public function export(Request $request)
    {
        $query = Payout::with(['sellerOrder.store', 'sellerOrder.seller', 'sellerOrder.order']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('sellerOrder', function ($q) use ($s) {
                $q->where('seller_order_number', 'like', "%$s%")
                  ->orWhereHas('order', fn($oq) => $oq->where('order_number', 'like', "%$s%"));
            });
        }

        $rows = $query->orderBy('created_at', 'desc')->get();

        $pdfData = [
            'printDate' => now()->format('d/m/Y H:i:s'),
            'payouts' => $rows,
            'filters' => $request->query(),
        ];

        // Ensure proper Unicode fonts and remote assets enabled for DomPDF
        Pdf::setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        // If there is a TTF font uploaded to storage/fonts, use it and pass to view
        $fontInfo = null;
        $fontDir = storage_path('fonts');
        if (is_dir($fontDir)) {
            $files = glob($fontDir . '/*.ttf');
            if (!empty($files)) {
                // pick the first font file found
                $path = $files[0];
                $family = pathinfo($path, PATHINFO_FILENAME);
                $fontInfo = ['path' => $path, 'family' => $family];

                // set defaultFont to this family so DomPDF uses it
                Pdf::setOptions(array_merge(Pdf::getOptions()->all(), [
                    'defaultFont' => $family,
                ]));
            }
        }

        $pdfData['customFont'] = $fontInfo;

        $filename = 'laporan.pdf';

        $pdf = Pdf::loadView('admin.reports.payouts', $pdfData);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}
