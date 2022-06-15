<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Items\ItemRequest;
use App\Repositories\Items\ItemRepository;

class ItemController extends Controller
{
    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    { 
        return $this->repository->browse($request);
    }
    public function browseItem(Request $request)
    { 
        return $this->repository->browseItem($request);
    }
    public function show($id, Request $request)
    {
        return $this->repository->show($id, $request);
    }

    public function store(ItemRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, ItemRequest $request){
      return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request){
      return $this->repository->destroy($id, $request);
    }
    public function itemSubmission(Request $request)
    {
        return $this->repository->itemSubmission($request);
    }
}
