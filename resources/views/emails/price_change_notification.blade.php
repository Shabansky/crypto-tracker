<!DOCTYPE html>
<html>

<head>
    <title>Price Change Notification</title>
</head>

<body>
    <h3>Abnormal Price Change Detected in the last {{ $timeframe }} hours</h3>
    <p>Specific details: </p>
    <p>Timeframe: {{ $timeframe }} hours</p>
    <p>Latest Price: {{ $latestPrice }}</p>
    <p>Initial Price: {{ $initialPrice }}</p>
    <p>Price Difference: {{ $priceDifference }}</p>
    <p>Percentage Difference: {{ $percentageDifference }}%</p>
</body>

</html>