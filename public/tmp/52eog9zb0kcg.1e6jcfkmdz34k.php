<div class="col-xs-12 admin page">
	<?php foreach (($users?:array()) as $user): ?>
	    <p>name: <a href="/admin/user/<?php echo $user['id']; ?>"><?php echo $user["name"]; ?></a></p>
	<?php endforeach; ?>
</div>
