<?php if($typedateinput == 1):?>
    <script type="text/javascript">
        function PaymentWindowReady() {
            paymentwindow = new PaymentWindow({
                <?= implode(',', $args_array);?>
            });
            paymentwindow.open();
        }
        function payClick(){
            paymentwindow.open();
            return false;
        };
    </script>
    <script type="text/javascript" src="https://compareking.dev/js/integration/paymentwindow.js" charset="UTF-8"></script>
<?php elseif($typedateinput == 3):?>
    <style>
       .no2pay4_paymentwindow_container label, .no2pay4_paymentwindow_container label{
            display: inline-block;
            margin-right: 10px;
            width: 86px;
        }

        .no2pay4_paymentwindow_container .col, .no2pay4_paymentwindow_container .col{
            display: inline-block;
        }

        .no2pay4_paymentwindow_container .col input,.no2pay4_paymentwindow_container .col input{
            padding-left: 5px;
            padding-right: 5px;
        }
    </style>
    <div>
        <h3><?=$payment_name;?></h3>
    </div>
    <div id="2pay4_payment_form" class="no2pay4_paymentwindow_container"></div>
    <script type="text/javascript">
        function PaymentFormReady() {
            paymentform = new PaymentForm({
                <?= implode(',', $args_array);?>
            });
            paymentform.buildForm("2pay4_payment_form");
        }
        function payClick(){ return false; };
    </script>
    <script type="text/javascript" src="https://compareking.dev/js/integration/paymentform.js" charset="UTF-8"></script>
<?php else:?>
    <script type="text/javascript">
        function payClick(){
            window.location.href = "<?=$action?>";
            return false;
        };
    </script>
<?php endif;?>

<div class="buttons">
  <div class="pull-right">
    <input type="button" onclick="javascript:payClick()" value="<?= $_->button_confirm;?>" id="submit_2pay4_payment_form" class="btn btn-primary" />
  </div>
</div>