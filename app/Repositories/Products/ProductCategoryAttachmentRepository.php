<?php

namespace App\Repositories\Products;

use App\Entities\ProductCategoryAttachment;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Products\ProductCategoryAttachmentResource;
use Illuminate\Support\Facades\Storage;
use App\Services\GoogleCloud\Spaces as GoogleSpaces;

class ProductCategoryAttachmentRepository extends BaseRepository
{
	public function __construct(GoogleSpaces $GoogleSpaces)
	{
    $this->GoogleSpaces = $GoogleSpaces;
		parent::__construct(ProductCategoryAttachment::class);
	}

	public function browse(Request $request)
	{
		$this->query = $this->getModel();
		$this->applyCriteria(new SearchCriteria($request));
		$presenter = new DataPresenter(ProductCategoryAttachmentResource::class, $request);

		return $presenter
			->preparePager()
			->renderCollection($this->query);
	}

	public function show($id, Request $request)
	{
		$this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
		$presenter = new DataPresenter(ProductCategoryAttachmentResource::class, $request);

		return $presenter->render($this->query);
	}
  
	public function store($request)
	{
		try {
			$payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;

			$file = $request->file('file');
			
			
			$payload['file_name'] = $this->generateFilename($file, $payload['category_id'], $payload['company_id']);

			// Upload to Google Spaces
			$results = $this->GoogleSpaces->upload('product_category_attachments', $payload['file_name'], $file);
			$payload['file_location'] = $results['folder_url'];

			// $productCategoryAttachment = 
			ProductCategoryAttachment::create($payload);

			// return $this->show($productCategoryAttachment->id, $request);
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
			$payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;

			if($request->has('file')){
				$file = $request->file('file');
				
				$payload['file_name'] = $this->generateFilename($file,$payload['category_id'], $payload['company_id']);
		
				// Upload to Google Spaces
				$results = $this->GoogleSpaces->upload('product_category_attachments', $payload['file_name'], $file);
				$payload['file_location'] = $results['folder_url'];
      }
      
			ProductCategoryAttachment::findOrFail($id)->update($payload);
			
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
			ProductCategoryAttachment::findOrFail($id)->delete();
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
	
	protected function generateFilename($file, $categoryId, $companyId)
	{
		$exploded = explode(".", $file->getClientOriginalName());
			$originalFilename = $exploded[0]; 
		$result = $originalFilename.'_categoryId' .$categoryId .'_userId' . $this->userLogin()->id . '_companyId' . $companyId. '.' . $file->getClientOriginalExtension();
		return $result;
	}
}
