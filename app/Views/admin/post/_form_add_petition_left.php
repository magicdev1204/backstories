<div class="box">
	<div class="box-header with-border">
		<div class="left">
			<h3 class="box-title"><?= trans('petition_details'); ?></h3>
		</div>
	</div>
	<div class="box-body">
		<?= view('admin/includes/_messages'); ?>
		<div class="form-group">
			<label class="control-label"><?= trans('title'); ?></label>
			<input type="text" class="form-control" name="title" placeholder="<?= trans('title'); ?>" value="<?= old('title'); ?>" required>
		</div>

		<div class="form-group">
			<label class="control-label"><?= trans('slug'); ?>
				<small>(<?= trans('slug_exp'); ?>)</small>
			</label>
			<input type="text" class="form-control" name="title_slug" placeholder="<?= trans('slug'); ?>" value="<?= old('title_slug'); ?>">
		</div>

		<div class="form-group">
			<label class="control-label"><?= trans('summary'); ?> & <?= trans("description"); ?> (<?= trans('meta_tag'); ?>)</label>
			<textarea class="form-control text-area" name="summary" placeholder="<?= trans('summary'); ?> & <?= trans("description"); ?> (<?= trans('meta_tag'); ?>)"><?= old('summary'); ?></textarea>
		</div>

		<div class="form-group">
			<label class="control-label"><?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)</label>
			<input type="text" class="form-control" name="keywords" placeholder="<?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)" value="<?= old('keywords'); ?>">
		</div>

		<?php if (isAdmin()): ?>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-3 col-xs-12">
						<label><?= trans('add_slider'); ?></label>
					</div>
					<div class="col-md-2 col-sm-4 col-xs-12 col-option">
						<input type="checkbox" name="is_slider" value="1" class="square-purple">
					</div>
				</div>
			</div>
		<?php else: ?>
			<input type="hidden" name="is_slider" value="0">
		<?php endif; ?>

		<?php if (isAdmin()): ?>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-3 col-xs-12">
						<label><?= trans('add_picked'); ?></label>
					</div>
					<div class="col-md-2 col-sm-4 col-xs-12 col-option">
						<input type="checkbox" name="is_picked" value="1" class="square-purple">
					</div>
				</div>
			</div>
		<?php else: ?>
			<input type="hidden" name="is_picked" value="0">
		<?php endif; ?>

		<div class="form-group">
			<div class="row">
				<div class="col-sm-3 col-xs-12">
					<label><?= trans('show_only_registered'); ?></label>
				</div>
				<div class="col-md-2 col-sm-4 col-xs-12 col-option">
					<input type="checkbox" name="need_auth" value="1" class="square-purple">
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="row">
				<div class="col-sm-12">
					<label class="control-label"><?= trans('tags'); ?></label>
					<input id="tags_1" type="text" name="tags" class="form-control tags"/>
					<small>(<?= trans('type_tag'); ?>)</small>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="row">
				<div class="col-sm-12">
					<label class="control-label"><?= trans('optional_url'); ?></label>
					<input type="text" class="form-control" name="optional_url" placeholder="<?= trans('optional_url'); ?>" value="<?= old('optional_url'); ?>">
				</div>
			</div>
		</div>

		<div class="form-group recipient_list">
			<div class="row">
				<div class="col-sm-12">
					<button class="btn btn-sm bg-purple pull-right" type="button" id="add_recipient"><?= trans('add_recipient'); ?></button>
				</div>
			</div>
			<div class="row recipient" style="margin-top: 10px;">
				<div class="col-sm-5">
					<label class="control-label"><?= trans('recipient_name'); ?></label>
					<input id="recipient_name" type="text" name="recipient_name[]" class="form-control recipient_name" placeholder="<?= trans('recipient_name'); ?>" />
				</div>
				<div class="col-sm-5">
					<label class="control-label"><?= trans('recipient_email'); ?></label>
					<input id="recipient_email" type="text" name="recipient_email[]" class="form-control recipient_email" placeholder="<?= trans('recipient_email'); ?>"/>
				</div>
				<div class="col-sm-2">
					<label class="control-label">Action</label>
					<button type="button" class="form-control btn btn-sm bg-purple remove_recipient"><i class="fa fa-times option-icon"></i></button>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-12">
					<label class="control-label"><?= trans('signature'); ?></label>
					<input type="text" class="form-control" name="signature" placeholder="<?= trans('signature'); ?>" value="<?= old('signature'); ?>">
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<label class="control-label"><?= trans('content'); ?></label>
		<div class="row">
			<div class="col-sm-12 editor-buttons">
				<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#image_file_manager" data-image-type="editor"><i class="fa fa-image"></i>&nbsp;&nbsp;&nbsp;<?= trans("add_image"); ?></button>
			</div>
		</div>
		<textarea class="tinyMCE form-control" name="content"><?= old('content'); ?></textarea>
	</div>
</div>