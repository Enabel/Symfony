# Symfony

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/46368121c35c4c3c87a0c7509a38bdfd)](https://www.codacy.com/gh/Enabel/Symfony/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Enabel/Symfony&amp;utm_campaign=Badge_Grade)

Template to make another Symfony application made by Enabel IT Team.

## Requirement
* You need PHP 7.2 or newer, with session support.
* PasswordManager supports MySQL-compatible databases
    * MySQL 5.7 or newer
    * MariaDB 10.1 or newer
* and the [usual Symfony application requirements][1].

## Setup

#### 1. Add Template to your repo.

1. Download This Repository
2. Copy all files to your new Application Repository.

OR

<a href="https://github.com/Enabel/symfony/generate">
<img src="https://user-images.githubusercontent.com/16992394/65464461-20c95880-de5a-11e9-9bf0-fc79d125b99e.png" alt="create repository from template"></a>

## Authentification OAuth2 (Azure/Office365)
### Register a Azure application
* Register a new application in the Microsoft Application Registration portal
  * Name: Name of your choice (to identify the app)
  * Account type: Accounts in this organizational directory only (for intern usage)
  * Redirect URI: Leave empty
  * **Copy/Save the application ID for later (AZURE_CLIENT_ID)**
* Certificates & secrets
  * Create new client secret
    * Description: Name of your choice (to identify the app)
    * Expires: 1 year / 2 year / never (according to your security policy)
    * **Copy/Save the secret value for later (AZURE_CLIENT_SECRET)**
* API permissions
  * Add the permissions for:
    * User.read
    * profile
    * openid
* Authentication
  * Add a platform > Web
    * Redirect URIs: {{ app_base_url }}/oauth/check/azure
    * Logout URL: leave empty
    * Implicit grant: Choose ID Tokens
    
### Configure OAuth2 client
- Copy the Client ID & Client Secret from above to the [.env](.env) file
```dotenv
AZURE_CLIENT_ID=xxxxxx
AZURE_CLIENT_SECRET=yyyyyy
```

## License

This project is open-sourced software licensed under the [GPL-3.0 License](LICENSE).

[1]: https://symfony.com/doc/current/reference/requirements.html