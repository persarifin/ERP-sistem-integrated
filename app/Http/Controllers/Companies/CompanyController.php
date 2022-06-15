<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Entities\Company;
use App\Entities\ProductCategory;
use App\Repositories\CompanyRepository;
use App\Repositories\Products\ProductCategoryRepository;
use App\Repositories\Products\ProductRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;

class CompanyController extends Controller 
{
  public $successStatus = 200;

  public function __construct(CompanyRepository $repository,UserRepository $userRepository,ProductRepository $productRepository, ProductCategoryRepository $productCategoryRepository)
  {
    $this->repository = $repository;
    $this->categoryRepository = $productCategoryRepository;
    $this->productRepository = $productRepository;
    $this->userRepository = $userRepository;
  }

  public function index(Request $request)
  {
    return $this->repository->browse($request);
  }
  public function show($id, Request $request)
  {
    return $this->repository->show($id, $request);
  }

  public function store(CompanyRequest $request) {
    return $this->repository->store($request);
  }

  public function update($id, CompanyRequest $request)
  {
    return $this->repository->update($id, $request);
  }

  public function destroy($id)
  {
    return $this->repository->destroy($id);
  }
  public function companyLandingPage($id)
  {
    try {
      $query = Company::where('id', $id)->with(['company_attachments' => function($query){
        $query->where('type', 'MAIN_IMAGE');
      },'product_category.product_category_attachment', 'product_category' => function($query){
        $query->orderBy('id','desc')->limit(3)->offset(0);
      }]);
      $total = $query->count(); 
      $data = $query->first();
      return [
        'success' => $total > 0 ? true : false,
        'data' => $data,
        'included' => [],
        'meta' => [
          'relations' => [],
          'available_relations' => [],
          'links' => [
              'self' => url()->current(),
          ],
        ],
      ];      
    } catch (Throwable $th) {
			return response()->json([
        'success' => false,
				'message' => $th
      ]);
		}
  }
	public function ProductCategoryLandingPage($companyId, Request $request)
  {
    return $this->categoryRepository->ProductCategoryLandingPage($companyId, $request);
  }
  public function productLandingPage($categoryId, Request $request)
  {
    return $this->ProductRepository->productLandingPage($categoryId, $request);
  }
  public function landingPageUserCompany($companyId, Request $request)
  {
    return $this->userRepository->landingPageUserCompany($companyId, $request);
  }

  public function toggleStatusCompany($id, Request $request) 
  {
    return $this->repository->toggleStatusCompany($id, $request);
  }
}