<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="shopify-api-key" content="{{ \Osiset\ShopifyApp\Util::getShopifyConfig('api_key', $shopDomain ?? Auth::user()->name ) }}" />
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>

    @viteReactRefresh
    @vite('resources/js/index.jsx')
</head>
<body>
</body>
</html>
