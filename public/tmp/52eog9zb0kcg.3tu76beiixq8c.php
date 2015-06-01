<div class="col-xs-12 admin-user-details page">
	<div class="col-xs-6">
	    <h3><?php echo $user_details["name"]; ?></h3>
	</div>

	<div class="col-xs-6">
		<form action="/admin/upload-file" method="post" enctype="multipart/form-data">
		    Select image to upload for <?php echo $user_details["name"]; ?>:<br>
		    <input type="file" name="file" id="file"><br>
		    <input type="hidden" name="user_id" value="<?php echo $user_details['id']; ?>"><br>
		    <input type="submit" name="submit">
		</form>
	</div>
	
	<div class="col-xs-6">
		<?php foreach (($files?:array()) as $file): ?>
			<div> <?php echo $file["file_name"]; ?> <a href="/view/<?php echo $file["file_name"]; ?>"> view </a> <a href="/download/<?php echo $file["file_name"]; ?>"> download </a></div>
		<?php endforeach; ?>
	</div>
</div>
