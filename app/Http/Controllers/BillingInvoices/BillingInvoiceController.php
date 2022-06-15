<?php

namespace App\Http\Controllers\BillingInvoices;

use App\Entities\BillingInvoice;
use App\Http\Controllers\Controller;
use App\Repositories\BillingInvoiceRepository;
use App\Http\Requests\Billings\UpdateStatusBillingInvoiceRequest;
use Illuminate\Http\Request;

class BillingInvoiceController extends Controller
{
    public $successStatus = 200;

    public function __construct(BillingInvoiceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        return $this->repository->browse($request);
    }
    
    public function show($id, Request $request)
    {
        return $this->repository->show($id, $request);
    }

    public function update(Request $request, $id)
    {
        return $this->repository->update($id, $request);
    }
    public function updateStatusBillingInvoice($id, UpdateStatusBillingInvoiceRequest $request)
    {
        return $this->repository->updateStatusBillingInvoice($id, $request);
    }

    // public function getFirstInvoice(Request $request){
    //   $userLogin = Auth::user();
    //   $foundInvoice = BillingInvoice::where(["company_id" => $userLogin->company_id])->whereNotNull('entity_id')->first();
    //   return response()->json(['success' => true, 'message' => 'Success', 'attributes' => $foundInvoice], $this->successStatus);
    // }

    // public function getInvoiceByCompanyId(Request $request) {
    //   $validator = Validator::make($request->all(), [
    //     'company_id' => 'required|integer'
    //   ]);
    //   if($validator->fails()){
    //     return response()->json(['success' => false, 'message' => $validator->errors()], 400);
    //   }
    //   $foundInvoices = BillingInvoice::where(["company_id" => $request->company_id])->get();
    //   return response()->json(['success' => true, 'message' => 'success get data', 'attributes' => $foundInvoices], $this->successStatus);
    // }

    // public function createInvoiceMonthly(){
    //   $companies = DB::table("companies")->get();
    //   $amount = 0;
    //   foreach($companies as $company) {
    //     $foundFirstInvoice = DB::table("billing_invoices")->where(["is_approved" => 1,"company_id" => $company->id])->whereNotNull('entity_id')->first();
    //     if($foundFirstInvoice){
    //       $amount = $foundFirstInvoice->amount;
    //     }else{
    //       $amount = 0;
    //     }
    //     $dateCarbon = Carbon::now()->startOfMonth();
    //   }
    //   return response()->json(['success' => true, 'message' => 'Success create billing invoice!'], 200);
    // }

    // // Mutation

    // /**
    //  * Store a newly invoice.
    //  *
    //  * @param $entity_id = int
    //  * @param $amount = int
    //  */
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //       'amount' => 'required',
    //       'entity_id' => 'required'
    //     ]);
    //     if($validator->fails()){
    //       return response()->json(['success' => false, 'message' => $validator->errors()], 400);
    //     }
    //     $foundEntity = User::find($request->entity_id);
    //     if(!$foundEntity) {
    //       return response()->json(['success' => false, 'message' => 'Company not found!'], 400);
    //     }
    //     $foundFirstInvoice = BillingInvoice::where(['company_id' => $foundEntity->company_id])->oldest()->first();
    //     if($foundFirstInvoice) {
    //       return $this->updateFirstInvoice($foundFirstInvoice, $request);
    //     }
    //     $createInvoice = BillingInvoice::create([
    //       'name' => 'Invoice '.Carbon::now(),
    //       'amount' => $request->amount,
    //       'date' => Carbon::now(),
    //       'is_approved' => 0,
    //       'status' => 'UNPAID',
    //       'entity_id' => $foundEntity->id,
    //       'company_id' => $foundEntity->company_id,
    //       ]);
    //     $company = Company::find($foundEntity->company_id);
    //     return response()->json(['success' => true, 'message' => 'Success create first invoice for '.$company->name.' company!', 'attributes' => $createInvoice], $this->successStatus);
    // }

    // public function approveFirstInvoiceByCompany(){
    //   $userLogin = Auth::user();
    //   $foundInvoice = BillingInvoice::where(["company_id" => $userLogin->company_id, "is_approved" => 0])->first();
    //   if(!$foundInvoice){
    //     return response()->json(["success" => false, "message" => "Invoice not found!"], 400);
    //   }
    //   $foundInvoice->is_approved = 1;
    //   $foundInvoice->save();
    //   return response()->json(["success" => true, "message" => "Success", "attributes" => $foundInvoice], $this->successStatus);
    // }

    // public function show(Request $request)
    // {
    //     return response()->json(['success' => true, 'message' => 'Success', 'attributes' => []], $this->successStatus);
    // }

    // public function update(Request $request, $id)
    // {
    //     $validator = Validator::make($request->all(), [
    //       'amount' => 'required'
    //     ]);

    //     if($validator->fails()){
    //       return response()->json(['success' => false, 'message' => $validator->errors()], 400);
    //     }

    //     $foundFirstInvoice = BillingInvoice::find($id);
    //     if(!$foundFirstInvoice) {
    //       return response()->json(['success' => false, 'message' => 'Invoice not found!'], 400);
    //     }

    //     $foundFirstInvoice->amount = $request->amount;
    //     $foundFirstInvoice->save();
    //     $company = Company::find($foundFirstInvoice->company_id);
    //     return response()->json(['success' => true, 'message' => 'Success update first invoice for '.$company->name], $this->successStatus);
    // }
    // protected function updateFirstInvoice($foundFirstInvoice, $request){
    //   $foundFirstInvoice->name = 'Updated';
    //   $foundFirstInvoice->amount = $request->amount;
    //   $foundFirstInvoice->save();
    //   $company = Company::find($foundFirstInvoice->company_id);
    //   return response()->json(['success' => true, 'message' => 'Success update invoice for '.$company->name, 'attributes' => $foundFirstInvoice], 200);
    // }

    // public function destroy($id){
    //     $foundFirstInvoice = BillingInvoice::find($id);
    //     if(!$foundFirstInvoice) {
    //       return response()->json(['success' => false, 'message' => 'Invoice not found!'], 400);
    //     }
    //     $foundFirstInvoice->delete();

    //     return response()->json(['success' => true, 'message' => 'Invoice successfully deleted!'], $this->successStatus);
    // }

}
