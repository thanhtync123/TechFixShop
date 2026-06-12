<?php
/**
 * ShopeeAPI — Wrapper cho Shopee Open API v2
 *
 * Tài liệu: https://open.shopee.com/documents
 *
 * Các tính năng:
 *  - Tạo sản phẩm mới (add_item)
 *  - Cập nhật thông tin sản phẩm (update_item)
 *  - Cập nhật giá (update_price)
 *  - Cập nhật tồn kho (update_stock)
 *  - Lấy danh sách sản phẩm đã đồng bộ
 *  - Tạo URL OAuth để lấy Access Token
 */
class ShopeeAPI
{
    private int    $partnerId;
    private string $partnerKey;
    private int    $shopId;
    private string $accessToken;
    private string $baseUrl;

    public function __construct(array $cfg)
    {
        $this->partnerId   = (int)$cfg['partner_id'];
        $this->partnerKey  = $cfg['partner_key'];
        $this->shopId      = (int)$cfg['shop_id'];
        $this->accessToken = $cfg['access_token'];
        $env               = $cfg['env'] ?? 'sandbox';
        $this->baseUrl     = $cfg['base_url'][$env];
    }

    // ─────────────────────────────────────────────────────────────────
    // AUTH
    // ─────────────────────────────────────────────────────────────────

    /**
     * Tạo URL redirect để lấy code OAuth
     * Sau khi redirect về redirect_url, lấy ?code=... và ?shop_id=...
     * rồi gọi getAccessToken($code, $shopId)
     */
    public function getAuthUrl(string $redirectUrl): string
    {
        $ts        = time();
        $path      = '/api/v2/shop/auth_partner';
        $baseStr   = $this->partnerId . $path . $ts;
        $sign      = hash_hmac('sha256', $baseStr, $this->partnerKey);

        return $this->baseUrl . $path
            . '?partner_id='   . $this->partnerId
            . '&timestamp='    . $ts
            . '&sign='         . $sign
            . '&redirect='     . urlencode($redirectUrl);
    }

    /**
     * Đổi code lấy access_token + refresh_token
     */
    public function getAccessToken(string $code, int $shopId): array
    {
        $path = '/api/v2/auth/token/get';
        $body = [
            'code'       => $code,
            'shop_id'    => $shopId,
            'partner_id' => $this->partnerId,
        ];
        return $this->post($path, $body, false);
    }

    /**
     * Làm mới access_token bằng refresh_token
     */
    public function refreshToken(string $refreshToken, int $shopId): array
    {
        $path = '/api/v2/auth/access_token/get';
        $body = [
            'refresh_token' => $refreshToken,
            'shop_id'       => $shopId,
            'partner_id'    => $this->partnerId,
        ];
        return $this->post($path, $body, false);
    }

    // ─────────────────────────────────────────────────────────────────
    // PRODUCTS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Thêm sản phẩm mới lên Shopee
     *
     * @param array $item  Dữ liệu chuẩn hoá từ services hoặc equipments
     * @return array       Response từ Shopee (có item_id nếu thành công)
     */
    public function addItem(array $item): array
    {
        $path = '/api/v2/product/add_item';
        return $this->post($path, $this->buildItemPayload($item));
    }

    /**
     * Cập nhật thông tin sản phẩm đã có trên Shopee
     */
    public function updateItem(int $shopeeItemId, array $item): array
    {
        $path    = '/api/v2/product/update_item';
        $payload = $this->buildItemPayload($item);
        $payload['item_id'] = $shopeeItemId;
        return $this->post($path, $payload);
    }

    /**
     * Cập nhật giá — dùng model_id = 0 nếu không có variation
     */
    public function updatePrice(int $shopeeItemId, float $price): array
    {
        $path = '/api/v2/product/update_price';
        return $this->post($path, [
            'item_id'    => $shopeeItemId,
            'price_list' => [[
                'model_id'      => 0,
                'original_price'=> $price,
            ]],
        ]);
    }

    /**
     * Cập nhật tồn kho
     */
    public function updateStock(int $shopeeItemId, int $stock): array
    {
        $path = '/api/v2/product/update_stock';
        return $this->post($path, [
            'item_id'     => $shopeeItemId,
            'stock_list'  => [[
                'model_id'  => 0,
                'normal_stock' => $stock,
            ]],
        ]);
    }

    /**
     * Lấy danh sách item của shop (phân trang)
     */
    public function getItemList(int $offset = 0, int $pageSize = 50, string $itemStatus = 'ALL'): array
    {
        $path = '/api/v2/product/get_item_list';
        $query = [
            'offset'      => $offset,
            'page_size'   => $pageSize,
            'item_status' => $itemStatus,
        ];
        return $this->get($path, $query);
    }

