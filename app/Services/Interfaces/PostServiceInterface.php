<?php

namespace App\Services\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface PostServiceInterface
{
    public function paginate($request,  $languageId);
    public function create($request, $languageId);
    public function update($id, $request, $languageId);
    public function destroy($id, $languageId);
}
