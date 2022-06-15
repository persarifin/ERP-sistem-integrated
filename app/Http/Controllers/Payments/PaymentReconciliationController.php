<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Payments\PaymentReconciliationRepository;
use App\Http\Requests\Payments\PaymentReconciliationRequest;

class PaymentReconciliationController extends Controller
{
    public function __construct(PaymentReconciliationRepository $repository)
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

    public function store(PaymentReconciliationRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, PaymentReconciliationRequest $request)
    {
        return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request)
    {
        return $this->repository->destroy($id, $request);
    }
}
