<?php
/**
 * Template: Overview / Home
 */
 
?>
<div class="wrap">
	<h2><?php esc_html_e('All Settings'); ?></h2>
	
	<form name="form" action="options.php" method="post" id="all-options">
		<?php wp_nonce_field('options-options') ?>
		<input type="hidden" name="action" value="update" />
		
		<div class="woa-search-section">
			<p>
				<input type="text" class="wide-text" name="search" /> <select name="search_col">
					<option value="value">Option value</option>
					<option value="name">Option name</option>
				</select>
				
				<button type="submit" class="button button-secondary"><?php _e('Search'); ?></button>
			</p>
			
		</div>
		
		<table class="form-table">
		<?php foreach( $options as $option ) :  ?>
			
			<tr>
				<th scope="row"><label for="<?php echo $option->label; ?>"><?php echo $option->name; ?></label></th>
				<td>					
			<?php if ( is_string( $option->value ) && strpos( $option->value, "\n" ) !== false ) { ?>
					<textarea class="all-options" name="<?php echo $option->label; ?>" id="<?php echo $option->label; ?>" cols="30" rows="5"><?php echo esc_textarea( $option->value ); ?></textarea>
			<?php } elseif( is_array( $option->value ) || is_object( $option->value ) ) { ?>
					<textarea class="large-text disabled" name="<?php echo $option->label; ?>" id="<?php echo $option->label; ?>" cols="30" rows="10"><?php echo esc_textarea( print_r( $option->value, true) ); ?></textarea>
			<?php } else { ?> 
					<input class="regular-text all-options" type="text" name="<?php echo $option->label; ?>" id="<?php echo $option->label; ?>" value="<?php echo esc_attr( $option->value ) ; ?>" />
			<?php } ?>
	
				</td>
			</tr>
			
	<?php  endforeach; ?>
	
		</table>
		
		<?php submit_button( __( 'Save Changes' ), 'primary', 'Update' ); ?>
	</form>
</div>


	
