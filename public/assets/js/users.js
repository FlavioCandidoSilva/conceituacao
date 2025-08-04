$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let usersTable;

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
        usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: window.location.pathname,
                type: 'GET',
                dataType: 'json',
                dataSrc: function (response) {
                    
                    if (response.can_create) {
                        $('.btn-add-user').show();
                    } else {
                        $('.btn-add-user').hide();
                    }
            
                    return response.data || [];
                },
                error: function (xhr, error, thrown) {
                    showAlert('Erro ao carregar dados da tabela', 'danger');
                }
            },
            columns: [
                {
                    data: 'name',
                    title: 'Usuário',
                    render: function (data, type, row) {
                        return `<div class="d-flex align-items-center">
                                    <div class="ms-1">
                                        <h6 class="mb-0 fw-bolder">${data}</h6>
                                    </div>
                                </div>`;
                    }
                },
                {
                    data: 'email',
                    title: 'Email'
                },
                {
                    data: 'roles',
                    title: 'Usuário',
                    orderable: false,
                    render: function (data, type, row) {
                        if (data && data.length > 0) {
                            return data.map(role =>
                                `<span class="badge bg-primary me-1">${role}</span>`
                            ).join('');
                        } else {
                            return '<span class="text-muted">Padrão</span>';
                        }
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
                                <button type="button" class="btn btn-sm btn-primary edit-user-btn" 
                                        data-user-id="${row.id}" 
                                        data-user-name="${row.name}"
                                        data-user-email="${row.email}"
                                        data-user-roles="${row.roles}"
                                        data-user-roles_id="${row.roles_id}">
                                    Editar
                                </button>
                            `;
                        }
                        
                                              
                         if (row.can_delete) {
                             buttons += `
                                 <button type="button" class="btn btn-sm btn-danger delete-user-btn" 
                                         data-user-id="${row.id}" 
                                         data-user-name="${row.name}">
                                     Excluir
                                 </button>
                             `;
                         }
                         
                         if (!row.can_edit && !row.can_delete) {
                            buttons = '<span class="text-muted">Sem permissões</span>';
                        }
                        
                        return buttons;
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

        $(document).off('click', '.delete-user-btn').on('click', '.delete-user-btn', function () {
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            deleteUser(userId, userName);
        });

        $(document).off('click', '.edit-user-btn').on('click', '.edit-user-btn', function () {
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            const userEmail = $(this).data('user-email');
            const userRolesId = $(this).data('user-roles_id');

            $('#edit_user_id').val(userId);
            $('#edit_name').val(userName);
            $('#edit_email').val(userEmail);
            $('#edit_role').val(userRolesId);
            $('#edit_password').val('');
            $('#edit_password_confirmation').val('');

            $('#editUserModal').modal('show');
        });
    }

    function deleteUser(userId, userName) {
        Swal.fire({
            title: "Confirmar exclusão",
            text: `Tem certeza que deseja excluir o usuário "${userName}"?`,
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
                    text: 'Aguarde enquanto excluímos o usuário',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/users/${userId}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            usersTable.ajax.reload();
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
                        let errorMessage = 'Erro ao excluir usuário';
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

    function addUser(formData) {
        $.ajax({
            url: '/users',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                usersTable.ajax.reload();
                $('#addUserModal').modal('hide');
                $('#create-user-form')[0].reset();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                let errorMessage = 'Erro ao criar usuário';
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

    function editUser(formData) {
        const userId = $('#edit_user_id').val();

        formData.append('_method', 'PUT');

        $.ajax({
            url: `/users/${userId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                usersTable.ajax.reload();
                $('#editUserModal').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                let errorMessage = 'Erro ao atualizar usuário';
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

    $(document).on('submit', '#create-user-form', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        addUser(formData);
    });

    $(document).on('submit', '#edit-user-form', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        editUser(formData);
    });

    $(document).on('click', '#refresh-table', function () {
        usersTable.ajax.reload();
        Swal.fire({
            icon: 'info',
            title: 'Atualizado!',
            text: 'Tabela atualizada com sucesso!',
            timer: 1500,
            showConfirmButton: false
        });
    });

    $('#addUserModal').on('hidden.bs.modal', function () {
        $('#create-user-form')[0].reset();
    });

    $('#editUserModal').on('hidden.bs.modal', function () {
        $('#edit-user-form')[0].reset();
    });

    initializeDataTable();
}); 