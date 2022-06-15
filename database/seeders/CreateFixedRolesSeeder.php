<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateFixedRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      /*
        'super_enterprise',
        'super_admin',
        'enterprise',
        'manager',
        'accountant',
        'executive',
        'cashier',
        'officer',
        'purchasing',
        'stockist',
        'organizer',
        'creator',
        'contributor',
        'verified',
        'unverified'
      */
        $fixRoles = [
          [
            'name' => 'super_enterprise', 
            'custom_name' => 'super_enterprise',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'super_admin', 
            'custom_name' => 'super_admin',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'enterprise', 
            'custom_name' => 'enterprise',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'manager', 
            'custom_name' => 'manager',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'accountant', 
            'custom_name' => 'accountant',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'executive', 
            'custom_name' => 'executive',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'cashier', 
            'custom_name' => 'cashier',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'officer', 
            'custom_name' => 'officer',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'purchasing', 
            'custom_name' => 'purchasing',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'stockist', 
            'custom_name' => 'stockist',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'organizer', 
            'custom_name' => 'organizer',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'creator', 
            'custom_name' => 'creator',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'contributor', 
            'custom_name' => 'contributor',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'verified', 
            'custom_name' => 'verified',
            'guard_name' => 'api', 
            'company_id' => 0
          ],
          [
            'name' => 'unverified', 
            'custom_name' => 'unverified',
            'guard_name' => 'api', 
            'company_id' => 0
          ]
        ];
        foreach($fixRoles as $role)
        {
          Role::updateOrCreate(
          [
            'name' => $role['name'],
            'company_id' => $role['company_id']
          ],
          [
            'name' => $role['name'],
            'guard_name' => $role['guard_name'],
            'custom_name' => $role['custom_name'],
            'company_id' => $role['company_id']
          ]);
        }
    }
}
