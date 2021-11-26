<div class="position-fixed hawthorne-notification-wrapper p-3" style="z-index: 5; right: 0; bottom: 0;">
	<!-- 
		classes for Bg color
		Notice : bg-warning
		Error  : bg-danger
		success: bg-success

		for Icon
		Notice : <span class="fa fa-exclamation-circle mr-2"></span>
		Error  : <span class="fa fa-skull-crossbones mr-2"></span>
		success: <span class="fa fa-check-circle mr-2"></span>


        For JS

        in this class class="hawthorne-notification toast fade hide"

        remove hide and add show class !!

	-->
	<div class="hawthorne-notification toast fade hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="6000" data-animation="false">
		<div class="toast-header bg-transparent">
			<span class="hawthorne-notification-icon fa mr-2"></span>
			<strong class="hawthorne-notification-heading mr-auto">
                Hey Title
            </strong>
			<button type="button" class="close" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body hawthorne-notification-message">
            Lorem ipsum dolor sit amet consectetur, adipisicing elit. Earum, error, porro distinctio aliquam autem maxime nostrum quaerat officiis ut at vitae accusamus corporis dignissimos alias voluptas veritatis recusandae iusto tenetur?
        </div>
	</div>
</div>