    /**
     * Lấy chi tiết item
     */
    public function getItemBaseInfo(array $itemIds): array
    {
        $path = '/api/v2/product/get_item_base_info';
        return $this->get($path, [
            'item_id_list' => implode(',', $itemIds),
        ]);
    }

    /**
     * Xoá (ẩn) item
     */
    public function deleteItem(int $shopeeItemId): array
    {
        $path = '/api/v2/product/delete_item';
        return $this->post($path, ['item_id' => $shopeeItemId]);
    }

    /**
     * Lấy danh sách danh mục Shopee
     */
    public function getCategoryTree(): array
    {
        $path = '/api/v2/product/get_category';
        return $this->get($path, ['language' => 'vi']);
    }

    // ─────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Build payload chuẩn cho add_item / update_item
     *
     * $item phải có:
     *   name, description, price, stock, category_id,
     *   image_urls (array), condition ('NEW'|'USED'), weight (kg)
     */
    private function buildItemPayload(array $item): array
    {
        // Shopee yêu cầu image_id_list, phải upload ảnh trước
        // Nếu đã có shopee_image_id thì dùng; nếu không, dùng url trực tiếp qua upload
        $imageList = [];
        foreach (($item['image_ids'] ?? []) as $imgId) {
            $imageList[] = ['image_id' => $imgId];
        }

        return [
            'original_price'    => (float)($item['price'] ?? 0),
            'description'       => $item['description'] ?? '',
            'weight'            => (float)($item['weight'] ?? 0.5),
            'item_name'         => mb_substr($item['name'] ?? '', 0, 120),
            'item_status'       => $item['status'] ?? 'UNLIST',
            'normal_stock'      => (int)($item['stock'] ?? 999),
            'image'             => ['image_id_list' => $imageList],
            'category_id'       => (int)($item['category_id'] ?? 0),
            'condition'         => $item['condition'] ?? 'NEW',
            'item_dangerous'    => 0,
            'logistic_info'     => $item['logistic_info'] ?? [],
            'pre_order'         => ['is_pre_order' => false],
        ];
    }

    /**
     * Upload ảnh lên Shopee media space
     * Trả về image_id để dùng trong add_item
     */
    public function uploadImage(string $imageUrl): array
    {
        $path = '/api/v2/media_space/upload_image_by_url';
        return $this->post($path, ['image_url' => $imageUrl]);
    }

    // ─────────────────────────────────────────────────────────────────
    // HTTP CORE
    // ─────────────────────────────────────────────────────────────────

    /**
     * Ký request theo Shopee Open API v2
     * Sign = HMAC-SHA256( partner_id + path + timestamp + access_token + shop_id, partner_key )
     */
    private function buildSign(string $path): array
    {
        $ts      = time();
        $baseStr = $this->partnerId . $path . $ts . $this->accessToken . $this->shopId;
        $sign    = hash_hmac('sha256', $baseStr, $this->partnerKey);
        return ['ts' => $ts, 'sign' => $sign];
    }

    /** POST request (JSON body) */
    private function post(string $path, array $body, bool $withAuth = true): array
    {
        ['ts' => $ts, 'sign' => $sign] = $this->buildSign($path);

        $queryParams = [
            'partner_id' => $this->partnerId,
            'timestamp'  => $ts,
            'sign'       => $sign,
        ];
        if ($withAuth) {
            $queryParams['access_token'] = $this->accessToken;
            $queryParams['shop_id']      = $this->shopId;
        }

        $url = $this->baseUrl . $path . '?' . http_build_query($queryParams);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false, // dev only; bật lại khi production
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return ['error' => 'cURL error: ' . $curlErr, 'http_code' => $httpCode];
        }

        $decoded = json_decode($response, true) ?? [];
        $decoded['_http_code'] = $httpCode;
        return $decoded;
    }

    /** GET request */
    private function get(string $path, array $params = []): array
    {
        ['ts' => $ts, 'sign' => $sign] = $this->buildSign($path);

        $queryParams = array_merge($params, [
            'partner_id'   => $this->partnerId,
            'timestamp'    => $ts,
            'sign'         => $sign,
            'access_token' => $this->accessToken,
            'shop_id'      => $this->shopId,
        ]);

        $url = $this->baseUrl . $path . '?' . http_build_query($queryParams);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return ['error' => 'cURL error: ' . $curlErr, 'http_code' => $httpCode];
        }

        $decoded = json_decode($response, true) ?? [];
        $decoded['_http_code'] = $httpCode;
        return $decoded;
    }
}
