<?php

return [
    'python_binary' => env('LSTM_PYTHON_BINARY', 'python'),
    'script_path' => base_path('scripts/lstm_forecast.py'),
    'look_back' => (int) env('LSTM_LOOK_BACK', 12),
    'epochs' => (int) env('LSTM_EPOCHS', 250),
    'timeout' => (int) env('LSTM_TIMEOUT', 120),
    'cache_seconds' => (int) env('LSTM_CACHE_SECONDS', 3600),
    'seed' => (int) env('LSTM_SEED', 42),
];
