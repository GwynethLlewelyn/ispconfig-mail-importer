# ispconfig-mail-importer
Swiss Army knife for importing all sorts of mail-related things into ISPConfig via the SOAP API

## Disclaimer

This _can_ completely break your ISPConfig 3 setup! _Caveat utilitor_. Making a backup of your setup _and_ the database (usually *db_ispconfig*) is _strongly recommended_.

## Limitations

In spite of the promising title, for now, this will just import mail aliases using a specially-formated [CSV](https://en.wikipedia.org/wiki/Comma-separated_values) file.

## Requirements

- ISPConfig 3.1.15p3 (may work on earlier 3.X versions)
- PHP 7.X with php-soap installed (both on the client and the server); the client needs the PHP CLI (should be pre-installed)

## Configuration steps

1. Add a new remote user from the ISPConfig 3 control panel, somewhere at `System > Remote Users > Add new user`
2. Give the new user at least permissions for all mail-related services
3. Double-check that you _have_ added the domain for which you're going to upload a 
4. On the client side, rename `soap_config_template.php` to `soap_config.php`, filling in your remote username and the correct paths

## CSV parameters

Allegedly, there used to exist an earlier script to do the same, but I couldn't find it. So basically I developed my own format.

`mail_forward_add_csv.php` will load a CSV with email addresses to forward, in the format:

- **server_id** (usually 1)
- **source** origin@registereddomain.tld (see note above; must be a registered domain on the ISPConfig 3 backend) 
- **destination** ciao@test.int (could be anything, potentially more than one address)
- **type** *(optional)* Set to *forward*
- **active** *(optional)* Set to *y*

 Fields with commas, such as the **destination**, should be quoted, as per the usual CSV standards.

## Running the application

`php mail_forward_add_csv.php aliases.csv`