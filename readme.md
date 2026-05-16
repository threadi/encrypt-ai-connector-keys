# Encrypt AI Connector Keys

With WordPress 7.0, the AI Client was integrated into the CMS. This allows users to store their API keys for accessing AIs in a central location within the backend. However, these keys are stored unencrypted in the database, which for some projects is a deal-breaker for using API keys in the AI Client.

This repository contains a small plugin that handles the encryption and decryption of these API keys. It uses the [Crypt for WordPress](https://github.com/threadi/crypt-for-wordpress), which has already proven its worth and supports various encryption methods. The key to decrypt the strings is not stored in the database but in separate files (wp-config.php, a generic MU plugin, or a custom file). This allows the API keys to be stored securely in the database by the AI Client.

## Features

- Encrypt any existing key on activation.
- Encrypts any new API AI key entered under Settings > Connectors.
- Decrypt encrypted API AI keys only when their use is requested.
- Decrypt any existing key on deactivation.
- No further settings necessary.

## Usage

1. Download the actual release ZIP (not the source ZIP) [from GitHub](https://github.com/threadi/encrypt-ai-connector-keys/releases/latest).
2. Install it in your WordPress and activate it.
3. Enjoy the peace of mind that comes with knowing your data is secure.

## Settings

There are no settings for this plugin. However, you can use this hook to control how encryption works:

`add_filter( 'encrypt_ai_connector_keys_crypt_config', function( $config ) {
 // change the configuration here.
 return $config;
});`

The possible configurations are described here: https://github.com/threadi/crypt-for-wordpress?tab=readme-ov-file#parameters

## Alternatives

The WordPress AI Client also includes an option to store API keys more securely. This can be done by using environment variables in the hosting environment. The key is stored in a hosting setting and then read by WordPress. The downside is that not all hosting providers support this, and it can be difficult for regular users to implement.