<?php

namespace App\Repositories;

use App\Entities\CompanyWallet;
use Illuminate\Http\Request;
use App\Entities\PaymentReconciliation;
use App\Entities\PaymentTransaction;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\CompanyWalletResource;

class CompanyWalletRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(CompanyWallet::class);
	}

	public function browse(Request $request)
	{
		try {
			if (!$this->roleHasPermission('Read Company Wallets')){ 
				throw new \Exception("User does not have the right permission.", 403);
			}
			$this->query = $this->getModel()->where('company_id', $this->userLogin()->company_id);
			$this->applyCriteria(new SearchCriteria($request));
			$presenter = new DataPresenter(CompanyWalletResource::class, $request);
	
			return $presenter
				->preparePager()
				->renderCollection($this->query);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
  	}

	public function show($id, Request $request)
	{
		try {
			if (!$this->roleHasPermission('Read Company Wallets')){ 
				throw new \Exception("User does not have the right permission.", 403);
			}
			$this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
			$presenter = new DataPresenter(CompanyWalletResource::class, $request);
	
			return $presenter->render($this->query);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
  	}

	public function store($request)
	{
		try {
			if (!$this->roleHasPermission('Create Company Wallets')){ 
				throw new \Exception("User does not have the right permission.", 403);
			}
			$payload = $request->all();
			$payload['company_id'] = $this->userLogin()->company_id;

			$companyWallet = CompanyWallet::create($payload);
			return $this->show($companyWallet->id, $request);
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
			if (!$this->roleHasPermission('Update Company Wallets')){ 
				throw new \Exception("User does not have the right permission.", 403);
			}
			CompanyWallet::where(['id' => $id, 'company_id' => $this->userLogin()->company_id])
			->firstOrFail()->update($request->all());
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
			if (!$this->roleHasPermission('Delete Company Wallets')){ 
				throw new \Exception("User does not have the right permission.", 403);
			}
			$companyWallet = CompanyWallet::where(['id' => $id, 'company_id' => $this->userLogin()->company_id])->firstOrFail();
			$transaction = PaymentTransaction::where(['company_wallet_id' => $companyWallet->id])->count();
			$reconciliation = PaymentReconciliation::where(['from_wallet_id' => $companyWallet->id])->orWhere(['to_wallet_id' => $companyWallet->id])->count();
			if ($reconciliation > 0 || $transaction > 0) {
				throw new \Exception("Action Denied, This wallet already having transactions or reconciliations", 403); 
			}
			$companyWallet->delete();
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
