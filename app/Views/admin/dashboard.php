<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= \App\Core\Csrf::generateToken() ?>">
    <title><?= $title ?? 'Aura Admin' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Helper global fetchJson
        async function fetchJson(url, opts = {}) {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const headers = { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': token 
            };
            if (opts.headers) {
                Object.assign(headers, opts.headers);
            }
            const res = await fetch(url, {
                ...opts,
                headers: headers,
                body: opts.body ? JSON.stringify(opts.body) : undefined
            });
            return res.json();
        }
    </script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Aura Admin</a>
        <div class="d-flex align-items-center">
            <span class="text-light me-3">Hola, <?= htmlspecialchars(\App\Core\Auth::user()['name'] ?? 'Administrador') ?></span>
            <form action="/logout" method="POST" class="m-0">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
                <button type="submit" class="btn btn-outline-light btn-sm">Salir</button>
            </form>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Usuarios</h6>
                    <h2 class="mb-0"><?= $totalUsers ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Aulas</h6>
                    <h2 class="mb-0"><?= $totalClassrooms ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Avisos Registrados</h6>
                    <h2 class="mb-0"><?= $totalReports ?? 0 ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 m-0" id="section-title">Gestión</h5>
        </div>
        <div class="card-body p-0">
            <ul class="nav nav-tabs px-3 pt-3" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users-pane" type="button" role="tab">Usuarios</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="classrooms-tab" data-bs-toggle="tab" data-bs-target="#classrooms-pane" type="button" role="tab">Aulas</button>
                </li>
            </ul>

            <div class="tab-content" id="adminTabsContent">
                <!-- Pestaña Usuarios -->
                <div class="tab-pane fade show active p-3" id="users-pane" role="tabpanel" tabindex="0">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-success btn-sm" onclick="openUserModal()">+ Nuevo Usuario</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="users-tbody">
                                <tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pestaña Aulas -->
                <div class="tab-pane fade p-3" id="classrooms-pane" role="tabpanel" tabindex="0">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-success btn-sm" onclick="openClassroomModal()">+ Nueva Aula</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre del Aula</th>
                                    <th>Tutor Asignado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="classrooms-tbody">
                                <tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Usuario -->
<div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="form-user" onsubmit="saveUser(event)">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalUserTitle">Nuevo Usuario</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="user-id">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" id="user-name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" id="user-email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select id="user-role" class="form-select">
                <option value="alumno">Alumno</option>
                <option value="profesor">Profesor</option>
                <option value="orientador">Orientador</option>
                <option value="direccion">Dirección</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña <small class="text-muted">(en blanco si es alumno o para no cambiar)</small></label>
            <input type="password" id="user-password" class="form-control">
        </div>
        <div class="mb-3 d-none" id="user-classroom-container">
            <label class="form-label">Aula Asignada (Solo Alumnos)</label>
            <select id="user-classroom" class="form-select">
                <option value="">-- Sin aula --</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Aula -->
