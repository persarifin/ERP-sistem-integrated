<?php

namespace App\Http\Controllers\Contents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Contents\ContentCategoryAttachmentRequest;
use App\Repositories\Contents\ContentCategoryAttachmentRepository;

class ContentCategoryAttachmentController extends Controller
{
    public function __construct(ContentCategoryAttachmentRepository $repository)
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

    public function store(ContentCategoryAttachmentRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, ContentCategoryAttachmentRequest $request){
      return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request){
      return $this->repository->destroy($id, $request);
    }
}