<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>CMI Payement Request</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body>
<form name="payForm" id="payForm" method="post" action="{{ $cmiClient->getBaseUri() }}">
    @foreach($payData as $key => $value)
        <input type="text" name="{{ $key }}" value="{{ $value }}">
    @endforeach
        <input type="text" name="HASH" value="{{ $hash }}">
</form>

<script type="text/javascript">
    // document.getElementById("payForm").submit();
</script>
</body>
</html>
