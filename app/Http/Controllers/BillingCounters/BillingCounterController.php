<?php

namespace App\Http\Controllers\BillingCounters;

use App\Entities\BillingCounter;
use App\Repositories\BillingCounterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use Log;

class BillingCounterController extends Controller
{
    public $successStatus = 200;

    public function __construct(BillingCounterRepository $repository) {
      $this->repository = $repository;
    }

    public function index(Request $request) {
      return $this->repository->browse($request);
    }
    public function show($id, Request $request)
    {
      return $this->repository->show($id, $request);
    }

    public function billingCounterByCompany($id, Request $request) {
      return $this->repository->showByCompany($id, $request);
    }

    public function billingCountersByCompany($id, Request $request) {
      return $this->repository->browseByCompany($id, $request);
    }
}
