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
                <?php if (hasPermission('add_post')): ?>
                    <div class="right">
                        <div class="dropdown">
                            <button class="btn btn-success btn-add-new dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-plus"></i><?= trans('add_collections'); ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" style="left: -20px;">
                                <li><a href="<?= adminUrl('add-post'); ?>"><?= trans('add_collections'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>     