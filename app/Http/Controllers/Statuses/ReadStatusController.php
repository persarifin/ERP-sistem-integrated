<?php

namespace App\Http\Controllers\Statuses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Statuses\ReadStatusRepository;
use App\Http\Requests\Statuses\ReadStatusRequest;

class ReadStatusController extends Controller
{
    public function __construct(ReadStatusRepository $repository)
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

    public function store(ReadStatusRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, ReadStatusRequest $request)
    {
        return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request)
    {
        return $this->repository->destroy($id, $request);
    }
}
