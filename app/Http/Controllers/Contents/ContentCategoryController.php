<?php

namespace App\Http\Controllers\Contents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Contents\ContentCategoryRequest;
use App\Repositories\Contents\ContentCategoryRepository;

class ContentCategoryController extends Controller
{
    public function __construct(ContentCategoryRepository $repository)
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

    public function store(ContentCategoryRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, ContentCategoryRequest $request)
    {
      return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request)
    {
      return $this->repository->destroy($id, $request);
    }
}
