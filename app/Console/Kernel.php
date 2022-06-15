<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Entities\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function(){
          $userEnterprise = User::whereHas('roles', function($query){
            $query->where('custom_name', 'enterprise');
          })->whereHas('company' ,function($q){
            $q->whereNotNull('companies.email_verified_at')
            ->whereNotNull('companies.phone_verified_at');
          })->get();
          
          foreach($userEnterprise as $enterprise)
          {
            $company = DB::table('companies')->where('id', $enterprise->company_id)->first();            
            $firstBillingInvoice = DB::table('billing_invoices')->where('company_id', $company->id)->oldest()->first(); 
            
            if(!$firstBillingInvoice)
            {
              continue;
            }
            
            DB::table('billing_invoices')->insert([
              "invoice_name" => "Invoice ".date('F Y'),
              "amount" => $firstBillingInvoice->amount,
              "date" => Carbon::now()->firstOfMonth(),
              "is_approved" => 1,
              "status" => "UNPAID",
              "company_id" => $company->id,
              "created_at" => Carbon::now(),
              "updated_at" => Carbon::now()
            ]);
          }
          
        })->monthlyOn(date('t'), '00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
