<?php

namespace App\Services;

use App\Contracts\WeatherServiceInterface;
use App\DTOs\WeatherData;
use App\Exceptions\WeatherServiceException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;
use InvalidArgumentException;

class WeatherService implements WeatherServiceInterface
{
    private const CACHE_TTL = 300; // 5 minutes
    private const DEFAULT_UNITS = 'metric';
    private const DEFAULT_LAT = 31.5017;
    private const DEFAULT_LON = 34.4668;
    private const HTTP_TIMEOUT = 10;
    
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');
        $this->baseUrl = config('services.openweather.url');
    }

    public function getCurrentWeather(float $lat = self::DEFAULT_LAT, float $lon = self::DEFAULT_LON): array
    {
        try {
            $this->validateCoordinates($lat, $lon);
            $this->validateApiKey();
            
            $cacheKey = $this->generateCacheKey($lat, $lon);
            
            return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($lat, $lon) {
                return $this->fetchWeatherData($lat, $lon);
            });
            
        } catch (WeatherServiceException $e) {
            Log::warning('Weather service error: ' . $e->getMessage());
            return $this->getErrorResponse($e->getMessage());
        } catch (Exception $e) {
            Log::error('Unexpected error in weather service: ' . $e->getMessage(), [
                'lat' => $lat,
                'lon' => $lon,
                'exception' => $e
            ]);
            return $this->getErrorResponse('Unexpected error occurred.');
        }
    }

    private function fetchWeatherData(float $lat, float $lon): array
    {
        $response = Http::timeout(self::HTTP_TIMEOUT)->get("{$this->baseUrl}/weather", [
            'lat'   => $lat,
            'lon'   => $lon,
            'appid' => $this->apiKey,
            'units' => self::DEFAULT_UNITS,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $this->formatWeatherData($data);
        }

        $this->logApiError($response);
        throw WeatherServiceException::apiError(
            $response->status(),
            $response->json()['message'] ?? 'Unknown error'
        );
    }

    private function validateCoordinates(float $lat, float $lon): void
    {
        if ($lat < -90 || $lat > 90) {
            throw WeatherServiceException::invalidCoordinates($lat, $lon);
        }
        
        if ($lon < -180 || $lon > 180) {
            throw WeatherServiceException::invalidCoordinates($lat, $lon);
        }
    }

    private function validateApiKey(): void
    {
        if (empty($this->apiKey)) {
            throw WeatherServiceException::apiKeyNotConfigured();
        }
    }

    private function generateCacheKey(float $lat, float $lon): string
    {
        return "weather_" . number_format($lat, 4) . "_" . number_format($lon, 4);
    }

    private function formatWeatherData(array $data): array
    {
        if (!$this->isValidWeatherData($data)) {
            Log::warning('Invalid weather data received', ['data' => $data]);
            throw WeatherServiceException::invalidData();
        }

        $weatherData = WeatherData::fromArray([
            'city' => $data['name'] ?? 'Unknown',
            'country' => $data['sys']['country'] ?? '',
            'temperature' => $this->roundTemperature($data['main']['temp'] ?? 0),
            'feels_like' => $this->roundTemperature($data['main']['feels_like'] ?? 0),
            'description' => $this->formatDescription($data['weather'][0]['description'] ?? 'No description'),
            'icon' => $data['weather'][0]['icon'] ?? '01d',
            'humidity' => (int) ($data['main']['humidity'] ?? 0),
            'pressure' => (int) ($data['main']['pressure'] ?? 0),
            'wind_speed' => round($data['wind']['speed'] ?? 0, 1),
            'visibility' => $this->formatVisibility($data['visibility'] ?? null),
            'clouds' => (int) ($data['clouds']['all'] ?? 0),
            'sunrise' => $this->formatTime($data['sys']['sunrise'] ?? null),
            'sunset' => $this->formatTime($data['sys']['sunset'] ?? null),
        ]);

        return $weatherData->toArray();
    }

    private function isValidWeatherData(array $data): bool
    {
        return isset($data['name'], $data['main'], $data['weather'][0]);
    }

    private function roundTemperature(float $temp): int
    {
        return (int) round($temp);
    }

    private function formatDescription(string $description): string
    {
        return ucfirst(trim($description));
    }

    private function formatVisibility(?int $visibility): ?float
    {
        return $visibility ? round($visibility / 1000, 1) : null;
    }

    private function formatTime(?int $timestamp): ?string
    {
        return $timestamp ? date('H:i', $timestamp) : null;
    }

    private function logApiError($response): void
    {
        Log::warning("Weather API failed with status {$response->status()}.", [
            'body' => $response->body(),
            'status' => $response->status()
        ]);
    }

    private function getErrorResponse(string $message): array
    {
        return [
            'success' => false,
            'error'   => true,
            'message' => $message,
        ];
    }
}
