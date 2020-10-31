# Maybe Route Request Through Moesif

This plugin aims to provide a way to route HTTP requests made by the `WP_Http` class through a [moesif](https://www.moesif.com/) API monitorying service for logging and monitoring.

# Installation and Use

1. Clone this repo.
2. Copy secrets.sample.php to secrets.php
3. Edit secrets.php to use your moesif application id

# Determining your moesif application id

1. Navigate to the 'Apps and Team' screen of your moesif account.
![see screen shot](https://d.pr/i/PXjyK2+)
2. Either create a new app, or select an existing one via 'Set Up App' button.
3. The installation instructions currently default to Node.js which will provide you with the application id:
![see screen shot](https://d.pr/i/pXjH0F+)

# Editing for URLs not already included

This plugin was originally built with a bit of a limited scope with the intention of expanding on that scope over time. By default, when first published, it only filtered on requests to PayPal standard (live and sandbox), and Stripe.

## Adding a Url Replacement

1. Work out what a replacment URL might look like. The plugin uses the 'Codeless Proxy' method for moesif. Clicking on that will provide an interface to convert a URL which includes your application id. The application id part will be replaced with the `$moesif_id` that is configured. Generally speaking, you're only going to want to replace the host part of the URL with a new host followed by the application id. Any path parts of the URL will want to be preserved and appended after the application id. e.g. with an application id of `foo-bar-123` and a URL going to the Stripe API that looks like `https://api.stripe.com/v1/customers/cus_IIs1jHbxJNjoO9/sources` will want to be converted to `https://https-api-stripe-com-3.moesif.net/foo-bar-123/v1/customers/cus_IIs1jHbxJNjoO9/sources`
