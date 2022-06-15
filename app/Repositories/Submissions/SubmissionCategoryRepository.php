<?php

namespace App\Repositories\Submissions;

use App\Entities\SubmissionCategory;
use App\Entities\Submission;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Submissions\SubmissionCategoryResource;

class SubmissionCategoryRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(SubmissionCategory::class);
	}

	public function browse(Request $request)
	{
		try{
			if (!$this->roleHasPermission('Read Submission Categories')){ 
				throw new \Exception("User does not have the right permission.", 403);
			}
			$this->query = $this->getModel()->where('company_id', $this->userLogin()->company_id);
			$this->applyCriteria(new SearchCriteria($request));
			$presenter = new DataPresenter(SubmissionCategoryResource::class, $request);

			return $presenter
				->preparePager()
			->renderCollection($this->query);
		}catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function show($id, Request $request)
	{
		try {
			if (!$this->roleHasPermission('Read Submission Categories')){ 
				throw new \Exception("User does not have the right permission.", 403);
			}
			$this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
			$presenter = new DataPresenter(SubmissionCategoryResource::class, $request);

			return $presenter->render($this->query);
		}catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function store($request)
	{
		try {
			if (!$this->roleHasPermission('Create Submission Categories')){ 
				throw new \Exception("User does not have the right permission.", 403);
			}
			$payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;
      		$submissionCategory = SubmissionCategory::create($payload);
			return $this->show($submissionCategory->id, $request);
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
			if (!$this->roleHasPermission('Update Submission Categories')){ 
				throw new \Exception("User does not have the right permission.", 403);
			}
			SubmissionCategory::findOrFail($id)->update($request->all());
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
			if (!$this->roleHasPermission('Delete Submission Categories')){ 
				throw new \Exception("User does not have the right permission.", 403);
			}
			$submissionCategory = SubmissionCategory::where(['id' => $id, 'company_id' => $this->userLogin()->company_id])->firstOrFail();
			$countSubmission = Submission::where('category_id', $submissionCategory->id)->count();
			if ($countSubmission > 0) {
				throw new \Exception("Operation failed, because this category already having submission", 403); 
			}
			$submissionCategory->delete();
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
