<?php

namespace App\Http\Controllers\Api\V1;

use App\Constants\Constants;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('permission:Roles View Roles', ['only' => ['index']]);
        $this->middleware('permission:Roles Add Role', ['only' => ['store', 'permissions']]);
        $this->middleware('permission:Roles View Role By ID', ['only' => ['show']]);
        $this->middleware('permission:Roles Edit Role', ['only' => ['update', 'permissions']]);
        $this->middleware('permission:Roles Delete Role', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        try {
            $roles = Role::with(['permissions'])->where('id', '!=', 1)->get();
            return $this->apiResponse(Constants::SUCCESSFUL_RETRIEVAL, $roles, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Display a listing of permissions.
     */
    public function permissions()
    {
        try {
            $permissions = Permission::all();
            return $this->apiResponse(Constants::SUCCESSFUL_RETRIEVAL, $permissions, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:roles|max:255',
            'permissions_IDs' => 'required|array',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->apiResponse('Validation Error: ' . $validator->errors(), null, Constants::ERROR_CODE);
        }

        try {
            $role = Role::create(['name' => $request->input('name')]);

            $permissions_IDs = $request->input('permissions_IDs');
            $permissions = Permission::whereIn('id', $permissions_IDs)->get();
            $role->syncPermissions($permissions);

            return $this->apiResponse(Constants::SUCCESSFUL_CREATION, $role, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Display the specified role.
     */
    public function show($id)
    {
        try {
            $role = Role::with(['permissions'])->where('id', '!=', 1)->find($id);

            if ($role) {
                return $this->apiResponse(Constants::SUCCESSFUL_DISPLAY, $role, Constants::SUCCESS_CODE);
            }
            return $this->apiResponse(Constants::OPERATION_FAILED . ': Not found', null, Constants::ERROR_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => [
                'required',
                Rule::unique('roles')->ignore($id),
                'max:255',
            ],
            'permissions_IDs' => 'required|array',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->apiResponse('Validation Error: ' . $validator->errors(), null, Constants::ERROR_CODE);
        }

        try {
            $role = Role::where('id', '!=', 1)->find($id);

            if (!$role) {
                return $this->apiResponse(Constants::OPERATION_FAILED . ': Not found', null, Constants::ERROR_CODE);
            }

            $role->update(['name' => $request->input('name')]);

            $permissions_IDs = $request->input('permissions_IDs');
            $permissions = Permission::whereIn('id', $permissions_IDs)->get();
            $role->syncPermissions($permissions);

            return $this->apiResponse(Constants::SUCCESSFUL_UPDATE, $role, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy($id)
    {
        try {
            $role = Role::where('id', '!=', 1)->find($id);

            if (!$role) {
                return $this->apiResponse(Constants::OPERATION_FAILED . ': Not found', null, Constants::ERROR_CODE);
            }

            $role->delete();

            return $this->apiResponse(Constants::SUCCESSFUL_DELETION, null, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }
}
