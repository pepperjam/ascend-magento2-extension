# Pepperjam Network for Magento 2

Launching an affiliate marketing program has never been easier. With Pepperjam Network Extension, you can start driving traffic and revenue right now. The extension empowers merchants with:

- A step by step integration and onboarding process
- Ability to track and record affiliate sales and conversions
- Flexible configuration
- Product feed export automated nightly in Pepperjam Network format
- Correction feed automated nightly in Pepperjam Network format

## Installation

**via composer**

Edit magento's composer.json and add repository 

    {
        "repositories":[         
    	    {
                "type":"git",
                "url":"git@github.com:pepperjam/ascend-magento2-extension.git"
            }
        ]
    }

Run installation
    
    composer require pepperjam/network-magento2-module:master@dev --update-no-dev

    
**via file transfer**

Download the package from releases tab https://github.com/pepperjam/ascend-magento2-extension/releases   

## How to get started

1. Install the extension
1. Under Stores > Configuration, you’ll find Pepperjam Network on the left hand navigation pane. Click to configure the extension.
1. Select “Yes” to enable Affiliate tracking.
1. Insert your unique Program ID (PID) into the extension. If you do not yet have a PID, click on the “Register Now” link to register for an account.
1. Select “Dynamic” for tracking type, unless otherwise instructed.
1. Set Enable Container Tag = Yes and fill in Tag Identifier field.   
1. Set export path to a directory accessible via FTP.
1. Place test transaction to confirm installation.

