<?php

namespace App\Repositories;

use App\Entities\Company;
use App\Entities\User;
use App\Entities\UserAttachment;
use App\Entities\UserHasCompany;
use App\Entities\BillingCounter;
use App\Entities\BillingInvoice;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Jobs\ActivateCompanyJob;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserAttachmentRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Services\GoogleCloud\Spaces as GoogleSpaces;

class UserRepository extends BaseRepository
{
    public function __construct(
        RoleRepository $roleRepository,
        CompanyRepository $companyRepository,
        UserHasCompanyRepository $userHasCompanyRepository,
        PermissionRepository $permissionRepository,
        BillingInvoiceRepository $billingInvoiceRepository,
        UserAttachmentRepository $userAttachment,
        GoogleSpaces $GoogleSpaces
    ) {
        parent::__construct(User::class);
        $this->GoogleSpaces = $GoogleSpaces;
        $this->companyRepository = $companyRepository;
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->userHasCompanyRepository = $userHasCompanyRepository;
        $this->billingInvoiceRepository = $billingInvoiceRepository;
        $this->userAttachment = $userAttachment;
    }

    public function login(LoginRequest $request)
    {
        try {
            $column = [filter_var($request->email, FILTER_VALIDATE_EMAIL) ?
                'email' : (is_numeric($request->email) ? 'phone' : 'username')];
            $data['user'] = $this->getModel()->where($column[0], $request['email'])->with('user_has_company')->first();

            if ($data['user'] && \Hash::check($request->password, $data['user']->password)) {
                if ($data['user']->hasRole('super_enterprise')) {
                    $this->role = $data['user']->roles->first();
                    User::findOrFail($data['user']->id)->update([
                        'company_id' => 0,
                    ]);
                } else {
                    $interface = $this->loginInterface($request->interface);
                    $this->companyUpdate($data['user']);
                    $this->definedRole($data['user']);
                    if (!$this->role->hasPermissionTo('Access Interface ' . $interface)) {
                        throw new \Exception("User does not have the right permission to access this interface", 403);
                    }
                    if ($data['user']->company_id != 0) {
                        $this->calculationBillingCounter($data['user']);
                    }
                }
                $data['permissions'] = $this->role->permissions()->pluck('name');

                $data['token'] = $this->getToken($data['user']);
                return response()->json([
                    'success' => true,
                    'data' => $data,
                ], 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 406);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    protected function calculationBillingCounter($user)
    {
        if ($user->hasRole('super_admin')) {
            $userHasCompany = UserHasCompany::where(['user_id' => $user->id, 'company_id' => $user->company_id])->first();
            if ($userHasCompany->reseller == 1) {
                return false;
            }
        }
        $billingInvoice = BillingInvoice::where(['company_id' => $user->company_id])->whereMonth('date', date('m'))->whereYear('date', date('Y'))->first();
        if (!$billingInvoice) {
            $billingInvoice = BillingInvoice::create([
                'invoice_name' => 'Billing Company ' . Company::find($user->company_id)->business_name,
                'amount'    => 1000000,
                'date'      => date('Y-m-01 H:i:s'),
                'is_approved' => true,
                'status'    => "UNPAID",
                'company_id' => $user->company_id
            ]);
        }
        $lastCounter = BillingCounter::where(['company_id' => $billingInvoice->company_id])->orderBy('id', 'desc')->first();
        if (isset($lastCounter)) {
            if (((int)date('m') - (int)date('m', strtotime($lastCounter->date))) > 0) {
                $costsLastMonth = ($billingInvoice->amount / (float)date('t', strtotime($lastCounter->date))) * ((int)date('t', strtotime($lastCounter->date)) - (int)date('d', strtotime($lastCounter->date)));
                BillingCounter::create([
                    'counter_name'  => 'Login By ' . $user->roles->where('company_id', $user->company_id)->first()->custom_name . ' ' . $user->full_name,
                    'amount'        => $costsLastMonth,
                    'date'          => date('Y-m-t H:i:s', strtotime($lastCounter->date)),
                    'company_id'    => $user->company_id
                ]);

                $lastCounter = BillingCounter::create([
                    'counter_name'  => 'Login By ' . $user->roles->where('company_id', $user->company_id)->first()->custom_name . ' ' . $user->full_name,
                    'amount'        => ($billingInvoice->amount / (float)date('t')),
                    'date'          => date('Y-m-01 H:i:s'),
                    'company_id'    => $user->company_id
                ]);
            }
        } else {
            $lastCounter = BillingCounter::create([
                'counter_name'  => 'Login By ' . $user->roles->where('company_id', $user->company_id)->first()->custom_name . ' ' . $user->full_name,
                'amount'        => ($billingInvoice->amount / (float)date('t')),
                'date'          => date('Y-m-01 H:i:s'),
                'company_id'    => $user->company_id
            ]);
        }
        $timeRange = (int)date('Ymd') - (int)date('Ymd', strtotime($lastCounter->date));
        if ($timeRange < 1) {
            return false;
        }
        $totalCost = (float)($billingInvoice->amount / (int)date('t', strtotime($lastCounter->date))) * (int)$timeRange;
        BillingCounter::create([
            'counter_name'  => 'Login By ' . $user->roles->where('company_id', $user->company_id)->first()->custom_name . ' ' . $user->full_name,
            'amount'        => $totalCost,
            'date'          => date('Y-m-d H:i:s'),
            'company_id'    => $user->company_id
        ]);
        return true;
    }
    protected function loginInterface($interface)
    {
        $webCompany = hash('sha256', 'WEB COMPANY' . date('Y-m-d H:i'));
        $webStore = hash('sha256', 'WEB P.O.S' . date('Y-m-d H:i'));
        $mobileExternal = hash('sha256', 'MOBILE' . date('Y-m-d H:i'));
        $mobileParking = hash('sha256', 'MOBILE PARKING' . date('Y-m-d H:i'));
        $NoWhere = hash('sha256', 'NOWHERE' . date('Y-m-d H:i'));
        if ($interface == $webCompany) {
            $interface = 'WEB COMPANY';
        } elseif ($interface == $webStore) {
            $interface = 'WEB P.O.S';
        } elseif ($interface == $mobileExternal) {
            $interface = ' MOBILE';
        } elseif ($interface == $mobileParking) {
            $interface = 'MOBILE PARKING';
        } elseif ($interface == $NoWhere) {
            $interface = 'NOWHERE';
        } else {
            throw new \Exception("Please try again", 422);
        }
        return $interface;
    }
    protected function companyUpdate($user)
    {
        if ($user->activated == 0) {
            throw new \Exception("Account not found, please enter another valid account address", 404);
        }
        if ($user->hasRole('super_admin')) {
            $selfCompany = UserHasCompany::where(['user_id' => $user->id, 'reseller' => 0, 'company_id' => $user->company_id])->first();
            if (isset($selfCompany)) {
                $user->company_id = $selfCompany->company_id;
                $user->save();
            } else {
                $selfCompany = UserHasCompany::where(['user_id' => $user->id, 'reseller' => 0])->first();
                if (isset($selfCompany)) {
                    $user->company_id = $selfCompany->company_id;
                } else {
                    $defaultCompany = $user->roles->where('custom_name', 'enterprise')->first();
                    $user->company_id = $defaultCompany->company_id;
                }
                $user->save();
            }
        }
        $userHasCompany = UserHasCompany::where(['user_id' => $user->id, 'company_id' => $user->company_id])
            ->whereHas('company', function ($query) {
                $query->where('companies.email_verified_at', '!=', null)
                    ->orWhere('companies.phone_verified_at', '!=', null);
            })->first();
        if (empty($userHasCompany)) {
            $otherCompanyUser = UserHasCompany::where('user_id', $user->id)->where('company_id', '!=', $user->company_id)
                ->whereHas('company', function ($query) {
                    $query->where('companies.email_verified_at', '!=', null)
                        ->orWhere('companies.phone_verified_at', '!=', null);
                })->first();
            if (isset($otherCompanyUser)) {
                $user->company_id = $otherCompanyUser->company_id;
                $user->save();
            } else {
                $companyDisabled = UserHasCompany::where('user_id', $user->id)->whereHas('company', function ($query) {
                    $query->where('companies.email_verified_at', null)
                        ->orWhere('companies.phone_verified_at', null);
                })->first();
                if (isset($companyDisabled)) {
                    $user->company_id = $companyDisabled->company_id;
                    $user->save();
                    throw new \Exception("Company has not been activated or deactivated, please contact your Entity or your Owner", 406);
                } else {
                    $user->company_id = 0;
                    $user->save();
                    $user->syncRoles('unverified');
                    throw new \Exception("You don't have a Company or terminated from your Company and revoke the access to this interface", 406);
                }
            }
        }
        return true;
    }

    public function browse(Request $request)
    {
        if ($this->roleHasPermission("Read All Users")) {
            $this->query = $this->getModel()->with(['roles']);
        } elseif ($this->roleHasPermission("Read Users") && $this->userLogin()->hasRole('super_admin')) {
            $companyId = $this->userLogin()->user_has_company->pluck('id');
            $this->query = $this->getModel()->with(['roles' => function ($q) use ($companyId) {
                $q->whereIn('company_id', $companyId);
            }])->whereIn('company_id', $companyId);
        } else {
            throw new \Exception("User does not have the right permission.", 403);
        }
        $this->applyCriteria(new SearchCriteria($request));
        $presenter = new DataPresenter(UserResource::class, $request);

        return $presenter
            ->preparePager()
            ->renderCollection($this->query);
    }

    public function browseCustomer(Request $request)
    {
        $this->query = User::whereHas('submission_as_customer', function ($q) {
            $q->where('company_id', \Auth::user()->company_id);
        });
        $this->applyCriteria(new SearchCriteria($request));
        $presenter = new DataPresenter(UserResource::class, $request);

        return $presenter
            ->preparePager()
            ->renderCollection($this->query);
    }

    public function browseUserCompany(Request $request)
    {
        try {
            if (!$this->roleHasPermission("Read Users")) {
                throw new \Exception("User does not have the right permission.", 403);
            }
            $this->query = $this->getModel()->whereHas('roles', function ($q) {
                $q->where('roles.company_id', $this->userLogin()->company_id);
            })->with(['roles' => function ($q) {
                $q->where('company_id', $this->userLogin()->company_id);
            }]);

            $this->applyCriteria(new SearchCriteria($request));
            $presenter = new DataPresenter(UserResource::class, $request);

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
    public function landingPageUserCompany($id, $request)
    {
        try {
            $this->query = $this->getModel()->whereHas('roles', function ($q) use ($id) {
                $q->where('roles.company_id', $id);
            })->with(['roles' => function ($q) use ($id) {
                $q->where('company_id', $id);
            }]);

            $this->applyCriteria(new SearchCriteria($request));
            $presenter = new DataPresenter(UserResource::class, $request);

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

    public function showRender($id, Request $request)
    {
        try {
            $this->query = $this->getModel()->where(['id' => $id]);

            $presenter = new DataPresenter(UserResource::class, $request);

            return $presenter->render($this->query);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }
    public function show($id, Request $request)
    {
        try {
            if ($this->roleHasPermission("Read Users")) {
                $this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
            } elseif ($this->userLogin() !== null) {
                $this->query = $this->getModel()->where(['id' => $this->userLogin()->id, 'company_id' => $this->userLogin()->company_id]);
            } else {
                throw new \Exception("User does not have the right permission.", 403);
            }

            $presenter = new DataPresenter(UserResource::class, $request);

            return $presenter->render($this->query);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }

    public function register($request) //email varification
    {
        try {
            $payload = $request->all();
            $payload['email'] = strtolower($payload['email']);
            $payload['activated'] = 0;
            $payload['company_id'] = 0;
            $payload['role_id'] = "unverified";
            $payload['username'] = strtolower($payload['username']);
            $payload['password'] = Hash::make($payload['password']);

            $user = User::create($payload);
            $user->assignRole($payload['role_id']);

            if ($payload['type'] === 'COMPANY') {
                $newCompany = $this->companyRepository->createCompany($payload);
                $payload['company_id'] = $newCompany->id;
                $user->syncRole('enterprise');
            }

            return $this->showRender($user->id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function store($request) //email varification
    {
        try {
            $payload = $request->all();
            if (!$this->roleHasPermission("Create Users")) {
                throw new \Exception('User does not have the right permission.', 403);
            }
            $payload['email'] = strtolower($payload['email']);
            $payload['username'] = strtolower($payload['username']);
            $payload['password'] = Hash::make($payload['password']);
            $payload['activated'] = 0;
            $payload['company_id'] = $this->userLogin()->company_id;
            $payload['role_id'] = $this->searchRoleCompany($payload['role_id'], $this->userLogin()->company_id);
            $user = User::create($payload);

            if ($payload['type'] === 'COMPANY') {
                if ($this->roleHasPermission('Create Companies')) {
                    throw new \Exception('User does not have the right permission to create company, please change type to PERSONAL.', 403);
                }
                $newCompany = $this->companyRepository->createCompany($payload);
                $payload['company_id'] = $newCompany->id;
                UserHasCompany::where('user_id', $user->id)->delete();
                UserHasCompany::create(
                    [
                        'user_id' => $user->id,
                        'company_id' => $payload['company_id'],
                        'reseller' => false
                    ],
                    [
                        'user_id' => $this->userLogin()->id,
                        'company_id' => $payload['company_id'],
                        'reseller' => $payload['reseller'] == 1 ? true : false
                    ]
                );
                $user->syncRole('enterprise');
            } else {
                $roleCompany = $user->roles->where('company_id', $payload['company_id'])->first();
                if (isset($roleCompany)) {
                    $user->removeRole($roleCompany->id);
                }
                UserHasCompany::UpdateOrCreate([
                    'user_id' => $user->id,
                    'company_id' => $payload['company_id'],
                    'reseller' => false
                ]);
                $role = Role::find($payload['role_id']);
                if ($role && $role->custom_name === 'enterprise') {
                    throw new \Exception('User does not have the right permission to create user with role enterprise.', 403);
                }
                $user->assignRole($payload['role_id']);
            }

            return $this->showRender($user->id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update($id, $request) //email varification
    {
        try {
            $user = User::where(['id' => $id, 'company_id' => $this->userLogin()->company_id])->first();
            $companyId = $this->userLogin()->company_id;
            if ($this->roleHasPermission("Update Users")) {
                $companyUser = UserHasCompany::where(['user_id' => $user->id, 'company_id' => $request->company_id])->first();
                if (isset($companyUser)) {
                    $companyId = $companyUser->company_id;
                } else {
                    throw new \Exception('User not in this company.', 403);
                }
            } else {
                throw new \Exception('User does not have the right permission.', 403);
            }
            $payload = $request->all();
            $user->username = strtolower($payload['username']);
            $user->full_name = $payload['full_name'];
            $payload['role_id'] = $this->searchRoleCompany($payload['role_id'], $companyId);

            if ($payload['type'] === 'COMPANY') {
                if ($this->roleHasPermission('Create Companies')) {
                    throw new \Exception('User does not have the right permission to create company, please change type to PERSONAL.', 403);
                }
                $newCompany = $this->companyRepository->createCompany($payload);
                $payload['company_id'] = $newCompany->id;
                UserHasCompany::where('user_id', $id)->delete();
                UserHasCompany::create(
                    [
                        'user_id' => $user->id,
                        'company_id' => $payload['company_id'],
                        'reseller' => false
                    ],
                    [
                        'user_id' => $this->userLogin()->id,
                        'company_id' => $payload['company_id'],
                        'reseller' => $payload['reseller'] == 1 ? true : false
                    ]
                );
                $user->syncRole('enterprise');
            } else {
                $roleCompany = $user->roles->where('company_id', $companyId)->first();
                $user->removeRole($roleCompany->id);
                $user->assignRole($payload['role_id']);
            }

            $user->save();
            return $this->showRender($user->id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }

    public function switchCompany($request)
    {
        try {
            $payload = $request->only('company_id');
            $company = Company::find($payload['company_id']);
            $user = User::find($this->userLogin()->id);
            if ($this->userLogin()->hasRole('super_enterprise')) {
                $user->company_id = $payload['company_id'];
                $user->save();
            } else {
                $foundUserHasCompanies = UserHasCompany::where([
                    'user_id' => $this->userLogin()->id,
                    'company_id' => $payload['company_id'],
                ])->first();
                if (empty($foundUserHasCompanies)) {
                    throw new \Exception("This is not your company.", 404);
                }
                $user->company_id = $payload['company_id'];
                $user->save();
                $interface = $this->loginInterface($request->interface);
                $this->definedRole($user);
                if (!$this->role->hasPermissionTo('Access Interface ' . $interface)) {
                    $this->companyUpdate($user);
                    throw new \Exception("User does not have the right permission to access this interface", 403);
                }
                if ($request->company_id == 0 && $foundUserHasCompanies->reseller == 0) {
                    $this->calculationBillingCounter($user);
                }
            }
            $data["user"] = $user;
            $data['token'] = $this->getToken($user);
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }
    public function changePassword($request)
    {
        try {
            if ($request->isMethod('POST')) {
                $user = User::find($this->userLogin()->id);
            } elseif ($request->isMethod('PUT')) {
                $user = User::find($request->email);
            }
            $payload = $request->only('password');
            $user->password = Hash::make($payload['password']);
            $user->save();

            $this->query = $this->getModel()->where('id', $user->id);
            $presenter = new DataPresenter(UserResource::class, $request);
            return $presenter->render($this->query);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function updateProfile($request) //email varification
    {
        try {
            $user = User::find($this->userLogin()->id);
            $payload = $request->all();
            $payload['email'] = strtolower($payload['email']);
            $payload['username'] = strtolower($payload['username']);
            $payload['password'] = $user->password;
            if ($this->havingRole($this->userLogin(), 'enterprise')) {
                $companyEnterprise = $this->userLogin()->roles->where('custom_name', 'enterprise')->first()->company_id;
                $company = Company::find($companyEnterprise);
                $company->email = $payload['email'];
                $company->phone = $payload['phone'];
                $company->save();
            }
            User::findOrFail($this->userLogin()->id)->update($payload);

            if (isset($payload['attachments'])) {
                foreach ($payload['attachments'] as $image) {
                    UserAttachment::where(['attachment_type' => $image['attachment_type'], 'user_id' => $this->userLogin()->id])->delete();
                    if ($image['file']) {
                        $attachment['attachment_type'] = $image['attachment_type'];
                        $attachment['file']              = $image['file'];
                        $attachment['user_id']         = $this->userLogin()->id;
                        $this->userAttachment->create($attachment);
                    }
                }
            }
            return $this->showRender($this->userLogin()->id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            if (!$this->roleHasPermission("Delete Users")) {
                throw new \Exception('User does not have the right permission.', 403);
            }
            User::where(['id' => $id, 'company_id' => $this->userLogin()->company_id])->firstOrFail()->delete();
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

    public function searchUser(Request $request)
    {
        try {
            $results = User::where(function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->whereRaw('lower(email) =?', strtolower($request->q));
                })->orWhere(function ($query) use ($request) {
                    $query->whereRaw('lower(username) =?', strtolower($request->q));
                })->orWhere('phone', $request->q);
            })->with(['roles', 'user_has_company'])->first();
            return $results;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function searchCustomer($request)
    {
        try {
            $results = User::where(function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->whereRaw('lower(email) =?', strtolower($request->q));
                })->orWhere(function ($query) use ($request) {
                    $query->whereRaw('lower(username) =?', strtolower($request->q));
                })->orWhere('phone', $request->q);
            })->with(['roles', 'user_has_company'])->first();
            if (!$results) {
                throw new \Exception('customer not found.', 403);
            }
            return $this->showRender($results->id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function userControl($id, $request)
    {
        try {
            $payload = $request->all();
            $user = User::findOrFail($id);
            $user->activated = $user->activated === 0 ? 1 : 0;
            $user->email_verified_at = $user->email_verified_at === null ? date('Y-m-d H:i:s') : null;
            $user->save();
            $company = Company::find($user->company_id);
            if (isset($company->email_verified_at)) {
                return $this->showRender($user->id, $request);
            }
            if ($user->hasRole('enterprise') || $this->havingRole($user, 'enterprise',)) {
                dispatch(new ActivateCompanyJob($user->company_id, $payload));
                $company->email_verified_at = date('Y-m-d H:i:s');
                $user->syncRole('enterprise_' . $user->company_id);
            } else {
                $role = $user->roles->where(function ($q) use ($company) {
                    $q->where('company_id', $company->id);
                });
                if (!$role) {
                    $user->assignRole('unverified');
                }
                $role->givePermissionTo($this->privatePermissions);
            }
            return $this->showRender($user->id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function updateRoleUser($request)
    {
        try {
            if (!$this->roleHasPermission("Update User role")) {
                throw new \Exception('User does not have the right permission.', 403);
            }
            $user = User::findOrFail($request->user_id);
            $role = $user->roles->where('company_id', $this->userLogin()->company_id)->first();
            $roles = Role::where(['id' => $request->role_id, 'company_id' => $this->userLogin()->company_id])->firstOrFail();
            if (in_array($roles->custom_name, array('enterprise', 'unverified', 'super_admin', 'super_enterprise'))) {
                throw new \Exception('User not allowed to update user with this role ', 403);
            }
            if (isset($role)) {
                $user->removeRole($role);
            }
            $user->assignRole($roles->id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function assignUserToCompany($request)
    {
        try {
            if (!$this->roleHasPermission("Assign User To Company")) {
                throw new \Exception('User does not have the right permission.', 403);
            }
            $foundUser = User::with('roles')->findOrFail($request->user_id);
            $role = Role::where(['id' => $request->role_id, 'company_id' => $this->userLogin()->company_id])->firstOrFail();
            if (in_array($role->custom_name, ['enterprise', 'unverified', 'super_admin', 'super_enterprise'])) {
                throw new \Exception('User not allowed to assign user with this role ', 403);
            }
            UserHasCompany::create([
                'user_id' => $foundUser->id,
                'company_id' => $this->userlogin()->company_id,
                'reseller' => false
            ]);
            $foundUser->assignRole($role->id);

            return $this->showRender($foundUser->id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function deleteUserFromCompany($id)
    {
        try {
            if (!$this->roleHasPermission("Delete User From Company")) {
                throw new \Exception('User does not have the right permission.', 403);
            }
            $role = Role::where('company_id', $this->userLogin()->company_id)->whereHas('users', function ($q) use ($id) {
                $q->where('id', $id);
            })->first();
            if (in_array($role->custom_name, ['enterprise', 'unverified', 'super_admin', 'super_enterprise'])) {
                throw new \Exception('User not allowed to terminated this user ', 403);
            }
            if ($role) {
                User::find($id)->removeRole($role);
            }
            UserHasCompany::where(['user_id' => $id, 'company_id' => $this->userLogin()->company_id])->delete();
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
    public function companyAsReseller($id)
    {
        try {
            if (!$this->roleHasPermission("Assign Company As Reseller")) {
                throw new \Exception('User does not have the right permission.', 403);
            }
            $user = User::whereHas('roles', function ($q) use ($id) {
                $q->where(['name' => 'enterprise_' . $id, 'company_id' => $id]);
            })->first();
            if (!$user) {
                throw new \Exception("The company don't have a user with role enterprise, please add first", 403);
            }
            $user->assignRole('super_admin');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function selectionPlayer($request)
    {
        try {
            $foundUser = User::where('phone', $request->phone)->first();

            if ($foundUser) {
                $foundUser->full_name = $request->full_name;
                $foundUser->save();
            } else {
                $username = Str::lower(preg_replace('/\s+/', '', $request->full_name)) . rand(1000, 9999);
                $foundUser = User::create([
                    'full_name' => $request->full_name,
                    'username' => $username,
                    'email' => $username . '@email.com',
                    'phone' => $request->phone,
                    'password' => uniqid(),
                    'activated' => false,
                    'company_id' => 0,
                ]);
            }

            $file = $request->file('profileImage');
            $filename = $foundUser->username . '.' . $file->getClientOriginalExtension();

            // Upload to Google Spaces
            $results = $this->GoogleSpaces->upload('selection_player_attachments', $filename, $file);
            $attachment['attachment_type'] = 'PHOTO PROFILE';
            $attachment['file_name'] = $filename;
            $attachment['file_location'] = $results['folder_url'];
            $attachment['user_id'] = $foundUser->id;
            UserAttachment::create($attachment);

            return $this->showRender($foundUser->id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
