<?php

namespace App\Repositories;

use App\Entities\BillingInvoice;
use App\Entities\BillingCounter;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Repositories\Payments\PaymentTransactionAttachmentRepository;
use App\Http\Resources\BillingInvoiceResource;
use App\Entities\Submission;
use App\Entities\SubmissionCategory;
use App\Entities\Company;
use App\Entities\Item;
use App\Entities\PaymentTransaction;
use Illuminate\Http\Request;

class BillingInvoiceRepository extends BaseRepository
{
    public function __construct(PaymentTransactionAttachmentRepository $paymentTransactionAttachmentRepository)
    {
        parent::__construct(BillingInvoice::class);
        $this->paymentAttachmentRepository = $paymentTransactionAttachmentRepository;
    }

    public function browse(Request $request)
    {
        try {
            if (!$this->roleHasPermission("Read All Billing Invoices")) {
                return $this->browseByCompany($this->userLogin()->company_id, $request);
            }
            $this->query = $this->getModel();
            $this->applyCriteria(new SearchCriteria($request));
            $presenter = new DataPresenter(BillingInvoiceResource::class, $request);

            return $presenter
                ->preparePager()
                ->renderCollection($this->query);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function browseByCompany($id, Request $request)
    {
        try {
            if (!$this->roleHasPermission("Read Billing Invoices")) {
                throw new \Exception("User does not have the right permission.", 403);
            }
            $this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
            $this->applyCriteria(new SearchCriteria($request));
            $presenter = new DataPresenter(BillingInvoiceResource::class, $request);

            return $presenter
                ->preparePager()
                ->renderCollection($this->query);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show($id, Request $request)
    {
        try {
            if(!$this->roleHasPermission("Read Billing Invoices"))
            {
              throw new \Exception("User does not have the right permission.", 403);
            }
            $this->query = $this->getModel()->where('id', $id);
            $presenter = new DataPresenter(BillingInvoiceResource::class, $request);

            return $presenter->render($this->query);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function showByCompany($id, $request)
    {
        try {
            if(!$this->roleHasPermission("Read Billing Invoices"))
            {
              throw new \Exception("User does not have the right permission.", 403);
            }
            $this->query = $this->getModel()->where('company_id', $id);
            $presenter = new DataPresenter(BillingInvoiceResource::class, $request);

            return $presenter->render($this->query);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function store($request)
    {
        try {
            if(!$this->roleHasPermission("Create Billing Invoices"))
            {
              throw new \Exception("User does not have the right permission.", 403);
            }
            $billingInvoice = BillingInvoice::create($request->all());
            return $this->show($billingInvoice->id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function createBillingInvoice($request)
    {
        try {
            if(!$this->roleHasPermission("Update Billing Invoices"))
            {
              throw new \Exception("User does not have the right permission.", 403);
            }
            $foundBillingInvoices = BillingInvoice::where(["company_id" => $request["company_id"]])->get();
            if (count($foundBillingInvoices) < 1) {
                $billingInvoice = BillingInvoice::create($request);
            }
            return true;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update($id, $request)
    {
        try {

            if ($this->roleHasPermission("Update Billing Invoices")) {

                $billingInvoice = BillingInvoice::findOrFail($id);
                $billingInvoice->amount = $request->amount;
                $billingInvoice->save();

                return $this->show($id, $request);

            } else if ($this->roleHasPermission("Approve Billing Invoices")) {

                return $this->approveBillingInvoice($id, $request);

            } else {

                throw new \Exception("User does not have the right permission.", 403);

            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    private function approveBillingInvoice($id, $request)
    {
        try {

            $payload = [
                "is_approved" => $request->is_approved ?? 0,
            ];

            $foundBillingInvoice = BillingInvoice::where([['id', $id], ['company_id', '=', $this->userLogin()->company_id]])
            ->firstOrFail()->update($payload);

            return $this->show($id, $request);
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
            BillingInvoice::findOrFail($id)->delete();
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
    public function updateStatusBillingInvoice($id, $request)
    {
        \DB::beginTransaction();
        try {
            if (!$this->roleHasPermission('Update Status Billing Invoices')) {
                throw new \Exception("User does not have the right permission.", 403);
            }
            $request->is_resign = $request->is_resign ? $request->is_resign : 0;
            $billingInvoice = BillingInvoice::where('id', $id)->first();
            $billingInvoice->status = 'PAID';
            $billingInvoice->save();
            $category = SubmissionCategory::firstOrCreate([
                'category_name'     => 'Payment Billing',
                'maximum'           => 0,
                'submission_type'   => 'INCOME',
                'company_id'        => $this->userLogin()->company_id
            ]);
            $number = Submission::where(['company_id' => $this->userLogin()->company_id])->whereHas('submission_category', function($q){
                $q->where('submission_categories.submission_type', 'INCOME');
            })->count();
            $company = Company::find($billingInvoice->company_id);
            if($company->email_verified_at == null){
                $company->email_verified_at = date('Y-m-d H:i:s');
                $company->save();
            }
            $submission = Submission::where('submission_name','Payment Billing #' . $company->business_name .'-'. date('F', strtotime($billingInvoice->date)) . date('Y'))->first();
            if (!$submission) {
                $submission = Submission::firstOrCreate([
                    'submission_name'       => 'Payment Billing #' . $company->business_name .'-'. date('F', strtotime($billingInvoice->date)) . date('Y'),
                    'status'                => "PARTIAL PAID", 
                    'amount'                => $billingInvoice->amount,
                    'reference_doc_number'  => 'INCOME' . $this->userLogin()->company_id . "-" . $number++. date('Ymd-His'), 
                    'date'                  => date('Y-m-01 00:00:00', strtotime($billingInvoice->date)), 
                    'due_date'              => date('Y-m-t 12:59:59', strtotime($billingInvoice->date)), 
                    'user_id'               => $this->userLogin()->id,
                    'company_id'            => $this->userLogin()->company_id,
                    'partner_id'            => \App\Entities\User::where('company_id' , $company->id)->role('enterprise_'.$company->id)->first()->id,
                    'category_id'           => $category->id,
                    'description'           => 'payment billing company',
                ]);
            }
            Item::create([
                'item_name'     => 'operating costs BOSS '. $company->business_name,
                'submission_id' => $submission->id,
                'quantity'      => 1,
                'selling_price' => $billingInvoice->amount,
                'company_id'    => $submission->company_id,
                'product_id'    => 0
            ]);
            $payment = PaymentTransaction::where('submission_id', $submission->id)->sum('amount');
            if ($payment == $billingInvoice->amount) {
                throw new \Exception("payment billing has been paid.", 402);
            }elseif (($payment + $request->amount) > $billingInvoice->amount) {
                throw new \Exception("payment exceed bill amount.", 402);
            }
            if ($request->is_resign == true && $this->userLogin()->company_id != 0) {
                $lastDateCounter = BillingCounter::where(['company_id' => $billingInvoice->company_id])->whereMonth('date', date('m', strtotime($billingInvoice->date)))->whereYear(date('Y'))->orderBy('id', 'desc')->first();
                $dayResign = date('d', strtotime($lastDateCounter->date));
                $amountDay = date('t', strtotime($lastDateCounter->date));
                $totalCostWhenResign = ($billingInvoice->amount / (int)$amountDay) * (int)$dayResign;
                $billingInvoice->amount = $totalCostWhenResign;
                $billingInvoice->invoice_name = $billingInvoice->invoice_name . "(RESIGN)";
                $billingInvoice->save();
                $paymentWillDeleting = PaymentTransaction::where('submission_id', $submission->id)->orderBy('id','desc')->first();
                $amountPaid = PaymentTransaction::where('submission_id', $submission->id)->sum('amount');
                if (($request->amount + $amountPaid) == $totalCostWhenResign) {
                    PaymentTransaction::create([
                        'amount'            => $totalCostWhenResign,
                        'transaction_date'  => date('Y-m-d H:i:s'),
                        'company_wallet_id' => $paymentWillDeleting->company_wallet_id, 
                        'user_id'           => $this->userLogin()->id,
                        'company_id'        => $submission->company_id,
                        'submission_id'     => $submission->id
                    ]);
                }else{
                    if (($request->amount + $amountPaid) < $totalCostWhenResign) {
                        throw new \Exception("Insufficient number of transactions, the rest of the transaction to be paid is ". ($totalCostWhenResign-$amountPaid) .".", 402);
                    }elseif(($request->amount + $amountPaid) > $totalCostWhenResign){
                        throw new \Exception("The number of transactions exceeds the total amount, the remaining transaction to be paid is ". ($totalCostWhenResign-$amountPaid) .".", 402);
                    }
                }
                $submission->status = "COMPLETED";
                $submission->fullfilment = true;
                $submission->amount = $totalCostWhenResign;
                $submission->save();
                PaymentTransaction::where('submission_id', $submission->id)->delete();
                $company->email_verified_at = null;
                $company->phone_verified_at = null;
                $company->save();
            }
            $transaction = PaymentTransaction::create([
                'amount'            => $request->amount,
                'transaction_date'  => date('Y-m-d H:i:s'),
                'company_wallet_id' => $request->company_wallet_id, 
                'user_id'           => $this->userLogin()->id,
                'company_id'        => $submission->company_id,
                'submission_id'     => $submission->id
            ]);
            $amount = PaymentTransaction::where('submission_id', $submission->id)->sum('amount');
            if ($amount < $billingInvoice->amount) {
                $submission->status = "PARTIAL PAID";
            }elseif ($amount == $billingInvoice->amount) {
                $submission->status = "PAID";
            }
            $submission->save();
            if (isset($request['file'])) {
                $request['transaction_id'] = $transaction->id;
                $this->paymentAttachmentRepository->storePaymentTransactionAttachment($request);
            }
            
            $this->copySubmissionToVendorOrCustomer($submission->id);

            \DB::commit();

            return  [
                'success' => true,
                'message' => 'Success to update billing invoice and create submission with status'. $submission->status,
                'data' => [
                    'attributes' => $billingInvoice
                ],
                'included' => [],   
                'meta' => [
                    'relations' => [],
                    'available_relations' => [],
                    'links' => [
                        'self' => url()->current(),
                    ]
                ],
            ]; 
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
