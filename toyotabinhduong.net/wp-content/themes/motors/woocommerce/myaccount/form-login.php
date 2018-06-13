<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="row" id="customer_login">

	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

<?php endif; ?>

		<form method="post" class="login">
			
			<h4><?php _e( 'Login', 'motors' ); ?></h4>

			<?php do_action( 'woocommerce_login_form_start' ); ?>
			<?php if(stm_is_rental()): ?>
				<div class="stm-rent-fields-wrap">
			<?php endif; ?>
			<p class="form-row form-row-wide">
				<?php if(stm_is_rental()) : ?>
                    <label class="h4"><?php _e( 'LOGIN', 'motors' ); ?></label>
				    <div class="stm-rent-text-wrap">
                <?php endif; ?>
					<input type="text" class="input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" placeholder="<?php _e( 'Username or email address', 'motors' ); ?> *" />
                <?php if(stm_is_rental()) : ?>
				</div>
                <?php endif; ?>
			</p>
			<p class="form-row form-row-wide <?php if(stm_is_rental()) echo "stm-rent-pass";?>">
				<?php if(stm_is_rental()) : ?>
					<label class="h4"><?php _e( 'PASSWORD', 'motors' ); ?></label>
					<a class="lost_password" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'motors' ); ?></a>
					<div class="stm-rent-pass-wrap">
				<?php endif ?>
				<input class="input-text" type="password" name="password" id="password" placeholder="<?php _e( 'Password', 'motors' ); ?> *" />
				<?php if(stm_is_rental()):?>
					</div>
				<?php endif; ?>
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row form-row-login">
				<?php wp_nonce_field( 'woocommerce-login' ); ?>
				<?php if(stm_is_rental()): ?>
					<div class="stm-rent-btn-wrap button stm-button stm-button-icon stm-button-secondary-color ">
						<input type="submit" class="button" name="login" value="<?php _e( 'Login', 'motors' ); ?>" />
						<i class="fa fa-arrow-right"></i>
					</div>
				<?php else : ?>
					<input type="submit" class="button" name="login" value="<?php _e( 'Login', 'motors' ); ?>" />
				<?php endif;?>
				<label for="rememberme" class="inline">
					<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'motors' ); ?>
				</label>
			</p>
			<?php if(!stm_is_rental()): ?>
				<p class="lost_password">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'motors' ); ?></a>
				</p>
			<?php endif; ?>
			<div class="clear"></div>

			<?php if(stm_is_rental()) : ?>
				</div>
			<?php endif; ?>
			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

	</div>

	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

		<form method="post" class="register">
			
			<h4><?php _e( 'Register', 'motors' ); ?></h4>

			<?php do_action( 'woocommerce_register_form_start' ); ?>
			<?php if(stm_is_rental()): ?>
				<div class="stm-rent-fields-wrap">
			<?php endif; ?>
			
			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
				<p class="form-row form-row-wide">
					<?php if(stm_is_rental()) : ?>
						<label class="h4"><?php _e( 'LOGIN', 'motors' ); ?></label>
						<div class="stm-rent-text-wrap">
					<?php endif ?>
					<input type="text" class="input-text" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" placeholder="<?php _e( 'Username', 'motors' ); ?> *" />
					<?php if(stm_is_rental()): ?>
						</div>
					<?php endif; ?>
				</p>
			<?php endif; ?>

			<p class="form-row form-row-wide">
				<?php if(stm_is_rental()) : ?>
					<label class="h4"><?php _e( 'EMAIL', 'motors' ); ?></label>
					<div class="stm-rent-text-wrap">
				<?php endif ?>
				<input type="email" class="input-text" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" placeholder="<?php _e( 'Email address', 'motors' ); ?> *" />
				<?php if(stm_is_rental()): ?>
					</div>
				<?php endif; ?>
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="form-row form-row-wide">
					<?php if(stm_is_rental()) : ?>
						<label class="h4"><?php _e( 'PASSWORD', 'motors' ); ?></label>
						<div class="stm-rent-pass-wrap">
					<?php endif ?>
						<input type="password" class="input-text" name="password" id="reg_password" placeholder="<?php _e( 'Password', 'motors' ); ?> *" />
					<?php if(stm_is_rental()): ?>
						</div>
					<?php endif; ?>
				</p>

			<?php endif; ?>

			<!-- Spam Trap -->
			<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'motors' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

			<?php do_action( 'woocommerce_register_form' ); ?>
			<?php do_action( 'register_form' ); ?>

			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-register' ); ?>
				<?php if(stm_is_rental()): ?>
					<div class="stm-rent-btn-wrap button stm-button stm-button-icon stm-button-secondary-color ">
						<input type="submit" class="button" name="register" value="<?php _e( 'Register', 'motors' ); ?>" />
						<i class="fa fa-arrow-right"></i>
					</div>
				<?php else : ?>
					<input type="submit" class="button" name="register" value="<?php _e( 'Register', 'motors' ); ?>" />
				<?php endif;?>
				
			</p>

			<?php if(stm_is_rental()) : ?>
				</div>
			<?php endif; ?>
			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>
</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
