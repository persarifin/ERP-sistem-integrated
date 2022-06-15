<?php

namespace App\Repositories\Products;

use App\Entities\ProductVisibility;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Products\ProductVisibilityResource;

class ProductVisibilityRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(ProductVisibility::class);
	}

	public function browse(Request $request)
	{
		$this->query = $this->getModel()->where('company_id', $this->userLogin()->company_id);
		$this->applyCriteria(new SearchCriteria($request));
		$presenter = new DataPresenter(ProductVisibilityResource::class, $request);

		return $presenter
			->preparePager()
			->renderCollection($this->query);
	}

	public function show($id, Request $request)
	{
		$this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
		$presenter = new DataPresenter(ProductVisibilityResource::class, $request);

		return $presenter->render($this->query);
	}

	public function store($request)
	{
		try {
			$payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;
			$productVisibility = ProductVisibility::create($payload);
			return $this->show($productVisibility->id, $request);
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
			ProductVisibility::findOrFail($id)->update($request->all());
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
			ProductVisibility::findOrFail($id)->delete();
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
