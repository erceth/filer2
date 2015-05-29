<div class="col-xs-12 admin-user-details page">
	<div class="col-xs-6">
		<?php foreach (($user_details?:array()) as $detail): ?>
		    <h3><?php echo $detail["name"]; ?></h3>
		<?php endforeach; ?>
	</div>

	<div class="col-xs-6">
		<form action="/admin/upload-file" method="post" enctype="multipart/form-data">
		    Select image to upload for <?php echo $detail["name"]; ?>:<br>
		    <input type="file" name="file" id="file"><br>
		    <input type="hidden" name="user_id" value="<?php echo $detail['id']; ?>"><br>
		    <input type="submit" name="submit">
		</form>
	</div>
	
</div>
