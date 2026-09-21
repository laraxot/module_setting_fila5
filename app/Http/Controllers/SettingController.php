<?php

declare(strict_types=1);

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        /**
         * @phpstan-var view-string
         */
        $view = 'setting::index';

        return view($view);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        /**
         * @phpstan-var view-string
         */
        $view = 'setting::create';

        return view($view);
    }

    /**
     * Store a newly created resource in storage.
     *
     * $request is part of the standard Laravel resource-controller signature.
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function store(Request $request): Response
    {
        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Show the specified resource.
     *
     * $id is part of the standard Laravel resource-controller signature.
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function show(int|string $id): View
    {
        /**
         * @phpstan-var view-string
         */
        $view = 'setting::show';

        return view($view);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * $id is part of the standard Laravel resource-controller signature.
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function edit(int|string $id): View
    {
        /**
         * @phpstan-var view-string
         */
        $view = 'setting::edit';

        return view($view);
    }

    /**
     * Update the specified resource in storage.
     *
     * $request and $id are part of the standard Laravel resource-controller signature.
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function update(Request $request, int|string $id): Response
    {
        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * $id is part of the standard Laravel resource-controller signature.
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function destroy(int|string $id): Response
    {
        return response('', Response::HTTP_NO_CONTENT);
    }
}
