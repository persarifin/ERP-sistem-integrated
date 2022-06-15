<?php

namespace App\Repositories\Submissions;

use App\Entities\SubmissionAttachment;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Submissions\SubmissionAttachmentResource;
use Illuminate\Support\Facades\Storage;
use App\Services\GoogleCloud\Spaces as GoogleSpaces;

class SubmissionAttachmentRepository extends BaseRepository
{
	public function __construct(GoogleSpaces $GoogleSpaces)
	{
		$this->GoogleSpaces = $GoogleSpaces;
		parent::__construct(SubmissionAttachment::class);
	}

	public function browse(Request $request)
	{
		$this->query = $this->getModel();
		$this->applyCriteria(new SearchCriteria($request));
		$presenter = new DataPresenter(SubmissionAttachmentResource::class, $request);

		return $presenter
			->preparePager()
			->renderCollection($this->query);
	}

	public function show($id, Request $request)
	{
		$this->query = $this->getModel()->where('id', $id);
		$presenter = new DataPresenter(SubmissionAttachmentResource::class, $request);

		return $presenter->render($this->query);
	}
  
	public function store($request)
	{
		try {
			$payload = $request->all();
			$payload["company_id"] = $this->userLogin()->company_id;
      $payload["attachment_type"] = $request->attachment_type ? $request->attachment_type : "SUBMISSION CONTENT";

			$file = $request->file('file');
			$exploded = explode(".", $file->getClientOriginalName());
			$originalFilename = $exploded[0]; 
			
			$payload['file_name'] = $this->generateFilename($file, $payload['submission_id'], $payload['attachment_type'], $payload['company_id']);

			// Upload to Google Spaces
			$results = $this->GoogleSpaces->upload('submission_attachments', $payload['file_name'], $file);
			$payload['file_location'] = $results['folder_url'];

			$submissionAttachment = SubmissionAttachment::create($payload);

			return $this->show($submissionAttachment->id, $request);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}
	
	public function storeSubmissionAttachment($payload)
	{
		try{
		$payload['company_id'] = $this->userLogin()->company_id;

		$file = $payload['file'];
		$exploded = explode(".", $file->getClientOriginalName());
		
		$originalFilename = $exploded[0]; 
				
		$payload['file_name'] = $this->generateFilename($file, $payload['submission_id'],$payload['attachment_type'], $payload['company_id']);

		// Upload to Google Spaces
		$results = $this->GoogleSpaces->upload('submission_attachments', $payload['file_name'], $file);
				$payload['file_location'] = $results['folder_url'];
		
		if($payload["attachment_type"] === "SUBMISSION PHOTO"){
			SubmissionAttachment::updateOrCreate([
			'attachment_type' => 'SUBMISSION PHOTO',
			'submission_id' => $payload['submission_id'],
			'company_id' => $payload['company_id']
			],$payload);
		}else{
			SubmissionAttachment::create($payload);
		}
			return "success";
			}catch(\Exception $e){
				throw new \Exception($e->getMessage(), 400);
				// return response()->json([
				// 	'success' => false,
				// 	'message' => $e->getMessage()
		// 		], 400);
			}
	}

	public function update($id, $request)
	{
		try {
			$payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;
     		$payload['attachment_type'] = $request->attachment_type ? $request->attachment_type : "SUBMISSION CONTENT";

			if($request->has('file')){
				$file = $request->file('file');
				
				$payload['file_name'] = $this->generateFilename($file, $payload['submission_id'], $payload['attachment_type'], $payload['company_id']);
		
				// Upload to Google Spaces
				$results = $this->GoogleSpaces->upload('submission_attachments', $payload['file_name'], $file);
				$payload['file_location'] = $results['folder_url'];
      		}
      
			SubmissionAttachment::findOrFail($id)->update($payload);
			
			return $this->show($id, $request);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function updateSubmissionAttachment($payload)
	{
		try{
			$payload['company_id'] = $this->userLogin()->company_id;

			$file = $payload['file'];
			$exploded = explode(".", $file->getClientOriginalName());
			
			$originalFilename = $exploded[0]; 
					
			$payload['file_name'] = $this->generateFilename($file, $payload['submission_id'], $payload['attachment_type'], $payload['company_id']);

			// Upload to Google Spaces
			$results = $this->GoogleSpaces->upload('submission_attachments', $payload['file_name'], $file);
			$payload['file_location'] = $results['folder_url'];
			
			if($payload["attachment_type"] === "SUBMISSION PHOTO"){
				SubmissionAttachment::updateOrCreate([
				'attachment_type' => 'SUBMISSION PHOTO',
				'submission_id' => $payload['submission_id'],
				'company_id' => $payload['company_id']
				],$payload);
			}else{
				SubmissionAttachment::create($payload);
			}

			}catch(\Exception $e){
				throw new \Exception($e->getMessage(), 400);
				// return response()->json([
				// 	'success' => false,
				// 	'message' => $e->getMessage()
		// 		], 400);
			}
	}

	public function destroy($id)
	{
		try {
			SubmissionAttachment::findOrFail($id)->delete();
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
	
	protected function generateFilename($file, $submissionId, $attachmentType, $companyId)
	{
		$result = 'submissionId' .$submissionId .'_' . $attachmentType . '_userId' . $this->userLogin()->id . '_companyId' . $companyId. '.' . $file->getClientOriginalExtension();
		return $result;
	}
}
