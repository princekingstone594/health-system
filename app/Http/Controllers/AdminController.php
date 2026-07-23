<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function revenue()
    {
        // 💰 Total revenue
        $totalRevenue = Appointment::where('payment_status', 'paid')
            ->sum('price');

        // 📅 Today's revenue
        $todayRevenue = Appointment::where('payment_status', 'paid')
            ->whereDate('updated_at', Carbon::today())
            ->sum('price');

        // 📆 This month's revenue
        $monthlyRevenue = Appointment::where('payment_status', 'paid')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->sum('price');

        // 🧾 Recent payments
        $recentPayments = Appointment::where('payment_status', 'paid')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.revenue', compact(
            'totalRevenue',
            'todayRevenue',
            'monthlyRevenue',
            'recentPayments'
        ));
    }
}