<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\PostCatalogueServiceInterface as PostCatalogueService;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
use App\Http\Requests\StorePostCatalogueRequest;
use App\Http\Requests\UpdatePostCatalogueRequest;

use App\Classes\Nestedsetbie;

class PostCatalogueController extends Controller
{

    protected $postCatalogueService;
    protected $postCatalogueRepository;
    protected $nestedsetbie;
    protected $language;
    public function __construct(
        PostCatalogueService $postCatalogueService,
        PostCatalogueRepository $postCatalogueRepository
    ) {
        $this->postCatalogueService = $postCatalogueService;
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->nestedsetbie = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => 5,
        ]);
        $this->language = $this->currentLanguage();
    }

    public function index(Request $request)
    {

        $postCatalogues = $this->postCatalogueService->paginate($request);
        // $postCatalogue:paginate(10);

        $config = $this->config();
        $config["seo"] = config('apps.postcatalogue');
        $template = 'backend.post.catalogue.index';
        return view('backend.dashboard.layout', compact('template', 'config', 'postCatalogues'));
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
        ];
    }


    public function create()
    {

        $config = $this->configData();
        $config["seo"] = config('apps.postcatalogue');
        $config["method"] = 'create';
        $dropdown = $this->nestedsetbie->Dropdown();
        $template = 'backend.post.catalogue.store';
        return view('backend.dashboard.layout', compact('template', 'dropdown', 'config',));
    }


    public function store(StorePostCatalogueRequest $request)
    {
        if ($this->postCatalogueService->create($request)) {
            return redirect()->route('post.catalogue.index')->with('success', 'Them moi ban ghi thanh cong');
        }
        return redirect()->route('post.catalogue.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($id, $this->language);
        // dd($postCatalogue);

        foreach ($postCatalogue->post_catalogue_language as $language) {
            echo $language->name . '<br>';
        }

        // die();
        $config = $config = $this->configData();
        $config["seo"] = config('apps.postcatalogue');
        $config["method"] = 'edit';
        $template = 'backend.post.catalogue.store';
        $dropdown = $this->nestedsetbie->Dropdown();

        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'postCatalogue'));
    }

    public function update($id, UpdatePostCatalogueRequest $request)
    {
        if ($this->postCatalogueService->update($id, $request)) {
            return redirect()->route('post.catalogue.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('post.catalogue.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $template = 'backend.post.catalogue.delete';
        $config["seo"] = config('apps.postcatalogue');
        $postCatalogue = $this->postCatalogueRepository->findById($id);
        return view('backend.dashboard.layout', compact('template', 'postCatalogue', 'config'));
    }

    public function destroy($id)
    {
        if ($this->postCatalogueService->destroy($id)) {
            return redirect()->route('post.catalogue.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('post.catalogue.index')->with('error', 'Xoá ban ghi khong thanh cong');
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
