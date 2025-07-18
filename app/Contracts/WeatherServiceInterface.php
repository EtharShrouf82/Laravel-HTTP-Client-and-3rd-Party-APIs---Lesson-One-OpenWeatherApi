<?php

namespace App\Contracts;

interface WeatherServiceInterface
{
    /**
     * Get current weather data for specified coordinates
     *
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @return array Weather data or error response
     */
    public function getCurrentWeather(float $lat = 31.5017, float $lon = 34.4668): array;
} 