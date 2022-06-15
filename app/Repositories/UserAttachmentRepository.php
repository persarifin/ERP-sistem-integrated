<?php

namespace App\Repositories;

use App\Entities\UserAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\UserAttachmentResource;
use App\Services\GoogleCloud\Spaces as GoogleSpaces;

class UserAttachmentRepository extends BaseRepository
{
	public function __construct(GoogleSpaces $GoogleSpaces)
	{
    $this->GoogleSpaces = $GoogleSpaces;
		parent::__construct(UserAttachment::class);
	}

  public function browse(Request $request)
  {
    try{
      $this->query = $this->getModel()->where('user_id', $this->userLogin()->id);
      $this->applyCriteria(new SearchCriteria($request));
      $presenter = new DataPresenter(UserAttachmentResource::class, $request);
  
      return $presenter
        ->preparePager()
        ->renderCollection($this->query);
    }catch(\Exception $e){
      return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
    }
  }

  public function show($id, Request $request)
  {
    try{
      $this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
      $presenter = new DataPresenter(UserAttachmentResource::class, $request);
  
      return $presenter->render($this->query);
    }catch(\Exception $e){
      return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
    }
  }
  
  public function create($payload)
  {
    $file = $payload['file']; 
    $exploded = explode(".", $file->getClientOriginalName());
    $originalFilename = $exploded[0]; 
			
    $payload['file_name'] = $this->generateFilename($file, $payload['attachment_type']);

    $results = $this->GoogleSpaces->upload('user_attachments', $payload['file_name'], $file);
    $payload['file_location'] = $results['folder_url'];

    $userAttachment = UserAttachment::create($payload);

    return true;
  }
  
	public function store($request)
	{
		try {
      $payload = $request->all();
      $payload['company_id'] = $this->userLogin()->company_id;
      $payload['user_id'] = $this->userLogin()->id;

      $file = $request->file('file');
      
      $payload['file_name'] = $this->generateFilename($file, $payload['attachment_type']);

      // Upload to Google Spaces
      $results = $this->GoogleSpaces->upload('user_attachments', $payload['file_name'], $file);
      $payload['file_location'] = $results['folder_url'];
      if($payload["attachment_type"] === "PHOTO PROFILE"){
        $userAttachment = UserAttachment::updateOrCreate([
          'user_id' => $payload['user_id'],
          'attachment_type' => $payload['attachment_type']
        ], $payload);
      }else{
        $userAttachment = UserAttachment::create($payload);
      }

      return $this->show($userAttachment->id, $request);
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
      $payload['user_id'] = $this->userLogin()->id;

      if($request->has('file') && $request->has('type')){
        $file = $request->file('file');
        
        $payload['file_name'] = $this->generateFilename($file, $payload['type']);
  
        // Upload to Google Spaces
        $results = $this->GoogleSpaces->upload('user_attachments', $payload['file_name'], $file);
        $payload['file_location'] = $results['folder_url'];
      }
      
      UserAttachment::where(['id' => $id, 'user_id' => $payload['user_id']])->firstOrFail()->update($payload);
      // UserAttachment::findOrFail($id)->update($payload);
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
      UserAttachment::where(['id' => $id, 'user_id' => $this->userLogin()->id])->firstOrFail()->delete();
			// UserAttachment::findOrFail($id)->delete();
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
  
  protected function generateFilename($file, $type)
  {
    $result = 'userId'.$this->userLogin()->id.'_'.preg_replace('/\s+/', '', $type).'.'.$file->getClientOriginalExtension();
    return $result;
  }
}
