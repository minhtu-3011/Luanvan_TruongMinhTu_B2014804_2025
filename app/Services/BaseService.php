<?php

namespace App\Services;

use App\Services\Interfaces\BaseServiceInterface;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;

/**
 * Class UserCatalogueService
 * @package App\Services
 */
class BaseService implements BaseServiceInterface
{
    protected $nestedsetbie;
    protected $routereRepository;
    protected $controllerName;


    public function __construct(
        RouterRepository $routereRepository

    ) {

        $this->nestedsetbie = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => $this->currentLanguage(),
        ]);
        $this->routereRepository = $routereRepository;
    }

    public function currentLanguage()
    {
        return 5;
    }

    public function formatAlbum($request)
    {
        return ($request->input('album') && !empty($request->input('album')))
            ? json_encode($request->input('album'))
            : '';
    }

    public function nestedset()
    {
        $this->nestedsetbie->Get('level ASC, order ASC');
        $this->nestedsetbie->Recursive(0, $this->nestedsetbie->Set());
        $this->nestedsetbie->Action();
    }

    public function formatRouterPayload($model, $request, $controllerName)
    {
        $router = [
            'canonical' => $request->input('canonical'),
            'module_id' => $model->id,
            'controllers' => 'App\Http\Controllers\Frontend\\' . $controllerName . '',
        ];

        return $router;
    }

    public function createRouter($model, $request, $controllerName)
    {
        $router = $this->formatRouterPayload($model, $request, $controllerName);
        $this->routereRepository->create($router);
    }

    public function updateRouter($model, $request, $controllerName)
    {
        $payload = $this->formatRouterPayload($model, $request, $controllerName);
        $condition = [
            ['module_id', '=', $model->id],
            ['controllers', '=', 'App\Http\Controllers\Frontend\\' . $controllerName . ''],
        ];
        $router = $this->routereRepository->findByCondition($condition);
        $res = $this->routereRepository->update($router->id, $payload);
        return $res;
    }
}
