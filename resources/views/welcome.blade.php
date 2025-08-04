@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Lista de Usuários</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" id="refresh-table">
                            <i class="ti ti-refresh"></i> Atualizar
                        </button>
                        @if ($isAdminLogged)
                            <a href="{{ route('roles.index') }}" class="btn btn-success btn-perfil">
                                <i class="ti ti-users-group"></i> Gerenciar Perfis
                            </a>
                        @endif
                        <button type="button" class="btn btn-primary btn-add-user" data-bs-toggle="modal"
                            data-bs-target="#addUserModal">
                            <i class="ti ti-plus"></i> Adicionar Usuário
                        </button>
                    </div>
                </div>

                <div id="alert-container"></div>

                <div class="table-responsive">
                    <table class="table text-nowrap table-striped mb-0 align-middle" id="users-table">
                        <thead class="text-dark fs-4">
                            <tr>
                                <th class="border-bottom-0 font-size-table">
                                    <h6 class="fw-semibold mb-0">Usuário</h6>
                                </th>
                                <th class="border-bottom-0 font-size-table">
                                    <h6 class="fw-semibold mb-0">Email</h6>
                                </th>
                                <th class="border-bottom-0 font-size-table">
                                    <h6 class="fw-semibold mb-0">Perfil</h6>
                                </th>
                                <th class="border-bottom-0 font-size-table">
                                    <h6 class="fw-semibold mb-0 text-center">Ações</h6>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Adicionar Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="create-user-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" required>
                    </div>
                    <div>
                        <label for="role" class="form-label">Perfil</label>
                        <select class="form-select" id="role" name="roles" required>
                            <option value="" disabled selected>Selecione um perfil</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar Usuário</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-user-form">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                        <input type="password" class="form-control" id="edit_password_confirmation"
                            name="password_confirmation">
                    </div>
                    @if ($isAdminLogged)
                        <div>
                            <label for="edit_role" class="form-label">Perfil</label>
                            <select class="form-select" id="edit_role" name="roles" required>
                                <option value="" disabled>Selecione um perfil</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Atualizar Usuário</button>
                    </div>
            </form>
        </div>
    </div>
</div>
@endsection
