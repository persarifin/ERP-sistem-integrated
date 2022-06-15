<?php

namespace App\Repositories\Statuses;

use App\Entities\ReadStatus;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Statuses\ReadStatusResource;

class ReadStatusRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(ReadStatus::class);
	}

	public function browse(Request $request)
	{
		$this->query = $this->getModel();
		$this->applyCriteria(new SearchCriteria($request));
		$presenter = new DataPresenter(ReadStatusResource::class, $request);

		return $presenter
			->preparePager()
			->renderCollection($this->query);
	}

	public function show($id, Request $request)
	{
		$this->query = $this->getModel()->where('id', $id);
		$presenter = new DataPresenter(ReadStatusResource::class, $request);

		return $presenter->render($this->query);
	}

	public function store($request)
	{
		try {
			  ReadStatus::create($payload);
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
			ReadStatus::findOrFail($id)->update($request->all());
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
			ReadStatus::findOrFail($id)->delete();
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
