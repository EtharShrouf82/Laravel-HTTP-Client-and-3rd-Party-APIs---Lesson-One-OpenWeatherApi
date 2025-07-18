

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حالة الطقس</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .weather-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .weather-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .temperature {
            font-size: 4rem;
            font-weight: 700;
            margin: 0;
        }

        .city-name {
            font-size: 1.5rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .weather-description {
            font-size: 1.1rem;
            opacity: 0.8;
        }

        .weather-stats {
            padding: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            display: block;
        }

        .humidity-icon { color: #00b894; }
        .pressure-icon { color: #e17055; }
        .wind-icon { color: #74b9ff; }

        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #636e72;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .refresh-btn {
            background: linear-gradient(135deg, #00b894 0%, #00a085 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 184, 148, 0.3);
            color: white;
        }

        .loading {
            display: none;
        }

        .weather-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .temperature {
                font-size: 3rem;
            }
            .stat-card {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="weather-card">
                @if(isset($weatherData['error']) && $weatherData['error'] === true)
                    {{-- حالة الخطأ --}}
                    <div class="bg-white rounded shadow p-4 text-center">
                        <div class="display-3 text-danger mb-3">⚠️</div>
                        <h2 class="h3 text-danger fw-bold mb-3">Weather Service Error</h2>
                        <p class="lead text-muted mb-4">Unable to fetch weather data</p>

                        <div class="alert alert-danger text-start" role="alert">
                            <strong>{{ $weatherData['message'] ?? 'Unknown error occurred' }}</strong>
                        </div>
                    </div>
                @else
                    {{-- حالة النجاح --}}
                    <div class="weather-header">
                        <div class="weather-icon">
                            @if(isset($weatherData['icon']))
                                <img src="https://openweathermap.org/img/wn/{{ $weatherData['icon'] }}@4x.png"
                                     alt="{{ $weatherData['description'] ?? 'Weather' }}"
                                     class="weather-icon">
                            @endif
                        </div>

                        <h1 class="city-name" id="cityName">{{ $weatherData['city'] }} - {{ $weatherData['country'] }}</h1>
                        <p class="temperature" id="temperature">{{ round($weatherData['temperature'] ?? 0) }}°</p>
                        <p class="weather-description" id="weatherDescription">{{ $weatherData['description'] ?? 'غير متوفر' }}</p>
                    </div>

                    <div class="weather-stats">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <i class="fas fa-tint stat-icon humidity-icon"></i>
                                    <div class="stat-value">{{ $weatherData['humidity'] ?? '--' }}%</div>
                                    <div class="stat-label">الرطوبة</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="stat-card">
                                    <i class="fas fa-thermometer-half stat-icon pressure-icon"></i>
                                    <div class="stat-value">{{ $weatherData['pressure'] ?? '--' }}</div>
                                    <div class="stat-label">الضغط الجوي (hPa)</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="stat-card">
                                    <i class="fas fa-wind stat-icon wind-icon"></i>
                                    <div class="stat-value">{{ $weatherData['wind_speed'] ?? '--' }}</div>
                                    <div class="stat-label">سرعة الرياح (km/h)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

</body>
</html>
