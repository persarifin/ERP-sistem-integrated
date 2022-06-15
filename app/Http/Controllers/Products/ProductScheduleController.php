<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Products\ProductScheduleRepository;
use App\Http\Requests\Products\ProductScheduleRequest;
use App\Http\Requests\Products\DateRequest;

class ProductScheduleController extends Controller
{
    public function __construct(ProductScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        return $this->repository->browse($request);
    }
    public function show($id, Request $request)
    {
        return $this->repository->show($id, $request);
    }

    public function store(ProductScheduleRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, ProductScheduleRequest $request)
    {
        return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request)
    {
        return $this->repository->destroy($id, $request);
    }
}
