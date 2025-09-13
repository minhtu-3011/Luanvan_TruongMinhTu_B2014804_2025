<?php

namespace App\Services;

use App\Services\Interfaces\LanguageServiceInterface;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserCatalogueService
 * @package App\Services
 */
class LanguageService implements LanguageServiceInterface
{
    protected $languageRepository;
    public function __construct(
        LanguageRepository $languageRepository

    ) {
        $this->languageRepository = $languageRepository;
    }

    public function paginate($request)
    {



        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish', -1);
        $perpage = $request->integer('perpage', 10);
        $language = $this->languageRepository->pagination(
            $this->paginateSelect(),
            $condition,
            [],
            ['path' => 'language/index'],
            $perpage,
            []
        );
        return $language;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {

            $payload = $request->except(['_token', 'send']);
            $payload['user_id'] = Auth::id();
            // dd($payload);
            $language = $this->languageRepository->create($payload);

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

    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->except(['_token', 'send']);
            $language = $this->languageRepository->update($id, $payload);

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
            $language = $this->languageRepository->delete($id);

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

    public function updateStatus($post = [])
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = (($post['value'] == 1) ? 0 : 1);

            $language = $this->languageRepository->update($post['modelId'], $payload);
            // $this->changeLangueStatus($post, $payload[$post['field']]);
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

    public function updateStatusAll($post)
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = $post['value'];

            $flag = $this->languageRepository->updateByWhereIn('id', $post['id'], $payload);
            // $this->changeLangueStatus($post, $post['value']);
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

    // private function changeUserStatus($post, $value)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $array = [];
    //         if (isset($post['modelId'])) {
    //             $array[] = $post['modelId'];
    //         } else {
    //             $array = $post['id'];
    //         }

    //         $payload[$post['field']] = $value;
    //         $this->userRepository->updateByWhereIn('user_catalogue_id', $array, $payload);
    //         DB::commit();
    //         return true;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         // Log::error($e->getMessage());
    //         echo $e->getMessage();
    //         die();
    //         return false;
    //     }
    // }

    private function paginateSelect()
    {
        return [
            'id',
            'name',
            'canonical',
            'publish',
            'image'
        ];
    }
}
