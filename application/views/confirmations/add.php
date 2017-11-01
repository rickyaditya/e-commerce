<script type="text/javascript">
  $(function() {
    $("#datepicker").datepicker();
  })
</script>
<div class="span12">
     <?php echo set_breadcrumb('<span class="divider">/</span>') ?>
    <div class="page-header">
        <h1>Konfirmasi Pembayaran</h1>
    </div>
    <form class="form-horizontal" method="post" action="<?php echo site_url('confirmations/add'); ?>">
        <fieldset>

            <?php echo form_hidden('code', $order['code']); ?>

            <div class="control-group">
                <label for="focusedInput" class="control-label">Nama Bank:</label>
                <div class="controls">
                    <input type="text" name="sender_bank" value="<?php echo set_value('sender_bank'); ?>" placeholder="Nama Bank" id="focusedInput" class="input-xlarge focused span6">
                </div>
            </div>
            <div class="control-group">
                <label for="focusedInput" class="control-label">Nama Rekening Bank:</label>
                <div class="controls">
                    <input type="text"  name="bank_account_name" value="<?php echo set_value('bank_account_name'); ?>" placeholder="Nama Rekening Bank" id="focusedInput" class="input-xlarge focused span6" />
                </div>
            </div>

            <div class="control-group">
                <label for="focusedInput" class="control-label">Bank Tujuan:</label>
                <div class="controls">
                    <?php echo form_dropdown('receiver_bank', $banks) ?>
                </div>
            </div>
            <div class="control-group">
                <label for="focusedInput" class="control-label">Jumlah:</label>
                <div class="controls">
                    <input type="text"  name="total" value="<?php echo set_value('total', isset($order['total']) ? $order['total']:'');?>" placeholder="" id="focusedInput" class="input-xlarge focused span6" />
                </div>
            </div>

            <div class="control-group">
                <label for="focusedInput" class="control-label">Tanggal Pembayaran:</label>
                <div class="controls">
                    <input type="text"  name="payment_date" value="<?php echo set_value('payment_date'); ?>" placeholder="" id="datepicker" class="form-control datepicker" data-date="2012-02-15" data-date-format="yyyy-mm-dd" />
                </div>
            </div>
            <div class="span8">
                <button class="btn btn-primary pull-right"  type="submit">Kirim</button>
            </div>
        </fieldset>
    </form>
</div>
