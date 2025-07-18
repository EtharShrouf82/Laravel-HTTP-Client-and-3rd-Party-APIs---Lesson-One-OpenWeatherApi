<?php

namespace App\Exceptions;

use Exception;

class WeatherServiceException extends Exception
{
    public static function apiKeyNotConfigured(): self
    {
        return new self('OpenWeather API key not configured.');
    }

    public static function invalidCoordinates(float $lat, float $lon): self
    {
        return new self("Invalid coordinates: lat={$lat}, lon={$lon}");
    }

    public static function apiError(int $statusCode, string $message): self
    {
        return new self("API error ({$statusCode}): {$message}");
    }

    public static function invalidData(): self
    {
        return new self('Invalid weather data received from API.');
    }

    public static function timeout(): self
    {
        return new self('Weather API request timed out.');
    }
} 