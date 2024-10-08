# 🛒 Custom Price By User Role

Adds custom price fields based on user role in Woocommerce Stores and allows updating the prices via WooCommerce REST API.

## Prerequisites

WordPress 6.5 or greater and WooCommerce 8.0.0 or greater should be installed prior to the Custom Price By User Role plugin.

Additionally you will need to install an user role plugin manager if you want to add custom roles, otherwise you will have only default wordpress user role like: subscriber, administrator, store manager and so on. 

In order to add custom role, I recommend to add Members plugin, a light weight plugin to manage Wordpress/Woocommerce user roles.


## Documentation


### 1. Introduction
This document describes the process to update product prices in WooCommerce based on user role using REST API. Custom prices are assigned to specific user roles and can be updated via HTTP requests to the WooCommerce API.


### 2. Previous requirements
- Access to the Consumer Key and COnsumer Secret and ability to send HTTP requests.
- Basic knowledge of the JSON format and how to make REST requests.
- Install [Members](https://wordpress.org/plugins/members/) plugin to create the custom members roles.

### 3. Plugin options

This plugins has a few settings to give admin users controls over the way custom prices are showed. 

#### Show/Hide custom price

This options allows admins to decide if show the custom price. Could be used for testing or for a fast desable without losing the custom price already inserted in products. 

#### Show custom price with discount 

This checkbox not selected by default gives admin the posibility to show the custom price as it was a discounted price from the woocommerce regular price. In case you are using also a Woocommerce discount price, it would show just the custom role price. 

### 3. Read and Update process

#### a. Get the product ID 
Before reading or updating prices, it is necessary to obtain the ID of the product in WooCommerce to which you want to apply the update. This ID will be used in the REST request to identify the specific product.

#### b. Build reading and update request:
- The REST request must be a PUT or POST request sent to the following URL:

```
[WooCommerce_Store_URL]/wp-json/wc/v3/products/[product_ID]
```
- Make sure you include the appropriate authentication parameters in the request, such as credentials or API keys.


####  c. Data format:
The request must contain data in JSON format that includes the new custom pricing values for each user role .

Each custom price field must have a unique name that reflects the user role it is associated with. You should check the new user ```id``` and ```key``` in order to read and write the data correctly. For example:

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
- Send the HTTP request to the WooCommerce server with the updated data.
- Check the request response to make sure prices have been updated correctly.

### 4. Request example:
Here is an example of what a read request would look like using cURL in php. You will need to know the exact ```id``` and ```key``` 

```
// Consumer Key and Consumer Secret of WooCommerce
$consumer_key = 'CONSUMER_KEY';
$consumer_secret = 'COMSUMER_SECRET';

// API WooCommerce Store URL to get product data and custom prices
$url = '[WooCommerce_Store_URL]/wp-json/wc/v3/products/[product_ID]';

// Data to send in the update request
$data = array(
    'meta_data' => array(
		array(
            'id' => 190, // Student Custom Price ID
            'key' => 'custom_price_estudiante',
            'value' => '6.00' // Add new price
        ),
        array(
            'id' => 152, // Profesional Custom Price ID
            'key' => 'custom_price_profesional',
            'value' => '5.00' // Add new price
        )
        
    )
);

// Convert the array to JSON
$data_json = json_encode($data);

// Init cURL
$ch = curl_init();

// Configure the URL and other necessary options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); // Use the PUT method to update data
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_json))
);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
curl_setopt($ch, CURLOPT_VERBOSE, true);

// Set up basic authentication
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $consumer_key . ':' . $consumer_secret);

// Make the request to the WooCommerce API
$response = curl_exec($ch);

// Check for errors
if(curl_errno($ch)){
    echo 'Error: ' . curl_error($ch);
    echo 'Error: ' . curl_errno($ch);
}

// Print the answer
echo $response;

// Close cURL
curl_close($ch);
```

### 5. Support
If you encounter any issues or need additional help, please contact the support team.

I hope this documentation will be useful to guide the programmer in updating prices based on user role through WooCommerce API. If you need more help, don't hesitate to ask.

## Contributing
There are many ways to contribute – reporting bugs, adding translations, feature suggestions and fixing bugs. For full details, please see [CONTRIBUTING.md](https://github.com/cquesadad/custom-price-user-role/blob/main/CONTRIBUTING.md)
