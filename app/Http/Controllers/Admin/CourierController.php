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

    public function edit($id)
    {
        $courier = Courier::findOrFail($id);

        $serviceList = collect([
            'JNE',
            'Ninja Express',
            'J&T Express',
            'SiCepat',
            'POS Indonesia'
        ])->merge(Courier::pluck('service'))
            ->unique()
            ->values();

        return view('admin.couriers', [
            'couriers' => Courier::all(),
            'serviceList' => $serviceList,
            'selectedService' => null,
            'editCourier' => $courier
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'estimate' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif,banned'
        ]);

        Courier::create($data);

        return back()->with('success', 'Kurir berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $courier = Courier::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'estimate' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif,banned'
        ]);

        $courier->update($data);

        return redirect('/admin/couriers')->with('success', 'Kurir berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Courier::findOrFail($id)->delete();

        return back()->with('success', 'Kurir berhasil dihapus.');
    }
}