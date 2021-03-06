<?php

namespace App\Http\Controllers\Submissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Submissions\SubmissionCategoryRepository;
use App\Http\Requests\Submissions\SubmissionCategoryRequest;

class SubmissionCategoryController extends Controller
{
    public function __construct(SubmissionCategoryRepository $repository)
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

    public function store(SubmissionCategoryRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, SubmissionCategoryRequest $request)
    {
        return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request)
    {
        return $this->repository->destroy($id, $request);
    }
}
