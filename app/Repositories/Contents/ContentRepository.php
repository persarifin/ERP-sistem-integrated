<?php

namespace App\Repositories\Contents;

use App\Entities\Content;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Contents\ContentResource;

class ContentRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(Content::class);
	}

	public function browse(Request $request)
    {
        try{
            if(!$this->roleHasPermission("Read Contents"))
            {
                throw new \Exception("User does not have the right permission.", 403); 
            }
		    $this->query = $this->getModel()->where(['company_id' => $this->userLogin()->company_id]);
            $this->applyCriteria(new SearchCriteria($request));
            $presenter = new DataPresenter(ContentResource::class, $request);
        
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
            if(!$this->roleHasPermission("Read Contents"))
            {
            throw new \Exception("User does not have the right permission.", 403); 
            }
            $this->query = $this->getModel()->where('id', $id);
            $presenter = new DataPresenter(ContentResource::class, $request);
        
            return $presenter->render($this->query);
        }catch(\Exception $e){
            return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
        }
    }
  
	public function store($request)
	{
        try {
            if(!$this->roleHasPermission("Create Contents"))
            {
                throw new \Exception("User does not have the right permission.", 403); 
            }
            
            $payload = $request->all();
            $payload['user_id'] = $this->userLogin()->id;
			$payload['company_id'] = $this->userLogin()->company_id;
            $content = Content::create($payload);
			return $this->show($content->id, $request);
		} catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
			], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
		}
	}

	public function update($id, $request)
	{
		try {
            if(!$this->roleHasPermission("Update Contents"))
            {
                throw new \Exception("User does not have the right permission.", 403); 
            }
            $payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;
            Content::findOrFail($id)->update($payload);
			return $this->show($id, $request);
		} catch (\Exception $e) {
			return response()->json([
                'success' => false,
                'message' => $e->getMessage()
			], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
		}
	}

	public function destroy($id)
	{
		try {
            if(!$this->roleHasPermission("Delete Contents"))
            {
                throw new \Exception("User does not have the right permission.", 403); 
            }
            Content::findOrFail($id)->delete();
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
