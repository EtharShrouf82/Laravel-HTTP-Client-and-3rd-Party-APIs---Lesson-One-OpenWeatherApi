<?php

namespace App\Http\Controllers;

use App\Contracts\WeatherServiceInterface;
use App\Services\WeatherService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(WeatherService $weatherService)
    {
        $weatherData = $weatherService->getCurrentWeather();
        return view('welcome', compact('weatherData'));
    }
}
