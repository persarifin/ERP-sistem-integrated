<?php

namespace App\Jobs;

use App\Entities\User;
use App\Entities\UserHasCompany;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeactivateCompanyJob implements ShouldQueue
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
        $companyId = $this->companyId;

        $userOnCompany = User::whereHas('user_has_company', function ($query) use ($companyId) {
            return $query->where('company_id', '=', $companyId);
        })->get();

        foreach ($userOnCompany as $user) {
            // if user has role enterprise, skip...
            if ($user->hasRole('enterprise_' . $user->company_id)) {
                continue;
            }

            // check if user has another company they have
            $anotherUserHasCompany = UserHasCompany::where([['user_id', '=', $user->id], ['company_id', '!=', $companyId]])->get();
            // set company id user to another company they have
            if (count($anotherUserHasCompany) > 0) {
                $user->company_id = $anotherUserHasCompany[0]->company_id;
                $user->save();
            } else {
                // set company id user to 0 if user dont have/work to another company
                $user->company_id = 0;
                $user->save();
            }

            // detach role user have on this company
            $foundUserRoles = $user->roles->where('company_id', '!=', $user->company_id)->pluck('name');
            $user->syncRoles($foundUserRoles);
        }
        // delete user has companies
        // UserHasCompany::where(['company_id' => $companyId])->delete();
    }
}
