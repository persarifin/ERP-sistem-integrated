<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Items\ItemAttachmentRequest;
use App\Repositories\Items\ItemAttachmentRepository;

class ItemAttachmentController extends Controller
{
    public function __construct(ItemAttachmentRepository $repository)
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

    public function store(ItemAttachmentRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, ItemAttachmentRequest $request){
      return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request){
      return $this->repository->destroy($id, $request);
    }
}
