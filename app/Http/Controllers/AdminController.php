<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 📊 stats
        $totalUsers = User::where('role', 'user')->count();
        $totalDoctors = User::where('role', 'doctor')->count();
        $totalAppointments = Appointment::count();

        // Active doctors = doctors with at least 1 appointment
        $activeDoctors = Appointment::distinct('doctor_id')->count('doctor_id');

        // 📈 Daily activity (last 7 days)
        $days = collect();
        $counts = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');

            $count = Appointment::whereDate('date', $date)->count;

            $days->push(Carbon::parse($date)->format('M d'));
            $counts->push($count);
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDoctors',
            'totalAppointments',
            'activeDoctors',
            'days',
            'counts'
        ));
        
    }

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