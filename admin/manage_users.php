<?php 
require_once '../config.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group">
                    <a href="dashboard.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="manage_items.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-box"></i> Manage Items
                    </a>
                    <a href="manage_claims.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-clipboard-check"></i> Manage Claims
                    </a>
                    <a href="manage_users.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a href="contact_messages.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-envelope"></i> Contact Messages
                    </a>
                </div>
            </div>
            
            <div class="col-md-10">
                <h2 class="mb-4"><i class="fas fa-users"></i> Manage Users</h2>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Admin Permissions:</strong> You can promote users to admin or remove admin permissions (requires your password). You can also delete user accounts.
                </div>
                
                <div id="usersContainer">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            loadUsers();
        });

        function loadUsers() {
            $.ajax({
                url: '../api/admin/get_users.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayUsers(response.users);
                    }
                },
                error: function() {
                    $('#usersContainer').html('<div class="alert alert-danger">Error loading users</div>');
                }
            });
        }

        function displayUsers(users) {
            let html = '<div class="card shadow"><div class="card-body"><div class="table-responsive"><table class="table table-striped table-hover"><thead class="table-dark"><tr><th>ID</th><th>Username</th><th>Full Name</th><th>Email</th><th>Student ID</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead><tbody>';
            
            const currentUserId = <?php echo $_SESSION['user_id']; ?>;
            
            users.forEach(function(user) {
                const isCurrentUser = (user.id === currentUserId);
                
                html += `
                    <tr>
                        <td>${user.id}</td>
                        <td><strong>${user.username}</strong></td>
                        <td>${user.full_name}</td>
                        <td>${user.email}</td>
                        <td>${user.student_id || 'N/A'}</td>
                        <td><span class="badge bg-${user.role === 'admin' ? 'danger' : 'primary'}">${user.role.toUpperCase()}</span></td>
                        <td>${new Date(user.created_at).toLocaleDateString()}</td>
                        <td>
                            ${user.role === 'student' ? 
                                `<button class="btn btn-sm btn-success mb-1" onclick="makeAdmin(${user.id}, '${user.username}')">
                                    <i class="fas fa-user-shield"></i> Make Admin
                                </button>` : 
                                (isCurrentUser ? 
                                    '<span class="badge bg-info">You</span>' :
                                    `<button class="btn btn-sm btn-warning mb-1" onclick="removeAdmin(${user.id}, '${user.username}')">
                                        <i class="fas fa-user-minus"></i> Remove Admin
                                    </button>`
                                )
                            }
                            ${!isCurrentUser ? 
                                `<button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id}, '${user.username}')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>` : ''
                            }
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div></div></div>';
            $('#usersContainer').html(html);
        }

        function makeAdmin(userId, username) {
            const password = prompt(`⚠️ SECURITY CHECK ⚠️\n\nYou are about to make "${username}" an admin.\n\nPlease enter YOUR admin password to confirm:`);
            
            if (password === null || password === '') {
                alert('Action cancelled');
                return;
            }
            
            $.ajax({
                url: '../api/admin/update_user_role.php',
                method: 'POST',
                data: { 
                    user_id: userId, 
                    role: 'admin',
                    admin_password: password
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(`✓ Success!\n\n"${username}" is now an admin!`);
                        loadUsers();
                    } else {
                        alert('❌ Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error updating user role');
                }
            });
        }

        function removeAdmin(userId, username) {
            const password = prompt(`⚠️ SECURITY CHECK ⚠️\n\nYou are about to remove admin privileges from "${username}".\n\nPlease enter YOUR admin password to confirm:`);
            
            if (password === null || password === '') {
                alert('Action cancelled');
                return;
            }
            
            $.ajax({
                url: '../api/admin/update_user_role.php',
                method: 'POST',
                data: { 
                    user_id: userId, 
                    role: 'student',
                    admin_password: password
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(`✓ Success!\n\nAdmin privileges removed from "${username}".`);
                        loadUsers();
                    } else {
                        alert('❌ Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error updating user role');
                }
            });
        }

        function deleteUser(userId, username) {
            if (confirm(`⚠️ WARNING ⚠️\n\nAre you sure you want to DELETE "${username}"?\n\nThis will:\n- Delete their account permanently\n- Delete all items they reported\n- Delete all their claims\n\nThis CANNOT be undone!`)) {
                $.ajax({
                    url: '../api/admin/delete_user.php',
                    method: 'POST',
                    data: { user_id: userId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('User deleted successfully!');
                            loadUsers();
                        } else {
                            alert('Failed to delete user: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error deleting user');
                    }
                });
            }
        }
    </script>
</body>
</html>