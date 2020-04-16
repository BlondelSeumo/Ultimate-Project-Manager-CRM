<html>
<head>
    <title> Non-Seamless-kit</title>
</head>
<body>

<?php
require_once APPPATH . '/libraries/Ccavenue.php';
error_reporting(0);

$merchant_data = '';
$working_key = config_item('ccavenue_key');//Shared by CCAVENUES
$access_code = config_item('ccavenue_access_code');//Shared by CCAVENUES

foreach ($_POST as $key => $value) {
    $merchant_data .= $key . '=' . $value . '&';
}
$encrypted_data = encrypt_cc($merchant_data, $working_key); // Method for encrypting the data.
if (config_item('ccavenue_enable_test_mode') == 'TRUE') {
    $ulr = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
} else {
    $ulr = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
}
?>
<form method="post" name="redirect"
      action="<?= $ulr ?>">
    <?php
    echo "<input type=hidden name=encRequest value=$encrypted_data>";
    echo "<input type=hidden name=access_code value=$access_code>";
    ?>
</form>

<script language='javascript'>document.redirect.submit();</script>
</body>
</html>