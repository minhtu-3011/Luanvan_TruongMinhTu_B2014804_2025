<?php

namespace App\Services;

use App\Services\Interfaces\GenerateServiceInterface;
use App\Repositories\Interfaces\GenerateRepositoryInterface as GenerateRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

/**
 * Class UserCatalogueService
 * @package App\Services
 */
class GenerateService implements GenerateServiceInterface
{
    protected $generateRepository;
    public function __construct(
        GenerateRepository $generateRepository

    ) {
        $this->generateRepository = $generateRepository;
    }

    public function paginate($request)
    {



        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish', -1);
        $perpage = $request->integer('perpage', 10);

        $generate = $this->generateRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'language/index'],
        );
        return $generate;
    }

    public function create($request)
    {
        // DB::beginTransaction();
        try {

            $database = $this->makeDatabase($request);
            $controller = $this->makeController($request);
            $model = $this->makeModel($request);
            $repository = $this->makeRepository($request);
            $service = $this->makeService($request);
            $provider = $this->makeProvider($request);
            $makeRequest = $this->makeRequest($request);
            $makeView = $this->makeView($request);
            if ($request->input('module_type') == 'catalogue') {
                $rule = $this->makeRule($request);
            }
            $makeRoute = $this->makeRoute($request);





            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function makeDatabase($request)
    {


        try {
            $payload = $request->only('schema', 'name', 'module_type');
            $module = $this->convertModuleNameToTableName($payload['name']); // product
            $moduleExtract = explode('_', $module);
            $this->makeMainTable($request, $module, $payload);
            if ($payload['module_type'] !== 'difference') {
                $this->makeLanguagetable($request, $module);

                if (count($moduleExtract) == 1) {
                    $this->makeRelationTable($request, $module);
                }
            }

            Artisan::call('migrate');
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function makeRelationTable($request, $module)
    {
        $moduleExtract = explode('_', $module);
        $tableName = $module . '_catalogue_' . $moduleExtract[0];
        $schema = $this->relationSchema($tableName, $module);

        $migrationRelationFile = $this->createMigrationFile($schema, $tableName);
        $migrationRelationFileName = date('Y_m_d_His', time() + 10) . '_create_' . $tableName . '_table.php';
        $tableName . '_table.php';
        $migrationRelationPath = database_path('migrations/' . $migrationRelationFileName);

        File::put($migrationRelationPath, $migrationRelationFile);
    }





    private function makeLanguageTable($request, $module)
    {
        $foreignKey      = $module . '_id';
        $pivotTableName  = $module . '_language';
        $pivotSchema     = $this->pivotSchema($module);
        $dropPivotTable  = $module . '_language';
        $migrationPivot = $this->createMigrationFile($pivotSchema, $dropPivotTable);

        $migrationPivotFileName = date('Y_m_d_His', time() + 10) . '_create_' . $pivotTableName . '_table.php';

        $migrationPivotPath     = database_path('migrations/' . $migrationPivotFileName);
        File::put($migrationPivotPath, $migrationPivot);
    }


    private function makeMainTable($request, $module, $payload)
    {
        $moduleExtract = explode('_', $module); // product
        $tableName = $module . 's';
        $migrationFileName = date('Y_m_d_His') . '_create_' . $tableName . '_table.php';
        $migrationPath = database_path('migrations/' . $migrationFileName);
        $migrationTemplate = $this->createMigrationFile($payload['schema'], $tableName);
        File::put($migrationPath, $migrationTemplate);
    }


    private function relationSchema($tableName = '', $module = '')
    {
        $shcema = <<<SCHEMA

Schema::create('{$tableName}', function (Blueprint \$table) {
    \$table->unsignedBigInteger('{$module}_catalogue_id');
    \$table->unsignedBigInteger('{$module}_id');
    \$table->foreign('{$module}_catalogue_id')->references('id')->on('{$module}_catalogues')->onDelete('cascade');
    \$table->foreign('{$module}_id')->references('id')->on('{$module}s')->onDelete('cascade');

    \$table->timestamps();
});
    
SCHEMA;

        return $shcema;
    }

    private function pivotSchema($module)
    {
        $pivotSchema = <<<SCHEMA
Schema::create('{$module}_language', function (Blueprint \$table)
{
    \$table->unsignedBigInteger('{$module}_id');
    \$table->unsignedBigInteger('language_id');
    \$table->foreign('{$module}_id')->references('id')->on('{$module}s')->onDelete('cascade');
    \$table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
    \$table->string('name');
    \$table->text('description')->nullable();
    \$table->longText('content')->nullable();
    \$table->string('meta_title')->nullable();
    \$table->string('meta_keyword')->nullable();
    \$table->text('meta_description')->nullable();
    \$table->string('canonical')->nullable();
    \$table->timestamps();

});
    
SCHEMA;
        return $pivotSchema;
    }


    private function createMigrationFile($schema, $dropTable = '')
    {

        $migrationTemplate = <<<MIGRATION

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        {$schema}

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$dropTable}');
    }
};
MIGRATION;
        return $migrationTemplate;
    }

    private function convertModuleNameToTableName($name)
    {
        $temp = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        return $temp;
    }

    private function makeController($request)
    {
        $payload = $request->only('name', 'module_type');

        switch ($payload['module_type']) {
            case 'catalogue':
                $this->createTemplateController($payload['name'], 'PostCatalogueController');
                break;

            case 'detail':
                $this->createTemplateController($payload['name'], 'PostController');
                break;

            default:
                $this->createSingleController();
        }
    }

    private function createTemplateController($name, $controllerFile)
    {
        $controllerName = $name . 'Controller.php';
        $templateControllerPath = base_path('app/Templates/controllers/' . $controllerFile . '.php');
        $module = explode('_', $this->convertModuleNameToTableName($name));

        $controllerContent = file_get_contents($templateControllerPath);
        $replace = [
            '$class' => ucfirst(current($module)),
            'module' => lcfirst(current($module)),
        ];
        $newContent = $this->replaceContent($controllerContent, $replace);
        $controllerPath = base_path('app/Http/Controllers/Backend/' . $controllerName);
        File::put($controllerPath, $newContent);
        // die();
    }

    private function makeModel($request)
    {
        $moduleType = $request->input('module_type');
        $modelName = $request->input('name') . '.php';

        switch ($moduleType) {
            case 'catalogue':
                $this->createCatalogueModel($request,  $modelName);
                break;
            case 'detail':
                $this->createModel($request,  $modelName);
                break;
            default:
                echo 1;
                die();
        }
    }


    private function createModel($request, $modelName)
    {
        $template = base_path('app/Templates/models/Post.php');
        $content = file_get_contents($template);
        $module = $this->convertModuleNameToTableName($request->input('name')); //product
        $replacement = [
            '$class' => ucfirst($module),
            '$module' => $module
        ];
        $newContent = $this->replaceContent($content, $replacement);
        $this->createModelFile($modelName, $newContent);
    }



    private function createCatalogueModel($request, $modelName)
    {
        $templateModelPath = base_path('app/Templates/models/PostCatalogue.php');
        $modelContent = file_get_contents($templateModelPath);
        $module = $this->convertModuleNameToTableName($request->input('name')); // product_catalogue
        $extractModule = explode('_', $module); // product, catalogue
        $replace = [
            '$class' => ucfirst($extractModule[0]),
            '$module' => $extractModule[0]
        ];
        foreach ($replace as $key => $val) {
            $modelContent = str_replace('{' . $key . '}', $replace[$key], $modelContent);
        }

        $this->createModelFile($modelName, $modelContent);
    }

    private function replaceContent($content, $replacement)
    {
        $newContent = $content;
        foreach ($replacement as $key => $val) {
            $newContent = str_replace('{' . $key . '}', $replacement[$key], $newContent);
        }
        return $newContent;
    }



    private function createModelFile($modelName, $modelContent)
    {
        $modelPath = base_path('app/Models/' . $modelName);
        File::put($modelPath, $modelContent);
    }


    public function makeRepository($request)
    {

        $name = $request->input('name');
        $module = explode('_', $this->convertModuleNameToTableName($name));
        $repositoryPath = (count($module) == 1)
            ? base_path('app/Templates/repositories/PostRepository.php')
            : base_path('app/Templates/repositories/PostCatalogueRepository.php');
        $path = [
            'Interfaces' => base_path('app/Templates/repositories/TemplateRepositoryInterface.php'),
            'Repositories' => $repositoryPath
        ];
        $replacement = [
            '$class' => ucfirst(current($module)),
            'module' => lcfirst(current($module)),
            '$extend' => (count($module) == 2) ? 'Catalogue' : ''
        ];

        foreach ($path as $key => $val) {
            $content = file_get_contents($val);
            $newContent = $this->replaceContent($content, $replacement);
            $contentPath = ($key == 'Interfaces')
                ? base_path('app/Repositories/Interfaces/' . $name . 'RepositoryInterface.php')
                : base_path('app/Repositories/' . $name . 'Repository.php');


            if (!FILE::exists($contentPath)) {
                FILE::put($contentPath, $newContent);
            }
        }

        // die();
    }

    public function makeService($request)
    {
        $name = $request->input('name');
        $module = explode('_', $this->convertModuleNameToTableName($name));
        $servicePath = (count($module) == 1)
            ? base_path('app/Templates/services/PostService.php')
            : base_path('app/Templates/services/PostCatalogueService.php');
        $path = [
            'Interfaces' => base_path('app/Templates/services/TemplateServiceInterface.php'),
            'Services' => $servicePath
        ];
        // dd($path);
        $replacement = [
            '$class' => ucfirst(current($module)),
            'module' => lcfirst(current($module)),
            '$extend' => (count($module) == 2) ? 'Catalogue' : ''

        ];

        foreach ($path as $key => $val) {
            $content = file_get_contents($val);
            $newContent = $this->replaceContent($content, $replacement);
            $contentPath = ($key == 'Interfaces')
                ? base_path('app/Services/Interfaces/' . $name . 'ServiceInterface.php')
                : base_path('app/Services/' . $name . 'Service.php');


            if (!FILE::exists($contentPath)) {
                FILE::put($contentPath, $newContent);
            }
        }

        // die();
    }



    // private function initializeServiceLayer($layer = '', $folder = '', $request)
    // {
    //     // ProductCatalogueRepository - ProductCatalogueRepositoryInterface
    //     $name = $request->input('name');

    //     $option = [
    //         $layer . 'Name' => $name . $layer,
    //         $layer . 'InterfaceName' => $name . $layer . 'Interface',
    //     ];

    //     $layerInterfaceRead = base_path('app/Templates/Template' . $layer . 'Interface.php');
    //     $layerInterfaceContent = file_get_contents($layerInterfaceRead);
    //     $layerInterfacePath = base_path('app/' . $folder . '/Interfaces/' . $option[$layer . 'InterfaceName'] . '.php');

    //     $layerPathRead = base_path('app/Templates/Template' . $layer . '.php');
    //     $layerContent = file_get_contents($layerPathRead);
    //     $layerPathPut = base_path('app/' . $folder . '/' . $option[$layer . 'Name'] . '.php');

    //     return [
    //         'interface' => [
    //             'layerInterfaceContent' => $layerInterfaceContent,
    //             'layerInterfacePath'    => $layerInterfacePath,
    //         ],
    //         'service' => [
    //             'layerContent' => $layerContent,
    //             'layerPathPut' => $layerPathPut
    //         ]
    //     ];
    // }

    private function makeProvider($request)
    {
        $name = $request->input('name');
        $provider = [
            'providerPath' => base_path('app/Providers/AppServiceProvider.php'),
            'repositoryProviderPath' => base_path('app/Providers/RepositoryServiceProvider.php'),
        ];

        foreach ($provider as $key => $val) {
            $content = file_get_contents($val);
            $insertLine = ($key == 'providerPath') ?
                "'App\\Services\\Interfaces\\{$name}ServiceInterface' => 'App\\Services\\{$name}Service',"
                :
                "'App\\Repositories\\Interfaces\\{$name}RepositoryInterface' => 'App\\Repositories\\{$name}Repository',";

            $position = strpos($content, '];');

            if ($position !== false) {
                $newContent = substr_replace($content, "    " . $insertLine . "\n", $position, 0);
            }

            File::put($val, $newContent);
        }
    }


    private function makeRequest($request)
    {
        // StoreModuleRequest UpdateModuleRequest, DeleteModuleRequest
        $name = $request->input('name');
        $requestArray = ['Store' . $name . 'Request', 'Update' . $name . 'Request', 'Delete' . $name . 'Request'];
        $requestTemplate = ['RequestTemplateStore', 'RequestTemplateUpdate', 'RequestTemplateDelete'];

        if ($request->input('module_type') != 'catalogue') {
            unset($requestArray[2]);
            unset($requestTemplate[2]);
        }

        foreach ($requestTemplate as $key => $val) {
            $requestPath = base_path('app/Templates/requests/' . $val . '.php');
            $requestContent = file_get_contents($requestPath);
            $requestContent = str_replace('{Module}', $name, $requestContent);
            $requestPut = base_path('app/Http/Requests/' . $requestArray[$key] . '.php');
            File::put($requestPut, $requestContent);
        }
    }



    private function makeView($request)
    {

        $name = $request->input('name');
        $module = $this->convertModuleNameToTableName($name);
        $extractModule = explode('_', $module);
        $basePath = resource_path("views/backend/{$extractModule[0]}");

        $folderPath = (count($extractModule) == 2)
            ? "$basePath/{$extractModule[1]}"
            : "$basePath/{$extractModule[0]}";
        $componentPath = "$folderPath/component";

        $this->createDirectory($folderPath);
        $this->createDirectory($componentPath);

        $sourcePath = base_path('app/Templates/views/' . ((count($extractModule) == 2) ?
            'catalogue' : 'post') . '/');

        $viewPath = (count($extractModule) == 2) ? "{$extractModule[0]}.{$extractModule[1]}" : $extractModule[0];
        $replacement = [
            'view'   => $viewPath,
            'module' => lcfirst($name),
            'Module' => $name,
        ];
        $fileArray = ['store.blade.php', 'index.blade.php', 'delete.blade.php'];
        $this->CopyAndReplaceContent($sourcePath, $folderPath, $fileArray, $replacement);
        $componentFile = ['aside.blade.php', 'filter.blade.php', 'table.blade.php'];
        $this->CopyAndReplaceContent("{$sourcePath}component/", $componentPath, $componentFile, $replacement);
    }

    private function createDirectory($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    private function CopyAndReplaceContent(string $sourcePath, string $destinationPath, array $fileArray, array $replacement)
    {
        foreach ($fileArray as $key => $val) {
            $sourceFile = $sourcePath . $val;
            $content = file_get_contents($sourceFile);
            $destination = "{$destinationPath}/{$val}";
            foreach ($replacement as $keyReplace => $replace) {
                $content = str_replace('{' . $keyReplace . '}', $replace, $content);
            }

            if (!File::exists($destination)) {
                File::put($destination, $content);
            }
        }
    }


    private function makeRule($request)
    {
        $name = $request->input('name');
        $destination = base_path('app/Rules/Check' . $name . 'ChildrenRule.php');
        $ruleTemplate = base_path('app/Templates/RuleTemplate.php');
        $content = file_get_contents($ruleTemplate);
        $content = str_replace('{Module}', $name, $content);
        if (!FILE::exists($destination)) {
            FILE::put($destination, $content);
        }
    }


    private function makeRoute($request)
    {
        $name = $request->input('name');
        $module = $this->convertModuleNameToTableName($name);
        $moduleExtract = explode('_', $module);
        $routesPath = base_path('routes/web.php');
        $content = file_get_contents($routesPath);
        $routeUrl = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}/{$moduleExtract[1]}" : $moduleExtract[0];
        $routeName = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}.{$moduleExtract[1]}" : $moduleExtract[0];
        $routeGroup = <<<ROUTE

            Route::group(['prefix' => '$routeUrl'], function () {
                Route::get('index', [{$name}Controller::class, 'index'])->name('{$routeName}.index')
                    ->middleware(AuthenticateMiddleware::class);
                Route::get('create', [{$name}Controller::class, 'create'])->name('{$routeName}.create')
                    ->middleware(AuthenticateMiddleware::class);
                Route::post('store', [{$name}Controller::class, 'store'])->name('{$routeName}.store')
                    ->middleware(AuthenticateMiddleware::class);
                Route::get('{id}/edit', [{$name}Controller::class, 'edit'])->where(['id' => '[0-9]+'])->name('{$routeName}.edit')
                    ->middleware(AuthenticateMiddleware::class);
                Route::post('{id}/update', [{$name}Controller::class, 'update'])->where(['id' => '[0-9]+'])->name('{$routeName}.update')
                    ->middleware(AuthenticateMiddleware::class);
                Route::get('{id}/delete', [{$name}Controller::class, 'delete'])->where(['id' => '[0-9]+'])->name('{$routeName}.delete')
                    ->middleware(AuthenticateMiddleware::class);
                Route::delete('{id}/destroy', [{$name}Controller::class, 'destroy'])->where(['id' => '[0-9]+'])->name('{$routeName}.destroy')
                    ->middleware(AuthenticateMiddleware::class);
            });

            //@@new-module@@

        ROUTE;

        // dd($routeGroup);

        $useController = <<<ROUTE
        use App\Http\Controllers\Backend\\{$name}Controller;
        //@@useController@@
        ROUTE;


        $content = str_replace('//@@new-module@@', $routeGroup, $content);
        $content = str_replace('//@@useController@@', $useController, $content);
        FILE::put($routesPath, $content);
    }




    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->except(['_token', 'send']);
            $generate = $this->generateRepository->update($id, $payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $generate = $this->generateRepository->delete($id);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }







    private function paginateSelect()
    {
        return [
            'id',
            'name',
            'schema',
        ];
    }
}
