<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Products\ProductCategoryRepository;
use App\Http\Requests\Products\ProductCategoryRequest;

class ProductCategoryController extends Controller
{
    public function __construct(ProductCategoryRepository $repository)
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

    public function store(ProductCategoryRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, ProductCategoryRequest $request)
    {
        return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request)
    {
        return $this->repository->destroy($id, $request);
    }
}
