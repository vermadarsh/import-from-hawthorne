<div class="modal fade" id="hawthorne-shoot-cart-contents-modal" tabindex="-1" aria-labelledby="hawthorne-shoot-cart-contents-modal-label" aria-hidden="false">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="hawthorne-shoot-cart-contents-modal-label"><?php esc_html_e( 'Send Cart', 'import-from-hawthorne' ); ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div class="form-row">
						<div class="col-12 col-md-6">
							<div class="form-group">
								<input type="text" id="customer-name" class="form-control" placeholder="<?php esc_html_e( 'Name*', 'import-from-hawthorne' ); ?>">
								<span class="hawthorne-send-cart-error customer-name"></span>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group">
								<input type="text" id="customer-phone" class="form-control" placeholder="<?php esc_html_e( 'Phone Number*', 'import-from-hawthorne' ); ?>">
								<span class="hawthorne-send-cart-error customer-phone"></span>
							</div>
						</div>
						<div class="col-12 col-md-12">
							<div class="form-group">
								<input type="email" id="customer-email" class="form-control" placeholder="<?php esc_html_e( 'Email*', 'import-from-hawthorne' ); ?>">
								<span class="hawthorne-send-cart-error customer-email"></span>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<textarea id="customer-message" class="form-control" placeholder="<?php esc_html_e( 'Message', 'import-from-hawthorne' ); ?>" style="width: 100%; height: 100px;" spellcheck="false"></textarea>
							</div>
							<div class="form-group text-right">
								<button class="hawthorne-send-cart-to-greenlight btn btn-accent" type="button"><?php esc_html_e( 'Send Cart to Greenlight', 'import-from-hawthorne' ); ?></button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
