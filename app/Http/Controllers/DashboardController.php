<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index(Request $request){

        $cards = [
            [
                'title' => 'Total Employees',
                'icon' => 'fa-sharp fa-regular fa-user',
                'value' => 313,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+10%',
                'time_period' => 'Year',
            ],
            [
                'title' => 'On Leave Employees',
                'icon' => 'fa-sharp fa-regular fa-house-person-leave',
                'value' => 55,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+2.15%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Total Projects',
                'icon' => 'fa-sharp fa-regular fa-gear',
                'value' => 313,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+5.15%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Complete Projects',
                'icon' => 'fa-light fa-badge-check',
                'value' => 150,
                'trend_class' => 'price-decrease',
                'trend_icon' => 'fa-arrow-down',
                'trend_value' => '+5.5%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Total Clients',
                'icon' => 'fa-sharp fa-regular fa-users',
                'value' => 151,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+2.15%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Total Revenues',
                'icon' => 'fa-regular fa-arrow-up-right-dots',
                'value' => '$55',
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+2.15%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Total Jobs',
                'icon' => 'fa-sharp fa-light fa-suitcase',
                'value' => 55,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+2.15%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Total Tickets',
                'icon' => 'fa-solid fa-ticket',
                'value' => 55,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+2.15%',
                'time_period' => 'Month',
            ],
        ];

        return view('business.index', compact('cards'));
    }
}
