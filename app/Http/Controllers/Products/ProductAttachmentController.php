<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Products\ProductAttachmentRepository;
use App\Http\Requests\Products\ProductAttachmentRequest;

class ProductAttachmentController extends Controller
{
    public function __construct(ProductAttachmentRepository $repository)
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

    public function store(ProductAttachmentRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, ProductAttachmentRequest $request)
    {
        return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request)
    {
        return $this->repository->destroy($id, $request);
    }
}
