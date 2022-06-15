<?php

namespace App\Http\Controllers\Permissions;

use App\Http\Controllers\Controller;
use App\Repositories\PermissionRepository;
use App\Http\Requests\PermissionRequest;
use Illuminate\Http\Request;
use App\Entities\Role;

class PermissionController extends Controller
{
    public $successStatus = 200;

    public function __construct(PermissionRepository $repository){
      $this->repository = $repository;
    }

    public function index(Request $request){
      return $this->repository->browse($request);
    }

    public function store(PermissionRequest $request){
      return $this->repository->store($request);
    }

    public function update($id, Request $request){
      return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request){
      return $this->repository->destroy($id, $request);
    }
}
