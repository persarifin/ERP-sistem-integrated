<?php

namespace App\Http\Controllers\Submissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Submissions\SubmissionRequest;
use App\Http\Requests\Submissions\UpdateSubmissionStatusRequest;
use App\Http\Requests\Submissions\UpdateSubmissionDueDateRequest;
use App\Http\Requests\Submissions\BulkSubmissionRequest;
use App\Entities\Submission;
use App\Entities\PaymentTransaction;
use App\Repositories\Submissions\SubmissionRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DateTime;
use DB;

class SubmissionController extends Controller{

  public function __construct(SubmissionRepository $repository)
  {
      $this->repository = $repository;
  }

  public function index(Request $request)
  {
    return $this->repository->browse($request);
  }

  public function getAllSubmission(Request $request)
  {
    return $this->repository->browse($request);
  } 

  public function store(SubmissionRequest $request)
  {
    return $this->repository->store($request);
  }
  public function show($id, Request $request)
  {
    return $this->repository->show($id, $request);
  }

  public function update(SubmissionRequest $request, $id)
  {
    return $this->repository->update($id, $request);
  }

  public function updateStatus($id, UpdateSubmissionStatusRequest $request)
  {
    return $this->repository->updateStatus($id, $request);
  }
  public function updateDueDate($id, UpdateSubmissionDueDateRequest $request)
  {
    return $this->repository->updateDueDate($id, $request);
  }
  public function updateFullfilment(Request $request, $id)
  {
    return $this->repository->updateFullfilment($id, $request);
  }

  public function bulkSubmission(BulkSubmissionRequest $request)
  {
    return $this->repository->bulk($request);
  }

