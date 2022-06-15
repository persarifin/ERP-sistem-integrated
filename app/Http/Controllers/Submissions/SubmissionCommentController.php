<?php

namespace App\Http\Controllers\Submissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Submissions\SubmissionCommentRepository;
use App\Http\Requests\Submissions\SubmissionCommentRequest;

class SubmissionCommentController extends Controller
{
    public function __construct(SubmissionCommentRepository $repository)
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

    public function store(SubmissionCommentRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, SubmissionCommentRequest $request)
    {
        return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request)
    {
        return $this->repository->destroy($id, $request);
    }
}
