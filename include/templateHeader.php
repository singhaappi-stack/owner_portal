<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appi Portal</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="./js/browser@4.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://momentjs.com/downloads/moment.js"></script>
<style>
</style>
<script>
function popupBox(type, message, callback = null) {
    const popupBox = $('#popupBox');
    const popupTitle = $('#popupTitle');
    const popupMessage = $('#popupMessage');
    const popupCloseButton = $('#popupCloseButton');
    const popupConfirmButton = $('#popupConfirmButton');

    // Reset buttons visibility and event handlers
    popupCloseButton.off('click').hide();
    popupConfirmButton.off('click').show();

    popupConfirmButton.on('click', function() {
        popupBox.addClass('hidden');
    });
    
    if (type === 'success') {
        popupTitle.text('成功'); // Success in Japanese
        popupTitle.removeClass('text-red-600').addClass('text-green-600');
        popupConfirmButton.on('click', function() {
            popupBox.addClass('hidden');
            if (callback) {
                callback();
            } else {
                popupBox.addClass('hidden');
            }
        });
    } else if (type === 'error') {
        popupTitle.text('エラー'); // Error in Japanese
        popupTitle.removeClass('text-green-600').addClass('text-red-600');
    } else if (type === 'confirm') {
        popupTitle.text('確認'); // Confirmation in Japanese
        popupTitle.removeClass('text-green-600').addClass('text-red-600');
        popupCloseButton.show();
        popupConfirmButton.on('click', function() {
            popupBox.addClass('hidden');
            if (callback) {
                callback();
            } else {
                popupBox.addClass('hidden');
            }
        });
    } else {
        popupTitle.text('');
    }
    popupMessage.html(message);
    popupBox.removeClass('hidden');

    popupCloseButton.on('click', function() {
        popupBox.addClass('hidden');
    });
}

function formatMoney(amount, currency = '¥', decimals = 0) {
    const num = parseFloat(amount);
    if (!isFinite(num)) return null;

    return currency + num
        .toFixed(decimals)
        .replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}
</script>
</head>
<body>