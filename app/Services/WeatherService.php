<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WeatherService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');
        $this->baseUrl = config('services.openweather.url');
    }

    public function getCurrentWeather(): array
    {
        $lat = 31.5017;
        $lon = 34.4668;

        if (empty($this->apiKey)) {
            return $this->getErrorResponse('OpenWeather API key not configured.');
        }

        try {
            $response = Http::get("{$this->baseUrl}/weather", [
                'lat'   => $lat,
                'lon'   => $lon,
                'appid' => $this->apiKey,
                'units' => 'metric',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatWeatherData($data);
            }

            $this->logApiError($response);
            return $this->getErrorResponse("API error ({$response->status()}): " . ($response->json()['message'] ?? 'Unknown error'));

        } catch (Exception $e) {
            Log::error('Weather API exception: ' . $e->getMessage());
            return $this->getErrorResponse('Unexpected error occurred.');
        }
    }

    private function formatWeatherData(array $data): array
    {
        return [
            'success' => true,
            'city' => $data['name'] ?? 'Unknown',
            'country' => $data['sys']['country'] ?? '',
            'temperature' => round($data['main']['temp'] ?? 0),
            'feels_like' => round($data['main']['feels_like'] ?? 0),
            'description' => ucfirst($data['weather'][0]['description'] ?? 'No description'),
            'icon' => $data['weather'][0]['icon'] ?? '01d',
            'humidity' => $data['main']['humidity'] ?? 0,
            'pressure' => $data['main']['pressure'] ?? 0,
            'wind_speed' => $data['wind']['speed'] ?? 0,
            'visibility' => isset($data['visibility']) ? $data['visibility'] / 1000 : null,
            'clouds' => $data['clouds']['all'] ?? 0,
            'sunrise' => isset($data['sys']['sunrise']) ? date('H:i', $data['sys']['sunrise']) : null,
            'sunset' => isset($data['sys']['sunset']) ? date('H:i', $data['sys']['sunset']) : null,
        ];
    }

    private function logApiError($response): void
    {
        Log::warning("Weather API failed with status {$response->status()}.", [
            'body' => $response->body()
        ]);
    }

    private function getErrorResponse(string $message): array
    {
        return [
            'error'   => true,
            'message' => $message,
        ];
    }
}
