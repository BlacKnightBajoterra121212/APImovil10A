<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const LOW_STOCK_THRESHOLD = 10;
    private const ALERT_LIMIT = 6;
    private const WEEKLY_ORDER_STATUSES = ['pending', 'completed'];

    private const ORDER_STATUS_LABELS = [
        'pending' => 'Pendientes',
        'completed' => 'Entregados',
        'cancelled' => 'Cancelados',
    ];

    public function index(): JsonResponse
    {
        $today = now()->toDateString();
        $weeklyStart = now()->copy()->subDays(6)->startOfDay();
        $weeklyEnd = now()->copy()->endOfDay();

        $orderStatusCounts = Order::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->whereIn('status', array_keys(self::ORDER_STATUS_LABELS))
            ->groupBy('status')
            ->pluck('total', 'status');

        $weeklySales = Order::query()
            ->selectRaw('DATE(order_date) as order_day, COALESCE(SUM(total), 0) as total, COUNT(*) as orders_count')
            ->whereBetween('order_date', [$weeklyStart, $weeklyEnd])
            ->whereIn('status', self::WEEKLY_ORDER_STATUSES)
            ->groupBy('order_day')
            ->orderBy('order_day')
            ->get()
            ->keyBy('order_day');

        $lowStockItems = BranchInventory::query()
            ->with([
                'branch:id,name',
                'product:id,id_category,name',
                'product.category:id,name',
            ])
            ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->orderBy('stock')
            ->orderBy('id')
            ->limit(self::ALERT_LIMIT)
            ->get();

        return $this->successResponse('Resumen del dashboard obtenido correctamente.', [
            'summary' => [
                'pedidos_hoy' => Order::query()->whereDate('order_date', $today)->count(),
                'ventas_hoy' => (float) Order::query()
                    ->whereDate('order_date', $today)
                    ->where('status', 'completed')
                    ->sum('total'),
                'stock_bajo' => BranchInventory::query()
                    ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
                    ->count(),
                'personal_activo' => User::query()
                    ->where('status', 'active')
                    ->count(),
                'sucursales' => Branch::query()->count(),
                'low_stock_threshold' => self::LOW_STOCK_THRESHOLD,
            ],
            'pedidos_estado' => collect(self::ORDER_STATUS_LABELS)->map(
                fn (string $label, string $key) => [
                    'key' => $key,
                    'label' => $label,
                    'count' => (int) ($orderStatusCounts[$key] ?? 0),
                ]
            )->values(),
            'ventas_semanales' => collect(range(0, 6))->map(function (int $offset) use ($weeklyStart, $weeklySales) {
                $day = $weeklyStart->copy()->addDays($offset);
                $dayKey = $day->toDateString();
                $dayData = $weeklySales->get($dayKey);

                return [
                    'date' => $dayKey,
                    'label' => $this->weekLabel($day),
                    'total' => $dayData ? (float) $dayData->total : 0.0,
                    'orders_count' => $dayData ? (int) $dayData->orders_count : 0,
                ];
            })->values(),
            'alertas_inventario' => [
                'total' => BranchInventory::query()
                    ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
                    ->count(),
                'threshold' => self::LOW_STOCK_THRESHOLD,
                'items' => $lowStockItems->map(fn (BranchInventory $item) => [
                    'product_name' => $item->product?->name,
                    'stock' => (int) $item->stock,
                    'branch_name' => $item->branch?->name,
                    'category_name' => $item->product?->category?->name,
                ])->values(),
            ],
        ]);
    }

    private function weekLabel(Carbon $day): string
    {
        return match ($day->dayOfWeekIso) {
            1 => 'Lun',
            2 => 'Mar',
            3 => 'Mié',
            4 => 'Jue',
            5 => 'Vie',
            6 => 'Sáb',
            default => 'Dom',
        };
    }

    private function successResponse(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => (object) [],
        ], $status);
    }
}
