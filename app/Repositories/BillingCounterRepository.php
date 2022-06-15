<?php

namespace App\Repositories;

use App\Entities\BillingCounter;
use Illuminate\Http\Request;

use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\BillingCounterResource;

class BillingCounterRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(BillingCounter::class);
	}

	public function browse(Request $request){
    try{
      $this->query = $this->getModel();
      if($this->roleHasPermission("Read All Billing Counters"))
      {
        $this->query = $this->query;
      }
      elseif($this->roleHasPermission("Read Billing Counters"))
      {
        $this->query = $this->query->where('company', $this->userLogin()->company_id);
      }
      else {
        throw new \Exception("User does not have the right permission.", 403); 
      }
      $this->applyCriteria(new SearchCriteria($request));
      $presenter = new DataPresenter(BillingCounterResource::class, $request);
  
      return $presenter
        ->preparePager()
        ->renderCollection($this->query);
    }catch(\Exception $e){
      return response()->json([
        'success' => false,
				'message' => $e->getMessage()
			], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
    }
  }

	public function show($id, Request $request){
    try{
      if($this->roleHasPermission("Read All Billing Counters"))
      {
        $this->query = $this->getModel()->where(['id' => $id]);
      }
      elseif($this->roleHasPermission("Read All Billing Counters"))
      {
        $this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
      }else {
        throw new \Exception("User does not have the right permission.", 403); 
      }
      $presenter = new DataPresenter(BillingCounterResource::class, $request);
      return $presenter->render($this->query);
    }catch(\Exception $e){
      return response()->json([
        'success' => false,
				'message' => $e->getMessage()
			], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
    }
  }
  
  public function showByCompany($id, $request) {
    $this->query = $this->getModel()->where('company_id', $id);
    $presenter = new DataPresenter(BillingCounterResource::class, $request);
    
    return $presenter->render($this->query);
  }

	public function store($request)
	{
		try {
      if(!$this->roleHasPermission("Create Billing Counters"))
      {
        throw new \Exception("User does not have the right permission.", 403); 
      }
			$billingCounter = BillingCounter::create($request->all());
			return $this->show($billingCounter->id, $request);
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
      if(!$this->roleHasPermission("Update Billing Counters"))
      {
        throw new \Exception("User does not have the right permission.", 403); 
      }
			BillingCounter::findOrFail($id)->update($request->all());
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
      if(!$this->roleHasPermission("Delete Billing Counters"))
      {
        throw new \Exception("User does not have the right permission.", 403); 
      }
			BillingCounter::findOrFail($id)->delete();
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
