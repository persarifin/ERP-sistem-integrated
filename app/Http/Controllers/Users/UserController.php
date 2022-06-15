<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\Users\AssignUserRequest;
use App\Http\Requests\Users\ControlUserRequest;
use App\Http\Requests\Users\SearchUserRequest;
use App\Http\Requests\Users\ChangePasswordRequest;
use App\Http\Requests\Users\UpdateProfileRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\Users\SelectionPlayerRequest;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function login(LoginRequest $request)
    {
      return $this->repository->login($request);
    }

    public function register(RegisterRequest $request)
    { 
      return $this->repository->register($request);
    }

    public function index(Request $request)
    {
      return $this->repository->browse($request);
    }

    public function show($id, Request $request)
    {
        return $this->repository->show($id, $request);
    }

    public function store(UserRequest $request)
    {
        return $this->repository->store($request);
    }

    public function update($id, UserRequest $request){
      return $this->repository->update($id, $request);
    }

    public function destroy($id, Request $request){
      return $this->repository->destroy($id, $request);
    }
    
    public function switchCompany(Request $request)
    {
      return $this->repository->switchCompany($request);
    }

    public function browseUserCompany(Request $request)
    {
      return $this->repository->browseUserCompany($request);
    }
    public function customer(Request $request)
    {
      return $this->repository->browseCustomer($request);
    }

    public function searchUser(SearchUserRequest $request)
    {
      return $this->repository->searchUser($request);
    }
    public function searchCustomer(SearchUserRequest $request)
    {
      return $this->repository->searchCustomer($request);
    }

    public function assignUserToCompany(AssignUserRequest $request)
    {
      return $this->repository->assignUserToCompany($request);
    }
    public function changePassword(ChangePasswordRequest $request)
    {
      return $this->repository->changePassword($request);
    }
    public function updateProfile(UpdateProfileRequest $request)
    {
      return $this->repository->updateProfile($request);
    }
    public function deleteUserFromCompany($id)
    {
      return $this->repository->deleteUserFromCompany($id);
    }
    public function updateRoleUser(AssignUserRequest $request)
    {
      return $this->repository->updateRoleUser($request); 
    }
    public function companyAsReseller($id)
    {
      return $this->repository->companyAsReseller($id); 
    }
    public function controlUser($id, ControlUserRequest $request)
    {
      return $this->repository->userControl($id, $request); 
    }
    public function selectionPlayer(SelectionPlayerRequest $request)
    {
      return $this->repository->selectionPlayer($request);
    }
}
