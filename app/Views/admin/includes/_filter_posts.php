<div class="row table-filter-container">
    <div class="col-sm-12">
        <form action="<?= $formAction; ?>" method="get">

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

<script>
    $('.square-purple').click(function(e){
        alert("afdad");
        // alert(e.name);
    });
</script>