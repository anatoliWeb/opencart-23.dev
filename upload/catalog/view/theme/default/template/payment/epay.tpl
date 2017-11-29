<div class="content" style="text-align: center;">
		<span style="width:100%; float: left;" id="epay_card_logos">Cards</span>
		<span style="height:49px;width:100%;float:left;" id="epay_logos">
			Logo
		</span>
</div>
<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
  </div>
</div>

<script type="text/javascript" src="https://ssl.ditonlinebetalingssystem.dk/integration/ewindow/paymentwindow.js" charset="UTF-8">
</script>
 
<script type="text/javascript">
	function newWindow() {
      paymentwindow = new PaymentWindow({
      <?php
      foreach($params as $key => $value)
      {
        if($key != "hash")
        {
          echo "'" . $key . "': \"". $value . "\",";
        }
      }
      ?>
			'hash': "<?php echo $params["hash"]; ?>"
      });
	}
</script>
 
<script type="text/javascript"><!--

var openwindowtries = 0;

$('#button-confirm').bind('click', function() {
	newWindow(); paymentwindow.open(); openwindowtries = 5;
});

function openwindow()
{
	if(openwindowtries < 5)
	{
		if(PaymentWindow != undefined)
		{
			newWindow(); paymentwindow.open(); openwindowtries = 5;
		}
		
		openwindowtries++;
	}
}

$(document).ready(function() {
	setTimeout("openwindow()", 500)	
});

//--></script> 

<script type="text/javascript" src="https://relay.ditonlinebetalingssystem.dk/integration/paymentlogos/PaymentLogos.aspx?merchantnumber=<?php echo $params["merchantnumber"] ?>&direction=2&padding=2&rows=1&logo=0&showdivs=0&divid=epay_card_logos"></script>
<script type="text/javascript" src="https://relay.ditonlinebetalingssystem.dk/integration/paymentlogos/PaymentLogos.aspx?merchantnumber=<?php echo $params["merchantnumber"] ?>&direction=2&padding=0&rows=1&logo=1&showdivs=0&showcards=0&enablelink=0&divid=epay_logos"></script>
