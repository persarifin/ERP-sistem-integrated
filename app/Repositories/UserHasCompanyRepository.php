<?php

namespace App\Repositories;

use App\Entities\UserHasCompany;
use App\Entities\Company;
use App\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\UserHasCompanyResource;

class UserHasCompanyRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(UserHasCompany::class);
	}

  public function browse(Request $request)
  {
    try {
      $this->query = $this->getModel()->where('company_id', $this->userLogin()->company_id);
      $this->applyCriteria(new SearchCriteria($request));
      $presenter = new DataPresenter(UserHasCompanyResource::class, $request);
  
      return $presenter
        ->preparePager()
        ->renderCollection($this->query);
    } catch (\Exception $e) {
      return response()->json([
          'success' => false,
          'message' => $e->getMessage(),
      ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
    }
  }

  public function show($id, Request $request)
  {
    $this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
		$presenter = new DataPresenter(UserHasCompanyResource::class, $request);

		return $presenter->render($this->query);
  }
  
	public function store($request)
	{
		try {
      $payload = $request->all();
      $foundCompany = Company::find($payload['company_id']);
      $foundUser = User::find($payload['user_id']);
      
      if(!$foundCompany)
      {
        throw new \Exception("Company doesn't exist.", 404);
      }

      if(!$foundUser)
      {
        throw new \Exception("User doesn't exist.", 404);
      }

      $userHasCompany = UserHasCompany::updateOrCreate([
        'user_id' => $payload['user_id'],
        'company_id' => $payload['company_id']
      ],
      [
        'user_id' => $payload['user_id'],
        'company_id' => $payload['company_id']
      ]);

      return $this->show($userHasCompany->id, $request);
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
      $validator = \Validator::make([
        'id' => $id
      ], [
        'id' => 'integer'
      ]);
  
      if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'code' => 422,
            'data' => $validator->errors()
        ], 422);
      }
      $payload = $request->all();
      $foundCompany = Company::find($payload['company_id']);
      $foundUser = User::find($payload['user_id']);
      
      if(!$foundCompany)
      {
        throw new \Exception("Company doesn't exist.", 404);
      }

      if(!$foundUser)
      {
        throw new \Exception("User doesn't exist.", 404);
      }
      
      $foundUserHasCompany = UserHasCompany::where(['id' => $id])->firstOrFail();

      if(!$foundUserHasCompany)
      {
        throw new \Exception("User has company not found.", 404);
      }
      $foundUserHasCompany = UserHasCompany::where([
        'user_id' => $payload['user_id'],
        'company_id' => $payload['company_id']
      ])->first();
      
      if($foundUserHasCompany)
      {
        throw new \Exception("User has company already exists", 400);
      }

      UserHasCompany::findOrFail($id)->update($payload);
      
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
      $validator = \Validator::make([
        'id' => $id
      ], [
        'id' => 'integer'
      ]);
  
      if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'code' => 422,
            'data' => $validator->errors()
        ], 422);
      }
      UserHasCompany::where(['id' => $id])->firstOrFail()->delete();
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
  public function addUserToCompany($userId, $companyId)
  {
    $userHasCompany = UserHasCompany::updateOrCreate([
      'user_id' => $userId,
      'company_id' => $companyId
    ],
    [
      'user_id' => $userId,
      'company_id' => $companyId
    ]);
    return true; 
  }
}
