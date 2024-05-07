<div class="row">
    <div class="col-sm-12">
        <?= view('admin/includes/_messages'); ?>
    </div>
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= $title; ?></h3>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" role="grid">
                            <div class="row table-filter-container">
                                <div class="col-sm-12">
                                    <form action="PostController/drafts" method="get">

                                        <div class="item-table-filter" style="width: 80px; min-width: 80px;">
                                            <label><?= trans("show"); ?></label>
                                            <select name="show" class="form-control">
                                                <option value="15" <?= inputGet('show', true) == '15' ? 'selected' : ''; ?>>15</option>
                                                <option value="30" <?= inputGet('show', true) == '30' ? 'selected' : ''; ?>>30</option>
                                                <option value="60" <?= inputGet('show', true) == '60' ? 'selected' : ''; ?>>60</option>
                                                <option value="100" <?= inputGet('show', true) == '100' ? 'selected' : ''; ?>>100</option>
                                            </select>
                                        </div>
                                        <div class="item-table-filter">
                                            <label><?= trans("language"); ?></label>
                                            <select name="lang_id" class="form-control" onchange="getParentCategoriesByLang(this.value);">
                                                <option value=""><?= trans("all"); ?></option>
                                                <?php foreach ($languages as $language): ?>
                                                    <option value="<?= $language->id; ?>" <?= inputGet('lang_id', true) == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="item-table-filter">
                                            <label><?= trans("post_type"); ?></label>
                                            <select name="post_type" class="form-control">
                                                <option value=""><?= trans("all"); ?></option>
                                                <option value="post" <?= inputGet('post_type', true) == 'post' ? 'selected' : ''; ?>><?= trans("post"); ?></option>
                                                <option value="video" <?= inputGet('post_type', true) == 'video' ? 'selected' : ''; ?>><?= trans("video"); ?></option>
                                            </select>
                                        </div>

                                        <?php if (isAdmin()): ?>
                                            <div class="item-table-filter">
                                                <label><?= trans("author"); ?></label>
                                                <select name="author" class="form-control">
                                                    <option value=""><?= trans("all"); ?></option>
                                                    <?php if (!empty($authors)):
                                                        foreach ($authors as $author): ?>
                                                            <option value="<?= $author->id; ?>"
                                                                <?= inputGet('author', true) == $author->id ? 'selected' : ''; ?>>
                                                                <?= esc($author->username); ?>
                                                            </option>
                                                        <?php endforeach;
                                                    endif; ?>
                                                </select>
                                            </div>
                                        <?php endif; ?>

                                        <div class="item-table-filter">
                                            <label><?= trans('category'); ?></label>
                                            <select id="categories" name="category" class="form-control" onchange="getSubCategories(this.value);">
                                                <option value=""><?= trans("all"); ?></option>
                                                <?php $categoryModel = new \App\Models\CategoryModel();
                                                $categories = $categoryModel->getParentCategories();
                                                if (!empty(inputGet('lang_id', true))) {
                                                    $categories = $categoryModel->getParentCategoriesByLang(inputGet('lang_id', true));
                                                }
                                                if (!empty($categories)):
                                                    foreach ($categories as $item): ?>
                                                        <option value="<?= $item->id; ?>" <?= inputGet('category', true) == $item->id ? 'selected' : ''; ?>>
                                                            <?= esc($item->name); ?>
                                                        </option>
                                                    <?php endforeach;
                                                endif; ?>
                                            </select>
                                        </div>
                                        <div class="item-table-filter">
                                            <div class="form-group">
                                                <label class="control-label"><?= trans('subcategory'); ?></label>
                                                <select id="subcategories" name="subcategory" class="form-control">
                                                    <option value=""><?= trans("all"); ?></option>
                                                    <?php if (!empty(inputGet('category', true))):
                                                        $subcategories = $categoryModel->getAllSubcategoriesByParentId(inputGet('category', true));
                                                        if (!empty($subcategories)):
                                                            foreach ($subcategories as $item):?>
                                                                <option value="<?= $item->id; ?>" <?= inputGet('subcategory', true) == $item->id ? 'selected' : ''; ?>><?= $item->name; ?></option>
                                                            <?php endforeach;
                                                        endif;
                                                    endif; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="item-table-filter">
                                            <div class="form-group">
                                                <label class="control-label">RSS Feed URLs</label>
                                                <select id="rssfeedurls" name="rssfeedurls" class="form-control">
                                                    <option value="">All</option>
                                                    <?php 
                                                        $feedModel = new \App\Models\RssModel();
                                                        $feeds = $feedModel->getFeeds();
                                                        if (!empty($feeds)):
                                                            foreach ($feeds as $item):?>
                                                                <option value="<?= $item->id; ?>" <?= inputGet('rssfeedurls', true) == $item->id ? 'selected' : ''; ?>><?= $item->feed_name; ?></option>
                                                            <?php endforeach;
                                                        endif;
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Filters</label>
                                                </div>
                                                <div class="col-md-4 col-sm-6 ">
                                                    <input type="checkbox" name="noimage" <?= inputGet('noimage', true) == 'on' ? 'checked' : ''; ?>>
                                                    <label for="noimage" class="option-label">No Image</label>
                                                </div>
                                                <div class="col-md-4 col-sm-6 ">
                                                    <input type="checkbox" name="links" <?= inputGet('links', true) == 'on' ? 'checked' : ''; ?>  >
                                                    <label for="links" class="option-label">No Secure links</label>
                                                </div>
                                                <div class="col-md-4 col-sm-6 ">
                                                    <input type="checkbox" name="nodesc" <?= inputGet('nodesc', true) == 'on' ? 'checked' : ''; ?>  >
                                                    <label for="nodesc" class="option-label">No Description</label>
                                                </div>
                                                <div class="col-md-4 col-sm-6 ">
                                                    <input type="checkbox" name="dupdesc" <?= inputGet('dupdesc', true) == 'on' ? 'checked' : ''; ?> >
                                                    <label for="dupdesc" class="option-label">Duplicate description</label>
                                                </div>
                                                <div class="col-md-4 col-sm-6 ">
                                                    <input type="checkbox" name="nodates" <?= inputGet('nodates', true) == 'on' ? 'checked' : ''; ?>  >
                                                    <label for="nodates" class="option-label">No dates</label>
                                                </div>
                                                <div class="col-md-4 col-sm-6 ">
                                                    <input type="checkbox" name= "duptitle" <?= inputGet('duptitle', true) == 'on' ? 'checked' : ''; ?>  >
                                                    <label for="duptitle" class="option-label">Duplicate titles</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>BlackList</label>
                                                    <input name="black" class="form-control" placeholder="Blacklist splited by comma" type="search" value="<?= esc(inputGet('black', true)); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                <label>WhiteList</label>
                                                <input name="white" class="form-control" placeholder="WhiteList splited by comma" type="search" value="<?= esc(inputGet('white', true)); ?>">
                                            </div>
                                            </div>
                                        </div>

                                        <div class="item-table-filter">
                                            <label><?= trans("search"); ?></label>
                                            <input name="q" class="form-control" placeholder="Search" type="search" value="<?= esc(inputGet('q', true)); ?>">
                                        </div>

                                        <div class="item-table-filter md-top-10" style="width: 65px; min-width: 65px;">
                                            <label style="display: block">&nbsp;</label>
                                            <button type="submit" class="btn bg-purple"><?= trans("filter"); ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                                <thead>
                                <tr role="row">
                                    <th width="20"><input type="checkbox" class="checkbox-table" id="checkAll"></th>
                                    <th width="20"><?= trans('id'); ?></th>
                                    <th><?= trans('post'); ?></th>
                                    <th><?= trans('language'); ?></th>
                                    <th><?= trans('category'); ?></th>
                                    <th><?= trans('author'); ?></th>
                                    <th></th>
                                    <th><?= trans('date'); ?></th>
                                    <th class="max-width-120"><?= trans('options'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($posts)):
                                    foreach ($posts as $item): ?>
                                        <tr>
                                            <td><input type="checkbox" name="checkbox-table" class="checkbox-table" value="<?= $item->id; ?>"></td>
                                            <td><?= esc($item->id); ?></td>
                                            <td>
                                                <div class="post-item-table">
                                                    <div class="post-image">
                                                        <img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="<?= getPostImage($item, 'small'); ?>" alt="" class="lazyload img-responsive"/>
                                                    </div>
                                                    <?= esc($item->title); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php $lang = getLanguageById($item->lang_id);
                                                if (!empty($lang)):
                                                    echo esc($lang->name);
                                                endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $categoryArray = getCategoryArray($item->category_id);
                                                if (!empty($categoryArray['parentCategory'])):?>
                                                    <label class="label label-table m-r-5 bg-primary">
                                                        <?= esc($categoryArray['parentCategory']->name); ?>
                                                    </label>
                                                <?php endif;
                                                if (!empty($categoryArray['subcategory'])):?>
                                                    <label class="label label-table m-r-5 bg-gray">
                                                        <?= esc($categoryArray['subcategory']->name); ?>
                                                    </label>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php $author = getUser($item->user_id);
                                                if (!empty($author)): ?>
                                                    <a href="<?= base_url(); ?>/profile/<?= esc($author->slug); ?>" target="_blank" class="table-link">
                                                        <strong><?= esc($author->username); ?></strong>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td class="td-post-sp">
                                                <?php if ($item->visibility == 1): ?>
                                                    <label class="label label-success label-table"><i class="fa fa-eye"></i></label>
                                                <?php else: ?>
                                                    <label class="label label-danger label-table"><i class="fa fa-eye"></i></label>
                                                <?php endif;
                                                if ($item->is_slider): ?>
                                                    <label class="label bg-olive label-table"><?= trans('slider'); ?></label>
                                                <?php endif;
                                                if ($item->is_picked): ?>
                                                    <label class="label bg-aqua label-table"><?= trans('our_picks'); ?></label>
                                                <?php endif;
                                                if ($item->need_auth): ?>
                                                    <label class="label label-warning label-table"><?= trans('only_registered'); ?></label>
                                                <?php endif; ?>
                                            </td>
                                            <td class="nowrap"><?= dateFormatDefault($item->created_at); ?></td>
                                            <td>
                                                <form action="<?= base_url('PostController/postOptionsPost'); ?>" method="post">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?= $item->id; ?>">
                                                    <div class="dropdown">
                                                        <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?>
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu options-dropdown">
                                                            <li>
                                                                <button type="submit" name="option" value="publish_draft" class="btn-list-button"><i class="fa fa-location-arrow option-icon"></i><?= trans('publish'); ?></button>
                                                            </li>
                                                            <li>
                                                                <?php if ($item->post_type == 'petition'): ?>
                                                                    <a href="<?= adminUrl('edit-petition/' . $item->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
                                                                <?php else: ?>
                                                                    <a href="<?= adminUrl('edit-post/' . $item->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
                                                                <?php endif;?>
                                                                
                                                                
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0)" onclick="deleteItem('PostController/deletePost','<?= $item->id; ?>','<?= trans("confirm_post"); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>

                            <div class="col-sm-12 table-ft">
                                <div class="row">
                                    <div class="pull-right">
                                        <?= view('partials/_pagination'); ?>
                                    </div>
                                    <?php if (count($posts) > 0): ?>
                                        <div class="pull-left">
                                            <button class="btn btn-sm btn-danger btn-table-delete" onclick="deleteSelectedPosts('<?= trans("confirm_posts"); ?>');"><?= trans('delete'); ?></button>
                                        </div>

                                        <div class="pull-left">
                                            <button class="btn btn-sm btn-info btn-table-info" onclick="publishSelectedPosts('<?= trans("confirm_posts"); ?>');">Publish</button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .options-dropdown {
        left: -40px;
    }
</style>
