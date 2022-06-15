<?php

namespace App\Jobs;

use App\Entities\User;
use App\Entities\BillingInvoice;
use App\Entities\CompanyWallet;
use App\Entities\ProductCategory;
use App\Entities\SubmissionCategory;
use App\Entities\UserHasCompany;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ActivateCompanyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $companyId;
    protected $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($companyId, $payload)
    {
        $this->companyId = $companyId;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Create default Submission Category for Company
    
        $defaultSubmissionCategory = [
            [
                'category_name' => 'PEMASUKAN',
                'submission_type' => 'INCOME',
                'company_id' => $this->companyId,
            ],
            [
                'category_name' => 'PENGELUARAN',
                'submission_type' => 'EXPENSE',
                'company_id' => $this->companyId,
            ],
        ];

        foreach ($defaultSubmissionCategory as $category) {
            SubmissionCategory::updateOrCreate([
                'category_name' => $category['category_name'],
                'submission_type' => 'EXPENSE',
                'maximum' => 0,
                'company_id' => $category['company_id'],
            ], [
                'category_name' => $category['category_name'],
                'submission_type' => $category['submission_type'],
                'maximum' => 0,
                'company_id' => $category['company_id'],
            ]);
        }

        // Create default Product Category for Company
        ProductCategory::updateOrCreate([
            'category_name' => 'UNCATEGORIZED',
            'company_id' => $this->companyId,
        ],
        [
            'category_name' => 'UNCATEGORIZED',
            "company_id" => $this->companyId,
        ]);

        // Create default Payment Method for Company
        $defaultCompanyWallet = [
            [
                'wallet_name' => 'Cash',
                'company_id' => $this->companyId,
            ],
            [
                'wallet_name' => 'Bank Transfer',
                'company_id' => $this->companyId,
            ],
        ];
        foreach ($defaultCompanyWallet as $companyWallet) {
            CompanyWallet::updateOrCreate(
                [
                    'wallet_name' => $companyWallet['wallet_name'],
                    'company_id' => $companyWallet['company_id'],
                ], $companyWallet);
        }

        // Create billing invoice company on this month
        $now = Carbon::now();
        $firstDayOfMonth = $now->firstOfMonth();
        $lastDayOfMonth = $now->lastOfMonth();
        $foundBillingInvoice = BillingInvoice::where('company_id', $this->companyId)->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])->first();

        if (!$foundBillingInvoice) {
            BillingInvoice::create([
                'invoice_name' => 'Invoice ' . $now,
                'amount' => $this->payload['amount'],
                'date' => $now,
                'is_approved' => false,
                'status' => 'UNPAID',
                'company_id' => $this->companyId,
            ]);
        } else {
            $foundBillingInvoice->amount = $this->payload['amount'];
            $foundBillingInvoice->save();
        }

        // Insert user on this company to User Has Company tabel
        $userOnCompany = User::where("company_id", $this->companyId)->get();
        foreach ($userOnCompany as $user) {
            UserHasCompany::updateOrCreate([
                'user_id' => $user->id,
                'company_id' => $user->company_id
            ],[
                'user_id' => $user->id,
                'company_id' => $user->company_id
            ]);
        }
        $role = Role::where('company_id', $this->companyId)->count();
        if ($role > 0){
            return true;
        }
        foreach (config('permission.roles') as $role) {
            $newRole = Role::create([
                'custom_name'=> $role,
                'name'       => $role.'_'.$this->companyId,
                'guard_name' => 'api', 
                'company_id' => $this->companyId
            ]);
            if ($role === "enterprise") {
                $newRole->givePermissionTo(Permission::whereNotIn('name', array_merge(config('permission.blacklist'), config('permission.unique')))->where('name', 'NOT ILIKE', '%data%')->get());
            }
            else {
                $newRole->givePermissionTo(config('permission.given'));
            }
        }
    }
}
