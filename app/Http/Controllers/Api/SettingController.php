<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Repository\Contracts\SettingRepositoryInterface;
use App\Helpers\Response;

class SettingController extends Controller
{
    protected $settingRepository;

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Display a listing of public settings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $settings = $this->settingRepository->getAll();
            
            return Response::api(
                __('message.Success'),
                200,
                true,
                null,
                SettingResource::collection($settings)
            );
        } catch (\Exception $e) {
            return Response::api(__('message.Not Found'), 404, false);
        }
    }

    /**
     * Display the specified setting by key.
     *
     * @param  string  $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($key)
    {
        try {
            $value = $this->settingRepository->get($key, app()->getLocale());
            
            if ($value === null) {
                return Response::api(__('message.Not Found'), 404, false);
            }

            return Response::api(
                __('message.Success'),
                200,
                true,
                null,
                [
                    'key' => $key,
                    'value' => $value,
                ]
            );
        } catch (\Exception $e) {
            return Response::api(__('message.Not Found'), 404, false);
        }
    }
}
