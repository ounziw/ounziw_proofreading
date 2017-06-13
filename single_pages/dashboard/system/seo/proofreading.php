<?php     defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" id="site-form" action="<?php    echo $this->action('save_settings'); ?>" enctype="multipart/form-data">

<?php    echo $this->controller->token->output('save_settings'); ?>

    <fieldset>
        <legend><?php  echo t('Enter your Yahoo API.'); ?></legend>
        <div class="form-group">
<?php    echo $form->text('ounziw_proofreading', $proofreading);?>
        </div>
    </fieldset>
    <fieldset>
        <legend><?php  echo t('SSL'); ?></legend>
        <div class="form-group">
            <label>
            <?php  echo $form->checkbox('ounziw_proofreading_ssl', 1, $proofreading_ssl);?>
            <?php  echo t('Use SSL to connect to Yahoo.');?>
                </label>
        </div>
        <p><?php echo t('If your environment allows, it is recommended to enable SSL.');?></p>
    </fieldset>
    <fieldset>
        <legend><?php  echo t('Optional: HTML Tag and/or ID/class for your main contents'); ?></legend>
        <div class="form-group">
            <?php echo $form->text('ounziw_proofreading_class', $proofreading_class);?>
        </div>
        <p><?php  echo t('If you enter the HTML and/or ID/class such as main, div#main or div.main-contents, this addon will search the content according to the specified tag and/or ID/class. If you leave this field blank, div.ccm-page will be used as default.'); ?></p>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <button class="pull-right btn btn-success" type="submit" ><?php    echo t('Save'); ?></button>
    </div>
    </div>

</form>
