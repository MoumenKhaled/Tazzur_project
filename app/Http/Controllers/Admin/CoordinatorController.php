<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manager;
use Hash;
use App\Http\Controllers\BaseController;
class CoordinatorController extends BaseController
{
    /**
     * عرض قائمة بجميع المديرين.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 2);

        $managers = Manager::paginate($perPage)->through(function ($manager) {
            return [
                'id' => $manager->id,
                'created_at' => $manager->created_at,
                'updated_at' => $manager->updated_at,
                'deleted_at' => $manager->deleted_at,
                'email' => $manager->email,
                'role_name' => json_decode($manager->role_name),
                'name' => $manager->name,
            ];
        });
        return $this->sendResponse($managers, 'managers');
    }
    public function store(Request $request)
{
    $validator = $request->validate([
        'email' => 'required|email|unique:managers,email',
        'name' => 'required',
        'role_name' => [
            'required',
            'array',
            function ($attribute, $value, $fail) {
                $existingRoles = Manager::whereJsonContains('role_name', $value)->exists();
                if ($existingRoles) {
                    $fail('One or more of the selected roles are already assigned to another manager.');
                }
            }
        ],
        'role_name.*' => 'string',
        'password' => ['required', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/']
    ]);
    $input = $request->all();
    $input['role_name'] = json_encode($request->role_name);
    $input['password'] =  Hash::make($input['password']);
    $manager = Manager::create($input);
    return $this->sendResponse($manager, 'Manager created successfully');
}


    /**
     * عرض بيانات مدير محدد.
     */
    public function show(string $id)
    {
        $manager = Manager::where('id',$id)->first();
        if (!$manager) {
            return $this->sendError(404, 'manager not found');
        }
            $manager= [
                'id' => $manager->id,
                'created_at' => $manager->created_at,
                'updated_at' => $manager->updated_at,
                'deleted_at' => $manager->deleted_at,
                'email' => $manager->email,
                'role_name' => json_decode($manager->role_name),
                'name' => $manager->name,
            ];

        return $this->sendResponse($manager, 'manager');
    }

    /**
     * تحديث بيانات مدير محدد في قاعدة البيانات.
     */
    public function update(Request $request, string $id)
    {
        $validator=$request->validate([
            'email' => 'required|email|unique:managers,email,'.$id,
            'name' => 'required',
            'role_name' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($id) {
                    foreach ($value as $role) {
                        $existingRole = Manager::where('id', '!=', $id)
                            ->whereJsonContains('role_name', $role)
                            ->exists();
                        if ($existingRole) {
                            $fail('The role "' . $role . '" is already assigned to another manager.');
                        }
                    }
                }
            ],
            'role_name.*' => 'string',
            'password' => ['nullable', 'min:8','regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/']
        ]);
        $manager = Manager::find($id);
        if (!$manager) {
            return $this->sendError(404, 'manager not found');
        }
        if (in_array('admin', json_decode($manager->role_name)) && !in_array('admin', $request->role_name)) {
            $otherAdminExists = Manager::where('id', '!=', $id)->whereRaw('json_contains(role_name, \'["admin"]\')')->exists();
            if (!$otherAdminExists) {
                return $this->sendError(400, 'At least one admin must exist.');
            }
        }

        $input = $request->all();
        $input['role_name'] = json_encode($request->role_name);
        $manager->update($input);
        if($input['password'])
        {
            $manager->update([
                'password'=>Hash::make($request->password)
            ]);
        }

        return $this->sendResponse($manager, 'manager');
    }


    public function destroy(string $id)
    {
        $manager = Manager::where('id',$id)->first();
        if (!$manager) {
            return $this->sendError(404, 'manager not found');
        }
        if (in_array('admin', json_decode($manager->role_name))) {
            $otherAdminExists = Manager::where('id', '!=', $id)->whereRaw('json_contains(role_name, \'["admin"]\')')->exists();
            if (!$otherAdminExists) {
                return $this->sendError(400, 'Cannot delete the only admin.');
            }
        }
        Manager::findOrFail($id)->delete();
        return $this->sendResponse(null, 'done');
    }
}
