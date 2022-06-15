<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Payments\PaymentTransactionRepository;
use App\Http\Requests\Payments\PaymentTransactionRequest;

class PaymentTransactionController extends Controller
{
    public function __construct(PaymentTransactionRepository $repository)
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

    public function store(PaymentTransactionRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, PaymentTransactionRequest $request)
    {
        return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request)
    {
        return $this->repository->destroy($id, $request);
    }
}
