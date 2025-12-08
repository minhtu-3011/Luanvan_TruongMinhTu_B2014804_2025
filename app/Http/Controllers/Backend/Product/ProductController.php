<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface as AttributeCatalogueRepository;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Language;
use App\Classes\Nestedsetbie;
use App\Http\Requests\DeleteProductRequest;

class ProductController extends Controller
{

    protected $productService;
    protected $productRepository;
    protected $languageRepository;
    protected $attributeCatalogue;
    protected $nestedsetbie;
    protected $language;
    public function __construct(
        ProductService $productService,
        ProductRepository $productRepository,
        AttributeCatalogueRepository $attributeCatalogue,

    ) {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // 
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->attributeCatalogue = $attributeCatalogue;

        // $this->language = $this->currentLanguage();

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
        $this->authorize('modules', 'product.index');

        $products = $this->productService->paginate($request, $this->language);
        // dd($products);
        $config = $this->config();
        $config["seo"] = config('apps.product');
        $template = 'backend.product.product.index';
        $dropdown = $this->nestedsetbie->Dropdown();
        // $language = $this->languageRepository->all();
        // dd($language);

        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'products'));
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
            'model' => 'Product'
        ];
    }


    public function create()
    {
        $this->authorize('modules', 'product.create');
        $attributeCatalogue = $this->attributeCatalogue->getAll($this->language);
        $config = $this->configData();
        $config["seo"] = config('apps.product');
        $config["method"] = 'create';
        $dropdown = $this->nestedsetbie->Dropdown();
        $template = 'backend.product.product.store';
        return view('backend.dashboard.layout', compact('template', 'dropdown', 'config', 'attributeCatalogue'));
    }


    public function store(StoreProductRequest $request)
    {
        if ($this->productService->create($request, $this->language)) {
            return redirect()->route('product.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('product.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'product.update');

        $product = $this->productRepository->getProductById($id, $this->language);
        $attributeCatalogue = $this->attributeCatalogue->getAll($this->language);
        // dd($product);

        $config = $config = $this->configData();
        $config["seo"] = config('apps.product');
        $config["method"] = 'edit';
        $template = 'backend.product.product.store';
        $dropdown = $this->nestedsetbie->Dropdown();
        $album = json_decode($product->album);
        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'product', 'album', 'attributeCatalogue'));
    }

    public function update($id, UpdateProductRequest $request)
    {
        if ($this->productService->update($id, $request, $this->language)) {
            return redirect()->route('product.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('product.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'product.destroy');

        $template = 'backend.product.product.delete';
        $config["seo"] = config('apps.product');
        $product = $this->productRepository->getProductById($id, $this->language);
        // dd($product);
        return view('backend.dashboard.layout', compact('template', 'product', 'config'));
    }

    public function destroy($id)
    {
        // echo 123;
        // die();
        if ($this->productService->destroy($id, $this->language)) {
            return redirect()->route('product.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('product.index')->with('error', 'Xoá ban ghi khong thanh cong');
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
                '/backend/library/variant.js',


            ],
            'css' => [
                '/backend/css/plugins/switchery/switchery.css'
            ],
        ];
    }
}
