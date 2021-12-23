<?php 

if (isset($_POST['new_user'])) {
  if (empty($_POST['name'])){
    $message = 'Please enter Name!';
    flash_alert('danger', $message);
    redirect('admin.php?action=users-add');
  }
  if (empty($_POST['last_name'])){
    $message = 'Please enter Last Name!';
    flash_alert('danger', $message);
    redirect('admin.php?action=users-add');
  }
  if (empty($_POST['email'])){
    $message = 'Please enter Email!';
    flash_alert('danger', $message);
    redirect('admin.php?action=users-add');
  }
  if (empty($_POST['password'])){
    $message = 'Please enter Password!';
    flash_alert('danger', $message);
    redirect('admin.php?action=users-add');
  }
}

// pa($_POST);

// pa($_FILES);

// Create
if(isset($_POST['new_user'])){
    $name = db_escape($_POST['name']);
    $last_name = db_escape($_POST['last_name']);
    $email = db_escape($_POST['email']);
    $age = db_escape($_POST['age']);
    $gender = db_escape($_POST['gender']);
    $password = db_escape(password_hash($_POST['password'], PASSWORD_DEFAULT));

    $avatar_name = '';
    if(isset($_FILES['avatar'])){
        $avatar_name = time() . '-' . $_FILES["avatar"]["name"];
        $file_path = "avatars/$avatar_name";
        move_uploaded_file($_FILES["avatar"]["tmp_name"], $file_path);
        resizeImage($file_path, $file_path, 300);
        $avatar_name = db_escape($avatar_name);
    }

    db_query("INSERT INTO users SET 
            `name` = '$name',
            last_name = '$last_name',
            email = '$email',
            age = '$age',
            gender = '$gender',
            `password` = '$password',
            avatar = '$avatar_name' ");
    $message = "User <b>$name</b> added successfuly!";
    flash_alert('success', $message);
    redirect('admin.php?action=users');
}

?>
<h2>Add user</h2>
<form action="?action=users-add" method="POST" enctype='multipart/form-data'>
    <div class="mb-3">
      <label for="formFile" class="form-label">Avatar</label>
      <input name="avatar" class="form-control" type="file" id="formFile">
    </div>
   <div class="row my-3">
      <div class="col">
         <label class="form-label">Name</label>
         <input name="name" value="<?= session_take_post('name')?>" type="text" class="form-control" placeholder="First name" aria-label="First name">
      </div>
      <div class="col">
         <label class="form-label">Last name</label>
         <input name="last_name" value="<?= session_take_post('last_name')?>" type="text" class="form-control" placeholder="Last name" aria-label="Last name">
      </div>
   </div>
   <div class="row my-3">
      <div class="col">
         <label class="form-label">Email</label>
         <input name="email" value="<?= session_take_post('email')?>" type="text" class="form-control" placeholder="Email" aria-label="First name">
      </div>
      <div class="col">
         <label class="form-label">Password</label>
         <input name="password" value="<?= session_take_post('password')?>" type="password" class="form-control" placeholder="Password" aria-label="Last name">
      </div>
   </div>
   <div class="row my-3">
      <div class="col">
         <label class="form-label">Age</label>
         <input name="age" type="number" min="1" max="150" class="form-control" placeholder="Age" aria-label="First name">
      </div>
      <div class="col">
         <label class="form-label">Gender</label>
         <select name="gender" class="form-select">
            <option value="male">male</option>
            <option value="female">female</option>
         </select>
      </div>
   </div>
   <button name="new_user" type="submit" class="btn btn-primary">Save</button>
</form>