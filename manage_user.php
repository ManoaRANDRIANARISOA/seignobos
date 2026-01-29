<?php
session_start();
require "config.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    exit("<p style='color:red;'>Accès refusé</p>");
}

$users = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="users-container">
<h2>Gestion des utilisateurs</h2>

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Nom</th>
    <th>Email</th>
    <th>Rôle</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach($users as $u): ?>
<tr id="user-<?= $u['id'] ?>">
    <td><?= $u['id'] ?></td>
    <td><?= htmlspecialchars($u['username']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td>
        <select onchange="changeRole(<?= $u['id'] ?>, this.value)">
            <option value="user" <?= $u['role']==='user'?'selected':'' ?>>User</option>
            <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
        </select>
    </td>
    <td>
        <button onclick="deleteUser(<?= $u['id'] ?>)"><i class="fas fa-trash"></i> Supprimer</button>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<script>
function changeRole(id, role){
    fetch("change_role.php",{
        method:"POST",
        headers:{ "Content-Type":"application/x-www-form-urlencoded" },
        body:"id="+id+"&role="+role
    }).then(()=>alert("Rôle mis à jour"));
}

function deleteUser(id){
    if(!confirm("Supprimer cet utilisateur ?")) return;
    fetch("delete_user.php",{
        method:"POST",
        headers:{ "Content-Type":"application/x-www-form-urlencoded" },
        body:"id="+id
    }).then(()=>{
        document.getElementById("user-"+id)?.remove();
    });
}
</script>

<style>
.users-container{padding:20px; overflow-x:auto;}
table{width:100%; border-collapse:collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);}
th,td{border:1px solid #eee; padding:12px; text-align: left;}
th{background: linear-gradient(135deg, #f5a623, #f76b1c); color:#fff; font-weight: 600;}
select{padding:8px; border-radius:6px; border: 1px solid #ccc; font-family: 'Poppins', sans-serif;}
button{background: #e74c3c; color:#fff;border:none;padding:8px 14px;border-radius:6px;cursor:pointer; font-weight: 600; font-family: 'Poppins', sans-serif; transition: 0.2s;}
button:hover{filter:brightness(1.1); transform: translateY(-1px);}
</style>
