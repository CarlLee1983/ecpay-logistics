<?php

declare(strict_types=1);

namespace CarlLee\EcPayLogistics;

/**
 * 物流表單產生器。
 *
 * 負責產生導向綠界物流頁面（如門市電子地圖）的 HTML 表單。
 */
class FormBuilder
{
    /**
     * 綠界伺服器網址。
     */
    private string $serverUrl;

    /**
     * 建立表單產生器。
     *
     * @param string $serverUrl 綠界伺服器網址
     */
    public function __construct(string $serverUrl = 'https://logistics-stage.ecpay.com.tw')
    {
        $this->serverUrl = rtrim($serverUrl, '/');
    }

    /**
     * 產生 HTML 表單。
     *
     * @param Content $logistics 物流操作物件
     * @param string $formId 表單 ID
     * @param string $submitText 提交按鈕文字
     * @return string HTML 表單
     */
    public function build(Content $logistics, string $formId = 'ecpay-logistics-form', string $submitText = '前往選擇門市'): string
    {
        $actionUrl = $this->getActionUrl($logistics);
        $fields = $this->getFields($logistics);

        $html = sprintf(
            '<form id="%s" method="post" action="%s">',
            htmlspecialchars($formId, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($actionUrl, ENT_QUOTES, 'UTF-8')
        );
        $html .= "\n";

        foreach ($fields as $name => $value) {
            $html .= sprintf(
                '    <input type="hidden" name="%s" value="%s">' . "\n",
                htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')
            );
        }

        $html .= sprintf(
            '    <button type="submit">%s</button>' . "\n",
            htmlspecialchars($submitText, ENT_QUOTES, 'UTF-8')
        );
        $html .= '</form>';

        return $html;
    }

    /**
     * 產生自動提交的 HTML 表單。
     *
     * @param Content $logistics 物流操作物件
     * @param string $formId 表單 ID
     * @param string $loadingText 載入提示文字
     * @param string|null $nonce CSP nonce 值（用於 Content-Security-Policy）
     * @return string HTML 表單（含自動提交 JavaScript）
     */
    public function autoSubmit(
        Content $logistics,
        string $formId = 'ecpay-logistics-form',
        string $loadingText = '正在導向綠界物流頁面，請稍候...',
        ?string $nonce = null
    ): string {
        $actionUrl = $this->getActionUrl($logistics);
        $fields = $this->getFields($logistics);

        $nonceAttr = $nonce !== null
            ? sprintf(' nonce="%s"', htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8'))
            : '';

        $html = '<!DOCTYPE html>' . "\n";
        $html .= '<html>' . "\n";
        $html .= '<head>' . "\n";
        $html .= '    <meta charset="UTF-8">' . "\n";
        $html .= '    <meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
        $html .= '    <title>物流處理中</title>' . "\n";
        $html .= sprintf('    <style%s>', $nonceAttr) . "\n";
        $html .= '        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }' . "\n";
        $html .= '        .loading { text-align: center; margin-top: 100px; }' . "\n";
        $html .= '        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #5cb85c; ';
        $html .= 'border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; ';
        $html .= 'margin: 0 auto 20px; }' . "\n";
        $html .= '        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }' . "\n";
        $html .= '    </style>' . "\n";
        $html .= '</head>' . "\n";
        $html .= '<body>' . "\n";
        $html .= '    <div class="loading">' . "\n";
        $html .= '        <div class="spinner"></div>' . "\n";
        $html .= sprintf(
            '        <p>%s</p>' . "\n",
            htmlspecialchars($loadingText, ENT_QUOTES, 'UTF-8')
        );
        $html .= '    </div>' . "\n";

        $html .= sprintf(
            '    <form id="%s" method="post" action="%s" style="display:none;">',
            htmlspecialchars($formId, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($actionUrl, ENT_QUOTES, 'UTF-8')
        );
        $html .= "\n";

        foreach ($fields as $name => $value) {
            $html .= sprintf(
                '        <input type="hidden" name="%s" value="%s">' . "\n",
                htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')
            );
        }

        $html .= '    </form>' . "\n";
        $html .= sprintf('    <script%s>', $nonceAttr) . "\n";
        $html .= sprintf(
            '        document.getElementById("%s").submit();',
            htmlspecialchars($formId, ENT_QUOTES, 'UTF-8')
        );
        $html .= "\n";
        $html .= '    </script>' . "\n";
        $html .= '</body>' . "\n";
        $html .= '</html>';

        return $html;
    }

    /**
     * 僅取得表單欄位（含 CheckMacValue）。
     *
     * @param Content $logistics 物流操作物件
     * @return array<string, mixed> 表單欄位
     */
    public function getFields(Content $logistics): array
    {
        return $logistics->getContent();
    }

    /**
     * 取得表單 Action URL。
     *
     * @param Content $logistics 物流操作物件
     * @return string
     */
    public function getActionUrl(Content $logistics): string
    {
        return $this->serverUrl . $logistics->getRequestPath();
    }

    /**
     * 產生 JSON 格式的表單資料。
     *
     * @param Content $logistics 物流操作物件
     * @return string JSON 字串
     */
    public function toJson(Content $logistics): string
    {
        $json = json_encode([
            'action' => $this->getActionUrl($logistics),
            'fields' => $this->getFields($logistics),
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return $json !== false ? $json : '{}';
    }

    /**
     * 取得陣列格式的表單資料。
     *
     * @param Content $logistics 物流操作物件
     * @return array{action: string, fields: array<string, mixed>}
     */
    public function toArray(Content $logistics): array
    {
        return [
            'action' => $this->getActionUrl($logistics),
            'fields' => $this->getFields($logistics),
        ];
    }

    /**
     * 設定伺服器網址。
     *
     * @param string $url 伺服器網址
     * @return static
     */
    public function setServerUrl(string $url): static
    {
        $this->serverUrl = rtrim($url, '/');

        return $this;
    }

    /**
     * 取得伺服器網址。
     *
     * @return string
     */
    public function getServerUrl(): string
    {
        return $this->serverUrl;
    }
}
