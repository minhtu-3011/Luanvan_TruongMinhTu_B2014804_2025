<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\DistrictRepositoryInterface;
use App\Repositories\Interfaces\ProvinceRepositoryInterface;


class LocationController extends Controller
{

    protected $districtRepository;
    protected $provinceRepository;
    public function __construct(
        DistrictRepositoryInterface $districtRepository,
        ProvinceRepositoryInterface $provinceRepository
    ) {
        $this->districtRepository = $districtRepository;
        $this->provinceRepository = $provinceRepository;
    }
    public function getLocation(Request $request)
    {
        $get = $request->input();
        $html = '';
        if ($get['target'] == 'districts') {
            $province = $this->provinceRepository->findById($get['data']['location_id'], ['code', 'name'], ['districts']);
            $html = $this->renderHTMl($province->districts);
        } else if ($get['target'] == 'wards') {
            $district = $this->districtRepository->findById($get['data']['location_id'], ['code', 'name'], ['wards']);
            $html = $this->renderHTMl($district->wards, '[Chọn phường/Xã]');
        };


        $response = [
            'html' => $html
        ];
        return response()->json($response);
    }

    public function renderHTML($districts, $root = '[chọn quận/huyện]')
    {
        $html = '<option value="0">' . $root . '</option>';
        foreach ($districts as $district) {
            $html .= '<option value="' . $district->code . '">' . $district->name . '</option>';
        }
        return $html;
    }
}
