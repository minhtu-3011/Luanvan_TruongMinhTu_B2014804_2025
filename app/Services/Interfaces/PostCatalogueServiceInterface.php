<?php

namespace App\Services\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface PostCatalogueServiceInterface
{
    public function paginate($request);
    public function create($request);
    public function update($id, $request);
    public function destroy($id);
}
