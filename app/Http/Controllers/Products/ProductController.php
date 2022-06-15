<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Products\ProductRepository;
use App\Http\Requests\Products\ProductRequest;

class ProductController extends Controller
{
    public function __construct(ProductRepository $repository)
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

    public function store(ProductRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, ProductRequest $request)
    {
        return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request)
    {
        return $this->repository->destroy($id, $request);
    }
    public function updateStatus($id, Request $request)
    {
        return $this->repository->updateStatus($id, $request);
    }
}
