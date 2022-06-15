<?php

namespace App\Repositories\Payments;

use App\Entities\PaymentReconciliation;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Payments\PaymentReconciliationResource;

class PaymentReconciliationRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(PaymentReconciliation::class);
	}

	public function browse(Request $request)
	{
		if(!$this->roleHasPermission("Read Payment Reconciliations"))
		{
		    throw new \Exception("User does not have the right permission.", 403); 
		}
		$this->query = $this->getModel()->where('company_id', $this->userLogin()->company_id);
		$this->applyCriteria(new SearchCriteria($request));
		$presenter = new DataPresenter(PaymentReconciliationResource::class, $request);

		return $presenter
			->preparePager()
			->renderCollection($this->query);
	}

	public function show($id, Request $request)
	{
		if(!$this->roleHasPermission("Read Payment Reconciliations"))
		{
		    throw new \Exception("User does not have the right permission.", 403); 
		}
		$this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
		$presenter = new DataPresenter(PaymentReconciliationResource::class, $request);

		return $presenter->render($this->query);
	}

	public function store($request)
	{
		try {
			if(!$this->roleHasPermission("Create Payment Reconciliations"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			$payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;
			$payload['user_id'] = $this->userLogin()->id;
      		$paymentReconciliation = PaymentReconciliation::create($payload);
			return $this->show($paymentReconciliation->id, $request);
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
			PaymentReconciliation::findOrFail($id)->update($request->all());
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
			if(!$this->roleHasPermission("Delete Payment Reconciliations"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			PaymentReconciliation::where(['id' => $id, 'company_id' => $this->userLogin()->company_id])->firstOrFail()->delete();
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
