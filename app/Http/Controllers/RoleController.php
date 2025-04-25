<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Business;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    use HandleTransactions;

    public function index()
    {
        return view('roles.index', ['page' => 'Roles Management']);
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function editView($business, $role)
    {
        $role = Role::where('name', $role)->firstOrFail();
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function fetch(Request $request)
    {
        $businessSlug = session('active_business_slug');
        $query = Role::with('permissions')->orderBy('created_at', 'desc');

        if ($businessSlug) {
            $business = Business::findBySlug($businessSlug);
            $query->where('business_id', $business->id);
        }

        if ($request->has('filter')) {
            $filter = $request->input('filter');
            $query->where('name', 'like', "%$filter%");
        }

        $roles = $query->get();
        $rolesTable = view('roles._table', compact('roles'))->render();
        return RequestResponse::ok('Ok', $rolesTable);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            $role = Role::create([
                'name' => $validatedData['name'],
                'guard_name' => 'web',
                'business_id' => $business->id,
            ]);

            if (!empty($validatedData['permissions'])) {
                $role->syncPermissions($validatedData['permissions']);
            }

            return RequestResponse::created('Role created successfully.', ['role' => $role]);
        });
    }

    public function show($business, $role)
    {
        $role = Role::with('permissions', 'users')->where('name', $role)->firstOrFail();
        $businessModel = Business::findBySlug($business);

        // Handle null business_id
        if (!$role->business || $role->business->slug !== $business) {
            Log::warning("Role {$role->name} has no business or mismatched business slug: {$business}");
            if ($businessModel) {
                $role->business_id = $businessModel->id;
                $role->save();
            } else {
                return RequestResponse::badRequest('Invalid business or role configuration.');
            }
        }

        // Fetch users belonging to the current business
        $users = User::whereHas('employee', function ($query) use ($businessModel) {
            $query->where('business_id', $businessModel->id);
        })->get();

        return view('roles.show', compact('role', 'users', 'businessModel'));
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate(['role' => 'required|exists:roles,id']);
        $role = Role::with('permissions')->findOrFail($validatedData['role']);
        $permissions = Permission::all();
        $roleForm = view('roles._form', compact('role', 'permissions'))->render();
        return RequestResponse::ok('Ok', $roleForm);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'role_name' => 'required|exists:roles,name',
            'name' => 'required|string|max:255|unique:roles,name,' . Role::where('name', $request->role_name)->first()->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $role = Role::where('name', $validatedData['role_name'])->firstOrFail();
            $role->update(['name' => $validatedData['name']]);
            $role->syncPermissions($validatedData['permissions'] ?? []);

            return RequestResponse::ok('Role updated successfully.', ['role' => $role]);
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate(['role' => 'required|exists:roles,name']);
        return $this->handleTransaction(function () use ($validatedData) {
            $role = Role::where('name', $validatedData['role'])->firstOrFail();
            $role->users()->each(function ($user) use ($role) {
                $user->removeRole($role);
            });
            $role->syncPermissions([]);
            $role->delete();
            return RequestResponse::ok('success', ['message' => 'Role deleted successfully.']);
        });
    }

    public function assign(Request $request)
    {
        $validatedData = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'user_id' => 'required|exists:users,id',
            '_method' => 'nullable|in:DELETE', // Support removal
        ]);

        return $this->handleTransaction(function () use ($validatedData, $request) {
            $role = Role::findOrFail($validatedData['role_id']);
            $user = User::findOrFail($validatedData['user_id']);

            // Ensure user belongs to the same business
            if ($user->employee && $user->employee->business_id !== $role->business_id) {
                return RequestResponse::badRequest('User does not belong to the same business as the role.');
            }

            if ($request->input('_method') === 'DELETE') {
                $user->removeRole($role);
                return RequestResponse::ok('Role removed successfully.', ['role' => $role, 'user' => $user]);
            }

            $user->assignRole($role);
            return RequestResponse::ok('Role assigned successfully.', ['role' => $role, 'user' => $user]);
        });
    }
}