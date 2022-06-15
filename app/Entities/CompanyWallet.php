<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyWallet extends Model 
{
  use SoftDeletes;
  protected $fillable = [
    'company_id',
    'wallet_name'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class, 'company_id');
  }
  public function payment_transaction()
  {
    return $this->hasMany(PaymentTransaction::class, 'company_wallet_id');
  }
  public function getAmountIncomeAttribute()
  {
    $submission = Submission::join('submission_categories as sc', 'submissions.category_id','=','sc.id')
    ->join('payment_transactions as pt', 'submissions.id','=','pt.submission_id')
    ->where([
      'sc.submission_type' => "INCOME", 
      'pt.company_wallet_id' => $this->id, 
      'pt.company_id' => Auth::user()->company_id ,
      'pt.deleted_at' => null
    ]);
    $request = request()->input('filter_date');
    if($request){
      foreach ($request as $criteria) {
        $request = explode(',', $criteria);
      }
      return $submission->whereBetween('transaction_date',$request)
            ->select('pt.amount')->sum('pt.amount');
    }
    return $submission->select('pt.amount')->sum('pt.amount');
  }
  public function getAmountExpenseAttribute()
  { 
    $submission = Submission::join('submission_categories as sc', 'submissions.category_id','=','sc.id')
    ->join('payment_transactions as pt', 'submissions.id','=','pt.submission_id')
    ->where([
      'sc.submission_type' => "EXPENSE", 
      'pt.company_wallet_id' => $this->id, 
      'pt.company_id' => Auth::user()->company_id ,
      'pt.deleted_at' => null
    ]);
    $request = request()->input('filter_date');
    if($request){
      foreach ($request as $criteria) {
        $request = explode(',', $criteria);
      }
      return $submission->whereBetween('transaction_date',$request)
            ->select('pt.amount')->sum('pt.amount');
    }
    return $submission->select('pt.amount')->sum('pt.amount');
  }
  public function getMarginAttribute()
  {
    return ((float)$this->amount_income - (float)$this->amount_expense);
  }
  public function getBalanceAttribute()
  {    
    $reconFrom  = PaymentReconciliation::where(['from_wallet_id' => $this->id]);
    $reconTo    = PaymentReconciliation::where(['to_wallet_id' => $this->id]);
    $request = request()->input('filter_date');
    if($request){
      foreach ($request as $criteria) {
        $request = explode(',', $criteria);
      }
      $reconFrom->whereBetween('created_at', $request);
      $reconTo->whereBetween('created_at', $request);
      return $this->margin + ($reconTo->sum('amount') - $reconFrom->sum('amount'));
    }

    return $this->margin + ($reconTo->sum('amount') - $reconFrom->sum('amount'));
  }
  protected $appends = ['amount_expense','amount_income','margin','balance'];
}