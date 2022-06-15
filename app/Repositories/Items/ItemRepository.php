<?php

namespace App\Repositories\Items;

use App\Entities\Item;
use App\Entities\Submission;
use App\Entities\PaymentTransaction;
use App\Entities\Product;
use Illuminate\Http\Request;
use App\Http\Criterias\SearchCriteria;
use Illuminate\Support\Facades\Auth;
use App\Repositories\BaseRepository;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\Items\ItemResource;
use DB;

class ItemRepository extends BaseRepository
{
	public function __construct()
	{
		parent::__construct(Item::class);
    }

    public function browse(Request $request)
    {
        try{
            if(!$this->roleHasPermission("Read All Items"))
            {
            throw new \Exception("User does not have the right permission.", 403); 
            }

            $this->query = $this->getModel()->join('submissions', 'submissions.id', 'items.submission_id')
            ->join('submission_categories','submission_categories.id' ,'=','submissions.category_id')
            ->where('items.company_id', $this->userLogin()->company_id);
            $this->applyCriteria(new SearchCriteria($request));
            $request->total = $this->getTotal();
            $presenter = new DataPresenter(ItemResource::class, $request);
            return $presenter
                ->preparePager()
                ->renderCollection($this->query);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getTotal()
    {
        $query = $this->query;
        $qtyIncome = 0;
        $qtyExpense = 0;
        $income = 0;
        $expense =0;
        foreach ($query->get() as $data) {
            if ($data->submission_type == "INCOME"){
                if (in_array($data->status, array('APPROVED','PARTIAL PAID','PAID','COMPLETED'))) {
                    $qtyIncome += $data['quantity'];
                    $amountIncome	= ($data['quantity'] * $data['selling_price']);
                    if($data['discount'] > 0){
                        $amountIncome	= ($amountIncome - (($amountIncome * $data['discount']))/100);
                    }
                    if($data['tax'] > 0){
                        $amountIncome	= ($amountIncome + (($amountIncome * $data['tax']))/100);
                    }
                    $income += $amountIncome;
                } 
                elseif (in_array($data->status , ['CANCELED','REFUND'])) {
                    $income += PaymentTransaction::where('submission_id', $data->submission_id)->sum('amount');
                    $qtyIncome += 0;    
                }
            }elseif ($data->submission_type == "EXPENSE"){ 
                if(in_array($data->status, array('APPROVED','PARTIAL PAID','PAID','COMPLETED'))){
                    $qtyExpense += $data['quantity'];
                    $amountExpense 	= ($data['quantity'] * $data['buying_price']);
                    if($data['discount'] > 0){
                        $amountExpense	= ($amountExpense - (($amountExpense * $data['discount']))/100);
                    }
                    if($data['tax'] > 0){
                        $amountExpense	= ($amountExpense + (($amountExpense * $data['tax']))/100);
                    }
                    $expense += $amountExpense;
                }
                elseif (in_array($data->status , ['CANCELED','REFUND'])) {
                    $expense += PaymentTransaction::where('submission_id', $data->submission_id)->sum('amount');
                    $qtyExpense += 0;
                }
            }  
        }

        return  ['income' => ['qty' => $qtyIncome, 'total'=> $income],'expense' => ['qty' => $qtyExpense, 'total' => $expense]];
    }
  
    public function browseItem(Request $request)
    {
        try{
            $this->query = $this->getModel()->join('submissions', 'submissions.id', 'items.submission_id')
            ->join('submission_categories','submission_categories.id' ,'=','submissions.category_id')
            ->where('items.company_id', $this->userLogin()->company_id);
            $this->applyCriteria(new SearchCriteria($request));
            $request->total = $this->getTotal();
            $presenter = new DataPresenter(ItemResource::class, $request);
            return $presenter
                ->preparePager()
                ->renderCollection($this->query);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
  
    public function show($id, Request $request)
    {
        try{
		    $this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
            $presenter = new DataPresenter(ItemResource::class, $request);
        
            return $presenter->render($this->query);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
	public function store($request)
	{
        try {
            $item = Item::create($request);
			return $this->show($item->id, $request);
		} catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
			], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
		}
	}

	public function update($id, $request)
	{
		try {
            Item::findOrFail($id)->update($request);
			return $this->show($id, $request);
		} catch (\Exception $e) {
			return response()->json([
                'success' => false,
                'message' => $e->getMessage()
			], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
		}
	}

	public function destroy($id)
	{
		try {
            Item::findOrFail($id)->delete();
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
    
    public function itemSubmission(Request $request)
    {
        try{
            $itemId = $this->getModel()->where('company_id', $this->userLogin()->company_id)->distinct("item_name")->get()->pluck('id');
            $this->query = $this->getModel()->whereIn('id', $itemId);
            
            $this->applyCriteria(new SearchCriteria($request));
            $request->total = ['income' => ['qty' => 0, 'total'=> 0],'expense' => ['qty' => 0, 'total' => 0]];
            $presenter = new DataPresenter(ItemResource::class, $request);
        
            return $presenter
                ->preparePager()
                ->renderCollection($this->query);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
