<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\Process\Process;

class LstmPredictionService
{
    /**
     * Memprediksi total kejadian pada tahun setelah tahun data terakhir.
     * Data tahunan diubah menjadi deret bulanan agar LSTM tidak hanya menerima 5 titik.
     *
     * @param array<int, int|float> $monthlySeries key: YYYYMM, value: jumlah kejadian
     * @return array<string, mixed>
     */
    public function forecast(array $monthlySeries): array
    {
        if ($monthlySeries === []) {
            return $this->unavailable('Data historis tidak tersedia.');
        }

        ksort($monthlySeries);
        $cacheKey = 'lstm_forecast:v3:' . sha1(json_encode([
            'series' => $monthlySeries,
            'look_back' => config('lstm.look_back'),
            'epochs' => config('lstm.epochs'),
            'seed' => config('lstm.seed'),
        ]));

        return Cache::remember($cacheKey, config('lstm.cache_seconds'), function () use ($monthlySeries) {
            try {
                return $this->runPython($monthlySeries);
            } catch (\Throwable $e) {
                Log::error('Prediksi LSTM gagal', [
                    'message' => $e->getMessage(),
                ]);

                return $this->unavailable(
                    app()->environment('local')
                        ? 'Prediksi LSTM gagal: ' . $e->getMessage()
                        : 'Prediksi LSTM sedang tidak tersedia.'
                );
            }
        });
    }

    private function runPython(array $monthlySeries): array
    {
        $pythonBinary = config('lstm.python_binary');
        $scriptPath = config('lstm.script_path');

        if (!is_string($pythonBinary) || trim($pythonBinary) === '') {
            throw new RuntimeException('LSTM_PYTHON_BINARY belum dikonfigurasi.');
        }

        if (!is_string($scriptPath) || !file_exists($scriptPath)) {
            throw new RuntimeException('File lstm_forecast.py tidak ditemukan: ' . $scriptPath);
        }

        $payload = [
            'series' => array_map(
                fn ($period, $value) => [
                    'period' => (string) $period,
                    'value' => (float) $value
                ],
                array_keys($monthlySeries),
                array_values($monthlySeries)
            ),
            'look_back' => config('lstm.look_back'),
            'epochs' => config('lstm.epochs'),
            'seed' => config('lstm.seed'),
        ];

        $process = new Process([
            $pythonBinary,
            $scriptPath,
        ]);
        $process->setInput(json_encode($payload, JSON_THROW_ON_ERROR));
        $process->setTimeout(config('lstm.timeout'));
        $process->run();

        if (! $process->isSuccessful()) {
            $stdout = trim($process->getOutput());
            $stderr = trim($process->getErrorOutput());

            if ($stdout !== '') {
                $errorResult = json_decode($stdout, true);

                if (
                    is_array($errorResult)
                    && isset($errorResult['message'])
                ) {
                    throw new RuntimeException($errorResult['message']);
                }
            }

            throw new RuntimeException(
                $stderr !== ''
                    ? $stderr
                    : 'Proses Python berhenti dengan kode gagal.'
            );
        }

        $result = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR);
        if (! isset($result['status']) || $result['status'] !== 'ok') {
            throw new RuntimeException($result['message'] ?? 'Respons prediksi tidak valid.');
        }

        return $result;
    }

    private function unavailable(string $message): array
    {
        return [
            'status' => 'unavailable',
            'message' => $message,
            'method' => 'LSTM',
            'forecast_year' => null,
            'forecast_total' => null,
            'monthly_forecast' => [],
            'evaluation_periods' => [],
            'evaluation_actual' => [],
            'evaluation_predictions' => [],
            'rmse' => null,
        ];
    }
}
