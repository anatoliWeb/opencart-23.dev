<?php
// array yes/no
$arrayYesNo = array(
    array(
        'value' => '1',
        'name'  =>  $_->text_yes
    ),
    array(
        'value' => '0',
        'name'  =>  $_->text_no
    )
);

// array Enabled/Disabled
$arrayEnabledDisabled = array(
    array(
        'value' => '1',
        'name'  =>  $_->text_enabled
    ),
    array(
        'value' => '0',
        'name'  =>  $_->text_disabled
    )
);



?><?= $header, $column_left; ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-no2pay4" data-toggle="tooltip" title="<?= $_->button_save;?>" class="btn btn-primary">
                    <i class="fa fa-save"></i>
                </button>
                <a href="<?= $link->cancel; ?>" data-toggle="tooltip" title="<?= $_->button_cancel; ?>" class="btn btn-default">
                    <i class="fa fa-reply"></i>
                </a>
                <a href="<?= $link->search; ?>" data-toggle="tooltip" title="<?= $_->button_search; ?>" class="btn btn-info">
                    <i class="fa fa-search"></i>
                </a>
            </div>
            <h1><?= $_->heading_title; ?></h1>
            <p><?= $_->heading_description ?></p>
            <div>
                <ul class="breadcrumb">
                    <?php foreach ($breadcrumbs as $breadcrumb):?>
                        <li>
                            <a href="<?= $breadcrumb['href']; ?>"><?= $breadcrumb['text']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        <?php if ($error_warning): ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i>
                <?= $error['error_warning']; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-pencil"></i>
                    <?= $_->text_edit; ?>
                </h3>
            </div>
            <div class="panel-body">
                <form action="<?= $link->action; ?>" method="post" enctype="multipart/form-data" id="form-no2pay4" class="form-horizontal">
                    <div class="tab-pane active" id="tab-general">
                        <?= $buildForm->input(array(
                            'type'=>'hidden',
                            'name'=>'no2pay4_password_hash',
                            'value'=>$no2pay4_password
                        ));?>

                        <?= $buildForm->select(array(
                            'label_title' => $_->entry_status,
                            'name'  =>  'no2pay4_status',
                            'options'  =>   $arrayEnabledDisabled,
                            'value' =>  $no2pay4_status,
                        ))?>

                        <?= $buildForm->input(array(
                            'label_title'=>$_->login,
                            'type'=>'text',
                            'name'=>'no2pay4_login',
                            'value'=>$no2pay4_login,
                            'placeholder'=>$_->login,
                            'id'=>'input-login'
                        ));?>

                        <?= $buildForm->input(array(
                            'label_title'=>$_->password,
                            'type'=>'password',
                            'name'=>'no2pay4_password',
                            'placeholder'=>$_->_('*********'),
                            'id'=>'input-password'
                        ));?>

                        <?= $buildForm->input(array(
                            'label_title'=>$_->key,
                            'type'=>'text',
                            'name'=>'no2pay4_key',
                            'value'=>$no2pay4_key,
                            'placeholder'=>$_->key,
                            'id'=>'input-key'
                        ));?>

                        <?= $buildForm->input(array(
                            'label_title'=>$_->_('private key'),
                            'type'=>'text',
                            'name'=>'no2pay4_private_key',
                            'value'=>$no2pay4_private_key,
                            'placeholder'=>$_->_('private key'),
                            'id'=>'input-private-key'
                        ));?>

                        <?= $buildForm->input(array(
                            'label_title'=>$_->text_merchantnumber,
                            'type'=>'text',
                            'name'=>'no2pay4_merchant_number',
                            'value'=>$no2pay4_merchant_number,
                            'placeholder'=>$_->text_merchantnumber,
                            'id'=>'input-merchantnumber'
                        ));?>

                        <?= $buildForm->select(array(
                            'label_title' => $_->_('Processing of types'),
                            'name'      =>  'no2pay4_typedateinput',
                            'options'  =>   array(
                                array(
                                    'name'  =>  $_->iframe,
                                    'value' =>  '1'
                                ),
                                array(
                                    'name'  =>  $_->redirect,
                                    'value' =>  '2'
                                ),
                                array(
                                    'name'  =>  $_->_('Dirrect call'),
                                    'value' =>  '3'
                                )
                            ),
                            'value' =>  $no2pay4_typedateinput,
                        ))?>

                        <?= $buildForm->select(array(
                            'label_title' => $_->text_paymentwindow,
                            'name'      =>  'no2pay4_paymentwindow',
                            'options'  =>   array(
                                array(
                                    'name'  =>  $_->text_paymentwindow_overlay,
                                    'value' =>  '1'
                                )
                            ),
                            'value' =>  $no2pay4_paymentwindow,
                        ))?>

                        <?= $buildForm->input(array(
                            'label_help'=>$_->help_total,
                            'label_title'=>$_->entry_total,
                            'type'=>'text',
                            'name'=>'no2pay4_total',
                            'value'=>$no2pay4_total,
                            'placeholder'=>$_->entry_total,
                            'id'=>'input-total'
                        ));?>

                        <?= $buildForm->input(array(
                            'label_title'=>$_->entry_payment_name,
                            'type'=>'text',
                            'name'=>'no2pay4_payment_name',
                            'value'=>$no2pay4_payment_name,
                            'placeholder'=>$_->entry_payment_name,
                            'id'=>'input-payment-name'
                        ));?>

                        <?= $buildForm->select(array(
                            'label_title' => $_->entry_order_status,
                            'name'  =>  'no2pay4_order_status_id',
                            'options'  =>   $order_statuses,
                            'option_value'  => 'order_status_id',
                            'value' =>  $no2pay4_order_status_id,
                        ))?>

                        <?= $buildForm->select(array(
                            'label_title' => $_->entry_geo_zone,
                            'name'  =>  'no2pay4_geo_zone_id',
                            'options'  =>   array_merge(array(array('geo_zone_id'=>0, 'name'=>$_->text_all_zones)), $geo_zones),
                            'option_value'  => 'geo_zone_id',
                            'value' =>  $no2pay4_geo_zone_id,
                            'multiple'=>true,
                        ))?>

                        <?= $buildForm->input(array(
                            'label_title'=>$_->entry_sort_order,
                            'type'=>'text',
                            'name'=>'no2pay4_sort_order',
                            'value'=>$no2pay4_sort_order,
                            'placeholder'=>$_->entry_sort_order,
                            'id'=>'input-sort-order'
                        ));?>
						
						<!-- Enabled/disabled test server --->
						<?= $buildForm->select(array(
							'label_title' => $_->Sandbox,
							'label_help'=>$_->_('2Pay4 sandbox can be used to test payments.'),
							'name'  =>  'no2pay4_testmode',
							'options'  =>   $arrayYesNo,
							'value' =>  $no2pay4_testmode,
						))?>
						
						<?= $buildForm->select(array(
							'label_title' => $_->_('Debug Log'),
							'label_help'=>$_->_('Log 2Pay4 events.'),
							'name'  =>  'no2pay4_debug',
							'options'  =>   $arrayYesNo,
							'value' =>  $no2pay4_debug,
						))?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $footer; ?>
