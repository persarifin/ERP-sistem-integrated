<?php

namespace App\Http\Controllers\InterfaceApps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\InterfaceApps\InterfaceRequest;
use App\Repositories\InterfaceApps\InterfaceRepository;

class InterfaceController extends Controller
{
    public function __construct(InterfaceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    { 
        return $this->repository->browse($request);
    }

    public function store(InterfaceRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, InterfaceRequest $request){
      return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request){
      return $this->repository->destroy($id, $request);
    }
}
