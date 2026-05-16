=== Encrypt AI Connector Keys ===
Contributors: threadi
Requires at least: 7.0
Tested up to: 7.0
Requires PHP: 8.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: @@VersionNumber@@

Do not store your API connector keys in plain text in your database. Encrypt them.

== Description ==

With WordPress 7.0, the AI Client was integrated into the CMS. This allows users to store their API keys for accessing AIs in a central location within the backend. However, these keys are stored unencrypted in the database, which for some projects is a deal-breaker for using API keys in the AI Client.

This repository contains a small plugin that handles the encryption and decryption of these API keys. It uses the [Crypt for WordPress](https://github.com/threadi/crypt-for-wordpress), which has already proven its worth and supports various encryption methods. The key to decrypt the strings is not stored in the database but in separate files (wp-config.php, a generic MU plugin, or a custom file). This allows the API keys to be stored securely in the database by the AI Client.