# 🛒 Custom Price By User Role

Adds custom price fields based on user role in Woocommerce Stores and allows updating the prices via WooCommerce REST API.

## Prerequisites

WordPress 5.6 or greater and WooCommerce 5.7.0 or greater should be installed prior to the Custom Price By User Role plugin.

Additionally you will need to install an user role plugin manager if you want to add custom roles, otherwise you will have only default wordpress user role like: subscriber, administrator, store manager and so on. 

In order to add custom role, I recommend to add Members plugin, a light weight plugin to manage Wordpress/Woocommerce user roles.


## Documentation


### 1. Introduction
This document describes the process to update product prices in WooCommerce based on user role using REST API. Custom prices are assigned to specific user roles and can be updated via HTTP requests to the WooCommerce API.


### 2. Previous requirements
- Access to the Consumer Key and COnsumer Secret and ability to send HTTP requests.
- Basic knowledge of the JSON format and how to make REST requests.


### 3. Read and Update process

#### a. Get the product ID 
Before reading or updating prices, it is necessary to obtain the ID of the product in WooCommerce to which you want to apply the update. This ID will be used in the REST request to identify the specific product.

#### b. Build reading and update request:
- The REST request must be a PUT or POST request sent to the following URL:

```
[WooCommerce_Store_URL]/wp-json/wc/v3/products/[product_ID]
```
- Make sure you include the appropriate authentication parameters in the request, such as OAuth credentials or API keys.


####  c. Formato de datos:
The request must contain data in JSON format that includes the new custom pricing values for each user role.

Each custom price field must have a unique name that reflects the user role it is associated with. For example:

```
{
    "meta_data": [
        {
            "id": 152,
            "key": "custom_price_profesional",
            "value": "12.00"
        },
        {
            "id": 190,
            "key": "custom_price_estudiante",
            "value": "11.00"
        }
    ]
}

```

#### d. Submit the request:
- Envía la solicitud HTTP al servidor de WooCommerce con los datos actualizados.
- Verifica la respuesta de la solicitud para asegurarte de que los precios se hayan actualizado correctamente.

### 4. Request example:
Here is an example of what a read request would look like using cURL in php:

```
// Consumer Key and Consumer Secret of WooCommerce
$consumer_key = 'CONSUMER_KEY';
$consumer_secret = 'COMSUMER_SECRET';

// API WooCommerce Store URL to get product data and custom prices
$url = '[WooCommerce_Store_URL]/wp-json/wc/v3/products/[product_ID]';

// init cURL
$ch = curl_init();

// Config URL and other settings
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Config Basic Auth
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $consumer_key . ':' . $consumer_secret);

// Make request to WooCommerce API
$response = curl_exec($ch);

// Check errors
if(curl_errno($ch)){
    echo 'Error: ' . curl_error($ch);
}

// Decode JSON reponse
$data = json_decode($response, true);

// Get custom prices 
$price_student = $data['meta_data'][9]['value']; // Adjust depending on JSON structure
$price_professional = $data['meta_data'][7]['value']; // Adjust depending on JSON structure

// Imprimir los precios obtenidos
echo "Student price: " . $price_student . "<br>";
echo "Professional price: " . $price_professional . "<br>";

// Close cURL
curl_close($ch);
```

### 5. Support
If you encounter any issues or need additional help, please contact the support team.

I hope this documentation will be useful to guide the programmer in updating prices based on user role through WooCommerce API. If you need more help, don't hesitate to ask.

## Contributing
There are many ways to contribute – reporting bugs, adding translations, feature suggestions and fixing bugs. For full details, please see CONTRIBUTING.md
