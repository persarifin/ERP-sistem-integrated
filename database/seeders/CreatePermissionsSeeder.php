<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Entities\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class CreatePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $tableWithPermissions = [
        "companies" => [
          "Create Companies",
          "Read Companies",
          "Read All Companies", 
          "Update All Companies", 
          "Update Companies",
          "Delete Companies",
          "Activation Companies",
        ],
        "permissions" => [
          "Read Permissions",
          "Read All Permissions" // blacklist permission  
        ],
        "roles" => [
          "Create Roles",
          "Read Roles",
          "Read All Roles", // blacklist permission
          "Update Roles",
          "Update All Roles",
          "Delete Roles"
        ],
        "company_wallets" => [
          "Create Company Wallets",
          "Read Company Wallets",
          "Update Company Wallets",
          "Delete Company Wallets"
        ],
        "users" => [
          "Create Users",
          "Read Users",
          "Read All Users", // blacklist permission,
          "Update Users",
          "Delete Users",
          "Assign User To Company",
          "Delete User From Company",
          "Update User Role",
          "Assign Company As Reseller"
        ],
        "user_wallets" => [
          "Read User Wallets",
          "Create User Wallets"
        ],
        "billing_counters" => [
          "Create Billing Counters",
          "Read Billing Counters",
          "Read All Billing Counters",
          "Update Billing Counters",
          "Delete Billing Counters"
        ],
        "billing_invoices" => [
          "Create Billing Invoices",
          "Read Billing Invoices",
          "Read All Billing Invoices", // blacklist permission
          "Update Billing Invoices",
          "Update Status Billing Invoices",
          "Approve Billing Invoices"
        ],
        "content_categories" =>[
          "Read Content Categories",
          "Create Content Categories",
          "Update Content Categories",
          "Delete Content Categories"
        ],
        "contents" =>[
          "Read Contents",
          "Create Contents",
          "Update Contents",
          "Delete Contents"
        ],
        "interfaces" => [
          "Access Interface WEB P.O.S",
          "Access Interface MOBILE PARKING",
          "Access Interface WEB COMPANY",
          "Access Interface MOBILE",
          "Access Interface NOWHERE",
        ],
        "items" => [
          "Read Items",
        ],
        "schedules" => [
          "Read Product Schedules"
        ],
        "payment transactions" =>[
          "Read Payment Transactions",
          "Create Payment Transactions",
          "Delete Payment Transactions"
        ],
        "payment reconciliations" =>[
          "Read Payment Reconciliations",
          "Create Payment Reconciliations",
          "Delete Payment Reconciliations"
        ],
        "product categories" => [
          "Read Product Categories",
          "Create Product Categories",
          "Update Product Categories",
          "Delete Product Categories",
        ],
        "products" => [
          "Read Products",
          "Create Products",
          "Update Products",
          "Delete Products",
          "Update Product After APPROVED",
          "Update Product Status To PENDING",
          "Update Product Status To PARTIAL APPROVED",
          "Update Product Status To APPROVED"
        ],
        "submission Income" => [
          "Read All Submission INCOME",
          "Read Submission INCOME",
          "Create Submission INCOME",
          "Create Submission INCOME With Status APPROVED",
          "Update All Submission INCOME",
          "Update Submission INCOME",
          "Delete All Submission INCOME",
          "Delete Submission INCOME",
          "Update Submission INCOME Status To REFUND",
          "Update Submission INCOME Status To CANCELLED",
          "Update Submission INCOME Status To PARTIAL APPROVED",
          "Update Submission INCOME Status To APPROVED",
          "Update Submission INCOME Status To COMPLETED",
          "Update Submission INCOME Status To REJECTED",
          "Update Submission INCOME Status REJECTED To PENDING",
          "Update Submission INCOME Fullfilment To FULLFILLED",
          "Update Submission INCOME Fullfilment To UNFULLFILLED",
          "Update Submission INCOME After APPROVED, Before COMPLETED And Before FULLFILLED", //=> special permission
          "Update Submission INCOME Due Date"
        ],
        "submission Expense" => [
          "Read All Submission EXPENSE",
          "Read Submission EXPENSE",
          "Create Submission EXPENSE",
          "Create Submission EXPENSE With Status APPROVED",
          "Update All Submission EXPENSE",
          "Update All Submission EXPENSE",
          "Delete All Submission EXPENSE",
          "Delete All Submission EXPENSE",
          "Update Submission EXPENSE Status To REFUND",
          "Update Submission EXPENSE Status To CANCELLED",
          "Update Submission EXPENSE Status To PARTIAL APPROVED",
          "Update Submission EXPENSE Status To APPROVED",
          "Update Submission EXPENSE Status To COMPLETED",
          "Update Submission EXPENSE Status To REJECTED",
          "Update Submission EXPENSE Status REJECTED To PENDING",
          "Update Submission EXPENSE Fullfilment To FULLFILLED",
          "Update Submission EXPENSE Fullfilment To UNFULLFILLED",
          "Update Submission EXPENSE After APPROVED, Before COMPLETED And Before FULLFILLED", //=> special permission
          "Update Submission EXPENSE Due Date"
        ],
        "submission categories" => [
          "Read Submission Categories",
          "Create Submission Categories",
          "Update Submission Categories",
          "Delete Submission Categories"
        ],
      ];
      
      foreach($tableWithPermissions as $key => $value)
      {      
        foreach($tableWithPermissions[$key] as $permission)
        {
          Permission::updateOrCreate(
            ['name' => $permission],
            ['name' => $permission]
          );
        }
      }
      $this->command->getOutput()->writeln("<info>Create ".count(Permission::all())." permissions.</info>");
    }
}