  public function destroy($id)
  {
    return $this->repository->destroy($id);
  }
  public function dashboard(Request $request)
  {
    try {
      $monthYear = [];
      $minYear = 0;
      $type = ['INCOME','EXPENSE'];
      $monthNow = (int) date('m');
      $yearNow = (int) date('Y');
      $submissions = Submission::join('submission_categories','submissions.category_id','=','submission_categories.id')
      ->where(['submissions.company_id' => Auth::user()->company_id])->select('submissions.*','submission_type')->with('payment_transaction');
      if ($request->year < 0 && $request->year != 0) {
        for($i = $monthNow-11; $i <= $monthNow; $i++){
          $zone = mktime(0, 0, 0, $i, 1, $yearNow);
          $monthYear[] = date('Y-m-d', $zone);
        }
        foreach ($type as $submission_type) {
          foreach ($monthYear as $number=>$date) {
            $data['trending'][$submission_type][$number] = [
            'date' => date('m/Y', strtotime($date)),
            'total' =>PaymentTransaction::join('submissions','payment_transactions.submission_id','=','submissions.id')
            ->join('submission_categories', 'submissions.category_id','=','submission_categories.id')
            ->where(['submissions.company_id' => Auth::user()->company_id, 'submission_type' => $submission_type])
            ->where('submissions.deleted_at' , NULL)
            ->whereMonth('submissions.due_date', date('m',strtotime($date)))
            ->whereYear('submissions.due_date', date('Y',strtotime($date)))
            ->sum('payment_transactions.amount')
            ];
          }
        }
        $submissions->whereYear('due_date', Carbon::now()->subYear()->year+1+$request->year);
      }
      elseif (isset($request->month) && isset($request->year)) {
        for($d=1; $d<=31; $d++){
          $time=mktime(12, 0, 0, $request->month, $d, $request->year);          
          if (date('m', $time)==$request->month)       
              $monthYear[] =date('Y-m-d', $time);
        }
        foreach ($type as $submission_type) {
          foreach ($monthYear as $number=>$date) {
            $data['trending'][$submission_type][$number] = [
            'date' => date('d/m/Y', strtotime($date)),
            'total' =>PaymentTransaction::join('submissions','payment_transactions.submission_id','=','submissions.id')
            ->join('submission_categories', 'submissions.category_id','=','submission_categories.id')
            ->where(['submissions.company_id' => Auth::user()->company_id, 'submission_type' => $submission_type])
            ->where('submissions.deleted_at' , NULL)
            ->whereDate('submissions.due_date', $date)
            ->sum('payment_transactions.amount')
            ];
          }
        }
        $submissions->whereMonth('due_date',$request->month)->whereYear('due_date', $request->year);
      }
      elseif (empty($request->month) && isset($request->year)) {
        for($i = 1; $i <= 12; $i++){
          $zone = mktime(0, 0, 0, $i, 1, $request->year);
          $monthYear[] = date('Y-m-d', $zone);
        }
        foreach ($type as $submission_type) {
          foreach ($monthYear as $number=>$date) {
            $data['trending'][$submission_type][$number] = [
            'date' => date('m/Y', strtotime($date)),
            'total' => PaymentTransaction::join('submissions','payment_transactions.submission_id','=','submissions.id')
            ->join('submission_categories', 'submissions.category_id','=','submission_categories.id')
            ->where(['submissions.company_id' => Auth::user()->company_id, 'submission_type' => $submission_type])
            ->where('submissions.deleted_at' , NULL)
            ->whereMonth('submissions.due_date', date('m',strtotime($date)))
            ->whereYear('submissions.due_date', date('Y',strtotime($date)))
            ->sum('payment_transactions.amount')
            ];
          }
        }
        $submissions->whereYear('due_date', $request->year);
      }
      else{
        $start = Submission::where(['submissions.company_id'=> Auth::user()->company_id])->orderByDesc('submissions.id')->first();
        $end = Submission::where(['submissions.company_id'=> Auth::user()->company_id])->orderBy('submissions.id')->first();
        $datetime1 = new DateTime($start->due_date);
        $datetime2 = new DateTime($end->due_date);
        $interval = $datetime1->diff($datetime2);
        $days = $interval->format('%a');
        if ($days < 31 && $days > 0) {
          for($d=1; $d<=31; $d++){
            $time=mktime(12, 0, 0, $monthNow, $d, $yearNow);          
            if (date('m', $time)==$monthNow)       
                $monthYear[] =date('Y-m-d', $time);
          }
          foreach ($type as $submission_type) {
            foreach ($monthYear as $number=>$date) {
              $data['trending'][$submission_type][$number] = [
              'date' => date('d/m/Y', strtotime($date)),
              'total' =>PaymentTransaction::join('submissions','payment_transactions.submission_id','=','submissions.id')
              ->join('submission_categories', 'submissions.category_id','=','submission_categories.id')
              ->where(['submissions.company_id' => Auth::user()->company_id, 'submission_type' => $submission_type])
              ->where('submissions.deleted_at' , NULL)
              ->whereDate('submission.due_date', $date)
              ->sum('payment_transactions.amount')
              ];
            }
          }
        }
        elseif ($days > 31 && $days <= 365) {
          for($i = $monthNow-11; $i <= $monthNow; $i++){
            $zone = mktime(0, 0, 0, $i, 1, $yearNow);
            $monthYear[] = date('Y-m-d', $zone);
          }
          foreach ($type as $submission_type) {
            foreach ($monthYear as $number=>$date) {
              $data['trending'][$submission_type][$number] = [
              'date' => date('m/Y', strtotime($date)),
              'total' =>PaymentTransaction::join('submissions','payment_transactions.submission_id','=','submissions.id')
              ->join('submission_categories', 'submissions.category_id','=','submission_categories.id')
              ->where(['submissions.company_id' => Auth::user()->company_id, 'submission_type' => $submission_type])
              ->where('submissions.deleted_at' , NULL)
              ->whereMonth('submissions.due_date', date('m',strtotime($date)))
              ->whereYear('submissions.due_date', date('Y',strtotime($date)))
              ->sum('payment_transactions.amount')
              ];
            }
          }
        }
        elseif($days > 365) {
          $diff = abs(strtotime($end->due_date) - strtotime($start->due_date));
          $years = floor($diff / (365*60*60*24));
          do {
            $years++;
            while ($years % 5) {
              $years++;
            }
          } while ($years % 2);
          for ($i= $years-1; $i >= 0; $i--) { 
            $zone = mktime(0, 0, 0, 1, 1, $yearNow-$i);
            $monthYear[] =  date('Y-m-d', $zone);
          }
          foreach ($type as $submission_type) {
            foreach ($monthYear as $number=>$date) {
              $data['trending'][$submission_type][$number] = [
              'date' => date('Y', strtotime($date)),
              'total' => PaymentTransaction::join('submissions','payment_transactions.submission_id','=','submissions.id')
              ->join('submission_categories', 'submissions.category_id','=','submission_categories.id')
              ->where(['submissions.company_id' => Auth::user()->company_id, 'submission_type' => $submission_type])
              ->where('submissions.deleted_at' , NULL)
              ->whereYear('submissions.due_date', date('Y', strtotime($date)))
              ->sum('payment_transactions.amount')
              ];
            }
          }
        }
      }
      $income['total_income'] = 0;
      $income['potential'] = 0;
      $income['partial'] = 0;
      $income['paid'] = 0;
      $income['unpaid'] = 0;
      $income['complete'] = 0;
      $income['rejected'] = 0;
      $expense['total_expense'] = 0;
      $expense['potential'] = 0;
      $expense['partial'] = 0;
      $expense['paid'] = 0;
      $expense['unpaid'] = 0;
      $expense['complete'] = 0;
      $expense['rejected'] = 0;
      $amountSubmissionPartialIncome = 0;
      $amountSubmissionPartialExpense = 0; 
      foreach ($submissions->get() as $submission) {
        $payment = 0;
        foreach ($submission->payment_transaction as $transaction) {
          $payment += $transaction->amount;
        }
        if ($submission->submission_type == 'INCOME') {
          $income['total_income'] += $payment;
          if (in_array($submission->status , array('DRAFT','PENDING','PARTIAL APPROVED','APPROVED'))) {
            $income['potential'] += $submission->amount;
          }
          elseif($submission->status == 'PARTIAL PAID'){
            $income['partial'] += $payment;
            $amountSubmissionPartialIncome += $submission->amount;
          }
          elseif($submission->status == 'PAID'){
            $income['paid'] += $payment;
          }
          elseif($submission->status == 'COMPLETED'){
            $income['complete'] += $payment;
          }
          else{
            $income['rejected'] += $payment;
          }
        }
        elseif($submission->submission_type == 'EXPENSE'){
          $expense['total_expense'] += $payment;
          if (in_array($submission->status , array('DRAFT','PENDING','PARTIAL APPROVED','APPROVED'))) {
            $expense['potential'] += $submission->amount;
          }
          elseif($submission->status == 'PARTIAL PAID'){
            $expense['partial'] += $payment;
            $amountSubmissionPartialExpense += $submission->amount;
          }
          elseif($submission->status == 'PAID'){
            $expense['paid'] += $payment;
          }
          elseif($submission->status == 'COMPLETED'){
            $expense['complete'] += $payment;
          }
          else{
            $expense['rejected'] += $payment;
          }
        }
        $income['unpaid'] = ($amountSubmissionPartialIncome - $income['partial']);
        $expense['unpaid'] = ($amountSubmissionPartialExpense - $expense['partial']);
      }
      // return $income['unpaid'];
      $data['statistic'] = ['income' => $income, 'expenses' => $expense];

      $month = (int) date('m');
      $year = (int) date('Y');
      $day = (int) date('d');

      for($d = $day; $d < ($day + 30); $d++)
      {
        $d > 30? $d-30 : $d;
        $time = mktime(12, 0, 0, $month-1, $d, $year);
        $time2 = mktime(12, 0, 0, $month-2, $d, $year);          
        $newMonth[] = date('Y-m-d', $time);
        $lastMonth[] = date('Y-m-d', $time2);
      }
      $now_income = Submission::join('submission_categories', 'submission_categories.id','=','submissions.category_id')
      ->join('payment_transactions','payment_transactions.submission_id','submissions.id')
      ->where(['submissions.company_id'=> Auth::user()->company_id, 'submission_type' => 'INCOME'])
      ->whereBetween('submissions.due_date',[$newMonth[0],end($newMonth)])
      ->sum('payment_transactions.amount');
      $last_income = Submission::join('submission_categories', 'submission_categories.id','=','submissions.category_id')
      ->join('payment_transactions','payment_transactions.submission_id','submissions.id')
      ->where(['submissions.company_id'=> Auth::user()->company_id, 'submission_type' => 'EXPENSE'])
      ->whereBetween('submissions.due_date',[$lastMonth[0],end($lastMonth)])
      ->sum('payment_transactions.amount');
      $now_expense = Submission::join('submission_categories', 'submission_categories.id','=','submissions.category_id')
      ->join('payment_transactions','payment_transactions.submission_id','submissions.id')
      ->where(['submissions.company_id'=> Auth::user()->company_id, 'submission_type' => 'INCOME'])
      ->whereBetween('submissions.due_date',[$newMonth[0],end($newMonth)])
      ->sum('payment_transactions.amount');
      $last_expense = Submission::join('submission_categories', 'submission_categories.id','=','submissions.category_id')
      ->join('payment_transactions','payment_transactions.submission_id','submissions.id')
      ->where(['submissions.company_id'=> Auth::user()->company_id, 'submission_type' => 'EXPENSE'])
      ->whereBetween('submissions.due_date',[$lastMonth[0],end($lastMonth)])
      ->sum('payment_transactions.amount');

      $presentaseIncome = (($last_income != 0) ? (($now_income - $last_income)/$last_income) * 100/100: (($now_income != 0) ? 100 : 0));
      $statusIncome = ((($now_income - $last_income) > 0) ? 1 : ((($now_income - $last_income) < 0)? 0 : null));
      $presentaseExpense = (($last_expense != 0) ?(($now_expense - $last_expense)/$last_expense) * 100/100: (($now_expense != 0) ? 100 : 0));
      $statusExpense = ((($now_expense - $last_expense) > 0) ? 1 : ((($now_expense - $last_expense) < 0)? 0 : null));
      $now_balance = $now_income - $now_expense;
      $presentaseBalance = ((($last_income - $last_expense) != 0) ? ($now_balance/($last_income - $last_expense)) * 100/100 : (($now_balance != 0) ? 100 : 0));
      $statusBalace = ((($now_balance - ($last_income - $last_expense)) > 0) ? 1 : ((($now_balance - ($last_income - $last_expense)) < 0)? 0 : null));

      $data['presentase'] = [
        'income' => [
          'total'   => $income['total_income'],
          'status'  => $statusIncome,
          'percent' => $presentaseIncome    
        ],
        'expense' => [
          'total'   => $expense['total_expense'], 
          'status'  => $statusExpense,
          'percent' => $presentaseExpense       
        ],
        'balance' => [
          'total'   => $income['total_income']-$expense['total_expense'],  
          'status'  => $statusBalace,
          'percent' => $presentaseBalance      
        ],
      ];

      $data['transaction'] = PaymentTransaction::join('company_wallets as cw','payment_transactions.company_wallet_id','=','cw.id')->join('submissions','submissions.id','=','submission_id')
      ->join('submission_categories', 'submission_categories.id','=','submissions.category_id')
      ->where(['submissions.company_id'=> Auth::user()->company_id])
      ->select('payment_transactions.id','submission_name','payment_transactions.amount','wallet_name','submission_type')
      ->orderBy('payment_transactions.created_at','desc')->limit(10)->get();

      return [
        'success' => true,
        'data' => $data,
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
      return response()->json([
          'success' => false,
          'message' => $e->getMessage(),
      ], 400);
    }
  }
}
