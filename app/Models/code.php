<!-- <div class="form-group">
                        <label><?= trans("number_of_posts_import"); ?></label>
                        <input type="number" class="form-control max-500" name="post_limit" placeholder="<?= trans("number_of_posts_import"); ?>" value="<?= old('post_limit'); ?>" min="1" max="500" required>
                    </div> -->



                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label><?= trans('add_posts_as_draft'); ?></label>
                            </div>
                            <div class="col-md-5 col-sm-12 col-option">
                                <input type="radio" name="add_posts_as_draft" value="1" id="add_posts_as_draft_1" class="square-purple">
                                <label for="add_posts_as_draft_1" class="option-label"><?= trans('yes'); ?></label>
                            </div>
                            <div class="col-md-5 col-sm-12 col-option">
                                <input type="radio" name="add_posts_as_draft" value="0" id="add_posts_as_draft_2" class="square-purple" checked>
                                <label for="add_posts_as_draft_2" class="option-label"><?= trans('no'); ?></label>
                            </div>
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Filters</label>
                            </div>
                            <div class="col-md-5 col-sm-12 col-option">
                                <input type="checkbox" name="filter_image" id="filter_image" class="square-purple" >
                                <label for="auto_update_enabled" class="option-label">No Image</label>
                            </div>
                            <div class="col-md-5 col-sm-12 col-option">
                                <input type="checkbox" name="filter_links" id="filter_lists" class="square-purple">
                                <label for="auto_update_disabled" class="option-label">No Secure links</label>
                            </div>
                            <div class="col-md-5 col-sm-12 col-option">
                                <input type="checkbox" name="filter_description" id="filter_description" class="square-purple" >
                                <label for="auto_update_enabled" class="option-label">No Description</label>
                            </div>
                            <div class="col-md-5 col-sm-12 col-option">
                                <input type="checkbox" name="filter_dupdesc" id="filter_dupdesc" class="square-purple">
                                <label for="auto_update_disabled" class="option-label">Duplicate description</label>
                            </div>
                            <div class="col-md-5 col-sm-12 col-option">
                                <input type="checkbox" name="filter_dates" id="filter_dates" class="square-purple" >
                                <label for="auto_update_enabled" class="option-label">No dates</label>
                            </div>
                            <div class="col-md-5 col-sm-12 col-option">
                                <input type="checkbox" name="filter_duptitle" id="filter_duptitle" class="square-purple">
                                <label for="auto_update_disabled" class="option-label">Duplicate titles</label>
                            </div>
                        </div>
                    </div>


                    feed.php   -30 
                    <th><?= trans('posts'); ?></th>
                    feed.php   -63
                    <td><?= getFeedPostsCount($item->id); ?></td>

                    update_feed.php   -23
                    
                    <div class="form-group">
                        <label><?= trans("number_of_posts_import"); ?></label>
                        <input type="number" class="form-control max-500" name="post_limit" placeholder="<?= trans("number_of_posts_import"); ?>" value="<?= esc($feed->post_limit); ?>" min="1" max="500">
                    </div>

                    update.php   -129 
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label><?= trans('add_posts_as_draft'); ?></label>
                            </div>
                            <div class="col-md-5 col-sm-12 col-option">
                                <input type="radio" name="add_posts_as_draft" value="1" id="add_posts_as_draft_1" class="square-purple" <?= $feed->add_posts_as_draft == 1 ? 'checked' : ''; ?>>
                                <label for="add_posts_as_draft_1" class="option-label"><?= trans('yes'); ?></label>
                            </div>
                            <div class="col-md-5 col-sm-12 col-option">
                                <input type="radio" name="add_posts_as_draft" value="0" id="add_posts_as_draft_2" class="square-purple" <?= $feed->add_posts_as_draft == 0 ? 'checked' : ''; ?>>
                                <label for="add_posts_as_draft_2" class="option-label"><?= trans('no'); ?></label>
                            </div>
                        </div>
                    </div>
