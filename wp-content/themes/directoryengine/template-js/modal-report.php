<div class="modal fade modal-submit-questions" id="report" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title modal-title-sign-in" id="myModalLabel">
					<?php _e("Report Place", ET_DOMAIN) ?>
				</h4>
			</div>
			<div class="modal-body">		
		        <form id="submit_report" class="form_modal_style edit_profile_form">
		            <div class="form-field">	            
			            <label><?php _e("Message", ET_DOMAIN) ?></label>
			            <textarea id="message" name="message"></textarea>
		            </div>		            
		            <div class="clearfix"></div>
		            <div class="form-field">
		            	<input type="submit" name="submit" value="<?php _e("Send", ET_DOMAIN) ?>" class="btn-submit update_profile" />
		            </div>
		        </form>
			</div>
		</div>
	</div>
</div>