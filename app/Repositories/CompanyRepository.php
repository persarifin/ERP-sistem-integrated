<?php

namespace App\Repositories;

use App\Entities\Company;
use App\Entities\User;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Repositories\CompanyAttachmentRepository;
use App\Jobs\ActivateCompanyJob;
use App\Jobs\DeactivateCompanyJob;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Log;

class CompanyRepository extends BaseRepository
{
    public function __construct(CompanyAttachmentRepository $companyAttachmentRepository)
    {
        parent::__construct(Company::class);
        $this->companyAttachmentRepository = $companyAttachmentRepository;
    }

    protected $defaultCompany = [
        'subdistrict' => '',
        'city' => '',
        'province' => '',
        'postal_code' => '',
        'country' => '',
        'bio' => '',
        'tagline' => '',
        'sub_tagline' => '',
        'vision' => '',
        'mission' => '',
        'work_culture' => '',
        'working_space' => '',

    ];

    public function browse(Request $request)
    {
        try {
            if($this->roleHasPermission("Read All Companies"))
            {
                $this->query = $this->getModel();
            }elseif ($this->roleHasPermission("Read Companies") && $this->userLogin()->hasRole('super_admin')) {
                $this->query = $this->getModel()->whereIn('id',  $this->userLogin()->user_has_company->pluck('id'));
            }
            else {
                throw new \Exception("User does not have the right permission.", 403);
            }
            $this->applyCriteria(new SearchCriteria($request));
            $presenter = new DataPresenter(CompanyResource::class, $request);

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

    public function store($request)
    {
        try {
            if(!$this->roleHasPermission("Create Companies"))
            {
              throw new \Exception("User does not have the right permission.", 403);
            }
            $payload = $request->all();
            $company = Company::create($payload);
            return $this->showRender($company->id, $request);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }
    // create company without permission
    public function createCompany($payload)
    {
        $company = Company::create(array_merge($payload, $this->defaultCompany));
        return $company;
    }

    public function update($id, Request $request)
    {
        try {
            if($this->roleHasPermission("Update All Companies"))
            {
                $company = Company::where(['id' => $id])->firstOrFail();
            }
            elseif($this->roleHasPermission("Update Companies"))
            {
                $company = Company::where(['id' => $this->userLogin()->company_id])->firstOrFail();
            }
            else {
                throw new \Exception("User does not have the right permission.", 403);
            }
          
            $company = $company->update($request->all());
            
            if(isset($request->company_attachments) && count($request->company_attachments) > 0)
            {
                foreach($request->company_attachments as $key => $attachment)
                {
                if($request->hasFile('company_attachments.'.$key.'.file'))
                { 
                    $attachment['company_id'] = $id;
                    $this->companyAttachmentRepository->storeCompanyAttachment($attachment);
                }
                }
            }
            return $this->showRender($id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }

    public function destroy($id)
    {
        try {
            if(!$this->roleHasPermission("Delete Companies"))
            {
              throw new \Exception("User does not have the right permission.", 403);
            }
            Company::findOrFail($id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'data has been deleted',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function toggleStatusCompany($id, $request)
    {
        try {
            if(!$this->roleHasPermission("Activation Companies"))
            {
              throw new \Exception("User does not have the right permission.", 403);
            }
            $validator = \Validator::make($request->all(), [
                'activated' => 'required|boolean',
                'amount' => ['integer', Rule::requiredIf($request->activated == true)],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'code' => 422,
                    'data' => $validator->errors(),
                ], 422);
            }

            $payload = $request->all();
            $foundCompany = Company::find($id);

            if (!$foundCompany) {
                throw new Error("Company not found!", 404);
            }

            if ($payload["activated"]) {
                dispatch(new ActivateCompanyJob($id, $payload));
                $foundCompany->email_verified_at = date('Y-m-d H:i:s');
            } else {
                $foundCompany->email_verified_at = null;
            }
            $foundCompany->save();
            
            return response()->json([
                'success' => true,
                'message' => 'data has been updatedd',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function showRender($id, $request)
    {
        $this->query = $this->getModel()->where(['id' => $id])->firstOrFail();
        $presenter = new DataPresenter(CompanyResource::class, $request);

        return $presenter->render($this->query);
    }
    public function show($id, Request $request)
    {
        try {
            if($this->roleHasPermission("Read All Companies")){
                $this->query = $this->getModel()->where(['id' => $id])->firstOrFail();
            }
            else{
                $this->query = $this->getModel()->where(['id' => $this->userLogin()->company_id])->first();
                if (!$this->query) {
                    throw new \Exception("User does not have the right permission.", 403);
                }
            }
            $presenter = new DataPresenter(CompanyResource::class, $request);

            return $presenter->render($this->query);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }
}
