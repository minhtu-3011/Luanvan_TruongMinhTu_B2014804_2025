<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\SystemRepositoryInterface  as SystemRepository;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\Interfaces\CustomerServiceInterface  as CustomerService;
use App\Repositories\Interfaces\CustomerCatalogueRepositoryInterface as CustomerCatalogueRepository;
use App\Repositories\Interfaces\SourceRepositoryInterface as SourceRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface as CustomerRepository;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Services\Interfaces\OrderServiceInterface  as OrderService;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Models\Province;






class CustomerController extends FrontendController
{
    protected $systemRepository;
    protected $provinceRepository;
    protected $customerService;
    protected $customerCatalogueRepository;
    protected $sourceRepository;
    protected $customerRepository;
    protected $system;
    protected $orderService;
    protected $orderRepository;



    public function __construct(
        SystemRepository $systemRepository,
        ProvinceRepository $provinceRepository,
        CustomerService $customerService,
        CustomerCatalogueRepository $customerCatalogueRepository,
        SourceRepository $sourceRepository,
        CustomerRepository $customerRepository,
        OrderService $orderService,
        OrderRepository $orderRepository,


    ) {
        $this->systemRepository = $systemRepository;
        $this->provinceRepository = $provinceRepository;
        $this->customerService = $customerService;
        $this->customerCatalogueRepository = $customerCatalogueRepository;
        $this->sourceRepository = $sourceRepository;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;

        parent::__construct(
            $systemRepository,
        );
    }

    public function index($id)
    {


        $customer = $this->customerRepository->findById($id);
        $provinces = $this->provinceRepository->all();
        $customerCatalogues = $this->customerCatalogueRepository->all();
        $sources = $this->sourceRepository->all();
        $config = $this->config();
        $config['seo'] = __('messages.customer');
        $system = $this->system;
        $seo = [
            'meta_title' => $this->system['seo_meta_title'],
            'meta_keyword' => $this->system['seo_meta_keyword'],
            'meta_description' => $this->system['seo_meta_description'],
            'meta_image' => $this->system['seo_meta_images'],
            'canonical' => config('app.url'),
        ];
        return view('frontend.customer.dashboard', compact(

            'config',
            'provinces',
            'customer',
            'customerCatalogues',
            'sources',
            'seo',
            'system',
        ));


        // return view('frontend.customer.login', compact('config', 'provinces', 'system', 'seo'));
    }


    public function indexOrder(int $id)
    {

        // Controller
        $provinces = Province::all()->map(function ($item) {
            return [
                'id' => $item->code,
                'name' => $item->name,
            ];
        })->values()->toArray(); // Bắt buộc chuyển sang array
        $order = $this->orderRepository->getOrderById($id);

        if (!$order) {
            abort(404);
        }

        $order = $this->orderService->getOrderItemImage2($order);
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                '/backend/css/custom.css'
            ],
            'js' => [
                'backend/library/order.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ],
        ];
        $system = $this->system;
        $seo = [
            'meta_title' => $this->system['seo_meta_title'],
            'meta_keyword' => $this->system['seo_meta_keyword'],
            'meta_description' => $this->system['seo_meta_description'],
            'meta_image' => $this->system['seo_meta_images'],
            'canonical' => config('app.url'),
        ];
        return view('frontend.customer.order', compact(
            'order',
            'provinces',
            'config',
            'seo',
            'system',
        ));
    }

    public function orderlist(string $phone)
    {
        // Lấy tỉnh thành
        $provinces = Province::all()->map(function ($item) {
            return [
                'id' => $item->code,
                'name' => $item->name,
            ];
        })->values()->toArray();

        // Lấy danh sách đơn theo phone + paginate
        $orders = $this->orderRepository->findOrdersByPhone($phone, ['products']);

        // Xử lý image variant trong từng order
        $orders = $this->orderService->getOrderItemImage2($orders);
        $system = $this->system;
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                '/backend/css/custom.css'
            ],
            'js' => [
                'backend/library/order.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ],
        ];

        $seo = [
            'meta_title'        => $this->system['seo_meta_title'],
            'meta_keyword'      => $this->system['seo_meta_keyword'],
            'meta_description'  => $this->system['seo_meta_description'],
            'meta_image'        => $this->system['seo_meta_images'],
            'canonical'         => config('app.url'),
        ];

        return view('frontend.customer.orderlist', compact(
            'orders',
            'provinces',
            'config',
            'seo',
            'system'
        ));
    }






    private function config()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                // '/backend/css/custom.css',
                // '/backend/css/style.css',
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                '/backend/library/location.js',
                '/backend/plugin/ckfinder/ckfinder.js',
                '/backend/library/finder.js',

            ]
        ];
    }

    public function update(UpdateCustomerRequest $request)
    {
        $id = Auth::guard('customer')->id();
        // dd($id);
        if ($this->customerService->update($id, $request)) {
            return redirect()->route('customer.editfe', $id)->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('customer.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }
}
