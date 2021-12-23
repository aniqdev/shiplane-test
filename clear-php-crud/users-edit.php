<?php 

// pa($_FILES);

// Create
if(isset($_POST['edit_user'])){
    $user_id = (int)$_POST['edit_user'];
    $name = db_escape($_POST['name']);
    $last_name = db_escape($_POST['last_name']);
    $email = db_escape($_POST['email']);
    $age = db_escape($_POST['age']);
    $gender = db_escape($_POST['gender']);
    // $password = db_escape(password_hash($_POST['password'], PASSWORD_DEFAULT));

    $user = db_query("SELECT avatar,`password` FROM users WHERE id = '$user_id'");
    $avatar_name = $user[0]['avatar'];
    $password = $user[0]['password'];

    if(isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0){

        if(file_exists("avatars/$avatar_name")) unlink("avatars/$avatar_name");

        $avatar_name = time() . '-' . $_FILES["avatar"]["name"];
        $file_path = "avatars/$avatar_name";
        move_uploaded_file($_FILES["avatar"]["tmp_name"], $file_path);
        resizeImage($file_path, $file_path, 300);
        $avatar_name = db_escape($avatar_name);
    }

    if ($_POST['password']) {
        $password = db_escape(password_hash($_POST['password'], PASSWORD_DEFAULT));
    }

    db_query("UPDATE users SET 
            name = '$name',
            last_name = '$last_name',
            email = '$email',
            age = '$age',
            gender = '$gender',
            avatar = '$avatar_name',
            `password` = '$password'
            WHERE id = '$user_id' ");
    redirect('admin.php?action=users');
}

$user_id = (int)$_GET['user_id'];
$user = db_query("SELECT * FROM users WHERE id = '$user_id' ");
$user = $user[0];

// pa($user);

$avatar = $user['avatar'] ? 'avatars/'.$user['avatar'] : 'https://via.placeholder.com/250';

?>
<h2>Edit user: <?= $user['name'] ?> <?= $user['last_name'] ?> (<?= $user['email'] ?>)</h2>
<form action="admin.php?action=users-edit&user_id=<?= $_GET['user_id'] ?? '' ?>" method="POST" enctype='multipart/form-data'>

<div class="row">
    <div class="col-lg-3">
        <img src="<?= $avatar ?>" class="img-thumbnail" alt="...">
        <div class="mb-3">
          <label for="formFile" class="form-label">Avatar</label>
          <input name="avatar" class="form-control" type="file" id="formFile">
        </div>
    </div>
    <div class="col-lg-9">
        
        <div class="row my-3">
          <div class="col">
          <label class="form-label">Name</label>
            <input name="name" value="<?= $user['name'] ?>" type="text" class="form-control" placeholder="First name" aria-label="First name">
          </div>
          <div class="col">
          <label class="form-label">Last name</label>
            <input name="last_name" value="<?= $user['last_name'] ?>" type="text" class="form-control" placeholder="Last name" aria-label="Last name">
          </div>
        </div>

        <div class="row my-3">
          <div class="col">
          <label class="form-label">Email</label>
            <input name="email" value="<?= $user['email'] ?>" type="text" class="form-control" placeholder="Email" aria-label="First name">
          </div>
          <div class="col">
          <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" placeholder="Password" aria-label="Last name">
          </div>
        </div>

        <div class="row my-3">
          <div class="col">
          <label class="form-label">Age</label>
            <input name="age" value="<?= $user['age'] ?>" type="number" min="1" max="150" class="form-control" placeholder="Age" aria-label="First name">
          </div>
          <div class="col">
          <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
            <option <?= if_selected($user['gender'], 'male') ?> value="male">male</option>
            <option <?= if_selected($user['gender'], 'female') ?> value="female">female</option>
            </select>
          </div>
        </div>

        <div class="row my-3">
          <div class="col">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" placeholder="enter new password">
          </div>
          <div class="col">

          </div>
        </div>

        <button name="edit_user" value="<?= $user['id'] ?>" type="submit" class="btn btn-primary">Save</button>

    </div>
</div>
</form>
