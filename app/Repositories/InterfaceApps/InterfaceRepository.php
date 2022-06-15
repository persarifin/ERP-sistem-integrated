<?php

namespace App\Repositories\InterfaceApps;

use App\Entities\InterfaceApp;
use Illuminate\Http\Request;
use App\Http\Criterias\SearchCriteria;
use App\Repositories\BaseRepository;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\InterfaceApps\InterfaceResource;

class InterfaceRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(InterfaceApp::class);
    }
    public function browse(Request $request)
    {
        try{
            // if(!$this->roleHasPermission("Read All Interfaces"))
            // {
            //     throw new \Exception("User does not have the right permission.", 403); 
            // }
            $this->query = $this->getModel();
            $this->applyCriteria(new SearchCriteria($request));
            $presenter = new DataPresenter(InterfaceResource::class, $request);
        
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
            // if(!$this->roleHasPermission("Read Interfaces"))
            // {
            // throw new \Exception("User does not have the right permission.", 403); 
            // }
            $this->query = $this->getModel()->where('id', $id);
            $presenter = new DataPresenter(InterfaceResource::class, $request);
        
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
            // if(!$this->roleHasPermission("Create Interfaces"))
            // {
            //     throw new \Exception("User does not have the right permission.", 403); 
            // }
            // $interfaceApp = 
            InterfaceApp::create($request->all());
			// return $this->show($interfaceApp->id, $request);
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
            // if(!$this->roleHasPermission("Update Interfaces"))
            // {
            //     throw new \Exception("User does not have the right permission.", 403); 
            // }
            InterfaceApp::findOrFail($id)->update($request->all());
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
            // if(!$this->roleHasPermission("Delete Interfaces"))
            // {
            //     throw new \Exception("User does not have the right permission.", 403); 
            // }
            InterfaceApp::findOrFail($id)->delete();
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
