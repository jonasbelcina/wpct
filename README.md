## Woocommerce shortcode product sorting by custom taxonomy and product category

This plugin adds custom taxonomy and product category sorting through Woocommerce shortcode.

## Installation

Simply install and activate the plugin.

## Usage

ex. '[woo_products_custom_tax tax_name="collection" tax_tags="gold-jewellery, diamond-jewellery" category="7" qty="10" order="DESC"]'
    Displays products with: 
    	- custom taxonomy of 'collection'
    	- tags of 'gold-jewellery' or 'diamond-jewellery'
    	- category id of '7'

# Attributes

tax_name*: The custom taxonomy name
tax_tags*: The custom taxonomy tag/s (comma separated)
category*: The product category id
qty: Number of products to display (posts_per_page, default: -1)
order: 'DESC' or 'ASC' (default: 'DESC')

*required

## Additional Feature

Order by Price: append '?orderby=price' to display results from lowest to highest price or '?orderby=price-desc' to display from highest to lowest

