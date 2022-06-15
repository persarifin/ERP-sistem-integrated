<?php

namespace App\Repositories\Products;

use App\Entities\ProductCategory;
use App\Entities\InterfaceApp;
use App\Entities\ProductCategoryAttachment;
use App\Entities\Product;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Products\ProductCategoryResource;

class ProductCategoryRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(ProductCategory::class);
	}

	public function ProductCategoryLandingPage($companyId, $request)
	{
		$this->query = $this->getModel()->where('company_id', $companyId);
		$presenter = new DataPresenter(ProductCategoryResource::class, $request);

		return $presenter
			->preparePager()
			->renderCollection($this->query);
	}
	public function browse(Request $request)
	{
		try {
			if(!$this->roleHasPermission("Read Product Categories"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			$rule = [];
			if($request->filter && is_array($request->input('filter'))) {
				foreach ($request->input('filter') as $name => $criteria) {
					if (!is_array($criteria)) {
						$rule = [
							'field' => $name, 
							'operator' => 'is',
							'value' => $criteria
							];
						continue;
					}
					else {
						foreach ($criteria as $operator => $value) {
						$rule = [
							'field' => $name, 
							'operator' => $operator,
							'value' => $value
							];
						}
					}
				}
			}
			$this->query = $this->getModel()->where('company_id', $this->userLogin()->company_id);
			if (!empty($rule) && $rule['field'] == 'visibilited_at') {
				if (is_numeric($rule['value'])) {
					$rule['value'] = $rule['value'];
				}
				else {
					$interface = InterfaceApp::where('interface_name', $rule['value'])->first();
					$rule['value'] = $interface->id;
				}
				$this->query = $this->query->whereHas('product', function($q) use($rule){
					$q->whereHas('product_visibility', function($q) use($rule){
						$q->where('interface_id', $rule['value']);
					});
				});
			}
			$this->applyCriteria(new SearchCriteria($request));
			$presenter = new DataPresenter(ProductCategoryResource::class, $request);

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
		try{
			if(!$this->roleHasPermission("Read Product Categories"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			$this->query = $this->getModel()->where('id', $id);
			$presenter = new DataPresenter(ProductCategoryResource::class, $request);

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
			if(!$this->roleHasPermission("Create Product Categories"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			$payload = $request->all();
		    $this->query = $this->getModel()->where(['company_id' => $this->userLogin()->company_id]);
			$payload['company_id'] = $this->userLogin()->company_id;
      		$productCategory = ProductCategory::create($payload);
			return $this->show($productCategory->id, $request);
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
			if(!$this->roleHasPermission("Update Product Categories"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			ProductCategory::findOrFail($id)->update($request->all());
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
			if(!$this->roleHasPermission("Update Product Categories"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			$productCategory = ProductCategory::where(['id' => $id, 'company_id' => $this->userLogin()->company_id])->firstOrFail();
			$countProduct = Product::where('category_id', $productCategory->id)->count();
			if ($countProduct > 0) {
				throw new \Exception("Action denied, this category already having Product", 403); 
			}
			ProductCategoryAttachment::where('category_id',$productCategory->id)->delete();
			$productCategory->delete();
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
