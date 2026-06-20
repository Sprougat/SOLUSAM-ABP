<?php

namespace App\Services;

use App\Models\Transaksi;

class RevenuePredictionService
{
    protected $transaksiModel;
    protected $model = [];

    public function __construct()
    {
        $this->transaksiModel = new Transaksi();
    }

    /**
     * Get historical monthly revenue data.
     * 
     * @param int $clientId
     * @param int $months Number of months to retrieve
     * @return array
     */
    public function getHistoricalRevenue(int $clientId, int $months = 12): array
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                DATE_FORMAT(t.tanggal, '%Y-%m') AS month,
                SUM(t.jumlah * s.harga_jual)    AS total_revenue
            FROM transaksi t
            JOIN data_sampah s ON s.id = t.sampah_id
            WHERE
                t.client_id = ?
                AND t.jenis = 'out'
                AND (t.payment_status IS NULL OR t.payment_status = 'paid')
            GROUP BY DATE_FORMAT(t.tanggal, '%Y-%m')
            ORDER BY DATE_FORMAT(t.tanggal, '%Y-%m') ASC
        ", [$clientId]);
        
        $results = $query->getResultArray();
        
        if (empty($results)) {
            return [];
        }

        if (count($results) > $months) {
            $results = array_slice($results, -$months);
        }
        
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'month'   => $row['month'],
                'revenue' => (float) ($row['total_revenue'] ?? 0)
            ];
        }
        
        return $data;
    }

    /**
     * Train linear regression model on historical data
     * 
     * @param array $data Historical monthly data
     * @return array Model parameters [m, b]
     */
    public function train(array $data): array
    {
        if (count($data) < 2) {
            $this->model = ['m' => 0, 'b' => 0];
            return ['m' => 0, 'b' => 0, 'error' => 'Insufficient data for training'];
        }

        $n = count($data);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        $dataset = [];
        foreach ($data as $index => $item) {
            $x = $index + 1;
            $y = $item['revenue'];

            $dataset[] = ['x' => $x, 'y' => $y];
            $sumX += $x;
            $sumY += $y;
            $sumXY += ($x * $y);
            $sumX2 += ($x * $x);
        }

        $denominator = ($n * $sumX2) - ($sumX * $sumX);
        
        if ($denominator == 0) {
            $this->model = ['m' => 0, 'b' => 0];
            return ['m' => 0, 'b' => 0, 'error' => 'Cannot calculate model'];
        }

        $m = (($n * $sumXY) - ($sumX * $sumY)) / $denominator;

        $b = ($sumY - ($m * $sumX)) / $n;

        $this->model = [
            'm' => $m,
            'b' => $b,
            'dataset' => $dataset,
            'n' => $n
        ];

        return $this->model;
    }

    /**
     * Predict revenue for a specific month index
     * 
     * @param int $monthIndex
     * @return float Predicted revenue
     */
    public function predict(int $monthIndex): float
    {
        if (empty($this->model) || !isset($this->model['m']) || !isset($this->model['b'])) {
            return 0;
        }

        $m = $this->model['m'];
        $b = $this->model['b'];

        $prediction = ($m * $monthIndex) + $b;

        return max(0, $prediction);
    }

    /**
     * Predict next month revenue
     * 
     * @param int $clientId
     * @return array Prediction data
     */
    public function predictNextMonth(int $clientId): array
    {
        $historical = $this->getHistoricalRevenue($clientId, 12);

        if (empty($historical)) {
            return [
                'current_month_revenue' => 0,
                'next_month_prediction' => 0,
                'confidence' => 'insufficient_data',
                'message' => 'Tidak ada data transaksi'
            ];
        }

        $this->train($historical);

        $nextMonthIndex = count($historical) + 1;
        $prediction = $this->predict($nextMonthIndex);

        $dataPoints = count($historical);
        $confidence = $this->calculateConfidence($dataPoints);

        return [
            'current_month_revenue' => (int) $historical[count($historical) - 1]['revenue'],
            'next_month_prediction' => (int) $prediction,
            'data_points' => $dataPoints,
            'confidence' => $confidence,
            'model' => [
                'm' => round($this->model['m'], 2),
                'b' => round($this->model['b'], 2)
            ]
        ];
    }

    /**
     * Predict next three months revenue
     * 
     * @param int $clientId
     * @return array Prediction data for next 3 months
     */
    public function predictNextThreeMonths(int $clientId): array
    {
        $historical = $this->getHistoricalRevenue($clientId, 12);

        if (empty($historical)) {
            return [
                'historical' => [],
                'predictions' => [],
                'confidence' => 'insufficient_data',
                'message' => 'Tidak ada data transaksi'
            ];
        }

        $this->train($historical);

        $predictions = [];
        $baseIndex = count($historical);

        for ($i = 1; $i <= 3; $i++) {
            $monthIndex = $baseIndex + $i;
            $prediction = $this->predict($monthIndex);

            $nextDate = new \DateTime('now');
            $nextDate->add(new \DateInterval('P' . $i . 'M'));

            $predictions[] = [
                'month' => $nextDate->format('Y-m'),
                'revenue' => (int) $prediction,
                'month_number' => $i
            ];
        }

        $dataPoints = count($historical);
        $confidence = $this->calculateConfidence($dataPoints);

        return [
            'historical' => $historical,
            'predictions' => $predictions,
            'data_points' => $dataPoints,
            'confidence' => $confidence,
            'model' => [
                'm' => round($this->model['m'], 2),
                'b' => round($this->model['b'], 2)
            ]
        ];
    }

    /**
     * Calculate model accuracy using MAE (Mean Absolute Error)
     * Uses 80-20 split
     * 
     * @param int $clientId
     * @return array Accuracy metrics
     */
    public function evaluateModel(int $clientId): array
    {
        $historical = $this->getHistoricalRevenue($clientId, 12);

        if (count($historical) < 3) {
            return [
                'mae' => 0,
                'message' => 'Insufficient data for evaluation'
            ];
        }

        $splitPoint = (int) (count($historical) * 0.8);
        $trainingData = array_slice($historical, 0, $splitPoint);
        $testData = array_slice($historical, $splitPoint);

        $this->train($trainingData);

        $totalError = 0;
        $testCount = 0;

        foreach ($testData as $index => $item) {
            $monthIndex = $splitPoint + $index + 1;
            $prediction = $this->predict($monthIndex);
            $actual = $item['revenue'];
            $error = abs($actual - $prediction);

            $totalError += $error;
            $testCount++;
        }

        $mae = $testCount > 0 ? $totalError / $testCount : 0;

        return [
            'mae' => (int) $mae,
            'training_data_points' => count($trainingData),
            'test_data_points' => count($testData),
            'accuracy_level' => $this->getAccuracyLevel($mae, $historical)
        ];
    }

    /**
     * Calculate confidence level based on data points and trend consistency
     * 
     * @param int $dataPoints
     * @return string
     */
    protected function calculateConfidence(int $dataPoints): string
    {
        if ($dataPoints < 3) {
            return 'low';
        } elseif ($dataPoints < 6) {
            return 'medium';
        } elseif ($dataPoints < 12) {
            return 'high';
        } else {
            return 'very_high';
        }
    }

    /**
     * Get accuracy level based on MAE
     * 
     * @param float $mae
     * @param array $historical
     * @return string
     */
    protected function getAccuracyLevel(float $mae, array $historical): string
    {
        $avgRevenue = array_reduce($historical, function ($carry, $item) {
            return $carry + $item['revenue'];
        }, 0) / count($historical);

        if ($avgRevenue == 0) {
            return 'unknown';
        }

        $mape = ($mae / $avgRevenue) * 100;

        if ($mape < 5) {
            return 'excellent';
        } elseif ($mape < 15) {
            return 'good';
        } elseif ($mape < 30) {
            return 'acceptable';
        } else {
            return 'poor';
        }
    }
}
