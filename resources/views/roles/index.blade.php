@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="ti ti-users-group"></i> Gerenciamento de Perfis
                            </h3>
                            <div>
                                <a href="{{ route('welcome') }}" class="btn btn-secondary">
                                    <i class="ti ti-arrow-left"></i> Voltar
                                </a>
                                <button type="button" class="btn btn-primary btn-add-role" data-bs-toggle="modal"
                                    data-bs-target="#addRoleModal">
                                    <i class="ti ti-plus"></i> Adicionar Perfil
                                </button>
                                <button type="button" class="btn btn-info" id="refresh-table">
                                    <i class="ti ti-refresh"></i> Atualizar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <table id="roles-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="font-size-table">Perfil</th>
                                    <th class="font-size-table">Descrição</th>
                                    <th class="font-size-table">Permissões</th>
                                    <th class="font-size-table">Usuários</th>
                                    <th class="font-size-table">Ações</th>
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

    <div class="modal fade" id="addRoleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-plus"></i> Adicionar Perfil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="create-role-form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Nome do Perfil *</label>
                                    <input type="text" class="form-control" id="role" name="role" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Descrição</label>
                                    <input type="text" class="form-control" id="description" name="description">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissões</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Usuários</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="user.create" id="user_create">
                                        <label class="form-check-label" for="user_create">Criar usuários</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="user.edit" id="user_edit">
                                        <label class="form-check-label" for="user_edit">Editar usuários</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="user.delete" id="user_delete">
                                        <label class="form-check-label" for="user_delete">Excluir usuários</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="user.view" id="user_view">
                                        <label class="form-check-label" for="user_view">Visualizar usuários</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Perfis</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="role.create" id="role_create">
                                        <label class="form-check-label" for="role_create">Criar perfis</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="role.edit" id="role_edit">
                                        <label class="form-check-label" for="role_edit">Editar perfis</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="role.delete" id="role_delete">
                                        <label class="form-check-label" for="role_delete">Excluir perfis</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="role.view" id="role_view">
                                        <label class="form-check-label" for="role_view">Visualizar perfis</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRoleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-edit"></i> Editar Perfil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="edit-role-form">
                    <input type="hidden" id="edit_role_id" name="role_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_role" class="form-label">Nome do Perfil *</label>
                                    <input type="text" class="form-control" id="edit_role" name="role" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_description" class="form-label">Descrição</label>
                                    <input type="text" class="form-control" id="edit_description" name="description">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissões</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Usuários</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="user.create" id="edit_user_create">
                                        <label class="form-check-label" for="edit_user_create">Criar usuários</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="user.edit" id="edit_user_edit">
                                        <label class="form-check-label" for="edit_user_edit">Editar usuários</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="user.delete" id="edit_user_delete">
                                        <label class="form-check-label" for="edit_user_delete">Excluir usuários</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="user.view" id="edit_user_view">
                                        <label class="form-check-label" for="edit_user_view">Visualizar usuários</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Perfis</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="role.create" id="edit_role_create">
                                        <label class="form-check-label" for="edit_role_create">Criar perfis</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="role.edit" id="edit_role_edit">
                                        <label class="form-check-label" for="edit_role_edit">Editar perfis</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="role.delete" id="edit_role_delete">
                                        <label class="form-check-label" for="edit_role_delete">Excluir perfis</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="role.view" id="edit_role_view">
                                        <label class="form-check-label" for="edit_role_view">Visualizar perfis</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
