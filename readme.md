Puntopagos magento plugin
=====================


## Description

Full integration with puntopagos payment gateway.

Puntopagos its a gateway available for payment processing in Chile.

This plugin is here because I had to do an integration between Magento and Puntopagos and I wanted to make something cool and make it available.


## Installation

### Composer

1. Add this repository url as a vcs repository to your composer.json
2. Install using: composer require androb/puntopagos

```
composer require androb/puntopagos
```

### Firegento

Soon :)

### MagentoConnect

Soon :)

### Manual

1. Copy the contents of the src folder into your root magento folder
2. Refresh magento cache

## Configuration

You must have a set of valid credentials from puntopagos, a valid secretKey and apiKey

Once you have then, go to

System > Configuration > Sales > Payment Methods

You will see 4 new tabs

### Tab 1 : PuntoPagos - Gateway Global Configuration

In this tab you will configure the global params of the plugin

**Enabled**

Whether or not the plugin is active

**Key Id**

Your puntopagos key id
  
**Key Secret**

Your puntopagos secret key

**Sanbox Mode**

Enabled by default for development mode, you will want to change when you release the store

**Sandbox Url**

The url for sandbox mode (You dont need to change it)

**Producto Url**

The url for production mode (You dont need to change it)

**Payment icons base url**

By default icons for payment options (i.e webpay, presto) will be retrieved using puntopagos images. (More on this below)

**Generate invoice**

Sucessful orders are set to processing state, active this if you want to get the invoice for such orders auto generated.

**Debug Mode**

Enable a very detailed log file in /var/log/puntopagos/debug.log

**Order Log**

Enable a less detailed log for each puntopagos order.

The logs are very useful to test integration and see what is happening

### The other tabs

This plugin groups the payment options in three categories, so in the other tabs you can set the configuration for each payment group.

These categories are, Bank transfer for bank options, Commercial Cards like presto or ripley and Webpay for webpay payment options.

### Tab 2 : PuntoPagos - Bank transfer

**Enabled**

If the group bank transfer if available during checkout

**Title**

A custom title for the group in the checkout process

**Payment Options**

Here you can select which banks are available or not (In sandbox mode you wont see any bank)

**Sort Order**

Allow establish and order between the other groups and payment options

### Tab 3 : PuntoPagos - Commercial Cards

The options ir are the same as the bank transfer tab.

**Payment Options**

Here you can select which cards are available or not (In sandbox mode you will ripley only even if you enable all)

### Tab 4 : PuntoPagos - Webpay

The options ir are the same as the bank transfer tab.

**Payment Options**

Here you can select webpay payment options (Webpay plus available in sandbox)

### Payment urls or endpoints

By default puntopagos will ask you for 3 urls, these urls will be the way for the gateway to tell the store that something happened with the order in the gateway.

This plugin implements these order processing endpoints as well

Here is the url format for each one and his actions:

#### Failure url

The user is sent to this url when the transaction fails in the gateway
The failure method will try to cancel de order (it might be canceled before by the notification endpoint)

```
http://yourmagentobaseurl/puntopagos/index/failure/t/
```

####  Success url

The user is sent to this url when the transaction is successful
A success page is shown to the user.

```
http://yourmagentobaseurl/puntopagos/index/success/t/
```

####  Notification url

The gateway sent notifications to the our store very time and order is processed.
This url will process these messages and will update the order accordingly.

```
http://yourmagentobaseurl/puntopagos/index/notification/t/
```

Both url are the same, but the first one is using parameter rewrites, is up to you.

*NOTE:* If you dont have rewrites enabled (if you see each url with index.php) you will have to append index.php between your base url and puntopagos

```
i.e: http://yourmagentobaseurl/index.php/puntopagos/index/notification?t=
```

By the way, enabling url rewrites and getting rid of index.php its a very good practice.

## Payment icons base url (Optional)

In the global gateway configuration you will see this option. This is the base url for each payment option icon, by default it points to puntopagos server

i.e: For the Santander bank logo the image url is: 

http://www.puntopagos.com/content/mp1.gif (Santander logo, Santander option is number 1)


You only want to change this if you want to use your own icon versions.

You can inspect in the browser to find the name of each image so you can put in your CND and set your custom base url.

See above supported payment options by the plugin so you can know the code for each one.

## Feedback

Feel free to report any issue you find or any feature you like or dislike.