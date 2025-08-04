<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {

        if (!Auth::check() || !Auth::user()->roles->contains('role', 'Administrador')) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para acessar esta página.'
                ], 403);
            }
            return redirect()->route('welcome')->with('error', 'Você não tem permissão para acessar esta página.');
        }


        if (request()->ajax()) {
            $roles = Role::where('is_active', true)->get();
            
            $data = [];
            foreach ($roles as $role) {
                $data[] = [
                    'id' => $role->id,
                    'role' => $role->role,
                    'description' => $role->description,
                    'permissions' => $role->permissions ?? [],
                    'users_count' => $role->users()->count(),
                    'can_edit' => $this->canManageRoles(),
                    'can_delete' => $this->canManageRoles()
                ];
            }
            
            return response()->json([
                'data' => $data,
                'can_create' => $this->canManageRoles()
            ]);
        }
        
        return view('roles.index');
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        if (!$this->canManageRoles()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para criar perfis.'
                ], 403);
            }
            return redirect()->route('roles.index')->with('error', 'Você não tem permissão para criar perfis.');
        }

        $request->validate([
            'role' => 'required|string|max:255|unique:roles,role',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:user.create,user.edit,user.delete,user.view,role.create,role.edit,role.delete,role.view'
        ]);

        $role = Role::create([
            'role' => $request->role,
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
            'is_active' => true
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Perfil criado com sucesso!',
                'role' => $role
            ]);
        }

        return redirect()->route('roles.index')->with('success', 'Perfil criado com sucesso!');
    }

    public function show(string $id)
    {
        $role = Role::findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json([
                'role' => $role,
                'users' => $role->users()->get(['id', 'name', 'email'])
            ]);
        }
        
        return view('roles.show', compact('role'));
    }

    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, string $id)
    {
        if (!$this->canManageRoles()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para editar perfis.'
                ], 403);
            }
            return redirect()->route('roles.index')->with('error', 'Você não tem permissão para editar perfis.');
        }

        $role = Role::findOrFail($id);

        $request->validate([
            'role' => 'required|string|max:255|unique:roles,role,' . $id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:user.create,user.edit,user.delete,user.view,role.create,role.edit,role.delete,role.view'
        ]);

        $role->update([
            'role' => $request->role,
            'description' => $request->description,
            'permissions' => $request->permissions ?? []
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso!',
                'role' => $role
            ]);
        }

        return redirect()->route('roles.index')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function destroy(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            
            if (!$this->canManageRoles()) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Você não tem permissão para excluir perfis.'
                    ], 403);
                }
                return redirect()->route('roles.index')->with('error', 'Você não tem permissão para excluir perfis.');
            }

            if ($role->users()->count() > 0) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Não é possível excluir um perfil que está sendo usado por usuários.'
                    ], 400);
                }
                return redirect()->route('roles.index')->with('error', 'Não é possível excluir um perfil que está sendo usado por usuários.');
            }

            $role->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Perfil excluído com sucesso!',
                    'role_id' => $id
                ]);
            }

            return redirect()->route('roles.index')->with('success', 'Perfil excluído com sucesso!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir perfil: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('roles.index')->with('error', 'Erro ao excluir perfil.');
        }
    }

    public function assignToUser(Request $request, $userId)
    {
        if (!$this->canManageRoles()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para gerenciar perfis de usuários.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Você não tem permissão para gerenciar perfis de usuários.');
        }

        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id'
        ]);

        $user = \App\Models\User::findOrFail($userId);
        $user->roles()->sync($request->role_ids);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Perfis atribuídos com sucesso!'
            ]);
        }

        return redirect()->back()->with('success', 'Perfis atribuídos com sucesso!');
    }

    public function getUserRoles($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        if (request()->ajax()) {
            return response()->json([
                'user' => $user->load('roles'),
                'available_roles' => Role::where('is_active', true)->get()
            ]);
        }
        
        return view('users.roles', compact('user'));
    }

    private function canManageRoles()
    {
        $currentUser = Auth::user();
        return $currentUser->roles->contains('role', 'Administrador') || 
               $currentUser->roles->some(function($role) {
                   return $role->hasPermission('role.create') || 
                          $role->hasPermission('role.edit') || 
                          $role->hasPermission('role.delete');
               });
    }
}
