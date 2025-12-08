<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\PostServiceInterface as PostService;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Language;


use App\Classes\Nestedsetbie;
use App\Http\Requests\DeletePostRequest;

class PostController extends Controller
{

    protected $postService;
    protected $postRepository;
    protected $languageRepository;

    protected $nestedsetbie;
    protected $language;
    public function __construct(
        PostService $postService,
        PostRepository $postRepository,

    ) {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // 
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->postService = $postService;
        $this->postRepository = $postRepository;

        // $this->language = $this->currentLanguage();

    }

    private function initialize()
    {
        $this->nestedsetbie = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => $this->language,
        ]);
    }


    public function index(Request $request)
    {
        $this->authorize('modules', 'post.index');

        $posts = $this->postService->paginate($request, $this->language);
        // $post:paginate(10);

        $config = $this->config();
        $config["seo"] = config('apps.post');
        $template = 'backend.post.post.index';
        $dropdown = $this->nestedsetbie->Dropdown();
        // $language = $this->languageRepository->all();
        // dd($language);

        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'posts'));
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
            'model' => 'Post'
        ];
    }


    public function create()
    {
        $this->authorize('modules', 'post.create');

        $config = $this->configData();
        $config["seo"] = config('apps.post');
        $config["method"] = 'create';
        $dropdown = $this->nestedsetbie->Dropdown();
        $template = 'backend.post.post.store';
        return view('backend.dashboard.layout', compact('template', 'dropdown', 'config',));
    }


    public function store(StorePostRequest $request)
    {
        if ($this->postService->create($request, $this->language)) {
            return redirect()->route('post.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('post.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'post.update');

        $post = $this->postRepository->getPostById($id, $this->language);



        $config = $config = $this->configData();
        $config["seo"] = config('apps.post');
        $config["method"] = 'edit';
        $template = 'backend.post.post.store';
        $dropdown = $this->nestedsetbie->Dropdown();
        $album = json_decode($post->album);
        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'post', 'album'));
    }

    public function update($id, UpdatePostRequest $request)
    {
        if ($this->postService->update($id, $request, $this->language)) {
            return redirect()->route('post.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('post.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'post.destroy');

        $template = 'backend.post.post.delete';
        $config["seo"] = config('apps.post');
        $post = $this->postRepository->getPostById($id, $this->language);
        // dd($post);
        return view('backend.dashboard.layout', compact('template', 'post', 'config'));
    }

    public function destroy($id)
    {
        // echo 123;
        // die();
        if ($this->postService->destroy($id, $this->language)) {
            return redirect()->route('post.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('post.index')->with('error', 'Xoá ban ghi khong thanh cong');
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
