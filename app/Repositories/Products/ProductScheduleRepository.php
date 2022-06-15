<?php

namespace App\Repositories\Products;

use App\Entities\ProductSchedule;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Products\ProductScheduleResource;
use Carbon\Carbon;

class ProductScheduleRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(ProductSchedule::class);
	}

	public function browse(Request $request)
	{
		try {
			if(!$this->roleHasPermission("Read Product Schedules"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			$this->query = $this->getModel()->where("company_id", $this->userLogin()->company_id);
			$this->applyCriteria(new SearchCriteria($request));
			$presenter = new DataPresenter(ProductScheduleResource::class, $request);

			return $presenter
				->preparePager()
				->renderCollection($this->query);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function show($id, Request $request)
	{
		try {
			$this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
			$presenter = new DataPresenter(ProductScheduleResource::class, $request);

			return $presenter->render($this->query);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function store($request)
	{
		try {
			$payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;
			$productSchedule = ProductSchedule::create($payload);
			return $this->show($productSchedule->id, $request);
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
			ProductSchedule::findOrFail($id)->update($request->all());
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
			ProductSchedule::findOrFail($id)->delete();
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
