<?php

namespace App\Repositories\Items;

use App\Entities\ItemAttachment;
use Illuminate\Http\Request;
use App\Http\Criterias\SearchCriteria;
use App\Repositories\BaseRepository;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\ItemAttachmentResource;
use App\Services\GoogleCloud\Spaces as GoogleSpaces;


class ItemAttachmentRepository extends BaseRepository
{
	public function __construct(GoogleSpaces $GoogleSpaces)
	{
    $this->GoogleSpaces = $GoogleSpaces;
		parent::__construct(ItemAttachment::class);
	}

	public function browse(Request $request)
	{
		$this->query = $this->getModel();
		$this->applyCriteria(new SearchCriteria($request));
		$presenter = new DataPresenter(ItemAttachmentResource::class, $request);

		return $presenter
			->preparePager()
			->renderCollection($this->query);
	}

	public function show($id, Request $request)
	{
		$this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
		$presenter = new DataPresenter(ItemAttachmentResource::class, $request);

		return $presenter->render($this->query);
	}
  
	public function store($request)
	{
		try {
			$payload = $request->all();
			$payload["company_id"] = $this->userLogin()->company_id;

			$file = $request->file('file');
			$exploded = explode(".", $file->getClientOriginalName());
			$originalFilename = $exploded[0]; 
			
			$payload['file_name'] = $this->generateFilename($file, $payload['item_id'], $payload['company_id']);

			// Upload to Google Spaces
			$results = $this->GoogleSpaces->upload('item_attachments', $payload['file_name'], $file);
			$payload['file_location'] = $results['folder_url'];

			$contentAttachment = ItemAttachment::create($payload);

			return $this->show($contentAttachment->id, $request);
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
				
				$payload['file_name'] = $this->generateFilename($file,$payload['item_id'], $payload['company_id']);
		
				// Upload to Google Spaces
				$results = $this->GoogleSpaces->upload('item_attachments', $payload['file_name'], $file);
				$payload['file_location'] = $results['folder_url'];
      		}
      
			ItemAttachment::findOrFail($id)->update($payload);
			
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
			ItemAttachment::findOrFail($id)->delete();
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
	
	protected function generateFilename($file, $itemId, $companyId)
	{
		$result = 'itemId' .$itemId .'_userId' . $this->userLogin()->id . '_companyId' . $companyId. '.' . $file->getClientOriginalExtension();
		return $result;
	}
}
