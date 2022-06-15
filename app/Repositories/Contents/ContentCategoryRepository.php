<?php

namespace App\Repositories\Contents;

use App\Entities\ContentCategory;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Contents\ContentCategoryResource;
use App\Http\Requests\Contents\ContentCategoryRequest;

class ContentCategoryRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(ContentCategory::class);
	}

	public function browse(Request $request)
    {
        try{
            if(!$this->roleHasPermission("Read Content Categories"))
            {
                throw new \Exception("User does not have the right permission.", 403); 
            }
            $this->query = $this->getModel();
            $this->applyCriteria(new SearchCriteria($request));
            $presenter = new DataPresenter(ContentCategoryResource::class, $request);
        
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
            if(!$this->roleHasPermission("Read Content Categories"))
            {
            throw new \Exception("User does not have the right permission.", 403); 
            }
		    $this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
            $presenter = new DataPresenter(ContentCategoryResource::class, $request);
        
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
            if(!$this->roleHasPermission("Create Content Categories"))
            {
                throw new \Exception("User does not have the right permission.", 403); 
            }
            
			$payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;
            $contentCategory = ContentCategory::create($payload);
			return $this->show($contentCategory->id, $request);
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
            if(!$this->roleHasPermission("Update Content Categories"))
            {
                throw new \Exception("User does not have the right permission.", 403); 
            }
            $payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;
            ContentCategory::findOrFail($id)->update($payload);
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
            if(!$this->roleHasPermission("Delete Content Categories"))
            {
                throw new \Exception("User does not have the right permission.", 403); 
            }
            ContentCategory::findOrFail($id)->delete();
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
