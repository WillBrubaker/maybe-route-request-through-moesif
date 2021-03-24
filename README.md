# Maybe Route Request Through Moesif

This plugin aims to provide a way to route HTTP requests made by the `WP_Http` class through a [moesif](https://www.moesif.com/) API monitoring service for logging and monitoring.

BTC Donations to: bc1qt4cmxxkpcv7dd0u855nzrfwazpfja50apv5kwp

![bc1qt4cmxxkpcv7dd0u855nzrfwazpfja50apv5kwp](https://c8tastrophe.com/frame.png)

# Installation and Use

1. Clone this repo to your WordPress plugins directory or download the zip and install.

# Get Your Moesif Collector Application Id

1. Navigate to the 'API Keys' screen of your moesif account.
![see screen shot](https://d.pr/i/sZZjcw+)
2. Copy The Collector Application Id
![see screen shot](https://d.pr/i/RqFJq8+)

# Configure The Plugin
1. Tools -> HTTP Proxy Configuration
2. Enter the Collector Application Id and save
3. That's it. You're rerouting requests.

# Editing for URLs not already included

This plugin was originally built with a bit of a limited scope with the intention of expanding on that scope over time. By default, when first published, it only filtered on requests to PayPal standard (live and sandbox), and Stripe.

## Adding a Url Replacement

1. Work out what a replacment URL might look like. The plugin uses the 'Codeless Proxy' method for moesif. Clicking on that will provide an interface to convert a URL which includes your application id. The application id part will be replaced with the `$moesif_id` that is configured. Generally speaking, you're only going to want to replace the host part of the URL with a new host followed by the application id. Any path parts of the URL will want to be preserved and appended after the application id. e.g. with an application id of `foo-bar-123` and a URL going to the Stripe API that looks like `https://api.stripe.com/v1/customers/cus_IIs1jHbxJNjoO9/sources` will want to be converted to `https://https-api-stripe-com-3.moesif.net/foo-bar-123/v1/customers/cus_IIs1jHbxJNjoO9/sources`
2. Work out a pattern match - again, generally this will be the scheme+domain that will want to be matched and later replaced. The path part of the URL generally is not going to want to be replaced. Regex patterns must escape the `/` character since that has a special meaning. Patterns must also be delimited. The `/` character will be used for that as well (but un-escaped, since we want it to mean something special). For our Stripe example, the match that we're looking for is `'/https:\/\/api.stripe.com/'` and that part of the URL wants to be replaced with `https://https-api-stripe-com-3.moesif.net/foo-bar-123/`. Add the pattern match to the `$patterns` array.
3. Work out the replacement, e.g. `https://https-api-stripe-com-3.moesif.net/foo-bar-123/` and add it to the replacements array.


**Note on array keys**
The array keys in use currently are arbitrary and only used for human readability. The `pre_replace` function that is used to match/replace does not concern itself with array keys, only values. Still, any keys added should probably make sense to the next human looking at the code.
