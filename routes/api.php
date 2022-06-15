<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('register', 'Users\UserController@register');
Route::post('login', 'Users\UserController@login');
Route::put('forgot-password', 'Users\UserController@changePassword');

Route::post('email-congress', 'EmailController@confirmationRegistration');
Route::get('landing-page/product/{category}', 'Companies\CompanyController@productLandingPage');
Route::get('landing-page/user-role/{company}', 'Companies\CompanyController@landingPageUserCompany');
Route::get('landing-page/company/{id}', 'Companies\CompanyController@companyLandingPage');
Route::get('landing-page/product-categories/{company}', 'Companies\CompanyController@ProductCategoryLandingPage');
Route::post('selection-player', 'Users\UserController@selectionPlayer');
Route::get('proof-selection-player', 'PdfController@index');

Route::group(['middleware' => ['auth:api']], function(){
    // ===== Submission =====
    // Submission
    Route::resource('submissions', 'Submissions\SubmissionController')->except('index');
    Route::match(['get','post'],'get-submissions', 'Submissions\SubmissionController@index');
    Route::put('update-statuses/{id}', 'Submissions\SubmissionController@updateStatus');
    Route::put('update-due-date/{id}', 'Submissions\SubmissionController@updateDueDate');
    Route::post('bulk-submissions', 'Submissions\SubmissionController@bulkSubmission');
    Route::put('update-fullfilments/{id}', 'Submissions\SubmissionController@updateFullfilment');
    Route::match(['get','post'],'dashboard-submissions', 'Submissions\SubmissionController@dashboard');

    // Submission category
    Route::resource('submission-categories', 'Submissions\SubmissionCategoryController')->except('index');
    Route::match(['get','post'],'get-submission-categories', 'Submissions\SubmissionCategoryController@index');
    // Submission attachment
    Route::resource('submission-attachments', 'Submissions\SubmissionAttachmentController')->except('index','update');
    Route::match(['get','post'],'get-submission-attachments', 'Submissions\SubmissionAttachmentController@index');

    // Submission comment
    Route::resource('submission-comments', 'Submissions\SubmissionCommentController')->except('index');
    Route::match(['get','post'],'get-submission-comments', 'Submissions\SubmissionCommentController@index');
    
    // ===== End Submission =====    

    // Billing Counter
    Route::get('billing-counters', 'BillingCounters\BillingCounterController@index');
    Route::get('billing-counters/{id}/company', 'BillingCounters\BillingCounterController@billingCounterByCompany');
    
    // Billing Invoice
    Route::resource('billing-invoices', 'BillingInvoices\BillingInvoiceController');
    Route::put('update-status-billing-invoices/{id}', 'BillingInvoices\BillingInvoiceController@updateStatusBillingInvoice');

    // Role
    Route::resource('roles', 'Roles\RoleController');

    // Permission
    Route::resource('permissions', 'Permissions\PermissionController')->only('index');

    // Company
    Route::resource('companies', 'Companies\CompanyController');
    // => toggle activated or deactivated company
    Route::put('toggle-status-company/{id}', 'Companies\CompanyController@toggleStatusCompany');

    // Company Attachment
    Route::resource('company-attachments', 'Companies\CompanyAttachmentController')->except('update');
    Route::post('company-attachments/{id}', 'Companies\CompanyAttachmentController@update');
    Route::get('company-attachments/{id}/company', 'Companies\CompanyAttachmentController@companyAttachment');
    
    // Company Wallet
    Route::resource('company-wallets', 'Companies\CompanyWalletController')->except('index');
    Route::match(['get','post'],'get-company-wallets', 'Companies\CompanyWalletController@index');

    // ===== User =====
    // => CRUD user
    Route::resource('users', 'Users\UserController');
    Route::post('user-search', 'Users\UserController@searchUser');
    Route::post('change-password', 'Users\UserController@changePassword');
    Route::post('update-profile', 'Users\UserController@updateProfile');
    Route::match(['get','post'],'get-user-customer', 'Users\UserController@customer');
    Route::post('update-user-role', 'Users\UserController@updateRoleUser');
    Route::put('assign-company-reseller/{company}', 'Users\UserController@companyAsReseller');
    Route::delete('delete-user-from-company/{user}', 'Users\UserController@deleteUserFromCompany');
    
    // => get users self/user company
    Route::match(['get','post'],'get-users', 'Users\UserController@browseUserCompany');
    // => search customers
    Route::get('search-customer', 'Users\UserController@searchCustomer');
    // => login by company or switch company user have
    Route::post('switch-company', 'Users\UserController@switchCompany');
    // => assign user to specific company
    Route::post('assign-user-to-company', 'Users\UserController@assignUserToCompany');
    // User Attachment
    Route::resource('user-attachments', 'Users\UserAttachmentController')->except('update');
    Route::post('user-attachments/{id}', 'Users\UserAttachmentController@update');
    // ===== End User =====

    // ===== Content =====
    // Content 
    Route::resource('contents', 'Contents\ContentController')->except('index');
    Route::match(['get','post'],'get-contents', 'Contents\ContentController@index');
    // Content Attachment
    Route::resource('content-attachments', 'Contents\ContentAttachmentController')->except('index','update');
    Route::match(['get','post'],'get-content-attachments', 'Contents\ContentAttachmentController@index');
    //Content Category
    Route::resource('content-categories', 'Contents\ContentCategoryController')->except('index');
    Route::match(['get','post'],'get-content-categories', 'Contents\ContentCategoryController@index');
    //Content Category Attachment
    Route::resource('content-category-attachments', 'Contents\ContentCategoryAttachmentController')->except('index','update');
    Route::match(['get','post'],'get-content-category-attachments', 'Contents\ContentCategoryAttachmentController@index');
    //Content Visibility
    Route::resource('content-visibilities', 'Contents\ContentVisibilityController')->except('index','update');
    Route::match(['get','post'],'get-content-visibilities', 'Contents\ContentVisibilityController@index');
    // Content Comment
    Route::resource('content-comments', 'Contents\ContentCommentController')->except('index');
    Route::match(['get','post'],'get-content-comments', 'Contents\ContentCommentController@index');
    // ===== End Content =====

    // ===== Interface =====
    //Interface 
    Route::resource('interfaces', 'InterfaceApps\InterfaceController')->only('index');
    Route::group(['middleware' => ['role:super_enterprise']], function () {
        Route::post('interfaces', 'InterfaceApps\InterfaceController@store');
    });

    // ===== End Interface =====

    // ===== Items =====
    // Items 
    Route::resource('items', 'Items\ItemController')->except('index');
    Route::match(['get','post'],'browse-items', 'Items\ItemController@index');
    Route::match(['get','post'],'get-items', 'Items\ItemController@browseItem');
    Route::match(['get','post'],'item-submissions', 'Items\ItemController@itemSubmission');


    //Item Attachment
    Route::resource('item-attachments', 'Items\ItemAttachmentController')->except('index','update');
    Route::match(['get','post'],'get-item-attachments', 'Items\ItemAttachmentController@index');
    // ===== End Items =====

    // ===== Product =====
    //Product
    Route::resource('products', 'Products\ProductController')->except('index');
    Route::match(['get','post'],'get-products', 'Products\ProductController@index');
    Route::put('status-products/{id}','Products\ProductController@updateStatus');
    //Product Attachment
    Route::resource('product-attachments', 'Products\ProductAttachmentController')->except('index','update');
    Route::match(['get','post'],'get-products-attachments', 'Products\ProductAttachmentController@index');
    //Product Category
    Route::resource('product-categories', 'Products\ProductCategoryController')->except('index');
    Route::match(['get','post'],'get-product-categories', 'Products\ProductCategoryController@index');
    //Product Category Attachment
    Route::resource('product-category-attachments', 'Products\ProductCategoryAttachmentController')->except('index','update');
    Route::match(['get','post'],'get-product-category-attachments', 'Products\ProductCategoryAttachmentController@index');
    //Product Visibility
    Route::resource('product-visibilities', 'Products\ProductVisibilityController')->except('index','update');
    Route::match(['get','post'],'get-product-visibilities', 'Products\ProductVisibilityController@index');
    //Product Schedule
    Route::resource('product-schedules', 'Products\ProductScheduleController')->except('index');
    Route::match(['get','post'],'get-product-schedules', 'Products\ProductScheduleController@index');
    // ===== End Product =====

    // ===== Payment =====
    //Payment Transaction
    Route::resource('payment-transactions', 'Payments\PaymentTransactionController')->except('index','update');
    Route::match(['get','post'],'get-payment-transactions', 'Payments\PaymentTransactionController@index');
    //payment Transaction Attachment
    Route::resource('payment-transaction-attachments', 'Payments\PaymentTransactionAttachmentController')->except('index','update');
    Route::match(['get','post'],'get-payment-transactions-attachments', 'Payments\PaymentTransactionAttachmentController@index');
    //Payment Reconciliation
    Route::resource('payment-reconciliations', 'Payments\PaymentReconciliationController')->except('index');
    Route::match(['get','post'],'get-payment-reconciliations', 'Payments\PaymentReconciliationController@index');
    // ===== End Payment =====

    // ===== Read Status =====
    //Read Status
    Route::resource('read-statuses', 'Statuses\ReadStatusController')->only('store','delete');
    // ===== End Status =====
});
