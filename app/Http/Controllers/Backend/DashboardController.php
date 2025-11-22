<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\OrderServiceInterface as OrderService;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Services\Interfaces\CustomerServiceInterface as CustomerService;

class DashboardController extends Controller
{


    protected $orderService;
    protected $customerService;
    protected $orderRepository;

    public function __construct(
        OrderService $orderService,
        CustomerService $customerService,
        OrderRepository $orderRepository,
    ) {
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
        $this->customerService = $customerService;
    }

    public function index()
    {
        $orderStatistic = $this->orderService->statistic();
        $customerStatistic = $this->customerService->statistic();
        $startDate = convertDateTime(now(), 'Y-m-d 00:00:00');
        $endDate = convertDateTime(now(), 'Y-m-d 23:59:59');
        $newOrders = $this->orderRepository->newOrder($startDate, $endDate);
        $config = $this->config();
        $template = 'backend.dashboard.home.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'orderStatistic',
            'customerStatistic',
            'newOrders'
        ));
    }



    private function config()
    {
        return [
            'js' => [
                '/backend/js/plugins/flot/jquery.flot.js',
                '/backend/js/plugins/flot/jquery.flot.tooltip.min.js',
                '/backend/js/plugins/flot/jquery.flot.spline.js',
                '/backend/js/plugins/flot/jquery.flot.resize.js',
                '/backend/js/plugins/flot/jquery.flot.pie.js',
                '/backend/js/plugins/flot/jquery.flot.symbol.js',
                '/backend/js/plugins/flot/jquery.flot.time.js',
                '/backend/js/plugins/peity/jquery.peity.min.js',
                '/backend/js/demo/peity-demo.js',
                '/backend/js/inspinia.js',
                '/backend/js/plugins/pace/pace.min.js',
                '/backend/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js',
                '/backend/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
                '/backend/js/plugins/easypiechart/jquery.easypiechart.js',
                '/backend/js/plugins/sparkline/jquery.sparkline.min.js',
                '/backend/js/demo/sparkline-demo.js',
                '/backend/js/plugins/jquery-ui/jquery-ui.min.js',
                'backend/js/plugins/chartJs/Chart.min.js',
                'backend/library/dashboard.js',
            ]
        ];
    }
}
