<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Http\Requests\StoreProductCatalogueRequest;
use App\Http\Requests\UpdateProductCatalogueRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\DeteleProductCatalogueRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\Language;

use App\Classes\Nestedsetbie;
use App\Http\Requests\DeleteProductCatalogueRequest;

class ProductCatalogueController extends Controller
{

    protected $productCatalogueService;
    protected $productCatalogueRepository;
    protected $nestedsetbie;
    protected $language;
    public function __construct(
        ProductCatalogueService $productCatalogueService,
        ProductCatalogueRepository $productCatalogueRepository
    ) {
        $this->productCatalogueService = $productCatalogueService;
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // 
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
    }
    private function initialize()
    {
        $this->nestedsetbie = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' => $this->language,
        ]);
    }

    public function index(Request $request)
    {
        // dd(session('app_locale'));
        $this->authorize('modules', 'product.catalogue.index');


        $productCatalogues = $this->productCatalogueService->paginate($request, $this->language);
        // $productCatalogue:paginate(10);
        $config = $this->config();
        $config["seo"] = __('messages.productCatalogue');
        $template = 'backend.product.catalogue.index';
        return view('backend.dashboard.layout', compact('template', 'config', 'productCatalogues'));
    }

    private function config()
    {
        return [
            'js' => [
                '/backend/js/plugins/switchery/switchery.js'
            ],
            'css' => [
                '/backend/css/plugins/switchery/switchery.css'
            ],
            'model' => 'ProductCatalogue'
        ];
    }


    public function create()
    {
        $this->authorize('modules', 'product.catalogue.create');

        $config = $this->configData();
        $config["seo"] = __('messages.productCatalogue');
        $config["method"] = 'create';
        $dropdown = $this->nestedsetbie->Dropdown();
        $template = 'backend.product.catalogue.store';
        return view('backend.dashboard.layout', compact('template', 'dropdown', 'config',));
    }


    public function store(StoreProductCatalogueRequest $request)
    {
        if ($this->productCatalogueService->create($request, $this->language)) {
            return redirect()->route('product.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('product.catalogue.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'product.catalogue.update');

        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
        // dd($productCatalogue);


        $config = $config = $this->configData();
        $config["seo"] = __('messages.productCatalogue');
        $config["method"] = 'edit';
        $template = 'backend.product.catalogue.store';
        $dropdown = $this->nestedsetbie->Dropdown();
        $album = json_decode($productCatalogue->album);

        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'productCatalogue', 'album'));
    }

    public function update($id, UpdateProductCatalogueRequest $request)
    {
        if ($this->productCatalogueService->update($id, $request, $this->language)) {
            return redirect()->route('product.catalogue.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('product.catalogue.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'product.catalogue.destroy');

        $template = 'backend.product.catalogue.delete';
        $config["seo"] = __('messages.productCatalogue');
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
        // dd($productCatalogue);
        return view('backend.dashboard.layout', compact('template', 'productCatalogue', 'config'));
    }

    public function destroy($id, DeleteProductCatalogueRequest $request)
    {
        // echo 123;
        // die();
        if ($this->productCatalogueService->destroy($id, $this->language)) {
            return redirect()->route('product.catalogue.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('product.catalogue.index')->with('error', 'Xoá ban ghi khong thanh cong');
    }

    private function configData()
    {
        return [
            'js' => [
                '/backend/plugin/ckeditor/ckeditor.js',
                '/backend/plugin/ckfinder/ckfinder.js',
                '/backend/library/finder.js', // nếu file này chỉ cấu hình thêm thì cho sau cùng
                '/backend/js/plugins/switchery/switchery.js',
                '/backend/library/seo.js',


            ],
            'css' => [
                '/backend/css/plugins/switchery/switchery.css'
            ],
        ];
    }
}
