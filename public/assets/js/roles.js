$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let rolesTable;

    function showAlert(message, type = 'success') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alert-container').html(alertHtml);

        setTimeout(function () {
            $('.alert').fadeOut();
        }, 5000);
    }

    function initializeDataTable() {
        rolesTable = $('#roles-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: window.location.pathname,
                type: 'GET',
                dataType: 'json',
                dataSrc: function (response) {
                    
                    if (response.can_create) {
                        $('.btn-add-role').show();
                    } else {
                        $('.btn-add-role').hide();
                    }
            
                    return response.data || [];
                },
                error: function (xhr, error, thrown) {
                    showAlert('Erro ao carregar dados da tabela', 'danger');
                }
            },
            columns: [
                {
                    data: 'role',
                    title: 'Perfil',
                    render: function (data, type, row) {
                        return `<div class="d-flex align-items-center">
                                    <div class="ms-1">
                                        <h6 class="mb-0 fw-bolder">${data}</h6>
                                    </div>
                                </div>`;
                    }
                },
                {
                    data: 'description',
                    title: 'Descrição',
                    render: function (data, type, row) {
                        return data || '<span class="text-muted">Sem descrição</span>';
                    }
                },
                {
                    data: 'permissions',
                    title: 'Permissões',
                    orderable: false,
                    render: function (data, type, row) {
                        if (data && data.length > 0) {
                            return data.map(permission => {
                                const permissionLabels = {
                                    'user.create': 'Criar Usuários',
                                    'user.edit': 'Editar Usuários',
                                    'user.delete': 'Excluir Usuários',
                                    'user.view': 'Visualizar Usuários',
                                    'role.create': 'Criar Perfis',
                                    'role.edit': 'Editar Perfis',
                                    'role.delete': 'Excluir Perfis',
                                    'role.view': 'Visualizar Perfis'
                                };
                                return `<span class="badge bg-info me-1">${permissionLabels[permission] || permission}</span>`;
                            }).join('');
                        } else {
                            return '<span class="text-muted">Sem permissões</span>';
                        }
                    }
                },
                {
                    data: 'users_count',
                    title: 'Usuários',
                    render: function (data, type, row) {
                        return `<span class="badge bg-secondary">${data}</span>`;
                    }
                },
                {
                    data: null,
                    title: 'Ações',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        let buttons = '';
                
                        if (row.can_edit) {
                            buttons += `
                                <button type="button" class="btn btn-sm btn-primary edit-role-btn" 
                                        data-role-id="${row.id}" 
                                        data-role-name="${row.role}"
                                        data-role-description="${row.description || ''}"
                                        data-role-permissions='${JSON.stringify(row.permissions)}'>
                                    Editar
                                </button>
                            `;
                        }
                
                        if (row.can_delete) {
                            buttons += `
                                <button type="button" class="btn btn-sm btn-danger delete-role-btn" 
                                        data-role-id="${row.id}" 
                                        data-role-name="${row.role}">
                                    Excluir
                                </button>
                            `;
                        }
                
                        if (!row.can_edit && !row.can_delete) {
                            return '<span class="text-muted">Sem permissões</span>';
                        }
                
                        return `<div class="d-flex justify-content-center gap-2 mt-2">${buttons}</div>`;
                    }
                }
            ],
            language: {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros no total)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            },
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            order: [[0, 'asc']],
            drawCallback: function () {
                addEventListeners();
            },
            initComplete: function () {
                addEventListeners();
            }
        });
    }

    function addEventListeners() {
        $(document).off('click', '.delete-role-btn').on('click', '.delete-role-btn', function () {
            const roleId = $(this).data('role-id');
            const roleName = $(this).data('role-name');
            deleteRole(roleId, roleName);
        });

        $(document).off('click', '.edit-role-btn').on('click', '.edit-role-btn', function () {
            const roleId = $(this).data('role-id');
            const roleName = $(this).data('role-name');
            const roleDescription = $(this).data('role-description');
            const rolePermissions = $(this).data('role-permissions');


            $('#edit_role_id').val(roleId);
            $('#edit_role').val(roleName);
            $('#edit_description').val(roleDescription);

            $('input[name="permissions[]"]').prop('checked', false);

            if (rolePermissions && rolePermissions.length > 0) {
                rolePermissions.forEach(permission => {
                    $(`input[name="permissions[]"][value="${permission}"]`).prop('checked', true);
                });
            }

            $('#editRoleModal').modal('show');
        });
    }

    function deleteRole(roleId, roleName) {
        Swal.fire({
            title: "Confirmar exclusão",
            text: `Tem certeza que deseja excluir o perfil "${roleName}"?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sim, excluir",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Excluindo...',
                    text: 'Aguarde enquanto excluímos o perfil',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/roles/${roleId}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            rolesTable.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'Erro ao excluir perfil';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: errorMessage
                        });
                    }
                });
            }
        });
    }

    function addRole(formData) {
        $.ajax({
            url: '/roles',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                rolesTable.ajax.reload();
                $('#addRoleModal').modal('hide');
                $('#create-role-form')[0].reset();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                let errorMessage = 'Erro ao criar perfil';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: errorMessage,
                    html: errorMessage
                });
            }
        });
    }

    function editRole(formData) {
        const roleId = $('#edit_role_id').val();

        formData.append('_method', 'PUT');

        $.ajax({
            url: `/roles/${roleId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                rolesTable.ajax.reload();
                $('#editRoleModal').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                let errorMessage = 'Erro ao atualizar perfil';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: errorMessage,
                    html: errorMessage
                });
            }
        });
    }

    $(document).on('submit', '#create-role-form', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        addRole(formData);
    });

    $(document).on('submit', '#edit-role-form', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        editRole(formData);
    });

    $(document).on('click', '#refresh-table', function () {
        rolesTable.ajax.reload();
        Swal.fire({
            icon: 'info',
            title: 'Atualizado!',
            text: 'Tabela atualizada com sucesso!',
            timer: 1500,
            showConfirmButton: false
        });
    });

    $('#addRoleModal').on('hidden.bs.modal', function () {
        $('#create-role-form')[0].reset();
    });

    $('#editRoleModal').on('hidden.bs.modal', function () {
        $('#edit-role-form')[0].reset();
    });

    initializeDataTable();
}); 