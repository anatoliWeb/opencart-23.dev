<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-epay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i>
        </button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i>
        </a>
        <a href="<?php echo $search; ?>" data-toggle="tooltip" title="<?php echo $button_search; ?>" class="btn btn-info"><i class="fa fa-search"></i>
        </a>
      </div>
      <h1>
        <?php echo $heading_title; ?>
      </h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li>
          <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger">
      <i class="fa fa-exclamation-circle"></i>
      <?php echo $error['error_warning']; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          <i class="fa fa-pencil"></i>
          <?php echo $text_edit; ?>
        </h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-epay" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active">
              <a href="#tab-general" data-toggle="tab">
                <?php echo $tab_general; ?>
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-total">
                  <span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?>
                  </span>
                </label>
                <div class="col-sm-10">
                  <input type="text" name="epay_total" value=""<?php echo $epay_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  <?php echo $entry_status; ?>
                </label>
                <div class="col-sm-10">
                  <select name="epay_status" class="form-control">
                    <?php if ($epay_status) { ?>
                    <option value="1" selected="selected">
                      <?php echo $text_enabled; ?>
                    </option>
                    <option value="0">
                      <?php echo $text_disabled; ?>
                    </option>
                    <?php } else { ?>
                    <option value="1">
                      <?php echo $text_enabled; ?>
                    </option>
                    <option value="0" selected="selected">
                      <?php echo $text_disabled; ?>
                    </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  <?php echo $entry_payment_name; ?>
                </label>
                <div class="col-sm-10">
                  <input type="text" name="epay_payment_name" value="<?php echo $epay_payment_name; ?>" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  <?php echo $text_merchantnumber; ?>
                </label>
                <div class="col-sm-10">
                  <input type="text" name="epay_merchant_number" value="<?php echo $epay_merchant_number; ?>" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  <?php echo $entry_order_status; ?>
                </label>
                <div class="col-sm-10">
                  <select name="epay_order_status_id" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                      <?php if ($order_status['order_status_id'] == $epay_order_status_id) { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php } else { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  <?php echo $text_paymentwindow; ?>
                </label>
                <div class="col-sm-10">
                  <select name="epay_payment_window" class="form-control">
                    <option value="1" <?php echo $epay_payment_window==1 ? 'selected' : '' ?>><?php echo $text_paymentwindow_overlay ?>
                    </option>
                    <option value="3" <?php echo $epay_payment_window==3 ? 'selected' : '' ?>><?php echo $text_paymentwindow_fullscreen ?>
                    </option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  <?php echo $text_ownreceipt; ?>
                </label>
                <div class="col-sm-10">
                  <select name="epay_ownreceipt" class="form-control">
                    <option value="0" <?php echo $epay_ownreceipt==0 ? 'selected' : '' ?>><?php echo $text_no ?></option>
                    <option value="1" <?php echo $epay_ownreceipt==1 ? 'selected' : '' ?>><?php echo $text_yes ?></option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  MD5 Key
                </label>
                <div class="col-sm-10">
                  <input type="text" name="epay_md5key" value="<?php echo $epay_md5key; ?>" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  <?php echo $text_group ?>
                </label>
                <div class="col-sm-10">
                  <input type="text" name="epay_group" value="<?php echo $epay_group; ?>" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  Auth E-mail
                </label>
                <div class="col-sm-10">
                  <input type="text" name="epay_authemail" value="<?php echo $epay_authemail; ?>" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  Instant capture
                </label>
                <div class="col-sm-10">
                  <select name="epay_instantcapture" class="form-control">
                    <option value="0" <?php echo $epay_instantcapture==0 ? 'selected' : '' ?>><?php echo $text_no ?>
                    </option>
                    <option value="1" <?php echo $epay_instantcapture==1 ? 'selected' : '' ?>><?php echo $text_yes ?>
                    </option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  <?php echo $entry_geo_zone; ?>
                </label>
                <div class="col-sm-10">
                  <select name="epay_geo_zone_id" class="form-control">
                    <option value="0">
                      <?php echo $text_all_zones; ?>
                    </option>
                    <?php foreach ($geo_zones as $geo_zone) { ?>
                      <?php if ($geo_zone['geo_zone_id'] == $epay_geo_zone_id) { ?>
                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                      <?php } else { ?>
                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">
                  <?php echo $entry_sort_order; ?>
                </label>
                <div class="col-sm-10">
                  <input type="text" name="epay_sort_order" value="<?php echo $epay_sort_order; ?>" class="form-control" />
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
