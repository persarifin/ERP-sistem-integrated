<?php

namespace App\Http\Controllers\Submissions;

use App\Http\Controllers\Controller;
use App\Repositories\Submissions\SubmissionAttachmentRepository;
use App\Http\Requests\Submissions\SubmissionAttachmentRequest;
use Illuminate\Http\Request;


class SubmissionAttachmentController extends Controller
{
    public function __construct(SubmissionAttachmentRepository $repository)
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

    public function store(SubmissionAttachmentRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, SubmissionAttachmentRequest $request)
    {
        return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request)
    {
        return $this->repository->destroy($id, $request);
    }
}
