<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Entities\CompanyWallet;
use App\Http\Requests\CompanyWalletRequest;
use App\Repositories\CompanyWalletRepository;
use Illuminate\Http\Request;
use Validator;

class CompanyWalletController extends Controller 
{
  public $successStatus = 200;

  public function __construct(CompanyWalletRepository $repository)
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
  
  public function store(CompanyWalletRequest $request) 
  {
    return $this->repository->store($request);
  }

  public function update($id, CompanyWalletRequest $request)
  {
    return $this->repository->update($id, $request);
  }

  public function destroy($id)
  {
    return $this->repository->destroy($id);
  }
}