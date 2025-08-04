<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Helpers\PermissionHelper;
use App\Helpers\RoleHelper;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $currentUser = Auth::user();
        $isAdminLogged = $currentUser->roles->contains('role', 'Administrador');
        $roles = Role::where('is_active', true)->get();

        if (request()->ajax()) {
            $data = [];
            foreach ($users as $user) {
                $roles = $user->roles->pluck('role')->toArray();
                $rolesId = $user->roles->pluck('id')->first();

                $canEdit = PermissionHelper::userCan('user.edit') || $currentUser->id === $user->id;
                $canDelete = PermissionHelper::userCan('user.delete') && $currentUser->id !== $user->id;

                $data[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $roles,
                    'roles_id' => $rolesId,
                    'can_edit' => $canEdit,
                    'can_delete' => $canDelete
                ];
            }

            return response()->json([
                'data' => $data,
                'can_create' => PermissionHelper::userCan('user.create')
            ]);
        }

        return view('welcome', compact('roles', 'users', 'isAdminLogged'));
    }

    public function store(Request $request)
    {
        if (!PermissionHelper::userCan('user.create')) {
            return $this->sendPermissionError('criar usuários', $request);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'roles' => 'required',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        
        $this->insertRoleUser($user, $request);

        return $request->ajax()
            ? response()->json(['success' => true, 'message' => 'Usuário criado com sucesso!'])
            : redirect()->route('welcome')->with('success', 'Usuário criado com sucesso!');
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if (!PermissionHelper::userCanOrSelf('user.edit', $user->id)) {
            return $this->sendPermissionError('editar este usuário', $request);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);


        $this->insertRoleUser($user, $request);

        return $request->ajax()
            ? response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso!',
                'user' => $user->load('roles')
            ])
            : redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    private function insertRoleUser($user, $request = null)
    {
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $defaultRole = RoleHelper::getIdByName('Usuário');
            $user->roles()->sync([$defaultRole]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $currentUser = Auth::user();

            if (!PermissionHelper::userCan('user.delete') || $currentUser->id === $user->id) {
                return $this->sendPermissionError('excluir este usuário', request());
            }

            $user->delete();

            return request()->ajax()
                ? response()->json([
                    'success' => true,
                    'message' => 'Usuário excluído com sucesso!',
                    'user_id' => $id
                ])
                : redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso!');
        } catch (\Exception $e) {
            return request()->ajax()
                ? response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir usuário: ' . $e->getMessage()
                ], 500)
                : redirect()->route('users.index')->with('error', 'Erro ao excluir usuário.');
        }
    }

    public function showRoles($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('users.roles', compact('user', 'roles'));
    }

    public function updateRoles(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->roles()->sync($request->roles);

        return redirect()->route('users.index')->with('success', 'Perfis atualizados com sucesso.');
    }

    private function sendPermissionError($message, $request)
    {
        $errorMessage = "Você não tem permissão para {$message}.";

        return $request->ajax()
            ? response()->json(['success' => false, 'message' => $errorMessage], 403)
            : redirect()->route('welcome')->with('error', $errorMessage);
    }
}
