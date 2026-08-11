<?php

namespace App\Http\Controllers;

use App\Models\Kullanici;
use App\Models\Siparis;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $todayStartsAt = today()->startOfDay();
        $todayEndsAt = today()->endOfDay();

        return Inertia::render('Dashboard', [
            'metrics' => [
                'customerCount' => Kullanici::query()->count(),
                'todaySalesTotal' => (float) Siparis::query()
                    ->whereBetween('tarih', [$todayStartsAt, $todayEndsAt])
                    ->sum('fiyat'),
                'pendingShipmentCount' => Siparis::query()
                    ->whereNull('kargo_kodu')
                    ->count(),
            ],
        ]);
    }
}
