<?php
 
function calculateSmartQuote(float $basePrice, string $district, string $date, ?string $slot = null): array
{
    $notes = [];
    $extraFee = 0;
    
    $district = trim($district);

    
    $zone_center = [
        'Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 
        'Phường 5', 'Phường 8', 'Phường 9'
    ];

    $zone_suburb = [
        'Phường Trường An', 'Phường Tân Ngãi', 'Phường Tân Hòa', 'Phường Tân Hội',
        'Xã Tân Hạnh', 'Xã Hòa Phú', 'Xã Phước Hậu', 'Xã Thanh Đức'
    ];

    if (in_array($district, $zone_center)) {
        $notes[] = "Khu vực trung tâm (Miễn phí đi lại)";
    } 
    elseif (in_array($district, $zone_suburb)) {
        $shipFee = 30000;
        $extraFee += $shipFee;
        $notes[] = "Phí di chuyển ngoại thành (+" . number_format($shipFee) . "đ)";
    } 
    else {
       
        $defaultFee = 50000;
        $extraFee += $defaultFee;
        $notes[] = "Khu vực xa (+" . number_format($defaultFee) . "đ)";
    }

   
    
    $isWeekend = (date('N', strtotime($date)) >= 6);
    
    $hour = 0;
    if ($slot) {
        $hour = (int)substr($slot, 0, 2);
    }
    $isRushHour = ($hour >= 17 && $hour <= 20);

    if ($isWeekend || $isRushHour) {
        $timeFee = 20000;
        $extraFee += $timeFee;
        
        $msg = [];
        if ($isWeekend) $msg[] = "Cuối tuần";
        if ($isRushHour) $msg[] = "Cao điểm";
        
        $notes[] = implode(' & ', $msg) . " (+" . number_format($timeFee) . "đ)";
    }

    $finalPrice = $basePrice + $extraFee;

    return [$finalPrice, $notes];
}



function getCoordinates(string $address): ?array
{
   
    $addressFull = $address . ', Vĩnh Long, Vietnam';
    $search_query = urlencode($addressFull);
    
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: TechFixVL/1.0 (admin@techfix.com)\r\n"
        ]
    ];
    
    $context = stream_context_create($opts);
    $url = "https://nominatim.openstreetmap.org/search?q={$search_query}&format=json&limit=1";
    
    try {
        $response = @file_get_contents($url, false, $context);
        
        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data) && isset($data[0])) {
                return [
                    'lat' => (float) $data[0]['lat'],
                    'lng' => (float) $data[0]['lon']
                ];
            }
        }
    } catch (Exception $e) {
        error_log("Geo Error: " . $e->getMessage());
    }
    
    return null;
}
?>