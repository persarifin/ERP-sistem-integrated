<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserHasCompanyRequest;
use App\Repositories\UserHasCompanyRepository;

class UserHasCompanyController extends Controller
{
  public function __construct(UserHasCompanyRepository $repository)
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

  public function store(UserHasCompanyRequest $request) {
    return $this->repository->store($request);
  }

  public function update($id, UserHasCompanyRequest $request)
  {
    return $this->repository->update($id, $request);
  }

  public function destroy($id)
  {
    return $this->repository->destroy($id);
  }
}
