<?php
namespace GTA\Controllers;

use GTA\Models\StatsModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;

class StatsController
{
    public function index(): void
    {
        $auth  = AuthMiddleware::require('employee');
        $stats = new StatsModel();

        $data = [
            'pending_orders'       => $stats->pendingOrdersCount(),
            'pending_appointments' => $stats->pendingAppointmentsCount(),
            'my_sales_count'       => $stats->myCompletedSalesCount((int)$auth['sub']),
        ];

        if ($auth['role'] === 'admin') {
            $data['total_cars']       = $stats->totalCars();
            $data['cars_sold']        = $stats->carsSoldCount();
            $data['total_deal_value'] = $stats->totalDealValue();
            $data['total_clients']    = $stats->usersByRoleCount('client');
            $data['total_staff']      = $stats->staffCount();
        }

        ResponseHelper::json($data);
    }

    public function topSalesmen(): void
    {
        AuthMiddleware::require('admin');
        ResponseHelper::json((new StatsModel())->topSalesmenThisMonth());
    }

    public function topCars(): void
    {
        AuthMiddleware::require('admin');
        ResponseHelper::json((new StatsModel())->topCarsThisMonth());
    }
}
