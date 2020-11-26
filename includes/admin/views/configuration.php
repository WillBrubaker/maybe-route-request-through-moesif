<?php

defined( 'ABSPATH' ) || exit;
if ( isset( $_POST ) && 'set_moesif_collector_id' === $_POST['action'] ) {
	update_option( 'moesif_collector_id', sanitize_text_field( $_POST['moesif_collector_id'] ) );
}
?>
<div class="wide-fat">
	<h3>Moesif configuraiton</h3>
	<div class="moesif-collector-id">
		<form method="post">
				<p><label for="moesif_collector_id">Collector Application Id:<br/><input id="moesif_collector_id" type="password" name="moesif_collector_id" value="<?php echo get_option( 'moesif_collector_id', '' ); ?>"/></label></p>
				<input type="hidden" name="action" value="set_moesif_collector_id" />
					<input class="button-primary" type="submit" value="Save" />
		</form>
	</div>
</div>
