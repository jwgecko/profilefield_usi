# Overview

This plugin implements a field format to store Australian unique student identifiers (USI)
that can be exposed on any Moodle user form as a custom user profile field.

In addition to storing USI, there are two levels of USI validation 

There are two levels of USI validation:

- Internal validation of USIs based on allowable rules such as length, 
allowable characters and Luhn checksum.

- External web service verification of student data and USI using the Australian 
Government USI  verification service. Note: USI verification requires a JWGecko 
AVETMISS done API key. Please contact JWGecko (https://jwgecko.com/products-and-services/avetmiss-done)
for details.

# Installation Instructions

1. Unzip plugin and copy the usi folder to your Moodle site under: /user/profile/field/
2. Log into the site as a site administrator and perform a plugin upgrade
3. After upgrade/installation, configure the profile field under:
Site administration > Users > Accounts User profile fields
4. Create a new user profile field of type Unique Student Identifier (USI). If you are
using the AVETMISS Done USI verification service, configure the token under specific settings.

# Credits

(c) 2021 JWGecko
https://jwgecko.com/

Please visit https://jwgecko.com/products-and-services/avetmiss-done/
for more information about using AVETMISS done for USI verification.