<div class="modal fade" id="modalClassroom" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="form-classroom" onsubmit="saveClassroom(event)">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalClassroomTitle">Nueva Aula</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="classroom-id">
        <div class="mb-3">
            <label class="form-label">Nombre del Aula (ej. 3º ESO A)</label>
            <input type="text" id="classroom-name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tutor Asignado (opcional)</label>
            <select id="classroom-tutor" class="form-select">
                <option value="">-- Sin tutor --</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let allUsers = [];
    let allClassrooms = [];
    let userModalInstance = null;
    let classroomModalInstance = null;

    document.addEventListener("DOMContentLoaded", () => {
        userModalInstance = new bootstrap.Modal(document.getElementById('modalUser'));
        classroomModalInstance = new bootstrap.Modal(document.getElementById('modalClassroom'));
        
        loadUsers();
        loadClassroomsInBackground();
        
        document.getElementById('user-role').addEventListener('change', function(e) {
            const classroomContainer = document.getElementById('user-classroom-container');
            if (e.target.value === 'alumno') {
                classroomContainer.classList.remove('d-none');
            } else {
                classroomContainer.classList.add('d-none');
            }
        });

        // Tabs event listeners for loading data
        document.getElementById('users-tab').addEventListener('shown.bs.tab', function () {
            loadUsers();
        });
        document.getElementById('classrooms-tab').addEventListener('shown.bs.tab', function () {
            loadClassrooms();
        });
    });

    async function loadClassroomsInBackground() {
        try {
            const res = await fetchJson('/admin/api/classrooms');
            allClassrooms = res.data || [];
            updateStudentClassroomSelect();
        } catch (e) {}
    }

    function updateStudentClassroomSelect() {
        const select = document.getElementById('user-classroom');
        select.innerHTML = '<option value="">-- Sin aula --</option>';
        allClassrooms.forEach(c => {
            select.innerHTML += `<option value="${c.id}">${c.name}</option>`;
        });
    }

    // --- Usuarios ---
    async function loadUsers() {
        try {
            const res = await fetchJson('/admin/api/users');
            allUsers = res.data || [];
            const tbody = document.getElementById('users-tbody');
            tbody.innerHTML = '';
            
            allUsers.forEach(u => {
                let badgeClass = u.role === 'admin' ? 'bg-danger' : (u.role === 'alumno' ? 'bg-success' : 'bg-primary');
                tbody.innerHTML += `
                    <tr>
                        <td>${u.id}</td>
                        <td class="fw-medium">${u.name}</td>
                        <td>${u.email}</td>
                        <td><span class="badge ${badgeClass}">${u.role}</span></td>
                        <td class="text-end">
                            <button onclick='editUser(${JSON.stringify(u).replace(/'/g, "&apos;")})' class="btn btn-sm btn-outline-primary me-1">Editar</button>
                            <button onclick="deleteUser(${u.id})" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
            updateTutorSelect(); // Update tutors list since users might have changed
        } catch (e) {
            alert('Error cargando usuarios');
        }
    }

    function openUserModal() {
        document.getElementById('user-id').value = '';
        document.getElementById('user-name').value = '';
        document.getElementById('user-email').value = '';
        document.getElementById('user-role').value = 'alumno';
        document.getElementById('user-password').value = '';
        document.getElementById('user-classroom').value = '';
        document.getElementById('user-classroom-container').classList.remove('d-none');
        document.getElementById('modalUserTitle').innerText = 'Nuevo Usuario';
        userModalInstance.show();
    }

    function editUser(user) {
        document.getElementById('user-id').value = user.id;
        document.getElementById('user-name').value = user.name;
        document.getElementById('user-email').value = user.email;
        document.getElementById('user-role').value = user.role;
        document.getElementById('user-password').value = '';
        
        const classroomContainer = document.getElementById('user-classroom-container');
        if (user.role === 'alumno') {
            classroomContainer.classList.remove('d-none');
            document.getElementById('user-classroom').value = user.classroom_id || '';
        } else {
            classroomContainer.classList.add('d-none');
            document.getElementById('user-classroom').value = '';
        }
        
        document.getElementById('modalUserTitle').innerText = 'Editar Usuario';
        userModalInstance.show();
    }

    async function saveUser(e) {
        e.preventDefault();
        const id = document.getElementById('user-id').value;
        const data = {
            name: document.getElementById('user-name').value,
            email: document.getElementById('user-email').value,
            role: document.getElementById('user-role').value,
            password: document.getElementById('user-password').value,
            classroom_id: document.getElementById('user-classroom').value
        };

        const url = id ? `/admin/api/users/${id}` : '/admin/api/users';
        const method = id ? 'PATCH' : 'POST';

        try {
            const res = await fetchJson(url, { method, body: data });
            if (res.success) {
                userModalInstance.hide();
                loadUsers();
            } else {
                alert(res.error || 'Error al guardar');
            }
        } catch (e) {
            alert('Error de conexión');
        }
    }

    async function deleteUser(id) {
        if (!confirm('¿Seguro que deseas eliminar este usuario?')) return;
        try {
            const res = await fetchJson(`/admin/api/users/${id}`, { method: 'DELETE' });
            if (res.success) {
                loadUsers();
            } else {
                alert(res.error || 'Error al eliminar');
            }
        } catch (e) {
            alert('Error de conexión');
        }
    }

    // --- Aulas ---
    async function loadClassrooms() {
        try {
            const res = await fetchJson('/admin/api/classrooms');
            allClassrooms = res.data || [];
            updateStudentClassroomSelect(); // Update student dropdown as well
            
            const tbody = document.getElementById('classrooms-tbody');
            tbody.innerHTML = '';
            
            res.data.forEach(c => {
                tbody.innerHTML += `
                    <tr>
                        <td>${c.id}</td>
                        <td class="fw-medium">${c.name}</td>
                        <td>${c.tutor_name || '<span class="text-muted fst-italic">Sin tutor</span>'}</td>
                        <td class="text-end">
                            <button onclick='editClassroom(${JSON.stringify(c).replace(/'/g, "&apos;")})' class="btn btn-sm btn-outline-primary me-1">Editar</button>
                            <button onclick="deleteClassroom(${c.id})" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } catch (e) {
            alert('Error cargando aulas');
        }
    }

    function updateTutorSelect() {
        const select = document.getElementById('classroom-tutor');
        select.innerHTML = '<option value="">-- Sin tutor --</option>';
        allUsers.forEach(u => {
            if (u.role === 'profesor') {
                select.innerHTML += `<option value="${u.id}">${u.name} (${u.email})</option>`;
            }
        });
    }

    function openClassroomModal() {
        document.getElementById('classroom-id').value = '';
        document.getElementById('classroom-name').value = '';
        document.getElementById('classroom-tutor').value = '';
        document.getElementById('modalClassroomTitle').innerText = 'Nueva Aula';
        classroomModalInstance.show();
    }

    function editClassroom(classroom) {
        document.getElementById('classroom-id').value = classroom.id;
        document.getElementById('classroom-name').value = classroom.name;
        document.getElementById('classroom-tutor').value = classroom.tutor_id || '';
        document.getElementById('modalClassroomTitle').innerText = 'Editar Aula';
        classroomModalInstance.show();
    }

    async function saveClassroom(e) {
        e.preventDefault();
        const id = document.getElementById('classroom-id').value;
        const data = {
            name: document.getElementById('classroom-name').value,
            tutor_id: document.getElementById('classroom-tutor').value
        };

        const url = id ? `/admin/api/classrooms/${id}` : '/admin/api/classrooms';
        const method = id ? 'PATCH' : 'POST';

        try {
            const res = await fetchJson(url, { method, body: data });
            if (res.success) {
                classroomModalInstance.hide();
                loadClassrooms();
            } else {
                alert(res.error || 'Error al guardar');
            }
        } catch (e) {
            alert('Error de conexión');
        }
    }

    async function deleteClassroom(id) {
        if (!confirm('¿Seguro que deseas eliminar esta aula?')) return;
        try {
            const res = await fetchJson(`/admin/api/classrooms/${id}`, { method: 'DELETE' });
            if (res.success) {
                loadClassrooms();
            } else {
                alert(res.error || 'Error al eliminar');
            }
        } catch (e) {
            alert('Error de conexión');
        }
    }
</script>

</body>
</html>