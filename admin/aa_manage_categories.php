<?

require 'login_check.php';

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'aa_functions.php';

include 'aa_header.php'; ?>

<div class="Title">
	<?= $site_name; ?> Categories
</div>
<?
	$topCats = $mysqli->query("SELECT * FROM $database WHERE type='Category' && relation='Primary' ORDER BY sort ASC");

	if($topCats){
		?>
		<div class="Label">
			Add New Primary Category
		</div>
		<div class="LabelInsert">
			<input type="text" name="category" value="" class="text Primary">

			<div class="Submit">
				<input type="submit" value="Add Category">
			</div>
		</div>

		<ul class="Categories Manage">
		<?

		$html = iterate_categories($topCats, [], true);
		
		echo $html;

		?></ul><?
	}
	else{
		?>
		<div class="Label">
			Add Your First Category
		</div>
		<div class="LabelInsert">
			<input type="text" name="category" value="" class="text Primary">
		</div>

		<div class="Submit">
			<input type="submit" value="Add Category">
		</div>
		<?
	}
?>

<?	include 'aa_footer.php'; ?>
