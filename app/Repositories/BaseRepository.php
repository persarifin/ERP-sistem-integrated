<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Entities\Submission;
use App\Entities\Item;
use App\Entities\SubmissionCategory;
use App\Entities\UserHasCompany;
use App\Entities\CompanyWallet;
use App\Entities\Company;
use App\Entities\PaymentTransaction;
use App\Entities\PaymentTransactionAttachment;
use App\Entities\User;
use App\Entities\BillingInvoice;
use App\Entities\BillingCounter;
use App\Http\Criterias\CriteriaInterface;
use App\Repositories\RepositoryInterface;
use App\Http\Presenters\PresenterInterface;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Log;

class BaseRepository implements RepositoryInterface
{
    protected $model;
    protected $queryExpense;
    protected $query;
    protected $included;
    protected $presenter;
    protected $modelInstance;
    protected $role;
    protected $password;
    protected $foto;
    protected $total;
    
    public function __construct(string $model)
    {
        $this->reinit($model);
    }

    public function reinit(string $model)
    {
        $this->model = $model;
        $this->modelInstance = null;
        $this->query = null;
        $this->total = 0;
        $this->queryExpense = null;
        $this->included = [];
        $this->password = null;
        $this->foto = null;
    }

    public function getModel()
    {
        if (!$this->modelInstance) {
            $this->modelInstance = app()->make($this->model);
        }

        return $this->modelInstance;
    }

    public function applyCriteria(CriteriaInterface $criteria)
    {
        $this->query = $criteria->apply($this->query);

        return $this;
    }

    public function setPresenter(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;
    }
    
    public function userLogin()
    {
        return Auth::user();
    }

    public function userHasCompany($user, $company)
    {
			$userHasCompany = UserHasCompany::where(['company_id' => $company, 'user_id' => $user])->first();
      if (!$userHasCompany) {
        throw new \Exception("This company is not customer company", 402);
      }
      return true;
    }
    public function searchRoleCompany($role, $company_id)
    {
      $foundRole = Role::where('company_id', $company_id)->where(function($q) use($role){
        if (is_numeric($role)) {
          $q->where('id', $role);
        }
        else {
          $q->where('name', $role.'_'.$company_id);
        }
      })->first();
      if (!$foundRole) {
        throw new \Exception("role not found in this company", 402);
      }

      return $foundRole->id;
    }

    public function validHttpCode($code)
    {
      $validHttpCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing', // WebDAV; RFC 2518
        103 => 'Early Hints', // RFC 8297
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information', // since HTTP/1.1
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content', // RFC 7233
        207 => 'Multi-Status', // WebDAV; RFC 4918
        208 => 'Already Reported', // WebDAV; RFC 5842
        226 => 'IM Used', // RFC 3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // Previously "Moved temporarily"
        303 => 'See Other', // since HTTP/1.1
        304 => 'Not Modified', // RFC 7232
        305 => 'Use Proxy', // since HTTP/1.1
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect', // since HTTP/1.1
        308 => 'Permanent Redirect', // RFC 7538
        400 => 'Bad Request',
        401 => 'Unauthorized', // RFC 7235
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required', // RFC 7235
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed', // RFC 7232
        413 => 'Payload Too Large', // RFC 7231
        414 => 'URI Too Long', // RFC 7231
        415 => 'Unsupported Media Type', // RFC 7231
        416 => 'Range Not Satisfiable', // RFC 7233
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot', // RFC 2324, RFC 7168
        421 => 'Misdirected Request', // RFC 7540
        422 => 'Unprocessable Entity', // WebDAV; RFC 4918
        423 => 'Locked', // WebDAV; RFC 4918
        424 => 'Failed Dependency', // WebDAV; RFC 4918
        425 => 'Too Early', // RFC 8470
        426 => 'Upgrade Required',
        428 => 'Precondition Required', // RFC 6585
        429 => 'Too Many Requests', // RFC 6585
        431 => 'Request Header Fields Too Large', // RFC 6585
        451 => 'Unavailable For Legal Reasons', // RFC 7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates', // RFC 2295
        507 => 'Insufficient Storage', // WebDAV; RFC 4918
        508 => 'Loop Detected', // WebDAV; RFC 5842
        510 => 'Not Extended', // RFC 2774
        511 => 'Network Authentication Required', // RFC 6585
      ];

