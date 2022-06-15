<?php

namespace App\Repositories\Products;

use App\Entities\ProductAttachment;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Products\ProductAttachmentResource;
use Illuminate\Support\Facades\Storage;
use App\Services\GoogleCloud\Spaces as GoogleSpaces;
use Log;

class ProductAttachmentRepository extends BaseRepository
{
	public function __construct(GoogleSpaces $GoogleSpaces)
	{
    $this->GoogleSpaces = $GoogleSpaces;
		parent::__construct(ProductAttachment::class);
	}

	public function browse(Request $request)
	{
		$this->query = $this->getModel();
		$this->applyCriteria(new SearchCriteria($request));
		$presenter = new DataPresenter(ProductAttachmentResource::class, $request);

		return $presenter
			->preparePager()
			->renderCollection($this->query);
	}

	public function show($id, Request $request)
	{
		$this->query = $this->getModel()->where('id', $id);
		$presenter = new DataPresenter(ProductAttachmentResource::class, $request);

		return $presenter->render($this->query);
	}
  
	public function store($request)
	{
		try {
			$payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;

			$file = $request->file('file');
			$exploded = explode(".", $file->getClientOriginalName());
			$originalFilename = $exploded[0]; 
			
			$payload['file_name'] = $this->generateFilename($file, $payload['attachment_type'], $payload['product_id'], $payload['company_id']);

			// Upload to Google Spaces
			$results = $this->GoogleSpaces->upload('product_attachments', $payload['file_name'], $file);
			$payload['file_location'] = $results['folder_url'];

			$productAttachment = ProductAttachment::create($payload);

			return $this->show($productAttachment->id, $request);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
  	}
  
    public function storeProductAttachment($payload)
    {
    try{
      $payload['company_id'] = $this->userLogin()->company_id;

			$file = $payload['file'];
      $exploded = explode(".", $file->getClientOriginalName());
      
      $originalFilename = $exploded[0]; 
			
      $payload['file_name'] = $this->generateFilename($file, $payload['attachment_type'], $payload['product_id'], $payload['company_id']);

      // Upload to Google Spaces
      $results = $this->GoogleSpaces->upload('product_attachments', $payload['file_name'], $file);
			$payload['file_location'] = $results['folder_url'];
      
      if($payload["attachment_type"] === "PRODUCT PHOTO"){
        ProductAttachment::updateOrCreate([
          'attachment_type' => 'PRODUCT PHOTO',
          'product_id' => $payload['product_id'],
          'company_id' => $payload['company_id']
        ],$payload);
      }else{
        ProductAttachment::create($payload);
      }
		  return "success";
		}catch(\Exception $e){
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
				
				$payload['file_name'] = $this->generateFilename($file, $payload['product_id'], $payload['attachment_type'], $payload['company_id']);
		
				// Upload to Google Spaces
        $results = $this->GoogleSpaces->upload('product_attachments', $payload['file_name'], $file);
				$payload['file_location'] = $results['folder_url'];
      }
      
			ProductAttachment::findOrFail($id)->update($payload);
			return $this->show($id, $request);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
  	}
  
	public function updateProductAttachment($payload)
	{
		$payload['company_id'] = $this->userLogin()->company_id;

		if($payload['file']){
			$file = $payload['file'];
			
			$payload['file_name'] = $this->generateFilename($file, $payload['attachment_type'],$payload['product_id'], $payload['company_id']);
		
			// Upload to Google Spaces
			$results = $this->GoogleSpaces->upload('product_attachments', $payload['file_name'], $file);
			$payload['file_location'] = $results['folder_url'];
		}
    
    	ProductAttachment::findOrFail($payload['id'])->update($payload);
    
    	return true;
  	}

	public function destroy($id)
	{
		try {
			ProductAttachment::findOrFail($id)->delete();
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
  
	protected function generateFilename($file, $type, $productId, $companyId)
	{
		$result = 'productId' .$productId.'_'. preg_replace('/\s+/', '', $type) .'_userId' . $this->userLogin()->id . '_companyId' . $companyId. '.' . $file->getClientOriginalExtension();
		return $result;
	}
}
