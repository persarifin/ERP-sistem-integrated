<?php

namespace App\Repositories\Products;

use App\Entities\Item;
use App\Entities\Product;
use \App\Entities\Submission;
use App\Entities\ProductAttachment;
use App\Entities\ProductSchedule;
use App\Entities\ProductVisibility;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Products\ProductResource;
use App\Repositories\BaseRepository;
use App\Repositories\Products\ProductAttachmentRepository;
use Illuminate\Http\Request;

class ProductRepository extends BaseRepository
{
	public function __construct(ProductAttachmentRepository $productAttachmentRepository,ProductVisibilityRepository $productVisibilityRepository)
	{
		parent::__construct(Product::class);
		$this->productVisibilityRepository 	= $productVisibilityRepository;
		$this->productAttachmentRepository 	= $productAttachmentRepository;
	}
	
	public function productLandingPage($categoryId, $request)
	{
		$this->query = $this->getModel()->with(['product_attachment' => function($q){
			$q->where('type', 'PRODUCT PHOTO');
		}])->where('category_id', $categoryId);
		$presenter = new DataPresenter(ProductResource::class, $request);

		return $presenter
			->preparePager()
			->renderCollection($this->query);
	}

	public function browse(Request $request)
	{
		try{
			if(!$this->roleHasPermission("Read Products"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			$this->query = $this->getModel()->where('company_id', $this->userLogin()->company_id);
			$this->applyCriteria(new SearchCriteria($request));
			$presenter = new DataPresenter(ProductResource::class, $request);

			return $presenter
				->preparePager()
				->renderCollection($this->query);
		}catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function show($id, Request $request)
	{
		try{
			if(!$this->roleHasPermission("Read Products"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			$this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
			$presenter = new DataPresenter(ProductResource::class, $request);

			return $presenter->render($this->query);
		}catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function store($request)
	{
		\DB::beginTransaction();
		try {
			if(!$this->roleHasPermission("Create Products"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			$payload = $request->except('status','already_sold');
			$payload['status'] 		= "PENDING";	
			$payload['company_id']	= $this->userLogin()->company_id;
			$item = Item::whereRaw('lower(item_name) =?', strtolower($payload['product_name']))
			->where("items.company_id", $payload['company_id'])->count();
			$payload['stock'] =  $item > 0 ? 0 : $payload['stock'];
			
			$product = Product::create($payload);
			$payload['attachment_type'] = 'PRODUCT PHOTO';
			$payload['product_id']		= $product->id;
			$this->productAttachmentRepository->storeProductAttachment($payload);

			foreach($payload['visibilities'] as $visibility){
				$visible['product_id'] = $product->id;
				$visible['company_id'] = $payload['company_id'];
				$visible['interface_id'] = $visibility;
				ProductVisibility::create($visible);
			}
			foreach($payload['detail_images'] as $image){
				$content['attachment_type'] = 'PRODUCT CONTENT';
				$content['file'] 			= $image;
				$content['product_id']		= $product->id;
				$content['company_id']		= $payload['company_id'];
				$this->productAttachmentRepository->storeProductAttachment($content);
			}
			
			if (in_array($payload['product_type'], array('LIMITED', 'TIME LIMIT'))) {
				$this->createNewSchedule($payload['schedules'], $product->id);
			}
			
			\DB::commit();
			return $this->show($product->id, $request);
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
			if(!$this->roleHasPermission("Update Products"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			$payload = $request->all();
			$product = Product::find($id);
			$payload["product_id"] 	= $id;
			$payload['company_id']	= $this->userLogin()->company_id;
			$item = Item::whereRaw('lower(item_name) =?', strtolower($payload['product_name']))
			->where("items.company_id", $payload['company_id'])->count();
			$payload['stock'] =  $item > 0? 0 : $payload['stock'];

			if ($product->status === 'APPROVED') {
				$payload['category_id'] = $payload['category_id'];
				$payload['description'] = $payload['description'];
				$product->update($payload);
			}else {
				Product::findOrFail($id)->update($payload);
			}
			// check if payload has file
			if(isset($payload['file']) && $request->hasFile($payload['file'])){
				$payload['file'] 			= $payload['file'];
				$payload['attachment_type'] = 'PRODUCT PHOTO';
				$this->productAttachmentRepository->storeProductAttachment($payload);
			}
			if(isset($payload['detail_images'])){
				ProductAttachment::where([
					'attachment_type' => 'PRODUCT CONTENT',
					'product_id' => $id
				])->delete();
				foreach($payload['detail_images'] as $image){
					if($request->hasFile($image)){
						$content['attachment_type'] = 'PRODUCT CONTENT';
						$content['file'] 			= $image;
						$content['product_id']		= $id;
						$this->productAttachmentRepository->storeProductAttachment($content);
					}
				}
			}
			if (in_array($payload['product_type'], array('LIMITED', 'TIME LIMIT'))) {
				$this->createNewSchedule($payload['schedules'], $product->id);
			}
			if(isset($payload['visibilities'])) {
				ProductVisibility::where('product_id', $id)->delete();
				foreach ($payload['visibilities'] as $visibility) {
					$visibility['product_id'] = $product->id;
					$visibility['company_id'] = $payload['company_id'];
					$visibility['interface_id'] = $visibility;
					ProductVisibility::create($visibility);
				}
			}

			\DB::commit();
			return $this->show($id, $request);
		} catch (\Exception $e) {
			\DB::rollback();
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}

	public function createNewSchedule($schedule, $productId)
	{ 
		$schedule['item_id'] 	= 0;
		$schedule['product_id'] = $productId; 
		$schedule['company_id'] = $this->userLogin()->company_id;
		$foundSchedule = ProductSchedule::where(['product_id' => $productId]);
		if(isset($schedule)){
			$foundSchedule->delete();
			ProductSchedule::create($schedule);
		}else {
			$foundSchedule->delete();
		}     
		return true;
	}

	public function destroy($id)
	{
		try {
			if(!$this->roleHasPermission("Delete Products"))
			{
				throw new \Exception("User does not have the right permission.", 403); 
			}
			$product = Product::where(['id' =>$id, 'company_id' => $this->userLogin()->company_id])->firstOrFail();
			if (in_array($product->status, array("PENDING","PARTIAL APPROVED"))) {
				ProductAttachment::where('product_id', $id)->delete();
				ProductSchedule::where(['product_id' => $id])->delete();
				$product->delete();
			}else {
				throw new \Exception("Cannot delete Product when Product status is APPROVED", 403); 
			}
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
			$product  	= Product::where(['id' => $id, 'company_id' => $this->userLogin()->company_id])->first();
			$status		= Item::select('items.*','submission_name','submission_type')
								->leftjoin('submissions','submissions.id','=','items.submission_id')
								->join('submission_categories','submissions.category_id','=','submission_categories.id')
								->whereRaw('lower(item_name) =?', strtolower($product['product_name']))
								->where(['submissions.deleted_at' => null,'items.company_id' => $product['company_id']]);
			$income = 0;
			$expense = 0;
			if(isset($status)){
				foreach ($status->get() as $key) {
					if ($key->submission_type == "INCOME") {
						$income += $key->quantity;
					}else{
						$expense += $key->quantity;
					}
				}
			}

			if($this->roleHasPermission("Update Product Status To PENDING")&& $request->status == "reject" && (in_array($product->status, array('PARTIAL APPROVED','PENDING')))){
				$product->status = "PENDING";
			}
			elseif(($this->roleHasPermission("Update Product Status To APPROVED","Update Product Status To PARTIAL APPROVED") ||$this->roleHasPermission("Update Product Status To APPROVED")) && (in_array($product->status, array('PARTIAL APPROVED','PENDING')))){
				if ($product->stock > 0) {
					$this->createSubmissionProduct($product);
				}
				else {
					$product['stock']		= $status ? $expense - $income : 0;
				}
				$product['already_sold']= $status->where(['submission_categories.submission_type' => "INCOME",
														'submissions.fullfilment' => 1])->sum('items.quantity');
				$product->status = "APPROVED";
				
			}
			elseif($this->roleHasPermission("Update Product Status To PARTIAL APPROVED") && $product->status =='PENDING'){
				$product->status = "PARTIAL APPROVED";
			}
			else{
				throw new \Exception("User does not have the right permission or wrong before condition status", 403); 
			}
			$product->save();

			\DB::commit();
			return $this->show($id, $request);
		} catch (\Exception $e) {
			\DB::rollback();
			return response()->json([
				'success' => false,
				'message' => $e->getMessage()
			], 400);
		}
	}
	public function createSubmissionProduct($product)
	{
		$number = Submission::whereHas('submission_categpry', function($q){
			$q->where('submission_type',"EXPENSE");
		})->count();
		$category = \App\Entities\SubmissionCategory::firstOrCreate([
			'category_name' => "PENGELUARAN",
			'submission_type' => 'EXPENSE',
			'maximum' => 0,
			'company_id' => $this->userLogin()->company_id,
		]);
		$submission = Submission::create([
			'submission_name' => "Cost for ". $product->product_name,
			'amount' => $product->buying_price * $product->stock,
			'status' => 'PAID', 
			'reference_doc_number' => 'EXPENSE' . $this->userLogin()->company_id . "-" . $number++. date('Ymd-His'), 
			'date' => date('Y-m-d H:i:s'), 
			'due_date' => date('Y-m-d H:i:s'), 
			'user_id' => $this->userLogin()->id,
			'company_id' => $this->userLogin()->company_id,
			'partner_id' => \App\Entities\User::whereHas('roles',function($q){
				$q->where('name', 'enterprise'. $this->userLogin()->company_id); 
			})->first()->id,
			'category_id' => $category->id,
			'description' => 'auto generated for creating product',
		]);
		App\Entities\Item::create([
			'item_name'     => $product->product_name,
			'submission_id' => $submission->id,
			'quantity'      => $product->stock,
			'buying_price' 	=> $product->buying_price,
			'selling_price' => $product->selling_price,
			'company_id'    => $this->userLogin()->company_id,
			'product_id'    => $product->id
		]);
		$wallet = \App\Entities\CompanyWallet::firstOrCreate([
			'wallet_name' => 'Cash',
			'company_id' => $this->userLogin()->company_id
		]);
		\App\Entities\PaymentTransaction::create([
			'amount' => $submission->amount,
			'transaction_date' => date('Y-m-d H:i:s'),
			'company_wallet_id' => $wallet->id,
			'user_id' => $this->userLogin()->id,
			'company_id' => $this->userLogin()->company_id,
			'submission_id' => $submission->id
		]);
	}
}
