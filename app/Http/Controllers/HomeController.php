<?php

namespace App\Http\Controllers;

use App\Contracts\WeatherServiceInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(WeatherServiceInterface $weatherService)
    {
        $weatherData = $weatherService->getCurrentWeather();
        return view('welcome', compact('weatherData'));
    }
}
