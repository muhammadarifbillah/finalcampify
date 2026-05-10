<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index(Request $request)
    {
        $selectedService = $request->query('service');

        $defaultServices = collect([
            'JNE',
            'Ninja Express',
            'J&T Express',
            'SiCepat',
            'POS Indonesia'
        ]);

        $serviceList = $defaultServices->merge(Courier::pluck('service'))
            ->unique()
            ->values();

        $couriers = Courier::when($selectedService, function ($query, $service) {
            return $query->where('service', $service);
        })->get();

        return view('admin.couriers', [
            'couriers' => $couriers,
            'serviceList' => $serviceList,
            'selectedService' => $selectedService
        ]);
    }

    // ✅ KURIR HANYA DATA SAJA - Tidak ada operasi edit, tambah, atau hapus
    // Kurir dikelola hanya untuk viewing dan pairing dengan produk
    // Metode edit(), store(), update(), dan destroy() dihapus karena kurir adalah data baca-saja
}
