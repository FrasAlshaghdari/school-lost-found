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
    <title>Contact Messages - Admin Panel</title>
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
                    <a href="manage_users.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a href="contact_messages.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-envelope"></i> Contact Messages
                    </a>
                </div>
            </div>
            
            <div class="col-md-10">
                <h2 class="mb-4"><i class="fas fa-envelope"></i> Contact Messages</h2>
                
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" data-status="unread" href="#">
                            <i class="fas fa-envelope"></i> Unread
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-status="read" href="#">
                            <i class="fas fa-envelope-open"></i> Read
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-status="all" href="#">
                            <i class="fas fa-inbox"></i> All Messages
                        </a>
                    </li>
                </ul>
                
                <div id="messagesContainer">
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
        let currentStatus = 'unread';
        
        $(document).ready(function() {
            loadMessages(currentStatus);
            
            $('.nav-link').on('click', function(e) {
                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                currentStatus = $(this).data('status');
                loadMessages(currentStatus);
            });
        });

        function loadMessages(status) {
            $('#messagesContainer').html('<div class="text-center"><div class="spinner-border text-primary"></div></div>');
            
            $.ajax({
                url: '../api/admin/get_contact_messages.php',
                method: 'GET',
                data: { status: status },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayMessages(response.messages);
                    }
                }
            });
        }

        function displayMessages(messages) {
            let html = '';
            
            if (messages.length === 0) {
                html = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No messages found</div>';
            } else {
                messages.forEach(function(msg) {
                    const statusClass = msg.status === 'unread' ? 'primary' : 'secondary';
                    const statusIcon = msg.status === 'unread' ? 'envelope' : 'envelope-open';
                    
                    html += `
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-${statusClass} text-white">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-0">
                                            <i class="fas fa-${statusIcon}"></i> ${msg.subject}
                                            ${msg.status === 'unread' ? '<span class="badge bg-warning text-dark">NEW</span>' : ''}
                                        </h5>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <small>${new Date(msg.created_at).toLocaleString()}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong><i class="fas fa-user"></i> From:</strong> ${msg.name}</p>
                                        <p class="mb-1"><strong><i class="fas fa-envelope"></i> Email:</strong> <a href="mailto:${msg.email}">${msg.email}</a></p>
                                        ${msg.phone ? '<p class="mb-0"><strong><i class="fas fa-phone"></i> Phone:</strong> <a href="tel:' + msg.phone + '">' + msg.phone + '</a></p>' : ''}
                                    </div>
                                    <div class="col-md-6 text-end">
                                        ${msg.status === 'unread' ? 
                                            '<button class="btn btn-sm btn-success" onclick="markAsRead(' + msg.id + ')"><i class="fas fa-check"></i> Mark as Read</button>' : 
                                            '<button class="btn btn-sm btn-secondary" onclick="markAsUnread(' + msg.id + ')"><i class="fas fa-undo"></i> Mark as Unread</button>'
                                        }
                                        <button class="btn btn-sm btn-danger ms-2" onclick="deleteMessage(' + msg.id + ')"><i class="fas fa-trash"></i> Delete</button>
                                    </div>
                                </div>
                                <hr>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0" style="white-space: pre-wrap;">${msg.message}</p>
                                </div>
                                <div class="mt-3">
                                    <a href="mailto:${msg.email}?subject=Re: ${msg.subject}" class="btn btn-primary">
                                        <i class="fas fa-reply"></i> Reply via Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            
            $('#messagesContainer').html(html);
        }

        function markAsRead(messageId) {
            $.ajax({
                url: '../api/admin/update_message_status.php',
                method: 'POST',
                data: { message_id: messageId, status: 'read' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        loadMessages(currentStatus);
                    }
                }
            });
        }

        function markAsUnread(messageId) {
            $.ajax({
                url: '../api/admin/update_message_status.php',
                method: 'POST',
                data: { message_id: messageId, status: 'unread' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        loadMessages(currentStatus);
                    }
                }
            });
        }

        function deleteMessage(messageId) {
            if (confirm('Are you sure you want to delete this message? This cannot be undone.')) {
                $.ajax({
                    url: '../api/admin/delete_message.php',
                    method: 'POST',
                    data: { message_id: messageId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Message deleted successfully');
                            loadMessages(currentStatus);
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>