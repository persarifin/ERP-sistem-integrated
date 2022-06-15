<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public $successStatus = 200;

    public function __construct(RoleRepository $repository){
      $this->repository = $repository;
    }

    public function index(Request $request){
      return $this->repository->browse($request);
    }

    public function store(RoleRequest $request){
      return $this->repository->store($request);
    }

    public function update($id, Request $request){
      return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request){
      return $this->repository->destroy($id, $request);
    }
}
