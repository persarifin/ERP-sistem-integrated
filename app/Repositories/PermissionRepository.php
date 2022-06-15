<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Http\Criterias\SearchCriteria;
use App\Http\Resources\PermissionResource;
use App\Entities\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;
use App\Http\Presenters\DataPresenter;
use Log;

class PermissionRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(Permission::class);
	}
	
	public function browse(Request $request)
	{
    try{
		if(!$this->roleHasPermission("Read Permissions"))
		{
			throw new \Exception("User does not have the right permission.", 403); 
		}
		if($this->userLogin()->hasRole('super_enterprise')){
			$this->query = $this->getModel()->whereNotIn('name', config('permission.blacklist'));
		}			
		else {
			$this->query = $this->getModel()->whereNotIn('name', array_merge(config('permission.blacklist'),config('permission.unique')));
		}
		$this->applyCriteria(new SearchCriteria($request));
		$presenter = new DataPresenter(PermissionResource::class, $request);
	
		return $presenter
			->preparePager()
			->renderCollection($this->query);
		}catch(\Exception $e){
			return response()->json([
			'success' => false,
			'message' => $e->getMessage()
			], 403);
		}
	}

	public function show($id, Request $request)
	{
		$this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
		$presenter = new DataPresenter(PermissionResource::class, $request);

		return $presenter->render($this->query);
	}

	public function store($request)
	{
		try {
			
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
			Permission::findOrFail($id)->update($request->all());
			return $this->show($id, $request);
		} catch (\Exception $e) {
			return response()->json([
        'success' => false,
        'message' => 'No query results for id '.$id
				//'message' => $e->getMessage()
			], 400);
		}
	}

	public function destroy($id)
	{
		try {
			Permission::findOrFail($id)->delete();
			return response()->json([
				'success' => true,
				'message' => 'data has been deleted'
			], 200);
		} catch (\Exception $e) {
			return response()->json([
        'success' => false,
        'message' => 'No query results for id '.$id
				//'message' => $e->getMessage()
			], 400);
		}
  }
}
