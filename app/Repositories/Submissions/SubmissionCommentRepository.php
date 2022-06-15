<?php

namespace App\Repositories\Submissions;

use App\Entities\SubmissionComment;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Submissions\SubmissionCommentResource;
use Carbon\Carbon;

class SubmissionCommentRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(SubmissionComment::class);
	}

	public function browse(Request $request)
	{
		$this->query = $this->getModel();
		$this->applyCriteria(new SearchCriteria($request));
		$presenter = new DataPresenter(SubmissionCommentResource::class, $request);

		return $presenter
			->preparePager()
			->renderCollection($this->query);
	}

	public function show($id, Request $request)
	{
		$this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
		$presenter = new DataPresenter(SubmissionCommentResource::class, $request);

		return $presenter->render($this->query);
	}

	public function store($request)
	{
		try {
			$payload = $request->all();
			$payload['user_id'] = $this->userLogin()->id;
			$payload['company_id'] = $this->userLogin()->company_id;
			$payload['date'] = Carbon::now();
      		$submissionComment = SubmissionComment::create($payload);
			return $this->show($submissionComment->id, $request);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function update($id, $request)
	{
		try {
			SubmissionComment::findOrFail($id)->update($request->all());
			return $this->show($id, $request);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function destroy($id)
	{
		try {
			SubmissionComment::findOrFail($id)->delete();
			return response()->json([
				'success' => true,
				'message' => 'data has been deleted'
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
  	}
}
