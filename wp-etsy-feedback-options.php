<div class="wrap">
    <h2>Etsy Feedback Widget Options</h2>
    <?php if($updated_message) { ?>
    <p>
        <?php echo $updated_message ?>
    </p>
    <?php } ?>
    <form name="form1" method="post" action="">
        <input type="hidden" name="hidden_field" value="Y">

        <p>Etsy Id:
            <input type="text" name="etsy_id" value="<?php echo $etsy_id; ?>" size="20">
        </p>
        <p>Number of feedbacks to show:
            <input type="text" name="etsy_count" value="<?php echo $etsy_count; ?>" size="20">
        </p><hr />
        

        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="Save Changes" />
        </p>

    </form>

</div>
