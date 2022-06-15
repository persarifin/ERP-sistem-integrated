<?php

namespace App\Repositories;

use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Role::class);
    }

    public function browse(Request $request)
    {
        try {
            $this->query = $this->getModel()->where('company_id', $this->userLogin()->company_id)->where('name', '!=', 'super_enterprise');
            if ($this->roleHasPermission("Read All Roles")) {
                $this->query = $this->query;
            } else if ($this->roleHasPermission("Read Roles")) {
                $this->query = $this->query->whereNotIn('custom_name',['enterprise','super_admin','unverified']);
            } else {
                throw new \Exception("User does not have the right permission.", 403);
            }
            $this->applyCriteria(new SearchCriteria($request));
            $presenter = new DataPresenter(RoleResource::class, $request);

            return $presenter
                ->preparePager()
                ->renderCollection($this->query);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }

    public function show($id, Request $request)
    {
        try {
            $this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
            $presenter = new DataPresenter(RoleResource::class, $request);

            return $presenter->render($this->query);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }

    public function store($request)
    {
        try {
            if (!$this->roleHasPermission("Create Roles")) {
                throw new \Exception("User does not have the right permission.", 403);
            }
            $payload = $request->all();
            $payload['company_id'] = $this->userLogin()->company_id;
            $payload['name'] = str_replace(' ', '_', $request->name) . '_' . $payload['company_id'];
            $payload['custom_name'] = $request->name;

            $foundPermissions = Permission::whereIn('id', $payload["permission_ids"])->get();

            $role = Role::create($payload)->syncPermissions($foundPermissions);
            $permissions = [
                'Melihat Order Yang Dibuat Oleh '. ucfirst($role->name),
                'Mengedit Order Yang Dibuat Oleh '. ucfirst($role->name),
                'Menghapus Order Yang Dibuat Oleh '. ucfirst($role->name)    
            ];
            foreach ($permissions as $permission) {
                Permission::UpdateOrCreate([
                    'name' => $permission,
                ],[
                    'name' => $permission,
                ]);
            } 
            return $this->show($role->id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }

    public function updateBySuperEnterprise($id, $request)
    {
        try {
            if(!$this->roleHasPermission("Update All Roles")){
                throw new \Exception("User does not have the right permission.", 403);
            }
            $foundRole = Role::where('id',$id);
            $foundRole = $foundRole->firstOrFail();
            if (in_array($foundRole->custom_name, ["enterprise","super_admin",'unverified']) && $this->userLogin()->hasRole('super_enterprise')) {
                $this->updateAllFixedRoles($foundRole, $request);
            }
            $foundRole->name = str_replace(' ', '_', $request->name) . '_' . $foundRole->company_id;
            $foundRole->custom_name = $request->name;
            $foundRole->save();

            $foundPermissions = Permission::whereIn('id', $request->permission_ids)->get();
            $foundRole->syncPermissions($foundPermissions);

            return $this->show($id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }
    public function update($id, $request)
    {
        try {
            $foundRole = Role::where('id',$id);
            if($this->roleHasPermission("Update All Roles")){
                $foundRole = $foundRole->firstOrFail();
            }elseif ($this->roleHasPermission("Update Roles")) {
                $foundRole = $foundRole->where('company_id',$this->userLogin()->company_id)->whereNotIn('custom_name', ['super_enterprise','enterprise', 'super_admin','verified','unverified'])->first();
                if (!$foundRole) {
                    throw new \Exception("User does not have the right permission to update this role.", 403);
                }
            }
            else{
                throw new \Exception("User does not have the right permission.", 403);
            }

            $foundRole->name = str_replace(' ', '_', $request->name) . '_' . $foundRole->company_id;
            $foundRole->custom_name = $request->name;
            $foundRole->save();

            $foundPermissions = Permission::whereIn('id', $request->permission_ids)->get();
            $foundRole->syncPermissions($foundPermissions);

            return $this->show($id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }

    private function updateAllFixedRoles($foundRole, $request)
    {
        $foundRoles = Role::where('custom_name', $foundRole->custom_name)->get();
        $foundPermissions = Permission::whereIn('id', $request->permission_ids)->get();
        $firstRoleId = "";

        foreach ($foundRoles as $key => $role) {
            if ($key === 0) {
                $firstRoleId = $role->id;
            }
            $role->syncPermissions($foundPermissions);
        }

        return $this->show($firstRoleId, $request);
    }

    public function destroy($id)
    {
        try {
            if (!$this->roleHasPermission("Delete Roles")) {
                throw new \Exception("User does not have the right permission.", 403);
            }
            $role = Role::where(['id' => $id, 'company_id' => $this->userLogin()->company_id])->with(['users'])->firstOrFail();
            if ($role->users) {
                throw new Exception("Error, Can not delete role where has user", 403);
                
            }

            $role->revokePermissionTo($role->permissions()->pluck('name'));
            Permission::where('name', 'ILIKE', "%{$role->name}%")->delete();

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'data has been deleted',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $this->validHttpCode($e->getCode()) ? $e->getCode() : 404);
        }
    }
}