      if(in_array($code, array_keys($validHttpCodes)))
      {
        return true;
      }
      return false;
    }

    public function havingRole($user,$role)
    {
      if($user->hasRole("super_enterprise")){
        return false;
      }
      $roles = $user->roles->where('custom_name', $role)->where('company_id', $user->company_id)->first();
      return !$roles ? false : true;
    }
    public function permissionReadSubmission($request, $param)
    {
      if($request->filter && is_array($request->input('filter'))) {
        foreach ($request->input('filter') as $name => $criteria) {
          if (!is_array($criteria)) {
            if ($param === 'all') {
              return $this->initSearch($name, 'is', $criteria);
            }
            else {
              return $this->initOtherSearch($name, 'is', $criteria);
            }
            continue;
          }
          else {
            foreach ($criteria as $operator => $value) {
              if ($param === 'all') {
                return $this->initSearch($name, 'is', $criteria);
              }
              else {
                return $this->initOtherSearch($name, 'is', $criteria);  
              }
            }
          }
        }
      }
      else {
        if ($this->userLogin()->hasRole('super_enterprise')) {
          return true;
        }
        throw new \Exception("Please specify the submission type", 400); 
      }
    }
    public function initSearch($name, $operator, $value)
    {
      if ($name == 'submission_category.submission_type' && $operator == 'is' && $value == "INCOME") {
        return $this->roleHasPermission('Read All Submission INCOME');
      }
      elseif ($name == 'submission_category.submission_type' && $operator == 'is' && $value == "EXPENSE") {
        return $this->roleHasPermission('Read All Submission EXPENSE');
      } 
      else {
        return true;
      }
    }
    public function initOtherSearch($name, $operator, $value)
    {
      if ($name == 'submission_category.submission_type' && $operator == 'is' && $value == "INCOME") {
        return $this->roleHasPermission('Read Submission INCOME');
      }
      elseif ($name == 'submission_category.submission_type' && $operator == 'is' && $value == "EXPENSE") {
        return $this->roleHasPermission('Read Submission EXPENSE');
      } 
      else {
        return true;
      }
    }
    public function readDataByRole($query, $permissionResult)
    {
      $userIdSubmission=[];
      $role = $this->userLogin()->roles->where('company_id', $this->userLogin()->company_id)->first();
      $permissions = $role->permissions()->get()->pluck('name');
      if (!$permissions) {
        return false;
      }
      foreach ($permissions as $permission) {
        $text = preg_replace('/\W\w+\s*(\W*)$/', '$1', $permission);
        if ($text == $permissionResult) {
          $foundRole = explode(' ', $permission);
          $role = array_pop($foundRole);
          $userId = User::whereHas("roles", function($q)use($role){ 
            $q->where("name", $role); 
          })->where('company_id', $this->userLogin()->company_id)->get();
          $userIdSubmission[] = $this->userLogin()->id;
          foreach ($userId as $id) {
            $userIdSubmission[] = $id->id;
          }
        }
        else {
          return false;
        }
      }
      $query->whereIn('user_id', $userIdSubmission);
      if ($query->get()) {
        return $query;
      }
      else {
        return false;
      }
    }

    public function updateDataByRole($query, $permissionResult)
    {
      $userIdSubmission=[];
      $role = $this->userLogin()->roles->where('company_id', $this->userLogin()->company_id)->first();
      $permissions = $role->permissions()->get()->pluck('name');
      if (!$permissions) {
        return false;
      }
      foreach ($permissions as $permission) {
        $text = preg_replace('/\W\w+\s*(\W*)$/', '$1', $permission);
        if ($text == $permissionResult) {
          $foundRole = explode(' ', $permission);
          $role = array_pop($foundRole);
          $userId = User::whereHas("roles", function($q)use($role){ 
            $q->where("name", $role); 
          })->where('company_id', $this->userLogin()->company_id)->get();
          $userIdSubmission[] = $this->userLogin()->id;
          foreach ($userId as $id) {
            $userIdSubmission[] = $id->id;
          }
        }
        else {
          return false;
        }
      }
      $query->whereIn('user_id', $userIdSubmission);
      if ($query->first()) {
        return $query;
      }
      else {
        return false;
      }
    }
    public function deleteDataByRole($query, $permissionResult)
    {
      $userIdSubmission=[];
      $role = $this->userLogin()->roles->where('company_id', $this->userLogin()->company_id)->first();
      $permissions = $role->permissions()->get()->pluck('name');
      if (!$permissions) {
        return false;
      }
      foreach ($permissions as $permission) {
        $text = preg_replace('/\W\w+\s*(\W*)$/', '$1', $permission);
        if ($text == $permissionResult) {
          $foundRole = explode(' ', $permission);
          $role = array_pop($foundRole);
          $userId = User::whereHas("roles", function($q)use($role){ 
            $q->where("name", $role); 
          })->where('company_id', $this->userLogin()->company_id)->get();
          $userIdSubmission[] = $this->userLogin()->id;
          foreach ($userId as $id) {
            $userIdSubmission[] = $id->id;
          }
        }
        else {
          return false;
        }
      }
      $query->whereIn('user_id', $userIdSubmission);
      if ($query->first()) {
        return $query;
      }
      else {
        return false;
      }
    }
    public function getToken($user)
    {
      return [
        'access_token' => $user->createToken('MyApp')->accessToken,
        'token_type' => 'Bearer',
        'expires_in' => 3600
      ];
    }
    public function roleHasPermission($permission)
    {
      $role = $this->definedRole($this->userLogin());
      if (!$this->approvedCompanyBilling()) {
		    throw new \Exception("This company not accept the billing payments", 400);
      }
      if($this->userLogin()->hasRole("super_enterprise")){
        return true;
      }
            
      return $role->hasPermissionTo($permission);
    }
    public function approvedCompanyBilling()
    {
      if ($this->userLogin()->hasRole('super_enterprise')) {
        return true;
      }
     $invoice =  BillingInvoice::where(['company_id' => $this->userLogin()->company_id])->first();
      if ($invoice) {
        return $invoice->is_approved == 1;
      }
      else {
        return false;
      }
    }


    public function definedRole($user)
    {
      if ($user->hasRole('super_enterprise')) {
        return true;
      }
      $userHasCompany = UserHasCompany::where(['user_id' => $user->id, 'company_id' => $user->company_id])->first();
      $role = $user->roles->where('company_id', $user->company_id)->first();
      if ($userHasCompany->reseller == true) {
          $superAdmin = $user->roles->where('custom_name', 'super_admin')->first();
          if ($superAdmin) {
            $role = $superAdmin;
          }
      }
      else {
          $enterprise = $user->roles->where('custom_name','enterprise')->first();
          if ($enterprise) {
            $role = $enterprise;
          }
      } 
      // if(!$role){
      //     throw new \Exception("You not have the right permission.", 403);
      // }
     return $this->role = $role;
    }

    public function createSessionCompanyId($companyId)
    {
      $newCompanyId = "";
      if($this->userLogin()->hasRole('super_enterprise')){
        $newCompanyId = $companyId;
      }else{  
        $newCompanyId = $this->userLogin()->company_id;
      };
      session(['companyId' => $newCompanyId]);
      return true;
    }

    public function getCompanyId()
    {
      $companyId = session('companyId');
      return $companyId ? (int)$companyId : 0;
    }
    public function setPassword($password)
    {
      $this->password = $password ? $password : 123456;
    }
    public function copySubmissionToVendorOrCustomer($id) 
	  {
      $submission = Submission::find($id);       
      $category = SubmissionCategory::find($submission->category_id);
      $partner = User::find($submission->partner_id);
      $foundCompanyPartner = Company::find($partner->company_id);
      // check if company not exists or company partner === company current user
      if(!$foundCompanyPartner || $foundCompanyPartner->id === $this->userLogin()->company_id)
      {
          return false;
      }
      $submissionVendor = Submission::where('submission_name', 'ILIKE',"%(copy " . $submission->reference_doc_number .")%")->first();
      if (isset($submissionVendor)) {
        $this->deleteSubmission($submissionVendor->id);
      }
      if (strpos($submission->submission_name, '#')) {
      $submissionCategoryPartner = SubmissionCategory::firstOrCreate([
        "category_name" => "Payment Billing",
        "company_id" => $foundCompanyPartner->id,
        "submission_type" => str_replace($category->submission_type, "INCOME", "EXPENSE"),
        'maximum' => 0
      ]);
      }else{
        $submissionCategoryPartner = SubmissionCategory::firstOrCreate([
          "category_name" => "UNCATEGORIZED",
          "company_id" => $foundCompanyPartner->id,
          "submission_type" => str_replace($category->submission_type, "INCOME", "EXPENSE")
        ]);
      }
      $number = Submission::where(['company_id' => $foundCompanyPartner->id])->whereHas('submission_category', function($q) use($category){
				$q->where('submission_categories.submission_type', str_replace($category->submission_type, "INCOME", "EXPENSE"));
			})->count();
      $copySubmission = [
        "submission_name" => $submission->submission_name . " (copy " . $submission->reference_doc_number .")",
        "description"     => $submission->description,
        "status"          => $submission->status,
        "amount"          => $submission->amount,
        "date"            => $submission->date,
        "due_date"        => $submission->due_date,
        "user_id"         => $partner->id,
        "partner_id"      => $submission->user_id,
        "company_id"      => $foundCompanyPartner->id,
        "category_id"     => $submissionCategoryPartner->id,
        "fullfilment"     => $submission->fullfilment,
        "reference_doc_number" => str_replace($category->submission_type, "INCOME", "EXPENSE")  . $foundCompanyPartner->id . "-" . $number++. date('Ymd-His'),
        "created_at"      => $submission->created_at,
        "updated_at"      => $submission->updated_at
      ];
      $copiedSubmission = Submission::create($copySubmission);
      $items = Item::where('submission_id', $id)->get();
      if(isset($items)){
        $this->copyItemToVendorOrCustomer($items, $copiedSubmission, $partner, $category);
      }
      $paymentTransaction = PaymentTransaction::where('submission_id', $submission->id)->first();
      if (isset($paymentTransaction)) {
        $this->copyTransactionToVendorOrCustomer($submission->id, $copiedSubmission, $partner);
      }
      return true;
	  }
    
  	public function copyItemToVendorOrCustomer($items, $submission, $partner, $category)
    {      
      foreach ($items as $item){
        $newItem['item_name'] = $item['item_name'];
        $newItem['product_id'] = 0;
        if(str_replace($category->submission_type, "INCOME", "EXPENSE") === 'INCOME'){
          $newItem['selling_price'] = $item['buying_price'];
        }else if(str_replace($category->submission_type, "INCOME", "EXPENSE") === 'EXPENSE'){
          $newItem['buying_price'] = $item['selling_price'];
        }
        $newItem['submission_id'] = $submission->id;
        $newItem['quantity'] = $item['quantity'];
        $newItem['tax'] = $item['tax'];
        $newItem['discount'] = $item['discount'];
        $newItem['company_id'] = $partner->company_id;
        
        $itemVendor = Item::updateOrCreate($newItem);
        $itemAttacment = \App\Entities\ItemAttachment::where('item_id', $item->id)->get();
        if (!$itemAttacment) {
          return true;
        }
        foreach ($itemAttacment as $attachment) {
          \App\Entities\ItemAttachment::create([
            'item_id'       => $itemVendor->id,
            'company_id'    => $itemVendor->company_id,
            'file_name'     => $itemAttacment->file_name,
            'file_location' => $itemAttacment->file_location
          ]);
        }
      }
      return true;
    }
    public function copyTransactionToVendorOrCustomer($submissionId, $submissionCopy, $partner)
    {
      $paymentTransaction = PaymentTransaction::where('submission_id', $submissionId)->with('company_wallet')->get();
      foreach ($paymentTransaction as $pay) {
        $walletVendor = CompanyWallet::firstOrCreate([
          'wallet_name' => $pay->company_wallet->wallet_name,
          'company_id'  => $submissionCopy->company_id
        ]);
        $paymentVendor = PaymentTransaction::create([
          'amount'            => $pay->amount,
          'transaction_date'  => $pay->transaction_date,
          'company_wallet_id' => $walletVendor->id,
          'user_id'           => $partner->id,
          'company_id'        => $partner->company_id,
          'submission_id'     => $submissionCopy->id
        ]);
        $paymentAttachment = PaymentTransactionAttachment::where('transaction_id', $pay->id)->get();
        if (!$paymentAttachment) {
          return true;
        }
        foreach ($paymentAttachment as $attachment) {
          PaymentTransactionAttachment::create([
            'transaction_id'  => $paymentVendor->id,
            'company_id'      => $paymentVendor->company_id,
            'file_name'       => $attachment->file_name,
            'file_location'   => $attachment->file_location
          ]);
        }
      }
      return true;
    }
    public function deleteSubmission($id)
    {
      $submissionVendor = Submission::find($id);
      $items = Item::where('submission_id', $id)->get();
      if (isset($items)) {
        foreach ($items as $deleteItem) {
          $deleteItem->product_schedule()->delete();
          $deleteItem->item_attachment()->delete();
          $deleteItem->delete();
        }
      }
      $payment = PaymentTransaction::where('submission_id', $id)->get();
      if (isset($payment)) {
        foreach ($payment as $transaction) {
          $transaction->payment_transaction_attachment()->delete();
          $transaction->delete();
        }
      }
      $submissionVendor->delete();
      return true;
    }
}
