<?php

namespace App\Repositories\Submissions;

use Spatie\Permission\Models\Permission;
use App\Entities\Submission;
use App\Entities\SubmissionAttachment;
use App\Entities\SubmissionCategory;
use App\Entities\ReadStatus;
use App\Entities\Item;
use App\Entities\PaymentTransaction;
use App\Entities\PaymentTransactionAttachment;
use App\Entities\Company;
use App\Entities\ProductSchedule;
use App\Entities\User;
use App\Entities\Product;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Repositories\Items\ItemRepository;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Submissions\SubmissionAttachmentRepository;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Submissions\SubmissionResource;
use DB;
use Log;

class SubmissionRepository extends BaseRepository
{
	public function __construct(ItemRepository $itemRepository, ProductRepository $productRepository, SubmissionAttachmentRepository $submissionAttachmentRepository)
	{
		parent::__construct(Submission::class);
		$this->itemRepository 		= $itemRepository;
		$this->productRepository 	= $productRepository;
		$this->submissionAttachmentRepository = $submissionAttachmentRepository;
	}

	public function browse(Request $request)
	{
		try {
			$submission = $this->getModel()->where('submissions.company_id',$this->userLogin()->company_id);
			if ($this->permissionReadSubmission($request, "all")){ 
				$this->query = $submission;
			}
			else {
				if($this->readDataByRole($submission, "Read Submission That Are Created By")) {
					$this->query = $this->readDataByRole($submission, "Read Submission That Are Created By");
				}
				if ($this->permissionReadSubmission($request, "one")){ 
					$this->query = $submission->where('user_id', $this->userLogin()->id);
				}else{
					throw new \Exception("User does not have the right permission.", 403);
				}
			}
			$this->applyCriteria(new SearchCriteria($request));
			$request['total_submission']= $this->getTotal('total_submission');
			$request['total_transaction'] = $this->getTotal('total_transaction');
			$presenter = new DataPresenter(SubmissionResource::class, $request);
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

	public function getTotal($param)
	{
		$total = 0;
		if ($param == 'total_transaction') {
			$submissions = $this->query->with(['payment_transaction'])->get();
			foreach ($submissions as $submission) {
				foreach ($submission->payment_transaction as $payment) {
					$total += $payment->amount;
				}
			}
			return $total;
		}elseif ($param == 'total_submission') {
			return $this->query->whereIn('status', ['APPROVED','PARTIAL PAID','PAID','COMPLETED','CANCELLED'])->sum('amount');
		}
	}

	public function show($id, Request $request)
	{
		try {
			$submission = Submission::where(['submissions.id' => $id, 'submissions.company_id' => $this->userLogin()->company_id]);
			if ($this->roleHasPermission('Read All Submission ' . SubmissionCategory::find($submission->first()->category_id)->submission_type)){ 
				$this->query = $submission;
			}
			elseif($this->readDataByRole($submission, "Read Submission That Are Created By")) {
				$this->query = $this->readDataByRole($submission, "Read Submission That Are Created By");
			}
			elseif ($this->roleHasPermission('Read Submission '.SubmissionCategory::find($submission->first()->category_id)->submission_type)) {
				$this->query = $submission->where('user_id', $this->userLogin()->id);
				if (!$this->query->first()) {
					throw new \Exception("Action denied, User only have permission to Read her submission", 403);
				}
			}else{
				throw new \Exception("User does not have the right permission.", 403);
			}
			$presenter = new DataPresenter(SubmissionResource::class, $request);
			return $presenter->render($this->query);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}
	public function showRender($id, $request)
	{
		$this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
		$presenter = new DataPresenter(SubmissionResource::class, $request);

		return $presenter->render($this->query);
	}

	public function store($request)
	{
		\DB::beginTransaction();
		try {
			$category = SubmissionCategory::find($request->category_id);
			if (!$this->roleHasPermission('Create Submission ' . $category->submission_type)){ 
				throw new \Exception("User does not have the right permission.", 403);
			}
			
			$payload = $request->except(['fullfilment']);
			if($this->roleHasPermission("Create Submission ".$category->submission_type." With Status APPROVED")) {
				$payload["status"]		= "APPROVED";
			}
			else{
				$payload["status"]		= $payload["status"] == 'oke' ? "PENDING" : "DRAFT";
			}
			
			$payload['description'] = $payload['description'] ?? '';
			$payload["company_id"] 	= $this->userLogin()->company_id;
			$payload["amount"]		= 0;
			$foundPartner 			= User::where('email' ,$payload['customer_email'])->orWhere('phone',$payload['customer_email'])
										->orWhere('username',$payload['customer_email'])->first();
			if ($this->havingRole($foundPartner,'enterprise') && $request->order_for['type'] === "PERSONAL") {
				throw new \Exception("User Enterprise can not create submission for personal", 403); 
			}
			if ($request->order_for['type'] === "COMPANY") {
				if($this->userHasCompany($foundPartner->id, $request->order_for['company_id'])){
					$role = $request->order_for['company_id'] == 0 ? "enterprise" : "enterprise_". $request->order_for['company_id'];
					$enterpriseCompany = User::where('company_id', $request->order_for['company_id'])->role($role)->first();
					if (isset($enterpriseCompany)) {
						$payload['partner_id'] = $enterpriseCompany->id;
					}
				}
			}
			else {
				$payload['partner_id']	= $foundPartner->id;
			}
			$payload["user_id"] 	= $this->userLogin()->id;

			$category = SubmissionCategory::find($payload['category_id']);
			$number = Submission::where(['company_id' => $payload['company_id']])->whereHas('submission_category', function($q)use($category){
				$q->where('submission_categories.submission_type', $category->submission_type);
			})->count();
			$payload['reference_doc_number'] = $category->submission_type . $this->userLogin()->company_id . "-" . $number++ . date('Ymd-His');
     	 	$submission = Submission::create($payload);

			if(isset($payload["items"])){
				foreach ($payload["items"] as $item){
					$product 	= Product::whereRaw('lower(product_name) =?', strtolower($item['item_name']))
								->where(['company_id' => $this->userLogin()->company_id, 'products.deleted_at' => null,
								'status' => 'APPROVED'])->first();
								
					if ($request->order_for['type'] == 'COMPANY') {
						if ($request->order_for['company_id'] == $this->userLogin()->company_id) {
							$item['tax'] 		= 0;
							$item['discount'] 	= 100;
						}
					}
					$item['selling_price'] 	= $category->submission_type == 'INCOME'? $item['selling_price'] : 0;
					$item['buying_price'] 	= $category->submission_type == 'EXPENSE'? $item['buying_price'] : 0;
					
					if(isset($product)){	
						$item['product_id'] 	= $product->id;
						$item['item_name'] 		= $product->product_name;
						$product->selling_price = ($category->submission_type === 'INCOME' && $submission->status !== "DRAFT" && 
						((float)$item['selling_price'] > (float) $product->selling_price)) ? (float)$item['selling_price'] : $product->selling_price;
						$product->buying_price  = ($category->submission_type === 'EXPENSE' && $submission->status !== "DRAFT" && 
						((float)$item['buying_price'] > (float)$product->buying_price)) ? (float)$item['buying_price'] : $product->buying_price;

            			$product->save();
					}
					$item['company_id'] 	= $this->userLogin()->company_id;
					$item['submission_id'] 	= $submission->id;
					$schedules 				= isset($item['schedules'])? $item['schedules'] : null;
					$item = Item::create($item);	

					$this->createNewSchedule($schedules, $item->id);
				  
					$amountIncome	= ($item['quantity'] * $item['selling_price']);
					$amountExpense 	= ($item['quantity'] * $item['buying_price']);
					if($item['discount'] > 0){
						$amountIncome	= ($amountIncome - (($amountIncome * $item['discount']))/100);
						$amountExpense	= ($amountExpense - (($amountExpense * $item['discount']))/100);
					}
					if($item['tax'] > 0){
						$amountIncome	= ($amountIncome + (($amountIncome * $item['tax']))/100);
						$amountExpense	= ($amountExpense + (($amountExpense * $item['tax']))/100);
					}
					$payload["amount"] 	+= $category->submission_type == 'INCOME'? $amountIncome : $amountExpense;
				}
      		}
      
			$submission = tap(Submission::findOrFail($submission->id))->update($payload);
			if($submission->status == "APPROVED" && $submission->amount == 0){
				$submission->status = "PAID";
				$submission->save();
     	 	}

			$partnerCompany = User::find($submission->partner_id);
			if($this->havingRole($partnerCompany,'enterprise') && in_array($submission->status, array("APPROVED", "PARTIAL PAID", "PAID", "COMPLETED")))
			{
				$this->copySubmissionToVendorOrCustomer($submission->id);
      		}

			\DB::commit();
			return $this->showRender($submission->id, $request);				
		} catch (\Exception $e) {
			\DB::rollback();
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function update($id, $request)
	{
		\DB::beginTransaction();
		try {
			$submission = Submission::where(['id' => $id, 'company_id' => $this->userLogin()->company_id])->firstOrFail();
			$category = SubmissionCategory::find($submission->category_id);
			if ($this->roleHasPermission('Update All Submission '.$category->submission_type)){ 
				$submission = $submission;
			}
			elseif($this->updateDataByRole($submission, "Update Submission That Are Created By")) {
				$submission = $this->updateDataByRole($submission, "Update Submission That Are Created By");
			}
			elseif ($this->roleHasPermission('Update Submission ' . $category->submission_type)) {
				$submission = $submission->where('user_id', $this->userLogin()->id);
				if (!$submission) {
					throw new \Exception("Action denied, User only have permission to Update her submission", 403);
				}
			}
			else{
				throw new \Exception("User does not have the right permission.", 403);
			}
			$payload = $request->except(['fullfilment','items','reference_doc_number']);
			$number = Submission::where(['company_id' => $submission->company_id])->whereHas('submission_category', function($q)use($category){
				$q->where('submission_categories.submission_type', $category->submission_type);
			})->count();
			$payload['reference_doc_number'] = strpos($submission->reference_doc_number, $category->submission_type . $this->userLogin()->company_id) ? 
			$submission->reference_doc_number : $category->submission_type . $this->userLogin()->company_id . "-" . $number++. date('Ymd-His'); 
			$payload["status"] = $submission->status === "DRAFT"? "PENDING" : $submission->status;
			$payload["amount"] = 0;
			
			$foundPartner = User::where('email' ,$payload['customer_email'])->orWhere('phone',$payload['customer_email'])
			->orWhere('username',$payload['customer_email'])->first();
			if (in_array($submission->status, ['DRAFT','PENDING','PARTIAL APPROVED'])) {
				if ($this->havingRole($foundPartner,'enterprise') && $request->order_for['type'] === "PERSONAL") {
					throw new \Exception("User Enterprise can not create submission for personal", 403); 
				}
				if ($request->order_for['type'] === "COMPANY") {
					if($this->userHasCompany($foundPartner->id, $request->order_for['company_id'])){
						$role = $request->order_for['company_id'] == 0 ? "enterprise" : "enterprise_". $request->order_for['company_id'];
						$enterpriseCompany = User::where('company_id', $request->order_for['company_id'])->role($role)->first();
						if (isset($enterpriseCompany)) {
							$payload['partner_id'] = $enterpriseCompany->id;
						}
					}
				}else {
					$payload['partner_id']	= $foundPartner->id;
				}
			}
			else {
				$payload['partner_id'] = $submission->partner_id;
			}
			$payload["user_id"] 	= $this->userLogin()->id; 
			$items = Item::where('submission_id', $submission->id)->get();
			foreach ($items as $deleteItem) {
				$deleteItem->product_schedule()->delete();
				$deleteItem->item_attachment()->delete();
				$deleteItem->delete();
			}
			if(isset($request['items'])){
				foreach($request['items'] as $item){
					$product = Product::whereRaw('lower(product_name) =?', strtolower($item['item_name']))->where([
					'company_id' => $this->userLogin()->company_id, 'products.deleted_at' => null, 
					'status' => 'APPROVED'])->first();
					$complimentUser = User::find($submission->partner_id);
					if ($this->havingRole($complimentUser, 'enterprise') && $complimentUser->company_id === $this->userLogin()->company_id) {
						$item['tax'] = 0;
						$item['discount'] = 100;
					}

					$item['selling_price'] = $category->submission_type == 'INCOME'? $item['selling_price'] : 0;
					$item['buying_price'] = $category->submission_type == 'EXPENSE'? $item['buying_price'] : 0;
					if(isset($product)){	
						$item['product_id'] = $product->id;
						$item['item_name'] = $product->product_name;
						$product->selling_price = ($category->submission_type === 'INCOME' && $submission->status !== "DRAFT" && 
						((float)$item['selling_price'] > (float) $product->selling_price))? : $item['selling_price'];
						$product->buying_price  = ($category->submission_type === 'EXPENSE' && $submission->status !== "DRAFT" && 
						((float)$item['buying_price'] > (float)$product->buying_price)) ? : $item['buying_price'];
						$product->save();
					}
					$item['company_id'] = $this->userLogin()->company_id;
					$item['submission_id'] = $submission->id;
					$schedules = isset($item['schedules'])? $item['schedules'] : null;

					$item = Item::create($item);
					$this->createNewSchedule($schedules, $item['id']);
					
					$amountIncome = ($item['quantity'] * $item['selling_price']);
					$amountExpense = ($item['quantity'] * $item['buying_price']);
					if($item['discount'] > 0){
						$amountIncome = ($amountIncome - (($amountIncome * $item['discount']))/100);
						$amountExpense = ($amountExpense - (($amountExpense * $item['discount']))/100);
					}
					if($item['tax'] > 0){
						$amountIncome = ($amountIncome + (($amountIncome * $item['tax']))/100);
						$amountExpense = ($amountExpense + (($amountExpense * $item['tax']))/100);
					}
					$payload["amount"] += $category->submission_type == "INCOME"? $amountIncome:$amountExpense;
				}				
			}
			if(in_array($submission->status, array("DRAFT", "PENDING", "PARTIAL APPROVED"))){
				$submission->update($payload);
			}
			else {
				$submission->due_date = $payload['due_date'];
				$submission->reference_doc_number = $payload["reference_doc_number"];
				if ($submission->status =="REJECTED") {
					$submission->status = "PENDING";
				}
				elseif ($payload['amount'] > $submission->amount && $submission->status == "PAID") {
					$submission->status = "PARTIAL PAID";
				}
				$submission->amount = $payload['amount'];
				$submission->save();
			}
			if($submission->status == "APPROVED" && $submission->amount == 0){
				$submission->status = "PAID";
				$submission->save();
     		}
			$partnerCompany = User::find($submission->partner_id);
			if($this->havingRole($partnerCompany,'enterprise') && in_array($submission->status, array("APPROVED", "PARTIAL PAID", "PAID", "COMPLETED")))
			{
				$this->copySubmissionToVendorOrCustomer($submission->id);
			}
			\DB::commit();
			return $this->showRender($id, $request);
		} catch (\Exception $e) {
			\DB::rollback();
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function destroy($id)
	{
		try {
			$submission = Submission::where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
			$category = SubmissionCategory::find($submission->first()->category_id);
			if (!$submission->first()) {
				throw new \Exception("Submission not found", 404);
			}
			if (strpos($submission->first()->submission_name, '(copy')) {
				throw new \Exception("Action Denied, cannot Delete submission generated by System", 400);
			}
			if ($this->roleHasPermission('Delete All Submission '. $category->submission_type)){ 
				$submission = $submission->firstOrFail();
			}
			elseif($this->deleteDataByRole($submission, "Delete Submission That Are Created By")) {
				$submission = $this->deleteDataByRole($submission, "Delete Submission That Are Created By")->firstOrFail();
			}
			elseif ($this->roleHasPermission('Delete Submission '. $category->submission_type)) {
				$submission = $submission->where('user_id', $this->userLogin()->id)->first();
				if (!$submission) {
					throw new \Exception("Action denied, User only have permission to Delete her submission", 403);
				}
			}
			else{
				throw new \Exception("User does not have the right permission.", 403);
			}
			if (!in_array($submission->status, array('DRAFT','PENDING','PARTIAL APPROVED','REJECTED'))) {
				throw new \Exception("Submission can be Deleted if status in DRAFT or PENDING or PARTIAL APPROVED or REJECTED", 403); 
			}
			$items = Item::where(['submission_id' => $submission->id]);
			$payment = PaymentTransaction::where(['submission_id'=> $submission->id]);
			foreach ($items as $item) {
				$item->item_attachment()->delete();
				$item->product_schedule()->delete();
				$item->delete();
			}
			foreach ($payment as $pay) {
				$pay->payment_transaction_attachment()->delete();
				$pay->delete();
			}
			$submission->submission_attachment()->delete();
			ReadStatus::where(['read_table' => "submission", 'table_row_id' => $submission->id])->delete();
			$submissionVendor = Submission::where('submission_name', 'ILIKE',"%(copy " . $submission->reference_doc_number .")%")->first();
			if (isset($submissionVendor)) {
				$this->deleteSubmission($submissionVendor->id);
			}
			$submission->delete();
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
  	public function updateStatus($id,$request)
  	{
		\DB::beginTransaction();
		try {
			$submission = Submission::find($id);
			$category = SubmissionCategory::find($submission->category_id);
			if($this->roleHasPermission("Update Submission ".$category->submission_type." Status To REJECTED") && $request->status == "reject"){
				if(in_array($submission->status, ['PENDING','PARTIAL APPROVED']) ){
					$submission->status = "REJECTED";
				}else{
					throw new \Exception("Update to REJECTED, Submission status must be in PENDING or PARTIAL APPROVED", 403); 
				}
			}elseif($submission->status == "APPROVED" && $submission->amount == 0){
				$submission->status = "PAID";
			}elseif($this->roleHasPermission("Update Submission ".$category->submission_type." Status To PARTIAL APPROVED") && $submission->status =='PENDING'){
				if ($this->roleHasPermission("Update Submission ".$category->submission_type." Status To APPROVED")) {
					$submission->status = "APPROVED";
				}else{
					$submission->status = "PARTIAL APPROVED";
				}
			}elseif($this->roleHasPermission("Update Submission ".$category->submission_type." Status To APPROVED") && $submission->status =='PARTIAL APPROVED'){
				$submission->status = "APPROVED";
			}elseif($this->roleHasPermission("Update Submission ".$category->submission_type." Status To COMPLETED") && $submission->status =='PAID'){
				if ($submission->fullfilment != 1) {
					throw new \Exception("Update to COMPLETED, Submission Fullfilment must be in FULLFILLED", 403); 
				}
				$submission->status = "COMPLETED";
			}else{
				throw new \Exception("User not have the right permission or wrong before condition status", 403); 
			}
			$submission->save();
			
			if($submission->status == "APPROVED" && $submission->amount == 0){
				$submission->status = "PAID";
				$submission->save();
			}
			$partnerCompany = User::find($submission->partner_id);
			if($this->havingRole($partnerCompany,'enterprise') && in_array($submission->status, array("APPROVED", "PARTIAL PAID", "PAID", "COMPLETED")))
			{
				$this->copySubmissionToVendorOrCustomer($submission->id);
			}

			\DB::commit();
			return $this->showRender($id, $request);
		} catch (\Exception $e) {
			\DB::rollback();
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}
	  
	public function updateFullfilment($id, $request)
	{
		\DB::beginTransaction();
		try {
			$submission  	= Submission::find($id);
			$category = SubmissionCategory::find($submission->category_id);
			if($this->roleHasPermission("Update Submission ".$category->submission_type." Status To REFUND","Update Submission ".$category->submission_type." Fullfilment To UNFULLFILLED") && $request->status == "refund" && $submission->fullfilment == null){
				if (in_array($submission->status, array('PARTIAL PAID', 'PAID', 'APPROVED'))){
					$submission->status = "REFUND";
					$submission->fullfilment = 0;
					$submission->amount = 0;
					$submission->save();
					PaymentTransaction::where(['submission_id' => $id])->delete();
				}else{
					throw new \Exception("Update to REFUND, Submission status must be in APPROVED, PARTIAL PAID or PAID", 403); 
				}
			}elseif($this->roleHasPermission("Update Submission ".$category->submission_type." Status To CANCELLED", "Update Submission ".$category->submission_type." Fullfilment To UNFULLFILLED") && $request->status == "cancel" && $submission->fullfilment == null){
				if(in_array($submission->status, array('PARTIAL PAID', 'PAID', 'APPROVED'))){
					$submission->status = "CANCELLED";
					$submission->fullfilment = 0;
					$transaction = PaymentTransaction::where(['submission_id' => $id])->sum('payment_transactions.amount');
					$submission->amount = $transaction;
					$submission->save();
				}else{
					throw new \Exception("Update to CANCELLED, Submission status must be in APPROVED, PARTIAL PAID or PAID", 403); 
				}
			}
			elseif($this->roleHasPermission("Update Submission ".$category->submission_type." Fullfilment To FULLFILLED") && $submission->fullfilment == null){
				if (in_array($submission->status, array("APPROVED","PAID","PARTIAL PAID"))) {
					$submission->fullfilment = 1;
					$this->calculateProductStock($id);
					$submission->save();
				}else {
					throw new \Exception("Update to FULLFILLED, Submission status must be in APPROVED, PAID, PARTIAL PAID, COMPLETED", 403); 
				}
			}			
			else{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			
			$partnerCompany = User::find($submission->partner_id);
			if($this->havingRole($partnerCompany,'enterprise') && in_array($submission->status, array("APPROVED", "PARTIAL PAID", "PAID", "COMPLETED")))
			{
				$this->copySubmissionToVendorOrCustomer($submission->id);
			}

			\DB::commit();
			return $this->showRender($id, $request);
		} catch (\Exception $e) {
			\DB::rollback();
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}
	
	public function calculateProductStock($id) 
	{
		$submission  	= Submission::find($id);
		$items 		= Item::where(['submission_id' => $id])->get();
		$category 	= SubmissionCategory::find($submission->category_id);
		foreach ($items as $item ){
			$product = Product::whereRaw('lower(product_name) =?', strtolower($item['item_name']))
						->where(['company_id' => $this->userLogin()->company_id, 'products.deleted_at' => null,
						'status' => 'APPROVED'])->first();
			if (isset($product)){
				if($category->submission_type === 'INCOME'){
					$product->stock = (float)$product['stock'] - (float)$item['quantity'];
					$product->already_sold += $item['quantity'];
				}
				elseif($category->submission_type === 'EXPENSE'){
					$product->stock = (float)$product['stock'] + (float)$item['quantity'];
				} 
				$product->save();
			}
		}
		return true;
	}

	public function createNewSchedule($payloadSchedule, $itemId){ 
		$foundSchedule = ProductSchedule::where('item_id', $itemId);
		if ($payloadSchedule) {
			$foundSchedule->delete();

			foreach ($payloadSchedule as $schedule) {
				$schedule['item_id'] 	= $itemId;
				$schedule['company_id'] = $this->userLogin()->company_id;
				ProductSchedule::create($schedule);
			}
		}else {
			$foundSchedule->delete();
		}
		return true;
	}
	public function updateDueDate($id,$request)
	{
		\DB::beginTransaction();
		try {
			$submission = Submission::find($id);
			$category = SubmissionCategory::find($submission->category_id);
			if(!$this->roleHasPermission("Update Submission ".$category->submission_type." Due Date")){
				throw new \Exception("User not have the right permission", 403); 
			}	
			$submission->due_date = $request->due_date;
			$submission->save();

			$partnerCompany = User::find($submission->partner_id);
			if($this->havingRole($partnerCompany,'enterprise') && in_array($submission->status, array("APPROVED", "PARTIAL PAID", "PAID", "COMPLETED")))
			{
				$this->copySubmissionToVendorOrCustomer($submission->id);
			}

			\DB::commit();
			return $this->showRender($id, $request);
		} catch (\Exception $e) {
			\DB::rollback();
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}
	public function bulk($request)
	{
		\DB::beginTransaction();
		try {
			$payload = $request->all();
			$submissions = Submission::whereIn('id', $payload['ids'])->where('company_id', $this->userLogin()->company_id);
			$category = SubmissionCategory::find(Submission::find($payload['ids'][0])->category_id);
			if(in_array('all', $payload['ids'])){
				$submissions = Submission::whereHas('submission_category', function($q) use($category){
					$q->where('submission_type', $category->submission_type);
				})->where('company_id', $this->userLogin()->company_id);
			}
			if ($payload['field'] == 'fullfilment') {
				if ($payload['value'] == 1) {
					if (!$this->roleHasPermission("Update Submission ".$category->submission_type." Fullfilment To FULLFILLED")) {
						throw new \Exception("User does not have the right permission.", 403); 
					}
				}elseif ($payload['value'] == 0) {
					if (!$this->roleHasPermission("Update Submission ".$category->submission_type." Fullfilment To UNFULLFILLED") ) {
						throw new \Exception("User does not have the right permission.", 403); 
					}else {
						if ($payload['status'] == "cancel" && !$this->roleHasPermission("Update Submission ".$category->submission_type." Status To CANCELLED")) {
							throw new \Exception("User does not have the right permission.", 403); 
						}elseif ($payload['status'] == "refund" && !$this->roleHasPermission("Update Submission ".$category->submission_type." Status To REFUND")) {
							throw new \Exception("User does not have the right permission.", 403); 
						}
					}
				}

				foreach ($submissions->get() as $submission) {
					if(in_array($submission->status, array('PARTIAL PAID', 'PAID', 'APPROVED')) && $submission->fullfilment == null){
						if ($payload['value'] == 1) {
							$this->calculateProductStock($submission->id);
						}
						elseif ($payload['value'] == 0) {
							if ($payload['status'] == "cancel") {
								$transactionAmount = PaymentTransaction::where(['submission_id' => $submission->id])->sum('payment_transactions.amount');
								$submission->amount = $transactionAmount;
								$submission->status = 'CANCELLED';
							}elseif ($payload['status'] == "refund") {
								$submission->status = 'REFUND';
								$submission->amount = 0;
								PaymentTransaction::where(['submission_id' => $submission->id])->delete();
							}
						}else {
							continue;
						}
					}
					$submission->fullfilment = $payload['value'];
					$submission->save();
				}
			}
			elseif($payload['field'] == "status"){
				if (!$this->roleHasPermission("Update Submission ".$category->submission_type." Status To COMPLETED")) {
					throw new \Exception("User does not have the right permission.", 403); 
				}
				$submissions->where($payload['field'], 'PAID')->update(array($payload['field'] => "COMPLETED"));
			}
			elseif ($payload['field'] == "category_id"){
				if ($this->roleHasPermission('Update All Submission '.$category->submission_type)){ 
					$submissions = $submissions;
				}
				elseif($this->updateDataByRole($submissions, "Update Submission That Are Created By")) {
					$submissions = $this->updateDataByRole($submissions, "Update Submission That Are Created By");
				}
				elseif ($this->roleHasPermission('Update Submission ' . $category->submission_type)) {
					$submissions = $submissions->where('user_id', $this->userLogin()->id);
				}
				else {
					throw new \Exception("User does not have the right permission.", 403); 
				}
				$submissions->update(array($payload['field'] => $payload['value']));
			}

			\DB::commit();
			return response()->json([
				'success' => true,
				'message' => 'data has been deleted'
			], 200);
		} catch (\Exception $e) {
			\DB::rollback();
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}
}
