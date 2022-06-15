<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Entities\CompanyAttachment;
use App\Repositories\CompanyAttachmentRepository;
use Illuminate\Http\Request;
use App\Http\Requests\CompanyAttachmentRequest;

class CompanyAttachmentController extends Controller 
{
  public $successStatus = 200;

  public function __construct(CompanyAttachmentRepository $repository)
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

  public function companyAttachment($id, Request $request)
  {
    return $this->repository->browseByCompany($id, $request);
  }

  public function store(CompanyAttachmentRequest $request) {
    return $this->repository->store($request);
  }

  public function update($id, CompanyAttachmentRequest $request)
  {
    return $this->repository->update($id, $request);
  }

  public function destroy($id)
  {
    return $this->repository->destroy($id);
  }
}