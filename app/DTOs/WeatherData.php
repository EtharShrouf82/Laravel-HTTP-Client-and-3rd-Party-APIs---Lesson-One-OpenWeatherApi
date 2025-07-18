<?php

namespace App\DTOs;

class WeatherData
{
    public function __construct(
        public readonly string $city,
        public readonly string $country,
        public readonly int $temperature,
        public readonly int $feelsLike,
        public readonly string $description,
        public readonly string $icon,
        public readonly int $humidity,
        public readonly int $pressure,
        public readonly float $windSpeed,
        public readonly ?float $visibility,
        public readonly int $clouds,
        public readonly ?string $sunrise,
        public readonly ?string $sunset
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            city: $data['city'] ?? 'Unknown',
            country: $data['country'] ?? '',
            temperature: $data['temperature'] ?? 0,
            feelsLike: $data['feels_like'] ?? 0,
            description: $data['description'] ?? 'No description',
            icon: $data['icon'] ?? '01d',
            humidity: $data['humidity'] ?? 0,
            pressure: $data['pressure'] ?? 0,
            windSpeed: $data['wind_speed'] ?? 0.0,
            visibility: $data['visibility'] ?? null,
            clouds: $data['clouds'] ?? 0,
            sunrise: $data['sunrise'] ?? null,
            sunset: $data['sunset'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'city' => $this->city,
            'country' => $this->country,
            'temperature' => $this->temperature,
            'feels_like' => $this->feelsLike,
            'description' => $this->description,
            'icon' => $this->icon,
            'humidity' => $this->humidity,
            'pressure' => $this->pressure,
            'wind_speed' => $this->windSpeed,
            'visibility' => $this->visibility,
            'clouds' => $this->clouds,
            'sunrise' => $this->sunrise,
            'sunset' => $this->sunset,
        ];
    }
} 