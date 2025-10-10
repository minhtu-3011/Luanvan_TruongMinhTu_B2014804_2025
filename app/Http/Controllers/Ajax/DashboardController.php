<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Str;




class DashboardController extends Controller
{

    protected $language;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function changeStatus(Request $request)
    {
        $post = $request->input();
        $serviceInterfaceNamespace = '\App\Services\\' . ucfirst($post['model']) . 'Service';

        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }

        $flag = $serviceInstance->updateStatus($post);
        return response()->json(['flag' => $flag]);
    }

    public function changeStatusAll(Request $request)
    {
        $post = $request->input();
        $serviceInterfaceNamespace = '\App\Services\\' . ucfirst($post['model']) . 'Service';

        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }

        $flag = $serviceInstance->updateStatusAll($post);

        return response()->json(['flag' => $flag]);
    }

    public function getMenu(Request $request)
    {
        $model = $request->input('model');
        $page = ($request->input('page')) ?? 1;
        $keyword = ($request->string('keyword')) ?? null;
        $serviceInterfaceNamespace = '\App\Repositories\\' . ucfirst($model) . 'Repository';
        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }

        $agruments = $this->paginationArgument($model, $keyword);
        $object = $serviceInstance->pagination(...array_values($agruments));

        return response()->json($object);
    }

    private function paginationArgument(string $model = '', string $keyword = ''): array
    {
        $model = Str::snake($model);
        $join = [
            [$model . '_language as tb2', 'tb2.' . $model . '_id', '=', $model . 's.id'],
        ];

        if (strpos($model, '_catalogue') === false) {
            $join[] = ['' . $model . '_catalogue_' . $model . ' as tb3', '' . $model . 's.id', '=', 'tb3.' . $model . '_id'];
        }

        $condition = [
            'where' => [
                ['tb2.language_id', '=', $this->language],
            ]
        ];

        if (!is_null($keyword)) {
            $condition['keyword'] = addslashes($keyword);
        }


        return [
            'column' => ['id', 'name', 'canonical'],
            'condition' => $condition,
            'perpage' => 20,
            'extend' => [
                'path' => $model . '.index',
                'groupBy' => ['id', 'name', 'canonical']
            ],
            'orderBy' => [$model . 's.id', 'DESC'],
            'join' => $join,
            'relations' => [],
        ];
    }
}
