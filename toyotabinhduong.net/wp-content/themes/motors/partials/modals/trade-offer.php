<div class="modal" id="trade-offer" tabindex="-1" role="dialog" aria-labelledby="myModalLabelTradeOffer">
	<form id="request-trade-offer-form" action="<?php echo esc_url( home_url('/') ); ?>" method="post">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header modal-header-iconed">
					<?php if(stm_is_motorcycle()): ?>
						<i class="stm-moto-icon-trade"></i>
						<h3 class="modal-title" id="myModalLabelTestDrive"><?php esc_html_e('Trade Offer', 'motors') ?></h3>
						<div class="test-drive-car-name"><?php echo stm_generate_title_from_slugs(get_the_id()); ?></div>
					<?php else: ?>
						<i class="stm-moto-icon-cash"></i>
						<h3 class="modal-title" id="myModalLabelTestDrive"><?php esc_html_e('Offer Price', 'motors') ?></h3>
						<div class="test-drive-car-name"><?php echo stm_generate_title_from_slugs(get_the_id()); ?></div>
					<?php endif; ?>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<div class="form-modal-label"><?php esc_html_e('Name', 'motors'); ?></div>
								<input name="name" type="text"/>
							</div>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<div class="form-modal-label"><?php esc_html_e('Email', 'motors'); ?></div>
								<input name="email" type="email" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<div class="form-modal-label"><?php esc_html_e('Phone', 'motors'); ?></div>
								<input name="phone" type="tel" />
							</div>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<div class="form-modal-label"><?php esc_html_e('Trade price', 'motors'); ?></div>
								<div class="stm-trade-input-icon">
									<input name="trade_price" type="text" />
								</div>
							</div>
						</div>
					</div>
					<div class="mg-bt-25px"></div>
					<div class="row">
						<?php
							$recaptcha_enabled = get_theme_mod('enable_recaptcha',0);
							$recaptcha_public_key = get_theme_mod('recaptcha_public_key');
							$recaptcha_secret_key = get_theme_mod('recaptcha_secret_key');
							if(!empty($recaptcha_enabled) and $recaptcha_enabled and !empty($recaptcha_public_key) and !empty($recaptcha_secret_key)):
						?>
							<div class="col-md-8 col-sm-8">
								<div class="g-recaptcha" data-sitekey="<?php echo esc_attr($recaptcha_public_key); ?>" data-size="normal"></div>
							</div>
						<?php endif; ?>
						<div class="col-md-4 col-sm-4">
							<button type="submit" class="stm-request-test-drive"><?php esc_html_e("Request", 'motors'); ?></button>
							<div class="stm-ajax-loader" style="margin-top:10px;">
								<i class="stm-icon-load1"></i>
							</div>
						</div>
					</div>
					<div class="mg-bt-25px"></div>
					<input name="vehicle_id" type="hidden" value="<?php echo esc_attr(get_the_id()); ?>" />
				</div>
			</div>
		</div>
	</form>
</div>