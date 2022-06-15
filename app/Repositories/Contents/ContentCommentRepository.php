<?php

namespace App\Repositories\Contents;

use App\Entities\ContentComment;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Contents\ContentCommentResource;

class ContentCommentRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(ContentComment::class);
	}

	public function browse(Request $request)
    {
        try{
		    $this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
            $this->applyCriteria(new SearchCriteria($request));
            $presenter = new DataPresenter(ContentCommentResource::class, $request);
        
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
            $this->query = $this->getModel()->where('id', $id);
            $presenter = new DataPresenter(ContentCommentResource::class, $request);
        
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
            $payload = $request->all();
            $payload['user_id'] = $this->userLogin()->id;
			$payload['company_id'] = $this->userLogin()->company_id;
            $contentComment = ContentComment::create($payload);
			return $this->show($contentComment->id, $request);
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
            $payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;
            ContentComment::findOrFail($id)->update($payload);
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
            ContentComment::findOrFail($id)->delete();
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
