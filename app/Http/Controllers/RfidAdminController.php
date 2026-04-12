<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\RfidTag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RfidAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::with(['user', 'rfidTag']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('plat_nomor', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('rfidTag', function($qr) use ($search) {
                      $qr->where('uid', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'linked') {
                $query->has('rfidTag');
            } elseif ($request->status === 'unlinked') {
                $query->doesntHave('rfidTag');
            }
        }

        $vehicles = $query->orderBy('id_kendaraan', 'desc')->paginate(15)->withQueryString();
        $title = 'Manajemen RFID Kendaraan';
        return view('rfid.index', compact('title', 'vehicles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:tb_kendaraan,id_kendaraan'],
            'rfid_uid' => [
                'required',
                'string',
                'max:128',
                Rule::unique('rfid_tags', 'uid')->ignore((int) $request->input('vehicle_id'), 'id_kendaraan'),
            ],
        ]);

        RfidTag::updateOrCreate(
            ['id_kendaraan' => $data['vehicle_id']],
            [
                'uid' => $data['rfid_uid'],
                'status' => 'active'
            ]
        );

        $vehicle = Kendaraan::findOrFail($data['vehicle_id']);
        return back()->with('success', 'RFID berhasil dihubungkan ke kendaraan ' . $vehicle->plat_nomor);
    }

    public function unlink($id)
    {
        $rfidTag = RfidTag::where('id_kendaraan', $id)->firstOrFail();
        $rfidTag->delete();

        return back()->with('success', 'Hubungan RFID berhasil dihapus dari kendaraan.');
    }
}

