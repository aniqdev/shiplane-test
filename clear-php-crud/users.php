<?php

// Delete
if(!empty($_GET['delete'])){
    $user_id = (int)$_GET['delete'];
    db_query("DELETE FROM users WHERE id = '$user_id' ");
    header('Location: ?action=users');
}

$offset = $_GET['offset'] ?? 0;
$limit = $_GET['limit'] ?? 5;

$total_count = db_query("SELECT count(*) FROM users");
$total_count = $total_count ? $total_count[0]['count(*)'] : 0;

$users = db_query("SELECT * FROM users LIMIT $limit OFFSET $offset ");

// pa($users);
?>
<h2>Users</h2>

<?php include 'blocks/admin-page-top.php'; ?>

<table class="table table-striped">
    <tr>
        <th></th>
        <th>Name</th>
        <th>Last name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Age</th>
        <th>Gender</th>
        <th></th>
    </tr>
    <?php foreach($users as $user): ?>
        <tr>
            <td>
                <img class="admin-users-avatar" src="avatars/<?= $user['avatar'] ?>" alt="">
            </td>
            <td><?= $user['name'] ?></td>
            <td><?= $user['last_name'] ?></td>
            <td><?= $user['email'] ?></td>
            <td><?= $user['role'] ?></td>
            <td><?= $user['age'] ?></td>
            <td><?= $user['gender'] ?></td>
            <td>
                <a href="?action=users-edit&user_id=<?= $user['id'] ?>" class="me-3 text-success"><i class="bi bi-pencil"></i></a>
                <a onclick="if(!confirm('Are you sure?')) return false" href="?action=users&delete=<?= $user['id'] ?>"><i class="bi bi-trash text-danger"></i></a>
            </td>
        </tr>
    <?php endforeach ?>
</table>