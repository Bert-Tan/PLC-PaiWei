<?php

// DKIM is used to sign e-mails. If you change your RSA key, apply modifications to the DNS DKIM record of the mailing (sub)domain too !
// Disclaimer : the php openssl extension can be buggy with Windows, try with Linux first

// To generate a new private key with Linux :
// openssl genrsa -des3 -out private.pem 1024
// Then get the public key
// openssl rsa -in private.pem -out public.pem -outform PEM -pubout

// Edit with your own info :

define('MAIL_RSA_PASSPHRASE', 'plc@1120e');

define('MAIL_RSA_PRIV',
'-----BEGIN RSA PRIVATE KEY-----
MIICXgIBAAKBgQDNWU/2ktWrs1Nf4pTiQP0wSDJY8Nt/1B9haL/XGOkKCiA7RMLi
4UChcGYleZvBm4daH3bO+fPnv3SJewdlofshY02djpRj1U4aCkaBGYtVV35cJzqU
ts3YnYh9Q2Uq2eAKJj2Ae+9Afs3iKueBY3eXAVdkgHogn5fXRJzv8pbnVQIDAQAB
AoGAD1WiEQm6Bw5nJXvoHlU4EwjxKY8i9RLEHSQTX16u2F8VNRfXbdXgW63nEtlX
9kdE/kfnOWGVAzNa4oFbdg14kU1WBZwijV0dHk7t51E9oZo5hiYgf2S2wlaJPBC1
nR5U3xvjQLaHfhDWqkYW5xjaCNZkm8Z+6R/PzdVdiDEHxSkCQQD6m13steX3mgGE
JzHaa29GfkhX6vrP7jve4v5/hrfH4WnxknIbey4cn8EIBeBumhpqnY/97TLi/e+W
C7xlDszLAkEA0cScoSDYe4vz57g/fnkywkTKsz1RcWEtyDBlc1R3rSS8abDHWkUr
/2H2NBTvHnGkUjrrd9f1pl+mwfRm/tW4XwJBAPl5m2netnzjMik1v3o5Q0AAzNHA
2UgPWEiM3l9jZCa17nqOl8tlt8TFACuVdhOEk1GZYtOcwvCXbF+JdVWBAzsCQQC2
5Cl8Ats8vMUnn2kcqCctYjUpGalMpWH5TNjnORovB/yOWec2OWEnBQ5YUng5nvOa
Dm0GzHANYxBNwv2Z6lUZAkEAz8Ol/4aRr8gZ2hJa8W4K/Hr5WLrdouaE7jJNwm/A
n9MOL5cmzDw5rWXwFWesnF6rrzLaqHPHCrfF4yqhXwDM/A==
-----END RSA PRIVATE KEY-----');

define('MAIL_RSA_PUBL',
'-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDNWU/2ktWrs1Nf4pTiQP0wSDJY
8Nt/1B9haL/XGOkKCiA7RMLi4UChcGYleZvBm4daH3bO+fPnv3SJewdlofshY02d
jpRj1U4aCkaBGYtVV35cJzqUts3YnYh9Q2Uq2eAKJj2Ae+9Afs3iKueBY3eXAVdk
gHogn5fXRJzv8pbnVQIDAQAB
-----END PUBLIC KEY-----');

// Domain or subdomain of the signing entity (i.e. the domain where the e-mail comes from)
define('MAIL_DOMAIN', 'amitabhalibrary.org');  

// Allowed user, defaults is "@<MAIL_DKIM_DOMAIN>", meaning anybody in the MAIL_DKIM_DOMAIN domain. Ex: 'admin@mydomain.tld'. You'll never have to use this unless you do not control the "From" value in the e-mails you send.
define('MAIL_IDENTITY', NULL);

// Selector used in your DKIM DNS record, e.g. : selector._domainkey.MAIL_DKIM_DOMAIN
define('MAIL_SELECTOR', 'amituofo');

?>