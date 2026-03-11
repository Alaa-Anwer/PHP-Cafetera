<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Users Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .page-title {
            font-weight: 600;
        }

        .user-img {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 50%;
        }

        .card {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
        }

        .table thead {
            background: #343a40;
            color: white;
        }
    </style>

</head>

<body>

    <div class="container py-5">

        <div class="card">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <h4 class="page-title mb-0">
                    Users Management
                </h4>

                <a href="/users/create" class="btn btn-primary">
                    + Add User
                </a>

            </div>

            <div class="card-body">

                <?php if (!empty($users)): ?>

                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Room</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($users as $user): ?>

                                    <tr>

                                        <td><?= $user['id'] ?></td>

                                        <td>

                                            <div class="d-flex align-items-center gap-2">

                                                <?php if (!empty($user['thumbnail'])): ?>

                                                    <img src="/uploads/<?= $user['thumbnail'] ?>" class="user-img">

                                                <?php else: ?>

                                                    <img src="https://ui-avatars.com/api/?name=<?= $user['name'] ?>"
                                                        class="user-img">

                                                <?php endif; ?>

                                                <span class="fw-semibold">
                                                    <?= htmlspecialchars($user['name']) ?>
                                                </span>

                                            </div>

                                        </td>

                                        <td>
                                            <?= htmlspecialchars($user['email']) ?>
                                        </td>

                                        <td>
                                            <span class="badge bg-secondary">
                                                Room <?= $user['room_id'] ?>
                                            </span>
                                        </td>

                                        <td>

                                            <?php if ($user['role'] == 'admin'): ?>

                                                <span class="badge bg-danger">
                                                    Admin
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-success">
                                                    User
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>
                                            <?= date('d M Y', strtotime($user['created_at'])) ?>
                                        </td>

                                        <td class="text-center">

                                            <form action="/users/<?= $user['id'] ?>/delete" method="POST"
                                                style="display:inline">

                                                <button class="btn btn-sm btn-outline-danger">
                                                    Delete
                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="text-center py-5">

                        <h5 class="text-muted">
                            No users found
                        </h5>

                        <a href="/users/create" class="btn btn-primary mt-3">
                            Create First User
                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</body>

</html>