<?php 
    echo $header;
    echo $column_left;
    echo $column_right; ?>
    <div id="content">
<center>
        <?php 

            
            echo $content_top;
?>
               <p>Order Id: <?php echo $order_id;?></p>
            <p>Transaction Id: <?php echo $transaction_id;?></p>
			<p> Description: <?php echo $description;?></p>
			    <p>Amount Paid: <?php echo $amount_paid;?></p>
				  <p>Bank: <?php echo $bank;?></p>       


  <?php
            echo $content_bottom;
        ?>
</center>
    </div>
<?php echo $footer; ?>