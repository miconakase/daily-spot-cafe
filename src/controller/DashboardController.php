<?php
require_once __DIR__ . '/../model/DashboardStats.php';

class DashboardController
{
    // Range key => [label, number of days including today]
    const RANGES = [
        'today' => ['Today', 1],
        '7d'    => ['Last 7 days', 7],
        '30d'   => ['Last 30 days', 30],
    ];

    private $stats;

    public function __construct()
    {
        $this->stats = new DashboardStats();
    }

    public function getDashboard($rangeKey)
    {
        if (!isset(self::RANGES[$rangeKey])) {
            $rangeKey = '7d';
        }
        [$label, $days] = self::RANGES[$rangeKey];

        $today    = new DateTimeImmutable($this->stats->today());
        $tomorrow = $today->modify('+1 day');
        $from     = $today->modify('-' . ($days - 1) . ' days');
        $prevFrom = $from->modify("-$days days");

        $current  = $this->stats->salesBetween($from->format('Y-m-d'), $tomorrow->format('Y-m-d'));
        $previous = $this->stats->salesBetween($prevFrom->format('Y-m-d'), $from->format('Y-m-d'));

        // The chart always shows at least a week so "Today" still has context
        $chartDays = max($days, 7);
        $chartFrom = $today->modify('-' . ($chartDays - 1) . ' days');
        $daily     = $this->stats->dailySales($chartFrom->format('Y-m-d'), $tomorrow->format('Y-m-d'));

        $series = array();
        for ($d = $chartFrom; $d < $tomorrow; $d = $d->modify('+1 day')) {
            $key = $d->format('Y-m-d');
            $series[] = array(
                'date'   => $key,
                'label'  => $d->format('M j'),
                'sales'  => $daily[$key]['sales'] ?? 0.0,
                'orders' => $daily[$key]['orders'] ?? 0,
            );
        }

        return array(
            'range'        => $rangeKey,
            'range_label'  => $label,
            'sales'        => $current['sales'],
            'orders'       => $current['orders'],
            'sales_delta'  => $this->percentChange($current['sales'], $previous['sales']),
            'orders_delta' => $this->percentChange($current['orders'], $previous['orders']),
            'unfulfilled'  => $this->stats->unfulfilledCount(),
            'daily'        => $series,
            'totals'       => $this->stats->allTimeTotals(),
            'top_products' => $this->stats->topProducts($from->format('Y-m-d'), $tomorrow->format('Y-m-d')),
            'latest'       => $this->stats->latestOrders(5),
        );
    }

    /** Percent change vs the previous period, or null when there is nothing to compare. */
    private function percentChange($current, $previous)
    {
        if ($previous == 0) {
            return null;
        }
        return round(($current - $previous) / $previous * 100);
    }
}
?>
